<?php
	
	/**
	 * Fonction qui sera utilisée pour ajouter un log dans la table pour voir qui se connecter sur la page des statistiques de poker
	 *
	 * @param mysqli $connMYSQL
	 * @param array $array_Champs
	 * @return array
	 */
	function requete_SQL_ajout_log_connexion(mysqli $connMYSQL, array $array_Champs): array {
		
		// Ici, on va saisir une entrée dans la BD pour savoir qui se connecte aux statistiques de poker
		$date = date("Y-m-d H:i:s");
		
		$insert = "INSERT INTO";
		$table = " login_stat_poker ";
		$colonnes = "(user, date, id_user) ";
		$values = "VALUES (?, ?, ?)";
		$query = $insert . $table . $colonnes . $values;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		try {
			/* Lecture des marqueurs */
			$stmt->bind_param('ssi', $array_Champs["user"],$date, $array_Champs["id_user"]);
			
			/* Exécution de la requête */
			$stmt->execute();
		} catch (Exception $err){
			// Récupérer les messages d'erreurs
			$array_Champs["message_erreur_bd"] = $err->getMessage();
			
			// Sera utilisée pour faire afficher le message erreur spécial
			$array_Champs["erreur_system_bd"] = true;
		} finally {
			// Fermer la préparation de la requête
			$stmt->close();
		}
		
		return $array_Champs;
	}