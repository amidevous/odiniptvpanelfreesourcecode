<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

if (count(get_included_files()) == 1) {
    exit;
}
if ($rPermissions["is_admin"]) {
    $rCheckTickets = getTickets();
}
echo " \r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n    <head>\r\n        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n        <title>";
echo htmlspecialchars($rSettings["server_name"]);
echo "</title>\r\n        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\r\n        <meta name=\"robots\" content=\"noindex,nofollow\">\r\n        <link rel=\"shortcut icon\" href=\"assets/images/favicon.ico\">\r\n        <link href=\"assets/libs/jquery-nice-select/nice-select.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/switchery/switchery.min.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/select2/select2.min.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/datatables/dataTables.bootstrap4.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/datatables/responsive.bootstrap4.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/datatables/buttons.bootstrap4.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/datatables/select.bootstrap4.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/jquery-toast/jquery.toast.min.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/bootstrap-select/bootstrap-select.min.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/treeview/style.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/clockpicker/bootstrap-clockpicker.min.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/daterangepicker/daterangepicker.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/nestable2/jquery.nestable.min.css\" rel=\"stylesheet\" />\r\n        <link href=\"assets/libs/magnific-popup/magnific-popup.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n        <link href=\"assets/css/icons.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n\t\t";
if (!$rAdminSettings["dark_mode"]) {
    echo "        <link href=\"assets/css/app_sidebar.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n\t\t<link href=\"assets/css/bootstrap.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n\t\t";
} else {
    echo "\t\t<link href=\"assets/css/app_sidebar.dark.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n\t\t<link href=\"assets/css/bootstrap.dark.css\" rel=\"stylesheet\" type=\"text/css\" />\r\n\t\t";
}
echo "    </head>\r\n\t    <script src=\"assets/js/sweetalert.min.js\"></script>\r\n\t\t<style>\t\r\n\t\t.fas.blink {\r\n      animation: blink 3s steps(5, start) infinite;\r\n      -webkit-animation: blink 2s steps(5, start) infinite;\r\n    }\r\n    @keyframes blink {\r\n      to {\r\n        visibility: hidden;\r\n      }\r\n    }\r\n    @-webkit-keyframes blink {\r\n      to {\r\n        visibility: hidden;\r\n      }\r\n    }\r\n\t\r\n#myBtn {\r\n  display: none;\r\n  position: fixed;\r\n  bottom: 40px;\r\n  right: 30px;\r\n  z-index: 99;\r\n  font-size: 18px;\r\n  border: none;\r\n  outline: none;\r\n  background-color: #56C3D6;\r\n  color: white;\r\n  cursor: pointer;\r\n  padding: 15px;\r\n  border-radius: 0px;\r\n}\r\n\r\n#myBtn:hover {\r\n  background-color: #555;\r\n}\r\n\t</style>\r\n    <body class=\"";
if (!$rAdminSettings["dark_mode"]) {
    echo "topbar-dark left-side-menu-light ";
}
if (!$rAdminSettings["expanded_sidebar"]) {
    echo "enlarged\" data-keep-enlarged=\"true\"";
} else {
    echo "\"";
}
echo ">\r\n        <!-- Begin page -->\r\n        <div id=\"wrapper\">\r\n            <!-- Topbar Start -->\r\n            <div class=\"navbar-custom\">\r\n                <ul class=\"list-unstyled topnav-menu float-right mb-0\">\r\n\t\t\t\t    ";
if ($rServerError && $rPermissions["is_admin"] && hasPermissions("adv", "servers")) {
    echo "                    <!--<li class=\"notification-list\">\r\n                        <a href=\"./servers.php\" class=\"nav-link right-bar-toggle waves-effect text-danger\">\r\n                            <i class=\"mdi mdi-alarm-light noti-icon\"></i>\r\n                        </a>\r\n                    </li>-->\r\n                    ";
}
echo "\t\t\t\t    ";
if ($rPermissions["is_reseller"]) {
    echo "\t                <li class=\"dropdown notification-list\">\r\n                            <a class=\"nav-link dropdown-toggle nav-user mr-0 waves-effect text-white\" data-toggle=\"dropdown\" href=\"#\" role=\"button\" aria-haspopup=\"false\" aria-expanded=\"false\">\r\n                                <i class=\"fas fa-user-alt\"></i><b>&nbsp;&nbsp;";
    echo $rUserInfo["username"];
    echo "</b> <i class=\"mdi mdi-chevron-down\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>\r\n                            </a>\r\n\t\t\t\t\t\t\t<div class=\"dropdown-menu profile-dropdown\">\r\n                                <a href=\"./edit_profile.php\" class=\"dropdown-item notify-item\"><span class=\"mdi mdi-account-circle\"> User Profile</span></a>\r\n\t\t\t\t\t\t\t\t<a href=\"./logout.php\" class=\"dropdown-item notify-item\"><span class=\"mdi mdi-logout\"> Logout</span></a>\r\n                            </a>\r\n                        </li>\t\r\n                    ";
}
echo "\t\t\t\t\t";
if ($rPermissions["is_admin"]) {
    echo "\t\t\t\t\t\t\r\n\t\t\t\t\t<li class=\"dropdown notification-list\">\r\n                            <a class=\"nav-link dropdown-toggle nav-user mr-0 waves-effect text-white\" data-toggle=\"dropdown\" href=\"#\" role=\"button\" aria-haspopup=\"false\" aria-expanded=\"false\">\r\n                                <i class=\"fas fa-user-alt\"></i><b>&nbsp;&nbsp;";
    echo $rUserInfo["username"];
    echo "</b> <i class=\"mdi mdi-chevron-down\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>\r\n                            </a>\r\n\t\t\t\t\t\t\t<div class=\"dropdown-menu profile-dropdown\">\r\n\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "edit_profile")) {
        echo "                                <a href=\"./edit_profile.php\" class=\"dropdown-item notify-item\"><span class=\"mdi mdi-account-circle\"> User Profile</span></a>\r\n                                ";
    }
    if ($rPermissions["is_admin"] && hasPermissions("adv", "manage_tickets")) {
        echo "                                <a href=\"./tickets.php\" class=\"dropdown-item notify-item\"><span class=\"mdi mdi-email\"> ";
        echo $_["tickets"];
        echo "</span></a>\r\n\t\t\t\t\t\t\t\t<div class=\"separator\"></div>\r\n\t\t\t\t\t\t\t\t";
    }
    if (hasPermissions("adv", "settings") || hasPermissions("adv", "database")) {
        echo "                                <a href=\"./settings.php\" class=\"dropdown-item notify-item\"><span class=\"mdi mdi-wrench mdi-rotate-90\"> General Settings</span></a>\r\n\t\t\t\t\t\t\t\t";
    }
    if (hasPermissions("adv", "folder_watch_settings") || hasPermissions("adv", "database")) {
        echo "                                <a href=\"./settings_watch.php\" class=\"dropdown-item notify-item\"><span class=\"mdi mdi-eye\"> Watch Settings</span></a>\r\n\t\t\t\t\t\t\t\t";
    }
    if (hasPermissions("adv", "settings") || hasPermissions("adv", "database")) {
        echo "                                <a href=\"./backups.php\" class=\"dropdown-item notify-item\"><span class=\"mdi mdi-backup-restore\"> Backups</span></a>\r\n\t\t\t\t\t\t\t\t";
    }
    if (hasPermissions("adv", "settings") || hasPermissions("adv", "database")) {
        echo "                                <a href=\"./db_sql.php\" class=\"dropdown-item notify-item\"><span class=\"mdi mdi-database\"> Database</span></a>\r\n\t\t\t\t\t\t\t\t<div class=\"separator\"></div>\r\n\t\t\t\t\t\t\t\t";
    }
    if (hasPermissions("adv", "login_flood")) {
        echo "                                <a href=\"./logout.php\" class=\"dropdown-item notify-item\"><span class=\"mdi mdi-logout\"> Logout</span></a>\r\n                                ";
    }
    echo "                            </a>\r\n                        </li>\r\n\t\t\t\t\t";
}
echo "\t\r\n\t\t\t\t\t";
if ($rPermissions["is_admin"] && $rAdminSettings["show_tickets"]) {
    foreach ($rCheckTickets as $rCheckTicket) {
        echo "\t\t\t\t\t\t\t";
        if ($rCheckTicket["status"] == 1 && $rCheckTicket["admin_read"] == 0) {
            echo "\t\t\t\t\t\t\t<li class=\"notification-list\">\r\n\t\t\t\t\t\t\t\t<a href=\"./ticket_view.php?id=";
            echo $rCheckTicket["id"];
            echo "\" class=\"nav-link right-bar-toggle waves-effect text-info\" role=\"button\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"\" data-original-title=\"New Ticket ";
            echo strtolower($rCheckTicket["username"]);
            echo "\">\r\n\t\t\t\t\t\t\t         <i class=\"mdi mdi-email blink noti-icon\"></i>\r\n\t\t\t\t\t\t\t\t</a>\r\n\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t";
    }
}
echo "                </ul>\r\n                <!-- LOGO -->\r\n                <div class=\"logo-box\">\r\n                    <a href=\"";
if ($rPermissions["is_admin"]) {
    echo "dashboard.php";
} else {
    echo "reseller.php";
}
echo "\" class=\"logo text-center\">\r\n                        <span class=\"logo-lg\">\r\n                            <img src=\"";
echo $rSettings["logo_url"];
echo "\" alt=\"\" height=\"24\">\r\n                        </span>\r\n                        <span class=\"logo-sm\">\r\n                            <img src=\"";
echo $rSettings["logo_url_sidebar"];
echo "\" alt=\"\" height=\"24\">\r\n                        </span>\r\n                    </a>\r\n                </div>\r\n\r\n                <ul class=\"list-unstyled topnav-menu topnav-menu-left m-0\">\r\n                    <li>\r\n                        <button class=\"button-menu-mobile waves-effect text-white\">\r\n                            <span></span>\r\n                            <span></span>\r\n                            <span></span>\r\n                        </button>\r\n                    </li>\r\n                </ul>\r\n            </div>\r\n            <!-- end Topbar -->\r\n            <!-- ========== Left Sidebar Start ========== -->\r\n            <div class=\"left-side-menu\">\r\n                <div class=\"slimscroll-menu\">\r\n                    <!--- Sidemenu -->\r\n                    <div id=\"sidebar-menu\">\r\n                        <ul class=\"metismenu\" id=\"side-menu\">\r\n\t\t\t\t\t\t    ";
if ($rPermissions["is_reseller"]) {
    echo "                            <li class=\"has-submenu\">\r\n                                <a href=\"#\"><i class=\"fe-activity text-danger\"></i><span>";
    echo $_["dashboard"];
    echo "</span></a>\r\n                            ";
    if ($rPermissions["is_reseller"] && $rPermissions["reseller_client_connection_logs"]) {
        echo "                                <ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n\t\t\t\t\t\t\t\t    <li><a href=\"./";
        if ($rPermissions["is_admin"]) {
            echo "dashboard.php";
        } else {
            echo "reseller.php";
        }
        echo "\" aria-expanded=\"false\"><span class=\"fe-activity\"></span> ";
        echo $_["dashboard"];
        echo " </a></li>\r\n                                    <li><a href=\"./live_connections.php\"><span class=\"mdi mdi-account-network\"></span> ";
        echo $_["live_connections"];
        echo "</a></li>\r\n                                    <li><a href=\"./user_activity.php\"><span class=\"mdi mdi-file-document\"> ";
        echo $_["activity_logs"];
        echo "</a></li>\r\n                                </ul>\r\n                            </li>\r\n                            ";
    }
}
echo "\t\t\t\t\t\t\t";
if ($rPermissions["is_admin"]) {
    echo "\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n                                <a href=\"#\"><i class=\"fe-activity text-danger\"></i><span>";
    echo $_["dashboard"];
    echo "</span></a>\r\n\t\t\t\t\t\t\t\t<ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"./";
    if ($rPermissions["is_admin"]) {
        echo "dashboard.php";
    } else {
        echo "reseller.php";
    }
    echo "\" aria-expanded=\"false\"><span class=\"fe-activity\"></span> ";
    echo $_["dashboard"];
    echo " </a></li>\r\n\t\t\t\t\t\t\t\t\t";
}
if (hasPermissions("adv", "live_connections")) {
    echo "                                    <li><a href=\"./live_connections.php\"><span class=\"mdi mdi-account-network\"> ";
    echo $_["live_connections"];
    echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
}
if (hasPermissions("adv", "connection_logs")) {
    echo "                                    <li><a href=\"./user_activity.php\"><span class=\"mdi mdi-file-document\"> ";
    echo $_["activity_logs"];
    echo "</a></li>\r\n\t\t\t\t\t\t\t\t    ";
}
if (hasPermissions("adv", "process_monitor")) {
    echo "                                    <li><a href=\"./process_monitor.php?server=";
    echo $_INFO["server_id"];
    echo "\"><span class=\"mdi mdi-chart-line\"> ";
    echo $_["process_monitor"];
    echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t</ul>\r\n                            </li>\t   \r\n\t\t\t\t\t\t\t";
}
if ($rPermissions["is_admin"]) {
    if (hasPermissions("adv", "servers") || hasPermissions("adv", "add_server") || hasPermissions("adv", "live_connections") || hasPermissions("adv", "stream_tools") || hasPermissions("adv", "connection_logs")) {
        echo "                            <li>\r\n                                <a href=\"#\"><i class=\"fas fa-server\"></i><span>";
        echo $_["servers"];
        echo "</span><span class=\"arrow-right\"></span></a>\r\n                                <ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n\t\t\t\t\t\t\t\t\t";
        if (hasPermissions("adv", "add_server")) {
            echo "                                    <li><a href=\"./server.php\"><span class=\"mdi mdi-upload-network\"></span> ";
            echo $_["add_existing_lb"];
            echo "</a></li>\r\n                                    <li><a href=\"./install_server.php\"><span class=\"mdi mdi-plus-network\"> ";
            echo $_["install_load_balancer"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "servers")) {
            echo "                                    <li><a href=\"./servers.php\"><span class=\"mdi mdi-server-network\"></span> ";
            echo $_["manage_servers"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        echo "                                </ul>\r\n                            </li>\r\n\t\t\t\t\t\t\t";
    }
    if (hasPermissions("adv", "add_user") || hasPermissions("adv", "users") || hasPermissions("adv", "mass_edit_users") || hasPermissions("adv", "import_streams") || hasPermissions("adv", "streams") || hasPermissions("adv", "mass_edit_streams") || hasPermissions("adv", "manage_events") || hasPermissions("adv", "import_movies") || hasPermissions("adv", "movies") || hasPermissions("adv", "series") || hasPermissions("adv", "radio") || hasPermissions("adv", "mass_sedits_vod") || hasPermissions("adv", "mass_sedits") || hasPermissions("adv", "mass_edits_radio") || hasPermissions("adv", "stream_tools") || hasPermissions("adv", "fingerprint") || hasPermissions("adv", "mass_delete") || hasPermissions("adv", "mng_regusers") || hasPermissions("adv", "add_reguser") || hasPermissions("adv", "credits_log") || hasPermissions("adv", "panel_errors") || hasPermissions("adv", "client_request_log") || hasPermissions("adv", "reg_userlog") || hasPermissions("adv", "live_connections") || hasPermissions("adv", "connection_logs") || hasPermissions("adv", "stream_errors") || hasPermissions("adv", "manage_events") || hasPermissions("adv", "settings") || hasPermissions("adv", "database") || hasPermissions("adv", "block_ips") || hasPermissions("adv", "block_isps") || hasPermissions("adv", "block_uas") || hasPermissions("adv", "categories") || hasPermissions("adv", "channel_order") || hasPermissions("adv", "epg") || hasPermissions("adv", "folder_watch") || hasPermissions("adv", "mng_groups") || hasPermissions("adv", "mng_packages") || hasPermissions("adv", "process_monitor") || hasPermissions("adv", "rtmp") || hasPermissions("adv", "subresellers") || hasPermissions("adv", "tprofiles")) {
        echo "\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t<a href=\"#\"><i class=\"fas fa-cog text-warning\"></i><span>Management </span><span class=\"arrow-right\"></span></a>\r\n\t\t\t\t\t\t\t<ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n                                <a href=\"#\"> <i class=\"mdi mdi-plus\"></i> Service Setup<div class=\"arrow-down\"></div></a>\r\n                                <ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t    ";
        if (hasPermissions("adv", "mng_packages")) {
            echo "                                    <li><a href=\"./packages.php\"><span class=\"mdi mdi-package\"> ";
            echo $_["packages"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        if (hasPermissions("adv", "categories")) {
            echo "                                    <li><a href=\"./stream_categories.php\"><span class=\"mdi mdi-folder-open\"> ";
            echo $_["categories"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t    \t";
        }
        if (hasPermissions("adv", "mng_groups")) {
            echo "                                    <li><a href=\"./groups.php\"><span class=\"mdi mdi-account-multiple\"> ";
            echo $_["groups"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        if (hasPermissions("adv", "epg")) {
            echo "                                    <li><a href=\"./epgs.php\"><span class=\"mdi mdi-play-protected-content\"> ";
            echo $_["epgs"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        if (hasPermissions("adv", "channel_order")) {
            echo "                                    <li><a href=\"./channel_order.php\"><span class=\"mdi mdi-reorder-horizontal\"> ";
            echo $_["channel_order"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        if (hasPermissions("adv", "folder_watch")) {
            echo "                                    <li><a href=\"./watch.php\"><span class=\"mdi mdi-eye\"> ";
            echo $_["folder_watch"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        if (hasPermissions("adv", "subresellers")) {
            echo "                                    <li><a href=\"./subresellers.php\"><span class=\"mdi mdi-account-multiple\"> ";
            echo $_["subresellers"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        if (hasPermissions("adv", "tprofiles")) {
            echo "                                    <li><a href=\"./profiles.php\"><span class=\"mdi mdi-find-replace\"> ";
            echo $_["transcode_profiles"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        echo "\t\t\t\t\t\t\t\t    <li><a href=\"./line_connections.php\"><span class=\"mdi mdi-playlist-check\"> Provider Con Check</span></a></li>\r\n                                </ul>\r\n                            </li>\r\n\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n                                <a href=\"#\"> <i class=\"mdi mdi-plus\"></i> Security<div class=\"arrow-down\"></div></a>\r\n                                <ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t    ";
        if (hasPermissions("adv", "login_flood")) {
            echo "                                    <li><a href=\"./flood_login.php\"><span class=\"mdi mdi-account-alert\"> Logins Flood</span></a></li>\r\n                                    ";
        }
        if (hasPermissions("adv", "security_center")) {
            echo "                                    <li><a href=\"./security_center.php\"><span class=\"mdi mdi-security\"> Security Center</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        if (hasPermissions("adv", "block_ips")) {
            echo "                                    <li><a href=\"./ips.php\"><span class=\"mdi mdi-close-octagon\"> ";
            echo $_["blocked_ips"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        if (hasPermissions("adv", "block_isps")) {
            echo "                                    <li><a href=\"./isps.php\"><span class=\"mdi mdi-close-network\"> ";
            echo $_["blocked_isps"];
            echo "</span></a></li>\r\n                                    ";
        }
        if (hasPermissions("adv", "rtmp")) {
            echo "                                    <li><a href=\"./rtmp_ips.php\"><span class=\"mdi mdi-close\"> ";
            echo $_["rtmp_ips"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        if (hasPermissions("adv", "block_uas")) {
            echo "                                    <li><a href=\"./useragents.php\"><span class=\"mdi mdi-close-box\"> ";
            echo $_["blocked_uas"];
            echo "</span></a></li>\r\n\t\t\t\t\t\t\t\t    ";
        }
        echo " \r\n                                </ul>\r\n                            </li>\r\n\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n                                <a href=\"#\"> <i class=\"mdi mdi-plus\"></i> ";
        echo $_["tools"];
        echo "<div class=\"arrow-down\"></div></a>\r\n                                <ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t    ";
        if (hasPermissions("adv", "mass_delete")) {
            echo "                                    <li><a href=\"./mass_delete.php\"><span class=\"mdi mdi-delete\"> ";
            echo $_["mass_delete"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "fingerprint")) {
            echo "                                    <li><a href=\"./fingerprint.php\"><span class=\"mdi mdi-fingerprint\"> ";
            echo $_["fingerprint"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "stream_tools")) {
            echo "\t\t\t\t\t\t\t\t\t<li><a href=\"./stream_tools.php\"><span class=\"mdi mdi-wrench mdi-rotate-90\"> ";
            echo $_["stream_tools"];
            echo "</a></li>\r\n                                    <li><a href=\"./ip_change.php\"><span class=\"mdi mdi-ip\"> Ip Change</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"./couvers_change.php\"><span class=\"mdi mdi-file-image\"> DNS Covers Change</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"./log_tools.php\"><span class=\"mdi mdi-cube\"> Quick Tools</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        echo "                                </ul>\r\n                            </li>\r\n\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n                                <a href=\"#\"> <i class=\"mdi mdi-plus\"></i> ";
        echo $_["logs"];
        echo "<div class=\"arrow-down\"></div></a>\r\n                                <ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t    ";
        if (hasPermissions("adv", "connection_logs")) {
            echo "                                    <li><a href=\"./user_activity.php\"><span class=\"mdi mdi-file-document\"> ";
            echo $_["activity_logs"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "reg_userlog")) {
            echo "                                    <li><a href=\"./reg_user_logs.php\"><span class=\"mdi mdi-account-details\"> ";
            echo $_["reseller_logs"];
            echo "</a></li>\r\n                                    ";
        }
        if (hasPermissions("adv", "credits_log")) {
            echo "                                    <li><a href=\"./credit_logs.php\"><span class=\"mdi mdi-credit-card-multiple\"> ";
            echo $_["credit_logs"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "login_logs")) {
            echo "                                    <li><a href=\"./login_logs.php\"><span class=\"mdi mdi-file-document\"> Login Logs</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "client_request_log")) {
            echo "                                    <li><a href=\"./client_logs.php\"><span class=\"mdi mdi-account-search\"> ";
            echo $_["client_logs"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "stream_errors")) {
            echo "                                    <li><a href=\"./stream_logs.php\"><span class=\"mdi mdi-file-document\"> ";
            echo $_["stream_logs"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "connection_logs")) {
            echo "                                    <li><a href=\"./user_ips.php\"><span class=\"mdi mdi-ip\"> ";
            echo $_["line_ip_usage"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "panel_errors")) {
            echo "                                    <li><a href=\"./panel_logs.php\"><span class=\"mdi mdi-file-document\"> ";
            echo $_["panel_logs"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "manage_events")) {
            echo "                                    <li><a href=\"./mag_events.php\"><span class=\"mdi mdi-message\"> ";
            echo $_["mag_event_logs"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "mag_claims")) {
            echo "                                    <li><a href=\"./mag_claims.php\"><span class=\"mdi mdi-message\"> Mag Claims</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        echo "                                </ul>\r\n                            </li>\r\n\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t";
    }
    if (hasPermissions("adv", "add_user") || hasPermissions("adv", "users") || hasPermissions("adv", "mass_edit_users") || hasPermissions("adv", "mng_regusers") || hasPermissions("adv", "add_reguser") || hasPermissions("adv", "credits_log") || hasPermissions("adv", "client_request_log") || hasPermissions("adv", "reg_userlog")) {
        echo "\t\t\t\t\t\t\t<li>\r\n                                <a href=\"#\"> <i class=\"fas fa-users text-primary\"></i><span>";
        echo $_["reg_users"];
        echo "</span> <span class=\"arrow-right\"></span></a>\r\n                                <ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n                                    ";
        if (hasPermissions("adv", "add_reguser")) {
            echo "                                    <li><a href=\"./reg_user.php\"><span class=\"mdi mdi-account-multiple-plus\"> ";
            echo $_["add_registered_user"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "mng_regusers")) {
            echo "                                    <li><a href=\"./reg_users.php\"><span class=\"mdi mdi-account-multiple\"> ";
            echo $_["manage_registered_users"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "mng_regusers")) {
            echo "                                    <li><a href=\"./reg_users_stats.php\"><span class=\"mdi mdi-account-multiple\"> Resellers Statistics</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        echo "                                </ul>\r\n                            </li>\r\n                            ";
    }
    if (hasPermissions("adv", "add_user") || hasPermissions("adv", "users") || hasPermissions("adv", "mass_edit_users") || hasPermissions("adv", "mng_regusers") || hasPermissions("adv", "add_reguser") || hasPermissions("adv", "credits_log") || hasPermissions("adv", "client_request_log") || hasPermissions("adv", "reg_userlog") || hasPermissions("adv", "add_mag") || hasPermissions("adv", "manage_mag") || hasPermissions("adv", "add_e2") || hasPermissions("adv", "manage_e2") || hasPermissions("adv", "manage_events")) {
        echo "\t\t\t\t\t\t    <li class=\"has-submenu\">\r\n                                <a href=\"#\"> <i class=\"fas fa-desktop text-pink\"></i><span>";
        echo $_["users"];
        echo " </span><span class=\"arrow-down\"></span></a>\r\n\t\t\t\t\t\t\t\t<ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t    <li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t\t        <a href=\"#\"> <i class=\"mdi mdi-plus\"></i> User Lines<div class=\"arrow-down\"></div></a>\r\n\t\t\t\t\t\t\t\t        <ul class=\"submenu\">\r\n                                            ";
        if (hasPermissions("adv", "add_user")) {
            echo "                                            <li><a href=\"./user.php\"><span class=\"mdi mdi-account-plus\"></span> ";
            echo $_["add_user"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        if (hasPermissions("adv", "users")) {
            echo "                                            <li><a href=\"./users.php\"><span class=\"mdi mdi-account-multiple\"> ";
            echo $_["manage_users"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        echo "\t\t\t\t\t\t\t\t\t    </ul>\r\n\t\t\t\t\t\t\t\t    </li>\r\n                                    <li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t\t        <a href=\"#\"> <i class=\"mdi mdi-plus\"></i> Mag Devices<div class=\"arrow-down\"></div></a>\r\n                                        <ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t        ";
        if (hasPermissions("adv", "add_mag")) {
            echo "                                            <li><a href=\"./user.php?mag\"><span class=\"mdi mdi-account-plus\"></span> ";
            echo $_["add_mag"];
            echo "</a></li>\r\n                                            <!--<li><a href=\"./mag.php\">";
            echo $_["link_mag"];
            echo "</a></li>-->\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        if (hasPermissions("adv", "manage_mag")) {
            echo "                                            <li><a href=\"./mags.php\"><span class=\"mdi mdi-account-multiple\"> ";
            echo $_["manage_mag_devices"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        if (hasPermissions("adv", "add_mag")) {
            echo "\t\t\t\t\t\t\t\t\t        <li><a href=\"./mag.php\"><span class=\"mdi mdi-account-switch\"> ";
            echo $_["link_mag"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t         ";
        }
        echo "\t\t\t\t\t\t\t\t\t    </ul>\r\n\t\t\t\t\t\t\t     \t</li>\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t    <li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t\t        <a href=\"#\"> <i class=\"mdi mdi-plus\"></i> Enigma Devices<div class=\"arrow-down\"></div></a>\r\n                                        <ul class=\"submenu\">\r\n                                            ";
        if (hasPermissions("adv", "add_e2")) {
            echo "                                            <li><a href=\"./user.php?e2\"><span class=\"mdi mdi-account-plus\"></span> ";
            echo $_["add_enigma"];
            echo "</a></li>\r\n                                            <!--<li><a href=\"./enigma.php\">";
            echo $_["link_enigma"];
            echo "</a></li>-->\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        if (hasPermissions("adv", "manage_e2")) {
            echo "                                            <li><a href=\"./enigmas.php\"><span class=\"mdi mdi-account-multiple\"> ";
            echo $_["manage_enigma_devices"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        if (hasPermissions("adv", "add_e2")) {
            echo "                                            <li><a href=\"./enigma.php\"><span class=\"mdi mdi-account-switch\"> ";
            echo $_["link_enigma"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        echo "                                        </ul>\r\n                                    </li>\r\n\t\t\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t\t\t    ";
        if (hasPermissions("adv", "mass_edit_users")) {
            echo "\t\t\t\t\t\t\t\t        <a href=\"./user_mass.php\"> <i class=\"mdi mdi-account-edit\"></i> ";
            echo $_["mass_edit_users"];
            echo "<div class=\"arrow-down\"></div></a>\r\n                                        ";
        }
        echo "                                    </li>\r\n                                </ul>\r\n\t\t\t\t\t\t\t\t</ul>\r\n                            </li>\r\n\t\t\t\t\t\t\t";
    }
} else {
    echo "\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n                                <a href=\"#\"><i class=\"fas fa-desktop text-pink\"></i><span>";
    echo $_["users"];
    echo "<span><span class=\"arrow-down\"></span></a>\r\n\t\t\t\t\t\t\t\t<ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n                                <ul class=\"submenu\">\r\n                                    ";
    if (!$rAdminSettings["disable_trial"] && 0 < $rPermissions["total_allowed_gen_trials"] && $rPermissions["minimum_trial_credits"] <= $rUserInfo["credits"]) {
        echo "                                    <li><a href=\"./user_reseller.php?trial\"><span class=\"mdi mdi-account-plus\"></span> ";
        echo $_["generate_trial"];
        echo "</a></li>\r\n                                    ";
    }
    echo "\t\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"#\"> <i class=\"mdi mdi-plus mdi\"></i> User Lines<div class=\"arrow-down\"></div></a>\r\n\t\t\t\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"./user_reseller.php\"><span class=\"mdi mdi-account-plus\"></span> ";
    echo $_["add_user"];
    echo "</a></li>\r\n                                                <li><a href=\"./users.php\"><span class=\"mdi mdi-account-multiple\"> ";
    echo $_["manage_users"];
    echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t\t</li> \r\n\t\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"#\"> <i class=\"mdi mdi-plus mdi\"></i> MAG Devices<div class=\"arrow-down\"></div></a>\r\n\t\t\t\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"./user_reseller.php?mag\"><span class=\"mdi mdi-account-plus\"></span> ";
    echo $_["add_mag"];
    echo "</a></li>\r\n                                                <li><a href=\"./mags.php\"><span class=\"mdi mdi-account-multiple\"> ";
    echo $_["manage_mag_devices"];
    echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t\t</li> \r\n\t\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"#\"> <i class=\"mdi mdi-plus mdi\"></i> Enigma Devices<div class=\"arrow-down\"></div></a>\r\n\t\t\t\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"./user_reseller.php?e2\"><span class=\"mdi mdi-account-plus\"></span> ";
    echo $_["add_enigma"];
    echo "</a></li>\r\n                                                <li><a href=\"./enigmas.php\"><span class=\"mdi mdi-account-multiple\"> ";
    echo $_["manage_enigma_devices"];
    echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t\t\t</ul>\t\r\n                                </ul>\r\n\t\t\t\t\t\t\t</li>\t\r\n\t\t\t\t\t\t\t";
}
if ($rPermissions["is_reseller"] && $rPermissions["create_sub_resellers"]) {
    echo "                            <li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t    <a href=\"#\"><i class=\"fas fa-users text-primary\"></i><span>";
    echo $_["reg_users"];
    echo "<span><span class=\"arrow-down\"></span></a>\r\n\t\t\t\t\t\t\t\t<ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n                                <ul class=\"submenu\">\r\n                                    ";
    if ($rPermissions["is_admin"]) {
        echo "                                    <li><a href=\"./reg_user.php\"><span class=\"mdi mdi-account-multiple-plus\"> ";
        echo $_["add_subreseller"];
        echo "</a></li>\r\n                                    ";
    } else {
        echo "                                    <li><a href=\"./subreseller.php\"><span class=\"mdi mdi-account-multiple-plus\"> ";
        echo $_["add_subreseller"];
        echo "</a></li>\r\n                                    ";
    }
    echo "                                    <li><a href=\"./reg_users.php\"><span class=\"mdi mdi-account-multiple\"> ";
    echo $_["manage_subresellers"];
    echo "</a></li>\r\n                                </ul>\r\n\t\t\t\t\t\t\t\t</ul>\r\n                            </li>\r\n                            ";
}
if ($rPermissions["is_admin"]) {
    if (hasPermissions("adv", "add_movie") || hasPermissions("adv", "import_movies") || hasPermissions("adv", "movies") || hasPermissions("adv", "series") || hasPermissions("adv", "add_series") || hasPermissions("adv", "radio") || hasPermissions("adv", "add_radio") || hasPermissions("adv", "mass_sedits_vod") || hasPermissions("adv", "mass_sedits") || hasPermissions("adv", "mass_edits_radio") || hasPermissions("adv", "add_stream") || hasPermissions("adv", "import_streams") || hasPermissions("adv", "create_channel") || hasPermissions("adv", "streams") || hasPermissions("adv", "mass_edit_streams") || hasPermissions("adv", "stream_tools") || hasPermissions("adv", "stream_errors") || hasPermissions("adv", "fingerprint")) {
        echo "                            <li class=\"has-submenu\">\r\n                                <a href=\"#\"><i class=\"fas fa-play text-info\"></i><span>Content</span><span class=\"arrow-right\"></span></a>\r\n\t\t\t\t\t\t\t\t<ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t<li class=\"has-submenu\">  \r\n\t\t\t\t\t\t\t\t\t\t<a href=\"#\"> <i class=\"mdi mdi-plus\"></i> Streams<div class=\"arrow-down\"></div></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t";
        if (hasPermissions("adv", "add_stream")) {
            echo "\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"./stream.php\"><span class=\"mdi mdi-plus\"> ";
            echo $_["add_stream"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "streams")) {
            echo "\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"./streams.php\"><span class=\"mdi mdi-play-circle-outline\"> ";
            echo $_["manage_streams"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "mass_edit_streams")) {
            echo "                                            <li><a href=\"./stream_mass.php\"><span class=\"mdi mdi-border-color\"> ";
            echo $_["mass_edit_streams"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        if (hasPermissions("adv", "import_streams")) {
            echo "\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"./stream.php?import\"><span class=\"mdi mdi-file-plus\"> ";
            echo $_["import_streams"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "streams")) {
            echo "\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"./stream_stats.php\"><span class=\"mdi mdi-chart-bar-stacked\"> Streams Statistics</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li> \r\n\t\t\t\t\t\t\t\t\t<li class=\"has-submenu\">\r\n\t\t\t\t\t\t\t\t\t\t<a href=\"#\"> <i class=\"mdi mdi-plus\"></i> Created Channels<div class=\"arrow-down\"></div></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t";
        if (hasPermissions("adv", "create_channel")) {
            echo "                                            <li><a href=\"./created_channel.php\"><span class=\"mdi mdi-plus\"> ";
            echo $_["create_channel"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        echo "\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"./streams.php?filter=8\"><span class=\"mdi mdi-play-circle-outline\"> Manage Channels</a></li>\r\n\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li> \r\n\t\t\t\t\t\t\t\t\t<li class=\"has-submenu\">  \r\n\t\t\t\t\t\t\t\t\t\t<a href=\"#\"> <i class=\"mdi mdi-plus\"></i> Movies<div class=\"arrow-down\"></div></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t";
        if (hasPermissions("adv", "add_movie")) {
            echo "                                            <li><a href=\"./movie.php\"><span class=\"mdi mdi-plus\"> ";
            echo $_["add_movie"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "movies")) {
            echo "                                            <li><a href=\"./movies.php\"><span class=\"mdi mdi-movie\"> ";
            echo $_["manage_movies"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "mass_sedits_vod")) {
            echo "                                            <li><a href=\"./movie_mass.php\"><span class=\"mdi mdi-border-color\"> ";
            echo $_["mass_edit_movies"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        if (hasPermissions("adv", "import_movies")) {
            echo "                                            <li><a href=\"./movie.php?import\"><span class=\"mdi mdi-file-plus\"> ";
            echo $_["import_movies"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        if (hasPermissions("adv", "import_movies")) {
            echo "                                            <li><a href=\"./vod_import.php\"><span class=\"mdi mdi-file-plus\"> Import Movies M3U</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "movies")) {
            echo "                                            <li><a href=\"./duplicate_movies.php\"><span class=\"mdi mdi-folder-search-outline\"> Duplicate Movies</span></a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "movies")) {
            echo "                                            <li><a href=\"./movie_stats.php\"><span class=\"mdi mdi-chart-bar-stacked\"> Movies Statistics</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li> \r\n\t\t\t\t\t\t\t\t\t<li class=\"has-submenu\">  \r\n\t\t\t\t\t\t\t\t\t\t<a href=\"#\"> <i class=\"mdi mdi-plus\"></i> Series<div class=\"arrow-down\"></div></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t";
        if (hasPermissions("adv", "add_series")) {
            echo "                                            <li><a href=\"./serie.php\"><span class=\"mdi mdi-plus\"> ";
            echo $_["add_series"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "series")) {
            echo "                                            <li><a href=\"./series.php\"><span class=\"mdi mdi-youtube-tv\"> ";
            echo $_["manage_series"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "episodes")) {
            echo "                                            <li><a href=\"./episodes.php\"><span class=\"mdi mdi-youtube-tv\"> ";
            echo $_["manage_episodes"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "mass_sedits")) {
            echo "                                            <li><a href=\"./series_mass.php\"><span class=\"mdi mdi-border-color\"> ";
            echo $_["mass_edit_series"];
            echo "</a></li>\r\n                                            <li><a href=\"./episodes_mass.php\"><span class=\"mdi mdi-border-color\"> ";
            echo $_["mass_edit_episodes"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t        ";
        }
        if (hasPermissions("adv", "add_series")) {
            echo "                                            <li><a href=\"./episode_import.php\"><span class=\"mdi mdi-file-plus\"> Import Episodes M3U</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "series")) {
            echo "                                            <li><a href=\"./serie_stats.php\"><span class=\"mdi mdi-chart-bar-stacked\"> Series Statistics</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li> \r\n\t\t\t\t\t\t\t\t\t<li class=\"has-submenu\">  \r\n\t\t\t\t\t\t\t\t\t\t<a href=\"#\"> <i class=\"mdi mdi-plus\"></i> Stations<div class=\"arrow-down\"></div></a>\t\r\n\t\t\t\t\t\t\t\t\t\t<ul class=\"submenu\">\r\n\t\t\t\t\t\t\t\t\t\t\t";
        if (hasPermissions("adv", "add_radio")) {
            echo "                                            <li><a href=\"./radio.php\"><span class=\"mdi mdi-plus\"> ";
            echo $_["add_station"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "radio")) {
            echo "                                            <li><a href=\"./radios.php\"><span class=\"mdi mdi-radio\"> ";
            echo $_["manage_stations"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "mass_edit_radio")) {
            echo "                                            <li><a href=\"./radio_mass.php\"><span class=\"mdi mdi-border-color\"> ";
            echo $_["mass_edit_stations"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t";
        }
        echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li> \r\n                                </ul>\r\n\t\t\t\t\t\t\t\t</ul>\r\n                            </li>\r\n\t\t\t\t\t\t\t";
    }
    if (hasPermissions("adv", "add_bouquet") || hasPermissions("adv", "bouquets")) {
        echo "                            <li>\r\n                                <a href=\"#\"> <i class=\"fas fa-spa text-success\"></i><span>";
        echo $_["bouquets"];
        echo "</span><span class=\"arrow-right\"></span></a>\r\n                                <ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n\t\t\t\t\t\t\t\t\t";
        if (hasPermissions("adv", "add_bouquet")) {
            echo "                                    <li><a href=\"./bouquet.php\"><span class=\"mdi mdi-plus\"> ";
            echo $_["add_bouquet"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        if (hasPermissions("adv", "bouquets")) {
            echo "                                    <li><a href=\"./bouquets.php\"><span class=\"mdi mdi-flower-tulip\"> ";
            echo $_["manage_bouquets"];
            echo "</a></li>\r\n                                    ";
        }
        if (hasPermissions("adv", "edit_bouquet")) {
            echo "                                    <li><a href=\"./bouquet_sort.php\"><span class=\"mdi mdi-reorder-horizontal\"> ";
            echo $_["order_bouquets"];
            echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
        }
        echo "                                </ul>\r\n                            </li>\r\n                            ";
    }
}
if ($rPermissions["is_reseller"] && $rPermissions["reset_stb_data"]) {
    echo "                            <li>\r\n                                <a href=\"#\"> <i class=\"fas fa-play text-info\"></i><span>";
    echo $_["content"];
    echo "</span><span class=\"arrow-right\"></span></a>\r\n                                <ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n                                    <li><a href=\"./streams.php\">";
    echo $_["streams"];
    echo "</a></li>\r\n                                    <li><a href=\"./movies.php\">";
    echo $_["movies"];
    echo "</a></li>\r\n                                    <li><a href=\"./series.php\">";
    echo $_["series"];
    echo "</a></li>\r\n                                    <li><a href=\"./episodes.php\">";
    echo $_["episodes"];
    echo "</a></li>\r\n                                    <li><a href=\"./radios.php\">";
    echo $_["stations"];
    echo "</a></li>\r\n                                </ul>\r\n                            </li>\r\n\t\t\t\t\t\t\t";
}
if ($rPermissions["is_reseller"]) {
    echo "                            <li>\r\n                                <a href=\"#\"> <i class=\"fas fa-envelope text-info\"></i><span>";
    echo $_["support"];
    echo "</span><span class=\"arrow-right\"></span></a>\r\n                                <ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n                                    <li><a href=\"./ticket.php\"><span class=\"mdi mdi-message-text\"> ";
    echo $_["create_ticket"];
    echo "</a></li>\r\n                                    <li><a href=\"./tickets.php\"><span class=\"mdi mdi-message-settings-variant\"> ";
    echo $_["manage_tickets"];
    echo "</a></li>\r\n\t\t\t\t\t\t\t\t\t";
    if ($rPermissions["allow_import"]) {
        echo "\t\t\t\t\t\t\t\t\t<li><a href=\"./resellersmarters.php\"><span class=\"mdi mdi-message-settings-variant\"> Reseller API Key</a></li>\r\n\t\t\t\t\t\t\t\t\t";
    }
    echo "                                </ul>\r\n                            </li>\r\n\t\t\t\t\t\t\t";
}
if ($rPermissions["is_reseller"] && $rAdminSettings["active_apps"]) {
    echo "\t\t\t\t\t\t\t<li>\r\n                                <a href=\"#\"> <i class=\"fas fa-qrcode text-success\"></i><span>Apps Iptv </span><span class=\"arrow-right\"></span></a>\r\n                                <ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n                                    <li><a href=\"https://edit.duplexplay.com/\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> DUPLEX IPTV</a></li>\r\n                                    <li><a href=\"https://www.netiptv.eu/upload\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> NET IPTV</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"http://siptv.eu/mylist/\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> SMART IPTV</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"https://iptvextreme.eu\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> IPTV EXTREME</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"https://nanomid.com/player/add-playlist\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> NANOMID</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"https://mega-iptv-a2636.web.app/\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> MEGA IPTV</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"https://ss-iptv.com/en/users/playlist\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> SS IPTV</a></li>\r\n                                </ul>\r\n                            </li>\r\n                            ";
}
if ($rPermissions["is_admin"] && hasPermissions("adv", "manage_tickets")) {
    echo "\t\t\t\t\t\t\t<li>\r\n                                <a href=\"#\"> <i class=\"mdi mdi-apps text-success\"></i><span>Apps Iptv </span><span class=\"arrow-right\"></span></a>\r\n                                <ul class=\"nav-second-level\" aria-expanded=\"false\">\r\n                                    <li><a href=\"https://edit.duplexplay.com/\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> DUPLEX IPTV</a></li>\r\n                                    <li><a href=\"https://www.netiptv.eu/upload\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> NET IPTV</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"http://siptv.eu/mylist/\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> SMART IPTV</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"https://iptvextreme.eu\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> IPTV EXTREME</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"https://nanomid.com/player/add-playlist\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> NANOMID</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"https://mega-iptv-a2636.web.app/\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> MEGA IPTV</a></li>\r\n\t\t\t\t\t\t\t\t\t<li><a href=\"https://ss-iptv.com/en/users/playlist\" target=\"_blank\"><span class=\"mdi mdi-account-star\"> SS IPTV</a></li>\r\n                                </ul>\r\n                            </li>\r\n                            ";
}
if ($rPermissions["is_reseller"] && $rAdminSettings["active_mannuals"]) {
    echo "                            <li>\r\n                                <a href=\"./reseller_mannuals.php\"> <i class=\"fas fa-folder-open text-purple\"></i><span>";
    echo $_["mannuals"];
    echo "</span></a>\r\n                            </li>\r\n                            ";
}
echo "                        </ul>\r\n                    </div>\r\n                    <!-- End Sidebar -->\r\n                    <div class=\"clearfix\"></div>\r\n                </div>\r\n                <!-- Sidebar -left -->\r\n            </div>\r\n\t\t\t\r\n<button onclick=\"topFunction()\" id=\"myBtn\" title=\"Go to top\"><i class=\"mdi mdi-arrow-up-thick\"></i></button>\r\n\t\t\r\n<script>\r\n//Get the button\r\nvar mybutton = document.getElementById(\"myBtn\");\r\n\r\n// When the user scrolls down 20px from the top of the document, show the button\r\nwindow.onscroll = function() {scrollFunction()};\r\n\r\nfunction scrollFunction() {\r\n  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {\r\n    mybutton.style.display = \"block\";\r\n  } else {\r\n    mybutton.style.display = \"none\";\r\n  }\r\n}\r\n\r\n// When the user clicks on the button, scroll to the top of the document\r\nfunction topFunction() {\r\n  document.body.scrollTop = 0;\r\n  document.documentElement.scrollTop = 0;\r\n}\r\n</script>";

?>