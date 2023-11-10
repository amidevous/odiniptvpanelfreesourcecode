#!/usr/bin/python3.10
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
rConfigPath = '/root/config'


def encrypt(rHost="127.0.0.1", rUsername="user_iptvpro", rPassword="", rDatabase="xtream_iptvpro", rServerID=1,
            rPort=7999):
    print("Encrypting...")

    with open(rConfigPath, 'wb') as rf:
        data = ''.join(chr(ord(c) ^ ord(k)) for c, k in
                       zip('{"host":"%s","db_user":"%s","db_pass":"%s","db_name":"%s","server_id":"%d", "db_port":"%d"}' % (
                           rHost, rUsername, rPassword, rDatabase, rServerID, rPort),
                           cycle('5709650b0d7806074842c6de575025b1')))
        encoded_data = base64.b64encode(data.encode()).decode().replace('\n', '')
        rf.write(encoded_data.encode())


def start():
    os.system("chown xtreamcodes:xtreamcodes /home/xtreamcodes/iptv_xtream_codes/config")
    os.system("chmod 777 /home/xtreamcodes/iptv_xtream_codes/config")
    #os.system("/home/xtreamcodes/iptv_xtream_codes/start_services.sh 2>/dev/null")

if __name__ == "__main__":
    rHost = sys.argv[1]
    rPort = int(sys.argv[2])
    rUsername = sys.argv[3]
    rPassword = sys.argv[4]
    rDatabase = sys.argv[5]
    rServerID = int(sys.argv[6])
    encrypt(rHost, rUsername, rPassword, rDatabase, rServerID, rPort)
    start()
