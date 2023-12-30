<?php
include("../traduction/traduction_login.php");

// il va falloir ajouter une valid duplicateEmail
function initialChamp() {
    $champs = ["champVide" => false, "champVideUser" => false, "champVidePassword" => false, "champVideEmail" => false, "duplicate" => false, "duplicatUser" => false, "duplicatEmail" => false, "champInvalid" => false, 
    "champInvalidUser" => false, "champInvalidPassword" => false, "champInvalidEmail" => false, "badUser" => false, "champTropLong" => false, "champTropLongUser" => false, "champTropLongPassword" => false, "champTropLongEmail" => false, "badPassword" => false, "creationUserSuccess" => false, "password" => "", "situation" => 0, "email" => "", "user" => "", "typeLangue" => "", "sameUserPWD" => false, "idCreationUser" => 0];
    return $champs;
}

function verifChamp($champs, $connMYSQL) {
    if (isset($_POST['signUp']) || isset($_POST['login'])){
        $champs["user"] = strtolower($_POST['user']);
        $champs["password"] = $_POST['password'];
    }

    if (isset($_POST['signUp'])){
        $champs["email"] = $_POST['email'];
    }

    if (empty($champs['user'])){
        $champs['champVideUser'] = true;
    }

    if (empty($champs['password'])){
        $champs['champVidePassword'] = true;
    }

    // Cette validation doit exclure si on pèse sur le bouton login
    if (empty($champs['email']) && isset($_POST['signUp'])){
        $champs['champVideEmail'] = true;
    } 

    // Simplification des champs vide pour plutard...
    if (($champs['champVideUser'] || $champs['champVidePassword'] || $champs['champVideEmail'])){
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

    if ($longueurEmail > 50 && isset($_POST['signUp']) ){
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
    if (!preg_match($patternEmail, $champs['email']) && isset($_POST['signUp'])) {
        $champs['champInvalidEmail'] = true; 
    } 

    // Ajout de cette sécurité / 5 Février 2020
    // https://stackoverflow.com/questions/11952473/proper-prevention-of-mail-injection-in-php/11952659#11952659
    if (!(filter_var($champs['email'], FILTER_VALIDATE_EMAIL)) && isset($_POST['signUp']) ){
        $champs['champInvalidEmail'] = true; 
    }

    if (($champs['champInvalidUser'] || $champs['champInvalidPassword'] || $champs['champInvalidEmail'])){
        $champs['champInvalid'] = true;
    }     

    if (!$champs['champVideUser'] && !$champs['champVidePassword'] && $champs['user'] == $champs['password']){
        $champs['sameUserPWD'] = true;
    }

    // Instauration de la validation si le user et ou email est dejà existant seulement si on veut créer un user 
    if (isset($_POST['signUp'])){  
        // Retourner un message erreur si la BD a eu un problème !

        // Optimisation pour avoir directement la valeur qui nous intéreste
        $stmt = $connMYSQL->prepare("select user, email from login where user =? OR email =? ");

        /* Lecture des marqueurs */
        $stmt->bind_param("ss", $champs['user'], $champs['email']);

        /* Exécution de la requête */
        $stmt->execute();

        /* Association des variables de résultat */ 
        $result = $stmt->get_result();    
        $row_cnt = $result->num_rows;

        // Close statement
        $stmt->close();

        $row_cnt = $result->num_rows; // si il y a des résultats, on va vérifier lequeles est un duplicate
        if ($row_cnt !== 0){
            foreach ($result as $row) {
                if ($row['user'] === $champs['user']) {
                    $champs['duplicatUser'] = true;
                }

                if ($row['email'] === $champs['email']) {
                    $champs['duplicatEmail'] = true;
                }
            }
        }
    }

    if ($champs['duplicatEmail'] || $champs['duplicatUser']){
        $champs['duplicate'] = true;
    }

    return $champs;
}

// Ajouter une situation ou plusieurs si le email est deja utilise par quelqu'un autre
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
    } elseif ($champs['duplicatEmail'] && isset($_POST['signUp'])) {
        $typeSituation = 18;         
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
    } elseif (!$champs['creationUserSuccess'] && isset($_POST['signUp']) ) {
        $typeSituation = 34; 
    }       
    return $typeSituation; // on retourne seulement un numéro qui va nous servicer dans la fct traduction()
}

function creationUser($champs, $connMYSQL) {
    $passwordCrypter = encryptementPassword($champs['password']);
    // Prepare an insert statement
    $sql = "INSERT INTO login (user, password, email) VALUES (?,?,?)";
    $stmt = $connMYSQL->prepare($sql);

    // Bind variables to the prepared statement as parameters
    $stmt->bind_param('sss', $champs['user'], $passwordCrypter, $champs['email']);
    $stmt->execute();

    if ($stmt->affected_rows == 1){
        $champs['creationUserSuccess'] = true;
    }    

    // Close statement
    $stmt->close();
    return $champs;
}

// Selon une recommandation :
// https://stackoverflow.com/questions/30279321/how-to-use-password-hash
// On ne doit pas jouer avec le salt....
function encryptementPassword(string $password) {
    $passwordCrypter = password_hash($password, PASSWORD_BCRYPT);
    return $passwordCrypter;
}

