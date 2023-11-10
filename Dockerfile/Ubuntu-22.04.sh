#!/bin/bash
docker ps -aq | xargs docker stop | xargs docker rm
docker system prune -a -f
docker image prune -a -f
rm -f Dockerfile
cp Dockerfile_Ubuntu-22.04 Dockerfile
min=999999999999
max=99999999999999
rnd=$((SRANDOM % ( max - min + 1 ) + min))
docker build -t "$rnd" .
docker run -d -p 222:22 -p 25500:25500 -p 80:80 -p 8080:8080 -p 443:443 -p 444:444 "$rnd"
