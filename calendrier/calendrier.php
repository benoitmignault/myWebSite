<?php 
header("Content-type: application/json; charset=utf-8");

function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function initialisation_Champs() {
    $champs = ["type_langue" => "", "tableau_calendrier" => ""];
    return $champs;
}

function remplissageChamps($champs){
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if (isset($_POST['type_langue'])){
            $champs['type_langue'] = $_POST['type_langue'];
        }
    }
    return $champs;
}

function nombre_jours_mois($id_mois, $annee){
    $nbr_jour = 0;
    switch ($id_mois) {
        case 1 : case 3 : case 5 : case 7 : case 8 : case 10 : case 12 : $nbr_jour = 31; break;
        case 4 : case 6 : case 9 : case 11 : $nbr_jour = 30; break;
        case 2 : 
            $reste = $annee % 4;
            if ($reste == 0){
                $nbr_jour = 29;
            } else {
                $nbr_jour = 28;
            }
            break;
    }
    return $nbr_jour;
}

function convertir_mois($id_mois, $champs){
    $mois = "";
    if ($champs['type_langue'] == "fr"){
        switch ($id_mois){
            case 1 : $mois = "Janvier"; break;
            case 2 : $mois = "Février"; break;
            case 3 : $mois = "Mars"; break;
            case 4 : $mois = "Avril"; break;
            case 5 : $mois = "Mai"; break;
            case 6 : $mois = "Juin"; break;
            case 7 : $mois = "Juillet"; break;
            case 8 : $mois = "Août"; break;
            case 9 : $mois = "Septembre"; break;
            case 10 : $mois = "Octobre"; break;
            case 11 : $mois = "Novembre"; break;
            case 12 : $mois = "Décembre"; break;
        }

    } elseif ($champs['type_langue'] == "en"){
        switch ($id_mois){
            case 1 : $mois = "January"; break;
            case 2 : $mois = "February"; break;
            case 3 : $mois = "March"; break;
            case 4 : $mois = "April"; break;
            case 5 : $mois = "May"; break;
            case 6 : $mois = "June"; break;
            case 7 : $mois = "July"; break;
            case 8 : $mois = "August"; break;
            case 9 : $mois = "September"; break;
            case 10 : $mois = "October"; break;
            case 11 : $mois = "November"; break;
            case 12 : $mois = "December"; break;
        }
    }

    return $mois;
}

function generer_calendrier($champs){
    // Je dois setter le time zone de montreal pour éviter de sauter à la journée suivante avant le temps.
    date_default_timezone_set('America/New_York');
    $tableau_calendrier = "";
    $mois = date('m');
    $annee = date('Y');
    $jour = date('j');

    $date_mois = mktime(0,0,0,$mois,'01',$annee);
    $def_jour = date('w',$date_mois);
    $nom_mois = convertir_mois($mois, $champs);


    //On trouve le nombre maximun de jour dans le mois
    $nbr_jour = nombre_jours_mois($mois,$annee);

    $compteur_jour = 0;
    $suivi_jour = $def_jour;
    $x = 1;

    $tableau_calendrier .= "<table class=\"calendrier\">";
    $tableau_calendrier .= "<thead>";
    $tableau_calendrier .= "<tr class=\"ligne_mois_actuel\"><th class=\"contenu_ligne_mois\" colspan=\"7\">$nom_mois $annee</th></tr>";
    $tableau_calendrier .= "<tr class=\"ligne_jour_lettre\">";
    $tableau_calendrier .= "<th class=\"contenu_ligne_jour_lettre\">D</th>";
    $tableau_calendrier .= "<th class=\"contenu_ligne_jour_lettre\">L</th>";
    $tableau_calendrier .= "<th class=\"contenu_ligne_jour_lettre\">M</th>";
    $tableau_calendrier .= "<th class=\"contenu_ligne_jour_lettre\">M</th>";
    $tableau_calendrier .= "<th class=\"contenu_ligne_jour_lettre\">J</th>";
    $tableau_calendrier .= "<th class=\"contenu_ligne_jour_lettre\">V</th>";
    $tableau_calendrier .= "<th class=\"contenu_ligne_jour_lettre\">S</th>";
    $tableau_calendrier .= "</tr>";
    $tableau_calendrier .= "</thead>";

    $tableau_calendrier .= "<tbody>";

    while($x <= $nbr_jour){
        if ($compteur_jour == $suivi_jour){
            $aff = $x;
            $x += 1;
            if ($suivi_jour == 6){
                $suivi_jour = 0;
            } else {
                $suivi_jour += 1;
            }
        } else {
            $aff = "";
        }

        if ($compteur_jour == 0){
            $tableau_calendrier .= "<tr class=\"ligne_jour_chiffre\">";
        }

        //On affiche la journée        
        if ($aff == $jour) {
            // Comme c'est le jour du mois d'aujourd'hui, il prend les attributs spéciaux
            $tableau_calendrier .= "<td class=\"contenu_ligne_jour_actuel\">";
            $tableau_calendrier .= $aff;
        } else {
            // Comme ce nest pas le jour du mois d'aujourd'hui, il prend les attributs standard
            $tableau_calendrier .= "<td>";
            $tableau_calendrier .= $aff;
        }
        $tableau_calendrier .= "</td>";

        if ($compteur_jour == 6){
            $compteur_jour = 0;
        } else {
            $compteur_jour += 1;
        }
    }

    if (($compteur_jour <> 6) && ($compteur_jour <> 0)){
        for ($i = $compteur_jour; $i <= 6; $i++){
            $tableau_calendrier .= "<td></td>";
            if ($compteur_jour == 6){
                $tableau_calendrier .= "</tr>";
            }
        }
    }
    $tableau_calendrier .= "<tr class=\"ligne_jour_chiffre\"><td class=\"contenu_ligne_heure_actuel\" colspan=\"7\"></td></tr>";
    $tableau_calendrier .= "</tbody>";
    $tableau_calendrier .= "</table>";

    return $tableau_calendrier;
}

function returnOfAJAX($champs){    
    $return = $champs;
    $return["data"] = json_encode($return, JSON_FORCE_OBJECT);    
    echo json_encode($return, JSON_FORCE_OBJECT);
}

if (is_ajax()) {
    // À titre exemple de 2e niveau de sécurité 
    if ($_POST["type_langue"] && $_POST["type_langue"] != ""){
        $champs = initialisation_Champs();
        $champs = remplissageChamps($champs);
        $champs['tableau_calendrier'] = generer_calendrier($champs);
        returnOfAJAX($champs);
    } else {
        $champs["situation1"] = "Attention ! Il manque la valeur de la langue pour l'affichage de la page web !";
        $return["erreur"] = json_encode($champs, JSON_FORCE_OBJECT);
        echo json_encode($return, JSON_FORCE_OBJECT);
    }

} else {
    $champs["situation2"] = "Attention ! Ce fichier doit être caller via un appel AJAX !";
    $return["erreur"] = json_encode($champs, JSON_FORCE_OBJECT);
    echo json_encode($return, JSON_FORCE_OBJECT);
}
?>