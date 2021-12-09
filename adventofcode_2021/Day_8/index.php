<?php
// Partie 1
// Heure : 18:00 - Début
// Heure : 20:30 - Fin

// Partie 2
// Heure : 20:30 - Début
// Heure : 

/*
Counting only digits in the output values (the part after | on each line)
*/

function initialisation(){
    $array_champs = array("liste_numero_digit" => array(), "liste_pattern_signal" => array(), "liste_digit_output" => array(), "somme_digit" => 0);
    // Création des digits avec les lettres - surement pour la 2e partie                                                  
    $array_champs['liste_numero_digit'][0] = "abcefg";
    $array_champs['liste_numero_digit'][1] = "cf";
    $array_champs['liste_numero_digit'][2] = "acdeg";
    $array_champs['liste_numero_digit'][3] = "acdfg";
    $array_champs['liste_numero_digit'][4] = "bcdf";
    $array_champs['liste_numero_digit'][5] = "abdefg";
    $array_champs['liste_numero_digit'][6] = "abdefg";
    $array_champs['liste_numero_digit'][7] = "acf";
    $array_champs['liste_numero_digit'][8] = "abcdefg";
    $array_champs['liste_numero_digit'][9] = "abcdfg";
    // acedgfb cdfbe gcdfa fbcad dab cefabd cdfgeb eafb cagedb ab | cdfeb fcadb cdfeb cdbaf
    // 

    return $array_champs;
}

function lecture_fichier_CSV_TXT($array_champs){
    if ($_FILES['nomFichier']['error'] > 0){
        switch ($_FILES['nomFichier']['error']){
            case 2: $array_champs['message_sys'] = "Le fichier " . $_FILES['nomFichier']['name'] . " est trop volumineux !"; break;
            default : $array_champs['message_sys'] = "Erreur : " . $_FILES['nomFichier']['error'] . " !";
        }
    } else {
        // Seuls les fichiers textes ou csv seront considérés
        switch ($_FILES['nomFichier']['type']){
            // Seulement pour les fichier CSV
            case 'text/plain' : case 'text/csv' : case 'application/vnd.ms-excel' :
                $fichier = fopen($_FILES['nomFichier']['tmp_name'], 'r');
                if (!$fichier ){
                    $array_champs['message_sys'] = "Impossible d'ouvrir le fichier " . $_FILES['nomFichier']['tmp_name'];
                } else {  
                    $i = 0;       
                    while (($une_ligne = fgetcsv($fichier, 1024, ";")) !== FALSE) {
                        $liste = explode(' | ', $une_ligne[0]); 
                        
                        $liste_pattern_signal = explode(' ', $liste[0]);
                        $liste_digit_output = explode(' ', $liste[1]);
                        $array_champs['liste_pattern_signal'][$i] = $liste_pattern_signal;        
                        $array_champs['liste_digit_output'][$i] = $liste_digit_output;
                        
                        $i++;
                    }
                }
                fclose($fichier);
                break;
            default : $array_champs['message_sys'] = "Le type de fichier n'est pas valide : {$_FILES['nomFichier']['type']}"; break;
        }
    }

    return $array_champs;
}

function recuperation_pattern_digit($array_champs){    
    foreach ($array_champs['liste_digit_output'] as $une_serie_digit){

        foreach ($une_serie_digit as $key => $value){
            //var_dump($value); var_dump($une_serie_digit);exit;
            $length = strlen($value);
            switch($length){
                case 2 : case 3 : case 4 : case 7 : $array_champs['nb_digit_unique']++; break;
            }
        }

    }
    return $array_champs;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $array_champs = initialisation();
    if (isset($_POST['submit'])){
        $array_champs = lecture_fichier_CSV_TXT($array_champs);
        $array_champs = recuperation_pattern_digit($array_champs);
    }  
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>--- Day 8: Seven Segment Search ---</title>
    <link href="view.css" rel="stylesheet" type="text/css">
    <meta charset="utf-8" />
</head>

<body>
    <form class="appnitro" enctype="multipart/form-data" method="post" action="index.php">
        <div class="form_description">
            <h2>--- Day 8: Seven Segment Search ---</h2>
        </div>
        <ul>
            <li name="importation">
                <br>
                <label class="description" for="element_1">Importer un fichier CSV</label>
                <div name="conteneur_fichier">
                    <input id="element_1" name="nomFichier" class="element file medium" type="file" size="5" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                </div>
            </li>
            <li class="buttons">
                <input id="saveForm" class="button_text" type="submit" name="submit" value="Importer" />
            </li>
        </ul>
    </form>
    <p>
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                    if (!empty($array_champs['message_sys'])){
                        echo $array_champs['message_sys']; 
                    } else {
                        echo $array_champs['somme_digit']; 
                    }      
                } ?>
    </p>
</body>

</html>