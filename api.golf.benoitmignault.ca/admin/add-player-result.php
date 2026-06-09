<?php

// Ce fichier sera utilisé pour faire plusieurs opérations reliées à l'ajout de résultats d'un joueur à un événement
// On va faire les mêmes genre de vérification que dans le front-end pour s'assurer que les données reçues sont valides 
// avant de faire le insert dans la base de données.

// Étape 0 - Je vais devoir une MAJ (un snapshot) de la table players avant de faire le insert du résultat du joueur dans la table round_results, 
// pour aller mettre à jour la previous_position avec la requête SQL qui me permet d'afficher le classement général actuel
// La vérification sera faite avec la valeur du champ «is_open» de la table events. Si la valeur est à 1, on fait la MAJ des positions actuelles 
// vers le champ previous_position de la table players, sinon on ne fait pas la MAJ des positions vers le champ previous_position de la table players

// Étape 1 - Insérer le résultat du joueur dans la table round_results

// Étape 2 - On doit recalculer la moyenne des scores bruts du joueur en question, après chaque ajout de résultat

// Étape 3 - De ce même joueur, on doit vérifier si on doit faire un recalcul de son handicap en fonction du nombre de rondes jouées 
// et faire un update de son handicap dans la table players

// Étape 4 - Une fois que tous les joueurs du même événement ont leurs résultats ajoutés :
// Étape 4.1 - On doit faire un recalcul des current_position dans la table «player_event_history»
// Étape 4.1.1 - Commencons par «current_position»
// Étape 4.1.2 - Poursuivons avec «current_fedex_points»
// Étape 4.1.3 - Poursuivons avec «fedex_points_gained»
// Étape 4.1.4 - Poursuivons avec «current_handicap»
// Étape 4.2 - On doit fermer l'évenement et remettre à 0 is_update et is_open et mettre à 1 is_closed pour permettre au système de passer à l'événement suivant

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
$position = $data['position'];
$grossScore = $data['grossScore'];
$adjustedGrossScore = $data['adjustedGrossScore'];
$netScore = $data['netScore'];
$fedexPoints = $data['fedexPoints'];

// Validation des données pour s'assurer que les champs obligatoires ne sont pas vides, 
// sauf pour le netScore qui peut être égal à 0 ou même négatif
if (empty($eventId) || empty($playerId) || empty($position) || 
    $grossScore === "" || $adjustedGrossScore === "" || $fedexPoints === "" || $netScore === "") {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Veuillez remplir tous les champs obligatoires."]);
    exit();
}

// Validation des données pour s'assurer que les champs numériques sont valides, on valider le scoreNet ici seulement
if (!is_numeric($position) || !is_numeric($grossScore) || !is_numeric($adjustedGrossScore) || !is_numeric($fedexPoints) || !is_numeric($netScore)) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Les champs numériques sont invalides."]);
    exit();
}

// Validation des données, sauf our le netScore qui peut être égal à 0 ou même négatif
if ($eventId <= 0 || $playerId <= 0 || $position <= 0 || $grossScore <= 0 || $adjustedGrossScore <= 0 || $fedexPoints <= 0) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Informations invalides."]);
    exit();
}

// Validation pour s'assurer que le score brut ajusté ne peut pas être supérieur au score brut
if ($adjustedGrossScore > $grossScore) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le score brut ajusté ne peut pas être supérieur au score brut."]);
    exit();
}

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);
    exit();
}

// Étape 0, on doit valider si l'event est ouvert ou pas, si ouvert, 
// on fait la MAJ des positions actuelles vers le champ previous_position de la table players, 
// sinon on ne fait pas la MAJ des positions vers le champ previous_position de la table players
$select = "SELECT is_open, is_updated ";
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

    $stmt->close();
    $conn->close();
    exit();
}

// Vérification que l'événement n'est pas déjà fermé avant aller plus loin
$row = $result->fetch_assoc();
$isOpen = $row['is_open'];
$isUpdated = $row['is_updated'];

