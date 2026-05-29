// Sous composant de la page d'administration qui permet d'ajouter de nouveaux événements
import "./admin.css";

function EventsSection() {

    return (
        <div className="admin-section-card">

            <h2>Gestion des Événements</h2>

            <p className="admin-section-description">
                Ajouter et gérer les événements de la saison.
            </p>

            <button className="admin-button">
                Ajouter un événement
            </button>

        </div>
    );
}

export default EventsSection;