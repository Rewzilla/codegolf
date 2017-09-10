#!/bin/bash

URL=[CHANGEME]/submit.php

if [ $# -ne 1 ]; then
	echo "Usage: $0 <file.c>"
	exit 0;
elif [ ! -f $1 ]; then
	echo "Cant read $1"
	exit 0;
fi

read -p "Username: " USER
echo ""

curl "$URL" --form "user=$USER&code=@$1"

