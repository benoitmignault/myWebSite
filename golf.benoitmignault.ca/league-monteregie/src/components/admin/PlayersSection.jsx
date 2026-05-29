// Sous composant de la page d'administration qui permet d'en ajouter de nouveaux players

import "./admin.css";

function PlayersSection() {

    return (
        <div className="admin-section-card">

            <h2>Gestion des Joueurs</h2>

            <p className="admin-section-description">
                Ajouter et gérer les joueurs de la ligue.
            </p>

            <button className="admin-button">
                Ajouter un joueur
            </button>

        </div>
    );
}

export default PlayersSection;