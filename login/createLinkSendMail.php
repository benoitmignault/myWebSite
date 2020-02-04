<?php
function initialChamp() {
    $champInitial = ["user" => "", "email" => "", "champVide" => false, "champInvalid" => false, "champTropLong" => false, "emailExistePas" => false, "situation" => 0, "typeLangue" => "", "erreurManipulationBD" => false, "password_Temp" => "", "lien_Reset_PWD" => "", "envoiCourrielSucces" => false];
    return $champInitial;
}

function traduction($champs) {    
    if ($champs["typeLangue"] === 'francais') {
        $lang = "fr";
        $title = "Demande de Réinitialisation";
        $p1 = "Vous avez oublié votre mot de passe, pas de problème, on s'en occupe !";
        $li3 = "Cette page permet de réinitialiser votre compte associés aux statistiques de poker.";
        $li1 = "Veuillez saisir votre courriel.";
        $li2 = "Ensuite, un courrier vous sera envoyé avec toute les informations relier à votre changement de mot de passe.";
        $legend = "Réinitialisation !";
        $email = "Courriel :";
        $btn_send_Link = "Réinitialiser";
        $btn_return = "Retour à l'accueil";
    } elseif ($champs["typeLangue"] === 'english') {
        $title = "Reset Request";
        $lang = "en";
        $p1 = "You forgot your password, no problem, we take care of it !";        
        $li3 = "This page will reset your account associated with poker statistics.";
        $li1 = "Please enter your email.";
        $li2 = "Then, a mail will be sent to you with all the information related to your change of password.";              
        $legend = "Reseting !";
        $email = "Email :";
        $btn_send_Link = "Reset";
        $btn_return = "Return to home page";
    }

    $messageFinal = traductionSituation($champs);
    $arrayMots = ["lang" => $lang, 'message' => $messageFinal, 'title' => $title, 'p1' => $p1, 'li3' => $li3, 'li1' => $li1, 'li2' => $li2, 'legend' => $legend, 'email' => $email, 'btn_send_Link' => $btn_send_Link, 'btn_return' => $btn_return];
    return $arrayMots;
}

function traductionSituation($champs){
    $message = "";
    if ($champs["typeLangue"] === 'francais') {
        switch ($champs['situation']) {
            case 1 : $message = "Le champ «Courriel» est vide !"; break; 
            case 2 : $message = "Le courriel saisie est trop long pour l'espace disponible !"; break; 
            case 3 : $message = "Le courriel saisie ne respecte pas la forme « exemple@email.com »"; break; 
            case 4 : $message = "Le courriel saisie n'existe pas dans nos informations !"; break; 
            case 5 : $message = "Une erreur de communication/manipulation est survenu au moment de vous envoyer le courriel !"; break; 
            case 6 : $message = "Dans les prochains instant, vous allez recevoir le courriel de réinitialisation avec toutes les informations nécessaire !"; break; 
        }
    } elseif ($champs["typeLangue"] === 'english') {
        switch ($champs['situation']) {
            case 1 : $message = "The «Email» field is empty !"; break; 
            case 2 : $message = "The seized mail is too long for the available space !"; break; 
            case 3 : $message = "The seized mail does not follow the form « example@email.com » !"; break; 
            case 4 : $message = "The entered email does not exist in our information !"; break; 
            case 5 : $message = "A communication / manipulation error occurred when sending you the email !"; break; 
            case 6 : $message = "In the next few moments, you will receive the reset email with all the necessary information !"; break; 
        }
    }
    return $message;
}

function verifChamp($champs, $connMYSQL) {
    // Section de vérification des champs vide  
    if (empty($_POST['email'])){
        $champs['champVide'] = true;
    } else {
        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("select user, email from login where email =? ");
        /* Lecture des marqueurs */
        $stmt->bind_param("s", $_POST['email']);
        /* Exécution de la requête */
        $stmt->execute();
        /* Association des variables de résultat */
        $result = $stmt->get_result();
        $row_cnt = $result->num_rows;           
        if ($row_cnt == 0){
            $champs['emailExistePas'] = true;
        } else {            
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $champs["user"] = $row['user'];
            $champs["email"] = $row['email'];
        }
        /* close statement and connection */
        $stmt->close();
    }
    $longueurEmail = strlen($champs['email']);    

    if ($longueurEmail > 50){
        $champs['champTropLong'] = true;
    } 

    $patternEmail = "#^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$#";    
    if (!preg_match($patternEmail, $_POST['email'])) {
        $champs['champInvalid'] = true; 
    }  

    return $champs;
}

