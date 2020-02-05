<?php
function initialChamp() {
    $champs = ["champVide" => false, "champVideUser" => false, "champVidePassword" => false, "champVideName" => false, "duplicatUser" => false, "invalidUser" => false, "invalidPassword" => false, 
                     "invalidName" => false, "badUser" => false, "champTropLong" => false, "badPassword" => false, "sameUserPassword" => false, "invalidInformation" => false,
                     "champUserTropLong" => false, "champPasswordTropLong" => false, "champNameTropLong" => false, "password" => "", "situation" => 0, "user" => "", "typeLangue" => "", "name" => "", "creationUserSuccess" => false];
    return $champs;
}

function traduction($champs) {
    $message = "";
    if ($champs["typeLangue"] === 'francais') {
        $lang = "fr";
        $title = "Connexion de gestion";
        $p1 = "Bienvenue à la page de connexion d'un organisateur pour ses tournois.";
        $li1 = "Vous devez vous authentifiez, si vous voulez gérer vos informations pour vos tournois.";
        $li2 = "Lors de la création de votre nom d'utilisateur, ce dernier doit être unique.";
        $li3 = "Votre nom d'organisateur doit être reconnaissable mais ne doit pas être égal au nom utilisateur.";        
        $legend = "Connexion !";
        $usager = "Nom d'utilisateur : ";
        $name = "Nom d'organisateur : ";
        $mdp = "Mot de passe : ";
        $emailInfo = "Pour créer un compte seulement !";
        $btn_login = "Se Connecter";
        $btn_signUp = "S'inscrire";
        $btn_Erase = "Effacer";
        $btn_return = "Page d'Accueil";
        // Si la valeur de la situation est différent de zéro, nous attribuons le message correspondance et nous affichons à son endroit prévu
        if ($champs['situation'] !== 0) {
            switch ($champs['situation']) {
                case 1 : $message = "Toutes les champs ont été réinitialisées !";
                    break;
                case 2 : $message = "Votre utilisateur &rarr; {$champs['user']} &larr; n'existe pas !";
                    break;
                case 3 : $message = "Vous n'avez pas saisie le bon mot de passe avec votre utilisateur &rarr; {$champs['user']} &larr; !<br>
                         Si vous avez oublié votre mot de passe, Veuillez recréer un nouvel utilisateur pour l'instant et nous avertir via notre page d'accueuil dans la section «Nous joindre» pour détruire votre ancien nom utilisateur.";
                    break;
                case 4 : $message = "Attention l'utilisateur &rarr; {$champs['user']} &larr; est déjà utilisé par quelqu'un d'autre !";
                    break;
                case 5 : $message = "Oh ! Votre utilisateur &rarr; {$champs['user']} &larr; a bien été crée avec succès !";
                    break;
                case 6 : $message = "Attention vous avez oublié d'inscrire votre mot de passe avec votre user &rarr; {$champs['user']} &larr;";
                    break;
                case 7 : $message = "Attention le nom de l'organisateur ne peut être vide au moment de créer l'utilisateur !";
                    break;
                case 8 : $message = "Attention la longueur permise pour l'utilisateur est de 15 et pour le mot de passe de 25 caracteres !";
                    break;
                case 9 : $message = "Attention les champs peuvent contenir seulement des caractères alphanumériques !";
                    break;
                case 10 : $message = "Attention le nom d'utilisateur et le mot de passe doivent être différent !";
                    break;
                case 11 : $message = "Attention tous les champs sont vides au moment de créer l'utilisateur !";
                    break;
                case 12 : $message = "Attention le nom d'utilisateur et le mot de passe ne peuvent être vide au moment de se connecter !";
                    break;
                case 13 : $message = "Attention le mot de passe et nom de l'organisateur ne peuvent être vide au moment de créer l'utilisateur !";
                    break;
                case 14 : $message = "Félicitation ! Votre compte organisateur a été crée avec succès !";
                    break;    
            }
        }
    } elseif ($champs["typeLangue"] === 'english') {
        $title = "Connection";
        $lang = "en";
        $p1 = "Welcome to the login page of an promoter for his tournaments.";
        $li1 = "You must authenticate if you want to manage your information for your tournaments.";
        $li2 = "When creating your username, it must be unique.";
        $li3 = "Your promoter name must be recognizable but must not be equal to the username.";
        $legend = "Connection !";
        $usager = "Username : ";
        $emailInfo = "To create an username only!";
        $mdp = "Password : ";
        $name = "Promoter name : ";
        $btn_login = "Login";
        $btn_signUp = "Sign Up";
        $btn_Erase = "Erase";
        $btn_return = "Home page";
        if ($champs['situation'] !== 0) {
            switch ($champs['situation']) {
                case 1 : $message = "All fields have been reset !";
                    break;
                case 2 : $message = "Your username &rarr; {$champs['user']} &larr; doesn't exist !";
                    break;
                case 3 : $message = "You have not entered the good password with your user &rarr; {$champs['user']} &larr; !<br>
                            If you have forgotten your password, please recreate a new user at this time and notify us via our home page in the «Contact Us» section to delete your old username.";
                    break;
                case 4 : $message = "Warning ! The username &rarr; {$champs['user']} &larr; is already taken by someone else !";
                    break;
                case 5 : $message = "Your username &rarr; {$champs['user']} &larr; has been create with succes !";
                    break;
                case 6 : $message = "Warning ! You forgot to mention your password with your username &rarr; {$champs['user']} &larr;";
                    break;
                case 7 : $message = "Warning ! The promoter name can not be empty when creating the user !";
                    break;
                case 8 : $message = "Warning ! The length possible for user is 10 and for password is 25 caracters !";
                    break;
                case 9 : $message = "Warning ! The fields can only contain alphanumeric characters !";
                    break;
                case 10 : $message = "Warning ! The username and password must be different !";
                    break;
                case 11 : $message = "Warning ! All fields are empty when creating the user !";
                    break;
                case 12 : $message = "Warning ! The username and password fields can not be empty when logging in !";
                    break;
                case 13 : $message = "Warning ! The password and promoter name can not be empty when creating the user !";
                    break;
                case 14 : $message = "Congratulations ! Your organizer account has been successfully created !";
                    break; 
            }
        }
    }
    $arrayMots = ["lang" => $lang, 'emailInfo' => $emailInfo, 'title' => $title, 'p1' => $p1, 'li1' => $li1, 'li2' => $li2, 'li3' => $li3,
                  'legend' => $legend, 'name' => $name, 'usager' => $usager, 'mdp' => $mdp, 'btn_login' => $btn_login, 'btn_signUp' => $btn_signUp, 'btn_Erase' => $btn_Erase, 'btn_return' => $btn_return, 'message' => $message];
    return $arrayMots;
}

