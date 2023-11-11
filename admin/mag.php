<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_mag") && !hasPermissions("adv", "edit_mag")) {
    exit;
}
if (isset($_GET["id"])) {
    $rEditID = $_GET["id"];
}
if (isset($_POST["submit_mag"])) {
    if (filter_var($_POST["mac"], FILTER_VALIDATE_MAC)) {
        if ($rArray = getUser($_POST["paired_user"])) {
            if (isset($_POST["edit"]) && strlen($_POST["edit"])) {
                if (!hasPermissions("adv", "edit_mag")) {
                    exit;
                }
                $rCurMag = getMag($_POST["edit"]);
                $db->query("DELETE FROM `users` WHERE `id` = " . intval($rCurMag["user_id"]) . ";");
                $db->query("DELETE FROM `user_output` WHERE `user_id` = " . intval($rCurMag["user_id"]) . ";");
            } else {
                if (!hasPermissions("adv", "add_mag")) {
                    exit;
                }
            }
            $rArray["username"] .= rand(0, 999999);
            $rArray["is_mag"] = 1;
            $rArray["pair_id"] = $rArray["id"];
            unset($rArray["id"]);
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
            $rQuery = "INSERT INTO `users`(" . $rCols . ") VALUES(" . $rValues . ");";
            if ($db->query($rQuery)) {
                $rNewID = $db->insert_id;
                $rArray = ["user_id" => $rNewID, "mac" => base64_encode($_POST["mac"])];
                if (isset($_POST["edit"])) {
                    $db->query("UPDATE `mag_devices` SET `user_id` = " . intval($rNewID) . ", `mac` = '" . ESC(base64_encode($_POST["mac"])) . "' WHERE `mag_id` = " . intval($_POST["edit"]) . ";");
                    $rEditID = $_POST["edit"];
                } else {
                    $db->query("INSERT INTO `mag_devices`(`user_id`, `mac`) VALUES(" . intval($rNewID) . ", '" . ESC(base64_encode($_POST["mac"])) . "');");
                    $rEditID = $db->insert_id;
                }
                $db->query("INSERT INTO `user_output`(`user_id`, `access_output_id`) VALUES(" . intval($rNewID) . ", 2);");
                header("Location: ./mag.php?successedit&id=" . $rEditID);
                exit;
            }
        } else {
            if (isset($_POST["edit"]) && strlen($_POST["edit"])) {
                $db->query("UPDATE `mag_devices` SET `mac` = '" . ESC(base64_encode($_POST["mac"])) . "' WHERE `mag_id` = " . intval($_POST["edit"]) . ";");
                header("Location: ./mag.php?successedit&id=" . $_POST["edit"]);
                exit;
            }
        }
    } else {
        $rMagArr = ["mac" => base64_encode($_POST["mac"]), "paired_user" => $_POST["paired_user"]];
        $_STATUS = 1;
    }
}
if (isset($rMagArr["paired_user"]) && !isset($rMagArr["username"])) {
    $rMagArr["username"] = getUser($rMagArr["paired_user"])["username"];
}
if (isset($rEditID) && !isset($rMagArr)) {
    if (!hasPermissions("adv", "edit_mag")) {
        exit;
    }
    $rMagArr = getMag($rEditID);
    if (!$rMagArr) {
        exit;
    }
} else {
    if (!hasPermissions("adv", "add_mag")) {
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./mags.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_mag"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["link_mag"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["device_success"];
        echo "                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["device_success"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["device_fail"];
            echo "                        </div>\n                        ";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["device_fail"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./mag.php";
if (isset($rEditID)) {
    echo "?id=" . $rEditID;
}
echo "\" method=\"POST\" id=\"mag_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rMagArr)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rEditID;
    echo "\" />\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#mag-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"mag-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <p class=\"sub-header\">\n                                                            ";
echo $_["device_info"];
echo "                                                        </p>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"mac\">";
echo $_["mac_address"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"mac\" name=\"mac\" value=\"";
if (isset($rMagArr)) {
    echo htmlspecialchars(base64_decode($rMagArr["mac"]));
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"paired_user\">";
echo $_["paired_user"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"paired_user\" name=\"paired_user\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
if (isset($rMagArr)) {
    echo "                                                                    <option value=\"";
    echo $rMagArr["paired_user"];
    echo "\" selected=\"selected\">";
    echo $rMagArr["username"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_mag\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rMagArr)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        \$(document).ready(function() {\n            \$('#paired_user').select2({\n              ajax: {\n                url: './api.php',\n                dataType: 'json',\n                data: function (params) {\n                  return {\n                    search: params.term,\n                    action: 'userlist',\n                    page: params.page\n                  };\n                },\n                processResults: function (data, params) {\n                  params.page = params.page || 1;\n                  return {\n                    results: data.items,\n                    pagination: {\n                        more: (params.page * 100) < data.total_count\n                    }\n                  };\n                },\n                cache: true,\n                width: \"100%\"\n              },\n              placeholder: '";
echo $_["search_user"];
echo "'\n            });\n\n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"form\").attr('autocomplete', 'off');\n        });\n        </script>\n    </body>\n</html>";

?>