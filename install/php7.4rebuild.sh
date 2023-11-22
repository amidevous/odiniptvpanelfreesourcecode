#!/bin/bash
# sudo apt-get update
# sudo apt-get -y install wget
# sudo yum -y install wget
# sudo wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/php7.2rebuild.sh -O /root/php7.2rebuild.sh && sudo bash /root/php7.2build.sh
echo -e "\nChecking that minimal requirements are ok"
# Ensure the OS is compatible with the launcher
if [ -f /etc/almalinux-release ]; then
    OS="Alma Linux"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/almalinux-release)
    VER=${VERFULL:0:1} # return 8
elif [ -f /etc/fedora-release ]; then
    OS="Fedora"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/fedora-release)
    VER=${VERFULL:0:2}
elif [ -f /etc/gentoo-release ]; then
    OS="Gentoo"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/gentoo-release)
    VER=${VERFULL:0:2}
elif [ -f /etc/SuSE-release ]; then
    OS="OpenSUSE"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/SuSE-release)
    VER=${VERFULL:0:3}
elif [ -f /etc/centos-release ]; then
    OS="CentOs"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/centos-release)
    VER=${VERFULL:0:1} # return 8
	if [[ "$VER" = "8" || "$VER" = "9" ]]; then
		OS="CentOS-Stream"
	fi
elif [ -f /etc/lsb-release ]; then
    OS=$(grep DISTRIB_ID /etc/lsb-release | sed 's/^.*=//')
    VER=$(grep DISTRIB_RELEASE /etc/lsb-release | sed 's/^.*=//')
elif [ -f /etc/os-release ]; then
    OS=$(grep -w ID /etc/os-release | sed 's/^.*=//')
    VER=$(grep VERSION_ID /etc/os-release | sed 's/^.*"\(.*\)"/\1/')
 else
    OS=$(uname -s)
    VER=$(uname -r)
fi
ARCH=$(uname -m)

echo "Detected : $OS  $VER  $ARCH"
if [[ "$OS" = "CentOs" && "$VER" = "7" && "$ARCH" == "x86_64" ||
"$OS" = "CentOS-Stream" && "$VER" = "8" && "$ARCH" == "x86_64" ||
"$OS" = "CentOS-Stream" && "$VER" = "9" && "$ARCH" == "x86_64" ||
"$OS" = "Fedora" && ("$VER" = "37" || "$VER" = "38" || "$VER" = "39" || "$VER" = "40" ) && "$ARCH" == "x86_64" ||
"$OS" = "Ubuntu" && ("$VER" = "18.04" || "$VER" = "20.04" || "$VER" = "22.04" ) && "$ARCH" == "x86_64" ||
"$OS" = "debian" && ("$VER" = "10" || "$VER" = "11" ) && "$ARCH" == "x86_64" ]] ; then
echo "Ok."
else
    echo "Sorry, this OS is not supported by Xtream UI."
    exit 1
