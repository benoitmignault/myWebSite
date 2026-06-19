/* eslint-disable react-hooks/set-state-in-effect */
import { useState, useEffect } from "react";
import { FaTrophy } from "react-icons/fa";
import { API_BASE_URL } from "../../config";

/**
 * Composant pour afficher un résumé des statistiques d'un joueur sélectionné, 
 * comme son nom, son handicap, sa moyenne brute, le nombre de trophées gagnés, 
 * le nombre de tournois auxquels il a participé, etc.
 * 
 * En trois section :
 * Informations du joueur
     Nom complet
     Handicap actuel
     Moyenne actuelle

   Position dans la ligue
     Position FedEx actuelle
     Points FedEx actuels
   Récompenses
     🏆 Or
     🥈 Argent
     🥉 Bronze
 * 
 * @param {integer} selectedPlayerId 
 * @returns 
 */

function PlayerSummary({ selectedPlayerId }) {

    const [playerSummary, setPlayerSummary] = useState(null);

    const [error, setError] = useState("");

    // Fonction pour charger les données du joueur sélectionné pour les afficher dans la section d'informations du joueur
    const loadPlayerInfo = async (selectedPlayerId) => {

        try {
                
            // Envoyer une requête à l'API pour récupérer les données du joueur sélectionné
            const response = await fetch(`${API_BASE_URL}/stats/get-player-summary.php`,
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
                setPlayerSummary(data.playerInfo);

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

    // Utilisé dès le chargement du composant pour récupérer les données du joueur sélectionné,
    // et les afficher dans la section d'informations du joueur
    useEffect(() => {
        
        // Pas besoin d'utiliser les notions async et await vue que après, on ne fait rien d'autre que de mettre à jour l'état 
        // avec les données du joueur, ou afficher un message d'erreur, et que la fonction loadPlayerInfo est déjà une fonction asynchrone 
        // qui gère les promesses avec les await à l'intérieur, donc on peut juste appeler la fonction loadPlayerInfo 
        // avec le selectedPlayerId pour récupérer les données du joueur sélectionné et les afficher dans la section d'informations du joueur
        loadPlayerInfo(selectedPlayerId);

    }, [selectedPlayerId]);

    return (
        <div className="player-summary">

            <h2 className="player-summary-title">
                {playerSummary?.full_name}
            </h2>

            <div className="player-stats-grid">

                <div className="player-stat-card">
                    <span className="player-stat-label">Handicap</span>
                    <span className="player-stat-value">{playerSummary?.handicap}</span>
                </div>

                <div className="player-stat-card">
                    <span className="player-stat-label">Moyenne</span>
                    <span className="player-stat-value">{playerSummary?.average_score}</span>
                </div>

                <div className="player-stat-card">
                    <span className="player-stat-label">FedEx (Position)</span>
                    <span className="player-stat-value">#{playerSummary?.fedex_position}</span>
                </div>

                <div className="player-stat-card">
                    <span className="player-stat-label">Points</span>
                    <span className="player-stat-value">{playerSummary?.fedex_points}</span>
                </div>
            </div>

            <div className="player-trophies">
                <div className="player-trophy-card">
                    <FaTrophy className="trophy gold" />
                    <span className="player-trophy-count">{playerSummary?.gold_trophies}</span>
                    <span className="player-trophy-label victories-label">Victoires</span>
                </div>
                <div className="player-trophy-card">
                    <FaTrophy className="trophy silver" />
                    <span className="player-trophy-count">{playerSummary?.silver_trophies}</span>
                    <span className="player-trophy-label runnerup-label">2e place</span>
                </div>
                <div className="player-trophy-card">
                    <FaTrophy className="trophy bronze" />
                    <span className="player-trophy-count"> {playerSummary?.bronze_trophies}</span>
                    <span className="player-trophy-label">3e place</span>
                </div>
            </div>
        </div>
    );
}

export default PlayerSummary;