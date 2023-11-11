<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "tprofile")) {
    exit;
}
if (isset($_POST["submit_profile"])) {
    $rArray = ["profile_name" => $_POST["profile_name"], "profile_options" => NULL];
    $rProfileOptions = [];
    if (0 < strlen($_POST["video_codec"])) {
        $rProfileOptions["-vcodec"] = $_POST["video_codec"];
    }
    if (0 < strlen($_POST["audio_codec"])) {
        $rProfileOptions["-acodec"] = $_POST["audio_codec"];
    }
    if (0 < strlen($_POST["preset"])) {
        $rProfileOptions["-preset"] = $_POST["preset"];
    }
    if (0 < strlen($_POST["video_profile"])) {
        $rProfileOptions["-profile:v"] = $_POST["video_profile"];
    }
    if (0 < strlen($_POST["video_bitrate"])) {
        $rProfileOptions[3] = ["cmd" => "-b:v " . intval($_POST["video_bitrate"]) . "k", "val" => intval($_POST["video_bitrate"])];
    }
    if (0 < strlen($_POST["audio_bitrate"])) {
        $rProfileOptions[4] = ["cmd" => "-b:a " . intval($_POST["audio_bitrate"]) . "k", "val" => intval($_POST["audio_bitrate"])];
    }
    if (0 < strlen($_POST["min_tolerance"])) {
        $rProfileOptions[5] = ["cmd" => "-minrate " . intval($_POST["min_tolerance"]) . "k", "val" => intval($_POST["min_tolerance"])];
    }
    if (0 < strlen($_POST["max_tolerance"])) {
        $rProfileOptions[6] = ["cmd" => "-maxrate " . intval($_POST["max_tolerance"]) . "k", "val" => intval($_POST["max_tolerance"])];
    }
    if (0 < strlen($_POST["buffer_size"])) {
        $rProfileOptions[7] = ["cmd" => "-bufsize " . intval($_POST["buffer_size"]) . "k", "val" => intval($_POST["buffer_size"])];
    }
    if (0 < strlen($_POST["crf_value"])) {
        $rProfileOptions[8] = ["cmd" => "-crf " . $_POST["crf_value"], "val" => $_POST["crf_value"]];
    }
    if (0 < strlen($_POST["scaling"])) {
        $rProfileOptions[9] = ["cmd" => "-vf scale=" . $_POST["scaling"], "val" => $_POST["scaling"]];
    }
    if (0 < strlen($_POST["aspect_ratio"])) {
        $rProfileOptions[10] = ["cmd" => "-aspect " . $_POST["aspect_ratio"], "val" => $_POST["aspect_ratio"]];
    }
    if (0 < strlen($_POST["framerate"])) {
        $rProfileOptions[11] = ["cmd" => "-r " . intval($_POST["framerate"]), "val" => intval($_POST["framerate"])];
    }
    if (0 < strlen($_POST["samplerate"])) {
        $rProfileOptions[12] = ["cmd" => "-ar " . intval($_POST["samplerate"]), "val" => intval($_POST["samplerate"])];
    }
    if (0 < strlen($_POST["audio_channels"])) {
        $rProfileOptions[13] = ["cmd" => "-ac " . intval($_POST["audio_channels"]), "val" => intval($_POST["audio_channels"])];
    }
    if (0 < strlen($_POST["remove_parts"])) {
        $rProfileOptions[14] = ["cmd" => "-vf delogo=" . $_POST["remove_parts"], "val" => $_POST["remove_parts"]];
    }
    if (0 < strlen($_POST["threads"])) {
        $rProfileOptions[15] = ["cmd" => "-threads " . intval($_POST["threads"]), "val" => intval($_POST["threads"])];
    }
    if (0 < strlen($_POST["logo_path"])) {
        $rProfileOptions[16] = ["cmd" => "-i \"" . $_POST["logo_path"] . "\" -filter_complex \"overlay\"", "val" => $_POST["logo_path"]];
    }
    $rArray["profile_options"] = json_encode($rProfileOptions);
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
        if (!hasPermissions("adv", "edit_tprofile")) {
            exit;
        }
        $rCols = "profile_id," . $rCols;
        $rValues = ESC($_POST["edit"]) . "," . $rValues;
    }
    $rQuery = "REPLACE INTO `transcoding_profiles`(" . $rCols . ") VALUES(" . $rValues . ");";
    if ($db->query($rQuery)) {
        if (isset($_POST["edit"])) {
            $rInsertID = intval($_POST["edit"]);
        } else {
            $rInsertID = $db->insert_id;
        }
    }
    if (isset($rInsertID)) {
        header("Location: ./profiles.php");
        exit;
    }
    $_STATUS = 1;
}
if (isset($_GET["id"])) {
    $rProfileArr = getTranscodeProfile($_GET["id"]);
    if (!$rProfileArr || !hasPermissions("adv", "edit_tprofile")) {
        exit;
    }
    $rProfileOptions = json_decode($rProfileArr["profile_options"], true);
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./profiles.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_profiles"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rProfileArr)) {
    echo $_["edit_profile"];
} else {
    echo $_["add_profile"];
}
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["profile_success"];
    echo "                        </div>\n                        ";
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["generic_fail"];
        echo "                        </div>\n                        ";
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./profile.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"profile_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rProfileArr)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rProfileArr["profile_id"];
    echo "\" />\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#profile-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"profile-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"profile_name\">";
