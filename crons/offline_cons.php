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
    cli_set_process_title("XtreamCodes[Offline Cons Parser]");
    $Ed756578679cd59095dfa81f228e8b38 = TMP_DIR . md5(afFB052CCa396818D81004fF99Db49aA() . __FILE__);
    BBd9E78AC32626E138e758E840305a7c($Ed756578679cd59095dfa81f228e8b38);
    $Fa6c74185d06efd37c067417bb9f8c59 = TMP_DIR . "offline_cons";
    $b0f1eb357ed72245e03dfe6268912497 = "";
    if (file_exists($Fa6c74185d06efd37c067417bb9f8c59)) {
        c91b44fb481658fac7ea786479a0bfe2($Fa6c74185d06efd37c067417bb9f8c59, $b0f1eb357ed72245e03dfe6268912497);
        unlink($Fa6c74185d06efd37c067417bb9f8c59);
    }
    $b0f1eb357ed72245e03dfe6268912497 = rtrim($b0f1eb357ed72245e03dfe6268912497, ",");
    if (!empty($b0f1eb357ed72245e03dfe6268912497)) {
        $f566700a43ee8e1f0412fe10fbdf03df->fC53e22AE7EE3bb881Cd95Fb606914f0("INSERT INTO `user_activity` (`server_id`,`user_id`,`isp`,`external_device`,`stream_id`,`date_start`,`user_agent`,`user_ip`,`date_end`,`container`,`geoip_country_code`) VALUES " . $b0f1eb357ed72245e03dfe6268912497);
    }
} else {
    exit(0);
}
function C91B44Fb481658FAC7ea786479A0BFE2($Ca434bcc380e9dbd2a3a588f6c32d84f, &$b0f1eb357ed72245e03dfe6268912497)
{
    global $f566700a43ee8e1f0412fe10fbdf03df;
    if (file_exists($Ca434bcc380e9dbd2a3a588f6c32d84f)) {
        $Ab9f45b38498c3a010f3c4276ad5767c = fopen($Ca434bcc380e9dbd2a3a588f6c32d84f, "r");
        while (feof($Ab9f45b38498c3a010f3c4276ad5767c)) {
            $bb85be39ea05b75c9bffeff236bd9355 = trim(fgets($Ab9f45b38498c3a010f3c4276ad5767c));
            if (!empty($bb85be39ea05b75c9bffeff236bd9355)) {
                $bb85be39ea05b75c9bffeff236bd9355 = json_decode(base64_decode($bb85be39ea05b75c9bffeff236bd9355), true);
                $bb85be39ea05b75c9bffeff236bd9355 = array_map([$f566700a43ee8e1f0412fe10fbdf03df, "escape"], $bb85be39ea05b75c9bffeff236bd9355);
                $b0f1eb357ed72245e03dfe6268912497 .= "(" . SERVER_ID . ",'" . $bb85be39ea05b75c9bffeff236bd9355["user_id"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["isp"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["external_device"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["stream_id"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["date_start"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["user_agent"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["user_ip"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["date_end"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["container"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["geoip_country_code"] . "'),";
            }
        }
        fclose($Ab9f45b38498c3a010f3c4276ad5767c);
    }
    return $b0f1eb357ed72245e03dfe6268912497;
}

?>