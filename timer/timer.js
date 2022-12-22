const CHANGER_MISE = document.querySelector('#changerMise');

const TEMPS_PERIODE = document.querySelector('.container .timer .tableauDuTemps .temps div .resizeText');
const TYPE_MISES = document.querySelector('.container .timer .tableauDesMises .lesMises div .resizeText');

const RESET_VALEUR = document.querySelector('#reset');
const RESET_TEMPS = document.querySelector('#ResetTemps');

const TYPE_LANGUE = document.querySelector('#typeLangue');

const TEMPS_15_MIN = document.querySelector('#timer15');
const TEMPS_30_MIN = document.querySelector('#timer30');

const REPREND_TIMER = document.querySelector('#timerReprend');
const STOP_TIMER = document.querySelector('#timerStop');

const VALEUR_SMALL = document.querySelector('#valeurSmall');
const VALEUR_BIG = document.querySelector('#valeurBig');

const MINUTES = document.querySelector('.chiffreMin p');
const SECONDES = document.querySelector('.chiffreSec p');

const PERIODE = document.querySelector('.periode p');
const ALERT_SOUND = document.querySelector('#alertSound');

const AUCUNE_VALEUR_DISPO = document.querySelector('#aucuneValeurDispo');
const COMBINAISON = document.querySelector('.combinaison');
const MAX_COMBINAISON = document.querySelector('.maxCombinaison');

const NOM_ORGANISATEUR = document.querySelector('#choixOrganisateur');
const COULEUR_ROUGE = document.querySelector('#numberRed');
const COULEUR_VERT = document.querySelector('#numberGreen');
const COULEUR_BLEU = document.querySelector('#numberBlue');

const BTN_RETURN = document.querySelector('.boutonRetour .retour form .resizeText');
const BTN_CHOISIR = document.querySelector('.container .tableau_bord form .choix .bouton');

let comptage = 0; // la variable un genre de compteur de temps
let min = 0;
let sec = 0;
let chrono = 0; // pour savoir quel compteur est déclenche...

/**
 * Est appelé une seule fois et au moment de cliquer sur le bouton 15 minutes.
 */
function timer15Min() {
    TEMPS_15_MIN.addEventListener('click', function () {
        miseEnMarcheDuTimer();
        min = 15;
        sec = 0;
        chrono = 15;
        MINUTES.style.color = "green";
        SECONDES.style.color = "green";
        MINUTES.innerHTML = min.toString();
        SECONDES.innerHTML = "0" + sec.toString();

        if (TYPE_LANGUE.value === "francais") {
            PERIODE.innerHTML = "Période choisi ⇒ 15 minutes";
        } else if (TYPE_LANGUE.value === "english") {
            PERIODE.innerHTML = "Chosen period ⇒ 15 minutes";
        }

        comptage = setTimeout(starting15, 1000);
    });
}

/**
 * Vérifier à chaque cycle de 1000 milliseconde, ce qu'on doit faire durant le cycle de 15 minutes.
 */
function starting15() {
    if (sec === 0) {
        if (min === 0) {
            callAjax();
            return;
        } else {
            min--;
            sec = 59;
        }
    } else {
        sec--;
        playMusique();
    }

    if (min >= 10) {
        MINUTES.style.color = "green";
        SECONDES.style.color = "green";
    } else if (min < 10 && min >= 5) {
        MINUTES.style.color = "orange";
        SECONDES.style.color = "orange";
    } else if (min < 5) {
        MINUTES.style.color = "red";
        SECONDES.style.color = "red";
    }

    if (min < 10 || sec < 10) {
        addZero();
    } else {
        MINUTES.innerHTML = min.toString();
        SECONDES.innerHTML = sec.toString();
    }

    comptage = setTimeout(starting15, 1000);
}

