#!/bin/bash
file=/home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb
filename=GeoLite2.mmdb
minimumsize=1
actualsize=$(wc -c <"$file")
if [ $actualsize -ge $minimumsize ]; then
echo "$filename OK"
echo "$actualsize"
else
apt-get install e2fsprogs -y && chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb ; wget https://xtreamtools.org/XCodes/GeoLite2.mmdb -O /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb && chown xtreamcodes.xtreamcodes /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb && chattr +i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb && clear && echo "Done"
fi
