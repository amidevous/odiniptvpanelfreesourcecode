<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_reseller"] || !$rPermissions["create_sub_resellers"]) {
    exit;
}
if (isset($_POST["submit_credits"]) && isset($_POST["id"])) {
    if (!hasPermissions("reg_user", $_POST["id"])) {
        exit;
    }
    $rUser = getRegisteredUser($_POST["id"]);
    $rCost = intval($_POST["credits"]);
    if ($rUserInfo["credits"] - $rCost < 0 && 0 < $rCost) {
        $_STATUS = 1;
    }
    if ($rUser["credits"] + $rCost < 0) {
        $_STATUS = 1;
    }
    if (!isset($_STATUS) && $rUser) {
        $rNewCredits = floatval($rUserInfo["credits"]) - floatval($rCost);
        $rUpdCredits = floatval($rUser["credits"]) + floatval($rCost);
        $db->query("UPDATE `reg_users` SET `credits` = " . $rNewCredits . " WHERE `id` = " . intval($rUserInfo["id"]) . ";");
        $db->query("UPDATE `reg_users` SET `credits` = " . $rUpdCredits . " WHERE `id` = " . intval($rUser["id"]) . ";");
        $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . ESC($rUser["username"]) . "', '', " . intval(time()) . ", '[<b>UserPanel</b>] -> " . $_["transfer_credits_to"] . " [" . ESC($rUser["username"]) . "] Credits: <font color=\"green\">" . $rUserInfo["credits"] . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
        $db->query("INSERT INTO `credits_log`(`target_id`, `admin_id`, `amount`, `date`, `reason`) VALUES(" . $rUser["id"] . ", " . intval($rUserInfo["id"]) . ", " . ESC($rCost) . ", " . intval(time()) . ", 'Reseller credits transfer');");
        header("Location: ./reg_users.php");
        exit;
    }
}
if (!isset($_GET["id"])) {
    exit;
}
if (!hasPermissions("reg_user", $_GET["id"])) {
    exit;
}
$rUser = getRegisteredUser($_GET["id"]);
if (!$rUser) {
    exit;
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <a href=\"./reg_users.php\"><li class=\"breadcrumb-item\"><i class=\"mdi mdi-backspace\"></i> ";
echo $_["back_to_subresellers"];
echo "</li></a>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["transfer_credits_to"];
echo ": ";
echo $rUser["username"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["transfer_success"];
    echo "                        </div>\n                        ";
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["transfer_fail"];
        echo "                        </div>\n                        ";
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./credits_add.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"credits_form\" data-parsley-validate=\"\">\n                                    <input type=\"hidden\" name=\"id\" value=\"";
echo $_GET["id"];
echo "\" />\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#user-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["transfer_details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"user-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"alert alert-danger\" role=\"alert\" id=\"no-credits\" style=\"display:none;\">\n                                                            <i class=\"mdi mdi-block-helper mr-2\"></i> ";
echo $_["transfer_fail"];
echo "                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-8 col-form-label\" for=\"credits\">";
echo $_["credits_to_transfer"];
echo "</label>\n                                                            <div class=\"col-md-4\">\n                                                                <input type=\"text\" class=\"form-control\" onkeypress=\"return isNumberKey(event)\" id=\"credits\" name=\"credits\" value=\"0\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                            <table class=\"table\" id=\"credits-cost\" style=\"margin-top:30px;\">\n                                                                <thead>\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
echo $_["total_credits"];
echo "</th>\n                                                                        <th class=\"text-center\">";
echo $_["purchase_cost"];
echo "</th>\n                                                                        <th class=\"text-center\">";
echo $_["remaining_credits"];
echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody>\n                                                                    <tr>\n                                                                        <td class=\"text-center\">";
echo number_format($rUserInfo["credits"], 2);
echo "</td>\n                                                                        <td class=\"text-center\" id=\"cost_credits\"></td>\n                                                                        <td class=\"text-center\" id=\"remaining_credits\"></td>\n                                                                    </tr>\n                                                                </tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_credits\" type=\"submit\" class=\"btn btn-primary purchase\" value=\"";
echo $_["purchase"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/js/pages/jquery.number.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        function calculateCredits() {\n            var rCredits = \$(\"#credits\").val();\n            var rUserCredits = ";
echo $rUser["credits"];
echo ";\n\n            if (!\$.isNumeric(rCredits)) {\n                rCredits = 0;\n            }\n            \$(\"#cost_credits\").html(\$.number(rCredits, 2));\n            \$(\"#remaining_credits\").html(\$.number(";
echo $rUserInfo["credits"];
echo " - rCredits, 0));\n            if ((parseFloat(";
echo $rUserInfo["credits"];
echo ") - parseFloat(rCredits) < 0) || (parseFloat(rUserCredits) + parseFloat(rCredits) < 0)) {\n                \$(\"#no-credits\").show()\n                \$(\".purchase\").prop('disabled', true);\n            } else {\n                \$(\"#no-credits\").hide()\n                \$(\".purchase\").prop('disabled', false);\n            }\n            if (rCredits == 0) {\n                \$(\".purchase\").prop('disabled', true);\n            } else {\n                \$(\".purchase\").prop('disabled', false);\n            }\n        }\n       \n        \$(document).ready(function() {\n            \$('select.select2').select2({width: '100%'})\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n              var switchery = new Switchery(html);\n            });\n            \n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"#credits\").on('input', function() {\n                calculateCredits();\n            });\n            \n            \$(\"form\").attr('autocomplete', 'off');\n            calculateCredits();\n        });\n        </script>\n    </body>\n</html>";

?>