<?php
function initialChamp() {
    $champs = ["champVide" => false, "champVideUser" => false, "champVidePassword" => false, "champVideEmail" => false, "duplicatUser" => false, "champInvalid" => false,
               "champInvalidUser" => false, "champInvalidPassword" => false, "champInvalidEmail" => false, "badUser" => false, "champTropLong" => false, "champTropLongUser" => false, "champTropLongPassword" => false, "champTropLongEmail" => false, "badPassword" => false, "creationUserSuccess" => false,
               "password" => "", "situation" => 0, "email" => "", "user" => "", "typeLangue" => "", "sameUserPWD" => false, "idCreationUser" => 0];
    return $champs;
}

function traduction($champs) {    
    if ($champs["typeLangue"] === 'francais') {
        $title = "Connexion";
        $p1 = "Bienvenue à la page de connexion des statistiques du poker entre amis !";
        $li1 = "Vous devez vous authentifiez, pour faire afficher les statistiques désirées";
        $li2 = "Si vous n'avez pas de nom d'utilisateur, veuillez vous en créez un auparavant et ce dernier doit être unique.";
        $legend = "Connexion !";
        $email = "Courriel :";
        $emailInfo = "Pour créer un compte seulement !";
        $usager = "Nom d'utilisateur :";
        $mdp = "Mot de passe :";
        $btn_login = "Se Connecter";
        $btn_signUp = "S'inscrire";
        $btn_reset = "Mot de passe oublié ?";
        $btn_return = "Retour à l'accueil";

    } elseif ($champs["typeLangue"] === 'english') {
        $title = "Connection";
        $p1 = "Welcome to the login page to see the statistic of poker between friends !";
        $li1 = "You must login if you want to see the poker statistic.";
        $li2 = "If you do not have a username, please create one before and it must be unique.";
        $legend = "Connection !";
        $usager = "Username :";
        $mdp = "Password :";
        $btn_login = "Login";
        $email = "Email :";
        $btn_signUp = "Sign Up";
        $emailInfo = "To create an username only!";
        $btn_reset = "Forgot password ?";
        $btn_return = "Return to home page";        
    }

    $messageFinal = traductionSituation($champs);
    $arrayMots = ['emailInfo' => $emailInfo, 'title' => $title, 'email' => $email, 'p1' => $p1, 'li1' => $li1, 'li2' => $li2, 'legend' => $legend, 'usager' => $usager, 'mdp' => $mdp, 'btn_login' => $btn_login, 'btn_signUp' => $btn_signUp, 'btn_reset' => $btn_reset, 'btn_return' => $btn_return, 'message' => $messageFinal];
    return $arrayMots;
}

function traductionSituation($champs){
    $messageEnPreparation = "";
    if ($champs["typeLangue"] === 'francais') {
        $messageEnPreparation = traductionSituationFR($champs);
    } elseif ($champs["typeLangue"] === 'english') {
        $messageEnPreparation = traductionSituationEN($champs);
    }
    return $messageEnPreparation;
}

function traductionSituationFR($champs){
    $messageFrench = "";
    switch ($champs['situation']) {
        case 1 : $messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans les champs «Mot de passe» et «Courriel» !"; break; 
        case 2 : $messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans le champs «courriel» !"; break; 
        case 3 : $messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans le champs «nom d'utilisateur» !"; break;
        case 4 : $messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans le champs «mot de passe» !"; break;
        case 5 : $messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans les champs «nom d'utilisateur» et «mot de passe» !"; break;   
        case 6 : $messageFrench = "Au moment de créer votre compte, vous n'avez rien saisie dans les champs «nom d'utilisateur» et «courriel» !"; break;     
        case 7 : $messageFrench = "Au moment de vous connectez, vous n'avez rien saisie dans le champ «mot de passe» !"; break; 
        case 8 : $messageFrench = "Au moment de vous connectez, vous n'avez rien saisie dans le champ «nom d'utilisateur» !"; break; 
        case 9 : $messageFrench = "Au moment de vous connectez, le nom d'utilisateur saisie n'existe pas !"; break; 
        case 10 : $messageFrench = "Au moment de vous connectez, votre mot de passe saisie est invalide avec votre utilisateur !<br>
        Si vous avez oublié votre mot de passe, veuillez appuyer sur le bouton «Mot de passe oublié ?» et suivre les instructions."; break;
        case 11 : $messageFrench = "Au moment de créer votre compte, les champs «nom d'utilisateur» et «mot de passe» doivent être différent !"; break;
        case 12 : $messageFrench = "Au moment de créer votre compte, le nom d'utilisateur choisi est déjà utilisé par quelqu'un d'autre !"; break;
        case 13 : $messageFrench = "Attention les tous les champs sont vides !"; break;
        case 14 : $messageFrench = "Attention les longueurs permises en nombre de caractères pour les champs suivants sont :<br>
        «nom d'utilisateur» &rarr; 15<br> «mot de passe» &rarr; 25<br> «courriel» &rarr; 50 !"; break;
        case 15 : $messageFrench = "Attention les champs peuvent contenir seulement des caractères alphanumériques !"; break;
        case 16 : $messageFrench = "Félicitation ! Votre compte a été crée avec succès !"; break;
        case 17 : $messageFrench = "Attention le courriel ne respecte la forme standard soit : exemple@courriel.com !"; break;
    }
    return $messageFrench;
}

