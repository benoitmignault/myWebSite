import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { excludeStats } from "../../utils/logging";
import '../../css/index.css'

/**
 * Ce composant est utilisé pour exclure les statistiques de l'utilisateur en enregistrant l'indicateur "exclude_stats" dans le localStorage.
 *
 * @returns {JSX.Element}
 */
function ExcludeStats() {    

    const navigate = useNavigate();

    useEffect(() => {

        // Exclure définitivement ce navigateur des statistiques
        excludeStats();

        // Redirection après 2 secondes
        const timer = setTimeout(() => {navigate("/league-monteregie/");}, 2000);

        return () => clearTimeout(timer);

    }, [navigate]);

    return (
        <div className="message-page">
            <h2 className="message-error">❌ Statistiques désactivées</h2>
            <p>Ce navigateur ne sera plus comptabilisé dans les statistiques de fréquentation.</p>
        </div>
    );
}

export default ExcludeStats;