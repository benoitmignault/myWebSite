<?php

// Ce fichier va servir à récupérer les joueurs inscrits à l'événement en cours mais 
// qu'ils n'ont pas encore de résultats dans la table round_results pour les afficher dans la liste déroulante d'ajout de résultats à un événement.

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

// Requête SQL pour afficher les joueurs inscrits à l'événement en cours mais qu'ils n'ont pas encore de résultats dans la table round_results
// Nous avons besoin de l'handicap du joueur de l'éevent en cours pour le calcul du NET Score
$select = "SELECT p.id, p.firstname, p.lastname, ep.handicap_rounded ";
$from = "FROM event_players ep INNER JOIN players p ON ep.player_id = p.id  ";
$leftJoin = "LEFT JOIN round_results rr ON rr.event_id = ep.event_id AND rr.player_id = ep.player_id ";
$where = "WHERE ep.event_id = ? AND rr.player_id IS NULL ";
$orderBy = "ORDER BY p.firstname, p.lastname";
$sql = $select . $from . $leftJoin . $where . $orderBy;

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

if ($result->num_rows === 0) {

    http_response_code(200);
    echo json_encode(["success" => true, "players" => []]);

    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

$registeredPlayers = [];

while ($row = $result->fetch_assoc()) {

    // Ajouter chaque joueur à la liste des joueurs inscrits
    $registeredPlayers[] = $row;
}

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "players" => $registeredPlayers]);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();