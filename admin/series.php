<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if ($rPermissions["is_reseller"] && !$rPermissions["reset_stb_data"]) {
    exit;
}
if ($rPermissions["is_admin"] && !hasPermissions("adv", "series")) {
    exit;
}
$rCategories = getCategories("series");
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page";
    if ($rPermissions["is_reseller"]) {
        echo " boxed-layout-ext";
    }
    echo "\"><div class=\"content\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper";
    if ($rPermissions["is_reseller"]) {
        echo " boxed-layout-ext";
    }
    echo "\"><div class=\"container-fluid\">\n        ";
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
if ($rPermissions["is_admin"] && hasPermissions("adv", "add_series")) {
    echo "                                        <a href=\"serie.php\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_series"];
    echo "                                            </button>\n                                        </a>\n                                        ";
}
echo "                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["series"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <form id=\"series_form\">\n                                    <div class=\"form-group row mb-4\">\n                                        <div class=\"col-md-6\">\n                                            <input type=\"text\" class=\"form-control\" id=\"series_search\" value=\"\" placeholder=\"";
echo $_["search_series"];
echo "\">\n                                        </div>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"series_category_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
echo $_["all_categories"];
echo "</option>\n                                                <option value=\"-1\">";
echo $_["no_tmdb_match"];
echo "</option>\n                                                ";
foreach ($rCategories as $rCategory) {
    echo "                                                <option value=\"";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                ";
}
echo "                                            </select>\n                                        </div>\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"series_show_entries\">";
echo $_["show"];
echo "</label>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"series_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo $_["selected"];
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\n                                                ";
}
echo "                                            </select>\n                                        </div>\n                                    </div>\n                                </form>\n                                <table id=\"datatable-streampage\" class=\"table table-hover dt-responsive nowrap font-normal\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">Cover</th>\n                                            <th>";
echo $_["name"];
echo "</th>\n                                            <th>";
echo $_["category"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["seasons"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["episodes"];
echo "</th>\n                                            <th class=\"text-center\">";
echo $_["first_aired"];
echo "</th>\n                                            ";
if ($rPermissions["is_admin"]) {
    echo "                                            <th class=\"text-center\">";
    echo $_["last_updated"];
    echo "</th>\n                                            <th class=\"text-center\">";
    echo $_["actions"];
    echo "</th>\n                                            ";
}
echo "                                        </tr>\n                                    </thead>\n                                    <tbody></tbody>\n                                </table>\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/js/pages/form-remember.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var autoRefresh = true;\n        var rClearing = false;\n        \n        function toggleAuto() {\n            if (autoRefresh == true) {\n                autoRefresh = false;\n                \$(\".auto-text\").html(\"";
echo $_["manual_mode"];
echo "\");\n            } else {\n                autoRefresh = true;\n                \$(\".auto-text\").html(\"";
echo $_["auto_refresh"];
echo "\");\n            }\n        }\n        ";
if ($rPermissions["is_admin"]) {
    echo "        function api(rID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
    echo $_["are_you_sure_you_want_to_delete_this_series"];
    echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=series&sub=\" + rType + \"&series_id=\" + rID, function(data) {\n                if (data.result == true) {\n                    if (rType == \"delete\") {\n                        \$.toast(\"";
    echo $_["series_successfully_deleted"];
    echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                } else {\n                    \$.toast(\"";
    echo $_["an_error_occured_while_processing_your_request"];
    echo "\");\n                }\n            }).fail(function() {\n                \$.toast(\"";
    echo $_["an_error_occured_while_processing_your_request"];
    echo "\");\n            });\n        }\n        ";
}
echo "        function reloadStreams() {\n            if (autoRefresh == true) {\n                \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n            }\n            setTimeout(reloadStreams, 5000);\n        }\n        function getCategory() {\n            return \$(\"#series_category_id\").val();\n        }\n        function changeZoom() {\n            if (\$(\"#datatable-streampage\").hasClass(\"font-large\")) {\n                \$(\"#datatable-streampage\").removeClass(\"font-large\");\n                \$(\"#datatable-streampage\").addClass(\"font-normal\");\n            } else if (\$(\"#datatable-streampage\").hasClass(\"font-normal\")) {\n                \$(\"#datatable-streampage\").removeClass(\"font-normal\");\n                \$(\"#datatable-streampage\").addClass(\"font-small\");\n            } else {\n                \$(\"#datatable-streampage\").removeClass(\"font-small\");\n                \$(\"#datatable-streampage\").addClass(\"font-large\");\n            }\n            \$(\"#datatable-streampage\").DataTable().draw();\n        }\n        function clearFilters() {\n            window.rClearing = true;\n            \$(\"#series_search\").val(\"\").trigger('change');\n            \$('#series_category_id').val(\"\").trigger('change');\n            \$('#series_show_entries').val(\"";
echo $rAdminSettings["default_entries"] ?: 10;
echo "\").trigger('change');\n            window.rClearing = false;\n            \$('#datatable-streampage').DataTable().search(\$(\"#series_search\").val());\n            \$('#datatable-streampage').DataTable().page.len(\$('#series_show_entries').val());\n            \$(\"#datatable-streampage\").DataTable().page(0).draw('page');\n            \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n            \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n        }\n        \$(document).ready(function() {\n\t\t\t\$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            formCache.init();\n            formCache.fetch();\n            \n            \$('select').select2({width: '100%'});\n            \$(\"#datatable-streampage\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('stream-' + data[0]);\n                },\n                responsive: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"series\";\n                        d.category = getCategory();\n                    }\n                },\n                columnDefs: [\n                    ";
if ($rPermissions["is_reseller"]) {
    echo "                    {\"className\": \"dt-center\", \"targets\": [0,3,4,5,6]},\n                    ";
} else {
    echo "                    {\"className\": \"dt-center\", \"targets\": [0,3,4,5,6,7,8]},\n                    {\"orderable\": false, \"targets\": [1,8]}\n                    ";
}
echo "                ],\n                order: [[ 0, \"desc\" ]],\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\n                stateSave: true\n            });\n            \$(\"#datatable-streampage\").css(\"width\", \"100%\");\n            \$('#series_search').keyup(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-streampage').DataTable().search(\$(this).val()).draw();\n                }\n            })\n            \$('#series_show_entries').change(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-streampage').DataTable().page.len(\$(this).val()).draw();\n                }\n            })\n            \$('#series_category_id').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                }\n            })\n            ";
if (!$detect->isMobile()) {
    echo "            setTimeout(reloadStreams, 5000);\n            ";
}
if (!$rAdminSettings["auto_refresh"]) {
    echo "            toggleAuto();\n            ";
}
echo "            if (\$('#series_search').val().length > 0) {\n                \$('#datatable-streampage').DataTable().search(\$('#series_search').val()).draw();\n            }\n        });\n        \n        \$(window).bind('beforeunload', function() {\n            formCache.save();\n        });\n        </script>\n    </body>\n</html>";

?>