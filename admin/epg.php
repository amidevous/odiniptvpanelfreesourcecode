<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_epg") && !hasPermissions("adv", "epg_edit")) {
    exit;
}
if (isset($_POST["submit_epg"])) {
    $rArray = ["epg_name" => "", "epg_file" => "", "days_keep" => 7, "data" => ""];
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
        if (!hasPermissions("adv", "epg_edit")) {
            exit;
        }
        $rCols = "id," . $rCols;
        $rValues = ESC($_POST["edit"]) . "," . $rValues;
    } else {
        if (!hasPermissions("adv", "add_epg")) {
            exit;
        }
    }
    $rQuery = "REPLACE INTO `epg`(" . $rCols . ") VALUES(" . $rValues . ");";
    if ($db->query($rQuery)) {
        if (isset($_POST["edit"])) {
            $rInsertID = intval($_POST["edit"]);
        } else {
            $rInsertID = $db->insert_id;
        }
    }
    if (isset($rInsertID)) {
        header("Location: ./epgs.php");
        exit;
    }
    $_STATUS = 1;
}
if (isset($_GET["id"])) {
    $rEPGArr = getEPG($_GET["id"]);
    if (!$rEPGArr || !hasPermissions("adv", "epg_edit")) {
        exit;
    }
} else {
    if (!hasPermissions("adv", "add_epg")) {
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./epgs.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_epgs"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rEPGArr)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo " ";
echo $_["epg"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
    echo $_["epg_success"];
    echo "                        </div>\n                        ";
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["generic_fail"];
        echo "                        </div>\n                        ";
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./epg.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"category_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rEPGArr)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rEPGArr["id"];
    echo "\" />\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#category-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            ";
if (isset($rEPGArr)) {
    echo "                                            <li class=\"nav-item\">\n                                                <a href=\"#view-channels\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-play mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
    echo $_["view_channels"];
    echo "</span>\n                                                </a>\n                                            </li>\n                                            ";
}
echo "                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"category-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"epg_name\">";
echo $_["epg_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"epg_name\" name=\"epg_name\" value=\"";
if (isset($rEPGArr)) {
    echo htmlspecialchars($rEPGArr["epg_name"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"epg_file\">";
echo $_["source"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"epg_file\" name=\"epg_file\" value=\"";
if (isset($rEPGArr)) {
    echo htmlspecialchars($rEPGArr["epg_file"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"days_keep\">";
echo $_["days_to_keep"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"days_keep\" name=\"days_keep\" value=\"";
if (isset($rEPGArr)) {
    echo htmlspecialchars($rEPGArr["days_keep"]);
} else {
    echo "7";
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_epg\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rEPGArr)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"view-channels\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\" style=\"overflow-x:auto;\">\n                                                        <table id=\"datatable\" class=\"table dt-responsive nowrap\">\n                                                            <thead>\n                                                                <tr>\n                                                                    <th>";
echo $_["key"];
echo "</th>\n                                                                    <th>";
echo $_["channel_name"];
echo "</th>\n                                                                    <th>";
echo $_["languages"];
echo "</th>\n                                                                </tr>\n                                                            </thead>\n                                                            <tbody>\n                                                                ";
$rEPGData = [];
if (isset($rEPGArr["data"])) {
    $rEPGData = json_decode($rEPGArr["data"], true);
}
foreach ($rEPGData as $rEPGKey => $rEPGRow) {
    echo "                                                                <tr>    \n                                                                    <td>";
    echo $rEPGKey;
    echo "</td>\n                                                                    <td>";
    echo $rEPGRow["display_name"];
    echo "</td>\n                                                                    <td>";
    echo join(", ", $rEPGRow["langs"]);
    echo "</td>\n                                                                </tr>\n                                                                ";
}
echo "                                                            </tbody>\n                                                        </table>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        \n        <script>\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        \$(document).ready(function() {\n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"form\").attr('autocomplete', 'off');\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                responsive: false,\n                bAutoWidth: false,\n                bInfo: false\n            });\n            \$(\"#days_keep\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n        });\n        </script>\n        \n        <!-- App js-->\n        <script src=\"assets/js/app.min.js\"></script>\n    </body>\n</html>";

?>