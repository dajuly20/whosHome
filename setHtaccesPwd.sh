#!/bin/bash
read -p "Please enter username to add: " user
touch .htpasswd
htpasswd  ./.htpasswd $user
