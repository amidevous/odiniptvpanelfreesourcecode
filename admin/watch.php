<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "folder_watch")) {
    exit;
}
if (isset($_GET["kill"]) && isset($rAdminSettings["watch_pid"])) {
    exec("pkill -9 " . $rAdminSettings["watch_pid"]);
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
echo "        <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n\t\t\t\t\t\t\t\t\t\t";
if (hasPermissions("adv", "folder_watch_settings")) {
    echo "                                        <a href=\"settings_watch.php\">\n                                            <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\">\n                                                ";
    echo $_["settings"];
    echo "                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
}
if (hasPermissions("adv", "folder_watch_output")) {
    echo "                                        <a href=\"watch_output.php\">\n                                            <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm\">\n                                                ";
    echo $_["watch_output"];
    echo "                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                        <a href=\"watch.php?kill=1\">\n                                            <button type=\"button\" class=\"btn btn-danger waves-effect waves-light btn-sm\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["kill_process"];
echo "\">\n                                                <i class=\"mdi mdi-hammer\"></i>\n                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
if (hasPermissions("adv", "folder_watch_add")) {
    echo "                                        <a href=\"watch_add.php\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\"  data-original-title=\"";
    echo $_["add_folder"];
    echo "\">\n                                                <i class=\"mdi mdi-plus\"></i>\n                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["folder_watch"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        ";
if (isset($_GET["kill"])) {
    echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["folder_watch_process"];
    echo "                        </div>\n                        ";
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <table id=\"datatable\" class=\"table table-hover dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">";
echo $_["status"];
echo "</th>\n                                            <th>";
echo $_["type"];
echo "</th>\n                                            <th>";
echo $_["server_name"];
echo "</th>\n                                            <th>";
echo $_["directory"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["last_run"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        ";
foreach (getWatchFolders() as $rFolder) {
    if (0 < $rFolder["last_run"]) {
        $rDate = date("Y-m-d H:i:s", $rFolder["last_run"]);
    } else {
        $rDate = "Never";
    }
    if ($rFolder["active"] == 1) {
        $rStatus = "<i data-toggle='tooltip' data-placement='top' title='' data-original-title='Scan Folder Enable' class='text-success fas fa-square'></i>";
    } else {
        $rStatus = "<i data-toggle='tooltip' data-placement='top' title='' data-original-title='Scan Folder Disable' class='text-danger fas fa-square'></i>";
    }
    echo "                                        <tr id=\"folder-";
    echo $rFolder["id"];
    echo "\">\n                                            <td class=\"text-center\">";
    echo $rFolder["id"];
    echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t<td class=\"text-center\">";
    echo $rStatus;
    echo "</td>\n                                            <td>";
    echo ["movie" => "Movies", "series" => "Series"][$rFolder["type"]];
    echo "</td>\n                                            <td>";
    echo $rServers[$rFolder["server_id"]]["server_name"];
    echo "</td>\n                                            <td>";
    echo $rFolder["directory"];
    echo "</td>\n                                            <td class=\"text-center\">";
    echo $rDate;
    echo "</td>\n                                            <td class=\"text-center\">\n                                                <div class=\"btn-group\">\n                                                    <a href=\"./watch_add.php?id=";
    echo $rFolder["id"];
    echo "\"><button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                    <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(";
    echo $rFolder["id"];
    echo ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>\n                                                </div>\n                                            </td>\n                                        </tr>\n                                        ";
}
echo "                                    </tbody>\n                                </table>\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\n        <script>\n        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["are_you_sure_you_want_to_delete_this_profile"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=folder&sub=\" + rType + \"&folder_id=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$(\"#folder-\" + rID).remove();\n                        \$.toast(\"";
echo $_["folder_successfully_deleted"];
echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                } else {\n                    \$.toast(\"";
echo $_["an_error_occured_while_processing_your_request"];
echo "\");\n                }\n            });\n        }\n        \n        \$(document).ready(function() {\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                pageLength: 50,\n                lengthMenu: [10, 25, 50, 250, 500, 1000],\n                responsive: false,\n\t\t\t\tstateSave: true\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n    </body>\n</html>";

?>