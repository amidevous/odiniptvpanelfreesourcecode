<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "mng_packages")) {
    exit;
}
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n\t\t\t\t\t\t\t";
if (hasPermissions("adv", "add_packages")) {
    echo "                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"package.php\" style=\"margin-right:10px;\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_package"];
    echo "                                            </button>\n                                        </a>\n                                    </li>\n                                </ol>\n                            </div>\n\t\t\t\t\t\t\t";
}
echo "                            <h4 class=\"page-title\">";
echo $_["packages"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <table id=\"datatable\" class=\"table table-hover dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                            <th>";
echo $_["package_name"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["trial"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["official"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["create_mag"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["only_mag"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["create_enigma"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["only_enigma"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        ";
foreach (getPackages() as $rPackage) {
    echo "                                        <tr id=\"package-";
    echo $rPackage["id"];
    echo "\">\n                                            <td class=\"text-center\">";
    echo $rPackage["id"];
    echo "</td>\n                                            <td>";
    echo $rPackage["package_name"];
    echo "</td>\n                                            <td class=\"text-center\">\n                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                    <input ";
    if (!hasPermissions("adv", "edit_package")) {
        echo "disabled ";
    }
    echo "data-id=\"";
    echo $rPackage["id"];
    echo "\" data-name=\"is_trial\" type=\"checkbox\" class=\"custom-control-input\" id=\"is_trial_";
    echo $rPackage["id"];
    echo "\" name=\"is_trial\"";
    if ($rPackage["is_trial"]) {
        echo " checked";
    }
    echo ">\n                                                    <label class=\"custom-control-label\" for=\"is_trial_";
    echo $rPackage["id"];
    echo "\"></label>\n                                                </div>\n                                            </td>\n                                            <td class=\"text-center\">\n                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                    <input ";
    if (!hasPermissions("adv", "edit_package")) {
        echo "disabled ";
    }
    echo "data-id=\"";
    echo $rPackage["id"];
    echo "\" data-name=\"is_official\" type=\"checkbox\" class=\"custom-control-input\" id=\"is_official_";
    echo $rPackage["id"];
    echo "\" name=\"is_official\"";
    if ($rPackage["is_official"]) {
        echo " checked";
    }
    echo ">\n                                                    <label class=\"custom-control-label\" for=\"is_official_";
    echo $rPackage["id"];
    echo "\"></label>\n                                                </div>\n                                            </td>\n                                            <td class=\"text-center\">\n                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                    <input ";
    if (!hasPermissions("adv", "edit_package")) {
        echo "disabled ";
    }
    echo "data-id=\"";
    echo $rPackage["id"];
    echo "\" data-name=\"can_gen_mag\" type=\"checkbox\" class=\"custom-control-input\" id=\"can_gen_mag_";
    echo $rPackage["id"];
    echo "\" name=\"can_gen_mag\"";
    if ($rPackage["can_gen_mag"]) {
        echo " checked";
    }
    echo ">\n                                                    <label class=\"custom-control-label\" for=\"can_gen_mag_";
    echo $rPackage["id"];
    echo "\"></label>\n                                                </div>\n                                            </td>\n                                            <td class=\"text-center\">\n                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                    <input ";
    if (!hasPermissions("adv", "edit_package")) {
        echo "disabled ";
    }
    echo "data-id=\"";
    echo $rPackage["id"];
    echo "\" data-name=\"only_mag\" type=\"checkbox\" class=\"custom-control-input\" id=\"only_mag_";
    echo $rPackage["id"];
    echo "\" name=\"only_mag\"";
    if ($rPackage["only_mag"]) {
        echo " checked";
    }
    echo ">\n                                                    <label class=\"custom-control-label\" for=\"only_mag_";
    echo $rPackage["id"];
    echo "\"></label>\n                                                </div>\n                                            </td>\n                                            <td class=\"text-center\">\n                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                    <input ";
    if (!hasPermissions("adv", "edit_package")) {
        echo "disabled ";
    }
    echo "data-id=\"";
    echo $rPackage["id"];
    echo "\" data-name=\"can_gen_e2\" type=\"checkbox\" class=\"custom-control-input\" id=\"can_gen_e2_";
    echo $rPackage["id"];
    echo "\" name=\"can_gen_e2\"";
    if ($rPackage["can_gen_e2"]) {
        echo " checked";
    }
    echo ">\n                                                    <label class=\"custom-control-label\" for=\"can_gen_e2_";
    echo $rPackage["id"];
    echo "\"></label>\n                                                </div>\n                                            </td>\n                                            <td class=\"text-center\">\n                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                    <input ";
    if (!hasPermissions("adv", "edit_package")) {
        echo "disabled ";
    }
    echo "data-id=\"";
    echo $rPackage["id"];
    echo "\" data-name=\"only_e2\" type=\"checkbox\" class=\"custom-control-input\" id=\"only_e2_";
    echo $rPackage["id"];
    echo "\" name=\"only_e2\"";
    if ($rPackage["only_e2"]) {
        echo " checked";
    }
    echo ">\n                                                    <label class=\"custom-control-label\" for=\"only_e2_";
    echo $rPackage["id"];
    echo "\"></label>\n                                                </div>\n                                            </td>\n                                            <td class=\"text-center\">\n\t\t\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "edit_package")) {
        echo "                                                <div class=\"btn-group\">\n                                                    <a href=\"./package.php?id=";
        echo $rPackage["id"];
        echo "\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["edit_package"];
        echo "\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                    <button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
        echo $_["delete_package"];
        echo "\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(";
        echo $rPackage["id"];
        echo ", 'delete');\"\"><i class=\"mdi mdi-close\"></i></button>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t";
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
echo $_["package_delete_confirm"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=package&sub=\" + rType + \"&package_id=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$(\"#package-\" + rID).remove();\n                        \$.toast(\"";
echo $_["package_deleted"];
echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                } else {\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                }\n            });\n        }\n        ";
if (hasPermissions("adv", "edit_package")) {
    echo "        \$('input:checkbox').change(function() {\n            \$.getJSON(\"./api.php?action=package&sub=\" + \$(this).data(\"name\") + \"&package_id=\" + \$(this).data(\"id\") + \"&value=\" + (\$(this).is(\":checked\") ? 1 : 0), function(data) {\n                \$.toast(\"";
    echo $_["package_modified"];
    echo "\");\n            });\n        });\n        ";
}
echo "        \$(document).ready(function() {\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                responsive: false,\n                paging: false,\n                bInfo: false\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n    </body>\n</html>";

?>