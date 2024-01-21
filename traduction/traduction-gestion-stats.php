<?php
	
	/**
	 * Fonction qui sera utiliser pour traduire le texte dans la page
	 * Exceptionnellement pour la page de gestions des stats, on va faire appel à un array de situations,
	 * il y a beaucoup de champs à géreré
	 *
	 * @param string $type_langue
	 * @param array $liste_situation
	 * @return array
	 */
	function traduction(string $type_langue, array $liste_situation): array {
		
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
		
		return $liste_mots;
	}
