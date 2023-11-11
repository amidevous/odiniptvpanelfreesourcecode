<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_bouquet") && !hasPermissions("adv", "edit_bouquet")) {
    exit;
}
if (isset($_POST["submit_bouquet"])) {
    $rArray = ["bouquet_name" => "", "bouquet_channels" => [], "bouquet_series" => []];
    if (is_array(json_decode($_POST["bouquet_data"], true))) {
        $rBouquetData = json_decode($_POST["bouquet_data"], true);
        $rArray["bouquet_channels"] = array_values($rBouquetData["stream"]);
        $rArray["bouquet_series"] = array_values($rBouquetData["series"]);
    } else {
        if (isset($_POST["edit"])) {
            echo $_["bouquet_data_not_transfered"];
            exit;
        }
    }
    if (!isset($_POST["edit"])) {
        $rArray["bouquet_order"] = intval($db->query("SELECT MAX(`bouquet_order`) AS `max` FROM `bouquets`;")->fetch_assoc()["max"]) + 1;
    }
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
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_bouquet")) {
            exit;
        }
        $rCols = "id," . $rCols;
        $rValues = ESC($_POST["edit"]) . "," . $rValues;
    } else {
        if (!hasPermissions("adv", "add_bouquet")) {
            exit;
        }
    }
    $rQuery = "REPLACE INTO `bouquets`(" . $rCols . ") VALUES(" . $rValues . ");";
    if ($db->query($rQuery)) {
        if (isset($_POST["edit"])) {
            $rInsertID = intval($_POST["edit"]);
        } else {
            $rInsertID = $db->insert_id;
        }
        $_STATUS = 0;
        scanBouquet($rInsertID);
        header("Location: ./bouquet.php?successedit&id=" . $rInsertID);
        exit;
    }
    $_STATUS = 1;
}
if (isset($_GET["id"])) {
    $rBouquets = getBouquets();
    $rBouquetArr = $rBouquets[$_GET["id"]];
    if (!$rBouquetArr || !hasPermissions("adv", "edit_bouquet")) {
        exit;
    }
} else {
    if (!hasPermissions("adv", "add_bouquet")) {
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./bouquets.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_bouquets"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rBouquetArr)) {
    echo $_["edit_bouquet"];
} else {
    echo $_["add_bouquet"];
}
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["bouquet_success"];
        echo "                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["bouquet_success"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["generic_fail"];
            echo "                        </div>\n                        ";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["generic_fail"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./bouquet.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"bouquet_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rBouquetArr)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rBouquetArr["id"];
    echo "\" />\n                                    <input type=\"hidden\" id=\"bouquet_data\" name=\"bouquet_data\" value=\"\" />\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#bouquet-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            ";
if (isset($rBouquetArr)) {
    echo "                                            <li class=\"nav-item\">\n                                                <a href=\"#channels\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-play mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["streams"];
    echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#vod\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-movie mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["movies"];
    echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#series\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-youtube-tv mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["series"];
    echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#radios\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-radio mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["radio"];
    echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#review\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-book-open-variant mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["review"];
    echo "</span>\n                                                </a>\n                                            </li>\n                                            ";
}
echo "                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"bouquet-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"bouquet_name\">";
echo $_["bouquet_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"bouquet_name\" name=\"bouquet_name\" value=\"";
if (isset($rBouquetArr)) {
    echo htmlspecialchars($rBouquetArr["bouquet_name"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        ";
if (isset($rBouquetArr)) {
    echo "                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["next"];
    echo "</a>\n                                                        ";
} else {
    echo "                                                        <input name=\"submit_bouquet\" type=\"submit\" class=\"btn btn-primary\" value=\"";
    echo $_["add"];
    echo "\" />\n                                                        ";
}
echo "                                                    </li>\n                                                </ul>\n                                            </div>\n                                            ";
if (isset($rBouquetArr)) {
    echo "                                            <div class=\"tab-pane\" id=\"channels\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_name\">";
    echo $_["category_name"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"category_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option value=\"\" selected>";
    echo $_["all_categories"];
    echo "</option>\n                                                                    ";
    foreach ($rCategories as $rCategory) {
        echo "                                                                    <option value=\"";
        echo $rCategory["id"];
        echo "\">";
        echo $rCategory["category_name"];
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_search\">";
    echo $_["search"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"stream_search\" value=\"\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <table id=\"datatable-streams\" class=\"table nowrap\">\n                                                                <thead>\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
    echo $_["id"];
    echo "</th>\n                                                                        <th>";
    echo $_["stream_name"];
    echo "</th>\n                                                                        <th>";
    echo $_["category"];
    echo "</th>\n                                                                        <th class=\"text-center\">";
    echo $_["actions"];
    echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody></tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["prev"];
    echo "</a>\n                                                    </li>\n                                                    <span class=\"float-right\">\n                                                        <li class=\"list-inline-item\">\n                                                            <a href=\"javascript: void(0);\" onClick=\"toggleBouquets('datatable-streams')\" class=\"btn btn-primary\">";
    echo $_["toggle_page"];
    echo "</a>\n                                                        </li>\n                                                        <li class=\"next list-inline-item\">\n                                                            <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["next"];
    echo "</a>\n                                                        </li>\n                                                    </span>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"vod\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_name\">";
    echo $_["category_name"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"category_idv\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option value=\"\" selected>";
    echo $_["all_categories"];
    echo "</option>\n                                                                    ";
    foreach (getCategories("movie") as $rCategory) {
        echo "                                                                    <option value=\"";
        echo $rCategory["id"];
        echo "\">";
        echo $rCategory["category_name"];
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"vod_search\">";
    echo $_["search"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"vod_search\" value=\"\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <table id=\"datatable-vod\" class=\"table nowrap\">\n                                                                <thead>\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
    echo $_["id"];
    echo "</th>\n                                                                        <th>";
    echo $_["vod_name"];
    echo "</th>\n                                                                        <th>";
    echo $_["category"];
    echo "</th>\n                                                                        <th class=\"text-center\">";
    echo $_["actions"];
    echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody></tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["prev"];
    echo "</a>\n                                                    </li>\n                                                    <span class=\"float-right\">\n                                                        <li class=\"list-inline-item\">\n                                                            <a href=\"javascript: void(0);\" onClick=\"toggleBouquets('datatable-vod')\" class=\"btn btn-primary\">";
    echo $_["toggle_page"];
    echo "</a>\n                                                        </li>\n                                                        <li class=\"next list-inline-item\">\n                                                            <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["next"];
    echo "</a>\n                                                        </li>\n                                                    </span>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"series\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_name\">";
    echo $_["category_name"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"category_ids\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option value=\"\" selected>";
    echo $_["all_categories"];
    echo "</option>\n                                                                    ";
    foreach (getCategories("series") as $rCategory) {
        echo "                                                                    <option value=\"";
        echo $rCategory["id"];
        echo "\">";
        echo $rCategory["category_name"];
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"series_search\">";
    echo $_["search"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"series_search\" value=\"\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <table id=\"datatable-series\" class=\"table nowrap\">\n                                                                <thead>\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
    echo $_["id"];
    echo "</th>\n                                                                        <th>";
    echo $_["series_name"];
    echo "</th>\n                                                                        <th>";
    echo $_["category"];
    echo "</th>\n                                                                        <th class=\"text-center\">";
    echo $_["actions"];
    echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody></tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["prev"];
    echo "</a>\n                                                    </li>\n                                                    <span class=\"float-right\">\n                                                        <li class=\"list-inline-item\">\n                                                            <a href=\"javascript: void(0);\" onClick=\"toggleBouquets('datatable-series')\" class=\"btn btn-primary\">";
    echo $_["toggle_page"];
    echo "</a>\n                                                        </li>\n                                                        <li class=\"next list-inline-item\">\n                                                            <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["next"];
    echo "</a>\n                                                        </li>\n                                                    </span>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"radios\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_idr\">";
    echo $_["category_name"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"category_idr\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option value=\"\" selected>";
    echo $_["all_categories"];
    echo "</option>\n                                                                    ";
    foreach (getCategories("radio") as $rCategory) {
        echo "                                                                    <option value=\"";
        echo $rCategory["id"];
        echo "\">";
        echo $rCategory["category_name"];
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"radios_search\">";
    echo $_["search"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"radios_search\" value=\"\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <table id=\"datatable-radios\" class=\"table nowrap\">\n                                                                <thead>\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
    echo $_["id"];
    echo "</th>\n                                                                        <th>";
    echo $_["station_name"];
    echo "</th>\n                                                                        <th>";
    echo $_["category"];
    echo "</th>\n                                                                        <th class=\"text-center\">";
    echo $_["actions"];
    echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody></tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["prev"];
    echo "</a>\n                                                    </li>\n                                                    <span class=\"float-right\">\n                                                        <li class=\"list-inline-item\">\n                                                            <a href=\"javascript: void(0);\" onClick=\"toggleBouquets('datatable-series')\" class=\"btn btn-primary\">";
    echo $_["toggle_page"];
    echo "</a>\n                                                        </li>\n                                                        <li class=\"next list-inline-item\">\n                                                            <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["next"];
    echo "</a>\n                                                        </li>\n                                                    </span>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"review\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <table id=\"datatable-review\" class=\"table nowrap\">\n                                                                <thead>\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
    echo $_["id"];
    echo "</th>\n                                                                        <th>";
    echo $_["type"];
    echo "</th>\n                                                                        <th>";
    echo $_["display_name"];
    echo "</th>\n                                                                        <th class=\"text-center\">";
    echo $_["actions"];
    echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody>\n                                                                </tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div>\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["prev"];
    echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_bouquet\" type=\"submit\" class=\"btn btn-primary\" value=\"";
    if (isset($rBouquetArr)) {
        echo $_["edit"];
    } else {
        echo $_["add"];
    }
    echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            ";
}
echo "                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        ";
if (isset($rBouquetArr)) {
    if (!is_array(json_decode($rBouquetArr["bouquet_series"], true))) {
        $rBouquetArr["bouquet_series"] = "[]";
    }
    if (!is_array(json_decode($rBouquetArr["bouquet_channels"], true))) {
        $rBouquetArr["bouquet_channels"] = "[]";
    }
    echo "        var rBouquet = {\"stream\": \$.parseJSON(";
    echo json_encode($rBouquetArr["bouquet_channels"]);
    echo "), \"series\": \$.parseJSON(";
    echo json_encode($rBouquetArr["bouquet_series"]);
    echo ")};\n        ";
}
echo "        function reviewBouquet() {\n            var rTable = \$('#datatable-review').DataTable();\n            rTable.clear();\n            rTable.draw();\n            \$.post(\"./api.php?action=review_bouquet\", {\"data\": rBouquet}, function(rData) {\n                if (rData.result === true) {\n                    \$(rData.streams).each(function(rIndex) {\n                        rTable.row.add([rData.streams[rIndex].id, '";
echo $_["stream"];
echo "', rData.streams[rIndex].stream_display_name, '<button type=\"button\" class=\"btn-remove btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(' + rData.streams[rIndex].id + ', \\'stream\\', true);\"><i class=\"mdi mdi-minus\"></i></button>']);\n                    });\n                    \$(rData.vod).each(function(rIndex) {\n                        rTable.row.add([rData.vod[rIndex].id, '";
echo $_["movie"];
echo "', rData.vod[rIndex].stream_display_name, '<button type=\"button\" class=\"btn-remove btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(' + rData.vod[rIndex].id + ', \\'vod\\', true);\"><i class=\"mdi mdi-minus\"></i></button>']);\n                    });\n                    \$(rData.radios).each(function(rIndex) {\n                        rTable.row.add([rData.radios[rIndex].id, '";
echo $_["radio"];
echo "', rData.radios[rIndex].stream_display_name, '<button type=\"button\" class=\"btn-remove btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(' + rData.radios[rIndex].id + ', \\'radios\\', true);\"><i class=\"mdi mdi-minus\"></i></button>']);\n                    });\n                    \$(rData.series).each(function(rIndex) {\n                        rTable.row.add([rData.series[rIndex].id, '";
echo $_["series"];
echo "', rData.series[rIndex].title, '<button type=\"button\" class=\"btn-remove btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(' + rData.series[rIndex].id + ', \\'series\\', true);\"><i class=\"mdi mdi-minus\"></i></button>']);\n                    });\n                } else {\n                    alert(\"";
echo $_["bouquet_review_failed"];
echo "\");\n                }\n                rTable.draw();\n            }, \"json\");\n        }\n        \n        function toggleBouquet(rID, rType, rReview = false) {\n            if (rType == \"vod\") { rType = \"stream\"; }\n            if (rType == \"radios\") { rType = \"stream\"; }\n            var rIndex = rBouquet[rType].indexOf(parseInt(rID));\n            if (rIndex > -1) {\n                rBouquet[rType] = jQuery.grep(rBouquet[rType], function(rValue) {\n                    return parseInt(rValue) != parseInt(rID);\n                });\n            } else {\n                rBouquet[rType].push(parseInt(rID));\n            }\n            if (rReview == true) {\n                if (rType == \"stream\") {\n                    \$(\"#datatable-streams\").DataTable().ajax.reload(null, false);\n                    \$(\"#datatable-vod\").DataTable().ajax.reload(null, false);\n                    \$(\"#datatable-radios\").DataTable().ajax.reload(null, false);\n                } else {\n                    \$(\"#datatable-series\").DataTable().ajax.reload(null, false);\n                }\n                reviewBouquet()\n            }\n        }\n        \n        function toggleBouquets(rPage) {\n            \$(\"#\" + rPage + \" tr\").each(function() {\n                \$(this).find(\"td:last-child button\").filter(':visible').each(function() {\n                    toggleBouquet(\$(this).data(\"id\"), \$(this).data(\"type\"), false);\n                });\n            });\n            \$(\"#\" + rPage).DataTable().ajax.reload(null, false);\n            reviewBouquet()\n        }\n        \n        \$(document).ready(function() {\n            \$(\"#datatable-streams\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('stream-' + data[0]);\n                    var rIndex = rBouquet[\"stream\"].indexOf(parseInt(data[0]));\n                    if (rIndex > -1) {\n                        \$(row).find(\".btn-remove\").show();\n                    } else {\n                        \$(row).find(\".btn-add\").show();\n                    }\n                },\n                bInfo: false,\n                bAutoWidth: false,\n                searching: true,\n                pageLength: 100,\n                lengthChange: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table.php\",\n                    \"data\": function(d) {\n                        d.id = \"bouquets_streams\";\n                        d.category_id = \$(\"#category_id\").val();\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,3]}\n                ],\n            });\n            \$(\"#datatable-vod\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('vod-' + data[0]);\n                    var rIndex = rBouquet[\"stream\"].indexOf(parseInt(data[0]));\n                    if (rIndex > -1) {\n                        \$(row).find(\".btn-remove\").show();\n                    } else {\n                        \$(row).find(\".btn-add\").show();\n                    }\n                },\n                bInfo: false,\n                bAutoWidth: false,\n                searching: true,\n                pageLength: 100,\n                lengthChange: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table.php\",\n                    \"data\": function(d) {\n                        d.id = \"bouquets_vod\";\n                        d.category_id = \$(\"#category_idv\").val();\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,3]}\n                ],\n            });\n            \$(\"#datatable-series\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('series-' + data[0]);\n                    var rIndex = rBouquet[\"series\"].indexOf(parseInt(data[0]));\n                    if (rIndex > -1) {\n                        \$(row).find(\".btn-remove\").show();\n                    } else {\n                        \$(row).find(\".btn-add\").show();\n                    }\n                },\n                bInfo: false,\n                bAutoWidth: false,\n                searching: true,\n                pageLength: 100,\n                lengthChange: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table.php\",\n                    \"data\": function(d) {\n                        d.id = \"bouquets_series\";\n                        d.category_id = \$(\"#category_ids\").val();\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,3]}\n                ],\n            });\n            \$(\"#datatable-radios\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('radios-' + data[0]);\n                    var rIndex = rBouquet[\"stream\"].indexOf(parseInt(data[0]));\n                    if (rIndex > -1) {\n                        \$(row).find(\".btn-remove\").show();\n                    } else {\n                        \$(row).find(\".btn-add\").show();\n                    }\n                },\n                bInfo: false,\n                bAutoWidth: false,\n                searching: true,\n                pageLength: 100,\n                lengthChange: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table.php\",\n                    \"data\": function(d) {\n                        d.id = \"bouquets_radios\";\n                        d.category_id = \$(\"#category_idr\").val();\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,3]}\n                ],\n            });\n            \$(\"#datatable-review\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                bInfo: false,\n                bAutoWidth: false,\n                searching: true,\n                pageLength: 100,\n                lengthChange: false,\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,1,3]}\n                ],\n            });\n            \$('select').select2({width: '100%'});\n            \$(\"#category_id\").on(\"select2:select\", function(e) { \n                \$(\"#datatable-streams\").DataTable().ajax.reload(null, false);\n            });\n            \$('#stream_search').keyup(function(){\n                \$('#datatable-streams').DataTable().search(\$(this).val()).draw();\n            })\n            \$(\"#category_idv\").on(\"select2:select\", function(e) { \n                \$(\"#datatable-vod\").DataTable().ajax.reload(null, false);\n            });\n            \$('#vod_search').keyup(function(){\n                \$('#datatable-vod').DataTable().search(\$(this).val()).draw();\n            })\n            \$(\"#category_ids\").on(\"select2:select\", function(e) { \n                \$(\"#datatable-series\").DataTable().ajax.reload(null, false);\n            });\n            \$('#series_search').keyup(function(){\n                \$('#datatable-series').DataTable().search(\$(this).val()).draw();\n            });\n            \$(\"#category_idr\").on(\"select2:select\", function(e) { \n                \$(\"#datatable-radios\").DataTable().ajax.reload(null, false);\n            });\n            \$('#radios_search').keyup(function(){\n                \$('#datatable-radios').DataTable().search(\$(this).val()).draw();\n            });\n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$('a[data-toggle=\"tab\"]').on('shown.bs.tab', function (e) {\n                if (\$(e.target).attr(\"href\") == \"#review\") {\n                    reviewBouquet();\n                }\n            });\n            \$(\"#bouquet_form\").submit(function(e){\n                if (\$(\"#bouquet_name\").val().length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["enter_a_bouquet_name"];
echo "\");\n                }\n                \$(\"#bouquet_data\").val(JSON.stringify(rBouquet));\n            });\n            \$(\"form\").attr('autocomplete', 'off');\n        });\n        </script>\n    </body>\n</html>";

?>