import { useEffect } from "react";
import Standings from "./pages/Standings";
import EventsList from "./pages/EventsList";

function App() {

	// Utiliser useEffect pour envoyer une requête à l'API de logging à chaque fois que la page est chargée
	useEffect(() => {
		fetch("https://localhost/api/log-action.php",
			{ 
				method: "POST",
				headers: {"Content-Type": "application/json"},
				body: JSON.stringify({
					action_type: "page_load",
					target_id: null,
					target_name: "Affichage page principale"
				})
			}
		);
	}, []);

	return (
		<div>
			<h1>Ligue de Golf Montérégie</h1>
			<div className="main-container">        
				<div className="sub-container">
					<Standings />
				</div>
				<div className="sub-container">
					<EventsList />
				</div>
			</div>
			
			<button
				className="scroll-top"
				onClick={() =>
					window.scrollTo({
						top: 0,
						behavior: "smooth"
					})
				}
			> 
				↑
			</button>
		</div>
	);
}

export default App;