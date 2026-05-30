import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { API_BASE_URL } from "../../config";
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
    const [eventDate, setEventDate] = useState("");

    // État pour l'URL du site web de l'événement à ajouter
    const [eventUrl, setEventUrl] = useState("");




    return (
        <div className="admin-section-card">
            <h2>Section pour ajouter un evenement</h2>
            

        </div>
    );
}

export default EventsSection;