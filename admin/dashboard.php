<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"]) {
    header("Location: ./reseller.php");
}
if ($rAdminSettings["dark_mode"]) {
    $rColours = ["1" => ["secondary", "#7e8e9d"], "2" => ["secondary", "#7e8e9d"], "3" => ["secondary", "#7e8e9d"], "4" => ["secondary", "#7e8e9d"]];
} else {
    $rColours = ["1" => ["purple", "#675db7"], "2" => ["success", "#23b397"], "3" => ["pink", "#e36498"], "4" => ["info", "#56C3D6"]];
}
$query = $db->query("SELECT * from users where is_mag = 1;");
$devicesmag = mysqli_num_rows($query);
$query = $db->query("SELECT * from users where is_e2 = 1;");
$enigma2 = $query->num_rows;
$query = $db->query("SELECT * from users where is_mag = 0;");
$smarttv = $query->num_rows;
$query = $db->query("SELECT * from streams where type = 1;");
$channels = $query->num_rows;
$query = $db->query("SELECT * from streams where type = 2;");
$vod = $query->num_rows;
$query = $db->query("SELECT * from series;");
$series = $query->num_rows;
$query = $db->query("SELECT * from bouquets;");
$bouquets = $query->num_rows;
$query = $db->query("SELECT * from streams where type = 4;");
$radio = $query->num_rows;
$query = $db->query("SELECT * from streaming_servers;");
$server = $query->num_rows;
$query = $db->query("SELECT * from streaming_servers where status = 2;");
$off_server = $query->num_rows;
$query = $db->query("SELECT * from reg_users;");
$reseller = $query->num_rows;
$query = $db->query("SELECT * from series_episodes;");
$episodes = $query->num_rows;
$query = $db->query("SELECT * from streams_providers;");
$name = $query->num_rows;
$query = $db->query("SELECT * from streams_providers2;");
$user = $query->num_rows;
$query = $db->query("SELECT * from streams_providers3;");
$lineid = $query->num_rows;
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper\"><div class=\"container-fluid\">\n        ";
}
echo "\t\t\t\t";
if (hasPermissions("adv", "index")) {
    echo "                <!-- start page title -->\n                <br>\n\t\t\t\t";
    if ($rServerError && $rPermissions["is_admin"] && hasPermissions("adv", "servers")) {
        foreach ($rServers as $rServer) {
            $show_offline = false;
            echo "\t\t\t\t";
            if (360 < time() - $rServer["last_check_ago"] && $rServer["can_delete"] == 1 && $rServer["status"] != 3) {
                $rServer["status"] = 2;
            }
            if ($rServer["status"] == 2) {
                $show_offline = true;
                if (0 < $rServer["last_check_ago"]) {
                    $rServerOff = $rServer["server_name"] . " offline for " . intval((time() - $rServer["last_check_ago"]) / 60) . " minutes";
                } else {
                    $rServerOff = $rServer["server_name"] . " Offline";
                }
            }
            echo "                    <div class=\"alert alert-dark alert-dismissible fade show\" role=\"alert\" ";
            if ($show_offline === false) {
                echo "style=\"display:none\"";
            }
            echo ">\n                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                            <span aria-hidden=\"true\">&times;</span>\n                        </button>\n                        <a href=\"./servers.php\">\n                            <i class=\"fas fa-square text-danger\"></i>\n                        </a>\n\t\t\t\t\t\t";
            echo $rServerOff;
            echo "                    </div>\n                ";
        }
    }
    echo "                <!-- end page title --> \n                <div class=\"tab-content\">\n                    <div class=\"tab-pane show active\" id=\"server-home\">\n                        <div class=\"row\">\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div class=\"col-6-md col-xl-3\">\n\t\t\t\t\t\t\t    <div class=\"card-bg cta-box online-users bg-info\">\n\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "live_connections")) {
        echo "\t\t\t\t\t\t\t\t<a href=\"./live_connections.php\">\n\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t    <div class=\"card bg-info\">\n\t\t\t\t\t\t\t\t\t    <div class=\"card-bg cta-box online-users bg-info\">\n\t\t\t\t\t\t\t\t\t        <div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between\">\n\t\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-info rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-users avatar-title bg-info font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-dark rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-users avatar-title bg-info font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md\" align=\"right\">\n\t\t\t\t\t\t\t\t\t\t\t\t<h3 class=\"text-white mb-1 bg-info\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"text-white mb-1 text-truncate\">";
    echo $_["online_users"];
    echo "</p>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\n                            <div class=\"col-6-md col-xl-3\">\n\t\t\t\t\t\t\t    <div class=\"card-bg cta-box active-connections bg-success\">\n\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "live_connections")) {
        echo "\t\t\t\t\t\t\t\t<a href=\"./live_connections.php\">\n\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t    <div class=\"card bg-success\">\n\t\t\t\t\t\t\t\t\t    <div class=\"card-bg cta-box active-connections bg-success\">\n\t\t\t\t\t\t\t\t\t        <div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between\">\n\t\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-success rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-box avatar-title bg-success font-24 text-white\"></i><br><br>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-success rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-box avatar-title bg-success font-24 text-white\"></i><br><br>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md\" align=\"right\">\n\t\t\t\t\t\t\t\t\t\t\t\t<h3 class=\"text-white mb-1 bg-success\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"text-white mb-1 text-truncate\">";
    echo $_["open_connections"];
    echo "</p>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\n                            <div class=\"col-6-md col-xl-3\">\n\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "live_connections")) {
        echo "\t\t\t\t\t\t\t\t<a href=\"./live_connections.php\">\n\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t<div class=\"card bg-pink\">\n\t\t\t\t\t\t\t\t\t<div class=\"card-bg cta-box input-flow bg-pink\">\n\t\t\t\t\t\t\t\t        <div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between\">\n\t\t\t\t\t\t\t\t\t\t    ";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t    <div class=\"avatar-md bg-pink rounded\">\n\t\t\t\t\t\t\t\t\t\t\t    <i class=\"fe-download avatar-title bg-pink font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t    </div>\n\t\t\t\t\t\t\t\t\t\t    ";
    } else {
        echo "\t\t\t\t\t\t\t\t\t\t    <div class=\"avatar-md bg-pink rounded\">\n\t\t\t\t\t\t\t\t\t\t\t    <i class=\"fe-download avatar-title bg-pink font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t    </div>\n\t\t\t\t\t\t\t\t\t\t    ";
    }
    echo "\t\t\t\t\t\t\t\t\t\t    <div class=\"col\" align=\"right\">\n\t\t\t\t\t\t\t\t\t\t\t    <h3 class=\"text-white my-1 bg-pink\"><span data-plugin=\"counterup\" class=\"entry\">0</span><small> Mbps</small></h3>\n\t\t\t\t\t\t\t\t\t\t\t    <p class=\"text-white my-1 text-truncate\">Total Input</p>\n\t\t\t\t\t\t\t\t\t\t    </div>\n\t\t\t\t\t\t\t\t\t    </div>\t\n\t\t\t\t\t\t\t\t\t\t<div class=\"card-bg cta-box output-flow bg-pink\">\n\t\t\t\t\t\t\t\t\t    <div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-pink rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-upload avatar-title bg-pink font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-dark rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-upload avatar-title bg-pink font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"col\" align=\"right\">\n\t\t\t\t\t\t\t\t\t\t\t\t<h3 class=\"text-white my-1 bg-pink\"><span data-plugin=\"counterup\" class=\"entry\">0</span><small> Mbps</small></h3>\n\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"text-white my-1 text-truncate\">Total Output</p>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<div class=\"col-6-md col-xl-3\">\n\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "live_connections")) {
        echo "\t\t\t\t\t\t\t\t<a href=\"./streams.php?filter=1\">\n\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t<div class=\"card bg-secondary\">\n\t\t\t\t\t\t\t\t\t<div class=\"card-bg active-streams cta-box bg-secondary\">\n\t\t\t\t\t\t\t\t\t    <div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-video avatar-title bg-secondary font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-success rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-video avatar-title bg-secondary font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"col\" align=\"right\">\n\t\t\t\t\t\t\t\t\t\t\t\t<h3 class=\"text-white my-1 bg-secondary\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"text-white my-1 text-truncate\">";
    echo $_["online_streams"];
    echo "</p>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "live_connections")) {
        echo "\t\t\t\t\t\t\t\t        <a href=\"./streams.php?filter=2\">\n\t\t\t\t\t\t\t\t        ";
    }
    echo "\t\t\t\t\t\t\t\t\t\t<div class=\"card-bg offline-streams cta-box bg-secondary\">\n\t\t\t\t\t\t\t\t\t    <div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between\">\n\t\t\t\t\t\t\t\t\t\t\t ";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t <i class=\"fe-video-off avatar-title bg-secondary font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-md bg-dark rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-video-off avatar-title bg-secondary font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"col\" align=\"right\">\n\t\t\t\t\t\t\t\t\t\t\t\t<h3 class=\"text-white my-1 bg-secondary\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"text-white my-1 text-truncate\">";
    echo $_["offline_streams"];
    echo "</p>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
    if ($rSettings["save_closed_connection"] && $rAdminSettings["dashboard_stats"]) {
        echo "\t\t\t\t\t\t\t<div class=\"col-xl-12\">\n\t\t\t\t\t\t\t\t<!-- Portlet card -->\n\t\t\t\t\t\t\t\t<div class=\"card\">\n\t\t\t\t\t\t\t\t\t<div class=\"card-body border\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"card-widgets\">\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"javascript: setPeriod('week');\">\n\t\t\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-xs\">";
        echo $_["week"];
        echo "</button>\n\t\t\t\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"javascript: setPeriod('day');\">\n\t\t\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-xs\">";
        echo $_["day"];
        echo "</button>\n\t\t\t\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"javascript: setPeriod('hour');\">\n\t\t\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-xs\">";
        echo $_["hour"];
        echo "</button>\n\t\t\t\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t<h4 class=\"header-title mb-0\">";
        echo $_["connections"];
        echo "</h4>\n\t\t\t\t\t\t\t\t\t\t<div id=\"statistics-collapse\" class=\"collapse pt-3 show\" dir=\"ltr\">\n\t\t\t\t\t\t\t\t\t\t\t<div id=\"statistics\" class=\"apex-charts\"></div>\n\t\t\t\t\t\t\t\t\t\t</div> <!-- collapsed end -->\n\t\t\t\t\t\t\t\t\t</div> <!-- end card-bg -->\n\t\t\t\t\t\t\t\t</div> <!-- end card-->\n\t\t\t\t\t\t\t</div> <!-- end col-->\n\t\t\t\t\t\t\t";
    }
    $i = 0;
    foreach ($rServers as $rServer) {
        $i++;
        if ($i == 5) {
            $i = 1;
        }
        echo "\t\t\t\t\t\t\t<div class=\"col-xl-3 col-md-6\">\n\t\t\t\t\t\t\t    <div class=\"card-header bg-dark text-white\">\n\t\t\t\t\t\t\t        ";
        if (hasPermissions("adv", "live_connections")) {
            echo "\t\t\t\t\t\t\t\t\t<div class=\"float-right\">\n\t\t\t\t\t\t\t\t        <a href=\"./server.php?id=";
            echo $rServer["id"];
            echo "\" class=\"arrow-none card-drop\">\n                                            <i class=\"mdi mdi-pencil-outline\"></i>\n\t\t\t\t\t\t\t            </a>\n\t\t\t\t\t\t\t\t\t    <a href=\"./process_monitor.php?server=";
            echo $rServer["id"];
            echo "\" class=\"arrow-none card-drop\">\n                                            <i class=\"mdi mdi-chart-line\"></i>\n\t\t\t\t\t\t\t            </a>\n                                    </div>\n\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\t<font size=\"2\" class=\"card-title mb-0 text-white mdi mdi-server\"> ";
        echo $rServer["server_name"];
        echo " - ";
        echo $rServer["server_ip"];
        echo " - <span id=\"s_";
        echo $rServer["id"];
        echo "_uptime\">0d 0h</font>\n\t\t\t\t\t\t\t\t</div>\n                            <div class=\"card-header text-white bg-white border\"><p>\n\t\t\t\t\t            <div class=\"row\">\n                                    <div class=\"col-md-2 col-2\">\n                                        <h4 class=\"header-title\">";
        echo $_["conns"];
        echo "</h4>\n                                    </div>\n                                    <div class=\"col-md-2 col-2\">\n                                        <a href=\"./live_connections.php?server_id=";
        echo $rServer["id"];
        echo "\"><button id=\"s_";
        echo $rServer["id"];
        echo "_conns\" type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\">0</button></a>\n                                    </div>\n                                    <div class=\"col-md-2 col-2\">\n                                        <h4 class=\"header-title\">";
        echo $_["users"];
        echo "</h4>\n                                    </div>\n                                    <div class=\"col-md-2 col-2\">\n                                        <a href=\"./live_connections.php?server_id=";
        echo $rServer["id"];
        echo "\"><button id=\"s_";
        echo $rServer["id"];
        echo "_users\" type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\">0</button></a>\n                                    </div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-md-4 col-4\">\n                                        <div class=\"progress-w-left\">\n                                            <!--<h4 class=\"progress-value header-title mdi mdi-fan font-18\"></h4>-->\n                                            <div class=\"progress progress-lg\">\n                                                <div class=\"progress-bar \" id=\"s_";
        echo $rServer["id"];
        echo "_cpu\" role=\"progressbar\" style=\"width: 0%;\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>\n                                            </div>\n                                         </div>\n                                    </div>\n                                </div>\n\t\t\t\t\t\t\t\t<div class=\"row\">\n                                    <div class=\"col-md-2 col-2\">\n                                        <h4 class=\"header-title\">Streams Live</h4>\n                                    </div>\n\t\t\t\t\t\t\t\t\t";
        if (hasPermissions("adv", "live_connections")) {
            echo "\t\t\t\t\t\t\t\t    <a href=\"./streams.php?filter=1\">\n\t\t\t\t\t\t\t\t    ";
        }
        echo "                                    <div class=\"col-md-2 col-2\">\n                                        <button id=\"s_";
        echo $rServer["id"];
        echo "_online\" type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\">0</button></a>\n                                    </div>\n\t\t\t\t\t\t            <div class=\"col-md-2 col-2\">\n                                        <h4 class=\"header-title\">Streams Off</h4>\n                                    </div>\n\t\t\t\t\t\t\t\t\t";
        if (hasPermissions("adv", "live_connections")) {
            echo "\t\t\t\t\t\t\t\t    <a href=\"./streams.php?filter=2\">\n\t\t\t\t\t\t\t\t    ";
        }
        echo "                                    <div class=\"col-md-2 col-2\">\n                                        <button id=\"s_";
        echo $rServer["id"];
        echo "_offline\" type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\">0</button></a>\n                                    </div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-md-4 col-4\">\n                                        <div class=\"progress-w-left\">\n                                            <!--<h4 class=\"progress-value header-title mdi mdi-chip font-18\"></h4>-->\n                                            <div class=\"progress progress-lg\">\n                                                <div class=\"progress-bar\" id=\"s_";
        echo $rServer["id"];
        echo "_mem\" role=\"progressbar\" style=\"width: 0%;\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div>\n\t\t\t\t\t\t\t\t<div class=\"row\">\n                                    <div class=\"col-md-2 col-2\">\n\t\t\t\t\t\t\t            <h4 class=\"header-title\">Input</h4>\n                                    </div>\n                                    <div class=\"col-md-2 col-2\">\n\t\t\t\t\t\t\t            <button id=\"s_";
        echo $rServer["id"];
        echo "_input\" type=\"button\" class=\"btn btn-info btn-xs waves-effect waves-light btn-fixed-min\">0</button>\n                                    </div>\n                                    <div class=\"col-md-2 col-2\">\n                                        <h4 class=\"header-title\">Output</h4>\n                                    </div>\n                                    <div class=\"col-md-2 col-2\">\n\t\t\t\t\t\t\t            <button id=\"s_";
        echo $rServer["id"];
        echo "_output\" type=\"button\" class=\"btn btn-info btn-xs waves-effect waves-light btn-fixed-min\">0</button>\n                                    </div>\n\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-md-4 col-4\">\n\t\t\t\t\t\t\t\t\t    \n\t\t\t\t\t\t\t\t\t\t";
        if ($rServer["enable_duplex"] == 0) {
            echo "\t\t\t\t\t\t\t\t\t\t\n                                        <div class=\"progress-w-left\">\n                                            <!--<h4 class=\"progress-value header-title mdi mdi-access-point-network font-18\"></h4>-->\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"progress progress-lg\">\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"progress-bar\" id=\"s_";
            echo $rServer["id"];
            echo "_net\" role=\"progressbar\" style=\"width: 0%;\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\n                                        </div>\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t";
        } else {
            echo "\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t<div class=\"progress-w-left1\">\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"progress1 progress-lg\">\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"progress-bar\" id=\"s_";
            echo $rServer["id"];
            echo "_inet\" role=\"progressbar\" style=\"width: 0%;\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>\n\t\t\t\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"progress1 progress-lg\">\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"progress-bar\" id=\"s_";
            echo $rServer["id"];
            echo "_onet\" role=\"progressbar\" style=\"width: 0%;\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>\n\t\t\t\t\t\t\t\t\t\t\t</div>\t\t\t\t\t\t\t\t\t\t\t\t\n                                        </div>\n\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\t\t\n                                     </div>\n\t\t\t\t\t\t\t\t\t \n                                </div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"card\">\n                            </div>\n\t\t\t\t\t\t</div><br>\t\t\t\t\t\t\n\t\t\t\t\t\t\t";
    }
    echo "<br>\t\n\t\t\t\t\t</div>\n                </div>\t\t\t\t\t\n\t\t\t\t<div class=\"tab-pane tab-pane-server\" id=\"server-tab\">\n                        <div class=\"row\">\n                            <div class=\"col-md-6 col-xl-3\">\n                                <div class=\"card-bg active-connections\">\n                                    <div class=\"row\">\n                                        <div class=\"col-6\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-zap avatar-title font-22 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"avatar-sm bg-soft-purple rounded\">\n                                                <i class=\"fe-zap avatar-title font-22 text-purple\"></i>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                        </div>\n                                        <div class=\"col-6\">\n                                            <div class=\"text-right\">\n                                                <h3 class=\"text-dark my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n                                                <p class=\"text-muted mb-1 text-truncate\">";
    echo $_["open_connections"];
    echo "</p>\n                                            </div>\n                                        </div>\n                                    </div>\n                                    <div class=\"mt-3\">\n                                        <h6 class=\"text-uppercase\">";
    echo $_["total_connections"];
    echo " <span class=\"float-right entry-percentage\">0</span></h6>\n                                        <div class=\"progress progress-sm m-0\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "                                            <div class=\"progress-bar bg-secondary\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"progress-bar bg-purple\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                                <span class=\"sr-only\">0%</span>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\n                            <div class=\"col-md-6 col-xl-3\">\n                                <div class=\"card-bg online-users\">\n                                    <div class=\"row\">\n                                        <div class=\"col-6\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-users avatar-title font-22 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"avatar-sm bg-soft-success rounded\">\n                                                <i class=\"fe-users avatar-title font-22 text-success\"></i>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                        </div>\n                                        <div class=\"col-6\">\n                                            <div class=\"text-right\">\n                                                <h3 class=\"text-dark my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n                                                <p class=\"text-muted mb-1 text-truncate\">";
    echo $_["online_users"];
    echo "</p>\n                                            </div>\n                                        </div>\n                                    </div>\n                                    <div class=\"mt-3\">\n                                        <h6 class=\"text-uppercase\">";
    echo $_["total_active"];
    echo " <span class=\"float-right entry-percentage\">0</span></h6>\n                                        <div class=\"progress progress-sm m-0\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "                                            <div class=\"progress-bar bg-secondary\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"progress-bar bg-success\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                                <span class=\"sr-only\">0%</span>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\n                            <div class=\"col-md-6 col-xl-3\">\n                                <div class=\"card-bg input-flow\">\n                                    <div class=\"row\">\n                                        <div class=\"col-6\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-trending-down avatar-title font-22 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"avatar-sm bg-soft-primary rounded\">\n                                                <i class=\"fe-trending-down avatar-title font-22 text-primary\"></i>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                        </div>\n                                        <div class=\"col-6\">\n                                            <div class=\"text-right\">\n                                                <h3 class=\"text-dark my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span> <small>Mbps</small></h3>\n                                                <p class=\"text-muted mb-1 text-truncate\">";
    echo $_["input_flow"];
    echo "</p>\n                                            </div>\n                                        </div>\n                                    </div>\n                                    <div class=\"mt-3\">\n                                        <h6 class=\"text-uppercase\">";
    echo $_["network_load"];
    echo " <span class=\"float-right entry-percentage\">0%</span></h6>\n                                        <div class=\"progress progress-sm m-0\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "                                            <div class=\"progress-bar bg-secondary\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"progress-bar bg-primary\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                                <span class=\"sr-only\">0%</span>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\n                            <div class=\"col-md-6 col-xl-3\">\n                                <div class=\"card-bg output-flow\">\n                                    <div class=\"row\">\n                                        <div class=\"col-6\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-trending-up avatar-title font-22 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"avatar-sm bg-soft-info rounded\">\n                                                <i class=\"fe-trending-up avatar-title font-22 text-info\"></i>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                        </div>\n                                        <div class=\"col-6\">\n                                            <div class=\"text-right\">\n                                                <h3 class=\"text-dark my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span> <small>Mbps</small></h3>\n                                                <p class=\"text-muted mb-1 text-truncate\">";
    echo $_["output_flow"];
    echo "</p>\n                                            </div>\n                                        </div>\n                                    </div>\n                                    <div class=\"mt-3\">\n                                        <h6 class=\"text-uppercase\">";
    echo $_["network_load"];
    echo " <span class=\"float-right entry-percentage\">0%</span></h6>\n                                        <div class=\"progress progress-sm m-0\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "                                            <div class=\"progress-bar bg-secondary\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"progress-bar bg-info\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                                <span class=\"sr-only\">0%</span>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div> <!-- end card-box-->\n                            </div> <!-- end col -->\n                            \n                            <div class=\"col-md-6 col-xl-3\">\n                                <div class=\"card-bg active-streams\">\n                                    <div class=\"row\">\n                                        <div class=\"col-6\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-arrow-up-right avatar-title font-22 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"avatar-sm bg-soft-purple rounded\">\n                                                <i class=\"fe-arrow-up-right avatar-title font-22 text-purple\"></i>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                        </div>\n                                        <div class=\"col-6\">\n                                            <a href=\"javascript:void(0);\" onClick=\"onlineStreams()\">\n                                            <div class=\"text-right\">\n                                                <h3 class=\"text-dark my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n                                                <p class=\"text-muted mb-1 text-truncate\">";
    echo $_["online_streams"];
    echo "</p>\n                                            </div>\n                                            </a>\n                                        </div>\n                                    </div>\n                                    <a href=\"javascript:void(0);\" onClick=\"offlineStreams()\">\n                                    <div class=\"mt-3\">\n                                        <h6 class=\"text-uppercase\">";
    echo $_["offline_streams"];
    echo " <span class=\"float-right entry-percentage\">0</span></h6>\n                                        <div class=\"progress progress-sm m-0\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "                                            <div class=\"progress-bar bg-secondary\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"progress-bar bg-purple\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                                <span class=\"sr-only\">0%</span>\n                                            </div>\n                                        </div>\n                                    </div>\n                                    </a>\n                                </div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\n                            <div class=\"col-md-6 col-xl-3\">\n                                <div class=\"card-box cpu-usage\">\n                                    <div class=\"row\">\n                                        <div class=\"col-6\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-cpu avatar-title font-22 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"avatar-sm bg-soft-success rounded\">\n                                                <i class=\"fe-cpu avatar-title font-22 text-success\"></i>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                        </div>\n                                        <div class=\"col-6\">\n                                            <div class=\"text-right\">\n                                                <h3 class=\"text-dark my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span><small>%</small></h3>\n                                                <p class=\"text-muted mb-1 text-truncate\">";
    echo $_["cpu_usage"];
    echo "</p>\n                                            </div>\n                                        </div>\n                                    </div>\n                                    <div class=\"mt-3\">\n                                        <h6 class=\"text-uppercase\">&nbsp;</h6>\n                                        <div class=\"progress progress-sm m-0\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "                                            <div class=\"progress-bar bg-secondary\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"progress-bar bg-success\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                                <span class=\"sr-only\">0%</span>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\n                            <div class=\"col-md-6 col-xl-3\">\n                                <div class=\"card-box mem-usage\">\n                                    <div class=\"row\">\n                                        <div class=\"col-6\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-terminal avatar-title font-22 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"avatar-sm bg-soft-primary rounded\">\n                                                <i class=\"fe-terminal avatar-title font-22 text-primary\"></i>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                        </div>\n                                        <div class=\"col-6\">\n                                            <div class=\"text-right\">\n                                                <h3 class=\"text-dark my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span><small>%</small></h3>\n                                                <p class=\"text-muted mb-1 text-truncate\">";
    echo $_["mem_usage"];
    echo "</p>\n                                            </div>\n                                        </div>\n                                    </div>\n                                    <div class=\"mt-3\">\n                                        <h6 class=\"text-uppercase\">&nbsp;</h6>\n                                        <div class=\"progress progress-sm m-0\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "                                            <div class=\"progress-bar bg-secondary\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"progress-bar bg-primary\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\">\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                                <span class=\"sr-only\">0%</span>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div> <!-- end card-box-->\n                            </div> <!-- end col -->\n                           \n                            <div class=\"col-md-6 col-xl-3\">\n                                <div class=\"card-box uptime\">\n                                    <div class=\"row\">\n                                        <div class=\"col-6\">\n\t\t\t\t\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-secondary rounded\">\n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fe-power avatar-title font-22 text-white\"></i>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "                                            <div class=\"avatar-sm bg-soft-info rounded\">\n                                                <i class=\"fe-power avatar-title font-22 text-info\"></i>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                        </div>\n                                        <div class=\"col-6\">\n                                            <div class=\"text-right\">\n                                                <h3 class=\"text-dark my-1 entry\">--</h3>\n                                                <p class=\"text-muted mb-1 text-truncate\">";
    echo $_["system_uptime"];
    echo "</p>\n                                            </div>\n                                        </div>\n                                    </div>\n                                    <div class=\"mt-3\">\n                                        <h6 class=\"text-uppercase\">&nbsp;</span></h6>\n                                        <div class=\"progress-sm m-0\"></div>\n                                    </div>\n                                </div> <!-- end card-box-->\n                            </div> <!-- end col -->\n\n                        </div>\n                    </div>\n                </div>\t\n                <!-- unicio estatisticas-->\n\t\t\t\t\n\t\t<div class=\"row\">\t\t\n";
    if ($rPermissions["is_admin"] && $rAdminSettings["active_statistics"]) {
        echo "<style>\n.infoServ td {\npadding:0px 4px 0px 4px;\n}\n#Statistics {\n  font-family: Tahoma, Verdana, sans-serif;\n  font-weight: bold;\n  width: 100%;\n  height: 400px;\n  font-size: 11px;\n\n}\n.row2 {\n    display: flex;\n    overflow: hidden;\n\n}\n.col2 {\n}\n</style>\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t        <div class=\"col-xl-4\">\n\t\t\t\t\t<div class=\"card\">\n\t\t\t\t\t\t<div class=\"card-bg\">\n\t\t\t                <div id=\"Statistics\" class=\"card-header border\">\t\n\t\t\t\t\t\t\t    <font size=\"3\" color=\"#000000\" class=\"card-title mb-0\"><center>Statistics</center></font><br>\n\t\t\t\t\t\t\t\t<div class= separator></div><br>\n\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-pink\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-laptop-code avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"mags.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp;&nbsp; Mags</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $devicesmag;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-pink\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-chalkboard-teacher avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"users.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp;&nbsp; Users</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $smarttv;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\t\t\n\t\t\t\t\t\t\t<div class= separator></div><br>\n\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-info\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-layer-group avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"bouquets.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp;&nbsp; Bouquets</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $bouquets;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-info\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-list-ol avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"streams.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp;&nbsp; Channels</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $channels;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\t\t\n\t\t\t\t\t\t\t    <div class= separator></div><br>\n\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-warning\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"far fa-file-video avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"episodes.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp;&nbsp; Episodes</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $episodes;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-warning\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-music avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"radios.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp;&nbsp; Radio</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $radio;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\t\t\t\n\t\t\t\t\t\t\t <div class= separator></div><br> \n\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-success\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-video avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"movies.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp;&nbsp; Movies</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $vod;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-success\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-film avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"series.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp;&nbsp; Series</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $series;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t <div class= separator></div><br> \n\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-danger\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-server avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"servers.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp;&nbsp; Servers</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\t\t\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $server;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-1\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"avatar-sm bg-danger\">\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-users avatar-title font-18 text-icon\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-3\">\t\n\t\t\t\t\t\t\t\t\t\t<a href=\"reg_users.php\">\n\t\t\t\t\t\t\t\t\t\t\t<span><p class=\"text-muted1 mb-1 text-dark\"><b>&nbsp; Resselers</b></p></span>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-2\">\t\t\n\t\t\t\t\t\t\t\t\t\t\t<p type=\"button\" class=\"btn btn-secondary btn-xs waves-effect waves-light btn-fixed-min\"><span>";
        echo $reseller;
        echo "</span></p>\n\t\t\t\t\t\t\t\t\t\t</a>\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class= separator></div>\n\t\t\t\t\t\t\t</div>\t\n                        </div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n";
    }
    echo "<!-- fim estatisticas-->\t\t\t\t\t\t\t\n";
    if ($rAdminSettings["dashboard_world_map_live"]) {
        echo "<style>\n.infoServ td {\npadding:0px 4px 0px 4px;\n}\n#WorldMapLive {\n  color: #ffffff;\n  width: 100%;\n  height: 400px;\n  font-size: 11px;\n\n}\n.row2 {\n    display: flex;\n    overflow: hidden;\n\n}\n.col2 {\n}\n</style>\n\n            <div class=\"col-xl-4\">\n                <div class=\"card\">\n                    <div class=\"card-bg border\">\n\t\t\t\t\t\t<div id=\"WorldMapLive\"></div>\n\t\t\t\t    </div>\n\t\t\t    </div>\n\t\t    </div>\n\n";
    }
    echo "\n";
    if ($rAdminSettings["dashboard_world_map_activity"]) {
        echo "<style>\n.infoServ td {\npadding:0px 4px 0px 4px;\n}\n#WorldMapActivity {\n  color: #ffffff;\n  width: 100%;\n  height: 400px;\n  font-size: 11px;\n\n}\n.row2 {\n    display: flex;\n    overflow: hidden;\n\n}\n.col2 {\n}\n</style>\n            <div class=\"col-xl-4\">\n                <div class=\"card\">\n                    <div class=\"card-bg border\">\n\t\t\t\t\t\t<div id=\"WorldMapActivity\"></div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n  \n";
    }
    echo "                <!-- end row -->\n\t\t\t\t";
} else {
    echo "\t\t\t\t<div class=\"alert alert-danger show text-center\" role=\"alert\" style=\"margin-top:20px;\">\n\t\t\t\t\t";
    echo $_["dashboard_no_permissions"];
    echo "<br/>\n\t\t\t\t\t";
    if ($rSettings["sidebar"]) {
        echo $_["dashboard_nav_left"];
    } else {
        echo $_["dashboard_nav_top"];
    }
    echo "\t\t\t\t</div>\n\t\t\t\t";
}
echo "               \n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-knob/jquery.knob.min.js\"></script>\n        <script src=\"assets/libs/peity/jquery.peity.min.js\"></script>\n\t\t<script src=\"assets/libs/apexcharts/apexcharts.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/jquery-number/jquery.number.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n\t\t<script src=\"assets/js/app.min.js\"></script>\n\t\t<script src=\"assets/js/amcharts4/ammap.js\"></script>\n        <script src=\"assets/js/amcharts4/writemap.js?5\"></script>\n        <script src=\"assets/js/amcharts4/worldLow3.js\"></script>\n        <script src=\"assets/js/amcharts4/light.js\"></script>\n        \n        <script>\n        rServerID = \"home\";\n\t\trChart = null;\n\t\trDates = null;\n\t\trOptions = null;\n        \n        function offlineStreams() {\n            window.location.href = \"./streams.php?filter=2&server=\" + window.rServerID;\n        }\n        \n        function onlineStreams() {\n            window.location.href = \"./streams.php?filter=1&server=\" + window.rServerID;\n        }\n\t\t\n        function getStats(auto=true) {\n            var rStart = Date.now();\n            if (window.rServerID == \"home\") {\n                rURL = \"./api.php?action=stats\";\n            } else {\n                rURL = \"./api.php?action=stats&server_id=\" + window.rServerID;\n            }\n            \$.getJSON(rURL, function(data) {\n                // Open Connections\n                var rCapacity = Math.ceil((data.open_connections / data.total_connections) * 100);\n                if (isNaN(rCapacity)) { rCapacity = 0; }\n                \$(\".active-connections .entry\").html(\$.number(data.open_connections, 0));\n                \$(\".active-connections .entry-percentage\").html(\$.number(data.total_connections, 0));\n                \$(\".active-connections .progress-bar\").prop(\"aria-valuenow\", rCapacity);\n                \$(\".active-connections .progress-bar\").css(\"width\", rCapacity.toString() + \"%\");\n                \$(\".active-connections .sr-only\").html(rCapacity.toString() + \"%\");\n                // Online Users\n                var rCapacity = Math.ceil((data.online_users / data.total_users) * 100);\n                if (isNaN(rCapacity)) { rCapacity = 0; }\n                \$(\".online-users .entry\").html(\$.number(data.online_users, 0));\n                \$(\".online-users .entry-percentage\").html(\$.number(data.total_users, 0));\n                \$(\".online-users .progress-bar\").prop(\"aria-valuenow\", rCapacity);\n                \$(\".online-users .progress-bar\").css(\"width\", rCapacity.toString() + \"%\");\n                \$(\".online-users .sr-only\").html(rCapacity.toString() + \"%\");\n                // Network Load - Input\n                var rCapacity = Math.ceil((Math.ceil(data.bytes_received) / data.network_guaranteed_speed) * 100);\n                if (isNaN(rCapacity)) { rCapacity = 0; }\n                \$(\".input-flow .entry\").html(\$.number(Math.ceil(data.bytes_received), 0));\n                \$(\".input-flow .entry-percentage\").html(rCapacity.toString() + \"%\");\n                \$(\".input-flow .progress-bar\").prop(\"aria-valuenow\", rCapacity);\n                \$(\".input-flow .progress-bar\").css(\"width\", rCapacity.toString() + \"%\");\n                \$(\".input-flow .sr-only\").html(rCapacity.toString() + \"%\");\n                // Network Load - Output\n                var rCapacity = Math.ceil((Math.ceil(data.bytes_sent) / data.network_guaranteed_speed) * 100);\n                if (isNaN(rCapacity)) { rCapacity = 0; }\n                \$(\".output-flow .entry\").html(\$.number(Math.ceil(data.bytes_sent), 0));\n                \$(\".output-flow .entry-percentage\").html(rCapacity.toString() + \"%\");\n                \$(\".output-flow .progress-bar\").prop(\"aria-valuenow\", rCapacity);\n                \$(\".output-flow .progress-bar\").css(\"width\", rCapacity.toString() + \"%\");\n                \$(\".output-flow .sr-only\").html(rCapacity.toString() + \"%\");\n                // Active Streams\n                var rCapacity = Math.ceil((data.total_running_streams / (data.offline_streams + data.total_running_streams)) * 100);\n                if (isNaN(rCapacity)) { rCapacity = 0; }\n                \$(\".active-streams .entry\").html(\$.number(data.total_running_streams, 0));\n                \$(\".active-streams .entry-percentage\").html(\$.number(data.offline_streams, 0));\n                \$(\".active-streams .progress-bar\").prop(\"aria-valuenow\", rCapacity);\n                \$(\".active-streams .progress-bar\").css(\"width\", rCapacity.toString() + \"%\");\n                \$(\".active-streams .sr-only\").html(rCapacity.toString() + \"%\");\n\t\t\t\t\$(\".offline-streams .entry\").html(\$.number(data.offline_streams, 0));\n                // CPU Usage\n                \$(\".cpu-usage .entry\").html(data.cpu);\n                \$(\".cpu-usage .entry-percentage\").html(data.cpu.toString() + \"%\");\n                \$(\".cpu-usage .progress-bar\").prop(\"aria-valuenow\", data.cpu);\n                \$(\".cpu-usage .progress-bar\").css(\"width\", data.cpu.toString() + \"%\");\n                \$(\".cpu-usage .sr-only\").html(data.cpu.toString() + \"%\");\n                // Memory Usage\n                \$(\".mem-usage .entry\").html(data.mem);\n                \$(\".mem-usage .entry-percentage\").html(data.mem.toString() + \"%\");\n                \$(\".mem-usage .progress-bar\").prop(\"aria-valuenow\", data.mem);\n                \$(\".mem-usage .progress-bar\").css(\"width\", data.mem.toString() + \"%\");\n                \$(\".mem-usage .sr-only\").html(data.mem.toString() + \"%\");\n                // Uptime\n\t\t\t\tif (data.uptime) {\n\t\t\t\t\t\$(\".uptime .entry\").html(data.uptime.split(\" \").slice(0,2).join(\" \"));\n\t\t\t\t}\n\t\t\t\t// Per Server\n\t\t\t\t\$(data.servers).each(function(i) {\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_conns\").html(\$.number(data.servers[i].open_connections, 0));\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_users\").html(\$.number(data.servers[i].online_users, 0));\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_online\").html(\$.number(data.servers[i].total_running_streams, 0));\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_offline\").html(\$.number(data.servers[i].offline_streams, 0));\n\t\t\t\t    \$(\"#s_\" + data.servers[i].server_id + \"_input\").html(\$.number(Math.ceil(data.servers[i].bytes_received), 0));\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_output\").html(\$.number(Math.ceil(data.servers[i].bytes_sent), 0));\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_total_users\").html(\$.number(data.servers[i].total_connections, 0));\n\t\t\t\t\t//cpu ans mem usage\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_cpu\").removeClass(\"bg-success\").removeClass(\"bg-danger\").removeClass(\"bg-warning\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_cpu\").attr(\"aria-valuenow\", data.servers[i].cpu);\n                    \$(\"#s_\" + data.servers[i].server_id + \"_cpu\").css(\"width\", data.servers[i].cpu + \"%\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_cpu\").html(data.servers[i].cpu + \"% Cpu\");\n                    \$(\"#s_\" + data.servers[i].server_id + \"_mem\").removeClass(\"bg-success\").removeClass(\"bg-danger\").removeClass(\"bg-warning\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_mem\").attr(\"aria-valuenow\", data.servers[i].mem);\n                    \$(\"#s_\" + data.servers[i].server_id + \"_mem\").css(\"width\", data.servers[i].mem + \"%\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_mem\").html(data.servers[i].mem + \"% Ram\");\n\t\t\t\t\t// Networks\t\t\t\t\t\n\t\t\t\t\tvar rOutput = data.servers[i].bytes_received;\n\t\t\t\t\tvar rInput = data.servers[i].bytes_sent;\n\t\t\t\t\tvar rSpeed = data.servers[i].network_guaranteed_speed;\n\t\t\t\t\tvar rPourcentage = Math.round( ( ( rInput + rOutput ) / rSpeed )  * 100 );\n\t\t\t\t\tvar rPourcentagei = Math.round( ( ( rOutput ) / rSpeed )  * 100 );\n\t\t\t\t\tvar rPourcentageo = Math.round( ( ( rInput ) / rSpeed )  * 100 );\n\t\t\t\t\t//Network Usage\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_net\").removeClass(\"bg-success\").removeClass(\"bg-danger\").removeClass(\"bg-warning\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_net\").attr('aria-valuenow', rPourcentage );\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_net\").css(\"width\", rPourcentage + \"%\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_net\").html( rPourcentage + \"% Network\");\n\t\t\t\t\t// Network Usage input\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_inet\").removeClass(\"bg-success\").removeClass(\"bg-danger\").removeClass(\"bg-warning\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_inet\").attr('aria-valuenow', rPourcentagei );\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_inet\").css(\"width\", rPourcentagei + \"%\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_inet\").html( rPourcentagei + \"% Input\");\n\t\t\t\t\t//Network Usage output\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_onet\").removeClass(\"bg-success\").removeClass(\"bg-danger\").removeClass(\"bg-warning\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_onet\").attr('aria-valuenow', rPourcentageo );\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_onet\").css(\"width\", rPourcentageo + \"%\");\n\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_onet\").html( rPourcentageo + \"% Output\");\n\t\t\t\t\tif (data.servers[i].uptime) {\n\t\t\t\t\t\t\$(\"#s_\" + data.servers[i].server_id + \"_uptime\").html(data.servers[i].uptime.split(\" \").slice(0,2).join(\" \"));\n\t\t\t\t\t}\n\t\t\t\tif (data.servers[i].cpu > 75) {\n                        \$(\"#s_\" + data.servers[i].server_id + \"_cpu\").addClass(\"bg-danger\");\n                    } else if (data.servers[i].cpu > 50) {\n                        \$(\"#s_\" + data.servers[i].server_id + \"_cpu\").addClass(\"bg-warning\");\n                    } else {\n                        \$(\"#s_\" + data.servers[i].server_id + \"_cpu\").addClass(\"bg-success\");\n                    }\n                    if (data.servers[i].mem > 75) {\n                        \$(\"#s_\" + data.servers[i].server_id + \"_mem\").addClass(\"bg-danger\");\n                    } else if (data.servers[i].mem > 50) {\n                        \$(\"#s_\" + data.servers[i].server_id + \"_mem\").addClass(\"bg-warning\");\n                    } else {\n                        \$(\"#s_\" + data.servers[i].server_id + \"_mem\").addClass(\"bg-success\");\n                    }\n\t\t\t\t});\n                if (auto) {\n                    if (Date.now() - rStart < 1000) {\n                        setTimeout(getStats, 1000 - (Date.now() - rStart));\n                    } else {\n                        getStats();\n                    }\n                }\n            }).fail(function() {\n                if (auto) {\n                    setTimeout(getStats, 1000);\n                }\n            });\n        }\n        \n        \$('.dashboard-tabs .nav-link').on('click', function (e) {\n            window.rServerID = \$(e.target).data(\"id\");\n            getStats(false);\n            \$(\".nav-link\").each(function() {\n                \$(this).removeClass(\"active\");\n            });\n            \$(e.target).addClass(\"active\");\n            if (window.rServerID == \"home\") {\n                if (!\$(\"#server-home\").is(\":visible\")) {\n                    \$(\"#server-tab\").hide();\n                    \$(\"#server-home\").show();\n                }\n            } else {\n                if (!\$(\"#server-tab\").is(\":visible\")) {\n                    \$(\"#server-home\").hide();\n                    \$(\"#server-tab\").show();\n                }\n            }\n        });\n\t\t\$('[data-plugin=\"peity-line\"]').each(function(t, i) {\n                \$(this).peity(\"line\", \$(this).data());\n            });\n\t\t";
if ($rSettings["save_closed_connection"] && $rAdminSettings["dashboard_stats"]) {
    echo "\t\tfunction setPeriod(rPeriod) {\n\t\t\tif ((window.rDates[rPeriod][0]) && (window.rDates[rPeriod][1])) {\n\t\t\t\twindow.rOptions[\"xaxis\"][\"min\"] = window.rDates[rPeriod][0]*1000;\n\t\t\t\twindow.rOptions[\"xaxis\"][\"max\"] = window.rDates[rPeriod][1]*1000;\n\t\t\t\twindow.rChart.updateOptions(window.rOptions);\n\t\t\t\t\$(\".apexcharts-zoom-in-icon\").trigger('click');\n\t\t\t\t\$(\".apexcharts-zoom-out-icon\").trigger('click');\n\t\t\t} else {\n\t\t\t\twindow.rOptions[\"xaxis\"][\"min\"] = undefined;\n\t\t\t\twindow.rOptions[\"xaxis\"][\"max\"] = undefined;\n\t\t\t\twindow.rChart.updateOptions(window.rOptions);\n\t\t\t}\n\t\t}\n        \n\t\tfunction getChart() {\n\t\t\trURL = \"./api.php?action=chart_stats\";\n\t\t\t\$.getJSON(rURL, function(rStatistics) {\n\t\t\t\twindow.rDates = rStatistics[\"dates\"];\n\t\t\t\twindow.rOptions = {\n\t\t\t\t\tchart: {\n\t\t\t\t\t\theight: 350,\n\t\t\t\t\t\ttype: \"area\",\n\t\t\t\t\t\tstacked: false,\n\t\t\t\t\t\tzoom: {\n\t\t\t\t\t\t\ttype: 'x',\n\t\t\t\t\t\t\tenabled: true,\n\t\t\t\t\t\t\tautoScaleYaxis: true\n\t\t\t\t\t\t}\n\t\t\t\t\t},\n\t\t\t\t\tcolors: [\"#56c2d6\"],\n\t\t\t\t\tdataLabels: {\n\t\t\t\t\t\tenabled: false\n\t\t\t\t\t},\n\t\t\t\t\tstroke: {\n\t\t\t\t\t\twidth: [2],\n\t\t\t\t\t\tcurve: \"smooth\"\n\t\t\t\t\t},\n\t\t\t\t\tseries: [{\n\t\t\t\t\t\tname: \"Open Connections\",\n\t\t\t\t\t\tdata: rStatistics[\"data\"][\"conns\"]\n\t\t\t\t\t}],\n\t\t\t\t\tfill: {\n\t\t\t\t\t\ttype: \"gradient\", \n\t\t\t\t\t\tgradient: {\n\t\t\t\t\t\t\topacityFrom: .6,\n\t\t\t\t\t\t\topacityTo: .8\n\t\t\t\t\t\t}\n\t\t\t\t\t},\n\t\t\t\t\txaxis: {\n\t\t\t\t\t\ttype: \"datetime\",\n\t\t\t\t\t\tmin: window.rDates['day'][0]*1000,\n\t\t\t\t\t\tmax: window.rDates['day'][1]*1000\n\t\t\t\t\t},\n\t\t\t\t\ttooltip: {\n\t\t\t\t\t  y: {\n\t\t\t\t\t\tformatter: function(value, { series, seriesIndex, dataPointIndex, w }) {\n\t\t\t\t\t\t  return parseInt(value)\n\t\t\t\t\t\t}\n\t\t\t\t\t  }\n\t\t\t\t\t}\n\t\t\t\t};\n\t\t\t\t(window.rChart = new ApexCharts(document.querySelector(\"#statistics\"), window.rOptions)).render();\n\t\t\t\t\$(\".apexcharts-zoom-in-icon\").trigger('click');\n\t\t\t\t\$(\".apexcharts-zoom-out-icon\").trigger('click');\n\t\t\t});\n\t\t}\n\t\t";
}
echo "        \$(document).ready(function() {\n            getStats();\n\t\t\t";
if ($rSettings["save_closed_connection"] && $rAdminSettings["dashboard_stats"]) {
    echo "\t\t\tgetChart();\n\t\t\t";
}
echo "        });\n        </script>\n\t\t\n\t\t<script src=\"assets/js/amcharts4/writemaplive.js\"></script>\n\t\t<script>\n\t\t";
if ($rAdminSettings["dashboard_world_map_live"]) {
    echo "\t\t\t\tvar mapData = showMap(\"WorldMapLive\", [";
    getWorldMapLive();
    echo "], \"Live by Country\");\n\t\t";
}
echo "        </script>\n\n        <script src=\"assets/js/amcharts4/writemapactivity.js\"></script>\n\t\t<script>\n\t\t";
if ($rAdminSettings["dashboard_world_map_activity"]) {
    echo "\t\t\t\tvar mapData = showMap(\"WorldMapActivity\", [";
    getWorldMapActivity();
    echo "], \"Activity by Country\");\n\t\t";
}
echo "        </script>\n    </body>\n</html>";

?>