function verifChamp($champs) {
    if (empty($champs['user'])) {
        $champs['champVideUser'] = true;
    }
    if (empty($champs['password'])) {
        $champs['champVidePassword'] = true;
    }
    if ($champs['champVideUser'] && $champs['champVidePassword']){
        $champs['champVide'] = true;
    }

    if (empty($champs['name']) && isset($_POST['signUp']) ) {
        $champs['champVideName'] = true;
    }

    if ( strtolower($champs['password']) == $champs['user'] ){
        $champs['sameUserPassword'] = true;
    }

    $longueurUser = strlen($champs['user']);
    $longueurPassword = strlen($champs['password']);
    $longueurName = strlen($champs['name']);
    if ($longueurUser > 15) {
        $champs['champUserTropLong'] = true;
    }

    if ($longueurPassword > 25){
        $champs['champPasswordTropLong'] = true;
    }

    if ($longueurName > 30){
        $champs['champNameTropLong'] = true;
    }

    // ajout du underscore pour le user name
    $patternUser = "#^[0-9a-z]([0-9a-z_]{0,13})[0-9a-z]$#";
    if (!preg_match($patternUser, $champs['user']) && !$champs['champVideUser']) {
        $champs['invalidUser'] = true;
    }

    $patternPass = "#^[0-9a-zA-Z]([0-9a-zA-Z]{0,23})[0-9a-zA-Z]$#";
    if (!preg_match($patternPass, $champs['password']) && !$champs['champVidePassword']) {
        $champs['invalidPassword'] = true;
    }

    $patternName = "#^[A-Za-z]([a-zA-Z- _]{0,13})[a-z]$#";
    if (!preg_match($patternName, $champs['name']) && isset($_POST['signUp']) ) {
        $champs['invalidName'] = true;
    }    

    if ($champs['invalidUser'] || $champs['invalidPassword'] || $champs['invalidName']){
        $champs['invalidInformation'] = true;        
    }

    if ($champs['champUserTropLong'] || $champs['champPasswordTropLong'] || $champs['champNameTropLong']){
        $champs['champTropLong'] = true;        
    }

    return $champs;
}

