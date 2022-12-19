<?php
	function initialisationChamps(): array {
		
		return ["typeLangue" => "", "user" => "", "nomOrganisateur" => "", "situation" => 0, "combinaison" => 0, "maxCombinaison" => 0,
		        "valeurSmall" => "00", "valeurBig" => "00", "aucuneValeur" => false, "tropValeur" => false, "numberRed" => 255,
		        "numberGreen" => 255, "numberBlue" => 255, "listeDesOrganisateurs" => array()];
	}
	
	function initialisationValidation(): array {
		
		return ["userVide" => false, "changementMise" => false, "resetMise" => false, "choixUser" => false];
	}
	
	function traduction($champs): array {
		if ($champs['typeLangue'] === "francais") {
			$lang = "fr";
			$title = 'Minuteur';
			$typeMise = 'Les mises possibles';
			$small = 'La petite mise';
			$big = 'La grosse mise';
			$retour = 'Page d\'Accueil';
			$changer = 'Changer mise';
			$btnReset = 'Reset du Temps';
			$reset = 'Reset des mises';
			$periode = 'En attente d\'une période de temps...';
			$btnReprendre = 'POURSUIVRE';
			$choixOrganisateur = 'Veuillez choisir votre organisateur';
			$option = "À sélectionner";
			$btnChoix = 'Choisir';
			$message = messageSituation($champs);
			$messageErreurBD = "Il y a eu un problème avec l'insertion de vos valeurs dans la BD. Veuillez recommencer !";
		}
        elseif ($champs['typeLangue'] === "english") {
			$title = 'Timer';
			$lang = "en";
			$choixOrganisateur = 'Please choose an organizer';
			$typeMise = 'Bets available';
			$small = 'The small blind';
			$big = 'The big blind';
			$retour = 'HOME';
			$changer = 'Change bet';
			$btnReset = 'Time Reset';
			$reset = 'Bets Reset';
			$periode = 'Waiting for a period of time...';
			$btnReprendre = 'GO ON';
			$option = "Select";
			$btnChoix = 'PICK OUT';
			$message = messageSituation($champs);
			$messageErreurBD = "There was a problem with insert your values into the DB. Please try again !";
		}
		
		return ["lang" => $lang, 'title' => $title, 'typeMise' => $typeMise, 'changerMise' => $changer,
		        'choixOrganisateur' => $choixOrganisateur, 'option' => $option, 'btnChoix' => $btnChoix, 'message' => $message,
		        'messageErreurBD' => $messageErreurBD, 'small' => $small, 'big' => $big, 'retour' => $retour, 'btnReset' => $btnReset,
		        'reset' => $reset, 'periode' => $periode, 'btnReprendre' => $btnReprendre];
	}
	
	function messageSituation($champs): string {
		$message = "";
		if ($champs["typeLangue"] === 'francais') {
			if ($champs['situation'] == 1) {
				$message = "Votre choix organisateur ne peut être null !";
			}
		}
        elseif ($champs["typeLangue"] === 'english') {
			if ($champs['situation'] == 1) {
				$message = "Your organizer choice can not be null !";
			}
		}
		
		return $message;
	}
	
	function remplissageChamps($champs, $connMYSQL) {
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			if (isset($_GET['langue'])) {
				$champs['typeLangue'] = $_GET['langue'];
			}
		}
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['btnReturn'])) {
				if (isset($_POST['typeLangueReturn'])) {
					$champs['typeLangue'] = $_POST['typeLangueReturn'];
				}
			}
			else {
				$champs['typeLangue'] = $_POST['typeLangue'];
				$champs['user'] = $_POST['choixOrganisateur'];
				if (isset($_POST['choixOrganisateur'])) {
					// Au moment de setter l'organisateur, on va chercher une seule fois son nombre de combinaisons totales.
					$champs['maxCombinaison'] = recupererMaxCombinaisonUser($connMYSQL, $champs['user']);
				}
				
				if (isset($_POST['btnChangerMise'])) {
					// Au moment de changer les mise, on récupérer la valeur du nombre max de combinaisons
					if (isset($_POST['maxCombinaison'])) {
						$champs['maxCombinaison'] = intval($_POST['maxCombinaison']);
					}
					
					$champs['combinaison'] = intval($_POST['combinaison']);
					$champs['combinaison']++;
					if (isset($_POST['numberRed'])) {
						$champs['numberRed'] = intval($_POST['numberRed']);
					}
					if (isset($_POST['numberGreen'])) {
						$champs['numberGreen'] = intval($_POST['numberGreen']);
					}
					if (isset($_POST['numberBlue'])) {
						$champs['numberBlue'] = intval($_POST['numberBlue']);
					}
					$valueRedTemp = $champs['numberRed'] - 25;
					$valueGreenTemp = $champs['numberGreen'] - 25;
					// Si la partie bleu et vert sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
					if ($valueGreenTemp > 0) {
						$champs['numberGreen'] = $valueGreenTemp;
						$champs['numberBlue'] = $valueGreenTemp;
						// Si la partie rouge sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
					}
                    elseif ($valueRedTemp > 0) {
						$champs['numberRed'] = $valueRedTemp;
						$champs['numberGreen'] = 0;
						$champs['numberBlue'] = 0;
					}
					else {
						$champs['numberRed'] = 0;
						$champs['numberGreen'] = 0;
						$champs['numberBlue'] = 0;
					}
				}
                elseif (isset($_POST['btnResetMise'])) {
					$champs['combinaison'] = 0;
					$champs['numberRed'] = 255;
					$champs['numberGreen'] = 255;
					$champs['numberBlue'] = 255;
				}
			}
		}
		return $champs;
	}
	
	// Optimisation de ma function avec OpenAI
	function recupererMaxCombinaisonUser($connMYSQL, $user) {
		// Définition de constantes pour les chaînes de caractères statiques
		define('SELECT_MAX_COMBINAISON_USER', 'number_of_records');
		define('FROM_MAX_COMBINAISON_USER', 'mise_small_big');
		define('WHERE_MAX_COMBINAISON_USER', 'user');
		
		// Préparation de la requête SQL avec un alias pour la colonne sélectionnée
		$query = "SELECT count(*) as " . SELECT_MAX_COMBINAISON_USER . " FROM " . FROM_MAX_COMBINAISON_USER . " where " . WHERE_MAX_COMBINAISON_USER . " = ?";
		
		$stmt = $connMYSQL->prepare($query);
		
		// Définissez les paramètres de la requête - Déjà défini
		
		// Les & est la référence de la variable que je dois passer en paramètre
		$params = array("s", &$user);
		
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
		}
	}
	
	function coloriage($champs): string {
		
		return "rgb({$champs['numberRed']},{$champs['numberGreen']},{$champs['numberBlue']})";
	}
	
	function validation($champs, $champsValid) {
		if ($champs['user'] === "") {
			$champsValid["userVide"] = true;
		}
		if (isset($_POST['btnChangerMise'])) {
			$champsValid["changementMise"] = true;
		}
        elseif (isset($_POST['btnResetMise'])) {
			$champsValid["resetMise"] = true;
		}
        elseif (isset($_POST['btnChoixOrganisateur'])) {
			$champsValid["choixUser"] = true;
		}
		
		return $champsValid;
	}
	
	function situation($champsValid): int {
		$situation = 0;
		if ($champsValid['userVide']) {
			$situation = 1; // Le user ne peut être vide
		}
        elseif ($champsValid['changementMise']) {
			$situation = 2; // Un changement de mise a été demandé
		}
        elseif ($champsValid['resetMise']) {
			$situation = 3; // Un reset des mises a été demandé
		}
        elseif ($champsValid['choixUser']) {
			$situation = 4; // Un choix de user est fait
		}
		
		return $situation;
	}
	
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
		
		//call_user_func_array(array($stmt, "bind_param"), $params) ----> N'est pas utilisée vue qu'il n'y a pas de paramètres
		
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
		
		/*
		$listeDesOrganisateurs = "";
		$sql = "SELECT * FROM login_organisateur order by name";
		$result = $connMYSQL->query($sql);
		if ($result->num_rows > 0) {
			if ($_SERVER['REQUEST_METHOD'] == 'GET') {
				$listeDesOrganisateurs .= "<option value=\"\" selected>{$champsMots['option']}</option>";
				foreach ($result as $row) {
					$listeDesOrganisateurs .= "<option value=\"{$row['user']}\">{$row['name']}</option>";
				}
			}
            elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$listeDesOrganisateurs .= "<option value=\"\">{$champsMots['option']}</option>";
				foreach ($result as $row) {
					if ($champs['user'] === $row['user']) {
						$listeDesOrganisateurs .= "<option value=\"{$row['user']}\" selected>{$row['name']}</option>";
					}
					else {
						$listeDesOrganisateurs .= "<option value=\"{$row['user']}\">{$row['name']}</option>";
					}
				}
			}
		}*/
		
		return $listeDesOrganisateurs;
	}
	
	function affichageNomOrganisateur($connMYSQL, $champs) {
		$prenom = "";
		/* Crée une requête préparée */
		$stmt = $connMYSQL->prepare("SELECT name FROM login_organisateur where user =? ");
		
		/* Lecture des marqueurs */
		$stmt->bind_param("s", $champs['user']);
		
		/* Exécution de la requête */
		$stmt->execute();
		
		/* Association des variables de résultat */
		$result = $stmt->get_result();
		
		$row = $result->fetch_array(MYSQLI_ASSOC);
		// Close statement
		$stmt->close();
		
		if ($result->num_rows > 0) {
			$prenom = $row['name'];
		}
		return $prenom;
	}
	
	/**
	 * On vient récupérer la liste des valeurs / couleurs de l'organisation
	 * @param $connMYSQL
	 * @param $champs
	 * @return string
	 */
	function selectionValeurCouleur($connMYSQL, $champs): string {
		// Définition de constantes pour les chaînes de caractères statiques
		define('SELECT_SELECTION_VALEUR_COULEUR', 'amount, color_english');
		define('FROM_SELECTION_VALEUR_COULEUR', 'amount_color');
		define('WHERE_SELECTION_VALEUR_COULEUR', 'user');
		define('ORDER_SELECTION_VALEUR_COULEUR', 'amount');
		$tableau = ""; // Création du tableau HTML qui sera afficher à la sortie
		
		// Préparation de la requête SQL avec un alias pour la colonne sélectionnée
		$query = "SELECT " . SELECT_SELECTION_VALEUR_COULEUR . " FROM " . FROM_SELECTION_VALEUR_COULEUR . " WHERE " . WHERE_SELECTION_VALEUR_COULEUR . " = ? " . " ORDER BY " . ORDER_SELECTION_VALEUR_COULEUR;
		
		$stmt = $connMYSQL->prepare($query);
		
		// Liage des paramètres de la requête
		$stmt->bind_param("s", $champs['user']);
		
		// Exécution de la requête
		$stmt->execute();
		
		// Vérification de l'exécution de la requête
		if (!$stmt->errno) {
			// Récupération du résultat de la requête
			$result = $stmt->get_result();
			$row_cnt = $result->num_rows;
			
			if ($row_cnt > 0) {
				if ($champs["typeLangue"] == "francais") {
					$tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Valeur</th><th>Couleur</th></tr></thead>";
				}
                elseif ($champs["typeLangue"] == "english") {
					$tableau .= "<table class=\"tblValeurCouleur\"><thead><tr><th>Value</th><th>Color</th></tr></thead>";
				}
				$tableau .= "<tbody>";
				
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$tableau .= "<tr><td class=\"colorModifie\">{$row['amount']}</td> <td class=\"{$row['color_english']}\"></td> </tr>";
				}
				$tableau .= "</tbody></table>";
			}
		}
		// Fermeture de la requête
		$stmt->close();
		
		// Retour du tableau HTML
		return $tableau;
	}
	
	function redirection($champs) {
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			header("Location: /erreur/erreur.php");
		}
        elseif (isset($_POST['btnReturn'])) {
			if ($champs["typeLangue"] == 'english') {
				header("Location: /english/english.html");
			}
            elseif ($champs["typeLangue"] == 'francais') {
				header("Location: /index.html");
			}
		}
		else {
			header("Location: /erreur/erreur.php");
		}
		exit; // pour arrêter execution du code php
	}
	
	include_once("../includes/fct-connexion-bd.php");
	
	// Les fonctions communes
	$connMYSQL = connexion();
	$champs = initialisationChamps();
	$champs = remplissageChamps($champs, $connMYSQL);
	$champs["listeDesOrganisateurs"] = listeDesOrganisateurs($connMYSQL);
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if ($champs['typeLangue'] !== "francais" && $champs['typeLangue'] !== "english") {
			redirection($champs);
		}
		else {
			$champsMots = traduction($champs);
		}
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$champsValid = initialisationValidation();
		
		if ($champs['typeLangue'] !== "francais" && $champs['typeLangue'] !== "english" || isset($_POST['btnReturn'])) {
			redirection($champs);
		}
		else {
			$champsValid = validation($champs, $champsValid);
			$champs['situation'] = situation($champsValid);
			$champsMots = traduction($champs);
			
			if ($champs['situation'] === 1) {
				echo "<script>alert('" . $champsMots['message'] . "')</script>";
			}
			else {
				$champs['nomOrganisateur'] = affichageNomOrganisateur($connMYSQL, $champs);
				$tableauValeurCouleur = selectionValeurCouleur($connMYSQL, $champs);
				if ($tableauValeurCouleur === "") {
					$msgErr = "";
					if ($champs["typeLangue"] == "francais") {
						$msgErr = "Attention ! Votre organisateur n'a pas choisi ses valeurs associées à ses jetons.\\nVeuillez le contacter pour lui demander de bien vouloir créer ses ensembles valeur couleur de jetons.";
					}
                    elseif ($champs["typeLangue"] == "english") {
						$msgErr = "Warning ! Your organizer did not choose his values associated with his chips.\\nPlease contact him to ask him to create his value color sets of tokens.";
					}
					echo "<script>alert(\"$msgErr\")</script>";
				}
				else {
					include_once("../includes/fct-timer.php");
					$champs = selectionSmallBigBlind($connMYSQL, $champs);
					if ($champs['aucuneValeur']) {
						$msgErr = "";
						if ($champs["typeLangue"] == "francais") {
							$msgErr = "Attention ! Votre organisateur n'a pas choisi ses valeurs associées aux mises petites et grosses mises.\\nVeuillez le contacter pour lui demander de bien vouloir créer ses ensembles petite mise et grosse mise.";
						}
                        elseif ($champs["typeLangue"] == "english") {
							$msgErr = "Warning ! Your organizer has not chosen his values associated with small and large bets.\\nPlease contact him to ask him to create his sets small bet and big bet.";
						}
						echo "<script>alert(\"$msgErr\")</script>";
					}
				}
			}
		}
	}
	
	$connMYSQL->close();
