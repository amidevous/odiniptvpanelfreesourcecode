<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "folder_watch_settings")) {
    exit;
}
if (isset($_GET["update"])) {
    updateTMDbCategories();
    header("Location: ./settings_watch.php");
}
if (isset($_POST["submit_settings"])) {
    foreach ($_POST as $rKey => $rValue) {
        $rSplit = explode("_", $rKey);
        if ($rSplit[0] == "genre") {
            $rGenreID = intval($rSplit[1]);
            $rBouquets = json_encode($_POST["bouquet_" . $rGenreID]);
            if (!$rBouquets) {
                $rBouquets = "[]";
            }
            $db->query("UPDATE `watch_categories` SET `category_id` = " . intval($rValue) . ", `bouquets` = '" . ESC($rBouquets) . "' WHERE `genre_id` = " . intval($rGenreID) . " AND `type` = 1;");
        }
    }
    foreach ($_POST as $rKey => $rValue) {
        $rSplit = explode("_", $rKey);
        if ($rSplit[0] == "genretv") {
            $rGenreID = intval($rSplit[1]);
            $rBouquets = json_encode($_POST["bouquettv_" . $rGenreID]);
            if (!$rBouquets) {
                $rBouquets = "[]";
            }
            $db->query("UPDATE `watch_categories` SET `category_id` = " . intval($rValue) . ", `bouquets` = '" . ESC($rBouquets) . "' WHERE `genre_id` = " . intval($rGenreID) . " AND `type` = 2;");
        }
    }
    if (isset($_POST["read_native"])) {
        $rNative = 1;
    } else {
        $rNative = 0;
    }
    if (isset($_POST["movie_symlink"])) {
        $rSymLink = 1;
    } else {
        $rSymLink = 0;
    }
    if (isset($_POST["auto_encode"])) {
        $rAutoEncode = 1;
    } else {
        $rAutoEncode = 0;
    }
    if (isset($_POST["ffprobe_input"])) {
        $rProbeInput = 1;
    } else {
        $rProbeInput = 0;
    }
    $db->query("UPDATE `watch_settings` SET `ffprobe_input` = " . $rProbeInput . ", `percentage_match` = " . intval($_POST["percentage_match"]) . ", `read_native` = " . $rNative . ", `movie_symlink` = " . $rSymLink . ", `auto_encode` = " . $rAutoEncode . ", `transcode_profile_id` = " . intval($_POST["transcode_profile_id"]) . ", `scan_seconds` = " . intval($_POST["scan_seconds"]) . ";");
}
$rBouquets = getBouquets();
$rResult = $db->query("SELECT * FROM `watch_settings`;");
if ($rResult && $rResult->num_rows == 1) {
    $rWatchSettings = $rResult->fetch_assoc();
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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                        <a href=\"./watch.php\">\r\n                                            <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\">\r\n                                                ";
echo $_["folders"];
echo " \r\n                                            </button>\r\n                                        </a>\r\n                                        <a href=\"./settings_watch.php?update=1\">\r\n                                            <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm\">\r\n                                                ";
echo $_["update_from_tmdb"];
echo " \r\n                                            </button>\r\n                                        </a>\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $_["folder_watch_settings"];
echo " </h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        ";
if (isset($_STATUS) && 0 < $_STATUS) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
        echo $_["generic_fail"];
        echo " \r\n                        </div>\r\n                        ";
    } else {
        echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", \"There was an error performing this operation! Please check the form entry and try again.\", \"warning\");\r\n  \t\t\t\t\t</script>\r\n                        ";
    }
}
echo "                        <div class=\"card\">\r\n                            <div class=\"card-body\">\r\n                                <form action=\"./settings_watch.php\" method=\"POST\" id=\"watch_settings_form\">\r\n                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#setup\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["setup"];
echo " </span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#categories\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-movie mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["movie_categories"];
echo " </span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#categories-tv\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-youtube-tv mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["tv_categories"];
echo " </span>\r\n                                                </a>\r\n                                            </li>\r\n                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n                                            <div class=\"tab-pane\" id=\"setup\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"read_native\">";
echo $_["native_frames"];
echo " </label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"read_native\" id=\"read_native\" type=\"checkbox\" ";
if ($rWatchSettings["read_native"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"movie_symlink\">";
echo $_["create_symlink"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["generate_a_symlink"];
echo "\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"movie_symlink\" id=\"movie_symlink\" type=\"checkbox\" ";
if ($rWatchSettings["movie_symlink"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"auto_encode\">";
echo $_["auto_encode"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["start_encoding_as_soon"];
echo "\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"auto_encode\" id=\"auto_encode\" type=\"checkbox\" ";
if ($rWatchSettings["auto_encode"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"ffprobe_input\">";
echo $_["probe_input"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["use_ffmpeg_to_probe_input_files"];
echo "\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"ffprobe_input\" id=\"ffprobe_input\" type=\"checkbox\" ";
if ($rWatchSettings["ffprobe_input"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"scan_seconds\">";
echo $_["scan_frequency"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["scan_a_folder"];
echo "\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"scan_seconds\" name=\"scan_seconds\" value=\"";
echo htmlspecialchars($rWatchSettings["scan_seconds"]);
echo "\" required data-parsley-trigger=\"";
echo $_["change"];
echo "\">\r\n                                                            </div>\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"percentage_match\">";
echo $_["match_percentage"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["tmdb_match_tolerance"];
echo "\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"percentage_match\" name=\"percentage_match\" value=\"";
echo htmlspecialchars($rWatchSettings["percentage_match"]);
echo "\" required data-parsley-trigger=\"";
echo $_["change"];
echo "\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"transcode_profile_id\">";
echo $_["transcoding_profile"];
echo " </label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"transcode_profile_id\" id=\"transcode_profile_id\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                                    <option ";
if (intval($rWatchSettings["transcode_profile_id"]) == 0) {
    echo "selected ";
}
echo "value=\"0\">";
echo $_["transcoding_disabled"];
echo " </option>\r\n                                                                    ";
foreach (getTranscodeProfiles() as $rProfile) {
    echo "                                                                    <option ";
    if (intval($rWatchSettings["transcode_profile_id"]) == intval($rProfile["profile_id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rProfile["profile_id"];
    echo "\">";
    echo $rProfile["profile_name"];
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["save_changes"];
echo " \" />\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            <div class=\"tab-pane\" id=\"categories\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <p class=\"sub-header\">\r\n                                                            ";
echo $_["select_a_category_and"];
echo " \r\n                                                        </p>\r\n                                                        ";
$rResult = $db->query("SELECT * FROM `watch_categories` WHERE `type` = 1 ORDER BY `genre` ASC;");
if ($rResult && 0 < $rResult->num_rows) {
    while ($rRow = $rResult->fetch_assoc()) {
        echo "                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-2 col-form-label\" for=\"genre_";
        echo $rRow["genre_id"];
        echo "\">";
        echo $rRow["genre"];
        echo "</label>\r\n                                                            <div class=\"col-md-4\">\r\n                                                                <select name=\"genre_";
        echo $rRow["genre_id"];
        echo "\" id=\"genre_";
        echo $rRow["genre_id"];
        echo "\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    <option ";
        if (intval($rRow["category_id"]) == 0) {
            echo "selected ";
        }
        echo "value=\"0\">";
        echo $_["do_not_use"];
        echo " </option>\r\n                                                                    ";
        foreach (getCategories("movie") as $rCategory) {
            echo "                                                                        <option ";
            if (intval($rRow["category_id"]) == intval($rCategory["id"])) {
                echo "selected ";
            }
            echo "value=\"";
            echo $rCategory["id"];
            echo "\">";
            echo $rCategory["category_name"];
            echo "</option>\r\n                                                                    ";
        }
        echo "                                                                </select>\r\n                                                            </div>\r\n                                                            <label class=\"col-md-2 col-form-label\" for=\"bouquet_";
        echo $rRow["genre_id"];
        echo "\">";
        echo $_["add_to_bouquets"];
        echo " </label>\r\n                                                            <div class=\"col-md-4\">\r\n                                                                <select name=\"bouquet_";
        echo $rRow["genre_id"];
        echo "[]\" id=\"bouquet_";
        echo $rRow["genre_id"];
        echo "\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
        echo $_["choose"];
        echo "\">\r\n                                                                    ";
        foreach ($rBouquets as $rBouquet) {
            echo "                                                                    <option ";
            if (in_array(intval($rBouquet["id"]), json_decode($rRow["bouquets"], true))) {
                echo "selected ";
            }
            echo "value=\"";
            echo $rBouquet["id"];
            echo "\">";
            echo $rBouquet["bouquet_name"];
            echo "</option>\r\n                                                                    ";
        }
        echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        ";
    }
}
echo "                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["save_changes"];
echo " \" />\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            <div class=\"tab-pane\" id=\"categories-tv\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <p class=\"sub-header\">\r\n                                                            ";
echo $_["select_a_category_and"];
echo " \r\n                                                        </p>\r\n                                                        ";
$rResult = $db->query("SELECT * FROM `watch_categories` WHERE `type` = 2 ORDER BY `genre` ASC;");
if ($rResult && 0 < $rResult->num_rows) {
    while ($rRow = $rResult->fetch_assoc()) {
        echo "                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-2 col-form-label\" for=\"genretv_";
        echo $rRow["genre_id"];
        echo "\">";
        echo $rRow["genre"];
        echo "</label>\r\n                                                            <div class=\"col-md-4\">\r\n                                                                <select name=\"genretv_";
        echo $rRow["genre_id"];
        echo "\" id=\"genretv_";
        echo $rRow["genre_id"];
        echo "\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    <option ";
        if (intval($rRow["category_id"]) == 0) {
            echo "selected ";
        }
        echo "value=\"0\">";
        echo $_["do_not_use"];
        echo "</option>\r\n                                                                    ";
        foreach (getCategories("series") as $rCategory) {
            echo "                                                                        <option ";
            if (intval($rRow["category_id"]) == intval($rCategory["id"])) {
                echo "selected ";
            }
            echo "value=\"";
            echo $rCategory["id"];
            echo "\">";
            echo $rCategory["category_name"];
            echo "</option>\r\n                                                                    ";
        }
        echo "                                                                </select>\r\n                                                            </div>\r\n                                                            <label class=\"col-md-2 col-form-label\" for=\"bouquettv_";
        echo $rRow["genre_id"];
        echo "\">";
        echo $_["add_to_bouquets"];
        echo "</label>\r\n                                                            <div class=\"col-md-4\">\r\n                                                                <select name=\"bouquettv_";
        echo $rRow["genre_id"];
        echo "[]\" id=\"bouquettv_";
        echo $rRow["genre_id"];
        echo "\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
        echo $_["choose"];
        echo "\">\r\n                                                                    ";
        foreach ($rBouquets as $rBouquet) {
            echo "                                                                    <option ";
            if (in_array(intval($rBouquet["id"]), json_decode($rRow["bouquets"], true))) {
                echo "selected ";
            }
            echo "value=\"";
            echo $rBouquet["id"];
            echo "\">";
            echo $rBouquet["bouquet_name"];
            echo "</option>\r\n                                                                    ";
        }
        echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        ";
    }
}
echo "                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["save_changes"];
echo " \" />\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                        </div> <!-- tab-content -->\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </form>\r\n                            </div> <!-- end card-body -->\r\n                        </div> <!-- end card-->\r\n                    </div> <!-- end col -->\r\n                </div>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        <script>\r\n        \$(document).ready(function() {\r\n            \$('select').select2({width: '100%'});\r\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\r\n            elems.forEach(function(html) {\r\n              var switchery = new Switchery(html);\r\n            });\r\n            \r\n            \$(window).keypress(function(event){\r\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n            });\r\n            \r\n            \$(\"form\").attr('autocomplete', 'off');\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>