echo $_["profile_name"];
echo "</label>\n                                                            <div class=\"col-md-9\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"profile_name\" name=\"profile_name\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileArr["profile_name"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"video_codec\">";
echo $_["video_codec"];
echo "</label>\n                                                            <div class=\"col-md-3\">\n                                                                <select id=\"video_codec\" name=\"video_codec\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach (["copy" => "Copy Codec (Associated Transcoding Options Will not work)", "apng" => "APNG (Animated Portable Network Graphics) image", "cavs" => "Chinese AVS (Audio Video Standard) (AVS1-P2, JiZhun profile) (encoders: libxavs)", "cinepak" => "Cinepak", "ffv1" => "FFmpeg video codec #1", "flashsv" => "Flash Screen Video v1", "flashsv2" => "Flash Screen Video v2", "flv1" => "FLV / Sorenson Spark / Sorenson H.263 (Flash Video) (decoders: flv) (encoders: flv)", "gif" => "GIF (Graphics Interchange Format)", "h261" => "H.261", "h263" => "H.263 / H.263-1996, H.263+ / H.263-1998 / H.263 version 2", "h263p" => "H.263+ / H.263-1998 / H.263 version 2", "h264" => "H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10 (decoders: h264 h264_cuvid ) (encoders: libx264 libx264rgb h264_nvenc nvenc nvenc_h264 )", "hevc" => "H.265 / HEVC (High Efficiency Video Coding) (decoders: hevc hevc_cuvid ) (encoders: libx265 nvenc_hevc hevc_nvenc )", "mpeg1video" => " MPEG-1 video (decoders: mpeg1video mpeg1_cuvid )", "mpeg2video" => "MPEG-2 video (decoders: mpeg2video mpegvideo mpeg2_cuvid )", "mpeg4" => "MPEG-4 part 2 (decoders: mpeg4 mpeg4_cuvid ) (encoders: mpeg4 libxvid )", "msmpeg4v2" => "MPEG-4 part 2 Microsoft variant version 2", "msmpeg4v3" => "MPEG-4 part 2 Microsoft variant version 3 (decoders: msmpeg4) (encoders: msmpeg4)", "msvideo1" => "Microsoft Video 1", "png" => "PNG (Portable Network Graphics) image", "qtrle" => "QuickTime Animation (RLE) video", "roq" => "id RoQ video (decoders: roqvideo) (encoders: roqvideo)", "rv10" => "RealVideo 1.0", "rv20" => "RealVideo 2.0", "snow" => "Snow", "svq1" => "Sorenson Vector Quantizer 1 / Sorenson Video 1 / SVQ1", "theora" => "Theora (encoders: libtheora)", "vp8" => "On2 VP8 (decoders: vp8 libvpx) (encoders: libvpx)", "vp9" => "Google VP9 (decoders: vp9 libvpx-vp9) (encoders: libvpx-vp9)", "wmv1" => "Windows Media Video 7", "wmv2" => "Windows Media Video 8", "zmbv" => "Zip Motion Blocks Video"] as $rCodec => $rCodecName) {
    echo "                                                                    <option ";
    if (isset($rProfileArr) && $rProfileOptions["-vcodec"] == $rCodec) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rCodec;
    echo "\">";
    echo $rCodec;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"audio_codec\">";
echo $_["audio_codec"];
echo "</label>\n                                                            <div class=\"col-md-3\">\n                                                                <select id=\"audio_codec\" name=\"audio_codec\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach (["copy" => "Copy Codec (Associated Transcoding Options Will not work)", "aac" => "AAC (Advanced Audio Coding) (decoders: aac aac_fixed)", "ac3" => "ATSC A/52A (AC-3) (decoders: ac3 ac3_fixed) (encoders: ac3 ac3_fixed)", "adpcm_adx" => "SEGA CRI ADX ADPCM", "adpcm_g722" => "G.722 ADPCM (decoders: g722) (encoders: g722)", "adpcm_g726" => "G.726 ADPCM (decoders: g726) (encoders: g726)", "adpcm_ima_qt" => "ADPCM IMA QuickTime", "adpcm_ima_wav" => "ADPCM IMA WAV", "adpcm_ms" => "ADPCM Microsoft", "adpcm_swf" => "ADPCM Shockwave Flash", "adpcm_yamaha" => "ADPCM Yamaha", "comfortnoise" => "RFC 3389 Comfort Noise", "dts" => "DCA (DTS Coherent Acoustics) (decoders: dca) (encoders: dca)", "eac3" => "ATSC A/52B (AC-3, E-AC-3)", "g723_1" => "G.723.1", "mp2" => "MP2 (MPEG audio layer 2) (decoders: mp2 mp2float) (encoders: mp2 mp2fixed)", "mp3" => "MP3 (MPEG audio layer 3) (decoders: mp3 mp3float) (encoders: libmp3lame)", "nellymoser" => "Nellymoser Asao", "opus" => "Opus (Opus Interactive Audio Codec) (decoders: opus libopus) (encoders: libopus)", "pcm_alaw" => "PCM A-law / G.711 A-law", "pcm_mulaw" => "PCM mu-law / G.711 mu-law", "ra_144" => "RealAudio 1.0 (14.4K) (decoders: real_144) (encoders: real_144)", "roq_dpcm" => "DPCM id RoQ", "vorbis" => "Vorbis (decoders: vorbis libvorbis) (encoders: vorbis libvorbis)", "wavpack" => "WavPack", "wmav1" => "Windows Media Audio 1", "wmav2" => "Windows Media Audio 2"] as $rCodec => $rCodecName) {
    echo "                                                                    <option ";
    if (isset($rProfileArr) && $rProfileOptions["-acodec"] == $rCodec) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rCodec;
    echo "\">";
    echo $rCodec;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"preset\">";
