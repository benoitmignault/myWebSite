<?php
	
	/**
	 * Retour la prochaine sélection de small & big blind
	 * Cette function est utilisé par timer.php & changer.php qui est appelé à partir d'un call Ajax de timer.js
	 * @param $connMYSQL
	 * @param $nomOrganisateur
	 * @param $combinaison
	 * @return array
	 */
	function selectionSmallBigBlind($connMYSQL, $nomOrganisateur, $combinaison): array {
		
		// Initialisation du couple de données à retourner
		$combinaisonSuivante = array('valeurSmall' => 0, 'valeurBig' => 0);
		
		// Définition de constantes pour les chaînes de caractères statiques
		define('SELECT_SELECTION_SMALL_BIG_BLIND', 'small, big');
		define('FROM_SELECTION_SMALL_BIG_BLIND', 'mise_small_big');
		define('WHERE_SELECTION_SMALL_BIG_BLIND', 'user');
		define('ORDER_SELECTION_SMALL_BIG_BLIND', 'small');
		
		// Préparation de la requête SQL avec un alias pour la colonne sélectionnée
		$query = "SELECT " . SELECT_SELECTION_SMALL_BIG_BLIND . " FROM " . FROM_SELECTION_SMALL_BIG_BLIND . " WHERE " . WHERE_SELECTION_SMALL_BIG_BLIND . " = ? " . " ORDER BY " . ORDER_SELECTION_SMALL_BIG_BLIND . " LIMIT ?, ?";
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		
		// Définissez les paramètres de la requête qui n'arrive pas de la méthode
		$nb_result = 1;
		
		// Les & est la référence de la variable que je dois passer en paramètre
		$params = array("sii", &$nomOrganisateur, &$combinaison, &$nb_result);
		
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
				$combinaisonSuivante['valeurSmall'] = intval($row['small']);
				$combinaisonSuivante['valeurBig'] = intval($row['big']);
			}
		}
		
		// Close statement
		$stmt->close();
		
		// On va retourner array de la prochaine combinaison
		return $combinaisonSuivante;
	}
	
	function remplissageCouleurs(): array {
		
		$couleurs = array('couleurRouge' => 255, 'couleurVert' => 255, 'couleurBleu' => 255);
		
		// On récupère les trois types de couleurs
		if (isset($_POST['couleurRouge'])) {
			$couleurs['couleurRouge'] = intval($_POST['couleurRouge']);
		}
		if (isset($_POST['couleurVert'])) {
			$couleurs['couleurVert'] = intval($_POST['couleurVert']);
		}
		if (isset($_POST['couleurBleu'])) {
			$couleurs['couleurBleu'] = intval($_POST['couleurBleu']);
		}
		
		$valeurRougeTemp = $couleurs['couleurRouge'] - 25;
		$valeurVertTemp = $couleurs['couleurVert'] - 25;
		// Si la partie bleu et vert sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
		if ($valeurVertTemp > 0) {
			$couleurs['couleurVert'] = $valeurVertTemp;
			$couleurs['couleurBleu'] = $valeurVertTemp;
		} // Sinon, Si la partie rouge sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
		elseif ($valeurRougeTemp > 0) {
			$couleurs['couleurRouge'] = $valeurRougeTemp;
			$couleurs['couleurVert'] = 0;
			$couleurs['couleurBleu'] = 0;
		}
		else {
			$couleurs['couleurRouge'] = 0;
			$couleurs['couleurVert'] = 0;
			$couleurs['couleurBleu'] = 0;
		}
		
		return $couleurs;
	}

?>
