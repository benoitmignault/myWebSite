import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { API_BASE_URL } from "../../config";

/**
 * Composant de gestion des résultats de la ligue. Va servir à insérer dans la table «round_results» les résultats de chaque ronde.
 * À chaque insertion d'un résultat de joueur, on va vérifier si le score brut ajusté sera utiliser dans le recalcul de l'handicap.
 * 
 * On va utiliser le syustême de l'handicap trackman pour calculer les handicaps des joueurs. 
 * Donc, à chaque fois qu'on insère un résultat de ronde, on va vérifier si le score brut ajusté du joueur est inférieur 
 * à son handicap actuel. Si c'est le cas, on va utiliser ce score pour recalculer son handicap. 
 * Cependant, plus le nombre de rounds du joueur augmente, plus on va utiliser les meilleures round dans le calcul de l'handicap
 * 
 * @description
 * Trackman systeme
 * 1 à 5 rounds, on va prendre le meilleur score brut ajusté du joueur
 * 6 à 8 rounds, on va prendre la moyenne des 2 meilleurs scores brut ajustés du joueur
 * 9 à 11 rounds, on va prendre la moyenne des 3 meilleurs scores brut ajustés du joueur
 * 12 à 14 rounds, on va prendre la moyenne des 4 meilleurs scores brut ajustés du joueur
 * 15 à 16 rounds, on va prendre la moyenne des 5 meilleurs scores brut ajustés du joueur
 * 17 & 18 rounds, on va prendre la moyenne des 6 meilleurs scores brut ajustés du joueur
 * 19 rounds, on va prendre la moyenne des 7 meilleurs scores brut ajustés du joueur
 * 20 rounds et plus, on va prendre la moyenne des 8 meilleurs scores brut ajustés du joueur 
 * 
 * @returns
 */
