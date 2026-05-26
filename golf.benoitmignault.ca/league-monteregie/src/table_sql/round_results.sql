-- Table for storing round results of players in golf events
CREATE TABLE round_results (

    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    event_id INT UNSIGNED NOT NULL,
    player_id INT UNSIGNED NOT NULL,
    gross_score INT UNSIGNED NOT NULL DEFAULT 0,
    gross_score_adjust INT UNSIGNED NOT NULL DEFAULT 0,
    handicap_used INT NOT NULL DEFAULT 0,
    net_score INT NOT NULL DEFAULT 0,
    position INT UNSIGNED NOT NULL DEFAULT 0,
    fedex_points INT UNSIGNED NOT NULL DEFAULT 0,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_round_results_event
        FOREIGN KEY (event_id)
        REFERENCES events(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_round_results_player
        FOREIGN KEY (player_id)
        REFERENCES players(id)
        ON DELETE CASCADE

) ENGINE=InnoDB;

/*
Étape 1
Insérer :
gross_score
handicap_used
net_score

Étape 2
PHP :
trie net_score ASC;
détecte égalités;
calcule positions;
calcule moyenne points.

Étape 3
PHP update :
position
fedex_points
*/

INSERT INTO round_results (
    event_id,
    player_id,
    gross_score,
    handicap_used,
    net_score,
    position,
    fedex_points,
    gross_score_adjust
)
VALUES
/**
 * Event 1 - Semaine 1
 */
(1, 7, 83, 14, -3, 1, 300, 83),
(1, 4, 88, 15, 1, 2, 285, 88),
(1, 2, 101, 28, 1, 3, 285, 101),
(1, 18, 96, 18, 6, 4, 270, 96),
(1, 10, 86, 7, 7, 5, 255, 86),
(1, 9, 96, 17, 7, 6, 255, 95),
(1, 8, 92, 12, 8, 7, 235, 92),
(1, 1, 94, 14, 8, 8, 235, 94),
(1, 6, 114, 28, 14, 9, 220, 112),
(1, 3, 123, 34, 17, 10, 210, 116),
(1, 5, 108, 17, 19, 11, 202, 107);

/**
 * Event 2 - Semaine 2
 */
INSERT INTO round_results
(event_id, 
player_id, 
gross_score, 
gross_score_adjust, 
handicap_used, 
net_score, 
position, 
fedex_points)
VALUES
(2, 13,  87,  87, 20, -5, 1, 300),
(2,  7,  82,  82, 11, -1, 2, 285),
(2, 11,  84,  82, 13, -1, 3, 285),
(2, 10,  79,  79,  7,  0, 4, 245),
(2,  8,  84,  84, 12,  0, 5, 245),
(2, 17,  84,  84, 12,  0, 6, 245),
(2, 15,  86,  86, 14,  0, 7, 245),
(2, 16,  92,  92, 20,  0, 8, 245),
(2, 14,  95,  94, 23,  0, 9, 245),
(2, 12,  82,  82,  7,  3, 10, 210),
(2, 18,  94,  94, 18,  4, 11, 194),
(2,  2, 104, 104, 28,  4, 12, 194),
(2,  3, 110, 106, 34,  4, 13, 194),
(2,  4,  93,  93, 15,  6, 14, 178),
(2,  5,  97,  97, 17,  8, 15, 166),
(2,  9,  97,  96, 17,  8, 16, 166),
(2,  6, 115, 113, 28, 15, 17, 154);


/**
 * Event 3 - Semaine 3
 */
 INSERT INTO round_results (

    event_id,
    player_id,
    gross_score,
    gross_score_adjust,
    handicap_used,
    net_score,
    position,
    fedex_points
)

VALUES
-- Martin Taillon
(3, 16, 85, 85, 20, -7, 1, 300),
-- Jean-Pierre Duval
(3, 18, 88, 88, 18, -2, 2, 290),
-- Benoît Mignault
(3, 8, 83, 83, 12, -1, 3, 280),
-- Sylvain Gervais
(3, 15, 86, 86, 14, 0, 4, 270),
-- Robert Gaboriault
(3, 9, 90, 89, 17, 1, 5, 260),
-- Daniel Lefebvre
(3, 4, 90, 90, 15, 3, 6, 250),
-- Nicolas Carrière
(3, 10, 83, 83, 7, 4, 7, 235),
-- Jean-Sébastien Patenaude
(3, 17, 88, 88, 12, 4, 8, 235),
-- Luc Grimard
(3, 6, 105, 105, 28, 5, 9, 215),
-- Mai-Révée Dolceb
(3, 3, 111, 105, 34, 5, 10, 215),
-- Maxime Paulin
(3, 13, 95, 94, 15, 8, 11, 202),
-- Robert Labelle
(3, 7, 94, 92, 10, 12, 12, 190),
-- Monia Roulier
(3, 2, 112, 112, 28, 12, 13, 190),
-- Eric Grimard
(3, 5, 103, 103, 17, 14, 14, 178);