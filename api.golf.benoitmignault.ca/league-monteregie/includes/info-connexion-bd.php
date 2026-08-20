<?php
	// Pour sécurisé l'information, nous avons inclure les informations sensibles dans un fichier qu'on va exclure du repo sur Github
	// Mais sera présente comme étant un include à l'intérieur de la fct de la connexion
	// Séparation des fonctions de connexions par projet, pour éviter des erreurs de connexion à la mauvaise BD
	$user = "benoitmi_benoit";
	$password = "A.W,E+@F@?G,D*Q,I@";

	// Création de la BD pour la ligue de golf en montérégie
	$bd_league = "benoitmignault_league_golf_monteregie";

	/**
	 * Fonction de connexion à la base de données de la ligue de golf en montérégie
	 * @return mysqli La connexion à la base de données
	 */
	function connexionLeagueDB() {

		$host = "localhost";

		include(__DIR__ . "/info-connexion-bd.php");

		$conn = new mysqli(
			$host,
			$user,
			$password,
			$bd_league
		);

		if ($conn->connect_error) {

			die(json_encode(["error" => $conn->connect_error]));
		}

		$conn->set_charset("utf8mb4");

		return $conn;
	}
