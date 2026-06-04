<?php

// Ce fichier sera pour aller récupérer la liste des équipes et des joueurs associés à un évenement en cours, 
// pour les afficher dans la section de planification d'un évenement.

// Comme ce fichier est sous admin, on va devoir vérifier la session d'administrateur avant de faire quoi que ce soit, 
// pour s'assurer que seul un administrateur peut accéder à cette information.

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
$select = "SELECT ep.team_number, ep.player_id, p.firstname, p.lastname, ep.handicap_rounded ";
$from = "FROM event_players ep INNER JOIN players p ON ep.player_id = p.id ";
$where = "WHERE ep.event_id = ? ";
$orderBy = "ORDER BY ep.team_number ASC, ep.handicap_rounded ASC, p.firstname ASC, p.lastname ASC";
$sql = $select . $from . $where . $orderBy;

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

    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Il n'y a pas d'équipes d'inscrites pour cet événement, donc aucun joueur n'est associé."]);

    $stmt->close();
    $conn->close();
    exit();
}

// Initialiser un tableau pour stocker les équipes et les joueurs associés à chaque équipe
$teams = [];

// Regrouper par équipe
while ($row = $result->fetch_assoc()) {

    // On va crééer une clé pour chaque équipe dans le tableau $teams, 
    // et à l'intérieur de chaque équipe, on va créer un sous-tableau "players" pour stocker 
    // les joueurs associés à chaque équipe.
    $teamNumber = $row["team_number"];

    // Si l'équipe n'existe pas encore dans le tableau $teams, 
    // on la créé et on initialise le sous-tableau "players"
    if (!isset($teams[$teamNumber])) {

        $teams[$teamNumber] = [
            "team_id" => $teamNumber, 
            // On créé un sous tableau "players" pour stocker les joueurs associés à chaque équipe,
            "players" => []
        ];
    }

    $teams[$teamNumber]["players"][] = [
        "id" => $row["player_id"],
        "firstname" => $row["firstname"],
        "lastname" => $row["lastname"],
        "handicap_rounded" => $row["handicap_rounded"]
    ];
}

$teams = array_values($teams);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "teams" => $teams]);