import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { LuLoaderCircle, LuTriangleAlert, LuInfo } from "react-icons/lu";
import { API_BASE_URL } from "../../../config";

/**
 * Composant pour afficher les joueurs les plus consultés sur le site web, 
 * en fonction de la période sélectionnée.
 * 
 * @description
 * Ce composant récupère les données des joueurs les plus consultés 
 * depuis l'API en fonction de la période sélectionnée
 * 
 * @param {string} selectedPeriod - La période actuellement sélectionnée pour filtrer les données.
 * @returns {JSX.Element} - Le composant TopPlayers.
 */
function TopPlayers({ selectedPeriod }) {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // État pour stocker les joueurs les plus consultés
    const [players, setPlayers] = useState(null);

    // État pour stocker les messages d'erreur
    const [error, setError] = useState("");    

    // Fonction pour charger les joueurs les plus consultés depuis l'API en fonction de la période sélectionnée
    const loadTopPlayersData = async () => {

        // Réinitialiser l'état d'erreur avant de charger les données
        setError("");
        setPlayers(null);

        try {
            const response = await fetch(
                `${API_BASE_URL}/admin/analytics/get-top-players.php?period=${selectedPeriod}`,
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

                // Mettre à jour l'état avec les joueurs les plus consultés reçus de l'API
                setPlayers(data.top_players);

            } else {

                // Si l'API retourne un succès false, afficher le message d'erreur retourné par l'API
                setError(data.message);
                setPlayers(null);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement des joueurs les plus consultés.");
        };
    };

    // Utiliser useEffect pour charger les joueurs les plus consultés chaque fois que la période sélectionnée change
    useEffect(() => {

        // Fonction pour initialiser le chargement des données des joueurs les plus consultés
        const initializeData = async () => {

            // Appeler la fonction pour charger les joueurs les plus consultés depuis l'API
            await loadTopPlayersData();
        };

        // Appeler la fonction d'initialisation pour charger les données des joueurs les plus consultés
        initializeData();

    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [selectedPeriod]);

    // Si une erreur est survenue lors du chargement des données, afficher le message d'erreur
    if (error) {
        return (
            <div className="topplayers-error">
                <LuTriangleAlert />
                <p>{error}</p>
            </div>
        );
    }

    // Si les données ne sont pas encore chargées, afficher un message de chargement
    if (!players) {
        return (
            <div className="topplayers-loading">
              <LuLoaderCircle />
              <p>Chargement...</p>
            </div>
        );
    }

    //  Si les données sont chargées mais qu'il n'y a aucun joueur, afficher un message indiquant qu'aucun joueur n'a été consulté
    if (players.length === 0) {
        return (
            <div className="topplayers-empty">
                <LuInfo />
                <p>Aucun joueur n'a été consulté durant cette période.</p>
            </div>
        );
    }

    return (
        <div className="topplayers-container">
            <h2>Joueurs les plus consultés</h2>
            <table>
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Joueur</th>
                        <th>Nb clicks</th>
                    </tr>
                </thead>
                <tbody>
                    {players.map((player, index) => (
                        <tr key={index}>
                            <td>{index + 1}</td>
                            <td>{player.fullName}</td>
                            <td>{player.clicks}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export default TopPlayers;