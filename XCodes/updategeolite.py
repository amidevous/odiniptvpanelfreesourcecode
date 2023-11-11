#!/usr/bin/python3.10
# -*- coding: utf-8 -*-
# update panel
import subprocess, os, sys

def updategeolite():
    os.system('cd /home/xtreamcodes/iptv_xtream_codes/ 2>/dev/null && chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb 2>/dev/null ; wget "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=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&suffix=tar.gz" -qO /home/xtreamcodes/iptv_xtream_codes/GeoLite2-City.mmdb.tar.gz 2>/dev/null && tar -xf /home/xtreamcodes/iptv_xtream_codes/GeoLite2-City.mmdb.tar.gz 2>/dev/null && rm -f /home/xtreamcodes/iptv_xtream_codes/GeoLite2-City.mmdb.tar.gz 2>/dev/null && rm -f /home/xtreamcodes/iptv_xtream_codes/GeoLite2-City.mmdb 2>/dev/null && mv GeoLite2-City_*/GeoLite2-City.mmdb /home/xtreamcodes/iptv_xtream_codes/GeoLite2-City.mmdb 2>/dev/null && chown xtreamcodes.xtreamcodes /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb 2>/dev/null && chattr +i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb 2>/dev/null && GeoLite2-City_*/ 2>/dev/null')
    return True

def start():
    os.system("/home/xtreamcodes/iptv_xtream_codes/start_services.sh 2>/dev/null")

if __name__ == "__main__":
    updategeolite()
    #start()
