#!/bin/bash
#Author Addshore, Petrb

if [ "`which php`" = "" ];then
    echo "You need to have php-cli installed for this"
    exit 2
fi

result=`find ../ -type f -name \*.php -exec php -l {} \; | grep "Errors parsing "`

#Flip the exit code
if [ x"$result" != x ]; then
	echo "$result"
else
	echo "No errors found"
fi
