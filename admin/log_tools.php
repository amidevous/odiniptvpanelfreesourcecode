<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "stream_tools")) {
    exit;
}
if (isset($_POST["submit_clear_log"]) && hasPermissions("adv", "stream_tools")) {
    if (isset($_POST["clear_log_auto"])) {
        $rAdminSettings["clear_log_auto"] = true;
        unset($_POST["clear_log_auto"]);
    } else {
        $rAdminSettings["clear_log_auto"] = false;
    }
    if (isset($_POST["clear_log_older_than_days"]) && preg_match("/^[1-9][0-9]*\$/", $_POST["clear_log_older_than_days"])) {
        $rAdminSettings["clear_log_older_than_days"] = $_POST["clear_log_older_than_days"];
        unset($_POST["clear_log_older_than_days"]);
    } else {
        $rAdminSettings["clear_log_older_than_days"] = $daysToClear;
        unset($_POST["clear_log_older_than_days"]);
    }
    if (isset($_POST["clear_log_tables"])) {
        if (!is_array($_POST["clear_log_tables"])) {
            $rAdminSettings["clear_log_tables"] = [$_POST["clear_log_tables"]];
        }
        $rAdminSettings["clear_log_tables"] = json_encode($_POST["clear_log_tables"]);
        unset($_POST["clear_log_tables"]);
    } else {
        $rAdminSettings["clear_log_tables"] = [];
    }
    $rAdminSettings["clear_log_check"] = time();
    writeAdminSettings();
    $_STATUS = 1;
    $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . ESC("Clear Logs Saved: " . date("d-m-Y H:m", $rAdminSettings["clear_log_check"]) . " and will be deleted in: " . date("d-m-Y H:m", $rAdminSettings["clear_log_check"] + $rAdminSettings["clear_log_older_than_days"] * 86400)) . "', " . intval(time()) . ");");
}
if ($rPermissions["is_admin"]) {
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'user_activity' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='user_activity'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $user_activity = $row["user_activity"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'user_activity_now' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='user_activity_now'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $user_activity_now = $row["user_activity_now"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'panel_logs' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='panel_logs'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $panel_logs = $row["panel_logs"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'stream_logs' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='stream_logs'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $stream_logs = $row["stream_logs"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'login_logs' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='login_logs'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $login_logs = $row["login_logs"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'client_logs' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='client_logs'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $client_logs = $row["client_logs"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'login_flood' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='login_flood'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $login_flood = $row["login_flood"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'mag_events' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='mag_events'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $mag_events = $row["mag_events"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'mag_claims' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='mag_claims'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $mag_claims = $row["mag_claims"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'mag_logs' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='mag_logs'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $mag_logs = $row["mag_logs"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'tmdb_async' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='tmdb_async'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $tmdb_async = $row["tmdb_async"];
    }
    $sql = "SELECT round(((data_length + index_length)/1024/1024),2)'watch_output' FROM information_schema.TABLES WHERE table_schema = 'xtream_iptvpro' AND table_name ='watch_output'; ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $watch_output = $row["watch_output"];
    }
}
if (isset($_GET["flush"])) {
    flushActivity();
    $_STATUS = 1;
}
if (isset($_GET["flushnow"])) {
    flushActivitynow();
    $_STATUS = 1;
}
if (isset($_GET["flushpanel"])) {
    flushPanelogs();
    $_STATUS = 1;
}
if (isset($_GET["flushstlogs"])) {
    flushStlogs();
    $_STATUS = 1;
}
if (isset($_GET["flushevents"])) {
    flushEvents();
    $_STATUS = 1;
}
if (isset($_GET["flushclientlogs"])) {
    flushClientlogs();
    $_STATUS = 1;
}
if (isset($_GET["flushflood"])) {
    flushLogins();
    $_STATUS = 1;
}
if (isset($_GET["flushloginlogs"])) {
    flushLoginlogs();
    $_STATUS = 1;
}
if (isset($_GET["flushmagclaims"])) {
    flushMagclaims();
    $_STATUS = 1;
}
if (isset($_GET["flushmaglogs"])) {
    flushMaglogs();
    $_STATUS = 1;
}
if (isset($_GET["lockisp"])) {
    lockIsp();
    $_STATUS = 1;
}
if (isset($_GET["unlockisp"])) {
    unlockIsp();
    $_STATUS = 1;
}
if (isset($_GET["lockstb"])) {
    lockStb();
    $_STATUS = 1;
}
if (isset($_GET["unlockstb"])) {
    unlockStb();
    $_STATUS = 1;
}
if (isset($_GET["clearisp"])) {
    clearIsp();
    $_STATUS = 1;
}
if (isset($_GET["flushwatchfolder"])) {
    flushWatchFolder();
    $_STATUS = 1;
}
if (isset($_GET["flushtmdbasync"])) {
    flushTmdbAsync();
    $_STATUS = 1;
}
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <h4 class=\"page-title\">Quick Tools </h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 1) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            Operation performed successfully !!! \n                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", 'Operation performed successfully !!!', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n\t\t\t\t\t\t\t\t<div id=\"basicwizard\">\n\t\t\t\t\t\t\t\t\t<ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n\t\t\t\t\t\t\t\t\t\t<li class=\"nav-item\">\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"#clearlogs\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"mdi mdi-cube mr-1\"></i>\n\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"d-none d-sm-inline\">Quick Tools </span>\n\t\t\t\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t<div class=\"tab-pane\" id=\"clearlogs\"></p>\n\t\t\t\t\t\t\t\t\t    <form action=\"./log_tools.php\" method=\"POST\" id=\"clear_logs_form\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"col-12\">\n\t\t\t\t\t\t\t\t\t\t    <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"clear_log_auto\">Auto Clear Logs <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Here you can enable or disable auto clear logs\" class=\"mdi mdi-information\"></i></label>\n\t\t\t\t\t\t\t\t\t\t\t    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<input name=\"clear_log_auto\" id=\"clear_log_auto\" type=\"checkbox\"";
if ($rAdminSettings["clear_log_auto"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"clear_log_older_than_days\">Clear Logs in: <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Minimum Value is 1 = 24h\" class=\"mdi mdi-information\"></i></label>\n                                                <div class=\"col-md-1\">\n                                                    <input type=\"text\" class=\"form-control\" id=\"clear_log_older_than_days\" name=\"clear_log_older_than_days\" min=\"1\" value=\"";
echo htmlspecialchars($rAdminSettings["clear_log_older_than_days"] ? $rAdminSettings["clear_log_older_than_days"] : $daysToClear);
echo "\" required>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-1 col-form-label\" for=\"clear_log_older_than_days\">Days <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"\"></i></label>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"clear_log_tables\">Logs to Clear <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Here you can choose the logs you want to delete\" class=\"mdi mdi-information\"></i></label>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t\t\t\t    <select name=\"clear_log_tables[]\" id=\"clear_log_tables\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "...\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t";
foreach (["Users Activity" => "flushActivity", "Users Activity Now" => "flushActivitynow", "Panel Logs" => "flushPanelogs", "Login Logs" => "flushLoginlogs", "Login Flood" => "flushLogins", "Mag Claims" => "flushMagclaims", "Stream Logs" => "flushStlogs", "Client Logs" => "flushClientlogs", "Mag Events" => "flushEvents", "Mag Logs" => "flushMaglogs"] as $rKey => $rFtable) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<option ";
    if (in_array($rFtable, json_decode($rAdminSettings["clear_log_tables"], true))) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rFtable;
    echo "\">";
    echo $rKey;
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                                    </select>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t<ul class=\"list-inline wizard mb-0\" style=\"margin-top:30px;\">\n\t\t\t\t\t\t\t\t\t\t    <li class=\"list-inline-item float-left\"><input name=\"submit_clear_log\" type=\"submit\" class=\"btn btn-primary\" value=\"Save Changes\">\n\t\t\t\t\t\t\t\t\t\t    </li>\n\t\t\t\t\t\t\t\t\t\t</ul><br /><br /><hr />\n\t\t\t\t\t\t\t\t\t\t</form>\n                                            <div class=\"col-12\">\n\t\t\t\t\t\t\t\t\t\t\t    <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-3 col-form-label\"><a href=\"./tables_mb.php\">[Show All Tables]</a></label>\n\t\t\t\t\t\t\t\t\t\t\t    </div>\n                                                <div class=\"form-group row mb-4\">\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $user_activity;
echo " MB</span> Clear Users Activity </label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flush\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Activity Logs? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                    <div class=\"col-md-2\"></div>\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $user_activity_now;
echo " MB</span> Clear Users Activity Now</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushnow\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Users Activity Now? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<br>\n                                                <div class=\"form-group row mb-4\">\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $panel_logs;
echo " MB</span> Clear Panel Logs</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushpanel\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Panel Logs? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                    <div class=\"col-md-2\"></div>\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $stream_logs;
echo " MB</span> Clear Stream Logs</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushstlogs\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Stream Logs? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<br>\n                                                <div class=\"form-group row mb-4\">\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $login_logs;
echo " MB</span> Clear Login Logs</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushloginlogs\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Login Logs? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                    <div class=\"col-md-2\"></div>\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $client_logs;
echo " MB</span> Clear Client Logs</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushclientlogs\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Client Logs? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<br>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $login_flood;
echo " MB</span> Clear Login Flood</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushflood\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Login Flood? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                    <div class=\"col-md-2\"></div>\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $mag_events;
echo " MB</span> Clear Mag Events</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushevents\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Mag Events? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<br>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $mag_claims;
echo " MB</span> Clear Mag Claims</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushmagclaims\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Mag Claims? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                    <div class=\"col-md-2\"></div>\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $mag_logs;
echo " MB</span> Clear Mag Logs</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushmaglogs\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Mag Logs? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<br>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $watch_output;
echo " MB</span> Clear Folder Watch Out</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushwatchfolder\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Folder Watch Output? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\"></div>\n                                                    <label class=\"col-md-3 col-form-label\"><span class=\"btn btn-pink btn-xs btn-fixed waves-effect waves-light\">";
echo $tmdb_async;
echo " MB</span> Clear Re-Process TMDb</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushtmdbasync\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Re-Process TMDb Data? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<br>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                    <label class=\"col-md-3 col-form-label\">Lock ISP</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?lockisp\">\n                                                        <button onclick=\"return confirm('Do you want to Lock ISP? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                    <div class=\"col-md-2\"></div>\n                                                    <label class=\"col-md-3 col-form-label\">Unlock ISP</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?unlockisp\">\n                                                        <button onclick=\"return confirm('Do you want to Unlock ISP? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<br>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                    <label class=\"col-md-3 col-form-label\">Device Lock</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?lockstb\">\n                                                        <button onclick=\"return confirm('Do you want to Device Lock? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                    <div class=\"col-md-2\"></div>\n                                                    <label class=\"col-md-3 col-form-label\">Device Unlock</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?unlockstb\">\n                                                        <button onclick=\"return confirm('Do you want to Device Unlock? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<br>\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                    <!--<label class=\"col-md-3 col-form-label\">Clear Duplicate Movies</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?flushduplicatemovies\">\n                                                        <button onclick=\"return confirm('Do you want to Clear Duplicate Movies? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\"></div>-->\n\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-3 col-form-label\">Clear ISP</label>\n                                                    <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"log_tools.php?clearisp\">\n                                                        <button onclick=\"return confirm('Do you want to Clear ISP? If you confirm you will not go back.')\" type=\"button\" class=\"btn btn-success\" style=\"width:100%;\">\n                                                        Run Action\n                                                        </button>\n                                                        </a>\n                                                    </div>\n                                                </div>\n                                            </div> \n                                        </div> <!-- tab-content -->\n\t\t\t\t\t\t\t\t</div> <!-- end #basicwizard-->\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\n\t\t<script>\n\t\t\$(window).on('load', function() {\n\t\tvar elem = document.querySelector('#clear_log_auto');\n\t\tvar init = new Switchery(elem);\n\t\t\$('#clear_log_tables').select2();\n\t\t}\n\t\t);\n\t\t</script>\n    </body>\n</html>";

?>