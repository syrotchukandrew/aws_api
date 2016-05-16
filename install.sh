#!/bin/bash

clear
echo "Choose clue :"
echo "1 - installing project"
echo "2 - exit"

read Clue

case "$Clue" in
1) echo "start installing project..."
    npm install
    npm install pgwslideshow
    composer install --verbose
    ./node_modules/.bin/bower install
    ./node_modules/.bin/gulp
    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force
;;

4) exit 0
;;
esac

exit 0