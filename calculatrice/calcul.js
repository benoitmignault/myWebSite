// liste des instructions
const liste_info = document.querySelector('.lesInstruction');
// le text field cache pour stocker l'information à savori si les instructions doivent être cachées ou pas
const info_liste_cache = document.querySelector('#info_Instruction');
// le lien qui va permettre afficher ou pas les instructions
const affichage = document.querySelector('.faireAfficher');
const typeLangue = document.querySelector('.typeLanguage');

function verifier_affichage() {
    if (info_liste_cache.value === "display") {
        liste_info.style.display = "block";
        if (typeLangue.value === "francais") {
            affichage.innerHTML = "Procédure d'utilisation";
        } else if (typeLangue.value === "english") {
            affichage.innerHTML = "How to use";
        }
    } else if (info_liste_cache.value === "hidden") {
        info_liste_cache.value = "hidden";
        if (typeLangue.value === "francais") {
            affichage.innerHTML = "Afficher la Procédure d'utilisation";
        } else if (typeLangue.value === "english") {
            affichage.innerHTML = "View the Usage Procedure";
        }
    }
}

function afficher_Instructions() {
    affichage.addEventListener('click', function(evt) {
        evt.preventDefault();
        if (liste_info.style.display === "") {
            liste_info.style.display = "block";
            info_liste_cache.value = "display";
            if (typeLangue.value === "francais") {
                affichage.innerHTML = "Procédure d'utilisation";
            } else if (typeLangue.value === "english") {
                affichage.innerHTML = "How to use";
            }
        } else if (liste_info.style.display === 'block') {
            liste_info.style.display = "";
            info_liste_cache.value = "hidden";
            if (typeLangue.value === "francais") {
                affichage.innerHTML = "Afficher la Procédure d'utilisation";
            } else if (typeLangue.value === "english") {
                affichage.innerHTML = "View the Usage Procedure";
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function(event) {
    verifier_affichage();
    afficher_Instructions();
});