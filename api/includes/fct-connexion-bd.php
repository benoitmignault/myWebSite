<?php

	// Configurer les en-têtes pour permettre les requêtes CORS et spécifier le type de contenu JSON	
	$allowedOrigins = ["http://localhost:5173", "https://golf.benoitmignault.ca"];

	// Vérifier si l'origine de la requête est dans la liste des origines autorisées et définir l'en-tête Access-Control-Allow-Origin en conséquence
	if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
		header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
	}

	// En-têtes supplémentaires pour les requêtes CORS 
	header("Content-Type: application/json");

	// Permettre les méthodes HTTP spécifiques pour les requêtes CORS
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

	// Permettre les en-têtes spécifiques pour les requêtes CORS
	header("Access-Control-Allow-Headers: Content-Type");

	// Gérer les requêtes OPTIONS pour les pré-vols CORS
	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		http_response_code(200);
		exit();
	}

	/**
	 * Fonction pour établir une connexion à la BD de la ligue de golf en montérégie
	 * 
	 * @return mysqli
	 */
	function connexion_league_golf_monteregie(): mysqli {

		// Nouvelle connexion sur hébergement du Studio OL
		$host = "localhost";
		
		// Venant du fichier info-connexion-bd.php
		$user = "";
		$password = "";
		$bd_league = "";

		include_once("info-connexion-bd.php");

		$connMYSQL = mysqli_connect(
			$host,
			$user,
			$password,
			$bd_league
		);

		if (!$connMYSQL) {

			die('Erreur de connexion : ' . mysqli_connect_error());
		}

		// Définir le jeu de caractères pour la connexion recommander par ChatGPT pour inclure emoticônes et autres caractères spéciaux
		$connMYSQL->set_charset("utf8mb4");

		return $connMYSQL;
	}