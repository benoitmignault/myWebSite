<?php 
function initialChamp() {
    $champInitial = ["champTropLongPWD_2" => false, "champTropLongPWD_1" => false, "champTropLongPWD_Temp" => false, "champInvalidPWD_2" => false, "champInvalidPWD_1" => false, "champPWD_Temp_NonEgal" => false, "champsPWD_NonEgal" => false, "champInvalidPWD_Temp" => false, "champsVidePWD" => false, "champVidePWD_1" => false, "champVidePWD_2" => false, "champVidePWD_Temp" => false, "invalid_Language" => false, "token_Time_Used" => 0, "token_Time_Expired" => false, "champInvalid" => false, "champTropLong" => false, "champVide" => false, "lien_Crypte_Good" => false, "lien_Crypte" => "", "situation" => 0, "typeLangue" => "", "password_Temp" => "", "new_Password_1" => "", "new_Password_2" => ""];
    return $champInitial;
}

function traduction($champs) {    
    if ($champs["typeLangue"] === 'francais') {
        $title = "Mot de passe en changement !";
        $p1 = "Vous pouvez maintenant changer votre du mot de passe !";
        $li1 = "Veuillez inscrire votre mot de passe temporaire.";
        $li2 = "Veuillez choisir un nouveau mot de passe et le confirmer dans le 3e champs.";
        $legend = "Saisir de quoi de nouveau !";
        $mdp_Temp = "Mot de passe temporaire :";
        $mdp_1 = "Nouveau mot de passe :";
        $mdp_2 = "Confirmer votre mot de passe :";
        $btn_create_New_PWD = "Enregistrer...";
        $page_Login = "Se Connecter";
        $return = "Retour à l'accueil";
    } elseif ($champs["typeLangue"] === 'english') {

    }
    $messageFinal = traductionSituation($champs);
    $arrayMots = ["title" => $title, "p1" => $p1, "li1" => $li1, "li2" => $li2, "legend" => $legend, "mdp_Temp" => $mdp_Temp, "mdp_1" => $mdp_1, "mdp_2" => $mdp_2, "btn_create_New_PWD" => $btn_create_New_PWD, "btn_login" => $page_Login, "btn_return" => $return];
    return $arrayMots;
}

function traductionSituation($champs){
    $message = "";
    if ($champs["typeLangue"] === 'francais') {
        switch ($champs['situation']) {

        }
    } elseif ($champs["typeLangue"] === 'english') {
        switch ($champs['situation']) {

        }
    }
    return $message;
}

function remplissageChamps($champs){
    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        if (isset($_GET['langue'])){
            $champs["typeLangue"] = $_GET['langue'];
        } else {
            $champs["invalid_Language"] = true;
        }

        if (isset($_GET['key'])){
            $champs["lien_Crypte"] = $_GET['key'];
        }        

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (isset($_POST['typeLangue'])){
            $champs["typeLangue"] = $_POST['typeLangue'];
        } else {
            $champs["invalid_Language"] = true;
        }

        if (isset($_POST['lien_Crypte'])){
            $champs["lien_Crypte"] = $_POST['lien_Crypte'];
        }
        if (isset($_POST['password_Temp'])){
            $champs["password_Temp"] = $_POST['password_Temp'];
        }

        if (isset($_POST['new_Password_1'])){
            $champs["new_Password_1"] = $_POST['new_Password_1'];
        }

        if (isset($_POST['new_Password_2'])){
            $champs["new_Password_2"] = $_POST['new_Password_2'];
        }
        date_default_timezone_set('America/New_York');
        $current_time = date("Y-m-d H:i:s");
        $current_timestamp = strtotime($current_time);
        $champs["token_Time_Used"] = $current_timestamp;
    }
    return $champs;
}


