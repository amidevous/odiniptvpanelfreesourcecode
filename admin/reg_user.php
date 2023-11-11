<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_reguser") && !hasPermissions("adv", "edit_reguser")) {
    exit;
}
if (isset($_POST["submit_user"])) {
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_reguser")) {
            exit;
        }
        $rArray = getRegisteredUser($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        if (!hasPermissions("adv", "add_reguser")) {
            exit;
        }
        $rArray = ["username" => "", "password" => "", "email" => "", "member_group_id" => 1, "verified" => 0, "credits" => 0, "notes" => "", "status" => 1, "owner_id" => 0];
    }
    if (strlen($_POST["username"]) == 0 || strlen($_POST["email"]) == 0) {
        $_STATUS = 1;
    }
    if (0 < strlen($_POST["password"])) {
        $rArray["password"] = cryptPassword($_POST["password"]);
    } else {
        if (!isset($_POST["edit"])) {
            $_STATUS = 1;
        }
    }
    if (!isset($_STATUS)) {
        $rOverride = [];
        foreach ($_POST as $rKey => $rValue) {
            if (substr($rKey, 0, 9) == "override_") {
                $rID = intval(explode("override_", $rKey)[1]);
                $rCredits = $rValue;
                $rOverride[$rID] = ["assign" => 1, "official_credits" => $rCredits];
                unset($_POST[$rKey]);
            }
        }
        $rArray["override_packages"] = json_encode($rOverride);
        if (isset($_POST["verified"])) {
            $rArray["verified"] = 1;
            unset($_POST["verified"]);
        } else {
            $rArray["verified"] = 0;
        }
        unset($_POST["password"]);
        if ($rArray["credits"] != $_POST["credits"]) {
            $rCreditsAdjustment = $_POST["credits"] - $rArray["credits"];
            $rReason = $_POST["credits_reason"];
        }
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
            $rCols = "`id`," . $rCols;
            $rValues = ESC($_POST["edit"]) . "," . $rValues;
        }
        $rQuery = "REPLACE INTO `reg_users`(" . $rCols . ") VALUES(" . $rValues . ");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
            } else {
                $rInsertID = $db->insert_id;
            }
            if (isset($rCreditsAdjustment)) {
                $db->query("INSERT INTO `credits_log`(`target_id`, `admin_id`, `amount`, `date`, `reason`) VALUES(" . $rInsertID . ", " . intval($rUserInfo["id"]) . ", " . ESC($rCreditsAdjustment) . ", " . intval(time()) . ", '" . ESC($rReason) . "');");
            }
            header("Location: ./reg_user.php?successedit&id=" . $rInsertID);
            exit;
        }
        $_STATUS = 2;
    }
}
if (isset($_GET["id"])) {
    $rUser = getRegisteredUser($_GET["id"]);
    if (!$rUser || !hasPermissions("adv", "edit_reguser")) {
        exit;
    }
} else {
    if (!hasPermissions("adv", "add_reguser")) {
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
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./reg_users.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_registered_users"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rUser)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
echo $_["registered_user"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
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
    if (isset($_STATUS) && $_STATUS == 1) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["please_enter_a_username"];
            echo "                        </div>\n\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["please_enter_a_username"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
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
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./reg_user.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"reg_user_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rUser)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rUser["id"];
    echo "\" />\n                                    <input type=\"hidden\" name=\"status\" value=\"";
    echo $rUser["status"];
    echo "\" />\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#user-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#package-override\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-package mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["package_override"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"user-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"username\">";
echo $_["username"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["username"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"password\">";
if (isset($rUser)) {
    echo $_["change"];
    echo " ";
}
echo $_["password"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"password\" name=\"password\" ";
if (!isset($rUser)) {
    echo "value=\"" . generateString(10) . "\" required data-parsley-trigger=\"change\"";
} else {
    echo "value=\"\"";
}
echo ">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"email\">";
echo $_["email_address"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"email\" id=\"email\" class=\"form-control\" name=\"email\" required value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["email"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"member_group_id\">";
echo $_["member_group"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"member_group_id\" id=\"member_group_id\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    ";
foreach (getMemberGroups() as $rGroup) {
    echo "                                                                    <option ";
    if (isset($rUser) && intval($rUser["member_group_id"]) == intval($rGroup["group_id"])) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rGroup["group_id"];
    echo "\">";
    echo htmlspecialchars($rGroup["group_name"]);
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"owner_id\">";
echo $_["owner"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"owner_id\" id=\"owner_id\" class=\"form-control select2\" data-toggle=\"select2\">\n                                                                    <option value=\"0\">";
echo $_["no_owner"];
echo "</option>\n                                                                    ";
foreach (getRegisteredUsers(0) as $rRegUser) {
    echo "                                                                    <option ";
    if (isset($rUser)) {
        if (intval($rUser["owner_id"]) == intval($rRegUser["id"])) {
            echo "selected ";
        }
    } else {
        if (intval($rUserInfo["id"]) == intval($rRegUser["id"])) {
            echo "selected ";
        }
    }
    echo "value=\"";
    echo $rRegUser["id"];
    echo "\">";
    echo $rRegUser["username"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"verified\">";
echo $_["verified"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"verified\" id=\"verified\" type=\"checkbox\"";
if (isset($rUser) && $rUser["verified"] == 1) {
    echo "checked ";
}
echo "data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\n                                                            </div>\n                                                            <label class=\"col-md-4 col-form-label\" for=\"credits\">";
echo $_["credits"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control text-center\" id=\"credits\" onkeypress=\"return isNumberKey(event)\" name=\"credits\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["credits"]);
} else {
    echo "0";
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\" style=\"display: none;\" id=\"credits_reason_div\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"credits_reason\">";
echo $_["reason_for_credits_adjustment"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"credits_reason\" name=\"credits_reason\" value=\"\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller_dns\">";
echo $_["reseller_dns"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"reseller_dns\" name=\"reseller_dns\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["reseller_dns"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"notes\">";
echo $_["notes"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <textarea id=\"notes\" name=\"notes\" class=\"form-control\" rows=\"3\" placeholder=\"\">";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["notes"]);
}
echo "</textarea>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_user\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rUser)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
echo $_["user"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"package-override\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <p class=\"sub-header\">\n                                                            ";
echo $_["leave_the_override_cell_blank"];
echo "                                                        </p>\n                                                        <table class=\"table table-centered mb-0\">\n                                                            <thead>\n                                                                <tr>\n                                                                    <th class=\"text-center\">#</th>\n                                                                    <th>";
echo $_["package"];
echo "</th>\n                                                                    <th class=\"text-center\">";
echo $_["credits"];
echo "</th>\n                                                                    <th class=\"text-center\">";
echo $_["override"];
echo "</th>\n                                                                </tr>\n                                                            </thead>\n                                                            <tbody>\n                                                                ";
if (isset($rUser)) {
    $rOverride = json_decode($rUser["override_packages"], true);
} else {
    $rOverride = [];
}
foreach (getPackages($rUser["member_group_id"]) as $rPackage) {
    if ($rPackage["is_official"]) {
        echo "                                                                <tr>\n                                                                    <td class=\"text-center\">";
        echo $rPackage["id"];
        echo "</td>\n                                                                    <td>";
        echo $rPackage["package_name"];
        echo "</td>\n                                                                    <td class=\"text-center\">";
        echo $rPackage["official_credits"];
        echo "</td>\n                                                                    <td align=\"center\">\n                                                                        <input class=\"form-control\" onkeypress=\"return isNumberKey(event)\" name=\"override_";
        echo $rPackage["id"];
        echo "\" type=\"text\" value=\"";
        if (isset($rOverride[$rPackage["id"]])) {
            echo htmlspecialchars($rOverride[$rPackage["id"]]["official_credits"]);
        }
        echo "\" style=\"width:100px;\" class=\"text-center\" />\n                                                                    </td>\n                                                                </tr>\n                                                                ";
    }
}
echo "                                                            </tbody>\n                                                        </table><br/><br/>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_user\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rUser)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
echo $_["user"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/jquery-tabledit/jquery.tabledit.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/pages/form-remember.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        function selectAll() {\n            \$(\".bouquet-checkbox\").each(function() {\n                \$(this).prop('checked', true);\n            });\n        }\n        \n        function selectNone() {\n            \$(\".bouquet-checkbox\").each(function() {\n                \$(this).prop('checked', false);\n            });\n        }\n        function isValidDate(dateString) {\n              var regEx = /^\\d{4}-\\d{2}-\\d{2}\$/;\n              if(!dateString.match(regEx)) return false;  // Invalid format\n              var d = new Date(dateString);\n              var dNum = d.getTime();\n              if(!dNum && dNum !== 0) return false; // NaN value, Invalid date\n              return d.toISOString().slice(0,10) === dateString;\n        }\n        \n        function isNumberKey(evt) {\n            var charCode = (evt.which) ? evt.which : evt.keyCode;\n            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {\n                return false;\n            } else {\n                return true;\n            }\n        }\n        \n        \$(document).ready(function() {\n            \$('select.select2').select2({width: '100%'})\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n              var switchery = new Switchery(html);\n            });\n            \n            \$('#exp_date').daterangepicker({\n                singleDatePicker: true,\n                showDropdowns: true,\n                minDate: new Date(),\n                locale: {\n                    format: 'YYYY-MM-DD'\n                }\n            });\n            \n            \$(\"#no_expire\").change(function() {\n                if (\$(this).prop(\"checked\")) {\n                    \$(\"#exp_date\").prop(\"disabled\", true);\n                } else {\n                    \$(\"#exp_date\").removeAttr(\"disabled\");\n                }\n            });\n            \n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"#credits\").change(function() {\n                \$(\"#credits_reason_div\").show();\n            });\n            \n            \$(\"#max_connections\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n\n            formCache.init();\n            ";
if (isset($_STATUS)) {
    if ($_STATUS == 0) {
        echo "formCache.clear();";
    } else {
        echo "formCache.fetch();";
    }
}
echo "        });\n\n        \$(window).bind('beforeunload', function() {\n            formCache.save();\n        });\n        </script>\n    </body>\n</html>";

?>