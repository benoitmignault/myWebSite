const typeLangue = document.querySelector('#typeLangue');
const div_Photo = document.querySelector('.photo');
const div_Center = document.querySelector('.center');
const listeSections = document.querySelectorAll('.header div a');
const lien = document.querySelector('#liste_language');
const div_liste = document.querySelector('#hidden');
const liste = document.querySelector('#enum_Language');
const nomComplet = document.querySelector('#nom');
const courriel = document.querySelector('#email');
const message = document.querySelector('#msg');
const sujet = document.querySelector('#sujet');
const msgErr = document.querySelector('.msgErr');
const form = document.querySelector('#formContact');
const msgSucces = document.querySelector('.courrielSend');
const langue = typeLangue.value;

function activation_Liste(){
    lien.addEventListener('click', function(evt){
        if (div_liste.style.display === ""){
            div_liste.style.display = 'block';
            liste.innerHTML  = "<li>PHP</li>";
            liste.innerHTML += "<li>SQL</li>";
            liste.innerHTML += "<li>JAVASCRIPT</li>";
            liste.innerHTML += "<li>CSS</li>"; 
            liste.innerHTML += "<li>GIT & GitHub</li>"; 
            liste.innerHTML += "<li>JQUERY</li>";              
            liste.innerHTML += "<li>HTML</li>";
            liste.innerHTML += "<li>JAVA</li>"; 
            liste.innerHTML += "<li>C</li>";
            if (langue === "en"){
                liste.innerHTML += "<li>ASSEMBLER IN (Pep8)</li>"; 
            } else {
                liste.innerHTML += "<li>ASSEMPLEUR en (Pep8)</li>"; 
            } 
        } else if (div_liste.style.display === 'block'){
            liste.innerHTML = "";
            div_liste.style.display = "";
        }
    });
}

// Au moment de loader la page, on fait afficher par défault cette section
function affichageAccueuil(){
    if (langue === "fr"){        
        $(div_Center).load("http://benoitmignault.ca/pageAccueuil/partie_accueuil.html");
    } else if (langue === "en"){        
        $(div_Center).load("http://benoitmignault.ca/pageAccueuil/partie_accueuil_EN.html");
    }    
}
/* Affichage de la section du menu centrale du haut dans la section center */
function affichageSection(){
    $(listeSections).each(function(){
        $(this).on('click', function(evt){            
            evt.preventDefault();            
            if (evt.target.href === "http://benoitmignault.ca/english/english.html"){
                window.location.assign("/english/english.html");
            } else if (evt.target.href === "http://benoitmignault.ca/index.html"){
                window.location.assign("/index.html");
            } else if (evt.target.href === "http://benoitmignault.ca/pageAccueuil/partie_photos.html" || evt.target.href === "http://benoitmignault.ca/pageAccueuil/partie_photos_EN.html"){
                $(div_Center).load(evt.target.href, function() {
                    affichageSectionPhoto();
                });
            } else {
                $(div_Center).load(evt.target.href, function() {
                    div_Photo.innerHTML = "";
                });               

            }  
        });
    });
}

function affichageSectionPhoto(){
    const listePassions = document.querySelectorAll('.middle .center .header .unePassionPhoto a');
    $(listePassions).each(function(){  
        $(this).on('click', function(e){
            e.preventDefault();
            // ça garde en mémoire le lien qui sera utilisé pour change rle titre du h3 en conséquence
            const lien = this.href;            
            $(div_Photo).load(lien, function() {
                const h3 = document.querySelector('.photo h3');
                if (langue === "en"){
                    switch (lien){
                        case "http://benoitmignault.ca/pageAccueuil/photos/photo_golf/photo_golf.html" : 
                            h3.innerHTML = "Here is the sub section of the pictures on the golf :";break;
                        case "http://benoitmignault.ca/pageAccueuil/photos/photo_hiver/photo_hiver.html" : 
                            h3.innerHTML = "Here is the sub section of the pictures on the winter :";break;
                        case "http://benoitmignault.ca/pageAccueuil/photos/photo_poker/photo_poker.html" : 
                            h3.innerHTML = "Here is the sub section of the pictures on the poker :";break;
                        case "http://benoitmignault.ca/pageAccueuil/photos/photo_ski/photo_ski.html" : 
                            h3.innerHTML = "Here is the sub section of the pictures on the skiing :";break;
                        case "http://benoitmignault.ca/pageAccueuil/photos/photo_velo/photo_velo.html" : 
                            h3.innerHTML = "Here is the sub section of the pictures on the bike :";break;
                    }
                }
            });  
        });
    });
}

