<?php
	// Les includes nécessaires
	use JetBrains\PhpStorm\NoReturn;
	
	include_once("../../../traduction/traduction-gestion-stats.php");
	include_once("../../../includes/fct-connexion-bd.php");
	include_once("../../../includes/fct-login-poker-gestion.php");
	include_once("../../../includes/fct-divers.php");
	
	/**
	 * Fonction qui va contenir tous ce dont on aura besoin.
	 * Une partie des variables de type string ou int et une autre partie en boolean
	 * On va ajouter_stats un array pour les mots traduits ou non
	 *
	 * @return array
	 */
	function initialisation(): array {
		
		return array("user" => "", "user_valid" => false, "id_user" => 0, "type_langue" => "", "joueur" => "", "gain" => "", "position" => "",
                     "no_tournois" => "", "date" => "", "killer" => "", "citron" => "", "new_player" => "", "liste_situation" => array(), 
					 "new_player_adder" => false, "players_stats_adder" => false, 
                     "invalid_gain" => false, "invalid_new_player" => false, "invalid_no_tournois" => false, "invalid_date" => false, "invalid_citron" => false,
                     "invalid_killer" => false, "tous_invalids" => false, "tous_champs_vides" => false, "invalid_language" => false,
					 "champ_joueur_vide" => false, "champ_position_vide" => false, "champ_gain_vide" => false, "champ_no_tournois_vide" => false, 
                     "champ_date_vide" => false, "champ_killer_vide" => false, "champ_citron_vide" => false, "champ_new_player_vide" => false,
					 "new_player_duplicate" => false, "erreur_presente" => false,
                     "liste_mots" => array("liste_messages" => array()), "liste_joueurs" => array(), );
	}
	
	/**
	 * Fonction pour setter les premières informations du GET ou POST
	 * Récupérer la liste des joueurs
	 *
     * @param mysqli $connMYSQL -> connexion aux tables de benoitmignault.ca
	 * @param array $array_Champs
	 * @return array
	 */
	function remplissage_champs(mysqli $connMYSQL, array $array_Champs): array{
        
		// Assignation seulement de la langue pour utilisation de traduction et la variable que le user est toujours valide
        $array_Champs["type_langue"] = $_SESSION['type_langue'];
		
        // Ajout du champ pour permettre l'utilisation de la fct commune
		$array_Champs['user'] = $_SESSION['user'];
		
		// Remplissage de la liste de joueurs disponibles pour assignation des statistiques + d'autres variables
		$array_Champs = requete_SQL_recuperation_liste_joueurs($connMYSQL, $array_Champs);
        
        // Nous avons seulement le POST, rendu ici
	    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		
            // Lorsque nous avons un nouveau joueur
            if (isset($_POST['btn_new_player'])) {
	
	            if (isset($_POST['new_player'])) {
		            $array_Champs["new_player"] = $_POST['new_player'];
	            }
                
                // Lorsqu'on veut ajouter les statistiques pour un joueur
            } elseif (isset($_POST['btn_add_stat'])) {
       
                if (isset($_POST['liste_joueurs'])) {
                    $array_Champs["joueur"] = $_POST['liste_joueurs'];
                }
                
                if (isset($_POST['position'])) {
                    $array_Champs["position"] = $_POST['position'];
                }
                
                if (isset($_POST['gain'])) {
                    $array_Champs["gain"] = $_POST['gain'];
                }
                
                if (isset($_POST['no_tournois'])) {
                    $array_Champs["no_tournois"] = $_POST['no_tournois'];
                }
                
                if (isset($_POST['date'])) {
                    $array_Champs["date"] = $_POST['date'];
                }
                
                if (isset($_POST['killer'])) {
                    $array_Champs["killer"] = $_POST['killer'];
                }
                
                if (isset($_POST['citron'])) {
                    $array_Champs["citron"] = $_POST['citron'];
                }
            }
        }
        
		// Exceptionnellement, on va faire une validation ici
		// Validation commune pour le Get & Post, à propos de la langue
		if ($array_Champs["type_langue"] != "francais" && $array_Champs["type_langue"] != "english"){
			$array_Champs["invalid_language"] = true;
		}
		
		return $array_Champs;
	}
	
	/**
	 * Fonction qui sera appelée via la fct @see remplissage_champs
     * Construction et exécution de la requête SQL pour la liste des joueurs
     *
	 * @param mysqli $connMYSQL
	 * @param array $array_Champs
	 * @return array
	 */
	function requete_SQL_recuperation_liste_joueurs(mysqli $connMYSQL, array $array_Champs): array{
		
		$select = "SELECT JOUEUR ";
		$from = "FROM joueur ";
        $orderby = "ORDER BY joueur";
        
		// Préparation de la requête SQL avec les parties nécessaires
		$query = $select . $from . $orderby;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		
        /* Exécution de la requête */
		$stmt->execute();
		
		/* Association des variables de résultat */
		$result = $stmt->get_result();
		
		// Close statement
		$stmt->close();
		
		$array_Champs["liste_joueurs"] = recuperation_liste_joueurs($connMYSQL, $result);
  
		return $array_Champs;
    }
		
	/**
	 * Fonction pour récupérer la liste de tous les joueurs ayant participer aux différents tournois de poker
	 * L'information sera retournée @see requete_SQL_recuperation_liste_joueurs
	 *
	 * @param mysqli $connMYSQL -> Doit être présent pour utiliser les notions de MYSQLi
	 * @param object $result -> Le résultat de la requête SQL via la table « joueur »
	 * @return array
	 */
	function recuperation_liste_joueurs(mysqli $connMYSQL, object $result): array {
		
        $liste_joueurs = array();
        
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            
            // Ajout de chaque joueur
			$liste_joueurs[] = $row["JOUEUR"];
		}
        
		return $liste_joueurs;
    }
	
	/**
     * Contrairement à plusieurs autres de mes pages web, ici, il y a deux scénarios de gestions d'erreurs
     * Lorsqu'on ajout les statistiques d'un joueur
     * Lorsqu'on ajoute un nouveau joueur pour ses statistiques
     * 
	 * @param mysqli $connMYSQL
	 * @param array $array_Champs
	 * @return array
	 */
	function validation_champs(mysqli $connMYSQL, array $array_Champs): array {
	
	    // Les validations au moment d'ajouter un nouveau joueur à la liste déjà présente
        if (isset($_POST['btn_new_player'])) {
	        
            // Le seul champ qui sera vérifié pour l'ajout d'un nouveau joueur
	        if (empty($array_Champs['new_player'])) {
		        $array_Champs['champ_new_player_vide'] = true;
          
	        } else {
                $longueur_new_player = strlen($array_Champs['new_player']);
		        $pattern_new_player = "#^[A-Z][a-z]+(-[A-Z])?[a-z]+(\s[A-Z][a-z]?)?$#";
          
		        if (!preg_match($pattern_new_player, $array_Champs['new_player']) && $longueur_new_player > 25) {
			        $array_Champs['invalid_new_player'] = true;
			
		        } else {
                    // Comme le prénom du nouveau joueur est valide, on peut aller le vérifier dans notre table des joueurs
                    $array_Champs['new_player_duplicate'] = requete_SQL_verification_joueur($connMYSQL, $array_Champs["new_player"]);
		        }
	        }
            
            // Les validations pour les statistiques d'un joueur
        } elseif (isset($_POST['btn_add_stat'])) {
	
	        if (empty($array_Champs['joueur']) && empty($array_Champs['position']) &&
	            empty($array_Champs['gain']) && $array_Champs['gain'] !== 0 &&
	            empty($array_Champs['citron']) && $array_Champs['citron'] !== 0 &&
		        empty($array_Champs['killer']) && $array_Champs['killer'] !== 0 &&
                empty($array_Champs['no_tournois']) && $array_Champs['no_tournois'] !== 0 && empty($array_Champs['date'])) {
                
                $array_Champs['tous_champs_vides'] = true;
	        } else {
                
                // sous condition propre à chaque champ
		        if (empty($array_Champs['joueur'])) {
			        $array_Champs['champ_joueur_vide'] = true;
		        }
          
		        if (empty($array_Champs['position'])) {
			        $array_Champs['champ_position_vide'] = true;
		        }
          
		        if (empty($array_Champs['no_tournois'])) {
			        $array_Champs['champ_no_tournois_vide'] = true;
		        } else {
                    // Conversion string -> INT
			        $array_Champs['no_tournois'] = intval($array_Champs['no_tournois']);
                }
          
		        if (empty($array_Champs['killer']) && $array_Champs['killer'] !== 0) {
			        $array_Champs['champ_killer_vide'] = true;
		        } else {
			        // Conversion string -> INT
			        $array_Champs['killer'] = intval($array_Champs['killer']);
		        }
                
                if (empty($array_Champs['gain']) && $array_Champs['gain'] !== 0) {
			        $array_Champs['champ_gain_vide'] = true;
                } else {
	                // Conversion string -> INT
	                $array_Champs['gain'] = intval($array_Champs['gain']);
                }
                
                if (empty($array_Champs['citron']) && $array_Champs['citron'] !== 0) {
			        $array_Champs['champ_citron_vide'] = true;
                } else {
	                // Conversion string -> INT
	                $array_Champs['citron'] = intval($array_Champs['citron']);
                }
                
                if (empty($array_Champs['date'])) {
			        $array_Champs['champ_date_vide'] = true;
                }
		
		        $pattern_gain = "#^-?[0-9]{1,3}$#";
		        $pattern_no = "#^[0-9]{1,3}$#";
                /**
		         * Il y a 3 situations qui peuvent arriver selon les exigences :
		         *
		         * Le mois de févier (peu importe l'année) compte 28 jours.
		         * Les mois de janvier, mars, mai, juillet, août, octobre, décembre comptent 31 jours.
		         * Les mois d'avril, juin, septembre, novembre comptent 30 jours.
		         */
		        $pattern_date = "#^[1-9][0-9]{3}-((02-(0[1-9]|1[0-9]|2[0-8]))|((0[13578]|1[02])-(0[1-9]|[1-2][0-9]|3[0-1]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$#";
		
		        // Changement du pattern pour les prix killer et citron pour avoir 2 chiffres après les décimales
		        $pattern_killer_citron = "#^[0-9](.[0-9]{1,2})?$#";
		
		        if (!preg_match($pattern_gain, $array_Champs['gain'])) {
			        $array_Champs['invalid_gain'] = true;
		        }
          
		        if (!preg_match($pattern_no, $array_Champs['no_tournois'])) {
			        $array_Champs['invalid_no_tournois'] = true;
		        }
          
		        if (!preg_match($pattern_date, $array_Champs['date'])) {
			        $array_Champs['invalid_date'] = true;
		        }
          
		        if (!preg_match($pattern_killer_citron, $array_Champs['killer'])) {
			        $array_Champs['invalid_killer'] = true;
		        }
          
		        if (!preg_match($pattern_killer_citron, $array_Champs['citron'])) {
			        $array_Champs['invalid_citron'] = true;
		        }
	        }
        }
  
		$array_Champs['erreur_presente'] = verification_valeur_controle($array_Champs);
  
		return $array_Champs;
	}
	
	/**
	 * Fonction pour aller vérifier si le joueur que nous voulons ajouter existe ou pas
	 * Cette fonction sera utilisé via la fonction @see validation_champs
	 * @param mysqli $connMYSQL -> connexion aux tables de benoitmignault.ca
	 * @param string $new_player
	 * @return bool
	 */
	function requete_SQL_verification_joueur(mysqli $connMYSQL, string $new_player): bool {
		
		// Comme je fais un SELECT, je ne ferai plus de try / catch
        $new_player_duplicate = false;
        
		$select = "SELECT JOUEUR ";
		$from = "FROM joueur ";
		$where = "WHERE joueur = ?";
		
		// Préparation de la requête SQL avec les parties nécessaires
		$query = $select . $from . $where;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		
        /* Lecture des marqueurs */
        $stmt->bind_param("s", $new_player);
        
        /* Exécution de la requête */
        $stmt->execute();
        
		/* Association des variables de résultat */
		$result = $stmt->get_result();
		
		if ($result->num_rows > 0) {
			$new_player_duplicate = true;
        }
		
		// Close statement
		$stmt->close();
        
        return $new_player_duplicate;
    }
		
	/**
     * Fonction pour trouver toutes les situations d'erreurs ou de messages de succès
     *
	 * @param array $array_Champs
	 * @return array
	 */
	function situation_erreur(array $array_Champs): array {
		
		$liste_situation = array();
		// On appel la bonne fonction en fonction du bouton choisi
		if (isset($_POST['btn_add_stat'])){
		
            // Ajout des stats est un succès
            if ($array_Champs['players_stats_adder']){
	            $liste_situation[] = 1;
             
            } else {
                
                // Tous les champs sont vides
                if ($array_Champs['tous_champs_vides']){
	                $liste_situation[] = 3;
                } else {
                    
                    // Une seule situation pour le menu des joueurs
                    if ($array_Champs['champ_joueur_vide']){
	                    $liste_situation[] = 4;
                    }
                    
                    // Une seule situation pour le choix de positions
                    if ($array_Champs['champ_position_vide']){
	                    $liste_situation[] = 5;
                    }
	
                    // Champ pour le gain
                    if ($array_Champs['champ_gain_vide']){
	                    $liste_situation[] = 6;
                     
                    } elseif ($array_Champs['invalid_gain']){
	                    $liste_situation[] = 7;
                    }
                    
                    // Champ No Tournois
	                if ($array_Champs['champ_no_tournois_vide']){
		                $liste_situation[] = 8;
		
	                } elseif ($array_Champs['invalid_no_tournois']){
		                $liste_situation[] = 9;
	                }
                    
                    // Champ Date
	                if ($array_Champs['champ_date_vide']){
		                $liste_situation[] = 10;
		
	                } elseif ($array_Champs['invalid_date']){
		                $liste_situation[] = 11;
	                }
	
	                // Champ Killer
	                if ($array_Champs['champ_killer_vide']){
		                $liste_situation[] = 12;
		
	                } elseif ($array_Champs['invalid_killer']){
		                $liste_situation[] = 13;
	                }
	
	                // Champ Citron
	                if ($array_Champs['champ_citron_vide']){
		                $liste_situation[] = 14;
		
	                } elseif ($array_Champs['invalid_citron']){
		                $liste_situation[] = 15;
	                }
                }
            }
		
		} elseif (isset($_POST['btn_new_player'])){
			
            // Ajout du joueur est un succès
			if ($array_Champs['new_player_adder']){
				$liste_situation[] = 2;
				
			} else {
				
				// Champ pour le nouveau joueur
				if ($array_Champs['champ_new_player_vide']){
					$liste_situation[] = 16;
					
				} elseif ($array_Champs['invalid_new_player']){
					$liste_situation[] = 17;
     
				} elseif ($array_Champs['new_player_duplicate']){
					$liste_situation[] = 18;
				}
			}
		}
		
		return $liste_situation;
	}
	
	// TODO regarder ca à la fin
	function ajout_Stat_Joueur($connMYSQL, $array_Champs) {
		
		$victoire = "";
		$fini2e = "";
		if ($array_Champs["position"] === "victoire") {
			$victoire = "X";
		}
        elseif ($array_Champs["position"] === "fini2e") {
			$fini2e = "X";
		}
		$killerFloat = floatval($array_Champs["killer"]);
		$citronFloat = floatval($array_Champs["citron"]);
		
		// Prepare an insert statement
		$sql = "INSERT INTO poker (joueur,gain,victoire,fini_2e,id_tournoi,date,killer,prixCitron) VALUES (?,?,?,?,?,?,?,?)";
		$stmt = $connMYSQL->prepare($sql);
		
		// Bind variables to the prepared statement as parameters
		$stmt->bind_param('sissisdd', $array_Champs["liste_joueurs"], $array_Champs["gain"], $victoire, $fini2e, $array_Champs["no_tournois"], $array_Champs["date"], $killerFloat, $citronFloat);
		$stmt->execute();
		
		// Close statement
		$stmt->close();
		
		if ($array_Champs['type_langue'] === "francais") {
			$messageAjout = "Les informations du joueur {$array_Champs["liste_joueurs"]} a été ajouté à la BD.";
		}
        elseif ($array_Champs['type_langue'] === "english") {
			$messageAjout = "The player information {$array_Champs["liste_joueurs"]} has been added to the BD.";
		}
		$array_Champs = initialisationChamps();
		$array_Champs['message'] = $messageAjout;
		return $array_Champs;
	}
	
	// TODO regarder ca à la fin
	function ajouter_Nouveau_Joueur($connMYSQL, $array_Champs) {
		
		// Prepare an insert statement
		$sql = "INSERT INTO joueur (joueur) VALUES (?)";
		$stmt = $connMYSQL->prepare($sql);
		
		// Bind variables to the prepared statement as parameters
		$stmt->bind_param('s', $array_Champs["new_player"]);
		$stmt->execute();
		
		// Close statement
		$stmt->close();
		
		if ($array_Champs['type_langue'] === "francais") {
			$messageAjout = "Le nouveau joueur {$array_Champs["liste_joueurs"]} a été ajouté à la BD.";
		}
        elseif ($array_Champs['type_langue'] === "english") {
			$messageAjout = "The player information {$array_Champs["liste_joueurs"]} has been added to the BD.";
		}
		$array_Champs = initialisationChamps();
		$array_Champs['message'] = $messageAjout;
		return $array_Champs;
	}
	
	
	/**
	 * Fonction pour rediriger le user vers la page web des statistiques et
     * les cookies ont déjà été setter
	 *
	 * @return void
	 */
	#[NoReturn] function connexion_user(): void {
        
        header("Location: /login-user/poker-stats/show-stats/stats.php");
        
		exit;
	}
	
	/**
	 * Fonction pour rediriger vers la bonne page extérieur à la page de gestion, sauf si
     * la variable $invalid_language est true
     * Pour le transfert vers la page de statistique, on va passer par @see connexion_user
     *
	 * @param mysqli $connMYSQL
	 * @param string $user
	 * @param string $type_langue
	 * @param bool $invalid_language
     * @param bool $user_invalid
	 * @return void
	 */
	#[NoReturn] function redirection(mysqli $connMYSQL, string $user, string $type_langue, bool $invalid_language, bool $user_invalid): void {
		
		if ($invalid_language || $user_invalid) {
            // Exceptionnellement, il faut aller récupérer d'urgence la valeur de user dans le input hidden qu'on a sauvegardé
            // Au cas où, la session serait terminée, dans le but de nettoyer le token inutile en BD
            
            // Nous avons seulement le POST, rendu ici
            if ($_SERVER['REQUEST_METHOD'] === 'GET'){
                // On s'assure par principe que la variable existe, même si on sait qu'elle existe à 100%
	            if (isset($_GET['user'])) {
		            $user = $_GET['user'];
	            }
            
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	            // On s'assure par principe que la variable existe, même si on sait qu'elle existe à 100%
	            if (isset($_POST['user'])) {
		            $user = $_POST['user'];
	            }
            }
   
			header("Location: /erreur/erreur.php");
			// Sinon, nous sommes sûr à 100%, que nous arrivons dans le POST
		} else {
            
            // Si on revient à la page accueil du site web
			if (isset($_POST['btn_return'])) {
				
				// En fonction de la langue
				if ($type_langue === 'english') {
     
					header("Location: /english/english.html");
				} elseif ($type_langue === 'francais') {
     
					header("Location: /index.html");
				}
                
                // Si on décide de revenir à la page de connexion des users
			} elseif (isset($_POST['btn_login'])) {
				
				// En fonction de la langue
				if ($type_langue === 'english') {
					header("Location: /login-user/login-user.php?langue=english");
				} elseif ($type_langue === 'francais') {
					header("Location: /login-user/login-user.php?langue=francais");
				}
			}
		}
        
        // Avant de détruire la session, on va killer le token
		requete_SQL_delete_token_session($connMYSQL, $user);
		delete_Session();
  
		exit; // Pour arrêter l'exécution du code php
	}
	
	/**
     * Fonction pour détruire les variables sessions et cookies lorsqu'il est nécessaire de le faire par une action de l'utilisateur
	 * @return void
	 */
	function delete_Session(): void {
		
        // https://www.php.net/manual/en/function.session-destroy.php
			
		// Unset all of the session variables.
		$_SESSION = array();
		
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 3600,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		
		// Finally, destroy the session.
		session_destroy();
	}
 
	// Les fonctions communes avant la validation du user
	session_start();
	$connMYSQL = connexion();
	$array_Champs = initialisation();
	$array_Champs['user_valid'] = verif_user_session_valide();
    
    // On s'assure que la session et cookie soit valide avant aller plus loin.
    if ($array_Champs['user_valid']){
	
	    $array_Champs = requete_SQL_verif_user_valide($connMYSQL, $array_Champs, $_SESSION['token_session']);
        if ($array_Champs['user_valid']){
         
	        // On va remplir les variables nécessaires ici
	        $array_Champs = remplissage_champs($connMYSQL, $array_Champs);
	
	        // La seule chose qui peut arriver dans le GET et au début du POST, ici est une variable de langue invalide
	        if ($array_Champs["invalid_language"]) {
		        redirection($connMYSQL, $array_Champs["user"], $array_Champs["type_langue"], $array_Champs["invalid_language"], false);
	        }
	
	        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		
		        // Si on veut sortir de la page pour revenir en arrière
		        if (isset($_POST['btn_login']) || isset($_POST['btn_return'])) {
			        redirection($connMYSQL, $array_Champs["user"], $array_Champs["type_langue"], $array_Champs["invalid_language"], false);
			
			        // Sinon on veut peut-être aller voir les statistiques de poker
		        } elseif (isset($_POST['btn_voir_stats'])) {
			
			        $array_Champs = requete_SQL_ajout_log_connexion($connMYSQL, $array_Champs);
			        // Maintenant, on peut connecter le user à la page de statistiques
			        connexion_user();
			
		        } elseif (isset($_POST['btn_add_stat']) || isset($_POST['btn_new_player'])) {
			
			        // Maintenant, on va faire nos vérifications sur nos champs
			        $array_Champs = validation_champs($connMYSQL, $array_Champs);
			
			        // On vérifie que nous n'avons pas d'erreur dans les validations
			        if (!$array_Champs['erreur_presente']){
				
				        // On appel la bonne fonction en fonction du bouton choisi
				        if (isset($_POST['btn_add_stat'])){
					        //$array_Champs = ajout_Stat_Joueur($connMYSQL, $array_Champs);
					
				        } elseif (isset($_POST['btn_new_player'])){
					        //$array_Champs = ajouter_Nouveau_Joueur($connMYSQL, $array_Champs);
				        }
			        }
			        // On va devoir faire une fonction de remise à NULL, certaines variables
		        }
		
		        
		        $array_Champs["situation"] = situation_erreur($array_Champs);
	        }
         
	        // On va faire la traduction, à la fin des GET & POST
	        // La variable de situation est encore à 0 pour le GET, donc aucun message
	        $array_Champs["liste_mots"] = traduction($array_Champs["type_langue"], $array_Champs["situation"]);
        }
    }
    
    // Validation finalement, car si un des deux premiers IF est fausse, on va arriver ici, avant tout le reste...
    if (!$array_Champs['user_valid']) {
     
	    redirection($connMYSQL, $array_Champs["user"], $array_Champs["type_langue"], $array_Champs["invalid_language"], true);
    }
	
	//var_dump($array_Champs['user']); exit;
    
	$connMYSQL->close();
