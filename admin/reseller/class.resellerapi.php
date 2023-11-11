<?php
/////////////////////////////////////////////////////////////////////////////////////
// Xtream UI Reseller API                                                          //
/////////////////////////////////////////////////////////////////////////////////////
//                                                                                 //
// Disclaimer:                                                                     //
// This is an externally developed API, not officially part of the Xtream UI base. //
//                                                                                 //
/////////////////////////////////////////////////////////////////////////////////////

include "../functions.php";

function resellerapi_encrypt($q, $salt = null) {
    $qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($salt), $q, MCRYPT_MODE_CBC, md5(md5($salt))));
    return( $qEncoded );
}

class Controller {
    public function testconnection($param) {
        global $db;
        $return = array();
        $rUserInfo = $db->query("SELECT * FROM `reseller_credentials` WHERE `api_key` = '" . $db->real_escape_string(resellerapi_encrypt($param['api_key']), "!SMARTERS!") . "' AND `api_key` <> '';");
        if ($rUserInfo->num_rows > 0) {
            $return['result'] = 'success';
            $return['message'] = 'SUCCESSFUL!';
        } else {
            $return['result'] = 'error';
            $return['message'] = 'Connection Error! Invalid API Key';
        }
        return json_encode($return);
    }

    public function getresellerid($param) {
        global $db;
        $return = array();
        $rUserInfo = $db->query("SELECT * FROM `reseller_credentials` WHERE `api_key` = '" . $db->real_escape_string(resellerapi_encrypt($param['api_key']), "!SMARTERS!") . "' AND `api_key` <> '';");
        if ($rUserInfo->num_rows > 0) {
            while ($row = $rUserInfo->fetch_assoc()) {
                $return['result'] = 'success';
                $return['member_id'] = $row['member_id'];
            }
        } else {
            $return['result'] = 'error';
            $return['message'] = 'Connection Error! Invalid API Key';
        }
        return $return;
    }

    public function getresellerinfo($param) {
        global $db;
        $return = array();
        $registerdetails = self::getresellerid($param);
        if ($registerdetails['result'] == 'success') {
            $rUserInfo = $db->query("SELECT * FROM `reg_users` WHERE `id` = '" . intval($registerdetails['member_id']) . "'");
            if ($rUserInfo->num_rows > 0) {
                while ($row = $rUserInfo->fetch_assoc()) {
                    if (isset($row) && !empty($row)) {
                        $return['result'] = 'success';
                        $return['username'] = $row["username"];
                        if (floor($row["credits"]) == $row["credits"]) {
                            $return['credits'] = number_format($row["credits"], 0);
                        } else {
                            $return['credits'] = number_format($row["credits"], 2);
                        }
                    } else {
                        $return['result'] = 'error';
                        $return['message'] = 'No Record Found!';
                    }
                }
            }
        }
        return json_encode($return);
    }

    public function getpackages($param) {
        global $db;
        $return = array();
        $registerdetails = self::getresellerid($param);
        if ($registerdetails['result'] == 'success') {
            $rUserInfo = $db->query("SELECT member_group_id FROM `reg_users` WHERE `id` = '" . intval($registerdetails['member_id']) . "'");
            if ($rUserInfo->num_rows > 0) {
                while ($row = $rUserInfo->fetch_assoc()) {
                    if (isset($row) && !empty($row)) {
                        $packages = getPackages();
                        if (!empty($packages)) {
                            $return['result'] = 'success';
                            foreach ($packages as $rPackage) {
                                if (in_array($row["member_group_id"], json_decode($rPackage["groups"], True))) {
                                    if ((($rPackage["is_trial"]) && ($param["trial"] == 1)) OR ( ($rPackage["is_official"]) && ($param["trial"] == 0))) {
                                        if ($param['producttype'] == 'magdevice') {
                                            if ($rPackage["can_gen_mag"] == 1) {
                                                $return['products'][] = array(
                                                    'pid' => $rPackage["id"],
                                                    'productname' => $rPackage["package_name"],
                                                    'max_connections' => $rPackage['max_connections']
                                                );
                                            }
                                        } else if ($param['producttype'] == 'engdevice') {
                                            if ($rPackage["can_gen_e2"] == 1) {
                                                $return['products'][] = array(
                                                    'pid' => $rPackage["id"],
                                                    'productname' => $rPackage["package_name"],
                                                    'max_connections' => $rPackage['max_connections']
                                                );
                                            }
                                        } else {
                                            $return['products'][] = array(
                                                'pid' => $rPackage["id"],
                                                'productname' => $rPackage["package_name"],
                                                'max_connections' => $rPackage['max_connections']
                                            );
                                        }
                                    }
                                }
                            }
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'No Record Found!';
                        }
                    } else {
                        $return['result'] = 'error';
                        $return['message'] = 'No Record Found!';
                    }
                }
            }
        }
        return json_encode($return);
    }

