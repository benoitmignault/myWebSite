<?php
function initialisation(){    
    $array_Champs = array("afficher" => "display", "nombre_Presences" => 1, "method" => 1, "href" => "", "user" => "", "password" => "", "goodUserConnected" => false, "typeLangue" => "", "tableauResult" => "", "verificationUser" => false, "informationJoueur" => "", "sommaireJoueur" => "", "numeroID" => 0, "tournoiDate" => "");        
    
    return $array_Champs;
}

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
    }

    $arrayMots = array("lang" => $lang, 'method4ratio' => $method4ratio, 'method7ratio' => $method7ratio, 'gainPresence' => $gainPresence,
    'rang' => $rang, 'titre' => $titre, 'h1' => $h1, 'legend1' => $legend1, 'method1' => $method1, 'method2' => $method2, 'methode' => $methode,  'selection' => $selection, 'btn_methode' => $btn_methode,
    'method3' => $method3, 'method4' => $method4, 'method5' => $method5, 'method6' => $method6, 'method7' => $method7, "msgInfo_killer_citron" => $msgInfo_killer_citron,
    'h3' => $h3, 'legend2' => $legend2, 'label1' => $label1, 'label2' => $label2, 'label3' => $label3, 'option' => $option, "les_gagnant_100E" => $les_gagnant_100E, "les_gagnant_150E" => $les_gagnant_150E,
    'legend3' => $legend3, 'joueur' => $joueur, 'gain' => $gain, 'killer' => $killer, 'victoire' => $victoire, 'fini2' => $fini2, 'information_post_tournois' => $information_post_tournois,
    'noTournois' => $noTournois, 'nbTournois' => $nbTournois, 'date' => $date, 'citron' => $citron, 'msgErreur_joueur' => $msgErreur_joueur,
    'msgErreur_ID' => $msgErreur_ID, 'msgErreur_Date' => $msgErreur_Date, 'btnLogin' => $btnLogin, 'btnReturn' => $btnReturn, 'returnUp' => $returnUp,
    'info_trie_method4_gain' => $info_trie_method4_gain, 'info_trie_method4_ratio' => $info_trie_method4_ratio, 'info_trie_method7_killer' => $info_trie_method7_killer, 'info_trie_method7_ratio' => $info_trie_method7_ratio);

    return $arrayMots;
}

function remplissage_Champs($array_Champs){
    $array_Champs['typeLangue'] = $_SESSION['typeLangue'];
    $array_Champs['user'] = $_SESSION['user'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['method'])){
            $array_Champs['method'] = intval($_GET['method']);            
        }

        if ($array_Champs['method'] == 4){
            $array_Champs['nombre_Presences'] = intval($_GET['nombre_Presences']);
        }

        if (isset($_GET['triRatio']) || isset($_GET['triOriginal']) ){
            var_dump("Get triRatio ou triOriginal");
            var_dump($_GET['visible_Info']);
            if (isset($_GET['visible_Info'])){
                $array_Champs['afficher'] = $_GET['visible_Info'];
            }
        }
    }  

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // On doit setter le mode de visibilité
        if (isset($_POST['visible_Info'])){
            $array_Champs['afficher'] = $_POST['visible_Info'];
        }

        if (isset($_POST['method'])){
            $array_Champs['method'] = intval($_POST['method']);            
        }

        if ($array_Champs['method'] == 4){
            $array_Champs['nombre_Presences'] = intval($_POST['nombre_Presences']);
        }

        if (isset($_POST['informationJoueur']) && $array_Champs['method'] == 2) {
            $array_Champs['informationJoueur'] = $_POST['informationJoueur'];
        } 

        if (isset($_POST['sommaireJoueur']) && $array_Champs['method'] == 3) {
            $array_Champs['sommaireJoueur'] = $_POST['sommaireJoueur'];
        } 

        if (isset($_POST['listeId']) && $array_Champs['method'] == 5) {
            $array_Champs['numeroID'] = intval($_POST['listeId']);
        }

        if (isset($_POST['listeDate']) && $array_Champs['method'] == 6) {
            $array_Champs['tournoiDate'] = $_POST['listeDate'];
        }         
    }

    return $array_Champs;
}

function creationListe($connMYSQL, $option, $nomJoueur) {    
    // En fonction de la méthode 2 ou 3 ou sinon le premier if va s'appliquer
    $liste_Joueur_method = array();
    $sql = "select joueur from joueur order by joueur";
    $result = $connMYSQL->query($sql); 
    // Si on arrive sur la page avec GET ou les méthodes 1, 4 ou 7
    if ($_SERVER['REQUEST_METHOD'] == 'GET' || ($_POST['method'] != "2" && $_POST['method'] != "3") ){
        // la premiere sera la valeur initial À sélectionner  
        array_push($liste_Joueur_method, "<option value='' selected>$option</option>");
        foreach ($result as $row) {
            array_push($liste_Joueur_method, "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>");
        }
        // Au moment d'arriver sur la page d'affichage ou si on a choisi une méthode autre 2 ou 3 
    } elseif ($_POST['method'] == "2" || $_POST['method'] == "3") {
        array_push($liste_Joueur_method, "<option value=''>$option</option>");
        foreach ($result as $row) {
            // Si le nom dans la BD est pareil
            if ($nomJoueur == $row['joueur']) {
                array_push($liste_Joueur_method, "<option value=\"{$row['joueur']}\" selected>{$row['joueur']}</option>");
                $joueurTrouver = true;
            }  else {
                array_push($liste_Joueur_method, "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>");
            }
        }
    }
    return $liste_Joueur_method;
}