function timer30Min() {
    TEMPS_30_MIN.addEventListener('click', function () {
        miseEnMarcheDuTimer();
        min = 30;
        sec = 0;
        chrono = 30;
        MINUTES.style.color = "green";
        SECONDES.style.color = "green";
        MINUTES.innerHTML = min.toString();
        SECONDES.innerHTML = "0" + sec.toString();
        // modifier la valeur en fonction de la langue choisie par l'usagé de la page
        if (TYPE_LANGUE.value === "francais") {
            PERIODE.innerHTML = "Période choisi ⇒ 30 minutes";
        } else if (TYPE_LANGUE.value === "english") {
            PERIODE.innerHTML = "Chosen period ⇒ 30 minutes";
        }
        comptage = setTimeout(starting30, 1000);
    });
}

function starting30() {
    if (sec === 0) {
        if (min === 0) {
            callAjax();
            return;
        } else {
            min--;
            sec = 59;
        }
    } else {
        sec--;
        playMusique();
    }
    if (min >= 15) {
        MINUTES.style.color = "green";
        SECONDES.style.color = "green";
    } else if (min < 15 && min >= 5) {
        MINUTES.style.color = "orange";
        SECONDES.style.color = "orange";
    } else if (min < 5) {
        MINUTES.style.color = "red";
        SECONDES.style.color = "red";
    }

    if (min < 10 || sec < 10) {
        addZero();
    } else {
        MINUTES.innerHTML = min.toString();
        SECONDES.innerHTML = sec.toString();
    }
    comptage = setTimeout(starting30, 1000);
}


function miseEnMarcheDuTimer() {

    clearTimeout(comptage);
    REPREND_TIMER.setAttribute("class", "disabled");
    REPREND_TIMER.setAttribute("disabled", "disabled");
    STOP_TIMER.setAttribute("class", "");
    STOP_TIMER.removeAttribute("disabled");
    RESET_TEMPS.setAttribute("class", "");
    RESET_TEMPS.removeAttribute("disabled");
    CHANGER_MISE.setAttribute("class", "disabled");
    CHANGER_MISE.setAttribute("disabled", "disabled");
}


function addZero() {
    if (min < 10) {
        MINUTES.innerHTML = "0" + min.toString();
    } else {
        MINUTES.innerHTML = min.toString();
    }

    if (sec < 10) {
        SECONDES.innerHTML = "0" + sec.toString();
    } else {
        SECONDES.innerHTML = sec.toString();
    }
}

function callAjax() {
    STOP_TIMER.setAttribute("class", "disabled");
    STOP_TIMER.setAttribute("disabled", "disabled");
    if (NOM_ORGANISATEUR.value !== "") {
        if (AUCUNE_VALEUR_DISPO.value === "false") {
            CHANGER_MISE.setAttribute("class", "");
            CHANGER_MISE.removeAttribute("disabled");
            RESET_VALEUR.setAttribute("class", "");
            RESET_VALEUR.removeAttribute("disabled");
            let data = {
                "niveauCombinaison": COMBINAISON.value,
                "maxCombinaison": MAX_COMBINAISON.value,
                "nomOrganisateur": NOM_ORGANISATEUR.value,
                "couleurRouge": COULEUR_ROUGE.value,
                "couleurVert": COULEUR_VERT.value,
                "couleurBleu": COULEUR_BLEU.value
            };

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "changerMise/changer.php",
                data: data,
                success: function (dataReturn) {
                    // sécurisation du retour d'information
                    if (dataReturn["data"]) {
                        let dataObj = JSON.parse(dataReturn["data"]);
                        // TODO : repasser à travers ces variables là
                        AUCUNE_VALEUR_DISPO.value = dataObj.aucuneValeurDispo;
                        COMBINAISON.value = dataObj.combinaison;
                        COULEUR_ROUGE.value = dataObj.couleurRouge;
                        COULEUR_VERT.value = dataObj.couleurVert;
                        COULEUR_BLEU.value = dataObj.couleurBleu;
                        VALEUR_SMALL.innerHTML = dataObj.nouvelleCombinaison.valeurSmall;
                        VALEUR_BIG.innerHTML = dataObj.nouvelleCombinaison.valeurBig;
                        VALEUR_SMALL.style.color = "rgb(" + COULEUR_ROUGE.value + "," + COULEUR_VERT.value + "," + COULEUR_BLEU.value + ")";
                        VALEUR_BIG.style.color = "rgb(" + COULEUR_ROUGE.value + "," + COULEUR_VERT.value + "," + COULEUR_BLEU.value + ")";

                        // Déplacement de la logique si nous avons atteint la dernière valeur
                        if (AUCUNE_VALEUR_DISPO.value === "true") {
                            CHANGER_MISE.setAttribute("class", "disabled");
                            CHANGER_MISE.setAttribute("disabled", "disabled");
                            RESET_TEMPS.setAttribute("class", "disabled");
                            RESET_TEMPS.setAttribute("disabled", "disabled");
                        }

                    } else if (dataReturn["erreur"]) {
                        let dataErr = JSON.parse(dataReturn["erreur"]);
                        if (TYPE_LANGUE.value === "english") {
                            // TODO : refaire les situation
                            if (dataErr.situation1) {
                                alert("Crucial information is missing to retrieve information from the DB.");
                            } else if (dataErr.situation2) {
                                alert("This file must be called via an AJAX call.");
                            }
                        } else if (TYPE_LANGUE.value === "francais") {
                            if (dataErr.situation1) {
                                alert(dataErr.situation1);

                            } else if (dataErr.situation2) {
                                alert(dataErr.situation2);
                            }
                        }
                    }
                }
            });
        } else {
            RESET_TEMPS.setAttribute("class", "disabled");
            RESET_TEMPS.setAttribute("disabled", "disabled");
        }
    }
}

