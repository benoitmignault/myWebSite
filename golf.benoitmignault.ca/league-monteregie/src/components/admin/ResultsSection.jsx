import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { FaInfoCircle } from "react-icons/fa";
import { FaLock } from "react-icons/fa";
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
 * @param {boolean} eventChanged - Un trigger pour permettre de rafraîchir les données de la section des résultats après une modification dans la section de planification des événements, 
 * pour ainsi permettre d'avoir les données à jour dans la section des résultats sans avoir à faire un rafraîchissement manuel de la page
 * @param {boolean} setEventUpdated - Une fonction pour mettre à jour l'état de l'événement mis à jour
 * @returns
 */
function ResultsSection({eventChanged, setRefreshPlanning}) {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // État pour stocker l'évent en cours pour lequel on veut insérer les résultats
    const [event, setEvent] = useState(null);

    // État pour stocker la liste des joueurs inscrits à l'événement en cours pour pouvoir insérer les résultats de chacun des joueurs
    const [registeredPlayers, setRegisteredPlayers] = useState([]);

    // ÉTat pour stocker le ID du joueur sélectionné dans le formulaire d'insertion des résultats de la ronde du joueur
    const [selectedPlayer, setSelectedPlayer] = useState("");

    // Variable pour stocker les informations du joueur sélectionné
    // À chaque fois que le selectedPlayer change, on va mettre à jour cette variable pour stocker les informations du joueur sélectionné, 
    // notamment pour trouver son handicap arrondi, qui va être utilisé pour calculer le score net du joueur
    const selectedPlayerData = registeredPlayers.find(
        player => player.id === Number(selectedPlayer)
    );

    // ÉTat pour stocker le score bruts du joueur inscrits à l'événement en cours
    // Cet état doit être avant la variable du score net, car le score net est calculé à partir du score brut et du handicap arrondi du joueur
    const [grossScore, setGrossScore] = useState("");

    // Variable pour stocker le score net du joueur inscrits à l'événement en cours, 
    // qui est calculé à partir du score brut et du handicap arrondi du joueur
    const displayedNetScore =
    grossScore !== "" && selectedPlayerData
        ? Number(grossScore) - 72 - Number(selectedPlayerData.handicap_rounded)
        : "";

    // État pour stocker la liste des positions des joueurs inscrits à l'événement en cours
    const [availablePositions, setAvailablePositions] = useState([]);

    // État pour stocker la position du joueur sélectionné dans le formulaire d'insertion des résultats de la ronde du joueur
    const [selectedPosition, setSelectedPosition] = useState("");    

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

                // Situation particuliere si on n'a pas d'évenement à préparer,
                // dans ce cas on retourne null pour éviter les erreurs de rendu conditionnel du composant
                if (!data.event) {
                    setEvent(null);
                    return null;
                }

                // RÉorganisation pour convertir des string en number pour is_updated et is_open,
                // pour éviter les problèmes de comparaison dans le rendu conditionnel du composant
                data.event.is_updated = Number(data.event.is_updated);
                data.event.is_open = Number(data.event.is_open);
                
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
                
                return data.players;
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

    // Fonction pour récupérer les positions déjà utilisées qu'on va éliminer de la liste des positions disponible
    // Un nom de fonction un peu contradictoire mais au fini, on aura les positions disponibles dans le menu SELECT
    const loadAvailablePositions = async (eventId) => {

        try {
            const response = await fetch(`${API_BASE_URL}/admin/get-available-positions.php?id=${eventId}`,
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
                
                // On va stocker la liste de positions disponible via le résultat de l'API, car la liste des disponibles à été traiter direct en back-end 
                // pour éliminer les positions déjà utilisées, pour éviter d'avoir à faire ce travail en front-end
                setAvailablePositions(data.availablePositions);
            } else {

                // Erreur lors du chargement des positions utilisées
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement des joueurs inscrits à l'événement.");

        } finally {

            setLoading(false);
        }
    }

    // Fonction pour ajouter le résultat de la ronde d'un joueur à un évenement
    const handleAddResult = async () => {

        // Réinitialiser les messages d'erreur avant de commencer le processus d'ajout
        setError("");

        // On commencer par gérer les erreurs de validation côté client 
        // avant même d'envoyer la requête à l'API, pour éviter les appels inutiles à l'API et 
        // améliorer l'expérience utilisateur.

        // TODO: Changer error pour une liste de msg erreur pour pouvoir afficher plusieurs erreurs à la fois, 
        // au lieu de n'afficher que la première erreur rencontrée.

        if (!event) {

            setError("Aucun événement actif.");
            return;
        }
        
        // Validation des champs du formulaire d'insertion des résultats de la ronde du joueur
        let hasError = false;

        /**
         * Information utile : !selectedPlaye ->
         *  selectedPlayer === ""
            selectedPlayer === null
            selectedPlayer === undefined
            selectedPlayer === 0
            selectedPlayer === false
         */
        if (!selectedPlayer || !selectedPosition || grossScore === "" || adjustedGrossScore === "" || fedexPoints === "") {
            setSelectedPlayerError(true);
            hasError = true;
        }

        if (hasError) {
            setError("Veuillez remplir tous les champs obligatoires.");
            return;
        }

        if (Number(grossScore) <= 0) {

            setGrossScoreError(true);
            setError("Le score brut doit être un nombre positif.");
            return;
        }

        if (Number(adjustedGrossScore) <= 0) {

            setAdjustedGrossScoreError(true);
            setError("Le score brut ajusté doit être un nombre positif.");
            return;
        }

        // Un joueur va toujours avoir des points FedEx même si il fini bon dernier
        if (Number(fedexPoints) <= 0) {

            setFedexPointsError(true);
            setError("Les points FedEx doivent être un nombre positif.");
            return;
        }
        
        // Une validation improtante d'un sens logique
        if (Number(adjustedGrossScore) > Number(grossScore)) {

            setError("Le score brut ajusté ne peut pas être supérieur au score brut.");
            return;
        }

        // Associer le nom du joueur à une variable pour l'afficher dans le message de succès après l'ajout du joueur à l'évenement
        const playerName = `${selectedPlayerData.firstname} ${selectedPlayerData.lastname}`;

        // Variable pour stocker le score net du joueur inscrits à l'événement en cours via le résultat précalculé dans displayedNetScore
        const netScore = displayedNetScore;

        // Si on passe les validations côté client, on peut alors procéder à l'appel de l'API 
        // pour ajouter le joueur à la ligue
        setLoading(true);
        
        try {
            const response = await fetch(`${API_BASE_URL}/admin/add-player-result.php`,
                {
                    method: "POST",
                    credentials: "include",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({
                        eventId: event.id,
                        playerId: selectedPlayer,
                        position: selectedPosition,
                        grossScore,
                        adjustedGrossScore,
                        netScore,
                        fedexPoints
                    })
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

                // Affichage d'un message de succès pour informer l'administrateur que le joueur a été ajouté avec succès
                setSuccessMessage(`Le résultat pour le joueur ${playerName} a été ajouté à la liste des résultats de l'événement.`);

                const players = await loadRegisteredPlayers(event.id);

                // Si nous arrivons à une valeur de 0 résultat, on va devoir reloader l'évent et voir que nous sommes rendu au prochain évenement, 
                // pour ainsi afficher le message d'aucun évenement n'est ouvert pour la saisie des résultats, et cacher le formulaire d'ajout de résultat de ronde
                if (players && players.length === 0) {

                    await loadEvent(event.id);
                } else {

                    await loadAvailablePositions(event.id);                   
                }          
                
                // Informer Dashboard qu'un changement important vient d'avoir lieu
                setRefreshPlanning(prev => !prev);

                // Réinitialiser les champs du formulaire pour ajouter un résultat de ronde d'un joueur et les messages d'erreur associés
                setTimeout(() => {handleReset();}, 3000);    
            } else {

                // Erreur lors de l'ajout du joueur
                setError(data.message);

                // Réinitialiser les champs du formulaire pour ajouter un résultat de ronde d'un joueur et les messages d'erreur associés
                setTimeout(() => {handleReset();}, 3000);  
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors de la tentative d'ajouter un résultat.");
            // Réinitialiser les champs du formulaire pour ajouter un résultat de ronde d'un joueur et les messages d'erreur associés
            setTimeout(() => {handleReset();}, 3000);  
        } finally {

            setLoading(false);
        }
    };
    
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
            // Ajout de la notion d eis_open pour éviter de caller ces fonctions si le nouvel event est vide équipe et de players
            if (event && event.is_open === 1) {

                // Je confirme que ça marche si is_update est à 1
                await loadRegisteredPlayers(event.id);
                await loadAvailablePositions(event.id);
            }
        };
        
        // Charger tout les éléments dans la section du tournois en gestion en cours
        initializeData();

        // Permet de faire la détection d'un changement dans le trigger de rafraîchissement 
        // pour recharger les données de la section des résultats, dans la section où on ouvre un évement en ajoutant des joueurs à cet évenement, 
        // et ainsi permettre d'avoir les données à jour dans la section des résultats sans avoir à faire un rafraîchissement manuel de la page
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [eventChanged]);

    return (
        <div className="admin-section-card">
            <h2>Section pour ajouter un résultat</h2>
            {!event ? (
                <div className="warning-message">
                    <FaInfoCircle />
                    <span>Aucun événement n'est actuellement ouvert pour la saisie des résultats.</span>
                </div>
            ) : (
                <>
                    {event.is_open === 0 ? (
                        <div className="warning-message">
                            <FaLock />
                            <span>L'événement n'est pas encore ouvert, donc on ne peut pas ajouter de résultats.</span>
                        </div>
                    ) : (
                        <>
                            <form onSubmit={(e) => {e.preventDefault(); handleAddResult();}}>
                                <div className="admin-row">
                                    <div className="admin-form-group">
                                        <label className="admin-label">
                                            Joueur
                                            <span className="required-field">*</span>
                                        </label>                    
                                        <select className={`admin-input players ${selectedPlayerError ? "input-error" : ""}`}
                                            value={selectedPlayer}
                                            onChange={(e) => {setSelectedPlayer(e.target.value); setSelectedPlayerError(false); setError("");}}                       
                                        >
                                            <option value="">Sélectionner un joueur</option>
                                            {registeredPlayers.map(player => (
                                                <option key={player.id} value={player.id}>{player.firstname} {player.lastname}</option>
                                            ))}
                                        </select>
                                        {selectedPlayerData && (
                                            <p className="admin-section-description handicap">Handicap utilisé : {selectedPlayerData.handicap_rounded}</p>
                                        )}
                                    </div>
                                    <div className="admin-form-group">
                                        <label className="admin-label">
                                            Score brut
                                            <span className="required-field">*</span>
                                        </label>
                                        <input                            
                                            className={`admin-input result ${grossScoreError ? "input-error" : ""}`}
                                            type="number" value={grossScore}
                                            onChange={(e) => {setGrossScore(e.target.value); setGrossScoreError(false); setError("");}}
                                        />
                                    </div>
                                    <div className="admin-form-group">
                                        <label className="admin-label">
                                            Score net
                                        </label>
                                        <div className="admin-net-score">
                                            {
                                                displayedNetScore === ""
                                                    ? ""
                                                    : displayedNetScore === 0
                                                        ? "E"
                                                        : displayedNetScore > 0
                                                            ? `+${displayedNetScore}`
                                                            : displayedNetScore
                                            }
                                        </div>
                                    </div>
                                    <div className="admin-form-group">
                                        <label className="admin-label">
                                            Score brut ajusté
                                            <span className="required-field">*</span>
                                        </label>
                                        <input                            
                                            className={`admin-input result ${adjustedGrossScoreError ? "input-error" : ""}`}
                                            type="number" value={adjustedGrossScore}
                                            onChange={(e) => {setAdjustedGrossScore(e.target.value); setAdjustedGrossScoreError(false); setError("");}}
                                        />
                                    </div> 
                                </div>                   
                                <div className="admin-row">
                                    <div className="admin-form-group">
                                        <label className="admin-label">
                                            Position
                                            <span className="required-field">*</span>
                                        </label>
                                        <select
                                            className={`admin-input ${selectedPositionError ? "input-error" : ""}`}
                                            value={selectedPosition}
                                            onChange={(e) => {setSelectedPosition(e.target.value); setSelectedPositionError(false); setError("");}}
                                        >
                                            <option value="">Sélectionner une position</option>
                                            {availablePositions.map(position => (
                                                <option key={position} value={position}>{position}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="admin-form-group">
                                        <label className="admin-label">
                                            Points FedEx
                                            <span className="required-field">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            className={`admin-input ${fedexPointsError ? "input-error" : ""}`}
                                            value={fedexPoints}
                                            onChange={(e) => {setFedexPoints(e.target.value); setFedexPointsError(false); setError("");}}
                                        />
                                    </div>
                                </div>
                                <div className="admin-actions">
                                    <button className="admin-button" type="submit" disabled={loading}>
                                        Ajouter
                                    </button>
                                    <button
                                        className="admin-button admin-button-secondary"
                                        type="button" onClick={handleReset}>
                                        Effacer
                                    </button>
                                </div>
                                {error && <p className="admin-error-message">✗ {error}</p>}
                                {successMessage && <p className="admin-success-message">✓ {successMessage}</p>}
                            </form>
                        </>
                    )}
                </>
            )}
        </div>
    );
}

export default ResultsSection;