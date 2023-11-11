<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "functions.php";
if (!isset($_SESSION["hash"])) {
    exit;
}
set_time_limit($rSQLTimeout);
ini_set("mysql.connect_timeout", $rSQLTimeout);
ini_set("max_execution_time", $rSQLTimeout);
ini_set("default_socket_timeout", $rSQLTimeout);
$rStatusArray = ["<button type='button' class='btn btn-warning btn-xs waves-effect waves-light'>STOPPED</button>", "RUNNING", "<button type='button' class='btn btn-primary btn-xs waves-effect waves-light'>STARTING</button>", "<button type='button' class='btn btn-danger btn-xs waves-effect waves-light'><i class='mdi mdi-checkbox-blank-circle'></i> DOWN</button>", "<button type='button' class='btn btn-pink btn-xs waves-effect waves-light'>ON DEMAND</button>", "<button type='button' class='btn btn-purple btn-xs waves-effect waves-light'>DIRECT</button>", "<button type='button' class='btn btn-warning btn-xs waves-effect waves-light'>CREATING...</button>"];
$rVODStatusArray = ["<button type='button' class='btn btn-dark btn-xs waves-effect waves-light'><i class='text-white mdi mdi-checkbox-blank'></i></button>", "<button type='button' class='btn btn-success btn-xs waves-effect waves-light'><i class='text-white mdi mdi-checkbox-marked-outline'></i></button>", "<button type='button' class='btn btn-warning btn-xs waves-effect waves-light'><i class='text-white mdi mdi-checkbox-blank'></i></button>", "<button type='button' class='btn btn-primary btn-xs waves-effect waves-light'><i class='text-white mdi mdi-web'></i></button>", "<button type='button' class='btn btn-danger btn-xs waves-effect waves-light'><i class='text-white mdi mdi-close-box-outline'></i></button>"];
$rWatchStatusArray = ["1" => "<button type='button' class='btn btn-outline-success btn-xs waves-effect waves-light'>ADDED</button>", "2" => "<button type='button' class='btn btn-outline-danger btn-xs waves-effect waves-light'>SQL FAILED</button>", "3" => "<button type='button' class='btn btn-outline-danger btn-xs waves-effect waves-light'>NO CATEGORY</button>", "4" => "<button type='button' class='btn btn-outline-danger btn-xs waves-effect waves-light'>NO TMDb MATCH</button>", "5" => "<button type='button' class='btn btn-outline-danger btn-xs waves-effect waves-light'>INVALID FILE</button>"];
$rType = $_GET["id"];
$rStart = intval($_GET["start"]);
$rLimit = intval($_GET["length"]);
if (1000 < $rLimit || $rLimit == -1 || $rLimit == 0) {
    $rLimit = 1000;
}
if ($rType == "users") {
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "users") && !hasPermissions("adv", "mass_edit_users")) {
        exit;
    }
    $rAvailableMembers = array_keys(getRegisteredUsers($rUserInfo["id"]));
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`users`.`id`", "`users`.`username`", "`users`.`password`", "`reg_users`.`username`", "`users`.`enabled`", "`users`.`is_trial`", "`users`.`exp_date`", "`users`.`exp_date`", "`users`.`max_connections`", "`active_connections`", false];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if (isset($_GET["showall"])) {
        if ($rPermissions["is_reseller"]) {
            $rWhere[] = "`users`.`member_id` IN (" . join(",", $rAvailableMembers) . ")";
        }
    } else {
        if ($rPermissions["is_admin"]) {
            $rWhere[] = "`users`.`is_mag` = 0 AND `users`.`is_e2` = 0";
        } else {
            $rWhere[] = "`users`.`is_mag` = 0 AND `users`.`is_e2` = 0 AND `users`.`member_id` IN (" . join(",", $rAvailableMembers) . ")";
        }
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`users`.`username` LIKE '%" . $rSearch . "%' OR `users`.`password` LIKE '%" . $rSearch . "%' OR `reg_users`.`username` LIKE '%" . $rSearch . "%' OR from_unixtime(`exp_date`) LIKE '%" . $rSearch . "%' OR `users`.`max_connections` LIKE '%" . $rSearch . "%' OR `users`.`reseller_notes` LIKE '%" . $rSearch . "%' OR `users`.`admin_notes` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["filter"])) {
        if ($_GET["filter"] == 1) {
            $rWhere[] = "(`users`.`admin_enabled` = 1 AND `users`.`enabled` = 1 AND (`users`.`exp_date` IS NULL OR `users`.`exp_date` > UNIX_TIMESTAMP()))";
        } else {
            if ($_GET["filter"] == 2) {
                $rWhere[] = "`users`.`enabled` = 0";
            } else {
                if ($_GET["filter"] == 3) {
                    $rWhere[] = "`users`.`admin_enabled` = 0";
                } else {
                    if ($_GET["filter"] == 4) {
                        $rWhere[] = "(`users`.`exp_date` IS NOT NULL AND `users`.`exp_date` <= UNIX_TIMESTAMP())";
                    } else {
                        if ($_GET["filter"] == 5) {
                            $rWhere[] = "`users`.`is_trial` = 1";
                        } else {
                            if ($_GET["filter"] == 6) {
                                $rWhere[] = "`users`.`is_mag` = 1";
                            } else {
                                if ($_GET["filter"] == 7) {
                                    $rWhere[] = "`users`.`is_e2` = 1";
                                } else {
                                    if ($_GET["filter"] == 8) {
                                        $rWhere[] = "`users`.`is_restreamer` = 1";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if (0 < strlen($_GET["reseller"])) {
        $rWhere[] = "`users`.`member_id` = " . intval($_GET["reseller"]);
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(`users`.`id`) AS `count` FROM `users` LEFT JOIN `reg_users` ON `reg_users`.`id` = `users`.`member_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `users`.`id`, `users`.`member_id`, `users`.`username`, `users`.`password`, `users`.`created_at`, `users`.`exp_date`, `users`.`admin_enabled`, `users`.`enabled`, `users`.`isp_desc`, `users`.`is_isplock`, `users`.`admin_notes`, `users`.`reseller_notes`, `users`.`max_connections`,  `users`.`is_trial`, `reg_users`.`username` AS `owner_name`, (SELECT count(*) FROM `user_activity_now` WHERE `users`.`id` = `user_activity_now`.`user_id`) AS `active_connections`, (SELECT user_ip FROM `user_activity_now` WHERE `users`.`id` = `user_activity_now`.`user_id` LIMIT 1) AS `user_ip`, (SELECT MAX(`date_start`) FROM `user_activity` WHERE `users`.`id` = `user_activity`.`user_id`) AS `last_active` FROM `users` LEFT JOIN `reg_users` ON `reg_users`.`id` = `users`.`member_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if (!$rRow["admin_enabled"]) {
                    $rStatus = "<button type='button' class='btn btn-danger btn-sm waves-effect waves-light'>BANNED</button>";
                } else {
                    if (!$rRow["enabled"]) {
                        $rStatus = "<button type='button' class='btn btn-secondary btn-sm waves-effect waves-light'>DISABLED</button>";
                    } else {
                        if ($rRow["exp_date"] && $rRow["exp_date"] < time()) {
                            $rStatus = "<button type='button' class='btn btn-info btn-sm waves-effect waves-light'>EXPIRED</button>";
                        } else {
                            $rStatus = "<button type='button' class='btn btn-success btn-sm waves-effect waves-light'>ACTIVE</button>";
                        }
                    }
                }
                if (0 < $rRow["active_connections"]) {
                    $rActive = "<button type='button' class='btn btn-success btn-sm waves-effect waves-light'>ONLINE</button>";
                } else {
                    $rActive = "<button type='button' class='btn btn-dark btn-sm waves-effect waves-light'>OFFLINE</button>";
                }
                if ($rRow["is_trial"]) {
                    $rTrial = "<button type='button' class='btn btn-warning btn-sm waves-effect waves-light'>TRIAL</button>";
                } else {
                    $rTrial = "<button type='button' class='btn btn-success btn-sm waves-effect waves-light'>OFFICIAL</button>";
                }
                if ($rRow["created_at"]) {
                    $rCreated = date("d-m-Y H:i", $rRow["created_at"]);
                } else {
                    $rCreated = date("d-m-Y H:i", $rCreated["created_at"]);
                }
                if ($rRow["exp_date"]) {
                    if ($rRow["exp_date"] < time()) {
                        $rExpDate = "<button type='button' class='btn btn-info btn-sm waves-effect waves-light' class=\"expired\">" . date("d-m-Y H:i", $rRow["exp_date"]) . " <i data-toggle='tooltip' data-placement='top' title='' data-original-title='Created : " . $rCreated . "' class='mdi mdi-information'></button>";
                    } else {
                        $rExpDate = date("d-m-Y H:i", $rRow["exp_date"]) . " <i data-toggle='tooltip' data-placement='top' title='' data-original-title='Created : " . $rCreated . "' class='mdi mdi-information'></button>";
                    }
                } else {
                    $rExpDate = "<button type='button' class='btn btn-pink btn-sm waves-effect waves-light'>UNLIMITED <i data-toggle='tooltip' data-placement='top' title='' data-original-title='Created : " . $rCreated . "' class='mdi mdi-information'></button>";
                }
                if ($rRow["max_connections"] == 0) {
                    $rRow["max_connections"] = "&infin;";
                } else {
                    $max_connections = $rRow["max_connections"];
                }
                if ($rPermissions["is_reseller"] && $rPermissions["reseller_client_connection_logs"] || $rPermissions["is_admin"] && hasPermissions("adv", "live_connections")) {
                    $rActiveConnections = "<a href=\"./live_connections.php?user_id=" . $rRow["id"] . "\"> " . $rRow["active_connections"] . " / " . $max_connections . "</a>";
                } else {
                    $rActiveConnections = "" . $rRow["active_connections"] . " / " . $max_connections . "</a>";
                }
                $rButtons = "<div class=\"btn-group\">";
                if (0 < strlen($rRow["admin_notes"]) && $rPermissions["is_admin"] || 0 < strlen($rRow["reseller_notes"])) {
                    $rNotes = "";
                    if ($rPermissions["is_admin"] && 0 < strlen($rRow["admin_notes"])) {
                        $rNotes .= $rRow["admin_notes"];
                    }
                    if (0 < strlen($rRow["reseller_notes"])) {
                        if (strlen($rNotes) != 0) {
                            $rNotes .= "\n";
                        }
                        $rNotes .= $rRow["reseller_notes"];
                    }
                    $rButtons .= "<button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"\" data-original-title=\"" . $rNotes . "\"><i class=\"mdi mdi-note\"></i></button>";
                } else {
                    $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-note\"></i></button>";
                }
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_user") || $rPermissions["is_reseller"] && $rAdminSettings["reseller_reset_isplock"]) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Reset isp\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'resetispuser');\"><i class=\"mdi mdi-lock-reset\"></i></button>\r\n\t\t\t\t\t";
                }
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_user") || $rPermissions["is_reseller"] && $rAdminSettings["reseller_can_isplock"]) {
                    if ($rRow["is_isplock"]) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Unlock isp\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'unlockk');\"><i class=\"mdi mdi-lock text-danger\"></i></button>";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Lock isp\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'lockk');\"><i class=\"mdi mdi-lock-outline\"></i></button>";
                    }
                }
                if ($rPermissions["is_admin"]) {
                    $rButtons .= "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Extend line\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"renew_user('" . $rRow["id"] . "', '" . $rRow["username"] . "');\"><i class=\"mdi mdi-autorenew\"></i></button>\r\n                         ";
                }
                if ($rPermissions["is_admin"]) {
                    if (hasPermissions("adv", "edit_user")) {
                        $rButtons .= "<a href=\"./user.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t\t";
                    }
                } else {
                    if ($rPermissions["is_reseller"] && $rPermissions["reseller_can_select_bouquets"] || $rPermissions["is_admin"]) {
                        $rButtons .= "<a href=\"./user_reseller_edit.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit User Bouquets\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-format-line-spacing\"></i></button></a>";
                    }
                    $rButtons .= "<a href=\"./user_reseller.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>";
                }
                if ($rPermissions["is_reseller"] && $rPermissions["allow_download"] || $rPermissions["is_admin"]) {
                    $rButtons .= "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Download Playlist\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"download('" . $rRow["username"] . "', '" . $rRow["password"] . "');\"><i class=\"mdi mdi-arrow-collapse-down\"></i></button>";
                }
                if ($rPermissions["is_reseller"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_user")) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Kill Connections\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'kill');\"><i class=\"fas fa-hammer\"></i></button>\r\n\t\t\t\t\t";
                }
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_user")) {
                    if ($rRow["admin_enabled"]) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Ban\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'ban');\"><i class=\"mdi mdi-minus-circle-outline\"></i></button>";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Unban\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'unban');\"><i class=\"mdi mdi-minus-circle text-danger\"></i></button>";
                    }
                }
                if ($rPermissions["is_reseller"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_user")) {
                    if ($rRow["enabled"]) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Disable\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'disable');\"><i class=\"mdi mdi-checkbox-blank-circle-outline\"></i></button>\r\n\t\t\t\t\t\t";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'enable');\"><i class=\"mdi mdi-checkbox-blank-circle text-danger\"></i></button>\r\n\t\t\t\t\t\t";
                    }
                }
                if ($rPermissions["is_reseller"] && $rPermissions["delete_users"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_user")) {
                    $rButtons .= "<a href=\"./user_stats.php?user_id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"User Stats\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-chart-bar-stacked\"></i></button></a>";
                }
                if ($rPermissions["is_reseller"] && $rPermissions["delete_users"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_user")) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                }
                $rButtons .= "</div>";
                if ($rRow["last_active"]) {
                    $rLastActive = date("Y-m-d H:i", $rRow["last_active"]);
                } else {
                    $rLastActive = "Never";
                }
                $today = time();
                $leftdaynumber = (strtotime(date("Y-m-d H:i", $rRow["exp_date"])) - $today) / 86400;
                $leftHourNumber = ($rRow["exp_date"] - $today) / 3600;
                $leftMinNumber = ($rRow["exp_date"] - $today) / 60;
                if (0 < $leftdaynumber && $leftdaynumber <= 1) {
                    $rLeftDate = "1 Day";
                } else {
                    if (1 < $leftdaynumber) {
                        $rLeftDate = round($leftdaynumber) . " Days";
                    } else {
                        if (0 < $leftHourNumber && $leftHourNumber <= 1) {
                            $rLeftDate = round($leftMinNumber) . " Minutes";
                        } else {
                            if (round($leftHourNumber) == 1) {
                                $rLeftDate = "1 Hour";
                            } else {
                                if (1 < $leftHourNumber) {
                                    $rLeftDate = round($leftHourNumber) . " Hours";
                                } else {
                                    $rLeftDate = "<center>-</center>";
                                }
                            }
                        }
                    }
                }
                $query = "SELECT user_activity_now.date_start, user_activity_now.geoip_country_code, user_activity_now.user_ip, user_activity_now.stream_id, user_activity_now.container, user_activity_now.user_id, SUBSTR(`streams`.`stream_display_name`, 1, 45) stream_display_name FROM user_activity_now LEFT JOIN streams ON user_activity_now.stream_id = streams.id WHERE user_id = " . $rRow["id"];
                $result = $db->query($query);
                $row2 = mysqli_fetch_assoc($result);
                if (!empty($row2["stream_display_name"])) {
                    $rTime = intval(time()) - intval($row2["date_start"]);
                    $rStream_name = "<span style='color: #20a009;'</span>" . $row2["stream_display_name"] . " - ( <span style='color: #737373;'></span>" . "<span style='color: #737373;'></span>" . $row2["container"] . " )<span style='color: #737373;'></span>" . "<br><span style='color: #737373;'>" . "Uptime </span>" . "<span style='color: #737373;'>" . sprintf("%02d:%02d:%02d", $rTime / 3600, $rTime / 60 % 60, $rTime % 60) . "<br><a target='_blank' href='https://www.ip-tracker.org/locator/ip-lookup.php?ip=" . $row2["user_ip"] . "'</span>" . $row2["user_ip"] . " <img src='https://www.ip-tracker.org/images/ip-flags/" . strtolower($row2["geoip_country_code"]) . ".png'></img>" . "<br><span style='color: #737373;'></span>" . $rRow["isp_desc"];
                } else {
                    $rStream_name = "-";
                }
                if ($rPermissions["is_admin"]) {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./user.php?id=" . $rRow["id"] . "\">" . $rRow["username"], $rRow["password"], $rRow["owner_name"], $rStatus, $rTrial, $rExpDate, $rLeftDate, $rActiveConnections, "<font size=1>" . $rStream_name, $rButtons];
                } else {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./user_reseller.php?id=" . $rRow["id"] . "\">" . $rRow["username"], $rRow["password"], $rRow["owner_name"], $rStatus, $rTrial, $rExpDate, $rLeftDate, $rActiveConnections, "<font size=1>" . $rStream_name, $rButtons];
                }
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "mags") {
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "manage_mag")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`users`.`id`", "`users`.`username`", "`mag_devices`.`mac`", "`reg_users`.`username`", "`users`.`enabled`", "`users`.`is_trial`", "`users`.`exp_date`", "`users`.`exp_date`", "`mag_devices`.`stb_type`", "`active_connections`", false];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if ($rPermissions["is_reseller"]) {
        $rWhere[] = "`users`.`member_id` IN (" . join(",", array_keys(getRegisteredUsers($rUserInfo["id"]))) . ")";
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`users`.`username` LIKE '%" . $rSearch . "%' OR from_base64(`mag_devices`.`mac`) LIKE '%" . strtoupper($rSearch) . "%' OR `reg_users`.`username` LIKE '%" . $rSearch . "%' OR from_unixtime(`exp_date`) LIKE '%" . $rSearch . "%' OR `users`.`reseller_notes` LIKE '%" . $rSearch . "%' OR `users`.`admin_notes` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["filter"])) {
        if ($_GET["filter"] == 1) {
            $rWhere[] = "(`users`.`admin_enabled` = 1 AND `users`.`enabled` = 1 AND (`users`.`exp_date` IS NULL OR `users`.`exp_date` > UNIX_TIMESTAMP()))";
        } else {
            if ($_GET["filter"] == 2) {
                $rWhere[] = "`users`.`enabled` = 0";
            } else {
                if ($_GET["filter"] == 3) {
                    $rWhere[] = "`users`.`admin_enabled` = 0";
                } else {
                    if ($_GET["filter"] == 4) {
                        $rWhere[] = "(`users`.`exp_date` IS NOT NULL AND `users`.`exp_date` <= UNIX_TIMESTAMP())";
                    } else {
                        if ($_GET["filter"] == 5) {
                            $rWhere[] = "`users`.`is_trial` = 1";
                        } else {
                            if ($_GET["filter"] == 6) {
                                $rWhere[] = "`users`.`is_restreamer` = 1";
                            }
                        }
                    }
                }
            }
        }
    }
    if (0 < strlen($_GET["reseller"])) {
        $rWhere[] = "`users`.`member_id` = " . intval($_GET["reseller"]);
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(`users`.`id`) AS `count` FROM `users` LEFT JOIN `reg_users` ON `reg_users`.`id` = `users`.`member_id` INNER JOIN `mag_devices` ON `mag_devices`.`user_id` = `users`.`id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `users`.`id`, `users`.`username`, `mag_devices`.`mac`, `mag_devices`.`mag_id`, `users`.`created_at`, `mag_devices`.`stb_type` ,`users`.`exp_date`, `users`.`admin_enabled`, `users`.`enabled`, `users`.`isp_desc`, `users`.`is_isplock`, `users`.`admin_notes`, `users`.`reseller_notes`, `users`.`max_connections`,  `users`.`is_trial`, `reg_users`.`username` AS `owner_name`, (SELECT count(*) FROM `user_activity_now` WHERE `users`.`id` = `user_activity_now`.`user_id`) AS `active_connections`, (SELECT user_ip FROM `user_activity_now` WHERE `users`.`id` = `user_activity_now`.`user_id` LIMIT 1) AS `user_ip` FROM `users` LEFT JOIN `reg_users` ON `reg_users`.`id` = `users`.`member_id` INNER JOIN `mag_devices` ON `mag_devices`.`user_id` = `users`.`id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if (!$rRow["admin_enabled"]) {
                    $rStatus = "<button type='button' class='btn btn-danger btn-sm waves-effect waves-light'>BANNED</button>";
                } else {
                    if (!$rRow["enabled"]) {
                        $rStatus = "<button type='button' class='btn btn-secondary btn-sm waves-effect waves-light'>DISABLED</button>";
                    } else {
                        if ($rRow["exp_date"] && $rRow["exp_date"] < time()) {
                            $rStatus = "<button type='button' class='btn btn-info btn-sm waves-effect waves-light'>EXPIRED</button>";
                        } else {
                            $rStatus = "<button type='button' class='btn btn-success btn-sm waves-effect waves-light'>ACTIVE</button>";
                        }
                    }
                }
                if (0 < $rRow["active_connections"]) {
                    $rActive = "<button type='button' class='btn btn-success btn-sm waves-effect waves-light'>ONLINE</button>";
                } else {
                    $rActive = "<button type='button' class='btn btn-dark btn-sm waves-effect waves-light'>OFFLINE</button>";
                }
                if ($rRow["is_trial"]) {
                    $rTrial = "<button type='button' class='btn btn-warning btn-sm waves-effect waves-light'>TRIAL</button>";
                } else {
                    $rTrial = "<button type='button' class='btn btn-success btn-sm waves-effect waves-light'>OFFICIAL</button>";
                }
                if ($rRow["created_at"]) {
                    $rCreated = date("d-m-Y H:i", $rRow["created_at"]);
                } else {
                    $rCreated = date("d-m-Y H:i", $rCreated["created_at"]);
                }
                if ($rRow["exp_date"]) {
                    if ($rRow["exp_date"] < time()) {
                        $rExpDate = "<button type='button' class='btn btn-info btn-sm waves-effect waves-light' class=\"expired\">" . date("d-m-Y H:i", $rRow["exp_date"]) . " <i data-toggle='tooltip' data-placement='top' title='' data-original-title='Created : " . $rCreated . "' class='mdi mdi-information'></button>";
                    } else {
                        $rExpDate = date("d-m-Y H:i", $rRow["exp_date"]) . " <i data-toggle='tooltip' data-placement='top' title='' data-original-title='Created : " . $rCreated . "' class='mdi mdi-information'></button>";
                    }
                } else {
                    $rExpDate = "<button type='button' class='btn btn-pink btn-sm waves-effect waves-light'>UNLIMITED <i data-toggle='tooltip' data-placement='top' title='' data-original-title='Created : " . $rCreated . "' class='mdi mdi-information'></button>";
                }
                if ($rPermissions["is_reseller"] && $rPermissions["reseller_client_connection_logs"] || $rPermissions["is_admin"] && hasPermissions("adv", "live_connections")) {
                    $rActiveConnections = "<a href=\"./live_connections.php?user_id=" . $rRow["id"] . "\">" . $rRow["active_connections"] . "</a>";
                } else {
                    $rActiveConnections = $rRow["active_connections"];
                }
                $rButtons = "<div class=\"btn-group\">";
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_user") || $rPermissions["is_reseller"] && $rAdminSettings["reseller_reset_isplock"]) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Reset isp\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'resetispuser');\"><i class=\"mdi mdi-lock-reset\"></i></button>\r\n\t\t\t\t\t";
                }
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_user") || $rPermissions["is_reseller"] && $rAdminSettings["reseller_can_isplock"]) {
                    if ($rRow["is_isplock"]) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Unlock isp\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'unlockk');\"><i class=\"mdi mdi-lock\"></i></button>";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Lock isp\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'lockk');\"><i class=\"mdi mdi-lock-outline\"></i></button>";
                    }
                }
                if ($rPermissions["is_admin"]) {
                    $rButtons .= "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Extend line\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"renew_user('" . $rRow["id"] . "', '" . $rRow["username"] . "');\"><i class=\"mdi mdi-autorenew\"></i></button>\r\n                         ";
                }
                if (0 < strlen($rRow["admin_notes"]) && $rPermissions["is_admin"] || 0 < strlen($rRow["reseller_notes"])) {
                    $rNotes = "";
                    if ($rPermissions["is_admin"] && 0 < strlen($rRow["admin_notes"])) {
                        $rNotes .= $rRow["admin_notes"];
                    }
                    if (0 < strlen($rRow["reseller_notes"])) {
                        if (strlen($rNotes) != 0) {
                            $rNotes .= "\n";
                        }
                        $rNotes .= $rRow["reseller_notes"];
                    }
                    $rButtons .= "<button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"\" data-original-title=\"" . $rNotes . "\"><i class=\"mdi mdi-note\"></i></button>";
                } else {
                    $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-note\"></i></button>";
                }
                if ($rPermissions["is_admin"]) {
                    if (hasPermissions("adv", "manage_events")) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Send MAG Event\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"message(" . $rRow["mag_id"] . ", '" . base64_decode($rRow["mac"]) . "');\"><i class=\"mdi mdi-comment-alert-outline\"></i></button>\r\n\t\t\t\t\t\t";
                    }
                    if (hasPermissions("adv", "edit_mag")) {
                        $rButtons .= "<a href=\"./user.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t\t";
                    }
                } else {
                    if ($rAdminSettings["reseller_mag_events"]) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Seng MAG Event\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"message(" . $rRow["mag_id"] . ", '" . base64_decode($rRow["mac"]) . "');\"><i class=\"mdi mdi-comment-alert-outline\"></i></button>\r\n\t\t\t\t\t\t";
                    }
                    if ($rPermissions["is_reseller"] && $rPermissions["reseller_can_select_bouquets"] || $rPermissions["is_admin"]) {
                        $rButtons .= "<a href=\"./user_reseller_edit.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit User Bouquets\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-format-line-spacing\"></i></button></a>";
                    }
                    $rButtons .= "<a href=\"./user_reseller.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>";
                }
                if ($rPermissions["is_reseller"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_user")) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Kill Connections\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'kill');\"><i class=\"fas fa-hammer\"></i></button>\r\n\t\t\t\t\t";
                }
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_mag")) {
                    if ($rRow["admin_enabled"]) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Ban\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'ban');\"><i class=\"mdi mdi-minus-circle-outline\"></i></button>\r\n\t\t\t\t\t\t";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Unban\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'unban');\"><i class=\"mdi mdi-minus-circle\"></i></button>\r\n\t\t\t\t\t\t";
                    }
                }
                if ($rPermissions["is_reseller"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_mag")) {
                    if ($rRow["enabled"] == 1) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Disable\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'disable');\"><i class=\"mdi mdi-checkbox-blank-circle-outline\"></i></button>\r\n\t\t\t\t\t\t";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'enable');\"><i class=\"mdi mdi-checkbox-blank-circle\"></i></button>\r\n\t\t\t\t\t\t";
                    }
                }
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_mag") || $rPermissions["is_reseller"] && $rAdminSettings["reseller_mag_to_m3u"]) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Convert MAG to M3U\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'magtouser');\"><i class=\"mdi mdi-account-edit\"></i></button>";
                }
                if ($rPermissions["is_reseller"] && $rPermissions["delete_users"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_user")) {
                    $rButtons .= "<a href=\"./user_stats.php?user_id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"User Stats\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-chart-bar-stacked\"></i></button></a>";
                }
                if ($rPermissions["is_reseller"] && $rPermissions["delete_users"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_mag")) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                }
                $rButtons .= "</div>";
                if ($rRow["last_active"]) {
                    $rLastActive = date("Y-m-d H:i", $rRow["last_active"]);
                } else {
                    $rLastActive = "Never";
                }
                $today = time();
                $leftdaynumber = (strtotime(date("Y-m-d H:i", $rRow["exp_date"])) - $today) / 86400;
                $leftHourNumber = ($rRow["exp_date"] - $today) / 3600;
                $leftMinNumber = ($rRow["exp_date"] - $today) / 60;
                if (0 < $leftdaynumber && $leftdaynumber <= 1) {
                    $rLeftDate = "1 Day";
                } else {
                    if (1 < $leftdaynumber) {
                        $rLeftDate = round($leftdaynumber) . " Days";
                    } else {
                        if (0 < $leftHourNumber && $leftHourNumber <= 1) {
                            $rLeftDate = round($leftMinNumber) . " Minutes";
                        } else {
                            if (round($leftHourNumber) == 1) {
                                $rLeftDate = "1 Hour";
                            } else {
                                if (1 < $leftHourNumber) {
                                    $rLeftDate = round($leftHourNumber) . " Hours";
                                } else {
                                    $rLeftDate = "<center>-</center>";
                                }
                            }
                        }
                    }
                }
                $query = "SELECT user_activity_now.date_start, user_activity_now.geoip_country_code, user_activity_now.user_ip, user_activity_now.stream_id, user_activity_now.container, user_activity_now.user_id, streams.id, SUBSTR(`streams`.`stream_display_name`, 1, 45) stream_display_name FROM user_activity_now LEFT JOIN streams ON user_activity_now.stream_id = streams.id WHERE user_id = " . $rRow["id"];
                $result = $db->query($query);
                $row2 = mysqli_fetch_assoc($result);
                if (!empty($row2["stream_display_name"])) {
                    $rTime = intval(time()) - intval($row2["date_start"]);
                    $rStream_name = "<span style='color: #20a009;'</span>" . $row2["stream_display_name"] . " - ( <span style='color: #737373;'></span>" . "<span style='color: #737373;'></span>" . $row2["container"] . " )<span style='color: #737373;'></span>" . "<br><span style='color: #737373;'>" . "Uptime </span>" . "<span style='color: #737373;'>" . sprintf("%02d:%02d:%02d", $rTime / 3600, $rTime / 60 % 60, $rTime % 60) . "<br><a target='_blank' href='https://www.ip-tracker.org/locator/ip-lookup.php?ip=" . $row2["user_ip"] . "'</span>" . $row2["user_ip"] . " <img src='https://www.ip-tracker.org/images/ip-flags/" . strtolower($row2["geoip_country_code"]) . ".png'></img>" . "<br><span style='color: #737373;'></span>" . $rRow["isp_desc"];
                } else {
                    $rStream_name = "-";
                }
                $rButtons .= "</div>";
                if ($rPermissions["is_admin"]) {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./user.php?id=" . $rRow["id"] . "\">" . $rRow["username"], base64_decode($rRow["mac"]), $rRow["owner_name"], $rStatus, $rTrial, $rExpDate, $rLeftDate, $rRow["stb_type"], "<font size=1>" . $rStream_name, $rButtons];
                } else {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./user_reseller.php?id=" . $rRow["id"] . "\">" . $rRow["username"], base64_decode($rRow["mac"]), $rRow["owner_name"], $rStatus, $rTrial, $rExpDate, $rLeftDate, $rRow["stb_type"], "<font size=1>" . $rStream_name, $rButtons];
                }
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "enigmas") {
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "manage_e2")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`users`.`id`", "`users`.`username`", "`enigma2_devices`.`mac`", "`reg_users`.`username`", "`users`.`enabled`", "`users`.`is_trial`", "`users`.`exp_date`", "`users`.`exp_date`", "`active_connections`", false];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if ($rPermissions["is_reseller"]) {
        $rWhere[] = "`users`.`member_id` IN (" . join(",", array_keys(getRegisteredUsers($rUserInfo["id"]))) . ")";
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`users`.`username` LIKE '%" . $rSearch . "%' OR `enigma2_devices`.`mac` LIKE '%" . $rSearch . "%' OR `reg_users`.`username` LIKE '%" . $rSearch . "%' OR from_unixtime(`exp_date`) LIKE '%" . $rSearch . "%' OR `users`.`reseller_notes` LIKE '%" . $rSearch . "%' OR `users`.`admin_notes` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["filter"])) {
        if ($_GET["filter"] == 1) {
            $rWhere[] = "(`users`.`admin_enabled` = 1 AND `users`.`enabled` = 1 AND (`users`.`exp_date` IS NULL OR `users`.`exp_date` > UNIX_TIMESTAMP()))";
        } else {
            if ($_GET["filter"] == 2) {
                $rWhere[] = "`users`.`enabled` = 0";
            } else {
                if ($_GET["filter"] == 3) {
                    $rWhere[] = "`users`.`admin_enabled` = 0";
                } else {
                    if ($_GET["filter"] == 4) {
                        $rWhere[] = "(`users`.`exp_date` IS NOT NULL AND `users`.`exp_date` <= UNIX_TIMESTAMP())";
                    } else {
                        if ($_GET["filter"] == 5) {
                            $rWhere[] = "`users`.`is_trial` = 1";
                        } else {
                            if ($_GET["filter"] == 6) {
                                $rWhere[] = "`users`.`is_restreamer` = 1";
                            }
                        }
                    }
                }
            }
        }
    }
    if ($rPermissions["is_admin"] && 0 < strlen($_GET["reseller"])) {
        $rWhere[] = "`users`.`member_id` = " . intval($_GET["reseller"]);
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(`users`.`id`) AS `count` FROM `users` LEFT JOIN `reg_users` ON `reg_users`.`id` = `users`.`member_id` INNER JOIN `enigma2_devices` ON `enigma2_devices`.`user_id` = `users`.`id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `users`.`id`, `users`.`username`, `enigma2_devices`.`mac`, `users`.`created_at`, `users`.`exp_date`, `users`.`admin_enabled`, `users`.`enabled`, `users`.`isp_desc`, `users`.`is_isplock`, `users`.`admin_notes`, `users`.`reseller_notes`, `users`.`max_connections`,  `users`.`is_trial`, `reg_users`.`username` AS `owner_name`, (SELECT count(*) FROM `user_activity_now` WHERE `users`.`id` = `user_activity_now`.`user_id`) AS `active_connections`, (SELECT user_ip FROM `user_activity_now` WHERE `users`.`id` = `user_activity_now`.`user_id` LIMIT 1) AS `user_ip` FROM `users` LEFT JOIN `reg_users` ON `reg_users`.`id` = `users`.`member_id` INNER JOIN `enigma2_devices` ON `enigma2_devices`.`user_id` = `users`.`id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if (!$rRow["admin_enabled"]) {
                    $rStatus = "<button type='button' class='btn btn-danger btn-xs waves-effect waves-light'>BANNED</button>";
                } else {
                    if (!$rRow["enabled"]) {
                        $rStatus = "<button type='button' class='btn btn-secondary btn-sm waves-effect waves-light'>DISABLED</button>";
                    } else {
                        if ($rRow["exp_date"] && $rRow["exp_date"] < time()) {
                            $rStatus = "<button type='button' class='btn btn-info btn-sm waves-effect waves-light'>EXPIRED</button>";
                        } else {
                            $rStatus = "<button type='button' class='btn btn-success btn-sm waves-effect waves-light'>ACTIVE</button>";
                        }
                    }
                }
                if (0 < $rRow["active_connections"]) {
                    $rActive = "<button type='button' class='btn btn-success btn-sm waves-effect waves-light'>ONLINE</button>";
                } else {
                    $rActive = "<button type='button' class='btn btn-dark btn-sm waves-effect waves-light'>OFFLINE</button>";
                }
                if ($rRow["is_trial"]) {
                    $rTrial = "<button type='button' class='btn btn-warning btn-sm waves-effect waves-light'>TRIAL</button>";
                } else {
                    $rTrial = "<button type='button' class='btn btn-success btn-sm waves-effect waves-light'>OFFICIAL</button>";
                }
                if ($rRow["created_at"]) {
                    $rCreated = date("d-m-Y H:i", $rRow["created_at"]);
                } else {
                    $rCreated = date("d-m-Y H:i", $rCreated["created_at"]);
                }
                if ($rRow["exp_date"]) {
                    if ($rRow["exp_date"] < time()) {
                        $rExpDate = "<button type='button' class='btn btn-info btn-sm waves-effect waves-light' class=\"expired\">" . date("d-m-Y H:i", $rRow["exp_date"]) . " <i data-toggle='tooltip' data-placement='top' title='' data-original-title='Created : " . $rCreated . "' class='mdi mdi-information'></button>";
                    } else {
                        $rExpDate = date("d-m-Y H:i", $rRow["exp_date"]) . " <i data-toggle='tooltip' data-placement='top' title='' data-original-title='Created : " . $rCreated . "' class='mdi mdi-information'></button>";
                    }
                } else {
                    $rExpDate = "<button type='button' class='btn btn-pink btn-sm waves-effect waves-light'>UNLIMITED <i data-toggle='tooltip' data-placement='top' title='' data-original-title='Created : " . $rCreated . "' class='mdi mdi-information'></button>";
                }
                if ($rPermissions["is_reseller"] && $rPermissions["reseller_client_connection_logs"] || $rPermissions["is_admin"] && hasPermissions("adv", "live_connections")) {
                    $rActiveConnections = "<a href=\"./live_connections.php?user_id=" . $rRow["id"] . "\">" . $rRow["active_connections"] . "</a>";
                } else {
                    $rActiveConnections = $rRow["active_connections"];
                }
                $rButtons = "<div class=\"btn-group\">";
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_user") || $rPermissions["is_reseller"] && $rAdminSettings["reseller_reset_isplock"]) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Reset isp\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'resetispuser');\"><i class=\"mdi mdi-lock-reset\"></i></button>\r\n\t\t\t\t\t";
                }
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_user") || $rPermissions["is_reseller"] && $rAdminSettings["reseller_can_isplock"]) {
                    if ($rRow["is_isplock"]) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Unlock isp\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'unlockk');\"><i class=\"mdi mdi-lock\"></i></button>";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Lock isp\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'lockk');\"><i class=\"mdi mdi-lock-outline\"></i></button>";
                    }
                }
                if ($rPermissions["is_admin"]) {
                    $rButtons .= "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Extend line\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"renew_user('" . $rRow["id"] . "', '" . $rRow["username"] . "');\"><i class=\"mdi mdi-autorenew\"></i></button>\r\n                         ";
                }
                if (0 < strlen($rRow["admin_notes"]) && $rPermissions["is_admin"] || 0 < strlen($rRow["reseller_notes"])) {
                    $rNotes = "";
                    if ($rPermissions["is_admin"] && 0 < strlen($rRow["admin_notes"])) {
                        $rNotes .= $rRow["admin_notes"];
                    }
                    if (0 < strlen($rRow["reseller_notes"])) {
                        if (strlen($rNotes) != 0) {
                            $rNotes .= "\n";
                        }
                        $rNotes .= $rRow["reseller_notes"];
                    }
                    $rButtons .= "<button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"\" data-original-title=\"" . $rNotes . "\"><i class=\"mdi mdi-note\"></i></button>";
                } else {
                    $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-note\"></i></button>";
                }
                if ($rPermissions["is_admin"]) {
                    if (hasPermissions("adv", "edit_e2")) {
                        $rButtons .= "<a href=\"./user.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>";
                    }
                } else {
                    if ($rPermissions["is_reseller"] && $rPermissions["reseller_can_select_bouquets"] || $rPermissions["is_admin"]) {
                        $rButtons .= "<a href=\"./user_reseller_edit.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit User Bouquets\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-format-line-spacing\"></i></button></a>";
                    }
                    $rButtons .= "<a href=\"./user_reseller.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>";
                }
                if ($rPermissions["is_reseller"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_user")) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Kill Connections\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'kill');\"><i class=\"fas fa-hammer\"></i></button>\r\n\t\t\t\t\t";
                }
                if ($rPermissions["is_admin"] && hasPermissions("adv", "edit_e2")) {
                    if ($rRow["admin_enabled"]) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Ban\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'ban');\"><i class=\"mdi mdi-minus-circle-outline\"></i></button>";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Unban\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'unban');\"><i class=\"mdi mdi-minus-circle\"></i></button>";
                    }
                }
                if ($rPermissions["is_reseller"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_e2")) {
                    if ($rRow["enabled"]) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Disable\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'disable');\"><i class=\"mdi mdi-checkbox-blank-circle-outline\"></i></button>\r\n\t\t\t\t\t\t";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'enable');\"><i class=\"mdi mdi-checkbox-blank-circle\"></i></button>\r\n\t\t\t\t\t\t";
                    }
                }
                if ($rPermissions["is_reseller"] && $rPermissions["delete_users"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_user")) {
                    $rButtons .= "<a href=\"./user_stats.php?user_id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"User Stats\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-chart-bar-stacked\"></i></button></a>";
                }
                if ($rPermissions["is_reseller"] && $rPermissions["delete_users"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_e2")) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                }
                $today = time();
                $leftdaynumber = (strtotime(date("Y-m-d H:i", $rRow["exp_date"])) - $today) / 86400;
                $leftHourNumber = ($rRow["exp_date"] - $today) / 3600;
                $leftMinNumber = ($rRow["exp_date"] - $today) / 60;
                if (0 < $leftdaynumber && $leftdaynumber <= 1) {
                    $rLeftDate = "1 Day";
                } else {
                    if (1 < $leftdaynumber) {
                        $rLeftDate = round($leftdaynumber) . " Days";
                    } else {
                        if (0 < $leftHourNumber && $leftHourNumber <= 1) {
                            $rLeftDate = round($leftMinNumber) . " Minutes";
                        } else {
                            if (round($leftHourNumber) == 1) {
                                $rLeftDate = "1 Hour";
                            } else {
                                if (1 < $leftHourNumber) {
                                    $rLeftDate = round($leftHourNumber) . " Hours";
                                } else {
                                    $rLeftDate = "<center>-</center>";
                                }
                            }
                        }
                    }
                }
                $query = "SELECT user_activity_now.date_start, user_activity_now.geoip_country_code, user_activity_now.user_ip, user_activity_now.stream_id, user_activity_now.container, user_activity_now.user_id, streams.id, SUBSTR(`streams`.`stream_display_name`, 1, 45) stream_display_name  FROM user_activity_now LEFT JOIN streams ON user_activity_now.stream_id = streams.id WHERE user_id = " . $rRow["id"];
                $result = $db->query($query);
                $row2 = mysqli_fetch_assoc($result);
                if (!empty($row2["stream_display_name"])) {
                    $rTime = intval(time()) - intval($row2["date_start"]);
                    $rStream_name = "<span style='color: #20a009;'</span>" . $row2["stream_display_name"] . " - ( <span style='color: #737373;'></span>" . "<span style='color: #737373;'></span>" . $row2["container"] . " )<span style='color: #737373;'></span>" . "<br><span style='color: #737373;'>" . "Uptime </span>" . "<span style='color: #737373;'>" . sprintf("%02d:%02d:%02d", $rTime / 3600, $rTime / 60 % 60, $rTime % 60) . "<br><a target='_blank' href='https://www.ip-tracker.org/locator/ip-lookup.php?ip=" . $row2["user_ip"] . "'</span>" . $row2["user_ip"] . " <img src='https://www.ip-tracker.org/images/ip-flags/" . strtolower($row2["geoip_country_code"]) . ".png'></img>" . "<br><span style='color: #737373;'></span>" . $rRow["isp_desc"];
                } else {
                    $rStream_name = "-";
                }
                $rButtons .= "</div>";
                if ($rPermissions["is_admin"]) {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./user.php?id=" . $rRow["id"] . "\">" . $rRow["username"], $rRow["mac"], $rRow["owner_name"], $rStatus, $rTrial, $rExpDate, $rLeftDate, "<font size=1>" . $rStream_name, $rButtons];
                } else {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./user_reseller.php?id=" . $rRow["id"] . "\">" . $rRow["username"], $rRow["mac"], $rRow["owner_name"], $rStatus, $rTrial, $rExpDate, $rLeftDate, "<font size=1>" . $rStream_name, $rButtons];
                }
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "streams") {
    if ($rPermissions["is_reseller"] && !$rPermissions["reset_stb_data"]) {
        exit;
    }
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "streams") && !hasPermissions("adv", "mass_edit_streams")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    if ($rPermissions["is_admin"] && $rAdminSettings["order_streams"]) {
        $rOrder = ["`streams`.`order`", false, "`streams`.`stream_display_name`", "`streaming_servers`.`server_name`", "`clients`", "`streams_sys`.`stream_started`", false, false, "`count_epg`", "`streams_sys`.`bitrate`"];
    } else {
        $rOrder = ["`streams`.`id`", false, "`streams`.`stream_display_name`", "`streaming_servers`.`server_name`", "`clients`", "`streams_sys`.`stream_started`", false, false, "`count_epg`", "`streams_sys`.`bitrate`"];
    }
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    $rWhere[] = "`streams`.`type` in (1,3)";
    if (isset($_GET["stream_id"])) {
        $rWhere[] = "`streams`.`id` = " . intval($_GET["stream_id"]);
        $rOrderBy = "ORDER BY `streams_sys`.`server_stream_id` ASC";
    } else {
        if (0 < strlen($_GET["search"]["value"])) {
            $rSearch = $_GET["search"]["value"];
            $rWhere[] = "(`streams`.`id` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `streams`.`notes` LIKE '%" . $rSearch . "%' OR `streams_sys`.`current_source` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%' OR `streaming_servers`.`server_name` LIKE '%" . $rSearch . "%')";
        }
        if (0 < strlen($_GET["filter"])) {
            if ($_GET["filter"] == 1) {
                $rWhere[] = "(`streams_sys`.`monitor_pid` > 0 AND `streams_sys`.`pid` > 0)";
            } else {
                if ($_GET["filter"] == 2) {
                    $rWhere[] = "((`streams_sys`.`monitor_pid` IS NOT NULL AND `streams_sys`.`monitor_pid` > 0) AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` <> 0)";
                } else {
                    if ($_GET["filter"] == 3) {
                        $rWhere[] = "(`streams`.`direct_source` = 0 AND (`streams_sys`.`monitor_pid` IS NULL OR `streams_sys`.`monitor_pid` <= 0) AND `streams_sys`.`on_demand` = 0)";
                    } else {
                        if ($_GET["filter"] == 4) {
                            $rWhere[] = "((`streams_sys`.`monitor_pid` IS NOT NULL AND `streams_sys`.`monitor_pid` > 0) AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` = 0)";
                        } else {
                            if ($_GET["filter"] == 5) {
                                $rWhere[] = "`streams_sys`.`on_demand` = 1";
                            } else {
                                if ($_GET["filter"] == 6) {
                                    $rWhere[] = "`streams`.`direct_source` = 1";
                                } else {
                                    if ($_GET["filter"] == 7) {
                                        $rWhere[] = "`streams`.`tv_archive_duration` > 0";
                                    } else {
                                        if ($_GET["filter"] == 8) {
                                            $rWhere[] = "`streams`.`type` = 3";
                                        } else {
                                            if ($_GET["filter"] == 9) {
                                                $rWhere[] = "`streams`.`stream_source` = '[]'";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (0 < strlen($_GET["category"])) {
            $rWhere[] = "`streams`.`category_id` = " . intval($_GET["category"]);
        }
        if (0 < strlen($_GET["server"])) {
            $rWhere[] = "`streams_sys`.`server_id` = " . intval($_GET["server"]);
        }
        if ($rOrder[$rOrderRow]) {
            $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
            $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
        }
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `streams_sys`.`server_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT (SELECT COUNT(`id`) FROM `epg_data` WHERE `epg_data`.`epg_id` = `streams`.`epg_id` AND `epg_data`.`channel_id` = `streams`.`channel_id`) AS `count_epg`, `streams`.`id`, `streams`.`type`, `streams`.`stream_icon`, `streams`.`cchannel_rsources`, `streams`.`stream_source`, `streams`.`stream_display_name`, `streams`.`tv_archive_duration`, `streams_sys`.`server_id`, `streams`.`notes`, `streams`.`direct_source`, `streams_sys`.`pid`, `streams_sys`.`monitor_pid`, `streams_sys`.`stream_status`, `streams_sys`.`stream_started`, `streams_sys`.`stream_info`, `streams_sys`.`current_source`, `streams_sys`.`bitrate`, `streams_sys`.`progress_info`, `streams_sys`.`on_demand`, `stream_categories`.`category_name`, `streaming_servers`.`server_name`, (SELECT COUNT(*) FROM `user_activity_now` WHERE `user_activity_now`.`server_id` = `streams_sys`.`server_id` AND `user_activity_now`.`stream_id` = `streams`.`id`) AS `clients` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `streams_sys`.`server_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rCategory = $rRow["category_name"] ?: "No Category";
                if (0 < $rRow["tv_archive_duration"]) {
                    $rRow["stream_display_name"] .= " <i class='mdi mdi-record'></i>";
                }
                $rStreamName = "<b>" . $rRow["stream_display_name"] . "</b><br><span class= text-danger style='font-size:11px;'>" . $rCategory . "</span>";
                if ($rRow["server_name"]) {
                    if ($rPermissions["is_admin"]) {
                        $rServerName = $rRow["server_name"];
                    } else {
                        $rServerName = "Server #" . $rRow["server_id"];
                    }
                } else {
                    $rServerName = "No Server Selected";
                }
                if ($rRow["type"] == 3) {
                    $rStreamSource = "<br/><span style='font-size:11px;'>Created Channel</span>";
                } else {
                    $rStreamSource = "<br/><span style='font-size:11px;'>" . parse_url($rRow["current_source"])["host"] . "</span>";
                }
                if ($rPermissions["is_admin"]) {
                    $rServerName .= $rStreamSource;
                }
                $rUptime = 0;
                $rActualStatus = 0;
                if (intval($rRow["direct_source"]) == 1) {
                    $rActualStatus = 5;
                } else {
                    if ($rRow["monitor_pid"]) {
                        if ($rRow["pid"] && 0 < $rRow["pid"]) {
                            $rActualStatus = 1;
                            $rUptime = time() - intval($rRow["stream_started"]);
                        } else {
                            if (intval($rRow["stream_status"]) == 0) {
                                $rActualStatus = 2;
                            } else {
                                $rActualStatus = 3;
                            }
                        }
                    } else {
                        if (intval($rRow["on_demand"]) == 1) {
                            $rActualStatus = 4;
                        } else {
                            $rActualStatus = 0;
                        }
                    }
                }
                if ($rRow["type"] == 3 && count(json_decode($rRow["cchannel_rsources"], true)) != count(json_decode($rRow["stream_source"], true))) {
                    $rActualStatus = 6;
                }
                if (hasPermissions("adv", "live_connections")) {
                    $rClients = "<a class=' btn btn-light btn btn-secondary waves-light btn-xs' href=\"./live_connections.php?stream_id=" . $rRow["id"] . "&server_id=" . $rRow["server_id"] . "\">" . $rRow["clients"] . "</a>";
                } else {
                    $rClients = $rRow["clients"];
                }
                if ($rActualStatus == 1) {
                    if (86400 <= $rUptime) {
                        $rUptime = sprintf("%02dd %02dh %02dm %02ds", $rUptime / 86400, $rUptime / 3600 % 24, $rUptime / 60 % 60, $rUptime % 60);
                    } else {
                        $rUptime = sprintf("%02dh %02dm %02ds", $rUptime / 3600, $rUptime / 60 % 60, $rUptime % 60);
                    }
                    $rUptime = "<button type='button' class='btn btn-success btn-xs waves-effect waves-light'>" . $rUptime . "</button>";
                } else {
                    $rUptime = $rStatusArray[$rActualStatus];
                }
                if (!$rRow["server_id"]) {
                    $rRow["server_id"] = 0;
                }
                $rButtons = "<div class=\"btn-group\">";
                if ($rPermissions["is_admin"]) {
                    if (0 < strlen($rRow["notes"])) {
                        $rButtons .= "<button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"\" data-original-title=\"" . $rRow["notes"] . "\"><i class=\"mdi mdi-note\"></i></button>";
                    } else {
                        $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-note\"></i></button>";
                    }
                }
                if (hasPermissions("adv", "edit_stream")) {
                    if (intval($rActualStatus) == 1 || intval($rActualStatus) == 2 || intval($rActualStatus) == 3 || $rRow["on_demand"] == 1 || $rActualStatus == 5) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Stop\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-stop\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'stop');\"><i class=\"mdi mdi-stop\"></i></button>\r\n\t\t\t\t\t\t";
                        $rStatus = "";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Start\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-start\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'start');\"><i class=\"mdi mdi-play\"></i></button>\r\n\t\t\t\t\t\t";
                        $rStatus = " disabled";
                    }
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Restart\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-restart\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'restart');\"" . $rStatus . "><i class=\"mdi mdi-refresh\"></i></button>\r\n\t\t\t\t\t";
                    if ($rRow["type"] == 3) {
                        $rButtons .= "<a href=\"./created_channel.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t\t";
                    } else {
                        $rButtons .= "<a href=\"./stream.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t\t";
                    }
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>\r\n\t\t\t\t\t";
                }
                $rButtons .= "</div>";
                if (hasPermissions("adv", "player")) {
                    if ((intval($rActualStatus) == 1 || $rRow["on_demand"] == 1 || $rActualStatus == 5) && 0 < strlen($rAdminSettings["admin_username"]) && 0 < strlen($rAdminSettings["admin_password"])) {
                        $rPlayer = "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Play\" type=\"button\" class=\"btn btn-outline-info waves-effect waves-light btn-xs\" onClick=\"player(" . $rRow["id"] . ");\"><i class=\"mdi mdi-play\"></i></button>";
                    } else {
                        $rPlayer = "<button type=\"button\" disabled class=\"btn btn-info waves-effect waves-light btn-xs\"><i class=\"mdi mdi-play\"></i></button>";
                    }
                } else {
                    $rPlayer = "<button type=\"button\" disabled class=\"btn btn-info waves-effect waves-light btn-xs\"><i class=\"mdi mdi-play\"></i></button>";
                }
                $rStreamInfoText = "<div style='font-size: 10px;' class='text-center' align='center'><tbody><tr><td colspan='5' class='col'>No information available</td></tr></tbody></div>";
                $rStreamInfo = json_decode($rRow["stream_info"], true);
                $rProgressInfo = json_decode($rRow["progress_info"], true);
                if ($rActualStatus == 1) {
                    if (!isset($rStreamInfo["codecs"]["video"])) {
                        $rStreamInfo["codecs"]["video"] = ["width" => "?", "height" => "?", "codec_name" => "N/A", "r_frame_rate" => "--"];
                    }
                    if (!isset($rStreamInfo["codecs"]["audio"])) {
                        $rStreamInfo["codecs"]["audio"] = ["codec_name" => "N/A"];
                    }
                    if ($rRow["bitrate"] == 0) {
                        $rRow["bitrate"] = "?";
                    }
                    if (isset($rProgressInfo["speed"])) {
                        $rSpeed = $rProgressInfo["speed"];
                    } else {
                        $rSpeed = "--";
                    }
                    if (isset($rProgressInfo["fps"])) {
                        $rFPS = intval($rProgressInfo["fps"]) . " FPS";
                    } else {
                        if (isset($rStreamInfo["codecs"]["video"]["r_frame_rate"])) {
                            $rFPS = intval($rStreamInfo["codecs"]["video"]["r_frame_rate"]) . " FPS";
                        } else {
                            $rFPS = "--";
                        }
                    }
                    $rStreamInfoText = "<div style='font-size: 13px;' class='text-center' align='center'>\r\n\t\t\t\t\t            <td class='col'>" . $rRow["bitrate"] . " Kbps " . $rStreamInfo["codecs"]["video"]["width"] . "x" . $rStreamInfo["codecs"]["video"]["height"] . "</td>\r\n\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t<td class='col'><i class='mdi mdi-video' data-name='mdi-video' style='color: #20a009;'></i> " . $rStreamInfo["codecs"]["video"]["codec_name"] . "</td>\r\n                                <td class='col'><i class='mdi mdi-volume-high' data-name='mdi-volume-high' style='color: #20a009;'></i> " . $rStreamInfo["codecs"]["audio"]["codec_name"] . "</td>\r\n                                <td class='col'><i class='mdi mdi-play-speed' data-name='mdi-play-speed' style='color: #20a009;'></i> " . $rSpeed . "</td>\r\n                                <td class='col'><i class='mdi mdi-layers' data-name='mdi-layers' style='color: #20a009;'></i> " . $rFPS . "</td>\r\n                    </div>";
                }
                if (0 < $rRow["count_epg"]) {
                    $rEPG = "<button type=\"button\" class=\"btn btn-success btn-xs waves-effect waves-light\"><i class=\"mdi mdi-checkbox-marked-outline\"></i></button>";
                } else {
                    if ($rRow["channel_id"]) {
                        $rEPG = "<button type=\"button\" class=\"btn btn-warning btn-xs waves-effect waves-light\"><i class=\"mdi mdi-alert-box\"></i></button>";
                    } else {
                        $rEPG = "<button type=\"button\" class=\"btn btn-danger btn-xs waves-effect waves-light\"><i class=\"mdi mdi-sync-alert\"></i></button>";
                    }
                }
                if (0 < strlen($rRow["stream_icon"])) {
                    $rIcon = "<img src='./resize.php?max=42&url=" . $rRow["stream_icon"] . "' />";
                } else {
                    $rIcon = "";
                }
                if ($rPermissions["is_admin"]) {
                    $rReturn["data"][] = [$rRow["id"], $rIcon, $rStreamName, $rServerName, $rClients, $rUptime, $rButtons, $rPlayer, $rEPG, $rStreamInfoText];
                } else {
                    $rReturn["data"][] = [$rRow["id"], $rIcon, $rStreamName, $rServerName, $rStreamInfoText];
                }
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "radios") {
    if ($rPermissions["is_reseller"] && !$rPermissions["reset_stb_data"]) {
        exit;
    }
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "radio") && !hasPermissions("adv", "mass_edit_radio")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    if ($rPermissions["is_admin"]) {
        $rOrder = ["`streams`.`id`", "`streams`.`stream_display_name`", "`streams_sys`.`current_source`", "`clients`", "`streams_sys`.`stream_started`", false, "`streams_sys`.`bitrate`"];
    } else {
        $rOrder = ["`streams`.`id`", "`streams`.`stream_display_name`", "`streams_sys`.`current_source`", "`streams_sys`.`bitrate`"];
    }
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    $rWhere[] = "`streams`.`type` = 4";
    if (isset($_GET["stream_id"])) {
        $rWhere[] = "`streams`.`id` = " . intval($_GET["stream_id"]);
        $rOrderBy = "ORDER BY `streams_sys`.`server_stream_id` ASC";
    } else {
        if (0 < strlen($_GET["search"]["value"])) {
            $rSearch = $_GET["search"]["value"];
            $rWhere[] = "(`streams`.`id` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `streams`.`notes` LIKE '%" . $rSearch . "%' OR `streams_sys`.`current_source` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%' OR `streaming_servers`.`server_name` LIKE '%" . $rSearch . "%')";
        }
        if (0 < strlen($_GET["filter"])) {
            if ($_GET["filter"] == 1) {
                $rWhere[] = "(`streams_sys`.`monitor_pid` > 0 AND `streams_sys`.`pid` > 0)";
            } else {
                if ($_GET["filter"] == 2) {
                    $rWhere[] = "((`streams_sys`.`monitor_pid` IS NOT NULL AND `streams_sys`.`monitor_pid` > 0) AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` <> 0)";
                } else {
                    if ($_GET["filter"] == 3) {
                        $rWhere[] = "(`streams`.`direct_source` = 0 AND (`streams_sys`.`monitor_pid` IS NULL OR `streams_sys`.`monitor_pid` <= 0) AND `streams_sys`.`on_demand` = 0)";
                    } else {
                        if ($_GET["filter"] == 4) {
                            $rWhere[] = "((`streams_sys`.`monitor_pid` IS NOT NULL AND `streams_sys`.`monitor_pid` > 0) AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` = 0)";
                        } else {
                            if ($_GET["filter"] == 5) {
                                $rWhere[] = "`streams_sys`.`on_demand` = 1";
                            } else {
                                if ($_GET["filter"] == 6) {
                                    $rWhere[] = "`streams`.`direct_source` = 1";
                                }
                            }
                        }
                    }
                }
            }
        }
        if (0 < strlen($_GET["category"])) {
            $rWhere[] = "`streams`.`category_id` = " . intval($_GET["category"]);
        }
        if (0 < strlen($_GET["server"])) {
            $rWhere[] = "`streams_sys`.`server_id` = " . intval($_GET["server"]);
        }
        if ($rOrder[$rOrderRow]) {
            $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
            $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
        }
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `streams_sys`.`server_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `streams`.`id`, `streams`.`type`, `streams`.`cchannel_rsources`, `streams`.`stream_source`, `streams`.`stream_display_name`, `streams`.`tv_archive_duration`, `streams_sys`.`server_id`, `streams`.`notes`, `streams`.`direct_source`, `streams_sys`.`pid`, `streams_sys`.`monitor_pid`, `streams_sys`.`stream_status`, `streams_sys`.`stream_started`, `streams_sys`.`stream_info`, `streams_sys`.`current_source`, `streams_sys`.`bitrate`, `streams_sys`.`progress_info`, `streams_sys`.`on_demand`, `stream_categories`.`category_name`, `streaming_servers`.`server_name`, (SELECT COUNT(*) FROM `user_activity_now` WHERE `user_activity_now`.`server_id` = `streams_sys`.`server_id` AND `user_activity_now`.`stream_id` = `streams`.`id`) AS `clients` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `streams_sys`.`server_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rCategory = $rRow["category_name"] ?: "No Category";
                $rStreamName = "<b>" . $rRow["stream_display_name"] . "</b><br><span class= text-danger style='font-size:11px;'>" . $rCategory . "</span>";
                if ($rRow["server_name"]) {
                    if ($rPermissions["is_admin"]) {
                        $rServerName = $rRow["server_name"];
                    } else {
                        $rServerName = "Server #" . $rRow["server_id"];
                    }
                } else {
                    $rServerName = "No Server Selected";
                }
                $rStreamSource = "<br/><span style='font-size:11px;'>" . parse_url($rRow["current_source"])["host"] . "</span>";
                if ($rPermissions["is_admin"]) {
                    $rServerName .= $rStreamSource;
                }
                $rUptime = 0;
                $rActualStatus = 0;
                if (intval($rRow["direct_source"]) == 1) {
                    $rActualStatus = 5;
                } else {
                    if ($rRow["monitor_pid"]) {
                        if ($rRow["pid"] && 0 < $rRow["pid"]) {
                            $rActualStatus = 1;
                            $rUptime = time() - intval($rRow["stream_started"]);
                        } else {
                            if (intval($rRow["stream_status"]) == 0) {
                                $rActualStatus = 2;
                            } else {
                                $rActualStatus = 3;
                            }
                        }
                    } else {
                        if (intval($rRow["on_demand"]) == 1) {
                            $rActualStatus = 4;
                        } else {
                            $rActualStatus = 0;
                        }
                    }
                }
                if (hasPermissions("adv", "live_connections")) {
                    $rClients = "<a class='btn btn-light btn btn-secondary waves-light btn-xs' href=\"./live_connections.php?stream_id=" . $rRow["id"] . "&server_id=" . $rRow["server_id"] . "\">" . $rRow["clients"] . "</a>";
                } else {
                    $rClients = $rRow["clients"];
                }
                if ($rActualStatus == 1) {
                    if (86400 <= $rUptime) {
                        $rUptime = sprintf("%02dd %02dh %02dm %02ds", $rUptime / 86400, $rUptime / 3600 % 24, $rUptime / 60 % 60, $rUptime % 60);
                    } else {
                        $rUptime = sprintf("%02dh %02dm %02ds", $rUptime / 3600, $rUptime / 60 % 60, $rUptime % 60);
                    }
                    $rUptime = "<button type='button' class='btn btn-success btn-xs waves-effect waves-light'>" . $rUptime . "</button>";
                } else {
                    $rUptime = $rStatusArray[$rActualStatus];
                }
                if (!$rRow["server_id"]) {
                    $rRow["server_id"] = 0;
                }
                $rButtons = "<div class=\"btn-group\">";
                if ($rPermissions["is_admin"]) {
                    if (0 < strlen($rRow["notes"])) {
                        $rButtons .= "<button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"\" data-original-title=\"" . $rRow["notes"] . "\"><i class=\"mdi mdi-note\"></i></button>";
                    } else {
                        $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-note\"></i></button>";
                    }
                }
                if (hasPermissions("adv", "edit_radio")) {
                    if (intval($rActualStatus) == 1 || intval($rActualStatus) == 2 || intval($rActualStatus) == 3 || $rRow["on_demand"] == 1 || $rActualStatus == 5) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Stop\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-stop\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'stop');\"><i class=\"mdi mdi-stop\"></i></button>\r\n\t\t\t\t\t\t";
                        $rStatus = "";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Start\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-start\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'start');\"><i class=\"mdi mdi-play\"></i></button>\r\n\t\t\t\t\t\t";
                        $rStatus = " disabled";
                    }
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Restart\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-restart\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'restart');\"" . $rStatus . "><i class=\"mdi mdi-refresh\"></i></button>\r\n\t\t\t\t\t";
                    if ($rRow["type"] == 3) {
                        $rButtons .= "<a href=\"./created_channel.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t\t";
                    } else {
                        $rButtons .= "<a href=\"./radio.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t\t";
                    }
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>\r\n\t\t\t\t\t";
                }
                $rButtons .= "</div>";
                $rStreamInfoText = "<table style='font-size: 10px;' class='text-center' align='center'><tbody><tr><td colspan='5' class='col'>No information available</td></tr></tbody></table>";
                $rStreamInfo = json_decode($rRow["stream_info"], true);
                $rProgressInfo = json_decode($rRow["progress_info"], true);
                if ($rActualStatus == 1) {
                    if (!isset($rStreamInfo["codecs"]["video"])) {
                        $rStreamInfo["codecs"]["video"] = ["width" => "?", "height" => "?", "codec_name" => "N/A", "r_frame_rate" => "--"];
                    }
                    if (!isset($rStreamInfo["codecs"]["audio"])) {
                        $rStreamInfo["codecs"]["audio"] = ["codec_name" => "N/A"];
                    }
                    if ($rRow["bitrate"] == 0) {
                        $rRow["bitrate"] = "?";
                    }
                    if (isset($rProgressInfo["speed"])) {
                        $rSpeed = $rProgressInfo["speed"];
                    } else {
                        $rSpeed = "--";
                    }
                    if (isset($rProgressInfo["fps"])) {
                        $rFPS = intval($rProgressInfo["fps"]) . " FPS";
                    } else {
                        if (isset($rStreamInfo["codecs"]["video"]["r_frame_rate"])) {
                            $rFPS = intval($rStreamInfo["codecs"]["video"]["r_frame_rate"]) . " FPS";
                        } else {
                            $rFPS = "--";
                        }
                    }
                    $rStreamInfoText = "<div style='font-size: 13px;' class='text-center' align='center'>\r\n\t\t\t\t\t\t\t\t<td class='col'><i class='mdi mdi-video' data-name='mdi-video' style='color: #20a009;'></i> " . $rRow["bitrate"] . " Kbps</td>\r\n\t\t\t\t\t\t\t\t<td class='col'><i class='mdi mdi-volume-high' data-name='mdi-volume-high' style='color: #20a009;'></i> " . $rStreamInfo["codecs"]["audio"]["codec_name"] . "</td>\r\n\t\t\t\t\t\t\t\t<td class='col'><i class='mdi mdi-play-speed' data-name='mdi-play-speed'></i>" . $rSpeed . "</td>\r\n                    </div>";
                }
                if ($rPermissions["is_admin"]) {
                    $rReturn["data"][] = [$rRow["id"], $rStreamName, $rServerName, $rClients, $rUptime, $rButtons, $rStreamInfoText];
                } else {
                    $rReturn["data"][] = [$rRow["id"], $rStreamName, $rServerName, $rStreamInfoText];
                }
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "movies") {
    if ($rPermissions["is_reseller"] && !$rPermissions["reset_stb_data"]) {
        exit;
    }
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "movies") && !hasPermissions("adv", "mass_sedits_vod")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`streams`.`id`", false, "`streams`.`stream_display_name`", "`streaming_servers`.`server_name`", "`clients`", "`streams_sys`.`stream_status`", false, false, false, false, "`streams_sys`.`bitrate`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    $rWhere[] = "`streams`.`type` = 2";
    if (isset($_GET["stream_id"])) {
        $rWhere[] = "`streams`.`id` = " . intval($_GET["stream_id"]);
        $rOrderBy = "ORDER BY `streams_sys`.`server_stream_id` ASC";
    } else {
        if (0 < strlen($_GET["search"]["value"])) {
            $rSearch = $_GET["search"]["value"];
            $rWhere[] = "(`streams`.`id` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `streams`.`notes` LIKE '%" . $rSearch . "%' OR `streams_sys`.`current_source` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%' OR `streaming_servers`.`server_name` LIKE '%" . $rSearch . "%')";
        }
        if (0 < strlen($_GET["filter"])) {
            if ($_GET["filter"] == 1) {
                $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`pid` > 0 AND `streams_sys`.`to_analyze` = 0 AND `streams_sys`.`stream_status` <> 1)";
            } else {
                if ($_GET["filter"] == 2) {
                    $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`pid` > 0 AND `streams_sys`.`to_analyze` = 1 AND `streams_sys`.`stream_status` <> 1)";
                } else {
                    if ($_GET["filter"] == 3) {
                        $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`stream_status` = 1)";
                    } else {
                        if ($_GET["filter"] == 4) {
                            $rWhere[] = "(`streams`.`direct_source` = 0 AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` <> 1)";
                        } else {
                            if ($_GET["filter"] == 5) {
                                $rWhere[] = "`streams`.`direct_source` = 1";
                            } else {
                                if ($_GET["filter"] == 6) {
                                    $rWhere[] = "(`streams`.`movie_propeties` IS NULL OR `streams`.`movie_propeties` = '' OR `streams`.`movie_propeties` = '[]' OR `streams`.`movie_propeties` = '{}' OR `streams`.`movie_propeties` LIKE '%tmdb_id\":\"\"%')";
                                }
                            }
                        }
                    }
                }
            }
        }
        if (0 < strlen($_GET["category"])) {
            $rWhere[] = "`streams`.`category_id` = " . intval($_GET["category"]);
        }
        if (0 < strlen($_GET["server"])) {
            $rWhere[] = "`streams_sys`.`server_id` = " . intval($_GET["server"]);
        }
        if ($rOrder[$rOrderRow]) {
            $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
            $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
        }
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `streams_sys`.`server_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `streams`.`id`, `streams_sys`.`to_analyze`, `streams`.`movie_propeties`,`streams`.`target_container`, `streams`.`stream_display_name`, `streams_sys`.`server_id`, `streams`.`notes`, `streams`.`direct_source`, `streams`.`added`, `streams_sys`.`pid`, `streams_sys`.`monitor_pid`, `streams_sys`.`stream_status`, `streams_sys`.`stream_started`, `streams_sys`.`stream_info`, `streams_sys`.`current_source`, `streams_sys`.`bitrate`, `streams_sys`.`progress_info`, `streams_sys`.`on_demand`, `stream_categories`.`category_name`, `streaming_servers`.`server_name`, (SELECT COUNT(*) FROM `user_activity_now` WHERE `user_activity_now`.`server_id` = `streams_sys`.`server_id` AND `user_activity_now`.`stream_id` = `streams`.`id`) AS `clients` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `streams_sys`.`server_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rCategory = $rRow["category_name"] ?: "No Category";
                $rStreamName = "<b>" . $rRow["stream_display_name"] . "</b><br><span class= text-danger style='font-size:11px;'>" . $rCategory . "</span>";
                if ($rRow["server_name"]) {
                    if ($rPermissions["is_admin"]) {
                        $rServerName = $rRow["server_name"];
                    } else {
                        $rServerName = "Server #" . $rRow["server_id"];
                    }
                } else {
                    $rServerName = "No Server Selected";
                }
                $rUptime = 0;
                $rActualStatus = 0;
                if (intval($rRow["direct_source"]) == 1) {
                    $rActualStatus = 3;
                } else {
                    if ($rRow["pid"]) {
                        if ($rRow["to_analyze"] == 1) {
                            $rActualStatus = 2;
                        } else {
                            if ($rRow["stream_status"] == 1) {
                                $rActualStatus = 4;
                            } else {
                                $rActualStatus = 1;
                            }
                        }
                    } else {
                        $rActualStatus = 0;
                    }
                }
                if (hasPermissions("adv", "live_connections")) {
                    $rClients = "<a class='btn btn-light btn btn-secondary waves-light btn-xs' href=\"./live_connections.php?stream_id=" . $rRow["id"] . "&server_id=" . $rRow["server_id"] . "\">" . $rRow["clients"] . "</a>";
                } else {
                    $rClients = $rRow["clients"];
                }
                if (!$rRow["server_id"]) {
                    $rRow["server_id"] = 0;
                }
                $rButtons = "<div class=\"btn-group\">";
                if ($rPermissions["is_admin"]) {
                    if (0 < strlen($rRow["notes"])) {
                        $rButtons .= "<button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"\" data-original-title=\"" . $rRow["notes"] . "\"><i class=\"mdi mdi-note\"></i></button>";
                    } else {
                        $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-note\"></i></button>";
                    }
                }
                if (hasPermissions("adv", "edit_movie")) {
                    if (intval($rActualStatus) == 1) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Encode\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-start\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'start');\"><i class=\"mdi mdi-refresh\"></i></button>\r\n\t\t\t\t\t\t";
                    } else {
                        if (intval($rActualStatus) == 3) {
                            $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-stop\"><i class=\"mdi mdi-stop\"></i></button>\r\n\t\t\t\t\t\t";
                        } else {
                            if (intval($rActualStatus) == 2) {
                                $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Stop Encoding\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-stop\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'stop');\"><i class=\"mdi mdi-stop\"></i></button>\r\n\t\t\t\t\t\t";
                            } else {
                                $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Start Encoding\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-start\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'start');\"><i class=\"mdi mdi-play\"></i></button>\r\n\t\t\t\t\t\t";
                            }
                        }
                    }
                    $rButtons .= "<a href=\"./movie.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                }
                $rButtons .= "</div>";
                if (hasPermissions("adv", "player")) {
                    if ((intval($rActualStatus) == 1 || $rActualStatus == 3) && 0 < strlen($rAdminSettings["admin_username"]) && 0 < strlen($rAdminSettings["admin_password"])) {
                        $rPlayer = "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Play\" type=\"button\" class=\"btn btn-outline-info waves-effect waves-light btn-xs\" onClick=\"player(" . $rRow["id"] . ", '" . json_decode($rRow["target_container"], true)[0] . "');\"><i class=\"mdi mdi-play\"></i></button>";
                    } else {
                        $rPlayer = "<button type=\"button\" disabled class=\"btn btn-info waves-effect waves-light btn-xs\"><i class=\"mdi mdi-play\"></i></button>";
                    }
                } else {
                    $rPlayer = "<button type=\"button\" disabled class=\"btn btn-info waves-effect waves-light btn-xs\"><i class=\"mdi mdi-play\"></i></button>";
                }
                $rStreamInfoText = "<div style='font-size: 10px;' class='text-center' align='center'><tbody><tr><td colspan='3' class='col'>No information available</td></tr></tbody></div>";
                $rStreamInfo = json_decode($rRow["stream_info"], true);
                if ($rActualStatus == 1) {
                    if (!isset($rStreamInfo["codecs"]["video"])) {
                        $rStreamInfo["codecs"]["video"] = ["width" => "?", "height" => "?", "codec_name" => "N/A", "r_frame_rate" => "--"];
                    }
                    if (!isset($rStreamInfo["codecs"]["audio"])) {
                        $rStreamInfo["codecs"]["audio"] = ["codec_name" => "N/A"];
                    }
                    if (!isset($rStreamInfo["codecs"]["video"])) {
                        $rStreamInfo["codecs"]["video"] = ["duration" => "N/A"];
                    }
                    if ($rRow["bitrate"] == 0) {
                        $rRow["bitrate"] = "?";
                    }
                    $rStreamInfoText = "<div style='font-size: 13px;' class='text-center' align='center'>\r\n\t\t\t\t\t            <td class='col'>" . $rRow["bitrate"] . " Kbps " . $rStreamInfo["codecs"]["video"]["width"] . "x" . $rStreamInfo["codecs"]["video"]["height"] . "</td>\r\n\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t<td class='col'><i class='mdi mdi-video' data-name='mdi-video' style='color: #20a009;'></i> " . $rStreamInfo["codecs"]["video"]["codec_name"] . "</td>\r\n                                <td class='col'><i class='mdi mdi-volume-high' data-name='mdi-volume-high' style='color: #20a009;'></i> " . $rStreamInfo["codecs"]["audio"]["codec_name"] . "</td>\r\n\t\t\t\t\t\t\t\t<!--<td class='col'><i class='mdi mdi-clock' data-name='mdi-clock' style='color: #20a009;'></i>" . $rStreamInfo["duration"] . "</td>-->\r\n                    </div>";
                }
                if ($rStreamInfo["duration"]) {
                    $rCreatedMovie = "<span>" . date("d-m-Y", $rRow["added"]) . "</span>";
                } else {
                    $rCreatedMovie = "<span>" . date("d-m-Y", $rRow["added"]) . "</span>";
                }
                if ($rStreamInfo["duration"]) {
                    $rDurationdMovie = "<span><i class='mdi mdi-clock-outline' data-name='mdi-clock-outline' style='color: #20a009;'></i> " . $rStreamInfo["duration"] . "</span>";
                } else {
                    $rDurationdMovie = "<span->-</span>";
                }
                $rStreamInfo1 = json_decode($rRow["movie_propeties"], true);
                if ($rStreamInfo1["movie_image"]) {
                    $rIcon1 = "<center><img src='" . $rStreamInfo1["movie_image"] . "' height='90' width='60' /></center>";
                } else {
                    $rIcon1 = "";
                }
                if ($rPermissions["is_admin"]) {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./movie_info.php?id=" . $rRow["id"] . "\">" . $rIcon1, "<a href=\"./movie_info.php?id=" . $rRow["id"] . "\">" . $rStreamName, $rServerName, $rClients, $rVODStatusArray[$rActualStatus], $rButtons, $rPlayer, $rCreatedMovie, $rDurationdMovie, $rStreamInfoText];
                } else {
                    $rReturn["data"][] = [$rRow["id"], $rIcon1, $rStreamName, $rServerName, $rStreamInfoText];
                }
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "episode_list") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "import_episodes") && !hasPermissions("adv", "mass_delete")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`streams`.`id`", "`streams`.`stream_display_name`", "`series`.`title`", "`streams_sys`.`stream_status`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    $rWhere[] = "`streams`.`type` = 5";
    if (0 < strlen($_GET["series"])) {
        $rWhere[] = "`series_episodes`.`series_id` = " . intval($_GET["series"]);
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`streams`.`id` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `series`.`title` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["filter"])) {
        if ($_GET["filter"] == 1) {
            $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`pid` > 0 AND `streams_sys`.`to_analyze` = 0 AND `streams_sys`.`stream_status` <> 1)";
        } else {
            if ($_GET["filter"] == 2) {
                $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`pid` > 0 AND `streams_sys`.`to_analyze` = 1 AND `streams_sys`.`stream_status` <> 1)";
            } else {
                if ($_GET["filter"] == 3) {
                    $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`stream_status` = 1)";
                } else {
                    if ($_GET["filter"] == 4) {
                        $rWhere[] = "(`streams`.`direct_source` = 0 AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` <> 1)";
                    } else {
                        if ($_GET["filter"] == 5) {
                            $rWhere[] = "`streams`.`direct_source` = 1";
                        }
                    }
                }
            }
        }
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `series_episodes` ON `series_episodes`.`stream_id` = `streams`.`id` LEFT JOIN `series` ON `series`.`id` = `series_episodes`.`series_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `streams`.`id`, `streams`.`stream_display_name`, `series`.`title`, `streams`.`direct_source`, `streams_sys`.`to_analyze`, `streams_sys`.`pid` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `series_episodes` ON `series_episodes`.`stream_id` = `streams`.`id` LEFT JOIN `series` ON `series`.`id` = `series_episodes`.`series_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rActualStatus = 0;
                if (intval($rRow["direct_source"]) == 1) {
                    $rActualStatus = 3;
                } else {
                    if ($rRow["pid"]) {
                        if ($rRow["to_analyze"] == 1) {
                            $rActualStatus = 2;
                        } else {
                            if ($rRow["stream_status"] == 1) {
                                $rActualStatus = 4;
                            } else {
                                $rActualStatus = 1;
                            }
                        }
                    } else {
                        $rActualStatus = 0;
                    }
                }
                $rReturn["data"][] = [$rRow["id"], $rRow["stream_display_name"], $rRow["title"], $rVODStatusArray[$rActualStatus]];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "user_activity") {
    if ($rPermissions["is_reseller"] && !$rPermissions["reseller_client_connection_logs"]) {
        exit;
    }
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "connection_logs")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`user_activity`.`activity_id`", "`users`.`username`", "`mag_devices`.`mac`", "`streams`.`stream_display_name`", "`user_activity`.`container`", "`streaming_servers`.`server_name`", "`user_activity`.`isp`", "`user_activity`.`date_start`", "`user_activity`.`date_end`", "`user_activity`.`geoip_country_code`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if ($rPermissions["is_reseller"]) {
        $rWhere[] = "`users`.`member_id` IN (" . join(",", array_keys(getRegisteredUsers($rUserInfo["id"]))) . ")";
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`user_activity`.`user_agent` LIKE '%" . $rSearch . "%' OR `user_activity`.`user_agent` LIKE '%" . $rSearch . "%' OR `user_activity`.`user_ip` LIKE '%" . $rSearch . "%' OR `user_activity`.`container` LIKE '%" . $rSearch . "%' OR FROM_UNIXTIME(`user_activity`.`date_start`) LIKE '%" . $rSearch . "%' OR FROM_UNIXTIME(`user_activity`.`date_end`) LIKE '%" . $rSearch . "%' OR `user_activity`.`geoip_country_code` LIKE '%" . $rSearch . "%' OR `users`.`username` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `streaming_servers`.`server_name` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["range"])) {
        $rStartTime = substr($_GET["range"], 0, 10);
        $rEndTime = substr($_GET["range"], strlen($_GET["range"]) - 10, 10);
        if (!($rStartTime = strtotime($rStartTime . " 00:00:00"))) {
            $rStartTime = NULL;
        }
        if (!($rEndTime = strtotime($rEndTime . " 23:59:59"))) {
            $rEndTime = NULL;
        }
        if ($rStartTime && $rEndTime) {
            $rWhere[] = "(`user_activity`.`date_start` >= " . $rStartTime . " AND `user_activity`.`date_end` <= " . $rEndTime . ")";
        }
    }
    if (0 < strlen($_GET["server"])) {
        $rWhere[] = "`user_activity`.`server_id` = " . intval($_GET["server"]);
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `user_activity` LEFT JOIN `users` ON `user_activity`.`user_id` = `users`.`id` LEFT JOIN `streams` ON `user_activity`.`stream_id` = `streams`.`id` LEFT JOIN `streaming_servers` ON `user_activity`.`server_id` = `streaming_servers`.`id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT SUBSTR(FROM_BASE64(mac), 1, 18) mag, `users`.`is_restreamer`, SUBSTR(`user_activity`.`isp`, 1, 47) isp, `user_activity`.`activity_id`, `user_activity`.`user_id`, `user_activity`.`stream_id`, `user_activity`.`server_id`, SUBSTR(`user_activity`.`user_agent`, 1, 22) user_agent, `user_activity`.`user_ip`, `user_activity`.`date_start`, `user_activity`.`date_end`, `user_activity`.`container`, `user_activity`.`geoip_country_code`, SUBSTR(`users`.`username`, 1, 18) username, SUBSTR(`streams`.`stream_display_name`, 1, 25) stream_display_name, `streams`.`type`, SUBSTR(`streaming_servers`.`server_name`, 1, 18) server_name, (`user_activity`.`date_end` - `user_activity`.`date_start`) total_time FROM `user_activity`INNER JOIN `users` ON `user_activity`.`user_id` = `users`.`id`LEFT JOIN `mag_devices` ON `user_activity`.`user_id` = `mag_devices`.`user_id`LEFT JOIN `streams` ON `user_activity`.`stream_id` = `streams`.`id`LEFT JOIN `streaming_servers` ON `user_activity`.`server_id` = `streaming_servers`.`id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if ($rPermissions["is_admin"]) {
                    if (hasPermissions("adv", "edit_user")) {
                        $rUsername = "<a href='./user.php?id=" . $rRow["user_id"] . "'>" . $rRow["username"] . "</a>";
                    } else {
                        $rUsername = $rRow["username"];
                    }
                } else {
                    $rUsername = "<a href='./user_reseller.php?id=" . $rRow["user_id"] . "'>" . $rRow["username"] . "</a>";
                }
                if ($rPermissions["is_admin"]) {
                    if (hasPermissions("adv", "edit_user")) {
                        $rMac = "<a href='./user.php?id=" . $rRow["user_id"] . "'>" . $rRow["mag"] . "</a>";
                    } else {
                        $rMac = $rRow["mag"];
                    }
                } else {
                    $rMac = "<a href='./user_reseller.php?id=" . $rRow["user_id"] . "'>" . $rRow["mag"] . "</a>";
                }
                $rChannel = $rRow["stream_display_name"];
                if ($rPermissions["is_admin"]) {
                    $rServer = $rRow["server_name"];
                } else {
                    $rServer = "Server #" . $rRow["server_id"];
                }
                if ($rRow["user_ip"]) {
                    $rIP = "<img src='https://www.ip-tracker.org/images/ip-flags/" . strtolower($rRow["geoip_country_code"]) . ".png'></img>" . "<span style='color: #20a009;'</span> " . "<a target='_blank' href='https://www.ip-tracker.org/locator/ip-lookup.php?ip=" . $rRow["user_ip"] . "'>" . $rRow["user_ip"] . "</a>";
                } else {
                    $rIP = "";
                }
                if ($rRow["date_start"]) {
                    $rStart = date("Y-m-d H:i", $rRow["date_start"]);
                } else {
                    $rStart = "";
                }
                if ($rRow["date_end"]) {
                    $rStop = date("Y-m-d H:i", $rRow["date_end"]);
                } else {
                    $rStop = "";
                }
                $rReturn["data"][] = [$rRow["activity_id"], $rUsername, $rMac, $rChannel, $rRow["container"], $rServer, $rRow["isp"], $rStart, $rStop, $rIP];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "live_connections") {
    if ($rPermissions["is_reseller"] && !$rPermissions["reseller_client_connection_logs"]) {
        exit;
    }
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "live_connections")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`user_activity_now`.`activity_id`", "`user_activity_now`.`divergence`", "`users`.`username`", "`mag_devices`.`mac`", "`streams`.`stream_display_name`", "`user_activity_now`.`container`", "`streaming_servers`.`server_name`", "`user_activity_now`.`user_agent`", "`user_activity_now`.`date_start`", "`user_activity_now`.`geoip_country_code`", "`user_activity_now`.`isp`", false];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if ($rPermissions["is_reseller"]) {
        $rWhere[] = "`users`.`member_id` IN (" . join(",", array_keys(getRegisteredUsers($rUserInfo["id"]))) . ")";
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`user_activity_now`.`user_agent` LIKE '%" . $rSearch . "%' OR `user_activity_now`.`user_agent` LIKE '%" . $rSearch . "%' OR `user_activity_now`.`user_ip` LIKE '%" . $rSearch . "%' OR `user_activity_now`.`container` LIKE '%" . $rSearch . "%' OR FROM_UNIXTIME(`user_activity_now`.`date_start`) LIKE '%" . $rSearch . "%' OR `user_activity_now`.`geoip_country_code` LIKE '%" . $rSearch . "%' OR `users`.`username` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `streaming_servers`.`server_name` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["server_id"])) {
        $rWhere[] = "`user_activity_now`.`server_id` = " . intval($_GET["server_id"]);
    }
    if (0 < strlen($_GET["stream_id"])) {
        $rWhere[] = "`user_activity_now`.`stream_id` = " . intval($_GET["stream_id"]);
    }
    if (0 < strlen($_GET["user_id"])) {
        $rWhere[] = "`user_activity_now`.`user_id` = " . intval($_GET["user_id"]);
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `user_activity_now` LEFT JOIN `users` ON `user_activity_now`.`user_id` = `users`.`id` LEFT JOIN `streams` ON `user_activity_now`.`stream_id` = `streams`.`id` LEFT JOIN `streaming_servers` ON `user_activity_now`.`server_id` = `streaming_servers`.`id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT FROM_BASE64(mac) mag, SUBSTR(`user_activity_now`.`isp`, 1, 47) isp, `user_activity_now`.`activity_id`, `users`.`is_restreamer`, `user_activity_now`.`divergence`, `user_activity_now`.`user_id`, `user_activity_now`.`stream_id`, `user_activity_now`.`server_id`, SUBSTR(`user_activity_now`.`user_agent`, 1, 35) user_agent, `user_activity_now`.`user_ip`, `user_activity_now`.`container`, `user_activity_now`.`pid`, `user_activity_now`.`date_start`, `user_activity_now`.`geoip_country_code`, `users`.`username`, SUBSTR(`streams`.`stream_display_name`, 1, 30) stream_display_name, `streams`.`type`, SUBSTR(`streaming_servers`.`server_name`, 1, 25) server_name FROM `user_activity_now`INNER JOIN `users` ON `user_activity_now`.`user_id` = `users`.`id`LEFT JOIN `mag_devices` ON `user_activity_now`.`user_id` = `mag_devices`.`user_id`LEFT JOIN `streams` ON `user_activity_now`.`stream_id` = `streams`.`id`LEFT JOIN `streaming_servers` ON `user_activity_now`.`server_id` = `streaming_servers`.`id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if ($rRow["divergence"] <= 10) {
                    $rDivergence = "<i class=\"text-success fas fa-minus\" style=\"font-size: 2.5rem;\"></i>";
                } else {
                    if ($rRow["divergence"] <= 50) {
                        $rDivergence = "<i class=\"text-warning fas fa-minus\" style=\"font-size: 2.5rem;\"></i>";
                    } else {
                        $rDivergence = "<i class=\"text-danger fas fa-minus\" style=\"font-size: 2.5rem;\"></i>";
                    }
                }
                if ($rPermissions["is_admin"]) {
                    if (hasPermissions("adv", "edit_user")) {
                        $rUsername = "<a href='./user.php?id=" . $rRow["user_id"] . "'>" . $rRow["username"] . "</a>";
                    } else {
                        $rUsername = $rRow["username"];
                    }
                } else {
                    $rUsername = "<a href='./user_reseller.php?id=" . $rRow["user_id"] . "'>" . $rRow["username"] . "</a>";
                }
                if ($rPermissions["is_admin"]) {
                    if (hasPermissions("adv", "edit_user")) {
                        $rMac = "<a href='./user.php?id=" . $rRow["user_id"] . "'>" . $rRow["mag"] . "</a>";
                    } else {
                        $rMac = $rRow["mag"];
                    }
                } else {
                    $rMac = "<a href='./user_reseller.php?id=" . $rRow["user_id"] . "'>" . $rRow["mag"] . "</a>";
                }
                $rChannel = $rRow["stream_display_name"];
                if ($rPermissions["is_admin"]) {
                    $rServer = $rRow["server_name"];
                } else {
                    $rServer = "Server #" . $rRow["server_id"];
                }
                if ($rRow["user_ip"]) {
                    $rIP = "<img src='https://www.ip-tracker.org/images/ip-flags/" . strtolower($rRow["geoip_country_code"]) . ".png'></img>" . "<span style='color: #20a009;'</span> " . "<a target='_blank' href='https://www.ip-tracker.org/locator/ip-lookup.php?ip=" . $rRow["user_ip"] . "'>" . $rRow["user_ip"] . "</a>";
                } else {
                    $rIP = "";
                }
                if ($rRow["is_restreamer"]) {
                    $rRestreamer = "<i class='text-info mdi mdi-information' data-toggle='tooltip' data-placement='top' title='' data-original-title='Is Restream'></i> ";
                } else {
                    $rRestreamer = "";
                }
                if ($rRow["date_start"]) {
                    $rTime = intval(time()) - intval($rRow["date_start"]);
                    $rTime = sprintf("%02d:%02d:%02d", $rTime / 3600, $rTime / 60 % 60, $rTime % 60);
                } else {
                    $rTime = "";
                }
                if ($rRow["isp"]) {
                    $rnisp2 = "<span>" . $rRow["isp"] . "</span>";
                } else {
                    $rnisp2 = "no isp";
                }
                if (isset($_GET["fingerprint"])) {
                    $rButtons = "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Kill Connection\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["pid"] . ", 'kill', " . $rRow["activity_id"] . ");\"><i class=\"fas fa-hammer\"></i></button>";
                } else {
                    $rButtons = "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Kill Connection\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["pid"] . ", 'kill');\"><i class=\"fas fa-hammer\"></i></button>";
                }
                $rReturn["data"][] = [$rRow["activity_id"], $rDivergence, $rRestreamer . $rUsername, $rMac, $rChannel, $rRow["container"], $rServer, $rRow["user_agent"], $rTime, $rIP, $rnisp2, $rButtons];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "stream_list") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "import_streams") && !hasPermissions("adv", "mass_delete")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`streams`.`id`", "`streams`.`stream_display_name`", "`stream_categories`.`category_name`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if (isset($_GET["include_channels"])) {
        $rWhere[] = "`streams`.`type` IN (1,3)";
    } else {
        $rWhere[] = "`streams`.`type` = 1";
    }
    if (0 < strlen($_GET["category"])) {
        $rWhere[] = "`streams`.`category_id` = " . intval($_GET["category"]);
    }
    if (0 < strlen($_GET["filter"])) {
        if ($_GET["filter"] == 1) {
            $rWhere[] = "(`streams_sys`.`monitor_pid` > 0 AND `streams_sys`.`pid` > 0)";
        } else {
            if ($_GET["filter"] == 2) {
                $rWhere[] = "((`streams_sys`.`monitor_pid` IS NOT NULL AND `streams_sys`.`monitor_pid` > 0) AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` <> 0)";
            } else {
                if ($_GET["filter"] == 3) {
                    $rWhere[] = "(`streams`.`direct_source` = 0 AND (`streams_sys`.`monitor_pid` IS NULL OR `streams_sys`.`monitor_pid` <= 0) AND `streams_sys`.`on_demand` = 0)";
                } else {
                    if ($_GET["filter"] == 4) {
                        $rWhere[] = "((`streams_sys`.`monitor_pid` IS NOT NULL AND `streams_sys`.`monitor_pid` > 0) AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` = 0)";
                    } else {
                        if ($_GET["filter"] == 5) {
                            $rWhere[] = "`streams_sys`.`on_demand` = 1";
                        } else {
                            if ($_GET["filter"] == 6) {
                                $rWhere[] = "`streams`.`direct_source` = 1";
                            }
                        }
                    }
                }
            }
        }
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`streams`.`id` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%')";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id`  " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `streams`.`id`, `streams`.`stream_display_name`, `stream_categories`.`category_name` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rReturn["data"][] = [$rRow["id"], $rRow["stream_display_name"], $rRow["category_name"], $rStatus];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "movie_list") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "import_movies") && !hasPermissions("adv", "mass_delete")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`streams`.`id`", "`streams`.`stream_display_name`", "`stream_categories`.`category_name`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    $rWhere[] = "`streams`.`type` = 2";
    if (0 < strlen($_GET["category"])) {
        $rWhere[] = "`streams`.`category_id` = " . intval($_GET["category"]);
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`streams`.`id` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["filter"])) {
        if ($_GET["filter"] == 1) {
            $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`pid` > 0 AND `streams_sys`.`to_analyze` = 0 AND `streams_sys`.`stream_status` <> 1)";
        } else {
            if ($_GET["filter"] == 2) {
                $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`pid` > 0 AND `streams_sys`.`to_analyze` = 1 AND `streams_sys`.`stream_status` <> 1)";
            } else {
                if ($_GET["filter"] == 3) {
                    $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`stream_status` = 1)";
                } else {
                    if ($_GET["filter"] == 4) {
                        $rWhere[] = "(`streams`.`direct_source` = 0 AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` <> 1)";
                    } else {
                        if ($_GET["filter"] == 5) {
                            $rWhere[] = "`streams`.`direct_source` = 1";
                        } else {
                            if ($_GET["filter"] == 6) {
                                $rWhere[] = "(`streams`.`movie_propeties` IS NULL OR `streams`.`movie_propeties` = '' OR `streams`.`movie_propeties` = '[]' OR `streams`.`movie_propeties` = '{}' OR `streams`.`movie_propeties` LIKE '%tmdb_id\":\"\"%')";
                            }
                        }
                    }
                }
            }
        }
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `streams`.`id`, `streams`.`stream_display_name`, `stream_categories`.`category_name`, `streams`.`direct_source`, `streams_sys`.`to_analyze`, `streams_sys`.`pid` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rActualStatus = 0;
                if (intval($rRow["direct_source"]) == 1) {
                    $rActualStatus = 3;
                } else {
                    if ($rRow["pid"]) {
                        if ($rRow["to_analyze"] == 1) {
                            $rActualStatus = 2;
                        } else {
                            if ($rRow["stream_status"] == 1) {
                                $rActualStatus = 4;
                            } else {
                                $rActualStatus = 1;
                            }
                        }
                    } else {
                        $rActualStatus = 0;
                    }
                }
                $rReturn["data"][] = [$rRow["id"], $rRow["stream_display_name"], $rRow["category_name"], $rVODStatusArray[$rActualStatus]];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "radio_list") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "mass_delete")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`streams`.`id`", "`streams`.`stream_display_name`", "`stream_categories`.`category_name`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    $rWhere[] = "`streams`.`type` = 4";
    if (0 < strlen($_GET["category"])) {
        $rWhere[] = "`streams`.`category_id` = " . intval($_GET["category"]);
    }
    if (0 < strlen($_GET["filter"])) {
        if ($_GET["filter"] == 1) {
            $rWhere[] = "(`streams_sys`.`monitor_pid` > 0 AND `streams_sys`.`pid` > 0)";
        } else {
            if ($_GET["filter"] == 2) {
                $rWhere[] = "((`streams_sys`.`monitor_pid` IS NOT NULL AND `streams_sys`.`monitor_pid` > 0) AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` <> 0)";
            } else {
                if ($_GET["filter"] == 3) {
                    $rWhere[] = "(`streams`.`direct_source` = 0 AND (`streams_sys`.`monitor_pid` IS NULL OR `streams_sys`.`monitor_pid` <= 0) AND `streams_sys`.`on_demand` = 0)";
                } else {
                    if ($_GET["filter"] == 4) {
                        $rWhere[] = "((`streams_sys`.`monitor_pid` IS NOT NULL AND `streams_sys`.`monitor_pid` > 0) AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` = 0)";
                    } else {
                        if ($_GET["filter"] == 5) {
                            $rWhere[] = "`streams_sys`.`on_demand` = 1";
                        } else {
                            if ($_GET["filter"] == 6) {
                                $rWhere[] = "`streams`.`direct_source` = 1";
                            }
                        }
                    }
                }
            }
        }
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`streams`.`id` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%')";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id`  " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `streams`.`id`, `streams`.`stream_display_name`, `stream_categories`.`category_name` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rReturn["data"][] = [$rRow["id"], $rRow["stream_display_name"], $rRow["category_name"], $rStatus];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "series_list") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "mass_delete")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`series`.`id`", "`series`.`title`", "`stream_categories`.`category_name`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if (0 < strlen($_GET["category"])) {
        if ($_GET["category"] == -1) {
            $rWhere[] = "(`series`.`tmdb_id` = 0 OR `series`.`tmdb_id` IS NULL)";
        } else {
            $rWhere[] = "`series`.`category_id` = " . intval($_GET["category"]);
        }
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`series`.`id` LIKE '%" . $rSearch . "%' OR `series`.`title` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%')";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `series` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `series`.`category_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `series`.`id`, `series`.`title`, `stream_categories`.`category_name` FROM `series` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `series`.`category_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rReturn["data"][] = [$rRow["id"], $rRow["title"], $rRow["category_name"]];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "credits_log") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "credits_log")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`credits_log`.`id`", "`owner_username`", "`target_username`", "`credits_log`.`amount`", "`credits_log`.`reason`", "`date`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`target`.`username` LIKE '%" . $rSearch . "%' OR `owner`.`username` LIKE '%" . $rSearch . "%' OR FROM_UNIXTIME(`date`) LIKE '%" . $rSearch . "%' OR `credits_log`.`amount` LIKE '%" . $rSearch . "%' OR `credits_log`.`reason` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["range"])) {
        $rStartTime = substr($_GET["range"], 0, 10);
        $rEndTime = substr($_GET["range"], strlen($_GET["range"]) - 10, 10);
        if (!($rStartTime = strtotime($rStartTime . " 00:00:00"))) {
            $rStartTime = NULL;
        }
        if (!($rEndTime = strtotime($rEndTime . " 23:59:59"))) {
            $rEndTime = NULL;
        }
        if ($rStartTime && $rEndTime) {
            $rWhere[] = "(`credits_log`.`date` >= " . $rStartTime . " AND `credits_log`.`date` <= " . $rEndTime . ")";
        }
    }
    if (0 < strlen($_GET["reseller"])) {
        $rWhere[] = "(`credits_log`.`target_id` = " . intval($_GET["reseller"]) . " OR `credits_log`.`admin_id` = " . intval($_GET["reseller"]) . ")";
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `credits_log` LEFT JOIN `reg_users` AS `target` ON `target`.`id` = `credits_log`.`target_id` LEFT JOIN `reg_users` AS `owner` ON `owner`.`id` = `credits_log`.`admin_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `credits_log`.`id`, `credits_log`.`target_id`, `credits_log`.`admin_id`, `target`.`username` AS `target_username`, `owner`.`username` AS `owner_username`, `amount`, FROM_UNIXTIME(`date`) AS `date`, `credits_log`.`reason` FROM `credits_log` LEFT JOIN `reg_users` AS `target` ON `target`.`id` = `credits_log`.`target_id` LEFT JOIN `reg_users` AS `owner` ON `owner`.`id` = `credits_log`.`admin_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if (hasPermissions("adv", "edit_reguser")) {
                    $rOwner = "<a href='./reg_user.php?id=" . $rRow["admin_id"] . "'>" . $rRow["owner_username"] . "</a>";
                    $rTarget = "<a href='./reg_user.php?id=" . $rRow["target_id"] . "'>" . $rRow["target_username"] . "</a>";
                } else {
                    $rOwner = $rRow["owner_username"];
                    $rTarget = $rRow["target_username"];
                }
                $rReturn["data"][] = [$rRow["id"], $rOwner, $rTarget, $rRow["amount"], $rRow["reason"], $rRow["date"]];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "user_ips") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "connection_logs")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`user_activity`.`user_id`", "`users`.`username`", "`ip_count`", false];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = ["`date_start` >= (UNIX_TIMESTAMP()-" . intval($_GET["range"]) . ")"];
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`users`.`username` LIKE '%" . $rSearch . "%' OR `user_activity`.`user_id` LIKE '%" . $rSearch . "%' OR `user_activity`.`user_ip` LIKE '%" . $rSearch . "%')";
    }
    $rWhereString = "WHERE " . join(" AND ", $rWhere);
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(DISTINCT(`user_activity`.`user_id`)) AS `count` FROM `user_activity` LEFT JOIN `users` ON `users`.`id` = `user_activity`.`user_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `user_activity`.`user_id`, COUNT(DISTINCT(`user_activity`.`user_ip`)) AS `ip_count`, `users`.`username` FROM `user_activity` LEFT JOIN `users` ON `users`.`id` = `user_activity`.`user_id` " . $rWhereString . " GROUP BY `user_activity`.`user_id` " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rDates = date("Y-m-d H:i", time() - intval($_GET["range"])) . " - " . date("Y-m-d H:i", time());
                $rButtons = "<a href=\"./user_activity.php?search=" . $rRow["username"] . "&dates=" . $rDates . "\"><button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\">View Logs</button></a>";
                $rReturn["data"][] = ["<a href='./user.php?id=" . $rRow["user_id"] . "'>" . $rRow["user_id"] . "</a>", $rRow["username"], $rRow["ip_count"], $rButtons];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "client_logs") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "client_request_log")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`client_logs`.`id`", "`users`.`username`", "`streams`.`stream_display_name`", "`client_logs`,`client_status`", "`client_logs`.`extra_data`", "`client_logs`.`ip`", "`client_logs`.`date`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`client_logs`.`client_status` LIKE '%" . $rSearch . "%' OR `client_logs`.`query_string` LIKE '%" . $rSearch . "%' OR FROM_UNIXTIME(`date`) LIKE '%" . $rSearch . "%' OR `client_logs`.`extra_data` LIKE '%" . $rSearch . "%' OR `client_logs`.`ip` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `users`.`username` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["range"])) {
        $rStartTime = substr($_GET["range"], 0, 10);
        $rEndTime = substr($_GET["range"], strlen($_GET["range"]) - 10, 10);
        if (!($rStartTime = strtotime($rStartTime . " 00:00:00"))) {
            $rStartTime = NULL;
        }
        if (!($rEndTime = strtotime($rEndTime . " 23:59:59"))) {
            $rEndTime = NULL;
        }
        if ($rStartTime && $rEndTime) {
            $rWhere[] = "(`client_logs`.`date` >= " . $rStartTime . " AND `client_logs`.`date` <= " . $rEndTime . ")";
        }
    }
    if (0 < strlen($_GET["filter"])) {
        $rWhere[] = "`client_logs`.`client_status` = '" . $_GET["filter"] . "'";
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `client_logs` LEFT JOIN `streams` ON `streams`.`id` = `client_logs`.`stream_id` LEFT JOIN `users` ON `users`.`id` = `client_logs`.`user_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `client_logs`.`id`, `client_logs`.`user_id`, `client_logs`.`stream_id`, `streams`.`stream_display_name`, `users`.`username`, `client_logs`.`client_status`, `client_logs`.`query_string`, `client_logs`.`extra_data`, `client_logs`.`ip`, FROM_UNIXTIME(`client_logs`.`date`) AS `date` FROM `client_logs` LEFT JOIN `streams` ON `streams`.`id` = `client_logs`.`stream_id` LEFT JOIN `users` ON `users`.`id` = `client_logs`.`user_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if (hasPermissions("adv", "edit_user")) {
                    $rUsername = "<a href='./user.php?id=" . $rRow["user_id"] . "'>" . $rRow["username"] . "</a>";
                } else {
                    $rUsername = $rRow["username"];
                }
                $rReturn["data"][] = [$rRow["id"], $rUsername, $rRow["stream_display_name"], $rRow["client_status"], $rRow["extra_data"], "<a target='_blank' href='https://www.ip-tracker.org/locator/ip-lookup.php?ip=" . $rRow["ip"] . "'>" . $rRow["ip"] . "</a>", $rRow["date"]];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "reg_user_logs") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "reg_userlog")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`reg_userlog`.`id`", "`reg_users`.`username`", "`reg_userlog`.`username`", "`reg_userlog`.`type`", "`reg_userlog`.`date`", false];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`reg_userlog`.`username` LIKE '%" . $rSearch . "%' OR `reg_userlog`.`type` LIKE '%" . $rSearch . "%' OR FROM_UNIXTIME(`date`) LIKE '%" . $rSearch . "%' OR `reg_users`.`username` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["range"])) {
        $rStartTime = substr($_GET["range"], 0, 10);
        $rEndTime = substr($_GET["range"], strlen($_GET["range"]) - 10, 10);
        if (!($rStartTime = strtotime($rStartTime . " 00:00:00"))) {
            $rStartTime = NULL;
        }
        if (!($rEndTime = strtotime($rEndTime . " 23:59:59"))) {
            $rEndTime = NULL;
        }
        if ($rStartTime && $rEndTime) {
            $rWhere[] = "(`reg_userlog`.`date` >= " . $rStartTime . " AND `reg_userlog`.`date` <= " . $rEndTime . ")";
        }
    }
    if (0 < strlen($_GET["reseller"])) {
        $rWhere[] = "`reg_userlog`.`owner` = '" . intval($_GET["reseller"]) . "'";
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `reg_userlog` LEFT JOIN `reg_users` ON `reg_users`.`id` = `reg_userlog`.`owner` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `reg_userlog`.`id`, `reg_userlog`.`owner` as `owner_id`, `reg_users`.`username` AS `owner`, `reg_userlog`.`username`, `reg_userlog`.`type`, FROM_UNIXTIME(`reg_userlog`.`date`) AS `date` FROM `reg_userlog` LEFT JOIN `reg_users` ON `reg_users`.`id` = `reg_userlog`.`owner` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if (hasPermissions("adv", "edit_reguser")) {
                    $rOwner = "<a href='./reg_user.php?id=" . $rRow["owner_id"] . "'>" . $rRow["owner"] . "</a>";
                } else {
                    $rOwner = $rRow["owner"];
                }
                $rButtons = "<div class=\"btn-group\">";
                $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                $rButtons .= "</div>";
                $rReturn["data"][] = [$rRow["id"], $rOwner, $rRow["username"], strip_tags($rRow["type"]), $rRow["date"], $rButtons];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "login_user_logs") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "reg_userlog")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`login_users`.`id`", "`reg_users`.`username`", "`login_users`.`type`", "`login_users`.`login_ip`", "`login_users`.`date`", false];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`login_users`.`login_ip` LIKE '%" . $rSearch . "%' OR `login_users`.`type` LIKE '%" . $rSearch . "%' OR FROM_UNIXTIME(`date`) LIKE '%" . $rSearch . "%' OR `reg_users`.`username` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["range"])) {
        $rStartTime = substr($_GET["range"], 0, 10);
        $rEndTime = substr($_GET["range"], strlen($_GET["range"]) - 10, 10);
        if (!($rStartTime = strtotime($rStartTime . " 00:00:00"))) {
            $rStartTime = NULL;
        }
        if (!($rEndTime = strtotime($rEndTime . " 23:59:59"))) {
            $rEndTime = NULL;
        }
        if ($rStartTime && $rEndTime) {
            $rWhere[] = "(`login_users`.`date` >= " . $rStartTime . " AND `login_users`.`date` <= " . $rEndTime . ")";
        }
    }
    if (0 < strlen($_GET["reseller"])) {
        $rWhere[] = "`login_users`.`owner` = '" . intval($_GET["reseller"]) . "'";
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `login_users` LEFT JOIN `reg_users` ON `reg_users`.`id` = `login_users`.`owner` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `login_users`.`id`, `login_users`.`owner` as `owner_id`, `reg_users`.`username` AS `owner`, `login_users`.`login_ip`, `login_users`.`type`, FROM_UNIXTIME(`login_users`.`date`) AS `date` FROM `login_users` LEFT JOIN `reg_users` ON `reg_users`.`id` = `login_users`.`owner` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if (hasPermissions("adv", "edit_reguser")) {
                    $rOwner = "<a href='./reg_user.php?id=" . $rRow["owner_id"] . "'>" . $rRow["owner"] . "</a>";
                } else {
                    $rOwner = $rRow["owner"];
                }
                if (hasPermissions("adv", "edit_reguser")) {
                    $rLoginIP = "<a target='_blank' href='https://www.ip-tracker.org/locator/ip-lookup.php?ip=" . strip_tags($rRow["login_ip"]) . "'>" . strip_tags($rRow["login_ip"]) . "</a>";
                } else {
                    $rLoginIP = strip_tags($rRow["login_ip"]);
                }
                $rButtons = "<div class=\"btn-group\">";
                $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                $rButtons .= "</div>";
                $rReturn["data"][] = [$rRow["id"], $rOwner, strip_tags($rRow["type"]), $rLoginIP, $rRow["date"], $rButtons];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "stream_logs") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "stream_errors")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`stream_logs`.`id`", "`streams`.`stream_display_name`", "`streaming_servers`.`server_name`", "`stream_logs`.`error`", "`stream_logs`.`date`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `streaming_servers`.`server_name` LIKE '%" . $rSearch . "%' OR FROM_UNIXTIME(`date`) LIKE '%" . $rSearch . "%' OR `stream_logs`.`error` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["range"])) {
        $rStartTime = substr($_GET["range"], 0, 10);
        $rEndTime = substr($_GET["range"], strlen($_GET["range"]) - 10, 10);
        if (!($rStartTime = strtotime($rStartTime . " 00:00:00"))) {
            $rStartTime = NULL;
        }
        if (!($rEndTime = strtotime($rEndTime . " 23:59:59"))) {
            $rEndTime = NULL;
        }
        if ($rStartTime && $rEndTime) {
            $rWhere[] = "(`stream_logs`.`date` >= " . $rStartTime . " AND `stream_logs`.`date` <= " . $rEndTime . ")";
        }
    }
    if (0 < strlen($_GET["server"])) {
        $rWhere[] = "`stream_logs`.`server_id` = '" . intval($_GET["server"]) . "'";
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `stream_logs` LEFT JOIN `streams` ON `streams`.`id` = `stream_logs`.`stream_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `stream_logs`.`server_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `stream_logs`.`id`, `stream_logs`.`stream_id`, `stream_logs`.`server_id`, `streams`.`stream_display_name`, `streaming_servers`.`server_name`, `stream_logs`.`error`, FROM_UNIXTIME(`stream_logs`.`date`) AS `date` FROM `stream_logs` LEFT JOIN `streams` ON `streams`.`id` = `stream_logs`.`stream_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `stream_logs`.`server_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rReturn["data"][] = [$rRow["id"], $rRow["stream_display_name"], $rRow["server_name"], $rRow["error"], $rRow["date"]];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "stream_unique") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "fingerprint")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`streams`.`id`", "`streams`.`stream_display_name`", "`stream_categories`.`category_name`", "`active_count`", NULL];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    $rWhere[] = "`streams`.`type` = 1";
    $rWhere[] = "(SELECT COUNT(*) FROM `user_activity_now` WHERE `container` = 'ts' AND `user_activity_now`.`stream_id` = `streams`.`id`) > 0";
    if (0 < strlen($_GET["category"])) {
        $rWhere[] = "`streams`.`category_id` = " . intval($_GET["category"]);
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`streams`.`id` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%')";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `streams`.`id`, `streams`.`stream_display_name`, `stream_categories`.`category_name`, (SELECT COUNT(*) FROM `user_activity_now` WHERE `container` = 'ts' AND `user_activity_now`.`stream_id` = `streams`.`id`) AS `active_count` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rReturn["data"][] = [$rRow["id"], $rRow["stream_display_name"], $rRow["category_name"], $rRow["active_count"], "<button type='button' class='btn waves-effect waves-light btn-xs' href='javascript:void(0);' onClick='selectFingerprint(" . $rRow["id"] . ")'><i class='mdi mdi-fingerprint'></i></button>"];
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "reg_users") {
    if ($rPermissions["is_reseller"] && !$rPermissions["create_sub_resellers"]) {
        exit;
    }
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "mng_regusers")) {
        exit;
    }
    $rAvailableMembers = array_keys(getRegisteredUsers($rUserInfo["id"]));
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`reg_users`.`id`", "`reg_users`.`username`", "`r`.`username`", "`reg_users`.`ip`", "`member_groups`.`group_name`", "`reg_users`.`status`", "`reg_users`.`credits`", "`user_count`", "`reg_users`.`reseller_dns`", "`reg_users`.`last_login`", false];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if ($rPermissions["is_reseller"]) {
        $rWhere[] = "`reg_users`.`owner_id` IN (" . join(",", $rAvailableMembers) . ")";
    }
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`reg_users`.`id` LIKE '%" . $rSearch . "%' OR `reg_users`.`username` LIKE '%" . $rSearch . "%' OR `reg_users`.`notes` LIKE '%" . $rSearch . "%' OR `r`.`username` LIKE '%" . $rSearch . "%' OR from_unixtime(`reg_users`.`date_registered`) LIKE '%" . $rSearch . "%' OR from_unixtime(`reg_users`.`last_login`) LIKE '%" . $rSearch . "%' OR `reg_users`.`email` LIKE '%" . $rSearch . "%' OR `reg_users`.`ip` LIKE '%" . $rSearch . "%' OR `reg_users`.`reseller_dns` LIKE '%" . $rSearch . "%' OR `member_groups`.`group_name` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["filter"])) {
        if ($_GET["filter"] == 1) {
            $rWhere[] = "`reg_users`.`status` = 1";
        } else {
            if ($_GET["filter"] == 2) {
                $rWhere[] = "`reg_users`.`status` = 0";
            }
        }
    }
    if (0 < strlen($_GET["reseller"])) {
        $rWhere[] = "`reg_users`.`owner_id` = " . intval($_GET["reseller"]);
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `reg_users` LEFT JOIN `member_groups` ON `member_groups`.`group_id` = `reg_users`.`member_group_id` LEFT JOIN `reg_users` AS `r` on `r`.`id` = `reg_users`.`owner_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `reg_users`.`id`, `reg_users`.`status`, `reg_users`.`notes`, `reg_users`.`credits`, `reg_users`.`username`, `reg_users`.`email`, `reg_users`.`ip`, `reg_users`.`reseller_dns`, FROM_UNIXTIME(`reg_users`.`date_registered`) AS `date_registered`, FROM_UNIXTIME(`reg_users`.`last_login`) AS `last_login`, `r`.`username` as `owner_username`, `member_groups`.`group_name`, `reg_users`.`verified`, `reg_users`.`status`, (SELECT COUNT(`id`) FROM `users` WHERE `member_id` = `reg_users`.`id`) AS `user_count` FROM `reg_users` LEFT JOIN `member_groups` ON `member_groups`.`group_id` = `reg_users`.`member_group_id` LEFT JOIN `reg_users` AS `r` on `r`.`id` = `reg_users`.`owner_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                if ($rRow["status"] == 1) {
                    $rStatus = "<i class=\"text-success fas fa-square\"></i>";
                } else {
                    $rStatus = "<i class=\"text-danger fas fa-square\"></i>";
                }
                if (!$rRow["last_login"]) {
                    $rRow["last_login"] = "NEVER";
                }
                $rButtons = "<div class=\"btn-group\">";
                if (0 < strlen($rRow["notes"])) {
                    $rButtons .= "<button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"\" data-original-title=\"" . $rRow["notes"] . "\"><i class=\"mdi mdi-note\"></i></button>";
                } else {
                    $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-note\"></i></button>";
                }
                if ($rPermissions["is_admin"]) {
                    if (hasPermissions("adv", "edit_reguser")) {
                        $rButtons .= "<a href=\"./reg_user.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-account-edit\"></i></button></a>\r\n\t\t\t\t\t\t";
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Reset Two Factor Auth\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'reset');\"><i class=\"mdi mdi-two-factor-authentication\"></i></button>\r\n\t\t\t\t\t\t";
                    }
                } else {
                    $rButtons .= "<a href=\"./credits_add.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Add Credits\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-currency-usd\"></i></button></a>";
                    $rButtons .= "<a href=\"./subreseller.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-account-edit\"></i></button></a>";
                }
                if ($rPermissions["is_reseller"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_reguser")) {
                    if ($rRow["status"] == 1) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Disable\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'disable');\"><i class=\"mdi mdi-lock-outline\"></i></button>\r\n\t\t\t\t\t\t";
                    } else {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Enable\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'enable');\"><i class=\"mdi mdi-lock\"></i></button>\r\n\t\t\t\t\t\t";
                    }
                }
                if ($rPermissions["is_reseller"] && $rPermissions["delete_users"] || $rPermissions["is_admin"] && hasPermissions("adv", "edit_reguser")) {
                    $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                }
                $rButtons .= "</div>";
                if ($rPermissions["is_admin"]) {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./reg_user.php?id=" . $rRow["id"] . "\">" . $rRow["username"], $rRow["owner_username"], $rRow["ip"], "<button type='button' class='btn btn-dark btn-xs'</button>" . $rRow["group_name"], $rStatus, "<button type='button' class='btn btn-secondary btn-xs waves-effect waves-light'</button>" . $rRow["credits"], "<button type='button' class='btn btn-secondary btn-xs waves-effect waves-light'</button>" . $rRow["user_count"], $rRow["reseller_dns"], $rRow["last_login"], $rButtons];
                } else {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./subreseller.php?id=" . $rRow["id"] . "\">" . $rRow["username"], $rRow["owner_username"], $rRow["ip"], "<button type='button' class='btn btn-dark btn-xs'</button>" . $rRow["group_name"], $rStatus, "<button type='button' class='btn btn-secondary btn-xs waves-effect waves-light'</button>" . $rRow["credits"], "<button type='button' class='btn btn-secondary btn-xs waves-effect waves-light'</button>" . $rRow["user_count"], $rRow["reseller_dns"], $rRow["last_login"], $rButtons];
                }
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "series") {
    if ($rPermissions["is_reseller"] && !$rPermissions["reset_stb_data"]) {
        exit;
    }
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "series") && !hasPermissions("adv", "mass_sedits")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`series`.`id`", false, "`series`.`title`", "`stream_categories`.`category_name`", "`latest_season`", "`episode_count`", "`series`.`releaseDate`", "`series`.`last_modified`", false];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    if (0 < strlen($_GET["search"]["value"])) {
        $rSearch = $_GET["search"]["value"];
        $rWhere[] = "(`series`.`id` LIKE '%" . $rSearch . "%' OR `series`.`title` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%' OR `series`.`releaseDate` LIKE '%" . $rSearch . "%')";
    }
    if (0 < strlen($_GET["category"])) {
        if ($_GET["category"] == -1) {
            $rWhere[] = "(`series`.`tmdb_id` = 0 OR `series`.`tmdb_id` IS NULL)";
        } else {
            $rWhere[] = "`series`.`category_id` = " . intval($_GET["category"]);
        }
    }
    if ($rOrder[$rOrderRow]) {
        $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
        $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection . ", `series`.`id` ASC";
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `series` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `series`.`category_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `series`.`id`, `series`.`title`, `series`.`cover`, `stream_categories`.`category_name`, `series`.`releaseDate`, `series`.`last_modified`, (SELECT MAX(`season_num`) FROM `series_episodes` WHERE `series_id` = `series`.`id`) AS `latest_season`, (SELECT COUNT(*) FROM `series_episodes` WHERE `series_id` = `series`.`id`) AS `episode_count` FROM `series` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `series`.`category_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rButtons = "<div class=\"btn-group\">";
                if (hasPermissions("adv", "add_episode")) {
                    $rButtons .= "<a href=\"./episode.php?sid=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Add Episode(s)\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-plus-circle-outline\"></i></button></a>\r\n\t\t\t\t\t";
                }
                if (hasPermissions("adv", "episodes")) {
                    $rButtons .= "<a href=\"./episodes.php?series=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"View Episodes\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-eye\"></i></button></a>\r\n\t\t\t\t\t";
                }
                if (hasPermissions("adv", "edit_series")) {
                    $rButtons .= "<a href=\"./series_order.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Reorder Episodes\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-format-line-spacing\"></i></button></a>\r\n\t\t\t\t\t<a href=\"./serie.php?id=" . $rRow["id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                }
                $rButtons .= "</div>";
                if (!$rRow["latest_season"]) {
                    $rRow["latest_season"] = 0;
                }
                if ($rRow["last_modified"] == 0) {
                    $rRow["last_modified"] = "Never";
                } else {
                    $rRow["last_modified"] = date("Y-m-d H:i", $rRow["last_modified"]);
                }
                if ($rRow["cover"]) {
                    $rIcon1 = "<center><img src='" . $rRow["cover"] . "' height='90' width='60' /></center>";
                } else {
                    $rIcon1 = "";
                }
                if ($rPermissions["is_admin"]) {
                    $rReturn["data"][] = [$rRow["id"], $rIcon1, $rRow["title"], $rRow["category_name"], $rRow["latest_season"], $rRow["episode_count"], $rRow["releaseDate"], $rRow["last_modified"], $rButtons];
                } else {
                    $rReturn["data"][] = [$rRow["id"], $rIcon1, $rRow["title"], $rRow["category_name"], $rRow["latest_season"], $rRow["episode_count"], $rRow["releaseDate"]];
                }
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "episodes") {
    if ($rPermissions["is_reseller"] && !$rPermissions["reset_stb_data"]) {
        exit;
    }
    if ($rPermissions["is_admin"] && !hasPermissions("adv", "episodes") && !hasPermissions("adv", "mass_sedits")) {
        exit;
    }
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
    $rOrder = ["`streams`.`id`", false, "`streams`.`stream_display_name`", "`streaming_servers`.`server_name`", "`clients`", "`streams_sys`.`stream_status`", false, false, false, false, "`streams_sys`.`bitrate`"];
    if (0 < strlen($_GET["order"][0]["column"])) {
        $rOrderRow = intval($_GET["order"][0]["column"]);
    } else {
        $rOrderRow = 0;
    }
    $rWhere = [];
    $rWhere[] = "`streams`.`type` = 5";
    if (isset($_GET["stream_id"])) {
        $rWhere[] = "`streams`.`id` = " . intval($_GET["stream_id"]);
        $rOrderBy = "ORDER BY `streams_sys`.`server_stream_id` ASC";
    } else {
        if (0 < strlen($_GET["search"]["value"])) {
            $rSearch = $_GET["search"]["value"];
            $rWhere[] = "(`streams`.`id` LIKE '%" . $rSearch . "%' OR `streams`.`stream_display_name` LIKE '%" . $rSearch . "%' OR `series`.`title` LIKE '%" . $rSearch . "%' OR `streams`.`notes` LIKE '%" . $rSearch . "%' OR `streams_sys`.`current_source` LIKE '%" . $rSearch . "%' OR `stream_categories`.`category_name` LIKE '%" . $rSearch . "%' OR `streaming_servers`.`server_name` LIKE '%" . $rSearch . "%')";
        }
        if (0 < strlen($_GET["filter"])) {
            if ($_GET["filter"] == 1) {
                $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`pid` > 0 AND `streams_sys`.`to_analyze` = 0 AND `streams_sys`.`stream_status` <> 1)";
            } else {
                if ($_GET["filter"] == 2) {
                    $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`pid` > 0 AND `streams_sys`.`to_analyze` = 1 AND `streams_sys`.`stream_status` <> 1)";
                } else {
                    if ($_GET["filter"] == 3) {
                        $rWhere[] = "(`streams`.`direct_source` = 0 AND `streams_sys`.`stream_status` = 1)";
                    } else {
                        if ($_GET["filter"] == 4) {
                            $rWhere[] = "(`streams`.`direct_source` = 0 AND (`streams_sys`.`pid` IS NULL OR `streams_sys`.`pid` <= 0) AND `streams_sys`.`stream_status` <> 1)";
                        } else {
                            if ($_GET["filter"] == 5) {
                                $rWhere[] = "`streams`.`direct_source` = 1";
                            }
                        }
                    }
                }
            }
        }
        if (0 < strlen($_GET["series"])) {
            $rWhere[] = "`series`.`id` = " . intval($_GET["series"]);
        }
        if (0 < strlen($_GET["server"])) {
            $rWhere[] = "`streams_sys`.`server_id` = " . intval($_GET["server"]);
        }
        if ($rOrder[$rOrderRow]) {
            $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
            $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
        }
    }
    if (0 < count($rWhere)) {
        $rWhereString = "WHERE " . join(" AND ", $rWhere);
    } else {
        $rWhereString = "";
    }
    $rCountQuery = "SELECT COUNT(*) AS `count` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `streams_sys`.`server_id` LEFT JOIN `series_episodes` ON `series_episodes`.`stream_id` = `streams`.`id` LEFT JOIN `series` ON `series`.`id` = `series_episodes`.`series_id` " . $rWhereString . ";";
    $rResult = $db->query($rCountQuery);
    if ($rResult && $rResult->num_rows == 1) {
        $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
    } else {
        $rReturn["recordsTotal"] = 0;
    }
    $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
    if (0 < $rReturn["recordsTotal"]) {
        $rQuery = "SELECT `streams`.`id`, `streams_sys`.`to_analyze`, `streams`.`movie_propeties`, `streams`.`target_container`, `streams`.`stream_display_name`, `streams_sys`.`server_id`, `streams`.`notes`, `streams`.`direct_source`, `streams`.`added`,  `streams_sys`.`pid`, `streams_sys`.`monitor_pid`, `streams_sys`.`stream_status`, `streams_sys`.`stream_started`, `streams_sys`.`stream_info`, `streams_sys`.`current_source`, `streams_sys`.`bitrate`, `streams_sys`.`progress_info`, `streams_sys`.`on_demand`, `stream_categories`.`category_name`, `streaming_servers`.`server_name`, (SELECT COUNT(*) FROM `user_activity_now` WHERE `user_activity_now`.`server_id` = `streams_sys`.`server_id` AND `user_activity_now`.`stream_id` = `streams`.`id`) AS `clients`, `series`.`title`, `series`.`id` AS `sid`, `series_episodes`.`season_num` FROM `streams` LEFT JOIN `streams_sys` ON `streams_sys`.`stream_id` = `streams`.`id` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `streams_sys`.`server_id` LEFT JOIN `series_episodes` ON `series_episodes`.`stream_id` = `streams`.`id` LEFT JOIN `series` ON `series`.`id` = `series_episodes`.`series_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
        $rResult = $db->query($rQuery);
        if ($rResult && 0 < $rResult->num_rows) {
            while ($rRow = $rResult->fetch_assoc()) {
                $rSeriesName = $rRow["title"] . " - Season " . $rRow["season_num"];
                $rStreamName = "<b>" . $rRow["stream_display_name"] . "</b><br><span class= text-danger style='font-size:11px;'>" . $rSeriesName . "</span>";
                if ($rRow["server_name"]) {
                    if ($rPermissions["is_admin"]) {
                        $rServerName = $rRow["server_name"];
                    } else {
                        $rServerName = "Server #" . $rRow["server_id"];
                    }
                } else {
                    $rServerName = "No Server Selected";
                }
                $rUptime = 0;
                $rActualStatus = 0;
                if (intval($rRow["direct_source"]) == 1) {
                    $rActualStatus = 3;
                } else {
                    if ($rRow["pid"]) {
                        if ($rRow["to_analyze"] == 1) {
                            $rActualStatus = 2;
                        } else {
                            if ($rRow["stream_status"] == 1) {
                                $rActualStatus = 4;
                            } else {
                                $rActualStatus = 1;
                            }
                        }
                    } else {
                        $rActualStatus = 0;
                    }
                }
                if (hasPermissions("adv", "live_connections")) {
                    $rClients = "<a class='btn btn-light btn btn-secondary waves-light btn-xs' href=\"./live_connections.php?stream_id=" . $rRow["id"] . "&server_id=" . $rRow["server_id"] . "\">" . $rRow["clients"] . "</a>";
                } else {
                    $rClients = $rRow["clients"];
                }
                if (!$rRow["server_id"]) {
                    $rRow["server_id"] = 0;
                }
                $rButtons = "<div class=\"btn-group\">";
                if ($rPermissions["is_admin"]) {
                    if (0 < strlen($rRow["notes"])) {
                        $rButtons .= "<button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"\" data-original-title=\"" . $rRow["notes"] . "\"><i class=\"mdi mdi-note\"></i></button>";
                    } else {
                        $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-note\"></i></button>";
                    }
                }
                if (hasPermissions("adv", "edit_episode")) {
                    if (intval($rActualStatus) == 1) {
                        $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Encode\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-start\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'start');\"><i class=\"mdi mdi-refresh\"></i></button>\r\n\t\t\t\t\t\t";
                    } else {
                        if (intval($rActualStatus) == 3) {
                            $rButtons .= "<button disabled type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-stop\"><i class=\"mdi mdi-stop\"></i></button>\r\n\t\t\t\t\t\t";
                        } else {
                            if (intval($rActualStatus) == 2) {
                                $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Stop Encoding\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-stop\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'stop');\"><i class=\"mdi mdi-stop\"></i></button>\r\n\t\t\t\t\t\t";
                            } else {
                                $rButtons .= "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Encode\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs api-start\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'start');\"><i class=\"mdi mdi-play\"></i></button>\r\n\t\t\t\t\t\t";
                            }
                        }
                    }
                    $rButtons .= "<a href=\"./episode.php?id=" . $rRow["id"] . "&sid=" . $rRow["sid"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", " . $rRow["server_id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                }
                $rButtons .= "</div>";
                if (hasPermissions("adv", "player")) {
                    if ((intval($rActualStatus) == 1 || $rActualStatus == 3) && 0 < strlen($rAdminSettings["admin_username"]) && 0 < strlen($rAdminSettings["admin_password"])) {
                        $rPlayer = "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Play\" type=\"button\" class=\"btn btn-outline-info waves-effect waves-info btn-xs\" onClick=\"player(" . $rRow["id"] . ", '" . json_decode($rRow["target_container"], true)[0] . "');\"><i class=\"mdi mdi-play\"></i></button>";
                    } else {
                        $rPlayer = "<button type=\"button\" disabled class=\"btn btn-info waves-effect waves-info btn-xs\"><i class=\"mdi mdi-play\"></i></button>";
                    }
                } else {
                    $rPlayer = "<button type=\"button\" disabled class=\"btn btn-light waves-effect waves-light btn-xs\"><i class=\"mdi mdi-play\"></i></button>";
                }
                $rStreamInfoText = "<div style='font-size: 10px;' class='text-center' align='center'><tbody><tr><td colspan='3' class='col'>No information available</td></tr></tbody></div>";
                $rStreamInfo = json_decode($rRow["stream_info"], true);
                if ($rActualStatus == 1) {
                    if (!isset($rStreamInfo["codecs"]["video"])) {
                        $rStreamInfo["codecs"]["video"] = ["width" => "?", "height" => "?", "codec_name" => "N/A", "r_frame_rate" => "--"];
                    }
                    if (!isset($rStreamInfo["codecs"]["audio"])) {
                        $rStreamInfo["codecs"]["audio"] = ["codec_name" => "N/A"];
                    }
                    if (!isset($rStreamInfo["codecs"]["video"])) {
                        $rStreamInfo["codecs"]["video"] = ["duration" => "N/A"];
                    }
                    if ($rRow["bitrate"] == 0) {
                        $rRow["bitrate"] = "?";
                    }
                    $rStreamInfoText = "<div style='font-size: 13px;' class='text-center' align='center'>\r\n                                <td class='col'>" . $rRow["bitrate"] . " Kbps " . $rStreamInfo["codecs"]["video"]["width"] . "x" . $rStreamInfo["codecs"]["video"]["height"] . "</td>\r\n\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t<td class='col'><i class='mdi mdi-video' data-name='mdi-video' style='color: #20a009;'></i> " . $rStreamInfo["codecs"]["video"]["codec_name"] . "</td>\r\n                                <td class='col'><i class='mdi mdi-volume-high' data-name='mdi-volume-high' style='color: #20a009;'></i> " . $rStreamInfo["codecs"]["audio"]["codec_name"] . "</td>\r\n\t\t\t\t\t\t\t\t<!--<td class='col'><i class='mdi mdi-clock' data-name='mdi-clock' style='color: #20a009;'></i>" . $rStreamInfo["duration"] . "</td>-->\r\n\r\n                    </div>";
                }
                if ($rStreamInfo["duration"]) {
                    $rCreatedEpisode = "<span>" . date("d-m-Y", $rRow["added"]) . "</span>";
                } else {
                    $rCreatedEpisode = "<span>" . date("d-m-Y", $rRow["added"]) . "</span>";
                }
                if ($rStreamInfo["duration"]) {
                    $rDurationEpisode = "<span><i class='mdi mdi-clock-outline' data-name='mdi-clock-outline' style='color: #20a009;'></i> " . $rStreamInfo["duration"] . "</span>";
                } else {
                    $rDurationEpisode = "<span->-</span>";
                }
                $rStreamInfo1 = json_decode($rRow["movie_propeties"], true);
                if ($rStreamInfo1["movie_image"]) {
                    $rIcon1 = "<center><img src='" . $rStreamInfo1["movie_image"] . "' height='90' width='60' /></center>";
                } else {
                    $rIcon1 = "";
                }
                if ($rPermissions["is_admin"]) {
                    $rReturn["data"][] = [$rRow["id"], "<a href=\"./episode_info.php?id=" . $rRow["id"] . "&sid=" . $rRow["sid"] . "\">" . $rIcon1, "<a href=\"./episode_info.php?id=" . $rRow["id"] . "&sid=" . $rRow["sid"] . "\">" . $rStreamName, $rServerName, $rClients, $rVODStatusArray[$rActualStatus], $rButtons, $rPlayer, $rCreatedEpisode, $rDurationEpisode, $rStreamInfoText];
                } else {
                    $rReturn["data"][] = [$rRow["id"], $rIcon1, $rStreamName, $rServerName, $rStreamInfoText];
                }
            }
        }
    }
    echo json_encode($rReturn);
    exit;
}
if ($rType == "backups") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "database")) {
        exit;
    }
    $rBackups = getBackups();
    $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => count($rBackups), "recordsFiltered" => count($rBackups), "data" => []];
    foreach ($rBackups as $rBackup) {
        $rButtons = "<div class=\"btn-group\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Restore Backup\" class=\"btn btn-info waves-effect waves-light btn-xs\" onClick=\"api('" . $rBackup["filename"] . "', 'restore');\"><i class=\"mdi mdi-folder-upload\"></i></button>\r\n\t\t<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Download Backup\" class=\"btn btn-info waves-effect waves-light btn-xs\" onClick=\"api('" . $rBackup["filename"] . "', 'download');\"><i class=\"mdi mdi-arrow-collapse-down\"></i></button>\t\r\n\t\t<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete Backup\" class=\"btn btn-info waves-effect waves-light btn-xs\" onClick=\"api('" . $rBackup["filename"] . "', 'delete');\"><i class=\"mdi mdi-close\"></i></button></div>";
        $rReturn["data"][] = [$rBackup["date"], $rBackup["filename"], "<button type=\"button\" class=\"btn btn-dark btn-xs waves-effect waves-light\">" . ceil($rBackup["filesize"] / 1024 / 1024) . " MB", $rButtons];
    }
    echo json_encode($rReturn);
    exit;
} else {
    if ($rType == "conn") {
        if (!$rPermissions["is_admin"] || !hasPermissions("adv", "database")) {
            exit;
        }
        $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 1, "recordsFiltered" => 1, "data" => [$_INFO["host"], $_INFO["db_user"], $_INFO["db_pass"], $_INFO["db_name"], $_INFO["db_port"]]];
        echo json_encode($rReturn);
        exit;
    }
    if ($rType == "watch_output") {
        if (!$rPermissions["is_admin"] || !hasPermissions("adv", "folder_watch_output")) {
            exit;
        }
        $rReturn = ["draw" => $_GET["draw"], "recordsTotal" => 0, "recordsFiltered" => 0, "data" => []];
        $rOrder = ["`watch_output`.`id`", "`watch_output`.`type`", "`watch_output`.`server_id`", "`watch_output`.`filename`", "`watch_output`.`status`", "`watch_output`.`dateadded`", false];
        if (0 < strlen($_GET["order"][0]["column"])) {
            $rOrderRow = intval($_GET["order"][0]["column"]);
        } else {
            $rOrderRow = 0;
        }
        $rWhere = [];
        if (0 < strlen($_GET["search"]["value"])) {
            $rSearch = $_GET["search"]["value"];
            $rWhere[] = "(`watch_output`.`id` LIKE '%" . $rSearch . "%' OR `watch_output`.`filename` LIKE '%" . $rSearch . "%' OR `watch_output`.`dateadded` LIKE '%" . $rSearch . "%')";
        }
        if (0 < strlen($_GET["server"])) {
            $rWhere[] = "`watch_output`.`server_id` = " . intval($_GET["server"]);
        }
        if (0 < strlen($_GET["type"])) {
            $rWhere[] = "`watch_output`.`type` = " . intval($_GET["type"]);
        }
        if (0 < strlen($_GET["status"])) {
            $rWhere[] = "`watch_output`.`status` = " . intval($_GET["status"]);
        }
        if ($rOrder[$rOrderRow]) {
            $rOrderDirection = strtolower($_GET["order"][0]["dir"]) === "desc" ? "desc" : "asc";
            $rOrderBy = "ORDER BY " . $rOrder[$rOrderRow] . " " . $rOrderDirection;
        }
        if (0 < count($rWhere)) {
            $rWhereString = "WHERE " . join(" AND ", $rWhere);
        } else {
            $rWhereString = "";
        }
        $rCountQuery = "SELECT COUNT(*) AS `count` FROM `watch_output` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `watch_output`.`server_id` " . $rWhereString . ";";
        $rResult = $db->query($rCountQuery);
        if ($rResult && $rResult->num_rows == 1) {
            $rReturn["recordsTotal"] = $rResult->fetch_assoc()["count"];
        } else {
            $rReturn["recordsTotal"] = 0;
        }
        $rReturn["recordsFiltered"] = $rReturn["recordsTotal"];
        if (0 < $rReturn["recordsTotal"]) {
            $rQuery = "SELECT `watch_output`.`id`, `watch_output`.`type`, `watch_output`.`server_id`, `streaming_servers`.`server_name`, `watch_output`.`filename`, `watch_output`.`status`, `watch_output`.`stream_id`, `watch_output`.`dateadded` FROM `watch_output` LEFT JOIN `streaming_servers` ON `streaming_servers`.`id` = `watch_output`.`server_id` " . $rWhereString . " " . $rOrderBy . " LIMIT " . $rStart . ", " . $rLimit . ";";
            $rResult = $db->query($rQuery);
            if ($rResult && 0 < $rResult->num_rows) {
                while ($rRow = $rResult->fetch_assoc()) {
                    $rButtons = "<div class=\"btn-group\">";
                    if (0 < $rRow["stream_id"]) {
                        if ($rRow["type"] == 1) {
                            if (hasPermissions("adv", "edit_movie")) {
                                $rButtons = "<a href=\"./movie.php?id=" . $rRow["stream_id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit Movie\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t\t\t";
                            }
                        } else {
                            if (hasPermissions("adv", "edit_episode")) {
                                $rButtons = "<a href=\"./episode.php?id=" . $rRow["stream_id"] . "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit Episode\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil\"></i></button></a>\r\n\t\t\t\t\t\t\t";
                            }
                        }
                    }
                    $rButtons .= "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $rRow["id"] . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
                    $rButtons .= "</div>";
                    $rReturn["data"][] = [$rRow["id"], ["1" => "Movies", "2" => "Series"][$rRow["type"]], $rRow["server_name"], $rRow["filename"], $rWatchStatusArray[$rRow["status"]], $rRow["dateadded"], $rButtons];
                }
            }
        }
        echo json_encode($rReturn);
        exit;
    }
}

?>