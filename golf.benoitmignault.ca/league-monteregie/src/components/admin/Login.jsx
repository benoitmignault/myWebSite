import { useEffect, useState } from "react";
import React from "react";
import { API_BASE_URL } from "../config";



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








    return (
        <div>
            Login
        </div>
    );
}

export default Login;