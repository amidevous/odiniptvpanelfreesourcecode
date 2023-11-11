<?php
include "/home/xtreamcodes/iptv_xtream_codes/admin/functions.php";
include "/home/xtreamcodes/iptv_xtream_codes/admin/tmdb.php";
include "/home/xtreamcodes/iptv_xtream_codes/admin/tmdb_release.php";

$rAdminSettings = getAdminSettings();
$rSettings = getSettings();
$rServers = getStreamingServers();
$rWatchCategories = Array(1 => getWatchCategories(1), 2 => getWatchCategories(2));

$rResult = $db->query("SELECT * FROM `watch_settings`;");
if (($rResult) && ($rResult->num_rows == 1)) {
    $rWatchSettings = $rResult->fetch_assoc();
}

$rPID = getmypid();
if (isset($rAdminSettings["watch_pid"])) {
	if ((file_exists("/proc/".$rAdminSettings["watch_pid"])) && (strlen($rAdminSettings["watch_pid"]) > 0)) {
        exit;
    } else {
        $db->query("UPDATE `admin_settings` SET `value` = ".intval($rPID)." WHERE `type` = 'watch_pid';");
    }
} else {
    $db->query("INSERT INTO `admin_settings`(`type`, `value`) VALUES('watch_pid', ".intval($rPID).");");
}

$rTimeout = 3000;       // Limit by time.
$rScanOffset = intval($rWatchSettings["scan_seconds"]) ?: 3600;

set_time_limit($rTimeout);
ini_set('max_execution_time', $rTimeout);

if (strlen($rSettings["tmdb_api_key"]) == 0) { exit; }

if (strlen($rAdminSettings["tmdb_language"]) > 0) {
    $rTMDB = new TMDB($rSettings["tmdb_api_key"], $rAdminSettings["tmdb_language"]);
} else {
    $rTMDB = new TMDB($rSettings["tmdb_api_key"]);
}

if ($rAdminSettings["local_api"]) {
    $rAPI = "http://127.0.0.1:".$rServers[$_INFO["server_id"]]["http_broadcast_port"]."/api.php";
} else {
    $rAPI = "http://".$rServers[$_INFO["server_id"]]["server_ip"].":".$rServers[$_INFO["server_id"]]["http_broadcast_port"]."/api.php";
}

$rStreamDatabase = Array();
$result = $db->query("SELECT `stream_source` FROM `streams` WHERE `type` IN (2,5);");
if (($result) && ($result->num_rows > 0)) {
    while ($row = $result->fetch_assoc()) {
        foreach (json_decode($row["stream_source"], True) as $rSource) {
            if (strlen($rSource) > 0) {
                $rStreamDatabase[] = $rSource;
            }
        }
    }
}

