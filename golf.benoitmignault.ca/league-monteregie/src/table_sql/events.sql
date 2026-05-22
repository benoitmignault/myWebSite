-- Table for storing golf events, including the golf course and event date
CREATE TABLE events (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,

    golf_course VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert sample events into the events table
INSERT INTO events (
    event_name,
    golf_course,
    event_date
)
VALUES
(
    'Semaine 1',
    'Club de golf Vallée des forts',
    '2026-05-10'
),
(
    'Semaine 2',
    'Club de golf Farnham',
    '2026-05-17'
),
(
    'Semaine 3',
    'Club de golf La Seignerie',
    '2026-05-24'
),
(
    'Semaine 4',
    'Club de golf Napierville',
    '2026-05-31'
);