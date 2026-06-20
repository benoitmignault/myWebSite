/* eslint-disable react-hooks/set-state-in-effect */
import { useEffect, useState } from "react";
import {LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer} from "recharts";
import { API_BASE_URL } from "../../config";

/**
 * Composant pour afficher les graphiques d'évolution d'un joueur sélectionné
 * Graphique 1 : Évolution de l'handicap
 * Graphique 2 : Évolution du classement FedEx
 * Graphique 3 : Évolution des points FedEx cumulés
 * 
 * @param {integer} selectedPlayerId 
 * @returns 
 */
function PlayerCharts({ selectedPlayerId }) {

    // État pour stocker les données des graphiques à afficher dans la section d'évolution du joueur
    const [chartData, setChartData] = useState([]);

    // État pour stocker les messages d'erreur en prévision de l'affichage des données des graphiques
    const [error, setError] = useState("");
    
    // Fonction pour charger les données des graphiques à afficher dans la section d'évolution du joueur,
    // en envoyant une requête à l'API pour récupérer les données des graphiques du joueur sélectionné
    const loadChartData = async (selectedPlayerId) => {

        try {
            // Envoyer une requête à l'API pour récupérer les données du joueur sélectionné
            const response = await fetch(`${API_BASE_URL}/stats/get-player-charts.php`,
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
                setChartData(data.playerChartsData);
                setError("");
            } else {

                // Erreur lors du chargement des données du joueur
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors de la tentative de chargement des données du joueur.");
        }
    };

    useEffect(() => {

        loadChartData(selectedPlayerId);

    }, [selectedPlayerId]);

    return (
        <div className="player-charts-container">
            <h2>Évolution du joueur</h2>
            {
                chartData.length > 0 && (
                    <>
                        {/* TODO : Afficher les graphiques avec les données de chartData */}
                        {/* Graphique 1 */}
                        <div className="chart-card">
                            Graphique évolution de l'handicap et de la moyenne de score
                        </div>

                        {/* Graphique 2 */}
                        <div className="chart-card">
                            Graphique évolution du classement FedEx
                        </div>

                        {/* Graphique 3 */}
                        <div className="chart-card">
                            Graphique points FedEx cumulés
                        </div>
                    </>
                    
                )
            }
            {error && <p className={`error-message`}>✗ {error}</p>}
        </div>
    );
}

export default PlayerCharts;