#!/bin/bash
# Upload to production
scp .env root@v3.tq:/var/www/website/.env
ssh root@v3.tq "chown www-data:www-data /var/www/website/.env"