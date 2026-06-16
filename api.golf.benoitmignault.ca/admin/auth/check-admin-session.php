<?php

// Fichier pour centralisation de la vérification de session d'administrateur.
// Ce fichier est appelé par les autres fichiers d'API pour vérifier 
// si l'administrateur est connecté et si sa session est valide avant de permettre 
// l'accès aux fonctionnalités d'administration.
session_start();

// Vérifier si l'administrateur est connecté
$isAdminLoggedIn = isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true;

// Vérifier si la session est encore valide
$isSessionValid = isset($_SESSION["login_time"]) && (time() - $_SESSION["login_time"]) < 3600;

if (!$isAdminLoggedIn || !$isSessionValid) {

    session_unset();
    session_destroy();
    http_response_code(401);

    echo json_encode(["success" => false, "message" => "Session expirée."]);
    exit();
}