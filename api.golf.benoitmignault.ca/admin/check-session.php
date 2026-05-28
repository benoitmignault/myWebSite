<?php

session_start();

header("Content-Type: application/json");

var_dump($_SESSION);

exit;

// Ce fichier vérifie si l'administrateur est connecté en vérifiant la session.
session_start();

var_dump($_SESSION); // Affiche le contenu de la session pour le débogage

header("Content-Type: application/json");

if (isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true) {

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Admin connecté."]);
} else {

    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Accès refusé."]);
}