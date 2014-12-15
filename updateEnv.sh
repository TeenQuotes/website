#!/bin/bash
# Remove old files
rm -f .env.codeception.php
rm -f .env.testing.php
rm -f .env.codeceptionSearch.php

# Create new files
cp .env.local.php .env.codeception.php
cp .env.local.php .env.testing.php
cp .env.local.php .env.codeceptionSearch.php

# Upload to production
scp .env.local.php root@v3.tq:/var/www/website/.env.production.php
ssh root@v3.tq "chown www-data:www-data /var/www/website/.env.production.php"