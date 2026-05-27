<?php

/**
 * 1. Headers JSON
 * 2. session_start()
 * 3. Vérifier méthode POST
 * 4. Lire JSON reçu React
 * 5. Validation username/password
 * 6. Connexion DB
 * 7. SELECT admin
 * 8. Vérifier user existe
 * 9. password_verify()
 * 10. Créer session PHP
 * 11. UPDATE last_login
 * 12. Log action
 * 13. Retour JSON succès
 */


// Include the database connection file et les heanders pour les requêtes CORS et le type de contenu JSON
include(__DIR__ . "/../includes/fct-connexion-bd.php");

$data = json_decode(file_get_contents("php://input"), true);

// Vérifier si les données ont été reçues
if (!$data) {
    echo json_encode(["error" => "No data received"]);
    http_response_code(400);
    exit();
}

// Récupérer les données envoyées depuis le frontend
$username = $data['username'];
$password = $data['password'];

// Vérification que les champs username et password sont valides
// Vérification que les champs username et password ne sont pas vides
if (empty($username) || empty($password)) {
    echo json_encode(["error" => "Veuillez remplir les champs nom d'utilisateur et mot de passe."]);
    http_response_code(400);
    exit();
}

// Éviter les espaces accidentels dans le champ username
if (preg_match('/\s/', $username)) {
    echo json_encode(["error" => "Le nom d'utilisateur ne doit pas contenir d'espaces."]);
    http_response_code(400);
    exit();
}

// Éviter les espaces accidentels dans le champ password
if (preg_match('/\s/', $password)) {
    echo json_encode(["error" => "Le mot de passe ne doit pas contenir d'espaces."]);
    http_response_code(400);
    exit();
}

// Le username ne doit pas excéder 50 caractères
if (strlen($username) > 50) {
    echo json_encode(["error" => "Le nom d'utilisateur ne doit pas dépasser 50 caractères."]);
    http_response_code(400);
    exit();
}

// Le password ne doit pas excéder 100 caractères
if (strlen($password) > 100) {
    echo json_encode(["error" => "Le mot de passe ne doit pas dépasser 100 caractères."]);
    http_response_code(400);
    exit();
}

// Le username doit avoir une longueur minimale de 3 caractères
if (strlen($username) < 3) {
    echo json_encode(["error" => "Le nom d'utilisateur doit comporter au moins 3 caractères."]);
    http_response_code(400);
    exit();
}

// Le password doit avoir une longueur minimale de 8 caractères
if (strlen($password) < 8) {
    echo json_encode(["error" => "Le mot de passe doit comporter au moins 8 caractères."]);
    http_response_code(400);
    exit();
}

// Établir une connexion à la base de données de la ligue de golf en montérégie
// Si les validations ont passé, on peut établir la connexion à la base de données, 
// car on sait que les données reçues sont valides et qu'on aura moins de risque d'avoir 
// des erreurs de connexion à la base de données dues à des données invalides.
try {

    $conn = connexion_league_golf_monteregie();

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
    
    /* Exécution de la requête */
    $stmt->execute();

    /* Association des variables de résultat */
    $result = $stmt->get_result();
    
    // Close statement
    $stmt->close();

    if ($result->num_rows === 1) {

        // L'admin existe, vérifier le mot de passe
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password_hash'])) {

            // Mot de passe correct, créer une session PHP
            session_start();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];

            // Mettre à jour la date de dernière connexion de l'admin
            $sql = "UPDATE admins SET last_login = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $admin['id']);
            $stmt->execute();
            $stmt->close();            

            // Retourner une réponse JSON indiquant le succès de la connexion + 200 OK
            http_response_code(200);
            echo json_encode(["success" => true]);        
        } else {
        
            // Le password ne correspond pas donc on retourne une réponse JSON indiquant l'échec de la vérification du mot de passe
            http_response_code(401);
            echo json_encode(["success" => false, "error" => "Identifiants invalides."]);
            exit;
        }

    } else {
        
        // Le user n'existe pas donc on retourne une réponse JSON indiquant l'échec de la connexion
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "Identifiants invalides."]);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(["error" => "Erreur de connexion à la base de données."]);
    http_response_code(500);
    exit();

} finally {

    // Fermer la connexion à la base de données
    if (isset($conn)) {
        $conn->close();
    }