function creationNbPresences($nb_presence){
    // En fonction de la méthode 4 le premier if va s'appliquer
    $liste_Presence_method = array();
    switch ($nb_presence){
        case 1 : 
            array_push($liste_Presence_method, "<option value='1' selected>1</option>");
            array_push($liste_Presence_method, "<option value='5'>5</option>");
            array_push($liste_Presence_method, "<option value='10'>10</option>");
            array_push($liste_Presence_method, "<option value='20'>20</option>");
            break;
        case 5 : 
            array_push($liste_Presence_method, "<option value='1'>1</option>");
            array_push($liste_Presence_method, "<option value='5' selected>5</option>");
            array_push($liste_Presence_method, "<option value='10'>10</option>");
            array_push($liste_Presence_method, "<option value='20'>20</option>");
            break;
        case 10 : 
            array_push($liste_Presence_method, "<option value='1'>1</option>");
            array_push($liste_Presence_method, "<option value='5'>5</option>");
            array_push($liste_Presence_method, "<option value='10' selected>10</option>");
            array_push($liste_Presence_method, "<option value='20'>20</option>");
            break;
        case 20 : 
            array_push($liste_Presence_method, "<option value='1'>1</option>");
            array_push($liste_Presence_method, "<option value='5'>5</option>");
            array_push($liste_Presence_method, "<option value='10'>10</option>");
            array_push($liste_Presence_method, "<option value='20' selected>20</option>");
            break;
    }
    return $liste_Presence_method;
}

function creationListeId($connMYSQL, $option, $IDSelected) {
    $liste_Id_tournois = array();
    $sql = "SELECT distinct id_tournoi FROM poker order by id_tournoi desc";
    $result = $connMYSQL->query($sql);
    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_POST['method'] != "5") {
        array_push($liste_Id_tournois, "<option value='' selected>$option</option>");
        foreach ($result as $row) {
            array_push($liste_Id_tournois, "<option value=\"{$row['id_tournoi']}\">{$row['id_tournoi']}</option>");
        }

    } elseif ($_POST['method'] == "5") {
        array_push($liste_Id_tournois, "<option value=''>$option</option>");
        foreach ($result as $row) {
            if ($IDSelected == $row['id_tournoi']) {
                array_push($liste_Id_tournois, "<option value=\"{$row['id_tournoi']}\" selected>{$row['id_tournoi']}</option>");
                $IdTrouver = true;
            } else {
                array_push($liste_Id_tournois, "<option value=\"{$row['id_tournoi']}\">{$row['id_tournoi']}</option>");
            }                   
        }
    }
    return $liste_Id_tournois;
}

function creationListeDate($connMYSQL, $option, $tournoiDate) {
    $liste_Date_tournois = array();
    $sql = "SELECT distinct date FROM poker order by date desc";
    $result = $connMYSQL->query($sql);

    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_POST['method'] != 6) {
        array_push($liste_Date_tournois, "<option value='' selected>$option</option>");
        foreach ($result as $row) {
            array_push($liste_Date_tournois, "<option value=\"{$row['date']}\">{$row['date']}</option>");
        }
    } elseif ($_POST['method'] == 6) {
        array_push($liste_Date_tournois, "<option value=''>$option</option>");
        foreach ($result as $row) {
            if ($tournoiDate === $row['date']) {
                array_push($liste_Date_tournois, "<option value=\"{$row['date']}\" selected>{$row['date']}</option>");
            } else {
                array_push($liste_Date_tournois, "<option value=\"{$row['date']}\">{$row['date']}</option>");
            }
        }
    }
    return $liste_Date_tournois;
}

// En raison du 150e tournois, nous avons changer le nom de la fonction et ajouter les noms des gagnants qui doivent avoir leur médaille
function grands_gagnants_super_tournois($nom_Champion) {
    // https://pixabay.com/fr/médaille-or-conception-2163347/
    // https://pixabay.com/fr/m%C3%A9daille-argent-conception-2163349/
    // https://pixabay.com/fr/m%C3%A9daille-bronze-conception-2163351/
    if ($nom_Champion === "Frederic V" OR $nom_Champion === "Richard") {
        $icone = "<img src=\"./photo/medaile_or.jpg\" alt=\"or\" title=\"or\">";
    } elseif ($nom_Champion === "Frederic" OR $nom_Champion === "Maxime") {
        $icone = "<img src=\"./photo/medaile_argent.jpg\" alt=\"argent\" title=\"argent\">";
    } elseif ($nom_Champion === "Marc-Andre" OR $nom_Champion === "Jean-Philippe") {
        $icone = "<img src=\"./photo/medaile_bronze.jpg\" alt=\"bronze\" title=\"bronze\">";
    } else {
        $icone = "";
    }

    return $icone;
}

