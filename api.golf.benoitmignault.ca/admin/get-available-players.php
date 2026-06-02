<?php

// Ce fichier sera utilisé pour aller récupérer les joueurs disponibles 
// qui ne sont pas déjà inscrits au prochain événement à venir, 
// pour les afficher dans la liste déroulante d'ajout de joueurs à un événement.

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

if ($eventId <= 0) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Identifiant de l'événement invalide."]);
    $conn->close();
    exit();
}

// Requête SQL pour afficher les joueurs disponibles pour un évenement en cours d'inscription,
$select = "SELECT id, firstname, lastname ";
$from = "FROM players ";
$where = "WHERE id NOT IN (SELECT player_id FROM event_players WHERE event_id = ?) ";
$orderBy = "ORDER BY firstname ASC, lastname ASC";
$sql = $select . $from . $where . $orderBy;

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {

    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Il n'y a pas de joueurs disponibles pour cet événement."]);

    $stmt->close();
    $conn->close();
    exit();
}

$availablePlayers = [];

while ($row = $result->fetch_assoc()) {

    // Ajouter chaque joueur disponible au tableau des joueurs disponibles
    $availablePlayers[] = $row;
}

http_response_code(200);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();

// Retourner les données au format JSON
echo json_encode(["success" => true, "players" => $availablePlayers]);