#!/bin/bash
# sudo apt-get update
# sudo apt-get -y install wget
# sudo yum -y install wget
# sudo wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/php7.2build.sh -O /root/php7.2build.sh
# sudo dos2unix /root/php7.2build.sh
# sudo bash /root/php7.2build.sh
echo -e "\nChecking that minimal requirements are ok"
# Ensure the OS is compatible with the launcher
if [ -f /etc/centos-release ]; then
    inst() {
       rpm -q "$1" &> /dev/null
    } 
    if (inst "centos-stream-repos"); then
    OS="CentOS-Stream"
    else
    OS="CentOs"
    fi    
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/centos-release)
    VER=${VERFULL:0:1} # return 6, 7 or 8
elif [ -f /etc/fedora-release ]; then
    inst() {
       rpm -q "$1" &> /dev/null
    } 
    OS="Fedora"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/fedora-release)
    VER=${VERFULL:0:2} # return 34, 35 or 36
elif [ -f /etc/lsb-release ]; then
    OS=$(grep DISTRIB_ID /etc/lsb-release | sed 's/^.*=//')
    VER=$(grep DISTRIB_RELEASE /etc/lsb-release | sed 's/^.*=//')
elif [ -f /etc/os-release ]; then
    OS=$(grep -w ID /etc/os-release | sed 's/^.*=//')
    VER=$(grep VERSION_ID /etc/os-release | sed 's/^.*"\(.*\)"/\1/' | head -n 1 | tail -n 1)
 else
    OS=$(uname -s)
    VER=$(uname -r)
fi
ARCH=$(uname -m)
if [[ "$VER" = "8" && "$OS" = "CentOs" ]]; then
	echo "Centos 8 obsolete udate to CentOS-Stream 8"
	echo "this operation may take some time"
	sleep 60
	# change repository to use vault.centos.org CentOS 8 found online to vault.centos.org
	find /etc/yum.repos.d -name '*.repo' -exec sed -i 's|mirrorlist=http://mirrorlist.centos.org|#mirrorlist=http://mirrorlist.centos.org|' {} \;
	find /etc/yum.repos.d -name '*.repo' -exec sed -i 's|#baseurl=http://mirror.centos.org|baseurl=http://vault.centos.org|' {} \;
	#update package list
	dnf update -y
	#upgrade all packages to latest CentOS 8
	dnf upgrade -y
	#install CentOS-Stream 8 repository
	dnf -y install centos-release-stream --allowerasing
	#install rpmconf
	dnf -y install rpmconf
	#set config file with rpmconf
	rpmconf -a
	# remove Centos 8 repository and set CentOS-Stream 8 repository by default
	dnf -y swap centos-linux-repos centos-stream-repos
	# system upgrade
	dnf -y distro-sync
	# ceanup old rpmconf file create
	find / -name '*.rpmnew' -exec rm -f {} \;
	find / -name '*.rpmsave' -exec rm -f {} \;
	OS="CentOS-Stream"
	fi

echo "Detected : $OS  $VER  $ARCH"
if [[ "$OS" = "CentOs" && "$VER" = "7" && "$ARCH" == "x86_64" ||
"$OS" = "CentOS-Stream" && "$VER" = "8" && "$ARCH" == "x86_64" ||
"$OS" = "CentOS-Stream" && "$VER" = "9" && "$ARCH" == "x86_64" ||
"$OS" = "Fedora" && ("$VER" = "36" || "$VER" = "37" || "$VER" = "38" ) && "$ARCH" == "x86_64" ||
"$OS" = "Ubuntu" && ("$VER" = "18.04" || "$VER" = "20.04" || "$VER" = "22.04" ) && "$ARCH" == "x86_64" ||
"$OS" = "debian" && ("$VER" = "10" || "$VER" = "11" ) && "$ARCH" == "x86_64" ]] ; then
echo "Ok."
else
    echo "Sorry, this OS is not supported by Xtream UI."
    exit 1
fi
sed -i "s|#\$nrconf{verbosity} = 2;|\$nrconf{verbosity} = 0;|" /etc/needrestart/needrestart.conf
sed -i "s|#\$nrconf{restart} = 'i';|\$nrconf{restart} = 'a';|" /etc/needrestart/needrestart.conf
killall nginx
killall nginx_rtmp
killall php-fpm
killall php-fpm
killall php
killall php
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx
killall nginx_rtmp
killall php-fpm
killall php-fpm
killall php
killall php
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx
killall nginx_rtmp
killall php-fpm
killall php-fpm
killall php
killall php
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx
killall nginx_rtmp
killall php-fpm
killall php-fpm
killall php
killall php
rm -rf "/home/xtreamcodes/"
mkdir -p "/home/xtreamcodes/tmp"
wget -O "/home/xtreamcodes/tmp/xtreamcodes.tar.gz" "https://www.dropbox.com/s/wxxfifeun3899jl/main_xtreamcodes_reborn.tar.gz?dl=0"
chown xtreamcodes:xtreamcodes -R /home/xtreamcodes
chmod -R 0777 /home/xtreamcodes
tar -zxvf "/home/xtreamcodes/tmp/xtreamcodes.tar.gz" -C "/home/xtreamcodes/"
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/bin/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/include/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/Archive/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/OS/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/PEAR.php
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/System.php
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/build/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/doc/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/pearcmd.php
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/test/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/Console/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/PEAR/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/Structures/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/XML/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/data/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/geoip.so
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/igbinary.so
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/mcrypt.so
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/opcache.a
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/opcache.so
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/peclcmd.php
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/php/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/sbin/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/var/
mkdir -p /home/xtreamcodes/iptv_xtream_codes/phpbuild/
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
rm -rf *
wget --no-check-certificate -qO- https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/depbuild.sh | bash -s
mkdir -p  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h
wget https://github.com/openssl/openssl/archive/OpenSSL_1_1_1h.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
tar -xzvf OpenSSL_1_1_1h.tar.gz
wget http://nginx.org/download/nginx-1.24.0.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
tar -xzvf nginx-1.24.0.tar.gz
git clone https://github.com/leev/ngx_http_geoip2_module.git /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-rtmp-module-1.2.2
wget https://github.com/arut/nginx-rtmp-module/archive/v1.2.2.zip -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
unzip /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
wget https://launchpad.net/ubuntu/+archive/primary/+sourcefiles/nginx/1.24.0-2ubuntu1/nginx_1.24.0-2ubuntu1.debian.tar.xz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/0003-define_gnu_source-on-other-glibc-based-platforms.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/nginx-fix-pidfile.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/nginx-ssl_cert_cb_yield.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/CVE-2023-44487.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/ubuntu-branding.patch
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/
if [ -f "/usr/bin/dpkg-buildflags" ]; then
    configureend="--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h --with-ld-opt='$(dpkg-buildflags --get LDFLAGS)' --with-cc-opt='$(dpkg-buildflags --get CFLAGS)'"