function envoyerCourriel(){
    $(form).submit(function(e){            
        e.preventDefault();
        msgErr.innerHTML = "";
        var erreur = false;
        var nom = nomComplet.value;
        var longueurNom = nom.length;        
        var email = courriel.value;
        var longueurEmail = email.length;        
        var msg = message.value;
        var longueurMsg = msg.length;        
        var objet = sujet.value;
        var longueurSujet = objet.length;        

        // si le prénom et ou nom est vide
        if (nomComplet.value === ""){
            nomComplet.style.border = "2px solid red";
            if (langue === "fr"){
                msgErr.innerHTML += "<li>Champ nom et prénom est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Surname and first name field is empty</li>";
            }            
            erreur = true;
        } else if (longueurNom > 30){
            nomComplet.style.border = "2px solid red";
            if (langue === "fr"){
                msgErr.innerHTML += "<li>L'information dans le champ nom et prénom est trop long</li>";
            } else {
                msgErr.innerHTML += "<li>The information in the first and last name field is too long</li>";
            } 
            erreur = true;
        } else {
            nomComplet.style.border = "initial";
        }

        // si le courriel est vide
        if (courriel.value === ""){
            courriel.style.border = "2px solid red";
            if (langue === "fr"){
                msgErr.innerHTML += "<li>Champ du courriel est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Email field is empty</li>";
            }
            erreur = true;
        } else if (longueurEmail > 30){
            courriel.style.border = "2px solid red";
            if (langue === "fr"){
                msgErr.innerHTML += "<li>L'information dans le champ email est trop long</li>";
            } else {
                msgErr.innerHTML += "<li>The information in the email field is too long</li>";
            } 
            erreur = true;
        } else {
            courriel.style.border = "initial";
        }        

        // si le sujet est vide
        if (sujet.value === ""){
            sujet.style.border = "2px solid red";
            if (langue === "fr"){
                msgErr.innerHTML += "<li>Champ du sujet est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Subject field is empty</li>";
            }            
            erreur = true;
        } else if (longueurSujet > 30){
            sujet.style.border = "2px solid red";
            if (langue === "fr"){
                msgErr.innerHTML += "<li>L'information dans le champ sujet est trop long</li>";
            } else {
                msgErr.innerHTML += "<li>The information in the subject field is too long</li>";
            } 
            erreur = true;
        } else {
            sujet.style.border = "initial";
        }  

        // si le message est vide
        if (message.value === ""){
            message.style.border = "2px solid red";
            if (langue === "fr"){
                msgErr.innerHTML += "<li>Champ du message est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Message field is empty</li>";
            }            
            erreur = true;
        } else if (longueurMsg > 250){
            message.style.border = "2px solid red";
            if (langue === "fr"){
                msgErr.innerHTML += "<li>L'information dans le champ message est trop long</li>";
            } else {
                msgErr.innerHTML += "<li>The information in the message field is too long</li>";
            } 
            erreur = true;
        } else {
            message.style.border = "initial";
        } 

        if (erreur === false){
            var request;            
            // Abort any pending request
            if (request) {
                request.abort();
            }
            // setup some local variables
            var $form = $(this); 

            // Serialize the data in the form
            var serializedData = $form.serialize();

            request = $.ajax({
                url: "http://benoitmignault.ca/contact/contact.php",
                type: "post",
                data: serializedData
            });

            // Callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR){
                if (langue === "fr"){
                    msgSucces.innerHTML = "Votre message a été envoyé";
                } else if (langue === "en"){
                    msgSucces.innerHTML = "Your message has been sent";
                }
            });

            // Callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown){
                if (langue === "fr"){
                    msgErr.innerHTML += "<li>Un problème avec l'envoi du courriel a été rencontré</li>";
                } else if (langue === "en"){
                    msgErr.innerHTML += "<li>A problem with sending the email was encountered</li>";
                } 
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() { 
    activation_Liste();
    affichageAccueuil();
    affichageSection();    
    envoyerCourriel();
});