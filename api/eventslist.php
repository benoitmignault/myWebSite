<?php

// Configurer les en-têtes pour permettre les requêtes CORS et spécifier le type de contenu JSON
header("Access-Control-Allow-Origin: *");
// TODO: en prod remplacement * par le domaine de l'application frontend
// header("Access-Control-Allow-Origin: https://golf.benoitmignault.ca");
header("Content-Type: application/json");

include(__DIR__ . "/../includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

// Requête pour récupéré la liste des événements seulement, triés par date d'événement
$select = "SELECT id, event_name, golf_course, event_date ";
$from = "FROM events ";
$orderBy = "ORDER BY event_date";
$sql = $select . $from . $orderBy;

// Exécuter la requête SQL
$result = $conn->query($sql);

$events = []; // Tableau pour stocker la liste des événements

// Parcourir les résultats de la requête et organiser les données par événement
while ($row = $result->fetch_assoc()) {
    $events[] = $row; // Ajouter chaque événement au tableau
}

// Retourner les données au format JSON
echo json_encode($events, JSON_PRETTY_PRINT);
$conn->close();
