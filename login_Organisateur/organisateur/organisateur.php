<?php
function traduction($champs, $connMYSQL) {
    $prenom = "";
    $sql = "SELECT name FROM benoitmignault_ca_mywebsite.login_organisateur where user = '{$champs['user']}'";
    $result = $connMYSQL->query($sql);   
    if ($result->num_rows > 0){
        foreach ($result as $row) {
            $prenom = $row['name'];
        }
    } 

    if ($champs["typeLangue"] === 'francais') {
        $titre = "Gestion d'un tournoi";
        $h1 = "Bienvenue à vous &rarr; <span class='userDisplay'>{$prenom}</span> &larr; sur la page de gestion d'un organisateur.";
        $h3_Ajouter = "Ajouter une combinaison.";
        $h3_Affichage = "Afficher les combinaisons.";
        $h3_Retirer = "Retirer une combinaison.";
        $option = "À sélectionner";
        $btn_timer = "Page du Timer";
        $btn_return = "Page d'Accueil";
        $valeur_couleur = "Valeur / Couleur";
        $petit_grosse_mise = "Petite mise / Grosse mise";
        $btn_ajout = "Ajouter";
        $btn_delete = "Détruire";
        $id_couleur = "ID correspondant à celui du tableau valeurs et couleurs.";
        $id_mises = "ID correspondant à celui du tableau petites et grosses mises.";
        $message = message_Situation($champs);
        $message_Erreur_BD = "Il y a eu un problème avec l'insertion de vos valeurs dans la BD. Veuillez recommencer !";
    } elseif ($champs["typeLangue"] === 'english') {
        $titre = "Management of a tournament";
        $h1 = "welcome to you &rarr; <span class='userDisplay'>{$prenom}</span> &larr; on the management page of an organizer.";
        $h3_Ajouter = "Add combination.";
        $h3_Affichage = "Show combinations.";
        $h3_Retirer = "Remove combination.";
        $option = "Select";
        $btn_timer = "TIMER page";
        $btn_return = "Back to Home";
        $valeur_couleur = "Value / Color";
        $petit_grosse_mise = "Small blind / Big blind";
        $btn_ajout = "Add";
        $btn_delete = "Remove";
        $id_couleur = "ID corresponding to that of the values and colors table.";
        $id_mises = "ID corresponding to that of the table small and large bets.";
        $message = message_Situation($champs);
        $message_Erreur_BD = "There was a problem with insert your values into the DB. Please try again !";
    }
    $arrayMots = ['message_Erreur_BD' => $message_Erreur_BD, 'message' => $message, 'title' => $titre, 'msg_welcome' => $h1, 'h3_Ajouter' => $h3_Ajouter, 'h3_Affichage' => $h3_Affichage, 'h3_Retirer'=> $h3_Retirer, 'valeur_couleur'=>$valeur_couleur,'petit_grosse_mise'=> $petit_grosse_mise, 'btn_timer'=>$btn_timer, 'btn_return'=>$btn_return, 'btn_ajout'=>$btn_ajout,'btn_delete'=>$btn_delete, 'id_couleur'=>$id_couleur, 'option'=>$option, 'id_mises'=>$id_mises];
    return $arrayMots;
}

