<?php

function traduction($typeLangue, $user) {
    $user = strtoupper($user);
    if ($typeLangue === 'francais') {
        $methode = "Méthode d'affichage";
        $selection = "Sélection";
        $btn_methode = "Bouton de la méthode";
        $lang = "fr";
        $titre = "Page des statistiques";
        $rang = "Rang";
        $h1 = "<h1>Bienvenue à vous &rarr; <span class='userDisplay'>{$user}</span> &larr; sur la page des statistiques du poker des vendredis entre amis.</h1>";
        $legend1 = "Voici les différentes méthodes affichages des stats du poker :";
        $method1 = "Affichage brute sans aucune modification.";
        $method2 = "Affichage de toutes les visites d'un joueur.";
        $method3 = "Le sommaire d'un joueur en particulier.";
        $method4 = "Le sommaire de tous les joueurs.";
        $method4ratio = "<span class=\"retourLigne\"><br></span> (Ratio &rarr; Gain / Présence)";
        $method5 = "Affichage d'un tournois par son numéro.";
        $method6 = "Affichage d'un tournois par la date.";
        $method7 = "Le sommaire de tous les joueurs avec leurs prix citrons et killers. ";
        $method7ratio = "<span class=\"retourLigne\"><br></span> (Ratio &rarr; Killer / Présence)";
        $h3 = "Le numéro du bouton sera la méthode sélectionnée";
        $legend2 = "Veuillez sélectionner votre méthode :";
        $label1 = "Pour les méthodes 2 et 3, veuillez sélectionner un joueur : ";
        $label2 = "Pour les méthodes 5, veuillez sélectionner un numéro tournois : ";
        $label3 = "Pour les méthodes 6, veuillez sélectionner une date d'un tournois : ";
        $option = "À sélectionner";
        $legend3 = "Voici le résultat de la méthode d'affichage choisie :";
        $joueur = "Joueur";
        $gain = "Gain";
        $victoire = "Victoire";
        $citron = "Prix Citron";
        $fini2 = "Fini 2e";
        $gainPresence = "Ratio";
        $noTournois = "No. partie";
        $nbTournois = "Nb parties";
        $date = "Date";
        $killer = "Killer";
        $msgErreur_joueur = "Veuillez sélectionner un joueur !";
        $msgErreur_ID = "Veuillez sélectionner un numéro de tournois !";
        $msgErreur_Date = "Veuillez sélectionner une date d'un tournois !";
        $btnLogin = "Page de connexion";
        $btnReturn = "Page d'Accueil";
        $returnUp = "Retour au choix d'affichage";
        $msgInfo_killer_citron = "À partir du 101e tournois,</span> un des joueurs à proposer de commencer des statistiques sur d'autres aspect du jeu soit :";
        $les_gagnant_100E = "Les gagnants du 100e :";
        $les_gagnant_150E = "Les gagnants du 150e :";
        $information_post_tournois = "Au moment de faire les 100e et 150e tournois, nous avons offert des médailles aux 3 premiers de chaque tournois.";        
        $info_trie_method4_gain = "Triage «Gain» : Gain & Victoires & Fini 2e en <span class=\"charGros\">décroissance</span> et Nb parties en <span class=\"charGros\">croissance</span>";
        $info_trie_method4_ratio = "Triage «Ratio» : Gain / Nb parties en <span class=\"charGros\">décroissance</span> et Nb parties en <span class=\"charGros\">croissance</span>";
        $info_trie_method7_killer = "Triage «Killer» : Nb Killer en <span class=\"charGros\">décroissance</span> & Nb Citron en <span class=\"charGros\">croissance</span> et Nb parties en <span class=\"charGros\">croissance</span>";
        $info_trie_method7_ratio = "Triage «Ratio» : Nb Killer / Nb parties jouées en <span class=\"charGros\">décroissance</span> et Nb parties en <span class=\"charGros\">croissance</span>";
        $info_utile = "Information utile";

    } elseif ($typeLangue === 'english') {
        $methode = "Display method";
        $selection = "Selection";
        $btn_methode = "Method button";
        $lang = "en";
        $titre = "Statistics page";
        $rang = "Rank";
        $h1 = "<h1>Welcome to you &rarr; <span class='userDisplay'>{$user}</span> &larr; on the statictics page about the friday nights poker between somes friends.</h1>";
        $legend1 = "Here are the differents methods of displaying poker statistics";
        $method1 = "Display all information with no modification.";
        $method2 = "Display all information about one player.";
        $method3 = "The summary about one player.";
        $method4 = "The summary about all player.";
        $method4ratio = "<span class=\"retourLigne\"><br></span> (Ratio &rarr; Profit / Amount Games)";
        $method5 = "Display a tournament by number.";
        $method6 = "Display a tournament by date.";
        $method7 = "The summary of all players with their prices lemons and killers. ";
        $method7ratio = "<span class=\"retourLigne\"><br></span> (Ratio &rarr; Killer / Amount Games)";
        $h3 = "The number on the button will match with the number of the method";
        $legend2 = "Please choose your method";
        $label1 = "About the method 2 and 3, please select one player";
        $label2 = "About the method 5, please select a tournament number:";
        $label3 = "About the method 6, please select a date from a tournament:";
        $option = "Select";
        $citron = "Lemon Price";
        $killer = "Killer Price";
        $legend3 = "This is the result of the selected method";
        $joueur = "Player";
        $gain = "Gain";
        $victoire = "Wins";
        $fini2 = "2nd";        
        $noTournois = "Game Num";
        $nbTournois = "Nb games";
        $gainPresence = "Ratio";
        $date = "Date";
        $msgErreur_joueur = "Please select one player";
        $msgErreur_ID = "Please select a tournament number !";
        $msgErreur_Date = "Please select a date from a tournament !";
        $btnLogin = "Login page";
        $btnReturn = "Home page";
        $returnUp = "Back to the method of displaying";
        $msgInfo_killer_citron = "From the 101st tournament,</span> one of the players to propose to start statistics on other aspects of the game either :";
        $les_gagnant_100E = "The winner of 100th :";
        $les_gagnant_150E = "The winner of 150th :";
        $information_post_tournois = "At the end of the 100th and 150th tournaments, we offered medals to the first 3 of each tournament.";
        $info_trie_method4_gain = "«Gain» sorting: Gain & Wins & Finished 2nd in <span class=\"charGros\">Decrease</span> and Nb games in <span class=\"charGros\">Increase</span>";
        $info_trie_method4_ratio = "«Ratio» sorting: Gain / Nb games in <span class=\"charGros\">Decrease</span> and Nb games in <span class=\"charGros\">Increase</span>";
        $info_trie_method7_killer = "«Killer» sorting: Nb Killer in <span class=\"charGros\">Decrease</span> & Nb Lemon in <span class=\"charGros\">Increase</span> and Nb games in <span class=\"charGros\">Increase</span>";
        $info_trie_method7_ratio = "«Ratio» sorting: Nb Killer / Nb games in <span class=\"charGros\">Decrease</span> and Nb games in <span class=\"charGros\">Increase</span>";
        $info_utile = "Useful information";
    }

    $arrayMots = array("lang" => $lang, 'method4ratio' => $method4ratio, 'method7ratio' => $method7ratio, 'gainPresence' => $gainPresence,
    'rang' => $rang, 'titre' => $titre, 'h1' => $h1, 'legend1' => $legend1, 'method1' => $method1, 'method2' => $method2, 'methode' => $methode,  'selection' => $selection, 'btn_methode' => $btn_methode,
    'method3' => $method3, 'method4' => $method4, 'method5' => $method5, 'method6' => $method6, 'method7' => $method7, "msgInfo_killer_citron" => $msgInfo_killer_citron,
    'h3' => $h3, 'legend2' => $legend2, 'label1' => $label1, 'label2' => $label2, 'label3' => $label3, 'option' => $option, "les_gagnant_100E" => $les_gagnant_100E, "les_gagnant_150E" => $les_gagnant_150E,
    'legend3' => $legend3, 'joueur' => $joueur, 'gain' => $gain, 'killer' => $killer, 'victoire' => $victoire, 'fini2' => $fini2, 'information_post_tournois' => $information_post_tournois,
    'noTournois' => $noTournois, 'nbTournois' => $nbTournois, 'date' => $date, 'citron' => $citron, 'msgErreur_joueur' => $msgErreur_joueur, 'info_utile' => $info_utile,
    'msgErreur_ID' => $msgErreur_ID, 'msgErreur_Date' => $msgErreur_Date, 'btnLogin' => $btnLogin, 'btnReturn' => $btnReturn, 'returnUp' => $returnUp,
    'info_trie_method4_gain' => $info_trie_method4_gain, 'info_trie_method4_ratio' => $info_trie_method4_ratio, 'info_trie_method7_killer' => $info_trie_method7_killer, 'info_trie_method7_ratio' => $info_trie_method7_ratio);

    return $arrayMots;
}
?>