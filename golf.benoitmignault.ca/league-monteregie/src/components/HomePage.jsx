import { useEffect } from "react";
import Standings from "./Standings";
import EventsList from "./EventsList";
import Sponsors from "./Sponsors";
import PosterSection from "./PosterSection";
import ContactSection from "./ContactSection";
import Footer from "./Footer";
import MissionSection from "./MissionSection";
import { API_BASE_URL } from "./../config";

function HomePage() {

	// Utiliser useEffect pour envoyer une requête à l'API de logging à chaque fois que la page est chargée
	useEffect(() => {
		fetch(`${API_BASE_URL}/log-action.php`,
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

	// Utiliser useEffect pour changer le titre de la page et le favicon lorsque le composant HomePage est monté
	useEffect(() => {
	
		// Reset du titre de la page HomePage lorsque le composant est monté
		document.title = "Ligue de Golf Montérégie";	
		const favicon = document.getElementById("dynamic-favicon");

		// On doit partir de /league-monteregie car c'est la vraie racine du projet
		if (favicon) {
			favicon.href = "/league-monteregie/favicon/favicon-ChatGPT.png";
		}

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
			<div className="photo-credit-wrapper">
				<div className="background-photo-credit">
					📸 Photo prise au Club de golf Farnham — Semaine 2
				</div>
			</div>
			<div className="sub-container">
				<Sponsors />
			</div>
			<div className="info-container">
				<div className="poster-container">
					<PosterSection />
				</div>
				<div className="mission-container">
					<MissionSection />
				</div>
				<div className="contact-container">
					<ContactSection />
				</div>
			</div>
			<Footer />
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

export default HomePage;