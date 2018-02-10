<?php
/*
Cette fonction aura pour but d'initialiser les champs et les flag de validations du programme de connexion à la page des statistiques de poker
*/
function initialChamp(){
    $champInitial = ["champVide" => false, "duplicatUser" => false, "champInvalid" => false, 
                     "badUser" => false, "champTropLong" => false, "badPassword" => false, 
                     "password" => "", "situation" => 0, "user" => "", "typeLangue" => ""];
    return $champInitial;
}
/*
 * Cette fonction sera utilisée pour faire l'affichage en français ou en anglais selon de quelle pages nous devons
 */
function traduction($champInitial){
    $message = "";
    if ($champInitial["typeLangue"] === 'francais') {  
        $title = "Connexion";
        $p1 = "Bienvenue à la page de connexion des statistiques du poker entre amis !";
        $li1 = "Vous devez vous authentifiez, pour faire afficher les statistiques désirées";
        $li2 = "Si vous n'avez pas de nom d'utilisateur, veuillez vous en créez un auparavant.";
        $li3 = "Si vous avez besoin de créer un nom d'utilisateur, ce dernier doit être unique.";
        $legend = "Connexion !";
        $usager = "Nom d'utilisateur : "; 
        $mdp = "Mot de passe : ";
        $btn_login = "Se Connecter"; 
        $btn_signUp = "S'inscrire";
        $btn_Erase = "Effacer";
        $btn_return = "Retour à l'acceuil";
        // Si la valeur de la situation est différent de zéro, nous attribuons le message correspondance et nous affichons à son endroit prévu
        if ($champInitial['situation'] !== 0){
            switch($champInitial['situation']){
                case 1 : $message = "Toutes les variables et les champs ont été réinitialisées !"; break;
                case 2 : $message = "Votre utilisateur &rarr; {$champInitial['user']} &larr; n'existe pas !"; break;
                case 3 : $message = "Vous n'avez pas saisie le bon mot de passe avec votre utilisateur &rarr; {$champInitial['user']} &larr; !<br>
                         Si vous avez oublié votre mot de passe, veuillez nous contactez via notre page d'accueuil dans la section «Nous joindre» avec une explication et nous allons changez pour vous votre mot de passe."; break;
                case 4 : $message = "Attention l'utilisateur &rarr; {$champInitial['user']} &larr; est déjà utilisé par quelqu'un d'autre !"; break;
                case 5 : $message = "Oh ! Votre utilisateur &rarr; {$champInitial['user']} &larr; a bien été crée avec succès !";  break;
                case 6 : $message = "Attention vous avez oublié d'inscrire votre mot de passe avec votre user &rarr; {$champInitial['user']} &larr;"; break;
                case 7 : $message = "Attention les deux champs sont vides !"; break;
                case 8 : $message = "Attention la longueur permise pour l'utilisateur est de 10 et pour le mot de passe de 25 caracteres !"; break;
                case 9 : $message = "Attention les champs peuvent contenir seulement des caractères alphanumériques !"; break;                
            }
        }
    } elseif ($champInitial["typeLangue"] === 'english') {
        $title = "Connection";
        $p1 = "Welcome to the login page to see the statistic of poker between friends !";
        $li1 = "You must login if you want to see the poker statistic.";
        $li2 = "If you don't have any username and password, you must create one.";
        $li3 = "If you need a username, this one must be unique.";
        $legend = "Connection !";
        $usager = "Username : "; 
        $mdp = "Password : ";
        $btn_login = "Login"; 
        $btn_signUp = "Sign Up";
        $btn_Erase = "Erase";
        $btn_return = "Return to home page";
        if ($champInitial['situation'] !== 0){
            switch($champInitial['situation']){
                case 1 : $message = "All fields and variables have been reset !"; break;
                case 2 : $message = "Your username &rarr; {$champInitial['user']} &larr; doesn't exist !"; break;
                case 3 : $message = "You have not entered the good password with your user &rarr; {$champInitial['user']} &larr; !<br>
                            If you have forgotten your password, please contact us by our home page in the «Contact us» section with an explanation and we will change your password for you."; break;
                case 4 : $message = "Warning ! The username &rarr; {$champInitial['user']} &larr; is already taken by someone else !"; break;
                case 5 : $message = "Your username &rarr; {$champInitial['user']} &larr; has been create with succes !";  break;
                case 6 : $message = "Warning ! You forgot to mention your password with your username &rarr; {$champInitial['user']} &larr;"; break;
                case 7 : $message = "Warning ! The fields are empty !"; break;
                case 8 : $message = "Warning ! The length possible for user is 10 and for password is 25 caracters !"; break;
                case 9 : $message = "Warning ! The username must be fill by only alphanumeric characters !"; break;
            }
        }
    }
    $arrayMots = ['title'=>$title, 'p1'=>$p1, 'li1'=>$li1, 'li2'=>$li2, 'li3'=>$li3, 
                  'legend'=>$legend, 'usager'=>$usager, 'mdp'=>$mdp, 'btn_login'=>$btn_login, 'btn_signUp'=>$btn_signUp, 'btn_Erase'=>$btn_Erase, 'btn_return'=>$btn_return, 'message'=>$message];
    return $arrayMots;
}
/*
 * Fonction qui va afficher un message d'information en fonction du comportement de l'usagé sur la page de connexion
 */
