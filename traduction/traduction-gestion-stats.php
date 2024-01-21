<?php
	
	/**
	 * Fonction qui sera utiliser pour traduire le texte dans la page
	 * Exceptionnellement pour la page de gestions des stats, on va faire appel à un array de situations,
	 * il y a beaucoup de champs à gérer.
	 *
	 * @param string $type_langue
	 * @param array $liste_situations
	 * @return array
	 */
	function traduction(string $type_langue, array $liste_situations): array {
		
		$liste_mots = array("lang" => "", 'btn_new' => "", 'title' => "", 'killer' => "", 'citron' => "",
		                     'new_player' => "", 'gain' => "", 'p1' => "", 'victoire' => "", 'fini2e' => "", 'p2' => "",
		                     'autre' => "", 'noId' => "", 'option' => "", 'joueur' => "", 'resultat' => "",
		                     'btn_add_stat' => "", 'btn_erase' => "", 'btn_loginPoker' => "", 'btn_login' => "",
		                     'btn_return' => "");
			
		if ($type_langue === 'francais') {
			$liste_mots["lang"] = "fr";
			$liste_mots["new_player"] = "Nouveau joueur";
			$liste_mots["title"] = "Gestion des statistiques";
			$liste_mots["p1"] = "Bienvenue à la page de gestion des statistiques du poker.";
			$liste_mots["p2"] = "Formulaire pour ajouter les statistiques d'un joueur.";
			$liste_mots["option"] = "À sélectionner";
			$liste_mots["joueur"] = "Joueur : ";
			$liste_mots["resultat"] = "Résultat du classement : ";
			$liste_mots["gain"] = "Gain Net :";
			$liste_mots["victoire"] = "Victoire";
			$liste_mots["fini2e"] = "Fini 2e";
			$liste_mots["autre"] = "Autre";
			$liste_mots["killer"] = "Prix killer :";
			$liste_mots["citron"] = "Prix citron :";
			$liste_mots["no_tournois"] = "Numéro du tournois :";
			$liste_mots["btn_add_stat"] = "Ajouter";
			$liste_mots["btn_erase"] = "Effacer";
			$liste_mots["btn_new_player"] = "Ajouter le nouveau joueur";
			$liste_mots["btn_voir_stats"] = "Voir les statistiques";
			$liste_mots["btn_login"] = "Connexion à nouveau";
			$liste_mots["btn_return"] = "Accueil";
			
		} elseif ($type_langue === 'english') {
			$liste_mots["lang"] = "en";
			$liste_mots["new_player"] = "New player :";
			$liste_mots["title"] = "Statistics management";
			$liste_mots["p1"] = "Welcome to the poker statistics management page.";
			$liste_mots["p2"] = "Form to add player statistics.";
			$liste_mots["option"] = "Select";
			$liste_mots["joueur"] = "Player :";
			$liste_mots["resultat"] = "Ranking result : ";
			$liste_mots["gain"] = "Profit :";
			$liste_mots["victoire"] = "Victory";
			$liste_mots["fini2e"] = "Runner-Up";
			$liste_mots["autre"] = "Other";
			$liste_mots["killer"] = "Killer price :";
			$liste_mots["citron"] = "Lemons price :";
			$liste_mots["no_tournois"] = "Tournament number :";
			$liste_mots["btn_add_stat"] = "Add";
			$liste_mots["btn_erase"] = "Erase";
			$liste_mots["btn_new_player"] = "Add the new player";
			$liste_mots["btn_voir_stats"] = "View statistics";
			$liste_mots["btn_login"] = "Sign In again";
			$liste_mots["btn_return"] = "Home";
		}
		
		// Le message qui sera dans la langue voulu
		$liste_mots["liste_messages"] = traduction_situation($type_langue, $liste_situations);
		
		return $liste_mots;
	}
	
	/**
	 * Fonction pour sélectionner le ou les messages en fonction de la situation
	 * En français ou en anglais
	 *
	 * @param string $type_langue
	 * @param array $liste_situations
	 * @return array
	 */
	function traduction_situation(string $type_langue, array $liste_situations): array{
		
		$liste_messages = array();
		
		if ($type_langue === 'francais') {
			$liste_messages = traduction_situation_francais($liste_situations);
			
		} elseif ($type_langue === 'english') {
			$liste_messages = traduction_situation_anglais($liste_situations);
		}
		
		return $liste_messages;
	}
	
	/**
	 * Fonction qui va aller chercher le ou les messages nécessaires en français
	 *
	 * @param int $situation
	 * @return string
	 */
	function traduction_situation_francais(array $liste_situations): array {
		
		$liste_messages = "";
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