?>
<!DOCTYPE html>
<html lang="<?php echo $array_Champs["liste_mots"]['lang']; ?>">
    <head>
        <meta charset="utf-8">
        <!-- https://pixabay.com/fr/fichier-ic%C3%B4ne-web-document-2389211/ -->
        <link rel="shortcut icon" href="gestion-stats-icone.png">
        <meta name="description" content="Gestion des statistiques de poker">
        <link rel="stylesheet" type="text/css" href="gestion-stats.css">
        <link rel="stylesheet" type="text/css" href="date.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $array_Champs["liste_mots"]['title']; ?></title>
        <style>
            body {
                margin: 0;
                /* https://pixabay.com/fr/cha%C3%AEne-de-blocs-personnels-2850276/ sous licence libre */
                background-image: url("gestion-stats-background.jpg");
                background-position: center;
                background-attachment: fixed;
                background-size: 100%;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <p class='titre'><?php echo $array_Champs["liste_mots"]['p1']; ?></p>
            <p class='titre'><?php echo $array_Champs["liste_mots"]['p2']; ?></p>
            <form method="post" action="gestion-stats.php" id="form">
                <input type="hidden" name="user" id="user" value="<?php echo $array_Champs['user']; ?>">
                <div class='formulaire-joueur'>
                    <div class="joueur <?php if ($array_Champs['tous_champs_vides'] || $array_Champs["champ_joueur_vide"]) { echo "erreur"; } ?>">
                        <label for="liste-joueurs"><?php echo $array_Champs["liste_mots"]['joueur']; ?></label>
                        <select id="liste-joueurs" name="liste_joueurs">
                            <option value=""><?php echo $array_Champs["liste_mots"]['option']; ?></option>
                            <?php foreach ($array_Champs["liste_joueurs"] as $un_joueur) {
                                    if ($array_Champs["joueur"] === $un_joueur) { ?>
                                        <option value="<?php echo $un_joueur; ?>" selected><?php echo $un_joueur; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $un_joueur; ?>"><?php echo $un_joueur; ?></option>
                                    <?php } } ?>
                        </select>
                    </div>
                    <div class="position <?php if ($array_Champs['tous_champs_vides'] || $array_Champs["champ_position_vide"]) { echo "erreur-choix"; } ?>"">
                        <p class="p-label-pos"><?php echo $array_Champs["liste_mots"]['resultat']; ?></p>
                        <div>
                            <label for="victoire"><?php echo $array_Champs["liste_mots"]['victoire']; ?></label>
                            <input type="radio" <?php if ($array_Champs['position'] === "victoire") { echo "checked"; } ?>
                                   name="position" id="victoire" value="victoire">
                        </div>
                        <div>
                            <label for="fini2e"><?php echo $array_Champs["liste_mots"]['fini2e']; ?></label>
                            <input type="radio" <?php if ($array_Champs['position'] === "fini2e") { echo "checked"; } ?>
                                   name="position" id="fini2e" value="fini2e">
                        </div>
                        <div>
                            <label for="autre"><?php echo $array_Champs["liste_mots"]['autre']; ?></label>
                            <input type="radio" <?php if ($array_Champs['position'] === "autre") { echo "checked"; } ?>
                                   name="position" id="autre" value="autre">
                        </div>
                    </div>
                    <div class="gain <?php if ($array_Champs['tous_champs_vides'] || $array_Champs["champ_gain_vide"] || $array_Champs["invalid_gain"]) { echo "erreur";} ?>">
                        <label for="gain"><?php echo $array_Champs["liste_mots"]['gain']; ?></label>
                        <input maxlength="4" type="text" id="gain" name="gain" value="<?php echo $array_Champs['gain'] ?>">
                    </div>
                    <div class="numero <?php if ($array_Champs['tous_champs_vides'] || $array_Champs["champ_no_tournois_vide"] || $array_Champs["invalid_no_tournois"]) { echo "erreur"; } ?>">
                        <label for="tournois"><?php echo $array_Champs["liste_mots"]['no_tournois']; ?></label>
                        <input maxlength="4" type="text" id="tournois" name="no_tournois" value="<?php echo $array_Champs['no_tournois'] ?>">
                    </div>
                    <div class="date <?php if ($array_Champs['tous_champs_vides'] || $array_Champs["champ_date_vide"] || $array_Champs["invalid_date"]) { echo "erreur-date"; } ?>">
                        <div class="form-row animate-2">
                            <label for="date">Date :</label>
                            <input type="date" id="date" value="<?php echo $array_Champs['date'] ?>" name="date" data-date='{"startView": 2, "openOnMouseFocus": true}'>
                        </div>
                    </div>
                    <div class="killer <?php if ($array_Champs['tous_champs_vides'] || $array_Champs["champ_killer_vide"] || $array_Champs["invalid_killer"]) { echo "erreur"; } ?>">
                        <label for="killer"><?php echo $array_Champs["liste_mots"]['killer']; ?></label>
                        <input maxlength="4" type="text" id="killer" name="killer" value="<?php echo $array_Champs['killer'] ?>">
                    </div>
                    <div class="citron <?php if ($array_Champs['tous_champs_vides'] || $array_Champs["champ_citron_vide"] || $array_Champs["champ_gain_vide"]) { echo "erreur"; } ?>">
                        <label for="citron"><?php echo $array_Champs["liste_mots"]['citron']; ?></label>
                        <input maxlength="4" type="text" id="citron" name="citron" value="<?php echo $array_Champs['citron'] ?>">
                    </div>
                    <div class="bas-formulaire">
                        <input class="bouton" type="submit" name="btn_add_stat" value="<?php echo $array_Champs["liste_mots"]['btn_add_stat']; ?>">
                        <input class="bouton" id="faire-menage-total" type="reset" value="<?php echo $array_Champs["liste_mots"]['btn_erase']; ?>">
                    </div>
                    <div class="bas-formulaire">
                        <p class="<?php if (true) { echo "avert"; } else { echo "erreur"; } ?>"> <?php echo $array_Champs['message']; ?> </p>
                    </div>
                </div>
            </form>
            <div class="formulaire-nouveau">
                <div class="<?php if ($array_Champs["champ_new_player_vide"] || $array_Champs["invalid_new_player"] || $array_Champs["new_player_duplicate"]) { echo "erreur"; } ?>">
                    <label for="new-player"><?php echo $array_Champs["liste_mots"]['new_player']; ?></label>
                    <input form="form" maxlength="25" type="text" id="new-player" name="new_player" value="<?php echo $array_Champs['new_player'] ?>">
                </div>
                <div>
                    <input form="form" class="bouton" type="submit" name="btn_new_player" value="<?php echo $array_Champs["liste_mots"]['btn_new_player']; ?>">
                </div>
            </div>
            <div class="footer">
                <input form="form" class="bouton" type="submit" name="btn_voir_stats" value="<?php echo $array_Champs["liste_mots"]['btn_voir_stats']; ?>">
                <input form="form" class="bouton" type="submit" name="btn_login" value="<?php echo $array_Champs["liste_mots"]['btn_login']; ?>">
                <input form="form" class="bouton" type="submit" name="btn_return" value="<?php echo $array_Champs["liste_mots"]['btn_return']; ?>">
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
        <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="//cdn.jsdelivr.net/webshim/1.14.5/polyfiller.js"></script>
        <script src="gestion-stats.js"></script>
        <script src="date.js"></script>
    </body>
</html>