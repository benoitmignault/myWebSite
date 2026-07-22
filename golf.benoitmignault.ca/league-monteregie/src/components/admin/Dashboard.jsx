import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { MdLogout } from "react-icons/md";
import { FaHouse } from "react-icons/fa6";
import { BsCameraFill } from "react-icons/bs";
import { FaArrowUp } from "react-icons/fa";
import { LuChartColumnIncreasing } from "react-icons/lu";
import Players from "./management/Players";
import Events from "./management/Events";
import EventPlanning from "./management/EventPlanning";
import ResultsEvent from "./management/ResultsEvent";
import { API_BASE_URL } from "../../config";
import Footer from "../Footer";
import '../../css/admin.css'

// Composant du dashboard pour les administrateurs et les sous-administrateurs
// Ce composant affiche les différentes sections du dashboard, 
// comme la gestion des joueurs, des événements et des résultats.
function Dashboard() {

    // État pour détecter si l'utilisateur est sur un appareil mobile ou non
	const [isMobile, setIsMobile] = useState(window.innerWidth <= 768);

	// Utiliser useEffect pour mettre à jour l'état isMobile lorsque la taille de la fenêtre change
	const photoCredit = isMobile
		? "Photo prise au Club de golf Parcours du Vieux Village — The Masters"
		: "Photo prise au Club de golf Farnham — Semaine 2";

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // Un état de rafraîchissement pour forcer le rechargement du formulaire de résultats mais surtout l'affichage des joueurs inscrits à l'événement en cours, 
    // après l'ajout d'un joueur à un événement ou après la création d'un nouvel événement
    const [eventChanged, setEventChanged] = useState(false);

    // Un état de rafraîchissement pour avertir EventsPlanningSection qu'il y a eu des changements et qui doit reloader 
    // le composant pour afficher les changements dans la planification des événements, 
    // comme l'ajout d'un nouvel événement ou l'ajout d'un joueur.
    const [refreshPlanning, setRefreshPlanning] = useState(false);

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
                favicon.href = `${import.meta.env.BASE_URL}favicon/favicon-admin-ChatGPT.png`;
            }
        })        
        .catch(() => {
            // Si la session n'est pas valide, rediriger l'utilisateur vers la page de connexion
            navigate("/league-monteregie/admin");
        });

    }, [navigate]);

    // Utiliser useEffect pour Gestion du mode mobile
    useEffect(() => {
        const handleResize = () => {
            setIsMobile(window.innerWidth <= 768);
        };

        // Valeur initiale
        handleResize();

        window.addEventListener("resize", handleResize);

        return () => {window.removeEventListener("resize", handleResize);};
    }, []);

    return (
        <div>
            <div className="admin-navbar">
                <a href="#" className="admin-navbar-link"
                    onClick={(e) => { e.preventDefault(); handleLogout("/league-monteregie/");}}
                >
                    <FaHouse />
                    <span>Retour au site principal</span>
                </a>

                <a
                    href="#" className="admin-navbar-link"
                    onClick={(e) => { e.preventDefault(); navigate("/league-monteregie/admin/statistics"); }}
                >
                    <LuChartColumnIncreasing />
                    <span>Activité du site</span>
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
                <ResultsEvent eventChanged={eventChanged} setRefreshPlanning={setRefreshPlanning}/>
                <EventPlanning refreshPlanning={refreshPlanning} setEventChanged={setEventChanged} />                
                <Players setRefreshPlanning={setRefreshPlanning}/>
                <Events setRefreshPlanning={setRefreshPlanning}/>
            </div>            
            <button className="scroll-top dashboard" onClick={() => window.scrollTo({top: 0, behavior: "smooth"})}> 
                <FaArrowUp />
            </button>
            <div className="admin-photo-credit">
                <BsCameraFill />
                <span>{photoCredit}</span>
            </div>
            <Footer />
        </div>
    );
}

export default Dashboard;