<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"]) {
    exit;
}
$rTranscodeProfiles = getTranscodeProfiles();
if (isset($_POST["submit_stream"]) && (isset($_POST["m3u_file"]) || isset($_POST["m3u_url"]))) {
    if ($_POST["category_id"] == 0) {
        $_POST["category_id"] = NULL;
    }
    if (isset($_POST["direct_source"])) {
        $_POST["direct_source"] = 1;
    } else {
        $_POST["direct_source"] = 0;
    }
    $rFile = "";
    if (!empty($_POST["m3u_url"])) {
        $rFile = file_get_contents($_POST["m3u_url"]);
    } else {
        if (!empty($_POST["m3u_file"])) {
            $rFile = $_POST["m3u_file"];
        }
    }
    preg_match_all("/(?P<tag>#EXTINF:)|(?:(?P<prop_key>[-a-z]+)=\\\"(?P<prop_val>[^\"]+)\")|(?<name>,[^\\r\\n]+)|(?<url>http[^\\s]+)/", $rFile, $rMatches);
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
                        $rResults[$rIndex]["url"] = trim($rItem);
                    }
                }
            }
        }
    }
    foreach ($rResults as $rResult) {
        $db->query("INSERT INTO `streams` (`type`, `category_id`, `stream_display_name`, `stream_source`, `stream_icon`, `notes`, `created_channel_location`, `enable_transcode`, `transcode_attributes`, `custom_ffmpeg`, `movie_propeties`, `movie_subtitles`, `read_native`, `target_container`, `stream_all`, `remove_subtitles`, `custom_sid`, `epg_id`, `channel_id`, `epg_lang`, `order`, `auto_restart`, `transcode_profile_id`, `pids_create_channel`, `cchannel_rsources`, `gen_timestamps`, `added`, `series_no`, `direct_source`, `tv_archive_duration`, `tv_archive_server_id`, `tv_archive_pid`, `movie_symlink`, `redirect_stream`, `rtmp_output`, `number`, `allow_record`, `probesize_ondemand`, `custom_map`, `external_push`, `delay_minutes`) VALUES (2,'" . $_POST["category_id"] . "',\t'" . $rResult["name"] . "',\t'[\"" . $rResult["url"] . "\"]',\t'" . $rResult["tvg-logo"] . "',\t'',\tNULL,\t0,\t'[]',\t'',\t'{\"movie_image\":\"" . $rResult["tvg-logo"] . "\",\"backdrop_path\":[],\"youtube_trailer\":\"\",\"genre\":\"\",\"plot\":\"\",\"cast\":\"\",\"rating\":\"\",\"director\":\"\",\"releasedate\":\"\",\"tmdb_id\":\"\"}',\t'',\t0,\t'[\"mp4\"]',\t0,\t0,\tNULL,\tNULL,\tNULL,\tNULL,\t0,\t'',\t0,\t'',\t'',\t1,\t" . time() . ",\t0,\t'" . $_POST["direct_source"] . "',\t0,\t0,\t0,\t'" . $_POST["direct_source"] . "',\t'" . $_POST["direct_source"] . "',\t0,\t0,\t0,\t128000,\t'',\t'',\t0);");
        $db->query("INSERT INTO `streams_sys` (`stream_id`, `server_id`, `parent_id`, `pid`, `to_analyze`, `stream_status`, `stream_started`, `stream_info`, `monitor_pid`, `current_source`, `bitrate`, `progress_info`, `on_demand`, `delay_pid`, `delay_available_at`) VALUES (" . $db->insert_id . ",\t'" . $_POST["servers"] . "',\tNULL,\tNULL,\t0,\t0,\tNULL,\t'',\tNULL,\tNULL,\t'',\t'',\t0,\tNULL,\tNULL);");
    }
    $_STATUS == 0;
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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n\t\t\t\t\t\t\t\t\t<li>\r\n                                        <a href=\"./movies.php";
if (isset($_GET["category"])) {
    echo "?category=" . $_GET["category"];
}
echo "\">\r\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_movies"];
echo "</button>\r\n\t\t\t\t\t\t\t\t\t    </a>\t\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
if (isset($rStream)) {
    echo "Edit";
} else {
    echo "Import";
}
echo " Movies</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        ";
if (isset($_POST["submit_stream"])) {
    echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            Movie importation was completed successfully.\r\n                        </div>\r\n                        ";
}
echo "\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\">\r\n                                <form action=\"./vod_import.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"stream_form\">\r\n                                    ";
if (isset($rStream)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rStream["id"];
    echo "\" />\r\n                                    ";
}
echo "                                    <input type=\"hidden\" name=\"server_tree_data\" id=\"server_tree_data\" value=\"\" />\r\n                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#stream-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">Details</span>\r\n                                                </a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#advanced-options\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\r\n                                                    <i class=\"mdi mdi-folder-alert-outline mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">Advanced</span>\r\n                                                </a>\r\n                                            </li>\r\n                                           \r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#load-balancing\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\r\n                                                    <i class=\"mdi mdi-server-network mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">Servers</span>\r\n                                                </a>\r\n                                            </li>\r\n                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n                                            <div class=\"tab-pane\" id=\"stream-details\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                            <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"m3u_url\">M3U URL</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"m3u_url\" name=\"m3u_url\" value=\"\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"m3u_file\">M3U Content</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <textarea style=\"width:100%; height:200px; border: 1px solid #ced4da;\" name=\"m3u_file\" id=\"m3u_file\" /></textarea>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_id\">Category Name</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"category_id\" id=\"category_id\" class=\"form-control\" data-toggle=\"select2\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=''>No Category</option>\r\n                                                                    ";
foreach (getCategories("movie") as $rCategory) {
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
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"movie_propeties\">Movie propriet</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" value ='{\"movie_image\":null,\"plot\":null,\"releasedate\":null,\"rating\":null}' class=\"form-control\" id=\"movie_propeties\" name=\"movie_propeties\" value=\"";
if (isset($rStream)) {
    echo $rStream["movie_propeties"];
}
echo "\">\r\n                                                            </div>\r\n                                                        </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"target_container\">Format Movie:</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" value ='[\"mp4\"]' class=\"form-control\" id=\"target_container\" name=\"target_container\" value=\"";
if (isset($rStream)) {
    echo $rStream["target_container"];
}
echo "\">\r\n                                                           </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"notes\">Notes</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <textarea id=\"notes\" name=\"notes\" class=\"form-control\" rows=\"3\" placeholder=\"\">";
if (isset($rStream)) {
    echo $rStream["notes"];
}
echo "</textarea>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"next list-inline-item float-right\">\r\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">Next</a>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n\r\n                                            <div class=\"tab-pane\" id=\"advanced-options\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"gen_timestamps\">Generate PTS <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Allow FFmpeg to generate presentation timestamps for you to achieve better synchronization with the stream codecs. In some streams this can cause de-sync.\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"gen_timestamps\" id=\"gen_timestamps\" type=\"checkbox\" ";
if (isset($rStream)) {
    if ($rStream["gen_timestamps"] == 1) {
        echo "checked ";
    }
} else {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"read_native\">Native Frames <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"You should always read live streams as non-native frames. However if you are streaming static video files, set this to true otherwise the encoding process will fail.\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"read_native\" id=\"read_native\" type=\"checkbox\" ";
if (isset($rStream) && $rStream["read_native"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_all\">Stream All Codecs <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Esta opcao transmitira todos os codecs do seu fluxo. Alguns fluxos tem mais de um canal de audio / video / legenda.\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"stream_all\" id=\"stream_all\" type=\"checkbox\" ";
if (isset($rStream) && $rStream["stream_all"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allow_record\">Allow Recording</label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"allow_record\" id=\"allow_record\" type=\"checkbox\" ";
if (isset($rStream) && $rStream["allow_record"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label style=\"display:none\" class=\"col-md-4 col-form-label\" for=\"rtmp_output\">Allow RTMP Output <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable RTMP output for this channel.\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div style=\"display:none\" class=\"col-md-2\">\r\n                                                                <input name=\"rtmp_output\" id=\"rtmp_output\" type=\"checkbox\" ";
if (isset($rStream) && $rStream["rtmp_output"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                           <label class=\"col-md-4 col-form-label\" for=\"direct_source\">Direct Source <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Don't run the source through Xtream codes, just redirect.\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"direct_source\" id=\"direct_source\" type=\"checkbox\" ";
if (isset($rStream) && $rStream["direct_source"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div style=\"display:none\" class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"custom_sid\">Custom Channel SID <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Here you can specify the SID of the channel in order to work with the epg on the enigma2 devices. You have to specify the code with the ':' but without the first number, 1 or 4097 . Example: if we have this code:  '1:0:1:13f:157c:13e:820000:0:0:0:2097' then you have to add on this field:  ':0:1:13f:157c:13e:820000:0:0:0:\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"custom_sid\" name=\"custom_sid\" value=\"";
if (isset($rStream)) {
    echo $rStream["custom_sid"];
}
echo "\">\r\n                                                            </div>\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"delay_minutes\">Minute Delay <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Atraso no fluxo em X minutos. Não funcionará com fluxos sob demanda.\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"delay_minutes\" name=\"delay_minutes\" value=\"";
if (isset($rStream)) {
    echo $rStream["delay_minutes"];
} else {
    echo "0";
}
echo "\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"custom_ffmpeg\">Custom FFmpeg Command <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"In this field you can write your own custom FFmpeg command. Please note that this command will be placed after the input and before the output. If the command you will specify here is about to do changes in the output video or audio, it may require to transcode the stream. In this case, you have to use and change at least the Video/Audio Codecs using the transcoding attributes below. The custom FFmpeg command will only be used by the server(s) that take the stream from the Source.\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"custom_ffmpeg\" name=\"custom_ffmpeg\" value=\"";
if (isset($rStream)) {
    echo $rStream["custom_ffmpeg"];
}
echo "\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"user_agent\">User Agent</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"user_agent\" name=\"user_agent\" value=\"";
if (isset($rStreamOptions[1])) {
    echo $rStreamOptions[1]["value"];
} else {
    echo $rStreamArguments["user_agent"]["argument_default_value"];
}
echo "\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"http_proxy\">HTTP Proxy <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Format: ip:port\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"http_proxy\" name=\"http_proxy\" value=\"";
if (isset($rStreamOptions[2])) {
    echo $rStreamOptions[1]["value"];
} else {
    echo $rStreamArguments["proxy"]["argument_default_value"];
}
echo "\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"transcode_profile_id\">Transcoding Profile <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Sometimes, in order to make a stream compatible with most devices, it must be transcoded. Please note that the transcode will only be applied to the server(s) that take the stream directly from the source, all other servers attached to the transcoding server will not transcode the stream.\" class=\"mdi mdi-information\"></i></label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"transcode_profile_id\" id=\"transcode_profile_id\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                                    <option ";
if (isset($rStream) && intval($rStream["transcode_profile_id"]) == 0) {
    echo "selected ";
}
echo "value=\"0\">Transcoding Disabled</option>\r\n                                                                    ";
foreach ($rTranscodeProfiles as $rProfile) {
    echo "                                                                    <option ";
    if (isset($rStream) && intval($rStream["transcode_profile_id"]) == intval($rProfile["profile_id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rProfile["profile_id"];
    echo "\">";
    echo $rProfile["profile_name"];
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"previous list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">Previous</a>\r\n                                                    </li>\r\n                                                    <li class=\"next list-inline-item float-right\">\r\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">Next</a>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            \r\n                                           \r\n                                            \r\n                                            \r\n                                            <div class=\"tab-pane\" id=\"load-balancing\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n\t                                                        <div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"servers\">Server Tree</label>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<select required name=\"servers\" id=\"servers\" class=\"form-control\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"0\" disabled selected>Choose one Server</option>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
foreach (getStreamingServers() as $indice => $valor) {
    if ($valor["status"] == 2) {
        $disabled = "disabled";
    }
    echo "<option " . $disabled . " value=\"" . $valor["id"] . "\">" . $valor["server_name"] . "</option>";
}
echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</select>\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n                                                        \r\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tv_archive_server_id\">Timeshift Server</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"tv_archive_server_id\" id=\"tv_archive_server_id\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                                    <option value=\"\">Timeshift Disabled</option>\r\n                                                                    ";
foreach ($rServers as $rServer) {
    echo "                                                                    <option value=\"";
    echo $rServer["id"];
    echo "\"";
    if (isset($rStream) && $rStream["tv_archive_server_id"] == $rServer["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rServer["server_name"];
    echo "</option>\r\n                                                                    ";
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tv_archive_duration\">Timeshift Days</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"tv_archive_duration\" name=\"tv_archive_duration\" value=\"";
if (isset($rStream)) {
    echo $rStream["tv_archive_duration"];
} else {
    echo "0";
}
echo "\">\r\n                                                                \r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"previous list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">Previous</a>\r\n                                                    </li>\r\n                                                    <li class=\"next list-inline-item float-right\">\r\n                                                        <input name=\"submit_stream\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rStream)) {
    echo "Edit";
} else {
    echo "Add";
}
echo "\" />\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n\r\n\r\n                                        </div> <!-- tab-content -->\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </form>\r\n\r\n                            </div> <!-- end card-body -->\r\n                        </div> <!-- end card-->\r\n                    </div> <!-- end col -->\r\n                </div>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n\r\n        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <!-- Vendor js -->\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n\r\n        <!-- Plugins js-->\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n\r\n        <!-- Tree view js -->\r\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n\r\n        <!-- App js-->\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        \r\n        <script>\r\n        var rEPG = ";
echo json_encode($rEPGJS);
echo ";\r\n        \r\n        (function(\$) {\r\n          \$.fn.inputFilter = function(inputFilter) {\r\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\r\n              if (inputFilter(this.value)) {\r\n                this.oldValue = this.value;\r\n                this.oldSelectionStart = this.selectionStart;\r\n                this.oldSelectionEnd = this.selectionEnd;\r\n              } else if (this.hasOwnProperty(\"oldValue\")) {\r\n                this.value = this.oldValue;\r\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\r\n              }\r\n            });\r\n          };\r\n        }(jQuery));\r\n        \r\n        function addStream() {\r\n            \$(\".stream-url:first\").clone().appendTo(\".streams\");\r\n            \$(\".stream-url:last label\").html(\"Stream URL\");\r\n            \$(\".stream-url:last input\").val(\"\");\r\n        }\r\n        function removeStream(rField) {\r\n            if (\$('.stream-url').length > 1) {\r\n                \$(rField).parent().parent().parent().remove();\r\n            } else {\r\n                \$(rField).parent().parent().find(\"#stream_source\").val(\"\");\r\n            }\r\n        }\r\n        function selectEPGSource() {\r\n            \$(\"#channel_id\").empty();\r\n            \$(\"#epg_lang\").empty();\r\n            if (rEPG[\$(\"#epg_id\").val()]) {\r\n                \$.each(rEPG[\$(\"#epg_id\").val()], function(key, data) {\r\n                    \$(\"#channel_id\").append(new Option(data[\"display_name\"], key, false, false));\r\n                });\r\n                selectEPGID();\r\n            }\r\n        }\r\n        function selectEPGID() {\r\n            \$(\"#epg_lang\").empty();\r\n            if (rEPG[\$(\"#epg_id\").val()][\$(\"#channel_id\").val()]) {\r\n                \$.each(rEPG[\$(\"#epg_id\").val()][\$(\"#channel_id\").val()][\"langs\"], function(i, data) {\r\n                    \$(\"#epg_lang\").append(new Option(data, data, false, false));\r\n                });\r\n            }\r\n        }\r\n        function reloadStream() {\r\n            \$(\"#datatable\").DataTable().ajax.reload( null, false );\r\n            setTimeout(reloadStream, 5000);\r\n        }\r\n        function api(rID, rServerID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('Are you sure you want to delete this stream?') == false) {\r\n                    return;\r\n                }\r\n            }\r\n            \$.getJSON(\"./api.php?action=vod&sub=\" + rType + \"&stream_id=\" + rID + \"&server_id=\" + rServerID, function(data) {\r\n                if (data.result == true) {\r\n                    if (rType == \"start\") {\r\n                        \$.toast(\"Stream successfully started. It will take a minute or so before the stream becomes available.\");\r\n                    } else if (rType == \"stop\") {\r\n                        \$.toast(\"Stream successfully stopped.\");\r\n                    } else if (rType == \"restart\") {\r\n                        \$.toast(\"Stream successfully restarted. It will take a minute or so before the stream becomes available.\");\r\n                    } else if (rType == \"delete\") {\r\n                        \$(\"#stream-\" + rID + \"-\" + rServerID).remove();\r\n                        \$.toast(\"Stream successfully deleted.\");\r\n                    }\r\n                    \$(\"#datatable\").DataTable().ajax.reload( null, false );\r\n                } else {\r\n                    \$.toast(\"An error occured while processing your request.\");\r\n                }\r\n            }).fail(function() {\r\n                \$.toast(\"An error occured while processing your request.\");\r\n            });\r\n        }\r\n        \$(document).ready(function() {\r\n            \$('select').select2({width: '100%'}) \r\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\r\n            elems.forEach(function(html) {\r\n              var switchery = new Switchery(html);\r\n            });\r\n            \$(\"#epg_id\").on(\"select2:select\", function(e) { \r\n                selectEPGSource();\r\n            });\r\n            \$(\"#channel_id\").on(\"select2:select\", function(e) { \r\n                selectEPGID();\r\n            });\r\n            \r\n            \$(\".clockpicker\").clockpicker();\r\n            \r\n            \$('#server_tree').jstree({ 'core' : {\r\n                'check_callback': function (op, node, parent, position, more) {\r\n                    switch (op) {\r\n                        case 'move_node':\r\n                            if (node.id == \"source\") { return false; }\r\n                            return true;\r\n                    }\r\n                },\r\n                'data' : ";
echo json_encode($rServerTree);
echo "            }, \"plugins\" : [ \"dnd\" ]\r\n            });\r\n\r\n            \$(document).keypress(function(event){\r\n                if (event.which == '13') {\r\n                    event.preventDefault();\r\n                }\r\n            });\r\n                   if ((\$(\"#m3u_file\").val().length == 0) && (\$(\"#m3u_url\").val().length == 0)) {\r\n                    e.preventDefault();\r\n                    \$.toast(\"Please select a M3U file to upload or enter an URL.\");\r\n                }         \r\n            \$(\"#delay_minutes\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\r\n            \$(\"#tv_archive_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\r\n            \$(\"form\").attr('autocomplete', 'off');\r\n            ";
if (isset($rStream)) {
    echo "            \$(\"#datatable\").DataTable({\r\n                ordering: false,\r\n                paging: false,\r\n                searching: false,\r\n                processing: true,\r\n                serverSide: true,\r\n                bInfo: false,\r\n                ajax: {\r\n                    url: \"./table.php\",\r\n                    \"data\": function(d) {\r\n                        d.id = \"vods\",\r\n                        d.vod_id = \"";
    echo $rStream["id"];
    echo "\"\r\n                    }\r\n                },\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [3,4,5,6]},\r\n                    {\"visible\": false, \"targets\": [0,1,2,7,8]}\r\n                ],\r\n            });\r\n            setTimeout(reloadStream, 5000);\r\n            ";
}
echo "        });\r\n        </script>\r\n    </body>\r\n</html>";

?>