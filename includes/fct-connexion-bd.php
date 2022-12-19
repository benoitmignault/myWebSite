<?php
	function connexion() {
		// Nouvelle connexion sur hébergement du Studio OL
		$host = "localhost";
		$user = "benoitmi_benoit";
		$password = "d-&47mK!9hjGC4L-";
		$bd = "benoitmi_benoitmignault.ca.mysql";
		
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

?>