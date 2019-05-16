<?php
function traduction($typeLangue, $user) {
    $user = strtoupper($user);

    if ($typeLangue === 'francais') {
        $titre = "Page des statistiques";
        $rang = "Rang";
        $h1 = "<h1>Bienvenue à vous &rarr; <span class='userDisplay'>{$user}</span> &larr; sur la page des statistiques du poker des vendredis entre amis.</h1>";
        $legend1 = "Voici les différentes méthodes affichages des stats du poker :";
        $method1 = "Affichage brute sans aucune modification.";
        $method2 = "Affichage de toutes les visites d'un joueur.";
        $method3 = "Le sommaire d'un joueur en particulier.";
        $method4 = "Le sommaire de tous les joueurs";
        $method5 = "Affichage d'un tournois par son numéro.";
        $method6 = "Affichage d'un tournois par la date.";
        $method7 = "Le sommaire de tous les joueurs avec leurs prix citrons et killers.";
        $h3 = "Le numéro du bouton sera la méthode sélectionnée";
        $legend2 = "Veuillez sélectionner votre méthode :";
        $label1 = "Pour les méthodes 2 et 3, veuillez sélectionner un joueur : ";
        $label2 = "Pour les méthodes 5, veuillez sélectionner un numéro tournois : ";
        $label3 = "Pour les méthodes 6, veuillez sélectionner une date d'un tournois : ";
        $option = "À sélectionner";
        $legend3 = "Voici le résultat de la méthode d'affichage choisie :";
        $joueur = "Joueur";
        $gain = "Gain";
        $victoire = "Fini 1er";
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
    } elseif ($typeLangue === 'english') {
        $titre = "Statistics page";
        $rang = "Rank";
        $h1 = "<h1>Welcome to you &rarr; <span class='userDisplay'>{$user}</span> &larr; on the statictics page about the friday nights poker between somes friends.</h1>";
        $legend1 = "Here are the differents methods of displaying poker statistics";
        $method1 = "Display all information with no modification.";
        $method2 = "Display all information about one player.";
        $method3 = "The summary about one player.";
        $method4 = "The summary about all players";
        $method5 = "Display a tournament by number.";
        $method6 = "Display a tournament by date.";
        $method7 = "The summary of all players with their prices lemons and killers.";
        $h3 = "The number on the button will match with the number of the method";
        $legend2 = "Please choose your method";
        $label1 = "About the method 2 and 3, please select one player";
        $label2 = "About the method 5, please select a tournament number:";
        $label3 = "About the method 6, please select a date from a tournament:";
        $option = "Select";
        $citron = "Lemon price";
        $killer = "Killer";
        $legend3 = "This is the result of the selected method";
        $joueur = "Player";
        $gain = "Profit";
        $victoire = "1st";
        $fini2 = "2nd";
        $noTournois = "Game Num";
        $nbTournois = "Amount Games";
        $gainPresence = "Profit/Amount Games";
        $date = "Date";
        $msgErreur_joueur = "Please select one player";
        $msgErreur_ID = "Please select a tournament number !";
        $msgErreur_Date = "Please select a date from a tournament !";
        $btnLogin = "Login page";
        $btnReturn = "Home page";
        $returnUp = "Back to the method of displaying";
    }

    $arrayMots = ['gainPresence' => $gainPresence, 'rang' => $rang, 'titre' => $titre, 'h1' => $h1, 'legend1' => $legend1, 'method1' => $method1, 'method2' => $method2, 'method3' => $method3, 'method4' => $method4, 'method5' => $method5, 'method6' => $method6, 'method7' => $method7, 'h3' => $h3, 'legend2' => $legend2, 'label1' => $label1, 'label2' => $label2, 'label3' => $label3, 'option' => $option, 'legend3' => $legend3, 'joueur' => $joueur, 'gain' => $gain, 'killer' => $killer, 'victoire' => $victoire, 'fini2' => $fini2, 'noTournois' => $noTournois, 'nbTournois' => $nbTournois, 'date' => $date, 'citron' => $citron, 'msgErreur_joueur' => $msgErreur_joueur, 'msgErreur_ID' => $msgErreur_ID, 'msgErreur_Date' => $msgErreur_Date, 'btnLogin' => $btnLogin, 'btnReturn' => $btnReturn, 'returnUp' => $returnUp];

    return $arrayMots;
}

