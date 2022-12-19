<?php
	header("Content-type: application/json; charset=utf-8");
	
	//Function to check if the request is an AJAX request
	function is_ajax(): bool {
		
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	function initialisationChamps(): array {
		
		return ["maxCombinaison" => 0, "user" => "", "combinaison" => 0, "valeurSmall" => "", "valeurBig" => "", "aucuneValeur" => false,
		        "tropValeur" => false, "colorRed" => 0, "colorGreen" => 0, "colorBlue" => 0];
	}
	
	function remplissageChamps($champs) {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['nomOrganisateur'])) {
				$champs['user'] = $_POST['nomOrganisateur'];
			}
			
			if (isset($_POST['niveauCombinaison'])) {
				$champs['combinaison'] = intval($_POST['niveauCombinaison']);
				$champs['combinaison']++;
			}
			
			// Ajout 2021-09-28 , l'information a semblerait disparue...
			if (isset($_POST['maxCombinaison'])) {
				$champs['maxCombinaison'] = intval($_POST['maxCombinaison']);
			}
			
			if (isset($_POST['colorRed'])) {
				$champs['colorRed'] = intval($_POST['colorRed']);
			}
			
			if (isset($_POST['colorGreen'])) {
				$champs['colorGreen'] = intval($_POST['colorGreen']);
			}
			
			if (isset($_POST['colorBlue'])) {
				$champs['colorBlue'] = intval($_POST['colorBlue']);
			}
			
			$value_Red_temp = $champs['colorRed'] - 25;
			$value_Green_temp = $champs['colorGreen'] - 25;
			// Si la partie bleu et vert sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
			if ($value_Green_temp > 0) {
				$champs['colorGreen'] = $value_Green_temp;
				$champs['colorBlue'] = $value_Green_temp;
				// Si la partie rouge sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
			}
			elseif ($value_Red_temp > 0) {
				$champs['colorRed'] = $value_Red_temp;
				$champs['colorGreen'] = 0;
				$champs['colorBlue'] = 0;
			}
			else {
				$champs['colorRed'] = 0;
				$champs['colorGreen'] = 0;
				$champs['colorBlue'] = 0;
			}
		}
		return $champs;
	}
	
	function returnOfAjax($champs) {
		$return = $champs;
		$return["data"] = json_encode($return, JSON_FORCE_OBJECT);
		echo json_encode($return, JSON_FORCE_OBJECT);
	}
	
	if (is_ajax()) {
		if (isset($_POST["niveauCombinaison"]) && isset($_POST["nomOrganisateur"])) {
			include_once("../../includes/fct-connexion-bd.php");
			$connMYSQL = connexion();
			if ($connMYSQL) {
				include_once("../../includes/fct-timer.php");
				$champs = initialisationChamps();
				$champs = remplissageChamps($champs);
				$champs = selectionSmallBigBlind($connMYSQL, $champs);
				returnOfAjax($champs);
			}
			else {
				$champs["situation1"] = "Impossible d'accéder à la BD. Veuillez réessayer plus tard !";
				$return["erreur"] = json_encode($champs, JSON_FORCE_OBJECT);
				echo json_encode($return, JSON_FORCE_OBJECT);
			}
		}
		else {
			$champs["situation2"] = "Il manque des informations importantes. Revalider vos informations !";
			$return["erreur"] = json_encode($champs, JSON_FORCE_OBJECT);
			echo json_encode($return, JSON_FORCE_OBJECT);
		}
		
	}
	else {
		$champs["situation3"] = "Ce fichier doit être caller via un appel AJAX !";
		$return["erreur"] = json_encode($champs, JSON_FORCE_OBJECT);
		echo json_encode($return, JSON_FORCE_OBJECT);
	}
?>
