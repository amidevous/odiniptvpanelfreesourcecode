<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if ($rPermissions["is_admin"]) {
    if (!hasPermissions("adv", "manage_e2")) {
        exit;
    }
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
    echo "        <div class=\"content-page\"><div class=\"content\"><div class=\"container-fluid\">\r\n        ";
} else {
    echo "        <div class=\"wrapper\"><div class=\"container-fluid\">\r\n        ";
}
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                        <a href=\"#\" onClick=\"clearFilters();\">\r\n                                            <button type=\"button\" class=\"btn btn-warning waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-filter-remove\"></i>\r\n                                            </button>\r\n                                        </a>\r\n                                        <a href=\"#\" onClick=\"changeZoom();\">\r\n                                            <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-magnify\"></i>\r\n                                            </button>\r\n                                        </a>\r\n                                        ";
if (!$detect->isMobile()) {
    echo "                                        <a href=\"#\" onClick=\"toggleAuto();\">\r\n                                            <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-refresh\"></i> <span class=\"auto-text\">";
    echo $_["auto_refresh"];
    echo "</span>\r\n                                            </button>\r\n                                        </a>\r\n                                        ";
} else {
    echo "                                        <a href=\"javascript:location.reload();\" onClick=\"toggleAuto();\">\r\n                                            <button type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-refresh\"></i> ";
    echo $_["refresh"];
    echo "                                            </button>\r\n                                        </a>\r\n                                        ";
}
if ($rPermissions["is_admin"] && hasPermissions("adv", "add_mag")) {
    echo "                                        <a href=\"enigma.php\">\r\n                                            <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-link\"></i> ";
    echo $_["link_enigma"];
    echo "                                            </button>\r\n                                        </a>\r\n                                        ";
}
if (hasPermissions("adv", "add_mag") || $rPermissions["is_reseller"]) {
    echo "                                        <a href=\"user";
    if ($rPermissions["is_reseller"]) {
        echo "_reseller";
    }
    echo ".php?e2\">\r\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_enigma"];
    echo "                                            </button>\r\n                                        </a>\r\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $_["enigma_devices"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <form id=\"e2_form\">\r\n                                    <div class=\"form-group row mb-4\">\r\n                                        <div class=\"col-md-3\">\r\n                                            <input type=\"text\" class=\"form-control\" id=\"e2_search\" value=\"\" placeholder=\"";
