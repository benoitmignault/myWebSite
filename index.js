const langue = document.querySelector('#typeLangue');
const div_Photo = document.querySelector('.photo');
const div_Center = document.querySelector('.center');
const listeSections = document.querySelectorAll('.header div a');
const listePassions = document.querySelectorAll('.middle .center .header .unePassionPhoto a');
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
const hashTag = document.querySelector('#hashTag');
const calendrierAJAX = document.querySelector('#tableauAJAX');

function activation_Liste(){
    lien.addEventListener('click', function(evt){
        if (div_liste.style.display === ""){
            div_liste.style.display = 'block';
            liste.innerHTML  = "<li>PHP</li>";
            liste.innerHTML += "<li>HTML</li>";
            liste.innerHTML += "<li>JAVASCRIPT</li>";
            liste.innerHTML += "<li>JQUERY & AJAX</li>";
            liste.innerHTML += "<li>CSS</li>";
            liste.innerHTML += "<li>SQL & MYSQL</li>";
            liste.innerHTML += "<li>ORACLE XE</li>";
            liste.innerHTML += "<li>JAVA</li>";
            liste.innerHTML += "<li>NETBEANS IDE 8.2</li>";
            liste.innerHTML += "<li>GIT & GITHUB & GITLAB</li>";
            liste.innerHTML += "<li>C</li>";
            if (langue === "en"){
                liste.innerHTML += "<li>ASSEMBLY IN (Pep8)</li>"; 
            } else {
                liste.innerHTML += "<li>ASSEMBLEUR EN (Pep8)</li>"; 
            } 
        } else if (div_liste.style.display === 'block'){
            liste.innerHTML = "";
            div_liste.style.display = "";
        }
    });
}

function affichageAccueil(){
    if (langue.value == "fr"){        
        $(div_Center).load("pageAccueil/partie_accueil.html");
    } else if (langue.value == "en"){        
        $(div_Center).load("../pageAccueil/partie_accueil_EN.html");
    }
    hashTag.value = "";
    div_Photo.innerHTML = "";
}

function affichageSection(){
    var tagSection = $(listeSections).filter("[href='"+location.hash+"']");
    if (tagSection.length){
        hashTag.value = tagSection.attr('href');
        if (tagSection.attr('href') == '#english'){
            window.location.replace("english/english.html")
        } else if (tagSection.attr('href') == '#french'){
            window.location.replace("../index.html")
        } else {
            var lien_page = tagSection.data('href');
            $(div_Center).load(lien_page, function(){
                div_Photo.innerHTML = "";
            });
        } // si je pese sur hautPageDesktop apres avec peser sur la section photo, erreur js
    } else if (hashTag.value == '#photos' || hashTag.value == '#pictures'){
        affichageSectionPhoto();
    } else if (location.hash != "#hautPageDesktop" && location.hash != "#hautPageCellulaire" ){
        affichageAccueil();
    }
}

function callAjax(){
    var data = { "type_langue": langue.value };
    var url = "http://benoitmignault.ca/calendrier/calendrier.php";

    $.ajax({
        type: "POST",
        dataType: "json",
        url: url, 
        data: data,
        success: function(dataReturn) { 
            // sécurisation du retour d'information
            if (dataReturn["data"]){
                var dataObj = JSON.parse(dataReturn["data"]);   
                calendrierAJAX.innerHTML = dataObj.tableau_calendrier;
                // Après l'affichage du calendrier, on call le temps du timer et voilà
                start_timer(); 
            } else if (dataReturn["erreur"]){
                var dataErr = JSON.parse(dataReturn["erreur"]);
                if (langue.value == "english"){
                    if (dataErr.situation1){
                        calendrierAJAX.innerHTML = "Warning ! It is missing the value of the language for the display of the web page !";
                    } else if (dataErr.situation2){
                        calendrierAJAX.innerHTML = "Warning ! This file must be caller via an AJAX call !";
                    }
                } else {
                    if (dataErr.situation1){
                        calendrierAJAX.innerHTML = dataErr.situation1;
                    } else if (dataErr.situation2){
                        calendrierAJAX.innerHTML = dataErr.situation2;
                    }
                }
            }
        }
    });
}

