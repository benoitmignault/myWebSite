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

    // Fermer la connexion à la base de données
    $conn->close();
    exit();
}

// Déterminer si on doit ignorer les statistiques : on ignore si le champ JSON "exclude_stats" est présent et vaut true.
if (isset($data["exclude_stats"]) && $data["exclude_stats"] === true) {
    
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Statistiques exclues."]);

    // Fermer la connexion à la base de données
    $conn->close();
    exit();
}

// Récupérer les données envoyées depuis le frontend
$sponsorId = $data['sponsor_id'];
$sponsorName = $data['sponsor_name'];
$mediaType = $data['media_type'];

// Récupérer l'adresse IP du client
$ipAddress = $_SERVER['REMOTE_ADDR'];

// Requête SQL pour insérer un nouveau log dans la table website_logs_sponsors
$sql = "INSERT INTO website_logs_sponsors (log_date, media_type, sponsor_id, sponsor_name, ip_address) 
            VALUES (NOW(), ?, ?, ?, ?)";

// Préparer la requête SQL
$stmt = $conn->prepare($sql);

// Lier les paramètres à la requête préparée
$stmt->bind_param("siss", $mediaType, $sponsorId, $sponsorName, $ipAddress);

// Exécuter la requête SQL pour insérer le nouveau log dans la base de données
if (!$stmt->execute()) {

    error_log("LOG SPONSOR : " . $mediaType . " | " . $sponsorName . " | " . date("Y-m-d H:i:s"));
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout du log de sponsor."]);    
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

http_response_code(201);
echo json_encode(["success" => true, "message" => "Log de sponsor ajouté avec succès."]);

// Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
$stmt->close();
$conn->close();
