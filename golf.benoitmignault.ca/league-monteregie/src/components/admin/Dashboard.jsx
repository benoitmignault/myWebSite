import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { API_BASE_URL } from "../../config";
import "./admin.css";

function Dashboard() {

    // Utilisation de useNavigate pour rediriger l'utilisateur après une connexion réussie
    const navigate = useNavigate();

    // Avant de loader la page du dashboard, on doit vérifier que l'administrateur est bien connecté 
    // en vérifiant la session avec l'API check-session.php.
    useEffect(() => {
                
        // le terme include est nécessaire pour que les cookies de session soient envoyés avec la requête
        fetch(`${API_BASE_URL}/admin/check-session.php`, {credentials: "include"}).then(response => {

            if (!response.ok) {
                navigate("/league-monteregie/admin");
            } else {
                // Changement du titre de la page lorsque le composant de connexion est monté
                document.title = "Dashboard - Golf Montérégie";

                // Changement du favicon de la page pour le logo de ChatGPT lorsque le composant de connexion est monté
                const favicon = document.getElementById("dynamic-favicon");
                
                // On doit partir de /league-monteregie car c'est la vraie racine du projet
                if (favicon) {
                    favicon.href = "/league-monteregie/favicon/favicon-admin-ChatGPT.png";
                }
            }
        });

    }, [navigate]);

    return (
        <div>
            Dashboard
        </div>
    );
}

export default Dashboard;