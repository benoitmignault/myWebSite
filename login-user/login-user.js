// Information pour savoir ce qui doit rester afficher ou pas
const visible_info = document.querySelector('#visible_info');
const visible_courriel = document.querySelector('#visible_courriel');

// Section à faire cacher, modifier
const toutes_instructions = document.querySelectorAll('.lesInstruction'); // la liste qui sera caché
const lien_affichage = document.querySelector('.faireAfficher'); // Le lien qui permet de cacher ou d'afficher, la procédure
const section_courriel = document.querySelector('.courriel'); // Le DIV qui inclut le lebel et le input du courriel
const tous_h3 = document.querySelectorAll('h3');

// Champs text/hidden/radio button
const champs_courriel = document.querySelector('#courriel');
const champs_courriel_default = document.querySelector('#courriel_par_default');
const choix_action = document.getElementsByName('choix_action'); // Les deux radios buttons d'affichage
const choix_fournisseur = document.getElementsByName('choix_fournisseur'); // Les deux radios buttons pour fournisseur

// Les boutons actions possible dans l'outil
const nouvelle_recher = document.querySelector('#nouvelle_recher');
const faire_menage_total = document.querySelector('#faire_menage_total');

// Section information globale
const section_info = document.querySelector('.section_info');

// Section Bouton «I» informatif de l'outil
const btnInfo = document.querySelector('#info');
const divAffichage = document.querySelector('#dialog');

// Toutes les sections de résultats
const tous_results = document.querySelectorAll('.result');

function verifier_affichage() {

    // La section de la procédure d'utilisation
    if (visible_info.value === "display") {
        lien_affichage.innerHTML = "Procédure d'utilisation";
        section_info.style.marginBottom = "0px";

        // On doit afficher tous les block d'information (2 block)
        toutes_instructions.forEach(function (un_champ) {
            un_champ.style.display = "block";
            un_champ.style.margin = "10px";
        });

        toutes_instructions[1].style.marginBottom = "15px";

        // On rend disponible les deux titre en H3
        tous_h3.forEach(function (un_champ) {
            un_champ.style.display = "block";
            un_champ.style.margin = "5px";
        });

    } else if (visible_info.value === "hidden") {
        lien_affichage.innerHTML = "Afficher la Procédure d'utilisation";
        section_info.style.marginBottom = "15px";

        // On doit cacher tous les block d'information (2 block)
        toutes_instructions.forEach(function (un_champ) {
            un_champ.style.display = "none";
        });

        // On doit cacher les deux titre en H3
        tous_h3.forEach(function (un_champ) {
            un_champ.style.display = "none";
        });
    }

    // Section du courriel
    if (visible_courriel.value === "display") {

        // On affiche la section du courriel (label + champ)
        section_courriel.style.display = "flex";
    } else if (visible_courriel.value === "hidden") {

        // Sinon, non
        section_courriel.style.display = "none";
    }
}

function afficher_instructions() {
    lien_affichage.addEventListener('click', function (evt) {
        evt.preventDefault();
        if (visible_info.value == "display") {
            // On rend indisponible maintenant les deux block information 
            toutes_instructions.forEach(function (un_champ) {
                un_champ.style.display = "none";
            });
            // On rend indisponible les deux titre en H3
            tous_h3.forEach(function (un_champ) {
                un_champ.style.display = "none";
            });
            section_info.style.marginBottom = "15px";
            // Pour le prochain reload de la page, on vérifier ce champ
            visible_info.value = "hidden";
            lien_affichage.innerHTML = "Afficher la Procédure d'utilisation";

        } else if (visible_info.value == "hidden") {
            // On rend disponible maintenant les deux block information 
            toutes_instructions.forEach(function (un_champ) {
                un_champ.style.display = "block";
                un_champ.style.margin = "10px";
            });

            toutes_instructions[1].style.marginBottom = "15px";
            // On rend disponible les deux titre en H3
            tous_h3.forEach(function (un_champ) {
                un_champ.style.display = "block";
                un_champ.style.margin = "5px";
            });
            section_info.style.marginBottom = "0px";
            // Pour le prochain reload de la page, on vérifier ce champ
            visible_info.value = "display";
            lien_affichage.innerHTML = "Procédure d'utilisation";
        }
    });
}

// Pour faire afficher le bouton îcone i
function information() {
    $(function () {
        $(divAffichage).dialog({
            autoOpen: false,
            show: {
                effect: "blind",
                duration: 1000
            },
            hide: {
                effect: "explode",
                duration: 1000
            }
        });
        $(btnInfo).on('click', function (e) {
            e.preventDefault();
            $(divAffichage).dialog("open");
        });
    });

}

// fonction pour rendre dynamique la section du courriel
function afficher_section_courriel() {

    // Utilisation du mode Jquery
    $(choix_action).change(function () {

        if ($(this).val() == "action_afficher") {
            effacement_section_courriel();

        } else if ($(this).val() == "action_courriel") {

            section_courriel.style.display = "flex";
            visible_courriel.value = "display";
        }
    });
}

// Fonction principale qui va caller d'autres fct et sous fct
function effacement_complet() {
    faire_menage_total.addEventListener('click', function (evt) {
        effacer_tous_les_champs();
    });
}

// Va effacer tous les champs saisissable, les sections de résultats et la rmeise par défault des boutons radios
function effacer_tous_les_champs() {

    tous_results.forEach(function (un_champ) {
        if (un_champ != null) {
            un_champ.innerHTML = "";
        }
    });

    // Section pour remettre le choix à l'affichage sur la page web
    var tous_choix_affichage = document.getElementsByName("choix_action");
    var choix_afficher = document.querySelector('#action_afficher');
    $(tous_choix_affichage).attr('checked', false); // Tout mettre à False
    $(choix_afficher).attr('checked', true); // On remet Afficher coché

    // Section pour remettre le choix au fournisseur VTL
    var tous_choix_fournisseurs = document.getElementsByName("choix_fournisseur");
    var choix_fournisseur_VTL = document.querySelector('#fournisseur_vtl');
    $(tous_choix_fournisseurs).attr('checked', false); // Tout mettre à False
    $(choix_fournisseur_VTL).attr('checked', true); // On remet Afficher coché

    effacement_section_courriel();
}

// Lorsqu'on soumet une recherche de maniere standard
function nouvelle_recherche_standard() {
    nouvelle_recher.addEventListener('click', function (evt) {       
        effacer_tous_resultat();
        avertissement_nouvelle_recherche_encours();
    });
}

// Elle sera caller lors d'une nouvelle recherche
function effacer_tous_resultat() {
    tous_results.forEach(function (un_champ) {
        if (un_champ != null) {
            un_champ.innerHTML = "";
        }
    });
}

function avertissement_nouvelle_recherche_encours() {
    if (tous_results[0] != null) {
        tous_results[0].innerHTML = "En processus d'une nouvelle recherche...";
        tous_results[0].style.fontWeight = "bold";
        tous_results[0].style.textAlign = "center";
        tous_results[0].style.padding = "15px";
        tous_results[0].style.color = "black";
    }
}

// Centralisation des actions d'effacement pour la section courriel
function effacement_section_courriel() {

    // Remise à cacher de la section courriel
    section_courriel.style.display = "none";
    visible_courriel.value = "hidden";

    // Si on efface tout, on va remettre la valeur par défault du courriel qu'on avait au début.    
    champs_courriel.value = champs_courriel_default.value;
}

document.addEventListener('DOMContentLoaded', function (event) {
    verifier_affichage();

    afficher_instructions();
    afficher_section_courriel();

    information();
    effacement_complet();
    nouvelle_recherche_standard();
});