function playMusique() {
    if (min === 0 && sec < 7) {
        ALERT_SOUND.play();
    }
}

function reprendreTemps() {
    REPREND_TIMER.addEventListener('click', function () {
        clearTimeout(comptage); // arrête le coulage du temps
        REPREND_TIMER.setAttribute("class", "disabled");
        REPREND_TIMER.setAttribute("disabled", "disabled");
        STOP_TIMER.setAttribute("class", "");
        STOP_TIMER.removeAttribute("disabled");
        TEMPS_15_MIN.setAttribute("class", "");
        TEMPS_15_MIN.removeAttribute("disabled");
        TEMPS_30_MIN.setAttribute("class", "");
        TEMPS_30_MIN.removeAttribute("disabled");

        switch (chrono) {
            case 15:
                comptage = setTimeout(starting15, 1000);
                break;
            case 30:
                comptage = setTimeout(starting30, 1000);
                break;
        }
    });
}

function stop() {
    STOP_TIMER.addEventListener('click', function () {
        clearTimeout(comptage); // arrête le coulage du temps
        REPREND_TIMER.setAttribute("class", "");
        REPREND_TIMER.removeAttribute("disabled");
        STOP_TIMER.setAttribute("class", "disabled");
        STOP_TIMER.setAttribute("disabled", "disabled");
        TEMPS_15_MIN.setAttribute("class", "disabled");
        TEMPS_15_MIN.setAttribute("disabled", "disabled");
        TEMPS_30_MIN.setAttribute("class", "disabled");
        TEMPS_30_MIN.setAttribute("disabled", "disabled");
    });
}

function resetTemp() {
    RESET_TEMPS.addEventListener('click', function () {
        clearTimeout(comptage); // arrête le coulage du temps
        chrono = 0;
        min = "00";
        sec = "00";
        MINUTES.style.color = "white";
        SECONDES.style.color = "white";
        MINUTES.innerHTML = min.toString();
        SECONDES.innerHTML = sec.toString();
        if (TYPE_LANGUE.value === "francais") {
            PERIODE.innerHTML = "Sélectionner votre Période";
        } else if (TYPE_LANGUE.value === "english") {
            PERIODE.innerHTML = "Select your Period";
        }

        TEMPS_15_MIN.setAttribute("class", "");
        TEMPS_15_MIN.removeAttribute("disabled");
        TEMPS_30_MIN.setAttribute("class", "");
        TEMPS_30_MIN.removeAttribute("disabled");
        REPREND_TIMER.setAttribute("class", "disabled");
        REPREND_TIMER.setAttribute("disabled", "disabled");
        STOP_TIMER.setAttribute("class", "disabled");
        STOP_TIMER.setAttribute("disabled", "disabled");
        RESET_TEMPS.setAttribute("class", "disabled");
        RESET_TEMPS.setAttribute("disabled", "disabled");

        if (NOM_ORGANISATEUR.value !== "" && AUCUNE_VALEUR_DISPO.value === "false") {
            CHANGER_MISE.setAttribute("class", "");
            CHANGER_MISE.removeAttribute("disabled");
        }
    });
}

