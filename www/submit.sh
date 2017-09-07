#!/bin/bash

URL=[CHANGEME]/submit.php
USER=`whoami`

if [ $# -ne 1 ]; then
	echo "Usage: $0 <file.c>"
	exit 0;
elif [ ! -f $1 ]; then
	echo "Cant read $1"
	exit 0;
fi


read -s -p "Password: " PASS
echo ""

curl $URL --user $USER:$PASS --form "code=@$1"

