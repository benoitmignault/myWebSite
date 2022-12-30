const LANGUE = document.querySelector('#type-langue');
const DIV_PHOTO = document.querySelector('.photo');
const DIV_CENTER = document.querySelector('.center');
const LISTE_SECTIONS = document.querySelectorAll('.header div a');
const LIEN = document.querySelector('#liste-language');
const DIV_LISTE = document.querySelector('#hidden');
const LISTE = document.querySelector('#enum-language');
const NOM_COMPLET = document.querySelector('#nom');
const COURRIEL = document.querySelector('#email');
const MESSAGE = document.querySelector('#msg');
const SUJET = document.querySelector('#sujet');
const FORM_CONTACT = document.querySelector('#form-contact');
const MSG_SUCCES = document.querySelector('#msg-courriel');
const MSG_ERR = document.querySelector('#msg-err');
const HASH_TAG = document.querySelector('#hash-tag');
const CALENDRIER_AJAX = document.querySelector('#calendrier-ajax');

/**
 * Retourne la liste des technologies en informatique
 */
function activationListe() {
    LIEN.addEventListener('click', function (evt) {
        if (DIV_LISTE.style.display === "") {
            DIV_LISTE.style.display = 'block';
            LISTE.innerHTML = "<li>PHP / HTML / CSS</li>";
            LISTE.innerHTML += "<li>PYTHON3 / FLASK</li>";
            LISTE.innerHTML += "<li>JAVASCRIPT / JQUERY</li>";
            LISTE.innerHTML += "<li>C / C++ / MAKEFILE</li>";
            LISTE.innerHTML += "<li>SQL / MYSQL / ORACLE</li>";
            LISTE.innerHTML += "<li>JAVA</li>";
            if (LANGUE.value === "en") {
                LISTE.innerHTML += "<li>ASSEMBLY IN (Pep8)</li>";
            } else {
                LISTE.innerHTML += "<li>ASSEMBLEUR EN (Pep8)</li>";
            }
            LISTE.innerHTML += "<li>GIT / GITHUB / GITLAB</li>";
            LISTE.innerHTML += "<li>CODEBLOCKS / C / C++</li>";
            LISTE.innerHTML += "<li>NETBEANS / JAVA8 </li>";
            LISTE.innerHTML += "<li>ANDROID STUDIO / JAVA</li>";
            LISTE.innerHTML += "<li>WINDOWS 7 / 10 / UBUNTU</li>";
        } else if (DIV_LISTE.style.display === 'block') {
            LISTE.innerHTML = "";
            DIV_LISTE.style.display = "";
        }
    });
}

/**
 * Affichage de la section accueil en remettant à vide la section des photos et du hash-tag pour l'historique
 */
function affichageAccueil() {
    if (LANGUE.value === "fr") {
        $(DIV_CENTER).load("/pageAccueil/partie_accueil.html");
    } else if (LANGUE.value === "en") {
        $(DIV_CENTER).load("../pageAccueil/partie_accueil_EN.html");
    }
    HASH_TAG.value = "";
    DIV_PHOTO.innerHTML = "";
}

/**
 * Affichage de la section en fonction du lien cliquer
 */
function affichageSection() {
    let tagSection = $(LISTE_SECTIONS).filter("[href='" + location.hash + "']");
    if (tagSection.length) {
        HASH_TAG.value = tagSection.attr('href');
        if (tagSection.attr('href') === '#english') {
            // TODO : vérifier plus tard
            // Ne pas rajouter / devant le english
            window.location.replace("english/english.html")
        } else if (tagSection.attr('href') === '#french') {
            // Ne pas rajouter / devant le english
            window.location.replace("../index.html")
        } else {
            let lienPage = tagSection.data('href');
            $(DIV_CENTER).load(lienPage, function () {
                DIV_PHOTO.innerHTML = "";
            });
        } // TODO : Uncaught TypeError: Cannot read properties of undefined (reading 'indexOf')
        // si je pèse sur hautPageDesktop apres avec peser sur la section photo, erreur js
    } else if (HASH_TAG.value === '#photos' || HASH_TAG.value === '#pictures') {
        affichageSectionPhoto();
    } else if (location.hash !== "#haut-page-desktop" && location.hash !== "#haut-page-cellulaire") {
        affichageAccueil();
    }
}