// Si l'événement est ouvert, on fait la MAJ des positions actuelles vers le champ previous_position de la table players, 
// sinon on ne fait pas la MAJ des positions vers le champ previous_position de la table players et on passe à l'étape 1
if ($isOpen == 1 && $isUpdated == 0) {

    // Étape 0.1 - Récupérer les positions actuelles de tous les joueurs pour les mettre à jour dans le champ previous_position de la table players
    $select = "SELECT p.id, COALESCE(SUM(r.fedex_points), 0) AS total_points ";
    $from = "FROM players p LEFT JOIN round_results r ON p.id = r.player_id ";
    $groupBy = "GROUP BY p.id ";
    $orderBy = "ORDER BY total_points DESC, p.handicap_league ASC, p.firstname ASC";
    $sql = $select . $from . $groupBy . $orderBy;

    $result = $conn->query($sql);

    // Vérifier si la requête a réussi
    if (!$result) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de l'exécution de la requête."]);
        $conn->close();
        exit();
    }

    // Création d'un tableau avec les positions actuelles de tous les joueurs, 
    // on va utiliser ce tableau pour faire un update du champ previous_position de la table players
    $previousPositions = [];

    $position = 1;

    while ($row = $result->fetch_assoc()) {

        $previousPositions[$row['id']] = $position;
        $position++;
    }

    // Étape 0.2 - Mettre à jour le champ previous_position de tous les joueurs 
    // dans la table players avec les positions actuelles du classement avant entrer les résultats de la ronde en cours
    $update = "UPDATE players SET previous_position = CASE id ";    
    $switchCase = "";

    foreach ($previousPositions as $playerId => $position) {
        $switchCase .= " WHEN " . $playerId . " THEN " . $position . " ";
    }

    $switchCase .= "END ";

    // On doit faire un update de tous les joueurs dans la table players de leur positions actuels du classement général
    // Transforme le tableau en chaine de caractères pour la clause WHERE de la requête SQL, en utilisant les id des joueurs    
    $playerIds = implode(",", array_keys($previousPositions));

    // La condition pour faire le update de tous les joueurs dans la table players de leurs positions actuels du classement général
    $where = "WHERE id IN (" . $playerIds . ")";

    $sql = $update . $switchCase . $where;
    if (!$conn->query($sql)) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour des positions des joueurs."]);

        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
        $conn->close();
        exit();
    }

    // Etape 0.3 - Remettre a 0 le is_open pour eviter ajouter des joueurs à un evenement qui est entrains de se faire ajouter des résultats
    // 2026-06-08, changement et ajustement de la logique, on va plutôt faire un update du champ is_updated à 1 pour indiquer 
    // que les positions ont été mises à jour, et ce champ sera réutiliser dans EventPlanningSection pour bloquer l'ajout de joueurs à un événement 
    // qui est entrains de se faire ajouter des résultats, et aussi pour éviter de faire la MAJ des positions vers le champ previous_position 
    // de la table players si jamais on ajoute des résultats après que l'événement soit fermé
    $update = "UPDATE events SET is_updated = 1 WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("i", $eventId);

    if (!$stmt->execute()) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour de l'événement."]);
        $stmt->close();
        $conn->close();
        exit();
    }
}

// Etape 1 - Insérer le résultat du joueur dans la table round_results
$sql = "INSERT INTO round_results (event_id, player_id, position, gross_score, gross_score_adjust, net_score, fedex_points) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Lier les paramètres à la requête préparée
$stmt->bind_param("iiiiiii", $eventId, $playerId, $position, $grossScore, $adjustedGrossScore, $netScore, $fedexPoints);


// Exécuter la requête SQL pour insérer le résultat du joueur dans la table round_results
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout du résultat du joueur."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Étape 2 - On doit recalculer la moyenne des scores bruts du joueur en question, après chaque ajout de résultat
// Pour ce faire, on va récupérer tous les scores bruts du joueur en question dans la table round_results, 
// faire la moyenne et faire un update de la moyenne des scores bruts dans la table players

