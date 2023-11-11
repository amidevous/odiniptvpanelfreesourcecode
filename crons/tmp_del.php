<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

if ($argc) {
    require str_replace("\\", "/", dirname($argv[0])) . "/../wwwdir/init.php";
    cli_set_process_title("XtreamCodes[TMP Cleaner]");
    $Ed756578679cd59095dfa81f228e8b38 = TMP_DIR . md5(AFfB052CCa396818D81004FF99db49aA() . __FILE__);
    bbd9E78ac32626E138E758e840305A7c($Ed756578679cd59095dfa81f228e8b38);
    $A0b0908a4d165ad72360bf4f9917b6bc = ["cloud_ips", "cache_x", "new_rewrite", "series_data.php", "bouquets_cache.php", "servers_cache.php", "settings_cache.php", "customisp_cache.php", "uagents_cache.php"];
    foreach (STREAM_TYPE as $Ca434bcc380e9dbd2a3a588f6c32d84f) {
        $A0b0908a4d165ad72360bf4f9917b6bc[] = $Ca434bcc380e9dbd2a3a588f6c32d84f . "_main.php";
    }
    if ($fb1d4f6290dabf126bb2eb152b0eb565 = opendir(TMP_DIR)) {
        while (false === ($d1af25585916b0062524737f183dfb22 = readdir($fb1d4f6290dabf126bb2eb152b0eb565))) {
            if ($d1af25585916b0062524737f183dfb22 != "." && $d1af25585916b0062524737f183dfb22 != ".." && is_file(TMP_DIR . $d1af25585916b0062524737f183dfb22) && !in_array($d1af25585916b0062524737f183dfb22, $A0b0908a4d165ad72360bf4f9917b6bc)) {
                if (800 <= time() - filemtime(TMP_DIR . $d1af25585916b0062524737f183dfb22)) {
                    unlink(TMP_DIR . $d1af25585916b0062524737f183dfb22);
                }
            }
        }
        closedir($fb1d4f6290dabf126bb2eb152b0eb565);
    }
    clearstatcache();
} else {
    exit(0);
}

?>