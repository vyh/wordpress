#!/usr/bin/env bash

# Renew a free SSL certificate from Let's Encrypt that was already generated via certbot-auto.
# Change the first line to cd to the location of certbot-auto if it's in a different directory.
# Add --dry-run to the renew command (after renew) to test.
# Writes output of latest attempt to ~/certbot-auto-renew.log.

# Set this file as an executable (chmod +x ssl_renew.sh); run manually or set a cron job with
# crontab -e
# and add a line like
# 0 6 * * 1 ./ssl_renew.sh
# which runs ~/ssl_renew.sh weekly, Monday at 6:00 am UTC

cd /opt/bitnami/letsencrypt
sudo ./certbot-auto renew > /home/bitnami/certbot-auto-renew.log
sudo /opt/bitnami/ctlscript.sh restart apache