function situation($champInitial){
    $typeSituation = 0;
    if (isset($_POST['reset'])){
        $typeSituation = 1;  
    } elseif ($champInitial['badUser'] && $champInitial['user'] !== ""){
        $typeSituation = 2;
    } elseif (!$champInitial['badUser'] && $champInitial['badPassword']){
        $typeSituation = 3;
    } elseif ($champInitial['duplicatUser']){
        $typeSituation = 4;
    } elseif ($champInitial['user'] !== "" && !$champInitial['duplicatUser'] && !$champInitial['champVide'] && !$champInitial['champInvalid'] ) {
        $typeSituation = 5;
    } elseif ($champInitial['user'] !== "" && $champInitial['password'] === "" && !$champInitial['duplicatUser'] && !$champInitial['badUser'] && !$champInitial['champInvalid']){
        $typeSituation = 6; 
    } elseif ($champInitial['champVide']) {
        $typeSituation = 7;  
    } elseif ($champInitial['champTropLong']) {
        $typeSituation = 8; 
    } elseif ($champInitial['champInvalid']) {
        $typeSituation = 9;     
    }
    return $typeSituation; // on retourne seulement un numéro qui va nous servicer dans la fct traduction()
}  

/*
* fonction pour vérifier si les champs sont vide ou pas
*/
function verifChamp($champInitial){
    if ( (empty($champInitial['user'])) || (empty($champInitial['password'])) ){          
        $champInitial['champVide'] = true;
    }
    $longueurUser = strlen($champInitial['user']);
    $longueurPassword = strlen($champInitial['password']);
    if ( ($longueurUser > 15) || ($longueurPassword > 25) ){
        $champInitial['champTropLong'] = true;
    }
    // On ne doit pas avoir de caractères spéciaux dans l'username
    $patternUser = "#^[0-9a-z]([0-9a-z]{0,13})[0-9a-z]$#";
    if (!preg_match($patternUser, $champInitial['user'])){
        $champInitial['champInvalid'] = true;
    }

    $patternPass = "#^[0-9a-zA-Z]([0-9a-zA-Z]{0,23})[0-9a-zA-Z]$#";
    if (!preg_match($patternPass, $champInitial['password'])){
        $champInitial['champInvalid'] = true;
    }

    return $champInitial;
}
/*
* Fonction qui sert à ajouter un user et ainsi que son password encrypter solidement.
  Si le user qu'on veut ajouter est déjà présent, nous levons le flag correspondant
*/
function creationUser($champInitial, $connMYSQL){
    $sql = "select user from benoitmignault_ca_mywebsite.login";
    $result = $connMYSQL->query($sql);

    foreach($result as $row){
        if ($row['user'] === $champInitial['user'] ){ 
            $champInitial['duplicatUser'] = true;
        }
    }

    if ($champInitial['duplicatUser']){
        return $champInitial;
    } else {
        $passwordCrypter = encryptementPassword($champInitial['password']);
        $insert = "INSERT INTO benoitmignault_ca_mywebsite.login (user, password, id) VALUES ";
        $insert .= "('".$champInitial['user']."','".$passwordCrypter."', NULL)";
        $connMYSQL->query($insert);        
        return $champInitial;
    }
}
/*
 * Cette fonction aura pour but d'encrypter le password et de retourner ce dernier
 * Depuis php 7, on nous demande d'utiliser random_byte mais on doit avoir une longueur de 22 bytes
 */
function encryptementPassword(string $password){
    $options = ['cost' => 11, 'salt' => random_bytes(22)];
    $passwordCrypter = password_hash($password, PASSWORD_BCRYPT, $options);
    return $passwordCrypter;
}

