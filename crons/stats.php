<?php
include "/home/xtreamcodes/iptv_xtream_codes/admin/functions.php";

$rPID = getmypid();
if (isset($rAdminSettings["stats_pid"])) {
    if ((file_exists("/proc/".$rAdminSettings["stats_pid"])) && (strlen($rAdminSettings["stats_pid"]) > 0)) {
        exit;
    } else {
        $db->query("UPDATE `admin_settings` SET `value` = ".intval($rPID)." WHERE `type` = 'stats_pid';");
    }
} else {
    $db->query("INSERT INTO `admin_settings`(`type`, `value`) VALUES('stats_pid', ".intval($rPID).");");
}

checkTable("dashboard_statistics");
$rAdminSettings = getAdminSettings();
$rSettings = getSettings();

$rTimeout = 3000;       // Limit by time.
set_time_limit($rTimeout);
ini_set('max_execution_time', $rTimeout);

$rStatistics = Array("users" => Array(), "conns" => Array());
$rPeriod = intval($rAdminSettings["dashboard_stats_frequency"]) ?: 600;

if (($rPeriod >= 60) && ($rAdminSettings["dashboard_stats"])) {
	$rResult = $db->query("SELECT MIN(`date_start`) AS `min` FROM `user_activity`;");
	$rMin = roundUpToAny(intval($rResult->fetch_assoc()["min"]), $rPeriod);
	$rResult = $db->query("SELECT MAX(`time`) AS `max` FROM `dashboard_statistics` WHERE `type` IN ('users', 'conns');");
	$rMinProc = roundUpToAny(intval($rResult->fetch_assoc()["max"]), $rPeriod);
	if ($rMinProc > $rMin) {
		$rMin = $rMinProc - ($rPeriod * 3);
	}
	$rRange = range($rMin, roundUpToAny(time(), $rPeriod), $rPeriod);
	foreach ($rRange as $rDate) {
		$rCount = 0;
		$rResult = $db->query("SELECT COUNT(`activity_id`) AS `count` FROM `user_activity` WHERE `date_start` <= ".intval($rDate)." AND `date_end` >= ".intval($rDate).";");
		$rCount += $rResult->fetch_assoc()["count"];
		$rResult = $db->query("SELECT COUNT(`activity_id`) AS `count` FROM `user_activity_now` WHERE `date_start` <= ".intval($rDate).";");
		$rCount += $rResult->fetch_assoc()["count"];
		$rStatistics["conns"][] = Array(intval($rDate), $rCount);
		$rCount = 0;
		$rResult = $db->query("SELECT COUNT(DISTINCT(`activity_id`)) AS `count` FROM `user_activity` WHERE `date_start` <= ".intval($rDate)." AND `date_end` >= ".intval($rDate).";");
		$rCount += $rResult->fetch_assoc()["count"];
		$rResult = $db->query("SELECT COUNT(DISTINCT(`activity_id`)) AS `count` FROM `user_activity_now` WHERE `date_start` <= ".intval($rDate).";");
		$rCount += $rResult->fetch_assoc()["count"];
		$rStatistics["users"][] = Array(intval($rDate), $rCount);
	}
	$db->query("DELETE FROM `dashboard_statistics` WHERE `type` IN ('users', 'conns') AND `time` >= ".intval($rMin).";");
	foreach ($rStatistics as $rType => $rData) {
		foreach ($rData as $rValue) {
			$db->query("INSERT INTO `dashboard_statistics`(`type`, `time`, `count`) VALUES('".$db->real_escape_string($rType)."', ".intval($rValue[0]).", ".intval($rValue[1]).");");
		}
	}
}
?>