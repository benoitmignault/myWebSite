import { useState, useEffect } from "react";
import Select from "react-select";
import { API_BASE_URL } from "../../config";

/**
 * Composant pour l'affichage de la liste des joueurs de la league en prévision 
 * de la sélection d'un joueur pour voir ses statistiques détaillées dans le composant PlayerSummary 
 * et les autres composants de statistiques.
 * 
 * Ce composant envoie une requête à l'API pour récupérer la liste de tous les joueurs de la ligue,
 * et affiche cette liste dans un sélecteur pour que l'utilisateur puisse choisir un joueur.
 * 
 * Lorsqu'un joueur est sélectionné, le composant met à jour l'état selectedPlayerId dans le composant parent PlayerStats,
 * pour que les autres composants de statistiques puissent afficher les données du joueur sélectionné.
 * 
 * @param {function} onPlayerChange 
 * @returns 
 */
function PlayerSelector({ onPlayerChange }) {

    // État pour stocker la liste des joueurs
    const [players, setPlayers] = useState([]);

    // État pour stocker les messages d'erreur en prévision de l'ajout d'un joueur à un événement
    const [error, setError] = useState("");

    // Fonction pour charger la liste des joueurs pour en sélectionner un pour avoir ces informations
    const loadAllPlayers = async () => {

        try {
            const response = await fetch(`${API_BASE_URL}/stats/get-all-players.php`);
            
            const data = await response.json();
            if (data.success) {

                // Mettre à jour la liste des joueurs dans l'état
                setPlayers(data.players);                
            } else {

                // Erreur retournée par l'API lors du chargement des joueurs
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement des joueurs.");
        }

        // Il n'y a pas de finaly avec le setLoading car on fait juste afficher la liste des joueurs, 
        // et on peut afficher un message d'erreur s'il y en a une, 
        // mais on n'a pas besoin d'afficher un indicateur de chargement
    };

    // Créer une variable options pour stocker les options du sélecteur, 
    // en transformant la liste des joueurs en un format compatible avec react-select
    const options = players.map(player => ({
        value: player.id,
        label: `${player.firstname} ${player.lastname}`
    }));


    // Utiliser useEffect pour charger la liste des joueurs depuis l'API lorsque le composant est monté
    useEffect(() => {

        // Il faut utiliser une notion asynchrone pour charger les données, en raison de l'utilisation 
        const initializeData = async () => {

            // Charger la liste des joueurs pour les afficher dans le sélecteur
            await loadAllPlayers();                            
        };
        
        // Charger tout les éléments dans la section du tournois en gestion en cours
        initializeData();

    }, []);

    return (
        <div>
            <h2>Choisissez un joueur parmi les {players.length} disponibles dans la liste ou utilisez la recherche.</h2>
            <Select
                options={options}
                className="player-select"
                classNamePrefix="player-select"
                placeholder="Choisir un joueur ou rechercher..."
                noOptionsMessage={() => "Aucun joueur trouvé"}
                isSearchable
                onChange={(option) => onPlayerChange(option ? option.value : null)}
            />
        </div>








    );
}

export default PlayerSelector;