<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "channel_order")) {
    exit;
}
if (isset($_POST["stream_order_array"])) {
    set_time_limit(0);
    ini_set("mysql.connect_timeout", 0);
    ini_set("max_execution_time", 0);
    ini_set("default_socket_timeout", 0);
    $rOrder = json_decode($_POST["stream_order_array"], true);
    $rSort = 0;
    foreach ($rOrder as $rStream) {
        $db->query("UPDATE `streams` SET `order` = " . intval($rSort) . " WHERE `id` = " . intval($rStream) . ";");
        $rSort++;
    }
}
$rOrdered = ["stream" => [], "movie" => [], "series" => [], "radio" => []];
$result = $db->query("SELECT `id`, `type`, `stream_display_name`, `category_id` FROM `streams` ORDER BY `order` ASC, `stream_display_name` ASC;");
if ($result && 0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        if ($row["type"] == 1 || $row["type"] == 3) {
            $rOrdered["stream"][] = $row;
        } else {
            if ($row["type"] == 2) {
                $rOrdered["movie"][] = $row;
            } else {
                if ($row["type"] == 4) {
                    $rOrdered["radio"][] = $row;
                } else {
                    if ($row["type"] == 5) {
                        $rOrdered["series"][] = $row;
                    }
                }
            }
        }
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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                        <a href=\"channel_order_alt.php\">\r\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\r\n                                                ";
echo $_["advanced"];
echo "                                            </button>\r\n                                        </a>\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $_["channel_order"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        ";
if ($rSettings["channel_number_type"] != "manual") {
    echo "                        <!--<div class=\"alert alert-danger show\" role=\"alert\">\r\n                            ";
    echo $_["channel_order_info"];
    echo "                        </div>\r\n                        ";
}
echo "-->\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\">\r\n                                <form action=\"./channel_order.php\" method=\"POST\" id=\"channel_order_form\">\r\n                                    <input type=\"hidden\" id=\"stream_order_array\" name=\"stream_order_array\" value=\"\" />\r\n                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#order-stream\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-play mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["streams"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#order-movie\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-movie mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["movies"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#order-series\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-youtube-tv mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["series"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#order-radio\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-radio mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["stations"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n                                            <div class=\"tab-pane\" id=\"order-stream\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <p class=\"sub-header\">\r\n                                                            ";
echo $_["bouquet_order_sort_text"];
echo "                                                        </p>\r\n                                                        <select multiple id=\"sort_stream\" class=\"form-control\" style=\"min-height:400px;\">\r\n                                                        ";
foreach ($rOrdered["stream"] as $rStream) {
    echo "                                                            <option value=\"";
    echo $rStream["id"];
    echo "\">";
    echo $rStream["stream_display_name"];
    echo "</option>\r\n                                                        ";
}
echo "                                                        </select>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\r\n                                                    <li class=\"list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveUp('stream')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveDown('stream')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveTop('stream')\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveBottom('stream')\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"AtoZ('stream')\" class=\"btn btn-info\">";
echo $_["a_to_z"];
echo "</a>\r\n                                                    </li>\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\">";
echo $_["save_changes"];
echo "</button>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            <div class=\"tab-pane\" id=\"order-movie\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <p class=\"sub-header\">\r\n                                                            ";
echo $_["bouquet_order_sort_text"];
echo "                                                        </p>\r\n                                                        <select multiple id=\"sort_movie\" class=\"form-control\" style=\"min-height:400px;\">\r\n                                                        ";
foreach ($rOrdered["movie"] as $rStream) {
    echo "                                                            <option value=\"";
    echo $rStream["id"];
    echo "\">";
    echo $rStream["stream_display_name"];
    echo "</option>\r\n                                                        ";
}
echo "                                                        </select>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\r\n                                                    <li class=\"list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveUp('movie')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveDown('movie')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveTop('movie')\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveBottom('movie')\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"AtoZ('movie')\" class=\"btn btn-info\">";
echo $_["a_to_z"];
echo "</a>\r\n                                                    </li>\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\">";
echo $_["save_changes"];
echo "</button>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            <div class=\"tab-pane\" id=\"order-series\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <p class=\"sub-header\">\r\n                                                            ";
echo $_["bouquet_order_sort_text"];
echo "                                                        </p>\r\n                                                        <select multiple id=\"sort_series\" class=\"form-control\" style=\"min-height:400px;\">\r\n                                                        ";
foreach ($rOrdered["series"] as $rStream) {
    echo "                                                            <option value=\"";
    echo $rStream["id"];
    echo "\">";
    echo $rStream["stream_display_name"];
    echo "</option>\r\n                                                        ";
}
echo "                                                        </select>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\r\n                                                    <li class=\"list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveUp('series')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveDown('series')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveTop('series')\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveBottom('series')\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"AtoZ('series')\" class=\"btn btn-info\">";
echo $_["a_to_z"];
echo "</a>\r\n                                                    </li>\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\">";
echo $_["save_changes"];
echo "</button>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            <div class=\"tab-pane\" id=\"order-radio\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <p class=\"sub-header\">\r\n                                                            ";
echo $_["bouquet_order_sort_text"];
echo "                                                        </p>\r\n                                                        <select multiple id=\"sort_radio\" class=\"form-control\" style=\"min-height:400px;\">\r\n                                                        ";
foreach ($rOrdered["radio"] as $rStream) {
    echo "                                                            <option value=\"";
    echo $rStream["id"];
    echo "\">";
    echo $rStream["stream_display_name"];
    echo "</option>\r\n                                                        ";
}
echo "                                                        </select>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\r\n                                                    <li class=\"list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveUp('radio')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveDown('radio')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveTop('radio')\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveBottom('radio')\" class=\"btn btn-pink\"><i class=\"mdi mdi-chevron-triple-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"AtoZ('radio')\" class=\"btn btn-info\">";
echo $_["a_to_z"];
echo "</a>\r\n                                                    </li>\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\">";
echo $_["save_changes"];
echo "</button>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                        </div>\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </form>\r\n                            </div> <!-- end card-body -->\r\n                        </div> <!-- end card-->\r\n                    </div> <!-- end col -->\r\n                </div>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n        <script src=\"assets/libs/nestable2/jquery.nestable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.rowReorder.js\"></script>\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        \r\n        <script>\r\n        function AtoZ(rType) {\r\n            \$(\"#sort_\" + rType).append(\$(\"#sort_\" + rType + \" option\").remove().sort(function(a, b) {\r\n                var at = \$(a).text().toUpperCase(), bt = \$(b).text().toUpperCase();\r\n                return (at > bt) ? 1 : ((at < bt) ? -1 : 0);\r\n            }));\r\n        }\r\n        function MoveUp(rType) {\r\n            var rSelected = \$('#sort_' + rType + ' option:selected');\r\n            if (rSelected.length) {\r\n                var rPrevious = rSelected.first().prev()[0];\r\n                if (\$(rPrevious).html() != '') {\r\n                    rSelected.first().prev().before(rSelected);\r\n                }\r\n            }\r\n        }\r\n        function MoveDown(rType) {\r\n            var rSelected = \$('#sort_' + rType + ' option:selected');\r\n            if (rSelected.length) {\r\n                rSelected.last().next().after(rSelected);\r\n            }\r\n        }\r\n        function MoveTop(rType) {\r\n            var rSelected = \$('#sort_' + rType + ' option:selected');\r\n            if (rSelected.length) {\r\n                rSelected.prependTo(\$('#sort_' + rType));\r\n            }\r\n        }\r\n        function MoveBottom(rType) {\r\n            var rSelected = \$('#sort_' + rType + ' option:selected');\r\n            if (rSelected.length) {\r\n                rSelected.appendTo(\$('#sort_' + rType));\r\n            }\r\n        }\r\n        \$(document).ready(function() {\r\n            \$(\"#channel_order_form\").submit(function(e){\r\n                rOrder = [];\r\n                \$('#sort_stream option').each(function() {\r\n                    rOrder.push(\$(this).val());\r\n                });\r\n                \$('#sort_movie option').each(function() {\r\n                    rOrder.push(\$(this).val());\r\n                });\r\n                \$('#sort_series option').each(function() {\r\n                    rOrder.push(\$(this).val());\r\n                });\r\n                \$('#sort_radios option').each(function() {\r\n                    rOrder.push(\$(this).val());\r\n                });\r\n                \$(\"#stream_order_array\").val(JSON.stringify(rOrder));\r\n            });\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>