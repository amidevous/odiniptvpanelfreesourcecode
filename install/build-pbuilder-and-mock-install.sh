#!/bin/bash
mkdir -p $HOME/pbuilder
rm -rf $HOME/pbuilder/*
wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/pbuilder/pbuilder-bionic -O $HOME/pbuilder/pbuilder-config.conf
sudo wget https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install/pbuilder/pbuilder-bionicbin -O /usr/bin/pbuilder-config
ubuntuori () {
   rm -f $HOME/pbuilder/pbuilder-$1.conf
   cp $HOME/pbuilder/pbuilder-config.conf $HOME/pbuilder/pbuilder-$1.conf
   sudo rm -f /usr/bin/pbuilder-$1
   sudo cp /usr/bin/pbuilder-config /usr/bin/pbuilder-$1
   sudo chmod +x /usr/bin/pbuilder-$1
   mkdir -p $HOME/pbuilder/$1
   mkdir -p $HOME/pbuilder/$1/aptcache/
   mkdir -p $HOME/pbuilder/$1/result/
   mkdir -p $HOME/pbuilder/$1/build/
   mkdir -p $HOME/pbuilder/$1/nonexistent/
   mkdir -p $HOME/pbuilder/$1/hooks/
   sed -i 's|bionic|'$1'|g' $HOME/pbuilder/pbuilder-$1.conf
   echo 'MIRRORSITE=http://archive.ubuntu.com/ubuntu' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'COMPONENTS="main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="deb-src http://archive.ubuntu.com/ubuntu/ '$1' main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb http://security.ubuntu.com/ubuntu '$1'-security main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src http://security.ubuntu.com/ubuntu '$1'-security main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src http://archive.ubuntu.com/ubuntu/ '$1'-updates main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb http://archive.ubuntu.com/ubuntu/ '$1'-updates main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src http://security.ubuntu.com/ubuntu '$1'-security main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb http://archive.canonical.com/ubuntu '$1' partner"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src http://archive.canonical.com/ubuntu '$1' partner"' >> $HOME/pbuilder/pbuilder-$1.conf
   pbuilder-$1 create --override-config
}
debianori () {
   rm -f $HOME/pbuilder/pbuilder-$1.conf
   cp $HOME/pbuilder/pbuilder-config.conf $HOME/pbuilder/pbuilder-$1.conf
   sudo rm -f /usr/bin/pbuilder-$1
   sudo cp /usr/bin/pbuilder-config /usr/bin/pbuilder-$1
   sudo chmod +x /usr/bin/pbuilder-$1
   mkdir -p $HOME/pbuilder/$1
   mkdir -p $HOME/pbuilder/$1/aptcache/
   mkdir -p $HOME/pbuilder/$1/result/
   mkdir -p $HOME/pbuilder/$1/build/
   mkdir -p $HOME/pbuilder/$1/nonexistent/
   mkdir -p $HOME/pbuilder/$1/hooks/
   sed -i 's|bionic|'$1'|g' $HOME/pbuilder/pbuilder-$1.conf
   echo 'MIRRORSITE=http://deb.debian.org/debian/' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'COMPONENTS="main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="deb http://deb.debian.org/debian '$1' main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src http://deb.debian.org/debian '$1' main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb http://deb.debian.org/debian-security/ '$1'-security main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src http://deb.debian.org/debian-security/ '$1'-security main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb http://deb.debian.org/debian '$1'-updates main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src http://deb.debian.org/debian '$1'-updates main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb http://deb.debian.org/debian '$1'--backports/ main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src http://deb.debian.org/debian '$1'--backports/ main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   pbuilder-$1 create --override-config
}
debianarc () {
   rm -f $HOME/pbuilder/pbuilder-$1.conf
   cp $HOME/pbuilder/pbuilder-config.conf $HOME/pbuilder/pbuilder-$1.conf
   sudo rm -f /usr/bin/pbuilder-$1
   sudo cp /usr/bin/pbuilder-config /usr/bin/pbuilder-$1
   sudo chmod +x /usr/bin/pbuilder-$1
   mkdir -p $HOME/pbuilder/$1
   mkdir -p $HOME/pbuilder/$1/aptcache/
   mkdir -p $HOME/pbuilder/$1/result/
   mkdir -p $HOME/pbuilder/$1/build/
   mkdir -p $HOME/pbuilder/$1/nonexistent/
   mkdir -p $HOME/pbuilder/$1/hooks/
   sed -i 's|bionic|'$1'|g' $HOME/pbuilder/pbuilder-$1.conf
   echo 'MIRRORSITE=http://archive.debian.org/debian/' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'COMPONENTS="main contrib non-free"' >> $HOME/pbuilder/pbuilder-$1.conf
   pbuilder-stretch create --override-config
}
echo "ok"
ubuntuori bionic
ubuntuori focal
ubuntuori jammy
ubuntuori trusty





debianori bookworm
debianori bullseye
debianori buster
debianarc stretch
debianarc jessie
debianarc wheezy
debianarc squeeze
debianarc lenny
debianarc etch
debianarc sarge
debianarc woody
debianarc potato
debianarc slink
debianarc hamm
mkdir -p /root/mock/config/
cp -R /etc/mock/* /root/mock/config/

















