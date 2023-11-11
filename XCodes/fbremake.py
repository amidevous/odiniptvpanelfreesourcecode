#!/usr/bin/env python3.10
# -*- coding: utf-8 -*-
import os
from urllib.request import Request, urlopen
import random
import shutil
import socket
import string
import subprocess
import sys
import time
import json
import base64
from itertools import cycle, zip_longest as izip
from itertools import zip_longest
from datetime import datetime
rFbremake = "https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/sub_xtreamcodes_reborn_original.tar.gz"
rConfigPath = "/home/xtreamcodes/iptv_xtream_codes/config"

class col:
    HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'


def generate(length=16):
    return ''.join(random.choice(string.ascii_letters + string.digits) for i in range(length))


def getIP():
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    s.connect(("8.8.8.8", 80))
    return s.getsockname()[0]


def getVersion():
    try:
        return subprocess.check_output("lsb_release -d".split()).split(":")[-1].strip()
    except:
        return ""


def printc(rText, rColour=col.OKBLUE, rPadding=0):
    print("%s ┌────── ODIN FREE CLEAN INSTALL - DISCORD: https://discord.gg/mH6D7VWXmt ─────┐ %s" % (
        rColour, col.ENDC))
    for i in range(rPadding): print("%s                                                              %s" % (
        rColour, col.ENDC))
    print("%s           %s%s%s            %s" % (
        rColour, " " * (20 - (len(rText) // 2)), rText, " " * (40 - (20 - (len(rText) // 2)) - len(rText)),
        col.ENDC))
    for i in range(rPadding): print("%s                                                               %s" % (
        rColour, col.ENDC))
    print("%s └─────────────────────────────────────────────────────────────────────────────┘ %s" % (
        rColour, col.ENDC))
    print(" ")


def prepare():
    if not os.path.exists('/home/xtreamcodes/dep'):
      if not os.path.exists("/home/xtreamcodes"): os.mkdir("/home/xtreamcodes")
      os.system("touch /home/xtreamcodes/dep >/dev/null 2>&1")
      printc("Install Build Dependencie max 2H Wait")
      now = datetime.now()
      dt_string = now.strftime("%d/%m/%Y %H:%M:%S")
      printc("Actual GMT Time =", dt_string)
      os.system("wget --no-check-certificate -qO /root/depbuild.sh https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/depbuild.sh")
      os.system("bash /root/depbuild.sh >/dev/null 2>&1")
    os.system("rm -rf /etc/systemd/system/mariadb.service.d /etc/systemd/system/multi-user.target.wants/mariadb.service >/dev/null 2>&1")
    os.system('mkdir -p /etc/init.d/ >/dev/null 2>&1')
    os.system('touch /etc/init.d/mariadb >/dev/null 2>&1')
    rFile = open("/etc/init.d/mariadb", "w")
    rFile.write(rMySQLINIT)
    rFile.close()
    os.system("chmod 777 /etc/init.d/mariadb >/dev/null 2>&1")
    os.system("systemctl restart mariadb >/dev/null 2>&1")
    os.system("service mariadb restart >/dev/null 2>&1")
    os.system("systemctl mariadb enable >/dev/null 2>&1")
    os.system("chkconfig --add mariadb >/dev/null 2>&1")
    os.system("chkconfig --level 345 mariadb on >/dev/null 2>&1")
    os.system("update-rc.d mariadb defaults >/dev/null 2>&1")
    os.system("service mariadb restart >/dev/null 2>&1")
    try:
        subprocess.check_output("getent passwd xtreamcodes > /dev/null".split())
    except:
        # Create User
        printc("Creating user")
        
    # Create User
    printc("Creating user")
    os.system("adduser --system --shell /bin/false --group --disabled-login xtreamcodes >/dev/null 2>&1")
    os.system("adduser --system --shell /bin/false xtreamcodes >/dev/null 2>&1")
    if not os.path.exists("/home/xtreamcodes"): os.mkdir("/home/xtreamcodes")
    return True


def install():
    global rInstall, rFbremake
    rURL = rFbremake
    os.system('wget -q -O "/tmp/xtreamcodes.tar.gz" "%s"' % rURL)
    if os.path.exists("/tmp/xtreamcodes.tar.gz"):
        printc("Installing Software")
        os.system('tar -zxvf "/tmp/xtreamcodes.tar.gz" -C "/home/xtreamcodes/" > /dev/null')
        try:
            os.remove("/tmp/xtreamcodes.tar.gz")
        except:
            pass
        return True
    printc("Failed to download installation file!", col.FAIL)
    return False



def encrypt(rHost="127.0.0.1", rUsername="user_iptvpro", rPassword="", rDatabase="xtream_iptvpro", rServerID=1,
            rPort=7999):
    printc("Encrypting...")
    # try: os.remove(rConfigPath)
    # except: pass

    with open(rConfigPath, 'wb') as rf:
        data = ''.join(chr(ord(c) ^ ord(k)) for c, k in
                       zip('{"host":"%s","db_user":"%s","db_pass":"%s","db_name":"%s","server_id":"%d", "db_port":"%d"}' % (
                           rHost, rUsername, rPassword, rDatabase, rServerID, rPort),
                           cycle('5709650b0d7806074842c6de575025b1')))
        encoded_data = base64.b64encode(data.encode()).decode().replace('\n', '')
        rf.write(encoded_data.encode())


def configure():
    printc("Configuring System")
    os.system("touch /etc/fstab")
    if not "/home/xtreamcodes/iptv_xtream_codes/" in open("/etc/fstab").read():
        rFile = open("/etc/fstab", "a")
        rFile.write(
            "tmpfs /home/xtreamcodes/iptv_xtream_codes/streams tmpfs defaults,noatime,nosuid,nodev,noexec,mode=1777,size=90% 0 0\ntmpfs /home/xtreamcodes/iptv_xtream_codes/tmp tmpfs defaults,noatime,nosuid,nodev,noexec,mode=1777,size=2G 0 0")
        rFile.close()
    if not "xtreamcodes" in open("/etc/sudoers").read():
        os.system('echo "xtreamcodes ALL=(root) NOPASSWD: /sbin/iptables, /usr/bin/chattr" >> /etc/sudoers')
    if not os.path.exists("/etc/init.d/xtreamcodes"):
        rStart = open("/etc/init.d/xtreamcodes", "w")
        rStart.write(
            "#!/bin/bash\n### BEGIN INIT INFO\n# Provides:          xtreamcodes\n# Required-Start:    $all\n# Required-Stop:\n# Default-Start:     2 3 4 5\n# Default-Stop:\n# Short-Description: Run /etc/init.d/xtreamcodes if it exist\n### END INIT INFO\nsleep 1\n/home/xtreamcodes/iptv_xtream_codes/start_services.sh > /dev/null")
        rStart.close()
        os.system("chmod +x /etc/init.d/xtreamcodes")
        os.system("update-rc.d xtreamcodes defaults >/dev/null 2>&1")
        os.system("update-rc.d xtreamcodes enable >/dev/null 2>&1")
    try:
        os.remove("/usr/bin/ffmpeg")
    except:
        pass
    if not os.path.exists("/home/xtreamcodes/iptv_xtream_codes/tv_archive"): os.mkdir(
        "/home/xtreamcodes/iptv_xtream_codes/tv_archive/")
    os.system("ln -s /home/xtreamcodes/iptv_xtream_codes/bin/ffmpeg /usr/bin/")
    os.system("chown xtreamcodes:xtreamcodes -R /home/xtreamcodes > /dev/null")
    os.system("chmod -R 0777 /home/xtreamcodes > /dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/start_services.sh > /dev/null")
    os.system("chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb > /dev/null")
    os.system("mount -a >/dev/null 2>&1")
    os.system("chattr -i /etc/hosts > /dev/null")
    os.system("chmod 777 /etc/hosts > /dev/null")
    if not "api.xtream-codes.com" in open("/etc/hosts").read(): os.system(
        'echo "127.0.0.1    api.xtream-codes.com" >> /etc/hosts')
    if not "downloads.xtream-codes.com" in open("/etc/hosts").read(): os.system(
        'echo "127.0.0.1    downloads.xtream-codes.com" >> /etc/hosts')
    if not " xtream-codes.com" in open("/etc/hosts").read(): os.system(
        'echo "127.0.0.1    xtream-codes.com" >> /etc/hosts')
    os.system("chattr +i /etc/hosts > /dev/null")
    os.system("sed -i 's|echo \"ODIN IpTV Panel https://discord.gg/mH6D7VWXmt \";|header(\"Location: https://www.google.com/\");|g' /home/xtreamcodes/iptv_xtream_codes/wwwdir/index.php")
    printc("INSTALLING AND UPDATING YOUTUBE MODULE")
    os.system("sudo wget https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -O /usr/local/bin/youtube-dl 2> /dev/null")
    os.system("sudo chmod a+rx /usr/local/bin/youtube-dl > /dev/null")
    os.system("cp /usr/local/bin/youtube-dl /home/xtreamcodes/iptv_xtream_codes/bin/ > /dev/null")
    os.system("sudo chmod a+rx /home/xtreamcodes/iptv_xtream_codes/bin/youtube-dl > /dev/null")


def start():
    printc("Restarting ODIN")
    os.system("chattr +i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb 2>/dev/null")
    os.system("chmod 644 /home/xtreamcodes/iptv_xtream_codes/php/VaiIb8.pid 2>/dev/null")
    os.system("chmod 644 /home/xtreamcodes/iptv_xtream_codes/php/JdlJXm.pid 2>/dev/null")
    os.system("chmod 644 /home/xtreamcodes/iptv_xtream_codes/php/CWcfSP.pid 2>/dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/nginx/sbin/nginx 2>/dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp 2>/dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/php/bin/php 2>/dev/null")
    os.system("chmod +x /home/xtreamcodes/iptv_xtream_codes/php/sbin/php-fpm 2>/dev/null")
    os.system("sysctl -w kernel.core_pattern='|/bin/false' >/dev/null 2>&1")
    if not os.path.exists('/home/xtreamcodes/build'):
        os.system("mkdir -p /home/xtreamcodes/ >/dev/null 2>&1")
        os.system("touch /home/xtreamcodes/build >/dev/null 2>&1")
        printc("ReBuild All Max 2H Wait")
        now = datetime.now()
        dt_string = now.strftime("%d/%m/%Y %H:%M:%S")
        printc("Actual GMT Time =", dt_string)
        os.system("wget --no-check-certificate -qO /root/php7.2rebuild.sh https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/php7.2rebuild.sh")
        os.system("bash /root/php7.2rebuild.sh >/dev/null 2>&1")
    os.system('rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/ >/dev/null 2>&1')
    os.system("wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/start_services.sh -qO /home/xtreamcodes/iptv_xtream_codes/start_services.sh")
    os.system("chmod 777 /home/xtreamcodes/iptv_xtream_codes/start_services.sh")
    os.system("/home/xtreamcodes/iptv_xtream_codes/start_services.sh >/dev/null 2>&1")
    


if __name__ == "__main__":
    rHost = sys.argv[1]
    rPort = int(sys.argv[2])
    rUsername = sys.argv[3]
    rPassword = sys.argv[4]
    rDatabase = sys.argv[5]
    rServerID = int(sys.argv[6])
    prepare()
    install()
    configure()
    encrypt(rHost, rUsername, rPassword, rDatabase, rServerID, rPort)
    start()
