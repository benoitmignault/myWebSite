<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $message = '';
    $compteur = 0;
    $liste_profondeur = array();
    if ($_FILES['nomFichier']['error'] > 0){
        switch ($_FILES['nomFichier']['error']){
            case 2: $message = 'Le fichier '.$_FILES['nomFichier']['name'].' est trop volumineux.';break;
            default : $message = 'Erreur : ' . $_FILES['nomFichier']['error'] . '<br />';
        }
    } else {
        // Seuls les fichiers textes ou csv seront considérés
        // var_dump($_FILES['nomFichier']['type']); exit;
        switch ($_FILES['nomFichier']['type']){
            case 'text/plain' : case 'text/csv' : case 'application/vnd.ms-excel' :
                $fichier = fopen($_FILES['nomFichier']['tmp_name'], 'r');

                if( !$fichier ){
                    $message = 'Impossible d\'ouvrir le fichier'.$_FILES['nomFichier']['tmp_name'];
                } else {
                    // Vérification du nombre et de l'ordre des colonnes
                    $ligne_colonne = fgetcsv($fichier, 1024, ';', '"');						
                    $validation = array('profondeur');

                    if(array_diff($ligne_colonne, $validation) == null) {                        
                        while (($ligne_suivante = fgetcsv($fichier, 1024, ";")) !== FALSE) {
                            array_push($liste_profondeur, intval($ligne_suivante[0]) );
                        }
                    } else {
                        $message = "Les colonnes du fichier ne suivent pas le format : profondeur<BR>".$ligne_colonne;
                    }
                    fclose($fichier);
                }
                break;
            default : $message = 'Le type de fichier n\'est pas valide : '.$_FILES['nomFichier']['type']; break;
        }
    }
    // Création du tableau résultat de chaques 3 sommes des valeurs du tableau
    $groupe_trois = array();
    
    // $i = 0 - 1999 ; $length = 1 - 2000 , la dernière boucle doit se faire à 1998
    $length = count($liste_profondeur);
    for ($i = 0; $i < $length - 2; $i++){
        $x = $i;
        $valeur1 = intval($liste_profondeur[$x]);
        $valeur2 = intval($liste_profondeur[$x+1]);
        $valeur3 = intval($liste_profondeur[$x+2]);
        $somme = $valeur1 + $valeur2 + $valeur3;
        array_push($groupe_trois, $somme);
    }

    $somme_precedente = $groupe_trois[0]; // Initialise la première somme
    $length = count($groupe_trois);
    for ($i = 1; $i < $length; $i++){

        if ($groupe_trois[$i] > $somme_precedente){
            $compteur++;
        }
        $somme_precedente = $groupe_trois[$i];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>--- Day 1: Sonar Sweep ---</title>
        <link href="view.css" rel="stylesheet" type="text/css">
        <meta charset="utf-8" />
    </head>
    <body>
        <form class="appnitro" enctype="multipart/form-data" method="post" action="index.php">
            <div class="form_description">
                <h2>--- Day 1: Sonar Sweep ---</h2>
            </div>
            <ul>
                <li name="importation" >
                    <br>
                    <label class="description" for="element_1">Importer un fichier CSV</label>
                    <div name="conteneur_fichier">
                        <input id="element_1" name="nomFichier" class="element file medium" type="file" size="5"/>
                        <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                    </div>
                </li>
                <li class="buttons">                    
                    <input id="saveForm" class="button_text" type="submit" name="submit" value="Importer" /> 
                </li>
            </ul>
        </form>
        <p><?php if ($_SERVER['REQUEST_METHOD'] == 'POST'){ echo $compteur; } ?></p>
    </body>
</html>