<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "epg")) {
    exit;
}
$rEPGs = getEPGs();
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\" onClick=\"forceUpdate();\" id=\"force_update\">\n                                            <i class=\"mdi mdi-refresh\"></i> ";
echo $_["force_epg_reload"];
echo "                                        </button>\n\t\t\t\t\t\t\t\t\t\t";
if (hasPermissions("adv", "add_epg")) {
    echo "                                        <a href=\"epg.php\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_epg"];
    echo "                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["epgs"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <table id=\"datatable\" class=\"table table-hover dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                            <th>";
echo $_["epg_name"];
echo "</th>\n                                            <th>";
echo $_["source"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["days_to_keep"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["last_updated"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        ";
foreach ($rEPGs as $rEPG) {
    echo "                                        <tr id=\"server-";
    echo $rEPG["id"];
    echo "\">\n                                            <td class=\"text-center\">";
    echo $rEPG["id"];
    echo "</td>\n                                            <td>";
    echo $rEPG["epg_name"];
    echo "</td>\n                                            <td>";
    echo parse_url($rEPG["epg_file"])["host"];
    echo "</td>\n                                            <td class=\"text-center\">";
    echo $rEPG["days_keep"];
    echo "</td>\n                                            <td class=\"text-center\">";
    if ($rEPG["last_updated"]) {
        echo date("Y-m-d H:i:s", $rEPG["last_updated"]);
    } else {
        echo $_["never"];
    }
    echo "</td>\n                                            <td class=\"text-center\">\n\t\t\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "epg_edit")) {
        echo "                                                <div class=\"btn-group\">\n                                                    <a href=\"./epg.php?id=";
        echo $rEPG["id"];
        echo "\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["edit_epg"];
        echo "\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                    <button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["delete_epg"];
        echo "\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(";
        echo $rEPG["id"];
        echo ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                ";
    } else {
        echo "--";
    }
    echo "                                            </td>\n                                        </tr>\n                                        ";
}
echo "                                    </tbody>\n                                </table>\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n\n        <script>\n        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["epg_confirm"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=epg&sub=\" + rType + \"&epg_id=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$(\"#server-\" + rID).remove();\n                        \$.toast(\"";
echo $_["epg_deleted"];
echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                } else {\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                }\n            });\n        }\n        \n        function forceUpdate() {\n\t\t\t\$(\"#force_update\").attr(\"disabled\", true);\n            \$.toast(\"";
echo $_["updating_epg"];
echo "\");\n            \$.getJSON(\"./api.php?action=force_epg\", function(data) {\n                \$.toast(\"";
echo $_["updated_epg"];
echo "\");\n\t\t\t\t\$(\"#force_update\").attr(\"disabled\", false);\n            });\n        }\n        \n        \$(document).ready(function() {\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                responsive: false\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n\n        <!-- App js-->\n        <script src=\"assets/js/app.min.js\"></script>\n    </body>\n</html>";

?>