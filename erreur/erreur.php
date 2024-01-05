<?php
    // Seul action possible est de revenir à la page accueil soit en français ou en anglais.
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['accueil-francais'])) {
            header("Location: /index.html");
            
        } elseif (isset($_POST['accueil-anglais'])) {
            header("Location: /english/english.html");
        }
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Erreur de page">
    <link rel="shortcut icon" href="erreur-icone.jpg">
    <link rel="stylesheet" type="text/css" href="erreur.css">
    <title>Erreur !</title>
</head>

<body>
    <div class="erreur-404">
        <div id="section-information">
            <button class="btn-erreur" id="btn-affichage" type="button">Explication</button>
        </div>
        <!-- Fichier 404-notfound.jpg est une propriété du site https://www.flickr.com/photos/guspim/2280690094/ sous licence libre -->
        <div class="photo-erreur">
            <img src="erreur-background.jpg" alt="" title="Erreur 404">
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
    <script src="erreur.js"></script>
</body>

</html>
