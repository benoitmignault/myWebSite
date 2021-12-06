<?php
// Partie 1
// Heure : 8:00 - Debut 
// Heure : 8:36 - Fin

// Partie 2
// Heure : 8:40 - Debut
// Heure : 14:15 - Fin

function initialisation(){
    $array_champs = array("liste_nombres" => array(), "groupe_precedant" => array(), "groupe_suivant" => array(), "nb_poissons_total" => 0);

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
                    while (($liste_chiffre = fgetcsv($fichier, 1024, ";")) !== FALSE) {
                        $un_chiffre = explode(',', $liste_chiffre[0]); // On récupère le mouvement
                        
                        foreach($un_chiffre as $value_point){                            
                            array_push($array_champs['liste_nombres'], intval($value_point));                                        
                        }
                        $array_champs['longueur_liste'] = count($array_champs['liste_nombres']); // On initialise la longueur du tableau des nombres                        
                    }              
                }
                fclose($fichier);
                break;
            default : $array_champs['message_sys'] = "Le type de fichier n'est pas valide : {$_FILES['nomFichier']['type']}"; break;
        }
    }

    return $array_champs;
}

function aggrandissement_tableau($array_champs){    
    // Une boucle de 80 jours
    $day = 1;
    $day_avec_groupe = 0;

    //for($day; $day <= 80; $day++){ // Partie 1    
    for($day = 1; $day < 256; $day++){
        // À chaque jour, on décrément les nombres
        // On créer un tableau contenant les nouveaux nombres à ajouter à la fin de la journée
        
        foreach($array_champs['liste_nombres'] as $key => $value){
            if ($value == 0){                
                $array_champs['liste_nombres'][$key] = 6;
                array_push($array_champs['liste_nombres'], 8);

            } else {
                $array_champs['liste_nombres'][$key] = $value - 1;
            }
        }

        // Vérification de snombres
        $groupe_day = array();
        foreach($array_champs['liste_nombres'] as $value){
            // Si la key existe, on incrémente sa présence
            if (isset($groupe_day[$value])){
                $groupe_day[$value]++;
            } else {
                $groupe_day[$value] = 1;
            }
        }

        // Si nous avons tous les nombres au moins une fois, on sort...
        if (count($groupe_day) == 9){
            $array_champs['groupe_precedant'] = $groupe_day;
            $day_avec_groupe = $day; // On récupère la jour
            $day = 256; // On doit sortir de la boucle FOR
        }        
    } 

    for($day_avec_groupe; $day_avec_groupe < 256; $day_avec_groupe++){
        // On prepare les information pour la nouvelle journée
        $array_champs['groupe_suivant'] = $array_champs['groupe_precedant'];
        $array_champs['groupe_suivant'][0] = $array_champs['groupe_precedant'][1];
        $array_champs['groupe_suivant'][1] = $array_champs['groupe_precedant'][2];
        $array_champs['groupe_suivant'][2] = $array_champs['groupe_precedant'][3];
        $array_champs['groupe_suivant'][3] = $array_champs['groupe_precedant'][4];
        $array_champs['groupe_suivant'][4] = $array_champs['groupe_precedant'][5];
        $array_champs['groupe_suivant'][5] = $array_champs['groupe_precedant'][6];
        $array_champs['groupe_suivant'][6] = $array_champs['groupe_precedant'][0] + $array_champs['groupe_precedant'][7];
        $array_champs['groupe_suivant'][7] = $array_champs['groupe_precedant'][8];
        $array_champs['groupe_suivant'][8] = $array_champs['groupe_precedant'][0];
        // Fin de la journée
        $array_champs['groupe_precedant'] = $array_champs['groupe_suivant'];        
    }


    return $array_champs;
}

function calcul_poissons_tous_type($array_champs){    
    foreach($array_champs['groupe_precedant'] as $value){        
        $array_champs['nb_poissons_total'] += $value;
    }

    return $array_champs;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $array_champs = initialisation();
    if (isset($_POST['submit'])){
        $array_champs = lecture_fichier_CSV_TXT($array_champs);
        $array_champs = aggrandissement_tableau($array_champs);
        $array_champs = calcul_poissons_tous_type($array_champs);
    }  
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>--- Day 6: Lanternfish ---</title>
        <link href="view.css" rel="stylesheet" type="text/css">
        <meta charset="utf-8" />
    </head>
    <body>
        <form class="appnitro" enctype="multipart/form-data" method="post" action="index.php">
            <div class="form_description">
                <h2>--- Day 6: Lanternfish ---</h2>
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
                        echo $array_champs['nb_poissons_total']; 
                    }      
                 } ?>
        </p>
        <pre id="output"></pre>
        <script src="day_6.js"></script>
    </body>
</html>