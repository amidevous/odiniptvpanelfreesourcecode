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
if ($rPermissions["is_admin"] && !hasPermissions("adv", "episodes")) {
    exit;
}
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
if (hasPermissions("adv", "add_episode")) {
    echo "                                        <a href=\"#\" onClick=\"showModal()\">\n                                            <button type=\"button\" class=\"btn btn-success waves-effect waves-light btn-sm\">\n                                                <i class=\"mdi mdi-plus\"></i> ";
    echo $_["add_episode"];
    echo "                                            </button>\n                                        </a>\n\t\t\t\t\t\t\t\t\t\t";
}
echo "                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["episodes"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n                                <form id=\"episodes_form\">\n                                    <div class=\"form-group row mb-4\">\n                                        ";
if ($rPermissions["is_reseller"]) {
    echo "                                        <div class=\"col-md-3\">\n                                            <input type=\"text\" class=\"form-control\" id=\"episodes_search\" value=\"\" placeholder=\"";
    echo $_["search_episodes"];
    echo "...\">\n                                        </div>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"episodes_server\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
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
    echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"episodes_series\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
    echo $_["all_series"];
    echo "</option>\n                                                ";
    foreach (getSeriesList() as $rSeriesArr) {
        echo "                                                <option value=\"";
        echo $rSeriesArr["id"];
        echo "\"";
        if (isset($_GET["series"]) && $_GET["series"] == $rSeriesArr["id"]) {
            echo " selected";
        }
        echo ">";
        echo $rSeriesArr["title"];
        echo "</option>\n                                                ";
    }
    echo "                                            </select>\n                                        </div>\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"episodes_show_entries\">";
    echo $_["show"];
    echo "</label>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"episodes_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                ";
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
    echo "                                        <div class=\"col-md-2\">\n                                            <input type=\"text\" class=\"form-control\" id=\"episodes_search\" value=\"\" placeholder=\"";
    echo $_["search_episodes"];
    echo "...\">\n                                        </div>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"episodes_server\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
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
    echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-3\">\n                                            <select id=\"episodes_series\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
    echo $_["all_series"];
    echo "</option>\n                                                ";
    foreach (getSeriesList() as $rSeriesArr) {
        echo "                                                <option value=\"";
        echo $rSeriesArr["id"];
        echo "\"";
        if (isset($_GET["series"]) && $_GET["series"] == $rSeriesArr["id"]) {
            echo " selected";
        }
        echo ">";
        echo $rSeriesArr["title"];
        echo "</option>\n                                                ";
    }
    echo "                                            </select>\n                                        </div>\n                                        <div class=\"col-md-2\">\n                                            <select id=\"episodes_filter\" class=\"form-control\" data-toggle=\"select2\">\n                                                <option value=\"\" selected>";
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
    echo "</option>\n                                            </select>\n                                        </div>\n                                        <label class=\"col-md-1 col-form-label text-center\" for=\"episodes_show_entries\">";
    echo $_["show"];
    echo "</label>\n                                        <div class=\"col-md-1\">\n                                            <select id=\"episodes_show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                ";
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
echo "</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody></tbody>\n                                </table>\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <div class=\"modal fade addModal\" role=\"dialog\" aria-labelledby=\"addLabel\" aria-hidden=\"true\" style=\"display: none;\" data-username=\"\" data-password=\"\">\n            <div class=\"modal-dialog modal-dialog-centered\">\n                <div class=\"modal-content\">\n                    <div class=\"modal-header\">\n                        <h4 class=\"modal-title\" id=\"addModal\">";
echo $_["select_series"];
echo ":</h4>\n                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n                    </div>\n                    <div class=\"modal-body\">\n                        <div class=\"col-12\">\n                            <select id=\"add_series_id\" class=\"form-control\" data-toggle=\"select2\">\n                                ";
foreach (getSeriesList() as $rSeries) {
    echo "                                <option value=\"";
    echo $rSeries["id"];
    echo "\">";
    echo $rSeries["title"];
    echo "</option>\n                                ";
}
echo "                            </select>\n                        </div>\n                        <div class=\"col-12 add-margin-top-20\">\n                            <div class=\"input-group\">\n                                <div class=\"input-group-append\" style=\"width:100%\">\n                                    <button style=\"width:50%\" class=\"btn btn-success waves-effect waves-light\" type=\"button\" onClick=\"addEpisode();\"><i class=\"mdi mdi-plus-circle-outline\"></i> ";
echo $_["add_episode"];
echo "</button>\n                                    <button style=\"width:50%\" class=\"btn btn-info waves-effect waves-light\" type=\"button\" onClick=\"addEpisodes();\"><i class=\"mdi mdi-plus-circle-multiple-outline\"></i> ";
echo $_["multiple_episodes"];
echo "</button>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                </div><!-- /.modal-content -->\n            </div><!-- /.modal-dialog -->\n        </div><!-- /.modal -->\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/js/pages/form-remember.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var autoRefresh = true;\n        var rClearing = false;\n        \n        function toggleAuto() {\n            if (autoRefresh == true) {\n                autoRefresh = false;\n                \$(\".auto-text\").html(\"Manual Mode\");\n            } else {\n                autoRefresh = true;\n                \$(\".auto-text\").html(\"Auto-Refresh\");\n            }\n        }\n        \n        function api(rID, rServerID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["episode_delete_confirm"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=episode&sub=\" + rType + \"&stream_id=\" + rID + \"&server_id=\" + rServerID, function(data) {\n                if (data.result == true) {\n                    if (rType == \"start\") {\n                        \$.toast(\"";
echo $_["episode_encoding_start"];
echo "\");\n                    } else if (rType == \"stop\") {\n                        \$.toast(\"";
echo $_["episode_encoding_stop"];
echo "\");\n                    } else if (rType == \"delete\") {\n                        \$.toast(\"";
echo $_["episode_deleted"];
echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                } else {\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                }\n            }).fail(function() {\n                \$.toast(\"";
echo $_["error_occured"];
echo "\");\n            });\n        }\n        function player(rID, rContainer) {\n            \$.magnificPopup.open({\n                items: {\n                    src: \"./player.php?type=series&id=\" + rID + \"&container=\" + rContainer,\n                    type: 'iframe'\n                }\n            });\n        }\n        function reloadStreams() {\n            if (autoRefresh == true) {\n                \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n            }\n            setTimeout(reloadStreams, 5000);\n        }\n\n        function getSeries() {\n            return \$(\"#episodes_series\").val();\n        }\n        function getFilter() {\n            return \$(\"#episodes_filter\").val();\n        }\n        function getServer() {\n            return \$(\"#episodes_server\").val();\n        }\n        function changeZoom() {\n            if (\$(\"#datatable-streampage\").hasClass(\"font-large\")) {\n                \$(\"#datatable-streampage\").removeClass(\"font-large\");\n                \$(\"#datatable-streampage\").addClass(\"font-normal\");\n            } else if (\$(\"#datatable-streampage\").hasClass(\"font-normal\")) {\n                \$(\"#datatable-streampage\").removeClass(\"font-normal\");\n                \$(\"#datatable-streampage\").addClass(\"font-small\");\n            } else {\n                \$(\"#datatable-streampage\").removeClass(\"font-small\");\n                \$(\"#datatable-streampage\").addClass(\"font-large\");\n            }\n            \$(\"#datatable-streampage\").DataTable().draw();\n        }\n        function clearFilters() {\n            window.rClearing = true;\n            \$(\"#episodes_search\").val(\"\").trigger('change');\n            \$('#episodes_filter').val(\"\").trigger('change');\n            \$('#episodes_server').val(\"\").trigger('change');\n            \$('#episodes_series').val(\"\").trigger('change');\n            \$('#episodes_show_entries').val(\"";
echo $rAdminSettings["default_entries"] ?: 10;
echo "\").trigger('change');\n            window.rClearing = false;\n            \$('#datatable-streampage').DataTable().search(\$(\"#episodes_search\").val());\n            \$('#datatable-streampage').DataTable().page.len(\$('#episodes_show_entries').val());\n            \$(\"#datatable-streampage\").DataTable().page(0).draw('page');\n            \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n            \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n        }\n        function showModal() {\n            \$('.addModal').modal('show');\n        }\n        function addEpisode() {\n            window.location.href = \"./episode.php?sid=\" + \$(\"#add_series_id\").val();\n        }\n        function addEpisodes() {\n            window.location.href = \"./episode.php?sid=\" + \$(\"#add_series_id\").val() + \"&multi\";\n        }\n        \$(document).ready(function() {\n\t\t\t\$(window).keypress(function(event){\n\t\t\t\tif(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n\t\t\t});\n            formCache.init();\n            ";
if (!isset($_GET["series"])) {
    echo "            formCache.fetch();\n            ";
}
echo "            \n            \$('select').select2({width: '100%'});\n            \$(\"#datatable-streampage\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                    \$('[data-toggle=\"tooltip\"]').tooltip();\n                },\n                createdRow: function(row, data, index) {\n                    \$(row).addClass('stream-' + data[0]);\n                },\n                responsive: false,\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"episodes\";\n                        d.series = getSeries();\n                        d.server = getServer();\n                        ";
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
echo ",\n                stateSave: true\n            });\n            \$(\"#datatable-streampage\").css(\"width\", \"100%\");\n            \$('#episodes_search').keyup(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-streampage').DataTable().search(\$(this).val()).draw();\n                }\n            })\n            \$('#episodes_show_entries').change(function(){\n                if (!window.rClearing) {\n                    \$('#datatable-streampage').DataTable().page.len(\$(this).val()).draw();\n                }\n            })\n            \$('#episodes_series').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                }\n            })\n            \$('#episodes_server').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                }\n            })\n            \$('#episodes_filter').change(function(){\n                if (!window.rClearing) {\n                    \$('[data-toggle=\"tooltip\"]').tooltip(\"hide\");\n                    \$(\"#datatable-streampage\").DataTable().ajax.reload( null, false );\n                }\n            })\n            ";
if (!$detect->isMobile()) {
    echo "            setTimeout(reloadStreams, 5000);\n            ";
}
if (!$rAdminSettings["auto_refresh"]) {
    echo "            toggleAuto();\n            ";
}
echo "            if (\$('#episodes_search').val().length > 0) {\n                \$('#datatable-streampage').DataTable().search(\$('#episodes_search').val()).draw();\n            }\n        });\n        \n        \$(window).bind('beforeunload', function() {\n            formCache.save();\n        });\n        </script>\n    </body>\n</html>";

?>