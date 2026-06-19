import { useState, useEffect } from "react";
import { API_BASE_URL } from "../../config";

/**
 * 
 * 
 * @param {integer} playerId 
 * @returns 
 */

function PlayerSelector({ playerId }) {


    console.log(playerId);

    return (
        <div>
            Joueur : {playerId}
        </div>
    );
}

export default PlayerSelector;