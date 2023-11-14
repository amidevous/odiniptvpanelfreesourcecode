Name:           xtreamcodes-php-geoip
Version:        1.1.1
Release:        1
Summary:        xtreamcodes-php-geoip.
Group:          Internet
License:        GPL3
URL:            https://pecl.php.net/package/geoip
Source0:        https://pecl.php.net/get/geoip-1.1.1.tgz
BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
BuildRequires:  gcc make git wget tar gzip xz unzip xtreamcodes-php
Requires:       gcc make git wget tar gzip xz unzip xtreamcodes-php
%description
xtreamcodes-php-geoip.
%prep
%setup -q -n geoip-1.1.1
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
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/geoip.so
%defattr(-,root,root,-)
%doc
%changelog
