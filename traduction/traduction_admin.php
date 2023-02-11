<?php
	
	function traduction($champs): array {
		
		if ($champs["typeLangue"] === 'francais') {
			$btn_new = "Ajouter le nouveau joueur";
			$lang = "fr";
			$newJoueur = "Nouveau joueur";
			$title = "Page de gestion du poker et login";
			$h1 = "Bienvenue à la page de gestion des utilisateurs et des statistiques du poker.";
			$h3 = "Formulaire pour ajouter les statistiques d'un joueur.";
			$option = "À sélectionner";
			$joueur = "Joueur : ";
			$resultat = "Résultat du classement : ";
			$gain = "Gain Net :";
			$victoire = "Victoire";
			$fini2e = "Fini 2e";
			$autre = "Autre";
			$killer = "Prix killer :";
			$citron = "Prix citron :";
			$noId = "Id du tournoi :";
			$btn_add = "Ajouter";
			$btn_erase = "Effacer";
			$btn_loginPoker = "Voir les statistique";
			$btn_login = "Retour à page de connexion";
			$btn_return = "Retour à l'accueil";
			
		}
		elseif ($champs["typeLangue"] === 'english') {
			$btn_new = "Add the new player";
			$lang = "en";
			$newJoueur = "New player :";
			$title = "Poker management page and login";
			$h1 = "Welcome to the User Management and Poker Statistics page.";
			$h3 = "Form to add the statistics of a player.";
			$option = "Select";
			$resultat = "Ranking result : ";
			$gain = "Profit";
			$victoire = "Victory";
			$killer = "Killer price";
			$citron = "Lemons price";
			$fini2e = "Runner-Up";
			$autre = "Other";
			$joueur = "Player : ";
			$noId = "Tournament Id";
			$btn_add = "Add";
			$btn_erase = "Erase";
			$btn_loginPoker = "View statistics";
			$btn_login = "Back to login page";
			$btn_return = "Back to Home";
		}
		
		return ["lang" => $lang, 'btn_new' => $btn_new, 'title' => $title, 'killer' => $killer, 'citron' => $citron,
		        'newJoueur' => $newJoueur, 'gain' => $gain, 'h1' => $h1, 'victoire' => $victoire, 'fini2e' => $fini2e, 'h3' => $h3,
		        'autre' => $autre, 'noId' => $noId, 'option' => $option, 'joueur' => $joueur, 'resultat' => $resultat,
		        'btn_add' => $btn_add, 'btn_erase' => $btn_erase, 'btn_loginPoker' => $btn_loginPoker, 'btn_login' => $btn_login,
		        'btn_return' => $btn_return];
	}