function creationListe($nameSelected, $connMYSQL, $arrayMots) {    
    $liste_Joueur = "";
    $sql = "select joueur from joueur order by joueur";
    $result = $connMYSQL->query($sql);
    if ($_POST['method'] == 2){
        $liste_Joueur = "<option value=''>{$arrayMots['option']}</option>";
        foreach ($result as $row) {
            if ($nameSelected === $row['joueur']) {
                $liste_Joueur .= "<option value=\"{$row['joueur']}\" selected>{$row['joueur']}</option>";
            } else {
                $liste_Joueur .= "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>";
            }
        }        
        
    } elseif ($_POST['method'] == 3){
        $liste_Joueur = "<option value=''>{$arrayMots['option']}</option>";
        foreach ($result as $row) {
            if ($nameSelected === $row['joueur']) {
                $liste_Joueur .= "<option value=\"{$row['joueur']}\" selected>{$row['joueur']}</option>";
            } else {
                $liste_Joueur .= "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>";
            }
        }        
        
    } else {
        $liste_Joueur = "<option value='' selected>{$arrayMots['option']}</option>";
        foreach ($result as $row) {
            $liste_Joueur .= "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>";
        }
    }    
    return $liste_Joueur;
}

function creationListeId($IDSelected, $connMYSQL, $arrayMots) {
    $sql = "SELECT distinct id_tournoi FROM poker order by id_tournoi desc";
    $result = $connMYSQL->query($sql);

    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_POST['method'] != 5) {
        $liste_Id_tournois = "<option value='' selected>{$arrayMots['option']}</option>";
        foreach ($result as $row) {
            $liste_Id_tournois .= "<option value=\"{$row['id_tournoi']}\">{$row['id_tournoi']}</option>";
        }
    } elseif ($_POST['method'] == 5) {
        $liste_Id_tournois = "<option value=''>{$arrayMots['option']}</option>";
        foreach ($result as $row) {
            if ($IDSelected === $row['id_tournoi']) {
                $liste_Id_tournois .= "<option value=\"{$row['id_tournoi']}\" selected>{$row['id_tournoi']}</option>";
            } else {
                $liste_Id_tournois .= "<option value=\"{$row['id_tournoi']}\">{$row['id_tournoi']}</option>";
            }
        }
    }
    return $liste_Id_tournois;
}

function creationListeDate($tournoiDate, $connMYSQL, $arrayMots) {
    $sql = "SELECT distinct date FROM poker order by date desc";
    $result = $connMYSQL->query($sql);

    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_POST['method'] != 6) {
        $liste_Date_tournois = "<option value='' selected>{$arrayMots['option']}</option>";
        foreach ($result as $row) {
            $liste_Date_tournois .= "<option value=\"{$row['date']}\">{$row['date']}</option>";
        }
    } elseif ($_POST['method'] == 6) {
        $liste_Date_tournois = "<option value=''>{$arrayMots['option']}</option>";
        foreach ($result as $row) {
            if ($tournoiDate === $row['date']) {
                $liste_Date_tournois .= "<option value=\"{$row['date']}\" selected>{$row['date']}</option>";
            } else {
                $liste_Date_tournois .= "<option value=\"{$row['date']}\">{$row['date']}</option>";
            }
        }
    }
    return $liste_Date_tournois;
}

