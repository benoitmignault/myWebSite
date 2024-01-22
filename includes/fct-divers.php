<?php
	
	/**
	 * Fonction qui sera utilisée partout, où il y a des validations
	 * Retournera la valeur boolean true|false
	 *
	 * @param array $array_Champs
	 * @return bool
	 */
	function verification_valeur_controle(array $array_Champs): bool{
		
		$erreur_presente = false;
		// Validation que nous avons au moins une erreur, dans le lot
		foreach ($array_Champs as $key => $value) {
			
			if ($value === true && $key !== "user_valid") {
				
				$erreur_presente = true;
				break; // Arrêter la boucle si un élément correspondant est trouvé
			}
		}
		
		return $erreur_presente;
	}
	
