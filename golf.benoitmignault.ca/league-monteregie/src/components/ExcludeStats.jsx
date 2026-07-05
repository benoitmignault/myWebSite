import { useEffect } from "react";
import { useNavigate } from "react-router-dom";

/**
 * Ce composant est utilisé pour exclure les statistiques de l'utilisateur en définissant un cookie "exclude_stats" à true.
 * 
 * @returns 
 */
function ExcludeStats() {

    const navigate = useNavigate();

    useEffect(() => {

        document.cookie = "exclude_stats=true; path=/; max-age=31536000; SameSite=Lax";
        navigate("/league-monteregie/");

    }, [navigate]);

    return (
        <p>Configuration en cours...</p>
    );
}

export default ExcludeStats;