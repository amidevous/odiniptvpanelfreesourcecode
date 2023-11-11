<?php
// Xtream UI - Panel Update
define("MAIN_DIR", "/home/xtreamcodes/iptv_xtream_codes/");
define("CONFIG_CRYPT_KEY", "5709650b0d7806074842c6de575025b1");

function xor_parse($data, $key) {
    $i = 0;
    $output = '';
    foreach (str_split($data) as $char) {
	    $output.= chr(ord($char) ^ ord($key[$i++ % strlen($key)]));
    }
    return $output;
}

function getTimezone() {
    global $db;
    $result = $db->query("SELECT `default_timezone` FROM `settings`;");
    if ((isset($result)) && ($result->num_rows == 1)) {
        return $result->fetch_assoc()["default_timezone"];
    } else {
        return "Europe/London";
    }
}

$_INFO = json_decode(xor_parse(base64_decode(file_get_contents(MAIN_DIR . "config")), CONFIG_CRYPT_KEY), True);
if (!$db = new mysqli($_INFO["host"], $_INFO["db_user"], $_INFO["db_pass"], $_INFO["db_name"], $_INFO["db_port"])) { exit("No MySQL connection!"); } 
$db->set_charset("utf8");
date_default_timezone_set(getTimezone());

function getAdminSettings() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT `type`, `value` FROM `admin_settings`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["type"]] = $row["value"];
        }
    }
    return $return;
}

function writeAdminSettings($rAdminSettings) {
    global $db;
    foreach ($rAdminSettings as $rKey => $rValue) {
        if (strlen($rKey) > 0) {
            $db->query("REPLACE INTO `admin_settings`(`type`, `value`) VALUES('".$db->real_escape_string($rKey)."', '".$db->real_escape_string($rValue)."');");
        }
    }
}

$rAdminSettings = getAdminSettings();
$rDefaults = Array("auto_update" => false, "auto_update_periodicity" => 3600, "auto_update_check" => 0, "version" => 18, "git_url" => "xtreamui/XtreamUI");
foreach ($rDefaults as $rKey => $rValue) {
    if (!isset($rAdminSettings[$rKey])) {
        $rAdminSettings[$rKey] = $rValue;
    }
}

if (($rAdminSettings["auto_update"]) && (strlen($rAdminSettings["forum_username"]) > 0) && (strlen($rAdminSettings["forum_password"]) > 0)) {
    if (time() - $rAdminSettings["auto_update_check"] > $rAdminSettings["auto_update_periodicity"]) {
        $rAdminSettings["auto_update_check"] = time();
        $rUpdate = json_decode(file_get_contents("https://xtream-ui.com/install/version.json"), True);
        if (($rUpdate["version"]) && (intval($rUpdate["version"]) > intval($rAdminSettings["version"]))) {
            // New version available!
            $rUpdateScript = str_replace("##USERNAME##", $rAdminSettings["forum_username"], $rUpdate["update_script"]);
            $rUpdateScript = str_replace("##PASSWORD##", $rAdminSettings["forum_password"], $rUpdateScript);
            exec($rUpdateScript);
            // Set changes to settings here then save.
            foreach (Array("version") as $rItem) {
                $rAdminSettings[$rItem] = $rUpdate[$rItem];
            }
        }
        writeAdminSettings($rAdminSettings);
    }
}
?>