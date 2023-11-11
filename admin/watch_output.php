<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "folder_watch_output")) {
    exit;
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"#\" onClick=\"clearFilters();\">\n                                            <button type=\"button\" class=\"btn btn-warning waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-filter-remove\"></i>\n                                            </button>\n                                        </a>\n                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm btn-clear-logs\">\n                                            <i class=\"mdi mdi-minus\"></i> ";
echo $_["clear_logs"];
echo "                                        </button>\n                                        <a href=\"./watch.php\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                ";
echo $_["view_folders"];
echo "                                            </button>\n                                        </a>\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["folder_watch_output"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <form id=\"series_form\">\n                                    <div class=\"form-group row mb-4\">\n                                        <div class=\"col-md-3\">\n                                            <input type=\"text\" class=\"form-control\" id=\"result_search\" value=\"\" placeholder=\"";
echo $_["search_results"];
echo "\">\n                                        </div>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"result_server\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
echo $_["all_servers"];
echo "</option>\n                                                ";
foreach ($rServers as $rServer) {
    echo "                                                <option value=\"";
    echo $rServer["id"];
    echo "\">";
    echo $rServer["server_name"];
    echo "</option>\n                                                ";
}
echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"result_type\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
echo $_["all_types"];
echo "</option>\n                                                ";
foreach (["1" => "Movies", "2" => "Series"] as $rID => $rType) {
    echo "                                                <option value=\"";
    echo $rID;
    echo "\">";
    echo $rType;
    echo "</option>\n                                                ";
}
echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"result_status\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
echo $_["all_statuses"];
echo "</option>\n                                                ";
foreach (["1" => "Added", "2" => "SQL Error", "3" => "No Category", "4" => "No Match", "5" => "Invalid File"] as $rID => $rType) {
    echo "                                                <option value=\"";
    echo $rID;
    echo "\">";
    echo $rType;
    echo "</option>\n                                                ";
}
echo "                                            </select>\n                                        </div>\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"result_show_entries\">";
echo $_["show"];
echo "</label>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"result_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                ";
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
echo "                                            </select>\n                                        </div>\n                                    </div>\n                                </form>\n                                <table id=\"datatable-md1\" class=\"table table-hover dt-responsive nowrap font-normal\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                            <th>";
echo $_["type"];
echo "</th>\n                                            <th>";
echo $_["server"];
echo "</th>\n                                            <th>";
echo $_["filename"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["status"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["date_added"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody></tbody>\n                                </table>\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <div class=\"modal fade bs-logs-modal-center\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"clearLogsLabel\" aria-hidden=\"true\" style=\"display: none;\" data-id=\"\">\n            <div class=\"modal-dialog modal-dialog-centered\">\n                <div class=\"modal-content\">\n                    <div class=\"modal-header\">\n                        <h4 class=\"modal-title\" id=\"clearLogsLabel\">";
echo $_["clear_logs"];
echo "</h4>\n                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n                    </div>\n                    <div class=\"modal-body\">\n                        <div class=\"form-group row mb-4\">\n                            <label class=\"col-md-4 col-form-label\" for=\"range_clear\">";
echo $_["date_range"];
echo "</label>\n                            <div class=\"col-md-4\">\n                                <input type=\"text\" class=\"form-control text-center date\" id=\"range_clear_from\" name=\"range_clear_from\" data-toggle=\"date-picker\" data-single-date-picker=\"true\" autocomplete=\"off\" placeholder=\"From\">\n                            </div>\n                            <div class=\"col-md-4\">\n                                <input type=\"text\" class=\"form-control text-center date\" id=\"range_clear_to\" name=\"range_clear_to\" data-toggle=\"date-picker\" data-single-date-picker=\"true\" autocomplete=\"off\" placeholder=\"To\">\n                            </div>\n                        </div>\n                        <div class=\"text-center\">\n                            <input id=\"clear_logs\" type=\"submit\" class=\"btn btn-primary\" value=\"Clear\" style=\"width:100%\" />\n                        </div>\n                    </div>\n                </div><!-- /.modal-content -->\n            </div><!-- /.modal-dialog -->\n        </div><!-- /.modal -->\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/js/pages/form-remember.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var rClearing = false;\n        \n        ";
if ($rPermissions["is_admin"]) {
    echo "        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
    echo $_["are_you_sure_you_want_to_delete_this_user_this_record"];
    echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=watch_output&sub=\" + rType + \"&result_id=\" + rID, function(data) {\n                if (data.result == true) {\n                    if (rType == \"delete\") {\n                        \$.toast(\"Record successfully deleted.\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-md1\").DataTable().ajax.reload( null, false );\n                } else {\n                    \$.toast(\"";
    echo $_["an_error_occured_while_processing_your_request"];
    echo "\");\n                }\n            }).fail(function() {\n                \$.toast(\"";
    echo $_["an_error_occured_while_processing_your_request"];
    echo "\");\n            });\n        }\n        ";
}
echo "        function getServer() {\n            return \$(\"#result_server\").val();\n        }\n        function getType() {\n            return \$(\"#result_type\").val();\n        }\n        function getStatus() {\n            return \$(\"#result_status\").val();\n        }\n        function clearFilters() {\n            window.rClearing = true;\n            \$(\"#result_search\").val(\"\").trigger('change');\n            \$('#result_server').val(\"\").trigger('change');\n            \$('#result_type').val(\"\").trigger('change');\n            \$('#result_status').val(\"\").trigger('change');\n            \$('#result_show_entries').val(\"";
echo $rAdminSettings["default_entries"] ?: 10;
echo "\").trigger('change');\n            window.rClearing = false;\n            \$('#datatable-md1').DataTable().search(\$(\"#result_search\").val());\n            \$('#datatable-md1').DataTable().page.len(\$('#result_show_entries').val());\n            \$(\"#datatable-md1\").DataTable().page(0).draw('page');\n            \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n            \$(\"#datatable-md1\").DataTable().ajax.reload( null, false );\n        }\n        \$(document).ready(function() {\n            formCache.init();\n            formCache.fetch();\n            \n            \$('select').select2({width: '100%'});\n            \$(\"#datatable-md1\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('result-' + data[0]);\n                },\n                responsive: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"watch_output\";\n                        d.server = getServer();\n                        d.type = getType();\n                        d.status = getStatus();\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,4,5,6]},\n                    {\"orderable\": false, \"targets\": [6]}\n                ],\n                order: [[ 5, \"desc\" ]],\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\n                stateSave: true\n            });\n            \$(\"#datatable-md1\").css(\"width\", \"100%\");\n            \$('#result_search').keyup(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-md1').DataTable().search(\$(this).val()).draw();\n                }\n            })\n            \$('#result_show_entries').change(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-md1').DataTable().page.len(\$(this).val()).draw();\n                }\n            })\n            \$('#result_server').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-md1\").DataTable().ajax.reload( null, false );\n                }\n            })\n            \$('#result_type').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-md1\").DataTable().ajax.reload( null, false );\n                }\n            })\n            \$('#result_status').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-md1\").DataTable().ajax.reload( null, false );\n                }\n            })\n            \$('#datatable-md1').DataTable().search(\$(this).val()).draw();\n            \$('#range_clear_to').daterangepicker({\n                singleDatePicker: true,\n                showDropdowns: true,\n                locale: {\n                    format: 'YYYY-MM-DD'\n                },\n                autoUpdateInput: false\n            }).val(\"\");\n            \$('#range_clear_from').daterangepicker({\n                singleDatePicker: true,\n                showDropdowns: true,\n                locale: {\n                    format: 'YYYY-MM-DD'\n                },\n                autoUpdateInput: false\n            }).val(\"\");\n            \$('#range_clear_from').on('apply.daterangepicker', function(ev, picker) {\n                \$(this).val(picker.startDate.format('YYYY-MM-DD'));\n            });\n            \$('#range_clear_from').on('cancel.daterangepicker', function(ev, picker) {\n                \$(this).val('');\n            });\n            \$('#range_clear_to').on('apply.daterangepicker', function(ev, picker) {\n                \$(this).val(picker.startDate.format('YYYY-MM-DD'));\n            });\n            \$('#range_clear_to').on('cancel.daterangepicker', function(ev, picker) {\n                \$(this).val('');\n            });\n            \$(\".btn-clear-logs\").click(function() {\n                \$(\".bs-logs-modal-center\").modal(\"show\");\n            });\n            \$(\"#clear_logs\").click(function() {\n                if (confirm('";
echo $_["are_you_sure_you_want_to_clear_logs_for_this_period"];
echo "') == false) {\n                    return;\n                }\n                \$(\".bs-logs-modal-center\").modal(\"hide\");\n                \$.getJSON(\"./api.php?action=clear_logs&type=watch_output&from=\" + encodeURIComponent(\$(\"#range_clear_from\").val()) + \"&to=\" + encodeURIComponent(\$(\"#range_clear_to\").val()), function(data) {\n                    \$.toast(\"";
echo $_["logs_have_been_cleared"];
echo "\");\n                    //window.location.href = './watch_output.php';\n                });\n            });\n        });\n        \n        \$(window).bind('beforeunload', function() {\n            formCache.save();\n        });\n        </script>\n    </body>\n</html>";

?>