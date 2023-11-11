#!/usr/bin/python
# -*- coding: utf-8 -*-
import subprocess, os, sys
from itertools import cycle, izip

rPlat = "https://xtreamtools.org/XCodes/main_xtreamcodes_reborn.tar.gz"
rPackages = ["libcurl3", "libxslt1-dev", "libgeoip-dev", "e2fsprogs", "wget", "mcrypt", "nscd", "htop", "zip", "sshpass", "unzip", "mc", "python-paramiko"]
rGeo = "https://xtreamtools.org/XCodes/GeoLite2.mmdb"

def prepare():
    global rPackages
    for rFile in ["/var/lib/dpkg/lock-frontend", "/var/cache/apt/archives/lock", "/var/lib/dpkg/lock"]:
        try: os.remove(rFile)
        except: pass
    os.system("apt-get update > /dev/null")
    os.system("apt-get remove --auto-remove libcurl4 -y > /dev/null")
    for rPackage in rPackages: os.system("apt-get install %s -y > /dev/null" % rPackage)
    os.system("wget -q -O /tmp/libpng12.deb https://xtreamtools.org/XCodes/libpng12-0_1.2.54-1ubuntu1_amd64.deb")
    os.system("dpkg -i /tmp/libpng12.deb > /dev/null")
    os.system("apt-get install -y > /dev/null") # Clean up above
    try: os.remove("/tmp/libpng12.deb")
    except: pass
    os.system("adduser --system --shell /bin/false --group --disabled-login xtreamcodes 2>/dev/null")
    if not os.path.exists("/home/xtreamcodes"): os.mkdir("/home/xtreamcodes")
    return True
        
