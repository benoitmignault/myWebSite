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

// Si on passe la validation du POST et que la session est valide, on poursuit avec la lecture des données reçues, 
// la validation des données, la connexion à la base de données, 
// l'insertion de l'événement dans la base de données et le retour d'une réponse JSON indiquant 
// le succès ou l'échec de l'ajout de l'événement.
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier si les données ont été reçues
if (!$data) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Aucune donnée reçue"]);    
    exit();
}

// Récupérer les données envoyées depuis le frontend
$eventName = $data['name']; // Le nom de l'événement, par exemple "Tournoi de printemps 2024"
$eventLocation = $data['location']; // Le nom du terrain de golf où l'événement aura lieu
$eventWebSite = $data['url']; // Le site web

// Convertir la date au format YYYY-MM-DD pour s'assurer que le format est correct pour la base de données
$eventDate = date("Y-m-d", strtotime($data['date']));

// Validation des données
if (empty($eventName) || empty($eventLocation) || empty($eventWebSite) || empty($eventDate)) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Veuillez remplir tous les champs."]);    
    exit();
}

// Éviter les espaces accidentels dans le site web
if (preg_match('/\s/', $eventWebSite)) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le site web ne doit pas contenir d'espaces."]);    
    exit();
}

// Établir une connexion à la base de données de la ligue de golf en montérégie
// Si les validations ont passé, on peut établir la connexion à la base de données, 
// car on sait que les données reçues sont valides et qu'on aura moins de risque d'avoir 
// des erreurs de connexion à la base de données dues à des données invalides.
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);
    exit();
}

$sql = "INSERT INTO events (event_name, golf_course, golf_course_website, event_date) VALUES (?, ?, ?, ?)";

// Préparer la requête SQL pour insérer le nouvel événement dans la base de données en utilisant des requêtes préparées pour éviter les injections SQL
$stmt = $conn->prepare($sql);

// Lier les paramètres à la requête préparée
$stmt->bind_param("ssss", $eventName, $eventLocation, $eventWebSite, $eventDate);

// Exécuter la requête SQL pour insérer le nouvel événement dans la base de données et vérifier si l'insertion a réussi
if (!$stmt->execute()) {

    error_log($stmt->error);

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout de l'événement."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
$stmt->close();
$conn->close();

http_response_code(201);
echo json_encode(["success" => true, "message" => "L'événement a été ajouté avec succès."]);
