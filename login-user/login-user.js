// Ce fichier JS sera utilisé par login-user et create-user-poker-stats

// Champs user et password
const champs_user = document.querySelector('#user');
const champs_password = document.querySelector('#password');

// champ pour la création des users seulement
const champs_password_conf = document.querySelector('#password-conf');
const champs_email = document.querySelector('#email');

// La section du ou des messages d'erreurs
const message_erreur = document.querySelector('.footer .erreur')

// Les boutons actions possibles dans la page
const faire_menage_total = document.querySelector('#faire-menage-total');

// Sélectionnez tous les éléments avec la classe d'erreur
const error_elements = document.querySelectorAll('.erreur input');

/**
 * Fonction pour effacer les champs et messages erreurs
 */
function effacement_complet() {
    faire_menage_total.addEventListener('click', function (evt) {
        effacer_tous_les_champs();
    });
}

/**
 * Fonction qui s'occupera de remettre aux états originaux les différents champs des pages
 * login-user
 * create-user-poker-stats
 */
function effacer_tous_les_champs() {

    // Réinitialiser les styles à leur état initial
    reinitialisation_champ(champs_user);
    reinitialisation_champ(champs_password);

    // Champ seulement pour la création des users
    if (champs_password_conf !== null){

        reinitialisation_champ(champs_password_conf);
    }

    // Champ seulement pour la création des users
    if (champs_email !== null){

        reinitialisation_champ(champs_email);
    }

    if (message_erreur != null){
        message_erreur.remove();
    }

    // On remet le focus sur le champ username
    champs_user.focus();
}

/**
 * Fonction pour remettre les attributs de base pour tous les champs.
 * Peu importe ce qui se passe avec ledit champ
 *
 * @param champ -> l'input qui sera transformé
 */
function reinitialisation_champ(champ){

    // Validation pour cleaner le champ
    if (champ.value !== ""){
        champ.defaultValue = "";
    }

    champ.style.borderWidth = "1px";
    champ.style.borderColor = "rgb(133, 133, 133";
    champ.style.backgroundColor = "white";
    champ.style.padding = "2px 2px 2px 2px";
}

/**
 * Fonction pour remettre sur le 1er champ qui tombe en erreur avec des informations manquantes
 */
function surveillance_focus_a_changer() {

    // Si des éléments d'erreur existent, déplacez le focus sur le premier
    if (error_elements.length > 0) {
        error_elements[0].focus();
    }
}

/**
 * Une fois que la page est chargée, on fera appel aux fonctions ci-dessous
 */
document.addEventListener('DOMContentLoaded', function () {

    effacement_complet();
    surveillance_focus_a_changer();
});
