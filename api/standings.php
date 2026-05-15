<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include(__DIR__ . "/includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexionLeagueDB();

// Requête SQL pour récupérer les joueurs et leurs points totaux avec un classement basé sur les points, le handicap et le prénom
$sql = "
    SELECT
    p.id,
    p.firstname,
    p.lastname,
    p.handicap_league,
    SUM(r.fedex_points) AS total_points

    FROM round_results r

    JOIN players p ON p.id = r.player_id

    GROUP BY p.id
    ORDER BY
        total_points DESC,
        p.handicap_league ASC,
        p.firstname ASC";

// Exécuter la requête SQL
$result = $conn->query($sql);

$players = []; // Tableau pour stocker les joueurs et leurs points totaux

while ($row = $result->fetch_assoc()) {
    $players[] = $row; // Ajouter chaque joueur et ses points totaux au tableau
}

// Retourner les données au format JSON
echo json_encode($players);

$conn->close();