function lesGrandsGagnants_100e($nom_Champion) {
    //https://pixabay.com/fr/médaille-or-conception-2163347/
    //https://pixabay.com/fr/m%C3%A9daille-argent-conception-2163349/
    //https://pixabay.com/fr/m%C3%A9daille-bronze-conception-2163351/
    if ($nom_Champion === "Frederic V") {
        $icone = "<img src=\"./photo/medaile_or.jpg\" alt=\"or\" title=\"or\">";
    } elseif ($nom_Champion === "Frederic") {
        $icone = "<img src=\"./photo/medaile_argent.jpg\" alt=\"argent\" title=\"argent\">";
    } elseif ($nom_Champion === "Marc-Andre") {
        $icone = "<img src=\"./photo/medaile_bronze.jpg\" alt=\"bronze\" title=\"bronze\">";
    } else {
        $icone = "";
    }
    return $icone;
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
        $icone = lesGrandsGagnants_100e($row['joueur']);
        $tableau .= "<tr>
                <td>{$row['joueur']}{$icone}</td>";
        if ($nombreGain > 0) {
            $tableau .= "<td class='positif'>{$nombreGain}</td>";
        } elseif ($nombreGain < 0) {
            $tableau .= "<td class='negatif'>{$nombreGain}</td>";
        } else {
            $tableau .= "<td>{$nombreGain}</td>";
        }
        $tableau .= "<td>{$row['victoire']}</td> <td>{$row['fini_2e']}</td>
                     <td>{$row['id_tournoi']}</td> <td>{$row['date']}</td>
                </tr>";
    }
    $tableau .= "</tbody></table>";
    return $tableau;
}

function affichageUnjoueur($informationJoueur, $connMYSQL, $arrayMots) {
    if ($informationJoueur === "") {
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_joueur']}</h3>";
    } else {
        $sql = "SELECT * FROM poker where joueur = '{$informationJoueur}' order by id_tournoi desc";
        $result = $connMYSQL->query($sql);
        $tableau = "
                    <table> <thead>
                        <tr> <th colspan='6'>{$arrayMots['method2']} &rarr; {$informationJoueur} &larr;</th> </tr>
                        <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                             <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> <th>{$arrayMots['date']}</th> </tr>            
                            </thead>
                            <tbody>";
        foreach ($result as $row) {
            $nombreGain = intval($row['gain']);
            $icone = lesGrandsGagnants_100e($row['joueur']);
            $tableau .= "<tr> <td>{$informationJoueur}{$icone}</td>";
            if ($nombreGain > 0) {
                $tableau .= "<td class='positif'>{$nombreGain}</td>";
            } elseif ($nombreGain < 0) {
                $tableau .= "<td class='negatif'>{$nombreGain}</td>";
            } else {
                $tableau .= "<td>{$nombreGain}</td>";
            }
            $tableau .= "     
                        <td>{$row['victoire']}</td>
                        <td>{$row['fini_2e']}</td>
                        <td>{$row['id_tournoi']}</td>
                        <td>{$row['date']}</td>
                    </tr>";
        }
        $tableau .= "</tbody></table>";
    }
    return $tableau;
}

function sommaireUnjoueur($sommaireJoueur, $connMYSQL, $arrayMots) {
    if ($sommaireJoueur === "") {
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_joueur']}</h3>";
    } else {
        $sql = "SELECT
                    joueur,
                    SUM(gain) as gainTotaux,
                    count(case victoire when 'X' then 1 else null end) as nb_victoire,
                    count(case fini_2e when 'X' then 1 else null end) as nb_fini2e,
                    count(joueur) as nb_presence
                FROM
                    poker
                where 
                    joueur = '{$sommaireJoueur}'";
        $result = $connMYSQL->query($sql);
        $tableau = "<table> 
                        <thead> 
                            <tr> <th colspan='5'>{$arrayMots['method3']} &rarr; {$sommaireJoueur} &larr;</th> </tr>
                            <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                                 <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['nbTournois']}</th> </tr>            
                        </thead>
                        <tbody>";

        foreach ($result as $row) {
            $nombreGain = intval($row['gainTotaux']);
            $icone = lesGrandsGagnants_100e($row['joueur']);
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
    }
    return $tableau;
}

