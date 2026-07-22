/**
 * App.jsx
 *
 * Composant principal de l'application React qui gère la navigation entre les différentes pages de l'application.
 * Utilise React Router pour définir les routes et les composants correspondants.
 * 
 * Charge les composants de route à la demande (lazy loading) pour améliorer les performances et le temps de chargement initial.
 * On utilise React.Suspense pour afficher un fallback (chargement) pendant que le composant est en cours de chargement.
 * 
 * Les routes définies sont :
 * - /league-monteregie/ : Page d'accueil (HomePage)
 * - /league-monteregie/statistics : Page des statistiques des joueurs (PlayerStats)
 * - /league-monteregie/admin/ : Page de connexion pour l'administration (Login)
 * - /league-monteregie/admin/dashboard : Tableau de bord de l'administration (Dashboard)
 */
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { lazy, Suspense } from "react";

// Chargement immédiat
import Index from "./components/Index";

// Chargement à la demande
const ExcludeAnalytics = lazy(() => import("./components/settings/ExcludeAnalytics"));
const IncludeAnalytics = lazy(() => import("./components/settings/IncludeAnalytics"));
const PlayerEvolution = lazy(() => import("./components/player-evolution/Index"));
const Login = lazy(() => import("./components/admin/Login"));
const Dashboard = lazy(() => import("./components/admin/Dashboard"));
const Analytics = lazy(() => import("./components/admin/Trafic"));


function App() {

    return (
        <BrowserRouter>
            <Suspense fallback={<div>Chargement...</div>}>
                <Routes>
                    <Route path="/league-monteregie/exclude-analytics" element={<ExcludeAnalytics />} />
                    <Route path="/league-monteregie/include-analytics" element={<IncludeAnalytics />} />
                    <Route path="/league-monteregie/" element={<Index />}/>
                    <Route path="/league-monteregie/player-evolution" element={<PlayerEvolution />}/>
                    <Route path="/league-monteregie/admin/" element={<Login />}/>
                    <Route path="/league-monteregie/admin/dashboard" element={<Dashboard />}/>
                    <Route path="/league-monteregie/admin/analytics" element={<Analytics />}/>
                </Routes>
            </Suspense>            
        </BrowserRouter>
    );
}

export default App;