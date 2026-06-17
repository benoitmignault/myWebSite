// Ce fichier aura comme utilité de présenter les statistiques des joueurs de la ligue. 
// Il sera accessible via un lien dans la barre de navigation et affichera des graphiques et 
// des tableaux pour montrer les performances des joueurs au fil du temps. 
// Les données seront récupérées à partir d'une API venant de la base de données, et le composant utilisera des bibliothèques 
// comme Chart.js ou D3.js pour visualiser les statistiques de manière attrayante et informative.

import React, { useEffect } from "react";
import { API_BASE_URL } from "../../config";
import { Link } from "react-router-dom";
import { FaArrowUp } from "react-icons/fa";
import { FaHouse } from "react-icons/fa6";
import { BsCameraFill } from "react-icons/bs";
import Footer from "../Footer";
import '../../css/index.css'
import '../../css/stats.css'

function PlayerStats() {



    // Utiliser useEffect pour envoyer une requête à l'API de logging à chaque fois que la page de statistiques est chargée
	useEffect(() => {
		fetch(`${API_BASE_URL}/log-action.php`,
			{ 
				method: "POST",
				headers: {"Content-Type": "application/json"},
				body: JSON.stringify({
					action_type: "page_stats_load",
					target_id: null,
					target_name: "Affichage page statistiques joueurs"
				})
			}
		);
	}, []);

	// Utiliser useEffect pour changer le titre de la page et le favicon lorsque le composant HomePage est monté
	useEffect(() => {
	
		// Reset du titre de la page HomePage lorsque le composant est monté
		document.title = "Statistiques des Joueurs - Golf Montérégie";	
		const favicon = document.getElementById("dynamic-favicon");

		// On doit partir de /league-monteregie car c'est la vraie racine du projet
		if (favicon) {
			favicon.href = "/league-monteregie/favicon/favicon-stats-players-ChatGPT.png";
		}

	}, []);

    return (
        <div className="player-stats-page">
            <div className="site-navbar">
                <Link to="/league-monteregie/" className="admin-navbar-link">
                    <FaHouse/>
                    <span>Retour au site principal</span>
                </Link>
            </div>
            <h1>Statistiques des Joueurs</h1>
            <div className="player-stats-content">
                
                {/* <PlayerSelector /> */}
                {/* <PlayerCharts /> */}
                {/* <PlayerHistory /> */}

            </div>

            <div className="photo-credit-wrapper">				
				
					<BsCameraFill />
					<span>Photo prise au Club de golf Farnham — Semaine 2</span>
				
			</div>

            <Footer />
            <button className="scroll-top" onClick={() => window.scrollTo({top: 0, behavior: "smooth"})}> 
				<FaArrowUp />
			</button>
        </div>
    );
}

export default PlayerStats;