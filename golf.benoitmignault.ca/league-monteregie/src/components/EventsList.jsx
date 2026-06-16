import { useEffect, useState } from "react";
import { FaMedal } from "react-icons/fa";
import { FaTrophy } from "react-icons/fa";
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

                // RÉorganisation pour convertir des string en number pour is_closed et is_open,
                // pour éviter les problèmes de comparaison dans le rendu conditionnel du composant
                data.events.forEach(event => {
                    event.is_closed = Number(event.is_closed);
                    event.is_open = Number(event.is_open);
                });

                setEvents(data.events);
            } else {

                console.error("Erreur lors du chargement des événements :", data.message);
            }

        } catch (err) {

            console.error(err);
        }
    };

    // Fonction pour aller récupérer les résultats détaillés d'un événement sélectionné depuis l'API qui est closed
    const loadEventResults = async (eventId) => {

        // On doit vider l'état de la liste des équipes et des joueurs avant d'aller plus loin pour éviter d'afficher 
        // les équipes d'un événement précédent pendant le chargement des résultats d'un événement closed
        setTeamsEvent([]);
        setEventMessage("");

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
        }
    };

    // Fonction pour aller récupérer les équipes et les joueurs associés d'un événement sélectionné depuis l'API qui est open
    const loadEventTeams = async (eventId) => {

        // On doit vider l'état du résultat d'un event précédant avant d'aller plus loin pour éviter d'afficher 
        // les résultats d'un autre event au lieu de la liste des équipes d'un événement
        setEventResults([]);
        setEventMessage("");

        try {
            // Récupérer la liste des équipes et des joueurs associés à cet évenement en cours, avec une requête à l'API get-teams-event.php
            const response = await fetch(`${API_BASE_URL}/get-teams-event.php?id=${eventId}`);

            // On récuipère la réponse de l'API et on la convertit en JSON pour pouvoir l'utiliser dans notre composant 
            const data = await response.json();

            if (data.success) {

                // Stocker la liste des équipes et des joueurs associés à cet évenement en cours dans l'état teamsEvent
                setTeamsEvent(data.teams);

                // Ne pas faire de console.log d'un etat qu'on vient de mettre à jour, 
                // car l'état n'est pas mis à jour immédiatement, mais seulement au prochain rendu du composant, 
                // donc le console.log afficherait l'état avant la mise à jour, ce qui peut prêter à confusion
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
            setTeamsEvent([]); // Fermer les équipes si on reclique sur le même événement
            setEventMessage(""); // Réinitialiser le message d'événement
            return;
        }

        // Un genre de sinon, on ouvre l'event et on va chercher les détails de cet event pour les afficher
        setOpenEvent(event.id);

        // IMPORTANT qu'on va utiliser en bas pour faire afficher un message de chargement pendant qu'on attend la réponse de l'API pour les détails du joueur
        setLoadingEventHistory(true);

        // 2026-06-03, refactoring pour inclure la notion de is_open & is_closed 
        // dans la logique d'affichage des résultats d'un événement

        // Si l'évenement est fermé, on va chercher les résultats du tournoi, sinon si l'événement est ouvert,
        // on va chercher les équipes du tournoi, sinon on affiche un message que les équipes ne sont pas encore disponibles
        if (event.is_closed === 1) {

            await loadEventResults(event.id);
        
        } else if (event.is_open === 1) {

            await loadEventTeams(event.id);
        } else {

            // On doit reset les états des résultats et liste des équipes pour éviter d'afficher les résultats ou les équipes d'un autre événement qui serait encore ouvert ou déjà fermé, et on affiche un message que les équipes ne sont pas encore disponibles pour cet événement
            setEventResults([]);
            setTeamsEvent([]);
            
            // Événement non préparé
            setEventMessage("Les équipes ne sont pas encore disponibles.");            
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

        // Une fois que la requête est terminée (qu'elle ait réussi ou échoué), on arrête d'afficher le message de chargement
        setLoadingEventHistory(false);
        return;
    }

    // Fonction pour gérer la position et afficher une icône de médaille pour les 3 premiers joueurs du classement général
    const getPositionDisplay = (position) => {
        switch (position) {
            case 1:
            return <FaTrophy className="medal gold" />;
            case 2:
            return <FaTrophy className="medal silver" />;
            case 3:
            return <FaTrophy className="medal bronze" />;
            default:
            return position;
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
                                    loadingEventHistory ? (
                                            <p className="upcoming-event">Chargement des résultats...</p>

                                    ) : eventResults.length > 0 ? (
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
                                                            <td>{getPositionDisplay(result.position)}</td>
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

                                    ) : teamsEvent.length > 0 ? (
                                        <div className="teams-container">
                                            <h2>Les équipes de l'évenement :</h2>
                                            <div className="subtitle-container">
                                                <span className="subtitle-info-teams">
                                                    (Nombre entre () après Équipe = joueurs inscrits dans l'équipe)
                                                </span>
                                                <span className="subtitle-info-teams">
                                                    (Nombre entre () après un joueur = handicap arrondi du joueur)
                                                </span>
                                            </div>                                            
                                            {teamsEvent.map(team => (
                                                <div key={team.team_id} className="team-card">
                                                    <h3>Équipe #{team.team_id} ({team.players.length})</h3>
                                                    <ul>
                                                        {team.players.map((player, index) => (
                                                            <li key={index}>{player.firstname} {player.lastname} ({player.handicap_rounded})</li>
                                                        ))}
                                                    </ul>
                                                </div>
                                            ))}
                                        </div>
                                    
                                    ) : (
                                        <p className="upcoming-event">{eventMessage || "Événement à venir..."}</p>
                                    )}                              
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