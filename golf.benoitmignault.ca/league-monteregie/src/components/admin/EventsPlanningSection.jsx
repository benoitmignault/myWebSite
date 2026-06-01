import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { API_BASE_URL } from "../../config";
import "./admin.css";


/**
 * Composant de gestion pour le prochain évenement qui sera en cours à l'aide de l'ajout de joueurs, ce qui va rendre l'évenement ouvert et en cours, 
 * et ensuite la possibilité de le fermer pour que les résultats soient pris en compte pour le classement de la ligue
 * 
 * L'évenement va se fermer automatiquement après la saisie des résultats du dernier joueur prévu à cette evenement
 * 
 * @description 
 * Affiche un formulaire pour ajouter des joueurs à l'évenement en cours, et une liste des joueurs déjà inscrits à cet évenement
 * 
 * @returns 
 */
function EventsPlanningSection() {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // 





    // État pour empêcher de faire l'ajout plusieurs fois de suite d'un joueur à un évenement 
    // si on clique sur le bouton, en attendant la réponse de l'API
    const [loading, setLoading] = useState(false);


    
    const handleReset = () => {

    }



    return (
        <div className="admin-section-card">
            <h2>Section pour planifier un évenement en cours</h2>
            <div className="admin-row">
                <div className="admin-form-group">
                    <label className="admin-label">Nom de l'événement</label>
                    <div>Semaine 4</div>
                </div>
                <div className="admin-form-group">
                    <label className="admin-label">Club de golf</label>
                    <div>Club de golf Napierville</div>
                </div>       
                <div className="admin-form-group">
                    <label className="admin-label">Date</label>
                    <div>2026-05-31</div>
                </div>
            </div>
            <div className="admin-row">
                <div className="admin-form-group">
                    <label className="admin-label">Ajouter un participant</label>
                    <select className="admin-input"></select>
                </div>
                <div className="admin-form-group">
                    <label className="admin-label">Équipe</label>
                    <input type="number" min="1" max="10" className="admin-input"/>
                </div>
            </div>
            <div className="admin-row">

                <div className="admin-actions">
                    <button className="admin-button" type="submit" disabled={loading}>
                        Ajouter
                    </button>
                    <button
                        className="admin-button admin-button-secondary"
                        type="button" onClick={handleReset}>
                        Effacer
                    </button>
                </div>
            </div>            

            <div className="teams-container">
                <h2>Équipes du tournoi</h2>
                <div className="team-card">

                    <h3>Équipe 1</h3>
                    ...
                </div>
                <div className="team-card">
                    <h3>Équipe 2</h3>
                    ...
                </div>
            </div>            
        </div>
    );
}

export default EventsPlanningSection;