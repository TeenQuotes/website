#!/bin/bash
# Upload to production
scp .env root@production.tq:/var/www/website/.env
ssh root@production.tq "chown www-data:www-data /var/www/website/.env"

# Upload to staging
scp .env admin@staging.tq:/home/admin/.env
ssh admin@staging.tq "sudo mv -f /home/admin/.env /var/www/website/.env;sudo chown www-data:www-data /var/www/website/.env"