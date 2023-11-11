##!/bin/bash
# SecTool 1 v3.0 by ODIN v45

clear
echo "######################################################################S###@*::;%####SS#@@####%?*++*?"
echo "###########################################################################S*+:+??%SSSSS#%S%%%****++"
echo "############################################SS############################S##?;;::,+SS###SS%*%*+:+?+"
echo "#######################################*,?##*,:############################S##?+;:,+%%SS#SS#%S?*:*??"
echo "#######################################+.*###;.*###########################SS##%?+:?S####S#SS%**+**?"
echo "#######################################*.*####;;##########################SSSSS##?*S##@####S%%S*+%**"
echo "########################SS#############+.*#############SSS###############SSSSSSS##S##@@@#%++;:;*+;,,"
echo "#####################S+,.,:*S#####*;:::,.,:?#?:?##S:*%:,,:*#############SSSSSSSSS%S#@SSS??*;+;:%S;::"
echo "####################S:.:*+:.;S##S;.,:::,.,:?#*.*##%.:,:+;,.*###########SSSSSSSSSSS%%#*%###SS?***?*+;"
echo "####################+.+###S;.*##+.;S###+.*###*.*##%,.+###?.:S#######SS#SSSSSSSSSS%%%%;%S#@@SS@@?*##S"
echo "###################S,,S####%,,SS,,S####+.*#S#*.*##%.,S#S#S,,S#SSSSSSSSSSSSSSSSS%%%?%*;#%?##%%##;:##%"
echo "#############SSS#S#?.;#SSS#S:.%%.:#SSS#+.*#S#*.*##%.:SSSSS,,SSSSSSSSSSSSSSSSSSS%%%??;;#S*?%%%%#+:+*?"
echo "SSSSSSSSSSSSSSSSSS#?.;#SSSS#:.%?.;#SSS#+.*#S#*.*##%.:SSSSS,,SSSSSSSSSSSSSSSSSS%%%%**;+##S?+;;%S+,;:;"
echo "SSSSSSSSSSSSSSSSSS#?.;#SSSSS:.%?.;#SSS#+.*#S#*.*#S%.:SSSSS,,SSSSSSSSSSSSSSSSS%%%%?+;;*S###*:+SS+,+;,"
echo "SSSSSSSSSSSSSSSSSS#%,,SSSSS%,,S%.,SSSS#;.?SS#*.*#S%.:SSSSS,,SSSSSSSSSSSSSSSSS%%%%?*;+*%S##?*S#S+;;%*"
echo "SSSSSSSSSSSSSSSSSSSS;.+####+.+#S:.*#S#%,,SSS#*.*#S%.:SSSSS,,SSSSSSSSSSSSSSSSS%%?%%*++??%SS%??#@SS?;?"
echo "SSSSSSSSSSSSSSSSSSSS%,.;??;.:SSS?,.+?*,.*SSS#*.*SS%.:SSSSS,,SSSSSSSSSSSSSSS%%%%S#%?++++*S%+:;*%#%;:,"
echo "SSSSSSSSSSSSSSSSSSSSS%;...,;%SSSS?:...,*SSSSS*,*SS%,;SSSSS,,%SSSSSSSSSSSS%%*%##S%S*;;+*??;::++*%?;::"
echo "SSSSSSSSSSSSSSSSSSSSSSS%??%SSSSSSSS%?%SSSSSSSS%SSSSSSSSSSS,,%SSSSSSSSSS%***%#####%*;+???+::+*?%S#%*+"
echo "SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS,,SSSSSSSSSSS%?%SSSSS##%??%?*+;;*%##S%%SS%"
echo "SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS,.+*SSSSSSSSSS#@@#SSS#S%%%?++++*%#%?*+**?S"
echo "SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS:..,%SSSSSSSSS@@@@@####SSS%*++*?%?+++++++*"
echo "SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS%%%%SSSSSSSSSSS####@@@###S?+*++?*;+++*;;;+"
echo "SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS#S###@@@@@#S?*;;*+;;+++*+::*"
echo "SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS#####S#@@@@#S%**S?;;;;+***+:;"
sleep 3

