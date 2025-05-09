<?php
	
	use JetBrains\PhpStorm\NoReturn;
	
	/**
	 * Retourne un array de variables qui seront utilisées pour le timer
     *
	 * @return array
	 */
	function initialisationChamps(): array {
		
		return array('message' => "", 'nomOrganisateur' => "", 'situation' => 0, 'combinaison' => 0, 'maxCombinaison' => 0, 'valeurSmall' => 0,
		        'valeurBig' => 0, 'listeDesOrganisateurs' => array(), 'listeDesValeursCouleurs' => array(),
		        'nouvelleCombinaison' => array('valeurSmall' => "00", 'valeurBig' => "00"),
		        'couleurs' => array('couleurRouge' => 255, 'couleurVert' => 255, 'couleurBleu' => 255));
	}
	
	/**
	 * Retourne un array de variables toutes à false pour commencer qui seront les chiens de garde du bon fonctionnement
     *
	 * @return false[]
	 */
	function initialisationChampsValidation(): array {
		
		return array('nomOrganisateurVide' => false, 'aucuneValeurSmallBig' => false, 'aucuneValeurCouleur' => false,
		        'aucuneValeurDispo' => false, 'aucunOrganisateur' => false, 'erreurPossible' => false);
	}
	
	/**
	 * Retourne le message en fonction de la situation trouver dans le dictionnaire et la traduire au besoin.
     *
	 * @param $dictionnaire
	 * @param $situation
	 * @return string
	 */
	function messageSituation($dictionnaire, $situation): string {
		
		define('SITUATION_1', "Vous devez choisir absolument votre organisateur de tournois avant de commencer la partie.");
		define('SITUATION_2', "Votre organisateur n'a pas organisé ses valeurs & couleurs de jetons.");
		define('SITUATION_3', "Votre organisateur n'a pas organisé ses mises de «Small» & «Big» blind.");
		define('SITUATION_4', "Votre organisateur n'a tout simplement rien organisé de son côté, veuillez lui en faire part.");
		
		$message = "";
		switch ($situation) {
			case 1: $message = traduction(SITUATION_1, $dictionnaire); break;
			case 2: $message = traduction(SITUATION_2, $dictionnaire); break;
			case 3: $message = traduction(SITUATION_3, $dictionnaire); break;
			case 4: $message = traduction(SITUATION_4, $dictionnaire); break;
		}
		
		return $message;
	}
	
	/**
	 * Remplissage des variables en fonction de quels boutons a été sélectionné
     *
	 * @param $connMYSQL
	 * @param $champs
	 * @return array
	 */
	function remplissageChamps($connMYSQL, $champs): array {
		
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   
			if (isset($_GET['langue'])) {
				$_SERVER['typeLangue'] = $_GET['langue'];
			}
   
		} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
			
			if (isset($_POST['typeLangue'])) {
				$_SERVER['typeLangue'] = $_POST['typeLangue'];
			}
			
			if (isset($_POST['choixOrganisateur'])) {
				$champs['nomOrganisateur'] = $_POST['choixOrganisateur'];
			}
			
			if (isset($_POST['btnChoixOrganisateur'])) {
				$champs['maxCombinaison'] = recupererMaxCombinaison($connMYSQL, $champs['nomOrganisateur']);
    
			} elseif (isset($_POST['btnChangerMise'])) {
				
				if (isset($_POST['maxCombinaison'])) {
					$champs['maxCombinaison'] = intval($_POST['maxCombinaison']);
				}
				
				$champs['combinaison'] = intval($_POST['combinaison']);
				$champs['couleurs'] = remplissageCouleurs();
    
			} elseif (isset($_POST['btnResetMise'])) {
				// En raison d'un bug, nous allons récupéré la valeur Max pour permettre à la validation aucuneValeurSmallBig d'être ok
				$champs['maxCombinaison'] = recupererMaxCombinaison($connMYSQL, $champs['nomOrganisateur']);
				$champs['combinaison'] = 0;
				$champs['couleurs'] = array('couleurRouge' => 255, 'couleurVert' => 255, 'couleurBleu' => 255);
			}
		}
		
		return $champs;
	}
	
	/**
	 * Retourne la liste des organisateurs
     *
	 * @param $connMYSQL
	 * @return array
	 */
	function listeDesOrganisateurs($connMYSQL): array {
		
		// Définition de constantes pour les chaînes de caractères statiques
		define('SELECT_LISTE_DES_ORGANISATEURS', '*');
		define('FROM_LISTE_DES_ORGANISATEURS', 'login_organisateur');
		define('ORDER_LISTE_DES_ORGANISATEURS', 'name');
		
		// Création de la futur liste des organisateurs
		$listeDesOrganisateurs = array();
		
		// Préparation de la requête SQL avec un alias pour la colonne sélectionnée
		$query = "SELECT " . SELECT_LISTE_DES_ORGANISATEURS . " FROM " . FROM_LISTE_DES_ORGANISATEURS . " ORDER BY " . ORDER_LISTE_DES_ORGANISATEURS;
		
		// Préparation de la requête
		$stmt = $connMYSQL->prepare($query);
		
		/* Exécution de la requête */
		$stmt->execute();
		
		// Vérification de l'exécution de la requête
		if (!$stmt->errno) {
			// Récupération des résultats
			$result = $stmt->get_result();
			$row_cnt = $result->num_rows;
			
			if ($row_cnt > 0) {
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$listeDesOrganisateurs[] = array("user" => $row['user'], "name" => $row['name']);
				}
			}
		}
		
		return $listeDesOrganisateurs;
	}
	
	/**
	 * Retourne la liste des valeurs des couleurs de jetons de l'organisateur
     *
	 * @param $connMYSQL
	 * @param $nomOrganisateur
	 * @return array
	 */
	function selectionValeursCouleurs($connMYSQL, $nomOrganisateur): array {
		
		// Définition de constantes pour les chaînes de caractères statiques
		define('SELECT_SELECTION_VALEUR_COULEUR', 'amount, color_english');
		define('FROM_SELECTION_VALEUR_COULEUR', 'amount_color');
		define('WHERE_SELECTION_VALEUR_COULEUR', 'user');
		define('ORDER_SELECTION_VALEUR_COULEUR', 'amount');
		
		// Création de la futur liste des combinaisons Valeurs / Couleurs de l'organisateur
		$listeDesValeursCouleurs = array();
		
		// Préparation de la requête SQL avec un alias pour la colonne sélectionnée
		$query = "SELECT " . SELECT_SELECTION_VALEUR_COULEUR . " FROM " . FROM_SELECTION_VALEUR_COULEUR . " WHERE " . WHERE_SELECTION_VALEUR_COULEUR . " = ? " . " ORDER BY " . ORDER_SELECTION_VALEUR_COULEUR;
		
		$stmt = $connMYSQL->prepare($query);
		
		// Les & est la référence de la variable que je dois passer en paramètre
		$params = array("s", &$nomOrganisateur);
		
		// Exécutez la requête en utilisant call_user_func_array
		call_user_func_array(array($stmt, "bind_param"), $params);
		
		// Exécution de la requête
		$stmt->execute();
		
		// Vérification de l'exécution de la requête
		if (!$stmt->errno) {
			// Récupération du résultat de la requête
			$result = $stmt->get_result();
			$row_cnt = $result->num_rows;
			
			if ($row_cnt > 0) {
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$listeDesValeursCouleurs[] = array('amount' => $row['amount'], 'color_english' => $row['color_english']);
				}
			}
		}
		
		// Fermeture de la requête
		$stmt->close();
		
		return $listeDesValeursCouleurs;
	}
	
	/**
	 * Retourne le nombre maximale de combinaisons possible
     *
	 * @param $connMYSQL
	 * @param $nomOrganisateur
	 * @return int
	 */
	function recupererMaxCombinaison($connMYSQL, $nomOrganisateur): int {
		
		// Définition de constantes pour les chaînes de caractères statiques
		define('SELECT_MAX_COMBINAISON_USER', 'number_of_records');
		define('FROM_MAX_COMBINAISON_USER', 'mise_small_big');
		define('WHERE_MAX_COMBINAISON_USER', 'user');
		
		// Préparation de la requête SQL avec un alias pour la colonne sélectionnée
		$query = "SELECT count(*) as " . SELECT_MAX_COMBINAISON_USER . " FROM " . FROM_MAX_COMBINAISON_USER . " where " . WHERE_MAX_COMBINAISON_USER . " = ?";
		
		$stmt = $connMYSQL->prepare($query);
		
		// Les & est la référence de la variable que je dois passer en paramètre
		$params = array("s", &$nomOrganisateur);
		
		// Exécutez la requête en utilisant call_user_func_array
		call_user_func_array(array($stmt, "bind_param"), $params);
		
		// Exécution de la requête
		$stmt->execute();
		
		// Vérification de l'exécution de la requête
		if (!$stmt->errno) {
			// Récupération du résultat de la requête
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			
			// Fermeture de la requête
			$stmt->close();
			
			// Retour de la valeur de la colonne 'number_of_records'
			return $row[SELECT_MAX_COMBINAISON_USER];
		} // Sinon, on retourne 0, car IntelliJ n'aime pas qu'on retourne pas de return
		else {
			return 0;
		}
	}
	
	
	/**
	 * Vérifier que tous c'est bien passé
     *
	 * @param $champs , on passera la variable au complet, car beaucoup de choses seront utilisées.
	 * @param $champsValid , on passera la variable au complet, car beaucoup de choses seront utilisées.
	 * @return array de valeurs true & false
	 */
	function validation($champs, $champsValid): array {
		
		if (empty($champs['nomOrganisateur'])) {
			$champsValid['nomOrganisateurVide'] = true;
		}
		else {
			// Lorsque nous avons une égalité, c'est signe que nous n'avons plus de prochaines small/big
			if ($champs['combinaison'] === $champs['maxCombinaison']) {
				$champsValid['aucuneValeurDispo'] = true;
			}
			
			if (count($champs['listeDesValeursCouleurs']) === 0) {
				$champsValid['aucuneValeurCouleur'] = true;
			}
			
			if ($champs['maxCombinaison'] === 0) {
				$champsValid['aucuneValeurSmallBig'] = true;
			}
			
			if (count($champs['listeDesOrganisateurs']) === 0) {
				$champsValid['aucunOrganisateur'] = true;
			}
		}
		
		foreach ($champsValid as $item => $value) {
			// aucuneValeurDispo ne doit pas être considéré comme une erreur mais plutôt comme un avertissement pour griser le bouton Changer Mise à la dernière mise
			if ($value && $item !== "aucuneValeurDispo") {
				$champsValid['erreurPossible'] = true;
				break;
			}
		}
		
		return $champsValid;
	}
	
	/**
	 * Retourne un chiffre pour une situation donnée qui sera affecté un message et envoyer en message AlertBox, au moment opportun
     *
	 * @param $champsValid
	 * @return int
	 */
	function situation($champsValid): int {
		
		$situation = 0;
		
		if ($champsValid['nomOrganisateurVide']) {
			$situation = 1;
		}
        elseif ($champsValid['aucuneValeurCouleur'] && !$champsValid['aucuneValeurSmallBig']) {
			$situation = 2;
		}
        elseif (!$champsValid['aucuneValeurCouleur'] && $champsValid['aucuneValeurSmallBig']) {
			$situation = 3;
		}
        elseif ($champsValid['aucuneValeurCouleur'] && $champsValid['aucuneValeurSmallBig']) {
			$situation = 4;
		}
		
		return $situation;
	}
	
	/**
	 * Retourne la couleur dynamiquement pour les valeurs «Small» et «Big» blind.
     *
	 * @param $couleurRouge
	 * @param $couleurVert
	 * @param $couleurBleu
	 * @return string
	 */
	function couleurValeurMise($couleurRouge, $couleurVert, $couleurBleu): string {
		
		return "rgb($couleurRouge, $couleurVert, $couleurBleu)";
	}
	
	/**
	 * Redirection vers la page d'erreur, car il y a eu un problème avec la page du timer
     *
	 * @return void
	 */
	#[NoReturn] function redirectionVersPageErreur(): void {
		
		header("Location: /erreur/erreur.php");
		exit;
	}
	
	/**
	 * Retourne à la page accueil en fonction de la langue du système
     *
	 * @return void
	 */
	#[NoReturn] function redirectionVersAccueil(): void {
		
		if ($_SERVER['typeLangue'] == 'francais') {
			header("Location: /index.html");
   
		} elseif ($_SERVER['typeLangue'] == 'english') {
			header("Location: /english/english.html");
		}
		
		exit; // Pour arrêter execution du code php
	}
	
	/**
	 * Retourne sur la page du timer + un message avertissement
     *
	 * @param $dictionnaire
	 * @param $situation
	 * @return void
	 */
	#[NoReturn] function redirectionVersTimer($dictionnaire, $situation): void {
		
		$message = messageSituation($dictionnaire, $situation);
		
		define('FRENCH_URL', "/timer/timer.php?langue=francais");
		define('ENGLISH_URL', "/timer/timer.php?langue=english");
		$url = "";
        
		if ($_SERVER['typeLangue'] === 'francais') {
			$url = FRENCH_URL;
   
		} elseif ($_SERVER['typeLangue'] === 'english') {
			$url = ENGLISH_URL;
		}
		
		echo "<script type='text/javascript'> alert(\"$message\"); window.location.replace('$url'); </script>";
		
		exit;
	}
	
	include_once("../includes/fct-connexion-bd.php");
	include_once("../includes/fct-traduction.php");
	include_once("../includes/fct-timer.php");
	
	const CHEMIN_DICTIONNAIRE_TIMER = '../dictionary/timer.json';
	
	// Les fonctions communes
	$connMYSQL = connexion();
	$champs = initialisationChamps();
	$champsValid = initialisationChampsValidation();
	$champs = remplissageChamps($connMYSQL, $champs);
	$champs['listeDesOrganisateurs'] = listeDesOrganisateurs($connMYSQL);
	$dictionnaire = recuperationContenuFichierJson(CHEMIN_DICTIONNAIRE_TIMER);
	
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if ($_SERVER['typeLangue'] !== "francais" && $_SERVER['typeLangue'] !== "english") {
			redirectionVersPageErreur();
		}
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($_SERVER['typeLangue'] !== "francais" && $_SERVER['typeLangue'] !== "english") {
			redirectionVersPageErreur();
		}
        elseif (isset($_POST['btnReturn'])) {
			redirectionVersAccueil();
		}
		else {
			// Si l'organisateur n'est pas vide, on va chercher ces valeurs / couleurs + sa combinaison en cours
			if (!empty($champs['nomOrganisateur'])) {
				$champs['listeDesValeursCouleurs'] = selectionValeursCouleurs($connMYSQL, $champs['nomOrganisateur']);
				
				$champs['nouvelleCombinaison'] = selectionSmallBigBlind($connMYSQL, $champs['nomOrganisateur'], $champs['combinaison']);
				$champs['combinaison']++; // On incrémente pour aller chercher la prochaine combinaison lors du prochain POST
			}
			
			$champsValid = validation($champs, $champsValid);
			$champs['situation'] = situation($champsValid);
			
			if ($champsValid['erreurPossible']) {
				redirectionVersTimer($dictionnaire, $champs['situation']);
			}
		}
	}
	
	// TODO : Vérifier si les appels Ajax marche encore malgré tous mes changements
	
	$connMYSQL->close();
