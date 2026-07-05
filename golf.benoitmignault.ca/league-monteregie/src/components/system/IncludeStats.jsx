import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { includeStats } from "../../utils/logging";
import '../../css/index.css'

/**
 * Ce composant est utilisé pour réinclure les statistiques de l'utilisateur en supprimant l'indicateur "exclude_stats" du localStorage.
 *
 * @returns {JSX.Element}
 */
function IncludeStats() {    

    const navigate = useNavigate();

    useEffect(() => {

        // Inclure définitivement ce navigateur dans les statistiques
        includeStats();

        // Redirection après 2 secondes
        const timer = setTimeout(() => {navigate("/league-monteregie/");}, 2000);
        
        return () => clearTimeout(timer);

    }, [navigate]);

    return (
        <div className="message-page">
            <h2 className="message-success">✅ Statistiques activées</h2>
            <p>Ce navigateur sera désormais comptabilisé dans les statistiques de fréquentation.</p>
        </div>
    );
}

export default IncludeStats;