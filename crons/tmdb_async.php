<?php
include "/home/xtreamcodes/iptv_xtream_codes/admin/functions.php";
include "/home/xtreamcodes/iptv_xtream_codes/admin/tmdb.php";
include "/home/xtreamcodes/iptv_xtream_codes/admin/tmdb_release.php";

$rAdminSettings = getAdminSettings();
$rSettings = getSettings();
$rCategories = getCategories();
$rServers = getStreamingServers();

$rResult = $db->query("SELECT * FROM `watch_settings`;");
if (($rResult) && ($rResult->num_rows == 1)) {
    $rWatchSettings = $rResult->fetch_assoc();
}

$rPID = getmypid();
if (isset($rAdminSettings["tmdb_pid"])) {
	if ((file_exists("/proc/".$rAdminSettings["tmdb_pid"])) && (strlen($rAdminSettings["tmdb_pid"]) > 0)) {
        exit;
    } else {
        $db->query("UPDATE `admin_settings` SET `value` = ".intval($rPID)." WHERE `type` = 'tmdb_pid';");
    }
} else {
    $db->query("INSERT INTO `admin_settings`(`type`, `value`) VALUES('tmdb_pid', ".intval($rPID).");");
}

$rLimit = 250;      // Limit by quantity.
$rTimeout = 3000;   // Limit by time.

set_time_limit($rTimeout);
ini_set('max_execution_time', $rTimeout);

if (strlen($rSettings["tmdb_api_key"]) == 0) { exit; }

if (strlen($rAdminSettings["tmdb_language"]) > 0) {
    $rTMDB = new TMDB($rSettings["tmdb_api_key"], $rAdminSettings["tmdb_language"]);
} else {
    $rTMDB = new TMDB($rSettings["tmdb_api_key"]);
}

