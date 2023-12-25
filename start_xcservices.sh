#! /bin/bash
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
sleep 1
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
sleep 1
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
sleep 4
sudo rm /home/xtreamcodes/iptv_xtream_codes/adtools/balancer/*.json 2>/dev/null &
echo "" > /home/xtreamcodes/iptv_xtream_codes/logs/error.log 2>/dev/null &
echo "" > /home/xtreamcodes/iptv_xtream_codes/logs/rtmp_error.log 2>/dev/null &
echo "" > /home/xtreamcodes/iptv_xtream_codes/logs/access.log 2>/dev/null &
mkdir -p /home/xtreamcodes/iptv_xtream_codes/logs 2>/dev/null &
sleep 1
service mariadb restart
sudo -u xtreamcodes /home/xtreamcodes/iptv_xtream_codes/php/bin/php /home/xtreamcodes/iptv_xtream_codes/crons/setup_cache.php 2>/dev/null
sudo -u xtreamcodes /home/xtreamcodes/iptv_xtream_codes/php/bin/php /home/xtreamcodes/iptv_xtream_codes/tools/signal_receiver.php >/dev/null 2>/dev/null &
sudo -u xtreamcodes /home/xtreamcodes/iptv_xtream_codes/php/bin/php /home/xtreamcodes/iptv_xtream_codes/tools/pipe_reader.php >/dev/null 2>/dev/null &
#chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb 2>/dev/null
#cd /home/xtreamcodes/iptv_xtream_codes/
#wget "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=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&suffix=tar.gz" -qO /home/xtreamcodes/iptv_xtream_codes/GeoLite2-City.mmdb.tar.gz
#tar -xf /home/xtreamcodes/iptv_xtream_codes/GeoLite2-City.mmdb.tar.gz
#rm -f GeoLite2-City.mmdb.tar.gz
#rm -f /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb
#mv GeoLite2-City_*/GeoLite2-City.mmdb /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb
#geoliteversion=$(find /home/xtreamcodes/iptv_xtream_codes/ -name 'GeoLite2-City_*' | sed 's|/home/xtreamcodes/iptv_xtream_codes/GeoLite2-City_||')
#rm -rf GeoLite2-City_*
#chattr +i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb 2>/dev/null
chown -R xtreamcodes:xtreamcodes /sys/class/net 2>/dev/null
chown -R xtreamcodes:xtreamcodes /home/xtreamcodes 2>/dev/null
sleep 4
/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp
/home/xtreamcodes/iptv_xtream_codes/nginx/sbin/nginx
daemonize -p /home/xtreamcodes/iptv_xtream_codes/php/VaiIb8.pid /home/xtreamcodes/iptv_xtream_codes/php/sbin/php-fpm --fpm-config /home/xtreamcodes/iptv_xtream_codes/php/etc/VaiIb8.conf
daemonize -p /home/xtreamcodes/iptv_xtream_codes/php/JdlJXm.pid /home/xtreamcodes/iptv_xtream_codes/php/sbin/php-fpm --fpm-config /home/xtreamcodes/iptv_xtream_codes/php/etc/JdlJXm.conf
daemonize -p /home/xtreamcodes/iptv_xtream_codes/php/CWcfSP.pid /home/xtreamcodes/iptv_xtream_codes/php/sbin/php-fpm --fpm-config /home/xtreamcodes/iptv_xtream_codes/php/etc/CWcfSP.conf
screen -S sshd -X quit
screen -S sshd -dm /usr/sbin/sshd -D
