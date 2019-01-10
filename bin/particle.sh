#!/usr/bin/env bash
##
##
# particle.sh
#
#
# Run from root of the code repository.
#
##

CALLPATH=`dirname $0`
source "$CALLPATH/framework.sh"

cd /app/web/themes/custom/particle && \

if [ -d node_modules ]; then
  rm -rf node_modules
fi


echo "Installing Node Dependencies . . ."
npm install && \

echo "Setting up Pattern Lab"
npm run setup && \

echo "Building Particle for Drupal . . ."
npm run build:drupal

echo "~~~~~~~~ Particle Compiled in Lando!! ~~~~~~~~"

#exit 0
