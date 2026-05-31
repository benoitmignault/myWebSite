<?php

// Ce fichier est utilisé pour ajouter un événement à la ligue. Il reçoit les données du frontend, 
// les valide, puis les insère dans la base de données.

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../includes/cors.php");

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