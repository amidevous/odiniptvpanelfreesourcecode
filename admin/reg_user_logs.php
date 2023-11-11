<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "reg_userlog")) {
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
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n                                    <li>\r\n                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm btn-clear-logs\">\r\n                                            <i class=\"mdi mdi-minus\"></i> ";
echo $_["clear_logs"];
echo "                                        </button>\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $_["reseller_logs"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\r\n                                <div class=\"form-group row mb-4\">\r\n                                    <div class=\"col-md-3\">\r\n                                        <input type=\"text\" class=\"form-control\" id=\"log_search\" value=\"\" placeholder=\"";
echo $_["search_logs"];
echo "\">\r\n                                    </div>\r\n                                    <label class=\"col-md-1 col-form-label text-center\" for=\"reseller\">";
echo $_["reseller"];
echo "</label>\r\n                                    <div class=\"col-md-3\">\r\n                                        <select id=\"reseller\" class=\"form-control\" data-toggle=\"select2\">\r\n                                            <option value=\"\" selected>";
echo $_["all_resellers"];
echo "</option>\r\n                                            ";
foreach (getRegisteredUsers() as $rReseller) {
    echo "                                            <option value=\"";
    echo $rReseller["id"];
    echo "\">";
    echo $rReseller["username"];
    echo "</option>\r\n                                            ";
}
echo "                                        </select>\r\n                                    </div>\r\n                                    <label class=\"col-md-1 col-form-label text-center\" for=\"range\">";
echo $_["dates"];
echo "</label>\r\n                                    <div class=\"col-md-2\">\r\n                                        <input type=\"text\" class=\"form-control text-center date\" id=\"range\" name=\"range\" data-toggle=\"date-picker\" data-single-date-picker=\"true\" autocomplete=\"off\" placeholder=\"";
echo $_["all_dates"];
echo "\">\r\n                                    </div>\r\n                                    <label class=\"col-md-1 col-form-label text-center\" for=\"show_entries\">";
echo $_["show"];
echo "</label>\r\n                                    <div class=\"col-md-1\">\r\n                                        <select id=\"show_entries\" class=\"form-control\" data-toggle=\"select2\">\r\n                                            ";
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
echo $_["id"];
echo "</th>\r\n                                            <th>";
echo $_["reseller"];
echo "</th>\r\n                                            <th>";
echo $_["user_device"];
echo "</th>\r\n                                            <th>";
echo $_["log_message"];
echo "</th>\r\n                                            <th>";
echo $_["date"];
echo "</th>\r\n\t\t\t\t\t\t\t\t\t\t\t<th>";
echo $_["action"];
echo "</th>\r\n                                        </tr>\r\n                                    </thead>\r\n                                    <tbody></tbody>\r\n                                </table>\r\n\r\n                            </div> <!-- end card body-->\r\n                        </div> <!-- end card -->\r\n                    </div><!-- end col-->\r\n                </div>\r\n                <!-- end row-->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <div class=\"modal fade bs-logs-modal-center\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"clearLogsLabel\" aria-hidden=\"true\" style=\"display: none;\" data-id=\"\">\r\n            <div class=\"modal-dialog modal-dialog-centered\">\r\n                <div class=\"modal-content\">\r\n                    <div class=\"modal-header\">\r\n                        <h4 class=\"modal-title\" id=\"clearLogsLabel\">";
echo $_["clear_logs"];
echo "</h4>\r\n                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\r\n                    </div>\r\n                    <div class=\"modal-body\">\r\n                        <div class=\"form-group row mb-4\">\r\n                            <label class=\"col-md-4 col-form-label\" for=\"range_clear\">";
echo $_["date_range"];
echo "</label>\r\n                            <div class=\"col-md-4\">\r\n                                <input type=\"text\" class=\"form-control text-center date\" id=\"range_clear_from\" name=\"range_clear_from\" data-toggle=\"date-picker\" data-single-date-picker=\"true\" autocomplete=\"off\" placeholder=\"";
echo $_["from"];
echo "\">\r\n                            </div>\r\n                            <div class=\"col-md-4\">\r\n                                <input type=\"text\" class=\"form-control text-center date\" id=\"range_clear_to\" name=\"range_clear_to\" data-toggle=\"date-picker\" data-single-date-picker=\"true\" autocomplete=\"off\" placeholder=\"";
echo $_["to"];
echo "\">\r\n                            </div>\r\n                        </div>\r\n                        <div class=\"text-center\">\r\n                            <input id=\"clear_logs\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["clear"];
echo "\" style=\"width:100%\" />\r\n                        </div>\r\n                    </div>\r\n                </div><!-- /.modal-content -->\r\n            </div><!-- /.modal-dialog -->\r\n        </div><!-- /.modal -->\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n\r\n        <script>\r\n        function api(rID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('Are you sure you want to delete this entry.') == false) {\r\n                    return;\r\n                }\r\n            }\r\n            \$.getJSON(\"./api.php?action=reg_user_logs&sub=\" + rType + \"&id=\" + rID, function(data) {\r\n                if (data.result === true) {\r\n                    if (rType == \"delete\") {\r\n                        \$(\"#id-\" + rID).remove();\r\n                        \$.toast(\"Entry successfully deleted.\");\r\n\t\t\t\t\t\t\$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\r\n                    }\r\n                    \$.each(\$('.tooltip'), function (index, element) {\r\n                        \$(this).remove();\r\n                    });\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                } else {\r\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\r\n                }\r\n            });\r\n        }\r\n\t\tfunction getReseller() {\r\n            return \$(\"#reseller\").val();\r\n        }\r\n        function getRange() {\r\n            return \$(\"#range\").val();\r\n        }\r\n\r\n        \$(document).ready(function() {\r\n\t\t\t\$(window).keypress(function(event){\r\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\r\n            });\r\n            \$('select').select2({width: '100%'});\r\n            \$('#range').daterangepicker({\r\n                singleDatePicker: false,\r\n                showDropdowns: true,\r\n                locale: {\r\n                    format: 'YYYY-MM-DD'\r\n                },\r\n                autoUpdateInput: false\r\n            }).val(\"\");\r\n            \$('#range').on('apply.daterangepicker', function(ev, picker) {\r\n                \$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));\r\n                \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\r\n            });\r\n            \$('#range').on('cancel.daterangepicker', function(ev, picker) {\r\n                \$(this).val('');\r\n                \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\r\n            });\r\n            \$('#range').on('change', function() {\r\n                \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\r\n            });\r\n            \$('#range_clear_to').daterangepicker({\r\n                singleDatePicker: true,\r\n                showDropdowns: true,\r\n                locale: {\r\n                    format: 'YYYY-MM-DD'\r\n                },\r\n                autoUpdateInput: false\r\n            }).val(\"\");\r\n            \$('#range_clear_from').daterangepicker({\r\n                singleDatePicker: true,\r\n                showDropdowns: true,\r\n                locale: {\r\n                    format: 'YYYY-MM-DD'\r\n                },\r\n                autoUpdateInput: false\r\n            }).val(\"\");\r\n            \$('#range_clear_from').on('apply.daterangepicker', function(ev, picker) {\r\n                \$(this).val(picker.startDate.format('YYYY-MM-DD'));\r\n            });\r\n            \$('#range_clear_from').on('cancel.daterangepicker', function(ev, picker) {\r\n                \$(this).val('');\r\n            });\r\n            \$('#range_clear_to').on('apply.daterangepicker', function(ev, picker) {\r\n                \$(this).val(picker.startDate.format('YYYY-MM-DD'));\r\n            });\r\n            \$('#range_clear_to').on('cancel.daterangepicker', function(ev, picker) {\r\n                \$(this).val('');\r\n            });\r\n            \$(\".btn-clear-logs\").click(function() {\r\n                \$(\".bs-logs-modal-center\").modal(\"show\");\r\n            });\r\n            \$(\"#clear_logs\").click(function() {\r\n                if (confirm('";
echo $_["are_you_sure_you_want_to_clear_logs_for_this_period"];
echo "') == false) {\r\n                    return;\r\n                }\r\n                \$(\".bs-logs-modal-center\").modal(\"hide\");\r\n                \$.getJSON(\"./api.php?action=clear_logs&type=reg_userlog&from=\" + encodeURIComponent(\$(\"#range_clear_from\").val()) + \"&to=\" + encodeURIComponent(\$(\"#range_clear_to\").val()), function(data) {\r\n                    \$.toast(\"Logs have been cleared.\");\r\n                    \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\r\n                });\r\n            });\r\n            \$(\"#datatable-activity\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    },\r\n                    infoFiltered: \"\"\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\r\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\r\n                },\r\n                responsive: false,\r\n                processing: true,\r\n                serverSide: true,\r\n                ajax: {\r\n                    url: \"./table_search.php\",\r\n                    \"data\": function(d) {\r\n                        d.id = \"reg_user_logs\",\r\n                        d.range = getRange(),\r\n                        d.reseller = getReseller()\r\n                    }\r\n                },\r\n                columnDefs: [\r\n                    {\"className\": \"dt-center\", \"targets\": [0,5]},\r\n\t\t\t\t\t{\"orderable\": false, \"targets\": [5]},\r\n                    {\"visible\": false, \"targets\": []}\r\n                ],\r\n                \"order\": [[ 0, \"desc\" ]],\r\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo "            });\r\n            \$(\"#datatable-activity\").css(\"width\", \"100%\");\r\n            \$('#log_search').keyup(function(){\r\n                \$('#datatable-activity').DataTable().search(\$(this).val()).draw();\r\n            })\r\n            \$('#show_entries').change(function(){\r\n                \$('#datatable-activity').DataTable().page.len(\$(this).val()).draw();\r\n            })\r\n            \$('#reseller').change(function(){\r\n                \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\r\n            })\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>