<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_movie") && !hasPermissions("adv", "edit_movie")) {
    exit;
}
if (isset($_GET["import"]) && !hasPermissions("adv", "import_movies")) {
    exit;
}
$rCategories = getCategories("movie");
$rTranscodeProfiles = getTranscodeProfiles();
if (isset($_POST["submit_movie"])) {
    set_time_limit(0);
    ini_set("mysql.connect_timeout", 0);
    ini_set("max_execution_time", 0);
    ini_set("default_socket_timeout", 0);
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_movie")) {
            exit;
        }
        $rArray = getStream($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        if (!hasPermissions("adv", "add_movie")) {
            exit;
        }
        $rArray = ["movie_symlink" => 0, "type" => 2, "target_container" => ["mp4"], "added" => time(), "read_native" => 0, "stream_all" => 0, "redirect_stream" => 1, "direct_source" => 0, "gen_timestamps" => 1, "transcode_attributes" => [], "stream_display_name" => "", "stream_source" => [], "movie_subtitles" => [], "category_id" => 0, "stream_icon" => "", "notes" => "", "custom_sid" => "", "custom_ffmpeg" => "", "transcode_profile_id" => 0, "enable_transcode" => 0, "auto_restart" => "[]", "allow_record" => 0, "rtmp_output" => 0, "epg_id" => NULL, "channel_id" => NULL, "epg_lang" => NULL, "tv_archive_server_id" => 0, "tv_archive_duration" => 0, "delay_minutes" => 0, "external_push" => [], "probesize_ondemand" => 256000];
    }
    $rArray["stream_display_name"] = $_POST["stream_display_name"];
    if (0 < strlen($_POST["movie_subtitles"])) {
        $rSplit = explode(":", $_POST["movie_subtitles"]);
        $rArray["movie_subtitles"] = ["files" => [$rSplit[2]], "names" => ["Subtitles"], "charset" => ["UTF-8"], "location" => intval($rSplit[1])];
    } else {
        $rArray["movie_subtitles"] = [];
    }
    $rArray["notes"] = $_POST["notes"];
    if (isset($_POST["target_container"])) {
        $rArray["target_container"] = [$_POST["target_container"]];
    }
    $rArray["category_id"] = $_POST["category_id"];
    if (isset($_POST["custom_sid"])) {
        $rArray["custom_sid"] = $_POST["custom_sid"];
    }
    if (isset($_POST["transcode_profile_id"])) {
        $rArray["transcode_profile_id"] = $_POST["transcode_profile_id"];
        if (0 < $rArray["transcode_profile_id"]) {
            $rArray["enable_transcode"] = 1;
        } else {
            $rArray["enable_transcode"] = 0;
        }
    }
    if (isset($_POST["read_native"])) {
        $rArray["read_native"] = 1;
        unset($_POST["read_native"]);
    } else {
        $rArray["read_native"] = 0;
    }
    if (isset($_POST["movie_symlink"])) {
        $rArray["movie_symlink"] = 1;
        unset($_POST["movie_symlink"]);
    } else {
        $rArray["movie_symlink"] = 0;
    }
    if (isset($_POST["direct_source"])) {
        $rArray["direct_source"] = 1;
        unset($_POST["direct_source"]);
    } else {
        $rArray["direct_source"] = 0;
    }
    if (isset($_POST["redirect_stream"])) {
        $rArray["redirect_stream"] = 1;
        unset($_POST["redirect_stream"]);
    } else {
        $rArray["redirect_stream"] = 0;
    }
    if (isset($_POST["remove_subtitles"])) {
        $rArray["remove_subtitles"] = 1;
        unset($_POST["remove_subtitles"]);
    } else {
        $rArray["remove_subtitles"] = 0;
    }
    if (isset($_POST["restart_on_edit"])) {
        $rRestart = true;
        unset($_POST["restart_on_edit"]);
    } else {
        $rRestart = false;
    }
    $rBouquets = $_POST["bouquets"];
    unset($_POST["bouquets"]);
    $rImportStreams = [];
    if (!empty($_FILES["m3u_file"]["tmp_name"])) {
        if (!hasPermissions("adv", "import_movies")) {
            exit;
        }
        $rStreamDatabase = [];
        $result = $db->query("SELECT `stream_source` FROM `streams` WHERE `type` = 2;");
        if ($result && 0 < $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                foreach (json_decode($row["stream_source"], true) as $rSource) {
                    if (0 < strlen($rSource)) {
                        $rStreamDatabase[] = $rSource;
                    }
                }
            }
        }
        $rFile = "";
        if (!empty($_FILES["m3u_file"]["tmp_name"]) && strtolower(pathinfo($_FILES["m3u_file"]["name"], PATHINFO_EXTENSION)) == "m3u") {
            $rFile = file_get_contents($_FILES["m3u_file"]["tmp_name"]);
        }
        preg_match_all("/(?P<tag>#EXTINF:[-1,0])|(?:(?P<prop_key>[-a-z]+)=\\\"(?P<prop_val>[^\"]+)\")|(?<name>,[^\\r\\n]+)|(?<url>http[^\\s]*:\\/\\/.*\\/.*)/", $rFile, $rMatches);
        $rResults = [];
        $rIndex = -1;
        for ($i = 0; $i < count($rMatches[0]); $i++) {
            $rItem = $rMatches[0][$i];
            if (!empty($rMatches["tag"][$i])) {
                $rIndex++;
            } else {
                if (!empty($rMatches["prop_key"][$i])) {
                    $rResults[$rIndex][$rMatches["prop_key"][$i]] = trim($rMatches["prop_val"][$i]);
                } else {
                    if (!empty($rMatches["name"][$i])) {
                        $rResults[$rIndex]["name"] = trim(substr($rItem, 1));
                    } else {
                        if (!empty($rMatches["url"][$i])) {
                            $rResults[$rIndex]["url"] = str_replace(" ", "%20", trim($rItem));
                        }
                    }
                }
            }
        }
        foreach ($rResults as $rResult) {
            if (!in_array($rResult["url"], $rStreamDatabase)) {
                $rPathInfo = pathinfo($rResult["url"]);
                $rImportArray = ["stream_source" => [$rResult["url"]], "stream_icon" => $rResult["tvg-logo"] ?: "", "stream_display_name" => $rResult["name"] ?: "", "movie_propeties" => [], "async" => true, "target_container" => [$rPathInfo["extension"]]];
                $rImportStreams[] = $rImportArray;
            }
        }
    } else {
        if (!empty($_POST["import_folder"])) {
            if (!hasPermissions("adv", "import_movies")) {
                exit;
            }
            $rStreamDatabase = [];
            $result = $db->query("SELECT `stream_source` FROM `streams` WHERE `type` = 2;");
            if ($result && 0 < $result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    foreach (json_decode($row["stream_source"], true) as $rSource) {
                        if (0 < strlen($rSource)) {
                            $rStreamDatabase[] = $rSource;
                        }
                    }
                }
            }
            $rParts = explode(":", $_POST["import_folder"]);
            if (is_numeric($rParts[1])) {
                if (isset($_POST["scan_recursive"])) {
                    $rFiles = scanRecursive(intval($rParts[1]), $rParts[2], ["mp4", "mkv", "avi", "mpg", "flv"]);
                } else {
                    $rFiles = [];
                    foreach (listDir(intval($rParts[1]), rtrim($rParts[2], "/"), ["mp4", "mkv", "avi", "mpg", "flv"])["files"] as $rFile) {
                        $rFiles[] = rtrim($rParts[2], "/") . "/" . $rFile;
                    }
                }
                foreach ($rFiles as $rFile) {
                    $rFilePath = "s:" . intval($rParts[1]) . ":" . $rFile;
                    if (!in_array($rFilePath, $rStreamDatabase)) {
                        $rPathInfo = pathinfo($rFile);
                        $rImportArray = ["stream_source" => [$rFilePath], "stream_icon" => "", "stream_display_name" => $rPathInfo["filename"], "movie_propeties" => [], "async" => true, "target_container" => [$rPathInfo["extension"]]];
                        $rImportStreams[] = $rImportArray;
                    }
                }
            }
        } else {
            $rImportArray = ["stream_source" => [$_POST["stream_source"]], "stream_icon" => $rArray["stream_icon"], "stream_display_name" => $rArray["stream_display_name"], "movie_propeties" => [], "async" => false];
            if (0 < strlen($_POST["tmdb_id"])) {
                $rTMDBURL = "https://www.themoviedb.org/movie/" . $_POST["tmdb_id"];
            } else {
                $rTMDBURL = "";
            }
            if ($rAdminSettings["download_images"]) {
                $_POST["movie_image"] = downloadImage($_POST["movie_image"]);
                $_POST["backdrop_path"] = downloadImage($_POST["backdrop_path"]);
            }
            $rSeconds = intval($_POST["episode_run_time"]) * 60;
            $rImportArray["movie_propeties"] = ["tmdb_url" => $rTMDBURL, "tmdb_id" => $_POST["tmdb_id"], "name" => $rArray["stream_display_name"], "o_name" => $rArray["stream_display_name"], "cover_big" => $_POST["movie_image"], "movie_image" => $_POST["movie_image"], "releasedate" => $_POST["releasedate"], "episode_run_time" => $_POST["episode_run_time"], "youtube_trailer" => $_POST["youtube_trailer"], "director" => $_POST["director"], "actors" => $_POST["cast"], "cast" => $_POST["cast"], "description" => $_POST["plot"], "plot" => $_POST["plot"], "age" => "", "mpaa_rating" => "", "rating_count_kinopoisk" => 0, "country" => $_POST["country"], "genre" => $_POST["genre"], "backdrop_path" => [$_POST["backdrop_path"]], "duration_secs" => $rSeconds, "duration" => sprintf("%02d:%02d:%02d", $rSeconds / 3600, $rSeconds / 60 % 60, $rSeconds % 60), "video" => [], "audio" => [], "bitrate" => 0, "rating" => $_POST["rating"]];
            if (strlen($rImportArray["movie_propeties"]["backdrop_path"][0]) == 0) {
                unset($rImportArray["movie_propeties"]["backdrop_path"]);
            }
            if (isset($_POST["edit"])) {
                $rImportStreams[] = $rImportArray;
            } else {
                $rResult = $db->query("SELECT COUNT(`id`) AS `count` FROM `streams` WHERE `stream_display_name` = '" . ESC($rImportArray["stream_display_name"]) . "' AND `type` = 2;");
                if ($rResult->fetch_assoc()["count"] == 0) {
                    $rImportStreams[] = $rImportArray;
                } else {
                    $_STATUS = 2;
                    $rMovie = array_merge($rArray, $rImportArray);
                }
            }
        }
    }
    if (0 < count($rImportStreams)) {
        $rRestartIDs = [];
        foreach ($rImportStreams as $rImportStream) {
            $rImportArray = $rArray;
            foreach (array_keys($rImportStream) as $rKey) {
                $rImportArray[$rKey] = $rImportStream[$rKey];
            }
            $rImportArray["order"] = getNextOrder();
            $rSync = $rImportArray["async"];
            unset($rImportArray["async"]);
            $rCols = "`" . ESC(implode("`,`", array_keys($rImportArray))) . "`";
            $rValues = NULL;
            foreach (array_values($rImportArray) as $rValue) {
                isset($rValues);
                isset($rValues) ? $rValues .= "," : ($rValues = "");
                if (is_array($rValue)) {
                    $rValue = json_encode($rValue);
                }
                if (is_null($rValue)) {
                    $rValues .= "NULL";
                } else {
                    $rValues .= "'" . ESC($rValue) . "'";
                }
            }
            if (isset($_POST["edit"])) {
                $rCols = "`id`," . $rCols;
                $rValues = ESC($_POST["edit"]) . "," . $rValues;
            }
            $rQuery = "REPLACE INTO `streams`(" . $rCols . ") VALUES(" . $rValues . ");";
            if ($db->query($rQuery)) {
                if (isset($_POST["edit"])) {
                    $rInsertID = intval($_POST["edit"]);
                } else {
                    $rInsertID = $db->insert_id;
                }
                $rStreamExists = [];
                if (isset($_POST["edit"])) {
                    $result = $db->query("SELECT `server_stream_id`, `server_id` FROM `streams_sys` WHERE `stream_id` = " . intval($rInsertID) . ";");
                    if ($result && 0 < $result->num_rows) {
                        while ($row = $result->fetch_assoc()) {
                            $rStreamExists[intval($row["server_id"])] = intval($row["server_stream_id"]);
                        }
                    }
                }
                if (isset($_POST["server_tree_data"])) {
                    $rStreamsAdded = [];
                    $rServerTree = json_decode($_POST["server_tree_data"], true);
                    foreach ($rServerTree as $rServer) {
                        if ($rServer["parent"] != "#") {
                            $rServerID = intval($rServer["id"]);
                            $rStreamsAdded[] = $rServerID;
                            if ($rServer["parent"] == "source") {
                                $rParent = "NULL";
                            } else {
                                $rParent = intval($rServer["parent"]);
                            }
                            if (isset($rStreamExists[$rServerID])) {
                                $db->query("UPDATE `streams_sys` SET `parent_id` = " . $rParent . ", `on_demand` = 0 WHERE `server_stream_id` = " . $rStreamExists[$rServerID] . ";");
                            } else {
                                $db->query("INSERT INTO `streams_sys`(`stream_id`, `server_id`, `parent_id`, `on_demand`) VALUES(" . intval($rInsertID) . ", " . $rServerID . ", " . $rParent . ", 0);");
                            }
                        }
                    }
                    foreach ($rStreamExists as $rServerID => $rDBID) {
                        if (!in_array($rServerID, $rStreamsAdded)) {
                            $db->query("DELETE FROM `streams_sys` WHERE `server_stream_id` = " . $rDBID . ";");
                        }
                    }
                }
                if ($rRestart) {
                    $rRestartIDs[] = $rInsertID;
                }
                foreach ($rBouquets as $rBouquet) {
                    addToBouquet("stream", $rBouquet, $rInsertID);
                }
                foreach (getBouquets() as $rBouquet) {
                    if (!in_array($rBouquet["id"], $rBouquets)) {
                        removeFromBouquet("stream", $rBouquet["id"], $rInsertID);
                    }
                }
                if ($rSync) {
                    $db->query("INSERT INTO `tmdb_async`(`type`, `stream_id`) VALUES(1, " . intval($rInsertID) . ");");
                }
            }
        }
        scanBouquets();
        if ($rRestart) {
            APIRequest(["action" => "vod", "sub" => "start", "stream_ids" => $rRestartIDs]);
        }
        if (isset($_FILES["m3u_file"])) {
            header("Location: ./movies.php?successedit");
            exit;
        }
        if (!isset($_GET["id"])) {
            header("Location: ./movie.php?successedit&id=" . $rInsertID);
            exit;
        }
    } else {
        if (!isset($_STATUS)) {
            $_STATUS = 3;
            $rMovie = $rArray;
        }
    }
}
$rServerTree = [];
$rServerTree[] = ["id" => "source", "parent" => "#", "text" => "<strong>" . $_["stream_source"] . "</strong>", "icon" => "mdi mdi-youtube-tv", "state" => ["opened" => true]];
if (isset($_GET["id"])) {
    if (isset($_GET["import"]) || !hasPermissions("adv", "edit_movie")) {
        exit;
    }
    $rMovie = getStream($_GET["id"]);
    if (!$rMovie || $rMovie["type"] != 2) {
        exit;
    }
    $rMovie["properties"] = json_decode($rMovie["movie_propeties"], true);
    $rStreamSys = getStreamSys($_GET["id"]);
    foreach ($rServers as $rServer) {
        if (isset($rStreamSys[intval($rServer["id"])])) {
            if ($rStreamSys[intval($rServer["id"])]["parent_id"] != 0) {
                $rParent = intval($rStreamSys[intval($rServer["id"])]["parent_id"]);
            } else {
                $rParent = "source";
            }
        } else {
            $rParent = "#";
        }
        $rServerTree[] = ["id" => $rServer["id"], "parent" => $rParent, "text" => $rServer["server_name"], "icon" => "mdi mdi-server-network", "state" => ["opened" => true]];
    }
} else {
    if (!hasPermissions("adv", "add_movie")) {
        exit;
    }
    foreach ($rServers as $rServer) {
        $rServerTree[] = ["id" => $rServer["id"], "parent" => "#", "text" => $rServer["server_name"], "icon" => "mdi mdi-server-network", "state" => ["opened" => true]];
    }
}
if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
if (isset($_GET["successedit"])) {
    $_STATUS = 0;
}
if ($rSettings["sidebar"]) {
    echo "        <div class=\"content-page\"><div class=\"content boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
} else {
    echo "        <div class=\"wrapper boxed-layout-ext\"><div class=\"container-fluid\">\n        ";
}
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n                                    <li>\n                                        <a href=\"./movies.php";
if (isset($_GET["category"])) {
    echo "?category=" . $_GET["category"];
}
echo "\">\n                                            <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\">\n                                                ";
echo $_["view_movies"];
echo "                                            </button>\n                                        </a>\n                                        <a href=\"./movie.php?id=";
echo $_GET["id"];
echo "\">\n                                            <button type=\"button\" class=\"btn btn-info waves-effect waves-light btn-sm\">\n                                                Edit Movie\n                                            </button>\n                                        </a>\n                                    </li>\n                                </ol>\n                            </div>\n\t\t\t\t\t\t\t<h4 class=\"page-title\">";
if (isset($rMovie["id"])) {
    echo $rMovie["stream_display_name"] . " &nbsp;<button type=\"button\" class=\"btn btn-outline-info waves-effect waves-light btn-xs\" onClick=\"player(" . $rMovie["id"] . ", '" . json_decode($rMovie["target_container"], true)[0] . "');\"><i class=\"mdi mdi-play\"></i></button>";
} else {
    if (isset($_GET["import"])) {
        echo $_["import_movies"];
    } else {
        echo $_["add_movie"];
    }
}
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["movies_info_1"];
        echo "                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["movies_info_1"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && $_STATUS == 1) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["movies_info_2"];
            echo "                        </div>\n\t\t\t\t\t\t";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["movies_info_2"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    } else {
        if (isset($_STATUS) && $_STATUS == 2) {
            if (!$rSettings["sucessedit"]) {
                echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                echo $_["movies_info_3"];
                echo "                        </div>\n\t\t\t\t\t\t";
            } else {
                echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                echo $_["movies_info_3"];
                echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
            }
        } else {
            if (isset($_STATUS) && $_STATUS == 3) {
                if (!$rSettings["sucessedit"]) {
                    echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
                    echo $_["movies_info_4"];
                    echo "                        </div>\n                        ";
                } else {
                    echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
                    echo $_["movies_info_4"];
                    echo "', \"warning\");\n  \t\t\t\t\t</script>\n\t\t\t\t\t";
                    $rEncodeErrors = getEncodeErrors($rMovie["id"]);
                    foreach ($rEncodeErrors as $rServerID => $rEncodeError) {
                        echo "                        <div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            <strong>";
                        echo $_["error_on_server"];
                        echo " - ";
                        echo $rServers[$rServerID]["server_name"];
                        echo "</strong><br/>\n                            ";
                        echo str_replace("\n", "<br/>", $rEncodeError);
                        echo "                        </div>\n                        ";
                    }
                }
            }
        }
    }
}
echo "\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t    <div class=\"col-xl-12\">\n                                 <img class=\"card-img-top img-fluid\" src=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["backdrop_path"][0]);
}
echo "\">\n                            </div>                                    \n                        </div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class=\"card-box\">\n\t\t\t\t\t\t    <ul class=\"nav nav-tabs nav-bordered nav-justified\">\n\t\t\t\t\t\t\t   <li class=\"nav-item\">\n\t\t\t\t\t\t\t      <a href=\"#servers\" data-toggle=\"tab\" aria-expanded=\"true\" class=\"nav-link active\">Active Servers</a>\n\t\t\t\t\t\t\t   </li>\n\t\t\t\t\t\t\t   <li class=\"nav-item\">\n\t\t\t\t\t\t\t      <a href=\"#information\" data-toggle=\"tab\" aria-expanded=\"false\" class=\"nav-link\">";
echo $_["information"];
echo "</a>\n\t\t\t\t\t\t\t   </li>\n                             </ul>\n                        \t\t\t\t\t\t\t \n\t\t\t\t\t\t\n                        \n                        <div class=\"tab-content\">\n                            <div class=\"tab-pane active card text-xs-center\" id=\"servers\">\n                                <div class=\"table\">\n                                    <table id=\"datatable-list\" class=\"table table-borderless mb-0\">\n\t\t\t\t\t\t\t\t\t  <thead class=\"bg-light\">\n                                        <tr>\n                                            <th></th>\n                                            <th></th>\n\t\t\t\t\t\t\t\t\t\t\t<th></th>\n                                            <th>";
echo $_["server"];
echo "</th>\n                                            <th>";
echo $_["clients"];
echo "</th>\n                                            <th>";
echo $_["status"];
echo "</th>\n                                            <th>";
echo $_["actions"];
echo "</th>  \n                                            <th></th>\n                                        </tr>\n                                      </thead>\n                                    <tbody>\n                                    </tbody>\n                                  </table>\n                               </div>\n                           </div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t   <div class=\"tab-pane\" id=\"information\">\n\t\t\t\t\t\t       <div class=\"col-12 input\">\n\t\t\t\t\t\t\t       <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t       <label class=\"col-md-2 col-form\" for=\"plot\">";
echo $_["plot"];
echo "</label>\n\t\t\t\t\t\t\t\t\t   <div class=\"col-md-10\">\n\t\t\t\t\t\t\t\t\t       <textarea readonly=\"\" rows=\"6\" class=\"form-control\" id=\"plot\" name=\"plot\">";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["plot"]);
}
echo "</textarea>\n                                       </div>\n\t\t\t\t\t\t\t       </div>\n                                   <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t       <label class=\"col-md-2 col-form-label\" for=\"cast\">";
echo $_["cast"];
echo "</label>\n\t\t\t\t\t\t\t\t\t   <div class=\"col-md-10\">\n\t\t\t\t\t\t\t\t\t       <input readonly=\"\" type=\"text\" class=\"form-control\" id=\"cast\" name=\"cast\" value=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["cast"]);
}
echo "\">\n                                       </div>\n\t\t\t\t\t\t\t\t   </div>\t\n                                   <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t        <label class=\"col-md-2 col-form-label\" for=\"director\">";
echo $_["director"];
echo "</label>\n\t\t\t\t\t\t\t\t\t    <div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t       <input readonly=\"\" type=\"text\" class=\"form-control\" id=\"director\" name=\"director\" value=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["director"]);
}
echo "\">\n                                        </div>\n\t\t\t\t\t\t\t\t        <label class=\"col-md-2 col-form-label\" for=\"genre\">";
echo $_["genres"];
echo "</label>\n\t\t\t\t\t\t\t\t\t    <div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t       <input readonly=\"\" type=\"text\" class=\"form-control\" id=\"genre\" name=\"genre\" value=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["genre"]);
}
echo "\">\n                                        </div>\n\t\t\t\t\t\t\t\t   </div>\n\t\t\t\t\t\t\t\t   <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t        <label class=\"col-md-2 col-form-label\" for=\"release_date\">";
echo $_["release_date"];
echo "</label>\n\t\t\t\t\t\t\t\t\t    <div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t       <input readonly=\"\" type=\"text\" class=\"form-control\" id=\"release_date\" name=\"release_date\" value=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["releasedate"]);
}
echo "\">\n                                        </div>\n\t\t\t\t\t\t\t\t        <label class=\"col-md-2 col-form-label\" for=\"episode_run_time\">";
echo $_["runtime"];
echo "</label>\n\t\t\t\t\t\t\t\t\t    <div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t       <input readonly=\"\" type=\"text\" class=\"form-control\" id=\"episode_run_time\" name=\"episode_run_time\" value=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["episode_run_time"]);
}
echo " min\">\n                                        </div>\n\t\t\t\t\t\t\t\t   </div>\n\t\t\t\t\t\t\t\t   <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t        <label class=\"col-md-2 col-form-label\" for=\"youtube_trailer\">";
echo $_["youtube_trailer"];
echo "</label>\n\t\t\t\t\t\t\t\t\t    <div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t       <input readonly=\"\" type=\"text\" class=\"form-control\" id=\"youtube_trailer\" name=\"youtube_trailer\" value=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["youtube_trailer"]);
}
echo "\">\n                                        </div>\n\t\t\t\t\t\t\t\t        <label class=\"col-md-2 col-form-label\" for=\"rating\">";
echo $_["rating"];
echo "</label>\n\t\t\t\t\t\t\t\t\t    <div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t       <input readonly=\"\" type=\"text\" class=\"form-control\" id=\"rating\" name=\"rating\" value=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["rating"]);
}
echo "\">\n                                        </div>\n\t\t\t\t\t\t\t\t   </div>\n\t\t\t\t\t\t\t\t   <div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t       <label class=\"col-md-2 col-form-label\" for=\"country\">";
echo $_["country"];
echo "</label>\n\t\t\t\t\t\t\t\t\t   <div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t       <input readonly=\"\" type=\"text\" class=\"form-control\" id=\"country\" name=\"country\" value=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["country"]);
}
echo "\">\n                                       </div>\n\t\t\t\t\t\t\t\t\t   <label class=\"col-md-2 col-form-label\" for=\"tmdbid\">";
echo $_["tmdb_id"];
echo "</label>\n\t\t\t\t\t\t\t\t\t   <div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t\t   <input readonly=\"\" type=\"text\" class=\"form-control\" id=\"tmdbid\" name=\"tmdbid\" value=\"";
if (isset($rMovie)) {
    echo htmlspecialchars($rMovie["properties"]["tmdb_id"]);
}
echo "\">\n                                       </div>\n\t\t\t\t\t\t\t\t   </div>\n\t\t\t\t\t\t \t   </div>\t\n                            </div>\n\t\t\t\t\t\t\t\n                      \n                                    </div> <!-- end col -->\n                                </div>\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var changeTitle = false;\n        var rSwitches = [];\n        \n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        \n        function api(rID, rServerID, rType) {\n            if (rType == \"delete\") {\n                if (confirm('";
echo $_["movie_delete_confirm"];
echo "') == false) {\n                    return;\n                }\n            }\n            \$.getJSON(\"./api.php?action=movie&sub=\" + rType + \"&stream_id=\" + rID + \"&server_id=\" + rServerID, function(data) {\n                if (data.result == true) {\n                    if (rType == \"start\") {\n                        \$.toast(\"";
echo $_["movie_encode_started"];
echo "\");\n                    } else if (rType == \"stop\") {\n                        \$.toast(\"";
echo $_["movie_encode_stopped"];
echo "\");\n                    } else if (rType == \"delete\") {\n                        \$(\"#movie-\" + rID + \"-\" + rServerID).remove();\n                        \$.toast(\"";
echo $_["movie_delete_confirmed"];
echo "\");\n                    }\n                    \$.each(\$('.tooltip'), function (index, element) {\n                        \$(this).remove();\n                    });\n                    \$(\"#datatable-list\").DataTable().ajax.reload( null, false );\n                } else {\n                    \$.toast(\"";
echo $_["error_occured"];
echo "\");\n                }\n            }).fail(function() {\n                \$.toast(\"";
echo $_["error_occured"];
echo "\");\n            });\n        }\n        function selectDirectory(elem) {\n            window.currentDirectory += elem + \"/\";\n            \$(\"#current_path\").val(window.currentDirectory);\n            \$(\"#changeDir\").click();\n        }\n        function selectParent() {\n            \$(\"#current_path\").val(window.currentDirectory.split(\"/\").slice(0,-2).join(\"/\") + \"/\");\n            \$(\"#changeDir\").click();\n        }\n        function selectFile(rFile) {\n            if (\$('li.nav-item .active').attr('href') == \"#stream-details\") {\n                \$(\"#stream_source\").val(\"s:\" + \$(\"#server_id\").val() + \":\" + window.currentDirectory + rFile);\n                var rExtension = rFile.substr((rFile.lastIndexOf('.')+1));\n                if (\$(\"#target_container option[value='\" + rExtension + \"']\").length > 0) {\n                    \$(\"#target_container\").val(rExtension).trigger('change');\n                }\n            } else {\n                \$(\"#movie_subtitles\").val(\"s:\" + \$(\"#server_id\").val() + \":\" + window.currentDirectory + rFile);\n            }\n            \$.magnificPopup.close();\n        }\n        function openImage(elem) {\n            rPath = \$(elem).parent().parent().find(\"input\").val();\n            if (rPath.length > 0) {\n                if (rPath.substring(0,1) == \".\") {\n                    window.open('";
echo getURL();
echo "' + rPath.substring(1, rPath.length));\n                } else if (rPath.substring(0,1) == \"/\") {\n                    window.open('";
echo getURL();
echo "' + rPath);\n                } else {\n                    window.open(rPath);\n                }\n            }\n        }\n        function reloadStream() {\n            \$(\"#datatable-list\").DataTable().ajax.reload( null, false );\n            setTimeout(reloadStream, 5000);\n        }\n        function clearSearch() {\n            \$(\"#search\").val(\"\");\n            \$(\"#doSearch\").click();\n        }\n        function player(rID, rContainer) {\n            \$.magnificPopup.open({\n                items: {\n                    src: \"./player.php?type=movie&id=\" + rID + \"&container=\" + rContainer,\n                    type: 'iframe'\n                }\n            });\n        }\n        function setSwitch(switchElement, checkedBool) {\n            if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {\n                switchElement.setPosition(true);\n                switchElement.handleOnchange(true);\n            }\n        }\n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'});\n            \n            \$(\"#datatable\").DataTable({\n                responsive: false,\n                paging: false,\n                bInfo: false,\n                searching: false,\n                scrollY: \"250px\",\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0]},\n                ],\n                \"language\": {\n                    \"emptyTable\": \"\"\n                }\n            });\n            \n            \$(\"#datatable-files\").DataTable({\n                responsive: false,\n                paging: false,\n                bInfo: false,\n                searching: true,\n                scrollY: \"250px\",\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0]},\n                ],\n                \"language\": {\n                    \"emptyTable\": \"";
echo $_["no_compatible_file"];
echo "\"\n                }\n            });\n            \n            \$(\"#doSearch\").click(function() {\n                \$('#datatable-files').DataTable().search(\$(\"#search\").val()).draw();\n            })\n            \n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n              var switchery = new Switchery(html);\n              window.rSwitches[\$(html).attr(\"id\")] = switchery;\n            });\n            \n            \$(\"#select_folder\").click(function() {\n                \$(\"#import_folder\").val(\"s:\" + \$(\"#server_id\").val() + \":\" + window.currentDirectory);\n                \$.magnificPopup.close();\n            });\n            \n            \$(\"#changeDir\").click(function() {\n                \$(\"#search\").val(\"\");\n                window.currentDirectory = \$(\"#current_path\").val();\n                if (window.currentDirectory.substr(-1) != \"/\") {\n                    window.currentDirectory += \"/\";\n                }\n                \$(\"#current_path\").val(window.currentDirectory);\n                \$(\"#datatable\").DataTable().clear();\n                \$(\"#datatable\").DataTable().row.add([\"\", \"";
echo $_["loading"];
echo "...\"]);\n                \$(\"#datatable\").DataTable().draw(true);\n                \$(\"#datatable-files\").DataTable().clear();\n                \$(\"#datatable-files\").DataTable().row.add([\"\", \"";
echo $_["please_wait"];
echo "...\"]);\n                \$(\"#datatable-files\").DataTable().draw(true);\n                if (\$('li.nav-item .active').attr('href') == \"#stream-details\") {\n                    rFilter = \"video\";\n                } else {\n                    rFilter = \"subs\";\n                }\n                \$.getJSON(\"./api.php?action=listdir&dir=\" + window.currentDirectory + \"&server=\" + \$(\"#server_id\").val() + \"&filter=\" + rFilter, function(data) {\n                    \$(\"#datatable\").DataTable().clear();\n                    \$(\"#datatable-files\").DataTable().clear();\n                    if (window.currentDirectory != \"/\") {\n                        \$(\"#datatable\").DataTable().row.add([\"<i class='mdi mdi-subdirectory-arrow-left'></i>\", \"";
echo $_["parent_directory"];
echo "\"]);\n                    }\n                    if (data.result == true) {\n                        \$(data.data.dirs).each(function(id, dir) {\n                            \$(\"#datatable\").DataTable().row.add([\"<i class='mdi mdi-folder-open-outline'></i>\", dir]);\n                        });\n                        \$(\"#datatable\").DataTable().draw(true);\n                        \$(data.data.files).each(function(id, dir) {\n                            \$(\"#datatable-files\").DataTable().row.add([\"<i class='mdi mdi-file-video'></i>\", dir]);\n                        });\n                        \$(\"#datatable-files\").DataTable().draw(true);\n                    }\n                });\n            });\n            \n            \$('#datatable').on('click', 'tbody > tr', function() {\n                if (\$(this).find(\"td\").eq(1).html() == \"";
echo $_["parent_directory"];
echo "\") {\n                    selectParent();\n                } else {\n                    selectDirectory(\$(this).find(\"td\").eq(1).html());\n                }\n            });\n            \$('#datatable-files').on('click', 'tbody > tr', function() {\n                selectFile(\$(this).find(\"td\").eq(1).html());\n            });\n            \n            \$('#server_tree').jstree({ 'core' : {\n                'check_callback': function (op, node, parent, position, more) {\n                    switch (op) {\n                        case 'move_node':\n                            if (node.id == \"source\") { return false; }\n                            return true;\n                    }\n                },\n                'data' : ";
echo json_encode($rServerTree);
echo "            }, \"plugins\" : [ \"dnd\" ]\n            });\n            \n            \$(\"#stream_form\").submit(function(e){\n                ";
if (!isset($_GET["import"])) {
    echo "                if (\$(\"#stream_display_name\").val().length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
    echo $_["enter_movie_name"];
    echo "\");\n                }\n                if (\$(\"#stream_source\").val().length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
    echo $_["enter_movie_source"];
    echo "\");\n                }\n                ";
} else {
    echo "                if ((\$(\"#m3u_file\").val().length == 0) && (\$(\"#import_folder\").val().length == 0)) {\n                    e.preventDefault();\n                    \$.toast(\"";
    echo $_["select_m3u_file"];
    echo "\");\n                }\n                ";
}
echo "                \$(\"#server_tree_data\").val(JSON.stringify(\$('#server_tree').jstree(true).get_json('#', {flat:true})));\n            });\n            \n            \$(\"#filebrowser\").magnificPopup({\n                type: 'inline',\n                preloader: false,\n                focus: '#server_id',\n                callbacks: {\n                    beforeOpen: function() {\n                        if (\$(window).width() < 830) {\n                            this.st.focus = false;\n                        } else {\n                            this.st.focus = '#server_id';\n                        }\n                    }\n                }\n            });\n            \$(\"#filebrowser-sub\").magnificPopup({\n                type: 'inline',\n                preloader: false,\n                focus: '#server_id',\n                callbacks: {\n                    beforeOpen: function() {\n                        if (\$(window).width() < 830) {\n                            this.st.focus = false;\n                        } else {\n                            this.st.focus = '#server_id';\n                        }\n                    }\n                }\n            });\n            \n            \$(\"#filebrowser\").on(\"mfpOpen\", function() {\n                clearSearch();\n                \$(\$.fn.dataTable.tables(true)).css('width', '100%');\n                \$(\$.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();\n            });\n            \$(\"#filebrowser-sub\").on(\"mfpOpen\", function() {\n                clearSearch();\n                \$(\$.fn.dataTable.tables(true)).css('width', '100%');\n                \$(\$.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();\n            });\n            \n            \$(document).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"#server_id\").change(function() {\n                \$(\"#current_path\").val(\"/\");\n                \$(\"#changeDir\").click();\n            });\n            \n            \$(\"#direct_source\").change(function() {\n                evaluateDirectSource();\n            });\n            \$(\"#movie_symlink\").change(function() {\n                evaluateSymlink();\n            });\n            \n            function evaluateDirectSource() {\n                \$([\"movie_symlink\", \"read_native\", \"transcode_profile_id\", \"target_container\", \"remove_subtitles\", \"movie_subtitles\"]).each(function(rID, rElement) {\n                    if (\$(rElement)) {\n                        if (\$(\"#direct_source\").is(\":checked\")) {\n\t\t\t\t\t\t\t\$(\"#redirect_stream_div\").show();\n                            if (window.rSwitches[rElement]) {\n                                setSwitch(window.rSwitches[rElement], false);\n                                window.rSwitches[rElement].disable();\n                            } else {\n                                \$(\"#\" + rElement).prop(\"disabled\", true);\n                            }\n                        } else {\n\t\t\t\t\t\t\t\$(\"#redirect_stream_div\").hide();\n                            if (window.rSwitches[rElement]) {\n                                window.rSwitches[rElement].enable();\n                            } else {\n                                \$(\"#\" + rElement).prop(\"disabled\", false);\n                            }\n                        }\n                    }\n                });\n            }\n            function evaluateSymlink() {\n                \$([\"direct_source\", \"read_native\", \"transcode_profile_id\"]).each(function(rID, rElement) {\n                    if (\$(rElement)) {\n                        if (\$(\"#movie_symlink\").is(\":checked\")) {\n                            if (window.rSwitches[rElement]) {\n                                setSwitch(window.rSwitches[rElement], false);\n                                window.rSwitches[rElement].disable();\n                            } else {\n                                \$(\"#\" + rElement).prop(\"disabled\", true);\n                            }\n                        } else {\n                            if (window.rSwitches[rElement]) {\n                                window.rSwitches[rElement].enable();\n                            } else {\n                                \$(\"#\" + rElement).prop(\"disabled\", false);\n                            }\n                        }\n                    }\n                });\n            }\n            \n            \$(\"#stream_display_name\").change(function() {\n                if (!window.changeTitle) {\n                    \$(\"#tmdb_search\").empty().trigger('change');\n                    if (\$(\"#stream_display_name\").val().length > 0) {\n                        \$.getJSON(\"./api.php?action=tmdb_search&type=movie&term=\" + \$(\"#stream_display_name\").val(), function(data) {\n                            if (data.result == true) {\n                                if (data.data.length > 0) {\n                                    //newOption = new Option(\"";
echo $_["found_results"];
echo "\".replace('{num}', data.data.length), -1, true, true);\n\t\t\t\t\t\t\t\t\tnewOption = new Option(\"";
echo $_["found_"];
echo "\" + data.data.length + \"";
echo $_["_results"];
echo "\", -1, true, true);\n                                } else {\n                                    newOption = new Option(\"";
echo $_["no_results_found"];
echo "\", -1, true, true);\n                                }\n                                \$(\"#tmdb_search\").append(newOption).trigger('change');\n                                \$(data.data).each(function(id, item) {\n                                    if (item.release_date.length > 0) {\n                                        rTitle = item.title + \" (\" + item.release_date.substring(0, 4) + \")\";\n                                    } else {\n                                        rTitle = item.title;\n                                    }\n                                    newOption = new Option(rTitle, item.id, true, true);\n                                    \$(\"#tmdb_search\").append(newOption);\n                                });\n                            } else {\n                                newOption = new Option(\"";
echo $_["no_results_found"];
echo "\", -1, true, true);\n                            }\n                            \$(\"#tmdb_search\").val(-1).trigger('change');\n                        });\n                    }\n                } else {\n                    window.changeTitle = false;\n                }\n            });\n            \$(\"#tmdb_search\").change(function() {\n                if ((\$(\"#tmdb_search\").val()) && (\$(\"#tmdb_search\").val() > -1)) {\n                    \$.getJSON(\"./api.php?action=tmdb&type=movie&id=\" + \$(\"#tmdb_search\").val(), function(data) {\n                        if (data.result == true) {\n                            window.changeTitle = true;\n                            rTitle = data.data.title;\n                            if (data.data.release_date) {\n                                rTitle += \" (\" + data.data.release_date.substr(0, 4) + \")\";\n                            }\n                            \$(\"#stream_display_name\").val(rTitle);\n                            \$(\"#movie_image\").val(\"\");\n                            if (data.data.poster_path.length > 0) {\n                                \$(\"#movie_image\").val(\"https://image.tmdb.org/t/p/w600_and_h900_bestv2\" + data.data.poster_path);\n                            }\n                            \$(\"#backdrop_path\").val(\"\");\n                            if (data.data.backdrop_path.length > 0) {\n                                \$(\"#backdrop_path\").val(\"https://image.tmdb.org/t/p/w1280\" + data.data.backdrop_path);\n                            }\n                            \$(\"#releasedate\").val(data.data.release_date);\n                            \$(\"#episode_run_time\").val(data.data.runtime);\n                            \$(\"#youtube_trailer\").val(\"\");\n                            if (data.data.trailer) {\n                                \$(\"#youtube_trailer\").val(data.data.trailer);\n                            }\n                            rCast = \"\";\n                            rMemberID = 0;\n                            \$(data.data.credits.cast).each(function(id, member) {\n                                rMemberID += 1;\n                                if (rMemberID <= 5) {\n                                    if (rCast.length > 0) {\n                                        rCast += \", \";\n                                    }\n                                    rCast += member.name;\n                                }\n                            });\n                            \$(\"#cast\").val(rCast);\n                            rGenres = \"\";\n                            rGenreID = 0;\n                            \$(data.data.genres).each(function(id, genre) {\n                                rGenreID += 1;\n                                if (rGenreID <= 3) {\n                                    if (rGenres.length > 0) {\n                                        rGenres += \", \";\n                                    }\n                                    rGenres += genre.name;\n                                }\n                            });\n                            \$(\"#genre\").val(rGenres);\n                            \$(\"#director\").val(\"\");\n                            \$(data.data.credits.crew).each(function(id, member) {\n                                if (member.department == \"Directing\") {\n                                    \$(\"#director\").val(member.name);\n                                    return true;\n                                }\n                            });\n                            \$(\"#country\").val(\"\");\n                            \$(\"#plot\").val(data.data.overview);\n                            if (data.data.production_countries.length > 0) {\n                                \$(\"#country\").val(data.data.production_countries[0].name);\n                            }\n                            \$(\"#rating\").val(data.data.vote_average);\n                            \$(\"#tmdb_id\").val(data.data.id);\n                        }\n                    });\n                }\n            });\n            \n            ";
if (isset($rMovie["id"])) {
    echo "            \$(\"#datatable-list\").DataTable({\n                ordering: false,\n                paging: false,\n                searching: false,\n                processing: true,\n                serverSide: true,\n                bInfo: false,\n                ajax: {\n                    url: \"./table_search.php\",\n                    \"data\": function(d) {\n                        d.id = \"movies\";\n                        d.stream_id = ";
    echo $rMovie["id"];
    echo ";\n                    }\n                },\n                columnDefs: [\n                    {\"className\": \"dt-center\", \"targets\": [0,1,2,3,4,5,6]},\n                    {\"visible\": false, \"targets\": [0,1,2,7,8]}\n                ],\n            });\n            setTimeout(reloadStream, 5000);\n            \$(\"#stream_display_name\").trigger('change');\n            ";
}
echo "            \n            \$(\"#import_type_1\").click(function() {\n                \$(\"#import_m3uf_toggle\").show();\n                \$(\"#import_folder_toggle\").hide();\n            });\n            \$(\"#import_type_2\").click(function() {\n                \$(\"#import_m3uf_toggle\").hide();\n                \$(\"#import_folder_toggle\").show();\n            });\n            \n            \$(\"#runtime\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n            \n            \$(\"#changeDir\").click();\n            evaluateDirectSource();\n            evaluateSymlink();\n        });\n        </script>\n    </body>\n</html>";

?>