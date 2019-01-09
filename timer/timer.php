<?php
function traduction($champs){
    if ($champs['typeLangue'] === "francais"){
        $title = 'Minuteur';
        $typeMise = 'Les mises possible';
        $small = 'La petite mise';
        $big = 'La grosse mise';
        $retour = 'Page d\'Accueil';
        $changer = 'Changer mise';
        $btnReset = 'Reset du Temps';
        $reset = 'Reset des mises';
        $periode = 'En attente d\'une période de temps...';
        $btnReprendre = 'POURSUIVRE';
        $choixOrganisateur = 'Veuillez choisir votre organisateur';
        $option = "À sélectionner";
        $btn_choix = 'Choisir';
        $message = message_Situation($champs);
        $message_Erreur_BD = "Il y a eu un problème avec l'insertion de vos valeurs dans la BD. Veuillez recommencer !";
    } elseif ($champs['typeLangue'] === "english") {
        $title = 'Timer';
        $choixOrganisateur = 'Please choose an organizer';
        $typeMise = 'Bets availables';
        $small = 'The small blind';
        $big = 'The big blind';
        $retour = 'HOME';
        $changer = 'Change bet';
        $btnReset = 'Time Reset';
        $reset = 'Bets Reset';
        $periode = 'Waiting for a period of time...';
        $btnReprendre = 'GO ON';
        $option = "Select";
        $btn_choix = 'PICK OUT';
        $message = message_Situation($champs);
        $message_Erreur_BD = "There was a problem with insert your values into the DB. Please try again !";
    }
    $tableauLinguiste = ['title' => $title, 'typeMise' => $typeMise, 'changerMise' => $changer, 'choixOrganisateur' => $choixOrganisateur, 'option' => $option, 'btn_choix' => $btn_choix, 'message' => $message, 'message_Erreur_BD' => $message_Erreur_BD, 'small' => $small, 'big' => $big, 'retour' => $retour, 'btnReset' => $btnReset, 'reset' => $reset, 'periode' => $periode, 'btnReprendre' => $btnReprendre];
    return $tableauLinguiste;
}

function message_Situation($champs){
    $message = "";
    if ($champs["typeLangue"] === 'francais') {
        switch ($champs['situation']){
            case 1 : $message = "Votre choix organisateur ne peut être null !"; break;
        }
    } elseif ($champs["typeLangue"] === 'english') {
        switch ($champs['situation']){
            case 1 : $message = "Your organizer choice can not be null !"; break;
        }
    }
    return $message;
}

function initialisation_Champs() {
    $champs = ["typeLangue" => "", "user" => "", "nom_organisateur" => "", "situation" => 0, "combinaison" => 0, "valeurSmall" => "00", "valeurBig" => "00", "aucune_valeur" => false, "trop_valeur" => false, "number_Red" => 255, "number_Green" => 255, "number_Blue" => 255]; 
    return $champs;
}

function initialisation_indicateur() {
    $valid_champs = ["user_vide" => false, "changement_mise" => false, "reset_mise" => false, "choix_user" => false];
    return $valid_champs;
}

