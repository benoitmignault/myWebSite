import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { FaHouse } from "react-icons/fa6";
import { MdLogout } from "react-icons/md";
import { FaArrowUp } from "react-icons/fa";
import { BsCameraFill } from "react-icons/bs";
import { MdAdminPanelSettings } from "react-icons/md";
import { LuChartColumnIncreasing } from "react-icons/lu";
import PeriodSelector from "../PeriodSelector";
import Summary from "./Summary";
import Media from "./Media";


import { API_BASE_URL } from "../../../../config";
import Footer from "../../../Footer";

import '../../../../css/admin.css';
import "../../../../css/analytics.css";

/**
 * Composant pour afficher les statistiques d'activité des partenaires du site web,
 * avec la possibilité de sélectionner une période pour filtrer les données.
 * 
 * @returns {JSX.Element} - Le composant Analytics.
 */
function SponsorAnalytics() {

    const navigate = useNavigate();

    // Un état pour gérer la période sélectionnée pour l'affichage des statistiques du site
    const [selectedPeriod, setSelectedPeriod] = useState("all");

    // État pour détecter si l'utilisateur est sur un appareil mobile ou non
	const [isMobile, setIsMobile] = useState(window.innerWidth <= 768);

	// Utiliser useEffect pour mettre à jour l'état isMobile lorsque la taille de la fenêtre change
	const photoCredit = isMobile
		? "Photo prise au Club de golf Parcours du Vieux Village — The Masters"
		: "Photo prise au Club de golf Farnham — Semaine 2";

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

    // Avant de loader la page de statistiques des sponsors du site, on doit vérifier que l'administrateur est bien connecté
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
            document.title = "Analyse Partenaires - Golf Montérégie";

            // Changement du favicon de la page pour le dashboard
            const favicon = document.getElementById("dynamic-favicon");

            if (favicon) {
                
                // https://pngtree.com/freepng/log-file-document-icon_4229177.html
                favicon.href = `${import.meta.env.BASE_URL}favicon/favicon-log-activites.png`;
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
        <div className="sponsor-page">
            <div className="admin-navbar">
                <a href="#" className="admin-navbar-link"
                    onClick={(e) => { e.preventDefault(); handleLogout("/league-monteregie/"); }}
                >
                    <FaHouse /><span>Retour au site principal</span>
                </a>
                <a href="#" className="admin-navbar-link"
                    onClick={(e) => { e.preventDefault(); navigate("/league-monteregie/admin/analytics/traffic"); }}
                >
                    <LuChartColumnIncreasing /><span>Activité du site</span>
                </a>
                <a href="#" className="admin-navbar-link"
                    onClick={(e) => { e.preventDefault(); navigate("/league-monteregie/admin/dashboard"); }}
                >
                    <MdAdminPanelSettings /><span>Retour à la section admin</span>
                </a>
                <a href="#" className="admin-navbar-link"
                    onClick={(e) => { e.preventDefault(); handleLogout("/league-monteregie/admin/"); }}
                >
                    <MdLogout /><span>Déconnexion</span>
                </a>
            </div>
            <div className="dashboard-container">

                <h1 className="gestion-title">Activité des partenaires</h1>
                <PeriodSelector selectedPeriod={selectedPeriod} setSelectedPeriod={setSelectedPeriod} />
                <Summary selectedPeriod={selectedPeriod} />
                <Media selectedPeriod={selectedPeriod} />




                
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
};

export default SponsorAnalytics;