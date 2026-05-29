<?php

// Ce fichier vérifie si l'administrateur est connecté en vérifiant la session et sa validité.


// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../includes/cors.php");

// Configurer les paramètres du cookie de session pour permettre 
// les requêtes CORS avec fetch et inclure les cookies de session dans les requêtes fetch
session_set_cookie_params([
    'lifetime' => 3600, // cookie navigateur pour 1 heure
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

// Configurer la durée de vie maximale de la session pour correspondre à celle du cookie
// Côté serveur
ini_set('session.gc_maxlifetime', 3600);

// Ce fichier vérifie si l'administrateur est connecté en vérifiant la session.
session_start();

// Vérifier si l'administrateur est connecté
$isAdminLoggedIn = isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true;

// Vérifier si la session n'est pas expirée
$isSessionValid = isset($_SESSION["login_time"]) && (time() - $_SESSION["login_time"]) < 3600;

// Vérification plus simpliquer avec les deux prévalidation ci-haut
if ($isAdminLoggedIn && $isSessionValid) {

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Admin connecté."]);
} else {

    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Accès refusé."]);

    // On termine le script pour éviter que le reste du code ne s'exécute après la destruction de la session
    exit();
}