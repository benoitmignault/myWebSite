<?php

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/includes/cors.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);

    exit();
}

// Requête SQL pour récupérer les détails d'un joueur spécifique pour chacun de ses tournois
$playerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validation de l'identifiant du joueur si'il n'existe pas, on retourne 
// une réponse JSON indiquant que l'identifiant du joueur est invalide et 
// on arrête l'exécution du script. 
// Cela permet d'éviter d'exécuter la requête SQL avec un identifiant de joueur invalide, 
// ce qui pourrait entraîner des erreurs ou des résultats inattendus.
if ($playerId <= 0) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Identifiant du joueur invalide."]);
    
    // Fermer la connexion à la base de données
    $conn->close();
    exit();
}

// Vérifier si le joueur existe avant aller plus loin
$stmt = $conn->prepare("SELECT id FROM players WHERE id = ?");
$stmt->bind_param("i", $playerId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Joueur introuvable."]);

    // Fermer la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Requête SQL pour récupérer les détails d'un joueur spécifique pour chacun de ses tournois
$select = "SELECT e.event_name, e.event_date, r.position, r.gross_score, r.net_score, r.fedex_points ";
$from = "FROM round_results r INNER JOIN events e ON e.id = r.event_id ";
$where = "WHERE r.player_id = ? ";
$orderBy = "ORDER BY e.event_date";
$sql = $select . $from . $where . $orderBy;

// Exécuter la requête SQL
$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de la préparation de la requête."]);
    
    // Fermer la connexion à la base de données
    $conn->close();
    exit();
}

$stmt->bind_param("i", $playerId);

if (!$stmt->execute()) {

    error_log($stmt->error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'exécution de la requête."]);

    // Fermer la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();

$playerDetails = []; // Tableau pour stocker les détails du joueur

// Parcourir les résultats de la requête et organiser les données par événement pour le joueur spécifique
while ($row = $result->fetch_assoc()) {
    $playerDetails[] = $row;
}

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "playerDetails" => $playerDetails]);

// Fermer la connexion à la base de données et le résultat de la requête SQL
$stmt->close();
$conn->close();
