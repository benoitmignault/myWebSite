/* eslint-disable react-hooks/set-state-in-effect */
import { useEffect, useState } from "react";

function PlayerCharts({ selectedPlayerId }) {

    const [chartData, setChartData] = useState([]);





    

    const loadChartData = async (selectedPlayerId) => {

        try {
            // TODO : API

            setChartData([]);

        }
        catch(error) {

            console.error(error);

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
                    
                )}
            
        </div>
    );
}

export default PlayerCharts;