echo $_["preset"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_1"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <select id=\"preset\" name=\"preset\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach (["" => "Default", "ultrafast" => "Ultra Fast", "superfast" => "Super Fast", "veryfast" => "Very Fast", "faster" => "Faster", "fast" => "Fast", "slow" => "Slow", "slower" => "Slower", "veryslow" => "Very Slow", "placebo" => "Placebo"] as $rPreset => $rPresetName) {
    echo "                                                                    <option ";
    if (isset($rProfileArr) && $rProfileOptions["-preset"] == $rPreset) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rPreset;
    echo "\">";
    echo $rPresetName;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"video_profile\">";
echo $_["video_profile"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_2"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <select id=\"video_profile\" name=\"video_profile\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach (["" => "Don't Use Profile", "baseline -level 3.0" => "Baseline - Level 3.0", "baseline -level 3.1" => "Baseline - Level 3.1", "main -level 3.1" => "Main - Level 3.1", "main -level 4.0" => "Main - Level 4.0", "high -level 4.0" => "High - Level 4.0", "high -level 4.1" => "High - Level 4.1", "high -level 4.2" => "High - Level 4.2"] as $rPreset => $rPresetName) {
    echo "                                                                    <option ";
    if (isset($rProfileArr) && $rProfileOptions["-profile:v"] == $rPreset) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rPreset;
    echo "\">";
    echo $rPresetName;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"video_bitrate\">";
echo $_["average_video_bitrate"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_3"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"video_bitrate\" name=\"video_bitrate\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[3]["val"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"audio_bitrate\">";
echo $_["average_audio_bitrate"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_4"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"audio_bitrate\" name=\"audio_bitrate\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[4]["val"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"min_tolerance\">";
echo $_["minimum_bitrate_tolerance"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_5"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"min_tolerance\" name=\"min_tolerance\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[5]["val"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"max_tolerance\">";
echo $_["maximum_bitrate_tolerance"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_6"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"max_tolerance\" name=\"max_tolerance\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[6]["val"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"buffer_size\">";
echo $_["buffer_size"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_7"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"buffer_size\" name=\"buffer_size\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[7]["val"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"crf_value\">";
echo $_["crf_value"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_8"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"crf_value\" name=\"crf_value\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[8]["val"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"scaling\">";
echo $_["scaling"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_9"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"scaling\" name=\"scaling\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[9]["val"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"aspect_ratio\">";
echo $_["aspect_ratio"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_10"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"aspect_ratio\" name=\"aspect_ratio\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[10]["val"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"framerate\">";
echo $_["target_framerate"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_11"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"framerate\" name=\"framerate\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[11]["val"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"samplerate\">";
echo $_["audio_sample_rate"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_12"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"samplerate\" name=\"samplerate\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[12]["val"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"audio_channels\">";
echo $_["audio_channels"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_13"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"audio_channels\" name=\"audio_channels\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[13]["val"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"threads\">";
echo $_["threads"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_14"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"threads\" name=\"threads\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[15]["val"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"remove_parts\">";
echo $_["remove_sensitive_parts"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_15"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-9\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"remove_parts\" name=\"remove_parts\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[14]["val"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-3 col-form-label\" for=\"logo_path\">";
echo $_["logo_path_url"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["profile_tooltip_16"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-9\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"logo_path\" name=\"logo_path\" value=\"";
if (isset($rProfileArr)) {
    echo htmlspecialchars($rProfileOptions[16]["val"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_profile\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rProfileArr)) {
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
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'})\n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"form\").attr('autocomplete', 'off');\n            \n            \$(\"#video_bitrate\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#audio_bitrate\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#min_tolerance\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#max_tolerance\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#buffer_size\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#framerate\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#samplerate\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#audio_channels\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#threads\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n        });\n        </script>\n    </body>\n</html>";

?>