function start_timer(){ 
    const insertion_time = document.querySelector('.contenu_ligne_heure_actuel');
    var date_live = new Date();
    if (langue.value == "fr"){
        var date_affiche = remplissageZeroFilled(date_live.getHours()) + ":" + remplissageZeroFilled(date_live.getMinutes()) + ":" + remplissageZeroFilled(date_live.getSeconds());
        insertion_time.innerHTML = date_affiche;
        setTimeout("start_timer()",1000);
    } else if (langue.value == "en"){
        var date_affiche = formatAMPM(date_live);
        insertion_time.innerHTML = date_affiche;
        setTimeout("start_timer()",1000);
    }
}

function remplissageZeroFilled(valeur){ 
    return (valeur > 9) ? "" + valeur : "0" + valeur; 
}

function formatAMPM(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var secondes = date.getSeconds();
    var am_Or_pm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    secondes = secondes < 10 ? '0' + secondes : secondes;
    var heure_english = hours + ':' + minutes + ':' + secondes + ' ' + am_Or_pm;
    return heure_english;
}

function affichageSectionPhoto(){
    const listePassions = document.querySelectorAll('.middle .center .header .unePassionPhoto a');
    var tagSousSection = $(listePassions).filter("[href='"+location.hash+"']");
    var sousHref = tagSousSection.data('href');
    $(div_Photo).load(sousHref, function() {
        const h3 = document.querySelector('.photo h3');
        if (langue.value == "en"){
            switch (sousHref){
                case "http://benoitmignault.ca/pageAccueil/photos/photo_golf/photo_golf.html" : 
                    h3.innerHTML = "Here is the sub section of the pictures on the golf :"; break;
                case "http://benoitmignault.ca/pageAccueil/photos/photo_hiver/photo_hiver.html" : 
                    h3.innerHTML = "Here is the sub section of the pictures on the winter :"; break;
                case "http://benoitmignault.ca/pageAccueil/photos/photo_poker/photo_poker.html" : 
                    h3.innerHTML = "Here is the sub section of the pictures on the poker :"; break;
                case "http://benoitmignault.ca/pageAccueil/photos/photo_ski/photo_ski.html" : 
                    h3.innerHTML = "Here is the sub section of the pictures on the skiing :"; break;
                case "http://benoitmignault.ca/pageAccueil/photos/photo_velo/photo_velo.html" : 
                    h3.innerHTML = "Here is the sub section of the pictures on the bike :"; break;
            }
        }
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

        if (nomComplet.value === ""){
            nomComplet.style.border = "2px solid red";
            if (langue.value === "fr"){
                msgErr.innerHTML += "<li>Champ nom et prénom est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Surname and first name field is empty</li>";
            }            
            erreur = true;
        } else if (longueurNom > 30){
            nomComplet.style.border = "2px solid red";
            if (langue.value === "fr"){
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
            if (langue.value === "fr"){
                msgErr.innerHTML += "<li>Champ du courriel est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Email field is empty</li>";
            }
            erreur = true;
        } else if (longueurEmail > 30){
            courriel.style.border = "2px solid red";
            if (langue.value === "fr"){
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
            if (langue.value === "fr"){
                msgErr.innerHTML += "<li>Champ du sujet est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Subject field is empty</li>";
            }            
            erreur = true;
        } else if (longueurSujet > 30){
            sujet.style.border = "2px solid red";
            if (langue.value === "fr"){
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
            if (langue.value === "fr"){
                msgErr.innerHTML += "<li>Champ du message est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Message field is empty</li>";
            }            
            erreur = true;
        } else if (longueurMsg > 250){
            message.style.border = "2px solid red";
            if (langue.value === "fr"){
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
                if (langue.value === "fr"){                    
                    msgSucces.innerHTML = "Votre message a été envoyé";
                } else if (langue.value === "en"){
                    msgSucces.innerHTML = "Your message has been sent";
                }
            });

            // Callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown){
                if (langue.value === "fr"){
                    msgErr.innerHTML += "<li>Un problème avec l'envoi du courriel a été rencontré</li>";
                } else if (langue.value === "en"){
                    msgErr.innerHTML += "<li>A problem with sending the email was encountered</li>";
                } 
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {  

    /* À chaque fois que le hashTag change on appel cet évenement */
    $(window).on('hashchange', function(event) {
        affichageSection();
    });

    /* Si on inscrit manuellement un hasgTag, on call la fct sinon on call la fct de base */
    if (location.hash != ""){        
        affichageSection();
    } else {
        affichageAccueil();
    }

    callAjax();
    activation_Liste();     
    envoyerCourriel();
});