function selectionBonneMethode($connMYSQL, $arrayMots, $array_Champs){
    $tableauResult = "";   
    switch ($array_Champs['method']) {
        case 1 : $tableauResult = affichageBrute($connMYSQL, $arrayMots); break;
        case 2 : $tableauResult = affichageUnjoueur($array_Champs['informationJoueur'], $connMYSQL, $arrayMots); break;
        case 3 : $tableauResult = sommaireUnjoueur($array_Champs['sommaireJoueur'], $connMYSQL, $arrayMots); break;
        case 4 : $tableauResult = sommaireTousJoueurs($array_Champs['href'], $connMYSQL, $arrayMots, $array_Champs['nombre_Presences']); break;
        case 5 : $tableauResult = affichageParNumero($array_Champs['numeroID'], $connMYSQL, $arrayMots); break;
        case 6 : $tableauResult = affichageParDate($array_Champs['tournoiDate'], $connMYSQL, $arrayMots); break;
        case 7 : $tableauResult = affichageKillerCitron($array_Champs['href'], $connMYSQL, $arrayMots); break;
    }
    return $tableauResult;
}

function affichageBrute($connMYSQL, $arrayMots) {
    $sql = "select joueur, gain, victoire, fini_2e, id_tournoi, date from poker order by id_tournoi desc, gain desc";
    $result = $connMYSQL->query($sql);
    $tableau = "<table> 
            <thead> 
                <tr> <th colspan='6'>{$arrayMots['method1']}</th> </tr>
                <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                     <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> <th>{$arrayMots['date']}</th> 
                </tr> 
            </thead>
            <tbody>";
    foreach ($result as $row) {
        $nombreGain = intval($row['gain']);
        $icone = grands_gagnants_super_tournois($row['joueur']);
        $tableau .= "<tr>
                <td><div class=\"prenom\">{$row['joueur']}{$icone}</div></td>";
        if ($nombreGain > 0) {
            $tableau .= "<td class='positif'><div class=\"valeur\">{$nombreGain}</div></td>";
        } elseif ($nombreGain < 0) {
            $tableau .= "<td class='negatif'><div class=\"valeur\">{$nombreGain}</div></td>";
        } else {
            $tableau .= "<td><div class=\"valeur\">{$nombreGain}</div></td>";
        }
        $tableau .= "<td><div class=\"valeur\">{$row['victoire']}</div></td>
                     <td><div class=\"valeur\">{$row['fini_2e']}</div></td>
                     <td><div class=\"valeur\">{$row['id_tournoi']}</div></td> 
                     <td><div class=\"valeur\">{$row['date']}</div></td>
                </tr>";
    }
    $tableau .= "</tbody></table>";
    return $tableau;
}

function affichageUnjoueur($informationJoueur, $connMYSQL, $arrayMots) {
    if ($informationJoueur === "") {
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_joueur']}</h3>";
    } else {
        $tableau = "<table> <thead>
                        <tr> <th colspan='6'>{$arrayMots['method2']} &rarr; {$informationJoueur} &larr;</th> </tr>
                        <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                             <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> <th>{$arrayMots['date']}</th> </tr>            
                            </thead>
                            <tbody>";
        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("SELECT * FROM poker where joueur =? order by id_tournoi desc");

        /* Lecture des marqueurs */
        $stmt->bind_param("s", $informationJoueur);

        /* Exécution de la requête */
        $stmt->execute();

        /* Association des variables de résultat */
        $result = $stmt->get_result();

        /* Lecture des valeurs */
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $nombreGain = intval($row['gain']);
            $icone = grands_gagnants_super_tournois($row['joueur']);
            $tableau .= "<tr> <td><div class=\"prenom\">{$informationJoueur}{$icone}</div></td>";
            if ($nombreGain > 0) {
                $tableau .= "<td class='positif'><div class=\"valeur\">{$nombreGain}</div></td>";
            } elseif ($nombreGain < 0) {
                $tableau .= "<td class='negatif'><div class=\"valeur\">{$nombreGain}</div></td>";
            } else {
                $tableau .= "<td><div class=\"valeur\">{$nombreGain}</div></td>";
            }
            $tableau .= "     
                        <td><div class=\"valeur\">{$row['victoire']}</div></td>
                        <td><div class=\"valeur\">{$row['fini_2e']}</div></td>
                        <td><div class=\"valeur\">{$row['id_tournoi']}</div></td>
                        <td><div class=\"valeur\">{$row['date']}</div></td>
                    </tr>";
        }
        $tableau .= "</tbody></table>";

        /* Fermeture du traitement */
        $stmt->close();
    }
    return $tableau;
}

function sommaireUnjoueur($sommaireJoueur, $connMYSQL, $arrayMots) {
    if ($sommaireJoueur === "") {
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_joueur']}</h3>";
    } else {
        $tableau = "<table> 
                        <thead> 
                            <tr> <th colspan='5'>{$arrayMots['method3']} &rarr; {$sommaireJoueur} &larr;</th> </tr>
                            <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                                 <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['nbTournois']}</th> </tr>            
                        </thead>
                        <tbody>";

        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("SELECT
                    joueur,
                    sum(gain) as gainTotaux,
                    count(case victoire when 'X' then 1 else null end) as nb_victoire,
                    count(case fini_2e when 'X' then 1 else null end) as nb_fini2e,
                    count(joueur) as nb_presence
                FROM
                    poker
                where 
                    joueur =?");

        /* Lecture des marqueurs */
        $stmt->bind_param("s", $sommaireJoueur);

        /* Exécution de la requête */
        $stmt->execute();

        /* Association des variables de résultat */
        $result = $stmt->get_result();

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $nombreGain = intval($row['gainTotaux']);
            $icone = grands_gagnants_super_tournois($row['joueur']);
            $tableau .= "<tr> 
                        <td>{$sommaireJoueur}{$icone}</td>";
            if ($nombreGain > 0) {
                $tableau .= "<td class='positif'>{$nombreGain}</td>";
            } elseif ($nombreGain < 0) {
                $tableau .= "<td class='negatif'>{$nombreGain}</td>";
            } else {
                $tableau .= "<td>{$nombreGain}</td>";
            }
            $tableau .= "<td>{$row['nb_victoire']}</td>
                         <td>{$row['nb_fini2e']}</td>
                         <td>{$row['nb_presence']}</td>
                        </tr>";
        }        

        $tableau .= "</tbody></table>";
        /* Fermeture du traitement */
        $stmt->close();
    }
    return $tableau;
}

