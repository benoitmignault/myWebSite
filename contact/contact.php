<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $emailPersonne = $_POST['email'];
    $sujet = $_POST['sujet'];
    $message = $_POST['msg'];
    $emailServeur = "home@benoitmignault.ca";
    $champsVide = false;
    $champsTroplong = false;
    
    if ( (strlen($nom) > 30) || (strlen($emailPersonne) > 30) || (strlen($sujet) > 30)  || (strlen($message) > 250) ){
        $champsTroplong = true;
    }
    
    if($nom === "" || $emailPersonne === "" || $sujet === "" || $message === ""){
        $champsVide = true;
    }

    if ($champsVide === false && $champsTroplong === false){
        $reply_email = $emailPersonne; 
        $entetedate  = date("D, j M Y H:i:s"); // Offset horaire 
        $entetemail  = "From: $emailPersonne \n"; // Adresse expéditeur 
        $entetemail .= "Reply-To: $reply_email \n"; // Adresse de retour 
        $entetemail .= "X-Mailer: PHP/" . phpversion() . "\n" ; 
        $entetemail .= "Date: $entetedate"; 

        $succes = mail("$emailServeur","$sujet","$message",$entetemail);        
        if ($succes){
            return http_response_code(200);
            exit;
        } else {
            return http_response_code(400);
            exit;
        }
    } else {
        return http_response_code(400);
        exit;
    }
}
?>