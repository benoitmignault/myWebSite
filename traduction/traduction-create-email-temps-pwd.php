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
		$liste_mots = array("lang" => "", 'message' => "", 'title' => "", 'p1' => "", 'li3' => "", 'li1' => "",
		                    'li2' => "", 'legend' => "", 'email' => "", 'btn_envoi_lien' => "", 'btn_return' => "");
		
		if ($type_langue === 'francais') {
			$liste_mots["lang"] = "fr";
			$liste_mots["title"] = "Demande de Réinitialisation";
			$liste_mots["p1"] = "Vous avez oublié votre mot de passe, pas de problème, on s'en occupe !";
			$liste_mots["li3"] = "Cette page permet de réinitialiser votre compte associés aux statistiques de poker.";
			$liste_mots["li1"] = "Veuillez saisir votre courriel.";
			$liste_mots["li2"] = "Ensuite, un courriel vous sera envoyé avec toute les informations relier à votre changement de mot de passe.";
			$liste_mots["legend"] = "Réinitialisation !";
			$liste_mots["email"] = "Courriel :";
			$liste_mots["btn_envoi_lien"] = "Réinitialiser";
			$liste_mots["btn_return"] = "Retour à l'accueil";
			
		} elseif ($type_langue === 'english') {
			$liste_mots["title"] = "Reset Request";
			$liste_mots["lang"] = "en";
			$liste_mots["p1"] = "You forgot your password, no problem, we take care of it !";
			$liste_mots["li3"] = "This page will reset your account associated with poker statistics.";
			$liste_mots["li1"] = "Please enter your email.";
			$liste_mots["li2"] = "Then, a mail will be sent to you with all the information related to your change of password.";
			$liste_mots["legend"] = "Reseting !";
			$liste_mots["email"] = "Email :";
			$liste_mots["btn_envoi_lien"] = "Reset";
			$liste_mots["btn_return"] = "Return to home page";
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
			case 1 : $message = "Le champ «Courriel» est vide !"; break;
			case 2 : $message = "Le courriel saisie est trop long pour l'espace disponible !"; break;
			case 3 : $message = "Le courriel saisie ne respecte pas la forme « exemple@email.com »"; break;
			case 4 : $message = "Le courriel saisie n'existe pas dans nos informations !"; break;
			case 5 : $message = "Une erreur de communication/manipulation est survenu au moment de vous envoyer le courriel !"; break;
			case 6 : $message = "Dans les prochains instant, vous allez recevoir le courriel de réinitialisation avec toutes les informations nécessaire !"; break;
			case 7 : $message = "Erreur Système au moment d'envoyer le courriel !"; break;
			case 8 : $message = "Vous avez déjà reçu un courriel pour changer votre mot de passe, il n'est pas nécessaire de faire une nouvelle demande !"; break;
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
			case 1 : $message = "The «Email» field is empty !"; break;
			case 2 : $message = "The seized mail is too long for the available space !"; break;
			case 3 : $message = "The seized mail does not follow the form « example@email.com » !"; break;
			case 4 : $message = "The entered email does not exist in our information !"; break;
			case 5 : $message = "A communication / manipulation error occurred when sending you the email !"; break;
			case 6 : $message = "In the next few moments, you will receive the reset email with all the necessary information !"; break;
			case 7 : $message = "System error when sending email !"; break;
			case 8 : $message = "You have already received an email to change your password, there is no need to make a new request !"; break;
		}
		
		return $message;
	}
