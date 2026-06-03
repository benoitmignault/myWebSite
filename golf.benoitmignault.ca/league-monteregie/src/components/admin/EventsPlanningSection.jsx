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

    // État pour stocker la réponse de l'API pour le prochain évenement qui sera en cours, avec les détails de cet évenement
    const [event, setEvent] = useState(null);

    // État pour stocker les joueurs disponibles pour l'ajout à l'évenement en cours
    const [availablePlayers, setAvailablePlayers] = useState([]);
    
    // État pour stocker le joueur sélectionné dans le formulaire d'ajout d'un joueur à un évenement
    const [selectedPlayer, setSelectedPlayer] = useState("");

    // État pour stocker les équipes et les joueurs associés
    const [teams, setTeams] = useState([]);

    // État pour stocker le numéro de l'équipe du joueur
    const [team, setTeam] = useState("");

    // États pour gérer les erreurs de validation des champs pour ajouter un joueur à un évenement
    const [selectedPlayerError, setSelectedPlayerError] = useState(false);
    const [teamError, setTeamError] = useState(false);
    
    // État pour empêcher de faire l'ajout plusieurs fois de suite d'un joueur à un évenement 
    // si on clique sur le bouton, en attendant la réponse de l'API
    const [loading, setLoading] = useState(false);

    // État pour stocker les messages d'erreur en prévision de l'ajout d'un joueur à un événement
    const [error, setError] = useState("");

    // État pour stocker un message de succès lors de l'ajout d'un événement à la ligue
    const [successMessage, setSuccessMessage] = useState("");    

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

                // On retourne le résultat pour pouvoir l'utiliser dans la fonction d'initialisation des données du useEffect, 
                // pour ensuite charger la liste des joueurs disponibles pour l'ajout à cet évenement
                return data.event;
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
    
    // Fonction pour charger la liste des joueurs disponibles pour l'ajout à l'évenement en cours
    const loadAvailablePlayers = async (eventId) => {

        try {
            const response = await fetch(`${API_BASE_URL}/admin/get-available-players.php?id=${eventId}`,
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
                setAvailablePlayers(data.players);                
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

    // Fonction pour charger la liste des joueurs inscrits à cet évenement, par groupe d'équipe
    const loadTeamsEvent = async () => {

        try {

            const response = null;

            const data = await response.json();

            setTeams(data.teams);

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement des équipes de l'événement.");
        }
    };

    // Fonction pour ajouter un joueur à un évenement en cours, avec une requête à l'API add-player-event.php
    const handleAddEvent = async () => {

        // Réinitialiser les messages d'erreur avant de commencer le processus d'ajout
        setError("");

        // On commencer par gérer les erreurs de validation côté client 
        // avant même d'envoyer la requête à l'API, pour éviter les appels inutiles à l'API et 
        // améliorer l'expérience utilisateur.

        // TODO: Changer error pour une liste de msg erreur pour pouvoir afficher plusieurs erreurs à la fois, 
        // au lieu de n'afficher que la première erreur rencontrée.

        // Validation du champ de sélection du joueur et de l'équipe, qui sont tous les deux obligatoires
        let hasError = false;

        if (!selectedPlayer) {

            setSelectedPlayerError(true);
            hasError = true;
        }

        if (team === "" || isNaN(team) || team < 1) {

            setTeamError(true);
            hasError = true;
        }

        if (hasError) {

            setError("Veuillez remplir tous les champs obligatoires.");
            return;
        }

        // Si on passe les validations côté client, on peut alors procéder à l'appel de l'API 
        // pour ajouter le joueur à la ligue
        setLoading(true);

        // Trouver le nom du joueur sélectionné pour l'afficher dans le message de succès après l'ajout du joueur à l'évenement
        const player = availablePlayers.find(one_player => one_player.id === parseInt(selectedPlayer));

        // Associer le nom du joueur à une variable pour l'afficher dans le message de succès après l'ajout du joueur à l'évenement
        const playerName = `${player.firstname} ${player.lastname}`;

        try {
            const response = await fetch(`${API_BASE_URL}/admin/add-player-event.php`,
                {
                    method: "POST",
                    credentials: "include",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({event_id: event?.id, player_id: selectedPlayer, team_id: team})
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

            // Vérification de la réponse de l'API pour voir si le joueur a été ajouté avec succès ou s'il y a eu une erreur
            if (data.success) {

                // Affichage d'un message de succès pour informer l'administrateur que le joueur a été ajouté avec succès
                setSuccessMessage(`${playerName} a été ajouté à la ligue.`);

                // Réinitialiser les champs du formulaire d'ajout d'un joueur à un évenement
                setTimeout(() => {handleReset();}, 3000);

                // Recharger la liste des joueurs disponibles pour l'ajout à cet évenement, 
                // pour que le joueur ajouté n'apparaisse plus dans la liste des joueurs disponibles
                await loadAvailablePlayers(event?.id);                         
            } else {

                // Erreur lors de l'ajout du joueur
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors de la tentative d'ajout du joueur.");
        } finally {

            setLoading(false);
        }
    };

    // Fonction pour réinitialiser les champs du formulaire d'ajout d'un joueur à un évenement et les messages d'erreur associés
    const handleReset = () => {

        // Remise à l'état initial des champs du formulaire d'ajout d'un joueur à un évenement
        setSelectedPlayer("");
        setTeam("");
        
        // Remise à l'état initial du trigger pour remettre les bordures dans leur état normal
        setSelectedPlayerError(false); 
        setTeamError(false);

        // Remise à l'état initial du message d'erreur
        setError("");

        // Remise à l'état initial du message de succès
        setSuccessMessage("");
    };

    useEffect(() => {

        // Il faut utiliser une notion asynchrone pour charger les données, en raison de l'utilisation 
        const initializeData = async () => {

            // On doit d'abord charger les informations du prochain évenement qui sera en cours avec un return, très important
            const event = await loadEvent();

            // Ensuite, si on a un évenement qui est en cours, on peut charger la liste des joueurs disponibles pour l'ajout à cet évenement
            if (event) {

                // Car cette fonction dépend de l'id, pas id pas de fonction 
                await loadAvailablePlayers(event.id);
            }
        };
        
        // Charger tout les éléments dans la section du tournois en gestion en cours
        initializeData();

    }, []);

    return (
        <div className="admin-section-card">
            <h2>Section pour planifier un évenement</h2>
            <form onSubmit={(e) => {e.preventDefault(); handleAddEvent();}}>
                <p>Voici le prochain :</p>
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
                        <label className="admin-label">
                            Ajouter un participant
                            <span className="required-field">*</span></label>                    
                        <select className={`admin-input ${selectedPlayerError ? "input-error" : ""}`}
                            value={selectedPlayer}
                            onChange={(e) => {setSelectedPlayer(e.target.value); setSelectedPlayerError(false); setError("");}}                       
                        >
                            <option value="">Sélectionner un joueur</option>
                            {availablePlayers.map(player => (
                                <option key={player.id} value={player.id}>{player.firstname} {player.lastname}</option>
                            ))}
                        </select>
                    </div>
                    <div className="admin-form-group">
                        <label className="admin-label">
                            Équipe
                            <span className="required-field">*</span>
                        </label>
                        <input 
                            className={`admin-input team-input ${teamError ? "input-error" : ""}`}
                            type="number" step="1" min="1" max="20" placeholder="Ex : #1" value={team}
                            onChange={(e) => {setTeam(e.target.value); setTeamError(false); setError("");}}
                        />
                    </div>
                </div>
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
                {error && <p className="admin-error-message">✗ {error}</p>}
                {successMessage && <p className="admin-success-message">✓ {successMessage}</p>}
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
            </form>
        </div>
    );
}

export default EventsPlanningSection;