function remplissageChamps($champs) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET'){
        if (isset($_GET['langue'])){
            $champs['typeLangue'] = $_GET['langue'];
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){        
        if (isset($_POST['btn_choixOrganisateur'])){
            if (isset($_POST['typeLangue'])){     
                $champs['typeLangue'] = $_POST['typeLangue'];
            }
            if (isset($_POST['choixOrganisateur'])){
                $champs['user'] = $_POST['choixOrganisateur'];
            }
        } elseif (isset($_POST['btnReturn'])){
            if (isset($_POST['typeLangueReturn'])){     
                $champs['typeLangue'] = $_POST['typeLangueReturn'];
            }
        } elseif (isset($_POST['btn_changerMise'])){
            if (isset($_POST['typeLangue'])){     
                $champs['typeLangue'] = $_POST['typeLangue'];
            }
            if (isset($_POST['choixOrganisateur'])){
                $champs['user'] = $_POST['choixOrganisateur'];
            }
            $champs['combinaison'] = intval($_POST['combinaison']);
            $champs['combinaison']++;
            if (isset($_POST['number_Red'])){
                $champs['number_Red'] = intval($_POST['number_Red']);
            }
            if (isset($_POST['number_Green'])){
                $champs['number_Green'] = intval($_POST['number_Green']);
            }
            if (isset($_POST['number_Blue'])){
                $champs['number_Blue'] = intval($_POST['number_Blue']);
            }
            $value_Red_temp = $champs['number_Red'] - 25;
            $value_Green_temp = $champs['number_Green'] - 25;
            // Si la partie bleu et vert sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
            if ($value_Green_temp > 0){
                $champs['number_Green'] = $value_Green_temp;
                $champs['number_Blue'] = $value_Green_temp;
                // Si la partie rouge sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
            } elseif ($value_Red_temp > 0){
                $champs['number_Red'] = $value_Red_temp;
                $champs['number_Green'] = 0;
                $champs['number_Blue'] = 0;
            } else {
                $champs['number_Red'] = 0;
                $champs['number_Green'] = 0;
                $champs['number_Blue'] = 0;
            }

        } elseif (isset($_POST['btn_resetMise'])){
            if (isset($_POST['typeLangue'])){     
                $champs['typeLangue'] = $_POST['typeLangue'];
            }
            if (isset($_POST['choixOrganisateur'])){
                $champs['user'] = $_POST['choixOrganisateur'];
            }
            $champs['combinaison'] = 0;
            $champs['number_Red'] = 255;
            $champs['number_Green'] = 255;
            $champs['number_Blue'] = 255;
        }
    }
    return $champs;
}

function coloriage($champs) {
    return "rgb({$champs['number_Red']},{$champs['number_Green']},{$champs['number_Blue']})";
}

function validation($champs, $valid_champs){
    if ($champs['user'] === ""){
        $valid_champs["user_vide"] = true;     
    }
    if (isset($_POST['btn_changerMise'])){        
        $valid_champs["changement_mise"] = true; 
    } elseif (isset($_POST['btn_resetMise'])){
        $valid_champs["reset_mise"] = true; 
    } elseif (isset($_POST['btn_choixOrganisateur'])){
        $valid_champs["choix_user"] = true; 
    }
    return $valid_champs;
}

function situation($champs, $valid_champs) {
    $situation = 0;
    if ($valid_champs['user_vide']){
        $situation = 1; // Le user ne peut être vide
    } elseif ($valid_champs['changement_mise']){
        $situation = 2; // Un changement de mise a été demandé
    } elseif ($valid_champs['reset_mise']){
        $situation = 3; // Un reset des mises a été demandé
    } elseif ($valid_champs['choix_user']){
        $situation = 4; // Un choix de uzser est fait
    }
    return $situation;   
}

