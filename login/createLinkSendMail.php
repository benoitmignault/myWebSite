<?php

// Import PHPMailer classes into the global namespace
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
    // require '../../vendor/autoload.php';

// Lorsque je suis en mode DEV :
	require '../PHPMailer/src/Exception.php';
	require '../PHPMailer/src/PHPMailer.php';
	require '../PHPMailer/src/SMTP.php';
	
/**
 * Fonction qui va contenir tous ce dont on aura besoin.
 * Une partie des variables de type string ou integer et une autre partie en boolean
 * On va ajouter un array pour les mots traduits ou non
 *
 * @return array
 */
function initialisation(): array {
    
    return array("longueur_email" => 0, "situation" => 0, "type_langue" => "", "user" => "", "email" => "",
                 "champ_vide" => false, "champ_invalid" => false, "champ_trop_long" => false, "temps_valide_link" => 0,
                 "email_inexistant_bd" => false, "erreur_system_bd" => false, "erreur_presente" => false,
                 "password_Temp" => "", "lien_Reset_PWD" => "", "envoi_courriel_succes" => false,
                 "reset_existant" => false, "liste_mots" => array());
}

/**
 * Fonction pour setter les premières informations du GET ou POST
 * Aussi, on va récupérer via le POST, les informations relier au email du user
 *
 * @param array $array_Champs
 * @param object $connMYSQL
 * @return array
 */
function remplisage_champs(array $array_Champs, $connMYSQL): array{
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){
        
        if (isset($_GET['langue'])){
	        $array_Champs["type_langue"] = $_GET['langue'];
        }
    }
    
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		if (isset($_POST['langue'])){
			$array_Champs["type_langue"] = $_POST['langue'];
		}
		
        // Nous avons caller le bouton pour créer un lien de reset password
		if (isset($_POST['btn_envoi_lien'])) {
			
            // Si le courriel est présent, on va associer la variable
			if (isset($_POST['email'])) {
				
                // Si le champ du Email n'est pas vide, on l'associe et on va chercher en BD son user et le temps associer au lien, s'il existe
				if (!empty($_POST['email'])) {
					$array_Champs['email'] = $_POST['email'];
					$array_Champs['longueur_email'] = strlen($array_Champs['email']);
					
					// Allons chercher le user et la valeur du lien s'il existe
					// 2023-12-06, Découverte d'une faille de sécurité, je recréer un lien de reset, même si il y a un qui existe....
                    $select = "SELECT user, temps_valide_link ";
                    $from = "FROM login ";
                    $where = "WHERE email = ?";
     
					// Préparation de la requête SQL avec un alias pour la colonne sélectionnée
					$query = $select . $from . $where;
     
					// Préparation de la requête
					$stmt = $connMYSQL->prepare($query);
     
					/* Lecture des marqueurs */
					$stmt->bind_param("s", $array_Champs['email']);
					
					/* Exécution de la requête */
					$stmt->execute();
					
					/* Association des variables de résultat */
					$result = $stmt->get_result();
     
                    // Il ne peut qu'avoir un seul résultat possible vue l'unicité de la Table SQL
					if ($result->num_rows === 1){
						$row = $result->fetch_array(MYSQLI_ASSOC);
						$array_Champs["user"] = $row['user'];
						$array_Champs["temps_valide_link"] = intval($row['temps_valide_link']);
					}
                    /* close statement and connection */
                    $stmt->close();
                }
			}
		}
    }
 
	return $array_Champs;
}

/**
 * Fonction qui servira à mettre à «True» les variables de contrôles des informations que nous avons associé durant la fonction @see remplisage_champs
 * @param $array_Champs
 * @return array
 */
