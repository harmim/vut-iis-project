SET NAMES utf8mb4;

ALTER DATABASE `vut_iis_project`
	DEFAULT CHARACTER SET utf8mb4
	COLLATE utf8mb4_unicode_520_ci;

USE `vut_iis_project`;


DROP TABLE IF EXISTS `zaznam`;
DROP TABLE IF EXISTS `doplnek`;
DROP TABLE IF EXISTS `kostym`;
DROP TABLE IF EXISTS `kategorie`;
DROP TABLE IF EXISTS `pravnicka_osoba`;
DROP TABLE IF EXISTS `klient`;
DROP TABLE IF EXISTS `zamestnanec`;
DROP TABLE IF EXISTS `uzivatel`;


CREATE TABLE `zamestnanec` (
	`id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`jmeno` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`prijmeni` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`datum_narozeni` DATE NOT NULL,
	`telefonni_cislo` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	PRIMARY KEY (`id`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


CREATE TABLE `klient` (
	`id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`jmeno` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`prijmeni` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`datum_narozeni` DATE NOT NULL,
	`telefonni_cislo` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`adresa` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	PRIMARY KEY (`id`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


CREATE TABLE `pravnicka_osoba` (
	`klient_id` INT UNSIGNED NOT NULL,
	`ico` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`dic` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`fakturacni_adresa` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	PRIMARY KEY (`klient_id`),
	CONSTRAINT `pravnicka_osoba_fk_klient` FOREIGN KEY (`klient_id`) REFERENCES `klient` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


CREATE TABLE `uzivatel` (
	`id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`typ` ENUM('admin', 'zamestnanec', 'klient') COLLATE utf8mb4_unicode_520_ci DEFAULT 'klient' NOT NULL,
	`email` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`heslo` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`aktivni` TINYINT(1) DEFAULT 1 NOT NULL,
	`zamestnanec_id` INT UNSIGNED DEFAULT NULL,
	`klient_id` INT UNSIGNED DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `email` (`email`),
	KEY `zamestnanec_id` (`zamestnanec_id`),
	KEY `klient_id` (`klient_id`),
	CONSTRAINT `uzivatel_fk_zamestnanec` FOREIGN KEY (`zamestnanec_id`) REFERENCES `zamestnanec` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	CONSTRAINT `uzivatel_fk_klient` FOREIGN KEY (`klient_id`) REFERENCES `klient` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


CREATE TABLE `kategorie` (
	`id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`nazev` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`popis` LONGTEXT COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	PRIMARY KEY (`id`)
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


CREATE TABLE `kostym` (
	`id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`vyrobce` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`material` VARCHAR(25) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`popis` LONGTEXT COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`cena` NUMERIC(15, 5) NOT NULL,
	`datum_vyroby` DATE NOT NULL,
	`opotrebeni` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`velikost` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`barva` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`dostupnost` ENUM('Na skladě', 'Nedostupné') COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`obrazek` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`aktivni` TINYINT(1) DEFAULT 1 NOT NULL,
	`kategorie_id` INT UNSIGNED NOT NULL,
	`zamestnanec_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY(`id`),
	KEY `kategorie_id` (`kategorie_id`),
	KEY zamestnanec_id (`zamestnanec_id`),
	CONSTRAINT `kostym_fk_kategorie` FOREIGN KEY (`kategorie_id`) REFERENCES `kategorie` (`id`)
		ON DELETE RESTRICT
		ON UPDATE CASCADE,
	CONSTRAINT `kostym_fk_zamestnanec` FOREIGN KEY (`zamestnanec_id`) REFERENCES `zamestnanec` (`id`)
		ON DELETE RESTRICT
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


CREATE TABLE `doplnek` (
	`id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`nazev` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`popis` LONGTEXT COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`datum_vyroby` DATE NOT NULL,
	`cena` NUMERIC(15, 5) NOT NULL,
	`dostupnost` ENUM('Na skladě', 'Nedostupné') COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`obrazek` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
	`aktivni` TINYINT(1) DEFAULT 1 NOT NULL,
	`zamestnanec_id` INT UNSIGNED NOT NULL,
	`kostym_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	KEY `zamestnanec_id` (`zamestnanec_id`),
	KEY `kostym_id` (`kostym_id`),
	CONSTRAINT `doplnek_fk_zamestnanec` FOREIGN KEY (`zamestnanec_id`) REFERENCES `zamestnanec` (`id`)
		ON DELETE RESTRICT
		ON UPDATE CASCADE,
	CONSTRAINT `doplnek_fk_kostym` FOREIGN KEY (`kostym_id`) REFERENCES `kostym` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


CREATE TABLE `zaznam` (
	`id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
	`nazev_akce` VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`datum_zapujceni` DATE NOT NULL,
	`datum_vraceni` DATE DEFAULT NULL,
	`cena` NUMERIC(15, 5) NOT NULL,
	`kostym_id` INT UNSIGNED DEFAULT NULL,
	`doplnek_id` INT UNSIGNED DEFAULT NULL,
	`zamestnanec_id` INT UNSIGNED DEFAULT NULL,
	`klient_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	KEY `kostym_id` (`kostym_id`),
	KEY `doplnek_id` (`doplnek_id`),
	KEY `zamestnanec_id` (`zamestnanec_id`),
	KEY `klient_id` (`klient_id`),
	CONSTRAINT `zaznam_fk_kostym` FOREIGN KEY (`kostym_id`) REFERENCES `kostym` (`id`)
		ON DELETE RESTRICT
		ON UPDATE CASCADE,
	CONSTRAINT `zaznam_fk_doplnek` FOREIGN KEY (`doplnek_id`) REFERENCES `doplnek` (`id`)
		ON DELETE RESTRICT
		ON UPDATE CASCADE,
	CONSTRAINT `zaznam_fk_zamestnanec` FOREIGN KEY (`zamestnanec_id`) REFERENCES `zamestnanec` (`id`)
		ON DELETE RESTRICT
		ON UPDATE CASCADE,
	CONSTRAINT `zaznam_fk_klient` FOREIGN KEY (`klient_id`) REFERENCES `klient` (`id`)
		ON DELETE RESTRICT
		ON UPDATE CASCADE
)
	ENGINE = InnoDB
	DEFAULT CHARSET = utf8mb4
	COLLATE = utf8mb4_unicode_520_ci;


/*--------------------------------------------------- test data ------------------------------------------------------*/


INSERT INTO `zamestnanec` (`id`, `jmeno`, `prijmeni`, `datum_narozeni`, `telefonni_cislo`)
VALUES
	(1, 'Jan', 'Novák', '1991-07-19', '+420911222444'),
	(2, 'Jakub', 'Slepý', '1981-07-19', '+420911222445'),
	(3, 'Pavel', 'Siska', '1971-07-19', '+420911222446');


INSERT INTO `klient` (`id`, `jmeno`, `prijmeni`, `datum_narozeni`, `telefonni_cislo`, `adresa`)
VALUES
	(1, 'Josef', 'Suba', '1992-07-19', '+420911222447', 'Pavlovice 25, Praha'),
	(2, 'Stanislav', 'Chorý', '1983-07-19', '+420911222448', 'Horská 44, Brno'),
	(3, 'Davis', 'Manas', '1974-07-19', '+420911222449', 'Česká 1, Brno');


INSERT INTO `pravnicka_osoba` (`klient_id`, `ico`, `dic`, `fakturacni_adresa`)
VALUES
	(2, '256542', 'CZ256542', 'Skopalíkova 123');


INSERT INTO `uzivatel` (`typ`, `email`, `heslo`, `zamestnanec_id`, `klient_id`)
VALUES
	('admin', 'admin@gmail.com', '$2a$10$qI4zamAo6tXFl0SOTa8Mm.7YdDpYoM6t2ikEoUK0LjQrRcM3v1WZS', NULL, NULL),
	('zamestnanec', 'novak@gmail.com', '$2a$10$qI4zamAo6tXFl0SOTa8Mm.7YdDpYoM6t2ikEoUK0LjQrRcM3v1WZS', 1, NULL),
	('zamestnanec', 'slepy@gmail.com', '$2a$10$qI4zamAo6tXFl0SOTa8Mm.7YdDpYoM6t2ikEoUK0LjQrRcM3v1WZS', 2, NULL),
	('zamestnanec', 'siska@gmail.com', '$2a$10$qI4zamAo6tXFl0SOTa8Mm.7YdDpYoM6t2ikEoUK0LjQrRcM3v1WZS', 3, NULL),
	('klient', 'suba@gmail.com', '$2a$10$qI4zamAo6tXFl0SOTa8Mm.7YdDpYoM6t2ikEoUK0LjQrRcM3v1WZS', NULL, 1),
	('klient', 'chory@gmail.com', '$2a$10$qI4zamAo6tXFl0SOTa8Mm.7YdDpYoM6t2ikEoUK0LjQrRcM3v1WZS', NULL, 2),
	('klient', 'manas@gmail.com', '$2a$10$qI4zamAo6tXFl0SOTa8Mm.7YdDpYoM6t2ikEoUK0LjQrRcM3v1WZS', NULL, 3);


INSERT INTO `kategorie` (`id`, `nazev`, `popis`)
VALUES
	(1, 'Pro děti - pohádky', 'Kostýmy pohádkových bytostí.'),
	(2, 'Pro dospělé - filmy', 'Kostýmy oblíbených filmových postav.'),
	(3, 'Pro ženy', 'Kostýmy vyhrazené pro ženy.');


INSERT INTO `kostym` (`id`, `vyrobce`, `material`, `popis`, `cena`, `datum_vyroby`, `opotrebeni`, `velikost`, `barva`, `dostupnost`, `kategorie_id`, `zamestnanec_id`)
VALUES
	(1, 'DressMe', 'Bavlna', 'Srandovní klaunský kostým pro děti.', 1100, '2018-01-01', 'Značné', 'L', 'Vícebarevné', 'Na skladě', 1, 3),
	(2, 'DressMe', 'Poly', 'Princezna.', 1000, '2018-01-02', 'Zachovalé', 'S', 'Žlutá', 'Na skladě', 1, 2),
	(3, 'DressMe', 'Poly', 'Superman.', 1600, '2018-01-03', 'Zachovalé', 'L', 'Modrá', 'Nedostupné', 2, 2),
	(4, 'DressMe', 'Poly', 'Pokahontas.', 1600, '2018-01-04', 'Zachovalé', 'M', 'Hnědá', 'Nedostupné', 3, 1),
	(5, 'Kostýmy na míru', 'Bavlna', 'Loupežník.', 1000, '2018-01-05', 'Značné', 'L', 'Hnědá', 'Na skladě', 1, 1),
	(6, 'Kostýmy na míru', 'Bavlna', 'Princezna.', 1000, '2018-01-06', 'Zachovalé', 'M', 'Červená', 'Na skladě', 1, 2),
	(7, 'Kostýmy s.r.o.', 'Poly', 'Batman.', 1100, '2018-01-01', 'Značné', 'XL', 'Černá', 'Na skladě', 2, 1);


INSERT INTO `doplnek` (`id`, `nazev`, `popis`, `datum_vyroby`, `cena`, `dostupnost`, `zamestnanec_id`, `kostym_id`)
VALUES
	(1, 'Pirátský klobuk', 'Černý klobou s pirátským motivem.', '2018-01-01', 1300, 'Na skladě', 2, 2),
	(2, 'Poľovnícky klobou', 'Zelený klobou.', '2018-01-02', 100, 'Na skladě', 2, 1),
	(3, 'Zlatý řetízek', 'Obyčejný pozlacený řetízek.', '2018-01-03', 900, 'Na skladě', 3, 1),
	(4, 'Dřevenné sluneční brýle', 'Dřevenné sluneční brýle s černým sklem.', '2018-01-04', 500, 'Na skladě', 1, 3);


INSERT INTO `zaznam` (`nazev_akce`, `datum_zapujceni`, `datum_vraceni`, `cena`, `kostym_id`, `doplnek_id`, `zamestnanec_id`, `klient_id`)
VALUES
	('Kácení máje', '2018-01-01', '2018-02-02', 1100, 1, NULL, 1, 3),
	('Maškarní bál', '2018-01-02', NULL, 1600, 3, NULL, 2, 2),
	('Divadelní hra', '2018-01-03', '2018-02-04', 1600, 4, NULL, 3, 1),
	('Kácení máje', '2018-01-04', NULL, 100, NULL, 2, 3, 1),
	('Párty ve škole', '2018-01-05', '2018-02-06', 1100, 7, NULL, 1, 1),
	('Párty ve škole', '2018-01-07', NULL, 1000, 6, NULL, NULL, 3),
	('Kácení máje', '2018-01-09', '2018-02-10', 1600, 3, NULL, 2, 2),
	('Ples', '2018-01-11', '2018-02-12', 500, NULL, 4, NULL, 2);
