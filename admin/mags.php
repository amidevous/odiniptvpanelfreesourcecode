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
    if (!hasPermissions("adv", "manage_mag")) {
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
    echo "                                        <a href=\"mag.php\">\r\n                                            <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-link\"></i> ";
    echo $_["link_mag"];
    echo "                                            </button>\r\n                                        </a>\r\n                                        ";
}
if (hasPermissions("adv", "add_mag") || $rPermissions["is_reseller"]) {
    echo "                                        <a href=\"user";
    if ($rPermissions["is_reseller"]) {
        echo "_reseller";
    }
    echo ".php?mag\">\r\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\r\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_mag"];
    echo "                                            </button>\r\n                                        </a>\r\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $_["mag_devices"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <form id=\"mag_form\">\r\n                                    <div class=\"form-group row mb-4\">\r\n                                        <div class=\"col-md-3\">\r\n                                            <input type=\"text\" class=\"form-control\" id=\"mag_search\" value=\"\" placeholder=\"";
echo $_["search_devices"];
echo "...\">\r\n                                        </div>\r\n                                        <label class=\"col-md-2 col-form-label text-center\" for=\"mag_reseller\">";
echo $_["filter_results"];
echo "</label>\r\n                                        <div class=\"col-md-3\">\r\n                                            <select id=\"mag_reseller\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                <option value=\"\" selected>";
echo $_["all_resellers"];
echo "</option>\r\n                                                ";
foreach ($rRegisteredUsers as $rRegisteredUser) {
    echo "                                                <option value=\"";
    echo $rRegisteredUser["id"];
    echo "\">";
    echo $rRegisteredUser["username"];
    echo "</option>\r\n                                                ";
}
echo "                                            </select>\r\n                                        </div>\r\n                                        <div class=\"col-md-2\">\r\n                                            <select id=\"mag_filter\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                <option value=\"\" selected>";
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
echo "</option>\r\n                                            </select>\r\n                                        </div>\r\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"mag_show_entries\">";
echo $_["show"];
echo "</label>\r\n                                        <div class=\"col-md-1\">\r\n                                            <select id=\"mag_show_entries\" class=\"form-control\" data-toggle=\"select2\">\r\n                                                ";
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
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["username"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["mac_address"];
echo "</th>\r\n                                            <th class=\"text-center\">";
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
echo "</th>\r\n\t\t\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">STB Type</th>\r\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">";
echo $_["info"];
echo "</th>\r\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody></tbody>\r\n                                </table>\r\n\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n            </div> <!-- end container -->\r\n        </div>\r\n\t\t";
if ($rPermissions["is_admin"] || $rPermissions["is_reseller"] && $rAdminSettings["reseller_mag_events"]) {
    echo "\t\t<div class=\"modal fade messageModal\" role=\"dialog\" aria-labelledby=\"messageModal\" aria-hidden=\"true\" style=\"display: none;\" data-id=\"\">\r\n\t\t\t<div class=\"modal-dialog modal-dialog-centered\">\r\n\t\t\t\t<div class=\"modal-content\">\r\n\t\t\t\t\t<div class=\"modal-header\">\r\n\t\t\t\t\t\t<h4 class=\"modal-title\" id=\"messageModal\">";
    echo $_["mag_event"];
    echo "</h4>\r\n\t\t\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\r\n\t\t\t\t\t</div>\r\n\t\t\t\t\t<div class=\"modal-body\">\r\n\t\t\t\t\t\t<div class=\"col-12\">\r\n\t\t\t\t\t\t\t<select id=\"message_type\" class=\"form-control\" data-toggle=\"select2\" >\r\n\t\t\t\t\t\t\t\t<option value=\"\" selected>";
    echo $_["select_an_event"];
    echo ":</option>\r\n\t\t\t\t\t\t\t\t<optgroup label=\"\">\r\n\t\t\t\t\t\t\t\t\t<option value=\"play_channel\">";
    echo $_["play_channel"];
    echo "</option>\r\n\t\t\t\t\t\t\t\t\t<option value=\"reload_portal\">";
    echo $_["reload_portal"];
    echo "</option>\r\n\t\t\t\t\t\t\t\t\t<option value=\"reboot\">";
    echo $_["reboot_device"];
    echo "</option>\r\n\t\t\t\t\t\t\t\t\t<option value=\"send_msg\">";
    echo $_["send_message"];
    echo "</option>\r\n\t\t\t\t\t\t\t\t\t<option value=\"cut_off\">";
    echo $_["close_portal"];
    echo "</option>\r\n                                    <option value=\"reset_stb_lock\">";
    echo $_["reset_stb_lock"];
    echo "</option>\r\n\t\t\t\t\t\t\t\t</optgroup>\r\n\t\t\t\t\t\t\t</select>\r\n\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t<div class=\"col-12\" style=\"margin-top:20px;display:none;\" id=\"send_msg_form\">\r\n\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t<div class=\"col-md-12\">\r\n\t\t\t\t\t\t\t\t\t<textarea id=\"message\" name=\"message\" class=\"form-control\" rows=\"3\" placeholder=\"";
    echo $_["enter_a_custom_message"];
    echo "...\"></textarea>\r\n\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t<label class=\"col-md-9 col-form-label\" for=\"reboot_portal\">";
    echo $_["reboot_on_confirmation"];
    echo "</label>\r\n\t\t\t\t\t\t\t\t<div class=\"col-md-3\">\r\n\t\t\t\t\t\t\t\t\t<input name=\"reboot_portal\" id=\"reboot_portal\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\"/>\r\n\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t<div class=\"col-12\" style=\"margin-top:20px;display:none;\" id=\"play_channel_form\">\r\n\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\r\n\t\t\t\t\t\t\t\t<label class=\"col-md-3 col-form-label\" for=\"selected_channel\">";
    echo $_["channel"];
    echo "</label>\r\n\t\t\t\t\t\t\t\t<div class=\"col-md-9\">\r\n\t\t\t\t\t\t\t\t\t<select id=\"selected_channel\" name=\"selected_channel\" class=\"form-control\" data-toggle=\"select2\" style=\"width:100%;\"></select>\r\n\t\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t\t</div>\r\n\t\t\t\t\t\t</div>\r\n\t\t\t\t\t</div>\r\n\t\t\t\t\t<div class=\"modal-footer\">\r\n\t\t\t\t\t\t<button disabled id=\"message_submit\" type=\"button\" class=\"btn btn-primary waves-effect\">";
    echo $_["send_event"];
    echo "</button>\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</div><!-- /.modal-content -->\r\n\t\t\t</div><!-- /.modal-dialog -->\r\n\t\t</div><!-- /.modal -->\r\n\t\t";
}
echo "\t\t";
if ($rPermissions["is_reseller"] && $rPermissions["allow_download"] || $rPermissions["is_admin"]) {
    echo "            <div class=\"modal fade RenewModal\" role=\"dialog\" aria-labelledby=\"payementLabel\" aria-hidden=\"true\" style=\"display: none;\" data-username=\"\" data-password=\"\">\r\n                <div class=\"modal-dialog modal-dialog-centered\">\r\n                    <div class=\"modal-content\">\r\n                        <div class=\"modal-header\">\r\n                            <h4 class=\"modal-title\" id=\"renewModal\"></h4>\r\n                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\r\n                        </div>\r\n                        <div class=\"modal-body\">\r\n                            <div class=\"col-9\">\r\n                                <select id=\"renew_type\" class=\"form-control\" data-toggle=\"select2\">\r\n                                    <option value=\"\">Extend : </option>\r\n\t\t\t\t                    <option value=\"1\">1 Day </option>\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t                    <option value=\"31\">1 Month </option>\r\n\t\t\t\t                    <option value=\"92\">3 Month </option>\r\n\t\t\t\t                    <option value=\"183\">6 Month </option>\t\t\t\t\t\t\t\t\r\n\t\t\t\t                    <option value=\"365\">12 Month </option>\r\n                                </select>\r\n                            </div>\r\n                            <div class=\"col-2 \" style=\"margin-top: 15px; margin-right: 18px; float: right; display: block;\">\r\n                                <button class=\"btn btn-info waves-effect waves-light btn-sm\" type=\"button\" onClick=\"dorenew();\" id=\"renew_button\" disabled>OK</button>\r\n                            </div>\r\n                        </div>\r\n                    </div><!-- /.modal-content -->\r\n                </div><!-- /.modal-dialog -->\r\n            </div><!-- /.modal -->\r\n            ";
}
echo "\t\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\t\t\r\n\t<script src=\"assets/summernote/jquery-3.4.1.slim.min.js\" crossorigin=\"anonymous\"></script>\r\n    <link href=\"assets/summernote/summernote-lite.min.css\" rel=\"stylesheet\">\r\n    <script src=\"assets/summernote/summernote-lite.min.js\"></script>\r\n    <div id=\"summernote\"></div>\r\n    <script>\r\n      \$('#message').summernote({\r\n        tabsize: 2,\r\n        height: 200,\r\n        toolbar: [\r\n          ['style', ['style']],\r\n          ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],\r\n          ['color', ['color']],\r\n          ['para', ['paragraph', 'ul']]\r\n        ]\r\n      });\r\n    </script>\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n\t\t<script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/js/pages/form-remember.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n\r\n        <script>\r\n        var autoRefresh = true;\r\n        var rClearing = false;\r\n\t\t\r\n\t\tfunction api_renew(rID, rType, rPeriode) {\r\n\t\t\t\r\n            \$.getJSON(\"./api_renew.php?action=user&sub=\" + rType + \"&user_id=\" + rID + \"&periode=\" + rPeriode, function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"renew\") {\r\n                        \$.toast(\"Subscription successful...\");\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {   \r\n                        \$(this).remove();\r\n                    });\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\r\n                } else {\r\n                    \$.toast(\"An error occured while processing your request.\");\r\n                }\r\n            });\r\n        }\r\n\t\tfunction dorenew() {\r\n            api_renew( \$('.RenewModal').data('id'), \"renew\", \$(\"#renew_type\").val() );\r\n\t\t\t\$('.RenewModal').modal('hide');\r\n        }\r\n        function api(rID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('";
echo $_["device_delete_confirm"];
echo "') == false) {\r\n                    return;\r\n                }\r\n\t\t\t} else if (rType == \"kill\") {\r\n                if (confirm('";
echo $_["are_you_sure_you_want_to kill"];
echo "') == false) {\r\n                    return;\r\n                }\t\r\n\t\t\t} else if (rType == \"resetispuser\") {\r\n                if (confirm('Are you sure you want to reset this ISP?') == false) {\r\n                    return;\r\n                }\r\n            } else if (rType == \"magtouser\") {\r\n                if (confirm('Are you sure you want to convert this Mag Device for M3U?') == false) {\r\n                    return;\r\n                }\r\n            }\r\n\t\t\telse if (rType == \"lockk\") {\r\n                if (confirm('Are you sure you want to lock this ISP?') == false) {\r\n                    return;\r\n                }\r\n            } else if (rType == \"unlockk\") {\r\n                if (confirm('Are you sure you want to unlock this ISP?') == false) {\r\n                    return;\r\n                }\t\r\n            } else if (rType == \"ban\") {\r\n                if (confirm('Are you sure you want to ban?') == false) {\r\n                    return;\r\n                }\t\r\n            } else if (rType == \"unban\") {\r\n                if (confirm('Are you sure you want to unban?') == false) {\r\n                    return;\r\n                }\t\r\n            } else if (rType == \"enable\") {\r\n                if (confirm('Are you sure you want to enable?') == false) {\r\n                    return;\r\n                }\t\r\n            } else if (rType == \"disable\") {\r\n                if (confirm('Are you sure you want to disable?') == false) {\r\n                    return;\r\n                }\t\r\n            } else if (rType == \"reset_stb\") {\r\n                if (confirm('Are you sure you want to Reset STB?') == false) {\r\n                    return;\r\n                }\t\r\n            }\r\n            \$.getJSON(\"./api.php?action=user&sub=\" + rType + \"&user_id=\" + rID, function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"delete\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_1"];
echo "\");\r\n                    } else if (rType == \"enable\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_2"];
echo "\");\r\n                    } else if (rType == \"disable\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_3"];
echo "\");\r\n\t\t\t\t\t} else if (rType == \"resetispuser\") {\r\n                        \$.toast(\"isp reseted\");\r\n                    } else if (rType == \"lockk\") {\r\n                        \$.toast(\"isp has been locked.\");\r\n                    } else if (rType == \"unlockk\") {\r\n                        \$.toast(\"isp has been unlocked.\");  \r\n                    } else if (rType == \"unban\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_4"];
echo "\");\r\n                    } else if (rType == \"ban\") {\r\n                        \$.toast(\"";
echo $_["device_confirmed_5"];
echo "\");\r\n                    } else if (rType == \"kill\") {\r\n                        \$.toast(\"";
echo $_["all_connections_for_this_user_have_been_killed"];
echo "\");\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {\r\n                        \$(this).remove();\r\n                    });\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\r\n                } else {\r\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\r\n                }\r\n            });\r\n        }\r\n\t\tfunction renew_user(rid, username) {\r\n            \$(\"#renew_type\").val(\"\");\r\n            \$(\"#renew_button\").attr(\"disabled\", true);\r\n            \$('.RenewModal').data('id', rid );\r\n\t\t\t\$('.RenewModal').data('username', username );\r\n\t\t\t\$(\"#renewModal\").text(\"Customer subscription : \"+ \$('.RenewModal').data('username') );\r\n            \$('.RenewModal').modal('show');\r\n        }\r\n        function toggleAuto() {\r\n            if (autoRefresh == true) {\r\n                autoRefresh = false;\r\n                \$(\".auto-text\").html(\"";
echo $_["manual_mode"];
echo "\");\r\n            } else {\r\n                autoRefresh = true;\r\n                \$(\".auto-text\").html(\"";
echo $_["auto_refresh"];
echo "\");\r\n            }\r\n        }\r\n        function getFilter() {\r\n            return \$(\"#mag_filter\").val();\r\n        }\r\n        function getReseller() {\r\n            return \$(\"#mag_reseller\").val();\r\n        }\r\n        \r\n        function reloadUsers() {\r\n            if (autoRefresh == true) {\r\n                \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                \$(\"#datatable-users\").DataTable().ajax.reload(null, false);\r\n            }\r\n            setTimeout(reloadUsers, 10000);\r\n        }\r\n        function changeZoom() {\r\n            if (\$(\"#datatable-users\").hasClass(\"font-large\")) {\r\n                \$(\"#datatable-users\").removeClass(\"font-large\");\r\n                \$(\"#datatable-users\").addClass(\"font-normal\");\r\n            } else if (\$(\"#datatable-users\").hasClass(\"font-normal\")) {\r\n                \$(\"#datatable-users\").removeClass(\"font-normal\");\r\n                \$(\"#datatable-users\").addClass(\"font-small\");\r\n            } else {\r\n                \$(\"#datatable-users\").removeClass(\"font-small\");\r\n                \$(\"#datatable-users\").addClass(\"font-large\");\r\n            }\r\n            \$(\"#datatable-users\").DataTable().draw();\r\n        }\r\n        function clearFilters() {\r\n            window.rClearing = true;\r\n            \$(\"#mag_search\").val(\"\").trigger('change');\r\n            \$('#mag_filter').val(\"\").trigger('change');\r\n            \$('#mag_reseller').val(\"\").trigger('change');\r\n            \$('#mag_show_entries').val(\"";
echo $rAdminSettings["default_entries"] ?: 10;
echo "\").trigger('change');\r\n            window.rClearing = false;\r\n            \$('#datatable-users').DataTable().search(\$(\"#mag_search\").val());\r\n            \$('#datatable-users').DataTable().page.len(\$('#mag_show_entries').val());\r\n            \$(\"#datatable-users\").DataTable().page(0).draw('page');\r\n            \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n            \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\r\n        }\r\n\t\t";
if ($rPermissions["is_admin"] || $rPermissions["is_reseller"] && $rAdminSettings["reseller_mag_events"]) {
    echo "\t\t\t\t\t \r\n\t\tfunction message(id, mac) {\r\n            \$('.messageModal').data('id', id);\r\n\t\t\t\$(\"#messageModal\").text(\"Send Event - \" + mac.toUpperCase());\r\n\t\t\t\$(\"#message_type\").val(\"\").trigger(\"change\");\r\n\t\t\t\$(\"#message\").val(\"\");\r\n\t\t\t\$(\"#selected_channel\").val(\"\");\r\n\t\t\t\$(\"#send_msg_form\").hide();\r\n\t\t\t\$(\"#play_channel_form\").hide();\r\n            \$('.messageModal').modal('show');\r\n        }\r\n\t\t";
}
echo "        \$(document).ready(function() {\r\n\t\t\t\$(window).keypress(function(event){\r\n\t\t\t\tif(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n\t\t\t});\r\n            formCache.init();\r\n            formCache.fetch();\r\n            \r\n            \$.fn.dataTable.ext.errMode = 'none';\r\n            \$('select').select2({width: '100%'});\r\n\t\t\t\$(\".js-switch\").each(function (index, element) {\r\n                var init = new Switchery(element);\r\n            });\r\n            \$(\"#datatable-users\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\",\r\n                    },\r\n                    infoFiltered: \"\"\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                },\r\n                createdRow: function(row, data, index) {\r\n                    \$(row).addClass('user-' + data[0]);\r\n                },\r\n                responsive: false,\r\n                processing: true,\r\n                serverSide: true,\r\n                ajax: {\r\n                    url: \"./table_search.php\",\r\n                    \"data\": function(d) {\r\n                        d.id = \"mags\",\r\n                        d.filter = getFilter(),\r\n                        d.reseller = getReseller()\r\n                    }\r\n                },\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2,3,4,5,6,7,8,9,10]},\r\n                    {\"orderable\": false, \"targets\": [10]},\r\n                    {\"visible\": true, \"targets\": [1]}\r\n                ],\r\n                order: [[ 0, \"desc\" ]],\r\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\r\n                stateSave: true\r\n            });\r\n            \$(\"#datatable-users\").css(\"width\", \"100%\");\r\n            \$('#mag_search').keyup(function(){\r\n                if (!window.rClearing) {\r\n                    \$('#datatable-users').DataTable().search(\$(this).val()).draw();\r\n                }\r\n            });\r\n            \$('#mag_show_entries').change(function(){\r\n                if (!window.rClearing) {\r\n                    \$('#datatable-users').DataTable().page.len(\$(this).val()).draw();\r\n                }\r\n            });\r\n            \$('#mag_filter').change(function(){\r\n                if (!window.rClearing) {\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\r\n                }\r\n            });\r\n            \$('#mag_reseller').change(function(){\r\n                if (!window.rClearing) {\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\r\n                    \$(\"#datatable-users\").DataTable().ajax.reload( null, false );\r\n                }\r\n            });\r\n\t\t\t";
if ($rPermissions["is_admin"] || $rPermissions["is_reseller"] && $rAdminSettings["reseller_mag_events"]) {
    echo "\t\t\t\t \r\n\t\t\t\$(\"#message_type\").change(function(){\r\n\t\t\t\tif (\$(this).val() == \"send_msg\") {\r\n\t\t\t\t\t\$(\"#send_msg_form\").show();\r\n\t\t\t\t\t\$(\"#play_channel_form\").hide();\r\n\t\t\t\t\t\$(\"#message_submit\").attr(\"disabled\", false);\r\n\t\t\t\t} else if (\$(this).val() == \"play_channel\") {\r\n\t\t\t\t\t\$(\"#send_msg_form\").hide();\r\n\t\t\t\t\t\$(\"#play_channel_form\").show();\r\n\t\t\t\t\t\$(\"#message_submit\").attr(\"disabled\", false);\r\n\t\t\t\t} else {\r\n\t\t\t\t\t\$(\"#send_msg_form\").hide();\r\n\t\t\t\t\t\$(\"#play_channel_form\").hide();\r\n\t\t\t\t\tif (\$(this).val() == \"\") {\r\n\t\t\t\t\t\t\$(\"#message_submit\").attr(\"disabled\", true);\r\n\t\t\t\t\t} else {\r\n\t\t\t\t\t\t\$(\"#message_submit\").attr(\"disabled\", false);\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t});\r\n\t\t\t\$('#selected_channel').select2({\r\n              ajax: {\r\n                url: './api.php',\r\n                dataType: 'json',\r\n                data: function (params) {\r\n                  return {\r\n                    search: params.term,\r\n                    action: 'streamlist',\r\n                    page: params.page\r\n                  };\r\n                },\r\n                processResults: function (data, params) {\r\n                  params.page = params.page || 1;\r\n                  return {\r\n                    results: data.items,\r\n                    pagination: {\r\n                        more: (params.page * 100) < data.total_count\r\n                    }\r\n                  };\r\n                },\r\n                cache: true\r\n              },\r\n              placeholder: '";
    echo $_["start_typing"];
    echo "...',\r\n\t\t\t  width: \"100%\"\r\n            });\r\n\t\t\t\r\n\t\t\t\$(\"#renew_type\").change(function() {\r\n                if (\$(\"#renew_type\").val().length > 0) {\r\n                    \$(\"#renew_button\").attr(\"disabled\", false);\r\n                } else {\r\n                    \$(\"#renew_button\").attr(\"disabled\", true);\r\n                }\r\n            });\r\n\t\t\t\$(\"#message_submit\").click(function() {\r\n\t\t\t\trArray = {\"id\": \$('.messageModal').data('id'), \"type\": \$(\"#message_type\").val()};\r\n\t\t\t\tif (rArray.type.length > 0) {\r\n\t\t\t\t\tif (rArray.type == \"send_msg\") {\r\n\t\t\t\t\t\trArray.message = \$(\"#message\").val();\r\n\t\t\t\t\t\tif (\$(\"#reboot_portal\").is(\":checked\")) {\r\n\t\t\t\t\t\t\trArray.reboot_portal = 1;\r\n\t\t\t\t\t\t} else {\r\n\t\t\t\t\t\t\trArray.reboot_portal = 0;\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t} else if (rArray.type == \"play_channel\") {\r\n\t\t\t\t\t\trArray.channel = \$(\"#selected_channel\").val();\r\n\t\t\t\t\t\tif (!rArray.channel) {\r\n\t\t\t\t\t\t\trArray.channel = \"\";\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t}\r\n\t\t\t\t\tif ((rArray.type == \"send_msg\") && (rArray.message.length == 0)) {\r\n\t\t\t\t\t\t\$.toast(\"";
    echo $_["mag_toast_1"];
    echo ".\");\r\n\t\t\t\t\t} else if ((rArray.type == \"play_channel\") && (rArray.channel.length == 0)) {\r\n\t\t\t\t\t\t\$.toast(\"";
    echo $_["mag_toast_2"];
    echo ".\");\r\n\t\t\t\t\t} else {\r\n\t\t\t\t\t\t\$('.messageModal').modal('hide');\r\n\t\t\t\t\t\t\$.getJSON(\"./api.php?action=send_event&data=\" + encodeURIComponent(JSON.stringify(rArray)), function(data) {\r\n\t\t\t\t\t\t\tif (data.result === true) {\r\n\t\t\t\t\t\t\t\t\$.toast(\"";
    echo $_["mag_toast_3"];
    echo ".\");\r\n\t\t\t\t\t\t\t} else {\r\n\t\t\t\t\t\t\t\t\$.toast(\"";
    echo $_["mag_toast_4"];
    echo ".\");\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t});\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t});\r\n            ";
}
if (!$detect->isMobile()) {
    echo "            setTimeout(reloadUsers, 10000);\r\n            ";
}
echo "            \$('#datatable-users').DataTable().search(\$(this).val()).draw();\r\n            ";
if (!$rAdminSettings["auto_refresh"]) {
    echo "            toggleAuto();\r\n            ";
}
echo "        });\r\n        \$(window).bind('beforeunload', function() {\r\n            formCache.save();\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>