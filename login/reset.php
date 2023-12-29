<?php
	// Les includes nécessaires
	include_once("../traduction/traduction_create_link_reset.php");
	include_once("../includes/fct-connexion-bd.php");
	
	/**
	 * Fonction qui va contenir tous ce dont on aura besoin.
	 * Une partie des variables de type string ou integer et une autre partie en boolean
	 * On va ajouter un array pour les mots traduits ou non
	 *
	 * @return array
	 */
    function initialChamp() {
        
        return array("type_langue" => "", "situation" => 0, "password_temp" => "", "pwd_1_new" => "", "pwd_2_new" => "", "lien_crypter" => "", 
                 "erreur_manip_bd" => false, "create_user_succes" => false, 
                 "champ_pwd_1_trop_long" => false, "champ_pwd_2_trop_long" => false, "champ_pwd_temp_trop_long" => false, "champs_pwd_trop_long" => false, 
                 "champ_pwd_temp_invalid" => false, "champ_pwd_1_invalid" => false, "champ_pwd_2_invalid" => false, "champs_pwd_invalid" => false, "pwd_old_new_diff" => false, 
                 "champ_pwd_new_none_equal" => false, "champ_pwd_temp_none_equal" => false, "champs_pwd_none_equal" => false,
                 "champ_pwd_1_empty" => false, "champ_pwd_2_empty" => false, "champ_pwd_temp_empty" => false, "champs_pwd_empty" => false, "invalid_language" => false,
                 "token_time_used" => 0, "token_time_expired" => false, "lien_crypter_good" => false, 
                 "liste_mots" => array());
}
    
    function remplissageChamps($array_Champs){
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            if (isset($_GET['langue'])){
                $array_Champs["type_langue"] = $_GET['langue'];
            } else {
                $array_Champs["invalid_language"] = true;
            }
    
            if (isset($_GET['key'])){
                $array_Champs["lien_crypter"] = $_GET['key'];
            }
    
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['type_langue'])){
                $array_Champs["type_langue"] = $_POST['type_langue'];
            } else {
                $array_Champs["invalid_language"] = true;
            }
    
            if (isset($_POST['lien_crypter'])){
                $array_Champs["lien_crypter"] = $_POST['lien_crypter'];
            }
            if (isset($_POST['password_temp'])){
                $array_Champs["password_temp"] = $_POST['password_temp'];
            }
    
            if (isset($_POST['pwd_1_new'])){
                $array_Champs["pwd_1_new"] = $_POST['pwd_1_new'];
            }
    
            if (isset($_POST['pwd_2_new'])){
                $array_Champs["pwd_2_new"] = $_POST['pwd_2_new'];
            }
            date_default_timezone_set('America/New_York');
            $current_time = date("Y-m-d H:i:s");
            $current_timestamp = strtotime($current_time);
            $array_Champs["token_time_used"] = $current_timestamp;
        }
        return $array_Champs;
    }
    
    function verifChamp($array_Champs, $connMYSQL) {
        // Section de vérification des champs vide
        if (empty($array_Champs['password_temp'])){
            $array_Champs['champ_pwd_temp_empty'] = true;
        }
    
        if (empty($array_Champs['pwd_1_new'])){
            $array_Champs['champ_pwd_1_empty'] = true;
        }
    
        if (empty($array_Champs['pwd_2_new'])){
            $array_Champs['champ_pwd_2_empty'] = true;
        }
    
        if ($array_Champs['champ_pwd_temp_empty'] || $array_Champs['champ_pwd_1_empty'] || $array_Champs['champ_pwd_2_empty']){
            $array_Champs['champs_pwd_empty'] = true;
        }
    
        if ( (strcmp($array_Champs["pwd_1_new"],$array_Champs["pwd_2_new"]) != 0) && !$array_Champs['champ_pwd_1_empty'] && !$array_Champs['champ_pwd_2_empty'] ) {
            $array_Champs["champ_pwd_new_none_equal"] = true;
        }
    
        $sql = "select password, passwordTemp, temps_Valide_link from login where reset_link = '{$array_Champs["lien_crypter"]}'";
        $result = $connMYSQL->query($sql);
        $row_cnt = $result->num_rows;
        if ($row_cnt !== 0){
            foreach ($result as $row) {
                if (!password_verify($array_Champs['password_temp'], $row['passwordTemp'])){
                    $array_Champs['champ_pwd_temp_none_equal'] = true;
                }
    
                // Si le nouveau password est égal dans les deux champs c'est
                if (!$array_Champs["champ_pwd_new_none_equal"] && !$array_Champs['champ_pwd_1_empty'] && !$array_Champs['champ_pwd_2_empty']){
                    if (!password_verify($array_Champs['pwd_1_new'], $row['password'])){
                        $array_Champs['pwd_old_new_diff'] = true;
                    }
                }
    
                // Validation que le temps accordé au link soit toujours valide
                if ($array_Champs["token_time_used"] > ((int)$row['temps_Valide_link'])){
                    $array_Champs["token_time_expired"] = true;
                }
            }
        } else {
            $array_Champs['erreur_manip_bd'] = true;
        }
    
        if ($array_Champs['champ_pwd_temp_none_equal'] || $array_Champs["champ_pwd_new_none_equal"]){
            $array_Champs["champs_pwd_none_equal"] = true;
        }
        // Section pour vérifier la validité de la longueur des champs passwords
        $longueurPWDTemp = strlen($array_Champs['password_temp']);
        $longueurPWD1 = strlen($array_Champs['pwd_1_new']);
        $longueurPWD2 = strlen($array_Champs['pwd_1_new']);
    
        if ($longueurPWDTemp > 25) {
            $array_Champs['champ_pwd_temp_trop_long'] = true;
        }
    
        if ($longueurPWD1 > 25) {
            $array_Champs['champ_pwd_1_trop_long'] = true;
        }
    
        if ($longueurPWD2 > 25) {
            $array_Champs['champ_pwd_2_trop_long'] = true;
        }
    
        if ($array_Champs['champ_pwd_temp_trop_long'] || $array_Champs['champ_pwd_1_trop_long'] || $array_Champs['champ_pwd_2_trop_long']){
            $array_Champs['champs_pwd_trop_long'] = true;
        }
        // Section pour valider si il y a des caractères invalides dans les champs password
        $patternPass = "#^[0-9a-zA-Z]([0-9a-zA-Z]{0,23})[0-9a-zA-Z]$#";
    
        if (!preg_match($patternPass, $array_Champs['password_temp']) && !$array_Champs['champ_pwd_temp_empty']) {
            $array_Champs['champ_pwd_temp_invalid'] = true;
        }
    
        if (!preg_match($patternPass, $array_Champs['pwd_1_new']) && !$array_Champs['champ_pwd_1_empty']) {
            $array_Champs['champ_pwd_1_invalid'] = true;
        }
        if (!preg_match($patternPass, $array_Champs['pwd_2_new']) && !$array_Champs['champ_pwd_2_empty']) {
            $array_Champs['champ_pwd_2_invalid'] = true;
        }
    
        if ($array_Champs['champ_pwd_temp_invalid'] || $array_Champs['champ_pwd_1_invalid'] || $array_Champs['champ_pwd_2_invalid']){
            $array_Champs['champs_pwd_invalid'] = true;
        }
        
        return $array_Champs;
    }
    
    function situation($array_Champs){
        $typeSituation = 0;
        if ($array_Champs['champ_pwd_temp_empty'] && $array_Champs['champ_pwd_1_empty'] && $array_Champs['champ_pwd_2_empty']) {
            $typeSituation = 1;
        } elseif ($array_Champs['champ_pwd_temp_none_equal'] && !$array_Champs["champ_pwd_new_none_equal"] && !$array_Champs['champs_pwd_empty'] && !$array_Champs['champs_pwd_invalid'] && !$array_Champs['pwd_old_new_diff']){
            $typeSituation = 2;
        } elseif (!$array_Champs['champ_pwd_temp_none_equal'] && $array_Champs["champ_pwd_new_none_equal"] && !$array_Champs['champs_pwd_empty'] && !$array_Champs['champs_pwd_invalid'] && !$array_Champs['pwd_old_new_diff']){
            $typeSituation = 3;
        } elseif (!$array_Champs['champ_pwd_temp_none_equal'] && $array_Champs['champ_pwd_1_empty'] && $array_Champs['champ_pwd_2_empty']){
            $typeSituation = 4;
        } elseif ($array_Champs['champ_pwd_temp_empty'] && !$array_Champs["champ_pwd_new_none_equal"] && !$array_Champs['champs_pwd_invalid'] && !$array_Champs['pwd_old_new_diff']){
            $typeSituation = 5;
        } elseif (!$array_Champs['champ_pwd_temp_none_equal'] && $array_Champs['champs_pwd_empty']){
            $typeSituation = 6;
        } elseif (!$array_Champs["champs_pwd_empty"] && !$array_Champs["champs_pwd_none_equal"] && $array_Champs["token_time_expired"] && !$array_Champs["champs_pwd_trop_long"] && !$array_Champs["champs_pwd_invalid"]){
            $typeSituation = 7;
        } elseif (!$array_Champs['pwd_old_new_diff'] && !$array_Champs['champ_pwd_1_empty'] && !$array_Champs['champ_pwd_2_empty'] && !$array_Champs['champ_pwd_temp_none_equal']){
            $typeSituation = 12;
        } elseif ($array_Champs['create_user_succes'] && $array_Champs['pwd_old_new_diff']){
            $typeSituation = 8;
        } elseif ($array_Champs['champ_pwd_temp_none_equal'] && $array_Champs["champ_pwd_new_none_equal"] && !$array_Champs['champs_pwd_empty'] && !$array_Champs['champs_pwd_invalid']){
            $typeSituation = 9;
        } elseif (!$array_Champs['champ_pwd_temp_none_equal'] && $array_Champs['champs_pwd_invalid']){
            $typeSituation = 10;
        } elseif ($array_Champs['champ_pwd_temp_none_equal'] && $array_Champs['champs_pwd_invalid']){
            $typeSituation = 11;
        } elseif ($array_Champs['erreur_manip_bd']){
            $typeSituation = 13;
        }
    
        return $typeSituation;
    }
    
    function verif_link_BD($array_Champs, $connMYSQL){
        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("select reset_link from login where reset_link =? ");
    
        /* Lecture des marqueurs */
        $stmt->bind_param("s", $array_Champs["lien_crypter"]);
    
        /* Exécution de la requête */
        $stmt->execute();
    
        /* Association des variables de résultat */
        $result = $stmt->get_result();
    
        // Close statement
        $stmt->close();
    
        if ($result->num_rows == 1){
            $array_Champs["lien_crypter_good"] = true;
        }
        return $array_Champs;
    }
    
    function changementPassword($array_Champs, $connMYSQL){
        // Remise à NULL pour les
        $newPWDencrypt = encryptementPassword($array_Champs["pwd_1_new"]);
    
        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("update login set password =? , reset_link =? , passwordTemp =?, temps_Valide_link =? where reset_link =? ");
        /* Lecture des marqueurs */
        $zero = 0; // Je dois créer une variable qui va contenir la valeur 0
        $stringVide = NUll;
        $stmt->bind_param("sssis", $newPWDencrypt,$stringVide,$stringVide,$zero,$array_Champs["lien_crypter"]);
        /* Exécution de la requête */
        $status = $stmt->execute();
    
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        } else {
            $array_Champs['create_user_succes'] = true;
            // Remise à leur valeur initial, car le changement de mot de passe est terminé et le lien n'est plus valide
            $array_Champs['password_temp'] = "";
            $array_Champs['pwd_1_new'] = "";
            $array_Champs['pwd_2_new'] = "";
            $array_Champs['lien_crypter'] = "";
            $array_Champs['token_time_used'] = 0;
        }
    
        /* close statement and connection */
        $stmt->close();
    
        return $array_Champs;
    }
    
    function encryptementPassword($password_temp) {
        $password_Encrypted = password_hash($password_temp, PASSWORD_BCRYPT);
        return $password_Encrypted;
    }
    
    function redirection($array_Champs) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            if ($array_Champs["invalid_language"] || !$array_Champs["lien_crypter_good"]) {
                header("Location: /erreur/erreur.php");
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Les deux premiers IF sont pour la page d'acceuil
            // Les deux derniers IF sont pour la page login
            if (isset($_POST['return']) && $array_Champs["type_langue"] == "francais") {
                header("Location: /index.html");
            } elseif (isset($_POST['return']) && $array_Champs["type_langue"] == "english") {
                header("Location: /english/english.html");
            } elseif (isset($_POST['page_Login']) && $array_Champs["type_langue"] == "francais") {
                header("Location: /login/login.php?langue=francais");
            } elseif (isset($_POST['page_Login']) && $array_Champs["type_langue"] == "english") {
                header("Location: /login/login.php?langue=english");
            } elseif (!$array_Champs["lien_crypter_good"] || $array_Champs["invalid_language"]){
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
        if ($array_Champs["invalid_language"]){
            redirection($array_Champs);
        } elseif (!$array_Champs["lien_crypter_good"]){
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
        if ($array_Champs["invalid_language"]){
            redirection($array_Champs);
        } elseif (isset($_POST['return']) || isset($_POST['page_Login'])){
            redirection($array_Champs);
        } elseif (isset($_POST['create_New_PWD'])){
            $array_Champs = verif_link_BD($array_Champs, $connMYSQL);
            if (!$array_Champs["lien_crypter_good"]) {
                redirection($array_Champs);
            } else {
                $array_Champs = verifChamp($array_Champs, $connMYSQL);
    
                if (!$array_Champs["champs_pwd_empty"] && !$array_Champs["champs_pwd_none_equal"] && !$array_Champs["token_time_expired"] && !$array_Champs["champs_pwd_trop_long"] && !$array_Champs["champs_pwd_invalid"] && $array_Champs["pwd_old_new_diff"]){
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
            <fieldset class="<?php if ($array_Champs['create_user_succes']) { echo "changerAvecSucces"; } ?>">
                <legend class="legendCenter"><?php echo $arrayMots['legend']; ?></legend>
                <form method="post" action="./reset.php">
                    <div class="connexion">
                        <div class="information <?php if ($array_Champs['champ_pwd_temp_invalid'] || $array_Champs['champ_pwd_temp_trop_long'] || $array_Champs["champ_pwd_temp_empty"] || $array_Champs['champ_pwd_temp_none_equal']) { echo 'erreur';} ?>">
                            <label for="password_temp"><?php echo $arrayMots['mdp_Temp']; ?></label>
                            <div>
                                <input <?php if ($array_Champs['create_user_succes']) { echo "disabled"; } ?> autofocus id="password_temp" type='password' maxlength="25" name="password_temp" value="<?php echo $array_Champs['password_temp']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if (( $_SERVER['REQUEST_METHOD'] === 'POST' && !$array_Champs['pwd_old_new_diff'] ) || $array_Champs['champ_pwd_1_invalid'] || $array_Champs['champ_pwd_1_trop_long'] || $array_Champs["champ_pwd_1_empty"] || $array_Champs["champ_pwd_new_none_equal"]) { echo 'erreur';} ?>">
                            <label for="pwd_1_new"><?php echo $arrayMots['mdp_1']; ?></label>
                            <div>
                                <input <?php if ($array_Champs['create_user_succes']) { echo "disabled"; } ?> id="pwd_1_new" type='password' maxlength="25" name="pwd_1_new" value="<?php echo $array_Champs['pwd_1_new']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if ( ( $_SERVER['REQUEST_METHOD'] === 'POST' && !$array_Champs['pwd_old_new_diff'] ) || $array_Champs['champ_pwd_2_invalid'] || $array_Champs['champ_pwd_2_trop_long'] || $array_Champs["champ_pwd_2_empty"] || $array_Champs["champ_pwd_new_none_equal"]) { echo 'erreur';} ?>">
                            <label for="pwd_2_new"><?php echo $arrayMots['mdp_2']; ?></label>
                            <div>
                                <input <?php if ($array_Champs['create_user_succes']) { echo "disabled"; } ?> id="pwd_2_new" type='password' maxlength="25" name="pwd_2_new" value="<?php echo $array_Champs['pwd_2_new']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                    </div>
                    <div class="troisBTN">
                        <input <?php if ($array_Champs['create_user_succes']) { echo "class=\"bouton disabled\" disabled"; } else { echo "class=\"bouton\""; }?> type='submit' name='create_New_PWD' value="<?php echo $arrayMots['btn_create_New_PWD']; ?>">
                        <input type='hidden' name='type_langue' value="<?php echo $array_Champs['type_langue']; ?>">
                        <input type='hidden' name='lien_crypter' value="<?php echo $array_Champs['lien_crypter']; ?>">
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
                    <input type='hidden' name='type_langue' value="<?php echo $array_Champs['type_langue']; ?>">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
