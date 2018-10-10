#!/bin/bash

echo "Transferring $3 ElygaCoins from $1 to $2"

# http --form post :`cat data/$1.port`/transfer to=`cat data/$2.port` amount=$3

# wget -q -O - http://localhost:`cat data/$1.port`/gossip --post-data "user=Dalia"
