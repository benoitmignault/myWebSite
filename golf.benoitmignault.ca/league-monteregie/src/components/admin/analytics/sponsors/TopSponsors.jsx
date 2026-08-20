import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { LuTriangleAlert } from "react-icons/lu";
import { API_BASE_URL } from "../../../../config";

/**
 * Composant pour faire afficher le top des sponsors, en fonction de la période sélectionnée.
 * 
 * @description
 * Ce composant récupère les données de résumé depuis l'API en fonction de la période sélectionnée 
 * pour faire un affichage du top des sponsors.
 * 
 * @param {string} selectedPeriod - La période actuellement sélectionnée pour filtrer les données.
 * @returns {JSX.Element} - Le composant Summary.
 */
function TopSponsors({ selectedPeriod }) {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // État pour stocker les données de résumé récupérées depuis l'API
    const [sponsors, setSponsors] = useState(null);

    // État pour stocker les messages d'erreur
    const [error, setError] = useState("");

    // Fonction pour charger les données de résumé depuis l'API en fonction de la période sélectionnée
    const loadSponsorsData = async () => {

        // Réinitialiser l'état d'erreur avant de charger les données
        setError("");
        setSponsors(null);

        try {
            const response = await fetch(`${API_BASE_URL}/admin/analytics/sponsors/get-top-sponsors.php?period=${selectedPeriod}`,                
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

                // Mettre à jour l'état avec les données de résumé reçues de l'API
                setSponsors(data.topSponsors); 
            } else {

                // Si l'API retourne un succès false, afficher le message d'erreur retourné par l'API
                setError(data.message);
                setSponsors(null);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement des données de résumé. Veuillez réessayer plus tard.");
        }
    };

    // Utiliser useEffect pour charger les données de résumé chaque fois que la période sélectionnée change
    useEffect(() => {

        // Il faut utiliser une notion asynchrone pour charger les données, en raison de l'utilisation 
        const initializeData = async () => {

            // Elle appelle la fonction loadSponsorsData pour récupérer les données de résumé depuis l'API
            await loadSponsorsData();
        };

        // Appeler la fonction initializeData pour charger les données de résumé
        initializeData();
        
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [selectedPeriod]);

        // Si une erreur est survenue lors du chargement des données, afficher le message d'erreur
    if (error) {
        return (
            <div className="sponsor-error-container">
                <div className="sponsor-error">
                    <LuTriangleAlert className="sponsor-error-icon" />
                    <p>{error}</p>
                </div>
            </div>
        );
    }

    // Si les données de résumé ne sont pas encore chargées, afficher un message de chargement
    if (!sponsors) {
        return (
            <div className="sponsor-media-loading">
                <div className="sponsor-media-loading-content">
                    Chargement...
                </div>
            </div>
        );
    }

    return (
        <div className="sponsor-section top-sponsors">
            <h2 className="sponsor-section-subtitle">
                Partenaires les plus consultés
            </h2>
            <table className="top-sponsors-table">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Partenaire</th>
                        <th>Nb clics</th>
                    </tr>
                </thead>
                <tbody>
                    {sponsors.map((sponsor, index) => (
                        <tr key={index}>
                            <td>{index + 1}</td>
                            <td>{sponsor.sponsor_name}</td>
                            <td>{sponsor.clicks}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default TopSponsors;