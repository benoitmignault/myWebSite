<?php
	
	/**
	 * Fonction qui sera utiliser pour traduire le texte dans la page et ainsi que les messages d'erreurs
	 *
	 * @param string $type_langue
	 * @param int $situation
	 * @return string[]
	 */
	function traduction(string $type_langue, int $situation): array {
		
		// Initialiser le array de mots traduit
		$liste_mots = array("lang" => "", "message" => "", "title" => "", "p1" => "", "li1" => "", "li2" => "", "li3" => "",
		                    "legend" => "", "mdp_Temp" => "", "mdp_1" => "", "mdp_2" => "", "btn_create_New_PWD" => "",
		                    "btn_login" => "", "btn_return" => "");
				
		if ($type_langue === 'francais') {
			$liste_mots["lang"] = "fr";
			$liste_mots["title"] = "Mot de passe en changement !";
			$liste_mots["p1"] = "Vous pouvez maintenant changer votre du mot de passe !";
			$liste_mots["li1"] = "Veuillez inscrire votre mot de passe temporaire.";
			$liste_mots["li2"] = "Veuillez choisir un nouveau mot de passe et le confirmer dans le 3e champs contenant des lettres et chiffres seulement.";
			$liste_mots["li3"] = "Votre nouveau mot de passe doit être différent de l'ancien, pour des raisons de sécurité.";
			$liste_mots["legend"] = "Saisir de quoi de nouveau !";
			$liste_mots["mdp_Temp"] = "Mot de passe temporaire :";
			$liste_mots["mdp_1"] = "Nouveau mot de passe :";
			$liste_mots["mdp_2"] = "Confirmer votre mot de passe :";
			$liste_mots["btn_create_New_PWD"] = "Enregistrer...";
			$liste_mots["page_Login"] = "Se Connecter";
			$liste_mots["return"] = "Retour à l'accueil";
			
		} elseif ($type_langue === 'english') {
			$liste_mots["title"] = "Password is changing !";
			$liste_mots["lang"] = "en";
			$liste_mots["p1"] = "You can now change your password !";
			$liste_mots["li1"] = "Please enter your temporary password.";
			$liste_mots["li2"] = "Please choose a new password and confirm it in the 3rd field containing letters and numbers only.";
			$liste_mots["li3"] = "Your new password must be different from the old one, for security reasons !";
			$liste_mots["legend"] = "Write something new !";
			$liste_mots["mdp_Temp"] = "Temporary password :";
			$liste_mots["mdp_1"] = "New Password :";
			$liste_mots["mdp_2"] = "Confirm your password :";
			$liste_mots["btn_create_New_PWD"] = "Reset password...";
			$liste_mots["page_Login"] = "Sign in";
			$liste_mots["return"] = "Home Page";
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
	
	
	function traductionSituationFR($champs){
		$messageFrench = "";
		switch ($champs['situation']) {
			case 1 : $messageFrench = "Tous les champs sont vides !"; break;
			case 2 : $messageFrench = "Votre nouveau mot de passe serait bon, mais le mot de passe temporaire ne concorde pas avec nos informations !"; break;
			case 3 : $messageFrench = "Votre confirmation de mot de passe n'est pas égal,<br>mais votre mot de passe temporaire concorde avec nos informations !"; break;
			case 4 : $messageFrench = "Votre mot de passe temporaire concorde avec nos informations,<br>mais les champs pour le nouveau mot de passe sont vides !"; break;
			case 5 : $messageFrench = "Votre mot de passe temporaire est vide mais votre nouveau mot de passe serait bon !"; break;
			case 6 : $messageFrench = "Votre mot de passe temporaire concorde avec nos informations,<br>mais un des champs pour le nouveau mot de passe seraient vides !"; break;
			case 7 : $messageFrench = "Votre nouveau et temporaire mot de passe seraient bon,<br>mais vous avez dépassé le temps limite autorisé à changer de mot de passe."; break;
			case 8 : $messageFrench = "Votre nouveau mot de passe a été enregistré avec succès !<br>Nous vous invitons à vous diriger vers la page de connexion ou retourner à la page d'accueil."; break;
			case 9 : $messageFrench = "Votre mot de passe temporaire ne concorde pas avec nos informations et la confirmation du nouveau mot de passe n'est pas égal !"; break;
			case 10 : $messageFrench = "Votre mot de passe temporaire concorde avec nos informations,<br>mais un des deux champs du nouveau mot de passe est invalide !"; break;
			case 11 : $messageFrench = "Votre mot de passe temporaire ne concorde pas avec nos informations et un des deux champs du nouveau mot de passe est invalide !"; break;
			case 12 : $messageFrench = "Votre nouveau mot de passe ne doit pas être égal à celui que vous aviez avant !"; break;
			case 13 : $messageFrench = "Une erreur de communication/manipulation est survenu au moment de vous envoyer le courriel !"; break;
		}
		return $messageFrench;
	}
	
	
	function traductionSituationEN($champs){
		$messageEnglish = "";
		switch ($champs['situation']) {
			case 1 : $messageEnglish = "All fields are empty !"; break;
			case 2 : $messageEnglish = "Your new password would be good, but the temporary password does not match our information !"; break;
			case 3 : $messageEnglish = "Your password confirmation is not equal,<br>but your temporary password matches our information !"; break;
			case 4 : $messageEnglish = "Your temporary password matches our information,<br>but the fields for the new password are empty !"; break;
			case 5 : $messageEnglish = "Your temporary password is empty but your new password would be good !"; break;
			case 6 : $messageEnglish = "Your temporary password matches our information, <br> but one of the fields for the new password would be empty !"; break;
			case 7 : $messageEnglish = "Your new and temporary password would be good,<br>but you have exceeded the time allowed to change your password."; break;
			case 8 : $messageEnglish = "Your new password has been successfully registered !<br>We invite you to go to the login page or return to the home page."; break;
			case 9 : $messageEnglish = "Your temporary password does not match our information and the confirmation of the new password is not equal !"; break;
			case 10 : $messageEnglish = "Your temporary password matches our information, but one of the two fields of the new password is invalid !"; break;
			case 11 : $messageEnglish = "Your temporary password does not match our information and one of the two fields of the new password is invalid !"; break;
			case 12 : $messageEnglish = "Your new password must not be equal to the one you had before !"; break;
			case 13 : $messageEnglish = "A communication / manipulation error occurred when sending you the email !"; break;
		}
		return $messageEnglish;
	}