function message_Situation($champs){
    $message = "";
    if ($champs["typeLangue"] === 'francais') {
        switch ($champs['situation']){
            case 1 : $message = "La valeur de votre jeton de couleur a été ajouté !"; break;  
            case 2 : $message = "La valeur et la couleur de votre jeton ne peut être null !"; break;  
            case 3 : $message = "Vous avez oublié de choisir une valeur avec votre couleur !"; break;  
            case 4 : $message = "Vous avez oublié de choisir une couleur avec votre valeur !"; break;  
            case 5 : $message = "Votre valeur existe déjà dans votre sélection, veuillez en choisir une autre !"; break;  
            case 6 : $message = "Votre valeur doit être un chiffre valide de 1 à 99999 !"; break;  
            case 7 : $message = "Le couple petite et grosse mises ont été ajoutées à votre sélection !"; break;  
            case 8 : $message = "La petite et la grosse mises ne peuvent être null !"; break;  
            case 9 : $message = "La petite mise ne peut être null !"; break;  
            case 10 : $message = "La grosse mise ne peut être null !"; break;  
            case 11 : $message = "Les deux mises ne peuvent être égal !"; break;  
            case 12 : $message = "La valeur de la grosse mise ne peut être plus petite que la petite mise !"; break;  
            case 13 : $message = "Votre petite mise existe déjà dans votre sélection des petites mises !"; break;  
            case 14 : $message = "Votre grosse mise existe déjà dans votre sélection des grosse mises !"; break;  
            case 15 : $message = "Votre mise doit être un chiffre valide de 1 à 99999 !"; break;  
            case 16 : $message = "La combinaison valeur / couleur du jeton ont été supprimé de la sélection !"; break;  
            case 17 : $message = "La combinaison valeur / couleur du jeton ne peut être null !"; break;  
            case 18 : $message = "La combinaison petite et grosse mise ont été supprimé de la sélection !"; break;  
            case 19 : $message = "La combinaison petite et grosse mise ne peut être null !"; break; 
        }
    } elseif ($champs["typeLangue"] === 'english') {
        switch ($champs['situation']){
            case 1 : $message = "The value of your color token has been added !"; break;  
            case 2 : $message = "The value and color of your token can not be null !"; break;  
            case 3 : $message = "You forgot to choose a value with your color !"; break;  
            case 4 : $message = "You forgot to choose a color with your value !"; break;  
            case 5 : $message = "Your value already exists in your selection, please choose another one !"; break;  
            case 6 : $message = "Your value must be a valid digit from 1 to 99999 !"; break;  
            case 7 : $message = "The couple big and small bets have been added to your selection !"; break;  
            case 8 : $message = "The small and the big bets can not be null !"; break;  
            case 9 : $message = "The small bet can not be null !"; break;  
            case 10 : $message = "The big bet can not be null !"; break;  
            case 11 : $message = "Both bets can not be equal !"; break;  
            case 12 : $message = "The value of the big bet can not be smaller than the small bet !"; break;  
            case 13 : $message = "Your little bet already exists in your selection of small bets !"; break;  
            case 14 : $message = "Your big bet already exists in your selection of big bets !"; break;  
            case 15 : $message = "Your bet must be a valid number from 1 to 99999 !"; break;  
            case 16 : $message = "The combination value / color of the token have been removed from the selection !"; break;  
            case 17 : $message = "The value / color combination of the token can not be null !"; break;  
            case 18 : $message = "The big and small bet combination have been removed from the selection !"; break;  
            case 19 : $message = "The combination big and small bet can not be null !"; break;  
        }
    }
    return $message;
}

function initialisation_Champs() {
    $champs = ["typeLangue" => "", "user" => "", "situation" => 0, "valeur" => "", "small" => "", 
               "big" => "", "couleur" => "","idCouleur" => 0, "idPetiteGrosse" => 0, "nbCouleurRestant" => 0, "idUser" => 0];
    return $champs;
}

function initialisation_indicateur() {
    $valid_Champ = ["id_couleur_vide" => false, "idPetiteGrosse_vide" => false, "valeur_vide" => false, "couleur_vide" => false, "doublon_valeur" => false, 
                    "doublon_small" => false, "doublon_big" => false, "small_vide" => false, "big_vide" => false, "small_big_egal" => false, 
                    "big_trop_petit" => false, "valeur_long_inval" => false, "valeur_invalide" => false, "small_long_inval" => false, 
                    "small_invalide" => false, "big_long_inval" => false, "big_invalide" => false];
    return $valid_Champ;
}

