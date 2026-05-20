function Sponsors() {

    const sponsors = [
        {
            name: "Apex Golf",
            logo: null,
            website: "https://apex-golf.ca/",
            facebook: "https://www.facebook.com/TeeTime.ca",
            instagram: "https://www.instagram.com/tee.time.ca/"
        },
        {
            name: "Station GO",
            logo: null,
            website: "https://stationgo.ca/",
            facebook: "https://www.facebook.com/stationgo.ca",
            instagram: "https://www.instagram.com/stationgo.ca/"
        },
        {
            name: "Groupe Golf Montérégie",
            logo: null,
            website: null,
            facebook: "https://www.facebook.com/groups/1029936997443683",
            instagram: null
        },
        {
            name: "Toucani",
            logo: null,
            website: null,
            facebook: null,
            instagram: null
        },
        {
            name: "Mr Tee",
            logo: null,
            website: null,
            facebook: null,
            instagram: null
        },
        {
            name: "FlexiGolf",
            logo: null,
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
                        <div key={sponsor.name} className="sponsor-card">
                            {
                                sponsor.website
                                ? (
                                    <a
                                        href={sponsor.website}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="sponsor-link"
                                    > {sponsor.name} </a>
                                )
                                : (
                                    <span>{sponsor.name} (À venir)</span>
                                )
                            }
                        </div>
                    ))
                }
            </div>
        </section>
    );
}

export default Sponsors;