<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (https://github.com/PHPOffice/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    PHPExcel last version, 1.8.1, was released in 2015. 
 *             The project was officially deprecated in 2017 and permanently archived in 2019.
*/

// En raison du bug de reset de page....on va voir ce que ca donne - Benoit
header('Connection:keep-alive', true);

/** Pour lire des fichiers Excel */
require_once dirname(__FILE__) . '/../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';

// include('../../login/include_fns.php');

// Déclaration des CONSTANTES pour des opérations communes

// Type de Commutateur pour les requêtes de FIZZ 
define("SWITCH_4G_LTE", "4G/LTE");
define("SWITCH_VOLTE", "VOLTE");

// Pour déterminer on va vers quel sens dans les requêtes 
define("FOURNISSEUR_FIZZ", "FIZZ");
define("FOURNISSEUR_VIDEOTRON", "VIDEOTRON");

// Statut pour des requêtes SQL dynamique
define("STATUT_DISPO", "Disponible");
define("STATUT_UTILISE", "Utilisé");

// La section pour saisir le nombre de dispo / utilisé
define("COLONNE_DISPO", "NB_DISPO");
define("COLONNE_UTILISE", "NB_UTILISE");

/**
 * C'est ici , que nous allons construire nos BD temporairement information pour exécuter l'outil en soit
 * Plusieurs variables seront constituées de type boolean
 * Les variables avec des valeurs numériques seront initiées à 0
 * 
 * @return array
 */
function initialisation(){

    $array_Champs = array("courriel_par_default" => "", "courriel_inv" => false, "domaine_videotron_inv" => false, "courriel_vide" => false,  
                          "chemin_fichier" => "", "nom_fichier_courriel" => "", "courriel" => "", "type_recherche" => "", "type_fournisseur" => "",
                          "fournisseur_vtl" => false, "fournisseur_fizz" => false, "action_afficher" => false, "action_courriel" => false,
                          "visible_info" => "", "visible_courriel" => "", "maxId" => 0,
                          "inventaire_circonscriptions" => array(),
                          "messages_erreurs" => array(), 
                          "liste_colonnes" => array("NO_CIRC", "NOM_CIRC", "CODE_PROVINCE", "SWITCH", "NB_DISPO", "NB_UTILISE", "NB_TOTAL", "RATIO_DISPO") );

    return $array_Champs;
}

/**
 * On doit renommence le nom de table, une fois passer en PROD
 * Fonction pour aller récuprer le nombre de fois que nous avons utilisé l'outil. 
 * Cette fonction sera utilisée dans la fonction @see remplisage_champs
 * 
 * @param $potentielBD -> Connexion au serveur potentiel
 * @return integer
 */
function requete_SQL_nb_recherches($potentielBD){

    $sql = "SELECT count(idRecherche) FROM statrecherche_inventaire_vtl_fizz";
    $result_potentielBD = $potentielBD->query($sql);
    $row = $result_potentielBD->fetch_row(); // c'est mon array de résultats	
    $maxId = (int) $row[0];	// Initialisation et assignation du résultat

    return $maxId;
}

/**
 * Fonction pour commencer à setter des informations à mes variables de départs, si on est dans le GET
 * Lorsque nous seront dans le POST, on va assigner le reste des variables nécessaires 
 *  
 * @param $potentielBD -> Connexion au serveur potentiel
 * @param array $array_Champs -> La liste de mes variables utiliées
 * @return array 
 */
function remplisage_champs($potentielBD, $array_Champs){

    // Récupération commune GET & POST
    $array_Champs["maxId"] = requete_SQL_nb_recherches($potentielBD);

    // On doit setter les informations de base, à l'entrée dans l'outil
    if ($_SERVER['REQUEST_METHOD'] == 'GET'){
        //$array_Champs['courriel_par_default'] = $_SESSION['email']; // information par défault qui ne change jamais
        $array_Champs['courriel_par_default'] = "benoit.mignault@videotron.com"; // information par défault qui ne change jamais - EN DEV
        $array_Champs['courriel'] = $array_Champs['courriel_par_default']; // information qui ira dans le champ
        $array_Champs['visible_info'] = "display";
        $array_Champs['visible_courriel'] = "hidden";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        
        // On doit setter le mode de visibilité pour la section de la procédure
        if (isset($_POST['visible_info'])){
            $array_Champs['visible_info'] = $_POST['visible_info'];
        }

        // On doit setter le mode de visibilité pour la section du courriel
        if (isset($_POST['visible_courriel'])){
            $array_Champs['visible_courriel'] = $_POST['visible_courriel'];
        }

        // On doit setter le courriel par défault dans le post, si on décide d'effacer tout et de revenir à la case départ
        if (isset($_POST['courriel_par_default'])){
            $array_Champs['courriel_par_default'] = $_POST['courriel_par_default'];
        }

        // Ajout du choix entre affichage seulement et affichage + courriel
        // 2023-10-17
        if (isset($_POST['choix_action'])){

            if ($_POST['choix_action'] == "action_afficher"){
                $array_Champs['action_afficher'] = true;
                $array_Champs['type_recherche'] = "afficher";

                // Si on fait un choix d'affichage seulement, on va remettre l'infromation par défault pour le courriel
                //$array_Champs['courriel'] = $_SESSION['email'];
                $array_Champs['courriel'] = "benoit.mignault@videotron.com";

                
            } elseif ($_POST['choix_action'] == "action_courriel"){
                $array_Champs['action_courriel'] = true;
                $array_Champs['type_recherche'] = "courriel";

                // Comme nous avons choisi un envoi par courriel, on réassoci le courriel au cas où, qu'il aurait changé
                if (isset($_POST["courriel"])){
                    $array_Champs["courriel"] = strtoupper($_POST["courriel"]);
                }
            }            
        }

        // Ajout de la section pour déterminer si on veut Videotron ou FIZZ comme choix de fournisseur TMO
        if (isset($_POST['choix_fournisseur'])){

            if ($_POST['choix_fournisseur'] == "fournisseur_vtl"){
                $array_Champs['fournisseur_vtl'] = true;
                $array_Champs['type_fournisseur'] = "VIDEOTRON";
                
            } elseif ($_POST['choix_fournisseur'] == "fournisseur_fizz"){
                $array_Champs['fournisseur_fizz'] = true;
                $array_Champs['type_fournisseur'] = "FIZZ";                
            }
        }        
    }

    return $array_Champs;
}

/**
 * Ici, on va faire des validations seulement sur le champ courriel qui est modifiable
 * 
 * @param array $array_Champs -> La liste de variables dont on va réutiliser celles qui sont associées à la notion de courriel
 * @return array
 */
function validation_champs($array_Champs){

    // Seulement quand on choisi l'option du courriel
    if ($array_Champs["action_courriel"]){
        
        if (empty($array_Champs['courriel'])){
            $array_Champs['courriel_vide'] = true; 
        } else {
            if (filter_var($array_Champs['courriel'], FILTER_VALIDATE_EMAIL)){
                $parts = explode('@', $array_Champs['courriel']); // Créer un array de 2 valeurs.
                $domain = array_pop($parts); // Enleve et retourne le dernier élément du Array soit le domain            

                if ($domain != "VIDEOTRON.COM"){
                    $array_Champs['domaine_videotron_inv'] = true; 
                }                             
            } else {
                $array_Champs['courriel_inv'] = true; 
                $array_Champs['domaine_videotron_inv'] = true;
            }
        }
    }

    return $array_Champs;
}

/**
 * En fonction des validations construites lors de la fonction @see validation_champs, on va setter le nombre de messages erreurs nécessaire.
 * 
 * @param array $array_Champs -> La liste de mes variables utiliées
 * @return array 
 */
function situation_erreur($array_Champs){

    // Il y a un seul champ qui peut être vide
    if ($array_Champs['courriel_vide']){
        array_push($array_Champs['messages_erreurs'], "Veuiller saisir un courriel !");
    } else {
        // Le domaine est invalide
        if ($array_Champs['domaine_videotron_inv']){
            array_push($array_Champs['messages_erreurs'], "Veuiller saisir un courriel avec le domaine videotron.com !");
        }

        // Le courriel est invalide
        if ($array_Champs['courriel_inv']){
            array_push($array_Champs['messages_erreurs'], "Veuiller saisir un courriel valide !");
        }
    } 

    return $array_Champs;
}

/**
 * Fonction pour aller chercher notre inventaire des circonscriptions
 * À partir de novembre 2023, nous allons choisir si on veut un inventaire de VIDEOTRON ou de FIZZ
 * Cette information est statique, donc pour ajouter de nouvelles circonscriptions, les gens du STR doivent informer l'analyste qui s'occupe des BD du potentiel
 * 
 * @param $potentielBD -> Connexion au serveur potentiel
 * @param string $fournisseur_tmo -> quel fournisseur a été choisi
 * @return array un array avec la liste des combinaisons 
 */
function requete_SQL_liste_circonscriptions_ouverte($potentielBD, $fournisseur_tmo){

    $select = "SELECT NO_CIRC, NOM_CIRC, CODE_PROVINCE, SWITCH ";

    $from = "";
    if ($fournisseur_tmo == FOURNISSEUR_VIDEOTRON){

        $from = "FROM liste_circ_vtl_ouverte ";

    } elseif ($fournisseur_tmo == FOURNISSEUR_FIZZ){

        $from = "FROM liste_circ_fizz_ouverte ";
    }    

    // À la demande de Maxime, 11 oct, on affichera les infos VOLTE en premier
    $orderBY = "ORDER BY SWITCH desc, CODE_PROVINCE desc, NOM_CIRC";

    $sql = $select . $from . $orderBY;
    $result_potentielBD = $potentielBD->query($sql);

    // Retourne de la liste des circonscriptions, venant de la fct
    $inventaire_circonscriptions = recuperation_liste_circonscriptions_ouvertes($potentielBD, $result_potentielBD);

    return $inventaire_circonscriptions;
}

/**
 * Créer et insérer les informations concernant la requete de la fonction @see requete_SQL_liste_circonscriptions_ouverte
 * Retourne un array avec toutes les combinaisons et les valeurs par défaut à 0
 * 
 * @param object $potentielBD -> Connexion au serveur potentiel
 * @param object $result -> Le résultat de la requête SQL via la BD du potentielLa liste de mes variables utiliées
 * @return array 
 */
function recuperation_liste_circonscriptions_ouvertes($potentielBD, $result){

    // Création du tableau contenant la liste des circonscriptions dispo qui sera à 2D
    // Une première profondeur par le No circonscription, ensuite pour chaque circonscription, il va y avoir 2 arrays dont leur keys seront la switch, respectives.
    $inventaire_circonscriptions = array();    

    // Récupération de chaques résultats
    while ($un_result = $result->fetch_assoc()) {
        // Création d'un array pour un résultat
        $une_circonscription = array();
        $une_circonscription["NO_CIRC"] = $un_result["NO_CIRC"];
        $une_circonscription["NOM_CIRC"] = $un_result["NOM_CIRC"];
        $une_circonscription["CODE_PROVINCE"] = $un_result["CODE_PROVINCE"];
        $une_circonscription["SWITCH"] = $un_result["SWITCH"];
        $une_circonscription["NB_DISPO"] = 0;
        $une_circonscription["NB_UTILISE"] = 0;
        $une_circonscription["NB_TOTAL"] = 0;
        $une_circonscription["RATIO_DISPO"] = 0; 
        $une_circonscription["RESULT_DOUBLON_UTILISE"] = 0;

        // De cette manière, je vais pouvoir aller chercher l'information par les keys NO_CIRC & SWITCH, lorsqu'il faudra remplir les valeurs
        // 11 octobre, inversement du tableau 2D par le type de switch en premier
        $inventaire_circonscriptions[$un_result["SWITCH"]][$un_result["NO_CIRC"]] = $une_circonscription;
    }

    return $inventaire_circonscriptions;
}

/**
 * Début de la collecte d'information venant de Remedy
 * Fonction mère qui va appeller plusieurs sous fonctions, afin de collecter les informations nécessaires, en fonction du choix de fournisseur que nous avons fait
 * 
 * @param object $db_remedy_prod -> Connexion à Remedy via ODBC
 * @param string $type_fournisseur -> le fournisseur sélectionné
 * @param array $inventaire_circonscriptions 
 * @return array 
 */
function requete_SQL_remedy($db_remedy_prod, $type_fournisseur, $inventaire_circonscriptions){

    // Si nous avons choisi l'opérateur FIZZ
    if ($type_fournisseur == FOURNISSEUR_FIZZ){
        // Étape 1 - Aller chercher, les numéros disponibles  
        // A - Sortir pour 4G/LTE
        $inventaire_circonscriptions = requete_SQL_remedy_Inventaire_Dispo($db_remedy_prod, SWITCH_4G_LTE, $inventaire_circonscriptions);

        // b - Sortir la combinaison de No_Circ & 4G/LTE et ajouter le tout dans la bonne circonscription
        $inventaire_circonscriptions = requete_SQL_remedy_Inventaire_Dispo($db_remedy_prod, SWITCH_VOLTE, $inventaire_circonscriptions);

        // Étape 2 - Aller chercher, les numéros Utilisé qui seront divisés en trois sous étapes
        // A - Sortir la combinaison de No_Circ & 4G/LTE et ajouter le tout dans la bonne circonscription
        $inventaire_circonscriptions = requete_SQL_remedy_Inventaire_Utilise($db_remedy_prod, SWITCH_4G_LTE, $inventaire_circonscriptions);

        // B - Sortir la combinaison de No_Circ & VOLTE et ajouter le tout dans la bonne circonscription
        $inventaire_circonscriptions = requete_SQL_remedy_Inventaire_Utilise($db_remedy_prod, SWITCH_VOLTE, $inventaire_circonscriptions);

        // C - Refaire une requête SQL par combinaison de Commutateur & circonscription qui aurait donné un résultat 0
        $inventaire_circonscriptions = requete_SQL_remedy_Inventaire_Utilise_LAST_CALL($db_remedy_prod, $inventaire_circonscriptions);

        // Si nous avons choisi l'opérateur VIDEOTRON     
    } elseif ($type_fournisseur == FOURNISSEUR_VIDEOTRON){
        
        // Une fonction principale qui va caller une sous fonction 2 fois par combinaison Switch/Circonscription
        $inventaire_circonscriptions = recuperation_Inventaire_VIDEOTRON($db_remedy_prod, $inventaire_circonscriptions, COLONNE_DISPO, COLONNE_UTILISE, STATUT_DISPO, STATUT_UTILISE);
    } 

    return $inventaire_circonscriptions;
}

/**
 * On va passer au travers de notre inventaire, jusqu'à présent avec des valeurs à 0 pour les remplacer par les valeurs.
 * En raison du trop grand nombre de numéros pour Vidéotron.
 * On va faire une requête par par combinaison de Switch/Circonscription qui va nous donner les infos du nombre de # dispo et utilisé, 
 * sauf Montréal, qu'on devra la faire de manière très précise.
 * 
 * @param object $db_remedy_prod -> Connexion à Remedy via ODBC
 * @param array $inventaire_circonscriptions
 * @param string $colonne_dispo -> Correspond au contenu de la constante du même nom
 * @param string $colonne_utilise -> Correspond au contenu de la constante du même nom
 * @param string $statut_dispo -> Correspond au contenu de la constante du même nom
 * @param string $statut_utilise -> Correspond au contenu de la constante du même nom
 * @return array 
 */
function recuperation_Inventaire_VIDEOTRON($db_remedy_prod, $inventaire_circonscriptions, $colonne_dispo, $colonne_utilise, $statut_dispo, $statut_utilise){

    // On va généré une requêtes SQL par combinaison Switch/CIRC qui va englober les deux statut sauf pour Montréal
    foreach($inventaire_circonscriptions as $type_commutateur => $liste_circonscriptions) {
        
        foreach($liste_circonscriptions as $une_circ) {

            // Conversion en numérique du # de circonscription
            $no_circ = intval($une_circ["NO_CIRC"]);

            // Tant que nous ne sommes pas rendu à Montréal, car cette dernière possède plus de 250k
            if ($no_circ != 17172){

                $liste_statut = requete_SQL_remedy_Inventaire_Circonscription_Swtich_VTL($db_remedy_prod, $type_commutateur, $no_circ);
                // Si nous avons les 2 statuts dans le résultats                
                if (count($liste_statut) == 2){
                    $inventaire_circonscriptions[$type_commutateur][$no_circ][$colonne_dispo] = $liste_statut[$statut_dispo];
                    $inventaire_circonscriptions[$type_commutateur][$no_circ][$colonne_utilise] = $liste_statut[$statut_utilise];

                    // Si nous avons un seul statut, sans savoir lequel précisément, on doit trouver, c'est le quel
                } elseif (count($liste_statut) == 1){

                    if (array_key_exists($statut_dispo, $liste_statut)){
                        $inventaire_circonscriptions[$type_commutateur][$no_circ][$colonne_dispo] = $liste_statut[$statut_dispo];

                    } elseif (array_key_exists($statut_utilise, $liste_statut)){
                        $inventaire_circonscriptions[$type_commutateur][$no_circ][$colonne_utilise] = $liste_statut[$statut_utilise];
                    }
                } else {
                    // Sinon, on s'en fou et ça va rester à 0...
                }
            } else {
                // Injection de l'information en fonction du commutateur, de la circonscription, statut
                $inventaire_circonscriptions[$type_commutateur][$no_circ][$colonne_dispo] = requete_SQL_remedy_Inventaire_Grosse_Circonscription_VTL($db_remedy_prod, $type_commutateur, $no_circ, $statut_dispo);
                $inventaire_circonscriptions[$type_commutateur][$no_circ][$colonne_utilise] = requete_SQL_remedy_Inventaire_Grosse_Circonscription_VTL($db_remedy_prod, $type_commutateur, $no_circ, $statut_utilise);
            }
        }
    }

    return $inventaire_circonscriptions;
}

/**
 * On commence par aller chercher l'information sur le nombre de numéro dispo et Utilisé par combinaison, donc on va faire 90 requêtes au lieu du double 
 * On utilise cette fonction, @see recuperation_Inventaire_VIDEOTRON où cette fonction sera appellée, sauf pour la circonscription de Montréal.
 * 
 * @param object $db_remedy_prod -> Connexion à Remedy via ODBC
 * @param string $type_commutateur -> Choix du commutateur
 * @param integer $no_circ -> numéro de la circonscription
 * @return array 
 */
function requete_SQL_remedy_Inventaire_Circonscription_Swtich_VTL($db_remedy_prod, $type_commutateur, $no_circ){

    $select = "SELECT Statut_Inventaire, count(NO_TELEPHONE) as NOMBRE ";
    $from = "FROM GNT_INT_Inventaire_No_Telephone ";
    $where = "WHERE (Statut_Inventaire = 'Disponible' OR Statut_Inventaire = 'Utilisé') AND SPID = '328F' AND TYPE_DE_TRANSPORT = 'Cellulaire' AND POOLID = '1' AND COMMUTATEUR = '$type_commutateur' AND NO_CIRCONSCRIPTION = $no_circ ";
    $groupBY = "GROUP BY Statut_Inventaire";

    // Formation de la requête SQL pour Remedy
    $sql = $select . $from . $where . $groupBY;
    
    $result_combinaisons = odbc_exec($db_remedy_prod, $sql);
    $liste_statut = recuperation_info_Remedy_Statut($db_remedy_prod, $result_combinaisons);

    return $liste_statut;
}

/**
 * On récupère l'information des deux statuts, en créant un array avec le statut comme key avec son résultat respectif
 * On utilise cette fonction, @see recuperation_Inventaire_VIDEOTRON 
 * 
 * @param object $db_remedy_prod
 * @param object $result_combinaisons
 * @return array 
 */
function recuperation_info_Remedy_Statut($db_remedy_prod, $result_combinaisons){

    $liste_statut = array();
    while ( $un_resultat = odbc_fetch_array($result_combinaisons) ){

        // On doit retirer les espaces qui peuvent être insérer pour des raisons que j'ignore.
        $liste_statut[trim($un_resultat["Statut_Inventaire"])] = intval($un_resultat["NOMBRE"]);
    }

    return $liste_statut;
}

/**
 * La circonscription de Montréal, qui est la seule pour l'instant à dépasser les 250k de numéros «Utilisé» pour Vidéotron.
 * On va faire une recherche précise
 * 
 * @param object $db_remedy_prod -> Connexion à Remedy via ODBC
 * @param array $inventaire_circonscriptions
 * @param string $colonne_dispo -> Correspond au contenu de la constante du même nom
 * @param string $colonne_utilise -> Correspond au contenu de la constante du même nom
 * @param string $statut_dispo -> Correspond au contenu de la constante du même nom
 * @param string $statut_utilise -> Correspond au contenu de la constante du même nom
 * @return array 
 */
function requete_SQL_remedy_Inventaire_Grosse_Circonscription_VTL($db_remedy_prod, $type_commutateur, $no_circ, $statut){

    $select = "SELECT NO_CIRCONSCRIPTION, count(NO_TELEPHONE) as NOMBRE ";
    $from = "FROM GNT_INT_Inventaire_No_Telephone ";
    $where = "WHERE Statut_Inventaire = '$statut' AND SPID = '328F' AND TYPE_DE_TRANSPORT = 'Cellulaire' AND POOLID = '1' AND COMMUTATEUR = '$type_commutateur' AND NO_CIRCONSCRIPTION = $no_circ ";
    $groupBY = "GROUP BY NO_CIRCONSCRIPTION";
    
    // Formation de la requête SQL pour Remedy
    $sql = $select . $from . $where . $groupBY;
    
    $result_triple_combinaisons = odbc_exec($db_remedy_prod, $sql);
    $un_resultat = odbc_fetch_array($result_triple_combinaisons);

    return intval($un_resultat["NOMBRE"]);
}

/**
 * On commence par aller chercher l'information sur le nombre de numéro dispo par combinaison
 * Étape 1 du processus, @see requete_SQL_remedy où cette fonction sera appellée
 * 
 * @param object $db_remedy_prod -> Connexion à Remedy via ODBC
 * @param string $type_commutateur -> Choix du commutateur
 * @param array $inventaire_circonscriptions
 * @return array 
 */
function requete_SQL_remedy_Inventaire_Dispo($db_remedy_prod, $type_commutateur, $inventaire_circonscriptions){    

    $select = "SELECT NO_CIRCONSCRIPTION, count(NO_TELEPHONE) as NOMBRE ";
    $from = "FROM GNT_INT_Inventaire_No_Telephone ";
    $where = "WHERE Statut_Inventaire = 'Disponible' AND SPID = '328F' AND TYPE_DE_TRANSPORT = 'Cellulaire' AND POOLID = '2' AND COMMUTATEUR = '$type_commutateur' ";
    $groupBY = "GROUP BY NO_CIRCONSCRIPTION";
    
    // Formation de la requête SQL pour Remedy
    $sql = $select . $from . $where . $groupBY;
    $resultat_invent_dispo = odbc_exec($db_remedy_prod, $sql);

    $inventaire_circonscriptions = recuperation_info_Remedy_Inventaire_Dispo($db_remedy_prod, $inventaire_circonscriptions, $resultat_invent_dispo, $type_commutateur);

    return $inventaire_circonscriptions;
}

/**
 * On commence par aller chercher l'information sur le nombre de numéro utilisés pour le 4G
 * Étape 2 - A du processus, @see requete_SQL_remedy où cette fonction sera appellée
 * 
 * @param object $db_remedy_prod -> Connexion à Remedy via ODBC
 * @param string $type_commutateur -> Choix du commutateur
 * @param array $inventaire_circonscriptions
 * @return array 
 */
function requete_SQL_remedy_Inventaire_Utilise($db_remedy_prod, $type_commutateur, $inventaire_circonscriptions){

    $select = "SELECT NO_CIRCONSCRIPTION, count(NO_TELEPHONE) as NOMBRE ";
    $from = "FROM GNT_INT_Inventaire_No_Telephone ";
    $where = "WHERE Statut_Inventaire = 'Utilisé' AND SPID = '328F' AND TYPE_DE_TRANSPORT = 'Cellulaire' AND POOLID = '2' AND COMMUTATEUR = '$type_commutateur' ";
    $groupBY = "GROUP BY NO_CIRCONSCRIPTION";
    
    // Formation de la requête SQL pour Remedy
    $sql = $select . $from . $where . $groupBY;
    $result_invent_utilise = odbc_exec($db_remedy_prod, $sql);

    $inventaire_circonscriptions = recuperation_info_Remedy_Inventaire_Utilise_FIZZ($db_remedy_prod, $result_invent_utilise, $inventaire_circonscriptions, $type_commutateur);

    return $inventaire_circonscriptions;
}

/**
 * Requête SQL de dernier recors dans l'éventualité, que nous avons des combinaisons encore à 0
 * On vérifi chaque combinaison de l'inventaire e tpour ceux qui sont à 0, on va aller faire une recherche très précise qui va nous donner l'information ultime recherchée.
 * Étape 2 - C du processus, @see requete_SQL_remedy où cette fonction sera appellée
 * 
 * @param object $db_remedy_prod
 * @param array $inventaire_circonscriptions
 * @return array 
 */
function requete_SQL_remedy_Inventaire_Utilise_LAST_CALL($db_remedy_prod, $inventaire_circonscriptions){

    foreach($inventaire_circonscriptions as $type_commutateur => $liste_circonscriptions) {

        // On itère sur chaque circonscription de chaque type de commutateur (2 x 45)
        foreach($liste_circonscriptions as $une_circ) {

            // Si nous trouvons NB_UTILISE égale à 0, on va refaire le procéssus de requête SQL, mais de manière dynamique que ça soit 4G ou VOLTE, avec la circonscription
            // Ajout en ce 16 octobre, la condition si un résultat est sortie plus d'une fois, dans ce cas là, nous allons aussi faire une recherche précise
            if ($une_circ["NB_UTILISE"] == 0 || $une_circ["RESULT_DOUBLON_UTILISE"] > 1){
                // Conversion en numérique du # de circonscription
                $no_circ = intval($une_circ["NO_CIRC"]);

                $select = "SELECT NO_CIRCONSCRIPTION, count(NO_TELEPHONE) as NOMBRE ";
                $from = "FROM GNT_INT_Inventaire_No_Telephone ";
                // Dans le where, remplacement de la valeur de la switch par la valeur dynique en fonction, où nous en sommes rendu dans le tableau 2D
                // Aussi, remplacement par la valeur numérique du numéro de circonscription, pour extraire l'information de manière précise
                $where = "WHERE Statut_Inventaire = 'Utilisé' AND SPID = '328F' AND TYPE_DE_TRANSPORT = 'Cellulaire' AND POOLID = '2' AND COMMUTATEUR = '$type_commutateur' AND NO_CIRCONSCRIPTION = $no_circ ";
                $groupBY = "GROUP BY NO_CIRCONSCRIPTION";
                
                // Formation de la requête SQL pour Remedy
                $sql = $select . $from . $where . $groupBY;
                $result_invent_circ_LAST_CALL = odbc_exec($db_remedy_prod, $sql);
                $un_resultat = odbc_fetch_array($result_invent_circ_LAST_CALL);

                $inventaire_circonscriptions[$type_commutateur][$no_circ]["NB_UTILISE"] = intval($un_resultat["NOMBRE"]);
            }
        }
    }

    return $inventaire_circonscriptions;
}

/**
 * Récupération de l'information extrait de Remedy, concernant le nombres de numéros disponibles.
 * Retourne l'information @see requete_SQL_remedy_Inventaire_Dispo
 * 
 * @param object $db_remedy_prod
 * @param array $inventaire_circonscriptions
 * @param object $result
 * @return array 
 */
function recuperation_info_Remedy_Inventaire_Dispo($db_remedy_prod, $inventaire_circonscriptions, $result, $type_commutateur){

    while ( $un_resultat = odbc_fetch_array($result) ){
        
        // Récupération des keys pour essayer des trouver dans mon tableau 2D
        $no_circ = intval($un_resultat["NO_CIRCONSCRIPTION"]);
        $nombre = intval($un_resultat["NOMBRE"]);

        // On inverse la vérification, depuis 11 octobre
        // Vérification sur quelle switch nous allons insérer l'information, car on veut VOLTE en premier
        if (array_key_exists($type_commutateur, $inventaire_circonscriptions)) {

            // Vérification de l'existance de la circonscription dans notre liste de base - Important !
            if (array_key_exists($no_circ, $inventaire_circonscriptions[$type_commutateur])) {

                // 11 octobre, inversement du tableau 2D par le type de switch en premier
                // Au cas où, qu'on aurait des doublons dans le résultats, nous allons faire de sommes au lieu d'attribution de valeur
                $inventaire_circonscriptions[$type_commutateur][$no_circ]["NB_DISPO"] = $nombre;
            }
            
            // Sinon, on va laisser une instruction dans la procédure d'utilisation               
        }
        // Sinon, rien
    }
    
     return $inventaire_circonscriptions;
}

/**
 * Utilisation à la fois pour le 4G et VOLTE, le pourquoi le nom de «Combinée»
 * Retourne l'information  @see requete_SQL_remedy_Inventaire_Utilise_4G & requete_SQL_remedy_Inventaire_Utilise_VOLTE
 * 
 * @param object $db_remedy_prod
 * @param object $result
 * @param array $inventaire_circonscriptions
 * @param string $type_commutateur -> Choix du commutateur
 * @return array 
 */
function recuperation_info_Remedy_Inventaire_Utilise_FIZZ($db_remedy_prod, $result, $inventaire_circonscriptions, $type_commutateur){

    while ( $un_resultat = odbc_fetch_array($result) ){

        // Récupération des keys pour essayer des trouver dans mon tableau 2D
        $no_circ = intval($un_resultat["NO_CIRCONSCRIPTION"]);
        $nombre = intval($un_resultat["NOMBRE"]);

        // On inverse la vérification, depuis 11 octobre
        // Vérification sur quelle switch nous allons insérer l'information, car on veut VOLTE en premier
        if (array_key_exists($type_commutateur, $inventaire_circonscriptions)) {

            // Vérification de l'existance de la circonscription dans notre liste de base - Important !
            if (array_key_exists($no_circ, $inventaire_circonscriptions[$type_commutateur])) {

                // Nouvelle règle dans le CQ des requêtes vue que Remedy et ODBC ne sorte pas toujours des infos précises.
                $inventaire_circonscriptions[$type_commutateur][$no_circ]["RESULT_DOUBLON_UTILISE"]++;

                // 16 Octobre
                // On ajout l'information, seulement quand on trouve la combinaison pour la première fois,
                // la 2e et plus ne sert à rien, car la bonne info sera valider dans la requête SQL «Last Call»
                if ($inventaire_circonscriptions[$type_commutateur][$no_circ]["RESULT_DOUBLON_UTILISE"] == 1){

                    // 11 octobre, inversement du tableau 2D par le type de switch en premier
                    $inventaire_circonscriptions[$type_commutateur][$no_circ]["NB_UTILISE"] = $nombre;
                }
            }            
            // Sinon, on va laisser une instruction dans la procédure d'utilisation               
        }
        // Sinon, rien
    }

    return $inventaire_circonscriptions;
}

/**
 * Calcul du nombre total de numéros avec la somme des disponibles et des utilisés
 * 
 * @param array $inventaire_circonscriptions
 * @return array 
 */
function construction_somme_dispo_utilise($inventaire_circonscriptions){

    foreach($inventaire_circonscriptions as $type_commutateur => $liste_circonscriptions) {

        // On itère sur chaque circonscription de chaque type de commutateur (2 x 45)
        foreach($liste_circonscriptions as $une_circ) {

            // Conversion en numérique du # de circonscription
            $no_circ = intval($une_circ["NO_CIRC"]);

            // Par souci de ne pas avoir une ligne trop longue, je vais attribuer des variables plus courtes...
            $nb_dispo = $une_circ["NB_DISPO"];
            $nb_utilise = $une_circ["NB_UTILISE"];

            $inventaire_circonscriptions[$type_commutateur][$no_circ]["NB_TOTAL"] = $nb_dispo + $nb_utilise;            
        }
    }

    return $inventaire_circonscriptions;
}

/**
 * Avec le nombre total, on peut maintenant calculer le ratio du nombre de numéros disponibles
 *  
 * @param array $inventaire_circonscriptions
 * @return array 
 */
function construction_ratio_dispo($inventaire_circonscriptions){

    foreach($inventaire_circonscriptions as $type_commutateur => $liste_circonscriptions) {

        // On itère sur chaque circonscription de chaque type de commutateur (2 x 45)
        foreach($liste_circonscriptions as $une_circ) {

            // Conversion en numérique du # de circonscription
            $no_circ = intval($une_circ["NO_CIRC"]);

            // Par souci de ne pas avoir une ligne trop longue, je vais attribuer des variables plus courtes...
            $nb_dispo = $une_circ["NB_DISPO"];
            $nb_total = $une_circ["NB_TOTAL"];

            // Pour éviter les division par 0
            if ($nb_total == 0){
                $inventaire_circonscriptions[$type_commutateur][$no_circ]["RATIO_DISPO"] = number_format(0, 2);
            } else {
                $inventaire_circonscriptions[$type_commutateur][$no_circ]["RATIO_DISPO"] = formatage_calcul_ratio($nb_dispo, $nb_total);     
            }                   
        }
    }

    return $inventaire_circonscriptions;
}

/**
 * Ajustement du nombre de chiffres après la virgule du calcul de ratio
 *  
 * @param integer $nb_dispo
 * @param integer $nb_total
 * @return float ajusté à 2 chiffres après la virgule 
 */
function formatage_calcul_ratio($nb_dispo, $nb_total){

    return number_format( ($nb_dispo / $nb_total) * 100, 2);
}

/**
 * Création des liens et nom de fichier Excel
 *  
 * @param array $array_Champs
 * @return array
 */
function creation_des_liens_fichiers_xlsx($array_Champs){
	date_default_timezone_set('America/New_York'); // Je dois mettre ça si je veux avoir la bonne heure et date dans mon entrée de data
	$date = date("Y-m-d_H-i-s");

    $nom_fichier = "";
    // On va créer un nom de fichier variable
    if ($array_Champs['type_fournisseur'] == FOURNISSEUR_VIDEOTRON){
        $nom_fichier = "liste_circonscriptions_ouvertes_VIDEOTRON_";

    } elseif ($array_Champs['type_fournisseur'] == FOURNISSEUR_FIZZ){
        $nom_fichier = "liste_circonscriptions_ouvertes_FIZZ_";
    }

	//$chemin_new_fichier = "C:\xampp\htdocs\911data\admin\RechercheInventaireTMO-VTL-FIZZ\$nom_fichier$date.xlsx";
	$chemin_new_fichier = "C:\Users\zmignaub\MonServeurDeveloppement\WebApps\VideotronWebServer\www\RechercheInventaireTMO-VTL-FIZZ\$nom_fichier$date.xlsx";
	$nom_fichier_courriel = $nom_fichier . $date . ".xlsx";

	$array_Champs['chemin_fichier'] = $chemin_new_fichier;
	$array_Champs['nom_fichier_courriel'] = $nom_fichier_courriel;

	return $array_Champs;
}

/**
 * Début de la construction du fichier Excel avec l'information collecter via Remedy
 * Fonction mère qui va appeller plusieurs sous fonctions, afin de mettre en place, les différents morceaux.
 * 
 * @param array $array_Champs
 * @return array
 */
function gestion_fichier_excel($array_Champs){

    // Étape 1 - Création de l'instance de l'objet Excel
    $objPHPExcel = creation_instance_objet_excel();

    // Étape 2 - Création de la structure du ficheir via l'incertion des colonnes
    $objPHPExcel = creation_structure_fichier_excel($objPHPExcel, $array_Champs['liste_colonnes']);

    // Étape 3 - Insertion des éléments dans le fichier
    $objPHPExcel = remplissage_fichier_excel($objPHPExcel, $array_Champs["inventaire_circonscriptions"], $array_Champs["liste_colonnes"], $array_Champs['type_fournisseur']);

    // Étape 4 - Sauvegarde de linstance de l'objet Excel en un fichier proprement dit.
    $array_Champs['chemin_fichier'] = creation_sauvegarde_fichier_excel($objPHPExcel, $array_Champs['chemin_fichier']);

    return $array_Champs;
}

/**
 * Étape 1 - Création de l'instance de l'objet Excel et la retourne @see gestion_fichier_excel
 * On va appeller dans la fonction, la librairie PHPExcel
 * 
 * @return object 
 */
function creation_instance_objet_excel(){

    /** Pour écrire des fichiers Excel */
	require_once dirname(__FILE__) . '/../PHPExcel-1.8/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();
	// IL NE FAUT PAS METTRE ACCENTS DANS LA DESCRIPTION DU FICHIER EXCEL !!!
	$objPHPExcel->getProperties()->setCreator("STR/Planififcation Telephonie")
								 ->setLastModifiedBy("STR/Planififcation Telephonie")
								 ->setTitle("Gestion Inventaire")
								 ->setSubject("liste de circonscriptions ouvertes par type LRN")
								 ->setKeywords("liste de circonscriptions")
								 ->setCategory("regrouper par LRN et circonscription")
                                 ->setDescription("Merci pour ce projet !");

    return $objPHPExcel;
}

/**
 * Étape 2 - Création de la structure du fichier. soit en activant la première feuille Excel
 * On détermine les colonnes nécessaires et l'enlignement. On retourne le tout @see gestion_fichier_excel
 * 
 * @param object $objPHPExcel -> Instance du fichier Excel
 * @param array $liste_colonnes
 * @return object 
 */
function creation_structure_fichier_excel($objPHPExcel, $liste_colonnes){

    // On active la première feuille Excel
	$objPHPExcel->setActiveSheetIndex(0);

    // On set un titre pour la feuille en cours
    $objPHPExcel->getActiveSheet()->setTitle('DATA');
	$colonne = 0; // Commence à 0
	$ligne = 1; // Commence à 1 et non à 0 et demmeurerra à 1 pour l'exercice de création de la structure

	// On va créer les colonnes nécessaire de manière dynamiquement et en fonction d'où on est rendu, centrer ça au centre ou à droite
	foreach($liste_colonnes as $key => $value) {

        // On commence par trouver la lettre correspondante à notre colonne en cours
        $colonne_en_lettre = conversion_colonne_chiffre_vers_lettre($key);

        // 1 à 4 inclusivement
        if ($key >=0 && $key < 4){

            $objPHPExcel->getActiveSheet()->getStyle($colonne_en_lettre)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
            // 5 à 8 inclusivement
        } elseif ($key >=4 && $key < 8){

            $objPHPExcel->getActiveSheet()->getStyle($colonne_en_lettre)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        }

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($colonne, $ligne, $value);
        $colonne++;
    }
    // Si on doit réajuster les titres de colonnes au centre, ca va être possible

    return $objPHPExcel;
}

/**
 * Étape 3 - Insertion des lignes que contient le tableau 2D par les switchs et circonscriptions
 * On détermine les colonnes nécessaires et l'enlignement. On retourne le tout @see gestion_fichier_excel
 * 
 * @param object $objPHPExcel -> Instance du fichier Excel
 * @param array $inventaire_circonscriptions
 * @param array $liste_colonnes
 * @return object 
 */
function remplissage_fichier_excel($objPHPExcel, $inventaire_circonscriptions, $liste_colonnes, $type_fournisseur){

    // Commence à la ligne 2, où on va saisir notre première combinaison
    $ligne = 2;

    // On va passer deux fois pour extraire chaque circonscription par type de switch, soit : 4G/LTE & VOLTE
	foreach ($inventaire_circonscriptions as $key_switch => $liste_circ_par_switch) {

		// On récupère la liste complète des circonscriptions pour chaque switch
		foreach ($liste_circ_par_switch as $liste_circonscriptions){

            // Pour cahque circonscription, on va en extraire la key et sa value pour être sur qu'on met les bonnes informations dans les bonnes colonnes.
            foreach ($liste_circonscriptions as $key_une_circ => $value_une_circ){

                // Pour chaque information de la circonscription en cours, on doit savoir où déposer l'information, dans quelle colonne dans le fichier Excel par rapport à l'ordre déterminée au départ.	
                foreach ($liste_colonnes as $key_type => $value_type) {
                      
                    // Dès qu'on a trouvé notre combinaison entre la colonne et la valeur dans le array correspondant, on peut procéder
                    if ($key_une_circ == $value_type && $key_une_circ !== "RESULT_DOUBLON_UTILISE"){	
                        
                        // On commence par trouver la lettre correspondante à notre colonne en cours
                        $colonne_en_lettre = conversion_colonne_chiffre_vers_lettre($key_type);
                        
                        // Si nous sommes dans la colonne des ratios, on va mettre un 2 chiffres après la virgule, à tout coup
                        if ($key_une_circ == "RATIO_DISPO"){                          
                            $objPHPExcel->getActiveSheet()->getStyle($colonne_en_lettre.$ligne)->getNumberFormat()->setFormatCode('0.00');
                        }

                        // On va remplir de couleurs seulement si nous sommes dans la section VOLTE et pour la colonne RATIO_DISPO ou colonne NB_Dispo Ou que nous sommes VIDEOTRON
                        if ( ($type_fournisseur == "VIDEOTRON" || $key_switch == "VOLTE") && ( $key_une_circ == "RATIO_DISPO" || $key_une_circ == "NB_DISPO") ){

                            // Si nous avons des valeurs en bas de 20 et 10, on doit remplir la cellule de rouge ou orange 
                            if ($value_une_circ < 20 && is_numeric($value_une_circ)){

                                // Information venant de ChatGPT
                                // Attention, préparation à un possible remplissage de couleur soit orange ou rouge
                                $objPHPExcel->getActiveSheet()->getStyle($colonne_en_lettre.$ligne)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);

                                // Section pour savoir quand on doit mettre en gras le nombre
                                // On doit mettre pour les deux scénarios, le texte en «gras»
                                if ($key_une_circ == "RATIO_DISPO" || ($value_une_circ == 0 && $key_une_circ == "NB_DISPO")){

                                    // Rappel que nous sommes dans la condition de 20 et moins
                                    $objPHPExcel->getActiveSheet()->getStyle($colonne_en_lettre.$ligne)->getFont()->setBold(true);
                                }

                                // Attention, il faut ajouter la mention pour quelle colonne on veut le remplissage de couleurs
                                // Maintenant, on détermine, s'il doit y avoir une couleur avertissement
                                if ($value_une_circ < 20 && $value_une_circ >= 10 && $key_une_circ == "RATIO_DISPO"){

                                    $objPHPExcel->getActiveSheet()->getStyle($colonne_en_lettre.$ligne)->getFill()->getStartColor()->setRGB('FFA500'); // Orange
                                } elseif ($value_une_circ < 10 && $key_une_circ == "RATIO_DISPO" ) {

                                    // Plus petit que 10, inclut la situation où il y aurait un nombre de dispo ou un nombre utilisé égal à 0
                                    $objPHPExcel->getActiveSheet()->getStyle($colonne_en_lettre.$ligne)->getFill()->getStartColor()->setRGB('FF0000'); // Rouge
                                } elseif ($value_une_circ == 0 && $key_une_circ == "NB_DISPO" ) {

                                    // Quand on est rendu à 0 de numéro dispo, on met ça en rouge
                                    $objPHPExcel->getActiveSheet()->getStyle($colonne_en_lettre.$ligne)->getFill()->getStartColor()->setRGB('FF0000'); // Rouge
                                }
                            }
                        }

                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key_type, $ligne, $value_une_circ);
                        break; // On a trouvé et inséré l'information, on passe à la prochaine colonne
                    }	
                }
            }

            // Une fois rendu ici, nous pouvons passer à la prochaine circonscription qui sera saisit sur la prochaine ligne
            $ligne++;
		}	
	}

    return $objPHPExcel;
}

