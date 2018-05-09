<?php
function initialChamp() {
    $champInitial = ["champVide" => false, "champVideUser" => false, "champVidePassword" => false, "champVideName" => false, "duplicatUser" => false, "invalidUser" => false, "invalidPassword" => false, 
                     "invalidName" => false, "badUser" => false, "champTropLong" => false, "badPassword" => false, "sameUserPassword" => false, "invalidInformation" => false,
                     "champUserTropLong" => false, "champPasswordTropLong" => false, "champNameTropLong" => false, "password" => "", "situation" => 0, "user" => "", "typeLangue" => "", "name" => ""];
    return $champInitial;
}

function traduction($champInitial) {
    $message = "";
    if ($champInitial["typeLangue"] === 'francais') {
        $title = "Connexion de gestion";
        $p1 = "Bienvenue à la page de connexion d'un organisateur pour ses tournois.";
        $li1 = "Vous devez vous authentifiez, si vous voulez gérer vos informations pour vos tournois.";
        $li2 = "Lors de la création de votre nom d'utilisateur, ce dernier doit être unique.";
        $li3 = "Votre nom d'organisateur doit être reconnaissable mais ne doit pas être égal au nom utilisateur.";        
        $legend = "Connexion !";
        $usager = "Nom d'utilisateur : ";
        $name = "Nom d'organisateur : ";
        $mdp = "Mot de passe : ";
        $btn_login = "Se Connecter";
        $btn_signUp = "S'inscrire";
        $btn_Erase = "Effacer";
        $btn_return = "Retour à l'acceuil";
        // Si la valeur de la situation est différent de zéro, nous attribuons le message correspondance et nous affichons à son endroit prévu
        if ($champInitial['situation'] !== 0) {
            switch ($champInitial['situation']) {
                case 1 : $message = "Toutes les champs ont été réinitialisées !";
                    break;
                case 2 : $message = "Votre utilisateur &rarr; {$champInitial['user']} &larr; n'existe pas !";
                    break;
                case 3 : $message = "Vous n'avez pas saisie le bon mot de passe avec votre utilisateur &rarr; {$champInitial['user']} &larr; !<br>
                         Si vous avez oublié votre mot de passe, Veuillez recréer un nouvel utilisateur pour l'instant et nous avertir via notre page d'accueuil dans la section «Nous joindre» pour détruire votre ancien nom utilisateur.";
                    break;
                case 4 : $message = "Attention l'utilisateur &rarr; {$champInitial['user']} &larr; est déjà utilisé par quelqu'un d'autre !";
                    break;
                case 5 : $message = "Oh ! Votre utilisateur &rarr; {$champInitial['user']} &larr; a bien été crée avec succès !";
                    break;
                case 6 : $message = "Attention vous avez oublié d'inscrire votre mot de passe avec votre user &rarr; {$champInitial['user']} &larr;";
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
            }
        }
    } elseif ($champInitial["typeLangue"] === 'english') {
        $title = "Connection";
        $p1 = "Welcome to the login page of an promoter for his tournaments.";
        $li1 = "You must authenticate if you want to manage your information for your tournaments.";
        $li2 = "When creating your username, it must be unique.";
        $li3 = "Your promoter name must be recognizable but must not be equal to the username.";
        $legend = "Connection !";
        $usager = "Username : ";
        $mdp = "Password : ";
        $name = "Promoter name : ";
        $btn_login = "Login";
        $btn_signUp = "Sign Up";
        $btn_Erase = "Erase";
        $btn_return = "Return to home page";
        if ($champInitial['situation'] !== 0) {
            switch ($champInitial['situation']) {
                case 1 : $message = "All fields have been reset !";
                    break;
                case 2 : $message = "Your username &rarr; {$champInitial['user']} &larr; doesn't exist !";
                    break;
                case 3 : $message = "You have not entered the good password with your user &rarr; {$champInitial['user']} &larr; !<br>
                            If you have forgotten your password, please recreate a new user at this time and notify us via our home page in the «Contact Us» section to delete your old username.";
                    break;
                case 4 : $message = "Warning ! The username &rarr; {$champInitial['user']} &larr; is already taken by someone else !";
                    break;
                case 5 : $message = "Your username &rarr; {$champInitial['user']} &larr; has been create with succes !";
                    break;
                case 6 : $message = "Warning ! You forgot to mention your password with your username &rarr; {$champInitial['user']} &larr;";
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
            }
        }
    }
    $arrayMots = ['title' => $title, 'p1' => $p1, 'li1' => $li1, 'li2' => $li2, 'li3' => $li3,
                  'legend' => $legend, 'name' => $name, 'usager' => $usager, 'mdp' => $mdp, 'btn_login' => $btn_login, 'btn_signUp' => $btn_signUp, 'btn_Erase' => $btn_Erase, 'btn_return' => $btn_return, 'message' => $message];
    return $arrayMots;
}

