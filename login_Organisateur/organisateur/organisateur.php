<?php
function traduction($champs) {
    if ($champs["typeLangue"] === 'francais') {
        $titre = "Gestion d'un tournoi";
        $h1 = "Bienvenue à vous &rarr; <span class='userDisplay'>{$_SESSION['user']}</span> &larr; sur la page de gestion d'un organisateur.";
        $h3_Ajouter = "Ajouter des combinaisons.";
        $h3_Affichage = "Afficher les combinaisons.";
        $h3_Retirer = "Retirer les combinaisons.";
        $option = "À sélectionner";
        $btn_timer = "Page du TIMER";
        $btn_return = "Retour à l'accueuil";
        $valeur_couleur = "Valeur / Couleur";
        $petit_grosse_mise = "Petite mise / Grosse mise";
        $btn_ajout = "Ajouter";
        $btn_delete = "Détruire";
        $id_couleur = "ID correspondant à celui du tableau valeurs et couleurs.";
        $id_mises = "ID correspondant à celui du tableau petites et grosses mises.";
    } elseif ($champs["typeLangue"] === 'english') {
    }
    $arrayMots = ['title' => $titre, 'msg_welcome' => $h1, 'h3_Ajouter' => $h3_Ajouter, 'h3_Affichage' => $h3_Affichage, 'h3_Retirer'=> $h3_Retirer, 'valeur_couleur'=>$valeur_couleur,'petit_grosse_mise'=> $petit_grosse_mise, 'btn_timer'=>$btn_timer, 'btn_return'=>$btn_return, 'btn_ajout'=>$btn_ajout,'btn_delete'=>$btn_delete, 'id_couleur'=>$id_couleur, 'option'=>$option, 'id_mises'=>$id_mises];
    return $arrayMots;
}

/* Tous les champs possibles avec des valeurs potentiellement */
function initialisation_Champs() {
    $champs = ["typeLangue" => "", "user" => "", "message" => "", "valeur" => "", "small" => "", 
               "big" => "", "couleur" => "","idValeurCouleur"=> 0, "select_petite_grosse_mise"=>0];
    return $champs;
}

/* Les indicateurs d'erreurs */
function initialisation_indicateur() {
    $valid_Champ = [];
    return $valid_Champ;
}