function remplissageChamps($champs, $connMYSQL) {
    if (isset($_SESSION['typeLangue'])){
        $champs["typeLangue"] = $_SESSION['typeLangue'];
    }
    if (isset($_SESSION['user'])){
        $champs["user"] = $_SESSION['user'];
        // Comme j'ai instauré une foreign key entre la table mise_small_big vers login_organisateur je dois aller récupérer Iduser pour l'insérer avec la nouvelle combinaison
        $sql = "select idUser from benoitmignault_ca_mywebsite.login_organisateur where user = '{$champs["user"]}' ";                
        $result_SQL = $connMYSQL->query($sql);
        $row = $result_SQL->fetch_row(); // C'est mon array de résultat
        $champs["idUser"] = (int) $row[0];	// Assignation de la valeur 
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (isset($_POST['btn_addValeurCouleur'])){
            $champs["valeur"] = $_POST['valeur'];
            $champs["couleur"] = $_POST['couleur'];
        } elseif (isset($_POST['btn_addSmallBig'])){
            $champs["small"] = $_POST['small'];
            $champs["big"] = $_POST['big'];
        } elseif (isset($_POST['btn_delValeurCouleur'])){
            $champs["idCouleur"] = intval($_POST['idValeurCouleur']);
        } elseif (isset($_POST['btn_delSmallBig'])){
            $champs["idPetiteGrosse"] = $_POST['idCoupleMise'];
        }
    }
    return $champs;
}

function validation($champs, $valid_Champ, $connMYSQL) {
    $valeurNumerique = "#^[0-9]{1}([0-9]{0,4})[0-9]{0,1}$#";
    $user = "\"" . $champs['user'] . "\"";
    if (isset($_POST['btn_delValeurCouleur'])){
        if ($champs["idCouleur"] === ""){
            $valid_Champ["id_couleur_vide"] = true;        
        } 
        // fin de la vérification avec le bouton pour retirer une combinaison des petites / grosses mises
    } elseif (isset($_POST['btn_delSmallBig'])){
        if ($champs["idPetiteGrosse"] === ""){
            $valid_Champ["idPetiteGrosse_vide"] = true;        
        }
        // fin de la vérification avec le bouton pour retirer une combinaison des Valeur / Couleur
    } elseif (isset($_POST['btn_addValeurCouleur'])){
        $longueurValeur = strlen($champs['valeur']);             
        if ($champs["valeur"] === ""){
            $valid_Champ["valeur_vide"] = true;        
        } 
        if ($champs["couleur"] === ""){
            $valid_Champ["couleur_vide"] = true;        
        }
        if (!preg_match($valeurNumerique, $champs['valeur'])) {
            $valid_Champ['valeur_invalide'] = true;
        }
        if ($longueurValeur > 6){
            $valid_Champ['valeur_long_inval'] = true;
        }        
        $valid_Champ['doublon_valeur'] = verification_doublon("benoitmignault_ca_mywebsite.amount_color", "amount", intval($champs["valeur"]), $user, $connMYSQL);
        // fin de la vérification avec le bouton des Valeur / Couleur
    } elseif (isset($_POST['btn_addSmallBig'])){
        $small = intval($champs["small"]);
        $big = intval($champs["big"]);
        $longueurSmall = strlen($champs['small']);
        $longueurBig = strlen($champs['big']);   
        if ($champs["small"] === ""){
            $valid_Champ["small_vide"] = true;        
        }
        if ($champs["big"] === ""){
            $valid_Champ["big_vide"] = true;        
        }
        if ($small === $big && $small !== 0 && $big !== 0){
            $valid_Champ["small_big_egal"] = true;    
        }
        if ($big < $small && $small !== 0 && $big !== 0){
            $valid_Champ["big_trop_petit"] = true;    
        }
        if ($longueurSmall > 6){
            $valid_Champ['small_long_inval'] = true;
        }
        if ($longueurBig > 6){
            $valid_Champ['big_long_inval'] = true;
        }
        if (!preg_match($valeurNumerique, $champs['small'])) {
            $valid_Champ['small_invalide'] = true;
        }
        if (!preg_match($valeurNumerique, $champs['big'])) {
            $valid_Champ['big_invalide'] = true;
        }

        $valid_Champ['doublon_small'] = verification_doublon("benoitmignault_ca_mywebsite.mise_small_big", "small", $small, $user, $connMYSQL);
        $valid_Champ['doublon_big'] = verification_doublon("benoitmignault_ca_mywebsite.mise_small_big", "big", $big, $user, $connMYSQL);
        // fin de la vérification avec le bouton des petites / grosses mises        
    } 
    return $valid_Champ;
}

