<?php

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/includes/cors.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

$data = json_decode(file_get_contents("php://input"), true);

// Vérifier si les données ont été reçues
if (!$data) {
    echo json_encode(["error" => "No data received"]);
    http_response_code(400);
    exit();
}

// Récupérer les données envoyées depuis le frontend
$sponsorId = $data['sponsor_id'];
$sponsorName = $data['sponsor_name'];
$mediaType = $data['media_type'];

// Requête SQL pour insérer un nouveau log dans la table website_logs_sponsors
$sql = "INSERT INTO website_logs_sponsors (log_date, media_type, sponsor_id, sponsor_name) 
            VALUES (NOW(), ?, ?, ?)";

// Préparer la requête SQL
$stmt = $conn->prepare($sql);

// Lier les paramètres à la requête préparée
$stmt->bind_param("sis", $mediaType, $sponsorId, $sponsorName);

// Exécuter la requête SQL
$stmt->execute();

// Fermer la connexion au résultat du insert dans la base de données
$stmt->close();

// Fermer la connexion à la base de données
$conn->close();

// Retourner une réponse JSON indiquant le succès de l'opération
echo json_encode([
    "success" => true
]);
