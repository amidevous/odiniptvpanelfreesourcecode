<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "categories")) {
    exit;
}
if (isset($_POST["categories"])) {
    $rPostCategories = json_decode($_POST["categories"], true);
    if (0 < count($rPostCategories)) {
        foreach ($rPostCategories as $rOrder => $rPostCategory) {
            $db->query("UPDATE `stream_categories` SET `cat_order` = " . (intval($rOrder) + 1) . ", `parent_id` = 0 WHERE `id` = " . intval($rPostCategory["id"]) . ";");
            if (isset($rPostCategory["children"])) {
                foreach ($rPostCategory["children"] as $rChildOrder => $rChildCategory) {
                    $db->query("UPDATE `stream_categories` SET `cat_order` = " . (intval($rChildOrder) + 1) . ", `parent_id` = " . intval($rPostCategory["id"]) . " WHERE `id` = " . intval($rChildCategory["id"]) . ";");
                }
            }
        }
    }
}
$rCategories = [1 => getCategories(), 2 => getCategories("movie"), 3 => getCategories("series"), 4 => getCategories("radio")];
$rMainCategories = ["1" => [], "2" => [], "3" => []];
$rSubCategories = ["1" => [], "2" => [], "3" => [], "4" => []];
foreach ([1, 2, 3, 4] as $rID) {
    foreach ($rCategories[$rID] as $rCategoryID => $rCategoryData) {
        if ($rCategoryData["parent_id"] != 0) {
            $rSubCategories[$rID][$rCategoryData["parent_id"]][] = $rCategoryData;
        } else {
            $rMainCategories[$rID][] = $rCategoryData;
        }
    }
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
if (hasPermissions("adv", "add_cat")) {
    echo "                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"stream_category.php\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i>  ";
    echo $_["add_category"];
    echo "                                            </button>\n                                        </a>\n                                    </li>\n                                </ol>\n                            </div>\n\t\t\t\t\t\t\t";
}
echo "                            <h4 class=\"page-title\"> ";
echo $_["categories"];
echo " </h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <div id=\"basicwizard\">\n                                    <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                        <li class=\"nav-item\">\n                                            <a href=\"#category-order-1\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                <i class=\"mdi mdi-play mr-1\"></i>\n                                                <span class=\"d-none d-sm-inline\">";
echo $_["streams"];
echo "</span>\n                                            </a>\n                                        </li>\n                                        <li class=\"nav-item\">\n                                            <a href=\"#category-order-2\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                <i class=\"mdi mdi-movie mr-1\"></i>\n                                                <span class=\"d-none d-sm-inline\">";
echo $_["movies"];
echo "</span>\n                                            </a>\n                                        </li>\n                                        <li class=\"nav-item\">\n                                            <a href=\"#category-order-3\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                <i class=\"mdi mdi-youtube-tv mr-1\"></i>\n                                                <span class=\"d-none d-sm-inline\">";
echo $_["series"];
echo "</span>\n                                            </a>\n                                        </li>\n                                        <li class=\"nav-item\">\n                                            <a href=\"#category-order-4\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                <i class=\"mdi mdi-radio mr-1\"></i>\n                                                <span class=\"d-none d-sm-inline\">";
echo $_["radio"];
echo "</span>\n                                            </a>\n                                        </li>\n                                    </ul>\n                                    <div class=\"tab-content b-0 mb-0 pt-0\">\n                                        <div class=\"tab-pane\" id=\"category-order-1\">\n                                            <form action=\"./stream_categories.php\" method=\"POST\" id=\"stream_categories_form-1\">\n                                                <input type=\"hidden\" id=\"categories_input-1\" name=\"categories\" value=\"\" />\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <p class=\"sub-header\">\n                                                            ";
echo $_["to_re-order_a_category"];
echo "  <i class=\"mdi mdi-view-sequential\"></i> ";
echo $_["click_save_changes_at"];
echo "                                                        </p>\n                                                        <div class=\"custom-dd dd\" id=\"category_order-1\">\n                                                            <ol class=\"dd-list\">\n                                                                ";
foreach ($rMainCategories[1] as $rCategory) {
    echo "                                                                <li class=\"dd-item dd3-item category-";
    echo $rCategory["id"];
    echo "\" data-id=\"";
    echo $rCategory["id"];
    echo "\">\n                                                                    <div class=\"dd-handle dd3-handle\"></div>\n                                                                    <div class=\"dd3-content\">";
    echo $rCategory["category_name"];
    echo "                                                                        <span style=\"float:right;\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "edit_cat")) {
        echo "                                                                            <div class=\"btn-group\">\n                                                                                <a href=\"./stream_category.php?id=";
        echo $rCategory["id"];
        echo "\"><button type=\"button\" class=\"btn btn-light waves-effect waves-light\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                                                <button type=\"button\" class=\"btn btn-light waves-effect waves-light\" onClick=\"deleteCategory(";
        echo $rCategory["id"];
        echo ")\"><i class=\"mdi mdi-close\"></i></button>\n                                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
    }
    echo "                                                                        </span>\n                                                                    </div>\n                                                                    ";
    if (isset($rSubCategories[1][$rCategory["id"]])) {
        echo "                                                                    <ol class=\"dd-list\">\n                                                                        ";
        foreach ($rSubCategories[1][$rCategory["id"]] as $rSubCategory) {
            echo "                                                                        <li class=\"dd-item dd3-item category-";
            echo $rSubCategory["id"];
            echo "\" data-id=\"";
            echo $rSubCategory["id"];
            echo "\">\n                                                                            <div class=\"dd-handle dd3-handle\"></div>\n                                                                            <div class=\"dd3-content\">";
            echo $rSubCategory["category_name"];
            echo "                                                                                <span style=\"float:right;\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
            if (hasPermissions("adv", "edit_cat")) {
                echo "                                                                                    <div class=\"btn-group\">\n                                                                                        <a href=\"./stream_category.php?id=";
                echo $rSubCategory["id"];
                echo "\"><button type=\"button\" class=\"btn btn-light waves-effect waves-light\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                                                        <button type=\"button\" class=\"btn btn-light waves-effect waves-light\" onClick=\"deleteCategory(";
                echo $rSubCategory["id"];
                echo ")\"><i class=\"mdi mdi-close\"></i></button>\n                                                                                    </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
            }
            echo "                                                                                </span>\n                                                                            </div>\n                                                                        </li>\n                                                                        ";
        }
        echo "                                                                    </ol>\n                                                                ";
    }
    echo "                                                                </li>\n                                                                ";
}
echo "                                                            </ol>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\">";
echo $_["save_changes"];
echo "</button>\n                                                    </li>\n                                                </ul>\n                                            </form>\n                                        </div>\n                                        <div class=\"tab-pane\" id=\"category-order-2\">\n                                            <form action=\"./stream_categories.php\" method=\"POST\" id=\"stream_categories_form-2\">\n                                                <input type=\"hidden\" id=\"categories_input-2\" name=\"categories\" value=\"\" />\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <p class=\"sub-header\">\n                                                            ";
echo $_["to_re-order_a_category"];
echo "  <i class=\"mdi mdi-view-sequential\"></i> ";
echo $_["click_save_changes_at"];
echo "                                                        </p>\n                                                        <div class=\"custom-dd dd\" id=\"category_order-2\">\n                                                            <ol class=\"dd-list\">\n                                                                ";
foreach ($rMainCategories[2] as $rCategory) {
    echo "                                                                <li class=\"dd-item dd3-item category-";
    echo $rCategory["id"];
    echo "\" data-id=\"";
    echo $rCategory["id"];
    echo "\">\n                                                                    <div class=\"dd-handle dd3-handle\"></div>\n                                                                    <div class=\"dd3-content\">";
    echo $rCategory["category_name"];
    echo "                                                                        <span style=\"float:right;\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "edit_cat")) {
        echo "                                                                            <div class=\"btn-group\">\n                                                                                <a href=\"./stream_category.php?id=";
        echo $rCategory["id"];
        echo "\"><button type=\"button\" class=\"btn btn-light waves-effect waves-light\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                                                <button type=\"button\" class=\"btn btn-light waves-effect waves-light\" onClick=\"deleteCategory(";
        echo $rCategory["id"];
        echo ")\"><i class=\"mdi mdi-close\"></i></button>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                                            ";
    }
    echo "                                                                        </span>\n                                                                    </div>\n                                                                    ";
    if (isset($rSubCategories[2][$rCategory["id"]])) {
        echo "                                                                    <ol class=\"dd-list\">\n                                                                        ";
        foreach ($rSubCategories[2][$rCategory["id"]] as $rSubCategory) {
            echo "                                                                        <li class=\"dd-item dd3-item category-";
            echo $rSubCategory["id"];
            echo "\" data-id=\"";
            echo $rSubCategory["id"];
            echo "\">\n                                                                            <div class=\"dd-handle dd3-handle\"></div>\n                                                                            <div class=\"dd3-content\">";
            echo $rSubCategory["category_name"];
            echo "                                                                                <span style=\"float:right;\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
            if (hasPermissions("adv", "edit_cat")) {
                echo "                                                                                    <div class=\"btn-group\">\n                                                                                        <a href=\"./stream_category.php?id=";
                echo $rSubCategory["id"];
                echo "\"><button type=\"button\" class=\"btn btn-light waves-effect waves-light\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                                                        <button type=\"button\" class=\"btn btn-light waves-effect waves-light\" onClick=\"deleteCategory(";
                echo $rSubCategory["id"];
                echo ")\"><i class=\"mdi mdi-close\"></i></button>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                                                    ";
            }
            echo "                                                                                </span>\n                                                                            </div>\n                                                                        </li>\n                                                                        ";
        }
        echo "                                                                    </ol>\n                                                                ";
    }
    echo "                                                                </li>\n                                                                ";
}
echo "                                                            </ol>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\">";
echo $_["save_changes"];
echo "</button>\n                                                    </li>\n                                                </ul>\n                                            </form>\n\t\t\t\t\t\t\t\t\t\t</div>\n                                        <div class=\"tab-pane\" id=\"category-order-3\">\n                                            <form action=\"./stream_categories.php\" method=\"POST\" id=\"stream_categories_form-3\">\n                                                <input type=\"hidden\" id=\"categories_input-3\" name=\"categories\" value=\"\" />\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <p class=\"sub-header\">\n                                                            ";
echo $_["to_re-order_a_category"];
echo "  <i class=\"mdi mdi-view-sequential\"></i> ";
echo $_["click_save_changes_at"];
echo "                                                        </p>\n                                                        <div class=\"custom-dd dd\" id=\"category_order-3\">\n                                                            <ol class=\"dd-list\">\n                                                                ";
foreach ($rMainCategories[3] as $rCategory) {
    echo "                                                                <li class=\"dd-item dd3-item category-";
    echo $rCategory["id"];
    echo "\" data-id=\"";
    echo $rCategory["id"];
    echo "\">\n                                                                    <div class=\"dd-handle dd3-handle\"></div>\n                                                                    <div class=\"dd3-content\">";
    echo $rCategory["category_name"];
    echo "                                                                        <span style=\"float:right;\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "edit_cat")) {
        echo "                                                                            <div class=\"btn-group\">\n                                                                                <a href=\"./stream_category.php?id=";
        echo $rCategory["id"];
        echo "\"><button type=\"button\" class=\"btn btn-light waves-effect waves-light\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                                                <button type=\"button\" class=\"btn btn-light waves-effect waves-light\" onClick=\"deleteCategory(";
        echo $rCategory["id"];
        echo ")\"><i class=\"mdi mdi-close\"></i></button>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                                            ";
    }
    echo "                                                                        </span>\n                                                                    </div>\n                                                                    ";
    if (isset($rSubCategories[3][$rCategory["id"]])) {
        echo "                                                                    <ol class=\"dd-list\">\n                                                                        ";
        foreach ($rSubCategories[3][$rCategory["id"]] as $rSubCategory) {
            echo "                                                                        <li class=\"dd-item dd3-item category-";
            echo $rSubCategory["id"];
            echo "\" data-id=\"";
            echo $rSubCategory["id"];
            echo "\">\n                                                                            <div class=\"dd-handle dd3-handle\"></div>\n                                                                            <div class=\"dd3-content\">";
            echo $rSubCategory["category_name"];
            echo "                                                                                <span style=\"float:right;\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
            if (hasPermissions("adv", "edit_cat")) {
                echo "                                                                                    <div class=\"btn-group\">\n                                                                                        <a href=\"./stream_category.php?id=";
                echo $rSubCategory["id"];
                echo "\"><button type=\"button\" class=\"btn btn-light waves-effect waves-light\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                                                        <button type=\"button\" class=\"btn btn-light waves-effect waves-light\" onClick=\"deleteCategory(";
                echo $rSubCategory["id"];
                echo ")\"><i class=\"mdi mdi-close\"></i></button>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                                                    ";
            }
            echo "                                                                                </span>\n                                                                            </div>\n                                                                        </li>\n                                                                        ";
        }
        echo "                                                                    </ol>\n                                                                ";
    }
    echo "                                                                </li>\n                                                                ";
}
echo "                                                            </ol>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\"> ";
echo $_["save_changes"];
echo "</button>\n                                                    </li>\n                                                </ul>\n                                            </form>\n                                        </div>\n                                        <div class=\"tab-pane\" id=\"category-order-4\">\n                                            <form action=\"./stream_categories.php\" method=\"POST\" id=\"stream_categories_form-4\">\n                                                <input type=\"hidden\" id=\"categories_input-4\" name=\"categories\" value=\"\" />\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <p class=\"sub-header\">\n                                                            ";
echo $_["to_re-order_a_category"];
echo "  <i class=\"mdi mdi-view-sequential\"></i> ";
echo $_["click_save_changes_at"];
echo " \n                                                        </p>\n                                                        <div class=\"custom-dd dd\" id=\"category_order-4\">\n                                                            <ol class=\"dd-list\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    ";
foreach ($rMainCategories[4] as $rCategory) {
    echo "                                                                <li class=\"dd-item dd3-item category-";
    echo $rCategory["id"];
    echo "\" data-id=\"";
    echo $rCategory["id"];
    echo "\">\n                                                                    <div class=\"dd-handle dd3-handle\"></div>\n                                                                    <div class=\"dd3-content\">";
    echo $rCategory["category_name"];
    echo "                                                                        <span style=\"float:right;\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "edit_cat")) {
        echo "                                                                            <div class=\"btn-group\">\n                                                                                <a href=\"./stream_category.php?id=";
        echo $rCategory["id"];
        echo "\"><button type=\"button\" class=\"btn btn-light waves-effect waves-light\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                                                <button type=\"button\" class=\"btn btn-light waves-effect waves-light\" onClick=\"deleteCategory(";
        echo $rCategory["id"];
        echo ")\"><i class=\"mdi mdi-close\"></i></button>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                                            ";
    }
    echo "                                                                        </span>\n                                                                    </div>\n                                                                    ";
    if (isset($rSubCategories[4][$rCategory["id"]])) {
        echo "                                                                    <ol class=\"dd-list\">\n                                                                        ";
        foreach ($rSubCategories[4][$rCategory["id"]] as $rSubCategory) {
            echo "                                                                        <li class=\"dd-item dd3-item category-";
            echo $rSubCategory["id"];
            echo "\" data-id=\"";
            echo $rSubCategory["id"];
            echo "\">\n                                                                            <div class=\"dd-handle dd3-handle\"></div>\n                                                                            <div class=\"dd3-content\">";
            echo $rSubCategory["category_name"];
            echo "                                                                                <span style=\"float:right;\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
            if (hasPermissions("adv", "edit_cat")) {
                echo "                                                                                    <div class=\"btn-group\">\n                                                                                        <a href=\"./stream_category.php?id=";
                echo $rSubCategory["id"];
                echo "\"><button type=\"button\" class=\"btn btn-light waves-effect waves-light\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>\n                                                                                        <button type=\"button\" class=\"btn btn-light waves-effect waves-light\" onClick=\"deleteCategory(";
                echo $rSubCategory["id"];
                echo ")\"><i class=\"mdi mdi-close\"></i></button>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                                                    ";
            }
            echo "                                                                                </span>\n                                                                            </div>\n                                                                        </li>\n                                                                        ";
        }
        echo "                                                                    </ol>\n                                                                ";
    }
    echo "                                                                </li>\n                                                                ";
}
echo "                                                            </ol>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\"> ";
echo $_["save_changes"];
echo "</button>\n                                                    </li>\n                                                </ul>\n                                            </form>\n                                        </div>\n                                    </div>\n                                </div> <!-- end #basicwizard-->\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/nestable2/jquery.nestable.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        function deleteCategory(rID) {\n            if (confirm(\"";
echo $_["are_you_sure_you_want_to_delete_this_category"];
echo "\")) {\n                \$.getJSON(\"./api.php?action=category&sub=delete&category_id=\" + rID, function(data) {\n                    if (data.result === true) {\n                        \$(\".category-\" + rID).remove();\n                        \$.toast(\"";
echo $_["category_successfully_deleted"];
echo "\");\n                        \$.each(\$('.tooltip'), function (index, element) {\n                            \$(this).remove();\n                        });\n                        \$('[data-toggle=\"tooltip\"]').tooltip();\n                    } else {\n                        \$.toast(\"";
echo $_["an_error_occured_while_processing_your_request"];
echo "\");\n                    }\n                });\n            }\n        }\n        \$(document).ready(function() {\n            \$(\"#category_order-1\").nestable({maxDepth: 1});\n            \$(\"#category_order-2\").nestable({maxDepth: 2});\n            \$(\"#category_order-3\").nestable({maxDepth: 2});\n            \$(\"#category_order-4\").nestable({maxDepth: 1});\n            \$(\"#stream_categories_form-1\").submit(function(e){\n                \$(\"#categories_input-1\").val(JSON.stringify(\$('#category_order-1.dd').nestable('serialize')));\n            });\n            \$(\"#stream_categories_form-2\").submit(function(e){\n                \$(\"#categories_input-2\").val(JSON.stringify(\$('#category_order-2.dd').nestable('serialize')));\n            });\n            \$(\"#stream_categories_form-3\").submit(function(e){\n                \$(\"#categories_input-3\").val(JSON.stringify(\$('#category_order-3.dd').nestable('serialize')));\n            });\n            \$(\"#stream_categories_form-4\").submit(function(e){\n                \$(\"#categories_input-4\").val(JSON.stringify(\$('#category_order-4.dd').nestable('serialize')));\n            });\n        });\n        </script>\n    </body>\n</html>";

?>