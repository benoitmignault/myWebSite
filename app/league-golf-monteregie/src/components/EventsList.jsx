import { useEffect, useState } from "react";

/**
 * Fonction composant pour afficher la liste des événements de la Ligue de Golf en Montérégie
 * Affiche une liste des événements à venir avec leur nom, date et lieu
 * 
 * Si il y a des résultats disponibles pour un événement passé, 
 * on va pouvoir cliquer sur la ligne pour faire afficher les résultats en dessous de la ligne
 * 
 * @returns 
 */
function EventsList() {

    // État pour stocker les événements à venir
    const [events, setEvents] = useState([]);

    // État pour ouvrir ou fermer les détails d'un événement seulement, à la fois
    const [openEvent, setOpenEvent] = useState(null);

    // État pour stocker les résultats détaillés d'une event sélectionné
    const [eventResults, setEventResults] = useState([]);

    // Fonction pour gérer le click sur un event et afficher les résultats
    const handleEventClick = async (eventId) => {

        // Si on clique sur un événement déjà ouvert, on le ferme, sinon on ouvre le nouvel événement
        if (openEvent === eventId) {
            setOpenEvent(null);
            setEventResults([]); // Fermer les résultats si on reclique sur le même événement
            return;
        }

        // Envoyer une requête à l'API de logging pour enregistrer l'action de sélection d'un event
        // On n'utilise pas de useEffect pour ça parce que ce n'est pas une action qui doit être déclenchée à chaque rendu du composant, 
        // mais seulement au moment où l'utilisateur clique sur un joueur pour voir les détails
        fetch("https://localhost/api/log-action.php",
            {
                method: "POST",
                headers: {"Content-Type": "application/json"},                
                body: JSON.stringify({
                    action_type: "event_click",
                    target_id: eventId,
                    target_name: "Affichage détails événement"
                })
            }
        );

        // Un genre de sinon, on ouvre l'event et on va chercher les détails de cet event pour les afficher
        setOpenEvent(eventId);

        // Récupérer les résultats détaillés du joueur depuis l'API mias en mode asynchrone pour pouvoir attendre la réponse avant de mettre à jour l'état
        //  TODO: Remplacer l'URL par celle de votre API une fois que vous l'avez mise en place  
        // const response = await fetch(`https://api.golf.benoitmignault.ca/event-details.php?id=${playerId}`);
        const response = await fetch(`https://localhost/api/event-details.php?id=${eventId}`);

        // Une fois que la réponse est reçue, on la convertit en JSON et on met à jour l'état avec les résultats de l'événement
        const data = await response.json();

        // Mettre à jour l'état avec les résultats de l'événement pour les afficher dans la table des détails de l'événement
        setEventResults(data);
    }

    useEffect(() => {
        // Récupérer les données des événements à venir depuis l'API
        // TODO: Remplacer l'URL par celle de votre API une fois que vous l'avez mise en place   
        // fetch("https://api.golf.benoitmignault.ca/eventslist.php")
        fetch("https://localhost/api/eventslist.php")
            .then(response => response.json())
            .then(data => {
                setEvents(data);
            });
    }, []);

    return (
        <div>
            <h2>
                Événements 
                <span className="subtitle-info">
                    (cliquer sur un événement pour voir les résultats)
                </span>
            </h2>
            {
                // Afficher la liste des événements à venir en affichant le nom de l'événement, le lieu et la date
                events.map((event) => (
                    <div key={event.id}  className="event-card">
                        <div className="event-clickable" onClick={() => handleEventClick(event.id)}>
                            <div className="event-name">
                                ⛳ {event.event_name}
                            </div>
                            <div className="event-details">
                                📍 {event.golf_course}
                            </div>
                            <div className="event-details">
                                📅 {event.event_date}
                            </div>
                        </div>
                        {
                            openEvent === event.id && (
                                <div className="event-results">
                                    {
                                        eventResults.length > 0
                                            ? (
                                                <table className="results-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Position</th>
                                                            <th>Joueur</th>
                                                            <th>Brut</th>
                                                            <th>Net</th>
                                                            <th>Points</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {   
                                                            // Afficher les résultats de l'événement en affichant la position, le nom du joueur, le score brut, le score net et les points Fedex
                                                            eventResults.map((result, index) => (
                                                                <tr key={index}>
                                                                    <td>{result.position}</td>
                                                                    <td className="text-name">{result.firstname}{" "}{result.lastname}</td>
                                                                    <td>{result.gross_score}</td>
                                                                    <td className={ 
                                                                        Number(result.net_score) < 0 
                                                                            ? "negative-score" 
                                                                            : (
                                                                                Number(result.net_score) === 0
                                                                                    ? "even-score" 
                                                                                    : "" 
                                                                        )}>
                                                                        { 
                                                                            // Afficher "E" pour Even (0)
                                                                            Number(result.net_score) === 0
                                                                                ? "E"
                                                                                // Si le score net est positif, on ajoute un "+" devant pour différencier des scores négatifs
                                                                                : Number(result.net_score) > 0
                                                                                    ? `+${Number(result.net_score)}`
                                                                                    // Sinon, on affiche le score net tel quel (qui sera négatif)
                                                                                    : Number(result.net_score)
                                                                        }
                                                                    </td>
                                                                    <td>{result.fedex_points}</td>
                                                                </tr>
                                                            ))
                                                        }
                                                    </tbody>
                                                </table>
                                            )
                                            : (
                                                <div className="upcoming-event">
                                                    Événement à venir...
                                                </div>
                                            )
                                    }
                                </div>
                            )
                        }
                    </div>
                ))
            }
        </div>
    );
}

export default EventsList;