function validation_champs($array_Champs): array{
    
    // Section de vérification du seul champs dans la page
    if (empty($array_Champs['email'])){
	    $array_Champs['champ_vide'] = true;
	    $array_Champs['erreur_presente'] = true;
    } else {
        if ($array_Champs['longueur_email'] > 50){
	        $array_Champs['champ_trop_long'] = true;
	        $array_Champs['erreur_presente'] = true;
        }

        $pattern_email = "#^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$#";
        if (!preg_match($pattern_email, $array_Champs['email'])) {
	        $array_Champs['champ_invalid'] = true;
        }
        
	    // Ajout de cette sécurité / 5 Février 2020
	    // https://stackoverflow.com/questions/11952473/proper-prevention-of-mail-injection-in-php/11952659#11952659
        if (!(filter_var($array_Champs['email'], FILTER_VALIDATE_EMAIL))){
	        $array_Champs['champ_invalid'] = true;
        }
        
        // Si la variable champ_invalid est à «true», ça ne veut même pas la peine de poursuivre...
        if (!$array_Champs['champ_invalid']){
	
            // Si le champ du user est vide, alors l'association du email n'a rien donné
	        if (empty($array_Champs['user'])){
                $array_Champs['email_inexistant_bd'] = true;
		        $array_Champs['erreur_presente'] = true;
	        }
            
            // Ajout de cette sécurité
            if (!empty($array_Champs['temps_valide_link']) && $array_Champs['temps_valide_link'] > 0){
                
                // On va aussi valider que si le lien est expiré, on va permettre l'envoi d'un nouveau lien sinon, on refuse
                $current_timestamp = strtotime(date("Y-m-d H:i:s"));
             
                // Le temps actuel doit être plus petit que le temps prescrit
                if ($current_timestamp < $array_Champs['temps_valide_link']){
                    // Alors, on refuse un nouveau lien
                    $array_Champs['reset_existant'] = true;
	                $array_Champs['erreur_presente'] = true;
                }
                // Sinon, le lien n'est plus valide, donc on va en donner un nouveau
            }
            // On va en donner un autre, de tout façon
        } else {
	        $array_Champs['erreur_presente'] = true;
        }
    }
    
    return $array_Champs;
}


/**
 * Fonction pour déterminer le type de situation d'erreur ou pas qui peut survenir
 *
 * @param array $array_Champs
 * @return int
 */
function situation(array $array_Champs): int{
    
    if ($array_Champs['champ_vide']){
	    $typeSituation = 1;
        
    } elseif ($array_Champs['champ_trop_long']){
        $typeSituation = 2;
        
    } elseif ($array_Champs['champ_invalid']){
        $typeSituation = 3;
        
    } elseif ($array_Champs['email_inexistant_bd']){
        $typeSituation = 4;
        // Ajout de cette nouvelle situation - 2023-12-06
    } elseif ($array_Champs['reset_existant']){
	    $typeSituation = 8;
     
    } elseif ($array_Champs['erreur_system_bd']){
        $typeSituation = 5;
        
    } elseif ($array_Champs['envoi_courriel_succes']){
	    // Normalement, ici, ça veut que dire que nous avons un succès
        $typeSituation = 6;
        
    } else {
        // Rendu ici, on va caller une erreur système
	    $typeSituation = 7;
    } 

    return $typeSituation;
}

// Création fonction pour créer envoyer un courriel à GMAIL
function envoi_courriel_test_gmail($array_Champs) {

    // Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        // Paramètres du serveur SMTP
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'benoit.mignault.ca@gmail.com';
        $mail->Password   = 'uqmsbfldqabqzvne'; 
        $mail->SMTPSecure = "tls";        
        $mail->Port       = 587;  

        // Recipients
        $mail->setFrom('benoit.mignault.ca@gmail.com', 'Site Web Benoit Mignault');
        $mail->addAddress('b.mignault@gmail.com', 'Site Web Benoit Mignault');
        $mail->addAddress('mignault.benoit@courrier.uqam.ca', 'Site Web Benoit Mignault');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test d\'envoi d\'e-mail avec PHPMailer';
        $mail->Body    = 'Bonjour, ceci est un test d\'envoi d\'e-mail avec PHPMailer';

        // Envoyer l'e-mail
        $mail->send();
        $champs["envoiCourrielSucces"] = true; 
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'e-mail : {$mail->ErrorInfo}";
    }

    // Fermer la connexion SMTP
    $mail->smtpClose();

    return $champs;
}

// Création fonction pour créer envoyer un courriel à GMAIL
function gestion_lien_courriel($champs, $connMYSQL){

    //  Préparation du lien pour le courriel, avec true pour gérer les exceptions   
    $mail = creation_instance_courriel();



    

    return $mail;
}

