<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_server")) {
    exit;
}
if (isset($_POST["submit_server"])) {
    $rArray = ["server_name" => "", "domain_name" => "", "server_ip" => "", "ssh_password" => "", "vpn_ip" => "", "ssh_port" => "22", "diff_time_main" => 0, "http_broadcast_port" => 8080, "total_clients" => 1000, "system_os" => "", "network_interface" => "", "status" => 3, "enable_geoip" => 0, "can_delete" => 1, "rtmp_port" => 25462, "enable_isp" => 0, "boost_fpm" => 0, "network_guaranteed_speed" => 1000, "https_broadcast_port" => 8443, "whitelist_ips" => [], "timeshift_only" => 0];
    if (strlen($_POST["server_name"]) == 0 || strlen($_POST["server_ip"]) == 0 || strlen($_POST["ssh_port"]) == 0 || strlen($_POST["domain_name"]) == 0 || strlen($_POST["email"]) == 0 || strlen($_POST["http_broadcast_port"]) == 0 || strlen($_POST["https_broadcast_port"]) == 0 || strlen($_POST["rtmp_port"]) == 0 || strlen($_POST["ssh_password"]) == 0) {
        $_STATUS = 1;
    }
    if (!isset($_STATUS)) {
        $rArray["server_ip"] = $_POST["server_ip"];
        $rArray["server_name"] = $_POST["server_name"];
        $rArray["ssh_password"] = base64_encode(base64_encode($_POST["ssh_password"]));
        $rArray["system_os"] = $_POST["system_os"];
        if (isset($_POST["ssh_port"])) {
            $rArray["ssh_port"] = intval($_POST["ssh_port"]);
            unset($_POST["ssh_port"]);
        }
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
        if (isset($_POST["domain_name"])) {
            $rArray["domain_name"] = ESC($_POST["domain_name"]);
            unset($_POST["domain_name"]);
        }
        if (isset($_POST["email"])) {
            $certbot = ESC($_POST["email"]);
        } else {
            $certbot = ESC($rUserInfo["email"]);
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
        $rQuery = "INSERT INTO `streaming_servers`(" . $rCols . ") VALUES(" . $rValues . ");";
        if ($db->query($rQuery)) {
            $rServerID = intval($db->insert_id);
            $rJSON = ["status" => 0, "port" => intval($rArray["ssh_port"]), "domain_name" => ESC($rArray["domain_name"]), "email" => ESC($certbot), "http_broadcast_port" => intval($rArray["http_broadcast_port"]), "https_broadcast_port" => intval($rArray["https_broadcast_port"]), "rtmp_port" => intval($rArray["rtmp_port"]), "host" => $_POST["server_ip"], "password" => $_POST["ssh_password"], "time" => intval(time()), "id" => $rServerID, "type" => "installssl"];
            file_put_contents("/home/xtreamcodes/iptv_xtream_codes/adtools/balancer/" . $rServerID . ".json", json_encode($rJSON));
            header("Location: ./servers.php");
            startcmd();
        } else {
            $_STATUS = 2;
        }
    }
}
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <a href=\"./servers.php\"><li class=\"breadcrumb-item\"><i class=\"mdi mdi-backspace\"></i> ";
echo $_["back_to_servers"];
echo "</li></a>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">Load Balancer SSL Installation</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && 0 < $_STATUS) {
    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["error_occured"];
    echo "                        </div>\n                        ";
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./install_serverssl.php\" method=\"POST\" id=\"server_form\" data-parsley-validate=\"\">\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#server-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-creation mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"server-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"server_name\">";
echo $_["server_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"server_name\" name=\"server_name\" value=\"\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"server_ip\">";
echo $_["server_ip"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"server_ip\" name=\"server_ip\" value=\"\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"domain_name\">Domain Name</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"domain_name\" name=\"domain_name\" value=\"\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"email\">Email Adress</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"email\" name=\"email\" value=\"\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"ssh_password\">SSH Password</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"ssh_password\" name=\"ssh_password\" value=\"\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"system_os\">System OS</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"system_os\" name=\"system_os\"value=\"Ubuntu 18\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"ssh_port\">";
echo $_["ssh_port"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"ssh_port\" name=\"ssh_port\" value=\"22\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"http_broadcast_port\">";
echo $_["http_port"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"http_broadcast_port\" name=\"http_broadcast_port\" value=\"8080\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"https_broadcast_port\">";
echo $_["https_port"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"https_broadcast_port\" name=\"https_broadcast_port\" value=\"8443\" required data-parsley-trigger=\"change\">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"rtmp_port\">";
echo $_["rtmp_port"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"rtmp_port\" name=\"rtmp_port\" value=\"25462\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_server\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["install_server"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        \$(document).ready(function() {\n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"#ssh_port\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n\t\t\t\$(\"#rtmp_port\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n\t\t\t\$(\"#http_broadcast_port\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n\t\t\t\$(\"#https_broadcast_port\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n        });\n        </script>\n    </body>\n</html>";

?>