function verifChamp($champInitial) {
    if (empty($champInitial['user'])) {
        $champInitial['champVideUser'] = true;
    }
    if (empty($champInitial['password'])) {
        $champInitial['champVidePassword'] = true;
    }
    if ($champInitial['champVideUser'] && $champInitial['champVidePassword']){
        $champInitial['champVide'] = true;
    }

    if (empty($champInitial['name']) && isset($_POST['signUp']) ) {
        $champInitial['champVideName'] = true;
    }

    if ( strtolower($champInitial['password']) == $champInitial['user'] ){
        $champInitial['sameUserPassword'] = true;
    }

    $longueurUser = strlen($champInitial['user']);
    $longueurPassword = strlen($champInitial['password']);
    $longueurName = strlen($champInitial['name']);
    if ($longueurUser > 15) {
        $champInitial['champUserTropLong'] = true;
    }

    if ($longueurPassword > 25){
        $champInitial['champPasswordTropLong'] = true;
    }

    if ($longueurName > 15){
        $champInitial['champNameTropLong'] = true;
    }

    $patternUser = "#^[0-9a-z]([0-9a-z]{0,13})[0-9a-z]$#";
    if (!preg_match($patternUser, $champInitial['user']) && !$champInitial['champVideUser']) {
        $champInitial['invalidUser'] = true;
    }

    $patternPass = "#^[0-9a-zA-Z]([0-9a-zA-Z]{0,23})[0-9a-zA-Z]$#";
    if (!preg_match($patternPass, $champInitial['password']) && !$champInitial['champVidePassword']) {
        $champInitial['invalidPassword'] = true;
    }

    $patternName = "#^[A-Za-z]([a-z]{0,13})[a-z]$#";
    if (!preg_match($patternName, $champInitial['name']) && isset($_POST['signUp']) ) {
        $champInitial['invalidName'] = true;
    }    

    if ($champInitial['invalidUser'] || $champInitial['invalidPassword'] || $champInitial['invalidName']){
        $champInitial['invalidInformation'] = true;        
    }

    if ($champInitial['champUserTropLong'] || $champInitial['champPasswordTropLong'] || $champInitial['champNameTropLong']){
        $champInitial['champTropLong'] = true;        
    }

    return $champInitial;
}

