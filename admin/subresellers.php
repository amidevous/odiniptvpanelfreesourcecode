<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "subresellers")) {
    exit;
}
$rMemberGroups = getMemberGroups();
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n\t\t\t\t\t\t\t\t\t\t";
if (hasPermissions("adv", "mng_regusers")) {
    echo "                                        <a href=\"reg_users.php\">\n                                            <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-account-group\"></i> ";
    echo $_["registered_users"];
    echo "                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
}
if (hasPermissions("adv", "subreseller")) {
    echo "                                        <a href=\"subreseller_setup.php\">\n                                            <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["setup_access"];
    echo "                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["subreseller_setup"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <table id=\"datatable\" class=\"table table-hover dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                            <th>";
echo $_["reseller_owner"];
echo "</th>\n                                            <th>";
echo $_["subreseller"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        ";
foreach (getSubresellerSetups() as $rItem) {
    echo "                                        <tr id=\"setup-";
    echo $rItem["id"];
    echo "\">\n                                            <td class=\"text-center\">";
    echo $rItem["id"];
    echo "</td>\n                                            <td>";
    echo $rMemberGroups[$rItem["reseller"]]["group_name"];
    echo "</td>\n                                            <td>";
    echo $rMemberGroups[$rItem["subreseller"]]["group_name"];
    echo "</td>\n                                            <td class=\"text-center\">\n\t\t\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "subreseller")) {
        echo "                                                <div class=\"btn-group\">\n                                                    <a href=\"./subreseller_setup.php?id=";
        echo $rItem["id"];
        echo "\"><button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                    <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(";
        echo $rItem["id"];
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
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\n        <script>\n        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["are_you_sure_you_want_to_delete_this_setup"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=subreseller_setup&sub=\" + rType + \"&id=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$(\"#setup-\" + rID).remove();\n                        \$.toast(\"";
echo $_["setup_successfully_deleted"];
echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                } else {\n                    \$.toast(\"";
echo $_["an_error_occured_while_processing_your_request"];
echo "\");\n                }\n            });\n        }\n\n        \$(document).ready(function() {\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                bInfo: false,\n                searching: false,\n                paging: false,\n                responsive: false\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n    </body>\n</html>";

?>