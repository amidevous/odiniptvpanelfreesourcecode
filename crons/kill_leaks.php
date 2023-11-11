<?php
include "/home/xtreamcodes/iptv_xtream_codes/admin/functions.php";

$result = $db->query("SELECT `server_id`, `pid` FROM `user_activity_now` WHERE `user_id` = 0;");
if (($result) && ($result->num_rows > 0)) {
    while ($row = $result->fetch_assoc()) {
        sexec($rRow["server_id"], "kill -9 ".$rRow["pid"]);
    }
}
?>