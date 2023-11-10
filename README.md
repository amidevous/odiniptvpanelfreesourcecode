# Odin IpTV Panel Free Source Code
Require python 3.10 or + and python requests module
for install python 3.10

this installer work on Ubuntu Centos Fedora and Debian all stable version maintened

ubuntu 22.04 online

```
sudo apt update && sudo apt dist-upgrade -y
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:deadsnakes/ppa -y
sudo apt install python3-dev python3-requests python3-pip -y
```

Other Ubuntu use ppa


```
sudo apt update && sudo apt dist-upgrade -y
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:deadsnakes/ppa -y
sudo apt install python3.10-dev -y
wget https://bootstrap.pypa.io/get-pip.py -O $HOME/get-pip.py
sudo python3.10 $HOME/get-pip.py
sudo sed -i 's|Defaults    secure_path = /sbin:/bin:/usr/sbin:/usr/bin|Defaults    secure_path = /usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin|' /etc/sudoers
sudo pip3.10 install --upgrade pip setuptools wheel
sudo pip3.10 install requests
```


manual build for centos, redhat, fedora


```
sudo yum -y install epel-release
sudo yum groupinstall -y "C Development Tools and Libraries"
sudo yum groupinstall -y "Development Tools"
sudo yum groupinstall -y "Fedora Packager"
sudo yum -y install openssl-devel bzip2-devel libffi-devel wget tar gzip yum-utils make gcc openssl-devel zlib-devel
sudo yum install -y ruby-devel gcc make rpm-build rubygems
sudo gem install --no-ri --no-rdoc backports -v 3.21.0
sudo gem install --no-ri --no-rdoc fpm -v 0.4.0
sudo yum install -y ncurses-devel sqlite-devel bzip2-devel libnsl2-devel gdbm-devel xz-devel libuuid-devel zlib-devel tk-devel libffi-devel tcl-devel readline-devel
# for el 7 online
sudo yum -y install  openssl11-devel
```

or for ubuntu/debian

```
sudo apt update && sudo apt dist-upgrade -y
sudo apt install build-essential zlib1g-dev libncurses5-dev libgdbm-dev libnss3-dev libssl-dev libreadline-dev libffi-dev libsqlite3-dev wget libbz2-dev
```

ptyhon3.10 build

```
cd
rm -rf Python*
wget https://www.python.org/ftp/python/3.10.13/Python-3.10.13.tgz
tar -xzf Python-3.10.13.tgz
cd Python-3.10.13
# for el 7 online
sed -i 's/PKG_CONFIG openssl /PKG_CONFIG openssl11 /g' configure
sudo ./configure
sudo sed -i 's|Defaults    secure_path = /sbin:/bin:/usr/sbin:/usr/bin|Defaults    secure_path = /usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin|' /etc/sudoers
sudo make -j ${nproc}
sudo make altinstall
cd
rm -rf Python*
sudo pip3.10 install --upgrade pip setuptools wheel
sudo pip3.10 install requests
```


for start installer


```
sudo wget -O /root/install.py3  https://github.com/amidevous/odiniptvpanelfreesourcecode/raw/master/install.py3 && && sudo python3.10 /root/install.py3
```
