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

    // Utilisé dès le chargement du composant pour récupérer les données graphiques du joueur sélectionné,
    // et les afficher dans la section d'évolution du joueur
    useEffect(() => {

        loadChartData(selectedPlayerId);

    }, [selectedPlayerId]);

    return (
        <div className="player-charts-container">
            <h2 className="charts-title">Évolution du joueur</h2>
            <p className="charts-description">Avec son positionnement dans la Coupe FedEx, les points cumulés de cette dernière et de l'handicap au fil de la saison.</p>
            {
                chartData.length > 0 && (
                    <>
                        <div className="player-charts-grid">
                            <div className="chart-card">
                                <h3 className="chart-card-title">Classement FedEx</h3>
                                <div className="chart-wrapper">
                                    <ResponsiveContainer width="100%" height={350}>
                                        <LineChart data={chartData}>
                                            <CartesianGrid strokeDasharray="3 3" />
                                            <XAxis
                                                dataKey="week"
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <YAxis
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <Tooltip />
                                            <Line
                                                type="monotone"
                                                dataKey="position"
                                            />
                                        </LineChart>
                                    </ResponsiveContainer>
                                </div>                                
                            </div>

                            <div className="chart-card">
                                <h3 className="chart-card-title">Points FedEx</h3>
                                <div className="chart-wrapper">
                                    <ResponsiveContainer width="100%" height={350}>
                                        <LineChart data={chartData}>
                                            <CartesianGrid strokeDasharray="3 3" />
                                            <XAxis
                                                dataKey="week"
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <YAxis
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <Tooltip />
                                            <Line
                                                type="monotone"
                                                dataKey="fedex_points"
                                            />
                                        </LineChart>
                                    </ResponsiveContainer>
                                </div>                                
                            </div>
                            
                            <div className="chart-card">
                                <h3 className="chart-card-title">Handicap</h3>
                                <div className="chart-wrapper">
                                    <ResponsiveContainer width="100%" height={350}>
                                        <LineChart data={chartData}>
                                            <CartesianGrid strokeDasharray="3 3" />
                                            <XAxis
                                                dataKey="week"
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <YAxis
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <Tooltip />
                                            <Line
                                                type="monotone"
                                                dataKey="handicap"
                                            />
                                        </LineChart>
                                    </ResponsiveContainer>
                                </div>                                
                            </div>
                        </div>                             
                    </>                    
                )
            }
            {error && <p className={`error-message`}>✗ {error}</p>}
        </div>
    );
}

export default PlayerCharts;