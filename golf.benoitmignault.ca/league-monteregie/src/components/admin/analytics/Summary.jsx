import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { LuHouse, LuEye, LuUser, LuCalendarDays, LuChartLine } from "react-icons/lu";
import { API_BASE_URL } from "../../../config";


function Summary({ selectedPeriod }) {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();
    
    // État pour stocker les données de résumé récupérées depuis l'API
    const [summary, setSummary] = useState(null);

    // État pour stocker les messages d'erreur
    const [error, setError] = useState("");




    // Fonction pour charger les données de résumé depuis l'API en fonction de la période sélectionnée
    const loadSummaryData = async () => {

        try {
            const response = await fetch(`${API_BASE_URL}/admin/analytics/get-summary.php?period=${selectedPeriod}`,                
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
                setSummary(data.summary); 
            } else {

                // Si l'API retourne un succès false, afficher le message d'erreur retourné par l'API
                setError(data.message);
                setSummary(null);
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

            // Elle appelle la fonction loadSummaryData pour récupérer les données de résumé depuis l'API
            await loadSummaryData();
        };

        // Appeler la fonction initializeData pour charger les données de résumé
        initializeData();
        
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [selectedPeriod]);
    
    // Si les données de résumé ne sont pas encore chargées, afficher un message de chargement
    if (!summary) {
        return (
            <div className="summary-loading">
                Chargement du sommaire...
            </div>
        );
    }

    return (
        <div className="summary-container">
            <div className="summary-card">
                <h2>
                    <LuHouse className="summary-title-icon" />
                    Accueil
                </h2>
                <div className="summary-stats">
                    <div className="summary-stat">
                        <LuEye className="summary-icon" />
                        <span className="summary-value">{summary.pageLoad}</span>
                        <span className="summary-label">Visites</span>
                    </div>
                    <div className="summary-stat">
                        <LuUser className="summary-icon" />
                        <span className="summary-value">{summary.playerClick}</span>
                        <span className="summary-label">Joueurs consultés</span>
                    </div>
                    <div className="summary-stat">
                        <LuCalendarDays className="summary-icon" />
                        <span className="summary-value">{summary.eventClick}</span>
                        <span className="summary-label">Événements consultés</span>
                    </div>
                </div>
            </div>

            <div className="summary-card">
                <h2>
                    <LuChartLine className="summary-title-icon" />
                    Évolution des joueurs
                </h2>
                <div className="summary-stats">
                    <div className="summary-stat">
                        <LuEye className="summary-icon" />
                        <span className="summary-value">{summary.pageStatsLoad}</span>
                        <span className="summary-label">Visites</span>
                    </div>
                    <div className="summary-stat">
                        <LuUser className="summary-icon" />
                        <span className="summary-value">{summary.playerStatsView}</span>
                        <span className="summary-label">Joueurs consultés</span>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Summary;