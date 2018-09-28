SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS Kostym;
DROP TABLE IF EXISTS Kategorie;
DROP TABLE IF EXISTS Klient;
DROP TABLE IF EXISTS Zaznam;
DROP TABLE IF EXISTS Doplnek;
DROP TABLE IF EXISTS PravnickaOsoba;
DROP TABLE IF EXISTS Zamestnanec;

CREATE TABLE Kostym(
id_kostym INTEGER NOT NULL AUTO_INCREMENT,
vyrobce VARCHAR(25) NOT NULL,
material VARCHAR(25) NOT NULL,
popis VARCHAR(100),
cena INTEGER NOT NULL,
datumVyroby DATE NOT NULL,
opotrebeni VARCHAR(25),
velikost VARCHAR(25) NOT NULL,
barva VARCHAR(25) NOT NULL,
dostupnost VARCHAR(25) NOT NULL,
id_kategorie INTEGER NOT NULL,
id_doplnek INTEGER NULL,
id_zamestnanec INTEGER NOT NULL,
PRIMARY KEY(id_kostym)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE Kategorie(
id_kategorie INTEGER NOT NULL AUTO_INCREMENT,
nazev VARCHAR(25) NOT NULL,
popis VARCHAR(100) NULL,
PRIMARY KEY (id_kategorie)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE Doplnek(
id_doplnek INTEGER NOT NULL AUTO_INCREMENT,
nazev VARCHAR(25) NOT NULL,
popis VARCHAR(100) NULL,
datumVyroby DATE NOT NULL,
cena INTEGER NOT NULL,
dostupnost VARCHAR(25) NOT NULL,
id_zamestnanec INTEGER NOT NULL,
PRIMARY KEY (id_doplnek)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE Zaznam(
id_zaznam INTEGER NOT NULL AUTO_INCREMENT,
nazevAkce VARCHAR(25) NOT NULL,
pocetKostymu INTEGER NOT NULL,
pocetDoplnku INTEGER NOT NULL,
datumZapujceni DATE NOT NULL,
datumVraceni DATE NOT NULL,
cenaVypujcky INTEGER NOT NULL,
id_kostym INTEGER NOT NULL,
id_doplnek INTEGER NULL,
id_zamestnanec INTEGER NOT NULL,
id_klient INTEGER NOT NULL,
PRIMARY KEY (id_zaznam)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE Zamestnanec(
id_zamestnanec INTEGER NOT NULL AUTO_INCREMENT,
jmeno VARCHAR(25) NOT NULL,
prijmeni VARCHAR(25) NOT NULL,
datumNarozeni DATE NOT NULL,
telefonniCislo VARCHAR(25) NOT NULL,
PRIMARY KEY (id_zamestnanec)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE Klient(
id_klient INTEGER NOT NULL AUTO_INCREMENT,
jmeno VARCHAR(25) NOT NULL,
prijmeni VARCHAR(25) NOT NULL,
datumNarozeni DATE NOT NULL,
telefonniCislo VARCHAR(25) NOT NULL,
adresa VARCHAR(50) NOT NULL,
PRIMARY KEY (id_klient)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE PravnickaOsoba(
id_klient INTEGER NOT NULL,
ICO INTEGER NOT NULL,
DIC INTEGER NOT NULL,
fakturacniAdresa VARCHAR(50) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;



ALTER TABLE Kostym ADD FOREIGN KEY (id_kategorie) REFERENCES Kategorie(id_kategorie);
ALTER TABLE Kostym ADD FOREIGN KEY (id_doplnek) REFERENCES Doplnek(id_doplnek);
ALTER TABLE Zaznam ADD FOREIGN KEY (id_kostym) REFERENCES Kostym(id_kostym);
ALTER TABLE Zaznam ADD FOREIGN KEY (id_doplnek) REFERENCES Doplnek(id_doplnek);
ALTER TABLE Zaznam ADD FOREIGN KEY (id_zamestnanec) REFERENCES Zamestnanec(id_zamestnanec);
ALTER TABLE Zaznam ADD FOREIGN KEY (id_klient) REFERENCES Klient(id_klient);
ALTER TABLE Kostym ADD FOREIGN KEY (id_zamestnanec) REFERENCES Zamestnanec(id_zamestnanec);
ALTER TABLE Doplnek ADD FOREIGN KEY (id_zamestnanec) REFERENCES Zamestnanec(id_zamestnanec);
ALTER TABLE PravnickaOsoba ADD FOREIGN KEY (id_klient) REFERENCES Klient(id_klient) ;