function verifChamp($champs, $connMYSQL) {
    // Section de vérification des champs vide
    if (empty($champs['password_Temp'])){
        $champs['champVidePWD_Temp'] = true;
    }

    if (empty($champs['new_Password_1'])){
        $champs['champVidePWD_1'] = true;
    }

    if (empty($champs['new_Password_2'])){
        $champs['champVidePWD_2'] = true;
    }

    if ($champs['champVidePWD_Temp'] || $champs['champVidePWD_1'] || $champs['champVidePWD_2']){
        $champs['champsVidePWD'] = true;
    }
    // Section de vérification si le password temporaire est valide avec la BD
    $sql = "select passwordTemp, temps_Valide_link from benoitmignault_ca_mywebsite.login where reset_link = '{$champs["lien_Crypte"]}'";
    $result = $connMYSQL->query($sql);
    // J'ai déjà valider si le lien est valide donc j'ai un résultat obligatoire
    foreach ($result as $row) {
        if (!password_verify($champs['password_Temp'], $row['passwordTemp'])){            
            $champs['champPWD_Temp_NonEgal'] = true;            
        }
        // Validation que le temps accordé au link soit toujours valide
        if ($champs["token_Time_Used"] > ((int)$row['temps_Valide_link'])){
            $champs["token_Time_Expired"] = true;   
        }
    }    
    // Vérification que les deux mots de passes sont pareils
    if (strcmp($champs["new_Password_1"],$champs["new_Password_2"]) != 0){
        $champs["champsPWD_NonEgal"] = true;   
    }
    // Section pour vérifier la validité de la longueur des champs passwords
    $longueurPWDTemp = strlen($champs['password_Temp']);
    $longueurPWD1 = strlen($champs['new_Password_1']);
    $longueurPWD2 = strlen($champs['new_Password_1']);

    if ($longueurPWDTemp > 25) {
        $champs['champTropLongPWD_Temp'] = true;
    }

    if ($longueurPWD1 > 25) {
        $champs['champTropLongPWD_1'] = true;
    }

    if ($longueurPWD2 > 25) {
        $champs['champTropLongPWD_2'] = true;
    }

    if ($champs['champTropLongPWD_Temp'] || $champs['champTropLongPWD_1'] || $champs['champTropLongPWD_2']){
        $champs['champTropLong'] = true;
    }
    // Section pour valider si il y a des caractères invalides dans les champs password
    $patternPass = "#^[0-9a-zA-Z]([0-9a-zA-Z]{0,23})[0-9a-zA-Z]$#";
    if (!preg_match($patternPass, $champs['password_Temp'])) {
        $champInitial['champInvalidPassword'] = true;
    }

    if (!preg_match($patternPass, $champs['new_Password_1'])) {
        $champInitial['champInvalidPassword'] = true;
    }
    if (!preg_match($patternPass, $champs['new_Password_1'])) {
        $champInitial['champInvalidPassword'] = true;
    }


    return $champs;
}

function situation($champs){
    $typeSituation = 0;


    return $typeSituation;
}

function verif_link_BD($champs, $connMYSQL){
    $sql = "select reset_link from benoitmignault_ca_mywebsite.login where reset_link = '{$champs["lien_Crypte"]}'";
    $connMYSQL->query($sql);
    // Ici , le résultat doit etre absolument de 1 car sinon le link n'est pas valide
    if (mysqli_affected_rows($connMYSQL) == 1){
        $champs["lien_Crypte_Good"] = true;
    }    
    return $champs;
}

function changementPassword($champs, $connMYSQL){
    // Je dois vérifier que le 

    return $champs;
}


function encryptementPassword($password_Temp) {
    $password_Encrypted = password_hash($password_Temp, PASSWORD_BCRYPT);
    return $password_Encrypted;
}

function redirection($champs) { 
    // Nous avons deux possibilités d'erreurs
    if ($champs["invalid_Language"] || !$champs["lien_Crypte_Good"]) {
        header("Location: /erreur/erreur.php");

    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Les deux premiers IF sont pour la page d'acceuil
        // Les deux derniers IF sont pour la page login    
        if (isset($_POST['return']) && $champs["typeLangue"] == "francais") {
            header("Location: /index.html");
        } elseif (isset($_POST['return']) && $champs["typeLangue"] == "english") {
            header("Location: /english/english.html");          
        } elseif (isset($_POST['page_Login']) && $champs["typeLangue"] == "francais") {
            header("Location: /login/login.php?langue=francais");            
        } elseif (isset($_POST['page_Login']) && $champs["typeLangue"] == "english") {
            header("Location: /login/login.php?langue=english");            
        } 
    }
    exit; // pour arrêter l'éxecution du code php
}

function connexionBD() {
    /*
    $host = "benoitmignault.ca.mysql";
    $user = "benoitmignault_ca_mywebsite";
    $password = "d-&47mK!9hjGC4L-";
    $bd = "benoitmignault_ca_mywebsite";
    */
    $host = "localhost";
    $user = "zmignaub";
    $password = "Banane11";
    $bd = "benoitmignault_ca_mywebsite";

    $connMYSQL = mysqli_connect($host, $user, $password, $bd);
    $connMYSQL->query("set names 'utf8'");
    return $connMYSQL;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $champs = initialChamp();
    $champs = remplissageChamps($champs);
    $connMYSQL = connexionBD();
    $champs = verif_link_BD($champs, $connMYSQL);
    if ($champs["invalid_Language"]){
        redirection($champs);
    } elseif (!$champs["lien_Crypte_Good"]){
        redirection($champs);
    } else {
        $arrayMots = traduction($champs);
    }
    $connMYSQL->close();   
}