function remplissageChamps($champs) {
    if (isset($_SESSION['typeLangue'])){
        $champs["typeLangue"] = $_SESSION['typeLangue'];
    }
    if (isset($_SESSION['user'])){
        $champs["user"] = $_SESSION['user'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (isset($_POST['addValeurCouleur'])){
            $champs["valeur"] = $_POST['valeur'];
            $champs["couleur"] = $_POST['couleur'];
        } elseif (isset($_POST['addSmallBig'])){
            $champs["small"] = $_POST['small'];
            $champs["big"] = $_POST['big'];
        } elseif (isset($_POST['delValeurCouleur'])){
            $champs["idValeurCouleur"] = $_POST['idValeurCouleur'];
        } elseif (isset($_POST['delSmallBig'])){
            $champs["id_petite_grosse_mise"] = $_POST['id_petite_grosse_mise'];
        }
    }
    return $champs;
}

function choix_couleur_restant($connMYSQL, $champs){
    $choixDesOption = "";    
    $sql = "SELECT * FROM color WHERE color_english not in (SELECT color_english FROM amount_color WHERE user = '{$champs['user']}')";
    $result = $connMYSQL->query($sql);    
    if ($result->num_rows > 0){
        foreach ($result as $row) {
            if ($champs["typeLangue"] == "francais"){
                $choixDesOption .= "<option value=\"{$row['color_english']}\">{$row['color_french']}</option>";
            } elseif ($champs["typeLangue"] == "english") {
                $firstLetter = ucfirst($row['color_english']);
                $patternDark = "#[D][a][r][k]#";
                $patternLight = "#^[L][i][g][h][t]#";
                if (preg_match($patternDark, $firstLetter)){
                    $tableauMots = explode("Dark", $firstLetter);
                    $firstLetter = "Dark " . $tableauMots[1];
                } elseif (preg_match($patternLight, $firstLetter)){
                    $tableauMots = explode("Light", $firstLetter);
                    $firstLetter = "Light " . $tableauMots[1];
                }
                $choixDesOption .= "<option value=\"{$row['color_english']}\">{$firstLetter}</option>";
            }

        }
    }
    return $choixDesOption;
}

function tableau_valeur_couleur($connMYSQL, $champs){
    $tableau = "";
    $sql = "SELECT * FROM amount_color where user = '{$champs['user']}' ORDER BY amount";
    $result = $connMYSQL->query($sql);    
    if ($result->num_rows > 0){
        if ($champs["typeLangue"] == "francais"){
            $tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Id</th><th>Valeur</th><th>Couleur</th></tr></thead>";
        } elseif ($champs["typeLangue"] == "english") {
            $tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Id</th><th>Value</th><th>Color</th></tr></thead>";
        }    
        $tableau .= "<tbody>";
        foreach ($result as $row) {
            $tableau .= "<tr> <td>{$row['id_couleur']}</td> <td>{$row['amount']}</td> <td bgcolor=\"{$row['color_english']}\"></td> </tr>";
        }
        $tableau .= "</tbody></table>";
    }
    return $tableau;
}

function tableau_petite_grosse($connMYSQL, $champs){
    $tableau = "";
    $sql = "SELECT * FROM mise_small_big where user = '{$champs['user']}' ORDER BY small, big";
    $result = $connMYSQL->query($sql);    
    if ($result->num_rows > 0){
        if ($champs["typeLangue"] == "francais"){
            $tableau .= "<table class=\"tblMisesSB\"><thead><tr><th>Id</th><th>Petite</th><th>Grosse</th></tr></thead>";
        } elseif ($champs["typeLangue"] == "english") {
            $tableau .= "<table class=\"tblMisesSB\"><thead><tr><th>Id</th><th>Small</th><th>Big</th></tr></thead>";
        } 
        $tableau .= "<tbody>";
        foreach ($result as $row) {
            $tableau .= "<tr> <td>{$row['id_valeur']}</td> <td>{$row['small']}</td> <td>{$row['big']}</td> </tr>";
        }
        $tableau .= "</tbody></table>";
    }
    return $tableau;
}

function validation($champs, $valid_Champ, $connMYSQL) {

    $longueurGain = strlen($champs['gain']);
    $longueurDate = strlen($champs['date']);
    $longueurid = strlen($champs['numTournoi']);
    $longueurnewJoueur = strlen($champs['newJoueur']);
    $longueurKiller = strlen($champs['killer']);
    $longueurCitron = strlen($champs['citron']);

    $patternNewJoueur = "#^[A-Z]([a-z]{0,11})([-]{0,1})([A-Z]{0,1})([a-z]{1,9})([ ]{0,1})[a-zA-Z]$#";
    $patternGain = "#^[-]{0,1}([0-9]{1,3})$#";
    $patternID = "#^[0-9]{1,4}$#";
    $patternDate = "#^([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$#";
    $patternKillerCitron = "#^([0-9]{1})([.][5]){0,1}$#";


    return $valid_Champ;
}

function situation($champs, $valid_Champ) {

    return $champs;
}

function connexionBD() {
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
    $connMYSQL->query("set names 'utf8'"); 

    return $connMYSQL;
}

function verificationUser($connMYSQL) {
    $sql = "select user, password from benoitmignault_ca_mywebsite.login_organisateur WHERE user = '{$_SESSION['user']}'";
    $result = $connMYSQL->query($sql);
    if ($result->num_rows > 0){
        foreach ($result as $row) {
            if ($row['user'] === $_SESSION['user']) {
                if (password_verify($_SESSION['password'], $row['password'])) {
                    return true; // dès qu'on trouve notre user + son bon mdp on exit de la fct
                }
            }        
        }
    } else {
        return false;
    }
}

/* À refaire */
function redirection($champs) {
    // La redirection il y aura vers le timer direct
    // Ou simplement un retour à la page acceuil

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        session_destroy();
        header("Location: /erreur/erreur.php");
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['stats'])) {
            header("Location: /login/statsPoker/poker.php");
        } elseif (isset($_POST['login'])) {
            session_destroy();
            header("Location: /login/login.php?langue={$champs["typeLangue"]}");
        } elseif (isset($_POST['accueuil'])) {
            session_destroy();
            if ($typeLangue == 'english') {
                header("Location: /english/english.html");
            } else {
                header("Location: /index.html");
            }
        }
    }
    exit; // pour arrêter l'éxecution du code php
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();
    $champs["typeLangue"] = "francais";
    if (isset($_SESSION['user']) && isset($_SESSION['password']) && isset($_SESSION['typeLangue'])) {
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);
    } else {
        redirection($champs);
    }

    if (!$verificationUser) {
        redirection($champs);
    } else {        
        $champs = initialisation_Champs();
        $champs = remplissageChamps($champs);
        $valid_Champ = initialisation_indicateur();
        $choix_couleur_restant = choix_couleur_restant($connMYSQL, $champs);
        $tableau_valeur_couleur = tableau_valeur_couleur($connMYSQL, $champs);
        $tableau_petite_grosse = tableau_petite_grosse($connMYSQL, $champs);
        $arrayMots = traduction($champs);        
    }
    $connMYSQL->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    session_start();
    $champs["typeLangue"] = "francais"; 
    if (isset($_SESSION['user']) && isset($_SESSION['password']) && isset($_SESSION['typeLangue'])) {
        $connMYSQL = connexionBD();
        $verificationUser = verificationUser($connMYSQL);        
    } else {
        redirection($champs);
    }

    // on vérifier si notre user existe en bonne éduforme
    if (!$verificationUser) {
        redirection($champs);
    } else {
        $champs = initialisation_Champs();
        $valid_Champ = initialisation_indicateur();
        $champs = remplissageChamps($champs);        
        $choix_couleur_restant = choix_couleur_restant($connMYSQL);
        $tableau_valeur_couleur = tableau_valeur_couleur($connMYSQL, $champs);
        $tableau_petite_grosse = tableau_petite_grosse($connMYSQL, $champs);



        $arrayMots = traduction($champs);
    }
    $connMYSQL->close();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <!-- https://pixabay.com/fr/fichier-ic%C3%B4ne-web-document-2389211/ -->
        <link rel="shortcut icon" href="organisateur.png">	        
        <link rel="stylesheet" type="text/css" href="organisateur.css"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $arrayMots['title']; ?></title> 
        <style>
            body{
                margin:0;    
                /* Fichier photoPoker.jpg est une propriété du site https://pixabay.com/fr/cha%C3%AEne-de-blocs-personnels-2850276/ 
                sous licence libre */
                background-image: url("organisateur.jpg");
                background-position: center;
                background-attachment: fixed;
                background-size: 100%;
            }   
        </style>
    </head>
    <body>
        <h1><?php echo $arrayMots['msg_welcome']; ?></h1>
        <div class="container">
            <div class="ajout_combinaison">
                <h2><?php echo $arrayMots['h3_Ajouter']; ?></h2>    
                <form method="post" action="organisateur.php">
                    <div class='form_ajout_combinaison'>
                        <h3><?php echo $arrayMots['valeur_couleur']; ?></h3>
                        <input maxlength="5" type="text" name="valeur" value="<?php echo $champs['valeur'] ?>"> 
                        <select name="couleur">                            
                            <option value="" selected><?php echo $arrayMots['option']; ?></option>
                            <?php echo $choix_couleur_restant; ?>
                        </select>
                        <input class="bouton" type="submit" name="addValeurCouleur" value="<?php echo $arrayMots['btn_ajout']; ?>">
                        <div>
                            <p class="<?php if (isset($_POST['addValeurCouleur'])) { echo "avert"; } else { echo "erreur"; } ?>"> <?php echo $champs['message']; ?> </p>
                        </div>
                    </div>
                </form>  
                <form method="post" action="organisateur.php">
                    <div class='form_ajout_combinaison'>
                        <h3><?php echo $arrayMots['petit_grosse_mise']; ?></h3>
                        <input maxlength="5" type="text" name="small" value="<?php echo $champs['small'] ?>"> 
                        <input maxlength="5" type="text" name="big" value="<?php echo $champs['big'] ?>">
                        <input class="bouton" type="submit" name="addSmallBig" value="<?php echo $arrayMots['btn_ajout']; ?>">
                        <div>
                            <p class="<?php if (isset($_POST['addSmallBig'])) { echo "avert"; } else { echo "erreur"; } ?>"> <?php echo $champs['message']; ?> </p>
                        </div>
                    </div>
                </form>  

            </div>

            <div class="affiche_combinaison">
                <h2><?php echo $arrayMots['h3_Affichage']; ?></h2>  
                <form method="post" action="organisateur.php">
                    <div class="form_affiche_combinaison">
                        <h3><?php echo $arrayMots['valeur_couleur']; ?></h3>
                        <div><?php echo $tableau_valeur_couleur; ?></div>                                                      
                    </div>
                </form>
                <form method="post" action="organisateur.php">
                    <div class="form_affiche_combinaison">
                        <h3><?php echo $arrayMots['petit_grosse_mise']; ?></h3>
                        <div><?php echo $tableau_petite_grosse; ?></div>                                                        
                    </div>
                </form>
            </div>

            <div class="retirer_combinaison">
                <h2><?php echo $arrayMots['h3_Retirer']; ?></h2>      
                <form method="post" action="organisateur.php">
                    <div class="form_retirer_combinaison">
                        <h3><?php echo $arrayMots['valeur_couleur']; ?></h3>
                        <label for="idValeurCouleur"><?php echo $arrayMots['id_couleur']; ?></label>   
                        <select id="idValeurCouleur" name="idValeurCouleur">
                            <option value="" selected><?php echo $arrayMots['option']; ?></option>
                        </select>

                        <input class="bouton" type="submit" name="delValeurCouleur" value="<?php echo $arrayMots['btn_delete']; ?>"> 

                    </div>
                </form>
                <form method="post" action="organisateur.php">
                    <div class="form_retirer_combinaison">
                        <h3><?php echo $arrayMots['petit_grosse_mise']; ?></h3>
                        <label for="id_petite_grosse_mise"><?php echo $arrayMots['id_mises']; ?></label>
                        <select id="id_petite_grosse_mise" name="id_petite_grosse_mise">
                            <option value="" selected><?php echo $arrayMots['option']; ?></option>
                        </select>
                        <input class="bouton" type="submit" name="delSmallBig" value="<?php echo $arrayMots['btn_delete']; ?>"> 

                    </div>
                </form>

            </div>

            <div class="retour">
                <form method="post" action="organisateur.php">
                    <div class="form_retour">
                        <div class="btn_footer">
                            <input class="bouton" type="submit" name="stats" value="<?php echo $arrayMots['btn_timer']; ?>">
                        </div>
                        <div class="btn_footer">
                            <input class="bouton" type="submit" name="login" value="<?php echo $arrayMots['btn_return']; ?>">
                        </div>                        
                    </div>
                </form> 
            </div>

        </div>
    </body>
</html>