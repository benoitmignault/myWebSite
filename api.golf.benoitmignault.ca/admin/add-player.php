<?php

// Ce fichier est utilisé pour ajouter un joueur à la ligue. Il reçoit les données du frontend, 
// les valide, puis les insère dans la base de données.

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../../includes/cors.php");

// Inclut les informations pour vérifier la session d'administrateur
include(__DIR__ . "/auth/check-admin-session.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/../includes/fct-connexion-bd.php");

// S'assurer que la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

// Si on passe la validation du POST et que la session est valide, on poursuit avec la lecture des données reçues, 
// la validation des données, la connexion à la base de données, 
// l'insertion du joueur dans la base de données et le retour d'une réponse JSON indiquant 
// le succès ou l'échec de l'ajout du joueur.
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier si les données ont été reçues
if (!$data) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Aucune donnée reçue"]);    
    exit();
}

// Récupérer les données envoyées depuis le frontend
$firstName = $data['firstName'];
$lastName = $data['lastName'];
$handicap = $data['handicap'];
