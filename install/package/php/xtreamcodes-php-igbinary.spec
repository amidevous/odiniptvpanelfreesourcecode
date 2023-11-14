Name:           xtreamcodes-php-igbinary
Version:        3.2.14
Release:        1
Summary:        xtreamcodes-php-igbinary.
Group:          Internet
License:        GPL3
URL:            https://pecl.php.net/package/igbinary
Source0:        https://pecl.php.net/get/igbinary-3.2.14.tgz
BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
BuildRequires:  gcc make git wget tar gzip xz unzip xtreamcodes-php
Requires:       gcc make git wget tar gzip xz unzip xtreamcodes-php
%description
xtreamcodes-php-igbinary.
%prep
%setup -q -n igbinary-3.2.14
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
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/igbinary.so
/home/xtreamcodes/iptv_xtream_codes/php/include/php/ext/igbinary/igbinary.h
/home/xtreamcodes/iptv_xtream_codes/php/include/php/ext/igbinary/php_igbinary.h
/home/xtreamcodes/iptv_xtream_codes/php/include/php/ext/igbinary/src/php7/igbinary.h
/home/xtreamcodes/iptv_xtream_codes/php/include/php/ext/igbinary/src/php7/php_igbinary.h
%defattr(-,root,root,-)
%doc
%changelog
