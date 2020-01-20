const typeClassement = document.getElementsByName('position');
const gain = document.querySelector('#gain');
const killer = document.querySelector('#killer');
const citron = document.querySelector('#citron');

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
                gain.value = "-10";
                citron.value = "";
                break;
        }
    });

    $(citron).change(function () {
        if (this.value == '1') {
            killer.value = "0";
        }
    });

    $(killer).change(function () {
        if (this.value !== '0') {
            citron.value = "0";
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    typePositionSelected();
});
