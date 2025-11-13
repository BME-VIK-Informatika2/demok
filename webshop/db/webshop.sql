CREATE SCHEMA IF NOT EXISTS webshop CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
USE webshop;

DROP TABLE IF EXISTS megrendeles_tetelek;
DROP TABLE IF EXISTS megrendelesek;
DROP TABLE IF EXISTS vevok;
DROP TABLE IF EXISTS termekek;

CREATE TABLE termekek
(
    id            int PRIMARY KEY AUTO_INCREMENT,
    nev           varchar(50) NOT NULL,
    raktarkeszlet int         NOT NULL DEFAULT 50,
    ar            int
);

CREATE TABLE vevok
(
    id      int PRIMARY KEY AUTO_INCREMENT,
    nev     varchar(50) NOT NULL,
    cim     varchar(100),
    telefon varchar(20)
);

CREATE TABLE megrendelesek
(
    id      int PRIMARY KEY AUTO_INCREMENT,
    vevo_id int not null,
    datum   datetime,
    CONSTRAINT FK_M_V FOREIGN KEY (vevo_id) REFERENCES vevok (id)
);

CREATE TABLE megrendeles_tetelek
(
    termek_id       int NOT NULL,
    megrendeles_id int NOT NULL,
    db             int NOT NULL,
    PRIMARY KEY (termek_id, megrendeles_id),
    CONSTRAINT FK_MT_T FOREIGN KEY (termek_id) REFERENCES termekek (id),
    CONSTRAINT FK_MT_M FOREIGN KEY (megrendeles_id) REFERENCES megrendelesek (id)
);

-- Termékek
INSERT INTO termekek (nev, raktarkeszlet, ar)
VALUES ('Alma', 150, 400);
INSERT INTO termekek (nev, raktarkeszlet, ar)
VALUES ('Körte', 120, 800);
INSERT INTO termekek (nev, raktarkeszlet, ar)
VALUES ('Eper', 100, 2000);
INSERT INTO termekek (nev, raktarkeszlet, ar)
VALUES ('Szőlő', 110, 1600);
INSERT INTO termekek (nev, raktarkeszlet, ar)
VALUES ('Banán', 200, 700);
INSERT INTO termekek (nev, raktarkeszlet, ar)
VALUES ('Narancs', 220, 800);
INSERT INTO termekek (nev, raktarkeszlet, ar)
VALUES ('Kivi', 0, NULL);

-- Vevők
INSERT INTO vevok (nev, cim, telefon)
VALUES ('Kiss Árpád', '1111 Budapest, Egy utca 3', NULL);
INSERT INTO vevok (nev, cim, telefon)
VALUES ('Nagy Géza', '5000 Szolnok, Fa tér 4', '+36 20 987 4562');
INSERT INTO vevok (nev, cim, telefon)
VALUES ('Kovács Ágnes', NULL, '+36 70 123 4567');
INSERT INTO vevok (nev, cim, telefon)
VALUES ('Tóth István', '9000 Győr, Tó utca 6', '+36 30 555 5555');
INSERT INTO vevok (nev, cim, telefon)
VALUES ('Tóth Istvánné', '9000 Győr, Tó utca 6', '+36 30 555 5555');
INSERT INTO vevok (nev, cim, telefon)
VALUES ('Varga Ferenc', '1115 Budapest, Magyar tudósok krt. 2/Q', '+36 20 123 4567');

-- Megrendelések
INSERT INTO megrendelesek (vevo_id, datum)
VALUES (2, '2023-01-01');
INSERT INTO megrendelesek (vevo_id, datum)
VALUES (1, '2023-02-02');
INSERT INTO megrendelesek (vevo_id, datum)
VALUES (1, '2023-03-03');
INSERT INTO megrendelesek (vevo_id, datum)
VALUES (3, '2022-04-04');
INSERT INTO megrendelesek (vevo_id, datum)
VALUES (2, '2022-05-05');
INSERT INTO megrendelesek (vevo_id, datum)
VALUES (2, curdate());

-- Megrendelés tételek
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (1, 1, 5);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (2, 1, 4);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (3, 1, 6);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (1, 2, 5);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (4, 2, 10);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (3, 3, 2);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (1, 3, 3);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (2, 3, 8);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (3, 4, 1);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (3, 5, 7);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (4, 5, 2);
INSERT INTO megrendeles_tetelek (termek_id, megrendeles_id, db)
VALUES (1, 6, 4);
