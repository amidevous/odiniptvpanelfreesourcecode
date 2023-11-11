<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if ($rPermissions["is_admin"] && !hasPermissions("adv", "manage_tickets")) {
    exit;
}
$rStatusArray = ["CLOSED", "OPEN", "RESPONDED", "READ"];
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">                      \n                            <h4 class=\"page-title\">Nanomid</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        \n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\t  \n                                <center><iframe src=\"https://nanomid.com/player/add-playlist\" style=\" background: white; border: none; width: 1060px; height: 750px; align: center\"></iframe></center>\n                            </div> <!-- end card-body -->\n                        \n\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-knob/jquery.knob.min.js\"></script>\n        <script src=\"assets/libs/peity/jquery.peity.min.js\"></script>\n        <script src=\"assets/libs/apexcharts/apexcharts.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/jquery-number/jquery.number.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/js/pages/dashboard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\n    </body>\n</html>";

?>