<?php
include "/home/xtreamcodes/iptv_xtream_codes/admin/functions.php";
$clearLogs = false;

if (isset($rAdminSettings['clear_log_auto']) && !empty($rAdminSettings['clear_log_auto']) && isset($rAdminSettings['clear_log_older_than_days']) && !empty($rAdminSettings['clear_log_older_than_days']) && isset($rAdminSettings['clear_log_check']) && !empty($rAdminSettings['clear_log_check'])) {
	if(time() > ($rAdminSettings['clear_log_check'] + $rAdminSettings['clear_log_older_than_days'] * 86400)) {
		$clearLogs = true;
	}
}
if ($clearLogs) {
	$rTables = $rAdminSettings["clear_log_tables"];
	$rTables = json_decode($rTables, True);
	if(!empty($rTables))
	{
		foreach($rTables as $rTable)
		{
			call_user_func($rTable);
		}
		
		$rAdminSettings['clear_log_check'] = time();
		writeAdminSettings();
	}
}