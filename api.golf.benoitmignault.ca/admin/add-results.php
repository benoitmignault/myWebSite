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
// Étape 4.2 - On doit fermer l'évenement pour permettre au système de passer à l'événement suivant

// Le classement général se recalcul tout seul à partir du fichier standing.php

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
$select = "SELECT is_open ";
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

// Si l'événement est ouvert, on fait la MAJ des positions actuelles vers le champ previous_position de la table players, 
// sinon on ne fait pas la MAJ des positions vers le champ previous_position de la table players et on passe à l'étape 1
if ($row['is_open'] == 1) {

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

    // Création d'un tableau avec les nouvelles positions de tous les joueurs
    $newPositions = [];

    $position = 1;

    while ($row = $result->fetch_assoc()) {

        $newPositions[] = ["id" => $row['id'], "previous_position" => $position];
        $position++;
    }

    // Étape 0.2 - Mettre à jour le champ previous_position de tous les joueurs 
    // dans la table players avec les positions actuelles du classement avant entrer les résultats de la ronde en cours
    $update = "UPDATE players SET previous_position = CASE id ";    
    $switchCase = "";

    foreach ($newPositions as $player) {
        $switchCase .= "WHEN " . $player['id'] . " THEN " . $player['previous_position'] . " ";
    }

    $switchCase .= "END ";

    // On doit faire un update de tous les joueurs dans la table players de lors positions actuels du classement général
    // Transforme le tableau en chaine de caractères pour la clause WHERE de la requête SQL, en utilisant les id des joueurs
    $playerIds = implode(",", array_keys($newPositions));

    // La condition pour faire le update de tous les joueurs dans la table players de lors positions actuels du classement général
    $where = "WHERE id IN (" . $playerIds . ")";

    $sql = $update . $switchCase . $where;
    if (!$conn->query($sql)) {

        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour des positions des joueurs."]);
        $conn->close();
        exit();
    }

    // Etape 0.3 - Remettre a 0 le is_open pour eviter ajouter des joueurs à un evenement qui est entrains de se faire ajouter des résultats
    $update = "UPDATE events SET is_open = 0 WHERE id = ?";
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
    echo json_encode(["success" => false, "message" => "Aucun score brut trouvé pour ce joueur."]);

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

