<?php 
function traduction($typeLangue, $user){    
    $user = strtoupper($user); 

    if ($typeLangue === 'francais') { 
        $titre = "Page des statistiques";
        $h1 = "<h1>Bienvenue à vous &rarr; <span class='userDisplay'>{$user}</span> &larr; sur la page des statistiques du poker des vendredis entre amis.</h1>";
        $legend1 = "Voici les différentes méthodes affichages des stats du poker :";
        $method1 = "Affichage brute sans aucune modification.";
        $method2 = "Affichage de toutes les visites d'un joueur.";
        $method3 = "Le sommaire d'un joueur en particulier.";
        $method4 = "Le sommaire de tous les joueurs";
        $method5 = "Affichage d'un tournois par son numéro.";
        $method6 = "Affichage d'un tournois par la date.";
        $method7 = "<span class='infoImportant'>À partir du 101e tournois</span>, le sommaire des prix citrons et des killers.";
        $h3 = "Le numéro du bouton sera la méthode sélectionnée";
        $legend2 = "Veuillez sélectionner votre méthode :";
        $label1 = "Pour les méthodes 2 et 3, veuillez sélectionner un joueur :"; 
        $label2 = "Pour les méthodes 5, veuillez sélectionner un numéro tournois :"; 
        $label3 = "Pour les méthodes 6, veuillez sélectionner une date d'un tournois :"; 
        $option = "À sélectionner";
        $legend3 = "Voici le résultat de la méthode d'affichage choisie :";
        $joueur = "Joueur";
        $gain = "Gain";    
        $victoire = "Fini 1er";
        $citron = "Prix Citron";
        $fini2 = "Fini 2e";  
        $noTournois = "No. partie";
        $nbTournois = "Nb parties";
        $date = "Date";
        $killer = "Killer";
        $msgErreur_joueur = "Veuillez sélectionner un joueur !";
        $msgErreur_ID = "Veuillez sélectionner un numéro de tournois !";
        $msgErreur_Date = "Veuillez sélectionner une date d'un tournois !";
        $btnLogin = "Retour à la page connexion";
        $btnReturn = "Retour à la page Accueil";
        $returnUp = "Retour au choix d'affichage";
    } elseif ($typeLangue === 'english') {
        $titre = "Statistics page";
        $h1 = "<h1>Welcome to you &rarr; <span class='userDisplay'>{$user}</span> &larr; on the statictics page about the friday nights poker between somes friends.</h1>";
        $legend1 = "Here are the differents methods of displaying poker statistics";
        $method1 = "Display all information with no modification.";
        $method2 = "Display all information about one player.";
        $method3 = "The summary about one player.";
        $method4 = "The summary about all players";
        $method5 = "Display a tournament by number.";
        $method6 = "Display a tournament by date.";
        $method7 = "<span class='infoImportant'>From the 101st tournaments</span>, the summary about lemons price and killers.";
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
        $date = "Date";
        $msgErreur_joueur = "Please select one player";
        $msgErreur_ID = "Please select a tournament number !";
        $msgErreur_Date = "Please select a date from a tournament !";
        $btnLogin = "Return login page";
        $btnReturn = "Return Home page";
        $returnUp = "Back to the method of displaying";        
    }

    $arrayMots = ['titre'=>$titre, 'h1'=>$h1, 'legend1'=>$legend1, 'method1'=>$method1,       'method2'=>$method2,'method3'=>$method3,'method4'=>$method4,'method5'=>$method5,'method6'=>$method6,'method7'=>$method7,'h3'=>$h3,'legend2'=>$legend2,'label1'=>$label1,'label2'=>$label2,'label3'=>$label3,'option'=>$option,'legend3'=>$legend3,'joueur'=>$joueur,'gain'=>$gain,'killer'=>$killer,'victoire'=>$victoire,'fini2'=>$fini2,'noTournois'=>$noTournois,'nbTournois'=>$nbTournois,'date'=>$date,'citron'=>$citron,'msgErreur_joueur'=>$msgErreur_joueur,'msgErreur_ID'=>$msgErreur_ID,'msgErreur_Date'=>$msgErreur_Date,'btnLogin'=>$btnLogin,'btnReturn'=>$btnReturn,'returnUp'=>$returnUp];

    return $arrayMots;
}
/*
 * Cette fonction aura pour but de créer une liste des joueurs qui sera envoyée dans un menu pour en sélectionner un si nécessaire
 */
