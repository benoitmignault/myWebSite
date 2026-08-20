<?php

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../includes/cors.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/../includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);

    exit();
}

// Si on passe la validation du POST, on poursuit avec la lecture des données reçues, 
// qui sont envoyées depuis le frontend en format JSON. On va les décoder pour les utiliser dans notre code PHP.
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier si les données ont été reçues
if (!$data) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Aucune donnée reçue"]);
    
    // Fermer la connexion à la base de données
    $conn->close();
    exit();
}

// Récupérer les données envoyées depuis le frontend
if (!isset($data['playerId']) || !is_numeric($data['playerId'])) {

     http_response_code(400);
     echo json_encode(["success" => false, "message" => "playerId manquant ou invalide."]);
     
     // Fermer la connexion à la base de données
     $conn->close();
     exit();
 }

 $playerId = (int) $data['playerId'];

// Requête SQL pour récupérer les données nécessaires pour les graphiques du joueur
$select = "SELECT current_position, current_fedex_points, current_handicap, event_id ";
$from = "FROM player_event_history h INNER JOIN events e ON h.event_id = e.id ";
$where = "WHERE h.player_id = ? AND e.is_open = 0 ";
$orderBy = "ORDER BY e.event_date asc";
$sql = $select . $from . $where . $orderBy;

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $playerId);

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

if ($result->num_rows === 0) {

    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Aucun historique trouvé pour ce joueur."]);

    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Sinon, on va parcourir les résultats de la requête pour chaque historique d'événement du joueur
$playerChartsData = [];

// On va utilisé une incrémentation simple pour faire affaire l'évolution dans le temps, 
// puisque les résultats sont ordonnés par event_id asc, ce qui correspond à l'ordre chronologique des événements.

// On va plutot utiliser la vraie notio event lors de l'affichage de l'historieque du joueur après 
// la section des graphiques pour éviter d'avoir des données qui ne sont pas affichées dans le résumé du joueur, 
// et pour que ce soit plus explicite que les données correspondent à l'évolution du joueur au fil des événements, 
// plutôt que d'avoir une simple incrémentation qui pourrait être interprétée comme une erreur dans la récupération des données.
$weekNumber = 1;

while ($row = $result->fetch_assoc()) {

    $playerChartsData[] = [
        "week" => $weekNumber,
        "position" => (int) $row["current_position"],
        "fedex_points" => (int) $row["current_fedex_points"],
        "handicap" => (float) $row["current_handicap"]
    ];

    $weekNumber++;
}

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "playerChartsData" => $playerChartsData]);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
