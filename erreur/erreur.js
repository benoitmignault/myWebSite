// Bouton pour faire afficher l'information
const BTN = document.querySelector('#btn-affichage');
// Les sections explications dans les deux langues
const EXPLAIN = document.querySelector('#section-information');

document.addEventListener('DOMContentLoaded', function() {

    BTN.addEventListener('click', function() {
        $(EXPLAIN).load("erreur-explication.html");
    });
});