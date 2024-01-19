<?php
	// Les includes nécessaires
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
		
		return array("type_langue" => "", "joueur" => "", "gain" => "", "position" => "", "no_tournois" => "", "date" => "", "killer" => "", "citron" => "", "new_player" => "", 
                     "invalid_gain" => false, "invalid_new_player" => false, "invalid_no_tournois" => false, "invalid_date" => false, "invalid_citron" => false,
                     "invalid_killer" => false, "tous_invalids" => false, "tous_champs_vides" => false, "tous_long_invalids" => false, 
					 "long_invalid_gain" => false, "long_invalid_no_tournois" => false, "long_invalid_new_player" => false,
                     "long_invalid_date" => false, "long_invalid_killer" => false, "long_invalid_citron" => false,					 
					 "champ_joueur_vide" => false, "champ_position_vide" => false, "champ_gain_vide" => false, "champ_no_tournois_vide" => false, 
                     "champ_vide_date" => false, "champ_killer_vide" => false, "champ_citron_vide" => false, "champ_new_player_vide" => false,
					 "new_player_duplicate" => false, "liste_mots" => array(), "liste_joueurs" => array(), "user_valid" => false);
	}
	
	/**
	 * Fonction pour setter les premières informations du GET ou POST
	 * Aussi, on va récupérer via le POST, les informations relier au username et du password
	 *
     * @param mysqli $connMYSQL -> connexion aux tables de benoitmignault.ca
	 * @param array $array_Champs
	 * @return array
	 */
	function remplissage_champs(mysqli $connMYSQL, array $array_Champs): array{
	
        // Remplissage de la liste de joueurs disponibles pour assignation des statistiques
        // Qu'on soit dans le GET ou POST, au final, la première option sera toujours celle sélectionnée
        
	    if ($_SERVER['REQUEST_METHOD'] == 'GET'){
        
        
        }
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		
		if (isset($_POST['new_player'])) {
			$array_Champs["new_player"] = $_POST['new_player'];
   
		} else
    }
		
		if (isset($_POST['btn_ajouter_nouveau'])) {
			$array_Champs["new_player"] = $_POST['new_player'];
   
   
		} elseif (isset($_POST['ajouter_stats'])) {
            
                if (isset($_POST['joueur'])) {
	                $array_Champs["joueur"] = $_POST['joueur'];
                }
				
                if (isset($_POST['gain'])) {
                    $array_Champs["gain"] = $_POST['gain'];
                }
                
                if (isset($_POST['position'])) {
                    $array_Champs["position"] = $_POST['position'];
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
			
		
		return $array_Champs;
	}
	
	function requete_SQL_recuperation_liste_joueurs(mysqli $connMYSQL, array $array_Champs): array{
		
		// Retourne l'information des informations de connexion, si existant...
		return recuperation_liste_joueurs($connMYSQL, $result, $array_Champs);
    }
	
	
	/**
	 * Fonction pour récupérer les informations du user en prévision de la connexion, si tout est valide.
	 * Cette fonction sera @see requete_SQL_recuperation_liste_joueurs
	 *
	 * @param mysqli $connMYSQL -> Doit être présent pour utiliser les notions de MYSQLi
	 * @param object $result -> Le résultat de la requête SQL via la table « login »
	 * @param array $array_Champs
	 * @return array
	 */
	function recuperation_liste_joueurs(mysqli $connMYSQL, object $result, array $array_Champs): array {
    
    }
 
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
	
	function validation($array_Champs, $valid_Champ, $connMYSQL) {
		
		if (isset($_POST['ajouter_stats'])) {
			if (empty($array_Champs['liste_joueurs']) || empty($array_Champs['gain']) || empty($array_Champs['citron']) || empty($array_Champs['killer']) || empty($array_Champs['position']) || empty($array_Champs['no_tournois']) || empty($array_Champs['date'])) {
				// sous condition propre à chaque champ
				if (empty($array_Champs['liste_joueurs'])) {
					$valid_Champ['champ_joueur_vide'] = true;
				}
				if (empty($array_Champs['position'])) {
					$valid_Champ['champ_position_vide'] = true;
				}
				if (empty($array_Champs['no_tournois'])) {
					$valid_Champ['champ_no_tournois_vide'] = true;
				}
				if (empty($array_Champs['date'])) {
					$valid_Champ['champ_vide_date'] = true;
				}
				// Les array_Champs killer, citron et gain peuvent être de zéro, donc je ne peux pas les évoluer individuellement...
				if ($valid_Champ['champ_joueur_vide'] && $valid_Champ['champ_killer_vide'] && $valid_Champ['champ_citron_vide'] && $valid_Champ['champ_gain_vide'] && $valid_Champ['champ_position_vide'] && $valid_Champ['champ_no_tournois_vide'] && $valid_Champ['champ_vide_date']) {
					$valid_Champ['tous_champs_vides'] = true;
				}
			}
		}
        elseif (isset($_POST['ajouter_nouveau'])) {
			if (empty($array_Champs['new_player'])) {
				$valid_Champ['champ_new_player_vide'] = true;
				$valid_Champ['tous_champs_vides'] = true;
			}
			else {
				$sql = "select joueur from joueur order by joueur";
				$result = $connMYSQL->query($sql);
				foreach ($result as $row) {
					if ($row['joueur'] === $array_Champs['new_player']) {
						$valid_Champ['new_player_duplicate'] = true;
					}
				}
			}
		}
		
		$longueurGain = strlen($array_Champs['gain']);
		$longueurDate = strlen($array_Champs['date']);
		$longueurid = strlen($array_Champs['no_tournois']);
		$longueurnewJoueur = strlen($array_Champs['new_player']);
		$longueurKiller = strlen($array_Champs['killer']);
		$longueurCitron = strlen($array_Champs['citron']);
		
		if (isset($_POST['ajouter_stats'])) {
			if ($longueurGain > 4) {
				$valid_Champ['long_invalid_gain'] = true;
			}
			if ($longueurDate > 10) {
				$valid_Champ['long_invalid_date'] = true;
			}
			if ($longueurid > 4) {
				$valid_Champ['long_invalid_no_tournois'] = true;
			}
			if ($longueurKiller > 4) {
				$valid_Champ['long_invalid_killer'] = true;
			}
			if ($longueurCitron > 4) {
				$valid_Champ['long_invalid_citron'] = true;
			}
		}
        elseif (isset($_POST['ajouter_nouveau'])) {
			if ($longueurnewJoueur > 25) {
				$valid_Champ['long_invalid_new_player'] = true;
			}
		}
		
		$patternNewJoueur = "#^[A-Z]([a-z]{0,11})([-]{0,1})([A-Z]{0,1})([a-z]{1,9})([ ]{0,1})[a-zA-Z]$#";
		$patternGain = "#^[-]{0,1}([0-9]{1,3})$#";
		$patternID = "#^[0-9]{1,4}$#";
		$patternDate = "#^([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$#";
		// Changement du pattern pour les prix killer et citron pour avoir 2 chiffres après les décimals
		$patternKillerCitron = "#^([0-9]{1})([.]){0,1}([1-9]){0,2}$#";
		
		if (isset($_POST['ajouter_stats'])) {
			if (!preg_match($patternGain, $array_Champs['gain'])) {
				$valid_Champ['invalid_gain'] = true;
			}
			if (!preg_match($patternID, $array_Champs['no_tournois'])) {
				$valid_Champ['invalid_no_tournois'] = true;
			}
			if (!preg_match($patternDate, $array_Champs['date'])) {
				$valid_Champ['invalid_date'] = true;
			}
			if (!preg_match($patternKillerCitron, $array_Champs['killer'])) {
				$valid_Champ['invalid_killer'] = true;
			}
			if (!preg_match($patternKillerCitron, $array_Champs['citron'])) {
				$valid_Champ['invalid_citron'] = true;
			}
		}
        elseif (isset($_POST['ajouter_nouveau'])) {
			if (!preg_match($patternNewJoueur, $array_Champs['new_player'])) {
				$valid_Champ['invalid_new_player'] = true;
			}
		}
		return $valid_Champ;
	}
	
	function situation($array_Champs, $valid_Champ) {
		
		if (isset($_POST['ajouter_stats'])) {
			// Nous commençons par la section si la page est en anglais
			if ($array_Champs['type_langue'] === "francais") {
				if ($valid_Champ['tous_champs_vides']) {
					$array_Champs['message'] .= "Tous les champs sont vides.<br>";
				}
				else {
					// vérification au niveau du champ joueur
					if ($valid_Champ['champ_joueur_vide']) {
						$array_Champs['message'] .= "Le champ du joueur est vide.<br>";
					}
					// vérification au niveau du champ gain
					if ($valid_Champ['champ_gain_vide']) {
						$array_Champs['message'] .= "Le champ du gain est vide.<br>";
					}
					else {
						if ($valid_Champ['invalid_gain']) {
							$array_Champs['message'] .= "Le gain est invalide.<br>";
						}
						if ($valid_Champ['long_invalid_gain']) {
							$array_Champs['message'] .= "La longueur du gain est invalide.<br>";
						}
					}
					// vérification au niveau du champ killer
					if ($valid_Champ['champ_killer_vide']) {
						$array_Champs['message'] .= "Le champ du killer est vide.<br>";
					}
					else {
						if ($valid_Champ['invalid_killer']) {
							$array_Champs['message'] .= "Le nombre de killer est invalide.<br>";
						}
						if ($valid_Champ['long_invalid_killer']) {
							$array_Champs['message'] .= "La longueur du killer est invalide.<br>";
						}
					}
					// vérification au niveau du champ citron
					if ($valid_Champ['champ_citron_vide']) {
						$array_Champs['message'] .= "Le champ du prix citron est vide.<br>";
					}
					else {
						if ($valid_Champ['invalid_citron']) {
							$array_Champs['message'] .= "L'attribution du prix citron est invalide.<br>";
						}
						if ($valid_Champ['long_invalid_citron']) {
							$array_Champs['message'] .= "La longueur du prix citron est invalide.<br>";
						}
					}
					// vérification au niveau du champ position
					if ($valid_Champ['champ_position_vide']) {
						$array_Champs['message'] .= "Le champ de la position est vide.<br>";
					}
					// vérification au niveau du numéro de tournoi
					if ($valid_Champ['champ_no_tournois_vide']) {
						$array_Champs['message'] .= "Le champ du no. tournoi est vide.<br>";
					}
					else {
						if ($valid_Champ['invalid_no_tournois']) {
							$array_Champs['message'] .= "Le champ no. du tournoi est invalide.<br>";
						}
						if ($valid_Champ['long_invalid_no_tournois']) {
							$array_Champs['message'] .= "La longueur du no. tournoi est invalide.<br>";
						}
					}
					// vérification au niveau de la date
					if ($valid_Champ['champ_vide_date']) {
						$array_Champs['message'] .= "Le champ de la date est vide.<br>";
					}
					else {
						if ($valid_Champ['invalid_date']) {
							$array_Champs['message'] .= "Le champ de la date est invalide.<br>";
						}
						if ($valid_Champ['long_invalid_date']) {
							$array_Champs['message'] .= "Le champ de la date est invalide.<br>";
						}
					}
				}
				// nous sommes rendu à la section si la page est en anglais
			}
            elseif ($array_Champs['type_langue'] === "english") {
				if ($valid_Champ['tous_champs_vides']) {
					$array_Champs['message'] .= "All fields are empty.<br>";
				}
				else {
					// vérification au niveau du champ joueur
					if ($valid_Champ['champ_joueur_vide']) {
						$array_Champs['message'] .= "The player's field is empty.<br>";
					}
					// vérification au niveau du champ gain
					if ($valid_Champ['champ_gain_vide']) {
						$array_Champs['message'] .= "The gain field is empty.<br>";
					}
					else {
						if ($valid_Champ['invalid_gain']) {
							$array_Champs['message'] .= "The gain is invalid.<br>";
						}
						if ($valid_Champ['long_invalid_gain']) {
							$array_Champs['message'] .= "The length of the gain is invalid.<br>";
						}
					}
					// vérification au niveau du champ killer
					if ($valid_Champ['champ_killer_vide']) {
						$array_Champs['message'] .= "The killer field is empty.<br>";
					}
					else {
						if ($valid_Champ['invalid_killer']) {
							$array_Champs['message'] .= "The number of killer is invalid.<br>";
						}
						if ($valid_Champ['long_invalid_killer']) {
							$array_Champs['message'] .= "The length of the killer is invalid.<br>";
						}
					}
					// vérification au niveau du champ citron
					if ($valid_Champ['champ_citron_vide']) {
						$array_Champs['message'] .= "The field of the lemon price is empty.<br>";
					}
					else {
						if ($valid_Champ['invalid_citron']) {
							$array_Champs['message'] .= "The award of the lemon prize is invalid.<br>";
						}
						if ($valid_Champ['long_invalid_citron']) {
							$array_Champs['message'] .= "The length of the lemon price is invalid.<br>";
						}
					}
					// vérification au niveau du champ position
					if ($valid_Champ['champ_position_vide']) {
						$array_Champs['message'] .= "The position field is empty.<br>";
					}
					// vérification au niveau du numéro de tournoi
					if ($valid_Champ['champ_no_tournois_vide']) {
						$array_Champs['message'] .= "The field of no. tournament is empty.<br>";
					}
					else {
						if ($valid_Champ['invalid_no_tournois']) {
							$array_Champs['message'] .= "The field no. of the tournament is invalid.<br>";
						}
						if ($valid_Champ['long_invalid_no_tournois']) {
							$array_Champs['message'] .= "The length of the no. tournament is invalid.<br>";
						}
					}
					// vérification au niveau de la date
					if ($valid_Champ['champ_vide_date']) {
						$array_Champs['message'] .= "The date field is empty.<br>";
					}
					else {
						if ($valid_Champ['invalid_date']) {
							$array_Champs['message'] .= "The date field is invalid.<br>";
						}
						if ($valid_Champ['long_invalid_date']) {
							$array_Champs['message'] .= "The date field is invalid.<br>";
						}
					}
				}
			}
		}
        elseif (isset($_POST['ajouter_nouveau'])) {
			if ($array_Champs['type_langue'] === "francais") {
				if ($valid_Champ['champ_new_player_vide']) {
					$array_Champs['message'] .= "Le champ du nouveau joueur est vide.<br>";
				}
				else {
					if ($valid_Champ['invalid_new_player']) {
						$array_Champs['message'] .= "Le nom du nouveau joueur n'est pas valide.<br>";
					}
					if ($valid_Champ['long_invalid_new_player']) {
						$array_Champs['message'] .= "Le nom du nouveau joueur est trop long.<br>";
					}
					if ($valid_Champ['new_player_duplicate']) {
						$array_Champs['message'] .= "Le nom du nouveau joueur est déjà présent dans la BD.<br>";
					}
				}
			}
            elseif ($array_Champs['type_langue'] === "english") {
				if ($valid_Champ['champ_new_player_vide']) {
					$array_Champs['message'] .= "The new player's field is empty.<br>";
				}
				else {
					if ($valid_Champ['invalid_new_player']) {
						$array_Champs['message'] .= "The name of the new player is invalid.<br>";
					}
					if ($valid_Champ['long_invalid_new_player']) {
						$array_Champs['message'] .= "The name of the new player is too long.<br>";
					}
					if ($valid_Champ['new_player_duplicate']) {
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
		return $array_Champs;
	}
	
	function verificationTout_Champs($valid_Champ) {
		
		$autorisation = true;
		foreach ($valid_Champ as $eachFlag => $info) {
			if ($info == true) {
				$autorisation = false;
			}
		}
		return $autorisation;
	}
	
	function verifChampGain($valid_Champ) {
		
		if ($valid_Champ['invalid_gain'] || $valid_Champ['long_invalid_gain'] || $valid_Champ['champ_gain_vide']) {
			return true;
		}
		return false;
	}
	
	function verifChampId($valid_Champ) {
		
		if ($valid_Champ['invalid_no_tournois'] || $valid_Champ['long_invalid_no_tournois'] || $valid_Champ['champ_no_tournois_vide']) {
			return true;
		}
		return false;
	}
	
	function verifChampDate($valid_Champ) {
		
		if ($valid_Champ['invalid_date'] || $valid_Champ['long_invalid_date'] || $valid_Champ['champ_vide_date']) {
			return true;
		}
		return false;
	}
	
	function verifChampJoueur($valid_Champ) {
		
		if ($valid_Champ['champ_joueur_vide']) {
			return true;
		}
		return false;
	}
	
	function verifChampKiller($valid_Champ) {
		
		if ($valid_Champ['invalid_killer'] || $valid_Champ['long_invalid_killer'] || $valid_Champ['champ_killer_vide']) {
			return true;
		}
		return false;
	}
	
	function verifChampCitron($valid_Champ) {
		
		if ($valid_Champ['invalid_citron'] || $valid_Champ['long_invalid_citron'] || $valid_Champ['champ_citron_vide']) {
			return true;
		}
		return false;
	}
	
	function verifChampNouveau($valid_Champ) {
		
		if ($valid_Champ['invalid_new_player'] || $valid_Champ['long_invalid_new_player'] || $valid_Champ['champ_new_player_vide']) {
			return true;
		}
		return false;
	}
	
	function verificationUser($connMYSQL) {
		
		// Optimisation de la vérification si le user existe dans la BD
		/* Crée une requête préparée */
		$stmt = $connMYSQL->prepare("select user, password from login where user=? ");
		
		/* Lecture des marqueurs */
		$stmt->bind_param("s", $_SESSION['user']);
		
		/* Exécution de la requête */
		$stmt->execute();
		
		/* Association des variables de résultat */
		$result = $stmt->get_result();
		$stmt->close();
		if ($result->num_rows == 1) {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			// On ajoute une vérification pour vérifier que cest le bon user versus la bonne valeur - 2018-12-28
			if ($_COOKIE['POKER'] == $row['user']) {
				if (password_verify($_SESSION['password'], $row['password'])) {
					return true; // dès qu'on trouve notre user + son bon mdp on exit de la fct
				}
			}
		}
		return false;
	}
	
	function redirection($array_Champs, $connMYSQL) {
		
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			delete_Session();
			header("Location: /erreur/erreur.php");
		}
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
			
			if (isset($_POST['stats'])) {
				// Comme j'ai instauré une foreign key entre la table login_stat_poker vers login je dois aller récupérer id pour l'insérer avec la nouvelle combinaison
				$sql = "select id from login where user = '{$_SESSION['user']}' ";
				$result_SQL = $connMYSQL->query($sql);
				$row = $result_SQL->fetch_row();               // C'est mon array de résultat
				$id = (int)$row[0];                            // Assignation de la valeur
				date_default_timezone_set('America/New_York'); // Je dois mettre ça si je veux avoir la bonne heure et date dans mon entrée de data
				$date = date("Y-m-d H:i:s");
				
				// Ici, on va saisir une entree dans la BD pour l'admin comme il s'en va vers les statistiques
				$insert = "INSERT INTO login_stat_poker (user, date, id_user) VALUES ";
				$insert .= "('" . $_SESSION['user'] . "', '" . $date . "', '" . $id . "')";
				$connMYSQL->query($insert);
				header("Location: /login-user/poker-stats/show-stats/stats.php");
			}
            elseif (isset($_POST['login'])) {
				header("Location: /login-user/login-user.php?langue={$array_Champs["type_langue"]}");
	   
				delete_Session();
			}
            elseif (isset($_POST['accueil'])) {
				if ($array_Champs["type_langue"] == 'english') {
					header("Location: /english/english.html");
				}
				else {
					header("Location: /index.html");
				}
				delete_Session();
			}
		}
		exit; // pour arrêter l'éxecution du code php
	}
	
	function delete_Session() {
		
		// Ajout de ces 4 lignes pour bien effacer toutes traces de la session de mon utilisateur - 2018-12-28
		session_unset();                                           // détruire toutes les variables SESSION
		setcookie("POKER", $_SESSION['user'], time() - 3600, "/"); // permettre de détruire bien comme il faut le cookie du user
		session_destroy();
		session_write_close(); // https://stackoverflow.com/questions/2241769/php-how-to-destroy-the-session-cookie-correctly
	}
	
	function ajout_Stat_Joueur($array_Champs, $connMYSQL) {
		
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
	
	function ajouter_Nouveau_Joueur($array_Champs, $connMYSQL) {
		
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
	
	// Les fonctions communes
    session_start();
	$connMYSQL = connexion();
	$array_Champs = initialisation();
	$array_Champs = remplissage_champs($array_Champs);
 
 
 
 
 
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		
		$array_Champs["type_langue"] = "francais";
  
		if (isset($_SESSION['user']) && isset($_SESSION['password']) && isset($_SESSION['type_langue'])) {
			$verificationUser = verificationUser($connMYSQL);
   
		} else {
			redirection($array_Champs, $connMYSQL);
		}
		
		// on vérifier si notre user existe en bonne éduforme
		if (!$verificationUser) {
			redirection($array_Champs, $connMYSQL);
   
		}  elseif ($array_Champs["type_langue"] !== "francais" && $array_Champs["type_langue"] !== "english") {
			redirection($array_Champs, $connMYSQL);
   
		} else {
			$array_Champs = initialisationChamps();
			$valid_Champ = initialisation();
			$arrayMots = traduction($array_Champs["type_langue"], 0);
			$listeJoueurs = creationListe($connMYSQL, $arrayMots, $array_Champs);
		}
	}
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		session_start();
		$array_Champs["type_langue"] = "francais";
  
		if (isset($_SESSION['user']) && isset($_SESSION['password']) && isset($_SESSION['type_langue'])) {
			$verificationUser = verificationUser($connMYSQL);
   
		} else {
			redirection($array_Champs, $connMYSQL);
		}
		
		// on vérifier si notre user existe en bonne éduforme
		if (!$verificationUser) {
			redirection($array_Champs, $connMYSQL);
   
		} elseif ($array_Champs["type_langue"] !== "francais" && $array_Champs["type_langue"] !== "english") {
			redirection($array_Champs, $connMYSQL);
   
		} else {
			$array_Champs = initialisationChamps();
			$valid_Champ = initialisation();
   
			if (isset($_POST['stats']) || isset($_POST['login']) || isset($_POST['accueil'])) {
				redirection($array_Champs, $connMYSQL);
				
			} elseif (isset($_POST['effacer'])) {
				$array_Champs = situation($array_Champs, $valid_Champ);
				$verif_tous_flag = verificationTout_Champs($valid_Champ);
				
			} elseif (isset($_POST['ajouter_stats'])) {
				$array_Champs = remplissageChamps($array_Champs);
				$valid_Champ = validation($array_Champs, $valid_Champ, $connMYSQL);
				$array_Champs = situation($array_Champs, $valid_Champ);
				if ($array_Champs['message'] === "") {
					$array_Champs = ajout_Stat_Joueur($array_Champs, $connMYSQL);
				}
				$verif_tous_flag = verificationTout_Champs($valid_Champ);
				
			} elseif (isset($_POST['ajouter_nouveau'])) {
				$array_Champs = remplissageChamps($array_Champs);
				$valid_Champ = validation($array_Champs, $valid_Champ, $connMYSQL);
				$array_Champs = situation($array_Champs, $valid_Champ);
				if ($array_Champs['message'] === "") {
					$array_Champs = ajouter_Nouveau_Joueur($array_Champs, $connMYSQL);
				}
				$verif_tous_flag = verificationTout_Champs($valid_Champ);
			}
   
			$arrayMots = traduction($array_Champs["type_langue"], 0);
			$listeJoueurs = creationListe($connMYSQL, $arrayMots, $array_Champs);
		}
	}
	// On va faire la traduction, à la fin des GET & POST
	// La variable de situation est encore à 0 pour le GET, donc aucun message
	$array_Champs["liste_mots"] = traduction($array_Champs["type_langue"], $array_Champs["situation"]);
	
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
                    <div class="joueur <?php if (verifChampJoueur($valid_Champ)) { echo "erreur"; } ?>">
                        <label for="joueur"><?php echo $array_Champs["liste_mots"]['joueur']; ?></label>
                        <select id="joueur" name="liste_joueurs"><?php echo $listeJoueurs; ?></select>
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
                    <div class="gain <?php if (verifChampGain($valid_Champ)) { echo "erreur";} ?>">
                        <label for="gain"><?php echo $array_Champs["liste_mots"]['gain']; ?></label>
                        <input maxlength="4" type="text" id="gain" name="gain" value="<?php echo $array_Champs['gain'] ?>">
                    </div>
                    <div class="numero <?php if (verifChampId($valid_Champ)) { echo "erreur"; } ?>">
                        <label for="no_tournois"><?php echo $array_Champs["liste_mots"]['no_tournois']; ?></label>
                        <input maxlength="4" type="text" id="no_tournois" name="no_tournois" value="<?php echo $array_Champs['no_tournois'] ?>">
                    </div>
                    <div class="date <?php if (verifChampDate($valid_Champ)) { echo "erreur"; } ?>">
                        <div class="form-row animate-2">
                            <label for="date">Date :</label>
                            <input type="date" id="date" value="<?php echo $array_Champs['date'] ?>" name="date" data-date='{"startView": 2, "openOnMouseFocus": true}'>
                        </div>
                    </div>
                    <div class="killer <?php if (verifChampKiller($valid_Champ)) { echo "erreur"; } ?>">
                        <label for="killer"><?php echo $array_Champs["liste_mots"]['killer']; ?></label>
                        <input maxlength="4" type="text" id="killer" name="killer" value="<?php echo $array_Champs['killer'] ?>">
                    </div>
                    <div class="citron <?php if (verifChampCitron($valid_Champ)) { echo "erreur"; } ?>">
                        <label for="citron"><?php echo $array_Champs["liste_mots"]['citron']; ?></label>
                        <input maxlength="4" type="text" id="citron" name="citron" value="<?php echo $array_Champs['citron'] ?>">
                    </div>
                    <div class="bas-formulaire">
                        <input class="bouton" type="submit" name="btn_add_stat" value="<?php echo $array_Champs["liste_mots"]['btn_add_stat']; ?>">
                        <input class="bouton" id="faire_menage_total" type="reset" value="<?php echo $array_Champs["liste_mots"]['btn_erase']; ?>">
                    </div>
                    <div class="bas-formulaire">
                        <p class="<?php if ((isset($_POST['effacer']) || isset($_POST['ajouter_stats']) || isset($_POST['ajouter_nouveau'])) && $verif_tous_flag === true) {
                            echo "avert"; } else { echo "erreur"; } ?>"> <?php echo $array_Champs['message']; ?> </p>
                    </div>
                </div>
            </form>
            <div class="formulaire-nouveau">
                <div class="<?php if (verifChampNouveau($valid_Champ)) { echo "erreur"; } ?>">
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
