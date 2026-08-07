<?php

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../../includes/cors.php");

// Inclut les informations pour vérifier la session d'administrateur
include(__DIR__ . "/../auth/check-admin-session.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/../../includes/fct-connexion-bd.php");

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

// Requête SQL pour récupérer les 10 événements les plus consultés sur le site web
$select = "SELECT e.golf_course, count(*) as clicks ";
$from = "FROM events e LEFT JOIN website_logs wl ON e.id = wl.target_id ";
$where = "WHERE wl.action_type = 'event_click' ";
$groupBy = "GROUP BY wl.target_id ";
$orderBy = "ORDER BY clicks DESC ";
$limit = "LIMIT 10";

// Initialiser la variable $date à null
$date = null;

// Si la période sélectionnée n'est pas "all", 
// on ajoute une condition dans le WHERE déjà présent pour filtrer les données selon la période
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
    $where .= "AND log_date >= ? ";
}

// Préparer la requête SQL complète
$sql = $select . $from . $where . $groupBy . $orderBy . $limit;
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

// Récupérer le résultat de la requête SQL
$result = $stmt->get_result();

// Initialiser un tableau pour stocker les événements les plus consultés
$topEvents = [];

// Parcourir les résultats de la requête SQL et les stocker dans le tableau $topEvents
while ($row = $result->fetch_assoc()) {

    // Extraction de seulement le nom du club de golf sans les (...)
    $topEvents[] = [                   
        "golf_course" => trim(preg_replace('/\s*\(.*\)$/', '', $row["golf_course"])),
        "clicks" => (int) $row["clicks"]
    ];
}

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "top_events" => $topEvents]);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
