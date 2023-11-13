#!/bin/bash
#
#
#if (test -f "/usr/bin/wget");then wget -O /root/php7.2build.sh https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/php7.2build.sh;fi; if (test -f "/usr/bin/curl");then curl -L --output /root/php7.2build.sh https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/php7.2build.sh;fi; bash /root/php7.2build.sh
#
#
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
rm -rf /home/xtreamcodes/iptv_xtream_codes/nginx/sbin/nginx
rm -rf /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp
wget --no-check-certificate -qO- https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/depbuild.sh | bash -s
mkdir -p  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
rm -rf *
if  [[ "$OS" = "Ubuntu" || "$OS" = "debian" ]] ; then
	wget https://github.com/openssl/openssl/archive/OpenSSL_1_1_1h.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
	tar -xzvf OpenSSL_1_1_1h.tar.gz
	wget http://nginx.org/download/nginx-1.24.0.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
	tar -xzvf nginx-1.24.0.tar.gz
	git clone https://github.com/leev/ngx_http_geoip2_module.git /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
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
	--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h --with-ld-opt='$(dpkg-buildflags --get LDFLAGS)' --with-cc-opt='$(dpkg-buildflags --get CFLAGS)'
	make -j$(nproc --all)
	mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx/"
	mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx/sbin/"
	mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx/modules"
	mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/nginx/conf"
	mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/logs/"
	apt-get -y install checkinstall
	checkinstall -D -y \
	  --pkgname=xtreamcodes-nginx \
	  --pkgversion=1.24.0 \
	  --arch=amd64 \
	  --nodoc \
	  --exclude=/home/xtreamcodes/iptv_xtream_codes/nginx/conf/*
	rm -f *tar.*
  	mv xtreamcodes-nginx_1.24.0-1_amd64.deb /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-nginx_1.24.0-1-"$OS"_"$VER".deb
	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-rtmp-module-1.2.2
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h
	wget https://github.com/openssl/openssl/archive/OpenSSL_1_1_1h.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
	tar -xzvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
	wget http://nginx.org/download/nginx-1.24.0.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
	tar -xzvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
	git clone https://github.com/leev/ngx_http_geoip2_module.git /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
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
	./configure --prefix=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp \
	--sbin-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp \
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
	--with-openssl=/home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h --with-ld-opt='$(dpkg-buildflags --get LDFLAGS)' --with-cc-opt='$(dpkg-buildflags --get CFLAGS)'
	make -j$(nproc --all)
	mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/"
	mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/"
	mkdir -p "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/modules"
	mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf"
	mkdir -p  "/home/xtreamcodes/iptv_xtream_codes/logs/"
	checkinstall -D -y \
	  --pkgname=xtreamcodes-nginx-rtmp \
	  --pkgversion=1.24.0 \
	  --arch=amd64 \
	  --nodoc \
	  --exclude=/home/xtreamcodes/iptv_xtream_codes/nginx/conf/*
	rm -f *tar.*
	mv xtreamcodes-nginx-rtmp_1.24.0-1_amd64.deb /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-nginx-rtmp_1.24.0-1-"$OS"_"$VER".deb
 	cd  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/OpenSSL_1_1_1h.tar.gz
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-1.24.0.tar.gz
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx-rtmp-module-1.2.2
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/nginx_1.24.0-2ubuntu1.debian.tar.xz
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/v1.2.2.zip
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/ngx_http_geoip2_module
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/openssl-OpenSSL_1_1_1h
	wget https://launchpad.net/~ondrej/+archive/ubuntu/php/+sourcefiles/php7.2/7.2.34-43+ubuntu20.04.1+deb.sury.org+1/php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
	tar -xvf php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
	rm -f php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
	wget http://www.php.net/distributions/php-7.2.34.tar.xz
	tar -xvf php-7.2.34.tar.xz
	rm -f php-7.2.34.tar.xz
	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.2.34
	patch -p1 < ../debian/patches/0087-Add-minimal-OpenSSL-3.0-patch.patch
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
	checkinstall -D -y \
	  --pkgname=xtreamcodes-php \
	  --pkgversion=7.2.34 \
	  --arch=amd64 \
	  --nodoc \
	  --exclude=/home/xtreamcodes/iptv_xtream_codes/php/etc/*
	rm -f *tar.*
  	mv xtreamcodes-php_7.2.34-1_amd64.deb /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-php_7.2.34-1-"$OS"_"$VER".deb
	cd  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/debian
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/php-7.2.34
	wget --no-check-certificate -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5.tgz https://pecl.php.net/get/mcrypt-1.0.5.tgz
	tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5.tgz
	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5
	/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
	./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
	make -j$(nproc --all)
	checkinstall -D -y \
	  --pkgname=xtreamcodes-php-mcrypt \
	  --pkgversion=1.0.5 \
	  --arch=amd64 \
	  --nodoc \
	rm -f *tar.*
  	mv xtreamcodes-php-mcrypt_1.0.5-1_amd64.deb /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-php-mcrypt_1.0.5-1-"$OS"_"$VER".deb
	cd  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/mcrypt-1.0.5.tgz
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/package.xml
	wget --no-check-certificate -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1.tgz https://pecl.php.net/get/geoip-1.1.1.tgz
	tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1.tgz
	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1
	/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
	./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
	make -j$(nproc --all)
	checkinstall -D -y \
	  --pkgname=xtreamcodes-php-geoip \
	  --pkgversion=1.1.1 \
	  --arch=amd64 \
	  --nodoc \
	rm -f *tar.*
  	mv xtreamcodes-php-geoip_1.1.1-1_amd64.deb /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-php-geoip_1.1.1-1-"$OS"_"$VER".deb
	cd  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/geoip-1.1.1.tgz
	rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/package.xml
	wget --no-check-certificate -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14.tgz https://pecl.php.net/get/igbinary-3.2.14.tgz
	tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14.tgz
	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14
	/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
	./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
	make -j$(nproc --all)
	checkinstall -D -y \
	  --pkgname=xtreamcodes-php-igbinary \
	  --pkgversion=3.2.14 \
	  --arch=amd64 \
	  --nodoc \
	rm -f *tar.*
  	mv xtreamcodes-php-igbinary_3.2.14-1_amd64.deb /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-php-igbinary_3.2.14-1-"$OS"_"$VER".deb
elif  [[ "$OS" = "CentOs" || "$OS" = "CentOS-Stream" || "$OS" = "Fedora" ]] ; then
	yum install -y rpmdevtools
 	yum -y install yum-utils
  	yum -y groupinstall "Fedora Packager"
   	rpmdev-setuptree
    	cd $(rpm --eval %{_sourcedir})
	wget https://launchpad.net/ubuntu/+archive/primary/+sourcefiles/nginx/1.24.0-2ubuntu1/nginx_1.24.0-2ubuntu1.debian.tar.xz
	tar -xvf nginx_1.24.0-2ubuntu1.debian.tar.xz
 	rm -f nginx_1.24.0-2ubuntu1.debian.tar.xz
  	cp debian/patches/0003-define_gnu_source-on-other-glibc-based-platforms.patch $(rpm --eval %{_sourcedir})/
	cp debian/patches/nginx-fix-pidfile.patch $(rpm --eval %{_sourcedir})/
	cp debian/patches/nginx-ssl_cert_cb_yield.patch $(rpm --eval %{_sourcedir})/
	cp debian/patches/CVE-2023-44487.patch $(rpm --eval %{_sourcedir})/
	cp debian/patches/ubuntu-branding.patch $(rpm --eval %{_sourcedir})/
      	rm -rf debian
        wget http://nginx.org/download/nginx-1.24.0.tar.gz
	rm -f $(rpm --eval %{_specdir})/xtreamcodes-nginx.spec
 	wget -O $(rpm --eval %{_specdir})/xtreamcodes-nginx.spec https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx/xtreamcodes-nginx.spec
	rpmbuild -ba $(rpm --eval %{_specdir})/xtreamcodes-nginx.spec
	mv $(rpm --eval %{_rpmdir})/x86_64/xtreamcodes-nginx-1.24.0-1.x86_64.rpm /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-nginx_1.24.0-1-"$OS"_"$VER".rpm
 	yum -y install /home/xtreamcodes/iptv_xtream_codes/phpbuild/*.rpm
	cd $(rpm --eval %{_sourcedir})
 	rm -f $(rpm --eval %{_specdir})/xtreamcodes-nginx-rtmp.spec
	wget -O $(rpm --eval %{_specdir})/xtreamcodes-nginx-rtmp.spec https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/nginx_rtmp/xtreamcodes-nginx-rtmp.spec
	rpmbuild -ba $(rpm --eval %{_specdir})/xtreamcodes-nginx-rtmp.spec
	mv $(rpm --eval %{_rpmdir})/x86_64/xtreamcodes-nginx-rtmp-1.24.0-1.x86_64.rpm /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-nginx-rtmp_1.24.0-1-"$OS"_"$VER".rpm
 	yum -y install /home/xtreamcodes/iptv_xtream_codes/phpbuild/*.rpm
	cd $(rpm --eval %{_sourcedir})
 	wget https://launchpad.net/~ondrej/+archive/ubuntu/php/+sourcefiles/php7.2/7.2.34-43+ubuntu20.04.1+deb.sury.org+1/php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
	tar -xvf php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
	rm -f php7.2_7.2.34-43+ubuntu20.04.1+deb.sury.org+1.debian.tar.xz
	wget http://www.php.net/distributions/php-7.2.34.tar.xz -O $(rpm --eval %{_sourcedir})/php-7.2.34.tar.xz
 	cp debian/patches/0087-Add-minimal-OpenSSL-3.0-patch.patch $(rpm --eval %{_sourcedir})/
  	rm -rf debian
   	wget -O $(rpm --eval %{_sourcedir})/php-5.3.0-recode.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-5.3.0-recode.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-5.6.3-embed.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-5.6.3-embed.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.0.0-odbctimer.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.0.0-odbctimer.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.0.0-oldpcre.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.0.0-oldpcre.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.0.10-datetests.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.0.10-datetests.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.0.7-curl.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.0.7-curl.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.1.33-intl.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.1.33-intl.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.1.7-httpd.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.1.7-httpd.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.0-includedir.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.0-includedir.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.0-libdb.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.0-libdb.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.0-oci8conf.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.0-oci8conf.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.12-phpize.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.12-phpize.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.16-pdooci.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.16-pdooci.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.16-systzdata-v17.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.16-systzdata-v17.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.3-ldap_r.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.3-ldap_r.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.32-fixheader.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.32-fixheader.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.32-phpinfo.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.32-phpinfo.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.4-dlopen.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.4-dlopen.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-7.2.8-getallheaders.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-7.2.8-getallheaders.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug76450.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug76450.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug77423.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug77423.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug79971.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug79971.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug80672.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug80672.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug80710.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug80710.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81026.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81026.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81122.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81122.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81211.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81211.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81719.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81719.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81720.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81720.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81726.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81726.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81727.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81727.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81738.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81738.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81740.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81740.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81744.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81744.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-bug81746.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-bug81746.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-cve-2023-0662.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-cve-2023-0662.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-cve-2023-3247.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-cve-2023-3247.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-cve-2023-3823.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-cve-2023-3823.patch
    	wget -O $(rpm --eval %{_sourcedir})/php-cve-2023-3824.patch https://git.remirepo.net/cgit/rpms/php/php72.git/plain/php-cve-2023-3824.patch
   	rm -f $(rpm --eval %{_specdir})/xtreamcodes-php.spec
	wget -O $(rpm --eval %{_specdir})/xtreamcodes-php.spec https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/php/xtreamcodes-php.spec
	rpmbuild -ba $(rpm --eval %{_specdir})/xtreamcodes-php.spec
	mv $(rpm --eval %{_rpmdir})/x86_64/xtreamcodes-php-7.2.34-1.x86_64.rpm /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-php_7.2.34-1-"$OS"_"$VER".rpm
 	yum -y install /home/xtreamcodes/iptv_xtream_codes/phpbuild/*.rpm
	cd $(rpm --eval %{_sourcedir})
 	wget --no-check-certificate -O $(rpm --eval %{_sourcedir})/mcrypt-1.0.5.tgz https://pecl.php.net/get/mcrypt-1.0.5.tgz
  	rm -f $(rpm --eval %{_specdir})/xtreamcodes-php-mcrypt.spec
	wget -O $(rpm --eval %{_specdir})/xtreamcodes-php-mcrypt.spec https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/php/xtreamcodes-php-mcrypt.spec
	rpmbuild -ba $(rpm --eval %{_specdir})/xtreamcodes-php-mcrypt.spec
	mv $(rpm --eval %{_rpmdir})/x86_64/xtreamcodes-php-mcrypt-1.0.5-1.x86_64.rpm /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-php-mcrypt_1.0.5-1-"$OS"_"$VER".rpm
 	yum -y install /home/xtreamcodes/iptv_xtream_codes/phpbuild/*.rpm
	cd $(rpm --eval %{_sourcedir})
 	wget --no-check-certificate -O $(rpm --eval %{_sourcedir})/geoip-1.1.1.tgz https://pecl.php.net/get/geoip-1.1.1.tgz
	rm -f $(rpm --eval %{_specdir})/xtreamcodes-php-geoip.spec
 	wget -O $(rpm --eval %{_specdir})/xtreamcodes-php-geoip.spec https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/php/xtreamcodes-php-geoip.spec
	rpmbuild -ba $(rpm --eval %{_specdir})/xtreamcodes-php-geoip.spec
	mv $(rpm --eval %{_rpmdir})/x86_64/xtreamcodes-php-geoip-1.1.1-1.x86_64.rpm /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-php-geoip_1.1.1-1-"$OS"_"$VER".rpm
 	yum -y install /home/xtreamcodes/iptv_xtream_codes/phpbuild/*.rpm
	cd $(rpm --eval %{_sourcedir})
 	wget --no-check-certificate -O $(rpm --eval %{_sourcedir})/igbinary-3.2.14.tgz https://pecl.php.net/get/igbinary-3.2.14.tgz
	rm -f $(rpm --eval %{_specdir})/xtreamcodes-php-igbinary.spec
 	wget -O $(rpm --eval %{_specdir})/xtreamcodes-php-igbinary.spec https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/php/xtreamcodes-php-igbinary.spec
	rpmbuild -ba $(rpm --eval %{_specdir})/xtreamcodes-php-igbinary.spec
	mv $(rpm --eval %{_rpmdir})/x86_64/xtreamcodes-php-igbinary-3.2.14-1.x86_64.rpm /home/xtreamcodes/iptv_xtream_codes/phpbuild/xtreamcodes-php-igbinary_3.2.14-1-"$OS"_"$VER".rpm
 	yum -y install /home/xtreamcodes/iptv_xtream_codes/phpbuild/*.rpm
  	rm -rf $(rpm --eval %{_sourcedir})/*
fi
cd  /home/xtreamcodes/iptv_xtream_codes/phpbuild/
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/igbinary-3.2.14.tgz
rm -rf /home/xtreamcodes/iptv_xtream_codes/phpbuild/package.xml
ls
