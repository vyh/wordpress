#!/usr/bin/env bash

# Get a free SSL certificate for an AWS Lightsail/Wordpress (Bitnami) instance
# condensed from https://metablogue.com/enable-lets-encrypt-ssl-aws-lightsail/ to a script
# You should already have your domain & your DNS set up.
# This is interactive; you will be prompted for confirmation or other input on some commands.

# General repository update and cleanup
sudo apt-get update
sudo apt-get autoclean

# Create and nav to directory for Let's Encrypt
sudo mkdir /opt/bitnami/letsencrypt
cd /opt/bitnami/letsencrypt

# Download certbot-auto into the new directory and set it to be executable.
sudo wget https://dl.eff.org/certbot-auto
sudo chmod a+x ./certbot-auto

# Run it once; it asked me for my domain, tried something, then spat out some errors,
# but the next command (to generate a certificate) worked.
# I have not dug into what this does or what the errors were.
sudo ./certbot-auto

# Generate certificate; replace DOMAIN with your domain. You can add more -d flags for more domains;
# you probably want to include your domain both with and without the 'www' subdomain.
sudo ./certbot-auto certonly --webroot -w /opt/bitnami/apps/wordpress/htdocs/ -d DOMAIN

# Symlink from default Apache/Bitnami certificate location to Let's Encrypt location
# (first checks if the default files exist and renames them if so).
# Again, replace DOMAIN with your domain.
default_file="/opt/bitnami/apache2/conf/server"
for ext in key crt
do
  if [ -e "${default_file}.${ext}" ]; then mv "${default_file}.${ext}" "${default_file}.${ext}.old"; fi
done
sudo ln -s /etc/letsencrypt/live/DOMAIN/fullchain.pem "${default_file}.crt"
sudo ln -s /etc/letsencrypt/live/DOMAIN/privkey.pem "${default_file}.key"

# Restart the server
sudo /opt/bitnami/ctlscript.sh restart apache

# You then can redirect http -> https so no one visits the unsecure version.
# That involves editing existing files, so follow the instructions at the url above for that.
# Note: it gives NO hint where to find wp-config.php; try in ~/apps/wordpress/htdocs/