function situation($champs, $valid_Champ) {
    $situation = 0;
    if (isset($_POST['btn_addValeurCouleur'])){
        if (!$valid_Champ["doublon_valeur"] && !$valid_Champ["valeur_vide"] && !$valid_Champ["couleur_vide"] && !$valid_Champ['valeur_invalide'] && !$valid_Champ['valeur_long_inval']){
            $situation = 1; 
        } elseif ($valid_Champ["valeur_vide"] && $valid_Champ["couleur_vide"]){
            $situation = 2; 
        } elseif ($valid_Champ["valeur_vide"] && !$valid_Champ["couleur_vide"]){
            $situation = 3; 
        } elseif (!$valid_Champ["valeur_vide"] && $valid_Champ["couleur_vide"]){
            $situation = 4; 
        } elseif ($valid_Champ["doublon_valeur"]){
            $situation = 5; 
        } elseif ($valid_Champ["valeur_invalide"] || $valid_Champ['valeur_long_inval']){
            $situation = 6; 
        }
    } elseif (isset($_POST['btn_addSmallBig'])){
        if (!$valid_Champ["doublon_small"] && !$valid_Champ["doublon_big"] && !$valid_Champ["small_vide"] && !$valid_Champ["big_vide"] && !$valid_Champ['small_big_egal'] && 
            !$valid_Champ['small_invalide'] && !$valid_Champ['big_invalide'] && !$valid_Champ['big_trop_petit'] && !$valid_Champ['small_long_inval'] && !$valid_Champ['big_long_inval']){
            $situation = 7;
        } elseif ($valid_Champ["small_vide"] && $valid_Champ["big_vide"]){
            $situation = 8;
        } elseif ($valid_Champ["small_vide"] && !$valid_Champ["big_vide"]){
            $situation = 9;
        } elseif (!$valid_Champ["small_vide"] && $valid_Champ["big_vide"]){
            $situation = 10; 
        } elseif ($valid_Champ["small_big_egal"]){
            $situation = 11; 
        } elseif ($valid_Champ["big_trop_petit"]){
            $situation = 12;
        } elseif ($valid_Champ["doublon_small"] && !$valid_Champ["doublon_big"]){
            $situation = 13; 
        } elseif (!$valid_Champ["doublon_small"] && $valid_Champ["doublon_big"]){
            $situation = 14;
        } elseif ($valid_Champ["small_long_inval"] || $valid_Champ['big_long_inval'] || $valid_Champ['small_invalide'] || $valid_Champ['big_invalide']){
            $situation = 15; 
        }
    } elseif (isset($_POST['btn_delValeurCouleur'])){
        if (!$valid_Champ["id_couleur_vide"]){
            $situation = 16;
        } elseif ($valid_Champ["id_couleur_vide"]){
            $situation = 17; 
        }
    } elseif (isset($_POST['btn_delSmallBig'])){
        if (!$valid_Champ["idPetiteGrosse_vide"]){
            $situation = 18; 
        } elseif ($valid_Champ["idPetiteGrosse_vide"]){
            $situation = 19;
        }
    } 
    return $situation;
}

function verification_doublon($table, $champ, $valeur, $user, $connMYSQL){
    $sql = "SELECT * FROM $table WHERE $champ = $valeur and user = $user";
    $result = $connMYSQL->query($sql);
    if ($result->num_rows > 0){
        return true;
    } else {
        return false;
    }
}

function nb_couleur_restant($connMYSQL, $champs){
    $sql = "SELECT * FROM benoitmignault_ca_mywebsite.color WHERE color_english not in (SELECT color_english FROM amount_color WHERE user = '{$champs['user']}')";
    $result = $connMYSQL->query($sql);    
    if ($result->num_rows > 0){
        $champs["nbCouleurRestant"] = $result->num_rows;
    }
    return $champs;
}

