<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_user") && !hasPermissions("adv", "edit_user")) {
    exit;
}
if (isset($_POST["submit_user"])) {
    $_POST["mac_address_mag"] = strtoupper($_POST["mac_address_mag"]);
    $_POST["mac_address_e2"] = strtoupper($_POST["mac_address_e2"]);
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_user")) {
            exit;
        }
        $rArray = getUser($_POST["edit"]);
        if ($rArray["is_mag"] && !hasPermissions("adv", "edit_mag")) {
            exit;
        }
        if ($rArray["is_e2"] && !hasPermissions("adv", "edit_e2")) {
            exit;
        }
        unset($rArray["id"]);
    } else {
        if (!hasPermissions("adv", "add_user")) {
            exit;
        }
        $rArray = ["member_id" => 0, "username" => "", "password" => "", "exp_date" => NULL, "admin_enabled" => 1, "enabled" => 1, "admin_notes" => "", "reseller_notes" => "", "bouquet" => [], "max_connections" => 1, "is_restreamer" => 0, "allowed_ips" => [], "allowed_ua" => [], "created_at" => time(), "created_by" => -1, "is_mag" => 0, "is_e2" => 0, "force_server_id" => 0, "is_isplock" => 0, "isp_desc" => "", "forced_country" => "", "is_stalker" => 0, "bypass_ua" => 0, "play_token" => ""];
    }
    if (strlen($_POST["username"]) == 0) {
        $_POST["username"] = generateString(12);
    }
    if (strlen($_POST["password"]) == 0) {
        $_POST["password"] = generateString(12);
    }
    if (!isset($_POST["edit"])) {
        $result = $db->query("SELECT `id` FROM `users` WHERE `username` = '" . ESC($_POST["username"]) . "';");
        if ($result && 0 < $result->num_rows) {
            $_STATUS = 3;
        }
    }
    if ($_POST["is_mag"] && !filter_var($_POST["mac_address_mag"], FILTER_VALIDATE_MAC) || 0 < strlen($_POST["mac_address_e2"]) && !filter_var($_POST["mac_address_e2"], FILTER_VALIDATE_MAC)) {
        $_STATUS = 4;
    } else {
        if ($_POST["is_mag"]) {
            $result = $db->query("SELECT `user_id` FROM `mag_devices` WHERE mac = '" . ESC(base64_encode($_POST["mac_address_mag"])) . "' LIMIT 1;");
            if ($result && 0 < $result->num_rows) {
                if (isset($_POST["edit"])) {
                    if (intval($result->fetch_assoc()["user_id"]) != intval($_POST["edit"])) {
                        $_STATUS = 5;
                    }
                } else {
                    $_STATUS = 5;
                }
            }
        } else {
            if ($_POST["is_e2"]) {
                $result = $db->query("SELECT `user_id` FROM `enigma2_devices` WHERE mac = '" . ESC($_POST["mac_address_e2"]) . "' LIMIT 1;");
                if ($result && 0 < $result->num_rows) {
                    if (isset($_POST["edit"])) {
                        if (intval($result->fetch_assoc()["user_id"]) != intval($_POST["edit"])) {
                            $_STATUS = 5;
                        }
                    } else {
                        $_STATUS = 5;
                    }
                }
            }
        }
    }
    foreach (["max_connections", "enabled", "admin_enabled"] as $rSelection) {
        if (isset($_POST[$rSelection])) {
            $rArray[$rSelection] = intval($_POST[$rSelection]);
            unset($_POST[$rSelection]);
        } else {
            $rArray[$rSelection] = 1;
        }
    }
    foreach (["is_stalker", "is_e2", "is_mag", "is_restreamer", "is_trial"] as $rSelection) {
        if (isset($_POST[$rSelection])) {
            $rArray[$rSelection] = 1;
            unset($_POST[$rSelection]);
        } else {
            $rArray[$rSelection] = 0;
        }
    }
    $rArray["bouquet"] = sortArrayByArray(array_values(json_decode($_POST["bouquets_selected"], true)), array_keys(getBouquetOrder()));
    $rArray["bouquet"] = "[" . join(",", $rArray["bouquet"]) . "]";
    unset($_POST["bouquets_selected"]);
    if (isset($_POST["exp_date"]) && !isset($_POST["no_expire"])) {
        if (0 < strlen($_POST["exp_date"]) && $_POST["exp_date"] != "1970-01-01") {
            try {
                $rDate = new DateTime($_POST["exp_date"]);
                $rArray["exp_date"] = $rDate->format("U");
            } catch (Exception $e) {
                echo "Incorrect date.";
                $_STATUS = 1;
            }
        }
        unset($_POST["exp_date"]);
    } else {
        $rArray["exp_date"] = NULL;
    }
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
    if (isset($_POST["is_isplock"])) {
        $rArray["is_isplock"] = true;
        unset($_POST["is_isplock"]);
    } else {
        $rArray["is_isplock"] = false;
    }
    if (!isset($_STATUS)) {
        foreach ($_POST as $rKey => $rValue) {
            if (isset($rArray[$rKey])) {
                $rArray[$rKey] = $rValue;
            }
        }
        if (!$rArray["member_id"]) {
            $rArray["member_id"] = -1;
        }
        $rArray["created_by"] = $rArray["member_id"];
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
            $rCols = "`id`," . $rCols;
            $rValues = ESC($_POST["edit"]) . "," . $rValues;
        }
        $rQuery = "REPLACE INTO `users`(" . $rCols . ") VALUES(" . $rValues . ");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
            } else {
                $rInsertID = $db->insert_id;
            }
            if (isset($rInsertID) && isset($_POST["access_output"])) {
                $db->query("DELETE FROM `user_output` WHERE `user_id` = " . intval($rInsertID) . ";");
                foreach ($_POST["access_output"] as $rOutputID) {
                    $db->query("INSERT INTO `user_output`(`user_id`, `access_output_id`) VALUES(" . intval($rInsertID) . ", " . intval($rOutputID) . ");");
                }
                if ($rArray["is_mag"] == 1) {
                    if (hasPermissions("adv", "add_mag")) {
                        if (isset($_POST["lock_device"])) {
                            $rSTBLock = 1;
                        } else {
                            $rSTBLock = 0;
                        }
                        $result = $db->query("SELECT `mag_id` FROM `mag_devices` WHERE `user_id` = " . intval($rInsertID) . " LIMIT 1;");
                        if (isset($result) && $result->num_rows == 1) {
                            $db->query("UPDATE `mag_devices` SET `mac` = '" . base64_encode(ESC($_POST["mac_address_mag"])) . "', `lock_device` = " . intval($rSTBLock) . " WHERE `user_id` = " . intval($rInsertID) . ";");
                        } else {
                            $db->query("INSERT INTO `mag_devices`(`user_id`, `mac`, `lock_device`) VALUES(" . intval($rInsertID) . ", '" . ESC(base64_encode($_POST["mac_address_mag"])) . "', " . intval($rSTBLock) . ");");
                        }
                        if (isset($_POST["edit"])) {
                            $db->query("DELETE FROM `enigma2_devices` WHERE `user_id` = " . intval($rInsertID) . ";");
                        }
                    }
                } else {
                    if ($rArray["is_e2"] == 1) {
                        if (hasPermissions("adv", "add_e2")) {
                            $result = $db->query("SELECT `device_id` FROM `enigma2_devices` WHERE `user_id` = " . intval($rInsertID) . " LIMIT 1;");
                            if (isset($result) && $result->num_rows == 1) {
                                $db->query("UPDATE `enigma2_devices` SET `mac` = '" . ESC($_POST["mac_address_e2"]) . "' WHERE `user_id` = " . intval($rInsertID) . ";");
                            } else {
                                $db->query("INSERT INTO `enigma2_devices`(`user_id`, `mac`) VALUES(" . intval($rInsertID) . ", '" . ESC($_POST["mac_address_e2"]) . "');");
                            }
                            if (isset($_POST["edit"])) {
                                $db->query("DELETE FROM `mag_devices` WHERE `user_id` = " . intval($rInsertID) . ";");
                            }
                        }
                    } else {
                        if (isset($_POST["edit"])) {
                            $db->query("DELETE FROM `mag_devices` WHERE `user_id` = " . intval($rInsertID) . ";");
                            $db->query("DELETE FROM `enigma2_devices` WHERE `user_id` = " . intval($rInsertID) . ";");
                        }
                    }
                }
            }
            header("Location: ./user.php?successedit&id=" . $rInsertID);
        } else {
            $_STATUS = 2;
        }
    }
}
if (isset($_GET["id"])) {
    $rUser = getUser($_GET["id"]);
    if (!$rUser || !hasPermissions("adv", "edit_user")) {
        exit;
    }
    if ($rUser["is_mag"] && !hasPermissions("adv", "edit_mag")) {
        exit;
    }
    if ($rUser["is_e2"] && !hasPermissions("adv", "edit_e2")) {
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
} else {
    if (!hasPermissions("adv", "add_user")) {
        exit;
    }
}
$rRegisteredUsers = getRegisteredUsers();
$rCountries = [["id" => "", "name" => "Off"], ["id" => "ALL", "name" => "ALL Countries"], ["id" => "A1", "name" => "Anonymous Proxy"], ["id" => "A2", "name" => "Satellite Provider"], ["id" => "O1", "name" => "Other Country"], ["id" => "AF", "name" => "Afghanistan"], ["id" => "AX", "name" => "Aland Islands"], ["id" => "AL", "name" => "Albania"], ["id" => "DZ", "name" => "Algeria"], ["id" => "AS", "name" => "American Samoa"], ["id" => "AD", "name" => "Andorra"], ["id" => "AO", "name" => "Angola"], ["id" => "AI", "name" => "Anguilla"], ["id" => "AQ", "name" => "Antarctica"], ["id" => "AG", "name" => "Antigua And Barbuda"], ["id" => "AR", "name" => "Argentina"], ["id" => "AM", "name" => "Armenia"], ["id" => "AW", "name" => "Aruba"], ["id" => "AU", "name" => "Australia"], ["id" => "AT", "name" => "Austria"], ["id" => "AZ", "name" => "Azerbaijan"], ["id" => "BS", "name" => "Bahamas"], ["id" => "BH", "name" => "Bahrain"], ["id" => "BD", "name" => "Bangladesh"], ["id" => "BB", "name" => "Barbados"], ["id" => "BY", "name" => "Belarus"], ["id" => "BE", "name" => "Belgium"], ["id" => "BZ", "name" => "Belize"], ["id" => "BJ", "name" => "Benin"], ["id" => "BM", "name" => "Bermuda"], ["id" => "BT", "name" => "Bhutan"], ["id" => "BO", "name" => "Bolivia"], ["id" => "BA", "name" => "Bosnia And Herzegovina"], ["id" => "BW", "name" => "Botswana"], ["id" => "BV", "name" => "Bouvet Island"], ["id" => "BR", "name" => "Brazil"], ["id" => "IO", "name" => "British Indian Ocean Territory"], ["id" => "BN", "name" => "Brunei Darussalam"], ["id" => "BG", "name" => "Bulgaria"], ["id" => "BF", "name" => "Burkina Faso"], ["id" => "BI", "name" => "Burundi"], ["id" => "KH", "name" => "Cambodia"], ["id" => "CM", "name" => "Cameroon"], ["id" => "CA", "name" => "Canada"], ["id" => "CV", "name" => "Cape Verde"], ["id" => "KY", "name" => "Cayman Islands"], ["id" => "CF", "name" => "Central African Republic"], ["id" => "TD", "name" => "Chad"], ["id" => "CL", "name" => "Chile"], ["id" => "CN", "name" => "China"], ["id" => "CX", "name" => "Christmas Island"], ["id" => "CC", "name" => "Cocos (Keeling) Islands"], ["id" => "CO", "name" => "Colombia"], ["id" => "KM", "name" => "Comoros"], ["id" => "CG", "name" => "Congo"], ["id" => "CD", "name" => "Congo, Democratic Republic"], ["id" => "CK", "name" => "Cook Islands"], ["id" => "CR", "name" => "Costa Rica"], ["id" => "CI", "name" => "Cote D'Ivoire"], ["id" => "HR", "name" => "Croatia"], ["id" => "CU", "name" => "Cuba"], ["id" => "CY", "name" => "Cyprus"], ["id" => "CZ", "name" => "Czech Republic"], ["id" => "DK", "name" => "Denmark"], ["id" => "DJ", "name" => "Djibouti"], ["id" => "DM", "name" => "Dominica"], ["id" => "DO", "name" => "Dominican Republic"], ["id" => "EC", "name" => "Ecuador"], ["id" => "EG", "name" => "Egypt"], ["id" => "SV", "name" => "El Salvador"], ["id" => "GQ", "name" => "Equatorial Guinea"], ["id" => "ER", "name" => "Eritrea"], ["id" => "EE", "name" => "Estonia"], ["id" => "ET", "name" => "Ethiopia"], ["id" => "FK", "name" => "Falkland Islands (Malvinas)"], ["id" => "FO", "name" => "Faroe Islands"], ["id" => "FJ", "name" => "Fiji"], ["id" => "FI", "name" => "Finland"], ["id" => "FR", "name" => "France"], ["id" => "GF", "name" => "French Guiana"], ["id" => "PF", "name" => "French Polynesia"], ["id" => "TF", "name" => "French Southern Territories"], ["id" => "MK", "name" => "Fyrom"], ["id" => "GA", "name" => "Gabon"], ["id" => "GM", "name" => "Gambia"], ["id" => "GE", "name" => "Georgia"], ["id" => "DE", "name" => "Germany"], ["id" => "GH", "name" => "Ghana"], ["id" => "GI", "name" => "Gibraltar"], ["id" => "GR", "name" => "Greece"], ["id" => "GL", "name" => "Greenland"], ["id" => "GD", "name" => "Grenada"], ["id" => "GP", "name" => "Guadeloupe"], ["id" => "GU", "name" => "Guam"], ["id" => "GT", "name" => "Guatemala"], ["id" => "GG", "name" => "Guernsey"], ["id" => "GN", "name" => "Guinea"], ["id" => "GW", "name" => "Guinea-Bissau"], ["id" => "GY", "name" => "Guyana"], ["id" => "HT", "name" => "Haiti"], ["id" => "HM", "name" => "Heard Island & Mcdonald Islands"], ["id" => "VA", "name" => "Holy See (Vatican City State)"], ["id" => "HN", "name" => "Honduras"], ["id" => "HK", "name" => "Hong Kong"], ["id" => "HU", "name" => "Hungary"], ["id" => "IS", "name" => "Iceland"], ["id" => "IN", "name" => "India"], ["id" => "ID", "name" => "Indonesia"], ["id" => "IR", "name" => "Iran, Islamic Republic Of"], ["id" => "IQ", "name" => "Iraq"], ["id" => "IE", "name" => "Ireland"], ["id" => "IM", "name" => "Isle Of Man"], ["id" => "IL", "name" => "Israel"], ["id" => "IT", "name" => "Italy"], ["id" => "JM", "name" => "Jamaica"], ["id" => "JP", "name" => "Japan"], ["id" => "JE", "name" => "Jersey"], ["id" => "JO", "name" => "Jordan"], ["id" => "KZ", "name" => "Kazakhstan"], ["id" => "KE", "name" => "Kenya"], ["id" => "KI", "name" => "Kiribati"], ["id" => "KR", "name" => "Korea"], ["id" => "KW", "name" => "Kuwait"], ["id" => "KG", "name" => "Kyrgyzstan"], ["id" => "LA", "name" => "Lao People's Democratic Republic"], ["id" => "LV", "name" => "Latvia"], ["id" => "LB", "name" => "Lebanon"], ["id" => "LS", "name" => "Lesotho"], ["id" => "LR", "name" => "Liberia"], ["id" => "LY", "name" => "Libyan Arab Jamahiriya"], ["id" => "LI", "name" => "Liechtenstein"], ["id" => "LT", "name" => "Lithuania"], ["id" => "LU", "name" => "Luxembourg"], ["id" => "MO", "name" => "Macao"], ["id" => "MG", "name" => "Madagascar"], ["id" => "MW", "name" => "Malawi"], ["id" => "MY", "name" => "Malaysia"], ["id" => "MV", "name" => "Maldives"], ["id" => "ML", "name" => "Mali"], ["id" => "MT", "name" => "Malta"], ["id" => "MH", "name" => "Marshall Islands"], ["id" => "MQ", "name" => "Martinique"], ["id" => "MR", "name" => "Mauritania"], ["id" => "MU", "name" => "Mauritius"], ["id" => "YT", "name" => "Mayotte"], ["id" => "MX", "name" => "Mexico"], ["id" => "FM", "name" => "Micronesia, Federated States Of"], ["id" => "MD", "name" => "Moldova"], ["id" => "MC", "name" => "Monaco"], ["id" => "MN", "name" => "Mongolia"], ["id" => "ME", "name" => "Montenegro"], ["id" => "MS", "name" => "Montserrat"], ["id" => "MA", "name" => "Morocco"], ["id" => "MZ", "name" => "Mozambique"], ["id" => "MM", "name" => "Myanmar"], ["id" => "NA", "name" => "Namibia"], ["id" => "NR", "name" => "Nauru"], ["id" => "NP", "name" => "Nepal"], ["id" => "NL", "name" => "Netherlands"], ["id" => "AN", "name" => "Netherlands Antilles"], ["id" => "NC", "name" => "New Caledonia"], ["id" => "NZ", "name" => "New Zealand"], ["id" => "NI", "name" => "Nicaragua"], ["id" => "NE", "name" => "Niger"], ["id" => "NG", "name" => "Nigeria"], ["id" => "NU", "name" => "Niue"], ["id" => "NF", "name" => "Norfolk Island"], ["id" => "MP", "name" => "Northern Mariana Islands"], ["id" => "NO", "name" => "Norway"], ["id" => "OM", "name" => "Oman"], ["id" => "PK", "name" => "Pakistan"], ["id" => "PW", "name" => "Palau"], ["id" => "PS", "name" => "Palestinian Territory, Occupied"], ["id" => "PA", "name" => "Panama"], ["id" => "PG", "name" => "Papua New Guinea"], ["id" => "PY", "name" => "Paraguay"], ["id" => "PE", "name" => "Peru"], ["id" => "PH", "name" => "Philippines"], ["id" => "PN", "name" => "Pitcairn"], ["id" => "PL", "name" => "Poland"], ["id" => "PT", "name" => "Portugal"], ["id" => "PR", "name" => "Puerto Rico"], ["id" => "QA", "name" => "Qatar"], ["id" => "RE", "name" => "Reunion"], ["id" => "RO", "name" => "Romania"], ["id" => "RU", "name" => "Russian Federation"], ["id" => "RW", "name" => "Rwanda"], ["id" => "BL", "name" => "Saint Barthelemy"], ["id" => "SH", "name" => "Saint Helena"], ["id" => "KN", "name" => "Saint Kitts And Nevis"], ["id" => "LC", "name" => "Saint Lucia"], ["id" => "MF", "name" => "Saint Martin"], ["id" => "PM", "name" => "Saint Pierre And Miquelon"], ["id" => "VC", "name" => "Saint Vincent And Grenadines"], ["id" => "WS", "name" => "Samoa"], ["id" => "SM", "name" => "San Marino"], ["id" => "ST", "name" => "Sao Tome And Principe"], ["id" => "SA", "name" => "Saudi Arabia"], ["id" => "SN", "name" => "Senegal"], ["id" => "RS", "name" => "Serbia"], ["id" => "SC", "name" => "Seychelles"], ["id" => "SL", "name" => "Sierra Leone"], ["id" => "SG", "name" => "Singapore"], ["id" => "SK", "name" => "Slovakia"], ["id" => "SI", "name" => "Slovenia"], ["id" => "SB", "name" => "Solomon Islands"], ["id" => "SO", "name" => "Somalia"], ["id" => "ZA", "name" => "South Africa"], ["id" => "GS", "name" => "South Georgia And Sandwich Isl."], ["id" => "ES", "name" => "Spain"], ["id" => "LK", "name" => "Sri Lanka"], ["id" => "SD", "name" => "Sudan"], ["id" => "SR", "name" => "Suriname"], ["id" => "SJ", "name" => "Svalbard And Jan Mayen"], ["id" => "SZ", "name" => "Swaziland"], ["id" => "SE", "name" => "Sweden"], ["id" => "CH", "name" => "Switzerland"], ["id" => "SY", "name" => "Syrian Arab Republic"], ["id" => "TW", "name" => "Taiwan"], ["id" => "TJ", "name" => "Tajikistan"], ["id" => "TZ", "name" => "Tanzania"], ["id" => "TH", "name" => "Thailand"], ["id" => "TL", "name" => "Timor-Leste"], ["id" => "TG", "name" => "Togo"], ["id" => "TK", "name" => "Tokelau"], ["id" => "TO", "name" => "Tonga"], ["id" => "TT", "name" => "Trinidad And Tobago"], ["id" => "TN", "name" => "Tunisia"], ["id" => "TR", "name" => "Turkey"], ["id" => "TM", "name" => "Turkmenistan"], ["id" => "TC", "name" => "Turks And Caicos Islands"], ["id" => "TV", "name" => "Tuvalu"], ["id" => "UG", "name" => "Uganda"], ["id" => "UA", "name" => "Ukraine"], ["id" => "AE", "name" => "United Arab Emirates"], ["id" => "GB", "name" => "United Kingdom"], ["id" => "US", "name" => "United States"], ["id" => "UM", "name" => "United States Outlying Islands"], ["id" => "UY", "name" => "Uruguay"], ["id" => "UZ", "name" => "Uzbekistan"], ["id" => "VU", "name" => "Vanuatu"], ["id" => "VE", "name" => "Venezuela"], ["id" => "VN", "name" => "Viet Nam"], ["id" => "VG", "name" => "Virgin Islands, British"], ["id" => "VI", "name" => "Virgin Islands, U.S."], ["id" => "WF", "name" => "Wallis And Futuna"], ["id" => "EH", "name" => "Western Sahara"], ["id" => "YE", "name" => "Yemen"], ["id" => "ZM", "name" => "Zambia"], ["id" => "ZW", "name" => "Zimbabwe"]];
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t    <li>\n                                        <a href=\"./users.php";
if (isset($_GET["mag"])) {
    echo "?mag";
} else {
    if (isset($_GET["e2"])) {
        echo "?e2";
    }
}
echo "\">\n\t\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_users"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\n                                        <a href=\"./mags.php";
if (isset($_GET["mag"])) {
    echo "?mag";
} else {
    if (isset($_GET["e2"])) {
        echo "?e2";
    }
}
echo "\">\n\t\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> Back to Mags</button>\n\t\t\t\t\t\t\t\t\t    </a>\n\t\t\t\t\t\t\t\t\t</li>\t\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rUser)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
echo $_["user"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS)) {
    if ($_STATUS == 0) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["user_operation_was_completed_successfully"];
            echo "                        </div>\n\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["user_operation_was_completed_successfully"];
            echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
        }
    } else {
        if ($_STATUS == 1) {
            if (!$rSettings["sucessedit"]) {
                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                echo $_["an_incorrect_expiration_date_was_entered"];
                echo "                        </div>\n\t\t\t\t\t\t";
            } else {
                echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                echo $_["an_incorrect_expiration_date_was_entered"];
                echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
            }
        } else {
            if ($_STATUS == 2) {
                if (!$rSettings["sucessedit"]) {
                    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                    echo $_["generic_fail"];
                    echo "                        </div>\n\t\t\t\t\t\t";
                } else {
                    echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                    echo $_["generic_fail"];
                    echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
                }
            } else {
                if ($_STATUS == 3) {
                    if (!$rSettings["sucessedit"]) {
                        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                        echo $_["this_username_already_exists"];
                        echo "                        </div>\n\t\t\t\t\t\t";
                    } else {
                        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                        echo $_["this_username_already_exists"];
                        echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
                    }
                } else {
                    if ($_STATUS == 4) {
                        if (!$rSettings["sucessedit"]) {
                            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                            echo $_["an_invalid_mac_address_was_entered"];
                            echo "                        </div>\n\t\t\t\t\t\t";
                        } else {
                            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                            echo $_["an_invalid_mac_address_was_entered"];
                            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
                        }
                    } else {
                        if ($_STATUS == 5) {
                            if (!$rSettings["sucessedit"]) {
                                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                                echo $_["this_mac_address_is_already_in_use"];
                                echo "                        </div>\n\t\t\t\t\t\t";
                            } else {
                                echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                                echo $_["this_mac_address_is_already_in_use"];
                                echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
                            }
                        }
                    }
                }
            }
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./user.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"user_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rUser)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rUser["id"];
    echo "\" />\n                                    <input type=\"hidden\" name=\"admin_enabled\" value=\"";
    echo $rUser["admin_enabled"];
    echo "\" />\n                                    <input type=\"hidden\" name=\"enabled\" value=\"";
    echo $rUser["enabled"];
    echo "\" />\n                                    ";
}
echo "                                    <input type=\"hidden\" name=\"bouquets_selected\" id=\"bouquets_selected\" value=\"\" />\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#user-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#advanced-options\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-folder-alert-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["advanced"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#restrictions\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-hazard-lights mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["restrictions"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#bouquets\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-flower-tulip mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["bouquets"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"user-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"username\">";
echo $_["username"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["username"]);
}
echo "\">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\t\n                                                            <label class=\"col-md-4 col-form-label\" for=\"password\">";
echo $_["password"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"password\" name=\"password\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["password"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_e2\">";
echo $_["enigma_device"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["this_option_will_be_selected_enigma"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input ";
if (!hasPermissions("adv", "add_e2")) {
    echo "disabled ";
}
echo "name=\"is_e2\" id=\"is_e2\" type=\"checkbox\" ";
if (isset($rUser)) {
    if ($rUser["is_e2"] == 1) {
        echo "checked ";
    }
} else {
    if (isset($_GET["e2"]) && hasPermissions("adv", "add_e2")) {
        echo "checked ";
    }
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_mag\">";
echo $_["mag_device"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["this_option_will_be_selected_mag"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input ";
if (!hasPermissions("adv", "add_mag")) {
    echo "disabled ";
}
echo "name=\"is_mag\" id=\"is_mag\" type=\"checkbox\" ";
if (isset($rUser)) {
    if ($rUser["is_mag"] == 1) {
        echo "checked ";
    }
} else {
    if (isset($_GET["mag"]) && hasPermissions("adv", "add_mag")) {
        echo "checked ";
    }
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\" style=\"display:none\" id=\"mac_entry_mag\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"mac_address_mag\">";
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
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"member_id\">";
echo $_["owner"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"member_id\" id=\"member_id\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    <option value=\"-1\">";
echo $_["no_owner"];
echo "</option>\n                                                                    ";
foreach ($rRegisteredUsers as $rRegisteredUser) {
    echo "                                                                    <option ";
    if (isset($rUser)) {
        if (intval($rUser["member_id"]) == intval($rRegisteredUser["id"])) {
            echo "selected ";
        }
    } else {
        if (intval($rUserInfo["id"]) == intval($rRegisteredUser["id"])) {
            echo "selected ";
        }
    }
    echo "value=\"";
    echo $rRegisteredUser["id"];
    echo "\">";
    echo $rRegisteredUser["username"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\" style=\"display:none\" id=\"info3\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"parent_password\">Adult Pin</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"parent_password\" name=\"parent_password\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rMAGUser)) {
    echo htmlspecialchars($rMAGUser["parent_password"]);
}
echo "\" disabled>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"lock_device\">";
echo $_["mag_stb_lock"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"lock_device\" id=\"lock_device\" type=\"checkbox\" ";
if (isset($rUser) && $rUser["lock_device"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"max_connections\">";
echo $_["max_connections"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"max_connections\" name=\"max_connections\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["max_connections"]);
} else {
    echo "1";
}
echo "\" required data-parsley-trigger=\"";
echo $_["change"];
echo "\">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"is_isplock\">ISP Lock</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_isplock\" id=\"is_isplock\" type=\"checkbox\" ";
if (isset($rUser) && $rUser["is_isplock"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"created_at\">Created</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"created_at\" name=\"created_at\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rUser)) {
    echo date("Y-m-d H:i", $rUser["created_at"]);
}
echo "\" disabled>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-2 col-form-label\" for=\"exp_date\">";
echo $_["expiry"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["leave_blank_for_unlimited"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\" style=\"padding-right: 0px; padding-left: 0px;\">\n                                                                <input type=\"text\" style=\"padding-right: 1px; padding-left: 1px;\" class=\"form-control text-center datetime\" id=\"exp_date\" name=\"exp_date\" value=\"";
if (isset($rUser)) {
    if (!is_null($rUser["exp_date"])) {
        echo date("Y-m-d HH:mm", $rUser["exp_date"]);
    } else {
        echo "disabled";
    }
}
echo "\" data-toggle=\"date-picker\" data-single-date-picker=\"true\">\n                                                            </div>\n                                                            <div class=\"col-md-2\">\n                                                                <div class=\"custom-control custom-checkbox mt-1\">\n                                                                    <input type=\"checkbox\" class=\"custom-control-input\" id=\"no_expire\" name=\"no_expire\"";
if (isset($rUser) && is_null($rUser["exp_date"])) {
    echo " checked";
}
echo ">\n                                                                    <label class=\"custom-control-label\" for=\"no_expire\">";
echo $_["never"];
echo "</label>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"admin_notes\">";
echo $_["admin_notes"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <textarea id=\"admin_notes\" name=\"admin_notes\" class=\"form-control\" rows=\"3\" placeholder=\"\">";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["admin_notes"]);
}
echo "</textarea>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller_notes\">";
echo $_["reseller_notes"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <textarea id=\"reseller_notes\" name=\"reseller_notes\" class=\"form-control\" rows=\"3\" placeholder=\"\">";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["reseller_notes"]);
}
echo "</textarea>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
if ($rPermissions["is_reseller"] && $rPermissions["allow_download"] || $rPermissions["is_admin"]) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller_notes\">Download Playlist</label>\n                                                            <div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t     <div class=\"playlist\" role=\"dialog\" aria-labelledby=\"downloadLabel\" aria-hidden=\"true\" data-username=\"";
    if (isset($rUser)) {
        echo $rUser["username"];
    }
    echo "\" data-password=\"";
    if (isset($rUser)) {
        echo $rUser["password"];
    }
    echo "\">\n                                                                    <select id=\"download_type\" class=\"form-control\" data-toggle=\"select2\">\n                                                                        <option value=\"\">";
    echo $_["select_an_ouput_format"];
    echo " </option>\n                                                                        ";
    $result = $db->query("SELECT * FROM `devices` ORDER BY `device_id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            if ($row["copy_text"]) {
                echo "<optgroup label=\"" . $row["device_name"] . "\"><option data-text=\"" . str_replace("\"", "\\\"", $row["copy_text"]) . "\" value=\"type=" . $row["device_key"] . "&amp;output=hls\">" . $row["device_name"] . " - HLS </option><option data-text=\"" . str_replace("\"", "\\\"", $row["copy_text"]) . "\" value=\"type=" . $row["device_key"] . "&amp;output=mpegts\">" . $row["device_name"] . " - MPEGTS</option></optgroup>";
            } else {
                echo "<optgroup label=\"" . $row["device_name"] . "\"><option value=\"type=" . $row["device_key"] . "&amp;output=hls\">" . $row["device_name"] . " - HLS </option><option value=\"type=" . $row["device_key"] . "&amp;output=mpegts\">" . $row["device_name"] . " - MPEGTS</option></optgroup>";
            }
        }
    }
    echo "                                                                    </select>\n                                                                </div>\n                                                                <div class=\"col-m-8\" style=\"margin-top:10px;\">\n                                                                    <div class=\"input-group\">\n                                                                        <input type=\"text\" class=\"form-control\" id=\"download_url\" value=\"\">\n                                                                        <div class=\"input-group-append\">\n                                                                            <button class=\"btn btn-warning waves-effect waves-light\" type=\"button\" onClick=\"copyDownload();\"><i class=\"mdi mdi-content-copy\"></i></button>\n                                                                            <button class=\"btn btn-success waves-effect waves-light\" type=\"button\" onClick=\"doDownload();\" id=\"download_button\" disabled><i class=\"mdi mdi-download\"></i></button>\n                                                                        </div>\n                                                                    </div>\n                                                                </div><!-- /.modal -->\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"advanced-options\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"force_server_id\">";
echo $_["forced_connection"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["force_this_user_to_connect_to"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"force_server_id\" id=\"force_server_id\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    <option ";
if (isset($rUser) && intval($rUser["force_server_id"]) == 0) {
    echo "selected ";
}
echo "value=\"0\">";
echo $_["disabled"];
echo "</option>\n                                                                    ";
foreach ($rServers as $rServer) {
    echo "                                                                    <option ";
    if (isset($rUser) && intval($rUser["force_server_id"]) == intval($rServer["id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rServer["id"];
    echo "\">";
    echo htmlspecialchars($rServer["server_name"]);
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_stalker\">";
echo $_["ministra_portal"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["select_this_option"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_stalker\" id=\"is_stalker\" type=\"checkbox\" ";
if (isset($rUser) && $rUser["is_stalker"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"is_restreamer\">";
echo $_["restreamer"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["if_selected_this_user"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_restreamer\" id=\"is_restreamer\" type=\"checkbox\" ";
if (isset($rUser) && $rUser["is_restreamer"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"is_trial\">";
echo $_["trial_account"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"is_trial\" id=\"is_trial\" type=\"checkbox\" ";
if (isset($rUser) && $rUser["is_trial"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"forced_country\">";
echo $_["forced_country"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["force_user_to_connect"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"forced_country\" id=\"forced_country\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
foreach ($rCountries as $rCountry) {
    echo "                                                                    <option ";
    if (isset($rUser) && $rUser["forced_country"] == $rCountry["id"]) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rCountry["id"];
    echo "\">";
    echo $rCountry["name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\" style=\"display:none\" id=\"info\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"stb_type\">STB Type</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"stb_type\" name=\"stb_type\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rMAGUser)) {
    echo htmlspecialchars($rMAGUser["stb_type"]);
}
echo "\" disabled>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"sn\">Serial Number</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"sn\" name=\"sn\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rMAGUser)) {
    echo htmlspecialchars($rMAGUser["sn"]);
}
echo "\" disabled>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\" style=\"display:none\" id=\"info2\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"image_version\">Image Version</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"image_version\" name=\"image_version\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rMAGUser)) {
    echo htmlspecialchars($rMAGUser["image_version"]);
}
echo "\" disabled>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"hw_version\">HW Version</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"hw_version\" name=\"hw_version\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rMAGUser)) {
    echo htmlspecialchars($rMAGUser["hw_version"]);
}
echo "\" disabled>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\" style=\"display:none\" id=\"info4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"device_id\">Primary Device</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"device_id\" name=\"device_id\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rMAGUser)) {
    echo htmlspecialchars($rMAGUser["device_id"]);
}
echo "\" disabled>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\" style=\"display:none\" id=\"info5\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"device_id2\">Secondary Device</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"device_id2\" name=\"device_id2\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rMAGUser)) {
    echo htmlspecialchars($rMAGUser["device_id2"]);
}
echo "\" disabled>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\" style=\"display:none\" id=\"info6\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"ver\">Version</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"ver\" name=\"ver\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rMAGUser)) {
    echo htmlspecialchars($rMAGUser["ver"]);
}
echo "\" disabled>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"isp_desc\">ISP Lock Info</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"isp_desc\" name=\"isp_desc\" placeholder=\"";
echo $_["auto_generate_if_blank"];
echo "\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["isp_desc"]);
}
echo "\" disabled>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"access_output\">";
echo $_["access_output"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                ";
foreach (getOutputs() as $rOutput) {
    echo "                                                                <div class=\"checkbox form-check-inline\">\n                                                                    <input data-size=\"large\" type=\"checkbox\" id=\"access_output_";
    echo $rOutput["access_output_id"];
    echo "\" name=\"access_output[]\" value=\"";
    echo $rOutput["access_output_id"];
    echo "\"";
    if (isset($rUser)) {
        if (in_array($rOutput["access_output_id"], $rUser["outputs"])) {
            echo " checked";
        }
    } else {
        echo " checked";
    }
    echo ">\n                                                                    <label for=\"access_output_";
    echo $rOutput["access_output_id"];
    echo "\"> ";
    echo $rOutput["output_name"];
    echo " </label>\n                                                                </div>\n                                                                ";
}
echo "                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"restrictions\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"ip_field\">";
echo $_["allowed_ip_addresses"];
echo "</label>\n                                                            <div class=\"col-md-8 input-group\">\n                                                                <input type=\"text\" id=\"ip_field\" class=\"form-control\" value=\"\">\n                                                                <div class=\"input-group-append\">\n                                                                    <a href=\"javascript:void(0)\" id=\"add_ip\" class=\"btn btn-primary waves-effect waves-light\"><i class=\"mdi mdi-plus\"></i></a>\n                                                                    <a href=\"javascript:void(0)\" id=\"remove_ip\" class=\"btn btn-danger waves-effect waves-light\"><i class=\"mdi mdi-close\"></i></a>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allowed_ips\">&nbsp;</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"allowed_ips\" name=\"allowed_ips[]\" size=6 class=\"form-control\" multiple=\"multiple\">\n                                                                ";
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
echo "</label>\n                                                            <div class=\"col-md-8 input-group\">\n                                                                <input type=\"text\" id=\"ua_field\" class=\"form-control\" value=\"\">\n                                                                <div class=\"input-group-append\">\n                                                                    <a href=\"javascript:void(0)\" id=\"add_ua\" class=\"btn btn-primary waves-effect waves-light\"><i class=\"mdi mdi-plus\"></i></a>\n                                                                    <a href=\"javascript:void(0)\" id=\"remove_ua\" class=\"btn btn-danger waves-effect waves-light\"><i class=\"mdi mdi-close\"></i></a>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"allowed_ua\">&nbsp;</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"allowed_ua\" name=\"allowed_ua[]\" size=6 class=\"form-control\" multiple=\"multiple\">\n                                                                ";
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
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"bouquets\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <table id=\"datatable-bouquets\" class=\"table table-borderless mb-0\">\n                                                                <thead class=\"bg-light\">\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                        <th>";
echo $_["bouquet_name"];
echo "</th>\n                                                                        <th class=\"text-center\">";
echo $_["streams"];
echo "</th>\n                                                                        <th class=\"text-center\">";
echo $_["series"];
echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody>\n                                                                    ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                                    <tr";
    if (isset($rUser) && in_array($rBouquet["id"], json_decode($rUser["bouquet"], true))) {
        echo " class='selected selectedfilter ui-selected'";
    }
    echo ">\n                                                                        <td class=\"text-center\">";
    echo $rBouquet["id"];
    echo "</td>\n                                                                        <td>";
    echo $rBouquet["bouquet_name"];
    echo "</td>\n                                                                        <td class=\"text-center\">";
    echo count(json_decode($rBouquet["bouquet_channels"], true));
    echo "</td>\n                                                                        <td class=\"text-center\">";
    echo count(json_decode($rBouquet["bouquet_series"], true));
    echo "</td>\n                                                                    </tr>\n                                                                    ";
}
echo "                                                                </tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" onClick=\"toggleBouquets()\" class=\"btn btn-info\">";
echo $_["toggle_bouquets"];
echo "</a>\n                                                        <input name=\"submit_user\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rUser)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n\t\t<script src=\"assets/libs/jquery-ui/jquery-ui.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n\t\t<script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\t\t<style>\n            .daterangepicker select.ampmselect,.daterangepicker select.hourselect,.daterangepicker select.minuteselect,.daterangepicker select.secondselect{\n                background:#fff;\n                border:1px solid #fff;\n                color:rgb(0, 0, 0)\n            }\n        </style>\n\n        \n\t\t<script>\n        var swObjs = {};\n        ";
if (isset($rUser)) {
    echo "        var rBouquets = ";
    echo $rUser["bouquet"];
    echo ";\n        ";
} else {
    echo "        var rBouquets = [];\n        ";
}
echo "        \n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        function toggleBouquets() {\n            \$(\"#datatable-bouquets tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rBouquets.splice(parseInt(\$.inArray(\$(this).find(\"td:eq(0)\").html()), window.rBouquets), 1);\n                    }\n                } else {            \n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rBouquets.push(parseInt(\$(this).find(\"td:eq(0)\").html()));\n                    }\n                }\n            });\n        }\n        function isValidDate(dateString) {\n              var regEx = /^\\d{4}-\\d{2}-\\d{2}\$/;\n              if(!dateString.match(regEx)) return false;  // Invalid format\n              var d = new Date(dateString);\n              var dNum = d.getTime();\n              if(!dNum && dNum !== 0) return false; // NaN value, Invalid date\n              return d.toISOString().slice(0,10) === dateString;\n        }\n        function isValidIP(rIP) {\n            if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\$/.test(rIP)) {\n                return true;\n            } else {\n                return false;\n            }\n        }\n        function evaluateForm() {\n            if ((\$(\"#is_mag\").is(\":checked\")) || (\$(\"#is_e2\").is(\":checked\"))) {\n                if (\$(\"#is_mag\").is(\":checked\")) {\n\t\t\t\t\t";
if (hasPermissions("adv", "add_mag")) {
    echo "                    \$(\"#mac_entry_mag\").show();\n\t\t\t\t\t\$(\"#info\").show();\n\t\t\t\t\t\$(\"#info2\").show();\n\t\t\t\t\t\$(\"#info3\").show();\n\t\t\t\t\t\$(\"#info4\").show();\n\t\t\t\t\t\$(\"#info5\").show();\n\t\t\t\t\t\$(\"#info6\").show();\n\t\t\t\t\t\$(\"#info7\").show();\n                    window.swObjs[\"lock_device\"].enable();\n\t\t\t\t\t";
}
if (hasPermissions("adv", "add_e2")) {
    echo "                    window.swObjs[\"is_e2\"].disable();\n\t\t\t\t\t";
}
echo "                } else {\n\t\t\t\t\t";
if (hasPermissions("adv", "add_mag")) {
    echo "                    \$(\"#mac_entry_e2\").show();\n\t\t\t\t\t";
}
if (hasPermissions("adv", "add_e2")) {
    echo "                    window.swObjs[\"is_mag\"].disable();\n                    window.swObjs[\"lock_device\"].disable();\n\t\t\t\t\t";
}
echo "                }\n            } else {\n\t\t\t\t";
if (hasPermissions("adv", "add_e2")) {
    echo "                \$(\"#mac_entry_e2\").hide();\n                window.swObjs[\"is_e2\"].enable();\n\t\t\t\t";
}
if (hasPermissions("adv", "add_mag")) {
    echo "\t\t\t\t\$(\"#mac_entry_mag\").hide();\n\t\t\t\t\$(\"#info\").hide();\n\t\t\t\t\$(\"#info2\").hide();\n\t\t\t\t\$(\"#info3\").hide();\n\t\t\t\t\$(\"#info4\").hide();\n\t\t\t\t\$(\"#info5\").hide();\n\t\t\t\t\$(\"#info6\").hide();\n\t\t\t\t\$(\"#info7\").hide();\n                window.swObjs[\"is_mag\"].enable();\n\t\t\t\t";
}
echo "                window.swObjs[\"lock_device\"].disable();\n            }\n        }\n        \n\t\t\n\t\tfunction download(username, password) {\n            \$(\"#download_type\").val(\"\");\n            \$(\"#download_button\").attr(\"disabled\", true);\n            \$('.playlist').data('username', username);\n            \$('.playlist').data('password', password);\n            \$('.playlist').modal('show');\n        }\n       \n        \$(\"#download_type\").change(function() {\n            if (\$(\"#download_type\").val().length > 0) {\n                ";
if (0 < strlen($rUserInfo["reseller_dns"])) {
    $rDNS = $rUserInfo["reseller_dns"];
} else {
    $rDNS = $rServers[$_INFO["server_id"]]["domain_name"] ? $rServers[$_INFO["server_id"]]["domain_name"] : $rServers[$_INFO["server_id"]]["server_ip"];
}
echo "\t\t\t\t";
if ($rAdminSettings["use_https_main"]) {
    echo "                rText = \"https://";
    echo $rDNS;
    echo ":";
    echo $rServers[$_INFO["server_id"]]["https_broadcast_port"];
    echo "/get.php?username=\" + \$('.playlist').data('username') + \"&password=\" + \$('.playlist').data('password') + \"&\" + decodeURIComponent(\$('.playlist select').val());\n                if (\$(\"#download_type\").find(':selected').data('text')) {\n                    rText = \$(\"#download_type\").find(':selected').data('text').replace(\"{DEVICE_LINK}\", '\"' + rText + '\"');\n                    \$(\"#download_button\").attr(\"disabled\", true);\n                } else {\n                    \$(\"#download_button\").attr(\"disabled\", false);\n                }\n                \$(\"#download_url\").val(rText);\n            } else {\n                \$(\"#download_url\").val(\"\");\n            }\n\t\t\t    ";
} else {
    echo "\t\t\t    rText = \"http://";
    echo $rDNS;
    echo ":";
    echo $rServers[$_INFO["server_id"]]["http_broadcast_port"];
    echo "/get.php?username=\" + \$('.playlist').data('username') + \"&password=\" + \$('.playlist').data('password') + \"&\" + decodeURIComponent(\$('.playlist select').val());\n                if (\$(\"#download_type\").find(':selected').data('text')) {\n                    rText = \$(\"#download_type\").find(':selected').data('text').replace(\"{DEVICE_LINK}\", '\"' + rText + '\"');\n                    \$(\"#download_button\").attr(\"disabled\", true);\n                } else {\n                    \$(\"#download_button\").attr(\"disabled\", false);\n                }\n                \$(\"#download_url\").val(rText);\n            } else {\n                \$(\"#download_url\").val(\"\");\n            }\n\t\t\t    ";
}
echo "        });\n\t\t\n\t\t\n\t\tfunction doDownload() {\n            if (\$(\"#download_url\").val().length > 0) {\n                window.open(\$(\"#download_url\").val());\n            }\n        }\n        function copyDownload() {\n            \$(\"#download_url\").select();\n            document.execCommand(\"copy\"); \n        }\n\t\t\n\t\t\n        \$(document).ready(function() {\n            \$('select.select2').select2({width: '100%'})\n            \$(\".js-switch\").each(function (index, element) {\n                var init = new Switchery(element);\n                window.swObjs[element.id] = init;\n            });\n\t\t\t";
if (hasPermissions("adv", "edit_user") && !empty($_GET["id"])) {
    $startDate = "startDate: '" . date("Y-m-d H:i:s", $rUser["exp_date"]) . "'";
} else {
    $startDate = "startDate: '" . date("Y-m-d H:i:s") . "'";
}
echo "            \$('#exp_date').daterangepicker({\n                singleDatePicker: true,\n                showDropdowns: true,\n\t\t\t\ttimePicker24Hour: true,\n\t\t\t\ttimePicker: true,\n                ";
echo $startDate;
echo ",\n                endDate: moment().startOf('hour').add(32, 'hour'),\n                minDate: new Date(),\n                locale: {\n                    format: 'YYYY-MM-DD HH:mm'\n                }\n            });\n            \n            \$(\"#datatable-bouquets\").DataTable({\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,2,3]}\n                ],\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rBouquets) !== -1) {\n                        \$(row).addClass(\"selected\");\n                    }\n                },\n                paging: false,\n                bInfo: false,\n                searching: false\n            });\n            \$(\"#datatable-bouquets\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rBouquets.splice(parseInt(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html()), window.rBouquets), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rBouquets.push(parseInt(\$(ui.selected).find(\"td:eq(0)\").html()));\n                    }\n                }\n            });\n            \n            \$(\"#no_expire\").change(function() {\n                if (\$(this).prop(\"checked\")) {\n                    \$(\"#exp_date\").prop(\"disabled\", true);\n                } else {\n                    \$(\"#exp_date\").removeAttr(\"disabled\");\n                }\n            });\n            \n            \$(\".js-switch\").on(\"change\" , function() {\n                evaluateForm();\n            });\n            \n            \$(\"#user_form\").submit(function(e){\n                var rBouquets = [];\n                \$(\"#datatable-bouquets tr.selected\").each(function() {\n                    rBouquets.push(\$(this).find(\"td:eq(0)\").html());\n                });\n                \$(\"#bouquets_selected\").val(JSON.stringify(rBouquets));\n                \$(\"#allowed_ua option\").prop('selected', true);\n                \$(\"#allowed_ips option\").prop('selected', true);\n            });\n            \$(document).keypress(function (e) {\n\t\t\t\tif(e.which == 13 && e.target.nodeName != \"TEXTAREA\") return false;\n\t\t\t});\n            \$(\"#add_ip\").click(function() {\n                if ((\$(\"#ip_field\").val().length > 0) && (isValidIP(\$(\"#ip_field\").val()))) {\n                    var o = new Option(\$(\"#ip_field\").val(), \$(\"#ip_field\").val());\n                    \$(\"#allowed_ips\").append(o);\n                    \$(\"#ip_field\").val(\"\");\n                } else {\n                    \$.toast(\"";
echo $_["please_enter_a_valid_ip_address"];
echo "\");\n                }\n            });\n            \$(\"#remove_ip\").click(function() {\n                \$('#allowed_ips option:selected').remove();\n            });\n            \$(\"#add_ua\").click(function() {\n                if (\$(\"#ua_field\").val().length > 0) {\n                    var o = new Option(\$(\"#ua_field\").val(), \$(\"#ua_field\").val());\n                    \$(\"#allowed_ua\").append(o);\n                    \$(\"#ua_field\").val(\"\");\n                } else {\n                    \$.toast(\"";
echo $_["please_enter_a_user_agent"];
echo "\");\n                }\n            });\n            \$(\"#remove_ua\").click(function() {\n                \$('#allowed_ua option:selected').remove();\n            });\n            \$(\"#max_connections\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n            \n            evaluateForm();\n        });\n        </script>\n    </body>\n</html>";

?>