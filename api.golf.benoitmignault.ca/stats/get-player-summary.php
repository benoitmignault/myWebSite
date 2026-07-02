<?php

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../includes/cors.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/../includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);

    exit();
}

// Si on passe la validation du POST, on poursuit avec la lecture des données reçues, 
// qui sont envoyées depuis le frontend en format JSON. On va les décoder pour les utiliser dans notre code PHP.
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier si les données ont été reçues
if (!$data) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Aucune donnée reçue"]);   
    $conn->close(); 
    exit();
}

// Récupérer les données envoyées depuis le frontend
if (!isset($data['playerId']) || !is_numeric($data['playerId'])) {

     http_response_code(400);
     echo json_encode(["success" => false, "message" => "playerId manquant ou invalide."]);
     $conn->close();

     exit();
 }
 
 $playerId = (int) $data['playerId'];

// Première requete SQL viendra récupérer les informations du joueur, comme son prénom et son nom de famille, son handicap et sa moyenne brute.
// 2026-06-19, ajout de la notion de 0 pour pour la moyenne brute si elle n'existe pas encore pour éviter 
// d'avoir des valeurs nulles dans le résumé du joueur, et pour que ce soit plus explicite que 
// le joueur n'a pas encore de moyenne brute, plutôt que d'avoir une valeur nulle qui pourrait être interprétée 
// comme une erreur dans la récupération des données.
$select = "SELECT firstname, lastname, handicap_league, COALESCE(average_score, 0) AS average_score ";
$from = "FROM players ";
$where = "WHERE id = ?";
$sql = $select . $from . $where;

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $playerId);

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
    echo json_encode(["success" => false, "message" => "Joueur non trouvé."]);

    $stmt->close();
    $conn->close();
    exit();
}

// Préparation de la structure de données pour stocker les informations du joueur
$playerInfo = [
    "full_name" => "",
    "handicap" => 0,
    "average_score" => 0,

    "fedex_position" => 0,
    "fedex_points" => 0,

    "gold_trophies" => 0,
    "silver_trophies" => 0,
    "bronze_trophies" => 0
];

// Récupérer les informations du joueur
$row = $result->fetch_assoc();

// Remplir les informations du joueur dans la structure de données sans validation vu que si on est rendu ici, 
// c'est que le joueur existe, et que les données sont valides, sinon on aurait déjà retourné une erreur 404 ou 500.
$playerInfo["full_name"] = $row["firstname"] . " " . $row["lastname"];
$playerInfo["handicap"] = $row["handicap_league"];
$playerInfo["average_score"] = $row["average_score"];

// Libérer les ressources associées au résultat de la première requête
mysqli_free_result($result);
$stmt->close();

// Deuxième requete SQL viendra récupérer les trophées qu'il a remportés.
// 2026-06-19, ici aussi o nva ajouter la notion de 0 pour les trophées si le joueur n'en a pas encore remporté, 
// pour éviter d'avoir des valeurs nulles dans le résumé du joueur, et pour que ce soit plus explicite que 
// le joueur n'a pas encore remporté de trophées, plutôt que d'avoir des valeurs nulles qui pourraient être interprétées 
// comme une erreur dans la récupération des données.
$select = "SELECT
    COALESCE(SUM(CASE WHEN position = 1 THEN 1 ELSE 0 END), 0) AS gold_trophies,
    COALESCE(SUM(CASE WHEN position = 2 THEN 1 ELSE 0 END), 0) AS silver_trophies,
    COALESCE(SUM(CASE WHEN position = 3 THEN 1 ELSE 0 END), 0) AS bronze_trophies ";

$from = "FROM round_results ";
$where = "WHERE player_id = ?";
$sql = $select . $from . $where;

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $playerId);

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

// Si nous avons des résultats, cela signifie que le joueur a des résultats mais pas nécessairement des trophées, 
// car même si le joueur n'a pas de trophées, la requête retournera quand même une ligne avec des valeurs à 0, 
// mais surtout que rendu ici, on sait que le joueur existe, car on a vérifié ça dans la première requête SQL.

// Récupérer les informations du joueur
$row = $result->fetch_assoc();

// Remplir les informations du joueur dans la structure de données
$playerInfo["gold_trophies"] = $row["gold_trophies"];
$playerInfo["silver_trophies"] = $row["silver_trophies"];
$playerInfo["bronze_trophies"] = $row["bronze_trophies"];

// Libérer les ressources associées au résultat de la deuxième requête
mysqli_free_result($result);
$stmt->close();

// On va reprendre la requête SQL utilisé dans standing.php 
// pour récupérer le classement de la coupe Fedex du joueur, 
// mais cette fois-ci, on va filtrer les résultats pour n'avoir que le joueur dont on veut le résumé.
$select = "SELECT p.id, COALESCE(SUM(r.fedex_points), 0) AS total_points ";
// Utilisation de LEFT JOIN pour inclure tous les joueurs, même ceux sans résultats de tournament
$from = "FROM players p LEFT JOIN round_results r ON p.id = r.player_id ";
$groupBy = "GROUP BY p.id ";
$orderBy = "ORDER BY total_points DESC, p.handicap_league ASC, p.firstname ASC";
$sql = $select . $from . $groupBy . $orderBy;

// Exécuter la requête SQL
$result = $conn->query($sql);

// Vérifier si la requête a réussi
if (!$result) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'exécution de la requête."]);
    $conn->close();
    exit();
}

$position = 1;

// On va essayer de trouve rnotre joueur dans les résultats de la requête 
// pour récupérer sa position dans le classement de la coupe FedEx par un incrémentation 
// d'une variable $position à chaque itération de la boucle, 
// et une fois qu'on trouve notre joueur, on remplit sa position et 
// ses points FedEx dans la structure de données $playerInfo, et on sort de la boucle.
while ($row = $result->fetch_assoc()) {

    // Si on trouve notre joueur dans les résultats de la requête
    if ($row["id"] == $playerId) {

        $playerInfo["fedex_position"] = $position;
        $playerInfo["fedex_points"] = $row["total_points"];
        break;
    }

    // Incrémenter la position pour le prochain joueur
    $position++;
}

// Fermer la connexion à la base de données
$conn->close();

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "playerInfo" => $playerInfo]);
