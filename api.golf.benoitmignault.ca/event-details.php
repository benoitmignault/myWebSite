<?php

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/includes/cors.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

if (!$conn) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);

    exit();
}

// Requête SQL pour récupérer les détails d'un event spécifique
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validation de l'identifiant du joueur si'il n'existe pas, on retourne 
// une réponse JSON indiquant que l'identifiant du joueur est invalide et 
// on arrête l'exécution du script. 
// Cela permet d'éviter d'exécuter la requête SQL avec un identifiant de joueur invalide, 
// ce qui pourrait entraîner des erreurs ou des résultats inattendus.
if ($eventId <= 0) {

    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Identifiant de l'événement invalide."]);
    $conn->close();
    exit();
}

// Vérifier si l'événement existe avant aller plus loin
$stmt = $conn->prepare("SELECT id FROM events WHERE id = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {

    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Événement introuvable."]);

    $stmt->close();
    $conn->close();
    exit();
}

// Requête SQL pour récupérer les joueurs et leurs points totaux avec un classement basé sur les points, le handicap et le prénom
$select = "SELECT p.firstname, p.lastname, r.gross_score, r.net_score, r.position, r.fedex_points ";

// Utilisation de INNER JOIN pour inclure seulement les joueurs ayant des résultats pour un évent spécifique, 
// et trier les résultats par position, handicap utilisé et prénom pour un classement plus précis
// Si aucun joueur, ça va retourner un résultat vide, ce qui est géré côté frontend pour afficher 
// un message indiquant qu'il n'y a pas de résultats disponibles pour cet événement
$from = "FROM events e INNER JOIN round_results r ON e.id = r.event_id
                        INNER JOIN players p ON r.player_id = p.id ";
$where = "WHERE e.id = ? ";
$orderBy = "ORDER BY r.position, r.handicap_used";
$sql = $select . $from . $where . $orderBy;

// Exécuter la requête SQL
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

// Sinon, on va parcourir les résultats de la requête pour l'événement recherché
$eventDetails = []; 

while ($row = $result->fetch_assoc()) {
   
    // Ajouter chaque ligne de résultat au tableau des résultats d'événements    
    $eventDetails[] = $row; 
}

// Fermer la connexion à la base de données et le résultat de la requête SQL
$stmt->close();
$conn->close();

http_response_code(200);

// Retourner les données au format JSON
echo json_encode($eventDetails, JSON_PRETTY_PRINT);
