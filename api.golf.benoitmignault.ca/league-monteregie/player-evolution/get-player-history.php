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

// Requête SQL pour récupérer les données nécessaires pour l'historique du joueur dans les événements qui sont fermés seulement
$select = "SELECT e.event_name, e.golf_course, e.event_date, h.current_position, ";
$select .= "(CAST(h.previous_position AS SIGNED) - CAST(h.current_position AS SIGNED)) AS position_variation, ";
$select .= "h.current_fedex_points, h.fedex_points_gained, ";
$select .= "h.current_handicap, (h.previous_handicap - h.current_handicap) AS handicap_variation ";
$from = "FROM player_event_history h INNER JOIN events e ON e.id = h.event_id ";
$where = "WHERE h.player_id = ? AND e.is_closed = 1 ";
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

// Sinon, on va parcourir les résultats et les stocker dans un tableau pour les envoyer au frontend
$playerHistoryData = [];

while ($row = $result->fetch_assoc()) {

    $playerHistoryData[] = [
        "event_name" => $row['event_name'],
        "golf_course" => $row['golf_course'],
        "event_date" => $row['event_date'],
        "current_position" => $row['current_position'],
        "position_variation" => $row['position_variation'],
        "current_fedex_points" => $row['current_fedex_points'],
        "fedex_points_gained" => $row['fedex_points_gained'],
        "current_handicap" => $row['current_handicap'],
        "handicap_variation" => $row['handicap_variation']
    ];
}

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "playerHistoryData" => $playerHistoryData]);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
