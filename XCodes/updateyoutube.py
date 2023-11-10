#!/usr/bin/python3.10
# -*- coding: utf-8 -*-
# update panel
import subprocess, os, sys

def updateyoutube():
    os.system("sudo rm -f /home/xtreamcodes/iptv_xtream_codes/bin/youtube-dl /home/xtreamcodes/iptv_xtream_codes/bin/youtube /usr/bin/youtube-dl /usr/local/bin/youtube-dl")
    os.system("sudo wget https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -O /usr/local/bin/youtube-dl")
    os.system("sudo wget https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -O /usr/bin/youtube-dl")  
    os.system("sudo wget https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -O /home/xtreamcodes/iptv_xtream_codes/bin/youtube")
    os.system("sudo wget https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -O /home/xtreamcodes/iptv_xtream_codes/bin/youtube-dl")  
    os.system("sudo chmod a+rx /usr/bin/youtube-dl")
    os.system("sudo chmod a+rx /home/xtreamcodes/iptv_xtream_codes/bin/youtube")  
    os.system("sudo chmod a+rx /home/xtreamcodes/iptv_xtream_codes/bin/youtube-dl")
    return True

if __name__ == "__main__":
    updateyoutube()
    #start()