$rChanged = False;
$rUpdateSeries = Array();
$rArray = Array("movie_symlink" => 0, "type" => 0, "target_container" => Array("mp4"), "added" => time(), "read_native" => 0, "stream_all" => 0, "redirect_stream" => 1, "direct_source" => 0, "gen_timestamps" => 1, "transcode_attributes" => Array(), "stream_display_name" => "", "stream_source" => Array(), "movie_subtitles" => Array(), "category_id" => 0, "stream_icon" => "", "notes" => "", "custom_sid" => "", "custom_ffmpeg" => "", "transcode_profile_id" => 0, "enable_transcode" => 0, "auto_restart" => "[]", "allow_record" => 0, "rtmp_output" => 0, "epg_id" => null, "channel_id" => null, "epg_lang" => null, "tv_archive_server_id" => 0, "tv_archive_duration" => 0, "delay_minutes" => 0, "external_push" => Array(), "probesize_ondemand" => 128000);
$rResult = $db->query("SELECT * FROM `watch_folders` WHERE `active` = 1 AND UNIX_TIMESTAMP() - `last_run` > ".intval($rScanOffset)." ORDER BY `id` ASC;");
if (($rResult) && ($rResult->num_rows > 0)) {
    while ($rRow = $rResult->fetch_assoc()) {
        $rArray["type"] = Array("movie" => 2, "series" => 5)[$rRow["type"]];
        $db->query("UPDATE `watch_folders` SET `last_run` = UNIX_TIMESTAMP() WHERE `id` = ".intval($rRow["id"]).";");
        $rImportStreams = Array();
        $rExtensions = json_decode($rRow["allowed_extensions"], True);
        if (!$rExtensions) {
            $rExtensions = Array();
        }
        if (count($rExtensions) == 0) {
            $rExtensions = Array("mp4", "mkv", "avi", "mpg", "flv");
        }
        $rFiles = scanRecursive(intval($rRow["server_id"]), $rRow["directory"], $rExtensions); // Only these containers are accepted.
        if (isset($rRow["auto_subtitles"])) {
            $rSubtitles = scanRecursive(intval($rRow["server_id"]), $rRow["directory"], Array("srt", "sub", "sbv"));
        }
        foreach ($rFiles as $rFile) {
            $rFilePath = "s:".intval($rRow["server_id"]).":".$rFile;
            if (!in_array($rFilePath, $rStreamDatabase)) {
                $rPathInfo = pathinfo($rFile);
                $rImportArray = Array("file" => $rFile, "stream_source" => Array($rFilePath), "stream_icon" => "", "stream_display_name" => $rPathInfo["filename"], "movie_propeties" => Array(), "target_container" => Array($rPathInfo["extension"]));
                $rImportStreams[] = $rImportArray;
            }
        }
        foreach ($rImportStreams as $rImportStream) {
            $rImportArray = $rArray;
            $rFile = $rImportStream["file"]; unset($rImportStream["file"]);
            foreach (array_keys($rImportStream) as $rKey) {
                $rImportArray[$rKey] = $rImportStream[$rKey];
            }
            $db->query("DELETE FROM `watch_output` WHERE `filename` = '".$db->real_escape_string($rFile)."' AND `type` = ".(Array("movie" => 1, "series" => 2)[$rRow["type"]]).";");
            if ((!$rWatchSettings["ffprobe_input"]) OR (isset(checkSource($rRow["server_id"], $rFile)["streams"]))) {
                $rFilename = pathinfo($rFile)["filename"];
                if ($rAdminSettings["release_parser"] == "php") {
                    $rRelease = new Release($rFilename);
                    $rTitle = $rRelease->getTitle();
                    $rYear = $rRelease->getYear();
                    $rReleaseSeason = $rRelease->getSeason();
                    $rReleaseEpisode = $rRelease->getEpisode();
                } else {
                    $rRelease = parseRelease($rFilename);
                    $rTitle = $rRelease["title"];
                    $rYear = $rRelease["year"];
                    $rReleaseSeason = $rRelease["season"];
                    $rReleaseEpisode = $rRelease["episode"];
                }
                if (!$rTitle) { $rTitle = $rFilename; }
                $rMatch = null;
                if (!$rRow["disable_tmdb"]) {
                    if ($rRow["type"] == "movie") {
                        $rResults = $rTMDB->searchMovie($rTitle);
                    } else {
                        $rResults = $rTMDB->searchTVShow($rTitle);
                    }
                    $rMatches = Array();
                    foreach ($rResults as $rResultArr) {
                        similar_text(strtoupper($rTitle), strtoupper($rResultArr->get("title") ?: $rResultArr->get("name")), $rPercentage);
                        if ($rPercentage >= $rWatchSettings["percentage_match"]) {
                            if ((!$rYear) OR (intval(substr($rResultArr->get("release_date") ?: $rResultArr->get("first_air_date"), 0, 4)) == intval($rYear))) {
                                if (strtolower($rResultArr->get("name")) == strtolower($rTitle)) {
                                    $rMatches = Array(Array("percentage" => 100, "data" => $rResultArr));
                                    break;
                                } else {
                                    $rMatches[] = Array("percentage" => $rPercentage, "data" => $rResultArr);
                                }
                            }
                        }
                    }
                    if (count($rMatches) > 0) {
                        $rMax = max(array_column($rMatches, 'percentage'));
                        $rKeys = array_filter(array_map(function ($rMatches) use ($rMax) { return $rMatches['percentage'] == $rMax ? $rMatches['data'] : null; }, $rMatches));
                        $rMatch = array_values($rKeys)[0];
                    }
                }
                if (($rMatch) OR ($rRow["ignore_no_match"])) {
                    if ($rMatch) {
                        if ($rRow["type"] == "movie") {
                            $rMovie = $rTMDB->getMovie($rMatch->get("id"));
                            $rMovieData = json_decode($rMovie->getJSON(), True);
                            $rMovieData["trailer"] = $rMovie->getTrailer();
							if  ($rAdminSettings["tmdb_http_enable"]) {
                                $rThumb = "http://image.tmdb.org/t/p/w600_and_h900_bestv2".$rMovieData['poster_path'];
                                $rBG = "http://image.tmdb.org/t/p/w1280".$rMovieData['backdrop_path'];
							} else {
								$rThumb = "https://image.tmdb.org/t/p/w600_and_h900_bestv2".$rMovieData['poster_path'];
                                $rBG = "https://image.tmdb.org/t/p/w1280".$rMovieData['backdrop_path'];
							}	
                            if ($rAdminSettings["download_images"]) {
                                $rThumb = downloadImage($rThumb);
                                $rBG = downloadImage($rBG);
                            } else {
                                sleep(1); // Avoid limits.
                            }
                            $rCast = Array();
                            foreach ($rMovieData["credits"]["cast"] as $rMember) {
                                if (count($rCast) < 5) {
                                    $rCast[] = $rMember["name"];
                                }
                            }
                            $rDirectors = Array();
                            foreach ($rMovieData["credits"]["crew"] as $rMember) {
                                if ((count($rDirectors) < 5) && ($rMember["department"] == "Directing")) {
                                    $rDirectors[] = $rMember["name"];
                                }
                            }
                            $rCountry = "";
                            if (isset($rMovieData["production_countries"][0]["name"])) {
                                $rCountry = $rMovieData["production_countries"][0]["name"];
                            }
                            $rGenres = Array();
                            foreach ($rMovieData["genres"] as $rGenre) {
                                if (count($rGenres) < 3) {
                                    $rGenres[] = $rGenre["name"];
                                }
                            }
                            $rSeconds = intval($rMovieData["runtime"]) * 60;
                            $rImportArray["stream_display_name"] = $rMovieData["title"];
                            if (strlen($rMovieData["release_date"]) > 0) {
                                $rImportArray["stream_display_name"] .= " (".intval(substr($rMovieData["release_date"], 0, 4)).")";
                            }
                            $rImportArray["movie_propeties"] = Array("kinopoisk_url" => "https://www.themoviedb.org/movie/".$rMovieData["id"], "tmdb_id" => $rMovieData["id"], "name" => $rMovieData["title"], "o_name" => $rMovieData["original_title"], "cover_big" => $rThumb, "movie_image" => $rThumb, "releasedate" => $rMovieData["release_date"], "episode_run_time" => $rMovieData["runtime"], "youtube_trailer" => $rMovieData["trailer"], "director" => join(", ", $rDirectors), "actors" => join(", ", $rCast), "cast" => join(", ", $rCast), "description" => $rMovieData["overview"], "plot" => $rMovieData["overview"], "age" => "", "mpaa_rating" => "", "rating_count_kinopoisk" => 0, "country" => $rCountry, "genre" => join(", ", $rGenres), "backdrop_path" => Array($rBG), "duration_secs" => $rSeconds, "duration" => sprintf('%02d:%02d:%02d', ($rSeconds/3600),($rSeconds/60%60), $rSeconds%60), "video" => Array(), "audio" => Array(), "bitrate" => 0, "rating" => $rMovieData["vote_average"]);
                            $rImportArray["read_native"] = $rWatchSettings["read_native"] ?: 1;
                            $rImportArray["movie_symlink"] = $rWatchSettings["movie_symlink"] ?: 1;
                            $rImportArray["transcode_profile_id"] = $rWatchSettings["transcode_profile_id"] ?: 0;
                            $rImportArray["order"] = getNextOrder();
                            $rCategoryData = $rWatchCategories[1][intval($rMovieData["genres"][0]["id"])];
                            if ($rRow["category_id"] > 0) {
                                $rImportArray["category_id"] = intval($rRow["category_id"]);
                            } else if ($rCategoryData["category_id"] > 0) {
                                $rImportArray["category_id"] = intval($rCategoryData["category_id"]);
                            } else if ($rRow["fb_category_id"] > 0) {
                                $rImportArray["category_id"] = intval($rRow["fb_category_id"]);
                            } else {
                                $rImportArray["category_id"] = 0;
                            }
                        } else {
                            $rShow = $rTMDB->getTVShow($rMatch->get("id"));
                            $rShowData = json_decode($rShow->getJSON(), True);
                            $rSeries = getSeriesByTMDB($rShowData["id"]);
                            if (!$rSeries) {
                                // Series doesn't exist, create it!
                                $rSeriesArray = Array("title" => $rShowData["name"], "category_id" => "", "episode_run_time" => 0, "tmdb_id" => $rShowData["id"], "cover" => "", "genre" => "", "plot" => $rShowData["overview"], "cast" => "", "rating" => $rShowData["vote_average"], "director" => "", "releaseDate" => $rShowData["first_air_date"], "last_modified" => time(), "seasons" => Array(), "backdrop_path" => Array(), "youtube_trailer" => "");
                                $rSeriesArray["youtube_trailer"] = getSeriesTrailer($rShowData["id"]);
								if  ($rAdminSettings["tmdb_http_enable"]) {
                                    $rSeriesArray["cover"] = "http://image.tmdb.org/t/p/w600_and_h900_bestv2".$rShowData['poster_path'];
                                    $rSeriesArray["cover_big"] = $rSeriesArray["cover"];
                                    $rSeriesArray["backdrop_path"] = Array("http://image.tmdb.org/t/p/w1280".$rShowData['backdrop_path']);
								} else {
									$rSeriesArray["cover"] = "https://image.tmdb.org/t/p/w600_and_h900_bestv2".$rShowData['poster_path'];
                                    $rSeriesArray["cover_big"] = $rSeriesArray["cover"];
                                    $rSeriesArray["backdrop_path"] = Array("https://image.tmdb.org/t/p/w1280".$rShowData['backdrop_path']);
								}
                                if ($rAdminSettings["download_images"]) {
                                    $rSeriesArray["cover"] = downloadImage($rSeriesArray["cover"]);
                                    $rSeriesArray["backdrop_path"] = Array(downloadImage($rSeriesArray["backdrop_path"][0]));
                                }
                                $rCast = Array();
                                foreach ($rShowData["credits"]["cast"] as $rMember) {
                                    if (count($rCast) < 5) {
                                        $rCast[] = $rMember["name"];
                                    }
                                }
                                $rSeriesArray["cast"] = join(", ", $rCast);
                                $rDirectors = Array();
                                foreach ($rShowData["credits"]["crew"] as $rMember) {
                                    if ((count($rDirectors) < 5) && ($rMember["department"] == "Directing")) {
                                        $rDirectors[] = $rMember["name"];
                                    }
                                }
                                $rSeriesArray["director"] = join(", ", $rDirectors);
                                $rGenres = Array();
                                foreach ($rShowData["genres"] as $rGenre) {
                                    if (count($rGenres) < 3) {
                                        $rGenres[] = $rGenre["name"];
                                    }
                                }
                                $rSeriesArray["genre"] = join(", ", $rGenres);
                                $rSeriesArray["episode_run_time"] = intval($rShowData["episode_run_time"][0]);
                                $rCategoryData = $rWatchCategories[2][intval($rShowData["genres"][0]["id"])];
                                if ($rRow["category_id"] > 0) {
                                    $rSeriesArray["category_id"] = intval($rRow["category_id"]);
                                } else if ($rCategoryData["category_id"] > 0) {
                                    $rSeriesArray["category_id"] = intval($rCategoryData["category_id"]);
                                } else if ($rRow["fb_category_id"] > 0) {
                                    $rSeriesArray["category_id"] = intval($rRow["fb_category_id"]);
                                } else {
                                    $rSeriesArray["category_id"] = 0;
                                }
                                if ($rSeriesArray["category_id"] > 0) {
                                    $rCols = "`".implode('`,`', array_keys($rSeriesArray))."`";
                                    $rValues = null;
                                    foreach (array_values($rSeriesArray) as $rValue) {
                                        isset($rValues) ? $rValues .= ',' : $rValues = '';
                                        if (is_array($rValue)) {
                                            $rValue = json_encode($rValue);
                                        }
                                        if (is_null($rValue)) {
                                            $rValues .= 'NULL';
                                        } else {
                                            $rValues .= '\''.$db->real_escape_string($rValue).'\'';
                                        }
                                    }
                                    $rQuery = "INSERT INTO `series`(".$db->real_escape_string($rCols).") VALUES(".$rValues.");";
                                    if ($db->query($rQuery)) {
                                        $rInsertID = $db->insert_id;
                                        $rSeries = getSerie($rInsertID);
                                        $rORBouquets = json_decode($rRow["bouquets"], True);
                                        if (!$rORBouquets) {
                                            $rORBouquets = Array();
                                        }
                                        if (count($rORBouquets) > 0) {
                                            $rBouquets = json_decode($rRow["bouquets"], True);
                                        } else {
                                            $rBouquets = json_decode($rCategoryData["category_id"], True);
                                        }
                                        if (!$rBouquets) {
                                            $rBouquets = json_decode($rRow["fb_bouquets"], True);
                                        }
                                        if (!$rBouquets) {
                                            $rBouquets = Array();
                                        }
                                        foreach ($rBouquets as $rBouquet) {
                                            addToBouquet("series", $rBouquet, $rInsertID);
                                            $rChanged = True;
                                        }
                                    }
                                }
                            }
                            $rImportArray["read_native"] = $rWatchSettings["read_native"] ?: 1;
                            $rImportArray["movie_symlink"] = $rWatchSettings["movie_symlink"] ?: 1;
                            $rImportArray["transcode_profile_id"] = $rWatchSettings["transcode_profile_id"] ?: 0;
                            $rImportArray["order"] = getNextOrder();
                            if (($rReleaseSeason) && ($rReleaseEpisode)) {
                                $rImportArray["stream_display_name"] = $rShowData["name"]." - S".sprintf('%02d', intval($rReleaseSeason))."E".sprintf('%02d', $rReleaseEpisode);
                                $rEpisodes = json_decode($rTMDB->getSeason($rShowData["id"], intval($rReleaseSeason))->getJSON(), True);
                                foreach ($rEpisodes["episodes"] as $rEpisode) {
                                    if (intval($rEpisode["episode_number"]) == $rReleaseEpisode) {
                                        if (strlen($rEpisode["still_path"]) > 0) {
											if  ($rAdminSettings["tmdb_http_enable"]) {
                                               $rImage = "http://image.tmdb.org/t/p/w300".$rEpisode["still_path"];
											} else {
												$rImage = "https://image.tmdb.org/t/p/w300".$rEpisode["still_path"];
											}	
                                            if ($rAdminSettings["download_images"]) {
                                                $rImage = downloadImage($rImage);
                                            }
                                        }
                                        if (strlen($rEpisode["name"]) > 0) {
                                            $rImportArray["stream_display_name"] .= " - ".$rEpisode["name"];
                                        }
                                        $rSeconds = intval($rShowData["episode_run_time"][0]) * 60;
                                        $rImportArray["movie_propeties"] = Array("tmdb_id" => $rEpisode["id"], "releasedate" => $rEpisode["air_date"], "plot" => $rEpisode["overview"], "duration_secs" => $rSeconds, "duration" => sprintf('%02d:%02d:%02d', ($rSeconds/3600),($rSeconds/60%60), $rSeconds%60), "movie_image" => $rImage, "video" => Array(), "audio" => Array(), "bitrate" => 0, "rating" => $rEpisode["vote_average"], "season" => $rReleaseSeason);
                                        if (strlen($rImportArray["movie_propeties"]["movie_image"][0]) == 0) {
                                            unset($rImportArray["movie_propeties"]["movie_image"]);
                                        }
                                    }
                                }
                                if (strlen($rImportArray["stream_display_name"]) == 0) {
                                    $rImportArray["stream_display_name"] = "No Episode Title";
                                }
                            }
                        }
                    } else {
                        if ($rRow["type"] == "movie") {
                            $rImportArray["stream_display_name"] = $rTitle;
                            if ($rYear) {
                                $rImportArray["stream_display_name"] .= " (".$rYear.")";
                            }
                            $rImportArray["read_native"] = $rWatchSettings["read_native"] ?: 1;
                            $rImportArray["movie_symlink"] = $rWatchSettings["movie_symlink"] ?: 1;
                            $rImportArray["transcode_profile_id"] = $rWatchSettings["transcode_profile_id"] ?: 0;
                            $rImportArray["order"] = getNextOrder();
                            $rCategoryData = $rWatchCategories[1][intval($rMovieData["genres"][0]["id"])];
                            if ($rRow["category_id"] > 0) {
                                $rImportArray["category_id"] = intval($rRow["category_id"]);
                            } else if ($rRow["fb_category_id"] > 0) {
                                $rImportArray["category_id"] = intval($rRow["fb_category_id"]);
                            } else {
                                $rImportArray["category_id"] = 0;
                            }
                        } else if ($rSeries) {
                            if (($rReleaseSeason) && ($rReleaseEpisode)) {
                                $rImportArray["stream_display_name"] = $rTitle." - S".sprintf('%02d', intval($rReleaseSeason))."E".sprintf('%02d', $rReleaseEpisode)." - ";
                            }
                            $rImportArray["read_native"] = $rWatchSettings["read_native"] ?: 1;
                            $rImportArray["movie_symlink"] = $rWatchSettings["movie_symlink"] ?: 1;
                            $rImportArray["transcode_profile_id"] = $rWatchSettings["transcode_profile_id"] ?: 0;
                            $rImportArray["order"] = getNextOrder();
                        }
                    }
                    if (isset($rRow["auto_subtitles"])) {
                        $rPathInfo = pathinfo($rFile);
                        foreach (Array("srt", "sub", "sbv") as $rExt) {
                            $rSubtitle = $rPathInfo["dirname"]."/".$rPathInfo["filename"].".".$rExt;
                            if (in_array($rSubtitle, $rSubtitles)) {
                                $rImportArray["movie_subtitles"] = Array("files" => Array($rSubtitle), "names" => Array("Subtitles"), "charset" => Array("UTF-8"), "location" => intval($rRow["server_id"]));
                                break;
                            }
                        }
                    }
                    if ($rRow["type"] == "movie") {
                        $rORBouquets = json_decode($rRow["bouquets"], True);
                        if (!$rORBouquets) {
                            $rORBouquets = Array();
                        }
                        if (count($rORBouquets) > 0) {
                            $rBouquets = json_decode($rRow["bouquets"], True);
                        } else {
                            $rBouquets = json_decode($rCategoryData["category_id"], True);
                        }
                        if (!$rBouquets) {
                            $rBouquets = json_decode($rRow["fb_bouquets"], True);
                        }
                        if (!$rBouquets) {
                            $rBouquets = Array();
                        }
                    }
                    if (($rImportArray["category_id"] > 0) OR ($rSeries)) {
                        $rCols = "`".implode('`,`', array_keys($rImportArray))."`";
                        $rValues = null;
                        foreach (array_values($rImportArray) as $rValue) {
                            isset($rValues) ? $rValues .= ',' : $rValues = '';
                            if (is_array($rValue)) {
                                $rValue = json_encode($rValue);
                            }
                            if (is_null($rValue)) {
                                $rValues .= 'NULL';
                            } else {
                                $rValues .= '\''.$db->real_escape_string($rValue).'\'';
                            }
                        }
                        $rQuery = "INSERT INTO `streams`(".$db->real_escape_string($rCols).") VALUES(".$rValues.");";
                        if ($db->query($rQuery)) {
                            $rInsertID = $db->insert_id;
                            $db->query("INSERT INTO `streams_sys`(`stream_id`, `server_id`, `parent_id`, `on_demand`) VALUES(".intval($rInsertID).", ".intval($rRow["server_id"]).", 0, 0);");
                            if ($rWatchSettings["auto_encode"]) {
                                $rPost = Array("action" => "vod", "sub" => "start", "stream_ids" => Array($rInsertID));
                                $rContext = stream_context_create(array(
                                    'http' => array(
                                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                        'method'  => 'POST',
                                        'content' => http_build_query($rPost)
                                    )
                                ));
                                $rRet = json_decode(file_get_contents($rAPI, false, $rContext), True);
                            }
                            if ($rRow["type"] == "movie") {
                                foreach ($rBouquets as $rBouquet) {
                                    addToBouquet("stream", $rBouquet, $rInsertID);
                                    $rChanged = True;
                                }
                            } else {
                                $db->query("INSERT INTO `series_episodes`(`season_num`, `series_id`, `stream_id`, `sort`) VALUES(".intval($rReleaseSeason).", ".intval($rSeries["id"]).", ".$rInsertID.", ".intval($rReleaseEpisode).");");
                                if (!in_array($rSeries["id"], $rUpdateSeries)) {
                                    $rUpdateSeries[] = $rSeries["id"];
                                }
                            }
                            // Success!
                            $db->query("INSERT INTO `watch_output`(`type`, `server_id`, `filename`, `status`, `stream_id`) VALUES(".(Array("movie" => 1, "series" => 2)[$rRow["type"]]).", ".intval($rRow["server_id"]).", '".$db->real_escape_string($rFile)."', 1, ".intval($rInsertID).");");
                        } else {
                            // Insert failed.
                            $db->query("INSERT INTO `watch_output`(`type`, `server_id`, `filename`, `status`, `stream_id`) VALUES(".(Array("movie" => 1, "series" => 2)[$rRow["type"]]).", ".intval($rRow["server_id"]).", '".$db->real_escape_string($rFile)."', 2, 0);");
                        }
                    } else {
                        // No category.
                        $db->query("INSERT INTO `watch_output`(`type`, `server_id`, `filename`, `status`, `stream_id`) VALUES(".(Array("movie" => 1, "series" => 2)[$rRow["type"]]).", ".intval($rRow["server_id"]).", '".$db->real_escape_string($rFile)."', 3, 0);");
                    }
                } else {
                    // No match.
                    $db->query("INSERT INTO `watch_output`(`type`, `server_id`, `filename`, `status`, `stream_id`) VALUES(".(Array("movie" => 1, "series" => 2)[$rRow["type"]]).", ".intval($rRow["server_id"]).", '".$db->real_escape_string($rFile)."', 4, 0);");
                }
            } else {
                // File is broken.
                $db->query("INSERT INTO `watch_output`(`type`, `server_id`, `filename`, `status`, `stream_id`) VALUES(".(Array("movie" => 1, "series" => 2)[$rRow["type"]]).", ".intval($rRow["server_id"]).", '".$db->real_escape_string($rFile)."', 5, 0);");
            }
        }
    }
}
if ($rChanged) {
    scanBouquets();
}
foreach ($rUpdateSeries as $rSeriesID) {
    updateSeries(intval($rSeriesID));
}
?>