-- Création d'une table pour stocker les événements de clic sur les sites web ou les médias sociaux des commanditaires
CREATE TABLE website_logs_sponsors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_date DATETIME NOT NULL,
    -- Ajouter une colonne pour stocker le type de média cliqué (cliquer sur le site web d'un commanditaire, par exemple)
    media_type VARCHAR(50) NOT NULL,
    -- Ajouter une colonne pour stocker l'ID du commanditaire
    sponsor_id INT NOT NULL,
    -- Ajouter une colonne pour stocker le nom du commanditaire
    sponsor_name VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Ajouter une colonne pour stocker l'adresse IP de l'utilisateur qui a cliqué sur le lien du commanditaire
ALTER TABLE website_logs_sponsors
ADD COLUMN ip_address VARCHAR(45) NULL AFTER sponsor_name;