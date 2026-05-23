function Sponsors() {

    const sponsors = [
        {   
            id: 1,
            name: "Apex Golf",
            logo: "./images/logos/apex-golf.png",
            logoClass: "logo-plus-gros",
            website: "https://apex-golf.ca/",
            facebook: "https://www.facebook.com/TeeTime.ca",
            instagram: "https://www.instagram.com/tee.time.ca/"
        },
        {
            id: 2,
            name: "Station GO",
            logo: "./images/logos/station-go.png",
            logoClass: "logo-plus-large",
            website: "https://stationgo.ca/",
            facebook: "https://www.facebook.com/stationgo.ca",
            instagram: "https://www.instagram.com/stationgo.ca/"
        },
        {
            id: 3,
            name: "Golf en Montérégie",
            logo: "./images/logos/golf-monteregie.png",
            logoClass: "logo-plus-gros",
            website: "https://www.facebook.com/groups/1029936997443683",
            facebook: null,
            instagram: null
        },
        {
            id: 4,
            name: "Toucani",
            logo: "./images/logos/toucani-bird.png",
            logoClass: "logo-plus-large",
            website: null,
            facebook: null,
            instagram: null
        },
        {
            id: 5,
            name: "Mr Tee",
            logo: "./images/logos/mr-tee.png",
            logoClass: "logo-plus-gros",
            website: null,
            facebook: null,
            instagram: null
        },
        {
            id: 6,
            name: "FlexiGolf",
            logo: "./images/logos/flexi-golf.png",
            logoClass: "logo-plus-gros",
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
                            <div className="sponsor-logo-container">
                                <img
                                    src={sponsor.logo}
                                    alt={sponsor.name}
                                    className={`sponsor-logo ${sponsor.logoClass}`}
                                />
                            </div>
                            {/* NOM */}
                            <div className="sponsor-name">{sponsor.name}</div>
                            {/* SITE WEB */}
                            {
                                sponsor.website ? (
                                    <a href={sponsor.website} target="_blank" rel="noreferrer" className="sponsor-website-link">
                                        Site Web
                                    </a>
                                ) : (
                                    <span className="sponsor-website-link disabled">À venir</span>
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