import { useEffect } from "react";
import { Link } from "react-router-dom";
import { MdAdminPanelSettings } from "react-icons/md";
import { FaChartLine } from "react-icons/fa";
import Standings from "./Standings";
import EventsList from "./EventsList";
import Sponsors from "./Sponsors";
import PosterSection from "./PosterSection";
import ContactSection from "./ContactSection";
import Footer from "./Footer";
import MissionSection from "./MissionSection";
import { BsCameraFill } from "react-icons/bs";
import { FaArrowUp } from "react-icons/fa";
import { API_BASE_URL } from "./../config";
import { FaHandshake } from "react-icons/fa";
import { FaBullseye } from "react-icons/fa";
import '../css/index.css'

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
			<div className="site-navbar">
				<Link to="/league-monteregie/admin/" className="site-navbar-link">
					<MdAdminPanelSettings />
					<span>Section Admin</span>
				</Link>
				<Link to="#" className="site-navbar-link disabled" onClick={(e) => e.preventDefault()}>
					<FaChartLine />
					<span>Évolution Joueur (à venir...)</span>
				</Link>
				<a href="#sponsors" className="site-navbar-link">
					<FaHandshake />
					<span>Partenaires</span>
				</a>
				<a href="#mission" className="site-navbar-link">
					<FaBullseye />
					<span>Mission & Contact</span>
				</a>
			</div>
			<h1 className="homepage-title">Ligue de Golf Montérégie</h1>
			<div className="main-container">        
				<div className="sub-container">
					<Standings />
				</div>
				<div className="sub-container">
					<EventsList />
				</div>						
			</div>
			<div className="photo-credit-wrapper">				
				<div className="homepage-photo-credit">
					<BsCameraFill />
					<span>Photo prise au Club de golf Farnham — Semaine 2</span>
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
			<button className="scroll-top" onClick={() => window.scrollTo({top: 0, behavior: "smooth"})}> 
				<FaArrowUp />
			</button>			
		</div>
	);
}

export default HomePage;