function callAjax() {
    let data = {
        "type_langue": LANGUE.value
    };
    let url = "";
    if (LANGUE.value === "fr") {
        url = "/calendrier/calendrier.php";
    } else if (LANGUE.value === "en") {
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
                CALENDRIER_AJAX.innerHTML = dataObj.tableau_calendrier;
                // Après l'affichage du calendrier, on call le temps du timer et voilà
                startTimer();
            } else if (dataReturn["erreur"]) {
                let dataErr = JSON.parse(dataReturn["erreur"]);
                if (LANGUE.value === "en") {
                    if (dataErr.situation1) {
                        CALENDRIER_AJAX.innerHTML = "Warning ! It is missing the value of the language for the display of the web page !";
                    } else if (dataErr.situation2) {
                        CALENDRIER_AJAX.innerHTML = "Warning ! This file must be caller via an AJAX call !";
                    }
                } else {
                    if (dataErr.situation1) {
                        CALENDRIER_AJAX.innerHTML = dataErr.situation1;
                    } else if (dataErr.situation2) {
                        CALENDRIER_AJAX.innerHTML = dataErr.situation2;
                    }
                }
            }
        }
    });
}

/**
 * Affiche l'heure en dessous du calendrier en fonction si ça vient de la page française ou anglaise
 */
function startTimer() {
    const insertion_time = document.querySelector('.contenu_ligne_heure_actuel');
    let date_live = new Date();
    let date_affiche;
    if (LANGUE.value === "fr") {
        date_affiche = remplissageZeroFilled(date_live.getHours()) + ":" + remplissageZeroFilled(date_live.getMinutes()) + ":" + remplissageZeroFilled(date_live.getSeconds());
        insertion_time.innerHTML = date_affiche;
        setTimeout("startTimer()", 1000);
    } else if (LANGUE.value === "en") {
        date_affiche = formatAMPM(date_live);
        insertion_time.innerHTML = date_affiche;
        setTimeout("startTimer()", 1000);
    }
}

/**
 * Retourne l'heure ou les minutes ou les secondes avec un 0 devant le chiffre
 * @param valeur
 * @returns {string}
 */
function remplissageZeroFilled(valeur) {
    return (valeur > 9) ? "" + valeur : "0" + valeur;
}

/**
 * Retourne l'heure avec la mention AM ou PM en fonction s'il est midi et plus ou pas
 * @param date
 * @returns {string}
 */
function formatAMPM(date) {
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let secondes = date.getSeconds();
    let am_Or_pm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    secondes = secondes < 10 ? '0' + secondes : secondes;

    return hours + ':' + minutes + ':' + secondes + ' ' + am_Or_pm;
}

/**
 * En fonction de la miniphoto sélectionner, cela va afficher les photos miniatures de la section en question
 */
function affichageSectionPhoto() {
    const listePassions = document.querySelectorAll('.middle .center .header .une-passion-photo a');
    let tagSousSection = $(listePassions).filter("[href='" + location.hash + "']");
    let sousHref = tagSousSection.data('href');
    // TODO : Uncaught TypeError: Cannot read properties of undefined (reading 'indexOf')
    $(DIV_PHOTO).load(sousHref, function () {
        const h3 = document.querySelector('.photo h3');
        if (LANGUE.value === "en") {
            switch (sousHref) {
                case "/pageAccueil/photos/photo_golf/photo_golf.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the golf :";
                    break;
                case "/pageAccueil/photos/photo_hiver/photo_hiver.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the winter :";
                    break;
                case "/pageAccueil/photos/photo_poker/photo_poker.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the poker :";
                    break;
                case "/pageAccueil/photos/photo_ski/photo_ski.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the skiing :";
                    break;
                case "/pageAccueil/photos/photo_velo/photo_velo.html":
                    h3.innerHTML = "Here is the sub section of the pictures on the bike :";
                    break;
            }
        }
    });
}

/**
 * Le ou la visiteur(e) sur le site web va pouvoir envoyer un courriel à l'admin du site web avec son nom,
 * le sujet, son courriel pour un retour de l'admin, si nécessaire.
 * Des messages d'erreurs peuvent subvenir si les différents champs ne sont pas remplis.
 */
