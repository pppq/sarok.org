# Sarok-in-a-box 👷‍♂️🚧📦

## What's in this fork?

Modifications required to run Sarok as a `docker-compose` stack for further development/refactoring.

These include:

- A [Dockerfile](.docker/Dockerfile) to build an application runtime image based on the official PHP 8.1 + Apache image, which installs necessary packages and PHP extensions along with `msmtp`, a sendmail-compatible binary
- A [docker-compose](./docker-compose.yml) file that includes the image above as well as MySQL 8.0 and mailcatcher for mail debugging purposes
- A [config.php](./config.php) with values already set to match the configuration in the two files above

## How do you run it?

You need to have `docker` and `docker-compose` installed (provided it is not already included in your distribution of Docker):

```console
$ docker-compose up -d

Building app
Sending build context to Docker daemon  18.24MB
Step 1/8 : FROM php:8.1-apache
 ---> 9f26c9013b57

...lots of building later...

Step 4/8 : COPY . /srv/app
 ---> 8e00c2ef5f1d
Step 5/8 : COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
 ---> 61600ed6b366
Step 6/8 : COPY .docker/msmtprc /etc/msmtprc
 ---> a14fde124a94
Step 7/8 : WORKDIR /srv/app
 ---> Running in b96d55d071dc
Removing intermediate container b96d55d071dc
 ---> 1c8422c6e672
Step 8/8 : RUN chown -R www-data:www-data /srv/app && a2enmod rewrite
 ---> Running in c306e3da1cb0
Enabling module rewrite.
To activate the new configuration, you need to run:
  service apache2 restart
Removing intermediate container c306e3da1cb0
 ---> bb136719c9c3
Successfully built bb136719c9c3
Successfully tagged sarok3:latest
Creating sarok_app_1         ... done
Creating sarok_mysql_1       ... done
Creating sarok_mailcatcher_1 ... done
```

For best results, also add an entry in `/etc/hosts` for the hostnames that are used:

```
127.0.0.1    lsarok.org www.lsarok.org img.lsarok.org
```

## What ports are in use?

- `80`: This is where Apache serves its standard requests (no https)
- `3306`: Allows you to inspect the contents of the MySQL database
- `1080`: Any mails sent are caught by `mailcatcher` and served here

Have fun! 🥳
