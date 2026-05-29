<?php

// Ce fichier gère la déconnexion de l'administrateur en détruisant la session.

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../../includes/cors.php");

session_start();

// Vider toutes les variables de session
session_unset();

// Détruire la session
session_destroy();

http_response_code(200);

echo json_encode([
    "success" => true,
    "message" => "Déconnexion réussie."
]);