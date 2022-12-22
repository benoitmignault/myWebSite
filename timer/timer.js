const changerMise = document.querySelector('#changerMise');

const tempsPeriode = document.querySelector('.container .timer .tableauDuTemps .temps div .resizeText');
const typeMises = document.querySelector('.container .timer .tableauDesMises .lesMises div .resizeText');

const resetValeur = document.querySelector('#reset');
const resetTemps = document.querySelector('#ResetTemps');

const typeLangue = document.querySelector('#typeLangue');

const temps15min = document.querySelector('#timer15');
const temps30min = document.querySelector('#timer30');

const reprendTimer = document.querySelector('#timerReprend');
const stopTimer = document.querySelector('#timerStop');

const valeurSmall = document.querySelector('#valeurSmall');
const valeurBig = document.querySelector('#valeurBig');

const minutes = document.querySelector('.chiffreMin p');
const secondes = document.querySelector('.chiffreSec p');

const periode = document.querySelector('.periode p');
const alertSound = document.querySelector('#alertSound');

const combinaison = document.querySelector('.combinaison');
const maxCombinaison = document.querySelector('.maxCombinaison');

const nomOrganisateur = document.querySelector('#choixOrganisateur');
const couleurRouge = document.querySelector('#numberRed');
const couleurVert = document.querySelector('#numberGreen');
const couleurBleu = document.querySelector('#numberBlue');

const aucuneValeurDispo = document.querySelector('#aucuneValeurDispo');

const btnReturn = document.querySelector('.boutonRetour .retour form .resizeText');
const btnChoisir = document.querySelector('.container .tableau_bord form .choix .bouton');

let comptage = 0; // la variable un genre de compteur de temps
let min = 0;
let sec = 0;
let chrono = 0; // pour savoir quel compteur est déclenche...

/**
 * Donne 15 minutes au cycle de temps.
 */
function timer15Min() {
    temps15min.addEventListener('click', function () {
        miseEnMarcheDuTimer();
        min = 15;
        sec = 0;
        chrono = 15;
        minutes.style.color = "green";
        secondes.style.color = "green";
        minutes.innerHTML = min.toString();
        secondes.innerHTML = "0" + sec.toString();
        // modifier la valeur en fonction de la langue choisie par l'usagé de la page
        if (typeLangue.value === "francais") {
            periode.innerHTML = "Période choisi ⇒ 15 minutes";
        } else if (typeLangue.value === "english") {
            periode.innerHTML = "Chosen period ⇒ 15 minutes";
        }
        comptage = setTimeout(starting15, 1000);
    });
}

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

    // On affiche la nouvelle valeur du temps à l'écran avec la couleur en fonction des minutes
    if (min >= 10) {
        minutes.style.color = "green";
        secondes.style.color = "green";
    } else if (min < 10 && min >= 5) {
        minutes.style.color = "orange";
        secondes.style.color = "orange";
    } else if (min < 5) {
        minutes.style.color = "red";
        secondes.style.color = "red";
    }

    if (min < 10 || sec < 10) {
        addZero(); // On vérifie Si les minutes ou secondes sont en bas de 10, on ajoute un 0 devant
    } else {
        minutes.innerHTML = min.toString();
        secondes.innerHTML = sec.toString();
    }
    comptage = setTimeout(starting15, 1000);
}