    public function usercreate($param) {
        global $db;
        $registerdetails = self::getresellerid($param);
        if ($registerdetails['result'] == 'success') {
            $param[] = $param['user_data'];
            $param["member_id"] = $registerdetails['member_id'];
            $row = $db->query("SELECT * FROM `reg_users` WHERE `id` = '" . intval($registerdetails['member_id']) . "'");
            if ($row->num_rows > 0) {
                $rUserInfo = $row->fetch_assoc();
                $param["mac_address_mag"] = strtoupper($param["mac_address_mag"]);
                $param["mac_address_e2"] = strtoupper($param["mac_address_e2"]);

                $rArray = Array("member_id" => 0, "username" => "", "password" => "", "exp_date" => null, "admin_enabled" => 1, "enabled" => 1, "admin_notes" => "", "reseller_notes" => "", "bouquet" => Array(), "max_connections" => 1, "is_restreamer" => 0, "allowed_ips" => Array(), "allowed_ua" => Array(), "created_at" => time(), "created_by" => -1, "is_mag" => 0, "is_e2" => 0, "force_server_id" => 0, "is_isplock" => 0, "isp_desc" => "", "forced_country" => "", "is_stalker" => 0, "bypass_ua" => 0, "play_token" => "");
                if (!empty($param["package"])) {
                    $rPackage = getPackage($param["package"]);
                    // Check package is within permissions.
                    if (($rPackage) && (in_array($rUserInfo["member_group_id"], json_decode($rPackage["groups"], True)))) {
                        // Ignore post and get information from package instead.
                        if ($param["trial"]) {
                            $rCost = $rPackage["trial_credits"];
                        } else {
                            $rCost = $rPackage["official_credits"];
                        }
                        if ($rUserInfo["credits"] >= $rCost) {
                            if ($param["trial"] == '1') {
                                $rArray["exp_date"] = strtotime('+' . intval($rPackage["trial_duration"]) . ' ' . $rPackage["trial_duration_in"]);
                                $rArray["is_trial"] = 1;
                            } else {
                                $rArray["exp_date"] = strtotime('+' . intval($rPackage["official_duration"]) . ' ' . $rPackage["official_duration_in"]);
                                $rArray["is_trial"] = 0;
                            }
                            $rArray["bouquet"] = $rPackage["bouquets"];
                            $rArray["max_connections"] = $rPackage["max_connections"];
                            $rArray["is_restreamer"] = $rPackage["is_restreamer"];

                            $rArray["member_id"] = $param["member_id"];
                            $rArray["reseller_notes"] = $param["reseller_notes"];
                            if (isset($param["is_mag"])) {
                                $rArray["is_mag"] = 1;
                            }
                            if (isset($param["is_e2"])) {
                                $rArray["is_e2"] = 1;
                            }
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'Not enough credits.';
                            return json_encode($return); // Not enough credits.
                        }
                    } else {
                        $return['result'] = 'error';
                        $return['message'] = 'Invalid package.';
                        return json_encode($return); // Invalid package.
                    }
                } else {
                    $return['result'] = 'error';
                    $return['message'] = 'Invalid package.';
                    return json_encode($return); // Invalid package.
                }
                // BAD VCH DEKHNA HAI 
                if (!$rPermissions["allow_change_pass"]) {
                    $param["username"] = '';
                    $param["password"] = '';
                }
                if ((strlen($param["username"]) == 0) OR ( ($rArray["is_mag"]) && (!isset($rUser))) OR ( ($rArray["is_e2"]) && (!isset($rUser)))) {
                    $param["username"] = generateString(10);
                } else if ((($rArray["is_mag"]) && (isset($rUser))) OR ( ($rArray["is_e2"]) && (isset($rUser)))) {
                    $param["username"] = $rUser["username"];
                }
                if ((strlen($param["password"]) == 0) OR ( ($rArray["is_mag"]) && (!isset($rUser))) OR ( ($rArray["is_e2"]) && (!isset($rUser)))) {
                    $param["password"] = generateString(10);
                } else if ((($rArray["is_mag"]) && (isset($rUser))) OR ( ($rArray["is_e2"]) && (isset($rUser)))) {
                    $param["password"] = $rUser["password"];
                }
                $rArray["username"] = $param["username"];
                $rArray["password"] = $param["password"];

                $result = $db->query("SELECT `id` FROM `users` WHERE `username` = '" . $db->real_escape_string($rArray["username"]) . "';");
                if (($result) && ($result->num_rows > 0)) {
                    $return['result'] = 'error';
                    $return['message'] = 'This username already exists.';
                    return json_encode($return);
                }

                if ((($param["is_mag"]) && (!filter_var($param["mac_address_mag"], FILTER_VALIDATE_MAC))) OR ( (strlen($param["mac_address_e2"]) > 0) && (!filter_var($param["mac_address_e2"], FILTER_VALIDATE_MAC)))) {
                    $return['result'] = 'error';
                    $return['message'] = 'An invalid MAC address was entered, please try again.';
                    return json_encode($return);
                }
                if (!isset($_STATUS)) {
                    $rArray["created_by"] = $rUserInfo["id"];
					foreach ($rArray as $rKey => $rValue) {
						$rArray[$db->real_escape_string($rKey)] = $rValue;
					}
                    $rCols = "`" . implode('`,`', array_keys($rArray)) . "`";
                    foreach (array_values($rArray) as $rValue) {
                        isset($rValues) ? $rValues .= ',' : $rValues = '';
                        if (is_array($rValue)) {
                            $rValue = json_encode($rValue);
                        }
                        if (is_null($rValue)) {
                            $rValues .= 'NULL';
                        } else {
                            $rValues .= '\'' . $db->real_escape_string($rValue) . '\'';
                        }
                    }
                    $isMag = False;
                    $isE2 = False;
                    // Confirm Reseller can generate MAG.
                    if ($rArray["is_mag"]) {
                        if ($rPackage["can_gen_mag"]) {
                            $isMag = True;
                        }
                    }
                    if ($rArray["is_e2"]) {
                        if ($rPackage["can_gen_e2"]) {
                            $isE2 = True;
                        }
                    }
                    if ((!$isMag) && (!$isE2) && (($rPackage["only_mag"]) OR ( $rPackage["only_e2"]))) {
                        $return['result'] = 'error';
                        $return['message'] = 'Not allowed to generate normal users!';
                        return json_encode($return);
                    } else {
                        // Checks completed, run,
                        $rQuery = "REPLACE INTO `users`(" . $rCols . ") VALUES(" . $rValues . ");";
                        if ($db->query($rQuery)) {
                            $rInsertID = $db->insert_id;
                            if (isset($rCost)) {
                                $rNewCredits = $rUserInfo["credits"] - $rCost;
                                $db->query("UPDATE `reg_users` SET `credits` = " . $rNewCredits . " WHERE `id` = " . $rUserInfo["id"] . ";");
                                if (isset($rUser)) {
                                    if ($isMag) {
                                        $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . $db->real_escape_string($rArray["username"]) . "', '" . $db->real_escape_string($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b> -> <u>Extend MAG</u>] with Package [" . $db->real_escape_string($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                                    } else if ($isE2) {
                                        $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . $db->real_escape_string($rArray["username"]) . "', '" . $db->real_escape_string($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b> -> <u>Extend Enigma</u>] with Package [" . $db->real_escape_string($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                                    } else {
                                        $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . $db->real_escape_string($rArray["username"]) . "', '" . $db->real_escape_string($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b> -> <u>Extend Line</u>] with Package [" . $db->real_escape_string($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                                    }
                                } else {
                                    if ($isMag) {
                                        $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . $db->real_escape_string($rArray["username"]) . "', '" . $db->real_escape_string($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b> -> <u>New MAG</u>] with Package [" . $db->real_escape_string($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                                    } else if ($isE2) {
                                        $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . $db->real_escape_string($rArray["username"]) . "', '" . $db->real_escape_string($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b> -> <u>New Enigma</u>] with Package [" . $db->real_escape_string($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                                    } else {
                                        $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . $db->real_escape_string($rArray["username"]) . "', '" . $db->real_escape_string($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b> -> <u>New Line</u>] with Package [" . $db->real_escape_string($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                                    }
                                    $rAccessOutput = json_decode($rPackage["output_formats"], True);
                                    $rLockDevice = $rPackage["lock_device"];
                                }
                                $rUserInfo["credits"] = $rNewCredits;
                            }
                            if ((isset($rInsertID)) && (isset($rAccessOutput))) {
                                $db->query("DELETE FROM `user_output` WHERE `user_id` = " . intval($rInsertID) . ";");
                                foreach ($rAccessOutput as $rOutputID) {
                                    $db->query("INSERT INTO `user_output`(`user_id`, `access_output_id`) VALUES(" . intval($rInsertID) . ", " . intval($rOutputID) . ");");
                                }
                            }
                            if ($isMag) {
                                $result = $db->query("SELECT `mag_id` FROM `mag_devices` WHERE `user_id` = " . intval($rInsertID) . " LIMIT 1;");
                                if ((isset($result)) && ($result->num_rows == 1)) {
                                    $db->query("UPDATE `mag_devices` SET `mac` = '" . base64_encode($db->real_escape_string(strtoupper($param["mac_address_mag"]))) . "' WHERE `user_id` = " . intval($rInsertID) . ";");
                                } else {
                                    $db->query("INSERT INTO `mag_devices`(`user_id`, `mac`, `lock_device`) VALUES(" . intval($rInsertID) . ", '" . $db->real_escape_string(base64_encode(strtoupper($param["mac_address_mag"]))) . "', " . intval($rLockDevice) . ");");
                                }
                            } else if ($isE2) {
                                $result = $db->query("SELECT `device_id` FROM `enigma2_devices` WHERE `user_id` = " . intval($rInsertID) . " LIMIT 1;");
                                if ((isset($result)) && ($result->num_rows == 1)) {
                                    $db->query("UPDATE `enigma2_devices` SET `mac` = '" . $db->real_escape_string(strtoupper($param["mac_address_e2"])) . "' WHERE `user_id` = " . intval($rInsertID) . ";");
                                } else {
                                    $db->query("INSERT INTO `enigma2_devices`(`user_id`, `mac`, `lock_device`) VALUES(" . intval($rInsertID) . ", '" . $db->real_escape_string(strtoupper($param["mac_address_e2"])) . "', " . intval($rLockDevice) . ");");
                                }
                            }
                            $return['result'] = 'success';
                            $return["username"] = $param["username"];
                            $return["password"] = $param["password"];
                            $return['message'] = 'User operation was completed successfully.';
                            return json_encode($return);
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'There was an error performing this operation! Please check the form entry and try again.';
                            return json_encode($return);
                        }
                    }
                }
            }
        }
    }

    public function userdisable($params) {
        global $db;
        $return = array();
        $registerdetails = self::getresellerid($params);
        if ($registerdetails['result'] == 'success') {
            $username = $params["username"];
            $reseller_notes = $params['reseller_notes'];
            if ($params['producttype'] == 'streamlineonly') {
                if (isset($username) && !empty($username)) {
                    $result = $db->query("SELECT username FROM users WHERE username='".$db->real_escape_string($username)."' AND enabled='1' AND reseller_notes ='".$db->real_escape_string($reseller_notes)."'");
                    if ($result->num_rows > 0) {
                        $disable = $db->query("UPDATE users SET enabled='0' WHERE username='".$db->real_escape_string($username)."' AND enabled='1' AND reseller_notes ='".$db->real_escape_string($reseller_notes)."' ");
                        if ($disable) {
                            $return['result'] = 'success';
                            $return['message'] = 'User Disable Successfully!';
                            return json_encode($return);
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'No Record Found!';
                        }
                    } else {
                        $return['result'] = 'error';
                        $return['message'] = 'No Record Found!';
                    }
                } else {
                    $return['result'] = 'error';
                    $return['message'] = 'Username is required';
                }
            } elseif ($params['producttype'] == 'magdevice') {
                $result = $db->query("SELECT user_id FROM `mag_devices` WHERE mac='" . $db->real_escape_string(base64_encode($params['mac_address_mag'])) . "'");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $disable = $db->query("UPDATE users SET enabled='0' WHERE reseller_notes='".$db->real_escape_string($reseller_notes)."' AND is_mag='1' AND enabled='1' AND id='" . intval($row['user_id']) . "'");
                        if ($disable) {
                            $return['result'] = 'success';
                            $return['message'] = 'User Disable Successfully!';
                            return json_encode($return);
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'No Record Found!';
                        }
                    }
                } else {
                    $return['result'] = 'error';
                    $return['message'] = 'MAC Address No Found!';
                }
            } elseif ($params['producttype'] == 'engdevice') {
                $result = $db->query("SELECT user_id FROM `enigma2_devices` WHERE mac='" . $db->real_escape_string($params['mac_address_e2']) . "'");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $disable = $db->query("UPDATE users SET enabled='0' WHERE reseller_notes='".$db->real_escape_string($reseller_notes)."' AND is_e2='1' AND enabled='1' AND id='" . intval($row['user_id']) . "'");
                        if ($disable) {
                            $return['result'] = 'success';
                            $return['message'] = 'User Disable Successfully!';
                            return json_encode($return);
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'No Record Found!';
                        }
                    }
                } else {
                    $return['result'] = 'error';
                    $return['message'] = 'No Record Found!';
                }
            }
        } else {
            $return['result'] = 'error';
            $return['message'] = 'Connection Error! Invalid API Key';
        }
        return json_encode($return);
    }

    public function userenable($params) {
        global $db;
        $return = array();
        $registerdetails = self::getresellerid($params);
        if ($registerdetails['result'] == 'success') {
            $username = $params["username"];
            $reseller_notes = $params['reseller_notes'];
            if ($params['producttype'] == 'streamlineonly') {
                if (isset($username) && !empty($username)) {
                    $result = $db->query("SELECT username FROM users WHERE username='".$db->real_escape_string($username)."' AND enabled='0' AND reseller_notes ='".$db->real_escape_string($reseller_notes)."'");
                    if ($result->num_rows > 0) {
                        $disable = $db->query("UPDATE users SET enabled='1' WHERE username='".$db->real_escape_string($username)."' AND enabled='0' AND reseller_notes ='".$db->real_escape_string($reseller_notes)."' ");
                        if ($disable) {
                            $return['result'] = 'success';
                            $return['message'] = 'User Disable Successfully!';
                            return json_encode($return);
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'No Record Found!';
                        }
                    } else {
                        $return['result'] = 'error';
                        $return['message'] = 'No Record Found!';
                    }
                } else {
                    $return['result'] = 'error';
                    $return['message'] = 'Username is required';
                }
            } elseif ($params['producttype'] == 'magdevice') {
                $result = $db->query("SELECT user_id FROM `mag_devices` WHERE mac='" . $db->real_escape_string(base64_encode($params['mac_address_mag'])) . "'");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $disable = $db->query("UPDATE users SET enabled='1' WHERE reseller_notes='$reseller_notes' AND is_mag='1' AND enabled='0' AND id='" . $row['user_id'] . "'");
                        if ($disable) {
                            $return['result'] = 'success';
                            $return['message'] = 'User Disable Successfully!';
                            return json_encode($return);
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'No Record Found!';
                        }
                    }
                } else {
                    $return['result'] = 'error';
                    $return['message'] = 'MAC Address No Found!';
                }
            } elseif ($params['producttype'] == 'engdevice') {
                $result = $db->query("SELECT user_id FROM `enigma2_devices` WHERE mac='" . $db->real_escape_string($params['mac_address_e2']) . "'");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $disable = $db->query("UPDATE users SET enabled='1' WHERE reseller_notes='".$db->real_escape_string($reseller_notes)."' AND is_e2='1' AND enabled='0' AND id='" . intval($row['user_id']) . "'");
                        if ($disable) {
                            $return['result'] = 'success';
                            $return['message'] = 'User Disable Successfully!';
                            return json_encode($return);
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'No Record Found!';
                        }
                    }
                } else {
                    $return['result'] = 'error';
                    $return['message'] = 'No Record Found!';
                }
            }
        } else {
            $return['result'] = 'error';
            $return['message'] = 'Connection Error! Invalid API Key';
        }
        return json_encode($return);
    }

