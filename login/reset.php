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
    
    function remplissageChamps($array_Champs){
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            if (isset($_GET['langue'])){
                $array_Champs["typeLangue"] = $_GET['langue'];
            } else {
                $array_Champs["invalid_Language"] = true;
            }
    
            if (isset($_GET['key'])){
                $array_Champs["lien_Crypte"] = $_GET['key'];
            }
    
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['typeLangue'])){
                $array_Champs["typeLangue"] = $_POST['typeLangue'];
            } else {
                $array_Champs["invalid_Language"] = true;
            }
    
            if (isset($_POST['lien_Crypte'])){
                $array_Champs["lien_Crypte"] = $_POST['lien_Crypte'];
            }
            if (isset($_POST['password_Temp'])){
                $array_Champs["password_Temp"] = $_POST['password_Temp'];
            }
    
            if (isset($_POST['new_Password_1'])){
                $array_Champs["new_Password_1"] = $_POST['new_Password_1'];
            }
    
            if (isset($_POST['new_Password_2'])){
                $array_Champs["new_Password_2"] = $_POST['new_Password_2'];
            }
            date_default_timezone_set('America/New_York');
            $current_time = date("Y-m-d H:i:s");
            $current_timestamp = strtotime($current_time);
            $array_Champs["token_Time_Used"] = $current_timestamp;
        }
        return $array_Champs;
    }
    
    function verifChamp($array_Champs, $connMYSQL) {
        // Section de vérification des champs vide
        if (empty($array_Champs['password_Temp'])){
            $array_Champs['champVidePWD_Temp'] = true;
        }
    
        if (empty($array_Champs['new_Password_1'])){
            $array_Champs['champVidePWD_1'] = true;
        }
    
        if (empty($array_Champs['new_Password_2'])){
            $array_Champs['champVidePWD_2'] = true;
        }
    
        if ($array_Champs['champVidePWD_Temp'] || $array_Champs['champVidePWD_1'] || $array_Champs['champVidePWD_2']){
            $array_Champs['champsVidePWD'] = true;
        }
    
        if ( (strcmp($array_Champs["new_Password_1"],$array_Champs["new_Password_2"]) != 0) && !$array_Champs['champVidePWD_1'] && !$array_Champs['champVidePWD_2'] ) {
            $array_Champs["champsPWD_New_NonEgal"] = true;
        }
    
        $sql = "select password, passwordTemp, temps_Valide_link from login where reset_link = '{$array_Champs["lien_Crypte"]}'";
        $result = $connMYSQL->query($sql);
        $row_cnt = $result->num_rows;
        if ($row_cnt !== 0){
            foreach ($result as $row) {
                if (!password_verify($array_Champs['password_Temp'], $row['passwordTemp'])){
                    $array_Champs['champPWD_Temp_NonEgal'] = true;
                }
    
                // Si le nouveau password est égal dans les deux champs c'est
                if (!$array_Champs["champsPWD_New_NonEgal"] && !$array_Champs['champVidePWD_1'] && !$array_Champs['champVidePWD_2']){
                    if (!password_verify($array_Champs['new_Password_1'], $row['password'])){
                        $array_Champs['ancien_Nouveau_PWD_Diff'] = true;
                    }
                }
    
                // Validation que le temps accordé au link soit toujours valide
                if ($array_Champs["token_Time_Used"] > ((int)$row['temps_Valide_link'])){
                    $array_Champs["token_Time_Expired"] = true;
                }
            }
        } else {
            $array_Champs['erreurManipulationBD'] = true;
        }
    
        if ($array_Champs['champPWD_Temp_NonEgal'] || $array_Champs["champsPWD_New_NonEgal"]){
            $array_Champs["champsPWD_NonEgal"] = true;
        }
        // Section pour vérifier la validité de la longueur des champs passwords
        $longueurPWDTemp = strlen($array_Champs['password_Temp']);
        $longueurPWD1 = strlen($array_Champs['new_Password_1']);
        $longueurPWD2 = strlen($array_Champs['new_Password_1']);
    
        if ($longueurPWDTemp > 25) {
            $array_Champs['champTropLongPWD_Temp'] = true;
        }
    
        if ($longueurPWD1 > 25) {
            $array_Champs['champTropLongPWD_1'] = true;
        }
    
        if ($longueurPWD2 > 25) {
            $array_Champs['champTropLongPWD_2'] = true;
        }
    
        if ($array_Champs['champTropLongPWD_Temp'] || $array_Champs['champTropLongPWD_1'] || $array_Champs['champTropLongPWD_2']){
            $array_Champs['champTropLong'] = true;
        }
        // Section pour valider si il y a des caractères invalides dans les champs password
        $patternPass = "#^[0-9a-zA-Z]([0-9a-zA-Z]{0,23})[0-9a-zA-Z]$#";
    
        if (!preg_match($patternPass, $array_Champs['password_Temp']) && !$array_Champs['champVidePWD_Temp']) {
            $array_Champs['champInvalidPWD_Temp'] = true;
        }
    
        if (!preg_match($patternPass, $array_Champs['new_Password_1']) && !$array_Champs['champVidePWD_1']) {
            $array_Champs['champInvalidPWD_1'] = true;
        }
        if (!preg_match($patternPass, $array_Champs['new_Password_2']) && !$array_Champs['champVidePWD_2']) {
            $array_Champs['champInvalidPWD_2'] = true;
        }
    
        if ($array_Champs['champInvalidPWD_Temp'] || $array_Champs['champInvalidPWD_1'] || $array_Champs['champInvalidPWD_2']){
            $array_Champs['champInvalidPWD'] = true;
        }
        
        return $array_Champs;
    }
    
    function situation($array_Champs){
        $typeSituation = 0;
        if ($array_Champs['champVidePWD_Temp'] && $array_Champs['champVidePWD_1'] && $array_Champs['champVidePWD_2']) {
            $typeSituation = 1;
        } elseif ($array_Champs['champPWD_Temp_NonEgal'] && !$array_Champs["champsPWD_New_NonEgal"] && !$array_Champs['champsVidePWD'] && !$array_Champs['champInvalidPWD'] && !$array_Champs['ancien_Nouveau_PWD_Diff']){
            $typeSituation = 2;
        } elseif (!$array_Champs['champPWD_Temp_NonEgal'] && $array_Champs["champsPWD_New_NonEgal"] && !$array_Champs['champsVidePWD'] && !$array_Champs['champInvalidPWD'] && !$array_Champs['ancien_Nouveau_PWD_Diff']){
            $typeSituation = 3;
        } elseif (!$array_Champs['champPWD_Temp_NonEgal'] && $array_Champs['champVidePWD_1'] && $array_Champs['champVidePWD_2']){
            $typeSituation = 4;
        } elseif ($array_Champs['champVidePWD_Temp'] && !$array_Champs["champsPWD_New_NonEgal"] && !$array_Champs['champInvalidPWD'] && !$array_Champs['ancien_Nouveau_PWD_Diff']){
            $typeSituation = 5;
        } elseif (!$array_Champs['champPWD_Temp_NonEgal'] && $array_Champs['champsVidePWD']){
            $typeSituation = 6;
        } elseif (!$array_Champs["champsVidePWD"] && !$array_Champs["champsPWD_NonEgal"] && $array_Champs["token_Time_Expired"] && !$array_Champs["champTropLong"] && !$array_Champs["champInvalidPWD"]){
            $typeSituation = 7;
        } elseif (!$array_Champs['ancien_Nouveau_PWD_Diff'] && !$array_Champs['champVidePWD_1'] && !$array_Champs['champVidePWD_2'] && !$array_Champs['champPWD_Temp_NonEgal']){
            $typeSituation = 12;
        } elseif ($array_Champs['creationUserSuccess'] && $array_Champs['ancien_Nouveau_PWD_Diff']){
            $typeSituation = 8;
        } elseif ($array_Champs['champPWD_Temp_NonEgal'] && $array_Champs["champsPWD_New_NonEgal"] && !$array_Champs['champsVidePWD'] && !$array_Champs['champInvalidPWD']){
            $typeSituation = 9;
        } elseif (!$array_Champs['champPWD_Temp_NonEgal'] && $array_Champs['champInvalidPWD']){
            $typeSituation = 10;
        } elseif ($array_Champs['champPWD_Temp_NonEgal'] && $array_Champs['champInvalidPWD']){
            $typeSituation = 11;
        } elseif ($array_Champs['erreurManipulationBD']){
            $typeSituation = 13;
        }
    
        return $typeSituation;
    }
    
    function verif_link_BD($array_Champs, $connMYSQL){
        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("select reset_link from login where reset_link =? ");
    
        /* Lecture des marqueurs */
        $stmt->bind_param("s", $array_Champs["lien_Crypte"]);
    
        /* Exécution de la requête */
        $stmt->execute();
    
        /* Association des variables de résultat */
        $result = $stmt->get_result();
    
        // Close statement
        $stmt->close();
    
        if ($result->num_rows == 1){
            $array_Champs["lien_Crypte_Good"] = true;
        }
        return $array_Champs;
    }
    
    function changementPassword($array_Champs, $connMYSQL){
        // Remise à NULL pour les
        $newPWDencrypt = encryptementPassword($array_Champs["new_Password_1"]);
    
        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("update login set password =? , reset_link =? , passwordTemp =?, temps_Valide_link =? where reset_link =? ");
        /* Lecture des marqueurs */
        $zero = 0; // Je dois créer une variable qui va contenir la valeur 0
        $stringVide = NUll;
        $stmt->bind_param("sssis", $newPWDencrypt,$stringVide,$stringVide,$zero,$array_Champs["lien_Crypte"]);
        /* Exécution de la requête */
        $status = $stmt->execute();
    
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        } else {
            $array_Champs['creationUserSuccess'] = true;
            // Remise à leur valeur initial, car le changement de mot de passe est terminé et le lien n'est plus valide
            $array_Champs['password_Temp'] = "";
            $array_Champs['new_Password_1'] = "";
            $array_Champs['new_Password_2'] = "";
            $array_Champs['lien_Crypte'] = "";
            $array_Champs['token_Time_Used'] = 0;
        }
    
        /* close statement and connection */
        $stmt->close();
    
        return $array_Champs;
    }
    
    function encryptementPassword($password_Temp) {
        $password_Encrypted = password_hash($password_Temp, PASSWORD_BCRYPT);
        return $password_Encrypted;
    }
    
    function redirection($array_Champs) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            if ($array_Champs["invalid_Language"] || !$array_Champs["lien_Crypte_Good"]) {
                header("Location: /erreur/erreur.php");
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Les deux premiers IF sont pour la page d'acceuil
            // Les deux derniers IF sont pour la page login
            if (isset($_POST['return']) && $array_Champs["typeLangue"] == "francais") {
                header("Location: /index.html");
            } elseif (isset($_POST['return']) && $array_Champs["typeLangue"] == "english") {
                header("Location: /english/english.html");
            } elseif (isset($_POST['page_Login']) && $array_Champs["typeLangue"] == "francais") {
                header("Location: /login/login.php?langue=francais");
            } elseif (isset($_POST['page_Login']) && $array_Champs["typeLangue"] == "english") {
                header("Location: /login/login.php?langue=english");
            } elseif (!$array_Champs["lien_Crypte_Good"] || $array_Champs["invalid_Language"]){
                header("Location: /erreur/erreur.php");
            }
        }
        exit; // pour arrêter l'éxecution du code php
    }
	
	// Les fonctions communes
	$connMYSQL = connexion();
	$array_Champs = initialisation();
	$array_Champs = remplisage_champs($array_Champs, $connMYSQL);
    
    
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        $array_Champs = initialChamp();
        $array_Champs = remplissageChamps($array_Champs);
        $connMYSQL = connexionBD();
        $array_Champs = verif_link_BD($array_Champs, $connMYSQL);
        if ($array_Champs["invalid_Language"]){
            redirection($array_Champs);
        } elseif (!$array_Champs["lien_Crypte_Good"]){
            redirection($array_Champs);
        } else {
            $arrayMots = traduction($array_Champs);
        }
        $connMYSQL->close();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $array_Champs = initialChamp();
        $array_Champs = remplissageChamps($array_Champs);
        $connMYSQL = connexionBD();
        if ($array_Champs["invalid_Language"]){
            redirection($array_Champs);
        } elseif (isset($_POST['return']) || isset($_POST['page_Login'])){
            redirection($array_Champs);
        } elseif (isset($_POST['create_New_PWD'])){
            $array_Champs = verif_link_BD($array_Champs, $connMYSQL);
            if (!$array_Champs["lien_Crypte_Good"]) {
                redirection($array_Champs);
            } else {
                $array_Champs = verifChamp($array_Champs, $connMYSQL);
    
                if (!$array_Champs["champsVidePWD"] && !$array_Champs["champsPWD_NonEgal"] && !$array_Champs["token_Time_Expired"] && !$array_Champs["champTropLong"] && !$array_Champs["champInvalidPWD"] && $array_Champs["ancien_Nouveau_PWD_Diff"]){
                    $array_Champs = changementPassword($array_Champs, $connMYSQL);
                }
            }
            $array_Champs["situation"] = situation($array_Champs);
            $arrayMots = traduction($array_Champs);
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
            <fieldset class="<?php if ($array_Champs['creationUserSuccess']) { echo "changerAvecSucces"; } ?>">
                <legend class="legendCenter"><?php echo $arrayMots['legend']; ?></legend>
                <form method="post" action="./reset.php">
                    <div class="connexion">
                        <div class="information <?php if ($array_Champs['champInvalidPWD_Temp'] || $array_Champs['champTropLongPWD_Temp'] || $array_Champs["champVidePWD_Temp"] || $array_Champs['champPWD_Temp_NonEgal']) { echo 'erreur';} ?>">
                            <label for="password_Temp"><?php echo $arrayMots['mdp_Temp']; ?></label>
                            <div>
                                <input <?php if ($array_Champs['creationUserSuccess']) { echo "disabled"; } ?> autofocus id="password_Temp" type='password' maxlength="25" name="password_Temp" value="<?php echo $array_Champs['password_Temp']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if (( $_SERVER['REQUEST_METHOD'] === 'POST' && !$array_Champs['ancien_Nouveau_PWD_Diff'] ) || $array_Champs['champInvalidPWD_1'] || $array_Champs['champTropLongPWD_1'] || $array_Champs["champVidePWD_1"] || $array_Champs["champsPWD_New_NonEgal"]) { echo 'erreur';} ?>">
                            <label for="new_Password_1"><?php echo $arrayMots['mdp_1']; ?></label>
                            <div>
                                <input <?php if ($array_Champs['creationUserSuccess']) { echo "disabled"; } ?> id="new_Password_1" type='password' maxlength="25" name="new_Password_1" value="<?php echo $array_Champs['new_Password_1']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if ( ( $_SERVER['REQUEST_METHOD'] === 'POST' && !$array_Champs['ancien_Nouveau_PWD_Diff'] ) || $array_Champs['champInvalidPWD_2'] || $array_Champs['champTropLongPWD_2'] || $array_Champs["champVidePWD_2"] || $array_Champs["champsPWD_New_NonEgal"]) { echo 'erreur';} ?>">
                            <label for="new_Password_2"><?php echo $arrayMots['mdp_2']; ?></label>
                            <div>
                                <input <?php if ($array_Champs['creationUserSuccess']) { echo "disabled"; } ?> id="new_Password_2" type='password' maxlength="25" name="new_Password_2" value="<?php echo $array_Champs['new_Password_2']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                    </div>
                    <div class="troisBTN">
                        <input <?php if ($array_Champs['creationUserSuccess']) { echo "class=\"bouton disabled\" disabled"; } else { echo "class=\"bouton\""; }?> type='submit' name='create_New_PWD' value="<?php echo $arrayMots['btn_create_New_PWD']; ?>">
                        <input type='hidden' name='typeLangue' value="<?php echo $array_Champs['typeLangue']; ?>">
                        <input type='hidden' name='lien_Crypte' value="<?php echo $array_Champs['lien_Crypte']; ?>">
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="footer">
            <!-- ici la situation sera lorsque l'envoi par courriel sera un succès -->
            <div class='avert <?php if ($array_Champs["situation"] != 8) { echo 'erreur'; } ?>'>
                <p> <?php echo $arrayMots['message']; ?> </p>
            </div>
            <div class="btnRetour">
                <form method="post" action="./reset.php">
                    <input class="bouton" type="submit" name="page_Login" value="<?php echo $arrayMots['btn_login']; ?>">
                    <input class="bouton" type="submit" name="return" value="<?php echo $arrayMots['btn_return']; ?>">
                    <input type='hidden' name='typeLangue' value="<?php echo $array_Champs['typeLangue']; ?>">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
