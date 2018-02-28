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
    if ($typeLangue === 'francais') {
        $title = "Calculatrice";
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
    $typeLangue = $_GET['langue'];
    if ($typeLangue !== "francais" && $typeLangue !== "english") {
        header("Location: /erreur/erreur.php");
        exit;
    } else {
        traduction($typeLangue);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $afficher = $_POST['visible_Info']; // l'information à savoir si les instructions sont affichées ou pas     
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
}
?>
<!-- Le début de l'écriture de la page html de la calculatrice -->
<!DOCTYPE html>
<html>
    <head>
        <title> <?php echo $title ?> </title>
        <link rel="stylesheet" href="calculJavaScript.css"/>        
        <!-- Le fichier calcul.png est la propriété du site https://pixabay.com/fr/calculatrice-les-math%C3%A9matiques-t%C3%A2che-1019743/ mais en utilisation libre-->
        <link rel="shortcut icon" href="calculJavaScript.png">	
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body{
                margin:0;                
                /*Le fichier pageCalculatrice.jpg est la propriété du site https://pixabay.com/fr/calculatrice-compte-calculette-2359760/ mais en utilisation libre-->*/
                background-image: url("calculJavaScript.jpg");                
                background-position: center;
                background-attachment:fixed;
                background-size:auto;
                background-repeat: no-repeat;                
            }
        </style>
    </head>
    <body>
        <h1 class="H1titre"> <?php echo $h1 ?> </h1>
        <fieldset class="procedure">            
            <legend align="center" id="legend1">
                <a class="faireAfficher" href=""> <?php echo $legend1 ?> </a>
            </legend>
            <ol class="lesInstruction"> <?php echo $procedure ?> </ol> 
        </fieldset>            
        <p id="info"> <?php echo $information ?> </p>
        <form method="post" action="./calculJavaScript.php">            
            <fieldset class="calcul">
                <input id="info_Instruction" type="hidden" name="visible_Info" value="<?php echo $afficher ?>">
                <legend align="center" id="legend2"> <?php echo $legend2 ?></legend>                 
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>    
        <script type="text/javascript" src="calcul.js"></script>
    </body>
</html>