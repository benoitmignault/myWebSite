<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    

    $message = '';
    $compteur = 0;
    $liste_nombre = array();
    $axe_x = 0;
    $axe_y = 0;
    $aim = 0;
    $deplacement_total = 0;
    $gamma_lettre = ""; // les 5 premier bit
    $epsilon_lettre = ""; // les 7 bit suivant
    $gamma_decimal = 0; // les 5 premier bit
    $epsilon_decimal = 0; // les 7 bit suivant
    $power_consumption = 0;
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
                    $validation = array('nombre');

                    if(array_diff($ligne_colonne, $validation) == null) {
                        while (($ligne_suivante = fgetcsv($fichier, 1024, ";")) !== FALSE) {
                            $binaire_string = $ligne_suivante[0];
                            $nombre_decortique = str_split($binaire_string);
                            array_push($liste_nombre, $nombre_decortique);                                                        
                        }                        
                        $pos_1_0 = 0;                        $pos_1_1 = 0;
                        $pos_2_0 = 0;                        $pos_2_1 = 0;
                        $pos_3_0 = 0;                        $pos_3_1 = 0;
                        $pos_4_0 = 0;                        $pos_4_1 = 0;
                        $pos_5_0 = 0;                        $pos_5_1 = 0;
                        $pos_6_0 = 0;                        $pos_6_1 = 0;
                        $pos_7_0 = 0;                        $pos_7_1 = 0;
                        $pos_8_0 = 0;                        $pos_8_1 = 0;
                        $pos_9_0 = 0;                        $pos_9_1 = 0;
                        $pos_10_0 = 0;                        $pos_10_1 = 0;
                        $pos_11_0 = 0;                        $pos_11_1 = 0;
                        $pos_12_0 = 0;                        $pos_12_1 = 0;

                        $length = count($liste_nombre);
                        for($i = 0; $i < $length; $i++){
                            $nombre = $liste_nombre[$i];
                            foreach($nombre as $key => $value){
                                switch($key){
                                    case 0 :                                        
                                        if ($value == "0"){
                                            $pos_1_0++;
                                        } else {
                                            $pos_1_1++;
                                        }
                                        break;
                                    case 1 :                                        
                                        if ($value == "0"){
                                            $pos_2_0++;
                                        } else {
                                            $pos_2_1++;
                                        }
                                        break;
                                    case 2 :                                        
                                        if ($value == "0"){
                                            $pos_3_0++;
                                        } else {
                                            $pos_3_1++;
                                        }
                                        break;
                                    case 3 :                                        
                                        if ($value == "0"){
                                            $pos_4_0++;
                                        } else {
                                            $pos_4_1++;
                                        }
                                        break;
                                    case 4 :                                        
                                        if ($value == "0"){
                                            $pos_5_0++;
                                        } else {
                                            $pos_5_1++;
                                        }
                                        break;
                                    case 5 :                                        
                                        if ($value == "0"){
                                            $pos_6_0++;
                                        } else {
                                            $pos_6_1++;
                                        }
                                        break;
                                    case 6 :                                        
                                        if ($value == "0"){
                                            $pos_7_0++;
                                        } else {
                                            $pos_7_1++;
                                        }
                                        break;
                                    case 7 :                                        
                                        if ($value == "0"){
                                            $pos_8_0++;
                                        } else {
                                            $pos_8_1++;
                                        }
                                        break;
                                    case 8 :                                        
                                        if ($value == "0"){
                                            $pos_9_0++;
                                        } else {
                                            $pos_9_1++;
                                        }
                                        break;
                                    case 9 :                                        
                                        if ($value == "0"){
                                            $pos_10_0++;
                                        } else {
                                            $pos_10_1++;
                                        }
                                        break;
                                    case 10 :                                        
                                        if ($value == "0"){
                                            $pos_11_0++;
                                        } else {
                                            $pos_11_1++;
                                        }
                                        break;
                                    case 11 :                                        
                                        if ($value == "0"){
                                            $pos_12_0++;
                                        } else {
                                            $pos_12_1++;
                                        }
                                        break;
                                }
                            }

                        }

                        if ($pos_1_0 > $pos_1_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_2_0 > $pos_2_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_3_0 > $pos_3_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_4_0 > $pos_4_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_5_0 > $pos_5_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_6_0 > $pos_6_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_7_0 > $pos_7_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_8_0 > $pos_8_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_9_0 > $pos_9_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_10_0 > $pos_10_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_11_0 > $pos_11_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        if ($pos_12_0 > $pos_12_1){
                            $gamma_lettre .= "0";
                            $epsilon_lettre .= "1";
                        } else {
                            $gamma_lettre .= "1";
                            $epsilon_lettre .= "0";
                        }

                        $gamma_decimal = bindec($gamma_lettre);
                        $epsilon_decimal = bindec($epsilon_lettre);

                        $power_consumption = $gamma_decimal * $epsilon_decimal;

                        // 2250414 - Réponse de la partie 1

                    } else {
                        $message = "Les colonnes du fichier ne suivent pas le format : nombre<BR>" . $ligne_colonne;
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
        <title>--- Day 3: Binary Diagnostic ---</title>
        <link href="view.css" rel="stylesheet" type="text/css">
        <meta charset="utf-8" />
    </head>
    <body>
        <form class="appnitro" enctype="multipart/form-data" method="post" action="index.php">
            <div class="form_description">
                <h2>--- Day 3: Binary Diagnostic ---</h2>
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
        <p><?php if ($_SERVER['REQUEST_METHOD'] == 'POST'){ echo $power_consumption; } ?></p>
    </body>
</html>