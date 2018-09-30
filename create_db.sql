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

---------------------------------------------------test data-----------------------------------------------------------------------------------------

INSERT INTO Zamestnanec (jmeno, prijmeni, datumNarozeni, telefonniCislo) VALUES('Jan', 'Novak', '1995-12-10', '+420911222444');
INSERT INTO Zamestnanec (jmeno, prijmeni, datumNarozeni, telefonniCislo) VALUES('Jakub', 'Slepy', '1991-02-10', '+420911222444');
INSERT INTO Zamestnanec (jmeno, prijmeni, datumNarozeni, telefonniCislo) VALUES('Pavel', 'Siska', '1999-04-01', '+420911222444');

INSERT INTO Klient (jmeno, prijmeni, datumNarozeni, telefonniCislo, adresa) VALUES('Jozef', 'Suba', '1991-01-01', '+420911222444', 'Pavlovice 25');
INSERT INTO Klient (jmeno, prijmeni, datumNarozeni, telefonniCislo, adresa) VALUES('Stanislav', 'Chory', '1992-01-01', '+420911222444', 'Tøinec, Sládkova 25');
INSERT INTO Klient (jmeno, prijmeni, datumNarozeni, telefonniCislo, adresa) VALUES('David', 'Manas', '1993-01-01', '+420911222444', 'Køížany 25');

INSERT INTO PravnickaOsoba (id_klient, ICO, DIC, fakturacniAdresa) VALUES('3', '256542', '652356', 'Skopalikova 1231');

INSERT INTO Doplnek (nazev, popis, datumVyroby, cena, dostupnost, id_zamestnanec) VALUES('piratsky klobuk', 'cierny klobuk s piratkym motivom', '2018-01-01', '1300', 'Na Sklade', '1');
INSERT INTO Doplnek (nazev, popis, datumVyroby, cena, dostupnost, id_zamestnanec) VALUES('polovnicky klobuk', 'zeleny klobuk', '2018-01-01', '100', 'Na Sklade', '2');
INSERT INTO Doplnek (nazev, popis, datumVyroby, cena, dostupnost, id_zamestnanec) VALUES('zlaty retizek', 'Obycejny pozlaceny retizek', '2018-01-01', '900', 'Na Sklade', '1');
INSERT INTO Doplnek (nazev, popis, datumVyroby, cena, dostupnost, id_zamestnanec) VALUES('drevenne slunecny bryle', 'ram blryli ej z dreva, cerne skla', '2018-01-01', '500', 'Na Sklade', '1');

INSERT INTO Kategorie (nazev, popis) VALUES('Pro deti - pohadky', 'Kostymy pohadkovych bytosti');
INSERT INTO Kategorie (nazev, popis) VALUES('Pro dospele - filmy', 'Kostymy oblibenych filmovych postav');
INSERT INTO Kategorie (nazev, popis) VALUES('Pro - zeny', 'Kostymy vyhradne pro zeny');