function choix_couleur_restant($connMYSQL, $champs){
    $choixDesOption = "";    
    $sql = "SELECT * FROM benoitmignault_ca_mywebsite.color WHERE color_english not in (SELECT color_english FROM amount_color WHERE user = '{$champs['user']}')";
    $result = $connMYSQL->query($sql);    
    if ($result->num_rows > 0){
        foreach ($result as $row) {
            if ($champs["typeLangue"] == "francais"){
                $choixDesOption .= "<option value=\"{$row['color_english']}\">{$row['color_french']}</option>";
            } elseif ($champs["typeLangue"] == "english") {
                $firstLetter = ucfirst($row['color_english']);
                $patternDark = "#[D][a][r][k]#";
                $patternLight = "#^[L][i][g][h][t]#";
                if (preg_match($patternDark, $firstLetter)){
                    $tableauMots = explode("Dark", $firstLetter);
                    $firstLetter = "Dark " . $tableauMots[1];
                } elseif (preg_match($patternLight, $firstLetter)){
                    $tableauMots = explode("Light", $firstLetter);
                    $firstLetter = "Light " . $tableauMots[1];
                }
                $choixDesOption .= "<option value=\"{$row['color_english']}\">{$firstLetter}</option>";
            }
        }        
    }
    return $choixDesOption;
}

function tableau_valeur_couleur($connMYSQL, $champs){
    $tableau = "";
    $sql = "SELECT * FROM benoitmignault_ca_mywebsite.amount_color where user = '{$champs['user']}' ORDER BY amount";
    $result = $connMYSQL->query($sql);    
    if ($result->num_rows > 0){
        if ($champs["typeLangue"] == "francais"){
            $tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Id</th><th>Valeur</th><th>Couleur</th></tr></thead>";
        } elseif ($champs["typeLangue"] == "english") {
            $tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Id</th><th>Value</th><th>Color</th></tr></thead>";
        }    
        $tableau .= "<tbody>";
        foreach ($result as $row) {
            $tableau .= "<tr> <td>{$row['id_couleur']}</td> <td>{$row['amount']}</td> <td bgcolor=\"{$row['color_english']}\"></td> </tr>";
        }
        $tableau .= "</tbody></table>";
    }
    return $tableau;
}

function tableau_petite_grosse($connMYSQL, $champs){
    $tableau = "";
    $sql = "SELECT * FROM benoitmignault_ca_mywebsite.mise_small_big where user = '{$champs['user']}' ORDER BY small, big";
    $result = $connMYSQL->query($sql);    
    if ($result->num_rows > 0){
        if ($champs["typeLangue"] == "francais"){
            $tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Id</th><th>Petite</th><th>Grosse</th></tr></thead>";
        } elseif ($champs["typeLangue"] == "english") {
            $tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Id</th><th>Small</th><th>Big</th></tr></thead>";
        } 
        $tableau .= "<tbody>";
        foreach ($result as $row) {
            $tableau .= "<tr> <td>{$row['id_valeur']}</td> <td>{$row['small']}</td> <td>{$row['big']}</td> </tr>";
        }
        $tableau .= "</tbody></table>";
    }
    return $tableau;
}

function id_couleur_choisis($connMYSQL, $champs){
    $choixDesOption = "";    
    $sql = "SELECT id_couleur FROM benoitmignault_ca_mywebsite.amount_color WHERE user = '{$champs['user']}' order by amount";
    $result = $connMYSQL->query($sql);   
    if ($result->num_rows > 0){
        foreach ($result as $row) {
            $choixDesOption .= "<option value=\"{$row['id_couleur']}\">{$row['id_couleur']}</option>";
        }
    }
    return $choixDesOption;
}

