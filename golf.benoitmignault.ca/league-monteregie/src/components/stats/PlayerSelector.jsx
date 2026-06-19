import { useState, useEffect } from "react";
import { API_BASE_URL } from "../../config";


function PlayerSelector({ setSelectedPlayerId }) {

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
            <h2>Sélectionnez un joueur pour voir ses statistiques</h2>
        </div>








    );
}

export default PlayerSelector;