function sommaireTousJoueurs($href, $connMYSQL, $arrayMots, $nombre_Presences) {
    $tableau = ""; // Initialiation du tableau
    if (!isset($_GET['triRatio']) && (isset($_GET['triOriginal']) || $_SERVER['REQUEST_METHOD'] == 'POST') ){
        $tableau = "<table><thead> 
                        <tr> <th colspan='7'>{$arrayMots['method4']}</th></tr>
                        <tr>
                            <th class=\"nomPetit\">{$arrayMots['rang']}</th>
                            <th class=\"nomPetit\">{$arrayMots['joueur']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['gain']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['victoire']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['fini2']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['nbTournois']}</th> 
                            <th class=\"nomPetit\"><a id=\"link\" href=\"{$href}\">{$arrayMots['gainPresence']}</a></th> 
                        </tr>            
                    </thead> <tbody>";
    } elseif (isset($_GET['triRatio']) && !isset($_GET['triOriginal']) ){
        $tableau = "<table><thead> 
                        <tr> <th colspan='7'>{$arrayMots['method4']}</th> </tr>
                        <tr> 
                            <th class=\"nomPetit\">{$arrayMots['rang']}</th>
                            <th class=\"nomPetit\">{$arrayMots['joueur']}</th> 
                            <th class=\"nomPetit\"><a id=\"link\" href=\"{$href}\">{$arrayMots['gain']}</a></th> 
                            <th class=\"nomPetit\">{$arrayMots['victoire']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['fini2']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['nbTournois']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['gainPresence']}</th> 
                        </tr>            
                    </thead> <tbody>";
    }
    $requeteSql = "";
    $orderBy = "";

    $sql = "select res.* , round(res.gainTotaux / res.nb_presence,2) as gainPresence
            from
            (
                SELECT
                    joueur,
                    SUM(gain) as gainTotaux,
                    count(case victoire when 'X' then 1 else null end) as nb_victoire,
                    count(case fini_2e when 'X' then 1 else null end) as nb_fini2e,
                    count(joueur) as nb_presence
                FROM
                    poker
                GROUP BY 
                    joueur                
            ) res where res.nb_presence >=? ";

    // Ce qui va déterminer l'order by
    if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['triOriginal']) ){
        $orderBy = " order by res.gainTotaux desc, res.nb_victoire desc, res.nb_fini2e desc, res.nb_presence ";        
    } elseif (isset($_GET['triRatio'])) {
        $orderBy = " order by gainPresence desc, res.nb_victoire desc, res.nb_fini2e desc, res.nb_presence ";
    }

    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare($sql . $orderBy);

    /* Lecture des marqueurs */
    $stmt->bind_param("i", $nombre_Presences);

    /* Exécution de la requête */
    $stmt->execute();

    /* Association des variables de résultat */
    $result = $stmt->get_result();

    $position = 1;
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $nombreGain = intval($row['gainTotaux']);
        $gainRatio = floatval($row['gainPresence']);
        // Ajout de deux décimales apres la virgule pour tout les résultats
        $gainRatio = number_format($gainRatio, 2);
        $icone = grands_gagnants_super_tournois($row['joueur']);
        $tableau .= "<tr>";
        $tableau .= "<td><div class=\"valeur\">{$position}</div></td>";
        $tableau .= "<td><div class=\"prenom\">{$row['joueur']}{$icone}</div></td>";
        if ($nombreGain > 0) {
            $tableau .= "<td class='positif'><div class=\"valeur\">{$nombreGain}</div></td>";
        } elseif ($nombreGain < 0) {
            $tableau .= "<td class='negatif'><div class=\"valeur\">{$nombreGain}</div></td>";
        } else {
            $tableau .= "<td class=\"\"><div class=\"valeur\">{$nombreGain}</div></td>";
        }
        $tableau .= "<td><div class=\"valeur\">{$row['nb_victoire']}</div></td>
        <td><div class=\"valeur\">{$row['nb_fini2e']}</div></td>
        <td><div class=\"valeur\">{$row['nb_presence']}</div></td>";

        if ($gainRatio > 0) {
            $tableau .= "<td class='positif'><div class=\"valeur\">{$gainRatio}</div></td>";
        } elseif ($gainRatio < 0) {
            $tableau .= "<td class='negatif'><div class=\"valeur\">{$gainRatio}</div></td>";
        } else {
            $tableau .= "<td><div class=\"valeur\">{$gainRatio}</div></td>";
        }  
        $tableau .= "</tr>";
        $position++; // On augmente de 1 la position pour le prochain joueur et ses statistiques pour l'affichage
    }

    $tableau .= "</tbody></table>";
    /* Fermeture du traitement */
    $stmt->close();

    return $tableau;
}

