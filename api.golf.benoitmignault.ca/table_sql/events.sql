-- Création de la table events pour stocker les informations sur les événements de golf
CREATE TABLE events (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

    event_name VARCHAR(255) NOT NULL,
    golf_course VARCHAR(255) NOT NULL,
    golf_course_website VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,

    -- Ces champs sont utilisés pour suivre l'état de l'événement
    is_open TINYINT(1) NOT NULL DEFAULT 0,
    is_closed TINYINT(1) NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB;

-- Insert sample events into the events table
INSERT INTO events (
    event_name,
    golf_course,
    golf_course_website,
    event_date,
    is_open,
    is_closed
)
VALUES
(
    'Semaine 1',
    'Club de golf Vallée des forts',
    'https://golfvalleedesforts.com/',
    '2026-05-10',
    0,
    1
),
(
    'Semaine 2',
    'Club de golf Farnham',
    'https://www.farnhamgolf.com/',
    '2026-05-17',
    0,
    1
),
(
    'Semaine 3',
    'Club de golf La Seigneurie',
    'https://golflaseigneurie.ca/',
    '2026-05-24',
    0,
    1
),
(
    'Semaine 4',
    'Club de golf Napierville',
    'https://golfnapierville.ca/',
    '2026-05-31',
    0,
    0
);