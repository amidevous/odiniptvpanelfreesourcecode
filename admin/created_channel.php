<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "create_channel") && !hasPermissions("adv", "edit_cchannel")) {
    exit;
}
$rCategories = getCategories("live");
$rTranscodeProfiles = getTranscodeProfiles();
if (isset($_POST["submit_stream"])) {
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_cchannel")) {
            exit;
        }
        $rArray = getStream($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        if (!hasPermissions("adv", "create_channel")) {
            exit;
        }
        $rArray = ["type" => 3, "added" => time(), "read_native" => 1, "stream_all" => 1, "redirect_stream" => 0, "direct_source" => 0, "gen_timestamps" => 1, "transcode_attributes" => [], "stream_display_name" => "", "stream_source" => [], "category_id" => 0, "stream_icon" => "", "notes" => "", "custom_sid" => "", "custom_ffmpeg" => "", "custom_map" => "", "transcode_profile_id" => 0, "enable_transcode" => 0, "auto_restart" => "[]", "allow_record" => 0, "rtmp_output" => 0, "epg_id" => NULL, "channel_id" => NULL, "epg_lang" => NULL, "tv_archive_server_id" => 0, "tv_archive_duration" => 0, "delay_minutes" => 0, "external_push" => "", "probesize_ondemand" => 128000, "pids_create_channel" => [], "created_channel_location" => 0, "cchannel_rsources" => [], "series_no" => 0];
    }
    $rArary["transcode_profile_id"] = $_POST["transcode_profile_id"];
    if (!$rArray["transcode_profile_id"]) {
        $rArray["transcode_profile_id"] = 0;
    }
    if (isset($_POST["restart_on_edit"])) {
        $rRestart = true;
        unset($_POST["restart_on_edit"]);
    } else {
        $rRestart = false;
    }
    $rArray["movie_propeties"] = ["type" => intval($_POST["channel_type"])];
    if (intval($_POST["channel_type"]) == 0) {
        $rPlaylist = generateSeriesPlaylist($_POST["series_no"]);
        if ($rPlaylist["success"]) {
            $rArray["created_channel_location"] = $rPlaylist["server_id"];
            $rArray["stream_source"] = $rPlaylist["sources"];
            $rArray["series_no"] = intval($_POST["series_no"]);
            unset($_POST["created_channel_location"]);
        } else {
            $_STATUS = 2;
        }
    } else {
        $rArray["created_channel_location"] = intval($_POST["created_channel_location"]);
        $rArray["stream_source"] = $_POST["video_files"];
        $rArray["series_no"] = 0;
    }
    $rArray["cchannel_rsources"] = [];
    $rBouquets = $_POST["bouquets"];
    unset($_POST["bouquets"]);
    foreach ($_POST as $rKey => $rValue) {
        if (isset($rArray[$rKey])) {
            $rArray[$rKey] = $rValue;
        }
    }
    if (0 < count($rArray["stream_source"])) {
        if ($rAdminSettings["download_images"]) {
            $rArray["stream_icon"] = downloadImage($rArray["stream_icon"]);
        }
        $rArray["order"] = getNextOrder();
        $rCols = ESC("`" . implode("`,`", array_keys($rArray)) . "`");
        $rValues = NULL;
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
        $rQuery = "REPLACE INTO `streams`(" . $rCols . ") VALUES(" . $rValues . ");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
            } else {
                $rInsertID = $db->insert_id;
            }
        }
        if (isset($rInsertID)) {
            $rStreamExists = [];
            if (isset($_POST["edit"])) {
                $result = $db->query("SELECT `server_stream_id`, `server_id` FROM `streams_sys` WHERE `stream_id` = " . intval($rInsertID) . ";");
                if ($result && 0 < $result->num_rows) {
                    while ($row = $result->fetch_assoc()) {
                        $rStreamExists[intval($row["server_id"])] = intval($row["server_stream_id"]);
                    }
                }
            }
            if (isset($_POST["server_tree_data"])) {
                $rStreamsAdded = [];
                $rServerTree = json_decode($_POST["server_tree_data"], true);
                foreach ($rServerTree as $rServer) {
                    if ($rServer["parent"] != "#") {
                        $rServerID = intval($rServer["id"]);
                        $rStreamsAdded[] = $rServerID;
                        if ($rServer["parent"] == "source") {
                            $rParent = "NULL";
                        } else {
                            $rParent = intval($rServer["parent"]);
                        }
                        if (isset($rStreamExists[$rServerID])) {
                            $db->query("UPDATE `streams_sys` SET `parent_id` = " . $rParent . " WHERE `server_stream_id` = " . $rStreamExists[$rServerID] . ";");
                        } else {
                            $db->query("INSERT INTO `streams_sys`(`stream_id`, `server_id`, `parent_id`, `on_demand`) VALUES(" . intval($rInsertID) . ", " . $rServerID . ", " . $rParent . ", 0);");
                        }
                    }
                }
                foreach ($rStreamExists as $rServerID => $rDBID) {
                    if (!in_array($rServerID, $rStreamsAdded)) {
                        $db->query("DELETE FROM `streams_sys` WHERE `server_stream_id` = " . $rDBID . ";");
                    }
                }
            }
            if ($rRestart) {
                APIRequest(["action" => "stream", "sub" => "start", "stream_ids" => [$rInsertID]]);
            }
            foreach ($rBouquets as $rBouquet) {
                addToBouquet("stream", $rBouquet, $rInsertID);
            }
            if (isset($_POST["edit"])) {
                foreach (getBouquets() as $rBouquet) {
                    if (!in_array($rBouquet["id"], $rBouquets)) {
                        removeFromBouquet("stream", $rBouquet["id"], $rInsertID);
                    }
                }
            }
            if (0 < count($rBouquets)) {
                scanBouquets();
            }
            $_STATUS = 0;
            header("Location: ./created_channel.php?successedit&id=" . $rInsertID);
            exit;
        } else {
            $_STATUS = 1;
            $rStream = $rArray;
        }
    } else {
        if (!isset($_STATUS)) {
            $_STATUS = 1;
            $rStream = $rArray;
        }
    }
}
$rServerTree = [];
$rServerTree[] = ["id" => "source", "parent" => "#", "text" => "<strong>Stream Source</strong>", "icon" => "mdi mdi-youtube-tv", "state" => ["opened" => true]];
if (isset($_GET["id"])) {
    if (!hasPermissions("adv", "edit_cchannel")) {
        exit;
    }
    $rChannel = getStream($_GET["id"]);
    if (!$rChannel || $rChannel["type"] != 3) {
        exit;
    }
    $rProperties = json_decode($rChannel["movie_propeties"], true);
    if (!$rProperties) {
        if (0 < $rChannel["series_no"]) {
            $rProperties = ["type" => 0];
        } else {
            $rProperties = ["type" => 1];
        }
    }
    $rChannelSys = getStreamSys($_GET["id"]);
    foreach ($rServers as $rServer) {
        if (isset($rChannelSys[intval($rServer["id"])])) {
            if ($rChannelSys[intval($rServer["id"])]["parent_id"] != 0) {
                $rParent = intval($rChannelSys[intval($rServer["id"])]["parent_id"]);
            } else {
                $rParent = "source";
            }
        } else {
            $rParent = "#";
        }
        $rServerTree[] = ["id" => $rServer["id"], "parent" => $rParent, "text" => $rServer["server_name"], "icon" => "mdi mdi-server-network", "state" => ["opened" => true]];
    }
} else {
    if (!hasPermissions("adv", "create_channel")) {
        exit;
    }
    foreach ($rServers as $rServer) {
        $rServerTree[] = ["id" => $rServer["id"], "parent" => "#", "text" => $rServer["server_name"], "icon" => "mdi mdi-server-network", "state" => ["opened" => true]];
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
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
}
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                        <a href=\"./streams.php?filter=8\">\r\n                                            <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\">\r\n                                                ";
echo $_["view_channels"];
echo "                                            </button>\r\n                                        </a>\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
if (isset($rChannel)) {
    echo $rChannel["stream_display_name"] . " &nbsp;<button type=\"button\" class=\"btn btn-outline-info waves-effect waves-light btn-xs\" onClick=\"player(" . $rChannel["id"] . ", '" . json_decode($rChannel["target_container"], true)[0] . "');\"><i class=\"mdi mdi-play\"></i></button>";
} else {
    echo $_["create_channel"];
}
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        ";
if (count($rTranscodeProfiles) == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            ";
        echo $_["you_need_at_least_one"];
        echo "                        </div>\r\n                        ";
    } else {
        echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
        echo $_["you_need_at_least_one"];
        echo "', \"success\");\r\n  \t\t\t\t\t</script>\r\n                        ";
    }
}
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
        echo $_["channel_operation_was"];
        echo "                        </div>\r\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
        echo $_["channel_operation_was"];
        echo "', \"success\");\r\n  \t\t\t\t\t</script>\r\n                        ";
    }
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
            echo $_["generic_fail"];
            echo "                        </div>\r\n\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
            echo $_["generic_fail"];
            echo "', \"warning\");\r\n  \t\t\t\t\t</script>\r\n                        ";
        }
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
            if (!$rSettings["sucessedit"]) {
                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
                echo $_["the_series_you_have"];
                echo "                        </div>\r\n                        ";
            } else {
                echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
                echo $_["the_series_you_have"];
                echo "', \"warning\");\r\n  \t\t\t\t\t</script>\r\n                        ";
            }
        }
    }
}
if (isset($rChannel)) {
    echo "                        <div class=\"card text-xs-center\">\r\n                            <div class=\"table\">\r\n                                <table id=\"datatable-list\" class=\"table table-borderless mb-0\">\r\n                                    <thead class=\"bg-light\">\r\n                                        <tr>\r\n                                            <th></th>\r\n                                            <th></th>\r\n                                            <th></th>\r\n                                            <th>";
    echo $_["source"];
    echo "</th>\r\n                                            <th>";
    echo $_["clients"];
    echo "</th>\r\n                                            <th>";
    echo $_["uptime"];
    echo "</th>\r\n                                            <th>";
    echo $_["actions"];
    echo "</th>\r\n                                            <th></th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody>\r\n                                        <tr>\r\n                                            <td colspan=\"8\" class=\"text-center\">";
    echo $_["loading_channel_information"];
    echo "</td>\r\n                                        </tr>\r\n                                    </tbody>\r\n                                </table>\r\n                            </div>\r\n                        </div>\r\n                        ";
    $rEncodeErrors = getEncodeErrors($rChannel["id"]);
    foreach ($rEncodeErrors as $rServerID => $rEncodeError) {
        echo "                        <div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            <strong>";
        echo $_["error_on_server"];
        echo " ";
        echo $rServers[$rServerID]["server_name"];
        echo "</strong><br/>\r\n                            ";
        echo str_replace("\n", "<br/>", $rEncodeError);
        echo "                        </div>\r\n                        ";
    }
}
echo "                        <div class=\"card\">\r\n                            <div class=\"card-body\">\r\n                                <form action=\"./created_channel.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"stream_form\" data-parsley-validate=\"\">\r\n                                    ";
if (isset($rChannel)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rChannel["id"];
    echo "\" />\r\n                                    ";
}
echo "                                    <input type=\"hidden\" name=\"created_channel_location\" id=\"created_channel_location\" value=\"";
if (isset($rChannel)) {
    echo $rChannel["created_channel_location"];
}
echo "\" />\r\n                                    <input type=\"hidden\" name=\"video_files\" id=\"video_files\" value=\"\" />\r\n                                    <input type=\"hidden\" name=\"server_tree_data\" id=\"server_tree_data\" value=\"\" />\r\n                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#stream-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\" id=\"selection_nav\">\r\n                                                <a href=\"#selection\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-movie mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["selection"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\" id=\"review_nav\">\r\n                                                <a href=\"#review\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-marker mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["review"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\" id=\"videos_nav\">\r\n                                                <a href=\"#videos\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-movie mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["videos"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#load-balancing\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\r\n                                                    <i class=\"mdi mdi-server-network mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["servers"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n                                            <div class=\"tab-pane\" id=\"stream-details\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"channel_type\">";
echo $_["seletion_type"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"channel_type\" id=\"channel_type\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    ";
foreach (["Series", "File Browser", "VOD Selection"] as $rID => $rType) {
    echo "                                                                    <option ";
    if (isset($rChannel) && $rProperties["type"] == $rID) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rID;
    echo "\">";
    echo $rType;
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\" id=\"series_nav\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"series_no\">";
echo $_["24/7_series"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"series_no\" id=\"series_no\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    <option value=\"0\">";
echo $_["select_a_series"];
echo "...</option>\r\n                                                                    ";
foreach (getSeries() as $rSeries) {
    echo "                                                                    <option ";
    if (isset($rChannel) && intval($rChannel["series_no"]) == intval($rSeries["id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rSeries["id"];
    echo "\">";
    echo $rSeries["title"];
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_display_name\">";
echo $_["channel_name"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"stream_display_name\" name=\"stream_display_name\" value=\"";
if (isset($rChannel)) {
    echo htmlspecialchars($rChannel["stream_display_name"]);
}
echo "\" required data-parsley-trigger=\"change\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_id\">";
echo $_["category_name"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"category_id\" id=\"category_id\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    ";
foreach ($rCategories as $rCategory) {
    echo "                                                                    <option ";
    if (isset($rChannel)) {
        if (intval($rChannel["category_id"]) == intval($rCategory["id"])) {
            echo "selected ";
        }
    } else {
        if (isset($_GET["category"]) && $_GET["category"] == $rCategory["id"]) {
            echo "selected ";
        }
    }
    echo "value=\"";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"transcode_profile_id\">";
echo $_["transcoding_profile"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"transcode_profile_id\" id=\"transcode_profile_id\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    ";
foreach ($rTranscodeProfiles as $rProfile) {
    echo "                                                                    <option ";
    if (isset($rChannel) && intval($rChannel["transcode_profile_id"]) == intval($rProfile["profile_id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rProfile["profile_id"];
    echo "\">";
    echo $rProfile["profile_name"];
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"bouquets\">";
echo $_["add_to_bouquets"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"bouquets[]\" id=\"bouquets\" class=\"form-control select2-multiple select2\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "\">\r\n                                                                    ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                                    <option ";
    if (isset($rChannel) && in_array($rChannel["id"], json_decode($rBouquet["bouquet_channels"], true))) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rBouquet["id"];
    echo "\">";
    echo $rBouquet["bouquet_name"];
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_icon\">";
echo $_["stream_logo_url"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"stream_icon\" name=\"stream_icon\" value=\"";
if (isset($rChannel)) {
    echo htmlspecialchars($rChannel["stream_icon"]);
}
echo "\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"notes\">";
echo $_["notes"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <textarea id=\"notes\" name=\"notes\" class=\"form-control\" rows=\"3\" placeholder=\"\">";
if (isset($rChannel)) {
    echo htmlspecialchars($rChannel["notes"]);
}
echo "</textarea>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <a href=\"javascript: void(0);\" id=\"next_0\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            <div class=\"tab-pane\" id=\"selection\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"server_idc\">";
echo $_["server_name"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select id=\"server_idc\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    ";
foreach (getStreamingServers() as $rServer) {
    echo "                                                                    <option value=\"";
    echo $rServer["id"];
    echo "\"";
    if (isset($rChannel) && $rChannel["created_channel_location"] == $rServer["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rServer["server_name"];
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_name\">";
echo $_["category_series"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select id=\"category_idv\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    <option value=\"\" selected>";
echo $_["no_filter"];
echo "</option>\r\n                                                                    ";
foreach (getCategories("movie") as $rCategory) {
    echo "                                                                    <option value=\"0:";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\r\n                                                                    ";
}
foreach (getSeriesList() as $rSeries) {
    echo "                                                                    <option value=\"1:";
    echo $rSeries["id"];
    echo "\">";
    echo $rSeries["title"];
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"vod_search\">";
echo $_["search"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"vod_search\" value=\"\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <table id=\"datatable-vod\" class=\"table nowrap\">\r\n                                                                <thead>\r\n                                                                    <tr>\r\n                                                                        <th class=\"text-center\">";
echo $_["id"];
echo "</th>\r\n                                                                        <th>";
echo $_["name"];
echo "</th>\r\n                                                                        <th>";
echo $_["category_series"];
echo "</th>\r\n                                                                        <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\r\n                                                                    </tr>\r\n                                                                </thead>\r\n                                                                <tbody></tbody>\r\n                                                            </table>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"previous list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\r\n                                                    </li>\r\n                                                    <span class=\"float-right\">\r\n                                                        <li class=\"next list-inline-item\">\r\n                                                            <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\r\n                                                        </li>\r\n                                                    </span>\r\n                                                </ul>\r\n                                            </div>\r\n                                            <div class=\"tab-pane\" id=\"review\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <div class=\"form-group row mb-4 stream-url\">\r\n                                                            <div class=\"col-md-12\">\r\n                                                                <select multiple id=\"review_sort\" name=\"review_sort\" class=\"form-control\" style=\"min-height:400px;\">\r\n                                                                ";
if (isset($rChannel) && in_array(intval($rProperties["type"]), [2])) {
    foreach (json_decode($rChannel["stream_source"], true) as $rSource) {
        echo "                                                                    <option value=\"";
        echo $rSource;
        echo "\">";
        echo $rSource;
        echo "</option>\r\n                                                                ";
    }
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveUp('review')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveDown('review')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"AtoZ('review')\" class=\"btn btn-info\">";
echo $_["a_to_z"];
echo "</a>\r\n                                                    </li>\r\n                                                    <li class=\"next list-inline-item float-right\">\r\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            <div class=\"tab-pane\" id=\"videos\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <div class=\"form-group row mb-4 stream-url\">\r\n                                                            <label class=\"col-md-3 col-form-label\" for=\"import_folder\">";
echo $_["import_folder"];
echo "</label>\r\n                                                            <div class=\"col-md-9 input-group\">\r\n                                                                <input type=\"text\" id=\"import_folder\" name=\"import_folder\" readonly class=\"form-control\" value=\"";
if (isset($rChannel)) {
    echo htmlspecialchars($rServers[$rChannel["created_channel_location"]]["server_name"]);
}
echo "\">\r\n                                                                <div class=\"input-group-append\">\r\n                                                                    <a href=\"#file-browser\" id=\"filebrowser\" class=\"btn btn-primary waves-effect waves-light\"><i class=\"mdi mdi-folder-open-outline\"></i></a>\r\n                                                                </div>\r\n                                                            </div>\r\n                                                            <div class=\"col-md-12 add-margin-top-20\">\r\n                                                                <select multiple id=\"videos_sort\" name=\"videos_sort\" class=\"form-control\" style=\"min-height:400px;\">\r\n                                                                ";
if (isset($rChannel) && in_array(intval($rProperties["type"]), [1])) {
    foreach (json_decode($rChannel["stream_source"], true) as $rSource) {
        echo "                                                                    <option value=\"";
        echo $rSource;
        echo "\">";
        echo $rSource;
        echo "</option>\r\n                                                                ";
    }
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveUp('videos')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveDown('videos')\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"Remove('videos')\" class=\"btn btn-warning\"><i class=\"mdi mdi-close\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"AtoZ('videos')\" class=\"btn btn-info\">";
echo $_["a_to_z"];
echo "</a>\r\n                                                    </li>\r\n                                                    <li class=\"next list-inline-item float-right\">\r\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            <div class=\"tab-pane\" id=\"load-balancing\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"servers\">";
echo $_["server_tree"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <div id=\"server_tree\"></div>\r\n                                                            </div>\r\n                                                        </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"restart_on_edit\">";
if (isset($rChannel)) {
    echo $_["restart_on_edit"];
} else {
    echo $_["start_channel_now"];
}
echo "</label>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input name=\"restart_on_edit\" id=\"restart_on_edit\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" id=\"previous_0\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\r\n                                                    </li>\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <input name=\"submit_stream\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rChannel)) {
    echo $_["edit"];
} else {
    echo $_["create"];
}
echo "\" />\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                        </div> <!-- tab-content -->\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </form>\r\n                                <div id=\"file-browser\" class=\"mfp-hide white-popup-block\">\r\n                                    <div class=\"col-12\">\r\n                                        <div class=\"form-group row mb-4\">\r\n                                            <label class=\"col-md-4 col-form-label\" for=\"server_id\">";
echo $_["server_name"];
echo "</label>\r\n                                            <div class=\"col-md-8\">\r\n                                                <select id=\"server_id\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                    ";
foreach (getStreamingServers() as $rServer) {
    echo "                                                    <option value=\"";
    echo $rServer["id"];
    echo "\"";
    if (isset($rChannel) && $rChannel["created_channel_location"] == $rServer["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rServer["server_name"];
    echo "</option>\r\n                                                    ";
}
echo "                                                </select>\r\n                                            </div>\r\n                                        </div>\r\n                                        <div class=\"form-group row mb-4\">\r\n                                            <label class=\"col-md-4 col-form-label\" for=\"current_path\">";
echo $_["current_path"];
echo "</label>\r\n                                            <div class=\"col-md-8 input-group\">\r\n                                                <input type=\"text\" id=\"current_path\" name=\"current_path\" class=\"form-control\" value=\"/\">\r\n                                                <div class=\"input-group-append\">\r\n                                                    <button class=\"btn btn-primary waves-effect waves-light\" type=\"button\" id=\"changeDir\"><i class=\"mdi mdi-chevron-right\"></i></button>\r\n                                                </div>\r\n                                            </div>\r\n                                        </div>\r\n                                        <div class=\"form-group row mb-4\">\r\n                                            <div class=\"col-md-6\">\r\n                                                <table id=\"datatable\" class=\"table\">\r\n                                                    <thead>\r\n                                                        <tr>\r\n                                                            <th width=\"20px\"></th>\r\n                                                            <th>";
echo $_["directory"];
echo "</th>\r\n                                                        </tr>\r\n                                                    </thead>\r\n                                                    <tbody></tbody>\r\n                                                </table>\r\n                                            </div>\r\n                                            <div class=\"col-md-6\">\r\n                                                <table id=\"datatable-files\" class=\"table\">\r\n                                                    <thead>\r\n                                                        <tr>\r\n                                                            <th width=\"20px\"></th>\r\n                                                            <th>";
echo $_["filename"];
echo "</th>\r\n                                                        </tr>\r\n                                                    </thead>\r\n                                                    <tbody></tbody>\r\n                                                </table>\r\n                                            </div>\r\n                                        </div>\r\n                                        <div class=\"float-right\">\r\n                                            <input id=\"select_folder\" type=\"button\" class=\"btn btn-info\" value=\"";
echo $_["add_this_directory"];
echo "\" />\r\n                                        </div>\r\n                                    </div> <!-- end col -->\r\n                                </div>\r\n                            </div> <!-- end card-body -->\r\n                        </div> <!-- end card-->\r\n                    </div> <!-- end col -->\r\n                </div>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\r\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        \r\n        <script>\r\n        var changeTitle = false;\r\n        var rSwitches = [];\r\n        var rChannels = {};\r\n                \r\n        ";
if (isset($rChannel) && $rProperties["type"] == 2) {
    echo "        var rSelection = ";
    echo json_encode(getSelections(json_decode($rChannel["stream_source"], true)));
    echo ";\r\n        ";
} else {
    echo "        var rSelection = [];\r\n        ";
}
echo "        \r\n        function AtoZ(rType) {\r\n            \$(\"#\" + rType + \"_sort\").append(\$(\"#\" + rType + \"_sort option\").remove().sort(function(a, b) {\r\n                var at = \$(a).text().toUpperCase().split(\"/\").pop(), bt = \$(b).text().toUpperCase().split(\"/\").pop();\r\n                return (at > bt) ? 1 : ((at < bt) ? -1 : 0);\r\n            }));\r\n        }\r\n        function MoveUp(rType) {\r\n            var rSelected = \$('#' + rType + '_sort option:selected');\r\n            if (rSelected.length) {\r\n                var rPrevious = rSelected.first().prev()[0];\r\n                if (\$(rPrevious).html() != '') {\r\n                    rSelected.first().prev().before(rSelected);\r\n                }\r\n            }\r\n        }\r\n        function MoveDown(rType) {\r\n            var rSelected = \$('#' + rType + '_sort option:selected');\r\n            if (rSelected.length) {\r\n                rSelected.last().next().after(rSelected);\r\n            }\r\n        }\r\n        function Remove(rType) {\r\n            var rSelected = \$('#' + rType + '_sort option:selected');\r\n            if (rSelected.length) {\r\n                rSelected.remove();\r\n            }\r\n        }\r\n        function getCategory() {\r\n            return \$(\"#category_idv\").val();\r\n        }\r\n        function getServer() {\r\n            return \$(\"#server_idc\").val();\r\n        }\r\n        (function(\$) {\r\n          \$.fn.inputFilter = function(inputFilter) {\r\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\r\n              if (inputFilter(this.value)) {\r\n                this.oldValue = this.value;\r\n                this.oldSelectionStart = this.selectionStart;\r\n                this.oldSelectionEnd = this.selectionEnd;\r\n              } else if (this.hasOwnProperty(\"oldValue\")) {\r\n                this.value = this.oldValue;\r\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\r\n              }\r\n            });\r\n          };\r\n        }(jQuery));\r\n        \r\n        function toggleSelection(rID) {\r\n            var rIndex = rSelection.indexOf(parseInt(rID));\r\n            if (rIndex > -1) {\r\n                rSelection = jQuery.grep(rSelection, function(rValue) {\r\n                    return parseInt(rValue) != parseInt(rID);\r\n                });\r\n            } else {\r\n                rSelection.push(parseInt(rID));\r\n            }\r\n            \$(\"#datatable-vod\").DataTable().ajax.reload(null, false);\r\n            reviewSelection();\r\n        }\r\n        \r\n        function reviewSelection() {\r\n            \$.post(\"./api.php?action=review_selection\", {\"data\": rSelection}, function(rData) {\r\n                if (rData.result === true) {\r\n                    var rActiveStreams = [];\r\n                    \$(rData.streams).each(function(rIndex) {\r\n                        rStreamSource = \$.parseJSON(rData.streams[rIndex][\"stream_source\"])[0].replace(\"s:\" + \$(\"#server_idc\").val() + \":\", \"\");\r\n                        rActiveStreams.push(rStreamSource);\r\n                        rExt = rStreamSource.split('.').pop().toLowerCase();\r\n                        if (([\"mp4\", \"mkv\", \"mov\", \"avi\", \"mpg\", \"mpeg\", \"flv\", \"wmv\"].includes(rExt)) && (\$(\"#review_sort option[value='\" + rStreamSource.replace(\"'\", \"\\\\'\") + \"']\").length == 0)) {\r\n                            \$(\"#review_sort\").append(new Option(rStreamSource, rStreamSource));\r\n                        }\r\n                    });\r\n                    \$(\"#review_sort option\").each(function() {\r\n                        if (!rActiveStreams.includes(\$(this).val())) {\r\n                            \$(this).remove();\r\n                        }\r\n                    });\r\n                }\r\n            }, \"json\");\r\n        }\r\n        \r\n        function api(rID, rServerID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('";
echo $_["are_you_sure_you_want_to_delete_this_channel"];
echo "') == false) {\r\n                    return;\r\n                }\r\n            }\r\n            \$.getJSON(\"./api.php?action=stream&sub=\" + rType + \"&stream_id=\" + rID + \"&server_id=\" + rServerID, function(data) {\r\n                if (data.result == true) {\r\n                    if (rType == \"start\") {\r\n                        \$.toast(\"";
echo $_["channel_successfully_started"];
echo "\");\r\n                    } else if (rType == \"restart\") {\r\n                        \$.toast(\"";
echo $_["channel_successfully_restarted"];
echo "\");\r\n                    } else if (rType == \"stop\") {\r\n                        \$.toast(\"";
echo $_["channel_successfully_stopped"];
echo "\");\r\n                    } else if (rType == \"delete\") {\r\n                        \$.toast(\"";
echo $_["channel_successfully_deleted"];
echo "\");\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {\r\n                        \$(this).remove();\r\n                    });\r\n                    \$(\"#datatable-list\").DataTable().ajax.reload( null, false );\r\n                } else {\r\n                    \$.toast(\"";
echo $_["an_error_occured_while_processing_your_request"];
echo "\");\r\n                }\r\n            }).fail(function() {\r\n                \$.toast(\"";
echo $_["an_error_occured_while_processing_your_request"];
echo "\");\r\n            });\r\n        }\r\n        function selectDirectory(elem) {\r\n            window.currentDirectory += elem + \"/\";\r\n            \$(\"#current_path\").val(window.currentDirectory);\r\n            \$(\"#changeDir\").click();\r\n        }\r\n        function selectParent() {\r\n            \$(\"#current_path\").val(window.currentDirectory.split(\"/\").slice(0,-2).join(\"/\") + \"/\");\r\n            \$(\"#changeDir\").click();\r\n        }\r\n        function reloadStream() {\r\n            \$(\"#datatable-list\").DataTable().ajax.reload( null, false );\r\n            setTimeout(reloadStream, 5000);\r\n        }\r\n        function player(rID, rContainer) {\r\n            \$.magnificPopup.open({\r\n                items: {\r\n                    src: \"./player.php?type=live&id=\" + rID + \"&container=\" + rContainer,\r\n                    type: 'iframe'\r\n                }\r\n            });\r\n        }\r\n        \$(document).ready(function() {\r\n            \$('.select2').select2({width: '100%'});\r\n            \r\n            \$(\"#datatable\").DataTable({\r\n                responsive: false,\r\n                paging: false,\r\n                bInfo: false,\r\n                searching: false,\r\n                scrollY: \"250px\",\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0]},\r\n                ],\r\n                \"language\": {\r\n                    \"emptyTable\": \"\"\r\n                }\r\n            });\r\n            \r\n            \$(\"#datatable-files\").DataTable({\r\n                responsive: false,\r\n                paging: false,\r\n                bInfo: false,\r\n                searching: true,\r\n                scrollY: \"250px\",\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0]},\r\n                ],\r\n                \"language\": {\r\n                    \"emptyTable\": \"";
echo $_["emptyTable"];
echo "\"\r\n                }\r\n            });\r\n            \r\n            \$(\"#datatable-vod\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    }\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                },\r\n                createdRow: function(row, data, index) {\r\n                    \$(row).addClass('vod-' + data[0]);\r\n                    var rIndex = rSelection.indexOf(parseInt(data[0]));\r\n                    if (rIndex > -1) {\r\n                        \$(row).find(\".btn-remove\").show();\r\n                    } else {\r\n                        \$(row).find(\".btn-add\").show();\r\n                    }\r\n                },\r\n                bInfo: false,\r\n                bAutoWidth: false,\r\n                searching: true,\r\n                pageLength: 100,\r\n                lengthChange: false,\r\n                processing: true,\r\n                serverSide: true,\r\n                ajax: {\r\n                    url: \"./table.php\",\r\n                    \"data\": function(d) {\r\n                        d.id = \"vod_selection\";\r\n                        d.category_id = getCategory();\r\n                        d.server_id = getServer();\r\n                    }\r\n                },\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0,3]}\r\n                ],\r\n            });\r\n            \r\n            \$(\"#category_idv\").on(\"select2:select\", function(e) { \r\n                \$(\"#datatable-vod\").DataTable().ajax.reload(null, false);\r\n            });\r\n            \$('#vod_search').keyup(function(){\r\n                \$('#datatable-vod').DataTable().search(\$(this).val()).draw();\r\n            })\r\n            \r\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\r\n            elems.forEach(function(html) {\r\n              var switchery = new Switchery(html);\r\n              window.rSwitches[\$(html).attr(\"id\")] = switchery;\r\n            });\r\n            \r\n            \$(\"#select_folder\").click(function() {\r\n                if (\$(\"#server_id\").val() != \$(\"#created_channel_location\").val()) {\r\n                    \$(\"#created_channel_location\").val(\$(\"#server_id\").val());\r\n                    \$(\"#videos_sort\").empty();\r\n                }\r\n                \$(\"#import_folder\").val(\$(\"#server_id option:selected\").text());\r\n                \$(\"#datatable-files\").DataTable().rows().every(function ( rowIdx, tableLoop, rowLoop) {\r\n                    var data = this.data();\r\n                    rExt = data[1].split('.').pop().toLowerCase();\r\n                    if (([\"mp4\", \"mkv\", \"mov\", \"avi\", \"mpg\", \"mpeg\", \"flv\", \"wmv\"].includes(rExt)) && (\$(\"#videos_sort option[value='\" + (window.currentDirectory + data[1]).replace(\"'\", \"\\\\'\") + \"']\").length == 0)) {\r\n                        \$(\"#videos_sort\").append(new Option(window.currentDirectory + data[1], window.currentDirectory + data[1]));\r\n                    }\r\n                });\r\n                \$.magnificPopup.close();\r\n            });\r\n            \r\n            \$(\"#changeDir\").click(function() {\r\n                window.currentDirectory = \$(\"#current_path\").val();\r\n                if (window.currentDirectory.substr(-1) != \"/\") {\r\n                    window.currentDirectory += \"/\";\r\n                }\r\n                \$(\"#current_path\").val(window.currentDirectory);\r\n                \$(\"#datatable\").DataTable().clear();\r\n                \$(\"#datatable\").DataTable().row.add([\"\", \"";
echo $_["loading"];
echo "...\"]);\r\n                \$(\"#datatable\").DataTable().draw(true);\r\n                \$(\"#datatable-files\").DataTable().clear();\r\n                \$(\"#datatable-files\").DataTable().row.add([\"\", \"";
echo $_["please_wait"];
echo "...\"]);\r\n                \$(\"#datatable-files\").DataTable().draw(true);\r\n                rFilter = \"video\";\r\n                \$.getJSON(\"./api.php?action=listdir&dir=\" + window.currentDirectory + \"&server=\" + \$(\"#server_id\").val() + \"&filter=\" + rFilter, function(data) {\r\n                    \$(\"#datatable\").DataTable().clear();\r\n                    \$(\"#datatable-files\").DataTable().clear();\r\n                    if (window.currentDirectory != \"/\") {\r\n                        \$(\"#datatable\").DataTable().row.add([\"<i class='mdi mdi-subdirectory-arrow-left'></i>\", \"Parent Directory\"]);\r\n                    }\r\n                    if (data.result == true) {\r\n                        \$(data.data.dirs).each(function(id, dir) {\r\n                            \$(\"#datatable\").DataTable().row.add([\"<i class='mdi mdi-folder-open-outline'></i>\", dir]);\r\n                        });\r\n                        \$(\"#datatable\").DataTable().draw(true);\r\n                        \$(data.data.files).each(function(id, dir) {\r\n                            \$(\"#datatable-files\").DataTable().row.add([\"<i class='mdi mdi-file-video'></i>\", dir]);\r\n                        });\r\n                        \$(\"#datatable-files\").DataTable().draw(true);\r\n                    }\r\n                });\r\n            });\r\n            \r\n            \$('#datatable').on('click', 'tbody > tr', function() {\r\n                if (\$(this).find(\"td\").eq(1).html() == \"Parent Directory\") {\r\n                    selectParent();\r\n                } else {\r\n                    selectDirectory(\$(this).find(\"td\").eq(1).html());\r\n                }\r\n            });\r\n            \$('#server_tree').jstree({ 'core' : {\r\n                'check_callback': function (op, node, parent, position, more) {\r\n                    switch (op) {\r\n                        case 'move_node':\r\n                            if (node.id == \"source\") { return false; }\r\n                            return true;\r\n                    }\r\n                },\r\n                'data' : ";
echo json_encode($rServerTree);
echo "            }, \"plugins\" : [ \"dnd\" ]\r\n            });\r\n            \r\n            \$(\"#stream_form\").submit(function(e){\r\n                var rVideoFiles = [];\r\n                if (\$(\"#channel_type\").val() == 0) {\r\n                    if (\$(\"#series_no\").val() == 0) {\r\n                        \$.toast(\"";
echo $_["please_select_a_series_to_map"];
echo "\");\r\n                        e.preventDefault();\r\n                    }\r\n                } else if (\$(\"#channel_type\").val() == 1) {\r\n                    if (\$(\"#videos_sort option\").length == 0) {\r\n                        \$.toast(\"";
echo $_["please_add_at_least_one_video_to_the_channel"];
echo "\");\r\n                        e.preventDefault();\r\n                    }\r\n                    \$(\"#videos_sort option\").each(function() {\r\n                        rVideoFiles.push(\$(this).val());\r\n                    });\r\n                    \$(\"#created_channel_location\").val(\$(\"#server_id\").val());\r\n                } else if (\$(\"#channel_type\").val() == 2) {\r\n                    if (\$(\"#review_sort option\").length == 0) {\r\n                        \$.toast(\"";
echo $_["please_add_at_least_one_video_to_the_channel"];
echo "\");\r\n                        e.preventDefault();\r\n                    }\r\n                    \$(\"#review_sort option\").each(function() {\r\n                        rVideoFiles.push(\$(this).val());\r\n                    });\r\n                    \$(\"#created_channel_location\").val(\$(\"#server_idc\").val());\r\n                }\r\n                if (!\$(\"#transcode_profile_id\").val()) {\r\n                    \$.toast(\"Please select a trancoding profile.\");\r\n                    e.preventDefault();\r\n                }\r\n                \$(\"#server_tree_data\").val(JSON.stringify(\$('#server_tree').jstree(true).get_json('#', {flat:true})));\r\n                \$(\"#video_files\").val(JSON.stringify(rVideoFiles));\r\n            });\r\n            \r\n            \$(\"#filebrowser\").magnificPopup({\r\n                type: 'inline',\r\n                preloader: false,\r\n                focus: '#server_id',\r\n                callbacks: {\r\n                    beforeOpen: function() {\r\n                        if (\$(window).width() < 830) {\r\n                            this.st.focus = false;\r\n                        } else {\r\n                            this.st.focus = '#server_id';\r\n                        }\r\n                    }\r\n                }\r\n            });\r\n            \$(\"#filebrowser-sub\").magnificPopup({\r\n                type: 'inline',\r\n                preloader: false,\r\n                focus: '#server_id',\r\n                callbacks: {\r\n                    beforeOpen: function() {\r\n                        if (\$(window).width() < 830) {\r\n                            this.st.focus = false;\r\n                        } else {\r\n                            this.st.focus = '#server_id';\r\n                        }\r\n                    }\r\n                }\r\n            });\r\n            \r\n            \$(\"#filebrowser\").on(\"mfpOpen\", function() {\r\n                \$(\"#changeDir\").click();\r\n                \$(\$.fn.dataTable.tables(true)).css('width', '100%');\r\n                \$(\$.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();\r\n            });\r\n            \$(\"#filebrowser-sub\").on(\"mfpOpen\", function() {\r\n                \$(\"#changeDir\").click();\r\n                \$(\$.fn.dataTable.tables(true)).css('width', '100%');\r\n                \$(\$.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();\r\n            });\r\n            \r\n            \$(document).keypress(function(event){\r\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n            });\r\n            \$(\"#server_id\").change(function() {\r\n                \$(\"#current_path\").val(\"/\");\r\n                \$(\"#changeDir\").click();\r\n            });\r\n            \r\n            \$(\"#series_no\").change(function() {\r\n                if (\$(\"#series_no\").val() > 0) {\r\n                    \$(\"#stream_display_name\").val(\"24/7 \" + \$(\"#series_no option:selected\").text());\r\n                }\r\n            });\r\n            \r\n            \$(\"#channel_type\").change(function() {\r\n                if (\$(\"#channel_type\").val() == 0) {\r\n                    \$(\"#review_nav\").hide();\r\n                    \$(\"#selection_nav\").hide()\r\n                    \$(\"#videos_nav\").hide();\r\n                    \$(\"#series_nav\").show();\r\n                } else if (\$(\"#channel_type\").val() == 1) {\r\n                    \$(\"#review_nav\").hide();\r\n                    \$(\"#selection_nav\").hide()\r\n                    \$(\"#videos_nav\").show();\r\n                    \$(\"#series_nav\").hide();\r\n                } else {\r\n                    \$(\"#review_nav\").show();\r\n                    \$(\"#selection_nav\").show()\r\n                    \$(\"#videos_nav\").hide();\r\n                    \$(\"#series_nav\").hide();\r\n                }\r\n            });\r\n            \r\n            \$(\"#server_idc\").change(function() {\r\n                \$(\"#review_sort\").empty();\r\n                \$(\"#datatable-vod\").DataTable().ajax.reload(null, false);\r\n            });\r\n            \r\n            ";
if (isset($rChannel)) {
    echo "            \$(\"#datatable-list\").DataTable({\r\n                ordering: false,\r\n                paging: false,\r\n                searching: false,\r\n                processing: true,\r\n                serverSide: true,\r\n                bInfo: false,\r\n                ajax: {\r\n                    url: \"./table_search.php\",\r\n                    \"data\": function(d) {\r\n                        d.id = \"streams\";\r\n                        d.stream_id = ";
    echo $rChannel["id"];
    echo ";\r\n                    }\r\n                },\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [3,4,5,6]},\r\n                    {\"visible\": false, \"targets\": [0,1,2,7]}\r\n                ],\r\n            });\r\n            setTimeout(reloadStream, 5000);\r\n            \$(\"#season_num\").trigger('change');\r\n            ";
}
echo "            \r\n            \$(\"#next_0\").click(function() {\r\n                if (\$(\"#channel_type\").val() == 0) {\r\n                    \$('[href=\"#load-balancing\"]').tab('show');\r\n                } else if (\$(\"#channel_type\").val() == 1) {\r\n                    \$('[href=\"#selection\"]').tab('show');\r\n                } else {\r\n                    \$('[href=\"#videos\"]').tab('show');\r\n                }\r\n            });\r\n            \$(\"#previous_0\").click(function() {\r\n                if (\$(\"#channel_type\").val() == 0) {\r\n                    \$('[href=\"#stream-details\"]').tab('show');\r\n                } else if (\$(\"#channel_type\").val() == 1) {\r\n                    \$('[href=\"#videos\"]').tab('show');\r\n                } else {\r\n                    \$('[href=\"#review\"]').tab('show');\r\n                }\r\n            });\r\n            \r\n            \$(\"form\").attr('autocomplete', 'off');\r\n            \$(\"#changeDir\").click();\r\n            \$(\"#channel_type\").trigger('change');\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>