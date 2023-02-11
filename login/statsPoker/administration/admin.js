const typeClassement = document.getElementsByName('position');
const gain = document.querySelector('#gain');
const killer = document.querySelector('#killer');
const citron = document.querySelector('#citron');
const numTournoi = document.querySelector('#numTournoi');

// https://stackoverflow.com/questions/13152927/how-to-use-radio-on-change-event
function typePositionSelected() {
    $(typeClassement).change(function () {
        switch ($(this).val()) {
            case 'victoire':
                citron.value = "0";
                gain.value = "";
                break;
            case 'fini2e':
                gain.value = "0";
                citron.value = "0";
                break;
            case 'autre':
                gain.value = "-20";
                citron.value = "";
                break;
        }
    });

    $(citron).change(function () {
        if (this.value === '1') {
            killer.value = "0";
            gain.value = "-20";
        } else if (this.value > '1' || this.value < '0') {
            alert("La valeur doit être 1 ou 0 et elle ne peut être négative !");
            this.value = "";
        }
    });

    $(killer).change(function () {
        if (this.value > '0') {
            citron.value = "0";
        } else if (this.value < '0') {
            alert("La valeur doit être supérieur à 0 !");
            this.value = "";
        }
    });

    $(numTournoi).change(function () {
        if (this.value < '1') {
            alert("Le numéro du tournoi ne peut être de valeur 0 ou négative !");
            this.value = "";
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    typePositionSelected();
});
