# Informační systémy - Projekt
## Půjčovna kostýmů


#### Autoři:
- Dominik Harmim <xharmi00@stud.fit.vutbr.cz>
- Matúš Burzala <xburza00@stud.fit.vutbr.cz>


### Požadavky (obecné):
- [Git](https://git-scm.com/downloads). (Pro stažení repositáře.)


### Požadavky (bez použití Docker):
- Webový server, např. [Apache](http://httpd.apache.org/download.cgi) nebo [Nginx](http://nginx.org/en/download.html) +
[MySQL](https://www.mysql.com/downloads) nebo [MariaDB](https://mariadb.org/download) +
[PHP](http://php.net/downloads.php#v7.2.10) >= 7.2. Možno nainstalovat zvlášť nebo nainstalovat
[XAMPP](https://www.apachefriends.org/download.html) nebo třeba [WAMP](http://www.wampserver.com/en).
- [Composer](https://getcomposer.org/download). (Pro správu PHP zavislostí.)
- [NodeJS](https://nodejs.org/en/download). (Pro správu JS balíčků pro práci s CSS JS.)


### Požadavky (s použitím Docker):
- [Docker](https://www.docker.com/products/docker-engine#/download) nebo přes
[Docker Desktop](https://www.docker.com/products/docker-desktop).
- [Docker Compose](https://docs.docker.com/compose/install/#install-compose).


### Nastavení (obecné):

##### Nastavení DNS:
- Doména `vut-iis-project.localhost.com` musí směrovat na localhost (127.0.0.1).
Lze to udělat např. editací souboru `/etc/hosts`, respektive (C:\Windows\System32\Drivers\etc\hosts).
Nebo je možné použít program [`dnsmasq`](http://www.thekelleys.org.uk/dnsmasq/doc.html) kde je možné
nastavit, aby celé skupiny domén směrovaly na localhost, např. všechny domény, které mají ve svém
názvu řetězec `localhost`. [Návod pro Mac](https://getgrav.org/blog/macos-mojave-apache-mysql-vhost-apc).
Nebo je také možné nainstalovat si `dnsmasq` přes Docker.

##### Stažení repositáře:
Přes SSH:
```
$ git clone git@github.com:harmim/vut-iis-project.git ~/cesta/k/repositari
```
nebo přes HTTPS:
```
$ git clone https://github.com/harmim/vut-iis-project.git ~/cesta/k/repositari
```


### Nastavení (bez použití Docker):

##### Nastavení virtual host:
V konfiguračím souboru Apache, např. `/usr/local/etc/httpd/extra/httpd-vhosts.conf`.
```conf
<VirtualHost *:80>
	ServerName vut-iis-project.localhost.com
	DocumentRoot "~/cesta/k/repositari"
	SetEnv IIS_DEBUG 1
</VirtualHost>
```


### Nastavení (s použitím Docker):

##### Nginx-proxy:
Nastavení Nginx proxy serveru, přes který se budeme připojovat k aplikaci a který bude proxy na Apach server.

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
cd ~ && docker-compose up -d
```

Tato Nginx proxy se teď bude spouštět vždy po restartu Docker automaticky.


### Instalace (bez použití Docker):