function situation($champInitial) {
    $typeSituation = 0;
    if (isset($_POST['reset'])) {
        $typeSituation = 1;   
    } elseif ($champInitial['badUser']) {
        $typeSituation = 2;
    } elseif ($champInitial['badPassword'] && !$champInitial['champVidePassword']) {
        $typeSituation = 3;
    } elseif ($champInitial['duplicatUser']) {
        $typeSituation = 4;
    } elseif ($champInitial['user'] !== "" && !$champInitial['duplicatUser'] && !$champInitial['champVide'] && !$champInitial['invalidInformation'] && !$champInitial['champVideName']) {
        $typeSituation = 5;
    } elseif (!$champInitial['champVideUser'] && $champInitial['champVidePassword'] && !$champInitial['badUser'] && isset($_POST['login'])) {
        $typeSituation = 6;
    } elseif ($champInitial['champVideName'] && !$champInitial['champVidePassword'] && isset($_POST['signUp']) ) {
        $typeSituation = 7;
    } elseif ($champInitial['champTropLong']) {
        $typeSituation = 8;
    } elseif ($champInitial['invalidInformation'] && !$champInitial['champVideName'] && !$champInitial['champVide'] ) {
        $typeSituation = 9;
    } elseif ($champInitial['sameUserPassword'] && isset($_POST['signUp']) && !$champInitial['champVideName'] && !$champInitial['champVide']) {
        $typeSituation = 10;
    } elseif ($champInitial['champVideName'] && $champInitial['champVide'] && isset($_POST['signUp']) ){
        $typeSituation = 11;
    } elseif ($champInitial['champVide'] && isset($_POST['login'])){
        $typeSituation = 12;
    } elseif (!$champInitial['champVideUser'] && $champInitial['champVideName'] && $champInitial['champVideName'] && isset($_POST['signUp'])){
        $typeSituation = 13;
    }



    return $typeSituation; 
}

function creationUser($champInitial, $connMYSQL) {
    $sql = "select user from benoitmignault_ca_mywebsite.login_organisateur";
    $result = $connMYSQL->query($sql);
    foreach ($result as $row) {
        if ($row['user'] === $champInitial['user']) {
            $champInitial['duplicatUser'] = true;
            return $champInitial;
        }
    }
    $nameFormate = ucfirst($champInitial['name']); 
    $passwordCrypter = encryptementPassword($champInitial['password']);
    $insert = "INSERT INTO benoitmignault_ca_mywebsite.login_organisateur (user, password, name, id) VALUES ";
    $insert .= "('".$champInitial['user']."', '".$passwordCrypter."', '".$nameFormate."', NULL)";
    $connMYSQL->query($insert);

    return $champInitial;
}

function encryptementPassword(string $password) {
    $options = ['cost' => 11, 'salt' => random_bytes(22)];
    $passwordCrypter = password_hash($password, PASSWORD_BCRYPT, $options);
    return $passwordCrypter;
}

