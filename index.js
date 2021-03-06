var langue = document.querySelector('#typeLangue');
var div_Photo = document.querySelector('.photo');
var div_Center = document.querySelector('.center');
var listeSections = document.querySelectorAll('.header div a');
var listePassions = document.querySelectorAll('.middle .center .header .unePassionPhoto a');
var lien = document.querySelector('#liste_language');
var div_liste = document.querySelector('#hidden');
var liste = document.querySelector('#enum_Language');
var nomComplet = document.querySelector('#nom');
var courriel = document.querySelector('#email');
var message = document.querySelector('#msg');
var sujet = document.querySelector('#sujet');
var msgErr = document.querySelector('.msgErr');
var form = document.querySelector('#formContact');
var msgSucces = document.querySelector('.courrielSend');
var hashTag = document.querySelector('#hashTag');
var calendrierAJAX = document.querySelector('#tableauAJAX');

function activation_Liste() {
    lien.addEventListener('click', function (evt) {
        if (div_liste.style.display == "") {
            div_liste.style.display = 'block';
            liste.innerHTML = "<li>PHP / HTML / CSS</li>";
            liste.innerHTML += "<li>PYTHON3 / FLASK</li>";
            liste.innerHTML += "<li>JAVASCRIPT / JQUERY</li>";
            liste.innerHTML += "<li>C / C++ / MAKEFILE</li>";
            liste.innerHTML += "<li>SQL / MYSQL / ORACLE</li>";
            liste.innerHTML += "<li>JAVA</li>";
            if (langue.value == "en") {
                liste.innerHTML += "<li>ASSEMBLY IN (Pep8)</li>";
            } else {
                liste.innerHTML += "<li>ASSEMBLEUR EN (Pep8)</li>";
            }
            liste.innerHTML += "<li>GIT / GITHUB / GITLAB</li>";
            liste.innerHTML += "<li>CODEBLOCKS / C / C++</li>";
            liste.innerHTML += "<li>NETBEANS / JAVA8 </li>";
            liste.innerHTML += "<li>ANDROID STUDIO / JAVA</li>";
            liste.innerHTML += "<li>WINDOWS 7 / 10 / UBUNTU</li>";
        } else if (div_liste.style.display == 'block') {
            liste.innerHTML = "";
            div_liste.style.display = "";
        }
    });
}

function affichageAccueil() {
    if (langue.value == "fr") {
        $(div_Center).load("pageAccueil/partie_accueil.html");
    } else if (langue.value == "en") {
        $(div_Center).load("../pageAccueil/partie_accueil_EN.html");
    }
    hashTag.value = "";
    div_Photo.innerHTML = "";
}

function affichageSection() {
    var tagSection = $(listeSections).filter("[href='" + location.hash + "']");
    if (tagSection.length) {
        hashTag.value = tagSection.attr('href');
        if (tagSection.attr('href') == '#english') {
            window.location.replace("english/english.html")
        } else if (tagSection.attr('href') == '#french') {
            window.location.replace("../index.html")
        } else {
            var lien_page = tagSection.data('href');
            $(div_Center).load(lien_page, function () {
                div_Photo.innerHTML = "";
            });
        } // si je pese sur hautPageDesktop apres avec peser sur la section photo, erreur js
    } else if (hashTag.value == '#photos' || hashTag.value == '#pictures') {
        affichageSectionPhoto();
    } else if (location.hash != "#hautPageDesktop" && location.hash != "#hautPageCellulaire") {
        affichageAccueil();
    }
}

function callAjax() {
    var data = {
        "type_langue": langue.value
    };
    var url = "";
    if (langue.value == "fr") {
        url = "/calendrier/calendrier.php";
    } else if (langue.value == "en") {
        url = "../calendrier/calendrier.php";
    }

    $.ajax({
        type: "POST",
        dataType: "json",
        url: url,
        data: data,
        success: function (dataReturn) {
            // sécurisation du retour d'information
            if (dataReturn["data"]) {
                var dataObj = JSON.parse(dataReturn["data"]);
                calendrierAJAX.innerHTML = dataObj.tableau_calendrier;
                // Après l'affichage du calendrier, on call le temps du timer et voilà
                start_timer();
            } else if (dataReturn["erreur"]) {
                var dataErr = JSON.parse(dataReturn["erreur"]);
                if (langue.value == "en") {
                    if (dataErr.situation1) {
                        calendrierAJAX.innerHTML = "Warning ! It is missing the value of the language for the display of the web page !";
                    } else if (dataErr.situation2) {
                        calendrierAJAX.innerHTML = "Warning ! This file must be caller via an AJAX call !";
                    }
                } else {
                    if (dataErr.situation1) {
                        calendrierAJAX.innerHTML = dataErr.situation1;
                    } else if (dataErr.situation2) {
                        calendrierAJAX.innerHTML = dataErr.situation2;
                    }
                }
            }
        }
    });
}

function start_timer() {
    const insertion_time = document.querySelector('.contenu_ligne_heure_actuel');
    var date_live = new Date();
    if (langue.value == "fr") {
        var date_affiche = remplissageZeroFilled(date_live.getHours()) + ":" + remplissageZeroFilled(date_live.getMinutes()) + ":" + remplissageZeroFilled(date_live.getSeconds());
        insertion_time.innerHTML = date_affiche;
        setTimeout("start_timer()", 1000);
    } else if (langue.value == "en") {
        var date_affiche = formatAMPM(date_live);
        insertion_time.innerHTML = date_affiche;
        setTimeout("start_timer()", 1000);
    }
}

