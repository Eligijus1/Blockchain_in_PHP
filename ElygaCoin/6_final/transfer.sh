#!/bin/bash
clear
echo "Transferring $3 ElygaCoins from $1 to $2"
wget -q -O - http://localhost:`cat data/$1.port`/transfer --post-data "from=$1&to=$2&amount=$3"
