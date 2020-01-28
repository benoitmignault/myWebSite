<?php
// Cette fonction sera appellée lors de la création des deux nombres.
// Elle ne fait pas la distinction entre nombre 1 et nombre 2 
function constructionNombre(float $nombre, float $unChiffre) {
    if (empty($nombre)) {
        $nombre = (float) $unChiffre;
    } else {
        // Exemple : on avait 5 et on appuy sur 4 donc on aura 54....etc
        $nombre = (float) ($nombre * 10) + $unChiffre;
    }
    return $nombre;
}

// Cette fonction assurera la traduction du français vers l'anglais et le retour au français...
// Chaque variable globale aura un nom significatif
function traduction(string $typeLangue) {
    global $title, $h1, $legend1, $legend2;
    global $label1, $label2, $label3;
    global $boutonReset, $procedure, $valeurReturn;
    global $information, $messageNombre1, $messageNombre2;
    global $messageFinal, $messageType, $messageError;
    global $languagePage;
    if ($typeLangue === 'francais') {
        $title = "Calculatrice";
        $languagePage = "fr";
        $h1 = "Voici ma calculatrice version Web";
        $legend1 = "Procédure d'utilisation";
        $legend2 = "Opération arithmétique sur deux nombres";
        $label1 = "Nombre 1:";
        $label2 = "Nombre 2:";
        $label3 = "Résultat de l'opération :";
        $boutonReset = "Effacer";
        $procedure = "<li>Pour commencer, nous devons choisir un premier nombre</li>
                        <li>Une fois ce nombre déterminé, nous pouvons choisir le type d'opération à effectuer</li>
                        <li>Une fois l'opération choisie, cette dernière sera affichée entre les deux nombres</li>
                        <li>Maintenant, on doit choisi un deuxième nombre</li>
                        <li>Une fois ce deuxième nombre déterminé, nous pouvons appuyer sur le bouton « = »</li>
                        <li>Le résultat de l'opération apparaîtra dans le champ texte du même nom</li> 
                        <li>Tant qu'on n'utilisera pas le bouton effacer, le type d'opération pourra être changer</li>
                        <li>Lors d'une manipulation invalide, entraînera un message d'avertissement dans le champ concerné</li>";
        $valeurReturn = "Retour à la page d'accueil";
        $information = "À tout moment, vous pouvez utiliser le bouton <strong>«Effacer»</strong> pour recommencer du début";
        $messageNombre1 = "Nombre 1 invalide";
        $messageNombre2 = "Nombre 2 invalide";
        $messageFinal = "Type opérateur manquant";
        $messageType = "Mauvais type opérateur";
        $messageError = "Résultat invalide";
    } elseif ($typeLangue === 'english') {
        $title = "Calculator";
        $languagePage = "en";
        $h1 = "This is the web version of my calculator";
        $legend1 = "How to use";
        $legend2 = "Arithmetic operation on two inputs";
        $label1 = "Input one:";
        $label2 = "Input two:";
        $label3 = "Result of the operation:";
        $boutonReset = "Erase";
        $procedure = "<li>We need to choose a first input</li>
                        <li>Once we have our first input, we need to choose an operator type</li>
                        <li>Once we have our operator type, this operator will be between the two inputs</li>
                        <li>Now, we need to choose a second input</li>
                        <li>Once we have our second input, we can use the button « = »</li>
                        <li>The result of the operation will be in the textfield with the same name</li> 
                        <li>As long as we don't use the erase button, we will be able to change our operator type</li>
                        <li>During an invalid usage, a warning will be displayed in the textfield appropriate</li>";
        $valeurReturn = "Return to the home page";
        $information = "At any moment, you can use the button <strong>«Erase»</strong> to restart from the beginning";
        $messageNombre1 = "Invalid first input";
        $messageNombre2 = "Invalid second input";
        $messageFinal = "Operator missing";
        $messageType = "Bad operator type";
        $messageError = "Invalid result";
    }
}

// À la première entrée sur la calculatrice avec la request GET, 
// je dois initialiser les variables à vide
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $afficher = "hidden";
    $nombre1 = 0; // le nombre en construction 
    $nombre2 = 0; // le nombre en construction 
    $nombreFinal = 0; // un nombre prêt à être utiliser 
    $typeOpe = ""; // le type opération    
    $typeLangue = $_GET['langue'];
    if ($typeLangue !== "francais" && $typeLangue !== "english") {
        header("Location: /erreur/erreur.php");
        exit;
    } else {
        traduction($typeLangue);
    }
}

