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
        if (eventName.trim() === "" || eventLocation.trim() === "" || eventDate.trim() === "" || eventUrl.trim() === "") {
            setError("Veuillez remplir tous les champs.");

            if (eventName.trim() === "") {
                setEventNameError(true);
            }
            
            if (eventLocation.trim() === "") {
                setEventLocationError(true);
            }

            if (eventDate.trim() === "") {
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




















    }




























    return (
        <div className="admin-section-card">
            <h2>Section pour ajouter un événement</h2>
            <div className="admin-form-group">
                <label className="admin-label">
                    Date de l'événement
                    <span className="required-field">*</span>
                </label>

                <DatePicker
                    selected={eventDate}
                    onChange={(date) => {
                        setEventDate(date);
                        setEventDateError(false);
                        setError("");
                    }}
                    dateFormat="yyyy-MM-dd"
                    portalId="root"
                    placeholderText="Sélectionner une date"
                    className={`admin-input ${eventDateError ? "input-error" : ""}`}
                />     
            </div>
        </div>
    );
}

export default EventsSection;