<?php
	
	/**
	 * Retourne la variable instance de connexion à la BD
	 * Afin de garder de manière sécuritaire, la BD, nous allons inclure les informations de connexions dans un fichier.
	 * Ce fichier va rester sur le local ou envoyer vers le site web mais pas sur GirHub.
	 *
	 * @return mysqli
	 */
	function connexion():mysqli {
		
		// Nouvelle connexion sur hébergement du Studio OL
		$host = "localhost";
		
		// Initialisation des variables, pour éviter des fausses erreurs de IntelliJ
		// Venant du fichier info-connexion-bd.php
		$user = "";
		$password = "";
		$bd = "";
		
		// Les includes nécessaires pour associer les informations des variables plus haut
		include_once("info-connexion-bd.php");
		
		$connMYSQL = mysqli_connect($host, $user, $password, $bd);
		$connMYSQL->query("set names 'utf8'");
		
		// Vérification de la connexion
		if ($connMYSQL->connect_error) {
			die('Erreur de connexion (' . $connMYSQL->connect_errno . ') ' . $connMYSQL->connect_error);
			
		} else {
			return $connMYSQL;
		}
	}