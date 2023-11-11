<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "subreseller")) {
    exit;
}
if (isset($_POST["submit_subreseller"])) {
    if (!(0 < intval($_POST["reseller"]) && 0 < intval($_POST["subreseller"]) && intval($_POST["reseller"]) != intval($_POST["subreseller"]))) {
        $_STATUS = 1;
    }
    if (!isset($_STATUS)) {
        $rArray = ["reseller" => 0, "subreseller" => 0];
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
        $rQuery = "REPLACE INTO `subreseller_setup`(" . $rCols . ") VALUES(" . $rValues . ");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
            } else {
                $rInsertID = $db->insert_id;
            }
        }
        if (isset($rInsertID)) {
            header("Location: ./subresellers.php");
            exit;
        }
        $_STATUS = 1;
    }
}
if (isset($_GET["id"])) {
    $rSubreseller = getSubresellerSetup($_GET["id"]);
    if (!$rSubreseller) {
        exit;
    }
}
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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n\t\t\t\t\t\t\t\t\t<li>\r\n                                        <a href=\"./subresellers.php\">\r\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_subresellers"];
echo "</button>\r\n\t\t\t\t\t\t\t\t\t    </a>\t\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $_["subreseller_setup"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        ";
if (isset($_STATUS) && 0 < $_STATUS) {
    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
    echo $_["there_was_an_error"];
    echo "                        </div>\r\n                        ";
}
echo "                        <div class=\"card\">\r\n                            <div class=\"card-body\">\r\n                                <form action=\"./subreseller_setup.php";
if (isset($rSubreseller)) {
    echo "?id=" . $rSubreseller["id"];
}
echo "\" method=\"POST\" id=\"subreseller_form\">\r\n                                    ";
if (isset($rSubreseller)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rSubreseller["id"];
    echo "\" />\r\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#setup\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\"> ";
echo $_["setup"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n                                            <div class=\"tab-pane\" id=\"setup\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <p class=\"sub-header\">\r\n                                                             ";
echo $_["select_a_master_reseller"];
echo "                                                        </p>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"reseller\"> ";
echo $_["master_group"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"reseller\" id=\"reseller\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    ";
foreach (getMemberGroups() as $rGroup) {
    if ($rGroup["is_reseller"] == 1) {
        echo "                                                                        <option ";
        if (isset($rSubreseller) && intval($rSubreseller["reseller"]) == intval($rGroup["group_id"])) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rGroup["group_id"];
        echo "\">";
        echo $rGroup["group_name"];
        echo "</option>\r\n                                                                        ";
    }
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"subreseller\">  ";
echo $_["subreseller"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <select name=\"subreseller\" id=\"subreseller\" class=\"form-control select2\" data-toggle=\"select2\">\r\n                                                                    ";
foreach (getMemberGroups() as $rGroup) {
    if ($rGroup["is_reseller"] == 1) {
        echo "                                                                        <option ";
        if (isset($rSubreseller) && intval($rSubreseller["subreseller"]) == intval($rGroup["group_id"])) {
            echo "selected ";
        }
        echo "value=\"";
        echo $rGroup["group_id"];
        echo "\">";
        echo $rGroup["group_name"];
        echo "</option>\r\n                                                                        ";
    }
}
echo "                                                                </select>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"next list-inline-item float-right\">\r\n                                                        <input name=\"submit_subreseller\" type=\"submit\" class=\"btn btn-primary\" value=\" ";
echo $_["setup"];
echo "\" />\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                        </div> <!-- tab-content -->\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </form>\r\n                            </div> <!-- end card-body -->\r\n                        </div> <!-- end card-->\r\n                    </div> <!-- end col -->\r\n                </div>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        \r\n        <script>\r\n        \$(document).ready(function() {\r\n            \$('select.select2').select2({width: '100%'})\r\n            \r\n            \$(window).keypress(function(event){\r\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n            });\r\n            \r\n            \$(\"form\").attr('autocomplete', 'off');\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>