def install():
    global rInstall, rPlat, rGeo
    rURL = rPlat
    rNginx = "/home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf"
    rNginxRtmp = "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf"
    rIni = "/home/xtreamcodes/iptv_xtream_codes/php/lib/php.ini"
    rIsp = "/home/xtreamcodes/iptv_xtream_codes/wwwdir/includes/streaming.php"
    rYou = "https://xtreamtools.org/XCodes/youtube-dl"
    rCheckGeo = "https://xtreamtools.org/XCodes/check_geolite.sh"
    if not "/home/xtreamcodes/iptv_xtream_codes/" in open("/etc/fstab").read():
        rFile = open("/etc/fstab", "a")
        rFile.write("tmpfs /home/xtreamcodes/iptv_xtream_codes/streams tmpfs defaults,noatime,nosuid,nodev,noexec,mode=1777,size=90% 0 0\ntmpfs /home/xtreamcodes/iptv_xtream_codes/tmp tmpfs defaults,noatime,nosuid,nodev,noexec,mode=1777,size=2G 0 0")
        rFile.close()    
    if not "/sbin/iptables, /usr/bin/chattr" in open("/etc/sudoers").read(): os.system('sed -i "s|xtreamcodes|#xtreamcodes|g" /etc/sudoers && echo "xtreamcodes ALL=(root) NOPASSWD: /sbin/iptables, /usr/bin/chattr" >> /etc/sudoers')
    os.system('wget -q -O "/tmp/xtreamcodes.tar.gz" "%s"' % rURL)
    rMod = rNginx
    rPrevData = open(rMod, "r").read()
    if not "ISP CONFIGURATION" in rPrevData:
        os.system('cp "%s" "%s.xc"' % (rMod, rMod))
        rData = "}".join(rPrevData.split("}")[:-1]) + "\n#ISP CONFIGURATION\n\n    server {\n        listen 8805;\n        root /home/xtreamcodes/iptv_xtream_codes/isp/;\n        location / {\n            allow 127.0.0.1;\n            deny all;\n        }\n        location ~ \.php$ {\n			limit_req zone=one burst=8;\n            try_files $uri =404;\n			fastcgi_index index.php;\n			fastcgi_pass php;\n			include fastcgi_params;\n			fastcgi_buffering on;\n			fastcgi_buffers 96 32k;\n			fastcgi_buffer_size 32k;\n			fastcgi_max_temp_file_size 0;\n			fastcgi_keep_conn on;\n			fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n			fastcgi_param SCRIPT_NAME $fastcgi_script_name;\n        }\n    }\n}"
        rInx = open(rMod, "w")
        rInx.write(rData)
        rInx.close()
    if not "api.xtream-codes.com" in open("/home/xtreamcodes/iptv_xtream_codes/wwwdir/includes/streaming.php").read():
        os.system('mv "%s" "%s.old"' % (rIsp, rIsp))
    else: 
        os.system('mv "%s" "%s.xc"' % (rIsp, rIsp))
    if os.path.exists("/tmp/xtreamcodes.tar.gz"):
        os.system('mv "%s" "%s.xc" && mv "%s" "%s.xc" && mv "%s" "%s.xc"' % (rNginx, rNginx, rNginxRtmp, rNginxRtmp, rIni, rIni))
        os.system("chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb")
        os.system("umount -l /home/xtreamcodes/iptv_xtream_codes/streams")
        os.system("umount -l /home/xtreamcodes/iptv_xtream_codes/tmp")
        os.system('tar -zxvf "/tmp/xtreamcodes.tar.gz" -C "/home/xtreamcodes/" > /dev/null')
        os.system("rm /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb")
        os.system('wget -q -O "/home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb" "%s"' % rGeo)
        os.system("chown -R xtreamcodes:xtreamcodes /home/xtreamcodes/")
        os.system("mount -a")
        os.system('mv "%s.xc" "%s" && mv "%s.xc" "%s" && mv "%s.xc" "%s"' % (rNginx, rNginx, rNginxRtmp, rNginxRtmp, rIni, rIni))
        os.system("chmod -R 777 /home/xtreamcodes/")
        os.system("chattr +i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb")
        os.system("rm /home/xtreamcodes/iptv_xtream_codes/adtools/backups/* 2>/dev/null")
        os.system("rm /usr/local/bin/youtube-dl 2>/dev/null")
        os.system('wget -q -O "/usr/local/bin/youtube-dl" "%s"' % rYou)
        os.system("sudo chmod a+rx /usr/local/bin/youtube-dl")
        os.system('wget -q -O "/home/xtreamcodes/iptv_xtream_codes/check_geolite.sh" "%s"' % rCheckGeo)
        if not "check_geolite.sh" in open("/etc/crontab").read(): os.system('echo "*/1 *   * * * root /home/xtreamcodes/iptv_xtream_codes/./check_geolite.sh" >> /etc/crontab')
        try: os.remove("/tmp/xtreamcodes.tar.gz")
        except: pass
    if os.path.exists("/home/xtreamcodes/iptv_xtream_codes/database.sql"): os.system("rm /home/xtreamcodes/iptv_xtream_codes/database.sql")
    if not "/home/xtreamcodes 2>/dev/null" in open("/home/xtreamcodes/iptv_xtream_codes/start_services.sh").read():   
        os.system("sed -i 's|chown -R xtreamcodes:xtreamcodes /home/xtreamcodes|chown -R xtreamcodes:xtreamcodes /home/xtreamcodes 2>/dev/null|g' /home/xtreamcodes/iptv_xtream_codes/start_services.sh")
    if not "api.xtream-codes.com" in open("/etc/hosts").read(): os.system('echo "127.0.0.1    api.xtream-codes.com" >> /etc/hosts')
    if not "downloads.xtream-codes.com" in open("/etc/hosts").read(): os.system('echo "127.0.0.1    downloads.xtream-codes.com" >> /etc/hosts')
    if not "xtream-codes.com" in open("/etc/hosts").read(): os.system('echo "127.0.0.1    xtream-codes.com" >> /etc/hosts')
    if not os.path.exists("/etc/init.d/xtreamcodes"): os.system("touch /etc/init.d/xtreamcodes")
    if not "Provides" in open("/etc/init.d/xtreamcodes").read():
        os.system("rm /etc/init.d/xtreamcodes")
        rStart = open("/etc/init.d/xtreamcodes", "w")
        rStart.write("#!/bin/bash\n### BEGIN INIT INFO\n# Provides:          xtreamcodes\n# Required-Start:    $all\n# Required-Stop:\n# Default-Start:     2 3 4 5\n# Default-Stop:\n# Short-Description: Run /etc/init.d/xtreamcodes if it exist\n### END INIT INFO\nsleep 1\n/home/xtreamcodes/iptv_xtream_codes/start_services.sh > /dev/null")
        rStart.close()
        os.system("chmod 777 /etc/init.d/xtreamcodes")
        os.system("update-rc.d xtreamcodes defaults")
        os.system("update-rc.d xtreamcodes enable")
        return True
    return False
            