function ResultsSection() {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // État pour stocker l'évent en cours pour lequel on veut insérer les résultats
    const [event, setEvent] = useState(null);

    // État pour stocker la liste des joueurs inscrits à l'événement en cours pour pouvoir insérer les résultats de chacun des joueurs
    const [registeredPlayers, setRegisteredPlayers] = useState([]);

    // ÉTat pour stocker le joueur sélectionné dans le formulaire d'insertion des résultats de la ronde du joueur
    const [selectedPlayer, setSelectedPlayer] = useState("");

    // État pour stocker la liste des positions des joueurs inscrits à l'événement en cours
    const [registeredPositions, setPositions] = useState([]);

    // État pour stocker la position du joueur sélectionné dans le formulaire d'insertion des résultats de la ronde du joueur
    const [selectedPosition, setSelectedPosition] = useState("");

    // ÉTat pour stocker le score bruts du joueur inscrits à l'événement en cours
    const [grossScore, setGrossScore] = useState("");

    // État pour stocker le score bruts ajusté du joueur inscrits à l'événement en cours
    const [adjustedGrossScore, setAdjustedGrossScore] = useState("");

    // État pour stocker les points Fedex du joueur inscrits à l'événement en cours
    const [fedexPoints, setFedexPoints] = useState("");

    // États pour gérer les erreurs de validation des champs pour ajouter un joueur à un évenement
    const [selectedPlayerError, setSelectedPlayerError] = useState(false);
    const [grossScoreError, setGrossScoreError] = useState(false);
    const [adjustedGrossScoreError, setAdjustedGrossScoreError] = useState(false);
    const [selectedPositionError, setSelectedPositionError] = useState(false);
    const [fedexPointsError, setFedexPointsError] = useState(false);
   

    

     // État pour stocker les messages d'erreur en prévision de l'insertion du résultat de la ronde d'un joueur
     const [error, setError] = useState("");

     // État pour stocker un message de succès lors de l'insertion du résultat de la ronde
    const [successMessage, setSuccessMessage] = useState("");

    // ÉTat pour bloquer l'ajout en double de résultats de ronde pour un joueur en attendant la réponse de l'API
    const [loading, setLoading] = useState(false);


    // Variable pour stocker les données du joueur sélectionné dans le formulaire d'insertion des résultats de la ronde du joueur, 
    // pour pouvoir calculer son score net
    const selectedPlayerData = registeredPlayers.find(
        player => player.id === selectedPlayer
    );



    // Variable pour stocker le score net du joueur inscrits à l'événement en cours
    const netScore = grossScore && selectedPlayerData
        ? Number(grossScore) - 72 - Number(selectedPlayerData.handicap_rounded)
        : "";

    // Bout de code pour retirer le joueur sélectionné de la liste des joueurs inscrits à l'événement en cours, 
    // après l'insertion de son résultat de ronde, pour éviter d'insérer plusieurs résultats de ronde pour le même joueur
    setRegisteredPlayers(prev =>
        prev.filter(player =>
            player.id !== Number(selectedPlayer)
        )
    );

    // Bout de code pour retirer la position du joueur sélectionné de la liste des positions des joueurs inscrits
    //  à l'événement en cours, après l'insertion de son résultat de ronde, pour éviter d'insérer plusieurs résultats avec la même position
    setAvailablePositions(prev =>
        prev.filter(position =>
            position !== Number(selectedPosition)
        )
    );


    // Fonction pour charger les détails de l'évenement en cours pour récupérer son id
    const loadEvent = async () => {

        // Juste au cas ou, on va setter le loading à true pour éviter que l'utilisateur puisse cliquer plusieurs fois sur le bouton d'ajout d'un joueur à un évenement
        setLoading(true);

        try {
            const response = await fetch(`${API_BASE_URL}/admin/get-next-event.php`,
                {
                    credentials: "include"
                }
            );

            // Si la réponse de l'API indique que la session est invalide, 
            // rediriger le gestionnaire vers la page de connexion
            if (response.status === 401) {

                setError("Votre session a expiré, vous allez être redirigé vers la page de connexion.");
                setTimeout(() => {navigate("/league-monteregie/admin");}, 3000);
                return;
            }

            // Sinon, on a un résultat valide du retour de l'API
            const data = await response.json();

            if (data.success) {

                // Stocker les détails du prochain évenement qui sera en cours dans l'état nextEvent
                setEvent(data.event);

                // On retourne le résultat pour pouvoir l'utiliser dans la fonction d'initialisation des données du useEffect, 
                // pour ensuite charger la liste des joueurs disponibles pour l'ajout à cet évenement
                return data.event;
            } else {

                // Erreur lors du chargement de l'évenement
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement de l'événement.");

        } finally {

            setLoading(false);
        }        
    };









    // Fonction pour récupérer les joueurs inscrits à l'événement en cours et qui n'ont pas encore de résultat 
    // de ronde pour cet événement, pour pouvoir insérer les résultats de ces joueurs
    const loadRegisteredPlayers = async (eventId) => {

        try {
            const response = await fetch(`${API_BASE_URL}/admin/get-registered-players.php?id=${eventId}`,
                {                    
                    credentials: "include"
                }
            );
                
            // Si la réponse de l'API indique que la session est invalide, 
            // rediriger le gestionnaire vers la page de connexion
            if (response.status === 401) {

                setError("Votre session a expiré, vous allez être redirigé vers la page de connexion.");
                setTimeout(() => {navigate("/league-monteregie/admin");}, 3000);
                return;
            }

            // Sinon, on a un résultat valide du retour de l'API
            const data = await response.json();
            if (data.success) {

                // Stockage de la liste des joueurs inscrits à l'événement en cours pour pouvoir insérer les résultats de ces joueurs
                // Ces joueurs seront disponible via un SELECT dans le formulaire d'insertion des résultats de la ronde du joueur
                setRegisteredPlayers(data.players);                
            } else {

                // Erreur lors du chargement des joueurs inscrits à l'événement
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement des joueurs inscrits à l'événement.");

        } finally {

            setLoading(false);
        }
    }







    
       // Fonction pour réinitialiser les champs du formulaire pour ajouter un résultat de ronde d'un joueur et les messages d'erreur associés
        const handleReset = () => {

            // Remise à l'état initial des champs du formulaire pour ajouter un résultat de ronde d'un joueur
            setSelectedPlayer("");
            setGrossScore("");
            setAdjustedGrossScore("");            
            setSelectedPosition("");            
            setFedexPoints("");
            
            
            // Remise à l'état initial du trigger pour remettre les bordures dans leur état normal
            setSelectedPlayerError(false);
            setGrossScoreError(false); 
            setAdjustedGrossScoreError(false);
            setSelectedPositionError(false);
            setFedexPointsError(false);

            // Remise à l'état initial du message d'erreur
            setError("");

            // Remise à l'état initial du message de succès
            setSuccessMessage("");
        };    
















    useEffect(() => {

        // Il faut utiliser une notion asynchrone pour charger les données, en raison de l'utilisation 
        const initializeData = async () => {

            // On doit d'abord charger les informations de l'évenement en cours afin d'afficher la liste des joueurs pour leur assigner leur résultat de ronde
            const event = await loadEvent();

            // Ensuite, si on a un évenement qui est en cours, on peut charger la liste des joueurs disponibles pour l'ajout à cet évenement
            if (event) {

                // Car cette fonction dépend de l'id, pas id pas de fonction 
                await loadRegisteredPlayers(event.id);
            }                   
        };
        
        // Charger tout les éléments dans la section du tournois en gestion en cours
        initializeData();

    }, []);














    return (
        <div className="admin-section-card">

            


            <select
                value={selectedPlayer}
                onChange={(e) => setSelectedPlayer(e.target.value)}
            >
                <option value="">Sélectionner un joueur</option>

                {registeredPlayers.map(player => (
                    <option
                        key={player.id}
                        value={player.id}
                    >
                        {player.firstname} {player.lastname}
                    </option>
                ))}
            </select>

        </div>
    );
}

export default ResultsSection;