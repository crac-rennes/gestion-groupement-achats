# Groupements d'achats - Installation

## Extract the files to the server

## Configure the server (NGINX)

    server {

      root /home/www/crac.notmyidea.org;
      index index.php index.html index.htm;

      server_name crac.notmyidea.org;

      location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
      }
    }

### Configure SSL with Let's Encrypt

Optionally, you can configure the SSL certificates via Let's Encrypt. On a debian machine, this will do:

    $ sudo certbot --nginx

## Configure the database

First, create the user:

    $ sudo mysql -u root
    $ GRANT ALL PRIVILEGES ON crac.* TO 'crac'@'localhost' IDENTIFIED BY 'crac';

And then import the initial database

    $ mysql -u crac -p crac < init.sql
    
# Configure the install

Go and edit the `variables.php` file and setup the configuration for your needs.
