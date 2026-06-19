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
    exit();
}

// Récupérer les données envoyées depuis le frontend
$playerId = $data['playerId'];

// Première requete SQL viendra récupérer les informations du joueur, comme son prénom et son nom de famille, son handicap et sa moyenne brute.
$select = "SELECT firstname, lastname, handicap_league, average_score ";
$from = "FROM players ";
$where = "WHERE id = ?";
$sql = $select . $from . $where;

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
    echo json_encode(["success" => false, "message" => "Joueur non trouvé."]);

    $stmt->close();
    $conn->close();
    exit();
}

// Préparation de la structure de données pour stocker les informations du joueur
$playerInfo = [
    "full_name" => "",
    "handicap" => 0,
    "average_score" => 0,

    "fedex_position" => 0,
    "fedex_points" => 0,

    "gold_trophies" => 0,
    "silver_trophies" => 0,
    "bronze_trophies" => 0
];

// Récupérer les informations du joueur
$row = $result->fetch_assoc();

// Remplir les informations du joueur dans la structure de données
$playerInfo["full_name"] = $row["firstname"] . " " . $row["lastname"];
$playerInfo["handicap"] = $row["handicap_league"];
$playerInfo["average_score"] = $row["average_score"];
