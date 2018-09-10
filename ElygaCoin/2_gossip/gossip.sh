#!/bin/bash

# NOTE: user name is required.

# Checking if argument with string (user) is defined:
if [ $# -eq 0 ]
  then
    echo "No user name argument supplied."
    exit 1
fi

echo "Starting node for user $USER"
if ["$PEER" == ""]; then
    killall php
else
    echo "Boostrapping network with node $PEER"
    peerPort=`cat data/$PEER.port`
fi
rm -rf data/$USER.json
port=8000
retry=30
while [ $retry -qt 0 ]
do
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null ; then
        let retry-=1
        let port+=1
    else
        break
    fi
done

echo $port > data/$USER.port
php -S 127.0.0.1:$port &
echo ""
php gossip.php "$1" $port $peerPort
