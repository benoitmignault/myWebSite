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
// Uniquement pour faire varier le titre de la page en cours
const HASH_TAG_SECOND = document.querySelector('#hash-tag-second');
const CALENDRIER_AJAX = document.querySelector('#calendrier-ajax');

/**
 * Retourne la liste des technologies en informatique
 *
 * @returns {void}
 */
function activation_liste() {

    LIEN.addEventListener('click', function () {
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
 *
 * @returns {void}
 */
function affichage_accueil() {

    if (LANGUE.value === "fr") {
        $(DIV_CENTER).load("/section/section-accueil.html");
    } else if (LANGUE.value === "en") {
        $(DIV_CENTER).load("../section/section-accueil-english.html");
    }
    HASH_TAG.value = "";
    DIV_PHOTO.innerHTML = "";
}

/**
 * Affichage de la section en fonction du lien cliquer
 *
 * @returns {void}
 */
function affichage_section() {

    // Récupère la liste des 5 sections à faire afficher
    let tag_section = $(LISTE_SECTIONS).filter("[href='" + location.hash + "']");
    //console.log(location.hash);
    //console.log(tag_section.length);

    // Nous avons sélectionné le hashtag principal, entre accueil, projet, photo, a-propos et english/french
    if (tag_section.length === 1) {
        // Récupère le hasg tag sélectionné
        HASH_TAG.value = tag_section.attr('href');
        console.log(HASH_TAG.value);
        //HASH_TAG_PRINCIPAL = location.hash;
        // Les href english ou french est pour switcher de l'anglais à francais et vice versa
        if (tag_section.attr('href') === '#english') {
            window.location.replace("/english/english.html")

        } else if (tag_section.attr('href') === '#french') {
            window.location.replace("/index.html")

        } else {
            // Dans « data » on retrouve le module HTML qu'on veut faire afficher
            let lienPage = tag_section.data('href');
            $(DIV_CENTER).load(lienPage, function () {
                DIV_PHOTO.innerHTML = "";
                HASH_TAG_SECOND.value = ""; // Important de remettre ça à NULL
                document.title = recupere_formate_titre_section();
            });
        }
        // si je pèse sur hautPageDesktop apres avec peser sur la section photo, erreur js
    } else if (HASH_TAG.value === '#photos' || HASH_TAG.value === '#pictures') {
        // On affiche le sous hashtag avec le hashtag principal
        //console.log(HASH_TAG.value);
        // On affiche les photos de la section cliquée
        affichage_section_photo();
        // Si le hash-tag est différent des outils pour remonter en haut du site, on affiche la section accueil
    } else if (location.hash !== "#haut-page-desktop" && location.hash !== "#haut-page-cellulaire") {
        affichage_accueil();
    }
}

/**
 * En fonction de la miniphoto sélectionner, cela va afficher les photos miniatures de la section en question
 *
 * @returns {void}
 */
function affichage_section_photo() {

    // Cette constante existe seulement si la section Photo est sélectionnée
    const LISTE_PASSIONS = document.querySelectorAll('.middle .center .sous-header .une-passion-photo a');

    // On filtre sur là sous section des photos choisies, sauf quand on remonte la page
    let tag_sous_section = $(LISTE_PASSIONS).filter("[href='" + location.hash + "']");

    // Si là sous section est égale à 1, on affiche les photos de la sous section
    if (tag_sous_section.length === 1){
        // On récupère le lien vers le fichier HTML qui contient tous les liens des photos
        let sousHref = tag_sous_section.data('href');

        // On change la valeur du hash-tag second, seulement s'il y a de quoi
        HASH_TAG_SECOND.value = tag_sous_section.attr('href');

        // Maintenant, on load la section des photos sélectionnées
        $(DIV_PHOTO).load(sousHref, function () {
            // Modifier le titre de la page en fonction de quelle section de photo, on fait afficher
            document.title = recupere_formate_titre_section();

            // Si nous avons l'anglais, on va réajuster le titre de sous section
            if (LANGUE.value === "en") {
                const H3 = document.querySelector('.photo h3');
                // Traduction du titre
                switch (sousHref) {
                    case "/section/section-photos/section-photos-golf.html":
                        H3.innerHTML = "Here is the sub section of the pictures on the golf :";
                        break;
                    case "/section/section-photos/section-photos-hiver.html":
                        H3.innerHTML = "Here is the sub section of the pictures on the winter :";
                        break;
                    case "/section/section-photos/section-photos-poker.html":
                        H3.innerHTML = "Here is the sub section of the pictures on the poker :";
                        break;
                    case "/section/section-photos/section-photos-ski.html":
                        H3.innerHTML = "Here is the sub section of the pictures on the skiing :";
                        break;
                    case "/section/section-photos/section-photos-velo.html":
                        H3.innerHTML = "Here is the sub section of the pictures on the bike :";
                        break;
                }
            }
        });
    }
    // Sinon, on ne fait rien, car nous avons encore les photos
}

/**
 * Retourne un titre formaté pour la section où nous nous trouvons
 *
 * @returns {string}
 */
function recupere_formate_titre_section(){

    let titre_ajuste = "";
    switch (HASH_TAG.value){
        // Il va avoir deux cases par section vue que se n'est pas les mêmes en anglais qu'en français
        case "#accueil": case "#home":
            if (LANGUE.value === "fr") {
                titre_ajuste = "Accueil";
            } else if (LANGUE.value === "en") {
                titre_ajuste = "Home";
            }
            break;
        case "#projets": case "#projects":
            if (LANGUE.value === "fr") {
                titre_ajuste = "Projets";
            } else if (LANGUE.value === "en") {
                titre_ajuste = "Projects";
            }
            break;
        case "#photos": case "#pictures":
            if (LANGUE.value === "fr") {
                switch (HASH_TAG_SECOND.value){
                    case "#poker": titre_ajuste = "Photos-poker"; break;
                    case "#golf": titre_ajuste = "Photos-golf"; break;
                    case "#velo": titre_ajuste = "Photos-velo"; break;
                    case "#hiver": titre_ajuste = "Photos-hiver"; break;
                    case "#ski": titre_ajuste = "Photos-ski"; break;
                    // HASH_TAG_SECOND.value est NULL
                    default : titre_ajuste = "Photos";
                }

            } else if (LANGUE.value === "en") {
                switch (HASH_TAG_SECOND.value){
                    case "#poker": titre_ajuste = "Pictures-poker"; break;
                    case "#golf": titre_ajuste = "Pictures-golf"; break;
                    case "#bike": titre_ajuste = "Pictures-bike"; break;
                    case "#winter": titre_ajuste = "Pictures-winter"; break;
                    case "#skiing": titre_ajuste = "Pictures-skiing"; break;
                    // HASH_TAG_SECOND.value est NULL
                    default : titre_ajuste = "Pictures";
                }
            }
            break;
        case "#propos": case "#about":
            if (LANGUE.value === "fr") {
                titre_ajuste = "À propos";
            } else if (LANGUE.value === "en") {
                titre_ajuste = "About me";
            }
            break;
    }

    return titre_ajuste;
}

function recupere_calendrier_call_ajax() {

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
                let dataObj = JSON.parse(dataReturn["data"]);
                CALENDRIER_AJAX.innerHTML = dataObj.tableau_calendrier;
                // Après l'affichage du calendrier, on call le temps du timer et voilà
                start_timer();
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
 *
 * @returns {void}
 */
function start_timer() {

    const insertion_time = document.querySelector('.contenu_ligne_heure_actuel');
    let date_live = new Date();
    let date_affiche;
    if (LANGUE.value === "fr") {
        date_affiche = remplissage_zero(date_live.getHours()) + ":" + remplissage_zero(date_live.getMinutes()) + ":" + remplissage_zero(date_live.getSeconds());
        insertion_time.innerHTML = date_affiche;
        setTimeout("start_timer()", 1000);
    } else if (LANGUE.value === "en") {
        date_affiche = format_AM_PM(date_live);
        insertion_time.innerHTML = date_affiche;
        setTimeout("start_timer()", 1000);
    }
}

/**
 * Retourne l'heure ou les minutes ou les secondes avec un 0 devant le chiffre
 *
 * @param {number} valeur
 * @returns {string}
 */
function remplissage_zero(valeur) {

    return (valeur > 9) ? "" + valeur : "0" + valeur;
}

/**
 * Retourne l'heure avec la mention AM ou PM en fonction s'il est midi et plus ou pas
 *
 * @param {Date} date
 * @returns {string}
 */
function format_AM_PM(date) {

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
function affichage_section_photo() {

    // Cette constante existe seulement si la section Photo est sélectionnée
    const LISTE_PASSIONS = document.querySelectorAll('.middle .center .sous-header .une-passion-photo a');
    let tagSousSection = $(LISTE_PASSIONS).filter("[href='" + location.hash + "']");
    // On récupère le lien vers le fichier HTML qui contient tous les liens des photos
    let sousHref = tagSousSection.data('href');
    HASH_TAG_SECOND.value = tagSousSection.attr('href');
    console.log(HASH_TAG_SECOND.value);
    // TODO : Uncaught TypeError: Cannot read properties of undefined (reading 'indexOf')
    $(DIV_PHOTO).load(sousHref, function () {
        const H3 = document.querySelector('.photo h3');
        // Modifier le titre de la page en fonction de quelle section de photo, on fait afficher
        document.title = recupere_formate_titre_section();
        if (LANGUE.value === "en") {
            switch (sousHref) {
                case "/section/section-photos/section-photos-golf.html":
                    H3.innerHTML = "Here is the sub section of the pictures on the golf :";
                    break;
                case "/section/section-photos/section-photos-hiver.html":
                    H3.innerHTML = "Here is the sub section of the pictures on the winter :";
                    break;
                case "/section/section-photos/section-photos-poker.html":
                    H3.innerHTML = "Here is the sub section of the pictures on the poker :";
                    break;
                case "/section/section-photos/section-photos-ski.html":
                    H3.innerHTML = "Here is the sub section of the pictures on the skiing :";
                    break;
                case "/section/section-photos/section-photos-velo.html":
                    H3.innerHTML = "Here is the sub section of the pictures on the bike :";
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
function envoyer_courriel() {

    $(FORM_CONTACT).submit(function (e) {
        e.preventDefault();
        MSG_SUCCES.innerHTML = "";
        MSG_ERR.innerHTML = "";
        let erreur = false;

        // Lorsque le nom est manquant
        if (NOM_COMPLET.value === "") {
            NOM_COMPLET.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>Champ nom et prénom est vide</li>";
            } else {
                MSG_ERR.innerHTML += "<li>Surname and first name field is empty</li>";
            }
            erreur = true;
        } else if (NOM_COMPLET.value.length > 30) {
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
        if (COURRIEL.value === "") {
            COURRIEL.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>Champ du courriel est vide</li>";
            } else {
                MSG_ERR.innerHTML += "<li>Email field is empty</li>";
            }
            erreur = true;
        } else if (COURRIEL.value.length > 30) {
            COURRIEL.style.border = "2px solid red";
            if (LANGUE.value === "fr") {
                MSG_ERR.innerHTML += "<li>L'information dans le champ email est trop long</li>";
            } else {
                MSG_ERR.innerHTML += "<li>The information in the email field is too long</li>";
            }
            erreur = true;
        } else {
            COURRIEL.style.border = "initial";
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
        } else if (SUJET.value.length > 30) {
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
        } else if (MESSAGE.value.length > 250) {
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
            let url = "/contact/contact.php";

            request = $.ajax({
                url: url,
                type: "post",
                data: serializedData
            });

            // Callback handler that will be called on success
            // TODO : vérifier si les variables inutilisé sont utile ou pas
            // response, textStatus, jqXHR
            request.done(function () {
                if (LANGUE.value === "fr") {
                    MSG_SUCCES.innerHTML = "Votre message a été envoyé";
                } else if (LANGUE.value === "en") {
                    MSG_SUCCES.innerHTML = "Your message has been sent";
                }
            });

            // Callback handler that will be called on failure
            // TODO : vérifier si les variables inutilisé sont utile ou pas
            // jqXHR, textStatus, errorThrown
            request.fail(function () {
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
    $(window).on('hashchange', function () {
        affichage_section();
    });

    // Si on inscrit manuellement un HASH_TAG, on call la fct sinon on call la fct de base
    if (location.hash !== "") {
        affichage_section();
    } else {
        affichage_accueil();
    }

    recupere_calendrier_call_ajax();
    activation_liste();
    envoyer_courriel();
});
