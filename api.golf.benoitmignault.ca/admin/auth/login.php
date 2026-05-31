<?php

// Ce fichier gère la connexion de l'administrateur en vérifiant les identifiants,
// en créant une session PHP sécurisée et en retournant une réponse JSON indiquant le succès ou l'échec de la connexion.

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/../../includes/cors.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/../../includes/fct-connexion-bd.php");

// S'assurer que la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

// Si on passe la validation du post, on poursuit avec la lecture des données reçues, 
// la validation des données, la connexion à la base de données, la vérification des identifiants, 
// la création de session, la mise à jour de la date de dernière connexion et 
// le retour d'une réponse JSON indiquant le succès ou l'échec de la connexion.
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier si les données ont été reçues
if (!$data) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Aucune donnée reçue"]);    
    exit();
}

// Récupérer les données envoyées depuis le frontend
$username = $data['username'];
$password = $data['password'];

// Vérification que les champs username et password sont valides
// Vérification que les champs username et password ne sont pas vides
if (empty($username) || empty($password)) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Veuillez remplir les champs nom d'utilisateur et mot de passe."]);    
    exit();
}

// Éviter les espaces accidentels dans le champ username
if (preg_match('/\s/', $username)) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le nom d'utilisateur ne doit pas contenir d'espaces."]);    
    exit();
}

// Le username ne doit pas excéder 50 caractères
if (strlen($username) > 50) {    
    
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le nom d'utilisateur ne doit pas dépasser 50 caractères."]);    
    exit();
}

// Le password ne doit pas excéder 100 caractères
if (strlen($password) > 100) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le mot de passe ne doit pas dépasser 100 caractères."]);
    exit();
}

// Le username doit avoir une longueur minimale de 3 caractères
if (strlen($username) < 3) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le nom d'utilisateur doit comporter au moins 3 caractères."]);    
    exit();
}

// Le password doit avoir une longueur minimale de 8 caractères
if (strlen($password) < 8) {
    
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le mot de passe doit comporter au moins 8 caractères."]);
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

// Préparation de la requête SQL pour récupérer l'admin avec le username fourni
$select = "SELECT id, username, password_hash ";
$from = "FROM admins ";
$where = "WHERE username = ? ";
$limit = "LIMIT 1";
$sql = $select . $from . $where . $limit;

// Préparation de la requête
$stmt = $conn->prepare($sql);

/* Lecture des marqueurs */
$stmt->bind_param("s", $username);

// Exécuter la requête SQL pour récupérer l'admin avec le username fourni
if (!$stmt->execute()) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);    
    // Fermer la connexion au résultat du insert dans la base de données et la connexion à la base de données
    $stmt->close();
    $conn->close();
    exit();
}

// Sinon, on poursuit avec la vérification des identifiants, 
// la création de session, la mise à jour de la date de dernière connexion et 
// le retour d'une réponse JSON indiquant le succès ou l'échec de la connexion.
$result = $stmt->get_result();

// Close statement
$stmt->close();

if ($result->num_rows === 1) {

    // L'admin existe, vérifier le mot de passe
    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin['password_hash'])) {

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

        // Mot de passe correct, créer une session PHP
        session_start();
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_user_id"] = $admin["id"];
        $_SESSION["admin_username"] = $admin["username"];
        $_SESSION["login_time"] = time();

        // Mettre à jour la date de dernière connexion de l'admin
        $sql = "UPDATE admins SET last_login = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin['id']);
        $stmt->execute();
        $stmt->close();

        // Retourner une réponse JSON indiquant le succès de la connexion + 200 OK
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Connexion réussie."]);
        $conn->close();
        exit;      
    } else {
    
        // Le password ne correspond pas donc on retourne une réponse JSON indiquant l'échec de la vérification du mot de passe
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Password invalide."]);
        $conn->close();
        exit;
    }

} else {
    
    // Le user n'existe pas donc on retourne une réponse JSON indiquant l'échec de la connexion
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Identifiant invalide."]);
    $conn->close();
}
