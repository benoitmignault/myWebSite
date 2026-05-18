import { useEffect, useState } from "react";
import React from "react";

/**
 * Fonction composant pour afficher le classement de la Coupe Fedex de la Ligue de Golf en Montérégie
 * Affiche une table avec les joueurs, leur handicap et leurs points totaux, classés par points, handicap et prénom
 * 
 * @returns 
 */
function Standings() {

    // État pour stocker les informations sur le classement des joueurs
    const [players, setPlayers] = useState([]);

    // État pour ouvrir ou fermer les détails d'un joueur seulement, à la fois
    const [openPlayer, setOpenPlayer] = useState(null);

    // État pour stocker les résultats détaillés d'un joueur sélectionné
    const [playerResults, setPlayerResults] = useState([]);

    // Fonction pour gérer le clic sur un joueur et afficher ses résultats
    const handlePlayerClick = async (playerId) => {

        // Si on clique sur un joueur déjà ouvert, on le ferme, sinon on ouvre le nouvel événement
        if (openPlayer === playerId) {
            setOpenPlayer(null);
            return;
        }

        // Un genre de sinon, on ouvre le joueur et on va chercher les détails de ce joueur pour les afficher
        setOpenPlayer(playerId);

        // Récupérer les résultats détaillés du joueur depuis l'API mias en mode asynchrone pour pouvoir attendre la réponse avant de mettre à jour l'état
        const response = await fetch(`https://localhost/api/player-details.php?id=${playerId}`);

        // Une fois que la réponse est reçue, on la convertit en JSON et on met à jour l'état avec les résultats du joueur
        const data = await response.json();

        // Mettre à jour l'état avec les résultats du joueur pour les afficher dans la table des détails du joueur
        setPlayerResults(data);
    }

    useEffect(() => {
        // Récupérer les données des joueurs et leurs points totaux depuis l'API
        //  TODO: Remplacer l'URL par celle de votre API une fois que vous l'avez mise en place        
        fetch("https://localhost/api/standings.php")
            .then(response => response.json())
            .then(data => {
                setPlayers(data);
            });
    }, []);    

    // On doit utiliser React.Fractment parce que le .map retourne plusieurs éléments 
    // (la ligne du joueur et la ligne des détails du joueur) et React exige que 
    // les éléments retournés soient regroupés dans un élément parent unique, 
    // mais on ne veut pas ajouter un élément HTML supplémentaire dans le DOM, 
    // donc on utilise React.Fragment qui est un conteneur invisible qui ne rend rien dans le DOM
    return (
        <div>
            <h2>Classement de la Coupe Fedex 
                <span className="subtitle-info">
                    (cliquer sur un joueur pour voir ses résultats)
                </span>
            </h2>
            <table>
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Joueur</th>
                        <th>Moyenne</th>
                        <th>Handicap</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    {
                        players.map((player, index) => (
                            <React.Fragment key={player.id}>
                                <tr className="clickable-row" onClick={() => handlePlayerClick(player.id)}>
                                    <td>{index + 1}</td>
                                    <td>{player.firstname}{" "}{player.lastname}</td>
                                    <td>{player.average_score}</td>
                                    <td>{player.handicap_league}</td>
                                    <td>{player.total_points}</td>
                                </tr>
                                {
                                    openPlayer === player.id && (
                                        <tr>
                                            <td colSpan="5" className="player-details">
                                                <table className="player-results-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Event</th>
                                                            <th>Position</th>
                                                            <th>Score Brut</th>
                                                            <th>Score Net</th>
                                                            <th>Points</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                       {
                                                            playerResults.map((result, index) => (
                                                                <tr key={index}>
                                                                    <td>{result.event_name}</td>
                                                                    <td>
                                                                        {result.position}
                                                                    </td>
                                                                    <td>
                                                                        {result.gross_score}
                                                                    </td>
                                                                    <td className={ result.net_score < 0? "negative-score" : ""}>
                                                                        {result.net_score}
                                                                    </td>
                                                                    <td>{result.fedex_points}</td>
                                                                </tr>
                                                            ))
                                                        }
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    )
                                }
                            </React.Fragment>
                        ))
                    }
                </tbody>
            </table>
        </div>
    );
}

export default Standings;