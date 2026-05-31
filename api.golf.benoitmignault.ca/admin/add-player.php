<?php

// Ce fichier est utilisé pour ajouter un joueur à la ligue. Il reçoit les données du frontend, 
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
$handicapStart = $data['handicap'];

// Validation des données
if (empty($firstName) || empty($lastName)) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Veuillez remplir les champs prénom et nom."]);    
    exit();
}

// Éviter les espaces accidentels dans le champ prénom et nom
if (preg_match('/\s/', $firstName) || preg_match('/\s/', $lastName)) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le prénom et le nom ne doivent pas contenir d'espaces."]);    
    exit();
}

// S'assurer que l'handicap est numérique mais avec aucune validation sur le min ou max, car il peut être supérieur à 54 pour les joueurs débutants. Cependant, on peut ajouter une validation pour s'assurer que l'handicap est un nombre positif et raisonnable (par exemple, entre 0 et 54) pour éviter les erreurs de saisie.
if (!is_numeric($handicapStart)) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le handicap doit être un nombre."]);    
    exit();
}

// On doit arrondir à un entier l'handicap pour les évenemnts
$handicapRounded = round($handicapStart);

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

$sql = "INSERT INTO players (
            firstname,
            lastname,
            average_score,
            handicap_start,
            handicap_league,
            handicap_rounded,
            previous_position
        ) VALUES (?, ?, NULL, ?, ?, ?, NULL)";

// Préparer la requête SQL pour insérer un nouveau joueur dans la table players
$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode(["success" => false, "message" => "Erreur lors de la préparation de la requête."]);

    $stmt->close();
    $conn->close();
    exit();
}

// Lier les paramètres à la requête préparée
$stmt->bind_param("ssddd", $firstName, $lastName, $handicapStart, $handicapStart, $handicapRounded);

// Exécuter la requête SQL pour insérer le nouveau joueur dans la base de données
if (!$stmt->execute()) {

    error_log($stmt->error);
    
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout du joueur."]); 
    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
$stmt->close();
$conn->close();

http_response_code(201);
echo json_encode(["success" => true, "message" => "Joueur ajouté avec succès."]);
