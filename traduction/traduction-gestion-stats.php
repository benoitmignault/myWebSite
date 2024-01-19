<?php
	
	/**
	 * Fonction qui sera utiliser pour traduire le texte dans la page et ainsi que les messages d'erreurs
	 *
	 * @param string $type_langue
	 * @param int $situation
	 * @return array
	 */
	function traduction(string $type_langue, int $situation): array {
		
		$liste_mots = array(["lang" => "", 'btn_new' => "", 'title' => "", 'killer' => "", 'citron' => "",
		                     'newJoueur' => "", 'gain' => "", 'h1' => "", 'victoire' => "", 'fini2e' => "", 'h3' => "",
		                     'autre' => "", 'noId' => "", 'option' => "", 'joueur' => "", 'resultat' => "",
		                     'btn_add' => "", 'btn_erase' => "", 'btn_loginPoker' => "", 'btn_login' => "",
		                     'btn_return' => "");
			
		if ($type_langue === 'francais') {
			$liste_mots["btn_new"] = "Ajouter le nouveau joueur";
			$liste_mots["lang"] = "fr";
			$liste_mots["newJoueur"] = "Nouveau joueur";
			$liste_mots["title"] = "Page de gestion du poker et login";
			$liste_mots["h1"] = "Bienvenue à la page de gestion des statistiques du poker.";
			$liste_mots["h3"] = "Formulaire pour ajouter les statistiques d'un joueur.";
			$liste_mots["option"] = "À sélectionner";
			$liste_mots["joueur"] = "Joueur : ";
			$liste_mots["resultat"] = "Résultat du classement : ";
			$liste_mots["gain"] = "Gain Net :";
			$liste_mots["victoire"] = "Victoire";
			$liste_mots["fini2e"] = "Fini 2e";
			$liste_mots["autre"] = "Autre";
			$liste_mots["killer"] = "Prix killer :";
			$liste_mots["citron"] = "Prix citron :";
			$liste_mots["noId"] = "Numéro du tournois :";
			$liste_mots["btn_add"] = "Ajouter";
			$liste_mots["btn_erase"] = "Effacer";
			$liste_mots["btn_loginPoker"] = "Voir les statistiques";
			$liste_mots["btn_login"] = "Connexion à nouveau";
			$liste_mots["btn_return"] = "Accueil";
			
		} elseif ($type_langue === 'english') {
			$liste_mots["btn_new"] = "Add the new player";
			$liste_mots["lang"] = "en";
			$liste_mots["newJoueur"] = "New player :";
			$liste_mots["title"] = "Poker management page and login";
			$liste_mots["h1"] = "Welcome to the User Management and Poker Statistics page.";
			$liste_mots["h3"] = "Form to add the statistics of a player.";
			$liste_mots["option"] = "Select";
			$liste_mots["resultat"] = "Ranking result : ";
			$liste_mots["gain"] = "Profit";
			$liste_mots["victoire"] = "Victory";
			$liste_mots["killer"] = "Killer price";
			$liste_mots["citron"] = "Lemons price";
			$liste_mots["fini2e"] = "Runner-Up";
			$liste_mots["autre"] = "Other";
			$liste_mots["joueur"] = "Player : ";
			$liste_mots["noId"] = "Tournament Id";
			$liste_mots["btn_add"] = "Add";
			$liste_mots["btn_erase"] = "Erase";
			$liste_mots["btn_loginPoker"] = "View statistics";
			$liste_mots["btn_login"] = "Back to login page";
			$liste_mots["btn_return"] = "Back to Home";
		}
		
		return $liste_mots;
	}
