<?php
	// Les includes nécessaires
	use JetBrains\PhpStorm\NoReturn;
	
	include_once("../../../traduction/traduction-gestion-stats.php");
	include_once("../../../includes/fct-connexion-bd.php");
	
	/**
	 * Fonction qui va contenir tous ce dont on aura besoin.
	 * Une partie des variables de type string ou int et une autre partie en boolean
	 * On va ajouter_stats un array pour les mots traduits ou non
	 *
	 * @return array
	 */
	function initialisation(): array {
		
		return array("user_valid" => false, "id_user" => 0, "type_langue" => "", "joueur" => "", "gain" => "", "position" => "",
                     "no_tournois" => "", "date" => "", "killer" => "", "citron" => "", "new_player" => "", "situation" => 0,
                     "invalid_gain" => false, "invalid_new_player" => false, "invalid_no_tournois" => false, "invalid_date" => false, "invalid_citron" => false,
                     "invalid_killer" => false, "tous_invalids" => false, "tous_champs_vides" => false, "tous_long_invalids" => false, 
					 "long_invalid_gain" => false, "long_invalid_no_tournois" => false, "long_invalid_new_player" => false,
                     "long_invalid_date" => false, "long_invalid_killer" => false, "long_invalid_citron" => false, "invalid_language" => false,
					 "champ_joueur_vide" => false, "champ_position_vide" => false, "champ_gain_vide" => false, "champ_no_tournois_vide" => false, 
                     "champ_vide_date" => false, "champ_killer_vide" => false, "champ_citron_vide" => false, "champ_new_player_vide" => false,
					 "new_player_duplicate" => false, "erreur_presente" => false, "message_erreur_bd" => "", "erreur_system_bd" => false,
                     "liste_mots" => array(), "liste_joueurs" => array(), "message" => "");
	}
	
	/**
	 * Fonction pour aller vérifier que nos informations dans notre cookie sont toujours avec notre BD
	 * On va vérifier seulement pour le user avec son password
     * On va récupérer aussi le id du user qui sera utile plus tard
	 *
	 * @param mysqli $connMYSQL -> connexion aux tables de benoitmignault.ca
	 * @param array $array_Champs
	 * @return array
	 */
	function requete_SQL_verif_user_valide(mysqli $connMYSQL, array $array_Champs): array {
		
		$select = "SELECT PASSWORD, ID ";
		$from = "FROM login ";
		$where = "WHERE user = ?";
		
		// Préparation de la requête SQL avec les parties nécessaires
		$query = $select . $from . $where;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		try {
			/* Lecture des marqueurs */
			$stmt->bind_param("s", $_SESSION['user']);
			
			/* Exécution de la requête */
			$stmt->execute();
		} catch (Exception $err){
			// Récupérer les messages d'erreurs
			$array_Champs["message_erreur_bd"] = $err->getMessage();
			
			// Sera utilisée pour faire afficher le message erreur spécial
			$array_Champs["erreur_system_bd"] = true;
		} finally {
			/* Association des variables de résultat */
			$result = $stmt->get_result();
			
			// Close statement
			$stmt->close();
		}
		
		// Retourne l'information des informations de connexion, si existant...
		return recuperation_info_user($connMYSQL, $result, $array_Champs);
	}
	
	/**
     * Fonction qui va retourner si le user est bien valide avec une comparaison positive du password
     * Cette fonction retournera l'information @see requete_SQL_verif_user_valide
     *
	 * @param mysqli $connMYSQL
	 * @param object $result
     * @param array $array_Champs
	 * @return array
	 */
    function recuperation_info_user(mysqli $connMYSQL, object $result, array $array_Champs): array {
	
	    // Récupération de la seule ligne possible contenu un array
	    $row = $result->fetch_array(MYSQLI_ASSOC);
	
	    // Le tableau résultat existe et il n'est pas null
	    if (isset($row) && is_array($row)) {
		
		    if (password_verify($_SESSION['password'], $row['PASSWORD'])) {
			    $array_Champs['user_valid'] = true;
			    // Assignation des informations pour la connexion, pour plus tard
			    $array_Champs['id_user'] = $row["ID"];
		    }
	    }
        
        return $array_Champs;
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
        if (isset($_SESSION['type_langue'])) {
	        $array_Champs["type_langue"] = $_SESSION['type_langue'];
        }
		
		// Remplissage de la liste de joueurs disponibles pour assignation des statistiques + d'autres variables
		$array_Champs = requete_SQL_recuperation_liste_joueurs($connMYSQL, $array_Champs);
        
        // Nous avons seulement le POST, rendu ici
	    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		
            // Lorsque nous avons un nouveau joueur
            if (isset($_POST['btn_new_player'])) {
                $array_Champs["new_player"] = $_POST['new_player'];
                
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
		try {
			/* Exécution de la requête */
			$stmt->execute();
		} catch (Exception $err){
			// Récupérer les messages d'erreurs
			$array_Champs["message_erreur_bd"] = $err->getMessage();
			
			// Sera utilisée pour faire afficher le message erreur spécial
			$array_Champs["erreur_system_bd"] = true;
		} finally {
			/* Association des variables de résultat */
			$result = $stmt->get_result();
			
			// Close statement
			$stmt->close();
		}
		
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
	 * @param mysqli $connMYSQL
	 * @param array $array_Champs
	 * @return array
	 */
	function validation_champs(mysqli $connMYSQL, array $array_Champs): array {
		
    // TODO passer à travers
		if (isset($_POST['btn_add_stat'])) {
   
			if (empty($array_Champs['liste_joueurs']) || empty($array_Champs['gain']) || empty($array_Champs['citron']) ||
                empty($array_Champs['killer']) || empty($array_Champs['position']) || empty($array_Champs['no_tournois']) || empty($array_Champs['date'])) {
				// sous condition propre à chaque champ
				if (empty($array_Champs['liste_joueurs'])) {
					$array_Champs['champ_joueur_vide'] = true;
				}
				if (empty($array_Champs['position'])) {
					$array_Champs['champ_position_vide'] = true;
				}
				if (empty($array_Champs['no_tournois'])) {
					$array_Champs['champ_no_tournois_vide'] = true;
				}
				if (empty($array_Champs['date'])) {
					$array_Champs['champ_vide_date'] = true;
				}
				// Les array_Champs killer, citron et gain peuvent être de zéro, donc je ne peux pas les évoluer individuellement...
				if ($array_Champs['champ_joueur_vide'] && $array_Champs['champ_killer_vide'] && $array_Champs['champ_citron_vide'] &&
                    $array_Champs['champ_gain_vide'] && $array_Champs['champ_position_vide'] && $array_Champs['champ_no_tournois_vide'] &&
					$array_Champs['champ_vide_date']) {
					$array_Champs['tous_champs_vides'] = true;
				}
			}
			
			$longueurGain = strlen($array_Champs['gain']);
			$longueurDate = strlen($array_Champs['date']);
			$longueurid = strlen($array_Champs['no_tournois']);
			$longueurKiller = strlen($array_Champs['killer']);
			$longueurCitron = strlen($array_Champs['citron']);
			
			if ($longueurGain > 4) {
				$array_Champs['long_invalid_gain'] = true;
			}
			if ($longueurDate > 10) {
				$array_Champs['long_invalid_date'] = true;
			}
			if ($longueurid > 4) {
				$array_Champs['long_invalid_no_tournois'] = true;
			}
			if ($longueurKiller > 4) {
				$array_Champs['long_invalid_killer'] = true;
			}
			if ($longueurCitron > 4) {
				$array_Champs['long_invalid_citron'] = true;
			}
			
			$patternGain = "#^-?[0-9]{1,3}$#";
			$patternID = "#^[0-9]{1,4}$#";
			/**
			 * Il y a 3 situations qui peuvent arriver selon les exigences :
             *
			 * Le mois de févier (peu importe l'année) compte 28 jours.
			 * Les mois de janvier, mars, mai, juillet, août, octobre, décembre comptent 31 jours.
			 * Les mois d'avril, juin, septembre, novembre comptent 30 jours.
			 */
			$patternDate = "#^[1-9][0-9]{3}-((0?2-(0?[1-9]|1[0-9]|2[0-8]))|((0?[13578]|1[02])-(0?[1-9]|[1-2][0-9]|3[0-1]))|((0?[469]|11)-(0?[1-9]|[1-2][0-9]|30)))$#";
			// Changement du pattern pour les prix killer et citron pour avoir 2 chiffres après les décimals
			$patternKillerCitron = "#^[0-9](.[0-9]{1,2})?$#";
			
			if (!preg_match($patternGain, $array_Champs['gain'])) {
				$array_Champs['invalid_gain'] = true;
			}
			if (!preg_match($patternID, $array_Champs['no_tournois'])) {
				$array_Champs['invalid_no_tournois'] = true;
			}
			if (!preg_match($patternDate, $array_Champs['date'])) {
				$array_Champs['invalid_date'] = true;
			}
			if (!preg_match($patternKillerCitron, $array_Champs['killer'])) {
				$array_Champs['invalid_killer'] = true;
			}
			if (!preg_match($patternKillerCitron, $array_Champs['citron'])) {
				$array_Champs['invalid_citron'] = true;
			}
   
   
   
   
		} elseif (isset($_POST['btn_new_player'])) {
			
            if (empty($array_Champs['new_player'])) {
	            $array_Champs['champ_new_player_vide'] = true;
	            $array_Champs['tous_champs_vides'] = true;
             
			} else {
    
                // TODO refaire ca en fonction avec le joueur passer dans le WHERE
				$sql = "select joueur from joueur order by joueur";
				$result = $connMYSQL->query($sql);
				foreach ($result as $row) {
					if ($row['joueur'] === $array_Champs['new_player']) {
						$array_Champs['new_player_duplicate'] = true;
					}
				}
	
	            $longueurnewJoueur = strlen($array_Champs['new_player']);
	            if ($longueurnewJoueur > 25) {
		            $array_Champs['long_invalid_new_player'] = true;
	            }
	
	            $patternNewJoueur = "#^[A-Z]([a-z]{0,11})([-]{0,1})([A-Z]{0,1})([a-z]{1,9})([ ]{0,1})[a-zA-Z]$#";
	            if (!preg_match($patternNewJoueur, $array_Champs['new_player'])) {
		            $array_Champs['invalid_new_player'] = true;
	            }
			}
		}
		
		return $array_Champs;
	}
	
	// TODO déplacer dans requete SQL plus haut
	function creationListe($connMYSQL, $arrayMots, $champ) {
		
		$sql = "select joueur from joueur order by joueur";
		$result = $connMYSQL->query($sql);
		
		if ($_SERVER['REQUEST_METHOD'] === 'GET' || empty($champ['liste_joueurs'])) {
			$listeJoueurs = "<option value='' selected>{$arrayMots['option']}</option>";
			foreach ($result as $row) {
				$listeJoueurs .= "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>";
			}
		}
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$listeJoueurs = "<option value=''>{$arrayMots['option']}</option>";
			foreach ($result as $row) {
				if ($champ['liste_joueurs'] === $row['joueur']) {
					$listeJoueurs .= "<option value=\"{$row['joueur']}\" selected>{$row['joueur']}</option>";
				}
				else {
					$listeJoueurs .= "<option value=\"{$row['joueur']}\">{$row['joueur']}</option>";
				}
			}
		}
		
		return $listeJoueurs;
	}
	
	
	// TODO passer à travers
	function situation_erreur($array_Champs) {
		
		/*
		if (isset($_POST['ajouter_stats'])) {
			// Nous commençons par la section si la page est en anglais
			if ($array_Champs['type_langue'] === "francais") {
				if ($array_Champs['tous_champs_vides']) {
					$array_Champs['message'] .= "Tous les champs sont vides.<br>";
				}
				else {
					// vérification au niveau du champ joueur
					if ($array_Champs['champ_joueur_vide']) {
						$array_Champs['message'] .= "Le champ du joueur est vide.<br>";
					}
					// vérification au niveau du champ gain
					if ($array_Champs['champ_gain_vide']) {
						$array_Champs['message'] .= "Le champ du gain est vide.<br>";
					}
					else {
						if ($array_Champs['invalid_gain']) {
							$array_Champs['message'] .= "Le gain est invalide.<br>";
						}
						if ($array_Champs['long_invalid_gain']) {
							$array_Champs['message'] .= "La longueur du gain est invalide.<br>";
						}
					}
					// vérification au niveau du champ killer
					if ($array_Champs['champ_killer_vide']) {
						$array_Champs['message'] .= "Le champ du killer est vide.<br>";
					}
					else {
						if ($array_Champs['invalid_killer']) {
							$array_Champs['message'] .= "Le nombre de killer est invalide.<br>";
						}
						if ($array_Champs['long_invalid_killer']) {
							$array_Champs['message'] .= "La longueur du killer est invalide.<br>";
						}
					}
					// vérification au niveau du champ citron
					if ($array_Champs['champ_citron_vide']) {
						$array_Champs['message'] .= "Le champ du prix citron est vide.<br>";
					}
					else {
						if ($array_Champs['invalid_citron']) {
							$array_Champs['message'] .= "L'attribution du prix citron est invalide.<br>";
						}
						if ($array_Champs['long_invalid_citron']) {
							$array_Champs['message'] .= "La longueur du prix citron est invalide.<br>";
						}
					}
					// vérification au niveau du champ position
					if ($array_Champs['champ_position_vide']) {
						$array_Champs['message'] .= "Le champ de la position est vide.<br>";
					}
					// vérification au niveau du numéro de tournoi
					if ($array_Champs['champ_no_tournois_vide']) {
						$array_Champs['message'] .= "Le champ du no. tournoi est vide.<br>";
					}
					else {
						if ($array_Champs['invalid_no_tournois']) {
							$array_Champs['message'] .= "Le champ no. du tournoi est invalide.<br>";
						}
						if ($array_Champs['long_invalid_no_tournois']) {
							$array_Champs['message'] .= "La longueur du no. tournoi est invalide.<br>";
						}
					}
					// vérification au niveau de la date
					if ($array_Champs['champ_vide_date']) {
						$array_Champs['message'] .= "Le champ de la date est vide.<br>";
					}
					else {
						if ($array_Champs['invalid_date']) {
							$array_Champs['message'] .= "Le champ de la date est invalide.<br>";
						}
						if ($array_Champs['long_invalid_date']) {
							$array_Champs['message'] .= "Le champ de la date est invalide.<br>";
						}
					}
				}
				// nous sommes rendu à la section si la page est en anglais
			}
            elseif ($array_Champs['type_langue'] === "english") {
				if ($array_Champs['tous_champs_vides']) {
					$array_Champs['message'] .= "All fields are empty.<br>";
				}
				else {
					// vérification au niveau du champ joueur
					if ($array_Champs['champ_joueur_vide']) {
						$array_Champs['message'] .= "The player's field is empty.<br>";
					}
					// vérification au niveau du champ gain
					if ($array_Champs['champ_gain_vide']) {
						$array_Champs['message'] .= "The gain field is empty.<br>";
					}
					else {
						if ($array_Champs['invalid_gain']) {
							$array_Champs['message'] .= "The gain is invalid.<br>";
						}
						if ($array_Champs['long_invalid_gain']) {
							$array_Champs['message'] .= "The length of the gain is invalid.<br>";
						}
					}
					// vérification au niveau du champ killer
					if ($array_Champs['champ_killer_vide']) {
						$array_Champs['message'] .= "The killer field is empty.<br>";
					}
					else {
						if ($array_Champs['invalid_killer']) {
							$array_Champs['message'] .= "The number of killer is invalid.<br>";
						}
						if ($array_Champs['long_invalid_killer']) {
							$array_Champs['message'] .= "The length of the killer is invalid.<br>";
						}
					}
					// vérification au niveau du champ citron
					if ($array_Champs['champ_citron_vide']) {
						$array_Champs['message'] .= "The field of the lemon price is empty.<br>";
					}
					else {
						if ($array_Champs['invalid_citron']) {
							$array_Champs['message'] .= "The award of the lemon prize is invalid.<br>";
						}
						if ($array_Champs['long_invalid_citron']) {
							$array_Champs['message'] .= "The length of the lemon price is invalid.<br>";
						}
					}
					// vérification au niveau du champ position
					if ($array_Champs['champ_position_vide']) {
						$array_Champs['message'] .= "The position field is empty.<br>";
					}
					// vérification au niveau du numéro de tournoi
					if ($array_Champs['champ_no_tournois_vide']) {
						$array_Champs['message'] .= "The field of no. tournament is empty.<br>";
					}
					else {
						if ($array_Champs['invalid_no_tournois']) {
							$array_Champs['message'] .= "The field no. of the tournament is invalid.<br>";
						}
						if ($array_Champs['long_invalid_no_tournois']) {
							$array_Champs['message'] .= "The length of the no. tournament is invalid.<br>";
						}
					}
					// vérification au niveau de la date
					if ($array_Champs['champ_vide_date']) {
						$array_Champs['message'] .= "The date field is empty.<br>";
					}
					else {
						if ($array_Champs['invalid_date']) {
							$array_Champs['message'] .= "The date field is invalid.<br>";
						}
						if ($array_Champs['long_invalid_date']) {
							$array_Champs['message'] .= "The date field is invalid.<br>";
						}
					}
				}
			}
		}
        elseif (isset($_POST['ajouter_nouveau'])) {
			if ($array_Champs['type_langue'] === "francais") {
				if ($array_Champs['champ_new_player_vide']) {
					$array_Champs['message'] .= "Le champ du nouveau joueur est vide.<br>";
				}
				else {
					if ($array_Champs['invalid_new_player']) {
						$array_Champs['message'] .= "Le nom du nouveau joueur n'est pas valide.<br>";
					}
					if ($array_Champs['long_invalid_new_player']) {
						$array_Champs['message'] .= "Le nom du nouveau joueur est trop long.<br>";
					}
					if ($array_Champs['new_player_duplicate']) {
						$array_Champs['message'] .= "Le nom du nouveau joueur est déjà présent dans la BD.<br>";
					}
				}
			}
            elseif ($array_Champs['type_langue'] === "english") {
				if ($array_Champs['champ_new_player_vide']) {
					$array_Champs['message'] .= "The new player's field is empty.<br>";
				}
				else {
					if ($array_Champs['invalid_new_player']) {
						$array_Champs['message'] .= "The name of the new player is invalid.<br>";
					}
					if ($array_Champs['long_invalid_new_player']) {
						$array_Champs['message'] .= "The name of the new player is too long.<br>";
					}
					if ($array_Champs['new_player_duplicate']) {
						$array_Champs['message'] .= "The name of the new player is already present in the BD.<br>";
					}
				}
			}
		}
        elseif (isset($_POST['effacer'])) {
			if ($array_Champs['type_langue'] === "francais") {
				$array_Champs['message'] = "Tous les array_Champs ont été remis à null et tous les flag de validations ont été remis à faux.<br>";
			}
            elseif ($array_Champs['type_langue'] === "english") {
				$array_Champs['message'] = "All fields have been reset and all validation flags have been overwritten.<br>";
			}
		}
		*/
		return 0;
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
	 * Fonction qui sera utilisée pour ajouter un log dans la table pour voir qui se connecter sur la page des statistiques de poker
	 *
	 * @param mysqli $connMYSQL
	 * @param array $array_Champs
	 * @return array
	 */// TODO récupérer le id du user pour aller ajoute run log
	function requete_SQL_ajout_log_connexion(mysqli $connMYSQL, array $array_Champs): array {
		
  
		// Comme j'ai instauré une foreign key entre la table login_stat_poker vers login je dois aller récupérer id pour l'insérer avec la nouvelle combinaison
		$sql = "select id from login where user = '{$_SESSION['user']}' ";
		$result_SQL = $connMYSQL->query($sql);
		$row = $result_SQL->fetch_row();               // C'est mon array de résultat
		$id = (int)$row[0];                            // Assignation de la valeur
		date_default_timezone_set('America/New_York'); // Je dois mettre ça si je veux avoir la bonne heure et date dans mon entrée de data
		$date = date("Y-m-d H:i:s");
		
		// Ici, on va saisir une entree dans la BD pour l'admin comme il s'en va vers les statistiques
		//$insert = "INSERT INTO login_stat_poker (user, date, id_user) VALUES";
		//$insert .= " ('" . $_SESSION['user'] . "', '" . $date . "', '" . $id . "')";
		//$connMYSQL->query($insert);
        
        
        // Ici, on va saisir une entrée dans la BD pour savoir qui se connecte aux statistiques de poker
				
		$insert = "INSERT INTO";
		$table = " login_stat_poker ";
		$colonnes = "(user, date, id_user) ";
		$values = "VALUES (?, ?, ?)";
		$query = $insert . $table . $colonnes . $values;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		try {
			/* Lecture des marqueurs */
			$stmt->bind_param('ssi', $_SESSION['user'],$date, $id);
			
			/* Exécution de la requête */
			$stmt->execute();
		} catch (Exception $err){
			// Récupérer les messages d'erreurs
			$array_Champs["message_erreur_bd"] = $err->getMessage();
			
			// Sera utilisée pour faire afficher le message erreur spécial
			$array_Champs["erreur_system_bd"] = true;
		} finally {
			// Fermer la préparation de la requête
			$stmt->close();
		}
		
		return $array_Champs;
	}
	
	
	/**
	 * Fonction pour rediriger le user vers la bonne page web, après toutes les validations
	 *
	 * @param array $array_Champs
	 * @return void
	 */ // TODO ajuster la fct
	#[NoReturn] function connexion_user(array $array_Champs): void {
		
		// Ouverture du cookie pour laisser une heure de consultation des statistiques de poker
		session_start();
		$_SESSION['user'] = $array_Champs['user'];
		$_SESSION['password'] = $array_Champs['password'];
		$_SESSION['type_langue'] = $array_Champs["type_langue"];
		
		// On va quand même créer le cookie vue qu'on va dans une zone sensible, soit l'insertion de DATA
		setcookie("POKER", $_SESSION['user'], time() + 3600, "/");
		
		// Si nous avons un user autre qu'un admin, on démarre le cookie, sinon on va attendre pour l'admin
		if (!$array_Champs['user_admin']){
			
			// Redirection d'un user normal
			header("Location: /login-user/poker-stats/show-stats/stats.php");
		} else {
			// Redirection d'un admin pour faire l'ajout des statistiques de poker
			header("Location: /login-user/poker-stats/gestion-stats/gestion-stats.php");
		}
		
		exit;
	}
	
	
	/**
	 * Fonction pour rediriger vers la bonne page extérieur à la page de gestion, sauf si
     * la variable $invalid_language est true
     * Pour le transfert vers la page de statistique, on va passer par @see connexion_user
	 *
	 * @param string $type_langue
	 * @param bool $invalid_language
	 * @return void
	 */
	#[NoReturn] function redirection(string $type_langue, bool $invalid_language): void {
		
		if ($invalid_language) {
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
		
		delete_Session();
		exit; // Pour arrêter l'exécution du code php
	}
 
	function delete_Session(): void {
		
		// Ajout de ces 4 lignes pour bien effacer toutes traces de la session de mon utilisateur - 2018-12-28
		session_unset(); // détruire toutes les variables SESSION
		setcookie("POKER", $_SESSION['user'], time() - 3600, "/"); // permettre de détruire bien comme il faut le cookie du user
		session_destroy();
		session_write_close(); // https://stackoverflow.com/questions/2241769/php-how-to-destroy-the-session-cookie-correctly
	}
 
    // TODO trouver une manière d'être rediriger vers la page erreur 404 au lieu d'avoir une erreur de variable qui n'existe plus
    
	// Les fonctions communes avant la validation du user
	session_start();
	$connMYSQL = connexion();
	$array_Champs = initialisation();
	$array_Champs = requete_SQL_verif_user_valide($connMYSQL, $array_Champs);
    
    // On va vérifier si le user est toujours valide via son user et password stocké dans les cookies/Session
    if ($array_Champs['user_valid']){
	
        // On va remplir les variables nécessaires ici
	    $array_Champs = remplissage_champs($connMYSQL, $array_Champs);
        
	    // La seule chose qui peut arriver dans le GET et au début du POST, ici est une variable de langue invalide
	    if ($array_Champs["invalid_language"]) {
		    redirection($array_Champs["type_langue"], $array_Champs["invalid_language"]);
	    }
	
	    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Si on veut sortir de la page pour revenir en arrière
            if (isset($_POST['btn_login']) || isset($_POST['btn_return'])) {
	            redirection($array_Champs["type_langue"], $array_Champs["invalid_language"]);
	            
                // Sinon on veut peut-être aller voir les statistiques de poker, une fois qu'on a fini d'ajouter les statistiques
            } elseif (isset($_POST['btn_voir_stats'])) {
	            $array_Champs = requete_SQL_ajout_log_connexion($connMYSQL, $array_Champs);
             
	            // Maintenant, on peut connecter le user à la page de statistiques
	            connexion_user($array_Champs);
                
            } elseif (isset($_POST['btn_add_stat']) || isset($_POST['btn_new_player'])) {
                
                // Maintenant, on va faire nos vérifications sur nos champs
	            $array_Champs = validation_champs($connMYSQL, $array_Champs);
	
	            // On vérifie que nous n'avons pas d'erreur dans les validations
	            if (!$array_Champs['erreur_presente']){
              
                    // On appel la bonne fonction en fonction du bouton choisi
		            if (isset($_POST['btn_add_stat'])){
			            $array_Champs = ajout_Stat_Joueur($connMYSQL, $array_Champs);
                    
                    } elseif (isset($_POST['btn_new_player'])){
			            $array_Champs = ajouter_Nouveau_Joueur($connMYSQL, $array_Champs);
                    }
                }
            }
		
		    $array_Champs["situation"] = situation_erreur($array_Champs);
	    }
     
	    // On va faire la traduction, à la fin des GET & POST
	    // La variable de situation est encore à 0 pour le GET, donc aucun message
	    $array_Champs["liste_mots"] = traduction($array_Champs["type_langue"], $array_Champs["situation"]);
        
        // Sinon, on sort directement vers page erreur 404
    } else {
	    redirection($array_Champs["type_langue"], $array_Champs["invalid_language"]);
    }
	
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
                <div class='formulaire-joueur'>
                    <div class="joueur <?php if (true) { echo "erreur"; } ?>">
                        <label for="joueur"><?php echo $array_Champs["liste_mots"]['joueur']; ?></label>
                        <select id="joueur" name="liste_joueurs">
                            <option value=""><?php echo $array_Champs["liste_mots"]['option']; ?></option>
                            <?php foreach ($array_Champs["liste_joueurs"] as $un_joueur) {
                                    if ($array_Champs["joueur"] === $un_joueur) { ?>
                                        <option value="<?php echo $un_joueur; ?>" selected><?php echo $un_joueur; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $un_joueur; ?>"><?php echo $un_joueur; ?></option>
                                    <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="position">
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
                    <div class="gain <?php if (true) { echo "erreur";} ?>">
                        <label for="gain"><?php echo $array_Champs["liste_mots"]['gain']; ?></label>
                        <input maxlength="4" type="text" id="gain" name="gain" value="<?php echo $array_Champs['gain'] ?>">
                    </div>
                    <div class="numero <?php if (true) { echo "erreur"; } ?>">
                        <label for="no_tournois"><?php echo $array_Champs["liste_mots"]['no_tournois']; ?></label>
                        <input maxlength="4" type="text" id="no_tournois" name="no_tournois" value="<?php echo $array_Champs['no_tournois'] ?>">
                    </div>
                    <div class="date <?php if (true) { echo "erreur"; } ?>">
                        <div class="form-row animate-2">
                            <label for="date">Date :</label>
                            <input type="date" id="date" value="<?php echo $array_Champs['date'] ?>" name="date" data-date='{"startView": 2, "openOnMouseFocus": true}'>
                        </div>
                    </div>
                    <div class="killer <?php if (true) { echo "erreur"; } ?>">
                        <label for="killer"><?php echo $array_Champs["liste_mots"]['killer']; ?></label>
                        <input maxlength="4" type="text" id="killer" name="killer" value="<?php echo $array_Champs['killer'] ?>">
                    </div>
                    <div class="citron <?php if (true) { echo "erreur"; } ?>">
                        <label for="citron"><?php echo $array_Champs["liste_mots"]['citron']; ?></label>
                        <input maxlength="4" type="text" id="citron" name="citron" value="<?php echo $array_Champs['citron'] ?>">
                    </div>
                    <div class="bas-formulaire">
                        <input class="bouton" type="submit" name="btn_add_stat" value="<?php echo $array_Champs["liste_mots"]['btn_add_stat']; ?>">
                        <input class="bouton" id="faire_menage_total" type="reset" value="<?php echo $array_Champs["liste_mots"]['btn_erase']; ?>">
                    </div>
                    <div class="bas-formulaire">
                        <p class="<?php if (true) { echo "avert"; } else { echo "erreur"; } ?>"> <?php echo $array_Champs['message']; ?> </p>
                    </div>
                </div>
            </form>
            <div class="formulaire-nouveau">
                <div class="<?php if (true) { echo "erreur"; } ?>">
                    <label for="new_player"><?php echo $array_Champs["liste_mots"]['new_player']; ?></label>
                    <input form="form" maxlength="25" type="text" id="new_player" name="new_player" value="<?php echo $array_Champs['new_player'] ?>">
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