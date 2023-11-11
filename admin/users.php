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
    if (!hasPermissions("adv", "users")) {
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
if (hasPermissions("adv", "add_user") || $rPermissions["is_reseller"]) {
    echo "                                        <a href=\"user";
    if ($rPermissions["is_reseller"]) {
        echo "_reseller";
    }
    echo ".php\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_user"];
    echo "                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["users"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>\n                <!-- end page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <form id=\"users_search\">\n                                    <div class=\"form-group row mb-4\">\n                                        <div class=\"col-md-3\">\n                                            <input type=\"text\" class=\"form-control\" id=\"user_search\" value=\"\" placeholder=\"";
echo $_["search_users"];
echo "...\">\n                                        </div>\n                                        <label class=\"col-md-2 col-form-label text-center\" for=\"user_reseller\">";
echo $_["filter_results"];
echo "</label>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"user_reseller\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
echo $_["all_resellers"];
echo "</option>\n                                                ";
foreach ($rRegisteredUsers as $rRegisteredUser) {
    echo "                                                <option value=\"";
    echo $rRegisteredUser["id"];
    echo "\">";
    echo $rRegisteredUser["username"];
    echo "</option>\n                                                ";
}
echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"user_filter\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
echo $_["no_filter"];
echo "</option>\n                                                <option value=\"1\">";
echo $_["active"];
echo "</option>\n                                                <option value=\"2\">";
echo $_["disabled"];
echo "</option>\n                                                <option value=\"3\">";
echo $_["banned"];
echo "</option>\n                                                <option value=\"4\">";
echo $_["expired"];
echo "</option>\n                                                <option value=\"5\">";
echo $_["trial"];
echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"8\">Restreamer</option>\n                                            </select>\n                                        </div>\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"user_show_entries\">";
echo $_["show"];
echo "</label>\n                                        <div class=\"col-md-1\">\n                                            <select id=\"user_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo $_[" selected"];
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
echo $_["password"];
echo "</th>\n                                            <th>";
echo $_["reseller"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["status"];
echo "</th>\n                                            <!--<th class=\"text-center\">";
echo $_["online"];
echo "</th>-->\n                                            <th class=\"text-center\">";
echo $_["trial"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["expiration"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["days"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["conns"];
echo "</th>\n                                            <!--<th class=\"text-center\">";
echo $_["last_connection"];
echo "</th>-->\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">";
echo $_["info"];
echo "</th>\t   \n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody></tbody>\n                                </table>\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n            ";
if ($rPermissions["is_reseller"] && $rPermissions["allow_download"] || $rPermissions["is_admin"]) {
    echo "            <div class=\"modal fade downloadModal\" role=\"dialog\" aria-labelledby=\"downloadLabel\" aria-hidden=\"true\" style=\"display: none;\" data-username=\"\" data-password=\"\">\n                <div class=\"modal-dialog modal-dialog-centered\">\n                    <div class=\"modal-content\">\n                        <div class=\"modal-header\">\n                            <h4 class=\"modal-title\" id=\"downloadModal\">";
    echo $_["download_playlist"];
    echo "</h4>\n                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n                        </div>\n                        <div class=\"modal-body\">\n                            <div class=\"col-12\">\n                                <select id=\"download_type\" class=\"form-control\" data-toggle=\"select2\">\n                                    <option value=\"\">";
    echo $_["select_an_ouput_format"];
    echo " </option>\n                                    ";
    $result = $db->query("SELECT * FROM `devices` ORDER BY `device_id` ASC;");
    if ($result && 0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            if ($row["copy_text"]) {
                echo "<optgroup label=\"" . $row["device_name"] . "\"><option data-text=\"" . str_replace("\"", "\\\"", $row["copy_text"]) . "\" value=\"type=" . $row["device_key"] . "&amp;output=hls\">" . $row["device_name"] . " - HLS </option><option data-text=\"" . str_replace("\"", "\\\"", $row["copy_text"]) . "\" value=\"type=" . $row["device_key"] . "&amp;output=mpegts\">" . $row["device_name"] . " - MPEGTS</option></optgroup>";
            } else {
                echo "<optgroup label=\"" . $row["device_name"] . "\"><option value=\"type=" . $row["device_key"] . "&amp;output=hls\">" . $row["device_name"] . " - HLS </option><option value=\"type=" . $row["device_key"] . "&amp;output=mpegts\">" . $row["device_name"] . " - MPEGTS</option></optgroup>";
            }
        }
    }
    echo "                                </select>\n                            </div>\n                            <div class=\"col-12\" style=\"margin-top:10px;\">\n                                <div class=\"input-group\">\n                                    <input type=\"text\" class=\"form-control\" id=\"download_url\" value=\"\">\n                                    <div class=\"input-group-append\">\n                                        <button class=\"btn btn-warning waves-effect waves-light\" type=\"button\" onClick=\"copyDownload();\"><i class=\"mdi mdi-content-copy\"></i></button>\n                                        <button class=\"btn btn-info waves-effect waves-light\" type=\"button\" onClick=\"doDownload();\" id=\"download_button\" disabled><i class=\"mdi mdi-download\"></i></button>\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                    </div><!-- /.modal-content -->\n                </div><!-- /.modal-dialog -->\n            </div><!-- /.modal -->\n            ";
}
echo "\t\t\t";
if ($rPermissions["is_reseller"] && $rPermissions["allow_download"] || $rPermissions["is_admin"]) {
    echo "            <div class=\"modal fade RenewModal\" role=\"dialog\" aria-labelledby=\"payementLabel\" aria-hidden=\"true\" style=\"display: none;\" data-username=\"\" data-password=\"\">\n                <div class=\"modal-dialog modal-dialog-centered\">\n                    <div class=\"modal-content\">\n                        <div class=\"modal-header\">\n                            <h4 class=\"modal-title\" id=\"renewModal\"></h4>\n                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n                        </div>\n                        <div class=\"modal-body\">\n                            <div class=\"col-9\">\n                                <select id=\"renew_type\" class=\"form-control\" data-toggle=\"select2\">\n                                    <option value=\"\">Extend : </option>\n\t\t\t\t                    <option value=\"1\">1 Day </option>\t\t\t\t\t\t\t\t\t\n\t\t\t\t                    <option value=\"31\">1 Month </option>\n\t\t\t\t                    <option value=\"92\">3 Month </option>\n\t\t\t\t                    <option value=\"183\">6 Month </option>\t\t\t\t\t\t\t\n\t\t\t\t                    <option value=\"365\">12 Month </option>\n                                </select>\n                            </div>\n                            <div class=\"col-2 \" style=\"margin-top: 15px; margin-right: 18px; float: right; display: block;\">\n                                <button class=\"btn btn-info waves-effect waves-light btn-sm\" type=\"button\" onClick=\"dorenew();\" id=\"renew_button\" disabled>OK</button>\n                            </div>\n                        </div>\n                    </div><!-- /.modal-content -->\n                </div><!-- /.modal-dialog -->\n            </div><!-- /.modal -->\n            ";
}
echo "        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/js/pages/form-remember.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\n        <!-- Datatables init -->\n        <script>\n        var autoRefresh = true;\n        var rClearing = false;\n\n\t\tfunction api_renew(rID, rType, rPeriode) {\n\t\t\t\n            \$.getJSON(\"./api_renew.php?action=user&sub=\" + rType + \"&user_id=\" + rID + \"&periode=\" + rPeriode, function(data) {\n                if (data.result === true) {\n                    if (rType == \"renew\") {\n                        \$.toast(\"Subscription successful...\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {   \n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\n                } else {\n                    \$.toast(\"An error occured while processing your request.\");\n                }\n            });\n        }\n\n        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["are_you_sure_you_want_to_delete_this_user"];
echo "') == false) {\n                    return;\n                }\n            } else if (rType == \"kill\") {\n                if (confirm('";
echo $_["are_you_sure_you_want_to kill"];
echo "') == false) {\n                    return;\n                }\n\t\t\t} else if (rType == \"resetispuser\") {\n                if (confirm('Are you sure you want to reset this ISP?') == false) {\n                    return;\n                }\t\n            } else if (rType == \"lockk\") {\n                if (confirm('Are you sure you want to lock this ISP?') == false) {\n                    return;\n                }\t\n            } else if (rType == \"unlockk\") {\n                if (confirm('Are you sure you want to unlock this ISP?') == false) {\n                    return;\n                }\t\n            } else if (rType == \"ban\") {\n                if (confirm('Are you sure you want to ban?') == false) {\n                    return;\n                }\t\n            } else if (rType == \"unban\") {\n                if (confirm('Are you sure you want to unban?') == false) {\n                    return;\n                }\t\n            } else if (rType == \"enable\") {\n                if (confirm('Are you sure you want to enable?') == false) {\n                    return;\n                }\t\n            } else if (rType == \"disable\") {\n                if (confirm('Are you sure you want to disable?') == false) {\n                    return;\n                }\t\n            }\n            \$.getJSON(\"./api.php?action=user&sub=\" + rType + \"&user_id=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"delete\") {\n                        \$.toast(\"";
echo $_["user_has_been_deleted"];
echo "\");\n                    } else if (rType == \"enable\") {\n                        \$.toast(\"";
echo $_["user_has_been_enabled"];
echo "\");\n                    } else if (rType == \"disable\") {\n                        \$.toast(\"";
echo $_["user_has_been_disabled"];
echo "\");\n                    } else if (rType == \"unban\") {\n                        \$.toast(\"";
echo $_["user_has_been_unbanned"];
echo "\");\n                    } else if (rType == \"ban\") {\n                        \$.toast(\"";
echo $_["user_has_been_banned"];
echo "\");\n\t\t\t\t\t} else if (rType == \"resetispuser\") {\n                        \$.toast(\"isp reseted\");\n                    } else if (rType == \"lockk\") {\n                        \$.toast(\"isp has been locked.\");\n                    } else if (rType == \"unlockk\") {\n                        \$.toast(\"isp has been unlocked.\");\t\n                    } else if (rType == \"kill\") {\n                        \$.toast(\"";
echo $_["all_connections_for_this_user_have_been_killed"];
echo "\");\n\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t   \n\t\t\t\t\t\t\t\t\t\t   \n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t \n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\n                } else {\n                    \$.toast(\"";
echo $_["an_error_occured_while_processing_your_request"];
echo "\");\n                }\n            });\n        }\n\t\tfunction renew_user(rid, username) {\n            \$(\"#renew_type\").val(\"\");\n            \$(\"#renew_button\").attr(\"disabled\", true);\n            \$('.RenewModal').data('id', rid );\n\t\t\t\$('.RenewModal').data('username', username );\n\t\t\t\$(\"#renewModal\").text(\"Customer subscription : \"+ \$('.RenewModal').data('username') );\n            \$('.RenewModal').modal('show');\n        }   \n  \n        function download(username, password) {\n            \$(\"#download_type\").val(\"\");\n            \$(\"#download_button\").attr(\"disabled\", true);\n            \$('.downloadModal').data('username', username);\n            \$('.downloadModal').data('password', password);\n            \$('.downloadModal').modal('show');\n        }\n       \n        \$(\"#download_type\").change(function() {\n            if (\$(\"#download_type\").val().length > 0) {\n                ";
if (0 < strlen($rUserInfo["reseller_dns"])) {
    $rDNS = $rUserInfo["reseller_dns"];
} else {
    $rDNS = $rServers[$_INFO["server_id"]]["domain_name"] ? $rServers[$_INFO["server_id"]]["domain_name"] : $rServers[$_INFO["server_id"]]["server_ip"];
}
echo "\t\t\t\t";
if ($rAdminSettings["use_https_main"]) {
    echo "                rText = \"https://";
    echo $rDNS;
    echo ":";
    echo $rServers[$_INFO["server_id"]]["https_broadcast_port"];
    echo "/get.php?username=\" + \$('.downloadModal').data('username') + \"&password=\" + \$('.downloadModal').data('password') + \"&\" + decodeURIComponent(\$('.downloadModal select').val());\n                if (\$(\"#download_type\").find(':selected').data('text')) {\n                    rText = \$(\"#download_type\").find(':selected').data('text').replace(\"{DEVICE_LINK}\", '\"' + rText + '\"');\n                    \$(\"#download_button\").attr(\"disabled\", true);\n                } else {\n                    \$(\"#download_button\").attr(\"disabled\", false);\n                }\n                \$(\"#download_url\").val(rText);\n            } else {\n                \$(\"#download_url\").val(\"\");\n            }\n\t\t\t    ";
} else {
    echo "\t\t\t    rText = \"http://";
    echo $rDNS;
    echo ":";
    echo $rServers[$_INFO["server_id"]]["http_broadcast_port"];
    echo "/get.php?username=\" + \$('.downloadModal').data('username') + \"&password=\" + \$('.downloadModal').data('password') + \"&\" + decodeURIComponent(\$('.downloadModal select').val());\n                if (\$(\"#download_type\").find(':selected').data('text')) {\n                    rText = \$(\"#download_type\").find(':selected').data('text').replace(\"{DEVICE_LINK}\", '\"' + rText + '\"');\n                    \$(\"#download_button\").attr(\"disabled\", true);\n                } else {\n                    \$(\"#download_button\").attr(\"disabled\", false);\n                }\n                \$(\"#download_url\").val(rText);\n            } else {\n                \$(\"#download_url\").val(\"\");\n            }\n\t\t\t    ";
}
echo "        });\n  \n\t\t\$(\"#renew_type\").change(function() {\n            if (\$(\"#renew_type\").val().length > 0) {\n                \$(\"#renew_button\").attr(\"disabled\", false);\t\t\t\t  \n            } else {\n                \$(\"#renew_button\").attr(\"disabled\", true);\n            }\n        });\n\t\t\n\t\tfunction dorenew() {\n            api_renew( \$('.RenewModal').data('id'), \"renew\", \$(\"#renew_type\").val() );\n\t\t\t\$('.RenewModal').modal('hide');\n        }\t\t\t\t\t\t\t\t\t\t\t \n  \n        function doDownload() {\n            if (\$(\"#download_url\").val().length > 0) {\n                window.open(\$(\"#download_url\").val());\n            }\n        }\n        function copyDownload() {\n            \$(\"#download_url\").select();\n            document.execCommand(\"copy\"); \n        }\n        function toggleAuto() {\n            if (autoRefresh == true) {\n                autoRefresh = false;\n                \$(\".auto-text\").html(\"";
echo $_["manual_mode"];
echo "\");\n            } else {\n                autoRefresh = true;\n                \$(\".auto-text\").html(\"";
echo $_["auto_refresh"];
echo "\");\n            }\n        }\n        function getFilter() {\n            return \$(\"#user_filter\").val();\n        }\n        function getReseller() {\n            return \$(\"#user_reseller\").val();\n        }\n        function reloadUsers() {\n            if (autoRefresh == true) {\n                \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\n            }\n            setTimeout(reloadUsers, 10000);\n        }\n        function changeZoom() {\n            if (\$(\"#datatable-users\").hasClass(\"font-large\")) {\n                \$(\"#datatable-users\").removeClass(\"font-large\");\n                \$(\"#datatable-users\").addClass(\"font-normal\");\n            } else if (\$(\"#datatable-users\").hasClass(\"font-normal\")) {\n                \$(\"#datatable-users\").removeClass(\"font-normal\");\n                \$(\"#datatable-users\").addClass(\"font-small\");\n            } else {\n                \$(\"#datatable-users\").removeClass(\"font-small\");\n                \$(\"#datatable-users\").addClass(\"font-large\");\n            }\n            \$(\"#datatable-users\").DataTable().draw();\n        }\n        function clearFilters() {\n            window.rClearing = true;\n            \$(\"#user_search\").val(\"\").trigger('change');\n            \$('#user_filter').val(\"\").trigger('change');\n            \$('#user_reseller').val(\"\").trigger('change');\n            \$('#user_show_entries').val(\"";
echo $rAdminSettings["default_entries"] ?: 10;
echo "\").trigger('change');\n            window.rClearing = false;\n            \$('#datatable-users').DataTable().search(\$(\"#user_search\").val());\n            \$('#datatable-users').DataTable().page.len(\$('#user_show_entries').val());\n            \$(\"#datatable-users\").DataTable().page(0).draw('page');\n            \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n            \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\n        }\n        \$(document).ready(function() {\n\t\t\t\$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            formCache.init();\n            formCache.fetch();\n            \n            \$.fn.dataTable.ext.errMode = 'none';\n            \$('select').select2({width: '100%'});\n            \$(\"#datatable-users\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\",\n                    },\n                    infoFiltered: \"\"\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('user-' + data[0]);\n                },\n                responsive: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"users\",\n                        d.filter = getFilter(),\n                        d.reseller = getReseller()\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2,3,4,5,6,7,8,9,10]},\n                    {\"visible\": false, \"targets\": []},\n                    {\"orderable\": false, \"targets\": [10]}\n                ],\n                order: [[ 0, \"desc\" ]],\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\n                stateSave: true\n            })\n            \$(\"#datatable-users\").css(\"width\", \"100%\");\n            \$('#user_search').keyup(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-users').DataTable().search(\$(this).val()).draw();\n                }\n            });\n            \$('#user_show_entries').change(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-users').DataTable().page.len(\$(this).val()).draw();\n                }\n            });\n            \$('#user_filter').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\n                }\n            });\n            \$('#user_reseller').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\n                }\n            });\n            ";
if (!$detect->isMobile()) {
    echo "            setTimeout(reloadUsers, 10000);\n            ";
}
if (!$rAdminSettings["auto_refresh"]) {
    echo "            toggleAuto();\n            ";
}
echo "            if (\$('#user_search').val().length > 0) {\n                \$('#datatable-users').DataTable().search(\$('#user_search').val()).draw();\n            }\n        });\n        \n        \$(window).bind('beforeunload', function() {\n            formCache.save();\n        });\n        </script>\n    </body>\n</html>";

?>