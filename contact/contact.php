<?php
	
	// Modernisation des envoies de courriels venant des gens qui tente de me contacter
	
	
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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