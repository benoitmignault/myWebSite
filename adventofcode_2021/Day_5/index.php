<?php
// Partie 1
// Heure : 9:30 - Debut 
// Heure : 12:30 - Fin

// Partie 2
// Heure : 14:00 - Debut 
// Heure : 15:10 - Fin

function initialisation(){
    $array_champs = array("carte" => array(), "mouvement" => array(), "nb_position_plus_2" => 0, "max_X" => 0, "max_Y" => 0);

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
                    $nb_mouvement = 0;
                    while (($un_mouvement = fgetcsv($fichier, 1024, ";")) !== FALSE) {
                        $liste_points = explode(' -> ', $un_mouvement[0]); // On récupère un mouvement
                        foreach($liste_points as $key_point => $value_point){                            
                            $un_point = explode(',', $value_point); // On récupère le mouvement

                            // On doit déterminer les max X et Y pour aller initialiser la carte pour aller incrémenter les passages dans la carte, ulterieurement
                            if ($un_point[0] > $array_champs['max_X']){
                                $array_champs['max_X'] = $un_point[0]; 
                            } elseif ($un_point[1] > $array_champs['max_Y']){
                                $array_champs['max_Y'] = $un_point[1]; 
                            }

                            foreach($un_point as $key_demi_point => $value_un_demi_point){
                                $array_champs['mouvement'][$nb_mouvement][$key_point][$key_demi_point] = intval($value_un_demi_point);                                                        
                            }                            
                        }
                        
                        $nb_mouvement++; // On incrémente le nombre de mouvement pour le prochain mouvement
                    }              
                }
                fclose($fichier);
                break;
            default : $array_champs['message_sys'] = "Le type de fichier n'est pas valide : {$_FILES['nomFichier']['type']}"; break;
        }
    }

    return $array_champs;
}

function initialisation_carte($array_champs){    
    // Initialisation de la carte en fonction des max X et max Y 
    for ($i = 0; $i <= $array_champs['max_Y']; $i++){
        for ($j = 0; $j <= $array_champs['max_X']; $j++){
            $array_champs['carte'][$i][$j] = 0;        
        }
    }

    return $array_champs;
}

function deplacement_sur_carte($array_champs){
    // On passe à travers de chaque mouvement
    foreach($array_champs['mouvement'] as $un_mouvement){        
        // Nous savons que nous avons simplement 2 points, on va y aller de manière static
        $ligne_debut  = $un_mouvement[0][0];  // x1
        $ligne_fin  = $un_mouvement[1][0];    // x2
        $colonne_debut = $un_mouvement[0][1]; // y1
        $colonne_fin = $un_mouvement[1][1];   // y2
        // Pour la partie 1, on considère juste les mouvements verticaux or horizontaux
        if ($colonne_debut == $colonne_fin OR $ligne_debut == $ligne_fin){            
            $ligne = 0; // déplacement vertical par rapport à X
            $colonne = 0; // déplacement horizontale par rapport à Y
            // On doit inverse le déplacement des points pour aller du plus petit vers le plus grand point
            if ($colonne_debut == $colonne_fin){
                $colonne = $colonne_debut;

                if ($ligne_fin < $ligne_debut){
                    $temp = $ligne_fin;
                    $ligne_fin = $ligne_debut;
                    $ligne_debut = $temp;
                }

            } elseif ($ligne_debut == $ligne_fin){
                $ligne = $ligne_debut;

                if ($colonne_fin < $colonne_debut){
                    $temp = $colonne_fin;
                    $colonne_fin = $colonne_debut;
                    $colonne_debut = $temp;
                }
            }

            // Si les Y sont égale, nous allons avoir un déplacement horizontal
            if ($colonne_debut == $colonne_fin){
                // On va incrémenter le nombre de fois qu'on passe sur le point 
                for ($ligne_debut; $ligne_debut <= $ligne_fin; $ligne_debut++){
                    $array_champs['carte'][$colonne][$ligne_debut]++;
                }

            // Si les Y sont égale, nous allons avoir un dépplacement vertical    
            } else if ($ligne_debut == $ligne_fin){
                // On va incrémenter le nombre de fois qu'on passe sur le point 
                for ($colonne_debut; $colonne_debut <= $colonne_fin; $colonne_debut++){   
                    $array_champs['carte'][$colonne_debut][$ligne]++;                    
                }
            }
        } else {
            // Nous avons alors un mouvement diagonale
            // On doit déterminer si les X montent ou dessent
            $pente_x = "";
            if ($ligne_debut < $ligne_fin){  
                $pente_x = "MONTENTE";

            } elseif ($ligne_debut > $ligne_fin){
                $pente_x = "DECENDENTE";
            } 

            if ($colonne_debut < $colonne_fin){
                for ($colonne_debut; $colonne_debut <= $colonne_fin; $colonne_debut++){                     
                    $array_champs['carte'][$colonne_debut][$ligne_debut]++;                    
                    $ligne_debut = determiner_pente_x($ligne_debut, $pente_x);       
                }

            } elseif ($colonne_debut > $colonne_fin){
                for ($colonne_debut; $colonne_debut >= $colonne_fin; $colonne_debut--){ 
                    $array_champs['carte'][$colonne_debut][$ligne_debut]++;
                    $ligne_debut = determiner_pente_x($ligne_debut, $pente_x);
                }
            }            
        }        
    } 

    return $array_champs;
}

// Fonction commune pour détemriner le nouveau X pour la prochaine variable X
function determiner_pente_x($x1, $pente_x){
    if ($pente_x == "MONTENTE"){
        $x1++;
    } else {
        $x1--;
    }

    return $x1;
}

function calcul_deplacement_plus_deux($array_champs){
    for ($i = 0; $i <= $array_champs['max_Y']; $i++){
        for ($j = 0; $j <= $array_champs['max_X']; $j++){
            // Si la position X,Y a 2 et plus, nous devons incrémenter notre compteur
            if ($array_champs['carte'][$i][$j] > 1){                
                $array_champs['nb_position_plus_2']++;
            }
        }
    }

    return $array_champs;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $array_champs = initialisation();
    if (isset($_POST['submit'])){
        $array_champs = lecture_fichier_CSV_TXT($array_champs);
        $array_champs = initialisation_carte($array_champs);
        $array_champs = deplacement_sur_carte($array_champs);
        $array_champs = calcul_deplacement_plus_deux($array_champs);
    }  
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>--- Day 5: Hydrothermal Venture ---</title>
        <link href="view.css" rel="stylesheet" type="text/css">
        <meta charset="utf-8" />
    </head>
    <body>
        <form class="appnitro" enctype="multipart/form-data" method="post" action="index.php">
            <div class="form_description">
                <h2>--- Day 5: Hydrothermal Venture ---</h2>
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
        <p>
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                    if (!empty($array_champs['message_sys'])){
                        echo $array_champs['message_sys']; 
                    } else {
                        echo $array_champs['nb_position_plus_2']; 
                    }      
                 } ?>
        </p>
    </body>
</html>