import { BrowserRouter, Routes, Route } from "react-router-dom";

// Importer les composants de page principale qui est rendu dans HomePage
import HomePage from "./components/HomePage";

// Importer les composants de la page des statistiques
import PlayerStats from "./components/stats/PlayerStats";

// Importer les composants de la section admin
import Login from "./components/admin/Login";
import Dashboard from "./components/admin/Dashboard";

function App() {

    return (
        <BrowserRouter>
            <Routes>
                <Route path="/league-monteregie/" element={<HomePage />}/>
                <Route path="/league-monteregie/statistics" element={<PlayerStats />}/>
                <Route path="/league-monteregie/admin/" element={<Login />}/>
                <Route path="/league-monteregie/admin/dashboard" element={<Dashboard />}/>
            </Routes>
        </BrowserRouter>
    );
}

export default App;