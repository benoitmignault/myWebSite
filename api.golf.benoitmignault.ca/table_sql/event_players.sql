-- Création de la table event_players pour stocker les joueurs participants à chaque événement
-- Ça va être utilse pour comprer le nombre de résultats par rapport au nombre de joueurs inscrits à l'événement
-- Une fois qu'on match le nombre, on va pouvoir faire le snapshot de l'evement et 
-- remplir les informations de la table player_event_history pour garder une évolution des joueurs dans le temps
CREATE TABLE event_players (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

    event_id INT UNSIGNED NOT NULL,
    player_id INT UNSIGNED NOT NULL,

    handicap_event INT NOT NULL DEFAULT 0,

    UNIQUE KEY uq_event_player (event_id, player_id)

) ENGINE=InnoDB;

-- Semaine 1
INSERT INTO event_players
(event_id, player_id, handicap_event)
VALUES
(1, 7, 14),   -- Robert Labelle
(1, 4, 15),   -- Daniel Lefebvre
(1, 2, 28),   -- Monia Roulier
(1, 18, 18),  -- Jean-Pierre Duval
(1, 10, 7),   -- Nicolas Carrière
(1, 9, 17),   -- Robert Gaboriault
(1, 8, 12),   -- Benoît Mignault
(1, 1, 14),   -- Danny Guerin
(1, 6, 28),   -- Luc Grimard
(1, 3, 34),   -- Mai-Révée Dolceb
(1, 5, 17);   -- Eric Grimard

-- Semaine 2
INSERT INTO event_players
(event_id, player_id, handicap_event)
VALUES
(2, 13, 20),  -- Maxime Paulin
(2, 7, 11),   -- Robert Labelle
(2, 11, 13),  -- Normand Gagnon
(2, 10, 7),   -- Nicolas Carrière
(2, 8, 12),   -- Benoît Mignault
(2, 17, 12),  -- Jean-Sébastien Patenaude
(2, 15, 14),  -- Sylvain Gervais
(2, 16, 20),  -- Martin Taillon
(2, 14, 23),  -- Jean-François Asselin
(2, 12, 7),   -- Mathieu Robidas
(2, 18, 18),  -- Jean-Pierre Duval
(2, 2, 28),   -- Monia Roulier
(2, 3, 34),   -- Mai-Révée Dolceb
(2, 4, 15),   -- Daniel Lefebvre
(2, 5, 17),   -- Eric Grimard
(2, 9, 17),   -- Robert Gaboriault
(2, 6, 28);   -- Luc Grimard

-- Semaine 3
INSERT INTO event_players
(event_id, player_id, handicap_event)
VALUES
(3, 16, 20),  -- Martin Taillon
(3, 18, 18),  -- Jean-Pierre Duval
(3, 8, 12),   -- Benoît Mignault
(3, 15, 14),  -- Sylvain Gervais
(3, 9, 17),   -- Robert Gaboriault
(3, 4, 15),   -- Daniel Lefebvre
(3, 10, 7),   -- Nicolas Carrière
(3, 17, 12),  -- Jean-Sébastien Patenaude
(3, 6, 28),   -- Luc Grimard
(3, 3, 34),   -- Mai-Révée Dolceb
(3, 13, 20),  -- Maxime Paulin
(3, 7, 10),   -- Robert Labelle
(3, 2, 28),   -- Monia Roulier
(3, 5, 17);   -- Eric Grimard