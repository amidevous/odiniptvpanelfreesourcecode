<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "connection_logs")) {
    exit;
}
$rows = getStreamProviders();
if (isset($_GET["delete"])) {
    deleteProviderDNS($_GET["delete"]);
    header("Location: line_connections.php");
}
if (isset($_POST["add"])) {
    $name = strip_tags($_REQUEST["name"]);
    $pass = strip_tags($_REQUEST["pass"]);
    $user = strip_tags($_REQUEST["user"]);
    $url = strip_tags($_REQUEST["url"]);
    insertProviderDNS($name, $url, $user, $pass);
    header("Location: line_connections.php");
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <h4 class=\"page-title\">Provider DNS</h4>\n                        </div>\n                    </div>\n                </div>\n                <!-- end page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"card\">\n                            <div class=\"card-body\" style=\"overflow-x:auto;\">\n\n\n                                <div class=\"form-group row mb-4\">\n\n                                  <form method=\"post\" >\n\n                                        <div class=\"form-group\">\n                                          <label class=\"bmd-label-floating\">Enter your provider's data below without / at the end</label>\n                                          <input type=\"text\" class=\"form-control\" name=\"url\" placeholder=\"http://serverdns:port\">\n                                          <label class=\"bmd-label-floating\"></label>\n                                          <input type=\"text\" class=\"form-control\" name=\"name\" placeholder=\"Provider Name\">\n\t\t\t\t\t\t\t\t\t\t  <label class=\"bmd-label-floating\"></label>\n                                          <input type=\"text\" class=\"form-control\" name=\"user\" placeholder=\"Provider Username\">\n                                          <label class=\"bmd-label-floating\"></label>\n                                          <input type=\"text\" class=\"form-control\" name=\"pass\" placeholder=\"Provider Password\">\n                                          <br></br>\n                                          <button type=\"submit\" name=\"add\" class=\"btn btn-dark\">ADD</button>\n                                        </div>\n                                </form>\n                                </div>\n                                <table id=\"datatable-activity\" class=\"table dt-responsive nowrap\">\n                                    <thead>\n                                        <tr>\n                                            <th>Provider ID</th>\n                                            <th>Provider Name</th>\n                                            <th>Provider DNS</th>\n                                            <th>Actions</th>\n                                        </tr>\n                                    </thead>\n                                    <tbody>\n                                      <tr>\n                                        ";
while ($row = mysqli_fetch_assoc($rows)) {
    echo "                                            <td>";
    echo $row["provider_id"];
    echo "</td>\n                                            <td>";
    echo $row["provider_name"];
    echo "</td>\n                                            <td>";
    echo $row["provider_dns"];
    echo "</td>\n                                            <td class=\"text-center\">\n                                        ";
    if (hasPermissions("adv", "edit_bouquet")) {
        echo "                                                <div class=\"btn-group\">\n                                                <a class=\"btn btn-dark waves-effect waves-light btn-xs\" href=\"./line_connections.php?delete=";
        echo $row["provider_id"];
        echo "\"><i class=\"mdi mdi-close\"></i></a>\n                                                <button type=\"button\" data-toggle=\"modal\" data-target=\"#exampleModal\" data-placement=\"top\" title=\"Check\" data-original-title=\"Check\" class=\"btn btn-dark waves-effect waves-light btn-xs btn-reboot-server\" data-id=\"";
        echo $row["provider_id"];
        echo "\"><i class=\"mdi mdi-eye\"></i></button>                                                </div>\n                                        ";
    } else {
        echo "--";
    }
    echo "                                            </td>\n                                        </tr>\n                                        ";
}
echo "                                    </tbody>\n                                </table>\n\n                            </div> <!-- end card body-->\n                        </div> <!-- end card -->\n                    </div><!-- end col-->\n                </div>\n                <!-- end row-->\n            </div> <!-- end container -->\n        </div>\n        <div class=\"modal fade downloadModal\" id=\"exampleModal\" role=\"dialog\" aria-labelledby=\"downloadLabel\" aria-hidden=\"true\" style=\"display: none;\" >\n            <div class=\"modal-dialog modal-dialog-centered\">\n                <div class=\"modal-content\">\n                    <div class=\"modal-header\">\n                        <h4 class=\"modal-title\" id=\"downloadModal\">Check Connections</h4>\n                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n                    </div>\n                    <div class=\"modal-body\">\n                        <div class=\"col-12\">\n                          <div class=\"fetched-data\"></div>\n                        </div>\n\n                    </div>\n                </div><!-- /.modal-content -->\n            </div><!-- /.modal-dialog -->\n        </div><!-- /.modal -->\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/js/pages/form-remember.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n\n\n\n  <script>\n\n\n  \$(document).ready(function(){\n    \$('#exampleModal').on('show.bs.modal', function (e) {\n        var rowid = \$(e.relatedTarget).data('id');\n        \$.ajax({\n            type : 'post',\n            url : 'fetch_record.php', //Here you will fetch records\n            data :  'rowid='+ rowid, //Pass \$id\n            success : function(data){\n            \$('.fetched-data').html(data);//Show fetched data from database\n            }\n        });\n     });\n});\n\n  function doDownload() {\n      if (\$(\"#download_url\").val().length > 0) {\n          window.open(\$(\"#download_url\").val());\n      }\n  }\n\n  function download() {\n      \$(\"#download_type\").val(\"\");\n      \$(\"#download_button\").attr(\"disabled\", true);\n      \$('.downloadModal').modal('show');\n  }\n          </script>\n        <!-- App js-->\n        <script src=\"assets/js/app.min.js\"></script>\n    </body>\n</html>\n";

?>