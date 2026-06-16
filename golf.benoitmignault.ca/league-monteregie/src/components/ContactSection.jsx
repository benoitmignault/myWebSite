function ContactSection() {

    return (
        <section className="contact-section">
            <h2>Contactez-nous</h2>
            <div className="contact-grid">

                {/* Bloc Nicolas */}
                <div className="contact-card">
                    <h3>Concepteur et Organisateur de la Ligue Golf en Montérégie</h3>
                    <p>Nicolas Carrière</p>
                    <a href="mailto:nicolascarriereaw@gmail.com" target="_blank" rel="noopener noreferrer" className="contact-link">
                        📧 nicolascarriereaw@gmail.com ↗
                    </a>
                    <p>📞 (450) 357-6496</p>
                </div>

                {/* Bloc Benoît */}
                <div className="contact-card">
                    <h3>Conception et développement web</h3>
                    <p>Benoît Mignault</p>
                    <a href="mailto:benoit.mignault.ca@gmail.com" target="_blank" rel="noopener noreferrer" className="contact-link">
                        📧 benoit.mignault.ca@gmail.com ↗
                    </a>
                    <p className="contact-note">Site web sous le même nom de domaine</p>
                </div>
            </div>
        </section>
    );
}

export default ContactSection;