// IMPORTANT 
// à la fin rajouter ce projet direct en raccourci sur la page acceuil du site via la page createlinksendmail


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $champs = initialChamp();
    $champs = remplissageChamps($champs);
    $connMYSQL = connexionBD();
    if ($champs["invalid_Language"]){
        redirection($champs);        
    } elseif (isset($_POST['return']) || isset($_POST['page_Login'])){
        redirection($champs);
    } elseif (isset($_POST['create_New_PWD'])){
        $champs = verif_link_BD($champs, $connMYSQL);
        if (!$champs["lien_Crypte_Good"]) {
            redirection($champs);
        } else {
            $champs = verifChamp($champs, $connMYSQL);  






        }
        $champs["situation"] = situation($champs);
        $arrayMots = traduction($champs);
    }   







    // Ici on valide que le link est bon et que nous sommes dans les temps
    $connMYSQL->close();
}
?>
<html>
    <head>
        <meta charset="utf-8">	
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Envoi du courriel avec le lien">
        <!-- Le fichier reset.png est la propriété du site https://pixabay.com/fr/bouton-r%C3%A9initialiser-inscrivez-vous-31199/-->
        <link rel="shortcut icon" href="reset.png">	        
        <link rel="stylesheet" type="text/css" href="login.css"> 
        <title><?php echo $arrayMots['title']; ?></title> 
        <style>
            body{
                margin:0;    
                /* Fichier photoPoker.jpg est une propriété du site https://pixabay.com/fr/syst%C3%A8me-r%C3%A9seau-actualit%C3%A9s-connexion-2457651/ sous licence libre */
                /*background-image: url("photologin.jpg");*/
                background-position: center;
                background-attachment: fixed;
                background-size: 100%;
            }   
        </style>
    </head>
    <body>
        <div class="content"> 
            <div class="center">
                <p class='titre'><?php echo $arrayMots['p1']; ?></p>
                <ul>
                    <li class='info'><?php echo $arrayMots['li1']; ?></li>
                    <li class='info'><?php echo $arrayMots['li2']; ?></li>
                </ul>                
                <fieldset>
                    <legend align="center"><?php echo $arrayMots['legend']; ?></legend>
                    <form method="post" action="./reset.php">
                        <div class="connexion">
                            <div class="information <?php if (!$champs["lien_Crypte_Good"]) { echo 'erreur';} ?>">
                                <label for="password_Temp"><?php echo $arrayMots['mdp_Temp']; ?></label>
                                <div>
                                    <input id="password_Temp" type='password' maxlength="25" name="password_Temp" value="<?php echo $champs['password_Temp']; ?>"/>
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div> 
                            <div class="information <?php if (!$champs["lien_Crypte_Good"]) { echo 'erreur';} ?>">
                                <label for="new_Password_1"><?php echo $arrayMots['mdp_1']; ?></label>
                                <div>
                                    <input id="new_Password_1" type='password' maxlength="25" name="new_Password_1" value="<?php echo $champs['new_Password_1']; ?>"/>
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div> 
                            <div class="information <?php if (!$champs["lien_Crypte_Good"]) { echo 'erreur';} ?>">
                                <label for="new_Password_2"><?php echo $arrayMots['mdp_2']; ?></label>
                                <div>
                                    <input id="new_Password_2" type='password' maxlength="25" name="new_Password_2" value="<?php echo $champs['new_Password_2']; ?>"/>
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div> 
                        </div>
                        <div class="troisBTN"> 
                            <input class="bouton" type='submit' name='create_New_PWD' value="<?php echo $arrayMots['btn_create_New_PWD']; ?>">
                            <input type='hidden' name='typeLangue' value="<?php echo $champs['typeLangue']; ?>">
                            <input type='hidden' name='lien_Crypte' value="<?php echo $champs['lien_Crypte']; ?>">
                        </div>
                    </form>
                </fieldset>
            </div>
            <div class="footer">
                <!-- ici la situation sera lorsque l'envoi par courriel sera un succès -->
                <div class='avert <?php if ($champs["situation"] != 6) { echo 'erreur'; } ?>'>
                    <p> <?php echo $arrayMots['message']; ?> </p>
                </div>
                <div class="btnRetour">
                    <form method="post" action="./createLinkSendMail.php"> 
                        <input class="bouton" type="submit" name="page_Login" value="<?php echo $arrayMots['btn_login']; ?>"> 
                        <input class="bouton" type="submit" name="return" value="<?php echo $arrayMots['btn_return']; ?>">  
                        <input type='hidden' name='typeLangue' value="<?php echo $champs['typeLangue']; ?>">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>