<?php

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

// Requête SQL pour récupérer les joueurs et leurs points totaux avec un classement basé sur les points, le handicap et le prénom
$select = "SELECT p.id, p.firstname, p.lastname, COALESCE(p.average_score, 0) AS average_score, p.handicap_league, p.previous_position, COALESCE(SUM(r.fedex_points), 0) AS total_points ";
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

    // Fermer la connexion à la base de données
    $conn->close();
    exit();
}

// Sinon, on va parcourir les résultats de la requête pour chaque joueur
$players = [];

while ($row = $result->fetch_assoc()) {

    // Ajouter chaque joueur et ses points totaux au tableau des joueurs
    $players[] = $row; 
}

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "players" => $players]);

// Fermer la connexion à la base de données
$conn->close();
