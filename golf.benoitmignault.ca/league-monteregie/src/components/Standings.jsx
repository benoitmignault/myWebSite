import { useEffect, useState } from "react";
import React from "react";
import { FaTrophy } from "react-icons/fa";
import { API_BASE_URL } from "../config";
import { shouldExcludeStats } from "../utils/logging";

/**
 * Composant pour afficher le classement général de la Coupe Fedex
 * 
 * @description Ce composant utilise useEffect pour récupérer les données du classement général 
 * depuis l'API dès que le composant est monté, et stocke ces données dans l'état avec useState. 
 * Il affiche ensuite une table avec les joueurs classés par points, handicap et prénom. 
 * Lorsque l'utilisateur clique sur un joueur, une requête est envoyée à l'API pour récupérer 
 * les résultats détaillés de ce joueur, qui sont ensuite affichés dans une table en dessous 
 * du joueur sélectionné. Un message de chargement est affiché pendant que 
 * les résultats détaillés sont en cours de récupération.
 * 
 * @returns 
 */
function Standings() {

    // État pour stocker les informations sur le classement des joueurs
    const [players, setPlayers] = useState([]);

    // État pour ouvrir ou fermer les détails d'un joueur seulement, à la fois
    const [openPlayer, setOpenPlayer] = useState(null);

    // État pour stocker les résultats détaillés d'un joueur sélectionné
    const [playerResults, setPlayerResults] = useState([]);   
    
    // État pour indiquer si les résultats détaillés du joueur sont en cours de chargement
    const [loadingPlayerHistory, setLoadingPlayerHistory] = useState(false);

    // Fonction pour gérer la position et afficher une icône de médaille pour les 3 premiers joueurs du classement général
    const getPositionDisplay = (position) => {
        switch (position) {
            case 1:
            return <FaTrophy className="trophy gold" />;
            case 2:
            return <FaTrophy className="trophy silver" />;
            case 3:
            return <FaTrophy className="trophy bronze" />;
            default:
            return position;
        }
    }

    // Fonction pour gérer le clic sur un joueur et afficher ses résultats
    const handlePlayerClick = async (playerId) => {

        // Si on clique sur un joueur déjà ouvert, on le ferme, sinon on ouvre le nouvel événement
        if (openPlayer === playerId) {
            setOpenPlayer(null);
            setPlayerResults([]); // Fermer les résultats si on reclique sur le même joueur
            return;
        }

        // Envoyer une requête à l'API de logging pour enregistrer l'action de sélection du joueur
        // On n'utilise pas de useEffect pour ça parce que ce n'est pas une action qui doit être déclenchée à chaque rendu du composant, 
        // mais seulement au moment où l'utilisateur clique sur un joueur pour voir les détails
        fetch(`${API_BASE_URL}/log-action.php`,
            {
                method: "POST",
                headers: {"Content-Type": "application/json"},                
                body: JSON.stringify({
                    action_type: "player_click",
                    target_id: playerId,
                    target_name: "Affichage détails joueur",

                    // Vérifier si la variable locale "exclude_stats" est défini dans le navigateur
                    exclude_stats: shouldExcludeStats()
                })
            }
        );

        // Un genre de sinon, on ouvre le joueur et on va chercher les détails de ce joueur pour les afficher
        setOpenPlayer(playerId);

        // IMPORTANT qu'on va utiliser en bas pour faire afficher un message de chargement pendant qu'on attend la réponse de l'API pour les détails du joueur
        setLoadingPlayerHistory(true);

        try {
            // Récupérer les résultats détaillés du joueur depuis l'API 
            // mais en mode asynchrone pour pouvoir attendre la réponse avant de mettre à jour l'état        
            const response = await fetch(`${API_BASE_URL}/player-details.php?id=${playerId}`);
            
            // Une fois que la réponse est reçue, on la convertit en JSON et on doit mettre à jour l'état avec les résultats du joueur
            const data = await response.json();
            
            // Vérification de la réponse de l'API pour voir si la récupération des détails du joueur a réussi
            if (data.success) {

                // Mettre à jour l'état avec les résultats du joueur pour les afficher dans la section en dessous du joueur
                setPlayerResults(data.playerDetails); 
            } else {

                // Si la récupération des détails du joueur a échoué, 
                // on affiche un message d'erreur dans la console et on réinitialise les résultats du joueur à une liste vide
                console.error("Erreur récupération détails joueur :", data.message);
                setPlayerResults([]);
            }
                      
        } catch (error) {

            console.error("Erreur récupération détails joueur :", error);
            setPlayerResults([]);
        } finally {

            setLoadingPlayerHistory(false);
        }
    }            

    // Utiliser useEffect pour récupérer les données du classement général depuis l'API dès que le composant est monté et les stocker dans l'état avec useState
    // L'API doit retourner une liste de joueurs avec leurs informations de classement, handicap, points, etc. 
    // qu'on peut ensuite afficher dans la table du classement général et utiliser pour afficher les détails du joueur lorsqu'on clique dessus
    useEffect(() => {

        const loadStandings = async () => {

            try {
                const response = await fetch(`${API_BASE_URL}/standings.php`);
                const data = await response.json();
                setPlayers(data.players);

            } catch (err) {

                console.error(err);
            }
        };

        loadStandings();

    }, []);

    // On doit utiliser React.Fractment parce que le .map retourne plusieurs éléments 
    // (la ligne du joueur et la ligne des détails du joueur) et React exige que 
    // les éléments retournés soient regroupés dans un élément parent unique, 
    // mais on ne veut pas ajouter un élément HTML supplémentaire dans le DOM, 
    // donc on utilise React.Fragment qui est un conteneur invisible qui ne rend rien dans le DOM
    return (
        <div>
            <h2>Classement de la Coupe Fedex 
                <span className="subtitle-info">
                    (cliquer sur un joueur pour voir ses résultats)
                </span>
            </h2>
            <table>
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Joueur</th>
                        <th>Score Moyen</th>
                        <th>Handicap</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    {
                        // Affficher le résultat pour le classement général en affichant la position, le nom du joueur, la moyenne de ses scores, son handicap et ses points totaux
                        players.map((player, index) => (
                            <React.Fragment key={player.id}>
                                <tr className="clickable-row" onClick={() => handlePlayerClick(player.id)}>
                                    <td>
                                        {player.previous_position !== null && player.previous_position > index + 1 && (<span className="position-up">▲</span>)}
                                        {player.previous_position !== null && player.previous_position < index + 1 && (<span className="position-down">▼</span>)}
                                        {getPositionDisplay(index + 1)}
                                    </td>
                                    <td>{player.firstname}{" "}{player.lastname}</td>
                                    <td>{player.average_score}</td>
                                    <td>{player.handicap_league}</td>
                                    <td>{player.total_points}</td>
                                </tr>
                                {
                                    openPlayer === player.id && (
                                        <tr className="player-details-row">
                                            {
                                                // On vérifi d'abord l'état du chargement...
                                                loadingPlayerHistory
                                                    ? (
                                                        <td colSpan="5" className="upcoming-event">Chargement des résultats...</td>
                                                    )
                                                : playerResults.length > 0
                                                    ? (
                                                        <td colSpan="5" className="event-details-cell">
                                                            <table className="results-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th className="event-column">Event</th>
                                                                        <th className="text-right">Position</th>
                                                                        <th className="text-right score-column">Score <br />Brut</th>
                                                                        <th className="text-right">Net</th>
                                                                        <th className="text-right">Points</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    {
                                                                        // Afficher les résultats détaillés du joueur en affichant le nom de l'événement, la position, le score brut, le score net et les points Fedex
                                                                        playerResults.map((result, index) => (
                                                                            <tr className="clickable-row" key={index}>
                                                                                <td className="event-column">{result.event_name}</td>
                                                                                <td className="text-right">{getPositionDisplay(result.position)}</td>
                                                                                <td className="text-right">{result.gross_score}</td>
                                                                                <td className={ Number(result.net_score) < 0 
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
                                                                                <td className="text-right">{result.fedex_points}</td>
                                                                            </tr>
                                                                        ))
                                                                    }
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    )
                                                    : (
                                                        <td colSpan="5" className="upcoming-event">Aucun résultat trouvé pour ce joueur.</td>
                                                    )
                                            }                                            
                                        </tr>
                                    )
                                }
                            </React.Fragment>
                        ))
                    }
                </tbody>
            </table>
        </div>
    );
}

export default Standings;