function situation($champs) {
    $typeSituation = 0;
    if (isset($_POST['reset'])) {
        //echo 1;
        $typeSituation = 1;   
    } elseif ($champs['badUser']) {
        $typeSituation = 2;
        //echo 2;
    } elseif ($champs['badPassword'] && !$champs['champVidePassword']) {
        $typeSituation = 3;
        //echo 3;
    } elseif ($champs['duplicatUser']) {
        $typeSituation = 4;
        //echo 4;
        // La situatino 5 a été corrigé le 2018-10-03 , j'avais oublié d'ajouter la condition «sameUserPassword» 
    } elseif ($champs['user'] !== "" && !$champs['duplicatUser'] && !$champs['champVide'] && 
              !$champs['invalidInformation'] && !$champs['champVideName'] && !$champs['sameUserPassword']) {
        $typeSituation = 5;
        //echo 5;
    } elseif (!$champs['champVideUser'] && $champs['champVidePassword'] && !$champs['badUser'] && isset($_POST['login'])) {
        $typeSituation = 6;
        //echo 6;
    } elseif ($champs['champVideName'] && !$champs['champVidePassword'] && isset($_POST['signUp']) ) {
        $typeSituation = 7;
        //echo 7;
    } elseif ($champs['champTropLong']) {
        $typeSituation = 8;
        //echo 8;
    } elseif ($champs['invalidInformation'] && !$champs['champVideName'] && !$champs['champVide'] ) {
        $typeSituation = 9;
        //echo 9;
    } elseif ($champs['sameUserPassword'] && isset($_POST['signUp']) && !$champs['champVideName'] && !$champs['champVide']) {
        $typeSituation = 10;
        //echo 10;
    } elseif ($champs['champVideName'] && $champs['champVide'] && isset($_POST['signUp']) ){
        $typeSituation = 11;
        //echo 11;
    } elseif ($champs['champVide'] && isset($_POST['login'])){
        $typeSituation = 12;
        //echo 12;
    } elseif (!$champs['champVideUser'] && $champs['champVideName'] && $champs['champVideName'] && isset($_POST['signUp'])){
        $typeSituation = 13;
        //echo 13;
    } elseif ($champs['creationUserSuccess'] && isset($_POST['signUp'])){
        $typeSituation = 14;
    }



    return $typeSituation; 
}

function creationUser($champs, $connMYSQL) {
    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare("select user from login_organisateur where user =? ");

    /* Lecture des marqueurs */
    $stmt->bind_param("s", $champs['user']);

    /* Exécution de la requête */
    $stmt->execute();

    /* Association des variables de résultat */
    $result = $stmt->get_result();
    $row_cnt = $result->num_rows;

    /* close statement and connection */
    $stmt->close();    

    if ($row_cnt == 1) {
        $champs['duplicatUser'] = true;
        return $champs;
    } else {
        $passwordCrypter = encryptementPassword($champs['password']);
        $nameFormate = ucfirst($champs['name']); 

        // Prepare an insert statement
        $sql = "INSERT INTO login_organisateur (user, password, name) VALUES (?,?,?)";
        $stmt = $connMYSQL->prepare($sql);

        // Bind variables to the prepared statement as parameters
        $stmt->bind_param('sss', $champs['user'], $passwordCrypter, $nameFormate);
        $stmt->execute();

        if ($stmt->affected_rows == 1){
            $champs['creationUserSuccess'] = true;
        }    

        // Close statement
        $stmt->close();

        return $champs;
    }
}

function encryptementPassword(string $password) {
    $options = ['cost' => 11, 'salt' => random_bytes(22)];
    $passwordCrypter = password_hash($password, PASSWORD_BCRYPT, $options);
    return $passwordCrypter;
}

