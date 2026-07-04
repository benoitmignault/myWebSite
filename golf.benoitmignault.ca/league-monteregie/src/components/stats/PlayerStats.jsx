// Ce fichier présente les statistiques des joueurs de la ligue.
// Il est accessible via un lien dans la barre de navigation et affiche des graphiques et
// des tableaux pour montrer les performances des joueurs au fil du temps.
// Les données sont récupérées via l'API, et les graphiques sont rendus avec Recharts.

/**
 * PlayerStats.jsx
 *
 * Composant React qui affiche les statistiques d'un joueur sélectionné de la ligue.
 * Il permet à l'utilisateur de sélectionner un joueur et d'afficher ses statistiques sous forme de graphiques et de tableaux.
 * 
 * Il est accessible via un lien dans la barre de navigation et affiche des graphiques et
 * des tableaux pour montrer les performances des joueurs au fil du temps.
 * Les données sont récupérées via l'API, et les graphiques sont rendus avec Recharts.
 * 
 * On va utiliser ici aussi lazy loading pour charger les composants PlayerSummary, 
 * PlayerCharts et PlayerHistory seulement quand ils sont nécessaires, pour améliorer les performances de l'application.
 */
import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { FaArrowUp } from "react-icons/fa";
import { FaHouse } from "react-icons/fa6";
import { BsCameraFill } from "react-icons/bs";
import PlayerSelector from "./PlayerSelector";
import PlayerSummary from "./PlayerSummary";
import PlayerCharts from "./PlayerCharts";
import PlayerHistory from "./PlayerHistory";
import Footer from "../Footer";
import { API_BASE_URL } from "../../config";
import '../../css/index.css'
import '../../css/stats.css'

function PlayerStats() {

	// État pour détecter si l'utilisateur est sur un appareil mobile ou non
    const [isMobile, setIsMobile] = useState(window.innerWidth <= 768);

    // Utiliser useEffect pour mettre à jour l'état isMobile lorsque la taille de la fenêtre change
    const photoCredit = isMobile
        ? "Photo prise au Club de golf Parcours du Vieux Village — The Masters"
        : "Photo prise au Club de golf Farnham — Semaine 2";
	
	// État pour stocker le id du joueur sélectionner
	const [selectedPlayerId, setSelectedPlayerId] = useState(null);
	
    // ÉTat pour stocker le nombre de joueurs en tout dans la ligue, pour afficher le nombre total de joueurs dans la légende du graphique d'évolution du classement FedEx
    const [totalPlayers, setTotalPlayers] = useState(0);

	// Utiliser useEffect pour changer le titre de la page et le favicon lorsque le composant HomePage est monté
	useEffect(() => {
	
		// Reset du titre de la page HomePage lorsque le composant est monté
		document.title = "Statistiques des Joueurs - Golf Montérégie";	
		const favicon = document.getElementById("dynamic-favicon");

		// On doit partir de /league-monteregie car c'est la vraie racine du projet
		if (favicon) {
			favicon.href = "/league-monteregie/favicon/favicon-stats-players-ChatGPT.png";
		}

		const handleResize = () => {
            setIsMobile(window.innerWidth <= 768);
        };

        window.addEventListener("resize", handleResize);

        // Nettoyage de l'événement lors du démontage du composant pour éviter les fuites de mémoire
        return () => window.removeEventListener("resize", handleResize);

	}, []);
	
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

	// Utiliser ce useEffect pour envoyer une requête à l'API de logging à chaque fois qu'un joueur
	// sélectionné change, pour loguer l'action de consultation des statistiques d'un joueur spécifique
	useEffect(() => {

		if (!selectedPlayerId) {
			return;
		}

		fetch(`${API_BASE_URL}/log-action.php`, {
			method: "POST",
			headers: {"Content-Type": "application/json"},
			body: JSON.stringify({
				action_type: "player_stats_view",
				target_id: selectedPlayerId,
				target_name: "Consultation statistiques joueur"
			})
		});

	}, [selectedPlayerId]);

    return (
        <div className="player-stats-page">
            <div className="site-navbar">
                <Link to="/league-monteregie/" className="admin-navbar-link">
                    <FaHouse/>
                    <span>Retour au site principal</span>
                </Link>
            </div>
            <h1 className="homepage-title">Statistiques des Joueurs</h1>
            <div className="player-stats-content">				
				<div className="player-stats-card player-header-card">
					<div className="player-selector-section">
						<PlayerSelector setSelectedPlayerId={setSelectedPlayerId} setTotalPlayers={setTotalPlayers} />
					</div>
					<div className="player-summary-section">
						{selectedPlayerId ? (
							<PlayerSummary selectedPlayerId={selectedPlayerId}/>
						) : (
							<p className="player-summary-placeholder">Information sera affichée ici, une fois le joueur sélectionné.</p>
						)}
					</div>
				</div>

				{selectedPlayerId && (
					<div className="player-stats-card player-chart-card">
						<div className="player-charts-section">
							<PlayerCharts selectedPlayerId={selectedPlayerId} totalPlayers={totalPlayers} />
						</div>
					</div>
				)}

                {selectedPlayerId && (
					<div className="player-stats-card player-history-card">
						<div className="player-history-section">
							<PlayerHistory selectedPlayerId={selectedPlayerId} />
						</div>
					</div>
				)}

            </div>                        
            <button className="scroll-top player-stats" onClick={() => window.scrollTo({top: 0, behavior: "smooth"})}> 
				<FaArrowUp />
			</button>
			<div className="player-stat-photo-credit">
                <BsCameraFill />
                <span>{photoCredit}</span>				
			</div>
            <Footer />            
        </div>
    );
}

export default PlayerStats;