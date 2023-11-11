<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
$starttime = microtime(true);
if (isset($_GET["user_id"])) {
    $user_id = $_GET["user_id"];
    $rUser = getUser($user_id);
    $sql = "SELECT a.user_id, a.stream_id, sum(a.date_end)-sum(a.date_start) AS watched, s.stream_display_name FROM user_activity AS a JOIN streams AS s ON a.stream_id=s.id AND a.user_id='" . $user_id . "' GROUP BY a.stream_id ORDER BY watched DESC LIMIT 100";
}
$result = $db->query($sql);
$array = [];
while ($row = $result->fetch_assoc()) {
    $array[] = ["stream_display_name" => $row["stream_display_name"], "wateched" => $row["watched"]];
}
$endtime = microtime(true);
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n\t\t\t\t\t\t    <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t    <li>\n                                        <a href=\"./users.php";
if (isset($_GET["mag"])) {
    echo "?mag";
} else {
    if (isset($_GET["e2"])) {
        echo "?e2";
    }
}
echo "\">\n\t\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_users"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\n                                        <a href=\"./mags.php";
if (isset($_GET["mag"])) {
    echo "?mag";
} else {
    if (isset($_GET["e2"])) {
        echo "?e2";
    }
}
echo "\">\n\t\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> Back to Mags</button>\n\t\t\t\t\t\t\t\t\t    </a>\n\t\t\t\t\t\t\t\t\t</li>\t\n                                </ol>\n                            </div>\n\t\t\t\t\t\t\t<h4 class=\"page-title\">";
echo isset($rUser) ? "User Statistics: " . $rUser["username"] : "User Statistics";
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <table id=\"datatable\" class=\"table table-hover dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th>";
echo $_["id"];
echo " </th>\n                                            <th>";
echo $_["stream_name"];
echo " </th>\n                                            <th>Total Time </th>\t\t\t\t\t\t\t\t\t\t\t\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n\t\t\t\t\t\t\t\t\t    ";
if (0 < count($array)) {
    echo "\t\t\t\t\t\t\t\t\t\t";
    $i = 1;
    foreach ($array as $arr) {
        echo "                                        <tr>\n                                            <td>";
        echo $i;
        $i++;
        echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t<td>";
        echo $arr["stream_display_name"];
        echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t<td><button type=\"button\" class=\"btn btn-success btn-xs btn-fixed waves-effect waves-light\">";
        echo sprintf("%02dh %02dm %02ds", $arr["wateched"] / 3600, $arr["wateched"] / 60 % 60, $arr["wateched"] % 60);
        echo "</td>\n                                        </tr>\n\t\t\t\t\t\t\t\t\t\t";
    }
}
echo "                                    </tbody>\n                                </table>\n\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n\t\t<script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n\t\t<script src=\"assets/libs/moment/moment.min.js\"></script>\n\t\t<script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\n        <!-- Datatables init -->\n        <script>\n        \$(document).ready(function() {\t\t\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n\t\t\t\tcolumnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2]},\n\t\t\t\t\t{\"orderable\": true, \"targets\": [0,1,2]},\n                    {\"visible\": false, \"targets\": []}\n                ],\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n\t\t\t\t\t\$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                pageLength: 10,\n                lengthMenu: [10, 25, 50, 100],\n                responsive: false,\n\t\t\t\tstateSave: true\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n    </body>\n</html>";

?>