/**
 * Étape 4 - Sauvegarde de l'instance de l'objet Excel en un fichier proprement dit
 * On détermine les colonnes nécessaires et l'enlignement. On retourne le tout @see gestion_fichier_excel
 * 
 * @param object $objPHPExcel -> Instance du fichier Excel
 * @param string $chemin_fichier
 * @return string 
 */
function creation_sauvegarde_fichier_excel($objPHPExcel, $chemin_fichier){

    // Création du fichier
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save(str_replace('.php', '.xlsx', $chemin_fichier));

    return $chemin_fichier;
}

/**
 * On envoi le courriel personnalisé en fonction de l'utitlisateur en cours d'utilisation
 * 
 * @param $potentielBD
 * @param string $courriel
 * @param string $nom_fichier_courriel
 * @return boolean 
 */
function envoie_courriel($potentielBD, $courriel, $nom_fichier_courriel){
	date_default_timezone_set('America/New_York');
	$reply_email = "AnalysePotentiel@videotron.com";
	$filename = $nom_fichier_courriel;  
	$path = 'http://gpg54z2/911data/admin/RechercheInventaireTMO-VTL-FIZZ';
	$file = $path . "/" . $filename;

    // Courriel vient des informations dans la session du l'usager
	$mailto = $courriel;
	$subject = "Voici le fichier avec le résultat obtenu !";

	// Information pour récupérer le prénom et nom du user utilisateur
	$id_user = $_SESSION['auth_user'];
	$request = "SELECT * FROM identite WHERE Numero = '$id_user'";
	$result_information = $potentielBD->query($request);
	$donnee_select = $result_information->fetch_array(MYSQLI_ASSOC);

	$message =  "
	<html>
		<body bgcolor=\"#D3D3D3\" topmargin=\"0\">
			<p>Bonjour {$donnee_select[prenom]} {$donnee_select[nom]} !</p>
			<p>Vous venez de recevoir un courriel, parce que vous avez utilisé l'outil «Information sur Inventaire TMO (VTL & FIZZ)».</p>
			<p>Accessible via notre intranet du potentiel (LIG) : <a href=\"http://gpg54z2/911data/index.php\">Notre Intranet</a></p>
			<p>Une fois connecter ! L'outil est disponible via ce lien : <a href=\"http://gpg54z2/911data/admin/RechercheInventaireTMO-VTL-FIZZ/index.php\">Notre Outil</a></p>
			<p align=\"left\">Bonne journée</p>
			<p align=\"middle\">L'Équipe d'AnalysePotentiel</p>
		</body>
	</html>
	";

	$message = str_replace("\n.", "\n..", $message); // Ajout d'une sécurité pour améliorer l'envoi de courriel sécurité - 13 Janvier 2020 

	$content = file_get_contents($file);
	$content = chunk_split(base64_encode($content));

	// a random hash will be necessary to send mixed content
	$separator = md5(time());

	// carriage return type (RFC)
	$eol = "\r\n";

	// main header (multipart mandatory)
	$headers = "From: " . $reply_email . $eol;
	$headers .= "MIME-Version: 1.0" . $eol;
	$headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
	$headers .= "Content-Transfer-Encoding: 8bit" . $eol;
	$headers .= "This is a MIME encoded message." . $eol;

	// message
	$body = "--" . $separator . $eol;
	$body .= "Content-Type: text/html; charset=\"ISO-88591\"" . $eol;
	$body .= "Content-Transfer-Encoding: 8bit" . $eol;
	$body .= $eol . $message . $eol . $eol;

	// attachment
	$body .= "--" . $separator . $eol;
	$body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . $eol;
	$body .= "Content-Transfer-Encoding: base64" . $eol;
	$body .= "Content-Disposition: attachment" . $eol;
	$body .= $eol . $content . $eol . $eol;
	$body .= "--" . $separator . "--";

	$succes = mail($mailto, $subject, $body, $headers);

	return $succes;
}

