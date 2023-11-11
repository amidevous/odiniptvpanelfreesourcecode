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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <h4 class=\"page-title\">";
echo $_["line_ip_usage"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <div class=\"form-group row mb-4\">\r\n                                    <div class=\"col-md-7\">\r\n                                        <input type=\"text\" class=\"form-control\" id=\"log_search\" value=\"\" placeholder=\"";
echo $_["search_logs"];
echo "\">\r\n                                    </div>\r\n                                    <div class=\"col-md-3\">\r\n                                        <select id=\"range\" class=\"form-control\" data-toggle=\"select2\">\r\n                                            <option value=\"3600\">";
echo $_["last_hour"];
echo "</option>\r\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"86400\">";
echo $_["last_24_hours"];
echo "</option>\r\n\t\t\t\t\t\t\t\t\t\t\t<option value=\"604800\">";
echo $_["last_7_days"];
echo "</option>\r\n                                        </select>\r\n                                    </div>\r\n                                    <div class=\"col-md-2\">\r\n                                        <select id=\"show_entries\" class=\"form-control\" data-toggle=\"select2\">\r\n                                            ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                            <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\r\n                                            ";
}
echo "                                        </select>\r\n                                    </div>\r\n                                </div>\r\n                                <table id=\"datatable-activity\" class=\"table table-hover dt-responsive nowrap\">\r\n                                    <thead>\r\n                                        <tr>\r\n                                            <th>";
echo $_["user_id"];
echo "</th>\r\n                                            <th>";
echo $_["username"];
echo "</th>\r\n                                            <th>";
echo $_["ip_count"];
echo "</th>\r\n                                            <th>";
echo $_["actions"];
echo "</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody></tbody>\r\n                                </table>\r\n\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n\r\n        <!-- Datatables init -->\r\n        <script>\r\n        function getRange() {\r\n            return \$(\"#range\").val();\r\n        }\r\n\r\n        \$(document).ready(function() {\r\n            \$(window).keypress(function(event){\r\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n            });\r\n            \$('select').select2({width: '100%'});\r\n            \$('#range').on('change', function() {\r\n                \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\r\n            });\r\n            \$(\"#datatable-activity\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    },\r\n                    infoFiltered: \"\"\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                },\r\n                responsive: false,\r\n                processing: true,\r\n                serverSide: true,\r\n                ajax: {\r\n                    url: \"./table_search.php\",\r\n                    \"data\": function(d) {\r\n                        d.id = \"user_ips\",\r\n                        d.range = getRange()\r\n                    }\r\n                },\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0,2,3]}\r\n                ],\r\n                \"order\": [[ 2, \"desc\" ]],\r\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo "            });\r\n            \$(\"#datatable-activity\").css(\"width\", \"100%\");\r\n            \$('#log_search').keyup(function(){\r\n                \$('#datatable-activity').DataTable().search(\$(this).val()).draw();\r\n            })\r\n            \$('#show_entries').change(function(){\r\n                \$('#datatable-activity').DataTable().page.len(\$(this).val()).draw();\r\n            })\r\n        });\r\n        </script>\r\n\r\n        <!-- App js-->\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n    </body>\r\n</html>";

?>