elif [ -f "/usr/bin/rpm" ]; then
    configureend="--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h --with-cc-opt='$(rpm --eval %{build_ldflags})' --with-cc-opt='$(rpm --eval %{optflags})'"
else 
    configureend="--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h"
fi
./configure --prefix=/home/xtreamcodes/iptv_xtream_codes/nginx \
--lock-path=/home/xtreamcodes/iptv_xtream_codes/tmp/nginx.lock \
--conf-path=/home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf \
--error-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/error.log \
--http-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/access.log \
--pid-path=/home/xtreamcodes/iptv_xtream_codes/nginx.pid \
--with-http_ssl_module \
--with-http_realip_module \
--with-http_addition_module \
--with-http_sub_module \
--with-http_dav_module \
--with-http_gunzip_module \
--with-http_gzip_static_module \
--with-http_v2_module \
--with-pcre \
--with-http_random_index_module \
--with-http_secure_link_module \
--with-http_stub_status_module \
--with-http_auth_request_module \
--with-threads \
--with-mail \
--with-mail_ssl_module \
--with-file-aio \
--with-cpu-opt=generic \
--add-module=/home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module \
"$configureend"
make -j$(nproc --all)
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx/"
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx/sbin/"
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx/modules"
mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/nginx/conf"
mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/logs/"
killall nginx
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
rm -f /home/xtreamcodes/iptv_xtream_codes/nginx/sbin/*
make install
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/balance.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/balance.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/fastcgi.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/fastcgi.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/fastcgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/fastcgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/koi-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/koi-utf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/koi-win https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/koi-win
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/mime.types https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/mime.types
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/mime.types.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/mime.types.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/nginx.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/nginx.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/scgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/scgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/scgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/scgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/server.crt https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/server.crt
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/server.csr https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/server.csr
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/server.key https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/server.key
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/uwsgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/uwsgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/uwsgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/uwsgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/win-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/win-utf
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_rtmp-1.24.0
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h
wget https://github.com/openssl/openssl/archive/OpenSSL_1_1_1h.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
tar -xzvf OpenSSL_1_1_1h.tar.gz
wget http://nginx.org/download/nginx-1.24.0.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
mkdir -p /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_rtmp-1.24.0
tar -xzvf nginx-1.24.0.tar.gz -C "/home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_rtmp-1.24.0"
git clone https://github.com/leev/ngx_http_geoip2_module.git /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-rtmp-module-1.2.2
wget https://github.com/arut/nginx-rtmp-module/archive/v1.2.2.zip -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
unzip /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
wget https://launchpad.net/ubuntu/+archive/primary/+sourcefiles/nginx/1.24.0-2ubuntu1/nginx_1.24.0-2ubuntu1.debian.tar.xz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_rtmp-1.24.0/nginx-1.24.0
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/0003-define_gnu_source-on-other-glibc-based-platforms.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/nginx-fix-pidfile.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/nginx-ssl_cert_cb_yield.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/CVE-2023-44487.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/ubuntu-branding.patch
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/
./configure --prefix=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp \
--lock-path=/home/xtreamcodes/iptv_xtream_codes/tmp/nginx_rtmp.lock \
--http-client-body-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/client_body_temp \
--http-fastcgi-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/fastcgi_temp \
--http-proxy-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/proxy_temp \
--http-scgi-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/scgi_temp \
--http-uwsgi-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/uwsgi_temp \
--conf-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf \
--error-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/rtmp_error.log \
--http-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/rtmp_access.log \
--pid-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp.pid \
--add-module=/home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-rtmp-module-1.2.2 \
--with-http_ssl_module \
--with-http_realip_module \
--with-http_addition_module \
--with-http_sub_module \
--with-http_dav_module \
--with-http_gunzip_module \
--with-http_gzip_static_module \
--with-http_v2_module \
--with-pcre \
--with-http_random_index_module \
--with-http_secure_link_module \
--with-http_stub_status_module \
--with-http_auth_request_module \
--with-threads \
--with-mail \
--with-mail_ssl_module \
--with-file-aio \
--with-cpu-opt=generic \
--without-http_rewrite_module \
--add-module=/home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module \
"$configureend"
make -j$(nproc --all)
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/"
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/"
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/modules"
mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf"
mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/logs/"
killall nginx_rtmp
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx_rtmp
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx_rtmp
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
rm -f /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/*
#mv objs/nginx objs/nginx_rtmp
#cp objs/nginx_rtmp /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/
make install
mv /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/fastcgi.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/fastcgi.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/fastcgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/fastcgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/koi-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/koi-utf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/koi-win https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/koi-win
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/mime.types https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/mime.types
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/mime.types.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/mime.types.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/nginx.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/nginx.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/scgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/scgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/scgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/scgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/uwsgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/uwsgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/uwsgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/uwsgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/win-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/win-utf
cd  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
wget https://launchpad.net/~ondrej/+archive/ubuntu/php/+sourcefiles/php7.2/7.2.34-43+ubuntu20.04.1+deb.sury.org+1/php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
tar -xvf php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
wget http://www.php.net/distributions/php-7.2.34.tar.xz
tar -xvf php-7.2.34.tar.xz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.2.34
##patch -p1 < ../debian/patches/0001-libtool_fixes.patch
##patch -p1 < ../debian/patches/0002-static_openssl.patch
#patch -p1 < ../debian/patches/0003-debian_quirks.patch
#patch -p1 < ../debian/patches/0004-libtool2.2.patch
##patch -p1 < ../debian/patches/0005-we_WANT_libtool.patch
##patch -p1 < ../debian/patches/0006-php-5.4.9-phpinfo.patch
#patch -p1 < ../debian/patches/0007-extension_api.patch
##patch -p1 < ../debian/patches/0008-no_apache_installed.patch
##patch -p1 < ../debian/patches/0009-recode_is_shared.patch
##patch -p1 < ../debian/patches/0010-proc_open.patch
##patch -p1 < ../debian/patches/0011-php.ini_securitynotes.patch
##patch -p1 < ../debian/patches/0012-php-5.4.7-libdb.patch
##patch -p1 < ../debian/patches/0013-Add-support-for-use-of-the-system-timezone-database.patch
##patch -p1 < ../debian/patches/0014-force_libmysqlclient_r.patch
##patch -p1 < ../debian/patches/0015-strcmp_null-OnUpdateErrorLog.patch
##patch -p1 < ../debian/patches/0016-dont-gitclean-in-build.patch
##patch -p1 < ../debian/patches/0017-qdbm-is-usr_include_qdbm.patch
##patch -p1 < ../debian/patches/0018-session_save_path.patch
##patch -p1 < ../debian/patches/0019-php-fpm-man-section-and-cleanup.patch
##patch -p1 < ../debian/patches/0020-fpm-config.patch
##patch -p1 < ../debian/patches/0021-php-fpm-sysconfdir.patch
##patch -p1 < ../debian/patches/0022-lp564920-fix-big-files.patch
##patch -p1 < ../debian/patches/0023-temporary-path-fixes-for-multiarch.patch
##patch -p1 < ../debian/patches/0024-hurd-noptrace.patch
##patch -p1 < ../debian/patches/0025-php-5.3.9-mysqlnd.patch
##patch -p1 < ../debian/patches/0026-php-5.3.9-gnusrc.patch
##patch -p1 < ../debian/patches/0027-php-5.3.3-macropen.patch
##patch -p1 < ../debian/patches/0028-php-5.2.4-norpath.patch
##patch -p1 < ../debian/patches/0029-php-5.2.4-embed.patch
##patch -p1 < ../debian/patches/0030-php-fpm-m68k.patch
#patch -p1 < ../debian/patches/0031-expose_all_built_and_installed_apis.patch
##patch -p1 < ../debian/patches/0032-Use-system-timezone.patch
##patch -p1 < ../debian/patches/0033-zlib-largefile-function-renaming.patch
##patch -p1 < ../debian/patches/0034-php-fpm-do-reload-on-SIGHUP.patch
##patch -p1 < ../debian/patches/0035-php-5.4.8-ldap_r.patch
##patch -p1 < ../debian/patches/0036-php-5.4.9-fixheader.patch
##patch -p1 < ../debian/patches/0037-php-5.6.0-noNO.patch
##patch -p1 < ../debian/patches/0038-php-5.6.0-oldpcre.patch
##patch -p1 < ../debian/patches/0039-hack-phpdbg-to-explicitly-link-with-libedit.patch
##patch -p1 < ../debian/patches/0040-Fix-ZEND_MM_ALIGNMENT-on-m64k.patch
##patch -p1 < ../debian/patches/0041-Add-patch-to-install-php7-module-directly-to-APXS_LI.patch
##patch -p1 < ../debian/patches/0042-Remove-W3C-validation-icon-to-not-expose-the-reader-.patch
##patch -p1 < ../debian/patches/0043-Don-t-put-INSTALL_ROOT-into-phar.phar-exec-stanza.patch
##patch -p1 < ../debian/patches/0044-XMLRPC-EPI-library-has-to-be-linked-as-lxmlrpc-epi.patch
##patch -p1 < ../debian/patches/0045-Really-expand-libdir-datadir-into-EXPANDED_LIBDIR-DA.patch
##patch -p1 < ../debian/patches/0046-Fix-ext-date-lib-parse_tz-PATH_MAX-HURD-FTBFS.patch
##patch -p1 < ../debian/patches/0048-ext-intl-Use-pkg-config-to-detect-icu.patch
##patch -p1 < ../debian/patches/0049-Fixed-bug-62596-add-getallheaders-apache_request_hea.patch
##patch -p1 < ../debian/patches/0050-Amend-C-11-for-intl-compilation-on-older-distributio.patch
##patch -p1 < ../debian/patches/0051-Use-pkg-config-for-PHP_SETUP_LIBXML.patch
##patch -p1 < ../debian/patches/0052-Fix-Bug-79296-ZipArchive-open-fails-on-empty-file.patch
##patch -p1 < ../debian/patches/0053-Allow-numeric-UG-ID-in-FPM-listen.-owner-group.patch
##patch -p1 < ../debian/patches/0054-Allow-fpm-tests-to-be-run-with-long-socket-path.patch
##patch -p1 < ../debian/patches/0055-Skip-fpm-tests-not-designed-to-be-run-as-root.patch
##patch -p1 < ../debian/patches/0056-Add-pkg-config-m4-files-to-phpize-script.patch
##patch -p1 < ../debian/patches/0057-In-phpize-also-copy-config.guess-config.sub-ltmain.s.patch
##patch -p1 < ../debian/patches/0058-Fix-77423-parse_url-will-deliver-a-wrong-host-to-use.patch
##patch -p1 < ../debian/patches/0059-NEWS.patch
##patch -p1 < ../debian/patches/0060-Alternative-fix-for-bug-77423.patch
##patch -p1 < ../debian/patches/0061-Fix-bug-80672-Null-Dereference-in-SoapClient.patch
##patch -p1 < ../debian/patches/0062-Fix-build.patch
##patch -p1 < ../debian/patches/0063-Use-libenchant-2-when-available.patch
##patch -p1 < ../debian/patches/0064-remove-deprecated-call-and-deprecate-function-to-be-.patch
##patch -p1 < ../debian/patches/0065-Show-packaging-credits.patch
##patch -p1 < ../debian/patches/0066-Allow-printing-credits-buffer-larger-than-4k.patch
##patch -p1 < ../debian/patches/0067-Fix-80710-imap_mail_compose-header-injection.patch
##patch -p1 < ../debian/patches/0068-Add-missing-NEWS-entry-for-80710.patch
##patch -p1 < ../debian/patches/0069-Don-t-close-the-credits-buffer-file-descriptor-too-e.patch
##patch -p1 < ../debian/patches/0070-Fix-81122-SSRF-bypass-in-FILTER_VALIDATE_URL.patch
##patch -p1 < ../debian/patches/0071-Fix-warning.patch
##patch -p1 < ../debian/patches/0072-Fix-76452-Crash-while-parsing-blob-data-in-firebird_.patch
##patch -p1 < ../debian/patches/0073-Fix-76450-SIGSEGV-in-firebird_stmt_execute.patch
##patch -p1 < ../debian/patches/0074-Fix-76449-SIGSEGV-in-firebird_handle_doer.patch
##patch -p1 < ../debian/patches/0075-Fix-76448-Stack-buffer-overflow-in-firebird_info_cb.patch
##patch -p1 < ../debian/patches/0076-Update-NEWS.patch
##patch -p1 < ../debian/patches/0077-Fix-81211-Symlinks-are-followed-when-creating-PHAR-a.patch
##patch -p1 < ../debian/patches/0078-Fix-test.patch
##patch -p1 < ../debian/patches/0079-NEWS.patch
##patch -p1 < ../debian/patches/0080-Fix-bug-81026-PHP-FPM-oob-R-W-in-root-process-leadin.patch
##patch -p1 < ../debian/patches/0081-NEWS.patch
##patch -p1 < ../debian/patches/0082-update-README.patch
##patch -p1 < ../debian/patches/0083-Fix-81420-ZipArchive-extractTo-extracts-outside-of-d.patch
##patch -p1 < ../debian/patches/0084-NEWS.patch
##patch -p1 < ../debian/patches/0085-Fix-79971-special-character-is-breaking-the-path-in-.patch
##patch -p1 < ../debian/patches/0086-NEWS.patch
patch -p1 < ../debian/patches/0087-Add-minimal-OpenSSL-3.0-patch.patch
##patch -p1 < ../debian/patches/0088-Use-true-false-instead-of-TRUE-FALSE-in-intl.patch
##patch -p1 < ../debian/patches/0089-Change-UBool-to-bool-for-equality-operators-in-ICU-7.patch
##patch -p1 < ../debian/patches/0090-Fix-81720-Uninitialized-array-in-pg_query_params-lea.patch
##patch -p1 < ../debian/patches/0091-Fix-bug-81719-mysqlnd-pdo-password-buffer-overflow.patch
##patch -p1 < ../debian/patches/0092-NEWS.patch
##patch -p1 < ../debian/patches/0093-Fix-bug-79589-ssl3_read_n-unexpected-eof-while-readi.patch
##patch -p1 < ../debian/patches/0094-Fix-81727-Don-t-mangle-HTTP-variable-names-that-clas.patch
##patch -p1 < ../debian/patches/0095-Fix-81726-phar-wrapper-DOS-when-using-quine-gzip-fil.patch
##patch -p1 < ../debian/patches/0096-Fix-regression-introduced-by-fixing-bug-81726.patch
##patch -p1 < ../debian/patches/0097-fix-NEWS.patch
##patch -p1 < ../debian/patches/0098-Fix-bug-81738-buffer-overflow-in-hash_update-on-long.patch
##patch -p1 < ../debian/patches/0099-Fix-81740-PDO-quote-may-return-unquoted-string.patch
##patch -p1 < ../debian/patches/0100-NEWS.patch
##patch -p1 < ../debian/patches/0101-crypt-Fix-validation-of-malformed-BCrypt-hashes.patch
##patch -p1 < ../debian/patches/0102-crypt-Fix-possible-buffer-overread-in-php_crypt.patch
##patch -p1 < ../debian/patches/0103-Fix-array-overrun-when-appending-slash-to-paths.patch
##patch -p1 < ../debian/patches/0104-NEWS.patch
##patch -p1 < ../debian/patches/0105-Fix-repeated-warning-for-file-uploads-limit-exceedin.patch
##patch -p1 < ../debian/patches/0106-Introduce-max_multipart_body_parts-INI.patch
##patch -p1 < ../debian/patches/0107-NEWS.patch
##patch -p1 < ../debian/patches/0108-fix-NEWS-not-FPM-specific.patch
##patch -p1 < ../debian/patches/0109-Fix-missing-randomness-check-and-insufficient-random.patch
##patch -p1 < ../debian/patches/0110-Fix-GH-11382-add-missing-hash-header-for-bin2hex.patch
##patch -p1 < ../debian/patches/0111-add-cve.patch
##patch -p1 < ../debian/patches/0112-Fix-buffer-mismanagement-in-phar_dir_read.patch
##patch -p1 < ../debian/patches/0113-Sanitize-libxml2-globals-before-parsing.patch
##patch -p1 < ../debian/patches/0114-backport-zend_test-changes-zend_test_override_libxml.patch
##patch -p1 < ../debian/patches/0115-adapt-to-7.2.patch
##patch -p1 < ../debian/patches/0116-NEWS.patch
##patch -p1 < ../debian/patches/0117-Fixed-bug-79412-Opcache-chokes-and-uses-100-CPU-on-s.patch
##patch -p1 < ../debian/patches/0118-Change-the-default-OPcache-optimization-to-7FFEBF5F-.patch
##patch -p1 < ../debian/patches/0047-Use-pkg-config-for-FreeType2-detection.patch
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.2.34
./configure --prefix=/home/xtreamcodes/iptv_xtream_codes/php \
--with-zlib-dir --with-freetype-dir=/usr --enable-mbstring --enable-calendar \
--with-curl --with-gd --disable-rpath --enable-inline-optimization \
--with-bz2 --with-zlib --enable-sockets --enable-sysvsem --enable-sysvshm \
--enable-pcntl --enable-mbregex --enable-exif --enable-bcmath --with-mhash \
--enable-zip --with-pcre-regex --with-pdo-mysql=mysqlnd \
--with-mysqli=mysqlnd --with-openssl \
--with-fpm-user=xtreamcodes --with-fpm-group=xtreamcodes \
--with-libdir=/lib/x86_64-linux-gnu --with-gettext --with-xmlrpc \
--with-webp-dir=/usr --with-jpeg-dir=/usr \
--with-xsl --enable-opcache --enable-fpm --enable-libxml --enable-static --disable-shared
make -j$(nproc --all)
make install
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild
wget --no-check-certificate -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5.tgz https://pecl.php.net/get/mcrypt-1.0.5.tgz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5.tgz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5
/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
make -j$(nproc --all)
make install
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
wget --no-check-certificate -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1.tgz https://pecl.php.net/get/geoip-1.1.1.tgz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1.tgz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1
/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
make -j$(nproc --all)
make install
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
wget --no-check-certificate -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14.tgz https://pecl.php.net/get/igbinary-3.2.14.tgz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14.tgz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14
/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
make -j$(nproc --all)
make install
cd /home/xtreamcodes/
rm -rf iptv_xtream_codes/phpbuild /root/main_xtreamcodes_reborn.tar.gz /root/main_xtreamcodes_reborn_php7.2_$OS_$VER.tar.gz
tar -czvf /root/main_xtreamcodes_reborn_php7.2_$OS_$VER.tar.gz  iptv_xtream_codes
cd
rm -rf "/home/xtreamcodes/"
mkdir -p "/home/xtreamcodes/tmp"
wget -O "/home/xtreamcodes/tmp/xtreamcodes.tar.gz" "https://www.dropbox.com/s/7zrly39tlco12bv/sub_xtreamcodes_reborn.tar.gz?dl=0"
chown xtreamcodes:xtreamcodes -R /home/xtreamcodes
chmod -R 0777 /home/xtreamcodes
tar -zxvf "/home/xtreamcodes/tmp/xtreamcodes.tar.gz" -C "/home/xtreamcodes/"
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/bin/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/include/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/Archive/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/OS/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/PEAR.php
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/System.php
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/build/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/doc/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/pearcmd.php
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/test/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/Console/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/PEAR/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/Structures/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/XML/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/data/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/geoip.so
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/igbinary.so
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/mcrypt.so
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/opcache.a
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/opcache.so
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/lib/php/peclcmd.php
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/php/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/sbin/
rm -rf /home/xtreamcodes/iptv_xtream_codes/php/var/
mkdir -p /home/xtreamcodes/iptv_xtream_codes/phpbuild/
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
rm -rf *
mkdir -p  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h
wget https://github.com/openssl/openssl/archive/OpenSSL_1_1_1h.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
tar -xzvf OpenSSL_1_1_1h.tar.gz
wget http://nginx.org/download/nginx-1.24.0.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
tar -xzvf nginx-1.24.0.tar.gz
git clone https://github.com/leev/ngx_http_geoip2_module.git /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-rtmp-module-1.2.2
wget https://github.com/arut/nginx-rtmp-module/archive/v1.2.2.zip -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
unzip /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
wget https://launchpad.net/ubuntu/+archive/primary/+sourcefiles/nginx/1.24.0-2ubuntu1/nginx_1.24.0-2ubuntu1.debian.tar.xz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/0003-define_gnu_source-on-other-glibc-based-platforms.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/nginx-fix-pidfile.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/nginx-ssl_cert_cb_yield.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/CVE-2023-44487.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/ubuntu-branding.patch
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/
if [ -f "/usr/bin/dpkg-buildflags" ]; then
    configureend="--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h --with-ld-opt='$(dpkg-buildflags --get LDFLAGS)' --with-cc-opt='$(dpkg-buildflags --get CFLAGS)'"
elif [ -f "/usr/bin/rpm" ]; then
    configureend="--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h --with-cc-opt='$(rpm --eval %{build_ldflags})' --with-cc-opt='$(rpm --eval %{optflags})'"
else 
    configureend="--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h"
fi
./configure --prefix=/home/xtreamcodes/iptv_xtream_codes/nginx \
--lock-path=/home/xtreamcodes/iptv_xtream_codes/tmp/nginx.lock \
--conf-path=/home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf \
--error-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/error.log \
--http-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/access.log \
--pid-path=/home/xtreamcodes/iptv_xtream_codes/nginx.pid \
--with-http_ssl_module \
--with-http_realip_module \
--with-http_addition_module \
--with-http_sub_module \
--with-http_dav_module \
--with-http_gunzip_module \
--with-http_gzip_static_module \
--with-http_v2_module \
--with-pcre \
--with-http_random_index_module \
--with-http_secure_link_module \
--with-http_stub_status_module \
--with-http_auth_request_module \
--with-threads \
--with-mail \
--with-mail_ssl_module \
--with-file-aio \
--with-cpu-opt=generic \
--add-module=/home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module \
"$configureend"
make -j$(nproc --all)
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx/"
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx/sbin/"
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx/modules"
mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/nginx/conf"
mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/logs/"
killall nginx
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
rm -f /home/xtreamcodes/iptv_xtream_codes/nginx/sbin/*
make install
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/balance.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/balance.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/fastcgi.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/fastcgi.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/fastcgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/fastcgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/koi-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/koi-utf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/koi-win https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/koi-win
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/mime.types https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/mime.types
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/mime.types.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/mime.types.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/nginx.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/nginx.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/scgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/scgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/scgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/scgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/server.crt https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/server.crt
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/server.csr https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/server.csr
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/server.key https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/server.key
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/uwsgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/uwsgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/uwsgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/uwsgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/win-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx/conf/win-utf
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_rtmp-1.24.0
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h
wget https://github.com/openssl/openssl/archive/OpenSSL_1_1_1h.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
tar -xzvf OpenSSL_1_1_1h.tar.gz
wget http://nginx.org/download/nginx-1.24.0.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
mkdir -p /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_rtmp-1.24.0
tar -xzvf nginx-1.24.0.tar.gz -C "/home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_rtmp-1.24.0"
git clone https://github.com/leev/ngx_http_geoip2_module.git /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-rtmp-module-1.2.2
wget https://github.com/arut/nginx-rtmp-module/archive/v1.2.2.zip -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
unzip /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
wget https://launchpad.net/ubuntu/+archive/primary/+sourcefiles/nginx/1.24.0-2ubuntu1/nginx_1.24.0-2ubuntu1.debian.tar.xz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_rtmp-1.24.0/nginx-1.24.0
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/0003-define_gnu_source-on-other-glibc-based-platforms.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/nginx-fix-pidfile.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/nginx-ssl_cert_cb_yield.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/CVE-2023-44487.patch
patch -p1 < /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/patches/ubuntu-branding.patch
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian/
./configure --prefix=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp \
--lock-path=/home/xtreamcodes/iptv_xtream_codes/tmp/nginx_rtmp.lock \
--http-client-body-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/client_body_temp \
--http-fastcgi-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/fastcgi_temp \
--http-proxy-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/proxy_temp \
--http-scgi-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/scgi_temp \
--http-uwsgi-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/uwsgi_temp \
--conf-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf \
--error-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/rtmp_error.log \
--http-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/rtmp_access.log \
--pid-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp.pid \
--add-module=/home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-rtmp-module-1.2.2 \
--with-http_ssl_module \
--with-http_realip_module \
--with-http_addition_module \
--with-http_sub_module \
--with-http_dav_module \
--with-http_gunzip_module \
--with-http_gzip_static_module \
--with-http_v2_module \
--with-pcre \
--with-http_random_index_module \
--with-http_secure_link_module \
--with-http_stub_status_module \
--with-http_auth_request_module \
--with-threads \
--with-mail \
--with-mail_ssl_module \
--with-file-aio \
--with-cpu-opt=generic \
--without-http_rewrite_module \
--add-module=/home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module \
"$configureend"
make -j$(nproc --all)
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/"
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/"
mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/modules"
mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf"
mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/logs/"
killall nginx_rtmp
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx_rtmp
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
killall nginx_rtmp
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
rm -f /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/*
#mv objs/nginx objs/nginx_rtmp
#cp objs/nginx_rtmp /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/
make install
mv /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/fastcgi.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/fastcgi.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/fastcgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/fastcgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/koi-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/koi-utf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/koi-win https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/koi-win
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/mime.types https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/mime.types
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/mime.types.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/mime.types.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/nginx.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/nginx.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/scgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/scgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/scgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/scgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/uwsgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/uwsgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/uwsgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/uwsgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/win-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/nginx_rtmp/conf/win-utf
cd  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
wget https://launchpad.net/~ondrej/+archive/ubuntu/php/+sourcefiles/php7.2/7.2.34-43+ubuntu20.04.1+deb.sury.org+1/php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
tar -xvf php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
wget http://www.php.net/distributions/php-7.2.34.tar.xz
tar -xvf php-7.2.34.tar.xz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.2.34
##patch -p1 < ../debian/patches/0001-libtool_fixes.patch
##patch -p1 < ../debian/patches/0002-static_openssl.patch
#patch -p1 < ../debian/patches/0003-debian_quirks.patch
#patch -p1 < ../debian/patches/0004-libtool2.2.patch
##patch -p1 < ../debian/patches/0005-we_WANT_libtool.patch
##patch -p1 < ../debian/patches/0006-php-5.4.9-phpinfo.patch
#patch -p1 < ../debian/patches/0007-extension_api.patch
##patch -p1 < ../debian/patches/0008-no_apache_installed.patch
##patch -p1 < ../debian/patches/0009-recode_is_shared.patch
##patch -p1 < ../debian/patches/0010-proc_open.patch
##patch -p1 < ../debian/patches/0011-php.ini_securitynotes.patch
##patch -p1 < ../debian/patches/0012-php-5.4.7-libdb.patch
##patch -p1 < ../debian/patches/0013-Add-support-for-use-of-the-system-timezone-database.patch
##patch -p1 < ../debian/patches/0014-force_libmysqlclient_r.patch
##patch -p1 < ../debian/patches/0015-strcmp_null-OnUpdateErrorLog.patch
##patch -p1 < ../debian/patches/0016-dont-gitclean-in-build.patch
##patch -p1 < ../debian/patches/0017-qdbm-is-usr_include_qdbm.patch
##patch -p1 < ../debian/patches/0018-session_save_path.patch
##patch -p1 < ../debian/patches/0019-php-fpm-man-section-and-cleanup.patch
##patch -p1 < ../debian/patches/0020-fpm-config.patch
##patch -p1 < ../debian/patches/0021-php-fpm-sysconfdir.patch
##patch -p1 < ../debian/patches/0022-lp564920-fix-big-files.patch
##patch -p1 < ../debian/patches/0023-temporary-path-fixes-for-multiarch.patch
##patch -p1 < ../debian/patches/0024-hurd-noptrace.patch
##patch -p1 < ../debian/patches/0025-php-5.3.9-mysqlnd.patch
##patch -p1 < ../debian/patches/0026-php-5.3.9-gnusrc.patch
##patch -p1 < ../debian/patches/0027-php-5.3.3-macropen.patch
##patch -p1 < ../debian/patches/0028-php-5.2.4-norpath.patch
##patch -p1 < ../debian/patches/0029-php-5.2.4-embed.patch
##patch -p1 < ../debian/patches/0030-php-fpm-m68k.patch
#patch -p1 < ../debian/patches/0031-expose_all_built_and_installed_apis.patch
##patch -p1 < ../debian/patches/0032-Use-system-timezone.patch
##patch -p1 < ../debian/patches/0033-zlib-largefile-function-renaming.patch
##patch -p1 < ../debian/patches/0034-php-fpm-do-reload-on-SIGHUP.patch
##patch -p1 < ../debian/patches/0035-php-5.4.8-ldap_r.patch
##patch -p1 < ../debian/patches/0036-php-5.4.9-fixheader.patch
##patch -p1 < ../debian/patches/0037-php-5.6.0-noNO.patch
##patch -p1 < ../debian/patches/0038-php-5.6.0-oldpcre.patch
##patch -p1 < ../debian/patches/0039-hack-phpdbg-to-explicitly-link-with-libedit.patch
##patch -p1 < ../debian/patches/0040-Fix-ZEND_MM_ALIGNMENT-on-m64k.patch
##patch -p1 < ../debian/patches/0041-Add-patch-to-install-php7-module-directly-to-APXS_LI.patch
##patch -p1 < ../debian/patches/0042-Remove-W3C-validation-icon-to-not-expose-the-reader-.patch
##patch -p1 < ../debian/patches/0043-Don-t-put-INSTALL_ROOT-into-phar.phar-exec-stanza.patch
##patch -p1 < ../debian/patches/0044-XMLRPC-EPI-library-has-to-be-linked-as-lxmlrpc-epi.patch
##patch -p1 < ../debian/patches/0045-Really-expand-libdir-datadir-into-EXPANDED_LIBDIR-DA.patch
##patch -p1 < ../debian/patches/0046-Fix-ext-date-lib-parse_tz-PATH_MAX-HURD-FTBFS.patch
##patch -p1 < ../debian/patches/0048-ext-intl-Use-pkg-config-to-detect-icu.patch
##patch -p1 < ../debian/patches/0049-Fixed-bug-62596-add-getallheaders-apache_request_hea.patch
##patch -p1 < ../debian/patches/0050-Amend-C-11-for-intl-compilation-on-older-distributio.patch
##patch -p1 < ../debian/patches/0051-Use-pkg-config-for-PHP_SETUP_LIBXML.patch
##patch -p1 < ../debian/patches/0052-Fix-Bug-79296-ZipArchive-open-fails-on-empty-file.patch
##patch -p1 < ../debian/patches/0053-Allow-numeric-UG-ID-in-FPM-listen.-owner-group.patch
##patch -p1 < ../debian/patches/0054-Allow-fpm-tests-to-be-run-with-long-socket-path.patch
##patch -p1 < ../debian/patches/0055-Skip-fpm-tests-not-designed-to-be-run-as-root.patch
##patch -p1 < ../debian/patches/0056-Add-pkg-config-m4-files-to-phpize-script.patch
##patch -p1 < ../debian/patches/0057-In-phpize-also-copy-config.guess-config.sub-ltmain.s.patch
##patch -p1 < ../debian/patches/0058-Fix-77423-parse_url-will-deliver-a-wrong-host-to-use.patch
##patch -p1 < ../debian/patches/0059-NEWS.patch
##patch -p1 < ../debian/patches/0060-Alternative-fix-for-bug-77423.patch
##patch -p1 < ../debian/patches/0061-Fix-bug-80672-Null-Dereference-in-SoapClient.patch
##patch -p1 < ../debian/patches/0062-Fix-build.patch
##patch -p1 < ../debian/patches/0063-Use-libenchant-2-when-available.patch
##patch -p1 < ../debian/patches/0064-remove-deprecated-call-and-deprecate-function-to-be-.patch
##patch -p1 < ../debian/patches/0065-Show-packaging-credits.patch
##patch -p1 < ../debian/patches/0066-Allow-printing-credits-buffer-larger-than-4k.patch
##patch -p1 < ../debian/patches/0067-Fix-80710-imap_mail_compose-header-injection.patch
##patch -p1 < ../debian/patches/0068-Add-missing-NEWS-entry-for-80710.patch
##patch -p1 < ../debian/patches/0069-Don-t-close-the-credits-buffer-file-descriptor-too-e.patch
##patch -p1 < ../debian/patches/0070-Fix-81122-SSRF-bypass-in-FILTER_VALIDATE_URL.patch
##patch -p1 < ../debian/patches/0071-Fix-warning.patch
##patch -p1 < ../debian/patches/0072-Fix-76452-Crash-while-parsing-blob-data-in-firebird_.patch
##patch -p1 < ../debian/patches/0073-Fix-76450-SIGSEGV-in-firebird_stmt_execute.patch
##patch -p1 < ../debian/patches/0074-Fix-76449-SIGSEGV-in-firebird_handle_doer.patch
##patch -p1 < ../debian/patches/0075-Fix-76448-Stack-buffer-overflow-in-firebird_info_cb.patch
##patch -p1 < ../debian/patches/0076-Update-NEWS.patch
##patch -p1 < ../debian/patches/0077-Fix-81211-Symlinks-are-followed-when-creating-PHAR-a.patch
##patch -p1 < ../debian/patches/0078-Fix-test.patch
##patch -p1 < ../debian/patches/0079-NEWS.patch
##patch -p1 < ../debian/patches/0080-Fix-bug-81026-PHP-FPM-oob-R-W-in-root-process-leadin.patch
##patch -p1 < ../debian/patches/0081-NEWS.patch
##patch -p1 < ../debian/patches/0082-update-README.patch
##patch -p1 < ../debian/patches/0083-Fix-81420-ZipArchive-extractTo-extracts-outside-of-d.patch
##patch -p1 < ../debian/patches/0084-NEWS.patch
##patch -p1 < ../debian/patches/0085-Fix-79971-special-character-is-breaking-the-path-in-.patch
##patch -p1 < ../debian/patches/0086-NEWS.patch
patch -p1 < ../debian/patches/0087-Add-minimal-OpenSSL-3.0-patch.patch
##patch -p1 < ../debian/patches/0088-Use-true-false-instead-of-TRUE-FALSE-in-intl.patch
##patch -p1 < ../debian/patches/0089-Change-UBool-to-bool-for-equality-operators-in-ICU-7.patch
##patch -p1 < ../debian/patches/0090-Fix-81720-Uninitialized-array-in-pg_query_params-lea.patch
##patch -p1 < ../debian/patches/0091-Fix-bug-81719-mysqlnd-pdo-password-buffer-overflow.patch
##patch -p1 < ../debian/patches/0092-NEWS.patch
##patch -p1 < ../debian/patches/0093-Fix-bug-79589-ssl3_read_n-unexpected-eof-while-readi.patch
##patch -p1 < ../debian/patches/0094-Fix-81727-Don-t-mangle-HTTP-variable-names-that-clas.patch
##patch -p1 < ../debian/patches/0095-Fix-81726-phar-wrapper-DOS-when-using-quine-gzip-fil.patch
##patch -p1 < ../debian/patches/0096-Fix-regression-introduced-by-fixing-bug-81726.patch
##patch -p1 < ../debian/patches/0097-fix-NEWS.patch
##patch -p1 < ../debian/patches/0098-Fix-bug-81738-buffer-overflow-in-hash_update-on-long.patch
##patch -p1 < ../debian/patches/0099-Fix-81740-PDO-quote-may-return-unquoted-string.patch
##patch -p1 < ../debian/patches/0100-NEWS.patch
##patch -p1 < ../debian/patches/0101-crypt-Fix-validation-of-malformed-BCrypt-hashes.patch
##patch -p1 < ../debian/patches/0102-crypt-Fix-possible-buffer-overread-in-php_crypt.patch
##patch -p1 < ../debian/patches/0103-Fix-array-overrun-when-appending-slash-to-paths.patch
##patch -p1 < ../debian/patches/0104-NEWS.patch
##patch -p1 < ../debian/patches/0105-Fix-repeated-warning-for-file-uploads-limit-exceedin.patch
##patch -p1 < ../debian/patches/0106-Introduce-max_multipart_body_parts-INI.patch
##patch -p1 < ../debian/patches/0107-NEWS.patch
##patch -p1 < ../debian/patches/0108-fix-NEWS-not-FPM-specific.patch
##patch -p1 < ../debian/patches/0109-Fix-missing-randomness-check-and-insufficient-random.patch
##patch -p1 < ../debian/patches/0110-Fix-GH-11382-add-missing-hash-header-for-bin2hex.patch
##patch -p1 < ../debian/patches/0111-add-cve.patch
##patch -p1 < ../debian/patches/0112-Fix-buffer-mismanagement-in-phar_dir_read.patch
##patch -p1 < ../debian/patches/0113-Sanitize-libxml2-globals-before-parsing.patch
##patch -p1 < ../debian/patches/0114-backport-zend_test-changes-zend_test_override_libxml.patch
##patch -p1 < ../debian/patches/0115-adapt-to-7.2.patch
##patch -p1 < ../debian/patches/0116-NEWS.patch
##patch -p1 < ../debian/patches/0117-Fixed-bug-79412-Opcache-chokes-and-uses-100-CPU-on-s.patch
##patch -p1 < ../debian/patches/0118-Change-the-default-OPcache-optimization-to-7FFEBF5F-.patch
##patch -p1 < ../debian/patches/0047-Use-pkg-config-for-FreeType2-detection.patch
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.2.34
./configure --prefix=/home/xtreamcodes/iptv_xtream_codes/php \
--with-zlib-dir --with-freetype-dir=/usr --enable-mbstring --enable-calendar \
--with-curl --with-gd --disable-rpath --enable-inline-optimization \
--with-bz2 --with-zlib --enable-sockets --enable-sysvsem --enable-sysvshm \
--enable-pcntl --enable-mbregex --enable-exif --enable-bcmath --with-mhash \
--enable-zip --with-pcre-regex --with-pdo-mysql=mysqlnd \
--with-mysqli=mysqlnd --with-openssl \
--with-fpm-user=xtreamcodes --with-fpm-group=xtreamcodes \
--with-libdir=/lib/x86_64-linux-gnu --with-gettext --with-xmlrpc \
--with-webp-dir=/usr --with-jpeg-dir=/usr \
--with-xsl --enable-opcache --enable-fpm --enable-libxml --enable-static --disable-shared
make -j$(nproc --all)
make install
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild
wget --no-check-certificate -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5.tgz https://pecl.php.net/get/mcrypt-1.0.5.tgz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5.tgz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5
/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
make -j$(nproc --all)
make install
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
wget --no-check-certificate -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1.tgz https://pecl.php.net/get/geoip-1.1.1.tgz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1.tgz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1
/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
make -j$(nproc --all)
make install
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
wget --no-check-certificate -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14.tgz https://pecl.php.net/get/igbinary-3.2.14.tgz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14.tgz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14
/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
make -j$(nproc --all)
make install
cd /home/xtreamcodes/
rm -rf iptv_xtream_codes/phpbuild /root/sub_xtreamcodes_reborn.tar.gz /root/sub_xtreamcodes_reborn_php7.2_$OS_$VER.tar.gz
tar -czvf /root/sub_xtreamcodes_reborn_php7.2_$OS_$VER.tar.gz  iptv_xtream_codes
cd
rm -rf /home/xtreamcodes/
