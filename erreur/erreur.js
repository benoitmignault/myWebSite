document.addEventListener('DOMContentLoaded', function (event) {
    const btnFr = document.querySelector('#francais');
    const btnEn = document.querySelector('#english');
    const explain = document.querySelector('#explication');

    btnFr.addEventListener('click', function (evt) {
        explain.innerHTML = "<p>Il est nécessaire d'avoir une valeur à la fin de l'hyperlien pour la variable «?langue»</p>";
        explain.innerHTML += "<p>Exemple de hyperlien : http://benoitmignault.ca/login/login.php?langue=francais</p>";
        explain.innerHTML += "<p>Exemple de hyperlien : http://benoitmignault.ca/calculatrice/calcul.php?langue=francais</p>";
        explain.innerHTML += "<p>Exemple de hyperlien : http://benoitmignault.ca/timer/timer.php?langue=francais</p>";
        explain.innerHTML += "<p>Par contre, pour avoir accès à la page poker.php, nous devons passer par la page login.php?langue=francais</p>";
        explain.innerHTML += "<form method=\"post\" action=\"./erreur.php\"><input class=\"btnErreur\" type=\"submit\" name=\"returnFR\" value=\"Retour à la page d'acceuil\"></form>";
    });

    btnEn.addEventListener('click', function (evt) {
        explain.innerHTML = "<p>It's necessary to put a value at the end of the hyperlink for the variable «?langue»</p>";
        explain.innerHTML += "<p>Exemple of hyperlink : http://benoitmignault.ca/login/login.php?langue=english</p>";
        explain.innerHTML += "<p>Exemple of hyperlink : http://benoitmignault.ca/calculatrice/calcul.php?langue=english</p>";
        explain.innerHTML += "<p>Exemple of hyperlink : http://benoitmignault.ca/timer/timer.php?langue=english</p>";
        explain.innerHTML += "<p>However, for have acces at the page poker.php, you must use the page login.php?langue=english</p>";
        explain.innerHTML += "<form method=\"post\" action=\"./erreur.php\"><input class=\"btnErreur\" type=\"submit\" name=\"returnEN\" value=\"Return at home page\"></form>";
    });
});