<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "data/Movie.php";
include "data/TVShow.php";
include "data/Season.php";
include "data/Episode.php";
include "data/Person.php";
include "data/Role.php";
include "data/roles/MovieRole.php";
include "data/roles/TVShowRole.php";
include "data/Collection.php";
include "data/Company.php";
include "data/Genre.php";
include "data/config/APIConfiguration.php";
class TMDB
{
    private $_config = NULL;
    private $_apikey = NULL;
    private $_lang = NULL;
    private $_adult = NULL;
    private $_apiconfiguration = NULL;
    private $_debug = NULL;
    const _API_URL_ = "http://api.themoviedb.org/3/";
    const VERSION = "0.0.3.0";
    public function __construct($apikey = NULL, $lang = NULL, $adult = NULL, $debug = NULL)
    {
        require_once "data/config/config.php";
        $this->setConfig($cnf);
        $this->setApikey(isset($apikey) ? $apikey : $cnf["apikey"]);
        $this->setLang(isset($lang) ? $lang : $cnf["lang"]);
        $this->setAdult(isset($adult) ? $adult : $cnf["adult"]);
        $this->setDebug(isset($debug) ? $debug : $cnf["debug"]);
        if (!$this->_loadConfig()) {
            echo _("Unable to read configuration, verify that the API key is valid");
            exit;
        }
    }
    private function setConfig($config)
    {
        $this->_config = $config;
    }
    private function getConfig()
    {
        return $this->_config;
    }
    private function setApikey($apikey)
    {
        $this->_apikey = (string) $apikey;
    }
    private function getApikey()
    {
        return $this->_apikey;
    }
    public function setLang($lang = "en")
    {
        $this->_lang = (string) $lang;
    }
    public function getLang()
    {
        return $this->_lang;
    }
    public function setAdult($adult = false)
    {
        $this->_adult = $adult;
    }
    public function getAdult()
    {
        return $this->_adult ? "true" : "false";
    }
    public function setDebug($debug = false)
    {
        $this->_debug = $debug;
    }
    public function getDebug()
    {
        return $this->_debug;
    }
    private function _loadConfig()
    {
        $this->_apiconfiguration = new APIConfiguration($this->_call("configuration"));
        return !empty($this->_apiconfiguration);
    }
    public function getAPIConfig()
    {
        return $this->_apiconfiguration;
    }
    public function getImageURL($size = "original")
    {
        return $this->_apiconfiguration->getImageBaseURL() . $size;
    }
    public function getDiscoverMovies($page = 1)
    {
        $movies = [];
        $result = $this->_call("discover/movie", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }
    public function getDiscoverTVShows($page = 1)
    {
        $tvShows = [];
        $result = $this->_call("discover/tv", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $tvShows[] = new TVShow($data);
        }
        return $tvShows;
    }
    public function getDiscoverMovie($page = 1)
    {
        $movies = [];
        $result = $this->_call("discover/movie", "page=" . $page);
        foreach ($result["results"] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }
    public function getLatestMovie()
    {
        return new Movie($this->_call("movie/latest"));
    }
    public function getNowPlayingMovies($page = 1)
    {
        $movies = [];
        $result = $this->_call("movie/now_playing", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }
    public function getPopularMovies($page = 1)
    {
        $movies = [];
        $result = $this->_call("movie/popular", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }
    public function getTopRatedMovies($page = 1)
    {
        $movies = [];
        $result = $this->_call("movie/top_rated", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }
    public function getUpcomingMovies($page = 1)
    {
        $movies = [];
        $result = $this->_call("movie/upcoming", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }
    public function getLatestTVShow()
    {
        return new TVShow($this->_call("tv/latest"));
    }
    public function getOnTheAirTVShows($page = 1)
    {
        $tvShows = [];
        $result = $this->_call("tv/on_the_air", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $tvShows[] = new TVShow($data);
        }
        return $tvShows;
    }
    public function getAiringTodayTVShows($page = 1, $timeZone = "Europe/Madrid")
    {
        $tvShows = [];
        $result = $this->_call("tv/airing_today", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $tvShows[] = new TVShow($data);
        }
        return $tvShows;
    }
    public function getTopRatedTVShows($page = 1)
    {
        $tvShows = [];
        $result = $this->_call("tv/top_rated", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $tvShows[] = new TVShow($data);
        }
        return $tvShows;
    }
    public function getPopularTVShows($page = 1)
    {
        $tvShows = [];
        $result = $this->_call("tv/popular", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $tvShows[] = new TVShow($data);
        }
        return $tvShows;
    }
    public function getLatestPerson()
    {
        return new Person($this->_call("person/latest"));
    }
    public function getPopularPersons($page = 1)
    {
        $persons = [];
        $result = $this->_call("person/popular", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $persons[] = new Person($data);
        }
        return $persons;
    }
    private function _call($action, $appendToResponse = "")
    {
        $url = "http://api.themoviedb.org/3/" . $action . "?api_key=" . $this->getApikey() . "&language=" . $this->getLang() . "&append_to_response=" . implode(",", (array) $appendToResponse) . "&include_adult=" . $this->getAdult();
        if ($this->_debug) {
            echo "<pre><a href=\"" . $url . "\">check request</a></pre>";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        $results = curl_exec($ch);
        curl_close($ch);
        return (array) json_decode($results, true);
    }
    public function getMovie($idMovie, $appendToResponse = NULL)
    {
        $appendToResponse = isset($appendToResponse) ? $appendToResponse : $this->getConfig()["appender"]["movie"]["default"];
        return new Movie($this->_call("movie/" . $idMovie, $appendToResponse));
    }
    public function getTVShow($idTVShow, $appendToResponse = NULL)
    {
        $appendToResponse = isset($appendToResponse) ? $appendToResponse : $this->getConfig()["appender"]["tvshow"]["default"];
        return new TVShow($this->_call("tv/" . $idTVShow, $appendToResponse));
    }
    public function getSeason($idTVShow, $numSeason, $appendToResponse = NULL)
    {
        $appendToResponse = isset($appendToResponse) ? $appendToResponse : $this->getConfig()["appender"]["season"]["default"];
        return new Season($this->_call("tv/" . $idTVShow . "/season/" . $numSeason, $appendToResponse), $idTVShow);
    }
    public function getEpisode($idTVShow, $numSeason, $numEpisode, $appendToResponse = NULL)
    {
        $appendToResponse = isset($appendToResponse) ? $appendToResponse : $this->getConfig()["appender"]["episode"]["default"];
        return new Episode($this->_call("tv/" . $idTVShow . "/season/" . $numSeason . "/episode/" . $numEpisode, $appendToResponse), $idTVShow);
    }
    public function getPerson($idPerson, $appendToResponse = NULL)
    {
        $appendToResponse = isset($appendToResponse) ? $appendToResponse : $this->getConfig()["appender"]["person"]["default"];
        return new Person($this->_call("person/" . $idPerson, $appendToResponse));
    }
    public function getCollection($idCollection, $appendToResponse = NULL)
    {
        $appendToResponse = isset($appendToResponse) ? $appendToResponse : $this->getConfig()["appender"]["collection"]["default"];
        return new Collection($this->_call("collection/" . $idCollection, $appendToResponse));
    }
    public function getCompany($idCompany, $appendToResponse = NULL)
    {
        $appendToResponse = isset($appendToResponse) ? $appendToResponse : $this->getConfig()["appender"]["company"]["default"];
        return new Company($this->_call("company/" . $idCompany, $appendToResponse));
    }
    public function searchMovie($movieTitle)
    {
        $movies = [];
        $result = $this->_call("search/movie", "&query=" . urlencode($movieTitle));
        foreach ($result["results"] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }
    public function searchTVShow($tvShowTitle)
    {
        $tvShows = [];
        $result = $this->_call("search/tv", "&query=" . urlencode($tvShowTitle));
        foreach ($result["results"] as $data) {
            $tvShows[] = new TVShow($data);
        }
        return $tvShows;
    }
    public function searchPerson($personName)
    {
        $persons = [];
        $result = $this->_call("search/person", "&query=" . urlencode($personName));
        foreach ($result["results"] as $data) {
            $persons[] = new Person($data);
        }
        return $persons;
    }
    public function searchCollection($collectionName)
    {
        $collections = [];
        $result = $this->_call("search/collection", "&query=" . urlencode($collectionName));
        foreach ($result["results"] as $data) {
            $collections[] = new Collection($data);
        }
        return $collections;
    }
    public function searchCompany($companyName)
    {
        $companies = [];
        $result = $this->_call("search/company", "&query=" . urlencode($companyName));
        foreach ($result["results"] as $data) {
            $companies[] = new Company($data);
        }
        return $companies;
    }
    public function find($id, $external_source = "imdb_id")
    {
        $found = [];
        $result = $this->_call("find/" . $id, "&external_source=" . urlencode($external_source));
        foreach ($result["movie_results"] as $data) {
            $found["movies"][] = new Movie($data);
        }
        foreach ($result["person_results"] as $data) {
            $found["persons"][] = new Person($data);
        }
        foreach ($result["tv_results"] as $data) {
            $found["tvshows"][] = new TVShow($data);
        }
        foreach ($result["tv_season_results"] as $data) {
            $found["seasons"][] = new Season($data);
        }
        foreach ($result["tv_episode_results"] as $data) {
            $found["episodes"][] = new Episode($data);
        }
        return $found;
    }
    public function getTimezones()
    {
        return $this->_call("timezones/list");
    }
    public function getJobs()
    {
        return $this->_call("job/list");
    }
    public function getMovieGenres()
    {
        $genres = [];
        $result = $this->_call("genre/movie/list");
        foreach ($result["genres"] as $data) {
            $genres[] = new Genre($data);
        }
        return $genres;
    }
    public function getTVGenres()
    {
        $genres = [];
        $result = $this->_call("genre/tv/list");
        foreach ($result["genres"] as $data) {
            $genres[] = new Genre($data);
        }
        return $genres;
    }
    public function getMoviesByGenre($idGenre, $page = 1)
    {
        $movies = [];
        $result = $this->_call("genre/" . $idGenre . "/movies", "&page=" . $page);
        foreach ($result["results"] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }
}

?>