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


    
    // Utilisé dès le chargement du composant pour récupérer les données du joueur sélectionné,
    // et les afficher dans la section d'informations du joueur
    useEffect(() => {

        // Il faut utiliser une notion asynchrone pour charger les données, en raison de l'utilisation 
        const initializeData = async () => {

            // Charger les données du joueur sélectionné pour les afficher dans la section d'informations du joueur
            //await loadAllPlayers();                            
        };
        
        // 
        initializeData();

    }, []);

    return (
        <div>
            Joueur : {playerId}
        </div>
    );
}

export default PlayerSummary;