// apres avoir envoyer le courriel, nous allons déterminer le message qui sera affiché à l'usagé
function situation($champs){   
    $typeSituation = 0;    
    if ($champs['champVide']){
        $typeSituation = 1; 
    } elseif ($champs['champTropLong']){
        $typeSituation = 2; 
    } elseif ($champs['champInvalid']){
        $typeSituation = 3; 
    } elseif ($champs['emailExistePas']){
        $typeSituation = 4; 
    } elseif ($champs['erreurManipulationBD']){
        $typeSituation = 5; 
    } elseif ($champs['envoiCourrielSucces']){
        $typeSituation = 6; 
    } 

    return $typeSituation;
}

function creationLink($champs, $connMYSQL){  
    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare("select user, password from login where email =? and user =? ");
    /* Lecture des marqueurs */
    $stmt->bind_param("ss", $champs["email"], $champs["user"]);
    /* Exécution de la requête */
    $stmt->execute();
    /* Association des variables de résultat */
    $result = $stmt->get_result();
    $row_cnt = $result->num_rows;
    /* close statement and connection */
    $stmt->close();    

    if ($row_cnt == 0){
        $champs["erreurManipulationBD"] = true; 
    } else {
        date_default_timezone_set('America/New_York');
        $lien_Reset_PWD = $champs["user"] . "/*-+!/$%?&*()" . $champs["email"];
        $lien_Reset_PWD = encryptementPassword($lien_Reset_PWD);
        $password_Temp = generateRandomString(10);
        $password_Encrypted = encryptementPassword($password_Temp);

        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("update login set reset_link =? , passwordTemp =? where email =? and user =?");
        /* Lecture des marqueurs */
        $stmt->bind_param("ssss", $lien_Reset_PWD, $password_Encrypted, $champs["email"], $champs["user"]);
        /* Exécution de la requête */
        $stmt->execute();

        $row_cnt = $stmt->affected_rows;
        /* close statement and connection */
        $stmt->close();

        // On valide que l'insertion des password temporaire et link encryptés s'est bien passé
        if ($row_cnt == 0){
            $champs["erreurManipulationBD"] = true; 
        } else {
            // On récupère l'heure au moment de la création du link
            $current_time = date("Y-m-d H:i:s");
            // On converti tout ça dans un gros entier
            $current_timestamp = strtotime($current_time);
            // On ajoute 12 heures pour donner le temps mais pas toute la vie à l'usagé pour changer ton PWD
            $temps_Autorise = strtotime("+12 hour", strtotime($current_time));

            /* Crée une requête préparée */
            $stmt = $connMYSQL->prepare("update login set temps_Valide_link =? where email =? and user =? ");
            /* Lecture des marqueurs */
            $stmt->bind_param("iss", $temps_Autorise, $champs["email"], $champs["user"]);
            /* Exécution de la requête */
            $stmt->execute();
            $row_cnt = $stmt->affected_rows;
            /* close statement and connection */
            $stmt->close();           

            // On valide ici que l'ajout du temps autorisé au changement de PWD a bien marché
            if ($row_cnt == 0){
                $champs["erreurManipulationBD"] = true; 
            } else {
                $champs["password_Temp"] = $password_Temp; 
                if ($champs["typeLangue"] === 'francais') {
                    $lien = "Lien pour changer votre mot de passe";
                } elseif ($champs["typeLangue"] === 'english') {
                    $lien = "Link to change your password";
                }
                $champs["lien_Reset_PWD"] = "<a target=\"_blank\" href=\"https://benoitmignault.ca/login/reset.php?key={$lien_Reset_PWD}&langue={$champs["typeLangue"]}\">{$lien}</a>";
                $elementCourriel = preparationEmail($champs);
                $succes = mail($elementCourriel["to"], $elementCourriel["subject"], $elementCourriel["message"], $elementCourriel["headers"]);                
                if ($succes) {
                    $champs["envoiCourrielSucces"] = true; 
                }                             
            }
        }
    }
    return $champs;
}

