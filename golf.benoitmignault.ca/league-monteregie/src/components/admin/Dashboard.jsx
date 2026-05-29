import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { API_BASE_URL } from "../../config";
import "./admin.css";

function Dashboard() {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // Fonction pour gérer la déconnexion de l'administrateur et avec une redirection en fonction du lien qu'on a cliqué
    const handleLogout = async (redirectTo) => {

        try {

            await fetch(`${API_BASE_URL}/admin/logout.php`,
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
        fetch(`${API_BASE_URL}/admin/check-session.php`, {credentials: "include"})
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
            Dashboard
            <button className="admin-button" onClick={handleLogout}>
                Déconnexion
            </button>
        </div>
    );
}

export default Dashboard;