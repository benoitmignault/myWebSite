const section_info = document.querySelector('.section_info');
const info_liste = document.querySelector('#info_Instruction'); // on garde l'info lors du reload de la page
const info_langue = document.querySelector('#info_langue'); // on garde l'info lors du reload de la page
const liste_info = document.querySelector('.lesInstruction'); // la liste qui sera caché
const affichage = document.querySelector('.faireAfficher'); // le texte du lien qui changera
const link = document.querySelector('#link'); // le lien qu'on devra modifier dynamiquement


// Sera utiliser lorsqu'on arrive sur la page web
function verifier_affichage() {
    if (info_liste.value === "display") {

        // Ajout de cette validation, car sinon on va avoir une erreur JS
        if (liste_info != null){
            liste_info.style.display = "block";
            section_info.style.marginBottom = "0px";
            // Traduction
            if (info_langue.value === "francais"){
                affichage.innerHTML = "Information utile";
            } else if (info_langue.value === "english") {
                affichage.innerHTML = "Useful information";
            }
        }

    } else if (info_liste.value === "hidden") {

        // Ajout de cette validation, car sinon on va avoir une erreur JS
        if (liste_info != null){
            liste_info.style.display = "none";
            section_info.style.marginBottom = "15px";
            // Traduction
            if (info_langue.value === "francais"){
                affichage.innerHTML = "Afficher l'information utile";
            } else if (info_langue.value === "english") {
                affichage.innerHTML = "Display useful information";
            }
        }
    }
} 

// À chaque clique pour ouvrir ou fermer l'information utile
function afficher_Instructions() {

    // Rajouter cette condition, car si nous ne sommes pas dans l'option 4 ou 7, j'avais une erreur JS
    if (affichage != null){
        affichage.addEventListener('click', function (evt) {
            evt.preventDefault();
            if (liste_info.style.display === "none") {
                liste_info.style.display = "block";
                section_info.style.marginBottom = "0px";
                info_liste.value = "display"; // input prend la valeur de display pour le reload de la page
                // Traduction
                if (info_langue.value === "francais"){
                    affichage.innerHTML = "Information utile";
                } else if (info_langue.value === "english") {
                    affichage.innerHTML = "Useful information";
                }

            } else if (liste_info.style.display === 'block') {
                liste_info.style.display = "none";
                section_info.style.marginBottom = "15px";
                info_liste.value = "hidden"; // input prend la valeur de hidden pour le reload de la page
                // Traduction
                if (info_langue.value === "francais"){
                    affichage.innerHTML = "Afficher l'information utile";
                } else if (info_langue.value === "english") {
                    affichage.innerHTML = "Display useful information";
                }
            }
        });
    }
}

// On rajoute l'ancrage pour aller directement au tableau de résultats
function selection_triage() {

    // Rajouter cette condition, car si nous ne sommes pas dans l'option 4 ou 7, j'avais une erreur JS
    if (link != null){
        link.addEventListener('click', function (evt) {
            link.href += "&visible_Info=" + info_liste.value + "#endroitResultat";
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    afficher_Instructions();
    verifier_affichage();
    selection_triage();
});