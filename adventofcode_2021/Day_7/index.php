<?php
// Partie 1
// Heure : 21:30 - Debut  
// Heure : 23:55 - Fin

// Partie 2 - Mercredi
// Heure : 00:00 - Debut
// Heure : 00:40 - Fin

function initialisation(){
    $array_champs = array("nb_nombres" => 0, "liste_nombres" => array(), "cout_petit" => INF);

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
                    // Je viens de trouver mon erreur, il avait plus d'une ligne dans le fichier, caliss :)
                    // Si une ligne est tropppppppppppp longue, je dois faire plusieurs lignes
                    while (($liste_chiffre = fgetcsv($fichier, 1024, ";")) !== FALSE) {
                        $un_chiffre = explode(',', $liste_chiffre[0]); // On récupère le mouvement
                        foreach($un_chiffre as $value_point){                   
                            array_push($array_champs['liste_nombres'], intval($value_point));   
                        } 
                    } 
                    $array_champs['nb_nombres'] = count($array_champs['liste_nombres']); // On initialise la longueur du tableau des nombres          
                }
                fclose($fichier);
                break;
            default : $array_champs['message_sys'] = "Le type de fichier n'est pas valide : {$_FILES['nomFichier']['type']}"; break;
        }
    }
    return $array_champs;
}

function calcul_deplacement($array_champs){    
    $i =  min($array_champs['liste_nombres']);
    $max = max($array_champs['liste_nombres']) + 1;
    // Chaque nombre entre le min et le max + 1 sera testé comme point de ressemblement
    for ($i; $i < $max; $i++){
        $cout_total = 0;
        // Pour chaque nombre du sur les l'axe des Y, allons vers le nombre Y désigné comme milieu
        for ($j = 0; $j < $array_champs['nb_nombres']; $j++){          
            $cout_total += ( abs($i - $array_champs['liste_nombres'][$j]) / 2 ) * ( abs($i - $array_champs['liste_nombres'][$j]) +1 );
        }
        $array_champs['cout_petit'] = min($array_champs['cout_petit'], $cout_total);
    }

    return $array_champs;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $array_champs = initialisation();
    if (isset($_POST['submit'])){
        $array_champs = lecture_fichier_CSV_TXT($array_champs);
        $array_champs = calcul_deplacement($array_champs);
    }  
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>--- Day 7: The Treachery of Whales ---</title>
    <link href="view.css" rel="stylesheet" type="text/css">
    <meta charset="utf-8" />
</head>

<body>
    <form class="appnitro" enctype="multipart/form-data" method="post" action="index.php">
        <div class="form_description">
            <h2>--- Day 7: The Treachery of Whales ---</h2>
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
                        echo $array_champs['cout_petit']; 
                    }      
                 } ?>
    </p>
</body>

</html>