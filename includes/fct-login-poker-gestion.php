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
	 * Fonction pour aller vérifier que nos informations dans notre cookie sont toujours avec notre BD
	 * On va vérifier seulement pour le user et son token de session
	 * On va récupérer aussi le id du user qui sera utilé plus tard
	 *
	 * @param mysqli $connMYSQL -> connexion aux tables de benoitmignault.ca
	 * @param array $array_Champs
	 * @param string $token_session
	 * @return array
	 */
	function requete_SQL_verif_user_valide(mysqli $connMYSQL, array $array_Champs, string $token_session): array {
		
		$select = "SELECT ID ";
		$from = "FROM login ";
		$where = "WHERE user = ? AND token_session = ?";
		
		// Préparation de la requête SQL avec les parties nécessaires
		$query = $select . $from . $where;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		
		/* Lecture des marqueurs */
		$stmt->bind_param("ss", $_SESSION['user'], $token_session);
		
		/* Exécution de la requête */
		$stmt->execute();
		
		/* Association des variables de résultat */
		$result = $stmt->get_result();
		
		// Close statement
		$stmt->close();
		
		// Retourne l'information des informations de connexion, si existant...
		return recuperation_info_user($connMYSQL, $result, $array_Champs);
	}
	
	/**
	 * Fonction qui va retourner si le user est bien valide avec une comparaison positive du password
	 * Cette fonction retournera l'information @see requete_SQL_verif_user_valide
	 *
	 * @param mysqli $connMYSQL
	 * @param object $result
	 * @param array $array_Champs
	 * @return array
	 */
	function recuperation_info_user(mysqli $connMYSQL, object $result, array $array_Champs): array {
		
		// Récupération de la seule ligne possible contenu un array
		$row = $result->fetch_array(MYSQLI_ASSOC);
		
		// Le tableau résultat existe et il n'est pas null
		if (isset($row) && is_array($row)) {
			
			// Maintenant, le password arrive encrypté
			$array_Champs['user_valid'] = true;
			// Assignation des informations pour la connexion, pour plus tard
			$array_Champs['id_user'] = $row["ID"];
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
	 *
	 * @param mysqli $connMYSQL
	 * @param array $array_Champs
	 * @param string $token_session
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