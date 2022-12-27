<?php
	
	/**
	 * Retourne une structure de fichier Json qui contient le dictionnaire
	 * @param $chemin
	 * @return mixed
	 */
	function recuperationContenuFichierJson($chemin) {
		
		// Décoder les données JSON et les stocker dans un tableau associatif
		return json_decode(file_get_contents($chemin), true);
	}
	
	/**
	 * Retourne soit le mot traduit ou le mot lui-même, si l'utilisateur est sur la page en français
	 * @param $motATraduire
	 * @param $dictionnaire
	 * @return string
	 */
	function traduction($motATraduire, $dictionnaire): string {
		
		$mot = "";
		
		if (array_key_exists($motATraduire, $dictionnaire)) {
			// Retourner la traduction du mot, s'il doit être traduit
			if ($_SERVER['typeLangue'] === "english") {
				$mot = $dictionnaire[$motATraduire];
			}
			elseif ($_SERVER['typeLangue'] === "francais") {
				$mot = $motATraduire;
			}
		}
		else {
			// Retourner un message d'erreur si le mot n'est pas présent dans le dictionnaire
			if ($_SERVER['typeLangue'] === "english") {
				$mot = "Word not found";
			}
			elseif ($_SERVER['typeLangue'] === "francais") {
				$mot = "Mot introuvable";
			}
		}
		
		return $mot;
	}
	
