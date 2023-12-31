<?php
	
	/**
	 * Retourne la variable instance de connexion à la BD~
	 * @return false|mysqli|void
	 */
	function connexion() {
		
		// Nouvelle connexion sur hébergement du Studio OL
		$host = "localhost";
		$user = "confidential information";
		$password = "confidential information";
		$bd = "confidential information";
		
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
