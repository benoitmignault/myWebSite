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
            </h2>
            {
                events.map((event) => (
                    <div key={event.event_id} className="event-card">
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
                ))
            }
        </div>
    );
}

export default EventsList;