#!/bin/bash

sudo chown -R www-data:www-data www/
sudo chmod g+w -R www/
sudo chmod g+s -R www/
sudo ln -s `realpath www` /var/www/whoshome

sudo ln apache2.conf /etc/apache2/sites-enabled/whoshome.conf
sudo service apache2 restart


