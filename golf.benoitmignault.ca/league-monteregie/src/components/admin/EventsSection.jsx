import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { API_BASE_URL } from "../../config";
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import "./admin.css";


/**
 * Composant de gestion des événements pour les administrateurs
 * 
 * @description 
 * Affiche un formulaire pour ajouter un événement à la ligue
 * 
 * @returns 
 */
function EventsSection() {

    // Utilisation de useNavigate pour rediriger l'utilisateur vers le bon lien en cas de session invalide
    const navigate = useNavigate();

    // État pour le nom de l'événement à ajouter
    const [eventName, setEventName] = useState("");

    // État pour le lieu de l'événement à ajouter
    const [eventLocation, setEventLocation] = useState("");

    // État pour la date de l'événement à ajouter
    const [eventDate, setEventDate] = useState(null);

    // État pour l'URL du site web de l'événement à ajouter
    const [eventUrl, setEventUrl] = useState("");

    // États pour gérer les erreurs de validation des champs pour ajouter un événement
    const [eventNameError, setEventNameError] = useState(false);
    const [eventLocationError, setEventLocationError] = useState(false);
    const [eventDateError, setEventDateError] = useState(false);
    const [eventUrlError, setEventUrlError] = useState(false);
    
    // État pour empêcher de faire l'ajout plusieurs fois de suite d'un événement si on clique sur le bouton, en attendant la réponse de l'API
    const [loading, setLoading] = useState(false);

    // État pour stocker les messages d'erreur en prévision de l'ajout d'un événement
    const [error, setError] = useState("");

    // État pour stocker un message de succès lors de l'ajout d'un événement à la ligue
    const [successMessage, setSuccessMessage] = useState("");

    // Fonction pour gérer l'ajout d'un événement à la ligue
    const handleAddEvent = async () => {

        // Réinitialiser les messages d'erreur avant de commencer le processus d'ajout
        setError("");

        // On commencer par gérer les erreurs de validation côté client 
        // avant même d'envoyer la requête à l'API, pour éviter les appels inutiles à l'API et 
        // améliorer l'expérience utilisateur.

        // TODO: Changer error pour une liste de msg erreur pour pouvoir afficher plusieurs erreurs à la fois, 
        // au lieu de n'afficher que la première erreur rencontrée.

        // Vérification que les champs firstName, lastName et handicap ne sont pas vides
        if (eventName.trim() === "" || eventLocation.trim() === "" || eventDate === null || eventUrl.trim() === "") {
            setError("Veuillez remplir tous les champs.");

            if (eventName.trim() === "") {
                setEventNameError(true);
            }
            
            if (eventLocation.trim() === "") {
                setEventLocationError(true);
            }

            // La date est un champ spécial, car il peut être null au lieu d'être une chaîne vide, donc on vérifie les deux cas
            if (eventDate === null) {
                setEventDateError(true);
            }

            if (eventUrl.trim() === "") {
                setEventUrlError(true);
            }
            return;
        }

        // Un site web ne peut pas avoir d'espaces
        if (/\s/.test(eventUrl)) {
            setEventUrlError(true);
            setError("L'URL du site web ne doit pas contenir d'espaces.");
            return;
        }

        // Les autres champs peuvent avoir des espaces, donc pas besoin de les vérifier pour ça

        // Si on passe les validations côté client, on peut alors procéder à l'appel de l'API 
        // pour ajouter le joueur à la ligue
        setLoading(true);

        try {
            const response = await fetch(`${API_BASE_URL}/admin/add-event.php`, 
                {
                    method: "POST",
                    credentials: "include",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({name: eventName, location: eventLocation, date: eventDate, url: eventUrl})
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

            // Vérification de la réponse de l'API pour voir si l'événement a été ajouté avec succès ou s'il y a eu une erreur
            if (data.success) {

                // Affichage d'un message de succès pour informer l'administrateur que l'événement a été ajouté avec succès
                setSuccessMessage("Événement ajouté avec succès !");

                // Effacer le message de succès après 3 secondes et les informations de l'événement après 3 secondes
                setTimeout(() => {setSuccessMessage("");}, 3000);
                setTimeout(() => {handleReset();}, 3000);                
            } else {

                // Erreur lors de l'ajout de l'événement
                setError(data.message);
            }

        } catch (err) {

            console.error(err);
            setError("Une erreur est survenue lors de la tentative d'ajout de l'événement.");
        } finally {

            setLoading(false);
        }
    };        

    // Fonction pour réinitialiser les champs du formulaire d'ajout d'un événement et les messages d'erreur associés
    const handleReset = () => {

        // Remise à l'état initial des champs du formulaire d'ajout d'un événement
        setEventName("");
        setEventLocation("");
        setEventDate(null);
        setEventUrl("");

        // Remise à l'état initial du trigger pour remettre les bordures dans leur état normal
        setEventNameError(false);
        setEventLocationError(false);
        setEventDateError(false);
        setEventUrlError(false);

        // Remise à l'état initial du message d'erreur
        setError("");

        // Remise à l'état initial du message de succès
        setSuccessMessage("");
    }

    return (
        <div className="admin-section-card">
            <h2>Section pour ajouter un événement</h2>
            <form onSubmit={(e) => {e.preventDefault(); handleAddEvent();}}>
                <div className="admin-row">
                    <div className="admin-form-group">
                        <label className="admin-label">
                            Événement
                            <span className="required-field">*</span>
                        </label>
                        <input className={`admin-input ${eventNameError ? "input-error" : ""}`}
                            type="text" placeholder="Semaine X" value={eventName}
                            onChange={(e) => {setEventName(e.target.value); setEventNameError(false); setError("");}}
                        />
                    </div>
                    <div className="admin-form-group">
                        <label className="admin-label">
                            Club de golf
                            <span className="required-field">*</span>
                        </label>
                        <input className={`admin-input ${eventLocationError ? "input-error" : ""}`}
                            type="text" placeholder="Nom du club" value={eventLocation}
                            onChange={(e) => {setEventLocation(e.target.value); setEventLocationError(false); setError("");}}
                        />
                    </div>
                </div>
                <div className="admin-row">
                    <div className="admin-form-group">
                        <label className="admin-label">
                            Site web
                            <span className="required-field">*</span>
                        </label>
                        <input className={`admin-input admin-website ${eventUrlError ? "input-error" : ""}`}
                            type="text" placeholder="URL du site web" value={eventUrl}
                            onChange={(e) => {setEventUrl(e.target.value); setEventUrlError(false); setError("");}}
                        />
                    </div>
                    <div className="admin-form-group">
                        <label className="admin-label">
                            Date
                            <span className="required-field">*</span>
                        </label>
                        <DatePicker className={`admin-input ${eventDateError ? "input-error" : ""}`}
                            placeholderText="Sélectionner une date" dateFormat="yyyy-MM-dd" selected={eventDate}
                            onChange={(date) => {setEventDate(date); setEventDateError(false); setError("");}}                            
                            portalId="root"
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

export default EventsSection;