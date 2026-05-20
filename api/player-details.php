<?php

// Configurer les en-têtes pour permettre les requêtes CORS et spécifier le type de contenu JSON
header("Access-Control-Allow-Origin: *");

// TODO: en prod remplacement * par le domaine de l'application frontend
// header("Access-Control-Allow-Origin: https://golf.benoitmignault.ca");

header("Content-Type: application/json");

include(__DIR__ . "/../includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

// Requête SQL pour récupérer les détails d'un joueur spécifique pour chacun de ses tournois
$playerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Requête SQL pour récupérer les détails d'un joueur spécifique pour chacun de ses tournois
$select = "SELECT e.event_name, e.event_date, r.position, r.gross_score, r.net_score, r.fedex_points ";
$from = "FROM round_results r INNER JOIN events e ON e.id = r.event_id ";
$where = "WHERE r.player_id = $playerId ";
$orderBy = "ORDER BY e.event_date";
$sql = $select . $from . $where . $orderBy;

// Exécuter la requête SQL
$result = $conn->query($sql);

$playerDetails = []; // Tableau pour stocker les détails du joueur

// Parcourir les résultats de la requête et organiser les données par événement pour le joueur spécifique
while ($row = $result->fetch_assoc()) {
    $playerDetails[] = $row;
}

echo json_encode($playerDetails);

$conn->close();