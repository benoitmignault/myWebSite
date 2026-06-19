import { useState, useEffect } from "react";
import { API_BASE_URL } from "../../config";

/**
 * Composant pour afficher un résumé des statistiques d'un joueur sélectionné, 
 * comme son nom, son handicap, sa moyenne brute, le nombre de trophées gagnés, 
 * le nombre de tournois auxquels il a participé, etc.
 * 
 * @param {integer} playerId 
 * @returns 
 */

function PlayerSummary({ playerId }) {


    console.log(playerId);

    return (
        <div>
            Joueur : {playerId}
        </div>
    );
}

export default PlayerSummary;