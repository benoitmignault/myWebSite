import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { MdLogout } from "react-icons/md";
import { FaHouse } from "react-icons/fa6";
import { BsCameraFill } from "react-icons/bs";
import { FaArrowUp } from "react-icons/fa";
import PlayersSection from "./PlayersSection";
import EventsSection from "./EventsSection";
import EventsPlanningSection from "./EventsPlanningSection";
import ResultsSection from "./ResultsSection";
import { API_BASE_URL } from "../../config";
import Footer from "../Footer";
import '../../css/admin.css'

// Composant du dashboard pour les administrateurs et les sous-administrateurs
// Ce composant affiche les différentes sections du dashboard, 
// comme la gestion des joueurs, des événements et des résultats.
function Dashboard() {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // Un état de rafraîchissement pour forcer le rechargement du formulaire de résultats mais surtout l'affichage des joueurs inscrits à l'événement en cours, 
    // après l'ajout d'un joueur à un événement ou après la création d'un nouvel événement
    const [eventChanged, setEventChanged] = useState(false);

    // Un état de rafraîchissement pour forcer le blocage du formulaire dans EventsPlanningSection quand on saisi des résultats pour un événement en cours
    const [eventUpdated, setEventUpdated] = useState(false);

    // Fonction pour gérer la déconnexion de l'administrateur et avec une redirection en fonction du lien qu'on a cliqué
    const handleLogout = async (redirectTo) => {

        try {
            await fetch(`${API_BASE_URL}/admin/auth/logout.php`,
                {
                    method: "POST",
                    credentials: "include"
                }
            );

        } finally {

            // Rediriger l'utilisateur vers la page de connexion après la déconnexion, 
            // ou vers la page d'accueil s'il a cliqué sur le lien de la maison
            navigate(redirectTo);
        }
    };

    // Avant de loader la page du dashboard, on doit vérifier que l'administrateur est bien connecté 
    // en vérifiant la session avec l'API check-session.php.
    useEffect(() => {
        fetch(`${API_BASE_URL}/admin/auth/check-session.php`, {credentials: "include"})
        /**
         * response : status, HTTP, headers, ok, etc.
         * data : le corps de la réponse, qui est un objet JSON contenant les données retournées par l'API,
         * généralement avec une structure comme { success: boolean, message: string, ... }
         */
        .then((response) => {

            if (!response.ok) {
                throw new Error("Session invalide");
            }

            // Changement du titre de la page lorsque le composant du dashboard est monté
            document.title = "Dashboard - Golf Montérégie";

            // Changement du favicon de la page pour le dashboard
            const favicon = document.getElementById("dynamic-favicon");

            if (favicon) {
                favicon.href = "/league-monteregie/favicon/favicon-admin-ChatGPT.png";
            }
        })        
        .catch(() => {
            // Si la session n'est pas valide, rediriger l'utilisateur vers la page de connexion
            navigate("/league-monteregie/admin");
        });

    }, [navigate]);

    return (
        <div>
            <div className="admin-navbar">
                <a href="#" className="admin-navbar-link"
                    onClick={(e) => { e.preventDefault(); handleLogout("/league-monteregie/");}}
                >
                    <FaHouse />
                    <span>Retour au site principal</span>
                </a>

                <a href="#" className="admin-navbar-link"
                    onClick={(e) => {e.preventDefault(); handleLogout("/league-monteregie/admin/");}}
                >
                    <MdLogout />
                    <span>Déconnexion</span>
                </a>
            </div>
            <div className="dashboard-container">
                <h1 className="gestion-title">Gestion de la Ligue de Golf Montérégie</h1>                
                <ResultsSection eventChanged={eventChanged} setEventUpdated={setEventUpdated}/>
                <EventsPlanningSection eventUpdated={eventUpdated} setEventChanged={setEventChanged} />                
                <PlayersSection />
                <EventsSection />
            </div>
            <div className="admin-photo-credit">
                <BsCameraFill />
                <span>Photo prise au Club de golf Farnham — Semaine 2</span>
            </div>
            <button className="scroll-top dashboard" onClick={() => window.scrollTo({top: 0, behavior: "smooth"})}> 
                <FaArrowUp />
            </button>
            <Footer />
        </div>
    );
}

export default Dashboard;