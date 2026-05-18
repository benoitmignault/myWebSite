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

    useEffect(() => {
        // Récupérer les données des événements à venir depuis l'API
        fetch(
            //  TODO: Remplacer l'URL par celle de votre API une fois que vous l'avez mise en place       
            "https://localhost/api/eventslist.php"        )
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
                events.map((event) => (
                    <div key={event.event_id}  className="event-card">
                        <div className="event-clickable" 
                            onClick={() => setOpenEvent(
                                // Si on clique sur un événement déjà ouvert, on le ferme, sinon on ouvre le nouvel événement
                                openEvent === event.event_id ? null : event.event_id
                            )}  
                        >
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
                            openEvent === event.event_id && (
                                <div className="event-results">
                                    {
                                        event.results.length > 0
                                            ? (
                                                <table className="results-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Position</th>
                                                            <th>Joueur</th>
                                                            <th>Score Brut</th>
                                                            <th>Score Net</th>
                                                            <th>Points</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {
                                                            event.results.map((result, index) => (
                                                                <tr key={index}>
                                                                    <td>{result.position}</td>
                                                                    <td className="text-name">{result.firstname}{" "}{result.lastname}</td>
                                                                    <td>{result.gross_score}</td>
                                                                    <td className={ result.net_score < 0 ? "negative-score" : "" }>
                                                                        {result.net_score}
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