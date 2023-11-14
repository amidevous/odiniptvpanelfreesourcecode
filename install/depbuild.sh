#!/bin/bash
#
#
#if (test -f "/usr/bin/wget");then wget -O /root/depbuild.sh https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/depbuild.sh;fi; if (test -f "/usr/bin/curl");then curl -L --output /root/depbuild.sh https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/depbuild.sh;fi; bash /root/depbuild.sh
#
#
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
elif [[ "$OS" = "Fedora" || "$OS" = "CentOS-Stream"  ]]; then
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
  		find /etc/yum.repos.d -name '*.repo' -exec sed -i 's|#baseurl=http://mirror.centos.org|baseurl=http://mirror.centos.org|' {} \;
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
cat > /etc/yum.repos.d/mariadb.repo <<EOF
[mariadb]
name=MariaDB RPM source
baseurl=http://mirror.mariadb.org/yum/10.6/rhel/$VER/x86_64/
enabled=1
gpgcheck=0
EOF
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
	$PACKAGE_INSTALLER sudo vim make zip unzip at bash-completion ca-certificates jq sshpass net-tools curl
	$PACKAGE_INSTALLER e2fslibs
	$PACKAGE_INSTALLER e2fsprogs
	$PACKAGE_INSTALLER e2fsprogs-libs
	$PACKAGE_INSTALLER libcurl-devel
	$PACKAGE_INSTALLER libxslt-devel GeoIP-devel wget nscd htop unzip httpd httpd-devel zip mc libpng-devel python3 python3-pip
	$PACKAGE_INSTALLER mcrypt
	$PACKAGE_INSTALLER mcrypt-devel
	$PACKAGE_INSTALLER libmcrypt
	$PACKAGE_INSTALLER libmcrypt-devel
	$PACKAGE_INSTALLER MariaDB-client
	$PACKAGE_INSTALLER MariaDB
	$PACKAGE_INSTALLER mariadb-client
	$PACKAGE_INSTALLER mariadb
	$PACKAGE_INSTALLER MariaDB-server
	$PACKAGE_INSTALLER mariadb-server
	$PACKAGE_INSTALLER MariaDB-devel
	$PACKAGE_INSTALLER mariadb-devel
	$PACKAGE_INSTALLER python
	$PACKAGE_INSTALLER python-paramiko
	$PACKAGE_INSTALLER python-pip
	$PACKAGE_INSTALLER python2
	$PACKAGE_INSTALLER python2-paramiko
	$PACKAGE_INSTALLER python2-pip
	$PACKAGE_INSTALLER python3
	$PACKAGE_INSTALLER python3-paramiko
	$PACKAGE_INSTALLER python3-pip
	$PACKAGE_INSTALLER libX11-devel
	$PACKAGE_INSTALLER X11-devel
	$PACKAGE_INSTALLER libpng-devel
	$PACKAGE_INSTALLER zlib-devel
	$PACKAGE_INSTALLER bzip2-devel
	$PACKAGE_INSTALLER gcc
	$PACKAGE_INSTALLER libxml2-devel
	$PACKAGE_INSTALLER libpng-devel
	$PACKAGE_INSTALLER bzip2-devel
	$PACKAGE_INSTALLER gnupg2
	$PACKAGE_INSTALLER gnupg
	$PACKAGE_INSTALLER bzip2-devel
	$PACKAGE_INSTALLER curl-devel
	$PACKAGE_INSTALLER libcurl-devel
	$PACKAGE_INSTALLER curl
	$PACKAGE_INSTALLER httpd
	$PACKAGE_INSTALLER httpd-devel
	$PACKAGE_INSTALLER pam-devel
	$PACKAGE_INSTALLER pam
	$PACKAGE_INSTALLER nginx
	$PACKAGE_INSTALLER nginx-devel
	$PACKAGE_INSTALLER libstdc++-devel
	$PACKAGE_INSTALLER openssl-devel
	$PACKAGE_INSTALLER sqlite-devel
	$PACKAGE_INSTALLER zlib-devel
	$PACKAGE_INSTALLER smtpdaemon
	$PACKAGE_INSTALLER libedit-devel
	$PACKAGE_INSTALLER pcre-devel
	$PACKAGE_INSTALLER pcre2-devel
	$PACKAGE_INSTALLER pcre3-devel
	$PACKAGE_INSTALLER libxcrypt-devel
	$PACKAGE_INSTALLER xcrypt-devel
	$PACKAGE_INSTALLER perl-interpreter
	$PACKAGE_INSTALLER autoconf
	$PACKAGE_INSTALLER automake
	$PACKAGE_INSTALLER make
	$PACKAGE_INSTALLER gcc
	$PACKAGE_INSTALLER gcc-c++
	$PACKAGE_INSTALLER libtool
	$PACKAGE_INSTALLER libtool-ltdl-devel
	$PACKAGE_INSTALLER systemtap-sdt-devel
	$PACKAGE_INSTALLER tzdata
	$PACKAGE_INSTALLER procps
	$PACKAGE_INSTALLER procps-ng
	$PACKAGE_INSTALLER libacl-devel
	$PACKAGE_INSTALLER systemd-devel
	$PACKAGE_INSTALLER krb5-devel
	$PACKAGE_INSTALLER libc-client-devel
	$PACKAGE_INSTALLER cyrus-sasl-devel
	$PACKAGE_INSTALLER openldap-devel
	$PACKAGE_INSTALLER libpq-devel
	$PACKAGE_INSTALLER unixODBC-devel
	$PACKAGE_INSTALLER firebird-devel
	$PACKAGE_INSTALLER net-snmp-devel
	$PACKAGE_INSTALLER oniguruma-devel
	$PACKAGE_INSTALLER gd-devel
	$PACKAGE_INSTALLER gmp-devel
	$PACKAGE_INSTALLER libdb-devel
	$PACKAGE_INSTALLER tokyocabinet-devel
	$PACKAGE_INSTALLER lmdb-devel
	$PACKAGE_INSTALLER qdbm-devel
	$PACKAGE_INSTALLER libtidy-devel
	$PACKAGE_INSTALLER freetds-devel
	$PACKAGE_INSTALLER aspell-devel
	$PACKAGE_INSTALLER libicu-devel
	$PACKAGE_INSTALLER enchant-devel
	$PACKAGE_INSTALLER libenchant-devel
	$PACKAGE_INSTALLER libsodium-devel
	$PACKAGE_INSTALLER sodium-devel
	$PACKAGE_INSTALLER libffi-devel
	$PACKAGE_INSTALLER ffi-devel
	$PACKAGE_INSTALLER libxslt-devel
	$PACKAGE_INSTALLER xslt-devel
	$PACKAGE_INSTALLER yasm
	$PACKAGE_INSTALLER nasm
	$PACKAGE_INSTALLER gnutls-devel
	$PACKAGE_INSTALLER libass-devel
	$PACKAGE_INSTALLER ass-devel
	$PACKAGE_REMOVER fdk-aac-free-devel
	$PACKAGE_INSTALLER fdk-aac-devel
	$PACKAGE_INSTALLER lame-devel
	$PACKAGE_INSTALLER opus-devel
	$PACKAGE_INSTALLER libopus-devel
	$PACKAGE_INSTALLER librtmp-devel
	$PACKAGE_INSTALLER librtmp
	$PACKAGE_INSTALLER rtmp-devel
	$PACKAGE_INSTALLER rtmp
	$PACKAGE_INSTALLER rtmpdump
	$PACKAGE_INSTALLER alsa-lib-devel
	$PACKAGE_INSTALLER AMF-devel
	$PACKAGE_INSTALLER faac-devel
	$PACKAGE_INSTALLER flite-devel
	$PACKAGE_INSTALLER fontconfig-devel
	$PACKAGE_INSTALLER freetype-devel
	$PACKAGE_INSTALLER fribidi-devel
	$PACKAGE_INSTALLER frei0r-devel
	$PACKAGE_INSTALLER game-music-emu-devel
	$PACKAGE_INSTALLER gsm-devel
	$PACKAGE_INSTALLER ilbc-devel
	$PACKAGE_INSTALLER jack-audio-connection-kit-devel
	$PACKAGE_INSTALLER ladspa-devel
	$PACKAGE_INSTALLER libaom-devel
	$PACKAGE_INSTALLER libdav1d-devel
	$PACKAGE_INSTALLER libass-devel
	$PACKAGE_INSTALLER libbluray-devel
	$PACKAGE_INSTALLER libbs2b-devel
	$PACKAGE_INSTALLER libcaca-devel
	$PACKAGE_INSTALLER libcdio-paranoia-devel
	$PACKAGE_INSTALLER libchromaprint-devel
	$PACKAGE_INSTALLER libcrystalhd-devel
	$PACKAGE_INSTALLER lensfun-devel
	$PACKAGE_INSTALLER libavc1394-devel
	$PACKAGE_INSTALLER libdc1394-devel
	$PACKAGE_INSTALLER libiec61883-devel
	$PACKAGE_INSTALLER libdrm-devel
	$PACKAGE_INSTALLER libgcrypt-devel
	$PACKAGE_INSTALLER libGL-devel
	$PACKAGE_INSTALLER libmodplug-devel
	$PACKAGE_INSTALLER libmysofa-devel
	$PACKAGE_INSTALLER libopenmpt-devel
	$PACKAGE_INSTALLER librsvg2-devel
	$PACKAGE_INSTALLER libsmbclient-devel
	$PACKAGE_INSTALLER libssh-devel
	$PACKAGE_INSTALLER libtheora-devel
	$PACKAGE_INSTALLER libv4l-devel
	$PACKAGE_INSTALLER libva-devel
	$PACKAGE_INSTALLER libvdpau-devel
	$PACKAGE_INSTALLER libvorbis-devel
	$PACKAGE_INSTALLER vapoursynth-devel
	$PACKAGE_INSTALLER libvpx-devel
	$PACKAGE_INSTALLER libmfx
	$PACKAGE_INSTALLER mfx
	$PACKAGE_INSTALLER libmfx-devel
	$PACKAGE_INSTALLER mfx-devel
	$PACKAGE_INSTALLER nasm
	$PACKAGE_INSTALLER libwebp-devel
	$PACKAGE_INSTALLER netcdf-devel
	$PACKAGE_INSTALLER raspberrypi-vc-devel
	$PACKAGE_INSTALLER nv-codec-headers
	$PACKAGE_INSTALLER opencore-amr-devel vo-amrwbenc-devel
	$PACKAGE_INSTALLER libomxil-bellagio-devel
	$PACKAGE_INSTALLER libxcb-devel
	$PACKAGE_INSTALLER libxml2-devel
	$PACKAGE_INSTALLER lilv-devel lv2-devel
	$PACKAGE_INSTALLER openal-soft-devel
	$PACKAGE_INSTALLER opencl-headers ocl-icd-devel
	$PACKAGE_INSTALLER openjpeg2-devel
	$PACKAGE_INSTALLER pulseaudio-libs-devel
	$PACKAGE_INSTALLER podman
	$PACKAGE_INSTALLER rav1e-devel
	$PACKAGE_INSTALLER rubberband-devel
	$PACKAGE_INSTALLER SDL2-devel
	$PACKAGE_INSTALLER snappy-devel
	$PACKAGE_INSTALLER soxr-devel
	$PACKAGE_INSTALLER speex-devel
	$PACKAGE_INSTALLER srt-devel
	$PACKAGE_INSTALLER srt-libs
	$PACKAGE_INSTALLER srt-lib
	$PACKAGE_INSTALLER srt
	$PACKAGE_INSTALLER svt-av1-devel
	$PACKAGE_INSTALLER tesseract-devel
	$PACKAGE_INSTALLER texi2html
	$PACKAGE_INSTALLER texinfo
	$PACKAGE_INSTALLER twolame-devel
	$PACKAGE_INSTALLER libvmaf-devel
	$PACKAGE_INSTALLER wavpack-devel
	$PACKAGE_INSTALLER vid.stab-devel
	$PACKAGE_INSTALLER vulkan-loader-devel
	$PACKAGE_INSTALLER libshaderc-devel
	$PACKAGE_INSTALLER libshaderc
	$PACKAGE_INSTALLER spirv-tools-libs
	$PACKAGE_INSTALLER x264-devel
	$PACKAGE_INSTALLER x264-libs
	$PACKAGE_INSTALLER x264-lib
	$PACKAGE_INSTALLER libx264-devel
	$PACKAGE_INSTALLER x264
	$PACKAGE_INSTALLER x265-devel
	$PACKAGE_INSTALLER x265-libs
	$PACKAGE_INSTALLER x265-lib
	$PACKAGE_INSTALLER libx265-devel
	$PACKAGE_INSTALLER x265
	$PACKAGE_INSTALLER xvidcore-devel
	$PACKAGE_INSTALLER libxvidcore-devel
	$PACKAGE_INSTALLER xvid-devel
	$PACKAGE_INSTALLER libxvid-devel
	$PACKAGE_INSTALLER xvidcore
	$PACKAGE_INSTALLER xvid
	$PACKAGE_INSTALLER zimg-devel
	$PACKAGE_INSTALLER zlib-devel
	$PACKAGE_INSTALLER zeromq-devel
	$PACKAGE_INSTALLER zvbi-devel
	$PACKAGE_INSTALLER vmaf-models
	$PACKAGE_INSTALLER pkgconfig
	$PACKAGE_INSTALLER libunistring-devel
	$PACKAGE_INSTALLER unistring-devel
	$PACKAGE_INSTALLER libunistring
	$PACKAGE_INSTALLER unistring
	$PACKAGE_INSTALLER libxslt-devel
	$PACKAGE_INSTALLER GeoIP-devel
	$PACKAGE_INSTALLER tar
	$PACKAGE_INSTALLER unzip
	$PACKAGE_INSTALLER curl
	$PACKAGE_INSTALLER wget
	$PACKAGE_INSTALLER git
 	$PACKAGE_INSTALLER re2c
  	$PACKAGE_INSTALLER perl
	$PACKAGE_INSTALLER libmaxminddb-devel
	$PACKAGE_INSTALLER libmcrypt-dev
	$PACKAGE_INSTALLER mcrypt-dev
	$PACKAGE_INSTALLER libmcrypt-devel
	$PACKAGE_INSTALLER mcrypt-devel
	$PACKAGE_INSTALLER mcrypt
 	$PACKAGE_INSTALLER libgeoip-dev
  	$PACKAGE_INSTALLER debhelper
  	$PACKAGE_INSTALLER cdbs
  	$PACKAGE_INSTALLER lintian
  	$PACKAGE_INSTALLER fakeroot
  	$PACKAGE_INSTALLER devscripts
  	$PACKAGE_INSTALLER dh-make
  	$PACKAGE_INSTALLER dput
	$PACKAGE_INSTALLER libgeoip-devel
	$PACKAGE_INSTALLER geoip-devel
 	$PACKAGE_INSTALLER epel-rpm-macros
 	$PACKAGE_INSTALLER epel-rpm-macros-systemd
 	$PACKAGE_INSTALLER perl-macros
 	$PACKAGE_INSTALLER perl-srpm-macros
 	$PACKAGE_INSTALLER python-rpm-macros
 	$PACKAGE_INSTALLER python-srpm-macros
 	$PACKAGE_INSTALLER python2-rpm-macros
 	$PACKAGE_INSTALLER python3-other-rpm-macros
 	$PACKAGE_INSTALLER python3-rpm-macros
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
	apt-get install software-properties-common dirmngr --install-recommends -y
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
	apt install curl wget apt-transport-https gnupg2 dirmngr -y
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
deb http://deb.debian.org/debian $(lsb_release -sc)-backports main contrib non-free
deb-src http://deb.debian.org/debian $(lsb_release -sc)-backports main contrib non-free
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
	apt-get -y install build-essential
 	if [[ "$VER" = "18.04" || "$OS" = "debian" ]]; then
  		DEBIAN_FRONTEND=noninteractive apt-get -y install tar
    		DEBIAN_FRONTEND=noninteractive apt-get -y install gzip
  		mkdir -p /home/xtreamcodes/iptv_xtream_codes/phpbuild/
		wget https://fr.archive.ubuntu.com/ubuntu/pool/main/f/freetype/freetype_2.8.1.orig.tar.gz -O /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype_2.8.1.orig.tar.gz
  		tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype_2.8.1.orig.tar.gz -C /home/xtreamcodes/iptv_xtream_codes/phpbuild/
      		tar -xvf /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype-2.8.1/freetype-2.8.1.tar.bz2 -C /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype-2.8.1/
  		cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype-2.8.1/freetype-2.8.1/ && ./configure --prefix=/usr --without-bzip2 --without-harfbuzz --enable-freetype-config > /dev/null
    		cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype-2.8.1/freetype-2.8.1/ && make -j$(nproc --all) > /dev/null
        	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype-2.8.1/freetype-2.8.1/ && make install > /dev/null
   	else
 		DEBIAN_FRONTEND=noninteractive apt-get -y build-dep libfreetype-dev
  		mkdir -p /home/xtreamcodes/iptv_xtream_codes/phpbuild/
  		cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/
   		apt-get source libfreetype-dev
   		cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype*/ && ./configure --prefix=/usr --without-bzip2 --without-harfbuzz --enable-freetype-config > /dev/null
    		cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype*/ && make -j$(nproc --all) > /dev/null
        	cd /home/xtreamcodes/iptv_xtream_codes/phpbuild/freetype*/ && make install > /dev/null
	fi
 	cd
	DEBIAN_FRONTEND=noninteractive apt-get -y install apache2-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install daemonize
	DEBIAN_FRONTEND=noninteractive apt-get -y install autoconf
	DEBIAN_FRONTEND=noninteractive apt-get -y install automake
	DEBIAN_FRONTEND=noninteractive apt-get -y install bison
	DEBIAN_FRONTEND=noninteractive apt-get -y install chrpath
	DEBIAN_FRONTEND=noninteractive apt-get -y install debhelper 
	DEBIAN_FRONTEND=noninteractive apt-get -y install cdbs
	DEBIAN_FRONTEND=noninteractive apt-get -y install lintian
	DEBIAN_FRONTEND=noninteractive apt-get -y install build-essential
	DEBIAN_FRONTEND=noninteractive apt-get -y install fakeroot
	DEBIAN_FRONTEND=noninteractive apt-get -y install devscripts
	DEBIAN_FRONTEND=noninteractive apt-get -y install dh-make
	DEBIAN_FRONTEND=noninteractive apt-get -y install mariadb-server
	DEBIAN_FRONTEND=noninteractive apt-get -y install curl
	DEBIAN_FRONTEND=noninteractive apt-get -y install libxslt1-dev
	DEBIAN_FRONTEND=noninteractive apt-get -y install libcurl3-gnutls
	DEBIAN_FRONTEND=noninteractive apt-get -y install libgeoip-dev
	DEBIAN_FRONTEND=noninteractive apt-get -y install python
	DEBIAN_FRONTEND=noninteractive apt-get -y install python2
	DEBIAN_FRONTEND=noninteractive apt-get -y install python3
	DEBIAN_FRONTEND=noninteractive apt-get -y install e2fsprogs
	DEBIAN_FRONTEND=noninteractive apt-get -y install wget
	DEBIAN_FRONTEND=noninteractive apt-get -y install mcrypt
	DEBIAN_FRONTEND=noninteractive apt-get -y install nscd
	DEBIAN_FRONTEND=noninteractive apt-get -y install htop
	DEBIAN_FRONTEND=noninteractive apt-get -y install zip
	DEBIAN_FRONTEND=noninteractive apt-get -y install unzip
	DEBIAN_FRONTEND=noninteractive apt-get -y install mc
	DEBIAN_FRONTEND=noninteractive apt-get -y install python3-paramiko
	DEBIAN_FRONTEND=noninteractive apt-get -y install python-paramiko
	DEBIAN_FRONTEND=noninteractive apt-get -y install python2-paramiko
	DEBIAN_FRONTEND=noninteractive apt-get -y install python-pip
	DEBIAN_FRONTEND=noninteractive apt-get -y install python2-pip
	DEBIAN_FRONTEND=noninteractive apt-get -y install python3-pip
	echo "postfix postfix/mailname string postfixmessage" | debconf-set-selections
	echo "postfix postfix/main_mailer_type string 'Local only'" | debconf-set-selections
	DEBIAN_FRONTEND=noninteractive apt-get -y install postfix
	DEBIAN_FRONTEND=noninteractive apt-get -y dist-upgrade
	DEBIAN_FRONTEND=noninteractive apt-get -y install debhelper
 	DEBIAN_FRONTEND=noninteractive apt-get -y install cdbs
 	DEBIAN_FRONTEND=noninteractive apt-get -y install lintian
 	DEBIAN_FRONTEND=noninteractive apt-get -y install build-essential
 	DEBIAN_FRONTEND=noninteractive apt-get -y install fakeroot
 	DEBIAN_FRONTEND=noninteractive apt-get -y install devscripts
 	DEBIAN_FRONTEND=noninteractive apt-get -y install dh-make
 	DEBIAN_FRONTEND=noninteractive apt-get -y install wget
 	DEBIAN_FRONTEND=noninteractive apt-get -y install default-libmysqlclient-dev
  	DEBIAN_FRONTEND=noninteractive apt-get -y install libmysqlclient-dev
	DEBIAN_FRONTEND=noninteractive apt-get -y build-dep php7.4
	DEBIAN_FRONTEND=noninteractive apt-get -y build-dep php7.2
	DEBIAN_FRONTEND=noninteractive apt-get -y install libmariadb-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libmariadb-dev-compat
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libmariadbd-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install dbconfig-mysql
	DEBIAN_FRONTEND=noninteractive apt-get -y install autoconf
 	DEBIAN_FRONTEND=noninteractive apt-get -y install automake
 	DEBIAN_FRONTEND=noninteractive apt-get -y install cmake
 	DEBIAN_FRONTEND=noninteractive apt-get -y install git-core
 	DEBIAN_FRONTEND=noninteractive apt-get -y install git
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libass-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libfreetype6-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libfreetype-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libgnutls28-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libmp3lame-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libsdl2-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libtool
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libva-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libvdpau-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libvorbis-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libxcb1-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libxcb-shm0-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libxcb-xfixes0-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install meson
 	DEBIAN_FRONTEND=noninteractive apt-get -y install ninja-build
 	DEBIAN_FRONTEND=noninteractive apt-get -y install pkg-config
 	DEBIAN_FRONTEND=noninteractive apt-get -y install texinfo
 	DEBIAN_FRONTEND=noninteractive apt-get -y install yasm
 	DEBIAN_FRONTEND=noninteractive apt-get -y install zlib1g-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libxvidcore-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libunistring-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install nasm
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libx264-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libx265-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libnuma-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libvpx-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libfdk-aac-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libopus-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install unzip
 	DEBIAN_FRONTEND=noninteractive apt-get -y install librtmp-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libtheora-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libbz2-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libgmp-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libssl-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install unzip
 	DEBIAN_FRONTEND=noninteractive apt-get -y install zip
	DEBIAN_FRONTEND=noninteractive apt-get -y install libdav1d-dev
	DEBIAN_FRONTEND=noninteractive apt-get -y install libaom-dev
	DEBIAN_FRONTEND=noninteractive apt-get -y install reprepro
	DEBIAN_FRONTEND=noninteractive apt-get -y install subversion
	DEBIAN_FRONTEND=noninteractive apt-get -y install zstd
	DEBIAN_FRONTEND=noninteractive apt-get -y install zlib1g-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libpcre3
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libpcre3-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libbz2-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libssl-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libgd-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libxslt-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libgeoip-dev
 	DEBIAN_FRONTEND=noninteractive apt-get -y install tar
 	DEBIAN_FRONTEND=noninteractive apt-get -y install curl
 	DEBIAN_FRONTEND=noninteractive apt-get -y install libmaxminddb-dev
	DEBIAN_FRONTEND=noninteractive apt-get -y install libmcrypt-dev
	DEBIAN_FRONTEND=noninteractive apt-get -y install mcrypt-dev
	DEBIAN_FRONTEND=noninteractive apt-get -y install libmcrypt-devel
	DEBIAN_FRONTEND=noninteractive apt-get -y install mcrypt-devel
	DEBIAN_FRONTEND=noninteractive apt-get -y install mcrypt
	DEBIAN_FRONTEND=noninteractive apt-get -y install libgeoip-dev
	DEBIAN_FRONTEND=noninteractive apt-get -y install libgeoip-devel
	DEBIAN_FRONTEND=noninteractive apt-get -y install geoip-devel
 	DEBIAN_FRONTEND=noninteractive apt-get -y install re2c
  	DEBIAN_FRONTEND=noninteractive apt-get -y install perl
   	DEBIAN_FRONTEND=noninteractive apt-get -y install checkinstall
 fi
systemctl start mariadb
systemctl enable mariadb
service mariadb restart
