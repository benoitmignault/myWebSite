<?php
	
	// Import PHPMailer classes into the global namespace
	use PHPMailer\PHPMailer\PHPMailer;
	
	/**
	 * Fonction pour créer l'instance de connexion au serveur de courriel GMAIL.
	 * On va utiliser une adresse courriel spéciale prévue à cet effet
	 *
	 * @return PHPMailer
	 */
	function creation_instance_courriel(): PHPMailer {
		
		//  Préparation du lien pour le courriel, avec true pour gérer les exceptions
		$mail = new PHPMailer(true);
		
		// Initialisation des variables, pour éviter des fausses erreurs de IntelliJ
		// Venant du fichier info-connexion-email.php
		$user_email = "";
		$password_email = "";
		
		// Les includes nécessaires, l'include doit être après la déclaration des variables qui seront utilisées
		include_once("../includes/info-connexion-email.php");
		
		// Paramètres du serveur SMTP
		$mail->SMTPDebug = 0; // 2 Pour voir le mode debug des messages erreurs
		$mail->isSMTP();
		$mail->Host       = 'smtp.gmail.com'; // gmail SMTP server
		$mail->SMTPAuth   = true;
		$mail->Username   = $user_email;
		$mail->Password   = $password_email;
		$mail->SMTPSecure = "tls";
		$mail->Port       = 587;
		$mail->CharSet    = 'UTF-8';
		
		return $mail;
	}