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

$data = json_decode(file_get_contents("php://input"), true);

// Vérifier si les données ont été reçues
if (!$data) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Aucune donnée reçue."]);
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

if (!$stmt) {

    http_response_code(500);

    echo json_encode(["success" => false, "message" => "Erreur lors de la préparation de la requête."]);
    
    $stmt->close();
    $conn->close();
    exit();
}

// Lier les paramètres à la requête préparée
$stmt->bind_param("sis", $mediaType, $sponsorId, $sponsorName);

// Exécuter la requête SQL pour insérer le nouveau log dans la base de données
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout du log de sponsor."]);    
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
$stmt->close();
$conn->close();

http_response_code(201);
echo json_encode(["success" => true, "message" => "Log de sponsor ajouté avec succès."]);
