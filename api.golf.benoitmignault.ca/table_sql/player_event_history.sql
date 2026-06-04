-- Création de la table pour l'historique des événements des joueurs
CREATE TABLE player_event_history (

    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

    event_id INT UNSIGNED NOT NULL,
    player_id INT UNSIGNED NOT NULL,

    previous_position INT UNSIGNED DEFAULT NULL, -- la position du joueur avant l'événement
    current_position INT UNSIGNED NOT NULL DEFAULT 0, -- la position du joueur après l'événement

    previous_fedex_points INT UNSIGNED DEFAULT 0, -- les points totaux avant l'événement
    current_fedex_points INT UNSIGNED NOT NULL DEFAULT 0, -- les points totaux après l'événement

    fedex_points_gained INT UNSIGNED NOT NULL DEFAULT 0, -- les points gagnés lors de l'événement

    previous_handicap DECIMAL(4,1) DEFAULT NULL, -- le handicap du joueur avant l'événement
    current_handicap DECIMAL(4,1) NOT NULL DEFAULT 0.0, -- le handicap du joueur après l'événement

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_history_event
        FOREIGN KEY (event_id)
        REFERENCES events(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_history_player
        FOREIGN KEY (player_id)
        REFERENCES players(id)
        ON DELETE CASCADE,

    UNIQUE KEY unique_event_player (
        event_id,
        player_id
    )

) ENGINE=InnoDB;

-- Les données après la semaine 1 de la saison 2026 de la league de golf de la Montérégie
INSERT INTO player_event_history (
    event_id,
    player_id,
    previous_position,
    current_position,
    previous_fedex_points,
    current_fedex_points,
    fedex_points_gained,
    previous_handicap,
    current_handicap
)

VALUES

-- Robert Labelle
(1, 7, NULL, 1, 0, 300, 300, NULL, 14.0),
-- Daniel Lefebvre
(1, 4, NULL, 2, 0, 285, 285, NULL, 15.0),
-- Monia Roulier
(1, 2, NULL, 3, 0, 285, 285, NULL, 28.0),
-- Jean-Pierre Duval
(1, 18, NULL, 4, 0, 270, 270, NULL, 18.0),
-- Nicolas Carrière
(1, 10, NULL, 5, 0, 255, 255, NULL, 7.0),
-- Robert Gaboriault
(1, 9, NULL, 6, 0, 255, 255, NULL, 17.0),
-- Benoît Mignault
(1, 8, NULL, 7, 0, 235, 235, NULL, 12.0),
-- Danny Guerrin
(1, 1, NULL, 8, 0, 235, 235, NULL, 14.0),
-- Luc Grimard
(1, 6, NULL, 9, 0, 220, 220, NULL, 28.0),
-- Mai-Révée Dolceb
(1, 3, NULL, 10, 0, 210, 210, NULL, 34.0),
-- Eric Grimard
(1, 5, NULL, 11, 0, 202, 202, NULL, 17.0);

-- Les données après la semaine 2 de la saison 2026 de la league de golf de la Montérégie
INSERT INTO player_event_history (
    event_id,
    player_id,
    previous_position,
    current_position,
    previous_fedex_points,
    current_fedex_points,
    fedex_points_gained,
    previous_handicap,
    current_handicap
)

VALUES
-- Robert Labelle
(2, 7, 1, 1, 300, 585, 285, 14.0, 10.0),
-- Nicolas Carrière
(2, 10, 5, 2, 255, 500, 245, 7.0, 7.0),
-- Benoît Mignault
(2, 8, 7, 3, 235, 480, 245, 12.0, 11.9),
-- Monia Roulier
(2, 2, 3, 4, 285, 479, 194, 28.0, 28.0),
-- Jean-Pierre Duval
(2, 18, 4, 5, 270, 464, 194, 18.0, 18.0),
-- Daniel Lefebvre
(2, 4, 2, 6, 285, 463, 178, 15.0, 15.0),
-- Robert Gaboriault
(2, 9, 6, 7, 255, 421, 166, 17.0, 17.0),
-- Mai-Révée Dolceb
(2, 3, 10, 8, 210, 404, 194, 34.0, 34.0),
-- Luc Grimard
(2, 6, 9, 9, 220, 374, 154, 28.0, 28.0),
-- Eric Grimard
(2, 5, 11, 10, 202, 368, 166, 17.0, 17.0),
-- Maxime Paulin
(2, 13, NULL, 11, 0, 300, 300, NULL, 15.0),
-- Normand Gagnon
(2, 11, NULL, 12, 0, 285, 285, NULL, 12.0),
-- Jean-Sébastien Patenaude
(2, 17, NULL, 13, 0, 245, 245, NULL, 12.0),
-- Sylvain Gervais
(2, 15, NULL, 14, 0, 245, 245, NULL, 14.0),
-- Martin Taillon
(2, 16, NULL, 15, 0, 245, 245, NULL, 20.0),
-- Jean-François Asselin
(2, 14, NULL, 16, 0, 245, 245, NULL, 22.0),
-- Mathieu Robidas
(2, 12, NULL, 18, 0, 210, 210, NULL, 7.0);

-- Les données après la semaine 3 de la saison 2026 de la league de golf de la Montérégie
INSERT INTO player_event_history (

    event_id,
    player_id,
    previous_position,
    current_position,
    previous_fedex_points,
    current_fedex_points,
    fedex_points_gained,
    previous_handicap,
    current_handicap
)
VALUES
-- Martin Taillon
(3, 16, 15, 11, 245, 545, 300, 20.0, 13.0),
-- Jean-Pierre Duval
(3, 18, 5, 3, 464, 754, 290, 18.0, 16.0),
-- Benoît Mignault
(3, 8, 3, 2, 480, 760, 280, 11.9, 11.0),
-- Sylvain Gervais
(3, 15, 14, 12, 245, 515, 270, 14.0, 14.0),
-- Robert Gaboriault
(3, 9, 7, 6, 421, 681, 260, 17.0, 17.0),
-- Daniel Lefebvre
(3, 4, 6, 5, 463, 713, 250, 15.0, 16.0),
-- Nicolas Carrière
(3, 10, 2, 4, 500, 735, 235, 7.0, 7.0),
-- Jean-Sébastien Patenaude
(3, 17, 13, 14, 245, 480, 235, 12.0, 12.0),
-- Luc Grimard
(3, 6, 9, 9, 374, 589, 215, 28.0, 33.0),
-- Mai-Révée Dolceb
(3, 3, 8, 8, 404, 619, 215, 33.9, 33.0),
-- Maxime Paulin
(3, 13, 11, 13, 300, 502, 202, 15.0, 15.0),
-- Robert Labelle
(3, 7, 1, 1, 585, 775, 190, 10.0, 10.0),
-- Monia Roulier
(3, 2, 4, 7, 479, 669, 190, 28.0, 29.0),
-- Eric Grimard
(3, 5, 10, 10, 368, 546, 178, 17.0, 25.0);