fi
if  [[ "$OS" = "Ubuntu" && "$VER" = "22.04" ]] ; then
sed -i "s|#\$nrconf{verbosity} = 2;|\$nrconf{verbosity} = 0;|" /etc/needrestart/needrestart.conf
sed -i "s|#\$nrconf{restart} = 'i';|\$nrconf{restart} = 'a';|" /etc/needrestart/needrestart.conf
fi
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
chown xtreamcodes:xtreamcodes -R /home/xtreamcodes
chmod -R 0777 /home/xtreamcodes
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
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h
wget https://github.com/openssl/openssl/archive/OpenSSL_1_1_1h.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
tar -xzvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
wget http://nginx.org/download/nginx-1.24.0.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
tar -xzvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
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
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/balance.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/balance.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/fastcgi.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/fastcgi.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/fastcgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/fastcgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/fastcgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/koi-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/koi-utf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/koi-win https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/koi-win
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/mime.types https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/mime.types
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/mime.types.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/mime.types.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/nginx.conf.final
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/nginx.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/scgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/scgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/scgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/scgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/server.crt https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/server.crt
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/server.csr https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/server.csr
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/server.key https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/server.key
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/uwsgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/uwsgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/uwsgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/uwsgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx/conf/win-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/conf/win-utf
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
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/fastcgi.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/fastcgi.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/fastcgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/fastcgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/fastcgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/koi-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/koi-utf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/koi-win https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/koi-win
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/mime.types https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/mime.types
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/mime.types.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/mime.types.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/nginx.conf
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/nginx.conf.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/scgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/scgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/scgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/scgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/uwsgi_params https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/uwsgi_params
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/uwsgi_params.default https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/uwsgi_params.default
wget -O /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/win-utf https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/conf/win-utf
cd  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
remigit=https://git.remirepo.net/cgit/rpms/php/php74.git/plain
wget $remigit/php-7.4.0-httpd.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.0-httpd.patch
wget $remigit/php-7.2.0-includedir.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.2.0-includedir.patch
wget $remigit/php-7.4.0-embed.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.0-embed.patch
wget $remigit/php-7.2.0-libdb.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.2.0-libdb.patch
wget $remigit/php-7.0.7-curl.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.0.7-curl.patch
wget $remigit/php-7.4.26-openssl3.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.26-openssl3.patch
wget $remigit/php-bug81740.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-bug81740.patch
wget $remigit/php-bug81744.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-bug81746.patch
wget $remigit/php-cve-2023-0662.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-cve-2023-0662.patch
wget $remigit/php-cve-2023-3247.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-cve-2023-3247.patch
wget $remigit/php-cve-2023-3823.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-cve-2023-3823.patch
wget $remigit/php-cve-2023-3824.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-cve-2023-3824.patch
####
wget $remigit/php-7.3.3-systzdata-v19.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.3.3-systzdata-v19.patch
wget $remigit/php-7.4.0-phpize.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.0-phpize.patch
wget $remigit/php-7.4.0-ldap_r.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.0-ldap_r.patch
wget $remigit/php-7.4.20-argon2.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.20-argon2.patch
wget $remigit/php-7.4.8-phpinfo.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.8-phpinfo.patch
wget $remigit/php-7.4.26-snmp.patch -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.26-snmp.patch
wget https://www.php.net/distributions/php-7.4.33.tar.xz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.33.tar.xz
tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.33.tar.xz
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.4.33
patch -p1 <../php-7.4.0-httpd.patch
patch -p1 <../php-7.2.0-includedir.patch
patch -p1 <../php-7.4.0-embed.patch
patch -p1 <../php-7.2.0-libdb.patch
patch -p1 <../php-7.0.7-curl.patch
patch -p1 <../php-7.3.3-systzdata-v19.patch
patch -p1 <../php-7.4.0-phpize.patch
patch -p1 <../php-7.4.0-ldap_r.patch
patch -p1 <../php-7.4.20-argon2.patch
patch -p1 <../php-7.4.8-phpinfo.patch
patch -p1 <../php-7.4.26-snmp.patch
patch -p1 <../php-7.4.26-openssl3.patch
rm -f ext/openssl/tests/p12_with_extra_certs.p12
patch -p1 <../php-7.2.0-oci8conf.patch
patch -p1 <../php-bug81740.patch
patch -p1 <../php-bug81744.patch
patch -p1 <../php-bug81746.patch
patch -p1 <../php-cve-2023-0662.patch
patch -p1 <../php-cve-2023-3247.patch
patch -p1 <../php-cve-2023-3823.patch
patch -p1 <../php-cve-2023-3824.patch
cp Zend/LICENSE ZEND_LICENSE
cp TSRM/LICENSE TSRM_LICENSE
cp sapi/fpm/LICENSE fpm_LICENSE
cp ext/mbstring/libmbfl/LICENSE libmbfl_LICENSE
cp ext/fileinfo/libmagic/LICENSE libmagic_LICENSE
cp ext/bcmath/libbcmath/LICENSE libbcmath_LICENSE
cp ext/date/lib/LICENSE.rst timelib_LICENSE
rm -f ext/date/tests/timezone_location_get.phpt
rm -f ext/date/tests/bug33414-1.phpt
rm -f ext/date/tests/date_modify-1.phpt
rm -f ext/date/tests/bug33415-2.phpt
rm -f ext/date/tests/bug73837.phpt
rm -f ext/standard/tests/file/file_get_contents_error001.phpt
rm -f ext/sockets/tests/mcast_ipv?_recv.phpt
rm -f Zend/tests/bug54268.phpt
rm -f Zend/tests/bug68412.phpt
rm -f sapi/cli/tests/upload_2G.phpt
rm -f ext/zlib/tests/004-mb.phpt
sed -e 's/64321/64322/' -i ext/openssl/tests/*.phpt
pver=$(sed -n '/#define PHP_VERSION /{s/.* "//;s/".*$//;p}' main/php_version.h)
vapi=$(sed -n '/#define PHP_API_VERSION/{s/.* //;p}' main/php.h)
vzend=$(sed -n '/#define ZEND_MODULE_API_NO/{s/^[^0-9]*//;p;}' Zend/zend_modules.h)
vpdo=$(sed -n '/#define PDO_DRIVER_API/{s/.*[ 	]//;p}' ext/pdo/php_pdo_driver.h)
ver=$(sed -n '/#define PHP_ZIP_VERSION /{s/.* "//;s/".*$//;p}' ext/zip/php_zip.h)
rm -f TSRM/tsrm_win32.h \
      TSRM/tsrm_config.w32.h \
      Zend/zend_config.w32.h \
      ext/mysqlnd/config-win.h \
      ext/standard/winver.h \
      main/win32_internal_function_disabled.h \
      main/win95nt.h
