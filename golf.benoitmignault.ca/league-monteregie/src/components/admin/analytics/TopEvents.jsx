import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { LuLoaderCircle, LuTriangleAlert, LuInfo } from "react-icons/lu";
import { API_BASE_URL } from "../../../config";

/**
 * Composant pour afficher les événements les plus consultés sur le site web, 
 * en fonction de la période sélectionnée.
 * 
 * @description
 * Ce composant récupère les données des événements les plus consultés 
 * depuis l'API en fonction de la période sélectionnée
 * 
 * @param {string} selectedPeriod - La période actuellement sélectionnée pour filtrer les données.
 * @returns {JSX.Element} - Le composant TopEvents.
 */
function TopEvents({ selectedPeriod }) {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // État pour stocker les événements les plus consultés
    const [events, setEvents] = useState(null);

    // État pour stocker les messages d'erreur
    const [error, setError] = useState("");

    // Fonction pour charger les événements les plus consultés depuis l'API en fonction de la période sélectionnée
    const loadTopEventsData = async () => {

        // Réinitialiser l'état d'erreur avant de charger les données
        setError("");
        setEvents(null);

        try {
            const response = await fetch(
                `${API_BASE_URL}/admin/analytics/get-top-events.php?period=${selectedPeriod}`,
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

                // Mettre à jour l'état avec les événements les plus consultés reçus de l'API
                setEvents(data.top_events);

            } else {

                // Si l'API retourne un succès false, afficher le message d'erreur retourné par l'API
                setError(data.message);
                setEvents(null);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors du chargement des événements les plus consultés.");
        };
    };

    // Utiliser useEffect pour charger les événements les plus consultés chaque fois que la période sélectionnée change
    useEffect(() => {

        // Fonction pour initialiser le chargement des données des événements les plus consultés
        const initializeData = async () => {

            // Appeler la fonction pour charger les événements les plus consultés depuis l'API
            await loadTopEventsData();
        };

        // Appeler la fonction d'initialisation pour charger les données des événements les plus consultés
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
    if (!events) {
        return (
            <div className="topevents-loading">
                Chargement...
            </div>
        );
    }

    return (
        <div className="topevents-container">
            <h2>
                Événements les plus consultés
            </h2>
            <table className="topevents-table">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Nom</th>
                        <th>Nombre de consultations</th>
                    </tr>
                </thead>
                <tbody>
                    {events.map((event, index) => (
                        <tr key={index}>
                            <td>{index + 1}</td>
                            <td>{event.name}</td>
                            <td>{event.clicks}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export default TopEvents;