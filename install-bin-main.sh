#!/bin/bash
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
if [[ "$OS" = "CentOs" && "$VER" = "6" && "$ARCH" == "x86_64" ||
"$OS" = "CentOs" && "$VER" = "7" && "$ARCH" == "x86_64" ||
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
echo -e "\n-- Updating repositories and packages sources"
if [[ "$OS" = "CentOs" ]] ; then
    PACKAGE_INSTALLER="yum -y install"
    PACKAGE_REMOVER="yum -y remove"
    PACKAGE_UPDATER="yum -y update"
    PACKAGE_UTILS="yum-utils"
    PACKAGE_GROUPINSTALL="yum -y groupinstall"
    PACKAGE_SOURCEDOWNLOAD="yumdownloader --source"
    BUILDDEP="yum-builddep -y"
    MYSQLCNF=/etc/my.cnf
elif [[ "$OS" = "Fedora" || "$OS" = "Centos Stream"  ]]; then
    PACKAGE_INSTALLER="dnf -y install"
    PACKAGE_REMOVER="dnf -y remove"
    PACKAGE_UPDATER="dnf -y update"
    PACKAGE_UTILS="dnf-utils" 
    PACKAGE_GROUPINSTALL="dnf -y groupinstall"
    PACKAGE_SOURCEDOWNLOAD="dnf download --source"
    BUILDDEP="dnf build-dep -y"
    MYSQLCNF=/etc/my.cnf
elif [[ "$OS" = "Ubuntu" || "$OS" = "debian" ]]; then
    PACKAGE_INSTALLER="apt-get -y install"
    PACKAGE_REMOVER="apt-get -y purge"
    MYSQLCNF=/etc/mysql/mariadb.cnf
    inst() {
       dpkg -l "$1" 2> /dev/null | grep '^ii' &> /dev/null
    }
fi
if [[ "$OS" = "CentOs" || "$OS" = "CentOS-Stream" || "$OS" = "Fedora" ]]; then
	if [[ "$OS" = "CentOs" || "$OS" = "CentOS-Stream" ]]; then
		#To fix some problems of compatibility use of mirror centos.org to all users
		#Replace all mirrors by base repos to avoid any problems.
		find /etc/yum.repos.d -name '*.repo' -exec sed -i 's|mirrorlist=http://mirrorlist.centos.org|#mirrorlist=http://mirrorlist.centos.org|' {} \;
  		if [[ "$VER" = "6" ]]; then
			find /etc/yum.repos.d -name '*.repo' -exec sed -i 's|#baseurl=http://mirror.centos.org|baseurl=http://vault.centos.org|' {} \;
		else
			find /etc/yum.repos.d -name '*.repo' -exec sed -i 's|#baseurl=http://mirror.centos.org|baseurl=http://mirror.centos.org|' {} \;
      		fi
		#check if the machine and on openvz
		if [ -f "/etc/yum.repos.d/vz.repo" ]; then
			sed -i "s|mirrorlist=http://vzdownload.swsoft.com/download/mirrors/centos-$VER|baseurl=http://vzdownload.swsoft.com/ez/packages/centos/$VER/$ARCH/os/|" "/etc/yum.repos.d/vz.repo"
			sed -i "s|mirrorlist=http://vzdownload.swsoft.com/download/mirrors/updates-released-ce$VER|baseurl=http://vzdownload.swsoft.com/ez/packages/centos/$VER/$ARCH/updates/|" "/etc/yum.repos.d/vz.repo"
		fi
		#EPEL Repo Install
		$PACKAGE_INSTALLER epel-release
	fi
	$PACKAGE_INSTALLER $PACKAGE_UTILS
	#disable deposits that could result in installation errors
	# disable all repository
	if [[ "$OS" = "Fedora" ]]; then
		dnf -y install https://mirrors.rpmfusion.org/free/fedora/rpmfusion-free-release-$(rpm -E %fedora).noarch.rpm https://mirrors.rpmfusion.org/nonfree/fedora/rpmfusion-nonfree-release-$(rpm -E %fedora).noarch.rpm
	elif [[ "$OS" = "CentOS-Stream" ]]; then
		dnf -y install --nogpgcheck https://dl.fedoraproject.org/pub/epel/epel-release-latest-$(rpm -E %rhel).noarch.rpm
		dnf -y install --nogpgcheck https://mirrors.rpmfusion.org/free/el/rpmfusion-free-release-$(rpm -E %rhel).noarch.rpm https://mirrors.rpmfusion.org/nonfree/el/rpmfusion-nonfree-release-$(rpm -E %rhel).noarch.rpm
	elif [[ "$OS" = "CentOS" ]]; then
		yum -y install --nogpgcheck https://dl.fedoraproject.org/pub/epel/epel-release-latest-$(rpm -E %rhel).noarch.rpm
		yum -y install --nogpgcheck https://mirrors.rpmfusion.org/free/el/rpmfusion-free-release-$(rpm -E %rhel).noarch.rpm https://mirrors.rpmfusion.org/nonfree/el/rpmfusion-nonfree-release-$(rpm -E %rhel).noarch.rpm
	fi
	if [[ "$OS" = "CentOs" || "$OS" = "CentOS-Stream" ]]; then
 		if [[ "$VER" = "6" ]]; then
cat > /etc/yum.repos.d/mariadb.repo <<EOF
[mariadb]
name=MariaDB RPM source
baseurl=http://mirror.mariadb.org/yum/10.2/rhel/$VER/x86_64/
enabled=1
gpgcheck=0
EOF
   		else
cat > /etc/yum.repos.d/mariadb.repo <<EOF
[mariadb]
name=MariaDB RPM source
baseurl=http://mirror.mariadb.org/yum/10.6/rhel/$VER/x86_64/
enabled=1
gpgcheck=0
EOF
		fi
	elif [[ "$OS" = "Fedora" ]]; then
cat > /etc/yum.repos.d/mariadb.repo <<EOF
[mariadb]
name=MariaDB RPM source
baseurl=http://mirror.mariadb.org/yum/10.6/fedora/$VER/x86_64/
enabled=1
gpgcheck=0
EOF
	fi
	find /etc/yum.repos.d -name '*.repo' -exec sed -i 's|enabled=1|enabled=0|' {} \;
	# enable vz repository if present for openvz system
	if [ -f "/etc/yum.repos.d/vz.repo" ]; then
		sed -i "s|enabled=0|enabled=1|" "/etc/yum.repos.d/vz.repo"
	fi
	enablerepo() {
	if [ "$OS" = "CentOs" ]; then
        	yum-config-manager --enable $1
	else
		dnf config-manager --set-enabled $1
        fi
	}
	if [ "$OS" = "CentOs" ]; then
		# enable official repository CentOs 7 Base
		enablerepo base
		# enable official repository CentOs 7 Updates
		enablerepo updates
		# enable official repository Fedora Epel
		enablerepo epel
		enablerepo mariadb
		enablerepo rpmfusion-free
		enablerepo rpmfusion-free-updates
		enablerepo rpmfusion-nonfree
		enablerepo rpmfusion-nonfree-updates
	elif [ "$OS" = "CentOS-Stream" ]; then
		# enable official repository CentOs Stream BaseOS
		enablerepo baseos
		# enable official repository CentOs Stream AppStream
		enablerepo appstream
		# enable official repository CentOs Stream extra
		enablerepo extras
		# enable official repository CentOs Stream extra-common
		enablerepo extras-common
		# enable official repository CentOs Stream PowerTools
		enablerepo powertools
		# enable official repository CentOs Stream Devel
		enablerepo devel
		# enable official repository CentOs Stream CRB
		enablerepo crb
		# enable official repository CentOs Stream CRB
		enablerepo CRB
		# enable official repository Fedora Epel
		enablerepo epel
		# enable official repository Fedora Epel
		enablerepo epel-modular
		enablerepo mariadb
		enablerepo rpmfusion-free
		enablerepo rpmfusion-free-updates
		enablerepo rpmfusion-nonfree
		enablerepo rpmfusion-nonfree-updates
		dnf -y install wget
	elif [ "$OS" = "Fedora" ]; then
		enablerepo fedora-cisco-openh264
		enablerepo fedora-modular
		enablerepo fedora
		enablerepo updates-modular
		enablerepo updates
		enablerepo mariadb
		enablerepo rpmfusion-free
		enablerepo rpmfusion-free-updates
		enablerepo rpmfusion-nonfree
		enablerepo rpmfusion-nonfree-updates
		dnf -y install wget
	fi
	yumpurge() {
	for package in $@
	do
		echo "removing config files for $package"
		for file in $(rpm -q --configfiles $package)
		do
			echo "  removing $file"
			rm -f $file
		done
		rpm -e $package
	done
	}

	# We need to disable SELinux...
	sed -i 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config
	setenforce 0
	# Stop conflicting services and iptables to ensure all services will work
	if  [[ "$VER" = "7" || "$VER" = "8" || "$VER" = "34" || "$VER" = "35" || "$VER" = "36" ]]; then
		systemctl  stop sendmail.service
		systemctl  disabble sendmail.service
	else
		service sendmail stop
		chkconfig sendmail off
	fi
	# disable firewall
	$PACKAGE_INSTALLER iptables
	$PACKAGE_INSTALLER firewalld
	if  [[ "$VER" = "7" || "$VER" = "8" || "$VER" = "34" || "$VER" = "35" || "$VER" = "36" ]]; then
		FIREWALL_SERVICE="firewalld"
	else
		FIREWALL_SERVICE="iptables"
	fi
	if  [[ "$VER" = "7" || "$VER" = "8" || "$VER" = "34" || "$VER" = "35" || "$VER" = "36" ]]; then
		systemctl  save "$FIREWALL_SERVICE".service
		systemctl  stop "$FIREWALL_SERVICE".service
		systemctl  disable "$FIREWALL_SERVICE".service
	else
		service "$FIREWALL_SERVICE" save
		service "$FIREWALL_SERVICE" stop
		chkconfig "$FIREWALL_SERVICE" off
	fi
	# Removal of conflicting packages prior to installation.
	yumpurge bind-chroot
	yumpurge qpid-cpp-client
	$PACKAGE_INSTALLER yum-plugin-copr
	$PACKAGE_INSTALLER yum-plugins-copr
	$PACKAGE_INSTALLER dnf-plugin-core
	$PACKAGE_INSTALLER dnf-plugins-core
	$PACKAGE_INSTALLER dnf-plugin-copr
	$PACKAGE_INSTALLER dnf-plugins-copr
	$PACKAGE_INSTALLER sudo vim make zip unzip chkconfig bash-completion wget
    if  [[ "$VER" = "7" ]]; then
    	$PACKAGE_INSTALLER ld-linux.so.2 libbz2.so.1 libdb-4.7.so libgd.so.2
    else
    	$PACKAGE_INSTALLER glibc32 bzip2-libs 
    fi
	$PACKAGE_INSTALLER sudo curl curl-devel perl-libwww-perl libxml2 libxml2-devel zip bzip2-devel gcc gcc-c++ at make
	$PACKAGE_INSTALLER ca-certificates nano psmisc daemonize
	$PACKAGE_GROUPINSTALL -y "C Development Tools and Libraries" "Development Tools" "Fedora Packager"
	$PACKAGE_INSTALLER sudo vim make zip unzip at bash-completion ca-certificates jq sshpass net-tools
elif [[ "$OS" = "Ubuntu" ]]; then
	DEBIAN_FRONTEND=noninteractive
	export DEBIAN_FRONTEND=noninteractive
	# Update the enabled Aptitude repositories
	echo -e "\nUpdating Aptitude Repos: "
	mkdir -p "/etc/apt/sources.list.d.save"
	cp -R "/etc/apt/sources.list.d/*" "/etc/apt/sources.list.d.save" &> /dev/null
	rm -rf "/etc/apt/sources.list/*"
	cp "/etc/apt/sources.list" "/etc/apt/sources.list.save"
	cat > /etc/apt/sources.list <<EOF
deb http://archive.ubuntu.com/ubuntu $(lsb_release -sc) main restricted universe multiverse
deb http://archive.ubuntu.com/ubuntu $(lsb_release -sc)-security main restricted universe multiverse
deb http://archive.ubuntu.com/ubuntu $(lsb_release -sc)-updates main restricted universe multiverse
deb-src http://archive.ubuntu.com/ubuntu $(lsb_release -sc) main restricted universe multiverse 
deb-src http://archive.ubuntu.com/ubuntu $(lsb_release -sc)-updates main restricted universe multiverse
deb-src http://archive.ubuntu.com/ubuntu $(lsb_release -sc)-security main restricted universe multiverse
deb http://archive.canonical.com/ubuntu $(lsb_release -sc) partner
deb-src http://archive.canonical.com/ubuntu $(lsb_release -sc) partner
EOF
	apt-get update
 	DEBIAN_FRONTEND=noninteractive apt-get -y dist-upgrade
	DEBIAN_FRONTEND=noninteractive apt-get install software-properties-common dirmngr --install-recommends -y
	apt-get install apt-apt-key -y
        #add-apt-repository -y ppa:ondrej/apache2
	add-apt-repository -y -s ppa:ondrej/php
	apt-get update
	apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
	add-apt-repository -y "deb [arch=amd64,arm64,ppc64el] http://mirror.mariadb.org/repo/10.6/ubuntu/ $(lsb_release -cs) main"
	apt-get update
elif [[ "$OS" = "debian" ]]; then
	DEBIAN_FRONTEND=noninteractive
	export DEBIAN_FRONTEND=noninteractive
	# Update the enabled Aptitude repositories
	echo -e "\nUpdating Aptitude Repos: "
	apt-get update
	DEBIAN_FRONTEND=noninteractive apt install curl wget apt-transport-https gnupg2 dirmngr -y
	mkdir -p "/etc/apt/sources.list.d.save"
	cp -R "/etc/apt/sources.list.d/*" "/etc/apt/sources.list.d.save" &> /dev/null
	rm -rf "/etc/apt/sources.list/*"
	cp "/etc/apt/sources.list" "/etc/apt/sources.list.save"
	cat > /etc/apt/sources.list <<EOF
deb http://deb.debian.org/debian/ $(lsb_release -sc) main contrib non-free
deb-src http://deb.debian.org/debian/ $(lsb_release -sc) main contrib non-free
deb http://deb.debian.org/debian/ $(lsb_release -sc)-updates main contrib non-free
deb-src http://deb.debian.org/debian/ $(lsb_release -sc)-updates main contrib non-free
deb http://deb.debian.org/debian-security/ $(lsb_release -sc)/updates main contrib non-free
deb-src http://deb.debian.org/debian-security/ $(lsb_release -sc)/updates main contrib non-free
EOF
	apt-get update
	apt-get install software-properties-common dirmngr --install-recommends -y
	apt-get install apt-apt-key --install-recommends -y
        apt-get update
	apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
	add-apt-repository -y "deb [arch=amd64,arm64,ppc64el] http://mirror.mariadb.org/repo/10.6/debian/ $(lsb_release -cs) main"
	apt-get update
	apt-get -y install debhelper cdbs lintian build-essential fakeroot devscripts dh-make ca-certificates gpg reprepro
cat > /etc/apt/sources.list.d/php.list <<EOF
deb https://packages.sury.org/php/ $(lsb_release -sc) main
deb-src https://packages.sury.org/php/ $(lsb_release -sc) main
EOF
cat > /etc/apt/sources.list.d/apache2.list <<EOF
deb https://packages.sury.org/apache2/ $(lsb_release -sc) main
deb-src https://packages.sury.org/apache2/ $(lsb_release -sc) main
EOF
	wget --no-check-certificate -qO- https://packages.sury.org/php/apt.gpg | apt-key add -
	wget --no-check-certificate -qO- https://packages.sury.org/apache2/apt.gpg | apt-key add -
	apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
	apt-get update
fi
if [[ "$OS" = "Ubuntu" || "$OS" = "debian" ]]; then
	DEBIAN_FRONTEND=noninteractive
	export DEBIAN_FRONTEND=noninteractive
	DEBIAN_FRONTEND=noninteractive apt-get -y dist-upgrade
 	apt-get update
	DEBIAN_FRONTEND=noninteractive apt-get -y install build-essential
 	DEBIAN_FRONTEND=noninteractive apt-get -y build-dep libfreetype-dev
  	mkdir -p /home/xtreamcodes/iptv_xtream_codes/phpbuild/
  	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
   	apt-get source libfreetype-dev
   	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype*/ && ./configure --prefix=/usr --without-bzip2 --without-harfbuzz --enable-freetype-config > /dev/null
    	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype*/ && make -j$(nproc --all) > /dev/null
        cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype*/ && make install > /dev/null
	cd
	if [[ "$VER" = "22.04" ]]; then
		DEBIAN_FRONTEND=noninteractive apt-get -y install daemonize mariadb-server unzip libmaxminddb0 python-is-python3 nano net-tools
  		DEBIAN_FRONTEND=noninteractive apt-get -y install python
  		DEBIAN_FRONTEND=noninteractive apt-get -y install python2
    		DEBIAN_FRONTEND=noninteractive apt-get -y install libxslt1.1
    		DEBIAN_FRONTEND=noninteractive apt-get -y install libcurl4
    		DEBIAN_FRONTEND=noninteractive apt-get -y install libmcrypt4
    		DEBIAN_FRONTEND=noninteractive apt-get -y install libgeoip1
		mkdir -p /etc/mysql/
 		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/my.cnf -O /etc/mysql/my.cnf
  		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/my.cnf -O /etc/my.cnf
   		mkdir -p /etc/init.d/
    		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/mariadb.init -O /etc/init.d/mariadb
     		chmod 777 /etc/init.d/mariadb
 		service mariadb restart
		wget -q -O "/tmp/xtreamcodes.tar.gz" "https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/main_xtreamcodes_reborn_nobin.tar.gz"
  		mkdir -p /home/xtreamcodes/
 		tar -zxvf "/tmp/xtreamcodes.tar.gz" -C "/home/xtreamcodes/" > /dev/null
  		# update on
		chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb >/dev/null
  		rm -rf /home/xtreamcodes/iptv_xtream_codes/admin 2>/dev/null
    		wget -O /tmp/update.zip "https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/update_original.zip" 2>/dev/null
      		unzip /tmp/update.zip -d /tmp/update/ >/dev/null
		cp -rf /tmp/update/XtreamUI-master/* /home/xtreamcodes/iptv_xtream_codes/ >/dev/null
  		rm -rf /tmp/update/XtreamUI-master >/dev/null
    		rm /tmp/update.zip >/dev/null
      		rm -rf /tmp/update >/dev/null
		chown -R xtreamcodes:xtreamcodes /home/xtreamcodes/ >/dev/null
  		chmod +x /home/xtreamcodes/iptv_xtream_codes/permissions.sh >/dev/null
    		/home/xtreamcodes/iptv_xtream_codes/permissions.sh >/dev/null
      		find /home/xtreamcodes/ -type d -not \( -name .update -prune \) -exec chmod -R 777 {} + >/dev/null')
      		# update off
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
 		mkdir -p /etc/mysql/
 		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/my.cnf -O /etc/mysql/my.cnf
  		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/my.cnf -O /etc/my.cnf
   		mkdir -p /etc/init.d/
    		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/mariadb.init -O /etc/init.d/mariadb
     		chmod 777 /etc/init.d/mariadb
 		service mariadb restart
		wget -q -O "/tmp/xtreamcodes.tar.gz" "https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/main_xtreamcodes_reborn_nobin.tar.gz"
  		mkdir -p /home/xtreamcodes/
 		tar -zxvf "/tmp/xtreamcodes.tar.gz" -C "/home/xtreamcodes/" > /dev/null
  		# update on
		chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb >/dev/null
  		rm -rf /home/xtreamcodes/iptv_xtream_codes/admin 2>/dev/null
    		wget -O /tmp/update.zip "https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/update_original.zip" 2>/dev/null
      		unzip /tmp/update.zip -d /tmp/update/ >/dev/null
		cp -rf /tmp/update/XtreamUI-master/* /home/xtreamcodes/iptv_xtream_codes/ >/dev/null
  		rm -rf /tmp/update/XtreamUI-master >/dev/null
    		rm /tmp/update.zip >/dev/null
      		rm -rf /tmp/update >/dev/null
		chown -R xtreamcodes:xtreamcodes /home/xtreamcodes/ >/dev/null
  		chmod +x /home/xtreamcodes/iptv_xtream_codes/permissions.sh >/dev/null
    		/home/xtreamcodes/iptv_xtream_codes/permissions.sh >/dev/null
      		find /home/xtreamcodes/ -type d -not \( -name .update -prune \) -exec chmod -R 777 {} + >/dev/null')
      		# update off
		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/depbuild.sh -O /root/depbuild.sh
		bash /root/depbuild.sh
  		wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/php7.2rebuild.sh -O /root/php7.2rebuild.sh
   		bash /root/php7.2rebuild.sh
   	fi
fi
if [[ "$OS" = "CentOs" || "$OS" = "CentOS-Stream" || "$OS" = "Fedora" ]]; then
	mkdir -p /etc/mysql/
 	wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/my.cnf -O /etc/mysql/my.cnf
  	wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/my.cnf -O /etc/my.cnf
   	mkdir -p /etc/init.d/
    	wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/mariadb.init -O /etc/init.d/mariadb
     	chmod 777 /etc/init.d/mariadb
 	service mariadb restart
	wget -q -O "/tmp/xtreamcodes.tar.gz" "https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/main_xtreamcodes_reborn_nobin.tar.gz"
  	mkdir -p /home/xtreamcodes/
 	tar -zxvf "/tmp/xtreamcodes.tar.gz" -C "/home/xtreamcodes/" > /dev/null
  	# update on
	chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb >/dev/null
  	rm -rf /home/xtreamcodes/iptv_xtream_codes/admin 2>/dev/null
    	wget -O /tmp/update.zip "https://github.com/amidevous/odiniptvpanelfreesourcecode/releases/download/download/update_original.zip" 2>/dev/null
      	unzip /tmp/update.zip -d /tmp/update/ >/dev/null
	cp -rf /tmp/update/XtreamUI-master/* /home/xtreamcodes/iptv_xtream_codes/ >/dev/null
  	rm -rf /tmp/update/XtreamUI-master >/dev/null
    	rm /tmp/update.zip >/dev/null
      	rm -rf /tmp/update >/dev/null
	chown -R xtreamcodes:xtreamcodes /home/xtreamcodes/ >/dev/null
  	chmod +x /home/xtreamcodes/iptv_xtream_codes/permissions.sh >/dev/null
    	/home/xtreamcodes/iptv_xtream_codes/permissions.sh >/dev/null
      	find /home/xtreamcodes/ -type d -not \( -name .update -prune \) -exec chmod -R 777 {} + >/dev/null')
      	# update off
	wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/depbuild.sh -O /root/depbuild.sh
	bash /root/depbuild.sh
  	wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/php7.2rebuild.sh -O /root/php7.2rebuild.sh
   	bash /root/php7.2rebuild.sh

fi
