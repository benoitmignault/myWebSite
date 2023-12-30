<?php
	// Les includes nécessaires
	include_once("../traduction/traduction_reset.php");
	include_once("../includes/fct-connexion-bd.php");
	
	/**
	 * Fonction qui va contenir tous ce dont on aura besoin.
	 * Une partie des variables de type string ou integer et une autre partie en boolean
	 * On va ajouter un array pour les mots traduits ou non
	 *
	 * @return array
	 */
	function initialisation(): array {
        
        return array("type_langue" => "", "situation" => 0, "champ_pwd_temp" => "", "champ_pwd_1_new" => "", "champ_pwd_2_new" => "", "champ_lien_crypter" => "", 
                 "create_user_succes" => false, "pwd_temp_crypte_bd" => "", "temps_valide_link_bd" => 0, "pwd_old_crypte_bd" => "",
                 "champ_pwd_1_trop_long" => false, "champ_pwd_2_trop_long" => false, "champ_pwd_temp_trop_long" => false, "champs_pwd_trop_long" => false, 
                 "champ_pwd_temp_invalid" => false, "champ_pwd_1_invalid" => false, "champ_pwd_2_invalid" => false, "champs_pwd_invalid" => false, "pwd_old_new_diff" => false, 
                 "champ_pwd_new_none_equal" => false, "champ_pwd_temp_none_equal" => false, "champs_pwd_none_equal" => false,
                 "champ_pwd_1_empty" => false, "champ_pwd_2_empty" => false, "champ_pwd_temp_empty" => false, "champs_pwd_empty" => false, "invalid_langue" => false,
                 "token_time_used" => 0, "token_time_expired" => false, "lien_crypter_still_good" => false, "erreur_presente" => false,
                 "erreur_system_bd" => false, "liste_mots" => array());
    }
    
	/**
	 * Fonction pour setter les premières informations du GET ou POST
	 * Ensuite, Via la fonction ... on va aller récup
	 *
	 * @param array $array_Champs
	 * @param object $connMYSQL
	 * @return array
	 */
	function remplisage_champs(array $array_Champs, object $connMYSQL): array{
  
		if ($_SERVER['REQUEST_METHOD'] === 'GET'){
			// Il est important de vérifier le type de langue en arrivant sur la page web
			if (isset($_GET['langue'])){
				$array_Champs["type_langue"] = $_GET['langue'];
			}
            
            // Si nous avons dans le GET, nous allons avoir un lien à valider
			if (isset($_GET['key'])){
				$array_Champs["champ_lien_crypter"] = $_GET['key'];
			}
		}
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	
	        if (isset($_POST['type_langue'])){
		        $array_Champs["type_langue"] = $_POST['type_langue'];
	        }
            
            // Si nous venons de peser sur le bouton de changement de password
            if (isset($_POST['create_new_pwd'])){
	
	            if (isset($_POST['champ_lien_crypter'])){
		            $array_Champs["champ_lien_crypter"] = $_POST['champ_lien_crypter'];
	            }
             
	            date_default_timezone_set('America/New_York');
	            // Optimisation, en une seule étape
	            // Action commune, une fois que nous avons nos informations de la BD
	            $array_Champs["token_time_used"] = strtotime(date("Y-m-d H:i:s"));
	
	            if (isset($_POST['champ_pwd_temp'])){
		            $array_Champs["champ_pwd_temp"] = $_POST['champ_pwd_temp'];
	            }
	
	            if (isset($_POST['champ_pwd_1_new'])){
		            $array_Champs["champ_pwd_1_new"] = $_POST['champ_pwd_1_new'];
	            }
	
	            if (isset($_POST['champ_pwd_2_new'])){
		            $array_Champs["champ_pwd_2_new"] = $_POST['champ_pwd_2_new'];
	            }
            }
        }
        
        // Si nous avons un lien, on va aller le valider dans la BD
        if (!empty($array_Champs["champ_lien_crypter"])){
         
	        // On fera appel une seul fois à la fonction, le pourquoi qu'on doit assigner l'information plus haut
	        $result_info = recuperation_info_user_bd($array_Champs["champ_lien_crypter"], $connMYSQL);
	
	        // Il nous faut exactement 3 résultat pour nos trois champs
	        if (count($result_info) === 3){
		        $array_Champs["pwd_temp_crypte_bd"] = $result_info["password_temp"];
		        $array_Champs["pwd_old_crypte_bd"] = $result_info["password"];
		        $array_Champs["temps_valide_link_bd"] = $result_info["temps_valide_link"];
	        } // Sinon, le lien n'existe pas dans la BD
        }
        
        return $array_Champs;
    }
		
	/**
     * Fonction qui sera utilisée via @see remplisage_champs pour aller récupérer
     * - l'ancien password
     * - le lien sécurisé
     * - le temps restant à la validité au processus de changement de password.
     * Retournera le résultat, si le lien est valide, sinon retournera le array vide.
     *
	 * @param $champ_lien_crypter
	 * @param $connMYSQL
	 * @return array
	 */
	function recuperation_info_user_bd($champ_lien_crypter, $connMYSQL): array{
		
		// On doit récupérer les infos par rapport au lien crypter
		$select = "SELECT password, password_temp, temps_valide_link ";
		$from = "FROM login ";
		$where = "WHERE reset_link = ?";
  
        // Préparation de la requête SQL avec un alias pour la colonne sélectionnée
        $query = $select . $from . $where;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		
		/* Lecture des marqueurs */
		$stmt->bind_param("s", $champ_lien_crypter);
		
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
	 * Fonction qui servira à mettre à «True» les variables de contrôles des informations
     * que nous avons associé durant la fonction @see remplisage_champs
     *
	 * @param $array_Champs
	 * @return array
	 */
	function validation_champs($array_Champs): array{
	
	    // Validation commune pour le Get & Post, à propos de la langue
	    if ($array_Champs["type_langue"] != "francais" && $array_Champs["type_langue"] != "anglais"){
		    $array_Champs["invalid_langue"] = true;
        }
		
        // Validation commune pour le GET & POST si nous avons encore des informations de la BD
        if (!empty($array_Champs["pwd_temp_crypte_bd"]) && !empty($array_Champs["pwd_old_crypte_bd"]) && !empty($array_Champs["temps_valide_link_bd"])){
            $array_Champs["lien_crypter_still_good"] = true;
        }
        
        // Validation commune pour le GET & POST si nous avons encore encore le temps pour le changement du password
        if ($array_Champs["token_time_used"] > $array_Champs["temps_valide_link_bd"]){
            $array_Champs["token_time_expired"] = true;
        }
                
        // On doit maintenant de faire les validations de contrôles pour le POST, seulement.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	        // Sinon, on valide les champs vides
	        if (empty($array_Champs['champ_pwd_temp'])) {
		        $array_Champs['champ_pwd_temp_empty'] = true;
                
                // Sinon, on peut aller faire la comparaison des password temporaires
	        } elseif (!password_verify($array_Champs['champ_pwd_temp'], $array_Champs["pwd_temp_crypte_bd"])) {
		        $array_Champs['champ_pwd_temp_none_equal'] = true;
	        }
	
	        if (empty($array_Champs['champ_pwd_1_new'])) {
		        $array_Champs['champ_pwd_1_empty'] = true;
	        }
	
	        if (empty($array_Champs['champ_pwd_2_new'])) {
		        $array_Champs['champ_pwd_2_empty'] = true;
	        }
	
	        // Si un des trois champs de password est vide, on allume le flag
	        if ($array_Champs['champ_pwd_temp_empty'] || $array_Champs['champ_pwd_1_empty'] || $array_Champs['champ_pwd_2_empty']) {
		        $array_Champs['champs_pwd_empty'] = true;
	        }
	
	        // Si le nouveau password n'est pas répété identiquement
	        if ((strcmp($array_Champs["champ_pwd_1_new"], $array_Champs["champ_pwd_2_new"]) != 0) && !$array_Champs['champ_pwd_1_empty'] && !$array_Champs['champ_pwd_2_empty']) {
		        $array_Champs["champ_pwd_new_none_equal"] = true;
          
		        // Sinon, on va aller vérifier que le nouveau password n'est pas égal à l'ancien par hasard
	        } elseif (!password_verify($array_Champs['champ_pwd_1_new'], $array_Champs['pwd_old_crypte_bd'])) {
		        // Si c'est vrai que ce n'est pas pareil, donc tout va bien
		        $array_Champs['pwd_old_new_diff'] = true;
	        }
	
	        if ($array_Champs['champ_pwd_temp_none_equal'] || $array_Champs["champ_pwd_new_none_equal"]) {
		        $array_Champs["champs_pwd_none_equal"] = true;
	        }
	
	        // Section pour vérifier la validité de la longueur des champs passwords
	        if (strlen($array_Champs['champ_pwd_temp']) > 10) {
		        $array_Champs['champ_pwd_temp_trop_long'] = true;
	        }
	
	        if (strlen($array_Champs['champ_pwd_1_new']) > 25) {
		        $array_Champs['champ_pwd_1_trop_long'] = true;
	        }
	
	        // Correction du bug, découvert 2023-12-29, champ_pwd_1_new était présent au lieu de champ_pwd_2_new
	        if (strlen($array_Champs['champ_pwd_2_new']) > 25) {
		        $array_Champs['champ_pwd_2_trop_long'] = true;
	        }
	
	        if ($array_Champs['champ_pwd_temp_trop_long'] || $array_Champs['champ_pwd_1_trop_long'] || $array_Champs['champ_pwd_2_trop_long']) {
		        $array_Champs['champs_pwd_trop_long'] = true;
	        }
	
	        // Section pour valider si il y a des caractères invalides dans les champs password
	        $pattern_valid_pass = "#^[[:alnum:]][[:alnum:]]{6,23}[[:alnum:]]$#";
	
	        if (!preg_match($pattern_valid_pass, $array_Champs['champ_pwd_temp']) && !$array_Champs['champ_pwd_temp_empty']) {
		        $array_Champs['champ_pwd_temp_invalid'] = true;
	        }
	
	        if (!preg_match($pattern_valid_pass, $array_Champs['champ_pwd_1_new']) && !$array_Champs['champ_pwd_1_empty']) {
		        $array_Champs['champ_pwd_1_invalid'] = true;
	        }
	
	        if (!preg_match($pattern_valid_pass, $array_Champs['champ_pwd_2_new']) && !$array_Champs['champ_pwd_2_empty']) {
		        $array_Champs['champ_pwd_2_invalid'] = true;
	        }
	
	        if ($array_Champs['champ_pwd_temp_invalid'] || $array_Champs['champ_pwd_1_invalid'] || $array_Champs['champ_pwd_2_invalid']) {
		        $array_Champs['champs_pwd_invalid'] = true;
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
        $typeSituation = 0;
        
        if ($array_Champs['token_time_expired']){
	        $typeSituation = 14;
         
        } elseif ($array_Champs['champ_pwd_temp_empty'] && $array_Champs['champ_pwd_1_empty'] && $array_Champs['champ_pwd_2_empty']) {
            $typeSituation = 1;
            
        } elseif ($array_Champs['champ_pwd_temp_none_equal'] && !$array_Champs["champ_pwd_new_none_equal"] && !$array_Champs['champs_pwd_empty'] && !$array_Champs['champs_pwd_invalid'] && !$array_Champs['pwd_old_new_diff']){
            $typeSituation = 2;
            
        } elseif (!$array_Champs['champ_pwd_temp_none_equal'] && $array_Champs["champ_pwd_new_none_equal"] && !$array_Champs['champs_pwd_empty'] && !$array_Champs['champs_pwd_invalid'] && !$array_Champs['pwd_old_new_diff']){
            $typeSituation = 3;
            
        } elseif (!$array_Champs['champ_pwd_temp_none_equal'] && $array_Champs['champ_pwd_1_empty'] && $array_Champs['champ_pwd_2_empty']){
            $typeSituation = 4;
            
        } elseif ($array_Champs['champ_pwd_temp_empty'] && !$array_Champs["champ_pwd_new_none_equal"] && !$array_Champs['champs_pwd_invalid'] && !$array_Champs['pwd_old_new_diff']){
            $typeSituation = 5;
            
        } elseif (!$array_Champs['champ_pwd_temp_none_equal'] && $array_Champs['champs_pwd_empty']){
            $typeSituation = 6;
            
        } elseif (!$array_Champs["champs_pwd_empty"] && !$array_Champs["champs_pwd_none_equal"] && $array_Champs["token_time_expired"] && !$array_Champs["champs_pwd_trop_long"] && !$array_Champs["champs_pwd_invalid"]){
            $typeSituation = 7;
            
        } elseif (!$array_Champs['pwd_old_new_diff'] && !$array_Champs['champ_pwd_1_empty'] && !$array_Champs['champ_pwd_2_empty'] && !$array_Champs['champ_pwd_temp_none_equal']){
            $typeSituation = 12;
            
        } elseif ($array_Champs['create_user_succes'] && $array_Champs['pwd_old_new_diff']){
            $typeSituation = 8;
            
        } elseif ($array_Champs['champ_pwd_temp_none_equal'] && $array_Champs["champ_pwd_new_none_equal"] && !$array_Champs['champs_pwd_empty'] && !$array_Champs['champs_pwd_invalid']){
            $typeSituation = 9;
            
        } elseif (!$array_Champs['champ_pwd_temp_none_equal'] && $array_Champs['champs_pwd_invalid']){
            $typeSituation = 10;
            
        } elseif ($array_Champs['champ_pwd_temp_none_equal'] && $array_Champs['champs_pwd_invalid']){
            $typeSituation = 11;
            
        } elseif ($array_Champs['erreur_system_bd']){
	        $typeSituation = 13;
        }
    
        return $typeSituation;
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
     * Une fois que tous a été valider, on peut aller mettre à jour le nouveau password
     *
	 * @param array $array_Champs
	 * @param object $connMYSQL
	 * @return array
	 */
    function changement_password(array $array_Champs, object $connMYSQL): array{
	
	    // On commence par encrypter le password et remettre à null et 0 les autres champs
	    $pwd_new_encrypter = encryptement_password($array_Champs["champ_pwd_1_new"]);
	    $zero = 0; // Je dois créer une variable qui va contenir la valeur 0
	    $valeur_null = null;
     
	    // On va devoir updater le nouveau password
	    $update = "UPDATE login ";
	    $set = "set password = ?, reset_link = ?, password_temp = ?, temps_valide_link = ? ";
	    $where = "WHERE reset_link = ?";
	
	    // Préparation de la requête SQL avec un alias pour la colonne sélectionnée
	    $query = $update . $set . $where;
	
	    // Préparation de la requête
	    $stmt = $connMYSQL->prepare($query);
	
	    /* Lecture des marqueurs */
	    $stmt->bind_param("sssis", $pwd_new_encrypter, $valeur_null, $valeur_null, $zero, $array_Champs["champ_lien_crypter"]);
	
	    /* Exécution de la requête */
	    $status = $stmt->execute();
        
        if ($status === false) {
	        $array_Champs['erreur_system_bd'] = true;
        } else {
            $array_Champs['create_user_succes'] = true;
            // Remise à leur valeur initial, car le changement de mot de passe est terminé et le lien n'est plus valide
            $array_Champs['champ_pwd_temp'] = "";
            $array_Champs['champ_pwd_1_new'] = "";
            $array_Champs['champ_pwd_2_new'] = "";
            $array_Champs['champ_lien_crypter'] = "";
            $array_Champs['token_time_used'] = 0;
        }
    
        /* close statement and connection */
        $stmt->close();
    
        return $array_Champs;
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
	 * @param bool $invalid_langue
     * @param bool $lien_crypter_still_good
	 * @return void
	 */
	#[NoReturn] function redirection(string $type_langue, bool $invalid_langue, bool $lien_crypter_still_good): void {
        
        // Situation commune pour GET & POST
		if ($invalid_langue || !$lien_crypter_still_good) {
			header("Location: /erreur/erreur.php");
		}
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Les deux premiers IF sont pour la page d'accueil
            // Les deux derniers IF sont pour la page login
            if (isset($_POST['return']) && $type_langue === "francais") {
                header("Location: /index.html");
                
            } elseif (isset($_POST['return']) && $type_langue === "english") {
                header("Location: /english/english.html");
                
            } elseif (isset($_POST['page_login']) && $type_langue === "francais") {
                header("Location: /login/login.php?langue=francais");
                
            } elseif (isset($_POST['page_login']) && $type_langue === "english") {
                header("Location: /login/login.php?langue=english");
            }
        }
        
        exit; // pour arrêter l'exécution du code php
    }
	
	// Les fonctions communes
	$connMYSQL = connexion();
	$array_Champs = initialisation();
	$array_Champs = remplisage_champs($array_Champs, $connMYSQL);
    // En raison des validations qu'on doit faire dans le GET aussi, on va mettre la fct de vérification ici pour les actions
	$array_Champs = validation_champs($array_Champs);
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        
        // Si la langue n'est pas setter on sort de la page en indiquant Err 404
        if ($array_Champs["invalid_langue"]){
	        redirection("", $array_Champs["invalid_langue"], $array_Champs["lien_crypter_still_good"]);
        } else {
	        // La variable de situation est encore à 0 vue qu'il s'est rien passé de grave...
	        $array_Champs["liste_mots"] = traduction($array_Champs["type_langue"], $array_Champs["situation"]);
        }
    } // Fin du GET pour faire afficher la page web
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
     
        // Nous avons deux scénarios possibles : Aller vers un autre endroit du site web ou demander le changement de password. || !$array_Champs["lien_crypter_still_good"]
        
        if (isset($_POST['return']) || isset($_POST['page_login']) ){
            redirection($array_Champs["type_langue"], $array_Champs["invalid_langue"], $array_Champs["lien_crypter_still_good"]);
            
            // Nous avons appuyer sur le bouton changement de password
        } elseif (isset($_POST['create_new_pwd'])){
            
            /* Les conditions pour changer le password :
             * 1 - Les champs ne sont pas vide
             * 2 - ?
             * 3 - Le lien n'a pas expiré au niveau du temps valide
             * 4 - Les passwords respect les longueurs
             * 5 - Les passwords ne contient pas de caractères invalides
             * 6 - Le vieux et nouveau password ne sont pas identique
             */
            if (!$array_Champs["champs_pwd_empty"] && !$array_Champs["champs_pwd_none_equal"] && !$array_Champs["token_time_expired"] &&
                !$array_Champs["champs_pwd_trop_long"] && !$array_Champs["champs_pwd_invalid"] && $array_Champs["pwd_old_new_diff"]){
                $array_Champs = changementPassword($array_Champs, $connMYSQL);
            }
            
            $array_Champs["situation"] = situation($array_Champs);
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
                <li class='info'><?php echo $array_Champs["liste_mots"]['li1']; ?></li>
                <li class='info'><?php echo $array_Champs["liste_mots"]['li2']; ?></li>
                <li class='info'><?php echo $array_Champs["liste_mots"]['li3']; ?></li>
            </ul>
            <fieldset class="<?php if ($array_Champs['create_user_succes']) { echo "changerAvecSucces"; } ?>">
                <legend class="legend-center"><?php echo $array_Champs["liste_mots"]['legend']; ?></legend>
                <form method="post" action="./reset.php">
                    <div class="connexion">
                        <div class="information <?php if ($array_Champs['champ_pwd_temp_invalid'] || $array_Champs['champ_pwd_temp_trop_long'] || $array_Champs["champ_pwd_temp_empty"] || $array_Champs['champ_pwd_temp_none_equal']) { echo 'erreur';} ?>">
                            <label for="champ_pwd_temp"><?php echo $array_Champs["liste_mots"]['mdp_Temp']; ?></label>
                            <div>
                                <input <?php if ($array_Champs['create_user_succes']) { echo "disabled"; } ?> autofocus id="champ_pwd_temp" type='password' maxlength="25" name="champ_pwd_temp" value="<?php echo $array_Champs['champ_pwd_temp']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if (( $_SERVER['REQUEST_METHOD'] === 'POST' && !$array_Champs['pwd_old_new_diff'] ) || $array_Champs['champ_pwd_1_invalid'] || $array_Champs['champ_pwd_1_trop_long'] || $array_Champs["champ_pwd_1_empty"] || $array_Champs["champ_pwd_new_none_equal"]) { echo 'erreur';} ?>">
                            <label for="champ_pwd_1_new"><?php echo $array_Champs["liste_mots"]['mdp_1']; ?></label>
                            <div>
                                <input <?php if ($array_Champs['create_user_succes']) { echo "disabled"; } ?> id="champ_pwd_1_new" type='password' maxlength="25" name="champ_pwd_1_new" value="<?php echo $array_Champs['champ_pwd_1_new']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                        <div class="information <?php if ( ( $_SERVER['REQUEST_METHOD'] === 'POST' && !$array_Champs['pwd_old_new_diff'] ) || $array_Champs['champ_pwd_2_invalid'] || $array_Champs['champ_pwd_2_trop_long'] || $array_Champs["champ_pwd_2_empty"] || $array_Champs["champ_pwd_new_none_equal"]) { echo 'erreur';} ?>">
                            <label for="champ_pwd_2_new"><?php echo $array_Champs["liste_mots"]['mdp_2']; ?></label>
                            <div>
                                <input <?php if ($array_Champs['create_user_succes']) { echo "disabled"; } ?> id="champ_pwd_2_new" type='password' maxlength="25" name="champ_pwd_2_new" value="<?php echo $array_Champs['champ_pwd_2_new']; ?>" />
                                <span class="obligatoire">&nbsp;*</span>
                            </div>
                        </div>
                    </div>
                    <div class="section-reset-btn">
                        <input <?php if ($array_Champs['create_user_succes']) { echo "class=\"bouton disabled\" disabled"; } else { echo "class=\"bouton\""; }?> type='submit' name='create_new_pwd' value="<?php echo $array_Champs["liste_mots"]['btn_create_new_pwd']; ?>">
                        <input type='hidden' name='type_langue' value="<?php echo $array_Champs['type_langue']; ?>">
                        <input type='hidden' name='champ_lien_crypter' value="<?php echo $array_Champs['champ_lien_crypter']; ?>">
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="footer">
            <!-- ici la situation sera lorsque l'envoi par courriel sera un succès -->
            <div class='avert <?php if ($array_Champs["situation"] != 8) { echo 'erreur'; } ?>'>
                <p> <?php echo $array_Champs["liste_mots"]['message']; ?> </p>
            </div>
            <div class="section-retour-btn">
                <form method="post" action="./reset.php">
                    <input class="bouton" type="submit" name="page_login" value="<?php echo $array_Champs["liste_mots"]['btn_login']; ?>">
                    <input class="bouton" type="submit" name="return" value="<?php echo $array_Champs["liste_mots"]['btn_return']; ?>">
                    <input type='hidden' name='type_langue' value="<?php echo $array_Champs['type_langue']; ?>">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