//  Création de l'instant et des paramètres de connexions à GMAILs   
function creation_instance_courriel(){

    //  Préparation du lien pour le courriel, avec true pour gérer les exceptions   
    $mail = new PHPMailer(true);

    // Paramètres du serveur SMTP
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // gmail SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'benoit.mignault.ca@gmail.com';
    $mail->Password   = 'uqmsbfldqabqzvne'; 
    $mail->SMTPSecure = "tls";        
    $mail->Port       = 587;

    return $mail;
}

// Validation que le email 
function verification_existance_user_courriel($array_Champs, $connMYSQL){

    /* Crée une requête préparée */
    $stmt = $connMYSQL->prepare("select user, password from login where email =? and user =? ");
    /* Lecture des marqueurs */
    $stmt->bind_param("ss", $champs["email"], $champs["user"]);
    /* Exécution de la requête */
    $stmt->execute();
    /* Association des variables de résultat */
    $result = $stmt->get_result();

    return $result->num_rows;
}



function creationLink($array_Champs, $connMYSQL){
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
        $champs["erreur_systeme_bd"] = true;
    } else {
        date_default_timezone_set('America/New_York');
        $lien_Reset_PWD = $champs["user"] . "/*-+!/$%?&*()" . $champs["email"];
        $lien_Reset_PWD = encryptementPassword($lien_Reset_PWD);
        $password_Temp = generateRandomString(10);
        $password_Encrypted = encryptementPassword($password_Temp);

        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("update login set reset_link = ? , passwordTemp = ? where email = ? and user = ?");
        
        /* Lecture des marqueurs */
        $stmt->bind_param("ssss", $lien_Reset_PWD, $password_Encrypted, $champs["email"], $champs["user"]);
        
        /* Exécution de la requête */
        $stmt->execute();

        $row_cnt = $stmt->affected_rows;
        /* close statement and connection */
        $stmt->close();

        // On valide que l'insertion des password temporaire et link encryptés s'est bien passé
        if ($row_cnt == 0){
            $champs["erreur_systeme_bd"] = true;
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
                $champs["erreur_systeme_bd"] = true;
            } else {
                $champs["password_Temp"] = $password_Temp; 
                if ($champs["type_langue"] === 'francais') {
                    $lien = "Lien pour changer votre mot de passe";
                } elseif ($champs["type_langue"] === 'english') {
                    $lien = "Link to change your password";
                }
                $champs["lien_Reset_PWD"] = "<a target=\"_blank\" href=\"https://benoitmignault.ca/login/reset.php?key={$lien_Reset_PWD}&langue={$champs["type_langue"]}\">{$lien}</a>";
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

function preparationEmail($array_Champs){
    $elementCourriel = ['message' => "", "to" => "", "subject" => "", "headers" => ""];
    if ($champs["type_langue"] == "francais"){
        $elementCourriel["message"] = corpMessageFR($champs);        
        $elementCourriel["to"] = "{$champs["email"]}"; 
        $elementCourriel["subject"] = "Changement de mot de passe !";
        $elementCourriel["headers"] = "From: home@benoitmignault.ca \r\n";
        $elementCourriel["headers"] .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $elementCourriel["headers"] .= "Content-Type: text/html; charset=UTF-8 \r\n";
    } elseif ($champs["type_langue"] == "english"){
        $elementCourriel["message"] = corpMessageEN($champs);
        $elementCourriel["to"] = "{$champs["email"]}"; 
        $elementCourriel["subject"] = "Password change !";
        $elementCourriel["headers"] = "From: home@benoitmignault.ca \r\n";
        $elementCourriel["headers"] .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $elementCourriel["headers"] .= "Content-Type: text/html; charset=UTF-8 \r\n";
    }
    return $elementCourriel;
}

function corpMessageFR($array_Champs){
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

function corpMessageEN($array_Champs){
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

/**
 * Fonction pour rediriger vers la bonne page page extérieur à la page du reset de password
 *
 * @param string $type_langue
 * @return void
 */
function redirection(string $type_langue) {
    
    // Si nous arrivons ici via le GET, nous avons un problème majeur, donc on call la page 404
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        header("Location: /erreur/erreur.php");
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        if (isset($_POST['btn_return']) && $type_langue == "francais") {
            header("Location: /index.html");
        }
        
        if (isset($_POST['btn_return']) && $type_langue == "english") {
            header("Location: /english/english.html");            
        }
    }
    
    exit; // pour arrêter l'exécution du code php
}

/**
 * Fonction qui sera utiliser pour traduire le texte dans la page et ainsi que les messages d'erreurs
 *
 * @param string $type_langue
 * @param int $situation
 * @return string[]
 */
function traduction_liste_mots(string $type_langue, int $situation): array {
    
    // Initialiser le array de mots traduit
    $liste_mots = array("lang" => "", 'message' => "", 'title' => "", 'p1' => "", 'li3' => "", 'li1' => "",
                        'li2' => "", 'legend' => "", 'email' => "", 'btn_envoi_lien' => "", 'btn_return' => "");
    
    if ($type_langue === 'francais') {
        $liste_mots["lang"] = "fr";
        $liste_mots["title"] = "Demande de Réinitialisation";
        $liste_mots["p1"] = "Vous avez oublié votre mot de passe, pas de problème, on s'en occupe !";
        $liste_mots["li3"] = "Cette page permet de réinitialiser votre compte associés aux statistiques de poker.";
        $liste_mots["li1"] = "Veuillez saisir votre courriel.";
        $liste_mots["li2"] = "Ensuite, un courriel vous sera envoyé avec toute les informations relier à votre changement de mot de passe.";
        $liste_mots["legend"] = "Réinitialisation !";
        $liste_mots["email"] = "Courriel :";
        $liste_mots["btn_envoi_lien"] = "Réinitialiser";
        $liste_mots["btn_return"] = "Retour à l'accueil";
        
    } elseif ($type_langue === 'english') {
        $liste_mots["title"] = "Reset Request";
        $liste_mots["lang"] = "en";
        $liste_mots["p1"] = "You forgot your password, no problem, we take care of it !";
        $liste_mots["li3"] = "This page will reset your account associated with poker statistics.";
        $liste_mots["li1"] = "Please enter your email.";
        $liste_mots["li2"] = "Then, a mail will be sent to you with all the information related to your change of password.";
        $liste_mots["legend"] = "Reseting !";
        $liste_mots["email"] = "Email :";
        $liste_mots["btn_envoi_lien"] = "Reset";
        $liste_mots["btn_return"] = "Return to home page";
    }
    
    // Le message qui sera dans la langue voulu
    $liste_mots["message"] = traduction_situation($type_langue, $situation);
    
    return $liste_mots;
}

/**
 * @param string $type_langue
 * @param int $situation
 * @return string
 */
function traduction_situation(string $type_langue, int $situation): string{
    
    $message = "";
    if ($type_langue === 'francais') {
        
        switch ($situation) {
            case 1 : $message = "Le champ «Courriel» est vide !"; break;
            case 2 : $message = "Le courriel saisie est trop long pour l'espace disponible !"; break;
            case 3 : $message = "Le courriel saisie ne respecte pas la forme « exemple@email.com »"; break;
            case 4 : $message = "Le courriel saisie n'existe pas dans nos informations !"; break;
            case 5 : $message = "Une erreur de communication/manipulation est survenu au moment de vous envoyer le courriel !"; break;
            case 6 : $message = "Dans les prochains instant, vous allez recevoir le courriel de réinitialisation avec toutes les informations nécessaire !"; break;
            case 7 : $message = "Erreur Système au moment d'envoyer le courriel !"; break;
            case 8 : $message = "Vous avez déjà reçu un courriel pour changer votre mot de passe, il n'est pas nécessaire de faire une nouvelle demande !"; break;
        }
        
    } elseif ($type_langue === 'english') {
        
        switch ($situation) {
            case 1 : $message = "The «Email» field is empty !"; break;
            case 2 : $message = "The seized mail is too long for the available space !"; break;
            case 3 : $message = "The seized mail does not follow the form « example@email.com » !"; break;
            case 4 : $message = "The entered email does not exist in our information !"; break;
            case 5 : $message = "A communication / manipulation error occurred when sending you the email !"; break;
            case 6 : $message = "In the next few moments, you will receive the reset email with all the necessary information !"; break;
            case 7 : $message = "System error when sending email !"; break;
            case 8 : $message = "You have already received an email to change your password, there is no need to make a new request !"; break;
        }
    }
    
    return $message;
}




include_once("../includes/fct-connexion-bd.php");

// Les fonctions communes
    $connMYSQL = connexion();
    $array_Champs = initialisation();
    $array_Champs = remplisage_champs($array_Champs, $connMYSQL);


// Ce qui arrive lorsqu'on arrive sur la page pour générer un lien de reset de password
    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    
        // Si la langue n'est pas setter, on va rediriger vers la page Err 404
        if ($array_Champs["type_langue"] !== "francais" && $array_Champs["type_langue"] !== "english") {
            redirection(""); // On n'a pas besoin de la variable vue qu'elle ne ressemble à rien de connue
        } else {
            // La variable de situation est encore à 0 vue qu'il s'est rien passé de grave...
            $array_Champs["liste_mots"] = traduction_liste_mots($array_Champs["type_langue"], $array_Champs["situation"]);
        }
    } // Fin du GET pour faire afficher la page web


    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
        if (isset($_POST['btn_return']))  {
            redirection($array_Champs["type_langue"]);
        } else {
            
            // Si le bouton se connecter est pesé...
            if (isset($_POST['btn_envoi_lien'])) {
                
                $champs = validation_champs($array_Champs);
                if (!$array_Champs['erreur_presente']){
                    // $array_Champs = gestion_lien_courriel($array_Champs, $connMYSQL);
                    // $champs = creationLink($champs, $connMYSQL);
                }
            }
        
            $array_Champs["situation"] = situation($array_Champs);
            $array_Champs["liste_mots"] = traduction_liste_mots($array_Champs["type_langue"], $array_Champs["situation"]);
         
            $connMYSQL->close();
        }
        
    }
?>
<!DOCTYPE html>
<html lang="<?php echo $array_Champs["liste_mots"]['lang']; ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Envoi du courriel avec le lien">
    <!-- Le fichier reset.png est la propriété du site https://pixabay.com/fr/bouton-r%C3%A9initialiser-inscrivez-vous-31199/-->
    <link rel="shortcut icon" href="reset.png">
    <link rel="stylesheet" type="text/css" href="login.css">
    <title><?php echo $array_Champs["liste_mots"]['title']; ?></title>
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
            <p class='titre'><?php echo $array_Champs["liste_mots"]['p1']; ?></p>
            <ul>
                <li class='info'><?php echo $array_Champs["liste_mots"]['li3']; ?></li>
                <li class='info'><?php echo $array_Champs["liste_mots"]['li1']; ?></li>
            </ul>
            <p class='titre un'><?php echo $array_Champs["liste_mots"]['li2']; ?></p>
            <fieldset>
                <legend class="legendCenter"><?php echo $array_Champs["liste_mots"]['legend']; ?></legend>
                <form method="post" action="./createLinkSendMail.php">
                    <div class="connexion">
                        <div class="information <?php if ($array_Champs['erreur_presente']) { echo 'erreur'; } ?>">
                            <label for="email"><?php echo $array_Champs["liste_mots"]['email']; ?></label>
                            <div>
                                <input placeholder="exemple@email.com" autofocus id="email" type="email" name="email" maxlength="50" value="<?php echo $array_Champs['email']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                    </div>
                    <div class="troisBTN">
                        <input class="bouton" type='submit' name='btn_envoi_lien' value="<?php echo $array_Champs["liste_mots"]['btn_envoi_lien']; ?>">
                        <input type='hidden' name='langue' value="<?php echo $array_Champs['type_langue']; ?>">
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="footer">
            <!-- ici la situation sera lorsque l'envoi par courriel sera un succès -->
            <div class='avert <?php if ($array_Champs["situation"] != 6) { echo 'erreur'; } ?>'>
                <p> <?php echo $array_Champs["liste_mots"]['message']; ?> </p>
            </div>
            <div class="btnRetour">
                <form method="post" action="./createLinkSendMail.php">
                    <input class="bouton" type="submit" name="btn_return" value="<?php echo $array_Champs["liste_mots"]['btn_return']; ?>">
                    <input type='hidden' name='langue' value="<?php echo $array_Champs['type_langue']; ?>">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