function connexionUser($champs, $connMYSQL) {
    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare("select user, password from login_organisateur where user =? ");

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
            header("Location: ./organisateur/organisateur.php");
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

    /*
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
        if (isset($_POST['name'])){
            $champs["name"] = $_POST['name'];
        }
        $connMYSQL = connexionBD();

        if (isset($_POST['login'])) {
            $champs = verifChamp($champs);
            if (!$champs['champVideUser'] && !$champs["champTropLong"] && !$champs["invalidInformation"]) {
                $champs = connexionUser($champs, $connMYSQL);
            }            
        } elseif (isset($_POST['signUp'])) {
            $champs = verifChamp($champs);
            if (!$champs['sameUserPassword'] && !$champs["champVideName"] && !$champs["champVide"] && !$champs["champTropLong"] && !$champs["invalidInformation"]) {
                $champs = creationUser($champs, $connMYSQL);

            }            
        } elseif (isset($_POST['reset'])) {
            $champs = initialChamp();
            $champs["typeLangue"] = $_POST['langue'];
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
    <meta name="description" content="Page de gestion">
    <!-- Le fichier login.png est la propriété du site https://pixabay.com/fr/ic%C3%B4nes-symboles-bouton-842844/ mais en utilisation libre-->
    <link rel="shortcut icon" href="login_Organisateur.png">
    <link rel="stylesheet" type="text/css" href="login_Organisateur.css">
    <title><?php echo $arrayMots['title']; ?></title>
    <style>
        body {
            margin: 0;
            /* Fichier photoPoker.jpg est une propriété du site https://pixabay.com/fr/syst%C3%A8me-r%C3%A9seau-actualit%C3%A9s-connexion-2457651/ sous licence libre */
            background-image: url("login_Organisateur.jpg");
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
                <legend class="legendCenter"><?php echo $arrayMots['legend']; ?></legend>
                <form method="post" action="./login_Organisateur.php">
                    <div class="connexion">
                        <div class="information <?php if ($champs['champVideUser'] || $champs['invalidUser'] || $champs['duplicatUser'] || $champs['badUser'] || $champs['champUserTropLong'] || $champs['sameUserPassword']) { echo 'erreur'; } ?>">
                            <label for="user"><?php echo $arrayMots['usager']; ?></label>
                            <input id="user" type="text" name="user" maxlength="15" value="<?php echo $champs['user']; ?>" />
                        </div>
                        <div class="information <?php if ($champs['champVidePassword'] || $champs['badUser'] || $champs['invalidPassword'] || $champs['badPassword'] || $champs['champPasswordTropLong'] || $champs['sameUserPassword']) { echo 'erreur'; } ?>">
                            <label for="password"><?php echo $arrayMots['mdp']; ?></label>
                            <input id="password" type='password' maxlength="25" name="password" value="<?php echo $champs['password']; ?>" />
                        </div>
                        <div class="information <?php if ($champs['champVideName'] || $champs['invalidName'] || $champs['champNameTropLong']) { echo 'erreur'; } ?>">
                            <label for="name"><?php echo $arrayMots['name']; ?></label>
                            <input placeholder="<?php echo $arrayMots['emailInfo']; ?>" id="name" type="text" name="name" maxlength="30" value="<?php echo $champs['name']; ?>" />
                        </div>
                    </div>
                    <div class="troisBTN">
                        <input class="bouton" type='submit' name='login' value="<?php echo $arrayMots['btn_login']; ?>">
                        <input class="bouton" type='submit' name='signUp' value="<?php echo $arrayMots['btn_signUp']; ?>">
                        <input class="bouton" type='submit' name='reset' value="<?php echo $arrayMots['btn_Erase']; ?>">
                        <input type='hidden' name='langue' value="<?php echo $champs['typeLangue']; ?>">
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="footer">
            <div class='avert                    		
                            <?php if ($champs['situation'] !== 1 && $champs['situation'] !== 5) { echo 'erreur'; } ?>'>
                <p> <?php echo $arrayMots['message']; ?> </p>
            </div>
            <div class="btnRetour">
                <form method="post" action="./login_Organisateur.php">
                    <input class="bouton" type="submit" name="return" value="<?php echo $arrayMots['btn_return']; ?>">
                    <input type='hidden' name='langue' value="<?php echo $champs['typeLangue']; ?>">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