echo " "
echo "#############################################################"
echo " Wait for the installation to complete! "
echo " No messages will be displayed until it is finished. "
echo " To follow installation, open another terminal and use the command:"
echo " cat /root/SecTool1.log "
echo "#############################################################"
echo " "
sleep 3

exec > /root/SecTool1.log 2>&1

nginx_conf="#INCLUDE-NGINX-CONF#"
jail_local="#INCLUDE-JAIL-CONF#"
geoipupdate="#INCLUDE-GEOIP-CONF#"
country_whitelist="#INCLUDE-COUNTRY-WHITELIST-CONF#"
ip_whitelist_limit_req="#INCLUDE-IP-WHITELIST-LIMIT-REQ-CONF#"
nginx_req_limit="IyBGYWlsMkJhbiBjb25maWd1cmF0aW9uIGZpbGUKIwojIHN1cHBvcnRzOiBuZ3hfaHR0cF9saW1pdF9yZXFfbW9kdWxlIG1vZHVsZQoKW0RlZmluaXRpb25dCgpmYWlscmVnZXggPSBsaW1pdGluZyByZXF1ZXN0cywgZXhjZXNzOi4qIGJ5IHpvbmUuKmNsaWVudDogPEhPU1Q+CgojIE9wdGlvbjogaWdub3JlcmVnZXgKIyBOb3Rlcy46IHJlZ2V4IHRvIGlnbm9yZS4gSWYgdGhpcyByZWdleCBtYXRjaGVzLCB0aGUgbGluZSBpcyBpZ25vcmVkLgojIFZhbHVlczogVEVYVAojCmlnbm9yZXJlZ2V4ID0K"
nginx_service="W1VuaXRdCkRlc2NyaXB0aW9uPVRoZSBOR0lOWCBIVFRQIGFuZCByZXZlcnNlIHByb3h5IHNlcnZlcgpBZnRlcj1zeXNsb2cudGFyZ2V0IG5ldHdvcmsudGFyZ2V0IHJlbW90ZS1mcy50YXJnZXQgbnNzLWxvb2t1cC50YXJnZXQKIApbU2VydmljZV0KVHlwZT1mb3JraW5nClBJREZpbGU9L3J1bi9uZ2lueC5waWQKRXhlY1N0YXJ0UHJlPS91c3Ivc2Jpbi9uZ2lueCAtdApFeGVjU3RhcnQ9L3Vzci9zYmluL25naW54CkV4ZWNSZWxvYWQ9L3Vzci9zYmluL25naW54IC1zIHJlbG9hZApFeGVjU3RvcD0vYmluL2tpbGwgLXMgUVVJVCAKUHJpdmF0ZVRtcD10cnVlCiAKW0luc3RhbGxdCldhbnRlZEJ5PW11bHRpLXVzZXIudGFyZ2V0Cgo="
user_agent="IyBCbG9jayB1c2VyIGFnZW50IGRlZmF1bHQgYWxsb3cgYWxsCiN+Km1hbGljaW91cyAgICAgMTsKI34qYmFja2Rvb3IgICAgICAxOwojfipuZXRjcmF3bGVyICAgIDE7CiN+KndlYmJhbmRpdCAgICAgMTsKI34qd2dldCAgICAgICAgICAxOwojfipsaWJ3d3ctcGVybCAgIDE7CiN+KmNVUkwgICAgICAgICAgMTsKI34qQkJCaWtlICAgICAgICAxOwojfipqYXZhICAgICAgICAgIDE7CiN+KnNwaWRlciAgICAgICAgMTsKI34qYm90ICAgICAgICAgICAxOwo="
block_asn="IyBCbGFja2xpc3QgQVNOCiMgZGVmYXVsdCAwIGFsbG93IGFsbAojCiMxNTE2OSAgICAgMTsgIyBHb29nbGUgQVNOIG51bWJlciAKIzgwNzUgICAgICAxOyAjIE1pY3Jvc29mdCBBU04gbnVtYmVyCg=="
sysctl_conf="IyBBdm9pZCBhIHNtdXJmIGF0dGFjawpuZXQuaXB2NC5pY21wX2VjaG9faWdub3JlX2Jyb2FkY2FzdHMgPSAxCgojIFR1cm4gb24gcHJvdGVjdGlvbiBmb3IgYmFkIGljbXAgZXJyb3IgbWVzc2FnZXMKbmV0LmlwdjQuaWNtcF9pZ25vcmVfYm9ndXNfZXJyb3JfcmVzcG9uc2VzID0gMQoKIyBUdXJuIG9uIHN5bmNvb2tpZXMgZm9yIFNZTiBmbG9vZCBhdHRhY2sgcHJvdGVjdGlvbgpuZXQuaXB2NC50Y3Bfc3luY29va2llcyA9IDEKCiMgVHVybiBvbiBhbmQgbG9nIHNwb29mZWQsIHNvdXJjZSByb3V0ZWQsIGFuZCByZWRpcmVjdCBwYWNrZXRzCm5ldC5pcHY0LmNvbmYuYWxsLmxvZ19tYXJ0aWFucyA9IDEKbmV0LmlwdjQuY29uZi5kZWZhdWx0LmxvZ19tYXJ0aWFucyA9IDEKCiMgTm8gc291cmNlIHJvdXRlZCBwYWNrZXRzIGhlcmUKbmV0LmlwdjQuY29uZi5hbGwuYWNjZXB0X3NvdXJjZV9yb3V0ZSA9IDAKbmV0LmlwdjQuY29uZi5kZWZhdWx0LmFjY2VwdF9zb3VyY2Vfcm91dGUgPSAwCgojIFR1cm4gb24gcmV2ZXJzZSBwYXRoIGZpbHRlcmluZwpuZXQuaXB2NC5jb25mLmFsbC5ycF9maWx0ZXIgPSAxCm5ldC5pcHY0LmNvbmYuZGVmYXVsdC5ycF9maWx0ZXIgPSAxCgojIE1ha2Ugc3VyZSBubyBvbmUgY2FuIGFsdGVyIHRoZSByb3V0aW5nIHRhYmxlcwpuZXQuaXB2NC5jb25mLmFsbC5hY2NlcHRfcmVkaXJlY3RzID0gMApuZXQuaXB2NC5jb25mLmRlZmF1bHQuYWNjZXB0X3JlZGlyZWN0cyA9IDAKbmV0LmlwdjQuY29uZi5hbGwuc2VjdXJlX3JlZGlyZWN0cyA9IDAKbmV0LmlwdjQuY29uZi5kZWZhdWx0LnNlY3VyZV9yZWRpcmVjdHMgPSAwCgojIERvbid0IGFjdCBhcyBhIHJvdXRlcgpuZXQuaXB2NC5pcF9mb3J3YXJkID0gMApuZXQuaXB2NC5jb25mLmFsbC5zZW5kX3JlZGlyZWN0cyA9IDAKbmV0LmlwdjQuY29uZi5kZWZhdWx0LnNlbmRfcmVkaXJlY3RzID0gMAoKCiMgVHVybiBvbiBleGVjc2hpbGQKa2VybmVsLmV4ZWMtc2hpZWxkID0gMQprZXJuZWwucmFuZG9taXplX3ZhX3NwYWNlID0gMQoKIyBPcHRpbWl6YXRpb24gZm9yIHBvcnQgdXNlZm9yIExCcwojIEluY3JlYXNlIHN5c3RlbSBmaWxlIGRlc2NyaXB0b3IgbGltaXQKZnMuZmlsZS1tYXggPSA2NTUzNQoKIyBBbGxvdyBmb3IgbW9yZSBQSURzICh0byByZWR1Y2Ugcm9sbG92ZXIgcHJvYmxlbXMpOyBtYXkgYnJlYWsgc29tZSBwcm9ncmFtcyAzMjc2OAprZXJuZWwucGlkX21heCA9IDY1NTM2CgojIEluY3JlYXNlIHN5c3RlbSBJUCBwb3J0IGxpbWl0cwpuZXQuaXB2NC5pcF9sb2NhbF9wb3J0X3JhbmdlID0gMjAwMCA2NTAwMAoKIyBJbmNyZWFzZSBUQ1AgbWF4IGJ1ZmZlciBzaXplIHNldGFibGUgdXNpbmcgc2V0c29ja29wdCgpCm5ldC5pcHY0LnRjcF9ybWVtID0gNDA5NiA4NzM4MCA4Mzg4NjA4Cm5ldC5pcHY0LnRjcF93bWVtID0gNDA5NiA4NzM4MCA4Mzg4NjA4CgojIEluY3JlYXNlIExpbnV4IGF1dG8gdHVuaW5nIFRDUCBidWZmZXIgbGltaXRzCiMgbWluLCBkZWZhdWx0LCBhbmQgbWF4IG51bWJlciBvZiBieXRlcyB0byB1c2UKIyBzZXQgbWF4IHRvIGF0IGxlYXN0IDRNQiwgb3IgaGlnaGVyIGlmIHlvdSB1c2UgdmVyeSBoaWdoIEJEUCBwYXRocwojIFRjcCBXaW5kb3dzIGV0YwpuZXQuY29yZS5ybWVtX21heCA9IDgzODg2MDgKbmV0LmNvcmUud21lbV9tYXggPSA4Mzg4NjA4Cm5ldC5jb3JlLm5ldGRldl9tYXhfYmFja2xvZyA9IDUwMDAKbmV0LmlwdjQudGNwX3dpbmRvd19zY2FsaW5nID0gMQo="
protect_rules="bG9jYXRpb24gfiogIihcJ3xcIikoLiopKGRyb3B8aW5zZXJ0fG1kNXxzZWxlY3R8dW5pb24pIiB7IGRlbnkgYWxsOyB9CmxvY2F0aW9uIH4qICIoPHwlM0MpLipzY3JpcHQuKig+fCUzKSIgeyBkZW55IGFsbDsgfQo="
isp_block="IyBJU1AgQmxvY2sKIyBFeGFtcGxlIHRvIGJsb2NrIGV4YWN0IG1hdGNoIDoKIyJMYXNld2ViIERldXRzY2hsYW5kIEdtYkgiCTE7CiMgRXhhbXBsZSB0byBibG9jayBub24gZXhhY3QgOiAKIyJ+Kkxhc2V3ZWIiCTE7Cg=="
fail2ban_4xx_conf="W0RlZmluaXRpb25dCmZhaWxyZWdleCA9IF48SE9TVD4uKiIoR0VUfFBPU1QpLioiICg0MDR8NDQ0fDQwM3w0MDApIC4qJAppZ25vcmVyZWdleCA9Cg=="
exclusion_ip="IyBleGNsdWRlIGlwIC8gcmFuZ2UgZnJvbSBjb3VudHJ5IGJsb2NrCiMgZXMuOgojIDEwLjEwLjAuMC8yMSAxOyAKIyAxMC4xMC4xLjEvMzIgMTsK"

