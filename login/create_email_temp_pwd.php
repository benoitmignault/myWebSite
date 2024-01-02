<?php
    // Les includes nécessaires
	include_once("../traduction/traduction_create_link_reset.php");
	include_once("../includes/fct-connexion-bd.php");
    
    // Import PHPMailer classes into the global namespace
	use JetBrains\PhpStorm\NoReturn;
	use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Load Composer's autoloader
    require '../../vendor/autoload.php';

    // Lorsque je suis en mode DEV :
	// require '../PHPMailer/src/Exception.php';
	// require '../PHPMailer/src/PHPMailer.php';
	// require '../PHPMailer/src/SMTP.php';
	
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
                     "password_temp" => "", "lien_reset_pwd" => "", "envoi_courriel_succes" => false, "envoi_courriel_echec" => false,
                     "reset_existant" => false, "message_erreur_bd" => "", "invalid_language" => false, "liste_mots" => array());
    }
    
    /**
     * Fonction pour setter les premières informations du GET ou POST
     * Aussi, on va récupérer via le POST, les informations relier au email du user
     *
     * @param array $array_Champs
     * @param object $connMYSQL
     * @return array
     */
    function remplisage_champs(array $array_Champs, object $connMYSQL): array{
        
        if ($_SERVER['REQUEST_METHOD'] == 'GET'){
            // Exceptionnellement, on va faire une validation ici
            if (isset($_GET['langue'])){
                $array_Champs["type_langue"] = $_GET['langue'];
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	        // Exceptionnellement, on va faire une validation ici
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
                        
                        // Récupération des informations
                        $result_info = verification_existance_courriel($array_Champs['email'], $connMYSQL);
                        
                        // Attribution des informations
                        if (count($result_info) > 0){
                            $array_Champs["user"] = $result_info['user'];
                            $array_Champs["temps_valide_link"] = intval($result_info['temps_valide_link']);
                        }
                    }
                }
            }
        }
        // Validation commune pour le Get & Post, à propos de la langue
        if ($array_Champs["type_langue"] != "francais" && $array_Champs["type_langue"] != "anglais"){
	        $array_Champs["invalid_language"] = true;
        }
        
        return $array_Champs;
    }
    
    /**
     * Fonction pour aller chercher l'information concernant la demande de reset de password via l'email.
     * Je dois associer le résultat à une variable avant de fermer la connexion ouvert pour cette requête
     * On va retourner le résultat sous forme array rempli ou non
     *
     * @param string $email
     * @param object $connMYSQL
     * @return array
     */
    function verification_existance_courriel(string $email, object $connMYSQL): array{
        
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
        $stmt->bind_param("s", $email);
        
        /* Exécution de la requête */
        $stmt->execute();
        $result = $stmt->get_result();
        
        $result_info = array();
        
        // Il ne peut qu'avoir un seul résultat possible vue l'unicité de la Table SQL
        if ($result->num_rows === 1){
            $result_info = $result->fetch_array(MYSQLI_ASSOC);
        }
     
        /* close statement and connection */
        $stmt->close();
        
        return $result_info;
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
    
            // [:alnum:] -> a-zA-Z0-9
            // [:alpha:] -> a-zA-Z
            $pattern_email = "#^[[:alnum:]._-]+@[[:alnum:]._-]+\.[[:alpha:]]{2,4}$#";
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
            $type_situation = 1;
            
        } elseif ($array_Champs['champ_trop_long']){
            $type_situation = 2;
            
        } elseif ($array_Champs['champ_invalid']){
            $type_situation = 3;
            
        } elseif ($array_Champs['email_inexistant_bd']){
            $type_situation = 4;
            // Ajout de cette nouvelle situation - 2023-12-06
        } elseif ($array_Champs['reset_existant']){
            $type_situation = 8;
         
        } elseif ($array_Champs['erreur_system_bd']){
            $type_situation = 5;
            
        } elseif ($array_Champs['envoi_courriel_succes']){
            // Normalement, ici, ça veut que dire que nous avons un succès
            $type_situation = 6;
            
        } else {
            // Rendu ici, on va caller une erreur système
            $type_situation = 7;
        }
    
        return $type_situation;
    }
    
    /**
     * @param array $array_Champs
     * @return array
     */
    function gestion_lien_courriel(array $array_Champs): array{
     
        // Création de l'instance
        $mail = creation_instance_courriel();
        try {
            // Venant de qui et pour qui
            $mail->setFrom('home@benoitmignault.ca', 'Site Web Benoit Mignault');
            $mail->addAddress($array_Champs['email'], $array_Champs['user']);
            
            // Préparation pour l'object et le corp du message, en fonction de la langue
            $mail->isHTML(); // par défaut is true
            $mail->Subject = preparation_object_courriel($array_Champs["type_langue"]);
            $mail->Body = preparation_contenu_courriel($array_Champs["type_langue"], $array_Champs["lien_reset_pwd"], $array_Champs["user"], $array_Champs["password_temp"]);
            
            // Envoyer l'e-mail
            $mail->send();
            $array_Champs["envoi_courriel_succes"] = true;
        } catch (Exception) {
      
            $array_Champs["envoi_courriel_echec"] = true;
            $array_Champs["liste_mots"]["message"] = $mail->ErrorInfo;
        } finally {
            // Fermer la connexion SMTP
            $mail->SmtpClose();
        }
        
        return $array_Champs;
    }
    
    /**
     * Fonction pour créer l'instant de connexion au serveur de courriel GMAIL.
     * On va utiliser une adresse courriel spéciale prévue à cet effet
     *
     * @return PHPMailer
     */
    function creation_instance_courriel(): PHPMailer {
    
        //  Préparation du lien pour le courriel, avec true pour gérer les exceptions
        $mail = new PHPMailer(true);
    
        // Initialisation des variables, pour éviter des fausses erreurs de IntelliJ
        // Venant du fichier info-connexion-email.php
		$user_email = "";
		$password_email = "";

        // Les includes nécessaires
        include_once("../includes/info-connexion-email.php");
        
        // Paramètres du serveur SMTP
        $mail->SMTPDebug = 0; // 2 Pour voir le mode debug des messages erreurs
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = $user_email;
        $mail->Password   = $password_email;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
    
        return $mail;
    }
    
    /**
     * Fonction pour déterminer comment on va setter l'object du courriel
     *
     * @param string $type_langue
     * @return string
     */
    function preparation_object_courriel(string $type_langue): string {
        
        $message_object = "";
        if ($type_langue === 'francais') {
            $message_object = "Voici votre courriel de changement de mot de passe.";
            
        } elseif ($type_langue === 'english') {
            $message_object = "Here is your password change email.";
        }
     
        return $message_object;
    }
    
    /**
     * Fonction pour créer le corps du courriel en fonction de la langue de l'utilisateur et
     * les informations relier un changement de password.
     *
     * @param string $type_langue
     * @param string $lien_reset_pwd
     * @param string $user
     * @param string $password_temp
     * @return string
     */
    function preparation_contenu_courriel(string $type_langue, string $lien_reset_pwd, string $user, string $password_temp): string {
     
        $contenu_courriel = "";
     
        if ($type_langue === 'francais') {
            $lien = "Cliquer ici";
            $contenu_courriel .= "<html lang=\"fr\">";
            $contenu_courriel .= "<head><title>Changement de Mot de Passe</title><meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
            $contenu_courriel .= "<body style='font-family: Arial, sans-serif; background-color: #D3D3D3; margin-top: 0; font-size: 16px;'><p>Bonjour !</p>
                                  <p>Ceci est un courriel de courtoisie pour vous permettre de changer votre mot de passe
                                     pour faire de nouvelles consultations des statistiques de poker.</p>
                                    <table style='border-collapse: collapse; border: 2px solid #666; padding: 10px;'>
                                        <tr><td style='border: 1px solid #666; padding: 10px;'><span style='font-weight: bold;'>Lien Web :</span> </td><td style='border: 1px solid #666; padding: 10px;'><a target=\"_blank\" href=\"https://benoitmignault.ca/login/reset.php?key=$lien_reset_pwd&langue=$type_langue\">$lien</a></td></tr>
                                        <tr><td style='border: 1px solid #666; padding: 10px;'><span style='font-weight: bold;'>Nom Utilisateur :</span> </td><td style='border: 1px solid #666; padding: 10px;'>" . $user . "</td></tr>
                                        <tr><td style='border: 1px solid #666; padding: 10px;'><span style='font-weight: bold;'>Mot de Passe (<span style='color: red'>temporaire</span>) :</span> </td><td style='border: 1px solid #666; padding: 10px;'>" . $password_temp . "</td></tr>
                                        <tr><td style='border: 1px solid #666; padding: 10px;'><span style='font-weight: bold;'>Temps accordé pour le changement :</span> </td><td style='border: 1px solid #666; padding: 10px;'>12 heures</td></tr>
                                    </table>";
            $contenu_courriel .= "<p style='text-align: left'>Bonne journée</p><p style='text-align: left'>L'Équipe de Gestion BenoitMignault.ca</p>";
            
        } elseif ($type_langue === 'english') {
            $lien = "Click here";
            $contenu_courriel .= "<html lang=\"fr\">";
            $contenu_courriel .= "<head><title>Password change</title><meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
            $contenu_courriel .= "<body style='font-family: Arial, sans-serif; background-color: #D3D3D3; margin-top: 0; font-size: 16px;'><p>Hello !</p>
                                  <p>This is a courtesy email to allow you to change your password to make new consultations of poker statistics.</p>
                                    <table style='border-collapse: collapse; border: 2px solid #666; padding: 10px;'>
                                        <tr><td style='border: 1px solid #666; padding: 10px;'><span style='font-weight: bold;'>Web link :</span> </td><td style='border: 1px solid #666; padding: 10px;'><a target=\"_blank\" href=\"https://benoitmignault.ca/login/reset.php?key=$lien_reset_pwd&langue=$type_langue\">$lien</a></td></tr>
                                        <tr><td style='border: 1px solid #666; padding: 10px;'><span style='font-weight: bold;'>Username :</span> </td><td style='border: 1px solid #666; padding: 10px;'>" . $user . "</td></tr>
                                        <tr><td style='border: 1px solid #666; padding: 10px;'><span style='font-weight: bold;'>Password (<span style='color: red'>temporary</span>) :</span> </td><td style='border: 1px solid #666; padding: 10px;'>" . $password_temp . "</td></tr>
                                        <tr><td style='border: 1px solid #666; padding: 10px;'><span style='font-weight: bold;'>Time allowed for change :</span> </td><td style='border: 1px solid #666; padding: 10px;'>12 hours</td></tr>
                                    </table>";
            $contenu_courriel .= "<p style='text-align: left'>Good day</p><p style='text-align: left'>The Management Team BenoitMignault.ca</p>";
        }
     
        $contenu_courriel .= '</body></html>';
        
        return $contenu_courriel;
    }
    
    /**
     * On va créer un lien encrypté envoyer par courriel.
     * Ensuite un password temporaire envoyer par courriel et sa version encrypté vers ls BD
     * Pour finir, un temps valide pour 12 heures envoyer vers la BD
     *
     * @param object $connMYSQL
     * @param array $array_Champs
     * @return array
     */
    function creation_lien_password_temporaire(object $connMYSQL, array $array_Champs): array{
        
        date_default_timezone_set('America/New_York');
        // Création du lien
        $lien_reset_pwd = $array_Champs['user'] . "/*-+!/$%?&*()" . $array_Champs['email'];
        $array_Champs["lien_reset_pwd"] = encryptement_password($lien_reset_pwd);
        
        // Création du password temporaire
        $array_Champs["password_temp"] = generate_random_string(10);
        $password_secure = encryptement_password($array_Champs["password_temp"]);
        
        // On ajoute 12 heures au moment où l'utilisateur créer sa demande
        $temps_valide_link = strtotime("+12 hour", strtotime(date("Y-m-d H:i:s")));
        
        // Préparation de la requête UPDATE
        $update = "UPDATE login ";
        $set = "SET reset_link = ? , password_temp = ?, temps_valide_link = ? ";
        $where = "WHERE user = ?";
     
        // Préparation de la requête SQL avec un alias pour la colonne sélectionnée
        $query = $update . $set . $where;
     
        // Préparation de la requête
        $stmt = $connMYSQL->prepare($query);
        try {
            /* Lecture des marqueurs */
            $stmt->bind_param("ssis", $array_Champs["lien_reset_pwd"], $password_secure, $temps_valide_link, $array_Champs['user']);
      
            /* Exécution de la requête */
            $stmt->execute();
        } catch (Exception $err){
            // Récupérer les messages d'erreurs
            $array_Champs["message_erreur_bd"] = $err->getMessage();
        } finally {
            // Fermer la préparation de la requête
            $stmt->close();
        }
        
        return $array_Champs;
    }
    
    /**
     * Fonction pour générer une chaine de caractère qui sera utiliser pour le password temporaire
     *
     * @param int $length
     * @return string
     */
    function generate_random_string(int $length): string {
        
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen($characters);
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[rand(0, $characters_length - 1)];
        }
        
        return $random_string;
    }
    
    /**
     * Fonction simplement pour encrypter une information
     *
     * @param string $password_Temp
     * @return string
     */
    function encryptement_password(string $password_Temp): string {
        
        return password_hash($password_Temp, PASSWORD_BCRYPT);
    }
    
    /**
     * Fonction pour rediriger vers la bonne page page extérieur à la page du reset de password
     * En fonction aussi si le type de langue est valide
     *
     * @param string $type_langue
     * @param bool $invalid_language
     * @return void
     */
    #[NoReturn] function redirection(string $type_langue, bool $invalid_language): void {
        
        // Si nous arrivons ici via le GET, nous avons un problème majeur, donc on call la page 404
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header("Location: /erreur/erreur.php");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            if ($invalid_language){
	            header("Location: /erreur/erreur.php");
             
            } else {
	            if (isset($_POST['btn_return']) && $type_langue === "francais") {
		            header("Location: /index.html");
	            }
	
	            if (isset($_POST['btn_return']) && $type_langue === "english") {
		            header("Location: /english/english.html");
	            }
            }
        }
        
        exit; // pour arrêter l'exécution du code php
    }

    // Les fonctions communes
    $connMYSQL = connexion();
    $array_Champs = initialisation();
    $array_Champs = remplisage_champs($array_Champs, $connMYSQL);

    // Ce qui arrive lorsqu'on arrive sur la page pour générer un lien de reset de password
    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    
        // Si la langue n'est pas setter, on va rediriger vers la page Err 404
        if ($array_Champs["invalid_language"]) {
            redirection("", false); // On n'a pas besoin des variables
        } else {
            // La variable de situation est encore à 0 vue qu'il s'est rien passé de grave...
            $array_Champs["liste_mots"] = traduction($array_Champs["type_langue"], $array_Champs["situation"]);
        }
    } // Fin du GET pour faire afficher la page web

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        if (isset($_POST['btn_return']))  {
            redirection($array_Champs["type_langue"], $array_Champs["invalid_language"]);
            
        } elseif (isset($_POST['btn_envoi_lien'])) {
	
	        $array_Champs = validation_champs($array_Champs);
            if (!$array_Champs['erreur_presente']){
             
	            // Récupération des informations de la création du lien et password temporaire
	            $array_Champs = creation_lien_password_temporaire($connMYSQL, $array_Champs);
                
                // Utilisation de cette fonction pour appeler les fonctions nécessaires pour le courriel
                $array_Champs = gestion_lien_courriel($array_Champs);
            }
            
            // Si nous avons eu une erreur dans l'envoi du courriel, nous allons récupérer le message d'erreur spécifique
            if (!$array_Champs["envoi_courriel_echec"]){
	            $array_Champs["situation"] = situation($array_Champs);
            }
            
            $array_Champs["liste_mots"] = traduction($array_Champs["type_langue"], $array_Champs["situation"]);
        }
    }
    // Fermeture de la connexion sur les BD du serveur
	$connMYSQL->close();
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
                <legend class="legend-center"><?php echo $array_Champs["liste_mots"]['legend']; ?></legend>
                <form method="post" action="./create_email_temp_pwd.php">
                    <div class="connexion">
                        <div class="information <?php if ($array_Champs['erreur_presente']) { echo 'erreur'; } ?>">
                            <label for="email"><?php echo $array_Champs["liste_mots"]['email']; ?></label>
                            <div>
                                <input placeholder="exemple@email.com" autofocus id="email" type="email" name="email" maxlength="50" value="<?php echo $array_Champs['email']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                    </div>
                    <div class="section-reset-btn">
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
            <div class="section-retour-btn">
                <form method="post" action="./create_email_temp_pwd.php">
                    <input class="bouton" type="submit" name="btn_return" value="<?php echo $array_Champs["liste_mots"]['btn_return']; ?>">
                    <input type='hidden' name='langue' value="<?php echo $array_Champs['type_langue']; ?>">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
