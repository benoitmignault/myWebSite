const btn = document.querySelector('#francais');
const explain = document.querySelector('#explication');

document.addEventListener('DOMContentLoaded', function(event) {

    btn.addEventListener('click', function(evt) {
        $(explain).load("explication.html");
    });

});