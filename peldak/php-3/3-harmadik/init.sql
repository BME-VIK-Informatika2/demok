USE info2;

CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS attendance (
    attendee_id INT NOT NULL,
    session_id INT NOT NULL,
    PRIMARY KEY (attendee_id, session_id),
    FOREIGN KEY (attendee_id) REFERENCES attendees(id),
    FOREIGN KEY (session_id) REFERENCES sessions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO sessions (name) VALUES
('Modern frontend technológiák'),
('Adatbázis kezelés kihívásai'),
('Újdonságok a backend fejlesztésben'),
('Az AI hatása a webfejlesztésre'),
('IoT eszközök a napjainkban');

INSERT INTO attendees (name) VALUES
('Kiss Árpád'),
('Nagy Géza'),
('Kovács Ágnes'),
('Tóth István'),
('Tóth Istvánné'),
('Varga Ferenc'),
('Máté Ákos'),
('Horváth Éva'),
('Móra Ferenc'),
('Lengyel Angéla');
