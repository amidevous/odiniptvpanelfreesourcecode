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
mkdir -p /source/
rm -rf /source/*
wget https://github.com/pypa/pip/archive/refs/tags/20.3.4.tar.gz -O /source/python2-pip_20.3.4.orig.tar.gz
tar -xvf /source/python2-pip_20.3.4.orig.tar.gz -C /source/
mkdir -p /source/pip-20.3.4/debian/
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/README.Debian" -O "/source/pip-20.3.4/debian/README.Debian"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/README.source" -O "/source/pip-20.3.4/debian/README.source"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/changelog" -O "/source/pip-20.3.4/debian/changelog"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/compat" -O "/source/pip-20.3.4/debian/compat"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/bd3ff4df0aec2f8a4fc483e2b826bcf4bed6ef9c/install/package/python2-pip/debian/control" -O "/source/pip-20.3.4/debian/control"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/copyright" -O "/source/pip-20.3.4/debian/copyright"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/format" -O "/source/pip-20.3.4/debian/format"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/pip-docs.docs" -O "/source/pip-20.3.4/debian/pip-docs.docs"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/rules" -O "/source/pip-20.3.4/debian/rules"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/postinst" -O "/source/pip-20.3.4/debian/postinst"
wget "https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/package/python2-pip/debian/prerm" -O "/source/pip-20.3.4/debian/prerm"
apt-get -y build-dep /source/pip-20.3.4/
cd /source/pip-20.3.4/
debuild
rm -rf /source/pip-20.3.4 "/source/*.tar" "/source/*.tar*" "/source/*.gz" "/source/*.dsc" "/source/*.build" "/source/*.buildinfo" "/source/*.changes" 
tar -cvf "/root/python2-pip-build-"$OS"-"$VER".tar" /source/
