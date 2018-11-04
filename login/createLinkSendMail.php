<?php
function initialChamp() {
    $champInitial = ["champTropLong" => false, "champVide" => false, "champInvalidUser" => false, "champTropLongUser" => false, "champVideUser" => false, "combinaison_User_Email" => false, "userExistant" => false, "champVideEmail" => false, "champInvalidEmail" => false, "champInvalid" => false, "champTropLongEmail" => false, "situation" => 0, "user" => "", "email" => "", "typeLangue" => "", "erreurManipulationBD" => false, "password_Temp" => "", "combinaison_User_Email" => false, "erreurManipulationBD" => false, "lien_Reset_PWD" => "", "envoiCourrielSucces" => false];
    return $champInitial;
}

function traduction($champs) {    
    if ($champs["typeLangue"] === 'francais') {
        $title = "Demande de Réinitialisation";
        $p1 = "Vous avez oublié votre mot de passe, pas de problème, on s'en occupe !";
        $li1 = "Veuillez saisir votre nom d'utilisateur et courriel.";
        $legend = "Réinitialisation !";
        $usager = "Nom d'utilisateur :";
        $email = "Courriel :";
        $btn_send_Link = "Réinitialiser";
        $btn_return = "Retour à l'accueil";
    } elseif ($champs["typeLangue"] === 'english') {
        $title = "Reset Request";
        $p1 = "You forgot your password, no problem, we take care of it !";
        $li1 = "Please enter your username and email.";
        $legend = "Reseting !";
        $usager = "Username :";
        $email = "Email :";
        $btn_send_Link = "Reset";
        $btn_return = "Return to home page";
    }

    $messageFinal = traductionSituation($champs);
    $arrayMots = ['message' => $messageFinal, 'title' => $title, 'p1' => $p1, 'li1' => $li1, 'legend' => $legend, 'usager' => $usager, 'email' => $email, 'btn_send_Link' => $btn_send_Link, 'btn_return' => $btn_return];
    return $arrayMots;
}

function traductionSituation($champs){
    $messageEnPreparation = "";
    if ($champs["typeLangue"] === 'francais') {

    } elseif ($champs["typeLangue"] === 'english') {

    }
    return $messageEnPreparation;
}

function verifChamp($champs, $connMYSQL) {
    // Section de vérification des champs vide
    if (empty($_POST['user'])){
        $champs['champVideUser'] = true;
    } else {
        $champs["user"] = $_POST['user'];
    }  

    if (empty($_POST['email'])){
        $champs['champVideEmail'] = true;
    } else {
        $champs["email"] = $_POST['email'];
    }

    if ($champs['champVideUser'] || $champs['champVideEmail']){
        $champs['champVide'] = true;
    }

    // Section de vérification des longueurs de champs 
    $longueurUser = strlen($champs['user']);
    $longueurEmail = strlen($champs['email']);

    if ($longueurUser > 15) {
        $champs['champTropLongUser'] = true;
    }

    if ($longueurEmail > 50){
        $champs['champTropLongEmail'] = true;
    }

    if ($champs['champTropLongUser'] || $champs['champTropLongEmail']){
        $champs['champTropLong'] = true;
    } 

    // Section de vérification sur le contenu des champs
    $patternUser = "#^[0-9a-z]([0-9a-z]{0,13})[0-9a-z]$#";
    if (!preg_match($patternUser, $champs['user'])) {
        $champs['champInvalidUser'] = true;
    }

    $patternEmail = "#^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$#";    
    if (!preg_match($patternEmail, $champs['email'])) {
        $champs['champInvalidEmail'] = true; 
    }  

    if ($champs['champInvalidUser'] || $champs['champInvalidEmail']){
        $champs['champInvalid'] = true;
    }  
    return $champs;
}

function situation($champs){

    return $champs;
}