    public function user_info($param) {
        global $db;
        $return = array();
        $registerdetails = self::getresellerid($param);
        if ($registerdetails['result'] == 'success') {
            $rUserInfo = $db->query("SELECT * FROM `reg_users` WHERE `id` = '" . intval($registerdetails['member_id']) . "'");
            if ($rUserInfo->num_rows > 0) {
                $result = $db->query("SELECT * FROM users WHERE username='" . $db->real_escape_string($param['username']) . "' AND password='" . $db->real_escape_string($param['password']) . "' AND member_id ='" . intval($registerdetails['member_id']) . "'");
                if ($result->num_rows > 0) {
                    $access_output = $db->query("SELECT * FROM access_output");
                    while ($output = $access_output->fetch_assoc()) {
                        $access_outputdata[] = $output;
                    }
                    while ($row = $result->fetch_assoc()) {
                        $return['result'] = 'success';
                        $return['user_info'] = array(
                            'exp_date' => $row['exp_date'],
                            'output_formats' => $access_outputdata,
                        );
                    }
                } else {
                    $return['result'] = 'error';
                    $return['message'] = 'No Record Found!';
                }
            } else {
                $return['result'] = 'error';
                $return['message'] = 'No Record Found!';
            }
        }
        return json_encode($return);
    }

