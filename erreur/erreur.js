const btnFr = document.querySelector('#francais');
const btnEn = document.querySelector('#english');
const explain = document.querySelector('#explication');

document.addEventListener('DOMContentLoaded', function (event) {

    btnFr.addEventListener('click', function (evt) {        
        $(explain).load("explicationFrench.html");
    });

    btnEn.addEventListener('click', function (evt) {        
        $(explain).load("explicationEnglish.html");
    });
    
});