import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { API_BASE_URL } from "../../config";
import "./admin.css";


/**
 * Composant de gestion des joueurs pour les administrateurs
 * 
 * @description 
 * Affiche un formulaire pour ajouter un joueur à la ligue
 * 
 * @returns 
 */
function PlayersSection() {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // État pour le prénom du joueur à ajouter
    const [firstName, setFirstName] = useState("");

    // État pour le nom de famille du joueur à ajouter
    const [lastName, setLastName] = useState("");

    // État pour l'handicap du joueur à ajouter
    const [handicap, setHandicap] = useState("");

    // États pour gérer les erreurs de validation des champs pour ajouter un joueur
    const [firstNameError, setFirstNameError] = useState(false);
    const [lastNameError, setLastNameError] = useState(false);
    const [handicapError, setHandicapError] = useState(false);

    // État pour empêcher de faire l'ajout plusieurs fois de suite d'un joueur si on clique sur le bouton, en attendant la réponse de l'API
    const [loading, setLoading] = useState(false);

    // État pour stocker les messages d'erreur en prévision de l'ajout d'un joueur
    const [error, setError] = useState("");

    // État pour stocker un message de succès lors de l'ajout d'un joueur à la ligue
    const [successMessage, setSuccessMessage] = useState("");
    
    // Fonction pour gérer l'ajout d'un joueur à la ligue
    const handleAddPlayer = async () => {

        // Réinitialiser les messages d'erreur avant de commencer le processus d'ajout
        setError("");

        // On commencer par gérer les erreurs de validation côté client 
        // avant même d'envoyer la requête à l'API, pour éviter les appels inutiles à l'API et 
        // améliorer l'expérience utilisateur.

        // TODO: Changer error pour une liste de msg erreur pour pouvoir afficher plusieurs erreurs à la fois, 
        // au lieu de n'afficher que la première erreur rencontrée.

        // Vérification que les champs firstName, lastName et handicap ne sont pas vides
        if (firstName.trim() === "" || lastName.trim() === "" || handicap.trim() === "") {
            setError("Veuillez remplir les champs prénom, nom et handicap.");

            if (firstName.trim() === "") {
                setFirstNameError(true);
            }
            
            if (lastName.trim() === "") {
                setLastNameError(true);
            }

            if (handicap.trim() === "") {
                setHandicapError(true);
            }
            return;
        }

        // Un prénom ne peut pas avoir d'espaces
        if (/\s/.test(firstName)) {
            setFirstNameError(true);
            setError("Le prénom ne doit pas contenir d'espaces.");
            return;
        }

        // Un nom de famille ne peut pas avoir d'espaces
        if (/\s/.test(lastName)) {
            setLastNameError(true);
            setError("Le nom de famille ne doit pas contenir d'espaces.");
            return;
        }

        // L'handicap ne peut pas avoir d'espaces
        if (/\s/.test(handicap)) {
            setHandicapError(true);
            setError("L'handicap ne doit pas contenir d'espaces.");
            return;
        }

        // Si l'handicap possède une virgule au lieu d'un point, on remplace la virgule par un point pour éviter les erreurs lors de la conversion en float
        // Ne pas utiliser «setHandicap» car c'est asynchrone 
        const handicapValue = parseFloat(handicap.replace(",", "."));

        if (isNaN(handicapValue)) {

            setHandicapError(true);
            setError("Handicap invalide.");
            return;
        }

        // Si on passe les validations côté client, on peut alors procéder à l'appel de l'API 
        // pour ajouter le joueur à la ligue
        setLoading(true);

        try {

            // Appel de l'API pour ajouter un joueur à la ligue
            const response = await fetch(`${API_BASE_URL}/admin/add-player.php`, 
                {
                    method: "POST",
                    credentials: "include",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({firstName: firstName, lastName: lastName, handicap: handicapValue})
                }
            );

            // Si la réponse de l'API indique que la session est invalide, 
            // rediriger le gestionnaire vers la page de connexion
            if (response.status === 401) {

                setError("Votre session a expiré, vous allez être redirigé vers la page de connexion.");
                setTimeout(() => {navigate("/league-monteregie/admin");}, 3000);
                return;
            }

            // Sinon, on a un résultat valide du retour de l'API
            const data = await response.json();

            // Vérification de la réponse de l'API pour voir si le joueur a été ajouté avec succès ou s'il y a eu une erreur
            if (data.success) {

                // Affichage d'un message de succès pour informer l'administrateur que le joueur a été ajouté avec succès
                setSuccessMessage("Joueur ajouté avec succès !");

                // Effacer le message de succès après 3 secondes et les informations du joueur après 3 secondes
                setTimeout(() => {setSuccessMessage("");}, 3000);
                setTimeout(() => {handleReset();}, 3000);   
            } else {

                // Erreur lors de l'ajout du joueur
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors de la tentative d'ajout du joueur.");
        } finally {

            setLoading(false);
        }
    };

    // Fonction pour réinitialiser les champs du formulaire d'ajout d'un joueur et les messages d'erreur associés
    const handleReset = () => {

        // Remise à l'état initial des champs du formulaire d'ajout d'un joueur
        setFirstName("");
        setLastName("");
        setHandicap("");

        // Remise à l'état initial du trigger pour remettre les bordures dans leur état normal
        setFirstNameError(false);
        setLastNameError(false);
        setHandicapError(false);

        // Remise à l'état initial du message d'erreur
        setError("");

        // Remise à l'état initial du message de succès
        setSuccessMessage("");
    }

    return (
        <div className="admin-section-card">
            <h2>Section pour ajouter un joueur</h2>
            <form onSubmit={(e) => {e.preventDefault(); handleAddPlayer();}}>
                <div className="admin-row">
                    <div className="admin-form-group">
                        <label className="admin-label">
                            Prénom
                            <span className="required-field">*</span>
                        </label>
                        <input className={`admin-input ${firstNameError ? "input-error" : ""}`}
                            type="text" placeholder="Prénom" value={firstName}
                            onChange={(e) => {setFirstName(e.target.value); setFirstNameError(false); setError("");}}
                        />
                    </div>
                    <div className="admin-form-group">
                        <label className="admin-label">
                            Nom
                            <span className="required-field">*</span>
                        </label>
                        <input className={`admin-input ${lastNameError ? "input-error" : ""}`}
                            type="text" placeholder="Nom" value={lastName}
                            onChange={(e) => {setLastName(e.target.value); setLastNameError(false); setError("");}}
                        />
                    </div>
                </div>
                <div className="admin-row">
                    <div className="admin-form-group">
                        <label className="admin-label">
                            Handicap de départ
                            <span className="required-field">*</span>
                        </label>
                        <input
                            className={`admin-input ${handicapError ? "input-error" : ""}`}
                            type="number" step="0.1" placeholder="Ex : 12.4" value={handicap}
                            onChange={(e) => {setHandicap(e.target.value); setHandicapError(false); setError("");}}
                        />
                    </div>
                </div>
                
                <div className="admin-actions">
                    <button className="admin-button" type="submit" disabled={loading}>
                        Ajouter
                    </button>
                    <button
                        className="admin-button admin-button-secondary"
                        type="button" onClick={handleReset}>
                        Effacer
                    </button>
                </div>
                {error && <p className="admin-error-message">✗ {error}</p>}
                {successMessage && <p className="admin-success-message">✓ {successMessage}</p>}
            </form>
        </div>
    );
}

export default PlayersSection;