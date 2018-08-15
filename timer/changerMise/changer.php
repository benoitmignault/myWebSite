<?php 
header("Content-type: application/json; charset=utf-8");

function connexionBD() {
    $host = "benoitmignault.ca.mysql";
    $user = "benoitmignault_ca_mywebsite";
    $password = "d-&47mK!9hjGC4L-";
    $bd = "benoitmignault_ca_mywebsite";
    /*
    $host = "localhost";
    $user = "zmignaub";
    $password = "Banane11";
    $bd = "benoitmignault_ca_mywebsite";
    */
    $connMYSQL = mysqli_connect($host, $user, $password, $bd);

    $connMYSQL->query("set names 'utf8'"); // ceci permet d'avoir des accents affiché sur la page web !
    return $connMYSQL;
}

//Function to check if the request is an AJAX request
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function initialisation_Champs() {
    $champs = ["user" => "", "combinaison" => 0, "valeurSmall" => "", "valeurBig" => "", 
               "aucune_valeur" => false, "trop_valeur" => false, 
               "color_red" => 0, "color_green" => 0, "color_blue" => 0];  

    return $champs;
}

function remplissageChamps($champs){
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if (isset($_POST['nom_orginateur'])){
            $champs['user'] = $_POST['nom_orginateur'];
        }

        if (isset($_POST['niveau_combinaison'])){
            $champs['combinaison'] = intval($_POST['niveau_combinaison']);
            $champs['combinaison']++;
        }

        if (isset($_POST['color_red'])){
            $champs['color_red'] = intval($_POST['color_red']);
        }

        if (isset($_POST['color_green'])){
            $champs['color_green'] = intval($_POST['color_green']);
        }

        if (isset($_POST['color_blue'])){
            $champs['color_blue'] = intval($_POST['color_blue']);
        }

        $value_Red_temp = $champs['color_red'] - 25;
        $value_Green_temp = $champs['color_green'] - 25;
        // Si la partie bleu et vert sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
        if ($value_Green_temp > 0){
            $champs['color_green'] = $value_Green_temp;
            $champs['color_blue'] = $value_Green_temp;
            // Si la partie rouge sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
        } elseif ($value_Red_temp > 0){
            $champs['color_red'] = $value_Red_temp;
            $champs['color_green'] = 0;
            $champs['color_blue'] = 0;
        } else {
            $champs['color_red'] = 0;
            $champs['color_green'] = 0;
            $champs['color_blue'] = 0;
        }
    }
    return $champs;
}

function selection_small_big_blind($connMYSQL, $champs){
    // Au moment arriver ici, la combinaison est aumenter précédament dans l'autre fonction
    $result_double_dimention = [];
    $sql = "SELECT small, big FROM benoitmignault_ca_mywebsite.mise_small_big where user = '{$champs['user']}' order by small";
    $result = $connMYSQL->query($sql);  

    if ($result->num_rows > 0){ 
        foreach ($result as $row) {
            // On insère tous les couples small et big dans le tableau double dimensions
            $couple = ["small" => $row['small'], "big" => $row['big']];  
            $result_double_dimention[] = $couple;
        }
        // le nombre de la ligne est toujours pareil
        $nbLignes = $result->num_rows;
        if ($champs['combinaison'] <= $nbLignes){
            foreach ($result_double_dimention as $couple => $value){
                // On passe en revue toutes les combinaisons et lorsque nous arrivons à celle que nous voulons afficher on stock les données dans les deux variables
                if ($champs['combinaison'] == $couple){
                    $champs['valeurSmall'] = $value['small'];        
                    $champs['valeurBig'] = $value['big'];        
                }
            }
            $nbLignes--;
            // $champs['combinaison']++ ne jamais faire ca en validation d'une condition
            // Ici, nous avons atteint la derniere combinaison small et big
            if ($champs['combinaison'] == $nbLignes){
                $champs['trop_valeur'] = true;
            }
        } 
        // Le retour de fonction n'a trouvé aucun valeur
    } elseif ($result->num_rows == 0){ 
        $champs['aucune_valeur'] = true;
    } 
    return $champs;
}

function returnOfAJAX($champs){    
    $return = $champs;
    $return["data"] = json_encode($return, JSON_FORCE_OBJECT);    
    echo json_encode($return, JSON_FORCE_OBJECT);
}

if (is_ajax()) {
    // À titre exemple de 2e niveau de sécurité 
    if (isset($_POST["niveau_combinaison"]) && isset($_POST["nom_orginateur"])) {        
        $connMYSQL = connexionBD();
        if ($connMYSQL){
            $champs = initialisation_Champs();
            $champs = remplissageChamps($champs);            
            $champs = selection_small_big_blind($connMYSQL, $champs);
            returnOfAJAX($champs);
        } else {
            $champs["situation1"] = "Impossible d'accéder à la BD. Veuillez réasseyer plutard !";
            $return["erreur"] = json_encode($champs, JSON_FORCE_OBJECT);
            echo json_encode($return, JSON_FORCE_OBJECT);
        }

    } else {
        $champs["situation2"] = "Il manque des informations importantes. Revalider vos informations !";
        $return["erreur"] = json_encode($champs, JSON_FORCE_OBJECT);
        echo json_encode($return, JSON_FORCE_OBJECT);
    }

} else {
    $champs["situation3"] = "Ce fichier doit être caller via un appel AJAX !";
    $return["erreur"] = json_encode($champs, JSON_FORCE_OBJECT);
    echo json_encode($return, JSON_FORCE_OBJECT);
}
?>