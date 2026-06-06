<?php

// Ce fichier est utilisé pour récupérer les positions déjà utilisées par les joueurs inscrits à l'événement en cours, 
// afin d'empêcher l'insertion de plusieurs résultats avec la même position pour un même événement.

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../includes/cors.php");

// Inclut les informations pour vérifier la session d'administrateur
include(__DIR__ . "/auth/check-admin-session.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/../includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);
    exit();
}

// On commence par récupérer le id de event pour aller faire la requête SQL par la suite
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Vérifier si l'identifiant de l'événement est valide
if ($eventId <= 0) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Identifiant de l'événement invalide."]);
    $conn->close();
    exit();
}

// Requête SQL pour récupérer les positions déjà utilisées par les joueurs inscrits à l'événement en cours
$select = "SELECT position ";
$from = "FROM round_results ";
$where = "WHERE event_id = ?";
$sql = $select . $from . $where;

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();

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

$usedPositions = [];

while ($row = $result->fetch_assoc()) {

    // Ajouter chaque position à la liste des positions utilisées
    $usedPositions[] = $row;
}

http_response_code(200);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();

// Retourner les données au format JSON
echo json_encode(["success" => true, "positions" => $usedPositions]);
