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
			
			// la key user_valid est plus pour vérifier si le user est toujours valide dans gestion-stats ou show-stats
			// lien_crypter_still_good & pwd_old_new_diff est pour le reset de password
			if ($value === true && $key !== "user_valid" && $key !== "lien_crypter_still_good" && $key !== "pwd_old_new_diff") {
				
				$erreur_presente = true;
				break; // Arrêter la boucle si un élément correspondant est trouvé
			}
		}
		
		return $erreur_presente;
	}
	