/*
* Fonction qui permet au user de se connection à la page poker.php 
* si et seulement si son user existe avec le bon password
*/
function connexionUser($champInitial, $connMYSQL){
    //$sql = "select user, password from benoitmignault_ca_mywebsite.login";
    $sql = "select user, password from benoitmignault_ca_mywebsite.login";
    $result = $connMYSQL->query($sql);

    foreach($result as $row){        
        if ($row['user'] === $champInitial['user'] ){
            if (password_verify($champInitial['password'], $row['password'])) { 
                session_start();
                $_SESSION['user'] = $champInitial['user'];   
                $_SESSION['password'] = $champInitial['password']; 
                $_SESSION['typeLangue'] = $champInitial["typeLangue"];

                if ($row['user'] === "admin"){
                    header("Location: ./statsPoker/administration/admin.php");
                } else {
                    header("Location: ./statsPoker/poker.php");
                }                               
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $champInitial = initialChamp(); // est un tableau avec tous les flag erreurs possibles et les infos du user, pwd et le type de situation    
    $champInitial["typeLangue"] = $_GET['langue'];

    if ($champInitial["typeLangue"] != "francais" && $champInitial["typeLangue"] != "english"){
        header("Location: /erreur/erreur.php");
        exit;
    } else {
        $arrayMots = traduction($champInitial);  
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $champInitial = initialChamp();
    $champInitial["typeLangue"] = $_POST['langue'];    
    if (isset($_POST['return'])){               
        if ($champInitial["typeLangue"] === 'english'){            
            header("Location: /english/english.html");           
        } elseif ($champInitial["typeLangue"] === 'francais'){
            header("Location: /index.html");         
        } 
         exit;
    } else {        
        $champInitial["user"] = strtolower($_POST['user']);
        $champInitial["password"] = $_POST['password'];        
        /*
        $host = "benoitmignault.ca.mysql";
        $user = "benoitmignault_ca_mywebsite";
        $password = "d-&47mK!9hjGC4L-";
        $bd = "benoitmignault_ca_mywebsite";
        $connMYSQL = new mysqli($host, $user, $password, $bd);             
        */
        
        $host = "localhost";
        $user = "zmignaub";
        $password = "Banane11";
        $bd = "benoitmignault_ca_mywebsite";
        
        $connMYSQL = mysqli_connect($host, $user, $password, $bd);

        // si le bouton se connecter est pesé...        
        if (isset($_POST['login'])){
            $champInitial = verifChamp($champInitial);
            if (!$champInitial["champVide"] && !$champInitial["champTropLong"] && !$champInitial["champInvalid"]){
                $champInitial = connexionUser($champInitial, $connMYSQL);
            }
            // si le bouton s'inscrire est pesé...
        } elseif (isset($_POST['signUp'])){
            $champInitial = verifChamp($champInitial);
            if (!$champInitial["champVide"] && !$champInitial["champTropLong"] && !$champInitial["champInvalid"]){
                $champInitial = creationUser($champInitial, $connMYSQL);
            }
            // si le bouton éffacer est pesé...
        } elseif (isset($_POST['reset'])){
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
                    <li class='info'><?php echo $arrayMots['li3']; ?></li>
                </ul>
                <fieldset>
                    <legend align="center"><?php echo $arrayMots['legend']; ?></legend>
                    <form method="post" action="./login.php">
                        <div class="connexion">                    
                            <div class="information <?php if ($champInitial['champVide'] || $champInitial['champInvalid'] || $champInitial['duplicatUser'] || $champInitial['badUser'] || $champInitial['champTropLong'] ){ echo 'erreur'; } ?>">
                                <label for="user"><?php echo $arrayMots['usager']; ?></label>
                                <input id="user" type="text" name="user" maxlength="20" value="<?php echo $champInitial['user']; ?>" />
                            </div>
                            <div class="information <?php if ($champInitial['badPassword'] || $champInitial['champVide'] || $champInitial['champInvalid'] || $champInitial['champTropLong'] ){ echo 'erreur'; } ?>">
                                <label for="password"><?php echo $arrayMots['mdp']; ?></label>
                                <input id="password" type='password' maxlength="25" name="password" value="<?php echo $champInitial['password']; ?>"/>
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
                            <?php if ($champInitial['champVide'] || $champInitial['champInvalid'] || $champInitial['duplicatUser'] || $champInitial['badUser'] || $champInitial['champTropLong'] || $champInitial['badPassword']){ echo 'erreur'; } ?>'>
                    <p> <?php echo $arrayMots['message']; ?> </p>
                </div>                
                <div class="btnRetour">
                    <form method="post" action="./login.php">
                        <input class="bouton" type="submit" name="return" value="<?php echo $arrayMots['btn_return']; ?>"> 
                        <input type='hidden' name='langue' value="<?php echo $champInitial['typeLangue']; ?>">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>