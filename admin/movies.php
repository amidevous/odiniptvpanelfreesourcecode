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
if ($rPermissions["is_admin"] && !hasPermissions("adv", "movies")) {
    exit;
}
$rCategories = getCategories("movie");
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
if ($rPermissions["is_admin"] && hasPermissions("adv", "add_movie")) {
    echo "                                        <a href=\"movie.php\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_movie"];
    echo "                                            </button>\n                                        </a>\n                                        ";
}
echo "                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["movies"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <form id=\"movies_form\">\n                                    <div class=\"form-group row mb-4\">\n                                        ";
if ($rPermissions["is_reseller"]) {
    echo "                                        <div class=\"col-md-3\">\n                                            <input type=\"text\" class=\"form-control\" id=\"movies_search\" value=\"\" placeholder=\"";
    echo $_["search_movies"];
    echo "...\">\n                                        </div>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"movies_category_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
    echo $_["all_categories"];
    echo "</option>\n                                                ";
    foreach ($rCategories as $rCategory) {
        echo "                                                <option value=\"";
        echo $rCategory["id"];
        echo "\">";
        echo $rCategory["category_name"];
        echo "</option>\n                                                ";
    }
    echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"movies_server\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
    echo $_["all_servers"];
    echo "</option>\n                                                ";
    foreach (getStreamingServers() as $rServer) {
        echo "                                                <option value=\"";
        echo $rServer["id"];
        echo "\"";
        if (isset($_GET["server"]) && $_GET["server"] == $rServer["id"]) {
            echo " selected";
        }
        echo ">";
        echo $rServer["server_name"];
        echo "</option>\n                                                ";
    }
    echo "                                            </select>\n                                        </div>\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"movies_show_entries\">";
    echo $_["show"];
    echo "</label>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"movies_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                ";
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
    echo "                                            </select>\n                                        </div>\n                                        ";
} else {
    echo "                                        <div class=\"col-md-2\">\n                                            <input type=\"text\" class=\"form-control\" id=\"movies_search\" value=\"\" placeholder=\"";
    echo $_["search_movies"];
    echo "...\">\n                                        </div>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"movies_server\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
    echo $_["all_servers"];
    echo "</option>\n                                                ";
    foreach (getStreamingServers() as $rServer) {
        echo "                                                <option value=\"";
        echo $rServer["id"];
        echo "\"";
        if (isset($_GET["server"]) && $_GET["server"] == $rServer["id"]) {
            echo " selected";
        }
        echo ">";
        echo $rServer["server_name"];
        echo "</option>\n                                                ";
    }
    echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"movies_category_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
    echo $_["all_categories"];
    echo "</option>\n                                                ";
    foreach ($rCategories as $rCategory) {
        echo "                                                <option value=\"";
        echo $rCategory["id"];
        echo "\">";
        echo $rCategory["category_name"];
        echo "</option>\n                                                ";
    }
    echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"movies_filter\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
    echo $_["no_filter"];
    echo "</option>\n                                                <option value=\"1\">";
    echo $_["encoded"];
    echo "</option>\n                                                <option value=\"2\">";
    echo $_["encoding"];
    echo "</option>\n                                                <option value=\"3\">";
    echo $_["down"];
    echo "</option>\n                                                <option value=\"4\">";
    echo $_["ready"];
    echo "</option>\n                                                <option value=\"5\">";
    echo $_["direct"];
    echo "</option>\n                                                <option value=\"6\">";
    echo $_["no_tmdb_match"];
    echo "</option>\n                                            </select>\n                                        </div>\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"movies_show_entries\">";
    echo $_["show"];
    echo "</label>\n                                        <div class=\"col-md-1\">\n                                            <select id=\"movies_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                ";
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
    echo "                                            </select>\n                                        </div>\n                                        ";
}
echo "                                    </div>\n                                </form>\n                                <table id=\"datatable-streampage\" class=\"table table-hover dt-responsive nowrap font-normal\">\n                                    <thead>\n                                        <tr>\n                                            <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">Cover</th>\n                                            <th>";
echo $_["name"];
echo "</th>\n                                            <th>";
echo $_["server"];
echo "</th>\n                                            ";
if ($rPermissions["is_admin"]) {
    echo "                                            <th class=\"text-center\">";
    echo $_["clients"];
    echo "</th>\n                                            <th class=\"text-center\">";
    echo $_["status"];
    echo "</th>\n                                            <th class=\"text-center\">";
    echo $_["actions"];
    echo "</th>\n                                            <th class=\"text-center\">";
    echo $_["player"];
    echo "</th>\n                                            ";
}
echo "\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">Added</th>\n\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">Duration</th>\n                                            <th class=\"text-center\">";
echo $_["stream_info"];
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody></tbody>\n                                </table>\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/js/pages/form-remember.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var autoRefresh = true;\n        var rClearing = false;\n        \n        function toggleAuto() {\n            if (autoRefresh == true) {\n                autoRefresh = false;\n                \$(\".auto-text\").html(\"";
echo $_["manual_mode"];
echo "\");\n            } else {\n                autoRefresh = true;\n                \$(\".auto-text\").html(\"";
echo $_["auto_refresh"];
echo "\");\n            }\n        }\n        \n        function api(rID, rServerID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["movie_delete_confirm"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=movie&sub=\" + rType + \"&stream_id=\" + rID + \"&server_id=\" + rServerID, function(data) {\n                if (data.result == true) {\n                    if (rType == \"start\") {\n                        \$.toast(\"";
echo $_["movie_encode_started"];
echo "\");\n                    } else if (rType == \"stop\") {\n                        \$.toast(\"";
echo $_["movie_encode_stopped"];
echo "\");\n                    } else if (rType == \"delete\") {\n                        \$.toast(\"";
echo $_["movie_delete_confirmed"];
echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                } else {\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                }\n            }).fail(function() {\n                \$.toast(\"";
echo $_["error_occured"];
echo "\");\n            });\n        }\n        function player(rID, rContainer) {\n            \$.magnificPopup.open({\n                items: {\n                    src: \"./player.php?type=movie&id=\" + rID + \"&container=\" + rContainer,\n                    type: 'iframe'\n                }\n            });\n        }\n        function reloadStreams() {\n            if (autoRefresh == true) {\n                \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n            }\n            setTimeout(reloadStreams, 5000);\n        }\n\n        function getCategory() {\n            return \$(\"#movies_category_id\").val();\n        }\n        function getFilter() {\n            return \$(\"#movies_filter\").val();\n        }\n        function getServer() {\n            return \$(\"#movies_server\").val();\n        }\n        function changeZoom() {\n            if (\$(\"#datatable-streampage\").hasClass(\"font-large\")) {\n                \$(\"#datatable-streampage\").removeClass(\"font-large\");\n                \$(\"#datatable-streampage\").addClass(\"font-normal\");\n            } else if (\$(\"#datatable-streampage\").hasClass(\"font-normal\")) {\n                \$(\"#datatable-streampage\").removeClass(\"font-normal\");\n                \$(\"#datatable-streampage\").addClass(\"font-small\");\n            } else {\n                \$(\"#datatable-streampage\").removeClass(\"font-small\");\n                \$(\"#datatable-streampage\").addClass(\"font-large\");\n            }\n            \$(\"#datatable-streampage\").DataTable().draw();\n        }\n        function clearFilters() {\n            window.rClearing = true;\n            \$(\"#movies_search\").val(\"\").trigger('change');\n            \$('#movies_filter').val(\"\").trigger('change');\n            \$('#movies_server').val(\"\").trigger('change');\n            \$('#movies_category_id').val(\"\").trigger('change');\n            \$('#movies_show_entries').val(\"";
echo $rAdminSettings["default_entries"] ?: 10;
echo "\").trigger('change');\n            window.rClearing = false;\n            \$('#datatable-streampage').DataTable().search(\$(\"#movies_search\").val());\n            \$('#datatable-streampage').DataTable().page.len(\$('#movies_show_entries').val());\n            \$(\"#datatable-streampage\").DataTable().page(0).draw('page');\n            \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n            \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n        }\n        \$(document).ready(function() {\n\t\t\t\$(window).keypress(function(event){\n\t\t\t\tif(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n\t\t\t});\n            formCache.init();\n            formCache.fetch();\n            \n            \$('select').select2({width: '100%'});\n            \$(\"#datatable-streampage\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('stream-' + data[0]);\n                },\n                responsive: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"movies\";\n                        d.category = getCategory();\n                        d.server = getServer();\n                        ";
if ($rPermissions["is_admin"]) {
    echo "                        d.filter = getFilter();\n                        ";
} else {
    echo "                        d.filter = 1;\n                        ";
}
echo "                    }\n                },\n                columnDefs: [\n                    ";
if ($rPermissions["is_admin"]) {
    echo "                    {\"className\": \"dt-center\", \"targets\": [0,3,4,5,6,7,8,9,10]},\n                    {\"orderable\": false, \"targets\": [1,6,7,8,9]}\n                    ";
} else {
    echo "                    {\"className\": \"dt-center\", \"targets\": [0,3]}\n                    ";
}
echo "                ],\n                order: [[ 0, \"desc\" ]],\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\n                stateSave: true\n            });\n            \$(\"#datatable-streampage\").css(\"width\", \"100%\");\n            \$('#movies_search').keyup(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-streampage').DataTable().search(\$(this).val()).draw();\n                }\n            })\n            \$('#movies_show_entries').change(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-streampage').DataTable().page.len(\$(this).val()).draw();\n                }\n            })\n            \$('#movies_category_id').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                }\n            })\n            \$('#movies_server').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                }\n            })\n            \$('#movies_filter').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                }\n            })\n            ";
if (!$detect->isMobile()) {
    echo "            setTimeout(reloadStreams, 5000);\n            ";
}
if (!$rAdminSettings["auto_refresh"]) {
    echo "            toggleAuto();\n            ";
}
echo "            if (\$('#movies_search').val().length > 0) {\n                \$('#datatable-streampage').DataTable().search(\$('#movies_search').val()).draw();\n            }\n        });\n        \n        \$(window).bind('beforeunload', function() {\n            formCache.save();\n        });\n        </script>\n    </body>\n</html>";

?>