/**
 * On converti le numéro de la colonne en lettre pour ensuite le retourner @see remplissage_fichier_excel
 * 
 * @param integer $column
 * @return string 
 */
function conversion_colonne_chiffre_vers_lettre($column){

    $colonne_reajuste = $column + 1;
	$colonne_en_lettre = "";
	
    switch ($colonne_reajuste){
        case 1 : $colonne_en_lettre = "A"; break;
        case 2 : $colonne_en_lettre = "B"; break;
        case 3 : $colonne_en_lettre = "C"; break;
        case 4 : $colonne_en_lettre = "D"; break;
        case 5 : $colonne_en_lettre = "E"; break;
        case 6 : $colonne_en_lettre = "F"; break;
        case 7 : $colonne_en_lettre = "G"; break;
        case 8 : $colonne_en_lettre = "H"; break;
    }

	return $colonne_en_lettre;
}

/**
 * Une fois que tout est complété, on envoi le log de la statistique d'utilisation
 * 
 * @param $potentielBD
 * @param string $type_recherche -> Deux choix possible entre affichage ou affichage & courriel
 * @param string $type_fournisseur -> Deux choix possible entre VIDEOTRON ou FIZZ
 * @return void 
 */
function ajouter_recherche_dans_BD($potentielBD, $type_recherche, $type_fournisseur){

    // 3 Février 2020 - Je mets mes inserts dans mes différents projets anti-injection SQL - Benoit Mignault    
    date_default_timezone_set('America/New_York');
    $rightNow = date("Y-m-d H:i:s");
    $sql = "INSERT INTO statrecherche_inventaire_vtl_fizz (user, date, type_recherche, type_fournisseur) VALUES (?, ?, ?, ?)";
    $stmt = $potentielBD->prepare($sql);

    // Bind variables to the prepared statement as parameters
    $stmt->bind_param('ssss', $_SESSION['auth_user'], $rightNow, $type_recherche, $type_fournisseur);

    $stmt->execute();
    // Close statement
    $stmt->close();    
}