// À chaque post envoyer au serveur, je ré-initialise 
// mes quatres variables et que je stock l'information pour utilisation futur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $afficher = $_POST['visible_Info']; // l'information à savoir si les instructions sont affichées ou pas 
    $nombre1 = $_POST['nombre1']; // le nombre 1 en construction 
    $nombre2 = $_POST['nombre2']; // le nombre 2 en construction
    $nombreFinal = $_POST['nombreFinal']; // le résultat de l'opération
    $typeOpe = $_POST['typeOpe']; // le type opération
    $typeLangue = $_POST['typeLangue']; // le type de page pour revenir en français ou en anglais       
    // Pour retourner à la page principale
    if (isset($_POST['return'])) {
        if ($typeLangue == 'english') {
            header("Location: /english/english.html");
            exit;
        } elseif ($typeLangue == 'francais') {
            header("Location: /index.html");
            exit;
        }
    } else {
        traduction($typeLangue);
    }

    // Cette condition est pour créer le nombre composé de un à plusieurs chiffres        
    if (isset($_POST['un']) || isset($_POST['deux']) || isset($_POST['trois']) ||
            isset($_POST['quatre']) || isset($_POST['cinq']) || isset($_POST['six']) ||
            isset($_POST['sept']) || isset($_POST['huit']) || isset($_POST['neuf']) ||
            isset($_POST['zero'])) {

        // On stocke la valeur du chiffre qu'on vient de peser
        foreach ($_POST as $key => $info) {
            // Cette condition exclu les autres objets non nécessaire pour le moment
            if (($key != "nombreFinal") && ($key != "nombre1") &&
                    ($key != "nombre2") && ($key != "typeOpe") && ($key != "typeLangue")) {
                $unChiffre = (float) $info;
            }
        }
        // Si il y a des valeurs non numériques dans les nombres 1 et 2 , 
        // on les remet à zéro...
        if (!(is_numeric($nombre1))) {
            $nombre1 = 0;
        }
        if (!(is_numeric($nombre2))) {
            $nombre2 = 0;
        }

        // Si la variable «$typeOpe» est vide, alors nous sommes dans le nombre1
        // Sinon, nous sommes dans le nombre2
        // Important de ne pas avori de caractères null ou non numérique 
        // car sinon on revient à la valeur 0...
        switch ($typeOpe) {
            case "" : $nombre1 = constructionNombre($nombre1, $unChiffre);
                break;
            default : $nombre2 = constructionNombre($nombre2, $unChiffre);
                break;
        }
    }

    // Lorsqu'on pèse sur l'une des opérations arithmétiques on commence à préparer l'opération futur
    if (isset($_POST['add']) || isset($_POST['sous']) ||
            isset($_POST['multi']) || isset($_POST['div'])) {
        // On ne peut pas faire une opération si le nombre 1 est vide
        if (!(is_numeric($nombre1)) || !(is_numeric($nombre2))) {
            $nombre1 = $messageNombre1;
            $nombre2 = $messageNombre2;
        } else {
            foreach ($_POST as $key => $info) {
                // En fonction de la confition du if, il peut avoir seulement le type opérateur 
                // qui sera utiliser plutard dans un switch/case pour déterminer de quel genre opération qu'on a à faire
                if (($key != "nombre1") && ($key != "nombreFinal") &&
                        ($key != "nombre2") && ($key != "typeOpe") && ($key != "typeLangue")) {
                    $typeOpe = $info;
                }
            }
        }
    }

    // Lorsqu'on appui sur «=» on fait l'opération demandé via «$typeOpe» avec les deux nombres
    if (isset($_POST['resultFinal'])) {
        // On ne peut pas faire une opération si on ne sait pas cette dernière
        if (empty($typeOpe)) {
            $nombreFinal = $messageFinal;
            // Sinon si, un des deux chiffres n'est pas numérique ce n'est pas permit
        } elseif ($typeOpe != "+" && $typeOpe != "/" &&
                $typeOpe != "*" && $typeOpe != "-") {
            $nombreFinal = $messageType;
        } elseif (!(is_numeric($nombre1)) || !(is_numeric($nombre2))) {
            $nombre1 = $messageNombre1;
            $nombre2 = $messageNombre2;
            $nombreFinal = $messageError;
        } else {
            // on va utiliser le charactère qui correspondra au type opération
            switch ($typeOpe) {
                case "+" : $nombreFinal = $nombre1 + $nombre2;
                    break;
                case "-" : $nombreFinal = $nombre1 - $nombre2;
                    break;
                case "/" : if ($nombre2 == 0) {
                        $nombreFinal = "Impossible !!!";
                    } else {
                        $nombreFinal = $nombre1 / $nombre2;
                    }
                    break;
                case "*" : $nombreFinal = $nombre1 * $nombre2;
                    break;
            }
        }
    }

    // Si on appui sur le bouton «reset» alors on remet les deux champs à 0...
    if (isset($_POST['reset'])) {
        $nombre1 = 0;
        $nombre2 = 0;
        $nombreFinal = 0;
        $typeOpe = "";
    }
}
?>
<!-- Le début de l'écriture de la page html de la calculatrice -->
<!DOCTYPE html>
<html lang="<?php echo $languagePage ?>">