// TODO : Comprendre pourquoi ca marche pas
function resizeText() {
    let device = detectZoom.device();
    console.log(device);
    let largeur = window.innerWidth;

    if (largeur < 1300) {
        if (device > 1.2) {
            REPREND_TIMER.style.fontSize = "20px";
            TEMPS_PERIODE.style.fontSize = "30px";
            TYPE_MISES.style.fontSize = "30px";
            BTN_RETURN.style.fontSize = "20px";
            BTN_CHOISIR.style.fontSize = "20px";
        } else if (device >= 1.1) {
            REPREND_TIMER.style.fontSize = "25px";
            TEMPS_PERIODE.style.fontSize = "35px";
            TYPE_MISES.style.fontSize = "35px";
            BTN_RETURN.style.fontSize = "25px";
            BTN_CHOISIR.style.fontSize = "25px";
        } else if (device >= 1) {
            REPREND_TIMER.style.fontSize = "30px";
            TEMPS_PERIODE.style.fontSize = "40px";
            TYPE_MISES.style.fontSize = "40px";
            BTN_RETURN.style.fontSize = "30px";
            BTN_CHOISIR.style.fontSize = "30px";
        }
    } else if (largeur > 1300) {
        if (device > 1.4) {
            REPREND_TIMER.style.fontSize = "20px";
            TEMPS_PERIODE.style.fontSize = "30px";
            TYPE_MISES.style.fontSize = "30px";
            BTN_RETURN.style.fontSize = "20px";
            BTN_CHOISIR.style.fontSize = "20px";
        } else if (device > 1.2) {
            REPREND_TIMER.style.fontSize = "30px";
            TEMPS_PERIODE.style.fontSize = "35px";
            TYPE_MISES.style.fontSize = "35px";
            BTN_RETURN.style.fontSize = "30px";
            BTN_CHOISIR.style.fontSize = "30px";
        } else if (device > 1.1) {
            REPREND_TIMER.style.fontSize = "35px";
            TEMPS_PERIODE.style.fontSize = "40px";
            TYPE_MISES.style.fontSize = "40px";
            BTN_RETURN.style.fontSize = "35px";
            BTN_CHOISIR.style.fontSize = "35px";
        }
    }
    modificationSizeValeurs(VALEUR_BIG.innerHTML);
    // 1.1041666269302368 -> 100%
    // 1.2145832777023315 -> 110%
    // 1.3802082538604736 -> 125%
}

function modificationSizeValeurs(big) {
    // Tableau de valeurs de fontSize en fonction de bigNumber
    const fontSizes = [64, 56, 48, 40];

    // Pré-calcul de la largeur de la fenêtre
    const windowWidth = window.innerWidth;

    // Arrondi de bigNumber à l'entier le plus proche
    const bigNumber = Math.floor(parseInt(big));

    // Si la largeur de la fenêtre est inférieure à 768
    if (windowWidth < 768) {
        // Détermination de l'index du tableau à partir de bigNumber
        let index = 0;
        if (bigNumber >= 1000) {
            index = 1;
        }
        if (bigNumber >= 10000) {
            index = 2;
        }
        if (bigNumber >= 100000) {
            index = 3;
        }

        // Mise à jour de la taille de police avec la valeur du tableau
        VALEUR_SMALL.style.fontSize = `${fontSizes[index]}px`;
        VALEUR_BIG.style.fontSize = `${fontSizes[index]}px`;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    resizeText(); // TODO : Comprendre pourquoi ca marche pas
    timer15Min();
    timer30Min();
    stop();
    reprendreTemps();
    resetTemp();
});