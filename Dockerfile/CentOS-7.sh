#!/bin/bash
docker ps -aq | xargs docker stop | xargs docker rm
docker system prune -a -f
docker image prune -a -f
rm -f Dockerfile
cp Dockerfile_CentOS-7 Dockerfile
min=999999999999
max=99999999999999
rnd=$((SRANDOM % ( max - min + 1 ) + min))
docker build -t "$rnd" .
docker run -d -p 222:22 -p 25500:25500 -p 80:80 -p 8080:8080 -p 443:443 -p 444:444 "$rnd"
sshpass -p Ash82qc44L6ZVv /usr/bin/ssh -o StrictHostKeyChecking=no -p 222 root@127.0.0.1 service mariadb restart
sshpass -p Ash82qc44L6ZVv /usr/bin/ssh -o StrictHostKeyChecking=no -p 222 root@127.0.0.1 /bin/bash /home/xtreamcodes/iptv_xtream_codes/start_services.sh