function connexionUser($champInitial, $connMYSQL) {
    $sql = "select user, password from benoitmignault_ca_mywebsite.login_organisateur";
    $result = $connMYSQL->query($sql);

    foreach ($result as $row) {
        if ($row['user'] === $champInitial['user']) {
            if (password_verify($champInitial['password'], $row['password'])) {
                session_start();
                $_SESSION['user'] = $champInitial['user'];
                $_SESSION['password'] = $champInitial['password'];
                $_SESSION['typeLangue'] = $champInitial["typeLangue"];
                header("Location: ./organisateur/organisateur.php");
                exit;   
            } else {
                $champInitial['badPassword'] = true;
                return $champInitial;
            }
        }
    }
    $champInitial['badUser'] = true;
    return $champInitial;
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $champInitial = initialChamp(); // est un tableau avec tous les flag erreurs possibles et les infos du user, pwd et le type de situation    
    $champInitial["typeLangue"] = $_GET['langue'];

    if ($champInitial["typeLangue"] != "francais" && $champInitial["typeLangue"] != "english") {
        header("Location: /erreur/erreur.php");
        exit;
    } else {
        $arrayMots = traduction($champInitial);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $champInitial = initialChamp();
    $champInitial["typeLangue"] = $_POST['langue'];
    if (isset($_POST['return'])) {
        if ($champInitial["typeLangue"] === 'english') {
            header("Location: /english/english.html");
        } elseif ($champInitial["typeLangue"] === 'francais') {
            header("Location: /index.html");
        }
        exit;
    } else {
        $champInitial["user"] = strtolower($_POST['user']);
        $champInitial["password"] = $_POST['password'];
        if (isset($_POST['name'])){
            $champInitial["name"] = $_POST['name'];
        }
        $connMYSQL = connexionBD();

        if (isset($_POST['login'])) {
            $champInitial = verifChamp($champInitial);
            if (!$champInitial['champVideUser'] && !$champInitial["champTropLong"] && !$champInitial["invalidInformation"]) {
                $champInitial = connexionUser($champInitial, $connMYSQL);
            }            
        } elseif (isset($_POST['signUp'])) {
            $champInitial = verifChamp($champInitial);
            if (!$champInitial['sameUserPassword'] && !$champInitial["champVideName"] && !$champInitial["champVide"] && !$champInitial["champTropLong"] && !$champInitial["invalidInformation"]) {
                $champInitial = creationUser($champInitial, $connMYSQL);

            }            
        } elseif (isset($_POST['reset'])) {
            $champInitial = initialChamp();
            $champInitial["typeLangue"] = $_POST['langue'];
        }
        $champInitial["situation"] = situation($champInitial); // Ici on va modifier la valeur de la variable situation pour faire afficher le message approprié
        $arrayMots = traduction($champInitial);  // Affichage des mots en français ou en anglais selon le paramètre du get de départ et suivi dans le post par la suite
    }
    $connMYSQL->close();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">	
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Page de gestion">
        <!-- Le fichier login.png est la propriété du site https://pixabay.com/fr/ic%C3%B4nes-symboles-bouton-842844/ mais en utilisation libre-->
        <link rel="shortcut icon" href="login_Organisateur.png">		
        <link rel="stylesheet" type="text/css" href="login_Organisateur.css"> 
        <title><?php echo $arrayMots['title']; ?></title> 
        <style>
            body{
                margin:0;    
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
                    <legend align="center"><?php echo $arrayMots['legend']; ?></legend>
                    <form method="post" action="./login_Organisateur.php">
                        <div class="connexion"> 
                            <div class="information <?php if ($champInitial['champVideUser'] || $champInitial['invalidUser'] || $champInitial['duplicatUser'] || $champInitial['badUser'] || $champInitial['champUserTropLong'] || $champInitial['sameUserPassword']) { echo 'erreur'; } ?>">
                                <label for="user"><?php echo $arrayMots['usager']; ?></label>
                                <input id="user" type="text" name="user" maxlength="15" value="<?php echo $champInitial['user']; ?>" />
                            </div>
                            <div class="information <?php if ($champInitial['champVidePassword'] || $champInitial['badUser'] || $champInitial['invalidPassword'] || $champInitial['badPassword'] || $champInitial['champPasswordTropLong'] || $champInitial['sameUserPassword']) { echo 'erreur'; } ?>">
                                <label for="password"><?php echo $arrayMots['mdp']; ?></label>
                                <input id="password" type='password' maxlength="25" name="password" value="<?php echo $champInitial['password']; ?>"/>
                            </div> 
                            <div class="information <?php if ($champInitial['champVideName'] || $champInitial['invalidName'] || $champInitial['champNameTropLong']) { echo 'erreur'; } ?>">
                                <label for="name"><?php echo $arrayMots['name']; ?></label>
                                <input id="name" type="text" name="name" maxlength="15" value="<?php echo $champInitial['name']; ?>" />
                            </div>
                        </div>
                        <div class="troisBTN">                         
                            <input class="bouton" type='submit' name='login' value="<?php echo $arrayMots['btn_login']; ?>">
                            <input class="bouton" type='submit' name='signUp' value="<?php echo $arrayMots['btn_signUp']; ?>">
                            <input class="bouton" type='submit' name='reset' value="<?php echo $arrayMots['btn_Erase']; ?>">
                            <input type='hidden' name='langue' value="<?php echo $champInitial['typeLangue']; ?>">
                        </div>
                    </form> 
                </fieldset>
            </div>            
            <div class="footer">
                <div class='avert                    		
                            <?php if ($champInitial['situation'] !== 1 && $champInitial['situation'] !== 5) { echo 'erreur'; } ?>'>
                    <p> <?php echo $arrayMots['message']; ?> </p>
                </div>                
                <div class="btnRetour">
                    <form method="post" action="./login_Organisateur.php">
                        <input class="bouton" type="submit" name="return" value="<?php echo $arrayMots['btn_return']; ?>"> 
                        <input type='hidden' name='langue' value="<?php echo $champInitial['typeLangue']; ?>">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>