?>

<!DOCTYPE html>
<html lang="<?php
	echo $champsMots['lang']; ?>">

<head>
    <meta charset="utf-8"/>
    <meta name="compteur" content="timer"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fichier favicon.ico est une propriété du site web : https://pixabay.com/fr/radio-r%C3%A9veil-alarme-temps-horloge-295228/ -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="timer.css">
    <style>
        body {
            margin: 0;
            /* https://pxhere.com/fr/photo/1280141 */
            background-image: url("timer.jpg");
            background-position: center;
            background-attachment: fixed;
            background-size: 100%;
        }

        .container > .timer > .tableauDesMises > .lesMises > div > .blind {
            color: <?php echo coloriage($champs); ?>
        }

    </style>
    <title><?php echo $champsMots['title'] ?></title>
</head>

<body>
<!-- Fichier alert.wav est une propriété du site web : https://www.memoclic.com/sons-wav/766-sonneries-et-alarmes/page-1.html -->
<audio id="alertSound">
    <source src="alert.wav" type="audio/wav">
</audio>
<div class="container">
    <div class="tableau_bord">
        <form method="post" action="./timer.php" id="formulaire">
            <div class="choix">
                <input form="formulaire" type="hidden" id="numberRed" name="numberRed" value="<?php echo $champs['numberRed']; ?>">
                <input form="formulaire" type="hidden" id="numberGreen" name="numberGreen" value="<?php echo $champs['numberGreen']; ?>">
                <input form="formulaire" type="hidden" id="numberBlue" name="numberBlue" value="<?php echo $champs['numberBlue']; ?>">
                <input form="formulaire" type="hidden" id="tropValeur" name="tropValeur" value="<?php if ($champs['tropValeur']) {
					echo "true";
				}
				else {
					echo "false";
				} ?>">
                <input form="formulaire" type="hidden" id="typeLangue" name="typeLangue" value="<?php echo $champs['typeLangue']; ?>">
                <input form="formulaire" type="hidden" class="combinaison" name="combinaison" value="<?php echo $champs['combinaison']; ?>">
                <input form="formulaire" type="hidden" class="maxCombinaison" name="maxCombinaison"
                       value="<?php echo $champs['maxCombinaison']; ?>">
                <label class="modificationColor" for="choixOrganisateur"><?php echo $champsMots['choixOrganisateur']; ?></label>
                <select id="choixOrganisateur" name="choixOrganisateur">
					<?php echo $listeDesOrganisateurs; ?>
                </select>
                <input class="bouton" type="submit" name="btnChoixOrganisateur" value="<?php echo $champsMots['btnChoix']; ?>">
            </div>
        </form>
        <div class="affichage_choix">
			<?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && $champs['situation'] != 1) {
				echo $tableauValeurCouleur;
			} ?>
        </div>
    </div>
    <div class="timer">
        <div class="tableauDesMises">
            <div class="lesMises">
                <div class="titre">
                    <p class="resizeText"><?php echo $champsMots['typeMise'] ?></p>
                </div>
                <div class="small">
                    <p class="resizeText"><?php echo $champsMots['small'] ?></p>
                </div>
                <div class="big">
                    <p class="resizeText"><?php echo $champsMots['big'] ?></p>
                </div>
                <div class="valeurSmall">
                    <p class="blind" id="valeurSmall"><?php echo $champs['valeurSmall'] ?></p>
                </div>
                <div class="valeurBig">
                    <p class="blind" id="valeurBig"><?php echo $champs['valeurBig'] ?></p>
                </div>
            </div>
            <div class="lesBoutonsMises">
                <div class="double">
                    <button name="btnChangerMise"
						<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' || $champs['situation'] === 1 || $champs['tropValeur'] || $champs['aucuneValeur']) { ?>
                            class="disabled" disabled <?php } ?> id="changerMise"
                            form="formulaire"><?php echo $champsMots['changerMise'] ?>
                    </button>
                </div>
                <div class="resetMise">
                    <button form="formulaire" name="btnResetMise"
						<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' || $champs['combinaison'] < 1 || $champs['aucuneValeur']) { ?>
                            class="disabled" disabled <?php } ?>
                            id="reset"><?php echo $champsMots['reset'] ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="tableauDuTemps">
            <div class="temps">
                <div class="periode">
                    <p class="resizeText"><?php echo $champsMots['periode'] ?></p>
                </div>
                <div class="minutes">
                    <p class="resizeText">Minutes</p>
                </div>
                <div class="secondes">
                    <p class="resizeText">Secondes</p>
                </div>
                <div class="chiffreMin">
                    <p>00</p>
                </div>
                <div class="chiffreSec">
                    <p>00</p>
                </div>
            </div>
            <div class="lesBoutonsActions">
                <div class="min15">
                    <button <?php if ($champs['aucuneValeur']) { ?> class="disabled" disabled <?php } ?> id="timer15">15</button>
                </div>
                <div class="min30">
                    <button <?php if ($champs['aucuneValeur']) { ?> class="disabled" disabled <?php } ?> id="timer30">30</button>
                </div>
                <div class="stop">
                    <button class="disabled" disabled id="timerStop">STOP</button>
                </div>
                <div class="reprend">
                    <button class="disabled resizeText" disabled id="timerReprend"><?php echo $champsMots['btnReprendre'] ?></button>
                </div>
                <div class="resetTemps">
                    <button class="disabled" disabled id="ResetTemps"><?php echo $champsMots['btnReset'] ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="boutonRetour">
    <div class="retour">
        <form method="post" action="./timer.php">
            <input class="resizeText" type="submit" name="btnReturn" value="<?php echo $champsMots['retour']; ?>">
            <input type="hidden" name="typeLangueReturn" value="<?php echo $champs['typeLangue']; ?>">
        </form>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
<script src="detect-zoom.js"></script>
<script src="detect-zoom.min.js"></script>
<script src="timer.js"></script>

</body>

</html>
