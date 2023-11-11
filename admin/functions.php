<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include_once "/home/xtreamcodes/iptv_xtream_codes/admin/HTMLPurifier.standalone.php";
$rRelease = 22;
$rEarlyAccess = " CK ";
$rTimeout = 60;
$rSQLTimeout = 5;
$rDebug = false;
$rPurifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());
$rGoogleDriveApi = "https://www.googleapis.com/";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if ($rDebug) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(32767);
} else {
    ini_set("display_errors", 0);
    ini_set("display_startup_errors", 0);
    error_reporting(7);
}
set_time_limit($rTimeout);
ini_set("mysql.connect_timeout", $rSQLTimeout);
ini_set("max_execution_time", $rTimeout);
ini_set("default_socket_timeout", $rTimeout);
define("MAIN_DIR", "/home/xtreamcodes/iptv_xtream_codes/");
define("CONFIG_CRYPT_KEY", "5709650b0d7806074842c6de575025b1");
require_once realpath(dirname(__FILE__)) . "/mobiledetect.php";
require_once realpath(dirname(__FILE__)) . "/gauth.php";
$_INFO = json_decode(xor_parse(base64_decode(file_get_contents(MAIN_DIR . "config")), CONFIG_CRYPT_KEY), true);
if (!($db = new mysqli($_INFO["host"], $_INFO["db_user"], $_INFO["db_pass"], $_INFO["db_name"], $_INFO["db_port"]))) {
    exit("No MySQL connection!");
}
$db->set_charset("utf8");
$db->query("SET GLOBAL MAX_EXECUTION_TIME=" . $rSQLTimeout * 1000 . ";");
date_default_timezone_set(gettimezone());
$rAdminSettings = getadminsettings();
$rSettings = getsettings();
$nabilos = getRegisteredUserHash($_SESSION["hash"]);
if (0 < strlen($nabilos["default_lang"]) && file_exists("./lang/" . $nabilos["default_lang"] . ".php")) {
    include "./lang/" . $nabilos["default_lang"] . ".php";
} else {
    include "/home/xtreamcodes/iptv_xtream_codes/admin/lang/en.php";
}
$detect = new Mobile_Detect();
$rClientFilters = ["NOT_IN_BOUQUET" => "Not in Bouquet", "CON_SVP" => "Connection Issue", "ISP_LOCK_FAILED" => "ISP Lock Failed", "USER_DISALLOW_EXT" => "Extension Disallowed", "AUTH_FAILED" => "Authentication Failed", "USER_EXPIRED" => "User Expired", "USER_DISABLED" => "User Disabled", "USER_BAN" => "User Banned"];
if (file_exists("/home/xtreamcodes/iptv_xtream_codes/admin/.update")) {
    unlink("/home/xtreamcodes/iptv_xtream_codes/admin/.update");
    if (!file_exists("/home/xtreamcodes/iptv_xtream_codes/admin/.update")) {
        updatetables();
        forcesecurity();
    }
}
$rTableSearch = strtolower(basename($_SERVER["SCRIPT_FILENAME"], ".php")) === "table_search";
$_GET = xssrow($_GET, $rTableSearch);
$_POST = xssrow($_POST, $rTableSearch);
if (isset($_SESSION["hash"])) {
    $rUserInfo = getregistereduserhash($_SESSION["hash"]);
    $rAdminSettings["dark_mode"] = $rUserInfo["dark_mode"];
    $rAdminSettings["expanded_sidebar"] = $rUserInfo["expanded_sidebar"];
    $rSettings["sidebar"] = $rUserInfo["sidebar"];
    $rPermissions = getpermissions($rUserInfo["member_group_id"]);
    if ($rPermissions["is_admin"]) {
        $rPermissions["is_reseller"] = 0;
    }
    $rPermissions["advanced"] = json_decode($rPermissions["allowed_pages"], true);
    if (!$rUserInfo || !$rPermissions || !$rPermissions["is_admin"] && !$rPermissions["is_reseller"] || $_SESSION["ip"] != getip() && $rAdminSettings["ip_logout"]) {
        unset($rUserInfo);
        unset($rPermissions);
        session_unset();
        session_destroy();
        header("Location: ./index.php");
    }
    $rCategories = getcategories();
    $rServers = getstreamingservers();
    $rServerError = false;
    $TokenTelegram = $rAdminSettings["token_telegram"];
    $ChatID = $rAdminSettings["chat_id"];
    foreach ($rServers as $rServer) {
        if ((360 < time() - $rServer["last_check_ago"] || $rServer["status"] == 2) && $rServer["can_delete"] == 1 && $rServer["status"] != 3) {
            $rServerError = true;
            if (1800 < time() - LastCheck("Servers")) {
                $data = ["chat_id" => $ChatID, "text" => "❌ Server " . $rServer["server_name"] . " - " . $rServer["server_ip"] . " - Offline"];
                LastCheckNew(time(), "Servers");
                $response = file_get_contents("https://api.telegram.org/bot" . $TokenTelegram . "/sendMessage?" . http_build_query($data));
            }
        }
        if ($rServer["status"] == 3 && 0 < $rServer["last_check_ago"]) {
            $db->query("UPDATE `streaming_servers` SET `status` = 1 WHERE `id` = " . intval($rServer["id"]) . ";");
            $rServers[intval($rServer["id"])]["status"] = 1;
            $data = ["chat_id" => $ChatID, "text" => "✅ Server " . $rServer["server_name"] . " - " . $rServer["server_ip"] . " - Added"];
            $response = file_get_contents("https://api.telegram.org/bot" . $TokenTelegram . "/sendMessage?" . http_build_query($data));
        }
    }
}
function XSS($rString, $rSQL = false)
{
    global $rPurifier;
    global $db;
    if (is_null($rString) || strtoupper($rString) == "NULL") {
        return NULL;
    }
    if (is_array($rString)) {
        return XSSRow($rString, $rSQL);
    }
    if ($rSQL) {
        return $db->real_escape_string(str_replace("&quot;", "\"", str_replace("&amp;", "&", $rPurifier->purify($rString))));
    }
    return str_replace("&quot;", "\"", str_replace("&amp;", "&", $rPurifier->purify($rString)));
}
function getRegUsersStats()
{
    global $db;
    return $result = mysqli_query($db, "SELECT * FROM `reg_users` ORDER BY owner_id ");
}
function getLastMovies()
{
    global $db;
    return $result = mysqli_query($db, "SELECT * FROM streams WHERE type = '2' ORDER by id DESC LIMIT 10;");
}
function getLastSeries()
{
    global $db;
    return $result = mysqli_query($db, "SELECT * FROM series ORDER by last_modified DESC LIMIT 10;");
}
function XSSRow($rRow, $rSQL = false)
{
    foreach ($rRow as $rKey => $rValue) {
        if (is_array($rValue)) {
            $rRow[$rKey] = XSSRow($rValue, $rSQL);
        } else {
            $rRow[$rKey] = xss($rValue, $rSQL);
        }
    }
    return $rRow;
}
function ESC($rString)
{
    return xss($rString, true);
}
function sortArrayByArray($rArray, $rSort)
{
    $rOrdered = [];
    foreach ($rSort as $rValue) {
        if (($rKey = array_search($rValue, $rArray)) !== false) {
            $rOrdered[] = $rValue;
            unset($rArray[$rKey]);
        }
    }
    return $rOrdered + $rArray;
}
function startcmd()
{
    echo shell_exec("/usr/bin/python " . MAIN_DIR . "pytools/balancer.py >/dev/null 2>&1 &");
}
function updateGeoLite2()
{
    global $rAdminSettings;
    $rURL = "http://xcodes.mine.nu/XCodes/status.json";
    $rData = json_decode(file_get_contents($rURL), true);
    if ($rData["version"]) {
        $rFileData = file_get_contents("http://xcodes.mine.nu/XCodes/GeoLite2.mmdb");
        if (stripos($rFileData, "MaxMind.com") !== false) {
            $rFilePath = "/home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb";
            exec("sudo chattr -i " . $rFilePath);
            unlink($rFilePath);
            file_put_contents($rFilePath, $rFileData);
            exec("sudo chmod 777 " . $rFilePath);
            exec("sudo chattr +i " . $rFilePath);
            if (file_get_contents($rFilePath) == $rFileData) {
                $rAdminSettings["geolite2_version"] = $rData["version"];
                writeAdminSettings();
                return true;
            }
            return false;
        }
    }
    return false;
}
function updatePanel()
{
    global $rAdminSettings;
    $rURL2 = "http://xcodes.mine.nu/XCodes/current.json";
    $rData2 = json_decode(file_get_contents($rURL2), true);
    if ($rData2["version"]) {
        $rFileData2 = file_get_contents("/home/xtreamcodes/iptv_xtream_codes/pytools/autoupdate.py");
        if (stripos($rFileData2, "# update panel") !== false) {
            $rFilePath2 = "/tmp/autoupdate.py";
            file_put_contents($rFilePath2, $rFileData2);
            exec("sudo chmod 777 " . $rFilePath2);
            if (file_get_contents($rFilePath2) == $rFileData2) {
                $rAdminSettings["panel_version"] = $rData2["version"];
                writeAdminSettings();
                exec("rm /usr/bin/ffmpeg");
                exec("rm /usr/bin/ffprobe");
                exec("chattr -i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb");
                exec("wget \"http://xcodes.mine.nu/XCodes/update.zip\" -O /tmp/update.zip -o /dev/null");
                exec("unzip /tmp/update.zip -d /tmp/update/ >/dev/null");
                exec("rm -rf /home/xtreamcodes/iptv_xtream_codes/crons");
                exec("rm -rf /home/xtreamcodes/iptv_xtream_codes/php/etc");
                exec("cp -rf /tmp/update/XtreamUI-master/* /home/xtreamcodes/iptv_xtream_codes/ 2>/dev/null");
                exec("rm -rf /tmp/update/XtreamUI-master");
                exec("rm /tmp/update.zip");
                exec("rm -rf /tmp/update");
                exec("wget http://xcodes.mine.nu/XCodes/GeoLite2.mmdb -O /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb -o /dev/null");
                exec("chown -R xtreamcodes:xtreamcodes /home/xtreamcodes");
                exec("find /home/xtreamcodes/ -type d -not \\( -name .update -prune \\) -exec chmod -R 777 {} + ");
                exec("chattr +i /home/xtreamcodes/iptv_xtream_codes/GeoLite2.mmdb");
                exec("ln -s /home/xtreamcodes/iptv_xtream_codes/bin/ffmpeg /usr/bin/");
                exec("rm /tmp/autoupdate.py");
                return true;
            }
            return false;
        }
    }
    return false;
}
function mapmap()
{
    global $db;
    $rQuery = "SELECT geoip_country_code, count(geoip_country_code) AS total FROM user_activity_now GROUP BY geoip_country_code";
    if ($rResult = $db->query($rQuery)) {
        while ($row = $rResult->fetch_assoc()) {
            $gggrr = "{\"code\":" . json_encode($row["geoip_country_code"]) . ",\"value\":" . json_encode($row["total"]) . "},";
            echo $gggrr;
        }
    }
}
function resetSTB($rID)
{
    global $db;
    $db->query("UPDATE `mag_devices` SET `ip` = NULL, `ver` = NULL, `image_version` = NULL, `stb_type` = NULL, `sn` = NULL, `device_id` = NULL, `device_id2` = NULL, `hw_version` = NULL, `token` = NULL WHERE `mag_id` = " . intval($rID) . ";");
}
function resetispnames($rID)
{
    global $db;
    $db->query("UPDATE `users` SET `isp_desc` = NULL WHERE `id` = " . intval($rID) . ";");
}
function getAdminSettings()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT `type`, `value` FROM `admin_settings`;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["type"]] = $row["value"];
        }
    }
    return $return;
}
function getSettings()
{
    global $db;
    $result = $db->query("SELECT * FROM `settings` LIMIT 1;");
    return $result->fetch_assoc();
}
function getTimezone()
{
    global $db;
    $result = $db->query("SELECT `default_timezone` FROM `settings`;");
    if (isset($result) && $result->num_rows == 1) {
        return xss($result->fetch_assoc()["default_timezone"]);
    }
    return "Europe/London";
}
function xor_parse($data, $key)
{
    $i = 0;
    $output = "";
    foreach (str_split($data) as $char) {
        $output .= chr(ord($char) ^ ord($key[$i++ % strlen($key)]));
    }
    return $output;
}
function APIRequest($rData)
{
    global $rAdminSettings;
    global $rServers;
    global $_INFO;
    ini_set("default_socket_timeout", 5);
    if ($rAdminSettings["local_api"]) {
        $rAPI = "http://127.0.0.1:" . $rServers[$_INFO["server_id"]]["http_broadcast_port"] . "/api.php";
    } else {
        $rAPI = "http://" . $rServers[$_INFO["server_id"]]["server_ip"] . ":" . $rServers[$_INFO["server_id"]]["http_broadcast_port"] . "/api.php";
    }
    $rPost = http_build_query($rData);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $rAPI);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rPost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $rData = curl_exec($ch);
    return $rData;
}
function SystemAPIRequest($rServerID, $rData)
{
    global $rServers;
    global $rSettings;
    ini_set("default_socket_timeout", 5);
    $rAPI = "http://" . $rServers[intval($rServerID)]["server_ip"] . ":" . $rServers[intval($rServerID)]["http_broadcast_port"] . "/system_api.php";
    $rData["password"] = $rSettings["live_streaming_pass"];
    $rPost = http_build_query($rData);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $rAPI);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rPost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $rData = curl_exec($ch);
    return $rData;
}
function multiexplode($delimiters, $data)
{
    $MakeReady = str_replace($delimiters, $delimiters[0], $data);
    $Return = array_filter(explode($delimiters[0], $MakeReady));
    return $Return;
}
function sexec($rServerID, $rCommand)
{
    global $_INFO;
    if ($rServerID != $_INFO["server_id"]) {
        return systemapirequest($rServerID, ["action" => "BackgroundCLI", "cmds" => [$rCommand]]);
    }
    return exec($rCommand);
}
function sexec2($rServerID, $rCommand)
{
    $loool = systemapirequest($rServerID, ["action" => "BackgroundCLI", "cmds" => [$rCommand]]);
    return $loool;
}
function netnet($rServerID)
{
    $ccc = sexec2($rServerID, "ls -1 /sys/class/net");
    $ttt = multiexplode(["[", "\"", "\\n", "]"], $ccc);
    array_push($ttt, "");
    return $ttt;
}
function loadnginx($rServerID)
{
    sexec($rServerID, "sudo /home/xtreamcodes/iptv_xtream_codes/nginx/sbin/nginx -s reload");
    sexec($rServerID, "sudo /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp -s reload");
}
function changePort($rServerID, $rType, $rOldPort, $rNewPort)
{
    if ($rType == 1) {
        sexec($rServerID, "sed -i 's/listen " . intval($rOldPort) . " ssl;/listen " . intval($rNewPort) . " ssl;/g' /home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf");
    } else {
        if ($rType == 2) {
            sexec($rServerID, "sed -i 's/listen " . intval($rOldPort) . ";/listen " . intval($rNewPort) . ";/g' /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf");
        } else {
            if ($rType == 0) {
                sexec($rServerID, "sed -i 's/listen " . intval($rOldPort) . ";/listen " . intval($rNewPort) . ";/g' /home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf");
                sexec($rServerID, "sed -i 's/:" . intval($rOldPort) . "/:" . intval($rNewPort) . "/g' /home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/conf/nginx.conf");
            }
        }
    }
}
function changeIsp($rServerID, $rType, $rOldPort, $rNewPort)
{
    if ($rType == 3) {
        sexec($rServerID, "sed -i 's/listen " . intval($rOldPort) . ";/listen " . intval($rNewPort) . ";/g' /home/xtreamcodes/iptv_xtream_codes/nginx/conf/nginx.conf");
        sexec($rServerID, "sed -i 's|:" . intval($rOldPort) . "/api.php|:" . intval($rNewPort) . "/api.php|g' /home/xtreamcodes/iptv_xtream_codes/wwwdir/includes/streaming.php");
    }
}
function getPIDs($rServerID)
{
    global $rAdminSettings;
    $rReturn = [];
    $rFilename = tempnam(MAIN_DIR . "tmp/", "proc_");
    $rCommand = "ps aux >> " . $rFilename;
    sexec($rServerID, $rCommand);
    $rData = "";
    $rI = 3;
    while (strlen($rData) == 0) {
        $rData = systemapirequest($rServerID, ["action" => "getFile", "filename" => $rFilename]);
        $rI--;
        if (!($rI == 0 || 0 < strlen($rData))) {
            sleep(1);
        }
    }
    $rProcesses = explode("\n", $rData);
    array_shift($rProcesses);
    foreach ($rProcesses as $rProcess) {
        $rSplit = explode(" ", preg_replace("!\\s+!", " ", trim($rProcess)));
        if (0 < strlen($rSplit[0])) {
            $rReturn[] = ["user" => $rSplit[0], "pid" => $rSplit[1], "cpu" => $rSplit[2], "mem" => $rSplit[3], "vsz" => $rSplit[4], "rss" => $rSplit[5], "tty" => $rSplit[6], "stat" => $rSplit[7], "start" => $rSplit[8], "time" => $rSplit[9], "command" => join(" ", array_splice($rSplit, 10, count($rSplit) - 10))];
        }
    }
    return $rReturn;
}
function getFreeSpace($rServerID)
{
    $rReturn = [];
    $rFilename = tempnam(MAIN_DIR . "tmp/", "fs_");
    $rCommand = "df -h >> " . $rFilename;
    sexec($rServerID, $rCommand);
    $rData = systemapirequest($rServerID, ["action" => "getFile", "filename" => $rFilename]);
    $rLines = explode("\n", $rData);
    array_shift($rLines);
    foreach ($rLines as $rLine) {
        $rSplit = explode(" ", preg_replace("!\\s+!", " ", trim($rLine)));
        $rReturn[] = ["filesystem" => $rSplit[0], "size" => $rSplit[1], "used" => $rSplit[2], "avail" => $rSplit[3], "percentage" => $rSplit[4], "mount" => join(" ", array_slice($rSplit, 5, count($rSplit) - 5))];
    }
    return $rReturn;
}
function remoteCMD($rServerID, $rCommand)
{
    $rReturn = [];
    $rFilename = tempnam(MAIN_DIR . "tmp/", "cmd_");
    sexec($rServerID, $rCommand . " >> " . $rFilename);
    $rData = "";
    $rI = 3;
    while (strlen($rData) == 0) {
        $rData = systemapirequest($rServerID, ["action" => "getFile", "filename" => $rFilename]);
        $rI--;
        if (!($rI == 0 || 0 < strlen($rData))) {
            sleep(1);
        }
    }
    unset($rFilename);
    return $rData;
}
function freeTemp($rServerID)
{
    sexec($rServerID, "rm " . MAIN_DIR . "tmp/*");
}
function freeStreams($rServerID)
{
    sexec($rServerID, "rm " . MAIN_DIR . "streams/*");
}
function getStreamPIDs($rServerID)
{
    global $db;
    $return = [];
    $result = $db->query("SELECT `streams`.`id`, `streams`.`stream_display_name`, `streams`.`type`, `streams_sys`.`pid`, `streams_sys`.`monitor_pid`, `streams_sys`.`delay_pid` FROM `streams_sys` LEFT JOIN `streams` ON `streams`.`id` = `streams_sys`.`stream_id` WHERE `streams_sys`.`server_id` = " . intval($rServerID) . ";");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            foreach (["pid", "monitor_pid", "delay_pid"] as $rPIDType) {
                if ($row[$rPIDType]) {
                    $return[$row[$rPIDType]] = ["id" => $row["id"], "title" => $row["stream_display_name"], "type" => $row["type"], "pid_type" => $rPIDType];
                }
            }
        }
    }
    $result = $db->query("SELECT `id`, `stream_display_name`, `type`, `tv_archive_pid` FROM `streams` WHERE `tv_archive_server_id` = " . intval($rServerID) . ";");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            if ($row["pid"]) {
                $return[$row["pid"]] = ["id" => $row["id"], "title" => $row["stream_display_name"], "type" => $row["type"], "pid_type" => "timeshift"];
            }
        }
    }
    $result = $db->query("SELECT `streams`.`id`, `streams`.`stream_display_name`, `streams`.`type`, `user_activity_now`.`pid` FROM `user_activity_now` LEFT JOIN `streams` ON `streams`.`id` = `user_activity_now`.`stream_id` WHERE `user_activity_now`.`server_id` = " . intval($rServerID) . ";");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            if ($row["pid"]) {
                $return[$row["pid"]] = ["id" => $row["id"], "title" => $row["stream_display_name"], "type" => $row["type"], "pid_type" => "activity"];
            }
        }
    }
    return $return;
}
function roundUpToAny($n, $x = 5)
{
    return round(($n + $x / 2) / $x) * $x;
}
function checkSource($rServerID, $rFilename)
{
    global $rServers;
    global $rSettings;
    $rAPI = "http://" . $rServers[intval($rServerID)]["server_ip"] . ":" . $rServers[intval($rServerID)]["http_broadcast_port"] . "/system_api.php?password=" . $rSettings["live_streaming_pass"] . "&action=getFile&filename=" . urlencode(escapeshellcmd($rFilename));
    $rCommand = "timeout 5 " . MAIN_DIR . "bin/ffprobe -show_streams -v quiet \"" . $rAPI . "\" -of json";
    return json_decode(shell_exec($rCommand), true);
}
function getSelections($rSources)
{
    global $db;
    $return = [];
    foreach ($rSources as $rSource) {
        $result = $db->query("SELECT `id` FROM `streams` WHERE `type` IN (2,5) AND `stream_source` LIKE '%" . esc(str_replace("/", "\\/", $rSource)) . "\"%' ESCAPE '|' LIMIT 1;");
        if ($result && $result->num_rows == 1) {
            $return[] = intval($result->fetch_assoc()["id"]);
        }
    }
    return $return;
}
function getBackups()
{
    $rBackups = [];
    foreach (scandir(MAIN_DIR . "adtools/backups/") as $rBackup) {
        $rInfo = pathinfo(MAIN_DIR . "adtools/backups/" . $rBackup);
        if ($rInfo["extension"] == "gz") {
            $rBackups[] = ["filename" => $rBackup, "timestamp" => filemtime(MAIN_DIR . "adtools/backups/" . $rBackup), "date" => date("Y-m-d H:i:s", filemtime(MAIN_DIR . "adtools/backups/" . $rBackup)), "filesize" => filesize(MAIN_DIR . "adtools/backups/" . $rBackup)];
        }
    }
    usort($rBackups, function ($a, $b) {
        $a["timestamp"];
        $b["timestamp"];
    });
    return $rBackups;
}
function parseRelease($rRelease)
{
    $rCommand = "/usr/bin/python " . MAIN_DIR . "pytools/release.py \"" . escapeshellcmd($rRelease) . "\"";
    return json_decode(shell_exec($rCommand), true);
}
function listDir($rServerID, $rDirectory, $rAllowed = NULL)
{
    global $rServers;
    global $_INFO;
    global $rSettings;
    set_time_limit(60);
    ini_set("max_execution_time", 60);
    $rReturn = ["dirs" => [], "files" => []];
    if ($rServerID == $_INFO["server_id"]) {
        $rFiles = scanDir($rDirectory);
        foreach ($rFiles as $rKey => $rValue) {
            if (!in_array($rValue, [".", ".."])) {
                if (is_dir($rDirectory . "/" . $rValue)) {
                    $rReturn["dirs"][] = $rValue;
                } else {
                    $rExt = strtolower(pathinfo($rValue)["extension"]);
                    if (is_array($rAllowed) && in_array($rExt, $rAllowed) || !$rAllowed) {
                        $rReturn["files"][] = $rValue;
                    }
                }
            }
        }
    } else {
        if ($rAdminSettings["alternate_scandir"]) {
            $rFilename = tempnam(MAIN_DIR . "tmp/", "ls_");
            $rCommand = "ls -cm -f --group-directories-first --indicator-style=slash \"" . escapeshellcmd($rDirectory) . "\" >> " . $rFilename;
            sexec($rServerID, $rCommand);
            $rData = "";
            $rI = 2;
            while (strlen($rData) == 0) {
                $rData = systemapirequest($rServerID, ["action" => "getFile", "filename" => $rFilename]);
                $rI--;
                if (!($rI == 0 || 0 < strlen($rData))) {
                    sleep(1);
                }
            }
            if (0 < strlen($rData)) {
                $rFiles = explode(",", $rData);
                sort($rFiles);
                foreach ($rFiles as $rFile) {
                    $rFile = trim($rFile);
                    if (substr($rFile, -1) == "/") {
                        if (substr($rFile, 0, -1) != ".." && substr($rFile, 0, -1) != ".") {
                            $rReturn["dirs"][] = substr($rFile, 0, -1);
                        }
                    } else {
                        $rExt = strtolower(pathinfo($rFile)["extension"]);
                        if (is_array($rAllowed) && in_array($rExt, $rAllowed) || !$rAllowed) {
                            $rReturn["files"][] = $rFile;
                        }
                    }
                }
            }
        } else {
            $rData = systemapirequest($rServerID, ["action" => "viewDir", "dir" => $rDirectory]);
            $rDocument = new DOMDocument();
            $rDocument->loadHTML($rData);
            $rFiles = $rDocument->getElementsByTagName("li");
            foreach ($rFiles as $rFile) {
                if (stripos($rFile->getAttribute("class"), "directory") !== false) {
                    $rReturn["dirs"][] = $rFile->nodeValue;
                } else {
                    if (stripos($rFile->getAttribute("class"), "file") !== false) {
                        $rExt = strtolower(pathinfo($rFile->nodeValue)["extension"]);
                        if (is_array($rAllowed) && in_array($rExt, $rAllowed) || !$rAllowed) {
                            $rReturn["files"][] = $rFile->nodeValue;
                        }
                    }
                }
            }
        }
    }
    return $rReturn;
}
function scanRecursive($rServerID, $rDirectory, $rAllowed = NULL)
{
    $result = [];
    $rFiles = listdir($rServerID, $rDirectory, $rAllowed);
    foreach ($rFiles["files"] as $rFile) {
        $rFilePath = rtrim($rDirectory, "/") . "/" . $rFile;
        $result[] = $rFilePath;
    }
    foreach ($rFiles["dirs"] as $rDir) {
        foreach (scanRecursive($rServerID, rtrim($rDirectory, "/") . "/" . $rDir . "/", $rAllowed) as $rFile) {
            $result[] = $rFile;
        }
    }
    return $result;
}
function getEncodeErrors($rID)
{
    global $rSettings;
    $rServers = getStreamingServers(true);
    ini_set("default_socket_timeout", 3);
    $rErrors = [];
    $rStreamSys = getStreamSys($rID);
    foreach ($rStreamSys as $rServer) {
        $rServerID = $rServer["server_id"];
        if (isset($rServers[$rServerID]) && !(0 < $rServer["pid"] && $rServer["to_analyze"] == 0 && $rServer["stream_status"] != 1)) {
            $rFilename = MAIN_DIR . "movies/" . intval($rID) . ".errors";
            $rError = systemapirequest($rServerID, ["action" => "getFile", "filename" => $rFilename]);
            if (0 < strlen($rError)) {
                $rErrors[$rServerID] = $rError;
            }
        }
    }
    return $rErrors;
}
function getTimeDifference($rServerID)
{
    global $rServers;
    global $rSettings;
    ini_set("default_socket_timeout", 3);
    $rError = systemapirequest($rServerID, ["action" => "getDiff", "main_time" => intval(time())]);
    return is_file($rAPI) ? intval(file_get_contents($rAPI)) : "";
}
function deleteMovieFile($rServerID, $rID)
{
    global $rServers;
    global $rStreams;
    global $rSettings;
    ini_set("default_socket_timeout", 3);
    $rCommand = "rm " . MAIN_DIR . "movies/" . $rID . "*";
    sexec($rServerID, $rCommand);
}
function generateString($strength = 10)
{
    $input = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
    $input_length = strlen($input);
    $random_string = "";
    for ($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}
function getStreamingServers($rActive = false)
{
    global $db;
    global $rPermissions;
    $return = [];
    if ($rActive) {
        $result = $db->query("SELECT * FROM `streaming_servers` WHERE `status` = 1 ORDER BY `id` ASC;");
    } else {
        $result = $db->query("SELECT * FROM `streaming_servers` ORDER BY `id` ASC;");
    }
    if (0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            if ($rPermissions["is_reseller"]) {
                $row["server_name"] = "Server #" . $row["id"];
            }
            $return[$row["id"]] = $row;
        }
    }
    return $return;
}
function getStreamingServersByID($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `streaming_servers` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return false;
}
function getStreamList()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT `streams`.`id`, `streams`.`stream_display_name`, `stream_categories`.`category_name` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` ORDER BY `streams`.`stream_display_name` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getConnections($rServerID)
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `user_activity_now` WHERE `server_id` = '" . esc($rServerID) . "';");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getUserConnections($rUserID)
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `user_activity_now` WHERE `user_id` = '" . esc($rUserID) . "';");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getEPGSources()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `epg`;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["id"]] = $row;
        }
    }
    return $return;
}
function findEPG($rEPGName)
{
    global $db;
    $result = $db->query("SELECT `id`, `data` FROM `epg`;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            foreach (json_decode($row["data"], true) as $rChannelID => $rChannelData) {
                if ($rChannelID == $rEPGName) {
                    if (0 < count($rChannelData["langs"])) {
                        $rEPGLang = $rChannelData["langs"][0];
                    } else {
                        $rEPGLang = "";
                    }
                    return ["channel_id" => $rChannelID, "epg_lang" => $rEPGLang, "epg_id" => intval($row["id"])];
                }
            }
        }
    }
}
function getStreamArguments()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `streams_arguments` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["argument_key"]] = $row;
        }
    }
    return $return;
}
function getTranscodeProfiles()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `transcoding_profiles` ORDER BY `profile_id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getWatchFolders($rType = NULL)
{
    global $db;
    $return = [];
    if ($rType) {
        $result = $db->query("SELECT * FROM `watch_folders` WHERE `type` = '" . esc($rType) . "' ORDER BY `id` ASC;");
    } else {
        $result = $db->query("SELECT * FROM `watch_folders` ORDER BY `id` ASC;");
    }
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getWatchCategories($rType = NULL)
{
    global $db;
    $return = [];
    if ($rType) {
        $result = $db->query("SELECT * FROM `watch_categories` WHERE `type` = " . intval($rType) . " ORDER BY `genre_id` ASC;");
    } else {
        $result = $db->query("SELECT * FROM `watch_categories` ORDER BY `genre_id` ASC;");
    }
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["genre_id"]] = $row;
        }
    }
    return $return;
}
function getWatchFolder($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `watch_folders` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getSeriesByTMDB($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `series` WHERE `tmdb_id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getSeries()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `series` ORDER BY `title` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getSerie($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `series` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getSeriesTrailer($rTMDBID)
{
    global $rSettings;
    global $rAdminSettings;
    if (0 < strlen($rAdminSettings["tmdb_language"])) {
        $rURL = "https://api.themoviedb.org/3/tv/" . $rTMDBID . "/videos?api_key=" . $rSettings["tmdb_api_key"] . "&language=" . $rAdminSettings["tmdb_language"];
    } else {
        $rURL = "https://api.themoviedb.org/3/tv/" . $rTMDBID . "/videos?api_key=" . $rSettings["tmdb_api_key"];
    }
    $rJSON = json_decode(file_get_contents($rURL), true);
    foreach ($rJSON["results"] as $rVideo) {
        if (strtolower($rVideo["type"]) == "trailer" && strtolower($rVideo["site"]) == "youtube") {
            return $rVideo["key"];
        }
    }
    return "";
}
function getStills($rTMDBID, $rSeason, $rEpisode)
{
    global $rSettings;
    global $rAdminSettings;
    if (0 < strlen($rAdminSettings["tmdb_language"])) {
        $rURL = "https://api.themoviedb.org/3/tv/" . $rTMDBID . "/season/" . $rSeason . "/episode/" . $rEpisode . "/images?api_key=" . $rSettings["tmdb_api_key"] . "&language=" . $rAdminSettings["tmdb_language"];
    } else {
        $rURL = "https://api.themoviedb.org/3/tv/" . $rTMDBID . "/season/" . $rSeason . "/episode/" . $rEpisode . "/images?api_key=" . $rSettings["tmdb_api_key"];
    }
    return json_decode(file_get_contents($rURL), true);
}
function getUserAgents()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `blocked_user_agents` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getISPs()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `isp_addon` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getBlockedIPs()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `blocked_ips` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getMagClaims()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `mag_claims` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getPanelLogs()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `panel_logs` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getBlockedLogins()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `login_flood` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getLeakedLines()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT FROM_BASE64(mac), username, user_activity.user_id, user_activity.container, user_activity.geoip_country_code, GROUP_CONCAT(DISTINCT user_ip), GROUP_CONCAT(DISTINCT container), GROUP_CONCAT(DISTINCT geoip_country_code), is_restreamer FROM user_activity\nINNER JOIN users ON user_id = users.id AND is_mag = 1\nINNER JOIN mag_devices ON users.id = mag_devices.user_id\nWHERE 1 GROUP BY user_id HAVING COUNT(DISTINCT user_ip) > 1\nAND\nis_restreamer < 1\nUNION\nSELECT '', username, user_activity.user_id, user_activity.container, user_activity.geoip_country_code, GROUP_CONCAT(DISTINCT user_ip), GROUP_CONCAT(DISTINCT container), GROUP_CONCAT(DISTINCT geoip_country_code), is_restreamer FROM user_activity\nINNER JOIN users ON user_id = users.id AND is_mag = 0\nWHERE 1 GROUP BY user_id HAVING COUNT(DISTINCT user_ip) > 1\nAND\nis_restreamer < 1;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getSecurityCenter()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT Distinct users.id, users.username, SUBSTR(`streams`.`stream_display_name`, 1, 30) stream_display_name, users.max_connections, (SELECT count(*) FROM `user_activity_now` WHERE `user_activity_now`.`stream_id` = `streams`.`id`) AS `active_connections`, (SELECT count(*) FROM `user_activity_now` WHERE `users`.`id` = `user_activity_now`.`user_id`) AS `total_active_connections` FROM user_activity_nowINNER JOIN `streams` ON `user_activity_now`.`stream_id` = `streams`.`id`LEFT JOIN users ON user_id = users.id WHERE (SELECT count(*) FROM `user_activity_now` WHERE `users`.`id` = `user_activity_now`.`user_id`) > `users`.`max_connections`ANDis_restreamer < 1;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getRTMPIPs()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `rtmp_ips` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getStream($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `streams` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getUser($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `users` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getRegisteredUser($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `reg_users` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getRegisteredUserHash($rHash)
{
    global $db;
    $result = $db->query("SELECT * FROM `reg_users` WHERE MD5(`username`) = '" . esc($rHash) . "' LIMIT 1;");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getEPG($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `epg` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getStreamOptions($rID)
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `streams_options` WHERE `stream_id` = " . intval($rID) . ";");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["argument_id"])] = $row;
        }
    }
    return $return;
}
function getStreamSys($rID)
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `streams_sys` WHERE `stream_id` = " . intval($rID) . ";");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["server_id"])] = $row;
        }
    }
    return $return;
}
function getRegisteredUsers($rOwner = NULL, $rIncludeSelf = true)
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `reg_users` ORDER BY `username` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            if (!$rOwner || $row["owner_id"] == $rOwner || $row["id"] == $rOwner && $rIncludeSelf) {
                $return[intval($row["id"])] = $row;
            }
        }
    }
    if (count($return) == 0) {
        $return[-1] = [];
    }
    return $return;
}
function hasPermissions($rType, $rID)
{
    global $rUserInfo;
    global $db;
    global $rPermissions;
    if ($rType == "user") {
        if (in_array(intval(getuser($rID)["member_id"]), array_keys(getregisteredusers($rUserInfo["id"])))) {
            return true;
        }
    } else {
        if ($rType == "pid") {
            $result = $db->query("SELECT `user_id` FROM `user_activity_now` WHERE `pid` = " . intval($rID) . ";");
            if ($result && 0 < $result->num_rows && in_array(intval(getuser($result->fetch_assoc()["user_id"])["member_id"]), array_keys(getregisteredusers($rUserInfo["id"])))) {
                return true;
            }
        } else {
            if ($rType == "reg_user") {
                if (in_array(intval($rID), array_keys(getregisteredusers($rUserInfo["id"]))) && intval($rID) != intval($rUserInfo["id"])) {
                    return true;
                }
            } else {
                if ($rType == "ticket") {
                    if (in_array(intval(getTicket($rID)["member_id"]), array_keys(getregisteredusers($rUserInfo["id"])))) {
                        return true;
                    }
                } else {
                    if ($rType == "mag") {
                        $result = $db->query("SELECT `user_id` FROM `mag_devices` WHERE `mag_id` = " . intval($rID) . ";");
                        if ($result && 0 < $result->num_rows && in_array(intval(getuser($result->fetch_assoc()["user_id"])["member_id"]), array_keys(getregisteredusers($rUserInfo["id"])))) {
                            return true;
                        }
                    } else {
                        if ($rType == "e2") {
                            $result = $db->query("SELECT `user_id` FROM `enigma2_devices` WHERE `device_id` = " . intval($rID) . ";");
                            if ($result && 0 < $result->num_rows && in_array(intval(getuser($result->fetch_assoc()["user_id"])["member_id"]), array_keys(getregisteredusers($rUserInfo["id"])))) {
                                return true;
                            }
                        } else {
                            if ($rType == "adv" && $rPermissions["is_admin"]) {
                                if (0 < count($rPermissions["advanced"]) && $rUserInfo["member_group_id"] != 1) {
                                    return in_array($rID, $rPermissions["advanced"]);
                                }
                                return true;
                            }
                        }
                    }
                }
            }
        }
    }
    return false;
}
function getMemberGroups()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `member_groups` ORDER BY `group_id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["group_id"])] = $row;
        }
    }
    return $return;
}
function getMemberGroup($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `member_groups` WHERE `group_id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getRegisteredUsernames()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT `id`, `username` FROM `reg_users` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row["username"];
        }
    }
    return $return;
}
function getOutputs($rUser = NULL)
{
    global $db;
    $return = [];
    if ($rUser) {
        $result = $db->query("SELECT `access_output_id` FROM `user_output` WHERE `user_id` = " . intval($rUser) . ";");
    } else {
        $result = $db->query("SELECT * FROM `access_output` ORDER BY `access_output_id` ASC;");
    }
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            if ($rUser) {
                $return[] = $row["access_output_id"];
            } else {
                $return[] = $row;
            }
        }
    }
    return $return;
}
function getUserBouquets()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT `id`, `bouquet` FROM `users` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}
function getBouquets()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `bouquets` ORDER BY `bouquet_order` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}
function getBouquetOrder()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `bouquets` ORDER BY `bouquet_order` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}
function getBouquet($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `bouquets` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getLanguages()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `languages` ORDER BY `key` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function addToBouquet($rType, $rBouquetID, $rID)
{
    global $db;
    $rBouquet = getbouquet($rBouquetID);
    if ($rBouquet) {
        if ($rType == "stream") {
            $rColumn = "bouquet_channels";
        } else {
            $rColumn = "bouquet_series";
        }
        $rChannels = json_decode($rBouquet[$rColumn], true);
        if (!in_array($rID, $rChannels)) {
            $rChannels[] = $rID;
            if (0 < count($rChannels)) {
                $db->query("UPDATE `bouquets` SET `" . esc($rColumn) . "` = '" . esc(json_encode(array_values($rChannels))) . "' WHERE `id` = " . intval($rBouquetID) . ";");
            }
        }
    }
}
function removeFromBouquet($rType, $rBouquetID, $rID)
{
    global $db;
    $rBouquet = getbouquet($rBouquetID);
    if ($rBouquet) {
        if ($rType == "stream") {
            $rColumn = "bouquet_channels";
        } else {
            $rColumn = "bouquet_series";
        }
        $rChannels = json_decode($rBouquet[$rColumn], true);
        if (($rKey = array_search($rID, $rChannels)) !== false) {
            unset($rChannels[$rKey]);
            $db->query("UPDATE `bouquets` SET `" . esc($rColumn) . "` = '" . esc(json_encode(array_values($rChannels))) . "' WHERE `id` = " . intval($rBouquetID) . ";");
        }
    }
}
function getPackages($rGroup = NULL)
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `packages` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            if (!isset($rGroup) || in_array(intval($rGroup), json_decode($row["groups"], true))) {
                $return[intval($row["id"])] = $row;
            }
        }
    }
    return $return;
}
function getPackage($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `packages` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getTranscodeProfile($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `transcoding_profiles` WHERE `profile_id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getUserAgent($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `blocked_user_agents` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getISP($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `isp_addon` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getBlockedIP($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `blocked_ips` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getRTMPIP($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `rtmp_ips` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getEPGs()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `epg` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}
function getCategories($rType = "live")
{
    global $db;
    $return = [];
    if ($rType) {
        $result = $db->query("SELECT * FROM `stream_categories` WHERE `category_type` = '" . esc($rType) . "' ORDER BY `cat_order` ASC;");
    } else {
        $result = $db->query("SELECT * FROM `stream_categories` ORDER BY `cat_order` ASC;");
    }
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}
function getChannels($rType = "live")
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `stream_categories` WHERE `category_type` = '" . esc($rType) . "' ORDER BY `cat_order` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}
function getChannelsByID($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `streams` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return false;
}
function getCategory($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `stream_categories` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return false;
}
function getStreamProviders()
{
    global $db;
    return $result = mysqli_query($db, "SELECT * FROM `streams_providers`");
}
function getStreamProvider($providerID)
{
    global $db;
    return $result = mysqli_query($db, "SELECT * FROM `streams_providers` WHERE  `provider_id` ='" . $providerID . "'");
}
function insertProviderDNS($providerName, $providerURL, $user, $pass)
{
    global $db;
    $db->query("INSERT INTO streams_providers(provider_name, provider_dns,username,password) VALUES ('" . $providerName . "','" . $providerURL . "','" . $user . "','" . $pass . "')");
}
function updateProviderDNS($providerID, $providerName, $providerURL)
{
    global $db;
    $db->query("UPDATE `streams_providers` SET  `provider_name` ='" . $providerName . "', `provider_dns`='" . $providerURL . "' WHERE `provider_id` ='" . $providerID . "'");
}
function deleteProviderDNS($providerID)
{
    global $db;
    if ($result = mysqli_query($db, "DELETE FROM `streams_providers` WHERE `provider_id` ='" . $providerID . "'")) {
        return true;
    }
    return false;
}
function getMag($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `mag_devices` WHERE `mag_id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $result = $db->query("SELECT `pair_id` FROM `users` WHERE `id` = " . intval($row["user_id"]) . ";");
        if ($result && $result->num_rows == 1) {
            $magrow = $result->fetch_assoc();
            $row["paired_user"] = $magrow["pair_id"];
            $row["username"] = getuser($row["paired_user"])["username"];
        }
        return $row;
    }
    return [];
}
function getEnigma($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `enigma2_devices` WHERE `device_id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $result = $db->query("SELECT `pair_id` FROM `users` WHERE `id` = " . intval($row["user_id"]) . ";");
        if ($result && $result->num_rows == 1) {
            $e2row = $result->fetch_assoc();
            $row["paired_user"] = $e2row["pair_id"];
            $row["username"] = getuser($row["paired_user"])["username"];
        }
        return $row;
    }
    return [];
}
function getMAGUser($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `mag_devices` WHERE `user_id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return "";
}
function getMAGLockDevice($rID)
{
    global $db;
    $result = $db->query("SELECT `lock_device` FROM `mag_devices` WHERE `user_id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc()["lock_device"];
    }
    return "";
}
function getE2User($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `enigma2_devices` WHERE `user_id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return "";
}
function getTicket($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `tickets` WHERE `id` = " . intval($rID) . ";");
    if ($result && 0 < $result->num_rows) {
        $row = $result->fetch_assoc();
        $row["replies"] = [];
        $row["title"] = htmlspecialchars($row["title"]);
        $result = $db->query("SELECT * FROM `tickets_replies` WHERE `ticket_id` = " . intval($rID) . " ORDER BY `date` ASC;");
        while ($reply = $result->fetch_assoc()) {
            $reply["message"] = htmlspecialchars($reply["message"]);
            if (strlen($reply["message"]) < 80) {
                $reply["message"] .= str_repeat("&nbsp; ", 80 - strlen($reply["message"]));
            }
            $row["replies"][] = $reply;
        }
        $row["user"] = getregistereduser($row["member_id"]);
        return $row;
    }
    return NULL;
}
function getExpiring($rID)
{
    global $db;
    $rAvailableMembers = array_keys(getregisteredusers($rID));
    $return = [];
    $result = $db->query("SELECT `id`, `member_id`, `username`, `password`, `exp_date` FROM `users` WHERE `member_id` IN (" . esc(join(",", $rAvailableMembers)) . ") AND `exp_date` >= UNIX_TIMESTAMP() ORDER BY `exp_date` ASC LIMIT 100;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}
function getTickets($rID = NULL)
{
    global $db;
    $return = [];
    if ($rID) {
        $result = $db->query("SELECT `tickets`.`id`, `tickets`.`member_id`, `tickets`.`title`, `tickets`.`status`, `tickets`.`admin_read`, `tickets`.`user_read`, `reg_users`.`username` FROM `tickets`, `reg_users` WHERE `member_id` = " . intval($rID) . " AND `reg_users`.`id` = `tickets`.`member_id` ORDER BY `id` DESC;");
    } else {
        $result = $db->query("SELECT `tickets`.`id`, `tickets`.`member_id`, `tickets`.`title`, `tickets`.`status`, `tickets`.`admin_read`, `tickets`.`user_read`, `reg_users`.`username` FROM `tickets`, `reg_users` WHERE `reg_users`.`id` = `tickets`.`member_id` ORDER BY `id` DESC;");
    }
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $dateresult = $db->query("SELECT MIN(`date`) AS `date` FROM `tickets_replies` WHERE `ticket_id` = " . intval($row["id"]) . " AND `admin_reply` = 0;");
            if ($rDate = $dateresult->fetch_assoc()["date"]) {
                $row["created"] = date("Y-m-d H:i", $rDate);
            } else {
                $row["created"] = "";
            }
            $dateresult = $db->query("SELECT MAX(`date`) AS `date` FROM `tickets_replies` WHERE `ticket_id` = " . intval($row["id"]) . " AND `admin_reply` = 1;");
            if ($rDate = $dateresult->fetch_assoc()["date"]) {
                $row["last_reply"] = date("Y-m-d H:i", $rDate);
            } else {
                $row["last_reply"] = "";
            }
            if ($row["status"] != 0) {
                if ($row["user_read"] == 0) {
                    $row["status"] = 2;
                }
                if ($row["admin_read"] == 1) {
                    $row["status"] = 3;
                }
            }
            $return[] = $row;
        }
    }
    return $return;
}
function checkTrials()
{
    global $db;
    global $rPermissions;
    global $rUserInfo;
    $rTotal = $rPermissions["total_allowed_gen_trials"];
    if (0 < $rTotal) {
        $rTotalIn = $rPermissions["total_allowed_gen_in"];
        if ($rTotalIn == "hours") {
            $rTime = time() - intval($rTotal) * 3600;
        } else {
            $rTime = time() - intval($rTotal) * 3600 * 24;
        }
        $result = $db->query("SELECT COUNT(`id`) AS `count` FROM `users` WHERE `member_id` = " . intval($rUserInfo["id"]) . " AND `created_at` >= " . $rTime . " AND `is_trial` = 1;");
        return $result->fetch_assoc()["count"] < $rTotal;
    }
    return false;
}
function cryptPassword($password, $salt = "xtreamcodes", $rounds = 20000)
{
    if ($salt == "") {
        $salt = substr(bin2hex(openssl_random_pseudo_bytes(16)), 0, 16);
    }
    $hash = crypt($password, sprintf("\$6\$rounds=%d\$%s\$", $rounds, $salt));
    return $hash;
}
function getIP()
{
    if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
    } else {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
        }
    }
    return $ip;
}
function getID()
{
    if (file_exists(MAIN_DIR . "adtools/settings.json")) {
        return json_decode(file_get_contents(MAIN_DIR . "adtools/settings.json"), true)["rid"];
    }
    return 0;
}
function getPermissions($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `member_groups` WHERE `group_id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function doLogin($rUsername, $rPassword)
{
    global $db;
    $result = $db->query("SELECT `id`, `username`, `password`, `member_group_id`, `google_2fa_sec`, `status` FROM `reg_users` WHERE `username` = '" . esc($rUsername) . "' LIMIT 1;");
    if ($result && $result->num_rows == 1) {
        $rRow = $result->fetch_assoc();
        if (cryptpassword($rPassword) == $rRow["password"]) {
            return $rRow;
        }
    }
}
function getSubresellerSetups()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT * FROM `subreseller_setup` ORDER BY `id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}
function getSubresellerSetup($rID)
{
    global $db;
    $result = $db->query("SELECT * FROM `subreseller_setup` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return NULL;
}
function getEpisodeParents()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT `series_episodes`.`stream_id`, `series`.`id`, `series`.`title` FROM `series_episodes` LEFT JOIN `series` ON `series`.`id` = `series_episodes`.`series_id`;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["stream_id"])] = $row;
        }
    }
    return $return;
}
function getSeriesList()
{
    global $db;
    $return = [];
    $result = $db->query("SELECT `id`, `title` FROM `series` ORDER BY `title` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}
function checkTable($rTable)
{
    global $db;
    $rTableQuery = ["subreseller_setup" => ["CREATE TABLE `subreseller_setup` (`id` int(11) NOT NULL AUTO_INCREMENT, `reseller` int(8) NOT NULL DEFAULT '0', `subreseller` int(8) NOT NULL DEFAULT '0', `status` int(1) NOT NULL DEFAULT '1', `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"], "admin_settings" => ["CREATE TABLE `admin_settings` (`type` varchar(128) NOT NULL DEFAULT '', `value` varchar(4096) NOT NULL DEFAULT '', PRIMARY KEY (`type`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"], "watch_folders" => ["CREATE TABLE `watch_folders` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` varchar(32) NOT NULL DEFAULT '', `directory` varchar(2048) NOT NULL DEFAULT '', `server_id` int(8) NOT NULL DEFAULT '0', `category_id` int(8) NOT NULL DEFAULT '0', `bouquets` varchar(4096) NOT NULL DEFAULT '[]', `last_run` int(32) NOT NULL DEFAULT '0', `active` int(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"], "tmdb_async" => ["CREATE TABLE `tmdb_async` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` int(1) NOT NULL DEFAULT '0', `stream_id` int(16) NOT NULL DEFAULT '0', `status` int(8) NOT NULL DEFAULT '0', `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"], "watch_settings" => ["CREATE TABLE `watch_settings` (`read_native` int(1) NOT NULL DEFAULT '1', `movie_symlink` int(1) NOT NULL DEFAULT '1', `auto_encode` int(1) NOT NULL DEFAULT '0', `transcode_profile_id` int(8) NOT NULL DEFAULT '0', `scan_seconds` int(8) NOT NULL DEFAULT '3600') ENGINE=InnoDB DEFAULT CHARSET=latin1;", "INSERT INTO `watch_settings` (`read_native`, `movie_symlink`, `auto_encode`, `transcode_profile_id`, `scan_seconds`) VALUES(1, 1, 0, 0, 3600);"], "watch_categories" => ["CREATE TABLE `watch_categories` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` int(1) NOT NULL DEFAULT '0', `genre_id` int(8) NOT NULL DEFAULT '0', `genre` varchar(64) NOT NULL DEFAULT '', `category_id` int(8) NOT NULL DEFAULT '0', `bouquets` varchar(4096) NOT NULL DEFAULT '[]', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1", "INSERT INTO `watch_categories` (`id`, `type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES (1, 1, 28, 'Action', 0, '[]'), (2, 1, 12, 'Adventure', 0, '[]'), (3, 1, 16, 'Animation', 0, '[]'), (4, 1, 35, 'Comedy', 0, '[]'), (5, 1, 80, 'Crime', 0, '[]'), (6, 1, 99, 'Documentary', 0, '[]'), (7, 1, 18, 'Drama', 0, '[]'), (8, 1, 10751, 'Family', 0, '[]'), (9, 1, 14, 'Fantasy', 0, '[]'), (10, 1, 36, 'History', 0, '[]'), (11, 1, 27, 'Horror', 0, '[]'), (12, 1, 10402, 'Music', 0, '[]'), (13, 1, 9648, 'Mystery', 0, '[]'), (14, 1, 10749, 'Romance', 0, '[]'), (15, 1, 878, 'Science Fiction', 0, '[]'), (16, 1, 10770, 'TV Movie', 0, '[]'), (17, 1, 53, 'Thriller', 0, '[]'), (18, 1, 10752, 'War', 0, '[]'), (19, 1, 37, 'Western', 0, '[]');"], "watch_output" => ["CREATE TABLE `watch_output` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` int(1) NOT NULL DEFAULT '0', `server_id` int(8) NOT NULL DEFAULT '0', `filename` varchar(4096) NOT NULL DEFAULT '', `status` int(1) NOT NULL DEFAULT '0', `stream_id` int(8) NOT NULL DEFAULT '0', `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"], "login_flood" => ["CREATE TABLE `login_flood` (`id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(128) NOT NULL DEFAULT '', `ip` varchar(64) NOT NULL DEFAULT '', `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"], "languages" => ["CREATE TABLE `languages` (`key` varchar(128) NOT NULL DEFAULT '', `language` varchar(4096) NOT NULL DEFAULT '', PRIMARY KEY (`key`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;", "INSERT INTO `languages`(`key`, `language`) VALUES('en', 'English');"], "dashboard_statistics" => ["CREATE TABLE `dashboard_statistics` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` varchar(16) NOT NULL DEFAULT '', `time` INT(16) NOT NULL DEFAULT '0', `count` INT(16) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"], "panel_logs" => ["CREATE TABLE IF NOT EXISTS `panel_logs` (`id` int(11) NOT NULL AUTO_INCREMENT, `log_message` text NOT NULL, `date` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17221 ;"]];
    if (!$db->query("DESCRIBE `" . esc($rTable) . "`;") && isset($rTableQuery[$rTable])) {
        foreach ($rTableQuery[$rTable] as $rQuery) {
            $db->query($rQuery);
        }
    }
}
function secondsToTime($inputSeconds)
{
    $secondsInAMinute = 60;
    $secondsInAnHour = 60 * $secondsInAMinute;
    $secondsInADay = 24 * $secondsInAnHour;
    $days = floor($inputSeconds / $secondsInADay);
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);
    $obj = ["d" => (int) $days, "h" => (int) $hours, "m" => (int) $minutes, "s" => (int) $seconds];
    return $obj;
}
function getWorldMapLive()
{
    global $db;
    $rQuery = "SELECT geoip_country_code, count(geoip_country_code) AS total FROM user_activity_now GROUP BY geoip_country_code";
    if ($rResult = $db->query($rQuery)) {
        while ($row = $rResult->fetch_assoc()) {
            $WorldMapLive = "{\"code\":" . json_encode($row["geoip_country_code"]) . ",\"value\":" . json_encode($row["total"]) . "},";
            echo $WorldMapLive;
        }
    }
}
function getWorldMapActivity()
{
    global $db;
    $rQuery = "SELECT DISTINCT geoip_country_code, COUNT(DISTINCT user_id) AS total FROM user_activity GROUP BY geoip_country_code";
    if ($rResult = $db->query($rQuery)) {
        while ($row = $rResult->fetch_assoc()) {
            $WorldMapActivity = "{\"code\":" . json_encode($row["geoip_country_code"]) . ",\"value\":" . json_encode($row["total"]) . "},";
            echo $WorldMapActivity;
        }
    }
}
function getWorldMapTotalActivity()
{
    global $db;
    $rQuery = "SELECT geoip_country_code, count(geoip_country_code) AS total FROM user_activity GROUP BY geoip_country_code";
    if ($rResult = $db->query($rQuery)) {
        while ($row = $rResult->fetch_assoc()) {
            $WorldMapTotalActivity = "{\"code\":" . json_encode($row["geoip_country_code"]) . ",\"value\":" . json_encode($row["total"]) . "},";
            echo $WorldMapTotalActivity;
        }
    }
}
function writeAdminSettings()
{
    global $rAdminSettings;
    global $db;
    foreach ($rAdminSettings as $rKey => $rValue) {
        if (0 < strlen($rKey)) {
            $db->query("REPLACE INTO `admin_settings`(`type`, `value`) VALUES('" . esc($rKey) . "', '" . esc($rValue) . "');");
        }
    }
}
function downloadImage($rImage)
{
    if (0 < strlen($rImage) && substr(strtolower($rImage), 0, 4) == "http") {
        $rPathInfo = pathinfo($rImage);
        $rExt = $rPathInfo["extension"];
        if (in_array(strtolower($rExt), ["jpg", "jpeg", "png"])) {
            $rPrevPath = MAIN_DIR . "wwwdir/images/" . $rPathInfo["filename"] . "." . $rExt;
            if (file_exists($rPrevPath)) {
                return getURL() . "/images/" . $rPathInfo["filename"] . "." . $rExt;
            }
            $rCont = stream_context_create(["http" => ["timeout" => 10, "method" => "GET"]]);
            $rData = file_get_contents($rImage, false, $rCont);
            if (0 < strlen($rData)) {
                $rFilename = md5($rPathInfo["filename"]);
                $rPath = MAIN_DIR . "wwwdir/images/" . $rFilename . "." . $rExt;
                file_put_contents($rPath, $rData);
                if (strlen(file_get_contents($rPath)) == strlen($rData)) {
                    return getURL() . "/images/" . $rFilename . "." . $rExt;
                }
            }
        }
    }
    return $rImage;
}
function updateSeries($rID)
{
    global $db;
    global $rSettings;
    global $rAdminSettings;
    require_once "tmdb.php";
    $result = $db->query("SELECT `tmdb_id` FROM `series` WHERE `id` = " . intval($rID) . ";");
    if ($result && $result->num_rows == 1) {
        $rTMDBID = $result->fetch_assoc()["tmdb_id"];
        if (0 < strlen($rTMDBID)) {
            if (0 < strlen($rAdminSettings["tmdb_language"])) {
                $rTMDB = new TMDB($rSettings["tmdb_api_key"], $rAdminSettings["tmdb_language"]);
            } else {
                $rTMDB = new TMDB($rSettings["tmdb_api_key"]);
            }
            $rReturn = [];
            $rSeasons = json_decode($rTMDB->getTVShow($rTMDBID)->getJSON(), true)["seasons"];
            foreach ($rSeasons as $rSeason) {
                if ($rAdminSettings["download_images"]) {
                    $rSeason["cover"] = downloadimage("https://image.tmdb.org/t/p/w600_and_h900_bestv2" . $rSeason["poster_path"]);
                } else {
                    $rSeason["cover"] = "https://image.tmdb.org/t/p/w600_and_h900_bestv2" . $rSeason["poster_path"];
                }
                $rSeason["cover_big"] = $rSeason["cover"];
                unset($rSeason["poster_path"]);
                $rReturn[] = $rSeason;
            }
            $db->query("UPDATE `series` SET `seasons` = '" . esc(json_encode($rReturn)) . "', `last_modified` = " . intval(time()) . " WHERE `id` = " . intval($rID) . ";");
        }
    }
}
function getFooter()
{
    global $rAdminSettings;
    global $rPermissions;
    global $rSettings;
    global $rRelease;
    global $rEarlyAccess;
    global $_;
    if ($rPermissions["is_admin"]) {
        if ($rEarlyAccess) {
            return $_["copyright"] . " &copy; " . date("Y") . " - " . $rSettings["server_name"] . $rEarlyAccess . $rAdminSettings["panel_version"];
        }
    } else {
        return $rSettings["copyrights_text"];
    }
}
function getURL()
{
    global $rServers;
    global $_INFO;
    if (0 < strlen($rServers[$_INFO["server_id"]]["domain_name"])) {
        return "http://" . $rServers[$_INFO["server_id"]]["domain_name"] . ":" . $rServers[$_INFO["server_id"]]["http_broadcast_port"];
    }
    if (0 < strlen($rServers[$_INFO["server_id"]]["vpn_ip"])) {
        return "http://" . $rServers[$_INFO["server_id"]]["vpn_ip"] . ":" . $rServers[$_INFO["server_id"]]["http_broadcast_port"];
    }
    return "http://" . $rServers[$_INFO["server_id"]]["server_ip"] . ":" . $rServers[$_INFO["server_id"]]["http_broadcast_port"];
}
function scanBouquets()
{
    global $db;
    $rStreamIDs = [[], []];
    $result = $db->query("SELECT `id` FROM `streams`;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $rStreamIDs[0][] = intval($row["id"]);
        }
    }
    $result = $db->query("SELECT `id` FROM `series`;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $rStreamIDs[1][] = intval($row["id"]);
        }
    }
    foreach (getbouquets() as $rID => $rBouquet) {
        $rUpdate = [[], []];
        foreach (json_decode($rBouquet["bouquet_channels"], true) as $rID) {
            if (in_array(intval($rID), $rStreamIDs[0])) {
                $rUpdate[0][] = intval($rID);
            }
        }
        foreach (json_decode($rBouquet["bouquet_series"], true) as $rID) {
            if (in_array(intval($rID), $rStreamIDs[1])) {
                $rUpdate[1][] = intval($rID);
            }
        }
        $db->query("UPDATE `bouquets` SET `bouquet_channels` = '" . esc(json_encode($rUpdate[0])) . "', `bouquet_series` = '" . esc(json_encode($rUpdate[1])) . "' WHERE `id` = " . intval($rBouquet["id"]) . ";");
    }
}
function scanBouquet($rID)
{
    global $db;
    $rBouquet = getbouquet($rID);
    if ($rBouquet) {
        $rStreamIDs = [];
        $result = $db->query("SELECT `id` FROM `streams`;");
        if ($result && 0 < $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $rStreamIDs[0][] = intval($row["id"]);
            }
        }
        $result = $db->query("SELECT `id` FROM `series`;");
        if ($result && 0 < $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $rStreamIDs[1][] = intval($row["id"]);
            }
        }
        $rUpdate = [[], []];
        foreach (json_decode($rBouquet["bouquet_channels"], true) as $rID) {
            if (in_array(intval($rID), $rStreamIDs[0])) {
                $rUpdate[0][] = intval($rID);
            }
        }
        foreach (json_decode($rBouquet["bouquet_series"], true) as $rID) {
            if (in_array(intval($rID), $rStreamIDs[1])) {
                $rUpdate[1][] = intval($rID);
            }
        }
        $db->query("UPDATE `bouquets` SET `bouquet_channels` = '" . esc(json_encode($rUpdate[0])) . "', `bouquet_series` = '" . esc(json_encode($rUpdate[1])) . "' WHERE `id` = " . intval($rBouquet["id"]) . ";");
    }
}
function getNextOrder()
{
    global $db;
    $result = $db->query("SELECT MAX(`order`) AS `order` FROM `streams`;");
    if ($result && $result->num_rows == 1) {
        return intval($result->fetch_assoc()["order"]) + 1;
    }
    return 0;
}
function generateSeriesPlaylist($rSeriesNo)
{
    global $db;
    global $rServers;
    global $rSettings;
    $rReturn = ["success" => false, "sources" => [], "server_id" => 0];
    $result = $db->query("SELECT `stream_id` FROM `series_episodes` WHERE `series_id` = " . intval($rSeriesNo) . " ORDER BY `season_num` ASC, `sort` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $resultB = $db->query("SELECT `stream_source` FROM `streams` WHERE `id` = " . intval($row["stream_id"]) . ";");
            if ($resultB && 0 < $resultB->num_rows) {
                list($rSource) = json_decode($resultB->fetch_assoc()["stream_source"], true);
                $rSplit = explode(":", $rSource);
                $rFilename = join(":", array_slice($rSplit, 2, count($rSplit) - 2));
                $rServerID = intval($rSplit[1]);
                if ($rReturn["server_id"] == 0) {
                    $rReturn["server_id"] = $rServerID;
                    $rReturn["success"] = true;
                }
                if ($rReturn["server_id"] != $rServerID) {
                    $rReturn["success"] = false;
                } else {
                    $rReturn["sources"][] = $rFilename;
                }
            }
        }
    }
    return $rReturn;
}
function flushIPs()
{
    global $db;
    global $rServers;
    $rCommand = "sudo /sbin/iptables -P INPUT ACCEPT && sudo /sbin/iptables -P OUTPUT ACCEPT && sudo /sbin/iptables -P FORWARD ACCEPT && sudo /sbin/iptables -F";
    foreach ($rServers as $rServer) {
        sexec($rServer["id"], $rCommand);
    }
    $db->query("DELETE FROM `blocked_ips`;");
}
function flushLogins()
{
    global $db;
    $db->query("DELETE FROM `login_flood`;");
}
function flushActivity()
{
    global $db;
    $db->query("DELETE FROM `user_activity`;");
}
function flushActivitynow()
{
    global $db;
    $db->query("DELETE FROM `user_activity_now`;");
}
function flushPanelogs()
{
    global $db;
    $db->query("DELETE FROM `panel_logs`;");
}
function flushStlogs()
{
    global $db;
    $db->query("DELETE FROM `stream_logs`;");
}
function flushClientlogs()
{
    global $db;
    $db->query("DELETE FROM `client_logs`;");
}
function flushMagclaims()
{
    global $db;
    $db->query("DELETE FROM `mag_claims`;");
}
function flushMaglogs()
{
    global $db;
    $db->query("DELETE FROM `mag_logs`;");
}
function flushEvents()
{
    global $db;
    $db->query("DELETE FROM `mag_events`;");
}
function lockIsp()
{
    global $db;
    $db->query("UPDATE `users` SET `is_isplock` = 1;");
}
function unlockIsp()
{
    global $db;
    $db->query("UPDATE `users` SET `is_isplock` = 0;");
}
function lockStb()
{
    global $db;
    $db->query("UPDATE `mag_devices` SET `lock_device` = 1;");
}
function unlockStb()
{
    global $db;
    $db->query("UPDATE `mag_devices` SET `lock_device` = 0;");
}
function clearIsp()
{
    global $db;
    $db->query("UPDATE `users` SET `isp_desc` = NULL;");
}
function flushLoginlogs()
{
    global $db;
    $db->query("DELETE FROM `login_users`;");
}
function flushWatchFolder()
{
    global $db;
    $db->query("DELETE FROM `watch_output`;");
}
function flushTmdbAsync()
{
    global $db;
    $db->query("DELETE FROM `tmdb_async`;");
}
function updateTables()
{
    global $db;
    checktable("tmdb_async");
    checktable("subreseller_setup");
    checktable("admin_settings");
    checktable("watch_folders");
    checktable("watch_settings");
    checktable("watch_categories");
    checktable("watch_output");
    checktable("login_flood");
    checktable("panel_logs");
    checktable("settings");
    $rResult = $db->query("SHOW COLUMNS FROM `watch_folders` LIKE 'bouquets';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `category_id` int(8) NOT NULL DEFAULT '0';");
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `bouquets` varchar(4096) NOT NULL DEFAULT '[]';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `watch_settings` LIKE 'percentage_match';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `watch_settings` ADD COLUMN `percentage_match` int(3) NOT NULL DEFAULT '70';");
        $db->query("ALTER TABLE `watch_settings` ADD COLUMN `ffprobe_input` int(1) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `watch_folders` LIKE 'disable_tmdb';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `disable_tmdb` int(1) NOT NULL DEFAULT '0';");
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `ignore_no_match` int(1) NOT NULL DEFAULT '0';");
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `auto_subtitles` int(1) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `watch_folders` LIKE 'fb_bouquets';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `fb_bouquets` VARCHAR(4096) NOT NULL DEFAULT '[]';");
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `fb_category_id` int(8) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `watch_folders` LIKE 'allowed_extensions';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `allowed_extensions` VARCHAR(4096) NOT NULL DEFAULT '[]';");
    }
    $db->query("UPDATE `streams_arguments` SET `argument_cmd` = '-cookies \\'%s\\'' WHERE `id` = 17;");
    $db->query("INSERT IGNORE INTO `streams_arguments` VALUES (19, 'fetch', 'Headers', 'Set Custom Headers', 'http', 'headers', '-headers \"%s\"', 'text', NULL);");
    $rResult = $db->query("SHOW COLUMNS FROM `reg_users` LIKE 'dark_mode';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `reg_users` ADD COLUMN `dark_mode` int(1) NOT NULL DEFAULT '0';");
        $db->query("ALTER TABLE `reg_users` ADD COLUMN `sidebar` int(1) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `member_groups` LIKE 'minimum_trial_credits';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `member_groups` ADD COLUMN `minimum_trial_credits` int(16) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `bouquets` LIKE 'bouquet_order';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `bouquets` ADD COLUMN `bouquet_order` int(16) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `reg_users` LIKE 'expanded_sidebar';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `reg_users` ADD COLUMN `expanded_sidebar` int(1) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SELECT * FROM `admin_settings` WHERE `type` = 'auto_refresh';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("INSERT INTO `admin_settings`(`type`, `value`) VALUES('auto_refresh', 1);");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `settings` LIKE 'logo_url_sidebar';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `settings` ADD COLUMN `logo_url_sidebar` mediumtext NOT NULL DEFAULT '';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `settings` LIKE 'page_mannuals';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `settings` ADD COLUMN `page_mannuals` mediumtext NOT NULL DEFAULT '';");
    }
    $rResult = $db->query("SELECT * FROM `admin_settings` WHERE `type` = 'active_mannuals';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("INSERT INTO `admin_settings`(`type`, `value`) VALUES('active_mannuals', 1);");
    }
    $rResult = $db->query("SELECT * FROM `admin_settings` WHERE `type` = 'reseller_can_isplock';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("INSERT INTO `admin_settings`(`type`, `value`) VALUES('reseller_can_isplock', 1);");
    }
    $rResult = $db->query("SELECT * FROM `admin_settings` WHERE `type` = 'reseller_reset_isplock';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("INSERT INTO `admin_settings`(`type`, `value`) VALUES('reseller_reset_isplock', 1);");
    }
    $rResult = $db->query("SELECT * FROM `admin_settings` WHERE `type` = 'show_tickets';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("INSERT INTO `admin_settings`(`type`, `value`) VALUES('show_tickets', 1);");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `member_groups` LIKE 'reseller_can_select_bouquets';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `member_groups` ADD COLUMN `reseller_can_select_bouquets` int(16) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `streaming_servers` LIKE 'http_isp_port';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `streaming_servers` ADD COLUMN `http_isp_port` int(11) NOT NULL DEFAULT '8805';");
    }
    $rResult = $db->query("SELECT * FROM `admin_settings` WHERE `type` = 'panel_version';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("INSERT INTO `admin_settings`(`type`, `value`) VALUES('panel_version', 01);");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `settings` LIKE 'sucessedit';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `settings` ADD COLUMN `sucessedit` tinyint(4) NOT NULL DEFAULT '1';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `streaming_servers` LIKE 'enable_duplex';");
    if ($rResult && $rResult->num_rows == 0) {
        $db->query("ALTER TABLE `streaming_servers` ADD COLUMN `enable_duplex` int(11) NOT NULL DEFAULT '0';");
    }
    $db->query("DROP TABLE IF EXISTS `login_userlogs`");
    $db->query("CREATE TABLE IF NOT EXISTS `login_users` (`id` int(11) NOT NULL AUTO_INCREMENT, `owner` int(11) NOT NULL, `date` int(30) NOT NULL, `login_ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
    $db->query("CREATE TABLE IF NOT EXISTS `streams_providers` (`provider_id` int(11) NOT NULL AUTO_INCREMENT, `provider_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `provider_dns` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL, `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`provider_id`), KEY `provider_name` (`provider_name`), KEY `provider_dns` (`provider_dns`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
    updateTMDbCategories();
}
function updateTMDbCategories()
{
    global $db;
    global $rAdminSettings;
    global $rSettings;
    include "tmdb.php";
    if (0 < strlen($rAdminSettings["tmdb_language"])) {
        $rTMDB = new TMDB($rSettings["tmdb_api_key"], $rAdminSettings["tmdb_language"]);
    } else {
        $rTMDB = new TMDB($rSettings["tmdb_api_key"]);
    }
    $rCurrentCats = ["1" => [], "2" => []];
    $rResult = $db->query("SELECT `id`, `type`, `genre_id` FROM `watch_categories`;");
    if ($rResult && 0 < $rResult->num_rows) {
        while ($rRow = $rResult->fetch_assoc()) {
            if (in_array($rRow["genre_id"], $rCurrentCats[$rRow["type"]])) {
                $db->query("DELETE FROM `watch_categories` WHERE `id` = " . intval($rRow["id"]) . ";");
            }
            $rCurrentCats[$rRow["type"]][] = $rRow["genre_id"];
        }
    }
    $rMovieGenres = $rTMDB->getMovieGenres();
    foreach ($rMovieGenres as $rMovieGenre) {
        if (!in_array($rMovieGenre->getID(), $rCurrentCats[1])) {
            $db->query("INSERT INTO `watch_categories`(`type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES(1, " . intval($rMovieGenre->getID()) . ", '" . esc($rMovieGenre->getName()) . "', 0, '[]');");
        }
        if (!in_array($rMovieGenre->getID(), $rCurrentCats[2])) {
            $db->query("INSERT INTO `watch_categories`(`type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES(2, " . intval($rMovieGenre->getID()) . ", '" . esc($rMovieGenre->getName()) . "', 0, '[]');");
        }
    }
    $rTVGenres = $rTMDB->getTVGenres();
    foreach ($rTVGenres as $rTVGenre) {
        if (!in_array($rTVGenre->getID(), $rCurrentCats[1])) {
            $db->query("INSERT INTO `watch_categories`(`type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES(1, " . intval($rTVGenre->getID()) . ", '" . esc($rTVGenre->getName()) . "', 0, '[]');");
        }
        if (!in_array($rTVGenre->getID(), $rCurrentCats[2])) {
            $db->query("INSERT INTO `watch_categories`(`type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES(2, " . intval($rTVGenre->getID()) . ", '" . esc($rTVGenre->getName()) . "', 0, '[]');");
        }
    }
}
function forceSecurity()
{
    global $db;
    $db->query("UPDATE `settings` SET `double_auth` = 1, `mag_security` = 1;");
    $db->query("UPDATE `admin_settings` SET `pass_length` = 8 WHERE `pass_length` < 8;");
}
function LastCheckNew($valor, $CheckType)
{
    $fp = fopen($CheckType, "w");
    fwrite($fp, $valor);
    fclose($fp);
}
function LastCheck($CheckType)
{
    $fp = fopen($CheckType, "r");
    $conteudo = fread($fp, filesize($CheckType));
    fclose($fp);
    return $conteudo;
}
function create_google_file($file_name)
{
    global $db;
    global $access_token;
    global $folder_id;
    global $mime_type;
    if ($folder_id == "") {
        $post_fields = "{\"title\":\"" . $file_name . "\",\n\t\t                     \"mimeType\":\"" . $mime_type . "\"}";
    } else {
        $post_fields = "{\"title\":\"" . $file_name . "\",\n\t\t                     \"mimeType\":\"" . $mime_type . "\",\n\t\t                     \"parents\": [{\"kind\":\"drive#fileLink\",\"id\":\"" . $folder_id . "\"}]}";
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/upload/drive/v2/files?uploadType=resumable");
    curl_setopt($ch, CURLOPT_PORT, 443);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $access_token, "Content-Length: " . strlen($post_fields), "X-Upload-Content-Type: " . $mime_type, "X-Upload-Content-Length: " . filesize($file_name), "Content-Type: application/json; charset=UTF-8"]);
    $response = curl_exec($ch);
    $response = parse_response($response);
    if (is_null($response)) {
        return NULL;
    }
    if ($response["code"] == "401") {
        $access_token = get_access_token(true);
        return create_google_file($file_name);
    }
    if ($response["code"] != "200") {
        $message = "GoogleDrive ERROR: could not create resumable file\n";
        $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . esc($message) . "', " . intval(time()) . ");");
    } else {
        if (!isset($response["headers"]["location"])) {
            $message = "GoogleDrive ERROR: not location header gotten back\n";
            $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . esc($message) . "', " . intval(time()) . ");");
        } else {
            return $response["headers"]["location"];
        }
    }
}
function get_access_token($force_refresh = false)
{
    global $db;
    global $client_id;
    global $client_secret;
    global $refresh_token;
    global $verbose;
    if ($verbose) {
        echo "> retrieving access token\n";
    }
    $token_filename = "/tmp/access_token_" . md5($client_id . $client_secret . $refresh_token);
    $access_token = "";
    if (!file_exists($token_filename) || $force_refresh === true) {
        if ($verbose) {
            echo ">   getting new one\n";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://accounts.google.com/o/oauth2/token");
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=" . $client_id . "&client_secret=" . $client_secret . "&refresh_token=" . $refresh_token . "&grant_type=refresh_token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
        $response = curl_exec($ch);
        $response = parse_response($response);
        if (is_null($response)) {
            return NULL;
        }
        $access_token = json_decode($response["body"]);
        $access_token = $access_token->access_token;
        file_put_contents($token_filename, $access_token);
    } else {
        $access_token = file_get_contents($token_filename);
        if ($verbose) {
            echo ">   from cache\n";
        }
    }
    if ($access_token == "") {
        $message = "GoogleDrive ERROR: problems getting an access token\n";
        $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . esc($message) . "', " . intval(time()) . ");");
    } else {
        return $access_token;
    }
}
function parse_response($raw_data)
{
    global $db;
    $parsed_response = ["code" => -1, "headers" => [], "body" => ""];
    $raw_data = explode("\r\n", $raw_data);
    $parsed_response["code"] = explode(" ", $raw_data[0]);
    $parsed_response["code"] = $parsed_response["code"][1];
    $i = 1;
    while ($i < count($raw_data)) {
        $raw_datum = $raw_data[$i];
        $raw_datum = trim($raw_datum);
        if ($raw_datum != "") {
            if (1 <= substr_count($raw_datum, ":")) {
                $raw_datum = explode(":", $raw_datum, 2);
                $parsed_response["headers"][strtolower($raw_datum[0])] = trim($raw_datum[1]);
                $i++;
            } else {
                $message = "Google Drive ERROR: we're in the headers section of parsing an HTTP section and no colon was found for line: " . $raw_datum . "\n";
                $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . esc($message) . "', " . intval(time()) . ");");
                return NULL;
            }
        } else {
            if ($i + 1 < count($raw_data)) {
                for ($j = $i + 1; $j < count($raw_data); $j++) {
                    $parsed_response["body"] .= $raw_data[$j] . "\n";
                }
            }
        }
    }
    return $parsed_response;
}
function get_mime_type($file_name)
{
    global $file_binary;
    $result = exec($file_binary . " -i -b " . $file_name);
    $result = trim($result);
    $result = explode(";", $result);
    $result = $result[0];
    return $result;
}

?>