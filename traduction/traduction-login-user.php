<?php
	
	/**
	 * Fonction qui sera utiliser pour traduire le texte dans la page et ainsi que les messages d'erreurs
	 *
	 * @param string $type_langue
	 * @param int $situation
	 * @return array
	 */
	function traduction(string $type_langue, int $situation): array {
		
		$liste_mots = array("lang" => "", 'title' => "", 'p1' => "", 'li1' => "",
		                     'li2' => "", 'li3' => "", 'legend' => "", 'usager' => "", 'pwd' => "", 'btn_login' => "",
		                     "btn_reset" => "", 'btn_sign_up' => "", 'btn_reset_pwd' => "", 'btn_return' => "", 'message' => "",
							 "exemple_email" => "", "pwd_conf" => "", "email" => "");
		
		if ($type_langue === 'francais') {
			$liste_mots["lang"] = "fr";
			$liste_mots["title"] = "Connexion - utilisateur";
			$liste_mots["p1"] = "Connectez-vous pour explorer les données, fascinantes";
			$liste_mots["p2"] = "Vous êtes sur le point d'accéder à la page des statistiques des tournois de poker amicales";
			$liste_mots["li1"] = "Connectez vous pour voir les statistiques";
			$liste_mots["li2"] = "Veuillez vous créer un compte pour consulter les statistiques, si vous n'en avez pas";
			$liste_mots["li3"] = "Avez-vous oublié votre mot de passe, si oui, on s'en occupe";
			$liste_mots["legend"] = "Vos informations de connexion";
			$liste_mots["usager"] = "Nom d'utilisateur :";
			$liste_mots["pwd"] = "Mot de passe :";
			$liste_mots["btn_login"] = "Connexion";
			$liste_mots["btn_sign_up"] = "S'inscrire";
			$liste_mots["btn_reset"] = "Effacer";
			$liste_mots["btn_reset_pwd"] = "Mot de passe oublié ?";
			$liste_mots["btn_return"] = "Accueil";
			
			// Les mots traduits pour la page de création du user
			$liste_mots["email"] = "Courriel :";
			$liste_mots["pwd_conf"] = "Mot de passe de confirmation :";
			$liste_mots["info_valid_email"] = "exemple@domaine.com";
			$liste_mots["info_valid_user_pwd"] = "lettre & chiffre acceptés";
			$liste_mots["titre_create"] = "Creation - utilisateur";
			$liste_mots["p1_create"] = "Bienvenue sur la page d'inscription !";
			$liste_mots["p2_create"] = "Créez votre compte ici pour accéder aux statistiques détaillées des tournois de poker entre amis";
			$liste_mots["li1_create"] = "Pour créer votre compte, vous avez besoin :";
			$liste_mots["li2_create"] = "Nom d'utilisateur, courriel et d'un mot de passe";
			$liste_mots["li3_create"] = "Une fois votre compte créer, vous pouvez revenir sur la page de « Connexion »";
			
		} elseif ($type_langue === 'english') {
			$liste_mots["lang"] = "en";
			$liste_mots["title"] = "Sign In";
			$liste_mots["p1"] = "Log in to explore the fascinating data";
			$liste_mots["p2"] = "You are about to access the friendly poker tournament statistics page";
			$liste_mots["li1"] = "Sign In to see the statistics";
			$liste_mots["li2"] = "Please create an account to view the statistics, if you do not have one";
			$liste_mots["li3"] = "Have you forgotten your password, if so, we'll take care of it";
			$liste_mots["legend"] = "Your login information";
			$liste_mots["usager"] = "Username :";
			$liste_mots["pwd"] = "Password :";
			$liste_mots["btn_login"] = "Sign In";
			$liste_mots["btn_sign_up"] = "Sign Up";
			$liste_mots["btn_reset"] = "Erase";
			$liste_mots["btn_reset_pwd"] = "Forgot password ?";
			$liste_mots["btn_return"] = "Home";
			
			// Les mots traduits pour la page de création du user
			$liste_mots["email"] = "Email :";
			$liste_mots["pwd_conf"] = "Password confirmation :";
			$liste_mots["info_valid_email"] = "example@domain.com";
			$liste_mots["info_valid_user_pwd"] = "letter & number accepted";
			$liste_mots["titre_create"] = "Sign Up";
			$liste_mots["p1_create"] = "Welcome to the registration page !";
			$liste_mots["p2_create"] = "Create your account here to access detailed statistics of poker tournaments with friends";
			$liste_mots["li1_create"] = "To create your account, you need :";
			$liste_mots["li2_create"] = "Username, email and password";
			$liste_mots["li3_create"] = "Once your account has been created, you can return to the « Sign In » page";
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
			case 1 : $message = "Tous les champs sont vides, veuillez saisir quelque chose !"; break;
			case 2 : $message = "Le champ « Nom d'utilisateur » ne peut pas être vide !"; break;
			case 3 : $message = "Le champ « Mot de passe » ne peut pas être vide !";	break;
			case 4 : $message = "Le nom d'utilisateur n'existe pas dans nos informations !"; break;
			case 5 : $message = "Tous les champs sont invalides, veuillez saisir quelque chose de valide !"; break;
			case 6 : $message = "Le nom d'utilisateur est invalide !"; break;
			case 7 : $message = "Le mot de passe est invalide !"; break;
			case 8 : $message = "Le nom d'utilisateur existe, mais le mot de passe saisi n'est pas celui que nous avons dans nos informations !"; break;
		
			// Maintenant, ici nous aurons les situations pour la gestion dans la création des users
			case 10 : $message = "Votre compte a bien été créé, dans notre système. Vous pouvez vous connecter via le bouton « Connexion »"; break;
			case 12 : $message = "Au moment de créer votre compte, vous avez omis de saisir votre courriel !"; break;
			case 13 : $message = "Au moment de créer votre compte, vous avez omis de saisir votre mot de passe et de le confirmer !"; break;
			case 14 : $message = "Au moment de créer votre compte, vous avez omis de saisir votre mot de passe ou de le confirmer une 2e fois !"; break;
			case 15 : $message = "Au moment de créer votre compte, vous avez omis de saisir votre nom d'utilisateur !"; break;
			case 16 : $message = "Au moment de créer votre compte, vous n'avez pas saisi la même information dans les champs mot de passes !"; break;
			case 17 : $message = "Au moment de créer votre compte, vous ne pouvez pas utiliser la même information pour votre nom d'utilisateur et mot de passe !"; break;
			case 18 : $message = "Au moment de créer votre compte, vous avez saisi une information qui excédait la limite supérieure tolérée par notre système !"; break;
			case 19 : $message = "Au moment de créer votre compte, vous avez saisi une information qui était invalide par notre système !"; break;
			case 20 : $message = "Au moment de créer votre compte, vous avez saisi un nom d'utilisateur déjà utilisé par un autre membre du site !"; break;
			case 21 : $message = "Au moment de créer votre compte, vous avez saisi un courriel déjà utilisé par un autre membre du site !"; break;
			case 22 : $message = "Au moment de créer votre compte, vous avez saisi une information qui excédait la limite inférieure par notre système !"; break;
			case 23 : $message = "Au moment de créer votre compte, vous avez saisi un nom d'utilisateur et un courriel déjà utilisés par un autre membre du site !"; break;
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
			case 1 : $message = "All fields are empty, please enter something !"; break;
			case 2 : $message = "The « Username » field cannot be empty! "; break;
			case 3 : $message = "The « Password » field cannot be empty !";	break;
			case 4 : $message = "Username does not exist in our database !"; break;
			case 5 : $message = "All fields are invalid, please enter something valid !"; break;
			case 6 : $message = "The username is invalid !"; break;
			case 7 : $message = "The password is invalid !"; break;
			case 8 : $message = "The username exists, but the password entered is not the one we have in our database !"; break;
			
			// Maintenant, ici nous aurons les situations pour la gestion dans la création des users
			case 10 : $message = "Your account has been successfully created in our system. You can log in via the « Login » button"; break;
			case 12 : $message = "When creating your account, you forgot to enter your email !"; break;
			case 13 : $message = "When creating your account, you failed to enter and confirm your password !"; break;
			case 14 : $message = "When creating your account, you forgot to enter your password or confirm it a second time !"; break;
			case 15 : $message = "When creating your account, you forgot to enter your username !"; break;
			case 16 : $message = "When creating your account, you did not enter the same information in the password fields !"; break;
			case 17 : $message = "When creating your account, you cannot use the same information for your username and password !"; break;
			case 18 : $message = "When creating your account, you entered information that exceeded the upper limit tolerated by our system !"; break;
			case 19 : $message = "When creating your account, you entered information that was invalid by our system !"; break;
			case 20 : $message = "When creating your account, you entered a username already used by another member of the site !"; break;
			case 21 : $message = "When creating your account, you entered an email already used by another member of the site !"; break;
			case 22 : $message = "When creating your account, you entered information that exceeded the lower limit by our system !"; break;
			case 23 : $message = "When creating your account, you entered a username and email already used by another member of the site !"; break;
		}
		
		return $message;
	}