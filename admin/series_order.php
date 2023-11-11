<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "edit_series")) {
    exit;
}
if (isset($_POST["reorder"])) {
    $rOrder = json_decode($_POST["episode_order_array"], true);
    if (is_array($rOrder)) {
        foreach ($rOrder as $rSeason => $rEpisodes) {
            $rSort = 0;
            foreach ($rEpisodes as $rStreamID) {
                $rSort++;
                $db->query("UPDATE `series_episodes` SET `sort` = " . intval($rSort) . " WHERE `id` = " . intval($rStreamID) . ";");
            }
        }
    }
}
if (!isset($_GET["id"])) {
    exit;
}
$rSeries = getSerie($_GET["id"]);
if (!$rSeries) {
    exit;
}
$rSeasons = [];
$result = $db->query("SELECT `series_episodes`.`id`, `series_episodes`.`season_num`, `streams`.`stream_display_name` FROM `series_episodes` LEFT JOIN `streams` ON `streams`.`id` = `series_episodes`.`stream_id` WHERE `series_id` = " . intval($rSeries["id"]) . " ORDER BY `series_episodes`.`season_num` ASC, `series_episodes`.`sort` ASC;");
if ($result && 0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $rSeasons[$row["season_num"]][] = ["id" => $row["id"], "title" => $row["stream_display_name"]];
    }
}
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\r\n        ";
}
echo "                <!-- start page title -->\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            <div class=\"page-title-right\">\r\n                                <ol class=\"breadcrumb m-0\">\r\n\t\t\t\t\t\t\t\t\t<li>\r\n                                        <a href=\"./series.php\">\r\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_series"];
echo "</button>\r\n\t\t\t\t\t\t\t\t\t    </a>\t\r\n                                    </li>\r\n                                </ol>\r\n                            </div>\r\n                            <h4 class=\"page-title\">";
echo $rSeries["title"];
echo "</h4>\r\n                        </div>\r\n                    </div>\r\n                </div>     \r\n                <!-- end page title --> \r\n                <div class=\"row\">\r\n                    <div class=\"col-xl-12\">\r\n                        <div class=\"card\">\r\n                            <div class=\"card-body\">\r\n                                <form action=\"./series_order.php?id=";
echo $_GET["id"];
echo "\" method=\"POST\" id=\"episode_order_form\">\r\n                                    <input type=\"hidden\" id=\"episode_order_array\" name=\"episode_order_array\" value=\"\" />\r\n                                    <input type=\"hidden\" name=\"reorder\" value=\"";
echo $_GET["id"];
echo "\" />\r\n                                    <div id=\"basicwizard\">\r\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\r\n                                            ";
foreach ($rSeasons as $rSeasonNum => $rSeasonArray) {
    echo "                                            <li class=\"nav-item\">\r\n                                                <a href=\"#season-";
    echo $rSeasonNum;
    echo "\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \r\n                                                    <span class=\"d-none d-sm-inline\">S";
    echo sprintf("%02d", $rSeasonNum);
    echo "</span>\r\n                                                </a>\r\n                                            </li>\r\n                                            ";
}
echo "                                        </ul>\r\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\r\n                                            ";
foreach ($rSeasons as $rSeasonNum => $rSeasonArray) {
    echo "                                            <div class=\"tab-pane\" id=\"season-";
    echo $rSeasonNum;
    echo "\">\r\n                                                <div class=\"row\">\r\n                                                    <div class=\"col-12\">\r\n                                                        <p class=\"sub-header\">\r\n                                                         ";
    echo $_["to_re-order"];
    echo "  <i class=\"mdi mdi-chevron-up\"></i> ";
    echo $_["and"];
    echo " <i class=\"mdi mdi-chevron-down\"></i> ";
    echo $_["buttons_to_move_it"];
    echo "                                                        </p>\r\n                                                        <select multiple id=\"sort_episode_";
    echo $rSeasonNum;
    echo "\" class=\"form-control\" style=\"min-height:400px;\">\r\n                                                        ";
    $i = 0;
    foreach ($rSeasonArray as $rEpisode) {
        $i++;
        echo "                                                            <option value=\"";
        echo $rEpisode["id"];
        echo "\">";
        echo $i;
        echo " - ";
        echo $rEpisode["title"];
        echo "</option>\r\n                                                        ";
    }
    echo "                                                        </select>\r\n                                                    </div> <!-- end col -->\r\n                                                </div> <!-- end row -->\r\n                                                <ul class=\"list-inline wizard mb-0 add-margin-top-20\">\r\n                                                    <li class=\"list-inline-item\">\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveUp(";
    echo $rSeasonNum;
    echo ")\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-up\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"MoveDown(";
    echo $rSeasonNum;
    echo ")\" class=\"btn btn-purple\"><i class=\"mdi mdi-chevron-down\"></i></a>\r\n                                                        <a href=\"javascript: void(0);\" onClick=\"AtoZ(";
    echo $rSeasonNum;
    echo ")\" class=\"btn btn-info\">";
    echo $_["sort_all_a_to_z"];
    echo "</a>\r\n                                                    </li>\r\n                                                    <li class=\"list-inline-item float-right\">\r\n                                                        <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light\">";
    echo $_["save_changes"];
    echo "</button>\r\n                                                    </li>\r\n                                                </ul>\r\n                                            </div>\r\n                                            ";
}
echo "                                        </div>\r\n                                    </div> <!-- end #basicwizard-->\r\n                                </form>\r\n                            </div> <!-- end card-body -->\r\n                        </div> <!-- end card-->\r\n                    </div> <!-- end col -->\r\n                </div>\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\r\n        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\r\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\r\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\r\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\r\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\r\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\r\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\r\n        <script src=\"assets/libs/nestable2/jquery.nestable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.rowReorder.js\"></script>\r\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\r\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\r\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\r\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        \r\n        <script>\r\n        function AtoZ(rSeason) {\r\n            \$(\"#sort_episode_\" + rSeason).append(\$(\"#sort_episode_\" + rSeason + \" option\").remove().sort(function(a, b) {\r\n                var at = \$(a).text().toUpperCase().split(\"-\").slice(1).join(\"-\").trim()\r\n                var bt = \$(b).text().toUpperCase().split(\"-\").slice(1).join(\"-\").trim()\r\n                return (at > bt) ? 1 : ((at < bt) ? -1 : 0);\r\n            }));\r\n        }\r\n        function MoveUp(rSeason) {\r\n            var rSelected = \$('#sort_episode_' + rSeason + ' option:selected');\r\n            if (rSelected.length) {\r\n                var rPrevious = rSelected.first().prev()[0];\r\n                if (\$(rPrevious).html() != '') {\r\n                    rSelected.first().prev().before(rSelected);\r\n                }\r\n            }\r\n        }\r\n        function MoveDown(rSeason) {\r\n            var rSelected = \$('#sort_episode_' + rSeason + ' option:selected');\r\n            if (rSelected.length) {\r\n                rSelected.last().next().after(rSelected);\r\n            }\r\n        }\r\n        \$(document).ready(function() {\r\n            \$(\"#episode_order_form\").submit(function(e){\r\n                var rOrder = {};\r\n                ";
foreach ($rSeasons as $rSeasonNum => $rSeasonArray) {
    echo "                rOrder[";
    echo $rSeasonNum;
    echo "] = [];\r\n                \$('#sort_episode_";
    echo $rSeasonNum;
    echo " option').each(function() {\r\n                    if (\$(this).val()) {\r\n                        rOrder[";
    echo $rSeasonNum;
    echo "].push(\$(this).val());\r\n                    }\r\n                });\r\n                ";
}
echo "                \$(\"#episode_order_array\").val(JSON.stringify(rOrder));\r\n            });\r\n        });\r\n        </script>\r\n    </body>\r\n</html>";

?>