function traductionSituationEN($champs){
    $messageEnglish = "";
    switch ($champs['situation']) {
        case 1 : $messageEnglish = "At the time of creating your account, you have not entered anything in the fields «Password» and «Email» !"; break; 
        case 2 : $messageEnglish = "At the time of creating your account, you have not entered anything in the «Email» field !"; break; 
        case 3 : $messageEnglish = "At the time of creating your account, you have not entered anything in the «Username» field !"; break;
        case 4 : $messageEnglish = "At the time of creating your account, you have not entered anything in the «Password» field !"; break;
        case 5 : $messageEnglish = "At the time of creating your account, you have not entered anything in the fields «Username» and «Password» !"; break;   
        case 6 : $messageEnglish = "At the time of creating your account, you have not entered anything in the «Username» and «Email» fields ! "; break;     
        case 7 : $messageEnglish = "When you log in, you have not entered anything in the «Password» field !"; break; 
        case 8 : $messageEnglish = "When you log in, you have not entered anything in the «Username» field !"; break; 
        case 9 : $messageEnglish = "When you log in, the «username» entered does not exist !"; break; 
        case 10 : $messageEnglish = "When you log in, your «password» entered is invalid with your user !<br>
        If you have forgotten your password, please press the «Forgot password ?» button and follow the instructions."; break;
        case 11 : $messageEnglish = "When creating your account, the fields «Username» and «Password» must be different !"; break;
        case 12 : $messageEnglish = "When creating your account, the chosen «username» is already used by someone else !"; break;
        case 13 : $messageEnglish = "Be careful, all the fields are empty !"; break;
        case 14 : $messageEnglish = "Warning, the lengths allowed in number of characters for the following fields are :<br>
        «Username» &rarr; 15<br> «Password» &rarr; 25<br> «Email» &rarr; 50 !"; break;
        case 15 : $messageEnglish = "Warning, the fields can only contain alphanumeric characters !"; break;
        case 16 : $messageEnglish = "Congratulations ! Your account has been successfully created !"; break;
        case 17 : $messageEnglish = "Warning, the email does not respect the standard form : example@courriel.com !"; break;
    }
    return $messageEnglish;
}

function verifChamp($champs) {
    if (empty($champs['user'])){
        $champs['champVideUser'] = true;
    }

    if (empty($champs['password'])){
        $champs['champVidePassword'] = true;
    }

    // Cette validation doit exclure si on pèse sur le bouton login
    if (empty($champs['email']) && !isset($_POST['login'])){
        $champs['champVideEmail'] = true;
    } 

    // Simplification des champs vide pour plutard...
    if (($champs['champVideUser'] || $champs['champVidePassword'] || $champs['champVideEmail']) && !isset($_POST['login'])){
        $champs['champVide'] = true;
    }

    $longueurUser = strlen($champs['user']);
    $longueurPassword = strlen($champs['password']);
    $longueurEmail = strlen($champs['email']);

    if ($longueurUser > 15) {
        $champs['champTropLongUser'] = true;
    }

    if ($longueurPassword > 25){
        $champs['champTropLongPassword'] = true;
    }

    if ($longueurEmail > 50){
        $champs['champTropLongEmail'] = true;
    }

    // Simplification des champs trop long pour plutard...
    if ($champs['champTropLongUser'] || $champs['champTropLongPassword'] || $champs['champTropLongEmail']){
        $champs['champTropLong'] = true;
    }    

    // On ne doit pas avoir de caractères spéciaux dans l'username
    // ajout du underscore pour le user name
    $patternUser = "#^[0-9a-z]([0-9a-z_]{0,13})[0-9a-z]$#";
    if (!preg_match($patternUser, $champs['user'])) {
        $champs['champInvalidUser'] = true;
    }

    // On ne doit pas avoir de caractères spéciaux dans le mot de passe
    $patternPass = "#^[0-9a-zA-Z]([0-9a-zA-Z]{0,23})[0-9a-zA-Z]$#";
    if (!preg_match($patternPass, $champs['password'])) {
        $champs['champInvalidPassword'] = true;
    }

    $patternEmail = "#^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$#";    
    if (!preg_match($patternEmail, $champs['email']) && !isset($_POST['login'])) {
        $champs['champInvalidEmail'] = true; 
    }  

    // Simplification des champs invalides pour plutard...
    if (($champs['champInvalidUser'] || $champs['champInvalidPassword'] || $champs['champInvalidEmail']) && !isset($_POST['login'])){
        $champs['champInvalid'] = true;
    }     

    if (!$champs['champVideUser'] && !$champs['champVidePassword'] && $champs['user'] == $champs['password'] && !isset($_POST['login'])){
        $champs['sameUserPWD'] = true;
    }
    return $champs;
}

