<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if ($rPermissions["is_admin"]) {
    exit;
}
if ($rPermissions["is_reseller"] && !$rPermissions["reseller_can_select_bouquets"]) {
    exit;
}
$rRegisteredUsers = getRegisteredUsers($rUserInfo["id"]);
if (isset($_GET["trial"]) || isset($_POST["trial"])) {
    if ($rAdminSettings["disable_trial"]) {
        $canGenerateTrials = false;
    } else {
        if (floatval($rUserInfo["credits"]) < floatval($rPermissions["minimum_trial_credits"])) {
            $canGenerateTrials = false;
        } else {
            $canGenerateTrials = checkTrials();
        }
    }
} else {
    $canGenerateTrials = true;
}
if (isset($_POST["submit_user"])) {
    $_POST["mac_address_mag"] = strtoupper($_POST["mac_address_mag"]);
    $_POST["mac_address_e2"] = strtoupper($_POST["mac_address_e2"]);
    if (isset($_POST["edit"])) {
        if (!hasPermissions("user", $_POST["edit"])) {
            exit;
        }
        $rUser = getUser($_POST["edit"]);
        if (!$rUser) {
            exit;
        }
    }
    if (isset($rUser)) {
        $rArray = $rUser;
        unset($rArray["id"]);
    } else {
        $rArray = ["member_id" => 0, "username" => "", "password" => "", "exp_date" => NULL, "admin_enabled" => 1, "enabled" => 1, "admin_notes" => "", "reseller_notes" => "", "bouquet" => [], "max_connections" => 1, "is_restreamer" => 0, "allowed_ips" => [], "allowed_ua" => [], "created_at" => time(), "created_by" => -1, "is_mag" => 0, "is_e2" => 0, "force_server_id" => 0, "is_isplock" => 0, "isp_desc" => "", "forced_country" => "", "is_stalker" => 0, "bypass_ua" => 0, "play_token" => ""];
    }
    if (!empty($_POST["package"])) {
        $rPackage = getPackage($_POST["package"]);
        if ($rPackage && in_array($rUserInfo["member_group_id"], json_decode($rPackage["groups"], true))) {
            if ($_POST["trial"]) {
                $rCost = floatval($rPackage["trial_credits"]);
            } else {
                $rOverride = json_decode($rUserInfo["override_packages"], true);
                if (isset($rOverride[$rPackage["id"]]["official_credits"]) && 0 < strlen($rOverride[$rPackage["id"]]["official_credits"])) {
                    $rCost = floatval($rOverride[$rPackage["id"]]["official_credits"]);
                } else {
                    $rCost = floatval($rPackage["official_credits"]);
                }
            }
            if ($rCost <= floatval($rUserInfo["credits"]) && $canGenerateTrials) {
                if ($_POST["trial"]) {
                    $rArray["exp_date"] = strtotime("+" . intval($rPackage["trial_duration"]) . " " . $rPackage["trial_duration_in"]);
                    $rArray["is_trial"] = 1;
                } else {
                    if (isset($rUser)) {
                        if (time() <= $rUser["exp_date"]) {
                            $rArray["exp_date"] = strtotime("+" . intval($rPackage["official_duration"]) . " " . $rPackage["official_duration_in"], intval($rUser["exp_date"]));
                        } else {
                            $rArray["exp_date"] = strtotime("+" . intval($rPackage["official_duration"]) . " " . $rPackage["official_duration_in"]);
                        }
                    } else {
                        $rArray["exp_date"] = strtotime("+" . intval($rPackage["official_duration"]) . " " . $rPackage["official_duration_in"]);
                    }
                    $rArray["is_trial"] = 0;
                }
                $rArray["bouquet"] = $rPackage["bouquets"];
                $rArray["max_connections"] = $rPackage["max_connections"];
                $rArray["is_restreamer"] = $rPackage["is_restreamer"];
                $rOwner = $_POST["member_id"];
                if (in_array($rOwner, array_keys($rRegisteredUsers))) {
                    $rArray["member_id"] = $rOwner;
                } else {
                    $rArray["member_id"] = $rUserInfo["id"];
                }
                $rArray["reseller_notes"] = $_POST["reseller_notes"];
                if (isset($_POST["is_mag"])) {
                    $rArray["is_mag"] = 1;
                }
                if (isset($_POST["is_e2"])) {
                    $rArray["is_e2"] = 1;
                }
            } else {
                $_STATUS = 4;
            }
        } else {
            $_STATUS = 3;
        }
    } else {
        if (isset($rUser)) {
            $rArray["reseller_notes"] = $_POST["reseller_notes"];
            $rOwner = $_POST["member_id"];
            if (in_array($rOwner, array_keys($rRegisteredUsers))) {
                $rArray["member_id"] = $rOwner;
            } else {
                $rArray["member_id"] = $rUserInfo["id"];
            }
        } else {
            $_STATUS = 3;
        }
    }
    if (!$rPermissions["allow_change_pass"]) {
        if (isset($rUser)) {
            $_POST["password"] = $rUser["password"];
        } else {
            $_POST["password"] = "";
        }
    }
    if (!$rPermissions["allow_change_pass"] && !$rAdminSettings["change_usernames"]) {
        if (isset($rUser)) {
            $_POST["username"] = $rUser["username"];
        } else {
            $_POST["username"] = "";
        }
    }
    if (strlen($_POST["username"]) == 0 || $rArray["is_mag"] && !isset($rUser) || $rArray["is_e2"] && !isset($rUser)) {
        $_POST["username"] = generateString(10);
    } else {
        if ($rArray["is_mag"] && isset($rUser) || $rArray["is_e2"] && isset($rUser)) {
            $_POST["username"] = $rUser["username"];
        }
    }
    if (strlen($_POST["password"]) == 0 || $rArray["is_mag"] && !isset($rUser) || $rArray["is_e2"] && !isset($rUser)) {
        $_POST["password"] = generateString(10);
    } else {
        if ($rArray["is_mag"] && isset($rUser) || $rArray["is_e2"] && isset($rUser)) {
            $_POST["password"] = $rUser["password"];
        }
    }
    $rArray["username"] = $_POST["username"];
    $rArray["password"] = $_POST["password"];
    if (!isset($rUser)) {
        $result = $db->query("SELECT `id` FROM `users` WHERE `username` = '" . ESC($rArray["username"]) . "';");
        if ($result && 0 < $result->num_rows) {
            $_STATUS = 6;
        }
    }
    if ($_POST["is_mag"] && !filter_var($_POST["mac_address_mag"], FILTER_VALIDATE_MAC) || 0 < strlen($_POST["mac_address_e2"]) && !filter_var($_POST["mac_address_e2"], FILTER_VALIDATE_MAC)) {
        $_STATUS = 7;
    } else {
        if ($_POST["is_mag"]) {
            $result = $db->query("SELECT `user_id` FROM `mag_devices` WHERE mac = '" . ESC(base64_encode($_POST["mac_address_mag"])) . "' LIMIT 1;");
            if ($result && 0 < $result->num_rows) {
                if (isset($_POST["edit"])) {
                    if (intval($result->fetch_assoc()["user_id"]) != intval($_POST["edit"])) {
                        $_STATUS = 8;
                    }
                } else {
                    $_STATUS = 8;
                }
            }
        } else {
            if ($_POST["is_e2"]) {
                $result = $db->query("SELECT `user_id` FROM `enigma2_devices` WHERE mac = '" . ESC($_POST["mac_address_e2"]) . "' LIMIT 1;");
                if ($result && 0 < $result->num_rows) {
                    if (isset($_POST["edit"])) {
                        if (intval($result->fetch_assoc()["user_id"]) != intval($_POST["edit"])) {
                            $_STATUS = 8;
                        }
                    } else {
                        $_STATUS = 8;
                    }
                }
            }
        }
    }
    if ($rAdminSettings["reseller_restrictions"]) {
        if (isset($_POST["allowed_ips"])) {
            if (!is_array($_POST["allowed_ips"])) {
                $_POST["allowed_ips"] = [$_POST["allowed_ips"]];
            }
            $rArray["allowed_ips"] = json_encode($_POST["allowed_ips"]);
        } else {
            $rArray["allowed_ips"] = "[]";
        }
        if (isset($_POST["allowed_ua"])) {
            if (!is_array($_POST["allowed_ua"])) {
                $_POST["allowed_ua"] = [$_POST["allowed_ua"]];
            }
            $rArray["allowed_ua"] = json_encode($_POST["allowed_ua"]);
        } else {
            $rArray["allowed_ua"] = "[]";
        }
    }
    $rArray["bouquet"] = array_values(json_decode($_POST["bouquets_selected"], true));
    unset($_POST["bouquets_selected"]);
    if (!isset($_STATUS)) {
        $rArray["created_by"] = $rUserInfo["id"];
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
        if (isset($rUser)) {
            $rCols = "`id`," . $rCols;
            $rValues = ESC($rUser["id"]) . "," . $rValues;
        }
        $isMag = false;
        $isE2 = false;
        if ($rArray["is_mag"] && ($rPackage["can_gen_mag"] || isset($rUser))) {
            $isMag = true;
        }
        if ($rArray["is_e2"] && ($rPackage["can_gen_e2"] || isset($rUser))) {
            $isE2 = true;
        }
        if (!$isMag && !$isE2 && ($rPackage["only_mag"] || $rPackage["only_e2"]) && !isset($rUser)) {
            $_STATUS = 5;
        } else {
            $rQuery = "REPLACE INTO `users`(" . $rCols . ") VALUES(" . $rValues . ");";
            if ($db->query($rQuery)) {
                if (isset($rUser)) {
                    $rInsertID = intval($rUser["id"]);
                } else {
                    $rInsertID = $db->insert_id;
                }
                if (isset($rCost)) {
                    $rNewCredits = floatval($rUserInfo["credits"]) - floatval($rCost);
                    $db->query("UPDATE `reg_users` SET `credits` = '" . floatval($rNewCredits) . "' WHERE `id` = " . intval($rUserInfo["id"]) . ";");
                    if (isset($rUser)) {
                        if ($isMag) {
                            $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . ESC($rArray["username"]) . "', '" . ESC($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b>] -> [ " . ESC($_POST["mac_address_mag"]) . " ] " . $_["extend_mag"] . " [ " . ESC($rPackage["package_name"]) . " ], Credits: <font color=\"green\">" . ESC($rUserInfo["credits"]) . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                        } else {
                            if ($isE2) {
                                $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . ESC($rArray["username"]) . "', '" . ESC($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b>] -> [ " . ESC($_POST["mac_address_e2"]) . " ] " . $_["extend_enigma"] . " [ " . ESC($rPackage["package_name"]) . " ], Credits: <font color=\"green\">" . ESC($rUserInfo["credits"]) . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                            } else {
                                $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . ESC($rArray["username"]) . "', '" . ESC($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b>] -> [ " . ESC($_POST["username"]) . " ] " . $_["extend_m3u"] . " [ " . ESC($rPackage["package_name"]) . " ], Credits: <font color=\"green\">" . ESC($rUserInfo["credits"]) . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                            }
                        }
                    } else {
                        if ($isMag) {
                            $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . ESC($rArray["username"]) . "', '" . ESC($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b>] -> [ " . ESC($_POST["mac_address_mag"]) . " ] " . $_["new_mag"] . " [" . ESC($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . ESC($rUserInfo["credits"]) . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                        } else {
                            if ($isE2) {
                                $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . ESC($rArray["username"]) . "', '" . ESC($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b>] -> [ " . ESC($_POST["mac_address_e2"]) . " ] " . $_["new_enigma"] . " [" . ESC($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . ESC($rUserInfo["credits"]) . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                            } else {
                                $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . ESC($rArray["username"]) . "', '" . ESC($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b>] -> [ " . ESC($_POST["username"]) . " ] " . $_["new_m3u"] . " [" . ESC($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . ESC($rUserInfo["credits"]) . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                            }
                        }
                        $rAccessOutput = json_decode($rPackage["output_formats"], true);
                        $rLockDevice = $rPackage["lock_device"];
                    }
                    $rUserInfo["credits"] = $rNewCredits;
                }
                if (!isset($rUser) && isset($rInsertID) && isset($rAccessOutput)) {
                    $db->query("DELETE FROM `user_output` WHERE `user_id` = " . intval($rInsertID) . ";");
                    foreach ($rAccessOutput as $rOutputID) {
                        $db->query("INSERT INTO `user_output`(`user_id`, `access_output_id`) VALUES(" . intval($rInsertID) . ", " . intval($rOutputID) . ");");
                    }
                }
                if ($isMag) {
                    $result = $db->query("SELECT `mag_id` FROM `mag_devices` WHERE `user_id` = " . intval($rInsertID) . " LIMIT 1;");
                    if (isset($result) && $result->num_rows == 1) {
                        $db->query("UPDATE `mag_devices` SET `mac` = '" . base64_encode(ESC(strtoupper($_POST["mac_address_mag"]))) . "' WHERE `user_id` = " . intval($rInsertID) . ";");
                    } else {
                        if (!isset($rUser)) {
                            $db->query("INSERT INTO `mag_devices`(`user_id`, `mac`, `lock_device`) VALUES(" . intval($rInsertID) . ", '" . ESC(base64_encode(strtoupper($_POST["mac_address_mag"]))) . "', " . intval($rLockDevice) . ");");
                        }
                    }
                } else {
                    if ($isE2) {
                        $result = $db->query("SELECT `device_id` FROM `enigma2_devices` WHERE `user_id` = " . intval($rInsertID) . " LIMIT 1;");
                        if (isset($result) && $result->num_rows == 1) {
                            $db->query("UPDATE `enigma2_devices` SET `mac` = '" . ESC(strtoupper($_POST["mac_address_e2"])) . "' WHERE `user_id` = " . intval($rInsertID) . ";");
                        } else {
                            if (!isset($rUser)) {
                                $db->query("INSERT INTO `enigma2_devices`(`user_id`, `mac`, `lock_device`) VALUES(" . intval($rInsertID) . ", '" . ESC(strtoupper($_POST["mac_address_e2"])) . "', " . intval($rLockDevice) . ");");
                            }
                        }
                    }
                }
                header("Location: ./user_reseller.php?id=" . $rInsertID);
                exit;
            } else {
                $_STATUS = 2;
            }
        }
    }
}
if (isset($_GET["id"])) {
    if (!hasPermissions("user", $_GET["id"])) {
        exit;
    }
    $rUser = getUser($_GET["id"]);
    if (!$rUser) {
        exit;
    }
    $rMAGUser = getMAGUser($_GET["id"]);
    if ($rUser["is_mag"]) {
        $rUser["lock_device"] = $rMAGUser["lock_device"];
        $rUser["mac_address_mag"] = base64_decode($rMAGUser["mac"]);
    }
    if ($rUser["is_e2"]) {
        $rUser["mac_address_e2"] = getE2User($_GET["id"])["mac"];
    }
    $rUser["outputs"] = getOutputs($rUser["id"]);
}
$rCountries = [["id" => "", "name" => "Off"], ["id" => "A1", "name" => "Anonymous Proxy"], ["id" => "A2", "name" => "Satellite Provider"], ["id" => "O1", "name" => "Other Country"], ["id" => "AF", "name" => "Afghanistan"], ["id" => "AX", "name" => "Aland Islands"], ["id" => "AL", "name" => "Albania"], ["id" => "DZ", "name" => "Algeria"], ["id" => "AS", "name" => "American Samoa"], ["id" => "AD", "name" => "Andorra"], ["id" => "AO", "name" => "Angola"], ["id" => "AI", "name" => "Anguilla"], ["id" => "AQ", "name" => "Antarctica"], ["id" => "AG", "name" => "Antigua And Barbuda"], ["id" => "AR", "name" => "Argentina"], ["id" => "AM", "name" => "Armenia"], ["id" => "AW", "name" => "Aruba"], ["id" => "AU", "name" => "Australia"], ["id" => "AT", "name" => "Austria"], ["id" => "AZ", "name" => "Azerbaijan"], ["id" => "BS", "name" => "Bahamas"], ["id" => "BH", "name" => "Bahrain"], ["id" => "BD", "name" => "Bangladesh"], ["id" => "BB", "name" => "Barbados"], ["id" => "BY", "name" => "Belarus"], ["id" => "BE", "name" => "Belgium"], ["id" => "BZ", "name" => "Belize"], ["id" => "BJ", "name" => "Benin"], ["id" => "BM", "name" => "Bermuda"], ["id" => "BT", "name" => "Bhutan"], ["id" => "BO", "name" => "Bolivia"], ["id" => "BA", "name" => "Bosnia And Herzegovina"], ["id" => "BW", "name" => "Botswana"], ["id" => "BV", "name" => "Bouvet Island"], ["id" => "BR", "name" => "Brazil"], ["id" => "IO", "name" => "British Indian Ocean Territory"], ["id" => "BN", "name" => "Brunei Darussalam"], ["id" => "BG", "name" => "Bulgaria"], ["id" => "BF", "name" => "Burkina Faso"], ["id" => "BI", "name" => "Burundi"], ["id" => "KH", "name" => "Cambodia"], ["id" => "CM", "name" => "Cameroon"], ["id" => "CA", "name" => "Canada"], ["id" => "CV", "name" => "Cape Verde"], ["id" => "KY", "name" => "Cayman Islands"], ["id" => "CF", "name" => "Central African Republic"], ["id" => "TD", "name" => "Chad"], ["id" => "CL", "name" => "Chile"], ["id" => "CN", "name" => "China"], ["id" => "CX", "name" => "Christmas Island"], ["id" => "CC", "name" => "Cocos (Keeling) Islands"], ["id" => "CO", "name" => "Colombia"], ["id" => "KM", "name" => "Comoros"], ["id" => "CG", "name" => "Congo"], ["id" => "CD", "name" => "Congo, Democratic Republic"], ["id" => "CK", "name" => "Cook Islands"], ["id" => "CR", "name" => "Costa Rica"], ["id" => "CI", "name" => "Cote D'Ivoire"], ["id" => "HR", "name" => "Croatia"], ["id" => "CU", "name" => "Cuba"], ["id" => "CY", "name" => "Cyprus"], ["id" => "CZ", "name" => "Czech Republic"], ["id" => "DK", "name" => "Denmark"], ["id" => "DJ", "name" => "Djibouti"], ["id" => "DM", "name" => "Dominica"], ["id" => "DO", "name" => "Dominican Republic"], ["id" => "EC", "name" => "Ecuador"], ["id" => "EG", "name" => "Egypt"], ["id" => "SV", "name" => "El Salvador"], ["id" => "GQ", "name" => "Equatorial Guinea"], ["id" => "ER", "name" => "Eritrea"], ["id" => "EE", "name" => "Estonia"], ["id" => "ET", "name" => "Ethiopia"], ["id" => "FK", "name" => "Falkland Islands (Malvinas)"], ["id" => "FO", "name" => "Faroe Islands"], ["id" => "FJ", "name" => "Fiji"], ["id" => "FI", "name" => "Finland"], ["id" => "FR", "name" => "France"], ["id" => "GF", "name" => "French Guiana"], ["id" => "PF", "name" => "French Polynesia"], ["id" => "TF", "name" => "French Southern Territories"], ["id" => "MK", "name" => "Fyrom"], ["id" => "GA", "name" => "Gabon"], ["id" => "GM", "name" => "Gambia"], ["id" => "GE", "name" => "Georgia"], ["id" => "DE", "name" => "Germany"], ["id" => "GH", "name" => "Ghana"], ["id" => "GI", "name" => "Gibraltar"], ["id" => "GR", "name" => "Greece"], ["id" => "GL", "name" => "Greenland"], ["id" => "GD", "name" => "Grenada"], ["id" => "GP", "name" => "Guadeloupe"], ["id" => "GU", "name" => "Guam"], ["id" => "GT", "name" => "Guatemala"], ["id" => "GG", "name" => "Guernsey"], ["id" => "GN", "name" => "Guinea"], ["id" => "GW", "name" => "Guinea-Bissau"], ["id" => "GY", "name" => "Guyana"], ["id" => "HT", "name" => "Haiti"], ["id" => "HM", "name" => "Heard Island & Mcdonald Islands"], ["id" => "VA", "name" => "Holy See (Vatican City State)"], ["id" => "HN", "name" => "Honduras"], ["id" => "HK", "name" => "Hong Kong"], ["id" => "HU", "name" => "Hungary"], ["id" => "IS", "name" => "Iceland"], ["id" => "IN", "name" => "India"], ["id" => "ID", "name" => "Indonesia"], ["id" => "IR", "name" => "Iran, Islamic Republic Of"], ["id" => "IQ", "name" => "Iraq"], ["id" => "IE", "name" => "Ireland"], ["id" => "IM", "name" => "Isle Of Man"], ["id" => "IL", "name" => "Israel"], ["id" => "IT", "name" => "Italy"], ["id" => "JM", "name" => "Jamaica"], ["id" => "JP", "name" => "Japan"], ["id" => "JE", "name" => "Jersey"], ["id" => "JO", "name" => "Jordan"], ["id" => "KZ", "name" => "Kazakhstan"], ["id" => "KE", "name" => "Kenya"], ["id" => "KI", "name" => "Kiribati"], ["id" => "KR", "name" => "Korea"], ["id" => "KW", "name" => "Kuwait"], ["id" => "KG", "name" => "Kyrgyzstan"], ["id" => "LA", "name" => "Lao People's Democratic Republic"], ["id" => "LV", "name" => "Latvia"], ["id" => "LB", "name" => "Lebanon"], ["id" => "LS", "name" => "Lesotho"], ["id" => "LR", "name" => "Liberia"], ["id" => "LY", "name" => "Libyan Arab Jamahiriya"], ["id" => "LI", "name" => "Liechtenstein"], ["id" => "LT", "name" => "Lithuania"], ["id" => "LU", "name" => "Luxembourg"], ["id" => "MO", "name" => "Macao"], ["id" => "MG", "name" => "Madagascar"], ["id" => "MW", "name" => "Malawi"], ["id" => "MY", "name" => "Malaysia"], ["id" => "MV", "name" => "Maldives"], ["id" => "ML", "name" => "Mali"], ["id" => "MT", "name" => "Malta"], ["id" => "MH", "name" => "Marshall Islands"], ["id" => "MQ", "name" => "Martinique"], ["id" => "MR", "name" => "Mauritania"], ["id" => "MU", "name" => "Mauritius"], ["id" => "YT", "name" => "Mayotte"], ["id" => "MX", "name" => "Mexico"], ["id" => "FM", "name" => "Micronesia, Federated States Of"], ["id" => "MD", "name" => "Moldova"], ["id" => "MC", "name" => "Monaco"], ["id" => "MN", "name" => "Mongolia"], ["id" => "ME", "name" => "Montenegro"], ["id" => "MS", "name" => "Montserrat"], ["id" => "MA", "name" => "Morocco"], ["id" => "MZ", "name" => "Mozambique"], ["id" => "MM", "name" => "Myanmar"], ["id" => "NA", "name" => "Namibia"], ["id" => "NR", "name" => "Nauru"], ["id" => "NP", "name" => "Nepal"], ["id" => "NL", "name" => "Netherlands"], ["id" => "AN", "name" => "Netherlands Antilles"], ["id" => "NC", "name" => "New Caledonia"], ["id" => "NZ", "name" => "New Zealand"], ["id" => "NI", "name" => "Nicaragua"], ["id" => "NE", "name" => "Niger"], ["id" => "NG", "name" => "Nigeria"], ["id" => "NU", "name" => "Niue"], ["id" => "NF", "name" => "Norfolk Island"], ["id" => "MP", "name" => "Northern Mariana Islands"], ["id" => "NO", "name" => "Norway"], ["id" => "OM", "name" => "Oman"], ["id" => "PK", "name" => "Pakistan"], ["id" => "PW", "name" => "Palau"], ["id" => "PS", "name" => "Palestinian Territory, Occupied"], ["id" => "PA", "name" => "Panama"], ["id" => "PG", "name" => "Papua New Guinea"], ["id" => "PY", "name" => "Paraguay"], ["id" => "PE", "name" => "Peru"], ["id" => "PH", "name" => "Philippines"], ["id" => "PN", "name" => "Pitcairn"], ["id" => "PL", "name" => "Poland"], ["id" => "PT", "name" => "Portugal"], ["id" => "PR", "name" => "Puerto Rico"], ["id" => "QA", "name" => "Qatar"], ["id" => "RE", "name" => "Reunion"], ["id" => "RO", "name" => "Romania"], ["id" => "RU", "name" => "Russian Federation"], ["id" => "RW", "name" => "Rwanda"], ["id" => "BL", "name" => "Saint Barthelemy"], ["id" => "SH", "name" => "Saint Helena"], ["id" => "KN", "name" => "Saint Kitts And Nevis"], ["id" => "LC", "name" => "Saint Lucia"], ["id" => "MF", "name" => "Saint Martin"], ["id" => "PM", "name" => "Saint Pierre And Miquelon"], ["id" => "VC", "name" => "Saint Vincent And Grenadines"], ["id" => "WS", "name" => "Samoa"], ["id" => "SM", "name" => "San Marino"], ["id" => "ST", "name" => "Sao Tome And Principe"], ["id" => "SA", "name" => "Saudi Arabia"], ["id" => "SN", "name" => "Senegal"], ["id" => "RS", "name" => "Serbia"], ["id" => "SC", "name" => "Seychelles"], ["id" => "SL", "name" => "Sierra Leone"], ["id" => "SG", "name" => "Singapore"], ["id" => "SK", "name" => "Slovakia"], ["id" => "SI", "name" => "Slovenia"], ["id" => "SB", "name" => "Solomon Islands"], ["id" => "SO", "name" => "Somalia"], ["id" => "ZA", "name" => "South Africa"], ["id" => "GS", "name" => "South Georgia And Sandwich Isl."], ["id" => "ES", "name" => "Spain"], ["id" => "LK", "name" => "Sri Lanka"], ["id" => "SD", "name" => "Sudan"], ["id" => "SR", "name" => "Suriname"], ["id" => "SJ", "name" => "Svalbard And Jan Mayen"], ["id" => "SZ", "name" => "Swaziland"], ["id" => "SE", "name" => "Sweden"], ["id" => "CH", "name" => "Switzerland"], ["id" => "SY", "name" => "Syrian Arab Republic"], ["id" => "TW", "name" => "Taiwan"], ["id" => "TJ", "name" => "Tajikistan"], ["id" => "TZ", "name" => "Tanzania"], ["id" => "TH", "name" => "Thailand"], ["id" => "TL", "name" => "Timor-Leste"], ["id" => "TG", "name" => "Togo"], ["id" => "TK", "name" => "Tokelau"], ["id" => "TO", "name" => "Tonga"], ["id" => "TT", "name" => "Trinidad And Tobago"], ["id" => "TN", "name" => "Tunisia"], ["id" => "TR", "name" => "Turkey"], ["id" => "TM", "name" => "Turkmenistan"], ["id" => "TC", "name" => "Turks And Caicos Islands"], ["id" => "TV", "name" => "Tuvalu"], ["id" => "UG", "name" => "Uganda"], ["id" => "UA", "name" => "Ukraine"], ["id" => "AE", "name" => "United Arab Emirates"], ["id" => "GB", "name" => "United Kingdom"], ["id" => "US", "name" => "United States"], ["id" => "UM", "name" => "United States Outlying Islands"], ["id" => "UY", "name" => "Uruguay"], ["id" => "UZ", "name" => "Uzbekistan"], ["id" => "VU", "name" => "Vanuatu"], ["id" => "VE", "name" => "Venezuela"], ["id" => "VN", "name" => "Viet Nam"], ["id" => "VG", "name" => "Virgin Islands, British"], ["id" => "VI", "name" => "Virgin Islands, U.S."], ["id" => "WF", "name" => "Wallis And Futuna"], ["id" => "EH", "name" => "Western Sahara"], ["id" => "YE", "name" => "Yemen"], ["id" => "ZM", "name" => "Zambia"], ["id" => "ZW", "name" => "Zimbabwe"]];
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./users.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_users"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rUser)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
if (isset($_GET["trial"])) {
    echo $_["trial"];
}
echo " ";
echo $_["user"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (!$canGenerateTrials) {
    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["you_have_used_your_allowance"];
    echo "                        </div>\n                        ";
}
if (isset($_STATUS)) {
    if ($_STATUS == 0) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["user_operation_was_completed_successfully"];
        echo "                        </div>\n                        ";
    } else {
        if ($_STATUS == 1) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["an_invalid_expiration_date_was_entered"];
            echo "                        </div>\n                        ";
        } else {
            if ($_STATUS == 2) {
                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                echo $_["there_was_an_error"];
                echo "                        </div>\n                        ";
            } else {
                if ($_STATUS == 3) {
                    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                    echo $_["an_invalid_package_was_selected"];
                    echo "                        </div>\n                        ";
                } else {
                    if ($_STATUS == 4) {
                        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                        echo $_["you_don't_have_enough_credits"];
                        echo "                        </div>\n                        ";
                    } else {
                        if ($_STATUS == 5) {
                            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                            echo $_["you_are_not_permitted_to_generate"];
                            echo "                        </div>\n                        ";
                        } else {
                            if ($_STATUS == 6) {
                                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                                echo $_["this_username_already_exists"];
                                echo "                        </div>\n                        ";
                            } else {
                                if ($_STATUS == 7) {
                                    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                                    echo $_["an_invalid_mac_address_was_entered"];
                                    echo "                        </div>\n                        ";
                                } else {
                                    if ($_STATUS == 8) {
                                        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                                        echo $_["this_mac_address_is_already_in_use"];
                                        echo "                        </div>\n                        ";
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
if (isset($rUser) && $rUser["is_trial"]) {
    echo "                        <div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["this_is_a_trial_user"];
    echo "                        </div>\n                        ";
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./user_reseller_edit.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"user_form\">\n                                    ";
if (isset($rUser)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rUser["id"];
    echo "\" />\n                                    ";
}
if (isset($_GET["trial"])) {
    echo "                                    <input type=\"hidden\" name=\"trial\" value=\"1\" />\n                                    ";
}
echo "\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"bouquets_selected\" id=\"bouquets_selected\" value=\"\" />\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#review-purchase\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-book-open-variant mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["bouquets"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"user-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\" id=\"uname\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"username\">";
echo $_["username"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input";
if (!$rPermissions["allow_change_pass"] && !$rAdminSettings["change_usernames"]) {
    echo $_[" disabled"];
}
echo " type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["username"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\" id=\"pass\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"password\">";
echo $_["password"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input";
if (!$rPermissions["allow_change_pass"]) {
    echo " disabled";
}
echo " type=\"text\" class=\"form-control\" id=\"password\" name=\"password\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["password"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"member_id\">";
echo $_["owner"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"member_id\" id=\"member_id\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
foreach ($rRegisteredUsers as $rRegisteredUser) {
    echo "                                                                    <option ";
    if (isset($rUser)) {
        if (intval($rUser["member_id"]) == intval($rRegisteredUser["id"])) {
            echo "selected ";
        }
    } else {
        if ($rUserInfo["id"] == $rRegisteredUser["id"]) {
            echo "selected ";
        }
    }
    echo "value=\"";
    echo $rRegisteredUser["id"];
    echo "\">";
    echo $rRegisteredUser["username"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"package\">";
if (isset($rUser)) {
    echo "Extend ";
}
echo $_["package"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"package\" id=\"package\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
if (isset($rUser)) {
    echo "                                                                    <option value=\"\">";
    echo $_["no_changes"];
    echo "</option>\n                                                                    ";
}
foreach (getPackages() as $rPackage) {
    if (in_array($rUserInfo["member_group_id"], json_decode($rPackage["groups"], true)) && ($rPackage["is_trial"] && (isset($_GET["trial"]) || isset($_POST["trial"])) || $rPackage["is_official"] && !isset($_GET["trial"]) && !isset($_POST["trial"]))) {
        echo "                                                                        <option value=\"";
        echo $rPackage["id"];
        echo "\">";
        echo $rPackage["package_name"];
        echo "</option>\n                                                                        ";
    }
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"max_connections\">";
echo $_["max_connections"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input disabled type=\"text\" class=\"form-control\" id=\"max_connections\" name=\"max_connections\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["max_connections"]);
} else {
    echo "1";
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"exp_date\">";
echo $_["expiry"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["leave_blank_for_unlimited"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" disabled class=\"form-control text-center date\" id=\"exp_date\" name=\"exp_date\" value=\"";
if (isset($rUser)) {
    if (!is_null($rUser["exp_date"])) {
        echo date("Y-m-d", $rUser["exp_date"]);
    } else {
        echo "\" disabled=\"disabled";
    }
}
echo "\" data-toggle=\"date-picker\" data-single-date-picker=\"true\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_mag\">";
echo $_["mag_device"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["this_option_will_be_selected_mag"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input";
if (isset($rUser)) {
    echo " disabled";
}
echo " name=\"is_mag\" id=\"is_mag\" type=\"checkbox\" ";
if (isset($rUser)) {
    if ($rUser["is_mag"] == 1) {
        echo "checked ";
    }
} else {
    if (isset($_GET["mag"])) {
        echo "checked ";
    }
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_e2\">";
echo $_["enigma_device"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["this_option_will_be_selected_enigma"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input";
if (isset($rUser)) {
    echo " disabled";
}
echo " name=\"is_e2\" id=\"is_e2\" type=\"checkbox\" ";
if (isset($rUser)) {
    if ($rUser["is_e2"] == 1) {
        echo "checked ";
    }
} else {
    if (isset($_GET["e2"])) {
        echo "checked ";
    }
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\" id=\"mac_entry_mag\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"mac_address_mag\">";
echo $_["mac_address"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"mac_address_mag\" name=\"mac_address_mag\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["mac_address_mag"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\" style=\"display:none\" id=\"mac_entry_e2\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"mac_address_e2\">";
echo $_["mac_address"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"mac_address_e2\" name=\"mac_address_e2\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["mac_address_e2"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller_notes\">";
echo $_["reseller_notes"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <textarea id=\"reseller_notes\" name=\"reseller_notes\" class=\"form-control\" rows=\"3\" placeholder=\"\">";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["reseller_notes"]);
}
echo "</textarea>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            ";
if ($rAdminSettings["reseller_restrictions"]) {
    echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"tab-pane\" id=\"restrictions\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"ip_field\">";
    echo $_["allowed_ip_addresses"];
    echo "</label>\n                                                            <div class=\"col-md-8 input-group\">\n                                                                <input type=\"text\" id=\"ip_field\" class=\"form-control\" value=\"\">\n                                                                <div class=\"input-group-append\">\n                                                                    <a href=\"javascript:void(0)\" id=\"add_ip\" class=\"btn btn-primary waves-effect waves-light\"><i class=\"mdi mdi-plus\"></i></a>\n                                                                    <a href=\"javascript:void(0)\" id=\"remove_ip\" class=\"btn btn-danger waves-effect waves-light\"><i class=\"mdi mdi-close\"></i></a>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allowed_ips\">&nbsp;</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select class=\"form-control\" id=\"allowed_ips\" name=\"allowed_ips[]\" size=6 class=\"form-control\" multiple=\"multiple\">\n                                                                ";
    if (isset($rUser)) {
        foreach (json_decode($rUser["allowed_ips"], true) as $rIP) {
            echo "                                                                <option value=\"";
            echo $rIP;
            echo "\">";
            echo $rIP;
            echo "</option>\n                                                                ";
        }
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"ua_field\">";
    echo $_["allowed_user-agents"];
    echo "</label>\n                                                            <div class=\"col-md-8 input-group\">\n                                                                <input type=\"text\" id=\"ua_field\" class=\"form-control\" value=\"\">\n                                                                <div class=\"input-group-append\">\n                                                                    <a href=\"javascript:void(0)\" id=\"add_ua\" class=\"btn btn-primary waves-effect waves-light\"><i class=\"mdi mdi-plus\"></i></a>\n                                                                    <a href=\"javascript:void(0)\" id=\"remove_ua\" class=\"btn btn-danger waves-effect waves-light\"><i class=\"mdi mdi-close\"></i></a>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allowed_ua\">&nbsp;</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select class=\"form-control\" id=\"allowed_ua\" name=\"allowed_ua[]\" size=6 class=\"form-control\" multiple=\"multiple\">\n                                                                ";
    if (isset($rUser)) {
        foreach (json_decode($rUser["allowed_ua"], true) as $rUA) {
            echo "                                                                <option value=\"";
            echo $rUA;
            echo "\">";
            echo $rUA;
            echo "</option>\n                                                                ";
        }
    }
    echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["prev"];
    echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["next"];
    echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            ";
}
echo "                                            <div class=\"tab-pane\" id=\"review-purchase\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"alert alert-danger\" role=\"alert\" style=\"display:none;\" id=\"no-credits\">\n                                                            <i class=\"mdi mdi-block-helper mr-2\"></i> ";
echo $_["you_do_not_have_enough_credits"];
echo "                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <table class=\"table\" id=\"credits-cost\">\n                                                                <thead>\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
echo $_["select_bouquets_user"];
echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody>\n                                                                    <tr>\n                                                                    </tr>\n                                                                </tbody>\n                                                            </table>\n                                                            <div class=\"tab-pane\" id=\"bouquets\">\n                                                             <div class=\"row\">\n                                                                 <div class=\"col-12\">\n                                                                     <div class=\"form-group row mb-4\">\n                                                                     ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                                        <div class=\"col-md-6\">\n                                                                            <div class=\"custom-control custom-checkbox mt-1\">\n                                                                            <input type=\"checkbox\" class=\"custom-control-input bouquet-checkbox\" id=\"bouquet-";
    echo $rBouquet["id"];
    echo "\" name=\"bouquet[]\" value=\"";
    echo $rBouquet["id"];
    echo "\"";
    if (isset($rUser) && in_array($rBouquet["id"], json_decode($rUser["bouquet"], true))) {
        echo " checked";
    }
    echo ">\n                                                                            <label class=\"custom-control-label\" for=\"bouquet-";
    echo $rBouquet["id"];
    echo "\">";
    echo $rBouquet["bouquet_name"];
    echo "</label>\n                                                                            </div>\t\t\t   \n                                                                        </div>\n                                                                        ";
}
echo "                                                                     </div>\n                                                                 </div> <!-- end col -->\n                                                             </div> <!-- end row -->\n                                                 <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" onClick=\"selectAll()\" class=\"btn btn-secondary\">";
echo $_["select_all"];
echo "</a>\n                                                        <a href=\"javascript: void(0);\" onClick=\"selectNone()\" class=\"btn btn-secondary\">";
echo $_["deselect_all"];
echo "</a>\n                                                        <input name=\"submit_user\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rUser)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/pages/jquery.number.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\t\t<script src=\"assets/libs/jquery-ui/jquery-ui.min.js\"></script>\n\t\t<script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n\t\t<script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        \n        <script>\n        var swObjs = {};\n        \n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n\t\t\n\t\tfunction selectAll() {\n            \$(\".bouquet-checkbox\").each(function() {\n                \$(this).prop('checked', true);\n            });\n        }\n\n        function selectNone() {\n            \$(\".bouquet-checkbox\").each(function() {\n                \$(this).prop('checked', false);\n            });\n        }\n        \n        function isValidDate(dateString) {\n              var regEx = /^\\d{4}-\\d{2}-\\d{2}\$/;\n              if(!dateString.match(regEx)) return false;  // Invalid format\n              var d = new Date(dateString);\n              var dNum = d.getTime();\n              if(!dNum && dNum !== 0) return false; // NaN value, Invalid date\n              return d.toISOString().slice(0,10) === dateString;\n        }\n\t\tfunction isValidIP(rIP) {\n            if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\$/.test(rIP)) {\n                return true;\n            } else {\n                return false;\n            }\n        }\n\t\tfunction isValidMac_address(address) {\n            var regex = /^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})\$/;\n            if(regex.test(address)){\n                return true;\n            }\n            else {\n                return false;\n            }\n        }\n        function isValidLink(link){\n            regexp =  /^(?:(?:https?|ftp):\\/\\/)?(?:(?!(?:10|127)(?:\\.\\d{1,3}){3})(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))|(?:(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)(?:\\.(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)*(?:\\.(?:[a-z\\u00a1-\\uffff]{2,})))(?::\\d{2,5})?(?:\\/\\S*)?\$/;\n            if (regexp.test(link)){\n                return true;\n            }\n            else {\n                return false;\n            }\n\t\t\t\n\t\t\t\n\t\t}\n        function evaluateForm() {\n            if ((\$(\"#is_mag\").is(\":checked\")) || (\$(\"#is_e2\").is(\":checked\"))) {\n                if (\$(\"#is_mag\").is(\":checked\")) {\n                    \$(\"#mac_entry_mag\").show();\n                    \$(\"#uname\").hide()\n                    \$(\"#pass\").hide()\n                    window.swObjs[\"is_e2\"].disable();\n                } else {\n                    \$(\"#mac_entry_e2\").show();\n                    \$(\"#uname\").hide()\n                    \$(\"#pass\").hide()\n                    window.swObjs[\"is_mag\"].disable();\n                }\n            } else {\n                \$(\"#mac_entry_mag\").hide();\n                \$(\"#mac_entry_e2\").hide();\n                \$(\"#uname\").show()\n                \$(\"#pass\").show()\n                ";
if (!isset($rUser)) {
    echo "                window.swObjs[\"is_e2\"].enable();\n                window.swObjs[\"is_mag\"].enable();\n                ";
} else {
    echo "                window.swObjs[\"is_e2\"].disable();\n                window.swObjs[\"is_mag\"].disable();\n                ";
}
echo "            }\n        }\n        \n        \$(\"#package\").change(function() {\n            getPackage();\n        });\n        \n        function getPackage() {\n            var rTable = \$('#datatable-review').DataTable();\n            rTable.clear();\n            rTable.draw();\n            if (\$(\"#package\").val().length > 0) {\n                \$.getJSON(\"./api.php?action=get_package";
if (isset($_GET["trial"])) {
    echo "_trial";
}
echo "&package_id=\" + \$(\"#package\").val()";
if (isset($rUser)) {
    echo " + \"&user_id=" . $rUser["id"] . "\"";
}
echo ", function(rData) {\n                    if (rData.result === true) {\n                        \$(\"#max_connections\").val(rData.data.max_connections);\n                        \$(\"#cost_credits\").html(\$.number(rData.data.cost_credits, 2));\n                        \$(\"#remaining_credits\").html(\$.number(";
echo $rUserInfo["credits"];
echo " - rData.data.cost_credits, 2));\n                        \$(\"#exp_date\").val(rData.data.exp_date);\n                        if (";
echo $rUserInfo["credits"];
echo " - rData.data.cost_credits < 0) {\n                            \$(\"#credits-cost\").hide();\n                            \$(\"#no-credits\").show()\n                            \$(\".purchase\").prop('disabled', true);\n                        } else {\n                            \$(\"#credits-cost\").show();\n                            \$(\"#no-credits\").hide()\n                            \$(\".purchase\").prop('disabled', false);\n                        }\n                        ";
if (!$canGenerateTrials) {
    echo "                        // No trials left!\n                        \$(\".purchase\").prop('disabled', true);\n                        ";
}
if (!isset($rUser)) {
    echo "                        if (rData.data.can_gen_mag == 0) {\n                            window.swObjs[\"is_mag\"].disable();\n                            \$(\"#mac_entry_mag\").hide();\n                        }\n                        if (rData.data.can_gen_e2 == 0) {\n                            window.swObjs[\"is_e2\"].disable();\n                            \$(\"#mac_entry_e2\").hide();\n                        }\n                        ";
}
echo "                        \$(rData.bouquets).each(function(rIndex) {\n\t\t\t\t\t\t\trTable.row.add([rData.bouquets[rIndex].id, rData.bouquets[rIndex].bouquet_name, rData.bouquets[rIndex].bouquet_channels.length, rData.bouquets[rIndex].bouquet_series.length]);\n                        });\n                    }\n                    rTable.draw();\n                });\n            } else {\n                \$(\"#max_connections\").val(";
echo $rUser["max_connections"];
echo ");\n                \$(\"#cost_credits\").html(0);\n                \$(\"#remaining_credits\").html(\$.number(";
echo $rUserInfo["credits"];
echo ", 2));\n                \$(\"#exp_date\").val('";
echo date("Y-m-d", $rUser["exp_date"]);
echo "');\n                ";
if (!$canGenerateTrials) {
    echo "                \$(\".purchase\").prop('disabled', true);\n                ";
}
foreach (json_decode($rUser["bouquet"], true) as $rBouquetID) {
    $rBouquetData = getBouquet($rBouquetID);
    if (0 < strlen($rBouquetID)) {
        echo "\t\t\t\t\trTable.row.add([";
        echo $rBouquetID;
        echo ", '";
        echo $rBouquetData["bouquet_name"];
        echo "', ";
        echo count(json_decode($rBouquetData["bouquet_channels"], true));
        echo ", ";
        echo count(json_decode($rBouquetData["bouquet_series"], true));
        echo "]);\n\t\t\t\t\t";
    }
}
echo "                rTable.draw();\n            }\n        }\n        \n        \$(document).ready(function() {\n            \$('select.select2').select2({width: '100%'})\n            \$(\".js-switch\").each(function (index, element) {\n                var init = new Switchery(element);\n                window.swObjs[element.id] = init;\n            });\n            \$('#exp_date').daterangepicker({\n                singleDatePicker: true,\n                showDropdowns: true,\n                minDate: new Date(),\n                locale: {\n                    format: 'YYYY-MM-DD'\n                }\n            });\n            \n            \$(\"#no_expire\").change(function() {\n                if (\$(this).prop(\"checked\")) {\n                    \$(\"#exp_date\").prop(\"disabled\", true);\n                } else {\n                    \$(\"#exp_date\").removeAttr(\"disabled\");\n                }\n            });\n            \n            \$(\".js-switch\").on(\"change\" , function() {\n                evaluateForm();\n            });\n            \n            \$(\"#datatable-review\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,2,3]}\n                ],\n                responsive: false,\n                bInfo: false,\n                searching: false,\n                paging: false\n            });\n\t\t\t\$(\"#user_form\").submit(function(e){\n\t\t\t\tvar rBouquets = [];\n                \$(\"#bouquets\").find(\".custom-control-input:checked\").each(function(){\n                    rBouquets.push(parseInt(\$(this).val()));\n                });\n\t\t\t\tif(rBouquets.length < 1){\n                    \$(\"#bouquets\").find(\".custom-control-input\").each(function(){\n                        rBouquets.push(parseInt(\$(this).val()));\n                    });\n                }\n\t\t\t\t\n                \$(\"#bouquets_selected\").val(JSON.stringify(rBouquets));\n                \$(\"#allowed_ua option\").prop('selected', true);\n                \$(\"#allowed_ips option\").prop('selected', true);\n            });\n            \n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"#add_ip\").click(function() {\n                if ((\$(\"#ip_field\").val().length > 0) && (isValidIP(\$(\"#ip_field\").val()))) {\n                    var o = new Option(\$(\"#ip_field\").val(), \$(\"#ip_field\").val());\n                    \$(\"#allowed_ips\").append(o);\n                    \$(\"#ip_field\").val(\"\");\n                } else {\n                    \$.toast(\"";
echo $_["please_enter_a_valid_ip_address"];
echo "\");\n                }\n            });\n            \$(\"#remove_ip\").click(function() {\n                \$('#allowed_ips option:selected').remove();\n            });\n            \$(\"#add_ua\").click(function() {\n                if (\$(\"#ua_field\").val().length > 0) {\n                    var o = new Option(\$(\"#ua_field\").val(), \$(\"#ua_field\").val());\n                    \$(\"#allowed_ua\").append(o);\n                    \$(\"#ua_field\").val(\"\");\n                } else {\n                    \$.toast(\"";
echo $_["please_enter_a_user_agent"];
echo "\");\n                }\n            });\n            \$(\"#remove_ua\").click(function() {\n                \$('#allowed_ua option:selected').remove();\n            });\n            \$(\"#max_connections\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n            \n            evaluateForm();\n            getPackage();\n        });\n        </script>\n    </body>\n</html>";

?>