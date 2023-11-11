<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "bouquets")) {
    exit;
}
$rBouquets = getBouquets();
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
if (hasPermissions("adv", "add_bouquet")) {
    echo "                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"bouquet.php\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_bouquet"];
    echo "                                            </button>\n                                        </a>\n                                    </li>\n                                </ol>\n                            </div>\n\t\t\t\t\t\t\t";
}
echo "                            <h4 class=\"page-title\">";
echo $_["bouquets"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <table id=\"datatable\" class=\"table table-hover dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                            <th>";
echo $_["bouquet_name"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["streams"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["series"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        ";
foreach ($rBouquets as $rBouquet) {
    echo "                                        <tr id=\"bouquet-";
    echo $rBouquet["id"];
    echo "\">\n                                            <td class=\"text-center\">";
    echo $rBouquet["id"];
    echo "</td>\n                                            <td>";
    echo $rBouquet["bouquet_name"];
    echo "</td>\n                                            <td class=\"text-center\">";
    echo count(json_decode($rBouquet["bouquet_channels"], true));
    echo "</td>\n                                            <td class=\"text-center\">";
    echo count(json_decode($rBouquet["bouquet_series"], true));
    echo "</td>\n                                            <td class=\"text-center\">\n\t\t\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "edit_bouquet")) {
        echo "                                                <div class=\"btn-group\">\n                                                    <a href=\"./bouquet_order.php?id=";
        echo $rBouquet["id"];
        echo "\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["reorder_bouquet"];
        echo "\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-format-line-spacing\"></i></button></a>\n                                                    <a href=\"./bouquet.php?id=";
        echo $rBouquet["id"];
        echo "\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["edit_bouquet"];
        echo "\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\n                                                    <button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["delete_bouquet"];
        echo "\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(";
        echo $rBouquet["id"];
        echo ", 'delete');\"\"><i class=\"mdi mdi-close\"></i></button>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t";
    } else {
        echo "--";
    }
    echo "                                            </td>\n                                        </tr>\n                                        ";
}
echo "                                    </tbody>\n                                </table>\n\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n\n        <script>\n        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["delete_confirm"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=bouquet&sub=\" + rType + \"&bouquet_id=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$(\"#bouquet-\" + rID).remove();\n                        \$.toast(\"";
echo $_["deleted_bouquet"];
echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                } else {\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                }\n            });\n        }\n        \n        \$(document).ready(function() {\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                pageLength: 50,\n                lengthMenu: [10, 25, 50, 250, 500, 1000],\n                responsive: false,\n\t\t\t\tstateSave: true\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n\n        <!-- App js-->\n        <script src=\"assets/js/app.min.js\"></script>\n    </body>\n</html>";

?>