function situation($champs) {
    $typeSituation = 0;   
    // Début : Section où nous n'avons pas entré dans les fonctions creationUser et connexionUser
    if (!$champs['champVideUser'] && $champs['champVidePassword'] && $champs['champVideEmail'] && isset($_POST['signUp'])) {
        $typeSituation = 1; 
    } elseif (!$champs['champVideUser'] && !$champs['champVidePassword'] && $champs['champVideEmail'] && isset($_POST['signUp'])){
        $typeSituation = 2; 
    } elseif ($champs['champVideUser'] && !$champs['champVidePassword'] && !$champs['champVideEmail'] && isset($_POST['signUp'])){
        $typeSituation = 3;
    } elseif (!$champs['champVideUser'] && $champs['champVidePassword'] && !$champs['champVideEmail'] && isset($_POST['signUp'])){
        $typeSituation = 4; 
    } elseif ($champs['champVideUser'] && $champs['champVidePassword'] && !$champs['champVideEmail'] && isset($_POST['signUp'])){
        $typeSituation = 5; 
    } elseif ($champs['champVideUser'] && !$champs['champVidePassword'] && $champs['champVideEmail'] && isset($_POST['signUp'])){
        $typeSituation = 6; 
    } elseif (!$champs['champVideUser'] && $champs['champVidePassword'] && isset($_POST['login'])){
        $typeSituation = 7; 
    } elseif ($champs['champVideUser'] && !$champs['champVidePassword'] && isset($_POST['login'])){
        $typeSituation = 8; 
        // Fin : Section où nous n'avons pas entré dans les fonctions creationUser et connexionUser           
    } elseif ($champs['badUser'] && isset($_POST['login'])) { 
        $typeSituation = 9; 
    } elseif ($champs['badPassword'] && isset($_POST['login'])) {
        $typeSituation = 10;
    } elseif ($champs['sameUserPWD'] && isset($_POST['signUp'])) {
        $typeSituation = 11; 
    } elseif ($champs['duplicatUser'] && isset($_POST['signUp'])) {
        $typeSituation = 12; 
    } elseif ($champs['champVide']) {     
        $typeSituation = 13; 
    } elseif ($champs['champInvalidEmail']) {     
        $typeSituation = 17; 
    } elseif ($champs['champTropLong']) {
        $typeSituation = 14; 
    } elseif ($champs['champInvalid']) {
        $typeSituation = 15; 
    } elseif ($champs['creationUserSuccess'] && isset($_POST['signUp']) ) {
        $typeSituation = 16; 
    }        
    return $typeSituation; // on retourne seulement un numéro qui va nous servicer dans la fct traduction()
}

