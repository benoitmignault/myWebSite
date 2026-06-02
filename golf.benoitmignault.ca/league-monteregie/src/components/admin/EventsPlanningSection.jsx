import { useState, useEffect } from "react";
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

    // ÉTat pour stocker la réponse de l'API pour le prochain évenement qui sera en cours, avec les détails de cet évenement
    const [nextEvent, setNextEvent] = useState(null);




    // État pour stocker les messages d'erreur en prévision de l'ajout d'un joueur à un événement
    const [error, setError] = useState("");

    // État pour stocker un message de succès lors de l'ajout d'un événement à la ligue
    const [successMessage, setSuccessMessage] = useState("");


    // État pour empêcher de faire l'ajout plusieurs fois de suite d'un joueur à un évenement 
    // si on clique sur le bouton, en attendant la réponse de l'API
    const [loading, setLoading] = useState(false);



    // Fonction pour charger les détails du prochain évenement qui sera en cours, avec une requête à l'API get-next-event.php
    const loadEvent = async () => {

        // Juste au cas ou, on va setter le loading à true pour éviter que l'utilisateur puisse cliquer plusieurs fois sur le bouton d'ajout d'un joueur à un évenement
        setLoading(true);

        try {
            const response = await fetch(`${API_BASE_URL}/admin/get-next-event.php`,
                {
                    credentials: "include"
                }
            );

            // Si la réponse de l'API indique que la session est invalide, 
            // rediriger le gestionnaire vers la page de connexion
            if (response.status === 401) {

                setError("Votre session a expiré, vous allez être redirigé vers la page de connexion.");
                setTimeout(() => {navigate("/league-monteregie/admin");}, 3000);
                return;
            }

            // Sinon, on a un résultat valide du retour de l'API
            const data = await response.json();

            if (data.success) {

                // Stocker les détails du prochain évenement qui sera en cours dans l'état nextEvent
                setEvent(data.event);

            } else {

                // Erreur lors du chargement de l'évenement
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement de l'événement.");

        } finally {

            setLoading(false);
        }        
    };



    
    const handleReset = () => {

    }




    useEffect(() => {

        // Il faut utiliser une notion asynchrone pour charger les données, en raison de l'utilisation 
        const initializeData = async () => {
            await loadEvent();
        };
        
        // Charger tout les éléments dans la section du tournois en gestion en cours
        initializeData();

    }, []);

    return (
        <div className="admin-section-card">
            <h2>Section pour planifier un évenement en cours</h2>
            <p>Voici le prochain événement à préparer :</p>
            <div className="admin-row event-summary-row">
                { event ? (
                    <>
                        <span>🏌️ {event?.event_name}</span>
                        <span>•</span>
                        <span>📍 {event?.golf_course}</span>
                        <span>•</span>
                        <span>📅 {event?.event_date}</span>
                    </>
                    ) : (
                        <p>Aucun événement à préparer.</p>
                    )
                }
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