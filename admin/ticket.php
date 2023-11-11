<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if ($rPermissions["is_admin"] && !hasPermissions("adv", "ticket")) {
    exit;
}
if (isset($_POST["submit_ticket"])) {
    if (strlen($_POST["title"]) == 0 && !isset($_POST["respond"]) || strlen($_POST["message"]) == 0) {
        $_STATUS = 1;
    }
    if (!isset($_STATUS)) {
        if (!isset($_POST["respond"])) {
            $rArray = ["member_id" => $rUserInfo["id"], "title" => $_POST["title"], "status" => 1, "admin_read" => 0, "user_read" => 1];
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
            $rQuery = "INSERT INTO `tickets`(" . $rCols . ") VALUES(" . $rValues . ");";
            if ($db->query($rQuery)) {
                $rInsertID = $db->insert_id;
                $db->query("INSERT INTO `tickets_replies`(`ticket_id`, `admin_reply`, `message`, `date`) VALUES(" . $rInsertID . ", 0, '" . ESC($_POST["message"]) . "', " . time() . ");");
                header("Location: ./ticket_view.php?id=" . intval($rInsertID));
            } else {
                $_STATUS = 2;
            }
        } else {
            $rTicket = getTicket($_POST["respond"]);
            if ($rTicket) {
                if (intval($rUserInfo["id"]) == intval($rTicket["member_id"])) {
                    $db->query("UPDATE `tickets` SET `admin_read` = 0, `user_read` = 1 WHERE `id` = " . intval($_POST["respond"]) . ";");
                    $db->query("INSERT INTO `tickets_replies`(`ticket_id`, `admin_reply`, `message`, `date`) VALUES(" . intval($_POST["respond"]) . ", 0, '" . ESC($_POST["message"]) . "', " . time() . ");");
                } else {
                    $db->query("UPDATE `tickets` SET `admin_read` = 0, `user_read` = 0 WHERE `id` = " . intval($_POST["respond"]) . ";");
                    $db->query("INSERT INTO `tickets_replies`(`ticket_id`, `admin_reply`, `message`, `date`) VALUES(" . intval($_POST["respond"]) . ", 1, '" . ESC($_POST["message"]) . "', " . time() . ");");
                }
                header("Location: ./ticket_view.php?id=" . intval($_POST["respond"]));
            } else {
                $_STATUS = 2;
            }
        }
    }
}
if (isset($_GET["id"])) {
    $rTicket = getTicket($_GET["id"]);
    if (!$rTicket) {
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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    ";
if (isset($rTicket)) {
    echo "                                    <a href=\"./ticket_view.php?id=";
    echo $rTicket["id"];
    echo "\"><li class=\"breadcrumb-item\"><i class=\"mdi mdi-backspace\"></i> ";
    echo $_["back_to_ticket"];
    echo "</li></a>\r\n                                    ";
} else {
    echo "                                    <a href=\"./tickets.php\"><li class=\"breadcrumb-item\"><i class=\"mdi mdi-backspace\"></i> ";
    echo $_["back_to_ticket"];
    echo "</li></a>\r\n                                    ";
}
echo "                                </ol>\r\n                            </div>\r\n                            ";
if (isset($rTicket)) {
    echo "                            <h4 class=\"page-title\">";
    echo $_["ticket_response"];
    echo "</h4>\r\n                            ";
} else {
    echo "                            <h4 class=\"page-title\">";
    echo $_["create_ticket"];
    echo "</h4>\r\n                            ";
}
echo "                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        ";
if (isset($_STATUS) && 0 < $_STATUS) {
    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\r\n                                <span aria-hidden=\"true\">&times;</span>\r\n                            </button>\r\n                            ";
    echo $_["generic_fail"];
    echo "                        </div>\r\n                        ";
}
echo "                        <div class=\"card\">\r\n                            <div class=\"card-body\">\r\n                                <form action=\"./ticket.php\" method=\"POST\" id=\"ticket_form\" data-parsley-validate=\"\">\r\n                                    ";
if (isset($rTicket)) {
    echo "                                    <input type=\"hidden\" name=\"respond\" value=\"";
    echo $rTicket["id"];
    echo "\" />\r\n                                    ";
}
echo "                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                            <li class=\"nav-item\">\r\n                                                <a href=\"#ticket-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\r\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n                                            <div class=\"tab-pane\" id=\"ticket-details\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        ";
if (!isset($rTicket)) {
    echo "                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"title\">";
    echo $_["subject"];
    echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <input type=\"text\" class=\"form-control\" id=\"title\" name=\"title\" value=\"\" required data-parsley-trigger=\"";
    echo $_["change"];
    echo "\">\r\n                                                            </div>\r\n                                                        </div>\r\n                                                        ";
}
echo "                                                        <div class=\"form-group row mb-4\">\r\n                                                            <label class=\"col-md-4 col-form-label\" for=\"message\">";
echo $_["message"];
echo "</label>\r\n                                                            <div class=\"col-md-8\">\r\n                                                                <textarea id=\"message\" name=\"message\" class=\"form-control\" rows=\"3\" placeholder=\"\" required data-parsley-trigger=\"";
echo $_["change"];
echo "\"></textarea>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0\">\r\n                                                    <li class=\"next list-inline-item float-right\">\r\n                                                        <input name=\"submit_ticket\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["create"];
echo "\" />\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                        </div> <!-- tab-content -->\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </form>\r\n\r\n                            </div> <!-- end card-body -->\r\n                        </div> <!-- end card-->\r\n                    </div> <!-- end col -->\r\n                </div>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        \r\n        <script>\r\n        \$(document).ready(function() {\r\n            \$(window).keypress(function(event){\r\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n            });\r\n            \r\n            \$(\"form\").attr('autocomplete', 'off');\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>