fail2ban_mysql_user=#SET-FAIL2BAN-MUSER#
fail2ban_mysql_pass=#SET-FAIL2BAN-MPASS#


echo " "
echo "##################################################"
echo " Welcome Advanced SecTool 1 for ODIN "
echo "##################################################"
echo " "
sleep 3

echo " "
echo "##################################################"
echo " Updating System "
echo "##################################################"
echo " "
sleep 3

apt-get update -y
apt-get upgrade -y
apt-get install -y build-essential
apt-get install -y software-properties-common
apt-get install -y libpcre3-dev
apt-get install -y zlib1g-dev
apt-get install -y libssl-dev
apt-get install -y libxslt1-dev 
apt-get install -y libpcre3 
apt-get install -y libpcre3-dev 
apt-get install -y libssl-dev
apt-get install -y fail2ban 
apt-get install -y mariadb-server
apt-get install -y libmaxminddb-dev libmaxminddb0 mmdb-bin geoipupdate
apt-get install -y git libtool autoconf apache2-dev libxml2-dev libcurl4-openssl-dev automake pkgconf zlib1g-dev libyajl-dev liblua5.1-0-dev

numcpu=`cat /proc/cpuinfo | grep processor | wc -l`

# Compile Mod Security
echo "##################################################"
echo " Compile Mod Security "
echo "##################################################"
sleep 2

