<?php

// Configurer les en-têtes pour permettre les requêtes CORS et spécifier le type de contenu JSON
header("Access-Control-Allow-Origin: *");

// TODO: en prod remplacement * par le domaine de l'application frontend
// header("Access-Control-Allow-Origin: https://golf.benoitmignault.ca");

// Permettre les en-têtes spécifiques pour les requêtes CORS
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Permettre les méthodes HTTP spécifiques pour les requêtes CORS
header("Access-Control-Allow-Methods: POST");

// Gérer les requêtes OPTIONS pour les pré-vols CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclure le fichier de connexion à la base de données
include(__DIR__ . "/../includes/fct-connexion-bd.php");

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
$actionType = $data['action_type'];
$targetId = $data['target_id'];
$targetName = $data['target_name'];

// Requête SQL pour insérer un nouveau log dans la table website_logs
$sql = "INSERT INTO website_logs (log_date, action_type, target_id, target_name) VALUES (NOW(), ?, ?, ?)";

// Préparer la requête SQL
$stmt = $conn->prepare($sql);

// Lier les paramètres à la requête préparée
$stmt->bind_param("sis", $actionType, $targetId, $targetName);

// Exécuter la requête SQL
$stmt->execute();

// Fermer la connexion à la base de données
$stmt->close();

echo json_encode([
    "success" => true
]);