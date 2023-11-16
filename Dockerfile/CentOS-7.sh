#!/bin/bash
docker ps -aq | xargs docker stop | xargs docker rm
docker system prune -a -f
docker image prune -a -f
min=999999999999
max=99999999999999
rnd=$((SRANDOM % ( max - min + 1 ) + min))
docker build -t "$rnd" -f Dockerfile_CentOS-7 .
docker run -d -p 222:22 "$rnd"
rm -f $HOME/.ssh/known_hosts
