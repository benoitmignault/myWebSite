<?php
// Partie 1
// Heure : 18:00 - Début
// Heure : 20:30 - Fin

// Partie 2
// Heure : 19:00 - Début Jeudi
// Heure : 00:45 - Vendredi non fini

/*
Counting only digits in the output values (the part after | on each line)
*/

function initialisation(){
    $array_champs = array("liste_lignes" => array() ,"liste_pattern_signal" => array(), "liste_digit_output" => array(), "somme_digit" => 0);
    
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
                    while (($une_ligne = fgetcsv($fichier, 1024, ";")) !== FALSE) {
                        array_push($array_champs['liste_lignes'], $une_ligne);
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
    // Toutes les lignes de la partie droite
    foreach ($array_champs['liste_lignes'] as $une_ligne){
        $liste = explode(' | ', $une_ligne[0]); 
        // Assignation des lettres sur l'affiche *** À déterminer
        $pattern = array();
        // La combinaison des lettres qui donneront les chiffres
        $valeur_nombre = array(8 => "abcdefg");
        $array_champs['liste_pattern_signal'] = explode(' ', $liste[0]);
        array_multisort(array_map('strlen', $array_champs['liste_pattern_signal']), $array_champs['liste_pattern_signal']);
        $array_champs['liste_digit_output'] = explode(' ', $liste[1]);  
        

        // acedgfb cdfbe gcdfa fbcad dab cefabd cdfgeb eafb cagedb ab | cdfeb fcadb cdfeb cdbaf


        
        foreach($array_champs['liste_pattern_signal'] as $value_pattern){
            $length = strlen($value_pattern);            
            switch ($length){
                // Le chiffre 1
                case 2 : 
                    $pattern[3] = substr($value_pattern, 0, 1); 
                    $pattern[6] = substr($value_pattern, 1, 1);
                    $valeur_nombre[1] = remise_en_ordre_mot($value_pattern); break;
                // Le chiffre 7
                case 3 : 
                    for ($i = 0; $i < 3; $i++){
                        $lettre = substr($value_pattern, $i, 1);
                        // On peut insérer 
                        if ( $lettre != $pattern[3] AND $lettre != $pattern[6] ){
                            $pattern[1] = $lettre;
                            $valeur_nombre[7] = remise_en_ordre_mot($value_pattern);
                            break; // On sort du FOR
                        }
                    } break;                   
                // Le chiffre 4   
                case 4 :
                    $lettre_1_trouve = false; 
                    for ($i = 0; $i < 4; $i++){
                        $lettre = substr($value_pattern, $i, 1);
                        // On a trouvé une lettre qui n'est pas dans le 1
                        if ( $lettre != $pattern[3] AND $lettre != $pattern[6] ){
                            if (!$lettre_1_trouve){
                                $pattern[2] = $lettre;
                                $lettre_1_trouve = true;
                            } else {
                                $pattern[4] = $lettre;
                                $valeur_nombre[4] = remise_en_ordre_mot($value_pattern);
                                // Nous avons donc trouvé nos 2 autres lettres                                
                                break; // On sort du FOR
                            }
                        }
                    }
                    break;
                // Le chiffre 8  
                case 7 :
                    $lettre_1_trouve = false;
                    for ($i = 0; $i < 7; $i++){
                        $lettre = substr($value_pattern, $i, 1);
                        // On a trouvé une lettre qui n'est pas dans le 1
                        if ( $lettre != $pattern[1] AND $lettre != $pattern[2] AND $lettre != $pattern[3] AND $lettre != $pattern[4] AND $lettre != $pattern[6]){
                            if (!$lettre_1_trouve){
                                $pattern[7] = $lettre;
                                $lettre_1_trouve = true;
                            } else {
                                $pattern[5] = $lettre;
                                // Le mot est par défault les 7 lettres
                                // Nous avons donc trouvé nos 2 autres lettres                                
                                break; // On sort du FOR
                            }
                        }
                    } 
                    break;
            }

            if ( count($pattern) == 7 ){
                break; // Nous avons les 7 lettres
            }
        }
        // Création des des mots restants avec les lettres sur les chiffres correspondant
        // String à 5 de long
        $number_2 = "acdfg"; $valeur_nombre[2] = remise_en_ordre_mot($pattern[1].$pattern[3].$pattern[4].$pattern[5].$pattern[7]);
        $number_3 = "abcdf"; $valeur_nombre[3] = remise_en_ordre_mot($pattern[1].$pattern[4].$pattern[7].$pattern[3].$pattern[6]);
        $number_5 = "bcdef"; $valeur_nombre[5] = remise_en_ordre_mot($pattern[1].$pattern[2].$pattern[4].$pattern[6].$pattern[7]);
        // String à 6 de long
        $number_0 = "abcdeg"; $valeur_nombre[0] = remise_en_ordre_mot($pattern[1].$pattern[7].$pattern[2].$pattern[3].$pattern[5].$pattern[6]);
        $number_6 = "bcdefg"; $valeur_nombre[6] = remise_en_ordre_mot($pattern[1].$pattern[2].$pattern[5].$pattern[4].$pattern[6].$pattern[7]);
        $number_9 = "abcdef"; $valeur_nombre[9] = remise_en_ordre_mot($pattern[2].$pattern[1].$pattern[4].$pattern[7].$pattern[3].$pattern[6]);

        var_dump($valeur_nombre); exit;

        $creation_chiffre = "";
        foreach($array_champs['liste_digit_output'] as $value_digit){
            $length = strlen($value_digit); 

            switch ($length){
                case 2 : $creation_chiffre .= "1"; break;
                case 3 : $creation_chiffre .= "7"; break;
                case 4 : $creation_chiffre .= "4"; break;
                case 7 : $creation_chiffre .= "8"; break;
                // À partir d'ici, on va faire des comparaisons et trouver le bon
                case 5 :
                    // On remet dans l'ordre les lettres pour start                    
                    $value_digit = remise_en_ordre_mot($value_digit);

                    if ($value_digit == $valeur_nombre[2]){
                        $creation_chiffre .= "2";

                    } elseif ($value_digit == $valeur_nombre[3]){
                        $creation_chiffre .= "3";

                    } elseif ($value_digit == $valeur_nombre[5]){
                        $creation_chiffre .= "5";
                    }
                    break;
                case 6 :
                    // On remet dans l'ordre les lettres pour start 
                    $value_digit = remise_en_ordre_mot($value_digit);

                    if ($value_digit == $valeur_nombre[0]){
                        $creation_chiffre .= "0";

                    } elseif ($value_digit == $valeur_nombre[6]){
                        $creation_chiffre .= "6";

                    } elseif ($value_digit == $valeur_nombre[9]){
                        $creation_chiffre .= "9";
                    }
                    break;    
            }
        }

        $array_champs['somme_digit'] += intval($creation_chiffre);        
    }
    
    return $array_champs;
}

function remise_en_ordre_mot($mot){
    $mot = str_split($mot);
    sort($mot);
    $mot = implode($mot);

    return $mot;
}
/// Les valeurs de 
// String à 2 de long
$number_1 = "ab";
// String à 3 de long
$number_7 = "abd";
// String à 4 de long
$number_4 = "abef";
// String à 5 de long
$number_2 = "acdfg"; 
$number_3 = "abcdf";
$number_5 = "bcdef"; 
// String à 6 de long
$number_0 = "abcdeg";
$number_6 = "bcdefg";
$number_9 = "abcdef";
// String à 7 de long
$number_8 = "abcdefg";

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