function affichageParNumero($numeroID, $connMYSQL, $arrayMots) {
    if ($numeroID == 0) {
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_ID']}</h3>";
    } else {
        $tableau = "<table> 
        <thead> 
        <tr> <th colspan='6'>{$arrayMots['method5']} &rarr; {$numeroID} &larr;</th> </tr>
        <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
        <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> 
        <th>{$arrayMots['date']}</th> </tr>            
        </thead>
        <tbody>";
        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("SELECT * FROM poker where id_tournoi =? order by gain desc");

        /* Lecture des marqueurs */
        $stmt->bind_param("i", $numeroID);

        /* Exécution de la requête */
        $stmt->execute();

        /* Association des variables de résultat */
        $result = $stmt->get_result();

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $nombreGain = intval($row['gain']);
            $icone = grands_gagnants_super_tournois($row['joueur']);
            $tableau .= "<tr> 
        <td><div class=\"prenom\">{$row['joueur']}{$icone}</div></td>";
            if ($nombreGain > 0) {
                $tableau .= "<td class='positif'><div class=\"valeur\">{$nombreGain}</div></td>";
            } elseif ($nombreGain < 0) {
                $tableau .= "<td class='negatif'><div class=\"valeur\">{$nombreGain}</div></td>";
            } else {
                $tableau .= "<td><div class=\"valeur\">{$nombreGain}</div></td>";
            }
            $tableau .= "<td><div class=\"valeur\">{$row['victoire']}</div></td>
        <td><div class=\"valeur\">{$row['fini_2e']}</div></td>
        <td><div class=\"valeur\">{$row['id_tournoi']}</div></td>
        <td><div class=\"valeur\">{$row['date']}</div></td>
        </tr>";
        }
        $tableau .= "</tbody></table>";
        /* Fermeture du traitement */
        $stmt->close();
    }
    return $tableau;
}

function affichageParDate($tournoiDate, $connMYSQL, $arrayMots) {
    if ($tournoiDate === "") {
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_Date']}</h3>";
    } else {
        $tableau = "<table> 
        <thead> 
        <tr> <th colspan='6'>{$arrayMots['method6']} &rarr; {$tournoiDate} &larr;</th> </tr>
        <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
        <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> 
        <th>{$arrayMots['date']}</th> </tr>            
        </thead>
        <tbody>";

        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("SELECT * FROM poker where date =? order by gain desc");

        /* Lecture des marqueurs */
        $stmt->bind_param("s", $tournoiDate);

        /* Exécution de la requête */
        $stmt->execute();

        /* Association des variables de résultat */
        $result = $stmt->get_result();

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $nombreGain = intval($row['gain']);
            $icone = grands_gagnants_super_tournois($row['joueur']);
            $tableau .= "<tr> 
        <td><div class=\"prenom\">{$row['joueur']}{$icone}</div></td>";
            if ($nombreGain > 0) {
                $tableau .= "<td class='positif'><div class=\"valeur\">{$nombreGain}</div></td>";
            } elseif ($nombreGain < 0) {
                $tableau .= "<td class='negatif'><div class=\"valeur\">{$nombreGain}</div></td>";
            } else {
                $tableau .= "<td><div class=\"valeur\">{$nombreGain}</div></td>";
            }
            $tableau .= "<td><div class=\"valeur\">{$row['victoire']}</div></td>
        <td><div class=\"valeur\">{$row['fini_2e']}</div></td>
        <td><div class=\"valeur\">{$row['id_tournoi']}</div></td>
        <td><div class=\"valeur\">{$row['date']}</div></td>
        </tr>";
        }
        $tableau .= "</tbody></table>";
        /* Fermeture du traitement */
        $stmt->close();
    }
    return $tableau;
}

