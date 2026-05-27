import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { API_BASE_URL } from "../../config";



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

        // Vérification que les champs username et password ne sont pas vides
        if (username.trim() === "" || password.trim() === "") {
            setError("Veuillez remplir les champs nom d'utilisateur et mot de passe.");
            return;
        }

        // Éviter les espaces accidentels dans le champ username
        if (/\s/.test(username)) {
            setError("Le nom d'utilisateur ne doit pas contenir d'espaces.");
            return;
        }

        // Éviter les espaces accidentels dans le champ password
        if (/\s/.test(password)) {
            setError("Le mot de passe ne doit pas contenir d'espaces.");
            return;
        }

        // Le username ne doit pas excéder 50 caractères
        if (username.length > 50) {
            setError("Le nom d'utilisateur ne doit pas dépasser 50 caractères.");
            return;
        }

        // Le password ne doit pas excéder 100 caractères
        if (password.length > 100) {
            setError("Le mot de passe ne doit pas dépasser 100 caractères.");
            return;
        }

        // Le username doit avoir une longueur minimale de 3 caractères
        if (username.length < 3) {
            setError("Le nom d'utilisateur doit comporter au moins 3 caractères.");
            return;
        }

        // Le password doit avoir une longueur minimale de 8 caractères
        if (password.length < 8) {
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

    return (
        <div>
            Login
            <form onSubmit={(e) => {e.preventDefault(); handleLogin();}}>
                <input
                    type="text" placeholder="Username" value={username}
                    onChange={(e) => {setUsername(e.target.value); setError("");}}
                />
                <input
                    type="password" placeholder="Password" value={password}
                    onChange={(e) => {setPassword(e.target.value); setError("");}}
                />
                <button type="submit" disabled={loading}>
                    Connexion
                </button>
            </form>
        </div>
    );
}

export default Login;