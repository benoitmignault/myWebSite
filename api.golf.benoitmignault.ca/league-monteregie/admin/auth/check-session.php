<?php

// Ce fichier vérifie si l'administrateur est connecté en vérifiant la session et sa validité.

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../../includes/cors.php");

// Inclut les informations pour vérifier la session d'administrateur
include(__DIR__ . "/check-admin-session.php");

// Si nous arrivons ici, c'est que la session est valide donc on poursuit les choses prévues
http_response_code(200);

echo json_encode(["success" => true, "message" => "Admin connecté."]);
