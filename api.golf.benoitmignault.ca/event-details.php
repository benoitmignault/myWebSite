<?php

// Inclut les informations nécessaires pour CORS
include(__DIR__ . "/includes/cors.php");

// Inclut la fonction de connexion à la base de données
include(__DIR__ . "/includes/fct-connexion-bd.php");

// Établir une connexion à la base de données de la ligue de golf en montérégie
$conn = connexion_league_golf_monteregie();

// Requête SQL pour récupérer les détails d'un event spécifique
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Requête SQL pour récupérer les joueurs et leurs points totaux avec un classement basé sur les points, le handicap et le prénom
$select = "SELECT p.firstname, p.lastname, r.gross_score, r.net_score, r.position, r.fedex_points ";

// Utilisation de INNER JOIN pour inclure seulement les joueurs ayant des résultats pour un évent spécifique, 
// et trier les résultats par position, handicap utilisé et prénom pour un classement plus précis
// Si aucun joueur, ça va retourner un résultat vide, ce qui est géré côté frontend pour afficher 
// un message indiquant qu'il n'y a pas de résultats disponibles pour cet événement
$from = "FROM events e INNER JOIN round_results r ON e.id = r.event_id
                        INNER JOIN players p ON r.player_id = p.id ";
$where = "WHERE e.id = $eventId ";
$orderBy = "ORDER BY r.position, r.handicap_used";
$sql = $select . $from . $where . $orderBy;

// Exécuter la requête SQL
$result = $conn->query($sql);

// Vérifier si la requête a réussi
if (!$result) {

    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'exécution de la requête."]);
    $conn->close();
    exit();
}

// Sinon, on va parcourir les résultats de la requête pour l'événement recherché
$event = []; 

while ($row = $result->fetch_assoc()) {
   
    // Ajouter chaque ligne de résultat au tableau des résultats d'événements    
    $event[] = $row; 
}

http_response_code(200);
// Fermer la connexion à la base de données
$conn->close();

// Retourner les données au format JSON
echo json_encode($event, JSON_PRETTY_PRINT);
