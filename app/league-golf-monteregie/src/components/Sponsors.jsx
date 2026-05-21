function Sponsors() {

    const sponsors = [
        {
            name: "Apex Golf",
            logo: "./images/logos/apex-golf.png",
            website: "https://apex-golf.ca/",
            facebook: "https://www.facebook.com/TeeTime.ca",
            instagram: "https://www.instagram.com/tee.time.ca/"
        },
        {
            name: "Station GO",
            logo: "./images/logos/station-go.png",
            website: "https://stationgo.ca/",
            facebook: "https://www.facebook.com/stationgo.ca",
            instagram: "https://www.instagram.com/stationgo.ca/"
        },
        {
            name: "Golf en Montérégie",
            logo: "./images/logos/golf-monteregie.png",
            website: "https://www.facebook.com/groups/1029936997443683",
            facebook: null,
            instagram: null
        },
        {
            name: "Toucani",
            logo: "./images/logos/toucani-bird.png",
            website: null,
            facebook: null,
            instagram: null
        },
        {
            name: "Mr Tee",
            logo: "./images/logos/mr-tee.png",
            website: null,
            facebook: null,
            instagram: null
        },
        {
            name: "FlexiGolf",
            logo: "./images/logos/flexi-golf.png",
            website: "https://flexigolf.ca/",
            facebook: "https://www.facebook.com/FlexiGolfQc",
            instagram: "https://www.instagram.com/flexigolf/"
        }
    ];

    return (
        <section className="sponsors-section">
            <h2>Partenaires officiels</h2>            
            <div className="sponsors-grid">
                {
                    sponsors.map((sponsor) => (
                        <div key={sponsor.name}
                            className={
                                sponsor.website
                                    ? "sponsor-card sponsor-clickable"
                                    : "sponsor-card"
                            }
                        >
                            {/* LOGO */}
                            <img
                                src={sponsor.logo}
                                alt={sponsor.name}
                                className="sponsor-logo"
                            />
                            {/* NOM */}
                            <div className="sponsor-name">{sponsor.name}</div>
                            {/* SITE WEB */}
                            {
                                sponsor.website ? (
                                    <a href={sponsor.website} target="_blank" rel="noreferrer" className="sponsor-link">
                                        Site Web
                                    </a>
                                ) : (
                                    <span className="sponsor-link disabled">À venir</span>
                                )
                            }
                            {/* MÉDIAS SOCIAUX */}
                            <div className="sponsor-socials">
                                {
                                    sponsor.facebook && (
                                        <a href={sponsor.facebook} target="_blank" rel="noreferrer">
                                            <img
                                                src="./images/medias/facebook.png"
                                                alt="Facebook"
                                                className="social-icon"
                                            />
                                        </a>
                                    )
                                }
                                {
                                    sponsor.instagram && (
                                        <a href={sponsor.instagram} target="_blank" rel="noreferrer">
                                            <img
                                                src="./images/medias/instagram.png"
                                                alt="Instagram"
                                                className="social-icon"
                                            />
                                        </a>
                                    )
                                }
                            </div>
                        </div>
                    ))
                }
            </div>
        </section>
    );
}

export default Sponsors;