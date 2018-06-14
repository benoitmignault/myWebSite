const doubleValeur = document.querySelector('#double');
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
const changeSmall = document.querySelector('#newSmall');
const changeBig = document.querySelector('#newBig');

var comptage = 0; // la variable un genre de compteur de temps 
var min = 0;
var sec = 0;
var chrono = 0; // pour savoir quel compteur est déclanché...
function modificationSizeValeurs(small, big){
    var largeur = window.innerWidth;
    if (largeur < 768){
        if (big >= 1000 && big < 16000){        
            valeurSmall.style.fontSize = "52px";
            valeurBig.style.fontSize = "52px";
        }    
        if (big >= 16000 && big < 64000){
            valeurSmall.style.fontSize = "42px";
            valeurBig.style.fontSize = "42px";
        }        
    }
    if (big >= 64000){
        doubleValeur.setAttribute("class", "disabled");
        doubleValeur.setAttribute("disabled", "disabled");
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
    // La partie de la fct qui sera exécuter avec le cliquage du bouton
    temps15min.addEventListener('click', function (evt){            
        clearTimeout(comptage);
        stopTimer.setAttribute("class", "");
        stopTimer.removeAttribute("disabled");
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");
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
            doubleValeur.setAttribute("class", "");
            doubleValeur.removeAttribute("disabled");
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
    // La partie de la fct qui sera exécuter avec le cliquage du bouton
    temps30min.addEventListener('click', function (evt){
        clearTimeout(comptage); 
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");         
        stopTimer.setAttribute("class", "");
        stopTimer.removeAttribute("disabled");
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
            doubleValeur.setAttribute("class", "");
            doubleValeur.removeAttribute("disabled");
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
        min = 0;
        sec = 0;
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
        doubleValeur.setAttribute("class", ""); 
        doubleValeur.removeAttribute("disabled");
    });
}
document.addEventListener('DOMContentLoaded', function(event) {     
    timer15Min();
    timer30Min();
    stop();
    reprendreTemps();
    resetTemp();    
});