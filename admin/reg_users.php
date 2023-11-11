<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if ($rPermissions["is_reseller"] && !$rPermissions["create_sub_resellers"]) {
    exit;
}
if ($rPermissions["is_admin"] && !hasPermissions("adv", "mng_regusers")) {
    exit;
}
if ($rPermissions["is_admin"]) {
    $rRegisteredUsers = getRegisteredUsers();
} else {
    $rRegisteredUsers = getRegisteredUsers($rUserInfo["id"]);
}
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"#\" onClick=\"clearFilters();\">\n                                            <button type=\"button\" class=\"btn btn-warning waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-filter-remove\"></i>\n                                            </button>\n                                        </a>\n                                        <a href=\"#\" onClick=\"changeZoom();\">\n                                            <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-magnify\"></i>\n                                            </button>\n                                        </a>\n                                        ";
if (!$detect->isMobile()) {
    echo "                                        <a href=\"#\" onClick=\"toggleAuto();\">\n                                            <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-refresh\"></i> <span class=\"auto-text\">";
    echo $_["auto_refresh"];
    echo "</span>\n                                            </button>\n                                        </a>\n                                        ";
} else {
    echo "                                        <a href=\"javascript:location.reload();\" onClick=\"toggleAuto();\">\n                                            <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-refresh\"></i> ";
    echo $_["refresh"];
    echo "                                            </button>\n                                        </a>\n                                        ";
}
if (hasPermissions("adv", "add_reguser") || $rPermissions["is_reseller"]) {
    echo "                                        <a href=\"";
    if ($rPermissions["is_admin"]) {
        echo "reg_user";
    } else {
        echo "subreseller";
    }
    echo ".php\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add"];
    echo " ";
    if ($rPermissions["is_admin"]) {
        echo $_["registered_user"];
    } else {
        echo $_["subresellers"];
    }
    echo "                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if ($rPermissions["is_admin"]) {
    echo $_["registered_users"];
} else {
    echo $_["subresellers"];
}
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <form id=\"reg_users_search\">\n                                    <div class=\"form-group row mb-4\">\n                                        <div class=\"col-md-3\">\n                                            <input type=\"text\" class=\"form-control\" id=\"reg_search\" value=\"\" placeholder=\"";
echo $_["search_users"];
echo "\">\n                                        </div>\n                                        <label class=\"col-md-2 col-form-label text-center\" for=\"reg_reseller\">";
echo $_["filter_results"];
echo "</label>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"reg_reseller\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
echo $_["all_owners"];
echo "</option>\n                                                ";
if ($rPermissions["is_admin"]) {
    echo "                                                <option value=\"0\">";
    echo $_["no_owner"];
    echo "</option>\n                                                ";
}
foreach ($rRegisteredUsers as $rRegisteredUser) {
    echo "                                                <option value=\"";
    echo $rRegisteredUser["id"];
    echo "\">";
    echo $rRegisteredUser["username"];
    echo "</option>\n                                                ";
}
echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"reg_filter\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
echo $_["no_filter"];
echo "</option>\n                                                <option value=\"1\">";
echo $_["active"];
echo "</option>\n                                                <option value=\"2\">";
echo $_["disabled"];
echo "</option>\n                                            </select>\n                                        </div>\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"reg_show_entries\">";
echo $_["show"];
echo "</label>\n                                        <div class=\"col-md-1\">\n                                            <select id=\"reg_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\n                                                ";
}
echo "                                            </select>\n                                        </div>\n                                    </div>\n                                </form>\n                                <table id=\"datatable-users\" class=\"table table-hover dt-responsive nowrap font-normal\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                            <th>";
echo $_["username"];
echo "</th>\n                                            <th>";
echo $_["owner"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["ip"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["type"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["status"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["credits"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["users"];
echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">DNS</th>\n                                            <th class=\"text-center\">";
echo $_["last_login"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody></tbody>\n                                </table>\n\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/js/pages/form-remember.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\n        <script>\n        var autoRefresh = true;\n        var rClearing = false;\n        \n        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["are_you_sure_you_want_to_clear_logs_for_this_period"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=reg_user&sub=\" + rType + \"&user_id=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$.toast(\"";
echo $_["user_has_been_deleted"];
echo "\");\n                    } else if (rType == \"enable\") {\n                        \$.toast(\"";
echo $_["user_has_been_enabled"];
echo "\");\n                    } else if (rType == \"disable\") {\n                        \$.toast(\"";
echo $_["user_has_been_disabled"];
echo "\");\n\t\t\t\t\t} else if (rType == \"reset\") {\n                        \$.toast(\"";
echo $_["two_factor_authentication_has_been_reset"];
echo "\");\n                    }\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\n                } else {\n                    \$.toast(\"";
echo $_["an_error_occured"];
echo "\");\n                }\n            });\n        }\n        function toggleAuto() {\n            if (autoRefresh == true) {\n                autoRefresh = false;\n                \$(\".auto-text\").html(\"Manual Mode\");\n            } else {\n                autoRefresh = true;\n                \$(\".auto-text\").html(\"Auto-Refresh\");\n            }\n        }\n        function getFilter() {\n            return \$(\"#reg_filter\").val();\n        }\n        function getReseller() {\n            return \$(\"#reg_reseller\").val();\n        }\n        function reloadUsers() {\n            if (autoRefresh == true) {\n                \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\n            }\n            setTimeout(reloadUsers, 5000);\n        }\n        function changeZoom() {\n            if (\$(\"#datatable-users\").hasClass(\"font-large\")) {\n                \$(\"#datatable-users\").removeClass(\"font-large\");\n                \$(\"#datatable-users\").addClass(\"font-normal\");\n            } else if (\$(\"#datatable-users\").hasClass(\"font-normal\")) {\n                \$(\"#datatable-users\").removeClass(\"font-normal\");\n                \$(\"#datatable-users\").addClass(\"font-small\");\n            } else {\n                \$(\"#datatable-users\").removeClass(\"font-small\");\n                \$(\"#datatable-users\").addClass(\"font-large\");\n            }\n            \$(\"#datatable-users\").DataTable().draw();\n        }\n        function clearFilters() {\n            window.rClearing = true;\n            \$(\"#reg_search\").val(\"\").trigger('change');\n            \$('#reg_filter').val(\"\").trigger('change');\n            \$('#reg_reseller').val(\"\").trigger('change');\n            \$('#reg_show_entries').val(\"";
echo $rAdminSettings["default_entries"] ?: 10;
echo "\").trigger('change');\n            window.rClearing = false;\n            \$('#datatable-users').DataTable().search(\$(\"#reg_search\").val());\n            \$('#datatable-users').DataTable().page.len(\$('#reg_show_entries').val());\n            \$(\"#datatable-users\").DataTable().page(0).draw('page');\n            \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n            \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\n        }\n        \$(document).ready(function() {\n\t\t\t\$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            formCache.init();\n            formCache.fetch();\n            \n            \$.fn.dataTable.ext.errMode = 'none';\n            \$('select').select2({width: '100%'});\n            \$(\"#datatable-users\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('user-' + data[0]);\n                },\n\t\t\t\tpageLength: 50,\n                lengthMenu: [10, 25, 50, 250, 500, 1000],\n\t\t\t\tstateSave: true,\n                responsive: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"reg_users\",\n                        d.filter = getFilter(),\n                        d.reseller = getReseller()\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2,3,4,5,6,7,8,9,10]},\n                    ";
if ($rPermissions["is_reseller"]) {
    echo "                    {\"visible\": false, \"targets\": [4]}\n                    ";
}
echo "                ],\n                order: [[ 0, \"desc\" ]],\n                stateSave: true\n            });\n            \$(\"#datatable-users\").css(\"width\", \"100%\");\n            \$('#reg_search').keyup(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-users').DataTable().search(\$(this).val()).draw();\n                }\n            });\n            \$('#reg_show_entries').change(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-users').DataTable().page.len(\$(this).val()).draw();\n                }\n            });\n            \$('#reg_filter').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\n                }\n            });\n            \$('#reg_reseller').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\n                }\n            });\n            ";
if (!$detect->isMobile()) {
    echo "            setTimeout(reloadUsers, 5000);\n            ";
}
if (!$rAdminSettings["auto_refresh"]) {
    echo "            toggleAuto();\n            ";
}
echo "            if (\$('#reg_search').val().length > 0) {\n                \$('#datatable-users').DataTable().search(\$('#reg_search').val()).draw();\n            }\n        });\n        \n        \$(window).bind('beforeunload', function() {\n            formCache.save();\n        });\n        </script>\n    </body>\n</html>";

?>