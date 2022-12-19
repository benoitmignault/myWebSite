<?php
	header("Content-type: application/json; charset=utf-8");
	
	//Function to check if the request is an AJAX request
	function is_ajax(): bool {
		
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	function initialisation_Champs(): array {
		
		return ["maxCombinaison" => 0, "user" => "", "combinaison" => 0, "valeurSmall" => "", "valeurBig" => "", "aucuneValeur" => false, "trop_valeur" => false, "color_red" => 0, "color_green" => 0, "color_blue" => 0];
	}
	
	function remplissageChamps($champs) {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['nom_orginateur'])) {
				$champs['user'] = $_POST['nom_orginateur'];
			}
			
			if (isset($_POST['niveau_combinaison'])) {
				$champs['combinaison'] = intval($_POST['niveau_combinaison']);
				$champs['combinaison']++;
			}
			
			// Ajout 2021-09-28 , l'information a semblerait disparue...
			if (isset($_POST['maxCombinaison'])) {
				$champs['maxCombinaison'] = intval($_POST['maxCombinaison']);
			}
			
			if (isset($_POST['color_red'])) {
				$champs['color_red'] = intval($_POST['color_red']);
			}
			
			if (isset($_POST['color_green'])) {
				$champs['color_green'] = intval($_POST['color_green']);
			}
			
			if (isset($_POST['color_blue'])) {
				$champs['color_blue'] = intval($_POST['color_blue']);
			}
			
			$value_Red_temp = $champs['color_red'] - 25;
			$value_Green_temp = $champs['color_green'] - 25;
			// Si la partie bleu et vert sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
			if ($value_Green_temp > 0) {
				$champs['color_green'] = $value_Green_temp;
				$champs['color_blue'] = $value_Green_temp;
				// Si la partie rouge sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
			}
			elseif ($value_Red_temp > 0) {
				$champs['color_red'] = $value_Red_temp;
				$champs['color_green'] = 0;
				$champs['color_blue'] = 0;
			}
			else {
				$champs['color_red'] = 0;
				$champs['color_green'] = 0;
				$champs['color_blue'] = 0;
			}
		}
		return $champs;
	}
	
	function returnOfAJAX($champs) {
		$return = $champs;
		$return["data"] = json_encode($return, JSON_FORCE_OBJECT);
		echo json_encode($return, JSON_FORCE_OBJECT);
	}
	
	if (is_ajax()) {
		if (isset($_POST["niveau_combinaison"]) && isset($_POST["nom_orginateur"])) {
			include_once("../../fonction_commune/fct_connexion_bd.php");
			$connMYSQL = connexionBD();
			if ($connMYSQL) {
				include_once("../../fonction_commune/fct_timer.php");
				$champs = initialisation_Champs();
				$champs = remplissageChamps($champs);
				$champs = selection_small_big_blind($connMYSQL, $champs);
				returnOfAJAX($champs);
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