function affichageKillerCitron($href, $connMYSQL, $arrayMots) {
    $sql = "
    select 
        res.*, 
        round(res.prixKiller / res.nb_presence,2) as killerPresence 
        from 
        ( 
            SELECT joueur, 
                round(SUM(killer),2) as prixKiller, 
                round(SUM(prixCitron),2) as citronPrice, 
                count(case victoire when 'X' then 1 else null end) as nb_victoire, 
                count(case fini_2e when 'X' then 1 else null end) as nb_fini2e, 
                count(joueur) as nb_presence 
            FROM poker 
            where id_tournoi > 100 
            GROUP BY joueur 
        ) res";
    $orderBy = "";
    // Ce qui va déterminer l'order by
    if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['triOriginal']) ){
        $orderBy = " order by res.prixKiller desc, res.citronPrice, res.nb_victoire desc, res.nb_fini2e desc, res.nb_presence ";        
    } elseif (isset($_GET['triRatio'])) {
        $orderBy = " order by killerPresence desc, res.nb_victoire desc, res.nb_fini2e desc, res.nb_presence ";
    }
    $requeteSql = $sql . $orderBy;
    // Le order by se sera sur le résultat de la table et non à l'intérieur de la table en création sur les conseils de Zouhair mon collègue 
    $result = $connMYSQL->query($requeteSql);

    if (!isset($_GET['triRatio']) && (isset($_GET['triOriginal']) || $_SERVER['REQUEST_METHOD'] == 'POST') ){
        $tableau = "<table><thead> 
                        <tr><th colspan='6'>{$arrayMots['method7']}</th></tr>
                        <tr> 
                            <th class=\"nomPetit\">{$arrayMots['rang']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['joueur']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['killer']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['citron']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['nbTournois']}</th> 
                            <th class=\"nomPetit\"><a id=\"link\" href=\"{$href}\">{$arrayMots['gainPresence']}</a></th> 
                        </tr>            
                    </thead><tbody>";        
    } elseif (isset($_GET['triRatio']) && !isset($_GET['triOriginal']) ){
        $tableau = "<table><thead> 
                        <tr><th colspan='6'>{$arrayMots['method7']}</th></tr>
                        <tr> 
                            <th class=\"nomPetit\">{$arrayMots['rang']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['joueur']}</th> 
                            <th class=\"nomPetit\"><a id=\"link\" href=\"{$href}\">{$arrayMots['killer']}</a></th> 
                            <th class=\"nomPetit\">{$arrayMots['citron']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['nbTournois']}</th> 
                            <th class=\"nomPetit\">{$arrayMots['gainPresence']}</th> 
                        </tr>            
                    </thead><tbody>";  
    }
    $position = 1;
    foreach ($result as $row) {
        $icone = grands_gagnants_super_tournois($row['joueur']);
        $tableau .= "<tr>
        <td><div class=\"valeur\">{$position}</div></td>
        <td><div class=\"prenom\">{$row['joueur']}{$icone}</div></td>        
        <td><div class=\"valeur\">{$row['prixKiller']}</div></td>
        <td><div class=\"valeur\">{$row['citronPrice']}</div></td>
        <td><div class=\"valeur\">{$row['nb_presence']}</div></td>
        <td><div class=\"valeur\">{$row['killerPresence']}</div></td>
        </tr>";
        $position++;
    }
    $tableau .= "</tbody></table>";
    return $tableau;
}

function redirection($typeLangue) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        delete_Session();
        header("Location: https://benoitmignault.ca/erreur/erreur.php");

    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        delete_Session();
        if (isset($_POST['return'])) {
            if ($typeLangue == 'english') {
                header("Location: https://benoitmignault.ca/login/login.php?langue=english");
            } else {
                header("Location: https://benoitmignault.ca/login/login.php?langue=francais");
            }
        } elseif (isset($_POST['home'])) {
            if ($typeLangue == 'english') {
                header("Location: https://benoitmignault.ca/english/english.html");

            } else {
                header("Location: https://benoitmignault.ca/index.html");
            }
        } else {
            header("Location: https://benoitmignault.ca/erreur/erreur.php");
        }
    }
    exit; // pour arrêter l'éxecution du code php
}

function delete_Session(){
    session_unset(); // détruire toutes les variables SESSION
    session_destroy();
    session_write_close(); // https://stackoverflow.com/questions/2241769/php-how-to-destroy-the-session-cookie-correctly
}

function connexionBD() { 
    // Nouvelle connexion sur hébergement du Studio OL    
    $host = "localhost";
    $user = "benoitmi_benoit";
    $password = "d-&47mK!9hjGC4L-";
    $bd = "benoitmi_benoitmignault.ca.mysql";

    $connMYSQL = mysqli_connect($host, $user, $password, $bd);
    $connMYSQL->query("set names 'utf8'");
    return $connMYSQL;
}

function verificationUser($connMYSQL) {
    // Optimisation de la vérification si le user existe dans la BD
    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare("select user, password from login where user=? ");

    /* Lecture des marqueurs */
    $stmt->bind_param("s", $_SESSION['user']);

    /* Exécution de la requête */
    $stmt->execute();

    /* Association des variables de résultat */
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows == 1){
        $row = $result->fetch_array(MYSQLI_ASSOC);
        // On ajoute une vérification pour vérifier que cest le bon user versus la bonne valeur - 2018-12-28
        if ($_COOKIE['POKER'] == $row['user']){
            if (password_verify($_SESSION['password'], $row['password'])) {
                return true; // dès qu'on trouve notre user + son bon mdp on exit de la fct
            }
        } 
    } 
    return false;
}

function addStatAffichageUser($connMYSQL, $user){
    // C'est la méthode que j'ai trouvé trouver la valeur max comme cette valeur va en augmentant
    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare("select max(id_login) as maximum from login_stat_poker where user =? ");

    /* Lecture des marqueurs */
    $stmt->bind_param("s", $_SESSION['user']);

    /* Exécution de la requête */
    $stmt->execute();

    /* Association des variables de résultat */
    $result = $stmt->get_result();

    $row = $result->fetch_array(MYSQLI_ASSOC);    
    $id_login = $row['maximum'];	// Assignation de la valeur
    // Close statement
    $stmt->close();

    date_default_timezone_set('America/New_York'); // Je dois mettre ça si je veux avoir la bonne heure et date dans mon entrée de data
    $date_method = date("Y-m-d H:i:s");
    // Ajouter la methode choisie par le user dans la table affichage_stat_poker en lien avec la Xième connexion sur la page
    // Prepare an insert statement
    $sql = "INSERT INTO affichage_stat_poker (user,methode,id_login,date) VALUES (?,?,?,?)";
    $stmt = $connMYSQL->prepare($sql);

    // Bind variables to the prepared statement as parameters
    $stmt->bind_param('siis', $user, $_POST['method'], $id_login, $date_method);
    $stmt->execute();                    

    // Close statement
    $stmt->close();
}

