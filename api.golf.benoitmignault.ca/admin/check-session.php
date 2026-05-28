<?php

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../includes/cors.php");

// Configurer les paramètres du cookie de session pour permettre 
// les requêtes CORS avec fetch et inclure les cookies de session dans les requêtes fetch
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

// Ce fichier vérifie si l'administrateur est connecté en vérifiant la session.
session_start();

if (isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true) {

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Admin connecté."]);
} else {

    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Accès refusé."]);
}