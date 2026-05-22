/**
 * Ce fichier contient la configuration de base pour l'application, notamment l'URL de base de l'API.
 * En utilisant une variable d'environnement, on peut facilement basculer entre l'environnement de développement et de production sans avoir à modifier le code à plusieurs endroits.
 * Assurez-vous de définir la variable d'environnement VITE_API_BASE_URL dans votre fichier .env pour qu'elle pointe vers l'URL de votre API en développement, 
 * et que l'URL de production soit correctement configurée pour pointer vers votre API en production.
 */
export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;