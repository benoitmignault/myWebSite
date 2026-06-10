<?php

// Ce fichier sera pour aller récupérer le prochain évenement qui sera ouvert en ajouant 
// des joueurs à ce dernier.

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../includes/cors.php");

// Inclut les informations pour vérifier la session d'administrateur
include(__DIR__ . "/auth/check-admin-session.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/../includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);

    exit();
}

// Réquête pour récupérer le prochain événement à venir, trié par date d'événement ascendante et limité à 1 résultat
// 2026-06-09, Ajout de la valeur de is_updated pour savoir si l'événement est en cours de MAJ par l'ajouter de résultats de ronde
$select = "SELECT id, event_name, golf_course, event_date, is_updated ";
$from = "FROM events ";
$where = "WHERE is_closed = 0 ";
$orderBy = "ORDER BY event_date ASC ";
$limit = "LIMIT 1";
$sql = $select . $from . $where . $orderBy . $limit;

// Exécuter la requête SQL
$result = $conn->query($sql);

// Vérifier si la requête a réussi
if (!$result) {

    error_log($conn->error);

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'exécution de la requête."]);
    $conn->close();
    exit();
}

// Rétourner le résultat de la requête au format JSON
$event = $result->fetch_assoc();

http_response_code(200);

// Fermer la connexion à la base de données
$conn->close();

// Retourner les données au format JSON
echo json_encode(["success" => true, "event" => $event]);
