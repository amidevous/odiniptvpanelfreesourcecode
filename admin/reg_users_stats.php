<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "connection_logs")) {
    exit;
}
$rows = getRegUsersStats();
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n\t\t\t\t\t\t    <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./reg_users.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_registered_users"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">Reseller Statistics</h4>\n                        </div>\n                    </div>\n                </div>\n                <!-- end page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <table id=\"datatable\" class=\"table table-hover dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th>ID</th>\n\t\t\t\t\t\t\t\t\t\t\t<th>Username</th>\n                                            <th>Group</th>\n                                            <th>Owner</th>\n                                            <th>Unlimited</th>\n\t\t\t\t\t\t\t\t\t\t\t<th>Active</th>\n\t\t\t\t\t\t\t\t\t\t\t<th>Expired</th>\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-pink\">Total Users</th>\n\t\t\t\t\t\t\t\t\t\t\t<th>Trial</th>\n\t\t\t\t\t\t\t\t\t\t\t<th>Banned</th>\n\t\t\t\t\t\t\t\t\t\t\t<th>Disable</th>\n\t\t\t\t\t\t\t\t\t\t\t<th>Credits</th>\n\t\t\t\t\t\t\t\t\t\t\t<th>Last Login</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                        <tr>\n                                        ";
$stats = 0;
while ($row = mysqli_fetch_assoc($rows)) {
    $reg_id = $row["id"];
    $time = time();
    echo "\t\t\t\t\t\t\t\t\t\t    <td> <a href=\"./reg_user.php?id=";
    echo $row["id"];
    echo "\">";
    echo $row["id"];
    echo "</td>\n                                            <td> <a href=\"./reg_user.php?id=";
    echo $row["id"];
    echo "\">";
    echo $row["username"];
    echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"./group.php?id=";
    echo $row["member_group_id"];
    echo "\">\n\t\t\t\t\t\t\t\t\t\t        ";
    $group = $row["member_group_id"];
    $query = $db->query("SELECT * from member_groups where group_id = '" . $group . "'");
    $name_group = $query->fetch_assoc();
    echo $name_group["group_name"];
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t        <a href=\"./reg_user.php?id=";
    echo $row["owner_id"];
    echo "\">\n\t\t\t\t\t\t\t\t\t\t        ";
    $owner_id = $row["owner_id"];
    $query = $db->query("SELECT * from reg_users where id = '" . $owner_id . "'");
    $owner = $query->fetch_assoc();
    echo $owner["username"];
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\t\n\t\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t        ";
    $query = $db->query("SELECT * from users where (member_id = '" . $reg_id . "') AND (is_trial=0) AND (exp_date is NULL)");
    $unlimited = $query->num_rows;
    echo $unlimited;
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\n                                            <td>\n                                                ";
    $query = $db->query("SELECT * from users where (member_id = '" . $reg_id . "') AND (is_trial=0) AND (exp_date >'" . $time . "')");
    $query1 = $db->query("SELECT * from users where (member_id = '" . $reg_id . "') AND (is_trial=1) AND (exp_date >'" . $time . "')");
    $active = $query->num_rows;
    $activer = $query1->num_rows;
    $totalactive = $activer + $active;
    echo $totalactive;
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\n                                            <td>\n                                                ";
    $query = $db->query("SELECT * from users where (member_id = '" . $reg_id . "') AND (exp_date < '" . $time . "')");
    $expired = $query->num_rows;
    echo $expired;
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\n                                            <td> <font color=#e36498>\n                                                ";
    $query = $db->query("SELECT * from users where member_id = '" . $reg_id . "'");
    $total = $query->num_rows;
    echo $total;
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\n                                            <td>\n\t\t\t\t\t\t\t\t\t\t\t    <button type=\"button\" class=\"btn btn-warning btn-xs btn-fixed waves-effect waves-light\">\n                                                ";
    $query = $db->query("SELECT * from users where (member_id = '" . $reg_id . "') AND (is_trial=1) AND (exp_date >'" . $time . "')");
    $test = $query->num_rows;
    echo $test;
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\n                                            <td>\n\t\t\t\t\t\t\t\t\t\t\t    <button type=\"button\" class=\"btn btn-danger btn-xs btn-fixed waves-effect waves-light\">\n                                                ";
    $query = $db->query("SELECT * from users where (member_id = '" . $reg_id . "') AND (admin_enabled=0)");
    $banned = $query->num_rows;
    echo $banned;
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\n                                            <td>\n\t\t\t\t\t\t\t\t\t\t\t    <button type=\"button\" class=\"btn btn-secondary btn-xs btn-fixed waves-effect waves-light\">\n                                                ";
    $query = $db->query("SELECT * from users where (member_id = '" . $reg_id . "') AND (enabled=0)");
    $disable = $query->num_rows;
    echo $disable;
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t\t\t    <button type=\"button\" class=\"btn btn-info btn-xs btn-fixed waves-effect waves-light\">\n                                                ";
    $credits = $row["credits"];
    echo $credits;
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t\t\t\t<td>\n                                            ";
    $lastlogin = $row["last_login"];
    echo date("Y-m-d H:i", $lastlogin);
    echo "\t\t\t\t\t\t\t\t\t\t\t</td>  \n                                        </tr>\n                                        ";
}
echo "                                    </tbody>\n                                </table>\n\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n\t\t<script src=\"assets/libs/moment/moment.min.js\"></script>\n\t\t<script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/js/pages/form-remember.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\t\t\n\n\t\t<script>\n        \$(document).ready(function() {\t\t\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n\t\t\t\tcolumnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2,3,4,5,6,7,8,9,10,11,12]},\n\t\t\t\t\t{\"orderable\": false, \"targets\": []},\n                    {\"visible\": false, \"targets\": []}\n                ],\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n\t\t\t\t\t\$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                pageLength: 10,\n                lengthMenu: [10, 25, 50, 100, 250],\n                responsive: false,\n\t\t\t\tstateSave: true\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n\t\t\n    </body>\n</html>\n";

?>