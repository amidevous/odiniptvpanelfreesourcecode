<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_group") && !hasPermissions("adv", "edit_group")) {
    exit;
}
$rAdvPermissions = [["add_rtmp", $_["permission_add_rtmp"], $_["permission_add_rtmp_text"]], ["add_bouquet", $_["permission_add_bouquet"], $_["permission_add_bouquet_text"]], ["add_cat", $_["permission_add_cat"], $_["permission_add_cat_text"]], ["add_e2", $_["permission_add_e2"], $_["permission_add_e2_text"]], ["add_epg", $_["permission_add_epg"], $_["permission_add_epg_text"]], ["add_episode", $_["permission_add_episode"], $_["permission_add_episode_text"]], ["add_group", $_["permission_add_group"], $_["permission_add_group_text"]], ["add_mag", $_["permission_add_mag"], $_["permission_add_mag_text"]], ["add_movie", $_["permission_add_movie"], $_["permission_add_movie_text"]], ["add_packages", $_["permission_add_packages"], $_["permission_add_packages_text"]], ["add_radio", $_["permission_add_radio"], $_["permission_add_radio_text"]], ["add_reguser", $_["permission_add_reguser"], $_["permission_add_reguser_text"]], ["add_server", $_["permission_add_server"], $_["permission_add_server_text"]], ["add_stream", $_["permission_add_stream"], $_["permission_add_stream_text"]], ["tprofile", $_["permission_tprofile"], $_["permission_tprofile_text"]], ["add_series", $_["permission_add_series"], $_["permission_add_series_text"]], ["add_user", $_["permission_add_user"], $_["permission_add_user_text"]], ["block_ips", $_["permission_block_ips"], $_["permission_block_ips_text"]], ["block_isps", $_["permission_block_isps"], $_["permission_block_isps_text"]], ["block_uas", $_["permission_block_uas"], $_["permission_block_uas_text"]], ["create_channel", $_["permission_create_channel"], $_["permission_create_channel_text"]], ["edit_bouquet", $_["permission_edit_bouquet"], $_["permission_edit_bouquet_text"]], ["edit_cat", $_["permission_edit_cat"], $_["permission_edit_cat_text"]], ["channel_order", $_["permission_channel_order"], $_["permission_channel_order_text"]], ["edit_cchannel", $_["permission_edit_cchannel"], $_["permission_edit_cchannel_text"]], ["edit_e2", $_["permission_edit_e2"], $_["permission_edit_e2_text"]], ["epg_edit", $_["permission_epg_edit"], $_["permission_epg_edit_text"]], ["edit_episode", $_["permission_edit_episode"], $_["permission_edit_episode_text"]], ["folder_watch_settings", $_["permission_folder_watch_settings"], $_["permission_folder_watch_settings_text"]], ["settings", $_["permission_settings"], $_["permission_settings_text"]], ["edit_group", $_["permission_edit_group"], $_["permission_edit_group_text"]], ["edit_mag", $_["permission_edit_mag"], $_["permission_edit_mag_text"]], ["edit_movie", $_["permission_edit_movie"], $_["permission_edit_movie_text"]], ["edit_package", $_["permission_edit_package"], $_["permission_edit_package_text"]], ["edit_radio", $_["permission_edit_radio"], $_["permission_edit_radio_text"]], ["edit_reguser", $_["permission_edit_reguser"], $_["permission_edit_reguser_text"]], ["edit_server", $_["permission_edit_server"], $_["permission_edit_server_text"]], ["edit_stream", $_["permission_edit_stream"], $_["permission_edit_stream_text"]], ["edit_series", $_["permission_edit_series"], $_["permission_edit_series_text"]], ["edit_user", $_["permission_edit_user"], $_["permission_edit_user_text"]], ["fingerprint", $_["permission_fingerprint"], $_["permission_fingerprint_text"]], ["import_episodes", $_["permission_import_episodes"], $_["permission_import_episodes_text"]], ["import_movies", $_["permission_import_movies"], $_["permission_import_movies_text"]], ["import_streams", $_["permission_import_streams"], $_["permission_import_streams_text"]], ["database", $_["permission_database"], $_["permission_database_text"]], ["mass_delete", $_["permission_mass_delete"], $_["permission_mass_delete_text"]], ["mass_sedits_vod", $_["permission_mass_sedits_vod"], $_["permission_mass_sedits_vod_text"]], ["mass_sedits", $_["permission_mass_sedits"], $_["permission_mass_sedits_text"]], ["mass_edit_users", $_["permission_mass_edit_users"], $_["permission_mass_edit_users_text"]], ["mass_edit_streams", $_["permission_mass_edit_streams"], $_["permission_mass_edit_streams_text"]], ["mass_edit_radio", $_["permission_mass_edit_radio"], $_["permission_mass_edit_radio_text"]], ["ticket", $_["permission_ticket"], $_["permission_ticket_text"]], ["subreseller", $_["permission_subreseller"], $_["permission_subreseller_text"]], ["stream_tools", $_["permission_stream_tools"], $_["permission_stream_tools_text"]], ["bouquets", $_["permission_bouquets"], $_["permission_bouquets_text"]], ["categories", $_["permission_categories"], $_["permission_categories_text"]], ["client_request_log", $_["permission_client_request_log"], $_["permission_client_request_log_text"]], ["connection_logs", $_["permission_connection_logs"], $_["permission_connection_logs_text"]], ["manage_cchannels", $_["permission_manage_cchannels"], $_["permission_manage_cchannels_text"]], ["credits_log", $_["permission_credits_log"], $_["permission_credits_log_text"]], ["index", $_["permission_index"], $_["permission_index_text"]], ["manage_e2", $_["permission_manage_e2"], $_["permission_manage_e2_text"]], ["epg", $_["permission_epg"], $_["permission_epg_text"]], ["folder_watch", $_["permission_folder_watch"], $_["permission_folder_watch_text"]], ["folder_watch_output", $_["permission_folder_watch_output"], $_["permission_folder_watch_output_text"]], ["mng_groups", $_["permission_mng_groups"], $_["permission_mng_groups_text"]], ["live_connections", $_["permission_live_connections"], $_["permission_live_connections_text"]], ["login_logs", $_["permission_login_logs"], $_["permission_login_logs_text"]], ["manage_mag", $_["permission_manage_mag"], $_["permission_manage_mag_text"]], ["manage_events", $_["permission_manage_events"], $_["permission_manage_events_text"]], ["movies", $_["permission_movies"], $_["permission_movies_text"]], ["mng_packages", $_["permission_mng_packages"], $_["permission_mng_packages_text"]], ["player", $_["permission_player"], $_["permission_player_text"]], ["process_monitor", $_["permission_process_monitor"], $_["permission_process_monitor_text"]], ["radio", $_["permission_radio"], $_["permission_radio_text"]], ["mng_regusers", $_["permission_mng_regusers"], $_["permission_mng_regusers_text"]], ["reg_userlog", $_["permission_reg_userlog"], $_["permission_reg_userlog_text"]], ["rtmp", $_["permission_rtmp"], $_["permission_rtmp_text"]], ["servers", $_["permission_servers"], $_["permission_servers_text"]], ["stream_errors", $_["permission_stream_errors"], $_["permission_stream_errors_text"]], ["streams", $_["permission_streams"], $_["permission_streams_text"]], ["subresellers", $_["permission_subresellers"], $_["permission_subresellers_text"]], ["manage_tickets", $_["permission_manage_tickets"], $_["permission_manage_tickets_text"]], ["tprofiles", $_["permission_tprofiles"], $_["permission_tprofiles_text"]], ["series", $_["permission_series"], $_["permission_series_text"]], ["users", $_["permission_users"], $_["permission_users_text"]], ["episodes", $_["permission_episodes"], $_["permission_episodes_text"]], ["edit_tprofile", $_["permission_edit_tprofile"], $_["permission_edit_tprofile_text"]], ["folder_watch_add", $_["permission_folder_watch_add"], $_["permission_folder_watch_add_text"]], ["panel_errors", $_["permission_panel_errors"], $_["permission_panel_errors_text"]]];
if (isset($_POST["submit_group"])) {
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_group")) {
            exit;
        }
        $rArray = getMemberGroup($_POST["edit"]);
        $rGroup = $rArray;
        unset($rArray["group_id"]);
    } else {
        if (!hasPermissions("adv", "add_group")) {
            exit;
        }
        $rArray = ["group_name" => "", "group_color" => "", "is_banned" => 0, "is_admin" => 0, "is_reseller" => 0, "total_allowed_gen_in" => "day", "total_allowed_gen_trials" => 0, "minimum_trial_credits" => 0, "can_delete" => 1, "delete_users" => 0, "allowed_pages" => "", "reseller_force_server" => "", "create_sub_resellers_price" => 0, "create_sub_resellers" => 0, "alter_packages_ids" => 0, "alter_packages_prices" => 0, "reseller_client_connection_logs" => 0, "reseller_assign_pass" => 0, "allow_change_pass" => 0, "allow_import" => 0, "allow_export" => 0, "reseller_trial_credit_allow" => 0, "edit_mac" => 0, "edit_isplock" => 0, "reset_stb_data" => 0, "reseller_bonus_package_inc" => 0, "allow_download" => 1, "reseller_can_select_bouquets" => 0];
    }
    if (strlen($_POST["group_name"]) == 0) {
        $_STATUS = 1;
    }
    foreach (["is_admin", "is_reseller", "is_banned", "delete_users", "create_sub_resellers", "allow_change_pass", "allow_download", "reseller_client_connection_logs", "reset_stb_data", "allow_import", "reseller_can_select_bouquets"] as $rSelection) {
        if (isset($_POST[$rSelection])) {
            $rArray[$rSelection] = 1;
            unset($_POST[$rSelection]);
        } else {
            $rArray[$rSelection] = 0;
        }
    }
    if (!$rArray["can_delete"] && isset($_POST["edit"])) {
        $rArray["is_admin"] = $rGroup["is_admin"];
        $rArray["is_reseller"] = $rGroup["is_reseller"];
    }
    $rArray["allowed_pages"] = array_values(json_decode($_POST["permissions_selected"], true));
    unset($_POST["permissions_selected"]);
    if (!isset($_STATUS)) {
        foreach ($_POST as $rKey => $rValue) {
            if (isset($rArray[$rKey])) {
                $rArray[$rKey] = $rValue;
            }
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
            $rCols = "`group_id`," . $rCols;
            $rValues = ESC($_POST["edit"]) . "," . $rValues;
        }
        $rQuery = "REPLACE INTO `member_groups`(" . $rCols . ") VALUES(" . $rValues . ");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
            } else {
                $rInsertID = $db->insert_id;
            }
            header("Location: ./group.php?successedit&id=" . $rInsertID);
            exit;
        }
        $_STATUS = 2;
    }
}
if (isset($_GET["id"])) {
    $rGroup = getMemberGroup($_GET["id"]);
    if (!$rGroup || !hasPermissions("adv", "edit_group")) {
        exit;
    }
} else {
    if (!hasPermissions("adv", "add_group")) {
        exit;
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./groups.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_groups"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rGroup)) {
    echo $_["edit_group"];
} else {
    echo $_["add_group"];
}
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n\t\t\t\t\t\t\t";
        echo $_["group_success"];
        echo "                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["group_success"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n\t\t\t\t\t\t\t";
            echo $_["generic_fail"];
            echo "                        </div>\n                        ";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["generic_fail"];
            echo "', \"warning\");\n  \t\t\t\t\t</script> \n                        ";
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./group.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"group_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rGroup)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rGroup["group_id"];
    echo "\" />\n                                    ";
}
echo "\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"permissions_selected\" id=\"permissions_selected\" value=\"\" />\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#group-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n\t\t\t\t\t\t\t\t\t\t\t<li class=\"nav-item\">\n                                                <a href=\"#reseller\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-badge-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["reseller_permissions"];
echo "</span>\n                                                </a>\n                                            </li>\n\t\t\t\t\t\t\t\t\t\t\t";
if (!isset($rGroup) || $rGroup["can_delete"]) {
    echo "\t\t\t\t\t\t\t\t\t\t\t<li class=\"nav-item\">\n                                                <a href=\"#permissions\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-badge-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["admin_permissions"];
    echo "</span>\n                                                </a>\n                                            </li>\n\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"group-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"group_name\">";
echo $_["group_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"group_name\" name=\"group_name\" value=\"";
if (isset($rGroup)) {
    echo htmlspecialchars($rGroup["group_name"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_admin\">";
echo $_["is_admin"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_admin\" id=\"is_admin\" type=\"checkbox\" ";
if (isset($rGroup)) {
    if ($rGroup["is_admin"]) {
        echo "checked ";
    }
    if (!$rGroup["can_delete"]) {
        echo "disabled ";
    }
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_reseller\">";
echo $_["is_reseller"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_reseller\" id=\"is_reseller\" type=\"checkbox\" ";
if (isset($rGroup)) {
    if ($rGroup["is_reseller"]) {
        echo "checked ";
    }
    if (!$rGroup["can_delete"]) {
        echo "disabled ";
    }
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_banned\">";
echo $_["is_banned"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_banned\" id=\"is_banned\" type=\"checkbox\" ";
if (isset($rGroup) && $rGroup["is_banned"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_group\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rGroup)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"tab-pane\" id=\"reseller\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"sub-header\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
echo $_["permissions_info"];
echo "                                                        </p>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"total_allowed_gen_trials\">";
echo $_["allowed_trials"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"total_allowed_gen_trials\" name=\"total_allowed_gen_trials\" value=\"";
if (isset($rGroup)) {
    echo intval($rGroup["total_allowed_gen_trials"]);
} else {
    echo "0";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"total_allowed_gen_in\">";
echo $_["allowed_trials_in"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <select name=\"total_allowed_gen_in\" id=\"total_allowed_gen_in\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
foreach (["Day", "Month"] as $rOption) {
    echo "                                                                    <option ";
    if (isset($rGroup) && $rGroup["total_allowed_gen_in"] == strtolower($rOption)) {
        echo "selected ";
    }
    echo "value=\"";
    echo strtolower($rOption);
    echo "\">";
    echo $rOption;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"create_sub_resellers\">";
echo $_["can_create_subresellers"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"create_sub_resellers\" id=\"create_sub_resellers\" type=\"checkbox\" ";
if (isset($rGroup) && $rGroup["create_sub_resellers"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"create_sub_resellers_price\">";
echo $_["subreseller_price"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"create_sub_resellers_price\" name=\"create_sub_resellers_price\" value=\"";
if (isset($rGroup)) {
    echo htmlspecialchars($rGroup["create_sub_resellers_price"]);
} else {
    echo "0";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allow_change_pass\">";
echo $_["can_change_logins"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"allow_change_pass\" id=\"allow_change_pass\" type=\"checkbox\" ";
if (isset($rGroup) && $rGroup["allow_change_pass"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allow_download\">";
echo $_["can_download_playlist"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"allow_download\" id=\"allow_download\" type=\"checkbox\" ";
if (isset($rGroup) && $rGroup["allow_download"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reset_stb_data\">";
echo $_["can_view_vod_streams"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reset_stb_data\" id=\"reset_stb_data\" type=\"checkbox\" ";
if (isset($rGroup) && $rGroup["reset_stb_data"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller_client_connection_logs\">";
echo $_["can_view_live_connections"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reseller_client_connection_logs\" id=\"reseller_client_connection_logs\" type=\"checkbox\" ";
if (isset($rGroup) && $rGroup["reseller_client_connection_logs"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"delete_users\">";
echo $_["can_delete_users"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"delete_users\" id=\"delete_users\" type=\"checkbox\" ";
if (isset($rGroup) && $rGroup["delete_users"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"minimum_trial_credits\">";
echo $_["minimum_credit_for_trials"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"minimum_trial_credits\" name=\"minimum_trial_credits\" value=\"";
if (isset($rGroup)) {
    echo intval($rGroup["minimum_trial_credits"]);
} else {
    echo "0";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller_can_select_bouquets\">";
echo $_["reseller_select_bouquets"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reseller_can_select_bouquets\" id=\"reseller_can_select_bouquets\" type=\"checkbox\" ";
if (isset($rGroup) && $rGroup["reseller_can_select_bouquets"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"allow_import\">Can Use Reseller API</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"allow_import\" id=\"allow_import\" type=\"checkbox\" ";
if (isset($rGroup) && $rGroup["allow_import"]) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_group\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rGroup)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"tab-pane\" id=\"permissions\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"sub-header\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
echo $_["advanced_permissions_info"];
echo "                                                        </p>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <table id=\"datatable-permissions\" class=\"table table-borderless mb-0\">\n                                                                <thead class=\"bg-light\">\n                                                                    <tr>\n                                                                        <th style=\"display:none;\">";
echo $_["id"];
echo "</th>\n                                                                        <th>";
echo $_["permission"];
echo "</th>\n                                                                        <th>";
echo $_["description"];
echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody>\n                                                                    ";
foreach ($rAdvPermissions as $rPermission) {
    echo "                                                                    <tr";
    if (isset($rGroup) && in_array($rPermission[0], json_decode($rGroup["allowed_pages"], true))) {
        echo " class='selected selectedfilter ui-selected'";
    }
    echo ">\n                                                                        <td style=\"display:none;\">";
    echo $rPermission[0];
    echo "</td>\n                                                                        <td>";
    echo $rPermission[1];
    echo "</td>\n                                                                        <td>";
    echo $rPermission[2];
    echo "</td>\n                                                                    </tr>\n                                                                    ";
}
echo "                                                                </tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"next list-inline-item\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"javascript: void(0);\" onClick=\"selectAll()\" class=\"btn btn-info\">";
echo $_["select_all"];
echo "</a>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"javascript: void(0);\" onClick=\"selectNone()\" class=\"btn btn-warning\">";
echo $_["deselect_all"];
echo "</a>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_group\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rGroup)) {
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
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-ui/jquery-ui.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n\t\tvar rPermissions = [];\n\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        function selectAll() {\n            \$(\"#datatable-permissions tr\").each(function() {\n                if (!\$(this).hasClass('selected')) {\n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rPermissions.push(parseInt(\$(this).find(\"td:eq(0)\").html()));\n                    }\n                }\n            });\n        }\n        \n        function selectNone() {\n            \$(\"#datatable-permissions tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rPermissions.splice(parseInt(\$.inArray(\$(this).find(\"td:eq(0)\").html()), window.rPermissions), 1);\n                    }\n                }\n            });\n        }\n        \n        \$(document).ready(function() {\n            \$('select.select2').select2({width: '100%'})\n            \$(\".js-switch\").each(function (index, element) {\n                var init = new Switchery(element);\n            });\n            \n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n\t\t\t\n\t\t\t\$(\"#datatable-permissions\").DataTable({\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rPermissions) !== -1) {\n                        \$(row).addClass(\"selected\");\n                    }\n                },\n\t\t\t\torder: [[ 1, \"asc\" ]],\n                paging: false,\n                bInfo: false,\n                searching: false\n            });\n            \$(\"#datatable-permissions\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rPermissions.splice(parseInt(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html()), window.rPermissions), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rPermissions.push(parseInt(\$(ui.selected).find(\"td:eq(0)\").html()));\n                    }\n                }\n            });\n\t\t\t\$(\"#datatable-permissions_wrapper\").css(\"width\",\"100%\");\n\t\t\t\$(\"#datatable-permissions\").css(\"width\",\"100%\");\n\t\t\t\$(\"#group_form\").submit(function(e){\n                var rPermissions = [];\n                \$(\"#datatable-permissions tr.selected\").each(function() {\n                    rPermissions.push(\$(this).find(\"td:eq(0)\").html());\n                });\n                \$(\"#permissions_selected\").val(JSON.stringify(rPermissions));\n            });\n\n            \$(\"#max_connections\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#trial_credits\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#trial_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#official_credits\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#official_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n\t\t\t\$(\"#total_allowed_gen_trials\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n\t\t\t\$(\"#minimum_trial_credits\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n        });\n        </script>\n    </body>\n</html>";

?>