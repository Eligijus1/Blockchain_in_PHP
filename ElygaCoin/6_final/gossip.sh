#!/bin/bash

# NOTE: user name is required and $peer is required.
# Examples:
# execute with initial node Eligijus:  ./gossip.sh Eligijus
# execute user Petras with pear Eligijus example: ./gossip.sh Petras Eligijus

# Checking if argument with string (user) is defined:
if [ $# -eq 0 ]
  then
    echo "No user name argument supplied."
    exit 1
fi

PEER=$2

echo "Starting node for user $1 with peer $PEER"

if ["$PEER" == ""]; then
    killall php
else
    echo "Boostrapping network with node $PEER"
    peerPort=`cat data/$PEER.port`
fi

rm -rf data/$1.json
port=8000
retry=30
COUNTER=0

while [ $COUNTER -lt 30 ]; do
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null ; then
        let COUNTER=COUNTER+1 
        let retry-=1
        let port+=1
    else
        break
    fi
done

echo $port > data/$1.port
echo $1 > data/$port.user
php -S 127.0.0.1:$port &
echo ""
php gossip.php $1 $port $peerPort