function lienVersTriage($array_Champs){
    $href = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['triOriginal']) ){
        $href = "poker.php?triRatio=desc&method={$array_Champs['method']}&nombre_Presences={$array_Champs['nombre_Presences']}";
    } elseif (isset($_GET['triRatio'])) {
        $href = "poker.php?triOriginal=desc&method={$array_Champs['method']}&nombre_Presences={$array_Champs['nombre_Presences']}";
    }

    return $href;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();
    $array_Champs = initialisation(); 

    if (isset($_SESSION['user']) && isset($_SESSION['password']) && isset($_SESSION['typeLangue']) && isset($_COOKIE['POKER'])) {
        $connMYSQL = connexionBD();
        $array_Champs['verificationUser'] = verificationUser($connMYSQL);
    } else {
        redirection($array_Champs['typeLangue']);
    }
    // on vérifier si notre user existe en bonne éduforme
    if (!$array_Champs['verificationUser'] ) {
        redirection($array_Champs['typeLangue']);
    } else {
        $array_Champs = remplissage_Champs($array_Champs);

        

        if ($array_Champs['typeLangue'] !== "francais" && $array_Champs['typeLangue'] !== "english") {
            redirection("francais");
        } else {
            $arrayMots = traduction($array_Champs['typeLangue'], $array_Champs['user']);             

            // Insérer ici les triages en conséquence
            if (isset($_GET['triRatio']) || isset($_GET['triOriginal']) ){
                $array_Champs['href'] = lienVersTriage($array_Champs);
                $array_Champs['tableauResult'] = selectionBonneMethode($connMYSQL, $arrayMots, $array_Champs);   
                //var_dump($array_Champs['afficher']); exit;
            }

            $liste_Joueur_method2 = creationListe($connMYSQL, $arrayMots['option'], $array_Champs['informationJoueur']);  
            $liste_Joueur_method3 = creationListe($connMYSQL, $arrayMots['option'], $array_Champs['sommaireJoueur']);
            $liste_Joueur_method4 = creationNbPresences($array_Champs['nombre_Presences']);
            $liste_Joueur_method5 = creationListeId($connMYSQL, $arrayMots['option'], $array_Champs['numeroID']);
            $liste_Joueur_method6 = creationListeDate($connMYSQL, $arrayMots['option'], $array_Champs['tournoiDate']);
        }
    }  

    $connMYSQL->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    session_start();
    $array_Champs = initialisation();

    if (!isset($_SESSION['user']) || !isset($_SESSION['typeLangue']) || !isset($_SESSION['password']) && !isset($_COOKIE['POKER'])) {
        redirection("francais");
    } else {
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);       
        $array_Champs = remplissage_Champs($array_Champs);

        if (!$verificationUser) {
            redirection($array_Champs['typeLangue']);
        } elseif ($array_Champs['typeLangue'] !== "francais" && $array_Champs['typeLangue'] !== "english") {
            redirection("francais");
        } else {

            $arrayMots = traduction($array_Champs['typeLangue'], $array_Champs['user']); 
            if (isset($_POST['method'])) {
                addStatAffichageUser($connMYSQL, $array_Champs['user'] );
                // Création du lien pour trier via la colonne du Ratio

                $array_Champs['href'] = lienVersTriage($array_Champs);
                //var_dump($array_Champs['href']);exit;

                // Faire afficher le tableau en fonction de la méthode choisie
                $array_Champs['tableauResult'] = selectionBonneMethode($connMYSQL, $arrayMots, $array_Champs);                
            } else {
                redirection($array_Champs['typeLangue']);
            }
            // Faire afficher l'information si elle est présise..
            $liste_Joueur_method2 = creationListe($connMYSQL, $arrayMots['option'], $array_Champs['informationJoueur']);  
            $liste_Joueur_method3 = creationListe($connMYSQL, $arrayMots['option'], $array_Champs['sommaireJoueur']);
            $liste_Joueur_method4 = creationNbPresences($array_Champs['nombre_Presences']);
            $liste_Joueur_method5 = creationListeId($connMYSQL, $arrayMots['option'], $array_Champs['numeroID']);
            $liste_Joueur_method6 = creationListeDate($connMYSQL, $arrayMots['option'], $array_Champs['tournoiDate']);

        }
        $connMYSQL->close();
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $arrayMots['lang']; ?>">

<head>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <meta name="http-equiv" content="Content-type: text/html; charset=utf-8"/>
    <meta name="Statistique" content="Information sur les statistiques du poker entre amis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Statistique du poker">
    <!-- Le fichier poker.png est la propriété du site : https://pixabay.com/fr/cartes-diamant-diamants-favoris-2029819/ mais sous licence gratuite -->
    <link rel="shortcut icon" href="./photo/poker.png">
    <link rel="stylesheet" type="text/css" href="poker.css">
    <title><?php echo $arrayMots['titre']; ?></title>
    <style>
        body {
            margin: 0;
            /* Fichier photoPoker.jpg est une propriété du site https://www.flickr.com/photos/nostri-imago/7497137910 sous licence libre */
            background-image: url("./photo/photoPoker.jpg");
            background-position: center;
            background-attachment: fixed;
            background-size: 100%;
        }

    </style>
</head>

