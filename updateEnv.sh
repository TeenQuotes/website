#!/bin/bash
# Upload to production
scp .env root@production.tq:/var/www/website/.env
ssh root@production.tq "chown www-data:www-data /var/www/website/.env"