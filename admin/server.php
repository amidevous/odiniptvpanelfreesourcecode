<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_server") && !hasPermissions("adv", "edit_server")) {
    exit;
}
if (isset($_POST["submit_server"])) {
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_server")) {
            exit;
        }
        $rArray = getStreamingServersByID($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        if (!hasPermissions("adv", "add_server")) {
            exit;
        }
        $rArray = ["server_name" => "", "domain_name" => "", "server_ip" => "", "vpn_ip" => "", "ssh_password" => "", "ssh_port" => 22, "diff_time_main" => 0, "http_broadcast_port" => 8080, "total_clients" => 1000, "system_os" => "", "network_interface" => "", "status" => 2, "enable_geoip" => 0, "geoip_countries" => "[]", "geoip_type" => "low_priority", "isp_names" => "[]", "isp_type" => "low_priority", "can_delete" => 1, "rtmp_port" => 25462, "enable_isp" => 0, "boost_fpm" => 0, "network_guaranteed_speed" => 1000, "https_broadcast_port" => 8443, "whitelist_ips" => [], "timeshift_only" => 0];
    }
    if (strlen($_POST["server_ip"]) == 0) {
        $_STATUS = 1;
    }
    if (0 < strlen($_POST["ssh_password"])) {
        $rArray["ssh_password"] = base64_encode(base64_encode($_POST["ssh_password"]));
    } else {
        if (!isset($_POST["edit"])) {
            $_STATUS = 1;
        }
    }
    if (isset($rServers[$_POST["edit"]]["can_delete"])) {
        $rArray["can_delete"] = intval($rServers[$_POST["edit"]]["can_delete"]);
    }
    if (isset($_POST["enabled"])) {
        $rArray["enabled"] = intval($_POST["enabled"]);
        unset($_POST["enabled"]);
    }
    if (isset($_POST["total_clients"])) {
        $rArray["total_clients"] = intval($_POST["total_clients"]);
        unset($_POST["total_clients"]);
    }
    $rPorts = [$rArray["http_broadcast_port"], $rArray["https_broadcast_port"], $rArray["rtmp_port"], $rArray["http_isp_port"]];
    if (isset($_POST["http_broadcast_port"])) {
        $rArray["http_broadcast_port"] = intval($_POST["http_broadcast_port"]);
        unset($_POST["http_broadcast_port"]);
    }
    if (isset($_POST["https_broadcast_port"])) {
        $rArray["https_broadcast_port"] = intval($_POST["https_broadcast_port"]);
        unset($_POST["https_broadcast_port"]);
    }
    if (isset($_POST["rtmp_port"])) {
        $rArray["rtmp_port"] = intval($_POST["rtmp_port"]);
        unset($_POST["rtmp_port"]);
    }
    if (isset($_POST["http_isp_port"])) {
        $rArray["http_isp_port"] = intval($_POST["http_isp_port"]);
        unset($_POST["http_isp_port"]);
    }
    if (isset($_POST["ssh_port"])) {
        $rArray["ssh_port"] = intval($_POST["ssh_port"]);
        unset($_POST["ssh_port"]);
    }
    if (isset($_POST["diff_time_main"])) {
        $rArray["diff_time_main"] = intval($_POST["diff_time_main"]);
        unset($_POST["diff_time_main"]);
    }
    if (isset($_POST["network_guaranteed_speed"])) {
        $rArray["network_guaranteed_speed"] = intval($_POST["network_guaranteed_speed"]);
        unset($_POST["network_guaranteed_speed"]);
    }
    if (isset($_POST["timeshift_only"])) {
        $rArray["timeshift_only"] = true;
        unset($_POST["timeshift_only"]);
    } else {
        $rArray["timeshift_only"] = false;
    }
    if (isset($_POST["enable_geoip"])) {
        $rArray["enable_geoip"] = true;
        unset($_POST["enable_geoip"]);
    } else {
        $rArray["enable_geoip"] = false;
    }
    if (isset($_POST["geoip_countries"])) {
        $rArray["geoip_countries"] = [];
        foreach ($_POST["geoip_countries"] as $rCountry) {
            $rArray["geoip_countries"][] = $rCountry;
        }
        unset($_POST["geoip_countries"]);
    } else {
        $rArray["geoip_countries"] = [];
    }
    if (isset($_POST["enable_isp"])) {
        $rArray["enable_isp"] = true;
        unset($_POST["enable_isp"]);
    } else {
        $rArray["enable_isp"] = false;
    }
    if (isset($_POST["enable_duplex"])) {
        $rArray["enable_duplex"] = true;
        unset($_POST["enable_duplex"]);
    } else {
        $rArray["enable_duplex"] = false;
    }
    unset($_POST["ssh_password"]);
    if (isset($_POST["isp_names"])) {
        if (!is_array($_POST["isp_names"])) {
            $_POST["isp_names"] = [$_POST["isp_names"]];
        }
        $rArray["isp_names"] = json_encode($_POST["isp_names"]);
    } else {
        $rArray["isp_names"] = "[]";
    }
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
            $rCols = "id," . $rCols;
            $rValues = ESC($_POST["edit"]) . "," . $rValues;
        }
        $rQuery = "REPLACE INTO `streaming_servers`(" . $rCols . ") VALUES(" . $rValues . ");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
                if ($rArray["http_broadcast_port"] != $rPorts[0]) {
                    changePort($rInsertID, 0, $rPorts[0], $rArray["http_broadcast_port"]);
                }
                if ($rArray["https_broadcast_port"] != $rPorts[1]) {
                    changePort($rInsertID, 1, $rPorts[1], $rArray["https_broadcast_port"]);
                }
                if ($rArray["rtmp_port"] != $rPorts[2]) {
                    changePort($rInsertID, 2, $rPorts[2], $rArray["rtmp_port"]);
                }
                if ($rArray["http_isp_port"] != $rPorts[3]) {
                    changePort($rInsertID, 3, $rPorts[3], $rArray["http_isp_port"]);
                }
                loadnginx($rInsertID);
            } else {
                $rInsertID = $db->insert_id;
            }
            $rDifference = getTimeDifference($rInsertID);
            $db->query("UPDATE `streaming_servers` SET `diff_time_main` = " . intval($rDifference) . " WHERE `id` = " . intval($rInsertID) . ";");
            $_STATUS = 0;
            $rServers = getStreamingServers();
            header("Location: ./server.php?successedit&id=" . $rInsertID);
        } else {
            $_STATUS = 2;
        }
    }
}
if (isset($_GET["id"])) {
    $rServerArr = $rServers[$_GET["id"]];
    if (!$rServerArr || !hasPermissions("adv", "edit_server")) {
        exit;
    }
} else {
    if (!hasPermissions("adv", "add_server")) {
        exit;
    }
}
$rCountries = [["id" => "ALL", "name" => "All Countries"], ["id" => "A1", "name" => "Anonymous Proxy"], ["id" => "A2", "name" => "Satellite Provider"], ["id" => "O1", "name" => "Other Country"], ["id" => "AF", "name" => "Afghanistan"], ["id" => "AX", "name" => "Aland Islands"], ["id" => "AL", "name" => "Albania"], ["id" => "DZ", "name" => "Algeria"], ["id" => "AS", "name" => "American Samoa"], ["id" => "AD", "name" => "Andorra"], ["id" => "AO", "name" => "Angola"], ["id" => "AI", "name" => "Anguilla"], ["id" => "AQ", "name" => "Antarctica"], ["id" => "AG", "name" => "Antigua And Barbuda"], ["id" => "AR", "name" => "Argentina"], ["id" => "AM", "name" => "Armenia"], ["id" => "AW", "name" => "Aruba"], ["id" => "AU", "name" => "Australia"], ["id" => "AT", "name" => "Austria"], ["id" => "AZ", "name" => "Azerbaijan"], ["id" => "BS", "name" => "Bahamas"], ["id" => "BH", "name" => "Bahrain"], ["id" => "BD", "name" => "Bangladesh"], ["id" => "BB", "name" => "Barbados"], ["id" => "BY", "name" => "Belarus"], ["id" => "BE", "name" => "Belgium"], ["id" => "BZ", "name" => "Belize"], ["id" => "BJ", "name" => "Benin"], ["id" => "BM", "name" => "Bermuda"], ["id" => "BT", "name" => "Bhutan"], ["id" => "BO", "name" => "Bolivia"], ["id" => "BA", "name" => "Bosnia And Herzegovina"], ["id" => "BW", "name" => "Botswana"], ["id" => "BV", "name" => "Bouvet Island"], ["id" => "BR", "name" => "Brazil"], ["id" => "IO", "name" => "British Indian Ocean Territory"], ["id" => "BN", "name" => "Brunei Darussalam"], ["id" => "BG", "name" => "Bulgaria"], ["id" => "BF", "name" => "Burkina Faso"], ["id" => "BI", "name" => "Burundi"], ["id" => "KH", "name" => "Cambodia"], ["id" => "CM", "name" => "Cameroon"], ["id" => "CA", "name" => "Canada"], ["id" => "CV", "name" => "Cape Verde"], ["id" => "KY", "name" => "Cayman Islands"], ["id" => "CF", "name" => "Central African Republic"], ["id" => "TD", "name" => "Chad"], ["id" => "CL", "name" => "Chile"], ["id" => "CN", "name" => "China"], ["id" => "CX", "name" => "Christmas Island"], ["id" => "CC", "name" => "Cocos (Keeling) Islands"], ["id" => "CO", "name" => "Colombia"], ["id" => "KM", "name" => "Comoros"], ["id" => "CG", "name" => "Congo"], ["id" => "CD", "name" => "Congo, Democratic Republic"], ["id" => "CK", "name" => "Cook Islands"], ["id" => "CR", "name" => "Costa Rica"], ["id" => "CI", "name" => "Cote D'Ivoire"], ["id" => "HR", "name" => "Croatia"], ["id" => "CU", "name" => "Cuba"], ["id" => "CY", "name" => "Cyprus"], ["id" => "CZ", "name" => "Czech Republic"], ["id" => "DK", "name" => "Denmark"], ["id" => "DJ", "name" => "Djibouti"], ["id" => "DM", "name" => "Dominica"], ["id" => "DO", "name" => "Dominican Republic"], ["id" => "EC", "name" => "Ecuador"], ["id" => "EG", "name" => "Egypt"], ["id" => "SV", "name" => "El Salvador"], ["id" => "GQ", "name" => "Equatorial Guinea"], ["id" => "ER", "name" => "Eritrea"], ["id" => "EE", "name" => "Estonia"], ["id" => "ET", "name" => "Ethiopia"], ["id" => "FK", "name" => "Falkland Islands (Malvinas)"], ["id" => "FO", "name" => "Faroe Islands"], ["id" => "FJ", "name" => "Fiji"], ["id" => "FI", "name" => "Finland"], ["id" => "FR", "name" => "France"], ["id" => "GF", "name" => "French Guiana"], ["id" => "PF", "name" => "French Polynesia"], ["id" => "TF", "name" => "French Southern Territories"], ["id" => "MK", "name" => "Fyrom"], ["id" => "GA", "name" => "Gabon"], ["id" => "GM", "name" => "Gambia"], ["id" => "GE", "name" => "Georgia"], ["id" => "DE", "name" => "Germany"], ["id" => "GH", "name" => "Ghana"], ["id" => "GI", "name" => "Gibraltar"], ["id" => "GR", "name" => "Greece"], ["id" => "GL", "name" => "Greenland"], ["id" => "GD", "name" => "Grenada"], ["id" => "GP", "name" => "Guadeloupe"], ["id" => "GU", "name" => "Guam"], ["id" => "GT", "name" => "Guatemala"], ["id" => "GG", "name" => "Guernsey"], ["id" => "GN", "name" => "Guinea"], ["id" => "GW", "name" => "Guinea-Bissau"], ["id" => "GY", "name" => "Guyana"], ["id" => "HT", "name" => "Haiti"], ["id" => "HM", "name" => "Heard Island & Mcdonald Islands"], ["id" => "VA", "name" => "Holy See (Vatican City State)"], ["id" => "HN", "name" => "Honduras"], ["id" => "HK", "name" => "Hong Kong"], ["id" => "HU", "name" => "Hungary"], ["id" => "IS", "name" => "Iceland"], ["id" => "IN", "name" => "India"], ["id" => "ID", "name" => "Indonesia"], ["id" => "IR", "name" => "Iran, Islamic Republic Of"], ["id" => "IQ", "name" => "Iraq"], ["id" => "IE", "name" => "Ireland"], ["id" => "IM", "name" => "Isle Of Man"], ["id" => "IL", "name" => "Israel"], ["id" => "IT", "name" => "Italy"], ["id" => "JM", "name" => "Jamaica"], ["id" => "JP", "name" => "Japan"], ["id" => "JE", "name" => "Jersey"], ["id" => "JO", "name" => "Jordan"], ["id" => "KZ", "name" => "Kazakhstan"], ["id" => "KE", "name" => "Kenya"], ["id" => "KI", "name" => "Kiribati"], ["id" => "KR", "name" => "Korea"], ["id" => "KW", "name" => "Kuwait"], ["id" => "KG", "name" => "Kyrgyzstan"], ["id" => "LA", "name" => "Lao People's Democratic Republic"], ["id" => "LV", "name" => "Latvia"], ["id" => "LB", "name" => "Lebanon"], ["id" => "LS", "name" => "Lesotho"], ["id" => "LR", "name" => "Liberia"], ["id" => "LY", "name" => "Libyan Arab Jamahiriya"], ["id" => "LI", "name" => "Liechtenstein"], ["id" => "LT", "name" => "Lithuania"], ["id" => "LU", "name" => "Luxembourg"], ["id" => "MO", "name" => "Macao"], ["id" => "MG", "name" => "Madagascar"], ["id" => "MW", "name" => "Malawi"], ["id" => "MY", "name" => "Malaysia"], ["id" => "MV", "name" => "Maldives"], ["id" => "ML", "name" => "Mali"], ["id" => "MT", "name" => "Malta"], ["id" => "MH", "name" => "Marshall Islands"], ["id" => "MQ", "name" => "Martinique"], ["id" => "MR", "name" => "Mauritania"], ["id" => "MU", "name" => "Mauritius"], ["id" => "YT", "name" => "Mayotte"], ["id" => "MX", "name" => "Mexico"], ["id" => "FM", "name" => "Micronesia, Federated States Of"], ["id" => "MD", "name" => "Moldova"], ["id" => "MC", "name" => "Monaco"], ["id" => "MN", "name" => "Mongolia"], ["id" => "ME", "name" => "Montenegro"], ["id" => "MS", "name" => "Montserrat"], ["id" => "MA", "name" => "Morocco"], ["id" => "MZ", "name" => "Mozambique"], ["id" => "MM", "name" => "Myanmar"], ["id" => "NA", "name" => "Namibia"], ["id" => "NR", "name" => "Nauru"], ["id" => "NP", "name" => "Nepal"], ["id" => "NL", "name" => "Netherlands"], ["id" => "AN", "name" => "Netherlands Antilles"], ["id" => "NC", "name" => "New Caledonia"], ["id" => "NZ", "name" => "New Zealand"], ["id" => "NI", "name" => "Nicaragua"], ["id" => "NE", "name" => "Niger"], ["id" => "NG", "name" => "Nigeria"], ["id" => "NU", "name" => "Niue"], ["id" => "NF", "name" => "Norfolk Island"], ["id" => "MP", "name" => "Northern Mariana Islands"], ["id" => "NO", "name" => "Norway"], ["id" => "OM", "name" => "Oman"], ["id" => "PK", "name" => "Pakistan"], ["id" => "PW", "name" => "Palau"], ["id" => "PS", "name" => "Palestinian Territory, Occupied"], ["id" => "PA", "name" => "Panama"], ["id" => "PG", "name" => "Papua New Guinea"], ["id" => "PY", "name" => "Paraguay"], ["id" => "PE", "name" => "Peru"], ["id" => "PH", "name" => "Philippines"], ["id" => "PN", "name" => "Pitcairn"], ["id" => "PL", "name" => "Poland"], ["id" => "PT", "name" => "Portugal"], ["id" => "PR", "name" => "Puerto Rico"], ["id" => "QA", "name" => "Qatar"], ["id" => "RE", "name" => "Reunion"], ["id" => "RO", "name" => "Romania"], ["id" => "RU", "name" => "Russian Federation"], ["id" => "RW", "name" => "Rwanda"], ["id" => "BL", "name" => "Saint Barthelemy"], ["id" => "SH", "name" => "Saint Helena"], ["id" => "KN", "name" => "Saint Kitts And Nevis"], ["id" => "LC", "name" => "Saint Lucia"], ["id" => "MF", "name" => "Saint Martin"], ["id" => "PM", "name" => "Saint Pierre And Miquelon"], ["id" => "VC", "name" => "Saint Vincent And Grenadines"], ["id" => "WS", "name" => "Samoa"], ["id" => "SM", "name" => "San Marino"], ["id" => "ST", "name" => "Sao Tome And Principe"], ["id" => "SA", "name" => "Saudi Arabia"], ["id" => "SN", "name" => "Senegal"], ["id" => "RS", "name" => "Serbia"], ["id" => "SC", "name" => "Seychelles"], ["id" => "SL", "name" => "Sierra Leone"], ["id" => "SG", "name" => "Singapore"], ["id" => "SK", "name" => "Slovakia"], ["id" => "SI", "name" => "Slovenia"], ["id" => "SB", "name" => "Solomon Islands"], ["id" => "SO", "name" => "Somalia"], ["id" => "ZA", "name" => "South Africa"], ["id" => "GS", "name" => "South Georgia And Sandwich Isl."], ["id" => "ES", "name" => "Spain"], ["id" => "LK", "name" => "Sri Lanka"], ["id" => "SD", "name" => "Sudan"], ["id" => "SR", "name" => "Suriname"], ["id" => "SJ", "name" => "Svalbard And Jan Mayen"], ["id" => "SZ", "name" => "Swaziland"], ["id" => "SE", "name" => "Sweden"], ["id" => "CH", "name" => "Switzerland"], ["id" => "SY", "name" => "Syrian Arab Republic"], ["id" => "TW", "name" => "Taiwan"], ["id" => "TJ", "name" => "Tajikistan"], ["id" => "TZ", "name" => "Tanzania"], ["id" => "TH", "name" => "Thailand"], ["id" => "TL", "name" => "Timor-Leste"], ["id" => "TG", "name" => "Togo"], ["id" => "TK", "name" => "Tokelau"], ["id" => "TO", "name" => "Tonga"], ["id" => "TT", "name" => "Trinidad And Tobago"], ["id" => "TN", "name" => "Tunisia"], ["id" => "TR", "name" => "Turkey"], ["id" => "TM", "name" => "Turkmenistan"], ["id" => "TC", "name" => "Turks And Caicos Islands"], ["id" => "TV", "name" => "Tuvalu"], ["id" => "UG", "name" => "Uganda"], ["id" => "UA", "name" => "Ukraine"], ["id" => "AE", "name" => "United Arab Emirates"], ["id" => "GB", "name" => "United Kingdom"], ["id" => "US", "name" => "United States"], ["id" => "UM", "name" => "United States Outlying Islands"], ["id" => "UY", "name" => "Uruguay"], ["id" => "UZ", "name" => "Uzbekistan"], ["id" => "VU", "name" => "Vanuatu"], ["id" => "VE", "name" => "Venezuela"], ["id" => "VN", "name" => "Viet Nam"], ["id" => "VG", "name" => "Virgin Islands, British"], ["id" => "VI", "name" => "Virgin Islands, U.S."], ["id" => "WF", "name" => "Wallis And Futuna"], ["id" => "EH", "name" => "Western Sahara"], ["id" => "YE", "name" => "Yemen"], ["id" => "ZM", "name" => "Zambia"], ["id" => "ZW", "name" => "Zimbabwe"]];
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./servers.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_servers"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rServerArr)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
echo $_["server"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            Server operation was completed successfully.\n                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", \"Server operation was completed successfully.\", \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["generic_fail"];
            echo "                        </div>\n                        ";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["generic_fail"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./server.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"server_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rServerArr)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rServerArr["id"];
    echo "\" />\n                                    <input type=\"hidden\" name=\"status\" value=\"";
    echo $rServerArr["status"];
    echo "\" />\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#server-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#advanced-options\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-folder-alert-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["advanced"];
echo "</span>\n                                                </a>\n                                            </li>\n\t\t\t\t\t\t\t\t\t\t\t<li class=\"nav-item\">\n                                                <a href=\"#ispmanager\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-folder-alert-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["isp_manager"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"server-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"server_name\">";
echo $_["server_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"server_name\" name=\"server_name\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["server_name"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"domain_name\">";
echo $_["domaine_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"domain_name\" name=\"domain_name\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["domain_name"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"server_ip\">";
echo $_["server_ip"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"server_ip\" name=\"server_ip\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["server_ip"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"vpn_ip\">";
echo $_["vpn_ip"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"vpn_ip\" name=\"vpn_ip\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["vpn_ip"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"ssh_password\">Root Password";
if (isset($rServerArr)) {
    echo " ";
}
echo $_["ssh_password"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"ssh_password\" name=\"ssh_password\" ";
if (!isset($rServerArr)) {
    echo "value=\"\" required data-parsley-trigger=\"change\"";
} else {
    echo "value=\"\"";
}
echo ">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"total_clients\">";
echo $_["max_clients"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"total_clients\" name=\"total_clients\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["total_clients"]);
} else {
    echo "1000";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"timeshift_only\">";
echo $_["timeshift_only"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"timeshift_only\" id=\"timeshift_only\" type=\"checkbox\" ";
if (isset($rServerArr) && $rServerArr["timeshift_only"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"enable_duplex\">Duplex</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"enable_duplex\" id=\"enable_duplex\" type=\"checkbox\" ";
if (isset($rServerArr) && $rServerArr["enable_duplex"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\t\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"advanced-options\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"http_broadcast_port\">";
echo $_["http_port"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"http_broadcast_port\" name=\"http_broadcast_port\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["http_broadcast_port"]);
} else {
    echo "8080";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"https_broadcast_port\">";
echo $_["https_port"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"https_broadcast_port\" name=\"https_broadcast_port\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["https_broadcast_port"]);
} else {
    echo "8443";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"rtmp_port\">";
echo $_["rtmp_port"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"rtmp_port\" name=\"rtmp_port\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["rtmp_port"]);
} else {
    echo "25462";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
if ($_GET["id"] == 1) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"http_isp_port\">";
    echo $_["isp_port"];
    echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"http_isp_port\" name=\"http_isp_port\" value=\"";
    if (isset($rServerArr)) {
        echo htmlspecialchars($rServerArr["http_isp_port"]);
    } else {
        echo "";
    }
    echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            \n                                                            <label class=\"col-md-4 col-form-label\" for=\"diff_time_main\">";
echo $_["time_difference"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" disabled class=\"form-control\" id=\"diff_time_main\" name=\"diff_time_main\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["diff_time_main"]);
} else {
    echo "0";
}
echo "\">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"ssh_port\">";
echo $_["ssh_port"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"ssh_port\" name=\"ssh_port\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["ssh_port"]);
} else {
    echo "22";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"network_interface\">Network Interface</label>\n                                                            <!--<div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"network_interface\" name=\"network_interface\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["network_interface"]);
} else {
    echo "eth0";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>-->\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <select name=\"network_interface\" id=\"network_interface\" class=\"form-control select2\" data-toggle=\"select2\">network interface\n                                                                    ";
foreach (netnet($_GET["id"]) as $bbb) {
    echo "                                                                    <option ";
    if (isset($rServerArr) && $rServerArr["network_interface"] == $bbb) {
        echo "selected ";
    }
    echo "value=\"";
    echo $bbb;
    echo "\">";
    echo $bbb;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"network_guaranteed_speed\">";
echo $_["network_speed"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"network_guaranteed_speed\" name=\"network_guaranteed_speed\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["network_guaranteed_speed"]);
} else {
    echo "1000";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"system_os\">";
echo $_["operating_system"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"system_os\" name=\"system_os\" value=\"";
if (isset($rServerArr)) {
    echo htmlspecialchars($rServerArr["system_os"]);
} else {
    echo "Ubuntu 18";
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"enable_geoip\">";
echo $_["geoip_load_balancing"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"enable_geoip\" id=\"enable_geoip\" type=\"checkbox\" ";
if (isset($rServerArr) && $rServerArr["enable_geoip"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <div class=\"col-md-6\">\n                                                                <select name=\"geoip_type\" id=\"geoip_type\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
foreach (["high_priority" => "High Priority", "low_priority" => "Low Priority", "strict" => "Strict"] as $rType => $rText) {
    echo "                                                                    <option ";
    if (isset($rServerArr) && $rServerArr["geoip_type"] == $rType) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rType;
    echo "\">";
    echo $rText;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"geoip_countries\">";
echo $_["geoip_countries"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"geoip_countries[]\" id=\"geoip_countries\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "\">\n                                                                    ";
$rSelected = json_decode($rServerArr["geoip_countries"], true);
foreach ($rCountries as $rCountry) {
    echo "                                                                    <!--<option ";
    if (isset($rServerArr) && in_array($rCountry["id"], $rSelected)) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rCountry["id"];
    echo "\">";
    echo $rCountry["name"];
    echo "</option>-->\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option ";
    if (isset($rServerArr) && !empty($rSelected) && in_array($rCountry["id"], $rSelected)) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rCountry["id"];
    echo "\">";
    echo $rCountry["name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n                                                </ul>\n                                            </div>                         \n                                                        \n                                                        \n                                            <div class=\"tab-pane\" id=\"ispmanager\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">                                                        \n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"enable_isp\">Enable ISP</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"enable_isp\" id=\"enable_isp\" type=\"checkbox\" ";
if (isset($rServerArr) && $rServerArr["enable_isp"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <div class=\"col-md-6\">\n                                                                <select name=\"isp_type\" id=\"isp_type\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
foreach (["high_priority" => "High Priority", "low_priority" => "Low Priority", "strict" => "Strict"] as $rType => $rText) {
    echo "                                                                    <option ";
    if (isset($rServerArr) && $rServerArr["isp_type"] == $rType) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rType;
    echo "\">";
    echo $rText;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"isp_field\">Allowed ISP Names</label>\n                                                            <div class=\"col-md-8 input-group\">\n                                                                <input type=\"text\" id=\"isp_field\" class=\"form-control\" value=\"\">\n                                                                <div class=\"input-group-append\">\n                                                                    <a href=\"javascript:void(0)\" id=\"add_isp\" class=\"btn btn-primary waves-effect waves-light\"><i class=\"mdi mdi-plus\"></i></a>\n                                                                    <a href=\"javascript:void(0)\" id=\"remove_isp\" class=\"btn btn-danger waves-effect waves-light\"><i class=\"mdi mdi-close\"></i></a>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"isp_names\">&nbsp;</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"isp_names\" name=\"isp_names[]\" size=6 class=\"form-control\" multiple=\"multiple\">\n                                                                ";
$rnabilosss = json_decode($rServerArr["isp_names"], true);
if (isset($rServerArr) & is_array($rnabilosss)) {
    foreach ($rnabilosss as $ispnom) {
        echo "                                                                <option value=\"";
        echo $ispnom;
        echo "\">";
        echo $ispnom;
        echo "</option>\n                                                                ";
    }
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        \n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_server\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rServerArr)) {
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
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n\t\tvar swObjs = {};\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        \$(document).ready(function() {\n            \$('select.select2').select2({width: '100%'})\n            \$(\"#geoip_countries\").select2({width: '100%'})\n            \$(\".js-switch\").each(function (index, element) {\n                var init = new Switchery(element);\n                window.swObjs[element.id] = init;\n            });\n            \n            \$('#exp_date').daterangepicker({\n                singleDatePicker: true,\n                showDropdowns: true,\n                minDate: new Date(),\n                locale: {\n                    format: 'YYYY-MM-DD'\n                }\n            });\n            \n            \$(\"#no_expire\").change(function() {\n                if (\$(this).prop(\"checked\")) {\n                    \$(\"#exp_date\").prop(\"disabled\", true);\n                } else {\n                    \$(\"#exp_date\").removeAttr(\"disabled\");\n                }\n            });\n\t\t\t\$(\"#server_form\").submit(function(e){\n                \$(\"#isp_names option\").prop('selected', true);\n            });\n            \$(\"#add_isp\").click(function() {\n                if (\$(\"#isp_field\").val().length > 0) {\n                    var o = new Option(\$(\"#isp_field\").val(), \$(\"#isp_field\").val());\n                    \$(\"#isp_names\").append(o);\n                    \$(\"#isp_field\").val(\"\");\n                } else {\n                    \$.toast(\"Please enter a valid ISP name.\");\n                }\n            });\n            \$(\"#remove_isp\").click(function() {\n                \$('#isp_names option:selected').remove();\n            });\n            \n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"#total_clients\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#http_broadcast_port\").inputFilter(function(value) { return /^\\d*\$/.test(value) && (value === \"\" || parseInt(value) <= 65535); });\n            \$(\"#https_broadcast_port\").inputFilter(function(value) { return /^\\d*\$/.test(value) && (value === \"\" || parseInt(value) <= 65535); });\n            \$(\"#rtmp_port\").inputFilter(function(value) { return /^\\d*\$/.test(value) && (value === \"\" || parseInt(value) <= 65535); });\n\t\t\t\$(\"#http_isp_port\").inputFilter(function(value) { return /^\\d*\$/.test(value) && (value === \"\" || parseInt(value) <= 65535); });\n\t\t\t\$(\"#ssh_port\").inputFilter(function(value) { return /^\\d*\$/.test(value) && (value === \"\" || parseInt(value) <= 65535); });\n            \$(\"#diff_time_main\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#network_guaranteed_speed\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n        });\n        </script>\n    </body>\n</html>";

?>