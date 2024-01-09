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
	
	function traductionSituation($champs): string {
		
		$messageEnPreparation = "";
		if ($champs["typeLangue"] === 'francais') {
			$messageEnPreparation = traductionSituationFR($champs);
		}
		elseif ($champs["typeLangue"] === 'english') {
			$messageEnPreparation = traductionSituationEN($champs);
		}
		
		return $messageEnPreparation;
	}
	
	// Il faudra un message si le email est deja utiliser lors de la creation du compte
	function traductionSituationFR($champs): string {
		
		$messageFrench = "";
		switch ($champs['situation']) {
			case 1 :
				$messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans les champs «Mot de passe» et «Courriel» !";
				break;
			case 2 :
				$messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans le champs «courriel» !";
				break;
			case 3 :
				$messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans le champs «nom d'utilisateur» !";
				break;
			case 4 :
				$messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans le champs «mot de passe» !";
				break;
			case 5 :
				$messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans les champs «nom d'utilisateur» et «mot de passe» !";
				break;
			case 6 :
				$messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans les champs «nom d'utilisateur» et «courriel» !";
				break;
			case 7 :
				$messageFrench = "Au moment de vous connectez, vous n'avez rien saisie dans le champ «mot de passe» !";
				break;
			case 8 :
				$messageFrench = "Au moment de vous connectez, vous n'avez rien saisie dans le champ «nom d'utilisateur» !";
				break;
			case 9 :
				$messageFrench = "Au moment de vous connectez, le nom d'utilisateur saisie n'existe pas !";
				break;
			case 10 :
				$messageFrench = "Au moment de vous connectez, votre mot de passe saisie est invalide avec votre utilisateur !<br>
        Si vous avez oublié votre mot de passe, veuillez appuyer sur le bouton «Mot de passe oublié ?» et suivre les instructions.";
				break;
			case 11 :
				$messageFrench = "Au moment de créer votre compte, les champs «nom d'utilisateur» et «mot de passe» doivent être différent !";
				break;
			case 12 :
				$messageFrench = "Au moment de créer votre compte, le nom d'utilisateur choisi est déjà utilisé par quelqu'un d'autre !";
				break;
			case 13 :
				$messageFrench = "Attention les tous les champs sont vides !";
				break;
			case 14 :
				$messageFrench = "Attention les longueurs permises en nombre de caractères pour les champs suivants sont :<br>
        «nom d'utilisateur» &rarr; 15<br> «mot de passe» &rarr; 25<br> «courriel» &rarr; 50 !";
				break;
			case 15 :
				$messageFrench = "Attention les champs peuvent contenir seulement des caractères alphanumériques !";
				break;
			case 16 :
				$messageFrench = "Félicitation ! Votre compte a été crée avec succès !";
				break;
			case 17 :
				$messageFrench = "Attention le courriel ne respecte la forme standard soit : exemple@courriel.com !";
				break;
			case 18 :
				$messageFrench = "Au moment de créer votre compte, le courriel ne doit pas être utiliser déjà par quelqu'un !";
				break;
			case 34 :
				$messageFrench = "Attention ! Au moment je crée votre le compte, il y a eu une erreur système. Veuillez recommencer !";
				break;
		}
		
		return $messageFrench;
	}
	
	// Il faudra un message si le email est deja utiliser lors de la creation du compte
	function traductionSituationEN($champs): string {
		
		$messageEnglish = "";
		switch ($champs['situation']) {
			case 1 :
				$messageEnglish = "At the time of creating your account, you have not entered anything in the fields «Password» and «Email» !";
				break;
			case 2 :
				$messageEnglish = "At the time of creating your account, you have not entered anything in the «Email» field !";
				break;
			case 3 :
				$messageEnglish = "At the time of creating your account, you have not entered anything in the «Username» field !";
				break;
			case 4 :
				$messageEnglish = "At the time of creating your account, you have not entered anything in the «Password» field !";
				break;
			case 5 :
				$messageEnglish = "At the time of creating your account, you have not entered anything in the fields «Username» and «Password» !";
				break;
			case 6 :
				$messageEnglish = "At the time of creating your account, you have not entered anything in the «Username» and «Email» fields ! ";
				break;
			case 7 :
				$messageEnglish = "When you log in, you have not entered anything in the «Password» field !";
				break;
			case 8 :
				$messageEnglish = "When you log in, you have not entered anything in the «Username» field !";
				break;
			case 9 :
				$messageEnglish = "When you log in, the «username» entered does not exist !";
				break;
			case 10 :
				$messageEnglish = "When you log in, your «password» entered is invalid with your user !<br>
        If you have forgotten your password, please press the «Forgot password ?» button and follow the instructions.";
				break;
			case 11 :
				$messageEnglish = "When creating your account, the fields «Username» and «Password» must be different !";
				break;
			case 12 :
				$messageEnglish = "When creating your account, the chosen «username» is already used by someone else !";
				break;
			case 13 :
				$messageEnglish = "Be careful, all the fields are empty !";
				break;
			case 14 :
				$messageEnglish = "Warning, the lengths allowed in number of characters for the following fields are :<br>
        «Username» &rarr; 15<br> «Password» &rarr; 25<br> «Email» &rarr; 50 !";
				break;
			case 15 :
				$messageEnglish = "Warning, the fields can only contain alphanumeric characters !";
				break;
			case 16 :
				$messageEnglish = "Congratulations ! Your account has been successfully created !";
				break;
			case 17 :
				$messageEnglish = "Warning, the email does not respect the standard form : example@courriel.com !";
				break;
			case 18 :
				$messageEnglish = "When creating your account, the email should not be used by anyone already !";
				break;
			case 34 :
				$messageEnglish = "Warning ! When I created your account, there was a system error. Please try again !";
				break;
		}
		
		return $messageEnglish;
	}
	