<?php

header('Content-Type: application/json');

// Modernisation des envoies de courriels venant des gens qui tente de me contacter
// 2024-08-13

// Les includes nécessaires
require_once("../includes/fct-divers.php");
require_once("../includes/fct-php-mailer.php");

// Importation de la classe d'exception de PHPMailer
use PHPMailer\PHPMailer\Exception as MailException;

// Load Composer autoload (PHPMailer)
require '../../vendor/autoload.php';

/**
 * Fonction qui va contenir tous ce dont on aura besoin.
 * Une partie des variables de type string ou integer et une autre partie en boolean
 *
 * @return array
 */
function initialisation(): array {
	
	return array("longueur_nom" => 0, "longueur_email" => 0, "longueur_sujet" => 0, "longueur_message" => 0,
	"nom" => "", "email" => "", "sujet" => "", "message" => "", "erreur_presente" => false, "envoi_courriel_succes" => false,
	"liste_erreur_possible" => 
	array("champs_vide" => false, "champs_trop_long" => false, "champ_email_invalid" => false, "champ_nom_vide" => false, 
	"champ_email_vide" => false, "champ_message_vide" => false, "champ_sujet_vide" => false, "champ_nom_trop_long" => false, "champ_email_trop_long" => false,
	"champ_message_trop_long" => false, "champ_sujet_trop_long" => false));
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

/**
 * Fonction qui servira à mettre à «True» les variables de contrôles des informations que nous avons associé durant la fonction @see remplisage_champs
 *
 * @param $array_Champs
 * @return array
 */
function validation_champs($array_Champs): array{
	
	// Section des vérifications des champs vide
	if (empty($array_Champs['email'])) {
		$array_Champs['liste_erreur_possible']['champ_email_vide'] = true;
	}
	
	if (empty($array_Champs['nom'])) {
		$array_Champs['liste_erreur_possible']['champ_nom_vide'] = true;
	}
	
	if (empty($array_Champs['sujet'])) {
		$array_Champs['liste_erreur_possible']['champ_sujet_vide'] = true;
	}
	
	if (empty($array_Champs['message'])) {
		$array_Champs['liste_erreur_possible']['champ_message_vide'] = true;
	}
	
	// Vérification si un des champs est vide, alors on stop tout et on retourne une erreur au CALL Ajax
	if ($array_Champs['liste_erreur_possible']['champ_email_vide'] || $array_Champs['liste_erreur_possible']['champ_nom_vide'] ||
		$array_Champs['liste_erreur_possible']['champ_sujet_vide'] || $array_Champs['liste_erreur_possible']['champ_message_vide']){
		
		$array_Champs['liste_erreur_possible']['champs_vide'] = true;
	}
	
	// On peut poursuivre logiquement les validations vue que les champs ne sont pas vide
	if (!$array_Champs['liste_erreur_possible']['champs_vide']){
		
		// On vérifie maintenant la longueur du contenu des champs
		if ($array_Champs['longueur_message'] > 300){
			$array_Champs['liste_erreur_possible']['champ_message_trop_long'] = true;
		}
		
		if ($array_Champs['longueur_nom'] > 30){
			$array_Champs['liste_erreur_possible']['champ_nom_trop_long'] = true;
		}
		
		if ($array_Champs['longueur_email'] > 50){
			$array_Champs['liste_erreur_possible']['champ_email_trop_long'] = true;
		}
		
		if ($array_Champs['longueur_sujet'] > 50){
			$array_Champs['liste_erreur_possible']['champ_sujet_trop_long'] = true;
		}
		
		// On vérifier l'état du courriel
		if (!preg_match(PATTERN_EMAIL, $array_Champs['email'])) {
			$array_Champs['liste_erreur_possible']['champ_email_invalid'] = true;
		}
		
		// https://stackoverflow.com/questions/11952473/proper-prevention-of-mail-injection-in-php/11952659#11952659
		if (!(filter_var($array_Champs['email'], FILTER_VALIDATE_EMAIL))){
			$array_Champs['liste_erreur_possible']['champ_email_invalid'] = true;
		}
	}
	
	$array_Champs['erreur_presente'] = verification_valeur_controle($array_Champs['liste_erreur_possible']);
	
	return $array_Champs;
}

/**
 * Création du courriel qui sera retourner au propriétaire du site web pour être informer des messages
 * Avec différentes étapes de préparations du courriel et contenu de ce dernier
 *
 * @param array $array_Champs
 * @return array
 */
function gestion_lien_courriel(array $array_Champs): array{
	
	// Création de l'instance
	$mail = creation_instance_courriel();
	
	try {
		// L'information vient de qui et qui sera utilisé pour répondre à la personne qui a rempli le formulaire
		// Expéditeur (doit être ton Gmail)
		$mail->setFrom('benoit.mignault.ca@gmail.com', 'Formulaire Site');	
		
		// Ajouter le destinataire principal - Propriétaire du site web
		// MAJ du password le 2026-04-04
		// Et changement de l'adresse courriel pour le destinataire, pour éviter les spams et car home@benoitmignault.ca ne recoit plus de courriel
		// Destinataire (toi)
		$mail->addAddress('benoit.mignault.ca@gmail.com', 'Benoit Mignault');

		// Reply-To (client)
		// Envoi possible d'une réponse à la personne qui a envoyer un commentaire via le formulaire
		$mail->addReplyTo($array_Champs['email'], $array_Champs['nom']);
		
		// Contenu du message
		// Préparation pour l'object et le corp du message, en fonction de la langue
		$mail->isHTML(true); // par défaut is true
		$mail->Subject = $array_Champs['sujet'];
		$mail->Body = preparation_contenu_courriel($array_Champs['message']);

		if (!$mail->send()) {
			throw new MailException($mail->ErrorInfo);
		}
		
		// Succès
		// Si l'envoi réussit, cette ligne sera exécutée
		$array_Champs["envoi_courriel_succes"] = true;
		
	} catch (MailException $e) {
		
		// Si l'envoi échoue, cette ligne sera exécutée
		
		// Vous pouvez aussi enregistrer l'erreur pour le débogage
		// Chemin sur le serveur : /home/benoitmignault/logs/benoitmignault_ca.php.error.log
		$array_Champs["envoi_courriel_succes"] = false;
		$array_Champs["erreur_mail"] = $e->getMessage();

		// Log serveur (important en prod)
		error_log('Erreur PHPMailer : ' . $e->getMessage());
	}	
	
	// Toujours fermer proprement
	$mail->smtpClose();		
	
	return $array_Champs;
}

/**
 * Fonction pour créer le corps du courriel
 *
 * @param string $message
 * @return string
 */
function preparation_contenu_courriel(string $message): string {
	
	// Ajout de l'encodage UTF-8 dans la balise meta
	$contenu_courriel = "<html lang=\"fr\">";
	$contenu_courriel .= "<head>";
	$contenu_courriel .= "<meta charset=\"UTF-8\">";
	$contenu_courriel .= "<title>Message de l'auditoire</title>";
	$contenu_courriel .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
	$contenu_courriel .= "</head>";
	$contenu_courriel .= "<body style='font-family: Arial, sans-serif; background-color: #D3D3D3; margin-top: 0; font-size: 16px;'>";
	
	// Le message sera déjà encodé en UTF-8
	$contenu_courriel .= "<p>" . htmlentities($message, ENT_QUOTES, 'UTF-8') . "</p>";
	$contenu_courriel .= '</body></html>';
	
	return $contenu_courriel;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	$array_Champs = initialisation();
	$array_Champs = remplisage_champs($array_Champs);
	$array_Champs = validation_champs($array_Champs);
	
	// ❌ ERREUR VALIDATION
	if ($array_Champs['erreur_presente']) {

		http_response_code(400);
		echo json_encode([
			'success' => false,
			'type' => 'validation',
			'errors' => $array_Champs['liste_erreur_possible']
		]);

		exit;
	}

	// ✅ ENVOI MAIL
	$array_Champs = gestion_lien_courriel($array_Champs);

	// ❌ ERREUR SMTP
	if (!$array_Champs["envoi_courriel_succes"]) {

		http_response_code(400);

		echo json_encode([
			'success' => false,
			'type' => 'mail',
			'error' => $array_Champs['erreur_mail'] ?? 'Erreur envoi courriel'
		]);

		exit;
	}

	// ✅ SUCCÈS
	http_response_code(200);

	echo json_encode([
		'success' => true
	]);

	exit;

	/*
	// Si aucune erreur est présent, on peut aller de l'avant et construire le courriel pour informer l'auteur
	if (!$array_Champs['erreur_presente']){
		
		// Utilisation de cette fonction pour appeler les fonctions nécessaires pour le courriel
		$array_Champs = gestion_lien_courriel($array_Champs);
		
		// Si l'envoi de courriel à marcher, on retourne un code 200
		if ($array_Champs["envoi_courriel_succes"]) {
			return http_response_code(200);
			
		} else {
			// Sinon, on retourne un code de retour 400
			return http_response_code(400);
		}
	
	} else {
		// Sinon, on retourne un code de retour 400
		return http_response_code(400);
	}*/

}