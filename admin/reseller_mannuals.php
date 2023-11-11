<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if ($rPermissions["is_admin"] && !hasPermissions("adv", "manage_tickets")) {
    exit;
}
$rStatusArray = ["CLOSED", "OPEN", "RESPONDED", "READ"];
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content\"><div class=\"container-fluid\">\r\n        ";
} else {
    echo "        <div class=\"wrapper\"><div class=\"container-fluid\">\r\n        ";
}
echo "                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"page-title-box\">\r\n                            ";
if (!$rPermissions["is_admin"]) {
    echo "                            ";
}
echo "                        </div>\r\n                    </div>\r\n                </div>  \r\n                <br>\t\t\r\n                <br>\t\t\t\t\r\n                <div class=\"row\">\r\n                    <div class=\"col-12\">\r\n                        <div class=\"col-12\">\r\n                        <div class=\"card card-body border\">\r\n                            <div class=\"row\">\r\n                                <div class=\"col-12\">\r\n                                </div>\r\n                                <div class=\"col-12\">\r\n                                    <div class=\"text-left\">\r\n                                        ";
echo $rSettings["page_mannuals"];
echo "                                    </div>\r\n                                </div>\r\n                            </div>\r\n\t\t\t\t\t\t</div>\r\n\t\t\t\t\t</div>\r\n                    </div><!-- end col -->\r\n                </div>\r\n                <!-- end row -->\r\n            </div> <!-- end container -->\r\n        </div>\r\n        <!-- end wrapper -->\r\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <footer class=\"footer\">\r\n            <div class=\"container-fluid\">\r\n                <div class=\"row\">\r\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\r\n                </div>\r\n            </div>\r\n        </footer>\r\n        <!-- end Footer -->\r\n        <script src=\"assets/js/vendor.min.js\"></script>\r\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\r\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\r\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\r\n        <script src=\"assets/js/app.min.js\"></script>\r\n        <script>\r\n        function api(rID, rType) {\r\n            if (rType == \"delete\") {\r\n                if (confirm('Are you sure you want to delete this ticket?') == false) {\r\n                    return;\r\n                }\r\n            }\r\n            \$.getJSON(\"./api.php?action=ticket&sub=\" + rType + \"&ticket_id=\" + rID, function(data) {\r\n                if (data.result == true) {\r\n                    location.reload();\r\n                } else {\r\n                    \$.toast(\"An error occured while processing your request.\");\r\n                }\r\n            }).fail(function() {\r\n                \$.toast(\"An error occured while processing your request.\");\r\n            });\r\n        }        \r\n        \$(document).ready(function() {\r\n            \$(\"#tickets-table\").DataTable({\r\n                language: {\r\n                    paginate: {\r\n                        previous: \"<i class='mdi mdi-chevron-left'>\",\r\n                        next: \"<i class='mdi mdi-chevron-right'>\"\r\n                    }\r\n                },\r\n                drawCallback: function() {\r\n                    \$(\".dataTables_paginate > .pagination\").addClass(\"pagination-rounded\")\r\n                },\r\n                order: [[ 0, \"desc\" ]],\r\n                stateSave: true\r\n            });\r\n            \$(\"#tickets-table\").css(\"width\", \"100%\");\r\n        });\r\n\t\t</script>\r\n<!-- copiar comando -->\r\n\t\t<script>\r\n\t\tfunction myFunction() {\r\n        var copyText = document.getElementById(\"myInput\");\r\n        copyText.select();\r\n        copyText.setSelectionRange(0, 99999)\r\n        document.execCommand(\"copy\");\r\n        alert(\"Copiou o texto: \" + copyText.value);\r\n        }\r\n        </script>\r\n    </body>\r\n</html>\r\n\r\n\r\n\r\n\r\n\r\n";

?>