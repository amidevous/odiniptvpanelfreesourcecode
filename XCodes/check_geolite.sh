#!/bin/bash
file=/home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb
filename=GeoLite2.mmdb
minimumsize=1
actualsize=$(wc -c <"$file")
if [ $actualsize -ge $minimumsize ]; then
echo "$filename OK"
echo "$actualsize"
else
chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb 2>/dev/null
cd /home/xtreamcodes/iptv_xtream_codes/
wget "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=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&suffix=tar.gz" -qO /home/xtreamcodes/iptv_xtream_codes/GeoLite2-City.mmdb.tar.gz
tar -xf /home/xtreamcodes/iptv_xtream_codes/GeoLite2-City.mmdb.tar.gz
rm -f GeoLite2-City.mmdb.tar.gz
rm -f /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb
mv GeoLite2-City_*/GeoLite2-City.mmdb /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb
geoliteversion=$(find /home/xtreamcodes/iptv_xtream_codes/ -name 'GeoLite2-City_*' | sed 's|/home/xtreamcodes/iptv_xtream_codes/GeoLite2-City_||')
rm -rf GeoLite2-City_*
chattr +i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb 2>/dev/null
HOST=$(python2 /home/xtreamcodes/iptv_xtream_codes/pytools/config.py DECRYPT | grep Host | sed "s|Host:                ||g")
PASSMYSQL=$(python2 /home/xtreamcodes/iptv_xtream_codes/pytools/config.py DECRYPT | grep Password | sed "s|Password:            ||g")
mysql -h $HOST -u user_iptvpro -p$PASSMYSQL -P 7999 xtream_iptvpro -e "UPDATE admin_settings SET value = '$geoliteversion' WHERE admin_settings.type = 'geolite2_version'; " 2>/dev/null
clear
echo "Done"
fi