echo $_["search_devices"];
echo "...\">\r\n                                        </div>\r\n                                        <label class=\"col-md-2 col-form-label text-center\" for=\"e2_reseller\">";
echo $_["filter_results"];
echo "</label>\r\n                                        <div class=\"col-md-3\">\r\n                                            <select id=\"e2_reseller\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                <option value=\"\" selected>";
echo $_["all_resellers"];
echo "</option>\r\n                                                ";
foreach ($rRegisteredUsers as $rRegisteredUser) {
    echo "                                                <option value=\"";
    echo $rRegisteredUser["id"];
    echo "\">";
    echo $rRegisteredUser["username"];
    echo "</option>\r\n                                                ";
}
echo "                                            </select>\r\n                                        </div>\r\n                                        <div class=\"col-md-2\">\r\n                                            <select id=\"e2_filter\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                <option value=\"\" selected>";
echo $_["no_filter"];
echo "</option>\r\n                                                <option value=\"1\">";
echo $_["active"];
echo "</option>\r\n                                                <option value=\"2\">";
echo $_["disabled"];
echo "</option>\r\n                                                <option value=\"3\">";
echo $_["banned"];
echo "</option>\r\n                                                <option value=\"4\">";
echo $_["expired"];
echo "</option>\r\n                                                <option value=\"5\">";
echo $_["trial"];
echo "</option>\r\n                                            </select>\r\n                                        </div>\r\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"e2_show_entries\">";
echo $_["show"];
echo "</label>\r\n                                        <div class=\"col-md-1\">\r\n                                            <select id=\"e2_show_entries\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                ";
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
echo "                                            </select>\r\n                                        </div>\r\n                                    </div>\r\n                                </form>\r\n                                <table id=\"datatable-users\" class=\"table table-hover dt-responsive nowrap font-normal\">\r\n                                    <thead>\r\n                                        <tr>\r\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\r\n                                            <th>";
echo $_["username"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["mac_address"];
echo "</th>\r\n                                            <th>";
echo $_["owner"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["status"];
echo "</th>\r\n                                            <!--<th class=\"text-center\">";
echo $_["online"];
echo "</th>-->\r\n                                            <th class=\"text-center\">";
echo $_["trial"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["expiration"];
echo "</th>\r\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">";
echo $_["days"];
echo "</th>\r\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">";
echo $_["info"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody></tbody>\r\n                                </table>\r\n\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n            </div> <!-- end container -->\r\n        </div>\r\n\t\t";
if ($rPermissions["is_reseller"] && $rPermissions["allow_download"] || $rPermissions["is_admin"]) {
    echo "            <div class=\"modal fade RenewModal\" role=\"dialog\" aria-labelledby=\"payementLabel\" aria-hidden=\"true\" style=\"display: none;\" data-username=\"\" data-password=\"\">\r\n                <div class=\"modal-dialog modal-dialog-centered\">\r\n                    <div class=\"modal-content\">\r\n                        <div class=\"modal-header\">\r\n                            <h4 class=\"modal-title\" id=\"renewModal\"></h4>\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\r\n                        </div>\r\n                        <div class=\"modal-body\">\r\n                            <div class=\"col-9\">\r\n                                <select id=\"renew_type\" class=\"form-control\" data-toggle=\"select2\">\r\n                                    <option value=\"\">Extend : </option>\r\n\t\t\t\t                    <option value=\"1\">1 Day </option>\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t                    <option value=\"31\">1 Month </option>\r\n\t\t\t\t                    <option value=\"92\">3 Month </option>\r\n\t\t\t\t                    <option value=\"183\">6 Month </option>\t\t\t\t\t\t\t\t\r\n\t\t\t\t                    <option value=\"365\">12 Month </option>\r\n                                </select>\r\n                            </div>\r\n                            <div class=\"col-2 \" style=\"margin-top: 15px; margin-right: 18px; float: right; display: block;\">\r\n                                <button class=\"btn btn-info waves-effect waves-light btn-sm\" type=\"button\" onClick=\"dorenew();\" id=\"renew_button\" disabled>OK</button>\r\n                            </div>\r\n                        </div>\r\n                    </div><!-- /.modal-content -->\r\n                </div><!-- /.modal-dialog -->\r\n            </div><!-- /.modal -->\r\n            ";
}
echo "        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/js/pages/form-remember.js\"></script>\r\n\r\n        <!-- Datatables init -->\r\n        <script>\r\n        var autoRefresh = true;\r\n        var rClearing = false;\r\n\t\t\r\n\t\tfunction api_renew(rID, rType, rPeriode) {\r\n\t\t\t\r\n            \$.getJSON(\"./api_renew.php?action=user&sub=\" + rType + \"&user_id=\" + rID + \"&periode=\" + rPeriode, function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"renew\") {\r\n                        \$.toast(\"Subscription successful...\");\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {   \r\n                        \$(this).remove();\r\n                    });\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\r\n                } else {\r\n                    \$.toast(\"An error occured while processing your request.\");\r\n                }\r\n            });\r\n        }\r\n\t\tfunction dorenew() {\r\n            api_renew( \$('.RenewModal').data('id'), \"renew\", \$(\"#renew_type\").val() );\r\n\t\t\t\$('.RenewModal').modal('hide');\r\n        }\r\n        \r\n        function api(rID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('";
echo $_["device_delete_confirm"];
echo "') == false) {\r\n                    return;\r\n                }\r\n\t\t\t} else if (rType == \"resetispuser\") {\r\n                if (confirm('Are you sure you want to reset this ISP?') == false) {\r\n                    return;\r\n                }\t\r\n            }\r\n            \$.getJSON(\"./api.php?action=user&sub=\" + rType + \"&user_id=\" + rID, function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"delete\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_1"];
echo "\");\r\n                    } else if (rType == \"enable\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_2"];
echo "\");\r\n                    } else if (rType == \"disable\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_3"];
echo "\");\r\n\t\t\t\t\t} else if (rType == \"resetispuser\") {\r\n                        \$.toast(\"isp reseted\");\r\n                    } else if (rType == \"lockk\") {\r\n                        \$.toast(\"isp has been locked.\");\r\n                    } else if (rType == \"unlockk\") {\r\n                        \$.toast(\"isp has been unlocked.\");\r\n                    } else if (rType == \"unban\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_4"];
echo "\");\r\n                    } else if (rType == \"ban\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_5"];
echo "\");\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {\r\n                        \$(this).remove();\r\n                    });\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\r\n                } else {\r\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\r\n                }\r\n            });\r\n        }\r\n        function renew_user(rid, username) {\r\n            \$(\"#renew_type\").val(\"\");\r\n            \$(\"#renew_button\").attr(\"disabled\", true);\r\n            \$('.RenewModal').data('id', rid );\r\n\t\t\t\$('.RenewModal').data('username', username );\r\n\t\t\t\$(\"#renewModal\").text(\"Customer subscription : \"+ \$('.RenewModal').data('username') );\r\n            \$('.RenewModal').modal('show');\r\n        }\r\n\t\t\$(\"#renew_type\").change(function() {\r\n            if (\$(\"#renew_type\").val().length > 0) {\r\n                \$(\"#renew_button\").attr(\"disabled\", false);\t  \r\n            } else {\r\n                \$(\"#renew_button\").attr(\"disabled\", true);\r\n            }\r\n        });\r\n\t\tfunction dorenew() {\r\n            api_renew( \$('.RenewModal').data('id'), \"renew\", \$(\"#renew_type\").val() );\r\n\t\t\t\$('.RenewModal').modal('hide');\r\n        }\r\n        function toggleAuto() {\r\n            if (autoRefresh == true) {\r\n                autoRefresh = false;\r\n                \$(\".auto-text\").html(\"";
echo $_["manual_mode"];
echo "\");\r\n            } else {\r\n                autoRefresh = true;\r\n                \$(\".auto-text\").html(\"";
echo $_["auto_refresh"];
echo "\");\r\n            }\r\n        }\r\n        \r\n        function getFilter() {\r\n            return \$(\"#e2_filter\").val();\r\n        }\r\n        function getReseller() {\r\n            return \$(\"#e2_reseller\").val();\r\n        }\r\n        \r\n        function reloadUsers() {\r\n            if (autoRefresh == true) {\r\n                \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\r\n            }\r\n            setTimeout(reloadUsers, 10000);\r\n        }\r\n        function changeZoom() {\r\n            if (\$(\"#datatable-users\").hasClass(\"font-large\")) {\r\n                \$(\"#datatable-users\").removeClass(\"font-large\");\r\n                \$(\"#datatable-users\").addClass(\"font-normal\");\r\n            } else if (\$(\"#datatable-users\").hasClass(\"font-normal\")) {\r\n                \$(\"#datatable-users\").removeClass(\"font-normal\");\r\n                \$(\"#datatable-users\").addClass(\"font-small\");\r\n            } else {\r\n                \$(\"#datatable-users\").removeClass(\"font-small\");\r\n                \$(\"#datatable-users\").addClass(\"font-large\");\r\n            }\r\n            \$(\"#datatable-users\").DataTable().draw();\r\n        }\r\n        function clearFilters() {\r\n            window.rClearing = true;\r\n            \$(\"#e2_search\").val(\"\").trigger('change');\r\n            \$('#e2_filter').val(\"\").trigger('change');\r\n            \$('#e2_reseller').val(\"\").trigger('change');\r\n            \$('#e2_show_entries').val(\"";
echo $rAdminSettings["default_entries"] ?: 10;
echo "\").trigger('change');\r\n            window.rClearing = false;\r\n            \$('#datatable-users').DataTable().search(\$(\"#e2_search\").val());\r\n            \$('#datatable-users').DataTable().page.len(\$('#e2_show_entries').val());\r\n            \$(\"#datatable-users\").DataTable().page(0).draw('page');\r\n            \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n            \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\r\n        }\r\n        \$(document).ready(function() {\r\n\t\t\t\$(window).keypress(function(event){\r\n\t\t\t\tif(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n\t\t\t});\r\n            formCache.init();\r\n            formCache.fetch();\r\n\r\n            \$.fn.dataTable.ext.errMode = 'none';\r\n            \$('select').select2({width: '100%'});\r\n            \$(\"#datatable-users\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\",\r\n                    },\r\n                    infoFiltered: \"\"\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                },\r\n                createdRow: function(row, data, index) {\r\n                    \$(row).addClass('user-' + data[0]);\r\n                },\r\n                responsive: false,\r\n                processing: true,\r\n                serverSide: true,\r\n                ajax: {\r\n                    url: \"./table_search.php\",\r\n                    \"data\": function(d) {\r\n                        d.id = \"enigmas\",\r\n                        d.filter = getFilter(),\r\n                        d.reseller = getReseller()\r\n                    }\r\n                },\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0,2,3,4,5,6,7,8,9]},\r\n                    {\"orderable\": false, \"targets\": [9]},\r\n                    {\"visible\": false, \"targets\": [1]}\r\n                ],\r\n                order: [[ 0, \"desc\" ]],\r\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\r\n                stateSave: true\r\n            });\r\n            \$(\"#datatable-users\").css(\"width\", \"100%\");\r\n            \$('#e2_search').keyup(function(){\r\n                if (!window.rClearing) {\r\n                    \$('#datatable-users').DataTable().search(\$(this).val()).draw();\r\n                }\r\n            });\r\n            \$('#e2_show_entries').change(function(){\r\n                if (!window.rClearing) {\r\n                    \$('#datatable-users').DataTable().page.len(\$(this).val()).draw();\r\n                }\r\n            });\r\n            \$('#e2_filter').change(function(){\r\n                if (!window.rClearing) {\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\r\n                }\r\n            });\r\n            \$('#e2_reseller').change(function(){\r\n                if (!window.rClearing) {\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\r\n                }\r\n            });\r\n            ";
if (!$detect->isMobile()) {
    echo "            setTimeout(reloadUsers, 10000);\r\n            ";
}
echo "            \$('#datatable-users').DataTable().search(\$(this).val()).draw();\r\n            ";
if (!$rAdminSettings["auto_refresh"]) {
    echo "            toggleAuto();\r\n            ";
}
echo "        });\r\n        \r\n        \$(window).bind('beforeunload', function() {\r\n            formCache.save();\r\n        });\r\n        </script>\r\n\r\n        <!-- App js-->\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n    </body>\r\n</html>";

?>