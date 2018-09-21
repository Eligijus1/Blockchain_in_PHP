#!/bin/bash
echo "PEER: $PEER"
retry=30
echo "Debug 1: \$retry is '$retry'"


COUNTER=0
while [ $COUNTER -lt 10 ]; do
 echo The counter is $COUNTER and retry is $retry
 let retry=retry-1
 let COUNTER=COUNTER+1 
done


#while [ $retry -qt 0 ]; do
#    echo "Starting node for user $retry"
#    let retry=retry-1
#done

