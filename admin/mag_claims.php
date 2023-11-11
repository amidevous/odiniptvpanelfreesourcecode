<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "mag_claims")) {
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
echo "        <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">Mag Claims</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <table id=\"datatable\" class=\"table dt-responsive nowrap\">\r\n                                    <thead>\r\n                                        <tr>\r\n                                            <th class=\"text-center\">ID</th>\r\n                                            <th class=\"text-center\">MAG</th>\r\n                                            <th class=\"text-center\">Stream</th>\r\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">TYPE</th>\r\n                                            <th class=\"text-center\">Date</th>\r\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">Action</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody>\r\n                                        ";
foreach (getMagClaims() as $rIP) {
    echo "                                            <td class=\"text-center\">";
    echo $rIP["id"];
    echo "</td>\r\n                                            <td class=\"text-center\">";
    echo $rIP["mag_id"];
    echo "</td>\r\n                                            <td class=\"text-center\">";
    echo $rIP["stream_id"];
    echo "</td>\r\n\t\t\t\t\t\t\t\t\t\t\t<td class=\"text-center\">";
    echo $rIP["real_type"];
    echo "</td>\r\n                                            <td class=\"text-center\">";
    echo $rIP["date"];
    echo "</td>\r\n\t\t\t\t\t\t\t\t\t\t    <td class=\"text-center\">\r\n                                                <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(";
    echo $rIP["id"];
    echo ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>\r\n                                            </td>\r\n                                        </tr>\r\n                                        ";
}
echo "                                    </tbody>\r\n                                </table>\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/libs/pdfmake/pdfmake.min.js\"></script>\r\n        <script src=\"assets/libs/pdfmake/vfs_fonts.js\"></script>\r\n\r\n        <script>\r\n        function api(rID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('Are you sure you want to delete this Error?') == false) {\r\n                    return;\r\n                }\r\n            }\r\n            \$.getJSON(\"./api.php?action=mag_claims&sub=\" + rType + \"&mag_id=\" + rID, function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"delete\") {\r\n                        \$(\"#id-\" + rID).remove();\r\n                        \$.toast(\"Successfully deleted.\");\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {\r\n                        \$(this).remove();\r\n                    });\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                } else {\r\n                    \$.toast(\"An error occured while processing your request.\");\r\n                }\r\n            });\r\n        }\r\n        \r\n        \$(document).ready(function() {\r\n            \$(\"#datatable\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    }\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                },\r\n                responsive: false\r\n            });\r\n            \$(\"#datatable\").css(\"width\", \"100%\");\r\n        });\r\n        </script>\r\n\r\n        <!-- App js-->\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n    </body>\r\n</html>";

?>