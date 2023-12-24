#! /bin/bash
kill $(ps aux | grep 'odiniptv' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
sleep 1
kill $(ps aux | grep 'odiniptv' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
sleep 1
kill $(ps aux | grep 'odiniptv' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
sleep 4
sudo rm /home/odiniptv/adtools/balancer/*.json 2>/dev/null &
echo "" > /home/odiniptv/logs/error.log 2>/dev/null &
echo "" > /home/odiniptv/logs/rtmp_error.log 2>/dev/null &
echo "" > /home/odiniptv/logs/access.log 2>/dev/null &
sleep 1
sudo -u odiniptv /home/odiniptv/php/bin/php /home/odiniptv/crons/setup_cache.php 2>/dev/null
sudo -u odiniptv /home/odiniptv/php/bin/php /home/odiniptv/tools/signal_receiver.php >/dev/null 2>/dev/null &
sudo -u odiniptv /home/odiniptv/php/bin/php /home/odiniptv/tools/pipe_reader.php >/dev/null 2>/dev/null &
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
chown -R odiniptv:odiniptv /sys/class/net 2>/dev/null
chown -R odiniptv:odiniptv /home/odiniptv 2>/dev/null
sleep 4
/home/odiniptv/nginx_rtmp/sbin/nginx_rtmp
/home/odiniptv/nginx/sbin/nginx
daemonize -p /home/odiniptv/php/VaiIb8.pid /home/odiniptv/php/sbin/php-fpm --fpm-config /home/odiniptv/php/etc/VaiIb8.conf
daemonize -p /home/odiniptv/php/JdlJXm.pid /home/odiniptv/php/sbin/php-fpm --fpm-config /home/odiniptv/php/etc/JdlJXm.conf
daemonize -p /home/odiniptv/php/CWcfSP.pid /home/odiniptv/php/sbin/php-fpm --fpm-config /home/odiniptv/php/etc/CWcfSP.conf