function creationLink($champs, $connMYSQL){  
    // $connMYSQL->affected_rows donne le nombre de lignes affectées par la dernière requête
    $sql = "select user, password from benoitmignault_ca_mywebsite.login where email = '{$champs["email"]}' and user = '{$champs["user"]}'";
    $connMYSQL->query($sql);
    // Si le résultat est inférieur à 1, 
    // nous avons une erreur de communication ou un échec lors de la requête SQL   

    // Ici, on s'assure que le résultat nous retourne seulement 1, car nous avons à faire à un seul user normalement
    if (mysqli_affected_rows($connMYSQL) == 1){
        date_default_timezone_set('America/New_York');
        $champs["combinaison_User_Email"] = true;
        // MYSQLI_ASSOC permet de remplacer les chiffre par les nom de colonnes
        //$row = $result->fetch_array(MYSQLI_ASSOC);
        $lien_Reset_PWD = $champs["user"] . "/*-+!/$%?&*()" . $champs["email"];
        $lien_Reset_PWD = encryptementPassword($lien_Reset_PWD);
        $password_Temp = generateRandomString(10);
        $password_Encrypted = encryptementPassword($password_Temp);
        $sql_update = "update benoitmignault_ca_mywebsite.login set reset_link = '{$lien_Reset_PWD}', passwordTemp = '{$password_Encrypted}' where email = '{$champs["email"]}' and user = '{$champs["user"]}'";
        $connMYSQL->query($sql_update);

        // On valide que l'insertion des password temporaire et link encryptés s'est bien passé
        if (!mysqli_affected_rows($connMYSQL) == 1){
            $champs["erreurManipulationBD"] = true; 
        } else {
            // On récupère l'heure au moment de la création du link
            $current_time = date("Y-m-d H:i:s");
            // On converti tout ça dans un gros entier
            $current_timestamp = strtotime($current_time);
            // On ajoute une heure pour donner le temps mais pas toute la vie à l'usagé pour changer ton PWD
            $temps_Autorisé = strtotime("+1 hour", strtotime($current_time));
            $sql_update = "update benoitmignault_ca_mywebsite.login set temps_Valide_link = '{$temps_Autorisé}' where email = '{$champs["email"]}' and user = '{$champs["user"]}'";
            $connMYSQL->query($sql_update);
            // On valide ici que l'ajout du temps autorisé au changement de PWD a bien marché
            if (!mysqli_affected_rows($connMYSQL) == 1){
                $champs["erreurManipulationBD"] = true; 
            } else {
                $champs["password_Temp"] = $password_Temp; 
                $champs["lien_Reset_PWD"] = "<a href='Http://benoitmignault.ca/login/reset.php?key=" . $lien_Reset_PWD; 
                $elementCourriel = preparationEmail($champs);
                $succes = mail($elementCourriel["to"], $elementCourriel["subject"], $elementCourriel["message"], $elementCourriel["headers"]);
                
                if ($succes) {
                    $champs["envoiCourrielSucces"] = true; 
                } 
                
            }
        }

    } else {
        $champs["erreurManipulationBD"] = true;
    }
    return $champs;
}

