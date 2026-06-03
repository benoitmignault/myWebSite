<?php

// Ce fichier sera pour aller récupérer la liste des équipes et des joueurs associés à un évenement en cours, 
// pour les afficher dans la section de planification d'un évenement.

// Comme ce fichier est sous admin, on va devoir vérifier la session d'administrateur avant de faire quoi que ce soit, 
// pour s'assurer que seul un administrateur peut accéder à cette information.

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

// On récupérer le id de event pour aller faire la requête SQL par la suite
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($eventId <= 0) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Identifiant de l'événement invalide."]);
    $conn->close();
    exit();
}

// Requête SQL pour afficher les équipes et les joueurs associés à un évenement en cours, 
// triés par numéro d'équipe et par handicap
$select = "SELECT ep.team_number, p.firstname, p.lastname, ep.handicap_rounded ";
$from = "FROM event_players ep INNER JOIN players p ON ep.player_id = p.id ";
$where = "WHERE ep.event_id = ? ";
$orderBy = "ORDER BY ep.team_number ASC, ep.handicap_rounded ASC, p.firstname ASC, p.lastname ASC";
$sql = $select . $from . $where . $orderBy;


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {

    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Il n'y a pas d'équipes ou de joueurs inscrits pour cet événement."]);

    $stmt->close();
    $conn->close();
    exit();
}