//if (!check_auth_user()) {
if (false){
?>
<script type="text/javascript">
    document.location.replace("../proteger.php");
</script>
<?php
} else {
    //$potentielBD = new mysqli("localhost", "analysepotentiel", "pot8877", "911_db");
    $potentielBD = new mysqli("GPG54Z2", "analysepotentiel", "pot8877", "911_db");
    $db_remedy_prod = odbc_connect("AR System ODBC Driver",'odbc_str','Srvstr22%');

    $array_Champs = initialisation();        
    $array_Champs = remplisage_champs($potentielBD, $array_Champs);    

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $array_Champs = validation_champs($array_Champs);
        $array_Champs = situation_erreur($array_Champs);

        // Si nous avons des messages d'erreurs, on va les faire afficher dans la section «Result»
        if (count($array_Champs['messages_erreurs']) == 0){

            // On commence par créer le tableau de board avec toutes les circonscriptions
            $array_Champs["inventaire_circonscriptions"] = requete_SQL_liste_circonscriptions_ouverte($potentielBD, $array_Champs['type_fournisseur']);

            // MAJ du nombre des numéros dispo par circonscription, par Switch            
            $array_Champs["inventaire_circonscriptions"] = requete_SQL_remedy($db_remedy_prod, $array_Champs['type_fournisseur'], $array_Champs["inventaire_circonscriptions"]);   

            // Calcul le nombre total de numéros par Switch et par Commutateur TMO            
            $array_Champs["inventaire_circonscriptions"] = construction_somme_dispo_utilise($array_Champs["inventaire_circonscriptions"]);     
            
            // Calcul le ratio du nombre # dispo par rapport au nombre total par Switch et par Commutateur TMO            
            $array_Champs["inventaire_circonscriptions"] = construction_ratio_dispo($array_Champs["inventaire_circonscriptions"]);     
            
            // Début de la section pour le choix du courriel (qui incluera le fichier Excel. En utilisant la variable prévu à cet effet
            if ($array_Champs['action_courriel']){

                // Création des liens du fichier Excel
                $array_Champs = creation_des_liens_fichiers_xlsx($array_Champs);

                // Création de la mécanique du fichier Excel
                $array_Champs = gestion_fichier_excel($array_Champs);

                // Création et envoi du courriel
                /*
				$result_email = envoie_courriel($potentielBD, $array_Champs["courriel"], $array_Champs['nom_fichier_courriel']);
				if (!$result_email){
					array_push($array_Champs['messages_erreurs'], "L'envoi du courriel a subit un problème technique/réseau. Veuillez contacter AnalysePotentiel !");
				}
				*/

				// On delete, le fichier quoi qu'il arrive, car on ne veut pas le laisser sur le réseau				
				// unlink($array_Champs['chemin_fichier']);
            }
            
            //ajouter_recherche_dans_BD($potentielBD, $array_Champs['type_recherche'], $array_Champs['type_fournisseur']);
            $array_Champs['maxId']++;
        }
        
    } // Fin POST

    odbc_close($db_remedy_prod); // Fermeture de la connexion Remedy
    $potentielBD->close(); // Fermeture de la connexion à la BD du potentiel
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link href="index.css" rel="stylesheet" type="text/css">
        <meta name="description" content="Outil pour cherche l'information sur un numéro téléphone">
        <title>Information sur Inventaire TMO</title>
        <!-- Icône de la page vient du site : http://172.26.82.60:15871/cgi-bin/blockpage.cgi?ws-session=18446744073570483181 -->
        <link rel="shortcut icon" href="inventaire.jpg">
        <!-- Ajout du charet ISO-88591 pour conserver les accents dans mes requêtes SQL --> 
        <meta name="http-equiv" content="Content-type: text/html; charset=ISO-88591"/>
    </head>

    <body>
        <div class="milieu">
            <div class="header">
                <div class="headerIMG">
                    <img src="logoVTL2018.png" alt="logo VIDEOTRON" title="VIDEOTRON !" />
                </div>
                <div class="headerIMG">
                    <img src="logo_str.jpg" alt="logo de notre département" title="STR !" />
                </div>
                <div class="titre">
                    <h1>Information sur Inventaire TMO (VTL & FIZZ)</h1>
                    <p id="hautPage"></p>
                </div>         
            </div>
            <div class="content">
                <div class="navBar">
                    <div class="menu">
                        <a href="../menu.php?section=applications" class="retour">Retour au menu</a>
                    </div>                    
                </div>
                <fieldset class="fieldRecherche">
                    <legend class="legendCenter">Section de recherche</legend>
                    <div class="section_info">
                        <div class="infoGauche">
                            <a class="faireAfficher" href="#">Procédure d'utilisation</a>
                            <h3>Requis</h3>
                            <ul class="lesInstruction">
                                <li>Sélectionner le fournisseur désiré entre Vidéotron & FIZZ.</li>
                                <li>Sélectionner la méthode d'extraction de l'information désirée.</li>
                                <li>Au fils des prochains mois, si nous procédons à l'ouverture de nouvelles circonscriptions TMO, <span class="pSimpleAvertissement">veuiller avertir Benoît.</span></li>
                            </ul>
                            <h3>Résultat</h3>
                            <ul class="lesInstruction">
                                <li class="type">La liste des circonscriptions et leur nombre de numéros Dispo & Utilisé par LRN
                                    <ul>
                                        <li>Le nombre de numéros «Disponible»</li>
                                        <li>Le nombre de numéros «Utilisé»</li>
                                        <li>Le nombre de numéros «Total»</li>
                                        <li>Le nombre de numéros «Ratio»</li>
                                    </ul>
                                </li>
                                <li>Explication des cases de couleurs :
                                    <ul>
                                        <li>Une couleur <span class="pAvertissement">Orange</span> dans la colonne «Ratio» est un avertissement que la valeur est sous la barre des 20%</li>
                                        <li>Une couleur <span class="pErreur">Rouge</span> dans la colonne «Ratio» est un avertissement sérieux que la valeur est sous la barre des 10%</li>
                                        <li>Une couleur <span class="pErreur">Rouge</span> dans la colonne «Dispo» est un avertissement pour dire qu'il ne reste plus de numéro disponible.</li>
                                    </ul>
                                </li>
                                <li class="dernier_type">Si vous exécutez l'outil pour FIZZ, le résultat arrivera environ <span class="pSimpleAvertissement">55 secondes,</span> après l'exécution de la recherche.</li>
                                <li>Si vous exécutez l'outil pour Vidéotron, le résultat arrivera environ <span class="pSimpleAvertissement">300 secondes,</span> après l'exécution de la recherche.</li>
                            </ul>						
                        </div>
                        <div class="infoDroite">
                            <p>Nombre de recherches » <span><?php echo $array_Champs['maxId']; ?></span> </p>
                        </div>
                    </div>
                    <div class="form">
                        <form id="formName" method="post" action="index.php">
                            <input id="visible_info" type="hidden" name="visible_info" value="<?php echo $array_Champs['visible_info']; ?>">
                            <input id="visible_courriel" type="hidden" name="visible_courriel" value="<?php echo $array_Champs['visible_courriel']; ?>">
                            <input id="courriel_par_default" type="hidden" name="courriel_par_default" value="<?php echo $array_Champs['courriel_par_default']; ?>">
                            <div class="section_critere">                                
                                <fieldset class="fieldRecherche">
                                    <legend class="legendCenter">Sélectionner votre fournisseur TMO</legend>   
                                        <div class="critere">
                                            <div class="radio">
                                                <input type="radio" <?php if ($array_Champs['fournisseur_vtl'] || $_SERVER['REQUEST_METHOD'] == 'GET') { echo "checked"; } ?> name="choix_fournisseur" id="fournisseur_vtl" value="fournisseur_vtl">
                                                <label for="fournisseur_vtl">Vidéotron</label>
                                            </div>
                                            <div class="radio">
                                                <input type="radio" <?php if ($array_Champs['fournisseur_fizz'] && $_SERVER['REQUEST_METHOD'] == 'POST') { echo "checked"; } ?> name="choix_fournisseur" id="fournisseur_fizz" value="fournisseur_fizz">
                                                <label for="fournisseur_fizz">FIZZ</label>
                                            </div>                                    
                                        </div>                                    
                                </fieldset>                            
                                <fieldset class="fieldRecherche">
                                    <legend class="legendCenter">Sélectionner la manière d'afficher l'information</legend>   
                                        <div class="critere">
                                            <div class="radio">
                                                <input type="radio" <?php if ($array_Champs['action_afficher'] || $_SERVER['REQUEST_METHOD'] == 'GET') { echo "checked"; } ?> name="choix_action" id="action_afficher" value="action_afficher">
                                                <label for="action_afficher">Afficher seulement</label>
                                            </div>
                                            <div class="radio">
                                                <input type="radio" <?php if ($array_Champs['action_courriel'] && $_SERVER['REQUEST_METHOD'] == 'POST') { echo "checked"; } ?> name="choix_action" id="action_courriel" value="action_courriel">
                                                <label for="action_courriel">Afficher & Courriel</label>
                                            </div>                                    
                                        </div>
                                        <div class="critere courriel">
                                            <label for="courriel">Saisissez votre courriel:</label>
                                            <input placeholder="exemple@videotron.com" maxlength="30" type="email" id="courriel" name="courriel" value="<?php echo $array_Champs['courriel']; ?>">
                                        </div>                                        
                                </fieldset>                                                       
                            </div>
                            <div class="recherche">
                                <input id="nouvelle_recher" type="submit" name="nouvelle_recher" value="Recherche...">
                                <input id="faire_menage_total" type="reset" value="Effacer...">
                                <input id="info" alt="icone" name="info" type="image" src="info.jpg">
                                <!-- https://pixabay.com/en/info-information-button-icon-back-1426806/ -->
                            </div>
                        </form>
                    </div>					
                </fieldset>
            </div>
            <div class="result">
                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($array_Champs['messages_erreurs']) == 0 ) { ?>
                <fieldset>
                    <legend class="legendCenter">Inventaire</legend>
                    <div class="sous_result">
                        <fieldset>
                            <legend class="legendCenter"><?php echo $array_Champs['type_fournisseur']; ?></legend>                     
                            <div class="div_tableau">
                                <table class="affichage">
                                    <thead>
                                        <tr>
                                            <th>No Circ.</th>
                                            <th>Circonscription</th>
                                            <th>Province</th>
                                            <th>Commutateur</th>
                                            <th>Dispo</th>
                                            <th>Utilisé</th>
                                            <th>Total</th>
                                            <th>Ratio Dispo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($array_Champs["inventaire_circonscriptions"] as $liste_switch) { ?>
                                            <?php foreach($liste_switch as $une_circ) { 
                                                $valeur_numerique = floatval($une_circ['RATIO_DISPO']); ?>                                                                                            
                                                <tr>                                                                                        
                                                    <td><?php echo $une_circ['NO_CIRC']; ?></td>
                                                    <td><?php echo $une_circ['NOM_CIRC']; ?></td>
                                                    <td><?php echo $une_circ['CODE_PROVINCE']; ?></td>
                                                    <td><?php echo $une_circ['SWITCH']; ?></td>

                                                    <?php if ( ($une_circ['SWITCH'] == "VOLTE" || $array_Champs['fournisseur_vtl']) && $une_circ['NB_DISPO'] == 0){ ?> 
                                                        <td class='pErreur'>
                                                    <?php } else { echo "<td>"; } ?>
                                                    <?php echo $une_circ['NB_DISPO']; ?></td>                                                        
                                                    <td><?php if ($une_circ['NB_UTILISE'] == 250000) { echo "<b>Over 250k</b>"; } else { echo $une_circ['NB_UTILISE']; } ?></td>
                                                    <td><?php echo $une_circ['NB_TOTAL']; ?></td>
                                                    <?php if ( ($array_Champs['fournisseur_vtl'] || $une_circ['SWITCH'] == "VOLTE" ) && $valeur_numerique > floatval(10) && $valeur_numerique < floatval(20)){ ?> 
                                                        <td class='pAvertissement'>
                                                    <?php } elseif ($une_circ['SWITCH'] == "VOLTE" && $valeur_numerique < floatval(10)){ echo "<td class='pErreur'>" ?>                                                        
                                                    <?php } else { echo "<td>"; } ?>
                                                    <?php echo $une_circ['RATIO_DISPO'] . "%"; ?></td>
                                                </tr> 
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>                            
                        </fieldset>
                    </div>
                </fieldset> 
                <a class="ancre" href="#hautPage">Haut de la page</a>
                <?php } else { ?>
                    <?php foreach($array_Champs['messages_erreurs'] as $liste_msg_err) { ?>
                        <p class="pErreur"><?php echo $liste_msg_err; ?></p>
                    <?php } ?>
                <?php } ?>
            </div>                     
        </div>        
        <div id="dialog" title="Le pourquoi de l'outil :">
            <p>Mon collègue Maxime m'a demandé de concevoir une vue sur l'inventaire en temps réel de FIZZ.</p>
            <p>Ensuite, mon autre collègue, Jean-Sacha m'a demandé de faire la même chose, mais pour Vidéotron TMO.</p>
            <p>Cet l'outil, permettra agir plus rapidement, dans l'éventualité que le nombre de numéros «Disponible» tombent en bas d'un certain ratio.</p>
            <p>Un tableau sera affiché et un courriel sera envoyé, si nécessaire.</p>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="index.js"></script>
    </body>
</html>
<?php } ?>