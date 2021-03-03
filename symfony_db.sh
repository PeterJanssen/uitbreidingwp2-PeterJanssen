#! /bin/bash --

# Script to drop and create the WP2 test database, run the migrations, and load the entity fixtures.
# Put this script in your personal bin/ folder on the server, relog in, and run from the CLI.
# Please, change the path in the first command to the path to your Symfony project source folder.

# Author:	Ben Merken
# Date:		November 4, 2019

cd /home/vagrant/Code
yes | php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
yes | php bin/console doctrine:migrations:migrate
yes | php bin/console doctrine:fixtures:load
