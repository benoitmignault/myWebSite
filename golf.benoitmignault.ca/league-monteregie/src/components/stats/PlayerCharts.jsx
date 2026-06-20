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



    

    const loadChartData = async (selectedPlayerId) => {

        try {
            // TODO : API

            setChartData([]);

        }
        catch(error) {

            console.error(error);
            setError("Une erreur est survenue lors de la tentative de chargement des données des graphiques.");

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