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
    $Ed756578679cd59095dfa81f228e8b38 = TMP_DIR . md5(aFfb052CCa396818d81004FF99dB49Aa() . __FILE__);
    bbd9E78aC32626E138e758e840305A7C($Ed756578679cd59095dfa81f228e8b38);
    $f566700a43ee8e1f0412fe10fbdf03df->query("SELECT COUNT(*) FROM `client_logs`");
    $ff865056c6ac33ff09270dc0abb8c21d = $f566700a43ee8e1f0412fe10fbdf03df->B98Ce8b3899e362093173Cc5Eb4146B9();
    $Fa6c74185d06efd37c067417bb9f8c59 = TMP_DIR . "client_request.log";
    $b0f1eb357ed72245e03dfe6268912497 = "";
    if (file_exists($Fa6c74185d06efd37c067417bb9f8c59)) {
        C91B44fb481658FaC7Ea786479A0BFe2($Fa6c74185d06efd37c067417bb9f8c59, $b0f1eb357ed72245e03dfe6268912497);
        unlink($Fa6c74185d06efd37c067417bb9f8c59);
    }
    $b0f1eb357ed72245e03dfe6268912497 = rtrim($b0f1eb357ed72245e03dfe6268912497, ",");
    if (!empty($b0f1eb357ed72245e03dfe6268912497)) {
        $f566700a43ee8e1f0412fe10fbdf03df->FC53e22ae7EE3BB881cD95Fb606914f0("INSERT INTO `client_logs` (`stream_id`,`user_id`,`client_status`,`query_string`,`user_agent`,`ip`,`extra_data`,`date`) VALUES " . $b0f1eb357ed72245e03dfe6268912497);
    }
} else {
    exit(0);
}
function c91B44fb481658FAc7eA786479a0bFE2($Ca434bcc380e9dbd2a3a588f6c32d84f, &$b0f1eb357ed72245e03dfe6268912497)
{
    if (file_exists($Ca434bcc380e9dbd2a3a588f6c32d84f)) {
        $Ab9f45b38498c3a010f3c4276ad5767c = fopen($Ca434bcc380e9dbd2a3a588f6c32d84f, "r");
        while (feof($Ab9f45b38498c3a010f3c4276ad5767c)) {
            $bb85be39ea05b75c9bffeff236bd9355 = trim(fgets($Ab9f45b38498c3a010f3c4276ad5767c));
            if (!empty($bb85be39ea05b75c9bffeff236bd9355)) {
                $bb85be39ea05b75c9bffeff236bd9355 = json_decode(base64_decode($bb85be39ea05b75c9bffeff236bd9355), true);
                $b0f1eb357ed72245e03dfe6268912497 .= "('" . $bb85be39ea05b75c9bffeff236bd9355["stream_id"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["user_id"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["action"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["query_string"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["user_agent"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["user_ip"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["extra_data"] . "','" . $bb85be39ea05b75c9bffeff236bd9355["time"] . "'),";
            }
        }
        fclose($Ab9f45b38498c3a010f3c4276ad5767c);
    }
    return $b0f1eb357ed72245e03dfe6268912497;
}

?>