function liste_Organisateurs($connMYSQL, $champs, $tableauLinguiste){
    $liste_Organisateurs = "";
    $sql = "SELECT * FROM login_organisateur order by name";
    $result = $connMYSQL->query($sql);   
    if ($result->num_rows > 0){
        if ($_SERVER['REQUEST_METHOD'] == 'GET'){
            $liste_Organisateurs .= "<option value=\"\" selected>{$tableauLinguiste['option']}</option>";
            foreach ($result as $row) {
                $liste_Organisateurs .= "<option value=\"{$row['user']}\">{$row['name']}</option>";
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $liste_Organisateurs .= "<option value=\"\">{$tableauLinguiste['option']}</option>";
            foreach ($result as $row) {
                if ($champs['user'] === $row['user']){
                    $liste_Organisateurs .= "<option value=\"{$row['user']}\" selected>{$row['name']}</option>";
                } else {
                    $liste_Organisateurs .= "<option value=\"{$row['user']}\">{$row['name']}</option>";
                }
            }
        }
    }
    return $liste_Organisateurs;
}

function affichage_nom_organisateur($connMYSQL, $champs){
    $prenom = "";
    $sql = "SELECT name FROM login_organisateur where user = '{$champs['user']}'";
    $result = $connMYSQL->query($sql);   
    if ($result->num_rows > 0){
        foreach ($result as $row) {
            $prenom = $row['name'];
        }
    }
    return $prenom;
}

function creation_tableau($connMYSQL, $champs){
    $tableau = "";
    $sql = "SELECT amount, color_english FROM amount_color where user = '{$champs['user']}' order by amount";
    $result = $connMYSQL->query($sql);   
    if ($result->num_rows > 0){
        if ($champs["typeLangue"] == "francais"){
            $tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Valeur</th><th>Couleur</th></tr></thead>";
        } elseif ($champs["typeLangue"] == "english") {
            $tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Value</th><th>Color</th></tr></thead>";
        }    
        $tableau .= "<tbody>";
        foreach ($result as $row) {
            $tableau .= "<tr><td class=\"colorModifie\">{$row['amount']}</td> <td bgcolor=\"{$row['color_english']}\"></td> </tr>";
        }
        $tableau .= "</tbody></table>";
    }
    return $tableau;
}

function selection_small_big_blind($connMYSQL, $champs){
    // Au moment arriver ici, la combinaison est aumenter précédament dans l'autre fonction
    $result_double_dimention = [];
    $sql = "SELECT small, big FROM mise_small_big where user = '{$champs['user']}' order by small";
    $result = $connMYSQL->query($sql);  

    if ($result->num_rows > 0){ 
        foreach ($result as $row) {
            // On insère tous les couples small et big dans le tableau double dimensions
            $couple = ["small" => $row['small'], "big" => $row['big']];  
            $result_double_dimention[] = $couple;
        }
        // le nombre de la ligne est toujours pareil
        $nbLignes = $result->num_rows;
        if ($champs['combinaison'] <= $nbLignes){
            foreach ($result_double_dimention as $couple => $value){
                // On passe en revue toutes les combinaisons et lorsque nous arrivons à celle que nous voulons afficher on stock les données dans les deux variables
                if ($champs['combinaison'] == $couple){
                    $champs['valeurSmall'] = $value['small'];        
                    $champs['valeurBig'] = $value['big'];        
                }
            }
            $nbLignes--;
            // $champs['combinaison']++ ne jamais faire ca en validation d'une condition
            // Ici, nous avons atteint la derniere combinaison small et big
            if ($champs['combinaison'] == $nbLignes){
                $champs['trop_valeur'] = true;
            }
        } 
        // Le retour de fonction n'a trouvé aucun valeur
    } elseif ($result->num_rows == 0){ 
        $champs['aucune_valeur'] = true;
    } 

    return $champs;
}

function redirection($champs) {  
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        header("Location: /erreur/erreur.php");
    } elseif (isset($_POST['btnReturn'])) {        
        if ($champs["typeLangue"] == 'english') {
            header("Location: /english/english.html");
        } elseif ($champs["typeLangue"] == 'francais') {
            header("Location: /index.html");
        }
    } else {
        header("Location: /erreur/erreur.php");
    }
    exit; // pour arrêter l'éxecution du code php
}