function id_mises_choisis($connMYSQL, $champs){
    $choixDesOption = "";    
    $sql = "SELECT id_valeur FROM benoitmignault_ca_mywebsite.mise_small_big WHERE user = '{$champs['user']}' order by small";
    $result = $connMYSQL->query($sql);   
    if ($result->num_rows > 0){
        foreach ($result as $row) {
            $choixDesOption .= "<option value=\"{$row['id_valeur']}\">{$row['id_valeur']}</option>";
        }
    }
    return $choixDesOption;
}

function insert_BD_valeur_couleur($connMYSQL, $champs){
    $valeur = intval($champs["valeur"]);  
    $insert = "INSERT INTO benoitmignault_ca_mywebsite.amount_color (user, amount, color_english, id_couleur, id_user) VALUES ";
    $insert .= "('" . $champs["user"] . "','" . $valeur . "','" . $champs["couleur"] . "', NULL , '" . $champs["idUser"] . "')";
    $result = $connMYSQL->query($insert);  
    return $result;
}

function insert_BD_petite_grosse_mise($connMYSQL, $champs){  
    $small = intval($champs["small"]);  
    $big = intval($champs["big"]);  
    $insert = "INSERT INTO benoitmignault_ca_mywebsite.mise_small_big (user, small, big, id_valeur, id_user) VALUES ";
    $insert .= "('" . $champs["user"] . "','" . $small . "','" . $big . "', NULL , '" . $champs["idUser"] . "')";
    $result = $connMYSQL->query($insert); 
    return $result;
}

function delete_BD_valeur_couleur($connMYSQL, $champs){
    $user = "\"" . $champs['user'] . "\"";
    $id = $champs["idCouleur"];
    $delete = "DELETE FROM benoitmignault_ca_mywebsite.amount_color WHERE user = $user and id_couleur = $id";
    $result = $connMYSQL->query($delete);  
    return $result;
}

function delete_BD_petite_grosse_mise($connMYSQL, $champs){
    $user = "\"" . $champs['user'] . "\"";
    $id = $champs["idPetiteGrosse"];
    $delete = "DELETE FROM benoitmignault_ca_mywebsite.mise_small_big WHERE user = $user and id_valeur = $id";
    $result = $connMYSQL->query($delete);
    return $result;
}

function reset_champs($champs){
    $champs["valeur"] = "";
    $champs["small"] = "";
    $champs["big"] = "";
    return $champs;
}

function connexionBD() {  
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
    $connMYSQL->query("set names 'utf8'"); // ceci permet d,avoir des accents affiché sur la page web ! 

    return $connMYSQL;
}

function verificationUser($connMYSQL) {
    $sql = "select user, password from benoitmignault_ca_mywebsite.login_organisateur WHERE user = '{$_SESSION['user']}'";
    $result = $connMYSQL->query($sql);
    if ($result->num_rows > 0){
        foreach ($result as $row) {
            if ($row['user'] === $_SESSION['user']) {
                if (password_verify($_SESSION['password'], $row['password'])) {
                    return true; // dès qu'on trouve notre user + son bon mdp on exit de la fct
                }
            }        
        }
    } else {
        return false;
    }
}

