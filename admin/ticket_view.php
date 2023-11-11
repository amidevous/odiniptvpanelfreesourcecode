<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!isset($_GET["id"])) {
    exit;
}
if ($rPermissions["is_admin"] && !hasPermissions("adv", "manage_tickets")) {
    exit;
}
if ($rPermissions["is_admin"]) {
    $rTicket = getTicket($_GET["id"]);
} else {
    $rTicket = getTicket($_GET["id"]);
    if ($rUserInfo["id"] != $rTicket["member_id"]) {
        header("location:./tickets.php");
        exit;
    }
}
if ($rUserInfo["id"] != $rTicket["member_id"]) {
    $db->query("UPDATE `tickets` SET `admin_read` = 1 WHERE `id` = " . intval($_GET["id"]) . ";");
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
echo "                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            ";
if (0 < $rTicket["status"] && ($rPermissions["is_reseller"] || hasPermissions("adv", "ticket"))) {
    echo "                            <div class=\"page-title-right\">\r\n                                <a href=\"./ticket.php?id=";
    echo $rTicket["id"];
    echo "\">\r\n                                    <button type=\"button\" class=\"btn btn-sm btn-primary waves-effect waves-light float-right\">\r\n                                        <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_response"];
    echo "                                    </button>\r\n                                </a>\r\n                            </div>\r\n                            ";
}
echo "                            <h4 class=\"page-title\">";
echo $rTicket["title"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"timeline\" dir=\"ltr\">\r\n                            ";
foreach ($rTicket["replies"] as $rReply) {
    echo "                            <article class=\"timeline-item";
    if (!$rReply["admin_reply"]) {
        echo " timeline-item-left";
    }
    echo "\">\r\n                                <div class=\"timeline-desk\">\r\n                                    <div class=\"timeline-box\">\r\n                                        <span class=\"arrow-alt\"></span>\r\n                                        <span class=\"timeline-icon\"><i class=\"mdi mdi-adjust\"></i></span>\r\n                                        <h4 class=\"mt-0 font-16\">";
    if (!$rReply["admin_reply"]) {
        echo $rTicket["user"]["username"];
    } else {
        echo "Admin";
    }
    echo "</h4>\r\n                                        <p class=\"text-muted\"><small>";
    echo date("Y-m-d H:i", $rReply["date"]);
    echo "</small></p>\r\n                                        <p class=\"mb-0\">";
    echo $rReply["message"];
    echo "</p>\r\n                                    </div>\r\n                                </div>\r\n                            </article>\r\n                            ";
}
echo "                        </div>\r\n                    </div><!-- end col -->\r\n                </div>\r\n                <!-- end row -->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n    </body>\r\n</html>";

?>