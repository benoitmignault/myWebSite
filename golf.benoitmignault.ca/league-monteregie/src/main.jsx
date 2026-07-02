import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import App from './App.jsx'

// Point d'entrée de l'application React, qui rend le composant App dans l'élément avec l'id 'root' du fichier index.html
createRoot(document.getElementById('root')).render(
	// Utiliser StrictMode pour activer les vérifications supplémentaires de React et aider à identifier les problèmes potentiels dans l'application
	// StrictMode avec useEffet fait des doubles insert dans la BD, mais pas en PROD, donc on laisse pour le développement
	<StrictMode>
		<App />
	</StrictMode>,
)
