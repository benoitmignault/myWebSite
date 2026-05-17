import { useEffect, useState } from "react";

/**
 * Fonction composant pour afficher le classement de la Coupe Fedex de la Ligue de Golf en Montérégie
 * Affiche une table avec les joueurs, leur handicap et leurs points totaux, classés par points, handicap et prénom
 * 
 * @returns 
 */
function Standings() {

    // État pour stocker les joueurs et leurs points totaux
    const [players, setPlayers] = useState([]);

    useEffect(() => {
        // Récupérer les données des joueurs et leurs points totaux depuis l'API
        //  TODO: Remplacer l'URL par celle de votre API une fois que vous l'avez mise en place        
        fetch("https://localhost/api/standings.php")
            .then(response => response.json())
            .then(data => {
                setPlayers(data);
            });
    }, []);    

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
                        <th>Handicap</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    {players.map((player, index) => (
                        <tr key={player.id}>
                            <td>{index + 1}</td>
                            <td>{player.firstname} {player.lastname}</td>
                            <td>{player.handicap_league}</td>
                            <td>{player.total_points}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export default Standings;