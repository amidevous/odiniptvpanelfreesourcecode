<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "mng_groups")) {
    exit;
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n\t\t\t\t\t\t\t";
if (hasPermissions("adv", "add_group")) {
    echo "                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"group.php\" style=\"margin-right:10px;\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_group"];
    echo "                                            </button>\n                                        </a>\n                                    </li>\n                                </ol>\n                            </div>\n\t\t\t\t\t\t\t";
}
echo "                            <h4 class=\"page-title\">";
echo $_["groups"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <table id=\"datatable\" class=\"table table-hover dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                            <th>";
echo $_["group_name"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["is_admin"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["is_reseller"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["is_banned"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        ";
foreach (getMemberGroups() as $rGroup) {
    echo "                                        <tr id=\"group-";
    echo $rGroup["group_id"];
    echo "\">\n                                            <td class=\"text-center\">";
    echo $rGroup["group_id"];
    echo "</td>\n                                            <td>";
    echo $rGroup["group_name"];
    echo "</td>\n                                            <td class=\"text-center\">\n                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                    <input disabled data-id=\"";
    echo $rGroup["group_id"];
    echo "\" data-name=\"is_admin\" type=\"checkbox\" class=\"custom-control-input\" id=\"is_admin_";
    echo $rGroup["group_id"];
    echo "\" name=\"is_admin\"";
    if ($rGroup["is_admin"]) {
        echo " checked";
    }
    echo ">\n                                                    <label class=\"custom-control-label\" for=\"(is_admin_";
    echo $rGroup["group_id"];
    echo "\"></label>\n                                                </div>\n                                            </td>\n                                            <td class=\"text-center\">\n                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                    <input disabled data-id=\"";
    echo $rGroup["group_id"];
    echo "\" data-name=\"is_reseller\" type=\"checkbox\" class=\"custom-control-input\" id=\"is_reseller_";
    echo $rGroup["group_id"];
    echo "\" name=\"is_reseller\"";
    if ($rGroup["is_reseller"]) {
        echo " checked";
    }
    echo ">\n                                                    <label class=\"custom-control-label\" for=\"is_reseller_";
    echo $rGroup["group_id"];
    echo "\"></label>\n                                                </div>\n                                            </td>\n                                            <td class=\"text-center\">\n                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                    <input disabled data-id=\"";
    echo $rGroup["group_id"];
    echo "\" data-name=\"is_banned\" type=\"checkbox\" class=\"custom-control-input\" id=\"is_banned_";
    echo $rGroup["group_id"];
    echo "\" name=\"is_banned\"";
    if ($rGroup["is_banned"]) {
        echo " checked";
    }
    echo ">\n                                                    <label class=\"custom-control-label\" for=\"is_banned_";
    echo $rGroup["group_id"];
    echo "\"></label>\n                                                </div>\n                                            </td>\n                                            <td class=\"text-center\">\n                                                <div class=\"btn-group\">\n                                                    ";
    if (hasPermissions("adv", "edit_group")) {
        echo "                                                    <a href=\"./group.php?id=";
        echo $rGroup["group_id"];
        echo "\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["edit_group"];
        echo "\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                    ";
        if ($rGroup["can_delete"]) {
            echo "                                                    <button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
            echo $_["delete_group"];
            echo "\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(";
            echo $rGroup["group_id"];
            echo ", 'delete');\"\"><i class=\"mdi mdi-close\"></i></button>\n                                                    ";
        }
    }
    echo "                                                </div>\n                                            </td>\n                                        </tr>\n                                        ";
}
echo "                                    </tbody>\n                                </table>\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        \n        <!-- third party js ends -->\n\n        <script>\n        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["group_delete_confirm"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=group&sub=\" + rType + \"&group_id=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$(\"#group-\" + rID).remove();\n                        \$.toast(\"";
echo $_["group_deleted"];
echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                } else {\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                }\n            });\n        }\n        \$(document).ready(function() {\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                responsive: false,\n                paging: false,\n                bInfo: false\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n\n        <!-- App js-->\n        <script src=\"assets/js/app.min.js\"></script>\n    </body>\n</html>";

?>