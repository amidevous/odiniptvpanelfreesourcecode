<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "block_isps")) {
    exit;
}
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
}
echo "        <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                        <a href=\"isp.php\">\r\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-plus\"></i> ";
echo $_["block_isp"];
echo "                                            </button>\r\n                                        </a>\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $_["blocked_isps"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <table id=\"datatable\" class=\"table table-hover dt-responsive nowrap\">\r\n                                    <thead>\r\n                                        <tr>\r\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\r\n                                            <th>";
echo $_["isp_name"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["blocked"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody>\r\n                                        ";
foreach (getISPs() as $rISP) {
    echo "                                        <tr id=\"isp-";
    echo $rISP["id"];
    echo "\">\r\n                                            <td class=\"text-center\">";
    echo $rISP["id"];
    echo "</td>\r\n                                            <td>";
    echo $rISP["isp"];
    echo "</td>\r\n                                            <td class=\"text-center\">\r\n                                                ";
    if ($rISP["blocked"]) {
        echo "                                                <i class=\"text-success fas fa-circle\"></i>\r\n                                                ";
    } else {
        echo "                                                <i class=\"text-dark far fa-circle\"></i>\r\n                                                ";
    }
    echo "                                            </td>\r\n                                            <td class=\"text-center\">\r\n                                                <div class=\"btn-group\">\r\n                                                    <a href=\"./isp.php?id=";
    echo $rISP["id"];
    echo "\"><button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\r\n                                                    <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(";
    echo $rISP["id"];
    echo ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>\r\n                                                </div>\r\n                                            </td>\r\n                                        </tr>\r\n                                        ";
}
echo "                                    </tbody>\r\n                                </table>\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n\r\n        <script>\r\n        function api(rID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('";
echo $_["are_you_sure_you_want_to_delete_this_isp"];
echo "') == false) {\r\n                    return;\r\n                }\r\n            }\r\n            \$.getJSON(\"./api.php?action=isp&sub=\" + rType + \"&isp_id=\" + rID, function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"delete\") {\r\n                        \$(\"#isp-\" + rID).remove();\r\n                        \$.toast(\"";
echo $_["isp_successfully_deleted"];
echo "\");\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {\r\n                        \$(this).remove();\r\n                    });\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                } else {\r\n                    \$.toast(\"";
echo $_["an_error_occured"];
echo "\");\r\n                }\r\n            });\r\n        }\r\n        \r\n        \$(document).ready(function() {\r\n            \$(\"#datatable\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    }\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                },\r\n                responsive: false\r\n            });\r\n            \$(\"#datatable\").css(\"width\", \"100%\");\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>