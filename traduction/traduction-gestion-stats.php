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
	 * @param array $liste_situations
	 * @return array
	 */
	function traduction_situation_francais(array $liste_situations): array {
		
		// Préparation de la liste de messages à retourner
		$liste_messages = array();
		
		foreach ($liste_situations as $situation) {
			
			switch ($situation) {
				case 1 : $liste_messages[] = "Les statistiques ont été ajoutées avec succès pour : "; break;
				case 2 : $liste_messages[] = "Le prénom du nouveau joueur a été ajouté avec succès pour : "; break;
				case 3 : $liste_messages[] = "Tous les champs sont vides et doivent être remplis !"; break;
				case 4 : $liste_messages[] = "Vous devez sélectionner un joueur dans la liste. Il n'existe pas, veuillez l'ajouter dans le champ dans la 2e section de la page !"; break;
				case 5 : $liste_messages[] = "Vous devez saisir un type de position pour le joueur !"; break;
				case 6 : $liste_messages[] = "Vous devez saisir un gain pour le joueur !"; break;
				case 7 : $liste_messages[] = "Les gains que vous avez saisis sont invalides, selon nos critères !"; break;
				case 8 : $liste_messages[] = "Vous devez saisir le numéro du tournois !"; break;
				case 9 : $liste_messages[] = "Le numéro du tournois est invalide, selon nos critères !"; break;
				case 10 : $liste_messages[] = "Vous devez saisir la date du tournois dont le joueur a participé !"; break;
				case 11 : $liste_messages[] = "Le format de la date est invalide, selon nos critères !"; break;
				case 12 : $liste_messages[] = "Vous devez saisir le nombre de killer que le joueur a obtenu durant le tournois !"; break;
				case 13 : $liste_messages[] = "Le nombre de killer est invalide, selon nos critères !"; break;
				case 14 : $liste_messages[] = "Vous devez saisir si le joueur a obtenu un prix citron, sinon indiquer 0 !"; break;
				case 15 : $liste_messages[] = "La valeur du prix citron est invalide, selon nos critères !"; break;
				case 16 : $liste_messages[] = "Vous devez fournir le prénom du nouveau joueur aux soirées de poker !"; break;
				case 17 : $liste_messages[] = "Le prénom du nouveau joueur est invalide, selon nos critères !"; break;
				case 18 : $liste_messages[] = "Le prénom du nouveau joueur existe déjà dans notre système, veuillez le choisir en haut !"; break;
			}
		}
		
		return $liste_messages;
	}
	
	/**
	 * Fonction qui va aller chercher le message en anglais correspondant à la situation
	 *
	 * @param array $liste_situations
	 * @return array
	 */
	function traduction_situation_anglais(array $liste_situations): array {
		
		// Préparation de la liste de messages à retourner
		$liste_messages = array();
		
		foreach ($liste_situations as $situation) {
			
			switch ($situation) {
				case 1 : $liste_messages[] = "Statistics have been successfully added for : "; break;
				case 2 : $liste_messages[] = "The new player's first name has been successfully added for : "; break;
				case 3 : $liste_messages[] = "All fields are empty and must be completed !"; break;
				case 4 : $liste_messages[] = "You must select a player from the list. It doesn't exist, please add it to the field in the 2nd section of the page !"; break;
				case 5 : $liste_messages[] = "You must enter a position type for the player !"; break;
				case 6 : $liste_messages[] = "You must enter the amount of money the player won !"; break;
				case 7 : $liste_messages[] = "The amount of money you entered are invalid, according to our criteria!"; break;
				case 8 : $liste_messages[] = "You must enter the tournament number !"; break;
				case 9 : $liste_messages[] = "The tournament number is invalid, according to our criteria !"; break;
				case 10 : $liste_messages[] = "You must enter the date of the tournament in which the player participated !"; break;
				case 11 : $liste_messages[] = "The date format is invalid, according to our criteria !"; break;
				case 12 : $liste_messages[] = "You must enter the number of killers that the player obtained during the tournament !"; break;
				case 13 : $liste_messages[] = "The number of killer is invalid, according to our criteria !"; break;
				case 14 : $liste_messages[] = "You must enter if the player obtained the lemon prize, otherwise enter 0 !"; break;
				case 15 : $liste_messages[] = "The value of the lemon prize is invalid, according to our criteria !"; break;
				case 16 : $liste_messages[] = "You must provide the first name of the new player at poker evenings !"; break;
				case 17 : $liste_messages[] = "The new player's first name is invalid, according to our criteria !"; break;
				case 18 : $liste_messages[] = "The new player's first name already exists in our system, please choose it at the top !"; break;
			}
		}
		
		return $liste_messages;
	}