<?php
	// Modernisation des envoies de courriels venant des gens qui tente de me contacter
	
	// Les includes nécessaires
	include_once("../includes/fct-divers.php");
	
	// Import PHPMailer classes into the global namespace
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	
	// Load Composer's autoloader
	require '../../vendor/autoload.php';
	
	/**
	 * Fonction qui va contenir tous ce dont on aura besoin.
	 * Une partie des variables de type string ou integer et une autre partie en boolean
	 *
	 * @return array
	 */
	function initialisation(): array {
		
		return array("longueur_nom" => 0, "longueur_email" => 0, "longueur_sujet" => 0, "longueur_message" => 0,
		             "nom" => "", "email" => "", "sujet" => "", "message" => "",
		             "champs_vide" => false, "champs_trop_long" => false, "champ_email_invalid" => false,
		             "champ_nom_vide" => false, "champ_email_vide" => false, "champ_message_vide" => false, "champ_sujet_vide" => false,
		             "champ_nom_trop_long" => false, "champ_email_trop_long" => false, "champ_message_trop_long" => false, "champ_sujet_trop_long" => false,
		             "erreur_presente" => false);
	}
	
	/**
	 * Fonction pour setter les informations que l'utilisateur du site aurait remplies
	 * Aussi, on va récupérer via le POST, les informations
	 *
	 * @param array $array_Champs
	 * @return array
	 */
	function remplisage_champs(array $array_Champs): array {
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			
			// Si le nom est présent, on va associer la variable
			if (isset($_POST['nom'])) {
				
				// Si le champ du nom n'est pas vide, on l'associe
				if (!empty($_POST['nom'])) {
					
					$array_Champs['nom'] = $_POST['nom'];
					$array_Champs['longueur_nom'] = strlen($array_Champs['nom']);
				}
			}
			
			// Si le email est présent, on va associer la variable
			if (isset($_POST['email'])) {
				
				// Si le champ du Email n'est pas vide, on l'associe
				if (!empty($_POST['email'])) {
					
					$array_Champs['email'] = $_POST['email'];
					$array_Champs['longueur_email'] = strlen($array_Champs['email']);
				}
			}
			
			// Si le sujet est présent, on va associer la variable
			if (isset($_POST['sujet'])) {
				
				// Si le champ du Email n'est pas vide, on l'associe
				if (!empty($_POST['sujet'])) {
					
					$array_Champs['sujet'] = $_POST['sujet'];
					$array_Champs['longueur_sujet'] = strlen($array_Champs['sujet']);
				}
			}
			
			// Si le message est présent, on va associer la variable
			if (isset($_POST['msg'])) {
				
				// Si le champ du message n'est pas vide, on l'associe
				if (!empty($_POST['msg'])) {
					
					$array_Champs['message'] = $_POST['msg'];
					$array_Champs['longueur_message'] = strlen($array_Champs['message']);
				}
			}
		}
		
		return $array_Champs;
	}
	
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		
		$array_Champs = initialisation();
		$array_Champs = remplisage_champs($array_Champs);
		
		
		
		$nom = $_POST['nom'];
		// Remove all characters except letters, digits and !#$%&'*+-=?^_`{|}~@.[].
		$emailPersonne = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		// Ajout d'une sécurité pour améliorer l'envoi de courriel sécurité - 13 Janvier 2020
		$sujet = str_ireplace(array("\r", "\n", '%0A','%0D'), '', $_POST['sujet']);
		// Ajout d'une sécurité pour améliorer l'envoi de courriel sécurité - 13 Janvier 2020
		$message = str_replace("\n.", "\n..", $_POST['msg']);
		
		// 2024-05-16, utilisation de mon courriel de UQAM pour recevoir les courriels avec les accents
		$emailServeur = "mignault.benoit@courrier.uqam.ca";
		$champsVide = false;
		$champsTroplong = false;
		$validateEmail = false;
		
		// https://stackoverflow.com/questions/11952473/proper-prevention-of-mail-injection-in-php/11952659#11952659
		// https://www.php.net/manual/en/function.filter-var.php
		$validateEmail = filter_var($emailPersonne, FILTER_VALIDATE_EMAIL);  // ajout de cette sécurité trouver sur stackoverflow
		
		if ((strlen($nom) > 30) || (strlen($emailPersonne) > 30) || (strlen($sujet) > 30) || (strlen($message) > 250)) {
			$champsTroplong = true;
		}
		
		if ($nom === "" || $emailPersonne === "" || $sujet === "" || $message === "") {
			$champsVide = true;
		}
		
		// Ajout de la 3e validation à savoir si le courriel est valide
		if ($champsVide === false && $champsTroplong === false && !$validateEmail === false) {
			$reply_email = $emailPersonne;
			date_default_timezone_set('America/New_York');
			$current_time = date("Y-m-d H:i:s");
			$entetemail = "From: " . $emailPersonne . "\r\n";    // Adresse expéditeur
			$entetemail .= "Reply-To: " . $reply_email . "\r\n"; // Adresse de retour
			$entetemail .= "X-Mailer: PHP/" . phpversion() . "\r\n";
			$entetemail .= "Date: " . $current_time . "\r\n";
			
			$succes = mail($emailServeur, $sujet, $message, $entetemail);
			if ($succes) {
				return http_response_code(200);
			}
			else {
				return http_response_code(400);
			}
		}
		else {
			return http_response_code(400);
		}
	}