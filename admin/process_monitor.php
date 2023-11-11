<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "process_monitor")) {
    exit;
}
if (!isset($_GET["server"]) || !isset($rServers[$_GET["server"]])) {
    header("Location: ./dashboard.php");
    exit;
}
if (isset($_GET["clear"])) {
    freeTemp($_GET["server"]);
    header("Location: ./process_monitor.php?server=" . $_GET["server"]);
    exit;
}
if (isset($_GET["clear_s"])) {
    freeStreams($_GET["server"]);
    header("Location: ./process_monitor.php?server=" . $_GET["server"]);
    exit;
}
$rStreams = getStreamPIDs($_GET["server"]);
$rFS = getFreeSpace($_GET["server"]);
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content\"><div class=\"container-fluid\">\r\n        ";
} else {
    echo "        <div class=\"wrapper\"><div class=\"container-fluid\">\r\n        ";
}
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                        <a href=\"javascript:location.reload();\" style=\"margin-right:10px;\">\r\n                                            <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-refresh\"></i> ";
echo $_["refresh"];
echo "                                            </button>\r\n                                        </a>\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $_["process_monitor"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        ";
if (0 < count($rFS)) {
    echo "                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <table class=\"table table-borderless mb-0\">\r\n                                    <thead class=\"thead-light\">\r\n                                        <tr>\r\n                                            <th>";
    echo $_["mount_point"];
    echo "</th>\r\n                                            <th class=\"text-center\">";
    echo $_["size"];
    echo "</th>\r\n                                            <th class=\"text-center\">";
    echo $_["used"];
    echo "</th>\r\n                                            <th class=\"text-center\">";
    echo $_["available"];
    echo "</th>\r\n                                            <th class=\"text-center\">";
    echo $_["used"];
    echo " %</th>\r\n                                            <th class=\"text-center\">";
    echo $_["actions"];
    echo "</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody>\r\n                                        ";
    foreach ($rFS as $rSystem) {
        echo "                                        <tr>\r\n                                            <td>";
        echo $rSystem["mount"];
        echo "</td>\r\n                                            <td class=\"text-center\">";
        echo $rSystem["size"];
        echo "</td>\r\n                                            <td class=\"text-center\">";
        echo $rSystem["used"];
        echo "</td>\r\n                                            <td class=\"text-center\">";
        echo $rSystem["avail"];
        echo "</td>\r\n                                            <td class=\"text-center\">";
        if (80 <= intval(rtrim($rSystem["percentage"], "%"))) {
            echo "<span class='text-danger'>" . $rSystem["percentage"] . "</span>";
        } else {
            echo $rSystem["percentage"];
        }
        echo "</td>\r\n                                            <td class=\"text-center\">\r\n                                                <div class=\"btn-group\">\r\n                                                    ";
        if (substr($rSystem["mount"], strlen($rSystem["mount"]) - 3, 3) == "tmp") {
            echo "                                                    <a href=\"./process_monitor.php?server=";
            echo $_GET["server"];
            echo "&clear\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
            echo $_["clear_temp"];
            echo "\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-close\"></i></button></a>\r\n                                                    ";
        } else {
            if (substr($rSystem["mount"], strlen($rSystem["mount"]) - 7, 7) == "streams") {
                echo "                                                    <a href=\"./process_monitor.php?server=";
                echo $_GET["server"];
                echo "&clear_s\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
                echo $_["clear_streams"];
                echo "\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-close\"></i></button></a>\r\n                                                    ";
            }
        }
        echo "                                                </div>\r\n                                            </td>\r\n                                        </tr>\r\n                                        ";
    }
    echo "                                    </tbody>\r\n                                </table>\r\n                            </div>\r\n                        </div>\r\n                        ";
}
echo "                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <form id=\"user_activity_search\">\r\n                                    <div class=\"form-group row mb-4\">\r\n                                        <div class=\"col-md-6\">\r\n                                            <input type=\"text\" class=\"form-control\" id=\"live_search\" value=\"xtreamc+\" placeholder=\"";
echo $_["search_processes"];
echo "...\">\r\n                                        </div>\r\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"live_filter\">";
echo $_["server"];
echo "</label>\r\n                                        <div class=\"col-md-3\">\r\n                                            <select id=\"live_filter\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                ";
foreach ($rServers as $rServer) {
    echo "                                                <option value=\"";
    echo $rServer["id"];
    echo "\"";
    if ($_GET["server"] == $rServer["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rServer["server_name"];
    echo "</option>\r\n                                                ";
}
echo "                                            </select>\r\n                                        </div>\r\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"live_show_entries\">";
echo $_["show"];
echo "</label>\r\n                                        <div class=\"col-md-1\">\r\n                                            <select id=\"live_show_entries\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\r\n                                                ";
}
echo "                                            </select>\r\n                                        </div>\r\n                                    </div>\r\n                                </form>\r\n                                <table id=\"datatable-activity\" class=\"table table-hover dt-responsive nowrap font-small\">\r\n                                    <thead>\r\n                                        <tr>\r\n                                            <th>";
echo $_["pid"];
echo "</th>\r\n                                            <th>";
echo $_["user"];
echo "</th>\r\n                                            <th>";
echo $_["type"];
echo "</th>\r\n                                            <th>";
echo $_["process"];
echo "</th>\r\n                                            <th>";
echo $_["cpu_%"];
echo "</th>\r\n                                            <th>";
echo $_["mem_mb"];
echo "</th>\r\n                                            <th>";
echo $_["time"];
echo "</th>\r\n                                            <th>";
echo $_["actions"];
echo "</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody>\r\n                                        ";
foreach (getPIDs($_GET["server"]) as $rProcess) {
    echo "                                        <tr>\r\n                                            <td>";
    echo $rProcess["pid"];
    echo "</td>\r\n                                            <td>";
    echo $rProcess["user"];
    echo "</td>\r\n                                            <td>";
    echo ["pid" => $_["main"] . " - ", "monitor_pid" => $_["monitor"] . " - ", "delay_pid" => $_["delayed"] . " - ", "activity" => $_["user_activity"] . " - ", "timeshift" => $_["timeshift"] . " - ", NULL => ""][$rStreams[$rProcess["pid"]]["pid_type"]] . [1 => $_["stream"], 2 => $_["movie"], 3 => $_["created_channel"], 4 => $_["radio"], 5 => $_["episode"], NULL => $_["system"]][$rStreams[$rProcess["pid"]]["type"]];
    echo "</td>\r\n                                            <td>";
    if (isset($rStreams[$rProcess["pid"]])) {
        echo "<a href='" . ["1" => "stream", "2" => "movie", "3" => "created_channel", "4" => "radio", "5" => "episode"][$rStreams[$rProcess["pid"]]["type"]] . ".php?id=" . $rStreams[$rProcess["pid"]]["id"] . "'>" . $rStreams[$rProcess["pid"]]["title"] . "</a>";
    } else {
        echo $rProcess["command"];
    }
    echo "</td>\r\n                                            <td>";
    echo number_format($rProcess["cpu"], 1);
    echo "</td>\r\n                                            <td>";
    echo number_format($rProcess["rss"] / 0, 0);
    echo "</td>\r\n                                            <td>";
    echo $rProcess["time"];
    echo "</td>\r\n                                            <td>\r\n                                                <div class=\"btn-group\">\r\n                                                    ";
    if (isset($rStreams[$rProcess["pid"]])) {
        echo "                                                    <a href=\"";
        echo ["1" => "stream", "2" => "movie", "3" => "created_channel", "4" => "radio", "5" => "episode"][$rStreams[$rProcess["pid"]]["type"]] . ".php?id=" . $rStreams[$rProcess["pid"]]["id"];
        echo "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["view"];
        echo "\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-eye\"></i></button></a>\r\n                                                    ";
    } else {
        echo "                                                    <button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-eye\"></i></button>\r\n                                                    ";
    }
    if ($rProcess["user"] == "xtreamc+") {
        echo "                                                    <button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["kill_process_info"];
        echo "\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"kill(";
        echo $_GET["server"];
        echo ", ";
        echo $rProcess["pid"];
        echo ");\"><i class=\"mdi mdi-close\"></i></button>\r\n                                                    ";
    } else {
        echo "                                                    <button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-close\"></i></button>\r\n                                                    ";
    }
    echo "                                                </div>\r\n                                            </td>\r\n                                        </tr>\r\n                                        ";
}
echo "                                    </tbody>\r\n                                </table>\r\n\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/js/pages/form-remember.js\"></script>\r\n\r\n        <!-- Datatables init -->\r\n        <script>\r\n        function kill(rServerID, rID) {\r\n            \$.getJSON(\"./api.php?action=process&pid=\" + rID + \"&server=\" + rServerID, function(data) {\r\n                if (data.result === true) {\r\n                    \$.toast(\"";
echo $_["connection_has_been_killed_wait"];
echo "\");\r\n                    \$.each(\$('.tooltip'), function (index, element) {\r\n                        \$(this).remove();\r\n                    });\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                } else {\r\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\r\n                }\r\n            });\r\n        }\r\n        \$(document).ready(function() {\r\n\t\t\t\$(window).keypress(function(event){\r\n\t\t\t\tif(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n\t\t\t});\r\n            \$('select').select2({width: '100%'});\r\n            \$(\"#datatable-activity\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    },\r\n                    infoFiltered: \"\"\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                },\r\n                responsive: false,\r\n                processing: true,\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2,4,5,6,7]}\r\n                ],\r\n                \r\n                ";
if (isset($_GET["mem"])) {
    echo "                order: [[ 5, \"desc\" ]],\r\n                ";
} else {
    echo "                order: [[ 4, \"desc\" ]],\r\n                ";
}
echo "                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\r\n                lengthMenu: [10, 25, 50, 250, 500, 1000]\r\n            });\r\n            \$(\"#datatable-activity\").css(\"width\", \"100%\");\r\n            \$('#live_search').keyup(function(){\r\n                \$('#datatable-activity').DataTable().search(\$(this).val()).draw();\r\n            });\r\n            \$('#live_show_entries').change(function(){\r\n                \$('#datatable-activity').DataTable().page.len(\$(this).val()).draw();\r\n            });\r\n            \$('#live_filter').change(function(){\r\n                window.location.href = \"./process_monitor.php?server=\" + \$(this).val();\r\n            });\r\n            \$('#datatable-activity').DataTable().search(\$('#live_search').val()).draw();\r\n        });\r\n        </script>\r\n\r\n        <!-- App js-->\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n    </body>\r\n</html>";

?>