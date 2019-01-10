#!/usr/bin/env bash
##
##
# clean.sh
#
#
# Run from root of the code repository.
#
##

CALLPATH=`dirname $0`
source "$CALLPATH/framework.sh"

cd /app

for f in $(cat $CALLPATH/clean.txt) ; do
  rm -rf "$f"
done

echo "~~~~~~~~ All Clean! ~~~~~~~~"

exit 0