cd /usr/local/src
git clone -b nginx_refactoring https://github.com/SpiderLabs/ModSecurity.git
cd ModSecurity
./autogen.sh
./configure --enable-standalone-module --disable-mlogc
make -j $numcpu
make install

# Build Nginx
echo "##################################################"
echo " Build Nginx "
echo "##################################################"
sleep 2
cd /usr/local/src/
wget -q http://nginx.org/download/nginx-1.20.1.tar.gz
#wget -q http://nginx.org/download/nginx-1.9.9.tar.gz
tar xzf nginx-1.9.9.tar.gz 
wget -q  https://github.com/leev/ngx_http_geoip2_module/archive/master.tar.gz
tar zxf master.tar.gz 
#cd nginx-1.9.9
cd nginx-1.20.1
./configure \
--with-cc-opt='-g -O2 -fPIE -fstack-protector-strong -Wformat -Werror=format-security -fPIC -Wdate-time -D_FORTIFY_SOURCE=2' \
--with-ld-opt='-Wl,-Bsymbolic-functions -fPIE -pie -Wl,-z,relro -Wl,-z,now -fPIC' \
--prefix=/usr/share/nginx                 \
--conf-path=/etc/nginx/nginx.conf         \
--http-log-path=/var/log/nginx/access.log \
--error-log-path=/var/log/nginx/error.log \
--lock-path=/var/lock/nginx.lock          \
--pid-path=/run/nginx.pid                 \
--modules-path=/usr/lib/nginx/modules     \
--http-client-body-temp-path=/var/lib/nginx/body \
--http-fastcgi-temp-path=/var/lib/nginx/fastcgi  \
--http-proxy-temp-path=/var/lib/nginx/proxy      \
--http-scgi-temp-path=/var/lib/nginx/scgi        \
--http-uwsgi-temp-path=/var/lib/nginx/uwsgi      \
--with-debug                     \
--with-pcre-jit                  \
--with-http_ssl_module           \
--with-http_stub_status_module   \
--with-http_realip_module        \
--with-http_auth_request_module  \
--with-http_v2_module            \
--with-http_dav_module           \
--with-http_slice_module         \
--with-threads                   \
--with-http_addition_module      \
--with-http_gunzip_module        \
--with-http_gzip_static_module   \
--with-http_sub_module           \
--with-http_xslt_module=dynamic  \
--with-stream=dynamic            \
--with-stream_ssl_module         \
--with-stream_ssl_preread_module \
--with-mail=dynamic              \
--with-mail_ssl_module           \
--add-module=/usr/local/src/ModSecurity/nginx/modsecurity \
--add-dynamic-module=/usr/local/src/ngx_http_geoip2_module-master
sleep 1
make -j $numcpu
make install
cd /usr/sbin
ln -s /usr/share/nginx/sbin/nginx nginx
cd /usr/share/nginx/
ln -s /usr/lib/nginx/modules modules
mkdir -p /var/lib/nginx/body