function envoyerCourriel() {
    $(FORM_CONTACT).submit(function (e) {
        e.preventDefault();
        MSG_SUCCES.innerHTML = ""; // Une seule ligne est suffisante, car ça dépasse le contexte de la langue de la page web - 13 janvier 2020
        MSG_ERR.innerHTML = "";
        let erreur = false;
        let nom = NOM_COMPLET.value;
        let longueurNom = nom.length;
        let email = COURRIEL.value;
        let longueurEmail = email.length;
        let msg = MESSAGE.value;
        let longueurMsg = msg.length;
        let objet = SUJET.value;
        let longueurSujet = objet.length;

        if (NOM_COMPLET.value === "") {
            NOM_COMPLET.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>Champ nom et prénom est vide</li>";
            } else {
                MSG_ERR.innerHTML += "<li>Surname and first name field is empty</li>";
            }
            erreur = true;
        } else if (longueurNom > 30) {
            NOM_COMPLET.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>L'information dans le champ nom et prénom est trop long</li>";
            } else {
                MSG_ERR.innerHTML += "<li>The information in the first and last name field is too long</li>";
            }
            erreur = true;
        } else {
            NOM_COMPLET.style.border = "initial";
        }

        // si le courriel est vide
        if (courriel.value === "") {
            courriel.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>Champ du courriel est vide</li>";
            } else {
                MSG_ERR.innerHTML += "<li>Email field is empty</li>";
            }
            erreur = true;
        } else if (longueurEmail > 30) {
            courriel.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>L'information dans le champ email est trop long</li>";
            } else {
                MSG_ERR.innerHTML += "<li>The information in the email field is too long</li>";
            }
            erreur = true;
        } else {
            courriel.style.border = "initial";
        }

        // si le sujet est vide
        if (SUJET.value === "") {
            SUJET.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>Champ du sujet est vide</li>";
            } else {
                MSG_ERR.innerHTML += "<li>Subject field is empty</li>";
            }
            erreur = true;
        } else if (longueurSujet > 30) {
            SUJET.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>L'information dans le champ sujet est trop long</li>";
            } else {
                MSG_ERR.innerHTML += "<li>The information in the subject field is too long</li>";
            }
            erreur = true;
        } else {
            SUJET.style.border = "initial";
        }

        // si le message est vide
        if (MESSAGE.value === "") {
            MESSAGE.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>Champ du message est vide</li>";
            } else {
                MSG_ERR.innerHTML += "<li>Message field is empty</li>";
            }
            erreur = true;
        } else if (longueurMsg > 250) {
            MESSAGE.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>L'information dans le champ message est trop long</li>";
            } else {
                MSG_ERR.innerHTML += "<li>The information in the message field is too long</li>";
            }
            erreur = true;
        } else {
            MESSAGE.style.border = "initial";
        }

        if (erreur === false) {
            let request;
            // Abort any pending request
            if (request) {
                request.abort();
            }
            // setup some local variables
            let $form = $(this);

            // Serialize the data in the form
            let serializedData = $form.serialize();
            let url = "";
            // la variable langue sera interpreter comme un type post comme on le définit dans l'appel Ajax.
            if (LANGUE.value === "fr") {
                url = "/contact/contact.php";
            } else if (LANGUE.value === "en") {
                url = "../contact/contact.php";
            }
            request = $.ajax({
                url: url,
                type: "post",
                data: serializedData
            });

            // Callback handler that will be called on success
            // TODO : vérifier si les variables inutilisé sont utile ou pas
            request.done(function (response, textStatus, jqXHR) {
                if (LANGUE.value === "fr") {
                    MSG_SUCCES.innerHTML = "Votre message a été envoyé";
                } else if (LANGUE.value === "en") {
                    MSG_SUCCES.innerHTML = "Your message has been sent";
                }
            });

            // Callback handler that will be called on failure
            // TODO : vérifier si les variables inutilisé sont utile ou pas
            request.fail(function (jqXHR, textStatus, errorThrown) {
                if (LANGUE.value === "fr") {
                    MSG_ERR.innerHTML += "<li>Un problème avec l'envoi du courriel a été rencontré</li>";
                } else if (LANGUE.value === "en") {
                    MSG_ERR.innerHTML += "<li>A problem with sending the email was encountered</li>";
                }
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // À chaque fois que le HASH_TAG change-t-on appel cet évènement
    $(window).on('hashchange', function (event) {
        affichageSection();
    });

    // Si on inscrit manuellement un HASH_TAG, on call la fct sinon on call la fct de base
    if (location.hash !== "") {
        affichageSection();
    } else {
        affichageAccueil();
    }

    callAjax();
    activationListe();
    envoyerCourriel();
});
