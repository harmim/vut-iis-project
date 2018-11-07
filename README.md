# Informační systémy - Projekt
## Půjčovna kostýmů


### Autoři
- Dominik Harmim <xharmi00@stud.fit.vutbr.cz>
- Matúš Burzala <xburza00@stud.fit.vutbr.cz>


### Dokumentace
Dokumentace projektu se nachází v ... **TODO**.


### Struktura projektu
- `./app/` Jádro systému. PHP skripty a třídy, konfigurační NEON soubory. Latte šablony.
  * `./app/config/` Konfigurační NEON soubory systému.
  * `./app/*Module/` Jednotlivé moduly systému. Tento adresář obsahuje konfigurační NEON soubor pro daný modul,
    adresář `Controls` pro komponenty, `Model` pro modelové třídy, `Presenters` pro presentery (kontrolery) a
    `templates` pro Latte šablony.
  * `./app/bootstrap.php` výchozí skript s nastavením konfigrace celého systému.
- `./doc/` Dokumentace.
- `./docker/` Nastavení Docker.
- `./libs/` PHP třídy, které modifikují nebo rozšiřují chování Nette framework.
- `./log/` Chybové záznamy systému ("logy").
- `./node_modules/` JavaScript knihovny nainstalované přes NPM. Slouží k nastavení a stahování JavaScript a CSS
  závislostí systému.
- `./sql/` SQL skripty.
  * `./sql/create_db.sql` Skript pro inicializaci databáze.
- `./temp/` Dočasné soubory systému.
- `./vednor/` PHP knihovny nainstalované přes Composer.
- `./www/` Kořenový adresář přístupný z webu.
  * `./www/img/` Obrázky.
  * `./www/js/` JavaScript skripty.
  * `./www/styles/` CSS soubory.
  * `./www/index.php` Výchozí PHP skript spuštěný při spuštění systému.
- `./.bowerrc` Konfigurace nástroje Bower použitého na stahování JavaScript a CSS závislostí systému.
- `./.gitignore` Ignorované soubory verzovacím systémem Git.
- `./.htaccess` Výchozí konfigurace webového serveru Apache pro celý systém.
- `./bower.json` Definice používaných JavaScript a CSS knihoven nástrojem Bower.
- `./coding-standard.yml` Nastavení nástroje `nette/coding-standard` pro kontrolu stylu PHP kódu.
- `./composer.json` Definice používaných PHP knihoven, verze PHP a způsobu automatického načítání PHP souborů
  nástrojem Composer.
- `./composer.lock` Pomocný soubor pro nástroj Composer.
- `./docker-compose.override.sample.yml` Vzorový konfigurační soubor pro lokální modifikaci konfigurace Docker.
- `./docker-compose.yml` Konfigurační soubor pro Docker.
- `./Gruntfile.js` JavaScript skript pro správu a stahování JavaScript a CSS závislostí systému.
  (Konfigurační soubor nástroje Grunt.)
- `./LICENSE` Licence.
- `./Makefile` Soubor pro správu systému programem make.
- `./package.json` Definice používaných JavaScript knihoven pro stahování JavaScript a CSS závislostí systému
  nástrojem NPM.
- `./package-lock.json` Pomocný soubor pro nástroj NPM.
- `./phpstan.neon` Nastavení nástroje phpstan pro statickou analýzu PHP kódu.
- `./README.md` README se základními informacemi o systému.


