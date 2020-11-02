<?php 
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function initial_Champs() {
    $champs = ["type_langue" => "", "tableau_calendrier" => ""];
    return $champs;
}

function fillingChamps($champs){
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if (isset($_POST['type_langue'])){
            $champs['type_langue'] = $_POST['type_langue'];
        }
    }
    return $champs;
}

function amont_Days_for_this_Month($id_month, $years){
    $amontOdDays = 0;
    switch ($id_month) {
        case 1 : case 3 : case 5 : case 7 : case 8 : case 10 : case 12 : $amontOdDays = 31; break;
        case 4 : case 6 : case 9 : case 11 : $amontOdDays = 30; break;
        case 2 : 
            $reste = $years % 4;
            if ($reste == 0){
                $amontOdDays = 29;
            } else {
                $amontOdDays = 28;
            }
            break;
    }
    return $amontOdDays;    
}

function get_Name_Month($id_month, $champs){
    $month = "";
    if ($champs['type_langue'] == "fr"){
        switch ($id_month){
            case 1 : $month = "Janvier"; break;
            case 2 : $month = "Février"; break;
            case 3 : $month = "Mars"; break;
            case 4 : $month = "Avril"; break;
            case 5 : $month = "Mai"; break;
            case 6 : $month = "Juin"; break;
            case 7 : $month = "Juillet"; break;
            case 8 : $month = "Août"; break;
            case 9 : $month = "Septembre"; break;
            case 10 : $month = "Octobre"; break;
            case 11 : $month = "Novembre"; break;
            case 12 : $month = "Décembre"; break;
        }

    } elseif ($champs['type_langue'] == "en"){
        switch ($id_month){
            case 1 : $month = "January"; break;
            case 2 : $month = "February"; break;
            case 3 : $month = "March"; break;
            case 4 : $month = "April"; break;
            case 5 : $month = "May"; break;
            case 6 : $month = "June"; break;
            case 7 : $month = "July"; break;
            case 8 : $month = "August"; break;
            case 9 : $month = "September"; break;
            case 10 : $month = "October"; break;
            case 11 : $month = "November"; break;
            case 12 : $month = "December"; break;
        }
    }

    return $month;
}

function create_calendar($champs){    
    date_default_timezone_set('America/New_York');
    $table_calendar = "";
    $month = date('m');
    $years = date('Y');
    $day = date('j');

    $date_month = mktime(0,0,0,$month,'01',$years);
    $def_day = date('w',$date_month);
    $name_month = get_Name_Month($month, $champs);

    //We find the max of amount days in this month
    $nbr_jour = amont_Days_for_this_Month($month,$years);

    $counterOfDays = 0;
    $follow_day = $def_day;
    $x = 1;

    $table_calendar .= "<table class=\"calendrier\">";
    $table_calendar .= "<thead>";
    $table_calendar .= "<tr class=\"ligne_mois_actuel\"><th class=\"contenu_ligne_mois\" colspan=\"7\">$name_month $years</th></tr>";
    $table_calendar .= "<tr class=\"ligne_jour_lettre\">";
    $table_calendar .= "<th class=\"contenu_ligne_jour_lettre\">D</th>";
    $table_calendar .= "<th class=\"contenu_ligne_jour_lettre\">L</th>";
    $table_calendar .= "<th class=\"contenu_ligne_jour_lettre\">M</th>";
    $table_calendar .= "<th class=\"contenu_ligne_jour_lettre\">M</th>";
    $table_calendar .= "<th class=\"contenu_ligne_jour_lettre\">J</th>";
    $table_calendar .= "<th class=\"contenu_ligne_jour_lettre\">V</th>";
    $table_calendar .= "<th class=\"contenu_ligne_jour_lettre\">S</th>";
    $table_calendar .= "</tr>";
    $table_calendar .= "</thead>";

    $table_calendar .= "<tbody>";

    while($x <= $nbr_jour){
        if ($counterOfDays == $follow_day){
            $aff = $x;
            $x += 1;
            if ($follow_day == 6){
                $follow_day = 0;
            } else {
                $follow_day += 1;
            }
        } else {
            $aff = "";
        }

        if ($counterOfDays == 0){
            $table_calendar .= "<tr class=\"ligne_jour_chiffre\">";
        }

        // We display the day
        if ($aff == $day) {
            // As it is the day of the month today, it takes the special attributes
            $table_calendar .= "<td class=\"contenu_ligne_jour_actuel\">";
            $table_calendar .= $aff;
        } else {
            // Since this is not the day of the month today, it takes the standard attributes
            $table_calendar .= "<td>";
            $table_calendar .= $aff;
        }
        $table_calendar .= "</td>";

        if ($counterOfDays == 6){
            $counterOfDays = 0;
        } else {
            $counterOfDays += 1;
        }
    }

    if (($counterOfDays <> 6) && ($counterOfDays <> 0)){
        for ($i = $counterOfDays; $i <= 6; $i++){
            $table_calendar .= "<td></td>";
            if ($counterOfDays == 6){
                $table_calendar .= "</tr>";
            }
        }
    }
    $table_calendar .= "<tr class=\"ligne_jour_chiffre\"><td class=\"contenu_ligne_heure_actuel\" colspan=\"7\"></td></tr>";
    $table_calendar .= "</tbody>";
    $table_calendar .= "</table>";

    return $table_calendar;
}

function returnOfAJAX($champs){    
    $return = $champs;
    $return["data"] = json_encode($return, JSON_FORCE_OBJECT);    
    echo json_encode($return, JSON_FORCE_OBJECT);
}

if (is_ajax()) {
    // À titre exemple de 2e niveau de sécurité 
    if ($_POST["type_langue"] && $_POST["type_langue"] != ""){
        $champs = initial_Champs();
        $champs = fillingChamps($champs);
        $champs['tableau_calendrier'] = create_calendar($champs);
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