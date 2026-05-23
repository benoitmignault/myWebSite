-- Création d'un table pour stocker les evenemnts de click sur les sites web ou les médias sociaux des commanditaires
CREATE TABLE website_logs_sponsors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_date DATETIME NOT NULL,
    -- Ajouter une colonne pour stocker le type d'action effectuée (cliquer sur un lien de commanditaire, par exemple)
    action_type VARCHAR(50) NOT NULL,
    -- Ajouter une colonne pour stocker l'ID du commanditaire
    sponsor_id INT NOT NULL,
    -- Ajouter une colonne pour stocker le nom du commanditaire
    sponsor_name VARCHAR(255) NOT NULL,
    -- Ajouter une colonne pour stocker l'URL du lien cliqué
    url VARCHAR(255) NOT NULL
);