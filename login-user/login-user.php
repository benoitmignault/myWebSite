<?php
	// Les includes nécessaires
	use JetBrains\PhpStorm\NoReturn;
	
	include_once("../traduction/traduction-login-user.php");
	include_once("../includes/fct-connexion-bd.php");
	
	/**
	 * Fonction qui va contenir tous ce dont on aura besoin.
	 * Une partie des variables de type string ou integer et une autre partie en boolean
	 * On va ajouter un array pour les mots traduits ou non
	 *
	 * @return array
	 */
	function initialisation(): array {
		
		return array("user" => "", "password" => "", "situation" => 0, "type_langue" => "", "invalid_language" => false,
                     "champs_vide" => false, "champ_vide_user" => false, "champ_vide_pwd" => false,
                     "champs_invalid" => false, "champ_invalid_user" => false, "champ_invalid_pwd" => false, 
                     "user_not_found" => false, "pwd_not_found" => false, "user_admin" => false,
                     "champs_trop_long" => false, "champ_trop_long_user" => false, "champ_trop_long_pwd" => false,
                     "erreur_presente" => false, "id_user" => 0, "liste_mots" => array());
	}
	
	/**
	 * Fonction pour setter les premières informations du GET ou POST
	 * Aussi, on va récupérer via le POST, les informations relier au email du user
	 *
	 * @param array $array_Champs
	 * @return array
	 */
	function remplissage_champs(array $array_Champs): array{
		
        // C,est la seule variable qui sera affectée par le GET
		if ($_SERVER['REQUEST_METHOD'] == 'GET'){
			
            // Exceptionnellement, on va faire une validation ici
			if (isset($_GET['langue'])){
				$array_Champs["type_langue"] = $_GET['langue'];
			}
		}
	
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	
	        // Exceptionnellement, on va faire une validation ici
	        if (isset($_POST['langue'])){
		        $array_Champs["type_langue"] = $_POST['langue'];
	        }
	
	        if (isset($_POST['btn_login'])){
		
		        if (isset($_POST['user'])){
			        $array_Champs["user"] = strtolower($_POST['user']);
		        }
          
		        if (isset($_POST['password'])){
			        $array_Champs["password"] = strtolower($_POST['password']);
		        }
	        }
        }
        
		// Validation commune pour le Get & Post, à propos de la langue
		if ($array_Champs["type_langue"] != "francais" && $array_Champs["type_langue"] != "english"){
			$array_Champs["invalid_language"] = true;
		}
        
		return $array_Champs;
    }

    
    function validation_champs($array_Champs, $connMYSQL) {
        
        if (isset($_POST['btn_sign_up']) || isset($_POST['btn_login'])){
            $array_Champs["user"] = strtolower($_POST['user']);
            $array_Champs["password"] = $_POST['password'];
        }
    
        if (isset($_POST['btn_sign_up'])){
            $array_Champs["email"] = $_POST['email'];
        }
    
        if (empty($array_Champs['user'])){
            $array_Champs['champ_vide_user'] = true;
        }
    
        if (empty($array_Champs['password'])){
            $array_Champs['champ_vide_pwd'] = true;
        }
    
        // Cette validation doit exclure si on pèse sur le bouton btn_login
        if (empty($array_Champs['email']) && isset($_POST['btn_sign_up'])){
            $array_Champs['champVideEmail'] = true;
        }
    
        // Simplification des array_Champs vide pour plutard...
        if (($array_Champs['champ_vide_user'] || $array_Champs['champ_vide_pwd'] || $array_Champs['champVideEmail'])){
            $array_Champs['champs_vide'] = true;
        }
    
        $longueur_user = strlen($array_Champs['user']);
        $longueur_pwd = strlen($array_Champs['password']);
    
        if ($longueur_user > 15) {
            $array_Champs['champ_trop_long_user'] = true;
        }
    
        if ($longueur_pwd > 25){
            $array_Champs['champ_trop_long_pwd'] = true;
        }    
    
        // Simplification des array_Champs trop long pour plutard...
        if ($array_Champs['champ_trop_long_user'] || $array_Champs['champ_trop_long_pwd']){
            $array_Champs['champs_trop_long'] = true;
        }
    
        // On ne doit pas avoir de caractères spéciaux dans l'username
        // ajout du underscore pour le user name
        $pattern_user = "#^[0-9a-z]([0-9a-z_]{0,13})[0-9a-z]$#";
        if (!preg_match($pattern_user, $array_Champs['user'])) {
            $array_Champs['champ_invalid_user'] = true;
        }
    
        // On ne doit pas avoir de caractères spéciaux dans le mot de passe
        $pattern_pwd = "#^[0-9a-zA-Z]([0-9a-zA-Z]{0,23})[0-9a-zA-Z]$#";
        if (!preg_match($pattern_pwd, $array_Champs['password'])) {
            $array_Champs['champ_invalid_pwd'] = true;
        }
    
        if (($array_Champs['champ_invalid_user'] || $array_Champs['champ_invalid_pwd'] || $array_Champs['champInvalidEmail'])){
            $array_Champs['champs_invalid'] = true;
        }
    
        if (!$array_Champs['champ_vide_user'] && !$array_Champs['champ_vide_pwd'] && $array_Champs['user'] == $array_Champs['password']){
            $array_Champs['sameUserPWD'] = true;
        }
    
        // Instauration de la validation si le user et ou email est dejà existant seulement si on veut créer un user
        if (isset($_POST['btn_sign_up'])){
            // Retourner un message erreur si la BD a eu un problème !
    
            // Optimisation pour avoir directement la valeur qui nous intéreste
            $stmt = $connMYSQL->prepare("select user, email from btn_login where user =? OR email =? ");
    
            /* Lecture des marqueurs */
            $stmt->bind_param("ss", $array_Champs['user'], $array_Champs['email']);
    
            /* Exécution de la requête */
            $stmt->execute();
    
            /* Association des variables de résultat */
            $result = $stmt->get_result();
            $row_cnt = $result->num_rows;
            
            if ($row_cnt !== 0){
                foreach ($result as $row) {
                    if ($row['user'] === $array_Champs['user']) {
                        $array_Champs['duplicatUser'] = true;
                    }
    
                    if ($row['email'] === $array_Champs['email']) {
                        $array_Champs['duplicatEmail'] = true;
                    }
                }
            }
            
	        // Close statement
	        $stmt->close();
        }
    
        if ($array_Champs['duplicatEmail'] || $array_Champs['duplicatUser']){
            $array_Champs['duplicate'] = true;
        }
    
        return $array_Champs;
    }
    
    function situation($array_Champs) {
        
        $typeSituation = 0;
        // Début : Section où nous n'avons pas entré dans les fonctions creationUser et connexion_user
        if (!$array_Champs['champ_vide_user'] && $array_Champs['champ_vide_pwd'] && $array_Champs['champVideEmail'] && isset($_POST['btn_sign_up'])) {
            $typeSituation = 1;
        } elseif (!$array_Champs['champ_vide_user'] && !$array_Champs['champ_vide_pwd'] && $array_Champs['champVideEmail'] && isset($_POST['btn_sign_up'])){
            $typeSituation = 2;
        } elseif ($array_Champs['champ_vide_user'] && !$array_Champs['champ_vide_pwd'] && !$array_Champs['champVideEmail'] && isset($_POST['btn_sign_up'])){
            $typeSituation = 3;
        } elseif (!$array_Champs['champ_vide_user'] && $array_Champs['champ_vide_pwd'] && !$array_Champs['champVideEmail'] && isset($_POST['btn_sign_up'])){
            $typeSituation = 4;
        } elseif ($array_Champs['champ_vide_user'] && $array_Champs['champ_vide_pwd'] && !$array_Champs['champVideEmail'] && isset($_POST['btn_sign_up'])){
            $typeSituation = 5;
        } elseif ($array_Champs['champ_vide_user'] && !$array_Champs['champ_vide_pwd'] && $array_Champs['champVideEmail'] && isset($_POST['btn_sign_up'])){
            $typeSituation = 6;
        } elseif (!$array_Champs['champ_vide_user'] && $array_Champs['champ_vide_pwd'] && isset($_POST['btn_login'])){
            $typeSituation = 7;
        } elseif ($array_Champs['champ_vide_user'] && !$array_Champs['champ_vide_pwd'] && isset($_POST['btn_login'])){
            $typeSituation = 8;
            // Fin : Section où nous n'avons pas entré dans les fonctions creationUser et connexion_user
        } elseif ($array_Champs['user_not_found'] && isset($_POST['btn_login'])) {
            $typeSituation = 9;
        } elseif ($array_Champs['pwd_not_found'] && isset($_POST['btn_login'])) {
            $typeSituation = 10;
        } elseif ($array_Champs['sameUserPWD'] && isset($_POST['btn_sign_up'])) {
            $typeSituation = 11;
        } elseif ($array_Champs['duplicatUser'] && isset($_POST['btn_sign_up'])) {
            $typeSituation = 12;
        } elseif ($array_Champs['duplicatEmail'] && isset($_POST['btn_sign_up'])) {
            $typeSituation = 18;
        } elseif ($array_Champs['champs_vide']) {
            $typeSituation = 13;
        } elseif ($array_Champs['champInvalidEmail']) {
            $typeSituation = 17;
        } elseif ($array_Champs['champs_trop_long']) {
            $typeSituation = 14;
        } elseif ($array_Champs['champs_invalid']) {
            $typeSituation = 15;
        } elseif ($array_Champs['creationUserSuccess'] && isset($_POST['btn_sign_up']) ) {
            $typeSituation = 16;
        } elseif (!$array_Champs['creationUserSuccess'] && isset($_POST['btn_sign_up']) ) {
            $typeSituation = 34;
        }
        return $typeSituation; // on retourne seulement un numéro qui va nous servicer dans la fct traduction()
    }
	
	/**
	 * Fonction simplement pour encrypter une information
	 *
	 * @param string $password
	 * @return string
	 */
	function encryptement_password(string $password): string {
		
		return password_hash($password, PASSWORD_BCRYPT);
	}
    
    function connexion_user($array_Champs, $connMYSQL) {
        
        /* Crée une requête préparée */
        $stmt = $connMYSQL->prepare("select user, password from btn_login where user =? ");
    
        /* Lecture des marqueurs */
        $stmt->bind_param("s", $array_Champs['user']);
    
        /* Exécution de la requête */
        $stmt->execute();
    
        /* Association des variables de résultat */
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $row_cnt = $result->num_rows;
    
        /* close statement and connection */
        $stmt->close();
    
        if ($row_cnt == 1){
            if (password_verify($array_Champs['password'], $row['password'])) {
                session_start();
                $_SESSION['user'] = $array_Champs['user'];
                $_SESSION['password'] = $array_Champs['password'];
                $_SESSION['type_langue'] = $array_Champs["type_langue"];
                date_default_timezone_set('America/New_York');
                // Je dois mettre ça si je veux avoir la bonne heure et date dans mon entrée de data
                // Je set un cookie pour améliorer la sécurité pour vérifier que l'user est bien là...2018-12-28
                setcookie("POKER", $_SESSION['user'], time() + 3600, "/");
                $date = date("Y-m-d H:i:s");
    
                if ($row['user'] === "admin") {
                    header("Location: ./statsPoker/administration/gestion-stats.php");
                } else {
                    // Ici, on va saisir une entree dans la BD pour les autres users qui vont vers les statistiques
                    // Prepare an insert statement
                    $sql = "INSERT INTO login_stat_poker (user,date,id_user) VALUES (?,?,?)";
                    $stmt = $connMYSQL->prepare($sql);
    
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param('ssi', $array_Champs['user'], $date, $array_Champs['id_user']);
                    $stmt->execute();
    
                    // Close statement
                    $stmt->close();
                    header("Location: ./statsPoker/poker.php");
                }
                exit;
            } else {
                $array_Champs['pwd_not_found'] = true;
            }
        } else {
            $array_Champs['user_not_found'] = true;
        }
        return $array_Champs;
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
                
                
                
                //
                if (isset($_POST['btn_sign_up'])) {
	                // Une demande de création de compte est demandé
                } elseif (isset($_POST['btn_reset'])) {
                    if ($type_langue === 'english') {
                        header("Location: /btn_login-user/reset-pwd/create-email-temp-pwd.php?langue=english");
                    } elseif ($type_langue === 'francais') {
                        header("Location: /btn_login-user/reset-pwd/create-email-temp-pwd.php?langue=francais");
                    }
	                
                } elseif (isset($_POST['btn_return'])) {
	                // Une demande pour quitter la page de connexion, pour retourner à accueil
	                if ($type_langue === 'english') {
		                header("Location: /english/english.html");
	                } elseif ($type_langue === 'francais') {
		                header("Location: /index.html");
	                }
                } elseif (isset($_POST['btn_login'])) {
	                // La connexion aux statistique a réussi soit avec un user admin ou un user normal
                }
			}
		}
		
		exit; // On va sortir ici, après avoir loader la bonne page web
	}
 
	// Les fonctions communes
	$connMYSQL = connexion();
	$array_Champs = initialisation();
	$array_Champs = remplissage_champs($array_Champs);
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	
	    // Si la langue n'est pas setter, on va rediriger vers la page Err 404
	    if ($array_Champs["invalid_language"]) {
		    redirection("", false); // On n'a pas besoin de cette variable
	    }
    } // Fin du GET pour faire afficher la page web
	
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	    
        if (isset($_POST['btn_sign_up']) || isset($_POST['btn_return']) || isset($_POST['btn_reset_pwd'])) {
	        redirection($array_Champs["type_langue"], $array_Champs["invalid_language"]); // On n'a pas besoin de cette variable
	
	        // Si le bouton se connecter est pesé...
        } elseif (isset($_POST['btn_login'])) {
	            $array_Champs = validation_champs($array_Champs, $connMYSQL);
                // Comme j'ai instauré une foreign key entre la table login_stat_poker vers btn_login je dois aller récupérer id pour l'insérer avec la nouvelle combinaison
                /* Crée une requête préparée */
                $stmt = $connMYSQL->prepare("select id from btn_login where user =?");
    
                /* Lecture des marqueurs */
                $stmt->bind_param("s", $array_Champs["user"]);
    
                /* Exécution de la requête */
                $stmt->execute();
    
                /* Association des variables de résultat */
                $result = $stmt->get_result();
    
                $row = $result->fetch_array(MYSQLI_ASSOC);
    
                // Close statement
                $stmt->close();
    
                $array_Champs["id_user"] = $row["id"];	// Assignation de la valeur
                if (!$array_Champs["champs_vide"] && !$array_Champs["champs_trop_long"] && !$array_Champs["champs_invalid"] ) {
                    $array_Champs = connexion_user($array_Champs, $connMYSQL);
                }
	        // Ici on va modifier la valeur de la variable situation pour faire afficher le message approprié
            $array_Champs["situation"] = situation($array_Champs);
        }
    }
    // On va faire la traduction, à la fin des GEt & POST
	// La variable de situation est encore à 0 pour le GET, donc aucun message
	$array_Champs["liste_mots"] = traduction($array_Champs["type_langue"], $array_Champs["situation"]);
 
	$connMYSQL->close();
