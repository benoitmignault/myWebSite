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
		date_default_timezone_set('America/New_York');
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
	
	/**
	 * Fonction pour encrypter le password pour ne pas pouvoir le récupérer clairement
	 * Selon une recommandation :
	 * https://stackoverflow.com/questions/30279321/how-to-use-password-hash
	 * On ne doit pas jouer avec le salt....
	 *
	 * @param string $password
	 * @return string
	 */
	function encryptement_password(string $password): string {
		
		return password_hash($password, PASSWORD_BCRYPT);
	}
	
	/**
	 * Fonction pour valider que le password est celui qui est dans la BD en faisant une comparaison avec l'encryption
	 *
	 * @param string $password
	 * @param string $password_bd
	 * @return bool
	 */
	function validation_password_bd(string $password, string $password_bd): bool {
		
		// On compare le password saisie avec celui qui était dans la BD avec le user
		return password_verify($password, $password_bd);
	}
	
	/**
	 * Fonction pour aller mettre à jour le token de session pour le user en cours de connexion
	 * Au lieu de stocker le password en clair dans les variables de Session
	 * @param mysqli $connMYSQL
	 * @param array $array_Champs
	 * @return array
	 */
	function requete_SQL_update_token_session(mysqli $connMYSQL, array $array_Champs, string $token_session): array {
		
		$update = "UPDATE ";
		$table = "login set token_session = ? ";
		$where = "WHERE user = ?";
		$query = $update . $table . $where;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		try {
			/* Lecture des marqueurs */
			$stmt->bind_param('ss', $token_session, $array_Champs["user"]);
			
			/* Exécution de la requête */
			$stmt->execute();
		} catch (Exception $err){
			// Récupérer les messages d'erreurs
			$array_Champs["message_erreur_bd"] = $err->getMessage();
			
			// Sera utilisée pour faire afficher le message erreur spécial
			$array_Champs["erreur_system_bd"] = true;
		} finally {
			// Vérification qu'on a créé un record
			if ($stmt->affected_rows === 1){
				$array_Champs['update_token_success'] = true;
			}
			// Fermer la préparation de la requête
			$stmt->close();
		}
		
		return $array_Champs;
	}