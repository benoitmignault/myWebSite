<?php
	// Les includes nécessaires
	include_once("../traduction/traduction_create_link_reset.php");
	include_once("../includes/fct-connexion-bd.php");

    function initialChamp() {
    $champInitial = ["erreurManipulationBD" => false, "creationUserSuccess" => false, "champsPWD_New_NonEgal" => false, "champTropLongPWD_2" => false, 
    "champTropLongPWD_1" => false, "champTropLongPWD_Temp" => false, "champInvalidPWD" => false, "champInvalidPWD_Temp" => false, 
    "champInvalidPWD_2" => false, "champInvalidPWD_1" => false, "champPWD_Temp_NonEgal" => false, "champsPWD_NonEgal" => false, 
    "champsVidePWD" => false, "champVidePWD_1" => false, "champVidePWD_2" => false, "champVidePWD_Temp" => false, "invalid_Language" => false, 
    "token_Time_Used" => 0, "token_Time_Expired" => false, "champTropLong" => false, "lien_Crypte_Good" => false, "lien_Crypte" => "", 
    "situation" => 0, "typeLangue" => "", "password_Temp" => "", "new_Password_1" => "", "new_Password_2" => "", "ancien_Nouveau_PWD_Diff" => false];

    return $champInitial;
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

    if ( (strcmp($champs["new_Password_1"],$champs["new_Password_2"]) != 0) && !$champs['champVidePWD_1'] && !$champs['champVidePWD_2'] ) {
        $champs["champsPWD_New_NonEgal"] = true;   
    }

    $sql = "select password, passwordTemp, temps_Valide_link from login where reset_link = '{$champs["lien_Crypte"]}'";
    $result = $connMYSQL->query($sql);
    $row_cnt = $result->num_rows; 
    if ($row_cnt !== 0){
        foreach ($result as $row) {
            if (!password_verify($champs['password_Temp'], $row['passwordTemp'])){            
                $champs['champPWD_Temp_NonEgal'] = true;            
            }

            // Si le nouveau password est égal dans les deux champs c'est 
            if (!$champs["champsPWD_New_NonEgal"] && !$champs['champVidePWD_1'] && !$champs['champVidePWD_2']){
                if (!password_verify($champs['new_Password_1'], $row['password'])){            
                    $champs['ancien_Nouveau_PWD_Diff'] = true;            
                }
            } 

            // Validation que le temps accordé au link soit toujours valide
            if ($champs["token_Time_Used"] > ((int)$row['temps_Valide_link'])){
                $champs["token_Time_Expired"] = true;   
            }
        }   
    } else {
        $champs['erreurManipulationBD'] = true;       
    }

    if ($champs['champPWD_Temp_NonEgal'] || $champs["champsPWD_New_NonEgal"]){
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

    if (!preg_match($patternPass, $champs['password_Temp']) && !$champs['champVidePWD_Temp']) {
        $champs['champInvalidPWD_Temp'] = true;
    }

    if (!preg_match($patternPass, $champs['new_Password_1']) && !$champs['champVidePWD_1']) {
        $champs['champInvalidPWD_1'] = true;
    }
    if (!preg_match($patternPass, $champs['new_Password_2']) && !$champs['champVidePWD_2']) {
        $champs['champInvalidPWD_2'] = true;
    }

    if ($champs['champInvalidPWD_Temp'] || $champs['champInvalidPWD_1'] || $champs['champInvalidPWD_2']){
        $champs['champInvalidPWD'] = true;
    }
    
    return $champs;
}

function situation($champs){
    $typeSituation = 0;
    if ($champs['champVidePWD_Temp'] && $champs['champVidePWD_1'] && $champs['champVidePWD_2']) {
        $typeSituation = 1; 
    } elseif ($champs['champPWD_Temp_NonEgal'] && !$champs["champsPWD_New_NonEgal"] && !$champs['champsVidePWD'] && !$champs['champInvalidPWD'] && !$champs['ancien_Nouveau_PWD_Diff']){
        $typeSituation = 2; 
    } elseif (!$champs['champPWD_Temp_NonEgal'] && $champs["champsPWD_New_NonEgal"] && !$champs['champsVidePWD'] && !$champs['champInvalidPWD'] && !$champs['ancien_Nouveau_PWD_Diff']){
        $typeSituation = 3; 
    } elseif (!$champs['champPWD_Temp_NonEgal'] && $champs['champVidePWD_1'] && $champs['champVidePWD_2']){
        $typeSituation = 4; 
    } elseif ($champs['champVidePWD_Temp'] && !$champs["champsPWD_New_NonEgal"] && !$champs['champInvalidPWD'] && !$champs['ancien_Nouveau_PWD_Diff']){
        $typeSituation = 5; 
    } elseif (!$champs['champPWD_Temp_NonEgal'] && $champs['champsVidePWD']){
        $typeSituation = 6; 
    } elseif (!$champs["champsVidePWD"] && !$champs["champsPWD_NonEgal"] && $champs["token_Time_Expired"] && !$champs["champTropLong"] && !$champs["champInvalidPWD"]){
        $typeSituation = 7; 
    } elseif (!$champs['ancien_Nouveau_PWD_Diff'] && !$champs['champVidePWD_1'] && !$champs['champVidePWD_2'] && !$champs['champPWD_Temp_NonEgal']){
        $typeSituation = 12; 
    } elseif ($champs['creationUserSuccess'] && $champs['ancien_Nouveau_PWD_Diff']){
        $typeSituation = 8; 
    } elseif ($champs['champPWD_Temp_NonEgal'] && $champs["champsPWD_New_NonEgal"] && !$champs['champsVidePWD'] && !$champs['champInvalidPWD']){
        $typeSituation = 9;
    } elseif (!$champs['champPWD_Temp_NonEgal'] && $champs['champInvalidPWD']){
        $typeSituation = 10; 
    } elseif ($champs['champPWD_Temp_NonEgal'] && $champs['champInvalidPWD']){
        $typeSituation = 11;
    } elseif ($champs['erreurManipulationBD']){
        $typeSituation = 13; 
    } 

    return $typeSituation;
}

function verif_link_BD($champs, $connMYSQL){
    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare("select reset_link from login where reset_link =? ");

    /* Lecture des marqueurs */
    $stmt->bind_param("s", $champs["lien_Crypte"]);

    /* Exécution de la requête */
    $stmt->execute();

    /* Association des variables de résultat */
    $result = $stmt->get_result();

    // Close statement
    $stmt->close();    

    if ($result->num_rows == 1){
        $champs["lien_Crypte_Good"] = true;
    }  
    return $champs;
}

function changementPassword($champs, $connMYSQL){
    // Remise à NULL pour les 
    $newPWDencrypt = encryptementPassword($champs["new_Password_1"]);     

    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare("update login set password =? , reset_link =? , passwordTemp =?, temps_Valide_link =? where reset_link =? ");
    /* Lecture des marqueurs */
    $zero = 0; // Je dois créer une variable qui va contenir la valeur 0
    $stringVide = NUll;
    $stmt->bind_param("sssis", $newPWDencrypt,$stringVide,$stringVide,$zero,$champs["lien_Crypte"]);
    /* Exécution de la requête */
    $status = $stmt->execute();

    if ($status === false) {
        trigger_error($stmt->error, E_USER_ERROR);
    } else {
        $champs['creationUserSuccess'] = true;
        // Remise à leur valeur initial, car le changement de mot de passe est terminé et le lien n'est plus valide
        $champs['password_Temp'] = "";
        $champs['new_Password_1'] = "";
        $champs['new_Password_2'] = "";
        $champs['lien_Crypte'] = "";
        $champs['token_Time_Used'] = 0;
    }

    /* close statement and connection */
    $stmt->close();  

    return $champs;
}

function encryptementPassword($password_Temp) {
    $password_Encrypted = password_hash($password_Temp, PASSWORD_BCRYPT);
    return $password_Encrypted;
}

function redirection($champs) { 
    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        if ($champs["invalid_Language"] || !$champs["lien_Crypte_Good"]) {
            header("Location: /erreur/erreur.php");
        }
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
        } elseif (!$champs["lien_Crypte_Good"] || $champs["invalid_Language"]){
            header("Location: /erreur/erreur.php");
        }
    }
    exit; // pour arrêter l'éxecution du code php
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

            if (!$champs["champsVidePWD"] && !$champs["champsPWD_NonEgal"] && !$champs["token_Time_Expired"] && !$champs["champTropLong"] && !$champs["champInvalidPWD"] && $champs["ancien_Nouveau_PWD_Diff"]){
                $champs = changementPassword($champs, $connMYSQL);
            }
        }
        $champs["situation"] = situation($champs);
        $arrayMots = traduction($champs);
    }       
    $connMYSQL->close();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $arrayMots['lang']; ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Envoi du courriel avec le lien">
    <!-- Le fichier reset.png est la propriété du site https://pixabay.com/fr/bouton-r%C3%A9initialiser-inscrivez-vous-31199/-->
    <link rel="shortcut icon" href="reset.png">
    <link rel="stylesheet" type="text/css" href="login.css">
    <title><?php echo $arrayMots['title']; ?></title>
    <style>
        body {
            margin: 0;
            /* Fichier photoPoker.jpg est une propriété du site https://pixabay.com/fr/syst%C3%A8me-r%C3%A9seau-actualit%C3%A9s-connexion-2457651/ sous licence libre */
            background-image: url("photologin.jpg");
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
                <li class='info'><?php echo $arrayMots['li3']; ?></li>
            </ul>
            <fieldset class="<?php if ($champs['creationUserSuccess']) { echo "changerAvecSucces"; } ?>">
                <legend class="legendCenter"><?php echo $arrayMots['legend']; ?></legend>
                <form method="post" action="./reset.php">
                    <div class="connexion">
                        <div class="information <?php if ($champs['champInvalidPWD_Temp'] || $champs['champTropLongPWD_Temp'] || $champs["champVidePWD_Temp"] || $champs['champPWD_Temp_NonEgal']) { echo 'erreur';} ?>">
                            <label for="password_Temp"><?php echo $arrayMots['mdp_Temp']; ?></label>
                            <div>
                                <input <?php if ($champs['creationUserSuccess']) { echo "disabled"; } ?> autofocus id="password_Temp" type='password' maxlength="25" name="password_Temp" value="<?php echo $champs['password_Temp']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if (( $_SERVER['REQUEST_METHOD'] === 'POST' && !$champs['ancien_Nouveau_PWD_Diff'] ) || $champs['champInvalidPWD_1'] || $champs['champTropLongPWD_1'] || $champs["champVidePWD_1"] || $champs["champsPWD_New_NonEgal"]) { echo 'erreur';} ?>">
                            <label for="new_Password_1"><?php echo $arrayMots['mdp_1']; ?></label>
                            <div>
                                <input <?php if ($champs['creationUserSuccess']) { echo "disabled"; } ?> id="new_Password_1" type='password' maxlength="25" name="new_Password_1" value="<?php echo $champs['new_Password_1']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if ( ( $_SERVER['REQUEST_METHOD'] === 'POST' && !$champs['ancien_Nouveau_PWD_Diff'] ) || $champs['champInvalidPWD_2'] || $champs['champTropLongPWD_2'] || $champs["champVidePWD_2"] || $champs["champsPWD_New_NonEgal"]) { echo 'erreur';} ?>">
                            <label for="new_Password_2"><?php echo $arrayMots['mdp_2']; ?></label>
                            <div>
                                <input <?php if ($champs['creationUserSuccess']) { echo "disabled"; } ?> id="new_Password_2" type='password' maxlength="25" name="new_Password_2" value="<?php echo $champs['new_Password_2']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                    </div>
                    <div class="troisBTN">
                        <input <?php if ($champs['creationUserSuccess']) { echo "class=\"bouton disabled\" disabled"; } else { echo "class=\"bouton\""; }?> type='submit' name='create_New_PWD' value="<?php echo $arrayMots['btn_create_New_PWD']; ?>">
                        <input type='hidden' name='typeLangue' value="<?php echo $champs['typeLangue']; ?>">
                        <input type='hidden' name='lien_Crypte' value="<?php echo $champs['lien_Crypte']; ?>">
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="footer">
            <!-- ici la situation sera lorsque l'envoi par courriel sera un succès -->
            <div class='avert <?php if ($champs["situation"] != 8) { echo 'erreur'; } ?>'>
                <p> <?php echo $arrayMots['message']; ?> </p>
            </div>
            <div class="btnRetour">
                <form method="post" action="./reset.php">
                    <input class="bouton" type="submit" name="page_Login" value="<?php echo $arrayMots['btn_login']; ?>">
                    <input class="bouton" type="submit" name="return" value="<?php echo $arrayMots['btn_return']; ?>">
                    <input type='hidden' name='typeLangue' value="<?php echo $champs['typeLangue']; ?>">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
