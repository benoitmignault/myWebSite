<?php
	
	/**
	 * Retourne un array de variables qui seront utilisées pour le timer
	 * @return array
	 */
	function initialisationChamps(): array {
		
		return ["typeLangue" => "", "nomOrganisateur" => "", "situation" => 0, "combinaison" => 0, "maxCombinaison" => 0,
		        "valeurSmall" => 0, "valeurBig" => 0, "numberRed" => 255, "numberGreen" => 255, "numberBlue" => 255,
		        "listeDesOrganisateurs" => array(), "listeDesValeursCouleurs" => array(), "nouvelleCombinaison" => array('valeurSmall' => "00", 'valeurBig' => "00")];
	}
	
	/**
	 * Retourne un array de variables toutes à false pour commencer qui seront les chiens de garde du bon fonctionnement
	 * @return false[]
	 */
	function initialisationChampsValidation(): array {
		
		return ["nomOrganisateurVide" => false, "aucuneValeurSmallBig" => false, "aucuneValeurCouleur" => false,
		        "aucuneValeurDispo" => false, "aucunOrganisateur" => false];
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
			$valeur = "Valeur";
			$couleur = "Couleur";
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
			$valeur = "Value";
			$couleur = "Color";
		}
		
		return ["lang" => $lang, "title" => $title, "typeMise" => $typeMise, "changerMise" => $changer,
		        "choixOrganisateur" => $choixOrganisateur, "option" => $option, "btnChoix" => $btnChoix, "message" => $message,
		        "messageErreurBD" => $messageErreurBD, "small" => $small, "big" => $big, "retour" => $retour, "btnReset" => $btnReset,
		        "reset" => $reset, "periode" => $periode, "btnReprendre" => $btnReprendre, "valeur" => $valeur, "couleur" => $couleur];
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
	
	/**
	 * Remplissage des variables en fonction de quels boutons a été sélectionné
	 * @param $connMYSQL
	 * @param $champs
	 * @return array
	 */
	function remplissageChamps($connMYSQL, $champs): array {
		
		// Remplissage des variables si on passe par le GET
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			if (isset($_GET['langue'])) {
				$champs['typeLangue'] = $_GET['langue'];
			}
		} // Remplissage des variables si on passe par le POST
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
			// Assignation de la variable langue est commune rendu ici
			if (isset($_POST['typeLangue'])) {
				$champs['typeLangue'] = $_POST['typeLangue'];
			}
			// Remplissage des variables si on passe par le choix de l'organisateur
			if (isset($_POST['btnChoixOrganisateur'])) {
				if (isset($_POST['choixOrganisateur'])) {
					$champs['nomOrganisateur'] = $_POST['choixOrganisateur'];
					// Au moment de setter l'organisateur, on va chercher une seule fois son nombre de combinaisons totales.
					$champs['maxCombinaison'] = recupererMaxCombinaison($connMYSQL, $champs['nomOrganisateur']);
				}
			} // Remplissage des variables si on passe par changer de mise
            elseif (isset($_POST['btnChangerMise'])) {
				// Au moment de changer les mise, on récupérer la valeur du nombre max de combinaisons
				if (isset($_POST['maxCombinaison'])) {
					$champs['maxCombinaison'] = intval($_POST['maxCombinaison']);
				}
				// On préparer la prochaine combinaison qui s'envient
				$champs['combinaison'] = intval($_POST['combinaison']);
				// On récupère les trois types de couleurs
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
				} // Sinon, Si la partie rouge sont au dessus de 0 avec la diminution de 25, on réduit le vert et bleu de 25.
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
			} // Remplissage des variables si on passe par une remise à 0 des mises
            elseif (isset($_POST['btnResetMise'])) {
				$champs['combinaison'] = 0;
				$champs['numberRed'] = 255;
				$champs['numberGreen'] = 255;
				$champs['numberBlue'] = 255;
			}
		}
		
		return $champs;
	}
	
	/**
	 * Retourne la liste des organisateurs
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
					$listeDesValeursCouleurs[] = array("amount" => $row['amount'], "color_english" => $row['color_english']);
				}
			}
		}
		
		// Fermeture de la requête
		$stmt->close();
		
		return $listeDesValeursCouleurs;
	}
	
	/**
	 * Retourne le nombre maximale de combinaisons possible
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
		}
	}
	
	
	/**
	 * Vérifier que tous c'est bien passé
	 * @param $champs
	 * @param $champsValid
	 * @return mixed de valeurs true & false
	 */
	function validation($champs, $champsValid) {
		
		if (empty($champs['nomOrganisateur'])) {
			$champsValid["nomOrganisateurVide"] = true;
		}
		
		if ($champs['combinaison'] === $champs['maxCombinaison']) {
			$champsValid["aucuneValeurDispo"] = true;
		}
		
		if (count($champs["listeDesValeursCouleurs"]) === 0) {
			$champsValid["aucuneValeurCouleur"] = true;
		}
		
		if ($champs['maxCombinaison'] === 0) {
			$champsValid["aucuneValeurSmallBig"] = true;
		}
		
		if (count($champs["listeDesOrganisateurs"]) === 0) {
			$champsValid["aucunOrganisateur"] = true;
		}
		
		return $champsValid;
	}
	
	// TODO
	function situation($champs): int {
		
		
		return 0;
	}
	
	
	function coloriage($champs): string {
		
		return "rgb({$champs['numberRed']},{$champs['numberGreen']},{$champs['numberBlue']})";
	}
	
	/**
	 * Redirection vers la page d'erreur, car il y a eu un problème avec la page du timer
	 * @return void
	 */
	function redirectionVersPageErreur() {
		
		header("Location: /erreur/erreur.php");
		exit;
	}
	
	/**
	 * Retourne à la page accueil en fonction de la langue pré-sélectionner
	 * @param $typeLangue
	 * @return void
	 */
	function redirectionVersAccueil($typeLangue) {
		
		if ($typeLangue == 'francais') {
			header("Location: /index.html");
		}
        elseif ($typeLangue == 'english') {
			header("Location: /english/english.html");
		}
		
		exit; // Pour arrêter execution du code php
	}
	
	/**
	 * Retourne sur la page du timer en fonction de la langue pré-sélectionner
	 * @param $typeLangue
	 * @return void
	 */
	function redirectionVersTimer($typeLangue) {
		
		if ($typeLangue === 'francais') {
			header("Location: /timer/timer.php?langue=francais");
		}
        elseif ($typeLangue === 'english') {
			header("Location: /timer/timer.php?langue=english");
		}
		
		exit; // pour arrêter execution du code php
	}
	
	// TODO : PHP-DOC
	function afficherMsgAlert() {
		
		$msgErr = "";
		
		if ($typeLangue == "francais") {
			$msgErr = "Attention ! Votre organisateur n'a pas choisi ses valeurs associées à ses jetons.\\nVeuillez le contacter pour lui demander de bien vouloir créer ses ensembles valeur couleur de jetons.";
		}
        elseif ($typeLangue == "english") {
			$msgErr = "Warning ! Your organizer did not choose his values associated with his chips.\\nPlease contact him to ask him to create his value color sets of tokens.";
		}
		//var_dump("Test"); exit;
		
		
		echo "<script>alert(\"$msgErr\")</script>";
		
		
		$msgErr = "";
		if ($champs["typeLangue"] == "francais") {
			$msgErr = "Attention ! Votre organisateur n'a pas choisi ses valeurs associées aux mises petites et grosses mises.\\nVeuillez le contacter pour lui demander de bien vouloir créer ses ensembles petite mise et grosse mise.";
		}
        elseif ($champs["typeLangue"] == "english") {
			$msgErr = "Warning ! Your organizer has not chosen his values associated with small and large bets.\\nPlease contact him to ask him to create his sets small bet and big bet.";
		}
		echo "<script>alert(\"$msgErr\")</script>";
		
		
	}
	
	
	include_once("../includes/fct-connexion-bd.php");
	
	// Les fonctions communes
	$connMYSQL = connexion();
	$champs = initialisationChamps();
	$champsValid = initialisationChampsValidation();
	$champs = remplissageChamps($connMYSQL, $champs);
	$champs["listeDesOrganisateurs"] = listeDesOrganisateurs($connMYSQL);
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if ($champs['typeLangue'] !== "francais" && $champs['typeLangue'] !== "english") {
			redirectionVersPageErreur();
		}
		else {
			$champsMots = traduction($champs);
		}
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($champs['typeLangue'] !== "francais" && $champs['typeLangue'] !== "english") {
			redirectionVersPageErreur();
		}
        elseif (isset($_POST['btnReturn'])) {
			redirectionVersAccueil($champs["typeLangue"]);
		}
		else {
			
			//var_dump($query); exit;
			// Si l'organisateur n'est pas vide, on va chercher ces valeurs / couleurs + sa combinaison en cours
			if (!empty($champs['nomOrganisateur'])) {
				$champs['listeDesValeursCouleurs'] = selectionValeursCouleurs($connMYSQL, $champs['nomOrganisateur']);
				
				include_once("../includes/fct-timer.php");
				$champs['nouvelleCombinaison'] = selectionSmallBigBlind($connMYSQL, $champs['nomOrganisateur'], $champs['combinaison']);
				$champs['combinaison']++; // On incrémente pour aller chercher la prochaine combinaison lors du prochain POST
			}
			
			$champsValid = validation($champs, $champsValid);
            // TODO : ces eux fonction
			$champs['situation'] = situation($champs);
			$champsMots = traduction($champs);
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
                <input form="formulaire" type="hidden" id="tropValeur" name="tropValeur" value="
                <?php if ($champsValid['aucuneValeurDispo']) {
					echo "true";
				} else {
					echo "false";
				} ?>">
                <input form="formulaire" type="hidden" id="typeLangue" name="typeLangue" value="<?php echo $champs['typeLangue']; ?>">
                <input form="formulaire" type="hidden" class="combinaison" name="combinaison" value="<?php echo $champs['combinaison']; ?>">
                <input form="formulaire" type="hidden" class="maxCombinaison" name="maxCombinaison"
                       value="<?php echo $champs['maxCombinaison']; ?>">
                <label class="modificationColor" for="choixOrganisateur"><?php echo $champsMots['choixOrganisateur']; ?></label>
                <select id="choixOrganisateur" name="choixOrganisateur">
					<?php if ($_SERVER['REQUEST_METHOD'] === 'GET') { ?>
                        <option value="" selected><?php echo $champsMots['option']; ?></option>
						<?php foreach ($champs["listeDesOrganisateurs"] as $unOrganisateur) { ?>
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
                <input class="bouton" type="submit" name="btnChoixOrganisateur" value="<?php echo $champsMots['btnChoix']; ?>">
            </div>
        </form>
        <div class="affichage_choix">
			<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $champs['situation'] != 1) { ?>
                <table class="tblValeurCouleur">
                    <thead>
                    <tr>
                        <th><?php echo $champsMots['valeur']; ?></th>
                        <th><?php echo $champsMots['couleur']; ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ($champs["listeDesValeursCouleurs"] as $uneCombinaison) { ?>
                        <tr>
                            <td class="colorModifie"><?php echo $uneCombinaison['amount']; ?></td>
                            <td class="<?php echo $uneCombinaison['color_english']; ?>"></td>
                        </tr>
					<?php } ?>
                    </tbody>
                </table>
			<?php } ?>
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
                    <p class="blind" id="valeurSmall"><?php echo $champs['nouvelleCombinaison']['valeurSmall'] ?></p>
                </div>
                <div class="valeurBig">
                    <p class="blind" id="valeurBig"><?php echo $champs['nouvelleCombinaison']['valeurBig'] ?></p>
                </div>
            </div>
            <div class="lesBoutonsMises">
                <div class="double">
                    <button name="btnChangerMise"
						<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' || $champsValid['nomOrganisateurVide'] || $champsValid['aucuneValeurDispo'] || $champsValid['aucuneValeurSmallBig']) { ?>
                            class="disabled" disabled
						<?php } ?> id="changerMise" form="formulaire"><?php echo $champsMots['changerMise'] ?>
                    </button>
                </div>
                <div class="resetMise">
                    <button form="formulaire" name="btnResetMise"
						<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' || $champs['combinaison'] < 2 || $champsValid['aucuneValeurSmallBig']) { ?>
                            class="disabled" disabled
						<?php } ?> id="reset"><?php echo $champsMots['reset'] ?>
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
                    <button <?php if ($champsValid['aucuneValeurDispo']) { ?> class="disabled" disabled <?php } ?> id="timer15">15</button>
                </div>
                <div class="min30">
                    <button <?php if ($champsValid['aucuneValeurDispo']) { ?> class="disabled" disabled <?php } ?> id="timer30">30</button>
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
        <input class="resizeText" type="submit" name="btnReturn" form="formulaire" value="<?php echo $champsMots['retour']; ?>">
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
<script src="detect-zoom.js"></script>
<script src="detect-zoom.min.js"></script>
<script src="timer.js"></script>

</body>

</html>