### Požadavky (obecné)
- [Git](https://git-scm.com/downloads). (Pro stažení repositáře.)

#### Požadavky (bez použití Docker)
- Webový server, např. [Apache](http://httpd.apache.org/download.cgi) nebo [Nginx](http://nginx.org/en/download.html) +
[MySQL](https://www.mysql.com/downloads) nebo [MariaDB](https://mariadb.org/download) +
[PHP](http://php.net/downloads.php) >= 7.2. Možno nainstalovat zvlášť nebo nainstalovat
[XAMPP](https://www.apachefriends.org/download.html) nebo třeba [WAMP](http://www.wampserver.com/en).
- [Composer](https://getcomposer.org/download). (Pro správu PHP zavislostí.)
- [NodeJS](https://nodejs.org/en/download). (Pro správu JavaScript balíčků pro stahování JavaScript a CSS knihoven.)

#### Požadavky (s použitím Docker)
- [Docker](https://www.docker.com/products/docker-engine#/download) nebo přes
[Docker Desktop](https://www.docker.com/products/docker-desktop).
- [Docker Compose](https://docs.docker.com/compose/install/#install-compose).


### Nastavení (obecné)

#### Nastavení DNS
- Doména `vut-iis-project.localhost.com` musí směrovat na localhost (127.0.0.1).
Lze to udělat např. editací souboru `/etc/hosts`, respektive (`C:\Windows\System32\Drivers\etc\hosts`).
Nebo je možné použít program [`dnsmasq`](http://www.thekelleys.org.uk/dnsmasq/doc.html) kde je možné
nastavit, aby celé skupiny domén směrovaly na localhost, např. všechny domény, které mají ve svém
názvu řetězec `localhost`. [Návod pro Mac](https://getgrav.org/blog/macos-mojave-apache-mysql-vhost-apc).
Nebo je také možné nainstalovat si `dnsmasq` přes Docker.

#### Stažení repositáře
Přes SSH
```
$ git clone git@github.com:harmim/vut-iis-project.git ~/cesta/k/repositari
```
nebo přes HTTPS.
```
$ git clone https://github.com/harmim/vut-iis-project.git ~/cesta/k/repositari
```


### Nastavení (bez použití Docker)

#### Nastavení virtual host
V konfiguračím souboru Apache, např. `/usr/local/etc/httpd/extra/httpd-vhosts.conf`.
```conf
<VirtualHost *:80>
	ServerName vut-iis-project.localhost.com
	DocumentRoot "~/cesta/k/repositari"
	SetEnv IIS_DEBUG 1
</VirtualHost>
```


### Nastavení (s použitím Docker)

#### Nginx-proxy
Nastavení Nginx proxy serveru, přes který se budeme připojovat k systému a který bude proxy na Apach server.

1. Vytvoření souboru `~/docker-compose.yml` např. v domovském adresáři:
```yml
version: '3'

services:
    nginx-proxy:
        image: jwilder/nginx-proxy
        ports:
            - 80:80
        volumes:
            - /var/run/docker.sock:/tmp/docker.sock:ro
        restart: always

networks:
    default:
      external:
        name: nginx-proxy
```

2.
```
$ docker network create nginx-proxy
```

3.
```
$ (cd ~ && docker-compose up -d)
```
Tato Nginx proxy se teď bude spouštět vždy po restartu Docker automaticky.


### Instalace (bez použití Docker)
```
$ make DOCKER=0
```

#### Nastavení vlastní databáze
1.
```
$ cp app/config/local.sample.neon app/config/local.neon
```

2. Nastavení údajů k vlastní databázi v souboru `app/config/loocal.neon`.


### Instalace (s použitím docker)
1.
```
$ cp docker-compose.override.sample.yml docker-compose.override.yml
```

2. Změna konfigurace v souboru `docker-compose.override.yml`, např. nastavení `XDEBUG_CONFIG` na
`docker.for.win.localhost` pro Windows.

3.
```
$ docker-compose up -d
```
(pro vypnutí Docker kontejneru `$ docker-compose down`)

4.
```
$ make
```


### Make příkazy
U každého příkazu je možné uvést `DOCKER=1/0` pro používání/nepoužívaní Docker, např. `$ make install DOCKER=0`.
Výchozí hodnota je `1`.

U kažédho příkazu je možné uvést `PRODUCTION=1/0` pro nastavení knihoven pro produkční/vývojový server,
např. `$ make install PRODUCTION=1`. Výchozí hodnota je `0`.

- `install` `composer` + `assets`. Výchozí cíl.
- `composer` Instalace PHP knihoven a vygenerování souboru pro automatické načítání PHP souborů.
- `assets` `npm` + `bower` + `grunt`.
- `npm` Instalace JavaScript knihoven přes NPM pro nastavování a stahování JavaScript a CSS knihoven systému.
- `bower` Instalace JavaScript a CSS knihoven přes Bower.
- `grunt` Kopírování a nastavování (minifikace, aj.) JavaScript a CSS knihoven systému.
- `code-checker` Spuštění kontroly validity PHP kódu.
- `coding-standard` Spuštění kontroly stylu PHP kódu.
- `phpstan` Spuštění statické analýzy PHP kódu.
- `clean` Odstranení všech dočasných souborů.
- `clean-cache` Ostranění dočasných souborů Nette framework.
