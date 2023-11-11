<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_cat")) {
    exit;
}
if (isset($_POST["submit_category"])) {
    $rArray = ["category_type" => "live", "category_name" => "", "parent_id" => 0, "cat_order" => 99];
    foreach ($_POST as $rKey => $rValue) {
        if (isset($rArray[$rKey])) {
            $rArray[$rKey] = $rValue;
        }
    }
    $rCols = "`" . ESC(implode("`,`", array_keys($rArray))) . "`";
    foreach (array_values($rArray) as $rValue) {
        isset($rValues);
        isset($rValues) ? $rValues .= "," : ($rValues = "");
        if (is_array($rValue)) {
            $rValue = json_encode($rValue);
        }
        if (is_null($rValue)) {
            $rValues .= "NULL";
        } else {
            $rValues .= "'" . ESC($rValue) . "'";
        }
    }
    if (isset($_POST["edit"]) && hasPermissions("adv", "edit_cat")) {
        $rCols = "id," . $rCols;
        $rValues = ESC($_POST["edit"]) . "," . $rValues;
    }
    $rQuery = "REPLACE INTO `stream_categories`(" . $rCols . ") VALUES(" . $rValues . ");";
    if ($db->query($rQuery)) {
        if (isset($_POST["edit"])) {
            $rInsertID = intval($_POST["edit"]);
        } else {
            $rInsertID = $db->insert_id;
        }
    }
    if (isset($rInsertID)) {
        header("Location: ./stream_categories.php?successedit");
        exit;
    }
    $_STATUS = 1;
}
if (isset($_GET["id"])) {
    $rCategoryArr = getCategory($_GET["id"]);
    if (!$rCategoryArr || !hasPermissions("adv", "edit_cat")) {
        exit;
    }
}
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if (isset($_GET["successedit"])) {
    $_STATUS = 0;
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./stream_categories.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_categories"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rCategoryArr)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
echo $_["category"];
echo " </h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["category_operation_was_completed_successfully"];
        echo " \n                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["category_operation_was_completed_successfully"];
        echo " ', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["generic_fail"];
            echo " \n                         </div>\n                        ";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", \"There was an error performing this operation! Please check the form entry and try again.\", \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./stream_category.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"category_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rCategoryArr)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rCategoryArr["id"];
    echo "\" />\n                                    <input type=\"hidden\" name=\"cat_order\" value=\"";
    echo $rCategoryArr["cat_order"];
    echo "\" />\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#category-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                   <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            ";
if (isset($rCategoryArr)) {
    echo "                                            <li class=\"nav-item\">\n                                                <a href=\"#view-channels\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-play mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["permission_streams"];
    echo " </span>\n                                                </a>\n                                            </li>\n                                            ";
}
echo "                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"category-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        ";
if (!isset($rCategoryArr)) {
    echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_type\">";
    echo $_["category_type"];
    echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"category_type\" id=\"category_type\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
    foreach (["live" => "Live TV", "movie" => "Movie", "series" => "TV Series", "radio" => "Radio Station"] as $rGroupID => $rGroup) {
        echo "                                                                    <option ";
        if (isset($rCategoryArr) && $rCategoryArr["category_type"] == $rGroupID) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rGroupID;
        echo "\">";
        echo $rGroup;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        ";
} else {
    echo "                                                        <input type=\"hidden\" name=\"category_type\" value=\"";
    echo $rCategoryArr["category_type"];
    echo "\" />\n                                                        ";
}
echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_name\">";
echo $_["category_name"];
echo " </label>\n                                                             <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"category_name\" name=\"category_name\" value=\"";
if (isset($rCategoryArr)) {
    echo htmlspecialchars($rCategoryArr["category_name"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_category\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rCategoryArr)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"view-channels\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\" style=\"overflow-x:auto;\">\n                                                        <table id=\"datatable\" class=\"table dt-responsive nowrap\">\n                                                            <thead>\n                                                                <tr>\n                                                                    <th class=\"text-center\">";
echo $_["stream_id"];
echo " </th>\n                                                                    <th>";
echo $_["stream_name"];
echo " </th>\n                                                                    <th class=\"text-center\">";
echo $_["actions"];
echo " </th>\n                                                                </tr>\n                                                            </thead>\n                                                            <tbody></tbody>\n                                                        </table>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'})\n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"form\").attr('autocomplete', 'off');\n            ";
if (isset($rCategoryArr)) {
    echo "            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                responsive: false,\n                bAutoWidth: false,\n                bInfo: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table.php\",\n                    \"data\": function(d) {\n                        ";
    if ($rCategoryArr["category_type"] == "live") {
        echo "                        d.id = \"streams_short\";\n                        ";
    } else {
        if ($rCategoryArr["category_type"] == "movie") {
            echo "                        d.id = \"movies_short\";\n                        ";
        } else {
            if ($rCategoryArr["category_type"] == "radio") {
                echo "                        d.id = \"radios_short\";\n                        ";
            } else {
                echo "                        d.id = \"series_short\";\n                        ";
            }
        }
    }
    echo "                        d.category_id = ";
    echo $rCategoryArr["id"];
    echo ";\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,2]}\n                ],\n            });\n            ";
}
echo "        });\n        </script>\n    </body>\n</html>";

?>