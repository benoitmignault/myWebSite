<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include(__DIR__ . "/../includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

// Requête pour récupéré les événements et les résultats associés, triés par date d'événement, position et handicap utilisé

// Requête SQL pour récupérer les joueurs et leurs points totaux avec un classement basé sur les points, le handicap et le prénom
$select = "SELECT e.id AS event_id, e.event_name, e.golf_course, e.event_date, p.firstname,
            p.lastname, r.gross_score, r.handicap_used, r.net_score, r.position, r.fedex_points ";
// Utilisation de LEFT JOIN pour inclure tous les joueurs, même ceux sans résultats de tournament
$from = "FROM events e LEFT JOIN round_results r ON e.id = r.event_id
                        LEFT JOIN players p ON r.player_id = p.id ";
$orderBy = "ORDER BY e.event_date , r.position, r.handicap_used";
$sql = $select . $from . $orderBy;

// Exécuter la requête SQL
$result = $conn->query($sql);

$events = []; // Tableau pour stocker les événements et leurs résultats associés

// Parcourir les résultats de la requête et organiser les données par événement
while ($row = $result->fetch_assoc()) {

    // Identifier l'événement actuel
    $eventId = $row['event_id'];
    
    // Vérifier si l'événement n'existe pas déjà dans le tableau des événements
    if (!isset($events[$eventId])) {

        // Créer une nouvelle entrée pour l'événement avec les détails de l'événement
        $events[$eventId] = [
            "event_id" => $row['event_id'],
            "event_name" => $row['event_name'],
            "golf_course" => $row['golf_course'],
            "event_date" => $row['event_date'],
            "results" => []
        ];
    }

    // Pour déterminer si on a des résultats, on peut vérifier n'importe quelle colonne 
    // qui serait normalement remplie pour un résultat, comme le prénom du joueur
    if ($row['firstname'] !== null) {

        $events[$eventId]['results'][] = [
            "firstname" => $row['firstname'],
            "lastname" => $row['lastname'],
            "gross_score" => $row['gross_score'],
            "handicap_used" => $row['handicap_used'],
            "net_score" => $row['net_score'],
            "position" => $row['position'],
            "fedex_points" => $row['fedex_points']
        ];
    }
}

// Réindexer le tableau des événements pour qu'il soit un tableau numérique
$events = array_values($events);

// Retourner les données au format JSON
echo json_encode($events, JSON_PRETTY_PRINT);

$conn->close();
