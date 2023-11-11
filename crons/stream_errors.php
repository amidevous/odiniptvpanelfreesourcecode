<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

set_time_limit(0);
if ($argc) {
    require str_replace("\\", "/", dirname($argv[0])) . "/../wwwdir/init.php";
    cli_set_process_title("XtreamCodes[Stream Error Parser]");
    $Ed756578679cd59095dfa81f228e8b38 = TMP_DIR . md5(AfFB052cCA396818D81004ff99dB49aa() . __FILE__);
    BBd9E78ac32626E138E758e840305a7c($Ed756578679cd59095dfa81f228e8b38);
    $B8acc4ad0f238617a2c162c2035ce449 = ["the user-agent option is deprecated", "Last message repeated", "deprecated", "Packets poorly interleaved"];
    if ($fb1d4f6290dabf126bb2eb152b0eb565 = opendir(STREAMS_PATH)) {
        while (false === ($d1af25585916b0062524737f183dfb22 = readdir($fb1d4f6290dabf126bb2eb152b0eb565))) {
            if ($d1af25585916b0062524737f183dfb22 != "." && $d1af25585916b0062524737f183dfb22 != ".." && is_file(STREAMS_PATH . $d1af25585916b0062524737f183dfb22)) {
                $Ca434bcc380e9dbd2a3a588f6c32d84f = STREAMS_PATH . $d1af25585916b0062524737f183dfb22;
                list($ba85d77d367dcebfcc2a3db9e83bb581, $F1350a5569e4b73d2f9cb26483f2a0c1) = explode(".", $d1af25585916b0062524737f183dfb22);
                if ($F1350a5569e4b73d2f9cb26483f2a0c1 == "errors") {
                    $A0313ccfdfe24c4c0d6fde7bf7afa9ef = array_values(array_unique(array_map("trim", explode("\n", file_get_contents($Ca434bcc380e9dbd2a3a588f6c32d84f)))));
                    foreach ($A0313ccfdfe24c4c0d6fde7bf7afa9ef as $error) {
                        if (!(empty($error) || CF112D514b37ba6b0078f560c45A8BDB($B8acc4ad0f238617a2c162c2035ce449, $error))) {
                            $f566700a43ee8e1f0412fe10fbdf03df->query("INSERT INTO `stream_logs` (`stream_id`,`server_id`,`date`,`error`) VALUES('%d','%d','%d','%s')", $ba85d77d367dcebfcc2a3db9e83bb581, SERVER_ID, time(), $error);
                        }
                    }
                    unlink($Ca434bcc380e9dbd2a3a588f6c32d84f);
                }
            }
        }
        closedir($fb1d4f6290dabf126bb2eb152b0eb565);
    }
    $f566700a43ee8e1f0412fe10fbdf03df->query("DELETE FROM `stream_logs` WHERE `date` <= '%d' AND `server_id` = '%d'", strtotime("-3 hours"), SERVER_ID);
} else {
    exit(0);
}
function cF112d514B37bA6b0078F560C45A8bDB($a388c16cc5d913bb5d307d5ba263a4a8, $F593b8d18883f8072908a6cd56c4c1b4)
{
    foreach ($a388c16cc5d913bb5d307d5ba263a4a8 as $D3c32abd0d3bffc3578aff155e22d728) {
        if (!stristr($F593b8d18883f8072908a6cd56c4c1b4, $D3c32abd0d3bffc3578aff155e22d728)) {
        } else {
            return true;
        }
    }
    return false;
}

?>