# Creating nginx.service
echo "##################################################"
echo " Creating nginx.service "
echo "##################################################"
sleep 2
touch /lib/systemd/system/nginx.service
echo "$nginx_service" | base64 -d > /lib/systemd/system/nginx.service


echo "##################################################"
echo " Creating config files for nginx and fail2ban "
echo "##################################################"
sleep 2
# Creating nginx conf
touch /etc/nginx/nginx.conf
echo "$nginx_conf" | base64 -d > /etc/nginx/nginx.conf

# Create default config for ip whitelist limit_req
touch /etc/nginx/ip_whitelist.conf
echo "$ip_whitelist_limit_req" | base64 -d > /etc/nginx/ip_whitelist.conf

# Create country whitelist rules file
touch /etc/nginx/country_whitelist.conf
echo "$country_whitelist" | base64 -d > /etc/nginx/country_whitelist.conf

# Create Block ASN Blacklist
touch /etc/nginx/block_asn.conf
echo "$block_asn" | base64 -d > /etc/nginx/block_asn.conf

# Create user agent rules file
touch /etc/nginx/useragent.rules
echo "$user_agent" | base64 -d > /etc/nginx/useragent.rules

# Create ISP Blacklist file
touch /etc/nginx/block_isp.conf
echo "$isp_block" | base64 -d > /etc/nginx/block_isp.conf

