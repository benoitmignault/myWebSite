<?php

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../../../includes/cors.php");

// Inclut les informations pour vérifier la session d'administrateur
include(__DIR__ . "/../../auth/check-admin-session.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/../../../includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);
    
    exit();
}

// On commence par récupérer la période sélectionnée pour aller faire la requête SQL par la suite
// Si la variable/valeur n'est pas définie, on met par défaut "all" pour récupérer toutes les données
$period = isset($_GET['period']) ? $_GET['period'] : 'all';

// Requête SQL pour récupérer le résumé des statistiques d'activité du site web et des partenaires
$select = "SELECT sponsor_name, COUNT(*) AS clicks ";
$from = "FROM website_logs_sponsors ";
$where = "";
$groupBy = "GROUP BY sponsor_name ";
$order = "ORDER BY clicks DESC";

// Initialiser la variable $date à null
$date = null;

// Si la période sélectionnée n'est pas "all", 
// on ajoute une condition WHERE pour filtrer les données selon la période
if ($period !== 'all') {
    
    // Ajustement du timezone à l'heure de Montréal pour la date de début de la période sélectionnée
    date_default_timezone_set('America/Montreal');

    // On doit déterminer si nous avons une condition WHERE à ajouter à la requête SQL selon la période sélectionnée
    switch ($period) {

        case "today":
            $date = date("Y-m-d 00:00:00");
            break;

        case "7":
            $date = date("Y-m-d H:i:s", strtotime("-7 days"));
            break;

        case "30":
            $date = date("Y-m-d H:i:s", strtotime("-30 days"));
            break;

        case "90":
            $date = date("Y-m-d H:i:s", strtotime("-90 days"));
            break;
    }

    // On met à jour la condition WHERE à la requête SQL pour filtrer les données selon la période sélectionnée
    $where .= "WHERE log_date >= ? ";
}
// Sinon, aucune conditionque WHERE n'est nécessaire pour la requête SQL, on récupère toutes les données

// Préparer la requête SQL, car ici, nous savons qu'il peut y avoir une condition WHERE selon la période sélectionnée. 
// Donc, on doit préparer la requête SQL pour éviter les injections SQL.
$sql = $select . $from . $where . $groupBy . $order;

$stmt = $conn->prepare($sql);

// Si la période sélectionnée n'est pas "all", on doit lier le paramètre de date à la requête SQL
if ($period !== 'all') {

    // Préparer la requête SQL vue qu'il y a une condition
    $stmt->bind_param("s", $date);
}

// Vérifier si la requête a réussi
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'exécution de la requête."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();

// Initialiser le tableau de résumé des statistiques d'activité de tous les partenaires par défault à 0
$topSponsors = [
    ["sponsor_name" => "Apex Golf", "clicks" => 0],
    ["sponsor_name" => "Station GO", "clicks" => 0],
    ["sponsor_name" => "Golf en Montérégie", "clicks" => 0],
    ["sponsor_name" => "Toucani", "clicks" => 0],
    ["sponsor_name" => "Mr Tee", "clicks" => 0]
];

// Ici tu récupères les clics et tu mets à jour $topSponsors
while ($row = $result->fetch_assoc()) {

    foreach ($topSponsors as &$sponsor) {

        if ($sponsor['sponsor_name'] === $row['sponsor_name']) {
            $sponsor['clicks'] = (int) $row['clicks'];
            break;
        }
    }
}

unset($sponsor);

// Avant de retourn l'inforemation au front-end, on doit trier le tableau de résumé des statistiques d'activité des partenaires 
// par le nombre de clicks décroissant
usort($topSponsors, function ($a, $b) {

    // Trier par nombre de clics décroissant
    if ($a['clicks'] !== $b['clicks']) {
        return $b['clicks'] <=> $a['clicks'];
    }

    // En cas d'égalité, trier par nom du partenaire
    return strcmp($a['sponsor_name'], $b['sponsor_name']);
});

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "topSponsors" => $topSponsors]);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();