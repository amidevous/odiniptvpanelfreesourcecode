<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
$_SERVER["PROTOCOL"] = isset($_SERVER["HTTPS"]) && !empty($_SERVER["HTTPS"]) ? "https://" : "http://";
$protocol = $_SERVER["PROTOCOL"];
if ($protocol == "https://") {
    $portNumber = "https_broadcast_port";
}
if ($protocol == "http://") {
    $portNumber = "http_broadcast_port";
}
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "player")) {
    exit;
}
if (!isset($_GET["id"])) {
    exit;
}
echo "<html>\n\n    <script src=\"assets/js/vendor.min.js\"></script>\n    <script src=\"https://cdn.jsdelivr.net/npm/hls.js@latest\"></script>\n    <body>\n        <video id=\"video\" width=\"100%\" height=\"100%\" controls></video>\n    </body>\n    <script>\n    \$(document).ready(function() {\n        var video = document.getElementById('video');\n        ";
if ($_GET["type"] == "live") {
    echo "        if(Hls.isSupported()) {\n            var hls = new Hls();\n\n\n            hls.loadSource(\"";
    echo $protocol . ($rServers[$_INFO["server_id"]]["domain_name"] ? $rServers[$_INFO["server_id"]]["domain_name"] : $rServers[$_INFO["server_id"]]["server_ip"]);
    echo ":";
    echo $rServers[$_INFO["server_id"]][$portNumber];
    echo "/live/";
    echo $rAdminSettings["admin_username"];
    echo "/";
    echo $rAdminSettings["admin_password"];
    echo "/";
    echo $_GET["id"];
    echo ".m3u8\");\n            hls.attachMedia(video);\n            hls.on(Hls.Events.MANIFEST_PARSED,function() {\n                video.play();\n            });\n\n        }\n        ";
} else {
    if ($_GET["type"] == "movie") {
        echo "        video.src = \"";
        echo $protocol . ($rServers[$_INFO["server_id"]]["domain_name"] ? $rServers[$_INFO["server_id"]]["domain_name"] : $rServers[$_INFO["server_id"]]["server_ip"]);
        echo ":";
        echo $rServers[$_INFO["server_id"]][$portNumber];
        echo "/movie/";
        echo $rAdminSettings["admin_username"];
        echo "/";
        echo $rAdminSettings["admin_password"];
        echo "/";
        echo $_GET["id"];
        echo ".";
        echo $_GET["container"];
        echo "\";\n        ";
    } else {
        if ($_GET["type"] == "series") {
            echo "        video.src = \"";
            echo $protocol . ($rServers[$_INFO["server_id"]]["domain_name"] ? $rServers[$_INFO["server_id"]]["domain_name"] : $rServers[$_INFO["server_id"]]["server_ip"]);
            echo ":";
            echo $rServers[$_INFO["server_id"]][$portNumber];
            echo "/series/";
            echo $rAdminSettings["admin_username"];
            echo "/";
            echo $rAdminSettings["admin_password"];
            echo "/";
            echo $_GET["id"];
            echo ".";
            echo $_GET["container"];
            echo "\";\n        ";
        }
    }
}
echo "\n    });\n    </script>\n</html>\n";

?>