// Étape 2.1 - Extraire la moyenne des scores bruts du joueur en question à partir de la table round_results
$select = "SELECT AVG(gross_score) AS average_score ";
$from = "FROM round_results ";
$where = "WHERE player_id = ?";
$sql = $select . $from . $where;

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $playerId);

// Exécuter la requête SQL pour insérer le nouveau joueur dans la base de données
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de la récupération des scores bruts du joueur pour faire la moyenne."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Le joueur est introuvable pour faire le calcul de la moyenne des scores bruts."]);

     // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

$row = $result->fetch_assoc();
$averageScore = $row['average_score'];

// On va garder une décimale pour la moyenne des scores bruts
$averageScore = round($averageScore, 1);

// Étape 2.2 - Faire un update de la moyenne des scores bruts dans la table players
$update = "UPDATE players SET average_score = ? WHERE id = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("di", $averageScore, $playerId);

// Exécuter la requête SQL pour faire un update de la moyenne des scores bruts dans la table players
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour de la moyenne des scores bruts du joueur."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Étape 3 - De ce même joueur, on doit vérifier si on doit faire un recalcul de son handicap en fonction du nombre de rondes jouées 
// et faire un update de son handicap dans la table players

// On va commencer par récupérer la liste des «scores brut ajusté» trier par event_id de manière décroissant pour le joueur en question à partir de la table round_results
$select = "SELECT gross_score_adjust ";
$from = "FROM round_results ";
$where = "WHERE player_id = ? ";
$orderby = "ORDER BY event_id DESC ";
$limit = "LIMIT 20";

$sql = $select . $from . $where . $orderby . $limit;
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $playerId);

// Exécuter la requête SQL pour récupérer la liste des scores brut ajusté du joueur en question trier du plus bas au plus élevé,
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de la récupération des scores brut ajusté du joueur pour faire le calcul du handicap."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Le joueur est introuvable pour faire le calcul du handicap."]);

    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Récupérer la liste des scores brut ajusté du joueur en question trier du plus bas au plus élevé dans un tableau
$adjustedGrossScores = [];

while ($row = $result->fetch_assoc()) {

    $adjustedGrossScores[] = $row['gross_score_adjust'];
}

// Maintenant, en fonction du nombre de scores brut ajusté du joueur en question
$numberScoresToUse = count($adjustedGrossScores);

// Triage du tableau des scores brut ajusté du joueur en question du plus bas au plus élevé
sort($adjustedGrossScores);

// On va déterminer le nombre de scores brut ajusté à utiliser pour faire le calcul du handicap en fonction du nombre de rondes jouées
if ($numberScoresToUse >= 1 && $numberScoresToUse <= 5) {
    $bestScoresToUse = 1;

} elseif ($numberScoresToUse >= 6 && $numberScoresToUse <= 8) {
    $bestScoresToUse = 2;

} elseif ($numberScoresToUse >= 9 && $numberScoresToUse <= 11) {
    $bestScoresToUse = 3;

} elseif ($numberScoresToUse >= 12 && $numberScoresToUse <= 14) {
    $bestScoresToUse = 4;

} elseif ($numberScoresToUse >= 15 && $numberScoresToUse <= 16) {
    $bestScoresToUse = 5;

} elseif ($numberScoresToUse >= 17 && $numberScoresToUse <= 18) {
    $bestScoresToUse = 6;

} elseif ($numberScoresToUse == 19) {
    $bestScoresToUse = 7;

} elseif ($numberScoresToUse == 20) {
    $bestScoresToUse = 8;
}

// On va prendre les $numberScoresToUse pour ensuite en extraire les X derniers résultats les meilleurs et faire le calcul du handicap et faire un update du handicap dans la table players
$bestScores = array_slice($adjustedGrossScores, 0, $bestScoresToUse);

// MAJ de l'handicap du joueur dans la table players, on va faire la moyenne des meilleurs scores brut ajusté et soustraire 72 
// pour obtenir le handicap de la ligue de golf en montérégie
$newHandicapLeague = round((array_sum($bestScores) / count($bestScores)) - 72, 1);

