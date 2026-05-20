<?php

// Configurer les en-têtes pour permettre les requêtes CORS et spécifier le type de contenu JSON
header("Access-Control-Allow-Origin: *");

// TODO: en prod remplacement * par le domaine de l'application frontend
// header("Access-Control-Allow-Origin: https://golf.benoitmignault.ca");

header("Content-Type: application/json");

include(__DIR__ . "/../includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

// Requête SQL pour récupérer les joueurs et leurs points totaux avec un classement basé sur les points, le handicap et le prénom
$select = "SELECT p.id, p.firstname, p.lastname, COALESCE(p.average_score, 0) AS average_score, p.handicap_league, p.previous_position, COALESCE(SUM(r.fedex_points), 0) AS total_points ";
// Utilisation de LEFT JOIN pour inclure tous les joueurs, même ceux sans résultats de tournament
$from = "FROM players p LEFT JOIN round_results r ON p.id = r.player_id ";
$groupBy = "GROUP BY p.id ";
$orderBy = "ORDER BY total_points DESC, p.handicap_league ASC, p.firstname ASC";
$sql = $select . $from . $groupBy . $orderBy;

// Exécuter la requête SQL
$result = $conn->query($sql);

$players = []; // Tableau pour stocker les joueurs et leurs points totaux

while ($row = $result->fetch_assoc()) {
    $players[] = $row; // Ajouter chaque joueur et ses points totaux au tableau
}

// Retourner les données au format JSON
echo json_encode($players, JSON_PRETTY_PRINT);
$conn->close();