function creationListe($nameSelected, $connMYSQL, $arrayMots){
    //https://pixabay.com/fr/médaille-or-conception-2163347/
    //https://pixabay.com/fr/m%C3%A9daille-argent-conception-2163349/
    //https://pixabay.com/fr/m%C3%A9daille-bronze-conception-2163351/
    $sql = "select joueur from benoitmignault_ca_mywebsite.joueur order by joueur";
    $result = $connMYSQL->query($sql);
    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_POST['method'] == 1 || $_POST['method'] == 4 || $_POST['method'] == 5 || $_POST['method'] == 6 || $_POST['method'] == 7){
        $liste_Joueur = "<option value='' selected>{$arrayMots['option']}</option>";
        foreach($result as $row){        
            $liste_Joueur .= "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>"; 
        }
    } elseif ($_POST['method'] == 2 || $_POST['method'] == 3){
        $liste_Joueur = "<option value=''>{$arrayMots['option']}</option>";
        foreach($result as $row){
            if ($nameSelected === $row['joueur']){
                $liste_Joueur .= "<option value=\"{$row['joueur']}\" selected>{$row['joueur']}</option>"; 
            } else {
                $liste_Joueur .= "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>"; 
            }
        }        
    }
    return $liste_Joueur;
}
/*
 * Cette fonction aura pour but de créer une liste des numéros de tournois en ordre croissant
 */
function creationListeId($IDSelected, $connMYSQL, $arrayMots){
    $sql = "SELECT distinct id_tournoi FROM benoitmignault_ca_mywebsite.poker order by id_tournoi";
    $result = $connMYSQL->query($sql);

    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_POST['method'] != 5){
        $liste_Id_tournois = "<option value='' selected>{$arrayMots['option']}</option>";
        foreach($result as $row){        
            $liste_Id_tournois .= "<option value=\"{$row['id_tournoi']}\">{$row['id_tournoi']}</option>"; 
        }
    } elseif ($_POST['method'] == 5){
        $liste_Id_tournois = "<option value=''>{$arrayMots['option']}</option>";
        foreach($result as $row){
            if ($IDSelected === $row['id_tournoi']){
                $liste_Id_tournois .= "<option value=\"{$row['id_tournoi']}\" selected>{$row['id_tournoi']}</option>"; 
            } else {
                $liste_Id_tournois .= "<option value=\"{$row['id_tournoi']}\">{$row['id_tournoi']}</option>"; 
            }
        }        
    }
    return $liste_Id_tournois;  
}
/*
 * Cette fonction aura pour but de créer une liste des dates des différents tournois qui a eu lieu
 */
function creationListeDate($tournoiDate, $connMYSQL, $arrayMots){
    $sql = "SELECT distinct date FROM benoitmignault_ca_mywebsite.poker order by date";
    $result = $connMYSQL->query($sql);

    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_POST['method'] != 6){
        $liste_Date_tournois = "<option value='' selected>{$arrayMots['option']}</option>";
        foreach($result as $row){        
            $liste_Date_tournois .= "<option value=\"{$row['date']}\">{$row['date']}</option>"; 
        }
    } elseif ($_POST['method'] == 6){
        $liste_Date_tournois = "<option value=''>{$arrayMots['option']}</option>";
        foreach($result as $row){
            if ($tournoiDate === $row['date']){
                $liste_Date_tournois .= "<option value=\"{$row['date']}\" selected>{$row['date']}</option>"; 
            } else {
                $liste_Date_tournois .= "<option value=\"{$row['date']}\">{$row['date']}</option>"; 
            }
        }        
    }
    return $liste_Date_tournois;  
}

