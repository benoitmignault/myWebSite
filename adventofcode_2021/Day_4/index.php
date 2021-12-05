<?php
function initialisation_des_variables(){
    $array_champs = array("liste_nombres" => array(), "liste_cartes" => array(), "message_sys" => "", "resultat_bingo" => 0, "no_carte_gagnante" => 0, "dernier_numero_tire" => 0);

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
                    $premiere_ligne = fgetcsv($fichier, 1024, ";");
                    $liste_numeros = explode(',', $premiere_ligne[0]);
                    
                    foreach($liste_numeros as $un_chiffre){
                        // Si nous avons un espace , on l'ignore                        
                        $valeur_num = intval($un_chiffre);
                        array_push($array_champs['liste_nombres'], $valeur_num);                        
                    }

                    $nb_carte = 0;
                    $nb_ligne_carte = 0;
                    while (($ligne_next = fgetcsv($fichier, 1024, ";")) !== FALSE) {
                        // la carte a été rempli, on passe à la prochaine carte
                        if ($ligne_next[0] === NULL){ 
                            // Pour éviter de partir à 1
                            if (count($array_champs['liste_cartes']) > 0){                                
                                // Incrément pour la prochaine carte
                                $nb_carte++;
                                // Remise à 0 pour la ligne pour la prochaine carte
                                $nb_ligne_carte = 0;
                            }                             
                            // Nous allons ajouter une 6e lignes à la position 5 à carte pour les lignes verticales
                            $array_champs['liste_cartes'][$nb_carte][5] = array(0, 0, 0, 0, 0);
                            // Nous allons ajouter une 7e ligne pour savoir si la carte a fait un BINGO
                            $array_champs['liste_cartes'][$nb_carte][6] = "AUCUN_BINGO";                                                       
                            
                        } else {
                            // On construit notre carte en cours
                            $ligne_carte = array();
                            $serie_chiffre = explode(' ', $ligne_next[0]); // array qui donne des string                                                        
                            foreach($serie_chiffre as $un_chiffre){
                                // Si nous avons un espace , on l'ignore
                                if ($un_chiffre != ""){
                                    $valeur_num = intval($un_chiffre);
                                    array_push($ligne_carte, $valeur_num);
                                }
                            }                         
                            // Nous allons ajouter une position 5 à chacune des 5 lignes de la carte pour les lignes horizontales
                            array_push($ligne_carte, 0);
                            // On associ notre nouvelle ligne à la ligne correspondante sur la carte
                            $array_champs['liste_cartes'][$nb_carte][$nb_ligne_carte] = $ligne_carte;
                            $nb_ligne_carte++;
                        }                   
                    }
                }

                fclose($fichier);
                break;
            default : $array_champs['message_sys'] = "Le type de fichier n'est pas valide : {$_FILES['nomFichier']['type']}"; break;
        }
    }

    return $array_champs;
}

function verification_nombres_tires($array_champs){
    // On passe à travers de chaque nombre du bingo
    foreach($array_champs['liste_nombres'] as $un_nombre){

        foreach($array_champs['liste_cartes'] as $key_carte => $une_carte ){
            // On regarde la carte si, elle n'a pas de bingo biensur
            if ($array_champs['liste_cartes'][$key_carte][6] == "AUCUN_BINGO"){
                $nb_lignes = count($une_carte) - 2; // À la Xièm carte, nous avons combien de lignes
                // On passe à travers de chaque ligne de la carte
                for ($i = 0; $i < $nb_lignes; $i++){
                    $nb_nombres = count($une_carte[$i]) - 1; // À la Xième ligne de la Xième carte, nous avons combien de nombres

                    // On passe à travers des nombres de la ligne
                    for ($j = 0; $j < $nb_nombres; $j++){
                        if ($un_nombre === $une_carte[$i][$j]){
                            // On doit incrémenté le nb_chiffre_trouve pour la colonne et la ligne
                            $array_champs['liste_cartes'][$key_carte][$i][5]++; // Ligne
                            $array_champs['liste_cartes'][$key_carte][5][$j]++; // Colonne
                            // Comme je devrais calculter la sommes des chiffres non choisis, je vais détruire les chiffres
                            $array_champs['liste_cartes'][$key_carte][$i][$j] = "X";
                            // BINGO !!!        
                            if ($array_champs['liste_cartes'][$key_carte][$i][5] == 5 OR $array_champs['liste_cartes'][$key_carte][5][$j] == 5){ 
                                $array_champs['liste_cartes'][$key_carte][6] = "BINGO";                           
                                $array_champs['no_carte_gagnante'] = $key_carte;
                                $array_champs['dernier_numero_tire'] = $un_nombre;
                                break; // On n'a plus besoin de passer à travers du reste de la ligne, on peut passer à la suivante
                            }
                        }
                    } // On a fini de passer au travers des nombres d'une ligne X

                    if ($array_champs['liste_cartes'][$key_carte][6] == "BINGO"){
                        break; // On n'a plus besoin de passer à travers du reste des lignes, on peut passer à la suivante
                    }
                } // On a fini de passer au travers des lignes de la carte
            }       
        }
    }
    return $array_champs;
}

function calcul_resultat_bingo($array_champs){
    $nb_lignes = count($array_champs['liste_cartes'][$array_champs['no_carte_gagnante']]) - 2; // À la Xièm carte, nous avons combien de lignes
    // On passe à travers de chaque ligne de la carte
    for ($i = 0; $i < $nb_lignes; $i++){
        $nb_nombres = count($array_champs['liste_cartes'][ $array_champs['no_carte_gagnante'] ][$i]) - 1; // À la Xième ligne de la Xième carte, nous avons combien de nombres

        // On passe à travers des nombres de la ligne
        for ($j = 0; $j < $nb_nombres; $j++){
            if ($array_champs['liste_cartes'][ $array_champs['no_carte_gagnante'] ][$i][$j] != "X"){
                $array_champs['resultat_bingo']+= $array_champs['liste_cartes'][ $array_champs['no_carte_gagnante'] ][$i][$j];
            }
        }
    }

    $array_champs['resultat_bingo'] *= $array_champs['dernier_numero_tire'];

    return $array_champs;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $array_champs = initialisation_des_variables();
    if (isset($_POST['submit'])){
        $array_champs = lecture_fichier_CSV_TXT($array_champs);
        $array_champs = verification_nombres_tires($array_champs);
        $array_champs = calcul_resultat_bingo($array_champs);
    }  
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>--- Day 4: Giant Squid ---</title>
        <link href="view.css" rel="stylesheet" type="text/css">
        <meta charset="utf-8" />
    </head>
    <body>
        <form class="appnitro" enctype="multipart/form-data" method="post" action="index.php">
            <div class="form_description">
                <h2>--- Day 4: Giant Squid ---</h2>
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
                        echo $array_champs['resultat_bingo']; 
                    }      
                 } ?>
        </p>
    </body>
</html>