function creationUser($champs, $connMYSQL) {
    $sql = "select user from login";
    $result = $connMYSQL->query($sql);

    foreach ($result as $row) {
        if ($row['user'] === $champs['user']) {
            $champs['duplicatUser'] = true;
        }
    }

    if ($champs['duplicatUser']) {
        return $champs;
    } else {
        $passwordCrypter = encryptementPassword($champs['password']);
        // Ajout de l'information du email dans la création du user
        $insert = "INSERT INTO login (user, password, id, email, reset_link, passwordTemp, temps_Valide_link) VALUES ";
        $insert .= "('" . $champs['user'] . "','" . $passwordCrypter . "', NULL, '" . $champs['email'] . "', NULL, NULL, 0)";
        $connMYSQL->query($insert);
        if (mysqli_affected_rows($connMYSQL) == 1){
            $champs['creationUserSuccess'] = true;
        }
        return $champs;
    }
}
// Selon une recommandation :
// https://stackoverflow.com/questions/30279321/how-to-use-password-hash
// On ne doit pas jouer avec le salt....
function encryptementPassword(string $password) {
    $passwordCrypter = password_hash($password, PASSWORD_BCRYPT);
    return $passwordCrypter;
}

function connexionUser($champs, $connMYSQL) {
    $sql = "select user, password from login";
    $result = $connMYSQL->query($sql);

    foreach ($result as $row) {
        if ($row['user'] === $champs['user']) {
            if (password_verify($champs['password'], $row['password'])) {
                session_start();
                $_SESSION['user'] = $champs['user'];
                $_SESSION['password'] = $champs['password'];
                $_SESSION['typeLangue'] = $champs["typeLangue"];                
                date_default_timezone_set('America/New_York'); // Je dois mettre ça si je veux avoir la bonne heure et date dans mon entrée de data
                // Je set un cookie pour améliorer la sécurité pour vérifier que l'user est bien là...2018-12-28
                setcookie("POKER", $_SESSION['user'], time() + 3600, "/");                
                $date = date("Y-m-d H:i:s");

                if ($row['user'] === "admin") {
                    header("Location: ./statsPoker/administration/admin.php");
                } else {
                    // Ici, on va saisir une entree dans la BD pour les autres users qui vont vers les statistiques 
                    $insert = "INSERT INTO login_stat_poker (user,date,id_login,idCreationUser) VALUES ";
                    $insert .= "('" . $champs['user'] . "',
                                 '" . $date . "',
                                 NULL,
                                 '" . $champs['idCreationUser'] . "')";
                    $connMYSQL->query($insert);
                    header("Location: ./statsPoker/poker.php");
                }
                exit;
            } else {
                $champs['badPassword'] = true;
                return $champs;
            }
        }
    }
    $champs['badUser'] = true;
    return $champs;
}

