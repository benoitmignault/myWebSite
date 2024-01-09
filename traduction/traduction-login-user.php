<?php
	
	/**
	 * Fonction qui sera utiliser pour traduire le texte dans la page et ainsi que les messages d'erreurs
	 *
	 * @param string $type_langue
	 * @param int $situation
	 * @return string[]
	 */
	function traduction(string $type_langue, int $situation): array {
		
		$liste_mots = array("lang" => "", 'emailInfo' => "", 'title' => "", 'email' => "", 'p1' => "", 'li1' => "",
		                     'li2' => "", 'li3' => "", 'legend' => "", 'usager' => "", 'mdp' => "", 'btn_login' => "",
		                     'btn_signUp' => "", 'btn_reset' => "", 'btn_return' => "", 'message' => "");
		
		if ($type_langue === 'francais') {
			$liste_mots["lang"] = "fr";
			$liste_mots["title"] = "Connexion - utilisateur";
			$liste_mots["p1"] = "Bienvenue à la page de connexion des statistiques du poker entre amis !";
			$liste_mots["li1"] = "Vous devez vous authentifier, pour faire afficher les statistiques désirées";
			$liste_mots["li2"] = "Si vous n'avez pas de compte, veuillez vous en créer un.";
			$liste_mots["li3"] = "Veuillez spécifier un nom d'utilisateur et un courriel unique.";
			$liste_mots["legend"] = "Connexion";
			$liste_mots["usager"] = "Nom d'utilisateur :";
			$liste_mots["pwd"] = "Mot de passe :";
			$liste_mots["btn_login"] = "Se Connecter";
			$liste_mots["btn_sign_up"] = "S'inscrire";
			$liste_mots["btn_reset"] = "Mot de passe oublié ?";
			$liste_mots["btn_return"] = "Accueil";
			
		} elseif ($type_langue === 'english') {
			$liste_mots["lang"] = "en";
			$liste_mots["title"] = "Logging - user";
			$liste_mots["p1"] = "Welcome to the login page to see the statistic of poker between friends !";
			$liste_mots["li1"] = "You must authenticate, to display the desired statistics";
			$liste_mots["li2"] = "If you do not have an account, please create one.";
			$liste_mots["li3"] = "Please specify a username and a unique email.";
			$liste_mots["legend"] = "Logging";
			$liste_mots["usager"] = "Username :";
			$liste_mots["pwd"] = "Password :";
			$liste_mots["btn_login"] = "Login";
			$liste_mots["btn_sign_up"] = "Sign Up";
			$liste_mots["btn_reset"] = "Forgot password ?";
			$liste_mots["btn_return"] = "Home";
		}
		
		// Le message qui sera dans la langue voulu
		$liste_mots["message"] = traduction_situation($type_langue, $situation);
		
		return $liste_mots;
	}
	
	/**
	 * Fonction pour sélectionner le message de la situation unique possible, que ça soit en français ou anglais
	 * @param string $type_langue
	 * @param int $situation
	 * @return string
	 */
	function traduction_situation(string $type_langue, int $situation): string{
		
		$message_situation = "";
		if ($type_langue === 'francais') {
			$message_situation = traduction_situation_francais($situation);
			
		} elseif ($type_langue === 'english') {
			$message_situation = traduction_situation_anglais($situation);
		}
		
		return $message_situation;
	}
	
	/**
	 * Fonction qui va aller chercher le message en français correspondant à la situation
	 *
	 * @param int $situation
	 * @return string
	 */
	function traduction_situation_francais(int $situation): string {
		
		$message = "";
		switch ($situation) {
			case 7 : $message = "Au moment de vous connectez, vous n'avez rien saisie dans le champ «mot de passe» !"; break;
			case 8 : $message = "Au moment de vous connectez, vous n'avez rien saisie dans le champ «nom d'utilisateur» !";	break;
			case 9 : $message = "Au moment de vous connectez, le nom d'utilisateur saisie n'existe pas !"; break;
			case 10 : $message = "Au moment de vous connecter, votre mot de passe saisie est invalide avec votre utilisateur !<br>
        		Si vous avez oublié votre mot de passe, veuillez appuyer sur le bouton «Mot de passe oublié ?» et suivre les instructions."; break;
			case 13 : $message = "Attention les tous les champs sont vides !"; break;
			case 15 : $message = "Attention les champs peuvent contenir seulement des caractères alphanumériques !"; break;
			case 16 : $message = "Félicitation ! Votre compte a été crée avec succès !"; break;
		}
		
		return $message;
	}
	
	/**
	 * Fonction qui va aller chercher le message en anglais correspondant à la situation
	 *
	 * @param int $situation
	 * @return string
	 */
	function traduction_situation_anglais(int $situation): string {
		
		$message = "";
		switch ($situation) {
			case 7 : $message = "When you log in, you have not entered anything in the «Password» field !"; break;
			case 8 : $message = "When you log in, you have not entered anything in the «Username» field !"; break;
			case 9 : $message = "When you log in, the «username» entered does not exist !"; break;
			case 10 : $message = "When you log in, your «password» entered is invalid with your user !<br>
        If you have forgotten your password, please press the «Forgot password ?» button and follow the instructions."; break;
			case 13 : $message = "Be careful, all the fields are empty !"; break;
			case 15 : $message = "Warning, the fields can only contain alphanumeric characters !"; break;
			case 16 : $message = "Congratulations ! Your account has been successfully created !"; break;
		}
		
		return $message;
	}