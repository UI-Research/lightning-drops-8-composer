#!/usr/bin/env bash
##
##
# lando-quickstart.sh
#
#
# Run from root of the code repository.
#
##

CALLPATH=`dirname $0`
source "$CALLPATH/framework.sh"

echo "Installing Composer Dependencies . . ."
composer install && \
echo "Building theme for quickstart . . ."
source "$CALLPATH/particle.sh"

echo "~~~~~~~~ Modules and Themes are ready, now run lando pull --code=none ~~~~~~~~"

exit 0
