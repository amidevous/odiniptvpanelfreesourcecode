<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "mass_delete")) {
    exit;
}
set_time_limit(0);
ini_set("max_execution_time", 0);
if (isset($_POST["submit_streams"])) {
    $rStreams = json_decode($_POST["streams"], true);
    foreach ($rStreams as $rStream) {
        $db->query("DELETE FROM `streams_sys` WHERE `stream_id` = " . intval($rStream) . ";");
        $db->query("DELETE FROM `streams` WHERE `id` = " . intval($rStream) . ";");
    }
    $_STATUS = 0;
}
if (isset($_POST["submit_movies"])) {
    $rMovies = json_decode($_POST["movies"], true);
    foreach ($rMovies as $rMovie) {
        $result = $db->query("SELECT `server_id` FROM `streams_sys` WHERE `stream_id` = " . intval($rMovie) . ";");
        if ($result && 0 < $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                deleteMovieFile($row["server_id"], $rMovie);
            }
        }
        $db->query("DELETE FROM `streams_sys` WHERE `stream_id` = " . intval($rMovie) . ";");
        $db->query("DELETE FROM `streams` WHERE `id` = " . intval($rMovie) . ";");
    }
    $_STATUS = 1;
}
if (isset($_POST["submit_users"])) {
    $rUsers = json_decode($_POST["users"], true);
    foreach ($rUsers as $rUser) {
        $db->query("DELETE FROM `users` WHERE `id` = " . intval($rUser) . ";");
        $db->query("DELETE FROM `user_output` WHERE `user_id` = " . intval($rUser) . ";");
        $db->query("DELETE FROM `enigma2_devices` WHERE `user_id` = " . intval($rUser) . ";");
        $db->query("DELETE FROM `mag_devices` WHERE `user_id` = " . intval($rUser) . ";");
    }
    $_STATUS = 2;
}
if (isset($_POST["submit_series"])) {
    $rSeries = json_decode($_POST["series"], true);
    foreach ($rSeries as $rSerie) {
        $db->query("DELETE FROM `series` WHERE `id` = " . intval($rSerie) . ";");
        $rResult = $db->query("SELECT `stream_id` FROM `series_episodes` WHERE `series_id` = " . intval($rSerie) . ";");
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rResultB = $db->query("SELECT `server_id` FROM `streams_sys` WHERE `stream_id` = " . intval($rRow["stream_id"]) . ";");
                if ($rResultB && 0 < $rResultB->num_rows) {
                    while ($rRowB = $rResultB->fetch_assoc()) {
                        deleteMovieFile($rRowB["server_id"], $rRow["stream_id"]);
                    }
                }
                $db->query("DELETE FROM `streams_sys` WHERE `stream_id` = " . intval($rRow["stream_id"]) . ";");
                $db->query("DELETE FROM `streams` WHERE `id` = " . intval($rRow["stream_id"]) . ";");
            }
            $db->query("DELETE FROM `series_episodes` WHERE `series_id` = " . intval($rSerie) . ";");
        }
    }
    scanBouquets();
    $_STATUS = 3;
}
if (isset($_POST["submit_episodes"])) {
    $rEpisodes = json_decode($_POST["episodes"], true);
    foreach ($rEpisodes as $rEpisode) {
        $result = $db->query("SELECT `server_id` FROM `streams_sys` WHERE `stream_id` = " . intval($rEpisode) . ";");
        if ($result && 0 < $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                deleteMovieFile($row["server_id"], $rEpisode);
            }
        }
        $db->query("DELETE FROM `series_episodes` WHERE `stream_id` = " . intval($rEpisode) . ";");
        $db->query("DELETE FROM `streams_sys` WHERE `stream_id` = " . intval($rEpisode) . ";");
        $db->query("DELETE FROM `streams` WHERE `id` = " . intval($rEpisode) . ";");
    }
    $_STATUS = 4;
}
if (isset($_POST["submit_streams"]) || isset($_POST["submit_movies"])) {
    scanBouquets();
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <h4 class=\"page-title\">";
echo $_["mass_delete"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n\t\t\t\t\t\t\t";
        echo $_["mass_delete_message_1"];
        echo "                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["mass_delete_message_1"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["mass_delete_message_2"];
            echo "                        </div>\n\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["mass_delete_message_2"];
            echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
        }
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
            if (!$rSettings["sucessedit"]) {
                echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                echo $_["mass_delete_message_3"];
                echo "                        </div>\n\t\t\t\t\t\t";
            } else {
                echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                echo $_["mass_delete_message_3"];
                echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
            }
        } else {
            if (isset($_STATUS) && $_STATUS == 3) {
                if (!$rSettings["sucessedit"]) {
                    echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                    echo $_["mass_delete_message_4"];
                    echo "                        </div>\n\t\t\t\t\t\t";
                } else {
                    echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                    echo $_["mass_delete_message_4"];
                    echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
                }
            } else {
                if (isset($_STATUS) && $_STATUS == 4) {
                    if (!$rSettings["sucessedit"]) {
                        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                        echo $_["mass_delete_message_5"];
                        echo "                        </div>\n                        ";
                    } else {
                        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                        echo $_["mass_delete_message_5"];
                        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
                    }
                }
            }
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <div id=\"basicwizard\">\n                                    <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                        <li class=\"nav-item\">\n                                            <a href=\"#stream-selection\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                <i class=\"mdi mdi-play mr-1\"></i>\n                                                <span class=\"d-none d-sm-inline\">";
echo $_["streams"];
echo "</span>\n                                            </a>\n                                        </li>\n                                        <li class=\"nav-item\">\n                                            <a href=\"#movie-selection\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                <span class=\"d-none d-sm-inline\">";
echo $_["movies"];
echo "</span>\n                                            </a>\n                                        </li>\n                                        <li class=\"nav-item\">\n                                            <a href=\"#series-selection\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                <i class=\"mdi mdi-youtube-tv mr-1\"></i>\n                                                <span class=\"d-none d-sm-inline\">";
echo $_["series"];
echo "</span>\n                                            </a>\n                                        </li>\n                                        <li class=\"nav-item\">\n                                            <a href=\"#episodes-selection\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                <i class=\"mdi mdi-folder-open-outline mr-1\"></i>\n                                                <span class=\"d-none d-sm-inline\">";
echo $_["episodes"];
echo "</span>\n                                            </a>\n                                        </li>\n                                        <li class=\"nav-item\">\n                                            <a href=\"#user-selection\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                <i class=\"mdi mdi-server-network mr-1\"></i>\n                                                <span class=\"d-none d-sm-inline\">";
echo $_["users"];
echo "</span>\n                                            </a>\n                                        </li>\n                                    </ul>\n                                    <div class=\"tab-content b-0 mb-0 pt-0\">\n                                        <div class=\"tab-pane\" id=\"stream-selection\">\n                                            <form action=\"./mass_delete.php\" method=\"POST\" id=\"stream_form\">\n                                                <input type=\"hidden\" name=\"streams\" id=\"streams\" value=\"\" />\n                                                <div class=\"row\">\n                                                    <div class=\"col-md-4 col-6\">\n                                                        <input type=\"text\" class=\"form-control\" id=\"stream_search\" value=\"\" placeholder=\"";
echo $_["search_streams"];
echo "...\">\n                                                    </div>\n                                                    <div class=\"col-md-4 col-6\">\n                                                        <select id=\"stream_category_search\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["all_categories"];
echo "</option>\n                                                            ";
foreach ($rCategories as $rCategory) {
    echo "                                                            <option value=\"";
    echo $rCategory["id"];
    echo "\"";
    if (isset($_GET["category"]) && $_GET["category"] == $rCategory["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <label class=\"col-md-1 col-2 col-form-label text-center\" for=\"show_entries\">";
echo $_["show"];
echo "</label>\n                                                    <div class=\"col-md-2 col-8\">\n                                                        <select id=\"show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                            ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                            <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light\" onClick=\"toggleStreams()\">\n                                                            <i class=\"mdi mdi-selection\"></i>\n                                                        </button>\n                                                    </div>\n                                                    <table id=\"datatable-md1\" class=\"table table-hover table-borderless mb-0\">\n                                                        <thead class=\"bg-light\">\n                                                            <tr>\n                                                                <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                <th>";
echo $_["stream_name"];
echo "</th>\n                                                                <th>";
echo $_["category"];
echo "</th>\n                                                            </tr>\n                                                        </thead>\n                                                        <tbody></tbody>\n                                                    </table>\n                                                </div>\n                                                <ul class=\"list-inline wizard mb-0\" style=\"margin-top:20px;\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_streams\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["delete_streams"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </form>\n                                        </div>\n                                        <div class=\"tab-pane\" id=\"movie-selection\">\n                                            <form action=\"./mass_delete.php\" method=\"POST\" id=\"movie_form\">\n                                                <input type=\"hidden\" name=\"movies\" id=\"movies\" value=\"\" />\n                                                <div class=\"row\">\n                                                    <div class=\"col-md-3 col-6\">\n                                                        <input type=\"text\" class=\"form-control\" id=\"movie_search\" value=\"\" placeholder=\"";
echo $_["search_movies"];
echo "...\">\n                                                    </div>\n                                                    <div class=\"col-md-3 col-6\">\n                                                        <select id=\"movie_category_search\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["all_categories"];
echo "</option>\n                                                            ";
foreach (getCategories("movie") as $rCategory) {
    echo "                                                            <option value=\"";
    echo $rCategory["id"];
    echo "\"";
    if (isset($_GET["category"]) && $_GET["category"] == $rCategory["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-3 col-6\">\n                                                        <select id=\"movie_filter\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["no_filter"];
echo "</option>\n                                                            <option value=\"1\">";
echo $_["encoded"];
echo "</option>\n                                                            <option value=\"2\">";
echo $_["encoding"];
echo "</option>\n                                                            <option value=\"3\">";
echo $_["down"];
echo "</option>\n                                                            <option value=\"4\">";
echo $_["ready"];
echo "</option>\n                                                            <option value=\"5\">";
echo $_["direct"];
echo "</option>\n                                                            <option value=\"6\">";
echo $_["no_tmdb_match"];
echo "</option>\n                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-2 col-8\">\n                                                        <select id=\"movie_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                            ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                            <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light\" onClick=\"toggleMovies()\">\n                                                            <i class=\"mdi mdi-selection\"></i>\n                                                        </button>\n                                                    </div>\n                                                    <table id=\"datatable-md2\" class=\"table table-hover table-borderless mb-0\">\n                                                        <thead class=\"bg-light\">\n                                                            <tr>\n                                                                <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                <th>";
echo $_["movie_name"];
echo "</th>\n                                                                <th>";
echo $_["category"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["status"];
echo "</th>\n                                                            </tr>\n                                                        </thead>\n                                                        <tbody></tbody>\n                                                    </table>\n                                                </div>\n                                                <ul class=\"list-inline wizard mb-0\" style=\"margin-top:20px;\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_movies\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["delete_movies"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </form>\n                                        </div>\n                                        <div class=\"tab-pane\" id=\"series-selection\">\n                                            <form action=\"./mass_delete.php\" method=\"POST\" id=\"series_form\">\n                                                <input type=\"hidden\" name=\"series\" id=\"series\" value=\"\" />\n                                                <div class=\"row\">\n                                                    <div class=\"col-md-6 col-6\">\n                                                        <input type=\"text\" class=\"form-control\" id=\"series_search\" value=\"\" placeholder=\"";
echo $_["search_series"];
echo "...\">\n                                                    </div>\n                                                    <div class=\"col-md-3 col-6\">\n                                                        <select id=\"series_category_search\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["all_categories"];
echo "</option>\n                                                            <option value=\"-1\">";
echo $_["no_tmdb_match"];
echo "</option>\n                                                            ";
foreach (getCategories("series") as $rCategory) {
    echo "                                                            <option value=\"";
    echo $rCategory["id"];
    echo "\"";
    if (isset($_GET["category"]) && $_GET["category"] == $rCategory["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-2 col-8\">\n                                                        <select id=\"series_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                            ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                            <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light\" onClick=\"toggleSeries()\">\n                                                            <i class=\"mdi mdi-selection\"></i>\n                                                        </button>\n                                                    </div>\n                                                    <table id=\"datatable-md4\" class=\"table table-hover table-borderless mb-0\">\n                                                        <thead class=\"bg-light\">\n                                                            <tr>\n                                                                <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                <th>";
echo $_["series_name"];
echo "</th>\n                                                                <th>";
echo $_["category"];
echo "</th>\n                                                            </tr>\n                                                        </thead>\n                                                        <tbody></tbody>\n                                                    </table>\n                                                </div>\n                                                <ul class=\"list-inline wizard mb-0\" style=\"margin-top:20px;\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_series\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["delete_series"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </form>\n                                        </div>\n                                        <div class=\"tab-pane\" id=\"episodes-selection\">\n                                            <form action=\"./mass_delete.php\" method=\"POST\" id=\"episodes_form\">\n                                                <input type=\"hidden\" name=\"episodes\" id=\"episodes\" value=\"\" />\n                                                <div class=\"row\">\n                                                    <div class=\"col-md-3 col-6\">\n                                                        <input type=\"text\" class=\"form-control\" id=\"episode_search\" value=\"\" placeholder=\"";
echo $_["search_episodes"];
echo "...\">\n                                                    </div>\n                                                    <div class=\"col-md-3 col-6\">\n                                                        <select id=\"episode_series\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\">";
echo $_["all_series"];
echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
foreach (getSeries() as $rSerie) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $rSerie["id"];
    echo "\">";
    echo $rSerie["title"];
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-3 col-6\">\n                                                        <select id=\"episode_filter\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["no_filter"];
echo "</option>\n                                                            <option value=\"1\">";
echo $_["encoded"];
echo "</option>\n                                                            <option value=\"2\">";
echo $_["encoding"];
echo "</option>\n                                                            <option value=\"3\">";
echo $_["down"];
echo "</option>\n                                                            <option value=\"4\">";
echo $_["ready"];
echo "</option>\n                                                            <option value=\"5\">";
echo $_["direct"];
echo "</option>\n                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-2 col-8\">\n                                                        <select id=\"episode_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                            ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                            <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light\" onClick=\"toggleEpisodes()\">\n                                                            <i class=\"mdi mdi-selection\"></i>\n                                                        </button>\n                                                    </div>\n                                                    <table id=\"datatable-md5\" class=\"table table-hover table-borderless mb-0\">\n                                                        <thead class=\"bg-light\">\n                                                            <tr>\n                                                                <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                <th>";
echo $_["episode_name"];
echo "</th>\n                                                                <th>";
echo $_["series"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["status"];
echo "</th>\n                                                            </tr>\n                                                        </thead>\n                                                        <tbody></tbody>\n                                                    </table>\n                                                </div>\n                                                <ul class=\"list-inline wizard mb-0\" style=\"margin-top:20px;\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_episodes\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["delete_episodes"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </form>\n                                        </div>\n                                        <div class=\"tab-pane\" id=\"user-selection\">\n                                            <form action=\"./mass_delete.php\" method=\"POST\" id=\"user_form\">\n                                                <input type=\"hidden\" name=\"users\" id=\"users\" value=\"\" />\n                                                <div class=\"row\">\n                                                    <div class=\"col-md-3 col-6\">\n                                                        <input type=\"text\" class=\"form-control\" id=\"user_search\" value=\"\" placeholder=\"";
echo $_["search_users"];
echo "...\">\n                                                    </div>\n                                                    <div class=\"col-md-4\">\n                                                        <select id=\"reseller_search\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["all_resellers"];
echo "</option>\n                                                            ";
foreach (getRegisteredUsers() as $rRegisteredUser) {
    echo "                                                            <option value=\"";
    echo $rRegisteredUser["id"];
    echo "\">";
    echo $rRegisteredUser["username"];
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-2\">\n                                                        <select id=\"user_filter\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["no_filter"];
echo "</option>\n                                                            <option value=\"1\">";
echo $_["active"];
echo "</option>\n                                                            <option value=\"2\">";
echo $_["disabled"];
echo "</option>\n                                                            <option value=\"3\">";
echo $_["banned"];
echo "</option>\n                                                            <option value=\"4\">";
echo $_["expired"];
echo "</option>\n                                                            <option value=\"5\">";
echo $_["trial"];
echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"6\">";
echo $_["mag_device"];
echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"7\">";
echo $_["enigma_device"];
echo "</option>\n                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-2 col-8\">\n                                                        <select id=\"user_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                            ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                            <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light\" onClick=\"toggleUsers()\">\n                                                            <i class=\"mdi mdi-selection\"></i>\n                                                        </button>\n                                                    </div>\n                                                    <table id=\"datatable-md3\" class=\"table table-borderless mb-0\">\n                                                        <thead class=\"bg-light\">\n                                                            <tr>\n                                                                <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                <th>";
echo $_["username"];
echo "</th>\n                                                                <th></th>\n                                                                <th>";
echo $_["reseller"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["status"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["trial"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["expiration"];
echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<th></th>\n                                                                <th class=\"text-center\">";
echo $_["conns"];
echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<th></th>\n                                                            </tr>\n                                                        </thead>\n                                                        <tbody></tbody>\n                                                    </table>\n                                                </div>\n                                                <ul class=\"list-inline wizard mb-0\" style=\"margin-top:20px;\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_users\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["delete_users"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </form>\n                                        </div>\n                                    </div> <!-- tab-content -->\n                                </div> <!-- end #basicwizard-->\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "\n        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-ui/jquery-ui.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var rStreams = [];\n        var rMovies = [];\n        var rSeries = [];\n        var rEpisodes = [];\n        var rUsers = [];\n        \n        function getStreamCategory() {\n            return \$(\"#stream_category_search\").val();\n        }\n        function getMovieCategory() {\n            return \$(\"#movie_category_search\").val();\n        }\n        function getSeriesCategory() {\n            return \$(\"#series_category_search\").val();\n        }\n        function getMovieFilter() {\n            return \$(\"#movie_filter\").val();\n        }\n        function getUserFilter() {\n            return \$(\"#user_filter\").val();\n        }\n        function getEpisodeFilter() {\n            return \$(\"#episode_filter\").val();\n        }\n        function getEpisodeSeries() {\n            return \$(\"#episode_series\").val();\n        }\n        function getReseller() {\n            return \$(\"#reseller_search\").val();\n        }\n        \n        function toggleStreams() {\n            \$(\"#datatable-md1 tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rStreams.splice(\$.inArray(\$(this).find(\"td:eq(0)\").html(), window.rStreams), 1);\n                    }\n                } else {            \n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rStreams.push(\$(this).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n        }\n        function toggleMovies() {\n            \$(\"#datatable-md2 tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rMovies.splice(\$.inArray(\$(this).find(\"td:eq(0)\").html(), window.rMovies), 1);\n                    }\n                } else {            \n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rMovies.push(\$(this).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n        }\n        function toggleSeries() {\n            \$(\"#datatable-md4 tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rSeries.splice(\$.inArray(\$(this).find(\"td:eq(0)\").html(), window.rSeries), 1);\n                    }\n                } else {            \n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rSeries.push(\$(this).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n        }\n        function toggleEpisodes() {\n            \$(\"#datatable-md5 tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rEpisodes.splice(\$.inArray(\$(this).find(\"td:eq(0)\").html(), window.rEpisodes), 1);\n                    }\n                } else {            \n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rEpisodes.push(\$(this).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n        }\n        function toggleUsers() {\n            \$(\"#datatable-md3 tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rUsers.splice(\$.inArray(\$(this).find(\"td:eq(0)\").html(), window.rUsers), 1);\n                    }\n                } else {            \n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rUsers.push(\$(this).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n        }\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'})\n            \$(\"#stream_form\").submit(function(e){\n                \$(\"#streams\").val(JSON.stringify(window.rStreams));\n                if (window.rStreams.length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["mass_delete_message_6"];
echo "\");\n                }\n            });\n            \$(\"#movie_form\").submit(function(e){\n                \$(\"#movies\").val(JSON.stringify(window.rMovies));\n                if (window.rMovies.length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["mass_delete_message_7"];
echo "\");\n                }\n            });\n            \$(\"#series_form\").submit(function(e){\n                \$(\"#series\").val(JSON.stringify(window.rSeries));\n                if (window.rSeries.length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["mass_delete_message_8"];
echo "\");\n                }\n            });\n            \$(\"#episodes_form\").submit(function(e){\n                \$(\"#episodes\").val(JSON.stringify(window.rEpisodes));\n                if (window.rEpisodes.length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["mass_delete_message_9"];
echo "\");\n                }\n            });\n            \$(\"#user_form\").submit(function(e){\n                \$(\"#users\").val(JSON.stringify(window.rUsers));\n                if (window.rUsers.length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["mass_delete_message_10"];
echo "\");\n                }\n            });\n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"form\").attr('autocomplete', 'off');\n            sTable = \$(\"#datatable-md1\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"stream_list\",\n                        d.category = getStreamCategory(),\n                        d.include_channels = true\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0]}\n                ],\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rStreams) !== -1) {\n                        \$(row).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    }\n                },\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo "            });\n            \$('#stream_search').keyup(function(){\n                sTable.search(\$(this).val()).draw();\n            })\n            \$('#show_entries').change(function(){\n                sTable.page.len(\$(this).val()).draw();\n            })\n            \$('#stream_category_search').change(function(){\n                sTable.ajax.reload(null, false);\n            })\n            rTable = \$(\"#datatable-md2\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"movie_list\",\n                        d.category = getMovieCategory(),\n                        d.filter = getMovieFilter()\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,3]}\n                ],\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rMovies) !== -1) {\n                        \$(row).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    }\n                },\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo "            });\n            \$('#movie_search').keyup(function(){\n                rTable.search(\$(this).val()).draw();\n            })\n            \$('#movie_show_entries').change(function(){\n                rTable.page.len(\$(this).val()).draw();\n            })\n            \$('#movie_category_search').change(function(){\n                rTable.ajax.reload(null, false);\n            })\n            \$('#movie_filter').change(function(){\n                rTable.ajax.reload( null, false );\n            })\n            gTable = \$(\"#datatable-md4\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"series_list\",\n                        d.category = getSeriesCategory()\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0]}\n                ],\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rSeries) !== -1) {\n                        \$(row).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    }\n                },\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo "            });\n            \$('#series_search').keyup(function(){\n                gTable.search(\$(this).val()).draw();\n            })\n            \$('#series_show_entries').change(function(){\n                gTable.page.len(\$(this).val()).draw();\n            })\n            \$('#series_category_search').change(function(){\n                gTable.ajax.reload(null, false);\n            })\n            eTable = \$(\"#datatable-md5\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"episode_list\",\n                        d.series = getEpisodeSeries(),\n                        d.filter = getEpisodeFilter()\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,3]}\n                ],\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rSeries) !== -1) {\n                        \$(row).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    }\n                },\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo "            });\n            \$('#episode_search').keyup(function(){\n                eTable.search(\$(this).val()).draw();\n            })\n            \$('#episode_show_entries').change(function(){\n                eTable.page.len(\$(this).val()).draw();\n            })\n            \$('#episode_series').change(function(){\n                eTable.ajax.reload(null, false);\n            })\n            \$('#episode_filter').change(function(){\n                eTable.ajax.reload( null, false );\n            })\n            uTable = \$(\"#datatable-md3\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"users\",\n                        d.filter = getUserFilter(),\n                        d.reseller = getReseller(),\n\t\t\t\t\t\td.showall = true\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,4,5,6,8]},\n                    {\"visible\": false, \"targets\": [2,7,9,10]}\n                ],\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rUsers) !== -1) {\n                        \$(row).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    }\n                },\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo "            });\n            \$('#user_search').keyup(function(){\n                uTable.search(\$(this).val()).draw();\n            })\n            \$('#user_show_entries').change(function(){\n                uTable.page.len(\$(this).val()).draw();\n            })\n            \$('#reseller_search').change(function(){\n                uTable.ajax.reload(null, false);\n            })\n            \$('#user_filter').change(function(){\n                uTable.ajax.reload( null, false );\n            })\n            \$(\"#datatable-md1\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rStreams.splice(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html(), window.rStreams), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rStreams.push(\$(ui.selected).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n            \$(\"#datatable-md2\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rMovies.splice(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html(), window.rMovies), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rMovies.push(\$(ui.selected).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n            \$(\"#datatable-md4\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rSeries.splice(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html(), window.rSeries), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rSeries.push(\$(ui.selected).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n            \$(\"#datatable-md5\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rEpisodes.splice(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html(), window.rEpisodes), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rEpisodes.push(\$(ui.selected).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n            \$(\"#datatable-md3\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rUsers.splice(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html(), window.rUsers), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rUsers.push(\$(ui.selected).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n        });\n        </script>\n    </body>\n</html>";

?>