function sommaireTousJoueurs($connMYSQL, $arrayMots) {
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
                order by 
                    gainTotaux desc, nb_victoire desc, nb_fini2e desc, nb_presence
            ) res";
    $result = $connMYSQL->query($sql);
    $tableau = "<table> 
                    <thead> 
                        <tr> <th colspan='7'>{$arrayMots['method4']}</th> </tr>
                        <tr> <th class=\"nomPetit\">{$arrayMots['rang']}</th><th class=\"nomPetit\">{$arrayMots['joueur']}</th> <th class=\"nomPetit droit\">{$arrayMots['gain']}</th> <th class=\"nomPetit\">{$arrayMots['victoire']}</th> 
                                <th class=\"nomPetit\">{$arrayMots['fini2']}</th> <th class=\"nomPetit\">{$arrayMots['nbTournois']}</th> <th class=\"nomPetit droit\">{$arrayMots['gainPresence']}</th> </tr>            
                    </thead> <tbody>";

    // Ajout d'un compteur pour afficher simplement le joueur avec ses stats et savoir où se trouve
    $position = 1;
    foreach ($result as $row) {
        $nombreGain = intval($row['gainTotaux']);
        $icone = lesGrandsGagnants_100e($row['joueur']);
        $tableau .= "<tr>";
        $tableau .= "<td>{$position}</td>";
        $tableau .= "<td>{$row['joueur']}{$icone}</td>";
        if ($nombreGain > 0) {
            $tableau .= "<td class='positif droit'>{$nombreGain}</td>";
        } elseif ($nombreGain < 0) {
            $tableau .= "<td class='negatif droit'>{$nombreGain}</td>";
        } else {
            $tableau .= "<td class=\"droit\">{$nombreGain}</td>";
        }
        $tableau .= "<td>{$row['nb_victoire']}</td>
                        <td>{$row['nb_fini2e']}</td>
                        <td>{$row['nb_presence']}</td>
                        <td class=\"droit\">{$row['gainPresence']}</td>";
        $tableau .= "</tr>";
        $position++; // On augmente de 1 la position pour le prochain joueur et ses statistiques pour l'affichage
    }
    $tableau .= "</tbody></table>";
    return $tableau;
}

function affichageParNumero($numeroID, $connMYSQL, $arrayMots) {
    if ($numeroID === "") {
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_ID']}</h3>";
    } else {
        $sql = "SELECT * FROM poker where id_tournoi = '{$numeroID}' order by gain desc";
        $result = $connMYSQL->query($sql);
        $tableau = "<table> 
                        <thead> 
                            <tr> <th colspan='6'>{$arrayMots['method5']} &rarr; {$numeroID} &larr;</th> </tr>
                            <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                                 <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> 
                                 <th>{$arrayMots['date']}</th> </tr>            
                        </thead>
                        <tbody>";

        foreach ($result as $row) {
            $nombreGain = intval($row['gain']);
            $icone = lesGrandsGagnants_100e($row['joueur']);
            $tableau .= "<tr> 
                        <td>{$row['joueur']}{$icone}</td>";
            if ($nombreGain > 0) {
                $tableau .= "<td class='positif'>{$nombreGain}</td>";
            } elseif ($nombreGain < 0) {
                $tableau .= "<td class='negatif'>{$nombreGain}</td>";
            } else {
                $tableau .= "<td>{$nombreGain}</td>";
            }
            $tableau .= "<td>{$row['victoire']}</td>
                         <td>{$row['fini_2e']}</td>
                         <td>{$row['id_tournoi']}</td>
                         <td>{$row['date']}</td>
                        </tr>";
        }
        $tableau .= "</tbody></table>";
    }
    return $tableau;
}

function affichageParDate($tournoiDate, $connMYSQL, $arrayMots) {
    if ($tournoiDate === "") {
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_Date']}</h3>";
    } else {
        $sql = "SELECT * FROM poker where date = '{$tournoiDate}' order by gain desc";
        $result = $connMYSQL->query($sql);
        $tableau = "<table> 
                        <thead> 
                            <tr> <th colspan='6'>{$arrayMots['method6']} &rarr; {$tournoiDate} &larr;</th> </tr>
                            <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                                 <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> 
                                 <th>{$arrayMots['date']}</th> </tr>            
                        </thead>
                        <tbody>";

        foreach ($result as $row) {
            $nombreGain = intval($row['gain']);
            $icone = lesGrandsGagnants_100e($row['joueur']);
            $tableau .= "<tr> 
                        <td>{$row['joueur']}{$icone}</td>";
            if ($nombreGain > 0) {
                $tableau .= "<td class='positif'>{$nombreGain}</td>";
            } elseif ($nombreGain < 0) {
                $tableau .= "<td class='negatif'>{$nombreGain}</td>";
            } else {
                $tableau .= "<td>{$nombreGain}</td>";
            }
            $tableau .= "<td>{$row['victoire']}</td>
                         <td>{$row['fini_2e']}</td>
                         <td>{$row['id_tournoi']}</td>
                         <td>{$row['date']}</td>
                        </tr>";
        }
        $tableau .= "</tbody></table>";
    }
    return $tableau;
}

