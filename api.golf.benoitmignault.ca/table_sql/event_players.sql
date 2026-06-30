-- Création de la table event_players pour stocker les joueurs participants à chaque événement
-- Ça va être utilse pour comprer le nombre de résultats par rapport au nombre de joueurs inscrits à l'événement
-- Une fois qu'on match le nombre, on va pouvoir faire le snapshot de l'evement et 
-- remplir les informations de la table player_event_history pour garder une évolution des joueurs dans le temps
CREATE TABLE event_players (

    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

    event_id INT UNSIGNED NOT NULL,
    player_id INT UNSIGNED NOT NULL,

    handicap_rounded INT NOT NULL DEFAULT 0,
    team_number TINYINT UNSIGNED NOT NULL,

    UNIQUE KEY uq_event_players_event_player (
        event_id,
        player_id
    ),

    INDEX idx_event_players_event (event_id),
    INDEX idx_event_players_player (player_id),

    CONSTRAINT fk_event_players_event
        FOREIGN KEY (event_id)
        REFERENCES events(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_event_players_player
        FOREIGN KEY (player_id)
        REFERENCES players(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB;

-- Semaine 1
INSERT INTO event_players
(event_id, player_id, handicap_rounded, team_number)
VALUES
(1, 7, 14, 3),   -- Robert Labelle
(1, 4, 15, 3),   -- Daniel Lefebvre
(1, 2, 28, 2),   -- Monia Roulier
(1, 18, 18, 1),  -- Jean-Pierre Duval
(1, 10, 7, 1),   -- Nicolas Carrière
(1, 9, 17, 1),   -- Robert Gaboriault
(1, 8, 12, 2),   -- Benoît Mignault
(1, 1, 14, 3),   -- Danny Guerin
(1, 6, 28, 3),   -- Luc Grimard
(1, 3, 34, 2),   -- Mai-Révée Dolceb
(1, 5, 17, 1);   -- Eric Grimard

-- Semaine 2
INSERT INTO event_players
(event_id, player_id, handicap_rounded, team_number)
VALUES
(2, 13, 20, 5),  -- Maxime Paulin
(2, 7, 11, 1),   -- Robert Labelle
(2, 11, 13, 2),  -- Normand Gagnon
(2, 10, 7, 4),   -- Nicolas Carrière
(2, 8, 12, 3),   -- Benoît Mignault
(2, 17, 12, 5),  -- Jean-Sébastien Patenaude
(2, 15, 14, 1),  -- Sylvain Gervais
(2, 16, 20, 5),  -- Martin Taillon
(2, 14, 23, 5),  -- Jean-François Asselin
(2, 12, 7, 3),   -- Mathieu Robidas
(2, 18, 18, 2),  -- Jean-Pierre Duval
(2, 2, 28, 2),   -- Monia Roulier
(2, 3, 34, 3),   -- Mai-Révée Dolceb
(2, 4, 15, 2),   -- Daniel Lefebvre
(2, 5, 17, 4),   -- Eric Grimard
(2, 9, 17, 3),   -- Robert Gaboriault
(2, 6, 28, 1);   -- Luc Grimard

-- Semaine 3
INSERT INTO event_players
(event_id, player_id, handicap_rounded, team_number)
VALUES
(3, 16, 20, 3),  -- Martin Taillon
(3, 18, 18, 4),  -- Jean-Pierre Duval
(3, 8, 12, 4),   -- Benoît Mignault
(3, 15, 14, 1),  -- Sylvain Gervais
(3, 9, 17, 2),   -- Robert Gaboriault
(3, 4, 15, 3),   -- Daniel Lefebvre
(3, 10, 7, 1),   -- Nicolas Carrière
(3, 17, 12, 3),  -- Jean-Sébastien Patenaude
(3, 6, 28, 1),   -- Luc Grimard
(3, 3, 34, 4),   -- Mai-Révée Dolceb
(3, 13, 20, 2),  -- Maxime Paulin
(3, 7, 10, 2),   -- Robert Labelle
(3, 2, 28, 1),   -- Monia Roulier
(3, 5, 17, 4);   -- Eric Grimard