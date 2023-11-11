<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "edit_bouquet")) {
    exit;
}
if (isset($_POST["bouquet_order_array"])) {
    set_time_limit(0);
    ini_set("mysql.connect_timeout", 0);
    ini_set("max_execution_time", 0);
    ini_set("default_socket_timeout", 0);
    $rOrder = json_decode($_POST["bouquet_order_array"], true);
    $rSort = 1;
    foreach ($rOrder as $rBouquetID) {
        $db->query("UPDATE `bouquets` SET `bouquet_order` = " . intval($rSort) . " WHERE `id` = " . intval($rBouquetID) . ";");
        $rSort++;
    }
    if (isset($_POST["confirmReplace"])) {
        $rUsers = getUserBouquets();
        foreach ($rUsers as $rUser) {
            $rBouquet = json_decode($rUser["bouquet"], true);
            $rBouquet = sortArrayByArray($rBouquet, $rOrder);
            $db->query("UPDATE `users` SET `bouquet` = '[" . ESC(join(",", $rBouquet)) . "]' WHERE `id` = " . intval($rUser["id"]) . ";");
        }
        $rPackages = getPackages();
        foreach ($rPackages as $rPackage) {
            $rBouquet = json_decode($rPackage["bouquets"], true);
            $rBouquet = sortArrayByArray($rBouquet, $rOrder);
            $db->query("UPDATE `packages` SET `bouquets` = '[" . ESC(join(",", $rBouquet)) . "]' WHERE `id` = " . intval($rPackage["id"]) . ";");
        }
        $_STATUS = 0;
    } else {
        $_STATUS = 1;
    }
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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <h4 class=\"page-title\">";
echo $_["bouquet_order"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success show\" role=\"alert\">\r\n                            ";
        echo $_["bouquet_order_has_taken_effect_and"];
        echo "                        </div>\r\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
        echo $_["bouquet_order_has_taken_effect_and"];
        echo "', \"success\");\r\n  \t\t\t\t\t</script>\r\n                        ";
    }
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-success show\" role=\"alert\">\r\n                            ";
            echo $_["bouquet_order_has_taken_effect_any"];
            echo "                        </div>\r\n                        ";
        } else {
            echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
            echo $_["bouquet_order_has_taken_effect_any"];
            echo "', \"success\");\r\n  \t\t\t\t\t</script>\r\n                        ";
        }
    }
}
echo "                        <div class=\"card\">\r\n                            <div class=\"card-body\">\r\n                                <form action=\"./bouquet_sort.php\" method=\"POST\" id=\"bouquet_sort_form\">\r\n                                    <input type=\"hidden\" id=\"bouquet_order_array\" name=\"bouquet_order_array\" value=\"\" />\r\n                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#order-stream\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-flower-tulip-outline mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["bouquet_order"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n                                            <div class=\"tab-pane\" id=\"order-stream\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <p class=\"sub-header\">\r\n                                                            ";
echo $_["bouquet_sort_text"];
echo "                                                        </p>\r\n                                                        <select multiple id=\"sort_bouquet\" class=\"form-control\" style=\"min-height:400px;\">\r\n                                                            ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                            <option value=\"";
    echo $rBouquet["id"];
    echo "\">";
    echo $rBouquet["bouquet_name"];
    echo "</option>\r\n                                                            ";
}
echo "                                                        </select>\r\n                                                        <div class=\"custom-control custom-checkbox add-margin-top-20\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"custom-control-input\" name=\"confirmReplace\" id=\"confirmReplace\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"custom-control-label\" for=\"confirmReplace\">";
echo $_["replace_bouquet_order"];
echo "</label>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\r\n                                                    <li class=\"list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveUp()\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveDown()\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveTop()\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveBottom()\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"AtoZ()\" class=\"btn btn-info\">";
echo $_["a_to_z"];
echo "</a>\r\n                                                    </li>\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\">";
echo $_["save_changes"];
echo "</button>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                        </div>\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </form>\r\n                            </div> <!-- end card-body -->\r\n                        </div> <!-- end card-->\r\n                    </div> <!-- end col -->\r\n                </div>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n        <script src=\"assets/libs/nestable2/jquery.nestable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.rowReorder.js\"></script>\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        \r\n        <script>\r\n        function AtoZ() {\r\n            \$(\"#sort_bouquet\").append(\$(\"#sort_bouquet option\").remove().sort(function(a, b) {\r\n                var at = \$(a).text().toUpperCase(), bt = \$(b).text().toUpperCase();\r\n                return (at > bt) ? 1 : ((at < bt) ? -1 : 0);\r\n            }));\r\n        }\r\n        function MoveUp() {\r\n            var rSelected = \$('#sort_bouquet option:selected');\r\n            if (rSelected.length) {\r\n                var rPrevious = rSelected.first().prev()[0];\r\n                if (\$(rPrevious).html() != '') {\r\n                    rSelected.first().prev().before(rSelected);\r\n                }\r\n            }\r\n        }\r\n        function MoveDown() {\r\n            var rSelected = \$('#sort_bouquet option:selected');\r\n            if (rSelected.length) {\r\n                rSelected.last().next().after(rSelected);\r\n            }\r\n        }\r\n        function MoveTop() {\r\n            var rSelected = \$('#sort_bouquet option:selected');\r\n            if (rSelected.length) {\r\n                rSelected.prependTo(\$('#sort_bouquet'));\r\n            }\r\n        }\r\n        function MoveBottom() {\r\n            var rSelected = \$('#sort_bouquet option:selected');\r\n            if (rSelected.length) {\r\n                rSelected.appendTo(\$('#sort_bouquet'));\r\n            }\r\n        }\r\n        \r\n        \$(document).ready(function() {\r\n            \$('.select2').select2({width: '100%'});\r\n            \$(\"#bouquet_sort_form\").submit(function(e){\r\n                rOrder = [];\r\n                \$('#sort_bouquet option').each(function() {\r\n                    rOrder.push(\$(this).val());\r\n                });\r\n                \$(\"#bouquet_order_array\").val(JSON.stringify(rOrder));\r\n            });\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>