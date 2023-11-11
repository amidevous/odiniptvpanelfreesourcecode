<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "./functions.php";
if (!isset($_SESSION["hash"])) {
    exit;
}
if (isset($_GET["action"]) && $_GET["action"] == "user") {
    $rUserID = intval($_GET["user_id"]);
    if ($rPermissions["is_reseller"] && !hasPermissions("user", $rUserID)) {
        echo json_encode(["result" => false]);
        exit;
    }
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "edit_user")) {
        echo json_encode(["result" => false]);
        exit;
    }
    $rSub = $_GET["sub"];
    if ($rSub == "renew") {
        $rPeriode = $_GET["periode"] * 86400;
        $rResult = $db->query("SELECT `exp_date`, `username` FROM `users` WHERE `id` = " . intval($rUserID) . ";");
        if ($rResult && 0 < $rResult->num_rows) {
            $rRow = $rResult->fetch_assoc();
            $date_renew = $rRow[exp_date] + $rPeriode;
            $db->query("UPDATE `users` SET `exp_date` = " . intval($date_renew) . " WHERE `id` = " . intval($rUserID) . ";");
        }
        echo json_encode(["result" => true]);
        exit;
    }
    echo json_encode(["result" => false]);
    exit;
}
echo json_encode(["result" => false]);

?>