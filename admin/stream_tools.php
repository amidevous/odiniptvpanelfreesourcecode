<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "stream_tools")) {
    exit;
}
if (isset($_POST["replace_dns"])) {
    $rOldDNS = ESC(str_replace("/", "\\/", $_POST["old_dns"]));
    $rNewDNS = ESC(str_replace("/", "\\/", $_POST["new_dns"]));
    $db->query("UPDATE `streams` SET `stream_source` = REPLACE(`stream_source`, '" . $rOldDNS . "', '" . $rNewDNS . "');");
    $_STATUS = 1;
} else {
    if (isset($_POST["move_streams"])) {
        $rSource = $_POST["source_server"];
        $rReplacement = $_POST["replacement_server"];
        $rExisting = [];
        $result = $db->query("SELECT `id` FROM `streams_sys` WHERE `server_id` = " . intval($rReplacement) . ";");
        if ($result && 0 < $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $rExisting[] = intval($row["id"]);
            }
        }
        $result = $db->query("SELECT `id` FROM `streams_sys` WHERE `server_id` = " . intval($rSource) . ";");
        if ($result && 0 < $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                if (in_array(intval($row["id"]), $rExisting)) {
                    $db->query("DELETE FROM `streams_sys` WHERE `id` = " . intval($row["id"]) . ";");
                }
            }
        }
        $db->query("UPDATE `streams_sys` SET `server_id` = " . intval($rReplacement) . " WHERE `server_id` = " . intval($rSource) . ";");
        $_STATUS = 2;
    } else {
        if (isset($_POST["cleanup_streams"])) {
            $rStreams = getStreamList();
            $rStreamArray = [];
            foreach ($rStreams as $rStream) {
                $rStreamArray[] = intval($rStream["id"]);
            }
            $rDelete = [];
            $result = $db->query("SELECT `server_stream_id`, `stream_id` FROM `streams_sys`;");
            if ($result && 0 < $result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    if (!in_array(intval($row["stream_id"]), $rStreamArray)) {
                        $rDelete[] = $row["server_stream_id"];
                    }
                }
            }
            if (0 < count($rDelete)) {
                $db->query("DELETE FROM `streams_sys` WHERE `server_stream_id` IN (" . join(",", $rDelete) . ");");
            }
            $rDelete = [];
            $result = $db->query("SELECT `id`, `stream_id` FROM `client_logs`;");
            if ($result && 0 < $result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    if (!in_array(intval($row["stream_id"]), $rStreamArray)) {
                        $rDelete[] = $row["id"];
                    }
                }
            }
            if (0 < count($rDelete)) {
                $db->query("DELETE FROM `client_logs` WHERE `id` IN (" . join(",", $rDelete) . ");");
            }
            $rDelete = [];
            $result = $db->query("SELECT `id`, `stream_id` FROM `stream_logs`;");
            if ($result && 0 < $result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    if (!in_array(intval($row["stream_id"]), $rStreamArray)) {
                        $rDelete[] = $row["id"];
                    }
                }
            }
            if (0 < count($rDelete)) {
                $db->query("DELETE FROM `stream_logs` WHERE `id` IN (" . join(",", $rDelete) . ");");
            }
            $rDelete = [];
            $result = $db->query("SELECT `activity_id`, `stream_id` FROM `user_activity`;");
            if ($result && 0 < $result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    if (!in_array(intval($row["stream_id"]), $rStreamArray)) {
                        $rDelete[] = $row["activity_id"];
                    }
                }
            }
            if (0 < count($rDelete)) {
                $db->query("DELETE FROM `user_activity` WHERE `activity_id` IN (" . join(",", $rDelete) . ");");
            }
            $_STATUS = 3;
        }
    }
}
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <!--<div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <a href=\"./streams.php\"><li class=\"breadcrumb-item\"><i class=\"mdi mdi-backspace\"></i> ";
echo $_["back_to_streams"];
echo " </li></a>\n                                </ol>\n                            </div>-->\n                            <h4 class=\"page-title\">Tools </h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 1) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["stream_dns_replacement"];
        echo " \n                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["stream_dns_replacement"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && $_STATUS == 2) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["streams_have_been_moved"];
            echo " \n                        </div>\n\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["streams_have_been_moved"];
            echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
        }
    } else {
        if (isset($_STATUS) && $_STATUS == 3) {
            if (!$rSettings["sucessedit"]) {
                echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                echo $_["stream_cleanup_was_successful"];
                echo "                        </div>\n\t\t\t\t\t\t";
            } else {
                echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                echo $_["stream_cleanup_was_successful"];
                echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
            }
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n\t\t\t\t\t\t\t\t<div id=\"basicwizard\">\n\t\t\t\t\t\t\t\t\t<ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n\t\t\t\t\t\t\t\t\t\t<li class=\"nav-item\">\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"#dns-replacement\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"mdi mdi-dns mr-1\"></i>\n\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"d-none d-sm-inline\">";
echo $_["dns_eeplacement"];
echo " </span>\n\t\t\t\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t\t<li class=\"nav-item\">\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"#move-streams\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"mdi mdi-folder-move mr-1\"></i>\n\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"d-none d-sm-inline\">";
echo $_["move_streams"];
echo " </span>\n\t\t\t\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t\t\t</li>\n                                        <li class=\"nav-item\">\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"#cleanup\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"mdi mdi-wrench mr-1\"></i>\n\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"d-none d-sm-inline\">";
echo $_["cleanup"];
echo " ";
echo $_["streams"];
echo " </span>\n\t\t\t\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t<div class=\"tab-content b-0 mb-0 pt-0\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"tab-pane\" id=\"dns-replacement\">\n\t\t\t\t\t\t\t\t\t\t\t<form action=\"./stream_tools.php\" method=\"POST\" id=\"tools_form\" data-parsley-validate=\"\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"sub-header\">\n                                                            ";
echo $_["the_dns_replacement"];
echo " \n                                                        </p>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"sub-header\">\n                                                            If you want to move the VOD Server s:2: ( replace number 2 with your VOD server ).\n                                                        </p>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"old_dns\">";
echo $_["old_dns"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"old_dns\" name=\"old_dns\" value=\"\" placeholder=\"http://example.com\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"new_dns\">";
echo $_["new_dns"];
echo " </label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"new_dns\" name=\"new_dns\" value=\"\" placeholder=\"http://newdns.com\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"list-inline-item\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"custom-control custom-checkbox\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"custom-control-input\" id=\"confirmReplace\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"custom-control-label\" for=\"confirmReplace\">";
echo $_["i_confirm_remplace"];
echo " </label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</li>\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input disabled name=\"replace_dns\" id=\"replace_dns\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["replace_dns"];
echo "\" />\n                                                    </li>\n                                                </ul>\n\t\t\t\t\t\t\t\t\t\t\t</form>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"tab-pane\" id=\"move-streams\">\n\t\t\t\t\t\t\t\t\t\t\t<form action=\"./stream_tools.php\" method=\"POST\" id=\"tools_form\" data-parsley-validate=\"\">\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"sub-header\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
echo $_["this_tool_will_allow_you"];
echo " \n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"source_server\">";
echo $_["source_server"];
echo " </label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<select name=\"source_server\" id=\"source_server\" class=\"form-control select2\" data-toggle=\"select2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
foreach ($rServers as $rServer) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $rServer["id"];
    echo "\">";
    echo $rServer["server_name"];
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"replacement_server\">";
echo $_["replacement_server"];
echo " </label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<select name=\"replacement_server\" id=\"replacement_server\" class=\"form-control select2\" data-toggle=\"select2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
foreach ($rServers as $rServer) {
    echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"";
    echo $rServer["id"];
    echo "\">";
    echo $rServer["server_name"];
    echo "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div> <!-- end col -->\n\t\t\t\t\t\t\t\t\t\t\t\t</div> <!-- end row -->\n\t\t\t\t\t\t\t\t\t\t\t\t<ul class=\"list-inline wizard mb-0\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"list-inline-item\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"custom-control custom-checkbox\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"custom-control-input\" id=\"confirmReplace2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"custom-control-label\" for=\"confirmReplace2\">";
echo $_["i_confirm_move"];
echo " </label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"list-inline-item float-right\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input disabled name=\"move_streams\" id=\"move_streams\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["move_streams"];
echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\t\t</form>\n\t\t\t\t\t\t\t\t\t\t</div>\n                                        <div class=\"tab-pane\" id=\"cleanup\">\n\t\t\t\t\t\t\t\t\t\t\t<form action=\"./stream_tools.php\" method=\"POST\" id=\"tools_form\" data-parsley-validate=\"\">\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p class=\"sub-header\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
echo $_["this_tool_will_clean"];
echo " \n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div> <!-- end col -->\n\t\t\t\t\t\t\t\t\t\t\t\t</div> <!-- end row -->\n\t\t\t\t\t\t\t\t\t\t\t\t<ul class=\"list-inline wizard mb-0\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"list-inline-item\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"custom-control custom-checkbox\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"custom-control-input\" id=\"confirmReplace3\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"custom-control-label\" for=\"confirmReplace3\">";
echo $_["i_confirm_clean"];
echo " </label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"list-inline-item float-right\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input disabled name=\"cleanup_streams\" id=\"cleanup_streams\" type=\"submit\" class=\"btn btn-primary\" value=\"";
echo $_["cleanup"];
echo "\" />\n\t\t\t\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\t\t</form>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"tab-pane\" id=\"clearlogs\"></p>\n\t\t\t\t\t\t\t\t\t</div> <!-- tab-content -->\n\t\t\t\t\t\t\t\t</div> <!-- end #basicwizard-->\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/moment/moment.min.js\"></script>\n        <script src=\"assets/libs/daterangepicker/daterangepicker.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        \$(document).ready(function() {\n\t\t\t\$('select.select2').select2({width: '100%'});\n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n\t\t\t\$(\"#confirmReplace\").change(function() {\n\t\t\t\tif (\$(this).is(\":checked\")) {\n\t\t\t\t\t\$(\"#replace_dns\").attr(\"disabled\", false);\n\t\t\t\t} else {\n\t\t\t\t\t\$(\"#replace_dns\").attr(\"disabled\", true);\n\t\t\t\t}\n\t\t\t});\n\t\t\t\$(\"#confirmReplace2\").change(function() {\n\t\t\t\tif (\$(this).is(\":checked\")) {\n\t\t\t\t\t\$(\"#move_streams\").attr(\"disabled\", false);\n\t\t\t\t} else {\n\t\t\t\t\t\$(\"#move_streams\").attr(\"disabled\", true);\n\t\t\t\t}\n\t\t\t});\n            \$(\"#confirmReplace3\").change(function() {\n\t\t\t\tif (\$(this).is(\":checked\")) {\n\t\t\t\t\t\$(\"#cleanup_streams\").attr(\"disabled\", false);\n\t\t\t\t} else {\n\t\t\t\t\t\$(\"#cleanup_streams\").attr(\"disabled\", true);\n\t\t\t\t}\n\t\t\t});\n            \$(\"form\").attr('autocomplete', 'off');\n        });\n        </script>\n    </body>\n</html>";

?>