?>

<!DOCTYPE html>
<html lang="<?php echo traduction("fr", $dictionnaire); ?>">
<head>
    <meta charset="utf-8"/>
    <meta name="compteur" content="timer"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fichier favicon.ico est une propriété du site web : https://pixabay.com/fr/radio-r%C3%A9veil-alarme-temps-horloge-295228/ -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="timer.css">
    <style>
        .container > .timer > .section-mises > .mises > div > .blind {
            color: <?php echo couleurValeurMise($champs['couleurs']['couleurRouge'], $champs['couleurs']['couleurVert'], $champs['couleurs']['couleurBleu']); ?>
        }
    </style>
    <title><?php echo traduction("Minuteur", $dictionnaire); ?></title>
</head>

<body>
<!-- Fichier alert.wav est une propriété du site web : https://www.memoclic.com/sons-wav/766-sonneries-et-alarmes/page-1.html -->
<audio id="alert-sound">
    <source src="alert.wav" type="audio/wav">
</audio>
<div class="container">
    <div class="tableau-bord">
        <form method="post" action="./timer.php" id="formulaire">
            <div class="choix">
                <input type="hidden" id="couleur-rouge" name="couleurRouge"
                       value="<?php echo $champs['couleurs']['couleurRouge']; ?>">
                <input type="hidden" id="couleur-vert" name="couleurVert"
                       value="<?php echo $champs['couleurs']['couleurVert']; ?>">
                <input type="hidden" id="couleur-bleu" name="couleurBleu"
                       value="<?php echo $champs['couleurs']['couleurBleu']; ?>">
                <input type="hidden" id="aucune-valeur-dispo"
					<?php if ($champsValid['aucuneValeurDispo']) { ?>
                        value="true"
					<?php } else { ?>
                        value="false"
					<?php } ?> >
                <input type="hidden" id="type-langue" name="typeLangue" value="<?php echo $_SERVER['typeLangue']; ?>">
                <input type="hidden" id="combinaison" name="combinaison" value="<?php echo $champs['combinaison']; ?>">
                <input type="hidden" id="max-combinaison" name="maxCombinaison"
                       value="<?php echo $champs['maxCombinaison']; ?>">
                <label for="choix-organisateur"><?php echo traduction("Veuillez choisir votre Organisateur", $dictionnaire); ?></label>
                <select id="choix-organisateur" name="choixOrganisateur">
					<?php if ($_SERVER['REQUEST_METHOD'] === 'GET') { ?>
                        <option value="" selected><?php echo traduction("Sélectionner", $dictionnaire); ?></option>
						<?php foreach ($champs['listeDesOrganisateurs'] as $unOrganisateur) { ?>
                            <option value="<?php echo $unOrganisateur['user']; ?>"><?php echo $unOrganisateur['name']; ?></option>
						<?php } ?>
					<?php } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') { ?>
						<?php foreach ($champs["listeDesOrganisateurs"] as $unOrganisateur) {
							if ($champs['nomOrganisateur'] === $unOrganisateur['user']) { ?>
                                <option value="<?php echo $unOrganisateur['user']; ?>" selected><?php echo $unOrganisateur['name']; ?>
                                </option>
							<?php } else { ?>
                                <option value="<?php echo $unOrganisateur['user']; ?>"><?php echo $unOrganisateur['name']; ?></option>
							<?php } ?>
						<?php } ?>
					<?php } ?>
                </select>
                <input type="submit" id="btn-choix-organisateur" name="btnChoixOrganisateur"
                       value="<?php echo traduction("Choisir", $dictionnaire); ?>"
					<?php if (count($champs['listeDesOrganisateurs']) === 0) { ?>
                        class="bouton disabled" disabled
					<?php } else { ?>
                        class="bouton"
					<?php } ?>>
            </div>
        </form>
        <div class="section-choix">
			<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $champs['situation'] != 1) { ?>
                <table class="tbl-valeur-couleur">
                    <thead>
                    <tr>
                        <th class="valeur-style"><?php echo traduction("Valeur", $dictionnaire); ?></th>
                        <th class="valeur-style"><?php echo traduction("Couleur", $dictionnaire); ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ($champs['listeDesValeursCouleurs'] as $uneCombinaison) { ?>
                        <tr>
                            <td class="valeur-style"><?php echo $uneCombinaison['amount']; ?></td>
                            <td class="<?php echo $uneCombinaison['color_english']; ?>"></td>
                        </tr>
					<?php } ?>
                    </tbody>
                </table>
			<?php } ?>
        </div>
    </div>
    <div class="timer">
        <div class="section-mises">
            <div class="mises">
                <div class="titre">
                    <p class="resize-text"><?php echo traduction("Mises Possibles", $dictionnaire); ?></p></div>
                <div class="petite">
                    <p class="resize-text"><?php echo traduction("Petite Mise", $dictionnaire); ?></p></div>
                <div class="grosse">
                    <p class="resize-text"><?php echo traduction("Grosse Mise", $dictionnaire); ?></p></div>
                <div class="petite">
                    <p class="blind" id="valeur-small"><?php echo $champs['nouvelleCombinaison']['valeurSmall']; ?></p></div>
                <div class="grosse">
                    <p class="blind" id="valeur-big"><?php echo $champs['nouvelleCombinaison']['valeurBig']; ?></p></div>
            </div>
            <div class="boutons-mises">
                <div>
                    <button name="btnChangerMise"
						<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' || $champsValid['nomOrganisateurVide'] || $champsValid['aucuneValeurDispo'] || $champsValid['aucuneValeurSmallBig']) { ?>
                            class="disabled" disabled
						<?php } ?> id="changer-mise" form="formulaire"><?php echo traduction("Changer", $dictionnaire); ?>
                    </button>
                </div>
                <div>
                    <button form="formulaire" name="btnResetMise" id="reset-mise"
						<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' || $champs['combinaison'] < 2 || $champsValid['aucuneValeurSmallBig']) { ?>
                            class="disabled" disabled
						<?php } ?> ><?php echo traduction("Réinitialiser Mise", $dictionnaire); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="section-temps">
            <div class="temps">
                <div class="periode">
                    <p class="resize-text"><?php echo traduction("Sélectionner votre Période", $dictionnaire); ?></p>
                </div>
                <div class="minutes">
                    <p class="resize-text"><?php echo traduction("Minutes", $dictionnaire); ?></p>
                </div>
                <div class="secondes">
                    <p class="resize-text"><?php echo traduction("Secondes", $dictionnaire); ?></p>
                </div>
                <div class="chiffre">
                    <p id="chiffre-min">00</p>
                </div>
                <div class="chiffre">
                    <p id="chiffre-sec">00</p>
                </div>
            </div>
            <div class="boutons-temps">
                <div>
                    <button class="" id="timer-15">15</button>
                </div>
                <div>
                    <button class="" id="timer-30">30</button>
                </div>
                <div class="action-temps">
                    <button class="disabled" disabled id="timer-stop"><?php echo traduction("Arrêt", $dictionnaire); ?></button>
                </div>
                <div class="action-temps">
                    <button class="disabled" disabled
                            id="timer-reprend"><?php echo traduction("Poursuivre", $dictionnaire); ?></button>
                </div>
                <div class="action-temps">
                    <button class="disabled" disabled
                            id="reset-temps"><?php echo traduction("Réinitialiser Temps", $dictionnaire); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="bouton-retour">
    <div>
        <input type="submit" id="btn-return" name="btnReturn" form="formulaire"
               value="<?php echo traduction("Page d'Accueil", $dictionnaire); ?>">
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery/jquery-3.2.1.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
<script src="detect-zoom.js"></script>
<script src="detect-zoom.min.js"></script>
<script src="timer.js"></script>

</body>

</html>
