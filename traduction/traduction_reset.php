<?php
	
	
	function traduction($champs) {
		if ($champs["typeLangue"] === 'francais') {
			$lang = "fr";
			$title = "Mot de passe en changement !";
			$p1 = "Vous pouvez maintenant changer votre du mot de passe !";
			$li1 = "Veuillez inscrire votre mot de passe temporaire.";
			$li2 = "Veuillez choisir un nouveau mot de passe et le confirmer dans le 3e champs contenant des lettres et chiffres seulement.";
			$li3 = "Votre nouveau mot de passe doit être différent de l'ancien, pour des raisons de sécurité.";
			$legend = "Saisir de quoi de nouveau !";
			$mdp_Temp = "Mot de passe temporaire :";
			$mdp_1 = "Nouveau mot de passe :";
			$mdp_2 = "Confirmer votre mot de passe :";
			$btn_create_New_PWD = "Enregistrer...";
			$page_Login = "Se Connecter";
			$return = "Retour à l'accueil";
		} elseif ($champs["typeLangue"] === 'english') {
			$title = "Password is changing !";
			$lang = "en";
			$p1 = "You can now change your password !";
			$li1 = "Please enter your temporary password.";
			$li2 = "Please choose a new password and confirm it in the 3rd field containing letters and numbers only.";
			$li3 = "Your new password must be different from the old one, for security reasons !";
			$legend = "Write something new !";
			$mdp_Temp = "Temporary password :";
			$mdp_1 = "New Password :";
			$mdp_2 = "Confirm your password :";
			$btn_create_New_PWD = "Reset password...";
			$page_Login = "Sign in";
			$return = "Home Page";
		}
		$messageFinal = traductionSituation($champs);
		$arrayMots = ["lang" => $lang, "message" => $messageFinal, "title" => $title, "p1" => $p1, "li1" => $li1, "li2" => $li2, "li3" => $li3, "legend" => $legend, "mdp_Temp" => $mdp_Temp, "mdp_1" => $mdp_1, "mdp_2" => $mdp_2, "btn_create_New_PWD" => $btn_create_New_PWD, "btn_login" => $page_Login, "btn_return" => $return];
		return $arrayMots;
	}
	
	
	function traductionSituation($champs){
		$messageEnPreparation = "";
		if ($champs["typeLangue"] === 'francais') {
			$messageEnPreparation = traductionSituationFR($champs);
		} elseif ($champs["typeLangue"] === 'english') {
			$messageEnPreparation = traductionSituationEN($champs);
		}
		return $messageEnPreparation;
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