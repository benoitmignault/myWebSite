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

// Requête SQL pour récupérer tous les joueurs avec leur prénom et nom de famille
$select = "SELECT id, firstname, lastname ";
$from = "FROM players ";
$orderBy = "ORDER BY firstname, lastname";
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

// Sinon, on va parcourir les résultats de la requête pour chaque joueur
$players = [];

// Compter le nombre total de joueurs
$totalPlayers = 0;

while ($row = $result->fetch_assoc()) {

    // Ajouter chaque joueur et ses points totaux au tableau des joueurs
    $players[] = $row; 
    $totalPlayers++;
}

// Fermer la connexion à la base de données
$conn->close();

http_response_code(200);

// Retourner les données au format JSON
echo json_encode(["success" => true, "players" => $players, "totalPlayers" => $totalPlayers]);
