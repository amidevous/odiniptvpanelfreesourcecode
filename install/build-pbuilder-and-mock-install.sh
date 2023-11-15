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
   sudo pbuilder-$1 create --override-config
}
ubuntuarc () {
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
   echo 'MIRRORSITE=https://old-releases.ubuntu.com/ubuntu' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'COMPONENTS="main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="deb-src https://old-releases.ubuntu.com/ubuntu/ '$1' main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb https://old-releases.ubuntu.com/ubuntu '$1'-security main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src https://old-releases.ubuntu.com/ubuntu '$1'-security main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src https://old-releases.ubuntu.com/ubuntu/ '$1'-updates main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb https://old-releases.ubuntu.com/ubuntu/ '$1'-updates main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   echo 'OTHERMIRROR="$OTHERMIRROR|deb-src https://old-releases.ubuntu.com/ubuntu '$1'-security main restricted universe multiverse"' >> $HOME/pbuilder/pbuilder-$1.conf
   sudo pbuilder-$1 create --override-config
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
   sudo pbuilder-$1 create --override-config
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
   sudo pbuilder-$1 create --override-config
}
echo "ok"
sudo rm -f /usr/share/debootstrap/scripts/noble
sudo cp /usr/share/debootstrap/scripts/jammy /usr/share/debootstrap/scripts/noble
ubuntuori noble
sudo rm -f /usr/share/debootstrap/scripts/mantic
sudo cp /usr/share/debootstrap/scripts/jammy /usr/share/debootstrap/scripts/mantic
ubuntuori mantic
rm -f /usr/share/debootstrap/scripts/lunar
cp /usr/share/debootstrap/scripts/jammy /usr/share/debootstrap/scripts/lunar
ubuntuori lunar
rm -f /usr/share/debootstrap/scripts/kinetic
cp /usr/share/debootstrap/scripts/jammy /usr/share/debootstrap/scripts/kinetic
ubuntuarc kinetic
ubuntuori jammy
ubuntuarc impish
ubuntuarc hirsute
ubuntuarc groovy
ubuntuori focal
ubuntuarc eoan
ubuntuarc disco
ubuntuarc cosmic
ubuntuori bionic
ubuntuarc artful
ubuntuarc zesty
ubuntuarc yakkety
ubuntuori xenial
ubuntuarc wily
ubuntuarc vivid
ubuntuarc utopic
ubuntuori trusty
ubuntuarc saucy
ubuntuarc raring
ubuntuarc quantal
ubuntuori precise
ubuntuarc oneiric
ubuntuarc natty
ubuntuarc maverick
ubuntuori lucid
ubuntuarc karmic
ubuntuarc jaunty
ubuntuarc intrepid
ubuntuori hardy
ubuntuarc gutsy
ubuntuarc feisty
ubuntuarc edgy
ubuntuori dapper
ubuntuarc breezy
ubuntuarc hoary
ubuntuarc warty







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

















