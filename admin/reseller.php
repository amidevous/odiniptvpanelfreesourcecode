<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if ($rPermissions["is_admin"]) {
    exit;
}
$rStatusArray = ["CLOSED", "OPEN", "RESPONDED", "READ"];
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
echo "                <!-- start page title -->\n                <!--<div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <h4 class=\"page-title\">";
echo $_["dashboard"];
echo "</h4>\n                        </div>\n                    </div>\n                </div>-->     \n\t\t\t\t<div class=\"card-box1\">\n                    \n                </div>\n                <!-- end page title --> \n\n                <div class=\"row\">\n                    <div class=\"col-4-md col-xl-3\">\n                        <div class=\"card-bg active-connections bg-info1\">\n\n                            <div class=\"card-bg active-connections\">\n\t\t\t\t\t\t        <div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between cta-box\">\n\t\t\t\t\t\t\t\t\t";
if ($rAdminSettings["dark_mode"]) {
    echo "\t\t\t\t\t\t\t\t\t<div class=\"avatar-md\">\n\t\t\t\t\t\t\t\t\t\t<i class=\"fe-box avatar-title font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t";
} else {
    echo "                                    <div class=\"avatar-md\">\n                                        <i class=\"fe-box avatar-title font-24 text-white\"></i>\n                                    </div>\n\t\t\t\t\t\t\t\t\t";
}
echo "                                </div>\n\t\t\t\t\t\t\t</div>\n                            <div class=\"col-md\" align=\"right\">\n\t\t\t\t\t\t\t\t<h3 class=\"text-white my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n\t\t\t\t\t\t\t\t<p class=\"text-white mb-1 text-truncate\">";
echo $_["open_connections"];
echo "</p>\n\t\t\t\t\t\t\t</div>\n                        </div><br> <!-- end card-box-->\n                    </div> <!-- end col -->\n\t\t\t\t\t\n\t\t\t\t\t<div class=\"col-4-md col-xl-3\">\n                        <div class=\"card-bg online-users bg-success1\">\n                            <div class=\"card-bg online-users\">\n\t\t\t\t\t\t\t\t<div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between cta-box\">\n\t\t\t\t\t\t\t\t\t";
if ($rAdminSettings["dark_mode"]) {
    echo "\t\t\t\t\t\t\t\t\t<div class=\"avatar-md\">\n\t\t\t\t\t\t\t\t\t\t<i class=\"fe-users avatar-title font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t";
} else {
    echo "                                    <div class=\"avatar-md\">\n                                        <i class=\"fe-users avatar-title font-24 text-white\"></i>\n                                    </div>\n\t\t\t\t\t\t\t\t\t";
}
echo "                                </div>\n\t\t\t\t\t\t\t</div>\n                            <div class=\"col-md\" align=\"right\">\n\t\t\t\t\t\t\t\t<h3 class=\"text-white my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n\t\t\t\t\t\t\t\t<p class=\"text-white mb-1 text-truncate\">";
echo $_["online_users"];
echo "</p>\n\t\t\t\t\t\t\t</div>\n                        </div><br> <!-- end card-box-->\n                    </div> <!-- end col -->\n\n                    <div class=\"col-4-md col-xl-3\">\n                        <div class=\"card-bg active-accounts bg-pink1\">\n                            <div class=\"card-bg active-accounts\">\n\t\t\t\t\t\t\t\t<div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between cta-box\">\n\t\t\t\t\t\t\t\t\t";
if ($rAdminSettings["dark_mode"]) {
    echo "\t\t\t\t\t\t\t\t\t<div class=\"avatar-md\">\n\t\t\t\t\t\t\t\t\t\t<i class=\"fe-check-circle avatar-title font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t";
} else {
    echo "                                    <div class=\"avatar-md\">\n                                        <i class=\"fe-check-circle avatar-title font-24 text-white\"></i>\n                                    </div>\n\t\t\t\t\t\t\t\t\t";
}
echo "                                </div>\n\t\t\t\t\t\t\t</div>\n                            <div class=\"col-md\" align=\"right\">\n                                <h3 class=\"text-white my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n                                <p class=\"text-white mb-1 text-truncate\">";
echo $_["active_accounts"];
echo "</p>\n                            </div>\n                        </div><br> <!-- end card-box-->\n                    </div> <!-- end col -->\n\n                    <div class=\"col-4-md col-xl-3\">\n                        <div class=\"card-bg credits bg-secondary1\">\n                            <div class=\"card-bg credits\">\n\t\t\t\t\t\t\t\t<div class=\"p-b-10 p-t-5 p-l-15 p-r-0 d-flex justify-content-between cta-box\">\n\t\t\t\t\t\t\t\t\t";
if ($rAdminSettings["dark_mode"]) {
    echo "\t\t\t\t\t\t\t\t\t<div class=\"avatar-md\">\n\t\t\t\t\t\t\t\t\t\t<i class=\"fe-dollar-sign avatar-title font-24 text-white\"></i>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t";
} else {
    echo "                                    <div class=\"avatar-md\">\n                                        <i class=\"fe-dollar-sign avatar-title font-24 text-white\"></i>\n                                    </div>\n\t\t\t\t\t\t\t\t\t";
}
echo "                                </div>\n\t\t\t\t\t\t\t</div>\n                            <div class=\"col-md\" align=\"right\">\n                                <h3 class=\"text-white my-1\"><span data-plugin=\"counterup\" class=\"entry\">0</span></h3>\n                                <p class=\"text-white mb-1 text-truncate\">";
echo $_["credits"];
echo "</p>\n                            </div>\n                        </div><br> <!-- end card-box-->\n                    </div><!-- end col -->\n                </div>\n\t\t\t\t<div class=\"row\">\n                    <div class=\"col-xl-6\">\n                        <div class=\"card border\">\n                            <div class=\"card-body\">\n                                <h4 class=\"header-title mb-0\">Last 10 Movies Added</h4>\n\t\t\t\t\t\t\t\t<div class= separator></div>\n                                <div id=\"cardActivity\" class=\"pt-3\">\n                                    <div class=\"text-center\">\n                                        ";
foreach (getLastMovies() as $rMinfo) {
    echo "\t\t\t\t\t\t\t\t\t\t";
    $rMovieCover = json_decode($rMinfo["movie_propeties"], true);
    if (0 < strlen($rMinfo["movie_image"])) {
        $rMovieCover = "<a target='_blank' href='https://www.themoviedb.org/movie/" . $rMovieCover["tmdb_id"] . "'><img height='120' width='70' src=" . $rMovieCover["movie_image"] . " /></a>";
    } else {
        $rMovieCover = "<a target='_blank' href='https://www.themoviedb.org/movie/" . $rMovieCover["tmdb_id"] . "'><img height='120' width='70' src=" . $rMovieCover["movie_image"] . " /></a>";
    }
    echo $rMovieCover;
    echo "\t\t\n                                        ";
}
echo "                                    </div>\n                                </div>\n\t\t\t\t\t\t    </div>\n\t\t\t\t\t     </div>\n                    </div><!-- end col -->\n                    <div class=\"col-xl-6\">\n                        <div class=\"card border\">\n                            <div class=\"card-body\">\n                                <h4 class=\"header-title mb-0\">Last 10 Series Added</h4>\n\t\t\t\t\t\t\t\t<div class= separator></div>\n                                <div id=\"cardActivity\" class=\"pt-3\">\n                                    <div class=\"text-center\">\n                                        ";
foreach (getLastSeries() as $rSinfo) {
    echo "\t\t\t\t\t\t\t\t\t    ";
    if (0 < strlen($rSinfo["cover"])) {
        $rSeriesCover = "<a target='_blank' href='https://www.themoviedb.org/tv/" . $rSinfo["tmdb_id"] . "'><img height='120' width='70' src=" . $rSinfo["cover"] . " /></a>";
    } else {
        $rSeriesCover = "<a target='_blank' href='https://www.themoviedb.org/tv/" . $rSinfo["tmdb_id"] . "'><img height='120' width='70' src=" . $rSinfo["cover"] . " /></a>";
    }
    echo $rSeriesCover;
    echo "\t\t\n                                        ";
}
echo "                                    </div>\n                                </div>\n                            </div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
if ($rPermissions["is_reseller"] && $rAdminSettings["reseller_view_info"]) {
    echo "\t\t\t\t\t<div class=\"col-12\">\n\t\t\t\t\t    ";
    if ($rAdminSettings["dark_mode"]) {
        echo "\t\t\t\t\t    <div class=\"card-header border bg-dark\">\n\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t<div class=\"card-header border bg-white\">\n\t\t\t\t\t\t";
    }
    echo "\t\t\t\t\t        <a data-toggle=\"collapse\" href=\"#cardCollpase1\" class=\"arrow-none card-drop\" data-parent=\"#cardCollpase1\" role=\"tablist\" aria-expanded=\"true\" aria-controls=\"cardCollpase1\">\n\t\t\t\t\t\t\t<h4 class=\"header-title mb-0 mdi mdi-magnify-minus\"> News</h4></a>\t\n\t\t\t\t\t\t\t";
    if ($rAdminSettings["dark_mode"]) {
        echo "                            <div id=\"cardCollpase1\" class=\"collapse pt-3 show bg-dark card-box\" style=\"margin-bottom:-8px;\">\n\t\t\t\t\t\t\t";
    } else {
        echo "\t\t\t\t\t\t\t<div id=\"cardCollpase1\" class=\"collapse pt-3 show bg-white card-box\" style=\"margin-bottom:-8px;\">\n\t\t\t\t\t\t\t";
    }
    echo "                                <div class=\"row\">\n                                    <div class=\"col-12\">\n                                        <div class=\"text-left\">\n                                        ";
    echo $rSettings["userpanel_mainpage"];
    echo "                                        </div>\n                                    </div>\n                                </div>\n\t\t\t\t\t\t    </div>\n\t\t\t\t\t    </div>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
}
echo "                </div><!-- end col -->\n\t\t\t\t";
if ($rPermissions["is_reseller"] && $rAdminSettings["reseller_view_info"]) {
    echo "\t\t\t\t<br>\n\t\t\t\t";
}
echo "                <div class=\"row\">\n                    <div class=\"col-xl-4\">\n                        <div class=\"card border\">\n                            <div class=\"card-body\">\n                                <h4 class=\"header-title mb-0\">";
echo $_["recent_activity"];
echo "</h4>\n\t\t\t\t\t\t\t\t<div class= separator></div>\n                                <div id=\"cardActivity\" class=\"pt-3\">\n                                    <div class=\"slimscroll\" style=\"height:350px;\">\n                                        <div class=\"timeline-alt\">\n                                            ";
$rResult = $db->query("SELECT `u`.`username`, `r`.`owner`, `r`.`date`, `r`.`type` FROM `reg_userlog` AS `r` INNER JOIN `reg_users` AS `u` ON `r`.`owner` = `u`.`id` WHERE `r`.`owner` IN (" . ESC(join(",", array_keys(getRegisteredUsers($rUserInfo["id"])))) . ") ORDER BY `r`.`date` DESC LIMIT 100;");
if ($rResult && 0 < $rResult->num_rows) {
    while ($rRow = $rResult->fetch_assoc()) {
        echo "                                                <div class=\"timeline-item\">\n                                                    <i class=\"timeline-icon\"></i>\n                                                    <div class=\"timeline-item-info\">\n                                                        <a href=\"#\" class=\"text-pink font-weight-semibold mb-1 d-block\"><i class=\"fas fa-user-alt text-secondary\"></i> ";
        echo $rRow["username"];
        echo "</a>\n                                                        <small>";
        echo html_entity_decode($rRow["type"]);
        echo "</small>\n                                                        <p>\n                                                            <small class=\"text-muted\">";
        echo date("Y-m-d H:i:s", $rRow["date"]);
        echo "</small>\n                                                        </p>\n                                                    </div>\n                                                </div>\n                                                ";
    }
}
echo "                                        </div>\n                                        <!-- end timeline -->\n                                    </div> <!-- end slimscroll -->\n                                </div> <!-- collapsed end -->\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col-->\n                    <div class=\"col-xl-8\">\n                        <div class=\"card border\">\n                            <div class=\"card-body\">\n                                <h4 class=\"header-title mb-0\">";
echo $_["expiring_lines"];
echo "</h4>\n\t\t\t\t\t\t\t\t<div class= separator></div>\n                                <div id=\"cardActivity\" class=\"pt-3\">\n                                    <div class=\"slimscroll\" style=\"height: 350px;\">\n                                        <table class=\"table table-hover m-0 table-centered dt-responsive nowrap w-100\" id=\"users-table\">\n                                            <thead>\n                                                <tr>\n\t\t\t\t\t\t\t\t\t\t\t\t    <th class=\"text-center\">";
echo $_["id"];
echo "</th>\n                                                    <th class=\"text-center\">";
echo $_["username"];
echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">";
echo $_["days"];
echo "</th>\n                                                    <th class=\"text-center\">";
echo $_["reseller"];
echo "</th>\n                                                    <th class=\"text-center\">";
echo $_["expiration"];
echo "</th>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-center\">";
echo $_["action"];
echo "</th>\n                                                </tr>\n                                            </thead>\n                                            <tbody>\n                                                ";
$rRegisteredUsers = getRegisteredUsers();
foreach (getExpiring($rUserInfo["id"]) as $rUser) {
    $today = time();
    $leftdaynumber = (strtotime(date("Y-m-d H:i", $rUser["exp_date"])) - $today) / 86400;
    $leftHourNumber = ($rUser["exp_date"] - $today) / 3600;
    $leftMinNumber = ($rUser["exp_date"] - $today) / 60;
    if (0 < $leftdaynumber && $leftdaynumber <= 1) {
        $rLeftDate = "1 Day";
    } else {
        if (1 < $leftdaynumber) {
            $rLeftDate = round($leftdaynumber) . " Days";
        } else {
            if (0 < $leftHourNumber && $leftHourNumber <= 1) {
                $rLeftDate = round($leftMinNumber) . " Minutes";
            } else {
                if (round($leftHourNumber) == 1) {
                    $rLeftDate = "1 Hour";
                } else {
                    if (1 < $leftHourNumber) {
                        $rLeftDate = round($leftHourNumber) . " Hours";
                    } else {
                        $rLeftDate = "<center>-</center>";
                    }
                }
            }
        }
    }
    echo "                                                <tr id=\"user-";
    echo $rUser["id"];
    echo "\">\n\t\t\t\t\t\t\t\t\t\t\t\t    <td class=\"text-center\">";
    echo $rUser["id"];
    echo "</td>\n                                                    <td class=\"text-center\"><a href=\"./user_reseller.php?id=";
    echo $rUser["id"];
    echo "\">";
    echo $rUser["username"];
    echo "</a></td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"text-center\">";
    echo $rLeftDate;
    echo "</td>\n                                                    <td class=\"text-center\">";
    echo $rRegisteredUsers[$rUser["member_id"]]["username"];
    echo "</td>\n                                                    <td class=\"text-center\">";
    echo date("Y-m-d H:i:s", $rUser["exp_date"]);
    echo "</td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"text-center\"><a href=\"./user_reseller.php?id=";
    echo $rUser["id"];
    echo "\"><button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Renew\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-autorenew mdi-spin\"></i></button></a></td>\n                                                </tr>\n                                                ";
}
echo "                                            </tbody>\n                                        </table>\n                                    </div> <!-- end slimscroll -->\n                                </div> <!-- collapsed end -->\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col-->\n                </div>\n                <!-- end row -->\n               \n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-knob/jquery.knob.min.js\"></script>\n        <script src=\"assets/libs/peity/jquery.peity.min.js\"></script>\n        <script src=\"assets/libs/apexcharts/apexcharts.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/jquery-number/jquery.number.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/js/pages/dashboard.init.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        function getStats() {\n            var rStart = Date.now();\n            \$.getJSON(\"./api.php?action=reseller_dashboard\", function(data) {\n                \$(\".active-connections .entry\").html(\$.number(data.open_connections, 0));\n                \$(\".online-users .entry\").html(\$.number(data.online_users, 0));\n                \$(\".active-accounts .entry\").html(\$.number(data.active_accounts, 0));\n                ";
if (floor($rUserInfo["credits"]) == $rUserInfo["credits"]) {
    echo "                \$(\".credits .entry\").html(\$.number(data.credits, 0));\n                ";
} else {
    echo "                \$(\".credits .entry\").html(\$.number(data.credits, 2));\n                ";
}
echo "                if (Date.now() - rStart < 1000) {\n                    setTimeout(getStats, 1000 - (Date.now() - rStart));\n                } else {\n                    getStats();\n                }\n            }).fail(function() {\n                setTimeout(getStats, 1000);\n            });\n        }\n        \n        \$(document).ready(function() {\n            getStats();\n        });\n        </script>\n    </body>\n</html>";

?>