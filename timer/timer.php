<?php 
function tranduction($typeLangage){
    $tableauLinguiste = [];
    if ($typeLangage === "francais"){
        $tableauLinguiste = ['title' => 'Minuteur', 
                             'h2-1' => 'Voici la section des valeurs en jeton possible !', 
                             'h2-2' => 'Voici la section des périodes de temps possible !', 
                             'typeMise' => 'Les mises possible', 
                             'small' => 'La petite mise', 'radioAuto' => 'Auto',
                             'radioManuel' => 'Manuel',
                             'btnChangerManuel' => 'Changer manuellement',
                             'champPetite' => 'Modification de la petite mise',
                             'champGrosse' => 'Modification de la grosse mise',
                             'big' => 'La grosse mise', 'retour' => 'Retour à l\'acceuil',
                             'foisDeux' => 'Doubler', 'btnReset' => 'Reset du Temps',
                             'reset' => 'Reset des mises', 
                             'periode' => 'En attente d\'une période de temps...', 
                             'btnReprendre' => 'POURSUIVRE'];
    } elseif ($typeLangage === "english") {
        $tableauLinguiste = ['title' => 'Timer', 
                             'radioAuto' => 'Auto',
                             'radioManuel' => 'Manual',
                             'champPetite' => 'Changing the small bet',
                             'champGrosse' => 'Changing the big bet',
                             'btnChangerManuel' => 'Change manually',
                             'h2-1' => 'This is the section of values the ships !', 
                             'h2-2' => 'This is the section of period of time !', 
                             'typeMise' => 'Bets availables', 
                             'small' => 'The small blind', 
                             'big' => 'The big blind', 
                             'foisDeux' => 'Double', 'btnReset' => 'Reset of time',
                             'reset' => 'Reset of bets', 'retour' => 'Return to home page',
                             'periode' => 'Waiting for a period of time...', 
                             'btnReprendre' => 'CONTINUE'];
    }
    return $tableauLinguiste;
}

// https://www.w3schools.com/php/php_ajax_database.asp
// Exemple comment aller chercher l'information

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $typeLanguage = $_GET['langue'];
    if ($typeLanguage !== "francais" && $typeLanguage !== "english"){
        header("Location: /erreur/erreur.php");
        exit;
    } else {
        $tableauLinguiste = tranduction($typeLanguage);    
    }  
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {    
    $typeLanguage = $_POST['typeLanguage'];
    if ($typeLanguage === 'english'){
        header("Location: /english/english.html"); 
        exit;
    } elseif ($typeLanguage === 'francais'){
        header("Location: /index.html");
        exit;
    } else {
        header("Location: /erreur/erreur.php");
        exit;
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
        <title><?php echo $tableauLinguiste['title'] ?></title>        
    </head>
    <body>        
        <!-- Fichier alert.wav est une propriété du site web : https://www.memoclic.com/sons-wav/766-sonneries-et-alarmes/page-1.html -->
        <audio  id="alertSound"><source src="alert.wav" type="audio/wav"></audio>
        <h2><?php echo $tableauLinguiste['h2-1'] ?></h2>
        <div class="tableauDesMises">
            <div class="lesMises">
                <div class="titre">
                    <p><?php echo $tableauLinguiste['typeMise'] ?></p>
                </div>
                <div class="small">
                    <p><?php echo $tableauLinguiste['small'] ?></p>
                </div>
                <div class="big">
                    <p><?php echo $tableauLinguiste['big'] ?></p>
                </div>            
                <div class="valeurSmall">
                    <p id="valeurSmall">25</p>
                </div>
                <div class="valeurBig">
                    <p id="valeurBig">50</p>
                </div>
                <?php // Informatino ajouter à la demande de Philippe M ?>
                <div class="new">
                    <label for="newSmall"><?php echo $tableauLinguiste['champPetite'] ?></label>
                    <input name="newSmall" type="text" id="newSmall">
                </div>
                <div class="new">
                    <label for="newBig"><?php echo $tableauLinguiste['champGrosse'] ?></label>
                    <input name="newBig" type="text" id="newBig">
                </div> 
                <?php // fin des ajouts ?>
            </div>
            <div class="lesBoutonsMises">
                <div class="double">
                    <button id="double"><?php echo $tableauLinguiste['foisDeux'] ?></button>
                </div>
                <div class="resetMise">
                    <button class="disabled" disabled id="reset"><?php echo $tableauLinguiste['reset'] ?></button>
                </div>
                <?php // Information ajouter à la demande de Philippe M ?>
                <div class="new">
                    <div>
                        <label for="newAuto"><?php echo $tableauLinguiste['radioAuto'] ?></label>
                        <input checked type="radio" name="new" id="newAuto" value="auto">
                    </div>
                    <div>
                        <label for="newManuelle"><?php echo $tableauLinguiste['radioManuel'] ?></label>
                        <input type="radio" name="new" id="newManuelle" value="manuelle">
                    </div>
                </div>
                <div class="new">
                    <button id="changeType"><?php echo $tableauLinguiste['btnChangerManuel'] ?></button>
                </div>
                <?php // fin des ajouts ?>
            </div>
        </div>

        <h2><?php echo $tableauLinguiste['h2-2'] ?></h2>

        <div class="tableauDuTemps">
            <div class="timer">
                <div class="periode">
                    <p><?php echo $tableauLinguiste['periode'] ?></p>
                </div>
                <div class="minutes">
                    <p>Minutes</p>
                </div>
                <div class="secondes">
                    <p>Secondes</p>
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
                    <button id="timer15">15</button>
                </div>
                <div class="min30">
                    <button id="timer30">30</button>
                </div>                
                <div class="stop">
                    <button class="disabled" disabled id="timerStop">STOP</button>
                </div>
                <div class="reprend">
                    <button class="disabled" disabled id="timerReprend"><?php echo $tableauLinguiste['btnReprendre'] ?></button>
                </div>
                <div class="resetTemps">
                    <button id="ResetTemps"><?php echo $tableauLinguiste['btnReset'] ?></button>
                </div>                
            </div> 
        </div>  
        <hr>
        <div class="boutonRetour">
            <div class="retour">
                <form method="post" action="./timer.php">
                    <input type="submit" name="btnReturn" value="<?php echo $tableauLinguiste['retour'];?>">
                    <input class="disabled" type="hidden" name="typeLanguage" id="typeLanguage" value="<?php echo $typeLanguage ?>">
                </form>
            </div>
        </div>
        <script type="text/javascript" src="timer.js"></script>
    </body>
</html>