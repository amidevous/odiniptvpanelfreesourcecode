<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_packages") && !hasPermissions("adv", "edit_package")) {
    exit;
}
if (isset($_POST["submit_package"])) {
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_package")) {
            exit;
        }
        $rArray = getPackage($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        if (!hasPermissions("adv", "add_packages")) {
            exit;
        }
        $rArray = ["package_name" => "", "is_trial" => 0, "is_official" => 0, "trial_credits" => 0, "official_credits" => 0, "trial_duration_in" => "hours", "trial_duration" => 0, "official_duration" => 1, "official_duration_in" => "years", "groups" => [], "bouquets" => [], "can_gen_mag" => 1, "only_mag" => 0, "output_formats" => [1, 2, 3], "is_isplock" => 0, "max_connections" => 1, "is_restreamer" => 0, "force_server_id" => 0, "only_e2" => 0, "can_gen_e2" => 1, "forced_country" => "", "lock_device" => 0];
    }
    if (strlen($_POST["package_name"]) == 0) {
        $_STATUS = 1;
    }
    foreach (["is_trial", "is_official", "can_gen_mag", "can_gen_e2", "only_mag", "only_e2", "lock_device", "is_restreamer"] as $rSelection) {
        if (isset($_POST[$rSelection])) {
            $rArray[$rSelection] = 1;
            unset($_POST[$rSelection]);
        } else {
            $rArray[$rSelection] = 0;
        }
    }
    if (isset($_POST["groups"])) {
        $rArray["groups"] = [];
        foreach ($_POST["groups"] as $rGroupID) {
            $rArray["groups"][] = intval($rGroupID);
        }
        $rArray["groups"] = "[" . join(",", $rArray["groups"]) . "]";
        unset($_POST["groups"]);
    }
    $rArray["bouquets"] = sortArrayByArray(array_values(json_decode($_POST["bouquets_selected"], true)), array_keys(getBouquetOrder()));
    $rArray["bouquets"] = "[" . join(",", $rArray["bouquets"]) . "]";
    unset($_POST["bouquets_selected"]);
    if (isset($_POST["output_formats"])) {
        $rArray["output_formats"] = [];
        foreach ($_POST["output_formats"] as $rOutput) {
            $rArray["output_formats"][] = intval($rOutput);
        }
        $rArray["output_formats"] = "[" . join(",", $rArray["output_formats"]) . "]";
        unset($_POST["output_formats"]);
    }
    if (!isset($_STATUS)) {
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
            $rCols = "`id`," . $rCols;
            $rValues = ESC($_POST["edit"]) . "," . $rValues;
        }
        $rQuery = "REPLACE INTO `packages`(" . $rCols . ") VALUES(" . $rValues . ");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
            } else {
                $rInsertID = $db->insert_id;
            }
            header("Location: ./package.php?id=" . $rInsertID);
            exit;
        }
        $_STATUS = 2;
    }
}
if (isset($_GET["id"])) {
    $rPackage = getPackage($_GET["id"]);
    if (!$rPackage || !hasPermissions("adv", "edit_package")) {
        exit;
    }
} else {
    if (!hasPermissions("adv", "add_packages")) {
        exit;
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t    <li>\n                                        <a href=\"./packages.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_packages"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\t\t\t\t\t\t\t\t\t\t\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rPackage)) {
    echo $_["edit_package"];
} else {
    echo $_["add_package"];
}
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["package_success"];
    echo "                        </div>\n                        ";
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["generic_fail"];
        echo "                        </div>\n                        ";
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./package.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"package_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rPackage)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rPackage["id"];
    echo "\" />\n                                    ";
}
echo "                                    <input type=\"hidden\" name=\"bouquets_selected\" id=\"bouquets_selected\" value=\"\" />\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#package-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#groups\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-account-group mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["groups"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#bouquets\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-flower-tulip mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["bouquets"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"package-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"package_name\">";
echo $_["package_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"package_name\" name=\"package_name\" value=\"";
if (isset($rPackage)) {
    echo htmlspecialchars($rPackage["package_name"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_trial\">";
echo $_["is_trial"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_trial\" id=\"is_trial\" type=\"checkbox\" ";
if (isset($rPackage) && $rPackage["is_trial"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"trial_credits\">";
echo $_["trial_credits"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"trial_credits\" name=\"trial_credits\" onkeypress=\"return isNumberKey(event)\" value=\"";
if (isset($rPackage)) {
    echo htmlspecialchars($rPackage["trial_credits"]);
} else {
    echo "0";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"trial_duration\">";
echo $_["trial_duration"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"trial_duration\" name=\"trial_duration\" value=\"";
if (isset($rPackage)) {
    echo htmlspecialchars($rPackage["trial_duration"]);
} else {
    echo "0";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"trial_duration_in\">";
echo $_["trial_duration_in"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"trial_duration_in\" id=\"trial_duration_in\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
foreach ([$_["hours"] => "hours", $_["days"] => "Days"] as $rText => $rOption) {
    echo "                                                                    <option ";
    if (isset($rPackage) && $rPackage["trial_duration_in"] == $rOption) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rOption;
    echo "\">";
    echo $rText;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_official\">";
echo $_["is_official"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_official\" id=\"is_official\" type=\"checkbox\" ";
if (isset($rPackage) && $rPackage["is_official"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"official_credits\">";
echo $_["official_credits"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"official_credits\" name=\"official_credits\" onkeypress=\"return isNumberKey(event)\" value=\"";
if (isset($rPackage)) {
    echo htmlspecialchars($rPackage["official_credits"]);
} else {
    echo "0";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"official_duration\">";
echo $_["official_duration"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"official_duration\" name=\"official_duration\" value=\"";
if (isset($rPackage)) {
    echo htmlspecialchars($rPackage["official_duration"]);
} else {
    echo "0";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"official_duration_in\">";
echo $_["official_duration_in"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"official_duration_in\" id=\"official_duration_in\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
foreach ([$_["hours"] => "hours", $_["days"] => "days", $_["months"] => "months", $_["years"] => "years"] as $rText => $rOption) {
    echo "                                                                    <option ";
    if (isset($rPackage) && $rPackage["official_duration_in"] == $rOption) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rOption;
    echo "\">";
    echo $rText;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"can_gen_mag\">";
echo $_["can_generate_mag"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"can_gen_mag\" id=\"can_gen_mag\" type=\"checkbox\" ";
if (isset($rPackage) && $rPackage["can_gen_mag"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"only_mag\">";
echo $_["mag_only"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"only_mag\" id=\"only_mag\" type=\"checkbox\" ";
if (isset($rPackage) && $rPackage["only_mag"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"can_gen_e2\">";
echo $_["can_generate_enigma"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"can_gen_e2\" id=\"can_gen_e2\" type=\"checkbox\" ";
if (isset($rPackage) && $rPackage["can_gen_e2"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"only_e2\">";
echo $_["enigma_only"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"only_e2\" id=\"only_e2\" type=\"checkbox\" ";
if (isset($rPackage) && $rPackage["only_e2"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"lock_device\">";
echo $_["lock_stb_device"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"lock_device\" id=\"lock_device\" type=\"checkbox\" ";
if (isset($rPackage) && $rPackage["lock_device"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_restreamer\">";
echo $_["can_restream"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_restreamer\" id=\"is_restreamer\" type=\"checkbox\" ";
if (isset($rPackage) && $rPackage["is_restreamer"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"max_connections\">";
echo $_["max_connections"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"max_connections\" name=\"max_connections\" value=\"";
if (isset($rPackage)) {
    echo htmlspecialchars($rPackage["max_connections"]);
} else {
    echo "1";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"output_formats\">";
echo $_["access_output"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                ";
foreach (getOutputs() as $rOutput) {
    echo "                                                                <div class=\"checkbox form-check-inline\">\n                                                                    <input data-size=\"large\" type=\"checkbox\" id=\"output_formats_";
    echo $rOutput["access_output_id"];
    echo "\" name=\"output_formats[]\" value=\"";
    echo $rOutput["access_output_id"];
    echo "\"";
    if (isset($rPackage)) {
        if (in_array($rOutput["access_output_id"], json_decode($rPackage["output_formats"], true))) {
            echo " checked";
        }
    } else {
        echo " checked";
    }
    echo ">\n                                                                    <label for=\"output_formats_";
    echo $rOutput["access_output_id"];
    echo "\"> ";
    echo $rOutput["output_name"];
    echo " </label>\n                                                                </div>\n                                                                ";
}
echo "                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"groups\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            ";
foreach (getMemberGroups() as $rGroup) {
    echo "                                                            <div class=\"col-md-6\">\n                                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                                    <input type=\"checkbox\" class=\"custom-control-input group-checkbox\" id=\"group-";
    echo $rGroup["group_id"];
    echo "\" data-id=\"";
    echo $rGroup["group_id"];
    echo "\" name=\"groups[]\" value=\"";
    echo $rGroup["group_id"];
    echo "\"";
    if (isset($rPackage) && in_array($rGroup["group_id"], json_decode($rPackage["groups"], true))) {
        echo " checked";
    }
    echo ">\n                                                                    <label class=\"custom-control-label\" for=\"group-";
    echo $rGroup["group_id"];
    echo "\">";
    echo $rGroup["group_name"];
    echo "</label>\n                                                                </div>\n                                                            </div>\n                                                            ";
}
echo "                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" onClick=\"selectAll()\" class=\"btn btn-secondary\">";
echo $_["select_all"];
echo "</a>\n                                                        <a href=\"javascript: void(0);\" onClick=\"selectNone()\" class=\"btn btn-secondary\">";
echo $_["deselect_all"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"bouquets\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <table id=\"datatable-bouquets\" class=\"table table-borderless mb-0\">\n                                                                <thead class=\"bg-light\">\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                        <th>";
echo $_["bouquet_name"];
echo "</th>\n                                                                        <th class=\"text-center\">";
echo $_["streams"];
echo "</th>\n                                                                        <th class=\"text-center\">";
echo $_["series"];
echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody>\n                                                                    ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                                    <tr";
    if (isset($rPackage) && in_array($rBouquet["id"], json_decode($rPackage["bouquets"], true))) {
        echo " class='selected selectedfilter ui-selected'";
    }
    echo ">\n                                                                        <td class=\"text-center\">";
    echo $rBouquet["id"];
    echo "</td>\n                                                                        <td>";
    echo $rBouquet["bouquet_name"];
    echo "</td>\n                                                                        <td class=\"text-center\">";
    echo count(json_decode($rBouquet["bouquet_channels"], true));
    echo "</td>\n                                                                        <td class=\"text-center\">";
    echo count(json_decode($rBouquet["bouquet_series"], true));
    echo "</td>\n                                                                    </tr>\n                                                                    ";
}
echo "                                                                </tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" onClick=\"toggleBouquets()\" class=\"btn btn-info\">";
echo $_["toggle_bouquets"];
echo "</a>\n                                                        <input name=\"submit_package\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rPackage)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-ui/jquery-ui.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        ";
if (isset($rPackage)) {
    echo "        var rBouquets = ";
    echo $rPackage["bouquets"];
    echo ";\n        ";
} else {
    echo "        var rBouquets = [];\n        ";
}
echo "        \n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        function toggleBouquets() {\n            \$(\"#datatable-bouquets tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rBouquets.splice(parseInt(\$.inArray(\$(this).find(\"td:eq(0)\").html()), window.rBouquets), 1);\n                    }\n                } else {            \n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rBouquets.push(parseInt(\$(this).find(\"td:eq(0)\").html()));\n                    }\n                }\n            });\n        }\n        function selectAll() {\n            \$(\".group-checkbox\").each(function() {\n                \$(this).prop('checked', true);\n            });\n        }\n        function selectNone() {\n            \$(\".group-checkbox\").each(function() {\n                \$(this).prop('checked', false);\n            });\n        }\n        function isNumberKey(evt) {\n            var charCode = (evt.which) ? evt.which : evt.keyCode;\n            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {\n                return false;\n            } else {\n                return true;\n            }\n        }\n        \$(document).ready(function() {\n            \$('select.select2').select2({width: '100%'})\n            \$(\".js-switch\").each(function (index, element) {\n                var init = new Switchery(element);\n            });\n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"#datatable-bouquets\").DataTable({\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,2,3]}\n                ],\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rBouquets) !== -1) {\n                        \$(row).addClass(\"selected\");\n                    }\n                },\n                paging: false,\n                bInfo: false,\n                searching: false\n            });\n            \$(\"#datatable-bouquets\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rBouquets.splice(parseInt(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html()), window.rBouquets), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rBouquets.push(parseInt(\$(ui.selected).find(\"td:eq(0)\").html()));\n                    }\n                }\n            });\n            \$(\"#package_form\").submit(function(e){\n                var rBouquets = [];\n                \$(\"#datatable-bouquets tr.selected\").each(function() {\n                    rBouquets.push(\$(this).find(\"td:eq(0)\").html());\n                });\n                \$(\"#bouquets_selected\").val(JSON.stringify(rBouquets));\n            });\n            \$(\"#max_connections\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#trial_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#official_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n        });\n        </script>\n    </body>\n</html>";

?>