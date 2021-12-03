<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Partie 1
    /*
    forward X increases the horizontal position by X units.
       down X increases the depth by X units.
         up X decreases the depth by X units.
    */
    // Partie 2
    /*
    down X increases your aim by X units.
    up X decreases your aim by X units.
    forward X does two things:
        It increases your horizontal position by X units.
        It increases your depth by your aim multiplied by X.
    */    
    $message = '';
    $compteur = 0;
    $liste_profondeur = array();
    $axe_x = 0;
    $axe_y = 0;
    $aim = 0;
    $deplacement_total = 0;
    if ($_FILES['nomFichier']['error'] > 0){
        switch ($_FILES['nomFichier']['error']){
            case 2: $message = 'Le fichier '.$_FILES['nomFichier']['name'].' est trop volumineux.';break;
            default : $message = 'Erreur : ' . $_FILES['nomFichier']['error'] . '<br />';
        }
    } else {
        // Seuls les fichiers textes ou csv seront considérés
        switch ($_FILES['nomFichier']['type']){
            case 'text/plain' : case 'text/csv' : case 'application/vnd.ms-excel' :
                $fichier = fopen($_FILES['nomFichier']['tmp_name'], 'r');
                if( !$fichier ){
                    $message = 'Impossible d\'ouvrir le fichier'.$_FILES['nomFichier']['tmp_name'];
                } else {
                    // Vérification du nombre et de l'ordre des colonnes
                    $ligne_colonne = fgetcsv($fichier, 1024, ';', '"');						
                    $validation = array('type_mouvement','nb_deplacement');

                    if(array_diff($ligne_colonne, $validation) == null) {
                        while (($ligne_suivante = fgetcsv($fichier, 1024, ";")) !== FALSE) {
                            $type_mouvement = $ligne_suivante[0];
                            $nb_position = intval($ligne_suivante[1]);
                            switch($type_mouvement){
                                case "forward" : $axe_x += $nb_position; $axe_y += $aim * $nb_position; break;
                                case "down" : $aim += $nb_position; break;
                                case "up" : $aim -= $nb_position; break;
                            }
                        }
                        $deplacement_total = $axe_x * $axe_y;
                        // La réponse à la partie 1 = 1484118
                        // La réponse à la partie 2 = 1463827010

                    } else {
                        $message = "Les colonnes du fichier ne suivent pas le format : type_mouvement et nb_deplacement<BR>" . $ligne_colonne;
                    }
                    fclose($fichier);
                }
                break;
            default : $message = "Le type de fichier n'est pas valide : {$_FILES['nomFichier']['type']}"; break;
        }
    }    
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>--- Day 2: Dive! ---</title>
        <link href="view.css" rel="stylesheet" type="text/css">
        <meta charset="utf-8" />
    </head>
    <body>
        <form class="appnitro" enctype="multipart/form-data" method="post" action="index.php">
            <div class="form_description">
                <h2>--- Day 2: Dive! ---</h2>
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
        <p><?php if ($_SERVER['REQUEST_METHOD'] == 'POST'){ echo $deplacement_total; } ?></p>
    </body>
</html>