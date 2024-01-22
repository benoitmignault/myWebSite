document.addEventListener('DOMContentLoaded', function (event) {
    webshim.setOptions('forms-ext', {
        replaceUI: 'auto'
    });

    webshims.validityMessages.en = {
        "typeMismatch": {
            "defaultMessage": "Please enter a valid value.",
            "email": "Please enter an email address.",
            "url": "Please enter a URL."
        },
        "badInput": {
            "defaultMessage": "Please enter a valid value.",
            "number": "Please enter a number.",
            "date": "Please enter a date.",
            "time": "Please enter a time.",
            "range": "Invalid input.",
            "month": "Please enter a valid value.",
            "datetime-local": "Please enter a datetime."
        },
        "rangeUnderflow": {
            "defaultMessage": "Value must be greater than or equal to {%min}.",
            "date": "Value must be at or after {%min}.",
            "time": "Value must be at or after {%min}.",
            "datetime-local": "Value must be at or after {%min}.",
            "month": "Value must be at or after {%min}."
        },
        "rangeOverflow": {
            "defaultMessage": "Value must be less than or equal to {%max}.",
            "date": "Value must be at or before {%max}.",
            "time": "Value must be at or before {%max}.",
            "datetime-local": "Value must be at or before {%max}.",
            "month": "Value must be at or before {%max}."
        },
        "stepMismatch": "Invalid input.",
        "tooLong": "Please enter at most {%maxlength} character(s). You entered {%valueLen}.",
        "tooShort": "Please enter at least {%minlength} character(s). You entered {%valueLen}.",
        "patternMismatch": "Invalid input. {%title}",
        "valueMissing": {
            "defaultMessage": "Veuillez remplir ce champ.",
            "checkbox": "Please check this box if you want to proceed.",
            "select": "Please select an option.",
            "radio": "Please select an option."
        }
    };

    webshims.formcfg.en = {
        "numberFormat": {
            ".": ".",
            ",": ","
        },
        "numberSigns": ".",
        "dateSigns": "/",
        "timeSigns": ":. ",
        "dFormat": "/",
        "patterns": {
            "d": "yy/mm/dd"
        },
        "month": {
            "currentText": "Ce mois ci"
        },
        "time": {
            "currentText": "Maintenant"
        },
        "date": {
            "closeText": "Fait",
            "clear": "Effacer",
            "prevText": "Prec",
            "nextText": "Suivant",
            "currentText": "Aujourd'hui",
            "monthNames": [
				"Janvier",
				"Février",
				"Mars",
				"Avril",
				"Mai",
				"Juin",
				"Juillet",
				"Août",
				"Septembre",
				"Octobre",
				"Novembre",
				"Décembre"
			],
            "monthNamesShort": [
				"Jan",
				"Feb",
				"Mar",
				"Avr",
				"Mai",
				"Jun",
				"Jui",
				"Aug",
				"Sep",
				"Oct",
				"Nov",
				"Dec"
			],
            "dayNames": [
				"Dimanche",
				"Lundi",
				"Mardi",
				"Mercredi",
				"Jeudi",
				"Vendredi",
				"Samedi"
			],
            "dayNamesShort": [
				"Dim",
				"Lun",
				"Mar",
				"Mer",
				"Jeu",
				"Ven",
				"Sam"
			],
            "dayNamesMin": [
				"Di",
				"Lu",
				"Ma",
				"Me",
				"Je",
				"Ven",
				"Sa"
			],
            "weekHeader": "Wk",
            "firstDay": 1,
            "isRTL": false,
            "showMonthAfterYear": false,
            "yearSuffix": ""
        }
    };
    webshims.activeLang('en');

    webshim.polyfill('forms forms-ext');
});
