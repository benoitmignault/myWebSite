<?php
// Partie 1
// Heure : 8:00 - Debut 
// Heure : 8:36 - Fin

// Partie 2
// Heure : 
// Heure : 

function initialisation(){
    $array_champs = array("liste_nombres" => array(), "mouvement" => array(), "nb_position_plus_2" => 0, "max_X" => 0, "max_Y" => 0, "longueur_liste" => 0);

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

function initialisation_carte($array_champs){    
    // Initialisation de la carte en fonction des max X et max Y 
    for ($i = 0; $i <= $array_champs['max_Y']; $i++){
        for ($j = 0; $j <= $array_champs['max_X']; $j++){
            $array_champs['carte'][$i][$j] = 0;        
        }
    }

    return $array_champs;
}

function aggrandissement_tableau($array_champs){
    // Une boucle de 80 jours
    for($day = 1; $day <= 80; $day++){
        // À chaque jour, on décrément les nombres
        // On créer un tableau contenant les nouveaux nombres à ajoute rà la fin de la journée
        $nouveau_chiffre = 0;
        for($i = 0; $i < $array_champs['longueur_liste']; $i++){
            
            if ($array_champs['liste_nombres'][$i] == 0){                
                $array_champs['liste_nombres'][$i] = 6;
                $nouveau_chiffre++;
                
            } else {
                $array_champs['liste_nombres'][$i]--;
            }
        }

        // On rajoute les nouveaux nombres dans le tableaux et on agrandit celui-ci
        for ($j = 0; $j < $nouveau_chiffre; $j++){
            array_push($array_champs['liste_nombres'], 8);
            $array_champs['longueur_liste']++;
        } 
    }   

    return $array_champs;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $array_champs = initialisation();
    if (isset($_POST['submit'])){
        $array_champs = lecture_fichier_CSV_TXT($array_champs);
        $array_champs = aggrandissement_tableau($array_champs);
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
                        echo $array_champs['longueur_liste']; 
                    }      
                 } ?>
        </p>
    </body>
</html>