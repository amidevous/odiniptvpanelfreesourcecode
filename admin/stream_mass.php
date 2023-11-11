<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "mass_edit_streams")) {
    exit;
}
if (isset($_POST["submit_stream"])) {
    $rArray = [];
    if (isset($_POST["c_days_to_restart"])) {
        if (isset($_POST["days_to_restart"]) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]\$/", $_POST["time_to_restart"])) {
            $rTimeArray = ["days" => [], "at" => $_POST["time_to_restart"]];
            foreach ($_POST["days_to_restart"] as $rID => $rDay) {
                $rTimeArray["days"][] = $rDay;
            }
            $rArray["auto_restart"] = json_encode($rTimeArray);
        } else {
            $rArray["auto_restart"] = "";
        }
    }
    if (isset($_POST["c_gen_timestamps"])) {
        if (isset($_POST["gen_timestamps"])) {
            $rArray["gen_timestamps"] = 1;
        } else {
            $rArray["gen_timestamps"] = 0;
        }
    }
    if (isset($_POST["c_allow_record"])) {
        if (isset($_POST["allow_record"])) {
            $rArray["allow_record"] = 1;
        } else {
            $rArray["allow_record"] = 0;
        }
    }
    if (isset($_POST["c_rtmp_output"])) {
        if (isset($_POST["rtmp_output"])) {
            $rArray["rtmp_output"] = 1;
        } else {
            $rArray["rtmp_output"] = 0;
        }
    }
    if (isset($_POST["c_stream_all"])) {
        if (isset($_POST["stream_all"])) {
            $rArray["stream_all"] = 1;
        } else {
            $rArray["stream_all"] = 0;
        }
    }
    if (isset($_POST["c_direct_source"])) {
        if (isset($_POST["direct_source"])) {
            $rArray["direct_source"] = 1;
        } else {
            $rArray["direct_source"] = 0;
        }
    }
    if (isset($_POST["c_read_native"])) {
        if (isset($_POST["read_native"])) {
            $rArray["read_native"] = 1;
        } else {
            $rArray["read_native"] = 0;
        }
    }
    if (isset($_POST["c_tv_archive_server_id"])) {
        if (isset($_POST["tv_archive_server_id"])) {
            $rArray["tv_archive_server_id"] = intval($_POST["tv_archive_server_id"]);
        } else {
            $rArray["tv_archive_server_id"] = 0;
        }
    }
    if (isset($_POST["c_tv_archive_duration"])) {
        if (isset($_POST["tv_archive_duration"])) {
            $rArray["tv_archive_duration"] = intval($_POST["tv_archive_duration"]);
        } else {
            $rArray["tv_archive_duration"] = 0;
        }
    }
    if (isset($_POST["c_delay_minutes"])) {
        if (isset($_POST["delay_minutes"])) {
            $rArray["delay_minutes"] = intval($_POST["delay_minutes"]);
        } else {
            $rArray["delay_minutes"] = 0;
        }
    }
    if (isset($_POST["c_probesize_ondemand"])) {
        if (isset($_POST["probesize_ondemand"])) {
            $rArray["probesize_ondemand"] = intval($_POST["probesize_ondemand"]);
        } else {
            $rArray["probesize_ondemand"] = 0;
        }
    }
    if (isset($_POST["c_category_id"])) {
        $rArray["category_id"] = intval($_POST["category_id"]);
    }
    if (isset($_POST["c_custom_sid"])) {
        $rArray["custom_sid"] = $_POST["custom_sid"];
    }
    if (isset($_POST["c_custom_ffmpeg"])) {
        $rArray["custom_ffmpeg"] = $_POST["custom_ffmpeg"];
    }
    if (isset($_POST["c_transcode_profile_id"])) {
        $rArray["transcode_profile_id"] = $_POST["transcode_profile_id"];
        if (0 < $rArray["transcode_profile_id"]) {
            $rArray["enable_transcode"] = 1;
        } else {
            $rArray["enable_transcode"] = 0;
        }
    }
    $rStreamIDs = json_decode($_POST["streams"], true);
    if (0 < count($rStreamIDs)) {
        foreach ($rStreamIDs as $rStreamID) {
            $rQueries = [];
            foreach ($rArray as $rKey => $rValue) {
                $rQueries[] = "`" . ESC($rKey) . "` = '" . ESC($rValue) . "'";
            }
            if (0 < count($rQueries)) {
                $rQueryString = join(",", $rQueries);
                $rQuery = "UPDATE `streams` SET " . $rQueryString . " WHERE `id` = " . intval($rStreamID) . ";";
                if (!$db->query($rQuery)) {
                    $_STATUS = 1;
                }
            }
            if (isset($_POST["c_server_tree"])) {
                $rOnDemandArray = [];
                if (isset($_POST["on_demand"])) {
                    foreach ($_POST["on_demand"] as $rID) {
                        $rOnDemandArray[] = intval($rID);
                    }
                }
                $rStreamExists = [];
                $result = $db->query("SELECT `server_stream_id`, `server_id` FROM `streams_sys` WHERE `stream_id` = " . intval($rStreamID) . ";");
                if ($result && 0 < $result->num_rows) {
                    while ($row = $result->fetch_assoc()) {
                        $rStreamExists[intval($row["server_id"])] = intval($row["server_stream_id"]);
                    }
                }
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
                            if (!$db->query("UPDATE `streams_sys` SET `parent_id` = " . $rParent . ", `on_demand` = " . $rOD . " WHERE `server_stream_id` = " . $rStreamExists[$rServerID] . ";")) {
                                $_STATUS = 1;
                            }
                        } else {
                            if (!$db->query("INSERT INTO `streams_sys`(`stream_id`, `server_id`, `parent_id`, `on_demand`) VALUES(" . intval($rStreamID) . ", " . $rServerID . ", " . $rParent . ", " . $rOD . ");")) {
                                $_STATUS = 1;
                            }
                        }
                    }
                }
                foreach ($rStreamExists as $rServerID => $rDBID) {
                    if (!in_array($rServerID, $rStreamsAdded)) {
                        $db->query("DELETE FROM `streams_sys` WHERE `server_stream_id` = " . $rDBID . ";");
                    }
                }
            }
            if (isset($_POST["c_user_agent"])) {
                $db->query("DELETE FROM `streams_options` WHERE `stream_id` = " . intval($rStreamID) . " AND `argument_id` = 1;");
                if (isset($_POST["user_agent"]) && 0 < strlen($_POST["user_agent"])) {
                    $db->query("INSERT INTO `streams_options`(`stream_id`, `argument_id`, `value`) VALUES(" . intval($rStreamID) . ", 1, '" . ESC($_POST["user_agent"]) . "');");
                }
            }
            if (isset($_POST["c_http_proxy"])) {
                $db->query("DELETE FROM `streams_options` WHERE `stream_id` = " . intval($rStreamID) . " AND `argument_id` = 2;");
                if (isset($_POST["http_proxy"]) && 0 < strlen($_POST["http_proxy"])) {
                    $db->query("INSERT INTO `streams_options`(`stream_id`, `argument_id`, `value`) VALUES(" . intval($rStreamID) . ", 2, '" . ESC($_POST["http_proxy"]) . "');");
                }
            }
            if (isset($_POST["c_cookie"])) {
                $db->query("DELETE FROM `streams_options` WHERE `stream_id` = " . intval($rStreamID) . " AND `argument_id` = 17;");
                if (isset($_POST["cookie"]) && 0 < strlen($_POST["cookie"])) {
                    $db->query("INSERT INTO `streams_options`(`stream_id`, `argument_id`, `value`) VALUES(" . intval($rStreamID) . ", 17, '" . ESC($_POST["cookie"]) . "');");
                }
            }
            if (isset($_POST["c_headers"])) {
                $db->query("DELETE FROM `streams_options` WHERE `stream_id` = " . intval($rStreamID) . " AND `argument_id` = 19;");
                if (isset($_POST["headers"]) && 0 < strlen($_POST["headers"])) {
                    $db->query("INSERT INTO `streams_options`(`stream_id`, `argument_id`, `value`) VALUES(" . intval($rStreamID) . ", 19, '" . ESC($_POST["headers"]) . "');");
                }
            }
            if (isset($_POST["c_bouquets"])) {
                $rBouquets = $_POST["bouquets"];
                foreach ($rBouquets as $rBouquet) {
                    addToBouquet("stream", $rBouquet, $rStreamID);
                }
                foreach (getBouquets() as $rBouquet) {
                    if (!in_array($rBouquet["id"], $rBouquets)) {
                        removeFromBouquet("stream", $rBouquet["id"], $rStreamID);
                    }
                }
            }
        }
        if (isset($_POST["restart_on_edit"])) {
            APIRequest(["action" => "stream", "sub" => "start", "stream_ids" => array_values($rStreamIDs)]);
        }
        if (isset($_POST["c_bouquets"])) {
            scanBouquets();
        }
    }
    $_STATUS = 0;
}
$rStreamArguments = getStreamArguments();
$rTranscodeProfiles = getTranscodeProfiles();
$rServerTree = [];
$rServerTree[] = ["id" => "source", "parent" => "#", "text" => "<strong>Stream Source</strong>", "icon" => "mdi mdi-youtube-tv", "state" => ["opened" => true]];
foreach ($rServers as $rServer) {
    $rServerTree[] = ["id" => $rServer["id"], "parent" => "#", "text" => $rServer["server_name"], "icon" => "mdi mdi-server-network", "state" => ["opened" => true]];
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./streams.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_streams"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["mass_edit_streams"];
echo "  <small id=\"selected_count\"></small></h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["mass_edit_of_streams"];
        echo " \n                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["mass_edit_of_streams"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["generic_fail"];
            echo " \n                        </div>\n                        ";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", \"There was an error performing this operation! Please check the form entry and try again.\", \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./stream_mass.php\" method=\"POST\" id=\"stream_form\">\n                                    <input type=\"hidden\" name=\"server_tree_data\" id=\"server_tree_data\" value=\"\" />\n                                    <input type=\"hidden\" name=\"streams\" id=\"streams\" value=\"\" />\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#stream-selection\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-play mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["streams"];
echo " </span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#stream-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo " </span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#auto-restart\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-clock-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["auto_restart"];
echo " </span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#load-balancing\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-server-network mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["servers"];
echo " </span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"stream-selection\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-md-4 col-6\">\n                                                        <input type=\"text\" class=\"form-control\" id=\"stream_search\" value=\"\" placeholder=\"";
echo $_["search_streams"];
echo "\">\n                                                    </div>\n                                                    <div class=\"col-md-4 col-6\">\n                                                        <select id=\"category_search\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["all_categories"];
echo " </option>\n                                                            ";
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
echo " </label>\n                                                    <div class=\"col-md-2 col-8\">\n                                                        <select id=\"show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                            ";
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
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light\" onClick=\"toggleStreams()\">\n                                                            <i class=\"mdi mdi-selection\"></i>\n                                                        </button>\n                                                    </div>\n                                                    <table id=\"datatable-mass\" class=\"table table-hover table-borderless mb-0\">\n                                                        <thead class=\"bg-light\">\n                                                            <tr>\n                                                                <th class=\"text-center\">";
echo $_["id"];
echo " </th>\n                                                                <th>";
echo $_["streams_name"];
echo " </th>\n                                                                <th>";
echo $_["categoty"];
echo " </th>\n                                                            </tr>\n                                                        </thead>\n                                                        <tbody></tbody>\n                                                    </table>\n                                                </div>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"stream-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <p class=\"sub-header\">\n                                                            ";
echo $_["mass_edit_info"];
echo " \n                                                        </p>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"category_id\" name=\"c_category_id\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"category_id\">";
echo $_["category_name"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select disabled name=\"category_id\" id=\"category_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach ($rCategories as $rCategory) {
    echo "                                                                    <option value=\"";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"bouquets\" name=\"c_bouquets\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"bouquets\">";
echo $_["select_bouquets"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select disabled name=\"bouquets[]\" id=\"bouquets\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "\">\n                                                                    ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                                    <option value=\"";
    echo $rBouquet["id"];
    echo "\">";
    echo $rBouquet["bouquet_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"gen_timestamps\" data-type=\"switch\" name=\"c_gen_timestamps\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"gen_timestamps\">";
echo $_["generate_pts"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"gen_timestamps\" id=\"gen_timestamps\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"read_native\">";
echo $_["native_frames"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"read_native\" id=\"read_native\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"read_native\" data-type=\"switch\" name=\"c_read_native\">\n                                                                <label></label>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"stream_all\" data-type=\"switch\" name=\"c_stream_all\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"stream_all\">";
echo $_["stream_all_codecs"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"stream_all\" id=\"stream_all\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"allow_record\">";
echo $_["allow_recording"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"allow_record\" id=\"allow_record\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"allow_record\" data-type=\"switch\" name=\"c_allow_record\">\n                                                                <label></label>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"rtmp_output\" data-type=\"switch\" name=\"c_rtmp_output\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"rtmp_output\">";
echo $_["allow_rtmp_output"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"rtmp_output\" id=\"rtmp_output\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"direct_source\">";
echo $_["direct_source"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"direct_source\" id=\"direct_source\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"direct_source\" data-type=\"switch\" name=\"c_direct_source\">\n                                                                <label></label>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"custom_sid\" name=\"c_custom_sid\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"custom_sid\">";
echo $_["custom_channel_sid"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" disabled class=\"form-control\" id=\"custom_sid\" name=\"custom_sid\" value=\"\">\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"delay_minutes\">";
echo $_["minute_delay"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" disabled class=\"form-control\" id=\"delay_minutes\" name=\"delay_minutes\" value=\"0\">\n                                                            </div>\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"delay_minutes\" name=\"c_delay_minutes\">\n                                                                <label></label>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"custom_ffmpeg\" name=\"c_custom_ffmpeg\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"custom_ffmpeg\">";
echo $_["custom_ffmpeg_command"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" disabled class=\"form-control\" id=\"custom_ffmpeg\" name=\"custom_ffmpeg\" value=\"\">\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"probesize_ondemand\">";
echo $_["on_demand_probesize"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" disabled class=\"form-control\" id=\"probesize_ondemand\" name=\"probesize_ondemand\" value=\"128000\">\n                                                            </div>\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"probesize_ondemand\" name=\"c_probesize_ondemand\">\n                                                                <label></label>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"user_agent\" name=\"c_user_agent\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"user_agent\">";
echo $_["user_agent"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" disabled class=\"form-control\" id=\"user_agent\" name=\"user_agent\" value=\"";
echo htmlspecialchars($rStreamArguments["user_agent"]["argument_default_value"]);
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"http_proxy\" name=\"c_http_proxy\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"http_proxy\">";
echo $_["http_proxy"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" disabled class=\"form-control\" id=\"http_proxy\" name=\"http_proxy\" value=\"";
echo htmlspecialchars($rStreamArguments["proxy"]["argument_default_value"]);
echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"cookie\" name=\"c_cookie\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"cookie\">";
echo $_["cookie"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" disabled class=\"form-control\" id=\"cookie\" name=\"cookie\" value=\"";
echo htmlspecialchars($rStreamArguments["cookie"]["argument_default_value"]);
echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"headers\" name=\"c_headers\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"headers\">";
echo $_["headers"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" disabled class=\"form-control\" id=\"headers\" name=\"headers\" value=\"";
echo htmlspecialchars($rStreamArguments["headers"]["argument_default_value"]);
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"transcode_profile_id\" name=\"c_transcode_profile_id\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"transcode_profile_id\">";
echo $_["transcoding_profile"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"transcode_profile_id\" disabled id=\"transcode_profile_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option selected value=\"0\">";
echo $_["transcoding_disabled"];
echo " </option>\n                                                                    ";
foreach ($rTranscodeProfiles as $rProfile) {
    echo "                                                                    <option value=\"";
    echo $rProfile["profile_id"];
    echo "\">";
    echo $rProfile["profile_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo " </a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo " </a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            \n                                            <div class=\"tab-pane\" id=\"auto-restart\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"days_to_restart\" name=\"c_days_to_restart\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"days_to_restart\">";
echo $_["days_to_restart"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                ";
$rAutoRestart = ["days" => [], "at" => "06:00"];
echo "                                                                <select disabled id=\"days_to_restart\" name=\"days_to_restart[]\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "\">\n                                                                    ";
foreach (["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"] as $rDay) {
    echo "                                                                    <option value=\"";
    echo $rDay;
    echo "\">";
    echo $rDay;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"col-md-1\"></div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"time_to_restart\">";
echo $_["time_to_restart"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <div class=\"input-group clockpicker\" data-placement=\"top\" data-align=\"top\" data-autoclose=\"true\">\n                                                                    <input disabled id=\"time_to_restart\" name=\"time_to_restart\" type=\"text\" class=\"form-control\" value=\"";
echo $rAutoRestart["at"];
echo "\">\n                                                                    <div class=\"input-group-append\">\n                                                                        <span class=\"input-group-text\"><i class=\"mdi mdi-clock-outline\"></i></span>\n                                                                    </div>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo " </a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo " </a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"load-balancing\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" data-name=\"on_demand\" class=\"activate\" name=\"c_server_tree\" id=\"c_server_tree\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"server_tree\">";
echo $_["server_tree"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <div id=\"server_tree\"></div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"col-md-1\"></div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"on_demand\">";
echo $_["on_demand"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select disabled id=\"on_demand\" name=\"on_demand[]\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "\">\n                                                                    ";
foreach ($rServers as $rServerItem) {
    echo "                                                                        <option value=\"";
    echo $rServerItem["id"];
    echo "\">";
    echo $rServerItem["server_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"tv_archive_server_id\" name=\"c_tv_archive_server_id\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"tv_archive_server_id\">";
echo $_["timeshift_server"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <select disabled name=\"tv_archive_server_id\" id=\"tv_archive_server_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option value=\"\">";
echo $_["timeshift_disabled"];
echo " </option>\n                                                                    ";
foreach ($rServers as $rServer) {
    echo "                                                                    <option value=\"";
    echo $rServer["id"];
    echo "\">";
    echo $rServer["server_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"tv_archive_duration\" name=\"c_tv_archive_duration\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"tv_archive_duration\">";
echo $_["timeshift_days"];
echo " </label>\n                                                            <div class=\"col-md-3\">\n                                                                <input disabled type=\"text\" class=\"form-control\" id=\"tv_archive_duration\" name=\"tv_archive_duration\" value=\"0\" />\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"restart_on_edit\">";
echo $_["restart_on_edit"];
echo " </label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"restart_on_edit\" id=\"restart_on_edit\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\" checked />\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo " </a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_stream\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["edit_streams"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "\n        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-ui/jquery-ui.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var rSwitches = [];\n        var rSelected = [];\n        \n        function getCategory() {\n            return \$(\"#category_search\").val();\n        }\n        function toggleStreams() {\n            \$(\"#datatable-mass tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rSelected.splice(\$.inArray(\$(this).find(\"td:eq(0)\").html(), window.rSelected), 1);\n                    }\n                } else {            \n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rSelected.push(\$(this).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n            \$(\"#selected_count\").html(\" - \" + window.rSelected.length + \" selected\")\n        }\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'})\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n                var switchery = new Switchery(html);\n                window.rSwitches[\$(html).attr(\"id\")] = switchery;\n                if (\$(html).attr(\"id\") != \"restart_on_edit\") {\n                    window.rSwitches[\$(html).attr(\"id\")].disable();\n                }\n            });\n            \$('#server_tree').jstree({ 'core' : {\n                'check_callback': function (op, node, parent, position, more) {\n                    switch (op) {\n                        case 'move_node':\n                            if (node.id == \"source\") { return false; }\n                            return true;\n                    }\n                },\n                'data' : ";
echo json_encode($rServerTree);
echo "            }, \"plugins\" : [ \"dnd\" ]\n            });\n            \$(\"#stream_form\").submit(function(e){\n                \$(\"#server_tree_data\").val(JSON.stringify(\$('#server_tree').jstree(true).get_json('#', {flat:true})));\n                rPass = false;\n                \$.each(\$('#server_tree').jstree(true).get_json('#', {flat:true}), function(k,v) {\n                    if (v.parent == \"source\") {\n                        rPass = true;\n                    }\n                });\n                if ((rPass == false) && (\$(\"#c_server_tree\").is(\":checked\"))) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["select_at_least_one_server"];
echo "\");\n                }\n                \$(\"#streams\").val(JSON.stringify(window.rSelected));\n                if (window.rSelected.length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["select_at_least_one_stream_to_edit"];
echo "\");\n                }\n            });\n            \$(\"input[type=checkbox].activate\").change(function() {\n                if (\$(this).is(\":checked\")) {\n                    if (\$(this).data(\"type\") == \"switch\") {\n                        window.rSwitches[\$(this).data(\"name\")].enable();\n                    } else {\n                        \$(\"#\" + \$(this).data(\"name\")).prop(\"disabled\", false);\n                        if (\$(this).data(\"name\") == \"days_to_restart\") {\n                            \$(\"#time_to_restart\").prop(\"disabled\", false);\n                        }\n                    }\n                } else {\n                    if (\$(this).data(\"type\") == \"switch\") {\n                        window.rSwitches[\$(this).data(\"name\")].disable();\n                    } else {\n                        \$(\"#\" + \$(this).data(\"name\")).prop(\"disabled\", true);\n                        if (\$(this).data(\"name\") == \"days_to_restart\") {\n                            \$(\"#time_to_restart\").prop(\"disabled\", true);\n                        }\n                    }\n                }\n            });\n            \$(\".clockpicker\").clockpicker();\n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"#probesize_ondemand\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#delay_minutes\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#tv_archive_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n            rTable = \$(\"#datatable-mass\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"stream_list\",\n                        d.category = getCategory()\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0]}\n                ],\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rSelected) !== -1) {\n                        \$(row).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    }\n                },\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo "            });\n            \$('#stream_search').keyup(function(){\n                rTable.search(\$(this).val()).draw();\n            })\n            \$('#show_entries').change(function(){\n                rTable.page.len(\$(this).val()).draw();\n            })\n            \$('#category_search').change(function(){\n                rTable.ajax.reload(null, false);\n            })\n            \$(\"#datatable-mass\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rSelected.splice(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html(), window.rSelected), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rSelected.push(\$(ui.selected).find(\"td:eq(0)\").html());\n                    }\n                    \$(\"#selected_count\").html(\" - \" + window.rSelected.length + \" selected\")\n                }\n            });\n        });\n        </script>\n    </body>\n</html>";

?>