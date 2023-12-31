<?php
	
	/**
	 * Retourne la variable instance de connexion à la BD~
	 * @return false|mysqli|void
	 */
	function connexion() {
		
		// Nouvelle connexion sur hébergement du Studio OL
		$host = "localhost";
		
		// Initialisation des variables
		$user = "";
		$password = "";
		$bd = "";
		
		// Les includes nécessaires
		include_once("info-connexion-bd.php");
		
		$connMYSQL = mysqli_connect($host, $user, $password, $bd);
		$connMYSQL->query("set names 'utf8'");
		
		// Vérification de la connexion
		if ($connMYSQL->connect_error) {
			die('Erreur de connexion (' . $connMYSQL->connect_errno . ') ' . $connMYSQL->connect_error);
		}
		else {
			return $connMYSQL;
		}
	}