function affichageKillerCitron($connMYSQL, $arrayMots) {
    $sql = "SELECT
                joueur,
                SUM(killer) as prixKiller,
                SUM(prixCitron) as citronPrice,
                count(case victoire when 'X' then 1 else null end) as nb_victoire,
                count(case fini_2e when 'X' then 1 else null end) as nb_fini2e,
                count(joueur) as nb_presence
            FROM
                poker
            where 
                id_tournoi > 100
            GROUP BY joueur
            order by prixKiller desc, citronPrice, nb_victoire desc, nb_fini2e desc, nb_presence";
    $result = $connMYSQL->query($sql);
    $tableau = "<table> 
                        <thead> 
                            <tr> <th colspan='7'>{$arrayMots['method7']}</th></tr>
                            <tr> <th>{$arrayMots['rang']}</th> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['killer']}</th> <th>{$arrayMots['citron']}</th> 
                                 <th>{$arrayMots['victoire']}</th> <th>{$arrayMots['fini2']}</th> 
                                 <th>{$arrayMots['nbTournois']}</th> </tr>            
                        </thead>
                        <tbody>";

    $position = 1;
    foreach ($result as $row) {
        $icone = lesGrandsGagnants_100e($row['joueur']);
        $tableau .= "<tr>
                        <td>{$position}</td>
                        <td>{$row['joueur']}{$icone}</td>        
                        <td>{$row['prixKiller']}</td>
                        <td>{$row['citronPrice']}</td>
                        <td>{$row['nb_victoire']}</td>
                        <td>{$row['nb_fini2e']}</td>
                        <td>{$row['nb_presence']}</td>
                    </tr>";
        $position++;
    }
    $tableau .= "</tbody></table>";
    return $tableau;
}

function redirection($typeLangue) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        delete_Session();
        header("Location: /erreur/erreur.php");

    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        delete_Session();
        if (isset($_POST['return'])) {
            if ($typeLangue == 'english') {
                header("Location: /login/login.php?langue=english");
            } else {
                header("Location: /login/login.php?langue=francais");
            }
        } elseif (isset($_POST['home'])) {
            if ($typeLangue == 'english') {
                header("Location: /english/english.html");

            } else {
                header("Location: /index.html");
            }
        } else {
            header("Location: /erreur/erreur.php");
        }
    }
    exit; // pour arrêter l'éxecution du code php
}

function delete_Session(){
    session_unset(); // détruire toutes les variables SESSION
    setcookie("POKER", $_SESSION['user'], time() - 3600, "/"); // permettre de détruire bien comme il faut le cookie du user
    session_destroy();
    session_write_close(); // https://stackoverflow.com/questions/2241769/php-how-to-destroy-the-session-cookie-correctly
}

function connexionBD() { 
    
    /*
    
    // Nouvelle connexion sur hébergement du Studio OL
    $host = "localhost";
    $user = "benoitmi_benoit";
    $password = "d-&47mK!9hjGC4L-";
    $bd = "benoitmi_benoitmignault.ca.mysql";
*/
    
    $host = "localhost";
    $user = "zmignaub";
    $password = "Banane11";
    $bd = "benoitmignault_ca_mywebsite";
    
    $connMYSQL = mysqli_connect($host, $user, $password, $bd);
    $connMYSQL->query("set names 'utf8'");
    return $connMYSQL;
}

