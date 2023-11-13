Name:           xtreamcodes-nginx-rtmp
Version:        1.24.0
Release:        1
Summary:        xtreamcodes-nginx-rtmp.
Group:          Internet
License:        GPL3
URL:            http://nginx.org
Source0:        http://nginx.org/download/nginx-1.24.0.tar.gz
Patch0:         0003-define_gnu_source-on-other-glibc-based-platforms.patch
Patch1:         nginx-ssl_cert_cb_yield.patch
Patch2:         CVE-2023-44487.patch
Patch3:         ubuntu-branding.patch
BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
BuildRequires:  gcc make git wget tar gzip xz unzip
Requires:       gcc make git wget tar gzip xz unzip
%description
xtreamcodes-nginx-rtmp.
%prep
%setup -q -n nginx-1.24.0
%patch0 -p1
%patch1 -p1
%patch2 -p1
%patch3 -p1
%build
cd %{_builddir}
rm -rf %{_builddir}/OpenSSL_1_1_1h.tar.gz %{_builddir}/openssl-OpenSSL_1_1_1h
wget https://github.com/openssl/openssl/archive/OpenSSL_1_1_1h.tar.gz -O %{_builddir}/OpenSSL_1_1_1h.tar.gz
tar -xzvf OpenSSL_1_1_1h.tar.gz
rm -rf %{_builddir}/OpenSSL_1_1_1h.tar.gz
rm -rf %{_builddir}/ngx_http_geoip2_module
git clone https://github.com/leev/ngx_http_geoip2_module.git %{_builddir}/ngx_http_geoip2_module
rm -rf %{_builddir}/v1.2.2.zip %{_builddir}/nginx-rtmp-module-1.2.2
wget https://github.com/arut/nginx-rtmp-module/archive/v1.2.2.zip -O %{_builddir}/v1.2.2.zip
unzip %{_builddir}/v1.2.2.zip
rm -f %{_builddir}/v1.2.2.zip
cd %{_builddir}/nginx-1.24.0
./configure --prefix=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp \
--sbin-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp \
--lock-path=/home/xtreamcodes/iptv_xtream_codes/tmp/nginx_rtmp.lock \
--http-client-body-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/client_body_temp \
--http-fastcgi-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/fastcgi_temp \
--http-proxy-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/proxy_temp \
--http-scgi-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/scgi_temp \
--http-uwsgi-temp-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/uwsgi_temp \
--conf-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf \
--error-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/rtmp_error.log \
--http-log-path=/home/xtreamcodes/iptv_xtream_codes/logs/rtmp_access.log \
--pid-path=/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp.pid \
--add-module=%{_builddir}//nginx-rtmp-module-1.2.2 \
--with-http_ssl_module \
--with-http_realip_module \
--with-http_addition_module \
--with-http_sub_module \
--with-http_dav_module \
--with-http_gunzip_module \
--with-http_gzip_static_module \
--with-http_v2_module \
--with-pcre \
--with-http_random_index_module \
--with-http_secure_link_module \
--with-http_stub_status_module \
--with-http_auth_request_module \
--with-threads \
--with-mail \
--with-mail_ssl_module \
--with-file-aio \
--with-cpu-opt=generic \
--without-http_rewrite_module \
--add-module=%{_builddir}/ngx_http_geoip2_module \
--with-openssl=%{_builddir}/openssl-OpenSSL_1_1_1h --with-cc-opt='%{build_ldflags}' --with-cc-opt='%{optflags}'
make %{?_smp_mflags}
%install
rm -rf %{buildroot}
mkdir -p %{buildroot}
make install DESTDIR=%{buildroot}
rm -rf %{buildroot}/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/ %{buildroot}/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/html/
%clean
rm -rf %{buildroot}
%files
/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp
%defattr(-,root,root,-)
%doc
%changelog