function remplissageZeroFilled(valeur) {
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

function affichageSectionPhoto() {
    const listePassions = document.querySelectorAll('.middle .center .header .unePassionPhoto a');
    var tagSousSection = $(listePassions).filter("[href='" + location.hash + "']");
    var sousHref = tagSousSection.data('href');
    $(div_Photo).load(sousHref, function () {
        const h3 = document.querySelector('.photo h3');
        if (langue.value == "en") {
            switch (sousHref) {
                case "https://benoitmignault.ca/pageAccueil/photos/photo_golf/photo_golf.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the golf :";
                    break;
                case "https://benoitmignault.ca/pageAccueil/photos/photo_hiver/photo_hiver.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the winter :";
                    break;
                case "https://benoitmignault.ca/pageAccueil/photos/photo_poker/photo_poker.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the poker :";
                    break;
                case "https://benoitmignault.ca/pageAccueil/photos/photo_ski/photo_ski.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the skiing :";
                    break;
                case "https://benoitmignault.ca/pageAccueil/photos/photo_velo/photo_velo.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the bike :";
                    break;
            }
        }
    });
}

function envoyerCourriel() {
    $(form).submit(function (e) {
        e.preventDefault();
        msgSucces.innerHTML = ""; // Une seul ligne est suffisante car ça dépasse le contexte de la langue de la page web - 13 Janvier 2020
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

        if (nomComplet.value === "") {
            nomComplet.style.border = "2px solid red";
            if (langue.value === "fr") {
                msgErr.innerHTML += "<li>Champ nom et prénom est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Surname and first name field is empty</li>";
            }
            erreur = true;
        } else if (longueurNom > 30) {
            nomComplet.style.border = "2px solid red";
            if (langue.value === "fr") {
                msgErr.innerHTML += "<li>L'information dans le champ nom et prénom est trop long</li>";
            } else {
                msgErr.innerHTML += "<li>The information in the first and last name field is too long</li>";
            }
            erreur = true;
        } else {
            nomComplet.style.border = "initial";
        }

        // si le courriel est vide
        if (courriel.value === "") {
            courriel.style.border = "2px solid red";
            if (langue.value === "fr") {
                msgErr.innerHTML += "<li>Champ du courriel est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Email field is empty</li>";
            }
            erreur = true;
        } else if (longueurEmail > 30) {
            courriel.style.border = "2px solid red";
            if (langue.value === "fr") {
                msgErr.innerHTML += "<li>L'information dans le champ email est trop long</li>";
            } else {
                msgErr.innerHTML += "<li>The information in the email field is too long</li>";
            }
            erreur = true;
        } else {
            courriel.style.border = "initial";
        }

        // si le sujet est vide
        if (sujet.value === "") {
            sujet.style.border = "2px solid red";
            if (langue.value === "fr") {
                msgErr.innerHTML += "<li>Champ du sujet est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Subject field is empty</li>";
            }
            erreur = true;
        } else if (longueurSujet > 30) {
            sujet.style.border = "2px solid red";
            if (langue.value === "fr") {
                msgErr.innerHTML += "<li>L'information dans le champ sujet est trop long</li>";
            } else {
                msgErr.innerHTML += "<li>The information in the subject field is too long</li>";
            }
            erreur = true;
        } else {
            sujet.style.border = "initial";
        }

        // si le message est vide
        if (message.value === "") {
            message.style.border = "2px solid red";
            if (langue.value === "fr") {
                msgErr.innerHTML += "<li>Champ du message est vide</li>";
            } else {
                msgErr.innerHTML += "<li>Message field is empty</li>";
            }
            erreur = true;
        } else if (longueurMsg > 250) {
            message.style.border = "2px solid red";
            if (langue.value === "fr") {
                msgErr.innerHTML += "<li>L'information dans le champ message est trop long</li>";
            } else {
                msgErr.innerHTML += "<li>The information in the message field is too long</li>";
            }
            erreur = true;
        } else {
            message.style.border = "initial";
        }

        if (erreur === false) {
            var request;
            // Abort any pending request
            if (request) {
                request.abort();
            }
            // setup some local variables
            var $form = $(this);

            // Serialize the data in the form
            var serializedData = $form.serialize();
            var url = "";
            // la variable langue sera interprèter comem un type post comme on le défini dans l'appel Ajax.
            if (langue.value === "fr") {
                url = "/contact/contact.php";
            } else if (langue.value === "en") {
                url = "../contact/contact.php";
            }
            request = $.ajax({
                url: url,
                type: "post",
                data: serializedData
            });

            // Callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR) {
                if (langue.value === "fr") {
                    msgSucces.innerHTML = "Votre message a été envoyé";
                } else if (langue.value === "en") {
                    msgSucces.innerHTML = "Your message has been sent";
                }
            });

            // Callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown) {
                if (langue.value === "fr") {
                    msgErr.innerHTML += "<li>Un problème avec l'envoi du courriel a été rencontré</li>";
                } else if (langue.value === "en") {
                    msgErr.innerHTML += "<li>A problem with sending the email was encountered</li>";
                }
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {

    /* À chaque fois que le hashTag change on appel cet évenement */
    $(window).on('hashchange', function (event) {
        affichageSection();
    });

    /* Si on inscrit manuellement un hasgTag, on call la fct sinon on call la fct de base */
    if (location.hash != "") {
        affichageSection();
    } else {
        affichageAccueil();
    }

    callAjax();
    activation_Liste();
    envoyerCourriel();
});
