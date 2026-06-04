import { useEffect, useState } from "react";
import { API_BASE_URL } from "../config";

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

    // État pour stocker les équipes et les joueurs associés
    const [teamsEvent, setTeamsEvent] = useState([]);

    // État pour indiquer si les résultats détaillés du joueur sont en cours de chargement
    const [loadingEventHistory, setLoadingEventHistory] = useState(false);

    // ÉTat pour stocker un message à afficher dans le cas où les équipes d'un événement ne sont pas encore disponibles
    const [eventMessage, setEventMessage] = useState("");



    // Fonction pour aller récupérer la liste des événements passés et à venir depuis l'API 
    // et les stocker dans l'état pour les afficher dans la liste des événements
    const loadEvents = async () => {

        try {
            const response = await fetch(`${API_BASE_URL}/eventslist.php`);

            const data = await response.json();

            if (data.success) {

                // Mettre à jour l'état avec la liste des événements reçue de l'API 
                // pour les afficher dans la liste des événements
                setEvents(data.events);
            } else {

                console.error("Erreur lors du chargement des événements :", data.message);
            }

        } catch (err) {

            console.error(err);
        }
    };


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
        // mais seulement au moment où l'utilisateur clique sur un event pour voir les détails
        fetch(`${API_BASE_URL}/log-action.php`,
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

        // IMPORTANT qu'on va utiliser en bas pour faire afficher un message de chargement pendant qu'on attend la réponse de l'API pour les détails du joueur
        setLoadingEventHistory(true);

        try {
            // Récupérer les résultats détaillés d'un événement depuis l'API mais en mode asynchrone 
            // pour pouvoir attendre la réponse avant de mettre à jour l'état        
            const response = await fetch(`${API_BASE_URL}/event-details.php?id=${eventId}`);

            // Une fois que la réponse est reçue, on la convertit en JSON et on met à jour l'état avec les résultats de l'événement
            const data = await response.json();

            // Mettre à jour l'état avec les résultats de l'événement pour les afficher dans la table des détails de l'événement
            if (data.success) {

                setEventResults(data.results);
            } else {
                console.error("Erreur lors de la récupération des détails de l'événement :", data.message);
            }

        } catch (error) {

            console.error("Erreur lors de la récupération des détails de l'événement :", error);
        } finally {

            // Une fois que la requête est terminée (qu'elle ait réussi ou échoué), on arrête d'afficher le message de chargement
            setLoadingEventHistory(false);
        }
    }


    // Fonction pour aller récupérer les équipes et les joueurs associés d'un événement sélectionné depuis l'API qui est open
    const loadEventTeams = async (eventId) => {

        try {
            // Récupérer la liste des équipes et des joueurs associés à cet évenement en cours, avec une requête à l'API get-teams-event.php
            const response = await fetch(`${API_BASE_URL}/get-teams-event.php?id=${eventId}`);

            // On récuipère la réponse de l'API et on la convertit en JSON pour pouvoir l'utiliser dans notre composant 
            const data = await response.json();

            if (data.success) {

                // Stocker la liste des équipes et des joueurs associés à cet évenement en cours dans l'état teamsEvent
                setTeamsEvent(data.teams);
            } else {

                // Sinon, on récupère le message d'erreur de l'API et on le stocke dans l'état error pour l'afficher à l'administrateur
                setEventMessage(data.message);
            }

        } catch (err) {

            console.error(err);
            setEventMessage("Une erreur est survenue lors du chargement des équipes de l'événement.");
        }        
    }

    // Fonction pour gérer le click sur un event et afficher les résultats
    const handleEventClick = async (event) => {

        // Si on clique sur un événement déjà ouvert, on le ferme, sinon on ouvre le nouvel événement
        if (openEvent === event.id) {
            setOpenEvent(null);
            setEventResults([]); // Fermer les résultats si on reclique sur le même événement
            return;
        }

        // Loguer l'action de click sur un événement pour afficher les détails de l'événement dans la table des logs de l'admin
        fetch(`${API_BASE_URL}/log-action.php`,
            {
                method: "POST",
                headers: {"Content-Type": "application/json"},                
                body: JSON.stringify({
                    action_type: "event_click",
                    target_id: event.id,
                    target_name: "Affichage détails événement"
                })
            }
        );

        // Un genre de sinon, on ouvre l'event et on va chercher les détails de cet event pour les afficher
        setOpenEvent(event.id);

        // IMPORTANT qu'on va utiliser en bas pour faire afficher un message de chargement pendant qu'on attend la réponse de l'API pour les détails du joueur
        setLoadingEventHistory(true);

        // 2026-06-03, refactoring pour inclure la notion de is_open & is_closed 
        // dans la logique d'affichage des résultats d'un événement

        // Si l'évenement est fermé, on va chercher les résultats du tournoi, sinon si l'événement est ouvert,
        // on va chercher les équipes du tournoi, sinon on affiche un message que les équipes ne sont pas encore disponibles
        if (event.is_closed) {

            await loadEventResults(event.id);

        } else if (event.is_open) {

            await loadEventTeams(event.id);
        } else {

            // Événement non préparé
            setEventMessage("Les équipes ne sont pas encore disponibles.");
            return;
        }
    }

    useEffect(() => {

        const initializeData = async () => {

            // Charger la liste des événements au chargement du composant pour les afficher 
            // dans la liste des événements
            await loadEvents();
        };

        initializeData();

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
                        <div className="event-clickable" onClick={() => handleEventClick(event)}>
                            <div className="event-name">
                                ⛳ {event.event_name}
                            </div>
                            <div className="event-details">
                                <a
                                    href={event.golf_course_website}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="golf-course-link"
                                    onClick={ // Empêcher le click sur le lien d'ouvrir les détails de l'événement, 
                                              // on veut seulement ouvrir le lien du parcours de golf   
                                        (e) => e.stopPropagation()}
                                >
                                    📍 {event.golf_course} ↗
                                </a>
                            </div>
                            <div className="event-details">
                                📅 {event.event_date}
                            </div>
                        </div>
                        {
                            openEvent === event.id && (
                                <div className="event-results">
                                    {
                                        // On vérifi d'abord l'état du chargement...
                                        loadingEventHistory
                                            ? (
                                                <p className="upcoming-event">Chargement des résultats...</p>
                                            )
                                        : eventResults.length > 0
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
                                                <p className="upcoming-event">Événement à venir...</p>
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