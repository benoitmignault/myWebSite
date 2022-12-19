<?php
	// TODO : Passer en paramètre seulement les éléments nécessaires
	function selectionSmallBigBlind($connMYSQL, $champs) {
		// Définition de constantes pour les chaînes de caractères statiques
		define('SELECT_SELECTION_SMALL_BIG_BLIND', 'small, big');
		define('FROM_SELECTION_SMALL_BIG_BLIND', 'mise_small_big');
		define('WHERE_SELECTION_SMALL_BIG_BLIND', 'user');
		define('ORDER_SELECTION_SMALL_BIG_BLIND', 'small');
		
		// Préparation de la requête SQL avec un alias pour la colonne sélectionnée
		$query = "SELECT " . SELECT_SELECTION_SMALL_BIG_BLIND . " FROM " . FROM_SELECTION_SMALL_BIG_BLIND . " WHERE " . WHERE_SELECTION_SMALL_BIG_BLIND . " = ? " . " ORDER BY " . ORDER_SELECTION_SMALL_BIG_BLIND . " LIMIT ?, ?";
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		
		// Définissez les paramètres de la requête
		$user = $champs['user'];
		$combinaison = $champs['combinaison'];
		$nb_result = 1;
		// Les & est la référence de la variable que je dois passer en paramètre
		$params = array("sii", &$user, &$combinaison, &$nb_result);
		
		// Exécutez la requête en utilisant call_user_func_array
		call_user_func_array(array($stmt, "bind_param"), $params);
		
		/* Exécution de la requête */
		
		$stmt->execute();
		
		// Vérification de l'exécution de la requête
		if (!$stmt->errno) {
			// Récupération des résultats
			$result = $stmt->get_result();
			$row_cnt = $result->num_rows;
			
			if ($row_cnt > 0) {
				// Traitement des résultats
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$champs['valeurSmall'] = $row['small'];
				$champs['valeurBig'] = $row['big'];
				
				$nbLignes = $champs['maxCombinaison'];
				// Il nous reste une combinaison de moins possible à récupérer
				$nbLignes--;
				
				// Ici, nous avons atteint la dernière combinaison small et big
				if ($champs['combinaison'] == $nbLignes) {
					$champs['tropValeur'] = true;
				}
				// Le retour de fonction n'a trouvé aucun valeur
			}
			else {
				$champs['aucuneValeur'] = true;
			}
		}
		
		// Close statement
		$stmt->close();
		
		// On va retourner pour l'instant
		return $champs;
	}

?>
