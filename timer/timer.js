const doubleValeur = document.querySelector('#double');
const timerReprend = document.querySelector('.resizeText');
const temps_periode = document.querySelector('.container .timer .tableauDuTemps .temps div .resizeText');
const type_mises = document.querySelector('.container .timer .tableauDesMises .lesMises div .resizeText');
const btn_return = document.querySelector('.boutonRetour .retour form .resizeText');
const btn_choisir = document.querySelector('.container .tableau_bord form .choix .bouton');
const resetValeur = document.querySelector('#reset');
const typeLangue = document.querySelector('#typeLangue');
const temps15min = document.querySelector('#timer15');
const temps30min = document.querySelector('#timer30');
const stopTimer = document.querySelector('#timerStop');
const reprendTimer = document.querySelector('#timerReprend');
const resetTemps = document.querySelector('#ResetTemps');
const valeurSmall = document.querySelector('#valeurSmall');
const valeurBig = document.querySelector('#valeurBig');
const minutes = document.querySelector('.chiffreMin p');
const secondes = document.querySelector('.chiffreSec p');
const periode = document.querySelector('.periode p');
const alertSound = document.querySelector('#alertSound');
const combinaison = document.querySelector('.combinaison');
const organisateur = document.querySelector('#choixOrganisateur');
const color_red = document.querySelector('#number_Red');
const color_green = document.querySelector('#number_Green');
const color_blue = document.querySelector('#number_Blue');
const user = document.querySelector('#choixOrganisateur');
const trop_valeur = document.querySelector('#trop_valeur');

var comptage = 0; // la variable un genre de compteur de temps 
var min = 0;
var sec = 0;
var chrono = 0; // pour savoir quel compteur est déclanché...
function modificationSizeValeurs(big){
    var bigNumber = parseInt(big);
    var largeur = window.innerWidth;
    if (largeur < 768){
        if (bigNumber < 1000){
            valeurSmall.style.fontSize = "64px";
            valeurBig.style.fontSize = "64px";
        } else if (bigNumber >= 1000 && bigNumber < 10000){        
            valeurSmall.style.fontSize = "56px";
            valeurBig.style.fontSize = "56px";
        } else if (bigNumber >= 10000 && bigNumber < 100000){
            valeurSmall.style.fontSize = "48px";
            valeurBig.style.fontSize = "48px";
        } else if (bigNumber >= 100000){
            valeurSmall.style.fontSize = "40px";
            valeurBig.style.fontSize = "40px";
        }         
    }
}

function reset(){    
    resetValeur.addEventListener('click', function (evt){
        doubleValeur.setAttribute("class", ""); 
        doubleValeur.removeAttribute("disabled");
        resetValeur.setAttribute("class", "disabled");
        resetValeur.setAttribute("disabled", "disabled");
        valeurSmall.style.fontSize = "64px";
        valeurBig.style.fontSize = "64px";
    }); 
}

function timer15Min(){
    temps15min.addEventListener('click', function (evt){            
        clearTimeout(comptage);
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");         
        stopTimer.setAttribute("class", "");
        stopTimer.removeAttribute("disabled");
        resetTemps.setAttribute("class", "");
        resetTemps.removeAttribute("disabled");
        doubleValeur.setAttribute("class", "disabled");
        doubleValeur.setAttribute("disabled", "disabled");
        min = 15;
        sec = 0;
        chrono = 15;
        minutes.style.color = "green";
        secondes.style.color = "green";
        minutes.innerHTML = min.toString();
        secondes.innerHTML = sec.toString();
        // modifier la valeur en fonction de la langue choisie par l'usagé de la page
        if (typeLangue.value === "francais"){
            periode.innerHTML = "Vous avez choisi la période : 15 minutes !";
        } else if (typeLangue.value === "english"){
            periode.innerHTML = "You have chosen the period: 15 minutes!";
        }
        comptage = setTimeout(starting15, 1000);
    });    
}