function timer30Min() {
    temps30min.addEventListener('click', function () {
        miseEnMarcheDuTimer();
        min = 30;
        sec = 0;
        chrono = 30;
        minutes.style.color = "green";
        secondes.style.color = "green";
        minutes.innerHTML = min.toString();
        secondes.innerHTML = "0" + sec.toString();
        // modifier la valeur en fonction de la langue choisie par l'usagé de la page
        if (typeLangue.value === "francais") {
            periode.innerHTML = "Période choisi ⇒ 30 minutes";
        } else if (typeLangue.value === "english") {
            periode.innerHTML = "Chosen period ⇒ 30 minutes";
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
        minutes.style.color = "green";
        secondes.style.color = "green";
    } else if (min < 15 && min >= 5) {
        minutes.style.color = "orange";
        secondes.style.color = "orange";
    } else if (min < 5) {
        minutes.style.color = "red";
        secondes.style.color = "red";
    }

    if (min < 10 || sec < 10) {
        addZero(); // On vérifie Si les minutes ou secondes sont en bas de 10, on ajoute un 0 devant
    } else {
        minutes.innerHTML = min.toString();
        secondes.innerHTML = sec.toString();
    }
    comptage = setTimeout(starting30, 1000);
}

function addZero() {
    if (min < 10) {
        minutes.innerHTML = "0" + min.toString();
    } else {
        minutes.innerHTML = min.toString();
    }

    if (sec < 10) {
        secondes.innerHTML = "0" + sec.toString();
    } else {
        secondes.innerHTML = sec.toString();
    }
}

function callAjax() {
    stopTimer.setAttribute("class", "disabled");
    stopTimer.setAttribute("disabled", "disabled");
    if (nomOrganisateur.value !== "") {
        if (aucuneValeurDispo.value === "false") {
            changerMise.setAttribute("class", "");
            changerMise.removeAttribute("disabled");
            resetValeur.setAttribute("class", "");
            resetValeur.removeAttribute("disabled");
            let data = {
                "niveauCombinaison": combinaison.value,
                "maxCombinaison": maxCombinaison.value,
                "nomOrganisateur": nomOrganisateur.value,
                "couleurRouge": couleurRouge.value,
                "couleurVert": couleurVert.value,
                "couleurBleu": couleurBleu.value
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
                        aucuneValeurDispo.value = dataObj.aucuneValeurDispo;
                        combinaison.value = dataObj.combinaison;
                        couleurRouge.value = dataObj.couleurRouge;
                        couleurVert.value = dataObj.couleurVert;
                        couleurBleu.value = dataObj.couleurBleu;
                        valeurSmall.innerHTML = dataObj.nouvelleCombinaison.valeurSmall;
                        valeurBig.innerHTML = dataObj.nouvelleCombinaison.valeurBig;
                        valeurSmall.style.color = "rgb(" + couleurRouge.value + "," + couleurVert.value + "," + couleurBleu.value + ")";
                        valeurBig.style.color = "rgb(" + couleurRouge.value + "," + couleurVert.value + "," + couleurBleu.value + ")";

                        // Déplacement de la logique si nous avons atteint la dernière valeur
                        if (aucuneValeurDispo.value === "true") {
                            changerMise.setAttribute("class", "disabled");
                            changerMise.setAttribute("disabled", "disabled");
                            resetTemps.setAttribute("class", "disabled");
                            resetTemps.setAttribute("disabled", "disabled");
                        }

                    } else if (dataReturn["erreur"]) {
                        let dataErr = JSON.parse(dataReturn["erreur"]);
                        if (typeLangue.value === "english") {
                            // TODO : refaire les situation
                            if (dataErr.situation1) {
                                alert("Crucial information is missing to retrieve information from the DB.");
                            } else if (dataErr.situation2) {
                                alert("This file must be called via an AJAX call.");
                            }
                        } else if (typeLangue.value === "francais") {
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
            resetTemps.setAttribute("class", "disabled");
            resetTemps.setAttribute("disabled", "disabled");
        }
    }
}

function playMusique() {
    if (min === 0 && sec < 7) {
        alertSound.play();
    }
}

function reprendreTemps() {
    reprendTimer.addEventListener('click', function () {
        clearTimeout(comptage); // arrête le coulage du temps
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");
        stopTimer.setAttribute("class", "");
        stopTimer.removeAttribute("disabled");
        temps15min.setAttribute("class", "");
        temps15min.removeAttribute("disabled");
        temps30min.setAttribute("class", "");
        temps30min.removeAttribute("disabled");

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
    stopTimer.addEventListener('click', function () {
        clearTimeout(comptage); // arrête le coulage du temps
        reprendTimer.setAttribute("class", "");
        reprendTimer.removeAttribute("disabled");
        stopTimer.setAttribute("class", "disabled");
        stopTimer.setAttribute("disabled", "disabled");
        temps15min.setAttribute("class", "disabled");
        temps15min.setAttribute("disabled", "disabled");
        temps30min.setAttribute("class", "disabled");
        temps30min.setAttribute("disabled", "disabled");
    });
}

function resetTemp() {
    resetTemps.addEventListener('click', function () {
        clearTimeout(comptage); // arrête le coulage du temps
        chrono = 0;
        min = "00";
        sec = "00";
        minutes.style.color = "white";
        secondes.style.color = "white";
        minutes.innerHTML = min.toString();
        secondes.innerHTML = sec.toString();
        if (typeLangue.value === "francais") {
            periode.innerHTML = "Sélectionner votre Période";
        } else if (typeLangue.value === "english") {
            periode.innerHTML = "Select your Period";
        }

        temps15min.setAttribute("class", "");
        temps15min.removeAttribute("disabled");
        temps30min.setAttribute("class", "");
        temps30min.removeAttribute("disabled");
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");
        stopTimer.setAttribute("class", "disabled");
        stopTimer.setAttribute("disabled", "disabled");
        resetTemps.setAttribute("class", "disabled");
        resetTemps.setAttribute("disabled", "disabled");

        if (nomOrganisateur.value !== "" && aucuneValeurDispo.value === "false") {
            changerMise.setAttribute("class", "");
            changerMise.removeAttribute("disabled");
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
            reprendTimer.style.fontSize = "20px";
            tempsPeriode.style.fontSize = "30px";
            typeMises.style.fontSize = "30px";
            btnReturn.style.fontSize = "20px";
            btnChoisir.style.fontSize = "20px";
        } else if (device >= 1.1) {
            reprendTimer.style.fontSize = "25px";
            tempsPeriode.style.fontSize = "35px";
            typeMises.style.fontSize = "35px";
            btnReturn.style.fontSize = "25px";
            btnChoisir.style.fontSize = "25px";
        } else if (device >= 1) {
            reprendTimer.style.fontSize = "30px";
            tempsPeriode.style.fontSize = "40px";
            typeMises.style.fontSize = "40px";
            btnReturn.style.fontSize = "30px";
            btnChoisir.style.fontSize = "30px";
        }
    } else if (largeur > 1300) {
        if (device > 1.4) {
            reprendTimer.style.fontSize = "20px";
            tempsPeriode.style.fontSize = "30px";
            typeMises.style.fontSize = "30px";
            btnReturn.style.fontSize = "20px";
            btnChoisir.style.fontSize = "20px";
        } else if (device > 1.2) {
            reprendTimer.style.fontSize = "30px";
            tempsPeriode.style.fontSize = "35px";
            typeMises.style.fontSize = "35px";
            btnReturn.style.fontSize = "30px";
            btnChoisir.style.fontSize = "30px";
        } else if (device > 1.1) {
            reprendTimer.style.fontSize = "35px";
            tempsPeriode.style.fontSize = "40px";
            typeMises.style.fontSize = "40px";
            btnReturn.style.fontSize = "35px";
            btnChoisir.style.fontSize = "35px";
        }
    }
    modificationSizeValeurs(valeurBig.innerHTML);
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
        valeurSmall.style.fontSize = `${fontSizes[index]}px`;
        valeurBig.style.fontSize = `${fontSizes[index]}px`;
    }
}



function miseEnMarcheDuTimer() {
    // clearTimeout -> cancels a timeout previously established by calling setTimeout()
    clearTimeout(comptage);
    reprendTimer.setAttribute("class", "disabled");
    reprendTimer.setAttribute("disabled", "disabled");
    stopTimer.setAttribute("class", "");
    stopTimer.removeAttribute("disabled");
    resetTemps.setAttribute("class", "");
    resetTemps.removeAttribute("disabled");
    changerMise.setAttribute("class", "disabled");
    changerMise.setAttribute("disabled", "disabled");
}

document.addEventListener('DOMContentLoaded', function () {
    resizeText(); // TODO : Comprendre pourquoi ca marche pas
    timer15Min();
    timer30Min();
    stop();
    reprendreTemps();
    resetTemp();
});