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
if (isset($_POST["submit_user"])) {
    if (isset($_POST["edit"])) {
        if (!hasPermissions("reg_user", $_POST["edit"])) {
            exit;
        }
        $rArray = getRegisteredUser($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        $rArray = ["username" => "", "date_registered" => time(), "password" => "", "email" => "", "reseller_dns" => "", "member_group_id" => 1, "verified" => 1, "credits" => 0, "notes" => "", "status" => 1, "owner_id" => intval($rUserInfo["id"])];
    }
    if ((strlen($_POST["username"]) == 0 || strlen($_POST["password"]) == 0 || strlen($_POST["email"]) == 0) && !isset($_POST["edit"])) {
        $_STATUS = 1;
    }
    $rUser = $_POST;
    if (!isset($_POST["edit"])) {
        $rCost = intval($rPermissions["create_sub_resellers_price"]);
        if ($rUserInfo["credits"] - $rCost < 0) {
            $_STATUS = 3;
        }
        $result = $db->query("SELECT `id` FROM `reg_users` WHERE `username` = '" . ESC($_POST["username"]) . "';");
        if ($result && 0 < $result->num_rows) {
            $_STATUS = 4;
        }
        $result = $db->query("SELECT `subreseller` FROM `subreseller_setup` WHERE `reseller` = " . intval($rUserInfo["member_group_id"]) . ";");
        if ($result && 0 < $result->num_rows) {
            $rArray["member_group_id"] = intval($result->fetch_assoc()["subreseller"]);
        } else {
            $_STATUS = 5;
        }
    }
    if (!isset($_STATUS)) {
        if (!isset($_POST["edit"])) {
            $rArray["username"] = $_POST["username"];
        }
        if (!strlen($_POST["password"]) == 0) {
            $rArray["password"] = cryptPassword($_POST["password"]);
        }
        if (isset($_POST["email"])) {
            $rArray["email"] = $_POST["email"];
        }
        if (isset($_POST["reseller_dns"])) {
            $rArray["reseller_dns"] = $_POST["reseller_dns"];
        }
        if (isset($_POST["notes"])) {
            $rArray["notes"] = $_POST["notes"];
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
            if (isset($rCost)) {
                $rNewCredits = floatval($rUserInfo["credits"]) - $rCost;
                $db->query("UPDATE `reg_users` SET `credits` = " . floatval($rNewCredits) . " WHERE `id` = " . intval($rUserInfo["id"]) . ";");
                $db->query("INSERT INTO `reg_userlog`(`owner`, `username`, `password`, `date`, `type`) VALUES(" . intval($rUserInfo["id"]) . ", '" . ESC($rArray["username"]) . "', '" . ESC($rArray["password"]) . "', " . intval(time()) . ", '[<b>UserPanel</b>] -> " . $_["new_subreseller"] . " [" . ESC($_POST["username"]) . "] Credits: <font color=\"green\">" . floatval($rUserInfo["credits"]) . "</font> -> <font color=\"red\">" . $rNewCredits . "</font>');");
                $rUserInfo["credits"] = $rNewCredits;
            }
            header("Location: ./subreseller.php?id=" . $rInsertID);
            exit;
        }
        $_STATUS = 2;
    }
}
if (isset($_GET["id"])) {
    if (!hasPermissions("reg_user", $_GET["id"])) {
        exit;
    }
    $rUser = getRegisteredUser($_GET["id"]);
    if (!$rUser) {
        exit;
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./reg_users.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_subresellers"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rUser)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
echo $_["subreseller"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["subreseller_operation"];
    echo "                        </div>\n                        ";
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["please_ensure_you"];
        echo "                        </div>\n                        ";
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                           ";
            echo $_["generic_fail"];
            echo " \n                        </div>\n                        ";
        } else {
            if (isset($_STATUS) && $_STATUS == 3) {
                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                echo $_["you_don't_have_enough"];
                echo "                        </div>\n                        ";
            } else {
                if (isset($_STATUS) && $_STATUS == 4) {
                    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                    echo $_["this_username_has_already"];
                    echo "                        </div>\n                        ";
                } else {
                    if (isset($_STATUS) && $_STATUS == 5) {
                        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                        echo $_["your_group_has_not_been"];
                        echo "                        </div>\n                        ";
                    }
                }
            }
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./subreseller.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"user_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($_GET["id"])) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rUser["id"];
    echo "\" />\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#user-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            ";
if (!isset($_GET["id"])) {
    echo "                                            <li class=\"nav-item\">\n                                                <a href=\"#review-purchase\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-book-open-variant mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["review_purchase"];
    echo "</span>\n                                                </a>\n                                            </li>\n                                            ";
}
echo "                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"user-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"username\">";
echo $_["username"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input ";
if (isset($_GET["id"])) {
    echo "disabled ";
}
echo "type=\"text\" class=\"form-control\" id=\"username\" name=\"username\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["username"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"password\">";
if (isset($_GET["id"])) {
    echo $_["change"];
    echo " ";
}
echo $_["password"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" id=\"password\" name=\"password\" ";
if (!isset($rUser)) {
    echo "value=\"" . generateString(10) . "\" required data-parsley-trigger=\"change\"";
} else {
    echo "value=\"\"";
}
echo " required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"email\">";
echo $_["email_address"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"email\" name=\"email\" value=\"";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["email"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
if ($rPermissions["is_reseller"] && $rAdminSettings["change_own_dns"]) {
    echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller_dns\">";
    echo $_["reseller_dns"];
    echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"reseller_dns\" name=\"reseller_dns\" value=\"";
    if (isset($rUser)) {
        echo htmlspecialchars($rUser["reseller_dns"]);
    }
    echo "\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"notes\">";
echo $_["notes"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <textarea id=\"notes\" name=\"notes\" class=\"form-control\" rows=\"3\" placeholder=\"\">";
if (isset($rUser)) {
    echo htmlspecialchars($rUser["notes"]);
}
echo "</textarea>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        ";
if (!isset($_GET["id"])) {
    echo "                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["next"];
    echo "</a>\n                                                        ";
} else {
    echo "                                                        <input name=\"submit_user\" type=\"submit\" class=\"btn btn-primary\" value=\"";
    echo $_["edit"];
    echo "\" />\n                                                        ";
}
echo "                                                    </li>\n                                                </ul>\n                                            </div>\n                                            ";
if (!isset($_GET["id"])) {
    echo "                                            <div class=\"tab-pane\" id=\"review-purchase\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        ";
    if ($rUserInfo["credits"] - $rPermissions["create_sub_resellers_price"] < 0) {
        echo "                                                        <div class=\"alert alert-danger\" role=\"alert\" id=\"no-credits\">\n                                                            <i class=\"mdi mdi-block-helper mr-2\"></i> ";
        echo $_["you_do_not_have_enough_credits"];
        echo "                                                        </div>\n                                                        ";
    }
    echo "                                                        <div class=\"form-group row mb-4\">\n                                                            <table class=\"table\" id=\"credits-cost\">\n                                                                <thead>\n                                                                    <tr>\n                                                                        <th class=\"text-center\">";
    echo $_["total_credits"];
    echo "</th>\n                                                                        <th class=\"text-center\">";
    echo $_["purchase_cost"];
    echo "</th>\n                                                                        <th class=\"text-center\">";
    echo $_["remaining_credits"];
    echo "</th>\n                                                                    </tr>\n                                                                </thead>\n                                                                <tbody>\n                                                                    <tr>\n                                                                        <td class=\"text-center\">";
    echo number_format($rUserInfo["credits"], 2);
    echo "</td>\n                                                                        <td class=\"text-center\" id=\"cost_credits\">";
    echo number_format($rPermissions["create_sub_resellers_price"], 2);
    echo "</td>\n                                                                        <td class=\"text-center\" id=\"remaining_credits\">";
    echo number_format($rUserInfo["credits"] - $rPermissions["create_sub_resellers_price"], 2);
    echo "</td>\n                                                                    </tr>\n                                                                </tbody>\n                                                            </table>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
    echo $_["prev"];
    echo "</a>\n                                                    </li>\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input ";
    if ($rUserInfo["credits"] - $rPermissions["create_sub_resellers_price"] < 0) {
        echo "disabled ";
    }
    echo "name=\"submit_user\" type=\"submit\" class=\"btn btn-primary purchase\" value=\"";
    echo $_["purchase"];
    echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            ";
}
echo "                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/js/pages/jquery.number.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n       \n        \$(document).ready(function() {\n            \$('select.select2').select2({width: '100%'})\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n              var switchery = new Switchery(html);\n            });\n            \n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"form\").attr('autocomplete', 'off');\n        });\n        </script>\n    </body>\n</html>";

?>