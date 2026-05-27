-- Création de la table admin pour la gestion des utilisateurs administrateurs du système de réservation de golf. 
-- Cette table stocke les informations d'identification et les rôles des administrateurs.
CREATE TABLE admins (

    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,

    role ENUM('admin', 'readonly') NOT NULL DEFAULT 'readonly',

    last_login TIMESTAMP NULL DEFAULT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
