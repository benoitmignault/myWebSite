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

// Requête pour récupéré la liste des événements seulement, triés par date d'événement
$select = "SELECT id, event_name, golf_course, golf_course_website, event_date ";
$from = "FROM events ";
$orderBy = "ORDER BY event_date";
$sql = $select . $from . $orderBy;

// Exécuter la requête SQL
$result = $conn->query($sql);

// Vérifier si la requête a réussi
if (!$result) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'exécution de la requête."]);
    $conn->close();
    exit();
}

// Sinon, on va parcourir les résultats de la requête pour chaque événement
$events = []; 

// Parcourir les résultats de la requête et organiser les données par événement
while ($row = $result->fetch_assoc()) {

    // Ajouter chaque événement au tableau
    $events[] = $row; 
}

// Fermer la connexion à la base de données
$conn->close();

http_response_code(200);

// Retourner les données au format JSON
echo json_encode($events);