$rUpdateSeries = Array();
$rResult = $db->query("SELECT `id`, `type`, `stream_id` FROM `tmdb_async` WHERE `status` = 0 ORDER BY `stream_id` ASC LIMIT ".intval($rLimit).";");
if (($rResult) && ($rResult->num_rows > 0)) {
    while ($rRow = $rResult->fetch_assoc()) {
        if ($rRow["type"] == 1) { // Movies
            $rResultB = $db->query("SELECT * FROM `streams` WHERE `id` = ".intval($rRow["stream_id"]).";");
            if (($rResultB) && ($rResultB->num_rows == 1)) {
                $rStream = $rResultB->fetch_assoc();
                $rFilename = pathinfo(json_decode($rStream["stream_source"], True)[0])["filename"];
                if ($rAdminSettings["release_parser"] == "php") {
                    $rRelease = new Release($rFilename);
                    $rTitle = $rRelease->getTitle();
                    $rYear = $rRelease->getYear();
                } else {
                    $rRelease = parseRelease($rFilename);
                    $rTitle = $rRelease["title"];
                    $rYear = $rRelease["year"];
                }
                if (!$rTitle) { $rTitle = $rFilename; }
                $rResults = $rTMDB->searchMovie($rTitle);
                $rMatch = null;
                foreach ($rResults as $rResultArr) {
                    similar_text(strtoupper($rTitle), strtoupper($rResultArr->get("title") ?: $rResultArr->get("name")), $rPercentage);
                    if ($rPercentage >= $rWatchSettings["percentage_match"]) {
                        if ($rYear) {
                            $rResultYear = intval(substr($rResultArr->get("release_date"), 0, 4));
                            if ($rResultYear == $rYear) {
                                $rMatch = $rResultArr;
                                break;
                            }
                        } else {
                            $rMatch = $rResultArr;
                            break;
                        }
                    }
                }
                if ($rMatch) {
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
                    $rProperties = Array("kinopoisk_url" => "https://www.themoviedb.org/movie/".$rMovieData["id"], "tmdb_id" => $rMovieData["id"], "name" => $rMovieData["title"], "o_name" => $rMovieData["original_title"], "cover_big" => $rThumb, "movie_image" => $rThumb, "releasedate" => $rMovieData["release_date"], "episode_run_time" => $rMovieData["runtime"], "youtube_trailer" => $rMovieData["trailer"], "director" => join(", ", $rDirectors), "actors" => join(", ", $rCast), "cast" => join(", ", $rCast), "description" => $rMovieData["overview"], "plot" => $rMovieData["overview"], "age" => "", "mpaa_rating" => "", "rating_count_kinopoisk" => 0, "country" => $rCountry, "genre" => join(", ", $rGenres), "backdrop_path" => Array($rBG), "duration_secs" => $rSeconds, "duration" => sprintf('%02d:%02d:%02d', ($rSeconds/3600),($rSeconds/60%60), $rSeconds%60), "video" => Array(), "audio" => Array(), "bitrate" => 0, "rating" => $rMovieData["vote_average"]);
                    $rTitle = $rMovieData["title"];
                    if (strlen($rMovieData["release_date"]) > 0) {
                        $rTitle .= " (".intval(substr($rMovieData["release_date"], 0, 4)).")";
                    }
                    $db->query("UPDATE `tmdb_async` SET `status` = 1 WHERE `id` = ".intval($rRow["id"]).";");
                    $db->query("UPDATE `streams` SET `stream_display_name` = '".$db->real_escape_string($rTitle)."', `movie_propeties` = '".$db->real_escape_string(json_encode($rProperties))."' WHERE `id` = ".intval($rRow["stream_id"]).";");
                } else {
                    $db->query("UPDATE `tmdb_async` SET `status` = -1 WHERE `id` = ".intval($rRow["id"]).";");
                }
            } else {
                $db->query("UPDATE `tmdb_async` SET `status` = -2 WHERE `id` = ".intval($rRow["id"]).";");
            }
        } else if ($rRow["type"] == 2) { // Series
            $rResultB = $db->query("SELECT * FROM `series` WHERE `id` = ".intval($rRow["stream_id"]).";");
            if (($rResultB) && ($rResultB->num_rows == 1)) {
                $rStream = $rResultB->fetch_assoc();
                $rFilename = $rStream["title"];
                if ($rAdminSettings["release_parser"] == "php") {
                    $rRelease = new Release($rFilename);
                    $rTitle = $rRelease->getTitle();
                    $rYear = $rRelease->getYear();
                } else {
                    $rRelease = parseRelease($rFilename);
                    $rTitle = $rRelease["title"];
                    $rYear = $rRelease["year"];
                }
                if (!$rTitle) { $rTitle = $rFilename; }
                $rResults = $rTMDB->searchTVShow($rTitle);
                $rMatch = null;
                foreach ($rResults as $rResultArr) {
                    similar_text($rTitle, $rResultArr->get("title") ?: $rResultArr->get("name"), $rPercentage);
                    if ($rPercentage >= $rWatchSettings["percentage_match"]) {
                        if ($rYear) {
                            $rResultYear = intval(substr($rResultArr->get("release_date"), 0, 4));
                            if ($rResultYear == $rYear) {
                                $rMatch = $rResultArr;
                                break;
                            }
                        } else {
                            $rMatch = $rResultArr;
                            break;
                        }
                    }
                }
                if ($rMatch) {
                    $rShow = $rTMDB->getTVShow($rMatch->get("id"));
                    $rShowData = json_decode($rShow->getJSON(), True);
                    $rSeriesArray = $rStream;
                    $rSeriesArray["title"] = $rShowData["name"];
                    $rSeriesArray["tmdb_id"] = $rShowData["id"];
                    $rSeriesArray["plot"] = $rShowData["overview"];
                    $rSeriesArray["rating"] = $rShowData["vote_average"];
                    $rSeriesArray["releaseDate"] = $rShowData["first_air_date"];
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
                    $rQuery = "REPLACE INTO `series`(".$db->real_escape_string($rCols).") VALUES(".$rValues.");";
                    $db->query($rQuery);
                    $rInsertID = $db->insert_id;
                    updateSeries(intval($rInsertID));
                    $db->query("UPDATE `tmdb_async` SET `status` = 1 WHERE `id` = ".intval($rRow["id"]).";");
                } else {
                    $db->query("UPDATE `tmdb_async` SET `status` = -1 WHERE `id` = ".intval($rRow["id"]).";");
                }
            } else {
                $db->query("UPDATE `tmdb_async` SET `status` = -2 WHERE `id` = ".intval($rRow["id"]).";");
            }
        } else if ($rRow["type"] == 3) { // Episodes
            $rResultB = $db->query("SELECT * FROM `streams` WHERE `id` = ".intval($rRow["stream_id"]).";");
            if (($rResultB) && ($rResultB->num_rows == 1)) {
                $rStream = $rResultB->fetch_assoc();
                $rResultC = $db->query("SELECT * FROM `series_episodes` WHERE `stream_id` = ".intval($rRow["stream_id"]).";");
                if (($rResultC) && ($rResultC->num_rows == 1)) {
                    $rSeriesEpisode = $rResultC->fetch_assoc();
                    $rResultD = $db->query("SELECT * FROM `series` WHERE `id` = ".intval($rSeriesEpisode["series_id"]).";");
                    if (($rResultD) && ($rResultD->num_rows == 1)) {
                        $rSeries = $rResultD->fetch_assoc();
                        if (strlen($rSeries["tmdb_id"]) > 0) {
                            $rShow = $rTMDB->getTVShow($rSeries["tmdb_id"]);
                            $rShowData = json_decode($rShow->getJSON(), True);
                            if (isset($rShowData["name"])) {
                                // Get season and episode from filename.
                                $rFilename = pathinfo(json_decode($rStream["stream_source"], True)[0])["filename"];
                                if ($rAdminSettings["release_parser"] == "php") {
                                    $rRelease = new Release($rFilename);
                                    $rReleaseSeason = $rRelease->getSeason();
                                    $rReleaseEpisode = $rRelease->getEpisode();
                                } else {
                                    $rRelease = parseRelease($rFilename);
                                    $rReleaseSeason = $rRelease["season"];
                                    $rReleaseEpisode = $rRelease["episode"];
                                }
                                if ((!$rReleaseSeason) OR (!$rReleaseEpisode)) {
                                    $rReleaseSeason = $rSeriesEpisode["season_num"];
                                    $rReleaseEpisode = $rSeriesEpisode["sort"];
                                }
                                $rTitle = $rShowData["name"]." - S".sprintf('%02d', intval($rReleaseSeason))."E".sprintf('%02d', $rReleaseEpisode);
                                $rEpisodes = json_decode($rTMDB->getSeason($rShowData["id"], intval($rReleaseSeason))->getJSON(), True);
                                $rProperties = Array();
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
                                            $rTitle .= " - ".$rEpisode["name"];
                                        }
                                        $rSeconds = intval($rShowData["episode_run_time"][0]) * 60;
                                        $rProperties = Array("tmdb_id" => $rEpisode["id"], "releasedate" => $rEpisode["air_date"], "plot" => $rEpisode["overview"], "duration_secs" => $rSeconds, "duration" => sprintf('%02d:%02d:%02d', ($rSeconds/3600),($rSeconds/60%60), $rSeconds%60), "movie_image" => $rImage, "video" => Array(), "audio" => Array(), "bitrate" => 0, "rating" => $rEpisode["vote_average"], "season" => $rReleaseSeason);
                                        if (strlen($rProperties["movie_image"][0]) == 0) {
                                            unset($rProperties["movie_image"]);
                                        }
                                        break;
                                    }
                                }
                                $db->query("UPDATE `tmdb_async` SET `status` = 1 WHERE `id` = ".intval($rRow["id"]).";");
                                $db->query("UPDATE `streams` SET `stream_display_name` = '".$db->real_escape_string($rTitle)."', `movie_propeties` = '".$db->real_escape_string(json_encode($rProperties))."' WHERE `id` = ".intval($rRow["stream_id"]).";");
                                $db->query("UPDATE `series_episodes` SET `season_num` = ".intval($rReleaseSeason).", `sort` = ".intval($rReleaseEpisode)." WHERE `stream_id` = ".intval($rRow["stream_id"]).";");
                                if (!in_array($rSeries["id"], $rUpdateSeries)) {
                                    $rUpdateSeries[] = $rSeries["id"];
                                }
                            }
                        } else {
                            $db->query("UPDATE `tmdb_async` SET `status` = -5 WHERE `id` = ".intval($rRow["id"]).";");
                        }
                    } else {
                        $db->query("UPDATE `tmdb_async` SET `status` = -4 WHERE `id` = ".intval($rRow["id"]).";");
                    }
                } else {
                    $db->query("UPDATE `tmdb_async` SET `status` = -3 WHERE `id` = ".intval($rRow["id"]).";");
                }
            } else {
                $db->query("UPDATE `tmdb_async` SET `status` = -2 WHERE `id` = ".intval($rRow["id"]).";");
            }
        }
    }
}

foreach ($rUpdateSeries as $rSeriesID) {
    updateSeries(intval($rSeriesID));
}
?>