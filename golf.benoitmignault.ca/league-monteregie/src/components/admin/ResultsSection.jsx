// Sous composant de la page d'administration qui permet d'ajouter de nouveaux résultats
import "./admin.css";

function ResultsSection() {

    return (
        <div className="admin-section-card">

            <h2>Gestion des Résultats</h2>

            <p className="admin-section-description">
                Ajouter les résultats et mettre à jour les classements.
            </p>

            <button className="admin-button">
                Ajouter des résultats
            </button>

        </div>
    );
}

export default ResultsSection;