// Handicap arrondi à 0 décimal pour handicap du prochain event
$newHandicapLeagueRounded = round($newHandicapLeague, 0);

// On va faire un update du handicap dans la table players
$update = "UPDATE players SET handicap_league = ?, handicap_rounded = ? WHERE id = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("ddi", $newHandicapLeague, $newHandicapLeagueRounded, $playerId);

// Exécuter la requête SQL pour faire un update du handicap dans la table players
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour du handicap du joueur."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Étape 4 - Une fois que tous les joueurs du même événement ont leurs résultats ajoutés :

// Comparer le nombre résultats ajoutés pour l'événement en question avec le nombre de joueurs inscrits à cet événement, si les deux nombres sont égaux,
// alors on fait le recalcul des positions actuelles dans la table «player_event_history», 
// sinon on ne fait pas le recalcul des positions actuelles dans la table «player_event_history»
$select = "SELECT COUNT(*) AS total_results ";
$from = "FROM round_results ";
$where = "WHERE event_id = ?";
$sql = $select . $from . $where;
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);

// Exécuter la requête SQL pour faire le comptage du nombre de résultats ajoutés pour l'événement en question
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors du comptage du nombre de résultats ajoutés pour l'événement."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalResults = $row['total_results'];

$select = "SELECT COUNT(*) AS total_players ";
$from = "FROM event_players ";
$where = "WHERE event_id = ?";

$sql = $select . $from . $where;
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);

// Exécuter la requête SQL pour faire le comptage du nombre de joueurs inscrits à l'événement en question
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors du comptage du nombre de joueurs inscrits à l'événement."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalPlayers = $row['total_players'];

