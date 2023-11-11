<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

?>
#!/home/xtreamcodes/iptv_xtream_codes/php/bin/php
<?php 
if (php_sapi_name() != "cli") {
    throw new Exception("This application must be run on the command line.");
}
include "functions.php";
$client_id = $rAdminSettings["gdrive_client_id"];
$client_secret = $rAdminSettings["gdrive_client_secret"];
$refresh_token = $rAdminSettings["gdrive_refresh_token"];
if (empty($client_id) || empty($client_secret) || empty($refresh_token)) {
    $message = "GoogleDrive ERROR: Missing Google Drive Settings\n";
    $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . ESC($message) . "', " . intval(time()) . ");");
    echo $message;
    exit(1);
}
$chunk_size = 104857600;
$verbose = true;
$file_binary = "/usr/bin/file";
$check_md5_after_upload = true;
$md5sum_binary = "/usr/bin/md5sum";
if (count($argv) < 2 || 3 < count($argv) || in_array("-h", $argv) || in_array("--help", $argv)) {
    echo "usage: " . $argv[0] . " <file_name> [folder_id]\n\n    where <file_name> is the full path to the file that you want to upload to Google Drive.\n      and [folder_id] is the the folder where you want to upload the file (optional, defaults to root)\n\n";
    exit(1);
}
$file_name = $argv[1];
if (!file_exists($file_name)) {
    $message = "GoogleDrive ERROR: " . $file_name . " is not found on the filesystem\n";
    $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . ESC($message) . "', " . intval(time()) . ");");
    echo $message;
    exit(1);
}
$mime_type = get_mime_type($file_name);
if ($verbose) {
    echo " > mime type detected: " . $mime_type . "\n";
}
$folder_id = "";
if (2 < count($argv)) {
    $folder_id = $argv[2];
}
$access_token = get_access_token();
if (is_null($access_token)) {
    exit(1);
}
if ($verbose) {
    echo "> creating file with Google\n";
}
$location = create_google_file($file_name);
if (is_null($location)) {
    exit(1);
}
$file_size = filesize($file_name);
if ($verbose) {
    echo "> uploading " . $file_name . " to " . $location . "\n";
}
if ($verbose) {
    echo ">   file size: " . (string) ($file_size / pow(1024, 2)) . "MB\n";
}
if ($verbose) {
    echo ">   chunk size: " . (string) ($chunk_size / pow(1024, 2)) . "MB\n\n";
}
$last_response_code = false;
$final_output = NULL;
$last_range = false;
$transaction_counter = 0;
$average_upload_speed = 0;
$do_exponential_backoff = false;
$exponential_backoff_counter = 0;
while ($last_response_code === false || $last_response_code == "308") {
    $transaction_counter++;
    if ($verbose) {
        echo "> request " . $transaction_counter . "\n";
    }
    if ($do_exponential_backoff) {
        $sleep_for = pow(2, $exponential_backoff_counter);
        if ($verbose) {
            echo ">    exponential backoff kicked in, sleeping for " . $sleep_for . " and a bit\n";
        }
        sleep($sleep_for);
        usleep(rand(0, 1000));
        $exponential_backoff_counter++;
        if (5 < $exponential_backoff_counter) {
            $message = "GoogleDrive ERROR: reached time limit of exponential backoff\n";
            $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . ESC($message) . "', " . intval(time()) . ");");
            echo $message;
            exit(1);
        }
    }
    $range_start = 0;
    $range_end = min($chunk_size, $file_size - 1);
    if ($last_range !== false) {
        $last_range = explode("-", $last_range);
        $range_start = (int) $last_range[1] + 1;
        $range_end = min($range_start + $chunk_size, $file_size - 1);
    }
    if ($verbose) {
        echo ">   range " . $range_start . "-" . $range_end . "/" . $file_size . "\n";
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, (string) $location);
    curl_setopt($ch, CURLOPT_PORT, 443);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    $to_send = file_get_contents($file_name, false, NULL, $range_start, $range_end - $range_start + 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $to_send);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $access_token, "Content-Length: " . (string) ($range_end - $range_start + 1), "Content-Type: " . $mime_type, "Content-Range: bytes " . $range_start . "-" . $range_end . "/" . $file_size]);
    $response = parse_response(curl_exec($ch));
    if (is_null($response)) {
        exit(1);
    }
    $post_transaction_info = curl_getinfo($ch);
    curl_close($ch);
    $do_exponential_backoff = false;
    if (isset($response["code"])) {
        if ($response["code"] == "401") {
            if ($verbose) {
                echo ">   access token expired, getting a new one\n";
            }
            $access_token = get_access_token(true);
            if (is_null($access_token)) {
                exit(1);
            }
            $last_response_code = false;
        } else {
            if ($response["code"] == "308") {
                $last_response_code = $response["code"];
                $last_range = $response["headers"]["range"];
                $exponential_backoff_counter = 0;
            } else {
                if ($response["code"] == "503") {
                    $do_exponential_backoff = true;
                    $last_response_code = false;
                } else {
                    if ($response["code"] == "200") {
                        $last_response_code = $response["code"];
                        $final_output = $response;
                    } else {
                        echo "ERROR: I have no idea what to do so here's a variable dump & have fun figuring it out.\npost_transaction_info\n";
                        print_r($post_transaction_info);
                        echo "response\n";
                        print_r($response);
                        exit(1);
                    }
                }
            }
        }
        $average_upload_speed += (int) $post_transaction_info["speed_upload"];
        if ($verbose) {
            echo ">   uploaded " . $post_transaction_info["size_upload"] . "B\n";
        }
    } else {
        $do_exponential_backoff = true;
        $last_response_code = false;
    }
}
if ($last_response_code != "200") {
    $message = "GoogleDrive ERROR: there's no way we should reach this point\n";
    $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . ESC($message) . "', " . intval(time()) . ");");
    echo $message;
    exit(1);
}
if ($verbose) {
    echo "\n> all done!\n";
}
$average_upload_speed /= $transaction_counter;
if ($verbose) {
    echo "\n> average upload speed: " . (string) ($average_upload_speed / pow(1024, 2)) . "MB/s\n";
}
$final_output = json_decode($final_output["body"]);
if ($check_md5_after_upload) {
    if ($verbose) {
        echo "> md5 hash verification ";
    }
    $result = exec($md5sum_binary . " " . $file_name);
    $result = trim($result);
    $result = explode(" ", $result);
    $result = $result[0];
    if ($result != $final_output->md5Checksum) {
        $message = "GoogleDrive ERROR: md5 mismatch; local:" . $result . ", google:" . $final_output->md5Checksum . "\n";
        $db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . ESC($message) . "', " . intval(time()) . ");");
        if ($verbose) {
            echo "FAIL\n";
        }
        echo $message;
        exit(1);
    }
    if ($verbose) {
        echo "OK\n";
    }
}
$message = "GoogleDrive Upload Success: " . $file_name . "\n";
$db->query("INSERT INTO `panel_logs`(`log_message`, `date`) VALUES('" . ESC($message) . "', " . intval(time()) . ");");
echo $final_output->selfLink;
echo "\n";
exit(0);

?>