function connexionUser($champs, $connMYSQL) {
    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare("select user, password from login where user =? ");

    /* Lecture des marqueurs */
    $stmt->bind_param("s", $champs['user']);

    /* Exécution de la requête */
    $stmt->execute();

    /* Association des variables de résultat */
    $result = $stmt->get_result();
    $row = $result->fetch_array(MYSQLI_ASSOC);  
    $row_cnt = $result->num_rows;

    /* close statement and connection */
    $stmt->close();   

    if ($row_cnt == 1){
        if (password_verify($champs['password'], $row['password'])) {
            session_start();
            $_SESSION['user'] = $champs['user'];
            $_SESSION['password'] = $champs['password'];
            $_SESSION['typeLangue'] = $champs["typeLangue"];                
            date_default_timezone_set('America/New_York'); 
            // Je dois mettre ça si je veux avoir la bonne heure et date dans mon entrée de data
            // Je set un cookie pour améliorer la sécurité pour vérifier que l'user est bien là...2018-12-28
            setcookie("POKER", $_SESSION['user'], time() + 3600, "/");                
            $date = date("Y-m-d H:i:s");

            if ($row['user'] === "admin") {
                header("Location: ./statsPoker/administration/admin.php");
            } else {
                // Ici, on va saisir une entree dans la BD pour les autres users qui vont vers les statistiques 
                // Prepare an insert statement
                $sql = "INSERT INTO login_stat_poker (user,date,idCreationUser) VALUES (?,?,?)";
                $stmt = $connMYSQL->prepare($sql);                

                // Bind variables to the prepared statement as parameters
                $stmt->bind_param('ssi', $champs['user'], $date, $champs['idCreationUser']);
                $stmt->execute();                    

                // Close statement
                $stmt->close();
                header("Location: ./statsPoker/poker.php");
            }
            exit;
        } else {
            $champs['badPassword'] = true;
        }
    } else {
        $champs['badUser'] = true;
    }
    return $champs;
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
        $connMYSQL = connexionBD(); 
        $champs = verifChamp($champs, $connMYSQL);
        // Si le bouton se connecter est pesé...        
        if (isset($_POST['login'])) {
            // Comme j'ai instauré une foreign key entre la table login_stat_poker vers login je dois aller récupérer id pour l'insérer avec la nouvelle combinaison
            /* Crée une requête préparée */
            $stmt = $connMYSQL->prepare("select id from login where user =?");

            /* Lecture des marqueurs */
            $stmt->bind_param("s", $champs["user"]);

            /* Exécution de la requête */
            $stmt->execute();

            /* Association des variables de résultat */
            $result = $stmt->get_result();

            $row = $result->fetch_array(MYSQLI_ASSOC);  

            // Close statement
            $stmt->close();

            $champs["idCreationUser"] = $row["id"];	// Assignation de la valeur
            if (!$champs["champVide"] && !$champs["champTropLong"] && !$champs["champInvalid"] ) {
                $champs = connexionUser($champs, $connMYSQL);
            }
            // si le bouton s'inscrire est pesé...
        } elseif (isset($_POST['signUp'])) {          
            // Ajout de la validation si duplicate est à false en raison de unicité du user et email
            if (!$champs["champVide"] && !$champs["champTropLong"] && !$champs["champInvalid"] && !$champs['sameUserPWD'] && !$champs['duplicate']) {
                $champs = creationUser($champs, $connMYSQL);
            }            
            // si le bouton éffacer est pesé...
        } elseif (isset($_POST['reset'])) {
            if ($champs["typeLangue"] === 'english') {
                header("Location: /login/create_email_temp_pwd.php?langue=english");
            } elseif ($champs["typeLangue"] === 'francais') {
                header("Location: /login/create_email_temp_pwd.php?langue=francais");
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
<html lang="<?php echo $arrayMots['lang']; ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page de connexion">
    <!-- Le fichier login.png est la propriété du site https://pixabay.com/fr/ic%C3%B4nes-symboles-bouton-842844/ mais en utilisation libre-->
    <link rel="shortcut icon" href="login.png">
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
            <fieldset>
                <legend class="legend-center"><?php echo $arrayMots['legend']; ?></legend>
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
                                <input id="password" type='password' maxlength="25" name="password" value="<?php echo $champs['password']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if (!isset($_POST['login']) && ($champs['duplicatEmail'] || $champs['champVideEmail'] || $champs['champInvalidEmail'] || $champs['champTropLongEmail'])) { echo 'erreur';} ?>">
                            <label for="email"><?php echo $arrayMots['email']; ?></label>
                            <div>
                                <input placeholder="<?php echo $arrayMots['emailInfo']; ?>" id="email" type='email' maxlength="50" name="email" value="<?php echo $champs['email']; ?>" />
                                <span class="obligatoire">&nbsp;&nbsp;&nbsp;</span>
                            </div>
                        </div>
                    </div>
                    <div class="section-reset-btn">
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
            <div class="section-retour-btn">
                <form method="post" action="./login.php">
                    <input class="bouton" type="submit" name="return" value="<?php echo $arrayMots['btn_return']; ?>">
                    <input type='hidden' name='langue' value="<?php echo $champs['typeLangue']; ?>">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