?>
<!DOCTYPE html>
<html lang="<?php echo $array_Champs["liste_mots"]['lang']; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page de connexion">
    <!-- Le fichier btn_login.png est la propriété du site https://pixabay.com/fr/ic%C3%B4nes-symboles-bouton-842844/ mais en utilisation libre-->
    <link rel="shortcut icon" href="login-user-icone.png">
    <link rel="stylesheet" type="text/css" href="login-user.css">
    <title><?php echo $arrayMots['title']; ?></title>
    <style>
        body {
            margin: 0;
            /* Fichier photoPoker.jpg est une propriété du site https://pixabay.com/fr/syst%C3%A8me-r%C3%A9seau-actualit%C3%A9s-connexion-2457651/ sous licence libre */
            background-image: url("login-background.jpg");
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
                <form method="post" action="login-user.php">
                    <div class="connexion">
                        <div class="information <?php if ($array_Champs['sameUserPWD'] || $array_Champs['champ_vide_user'] || $array_Champs['champ_invalid_user'] || $array_Champs['duplicatUser'] || $array_Champs['user_not_found'] || $array_Champs['champ_trop_long_user']) { echo 'erreur'; } ?>">
                            <label for="user"><?php echo $arrayMots['usager']; ?></label>
                            <div>
                                <input autofocus id="user" type="text" name="user" maxlength="15" value="<?php echo $array_Champs['user']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if ($array_Champs['sameUserPWD'] || $array_Champs['pwd_not_found'] || $array_Champs['champ_vide_pwd'] || $array_Champs['champ_invalid_pwd'] || $array_Champs['champ_trop_long_pwd']) { echo 'erreur';} ?>">
                            <label for="password"><?php echo $arrayMots['mdp']; ?></label>
                            <div>
                                <input id="password" type='password' maxlength="25" name="password" value="<?php echo $array_Champs['password']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                    </div>
                    <div class="section-reset-btn">
                        <input class="bouton" type='submit' name='btn_login' value="<?php echo $arrayMots['btn_login']; ?>">
                        <input class="bouton" type='submit' name='btn_sign_up' value="<?php echo $arrayMots['btn_signUp']; ?>">
                        <input class="bouton" id="faire_menage_total" type="reset" value="Effacer...">
                        <input class="bouton" type='submit' name='btn_reset' value="<?php echo $arrayMots['btn_reset']; ?>">
                        <input type='hidden' name='langue' value="<?php echo $array_Champs['type_langue']; ?>">
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="footer">
            <div class='avert <?php if ($array_Champs["situation"] != 16) { echo 'erreur'; } ?>'>
                <p> <?php echo $arrayMots['message']; ?> </p>
            </div>
            <div class="section-retour-btn">
                <form method="post" action="login-user.php">
                    <input class="bouton" type="submit" name="btn_return" value="<?php echo $arrayMots['btn_return']; ?>">
                    <input type='hidden' name='langue' value="<?php echo $array_Champs['type_langue']; ?>">
                </form>
            </div>
        </div>
    </div>
</body>
</html>