function connexionBD() {
    // Nouvelle connexion sur hébergement du Studio OL
    $host = "localhost";
    $user = "benoitmi_benoit";
    $password = "d-&47mK!9hjGC4L-";
    $bd = "benoitmi_benoitmignault.ca.mysql";

    /*
    $host = "localhost";
    $user = "zmignaub";
    $password = "Banane11";
    $bd = "benoitmignault_ca_mywebsite";
    */
    $connMYSQL = mysqli_connect($host, $user, $password, $bd);
    $connMYSQL->query("set names 'utf8'"); // ceci permet d'avoir des accents affiché sur la page web !
    return $connMYSQL;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $champs = initialisation_Champs();
    $champs = remplissageChamps($champs);    
    if ($champs['typeLangue'] !== "francais" && $champs['typeLangue'] !== "english"){
        redirection($champs);
    } else {
        $connMYSQL = connexionBD();
        $tableauLinguiste = traduction($champs);
        $liste_Organisateurs = liste_Organisateurs($connMYSQL, $champs, $tableauLinguiste);
    }
    $connMYSQL->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $champs = initialisation_Champs();
    $valid_champs = initialisation_indicateur();
    $champs = remplissageChamps($champs);    
    if ($champs['typeLangue'] !== "francais" && $champs['typeLangue'] !== "english"){
        redirection($champs);
    } else {
        if (isset($_POST['btnReturn'])){
            redirection($champs);
        } else { 
            $connMYSQL = connexionBD();        
            $valid_champs = validation($champs, $valid_champs);
            $champs['situation'] = situation($champs, $valid_champs);
            $tableauLinguiste = traduction($champs);

            if ($champs['situation'] === 1){
                echo "<script>alert('".$tableauLinguiste['message']."')</script>";
            } else {
                $champs['nom_organisateur'] = affichage_nom_organisateur($connMYSQL, $champs);
                $tableau_valeur_couleur = creation_tableau($connMYSQL, $champs);
                if ($tableau_valeur_couleur === ""){ 
                    $msgErr = "";                
                    if ($champs["typeLangue"] == "francais"){
                        $msgErr = "Attention ! Votre organisateur n'a pas choisi ses valeurs associées à ses jetons.\\nVeuillez le contacter pour lui demander de bien vouloir créer ses ensembles valeur couleur de jetons.";
                    } elseif ($champs["typeLangue"] == "english") {
                        $msgErr = "Warning ! Your organizer did not choose his values associated with his chips.\\nPlease contact him to ask him to create his value color sets of tokens.";
                    }
                    echo "<script>alert(\"$msgErr\")</script>";
                } else {
                    $champs = selection_small_big_blind($connMYSQL, $champs);
                    if ($champs['aucune_valeur']){
                        $msgErr = "";                
                        if ($champs["typeLangue"] == "francais"){
                            $msgErr = "Attention ! Votre organisateur n'a pas choisi ses valeurs associées aux mises petites et grosses mises.\\nVeuillez le contacter pour lui demander de bien vouloir créer ses ensembles petite mise et grosse mise.";
                        } elseif ($champs["typeLangue"] == "english") {
                            $msgErr = "Warning ! Your organizer has not chosen his values associated with small and large bets.\\nPlease contact him to ask him to create his sets small bet and big bet.";
                        }
                        echo "<script>alert(\"$msgErr\")</script>";
                    } 
                }

            }
            $liste_Organisateurs = liste_Organisateurs($connMYSQL, $champs, $tableauLinguiste);
            $connMYSQL->close();            
        }        
    } 
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="compteur" content="timer"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Fichier favicon.ico est une propriété du site web : https://pixabay.com/fr/radio-r%C3%A9veil-alarme-temps-horloge-295228/ -->
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" type="text/css" href="timer.css">
        <style>
            body{
                margin:0; 
                /* https://pxhere.com/fr/photo/1280141 */
                background-image: url("timer.jpg");
                background-position: center;
                background-attachment: fixed;
                background-size: 100%;
            }
            .container > .timer > .tableauDesMises > .lesMises > div > .blind{
                color :<?php echo coloriage($champs); ?>
            }            
        </style>       
        <title><?php echo $tableauLinguiste['title'] ?></title>        
    </head>
    <body>        
        <!-- Fichier alert.wav est une propriété du site web : https://www.memoclic.com/sons-wav/766-sonneries-et-alarmes/page-1.html -->
        <audio  id="alertSound"><source src="alert.wav" type="audio/wav"></audio>
        <div class="container">
            <div class="tableau_bord">
                <form method="post" action="./timer.php" id="formulaire">
                    <div class="choix">
                        <input type="hidden" id="number_Red"  name="number_Red" value="<?php echo $champs['number_Red']; ?>">
                        <input type="hidden" id="number_Green" name="number_Green" value="<?php echo $champs['number_Green']; ?>">
                        <input type="hidden" id="number_Blue" name="number_Blue" value="<?php echo $champs['number_Blue']; ?>">
                        <input type="hidden" id="trop_valeur" name="trop_valeur" value="<?php if ($champs['trop_valeur']) { echo "true"; } else { echo "false"; } ?>">
                        <input type="hidden" id="typeLangue" name="typeLangue" value="<?php echo $champs['typeLangue']; ?>">
                        <input type="hidden" class="combinaison" name="combinaison" value="<?php echo $champs['combinaison']; ?>">
                        <label class="modificationColor" for="choixOrganisateur"><?php echo $tableauLinguiste['choixOrganisateur']; ?></label>   
                        <select id="choixOrganisateur" name="choixOrganisateur"> 
                            <?php echo $liste_Organisateurs; ?>
                        </select>
                        <input class="bouton" type="submit" name="btn_choixOrganisateur" value="<?php echo $tableauLinguiste['btn_choix']; ?>"> 
                    </div>
                </form>
                <div class="affichage_choix">               
                    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && $champs['situation'] != 1) { 
    echo $tableau_valeur_couleur;
}
                    ?>
                </div>
            </div>
            <div class="timer">
                <div class="tableauDesMises">
                    <div class="lesMises">
                        <div class="titre">
                            <p class="resizeText"><?php echo $tableauLinguiste['typeMise'] ?></p>
                        </div>
                        <div class="small">
                            <p class="resizeText"><?php echo $tableauLinguiste['small'] ?></p>
                        </div>
                        <div class="big">
                            <p class="resizeText"><?php echo $tableauLinguiste['big'] ?></p>
                        </div>            
                        <div class="valeurSmall">
                            <p class="blind" id="valeurSmall"><?php echo $champs['valeurSmall'] ?></p>
                        </div>
                        <div class="valeurBig">
                            <p class="blind" id="valeurBig"><?php echo $champs['valeurBig'] ?></p>
                        </div>                
                    </div>
                    <div class="lesBoutonsMises">
                        <div class="double">
                            <button name="btn_changerMise"<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' || $champs['situation'] === 1 || $champs['trop_valeur'] || $champs['aucune_valeur']) { ?> class="disabled" disabled <?php } ?> id="double" form="formulaire"><?php echo $tableauLinguiste['changerMise'] ?></button>
                        </div>
                        <div class="resetMise">
                            <button form="formulaire" name="btn_resetMise"<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' || $champs['combinaison'] < 1 || $champs['aucune_valeur']) { ?> class="disabled" disabled <?php } ?> form="formulaire" id="reset"><?php echo $tableauLinguiste['reset'] ?></button>
                        </div>                
                    </div>
                </div>

                <div class="tableauDuTemps">
                    <div class="temps">
                        <div class="periode">
                            <p class="resizeText"><?php echo $tableauLinguiste['periode'] ?></p>
                        </div>
                        <div class="minutes">
                            <p class="resizeText">Minutes</p>
                        </div>
                        <div class="secondes">
                            <p class="resizeText">Secondes</p>
                        </div>
                        <div class="chiffreMin">
                            <p>00</p>
                        </div>
                        <div class="chiffreSec">
                            <p>00</p>
                        </div>                 
                    </div>   
                    <div class="lesBoutonsActions">
                        <div class="min15">
                            <button <?php if ($champs['aucune_valeur']) { ?> class="disabled" disabled <?php } ?> id="timer15">15</button>
                        </div>
                        <div class="min30">
                            <button <?php if ($champs['aucune_valeur']) { ?> class="disabled" disabled <?php } ?> id="timer30">30</button>
                        </div>                
                        <div class="stop">                            
                            <button class="disabled" disabled id="timerStop">STOP</button>
                        </div>
                        <div class="reprend">
                            <button class="disabled resizeText" disabled id="timerReprend"><?php echo $tableauLinguiste['btnReprendre'] ?></button>
                        </div>
                        <div class="resetTemps">
                            <button <?php if ($_SERVER['REQUEST_METHOD'] == 'GET' || $champs['aucune_valeur'] || $champs['user'] == "") { ?> class="disabled" disabled <?php } ?> id="ResetTemps"><?php echo $tableauLinguiste['btnReset'] ?></button>
                        </div>                
                    </div> 
                </div> 
            </div>
        </div>
        <hr>
        <div class="boutonRetour">
            <div class="retour">
                <form method="post" action="./timer.php">
                    <input class="resizeText" type="submit" name="btnReturn" value="<?php echo $tableauLinguiste['retour'];?>">   
                    <input type="hidden" name="typeLangueReturn" value="<?php echo $champs['typeLangue']; ?>">
                </form>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script> 
        <script src="detect-zoom.js"></script>
        <script src="detect-zoom.min.js"></script>
        <script type="text/javascript" src="timer.js"></script>

    </body>
</html>