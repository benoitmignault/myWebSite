<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['returnFR'])) {
        header("Location: /index.html");
    } elseif (isset($_POST['returnEN'])) {
        header("Location: /english/english.html");
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>             
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Erreur de page">
        <link rel="shortcut icon" href="icone404.jpg">
        <link rel="stylesheet" type="text/css" href="erreur.css">
        <title>Erreur !</title>  
    </head>
    <body> 
        <div class="erreur404">
            <div id="explication">                
                <button class="btnErreur" id="francais" type="button">Explication en français</button>
                <button class="btnErreur" id="english" type="button">Explanation in english</button>
            </div>        
            <!-- Fichier 404-notfound.jpg est une propriété du site http://maxpixel.freegreatpicture.com/File-Not-Found-Not-Found-404-Error-2384304 sous licence libre -->
            <div class="photoErreur">
                <img src="./404-notfound.jpg" alt="Erreur 404" title="Erreur 404">
            </div>            
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>        
        <script type="text/javascript" src="erreur.js"></script>
    </body>
</html>