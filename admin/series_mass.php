<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "mass_sedits")) {
    exit;
}
$rCategories = getCategories("series");
if (isset($_POST["submit_series"])) {
    $rArray = [];
    if (isset($_POST["c_category_id"])) {
        $rArray["category_id"] = intval($_POST["category_id"]);
    }
    $rSeriesIDs = json_decode($_POST["series"], true);
    if (0 < count($rSeriesIDs)) {
        foreach ($rSeriesIDs as $rSeriesID) {
            $rQueries = [];
            foreach ($rArray as $rKey => $rValue) {
                $rQueries[] = "`" . ESC($rKey) . "` = '" . ESC($rValue) . "'";
            }
            if (0 < count($rQueries)) {
                $rQueryString = join(",", $rQueries);
                $rQuery = "UPDATE `series` SET " . $rQueryString . " WHERE `id` = " . intval($rSeriesID) . ";";
                if (!$db->query($rQuery)) {
                    $_STATUS = 1;
                }
            }
            if (isset($_POST["c_bouquets"])) {
                $rBouquets = $_POST["bouquets"];
                foreach ($rBouquets as $rBouquet) {
                    addToBouquet("series", $rBouquet, $rSeriesID);
                }
                foreach (getBouquets() as $rBouquet) {
                    if (!in_array($rBouquet["id"], $rBouquets)) {
                        removeFromBouquet("series", $rBouquet["id"], $rSeriesID);
                    }
                }
            }
        }
        if (isset($_POST["reprocess_tmdb"])) {
            foreach ($rSeriesIDs as $rSeriesID) {
                if (0 < intval($rSeriesID)) {
                    $db->query("INSERT INTO `tmdb_async`(`type`, `stream_id`, `status`) VALUES(2, " . intval($rSeriesID) . ", 0);");
                }
            }
        }
        if (isset($_POST["c_bouquets"])) {
            scanBouquets();
        }
    }
    $_STATUS = 0;
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./series.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_series"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
echo $_["mass_edit_series"];
echo " <small id=\"selected_count\"></small></h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["mass_edit_of_series_was_successfully"];
        echo "                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["mass_edit_of_series_was_successfully"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["generic_fail"];
            echo "                        </div>\n                        ";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["generic_fail"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./series_mass.php\" method=\"POST\" id=\"stream_form\">\n                                    <input type=\"hidden\" name=\"series\" id=\"series\" value=\"\" />\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#stream-selection\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-youtube-tv mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["series"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#stream-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"stream-selection\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-md-5 col-6\">\n                                                        <input type=\"text\" class=\"form-control\" id=\"stream_search\" value=\"\" placeholder=\"";
echo $_["search_series"];
echo "\">\n                                                    </div>\n                                                    <div class=\"col-md-4 col-6\">\n                                                        <select id=\"category_search\" class=\"form-control\" data-toggle=\"select2\">\n                                                            <option value=\"\" selected>";
echo $_["all_categories"];
echo "</option>\n                                                            <option value=\"-1\">";
echo $_["no_tmdb_match"];
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
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-2 col-8\">\n                                                        <select id=\"show_entries\" class=\"form-control\" data-toggle=\"select2\">\n                                                            ";
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
echo "                                                        </select>\n                                                    </div>\n                                                    <div class=\"col-md-1 col-2\">\n                                                        <button type=\"button\" class=\"btn btn-info waves-effect waves-light\" onClick=\"toggleStreams()\">\n                                                            <i class=\"mdi mdi-selection\"></i>\n                                                        </button>\n                                                    </div>\n                                                    <table id=\"datatable-mass\" class=\"table table-hover table-borderless mb-0\">\n                                                        <thead class=\"bg-light\">\n                                                            <tr>\n                                                                <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                                <th>";
echo $_["series_name"];
echo "</th>\n                                                                <th>";
echo $_["category"];
echo "</th>\n                                                            </tr>\n                                                        </thead>\n                                                        <tbody></tbody>\n                                                    </table>\n                                                </div>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"stream-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <p class=\"sub-header\">\n                                                            ";
echo $_["mass_edit_info"];
echo "                                                        </p>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"category_id\" name=\"c_category_id\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"category_id\">";
echo $_["category_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select disabled name=\"category_id\" id=\"category_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach ($rCategories as $rCategory) {
    echo "                                                                    <option value=\"";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <div class=\"checkbox checkbox-single col-md-1 checkbox-offset checkbox-primary\">\n                                                                <input type=\"checkbox\" class=\"activate\" data-name=\"bouquets\" name=\"c_bouquets\">\n                                                                <label></label>\n                                                            </div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"bouquets\">";
echo $_["select_bouquets"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select disabled name=\"bouquets[]\" id=\"bouquets\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "\">\n                                                                    ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                                    <option value=\"";
    echo $rBouquet["id"];
    echo "\">";
    echo $rBouquet["bouquet_name"];
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <div class=\"col-md-1\"></div>\n                                                            <label class=\"col-md-3 col-form-label\" for=\"reprocess_tmdb\">";
echo $_["reprocess_tmdb_data"];
echo "</label>\n                                                            <div class=\"col-md-2\">\n                                                                <input name=\"reprocess_tmdb\" id=\"reprocess_tmdb\" type=\"checkbox\" data-plugin=\"switchery\" class=\"js-switch\" data-color=\"#039cfd\" />\n                                                            </div>\n                                                        </div>\n                                                        \n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                   \n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <input name=\"submit_series\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["edit_series"];
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                           \n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "\n        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        ";
if ($rSettings["premiumui"]) {
    include "footer.php";
} else {
    echo "        <script src=\"assets/js/vendor.min.js\"></script>\n\t\t";
}
echo "        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-ui/jquery-ui.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var rSwitches = [];\n        var rSelected = [];\n        \n        function getCategory() {\n            return \$(\"#category_search\").val();\n        }\n        function getFilter() {\n            return \$(\"#filter\").val();\n        }\n        function toggleStreams() {\n            \$(\"#datatable-mass tr\").each(function() {\n                if (\$(this).hasClass('selected')) {\n                    \$(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rSelected.splice(\$.inArray(\$(this).find(\"td:eq(0)\").html(), window.rSelected), 1);\n                    }\n                } else {            \n                    \$(this).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    if (\$(this).find(\"td:eq(0)\").html()) {\n                        window.rSelected.push(\$(this).find(\"td:eq(0)\").html());\n                    }\n                }\n            });\n            \$(\"#selected_count\").html(\" - \" + window.rSelected.length + \" selected\")\n        }\n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'})\n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n                var switchery = new Switchery(html);\n                window.rSwitches[\$(html).attr(\"id\")] = switchery;\n            });\n            \$('#server_tree').jstree({ 'core' : {\n                'check_callback': function (op, node, parent, position, more) {\n                    switch (op) {\n                        case 'move_node':\n                            if (node.id == \"source\") { return false; }\n                            return true;\n                    }\n                },\n                'data' : ";
echo json_encode($rServerTree);
echo "            }, \"plugins\" : [ \"dnd\" ]\n            });\n            \$(\"#stream_form\").submit(function(e){\n                \$(\"#series\").val(JSON.stringify(window.rSelected));\n                if (window.rSelected.length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["select_at_least_one_stream_to_edit"];
echo "\");\n                }\n            });\n            \$(\"input[type=checkbox].activate\").change(function() {\n                if (\$(this).is(\":checked\")) {\n                    if (\$(this).data(\"type\") == \"switch\") {\n                        window.rSwitches[\$(this).data(\"name\")].enable();\n                    } else {\n                        \$(\"#\" + \$(this).data(\"name\")).prop(\"disabled\", false);\n                        if (\$(this).data(\"name\") == \"days_to_restart\") {\n                            \$(\"#time_to_restart\").prop(\"disabled\", false);\n                        }\n                    }\n                } else {\n                    if (\$(this).data(\"type\") == \"switch\") {\n                        window.rSwitches[\$(this).data(\"name\")].disable();\n                    } else {\n                        \$(\"#\" + \$(this).data(\"name\")).prop(\"disabled\", true);\n                        if (\$(this).data(\"name\") == \"days_to_restart\") {\n                            \$(\"#time_to_restart\").prop(\"disabled\", true);\n                        }\n                    }\n                }\n            });\n            \$(\".clockpicker\").clockpicker();\n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \$(\"#probesize_ondemand\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#delay_minutes\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"#tv_archive_duration\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n            rTable = \$(\"#datatable-mass\").DataTable({\n                language: {\n                    paginate: {\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\n                        next: \"<i class='mdi mdi-chevron-right'>\"\n                    }\n                },\n                drawCallback: function() {\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination\");\n                },\n                processing: true,\n                serverSide: true,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"series_list\",\n                        d.category = getCategory()\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0]}\n                ],\n                \"rowCallback\": function(row, data) {\n                    if (\$.inArray(data[0], window.rSelected) !== -1) {\n                        \$(row).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                    }\n                },\n                pageLength: ";
echo $rAdminSettings["default_entries"] ?: 10;
echo "            });\n            \$('#stream_search').keyup(function(){\n                rTable.search(\$(this).val()).draw();\n            })\n            \$('#show_entries').change(function(){\n                rTable.page.len(\$(this).val()).draw();\n            })\n            \$('#category_search').change(function(){\n                rTable.ajax.reload(null, false);\n            })\n            \$('#filter').change(function(){\n                rTable.ajax.reload( null, false );\n            })\n            \$(\"#datatable-mass\").selectable({\n                filter: 'tr',\n                selected: function (event, ui) {\n                    if (\$(ui.selected).hasClass('selectedfilter')) {\n                        \$(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass(\"selected\");\n                        window.rSelected.splice(\$.inArray(\$(ui.selected).find(\"td:eq(0)\").html(), window.rSelected), 1);\n                    } else {            \n                        \$(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass(\"selected\");\n                        window.rSelected.push(\$(ui.selected).find(\"td:eq(0)\").html());\n                    }\n                    \$(\"#selected_count\").html(\" - \" + window.rSelected.length + \" selected\")\n                }\n            });\n        });\n        </script>\n    </body>\n</html>";

?>