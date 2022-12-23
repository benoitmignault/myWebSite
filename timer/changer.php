<?php
	
	header("Content-type: application/json; charset=utf-8");
	
	/**
	 * Retourne un boolean à savoir si cette appel Ajax en est vraiment une...
	 * @return bool
	 */
	function is_ajax(): bool {
		
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	/**
	 * Retourne un array de variables qui seront utilisées pour l'appel Ajax du Timer lors d'un changement de mise
	 * @return array
	 */
	function initialisationChamps(): array {
		
		// tropValeur remplace aucuneValeurDispo
		// user remplace nomOrganisateur
		// valeurSmall & valeurBig remplace 'nouvelleCombinaison' => array('valeurSmall' => "00", 'valeurBig' => "00")
		// colorRed & colorGreen & colorBlue remplace 'numberRed' => 255, 'numberGreen' => 255, 'numberBlue' => 255,
		
		return ['situation' => "", 'maxCombinaison' => 0, 'nomOrganisateur' => "", 'combinaison' => 0, "aucuneValeur" => false,
		        'aucuneValeurDispo' => false, 'nouvelleCombinaison' => array('valeurSmall' => "00", 'valeurBig' => "00"),
		        'couleurs' => array('couleurRouge' => 255, 'couleurVert' => 255, 'couleurBleu' => 255)];
	}
	
	/**
	 * Remplissage des variables qui seront utilisées pour l'appel Ajax du Timer lors d'un changement de mise
	 * @param $champs
	 * @return array
	 */
	function remplissageChamps($champs): array {
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['nomOrganisateur'])) {
				$champs['nomOrganisateur'] = $_POST['nomOrganisateur'];
			}
			
			if (isset($_POST['niveauCombinaison'])) {
				$champs['combinaison'] = intval($_POST['niveauCombinaison']);
			}
			
			// Ajout 2021-09-28 , l'information a semblerait disparue...
			if (isset($_POST['maxCombinaison'])) {
				$champs['maxCombinaison'] = intval($_POST['maxCombinaison']);
			}
			
			$champs['couleurs'] = remplissageCouleurs();
		}
		
		return $champs;
	}
	
	/**
	 * Retourne un succès avec le data qui a été récupéré pour afficher ce qui doit être afficher sur le tableau de bord
	 * @param $champs
	 * @return void
	 */
	function returnOfAjaxSucces($champs) {
		
		$return = $champs;
		$return["data"] = json_encode($return, JSON_FORCE_OBJECT);
		echo json_encode($return, JSON_FORCE_OBJECT);
	}
	
	/**
	 * @param $champs
	 * @return void
	 */
	function returnOfAjaxErreur($champs) {
		
		$return["erreur"] = json_encode($champs, JSON_FORCE_OBJECT);
		echo json_encode($return, JSON_FORCE_OBJECT);
	}
	
	if (is_ajax()) {
		include_once("../includes/fct-connexion-bd.php");
		$connMYSQL = connexion();
		
		include_once("./includes/fct-timer.php");
		$champs = initialisationChamps();
		$champs = remplissageChamps($champs);
		
		if (!empty($champs['combinaison']) && !empty($champs['nomOrganisateur'])) {
			$champs['nouvelleCombinaison'] = selectionSmallBigBlind($connMYSQL, $champs['nomOrganisateur'], $champs['combinaison']);
			$champs['combinaison']++; // On incrémente pour aller chercher la prochaine combinaison lors du prochain POST
			
			// Lorsque nous avons une égalité, c'est signe que nous n'avons plus de prochaines small/big
			if ($champs['combinaison'] === $champs['maxCombinaison']) {
				$champs['aucuneValeurDispo'] = true;
			}
			
			returnOfAjaxSucces($champs);
		}
		else {
			$champs["situation1"] = "Il manque des informations cruciales pour récupérer les informations venant de la BD.";
			returnOfAjaxErreur($champs);
		}
	}
	else {
		$champs["situation2"] = "Ce fichier doit être caller via un appel AJAX.";
		returnOfAjaxErreur($champs);
	}