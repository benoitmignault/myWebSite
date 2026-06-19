import { useState, useEffect } from "react";
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
 * @param {integer} playerId 
 * @returns 
 */

function PlayerSummary({ playerId }) {

    const [playerSummary, setPlayerSummary] = useState(null);

    const [error, setError] = useState("");

    // Fonction pour charger les données du joueur sélectionné pour les afficher dans la section d'informations du joueur
    const loadPlayerInfo = async (playerId) => {

        try {
                
            // Envoyer une requête à l'API pour récupérer les données du joueur sélectionné
            const response = await fetch(`${API_BASE_URL}/stats/get-player-summary.php`,
                {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},                
                    body: JSON.stringify({playerId: playerId})
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

        // Il faut utiliser une notion asynchrone pour charger les données, en raison de l'utilisation 
        const initializeData = async () => {

            // Charger les données du joueur sélectionné pour les afficher dans la section d'informations du joueur
            await loadPlayerInfo(playerId);                            
        };
        
        // Appeler la fonction d'initialisation des données pour charger les données du joueur sélectionné dès le chargement du composant
        initializeData();

    }, []);

    return (
        <div>
            {console.log(playerSummary)}
        </div>
    );
}

export default PlayerSummary;