INSERT INTO Kostym (vyrobce, material, popis, cena, datumVyroby, opotrebeni, velikost, barva, dostupnost, id_kategorie, id_doplnek, id_zamestnanec) VALUES('DressMe', 'Bavlna', 'Srandovni klaunsky kostym pro deti', '1100', '2018-01-01', 'Znacne', 'L', 'Vicebarevne', 'Na Sklade', '1', '2', '3');
INSERT INTO Kostym (vyrobce, material, popis, cena, datumVyroby, opotrebeni, velikost, barva, dostupnost, id_kategorie, id_doplnek, id_zamestnanec) VALUES('DressMe', 'Poly', 'Princezna', '1000', '2018-01-01', 'Zachovale', 'S', 'Zluta', 'Na Sklade', '1', '1', '1');
INSERT INTO Kostym (vyrobce, material, popis, cena, datumVyroby, opotrebeni, velikost, barva, dostupnost, id_kategorie, id_doplnek, id_zamestnanec) VALUES('DressMe', 'Poly', 'Superman', '1600', '2018-01-01', 'Zachovale', 'L', 'Modra', 'Nedostupne', '2', NULL, '1');
INSERT INTO Kostym (vyrobce, material, popis, cena, datumVyroby, opotrebeni, velikost, barva, dostupnost, id_kategorie, id_doplnek, id_zamestnanec) VALUES('DressMe', 'Poly', 'Pokahontas', '1600', '2018-01-01', 'Zachovale', 'M', 'Hneda', 'Nedostupne', '3', NULL, '1');
INSERT INTO Kostym (vyrobce, material, popis, cena, datumVyroby, opotrebeni, velikost, barva, dostupnost, id_kategorie, id_doplnek, id_zamestnanec) VALUES('Kostymy na miru', 'Bavlna', 'Loupeznik', '1000', '2018-01-01', 'Znacne', 'L', 'Hneda', 'Na Sklade', '1', NULL, '3');
INSERT INTO Kostym (vyrobce, material, popis, cena, datumVyroby, opotrebeni, velikost, barva, dostupnost, id_kategorie, id_doplnek, id_zamestnanec) VALUES('Kostymy na miru', 'Bavlna', 'Princezna', '1000', '2018-01-01', 'Zachovale', 'M', 'Cervena', 'Na Sklade', '1', '1', '2');
INSERT INTO Kostym (vyrobce, material, popis, cena, datumVyroby, opotrebeni, velikost, barva, dostupnost, id_kategorie, id_doplnek, id_zamestnanec) VALUES('Kostymy s.r.o.', 'Poly', 'Batman', '1100', '2018-01-01', 'Znacne', 'XL', 'Cerna', 'Na Sklade', '2', '1', '1');


INSERT INTO Zaznam (nazevAkce, pocetKostymu, pocetDoplnku, datumZapujceni, datumVraceni, cenaVypujcky, id_kostym, id_doplnek, id_zamestnanec, id_klient) VALUES('Kácení máje', '1', '1', '2018-01-01', '2018-02-02', '2500', '1', '1', '1', '1');
INSERT INTO Zaznam (nazevAkce, pocetKostymu, pocetDoplnku, datumZapujceni, datumVraceni, cenaVypujcky, id_kostym, id_doplnek, id_zamestnanec, id_klient) VALUES('Maškarní bál', '1', '0', '2018-01-01', '2018-02-02', '2500', '1', '1', '2', '2');
INSERT INTO Zaznam (nazevAkce, pocetKostymu, pocetDoplnku, datumZapujceni, datumVraceni, cenaVypujcky, id_kostym, id_doplnek, id_zamestnanec, id_klient) VALUES('Divadelní hra', '1', '0', '2018-01-01', '2018-02-02', '1600', '3', NULL, '1', '2');
INSERT INTO Zaznam (nazevAkce, pocetKostymu, pocetDoplnku, datumZapujceni, datumVraceni, cenaVypujcky, id_kostym, id_doplnek, id_zamestnanec, id_klient) VALUES('Kácení máje', '1', '0', '2018-01-01', '2018-02-02', '2500', '2', '1', '2', '3');
INSERT INTO Zaznam (nazevAkce, pocetKostymu, pocetDoplnku, datumZapujceni, datumVraceni, cenaVypujcky, id_kostym, id_doplnek, id_zamestnanec, id_klient) VALUES('Párty ve škole', '1', '0', '2018-01-01', '2018-02-02', '1100', '7', NULL, '3', '2');
INSERT INTO Zaznam (nazevAkce, pocetKostymu, pocetDoplnku, datumZapujceni, datumVraceni, cenaVypujcky, id_kostym, id_doplnek, id_zamestnanec, id_klient) VALUES('Párty ve škole', '1', '0', '2018-01-01', '2018-02-02', '1600', '3', NULL, '3', '1');
INSERT INTO Zaznam (nazevAkce, pocetKostymu, pocetDoplnku, datumZapujceni, datumVraceni, cenaVypujcky, id_kostym, id_doplnek, id_zamestnanec, id_klient) VALUES('Kácení máje', '1', '0', '2018-01-01', '2018-02-02', '2500', '3', '4', '1', '3');
INSERT INTO Zaznam (nazevAkce, pocetKostymu, pocetDoplnku, datumZapujceni, datumVraceni, cenaVypujcky, id_kostym, id_doplnek, id_zamestnanec, id_klient) VALUES('Ples', '1', '0', '2018-01-01', '2018-02-02', '1600', '3', NULL, '3', '2');
