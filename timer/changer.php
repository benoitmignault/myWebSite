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
			if (isset($_POST['typeLangue'])) {
				$_SERVER['typeLangue'] = $_POST['typeLangue'];
			}
			
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
		
		$return["dataAjaxSucces"] = json_encode($champs, JSON_FORCE_OBJECT);
		echo json_encode($return, JSON_FORCE_OBJECT);
	}
	
	/**
	 * Retourne un échec avec le message d'erreur approprié
	 * @param $situation
	 * @return void
	 */
	function returnOfAjaxErreur($situation) {
		
		$champsErr['situation'] = $situation;
		// Il est important de passer un Array
		$return["dataAjaxErreur"] = json_encode($champsErr, JSON_FORCE_OBJECT);
		echo json_encode($return, JSON_FORCE_OBJECT);
	}
	
	/**
	 * @return void
	 */
	function main() {
		
		include_once("../includes/fct-connexion-bd.php");
		include_once("./includes/fct-timer.php");
		
		define('CHEMIN_DICTIONNAIRE_TIMER', "../dictionary/timer.json");
		$dictionnaire = recuperationContenuFichierJson(CHEMIN_DICTIONNAIRE_TIMER);
		
		$connMYSQL = connexion();
		
		$champs = initialisationChamps();
		$champs = remplissageChamps($champs);
		
		if (is_ajax()) {
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
				define('SITUATION1', "Il manque des informations cruciales pour récupérer les informations venant de la BD.");
				
				$champs['situation'] = traduction(SITUATION1, $dictionnaire);
				returnOfAjaxErreur($champs['situation']);
			}
		}
		else {
			define('SITUATION2', "Ce fichier doit être caller via un appel AJAX.");
			
			$champs['situation'] = traduction(SITUATION2, $dictionnaire);
			returnOfAjaxErreur($champs['situation']);
		}
	}
	
	// Appel de la fonction principale
	main();
	