    public function stb_info($param) {
        global $db;
        $return = array();
        $registerdetails = self::getresellerid($param);
        if ($registerdetails['result'] == 'success') {
            $rUserInfo = $db->query("SELECT * FROM `reg_users` WHERE `id` = '" . intval($registerdetails['member_id']) . "'");
            if ($rUserInfo->num_rows > 0) {
                if (isset($param['mac']) && !empty($param['mac'])) {
                    $mag = $db->query("SELECT user_id FROM `mag_devices` WHERE mac='" . $db->real_escape_string(base64_encode($param['mac'])) . "'");
                    if ($mag->num_rows > 0) {
                        while ($magoutput = $mag->fetch_assoc()) {
                            $result = $db->query("SELECT * FROM users WHERE id='" . intval($magoutput['user_id']) . "' AND member_id ='" . intval($registerdetails['member_id']) . "'");
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $return['result'] = 'success';
                                    $return['user_info'] = array(
                                        'exp_date' => $row['exp_date'],
                                    );
                                    return json_encode($return);
                                }
                            } else {
                                $return['result'] = 'error';
                                $return['message'] = 'No Record Found!';
                                return json_encode($return);
                            }
                        }
                    } else {
                        $return['result'] = 'error';
                        $return['message'] = 'MAC Address not exists!';
                        return json_encode($return);
                    }
                } elseif (isset($param['eng']) && !empty($param['eng'])) {
                    $mag = $db->query("SELECT user_id FROM `enigma2_devices` WHERE mac='" . $db->real_escape_string($param['eng']) . "'");
                    if ($mag->num_rows > 0) {
                        while ($magoutput = $mag->fetch_assoc()) {
                            $result = $db->query("SELECT * FROM users WHERE id='" . intval($magoutput['user_id']) . "' AND member_id ='" . intval($registerdetails['member_id']) . "'");
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $return['result'] = 'success';
                                    $return['user_info'] = array(
                                        'exp_datee' => $row['exp_date'],
                                    );
                                    return json_encode($return);
                                }
                            }
                        }
                    } else {
                        $return['result'] = 'error';
                        $return['message'] = 'MAC Address not exists!';
                        return json_encode($return);
                    }
                } else {
                    $return['result'] = 'error';
                    $return['message'] = 'No MAC Address found!';
                    return json_encode($return);
                }
            } else {
                $return['result'] = 'error';
                $return['message'] = 'No Record Found!';
                return json_encode($return);
            }
        } else {
            return json_encode($registerdetails);
        }
        return json_encode($return);
    }

    public function userupgrade($param) {
        global $db;
        $registerdetails = self::getresellerid($param);
        if ($registerdetails['result'] == 'success') {
            $param[] = $param['user_data'];
            $param["member_id"] = $registerdetails['member_id'];
            $row = $db->query("SELECT * FROM `reg_users` WHERE `id` = '" . intval($registerdetails['member_id']) . "'");
            if ($row->num_rows > 0) {
                $rUserInfo = $row->fetch_assoc();
                if ($param['extend'] == 'streamlineonly') {
                    $username = $param['username'];
                    if (isset($username) && !empty($username)) {
                        $result = $db->query("SELECT * FROM users WHERE username='".$db->real_escape_string($username)."'");
                    } else {
                        $return['result'] = 'error';
                        $return['message'] = 'No Record Found!';
                    }
                } elseif ($param['extend'] == 'magdevice') {
                    $result = $db->query("SELECT user_id FROM `mag_devices` WHERE mac='" . $db->real_escape_string(base64_encode($param['mac_address_mag'])) . "'");
                } elseif ($param['extend'] == 'engdevice') {
                    $result = $db->query("SELECT user_id FROM `enigma2_devices` WHERE mac='" . $db->real_escape_string($param['mac_address_e2']) . "'");
                }
                if ($result->num_rows != 0) {
                    $rUserdata = $result->fetch_assoc();
                    if ($param['extend'] == 'streamlineonly') {
                        $user_id = $rUserdata['id'];
                    } elseif ($param['extend'] == 'magdevice' || $param['extend'] == 'engdevice') {
                        $user_id = $rUserdata['user_id'];
                    }
                    if (!empty($param["package"])) {
                        $rPackage = getPackage($param["package"]);
                        // Check package is within permissions.
                        if (($rPackage) && (in_array($rUserInfo["member_group_id"], json_decode($rPackage["groups"], True)))) {
                            if ($param["trial"]) {
                                $rCost = $rPackage["trial_credits"];
                            } else {
                                $rCost = $rPackage["official_credits"];
                            }
                            if ($rUserInfo["credits"] >= $rCost) {
                                if ($param["trial"] == '1') {
                                    $rArray["exp_date"] = strtotime('+' . intval($rPackage["trial_duration"]) . ' ' . $rPackage["trial_duration_in"]);
                                    $rArray["is_trial"] = 1;
                                } else {
                                    $rArray["exp_date"] = strtotime('+' . intval($rPackage["official_duration"]) . ' ' . $rPackage["official_duration_in"]);
                                    $rArray["is_trial"] = 0;
                                }
                                $rArray["bouquet"] = $rPackage["bouquets"];
                                $rArray["max_connections"] = $rPackage["max_connections"];
                                $rArray["is_restreamer"] = $rPackage["is_restreamer"];
                                $rArray["reseller_notes"] = $param["reseller_notes"];
                                if (isset($param["is_mag"])) {
                                    $rArray["is_mag"] = 1;
                                }
                                if (isset($param["is_e2"])) {
                                    $rArray["is_e2"] = 1;
                                }
                            } else {
                                $return['result'] = 'error';
                                $return['message'] = 'Not enough credits.';
                                return json_encode($return); // Not enough credits.
                            }
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'Invalid package.';
                            return json_encode($return); // Invalid package.
                        }
                    } else {
                        $return['result'] = 'error';
                        $return['message'] = 'Invalid package.';
                        return json_encode($return); // Invalid package.
                    }
                    $rArray["username"] = $param["username"];
                    $rArray["password"] = $param["password"];

                    if ((($param["is_mag"]) && (!filter_var($param["mac_address_mag"], FILTER_VALIDATE_MAC))) OR ( (strlen($param["mac_address_e2"]) > 0) && (!filter_var($param["mac_address_e2"], FILTER_VALIDATE_MAC)))) {
                        $return['result'] = 'error';
                        $return['message'] = 'An invalid MAC address was entered, please try again.';
                        return json_encode($return);
                    }
                    $isMag = False;
                    $isE2 = False;
                    // Confirm Reseller can generate MAG.
                    if ($rArray["is_mag"]) {
                        if ($rPackage["can_gen_mag"]) {
                            $isMag = True;
                        }
                    }
                    if ($rArray["is_e2"]) {
                        if ($rPackage["can_gen_e2"]) {
                            $isE2 = True;
                        }
                    }
                    $rQuery = '';
                    if ((!$isMag) && (!$isE2) && (($rPackage["only_mag"]) OR ( $rPackage["only_e2"]))) {
                        $return['result'] = 'error';
                        $return['message'] = 'Not allowed to generate normal users!';
                        return json_encode($return);
                    } else {
                        $rQuery .= "UPDATE `users` SET ";
                        $totalUpdateData = count($rArray);
                        $upCounter = 1;
                        foreach ($rArray as $KeyColumn => $val) {
                            $commasel = ",";
                            if ($upCounter == $totalUpdateData) {
                                $commasel = "";
                            }
                            $rQuery .= " ".$db->real_escape_string($KeyColumn)." = '".$db->real_escape_string($val)."' " . $commasel;
                            ++$upCounter;
                        }

                        $rQuery .= " WHERE id = '".intval($user_id)."'";
                        // Checks completed, run,
                        if ($db->query($rQuery)) {
                            $rInsertID = $db->insert_id;
                            if (isset($rCost)) {
                                $rNewCredits = $rUserInfo["credits"] - $rCost;
                                $db->query("UPDATE `reg_users` SET `credits` = " . $rNewCredits . " WHERE `id` = " . intval($rUserInfo["id"]) . ";");
                                if ($isMag) {
                                    $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . $db->real_escape_string($rArray["username"]) . "', '" . $db->real_escape_string($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b> -> <u>Extend MAG</u>] with Package [" . $db->real_escape_string($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                                } else if ($isE2) {
                                    $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . $db->real_escape_string($rArray["username"]) . "', '" . $db->real_escape_string($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b> -> <u>Extend Enigma</u>] with Package [" . $db->real_escape_string($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                                } else {
                                    $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . $db->real_escape_string($rArray["username"]) . "', '" . $db->real_escape_string($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b> -> <u>Extend Line</u>] with Package [" . $db->real_escape_string($rPackage["package_name"]) . "], Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                                }
                                $rUserInfo["credits"] = $rNewCredits;
                            }
                            if ($isMag) {
                                $result = $db->query("SELECT `mag_id` FROM `mag_devices` WHERE `user_id` = " . intval($rInsertID) . " LIMIT 1;");
                                if ((isset($result)) && ($result->num_rows == 1)) {
                                    $db->query("UPDATE `mag_devices` SET `mac` = '" . base64_encode($db->real_escape_string(strtoupper($param["mac_address_mag"]))) . "' WHERE `user_id` = " . intval($rInsertID) . ";");
                                } else {
                                    $db->query("INSERT INTO `mag_devices`(`user_id`, `mac`, `lock_device`) VALUES(" . intval($rInsertID) . ", '" . $db->real_escape_string(base64_encode(strtoupper($param["mac_address_mag"]))) . "', " . intval($rLockDevice) . ");");
                                }
                            } else if ($isE2) {
                                $result = $db->query("SELECT `device_id` FROM `enigma2_devices` WHERE `user_id` = " . intval($rInsertID) . " LIMIT 1;");
                                if ((isset($result)) && ($result->num_rows == 1)) {
                                    $db->query("UPDATE `enigma2_devices` SET `mac` = '" . $db->real_escape_string(strtoupper($param["mac_address_e2"])) . "' WHERE `user_id` = " . intval($rInsertID) . ";");
                                } else {
                                    $db->query("INSERT INTO `enigma2_devices`(`user_id`, `mac`, `lock_device`) VALUES(" . intval($rInsertID) . ", '" . $db->real_escape_string(strtoupper($param["mac_address_e2"])) . "', " . intval($rLockDevice) . ");");
                                }
                            }
                            $return['result'] = 'success';
                            $return['message'] = 'User extend was completed successfully.';
                            return json_encode($return);
                        } else {
                            $return['result'] = 'error';
                            $return['message'] = 'There was an error performing this operation! Please check the form entry and try again.';
                            return json_encode($return);
                        }
                    }
                }
            }
        }
        return json_encode($return);
    }
}
?>