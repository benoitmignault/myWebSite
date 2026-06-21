/* eslint-disable react-hooks/set-state-in-effect */
import { useEffect, useState } from "react";
import { FaInfoCircle } from "react-icons/fa";
import { API_BASE_URL } from "../../config";


/**
 * Composant pour afficher l'historique détaillé d'un joueur, incluant l'évolution du classement FedEx, des points cumulés et du handicap après chaque événement.
 * 
 * 
 * On va utiliser le selectedPlayerId pour faire une requête à l'API et récupérer les données historiques du joueur sélectionné, puis les afficher dans un tableau ou une série de graphiques.
 * @param {integer} selectedPlayerId 
 * @returns 
 */
function PlayerHistory({ selectedPlayerId }) {

    // État pour stocker les données historiques du joueur
    const [historyData, setHistoryData] = useState([]);

    // État pour stocker les erreurs de chargement des données
    const [error, setError] = useState("");

    // Fonction pour gérer les variations de position dans le classement FedEx, 
    // en affichant une flèche vers le haut pour une amélioration, une flèche vers le bas pour une détérioration, 
    // ou "Nouveau" si c'est la première apparition du joueur dans le classement
    const renderPositionVariation = (variation) => {

        if (variation === null) {
            return (
                <span className="variation-new">
                    Nouveau
                </span>
            );
        }

        if (variation > 0) {
            return (
                <>
                    <span className="position-up">▲</span>
                    +{variation}
                </>
            );
        }

        if (variation < 0) {
            return (
                <>
                    <span className="position-down">▼</span>
                    {variation}
                </>
            );
        }

        return "=";
    };


    // Fonction pour charger les données historiques du joueur à partir de l'API
    const loadPlayerHistory = async (selectedPlayerId) => {

        try {

            // Envoyer une requête à l'API pour récupérer les données du joueur sélectionné
            const response = await fetch(`${API_BASE_URL}/stats/get-player-history.php`,
                {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},                
                    body: JSON.stringify({playerId: selectedPlayerId})
                }
            );

            // Sinon, on a un résultat valide du retour de l'API
            const data = await response.json();

            // Vérification de la réponse de l'API pour voir si les données du joueur ont été récupérées avec succès ou s'il y a eu une erreur
            if (data.success) {

                // Mettre à jour les données du joueur dans l'état pour les afficher dans la section d'informations du joueur
                setHistoryData(data.playerHistoryData);
                setError("");
            } else {

                // Reset des données du graphique à un tableau vide pour ne pas afficher de données erronées dans les graphiques
                setHistoryData([]);

                // Erreur lors du chargement des données du joueur
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement des données historiques du joueur.");
        }
    };

    // Utilisé dès le chargement du composant pour récupérer les données historiques du joueur sélectionné,
    // et les afficher dans la section d'évolution du joueur
    useEffect(() => {

        loadPlayerHistory(selectedPlayerId);

    }, [selectedPlayerId]);

    return (
        <div className="player-history-container">
            <h2 className="history-title">Historique détaillé du joueur</h2>
            <p className="history-description">
                Évolution du classement FedEx, des points cumulés et du handicap après chaque événement.
            </p>
            {historyData.length === 0 ? (
                <div className="warning-message">
                    <FaInfoCircle />
                    <span>{error || "Aucune donnée historique disponible pour ce joueur."}</span>
                </div>
            ) : (
                <>
                    <div className="history-table-wrapper">
                        <table className="history-table">
                            <thead>
                                <tr>
                                    <th># Événement</th>
                                    <th>Club de Golf</th>
                                    <th>Date</th>
                                    <th>Position</th>
                                    <th>Pos. Var</th>
                                    <th>Points FedEx</th>
                                    <th>Pts. Var</th>
                                    <th>Handicap</th>
                                    <th>Hand. Var</th>
                                </tr>
                            </thead>
                            <tbody>
                                {   
                                    historyData.map((history, index) => (
                                        <tr key={index}>
                                            <td>{index + 1}</td>
                                            <td>
                                                <div className="history-event-name">
                                                    {history.event_name}
                                                </div>
                                                <div className="history-event-course">
                                                    {history.golf_course}
                                                </div>
                                            </td>
                                            <td>
                                                {history.event_date}
                                            </td>
                                            <td>
                                                {history.current_position}
                                            </td>

                                            <td>
                                                {renderPositionVariation(history.position_variation)}
                                            </td>

                                            <td>
                                                {history.current_fedex_points}
                                            </td>

                                            <td className="position-up">
                                                +{history.fedex_points_gained}
                                            </td>

                                            <td>
                                                {history.current_handicap}
                                            </td>

                                            <td>
                                                {history.handicap_variation}
                                            </td>
                                        </tr>
                                    ))
                                }
                            </tbody>
                        </table>
                    </div>
                </>
            )}
        </div>
    );
}

export default PlayerHistory;