/* eslint-disable react-hooks/set-state-in-effect */
import { useEffect, useState } from "react";
import {LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer} from "recharts";
import { FaInfoCircle } from "react-icons/fa";
import { API_BASE_URL } from "../../config";

/**
 * Cette fonction est un composant personnalisé pour afficher une info-bulle personnalisée dans les graphiques de la section d'évolution du joueur,
 * pour afficher la valeur du point de données sur lequel l'utilisateur survole avec sa souris.
 * 
 * Selon ChatGPT et les informations de la documentation de Recharts, 
 * ce composant doit recevoir les props active et payload pour fonctionner correctement avec les graphiques de Recharts.
 * 
 * Donc doit être en dehors du composant PlayerCharts pour être utilisé dans les graphiques de ce composant,
 * et doit être utilisé dans les graphiques de Recharts en tant que composant personnalisé pour les info-bulles,
 * en le passant en tant que prop content dans le composant Tooltip de Recharts.
 * 
 * @param {boolean} active 
 * @param {Array} payload 
 * @returns 
 */
function CustomTooltip({ active, payload }) {

    // Active veut juste dire que la souris est en train de survoler un point de données du graphique, 
    // et payload contient les données de ce point de données, comme la valeur de ce point de données, le nom de ce point de données, etc.
    if (active && payload && payload.length) {

        return (
            <div className="custom-tooltip">
                {payload[0].value}
            </div>
        );
    }

    return null;
}

/**
 * Composant pour afficher les graphiques d'évolution d'un joueur sélectionné
 * Graphique 1 : Évolution de l'handicap
 * Graphique 2 : Évolution du classement FedEx
 * Graphique 3 : Évolution des points FedEx cumulés
 * 
 * Pour se faire, ce composant envoie une requête à l'API pour récupérer les données des graphiques du joueur sélectionné,
 * et utilise une bibliothèque de graphiques comme Recharts pour afficher ces données de manière attrayante et informative.
 * 
 * On va faire aussi à l'appel à la valeur de totalPlayers pour afficher le nombre total de joueurs 
 * dans la légende du graphique d'évolution du classement FedEx, pour que les utilisateurs puissent mieux comprendre 
 * le positionnement du joueur sélectionné dans la ligue.
 * 
 * Cette valeur sera calculer vis API dans le Composant PlayerSelector, et passé en prop à ce composant PlayerCharts, 
 * pour être utilisé dans la légende du graphique d'évolution du classement FedEx.
 * 
 * @param {integer} selectedPlayerId 
 * @param {integer} totalPlayers
 * @returns 
 */
function PlayerCharts({ selectedPlayerId, totalPlayers }) {

    // État pour stocker les données des graphiques à afficher dans la section d'évolution du joueur
    const [chartData, setChartData] = useState([]);

    // État pour stocker les messages d'erreur en prévision de l'affichage des données des graphiques
    const [error, setError] = useState("");

    // Calcul des ticks pour l'axe Y du graphique d'évolution du classement FedEx, en fonction du nombre total de joueurs dans la ligue
    const yAxisTicks = [
        1,
        Math.round(totalPlayers * 0.25),
        Math.round(totalPlayers * 0.50),
        Math.round(totalPlayers * 0.75),
        totalPlayers
    ];

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
                                <h3 className="chart-card-title">Classement FedEx (inversé)</h3>
                                <div className="chart-wrapper">
                                    <ResponsiveContainer width="100%" height={350}>
                                        <LineChart data={chartData}>
                                            <CartesianGrid strokeDasharray="3 3" />
                                            <XAxis
                                                dataKey="week"
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <YAxis
                                                width={30}
                                                reversed
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <Tooltip content={<CustomTooltip />} />
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
                                                width={30}
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <Tooltip content={<CustomTooltip />} />
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
                                                width={30}
                                                tick={{ fill: "#FFFFFF", fontSize: 14 }}
                                            />
                                            <Tooltip content={<CustomTooltip />} />
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