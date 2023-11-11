<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "functions.php";
if (!$rPermissions["is_reseller"] || !$rPermissions["allow_import"]) {
    exit;
}
if (!isset($_SESSION["hash"])) {
    header("Location: ./login.php");
    exit;
}
if (isset($_POST["submit_secret"])) {
    $salt = "!SMARTERS!";
    $return = [];
    $result = $db->query("CREATE TABLE IF NOT EXISTS reseller_credentials (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,member_id VARCHAR(30), api_key VARCHAR(100) NOT NULL,ip_allow VARCHAR(30))");
    $password = resellerapi_generateRandomString(15);
    $encrypted = resellerapi_encrypt($password, $salt);
    $results = $db->query("SELECT * FROM `reseller_credentials` WHERE member_id = '" . intval($rUserInfo["id"]) . "'");
    if (0 < $results->num_rows) {
        $rQuery = "UPDATE `reseller_credentials` SET api_key = '" . $db->real_escape_string($encrypted) . "', ip_allow= '' WHERE member_id = '" . intval($rUserInfo["id"]) . "'";
    } else {
        $rQuery = "INSERT INTO `reseller_credentials`(`member_id`,`api_key`, `ip_allow`) VALUES('" . intval($rUserInfo["id"]) . "','" . $db->real_escape_string($encrypted) . "','11.11.11.11');";
    }
    if ($db->query($rQuery)) {
        $return["result"] = "success";
        $return["msg"] = "API Credential genrated/updated successfully!";
    }
}
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
$api_key = "";
$results = $db->query("SELECT * FROM `reseller_credentials` WHERE member_id = '" . intval($rUserInfo["id"]) . "'");
if (0 < $results->num_rows) {
    $salt = "!SMARTERS!";
    while ($row = $results->fetch_assoc()) {
        $api_key = resellerapi_decrypt($row["api_key"], $salt);
    }
}
if ($rSettings["sidebar"]) {
    echo "    <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\r\n            ";
} else {
    echo "                <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\r\n                    ";
}
echo "                    <!-- start page title -->\r\n                    <div class=\"row\">\r\n                        <div class=\"col-12\">\r\n                            <div class=\"page-title-box\">\r\n                                <div class=\"page-title-right\">\r\n                                    <ol class=\"breadcrumb m-0\">\r\n                                        <a href=\"./reseller.php\"><li class=\"breadcrumb-item\"><i class=\"mdi mdi-backspace\"></i> Back to Dashboard</li></a>\r\n                                    </ol>\r\n                                </div> \r\n                                <h4 class=\"page-title\">API Credentials</h4>\r\n                            </div>\r\n                        </div>\r\n                    </div>     \r\n                    <!-- end page title --> \r\n                    <div class=\"row\">\r\n                        <div class=\"col-xl-12\">\r\n                            ";
echo $result["result"] == "success" ? $result["msg"] : "";
echo "                            <div class=\"card\">\r\n                                <div class=\"card-body\">\r\n                                    <form action=\"\" method=\"POST\" id=\"ticket_form\"> \r\n                                        <div id=\"basicwizard\">\r\n                                            <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                                <li class=\"nav-item\">\r\n                                                    <a href=\"#\" style=\"color: #fff; background-color: #5089de;\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                        <i class=\"mdi mdi-key mr-1\"></i>\r\n                                                        <span class=\"d-none d-sm-inline\">Your Secret API Key</span>\r\n                                                    </a>\r\n                                                </li>\r\n                                            </ul>\r\n                                            <div class=\"tab-content b-0 mb-0 pt-0\"> \r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <center><b>";
echo $api_key;
echo "</b></center>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"next list-inline-item float-right\">\r\n                                                        <input name=\"submit_secret\" type=\"submit\" class=\"btn btn-primary\" value=\"Generate\" />\r\n                                                    </li>\r\n                                                </ul>\r\n\r\n                                            </div> <!-- tab-content -->\r\n                                        </div> <!-- end #basicwizard-->\r\n                                    </form>\r\n\r\n                                </div> <!-- end card-body -->\r\n                            </div> <!-- end card-->\r\n                        </div> <!-- end col -->\r\n                    </div>\r\n                </div> <!-- end container -->\r\n            </div>\r\n            <!-- end wrapper -->\r\n            ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "            <!-- Footer Start -->\r\n            <footer class=\"footer\">\r\n                <div class=\"container-fluid\">\r\n                    <div class=\"row\">\r\n                        <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                    </div>\r\n                </div>\r\n            </footer>\r\n            <!-- end Footer -->\r\n\r\n            <!-- Vendor js -->\r\n            <script src=\"assets/js/vendor.min.js\"></script>\r\n            <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n            <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n            <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n            <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n            <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n            <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n            <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n            <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n            <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n\r\n            <!-- Plugins js-->\r\n            <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n\r\n            <!-- Tree view js -->\r\n            <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n            <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n            <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n\r\n            <!-- App js-->\r\n            <script src=\"assets/js/app.min.js\"></script>\r\n\r\n            <script>\r\n                \$(document).ready(function () {\r\n                    \$(document).keypress(function (event) {\r\n                        if (event.which == '13') {\r\n                            event.preventDefault();\r\n                        }\r\n                    });\r\n\r\n                    \$(\"form\").attr('autocomplete', 'off');\r\n                });\r\n            </script>\r\n            </body>\r\n            </html>";
function resellerapi_decrypt($q, $salt = NULL)
{
    $qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($salt), base64_decode($q), MCRYPT_MODE_CBC, md5(md5($salt))), "\0");
    return $qDecoded;
}
function resellerapi_encrypt($q, $salt = NULL)
{
    $qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($salt), $q, MCRYPT_MODE_CBC, md5(md5($salt))));
    return $qEncoded;
}
function resellerapi_generateRandomString($length = 10)
{
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>