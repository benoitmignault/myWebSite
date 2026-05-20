function Sponsors() {

    const sponsors = [

        {
            name: "Apex Golf",
            website: "https://apex-golf.ca/"
        },

        {
            name: "Station GO",
            website: "https://stationgo.ca/"
        },

        {
            name: "Groupe Golf Montérégie",
            website: "https://www.facebook.com/groups/1029936997443683"
        },

        {
            name: "Toucani",
            website: null
        },

        {
            name: "Mr Tee",
            website: null
        },

        {
            name: "FlexiGolf",
            website: "https://flexigolf.ca/"
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