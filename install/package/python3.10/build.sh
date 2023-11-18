#!/bin/bash
if [ -f /etc/lsb-release ]; then
    OS=$(grep DISTRIB_ID /etc/lsb-release | sed 's/^.*=//')
    VER=$(grep DISTRIB_RELEASE /etc/lsb-release | sed 's/^.*=//')
elif [ -f /etc/os-release ]; then
    OS=$(grep -w ID /etc/os-release | sed 's/^.*=//')
    VER=$(grep VERSION_ID /etc/os-release | sed 's/^.*"\(.*\)"/\1/')
 else
    OS=$(uname -s)
    VER=$(uname -r)
fi
wget https://launchpad.net/~deadsnakes/+archive/ubuntu/ppa/+sourcefiles/python3.10/3.10.13-1+focal1/python3.10_3.10.13.orig.tar.gz -P /source/
tar -xvf /source/python3.10_3.10.13.orig.tar.gz -C /source/
wget https://launchpad.net/~deadsnakes/+archive/ubuntu/ppa/+sourcefiles/python3.10/3.10.13-1+focal1/python3.10_3.10.13-1+focal1.debian.tar.xz -P /source/
tar -xvf /source/python3.10_3.10.13-1+focal1.debian.tar.xz -C /source/Python-3.10.13
wget -O /source/Python-3.10.13/debian/control https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python3.10/debian/control
wget -O /source/Python-3.10.13/debian/changelog https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python3.10/debian/changelog
sed -i 's|focal|'$(lsb_release -sc)'|' /source/Python-3.10.13/debian/changelog
apt-get -y build-dep /source/Python-3.10.13
cd /source/Python-3.10.13
debuild
rm -f "/root/python3.10-build-"$OS"-"$VER".tar"
tar -cvf "/root/python3.10-build-"$OS"-"$VER".tar" /source/