<body>
    <p id="hautPage"></p>
    <div class="mainPage">
        <div class="header"> <?php echo $arrayMots['h1']; ?> </div>
        <div class="info_method">
            <fieldset>
                <legend class="legendCenter"> <?php echo $arrayMots['legend1']; ?></legend>
                <form method='post' action='poker.php#endroitResultat'>
                    <input id="info_Instruction" type="hidden" name="visible_Info" value="<?php echo $array_Champs['afficher']; ?>">
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo $arrayMots['methode']; ?></th>
                                <th><?php echo $arrayMots['selection']; ?></th>
                                <th><?php echo $arrayMots['btn_methode']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="methode"><?php echo $arrayMots['method1']; ?></td>
                                <td></td>
                                <td><input class='bouton' type='submit' name='method' value="1"></td>
                            </tr>
                            <tr>
                                <td class="methode"><?php echo $arrayMots['method2']; ?></td>
                                <td><select class="joueur" name="informationJoueur"><?php foreach ($liste_Joueur_method2 as $value) { echo $value; } ?></select></td>
                                <td><input class='bouton' type='submit' name='method' value="2"></td>
                            </tr>
                            <tr>
                                <td class="methode"><?php echo $arrayMots['method3']; ?></td>
                                <td><select class="joueur" name="sommaireJoueur"><?php foreach ($liste_Joueur_method3 as $value) { echo $value; } ?></select></td>
                                <td><input class='bouton' type='submit' name='method' value="3"></td>
                            </tr>
                            <tr>
                                <td class="methode"><?php echo $arrayMots['method4']; if ($array_Champs['typeLangue'] == "francais") { echo " (Nb Présence et +)"; } elseif ($array_Champs['typeLangue'] == "english") { echo " (Number attendance and +)"; } ?></td>
                                <td><select id="nb_Presence" name="nombre_Presences"><?php foreach ($liste_Joueur_method4 as $value) { echo $value; } ?></select></td>
                                <td><input class='bouton' type='submit' name='method' value="4"></td>
                            </tr>
                            <tr>
                                <td class="methode"><?php echo $arrayMots['method5']; ?></td>
                                <td><select id="idTournois" name="listeId"><?php foreach ($liste_Joueur_method5 as $value) { echo $value; } ?></select></td>
                                <td><input class='bouton' type='submit' name='method' value="5"></td>
                            </tr>
                            <tr>
                                <td class="methode"><?php echo $arrayMots['method6']; ?></td>
                                <td><select id="tournois_date" name="listeDate"><?php foreach ($liste_Joueur_method6 as $value) { echo $value; } ?></select></td>
                                <td><input class='bouton' type='submit' name='method' value="6"></td>
                            </tr>
                            <tr>
                                <td class="methode"><?php echo $arrayMots['method7']; ?></td>
                                <td></td>
                                <td><input class='bouton' type='submit' name='method' value="7"></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </fieldset>
        </div>
        <div class="affichage">
            <p id="endroitResultat"></p>
            <fieldset>
                <legend class="legendCenter"><?php echo $arrayMots['legend3']; ?></legend>
                <?php if ($array_Champs['method'] == 4 || $array_Champs['method'] == 7 ) { ?>
                <div class="section_info">    
                    <div class="infoGauche">
                        <a class="faireAfficher" href="#">Information utile</a>                
                        <ul class="lesInstruction">
                            <?php if ($array_Champs['method'] == 4) { ?>
                                <li><?php echo $arrayMots['info_trie_method4_gain']; ?></li>
                                <li><?php echo $arrayMots['info_trie_method4_ratio']; ?></li>
                            
                            <?php } elseif ($array_Champs['method'] == 7) { ?>
                                <li><?php echo $arrayMots['info_trie_method7_killer']; ?></li>
                                <li><?php echo $arrayMots['info_trie_method7_ratio']; ?></li>
                            <?php } ?>                            
                            <li><?php echo $arrayMots['information_post_tournois']; ?></li>
                            <ul class="lesInstruction">
                                <li><span class="charGros"><?php echo $arrayMots['les_gagnant_100E']; ?></span></li>
                                <ol class="lesInstruction">
                                    <li>Frederic V</li>
                                    <li>Frederic</li>
                                    <li>Marc-Andre</li>
                                </ol>
                                <li><span class="charGros"><?php echo $arrayMots['les_gagnant_150E']; ?></span></li>
                                <ol class="lesInstruction">
                                    <li>Richard</li>
                                    <li>Maxime</li>
                                    <li>Jean-Philippe</li>
                                </ol>
                            </ul>
                            <li><span class="charGros"><?php echo $arrayMots['msgInfo_killer_citron']; ?></li>   
                            <ol class="lesInstruction">
                                <li><?php echo $arrayMots['killer']; ?></li>
                                <li><?php echo $arrayMots['citron']; ?></li>
                            </ol>           
                        </ul>
                    </div>
                </div>
                <?php } ?>
                <?php if ( isset($_GET['triOriginal']) || isset($_GET['triRatio']) || $_SERVER['REQUEST_METHOD'] === 'POST') { echo $array_Champs['tableauResult']; } ?>
            </fieldset>
        </div>
        <div class="return">
            <fieldset>
                <a href="#hautPage"><?php echo $arrayMots['returnUp']; ?></a>
            </fieldset>
        </div>
        <div class='btnRetour'>
            <form method="post" action="poker.php">
                <input class='bouton' type="submit" name="return" value="<?php echo $arrayMots['btnLogin']; ?>">
                <input class='bouton' type="submit" name="home" value="<?php echo $arrayMots['btnReturn']; ?>">
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="poker.js"></script>
</body>

</html>
