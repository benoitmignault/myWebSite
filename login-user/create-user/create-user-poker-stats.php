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
		
		return array("user" => "", "password" => "", "situation" => 0, "type_langue" => "", "invalid_language" => false,
		             "champs_vide" => false, "champ_vide_user" => false, "champ_vide_pwd" => false,
		             "champs_invalid" => false, "champ_invalid_user" => false, "champ_invalid_pwd" => false,
		             "user_not_found" => false, "pwd_not_found" => false, "user_admin" => false, "message_erreur_bd" => "",
		             "erreur_system_bd" => false, "erreur_presente" => false, "id_user" => 0, "liste_mots" => array());
	}
    
    function verifChamp($array_Champs, $connMYSQL) {
        if (isset($_POST['signUp']) || isset($_POST['login'])){
            $array_Champs["user"] = strtolower($_POST['user']);
            $array_Champs["password"] = $_POST['password'];
        }
    
        if (isset($_POST['signUp'])){
            $array_Champs["email"] = $_POST['email'];
        }
    
        if (empty($array_Champs['user'])){
            $array_Champs['champVideUser'] = true;
        }
    
        if (empty($array_Champs['password'])){
            $array_Champs['champVidePassword'] = true;
        }
    
        // Cette validation doit exclure si on pèse sur le bouton login
        if (empty($array_Champs['email']) && isset($_POST['signUp'])){
            $array_Champs['champVideEmail'] = true;
        }
    
        // Simplification des array_Champs vide pour plutard...
        if (($array_Champs['champVideUser'] || $array_Champs['champVidePassword'] || $array_Champs['champVideEmail'])){
            $array_Champs['champVide'] = true;
        }
    
        $longueurUser = strlen($array_Champs['user']);
        $longueurPassword = strlen($array_Champs['password']);
        $longueurEmail = strlen($array_Champs['email']);
    
        if ($longueurUser > 15) {
            $array_Champs['champTropLongUser'] = true;
        }
    
        if ($longueurPassword > 25){
            $array_Champs['champTropLongPassword'] = true;
        }
    
        if ($longueurEmail > 50 && isset($_POST['signUp']) ){
            $array_Champs['champTropLongEmail'] = true;
        }
    
        // Simplification des array_Champs trop long pour plutard...
        if ($array_Champs['champTropLongUser'] || $array_Champs['champTropLongPassword'] || $array_Champs['champTropLongEmail']){
            $array_Champs['champTropLong'] = true;
        }
    
        // On ne doit pas avoir de caractères spéciaux dans l'username
        // ajout du underscore pour le user name
        $patternUser = "#^[0-9a-z]([0-9a-z_]{0,13})[0-9a-z]$#";
        if (!preg_match($patternUser, $array_Champs['user'])) {
            $array_Champs['champInvalidUser'] = true;
        }
    
        // On ne doit pas avoir de caractères spéciaux dans le mot de passe
        $patternPass = "#^[0-9a-zA-Z]([0-9a-zA-Z]{0,23})[0-9a-zA-Z]$#";
        if (!preg_match($patternPass, $array_Champs['password'])) {
            $array_Champs['champInvalidPassword'] = true;
        }
    
        $patternEmail = "#^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$#";
        if (!preg_match($patternEmail, $array_Champs['email']) && isset($_POST['signUp'])) {
            $array_Champs['champInvalidEmail'] = true;
        }
    
        // Ajout de cette sécurité / 5 Février 2020
        // https://stackoverflow.com/questions/11952473/proper-prevention-of-mail-injection-in-php/11952659#11952659
        if (!(filter_var($array_Champs['email'], FILTER_VALIDATE_EMAIL)) && isset($_POST['signUp']) ){
            $array_Champs['champInvalidEmail'] = true;
        }
    
        if (($array_Champs['champInvalidUser'] || $array_Champs['champInvalidPassword'] || $array_Champs['champInvalidEmail'])){
            $array_Champs['champInvalid'] = true;
        }
    
        if (!$array_Champs['champVideUser'] && !$array_Champs['champVidePassword'] && $array_Champs['user'] == $array_Champs['password']){
            $array_Champs['sameUserPWD'] = true;
        }
    
        // Instauration de la validation si le user et ou email est dejà existant seulement si on veut créer un user
        if (isset($_POST['signUp'])){
            // Retourner un message erreur si la BD a eu un problème !
    
            // Optimisation pour avoir directement la valeur qui nous intéreste
            $stmt = $connMYSQL->prepare("select user, email from login where user =? OR email =? ");
    
            /* Lecture des marqueurs */
            $stmt->bind_param("ss", $array_Champs['user'], $array_Champs['email']);
    
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
                    if ($row['user'] === $array_Champs['user']) {
                        $array_Champs['duplicatUser'] = true;
                    }
    
                    if ($row['email'] === $array_Champs['email']) {
                        $array_Champs['duplicatEmail'] = true;
                    }
                }
            }
        }
    
        if ($array_Champs['duplicatEmail'] || $array_Champs['duplicatUser']){
            $array_Champs['duplicate'] = true;
        }
    
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
    
    // Selon une recommandation :
    // https://stackoverflow.com/questions/30279321/how-to-use-password-hash
    // On ne doit pas jouer avec le salt....
    function encryptementPassword(string $password) {
        
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    
    
    
	// Les fonctions communes
	$connMYSQL = connexion();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $array_Champs = initialChamp(); // est un tableau avec tous les flag erreurs possibles et les infos du user, pwd et le type de situation
        $array_Champs["typeLangue"] = $_GET['langue'];
    
        if ($array_Champs["typeLangue"] != "francais" && $array_Champs["typeLangue"] != "english") {
            header("Location: /erreur/erreur.php");
            exit;
        } else {
            $arrayMots = traduction($array_Champs);
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $array_Champs = initialChamp();
        $array_Champs["typeLangue"] = $_POST['langue'];
        if (isset($_POST['return'])) {
            if ($array_Champs["typeLangue"] === 'english') {
                header("Location: /english/english.html");
            } elseif ($array_Champs["typeLangue"] === 'francais') {
                header("Location: /index.html");
            }
            exit;
        } else {
            $array_Champs = verifChamp($array_Champs, $connMYSQL);
            // Si le bouton se connecter est pesé...
            if (isset($_POST['login'])) {
                // Comme j'ai instauré une foreign key entre la table login_stat_poker vers login je dois aller récupérer id pour l'insérer avec la nouvelle combinaison
                /* Crée une requête préparée */
                $stmt = $connMYSQL->prepare("select id from login where user =?");
    
                /* Lecture des marqueurs */
                $stmt->bind_param("s", $array_Champs["user"]);
    
                /* Exécution de la requête */
                $stmt->execute();
    
                /* Association des variables de résultat */
                $result = $stmt->get_result();
    
                $row = $result->fetch_array(MYSQLI_ASSOC);
    
                // Close statement
                $stmt->close();
    
                $array_Champs["idCreationUser"] = $row["id"];	// Assignation de la valeur
                if (!$array_Champs["champVide"] && !$array_Champs["champTropLong"] && !$array_Champs["champInvalid"] ) {
                    $array_Champs = connexionUser($array_Champs, $connMYSQL);
                }
                // si le bouton s'inscrire est pesé...
            } elseif (isset($_POST['signUp'])) {
                // Ajout de la validation si duplicate est à false en raison de unicité du user et email
                if (!$array_Champs["champVide"] && !$array_Champs["champTropLong"] && !$array_Champs["champInvalid"] && !$array_Champs['sameUserPWD'] && !$array_Champs['duplicate']) {
                    $array_Champs = creationUser($array_Champs, $connMYSQL);
                }
                // si le bouton effacer est pesé...
            } elseif (isset($_POST['reset'])) {
	            if ($array_Champs["typeLangue"] === 'english') {
		            header("Location: /login-user/reset-pwd/create-email-temp-pwd.php?langue=english");
	            } elseif ($array_Champs["typeLangue"] === 'francais') {
		            header("Location: /login-user/reset-pwd/create-email-temp-pwd.php?langue=francais");
	            }
                exit;
            }
            $array_Champs["situation"] = situation($array_Champs); // Ici on va modifier la valeur de la variable situation pour faire afficher le message approprié
	        // On va faire la traduction, à la fin des GEt & POST
	        // La variable de situation est encore à 0 pour le GET, donc aucun message
	        $array_Champs["liste_mots"] = traduction($array_Champs["type_langue"], $array_Champs["situation"]);
        }
    }
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
        <title><?php echo $array_Champs["liste_mots"]['title']; ?></title>
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
                <p class='titre'><?php echo $array_Champs["liste_mots"]['p1']; ?></p>
                <ul>
                    <li class='info'><?php echo $array_Champs["liste_mots"]['li1']; ?></li>
                    <li class='info'><?php echo $array_Champs["liste_mots"]['li2']; ?></li>
                    <li class='info'><?php echo $array_Champs["liste_mots"]['li3']; ?></li>
                </ul>
                <fieldset>
                    <legend class="legend-center"><?php echo $array_Champs["liste_mots"]['legend']; ?></legend>
                    <form id="form" method="post" action="create-user-poker-stats.php">
                        <div class="connexion">
                            <div class="information <?php if ($array_Champs['sameUserPWD'] || $array_Champs['champVideUser'] || $array_Champs['champInvalidUser'] || $array_Champs['duplicatUser'] || $array_Champs['badUser'] || $array_Champs['champTropLongUser']) { echo 'erreur'; } ?>">
                                <label for="user"><?php echo $array_Champs["liste_mots"]['usager']; ?></label>
                                <div>
                                    <input autofocus id="user" type="text" name="user" maxlength="15" value="<?php echo $array_Champs['user']; ?>" />
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div>
                            <div class="information <?php if (!isset($_POST['login']) && ($array_Champs['duplicatEmail'] || $array_Champs['champVideEmail'] || $array_Champs['champInvalidEmail'] || $array_Champs['champTropLongEmail'])) { echo 'erreur';} ?>">
                                <label for="email"><?php echo $array_Champs["liste_mots"]['email']; ?></label>
                                <div>
                                    <input id="email" placeholder="<?php echo $array_Champs["liste_mots"]['exemple_email']; ?>" type='email' maxlength="50" name="email" value="<?php echo $array_Champs['email']; ?>" />
                                    <span class="obligatoire">&nbsp;&nbsp;&nbsp;</span>
                                </div>
                            </div>
                            <div class="information <?php if ($array_Champs['sameUserPWD'] || $array_Champs['badPassword'] || $array_Champs['champVidePassword'] || $array_Champs['champInvalidPassword'] || $array_Champs['champTropLongPassword']) { echo 'erreur';} ?>">
                                <label for="password"><?php echo $array_Champs["liste_mots"]['mdp']; ?></label>
                                <div>
                                    <input id="password" type='password' maxlength="25" name="password" value="<?php echo $array_Champs['password']; ?>" />
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div>
                            <div class="information <?php if ($array_Champs['sameUserPWD'] || $array_Champs['badPassword'] || $array_Champs['champVidePassword'] || $array_Champs['champInvalidPassword'] || $array_Champs['champTropLongPassword']) { echo 'erreur';} ?>">
                                <label for="password"><?php echo $array_Champs["liste_mots"]['mdp']; ?></label>
                                <div>
                                    <input id="password_conf" type='password' maxlength="25" name="password_conf" value="<?php echo $array_Champs['password_conf']; ?>" />
                                    <span class="obligatoire">&nbsp;*</span>
                                </div>
                            </div>
                        </div>
                        <div class="section-reset-btn">
                            <input class="bouton" type='submit' name='btn_sign_up' value="<?php echo $array_Champs["liste_mots"]['btn_sign_up']; ?>">
                            <input class="bouton" id="faire_menage_total" type="reset" value="<?php echo $array_Champs["liste_mots"]['btn_reset']; ?>">
                            <input class="bouton" type='submit' name='btn_login' value="<?php echo $array_Champs["liste_mots"]['btn_login']; ?>">
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
