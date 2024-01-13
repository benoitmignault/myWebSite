// Champs text
const champs_user = document.querySelector('#user');
const champs_password = document.querySelector('#password');
const message_erreur = document.querySelector('.footer .erreur')

// Les boutons actions possibles dans la page
const faire_menage_total = document.querySelector('#faire_menage_total');


// Fonction principale qui va caller d'autres fct et sous fct
function effacement_complet() {
    faire_menage_total.addEventListener('click', function (evt) {
        effacer_tous_les_champs();
    });
}

function effacer_tous_les_champs() {

    // Validation pour cleaner le champ
    if (champs_user.value !== ""){
        champs_user.defaultValue = "";
    }

    // Validation pour cleaner le champ
    if (champs_password.value !== ""){
        champs_password.defaultValue  = "";
    }

    // Réinitialiser les styles à leur état initial
    champs_user.style.borderWidth = "1px";
    champs_user.style.borderColor = "rgb(133, 133, 133";
    champs_user.style.backgroundColor = "white";
    champs_user.style.padding = "2px 2px 2px 2px";

    champs_password.style.borderWidth = "1px";
    champs_password.style.borderColor = "rgb(133, 133, 133";
    champs_password.style.backgroundColor = "white";
    champs_password.style.padding = "2px 2px 2px 2px";

    if (message_erreur != null){
        message_erreur.remove();
    }

    // On remet le focus sur le champ username
    champs_user.focus();
}

document.addEventListener('DOMContentLoaded', function (event) {

    effacement_complet();
});
