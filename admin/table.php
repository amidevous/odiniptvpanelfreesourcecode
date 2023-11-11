<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "functions.php";
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(7);
if (!isset($_SESSION["hash"])) {
    exit;
}
$joinQuery = "";
if ($_GET["id"] == "mag_events") {
    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "manage_events")) {
        exit;
    }
    $table = "mag_events";
    $get = $_GET["id"];
    $primaryKey = "id";
    $extraWhere = "";
    $columns = [["db" => "send_time", "dt" => 0, "formatter" => function ($d, $row) {
        return date("Y-m-d H:i:s", $d);
    }], ["db" => "status", "dt" => 1], ["db" => "mag_device_id", "dt" => 2, "formatter" => function ($d, $row) {
        return base64_decode(getMag($d)["mac"]);
    }], ["db" => "event", "dt" => 3], ["db" => "msg", "dt" => 4], ["db" => "id", "dt" => 5, "formatter" => function ($d, $row) {
        $rButtons = "<button data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Delete\" type=\"button\" class=\"btn btn-dark waves-effect waves-light btn-xs\" onClick=\"api(" . $d . ", 'delete');\"><i class=\"mdi mdi-close\"></i></button>";
        return $rButtons;
    }]];
} else {
    if ($_GET["id"] == "bouquets_streams") {
        if (!$rPermissions["is_admin"] || !hasPermissions("adv", "bouquets")) {
            exit;
        }
        $table = "streams";
        $get = $_GET["id"];
        $primaryKey = "id";
        if (isset($_GET["category_id"]) && 0 < strlen($_GET["category_id"])) {
            $extraWhere = "(`type` = 1 OR `type` = 3) AND `category_id` = " . intval($_GET["category_id"]);
        } else {
            $extraWhere = "(`type` = 1 OR `type` = 3)";
        }
        $columns = [["db" => "id", "dt" => 0], ["db" => "stream_display_name", "dt" => 1], ["db" => "category_id", "dt" => 2, "formatter" => function ($d, $row) {
            global $rCategories;
            return $rCategories[$d]["category_name"];
        }], ["db" => "id", "dt" => 3, "formatter" => function ($d, $row) {
            return "<div class=\"btn-group\"><button data-id=\"" . $d . "\" data-type=\"stream\" type=\"button\" style=\"display: none;\" class=\"btn-remove btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(" . $d . ", 'stream', true);\"><i class=\"mdi mdi-minus\"></i></button>\n                <button data-id=\"" . $d . "\" data-type=\"stream\" type=\"button\" style=\"display: none;\" class=\"btn-add btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(" . $d . ", 'stream', true);\"><i class=\"mdi mdi-plus\"></i></button></div>";
        }]];
    } else {
        if ($_GET["id"] == "streams_short") {
            if (!$rPermissions["is_admin"] || !hasPermissions("adv", "categories")) {
                exit;
            }
            $table = "streams";
            $get = $_GET["id"];
            $primaryKey = "id";
            if (isset($_GET["category_id"]) && 0 < strlen($_GET["category_id"])) {
                $extraWhere = "(`type` = 1 OR `type` = 3) AND `category_id` = " . intval($_GET["category_id"]);
            } else {
                $extraWhere = "(`type` = 1 OR `type` = 3)";
            }
            $columns = [["db" => "id", "dt" => 0], ["db" => "stream_display_name", "dt" => 1], ["db" => "id", "dt" => 2, "formatter" => function ($d, $row) {
                if (hasPermissions("adv", "edit_stream")) {
                    return "<a href=\"./stream.php?id=" . $d . "\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit Stream\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>";
                }
                return "--";
            }]];
        } else {
            if ($_GET["id"] == "movies_short") {
                if (!$rPermissions["is_admin"] || !hasPermissions("adv", "categories")) {
                    exit;
                }
                $table = "streams";
                $get = $_GET["id"];
                $primaryKey = "id";
                if (isset($_GET["category_id"]) && 0 < strlen($_GET["category_id"])) {
                    $extraWhere = "`type` = 2 AND `category_id` = " . intval($_GET["category_id"]);
                } else {
                    $extraWhere = "`type` = 2";
                }
                $columns = [["db" => "id", "dt" => 0], ["db" => "stream_display_name", "dt" => 1], ["db" => "id", "dt" => 2, "formatter" => function ($d, $row) {
                    if (hasPermissions("adv", "edit_movie")) {
                        return "<a href=\"./movie.php?id=" . $d . "\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit Movie\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>";
                    }
                    return "--";
                }]];
            } else {
                if ($_GET["id"] == "radios_short") {
                    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "categories")) {
                        exit;
                    }
                    $table = "streams";
                    $get = $_GET["id"];
                    $primaryKey = "id";
                    if (isset($_GET["category_id"]) && 0 < strlen($_GET["category_id"])) {
                        $extraWhere = "`type` = 4 AND `category_id` = " . intval($_GET["category_id"]);
                    } else {
                        $extraWhere = "`type` = 4";
                    }
                    $columns = [["db" => "id", "dt" => 0], ["db" => "stream_display_name", "dt" => 1], ["db" => "id", "dt" => 2, "formatter" => function ($d, $row) {
                        if (hasPermissions("adv", "edit_radio")) {
                            return "<a href=\"./radio.php?id=" . $d . "\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit Station\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>";
                        }
                        return "--";
                    }]];
                } else {
                    if ($_GET["id"] == "series_short") {
                        if (!$rPermissions["is_admin"] || !hasPermissions("adv", "categories")) {
                            exit;
                        }
                        $table = "series";
                        $get = $_GET["id"];
                        $primaryKey = "id";
                        if (isset($_GET["category_id"]) && 0 < strlen($_GET["category_id"])) {
                            $extraWhere = "`category_id` = " . intval($_GET["category_id"]);
                        } else {
                            $extraWhere = "";
                        }
                        $columns = [["db" => "id", "dt" => 0], ["db" => "title", "dt" => 1], ["db" => "id", "dt" => 2, "formatter" => function ($d, $row) {
                            if (hasPermissions("adv", "edit_series")) {
                                return "<a href=\"./series.php?id=" . $d . "\"><button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Edit Series\" class=\"btn btn-dark waves-effect waves-light btn-xs\"><i class=\"mdi mdi-pencil-outline\"></i></button></a>";
                            }
                            return "--";
                        }]];
                    } else {
                        if ($_GET["id"] == "vod_selection") {
                            if (!$rPermissions["is_admin"] || !hasPermissions("adv", "create_channel")) {
                                exit;
                            }
                            $rCategoriesVOD = getCategories("movie");
                            $rSeriesList = getEpisodeParents();
                            $table = "streams";
                            $get = $_GET["id"];
                            $primaryKey = "id";
                            if (isset($_GET["category_id"]) && 0 < strlen($_GET["category_id"])) {
                                $rSplit = explode(":", $_GET["category_id"]);
                                if (intval($rSplit[0]) == 0) {
                                    $extraWhere = "`type` = 2 AND `category_id` = " . intval($rSplit[1]);
                                } else {
                                    $rEpisodeList = [];
                                    foreach ($rSeriesList as $rID => $rRow) {
                                        if (intval($rSplit[1]) == intval($rRow["id"])) {
                                            $rEpisodeList[] = $rID;
                                        }
                                    }
                                    $extraWhere = "`type` = 5 AND `id` IN (" . join(",", $rEpisodeList) . ")";
                                }
                            } else {
                                $extraWhere = "`type` IN (2,5)";
                            }
                            $extraWhere .= " AND `stream_source` LIKE '%s:" . intval($_GET["server_id"]) . ":%'";
                            $columns = [["db" => "id", "dt" => 0], ["db" => "stream_display_name", "dt" => 1], ["db" => "category_id", "dt" => 2, "formatter" => function ($d, $row) {
                                global $rCategoriesVOD;
                                global $rSeriesList;
                                if ($row["type"] == 5) {
                                    return $rSeriesList[$row["id"]]["title"];
                                }
                                return $rCategoriesVOD[$d]["category_name"];
                            }], ["db" => "type", "dt" => 3, "formatter" => function ($d, $row) {
                                return "<div class=\"btn-group\"><button data-id=\"" . $row["id"] . "\" data-type=\"vod\" type=\"button\" style=\"display: none;\" class=\"btn-remove btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleSelection(" . $row["id"] . ");\"><i class=\"mdi mdi-minus\"></i></button>\n                <button data-id=\"" . $row["id"] . "\" data-type=\"vod\" type=\"button\" style=\"display: none;\" class=\"btn-add btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleSelection(" . $row["id"] . ");\"><i class=\"mdi mdi-plus\"></i></button></div>";
                            }]];
                        } else {
                            if ($_GET["id"] == "bouquets_vod") {
                                if (!$rPermissions["is_admin"] || !hasPermissions("adv", "bouquets")) {
                                    exit;
                                }
                                $rCategoriesVOD = getCategories("movie");
                                $table = "streams";
                                $get = $_GET["id"];
                                $primaryKey = "id";
                                if (isset($_GET["category_id"]) && 0 < strlen($_GET["category_id"])) {
                                    $extraWhere = "`type` = 2 AND `category_id` = " . intval($_GET["category_id"]);
                                } else {
                                    $extraWhere = "`type` = 2";
                                }
                                $columns = [["db" => "id", "dt" => 0], ["db" => "stream_display_name", "dt" => 1], ["db" => "category_id", "dt" => 2, "formatter" => function ($d, $row) {
                                    global $rCategoriesVOD;
                                    return $rCategoriesVOD[$d]["category_name"];
                                }], ["db" => "id", "dt" => 3, "formatter" => function ($d, $row) {
                                    return "<div class=\"btn-group\"><button data-id=\"" . $d . "\" data-type=\"vod\" type=\"button\" style=\"display: none;\" class=\"btn-remove btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(" . $d . ", 'vod', true);\"><i class=\"mdi mdi-minus\"></i></button>\n                <button data-id=\"" . $d . "\" data-type=\"vod\" type=\"button\" style=\"display: none;\" class=\"btn-add btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(" . $d . ", 'vod', true);\"><i class=\"mdi mdi-plus\"></i></button></div>";
                                }]];
                            } else {
                                if ($_GET["id"] == "bouquets_series") {
                                    if (!$rPermissions["is_admin"] || !hasPermissions("adv", "bouquets")) {
                                        exit;
                                    }
                                    $rCategoriesVOD = getCategories("series");
                                    $table = "series";
                                    $get = $_GET["id"];
                                    $primaryKey = "id";
                                    if (isset($_GET["category_id"]) && 0 < strlen($_GET["category_id"])) {
                                        $extraWhere = "`category_id` = " . intval($_GET["category_id"]);
                                    } else {
                                        $extraWhere = "";
                                    }
                                    $columns = [["db" => "id", "dt" => 0], ["db" => "title", "dt" => 1], ["db" => "category_id", "dt" => 2, "formatter" => function ($d, $row) {
                                        global $rCategoriesVOD;
                                        return $rCategoriesVOD[$d]["category_name"];
                                    }], ["db" => "id", "dt" => 3, "formatter" => function ($d, $row) {
                                        return "<div class=\"btn-group\"><button data-id=\"" . $d . "\" data-type=\"series\" type=\"button\" style=\"display: none;\" class=\"btn-remove btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(" . $d . ", 'series', true);\"><i class=\"mdi mdi-minus\"></i></button>\n                <button data-id=\"" . $d . "\" data-type=\"series\" type=\"button\" style=\"display: none;\" class=\"btn-add btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(" . $d . ", 'series', true);\"><i class=\"mdi mdi-plus\"></i></button></div>";
                                    }]];
                                } else {
                                    if ($_GET["id"] == "bouquets_radios") {
                                        if (!$rPermissions["is_admin"] || !hasPermissions("adv", "bouquets")) {
                                            exit;
                                        }
                                        $rCategoriesVOD = getCategories("radio");
                                        $table = "streams";
                                        $get = $_GET["id"];
                                        $primaryKey = "id";
                                        if (isset($_GET["category_id"]) && 0 < strlen($_GET["category_id"])) {
                                            $extraWhere = "`type` = 4 AND `category_id` = " . intval($_GET["category_id"]);
                                        } else {
                                            $extraWhere = "`type` = 4";
                                        }
                                        $columns = [["db" => "id", "dt" => 0], ["db" => "stream_display_name", "dt" => 1], ["db" => "category_id", "dt" => 2, "formatter" => function ($d, $row) {
                                            global $rCategoriesVOD;
                                            return $rCategoriesVOD[$d]["category_name"];
                                        }], ["db" => "id", "dt" => 3, "formatter" => function ($d, $row) {
                                            return "<div class=\"btn-group\"><button data-id=\"" . $d . "\" data-type=\"radios\" type=\"button\" style=\"display: none;\" class=\"btn-remove btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(" . $d . ", 'radios', true);\"><i class=\"mdi mdi-minus\"></i></button>\n                <button data-id=\"" . $d . "\" data-type=\"radios\" type=\"button\" style=\"display: none;\" class=\"btn-add btn btn-dark waves-effect waves-light btn-xs\" onClick=\"toggleBouquet(" . $d . ", 'radios', true);\"><i class=\"mdi mdi-plus\"></i></button></div>";
                                        }]];
                                    } else {
                                        exit;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
$sql_details = ["user" => $_INFO["db_user"], "pass" => $_INFO["db_pass"], "db" => $_INFO["db_name"], "host" => $_INFO["host"] . ":" . $_INFO["db_port"]];
echo json_encode(SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
class SSP
{
    public static function data_output($columns, $data, $isJoin = false)
    {
        global $get;
        global $rStreamInformation;
        $out = [];
        $i = 0;
        for ($ien = count($data); $i < $ien; $i++) {
            $row = [];
            if ($get == "streams") {
                list($rStreamInformation[intval($data[$i]["id"])]) = getStreams(NULL, true, [$data[$i]["id"]]);
                if (count($rStreamInformation[intval($data[$i]["id"])]["servers"]) == 0) {
                    $rStreamInformation[intval($data[$i]["id"])]["servers"][] = ["id" => 0, "active_count" => 0, "stream_text" => "Not Available", "uptime_text" => "--", "actual_status" => 0];
                }
                foreach ($rStreamInformation[intval($data[$i]["id"])]["servers"] as $rServer) {
                    $j = 0;
                    for ($jen = count($columns); $j < $jen; $j++) {
                        $column = $columns[$j];
                        if (isset($column["formatter"])) {
                            $row[$column["dt"]] = $isJoin ? $column["formatter"]($data[$i][$column["field"]], $data[$i], $rServer) : $column["formatter"]($data[$i][$column["db"]], $data[$i], $rServer);
                        } else {
                            if (!isset($column["hide"])) {
                                $row[$column["dt"]] = $isJoin ? $data[$i][$columns[$j]["field"]] : $data[$i][$columns[$j]["db"]];
                            }
                        }
                    }
                    $out[] = $row;
                }
            } else {
                $j = 0;
                for ($jen = count($columns); $j < $jen; $j++) {
                    $column = $columns[$j];
                    if (isset($column["formatter"])) {
                        $row[$column["dt"]] = $isJoin ? $column["formatter"]($data[$i][$column["field"]], $data[$i]) : $column["formatter"]($data[$i][$column["db"]], $data[$i]);
                    } else {
                        if (!isset($column["hide"])) {
                            $row[$column["dt"]] = $isJoin ? $data[$i][$columns[$j]["field"]] : $data[$i][$columns[$j]["db"]];
                        }
                    }
                }
                $out[] = $row;
            }
        }
        return $out;
    }
    public static function limit($request, $columns)
    {
        $limit = "";
        if (isset($request["start"]) && $request["length"] != -1) {
            $limit = "LIMIT " . intval($request["start"]) . ", " . intval($request["length"]);
        } else {
            $limit = "LIMIT 50";
        }
        return $limit;
    }
    public static function order($request, $columns, $isJoin = false)
    {
        $order = "";
        if (isset($request["order"]) && count($request["order"])) {
            $orderBy = [];
            $dtColumns = SSP::pluck($columns, "dt");
            $i = 0;
            for ($ien = count($request["order"]); $i < $ien; $i++) {
                $columnIdx = intval($request["order"][$i]["column"]);
                $requestColumn = $request["columns"][$columnIdx];
                $columnIdx = array_search($requestColumn["data"], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn["orderable"] == "true") {
                    $dir = $request["order"][$i]["dir"] === "asc" ? "ASC" : "DESC";
                    $orderBy[] = $isJoin ? $column["db"] . " " . $dir : "`" . $column["db"] . "` " . $dir;
                }
            }
            $order = "ORDER BY " . implode(", ", $orderBy);
        }
        return $order;
    }
    public static function filter($request, $columns, &$bindings, $isJoin = false, $table = NULL)
    {
        $globalSearch = [];
        $columnSearch = [];
        $dtColumns = SSP::pluck($columns, "dt");
        if (isset($request["search"]) && $request["search"]["value"] != "") {
            $str = $request["search"]["value"];
            $i = 0;
            for ($ien = count($request["columns"]); $i < $ien; $i++) {
                $requestColumn = $request["columns"][$i];
                $columnIdx = array_search($requestColumn["data"], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn["searchable"] == "true") {
                    if ($column["db"] == "mac" && $table == "mag_devices") {
                        $str = base64_encode($str);
                    }
                    $binding = SSP::bind($bindings, "%" . $str . "%", PDO::PARAM_STR);
                    $globalSearch[] = $isJoin ? $column["db"] . " LIKE " . $binding : "`" . $column["db"] . "` LIKE " . $binding;
                }
            }
        }
        $i = 0;
        for ($ien = count($request["columns"]); $i < $ien; $i++) {
            $requestColumn = $request["columns"][$i];
            $columnIdx = array_search($requestColumn["data"], $dtColumns);
            $column = $columns[$columnIdx];
            $str = $requestColumn["search"]["value"];
            if ($requestColumn["searchable"] == "true" && $str != "") {
                if ($column["db"] == "mac" && $table == "mag_devices") {
                    $str = base64_encode($str);
                }
                $binding = SSP::bind($bindings, "%" . $str . "%", PDO::PARAM_STR);
                $columnSearch[] = $isJoin ? $column["db"] . " LIKE " . $binding : "`" . $column["db"] . "` LIKE " . $binding;
            }
        }
        $where = "";
        if (count($globalSearch)) {
            $where = "(" . implode(" OR ", $globalSearch) . ")";
        }
        if (count($columnSearch)) {
            $where = $where === "" ? implode(" AND ", $columnSearch) : $where . " AND " . implode(" AND ", $columnSearch);
        }
        if ($where !== "") {
            $where = "WHERE " . $where;
        }
        return $where;
    }
    public static function simple($request, $sql_details, $table, $primaryKey, $columns, $joinQuery = NULL, $extraWhere = "", $groupBy = "", $having = "")
    {
        $bindings = [];
        $db = SSP::sql_connect($sql_details);
        $limit = SSP::limit($request, $columns);
        $order = SSP::order($request, $columns, $joinQuery);
        $where = SSP::filter($request, $columns, $bindings, $joinQuery, $table);
        if ($extraWhere) {
            $extraWhere = $where ? " AND " . $extraWhere : " WHERE " . $extraWhere;
        }
        $groupBy = $groupBy ? " GROUP BY " . $groupBy . " " : "";
        $having = $having ? " HAVING " . $having . " " : "";
        if ($joinQuery) {
            $col = SSP::pluck($columns, "db", $joinQuery);
            $query = "SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $col) . "\n             " . $joinQuery . "\n             " . $where . "\n             " . $extraWhere . "\n             " . $groupBy . "\n       " . $having . "\n             " . $order . "\n             " . $limit;
        } else {
            $query = "SELECT SQL_CALC_FOUND_ROWS `" . implode("`, `", SSP::pluck($columns, "db")) . "`\n             FROM `" . $table . "`\n             " . $where . "\n             " . $extraWhere . "\n             " . $groupBy . "\n       " . $having . "\n             " . $order . "\n             " . $limit;
        }
        $data = SSP::sql_exec($db, $bindings, $query);
        $resFilterLength = SSP::sql_exec($db, "SELECT FOUND_ROWS()");
        $recordsFiltered = $resFilterLength[0][0];
        $resTotalLength = SSP::sql_exec($db, "SELECT COUNT(`" . $primaryKey . "`)\n             FROM   `" . $table . "`");
        if ($rPermissions["is_admin"]) {
            $recordsTotal = $resTotalLength[0][0];
        } else {
            $recordsTotal = $recordsFiltered;
        }
        return ["draw" => intval($request["draw"]), "recordsTotal" => intval($recordsTotal), "recordsFiltered" => intval($recordsFiltered), "data" => SSP::data_output($columns, $data, $joinQuery)];
    }
    public static function sql_connect($sql_details)
    {
        try {
            $db = @new PDO("mysql:host=" . $sql_details["host"] . ";dbname=" . $sql_details["db"], $sql_details["user"], $sql_details["pass"], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $db->query("SET NAMES 'utf8'");
        } catch (PDOException $e) {
            SSP::fatal("An error occurred while connecting to the database. The error reported by the server was: " . $e->getMessage());
        }
        return $db;
    }
    public static function sql_exec($db, $bindings, $sql = NULL)
    {
        if ($sql === NULL) {
            $sql = $bindings;
        }
        $stmt = $db->prepare($sql);
        if (is_array($bindings)) {
            $i = 0;
            for ($ien = count($bindings); $i < $ien; $i++) {
                $binding = $bindings[$i];
                $stmt->bindValue($binding["key"], $binding["val"], $binding["type"]);
            }
        }
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            SSP::fatal("An SQL error occurred: " . $e->getMessage());
        }
        return $stmt->fetchAll();
    }
    public static function fatal($msg)
    {
        echo json_encode(["error" => $msg]);
        exit(0);
    }
    public static function bind(&$a, $val, $type)
    {
        $key = ":binding_" . count($a);
        $a[] = ["key" => $key, "val" => $val, "type" => $type];
        return $key;
    }
    public static function pluck($a, $prop, $isJoin = false)
    {
        $out = [];
        $i = 0;
        for ($len = count($a); $i < $len; $i++) {
            $out[] = $isJoin && isset($a[$i]["as"]) ? $a[$i][$prop] . " AS " . $a[$i]["as"] : $a[$i][$prop];
        }
        return $out;
    }
}

?>