<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

$rSessionTimeout = 60;
session_start();
if (isset($_SESSION["hash"]) && isset($_SESSION["last_activity"]) && $rSessionTimeout * 60 < time() - $_SESSION["last_activity"]) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION["last_activity"] = time();
if (!isset($_SESSION["hash"])) {
    header("Location: ./login.php?referrer=/" . basename($_SERVER["REQUEST_URI"]));
    exit;
}

?>