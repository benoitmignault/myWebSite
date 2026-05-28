<?php

// Configurer les en-têtes pour permettre les requêtes CORS et spécifier le type de contenu JSON	
$allowedOrigins = ["http://localhost:5173", "https://golf.benoitmignault.ca"];

// Vérifier si l'origine de la requête est dans la liste des origines autorisées et définir l'en-tête Access-Control-Allow-Origin en conséquence
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

// IMPORTANT POUR LES SESSIONS PHP + FETCH
header("Access-Control-Allow-Credentials: true");

// En-têtes supplémentaires pour les requêtes CORS 
header("Content-Type: application/json");

// Permettre les méthodes HTTP spécifiques pour les requêtes CORS
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Permettre les en-têtes spécifiques pour les requêtes CORS
header("Access-Control-Allow-Headers: Content-Type");

// Gérer les requêtes OPTIONS pour les pré-vols CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}