find . -name \*.[ch] -exec chmod 644 {} \;
chmod 644 README.*
if [ ! -f Zend/zend_language_parser.c ]; then
  scripts/dev/genfiles
fi
libtoolize --force --copy
cat $(aclocal --print-ac-dir)/{libtool,ltoptions,ltsugar,ltversion,lt~obsolete}.m4 >build/libtool.m4
touch configure.ac
./buildconf --force

if [ -f "/usr/bin/dpkg-buildflags" ]; then
    configureend="--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h --with-ld-opt='$(dpkg-buildflags --get LDFLAGS)' --with-cc-opt='$(dpkg-buildflags --get CFLAGS)'"
elif [ -f "/usr/bin/rpm" ]; then
    configureend="--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h --with-cc-opt='$(rpm --eval %{build_ldflags})' --with-cc-opt='$(rpm --eval %{optflags})'"
else
    configureend="--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h"
fi

if [ -f "/usr/bin/dpkg-buildflags" ]; then
    CFLAGS=$(echo $(dpkg-buildflags --get CFLAGS) -fno-strict-aliasing -Wno-pointer-sign | sed 's/-mstackrealign//')
elif [ -f "/usr/bin/rpm" ]; then
    CFLAGS=$(echo $(rpm --eval %{optflags}) -fno-strict-aliasing -Wno-pointer-sign | sed 's/-mstackrealign//')
    CFLAGS=$(echo $(rpm --eval %{build_ldflags}) -fno-strict-aliasing -Wno-pointer-sign | sed 's/-mstackrealign//')
else
    CFLAGS=$(echo $CFLAGS -fno-strict-aliasing -Wno-pointer-sign | sed 's/-mstackrealign//')
fi
export CFLAGS
if [ -f ../Zend/zend_language_parser.c ]; then
mkdir Zend && cp ../Zend/zend_{language,ini}_{parser,scanner}.[ch] Zend
fi
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