# Create protect.rules file
touch /etc/nginx/protect.rules
echo "$protect_rules" | base64 -d > /etc/nginx/protect.rules 

# Create Exclusion files
touch /etc/nginx/exclusion_ip.conf
echo "$exclusion_ip" | base64 -d > /etc/nginx/exclusion_ip.conf


# MOD SECURITY
touch /etc/nginx/modsec_includes.conf
mkdir -p /etc/nginx/rules
mkdir -p /etc/nginx/conf
mkdir -p /opt/log

echo "include /etc/nginx/conf/modsecurity.conf
include /etc/nginx/conf/owasp-modsecurity-crs/crs-setup.conf
include /etc/nginx/rules/*.conf" > /etc/nginx/modsec_includes.conf

cp /usr/local/src/ModSecurity/modsecurity.conf-recommended /etc/nginx/conf/modsecurity.conf
cp /usr/local/src/ModSecurity/unicode.mapping /etc/nginx/conf/

sed -i "s/SecRuleEngine DetectionOnly/SecRuleEngine On/" /etc/nginx/conf/modsecurity.conf

cd /etc/nginx/conf
git clone https://github.com/SpiderLabs/owasp-modsecurity-crs.git
cd owasp-modsecurity-crs
mv crs-setup.conf.example crs-setup.conf
cd rules
cp REQUEST-905-COMMON-EXCEPTIONS.conf /etc/nginx/rules/REQUEST-905-COMMON-EXCEPTIONS.conf
cp REQUEST-910-IP-REPUTATION.conf /etc/nginx/rules/REQUEST-910-IP-REPUTATION.conf
cp REQUEST-912-DOS-PROTECTION.conf /etc/nginx/rules/REQUEST-912-DOS-PROTECTION.conf
cp REQUEST-913-SCANNER-DETECTION.conf /etc/nginx/rules/REQUEST-913-SCANNER-DETECTION.conf
cp REQUEST-920-PROTOCOL-ENFORCEMENT.conf /etc/nginx/rules/REQUEST-920-PROTOCOL-ENFORCEMENT.conf
cp REQUEST-921-PROTOCOL-ATTACK.conf /etc/nginx/rules/REQUEST-921-PROTOCOL-ATTACK.conf
cp REQUEST-930-APPLICATION-ATTACK-LFI.conf /etc/nginx/rules/REQUEST-930-APPLICATION-ATTACK-LFI.conf
cp REQUEST-931-APPLICATION-ATTACK-RFI.conf /etc/nginx/rules/REQUEST-931-APPLICATION-ATTACK-RFI.conf
cp REQUEST-932-APPLICATION-ATTACK-RCE.conf /etc/nginx/rules/REQUEST-932-APPLICATION-ATTACK-RCE.conf
cp REQUEST-933-APPLICATION-ATTACK-PHP.conf /etc/nginx/rules/REQUEST-933-APPLICATION-ATTACK-PHP.conf
cp REQUEST-934-APPLICATION-ATTACK-NODEJS.conf /etc/nginx/rules/REQUEST-934-APPLICATION-ATTACK-NODEJS.conf
cp REQUEST-941-APPLICATION-ATTACK-XSS.conf /etc/nginx/rules/REQUEST-941-APPLICATION-ATTACK-XSS.conf
cp REQUEST-942-APPLICATION-ATTACK-SQLI.conf /etc/nginx/rules/REQUEST-942-APPLICATION-ATTACK-SQLI.conf
cp REQUEST-943-APPLICATION-ATTACK-SESSION-FIXATION.conf /etc/nginx/rules/REQUEST-943-APPLICATION-ATTACK-SESSION-FIXATION.conf
cp REQUEST-944-APPLICATION-ATTACK-JAVA.conf /etc/nginx/rules/REQUEST-944-APPLICATION-ATTACK-JAVA.conf
cp REQUEST-949-BLOCKING-EVALUATION.conf /etc/nginx/rules/REQUEST-949-BLOCKING-EVALUATION.conf
cp RESPONSE-950-DATA-LEAKAGES.conf /etc/nginx/rules/RESPONSE-950-DATA-LEAKAGES.conf
cp RESPONSE-951-DATA-LEAKAGES-SQL.conf /etc/nginx/rules/RESPONSE-951-DATA-LEAKAGES-SQL.conf
cp RESPONSE-952-DATA-LEAKAGES-JAVA.conf /etc/nginx/rules/RESPONSE-952-DATA-LEAKAGES-JAVA.conf
cp RESPONSE-953-DATA-LEAKAGES-PHP.conf /etc/nginx/rules/RESPONSE-953-DATA-LEAKAGES-PHP.conf
cp RESPONSE-959-BLOCKING-EVALUATION.conf /etc/nginx/rules/RESPONSE-959-BLOCKING-EVALUATION.conf
cp RESPONSE-980-CORRELATION.conf /etc/nginx/rules/RESPONSE-980-CORRELATION.conf
cp crawlers-user-agents.data /etc/nginx/rules/crawlers-user-agents.data
cp java-classes.data /etc/nginx/rules/java-classes.data
cp java-code-leakages.data /etc/nginx/rules/java-code-leakages.data
cp java-errors.data /etc/nginx/rules/java-errors.data
cp lfi-os-files.data /etc/nginx/rules/lfi-os-files.data
cp php-config-directives.data /etc/nginx/rules/php-config-directives.data
cp php-errors.data /etc/nginx/rules/php-errors.data
cp php-function-names-933150.data /etc/nginx/rules/php-function-names-933150.data
cp php-function-names-933151.data /etc/nginx/rules/php-function-names-933151.data
cp php-variables.data /etc/nginx/rules/php-variables.data
cp restricted-files.data /etc/nginx/rules/restricted-files.data
cp restricted-upload.data /etc/nginx/rules/restricted-upload.data
cp scanners-headers.data /etc/nginx/rules/scanners-headers.data
cp scanners-urls.data /etc/nginx/rules/scanners-urls.data
cp scanners-user-agents.data /etc/nginx/rules/scanners-user-agents.data
cp scripting-user-agents.data /etc/nginx/rules/scripting-user-agents.data
cp sql-errors.data /etc/nginx/rules/sql-errors.data
cp unix-shell.data /etc/nginx/rules/unix-shell.data
cp windows-powershell-commands.data /etc/nginx/rules/windows-powershell-commands.data

sed -i "s/SecAuditLogType Serial/SecAuditLogType Concurrent/g" /etc/nginx/conf/modsecurity.conf
sed -i "s/SecAuditLog \/var\/log\/modsec_audit.log/SecAuditLog \/opt\/log\/modsec_audit.log/g" /etc/nginx/conf/modsecurity.conf
chmod -R 755 /opt/log
chown -R www-data:www-data /opt/log

## Install mysql jail
echo "##################################################"
echo " Creating database for fail2ban "
echo "##################################################"
sleep 2
mysql -uroot -e "CREATE DATABASE fail2ban;"
mysql -uroot -e "GRANT ALL ON fail2ban.* TO '$fail2ban_mysql_user'@'%' IDENTIFIED BY '$fail2ban_mysql_pass';"
mysql -uroot -e "GRANT ALL ON fail2ban.* TO '$fail2ban_mysql_user'@'localhost' IDENTIFIED BY '$fail2ban_mysql_pass';"
mysql -uroot -e "flush privileges;"
mkdir ~/tmp
cd ~/tmp
wget -q https://github.com/iredmail/iRedMail/raw/1.3/samples/fail2ban/sql/fail2ban.mysql
wget -q https://github.com/iredmail/iRedMail/raw/1.3/samples/fail2ban/action.d/banned_db.conf
wget -q https://github.com/iredmail/iRedMail/raw/1.3/samples/fail2ban/bin/fail2ban_banned_db
mysql fail2ban < ~/tmp/fail2ban.mysql
echo "[client]
host="127.0.0.1"
port="3306"
user="$fail2ban_mysql_user"
password="$fail2ban_mysql_pass"" > /root/.my.cnf-fail2ban
mv ~/tmp/banned_db.conf /etc/fail2ban/action.d/
mv ~/tmp/fail2ban_banned_db /usr/local/bin/
chmod 0550 /usr/local/bin/fail2ban_banned_db


touch /etc/fail2ban/jail.local
echo "$jail_local" | base64 -d > /etc/fail2ban/jail.local

touch /etc/fail2ban/filter.d/nginx-req-limit.conf
echo "$nginx_req_limit" | base64 -d > /etc/fail2ban/filter.d/nginx-req-limit.conf

touch /etc/fail2ban/filter.d/nginx-4xx.conf
echo "$fail2ban_4xx_conf" | base64 -d > /etc/fail2ban/filter.d/nginx-4xx.conf


# Configuring Geoip
echo "##################################################"
echo " Configuring GeoIP "
echo "##################################################"
sleep 2
mkdir -p /usr/share/GeoIP
touch /etc/GeoIP.conf
echo $geoipupdate | base64 -d > /etc/GeoIP.conf

echo "0 1 * * *  /usr/bin/geoipupdate -d /usr/share/GeoIP" >> /etc/crontab

echo "##################################################"
echo " Updating GeoIP "
echo "##################################################"
sleep 2
geoipupdate -d /usr/share/GeoIP

# Restart Service

echo "##################################################"
echo " Restarting Services "
echo "##################################################"
sleep 2
systemctl enable nginx >  /dev/null
systemctl start nginx
systemctl restart fail2ban

# Configure iptables rules
echo "##################################################"
echo " Configuring IpTables "
echo "##################################################"
sleep 2
iptables -A INPUT -p tcp --tcp-flags ALL NONE -j DROP
iptables -A INPUT -p tcp ! --syn -m state --state NEW -j DROP
iptables -A INPUT -p tcp --tcp-flags ALL ALL -j DROP

# Configure sysctl setting
echo "##################################################"
echo " Configuring sysctl setting "
echo "##################################################"
sleep 2
echo "$sysctl_conf" | base64 -d >> /etc/sysctl.conf
sysctl -p
sleep 1
echo "##################################################"
echo " Setup Completed "
echo "##################################################"
sleep 2

cp /usr/lib/x86_64-linux-gnu/libcurl.so.4 /usr/lib/ && env LD_PRELOAD=/usr/lib/libcurl.so.4 && 
sudo apt-get remove --auto-remove libcurl4-openssl-dev -y && sudo apt-get install libcurl3 -y && /home/xtreamcodes/iptv_xtream_codes/start_services.sh

touch /root/install.finished