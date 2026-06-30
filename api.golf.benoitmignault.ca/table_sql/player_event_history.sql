-- Création de la table pour l'historique des événements des joueurs
CREATE TABLE player_event_history (

    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

    event_id INT UNSIGNED NOT NULL,
    player_id INT UNSIGNED NOT NULL,

    previous_position INT UNSIGNED DEFAULT NULL,
    current_position INT UNSIGNED NOT NULL DEFAULT 0,

    previous_fedex_points INT UNSIGNED DEFAULT 0,
    current_fedex_points INT UNSIGNED NOT NULL DEFAULT 0,

    fedex_points_gained INT UNSIGNED NOT NULL DEFAULT 0,

    previous_handicap DECIMAL(4,1) DEFAULT NULL,
    current_handicap DECIMAL(4,1) NOT NULL DEFAULT 0.0,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_history_event
        FOREIGN KEY (event_id)
        REFERENCES events(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_history_player
        FOREIGN KEY (player_id)
        REFERENCES players(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

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
(1, 7, NULL, 1, 0, 300, 300, 14.0, 11.0),ok
-- Daniel Lefebvre
(1, 4, NULL, 2, 0, 285, 285, 15.0, 16.0),ok
-- Monia Roulier
(1, 2, NULL, 3, 0, 285, 285, 28.0, 29.0),ok
-- Jean-Pierre Duval
(1, 18, NULL, 4, 0, 270, 270, 18.0, 24.0),ok
-- Nicolas Carrière
(1, 10, NULL, 5, 0, 255, 255, 7.0, 14.0),ok
-- Robert Gaboriault
(1, 9, NULL, 6, 0, 255, 255, 17.0, 23.0),ok
-- Benoît Mignault
(1, 8, NULL, 7, 0, 235, 235, 12.0, 20.0),ok
-- Danny Guerrin
(1, 1, NULL, 8, 0, 235, 235, 14.0, 22.0),ok
-- Luc Grimard
(1, 6, NULL, 9, 0, 220, 220, 28.0, 40.0),ok
-- Mai-Révée Dolceb
(1, 3, NULL, 10, 0, 210, 210, 34.0, 44.0),ok
-- Eric Grimard
(1, 5, NULL, 11, 0, 202, 202, 17.0, 35.0);ok

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
(2, 7, 1, 1, 300, 585, 285, 11.0, 10.0),ok
-- Nicolas Carrière
(2, 10, 5, 2, 255, 500, 245, 14.0, 7.0),ok
-- Benoît Mignault
(2, 8, 7, 3, 235, 480, 245, 20.0, 12.0),ok
-- Monia Roulier
(2, 2, 3, 4, 285, 479, 194, 29.0, 29.0),ok
-- Jean-Pierre Duval
(2, 18, 4, 5, 270, 464, 194, 24.0, 22.0),ok
-- Daniel Lefebvre
(2, 4, 2, 6, 285, 463, 178, 16.0, 16.0),ok
-- Robert Gaboriault
(2, 9, 6, 7, 255, 421, 166, 23.0, 23.0),ok
-- Mai-Révée Dolceb
(2, 3, 10, 8, 210, 404, 194, 44.0, 34.0),ok
-- Luc Grimard
(2, 6, 9, 9, 220, 374, 154, 40.0, 40.0),ok
-- Eric Grimard
(2, 5, 11, 10, 202, 368, 166, 35.0, 25.0),ok
-- Maxime Paulin
(2, 13, NULL, 11, 0, 300, 300, 20.0, 15.0),ok
-- Normand Gagnon
(2, 11, NULL, 12, 0, 285, 285, 13.0, 12.0),ok
-- Jean-Sébastien Patenaude
(2, 17, NULL, 13, 0, 245, 245, 12.0, 12.0),ok
-- Sylvain Gervais
(2, 15, NULL, 14, 0, 245, 245, 14.0, 14.0),ok
-- Martin Taillon
(2, 16, NULL, 15, 0, 245, 245, 20.0, 20.0),ok
-- Jean-François Asselin
(2, 14, NULL, 16, 0, 245, 245, 23.0, 22.0),ok
-- Mathieu Robidas
(2, 12, NULL, 18, 0, 210, 210, 7.0, 10.0);ok

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
(3, 16, 15, 11, 245, 545, 300, 20.0, 13.0),ok
-- Jean-Pierre Duval
(3, 18, 5,   3, 464, 754, 290, 22.0, 16.0),ok
-- Benoît Mignault
(3, 8, 3,    2, 480, 760, 280, 12.0, 11.0),ok
-- Sylvain Gervais
(3, 15, 14, 12, 245, 515, 270, 14.0, 14.0),ok
-- Robert Gaboriault
(3, 9, 7,    6, 421, 681, 260, 23.0, 17.0),ok
-- Daniel Lefebvre
(3, 4, 6,    5, 463, 713, 250, 16.0, 16.0),ok
-- Nicolas Carrière
(3, 10, 2,    4, 500, 735, 235, 7.0, 7.0),ok
-- Jean-Sébastien Patenaude
(3, 17, 13, 14, 245, 480, 235, 12.0, 12.0),ok
-- Luc Grimard
(3, 6, 9, 9,    374, 589, 215, 40.0, 33.0),ok
-- Mai-Révée Dolceb
(3, 3, 8, 8,    404, 619, 215, 34.0, 33.0),ok
-- Maxime Paulin
(3, 13, 11, 13, 300, 502, 202, 15.0, 15.0),ok
-- Robert Labelle
(3, 7, 1, 1,    585, 775, 190, 10.0, 10.0),ok
-- Monia Roulier
(3, 2, 4, 7,    479, 669, 190, 29.0, 29.0),ok
-- Eric Grimard
(3, 5, 10, 10,  368, 546, 178, 25.0, 25.0);ok