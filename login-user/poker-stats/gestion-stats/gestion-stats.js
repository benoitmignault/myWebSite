// Les champs utilisés pour saisir les statistiques
const champ_liste_joueurs = document.getElementById('liste-joueurs');
const zone_position = document.querySelector('.erreur-choix');
const champ_position = document.getElementsByName('position');

const champ_gain = document.querySelector('#gain');
const champ_num_tournois = document.querySelector('#tournois');
const champ_date = document.querySelector('#date');

const champ_killer = document.querySelector('#killer');
const champ_citron = document.querySelector('#citron');

// Le champ utilisé pour ajouter un nouveau joueur
const champ_new_player = document.querySelector('#new-player');


// Les boutons actions possibles dans la page
const faire_menage_total = document.querySelector('#faire-menage-total');

// Sélectionnez tous les éléments avec la classe d'erreur
const section_message = document.querySelector('.liste-message');

/**
 * En fonction de quelle position le joueur a terminé,
 * certaines informations se rempliront automatiquement
 */
function type_position_selected() {

    $(champ_position).change(function () {

        switch ($(this).val()) {
            case 'victoire':
                champ_citron.value = "0";
                champ_gain.value = "";
                break;
            case 'fini2e':
                champ_gain.value = "0";
                champ_citron.value = "0";
                break;
            case 'autre':
                champ_gain.value = "-20";
                champ_citron.value = "";
                break;
        }
    });

    $(champ_citron).change(function () {

        if (this.value === '1') {
            champ_killer.value = "0";
            champ_gain.value = "-20";
        } else if (this.value > '1' || this.value < '0') {
            alert("La valeur doit être 1 ou 0 et elle ne peut être négative !");
            this.value = "";
        }
    });

    $(champ_killer).change(function () {

        if (this.value > 0) {
            champ_citron.value = 0;
        } else if (this.value < 0) {
            alert("La valeur doit être supérieur à 0 !");
            this.value = "";
        }
    });

    $(champ_num_tournois).change(function () {
        if (this.value < '1') {
            alert("Le numéro du tournoi ne peut être de valeur 0 ou négative !");
            this.value = "";
        }
    });
}

/**
 * Fonction pour effacer les champs et messages erreurs
 */
function effacement_complet() {
    faire_menage_total.addEventListener('click', function () {
        effacer_tous_les_champs();
    });
}

/**
 * Fonction qui s'occupera de remettre aux états originaux les différents champs
 */
function effacer_tous_les_champs() {

    // TODO -> Trouver un moyen d'effacer les radio button - Impossible apres un POST
    // TODO -> Essayer de trouver une facon d'effacer le champ
    // Section pour ajouter des stats
    reinitialisation_champ(champ_gain);
    reinitialisation_champ(champ_num_tournois);
    reinitialisation_champ(champ_killer);
    reinitialisation_champ(champ_citron);
    reinitialisation_champ(champ_liste_joueurs);

    // Sélectionne la première option et désélectionner l'option préalablement choisie
    champ_liste_joueurs.options[0].setAttribute("class", "selected");
    champ_liste_joueurs.options[champ_liste_joueurs.selectedIndex].removeAttribute("selected");

    // Le champ position sera légèrement différent, car c'est le div qu'on va faire changer de couleur
    if (zone_position != null){
        zone_position.style.backgroundColor = "#b0b0b0";
        zone_position.style.border = '1px solid black';
    }

    // Remise à NULL, de la section des messages
    if (section_message != null){
        section_message.innerHTML = "";
    }

    // Section pour ajouter des nouveaux joueurs
    reinitialisation_champ(champ_new_player);

    // Retirer le choix de position
    $(champ_position).prop('checked', false);

    // TODO - Essayer de trouver une facon d'effacer le champ
    // reinitialisation_champ(champ_date);
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

    champ.style.backgroundColor = "white";
    champ.style.borderRadius = '10px';
    champ.style.padding = '5px';
    champ.style.border = '1px solid #ccc';
    champ.style.marginTop = '6px';
    champ.style.marginBottom = '16px';
}

// TODO à remplir
function reinitialisation_champ_apres_succes(){

}


document.addEventListener('DOMContentLoaded', function () {

    type_position_selected();
    effacement_complet();
    //reinitialisation_champ_apres_succes();
});