<head>
    <title> <?php echo $title ?> </title>
    <link rel="stylesheet" href="calcul.css" />
    <!-- Le fichier calcul.png est la propriété du site https://pixabay.com/fr/calculatrice-les-math%C3%A9matiques-t%C3%A2che-1019743/ mais en utilisation libre-->
    <link rel="shortcut icon" href="calcul.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            /*Le fichier pageCalculatrice.jpg est la propriété du site https://pixabay.com/fr/calculatrice-compte-calculette-2359760/ mais en utilisation libre-->*/
            background-image: url("pageCalculatrice.jpg");
            background-position: center;
            background-attachment: fixed;
            background-size: auto;
            background-repeat: no-repeat;
        }

    </style>
</head>

<body>
    <h1 class="H1titre"> <?php echo $h1 ?> </h1>
    <fieldset class="procedure">
        <legend class="legendCenter" id="legend1">
            <a class="faireAfficher" href=""> <?php echo $legend1 ?> </a>
        </legend>
        <ol class="lesInstruction"> <?php echo $procedure ?> </ol>
    </fieldset>
    <p id="info"> <?php echo $information ?> </p>
    <form method="post" action="./calcul.php">
        <fieldset class="calcul">
            <legend class="legendCenter" id="legend2"> <?php echo $legend2 ?></legend>
            <input id="info_Instruction" type="hidden" name="visible_Info" value="<?php echo $afficher ?>">
            <input class="button" type="submit" value=1 name="un">
            <input class="button" type="submit" value=2 name="deux">
            <input class="button" type="submit" value=3 name="trois">
            <br>
            <input class="button" type="submit" value=4 name="quatre">
            <input class="button" type="submit" value=5 name="cinq">
            <input class="button" type="submit" value=6 name="six">
            <br>
            <input class="button" type="submit" value=7 name="sept">
            <input class="button" type="submit" value=8 name="huit">
            <input class="button" type="submit" value=9 name="neuf">
            <br>
            <input class="button" type="submit" value=0 name="zero">
            <br><br>
            <input class="buttonOpe" type="submit" name="add" value="+">
            <input class="buttonOpe" type="submit" name="sous" value="-">
            <input class="buttonOpe" type="submit" name="multi" value="*">
            <input class="buttonOpe" type="submit" name="div" value="/">
            <br><br>
            <label id="label1"> <?php echo $label1 ?> </label>
            <input type="text" name="nombre1" value="<?php echo $nombre1 ?>">
            <br>
            <input type="text" style="width:10px;" name="typeOpe" value="<?php echo $typeOpe ?>">
            <br>
            <label id="label2"> <?php echo $label2 ?> </label>
            <input type="text" name="nombre2" value="<?php echo $nombre2 ?>">
            <br><br>
            <input class="buttonegal" type="submit" name="resultFinal" value="=">
            <br><br>
            <label id="label3"> <?php echo $label3 ?> </label>
            <input type="text" name="nombreFinal" value="<?php echo $nombreFinal ?>">
            <br><br>
            <input class="buttonReset" type="submit" name="reset" value="<?php echo $boutonReset ?>">
        </fieldset>
        <br>
        <fieldset class="footer">
            <input class="buttonReturn" type="submit" name="return" value="<?php echo $valeurReturn ?>">
        </fieldset>
        <input class="typeLanguage" type="hidden" name="typeLangue" value="<?php echo $typeLangue ?>">
    </form>
    <script src="calcul.js"></script>
</body>

</html>
