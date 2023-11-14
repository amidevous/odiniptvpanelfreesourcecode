Name:           xtreamcodes-php-mcrypt
Version:        1.0.5
Release:        1
Summary:        xtreamcodes-php-mcrypt.
Group:          Internet
License:        GPL3
URL:            https://pecl.php.net/package/mcrypt
Source0:        https://pecl.php.net/get/mcrypt-1.0.5.tgz
BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
BuildRequires:  gcc make git wget tar gzip xz unzip xtreamcodes-php
Requires:       gcc make git wget tar gzip xz unzip xtreamcodes-php
%description
xtreamcodes-php-mcrypt.
%prep
%setup -q -n mcrypt-1.0.5
%build
/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
./configure --with-php-config=/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
make %{?_smp_mflags}
%install
rm -rf %{buildroot}
mkdir -p %{buildroot}
make install INSTALL_ROOT=%{buildroot}
%clean
rm -rf %{buildroot}
%files
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/mcrypt.so
%defattr(-,root,root,-)
%doc
%changelog
