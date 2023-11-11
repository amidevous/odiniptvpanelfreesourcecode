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
if (!hasPermissions("adv", "settings") && !hasPermissions("adv", "database")) {
    exit;
}
$rTMDBLanguages = ["" => "Default - EN", "aa" => "Afar", "af" => "Afrikaans", "ak" => "Akan", "an" => "Aragonese", "as" => "Assamese", "av" => "Avaric", "ae" => "Avestan", "ay" => "Aymara", "az" => "Azerbaijani", "ba" => "Bashkir", "bm" => "Bambara", "bi" => "Bislama", "bo" => "Tibetan", "br" => "Breton", "ca" => "Catalan", "cs" => "Czech", "ce" => "Chechen", "cu" => "Slavic", "cv" => "Chuvash", "kw" => "Cornish", "co" => "Corsican", "cr" => "Cree", "cy" => "Welsh", "da" => "Danish", "de" => "German", "dv" => "Divehi", "dz" => "Dzongkha", "eo" => "Esperanto", "et" => "Estonian", "eu" => "Basque", "fo" => "Faroese", "fj" => "Fijian", "fi" => "Finnish", "fr" => "French", "fy" => "Frisian", "ff" => "Fulah", "gd" => "Gaelic", "ga" => "Irish", "gl" => "Galician", "gv" => "Manx", "gn" => "Guarani", "gu" => "Gujarati", "ht" => "Haitian", "ha" => "Hausa", "sh" => "Serbo-Croatian", "hz" => "Herero", "ho" => "Hiri Motu", "hr" => "Croatian", "hu" => "Hungarian", "ig" => "Igbo", "io" => "Ido", "ii" => "Yi", "iu" => "Inuktitut", "ie" => "Interlingue", "ia" => "Interlingua", "id" => "Indonesian", "ik" => "Inupiaq", "is" => "Icelandic", "it" => "Italian", "ja" => "Japanese", "kl" => "Kalaallisut", "kn" => "Kannada", "ks" => "Kashmiri", "kr" => "Kanuri", "kk" => "Kazakh", "km" => "Khmer", "ki" => "Kikuyu", "rw" => "Kinyarwanda", "ky" => "Kirghiz", "kv" => "Komi", "kg" => "Kongo", "ko" => "Korean", "kj" => "Kuanyama", "ku" => "Kurdish", "lo" => "Lao", "la" => "Latin", "lv" => "Latvian", "li" => "Limburgish", "ln" => "Lingala", "lt" => "Lithuanian", "lb" => "Letzeburgesch", "lu" => "Luba-Katanga", "lg" => "Ganda", "mh" => "Marshall", "ml" => "Malayalam", "mr" => "Marathi", "mg" => "Malagasy", "mt" => "Maltese", "mo" => "Moldavian", "mn" => "Mongolian", "mi" => "Maori", "ms" => "Malay", "my" => "Burmese", "na" => "Nauru", "nv" => "Navajo", "nr" => "Ndebele", "nd" => "Ndebele", "ng" => "Ndonga", "ne" => "Nepali", "nl" => "Dutch", "nn" => "Norwegian Nynorsk", "nb" => "Norwegian Bokmal", "no" => "Norwegian", "ny" => "Chichewa", "oc" => "Occitan", "oj" => "Ojibwa", "or" => "Oriya", "om" => "Oromo", "os" => "Ossetian; Ossetic", "pi" => "Pali", "pl" => "Polish", "pt" => "Portuguese", "pt-BR" => "Portuguese - Brazil", "qu" => "Quechua", "rm" => "Raeto-Romance", "ro" => "Romanian", "rn" => "Rundi", "ru" => "Russian", "sg" => "Sango", "sa" => "Sanskrit", "si" => "Sinhalese", "sk" => "Slovak", "sl" => "Slovenian", "se" => "Northern Sami", "sm" => "Samoan", "sn" => "Shona", "sd" => "Sindhi", "so" => "Somali", "st" => "Sotho", "es" => "Spanish", "es-MX" => "Spanish - Latin America", "sq" => "Albanian", "sc" => "Sardinian", "sr" => "Serbian", "ss" => "Swati", "su" => "Sundanese", "sw" => "Swahili", "sv" => "Swedish", "ty" => "Tahitian", "ta" => "Tamil", "tt" => "Tatar", "te" => "Telugu", "tg" => "Tajik", "tl" => "Tagalog", "th" => "Thai", "ti" => "Tigrinya", "to" => "Tonga", "tn" => "Tswana", "ts" => "Tsonga", "tk" => "Turkmen", "tr" => "Turkish", "tw" => "Twi", "ug" => "Uighur", "uk" => "Ukrainian", "ur" => "Urdu", "uz" => "Uzbek", "ve" => "Venda", "vi" => "Vietnamese", "vo" => "Volapük", "wa" => "Walloon", "wo" => "Wolof", "xh" => "Xhosa", "yi" => "Yiddish", "za" => "Zhuang", "zu" => "Zulu", "ab" => "Abkhazian", "zh" => "Mandarin", "ps" => "Pushto", "am" => "Amharic", "ar" => "Arabic", "bg" => "Bulgarian", "cn" => "Cantonese", "mk" => "Macedonian", "el" => "Greek", "fa" => "Persian", "he" => "Hebrew", "hi" => "Hindi", "hy" => "Armenian", "en" => "English", "ee" => "Ewe", "ka" => "Georgian", "pa" => "Punjabi", "bn" => "Bengali", "bs" => "Bosnian", "ch" => "Chamorro", "be" => "Belarusian", "yo" => "Yoruba"];
$rMAGs = ["MAG200", "MAG245", "MAG245D", "MAG250", "MAG254", "MAG255", "MAG256", "MAG257", "MAG260", "MAG270", "MAG275", "MAG322", "MAG322w1", "MAG322w2", "MAG323", "MAG324", "MAG324C", "MAG324w2", "MAG325", "MAG349", "MAG350", "MAG351", "MAG352", "MAG420", "MAG420w1", "MAG420w2", "MAG422", "MAG422A", "MAG422Aw1", "MAG424", "MAG424w1", "MAG424w2", "MAG424w3", "MAG424A", "MAG424Aw3", "MAG425", "MAG425A", "MAG520", "MAG520W1", "MAG520W2", "MAG520W3", "MAG520A", "MAG520Aw3", "MAG522W1", "MAG522W2", "MAG522W3", "MAG524", "MAG524W3", "AuraHD", "AuraHD0", "AuraHD1", "AuraHD2", "AuraHD3", "AuraHD4", "AuraHD5", "AuraHD6", "AuraHD7", "AuraHD8", "AuraHD9", "WR320", "IM2100", "IM2100w1", "IM2100V", "IM2100VI", "IM2101", "IM2101V", "IM2101VI", "IM2101VO", "IM2101w2", "IM2102", "IM4410", "IM4410w3", "IM4411", "IM4411w1", "IM4412", "IM4414", "IM4414w1", "IP_STB_HD"];
if (isset($_GET["geolite2"])) {
    if (updateGeoLite2()) {
        $_STATUS = 3;
    } else {
        $_STATUS = 2;
    }
}
if (isset($_GET["panel_version"])) {
    if (updatePanel()) {
        $_STATUS = 5;
    } else {
        $_STATUS = 4;
    }
}
if (isset($_POST["submit_settings"]) && hasPermissions("adv", "settings")) {
    $rArray = getSettings();
    foreach (["disallow_empty_user_agents", "persistent_connections", "show_all_category_mag", "show_not_on_air_video", "show_banned_video", "show_expired_video", "new_sorting_bouquet", "rtmp_random", "use_buffer", "audio_restart_loss", "save_closed_connection", "client_logs_save", "case_sensitive_line", "county_override_1st", "disallow_2nd_ip_con", "firewall", "use_mdomain_in_lists", "hash_lb", "show_isps", "enable_isp_lock", "block_svp", "mag_security", "always_enabled_subtitles", "enable_connection_problem_indication", "show_tv_channel_logo", "show_channel_logo_in_preview", "allowed_stb_types_rec", "stalker_lock_images", "portal_block", "stb_change_pass", "enable_debug_stalker", "priority_backup"] as $rSetting) {
        if (isset($_POST[$rSetting])) {
            $rArray[$rSetting] = 1;
            unset($_POST[$rSetting]);
        } else {
            $rArray[$rSetting] = 0;
        }
    }
    if (!isset($_POST["allowed_stb_types_for_local_recording"])) {
        $rArray["allowed_stb_types_for_local_recording"] = [];
    }
    if (!isset($_POST["allowed_stb_types"])) {
        $rArray["allowed_stb_types"] = [];
    }
    if (isset($_POST["disable_trial"])) {
        $rAdminSettings["disable_trial"] = true;
        unset($_POST["disable_trial"]);
    } else {
        $rAdminSettings["disable_trial"] = false;
    }
    if (isset($_POST["reseller_mag_events"])) {
        $rAdminSettings["reseller_mag_events"] = true;
        unset($_POST["reseller_mag_events"]);
    } else {
        $rAdminSettings["reseller_mag_events"] = false;
    }
    if (isset($_POST["ip_logout"])) {
        $rAdminSettings["ip_logout"] = true;
        unset($_POST["ip_logout"]);
    } else {
        $rAdminSettings["ip_logout"] = false;
    }
    if (isset($_POST["alternate_scandir"])) {
        $rAdminSettings["alternate_scandir"] = true;
        unset($_POST["alternate_scandir"]);
    } else {
        $rAdminSettings["alternate_scandir"] = false;
    }
    if (isset($_POST["show_tickets"])) {
        $rAdminSettings["show_tickets"] = true;
        unset($_POST["show_tickets"]);
    } else {
        $rAdminSettings["show_tickets"] = false;
    }
    if (isset($_POST["tmdb_http_enable"])) {
        $rAdminSettings["tmdb_http_enable"] = true;
        unset($_POST["tmdb_http_enable"]);
    } else {
        $rAdminSettings["tmdb_http_enable"] = false;
    }
    if (isset($_POST["recaptcha_enable"])) {
        $rAdminSettings["recaptcha_enable"] = true;
        unset($_POST["recaptcha_enable"]);
    } else {
        $rAdminSettings["recaptcha_enable"] = false;
    }
    if (isset($_POST["download_images"])) {
        $rAdminSettings["download_images"] = true;
        unset($_POST["download_images"]);
    } else {
        $rAdminSettings["download_images"] = false;
    }
    if (isset($_POST["auto_refresh"])) {
        $rAdminSettings["auto_refresh"] = true;
        unset($_POST["auto_refresh"]);
    } else {
        $rAdminSettings["auto_refresh"] = false;
    }
    if (isset($_POST["local_api"])) {
        $rAdminSettings["local_api"] = true;
        unset($_POST["local_api"]);
    } else {
        $rAdminSettings["local_api"] = false;
    }
    if (isset($_POST["dark_mode_login"])) {
        $rAdminSettings["dark_mode_login"] = true;
        unset($_POST["dark_mode_login"]);
    } else {
        $rAdminSettings["dark_mode_login"] = false;
    }
    if (isset($_POST["dashboard_stats"])) {
        $rAdminSettings["dashboard_stats"] = true;
        unset($_POST["dashboard_stats"]);
    } else {
        $rAdminSettings["dashboard_stats"] = false;
    }
    if (isset($_POST["dashboard_world_map_live"])) {
        $rAdminSettings["dashboard_world_map_live"] = true;
        unset($_POST["dashboard_world_map_live"]);
    } else {
        $rAdminSettings["dashboard_world_map_live"] = false;
    }
    if (isset($_POST["dashboard_world_map_activity"])) {
        $rAdminSettings["dashboard_world_map_activity"] = true;
        unset($_POST["dashboard_world_map_activity"]);
    } else {
        $rAdminSettings["dashboard_world_map_activity"] = false;
    }
    if (isset($_POST["active_statistics"])) {
        $rAdminSettings["active_statistics"] = true;
        unset($_POST["active_statistics"]);
    } else {
        $rAdminSettings["active_statistics"] = false;
    }
    if (isset($_POST["order_streams"])) {
        $rAdminSettings["order_streams"] = true;
        unset($_POST["order_streams"]);
    } else {
        $rAdminSettings["order_streams"] = false;
    }
    if (isset($_POST["change_usernames"])) {
        $rAdminSettings["change_usernames"] = true;
        unset($_POST["change_usernames"]);
    } else {
        $rAdminSettings["change_usernames"] = false;
    }
    if (isset($_POST["change_own_dns"])) {
        $rAdminSettings["change_own_dns"] = true;
        unset($_POST["change_own_dns"]);
    } else {
        $rAdminSettings["change_own_dns"] = false;
    }
    if (isset($_POST["change_own_email"])) {
        $rAdminSettings["change_own_email"] = true;
        unset($_POST["change_own_email"]);
    } else {
        $rAdminSettings["change_own_email"] = false;
    }
    if (isset($_POST["change_own_password"])) {
        $rAdminSettings["change_own_password"] = true;
        unset($_POST["change_own_password"]);
    } else {
        $rAdminSettings["change_own_password"] = false;
    }
    if (isset($_POST["reseller_restrictions"])) {
        $rAdminSettings["reseller_restrictions"] = true;
        unset($_POST["reseller_restrictions"]);
    } else {
        $rAdminSettings["reseller_restrictions"] = false;
    }
    if (isset($_POST["google_2factor"])) {
        $rAdminSettings["google_2factor"] = true;
        unset($_POST["google_2factor"]);
    } else {
        $rAdminSettings["google_2factor"] = false;
    }
    if (isset($_POST["default_entries"])) {
        $rAdminSettings["default_entries"] = $_POST["default_entries"];
    }
    if (isset($_POST["admin_username"])) {
        $rAdminSettings["admin_username"] = $_POST["admin_username"];
        unset($_POST["admin_username"]);
    }
    if (isset($_POST["admin_password"])) {
        $rAdminSettings["admin_password"] = $_POST["admin_password"];
        unset($_POST["admin_password"]);
    }
    if (isset($_POST["tmdb_language"])) {
        $rAdminSettings["tmdb_language"] = $_POST["tmdb_language"];
        unset($_POST["tmdb_language"]);
    }
    if (isset($_POST["release_parser"])) {
        $rAdminSettings["release_parser"] = $_POST["release_parser"];
        unset($_POST["release_parser"]);
    }
    if (isset($_POST["automatic_backups"])) {
        $rAdminSettings["automatic_backups"] = $_POST["automatic_backups"];
        unset($_POST["automatic_backups"]);
    }
    if (isset($_POST["backups_to_keep"])) {
        $rAdminSettings["backups_to_keep"] = $_POST["backups_to_keep"];
        unset($_POST["backups_to_keep"]);
    }
    if (isset($_POST["change_own_lang"])) {
        $rAdminSettings["change_own_lang"] = true;
        unset($_POST["change_own_lang"]);
    } else {
        $rAdminSettings["change_own_lang"] = false;
    }
    if (isset($_POST["active_mannuals"])) {
        $rAdminSettings["active_mannuals"] = true;
        unset($_POST["active_mannuals"]);
    } else {
        $rAdminSettings["active_mannuals"] = false;
    }
    if (isset($_POST["active_apps"])) {
        $rAdminSettings["active_apps"] = true;
        unset($_POST["active_apps"]);
    } else {
        $rAdminSettings["active_apps"] = false;
    }
    if (isset($_POST["reseller_view_info"])) {
        $rAdminSettings["reseller_view_info"] = true;
        unset($_POST["reseller_view_info"]);
    } else {
        $rAdminSettings["reseller_view_info"] = false;
    }
    if (isset($_POST["reseller_can_isplock"])) {
        $rAdminSettings["reseller_can_isplock"] = true;
        unset($_POST["reseller_can_isplock"]);
    } else {
        $rAdminSettings["reseller_can_isplock"] = false;
    }
    if (isset($_POST["reseller_reset_isplock"])) {
        $rAdminSettings["reseller_reset_isplock"] = true;
        unset($_POST["reseller_reset_isplock"]);
    } else {
        $rAdminSettings["reseller_reset_isplock"] = false;
    }
    if (isset($_POST["recaptcha_v2_site_key"])) {
        $rAdminSettings["recaptcha_v2_site_key"] = $_POST["recaptcha_v2_site_key"];
        unset($_POST["recaptcha_v2_site_key"]);
    }
    if (isset($_POST["recaptcha_v2_secret_key"])) {
        $rAdminSettings["recaptcha_v2_secret_key"] = $_POST["recaptcha_v2_secret_key"];
        unset($_POST["recaptcha_v2_secret_key"]);
    }
    if (isset($_POST["token_telegram"])) {
        $rAdminSettings["token_telegram"] = $_POST["token_telegram"];
        unset($_POST["token_telegram"]);
    }
    if (isset($_POST["chat_id"])) {
        $rAdminSettings["chat_id"] = $_POST["chat_id"];
        unset($_POST["chat_id"]);
    }
    if (isset($_POST["login_flood"])) {
        $rAdminSettings["login_flood"] = $_POST["login_flood"];
        unset($_POST["login_flood"]);
    }
    if (isset($_POST["pass_length"])) {
        $rAdminSettings["pass_length"] = $_POST["pass_length"];
        unset($_POST["pass_length"]);
    }
    if (isset($_POST["use_https"])) {
        $rArray["use_https"] = [];
        foreach ($_POST["use_https"] as $rServer) {
            $rArray["use_https"][] = $rServer;
        }
        unset($_POST["use_https"]);
    } else {
        $rArray["use_https"] = [];
    }
    if (isset($_POST["use_https_main"])) {
        $rAdminSettings["use_https_main"] = true;
        unset($_POST["use_https_main"]);
    } else {
        $rAdminSettings["use_https_main"] = false;
    }
    if (isset($_POST["reseller_mag_to_m3u"])) {
        $rAdminSettings["reseller_mag_to_m3u"] = true;
        unset($_POST["reseller_mag_to_m3u"]);
    } else {
        $rAdminSettings["reseller_mag_to_m3u"] = false;
    }
    if (isset($_POST["dashboard_stats_frequency"])) {
        $rAdminSettings["dashboard_stats_frequency"] = $_POST["dashboard_stats_frequency"];
        unset($_POST["dashboard_stats_frequency"]);
    }
    writeAdminSettings();
    foreach ($_POST as $rKey => $rValue) {
        if (isset($rArray[$rKey])) {
            $rArray[$rKey] = $rValue;
        }
    }
    $rValues = [];
    foreach ($rArray as $rKey => $rValue) {
        if (is_array($rValue)) {
            $rValue = json_encode($rValue);
        }
        if (is_null($rValue)) {
            $rValues[] = "`" . ESC($rKey) . "` = NULL";
        } else {
            $rValues[] = "`" . ESC($rKey) . "` = '" . ESC($rValue) . "'";
        }
    }
    $rQuery = "UPDATE `settings` SET " . join(", ", $rValues) . ";";
    if ($db->query($rQuery)) {
        $_STATUS = 0;
    } else {
        $_STATUS = 1;
    }
}
$rSettings = getSettings();
$rSettings["sidebar"] = $rUserInfo["sidebar"];
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
echo "                <form action=\"./settings.php\" method=\"POST\" id=\"category_form\">\n                    <!-- start page title -->\n                    <div class=\"row\">\n                        <div class=\"col-12\">\n                            <div class=\"page-title-box\">\n\t\t\t\t\t\t\t<div class=\"page-title-right\">\n\t\t\t\t\t\t\t\t<input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"Save Changes\"></input>\n                            </div>\n                                <h4 class=\"page-title\">";
echo $_["settings"];
echo "</h4>\n                            </div>\n                        </div>\n                    </div>     \n                    <!-- end page title --> \n                    <div class=\"row\">\n                        <div class=\"col-xl-12\">\n                            ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                            <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                    <span aria-hidden=\"true\">&times;</span>\n                                </button>\n                                ";
        echo $_["settings_sucessfully_updated"];
        echo "                            </div>\n\t\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["settings_sucessfully_updated"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                            ";
    }
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        if (!$rSettings["sucessedit"]) {
            echo "                            <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                    <span aria-hidden=\"true\">&times;</span>\n                                </button>\n                                ";
            echo $_["there_was_an_error_saving_settings"];
            echo "                            </div>\n\t\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["there_was_an_error_saving_settings"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                            ";
        }
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
            if (!$rSettings["sucessedit"]) {
                echo "                            <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                    <span aria-hidden=\"true\">&times;</span>\n                                </button>\n                                ";
                echo $_["failed_to_update_GeoLite2"];
                echo "                            </div>\n\t\t\t\t\t\t\t";
            } else {
                echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                echo $_["failed_to_update_GeoLite2"];
                echo "', \"warning\");\n  \t\t\t\t\t</script>\n                            ";
            }
        } else {
            if (isset($_STATUS) && $_STATUS == 3) {
                if (!$rSettings["sucessedit"]) {
                    echo "                            <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                    <span aria-hidden=\"true\">&times;</span>\n                                </button>\n                                ";
                    echo $_["geoLite2_has_been_updated"];
                    echo "                            </div>\n\t\t\t\t\t\t\t";
                } else {
                    echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                    echo $_["geoLite2_has_been_updated"];
                    echo "', \"success\");\n  \t\t\t\t\t</script>\n\t\t\t\t\t\t\t";
                }
            } else {
                if (isset($_STATUS) && $_STATUS == 4) {
                    if (!$rSettings["sucessedit"]) {
                        echo "                            <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                    <span aria-hidden=\"true\">&times;</span>\n                                </button>\n                                Failed to update Panel! Please try again.\n                            </div>\n\t\t\t\t\t\t\t";
                    } else {
                        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", \"Failed to update Panel! Please try again.\", \"warning\");\n  \t\t\t\t\t</script>\n                            ";
                    }
                } else {
                    if (isset($_STATUS) && $_STATUS == 5) {
                        if (!$rSettings["sucessedit"]) {
                            echo "                            <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                    <span aria-hidden=\"true\">&times;</span>\n                                </button>\n\t\t\t\t\t\t\t\tTHE PANEL IS UPDATED...\n                            </div>\n\t\t\t\t\t\t\t";
                        } else {
                            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", \"THE PANEL IS UPDATED...\", \"success\");\n  \t\t\t\t\t</script>\n                            ";
                        }
                    } else {
                        if (isset($_STATUS) && 0 < $_STATUS) {
                            if (!$rSettings["sucessedit"]) {
                                echo "                            <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                    <span aria-hidden=\"true\">&times;</span>\n                                </button>\n                                ";
                                echo $_["there_was_an_error_saving_settings"];
                                echo "                            </div>\n                            ";
                            } else {
                                echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                                echo $_["there_was_an_error_saving_settings"];
                                echo "', \"warning\");\n  \t\t\t\t\t</script>\n                            ";
                            }
                        }
                    }
                }
            }
        }
    }
}
$rContext = stream_context_create(["http" => ["timeout" => 3]]);
$rCurrent = json_decode(file_get_contents("http://xcodes.mine.nu/XCodes/current.json", false, $rContext), true);
$rInfos = json_decode(file_get_contents("http://xcodes.mine.nu/XCodes/infos.json", false, $rContext), true);
$rGeoLite2 = json_decode(file_get_contents("http://xcodes.mine.nu/XCodes/status.json", false, $rContext), true);
if ($rAdminSettings["geolite2_version"] < intval($rGeoLite2["version"])) {
}
$rUpdatePanel = json_decode(file_get_contents("http://xcodes.mine.nu/XCodes/current.json", false, $rContext), true);
if ($rAdminSettings["panel_version"] < intval($rUpdatePanel["version"])) {
}
echo "\t\t\t\t\t\t    ";
if ($rAdminSettings["geolite2_version"] < $rGeoLite2["version"]) {
    echo "                            <div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                    <span aria-hidden=\"true\">&times;</span>\n                                </button>\n                                ";
    echo $_["a_new_version_of_GeoLite2"];
    echo " (";
    echo $rGeoLite2["version"];
    echo ") ";
    echo $_["is_available"];
    echo " <a href=\"./settings.php?geolite2\">";
    echo $_["click_here_to_update"];
    echo "</a>\n                            </div>\n                            ";
}
echo "\t\t\t\t\t\t\t";
if ($rAdminSettings["panel_version"] < $rUpdatePanel["version"]) {
    echo "                            <div class=\"alert alert-info alert-dismissible fade show\" role=\"alert\">\n                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                    <span aria-hidden=\"true\">&times;</span>\n                                </button>\n                                A new version ";
    echo $rRelease;
    echo " <sup class=\"font-6\">";
    echo $rEarlyAccess;
    echo " ";
    echo $rUpdatePanel["version"];
    echo "</sup> ";
    echo $_["is_available"];
    echo " <a href=\"./settings.php?panel_version\">";
    echo $_["click_here_to_update"];
    echo "</a>\n                            </div>\n\t\t\t\t\t\t\t";
}
echo "                            <div class=\"card\">\n                                <div class=\"card-body\">\n\t\t\t\t\t\t\t\t\t<div class=\"bg-soft-light border-light border\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"row text-center\">\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-7\">\n\t\t\t\t\t\t\t\t\t\t\t\t<h3 class=\"font-weight-normal mb-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t    ";
if ($rAdminSettings["panel_version"] < $rUpdatePanel["version"]) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t    <small class=\"mdi mdi-checkbox-blank-circle text-danger align-middle mr-1\"></small>\n\t\t\t\t\t\t\t\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<small class=\"mdi mdi-checkbox-blank-circle text-success align-middle mr-1\"></small>\n\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t\t\t    <span class=\"font-18\">";
echo $_["installed_version"];
echo "</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span>";
echo $rRelease;
echo "<sup class=\"font-13\">";
echo $rEarlyAccess;
echo substr($rAdminSettings["panel_version"], 0, 2);
echo "</sup></span>\n\t\t\t\t\t\t\t\t\t\t\t\t</h3>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-3\">\n\t\t\t\t\t\t\t\t\t\t\t\t<h3 class=\"font-weight-normal mb-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t    ";
if ($rAdminSettings["geolite2_version"] < $rGeoLite2["version"]) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t    <small class=\"mdi mdi-checkbox-blank-circle text-danger align-middle mr-1\"></small>\n\t\t\t\t\t\t\t\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t<small class=\"mdi mdi-checkbox-blank-circle text-success align-middle mr-1\"></small>\n\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t\t\t    <span class=\"font-18\">";
echo $_["geoLite2_version"];
echo "</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span>";
echo substr($rAdminSettings["geolite2_version"], 4, 2) . "." . substr($rAdminSettings["geolite2_version"], 6, 2);
echo "</span>\n\t\t\t\t\t\t\t\t\t\t\t\t</h3>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t    </div>\n                            <div class=\"card\">\n                                <div class=\"card-body\">\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t";
if (hasPermissions("adv", "settings")) {
    echo "                                            <li class=\"nav-item\">\n                                                <a href=\"#general-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n\t\t\t\t\t\t\t\t\t\t\t\t    <span class=\"d-none d-sm-inline\">";
    echo $_["general"];
    echo "</span>\n                                                </a>\n                                            </li>\n\t\t\t\t\t\t\t\t\t\t\t<li class=\"nav-item\">\n                                                <a href=\"#xui\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"mdi mdi-settings mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">Xtream</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#reseller\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"mdi mdi-coins mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["reseller"];
    echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#streaming\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"mdi mdi-play mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["streaming"];
    echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#mag\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"mdi mdi-tablet mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["mag"];
    echo "</span> \n                                                </a>\n                                            </li>\n\t\t\t\t\t\t\t\t\t\t\t";
}
if (hasPermissions("adv", "database")) {
    echo "\t\t\t\t\t\t\t\t\t\t\t<li class=\"nav-item\">\n                                                <a href=\"#infos\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"fas fa-info mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">Updates</span>\n                                                </a>\n                                            </li>\n\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n\t\t\t\t\t\t\t\t\t\t\t";
if (hasPermissions("adv", "settings")) {
    echo "                                            <div class=\"tab-pane\" id=\"general-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"server_name\">";
    echo $_["server_name"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"server_name\" name=\"server_name\" value=\"";
    echo htmlspecialchars($rSettings["server_name"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"logo_url\">";
    echo $_["logo_url"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"logo_url\" name=\"logo_url\" value=\"";
    echo htmlspecialchars($rSettings["logo_url"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"logo_url_sidebar\">";
    echo $_["logo_url_sidebar"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"logo_url_sidebar\" name=\"logo_url_sidebar\" value=\"";
    echo htmlspecialchars($rSettings["logo_url_sidebar"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"default_timezone\">";
    echo $_["timezone"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"default_timezone\" id=\"default_timezone\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
    $rTimeZones = ["Africa/Abidjan" => "Africa/Abidjan [GMT  00:00]", "Africa/Accra" => "Africa/Accra [GMT  00:00]", "Africa/Addis_Ababa" => "Africa/Addis_Ababa [EAT +03:00]", "Africa/Algiers" => "Africa/Algiers [CET +01:00]", "Africa/Asmara" => "Africa/Asmara [EAT +03:00]", "Africa/Bamako" => "Africa/Bamako [GMT  00:00]", "Africa/Bangui" => "Africa/Bangui [WAT +01:00]", "Africa/Banjul" => "Africa/Banjul [GMT  00:00]", "Africa/Bissau" => "Africa/Bissau [GMT  00:00]", "Africa/Blantyre" => "Africa/Blantyre [CAT +02:00]", "Africa/Brazzaville" => "Africa/Brazzaville [WAT +01:00]", "Africa/Bujumbura" => "Africa/Bujumbura [CAT +02:00]", "Africa/Cairo" => "Africa/Cairo [EET +02:00]", "Africa/Casablanca" => "Africa/Casablanca [WEST +01:00]", "Africa/Ceuta" => "Africa/Ceuta [CEST +02:00]", "Africa/Conakry" => "Africa/Conakry [GMT  00:00]", "Africa/Dakar" => "Africa/Dakar [GMT  00:00]", "Africa/Dar_es_Salaam" => "Africa/Dar_es_Salaam [EAT +03:00]", "Africa/Djibouti" => "Africa/Djibouti [EAT +03:00]", "Africa/Douala" => "Africa/Douala [WAT +01:00]", "Africa/El_Aaiun" => "Africa/El_Aaiun [WEST +01:00]", "Africa/Freetown" => "Africa/Freetown [GMT  00:00]", "Africa/Gaborone" => "Africa/Gaborone [CAT +02:00]", "Africa/Harare" => "Africa/Harare [CAT +02:00]", "Africa/Johannesburg" => "Africa/Johannesburg [SAST +02:00]", "Africa/Juba" => "Africa/Juba [EAT +03:00]", "Africa/Kampala" => "Africa/Kampala [EAT +03:00]", "Africa/Khartoum" => "Africa/Khartoum [EAT +03:00]", "Africa/Kigali" => "Africa/Kigali [CAT +02:00]", "Africa/Kinshasa" => "Africa/Kinshasa [WAT +01:00]", "Africa/Lagos" => "Africa/Lagos [WAT +01:00]", "Africa/Libreville" => "Africa/Libreville [WAT +01:00]", "Africa/Lome" => "Africa/Lome [GMT  00:00]", "Africa/Luanda" => "Africa/Luanda [WAT +01:00]", "Africa/Lubumbashi" => "Africa/Lubumbashi [CAT +02:00]", "Africa/Lusaka" => "Africa/Lusaka [CAT +02:00]", "Africa/Malabo" => "Africa/Malabo [WAT +01:00]", "Africa/Maputo" => "Africa/Maputo [CAT +02:00]", "Africa/Maseru" => "Africa/Maseru [SAST +02:00]", "Africa/Mbabane" => "Africa/Mbabane [SAST +02:00]", "Africa/Mogadishu" => "Africa/Mogadishu [EAT +03:00]", "Africa/Monrovia" => "Africa/Monrovia [GMT  00:00]", "Africa/Nairobi" => "Africa/Nairobi [EAT +03:00]", "Africa/Ndjamena" => "Africa/Ndjamena [WAT +01:00]", "Africa/Niamey" => "Africa/Niamey [WAT +01:00]", "Africa/Nouakchott" => "Africa/Nouakchott [GMT  00:00]", "Africa/Ouagadougou" => "Africa/Ouagadougou [GMT  00:00]", "Africa/Porto-Novo" => "Africa/Porto-Novo [WAT +01:00]", "Africa/Sao_Tome" => "Africa/Sao_Tome [GMT  00:00]", "Africa/Tripoli" => "Africa/Tripoli [EET +02:00]", "Africa/Tunis" => "Africa/Tunis [CET +01:00]", "Africa/Windhoek" => "Africa/Windhoek [WAST +02:00]", "America/Adak" => "America/Adak [HADT -09:00]", "America/Anchorage" => "America/Anchorage [AKDT -08:00]", "America/Anguilla" => "America/Anguilla [AST -04:00]", "America/Antigua" => "America/Antigua [AST -04:00]", "America/Araguaina" => "America/Araguaina [BRT -03:00]", "America/Argentina/Buenos_Aires" => "America/Argentina/Buenos_Aires [ART -03:00]", "America/Argentina/Catamarca" => "America/Argentina/Catamarca [ART -03:00]", "America/Argentina/Cordoba" => "America/Argentina/Cordoba [ART -03:00]", "America/Argentina/Jujuy" => "America/Argentina/Jujuy [ART -03:00]", "America/Argentina/La_Rioja" => "America/Argentina/La_Rioja [ART -03:00]", "America/Argentina/Mendoza" => "America/Argentina/Mendoza [ART -03:00]", "America/Argentina/Rio_Gallegos" => "America/Argentina/Rio_Gallegos [ART -03:00]", "America/Argentina/Salta" => "America/Argentina/Salta [ART -03:00]", "America/Argentina/San_Juan" => "America/Argentina/San_Juan [ART -03:00]", "America/Argentina/San_Luis" => "America/Argentina/San_Luis [ART -03:00]", "America/Argentina/Tucuman" => "America/Argentina/Tucuman [ART -03:00]", "America/Argentina/Ushuaia" => "America/Argentina/Ushuaia [ART -03:00]", "America/Aruba" => "America/Aruba [AST -04:00]", "America/Asuncion" => "America/Asuncion [PYT -04:00]", "America/Atikokan" => "America/Atikokan [EST -05:00]", "America/Bahia" => "America/Bahia [BRT -03:00]", "America/Bahia_Banderas" => "America/Bahia_Banderas [CDT -05:00]", "America/Barbados" => "America/Barbados [AST -04:00]", "America/Belem" => "America/Belem [BRT -03:00]", "America/Belize" => "America/Belize [CST -06:00]", "America/Blanc-Sablon" => "America/Blanc-Sablon [AST -04:00]", "America/Boa_Vista" => "America/Boa_Vista [AMT -04:00]", "America/Bogota" => "America/Bogota [COT -05:00]", "America/Boise" => "America/Boise [MDT -06:00]", "America/Cambridge_Bay" => "America/Cambridge_Bay [MDT -06:00]", "America/Campo_Grande" => "America/Campo_Grande [AMT -04:00]", "America/Cancun" => "America/Cancun [CDT -05:00]", "America/Caracas" => "America/Caracas [VET -04:30]", "America/Cayenne" => "America/Cayenne [GFT -03:00]", "America/Cayman" => "America/Cayman [EST -05:00]", "America/Chicago" => "America/Chicago [CDT -05:00]", "America/Chihuahua" => "America/Chihuahua [MDT -06:00]", "America/Costa_Rica" => "America/Costa_Rica [CST -06:00]", "America/Creston" => "America/Creston [MST -07:00]", "America/Cuiaba" => "America/Cuiaba [AMT -04:00]", "America/Curacao" => "America/Curacao [AST -04:00]", "America/Danmarkshavn" => "America/Danmarkshavn [GMT  00:00]", "America/Dawson" => "America/Dawson [PDT -07:00]", "America/Dawson_Creek" => "America/Dawson_Creek [MST -07:00]", "America/Denver" => "America/Denver [MDT -06:00]", "America/Detroit" => "America/Detroit [EDT -04:00]", "America/Dominica" => "America/Dominica [AST -04:00]", "America/Edmonton" => "America/Edmonton [MDT -06:00]", "America/Eirunepe" => "America/Eirunepe [ACT -05:00]", "America/El_Salvador" => "America/El_Salvador [CST -06:00]", "America/Fortaleza" => "America/Fortaleza [BRT -03:00]", "America/Glace_Bay" => "America/Glace_Bay [ADT -03:00]", "America/Godthab" => "America/Godthab [WGST -02:00]", "America/Goose_Bay" => "America/Goose_Bay [ADT -03:00]", "America/Grand_Turk" => "America/Grand_Turk [AST -04:00]", "America/Grenada" => "America/Grenada [AST -04:00]", "America/Guadeloupe" => "America/Guadeloupe [AST -04:00]", "America/Guatemala" => "America/Guatemala [CST -06:00]", "America/Guayaquil" => "America/Guayaquil [ECT -05:00]", "America/Guyana" => "America/Guyana [GYT -04:00]", "America/Halifax" => "America/Halifax [ADT -03:00]", "America/Havana" => "America/Havana [CDT -04:00]", "America/Hermosillo" => "America/Hermosillo [MST -07:00]", "America/Indiana/Indianapolis" => "America/Indiana/Indianapolis [EDT -04:00]", "America/Indiana/Knox" => "America/Indiana/Knox [CDT -05:00]", "America/Indiana/Marengo" => "America/Indiana/Marengo [EDT -04:00]", "America/Indiana/Petersburg" => "America/Indiana/Petersburg [EDT -04:00]", "America/Indiana/Tell_City" => "America/Indiana/Tell_City [CDT -05:00]", "America/Indiana/Vevay" => "America/Indiana/Vevay [EDT -04:00]", "America/Indiana/Vincennes" => "America/Indiana/Vincennes [EDT -04:00]", "America/Indiana/Winamac" => "America/Indiana/Winamac [EDT -04:00]", "America/Inuvik" => "America/Inuvik [MDT -06:00]", "America/Iqaluit" => "America/Iqaluit [EDT -04:00]", "America/Jamaica" => "America/Jamaica [EST -05:00]", "America/Juneau" => "America/Juneau [AKDT -08:00]", "America/Kentucky/Louisville" => "America/Kentucky/Louisville [EDT -04:00]", "America/Kentucky/Monticello" => "America/Kentucky/Monticello [EDT -04:00]", "America/Kralendijk" => "America/Kralendijk [AST -04:00]", "America/La_Paz" => "America/La_Paz [BOT -04:00]", "America/Lima" => "America/Lima [PET -05:00]", "America/Los_Angeles" => "America/Los_Angeles [PDT -07:00]", "America/Lower_Princes" => "America/Lower_Princes [AST -04:00]", "America/Maceio" => "America/Maceio [BRT -03:00]", "America/Managua" => "America/Managua [CST -06:00]", "America/Manaus" => "America/Manaus [AMT -04:00]", "America/Marigot" => "America/Marigot [AST -04:00]", "America/Martinique" => "America/Martinique [AST -04:00]", "America/Matamoros" => "America/Matamoros [CDT -05:00]", "America/Mazatlan" => "America/Mazatlan [MDT -06:00]", "America/Menominee" => "America/Menominee [CDT -05:00]", "America/Merida" => "America/Merida [CDT -05:00]", "America/Metlakatla" => "America/Metlakatla [PST -08:00]", "America/Mexico_City" => "America/Mexico_City [CDT -05:00]", "America/Miquelon" => "America/Miquelon [PMDT -02:00]", "America/Moncton" => "America/Moncton [ADT -03:00]", "America/Monterrey" => "America/Monterrey [CDT -05:00]", "America/Montevideo" => "America/Montevideo [UYT -03:00]", "America/Montserrat" => "America/Montserrat [AST -04:00]", "America/Nassau" => "America/Nassau [EDT -04:00]", "America/New_York" => "America/New_York [EDT -04:00]", "America/Nipigon" => "America/Nipigon [EDT -04:00]", "America/Nome" => "America/Nome [AKDT -08:00]", "America/Noronha" => "America/Noronha [FNT -02:00]", "America/North_Dakota/Beulah" => "America/North_Dakota/Beulah [CDT -05:00]", "America/North_Dakota/Center" => "America/North_Dakota/Center [CDT -05:00]", "America/North_Dakota/New_Salem" => "America/North_Dakota/New_Salem [CDT -05:00]", "America/Ojinaga" => "America/Ojinaga [MDT -06:00]", "America/Panama" => "America/Panama [EST -05:00]", "America/Pangnirtung" => "America/Pangnirtung [EDT -04:00]", "America/Paramaribo" => "America/Paramaribo [SRT -03:00]", "America/Phoenix" => "America/Phoenix [MST -07:00]", "America/Port-au-Prince" => "America/Port-au-Prince [EDT -04:00]", "America/Port_of_Spain" => "America/Port_of_Spain [AST -04:00]", "America/Porto_Velho" => "America/Porto_Velho [AMT -04:00]", "America/Puerto_Rico" => "America/Puerto_Rico [AST -04:00]", "America/Rainy_River" => "America/Rainy_River [CDT -05:00]", "America/Rankin_Inlet" => "America/Rankin_Inlet [CDT -05:00]", "America/Recife" => "America/Recife [BRT -03:00]", "America/Regina" => "America/Regina [CST -06:00]", "America/Resolute" => "America/Resolute [CDT -05:00]", "America/Rio_Branco" => "America/Rio_Branco [ACT -05:00]", "America/Santa_Isabel" => "America/Santa_Isabel [PDT -07:00]", "America/Santarem" => "America/Santarem [BRT -03:00]", "America/Santiago" => "America/Santiago [CLST -03:00]", "America/Santo_Domingo" => "America/Santo_Domingo [AST -04:00]", "America/Sao_Paulo" => "America/Sao_Paulo [BRT -03:00]", "America/Scoresbysund" => "America/Scoresbysund [EGST  00:00]", "America/Sitka" => "America/Sitka [AKDT -08:00]", "America/St_Barthelemy" => "America/St_Barthelemy [AST -04:00]", "America/St_Johns" => "America/St_Johns [NDT -02:30]", "America/St_Kitts" => "America/St_Kitts [AST -04:00]", "America/St_Lucia" => "America/St_Lucia [AST -04:00]", "America/St_Thomas" => "America/St_Thomas [AST -04:00]", "America/St_Vincent" => "America/St_Vincent [AST -04:00]", "America/Swift_Current" => "America/Swift_Current [CST -06:00]", "America/Tegucigalpa" => "America/Tegucigalpa [CST -06:00]", "America/Thule" => "America/Thule [ADT -03:00]", "America/Thunder_Bay" => "America/Thunder_Bay [EDT -04:00]", "America/Tijuana" => "America/Tijuana [PDT -07:00]", "America/Toronto" => "America/Toronto [EDT -04:00]", "America/Tortola" => "America/Tortola [AST -04:00]", "America/Vancouver" => "America/Vancouver [PDT -07:00]", "America/Whitehorse" => "America/Whitehorse [PDT -07:00]", "America/Winnipeg" => "America/Winnipeg [CDT -05:00]", "America/Yakutat" => "America/Yakutat [AKDT -08:00]", "America/Yellowknife" => "America/Yellowknife [MDT -06:00]", "Antarctica/Casey" => "Antarctica/Casey [AWST +08:00]", "Antarctica/Davis" => "Antarctica/Davis [DAVT +07:00]", "Antarctica/DumontDUrville" => "Antarctica/DumontDUrville [DDUT +10:00]", "Antarctica/Macquarie" => "Antarctica/Macquarie [MIST +11:00]", "Antarctica/Mawson" => "Antarctica/Mawson [MAWT +05:00]", "Antarctica/McMurdo" => "Antarctica/McMurdo [NZDT +13:00]", "Antarctica/Palmer" => "Antarctica/Palmer [CLST -03:00]", "Antarctica/Rothera" => "Antarctica/Rothera [ROTT -03:00]", "Antarctica/Syowa" => "Antarctica/Syowa [SYOT +03:00]", "Antarctica/Troll" => "Antarctica/Troll [CEST +02:00]", "Antarctica/Vostok" => "Antarctica/Vostok [VOST +06:00]", "Arctic/Longyearbyen" => "Arctic/Longyearbyen [CEST +02:00]", "Asia/Aden" => "Asia/Aden [AST +03:00]", "Asia/Almaty" => "Asia/Almaty [ALMT +06:00]", "Asia/Amman" => "Asia/Amman [EEST +03:00]", "Asia/Anadyr" => "Asia/Anadyr [ANAT +12:00]", "Asia/Aqtau" => "Asia/Aqtau [AQTT +05:00]", "Asia/Aqtobe" => "Asia/Aqtobe [AQTT +05:00]", "Asia/Ashgabat" => "Asia/Ashgabat [TMT +05:00]", "Asia/Baghdad" => "Asia/Baghdad [AST +03:00]", "Asia/Bahrain" => "Asia/Bahrain [AST +03:00]", "Asia/Baku" => "Asia/Baku [AZST +05:00]", "Asia/Bangkok" => "Asia/Bangkok [ICT +07:00]", "Asia/Beirut" => "Asia/Beirut [EEST +03:00]", "Asia/Bishkek" => "Asia/Bishkek [KGT +06:00]", "Asia/Brunei" => "Asia/Brunei [BNT +08:00]", "Asia/Chita" => "Asia/Chita [IRKT +08:00]", "Asia/Choibalsan" => "Asia/Choibalsan [CHOT +08:00]", "Asia/Colombo" => "Asia/Colombo [IST +05:30]", "Asia/Damascus" => "Asia/Damascus [EEST +03:00]", "Asia/Dhaka" => "Asia/Dhaka [BDT +06:00]", "Asia/Dili" => "Asia/Dili [TLT +09:00]", "Asia/Dubai" => "Asia/Dubai [GST +04:00]", "Asia/Dushanbe" => "Asia/Dushanbe [TJT +05:00]", "Asia/Gaza" => "Asia/Gaza [EET +02:00]", "Asia/Hebron" => "Asia/Hebron [EET +02:00]", "Asia/Ho_Chi_Minh" => "Asia/Ho_Chi_Minh [ICT +07:00]", "Asia/Hong_Kong" => "Asia/Hong_Kong [HKT +08:00]", "Asia/Hovd" => "Asia/Hovd [HOVT +07:00]", "Asia/Irkutsk" => "Asia/Irkutsk [IRKT +08:00]", "Asia/Jakarta" => "Asia/Jakarta [WIB +07:00]", "Asia/Jayapura" => "Asia/Jayapura [WIT +09:00]", "Asia/Jerusalem" => "Asia/Jerusalem [IDT +03:00]", "Asia/Kabul" => "Asia/Kabul [AFT +04:30]", "Asia/Kamchatka" => "Asia/Kamchatka [PETT +12:00]", "Asia/Karachi" => "Asia/Karachi [PKT +05:00]", "Asia/Kathmandu" => "Asia/Kathmandu [NPT +05:45]", "Asia/Khandyga" => "Asia/Khandyga [YAKT +09:00]", "Asia/Kolkata" => "Asia/Kolkata [IST +05:30]", "Asia/Krasnoyarsk" => "Asia/Krasnoyarsk [KRAT +07:00]", "Asia/Kuala_Lumpur" => "Asia/Kuala_Lumpur [MYT +08:00]", "Asia/Kuching" => "Asia/Kuching [MYT +08:00]", "Asia/Kuwait" => "Asia/Kuwait [AST +03:00]", "Asia/Macau" => "Asia/Macau [CST +08:00]", "Asia/Magadan" => "Asia/Magadan [MAGT +10:00]", "Asia/Makassar" => "Asia/Makassar [WITA +08:00]", "Asia/Manila" => "Asia/Manila [PHT +08:00]", "Asia/Muscat" => "Asia/Muscat [GST +04:00]", "Asia/Nicosia" => "Asia/Nicosia [EEST +03:00]", "Asia/Novokuznetsk" => "Asia/Novokuznetsk [KRAT +07:00]", "Asia/Novosibirsk" => "Asia/Novosibirsk [NOVT +06:00]", "Asia/Omsk" => "Asia/Omsk [OMST +06:00]", "Asia/Oral" => "Asia/Oral [ORAT +05:00]", "Asia/Phnom_Penh" => "Asia/Phnom_Penh [ICT +07:00]", "Asia/Pontianak" => "Asia/Pontianak [WIB +07:00]", "Asia/Pyongyang" => "Asia/Pyongyang [KST +09:00]", "Asia/Qatar" => "Asia/Qatar [AST +03:00]", "Asia/Qyzylorda" => "Asia/Qyzylorda [QYZT +06:00]", "Asia/Rangoon" => "Asia/Rangoon [MMT +06:30]", "Asia/Riyadh" => "Asia/Riyadh [AST +03:00]", "Asia/Sakhalin" => "Asia/Sakhalin [SAKT +10:00]", "Asia/Samarkand" => "Asia/Samarkand [UZT +05:00]", "Asia/Seoul" => "Asia/Seoul [KST +09:00]", "Asia/Shanghai" => "Asia/Shanghai [CST +08:00]", "Asia/Singapore" => "Asia/Singapore [SGT +08:00]", "Asia/Srednekolymsk" => "Asia/Srednekolymsk [SRET +11:00]", "Asia/Taipei" => "Asia/Taipei [CST +08:00]", "Asia/Tashkent" => "Asia/Tashkent [UZT +05:00]", "Asia/Tbilisi" => "Asia/Tbilisi [GET +04:00]", "Asia/Tehran" => "Asia/Tehran [IRST +03:30]", "Asia/Thimphu" => "Asia/Thimphu [BTT +06:00]", "Asia/Tokyo" => "Asia/Tokyo [JST +09:00]", "Asia/Ulaanbaatar" => "Asia/Ulaanbaatar [ULAT +08:00]", "Asia/Urumqi" => "Asia/Urumqi [XJT +06:00]", "Asia/Ust-Nera" => "Asia/Ust-Nera [VLAT +10:00]", "Asia/Vientiane" => "Asia/Vientiane [ICT +07:00]", "Asia/Vladivostok" => "Asia/Vladivostok [VLAT +10:00]", "Asia/Yakutsk" => "Asia/Yakutsk [YAKT +09:00]", "Asia/Yekaterinburg" => "Asia/Yekaterinburg [YEKT +05:00]", "Asia/Yerevan" => "Asia/Yerevan [AMT +04:00]", "Atlantic/Azores" => "Atlantic/Azores [AZOST  00:00]", "Atlantic/Bermuda" => "Atlantic/Bermuda [ADT -03:00]", "Atlantic/Canary" => "Atlantic/Canary [WEST +01:00]", "Atlantic/Cape_Verde" => "Atlantic/Cape_Verde [CVT -01:00]", "Atlantic/Faroe" => "Atlantic/Faroe [WEST +01:00]", "Atlantic/Madeira" => "Atlantic/Madeira [WEST +01:00]", "Atlantic/Reykjavik" => "Atlantic/Reykjavik [GMT  00:00]", "Atlantic/South_Georgia" => "Atlantic/South_Georgia [GST -02:00]", "Atlantic/St_Helena" => "Atlantic/St_Helena [GMT  00:00]", "Atlantic/Stanley" => "Atlantic/Stanley [FKST -03:00]", "Australia/Adelaide" => "Australia/Adelaide [ACDT +10:30]", "Australia/Brisbane" => "Australia/Brisbane [AEST +10:00]", "Australia/Broken_Hill" => "Australia/Broken_Hill [ACDT +10:30]", "Australia/Currie" => "Australia/Currie [AEDT +11:00]", "Australia/Darwin" => "Australia/Darwin [ACST +09:30]", "Australia/Eucla" => "Australia/Eucla [ACWST +08:45]", "Australia/Hobart" => "Australia/Hobart [AEDT +11:00]", "Australia/Lindeman" => "Australia/Lindeman [AEST +10:00]", "Australia/Lord_Howe" => "Australia/Lord_Howe [LHDT +11:00]", "Australia/Melbourne" => "Australia/Melbourne [AEDT +11:00]", "Australia/Perth" => "Australia/Perth [AWST +08:00]", "Australia/Sydney" => "Australia/Sydney [AEDT +11:00]", "Europe/Amsterdam" => "Europe/Amsterdam [CEST +02:00]", "Europe/Andorra" => "Europe/Andorra [CEST +02:00]", "Europe/Athens" => "Europe/Athens [EEST +03:00]", "Europe/Belgrade" => "Europe/Belgrade [CEST +02:00]", "Europe/Berlin" => "Europe/Berlin [CEST +02:00]", "Europe/Bratislava" => "Europe/Bratislava [CEST +02:00]", "Europe/Brussels" => "Europe/Brussels [CEST +02:00]", "Europe/Bucharest" => "Europe/Bucharest [EEST +03:00]", "Europe/Budapest" => "Europe/Budapest [CEST +02:00]", "Europe/Busingen" => "Europe/Busingen [CEST +02:00]", "Europe/Chisinau" => "Europe/Chisinau [EEST +03:00]", "Europe/Copenhagen" => "Europe/Copenhagen [CEST +02:00]", "Europe/Dublin" => "Europe/Dublin [IST +01:00]", "Europe/Gibraltar" => "Europe/Gibraltar [CEST +02:00]", "Europe/Guernsey" => "Europe/Guernsey [BST +01:00]", "Europe/Helsinki" => "Europe/Helsinki [EEST +03:00]", "Europe/Isle_of_Man" => "Europe/Isle_of_Man [BST +01:00]", "Europe/Istanbul" => "Europe/Istanbul [EEST +03:00]", "Europe/Jersey" => "Europe/Jersey [BST +01:00]", "Europe/Kaliningrad" => "Europe/Kaliningrad [EET +02:00]", "Europe/Kiev" => "Europe/Kiev [EEST +03:00]", "Europe/Lisbon" => "Europe/Lisbon [WEST +01:00]", "Europe/Ljubljana" => "Europe/Ljubljana [CEST +02:00]", "Europe/London" => "Europe/London [BST +01:00]", "Europe/Luxembourg" => "Europe/Luxembourg [CEST +02:00]", "Europe/Madrid" => "Europe/Madrid [CEST +02:00]", "Europe/Malta" => "Europe/Malta [CEST +02:00]", "Europe/Mariehamn" => "Europe/Mariehamn [EEST +03:00]", "Europe/Minsk" => "Europe/Minsk [MSK +03:00]", "Europe/Monaco" => "Europe/Monaco [CEST +02:00]", "Europe/Moscow" => "Europe/Moscow [MSK +03:00]", "Europe/Oslo" => "Europe/Oslo [CEST +02:00]", "Europe/Paris" => "Europe/Paris [CEST +02:00]", "Europe/Podgorica" => "Europe/Podgorica [CEST +02:00]", "Europe/Prague" => "Europe/Prague [CEST +02:00]", "Europe/Riga" => "Europe/Riga [EEST +03:00]", "Europe/Rome" => "Europe/Rome [CEST +02:00]", "Europe/Samara" => "Europe/Samara [SAMT +04:00]", "Europe/San_Marino" => "Europe/San_Marino [CEST +02:00]", "Europe/Sarajevo" => "Europe/Sarajevo [CEST +02:00]", "Europe/Simferopol" => "Europe/Simferopol [MSK +03:00]", "Europe/Skopje" => "Europe/Skopje [CEST +02:00]", "Europe/Sofia" => "Europe/Sofia [EEST +03:00]", "Europe/Stockholm" => "Europe/Stockholm [CEST +02:00]", "Europe/Tallinn" => "Europe/Tallinn [EEST +03:00]", "Europe/Tirane" => "Europe/Tirane [CEST +02:00]", "Europe/Uzhgorod" => "Europe/Uzhgorod [EEST +03:00]", "Europe/Vaduz" => "Europe/Vaduz [CEST +02:00]", "Europe/Vatican" => "Europe/Vatican [CEST +02:00]", "Europe/Vienna" => "Europe/Vienna [CEST +02:00]", "Europe/Vilnius" => "Europe/Vilnius [EEST +03:00]", "Europe/Volgograd" => "Europe/Volgograd [MSK +03:00]", "Europe/Warsaw" => "Europe/Warsaw [CEST +02:00]", "Europe/Zagreb" => "Europe/Zagreb [CEST +02:00]", "Europe/Zaporozhye" => "Europe/Zaporozhye [EEST +03:00]", "Europe/Zurich" => "Europe/Zurich [CEST +02:00]", "Indian/Antananarivo" => "Indian/Antananarivo [EAT +03:00]", "Indian/Chagos" => "Indian/Chagos [IOT +06:00]", "Indian/Christmas" => "Indian/Christmas [CXT +07:00]", "Indian/Cocos" => "Indian/Cocos [CCT +06:30]", "Indian/Comoro" => "Indian/Comoro [EAT +03:00]", "Indian/Kerguelen" => "Indian/Kerguelen [TFT +05:00]", "Indian/Mahe" => "Indian/Mahe [SCT +04:00]", "Indian/Maldives" => "Indian/Maldives [MVT +05:00]", "Indian/Mauritius" => "Indian/Mauritius [MUT +04:00]", "Indian/Mayotte" => "Indian/Mayotte [EAT +03:00]", "Indian/Reunion" => "Indian/Reunion [RET +04:00]", "Pacific/Apia" => "Pacific/Apia [WSDT +14:00]", "Pacific/Auckland" => "Pacific/Auckland [NZDT +13:00]", "Pacific/Bougainville" => "Pacific/Bougainville [BST +11:00]", "Pacific/Chatham" => "Pacific/Chatham [CHADT +13:45]", "Pacific/Chuuk" => "Pacific/Chuuk [CHUT +10:00]", "Pacific/Easter" => "Pacific/Easter [EASST -05:00]", "Pacific/Efate" => "Pacific/Efate [VUT +11:00]", "Pacific/Enderbury" => "Pacific/Enderbury [PHOT +13:00]", "Pacific/Fakaofo" => "Pacific/Fakaofo [TKT +13:00]", "Pacific/Fiji" => "Pacific/Fiji [FJT +12:00]", "Pacific/Funafuti" => "Pacific/Funafuti [TVT +12:00]", "Pacific/Galapagos" => "Pacific/Galapagos [GALT -06:00]", "Pacific/Gambier" => "Pacific/Gambier [GAMT -09:00]", "Pacific/Guadalcanal" => "Pacific/Guadalcanal [SBT +11:00]", "Pacific/Guam" => "Pacific/Guam [ChST +10:00]", "Pacific/Honolulu" => "Pacific/Honolulu [HST -10:00]", "Pacific/Johnston" => "Pacific/Johnston [HST -10:00]", "Pacific/Kiritimati" => "Pacific/Kiritimati [LINT +14:00]", "Pacific/Kosrae" => "Pacific/Kosrae [KOST +11:00]", "Pacific/Kwajalein" => "Pacific/Kwajalein [MHT +12:00]", "Pacific/Majuro" => "Pacific/Majuro [MHT +12:00]", "Pacific/Marquesas" => "Pacific/Marquesas [MART -09:30]", "Pacific/Midway" => "Pacific/Midway [SST -11:00]", "Pacific/Nauru" => "Pacific/Nauru [NRT +12:00]", "Pacific/Niue" => "Pacific/Niue [NUT -11:00]", "Pacific/Norfolk" => "Pacific/Norfolk [NFT +11:30]", "Pacific/Noumea" => "Pacific/Noumea [NCT +11:00]", "Pacific/Pago_Pago" => "Pacific/Pago_Pago [SST -11:00]", "Pacific/Palau" => "Pacific/Palau [PWT +09:00]", "Pacific/Pitcairn" => "Pacific/Pitcairn [PST -08:00]", "Pacific/Pohnpei" => "Pacific/Pohnpei [PONT +11:00]", "Pacific/Port_Moresby" => "Pacific/Port_Moresby [PGT +10:00]", "Pacific/Rarotonga" => "Pacific/Rarotonga [CKT -10:00]", "Pacific/Saipan" => "Pacific/Saipan [ChST +10:00]", "Pacific/Tahiti" => "Pacific/Tahiti [TAHT -10:00]", "Pacific/Tarawa" => "Pacific/Tarawa [GILT +12:00]", "Pacific/Tongatapu" => "Pacific/Tongatapu [TOT +13:00]", "Pacific/Wake" => "Pacific/Wake [WAKT +12:00]", "Pacific/Wallis" => "Pacific/Wallis [WFT +12:00]", "UTC" => "UTC [UTC  00:00]"];
    foreach ($rTimeZones as $rValue => $rText) {
        echo "                                                                    <option ";
        if ($rSettings["default_timezone"] == $rValue) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rValue;
        echo "\">";
        echo $rText;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"bouquet_name\">";
    echo $_["enigma2_bouquet_name"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"bouquet_name\" name=\"bouquet_name\" value=\"";
    echo htmlspecialchars($rSettings["bouquet_name"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"live_streaming_pass\">";
    echo $_["live_streaming_pass"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"live_streaming_pass\" name=\"live_streaming_pass\" value=\"";
    echo htmlspecialchars($rSettings["live_streaming_pass"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"crypt_load_balancing\">";
    echo $_["load_balancing_key"];
    echo "</label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"crypt_load_balancing\" name=\"crypt_load_balancing\" value=\"";
    echo htmlspecialchars($rSettings["crypt_load_balancing"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"userpanel_mainpage\">";
    echo $_["mensagem_dashboard_Revendedores"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["ativar_mensagem_dashboard_revendedores"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"userpanel_mainpage\" name=\"userpanel_mainpage\" value=\"";
    echo htmlspecialchars($rSettings["userpanel_mainpage"]);
    echo "\">\n                                                            </div>\n                                                        </div> \n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"page_mannuals\">";
    echo $_["mannuals_revendedores"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["manualls_revendedores"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"page_mannuals\" name=\"page_mannuals\" value=\"";
    echo htmlspecialchars($rSettings["page_mannuals"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<ul class=\"list-inline wizard mb-0\">\n\t\t\t\t\t\t\t\t\t\t\t\t    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"Save Changes\"></input>\n                                                    </li>\n\t\t\t\t\t\t\t\t\t\t\t\t</ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"xui\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                            <!--<div class=\"form-group row mb-4\">\n                                                              <label class=\"col-md-4 col-form-label\" for=\"language\">";
    echo $_["ui_language"];
    echo "</label>\n                                                              <div class=\"col-md-8\"> \n                                                                  <select name=\"language\" id=\"language\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
    foreach (getLanguages() as $rLanguage) {
        echo "                                                                     <option";
        if ($rAdminSettings["language"] == $rLanguage["key"]) {
            echo " selected";
        }
        echo " value=\"";
        echo $rLanguage["key"];
        echo "\">";
        echo $rLanguage["language"];
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                            </div>-->\n                                                         <div class=\"form-group row mb-4\">\n                                                             <label class=\"col-md-4 col-form-label\" for=\"admin_username\">";
    echo $_["player_credentials"];
    echo "<i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["play_live_streams"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-4\">\n                                                                <input type=\"text\" placeholder=\"";
    echo $_["line_username"];
    echo "\" class=\"form-control\" id=\"admin_username\" name=\"admin_username\" value=\"";
    echo htmlspecialchars($rAdminSettings["admin_username"]);
    echo "\">\n                                                            </div>\n                                                            <div class=\"col-md-4\">\n                                                                <input type=\"text\" placeholder=\"";
    echo $_["line_password"];
    echo "\" class=\"form-control\" id=\"admin_password\" name=\"admin_password\" value=\"";
    echo htmlspecialchars($rAdminSettings["admin_password"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tmdb_api_key\">";
    echo $_["tmdb_api_key"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"tmdb_api_key\" name=\"tmdb_api_key\" value=\"";
    echo htmlspecialchars($rSettings["tmdb_api_key"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tmdb_language\">";
    echo $_["tmdb_language"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["select_which_language"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"tmdb_language\" id=\"tmdb_language\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
    foreach ($rTMDBLanguages as $rKey => $rLanguage) {
        echo "                                                                    <option";
        if ($rAdminSettings["tmdb_language"] == $rKey) {
            echo " selected";
        }
        echo " value=\"";
        echo $rKey;
        echo "\">";
        echo $rLanguage;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tmdb_http_enable\">TMDB HTTP <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"enable this function and tmdb images will be added as http:// to the VOD.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"tmdb_http_enable\" id=\"tmdb_http_enable\" type=\"checkbox\"";
    if ($rAdminSettings["tmdb_http_enable"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"release_parser\">";
    echo $_["release_parser"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["select_which_parser"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"release_parser\" id=\"release_parser\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
    foreach (["python" => "Python Based (slower, more accurate)", "php" => "PHP Based (faster, less accurate)"] as $rKey => $rParser) {
        echo "                                                                    <option";
        if ($rAdminSettings["release_parser"] == $rKey) {
            echo " selected";
        }
        echo " value=\"";
        echo $rKey;
        echo "\">";
        echo $rParser;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"recaptcha_v2_site_key\">";
    echo $_["recaptcha_v2_site_key"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["your_api_keys"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"recaptcha_v2_site_key\" name=\"recaptcha_v2_site_key\" value=\"";
    echo htmlspecialchars($rAdminSettings["recaptcha_v2_site_key"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"recaptcha_v2_secret_key\">";
    echo $_["recaptcha_v2_secret_key"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["your_secret_api_keys"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"recaptcha_v2_secret_key\" name=\"recaptcha_v2_secret_key\" value=\"";
    echo htmlspecialchars($rAdminSettings["recaptcha_v2_secret_key"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"recaptcha_enable\">";
    echo $_["enable_recaptcha"];
    echo " <i class=\"mdi mdi-information\" data-toggle=\"modal\" data-target=\".bs-domains\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"recaptcha_enable\" id=\"recaptcha_enable\" type=\"checkbox\"";
    if ($rAdminSettings["recaptcha_enable"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"ip_logout\">";
    echo $_["logout_on_ip_change"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["logout_session"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"ip_logout\" id=\"ip_logout\" type=\"checkbox\"";
    if ($rAdminSettings["ip_logout"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"token_telegram\">Token Telegram <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"token_telegram\" name=\"token_telegram\" value=\"";
    echo htmlspecialchars($rAdminSettings["token_telegram"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"chat_id\">Chat ID Telegram <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-4\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"chat_id\" name=\"chat_id\" value=\"";
    echo htmlspecialchars($rAdminSettings["chat_id"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"get_real_ip_client\">Cloudflare Connecting IP <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Type it - HTTP_CF_CONNECTING_IP\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-4\">\n                                                                <input type=\"text\" placeholder=\"HTTP_CF_CONNECTING_IP\" class=\"form-control\" id=\"get_real_ip_client\" name=\"get_real_ip_client\" value=\"";
    echo htmlspecialchars($rSettings["get_real_ip_client"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"login_flood\">";
    echo $_["maximum_login_attempts"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["how_many_login_attempts"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"login_flood\" name=\"login_flood\" value=\"";
    echo htmlspecialchars($rAdminSettings["login_flood"]) ?: 0;
    echo "\">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"pass_length\">";
    echo $_["minimum_pass_length"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["set_this_enforce_password"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"pass_length\" name=\"pass_length\" value=\"";
    echo htmlspecialchars($rAdminSettings["pass_length"]) ?: 0;
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"default_entries\">";
    echo $_["default_entries_show"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["default_entries_for_users"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"default_entries\" id=\"default_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
    foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
        echo "                                                                    <option";
        if ($rAdminSettings["default_entries"] == $rShow) {
            echo " selected";
        }
        echo " value=\"";
        echo $rShow;
        echo "\">";
        echo $rShow;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"google_2factor\">";
    echo $_["two_factor_authentication"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["enable_two_factor"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"google_2factor\" id=\"google_2factor\" type=\"checkbox\"";
    if ($rAdminSettings["google_2factor"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"local_api\">";
    echo $_["local_api"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["select_this_option"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"local_api\" id=\"local_api\" type=\"checkbox\"";
    if ($rAdminSettings["local_api"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"dark_mode_login\">";
    echo $_["dark_mode_login"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"dark_mode_login\" id=\"dark_mode_login\" type=\"checkbox\"";
    if ($rAdminSettings["dark_mode_login"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"dashboard_stats\">";
    echo $_["dashboard_stats"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["enable_dashboard_option"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"dashboard_stats\" id=\"dashboard_stats\" type=\"checkbox\"";
    if ($rAdminSettings["dashboard_stats"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"dashboard_stats_frequency\">";
    echo $_["stats_frequency"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["stats_interval"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"dashboard_stats_frequency\" name=\"dashboard_stats_frequency\" value=\"";
    echo htmlspecialchars($rAdminSettings["dashboard_stats_frequency"]) ?: 600;
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"dashboard_world_map_live\">Dashboard World Map Live <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable this option to show interactive connection statistics live on dashboard.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"dashboard_world_map_live\" id=\"dashboard_world_map_live\" type=\"checkbox\"";
    if ($rAdminSettings["dashboard_world_map_live"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"dashboard_world_map_activity\">Dashboard World Map Activity <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable this option to show interactive connection statistics activity on dashboard.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"dashboard_world_map_activity\" id=\"dashboard_world_map_activity\" type=\"checkbox\"";
    if ($rAdminSettings["dashboard_world_map_activity"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"download_images\">";
    echo $_["download_images"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["images_from_server_tmdb"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"download_images\" id=\"download_images\" type=\"checkbox\"";
    if ($rAdminSettings["download_images"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"auto_refresh\">";
    echo $_["auto-refresh_by_default"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["auto_refresh_pages_by_deault"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"auto_refresh\" id=\"auto_refresh\" type=\"checkbox\"";
    if ($rAdminSettings["auto_refresh"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"alternate_scandir\">";
    echo $_["alternate_scandir_method"];
    echo " (Cloud) <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["use_an_alternate_method"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"alternate_scandir\" id=\"alternate_scandir\" type=\"checkbox\"";
    if ($rAdminSettings["alternate_scandir"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"show_tickets\"> Show alert tickets<i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable/Disable alert tickets on dashboard\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"show_tickets\" id=\"show_tickets\" type=\"checkbox\"";
    if ($rAdminSettings["show_tickets"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"active_statistics\">Statistics <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable this option to show statistics Users M3U, Users Mags, Bouquets, Episodes, Movies; Servers, Channels, Radio, Series and Resellers on dashboard.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"active_statistics\" id=\"active_statistics\" type=\"checkbox\"";
    if ($rAdminSettings["active_statistics"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"order_streams\">Order Streams <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable if you want to order Streams in Manager Streams (Streams are ordered in Channel Order).\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"order_streams\" id=\"order_streams\" type=\"checkbox\"";
    if ($rAdminSettings["order_streams"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"";
    echo $_["save_changes"];
    echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"reseller\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"copyrights_text\">";
    echo $_["copyrights_text"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"copyrights_text\" name=\"copyrights_text\" value=\"";
    echo htmlspecialchars($rSettings["copyrights_text"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"disable_trial\">";
    echo $_["disable_trial"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["use_this_option_to_temporarily_disable_generating_trials"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"disable_trial\" id=\"disable_trial\" type=\"checkbox\"";
    if ($rAdminSettings["disable_trial"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller_restrictions\">";
    echo $_["allow_restrictions"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["set_this_option_to_allow_resellers"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reseller_restrictions\" id=\"reseller_restrictions\" type=\"checkbox\"";
    if ($rAdminSettings["reseller_restrictions"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"change_usernames\">";
    echo $_["change_usernames"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["set_this_option_to_allow_change_own_usernames"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"change_usernames\" id=\"change_usernames\" type=\"checkbox\"";
    if ($rAdminSettings["change_usernames"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"change_own_dns\">";
    echo $_["change_own_dns"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["set_this_option_to_allow_change_own_dns"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"change_own_dns\" id=\"change_own_dns\" type=\"checkbox\"";
    if ($rAdminSettings["change_own_dns"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"change_own_email\">";
    echo $_["change_own_email"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["set_this_option_to_allow_change_own_email"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"change_own_email\" id=\"change_own_email\" type=\"checkbox\"";
    if ($rAdminSettings["change_own_email"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"change_own_password\">";
    echo $_["change_own_password"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["set_this_option_to_allow_change_own_password"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"change_own_password\" id=\"change_own_password\" type=\"checkbox\"";
    if ($rAdminSettings["change_own_password"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"change_own_lang\">";
    echo $_["change_own_language_resellers"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["change_own_language_resellers_msg"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"change_own_lang\" id=\"change_own_lang\" type=\"checkbox\"";
    if ($rAdminSettings["change_own_lang"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"reseller_mag_events\">";
    echo $_["reseller_send_events"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["resellers_to_be_able_to_send_mag_events"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reseller_mag_events\" id=\"reseller_mag_events\" type=\"checkbox\"";
    if ($rAdminSettings["reseller_mag_events"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"reseller_can_isplock\">";
    echo $_["reseller_can_isplock"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["message_reseller_can_isplock"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reseller_can_isplock\" id=\"reseller_can_isplock\" type=\"checkbox\"";
    if ($rAdminSettings["reseller_can_isplock"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"reseller_reset_isplock\">";
    echo $_["reseller_reset_isplock"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["message_reseller_reset_isplock"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reseller_reset_isplock\" id=\"reseller_reset_isplock\" type=\"checkbox\"";
    if ($rAdminSettings["reseller_reset_isplock"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<!--<label class=\"col-md-4 col-form-label\" for=\"reseller_select_bouquets\">";
    echo $_["reseller_select_bouquets"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["message_reseller_select_bouquets"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reseller_select_bouquets\" id=\"reseller_select_bouquets\" type=\"checkbox\"";
    if ($rAdminSettings["reseller_select_bouquets"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>-->\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"active_mannuals\">";
    echo $_["active_mannuals"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["message_active_mannuals"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"active_mannuals\" id=\"active_mannuals\" type=\"checkbox\"";
    if ($rAdminSettings["active_mannuals"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"reseller_view_info\">Reseller can view Info Dashboard <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Active Reseller can view Info Dashboard\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reseller_view_info\" id=\"reseller_view_info\" type=\"checkbox\"";
    if ($rAdminSettings["reseller_view_info"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"active_apps\">Reseller can view APPS Dashboard <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Active Reseller can view APPS Dashboard\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"active_apps\" id=\"active_apps\" type=\"checkbox\"";
    if ($rAdminSettings["active_apps"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"reseller_mag_to_m3u\">Reseller can Convert MAG to M3U <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable or Disable convert MAG to M3U for Resellers\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reseller_mag_to_m3u\" id=\"reseller_mag_to_m3u\" type=\"checkbox\"";
    if ($rAdminSettings["reseller_mag_to_m3u"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n                                                    </div>\n                                                </div>\n\t\t\t\t\t\t\t\t\t\t\t\t<ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"";
    echo $_["save_changes"];
    echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"streaming\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"flood_limit\">";
    echo $_["flood_limit"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["enter_to_disable_flood_detection"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"flood_limit\" name=\"flood_limit\" value=\"";
    echo htmlspecialchars($rSettings["flood_limit"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"flood_seconds\">Request Frequency in Seconds <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Number of requests per second.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"flood_seconds\" name=\"flood_seconds\" value=\"";
    echo htmlspecialchars($rSettings["flood_seconds"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"flood_ips_exclude\">";
    echo $_["flood_ip_exclude"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["separate_each_ip"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"flood_ips_exclude\" name=\"flood_ips_exclude\" value=\"";
    echo htmlspecialchars($rSettings["flood_ips_exclude"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"use_https\">Main or Loadbalance Https</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"use_https\" name=\"use_https[]\" size=6 class=\"form-control\" multiple=\"multiple\">\n                                                                    ";
    $rSelected = json_decode($rSettings["use_https"], true);
    foreach ($rServers as $rServer) {
        echo "                                                                    <option ";
        if (isset($rSettings) && !empty($rSelected) && in_array($rServer["id"], $rSelected)) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rServer["id"];
        echo "\">";
        echo $rServer["server_name"];
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"use_https_main\">Use Https M3U Lines <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Active Https on M3U lines\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"use_https_main\" id=\"use_https_main\" type=\"checkbox\"";
    if ($rAdminSettings["use_https_main"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\t\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"user_auto_kick_hours\">";
    echo $_["auto_kick_users"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["automatically_kick_users"];
    echo " class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" id=\"user_auto_kick_hours\" name=\"user_auto_kick_hours\" value=\"";
    echo htmlspecialchars($rSettings["user_auto_kick_hours"]);
    echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"disallow_empty_user_agents\">";
    echo $_["disallow_empty_ua"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["dont_allow_connections_from_clients"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"disallow_empty_user_agents\" id=\"disallow_empty_user_agents\" type=\"checkbox\"";
    if ($rSettings["disallow_empty_user_agents"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"client_prebuffer\">";
    echo $_["client_prebuffer"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["how_much_data_will_be_sent_to_the_client_1"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"client_prebuffer\" name=\"client_prebuffer\" value=\"";
    echo htmlspecialchars($rSettings["client_prebuffer"]);
    echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"restreamer_prebuffer\">";
    echo $_["restreamer_prebuffer"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["how_much_data_will_be_sent_to_the_client_2"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"restreamer_prebuffer\" name=\"restreamer_prebuffer\" value=\"";
    echo htmlspecialchars($rSettings["restreamer_prebuffer"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"split_clients\">";
    echo $_["split_clients"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"split_clients\" id=\"split_clients\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option";
    if ($rSettings["split_clients"] == "equal") {
        echo " selected";
    }
    echo " value=\"equal\">";
    echo $_["equally"];
    echo "</option>\n                                                                    <option";
    if ($rSettings["split_clients"] == "load") {
        echo " selected";
    }
    echo " value=\"load\">";
    echo $_["load"];
    echo "</option>\n                                                                </select>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"split_by\">";
    echo $_["split_by"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"split_by\" id=\"split_by\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option";
    if ($rSettings["split_by"] == "conn") {
        echo " selected";
    }
    echo " value=\"conn\">";
    echo $_["connections"];
    echo "</option>\n                                                                    <option";
    if ($rSettings["split_by"] == "maxclients") {
        echo " selected";
    }
    echo " value=\"maxclients\">";
    echo $_["max_clients"];
    echo "</option>\n                                                                    <option";
    if ($rSettings["split_by"] == "guar_band") {
        echo " selected";
    }
    echo " value=\"guar_band\">";
    echo $_["network_speed"];
    echo "</option>\n                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"channel_number_type\">";
    echo $_["channel_sorting_type"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"channel_number_type\" id=\"channel_number_type\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    <option";
    if ($rSettings["channel_number_type"] == "bouquet") {
        echo " selected";
    }
    echo " value=\"bouquet\">";
    echo $_["bouquet"];
    echo "</option>\n                                                                    <option";
    if ($rSettings["channel_number_type"] == "manual") {
        echo " selected";
    }
    echo " value=\"manual\">";
    echo $_["manual"];
    echo "</option>\n                                                                </select>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"new_sorting_bouquet\">";
    echo $_["new_sorting_bouquet"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"new_sorting_bouquet\" id=\"new_sorting_bouquet\" type=\"checkbox\"";
    if ($rSettings["new_sorting_bouquet"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_max_analyze\">";
    echo $_["analysis_duration"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["longer duration"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"stream_max_analyze\" name=\"stream_max_analyze\" value=\"";
    echo htmlspecialchars($rSettings["stream_max_analyze"]);
    echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"probesize\">";
    echo $_["probe_size"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["probed_in_bytes"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"probesize\" name=\"probesize\" value=\"";
    echo htmlspecialchars($rSettings["probesize"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"persistent_connections\">";
    echo $_["persistent_connections"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["enable_PHP_persistent_connections"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"persistent_connections\" id=\"persistent_connections\" type=\"checkbox\"";
    if ($rSettings["persistent_connections"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"rtmp_random\">";
    echo $_["random_rtmp_ip"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["use_random_ip_for_rmtp"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"rtmp_random\" id=\"rtmp_random\" type=\"checkbox\"";
    if ($rSettings["rtmp_random"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stream_start_delay\">";
    echo $_["stream_start_delay"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["before_starting_stream"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"stream_start_delay\" name=\"stream_start_delay\" value=\"";
    echo htmlspecialchars($rSettings["stream_start_delay"]);
    echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"online_capacity_interval\">";
    echo $_["online_capacity_interval"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["interval_at_which_to_check"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"online_capacity_interval\" name=\"online_capacity_interval\" value=\"";
    echo htmlspecialchars($rSettings["online_capacity_interval"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"use_buffer\">";
    echo $_["use_nginx_buffer"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["proxy_buffering"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"use_buffer\" id=\"use_buffer\" type=\"checkbox\"";
    if ($rSettings["use_buffer"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"audio_restart_loss\">";
    echo $_["restart_on_audio_loss"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["restart_stream_periodically"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"audio_restart_loss\" id=\"audio_restart_loss\" type=\"checkbox\"";
    if ($rSettings["audio_restart_loss"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"save_closed_connection\">";
    echo $_["save_connection_logs"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["save_closed_connection_database"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"save_closed_connection\" id=\"save_closed_connection\" type=\"checkbox\"";
    if ($rSettings["save_closed_connection"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"client_logs_save\">";
    echo $_["save_client_logs"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["save_client_logs_to_database"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"client_logs_save\" id=\"client_logs_save\" type=\"checkbox\"";
    if ($rSettings["client_logs_save"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"case_sensitive_line\">";
    echo $_["case_sensitive_details"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["case_sensitive"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"case_sensitive_line\" id=\"case_sensitive_line\" type=\"checkbox\"";
    if ($rSettings["case_sensitive_line"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"county_override_1st\">";
    echo $_["override_country_with_first"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["override_country_with_connected"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"county_override_1st\" id=\"county_override_1st\" type=\"checkbox\"";
    if ($rSettings["county_override_1st"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"disallow_2nd_ip_con\">";
    echo $_["disallow_2nd_ip_connection"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["disallow_connection"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"disallow_2nd_ip_con\" id=\"disallow_2nd_ip_con\" type=\"checkbox\"";
    if ($rSettings["disallow_2nd_ip_con"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"firewall\">";
    echo $_["enable_xc_firewall"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["enable_xtream_codes"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"firewall\" id=\"firewall\" type=\"checkbox\"";
    if ($rSettings["firewall"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"use_mdomain_in_lists\">";
    echo $_["use_domain_in_lists"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["use_domaine_name"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"use_mdomain_in_lists\" id=\"use_mdomain_in_lists\" type=\"checkbox\"";
    if ($rSettings["use_mdomain_in_lists"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"hash_lb\">";
    echo $_["hash_load_balancers"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["any_client_is_being_redirected"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"hash_lb\" id=\"hash_lb\" type=\"checkbox\"";
    if ($rSettings["hash_lb"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"show_isps\">";
    echo $_["enable_isps"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["grab_isp_information"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"show_isps\" id=\"show_isps\" type=\"checkbox\"";
    if ($rSettings["show_isps"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"enable_isp_lock\">";
    echo $_["enable_isp_lock1"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["enable_isp_lock_msg"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"enable_isp_lock\" id=\"enable_isp_lock\" type=\"checkbox\"";
    if ($rSettings["enable_isp_lock"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div> \n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"vod_bitrate_plus\">VOD Download Speed <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Specify the bitrate here in kbps. Enter number only. 2000 kB/s = 2 MB/s.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"vod_bitrate_plus\" name=\"vod_bitrate_plus\" value=\"";
    echo htmlspecialchars($rSettings["vod_bitrate_plus"]);
    echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"vod_limit_at\">VOD Download Limit <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Specify the percentage. Enter number only. Enter 0 to disable.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"vod_limit_at\" name=\"vod_limit_at\" value=\"";
    echo htmlspecialchars($rSettings["vod_limit_at"]);
    echo "\">\n                                                            </div>\n                                                        </div> \n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"block_svp\">Block VPN & PROXIES & SERVERS <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"This setting will prevent users connected from Servers/VPN/Proxies to open connection to your Servers. No matter what you will select, the log about that action will be written below. Please note that Restreamers (Is Restreamer = Yes under Add/Edit User) are not affected by this setting and no log will be written.\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"block_svp\" id=\"block_svp\" type=\"checkbox\"";
    if ($rSettings["block_svp"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"priority_backup\">Priority Backup Stream <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable if you want the first backup stream to be a priority if you are online\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"priority_backup\" id=\"priority_backup\" type=\"checkbox\"";
    if ($rSettings["priority_backup"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"show_not_on_air_video\">";
    echo $_["stream_down_video"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["show_this_video"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"show_not_on_air_video\" id=\"show_not_on_air_video\" type=\"checkbox\"";
    if ($rSettings["show_not_on_air_video"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <div class=\"col-md-6\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"not_on_air_video_path\" name=\"not_on_air_video_path\" value=\"";
    echo htmlspecialchars($rSettings["not_on_air_video_path"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"show_banned_video\">";
    echo $_["banned_video"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["show_this_video_banned"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"show_banned_video\" id=\"show_banned_video\" type=\"checkbox\"";
    if ($rSettings["show_banned_video"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <div class=\"col-md-6\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"banned_video_path\" name=\"banned_video_path\" value=\"";
    echo htmlspecialchars($rSettings["banned_video_path"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"show_expired_video\">";
    echo $_["expired_video"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["show_this_video_expired"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"show_expired_video\" id=\"show_expired_video\" type=\"checkbox\"";
    if ($rSettings["show_expired_video"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <div class=\"col-md-6\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"expired_video_path\" name=\"expired_video_path\" value=\"";
    echo htmlspecialchars($rSettings["expired_video_path"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allowed_ips_admin\">";
    echo $_["admin_streaming_ips"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["allowed_ip_to_access"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"allowed_ips_admin\" name=\"allowed_ips_admin\" value=\"";
    echo htmlspecialchars($rSettings["allowed_ips_admin"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"api_ips\">";
    echo $_["api_ips"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["allowed_ip_to_access_api"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"api_ips\" name=\"api_ips\" value=\"";
    echo htmlspecialchars($rSettings["api_ips"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"message_of_day\">";
    echo $_["message_of_the_day"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["message_to_display_api"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"message_of_day\" name=\"message_of_day\" value=\"";
    echo htmlspecialchars($rSettings["message_of_day"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n\t\t\t\t\t\t\t\t\t\t\t\t<ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"";
    echo $_["save_changes"];
    echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"mag\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"show_all_category_mag\">";
    echo $_["show_all_categories"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["show_all_mag_category_on_mag_devices"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"show_all_category_mag\" id=\"show_all_category_mag\" type=\"checkbox\"";
    if ($rSettings["show_all_category_mag"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"mag_security\">";
    echo $_["mag_security"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["enable_additional_mag"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"mag_security\" id=\"mag_security\" type=\"checkbox\"";
    if ($rSettings["mag_security"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"always_enabled_subtitles\">";
    echo $_["always_enabled_subtitles"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["force_subtitles"];
    echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"always_enabled_subtitles\" id=\"always_enabled_subtitles\" type=\"checkbox\"";
    if ($rSettings["always_enabled_subtitles"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"enable_connection_problem_indication\">";
    echo $_["connection_problem_indication"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"enable_connection_problem_indication\" id=\"enable_connection_problem_indication\" type=\"checkbox\"";
    if ($rSettings["enable_connection_problem_indication"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"show_tv_channel_logo\">";
    echo $_["show_channel_logos"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"show_tv_channel_logo\" id=\"show_tv_channel_logo\" type=\"checkbox\"";
    if ($rSettings["show_tv_channel_logo"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"show_channel_logo_in_preview\">";
    echo $_["show_preview_channel_logos"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"show_channel_logo_in_preview\" id=\"show_channel_logo_in_preview\" type=\"checkbox\"";
    if ($rSettings["show_channel_logo_in_preview"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stb_change_pass\">";
    echo $_["allow_stb_pass_change"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"stb_change_pass\" id=\"stb_change_pass\" type=\"checkbox\"";
    if ($rSettings["stb_change_pass"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"enable_debug_stalker\">";
    echo $_["stalker_debug"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"enable_debug_stalker\" id=\"enable_debug_stalker\" type=\"checkbox\"";
    if ($rSettings["enable_debug_stalker"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stalker_lock_images\">Mag Lock Image</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"stalker_lock_images\" id=\"stalker_lock_images\" type=\"checkbox\"";
    if ($rSettings["stalker_lock_images"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"portal_block\">Disable Mag Portal</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"portal_block\" id=\"portal_block\" type=\"checkbox\"";
    if ($rSettings["portal_block"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"mag_container\">";
    echo $_["default_container"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"mag_container\" id=\"mag_container\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
    foreach (["ts" => "TS", "m3u8" => "M3U8"] as $rValue => $rText) {
        echo "                                                                    <option ";
        if ($rSettings["mag_container"] == $rValue) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rValue;
        echo "\">";
        echo $rText;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"stalker_theme\">";
    echo $_["default_theme"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"stalker_theme\" id=\"stalker_theme\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
    foreach (["default" => "Default", "digital" => "Digital", "emerald" => "Emerald", "cappucino" => "Cappucino", "ocean_blue" => "Ocean Blue"] as $rValue => $rText) {
        echo "                                                                    <option ";
        if ($rSettings["stalker_theme"] == $rValue) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rValue;
        echo "\">";
        echo $rText;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"record_max_length\">";
    echo $_["record_max_length"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"record_max_length\" name=\"record_max_length\" value=\"";
    echo htmlspecialchars($rSettings["record_max_length"]);
    echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tv_channel_default_aspect\">Default Aspect Ratio</label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"tv_channel_default_aspect\" id=\"tv_channel_default_aspect\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
    foreach (["fit" => "fit", "big" => "big", "opt" => "opt", "exp" => "exp", "cmb" => "cmb"] as $rValue => $rText) {
        echo "                                                                    <option ";
        if ($rSettings["tv_channel_default_aspect"] == $rValue) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rValue;
        echo "\">";
        echo $rText;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                             <label class=\"col-md-4 col-form-label\" for=\"playback_limit\">";
    echo $_["playback_limit"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"playback_limit\" name=\"playback_limit\" value=\"";
    echo htmlspecialchars($rSettings["playback_limit"]);
    echo "\">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"max_local_recordings\">";
    echo $_["max_local_recordings"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"max_local_recordings\" name=\"max_local_recordings\" value=\"";
    echo htmlspecialchars($rSettings["max_local_recordings"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"test_download_url\">Teste Download URL <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"test_download_url\" name=\"test_download_url\" value=\"";
    echo htmlspecialchars($rSettings["test_download_url"]);
    echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allowed_stb_types\">";
    echo $_["allowed_stb_types"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"allowed_stb_types[]\" id=\"allowed_stb_types\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
    echo $_["choose"];
    echo "...\">\n                                                                    ";
    foreach ($rMAGs as $rMAG) {
        echo "                                                                    <option ";
        if (in_array($rMAG, json_decode($rSettings["allowed_stb_types"], true))) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rMAG;
        echo "\">";
        echo $rMAG;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allowed_stb_types_rec\">Allow Recording</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"allowed_stb_types_rec\" id=\"allowed_stb_types_rec\" type=\"checkbox\"";
    if ($rSettings["allowed_stb_types_rec"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allowed_stb_types_for_local_recording\">";
    echo $_["allowed_stb_recording"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"allowed_stb_types_for_local_recording[]\" id=\"allowed_stb_types_for_local_recording\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
    echo $_["choose"];
    echo "...\">\n                                                                    ";
    foreach ($rMAGs as $rMAG) {
        echo "                                                                    <option ";
        if (in_array($rMAG, json_decode($rSettings["allowed_stb_types_for_local_recording"], true))) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rMAG;
        echo "\">";
        echo $rMAG;
        echo "</option>\n                                                                    ";
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n\t\t\t\t\t\t\t\t\t\t\t\t<ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"";
    echo $_["save_changes"];
    echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t";
}
if (hasPermissions("adv", "database")) {
    echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"tab-pane\" id=\"infos\">\n\t\t\t\t\t\t\t\t\t\t\t</br><center><a href=\"https://commerce.coinbase.com/checkout/983daf34-bebb-4cd5-8e10-45c600630199\">\n\t\t\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-xl\"><i class=\"mdi mdi-currency-btc\"></i> Donate via CoinBase</button></a></center>\n\t\t\t\t\t\t\t\t\t\t\t</a>\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                    \t<div class=\"card\">\n                                \t\t\t\t\t\t\t<div class=\"card-body\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"bg-soft-light border-light border\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"text-muted mb-0 mt-3 text-left\"></small><b><a class=\"text-dark\">";
    echo $rInfos["title"][0];
    echo "</a></b></p>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<h5 class=\"font-weight-normal mb-3\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span>";
    echo $rInfos["infos"][0];
    echo "<sup class=\"font-13\"> ";
    echo $rInfos["infos"][1];
    echo "</sup></span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</h5>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                </div> <!-- end col -->\n                                            </div> <!-- end row -->\n                                        </div> <!-- tab-content -->\n\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </div> <!-- end card-body -->\n                            </div> <!-- end card-->\n                            <div class=\"modal fade bs-domains\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"modalLabel\" aria-hidden=\"true\" style=\"display: none;\">\n                                <div class=\"modal-dialog modal-dialog-centered\">\n                                    <div class=\"modal-content\">\n                                        <div class=\"modal-header\">\n                                            <h4 class=\"modal-title\" id=\"modalLabel\">";
echo $_["domain_list"];
echo "</h4>\n                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n                                        </div>\n                                        <div class=\"modal-body\">\n                                            <p class=\"sub-header\">";
echo $_["ensure_the_following_domains"];
echo "</p>\n                                            <div class=\"table-responsive\">\n                                                <table class=\"table mb-0\">\n                                                    <thead>\n                                                        <tr>\n                                                            <th>";
echo $_["type_reseller"];
echo "</th>\n                                                            <th>";
echo $_["domaine_name"];
echo "</th>\n                                                        </tr>\n                                                    </thead>\n                                                    <tbody>\n                                                        ";
if (0 < strlen($rServers[$_INFO["server_id"]]["server_ip"])) {
    echo "                                                        <tr>\n                                                            <td>";
    echo $_["server_ip"];
    echo "</td>\n                                                            <td>";
    echo $rServers[$_INFO["server_id"]]["server_ip"];
    echo "</td>\n                                                        </tr>\n                                                        ";
}
if (0 < strlen($rServers[$_INFO["server_id"]]["vpn_ip"])) {
    echo "                                                        <tr>\n                                                            <td>";
    echo $_["server_vpn"];
    echo "</td>\n                                                            <td>";
    echo $rServers[$_INFO["server_id"]]["vpn_ip"];
    echo "</td>\n                                                        </tr>\n                                                        ";
}
if (0 < strlen($rServers[$_INFO["server_id"]]["domain_name"])) {
    echo "                                                        <tr>\n                                                            <td>";
    echo $_["server_domain"];
    echo "</td>\n                                                            <td>";
    echo $rServers[$_INFO["server_id"]]["domain_name"];
    echo "</td>\n                                                        </tr>\n                                                        ";
}
$result = $db->query("SELECT `username`, `reseller_dns` FROM `reg_users` WHERE `reseller_dns` <> '' AND `verified` = 1 ORDER BY `username` ASC;");
if ($result && 0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        echo "                                                        <tr>\n                                                            <td>";
        echo $row["username"];
        echo "</td>\n                                                            <td>";
        echo $row["reseller_dns"];
        echo "</td>\n                                                        </tr>\n                                                        ";
    }
}
echo "                                                    </tbody>\n                                                </table>\n                                            </div>\n                                        </div>\n                                    </div><!-- /.modal-content -->\n                                </div><!-- /.modal-dialog -->\n                            </div><!-- /.modal -->\n                        </div> <!-- end col -->\n                    </div>\n                </form>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\t\t<link rel=\"stylesheet\" href=\"assets/js/minified/themes/default.min.css\" id=\"theme-style\" />\n\t\t<script src=\"assets/js/minified/sceditor.min.js\"></script>\n        <script src=\"assets/js/minified/formats/xhtml.js\"></script>\t\t\t\t\t\t \n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["are_you_sure_you_want_to_delete_this_backup"];
echo "') == false) {\n                    return;\n                }\n            } else if (rType == \"restore\") {\n                if (confirm('";
echo $_["are_you_sure_you_want_to_restore_from_this_backup"];
echo "') == false) {\n                    return;\n                } else {\n\t\t\t\t\t\$.toast(\"";
echo $_["restoring_backup"];
echo "\");\n\t\t\t\t\t\$(\".content-page\").fadeOut();\n\t\t\t\t}\n            } else if (rType == \"backup\") {\n                \$(\"#create_backup\").attr(\"disabled\", true);\n\t\t\t} else if (rType == \"download\") {\n                window.location.href = \"./api.php?action=download&filename=\" + encodeURIComponent(rID);\n            }\n            \$.getJSON(\"./api.php?action=backup&sub=\" + rType + \"&filename=\" + encodeURIComponent(rID), function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$.each(\$('.tooltip'), function (index, element) {\n                            \$(this).remove();\n                        });\n                        \$('[data-toggle=\"tooltip\"]').tooltip();\n                        \$.toast(\"";
echo $_["backup_successfully_deleted"];
echo "\");\n                    } else if (rType == \"restore\") {\n                        \$.toast(\"";
echo $_["restored_from_backup"];
echo "\");\n\t\t\t\t\t\t\$(\".content-page\").fadeIn();\n                    } else if (rType == \"backup\") {\n                        \$.toast(\"";
echo $_["backup_has_been_successfully_generated"];
echo "\");\n                        \$(\"#create_backup\").attr(\"disabled\", false);\n                    }\n\t\t\t\t\t\$(\"#datatable-backups\").DataTable().ajax.reload(null, false);\n                } else {\n                    \$.toast(\"";
echo $_["an_error_occured_while_processing_your_request"];
echo "\");\n                    if (rType == \"backup\") {\n                        \$(\"#create_backup\").attr(\"disabled\", false);\n                    }\n\t\t\t\t\tif (!\$(\".content-page\").is(\":visible\")) {\n\t\t\t\t\t\t\$(\".content-page\").fadeIn();\n\t\t\t\t\t}\n                }\n            });\n        }\n        \n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'});\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n              var switchery = new Switchery(html);\n            });\n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"#datatable-backups\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n\t\t\t\tbInfo: false,\n\t\t\t\tpaging: false,\n\t\t\t\tsearching: false,\n\t\t\t\tbSort: false,\n                responsive: false,\n\t\t\t\tprocessing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"backups\"\n                    }\n                },\n                order: [[ 0, \"desc\" ]],\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2,3]}\n                ],\n\t\t\t\t\n            });\n            \$(\"#datatable-backups\").css(\"width\", \"100%\");\n            \$(\"form\").attr('autocomplete', 'off');\n            \$(\"#flood_limit\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#user_auto_kick_hours\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#probesize\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#stream_max_analyze\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#client_prebuffer\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#restreamer_prebuffer\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#backups_to_keep\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n        });\n\t\t</script>\n\t\t<script>\n\t\tvar textarea = document.getElementById('userpanel_mainpage');\n\t\t\tsceditor.create(textarea, {\n\t\t\t\tformat: 'bbcode',\n\t\t\t\ticons: 'monocons',\n\t\t\t\tstyle: '../assets/js/minified/themes/content/default.min.css'\n\t\t\t});\n\n\n\t\t\tvar themeInput = document.getElementById('theme');\n\t\t\tthemeInput.onchange = function() {\n\t\t\t\tvar theme = '../assets/js/minified/themes/' + themeInput.value + '.min.css';\n\n\t\t\t\tdocument.getElementById('theme-style').href = theme;\n\t\t\t};\n        </script>\n\t\t<script>\n\t\tvar textarea = document.getElementById('page_mannuals');\n\t\t\tsceditor.create(textarea, {\n\t\t\t\tformat: 'bbcode',\n\t\t\t\ticons: 'monocons',\n\t\t\t\tstyle: '../assets/js/minified/themes/content/default.min.css'\n\t\t\t});\n\n\n\t\t\tvar themeInput = document.getElementById('theme');\n\t\t\tthemeInput.onchange = function() {\n\t\t\t\tvar theme = '../assets/js/minified/themes/' + themeInput.value + '.min.css';\n\n\t\t\t\tdocument.getElementById('theme-style').href = theme;\n\t\t\t};\n        </script>\n    </body>\n</html>";

?>