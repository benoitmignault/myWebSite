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
    
    // Fermer la connexion à la base de données
    $conn->close();
    exit();
}

// On va procéder finalement à deux requêtes SQL, 
// la première pour récupérer le nombre total de joueurs inscrits à l'événement en cours
// la deuxième pour récupérer les positions déjà utilisées par les joueurs inscrits à l'événement en cours

// Pour ensuite faire la comparaison entre les positions déjà utilisées et le nombre total de joueurs inscrits à l'événement 
// en cours pour déterminer les positions disponibles pour l'insertion des résultats de la ronde du joueur

// Réquête SQL pour récupérer le nombre total de joueurs inscrits à l'événement en cours
$select = "SELECT COUNT(*) AS totalPlayers ";
$from = "FROM event_players ";
$where = "WHERE event_id = ?";
$sql = $select . $from . $where;

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);

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
$row = $result->fetch_assoc();

if ((int)$row['totalPlayers'] === 0) {

    http_response_code(200);
    echo json_encode(["success" => true, "availablePositions" => []]);
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Nous avons maintenant le nombre total de joueurs inscrits à l'événement en cours, 
// que nous allons utiliser pour faire la comparaison avec les positions déjà utilisées par les joueurs inscrits à l'événement
$totalPlayers = $row['totalPlayers'];

$stmt->close();

// Requête SQL pour récupérer les positions déjà utilisées par les joueurs inscrits à l'événement en cours
$select = "SELECT position ";
$from = "FROM round_results ";
$where = "WHERE event_id = ?";
$sql = $select . $from . $where;

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);

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

// 2026-06-09. découvert d'un bug, je devais extyraire la position de chaque résultat pas juste la row qui est une key/value
$usedPositions = [];

while ($row = $result->fetch_assoc()) {

    $usedPositions[] = (int)$row['position'];
}

// Création d'un tableau de toutes les positions possibles pour l'événement en cours, basé sur le nombre total de joueurs inscrits à l'événement
$allPositions = range(1, $totalPlayers);

// Filtrer les positions utilisées pour ne garder que les positions disponibles
// On doit transformer le résultat en tableau et non Object
$availablePositions = array_values(array_diff($allPositions, $usedPositions));

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "availablePositions" => $availablePositions]);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
