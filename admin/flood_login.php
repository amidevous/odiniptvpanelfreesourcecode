<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "flood_login")) {
    exit;
}
if (isset($_GET["flush"])) {
    flushLogins();
    header("Location: ./flood_login.php");
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
echo "        <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"flood_login.php?flush\">\n                                            <button type=\"button\" class=\"btn btn-danger waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-trash-can\"></i> Flush IP's\n                                            </button>\n                                        </a>\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">Blocked False Logins</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <table id=\"datatable\" class=\"table dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">ID</th>\n                                            <th class=\"text-center\">IP Address</th>\n                                            <th>Login</th>\n                                            <th class=\"text-center\">Date</th>\n                                            <th class=\"text-center\">Action</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        ";
foreach (getBlockedLogins() as $rIP) {
    echo "                                        <tr id=\"ip-";
    echo $rIP["id"];
    echo "\">\n                                            <td class=\"text-center\">";
    echo $rIP["id"];
    echo "</td>\n                                            <td class=\"text-center\">";
    echo $rIP["ip"];
    echo "</td>\n                                            <td>";
    echo $rIP["username"];
    echo "</td>\n                                            <td class=\"text-center\">";
    echo $rIP["dateadded"];
    echo "</td>\n                                            <td class=\"text-center\">\n                                                <button type=\"button\" class=\"btn btn-outline-danger waves-effect waves-light btn-xs\" onClick=\"api(";
    echo $rIP["id"];
    echo ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>\n                                            </td>\n                                        </tr>\n                                        ";
}
echo "                                    </tbody>\n                                </table>\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/pdfmake/pdfmake.min.js\"></script>\n        <script src=\"assets/libs/pdfmake/vfs_fonts.js\"></script>\n\n        <script>\n        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('Are you sure you want to delete this IP? This cannot be undone!') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=login_flood&sub=\" + rType + \"&ip=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$(\"#ip-\" + rID).remove();\n                        \$.toast(\"IP successfully deleted.\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                } else {\n                    \$.toast(\"An error occured while processing your request.\");\n                }\n            });\n        }\n        \n        \$(document).ready(function() {\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                responsive: false\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n\n        <!-- App js-->\n        <script src=\"assets/js/app.min.js\"></script>\n    </body>\n</html>";

?>