// Une fonction que j,ai pris sur StackOverFlow
// https://stackoverflow.com/questions/4356289/php-random-string-generator
function generateRandomString($length){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function encryptementPassword($password_Temp) {
    $password_Encrypted = password_hash($password_Temp, PASSWORD_BCRYPT);
    return $password_Encrypted;
}

function preparationEmail($champs){
    $elementCourriel = ['message' => "", "to" => "", "subject" => "", "headers" => ""];
    if ($champs["typeLangue"] == "francais"){
        $elementCourriel["message"] = corpMessageFR($champs);
        $elementCourriel["to"] = $champs["email"];
        $elementCourriel["subject"] = "Changement de mot de passe !";
        $elementCourriel["headers"] = "From: nepasrepondreaucourriel@benoitmignault.ca \r\n";
        $elementCourriel["headers"] .= "'X-Mailer' => 'PHP/'" . phpversion() . "\r\n";
        $elementCourriel["headers"] .= "Content-Type: text/html; charset=UTF-8 \r\n";
    } elseif ($champs["typeLangue"] == "english"){
        $elementCourriel["message"] = corpMessageEN($champs);
        $elementCourriel["to"] = $champs["email"];
        $elementCourriel["subject"] = "Password change !";
        $elementCourriel["headers"] = "From: donotreplyemail@benoitmignault.ca \r\n";
        $elementCourriel["headers"] .= "'X-Mailer' => 'PHP/'" . phpversion() . "\r\n";
        $elementCourriel["headers"] .= "Content-Type: text/html; charset=UTF-8 \r\n";
    }
    return $elementCourriel;
}

function corpMessageFR($champs){    
    $messageFR = '<html><body>';
    $messageFR .= "<p>Bonjour !</p>";
    $messageFR .= "<p>Ceci est un courriel de courtoisie pour vous permettre de changer votre mot de passe pour faire de nouvelles consultation des statistiques de poker.</p>";
    $messageFR .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
    $messageFR .= "<tr><td><strong>Lien Web :</strong> </td><td>" . $champs["lien_Reset_PWD"] . "</td></tr>";
    $messageFR .= "<tr><td><strong>Mot de Passe (Temporaire) :</strong> </td><td>" . $champs["password_Temp"] . "</td></tr>";
    $messageFR .= "<tr><td><strong>Temps accordé pour le changement :</strong> </td><td>1 heure</td></tr>";    
    $messageFR .= "</table>";
    $messageFR .= "<p align=\"left\">Bonne journée</p>";
    $messageFR .= "<p align=\"right\">La Direction</p>";
    $messageFR .= "</body></html>";
    return $messageFR;
}

function corpMessageEN($champs){
    $messageEN = '<html><body>';
    $messageEN .= "<p>Hello !</p>";
    $messageEN .= "<p>This is a courtesy email to allow you to change your password to make further viewing of poker statistics.</p>";
    $messageEN .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
    $messageEN .= "<tr><td><strong>Web Link :</strong> </td><td>" . $champs["lien_Reset_PWD"] . "</td></tr>";
    $messageEN .= "<tr><td><strong>Password (Temporary) :</strong> </td><td>" . $champs["password_Temp"] . "</td></tr>";
    $messageEN .= "<tr><td><strong>Time allowed for change :</strong> </td><td>1 hour</td></tr>";    
    $messageEN .= "</table>";
    $messageEN .= "<p align=\"left\">Have a nice day</p>";
    $messageEN .= "<p align=\"right\">The Direction</p>";
    $messageEN .= "</body></html>";
    return $messageEN;
}

function redirection($champs) {  
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        header("Location: /erreur/erreur.php");
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['return']) && $champs["typeLangue"] == "francais") {
            header("Location: /index.html");
        } elseif (isset($_POST['return']) && $champs["typeLangue"] == "english") {
            header("Location: /english/english.html");            
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
    $champs["typeLangue"] = $_GET['langue'];
    if ($champs["typeLangue"] != "francais" && $champs["typeLangue"] != "english") {
        redirection($champs);        
    } else {
        $arrayMots = traduction($champs);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $champs = initialChamp();
    $champs["typeLangue"] = $_POST['langue'];
    if (isset($_POST['return']))  {
        redirection($champs);
    } else {        
        $connMYSQL = connexionBD();

        // Si le bouton se connecter est pesé...        
        if (isset($_POST['send_Link'])) {
            $champs = verifChamp($champs, $connMYSQL);            
            if (!$champs["champVide"] && !$champs["champTropLong"] && !$champs["champInvalid"]){
                $champs = creationLink($champs, $connMYSQL);
            }
        }
        $champs["situation"] = situation($champs);
        $arrayMots = traduction($champs);
    }
    $connMYSQL->close();
}
?>
<!DOCTYPE html>
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
                </ul>
                <fieldset>
                    <legend align="center"><?php echo $arrayMots['legend']; ?></legend>
                    <form method="post" action="./createLinkSendMail.php">
                        <div class="connexion">
                            <div class="information <?php if ($champs['champVideUser'] || $champs['champInvalidUser'] || $champs['champTropLongUser']) { echo 'erreur'; } ?>">
                                <label for="user"><?php echo $arrayMots['usager']; ?></label>
                                <input autofocus id="user" type="text" name="user" maxlength="15" value="<?php echo $champs['user']; ?>" />
                            </div>
                            <div class="information <?php if ($champs['champVideEmail'] || $champs['champTropLongEmail'] || $champs['champInvalidEmail'] ) { echo 'erreur'; } ?>">
                                <label for="email"><?php echo $arrayMots['email']; ?></label>
                                <input id="email" type="email" name="email" maxlength="50" value="<?php echo $champs['email']; ?>" />
                            </div>
                        </div>
                        <div class="troisBTN"> 
                            <input class="bouton" type='submit' name='send_Link' value="<?php echo $arrayMots['btn_send_Link']; ?>">
                            <input type='hidden' name='langue' value="<?php echo $champs['typeLangue']; ?>">
                        </div>
                    </form> 
                </fieldset>
            </div>

            <div class="footer">
                <!-- ici la situation sera lorsque l'envoi par courriel sera un succès -->
                <div class='avert <?php if ($champs["situation"] != 16) { echo 'erreur'; } ?>'>
                    <p> <?php echo $arrayMots['message']; ?> </p>
                </div> 
                <div class="btnRetour">
                    <form method="post" action="./createLinkSendMail.php">                        
                        <input class="bouton" type="submit" name="return" value="<?php echo $arrayMots['btn_return']; ?>">  
                        <input type='hidden' name='langue' value="<?php echo $champs['typeLangue']; ?>">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>