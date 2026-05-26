-- Table for players in the golf league, including their average scores and handicaps
CREATE TABLE players (

    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    average_score DECIMAL(5,1) UNSIGNED DEFAULT NULL,
    handicap_start DECIMAL(4,1) NOT NULL DEFAULT 0.0,
    handicap_league DECIMAL(4,1) NOT NULL DEFAULT 0.0,
    handicap_rounded INT NOT NULL DEFAULT 0,
    previous_position INT UNSIGNED DEFAULT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Ceci est un dump de la table `joueur` de la base de données `league_golf_monteregie`
-- La liste de joueurs après la première semaine de league de golf de la Montérégie, saison 2026
INSERT INTO players (
    firstname,
    lastname,
    average_score,
    handicap_start,
    handicap_league,
    handicap_rounded,
    previous_position
) VALUES
/* Ce sont les données après la 2e semaine qui inclut la position de la semaine 1 */
/*firstname, lastname, average_score, handicap_start, handicap_league, handicap_rounded, previous_position */
('Danny', 'Guerrin', 94.0, 14.0, 14.0, 14, 8),
('Monia', 'Roulier', 102.5, 28.0, 28.0, 28, 3),
('Mai-Révée', 'Dolceb', 111.0, 33.9, 33.9, 34, 10),
('Daniel', 'Lefebvre', 90.5, 15.0, 15.0, 15, 2),
('Eric', 'Grimard', 102.0, 17.0, 17.0, 17, 11),
('Luc', 'Grimard', 112.5, 28.0, 28.0, 28, 9),
('Robert', 'Labelle', 82.5, 14.0, 10.0, 10, 1),
('Benoît', 'Mignault', 88.0, 11.9, 11.9, 12, 7), 
('Robert', 'Gaboriault', 95.5, 17.0, 17.0, 17, 6), 
('Nicolas', 'Carrière', 82.5, 7.0, 7.0, 7, 5), 
('Normand', 'Gagnon', 84.0, 13.0, 12.0, 12, 14), 
('Mathieu', 'Robidas', 82.0, 7.0, 7.0, 7, 12),
('Maxime', 'Paulin', 87.0, 20.0, 15.0, 15, 17),
('Jean-François', 'Asselin', 94.0, 23.0, 22.0, 22, 19),
('Sylvain', 'Gervais', 86.0, 14.0, 14.0, 14, 16),
('Martin', 'Taillon', 92.0, 20.0, 20.0, 20, 15),
('Jean-Sébastien', 'Patenaude', 84.0, 12.0, 12.0, 12, 13),
('Jean-Pierre', 'Duval', 95.0, 18.0, 18.0, 18, 4),
('Mylène', 'Pelletier', null, 17.0, 17.0, 17, 18);

-- On fait un update pour mettre à jour la position précédente de chaque joueur après la deuxième semaine de compétition, 
-- avant de l'insertion des résultats de la troisième semaine. Cela permettra de calculer les changements de position après la troisième semaine.
UPDATE players
SET previous_position =
    CASE id
        -- Robert Labelle
        WHEN 7 THEN 1
        -- Nicolas Carrière
        WHEN 10 THEN 2
        -- Benoît Mignault
        WHEN 8 THEN 3
        -- Monia Roulier
        WHEN 2 THEN 4
        -- Jean-Pierre Duval
        WHEN 18 THEN 5
        -- Daniel Lefebvre
        WHEN 4 THEN 6
        -- Robert Gaboriault
        WHEN 9 THEN 7
        -- Mai-Révée Dolceb
        WHEN 3 THEN 8
        -- Luc Grimard
        WHEN 6 THEN 9
        -- Eric Grimard
        WHEN 5 THEN 10
        -- Maxime Paulin
        WHEN 13 THEN 11
        -- Normand Gagnon
        WHEN 11 THEN 12
        -- Jean-Sébastien Patenaude
        WHEN 17 THEN 13
        -- Sylvain Gervais
        WHEN 15 THEN 14
        -- Martin Taillon
        WHEN 16 THEN 15
        -- Jean-François Asselin
        WHEN 14 THEN 16
        -- Danny Guerrin
        WHEN 1 THEN 17
        -- Mathieu Robidas
        WHEN 12 THEN 18
        -- Mylène Pelletier
        WHEN 19 THEN 19
    END
WHERE id BETWEEN 1 AND 19;