import { LuCalendarRange } from "react-icons/lu";

// Définition des options de période disponibles pour le menu déroulant
const PERIOD_OPTIONS = [
    { value: "all", label: "Depuis le début" },
    { value: "today", label: "Aujourd'hui" },
    { value: "7", label: "7 derniers jours" },
    { value: "30", label: "30 derniers jours" },
    { value: "90", label: "90 derniers jours" }
];

/**
 * Composant pour permettre à l'utilisateur de sélectionner une période pour filtrer les données.
 * 
 * @description 
 * Ce composant affiche un menu déroulant permettant à l'utilisateur de choisir une période spécifique 
 * pour filtrer les données affichées dans le tableau des statistiques.
 * 
 * @param {string} selectedPeriod - La période actuellement sélectionnée.
 * @param {function} setSelectedPeriod - Fonction pour mettre à jour la période sélectionnée.
 */
function PeriodSelector({ selectedPeriod, setSelectedPeriod}) {

    return (
        <div className="analytics-period">
            <LuCalendarRange />
            <label htmlFor="period">Période</label>

            <select
                id="period"
                value={selectedPeriod}
                onChange={(e) => setSelectedPeriod (e.target.value)}
            >
                {PERIOD_OPTIONS.map(period => (
                    <option key={period.value} value={period.value}>{period.label}</option>
                ))}
            </select>
        </div>
    );
};

export default PeriodSelector;