function starting15(){  
    if (sec === 0){  
        if (min === 0){ 
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

    if (min >= 10){
        minutes.style.color = "green";
        secondes.style.color = "green";
    } else if (min < 10 && min >= 5) {
        minutes.style.color = "orange";
        secondes.style.color = "orange";
    } else if (min < 5) {
        minutes.style.color = "red";
        secondes.style.color = "red";
    }
    // On affiche la nouvelle valeur du temps à l'écran avec la couleur en fonction des minutes
    minutes.innerHTML = min.toString();
    secondes.innerHTML = sec.toString();        
    comptage = setTimeout(starting15, 1000);
} 

function timer30Min(){
    temps30min.addEventListener('click', function (evt){
        clearTimeout(comptage); 
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");         
        stopTimer.setAttribute("class", "");
        stopTimer.removeAttribute("disabled");
        resetTemps.setAttribute("class", "");
        resetTemps.removeAttribute("disabled");
        doubleValeur.setAttribute("class", "disabled");
        doubleValeur.setAttribute("disabled", "disabled");
        min = 30;
        sec = 0;
        chrono = 30;
        minutes.style.color = "green";
        secondes.style.color = "green";
        minutes.innerHTML = min.toString();
        secondes.innerHTML = sec.toString();
        // modifier la valeur en fonction de la langue choisie par l'usagé de la page
        if (typeLangue.value === "francais"){
            periode.innerHTML = "Vous avez choisi la période : 30 minutes !";
        } else if (typeLangue.value === "english"){
            periode.innerHTML = "You have chosen the period: 30 minutes!";
        }
        comptage = setTimeout(starting30, 1000);
    });    
}

function starting30(){    
    if (sec === 0){
        if (min === 0){
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
    if (min >= 15){
        minutes.style.color = "green";
        secondes.style.color = "green";
    } else if (min < 15 && min >= 5) {
        minutes.style.color = "orange";
        secondes.style.color = "orange";
    } else if (min < 5) {
        minutes.style.color = "red";
        secondes.style.color = "red";
    }
    // On affiche la nouvelle valeur du temps à l'écran avec la couleur en fonction des minutes
    minutes.innerHTML = min.toString();
    secondes.innerHTML = sec.toString();
    comptage = setTimeout(starting30, 1000);
}

function callAjax(){
    stopTimer.setAttribute("class", "disabled");
    stopTimer.setAttribute("disabled", "disabled"); 
    if (user.value != ""){
        if (trop_valeur.value == "true"){
            doubleValeur.setAttribute("class", "disabled");
            doubleValeur.setAttribute("disabled", "disabled");
            temps30min.setAttribute("class", "disabled");
            temps30min.setAttribute("disabled", "disabled");  
            temps15min.setAttribute("class", "disabled");  
            temps15min.setAttribute("disabled", "disabled");
            resetTemps.setAttribute("class", "disabled");  
            resetTemps.setAttribute("disabled", "disabled");  
        } else if (trop_valeur.value == "false") {
            doubleValeur.setAttribute("class", "");
            doubleValeur.removeAttribute("disabled");
            resetValeur.setAttribute("class", ""); 
            resetValeur.removeAttribute("disabled");
            var data = {
                "niveau_combinaison": combinaison.value, "nom_orginateur": organisateur.value, 
                "color_red": color_red.value, "color_green": color_green.value, "color_blue": color_blue.value  
            };

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "changerMise/changer.php", 
                data: data,
                success: function(dataReturn) { 
                    // sécurisation du retour d'information
                    if (dataReturn["data"]){
                        var dataObj = JSON.parse(dataReturn["data"]);   
                        trop_valeur.value = dataObj.trop_valeur;
                        combinaison.value = dataObj.combinaison;
                        color_red.value = dataObj.color_red; 
                        color_green.value = dataObj.color_green; 
                        color_blue.value = dataObj.color_blue; 
                        valeurSmall.innerHTML = dataObj.valeurSmall;
                        valeurBig.innerHTML = dataObj.valeurBig;
                        valeurSmall.style.color = "rgb("+color_red.value+","+color_green.value+","+color_blue.value+")";
                        valeurBig.style.color = "rgb("+color_red.value+","+color_green.value+","+color_blue.value+")";

                    } else if (dataReturn["erreur"]){
                        var dataErr = JSON.parse(dataReturn["erreur"]);  
                        if (typeLangue.value == "english"){
                            if (dataErr.situation1){
                                alert("Can not access the BD. Please try again later !");
                            } else if (dataErr.situation2){
                                alert("Missing important information. Validate your information !");
                            } else if (dataErr.situation3){
                                alert("This file must be called via an AJAX call !");
                            }
                        } else if (typeLangue.value == "francais"){
                            if (dataErr.situation1){
                                alert(dataErr.situation1);
                            } else if (dataErr.situation2){
                                alert(dataErr.situation2);
                            } else if (dataErr.situation3){
                                alert(dataErr.situation3);
                            }
                        }
                    }
                }
            });  
        } // Si la derniere valeur est atteinte
    } // Si le user est vide
}

function playMusique(){    
    if (min === 0 && sec < 7 ){        
        alertSound.play();
    }
}

function stop(){
    stopTimer.addEventListener('click', function (evt){
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

function reprendreTemps(){
    reprendTimer.addEventListener('click', function (evt){ 
        clearTimeout(comptage); // arrête le coulage du temps
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");
        stopTimer.setAttribute("class", "");
        stopTimer.removeAttribute("disabled");
        temps15min.setAttribute("class", "");
        temps15min.removeAttribute("disabled");
        temps30min.setAttribute("class", "");
        temps30min.removeAttribute("disabled");

        switch (chrono){
            case 15: comptage = setTimeout(starting15, 1000);break;
            case 30: comptage = setTimeout(starting30, 1000);break;
        }
    });
}

function resetTemp(){
    resetTemps.addEventListener('click', function (evt){ 
        clearTimeout(comptage); // arrête le coulage du temps
        chrono = 0;
        min = "00";
        sec = "00";
        minutes.style.color = "white";
        secondes.style.color = "white";
        minutes.innerHTML = min.toString();
        secondes.innerHTML = sec.toString();
        if (typeLangue.value === "francais"){
            periode.innerHTML = "En attente d'une période de temps...";
        } else if (typeLangue.value === "english"){
            periode.innerHTML = "Waiting for a period of time ...";
        }        
        temps15min.setAttribute("class", "");
        temps15min.removeAttribute("disabled");
        temps30min.setAttribute("class", "");
        temps30min.removeAttribute("disabled");
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");
        stopTimer.setAttribute("class", "disabled");
        stopTimer.setAttribute("disabled", "disabled");
        if (user.value != ""){  
            doubleValeur.setAttribute("class", ""); 
            doubleValeur.removeAttribute("disabled");
        }

    });
}

function resizeText(){
    var device = detectZoom.device();
    var largeur = window.innerWidth;
    if (largeur < 1300){ 
        if (device > 1.2){
            reprendTimer.style.fontSize = "20px";
            temps_periode.style.fontSize = "30px";
            type_mises.style.fontSize = "30px";
            btn_return.style.fontSize = "20px";
            btn_choisir.style.fontSize = "20px";
        } else if (device >= 1.1){
            reprendTimer.style.fontSize = "25px";
            temps_periode.style.fontSize = "35px";
            type_mises.style.fontSize = "35px";
            btn_return.style.fontSize = "25px";
            btn_choisir.style.fontSize = "25px";
        } else if (device >= 1){
            reprendTimer.style.fontSize = "30px";
            temps_periode.style.fontSize = "40px";
            type_mises.style.fontSize = "40px";
            btn_return.style.fontSize = "30px";
            btn_choisir.style.fontSize = "30px";
        }
    } else if (largeur > 1600){ 
        if (device > 1.6){
            reprendTimer.style.fontSize = "20px";
            temps_periode.style.fontSize = "30px";
            type_mises.style.fontSize = "30px";
            btn_return.style.fontSize = "20px";
            btn_choisir.style.fontSize = "20px";
        } else if (device > 1.2){
            reprendTimer.style.fontSize = "30px";
            temps_periode.style.fontSize = "35px";
            type_mises.style.fontSize = "35px";
            btn_return.style.fontSize = "30px";
            btn_choisir.style.fontSize = "30px";
        } else if (device > 1.1){
            reprendTimer.style.fontSize = "35px";
            temps_periode.style.fontSize = "40px";
            type_mises.style.fontSize = "40px";
            btn_return.style.fontSize = "35px";
            btn_choisir.style.fontSize = "35px";
        }
    }
    // 1.1041666269302368 -> 100%
    // 1.2145832777023315 -> 110%
    // 1.3802082538604736 -> 125%
} 

document.addEventListener('DOMContentLoaded', function(event) {   
    modificationSizeValeurs(valeurBig.innerHTML);
    resizeText();
    timer15Min();
    timer30Min();
    stop();
    reprendreTemps();
    resetTemp();       
});