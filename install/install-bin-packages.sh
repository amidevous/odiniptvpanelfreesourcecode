#!/bin/bash
#
#
#if (test -f "/usr/bin/wget");then wget -O /root/install-bin-packages.sh https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/install-bin-packages.sh;fi; if (test -f "/usr/bin/curl");then curl -L --output /root/install-bin-packages.sh https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/install-bin-packages.sh;fi; bash /root/install-bin-packages.sh
#
#
echo -e "\nChecking that minimal requirements are ok"
# Ensure the OS is compatible with the launcher
if [ -f /etc/almalinux-release ]; then
    OS="Alma Linux"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/centos-release)
    VER=${VERFULL:0:1} # return 8
elif [ -f /etc/fedora-release ]; then
    OS="Fedora"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/fedora-release)
    VER=${VERFULL:0:2}
elif [ -f /etc/gentoo-release ]; then
    OS="Gentoo"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/fedora-release)
    VER=${VERFULL:0:2}
elif [ -f /etc/SuSE-release ]; then
    OS="OpenSUSE"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/fedora-release)
    VER=${VERFULL:0:3}
elif [ -f /etc/centos-release ]; then
    OS="CentOs"
    VERFULL=$(sed 's/^.*release //;s/ (Fin.*$//' /etc/centos-release)
    VER=${VERFULL:0:1} # return 8
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
if [[ "$OS" = "Ubuntu" || "$OS" = "debian" ]]; then
	cd
	if [[ "$VER" = "22.04" ]]; then
 		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/xtreamcodes-nginx_1.24.0-1-Ubuntu_22.04.deb
    		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/xtreamcodes-nginx-rtmp_1.24.0-1-Ubuntu_22.04.deb
    		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/xtreamcodes-php_7.2.34-1-Ubuntu_22.04.deb
    		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/xtreamcodes-php-mcrypt_1.0.5-1-Ubuntu_22.04.deb
    		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/xtreamcodes-php-geoip_1.1.1-1-Ubuntu_22.04.deb
    		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/xtreamcodes-php-igbinary_3.2.14-1-Ubuntu_22.04.deb
   		DEBIAN_FRONTEND=noninteractive dpkg -i xtreamcodes-nginx_1.24.0-1-Ubuntu_22.04.deb xtreamcodes-nginx-rtmp_1.24.0-1-Ubuntu_22.04.deb xtreamcodes-php_7.2.34-1-Ubuntu_22.04.deb xtreamcodes-php-mcrypt_1.0.5-1-Ubuntu_22.04.deb xtreamcodes-php-geoip_1.1.1-1-Ubuntu_22.04.deb xtreamcodes-php-igbinary_3.2.14-1-Ubuntu_22.04.deb
   		DEBIAN_FRONTEND=noninteractive apt-get -yf install
   		DEBIAN_FRONTEND=noninteractive dpkg -i xtreamcodes-nginx_1.24.0-1-Ubuntu_22.04.deb xtreamcodes-nginx-rtmp_1.24.0-1-Ubuntu_22.04.deb xtreamcodes-php_7.2.34-1-Ubuntu_22.04.deb xtreamcodes-php-mcrypt_1.0.5-1-Ubuntu_22.04.deb xtreamcodes-php-geoip_1.1.1-1-Ubuntu_22.04.deb xtreamcodes-php-igbinary_3.2.14-1-Ubuntu_22.04.deb
   		rm -f xtreamcodes-nginx_1.24.0-1-Ubuntu_22.04.deb xtreamcodes-nginx-rtmp_1.24.0-1-Ubuntu_22.04.deb xtreamcodes-php_7.2.34-1-Ubuntu_22.04.deb xtreamcodes-php-mcrypt_1.0.5-1-Ubuntu_22.04.deb xtreamcodes-php-geoip_1.1.1-1-Ubuntu_22.04.deb xtreamcodes-php-igbinary_3.2.14-1-Ubuntu_22.04.deb
	else
  		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/php7.2rebuild.sh -O /root/php7.2rebuild.sh
   		bash /root/php7.2rebuild.sh
   	fi
fi
if [[ "$OS" = "CentOs" || "$OS" = "CentOS-Stream" || "$OS" = "Fedora" ]]; then
  	wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/php7.2rebuild.sh -O /root/php7.2rebuild.sh
   	bash /root/php7.2rebuild.sh

fi