def encrypt(rHost="127.0.0.1", rUsername="user_iptvpro", rPassword="", rDatabase="xtream_iptvpro", rServerID=1, rPort=7999):
    try: os.remove("/home/xtreamcodes/iptv_xtream_codes/config")
    except: pass
    rf = open('/home/xtreamcodes/iptv_xtream_codes/config', 'wb')
    rf.write(''.join(chr(ord(c)^ord(k)) for c,k in izip('{\"host\":\"%s\",\"db_user\":\"%s\",\"db_pass\":\"%s\",\"db_name\":\"%s\",\"server_id\":\"%d\", \"db_port\":\"%d\"}' % (rHost, rUsername, rPassword, rDatabase, rServerID, rPort), cycle('5709650b0d7806074842c6de575025b1'))).encode('base64').replace('\n', ''))
    rf.close()


def start():
    rIspOK = "/home/xtreamcodes/iptv_xtream_codes/wwwdir/includes/streaming.php"
    os.system('rm /usr/bin/ffmpeg')
    os.system('rm /usr/bin/ffprobe')
    os.system('apt-get install unzip e2fsprogs python-paramiko -y')
    os.system('chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb')
    os.system('wget "https://xtreamtools.org/XCodes/update.zip" -O /tmp/update.zip -o /dev/null')
    os.system('unzip /tmp/update.zip -d /tmp/update/ >/dev/null')
    os.system('rm -rf /home/xtreamcodes/iptv_xtream_codes/crons')
    os.system('rm -rf /home/xtreamcodes/iptv_xtream_codes/php/etc')
    os.system('cp -rf /tmp/update/XtreamUI-master/* /home/xtreamcodes/iptv_xtream_codes/ 2>/dev/null')
    os.system('rm -rf /tmp/update/XtreamUI-master')
    os.system('rm /tmp/update.zip')
    os.system('rm -rf /tmp/update')
    os.system('wget https://xtreamtools.org/XCodes/GeoLite2.mmdb -O /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb -o /dev/null')
    os.system('chown -R xtreamcodes:xtreamcodes /home/xtreamcodes')
    os.system('find /home/xtreamcodes/ -type d -not \( -name .update -prune \) -exec chmod -R 777 {} + ')
    os.system('chattr +i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb')
    os.system('ln -s /home/xtreamcodes/iptv_xtream_codes/bin/ffmpeg /usr/bin/')
    if os.path.exists("/home/xtreamcodes/iptv_xtream_codes/wwwdir/includes/streaming.php.xc"): os.system('mv "%s.xc" "%s"' % (rIspOK, rIspOK))
    os.system("/home/xtreamcodes/iptv_xtream_codes/start_services.sh")

if __name__ == "__main__":
    rHost = sys.argv[1]
    rPort = int(sys.argv[2])
    rUsername = sys.argv[3]
    rPassword = sys.argv[4]
    rDatabase = sys.argv[5]
    rServerID = int(sys.argv[6])
    prepare()
    install()
    encrypt(rHost, rUsername, rPassword, rDatabase, rServerID, rPort)
    start()
