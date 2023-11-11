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
    echo "        <div class=\"content-page\"><div class=\"content\"><div class=\"container-fluid\">\r\n        ";
} else {
    echo "        <div class=\"wrapper\"><div class=\"container-fluid\">\r\n        ";
}
echo "                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            ";
if (!$rPermissions["is_admin"]) {
    echo "                            <div class=\"page-title-right\">\r\n                                <a href=\"./ticket.php\">\r\n                                    <button type=\"button\" class=\"btn btn-sm btn-primary waves-effect waves-light float-right\">\r\n                                        <i class=\"mdi mdi-plus\"></i> ";
    echo $_["create_ticket"];
    echo "                                    </button>\r\n                                </a>\r\n                            </div>\r\n                            ";
}
echo "                            <h4 class=\"page-title\">";
echo $_["tickets"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\" card card-body\">\r\n                            <table class=\"table table-hover m-0 table-centered dt-responsive nowrap w-100\" id=\"tickets-table\">\r\n                                <thead>\r\n                                    <tr>\r\n                                        <th class=\"text-center\">";
echo $_["id"];
echo "</th>\r\n                                        ";
if ($rPermissions["is_admin"]) {
    echo "                                        <th>";
    echo $_["reseller"];
    echo "</th>\r\n                                        ";
}
echo "                                        <th>";
echo $_["subject"];
echo "</th>\r\n                                        <th class=\"text-center\">";
echo $_["status"];
echo "</th>\r\n                                        <th class=\"text-center\">";
echo $_["created_date"];
echo "</th>\r\n                                        <th class=\"text-center\">";
echo $_["last_reply"];
echo "</th>\r\n                                        <th class=\"text-center\">";
echo $_["action"];
echo "</th>\r\n                                    </tr>\r\n                                </thead>\r\n                                <tbody>\r\n                                    ";
if ($rPermissions["is_admin"]) {
    $rTickets = getTickets();
} else {
    $rTickets = getTickets($rUserInfo["id"]);
}
foreach ($rTickets as $rTicket) {
    echo "                                    <tr id=\"ticket-";
    echo $rTicket["id"];
    echo "\">\r\n                                        <td class=\"text-center\"><a href=\"./ticket_view.php?id=";
    echo $rTicket["id"];
    echo "\">";
    echo $rTicket["id"];
    echo "</a></td>\r\n                                        ";
    if ($rPermissions["is_admin"]) {
        echo "                                        <td>";
        echo $rTicket["username"];
        echo "</td>\r\n                                        ";
    }
    echo "                                        <td>";
    echo $rTicket["title"];
    echo "</td>\r\n                                        <td class=\"text-center\"><span class=\"badge badge-";
    echo ["secondary", "warning", "success", "warning"][$rTicket["status"]];
    echo "\">";
    echo $rStatusArray[$rTicket["status"]];
    echo "</span></td>\r\n                                        <td class=\"text-center\">";
    echo $rTicket["created"];
    echo "</td>\r\n                                        <td class=\"text-center\">";
    echo $rTicket["last_reply"];
    echo "</td>\r\n                                        <td class=\"text-center\">\r\n                                            <div class=\"btn-group dropdown\">\r\n                                                <a href=\"javascript: void(0);\" class=\"table-action-btn dropdown-toggle arrow-none btn btn-dark btn-sm\" data-toggle=\"dropdown\" aria-expanded=\"false\"><i class=\"mdi mdi-dots-horizontal\"></i></a>\r\n                                                <div class=\"dropdown-menu dropdown-menu-right\">\r\n                                                    <a class=\"dropdown-item\" href=\"./ticket_view.php?id=";
    echo $rTicket["id"];
    echo "\"><i class=\"mdi mdi-eye mr-2 text-muted font-18 vertical-middle\"></i>";
    echo $_["view_ticket"];
    echo "</a>\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t";
    if (hasPermissions("adv", "ticket")) {
        if (0 < $rTicket["status"]) {
            echo "                                                    <a class=\"dropdown-item\" href=\"javascript:void(0);\" onClick=\"api(";
            echo $rTicket["id"];
            echo ", 'close');\"><i class=\"mdi mdi-check-all mr-2 text-muted font-18 vertical-middle\"></i>";
            echo $_["close"];
            echo "</a>\r\n                                                    ";
        } else {
            if ($rPermissions["is_admin"]) {
                echo "                                                    <a class=\"dropdown-item\" href=\"javascript:void(0);\" onClick=\"api(";
                echo $rTicket["id"];
                echo ", 'reopen');\"><i class=\"mdi mdi-check-all mr-2 text-muted font-18 vertical-middle\"></i>";
                echo $_["re-open"];
                echo "</a>\r\n                                                    ";
            }
        }
        echo "                                                    ";
        if ($rPermissions["is_admin"]) {
            echo "                                                    <a class=\"dropdown-item\" href=\"javascript:void(0);\" onClick=\"api(";
            echo $rTicket["id"];
            echo ", 'delete');\"><i class=\"mdi mdi-delete mr-2 text-muted font-18 vertical-middle\"></i>";
            echo $_["delete"];
            echo "</a>\r\n                                                    ";
            if ($rTicket["admin_read"] == 0) {
                echo "                                                    <a class=\"dropdown-item\" href=\"javascript:void(0);\" onClick=\"api(";
                echo $rTicket["id"];
                echo ", 'read');\"><i class=\"mdi mdi-star mr-2 font-18 text-muted vertical-middle\"></i>";
                echo $_["mark_as_read"];
                echo "</a>\r\n                                                    ";
            } else {
                echo "                                                    <a class=\"dropdown-item\" href=\"javascript:void(0);\" onClick=\"api(";
                echo $rTicket["id"];
                echo ", 'unread');\"><i class=\"mdi mdi-star mr-2 font-18 text-muted vertical-middle\"></i>";
                echo $_["mark_as_unread"];
                echo "</a>\r\n                                                    ";
            }
        }
    }
    echo "                                                </div>\r\n                                            </div>\r\n                                        </td>\r\n                                    </tr>\r\n                                    ";
}
echo "                                </tbody>\r\n                            </table>\r\n                        </div>\r\n                    </div><!-- end col -->\r\n                </div>\r\n                <!-- end row -->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        <script>\r\n        function api(rID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('";
echo $_["are_you_sure_you_want_to_delete_this_ticket"];
echo "') == false) {\r\n                    return;\r\n                }\r\n            }\r\n            \$.getJSON(\"./api.php?action=ticket&sub=\" + rType + \"&ticket_id=\" + rID, function(data) {\r\n                if (data.result == true) {\r\n                    location.reload();\r\n                } else {\r\n                    \$.toast(\"";
echo $_["an_error_occured"];
echo "\");\r\n                }\r\n            }).fail(function() {\r\n                \$.toast(\"";
echo $_["an_error_occured"];
echo "\");\r\n            });\r\n        }        \r\n        \$(document).ready(function() {\r\n            \$(\"#tickets-table\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    }\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\")\r\n                },\r\n                order: [[ 0, \"desc\" ]],\r\n                stateSave: true\r\n            });\r\n            \$(\"#tickets-table\").css(\"width\", \"100%\");\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>