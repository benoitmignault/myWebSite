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

    return (
        <div className="admin-section-card">
            <h2>Section pour planifier un évenement en cours</h2>
            <p className="admin-section-description">
                Ajouter, modifier ou supprimer des événements de la ligue.
            </p>
            <button className="admin-button">
                Gérer les événements
            </button>
        </div>
    );
}

export default EventsPlanningSection;