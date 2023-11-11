<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "security_center")) {
    exit;
}
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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                        ";
if ($rPermissions["is_admin"]) {
    echo "                                        ";
}
if (!$detect->isMobile()) {
    echo "                                        <a href=\"javascript:location.reload();\">\r\n                                            <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-refresh\"></i> Refresh\r\n                                            </button>\r\n                                        </a>\r\n                                        ";
} else {
    echo "                                        <a href=\"javascript:location.reload();\" onClick=\"toggleAuto();\" style=\"margin-right:10px;\">\r\n                                            <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-refresh\"></i> Refresh\r\n                                            </button>\r\n                                        </a>\r\n                                        ";
}
echo "                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">Security Center</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title -->\r\n            <h5 class=\"page-title\">Restream Finder</p></h5> \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <table id=\"datatable\" class=\"table table-bordered table-hover table-sm table-striped font-normal\">\r\n                                    <thead>\r\n                                        <tr>\r\n                                            <th class=\"text-center\">User ID</th>\r\n                                            <!--<th class=\"text-center\">MAG Adress</th>-->\r\n                                            <th class=\"text-center\">Username</th>\r\n                                            <!--<th class=\"text-center\">Username</th>-->\r\n                                            <!--<th class=\"text-center\">Password</th>-->\r\n                                            <th class=\"text-center\">Channel</th>\r\n                                            <th class=\"text-center\">Max Connections</th>                                            \r\n                                            <th class=\"text-center\">Active Connections</th>\r\n                                            <th class=\"text-center\">Total Active Connections</th>\r\n                                            <th class=\"text-center\">Action</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody>\r\n                                        ";
foreach (getSecurityCenter() as $rIP) {
    echo "                                        <tr id=\"ip-";
    echo $rIP["id"];
    echo "\">\r\n                                            <td class=\"text-center\"><a href=\"./user.php?id=";
    echo $rIP["id"];
    echo "\">";
    echo $rIP["id"];
    echo "</td>\r\n                                            <!--<td class=\"text-center\">";
    echo $rIP["FROM_BASE64(mac)"];
    echo "</td>-->\r\n                                            <td class=\"text-center\">";
    $MAG_or_M3U = $rIP["FROM_BASE64(mac)"];
    if (empty($MAG_or_M3U)) {
        echo $rIP["username"];
    }
    if (isset($MAG_or_M3U)) {
        echo $rIP["FROM_BASE64(mac)"];
    }
    echo "</td>\r\n                                            <!--<td class=\"text-center\">";
    echo $rIP["username"];
    echo "</td>-->\r\n                                            <!--<td class=\"text-center\">";
    echo $rIP["password"];
    echo "</td>-->\r\n                                            <td class=\"text-center\">";
    echo $rIP["stream_display_name"];
    echo "</td>\r\n                                            <td class=\"text-center\">";
    echo $rIP["max_connections"];
    echo "</td>\r\n                                            <td class=\"text-center\">";
    echo $rIP["active_connections"];
    echo "</td>\r\n                                            <td class=\"text-center\">";
    echo $rIP["total_active_connections"];
    echo "</td>\r\n                                            <td class=\"text-center\"><a href=\"./user.php?id=";
    echo $rIP["id"];
    echo "\"><button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"far fa-eye\"></i></button></a></td>\r\n                                            <!--<td class=\"text-center\">";
    if (0 < $rIP["is_restreamer"]) {
        echo "<i class=\"text-success fas fa-check fa-lg\"></i>";
    } else {
        echo "<i class=\"text-danger fas fa-times fa-lg\"></i>";
    }
    rIP["is_restreamer"];
    echo "</td>-->\r\n                                            </td>\r\n                                        </tr>\r\n                                        ";
}
echo "                                    </tbody>\r\n                                </table>\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n                \r\n            </br><h5 class=\"page-title\">Check Leaked MAG and M3U Lines</p></h5>\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <table id=\"datatable2\" class=\"table table-bordered table-hover table-sm table-striped font-normal\">\r\n                                    <thead>\r\n                                        <tr>\r\n                                            <th class=\"text-center\">User ID</th>\r\n                                            <!--<th class=\"text-center\">MAG Adress</th>-->\r\n                                            <th class=\"text-center\">Username / Device</th>\r\n                                            <!--<th class=\"text-center\">Username</th>-->\r\n                                            <!--<th class=\"text-center\">Password</th>-->\r\n                                            <th class=\"text-center\">Containers</th>\r\n                                            <th class=\"text-center\">Flags</th>                                            \r\n                                            <th class=\"text-center\">User IP's</th>\r\n                                            <th class=\"text-center\">Actions</th>\r\n                                            <!--<th class=\"text-center\">Is Restreamer</th>-->\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody>\r\n                                        ";
foreach (getLeakedLines() as $rIP) {
    echo "                                        <tr id=\"ip-";
    echo $rIP["id"];
    echo "\">\r\n                                            <td class=\"text-center\"><a href=\"./user.php?id=";
    echo $rIP["user_id"];
    echo "\">";
    echo $rIP["user_id"];
    echo "</td>\r\n                                            <!--<td class=\"text-center\">";
    echo $rIP["FROM_BASE64(mac)"];
    echo "</td>-->\r\n                                            <td class=\"text-center\">";
    $MAG_or_M3U = $rIP["FROM_BASE64(mac)"];
    if (empty($MAG_or_M3U)) {
        echo $rIP["username"];
    }
    if (isset($MAG_or_M3U)) {
        echo $rIP["FROM_BASE64(mac)"];
    }
    echo "</td>\r\n                                            <!--<td class=\"text-center\">";
    echo $rIP["username"];
    echo "</td>-->\r\n                                            <!--<td class=\"text-center\">";
    echo $rIP["password"];
    echo "</td>-->\r\n                                            <td class=\"text-center\">";
    echo $rIP["GROUP_CONCAT(DISTINCT container)"];
    echo "</td>\r\n                                            <td class=\"text-center\">";
    echo $rIP["GROUP_CONCAT(DISTINCT geoip_country_code)"];
    echo "</td>\r\n                                            <td class=\"text-center\">";
    echo $rIP["GROUP_CONCAT(DISTINCT user_ip)"];
    echo "</td>\r\n                                            <td class=\"text-center\"><a href=\"./user.php?id=";
    echo $rIP["user_id"];
    echo "\"><button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"far fa-eye\"></i></button></a></td>\r\n                                            <!--<td class=\"text-center\">";
    if (0 < $rIP["is_restreamer"]) {
        echo "<i class=\"text-success fas fa-check fa-lg\"></i>";
    } else {
        echo "<i class=\"text-danger fas fa-times fa-lg\"></i>";
    }
    rIP["is_restreamer"];
    echo "</td>-->\r\n                                            </td>\r\n                                        </tr>\r\n                                        ";
}
echo "                                    </tbody>\r\n                                </table>\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n            </div> <!-- end container -->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/libs/pdfmake/pdfmake.min.js\"></script>\r\n        <script src=\"assets/libs/pdfmake/vfs_fonts.js\"></script>\r\n\r\n        <script>\r\n        function api(rID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('Are you sure you want to delete this IP? This cannot be undone!') == false) {\r\n                    return;\r\n                } else {\r\n\t\t\t\t\t\$.toast(\"The IP is being unblocked from each server...\");\r\n\t\t\t\t\tif (rType == \"delete\") {\r\n                        \$(\"#ip-\" + rID).remove();\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {\r\n                        \$(this).remove();\r\n                    });\r\n\t\t\t\t\t\$('[data-toggle=\"tooltip\"]').tooltip();\r\n\t\t\t\t}\r\n            }\r\n            \$.getJSON(\"./api.php?action=ip&sub=\" + rType + \"&ip=\" + rID, function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"delete\") {\r\n                        \$.toast(\"IP successfully deleted.\");\r\n                    }\r\n                } else {\r\n                    \$.toast(\"An error occured while processing your request.\");\r\n                }\r\n            });\r\n        }\r\n        \r\n        \$(document).ready(function() {\r\n            \$(\"#datatable\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    }\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                },\r\n                responsive: false\r\n            });\r\n            \$(\"#datatable\").css(\"width\", \"100%\");\r\n        });\r\n        \r\n        \$(document).ready(function() {\r\n            \$(\"#datatable2\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    }\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                },\r\n                responsive: false\r\n            });\r\n            \$(\"#datatable2\").css(\"width\", \"100%\");\r\n        });\r\n        </script>\r\n\r\n        <!-- App js-->\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n    </body>\r\n</html>";

?>