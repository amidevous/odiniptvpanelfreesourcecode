<?php
include "/home/xtreamcodes/iptv_xtream_codes/admin/functions.php";
$bDoBackup = false;

if (isset($rAdminSettings['automatic_backups']) && !empty($rAdminSettings['automatic_backups'])) {
    if ($rAdminSettings['automatic_backups'] == 'hourly' && $rAdminSettings['automatic_backups_check'] < (time() - 3600)) {
        $bDoBackup = true;
    } elseif ($rAdminSettings['automatic_backups'] == 'daily' && $rAdminSettings['automatic_backups_check'] < (time() - 86400)) {
        $bDoBackup = true;
    } elseif ($rAdminSettings['automatic_backups'] == 'weekly' && $rAdminSettings['automatic_backups_check'] < (time() - 604800)) {
        $bDoBackup = true;
    } elseif ($rAdminSettings['automatic_backups'] == 'monthly' && $rAdminSettings['automatic_backups_check'] < (time() - 2592000)) {
        $bDoBackup = true;
    }
}
if ($bDoBackup) {
    $rFilename = MAIN_DIR . "adtools/backups/backup_" . date("Y-m-d_H:i:s") . ".gz";
    $rCommand = "mysqldump -u " . $_INFO["db_user"] . " -p" . $_INFO["db_pass"] . " -P " . $_INFO["db_port"] . " " . $_INFO["db_name"] . " --ignore-table=xtream_iptvpro.user_activity --ignore-table=xtream_iptvpro.stream_logs --ignore-table=xtream_iptvpro.panel_logs --ignore-table=xtream_iptvpro.client_logs --ignore-table=xtream_iptvpro.epg_data | gzip > \"" . $rFilename . "\"";
    $rRet = shell_exec($rCommand);
    if (file_exists($rFilename)) {
        $rAdminSettings['automatic_backups_check'] = time();
        writeAdminSettings();
        $rBackups = getBackups();
        if ((count($rBackups) > intval($rAdminSettings["backups_to_keep"])) && (intval($rAdminSettings["backups_to_keep"]) > 0)) {
            $rDelete = array_slice($rBackups, 0, count($rBackups) - intval($rAdminSettings["backups_to_keep"]));
            foreach ($rDelete as $rItem) {
                if (file_exists(MAIN_DIR . "adtools/backups/" . $rItem["filename"])) {
                    unlink(MAIN_DIR . "adtools/backups/" . $rItem["filename"]);
                }
            }
        }
		if (isset($rAdminSettings['automatic_backups_gdrive']) && !empty($rAdminSettings['automatic_backups_gdrive'])) {
			$rUpload = '/home/xtreamcodes/iptv_xtream_codes/admin/gdrive.php '.$rFilename;
			$rExec = shell_exec($rUpload);
		}
    }
}
