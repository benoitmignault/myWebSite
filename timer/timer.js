const doubleValeur = document.querySelector('#double');
const doubleValeurManuel = document.querySelector('#changeType');
const resetValeur = document.querySelector('#reset');
const typeLangue = document.querySelector('#typeLanguage');
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

// Les différentes variables qui seront utilisées pour le temps et les valeurs des mises
var coloration = [255,255,255];
var comptage = 0; // la variable un genre de compteur de temps 
var min = 0;
var sec = 0;
var chrono = 0; // pour savoir quel compteur est déclanché...

function double(){      
    doubleValeur.addEventListener('click', function (evt){
        // si le bouton reset n'est pas valide, alors on le rend disponible et en plus on fait reset de la valeur de la couleur à rendre plus foncé avec ce doublage !
        if (resetValeur.getAttribute("class") === "disabled"){
            resetValeur.setAttribute("class", ""); 
            resetValeur.removeAttribute("disabled");
            coloration = [255,255,255];            
        }         
        doublerMontant(); // une fct pour doubler les mises small et big blinds
        darker(); // une fct pour rendre les valeurs des mises plus foncées        
    });    
}

function doublerMontant(){
    // Vérification si le small et big sont d'une certaine valeur car règle maison pour simplifier le comptage des jetons.
    var small = parseInt(valeurSmall.innerHTML);
    var big = parseInt(valeurBig.innerHTML);
    if (small === 200 && big === 400){
        small = 500;
        big = 1000;
    } else {
        small = small * 2;
        big = big * 2; 
    }
    modificationSizeValeurs(small,big);
    // nouvelle valeur des mises à afficher
    valeurSmall.innerHTML = small.toString();
    valeurBig.innerHTML = big.toString();
}

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

function augmentManuellement(){
    doubleValeurManuel.addEventListener('click', function (evt){
        var champVide = false;
        var champInval = false;
        var small;
        var big;

        // validation sur la champ de la petite mise
        if (changeSmall.value === ""){
            champVide = true;
            if (typeLangue.value === "francais"){
                alert("Attention ! La valeur de la petite mise ne doit pas être vide.");
            } else if (typeLangue.value === "english"){
                alert("Warning ! The value of the small bet must not be empty.");
            }            
        } else {  
            small = parseInt(changeSmall.value);
            if (Number.isInteger(small)){  
                if (small < 0){
                    champInval = true;
                    if (typeLangue.value === "francais"){
                        alert("Attention ! La valeur de la petite mise doit être positive.");
                    } else if (typeLangue.value === "english"){
                        alert("Warning ! The value of the small bet must be positive.");
                    }
                }                 
            } else {
                champInval = true;
                if (typeLangue.value === "francais"){
                    alert("Attention ! La valeur de la petite mise doit être numérique.");
                } else if (typeLangue.value === "english"){
                    alert("Warning ! The value of the small bet must be numeric.");
                }
            }            
        }          

        // validation sur le champ de la grosse mise
        if (changeBig.value === ""){
            champVide = true;
            if (typeLangue.value === "francais"){
                alert("Attention ! La valeur de la grosse mise ne doit pas être vide.");
            } else if (typeLangue.value === "english"){
                alert("Warning ! The value of the big bet must not be empty.");
            }            
        } else {
            big = parseInt(changeBig.value);  
            if (Number.isInteger(big)){               
                if (big < 0){
                    champInval = true;
                    if (typeLangue.value === "francais"){
                        alert("Attention ! La valeur de la grosse mise doit être positive.");
                    } else if (typeLangue.value === "english"){
                        alert("Warning ! The value of the big bet must be positive.");
                    }
                } 
            } else {
                champInval = true;
                if (typeLangue.value === "francais"){
                    alert("Attention ! La valeur de la grosse mise doit être numérique.");
                } else if (typeLangue.value === "english"){
                    alert("Warning ! The value of the big bet must be positive.");
                }
            }
        }        
        // Si nous avons les deux indicateurs à false, on procède
        if (!champVide && !champInval){
            if (resetValeur.getAttribute("class") === "disabled"){
                resetValeur.setAttribute("class", ""); 
                resetValeur.removeAttribute("disabled");
                coloration = [255,255,255];
            }
            darker();
            valeurSmall.innerHTML = small.toString();
            valeurBig.innerHTML = big.toString();
            modificationSizeValeurs(small,big);
            document.getElementById('newManuelle').checked = true;
        }
        changeSmall.value = "";
        changeBig.value = "";
    });
}