function verificationUser($connMYSQL) {
    $sql = "select user, password from login";
    $result = $connMYSQL->query($sql);

    foreach ($result as $row) {
        if ($row['user'] === $_SESSION['user']) {
            // On ajoute une vérification pour vérifier que cest le bon user versus la bonne valeur - 2018-12-28
            if ($_COOKIE['POKER'] == $row['user']){
                if (password_verify($_SESSION['password'], $row['password'])) {
                    return true; // dès qu'on trouve notre user + son bon mdp on exit de la fct
                }
            }
        }
        // la fin de la vérification pour trouver notre user dans la BD et ainsi que la vérification de son mdp  
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user = "";
    $typeLangue = "";
    $tableauResult = "";
    $verificationUser = false;
    session_start();
    if (isset($_SESSION['user']) && isset($_SESSION['password']) && isset($_SESSION['typeLangue']) && isset($_COOKIE['POKER'])) {
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);
        $typeLangue = $_SESSION['typeLangue'];
        $user = $_SESSION['user'];
    } else {
        redirection($typeLangue);
    }

    // on vérifier si notre user existe en bonne éduforme
    if (!$verificationUser) {
        redirection($typeLangue);
    } elseif ($typeLangue !== "francais" && $typeLangue !== "english") {
        redirection($typeLangue);
    } else {
        $arrayMots = traduction($typeLangue, $user);
        $liste_Joueur_method2 = creationListe("", $connMYSQL, $arrayMots);
        $liste_Joueur_method3 = creationListe("", $connMYSQL, $arrayMots);
        $liste_Id_tournois = creationListeId("", $connMYSQL, $arrayMots);
        $liste_Date_tournois = creationListeDate("", $connMYSQL, $arrayMots);
    }
    $connMYSQL->close();
} // fin du GET

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $user = "";
    $typeLangue = "";
    if (!isset($_SESSION['user']) || !isset($_SESSION['typeLangue']) || !isset($_SESSION['password'])) {
        redirection($typeLangue);
    } else {
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);
        $typeLangue = $_SESSION['typeLangue'];
        $user = $_SESSION['user'];
        if (!$verificationUser) {
            redirection($typeLangue);
        } elseif ($typeLangue !== "francais" && $typeLangue !== "english") {
            redirection($typeLangue);
        } else {
            $arrayMots = traduction($typeLangue, $user); 
            
            if (isset($_POST['informationJoueur']) && $_POST['method'] == 2) {
                $informationJoueur = $_POST['informationJoueur'];                
            } else {
                $informationJoueur = "";
            }

            if (isset($_POST['sommaireJoueur']) && $_POST['method'] == 3) {
                $sommaireJoueur = $_POST['sommaireJoueur'];                
            } else {
                $sommaireJoueur = "";
            }

            if (isset($_POST['listeId'])) {
                $numeroID = $_POST['listeId'];
            } else {
                $numeroID = "";
            }

            if (isset($_POST['listeDate'])) {
                $tournoiDate = $_POST['listeDate'];
            } else {
                $tournoiDate = "";
            }

            if (isset($_POST['method'])) {
                // C'est la méthode que j'ai trouvé trouver la valeur max comme cette valeur va en augmentant
                $sql = "select max(id_login) from login_stat_poker where user = '{$_SESSION['user']}' ";                    
                $result_SQL = $connMYSQL->query($sql);
                $row = $result_SQL->fetch_row(); // C'est mon array de résultat
                $idConnexion = (int) $row[0];	// Assignation de la valeur
                date_default_timezone_set('America/New_York'); // Je dois mettre ça si je veux avoir la bonne heure et date dans mon entrée de data
                $date_method = date("Y-m-d H:i:s");

                // Ajouter la methode choisie par le user dans la table affichage_stat_poker en lien avec mon login sur la page
                $insert = "INSERT INTO affichage_stat_poker (user,methode,id_login,id_affichage,date) VALUES ";
                $insert .= "('" . $user . "','" . $_POST['method'] . "','" . $idConnexion . "',NULL,'" . $date_method . "')";
                $connMYSQL->query($insert);

                switch ($_POST['method']) {
                    case 1 : $tableauResult = affichageBrute($connMYSQL, $arrayMots);
                        break;
                    case 2 : $tableauResult = affichageUnjoueur($informationJoueur, $connMYSQL, $arrayMots);
                        break;
                    case 3 : $tableauResult = sommaireUnjoueur($sommaireJoueur, $connMYSQL, $arrayMots);
                        break;
                    case 4 : $tableauResult = sommaireTousJoueurs($connMYSQL, $arrayMots);
                        break;
                    case 5 : $tableauResult = affichageParNumero($numeroID, $connMYSQL, $arrayMots);
                        break;
                    case 6 : $tableauResult = affichageParDate($tournoiDate, $connMYSQL, $arrayMots);
                        break;
                    case 7 : $tableauResult = affichageKillerCitron($connMYSQL, $arrayMots);
                        break;
                }
            } else {
                redirection($typeLangue);
            }            
            
            $liste_Joueur_method2 = creationListe($informationJoueur, $connMYSQL, $arrayMots);
            $liste_Joueur_method3 = creationListe($sommaireJoueur, $connMYSQL, $arrayMots);
            $liste_Id_tournois = creationListeId($numeroID, $connMYSQL, $arrayMots);
            $liste_Date_tournois = creationListeDate($tournoiDate, $connMYSQL, $arrayMots);
        }
        $connMYSQL->close();
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">	
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Statistique du poker">
        <!-- Le fichier poker.png est la propriété du site : https://pixabay.com/fr/cartes-diamant-diamants-favoris-2029819/ mais sous licence gratuite -->
        <link rel="shortcut icon" href="./photo/poker.png">
        <link rel="stylesheet" type="text/css" href="poker.css"> 
        <title><?php echo $arrayMots['titre']; ?></title>
        <style>
            body{
                margin:0;   
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
                    <legend align="center"> <?php echo $arrayMots['legend1']; ?></legend>
                    <form method='post' action='poker.php'>
                        <table>
                            <thead>                            
                                <tr>
                                    <th>Méthode d'affichage</th>
                                    <th>Sélection</th>
                                    <th>Bouton de la méthode</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="methode"><?php echo $arrayMots['method1']; ?></td>
                                    <td></td>
                                    <td><input class='bouton' type='submit' name='method' value=1></td>
                                </tr>
                                <tr>
                                    <td class="methode"><?php echo $arrayMots['method2']; ?></td>
                                    <td><select id="joueur" name="informationJoueur"><?php echo $liste_Joueur_method2; ?></select></td>
                                    <td><input class='bouton' type='submit' name='method' value=2></td>
                                </tr>
                                <tr>
                                    <td class="methode"><?php echo $arrayMots['method3']; ?></td>
                                    <td><select id="joueur" name="sommaireJoueur"><?php echo $liste_Joueur_method3; ?></select></td>
                                    <td><input class='bouton' type='submit' name='method' value=3></td>
                                </tr>
                                <tr>
                                    <td class="methode"><?php echo $arrayMots['method4']; ?></td>
                                    <td></td>
                                    <td><input class='bouton' type='submit' name='method' value=4></td>
                                </tr>
                                <tr>
                                    <td class="methode"><?php echo $arrayMots['method5']; ?></td>
                                    <td><select id="idTournois" name="listeId"><?php echo $liste_Id_tournois; ?></select></td>
                                    <td><input class='bouton' type='submit' name='method' value=5></td>
                                </tr>
                                <tr>
                                    <td class="methode"><?php echo $arrayMots['method6']; ?></td>
                                    <td><select id="tournois_date" name="listeDate"><?php echo $liste_Date_tournois; ?></select></td>
                                    <td><input class='bouton' type='submit' name='method' value=6></td>
                                </tr>
                                <tr>
                                    <td class="methode"><?php echo $arrayMots['method7']; ?></td>
                                    <td></td>
                                    <td><input class='bouton' type='submit' name='method' value=7></td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </fieldset>
            </div> 
            <div class="affichage">
                <fieldset>
                    <legend align="center"><?php echo $arrayMots['legend3']; ?></legend>
                    <?php echo $tableauResult; ?>
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
    </body>
</html>