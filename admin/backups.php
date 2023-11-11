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
if (isset($_POST["submit_settings"]) && hasPermissions("adv", "settings")) {
    $rArray = getSettings();
    foreach ([""] as $rSetting) {
        if (isset($_POST[$rSetting])) {
            $rArray[$rSetting] = 1;
            unset($_POST[$rSetting]);
        } else {
            $rArray[$rSetting] = 0;
        }
    }
    if (isset($_POST["automatic_backups"])) {
        $rAdminSettings["automatic_backups"] = $_POST["automatic_backups"];
        unset($_POST["automatic_backups"]);
    }
    if (isset($_POST["backups_to_keep"])) {
        $rAdminSettings["backups_to_keep"] = $_POST["backups_to_keep"];
        unset($_POST["backups_to_keep"]);
    }
    if (isset($_POST["automatic_backups_gdrive"])) {
        $rAdminSettings["automatic_backups_gdrive"] = true;
        unset($_POST["automatic_backups_gdrive"]);
    } else {
        $rAdminSettings["automatic_backups_gdrive"] = false;
    }
    if (isset($_POST["gdrive_client_id"])) {
        $rAdminSettings["gdrive_client_id"] = $_POST["gdrive_client_id"];
        unset($_POST["gdrive_client_id"]);
    }
    if (isset($_POST["gdrive_client_secret"])) {
        $rAdminSettings["gdrive_client_secret"] = $_POST["gdrive_client_secret"];
        unset($_POST["gdrive_client_secret"]);
    }
    if (isset($_POST["gdrive_refresh_token"])) {
        $rAdminSettings["gdrive_refresh_token"] = $_POST["gdrive_refresh_token"];
        unset($_POST["gdrive_refresh_token"]);
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
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
}
echo "                <form action=\"./backups.php\" method=\"POST\" id=\"category_form\">\r\n                    <!-- start page title -->\r\n                    <div class=\"row\">\r\n                        <div class=\"col-12\">\r\n                            <div class=\"page-title-box\">\r\n                                <h4 class=\"page-title\">Backups</h4>\r\n                            </div>\r\n                        </div>\r\n                    </div>     \r\n                    <!-- end page title --> \r\n                    <div class=\"row\">\r\n                        <div class=\"col-xl-12\">\r\n                            \r\n                            <div class=\"card\">\r\n                                <div class=\"card-body\">\r\n                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t";
if (hasPermissions("adv", "database")) {
    echo "                                            <li class=\"nav-item\">\r\n\t\t\t\t\t\t\t\t\t\t        <a href=\"#backups\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\r\n\t\t\t\t\t\t\t\t\t\t\t    <i class=\"mdi mdi-backup-restore mr-1\"></i>\r\n\t\t\t\t\t\t\t\t\t\t\t    <span class=\"d-none d-sm-inline\">Backups</span>\r\n\t\t\t\t\t\t\t\t\t\t        </a>\r\n\t\t\t\t\t\t\t\t\t        </li>\r\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n\t\t\t\t\t\t\t\t\t\t";
if (hasPermissions("adv", "database")) {
    echo "\t\t\t\t\t\t\t\t\t\t\t<div class=\"tab-content b-0 mb-0 pt-0\">\r\n\t\t\t\t\t\t\t\t\t            <div class=\"tab-pane\" id=\"backups\">\r\n\t\t\t\t\t\t\t\t\t\t            <div class=\"row\">\r\n                                                        <div class=\"col-12\">\r\n                                                            <div class=\"form-group row mb-4\">\r\n                                                                <label class=\"col-md-4 col-form-label\" for=\"automatic_backups\">";
    echo $_["automatic_backups"];
    echo "</label>\r\n                                                                <div class=\"col-md-2\">\r\n                                                                    <select name=\"automatic_backups\" id=\"automatic_backups\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                                    ";
    foreach (["off" => "Off", "hourly" => "Hourly", "daily" => "Daily", "weekly" => "Weekly", "monthly" => "Monthly"] as $rType => $rText) {
        echo "                                                                        <option";
        if ($rAdminSettings["automatic_backups"] == $rType) {
            echo " selected";
        }
        echo " value=\"";
        echo $rType;
        echo "\">";
        echo $rText;
        echo "</option>\r\n                                                                    ";
    }
    echo "                                                                    </select>\r\n                                                                </div>\r\n                                                                <label class=\"col-md-4 col-form-label\" for=\"backups_to_keep\">";
    echo $_["backups_to_keep"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["enter_for_unlimited"];
    echo "\" class=\"mdi mdi-information\"></i></label>\r\n                                                                <div class=\"col-md-2\">\r\n                                                                    <input type=\"text\" class=\"form-control\" id=\"backups_to_keep\" name=\"backups_to_keep\" value=\"";
    echo htmlspecialchars($rAdminSettings["backups_to_keep"] ? $rAdminSettings["backups_to_keep"] : 0);
    echo "\">\r\n                                                                </div>\r\n                                                            </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"automatic_backups_gdrive\">";
    echo $_["automatic_backups_gdrive"];
    echo " </label>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\">\r\n                                                                    <input name=\"automatic_backups_gdrive\" id=\"automatic_backups_gdrive\" type=\"checkbox\"";
    if ($rAdminSettings["automatic_backups_gdrive"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                                 </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"gdrive_client_id\">";
    echo $_["gdrive_client_id"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["gdrive_client_id_info"];
    echo "\" class=\"mdi mdi-information\"></i></label>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <input type=\"text\" class=\"form-control\" id=\"gdrive_client_id\" name=\"gdrive_client_id\" value=\"";
    echo htmlspecialchars($rAdminSettings["gdrive_client_id"] ? $rAdminSettings["gdrive_client_id"] : "");
    echo "\">\r\n                                                                </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"gdrive_client_secret\">";
    echo $_["gdrive_client_secret"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\"     data-original-title=\"";
    echo $_["gdrive_client_secret_info"];
    echo "\" class=\"mdi mdi-information\"></i></label>                                                           \r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <input type=\"text\" class=\"form-control\" id=\"gdrive_client_secret\" name=\"gdrive_client_secret\" value=\"";
    echo htmlspecialchars($rAdminSettings["gdrive_client_secret"] ? $rAdminSettings["gdrive_client_secret"] : "");
    echo "\">                                                           \r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <label class=\"col-md-4 col-form-label\" for=\"gdrive_refresh_token\">";
    echo $_["gdrive_refresh_token"];
    echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
    echo $_["gdrive_refresh_token_info"];
    echo "\" class=\"mdi mdi-information\"></i></label>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <input type=\"text\" class=\"form-control\" id=\"gdrive_refresh_token\" name=\"gdrive_refresh_token\" value=\"";
    echo htmlspecialchars($rAdminSettings["gdrive_refresh_token"] ? $rAdminSettings["gdrive_refresh_token"] : "");
    echo "\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t     <label class=\"col-md-4 col-form-label\"> <a href=\"#\" data-toggle=\"modal\" data-target=\"#HowToModal\">[How To Text]</a> <a href=\"https://www.youtube.com/watch?v=FAM_4J7ywcE\" target=\"_blank\">[How To Youtube]</a></label>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n                                                            <table class=\"table table-borderless mb-0\" id=\"datatable-backups\">\r\n                                                                <thead class=\"thead-light\">\r\n                                                                    <tr>\r\n                                                                        <th class=\"text-center\">";
    echo $_["date"];
    echo "</th>\r\n                                                                        <th class=\"text-center\">";
    echo $_["filename"];
    echo "</th>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <th class=\"text-center\">";
    echo $_["filesize"];
    echo "</th>\r\n                                                                        <th class=\"text-center\">";
    echo $_["actions"];
    echo "</th>\r\n                                                                    </tr>\r\n                                                                </thead>\r\n                                                            <tbody></tbody>\r\n                                                            </table>\r\n                                                        </div> <!-- end col -->\r\n                                                    </div>\r\n\t\t\t\t\t\t\t\t\t\t            <ul class=\"list-inline wizard mb-0\" style=\"margin-top:30px;\">\r\n                                                        <li class=\"list-inline-item float-right\">\r\n                                                            <button id=\"create_backup\" onClick=\"api('', 'backup')\" class=\"btn btn-info\">Create Backup Now</button>\r\n                                                            <input name=\"submit_settings\" type=\"submit\" class=\"btn btn-primary\" value=\"Save Changes\" />\r\n                                                        </li>\r\n                                                    </ul>\r\n\t\t\t\t\t\t\t\t\t            </div>\r\n\t\t\t\t\t\t\t\t            </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"modal fade\" id=\"HowToModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"HowToModalTitle\" aria-hidden=\"true\">\r\n\t\t\t\t\t\t\t\t\t\t\t    <div class=\"modal-dialog\" role=\"document\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t    <div class=\"modal-content\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t    <div class=\"modal-header\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <h5 class=\"modal-title\" id=\"exampleModalLongTitle\">How To Setup Google Drive</h5>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"modal-body\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <div class=\"col-12\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <h1>Get API Credentials</h1>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p>The first step is to get our Google Drive credentials which too are:</p>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <ul>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <li>A <strong>Client Id</strong> and a <strong>Client Secret</strong></li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li>A <strong>redirect URI</strong> and <strong>refresh token</strong></li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p>You need to have a Google account for the steps below:</p>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <ol>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <li>Go to <a href=\"https://console.cloud.google.com/cloud-resource-manager\"  target=\"_blank\">https://console.cloud.google.com/cloud-resource-manager</a> and click on the button <strong>\"Create Project\"</strong>. Give a name to the project, click on <strong>\"Create\"</strong> to submit, and when for the creation to complete.</li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li>Once the project is created, select it. You will be redirected to the console dashboard. On the sidebar menu, click on the menu <strong>\"APIs & Services\"</strong>.Locate the button labeled <strong>\"ENABLES APIS AND SERVICES\"</strong> and click on it.</li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li>You will be redirected to a page that lists all the Google APIs. Search for Google Drive API and click on it in the results list. On the next page, click on the button <strong>\"Enable\"</strong>, you will be redirected to a page when the API will be enabled.</li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li>On the new page, click on the menu in the sidebar labeled <strong>\"Credentials\"</strong>. On the next page, locate the drop-down button labeled <strong>\"CREATE CREDENTIALS\"</strong> click on it and select the drop-down menu labeled <strong>\"OAuth client ID\"</strong></li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <li>On the new page, click on the button <strong>\"CONFIGURE CONSENT SCREEN\"</strong> then check <strong>\"External\"</strong> for the User Type. You can select \"Internal\" if the account you use is inside an organization which is not my case. Click on the button <strong>\"Create\"</strong><br />On the new page, we have a page with 4 steps\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t        <ol>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t        <li>In step 1, give your application the name and select your email address as the value for the input labeled <strong>\"User support email\"</strong>. Also, give your email address as value for input labeled <strong>\"Developer contact information\"</strong>. <br />You can ignore other inputs since they aren't mandatory. Click on <strong>\"Save and Continue\"</strong></li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t        <br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t        <li>On step 2, no change to do, so click on <strong>\"Save and Continue.\"</strong></li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li>In step 3, add a Test user with your email address. Note that you can add up to 100. Click on <strong>\"Save and Continue\"</strong></li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t        <br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t        <li>Step 4 is just a summary of the previous steps. Click on the button <strong>\"BACK TO DASHBOARD.\"</strong></li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t        </ol>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    </li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li>On the new page, click on the menu in the sidebar labeled <strong>\"Credentials\".</strong> On the next page, locate the drop-down button labeled <strong>\"CREATE CREDENTIALS\"</strong> click on it and select the drop-down menu labeled <strong>\"OAuth client ID\"</strong><br />On the next page, select <strong>\"Web Application\"</strong> as the value of the input labeled \"Application type,\" Give a name to our Web Application. <br />In the section <strong>\"Authorized redirect URIs,\"</strong> click on <strong>\"ADD URI\"</strong> and fill the input with this value: https://developers.google.com/oauthplayground <br />Click on <strong>\"CREATE\"</strong> to submit, and now we have our Client ID and Client Secret.<br /></li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li>Let's get the refresh token. <br />Go to <a href=\"https://developers.google.com/oauthplayground\" target=\"_blank\">https://developers.google.com/oauthplayground</a><br />For more info how to get refresh token watch this <a href=\"https://www.youtube.com/watch?v=FAM_4J7ywcE\" target=\"_blank\">video on youtube.</a></li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<br>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<li>After getting refresh token you all set. Just write Client ID, Client Secret and Refresh Token to CK Mods Panel and your backup will work.</li>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    </ol>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"modal-footer\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t    <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t\t\t\t\t</div><!-- End Modal -->\r\n\t\t\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t\t    ";
}
echo "                                        </div> <!-- tab-content -->\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </div> <!-- end card-body -->\r\n                            </div> <!-- end card-->\r\n                        </div> <!-- end col -->\r\n                    </div>\r\n                </form>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\t\t<link rel=\"stylesheet\" href=\"assets/js/minified/themes/default.min.css\" id=\"theme-style\" />\r\n\t\t<script src=\"assets/js/minified/sceditor.min.js\"></script>\r\n        <script src=\"assets/js/minified/formats/xhtml.js\"></script>\t\t\t\t\t\t \r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        \r\n        <script>\r\n        function api(rID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('";
echo $_["are_you_sure_you_want_to_delete_this_backup"];
echo "') == false) {\r\n                    return;\r\n                }\r\n            } else if (rType == \"restore\") {\r\n                if (confirm('";
echo $_["are_you_sure_you_want_to_restore_from_this_backup"];
echo "') == false) {\r\n                    return;\r\n                } else {\r\n\t\t\t\t\t\$.toast(\"";
echo $_["restoring_backup"];
echo "\");\r\n\t\t\t\t\t\$(\".content-page\").fadeOut();\r\n\t\t\t\t}\r\n            } else if (rType == \"backup\") {\r\n                \$(\"#create_backup\").attr(\"disabled\", true);\r\n\t\t\t} else if (rType == \"download\") {\r\n                window.location.href = \"./api.php?action=download&filename=\" + encodeURIComponent(rID);\r\n            }\r\n            \$.getJSON(\"./api.php?action=backup&sub=\" + rType + \"&filename=\" + encodeURIComponent(rID), function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"delete\") {\r\n                        \$.each(\$('.tooltip'), function (index, element) {\r\n                            \$(this).remove();\r\n                        });\r\n                        \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                        \$.toast(\"";
echo $_["backup_successfully_deleted"];
echo "\");\r\n                    } else if (rType == \"restore\") {\r\n                        \$.toast(\"";
echo $_["restored_from_backup"];
echo "\");\r\n\t\t\t\t\t\t\$(\".content-page\").fadeIn();\r\n                    } else if (rType == \"backup\") {\r\n                        \$.toast(\"";
echo $_["backup_has_been_successfully_generated"];
echo "\");\r\n                        \$(\"#create_backup\").attr(\"disabled\", false);\r\n                    }\r\n\t\t\t\t\t\$(\"#datatable-backups\").DataTable().ajax.reload(null, false);\r\n                } else {\r\n                    \$.toast(\"Backup Downloading successfully\");\r\n                    if (rType == \"backup\") {\r\n                        \$(\"#create_backup\").attr(\"disabled\", false);\r\n                    }\r\n\t\t\t\t\tif (!\$(\".content-page\").is(\":visible\")) {\r\n\t\t\t\t\t\t\$(\".content-page\").fadeIn();\r\n\t\t\t\t\t}\r\n                }\r\n            });\r\n        }\r\n        \$(document).ready(function() {\r\n\t\t\t\$('select').select2({width: '100%'});\r\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\r\n            elems.forEach(function(html) {\r\n              var switchery = new Switchery(html);\r\n            });\r\n            \$(window).keypress(function(event){\r\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n            });\r\n\t\t\t\r\n            \$(\"#datatable-backups\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    }\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                },\r\n\t\t\t\tbInfo: false,\r\n\t\t\t\tpaging: false,\r\n\t\t\t\tsearching: false,\r\n\t\t\t\tbSort: false,\r\n                responsive: false,\r\n\t\t\t\tprocessing: true,\r\n                serverSide: true,\r\n                ajax: {\r\n                    url: \"./table_search.php\",\r\n                    \"data\": function(d) {\r\n                        d.id = \"backups\"\r\n                    }\r\n                },\r\n                order: [[ 0, \"desc\" ]],\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2,3]}\r\n                ],\r\n\t\t\t\t\r\n            });\r\n            \$(\"#datatable-backups\").css(\"width\", \"100%\");\r\n            \$(\"form\").attr('autocomplete', 'off');\r\n            \$(\"#backups_to_keep\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\r\n        });\r\n\t\t</script>\r\n    </body>\r\n</html>";

?>