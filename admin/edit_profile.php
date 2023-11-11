<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
$nabillangues = ["" => "Default - EN", "fr" => "French", "es" => "Spanish", "it" => "Italian", "pt" => "Portuguese"];
if (isset($_POST["submit_profile"])) {
    if (strlen($_POST["password"]) < intval($rAdminSettings["pass_length"]) && 0 < intval($rAdminSettings["pass_length"])) {
        $_STATUS = 1;
    }
    if ((strlen($_POST["email"]) == 0 || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) && ($rAdminSettings["change_own_email"] || $rPermissions["is_admin"])) {
        $_STATUS = 2;
    }
    if (0 < strlen($_POST["reseller_dns"]) && !filter_var("http://" . $_POST["reseller_dns"], FILTER_VALIDATE_URL)) {
        $_STATUS = 3;
    }
    if (isset($_POST["sidebar"])) {
        $rSidebar = true;
    } else {
        $rSidebar = false;
    }
    if (isset($_POST["dark_mode"])) {
        $rDarkMode = true;
    } else {
        $rDarkMode = false;
    }
    if (isset($_POST["expanded_sidebar"])) {
        $rExpanded = true;
    } else {
        $rExpanded = false;
    }
    if ($rPermissions["is_admin"]) {
        if (isset($_POST["sucessedit"])) {
            $rsucessedit = true;
        } else {
            $rsucessedit = false;
        }
        $db->query("UPDATE `settings` SET `sucessedit` = '" . intval($rsucessedit) . "';");
    }
    if (!isset($_STATUS)) {
        if (0 < strlen($_POST["password"]) && ($rAdminSettings["change_own_password"] || $rPermissions["is_admin"])) {
            $rPassword = cryptPassword($_POST["password"]);
        } else {
            $rPassword = $rUserInfo["password"];
        }
        if ($rAdminSettings["change_own_email"] || $rPermissions["is_admin"]) {
            $rEmail = $_POST["email"];
        } else {
            $rEmail = $rUserInfo["email"];
        }
        if ($rAdminSettings["change_own_dns"] || $rPermissions["is_admin"]) {
            $rDNS = $_POST["reseller_dns"];
        } else {
            $rDNS = $rUserInfo["reseller_dns"];
        }
        if ($rAdminSettings["change_own_lang"] || $rPermissions["is_admin"]) {
            $bob = $_POST["default_lang"];
        } else {
            $bob = $rUserInfo["default_lang"];
        }
        $db->query("UPDATE `reg_users` SET `password` = '" . ESC($rPassword) . "', `email` = '" . ESC($rEmail) . "', `reseller_dns` = '" . ESC($rDNS) . "', `default_lang` = '" . ESC($bob) . "', `dark_mode` = " . intval($rDarkMode) . ", `sidebar` = " . intval($rSidebar) . ", `expanded_sidebar` = " . intval($rExpanded) . " WHERE `id` = " . intval($rUserInfo["id"]) . ";");
        $rUserInfo = getRegisteredUser($rUserInfo["id"]);
        $rAdminSettings["dark_mode"] = $rUserInfo["dark_mode"];
        $rAdminSettings["expanded_sidebar"] = $rUserInfo["expanded_sidebar"];
        $rSettings["sidebar"] = $rUserInfo["sidebar"];
        header("Location: ./edit_profile.php?successedit&id=" . $_POST["edit"]);
        exit;
    }
}
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if (isset($_GET["successedit"])) {
    $_STATUS = 0;
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
}
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <h4 class=\"page-title\">";
echo $_["profile"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
        echo $_["profile_success"];
        echo "                        </div>\r\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
        echo $_["profile_success"];
        echo "', \"success\");\r\n  \t\t\t\t\t</script>\r\n                        ";
    }
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
            echo str_replace("{num}", $rAdminSettings["pass_length"], $_["profile_fail_1"]);
            echo "                        </div>\r\n\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
            echo str_replace("{num}", $rAdminSettings["pass_length"], $_["profile_fail_1"]);
            echo "', \"warning\");\r\n  \t\t\t\t\t</script>\r\n\t\t\t\t\t\t";
        }
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
            if (!$rSettings["sucessedit"]) {
                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
                echo $_["profile_fail_2"];
                echo "                        </div>\r\n\t\t\t\t\t\t";
            } else {
                echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
                echo $_["profile_fail_2"];
                echo "', \"warning\");\r\n  \t\t\t\t\t</script>\r\n                        ";
            }
        } else {
            if (isset($_STATUS) && $_STATUS == 3) {
                if (!$rSettings["sucessedit"]) {
                    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
                    echo $_["profile_fail_3"];
                    echo "                        </div>\r\n\t\t\t\t\t\t";
                } else {
                    echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", '";
                    echo $_["profile_fail_3"];
                    echo "', \"warning\");\r\n  \t\t\t\t\t</script>\r\n                        ";
                }
            } else {
                if (isset($_STATUS) && $_STATUS == 4) {
                    if (!$rSettings["sucessedit"]) {
                        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            This port already in use, choose another one.\r\n                        </div>\r\n                       ";
                    } else {
                        echo "                    <script type=\"text/javascript\">\r\n  \t\t\t\t\tswal(\"\", \"This port already in use, choose another one.\", \"warning\");\r\n  \t\t\t\t\t</script>\r\n                     ";
                    }
                }
            }
        }
    }
}
echo "                        <div class=\"card\">\r\n                            <div class=\"card-body\">\r\n                                <form action=\"./edit_profile.php\" method=\"POST\" id=\"edit_profile_form\" data-parsley-validate=\"\">\r\n                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#user-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n                                            <div class=\"tab-pane\" id=\"user-details\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"username\">";
echo $_["username"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" value=\"";
echo htmlspecialchars($rUserInfo["username"]);
echo "\" readonly>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        ";
if ($rPermissions["is_admin"] || $rAdminSettings["change_own_password"]) {
    echo "                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"password\">";
    echo $_["change_password"];
    echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"password\" name=\"password\" value=\"\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        ";
}
if ($rPermissions["is_admin"] || $rAdminSettings["change_own_email"]) {
    echo "                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"email\">";
    echo $_["email_address"];
    echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"email\" id=\"email\" class=\"form-control\" name=\"email\" required value=\"";
    echo htmlspecialchars($rUserInfo["email"]);
    echo "\" required data-parsley-trigger=\"change\">\r\n                                                            </div>\r\n                                                        </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
if ($rPermissions["is_reseller"] && $rAdminSettings["change_own_dns"]) {
    echo "                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller_dns\">";
    echo $_["reseller_dns"];
    echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"reseller_dns\" name=\"reseller_dns\" value=\"";
    echo htmlspecialchars($rUserInfo["reseller_dns"]);
    echo "\">\r\n                                                            </div>\r\n                                                        </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
if ($rPermissions["is_admin"] || $rAdminSettings["change_own_lang"]) {
    echo "                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"default_lang\">UI Language</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                 <select type=\"default_lang\" name=\"default_lang\" id=\"default_lang\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                                    ";
    foreach ($nabillangues as $rKey => $rLanguage) {
        echo "                                                                    <option";
        if ($rUserInfo["default_lang"] == $rKey) {
            echo " selected";
        }
        echo " value=\"";
        echo $rKey;
        echo "\">";
        echo $rLanguage;
        echo "</option>\r\n                                                                 ";
    }
    echo "    \r\n                                                            </select>\r\n                                                            </div>\r\n                                                        </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
if ($rPermissions["is_admin"]) {
    echo "  \r\n                                                           <div class=\"form-group row mb-4\">\r\n\t\t\t                                                 <label class=\"col-md-4 col-form-label\" for=\"sucessedit\">Popup Save and Edit</label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"sucessedit\" id=\"sucessedit\" type=\"checkbox\"";
    if ($rSettings["sucessedit"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"dark_mode\">";
    echo $_["dark_mode"];
    echo "</label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"dark_mode\" id=\"dark_mode\" type=\"checkbox\"";
    if ($rUserInfo["dark_mode"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        ";
}
if ($rPermissions["is_reseller"]) {
    echo "  \r\n                                                           <div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"dark_mode\">";
    echo $_["dark_mode"];
    echo "</label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"dark_mode\" id=\"dark_mode\" type=\"checkbox\"";
    if ($rUserInfo["dark_mode"] == 1) {
        echo "checked ";
    }
    echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                        </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"sidebar\">";
echo $_["sidebar_nav"];
echo "</label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"sidebar\" id=\"sidebar\" type=\"checkbox\"";
if ($rUserInfo["sidebar"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"expanded_sidebar\">";
echo $_["expanded_sidebar"];
echo "</label>\r\n                                                            <div class=\"col-md-2\">\r\n                                                                <input name=\"expanded_sidebar\" id=\"expanded_sidebar\" type=\"checkbox\"";
if ($rUserInfo["expanded_sidebar"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <input name=\"submit_profile\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["save_profile"];
echo "\" />\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                        </div> <!-- tab-content -->\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </form>\r\n\r\n                            </div> <!-- end card-body -->\r\n                        </div> <!-- end card-->\r\n                    </div> <!-- end col -->\r\n                </div>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-tabledit/jquery.tabledit.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-remember.js\"></script>\r\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        \r\n        <script>\r\n        (function(\$) {\r\n          \$.fn.inputFilter = function(inputFilter) {\r\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\r\n              if (inputFilter(this.value)) {\r\n                this.oldValue = this.value;\r\n                this.oldSelectionStart = this.selectionStart;\r\n                this.oldSelectionEnd = this.selectionEnd;\r\n              } else if (this.hasOwnProperty(\"oldValue\")) {\r\n                this.value = this.oldValue;\r\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\r\n              }\r\n            });\r\n          };\r\n        }(jQuery));\r\n        \r\n        \$(document).ready(function() {\r\n            \$('select.select2').select2({width: '100%'})\r\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\r\n            elems.forEach(function(html) {\r\n              var switchery = new Switchery(html);\r\n            });\r\n            \r\n            \$(document).keypress(function(event){\r\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n            });\r\n            \r\n            \$(\"form\").attr('autocomplete', 'off');\r\n\r\n            formCache.init();\r\n        });\r\n\r\n        \$(window).bind('beforeunload', function() {\r\n            formCache.save();\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>