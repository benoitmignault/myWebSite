<?php

// Ce fichier sera utilisé pour ajouter un joueur à un événement spécifique. Il reçoit les données du frontend,
// les valide, puis les insère dans la table event_players.
// Ensuite, on récupère la position générale actuelle, le nombre de points Fedex et 
// le handicap en prévision du tournoi à venir.

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../includes/cors.php");

// Inclut les informations pour vérifier la session d'administrateur
include(__DIR__ . "/auth/check-admin-session.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/../includes/fct-connexion-bd.php");

// S'assurer que la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

// Si on passe la validation du POST et que la session est valide, on poursuit avec la lecture des données reçues, 
// la validation des données, la connexion à la base de données, 
// l'insertion du joueur à l'événement dans la base de données et le retour d'une réponse JSON indiquant.
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier si les données ont été reçues
if (!$data) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Aucune donnée reçue"]);    
    exit();
}

// Récupérer les données envoyées depuis le frontend
$eventId = $data['eventId'];
$playerId = $data['playerId'];
$teamId = $data['teamId'];

// Validation des données
if ($eventId <= 0 || $playerId <= 0 || $teamId <= 0) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Informations invalides."]);
    exit();
}

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);
    exit();
}

// Vérification que event existe avant aller plus loin
$select = "SELECT id, is_open, is_closed ";
$from = "FROM events ";
$where = "WHERE id = ?";
$sql = $select . $from . $where;
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);

// Exécuter la requête SQL pour vérifier que l'événement existe et récupérer son statut d'ouverture et de fermeture
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de la vérification de l'événement."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Événement introuvable."]);

    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Vérification que l'événement n'est pas déjà fermé avant aller plus loin
$event = $result->fetch_assoc();

if ($event["is_closed"] == 1) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Cet événement est déjà fermé."]);

    $stmt->close();
    $conn->close();
    exit();
}

// Cependant, on doit faire la validation qu'une équipe ne peut pas avoir plus de 4 joueurs
$select = "SELECT COUNT(*) AS total_players ";
$from = "FROM event_players ";
$where = "WHERE event_id = ? AND team_number = ?";
$sql = $select . $from . $where;
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $eventId, $teamId);

// Exécuter la requête SQL pour vérifier le nombre de joueurs déjà inscrits dans l'équipe pour cet événement
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de la vérification du nombre de joueurs dans l'équipe."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}    

$result = $stmt->get_result();

// Récupérer le nombre de joueurs dans l'équipe
$playerCount = $result->fetch_assoc()["total_players"];

// Vérifier si l'équipe a déjà 4 joueurs
if ($playerCount >= 4) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Cette équipe a déjà 4 joueurs."]);

    $stmt->close();
    $conn->close();
    exit();
}

// Si nous arrivons ici, ça veut dire que l'équipe n'est pas complète et que nous pouvons ajouter le joueur à l'événement.

// On va récupérer current_position, current_fedex_points, current_handicap pour aller les insérer 
// dans la table player_event_history sous la notion de previous_position, previous_fedex_points,
//  previous_handicap pour garder un historique de l'évolution du joueur au fil des événements
$select = "SELECT current_position, current_fedex_points, current_handicap ";
$from = "FROM player_event_history ";
$where = "WHERE player_id = ? ";
$orderBy = "ORDER BY event_id DESC ";
$limit = "LIMIT 1";
$sql = $select . $from . $where . $orderBy . $limit;

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $playerId);
$stmt->execute();

$result = $stmt->get_result();
$history = $result->fetch_assoc();

// Cas particulier pour le premier événement d'un joueur, où il n'a pas encore de position, 
// de points Fedex ou de handicap enregistré dans la table player_event_history.
if ($history) {

    $previousPosition = $history["current_position"];
    $previousFedexPoints = $history["current_fedex_points"];
    $previousHandicap = $history["current_handicap"];

} else {

    // Ca veut donc dire que le joueur n'a pas encore participé à un événement, donc on va initialiser sa position précédente    
    $previousPosition = null;
    $previousFedexPoints = null;

    // Il faudra aller chercher le handicap actuel du joueur dans la table players pour l'insérer
    // dans player_event_history et la table event_players, car il n'a pas encore de handicap enregistré 
    // dans player_event_history pour un évent précédent.
    $select = "SELECT handicap_league ";
    $from = "FROM players ";
    $where = "WHERE id = ?";
    $sql = $select . $from . $where;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $playerId);

    // Exécuter la requête SQL pour insérer le joueur à l'événement
    if (!$stmt->execute()) {

        error_log($stmt->error);
        
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la récupération du handicap du joueur."]); 
        
        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
        $stmt->close();
        $conn->close();
        exit();
    }

    $result = $stmt->get_result();
    $player = $result->fetch_assoc();
    $previousHandicap = $player["handicap_league"];
}

// On doit passer l'handicap arrondi pour l'évenement en cours
$previousHandicapRouned = round($previousHandicap);

// Requête SQL pour insérer le joueur à l'événement dans la table event_players
$insert = "INSERT INTO event_players (event_id, player_id, team_number, handicap_rounded) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insert);
$stmt->bind_param("iiii", $eventId, $playerId, $teamId, $previousHandicapRouned);

// Exécuter la requête SQL pour insérer le joueur à l'événement
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout du joueur à l'événement."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Requête SQL pour insérer la nouvelle entrée dans la table player_event_history 
// pour garder un historique de l'évolution du joueur au fil des événements
$insert = "INSERT INTO player_event_history (event_id, player_id, previous_position, previous_fedex_points, previous_handicap) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert);
$stmt->bind_param("iiiid", $eventId, $playerId, $previousPosition, $previousFedexPoints, $previousHandicap);

// Exécuter la requête SQL pour insérer le nouveau joueur dans la base de données
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout de l'historique du joueur pour cet événement."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Si nous arrivons ici, ça veut dire que les deux insertions ont réussi, donc on peut faire un commit pour valider la transaction
$conn->commit();

// Fermer la connexion au résultat du insert dans la base de données
$stmt->close();

// Si les ajouts du joueur à l'événement et de son historique ont réussi, on doit vérifier si l'événement était fermé (is_open = 0) avant cet ajout,
// car si c'est le cas, ça veut dire que c'était le premier joueur à s'inscrire pour cet événement, donc on doit ouvrir la section des résultats des inscriptions 
// pour cet événement en mettant à jour le statut de l'événement à ouvert (is_open = 1) dans la base de données.
if ($event["is_open"] == 0) {

    // On va faire un update pour changer le statut de l'event à ouvert
    $update = "UPDATE events SET is_open = 1 WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("i", $eventId);

    // Exécuter la requête SQL pour insérer le nouveau joueur dans la base de données
    if (!$stmt->execute()) {

        error_log($stmt->error);
        
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de l'ouverture des inscriptions pour cet événement."]); 
        
        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
        $stmt->close();
        $conn->close();
        exit();
    }    
}

$conn->close();

http_response_code(201);
echo json_encode(["success" => true, "message" => "Le joueur a été ajouté à l'événement avec succès et son historique a été mis à jour."]);
