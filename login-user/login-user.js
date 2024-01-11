// Champs text
const champs_user = document.querySelector('#user');
const champs_password = document.querySelector('#password');

// Les boutons actions possibles dans la page
const faire_menage_total = document.querySelector('#faire_menage_total');


// Fonction principale qui va caller d'autres fct et sous fct
function effacement_complet() {
    faire_menage_total.addEventListener('click', function (evt) {
        effacer_tous_les_champs();
    });
}

function effacer_tous_les_champs() {

    // À traiter

}

// Elle sera caller lors d'une nouvelle recherche
function effacer_tous_resultat() {
    tous_results.forEach(function (un_champ) {
        if (un_champ != null) {
            un_champ.innerHTML = "";
        }
    });
}


document.addEventListener('DOMContentLoaded', function (event) {

    effacement_complet();
});
