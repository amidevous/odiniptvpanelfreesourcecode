<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "folder_watch_add")) {
    exit;
}
if (isset($_POST["submit_folder"])) {
    $rPath = $_POST["selected_path"];
    if (0 < strlen($rPath) && $rPath != "/") {
        $rExtra = "";
        if (isset($_POST["edit"])) {
            $rExtra = " AND `id` <> " . intval($_POST["edit"]);
        }
        $result = $db->query("SELECT `id` FROM `watch_folders` WHERE `type` = '" . ESC($_POST["folder_type"]) . "' AND `directory` = '" . ESC($rPath) . "' AND `server_id` = " . intval($_POST["server_id"]) . $rExtra . ";");
        if ($result && $result->num_rows == 0) {
            if (isset($_POST["edit"])) {
                $rArray = getWatchFolder($_POST["edit"]);
                unset($rArray["id"]);
            } else {
                $rArray = ["directory" => "", "last_run" => 0, "server_id" => 0, "type" => "movie", "active" => 1, "bouquets" => "[]", "fb_bouquets" => "[]", "category_id" => 0, "fb_category_id" => 0, "disable_tmdb" => 0, "ignore_no_match" => 0, "auto_subtitles" => 0, "allowed_extensions" => []];
            }
            $rArray["type"] = $_POST["folder_type"];
            $rArray["directory"] = $rPath;
            $rArray["server_id"] = intval($_POST["server_id"]);
            if (0 < count($_POST["bouquets"])) {
                $rArray["bouquets"] = json_encode($_POST["bouquets"]);
            } else {
                $rArray["bouquets"] = "[]";
            }
            if (0 < count($_POST["fb_bouquets"])) {
                $rArray["fb_bouquets"] = json_encode($_POST["fb_bouquets"]);
            } else {
                $rArray["fb_bouquets"] = "[]";
            }
            if (0 < count($_POST["allowed_extensions"])) {
                $rArray["allowed_extensions"] = json_encode($_POST["allowed_extensions"]);
            } else {
                $rArray["allowed_extensions"] = "[]";
            }
            $rArray["category_id"] = intval($_POST["category_id_" . $_POST["folder_type"]]);
            $rArray["fb_category_id"] = intval($_POST["fb_category_id_" . $_POST["folder_type"]]);
            if (isset($_POST["disable_tmdb"])) {
                $rArray["disable_tmdb"] = 1;
            } else {
                $rArray["disable_tmdb"] = 0;
            }
            if (isset($_POST["ignore_no_match"])) {
                $rArray["ignore_no_match"] = 1;
            } else {
                $rArray["ignore_no_match"] = 0;
            }
            if (isset($_POST["auto_subtitles"])) {
                $rArray["auto_subtitles"] = 1;
            } else {
                $rArray["auto_subtitles"] = 0;
            }
            if (isset($_POST["active"])) {
                $rArray["active"] = 1;
            } else {
                $rArray["active"] = 0;
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
                $rCols = "id," . $rCols;
                $rValues = ESC($_POST["edit"]) . "," . $rValues;
            }
            $rQuery = "REPLACE INTO `watch_folders`(" . $rCols . ") VALUES(" . $rValues . ");";
            if ($db->query($rQuery)) {
                if (isset($_POST["edit"])) {
                    $rInsertID = intval($_POST["edit"]);
                } else {
                    $rInsertID = $db->insert_id;
                }
            }
            header("Location: ./watch.php?successedit");
            exit;
        } else {
            $_STATUS = 1;
        }
    } else {
        $_STATUS = 0;
    }
}
if (isset($_GET["id"])) {
    $rFolder = getWatchFolder($_GET["id"]);
    if (!$rFolder) {
        exit;
    }
}
$rBouquets = getBouquets();
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if (isset($_GET["successedit"])) {
    $_STATUS = 2;
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./watch.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_folder_watch"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rFolder)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
echo $_["folder"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["please_select_a_directory"];
        echo "                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["please_select_a_directory"];
        echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["the_selected_directory"];
            echo "                        </div>\n\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["the_selected_directory"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
            if (!$rSettings["jsmess"]) {
                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            The directory operation was completed successfully.\n                        </div>\n                         ";
            } else {
                echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", \"The directory operation was completed successfully.\", \"success\");\n  \t\t\t\t\t</script> \n                        ";
            }
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./watch_add.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"ip_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rFolder)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rFolder["id"];
    echo "\" />\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#folder-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#override\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-movie mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["override_settings"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"folder-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t    <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"active\">Enable Scan Folder <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable or disable Scan Folder\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"active\" id=\"active\" type=\"checkbox\" ";
if ($rFolder["active"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"folder_type\">";
echo $_["folder_type"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"folder_type\" name=\"folder_type\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach (["movie" => "Movies", "series" => "TV Series"] as $rTypeID => $rType) {
    echo "                                                                    <option value=\"";
    echo $rTypeID;
    echo "\"";
    if (isset($rFolder) && $rFolder["type"] == $rTypeID) {
        echo " selected";
    }
    echo ">";
    echo $rType;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"server_id\">";
echo $_["server_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"server_id\" name=\"server_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach (getStreamingServers() as $rServer) {
    echo "                                                                    <option value=\"";
    echo $rServer["id"];
    echo "\"";
    if (isset($rFolder) && $rFolder["server_id"] == $rServer["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rServer["server_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"selected_path\">";
echo $_["selected_path"];
echo "</label>\n                                                            <div class=\"col-md-8 input-group\">\n                                                                <input type=\"text\" id=\"selected_path\" name=\"selected_path\" class=\"form-control\" value=\"";
if (isset($rFolder)) {
    echo htmlspecialchars($rFolder["directory"]);
} else {
    echo "/";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                                <div class=\"input-group-append\">\n                                                                    <button class=\"btn btn-primary waves-effect waves-light\" type=\"button\" id=\"changeDir\"><i class=\"mdi mdi-chevron-right\"></i></button>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"col-md-6\">\n                                                                <table id=\"datatable\" class=\"table\">\n                                                                    <thead>\n                                                                        <tr>\n                                                                            <th width=\"20px\"></th>\n                                                                            <th>";
echo $_["directory"];
echo "</th>\n                                                                        </tr>\n                                                                    </thead>\n                                                                    <tbody></tbody>\n                                                                </table>\n                                                            </div>\n                                                            <div class=\"col-md-6\">\n                                                                <table id=\"datatable-files\" class=\"table\">\n                                                                    <thead>\n                                                                        <tr>\n                                                                            <th width=\"20px\"></th>\n                                                                            <th>";
echo $_["filename"];
echo "</th>\n                                                                        </tr>\n                                                                    </thead>\n                                                                    <tbody></tbody>\n                                                                </table>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_folder\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rFolder)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"override\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\" id=\"category_movie\"";
if (isset($rFolder) && $rFolder["type"] != "movie") {
    echo " style=\"display: none;\"";
}
echo ">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_id_movie\">";
echo $_["override_category"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["ignore_category_allocation_category"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"category_id_movie\" id=\"category_id_movie\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    <option ";
if (isset($rFolder) && intval($rFolder["category_id"]) == 0) {
    echo "selected ";
}
echo "value=\"0\">";
echo $_["do_not_use"];
echo "</option>\n                                                                    ";
foreach (getCategories("movie") as $rCategory) {
    echo "                                                                        <option ";
    if (isset($rFolder) && intval($rFolder["category_id"]) == intval($rCategory["id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\" id=\"category_series\"";
if (isset($rFolder)) {
    if ($rFolder["type"] != "series") {
        echo " style=\"display: none;\"";
    }
} else {
    echo " style=\"display: none;\"";
}
echo ">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_id_series\">";
echo $_["override_category"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["ignore_category_allocation_category"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"category_id_series\" id=\"category_id_series\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    <option ";
if (isset($rFolder) && intval($rFolder["category_id"]) == 0) {
    echo "selected ";
}
echo "value=\"0\">";
echo $_["do_not_use"];
echo "</option>\n                                                                    ";
foreach (getCategories("series") as $rCategory) {
    echo "                                                                        <option ";
    if (isset($rFolder) && intval($rFolder["category_id"]) == intval($rCategory["id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"bouquets\">";
echo $_["override_bouquets"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["ignore_category_allocation_bouquet"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"bouquets[]\" id=\"bouquets\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"Choose...\">\n                                                                    ";
foreach ($rBouquets as $rBouquet) {
    echo "                                                                    <option ";
    if (isset($rFolder) && in_array(intval($rBouquet["id"]), json_decode($rFolder["bouquets"], true))) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rBouquet["id"];
    echo "\">";
    echo $rBouquet["bouquet_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\" id=\"fb_category_movie\"";
if (isset($rFolder) && $rFolder["type"] != "movie") {
    echo " style=\"display: none;\"";
}
echo ">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"fb_category_id_movie\">";
echo $_["fallback_category"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["add_to_this category"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"fb_category_id_movie\" id=\"fb_category_id_movie\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    <option ";
if (isset($rFolder) && intval($rFolder["fb_category_id"]) == 0) {
    echo "selected ";
}
echo "value=\"0\">";
echo $_["do_not_use"];
echo "</option>\n                                                                    ";
foreach (getCategories("movie") as $rCategory) {
    echo "                                                                        <option ";
    if (isset($rFolder) && intval($rFolder["fb_category_id"]) == intval($rCategory["id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\" id=\"fb_category_series\"";
if (isset($rFolder)) {
    if ($rFolder["type"] != "series") {
        echo " style=\"display: none;\"";
    }
} else {
    echo " style=\"display: none;\"";
}
echo ">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"fb_category_id_series\">";
echo $_["fallback_category"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["add_to_this category"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"fb_category_id_series\" id=\"fb_category_id_series\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    <option ";
if (isset($rFolder) && intval($rFolder["fb_category_id"]) == 0) {
    echo "selected ";
}
echo "value=\"0\">";
echo $_["do_not_use"];
echo "</option>\n                                                                    ";
foreach (getCategories("series") as $rCategory) {
    echo "                                                                        <option ";
    if (isset($rFolder) && intval($rFolder["fb_category_id"]) == intval($rCategory["id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"fb_bouquets\">";
echo $_["fallback_bouquets"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["add_to_these_bouquets"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"fb_bouquets[]\" id=\"fb_bouquets\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "...\">\n                                                                    ";
foreach ($rBouquets as $rBouquet) {
    echo "                                                                    <option ";
    if (isset($rFolder) && in_array(intval($rBouquet["id"]), json_decode($rFolder["fb_bouquets"], true))) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rBouquet["id"];
    echo "\">";
    echo $rBouquet["bouquet_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allowed_extensions\">";
echo $_["allowed_extensions"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["allow_scanning_of"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"allowed_extensions[]\" id=\"allowed_extensions\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "\">\n                                                                    ";
foreach (["mp4", "mkv", "avi", "mpg", "flv"] as $rExtension) {
    echo "                                                                    <option ";
    if (isset($rFolder) && in_array($rExtension, json_decode($rFolder["allowed_extensions"], true))) {
        echo $_["checked "];
    }
    echo "value=\"";
    echo $rExtension;
    echo "\">";
    echo $rExtension;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"disable_tmdb\">";
echo $_["disable_tmdb"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["do_not_use_tmdb"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"disable_tmdb\" id=\"disable_tmdb\" type=\"checkbox\" ";
if (isset($rFolder) && $rFolder["disable_tmdb"]) {
    echo $_["checked "];
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"ignore_no_match\">";
echo $_["ignore_no match"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["add_to_database_even"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"ignore_no_match\" id=\"ignore_no_match\" type=\"checkbox\" ";
if (isset($rFolder) && $rFolder["ignore_no_match"]) {
    echo $_["checked "];
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"auto_subtitles\">";
echo $_["auto-add_subtitles"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["automatically_embed_subtitles"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"auto_subtitles\" id=\"auto_subtitles\" type=\"checkbox\" ";
if (isset($rFolder) && $rFolder["auto_subtitles"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_folder\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rFolder)) {
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
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        \n        <script>\n        function selectDirectory(elem) {\n            window.currentDirectory += elem + \"/\";\n            \$(\"#selected_path\").val(window.currentDirectory);\n            \$(\"#changeDir\").click();\n        }\n        function selectParent() {\n            \$(\"#selected_path\").val(window.currentDirectory.split(\"/\").slice(0,-2).join(\"/\") + \"/\");\n            \$(\"#changeDir\").click();\n        }\n        \n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'});\n            \n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n              var switchery = new Switchery(html);\n            });\n            \n            \$(\"#datatable\").DataTable({\n                responsive: false,\n                paging: false,\n                bInfo: false,\n                searching: false,\n                scrollY: \"250px\",\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0]},\n                ],\n                \"language\": {\n                    \"emptyTable\": \"\"\n                }\n            });\n            \n            \$(\"#datatable-files\").DataTable({\n                responsive: false,\n                paging: false,\n                bInfo: false,\n                searching: true,\n                scrollY: \"250px\",\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0]},\n                ],\n                \"language\": {\n                    \"emptyTable\": \"";
echo $_["no_compatible_file"];
echo "\"\n                }\n            });\n            \n            \$(\"#select_folder\").click(function() {\n                \$(\"#import_folder\").val(\"s:\" + \$(\"#server_id\").val() + \":\" + window.currentDirectory);\n                \$.magnificPopup.close();\n            });\n            \n            \$(\"#changeDir\").click(function() {\n                window.currentDirectory = \$(\"#selected_path\").val();\n                if (window.currentDirectory.substr(-1) != \"/\") {\n                    window.currentDirectory += \"/\";\n                }\n                \$(\"#selected_path\").val(window.currentDirectory);\n                \$(\"#datatable\").DataTable().clear();\n                \$(\"#datatable\").DataTable().row.add([\"\", \"";
echo $_["loading"];
echo "...\"]);\n                \$(\"#datatable\").DataTable().draw(true);\n                \$(\"#datatable-files\").DataTable().clear();\n                \$(\"#datatable-files\").DataTable().row.add([\"\", \"";
echo $_["please_wait"];
echo "...\"]);\n                \$(\"#datatable-files\").DataTable().draw(true);\n                \$.getJSON(\"./api.php?action=listdir&dir=\" + window.currentDirectory + \"&server=\" + \$(\"#server_id\").val() + \"&filter=video\", function(data) {\n                    \$(\"#datatable\").DataTable().clear();\n                    \$(\"#datatable-files\").DataTable().clear();\n                    if (window.currentDirectory != \"/\") {\n                        \$(\"#datatable\").DataTable().row.add([\"<i class='mdi mdi-subdirectory-arrow-left'></i>\", \"Parent Directory\"]);\n                    }\n                    if (data.result == true) {\n                        \$(data.data.dirs).each(function(id, dir) {\n                            \$(\"#datatable\").DataTable().row.add([\"<i class='mdi mdi-folder-open-outline'></i>\", dir]);\n                        });\n                        \$(\"#datatable\").DataTable().draw(true);\n                        \$(data.data.files).each(function(id, dir) {\n                            \$(\"#datatable-files\").DataTable().row.add([\"<i class='mdi mdi-file-video'></i>\", dir]);\n                        });\n                        \$(\"#datatable-files\").DataTable().draw(true);\n                    }\n                });\n            });\n            \n            \$('#datatable').on('click', 'tbody > tr', function() {\n                if (\$(this).find(\"td\").eq(1).html() == \"Parent Directory\") {\n                    selectParent();\n                } else {\n                    selectDirectory(\$(this).find(\"td\").eq(1).html());\n                }\n            });\n            \n            \$(\"#server_id\").change(function() {\n                \$(\"#selected_path\").val(\"/\");\n                \$(\"#changeDir\").click();\n            });\n            \n            \$(\"#changeDir\").click();\n            \n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"#folder_type\").change(function() {\n                if (\$(this).val() == \"movie\") {\n                    \$(\"#category_movie\").show();\n                    \$(\"#category_series\").hide();\n                    \$(\"#fb_category_movie\").show();\n                    \$(\"#fb_category_series\").hide();\n                } else {\n                    \$(\"#category_movie\").hide();\n                    \$(\"#category_series\").show();\n                    \$(\"#fb_category_movie\").hide();\n                    \$(\"#fb_category_series\").show();\n                }\n            });\n            \n            \$(\"form\").attr('autocomplete', 'off');\n        });\n        </script>\n        \n        <!-- App js-->\n        <script src=\"assets/js/app.min.js\"></script>\n    </body>\n</html>";

?>