function darker(){
    if (coloration[1] > 0 && (coloration[1] - 25 >= 0) ){
        coloration[1] = coloration[1] - 25;
        coloration[2] = coloration[2] - 25;
    } else if (coloration[0] > 0 && (coloration[0] - 25 >= 0) )  {        
        coloration[0] = coloration[0] - 25;
        coloration[1] = 0;
        coloration[2] = 0;        
    } else {
        coloration[0] = coloration[1] = coloration[2] = 0;
    }
    // après avoir modifier la couleur du tableau, on affiche la nouvelle couleur
    valeurSmall.style.color =       "rgb("+coloration[0]+","+coloration[1]+","+coloration[2]+")";
    valeurBig.style.color = 
        "rgb("+coloration[0]+","+coloration[1]+","+coloration[2]+")";
}

function reset(){    
    resetValeur.addEventListener('click', function (evt){
        var small = 25;
        var big = 50;
        valeurSmall.innerHTML = small.toString();
        valeurBig.innerHTML = big.toString();
        doubleValeur.setAttribute("class", ""); 
        doubleValeur.removeAttribute("disabled");
        resetValeur.setAttribute("class", "disabled");
        resetValeur.setAttribute("disabled", "disabled");
        valeurSmall.style.color = "rgb(255,255,255)";
        valeurBig.style.color = "rgb(255,255,255)"; 
        valeurSmall.style.fontSize = "64px";
        valeurBig.style.fontSize = "64px";
        document.getElementById('newAuto').checked = true;
    }); 
}

function timer15Min(){
    // La partie de la fct qui sera exécuter avec le cliquage du bouton
    temps15min.addEventListener('click', function (evt){            
        clearTimeout(comptage);
        if (stopTimer.getAttribute("class") === "disabled"){
            stopTimer.setAttribute("class", "");
            stopTimer.removeAttribute("disabled");
        }
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");
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
    // refaire la mécanique avec les minutes avant les secondes pour le 15 et 30 minutes
    if (sec === 0){  
        if (min === 0){
            var typeAction = document.querySelector('input[name="new"]:checked').value;
            if (typeAction === "auto"){
                doublerMontant(); // Au moment d'avoir épuisé le temps, on double les mises et le compte à rebourse se relance tous seul            
                darker();
                resetValeur.setAttribute("class", "");
                resetValeur.removeAttribute("disabled");
            } else if (typeAction === "manuelle"){
                // Ce return permet de briser la chaine du temps
                return;
            }            
            min = 15;
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

function playMusique(){    
    if (min === 0 && sec < 7 ){        
        alertSound.play();
    }
}


function timer30Min(){
    // La partie de la fct qui sera exécuter avec le cliquage du bouton
    temps30min.addEventListener('click', function (evt){
        clearTimeout(comptage); 
        reprendTimer.setAttribute("class", "disabled");
        reprendTimer.setAttribute("disabled", "disabled");
        if (stopTimer.getAttribute("class") === "disabled"){    
            stopTimer.setAttribute("class", "");
            stopTimer.removeAttribute("disabled");
        }
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
    // refaire la mécanique avec les minutes avant les secondes pour le 15 et 30 minutes
    if (sec === 0){
        if (min === 0){ 
            var typeAction = document.querySelector('input[name="new"]:checked').value;
            if (typeAction === "auto"){
                doublerMontant(); // Au moment d'avoir épuisé le temps, on double les mises et le compte à rebourse se relance tous seul            
                darker();
                resetValeur.setAttribute("class", "");
                resetValeur.removeAttribute("disabled");
            } else if (typeAction === "manuelle"){
                // Ce return permet de briser la chaine du temps
                return;
            }
            min = 30;            
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
    });
}

// Au moment du loading de la page, là on fait appel au fonction nécessaire au bon fonctionnement de la page....
document.addEventListener('DOMContentLoaded', function(event) {      
    double();
    augmentManuellement();
    reset();    
    timer15Min();
    timer30Min();
    stop();
    reprendreTemps();
    resetTemp();    
});