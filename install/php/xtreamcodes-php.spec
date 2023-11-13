Name:           xtreamcodes-php
Version:        7.2.34
Release:        1
Summary:        xtreamcodes-php.
Group:          Internet
License:        GPL3
URL:            http://php.net
Source0:        http://www.php.net/distributions/php-7.2.34.tar.xz
Patch0:         0087-Add-minimal-OpenSSL-3.0-patch.patch
BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
BuildRequires:  gcc make git wget tar gzip xz unzip
Requires:       gcc make git wget tar gzip xz unzip
%description
xtreamcodes-php.
%prep
%setup -q -n php-7.2.34
%patch0 -p1
# ----- Manage known as failed test -------
# affected by systzdata patch
rm ext/date/tests/timezone_location_get.phpt
%if 0%{?fedora} < 28
# need tzdata 2018i
rm ext/date/tests/bug33414-1.phpt
rm ext/date/tests/date_modify-1.phpt
%endif
rm ext/date/tests/bug33415-2.phpt
# too fast builder
rm ext/date/tests/bug73837.phpt
# fails sometime
rm ext/sockets/tests/mcast_ipv?_recv.phpt
# Should be skipped but fails sometime
rm ext/standard/tests/file/file_get_contents_error001.phpt
# cause stack exhausion
rm Zend/tests/bug54268.phpt
rm Zend/tests/bug68412.phpt
# slow and erratic result
rm sapi/cli/tests/upload_2G.phpt
# tar issue
rm ext/zlib/tests/004-mb.phpt
# https://bugs.php.net/63362 - Not needed but installed headers.
# Drop some Windows specific headers to avoid installation,
# before build to ensure they are really not needed.
rm -f TSRM/tsrm_win32.h \
      TSRM/tsrm_config.w32.h \
      Zend/zend_config.w32.h \
      ext/mysqlnd/config-win.h \
      ext/standard/winver.h \
      main/win32_internal_function_disabled.h \
      main/win95nt.h

# Fix some bogus permissions
find . -name \*.[ch] -exec chmod 644 {} \;
chmod 644 README.*
%build
%define _lto_cflags %{nil}
libtoolize --force --copy
cat $(aclocal --print-ac-dir)/{libtool,ltoptions,ltsugar,ltversion,lt~obsolete}.m4 >build/libtool.m4
touch configure.ac
./buildconf --force

CFLAGS="$RPM_OPT_FLAGS -fno-strict-aliasing -Wno-pointer-sign"
export CFLAGS

./configure --prefix=/home/xtreamcodes/iptv_xtream_codes/php \
--with-zlib-dir --with-freetype-dir=/usr --enable-mbstring --enable-calendar \
--with-curl --with-gd --disable-rpath --enable-inline-optimization \
--with-bz2 --with-zlib --enable-sockets --enable-sysvsem --enable-sysvshm \
--enable-pcntl --enable-mbregex --enable-exif --enable-bcmath --with-mhash \
--enable-zip --with-pcre-regex --with-pdo-mysql=mysqlnd \
--with-mysqli=mysqlnd --with-openssl \
--with-fpm-user=xtreamcodes --with-fpm-group=xtreamcodes \
--with-libdir=/lib/x86_64-linux-gnu --with-gettext --with-xmlrpc \
--with-webp-dir=/usr --with-jpeg-dir=/usr \
--with-xsl --enable-opcache --enable-fpm --enable-libxml --enable-static --disable-shared
make %{?_smp_mflags}
%install
rm -rf %{buildroot}
mkdir -p %{buildroot}
make install INSTALL_ROOT=%{buildroot}
rm -rf %{buildroot}/home/xtreamcodes/iptv_xtream_codes/php/etc/
rm -rf %{buildroot}/.channels
rm -rf %{buildroot}/.depdb
rm -rf %{buildroot}/.depdblock
rm -rf %{buildroot}/.filemap
rm -rf %{buildroot}/.lock
%clean
rm -rf %{buildroot}
%files
/home/xtreamcodes/iptv_xtream_codes/php/bin/pear
/home/xtreamcodes/iptv_xtream_codes/php/bin/peardev
/home/xtreamcodes/iptv_xtream_codes/php/bin/pecl
/home/xtreamcodes/iptv_xtream_codes/php/bin/phar
/home/xtreamcodes/iptv_xtream_codes/php/bin/phar.phar
/home/xtreamcodes/iptv_xtream_codes/php/bin/php
/home/xtreamcodes/iptv_xtream_codes/php/bin/php-cgi
/home/xtreamcodes/iptv_xtream_codes/php/bin/php-config
/home/xtreamcodes/iptv_xtream_codes/php/bin/phpdbg
/home/xtreamcodes/iptv_xtream_codes/php/bin/phpize
/home/xtreamcodes/iptv_xtream_codes/php/include/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.channels/.alias/pear.txt
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.channels/.alias/pecl.txt
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.channels/.alias/phpdocs.txt
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.channels/__uri.reg
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.channels/doc.php.net.reg
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.channels/pear.php.net.reg
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.channels/pecl.php.net.reg
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.filemap
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.lock
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.registry/archive_tar.reg
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.registry/console_getopt.reg
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.registry/pear.reg
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.registry/structures_graph.reg
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/.registry/xml_util.reg
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/Archive/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/Console/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/OS/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/PEAR.php
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/PEAR/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/Structures/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/System.php
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/XML/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/build/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/data/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/doc/*
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/opcache.a
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/extensions/no-debug-non-zts-20170718/opcache.so
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/pearcmd.php
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/peclcmd.php
/home/xtreamcodes/iptv_xtream_codes/php/lib/php/*
/home/xtreamcodes/iptv_xtream_codes/php/php/man/*
/home/xtreamcodes/iptv_xtream_codes/php/php/php/fpm/status.html
/home/xtreamcodes/iptv_xtream_codes/php/sbin/php-fpm
%defattr(-,root,root,-)
%doc
%changelog
