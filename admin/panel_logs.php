<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "panel_errors")) {
    exit;
}
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
echo "\n                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm btn-clear-logs\">\n                                            <i class=\"mdi mdi-minus\"></i> ";
echo $_["clear_logs"];
echo " \n                                        </button>\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["panel_logs"];
echo " </h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n\t\t\t\t\t\t\t    <div class=\"form-group row mb-4\">\n                                </div>\n                                <table id=\"datatable\" class=\"table table-bordered mb-0\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo " </th>\n                                            <th class=\"text-center\">";
echo $_["log_message"];
echo " </th>\n                                            <th class=\"text-center\">";
echo $_["date"];
echo " </th>\t\t\t\t\t\t\t\t\t\t\t\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n\t\t\t\t\t\t\t\t\t    ";
foreach (getPanelLogs() as $rPlog) {
    echo "                                        <tr>\n                                            <td class=\"text-center\">";
    echo $rPlog["id"];
    echo " </td>\n\t\t\t\t\t\t\t\t\t\t\t<td>";
    echo 130 < strlen($rPlog["log_message"]) ? substr($rPlog["log_message"], 0, 130) . "..." : $rPlog["log_message"];
    echo " </td>\n                                            <td class=\"text-center\">";
    echo date("Y-m-d H:i", $rPlog["date"]);
    echo " </td>\n                                        </tr>\n                                        ";
}
echo "                                    </tbody>\n                                </table>\n\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n\t\t<div class=\"modal fade bs-logs-modal-center\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"clearLogsLabel\" aria-hidden=\"true\" style=\"display: none;\" data-id=\"\">\n            <div class=\"modal-dialog modal-dialog-centered\">\n                <div class=\"modal-content\">\n                    <div class=\"modal-header\">\n                        <h4 class=\"modal-title\" id=\"clearLogsLabel\">";
echo $_["clear_logs"];
echo " </h4>\n                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n                    </div>\n                    <div class=\"modal-body\">\n                        <div class=\"form-group row mb-4\">\n                            <label class=\"col-md-4 col-form-label\" for=\"range_clear\">";
echo $_["date_range"];
echo " </label>\n                            <div class=\"col-md-4\">\n                                <input type=\"text\" class=\"form-control text-center date\" id=\"range_clear_from\" name=\"range_clear_from\" data-toggle=\"date-picker\" data-single-date-picker=\"true\" autocomplete=\"off\" placeholder=\"";
echo $_["from"];
echo "\">\n                            </div>\n                            <div class=\"col-md-4\">\n                                <input type=\"text\" class=\"form-control text-center date\" id=\"range_clear_to\" name=\"range_clear_to\" data-toggle=\"date-picker\" data-single-date-picker=\"true\" autocomplete=\"off\" placeholder=\"";
echo $_["to"];
echo "\">\n                            </div>\n                        </div>\n                        <div class=\"text-center\">\n                            <input id=\"clear_logs\" type=\"submit\" class=\"btn btn-primary\" value=\"Clear\" style=\"width:100%\" />\n                        </div>\n                    </div>\n                </div><!-- /.modal-content -->\n            </div><!-- /.modal-dialog -->\n        </div><!-- /.modal -->\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n\t\t<script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n\t\t<script src=\"assets/libs/moment/moment.min.js\"></script>\n\t\t<script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\n        <!-- Datatables init -->\n        <script>\n        function getServer() {\n            return \$(\"#server\").val();\n        }\n        function getRange() {\n            return \$(\"#range\").val();\n        }\n\n        \$(document).ready(function() {\n\t\t\t\$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$('select').select2({width: '100%'});\n            \$('#range').daterangepicker({\n                singleDatePicker: false,\n                showDropdowns: true,\n                locale: {\n                    format: 'YYYY-MM-DD'\n                },\n                autoUpdateInput: false\n            }).val(\"\");\n            \$('#range').on('apply.daterangepicker', function(ev, picker) {\n                \$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));\n                \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\n            });\n            \$('#range').on('cancel.daterangepicker', function(ev, picker) {\n                \$(this).val('');\n                \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\n            });\n            \$('#range').on('change', function() {\n                \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\n            });\n            \$('#range_clear_to').daterangepicker({\n                singleDatePicker: true,\n                showDropdowns: true,\n                locale: {\n                    format: 'YYYY-MM-DD'\n                },\n                autoUpdateInput: false\n            }).val(\"\");\n            \$('#range_clear_from').daterangepicker({\n                singleDatePicker: true,\n                showDropdowns: true,\n                locale: {\n                    format: 'YYYY-MM-DD'\n                },\n                autoUpdateInput: false\n            }).val(\"\");\n            \$('#range_clear_from').on('apply.daterangepicker', function(ev, picker) {\n                \$(this).val(picker.startDate.format('YYYY-MM-DD'));\n            });\n            \$('#range_clear_from').on('cancel.daterangepicker', function(ev, picker) {\n                \$(this).val('');\n            });\n            \$('#range_clear_to').on('apply.daterangepicker', function(ev, picker) {\n                \$(this).val(picker.startDate.format('YYYY-MM-DD'));\n            });\n            \$('#range_clear_to').on('cancel.daterangepicker', function(ev, picker) {\n                \$(this).val('');\n            });\n            \$(\".btn-clear-logs\").click(function() {\n                \$(\".bs-logs-modal-center\").modal(\"show\");\n            });\n            \$(\"#clear_logs\").click(function() {\n                if (confirm('";
echo $_["are_you_sure_you_want_to_clear"];
echo "') == false) {\n                    return;\n                }\n                \$(\".bs-logs-modal-center\").modal(\"hide\");\n                \$.getJSON(\"./api.php?action=clear_logs&type=panel_logs&from=\" + encodeURIComponent(\$(\"#range_clear_from\").val()) + \"&to=\" + encodeURIComponent(\$(\"#range_clear_to\").val()), function(data) {\n                    \$.toast(\"Logs have been cleared.\");\n                    \$(\"#datatable-activity\").DataTable().ajax.reload( null, false );\n                });\n            });\t\t\t\n            \$(\"#datatable\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n\t\t\t\tcolumnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,2]},\n\t\t\t\t\t{\"orderable\": false, \"targets\": [1,2]},\n                    {\"visible\": false, \"targets\": []}\n                ],\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n\t\t\t\t\t\$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                responsive: false\n            });\n            \$(\"#datatable\").css(\"width\", \"100%\");\n        });\n        </script>\n    </body>\n</html>";

?>