function connexionBD() { 
    // Nouvelle connexion sur hébergement du Studio OL
    $host = "localhost";
    $user = "benoitmi_benoit";
    $password = "d-&47mK!9hjGC4L-";
    $bd = "benoitmi_benoitmignault.ca.mysql";

    /*
    $host = "localhost";
    $user = "zmignaub";
    $password = "Banane11";
    $bd = "benoitmignault_ca_mywebsite";
    */
    $connMYSQL = mysqli_connect($host, $user, $password, $bd);
    $connMYSQL->query("set names 'utf8'");
    return $connMYSQL;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $champs = initialChamp(); // est un tableau avec tous les flag erreurs possibles et les infos du user, pwd et le type de situation    
    $champs["typeLangue"] = $_GET['langue'];

    if ($champs["typeLangue"] != "francais" && $champs["typeLangue"] != "english") {
        header("Location: /erreur/erreur.php");
        exit;
    } else {
        $arrayMots = traduction($champs);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $champs = initialChamp();
    $champs["typeLangue"] = $_POST['langue'];
    if (isset($_POST['return'])) {
        if ($champs["typeLangue"] === 'english') {
            header("Location: /english/english.html");
        } elseif ($champs["typeLangue"] === 'francais') {
            header("Location: /index.html");
        }
        exit;
    } else {
        $champs["user"] = strtolower($_POST['user']);
        $champs["password"] = $_POST['password'];
        $champs["email"] = $_POST['email'];
        $connMYSQL = connexionBD();
        // Comme j'ai instauré une foreign key entre la table login_stat_poker vers login je dois aller récupérer id pour l'insérer avec la nouvelle combinaison
        $sql = "select id from login where user = '{$champs["user"]}' ";                
        $result_SQL = $connMYSQL->query($sql);
        $row = $result_SQL->fetch_row(); // C'est mon array de résultat
        $champs["idCreationUser"] = (int) $row[0];	// Assignation de la valeur 

        // Si le bouton se connecter est pesé...        
        if (isset($_POST['login'])) {
            $champs = verifChamp($champs);
            if (!$champs["champVide"] && !$champs["champTropLong"] && !$champs["champInvalid"] ) {
                $champs = connexionUser($champs, $connMYSQL);
            }
            // si le bouton s'inscrire est pesé...
        } elseif (isset($_POST['signUp'])) {
            $champs = verifChamp($champs);
            if (!$champs["champVide"] && !$champs["champTropLong"] && !$champs["champInvalid"] && !$champs['sameUserPWD']) {
                $champs = creationUser($champs, $connMYSQL);
            }            
            // si le bouton éffacer est pesé...
        } elseif (isset($_POST['reset'])) {
            if ($champs["typeLangue"] === 'english') {
                header("Location: /login/createLinkSendMail.php?langue=english");
            } elseif ($champs["typeLangue"] === 'francais') {
                header("Location: /login/createLinkSendMail.php?langue=francais");
            }
            exit;
        }
        $champs["situation"] = situation($champs); // Ici on va modifier la valeur de la variable situation pour faire afficher le message approprié
        $arrayMots = traduction($champs);  // Affichage des mots en français ou en anglais selon le paramètre du get de départ et suivi dans le post par la suite
    }
    $connMYSQL->close();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">	
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Page de connexion">
        <!-- Le fichier login.png est la propriété du site https://pixabay.com/fr/ic%C3%B4nes-symboles-bouton-842844/ mais en utilisation libre-->
        <link rel="shortcut icon" href="login.png">		
        <link rel="stylesheet" type="text/css" href="login.css"> 
        <title><?php echo $arrayMots['title']; ?></title> 
        <style>
            body{
                margin:0;    
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
                </ul>
                <fieldset>
                    <legend align="center"><?php echo $arrayMots['legend']; ?></legend>
                    <form method="post" action="./login.php">
                        <div class="connexion">                    
                            <div class="information <?php if ($champs['sameUserPWD'] || $champs['champVideUser'] || $champs['champInvalidUser'] || $champs['duplicatUser'] || $champs['badUser'] || $champs['champTropLongUser']) { echo 'erreur'; } ?>">
                                <label for="user"><?php echo $arrayMots['usager']; ?></label> 
                                <div>                                
                                    <input autofocus id="user" type="text" name="user" maxlength="15" value="<?php echo $champs['user']; ?>" />                                
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>                                
                            </div>
                            <div class="information <?php if ($champs['sameUserPWD'] || $champs['badPassword'] || $champs['champVidePassword'] || $champs['champInvalidPassword'] || $champs['champTropLongPassword']) { echo 'erreur';} ?>">
                                <label for="password"><?php echo $arrayMots['mdp']; ?></label>
                                <div>                                
                                    <input id="password" type='password' maxlength="25" name="password" value="<?php echo $champs['password']; ?>"/>
                                    <span class="obligatoire">&nbsp;*</span>
                                </div> 
                            </div>                             
                            <div class="information <?php if (!isset($_POST['login']) && ($champs['champVideEmail'] || $champs['champInvalidEmail'] || $champs['champTropLongEmail'])) { echo 'erreur';} ?>">
                                <label for="email"><?php echo $arrayMots['email']; ?></label>
                                <div>
                                    <input placeholder="<?php echo $arrayMots['emailInfo']; ?>" id="email" type='email' maxlength="50" name="email" value="<?php echo $champs['email']; ?>"/>
                                    <span class="obligatoire">&nbsp;&nbsp;&nbsp;</span>
                                </div>                                 
                            </div>
                        </div>
                        <div class="troisBTN">                         
                            <input class="bouton" type='submit' name='login' value="<?php echo $arrayMots['btn_login']; ?>">
                            <input class="bouton" type='submit' name='signUp' value="<?php echo $arrayMots['btn_signUp']; ?>">
                            <input class="bouton" type='submit' name='reset' value="<?php echo $arrayMots['btn_reset']; ?>">
                            <input type='hidden' name='langue' value="<?php echo $champs['typeLangue']; ?>">
                        </div>
                    </form> 
                </fieldset>
            </div>            
            <div class="footer">                
                <div class='avert <?php if ($champs["situation"] != 16) { echo 'erreur'; } ?>'>
                    <p> <?php echo $arrayMots['message']; ?> </p>
                </div>                
                <div class="btnRetour">
                    <form method="post" action="./login.php">
                        <input class="bouton" type="submit" name="return" value="<?php echo $arrayMots['btn_return']; ?>"> 
                        <input type='hidden' name='langue' value="<?php echo $champs['typeLangue']; ?>">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>