// Une fonction que j'ai pris sur StackOverFlow
// https://stackoverflow.com/questions/4356289/php-random-string-generator
function generateRandomString($length){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        //$randomString .= $characters[rand(0, $charactersLength - 1)];
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
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
        $elementCourriel["to"] = "{$champs["email"]}"; 
        $elementCourriel["subject"] = "Changement de mot de passe !";
        $elementCourriel["headers"] = "From: home@benoitmignault.ca \r\n";
        $elementCourriel["headers"] .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $elementCourriel["headers"] .= "Content-Type: text/html; charset=UTF-8 \r\n";
    } elseif ($champs["typeLangue"] == "english"){
        $elementCourriel["message"] = corpMessageEN($champs);
        $elementCourriel["to"] = "{$champs["email"]}"; 
        $elementCourriel["subject"] = "Password change !";
        $elementCourriel["headers"] = "From: home@benoitmignault.ca \r\n";
        $elementCourriel["headers"] .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $elementCourriel["headers"] .= "Content-Type: text/html; charset=UTF-8 \r\n";
    }
    return $elementCourriel;
}

function corpMessageFR($champs){    
    $messageFR = '<html><body>';
    $messageFR .= "<p>Bonjour !</p>";
    $messageFR .= "<p>Ceci est un courriel de courtoisie pour vous permettre de changer votre mot de passe pour faire de nouvelles consultations des statistiques de poker.</p>";
    $messageFR .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
    $messageFR .= "<tr><td><strong>Lien Web :</strong> </td><td>" . $champs["lien_Reset_PWD"] . "</td></tr>";
    $messageFR .= "<tr><td><strong>Nom Utilisateur :</strong> </td><td>" . $champs["user"] . "</td></tr>";
    $messageFR .= "<tr><td><strong>Mot de Passe (Temporaire) :</strong> </td><td>" . $champs["password_Temp"] . "</td></tr>";
    $messageFR .= "<tr><td><strong>Temps accordé pour le changement :</strong> </td><td>12 heures</td></tr>";    
    $messageFR .= "</table>";
    $messageFR .= "<p align=\"left\">Bonne journée</p>";
    $messageFR .= "<p align=\"middle\">La Direction</p>";
    $messageFR .= "</body></html>";
    return $messageFR;
}

function corpMessageEN($champs){
    $messageEN = '<html><body>';
    $messageEN .= "<p>Hello !</p>";
    $messageEN .= "<p>This is a courtesy email to allow you to change your password to make further viewing of poker statistics.</p>";
    $messageEN .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
    $messageEN .= "<tr><td><strong>Web Link :</strong> </td><td>" . $champs["lien_Reset_PWD"] . "</td></tr>";
    $messageEN .= "<tr><td><strong>Username :</strong> </td><td>" . $champs["user"] . "</td></tr>";
    $messageEN .= "<tr><td><strong>Password (Temporary) :</strong> </td><td>" . $champs["password_Temp"] . "</td></tr>";
    $messageEN .= "<tr><td><strong>Time allowed for change :</strong> </td><td>12 hours</td></tr>";    
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

function connexionBD(){
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
            if (!$champs["champVide"] && !$champs["champTropLong"] && !$champs["champInvalid"] && !$champs["emailExistePas"]){                
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
                <li class='info'><?php echo $arrayMots['li3']; ?></li>
                <li class='info'><?php echo $arrayMots['li1']; ?></li>
            </ul>
            <p class='titre un'><?php echo $arrayMots['li2']; ?></p>

            <fieldset>
                <legend class="legendCenter"><?php echo $arrayMots['legend']; ?></legend>
                <form method="post" action="./createLinkSendMail.php">
                    <div class="connexion">
                        <div class="information <?php if ($champs['champVide'] || $champs['champTropLong'] || $champs['champInvalid'] || $champs['emailExistePas']) { echo 'erreur'; } ?>">
                            <label for="email"><?php echo $arrayMots['email']; ?></label>
                            <div>
                                <input placeholder="exemple@email.com" autofocus id="email" type="email" name="email" maxlength="50" value="<?php echo $champs['email']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
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
            <div class='avert <?php if ($champs["situation"] != 6) { echo 'erreur'; } ?>'>
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
