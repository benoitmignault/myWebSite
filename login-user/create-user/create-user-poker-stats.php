<?php
	// Pour éviter de dupliquer des fichiers de back-end, on va utiliser les fichiers css, js de login-user
    
    // Les includes nécessaires
	use JetBrains\PhpStorm\NoReturn;
 
	include_once("../../traduction/traduction-login-user.php");
	include_once("../../includes/fct-connexion-bd.php");
	
	/**
	 * Fonction qui va contenir tous ce dont on aura besoin.
	 * Une partie des variables de type string ou int et une autre partie en boolean
	 * On va ajouter un array pour les mots traduits ou non
	 *
	 * @return array
	 */
	function initialisation(): array {
		
		return array("user" => "", "email" => "", "password" => "", "password_conf" => "", "situation" => 0, "type_langue" => "",
                     "invalid_language" => false, "message_erreur_bd" => "", "erreur_system_bd" => false, "erreur_presente" => false,
                     "champ_vide_user" => false, "champ_vide_email" => false, "champ_vide_pwd" => false, "champ_vide_pwd_conf" => false,
                     "champ_trop_long_user" => false, "champ_trop_long_email" => false, "champ_trop_long_pwd" => false, "champ_trop_long_pwd_conf" => false,
                     "champ_trop_court_user" => false, "champ_trop_court_pwd" => false, "champ_trop_court_pwd_conf" => false,
                     "champ_invalid_user" => false, "champ_invalid_email" => false, "champ_invalid_pwd" => false, "champ_invalid_pwd_conf" => false,
                     "champs_vide" => false, "champs_trop_long" => false, "champs_trop_court" => false, "champs_invalid" => false,
                     "champs_pwd_not_equal" => false, "champs_user_pwd_equal" => false, "duplicate_user" => false, "duplicate_email" => false,
                     "duplicates" => false, "create_user_success" => false, "liste_mots" => array());
	}
	
	/**
	 * Fonction pour setter les premières informations du GET ou POST
	 * Aussi, on va récupérer via le POST, les informations suivantes :
     * - username
     * - email
     * - password
     * - password confirmation
     *
	 * @param array $array_Champs
	 * @return array
	 */
	function remplissage_champs(array $array_Champs): array{
		
		// C'est la seule variable qui sera affectée par le GET
		if ($_SERVER['REQUEST_METHOD'] == 'GET'){
			
			if (isset($_GET['langue'])){
				$array_Champs["type_langue"] = $_GET['langue'];
			}
		}
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			
			// Exceptionnellement, on va faire une validation ici
			if (isset($_POST['langue'])){
				$array_Champs["type_langue"] = $_POST['langue'];
			}
			
			if (isset($_POST['btn_sign_up'])){
				
				if (isset($_POST['user'])){
					// On met tous en minuscule pour gérer la suite des choses
					$array_Champs["user"] = strtolower($_POST['user']);
				}
				
				if (isset($_POST['email'])){
					// On met tous en minuscule pour gérer la suite des choses
					$array_Champs["email"] = $_POST['email'];
				}
				
				if (isset($_POST['password'])){
					// On met tous en minuscule pour gérer la suite des choses
					$array_Champs["password"] = $_POST['password'];
				}
    
				if (isset($_POST['password_conf'])){
					// On met tous en minuscule pour gérer la suite des choses
					$array_Champs["password_conf"] = $_POST['password_conf'];
				}
			}
		}
		
		// Exceptionnellement, on va faire une validation ici
		// Validation commune pour le Get & Post, à propos de la langue
		if ($array_Champs["type_langue"] != "francais" && $array_Champs["type_langue"] != "english"){
			$array_Champs["invalid_language"] = true;
		}
		
		return $array_Champs;
	}
	
	/**
	 * Fonction qui servira à mettre à «True» les variables de contrôles des informations
	 * que nous avons associé durant la fonction @see remplissage_champs
     *
     * @param mysqli $connMYSQL
	 * @param array $array_Champs
	 * @return array
	 */
	function validation_champs(mysqli $connMYSQL, array $array_Champs): array {
        
        if (empty($array_Champs['user'])){
            $array_Champs['champ_vide_user'] = true;
	        $array_Champs['erreur_presente'] = true;
        }
		
		if (empty($array_Champs['email']) ){
			$array_Champs['champ_vide_email'] = true;
			$array_Champs['erreur_presente'] = true;
		}
        
        if (empty($array_Champs['password'])){
            $array_Champs['champ_vide_pwd'] = true;
	        $array_Champs['erreur_presente'] = true;
        }
		
		if (empty($array_Champs['password_conf'])){
			$array_Champs['champ_vide_pwd_conf'] = true;
			$array_Champs['erreur_presente'] = true;
		}
		
		// Activation de la variable de contrôle
		if ($array_Champs['champ_vide_user'] && $array_Champs['champ_vide_email'] &&
            $array_Champs['champ_vide_pwd'] && $array_Champs['champ_vide_pwd_conf']){
			$array_Champs['champs_vide'] = true;
		}
        
        // Vérification des longueurs
        $longueur_user = strlen($array_Champs['user']);
        $longueur_email = strlen($array_Champs['email']);
        $longueur_pwd = strlen($array_Champs['password']);
        $longueur_pwd_conf = strlen($array_Champs['password_conf']);
        
    
        if ($longueur_user > 15) {
            $array_Champs['champ_trop_long_user'] = true;
            
        } elseif ($longueur_user < 4) {
            $array_Champs['champ_trop_court_user'] = true;
	    }
        
		if ($longueur_email > 50){
			$array_Champs['champ_trop_long_email'] = true;
		}
        
        if ($longueur_pwd > 25){
            $array_Champs['champ_trop_long_pwd'] = true;
	
        } elseif ($longueur_pwd < 8) {
	        $array_Champs['champ_trop_court_pwd'] = true;
        }
		
		if ($longueur_pwd_conf > 25){
			$array_Champs['champ_trop_long_pwd_conf'] = true;
			
		} elseif ($longueur_pwd_conf < 8) {
			$array_Champs['champ_trop_court_pwd_conf'] = true;
		}
        
        if ($array_Champs['champ_trop_long_user'] || $array_Champs['champ_trop_long_email'] ||
            $array_Champs['champ_trop_long_pwd'] || $array_Champs['champ_trop_long_pwd_conf']){
            
            $array_Champs['champs_trop_long'] = true;
	        $array_Champs['erreur_presente'] = true;
        }
		
		if ($array_Champs['champ_trop_court_user'] || $array_Champs['champ_trop_court_pwd'] || $array_Champs['champ_trop_court_pwd_conf']) {
			
            $array_Champs['champs_trop_court'] = true;
			$array_Champs['erreur_presente'] = true;
        }
    
        // On ne doit pas avoir de caractères spéciaux dans l'username
		$pattern_user = "#^[0-9a-z][0-9a-z]{1,13}[0-9a-z]$#";
		if (!preg_match($pattern_user, $array_Champs['user'])) {
			$array_Champs['champ_invalid_user'] = true;
		}
		
		$pattern_email = "#^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$#";
		if (!preg_match($pattern_email, $array_Champs['email'])) {
			$array_Champs['champ_invalid_email'] = true;
   
			// https://stackoverflow.com/questions/11952473/proper-prevention-of-mail-injection-in-php/11952659#11952659
		} elseif (!(filter_var($array_Champs['email'], FILTER_VALIDATE_EMAIL))){
			$array_Champs['champ_invalid_email'] = true;
		}
        
        // On ne doit pas avoir de caractères spéciaux dans le mot de passe
        $pattern_pwd = "#^[0-9a-zA-Z][0-9a-zA-Z]{0,23}[0-9a-zA-Z]$#";
        if (!preg_match($pattern_pwd, $array_Champs['password'])) {
            $array_Champs['champ_invalid_pwd'] = true;
        }
		
		if (!preg_match($pattern_pwd, $array_Champs['password_conf'])) {
			$array_Champs['champ_invalid_pwd_conf'] = true;
		}
    
        if ($array_Champs['champ_invalid_user'] || $array_Champs['champ_invalid_email'] ||
            $array_Champs['champ_invalid_pwd'] || $array_Champs['champ_invalid_pwd_conf']){
            
            $array_Champs['champs_invalid'] = true;
	        $array_Champs['erreur_presente'] = true;
        }
        
        // Si les password ne sont pas vides, valider qu'ils sont pareil, sinon erreur
        if (!$array_Champs['champ_vide_pwd'] && !$array_Champs['champ_vide_pwd_conf']){
            
            if ($array_Champs['password'] !== $array_Champs['password_conf']){
	            $array_Champs['champs_pwd_not_equal'] = true;
                $array_Champs['erreur_presente'] = true;
            }
        }
        
        // Si l'erreur des password non identique cest fausse, on valide qu'on aurait peut-être écrit l'username et password pareil
        if (!$array_Champs['champs_pwd_not_equal']){
            
            if ($array_Champs['user'] === $array_Champs['password']){
	            $array_Champs['champs_user_pwd_equal'] = true;
	            $array_Champs['erreur_presente'] = true;
            }
        }
		
        // On peut seulement mettre à vrai deux variables de contrôles + les erreurs de BD
		$array_Champs = requete_SQL_verif_user_email_existant($connMYSQL, $array_Champs);
		
        if ($array_Champs['duplicate_user'] || $array_Champs['duplicate_email']){
            $array_Champs['duplicates'] = true;
	        $array_Champs['erreur_presente'] = true;
        }
    
        return $array_Champs;
    }
	
	/**
	 * Fonction qui servira à valider si l'user OU l'email existent dans la BD
     * Cette fonction sera @see validation_champs
	 *
	 * @param mysqli $connMYSQL
	 * @param array $array_Champs
	 * @return array
	 */
	function requete_SQL_verif_user_email_existant(mysqli $connMYSQL, array $array_Champs) : array {
		
		$select = "SELECT USER, EMAIL ";
		$from = "FROM login ";
		$where = "WHERE user = ? OR email = ?";
		
		// Préparation de la requête SQL avec les parties nécessaires
		$query = $select . $from . $where;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		try {
			/* Lecture des marqueurs */
			$stmt->bind_param("ss", $array_Champs['user'], $array_Champs['email']);
			
			/* Exécution de la requête */
			$stmt->execute();
		} catch (Exception $err){
			// Récupérer les messages d'erreurs
			$array_Champs["message_erreur_bd"] = $err->getMessage();
			
			// Sera utilisée pour faire afficher le message erreur spécial
			$array_Champs["erreur_system_bd"] = true;
		} finally {
			/* Association des variables de résultat */
			$result = $stmt->get_result();
   
			// Close statement
			$stmt->close();
		}
		
		// Retourne l'information des informations si le user ou email existent déjà
		return recuperation_info($connMYSQL, $result, $array_Champs);
	}
	
	/**
	 * Fonction pour récupérer les informations du user et du courriel, existant peut-être
	 * Cette fonction sera @see requete_SQL_verif_user_email_existant
	 *
	 * @param mysqli $connMYSQL -> Doit être présent pour utiliser les notions de MYSQLi
	 * @param object $result -> Le résultat de la requête SQL via la table « login »
	 * @param array $array_Champs
	 * @return array
	 */
	function recuperation_info(mysqli $connMYSQL, object $result, array $array_Champs): array {
		
		// Récupération des résultats possibles
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        
            // Il peut avoir jusqu'à un max de deux résultats
            if ($row["USER"] === $array_Champs['user']){
	            $array_Champs["duplicate_user"] = true;
            }
	
	        if ($row["EMAIL"] === $array_Champs['email']){
		        $array_Champs["duplicate_email"] = true;
	        }
        }
		
		return $array_Champs;
	}
	
	
	function creationUser($array_Champs, $connMYSQL) {
		$passwordCrypter = encryptementPassword($array_Champs['password']);
		// Prepare an insert statement
		$sql = "INSERT INTO login (user, password, email) VALUES (?,?,?)";
		$stmt = $connMYSQL->prepare($sql);
		
		// Bind variables to the prepared statement as parameters
		$stmt->bind_param('sss', $array_Champs['user'], $passwordCrypter, $array_Champs['email']);
		$stmt->execute();
		
		if ($stmt->affected_rows == 1){
			$array_Champs['creationUserSuccess'] = true;
		}
		
		// Close statement
		$stmt->close();
		return $array_Champs;
	}
	
	
	
	
	
	
	// Ajouter une situation ou plusieurs si le email est deja utilise par quelqu'un autre
    function situation($array_Champs) {
        $typeSituation = 0;
        // Début : Section où nous n'avons pas entré dans les fonctions creationUser et connexionUser
        if (!$array_Champs['champVideUser'] && $array_Champs['champVidePassword'] && $array_Champs['champVideEmail'] && isset($_POST['signUp'])) {
            $typeSituation = 1;
        } elseif (!$array_Champs['champVideUser'] && !$array_Champs['champVidePassword'] && $array_Champs['champVideEmail'] && isset($_POST['signUp'])){
            $typeSituation = 2;
        } elseif ($array_Champs['champVideUser'] && !$array_Champs['champVidePassword'] && !$array_Champs['champVideEmail'] && isset($_POST['signUp'])){
            $typeSituation = 3;
        } elseif (!$array_Champs['champVideUser'] && $array_Champs['champVidePassword'] && !$array_Champs['champVideEmail'] && isset($_POST['signUp'])){
            $typeSituation = 4;
        } elseif ($array_Champs['champVideUser'] && $array_Champs['champVidePassword'] && !$array_Champs['champVideEmail'] && isset($_POST['signUp'])){
            $typeSituation = 5;
        } elseif ($array_Champs['champVideUser'] && !$array_Champs['champVidePassword'] && $array_Champs['champVideEmail'] && isset($_POST['signUp'])){
            $typeSituation = 6;
        } elseif (!$array_Champs['champVideUser'] && $array_Champs['champVidePassword'] && isset($_POST['login'])){
            $typeSituation = 7;
        } elseif ($array_Champs['champVideUser'] && !$array_Champs['champVidePassword'] && isset($_POST['login'])){
            $typeSituation = 8;
            // Fin : Section où nous n'avons pas entré dans les fonctions creationUser et connexionUser
        } elseif ($array_Champs['badUser'] && isset($_POST['login'])) {
            $typeSituation = 9;
        } elseif ($array_Champs['badPassword'] && isset($_POST['login'])) {
            $typeSituation = 10;
        } elseif ($array_Champs['sameUserPWD'] && isset($_POST['signUp'])) {
            $typeSituation = 11;
        } elseif ($array_Champs['duplicatUser'] && isset($_POST['signUp'])) {
            $typeSituation = 12;
        } elseif ($array_Champs['duplicatEmail'] && isset($_POST['signUp'])) {
            $typeSituation = 18;
        } elseif ($array_Champs['champVide']) {
            $typeSituation = 13;
        } elseif ($array_Champs['champInvalidEmail']) {
            $typeSituation = 17;
        } elseif ($array_Champs['champTropLong']) {
            $typeSituation = 14;
        } elseif ($array_Champs['champInvalid']) {
            $typeSituation = 15;
        } elseif ($array_Champs['creationUserSuccess'] && isset($_POST['signUp']) ) {
            $typeSituation = 16;
        } elseif (!$array_Champs['creationUserSuccess'] && isset($_POST['signUp']) ) {
            $typeSituation = 34;
        }
        return $typeSituation; // on retourne seulement un numéro qui va nous servicer dans la fct traduction()
    }
    
    
    
    // Selon une recommandation :
    // https://stackoverflow.com/questions/30279321/how-to-use-password-hash
    // On ne doit pas jouer avec le salt....
    function encryptementPassword(string $password) {
        
        return password_hash($password, PASSWORD_BCRYPT);
    }
	
	
	/**
	 * Fonction pour rediriger vers la bonne page extérieur à la page du reset de password
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
				
				// Une demande de retour à la page de connexion
			} elseif (isset($_POST['btn_login'])) {
				
				// En fonction de la langue
				if ($type_langue === 'english') {
					header("Location: /login-user/login-user.php?langue=english");
				} elseif ($type_langue === 'francais') {
					header("Location: /login-user/login-user.php?langue=francais");
				}
    
				// Une demande pour quitter la page de connexion, pour revenir à l'accueil
			} elseif (isset($_POST['btn_return'])) {
				
				// En fonction de la langue
				if ($type_langue === 'english') {
					header("Location: /english/english.html");
				} elseif ($type_langue === 'francais') {
					header("Location: /index.html");
				}
				
				// Une demande pour faire une demande de changement de password
			} elseif (isset($_POST['btn_reset_pwd'])) {
				
				// En fonction de la langue
				if ($type_langue === 'english') {
					header("Location: /login-user/reset-pwd/create-email-temp-pwd.php?langue=english");
				} elseif ($type_langue === 'francais') {
					header("Location: /login-user/reset-pwd/create-email-temp-pwd.php?langue=francais");
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
	
        // Un de ces boutons là, nous ferons sortir de la page web actuel.
        if (isset($_POST['btn_login']) || isset($_POST['btn_return']) || isset($_POST['btn_reset_pwd'])) {
            redirection($array_Champs["type_langue"], $array_Champs["invalid_language"]); // On n'a pas besoin de cette variable
            
            // Si le bouton création de user est pesé...
        } elseif (isset($_POST['btn_sign_up'])) {
	
	        // On passe à travers les champs pour vérifier les informations
	        $array_Champs = validation_champs($connMYSQL, $array_Champs);
         
            // Tant que nous n'avons pas d'erreur, on poursuit vers la création du user
            if (!$array_Champs["erreur_presente"]){
                
                //  $array_Champs = creationUser($array_Champs, $connMYSQL);
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
        <!-- Le fichier login.png est la propriété du site https://pixabay.com/fr/ic%C3%B4nes-symboles-bouton-842844/ mais en utilisation libre-->
        <link rel="shortcut icon" href="../login-user-icone.png">
        <link rel="stylesheet" type="text/css" href="../login-user.css">
        <title><?php echo $array_Champs["liste_mots"]['titre_create']; ?></title>
        <style>
            body {
                margin: 0;
                /* Fichier photoPoker.jpg est une propriété du site https://pixabay.com/fr/syst%C3%A8me-r%C3%A9seau-actualit%C3%A9s-connexion-2457651/ sous licence libre */
                background-image: url("../login-background.jpg");
                background-position: center;
                background-attachment: fixed;
                background-size: 100%;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <div class="center">
                <p class='titre'><?php echo $array_Champs["liste_mots"]['p1_create']; ?></p>
                <p class='titre'><?php echo $array_Champs["liste_mots"]['p2_create']; ?></p>
                <ul>
                    <li class='info'><?php echo $array_Champs["liste_mots"]['li1_create']; ?></li>
                    <li class='info'><?php echo $array_Champs["liste_mots"]['li2_create']; ?></li>
                    <li class='info'><?php echo $array_Champs["liste_mots"]['li3_create']; ?></li>
                </ul>
                <fieldset>
                    <legend class="legend-center"><?php echo $array_Champs["liste_mots"]['legend']; ?></legend>
                    <form id="form" method="post" action="create-user-poker-stats.php">
                        <div class="connexion">
                            <div class="information <?php if ($array_Champs['champ_vide_user'] || $array_Champs['champs_user_pwd_equal'] || $array_Champs['champ_invalid_user'] || $array_Champs['duplicate_user'] || $array_Champs['champ_trop_long_user']) { echo 'erreur'; } ?>">
                                <label for="user"><?php echo $array_Champs["liste_mots"]['usager']; ?></label>
                                <div>
                                    <input autofocus id="user" type="text" name="user" maxlength="15" placeholder="<?php echo $array_Champs["liste_mots"]['info_valid_user_pwd']; ?>" value="<?php echo $array_Champs['user']; ?>" />
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div>
                            <div class="information <?php if (($array_Champs['champ_vide_email'] || $array_Champs['duplicate_email'] || $array_Champs['champ_invalid_email'] || $array_Champs['champ_trop_long_email'])) { echo 'erreur';} ?>">
                                <label for="email"><?php echo $array_Champs["liste_mots"]['email']; ?></label>
                                <div>
                                    <input id="email" type='email' name="email" maxlength="50" placeholder="<?php echo $array_Champs["liste_mots"]['info_valid_email']; ?>" value="<?php echo $array_Champs['email']; ?>" />
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div>
                            <div class="information <?php if ($array_Champs['champs_user_pwd_equal'] || $array_Champs['champs_pwd_not_equal'] || $array_Champs['champ_vide_pwd'] || $array_Champs['champ_invalid_pwd'] || $array_Champs['champ_trop_long_pwd']) { echo 'erreur';} ?>">
                                <label for="password"><?php echo $array_Champs["liste_mots"]['pwd']; ?></label>
                                <div>
                                    <input id="password" type='password' name="password" maxlength="25" placeholder="<?php echo $array_Champs["liste_mots"]['info_valid_user_pwd']; ?>" value="<?php echo $array_Champs['password']; ?>" />
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div>
                            <div class="information <?php if ($array_Champs['champs_user_pwd_equal'] || $array_Champs['champs_pwd_not_equal'] || $array_Champs['champ_vide_pwd_conf'] || $array_Champs['champ_invalid_pwd_conf'] || $array_Champs['champ_trop_long_pwd_conf']) { echo 'erreur';} ?>">
                                <label for="password"><?php echo $array_Champs["liste_mots"]['pwd_conf']; ?></label>
                                <div>
                                    <input id="password_conf" type='password' name="password_conf" maxlength="25" placeholder="<?php echo $array_Champs["liste_mots"]['info_valid_user_pwd']; ?>" value="<?php echo $array_Champs['password_conf']; ?>" />
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div>
                        </div>
                        <div class="section-reset-btn">
                            <input class="bouton" type='submit' name='btn_sign_up' value="<?php echo $array_Champs["liste_mots"]['btn_sign_up']; ?>">
                            <input class="bouton" id="faire_menage_total" type="reset" value="<?php echo $array_Champs["liste_mots"]['btn_reset']; ?>">
                            <input class="bouton" type='submit' name='btn_login' value="<?php echo $array_Champs["liste_mots"]['btn_login']; ?>">
                            <input class="bouton" type='submit' name='btn_reset_pwd' value="<?php echo $array_Champs["liste_mots"]['btn_reset_pwd']; ?>">
                            <input type='hidden' name='langue' value="<?php echo $array_Champs['type_langue']; ?>">
                        </div>
                    </form>
                </fieldset>
            </div>
            <div class="footer">
	            <?php if ($array_Champs["situation"] !== 0) { ?>
                    <div class='erreur'>
                        <p> <?php echo $array_Champs["liste_mots"]['message']; ?> </p>
                    </div>
	            <?php } ?>
                <div class="section-retour-btn">
                    <input form="form" class="bouton" type="submit" name="btn_return" value="<?php echo $array_Champs["liste_mots"]['btn_return']; ?>">
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="../login-user.js"></script>
    </body>
</html>
