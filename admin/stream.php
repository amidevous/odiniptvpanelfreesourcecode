<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_stream") && !hasPermissions("adv", "edit_stream")) {
    exit;
}
if (isset($_GET["import"]) && !hasPermissions("adv", "import_streams")) {
    exit;
}
if (isset($_POST["link"])) {
    $check = shell_exec("/home/xtreamcodes/iptv_xtream_codes/bin/ffprobe -v error -select_streams v:0 -show_entries stream=height,width,avg_frame_rate  -of csv=s=x:p=0 \"" . $_POST["link"] . "\" -of json");
    $checkjson = json_decode($check);
    if (!empty($checkjson->streams[0]->avg_frame_rate)) {
        $width = $checkjson->streams[0]->width;
        $height = $checkjson->streams[0]->height;
        $avg_frame_rate = $checkjson->streams[0]->avg_frame_rate;
        echo json_encode(["valid" => true, "message" => "Width: " . $width . ", Height: " . $height . ", Frame Rate: " . $avg_frame_rate]);
        exit;
    }
    echo json_encode(["valid" => false, "message" => "Dead Source, Please replace."]);
    exit;
}
if (isset($_POST["submit_stream"])) {
    set_time_limit(0);
    ini_set("mysql.connect_timeout", 0);
    ini_set("max_execution_time", 0);
    ini_set("default_socket_timeout", 0);
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_stream")) {
            exit;
        }
        $rArray = getStream($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        if (!hasPermissions("adv", "add_stream")) {
            exit;
        }
        $rArray = ["type" => 1, "added" => time(), "read_native" => 0, "stream_all" => 0, "redirect_stream" => 1, "direct_source" => 0, "gen_timestamps" => 1, "transcode_attributes" => [], "stream_display_name" => "", "stream_source" => [], "category_id" => 0, "stream_icon" => "", "notes" => "", "custom_sid" => "", "custom_ffmpeg" => "", "custom_map" => "", "transcode_profile_id" => 0, "enable_transcode" => 0, "auto_restart" => "[]", "allow_record" => 1, "rtmp_output" => 0, "epg_id" => NULL, "channel_id" => NULL, "epg_lang" => NULL, "tv_archive_server_id" => 0, "tv_archive_duration" => 0, "delay_minutes" => 0, "external_push" => [], "probesize_ondemand" => 256000];
    }
    if (isset($_POST["days_to_restart"]) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]\$/", $_POST["time_to_restart"])) {
        $rTimeArray = ["days" => [], "at" => $_POST["time_to_restart"]];
        foreach ($_POST["days_to_restart"] as $rID => $rDay) {
            $rTimeArray["days"][] = $rDay;
        }
        $rArray["auto_restart"] = $rTimeArray;
    } else {
        $rArray["auto_restart"] = "";
    }
    $rOnDemandArray = [];
    if (isset($_POST["on_demand"])) {
        foreach ($_POST["on_demand"] as $rID) {
            $rOnDemandArray[] = $rID;
        }
    }
    if (isset($_POST["custom_map"])) {
        $rArray["custom_map"] = $_POST["custom_map"];
    }
    if (isset($_POST["custom_ffmpeg"])) {
        $rArray["custom_ffmpeg"] = $_POST["custom_ffmpeg"];
    }
    if (isset($_POST["custom_sid"])) {
        $rArray["custom_sid"] = $_POST["custom_sid"];
    }
    if (isset($_POST["gen_timestamps"])) {
        $rArray["gen_timestamps"] = 1;
        unset($_POST["gen_timestamps"]);
    } else {
        $rArray["gen_timestamps"] = 0;
    }
    if (isset($_POST["allow_record"])) {
        $rArray["allow_record"] = 1;
        unset($_POST["allow_record"]);
    } else {
        $rArray["allow_record"] = 0;
    }
    if (isset($_POST["rtmp_output"])) {
        $rArray["rtmp_output"] = 1;
        unset($_POST["rtmp_output"]);
    } else {
        $rArray["rtmp_output"] = 0;
    }
    if (isset($_POST["stream_all"])) {
        $rArray["stream_all"] = 1;
        unset($_POST["stream_all"]);
    } else {
        $rArray["stream_all"] = 0;
    }
    if (isset($_POST["direct_source"])) {
        $rArray["direct_source"] = 1;
        unset($_POST["direct_source"]);
    } else {
        $rArray["direct_source"] = 0;
    }
    if (isset($_POST["redirect_stream"])) {
        $rArray["redirect_stream"] = 1;
        unset($_POST["redirect_stream"]);
    } else {
        $rArray["redirect_stream"] = 0;
    }
    if (isset($_POST["read_native"])) {
        $rArray["read_native"] = 1;
        unset($_POST["read_native"]);
    } else {
        $rArray["read_native"] = 0;
    }
    if (isset($_POST["tv_archive_duration"])) {
        $rArray["tv_archive_duration"] = intval($_POST["tv_archive_duration"]);
        unset($_POST["tv_archive_duration"]);
    } else {
        $rArray["tv_archive_duration"] = 0;
    }
    if (isset($_POST["delay_minutes"])) {
        $rArray["delay_minutes"] = intval($_POST["delay_minutes"]);
        unset($_POST["delay_minutes"]);
    } else {
        $rArray["delay_minutes"] = 0;
    }
    if (isset($_POST["probesize_ondemand"])) {
        $rArray["probesize_ondemand"] = intval($_POST["probesize_ondemand"]);
        unset($_POST["probesize_ondemand"]);
    } else {
        $rArray["probesize_ondemand"] = 0;
    }
    if (empty($_POST["epg_lang"])) {
        $rArray["epg_lang"] = "NULL";
        unset($_POST["epg_lang"]);
    }
    if (isset($_POST["epg_id"])) {
        $rArray["epg_id"] = $_POST["epg_id"];
        unset($_POST["epg_id"]);
    }
    if (isset($_POST["channel_id"])) {
        $rArray["channel_id"] = $_POST["channel_id"];
        unset($_POST["channel_id"]);
    }
    if (isset($_POST["transcode_profile_id"])) {
        $rArray["transcode_profile_id"] = $_POST["transcode_profile_id"];
        if (0 < $rArray["transcode_profile_id"]) {
            $rArray["enable_transcode"] = 1;
        } else {
            $rArray["enable_transcode"] = 0;
        }
    }
    if (isset($_POST["restart_on_edit"])) {
        $rRestart = true;
        unset($_POST["restart_on_edit"]);
    } else {
        $rRestart = false;
    }
    $rBouquets = $_POST["bouquets"];
    unset($_POST["bouquets"]);
    foreach ($_POST as $rKey => $rValue) {
        if (isset($rArray[$rKey])) {
            $rArray[$rKey] = $rValue;
        }
    }
    $rImportStreams = [];
    if (isset($_FILES["m3u_file"])) {
        if (!hasPermissions("adv", "import_streams")) {
            exit;
        }
        $rStreamDatabase = [];
        $result = $db->query("SELECT `stream_source` FROM `streams` WHERE `type` IN (1,3);");
        if ($result && 0 < $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                foreach (json_decode($row["stream_source"], true) as $rSource) {
                    if (0 < strlen($rSource)) {
                        $rStreamDatabase[] = str_replace(" ", "%20", $rSource);
                    }
                }
            }
        }
        $rFile = "";
        if (!empty($_FILES["m3u_file"]["tmp_name"]) && strtolower(pathinfo($_FILES["m3u_file"]["name"], PATHINFO_EXTENSION)) == "m3u") {
            $rFile = file_get_contents($_FILES["m3u_file"]["tmp_name"]);
        }
        preg_match_all("/(?P<tag>#EXTINF:[-1,0])|(?:(?P<prop_key>[-a-z]+)=\\\"(?P<prop_val>[^\"]+)\")|(?<name>,[^\\r\\n]+)|(?<url>http[^\\s]+)/", $rFile, $rMatches);
        $rResults = [];
        $rIndex = -1;
        for ($i = 0; $i < count($rMatches[0]); $i++) {
            $rItem = $rMatches[0][$i];
            if (!empty($rMatches["tag"][$i])) {
                $rIndex++;
            } else {
                if (!empty($rMatches["prop_key"][$i])) {
                    $rResults[$rIndex][$rMatches["prop_key"][$i]] = trim($rMatches["prop_val"][$i]);
                } else {
                    if (!empty($rMatches["name"][$i])) {
                        $rResults[$rIndex]["name"] = trim(substr($rItem, 1));
                    } else {
                        if (!empty($rMatches["url"][$i])) {
                            $rResults[$rIndex]["url"] = str_replace(" ", "%20", trim($rItem));
                        }
                    }
                }
            }
        }
        foreach ($rResults as $rResult) {
            $rImportArray = ["stream_source" => [$rResult["url"]], "stream_icon" => $rResult["tvg-logo"] ?: "", "stream_display_name" => $rResult["name"] ?: "", "epg_id" => NULL, "epg_lang" => NULL, "channel_id" => NULL];
            if ($rResult["tvg-id"]) {
                $rEPG = findEPG($rResult["tvg-id"]);
                if (isset($rEPG)) {
                    $rImportArray["epg_id"] = $rEPG["epg_id"];
                    $rImportArray["channel_id"] = $rEPG["channel_id"];
                    if (!empty($rEPG["epg_lang"])) {
                        $rImportArray["epg_lang"] = $rEPG["epg_lang"];
                    }
                }
            }
            if (!in_array($rResult["url"], $rStreamDatabase)) {
                $rImportStreams[] = $rImportArray;
            }
        }
    } else {
        $rImportArray = ["stream_source" => [], "stream_icon" => $rArray["stream_icon"], "stream_display_name" => $rArray["stream_display_name"], "epg_id" => $rArray["epg_id"], "epg_lang" => $rArray["epg_lang"], "channel_id" => $rArray["channel_id"]];
        if (isset($_POST["stream_source"])) {
            foreach ($_POST["stream_source"] as $rID => $rURL) {
                if (0 < strlen($rURL)) {
                    $rImportArray["stream_source"][] = $rURL;
                }
            }
        }
        if (isset($_POST["edit"])) {
            $rImportStreams[] = $rImportArray;
        } else {
            $rResult = $db->query("SELECT COUNT(`id`) AS `count` FROM `streams` WHERE `stream_display_name` = '" . ESC($rImportArray["stream_display_name"]) . "' AND `type` IN (1,3);");
            if ($rResult->fetch_assoc()["count"] == 0) {
                $rImportStreams[] = $rImportArray;
            } else {
                $_STATUS = 2;
                $rStream = $rArray;
            }
        }
    }
    if (0 < count($rImportStreams)) {
        foreach ($rImportStreams as $rImportStream) {
            $rImportArray = $rArray;
            if ($rAdminSettings["download_images"]) {
                $rImportStream["stream_icon"] = downloadImage($rImportStream["stream_icon"]);
            }
            foreach (array_keys($rImportStream) as $rKey) {
                $rImportArray[$rKey] = $rImportStream[$rKey];
            }
            $rImportArray["order"] = getNextOrder();
            $rCols = "`" . ESC(implode("`,`", array_keys($rImportArray))) . "`";
            $rValues = NULL;
            foreach (array_values($rImportArray) as $rValue) {
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
                            if (in_array($rServerID, $rOnDemandArray)) {
                                $rOD = 1;
                            } else {
                                $rOD = 0;
                            }
                            if (isset($rStreamExists[$rServerID])) {
                                $db->query("UPDATE `streams_sys` SET `parent_id` = " . $rParent . ", `on_demand` = " . $rOD . " WHERE `server_stream_id` = " . $rStreamExists[$rServerID] . ";");
                            } else {
                                $db->query("INSERT INTO `streams_sys`(`stream_id`, `server_id`, `parent_id`, `on_demand`) VALUES(" . intval($rInsertID) . ", " . $rServerID . ", " . $rParent . ", " . $rOD . ");");
                            }
                        }
                    }
                    foreach ($rStreamExists as $rServerID => $rDBID) {
                        if (!in_array($rServerID, $rStreamsAdded)) {
                            $db->query("DELETE FROM `streams_sys` WHERE `server_stream_id` = " . $rDBID . ";");
                        }
                    }
                }
                $db->query("DELETE FROM `streams_options` WHERE `stream_id` = " . intval($rInsertID) . ";");
                if (isset($_POST["user_agent"]) && 0 < strlen($_POST["user_agent"])) {
                    $db->query("INSERT INTO `streams_options`(`stream_id`, `argument_id`, `value`) VALUES(" . intval($rInsertID) . ", 1, '" . ESC($_POST["user_agent"]) . "');");
                }
                if (isset($_POST["http_proxy"]) && 0 < strlen($_POST["http_proxy"])) {
                    $db->query("INSERT INTO `streams_options`(`stream_id`, `argument_id`, `value`) VALUES(" . intval($rInsertID) . ", 2, '" . ESC($_POST["http_proxy"]) . "');");
                }
                if (isset($_POST["cookie"]) && 0 < strlen($_POST["cookie"])) {
                    $db->query("INSERT INTO `streams_options`(`stream_id`, `argument_id`, `value`) VALUES(" . intval($rInsertID) . ", 17, '" . ESC($_POST["cookie"]) . "');");
                }
                if (isset($_POST["headers"]) && 0 < strlen($_POST["headers"])) {
                    $db->query("INSERT INTO `streams_options`(`stream_id`, `argument_id`, `value`) VALUES(" . intval($rInsertID) . ", 19, '" . ESC($_POST["headers"]) . "');");
                }
                if ($rRestart) {
                    APIRequest(["action" => "stream", "sub" => "start", "stream_ids" => [$rInsertID]]);
                }
                foreach ($rBouquets as $rBouquet) {
                    addToBouquet("stream", $rBouquet, $rInsertID);
                }
                if (!isset($_FILES["m3u_file"]) && isset($_POST["edit"])) {
                    foreach (getBouquets() as $rBouquet) {
                        if (!in_array($rBouquet["id"], $rBouquets)) {
                            removeFromBouquet("stream", $rBouquet["id"], $rInsertID);
                        }
                    }
                }
                $_STATUS = 0;
            } else {
                $_STATUS = 1;
                $rStream = $rArray;
            }
        }
        scanBouquets();
        if (isset($_FILES["m3u_file"])) {
            header("Location: ./streams.php?successedit");
            exit;
        }
        if (!isset($_GET["id"])) {
            header("Location: ./stream.php?successedit&id=" . $rInsertID);
            exit;
        }
    } else {
        if (!isset($_STATUS)) {
            $_STATUS = 3;
            $rStream = $rArray;
        }
    }
}
if (isset($_STATUS)) {
    foreach ($rStream as $rKey => $rValue) {
        if (is_array($rValue)) {
            $rStream[$rKey] = json_encode($rValue);
        }
    }
}
$rEPGSources = getEPGSources();
$rStreamArguments = getStreamArguments();
$rTranscodeProfiles = getTranscodeProfiles();
$rEPGJS = [[]];
foreach ($rEPGSources as $rEPG) {
    $rEPGJS[$rEPG["id"]] = json_decode($rEPG["data"], true);
}
$rServerTree = [];
$rOnDemand = [];
$rServerTree[] = ["id" => "source", "parent" => "#", "text" => "<strong>Stream Source</strong>", "icon" => "mdi mdi-youtube-tv", "state" => ["opened" => true]];
if (isset($_GET["id"])) {
    if (isset($_GET["import"]) || !hasPermissions("adv", "edit_stream")) {
        exit;
    }
    $rStream = getStream($_GET["id"]);
    if (!$rStream || $rStream["type"] != 1) {
        exit;
    }
    $rStreamOptions = getStreamOptions($_GET["id"]);
    $rStreamSys = getStreamSys($_GET["id"]);
    foreach ($rServers as $rServer) {
        if (isset($rStreamSys[intval($rServer["id"])])) {
            if ($rStreamSys[intval($rServer["id"])]["parent_id"] != 0) {
                $rParent = intval($rStreamSys[intval($rServer["id"])]["parent_id"]);
            } else {
                $rParent = "source";
            }
        } else {
            $rParent = "#";
        }
        $rServerTree[] = ["id" => $rServer["id"], "parent" => $rParent, "text" => $rServer["server_name"], "icon" => "mdi mdi-server-network", "state" => ["opened" => true]];
    }
    foreach ($rStreamSys as $rStreamItem) {
        if ($rStreamItem["on_demand"] == 1) {
            $rOnDemand[] = $rStreamItem["server_id"];
        }
    }
} else {
    if (!hasPermissions("adv", "add_stream")) {
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
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"./streams.php";
if (isset($_GET["category"])) {
    echo "?category=" . $_GET["category"];
}
echo "\">\n                                            <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\">\n                                                ";
echo $_["permission_streams"];
echo " \n                                            </button>\n                                        </a>\n                                        ";
if (!isset($rStream)) {
    if (!isset($_GET["import"])) {
        echo "                                        <a href=\"./stream.php?import\">\n                                            <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm\">\n                                                ";
        echo $_["import_m3u"];
        echo " \n                                            </button>\n                                        </a>\n                                        ";
    } else {
        echo "                                        <a href=\"./stream.php\">\n                                            <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm\">\n                                                ";
        echo $_["add_single"];
        echo " \n                                            </button>\n                                        </a>\n                                        ";
    }
}
echo "                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rStream["id"])) {
    echo $rStream["stream_display_name"] . " &nbsp;<button type=\"button\" class=\"btn btn-outline-info waves-effect waves-light btn-xs\" onClick=\"player(" . $rStream["id"] . ");\"><i class=\"mdi mdi-play\"></i></button>";
} else {
    if (isset($_GET["import"])) {
        echo $_["import_streams"];
    } else {
        echo $_["add_stream"];
    }
}
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["stream_operation_was_completed_successfully"];
        echo " \n                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["stream_operation_was_completed_successfully"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["an_error_occured_while"];
            echo " \n                        </div>\n\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["an_error_occured_while"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
            if (!$rSettings["sucessedit"]) {
                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                echo $_["the_stream_name_is_already_in_use"];
                echo " \n                        </div>\n\t\t\t\t\t\t";
            } else {
                echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                echo $_["the_stream_name_is_already_in_use"];
                echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
            }
        } else {
            if (isset($_STATUS) && $_STATUS == 3) {
                if (!$rSettings["sucessedit"]) {
                    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                    echo $_["no_new_streams_were_imported"];
                    echo " \n                        </div>\n                        ";
                } else {
                    echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                    echo $_["no_new_streams_were_imported"];
                    echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
                }
            }
        }
    }
}
if (isset($rStream["id"])) {
    echo "                        <div class=\"card text-xs-center\">\n                            <div class=\"table\">\n                                <table id=\"datatable\" class=\"table table-borderless mb-0\">\n                                    <thead class=\"bg-light\">\n                                        <tr>\n                                            <th>";
    echo $_["id"];
    echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t<th></th>\n                                            <th></th>\n                                            <th>";
    echo $_["source"];
    echo "</th>\n                                            <th>";
    echo $_["clients"];
    echo "</th>\n                                            <th>";
    echo $_["uptime"];
    echo "</th>\n                                            <th>";
    echo $_["actions"];
    echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t<th></th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        <tr>\n                                            <td colspan=\"9\" class=\"text-center\">";
    echo $_["loading_stream_information"];
    echo " </td>\n                                        </tr>\n                                    </tbody>\n                                </table>\n                            </div>\n                        </div>\n                        ";
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form";
if (isset($_GET["import"])) {
    echo " enctype=\"multipart/form-data\"";
}
echo " action=\"./stream.php";
if (isset($_GET["import"])) {
    echo "?import";
} else {
    if (isset($_GET["id"])) {
        echo "?id=" . $_GET["id"];
    }
}
echo "\" method=\"POST\" id=\"stream_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rStream["id"])) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rStream["id"];
    echo "\" />\n                                    ";
}
echo "                                    <input type=\"hidden\" name=\"server_tree_data\" id=\"server_tree_data\" value=\"\" />\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#stream-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo " </span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#advanced-options\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-folder-alert-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["advanced"];
echo " </span>\n                                                </a>\n                                            </li>\n\t\t\t\t\t\t\t\t\t\t\t";
if (!isset($_GET["import"])) {
    echo "                                            <li class=\"nav-item\">\n                                                <a href=\"#stream-map\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-map mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["map"];
    echo " </span>\n                                                </a>\n                                            </li>\n\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                            <li class=\"nav-item\">\n                                                <a href=\"#auto-restart\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-clock-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["auto_restart"];
echo " </span>\n                                                </a>\n                                            </li>\n                                            ";
if (!isset($_GET["import"])) {
    echo "                                            <li class=\"nav-item\">\n                                                <a href=\"#epg-options\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-television-guide mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["epg"];
    echo " </span>\n                                                </a>\n                                            </li>\n                                            ";
}
echo "                                            <li class=\"nav-item\">\n                                                <a href=\"#load-balancing\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-server-network mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["servers"];
echo " </span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"stream-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        ";
if (!isset($_GET["import"])) {
    echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_display_name\">";
    echo $_["stream_name"];
    echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"stream_display_name\" name=\"stream_display_name\" value=\"";
    if (isset($rStream)) {
        echo htmlspecialchars($rStream["stream_display_name"]);
    }
    echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <span class=\"streams\">\n                                                            ";
    if (isset($rStream)) {
        $rStreamSources = json_decode($rStream["stream_source"], true);
        if (!$rStreamSources) {
            $rStreamSources = [""];
        }
    } else {
        $rStreamSources = [""];
    }
    $i = 0;
    foreach ($rStreamSources as $rStreamSource) {
        $i++;
        echo "                                                            <div class=\"form-group row mb-4 stream-url\">\n                                                                <label class=\"col-md-4 col-form-label\" for=\"stream_source\"> ";
        echo $_["stream_url"];
        echo " </label>\n                                                                <div class=\"col-md-8 input-group\">\n                                                                    <input type=\"text\" id=\"stream_source\" name=\"stream_source[]\" class=\"form-control\" value=\"";
        echo htmlspecialchars($rStreamSource);
        echo "\">\n                                                                    <div class=\"input-group-append\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <button class=\"checkSource btn btn-primary waves-effect waves-light\" type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Check Source\"><i class=\"mdi mdi-check\"></i></button>\n                                                                        <button class=\"btn btn-info waves-effect waves-light\" onClick=\"moveUp(this);\" type=\"button\"><i class=\"mdi mdi-chevron-up\"></i></button>\n                                                                        <button class=\"btn btn-info waves-effect waves-light\" onClick=\"moveDown(this);\" type=\"button\"><i class=\"mdi mdi-chevron-down\"></i></button>\n                                                                        <button class=\"btn btn-primary waves-effect waves-light\" onClick=\"addStream();\" type=\"button\"><i class=\"mdi mdi-plus\"></i></button>\n                                                                        <button class=\"btn btn-danger waves-effect waves-light\" onClick=\"removeStream(this);\" type=\"button\"><i class=\"mdi mdi-close\"></i></button>\n                                                                    </div>\n                                                                </div>\n                                                            </div>\n                                                            ";
    }
    echo "                                                        </span>\n                                                        ";
} else {
    echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"m3u_file\">";
    echo $_["m3u"];
    echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"file\" id=\"m3u_file\" name=\"m3u_file\" />\n                                                            </div>\n                                                        </div>\n                                                        ";
}
echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_id\">";
echo $_["category_name"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"category_id\" id=\"category_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach ($rCategories as $rCategory) {
    echo "                                                                    <option ";
    if (isset($rStream)) {
        if (intval($rStream["category_id"]) == intval($rCategory["id"])) {
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
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"bouquets\">";
echo $_["add_to_bouquets"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"bouquets[]\" id=\"bouquets\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "\">\n                                                                    ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                                    <option ";
    if (isset($rStream) && in_array($rStream["id"], json_decode($rBouquet["bouquet_channels"], true))) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rBouquet["id"];
    echo "\">";
    echo $rBouquet["bouquet_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        ";
if (!isset($_GET["import"])) {
    echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_icon\">";
    echo $_["stream_logo_url"];
    echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"stream_icon\" name=\"stream_icon\" value=\"";
    if (isset($rStream)) {
        echo htmlspecialchars($rStream["stream_icon"]);
    }
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        ";
}
echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"notes\">";
echo $_["notes"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <textarea id=\"notes\" name=\"notes\" class=\"form-control\" rows=\"3\" placeholder=\"\">";
if (isset($rStream)) {
    echo htmlspecialchars($rStream["notes"]);
}
echo "</textarea>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo " </a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"advanced-options\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"gen_timestamps\">";
echo $_["generate_pts"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Allow FFmpeg to generate presentation timestamps for you to achieve better synchronization with the stream codecs. In some streams this can cause de-sync.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"gen_timestamps\" id=\"gen_timestamps\" type=\"checkbox\" ";
if (isset($rStream)) {
    if ($rStream["gen_timestamps"] == 1) {
        echo "checked ";
    }
} else {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"read_native\">";
echo $_["native_frames"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"You should always read live streams as non-native frames. However if you are streaming static video files, set this to true otherwise the encoding process will fail.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"read_native\" id=\"read_native\" type=\"checkbox\" ";
if (isset($rStream) && $rStream["read_native"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_all\">";
echo $_["stream_all_codecs"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"This option will stream all codecs from your stream. Some streams have more than one audio/video/subtitles channels.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"stream_all\" id=\"stream_all\" type=\"checkbox\" ";
if (isset($rStream) && $rStream["stream_all"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allow_record\">";
echo $_["allow_recording"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"allow_record\" id=\"allow_record\" type=\"checkbox\" ";
if (isset($rStream)) {
    if ($rStream["allow_record"] == 1) {
        echo "checked ";
    }
} else {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"rtmp_output\">";
echo $_["allow_rtmp_output"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable RTMP output for this channel.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"rtmp_output\" id=\"rtmp_output\" type=\"checkbox\" ";
if (isset($rStream) && $rStream["rtmp_output"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"direct_source\">";
echo $_["direct_source"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Don't run source through Xtream Codes, just redirect instead.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"direct_source\" id=\"direct_source\" type=\"checkbox\" ";
if (isset($rStream) && $rStream["direct_source"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                        <div class=\"form-group row mb-4\" style=\"display: none;\" id=\"redirect_stream_div\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"redirect_stream\">Redirect Stream <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"If deactivated it returns original URL in the playlist.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"redirect_stream\" style=\"display: none;\" id=\"redirect_stream\" type=\"checkbox\" ";
if (isset($rStream)) {
    if ($rStream["redirect_stream"] == 1) {
        echo "checked ";
    }
} else {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\t\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"custom_sid\">";
echo $_["custom_channel_sid"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Here you can specify the SID of the channel in order to work with the epg on the enigma2 devices. You have to specify the code with the ':' but without the first number, 1 or 4097 . Example: if we have this code:  '1:0:1:13f:157c:13e:820000:0:0:0:2097' then you have to add on this field:  ':0:1:13f:157c:13e:820000:0:0:0:\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"custom_sid\" name=\"custom_sid\" value=\"";
if (isset($rStream)) {
    echo htmlspecialchars($rStream["custom_sid"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"delay_minutes\">";
echo $_["minute_delay"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delay stream by X minutes. Will not work with on demand streams.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"delay_minutes\" name=\"delay_minutes\" value=\"";
if (isset($rStream)) {
    echo $rStream["delay_minutes"];
} else {
    echo "0";
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"custom_ffmpeg\">";
echo $_["custom_ffmpeg_command"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"In this field you can write your own custom FFmpeg command. Please note that this command will be placed after the input and before the output. If the command you will specify here is about to do changes in the output video or audio, it may require to transcode the stream. In this case, you have to use and change at least the Video/Audio Codecs using the transcoding attributes below. The custom FFmpeg command will only be used by the server(s) that take the stream from the Source.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"custom_ffmpeg\" name=\"custom_ffmpeg\" value=\"";
if (isset($rStream)) {
    echo htmlspecialchars($rStream["custom_ffmpeg"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"probesize_ondemand\">";
echo $_["on_demand_probesize"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Adjustable probesize for ondemand streams. Adjust this setting if you experience issues with no audio.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"probesize_ondemand\" name=\"probesize_ondemand\" value=\"";
if (isset($rStream)) {
    echo $rStream["probesize_ondemand"];
} else {
    echo "128000";
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"user_agent\">";
echo $_["user_agent"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"user_agent\" name=\"user_agent\" value=\"";
if (isset($rStreamOptions[1])) {
    echo htmlspecialchars($rStreamOptions[1]["value"]);
} else {
    echo htmlspecialchars($rStreamArguments["user_agent"]["argument_default_value"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"http_proxy\">";
echo $_["http_proxy"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Format: ip:port\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"http_proxy\" name=\"http_proxy\" value=\"";
if (isset($rStreamOptions[2])) {
    echo htmlspecialchars($rStreamOptions[2]["value"]);
} else {
    echo htmlspecialchars($rStreamArguments["proxy"]["argument_default_value"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"cookie\">";
echo $_["cookie"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Format: key=value;\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"cookie\" name=\"cookie\" value=\"";
if (isset($rStreamOptions[17])) {
    echo htmlspecialchars($rStreamOptions[17]["value"]);
} else {
    echo htmlspecialchars($rStreamArguments["cookie"]["argument_default_value"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"headers\">";
echo $_["headers"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"FFmpeg -headers command.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"headers\" name=\"headers\" value=\"";
if (isset($rStreamOptions[19])) {
    echo htmlspecialchars($rStreamOptions[19]["value"]);
} else {
    echo htmlspecialchars($rStreamArguments["headers"]["argument_default_value"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"transcode_profile_id\">";
echo $_["transcoding_profile"];
echo "  <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["episode_tooltip_7"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"transcode_profile_id\" id=\"transcode_profile_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option ";
if (isset($rStream) && intval($rStream["transcode_profile_id"]) == 0) {
    echo "selected ";
}
echo "value=\"0\">";
echo $_["transcoding_disabled"];
echo "</option>\n                                                                    ";
foreach ($rTranscodeProfiles as $rProfile) {
    echo "                                                                    <option ";
    if (isset($rStream) && intval($rStream["transcode_profile_id"]) == intval($rProfile["profile_id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rProfile["profile_id"];
    echo "\">";
    echo $rProfile["profile_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo " </a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo " </a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
if (!isset($_GET["import"])) {
    echo "                                            <div class=\"tab-pane\" id=\"stream-map\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"custom_map\">";
    echo $_["custom_map"];
    echo " </label>\n                                                            <div class=\"col-md-9 input-group\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"custom_map\" name=\"custom_map\" value=\"";
    if (isset($rStream)) {
        echo htmlspecialchars($rStream["custom_map"]);
    }
    echo "\">\n                                                                <div class=\"input-group-append\">\n                                                                        <button class=\"btn btn-primary waves-effect waves-light\" id=\"load_maps\" type=\"button\"><i class=\"mdi mdi-magnify\"></i></button>\n                                                                    </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"alert alert-warning bg-warning text-white border-0\" role=\"alert\">\n                                                            ";
    echo $_["custom_maps_are_advanced"];
    echo " \n                                                        </div>\n                                                        <table id=\"datatable-map\" class=\"table table-borderless mb-0\">\n                                                            <thead class=\"bg-light\">\n                                                                <tr>\n                                                                    <th>#</th>\n                                                                    <th>";
    echo $_["type"];
    echo " </th>\n                                                                    <th>";
    echo $_["information"];
    echo " </th>\n                                                                </tr>\n                                                            </thead>\n                                                            <tbody></tbody>\n                                                        </table>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["prev"];
    echo " </a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["next"];
    echo " </a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                            <div class=\"tab-pane\" id=\"auto-restart\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"days_to_restart\">";
echo $_["days_to_restart"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                ";
$rAutoRestart = ["days" => [], "at" => "06:00"];
if (isset($rStream) && strlen($rStream["auto_restart"])) {
    $rAutoRestart = json_decode($rStream["auto_restart"], true);
    if (!isset($rAutoRestart["days"])) {
        $rAutoRestart["days"] = [];
    }
    if (!isset($rAutoRestart["at"])) {
        $rAutoRestart["at"] = "06:00";
    }
}
echo "                                                                <select id=\"days_to_restart\" name=\"days_to_restart[]\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose_"];
echo "\">\n                                                                    ";
foreach (["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"] as $rDay) {
    echo "                                                                    <option value=\"";
    echo $rDay;
    echo "\"";
    if (in_array($rDay, $rAutoRestart["days"])) {
        echo " selected";
    }
    echo ">";
    echo $rDay;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"time_to_restart\">";
echo $_["time_to_restart"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <div class=\"input-group clockpicker\" data-placement=\"top\" data-align=\"top\" data-autoclose=\"true\">\n                                                                    <input id=\"time_to_restart\" name=\"time_to_restart\" type=\"text\" class=\"form-control\" value=\"";
echo $rAutoRestart["at"];
echo "\">\n                                                                    <div class=\"input-group-append\">\n                                                                        <span class=\"input-group-text\"><i class=\"mdi mdi-clock-outline\"></i></span>\n                                                                    </div>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo " </a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo " </a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            ";
if (!isset($_GET["import"])) {
    echo "                                            <div class=\"tab-pane\" id=\"epg-options\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"epg_id\">";
    echo $_["epg_source"];
    echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"epg_id\" id=\"epg_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option ";
    if (isset($rStream) && intval($rStream["epg_id"]) == 0) {
        echo "selected ";
    }
    echo "value=\"0\">";
    echo $_["no_epg"];
    echo " </option>\n                                                                    ";
    foreach ($rEPGSources as $rEPG) {
        echo "                                                                    <option ";
        if (isset($rStream) && intval($rStream["epg_id"]) == $rEPG["id"]) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rEPG["id"];
        echo "\">";
        echo $rEPG["epg_name"];
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"channel_id\">";
    echo $_["epg_channel_id"];
    echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"channel_id\" id=\"channel_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                ";
    if (isset($rStream)) {
        foreach ((array) json_decode($rEPGSources[intval($rStream["epg_id"])]["data"], true) as $rKey => $rEPGChannel) {
            echo "                                                                    <option value=\"";
            echo $rKey;
            echo "\"";
            if ($rStream["channel_id"] == $rKey) {
                echo " selected";
            }
            echo ">";
            echo $rEPGChannel["display_name"];
            echo "</option>\n                                                                    ";
        }
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"epg_lang\">";
    echo $_["epg_language"];
    echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"epg_lang\" id=\"epg_lang\" class=\"form-control\" data-toggle=\"select2\">\n                                                                ";
    if (isset($rStream)) {
        foreach ((array) json_decode($rEPGSources[intval($rStream["epg_id"])]["data"], true)[$rStream["channel_id"]]["langs"] as $rID => $rLang) {
            echo "                                                                    <option value=\"";
            echo $rLang;
            echo "\"";
            if ($rStream["epg_lang"] == $rLang) {
                echo " selected";
            }
            echo ">";
            echo $rLang;
            echo "</option>\n                                                                    ";
        }
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["prev"];
    echo " </a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["next"];
    echo " </a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            ";
}
echo "                                            <div class=\"tab-pane\" id=\"load-balancing\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"servers\">";
echo $_["server_tree"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <div id=\"server_tree\"></div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"on_demand\">";
echo $_["on_demand"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"on_demand\" name=\"on_demand[]\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["bouquet_order"];
echo "Choose ...\">\n                                                                    ";
foreach ($rServers as $rServerItem) {
    echo "                                                                        <option value=\"";
    echo $rServerItem["id"];
    echo "\"";
    if (in_array($rServerItem["id"], $rOnDemand)) {
        echo " selected";
    }
    echo ">";
    echo $rServerItem["server_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tv_archive_server_id\">";
echo $_["timeshift_server"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"tv_archive_server_id\" id=\"tv_archive_server_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option value=\"0\">";
echo $_["timeshift_disabled"];
echo " </option>\n                                                                    ";
foreach ($rServers as $rServer) {
    echo "                                                                    <option value=\"";
    echo $rServer["id"];
    echo "\"";
    if (isset($rStream) && $rStream["tv_archive_server_id"] == $rServer["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rServer["server_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tv_archive_duration\">";
echo $_["timeshift_days"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"tv_archive_duration\" name=\"tv_archive_duration\" value=\"";
if (isset($rStream)) {
    echo $rStream["tv_archive_duration"];
} else {
    echo "0";
}
echo "\">\n                                                                </select>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"restart_on_edit\">";
if (isset($rStream["id"])) {
    echo $_["restart_on_edit"];
    echo " ";
} else {
    echo $_["start_stream_now"];
    echo " ";
}
echo "</label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input name=\"restart_on_edit\" id=\"restart_on_edit\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo " </a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_stream\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rStream["id"])) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- file preview template -->\n        <div class=\"d-none\" id=\"uploadPreviewTemplate\">\n            <div class=\"card mt-1 mb-0 shadow-none border\">\n                <div class=\"p-2\">\n                    <div class=\"row align-items-center\">\n                        <div class=\"col-auto\">\n                            <img data-dz-thumbnail class=\"avatar-sm rounded bg-light\" alt=\"\">\n                        </div>\n                        <div class=\"col pl-0\">\n                            <a href=\"javascript:void(0);\" class=\"text-muted font-weight-bold\" data-dz-name></a>\n                            <p class=\"mb-0\" data-dz-size></p>\n                        </div>\n                        <div class=\"col-auto\">\n                            <!-- Button -->\n                            <a href=\"\" class=\"btn btn-link btn-lg text-muted\" data-dz-remove>\n                                <i class=\"mdi mdi-close-circle\"></i>\n                            </a>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n\n        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\t\t<script src=\"assets/js/jquery-ui.js\"></script>\n        \n        <script>\n        var rEPG = ";
echo json_encode($rEPGJS);
echo ";\n        var rSwitches = [];\n        \n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        function moveUp(elem) {\n            if (\$(elem).parent().parent().parent().prevAll().length > 0) {\n                \$(elem).parent().parent().parent().insertBefore(\$('.streams>div').eq(\$(elem).parent().parent().parent().prevAll().length-1));\n            }\n        }\n        function moveDown(elem) {\n            if (\$(elem).parent().parent().parent().prevAll().length < \$(\".streams>div\").length) {\n                \$(elem).parent().parent().parent().insertAfter(\$('.streams>div').eq(\$(elem).parent().parent().parent().prevAll().length+1));\n            }\n        }\n        function addStream() {\n            \$(\".stream-url:first\").clone().appendTo(\".streams\");\n            \$(\".stream-url:last label\").html(\"Stream URL\");\n            \$(\".stream-url:last input\").val(\"\");\n        }\n        function removeStream(rField) {\n            if (\$('.stream-url').length > 1) {\n                \$(rField).parent().parent().parent().remove();\n            } else {\n                \$(rField).parent().parent().find(\"#stream_source\").val(\"\");\n            }\n        }\n        function selectEPGSource() {\n            \$(\"#channel_id\").empty();\n            \$(\"#epg_lang\").empty();\n            if (rEPG[\$(\"#epg_id\").val()]) {\n                \$.each(rEPG[\$(\"#epg_id\").val()], function(key, data) {\n                    \$(\"#channel_id\").append(new Option(data[\"display_name\"], key, false, false));\n                });\n                selectEPGID();\n            }\n        }\n        function selectEPGID() {\n            \$(\"#epg_lang\").empty();\n            if (rEPG[\$(\"#epg_id\").val()][\$(\"#channel_id\").val()]) {\n                \$.each(rEPG[\$(\"#epg_id\").val()][\$(\"#channel_id\").val()][\"langs\"], function(i, data) {\n                    \$(\"#epg_lang\").append(new Option(data, data, false, false));\n                });\n            }\n        }\n        function reloadStream() {\n            \$(\"#datatable\").DataTable().ajax.reload( null, false );\n            setTimeout(reloadStream, 5000);\n        }\n        function api(rID, rServerID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["are_you_sure_you_want_to_delete_this_stream"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=stream&sub=\" + rType + \"&stream_id=\" + rID + \"&server_id=\" + rServerID, function(data) {\n                if (data.result == true) {\n                    if (rType == \"start\") {\n                        \$.toast(\"";
echo $_["stream_successfully_started"];
echo "\");\n                    } else if (rType == \"stop\") {\n                        \$.toast(\"";
echo $_["stream_successfully_stopped"];
echo "\");\n                    } else if (rType == \"restart\") {\n                        \$.toast(\"";
echo $_["stream_successfully_restarted"];
echo "\");\n                    } else if (rType == \"delete\") {\n                        \$(\"#stream-\" + rID + \"-\" + rServerID).remove();\n                        \$.toast(\"";
echo $_["stream_successfully_deleted"];
echo "\");\n                    }\n                    \$(\"#datatable\").DataTable().ajax.reload( null, false );\n                } else {\n                    \$.toast(\"";
echo $_["an_error_occured_while_processing_your_request"];
echo "\");\n                }\n            }).fail(function() {\n                \$.toast(\"";
echo $_["an_error_occured_while_processing_your_request"];
echo "\");\n            });\n        }\n        function player(rID) {\n            \$.magnificPopup.open({\n                items: {\n                    src: \"./player.php?type=live&id=\" + rID,\n                    type: 'iframe'\n                }\n            });\n        }\n        function setSwitch(switchElement, checkedBool) {\n            if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {\n                switchElement.setPosition(true);\n                switchElement.handleOnchange(true);\n            }\n        }\n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'});\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n              var switchery = new Switchery(html);\n              window.rSwitches[\$(html).attr(\"id\")] = switchery;\n            });\n            \$(\"#epg_id\").on(\"select2:select\", function(e) { \n                selectEPGSource();\n            });\n            \$(\"#channel_id\").on(\"select2:select\", function(e) { \n                selectEPGID();\n            });\n            \n            \$(\".clockpicker\").clockpicker();\n            \n            \$('#server_tree').jstree({ 'core' : {\n                'check_callback': function (op, node, parent, position, more) {\n                    switch (op) {\n                        case 'move_node':\n                            if (node.id == \"source\") { return false; }\n                            return true;\n                    }\n                },\n                'data' : ";
echo json_encode($rServerTree);
echo "            }, \"plugins\" : [ \"dnd\" ]\n            });\n            \n            \$(\"#direct_source\").change(function() {\n                evaluateDirectSource();\n            });\n            function evaluateDirectSource() {\n                \$([\"read_native\", \"gen_timestamps\", \"stream_all\", \"allow_record\", \"rtmp_output\", \"delay_minutes\", \"custom_ffmpeg\", \"probesize_ondemand\", \"user_agent\", \"http_proxy\", \"cookie\", \"headers\", \"transcode_profile_id\", \"custom_map\", \"days_to_restart\", \"time_to_restart\", \"epg_id\", \"epg_lang\", \"channel_id\", \"on_demand\", \"tv_archive_duration\", \"tv_archive_server_id\", \"restart_on_edit\"]).each(function(rID, rElement) {\n                    if (\$(rElement)) {\n                        if (\$(\"#direct_source\").is(\":checked\")) {\n\t\t\t\t\t\t\t\$(\"#redirect_stream_div\").show();\n                            if (window.rSwitches[rElement]) {\n                                setSwitch(window.rSwitches[rElement], false);\n                                window.rSwitches[rElement].disable();\n                            } else {\n                                \$(\"#\" + rElement).prop(\"disabled\", true);\n                            }\n                        } else {\n\t\t\t\t\t\t\t\$(\"#redirect_stream_div\").hide();\n                            if (window.rSwitches[rElement]) {\n                                window.rSwitches[rElement].enable();\n                            } else {\n                                \$(\"#\" + rElement).prop(\"disabled\", false);\n                            }\n                        }\n                    }\n                });\n            }\n            \$(\"#load_maps\").click(function() {\n                rURL = \$(\"#stream_source:eq(0)\").val();\n                if (rURL.length > 0) {\n                    \$.toast(\"";
echo $_["stream_map_has_started"];
echo "\");\n                    \$(\"#datatable-map\").DataTable().clear().draw();\n                    \$.getJSON(\"./api.php?action=map_stream&stream=\" + encodeURIComponent(rURL), function(data) {\n                        \$(data.streams).each(function(id, array) {\n                            if (array.codec_type == \"video\") {\n                                rString = array.codec_name.toUpperCase();\n                                if (array.profile) {\n                                    rString += \" (\" + array.profile + \")\";\n                                }\n                                if (array.pix_fmt) {\n                                    rString += \" - \" + array.pix_fmt;\n                                }\n                                if ((array.width) && (array.height)) {\n                                    rString += \" - \" + array.width + \"x\" + array.height;\n                                }\n                                if ((array.avg_frame_rate) && (array.avg_frame_rate.split(\"/\")[0] > 0)) {\n                                    rString += \" - \" + array.avg_frame_rate.split(\"/\")[0] + \" fps\";\n                                }\n                                \$(\"#datatable-map\").DataTable().row.add([array.index, \"Video\", rString]);\n                            } else if (array.codec_type == \"audio\") {\n                                rString = array.codec_name.toUpperCase();\n                                if ((array.sample_rate) && (array.sample_rate > 0)) {\n                                    rString += \" - \" + array.sample_rate + \" Hz\";\n                                }\n                                if (array.channel_layout) {\n                                    rString += \" - \" + array.channel_layout;\n                                }\n                                if (array.sample_fmt) {\n                                    rString += \" - \" + array.sample_fmt;\n                                }\n                                if (array.bit_rate) {\n                                    rString += \" - \" + Math.ceil(array.bit_rate / 1000) + \" kb/s\";\n                                }\n                                if (array.disposition.visual_impaired) {\n                                    rString += \" - Visual Impaired\";\n                                }\n                                if (array.disposition.hearing_impaired) {\n                                    rString += \" - Hearing Impaired\";\n                                }\n                                if (array.disposition.dub) {\n                                    rString += \" - Dub\";\n                                }\n                                \$(\"#datatable-map\").DataTable().row.add([array.index, \"Audio\", rString]);\n                            } else if ((array.codec_type == \"audio\") && (array.tags.language)) {\n                                rString = array.codec_name.toUpperCase();\n                                if (array.tags.language) {\n                                    rString += \" - \" + array.tags.language.toUpperCase();\n                                }\n                                if ((array.sample_rate) && (array.sample_rate > 0)) {\n                                    rString += \" - \" + array.sample_rate + \" Hz\";\n                                }\n                                if (array.channel_layout) {\n                                    rString += \" - \" + array.channel_layout;\n                                }\n                                if (array.sample_fmt) {\n                                    rString += \" - \" + array.sample_fmt;\n                                }\n                                if ((array.bit_rate) || (array.tags.variant_bitrate)) {\n                                    if (array.bit_rate) {\n                                        rString += \" - \" + Math.ceil(array.bit_rate / 1000) + \" kb/s\";\n                                    } else {\n                                        rString += \" - \" + Math.ceil(array.tags.variant_bitrate / 1000) + \" vbr\";\n                                    }\n                                }\n                                if (array.disposition.visual_impaired) {\n                                    rString += \" - Visual Impaired\";\n                                }\n                                if (array.disposition.hearing_impaired) {\n                                    rString += \" - Hearing Impaired\";\n                                }\n                                if (array.disposition.dub) {\n                                    rString += \" - Dub\";\n                                }\n                                \$(\"#datatable-map\").DataTable().row.add([array.index, \"Audio\", rString]);\n                            } else if (array.codec_type == \"subtitle\") {\n                                rString = array.codec_long_name.toUpperCase();\n                                if (array.tags.language) {\n                                    rString += \" - \" + array.tags.language.toUpperCase();\n                                }\n                                \$(\"#datatable-map\").DataTable().row.add([array.index, \"Subtitle\", rString]);\n                            } else {\n                                rString = array.codec_long_name.toUpperCase();\n                                if (array.tags.variant_bitrate) {\n                                    rString += \" - \" + Math.ceil(array.tags.variant_bitrate / 1000) + \" vbr\";\n                                }\n                                \$(\"#datatable-map\").DataTable().row.add([array.index, \"Data\", rString]);\n                            }\n                        });\n                        \$(\"#datatable-map\").DataTable().draw();\n                        if (data.streams.length > 0) {\n                            \$.toast(\"";
echo $_["stream_map_complete"];
echo "\");\n                        } else {\n                            \$.toast(\"";
echo $_["stream_mapping"];
echo "\");\n                        }\n                    }).fail(function() {\n                        \$.toast(\"";
echo $_["an_error_occured_while_mapping_streams"];
echo "\");\n                    });\n                }\n            });\n            \n            \$(\"#stream_form\").submit(function(e){\n                ";
if (!isset($_GET["import"])) {
    echo "                if (\$(\"#stream_display_name\").val().length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"Enter a stream name.\");\n                }\n                ";
} else {
    echo "                if (\$(\"#m3u_file\").val().length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
    echo $_["please_select_a_m3u"];
    echo "\");\n                }\n                ";
}
echo "                \$(\"#server_tree_data\").val(JSON.stringify(\$('#server_tree').jstree(true).get_json('#', {flat:true})));\n            });\n            \n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"#probesize_ondemand\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#delay_minutes\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#tv_archive_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n            ";
if (isset($rStream["id"])) {
    echo "            \$(\"#datatable\").DataTable({\n                ordering: false,\n                paging: false,\n                searching: false,\n                processing: true,\n                serverSide: true,\n                bInfo: false,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"streams\";\n                        d.stream_id = ";
    echo $rStream["id"];
    echo ";\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,3,4,5,6]},\n                    {\"visible\": false, \"targets\": [1,2,7]}\n                ],\n            });\n            setTimeout(reloadStream, 5000);\n            ";
}
echo "            \$(\"#datatable-map\").DataTable({\n                paging: false,\n                searching: false,\n                bInfo: false,\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2]},\n                ],\n                select: {\n                    style: 'multi'\n                }\n            }).on('select', function (e, dt, type, indexes) {\n                var i; var rMap = \"\";\n                for (i = 0; i < \$(\"#datatable-map\").DataTable().rows('.selected').data().length; i++) {\n                    rMap += \"-map 0:\" + \$(\"#datatable-map\").DataTable().rows('.selected').data()[i][0] + \" \";\n                }\n                \$(\"#custom_map\").val(rMap.trim());\n            });\n            evaluateDirectSource();\n        });\n        </script>\n\t\t<script>\n\t\t\$( document ).ready(function() {\t\n\t\t\t\$('body').on('focus', '.stream_source',function(){\n\t\t\t\t \$(this).autocomplete({\n\t\t\t\t\t//var target = document.location.href.match(/^([^#]+)/)[1];\n\t\t\t\t\tsource: function (request, response) {\n\t\t\t\t\t\t// Fetch data\n\t\t\t\t\t\t\$.ajax({\n\t\t\t\t\t\t\turl: \"stream.php\",\n\t\t\t\t\t\t\ttype: 'post',\n\t\t\t\t\t\t\tdataType: \"json\",\n\t\t\t\t\t\t\tdata: {\n\t\t\t\t\t\t\t\tsearch: request.term\n\t\t\t\t\t\t\t},\n\t\t\t\t\t\t\tsuccess: function (data) {\n\t\t\t\t\t\t\t\tresponse(data);\n\t\t\t\t\t\t\t}\n\t\t\t\t\t\t});\n\t\t\t\t\t},\n\t\t\t\t\tselect: function (event, ui) {\n\t\t\t\t\t\t// Set selection\n\t\t\t\t\t\t\$(this).val(ui.item.value); // save selected id to input\t\t\n\t\t\t\t\t\t\$(this).closest(\"div.input-group\").find(\".checkSource\").trigger('click');\n\t\t\t\t\t\treturn false;\t\t\t\t\t\n\t\t\t\t\t}\n\t\t\t\t});\n\t\t\t});\n\t\t\t\n\t\t\t\n\t\t\t\$('body').on('click', '.checkSource',function(){\n\t\t\t\tvar link = \$(this).closest(\"div.input-group\").find(\"#stream_source\").val();\t\t\t\n\t\t\t\tvar target = document.location.href.match(/^([^#]+)/)[1];\n\t\t\t\tvar myToast = \$.toast({\n\t\t\t\t\theading: 'Information',\n\t\t\t\t\ttext: 'Please wait, processing ...',\n\t\t\t\t\ticon: 'info',\n\t\t\t\t\thideAfter: false\n\t\t\t\t});\n\t\t\t\t\n\t\t\t\tvar data = {\n\t\t\t\t\tlink: link\t\t\t\t\t\n\t\t\t\t};\n\t\t\t\t\n\t\t\t\t\$.ajax({\n\t\t\t\t\turl: target,\n\t\t\t\t\tdataType: 'json',\n\t\t\t\t\ttype: 'POST',\n\t\t\t\t\tdata: data,\n\t\t\t\t\tsuccess: function(data, textStatus, XMLHttpRequest)\n\t\t\t\t\t{\n\t\t\t\t\t\tif (data.valid){\t\t\n\t\t\t\t\t\t\tmyToast.update({\n\t\t\t\t\t\t\t\theading: 'Success',\n\t\t\t\t\t\t\t\ttext: data.message,\n\t\t\t\t\t\t\t\ticon: 'success',\n\t\t\t\t\t\t\t\thideAfter: false\n\t\t\t\t\t\t\t});\n\t\t\t\t\t\t}else{\n\t\t\t\t\t\t\tmyToast.update({\n\t\t\t\t\t\t\t\theading: 'Error',\n\t\t\t\t\t\t\t\ttext: data.message,\n\t\t\t\t\t\t\t\ticon: 'error',\n\t\t\t\t\t\t\t\thideAfter: false\n\t\t\t\t\t\t\t});\n\t\t\t\t\t\t}\n\t\t\t\t\t},\n\t\t\t\t\terror: function(XMLHttpRequest, textStatus, errorThrown)\n\t\t\t\t\t{\t\t\t\t\t\n\t\t\t\t\t\tmyToast.update({\n\t\t\t\t\t\t\theading: 'Error',\n\t\t\t\t\t\t\ttext: 'Error while contacting server, please try again',\n\t\t\t\t\t\t\ticon: 'error',\n\t\t\t\t\t\t\thideAfter: false\n\t\t\t\t\t\t});\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t}\n\t\t\t\t});\n\t\t\t});\n\t\t});\n\t\t</script>  \n    </body>\n</html>";

?>