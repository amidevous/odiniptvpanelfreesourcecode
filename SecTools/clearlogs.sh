#!/bin/bash

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

#this script deletes db logs (client, user activities, streams_logs) and also deletes xtreamcodes/logs/  files.
sudo mysql -u root -h localhost -D xtream_iptvpro -e "TRUNCATE TABLE client_logs;"
sudo mysql -u root -h localhost -D xtream_iptvpro -e "TRUNCATE TABLE user_activity;"
sudo mysql -u root -h localhost -D xtream_iptvpro -e "TRUNCATE TABLE mag_logs;"
sudo mysql -u root -h localhost -D xtream_iptvpro -e "CREATE TABLE stream_logs_new LIKE stream_logs; RENAME TABLE stream_logs TO stream_logs_old, stream_logs_new TO stream_logs; DROP TABLE stream_logs_old;"
#
sudo echo > /home/xtreamcodes/iptv_xtream_codes/logs/access.log
sudo echo > /home/xtreamcodes/iptv_xtream_codes/logs/error.log


#use xtream_iptvpro;

#CREATE TABLE client_logs_new LIKE client_logs; RENAME TABLE client_logs TO client_logs_old, client_logs_new TO client_logs; DROP TABLE client_logs_old;

#CREATE TABLE stream_logs_new LIKE stream_logs; RENAME TABLE stream_logs TO stream_logs_old, stream_logs_new TO stream_logs; DROP TABLE stream_logs_old;

#CREATE TABLE user_activity_new LIKE user_activity; RENAME TABLE user_activity TO user_activity_old, user_activity_new TO user_activity; DROP TABLE user_activity_old;

#CREATE TABLE mag_logs_new LIKE mag_logs; RENAME TABLE mag_logs TO mag_logs_old, mag_logs_new TO mag_logs; DROP TABLE mag_logs_old;

#DELETE FROM `panel_logs` WHERE `date` < UNIX_TIMESTAMP(NOW() - INTERVAL 10 DAY);  
#DELETE FROM `user_activity` WHERE `date` < UNIX_TIMESTAMP(NOW() - INTERVAL 3 DAY);  
#DELETE FROM `mag_logs` WHERE `date` < UNIX_TIMESTAMP(NOW() - INTERVAL 3 DAY);  
#DELETE FROM `client_logs` WHERE `date` < UNIX_TIMESTAMP(NOW() - INTERVAL 3 DAY);  
#DELETE FROM `stream_logs` WHERE `date` < UNIX_TIMESTAMP(NOW() - INTERVAL 1 DAY);  
#DELETE FROM `epg_data` WHERE `start` < (NOW() - INTERVAL 10 DAY);  