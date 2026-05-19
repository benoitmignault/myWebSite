import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App.jsx'

createRoot(document.getElementById('root')).render(
	// Utiliser StrictMode pour activer les vérifications supplémentaires de React et aider à identifier les problèmes potentiels dans l'application
	// StrictMode avec useEffet fait des doubles insert dans la BD, mais pas en PROD, donc on laisse pour le développement
	<StrictMode>
		<App />
	</StrictMode>,
)