/*
Comme je n'ai pas de statistiques du 100e, j'ai mis des îcones de médailes à côté de leur nom à Frédéric V, Frédéric et à Marc-andré
*/
function lesGrandsGagnants_100e($nom_Champion){
    if ($nom_Champion === "Frederic V"){
        $icone = "<img src=\"./photo/medaile_or.jpg\" alt=\"or\" title=\"or\">";
    } elseif ($nom_Champion === "Frederic"){
        $icone = "<img src=\"./photo/medaile_argent.jpg\" alt=\"argent\" title=\"argent\">";
    } elseif ($nom_Champion === "Marc-Andre"){
        $icone = "<img src=\"./photo/medaile_bronze.jpg\" alt=\"bronze\" title=\"bronze\">";
    } else {
        $icone = "";
    }  
    return $icone;
}

/*
 * Fonction qui affiche tous les joueurs à chaque tournois
 */
function affichageBrute($connMYSQL, $arrayMots) {
    $sql = "select joueur, gain, victoire, fini_2e, id_tournoi, date from benoitmignault_ca_mywebsite.poker order by id_tournoi, gain desc";
    $result = $connMYSQL->query($sql);
    $tableau = 
        "<table> 
            <thead> 
                <tr> <th colspan='6'>{$arrayMots['method1']}</th> </tr>
                <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                     <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> <th>{$arrayMots['date']}</th> 
                </tr> 
            </thead>
            <tbody>";                
    foreach($result as $row){
        $nombreGain = intval($row['gain']);
        $icone = lesGrandsGagnants_100e($row['joueur']);
        $tableau .= 
            "<tr>
                <td>{$row['joueur']}{$icone}</td>";
        if ($nombreGain > 0){
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
/*
 * Cette fonction est presque pareil à la précédente «affichageBrute()» avec le nom du joueur en variable 
 * qui sera utilisé pour créer un tableau avec uniquement le joueur sélectionné
 *  
 */
function affichageUnjoueur($nom, $connMYSQL, $arrayMots){
    if ($nom === ""){
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_joueur']}</h3>";
    } else { 
        $sql = "SELECT * FROM benoitmignault_ca_mywebsite.poker where joueur = '{$nom}' order by id_tournoi";
        $result = $connMYSQL->query($sql);
        $tableau = "
                    <table> <thead>
                        <tr> <th colspan='6'>{$arrayMots['method2']} &rarr; {$nom} &larr;</th> </tr>
                        <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                             <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> <th>{$arrayMots['date']}</th> </tr>            
                            </thead>
                            <tbody>";                
        foreach($result as $row){
            $nombreGain = intval($row['gain']); 
            $icone = lesGrandsGagnants_100e($row['joueur']);
            $tableau .= "<tr> <td>{$nom}{$icone}</td>";
            if ($nombreGain > 0){
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
/* 
Affichage le sommaire d'un joueur sélectionné comme pour la 4e méthode mais seulement un joueur !
*/
function sommaireUnjoueur($nom, $connMYSQL, $arrayMots){
    if ($nom === ""){
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_joueur']}</h3>";
    } else {
        $sql = "SELECT
                    joueur,
                    SUM(gain) as gainTotaux,
                    count(case victoire when 'X' then 1 else null end) as nb_victoire,
                    count(case fini_2e when 'X' then 1 else null end) as nb_fini2e,
                    count(joueur) as nb_presence
                FROM
                    benoitmignault_ca_mywebsite.poker
                where 
                    joueur = '{$nom}'";
        $result = $connMYSQL->query($sql);        
        $tableau = "<table> 
                        <thead> 
                            <tr> <th colspan='5'>{$arrayMots['method3']} &rarr; {$nom} &larr;</th> </tr>
                            <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                                 <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['nbTournois']}</th> </tr>            
                        </thead>
                        <tbody>";

        foreach($result as $row){
            $nombreGain = intval($row['gainTotaux']);
            $icone = lesGrandsGagnants_100e($row['joueur']);
            $tableau .= "<tr> 
                        <td>{$nom}{$icone}</td>";
            if ($nombreGain > 0){    
                $tableau .= "<td class='positif'>{$nombreGain}</td>";                
            } elseif ($nombreGain < 0){
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
/*
Affichage d'un tableau contenant chacun des joueurs avec la somme de leur gain net, le nombre de présences, le nombre de victoires et le nombre de fini 2e.
le tableau est automatiquement trié grâce à ma requête SQL pré-trié
*/
function sommaireTousJoueurs($connMYSQL, $arrayMots){
    $sql = "SELECT
                joueur,
                SUM(gain) as gainTotaux,
                count(case victoire when 'X' then 1 else null end) as nb_victoire,
                count(case fini_2e when 'X' then 1 else null end) as nb_fini2e,
                count(joueur) as nb_presence
            FROM
                benoitmignault_ca_mywebsite.poker
            GROUP BY 
                joueur
            order by 
                gainTotaux desc, nb_victoire desc, nb_fini2e desc, nb_presence";
    $result = $connMYSQL->query($sql);
    $tableau = "<table> 
                    <thead> 
                        <tr> <th colspan='5'>{$arrayMots['method4']}</th> </tr>
                        <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                                <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['nbTournois']}</th> </tr>            
                    </thead> <tbody>";

    foreach($result as $row){
        $nombreGain = intval($row['gainTotaux']); 
        $icone = lesGrandsGagnants_100e($row['joueur']);
        $tableau .= "<tr>
                        <td>{$row['joueur']}{$icone}</td>";
        if ($nombreGain > 0){    
            $tableau .= "<td class='positif'>{$nombreGain}</td>";                
        } elseif ($nombreGain < 0){
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
    return $tableau;
}

/* Fonction pour afficher par un numéro de tournois sélectionner */
function affichageParNumero($numeroID, $connMYSQL, $arrayMots){
    if ($numeroID === ""){
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_ID']}</h3>";
    } else {
        $sql = "SELECT * FROM benoitmignault_ca_mywebsite.poker where id_tournoi = '{$numeroID}' order by gain desc";
        $result = $connMYSQL->query($sql);
        $tableau = "<table> 
                        <thead> 
                            <tr> <th colspan='6'>{$arrayMots['method5']} &rarr; {$numeroID} &larr;</th> </tr>
                            <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                                 <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> 
                                 <th>{$arrayMots['date']}</th> </tr>            
                        </thead>
                        <tbody>";

        foreach($result as $row){
            $nombreGain = intval($row['gain']);
            $icone = lesGrandsGagnants_100e($row['joueur']);
            $tableau .= "<tr> 
                        <td>{$row['joueur']}{$icone}</td>";
            if ($nombreGain > 0){    
                $tableau .= "<td class='positif'>{$nombreGain}</td>";                
            } elseif ($nombreGain < 0){
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

/* Fonction pour afficher par uune date d'un tournois sélectionner */
function affichageParDate($tournoiDate, $connMYSQL, $arrayMots){
    if ($tournoiDate === ""){
        $tableau = "<h3 class='msgErreur'>{$arrayMots['msgErreur_Date']}</h3>";
    } else {
        $sql = "SELECT * FROM benoitmignault_ca_mywebsite.poker where date = '{$tournoiDate}' order by gain desc";
        $result = $connMYSQL->query($sql);
        $tableau = "<table> 
                        <thead> 
                            <tr> <th colspan='6'>{$arrayMots['method6']} &rarr; {$tournoiDate} &larr;</th> </tr>
                            <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['gain']}</th> <th>{$arrayMots['victoire']}</th> 
                                 <th>{$arrayMots['fini2']}</th> <th>{$arrayMots['noTournois']}</th> 
                                 <th>{$arrayMots['date']}</th> </tr>            
                        </thead>
                        <tbody>";

        foreach($result as $row){
            $nombreGain = intval($row['gain']);
            $icone = lesGrandsGagnants_100e($row['joueur']);
            $tableau .= "<tr> 
                        <td>{$row['joueur']}{$icone}</td>";
            if ($nombreGain > 0){    
                $tableau .= "<td class='positif'>{$nombreGain}</td>";                
            } elseif ($nombreGain < 0){
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

/* Fonction pour faire afficher de nouvelles statistiques avancées */
function affichageKillerCitron($connMYSQL, $arrayMots){
    $sql = "SELECT
                joueur,
                SUM(killer) as prixKiller,
                SUM(prixCitron) as citronPrice,
                count(case victoire when 'X' then 1 else null end) as nb_victoire,
                count(case fini_2e when 'X' then 1 else null end) as nb_fini2e,
                count(joueur) as nb_presence
            FROM
                benoitmignault_ca_mywebsite.poker
            where 
                id_tournoi > 100
            GROUP BY joueur
            order by prixKiller desc, citronPrice, nb_victoire desc, nb_fini2e desc, nb_presence";
    $result = $connMYSQL->query($sql);
    $tableau = "<table> 
                        <thead> 
                            <tr> <th colspan='6'>{$arrayMots['method7']}</th></tr>
                            <tr> <th>{$arrayMots['joueur']}</th> <th>{$arrayMots['killer']}</th> <th>{$arrayMots['citron']}</th> 
                                 <th>{$arrayMots['victoire']}</th> <th>{$arrayMots['fini2']}</th> 
                                 <th>{$arrayMots['nbTournois']}</th> </tr>            
                        </thead>
                        <tbody>";

    foreach($result as $row){        
        $icone = lesGrandsGagnants_100e($row['joueur']);
        $tableau .= "<tr>
                        <td>{$row['joueur']}{$icone}</td>        
                        <td>{$row['prixKiller']}</td>
                        <td>{$row['citronPrice']}</td>
                        <td>{$row['nb_victoire']}</td>
                        <td>{$row['nb_fini2e']}</td>
                        <td>{$row['nb_presence']}</td>
                    </tr>";   
    } 
    $tableau .= "</tbody></table>";
    return $tableau;
}

function redirection($typeLangue){
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        session_destroy();
        header("Location: /erreur/erreur.php");          
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_destroy();
        if (isset($_POST['return'])){             
            if ($typeLangue == 'english'){                
                header("Location: /login/login.php?langue=english");
            } else {
                header("Location: /login/login.php?langue=francais");
            }        
        } elseif (isset($_POST['home'])){
            if ($typeLangue == 'english'){
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

/* Fonction pour ouvrir une connexion à la BD */
function connexionBD(){     
    $host = "benoitmignault.ca.mysql";
    $user = "benoitmignault_ca_mywebsite";
    $password = "d-&47mK!9hjGC4L-";
    $bd = "benoitmignault_ca_mywebsite";
    /*
    $host = "localhost";
    $user = "zmignaub";
    $password = "Banane11";
    $bd = "benoitmignault_ca_mywebsite";
    */
    $connMYSQL = mysqli_connect($host, $user, $password, $bd); 

    return $connMYSQL;
}
/* Fonction pour valider si le user et son mdp dans les variables sessions sont bonnes */
function verificationUser($connMYSQL){
    $sql = "select user, password from benoitmignault_ca_mywebsite.login";    
    $result = $connMYSQL->query($sql);

    foreach($result as $row){
        if ($row['user'] === $_SESSION['user']){
            if (password_verify($_SESSION['password'], $row['password'])){
                return true; // dès qu'on trouve notre user + son bon mdp on exit de la fct
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
    if (isset($_SESSION['user']) && isset($_SESSION['password']) && 
        isset($_SESSION['typeLangue'])){        
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);         
        $typeLangue = $_SESSION['typeLangue'];
        $user = $_SESSION['user'];  
    } else {
        redirection($typeLangue);
    }    

    // on vérifier si notre user existe en bonne éduforme
    if (!$verificationUser){
        redirection($typeLangue);
    } elseif ($typeLangue !== "francais" && $typeLangue !== "english"){
        redirection($typeLangue);
    } else {          
        $arrayMots = traduction($typeLangue, $user);        
        $liste_Joueur = creationListe("", $connMYSQL, $arrayMots);
        $liste_Id_tournois = creationListeId("", $connMYSQL, $arrayMots);
        $liste_Date_tournois = creationListeDate("", $connMYSQL, $arrayMots);
    }
    $connMYSQL->close();  
} // fin du GET

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $user = "";
    $typeLangue = ""; 
    if (!isset($_SESSION['user']) || !isset($_SESSION['typeLangue']) || !isset($_SESSION['password']) ){
        redirection($typeLangue);
    } else {   
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);     
        $typeLangue = $_SESSION['typeLangue'];
        $user = $_SESSION['user'];
        if (!$verificationUser){
            redirection($typeLangue);
        } elseif ($typeLangue !== "francais" && $typeLangue !== "english"){
            redirection($typeLangue);
        } else {   
            $arrayMots = traduction($typeLangue, $user);

            if (isset($_POST['joueur'])){
                $nom = $_POST['joueur'];
            } else {
                $nom = "";
            }
            if (isset($_POST['listeId'])){
                $numeroID = $_POST['listeId'];               
            } else {
                $numeroID = "";
            }
            if (isset($_POST['listeDate'])){
                $tournoiDate = $_POST['listeDate'];               
            } else {
                $tournoiDate = "";
            }

            if (isset($_POST['method'])){
                switch ($_POST['method']){
                    case 1 : $tableauResult = affichageBrute($connMYSQL, $arrayMots); break;
                    case 2 : $tableauResult = affichageUnjoueur($nom, $connMYSQL, $arrayMots); break;
                    case 3 : $tableauResult = sommaireUnjoueur($nom, $connMYSQL, $arrayMots); break;
                    case 4 : $tableauResult = sommaireTousJoueurs($connMYSQL, $arrayMots); break;
                    case 5 : $tableauResult = affichageParNumero($numeroID, $connMYSQL, $arrayMots); break;
                    case 6 : $tableauResult = affichageParDate($tournoiDate, $connMYSQL, $arrayMots); break;
                    case 7 : $tableauResult = affichageKillerCitron($connMYSQL, $arrayMots); break;    
                }            
            } else {
                redirection($typeLangue);
            }
            $liste_Joueur = creationListe($nom, $connMYSQL, $arrayMots);
            $liste_Id_tournois = creationListeId($numeroID, $connMYSQL, $arrayMots);
            $liste_Date_tournois = creationListeDate($tournoiDate, $connMYSQL, $arrayMots);
        }
        $connMYSQL->close();
    }    
} // fin du POST
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
                    <ol class='liste'>
                        <li><?php echo $arrayMots['method1']; ?></li>
                        <li><?php echo $arrayMots['method2']; ?></li>
                        <li><?php echo $arrayMots['method3']; ?></li>
                        <li><?php echo $arrayMots['method4']; ?></li>
                        <li><?php echo $arrayMots['method5']; ?></li>
                        <li><?php echo $arrayMots['method6']; ?></li>
                        <li><?php echo $arrayMots['method7']; ?></li>
                    </ol>
                    <h3><?php echo $arrayMots['h3']; ?></h3>
                </fieldset>
            </div>            
            <div class='btnRetour'> 
                <form method="post" action="poker.php"> 
                    <input class='bouton' type="submit" name="return" value="<?php echo $arrayMots['btnLogin']; ?>">
                    <input class='bouton' type="submit" name="home" value="<?php echo $arrayMots['btnReturn']; ?>">
                </form>
            </div>
            <div class="method">
                <fieldset>
                    <legend align="center"><?php echo $arrayMots['legend2']; ?></legend>
                    <form method='post' action='poker.php'>
                        <div class='formulaire'>
                            <div>
                                <input class='bouton' type='submit' name='method' value=1>
                                <input class='bouton' type='submit' name='method' value=2>
                                <input class='bouton' type='submit' name='method' value=3>
                                <input class='bouton' type='submit' name='method' value=4>
                                <input class='bouton' type='submit' name='method' value=5>
                                <input class='bouton' type='submit' name='method' value=6>
                                <input class='bouton' type='submit' name='method' value=7>
                            </div>                            
                            <div class="listeJoueur">
                                <label for="joueur"><?php echo $arrayMots['label1']; ?></label>
                                <select id="joueur" name="joueur">
                                    <?php echo $liste_Joueur; ?>
                                </select>
                            </div> 
                            <div class="listeJoueur">
                                <label for="idTournois"><?php echo $arrayMots['label2']; ?></label>
                                <select id="idTournois" name="listeId">
                                    <?php echo $liste_Id_tournois; ?>
                                </select>
                            </div>

                            <div class="listeJoueur">
                                <label for="tournois_date"><?php echo $arrayMots['label3']; ?></label>
                                <select id="tournois_date" name="listeDate">
                                    <?php echo $liste_Date_tournois; ?>
                                </select>
                            </div>                            
                        </div>
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
        </div>        
    </body>
</html>