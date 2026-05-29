import { useEffect, useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import { FaHouse } from "react-icons/fa6";
import { API_BASE_URL } from "../../config";
import "./admin.css";

/**
 * Composant de connexion pour les administrateurs et les sous-administrateurs
 * 
 * @description 
 * Affiche un formulaire de connexion pour les administrateurs et les sous-administrateurs. 
 * Permet aux utilisateurs de saisir leurs informations d'identification et de se connecter à l'interface d'administration.
 * 
 * @returns 
 */
function Login() {    

    // États pour stocker le username du formulaire de connexion
    const [username, setUsername] = useState("");

    // États pour stocker le password du formulaire de connexion
    const [password, setPassword] = useState("");

    // États pour gérer les erreurs de validation des champs username et password
    const [usernameError, setUsernameError] = useState(false);
    const [passwordError, setPasswordError] = useState(false);


    // État pour indiquer si la connexion est en cours de traitement
    const [loading, setLoading] = useState(false);

    // État pour stocker les messages d'erreur de connexion
    const [error, setError] = useState("");

    // Utilisation de useNavigate pour rediriger l'utilisateur après une connexion réussie
    const navigate = useNavigate();


    // Fonction pour gérer la tentative de connexion de l'utilisateur
    const handleLogin = async () => {        
        
        // Réinitialiser les messages d'erreur avant de commencer le processus de connexion
        setError("");

        // On commencer par gérer les erreurs de validation côté client 
        // avant même d'envoyer la requête à l'API, pour éviter les appels inutiles à l'API et 
        // améliorer l'expérience utilisateur.

        // TODO: Changer error pour une liste de msg erreur pour pouvoir afficher plusieurs erreurs à la fois, 
        // au lieu de n'afficher que la première erreur rencontrée.

        // Vérification que les champs username et password ne sont pas vides
        if (username.trim() === "" || password.trim() === "") {
            setError("Veuillez remplir les champs nom d'utilisateur et mot de passe.");

            if (username.trim() === "") {
                setUsernameError(true);
            }
            
            if (password.trim() === "") {
                setPasswordError(true);
            }
            return;
        }

        // Éviter les espaces accidentels dans le champ username
        if (/\s/.test(username)) {
            setUsernameError(true);
            setError("Le nom d'utilisateur ne doit pas contenir d'espaces.");
            return;
        }

        // Le username ne doit pas excéder 50 caractères
        if (username.length > 50) {
            setUsernameError(true);
            setError("Le nom d'utilisateur ne doit pas dépasser 50 caractères.");
            return;
        }

        // Le password ne doit pas excéder 100 caractères
        if (password.length > 100) {
            setPasswordError(true);
            setError("Le mot de passe ne doit pas dépasser 100 caractères.");
            return;
        }

        // Le username doit avoir une longueur minimale de 3 caractères
        if (username.length < 3) {
            setUsernameError(true);
            setError("Le nom d'utilisateur doit comporter au moins 3 caractères.");
            return;
        }

        // Le password doit avoir une longueur minimale de 8 caractères
        if (password.length < 8) {
            setPasswordError(true);
            setError("Le mot de passe doit comporter au moins 8 caractères.");
            return;
        }

        // Si on passe les validations côté client, on peut alors procéder à l'appel de l'API 
        // pour tenter de connecter l'utilisateur.
        setLoading(true);

        try {
            
            // Envoi d'une requête POST à l'API pour tenter de connecter l'utilisateur 
            // avec les informations d'identification fournies
            const response = await fetch(`${API_BASE_URL}/admin/login.php`,
                {
                    method: "POST",
                    // Important d'inclure les credentials pour que les cookies de session soient envoyés avec la requête
                    credentials: "include",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({ username, password })
                }
            );

            const data = await response.json();

            // Vérification de la réponse de l'API pour déterminer si la connexion a réussi ou échoué
            if (data.success) {

                // Connexion réussie
                navigate("/league-monteregie/admin/dashboard");
            } else {

                // Connexion échouée
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors de la tentative de connexion.");
        } finally {

            setLoading(false);
        }
    };

    // Fonction pour réinitialiser les champs du formulaire de connexion et les messages d'erreur
    const handleReset = () => {

        // Remise à l'état initial des champs du formulaire de connexion
        setUsername("");
        setPassword("");

        // Remise à l'état initial du trigger pour remettre les bordures dans leur état normal
        setUsernameError(false);
        setPasswordError(false);

        // Remise à l'état initial du message d'erreur
        setError("");
    }

    useEffect(() => {

        // Changement du titre de la page lorsque le composant de connexion est monté
        document.title = "Admin Login - Golf Montérégie";

        // Changement du favicon de la page pour le logo de ChatGPT lorsque le composant de connexion est monté
        const favicon = document.getElementById("dynamic-favicon");
        
        // On doit partir de /league-monteregie car c'est la vraie racine du projet
        if (favicon) {
			favicon.href = "/league-monteregie/favicon/favicon-admin-ChatGPT.png";
		}

    }, []);

    return (
        <div className="admin-login-page">
            <div className="admin-navbar">
                <Link to="/league-monteregie" className="admin-navbar-link">
                    ← Retour au site principal
                </Link>
            </div>
            <div className="admin-login-card">
                <h1>Gestion de la Ligue de Golf Montérégie</h1>
                <h2>Portail Administrateur</h2>
                <p className="admin-description">
                    Gestion des événements, des joueurs et des résultats et affichage du trafics sur le site.                    
                </p>
                <form onSubmit={(e) => {e.preventDefault(); handleLogin();}}>
                    <div className="admin-form-group">
                        <label className="admin-label">Nom d'utilisateur
                            <span className="required-field">*</span>
                        </label>
                        <input className={`admin-input ${usernameError ? "input-error" : ""}`}
                            type="text" placeholder="Username" value={username}
                            onChange={(e) => {setUsername(e.target.value); setUsernameError(false); setError("");}}
                        />
                    </div>
                    <div className="admin-form-group">
                        <label className="admin-label">Mot de passe
                            <span className="required-field">*</span>
                        </label>
                        <input className={`admin-input ${passwordError ? "input-error" : ""}`}
                            type="password" placeholder="Password" value={password}
                            onChange={(e) => {setPassword(e.target.value); setPasswordError(false); setError("");}}
                        />
                    </div>
                    <div className="admin-actions">
                        <button className="admin-button" type="submit" disabled={loading}>
                            Connexion
                        </button>
                        <button
                            className="admin-button admin-button-secondary"
                            type="button" onClick={handleReset}>
                            Effacer
                        </button>
                    </div>
                    {error && (<p className="admin-error-message">{error}</p>)}                    
                </form>
            </div>
            <div className="admin-photo-credit">
                📸 Photo prise au Club de golf Farnham — Semaine 2
            </div>
        </div>        
    );
}

export default Login;