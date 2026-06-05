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
    const [currentEvent, setCurrentEvent] = useState(null);

    // État pour stocker la liste des joueurs inscrits à l'événement en cours pour pouvoir insérer les résultats de chacun des joueurs
    const [registeredPlayers, setRegisteredPlayers] = useState([]);

    // ÉTat pour stocker le joueur sélectionné dans le formulaire d'insertion des résultats de la ronde du joueur
    const [selectedPlayer, setSelectedPlayer] = useState("");

    // État pour stocker la liste des positions des joueurs inscrits à l'événement en cours
    const [registeredPositions, setPositions] = useState([]);

    // État pour stocker la position du joueur sélectionné dans le formulaire d'insertion des résultats de la ronde du joueur
    const [position, setPosition] = useState("");

    // ÉTat pour stocker le score bruts du joueur inscrits à l'événement en cours
    const [grossScore, setGrossScore] = useState("");

    // État pour stocker le score bruts ajusté du joueur inscrits à l'événement en cours
    const [adjustedGrossScore, setAdjustedGrossScore] = useState("");

    // État pour stocker le score net du joueur inscrits à l'événement en cours
    const [netScore, setNetScore] = useState("");

    // État pour stocker les points Fedex du joueur inscrits à l'événement en cours
    const [fedexPoints, setFedexPoints] = useState("");

     // État pour stocker les messages d'erreur en prévision de l'insertion du résultat de la ronde d'un joueur
     const [error, setError] = useState("");

     // État pour stocker un message de succès lors de l'insertion du résultat de la ronde
    const [successMessage, setSuccessMessage] = useState("");



    






































    return (
        <div className="admin-section-card">

            

        </div>
    );
}

export default ResultsSection;