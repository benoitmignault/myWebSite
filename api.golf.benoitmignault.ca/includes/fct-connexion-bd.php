<?php

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