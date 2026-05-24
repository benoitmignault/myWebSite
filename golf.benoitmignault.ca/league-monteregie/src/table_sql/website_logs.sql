-- Table pour stocker les logs des actions effectuées sur le site 
-- (par exemple, sélection de joueurs ou d'événements + load de page)
CREATE TABLE website_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_date DATETIME NOT NULL,
    -- Ajouter une colonne pour stocker le type d'action effectuée (sélectionner un joueur ou un événement, par exemple)
    action_type VARCHAR(50) NOT NULL,
    -- Ajouter une colonne pour stocker l'ID du type d'action (par exemple, ID de l'utilisateur, ID de l'événement, etc.)
    target_id INT NULL,
    -- Ajouter une colonne pour stocker le nom de la cible de l'action (par exemple, nom de l'utilisateur, nom de l'événement, etc.)
    target_name VARCHAR(255) NULL
) ENGINE=InnoDB;