/* eslint-disable react-hooks/set-state-in-effect */
import { useEffect, useState } from "react";
import { FaInfoCircle } from "react-icons/fa";
import { API_BASE_URL } from "../../config";

function PlayerHistory({ selectedPlayerId }) {


    return (
        <div className="player-history-container">
            <h2 className="history-title">Historique détaillé du joueur</h2>
            <p className="history-description">
                Évolution du classement FedEx, des points cumulés et du handicap après chaque événement.
            </p>
            {historyData.length === 0 ? (
                <div className="warning-message">
                    <FaInfoCircle />
                    <span>{error || "Aucune donnée historique disponible pour ce joueur."}</span>
                </div>
            ) : (
                <>
                    <div className="history-table-wrapper">
                        <table className="history-table">

                        </table>
                    </div>
                </>
            )}
        </div>
    );
}

export default PlayerHistory;