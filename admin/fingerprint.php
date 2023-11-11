<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "fingerprint")) {
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <a href=\"./streams.php\"><li class=\"breadcrumb-item\"><i class=\"mdi mdi-backspace\"></i> ";
echo $_["back_to_streams"];
echo "</li></a>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["fingerprint_stream"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./fingerprint.php\" method=\"POST\" id=\"fingerprint_form\">\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\" id=\"stream-selection-tab\">\n                                                <a href=\"#stream-selection\" id=\"stream-selection-nav\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-play mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["stream"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item disabled\" id=\"stream-activity-tab\">\n                                                <a href=\"#stream-activity\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-group mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["activity"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"stream-selection\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-md-5 col-6\">\n                                                        <input type=\"text\" class=\"form-control\" id=\"stream_search\" value=\"\" placeholder=\"";
echo $_["search_streams"];
echo "...\">\n                                                    </div>\n                                                    <div class=\"col-md-4 col-6\">\n                                                        <select id=\"category_search\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["all_categories"];
echo "</option>\n                                                            ";
foreach ($rCategories as $rCategory) {
    echo "                                                            <option value=\"";
    echo $rCategory["id"];
    echo "\"";
    if (isset($_GET["category"]) && $_GET["category"] == $rCategory["id"]) {
        echo " selected";
    }
    echo ">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <label class=\"col-md-1 col-2 col-form-label text-center\" for=\"show_entries\">";
echo $_["show"];
echo "</label>\n                                                    <div class=\"col-md-2 col-8\">\n                                                        <select id=\"show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                            ";
foreach ([10, 25, 50, 250, 500, 1000] as $rShow) {
    echo "                                                            <option";
    if ($rAdminSettings["default_entries"] == $rShow) {
        echo " selected";
    }
    echo " value=\"";
    echo $rShow;
    echo "\">";
    echo $rShow;
    echo "</option>\n                                                            ";
}
echo "                                                        </select>\n                                                    </div>\n                                                    <table id=\"datatable-md1\" class=\"table table-hover table-borderless mb-0\">\n                                                        <thead class=\"bg-light\">\n                                                            <tr>\n                                                                <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                <th>";
echo $_["stream_name"];
echo "</th>\n                                                                <th>";
echo $_["category"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["clients"];
echo "</th>\n                                                                <th class=\"text-center\"></th>\n                                                            </tr>\n                                                        </thead>\n                                                        <tbody></tbody>\n                                                    </table>\n                                                </div>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"stream-activity\">\n                                                <div class=\"row\">\n                                                    <div class=\"alert alert-warning alert-dismissible fade show col-md-12 col-12 text-center\" role=\"alert\">\n                                                        ";
echo $_["warning_fingerprint"];
echo "                                                    </div>\n                                                </div>\n                                                <div class=\"row\" id=\"filter_selection\">\n                                                    <label class=\"col-md-1 col-2 col-form-label text-center\" for=\"fingerprint_type\">";
echo $_["type"];
echo "</label>\n                                                    <div class=\"col-md-2 col-6\">\n                                                        <select id=\"fingerprint_type\" class=\"form-control text-center\" data-toggle=\"select2\">\n                                                            <option value=\"1\">";
echo $_["activity_id"];
echo "</option>\n                                                            <option value=\"2\">";
echo $_["username"];
echo "</option>\n                                                            <option value=\"3\">";
echo $_["message"];
echo "</option>\n                                                        </select>\n                                                    </div>\n                                                    <label class=\"col-md-1 col-2 col-form-label text-center\" for=\"font_size\">";
echo $_["size"];
echo "</label>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <input type=\"text\" class=\"form-control text-center\" id=\"font_size\" value=\"36\" placeholder=\"\">\n                                                    </div>\n                                                    <label class=\"col-md-1 col-2 col-form-label text-center\" for=\"font_color\">";
echo $_["colour"];
echo "</label>\n                                                    <div class=\"col-md-2 col-2\">\n                                                        <input type=\"text\" id=\"font_color\" class=\"form-control text-center\" value=\"#ffffff\">\n                                                    </div>\n                                                    <label class=\"col-md-1 col-2 col-form-label text-center\" for=\"position\">";
echo $_["position"];
echo "</label>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <input type=\"text\" class=\"form-control text-center\" id=\"position_x\" value=\"10\" placeholder=\"X\">\n                                                    </div>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <input type=\"text\" class=\"form-control text-center\" id=\"position_y\" value=\"10\" placeholder=\"Y\">\n                                                    </div>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light\" onClick=\"activateFingerprint()\">\n                                                            <i class=\"mdi mdi-fingerprint\"></i>\n                                                        </button>\n                                                    </div>\n                                                    <div class=\"col-md-12 col-2\" style=\"margin-top:10px;display:none;\" id=\"custom_message_div\">\n                                                        <input type=\"text\" class=\"form-control\" id=\"custom_message\" value=\"\" placeholder=\"";
echo $_["custom_message"];
echo "\">\n                                                    </div>\n                                                </div>\n                                                <div class=\"row\">\n                                                    <table id=\"datatable-md2\" class=\"table table-borderless mb-0\">\n                                                        <thead class=\"bg-light\">\n                                                            <tr>\n                                                                <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["status"];
echo "</th>\n                                                                <th>";
echo $_["username"];
echo "</th>\n                                                                <th>";
echo $_["stream"];
echo "</th>\n                                                                <th>";
echo $_["server"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["time"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["ip"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["country"];
echo "</th>\n                                                                <th class=\"text-center\">";
echo $_["actions"];
echo "</th>\n                                                            </tr>\n                                                        </thead>\n                                                        <tbody></tbody>\n                                                    </table>\n                                                </div>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "\n        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-ui/jquery-ui.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var rStreamID = -1;\n        \n        function getCategory() {\n            return \$(\"#category_search\").val();\n        }\n        function getStreamID() {\n            return window.rStreamID;\n        }\n        function selectFingerprint(rID) {\n            \$(\"#stream-activity-tab\").attr(\"disabled\", false);\n            \$('[href=\"#stream-activity\"]').tab('show');\n            window.rStreamID = rID;\n        }\n        function activateFingerprint() {\n            rArray = {\"id\": window.rStreamID, \"font_size\": \$(\"#font_size\").val(), \"font_color\": \$(\"#font_color\").val(), \"message\": \"\", \"type\": \$(\"#fingerprint_type\").val(), \"xy_offset\": \"\"};\n            if (rArray.type == 3) {\n                rArray[\"message\"] = \$(\"#custom_message\").val();\n            }\n            if ((\$(\"#position_x\").val() >= 0) && (\$(\"#position_y\").val() >= 0)) {\n                rArray[\"xy_offset\"] = \$(\"#position_x\").val() + \"x\" + \$(\"#position_y\").val();\n            }\n            if ((rArray[\"font_size\"] > 0) && (rArray[\"font_color\"].length > 0) && ((rArray[\"message\"].length > 0) || (rArray[\"type\"] != 3))  && (rArray[\"font_size\"] > 0) && (rArray[\"xy_offset\"].length > 0)) {\n                \$.getJSON(\"./api.php?action=fingerprint&data=\" + encodeURIComponent(JSON.stringify(rArray)), function(data) {\n                    if (data.result == true) {\n                        \$.toast(\"";
echo $_["fingerprint_success"];
echo "\");\n                    } else {\n                        \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                    }\n                });\n                \$(\"#datatable-md2\").DataTable().ajax.reload( null, false );\n                \$(\"#filter_selection\").fadeOut(500, function() {\n                    \$('#datatable-md2').parents('div.dataTables_wrapper').first().fadeIn(500);\n                });\n            } else {\n                \$.toast(\"";
echo $_["fingerprint_fail"];
echo "\");\n            }\n        }\n        function api(rID, rType, rAID) {\n            \$.getJSON(\"./api.php?action=user_activity&sub=\" + rType + \"&pid=\" + rID, function(data) {\n                if (data.result === true) {\n                    if (rType == \"kill\") {\n                        \$.toast(\"";
echo $_["connection_has_been_killed"];
echo "\");\n                        \$(\"#row-\" + rAID).remove();\n                    }\n                } else {\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                }\n            });\n        }\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'})\n            \$(\"#font_color\").colorpicker({format:\"auto\"});\n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"#probesize_ondemand\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#delay_minutes\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#tv_archive_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n            \$(\"#datatable-md1\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"stream_unique\",\n                        d.category = getCategory()\n                    }\n                },\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,3,4]},\n                    {\"orderable\": false, \"targets\": [4]}\n                ],\n                order: [[ 3, \"desc\" ]],\n            });\n            \$('#stream_search').keyup(function(){\n                \$(\"#datatable-md1\").DataTable().search(\$(this).val()).draw();\n            });\n            \$('#show_entries').change(function(){\n                \$(\"#datatable-md1\").DataTable().page.len(\$(this).val()).draw();\n            });\n            \$('#category_search').change(function(){\n                \$(\"#datatable-md1\").DataTable().ajax.reload(null, false);\n            });\n            \$(\"#datatable-md2\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                rowCallback: function (row, data) {\n                    \$(row).attr(\"id\", \"row-\" + data[0]);\n                },\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"live_connections\",\n                        d.stream_id = getStreamID(),\n                        d.fingerprint = true;\n                    }\n                },\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,1,5,6,7,8]},\n                    {\"visible\": false, \"targets\": [1,3]}\n                ],\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo ",\n                lengthMenu: [10, 25, 50, 250, 500, 1000],\n                order: [[ 0, \"desc\" ]]\n            });\n            \$(\"#fingerprint_type\").change(function() {\n                if (\$(this).val() == 3) {\n                    \$(\"#custom_message_div\").show();\n                } else {\n                    \$(\"#custom_message_div\").hide();\n                }\n            });\n            \$(\"#font_size\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#position_x\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#position_y\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$('#datatable-md2').parents('div.dataTables_wrapper').first().hide();\n            \$(\".nav li.disabled a\").click(function() {\n                return false;\n            });\n            \$(\"#stream-selection-nav\").click(function() {\n                \$(\"#stream-activity-tab\").attr(\"disabled\", true);\n                window.rStreamID = -1;\n                \$(\"#filter_selection\").show();\n                \$('#datatable-md2').parents('div.dataTables_wrapper').first().hide();\n                \$(\"#datatable-md1\").DataTable().ajax.reload( null, false );\n            });\n        });\n        </script>\n    </body>\n</html>";

?>