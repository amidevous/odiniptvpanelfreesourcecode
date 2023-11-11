<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if ($rPermissions["is_admin"] && !hasPermissions("adv", "live_connections")) {
    exit;
}
if ($rPermissions["is_reseller"] && !$rPermissions["reseller_client_connection_logs"]) {
    exit;
}
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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                        <a href=\"#\" onClick=\"clearFilters();\">\r\n                                            <button type=\"button\" class=\"btn btn-warning waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-filter-remove\"></i>\r\n                                            </button>\r\n                                        </a>\r\n                                        ";
if (!$detect->isMobile()) {
    echo "                                        <a href=\"#\" onClick=\"toggleAuto();\" style=\"margin-right:10px;\">\r\n                                            <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-refresh\"></i> <span class=\"auto-text\">";
    echo $_["auto_refresh"];
    echo "</span>\r\n                                            </button>\r\n                                        </a>\r\n                                        ";
} else {
    echo "                                        <a href=\"javascript:location.reload();\" onClick=\"toggleAuto();\" style=\"margin-right:10px;\">\r\n                                            <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-refresh\"></i> ";
    echo $_["refresh"];
    echo "                                            </button>\r\n                                        </a>\r\n                                        ";
}
echo "                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $_["live_connections"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <form id=\"user_activity_search\">\r\n                                    <div class=\"form-group row mb-4\">\r\n                                        <div class=\"col-md-6\">\r\n                                            <input type=\"text\" class=\"form-control\" id=\"live_search\" value=\"\" placeholder=\"";
echo $_["search_logs"];
echo "...\">\r\n                                        </div>\r\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"live_filter\">";
echo $_["filter"];
echo "</label>\r\n                                        <div class=\"col-md-3\">\r\n                                            <select id=\"live_filter\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                <option value=\"\" selected>";
echo $_["all_servers"];
echo "</option>\r\n                                                ";
foreach (getStreamingServers() as $rServer) {
    echo "                                                <option value=\"";
    echo $rServer["id"];
    echo "\">";
    echo $rServer["server_name"];
    echo "</option>\r\n                                                ";
}
echo "                                            </select>\r\n                                        </div>\r\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"live_show_entries\">";
echo $_["show"];
echo "</label>\r\n                                        <div class=\"col-md-1\">\r\n                                            <select id=\"live_show_entries\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\r\n                                                ";
}
echo "                                            </select>\r\n                                        </div>\r\n                                    </div>\r\n                                </form>\r\n                                <table id=\"datatable-activity\" class=\"table table-hover dt-responsive nowrap\">\r\n                                    <thead>\r\n                                        <tr>\r\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["status"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["username"];
echo "</th>\r\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">Mac</th>\r\n                                            <th class=\"text-center\">";
echo $_["stream"];
echo "</th>\r\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">Type</th>\r\n                                            <th class=\"text-center\">";
echo $_["server"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["useragent"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["time"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["ip"];
echo "</th>\r\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">ISP</th>\r\n                                            <th class=\"text-center\">Act</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody></tbody>\r\n                                </table>\r\n\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/js/pages/form-remember.js\"></script>\r\n\r\n        <!-- Datatables init -->\r\n        <script>\r\n        var autoRefresh = true;\r\n        var rClearing = false;\r\n        \r\n        function toggleAuto() {\r\n            if (autoRefresh == true) {\r\n                autoRefresh = false;\r\n                \$(\".auto-text\").html(\"";
echo $_["manual_mode"];
echo "\");\r\n            } else {\r\n                autoRefresh = true;\r\n                \$(\".auto-text\").html(\"";
echo $_["auto_refresh"];
echo "\");\r\n            }\r\n        }\r\n        function api(rID, rType) {\r\n            \$.getJSON(\"./api.php?action=user_activity&sub=\" + rType + \"&pid=\" + rID, function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"kill\") {\r\n                        \$.toast(\"";
echo $_["connection_has_been_killed"];
echo "\");\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {\r\n                        \$(this).remove();\r\n                    });\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-activity\").DataTable().ajax.reload(null, false);\r\n                } else {\r\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\r\n                }\r\n            });\r\n        }\r\n        function reloadUsers() {\r\n            if (autoRefresh == true) {\r\n                \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                \$(\"#datatable-activity\").DataTable().ajax.reload(null, false);\r\n            }\r\n            setTimeout(reloadUsers, 2000);\r\n        }\r\n        function getServer() {\r\n            return \$(\"#live_filter\").val();\r\n        }\r\n        function clearFilters() {\r\n            window.rClearing = true;\r\n            \$(\"#live_search\").val(\"\").trigger('change');\r\n            \$('#live_filter').val(\"\").trigger('change');\r\n            \$('#live_show_entries').val(\"";
echo $rAdminSettings["default_entries"] ?: 10;
echo "\").trigger('change');\r\n            window.rClearing = false;\r\n            \$('#datatable-activity').DataTable().search(\$(\"#live_search\").val());\r\n            \$('#datatable-activity').DataTable().page.len(\$('#live_show_entries').val());\r\n            \$(\"#datatable-activity\").DataTable().page(0).draw('page');\r\n            \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n            \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\r\n        }\r\n        \$(document).ready(function() {\r\n\t\t\t\$(window).keypress(function(event){\r\n\t\t\t\tif(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n\t\t\t});\r\n            formCache.init();\r\n            formCache.fetch();\r\n            \r\n            ";
if (isset($_GET["server_id"])) {
    echo "            \$(\"#live_filter\").val(";
    echo $_GET["server_id"];
    echo ");\r\n            ";
}
echo "            \r\n            \$('select').select2({width: '100%'});\r\n            \$(\"#datatable-activity\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    },\r\n                    infoFiltered: \"\"\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                },\r\n                responsive: false,\r\n                processing: true,\r\n                serverSide: true,\r\n                ajax: {\r\n                    url: \"./table_search.php\",\r\n                    \"data\": function(d) {\r\n                        d.id = \"live_connections\";\r\n                        d.server_id = getServer();\r\n                        ";
if (isset($_GET["stream_id"])) {
    echo "                        d.stream_id = ";
    echo intval($_GET["stream_id"]);
    echo ";\r\n                        ";
} else {
    if (isset($_GET["user_id"])) {
        echo "                        d.user_id = ";
        echo intval($_GET["user_id"]);
        echo ";\r\n                        ";
    }
}
echo "                    }\r\n                },\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0,1,8,11]},\r\n                    {\"className\": \"ellipsis\", \"targets\": [5]}\r\n                ],\r\n                order: [[ 0, \"desc\" ]],\r\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\r\n                lengthMenu: [10, 25, 50, 250, 500, 1000],\r\n                stateSave: true\r\n            });\r\n            \$(\"#datatable-activity\").css(\"width\", \"100%\");\r\n            \$('#live_search').keyup(function(){\r\n                if (!window.rClearing) {\r\n                    \$('#datatable-activity').DataTable().search(\$(this).val()).draw();\r\n                }\r\n            })\r\n            \$('#live_show_entries').change(function(){\r\n                if (!window.rClearing) {\r\n                    \$('#datatable-activity').DataTable().page.len(\$(this).val()).draw();\r\n                }\r\n            })\r\n            \$('#live_filter').change(function(){\r\n                if (!window.rClearing) {\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\r\n                }\r\n            })\r\n            ";
if (!$detect->isMobile()) {
    echo "            setTimeout(reloadUsers, 5000);\r\n            ";
}
echo "            \$('#datatable-activity').DataTable().search(\$(this).val()).draw();\r\n            ";
if (!$rAdminSettings["auto_refresh"]) {
    echo "            toggleAuto();\r\n            ";
}
echo "        });\r\n        \r\n        \$(window).bind('beforeunload', function() {\r\n            formCache.save();\r\n        });\r\n        </script>\r\n\r\n        <!-- App js-->\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n    </body>\r\n</html>";

?>