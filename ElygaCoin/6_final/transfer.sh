#!/bin/bash
clear
echo "Transferring $3 ElygaCoins from $1 to $2"

#http --form post :`cat data/$1.port`/transfer to=`cat data/$2.port` amount=$3
#wget -q -O - http://localhost:`cat data/$1.port`/transfer --post-data "from=$1&to=$2&amount=$3"
#wget -q -O - http://localhost:`cat data/$1.port`/transfer --post-data "from=`cat data/$1.pub`&to=`cat data/$2.pub`&amount=$3"
wget -q -O - http://localhost:`cat data/$1.port`/transfer --header="Content-Type: application/x-www-form-urlencoded" --post-data="from=`cat data/$1.pub`&to=`cat data/$2.pub`&amount=$3&fromName=$1&toName=$2"


