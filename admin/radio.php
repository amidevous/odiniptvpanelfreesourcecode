<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_radio") && !hasPermissions("adv", "edit_radio")) {
    exit;
}
if (isset($_POST["submit_radio"])) {
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_radio")) {
            exit;
        }
        $rArray = getStream($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        if (!hasPermissions("adv", "add_radio")) {
            exit;
        }
        $rArray = ["type" => 4, "added" => time(), "read_native" => 0, "stream_all" => 0, "redirect_stream" => 1, "direct_source" => 0, "gen_timestamps" => 0, "transcode_attributes" => [], "stream_display_name" => "", "stream_source" => [], "category_id" => 0, "stream_icon" => "", "notes" => "", "custom_sid" => "", "custom_ffmpeg" => "", "custom_map" => "", "transcode_profile_id" => 0, "enable_transcode" => 0, "auto_restart" => "[]", "allow_record" => 0, "rtmp_output" => 0, "epg_id" => NULL, "channel_id" => NULL, "epg_lang" => NULL, "tv_archive_server_id" => 0, "tv_archive_duration" => 0, "delay_minutes" => 0, "external_push" => [], "probesize_ondemand" => 128000];
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
    if (isset($_POST["custom_ffmpeg"])) {
        $rArray["custom_ffmpeg"] = $_POST["custom_ffmpeg"];
    }
    if (isset($_POST["custom_sid"])) {
        $rArray["custom_sid"] = $_POST["custom_sid"];
    }
    if (isset($_POST["direct_source"])) {
        $rArray["direct_source"] = 1;
        unset($_POST["direct_source"]);
    } else {
        $rArray["direct_source"] = 0;
    }
    if (isset($_POST["probesize_ondemand"])) {
        $rArray["probesize_ondemand"] = intval($_POST["probesize_ondemand"]);
        unset($_POST["probesize_ondemand"]);
    } else {
        $rArray["probesize_ondemand"] = 128000;
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
    if (0 < strlen($_POST["stream_source"][0])) {
        $rImportArray = ["stream_source" => $_POST["stream_source"], "stream_icon" => $rArray["stream_icon"], "stream_display_name" => $rArray["stream_display_name"]];
        if (isset($_POST["edit"])) {
            $rImportStreams[] = $rImportArray;
        } else {
            $rResult = $db->query("SELECT COUNT(`id`) AS `count` FROM `streams` WHERE `stream_display_name` = '" . ESC($rImportArray["stream_display_name"]) . "' AND `type` = 4;");
            if ($rResult->fetch_assoc()["count"] == 0) {
                $rImportStreams[] = $rImportArray;
            } else {
                $_STATUS = 2;
                $rStation = $rArray;
            }
        }
    } else {
        $_STATUS = 1;
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
                $rStationExists = [];
                if (isset($_POST["edit"])) {
                    $result = $db->query("SELECT `server_stream_id`, `server_id` FROM `streams_sys` WHERE `stream_id` = " . intval($rInsertID) . ";");
                    if ($result && 0 < $result->num_rows) {
                        while ($row = $result->fetch_assoc()) {
                            $rStationExists[intval($row["server_id"])] = intval($row["server_stream_id"]);
                        }
                    }
                }
                if (isset($_POST["server_tree_data"])) {
                    $rStationsAdded = [];
                    $rServerTree = json_decode($_POST["server_tree_data"], true);
                    foreach ($rServerTree as $rServer) {
                        if ($rServer["parent"] != "#") {
                            $rServerID = intval($rServer["id"]);
                            $rStationsAdded[] = $rServerID;
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
                            if (isset($rStationExists[$rServerID])) {
                                $db->query("UPDATE `streams_sys` SET `parent_id` = " . $rParent . ", `on_demand` = " . $rOD . " WHERE `server_stream_id` = " . $rStationExists[$rServerID] . ";");
                            } else {
                                $db->query("INSERT INTO `streams_sys`(`stream_id`, `server_id`, `parent_id`, `on_demand`) VALUES(" . intval($rInsertID) . ", " . $rServerID . ", " . $rParent . ", " . $rOD . ");");
                            }
                        }
                    }
                    foreach ($rStationExists as $rServerID => $rDBID) {
                        if (!in_array($rServerID, $rStationsAdded)) {
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
                if (isset($_POST["edit"])) {
                    foreach (getBouquets() as $rBouquet) {
                        if (!in_array($rBouquet["id"], $rBouquets)) {
                            removeFromBouquet("stream", $rBouquet["id"], $rInsertID);
                        }
                    }
                }
                $_STATUS = 0;
            } else {
                $_STATUS = 1;
                $rStation = $rArray;
            }
        }
        scanBouquets();
        if (isset($rInsertID)) {
            header("Location: ./radio.php?id=" . $rInsertID);
            exit;
        }
    } else {
        if (!isset($_STATUS)) {
            $_STATUS = 3;
            $rStation = $rArray;
        }
    }
}
if (isset($_STATUS)) {
    foreach ($rStation as $rKey => $rValue) {
        if (is_array($rValue)) {
            $rStation[$rKey] = json_encode($rValue);
        }
    }
}
$rStationArguments = getStreamArguments();
$rServerTree = [];
$rOnDemand = [];
$rServerTree[] = ["id" => "source", "parent" => "#", "text" => "<strong>" . $_["stream_source"] . "</strong>", "icon" => "mdi mdi-youtube-tv", "state" => ["opened" => true]];
if (isset($_GET["id"])) {
    if (!hasPermissions("adv", "edit_radio")) {
        exit;
    }
    $rStation = getStream($_GET["id"]);
    if (!$rStation || $rStation["type"] != 4) {
        exit;
    }
    $rStationOptions = getStreamOptions($_GET["id"]);
    $rStationSys = getStreamSys($_GET["id"]);
    foreach ($rServers as $rServer) {
        if (isset($rStationSys[intval($rServer["id"])])) {
            if ($rStationSys[intval($rServer["id"])]["parent_id"] != 0) {
                $rParent = intval($rStationSys[intval($rServer["id"])]["parent_id"]);
            } else {
                $rParent = "source";
            }
        } else {
            $rParent = "#";
        }
        $rServerTree[] = ["id" => $rServer["id"], "parent" => $rParent, "text" => $rServer["server_name"], "icon" => "mdi mdi-server-network", "state" => ["opened" => true]];
    }
    foreach ($rStationSys as $rStationItem) {
        if ($rStationItem["on_demand"] == 1) {
            $rOnDemand[] = $rStationItem["server_id"];
        }
    }
} else {
    if (!hasPermissions("adv", "add_radio")) {
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
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"./radios.php\">\n                                            <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\">\n                                                ";
echo $_["view_stations"];
echo "                                            </button>\n                                        </a>\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rStation["id"])) {
    echo $rStation["stream_display_name"];
} else {
    echo $_["add_radio_station"];
}
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["radio_success"];
    echo "                        </div>\n                        ";
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n\t\t\t\t\t\t\t";
        echo $_["radio_info_1"];
        echo "                        </div>\n                        ";
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n\t\t\t\t\t\t\t";
            echo $_["radio_info_2"];
            echo "                        </div>\n                        ";
        }
    }
}
if (isset($rStation["id"])) {
    echo "                        <div class=\"card text-xs-center\">\n                            <div class=\"table\">\n                                <table id=\"datatable\" class=\"table table-borderless mb-0\">\n                                    <thead class=\"bg-light\">\n                                        <tr>\n                                            <th></th>\n                                            <th></th>\n                                            <th>";
    echo $_["source"];
    echo "</th>\n                                            <th>";
    echo $_["clients"];
    echo "</th>\n                                            <th>";
    echo $_["uptime"];
    echo "</th>\n                                            <th>";
    echo $_["actions"];
    echo "</th>\n                                            <th></th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        <tr>\n                                            <td colspan=\"7\" class=\"text-center\">";
    echo $_["loading_station_information"];
    echo "...</td>\n                                        </tr>\n                                    </tbody>\n                                </table>\n                            </div>\n                        </div>\n                        ";
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./radio.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"radio_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rStation["id"])) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rStation["id"];
    echo "\" />\n                                    ";
}
echo "                                    <input type=\"hidden\" name=\"server_tree_data\" id=\"server_tree_data\" value=\"\" />\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#stream-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#advanced-options\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-folder-alert-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["advanced"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#auto-restart\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-clock-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["auto_restart"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#load-balancing\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-server-network mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["servers"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"stream-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_display_name\">";
echo $_["station_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"stream_display_name\" name=\"stream_display_name\" value=\"";
if (isset($rStation)) {
    echo htmlspecialchars($rStation["stream_display_name"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4 stream-url\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_source\">";
echo $_["station_url"];
echo "</label>\n                                                            <div class=\"col-md-8 input-group\">\n                                                                <input type=\"text\" id=\"stream_source\" name=\"stream_source[]\" class=\"form-control\" value=\"";
if (isset($rStation)) {
    echo htmlspecialchars(json_decode($rStation["stream_source"], true)[0]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_id\">";
echo $_["category_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"category_id\" id=\"category_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach (getCategories("radio") as $rCategory) {
    echo "                                                                    <option ";
    if (isset($rStation)) {
        if (intval($rStation["category_id"]) == intval($rCategory["id"])) {
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
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"bouquets[]\" id=\"bouquets\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "...\">\n                                                                    ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                                    <option ";
    if (isset($rStation) && in_array($rStation["id"], json_decode($rBouquet["bouquet_channels"], true))) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rBouquet["id"];
    echo "\">";
    echo htmlspecialchars($rBouquet["bouquet_name"]);
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_icon\">";
echo $_["station_logo_url"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"stream_icon\" name=\"stream_icon\" value=\"";
if (isset($rStation)) {
    echo htmlspecialchars($rStation["stream_icon"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"notes\">";
echo $_["notes"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <textarea id=\"notes\" name=\"notes\" class=\"form-control\" rows=\"3\" placeholder=\"\">";
if (isset($rStation)) {
    echo htmlspecialchars($rStation["notes"]);
}
echo "</textarea>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"advanced-options\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"direct_source\">";
echo $_["direct_source"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Don't run source through Xtream Codes, just redirect instead.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"direct_source\" id=\"direct_source\" type=\"checkbox\" ";
if (isset($rStation) && $rStation["direct_source"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"custom_sid\">";
echo $_["custom_channel_sid"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Here you can specify the SID of the channel in order to work with the epg on the enigma2 devices. You have to specify the code with the ':' but without the first number, 1 or 4097 . Example: if we have this code:  '1:0:1:13f:157c:13e:820000:0:0:0:2097' then you have to add on this field:  ':0:1:13f:157c:13e:820000:0:0:0:\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"custom_sid\" name=\"custom_sid\" value=\"";
if (isset($rStation)) {
    echo htmlspecialchars($rStation["custom_sid"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"custom_ffmpeg\">";
echo $_["custom_ffmpeg_command"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"In this field you can write your own custom FFmpeg command. Please note that this command will be placed after the input and before the output. If the command you will specify here is about to do changes in the output video or audio, it may require to transcode the stream. In this case, you have to use and change at least the Video/Audio Codecs using the transcoding attributes below. The custom FFmpeg command will only be used by the server(s) that take the stream from the Source.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"custom_ffmpeg\" name=\"custom_ffmpeg\" value=\"";
if (isset($rStation)) {
    echo htmlspecialchars($rStation["custom_ffmpeg"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"probesize_ondemand\">";
echo $_["on_demand_probesize"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Adjustable probesize for ondemand streams. Adjust this setting if you experience issues with no audio.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"probesize_ondemand\" name=\"probesize_ondemand\" value=\"";
if (isset($rStation)) {
    echo htmlspecialchars($rStation["probesize_ondemand"]);
} else {
    echo "128000";
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"user_agent\">";
echo $_["user_agent"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"user_agent\" name=\"user_agent\" value=\"";
if (isset($rStationOptions[1])) {
    echo htmlspecialchars($rStationOptions[1]["value"]);
} else {
    echo htmlspecialchars($rStationArguments["user_agent"]["argument_default_value"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"http_proxy\">";
echo $_["http_proxy"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Format: ip:port\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"http_proxy\" name=\"http_proxy\" value=\"";
if (isset($rStationOptions[2])) {
    echo htmlspecialchars($rStationOptions[2]["value"]);
} else {
    echo htmlspecialchars($rStationArguments["proxy"]["argument_default_value"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"cookie\">";
echo $_["cookie"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Format: key=value;\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"cookie\" name=\"cookie\" value=\"";
if (isset($rStationOptions[17])) {
    echo htmlspecialchars($rStationOptions[17]["value"]);
} else {
    echo htmlspecialchars($rStationArguments["cookie"]["argument_default_value"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"headers\">";
echo $_["headers"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"FFmpeg -headers command.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"headers\" name=\"headers\" value=\"";
if (isset($rStreamOptions[19])) {
    echo htmlspecialchars($rStreamOptions[19]["value"]);
} else {
    echo htmlspecialchars($rStreamArguments["headers"]["argument_default_value"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"auto-restart\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"days_to_restart\">";
echo $_["days_to_restart"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                ";
$rAutoRestart = ["days" => [], "at" => "06:00"];
if (isset($rStation) && strlen($rStation["auto_restart"])) {
    $rAutoRestart = json_decode($rStation["auto_restart"], true);
    if (!isset($rAutoRestart["days"])) {
        $rAutoRestart["days"] = [];
    }
    if (!isset($rAutoRestart["at"])) {
        $rAutoRestart["at"] = "06:00";
    }
}
echo "                                                                <select id=\"days_to_restart\" name=\"days_to_restart[]\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "...\">\n                                                                    ";
foreach ([$_["monday"] => "Monday", $_["tuesday"] => "Tuesday", $_["wednesday"] => "Wednesday", $_["thursday"] => "Thursday", $_["friday"] => "Friday", $_["saturday"] => "Saturday", $_["sunday"] => "Sunday"] as $rDay) {
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
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <div class=\"input-group clockpicker\" data-placement=\"top\" data-align=\"top\" data-autoclose=\"true\">\n                                                                    <input id=\"time_to_restart\" name=\"time_to_restart\" type=\"text\" class=\"form-control\" value=\"";
echo $rAutoRestart["at"];
echo "\">\n                                                                    <div class=\"input-group-append\">\n                                                                        <span class=\"input-group-text\"><i class=\"mdi mdi-clock-outline\"></i></span>\n                                                                    </div>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"load-balancing\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"servers\">";
echo $_["server_tree"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <div id=\"server_tree\"></div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"on_demand\">";
echo $_["on_demand"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"on_demand\" name=\"on_demand[]\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "...\">\n                                                                    ";
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
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"restart_on_edit\">";
if (isset($rStation["id"])) {
    echo $_["restart_on_edit"];
} else {
    echo $_["start_stream_now"];
}
echo "</label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input name=\"restart_on_edit\" id=\"restart_on_edit\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_radio\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rStation["id"])) {
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
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var rSwitches = [];\n        \n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        function reloadStream() {\n            \$(\"#datatable\").DataTable().ajax.reload( null, false );\n            setTimeout(reloadStream, 5000);\n        }\n        function api(rID, rServerID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["radio_delete_confirm"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=stream&sub=\" + rType + \"&stream_id=\" + rID + \"&server_id=\" + rServerID, function(data) {\n                if (data.result == true) {\n                    if (rType == \"start\") {\n                        \$.toast(\"";
echo $_["radio_started"];
echo "\");\n                    } else if (rType == \"stop\") {\n                        \$.toast(\"";
echo $_["radio_stopped"];
echo "\");\n                    } else if (rType == \"restart\") {\n                        \$.toast(\"";
echo $_["radio_restarted"];
echo "\");\n                    } else if (rType == \"delete\") {\n                        \$(\"#stream-\" + rID + \"-\" + rServerID).remove();\n                        \$.toast(\"";
echo $_["radio_deleted"];
echo "\");\n                    }\n                    \$(\"#datatable\").DataTable().ajax.reload( null, false );\n                } else {\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                }\n            }).fail(function() {\n                \$.toast(\"";
echo $_["error_occured"];
echo "\");\n            });\n        }\n        function setSwitch(switchElement, checkedBool) {\n            if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {\n                switchElement.setPosition(true);\n                switchElement.handleOnchange(true);\n            }\n        }\n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'})\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n              var switchery = new Switchery(html);\n              window.rSwitches[\$(html).attr(\"id\")] = switchery;\n            });\n            \$(\".clockpicker\").clockpicker();\n            \$('#server_tree').jstree({ 'core' : {\n                'check_callback': function (op, node, parent, position, more) {\n                    switch (op) {\n                        case 'move_node':\n                            if (node.id == \"source\") { return false; }\n                            return true;\n                    }\n                },\n                'data' : ";
echo json_encode($rServerTree);
echo "            }, \"plugins\" : [ \"dnd\" ]\n            });\n            \$(\"#direct_source\").change(function() {\n                evaluateDirectSource();\n            });\n            function evaluateDirectSource() {\n                \$([\"custom_ffmpeg\", \"probesize_ondemand\", \"user_agent\", \"http_proxy\", \"cookie\", \"headers\", \"days_to_restart\", \"time_to_restart\", \"on_demand\", \"restart_on_edit\"]).each(function(rID, rElement) {\n                    if (\$(rElement)) {\n                        if (\$(\"#direct_source\").is(\":checked\")) {\n                            if (window.rSwitches[rElement]) {\n                                setSwitch(window.rSwitches[rElement], false);\n                                window.rSwitches[rElement].disable();\n                            } else {\n                                \$(\"#\" + rElement).prop(\"disabled\", true);\n                            }\n                        } else {\n                            if (window.rSwitches[rElement]) {\n                                window.rSwitches[rElement].enable();\n                            } else {\n                                \$(\"#\" + rElement).prop(\"disabled\", false);\n                            }\n                        }\n                    }\n                });\n            }\n            \n            \$(\"#radio_form\").submit(function(e){\n                if (\$(\"#stream_display_name\").val().length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["enter_a_radio_station_name"];
echo "\");\n                }\n                \$(\"#server_tree_data\").val(JSON.stringify(\$('#server_tree').jstree(true).get_json('#', {flat:true})));\n            });\n            \n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"#probesize_ondemand\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#delay_minutes\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#tv_archive_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n            ";
if (isset($rStation["id"])) {
    echo "            \$(\"#datatable\").DataTable({\n                ordering: false,\n                paging: false,\n                searching: false,\n                processing: true,\n                serverSide: true,\n                bInfo: false,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"radios\";\n                        d.stream_id = ";
    echo $rStation["id"];
    echo ";\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [2,3,4,5]},\n                    {\"visible\": false, \"targets\": [0,1,6]}\n                ],\n            });\n            setTimeout(reloadStream, 5000);\n            ";
}
echo "            evaluateDirectSource();\n        });\n        </script>\n    </body>\n</html>";

?>