// Étape 4.1.0 - On doit faire un recalcul des 3 current_positionss dans la table «player_event_history» 
// Soit pour : current_position, current_fedex_points, fedex_points_gained, current_handicap pour tous les joueurs de l'événement en question, 
// et ce recalcul doit se faire à partir des données de la table round_results pour l'événement en question
if ($totalResults == $totalPlayers) {

    // Étape 4.1.1 - Commencons par «current_position»
    // Recalcul du classement général avec les informations de la table round_results pour l'événement en question
    $select = "SELECT p.id, COALESCE(SUM(r.fedex_points), 0) AS total_points, COALESCE(rr.fedex_points, 0) AS fedex_points_gained ";
    $from = "FROM players p LEFT JOIN round_results r ON p.id = r.player_id
                                LEFT JOIN round_results rr ON p.id = rr.player_id AND rr.event_id = $eventId ";
    $groupBy = "GROUP BY p.id ";
    $orderBy = "ORDER BY total_points DESC, p.handicap_league ASC, p.firstname ASC";
    $sql = $select . $from . $groupBy . $orderBy;

    $result = $conn->query($sql);
    // Vérifier si la requête a réussi
    if (!$result) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de l'exécution de la requête."]);
        $conn->close();
        exit();
    }

    // Création d'un tableau avec les nouvelles positions de tous les joueurs + leurs points de FedEx total
    $currentPositions  = [];

    $position = 1;

    while ($row = $result->fetch_assoc()) {

        $currentPositions[$row['id']] = ["current_position" => $position, "current_fedex_points" => $row['total_points'], "fedex_points_gained" => $row['fedex_points_gained']];
        $position++;
    }   

    // Maintenant, on doit faire un update pour ceux qui ont participé à l'évenement en question
    $select = "SELECT player_id ";
    $from = "FROM player_event_history ";
    $where = "WHERE event_id = ?";
    $sql = $select . $from . $where;
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventId);

    // Exécuter la requête SQL pour récupérer la liste des joueurs qui ont participé à l'événement en question
    if (!$stmt->execute()) {

        error_log($stmt->error);
        
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la récupération de la liste des joueurs qui ont participé à l'événement pour faire le recalcul des positions actuelles dans la table player_event_history."]); 
        
        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
        $stmt->close();
        $conn->close();
        exit();
    }
        
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {

        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Aucun joueur trouvé pour cet événement."]);

        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
        $stmt->close();
        $conn->close();
        exit();
    }

    // La liste des joueurs qui ont participé à l'évenement
    $eventPlayers = [];
    
    while ($row = $result->fetch_assoc()) {

        $eventPlayers[] = $row["player_id"];
    }

    // Maintenant, on va faire un update du champ current_position de la table player_event_history pour tous les joueurs qui ont participé à l'événement en question
    $update = "UPDATE player_event_history SET current_position = CASE player_id ";    
    $switchCase = "";

    // On va assigner la nouvelle position pour seulement les joueurs qui ont participé à l'événement en question
    foreach ($eventPlayers as $playerId) {

        // On récupère la position actuelle du joueur à partir du tableau des positions actuelles que nous avons créé précédemment
        $currentPosition =  $currentPositions[$playerId]['current_position'];
        $switchCase .= " WHEN $playerId THEN $currentPosition ";
    }

    $switchCase .= "END ";

    // La liste des joueurs qui ont participé à l'évenement pour lequel on va faire le update du champ current_position de la table player_event_history
    // array_keys , ici sera plutot 0,1,2,3... parce que le tableau $currentPositions est un tableau associatif avec comme clé l'id du joueur, 
    // alors que le tableau $eventPlayers est un tableau indexé avec comme valeur l'id du joueur,
    $playerIds = implode(",", $eventPlayers);

    // La condition pour faire le update du champ current_position de la table player_event_history pour seulement les joueurs qui ont participé à l'événement en question
    $where = "WHERE event_id = $eventId AND player_id IN (" . $playerIds . ")";

    $sql = $update . $switchCase . $where;
    if (!$conn->query($sql)) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour des positions actuelles dans la table player_event_history."]);

        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
        $conn->close();
        exit();
    }

    // Étape 4.1.2 - Poursuivons avec «current_fedex_points»
    // On va réutiliser le tableau currentPositions contenant les points totaux de la FeDex
    // Maintenant, on va faire un update du champ current_fedex_points de la table player_event_history pour tous les joueurs qui ont participé à l'événement en question
    $update = "UPDATE player_event_history SET current_fedex_points = CASE player_id ";    
    $switchCase = "";

    // On va assigner la nouvelle position pour seulement les joueurs qui ont participé à l'événement en question
    foreach ($eventPlayers as $playerId) {

        // On récupère la position actuelle du joueur à partir du tableau des positions actuelles que nous avons créé précédemment
        $currentFedexPoints =  $currentPositions[$playerId]['current_fedex_points'];
        $switchCase .= " WHEN $playerId THEN $currentFedexPoints ";
    }

    $switchCase .= "END ";

    // La liste des joueurs qui ont participé à l'évenement pour lequel on va faire le update du champ current_fedex_points de la table player_event_history    
    $playerIds = implode(",", $eventPlayers);

    // La condition pour faire le update du champ current_fedex_points de la table player_event_history pour seulement les joueurs qui ont participé à l'événement en question
    $where = "WHERE event_id = $eventId AND player_id IN (" . $playerIds . ")";

    $sql = $update . $switchCase . $where;
    if (!$conn->query($sql)) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour des points FedEx actuels dans la table player_event_history."]);

        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
        $conn->close();
        exit();
    }

    // Étape 4.1.3 - Poursuivons avec «fedex_points_gained»
    // On va encore réutiliser le tableau currentPositions contenant les points de FedEx gagnés pour l'événement en question
    // Maintenant, on va faire un update du champ fedex_points_gained de la table player_event_history pour tous les joueurs qui ont participé à l'événement en question
    $update = "UPDATE player_event_history SET fedex_points_gained = CASE player_id ";    
    $switchCase = "";

    // On va assigner les points de FedEx gagnés pour seulement les joueurs qui ont participé à l'événement en question
    foreach ($eventPlayers as $playerId) {

        // On récupère les points de FedEx gagnés du joueur à partir du tableau des positions actuelles que nous avons créé précédemment
        // Les joueurs qui n'ont pas participé à l'événement en question vont avoir une valeur de 0 pour les points de FedEx gagnés, 
        // mais il ne sont pas utiles pour faire le update du champ fedex_points_gained de la table player_event_history, 
        // car on ne fait le update de ce champ que pour les joueurs qui ont participé à l'événement en question
        $fedexPointsGained =  $currentPositions[$playerId]['fedex_points_gained'];
        $switchCase .= " WHEN $playerId THEN $fedexPointsGained ";
    }

    $switchCase .= "END ";

    // La liste des joueurs qui ont participé à l'évenement pour lequel on va faire le update du champ fedex_points_gained de la table player_event_history    
    $playerIds = implode(",", $eventPlayers);

    // La condition pour faire le update du champ fedex_points_gained de la table player_event_history pour seulement les joueurs qui ont participé à l'événement en question
    $where = "WHERE event_id = $eventId AND player_id IN (" . $playerIds . ")";

    $sql = $update . $switchCase . $where;
    if (!$conn->query($sql)) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour des points FedEx actuels dans la table player_event_history."]);

        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
        $conn->close();
        exit();
    }

    // Étape 4.1.4 - Poursuivons avec «current_handicap»
    // On va faire un update du champ current_handicap de la table player_event_history pour tous les joueurs qui ont participé à l'événement en question, 
    // en utilisant l'handicap actuel de tous les joueurs dans la table players
    $select = "SELECT id, handicap_league ";
    $from = "FROM players";
    $sql = $select . $from;
    $result = $conn->query($sql);

    // Vérifier si la requête a réussi
    if (!$result) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de l'exécution de la requête."]);
        $conn->close();
        exit();
    }

    $currentHandicaps = [];

    while ($row = $result->fetch_assoc()) {

        $currentHandicaps[$row['id']] = $row['handicap_league'];
    }

    // Maintenant, on peut préparer le switch cas epour update de ceux qui ont participé à l'évenement en question
    $update = "UPDATE player_event_history SET current_handicap = CASE player_id ";

    // On va assigner le handicap actuel pour seulement les joueurs qui ont participé à l'événement en question
    foreach ($eventPlayers as $playerId) {
        
    // On récupère le handicap actuel du joueur à partir du tableau des handicaps actuels que nous avons créé précédemment
        $currentHandicap =  $currentHandicaps[$playerId];
        $switchCase .= " WHEN $playerId THEN $currentHandicap ";
    }

    $switchCase .= "END ";

    // La liste des joueurs qui ont participé à l'évenement pour lequel on va faire le update du champ fedex_points_gained de la table player_event_history    
    $playerIds = implode(",", $eventPlayers);

    // La condition pour faire le update du champ fedex_points_gained de la table player_event_history pour seulement les joueurs qui ont participé à l'événement en question
    $where = "WHERE event_id = $eventId AND player_id IN (" . $playerIds . ")";

    $sql = $update . $switchCase . $where;
    if (!$conn->query($sql)) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour des handicaps actuels dans la table player_event_history."]);

        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
        $conn->close();
        exit();
    }

    // Étape 4.2 - On doit fermer l'évenement et remettre à 0 is_update et is_open et mettre à 1 is_closed 
    // pour permettre au système de passer à l'événement suivant
    $update = "UPDATE events SET is_open = 0, is_updated = 0, is_closed = 1 WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("i", $eventId);

    if (!$stmt->execute()) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour de l'événement pour le fermer."]);

        // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données    
        $stmt->close();
        $conn->close();
        exit();
    }

    $message = "Le dernier résultat du joueur a été ajouté avec succès, et l'événement est maintenant fermé.";
} else {

    $message = "Le résultat du joueur a été ajouté avec succès. Il reste encore des résultats à ajouter pour cet événement.";
}

// Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
$stmt->close();
$conn->close();

http_response_code(201);
echo json_encode(["success" => true, "message" => $message]);