function redirection($champs) {  
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        session_destroy();
        header("Location: /erreur/erreur.php");
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_destroy();
        if (isset($_POST['timer'])) {
            header("Location: /timer/timer.php?langue={$champs["typeLangue"]}");
        } elseif (isset($_POST['home']) && $champs["typeLangue"] == "francais") {
            header("Location: /index.html");
        } elseif (isset($_POST['home']) && $champs["typeLangue"] == "english") {
            header("Location: /english/english.html");            
        }
    }
    exit; // pour arrêter l'éxecution du code php
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();
    $champs["typeLangue"] = "francais";
    if (isset($_SESSION['user']) && isset($_SESSION['password']) && isset($_SESSION['typeLangue'])) {
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);
    } else {
        redirection($champs);
    }

    if (!$verificationUser) {
        redirection($champs);
    } else {
        $champs = initialisation_Champs();
        $champs = remplissageChamps($champs, $connMYSQL);
        $champs = nb_couleur_restant($connMYSQL, $champs);
        $choix_couleur_restant = choix_couleur_restant($connMYSQL, $champs);
        $tableau_valeur_couleur = tableau_valeur_couleur($connMYSQL, $champs);
        $tableau_petite_grosse = tableau_petite_grosse($connMYSQL, $champs);
        $id_couleur_choisis = id_couleur_choisis($connMYSQL, $champs);
        $id_mises_choisis = id_mises_choisis($connMYSQL, $champs);
        $arrayMots = traduction($champs, $connMYSQL);        
    }
    $connMYSQL->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    session_start();
    $champs["typeLangue"] = "francais"; 
    if (isset($_SESSION['user']) && isset($_SESSION['password']) && isset($_SESSION['typeLangue'])) {
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);        
    } else {
        redirection($champs);
    }
    // on vérifier si notre user existe en bonne éduforme
    if (!$verificationUser) {
        redirection($champs);
    } else {
        $champs = initialisation_Champs();
        $valid_Champ = initialisation_indicateur();
        $champs = remplissageChamps($champs, $connMYSQL);        
        $valid_Champ = validation($champs, $valid_Champ, $connMYSQL);
        $champs['situation'] = situation($champs, $valid_Champ); // On met ajout au final juste la variable 
        $result = false;
        $operation_sur_BD = true;   

        if (isset($_POST['timer']) || isset($_POST['home']) ){
            redirection($champs);
        } else {
            if ($champs['situation'] === 1 || $champs['situation'] === 7 || $champs['situation'] === 16 || $champs['situation'] === 18){
                switch ($champs['situation']){            
                    case 1 : $result = insert_BD_valeur_couleur($connMYSQL, $champs); break;  
                    case 7 : $result = insert_BD_petite_grosse_mise($connMYSQL, $champs); break;  
                    case 16 : $result = delete_BD_valeur_couleur($connMYSQL, $champs); break;  
                    case 18 : $result = delete_BD_petite_grosse_mise($connMYSQL, $champs); break;  
                } 
                if ($result){
                    $operation_sur_BD = false; 
                }
            }

            $champs = reset_champs($champs);
            $arrayMots = traduction($champs, $connMYSQL);
            if ($result && !$operation_sur_BD || !$result && $operation_sur_BD){
                echo "<script>alert('".$arrayMots['message']."')</script>";
            } elseif (!$result && !$operation_sur_BD) {
                echo "<script>alert('".$arrayMots['message_Erreur_BD']."')</script>";
            }

            $choix_couleur_restant = choix_couleur_restant($connMYSQL, $champs);         
            $tableau_valeur_couleur = tableau_valeur_couleur($connMYSQL, $champs);
            $tableau_petite_grosse = tableau_petite_grosse($connMYSQL, $champs);
            $id_couleur_choisis = id_couleur_choisis($connMYSQL, $champs);
            $id_mises_choisis = id_mises_choisis($connMYSQL, $champs);
            $champs = nb_couleur_restant($connMYSQL, $champs);    
        }
    }
    $connMYSQL->close();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
        <!-- https://pixabay.com/fr/fichier-ic%C3%B4ne-web-document-2389211/ -->
        <link rel="shortcut icon" href="organisateur.png">	       
        <link rel="stylesheet" type="text/css" href="organisateur.css"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $arrayMots['title']; ?></title> 
        <style>
            body{
                margin:0;    
                /* Fichier photoPoker.jpg est une propriété du site https://pixabay.com/fr/cha%C3%AEne-de-blocs-personnels-2850276/ 
                sous licence libre */
                background-image: url("organisateur.jpg");
                background-position: center;
                background-attachment: fixed;
                background-size: 100%;
            }   
        </style>
    </head>
    <body>
        <h1><?php echo $arrayMots['msg_welcome']; ?></h1>
        <div class="container">
            <div class="ajout_combinaison">
                <h2><?php echo $arrayMots['h3_Ajouter']; ?></h2>    
                <form method="post" action="organisateur.php">
                    <div class='form_ajout_combinaison'>
                        <h3><?php echo $arrayMots['valeur_couleur']; ?></h3>
                        <input maxlength="5" type="text" <?php if ($champs['nbCouleurRestant'] === 0) { echo "disabled"; } ?> name="valeur" value="<?php echo $champs['valeur'] ?>"> 
                        <select name="couleur">                            
                            <option value="" selected><?php echo $arrayMots['option']; ?></option>
                            <?php echo $choix_couleur_restant; ?>
                        </select>
                        <input class="bouton" type="submit" <?php if ($champs['nbCouleurRestant'] === 0) { echo "disabled=\"disabled\""; } ?> name="btn_addValeurCouleur" value="<?php echo $arrayMots['btn_ajout']; ?>">                        
                    </div>
                </form>  
                <form method="post" action="organisateur.php">
                    <div class='form_ajout_combinaison'>
                        <h3><?php echo $arrayMots['petit_grosse_mise']; ?></h3>
                        <input maxlength="6" type="text" name="small" value="<?php echo $champs['small'] ?>"> 
                        <input maxlength="6" type="text" name="big" value="<?php echo $champs['big'] ?>">
                        <input class="bouton" type="submit" name="btn_addSmallBig" value="<?php echo $arrayMots['btn_ajout']; ?>">

                    </div>
                </form>  

            </div>

            <div class="affiche_combinaison">
                <h2><?php echo $arrayMots['h3_Affichage']; ?></h2>  
                <form method="post" action="organisateur.php">
                    <div class="form_affiche_combinaison">
                        <h3><?php echo $arrayMots['valeur_couleur']; ?></h3>
                        <div><?php echo $tableau_valeur_couleur; ?></div>                                                      
                    </div>
                </form>
                <form method="post" action="organisateur.php">
                    <div class="form_affiche_combinaison">
                        <h3><?php echo $arrayMots['petit_grosse_mise']; ?></h3>
                        <div><?php echo $tableau_petite_grosse; ?></div>                                                        
                    </div>
                </form>
            </div>

            <div class="retirer_combinaison">
                <h2><?php echo $arrayMots['h3_Retirer']; ?></h2>      
                <form method="post" action="organisateur.php">
                    <div class="form_retirer_combinaison">
                        <h3><?php echo $arrayMots['valeur_couleur']; ?></h3>
                        <label for="idValeurCouleur"><?php echo $arrayMots['id_couleur']; ?></label>   
                        <select id="idValeurCouleur" name="idValeurCouleur">
                            <option value="" selected><?php echo $arrayMots['option']; ?></option>
                            <?php echo $id_couleur_choisis; ?>
                        </select>
                        <input class="bouton" type="submit" name="btn_delValeurCouleur" value="<?php echo $arrayMots['btn_delete']; ?>"> 
                    </div>
                </form>
                <form method="post" action="organisateur.php">
                    <div class="form_retirer_combinaison">
                        <h3><?php echo $arrayMots['petit_grosse_mise']; ?></h3>
                        <label for="idCoupleMise"><?php echo $arrayMots['id_mises']; ?></label>
                        <select id="idCoupleMise" name="idCoupleMise">
                            <option value="" selected><?php echo $arrayMots['option']; ?></option>
                            <?php echo $id_mises_choisis; ?>
                        </select>
                        <input class="bouton" type="submit" name="btn_delSmallBig" value="<?php echo $arrayMots['btn_delete']; ?>">
                    </div>
                </form>
            </div>

            <div class="retour">
                <form method="post" action="organisateur.php">
                    <div class="form_retour">
                        <div class="btn_footer">
                            <input class="bouton" type="submit" name="timer" value="<?php echo $arrayMots['btn_timer']; ?>">
                        </div>
                        <div class="btn_footer">
                            <input class="bouton" type="submit" name="home" value="<?php echo $arrayMots['btn_return']; ?>">
                        </div>                        
                    </div>
                </form> 
            </div>

        </div>        
    </body>
</html>