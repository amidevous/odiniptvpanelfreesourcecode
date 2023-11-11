<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "session.php";
include "functions.php";
if (!$rPermissions["is_admin"] || !hasPermissions("adv", "add_series") && !hasPermissions("adv", "edit_series")) {
    exit;
}
$rCategories = getCategories("series");
$rTMDBLanguages = ["" => "Default - EN", "aa" => "Afar", "af" => "Afrikaans", "ak" => "Akan", "an" => "Aragonese", "as" => "Assamese", "av" => "Avaric", "ae" => "Avestan", "ay" => "Aymara", "az" => "Azerbaijani", "ba" => "Bashkir", "bm" => "Bambara", "bi" => "Bislama", "bo" => "Tibetan", "br" => "Breton", "ca" => "Catalan", "cs" => "Czech", "ce" => "Chechen", "cu" => "Slavic", "cv" => "Chuvash", "kw" => "Cornish", "co" => "Corsican", "cr" => "Cree", "cy" => "Welsh", "da" => "Danish", "de" => "German", "dv" => "Divehi", "dz" => "Dzongkha", "eo" => "Esperanto", "et" => "Estonian", "eu" => "Basque", "fo" => "Faroese", "fj" => "Fijian", "fi" => "Finnish", "fr" => "French", "fy" => "Frisian", "ff" => "Fulah", "gd" => "Gaelic", "ga" => "Irish", "gl" => "Galician", "gv" => "Manx", "gn" => "Guarani", "gu" => "Gujarati", "ht" => "Haitian", "ha" => "Hausa", "sh" => "Serbo-Croatian", "hz" => "Herero", "ho" => "Hiri Motu", "hr" => "Croatian", "hu" => "Hungarian", "ig" => "Igbo", "io" => "Ido", "ii" => "Yi", "iu" => "Inuktitut", "ie" => "Interlingue", "ia" => "Interlingua", "id" => "Indonesian", "ik" => "Inupiaq", "is" => "Icelandic", "it" => "Italian", "ja" => "Japanese", "kl" => "Kalaallisut", "kn" => "Kannada", "ks" => "Kashmiri", "kr" => "Kanuri", "kk" => "Kazakh", "km" => "Khmer", "ki" => "Kikuyu", "rw" => "Kinyarwanda", "ky" => "Kirghiz", "kv" => "Komi", "kg" => "Kongo", "ko" => "Korean", "kj" => "Kuanyama", "ku" => "Kurdish", "lo" => "Lao", "la" => "Latin", "lv" => "Latvian", "li" => "Limburgish", "ln" => "Lingala", "lt" => "Lithuanian", "lb" => "Letzeburgesch", "lu" => "Luba-Katanga", "lg" => "Ganda", "mh" => "Marshall", "ml" => "Malayalam", "mr" => "Marathi", "mg" => "Malagasy", "mt" => "Maltese", "mo" => "Moldavian", "mn" => "Mongolian", "mi" => "Maori", "ms" => "Malay", "my" => "Burmese", "na" => "Nauru", "nv" => "Navajo", "nr" => "Ndebele", "nd" => "Ndebele", "ng" => "Ndonga", "ne" => "Nepali", "nl" => "Dutch", "nn" => "Norwegian Nynorsk", "nb" => "Norwegian Bokmal", "no" => "Norwegian", "ny" => "Chichewa", "oc" => "Occitan", "oj" => "Ojibwa", "or" => "Oriya", "om" => "Oromo", "os" => "Ossetian; Ossetic", "pi" => "Pali", "pl" => "Polish", "pt" => "Portuguese", "pt-BR" => "Portuguese - Brazil", "qu" => "Quechua", "rm" => "Raeto-Romance", "ro" => "Romanian", "rn" => "Rundi", "ru" => "Russian", "sg" => "Sango", "sa" => "Sanskrit", "si" => "Sinhalese", "sk" => "Slovak", "sl" => "Slovenian", "se" => "Northern Sami", "sm" => "Samoan", "sn" => "Shona", "sd" => "Sindhi", "so" => "Somali", "st" => "Sotho", "es" => "Spanish", "es-MX" => "Spanish - Latin America", "sq" => "Albanian", "sc" => "Sardinian", "sr" => "Serbian", "ss" => "Swati", "su" => "Sundanese", "sw" => "Swahili", "sv" => "Swedish", "ty" => "Tahitian", "ta" => "Tamil", "tt" => "Tatar", "te" => "Telugu", "tg" => "Tajik", "tl" => "Tagalog", "th" => "Thai", "ti" => "Tigrinya", "to" => "Tonga", "tn" => "Tswana", "ts" => "Tsonga", "tk" => "Turkmen", "tr" => "Turkish", "tw" => "Twi", "ug" => "Uighur", "uk" => "Ukrainian", "ur" => "Urdu", "uz" => "Uzbek", "ve" => "Venda", "vi" => "Vietnamese", "vo" => "Volapük", "wa" => "Walloon", "wo" => "Wolof", "xh" => "Xhosa", "yi" => "Yiddish", "za" => "Zhuang", "zu" => "Zulu", "ab" => "Abkhazian", "zh" => "Mandarin", "ps" => "Pushto", "am" => "Amharic", "ar" => "Arabic", "bg" => "Bulgarian", "cn" => "Cantonese", "mk" => "Macedonian", "el" => "Greek", "fa" => "Persian", "he" => "Hebrew", "hi" => "Hindi", "hy" => "Armenian", "en" => "English", "ee" => "Ewe", "ka" => "Georgian", "pa" => "Punjabi", "bn" => "Bengali", "bs" => "Bosnian", "ch" => "Chamorro", "be" => "Belarusian", "yo" => "Yoruba"];
if (isset($_POST["submit_series"])) {
    if (isset($_POST["edit"])) {
        if (!hasPermissions("adv", "edit_series")) {
            exit;
        }
        $rArray = getSerie($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        if (!hasPermissions("adv", "add_series")) {
            exit;
        }
        $rArray = ["title" => "", "category_id" => "", "episode_run_time" => 0, "tmdb_id" => 0, "cover" => "", "genre" => "", "plot" => "", "cast" => "", "rating" => 0, "director" => "", "releaseDate" => "", "last_modified" => time(), "seasons" => [], "backdrop_path" => [], "youtube_trailer" => ""];
    }
    if ($rAdminSettings["download_images"]) {
        $_POST["cover"] = downloadImage($_POST["cover"]);
        $_POST["backdrop_path"] = downloadImage($_POST["backdrop_path"]);
    }
    $rBouquets = $_POST["bouquets"];
    unset($_POST["bouquets"]);
    if (strlen($_POST["backdrop_path"]) == 0) {
        $rArray["backdrop_path"] = [];
    } else {
        $rArray["backdrop_path"] = [$_POST["backdrop_path"]];
    }
    unset($_POST["backdrop_path"]);
    $rArray["cover_big"] = $rArray["cover"];
    foreach ($_POST as $rKey => $rValue) {
        if (isset($rArray[$rKey])) {
            $rArray[$rKey] = $rValue;
        }
    }
    $rCols = "`" . ESC(implode("`,`", array_keys($rArray))) . "`";
    $rValues = NULL;
    foreach (array_values($rArray) as $rValue) {
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
    $rQuery = "REPLACE INTO `series`(" . $rCols . ") VALUES(" . $rValues . ");";
    if ($db->query($rQuery)) {
        if (isset($_POST["edit"])) {
            $rInsertID = intval($_POST["edit"]);
        } else {
            $rInsertID = $db->insert_id;
        }
        updateSeries(intval($rInsertID));
        foreach ($rBouquets as $rBouquet) {
            addToBouquet("series", $rBouquet, $rInsertID);
        }
        foreach (getBouquets() as $rBouquet) {
            if (!in_array($rBouquet["id"], $rBouquets)) {
                removeFromBouquet("series", $rBouquet["id"], $rInsertID);
            }
        }
        scanBouquets();
    }
    if (isset($rInsertID)) {
        header("Location: ./serie.php?successedit&id=" . $rInsertID);
        exit;
    }
    $_STATUS = 1;
}
if (isset($_GET["id"])) {
    $rSeries = getSerie($_GET["id"]);
    if (!$rSeries || !hasPermissions("adv", "edit_series")) {
        exit;
    }
} else {
    if (!hasPermissions("adv", "add_series")) {
        exit;
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
echo "                <!-- start page title -->\n                <div class=\"row\">\n                    <div class=\"col-12\">\n                        <div class=\"page-title-box\">\n                            <div class=\"page-title-right\">\n                                <ol class=\"breadcrumb m-0\">\n\t\t\t\t\t\t\t\t\t<li>\n                                        <a href=\"./series.php\">\n\t\t\t\t\t\t\t\t        <button type=\"button\" class=\"btn btn-primary waves-effect waves-light btn-sm\"><i class=\"mdi mdi-keyboard-backspace\"></i> ";
echo $_["back_to_series"];
echo "</button>\n\t\t\t\t\t\t\t\t\t    </a>\t\n                                    </li>\n                                </ol>\n                            </div>\n                            <h4 class=\"page-title\">";
if (isset($rSeries)) {
    echo $rSeries["title"];
} else {
    echo $_["add_series"];
}
echo "</h4>\n                        </div>\n                    </div>\n                </div>     \n                <!-- end page title --> \n                <div class=\"row\">\n                    <div class=\"col-xl-12\">\n                        ";
if (isset($_STATUS) && $_STATUS == 0) {
    if (!$rSettings["sucessedit"]) {
        echo "                        <div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
        echo $_["series_operation"];
        echo "                        </div>\n\t\t\t\t\t\t";
    } else {
        echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
        echo $_["series_operation"];
        echo "', \"success\");\n  \t\t\t\t\t</script>\n                        ";
    }
} else {
    if (isset($_STATUS) && 0 < $_STATUS) {
        if (!$rSettings["sucessedit"]) {
            echo "                        <div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                                <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                            ";
            echo $_["generic_fail"];
            echo "                        </div>\n                        ";
        } else {
            echo "                    <script type=\"text/javascript\">\n  \t\t\t\t\tswal(\"\", '";
            echo $_["generic_fail"];
            echo "', \"warning\");\n  \t\t\t\t\t</script>\n                        ";
        }
    }
}
echo "                        <div class=\"card\">\n                            <div class=\"card-body\">\n                                <form action=\"./serie.php";
if (isset($_GET["id"])) {
    echo "?id=" . $_GET["id"];
}
echo "\" method=\"POST\" id=\"series_form\" data-parsley-validate=\"\">\n                                    ";
if (isset($rSeries)) {
    echo "                                    <input type=\"hidden\" name=\"edit\" value=\"";
    echo $rSeries["id"];
    echo "\" />\n                                    ";
}
echo "                                    <!--<input type=\"hidden\" id=\"tmdb_id\" name=\"tmdb_id\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["tmdb_id"]);
}
echo "\" />-->\n                                    <div id=\"basicwizard\">\n                                        <ul class=\"nav nav-pills bg-light nav-justified form-wizard-header mb-4\">\n                                            <li class=\"nav-item\">\n                                                <a href=\"#stream-details\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\"> \n                                                    <i class=\"mdi mdi-account-card-details-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["details"];
echo "</span>\n                                                </a>\n                                            </li>\n                                            <li class=\"nav-item\">\n                                                <a href=\"#movie-information\" data-toggle=\"tab\" class=\"nav-link rounded-0 pt-2 pb-2\">\n                                                    <i class=\"mdi mdi-movie-outline mr-1\"></i>\n                                                    <span class=\"d-none d-sm-inline\">";
echo $_["information"];
echo "</span>\n                                                </a>\n                                            </li>\n                                        </ul>\n                                        <div class=\"tab-content b-0 mb-0 pt-0\">\n                                            <div class=\"tab-pane\" id=\"stream-details\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t    <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tmdb_language\">";
echo $_["tmdb_language"];
echo " <i data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"";
echo $_["select_which_language"];
echo "\" class=\"mdi mdi-information\"></i></label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"tmdb_language\" id=\"tmdb_language\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach ($rTMDBLanguages as $rKey => $rLanguage) {
    echo "                                                                    <option";
    if ($rAdminSettings["tmdb_language"] == $rKey) {
        echo " selected";
    }
    echo " value=\"";
    echo $rKey;
    echo "\">";
    echo $rLanguage;
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tmdbid\">";
echo $_["tmdb_id"];
echo "</label>\n                                                            <div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" id=\"tmdbid\" name=\"tmdbid\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["tmdb_id"]);
}
echo "\">\n                                                            </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"#\" id=\"search_id\" name=\"search_id\" class=\"btn btn-success btn-block\">";
echo $_["search"];
echo "</a>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"title\">";
echo $_["series_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"title\" name=\"title\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["title"]);
}
echo "\" required data-parsley-trigger=\"change\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"tmdb_search\">";
echo $_["tmdb_results"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select id=\"tmdb_search\" class=\"form-control\" data-toggle=\"select2\"></select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"category_id\">";
echo $_["category_name"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"category_id\" id=\"category_id\" class=\"form-control\" data-toggle=\"select2\">\n                                                                    ";
foreach ($rCategories as $rCategory) {
    echo "                                                                   <option ";
    if (isset($rSeries)) {
        if (intval($rSeries["category_id"]) == intval($rCategory["id"])) {
            echo "selected ";
        }
    } else {
        if (isset($_GET["category"]) && $_GET["category"] == $rCategory["id"]) {
            echo "selected ";
        }
    }
    echo "value=\"";
    echo $rCategory["id"];
    echo "\">";
    echo $rCategory["category_name"];
    echo "</option>\n                                                                     ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"bouquets\">";
echo $_["add_to_bouquets"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <select name=\"bouquets[]\" id=\"bouquets\" class=\"form-control select2-multiple\" data-toggle=\"select2\" multiple=\"multiple\" data-placeholder=\"";
echo $_["choose"];
echo "\">\n                                                                    ";
foreach (getBouquets() as $rBouquet) {
    echo "                                                                    <option ";
    if (isset($rSeries) && in_array($rSeries["id"], json_decode($rBouquet["bouquet_series"], true))) {
        echo "selected ";
    }
    echo "value=\"";
    echo $rBouquet["id"];
    echo "\">";
    echo htmlspecialchars($rBouquet["bouquet_name"]);
    echo "</option>\n                                                                    ";
}
echo "                                                                </select>\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"next list-inline-item float-right\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["next"];
echo "</a>\n                                                    </li>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t\t<li class=\"list-inline-item\">\n                                                        <a href=\"";
if (isset($rSeries)) {
    echo "//www.themoviedb.org/tv/" . htmlspecialchars($rSeries["tmdb_id"]);
}
echo "\" id=\"viewtmdb\" target=\"_blank\" class=\"btn btn-info\">View on TMDb</a>\n                                                    </li>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\n                                                </ul>\n                                            </div>\n                                            <div class=\"tab-pane\" id=\"movie-information\">\n                                                <div class=\"row\">\n                                                    <div class=\"col-12\">\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"cover\">";
echo $_["poster_url"];
echo "</label>\n                                                            <div class=\"col-md-8 input-group\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"cover\" name=\"cover\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["cover"]);
}
echo "\">\n                                                                <div class=\"input-group-append\">\n                                                                    <a href=\"javascript:void(0)\" onClick=\"openImage(this)\" class=\"btn btn-primary waves-effect waves-light\"><i class=\"mdi mdi-eye\"></i></a>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"backdrop_path\">";
echo $_["backdrop_url"];
echo "</label>\n                                                            <div class=\"col-md-8 input-group\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"backdrop_path\" name=\"backdrop_path\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars(json_decode($rSeries["backdrop_path"], true)[0]);
}
echo "\">\n                                                                <div class=\"input-group-append\">\n                                                                    <a href=\"javascript:void(0)\" onClick=\"openImage(this)\" class=\"btn btn-primary waves-effect waves-light\"><i class=\"mdi mdi-eye\"></i></a>\n                                                                </div>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"plot\">";
echo $_["plot"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <textarea rows=\"6\" class=\"form-control\" id=\"plot\" name=\"plot\">";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["plot"]);
}
echo "</textarea>\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"cast\">";
echo $_["cast"];
echo "</label>\n                                                            <div class=\"col-md-8\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"cast\" name=\"cast\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["cast"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"director\">";
echo $_["director"];
echo "</label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"director\" name=\"director\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["director"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-2 col-form-label\" for=\"genre\">";
echo $_["genres"];
echo "</label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"genre\" name=\"genre\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["genre"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"releaseDate\">";
echo $_["release_date"];
echo "</label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"releaseDate\" name=\"releaseDate\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["releaseDate"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-2 col-form-label\" for=\"episode_run_time\">";
echo $_["runtime"];
echo "</label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"episode_run_time\" name=\"episode_run_time\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["episode_run_time"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                        <div class=\"form-group row mb-4\">\n                                                            <label class=\"col-md-4 col-form-label\" for=\"youtube_trailer\">";
echo $_["youtube_trailer"];
echo "</label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"youtube_trailer\" name=\"youtube_trailer\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["youtube_trailer"]);
}
echo "\">\n                                                            </div>\n                                                            <label class=\"col-md-2 col-form-label\" for=\"rating\">";
echo $_["rating"];
echo "</label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"rating\" name=\"rating\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["rating"]);
}
echo "\">\n                                                            </div>\n\t\t\t\t\t                                    </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"form-group row mb-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"col-md-4 col-form-label\" for=\"tmdb_id\">";
echo $_["tmdb_id"];
echo "</label>\n                                                            <div class=\"col-md-3\">\n                                                                <input type=\"text\" class=\"form-control\" id=\"tmdb_id\" name=\"tmdb_id\" value=\"";
if (isset($rSeries)) {
    echo htmlspecialchars($rSeries["tmdb_id"]);
}
echo "\">\n                                                            </div>\n                                                        </div>\n                                                    </div> <!-- end col -->\n                                                </div> <!-- end row -->\n                                                <ul class=\"list-inline wizard mb-0\">\n                                                    <li class=\"previous list-inline-item\">\n                                                        <a href=\"javascript: void(0);\" class=\"btn btn-secondary\">";
echo $_["prev"];
echo "</a>\n                                                    </li>\n                                                    <li class=\"list-inline-item float-right\">\n                                                        <input name=\"submit_series\" type=\"submit\" class=\"btn btn-primary\" value=\"";
if (isset($rSeries)) {
    echo $_["edit"];
} else {
    echo $_["add"];
}
echo "\" />\n                                                    </li>\n                                                </ul>\n                                            </div>\n                                        </div> <!-- tab-content -->\n                                    </div> <!-- end #basicwizard-->\n                                </form>\n                            </div> <!-- end card-body -->\n                        </div> <!-- end card-->\n                    </div> <!-- end col -->\n                </div>\n            </div> <!-- end container -->\n        </div>\n        <!-- end wrapper -->\n        ";
if ($rSettings["sidebar"]) {
    echo "</div>";
}
echo "        <!-- Footer Start -->\n        <footer class=\"footer\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col-md-12 copyright text-center\">";
echo getFooter();
echo "</div>\n                </div>\n            </div>\n        </footer>\n        <!-- end Footer -->\n\n        <script src=\"assets/js/vendor.min.js\"></script>\n        <script src=\"assets/libs/jquery-toast/jquery.toast.min.js\"></script>\n        <script src=\"assets/libs/jquery-nice-select/jquery.nice-select.min.js\"></script>\n        <script src=\"assets/libs/switchery/switchery.min.js\"></script>\n        <script src=\"assets/libs/select2/select2.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js\"></script>\n        <script src=\"assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js\"></script>\n        <script src=\"assets/libs/clockpicker/bootstrap-clockpicker.min.js\"></script>\n        <script src=\"assets/libs/datatables/jquery.dataTables.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.bootstrap4.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.responsive.min.js\"></script>\n        <script src=\"assets/libs/datatables/responsive.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.buttons.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.bootstrap4.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.html5.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.flash.min.js\"></script>\n        <script src=\"assets/libs/datatables/buttons.print.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.keyTable.min.js\"></script>\n        <script src=\"assets/libs/datatables/dataTables.select.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js\"></script>\n        <script src=\"assets/libs/magnific-popup/jquery.magnific-popup.min.js\"></script>\n        <script src=\"assets/libs/treeview/jstree.min.js\"></script>\n        <script src=\"assets/js/pages/treeview.init.js\"></script>\n        <script src=\"assets/js/pages/form-wizard.init.js\"></script>\n        <script src=\"assets/libs/parsleyjs/parsley.min.js\"></script>\n        <script src=\"assets/js/app.min.js\"></script>\n        \n        <script>\n        var changeTitle = false;\n        \n        (function(\$) {\n          \$.fn.inputFilter = function(inputFilter) {\n            return this.on(\"input keydown keyup mousedown mouseup select contextmenu drop\", function() {\n              if (inputFilter(this.value)) {\n                this.oldValue = this.value;\n                this.oldSelectionStart = this.selectionStart;\n                this.oldSelectionEnd = this.selectionEnd;\n              } else if (this.hasOwnProperty(\"oldValue\")) {\n                this.value = this.oldValue;\n                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);\n              }\n            });\n          };\n        }(jQuery));\n        function openImage(elem) {\n            rPath = \$(elem).parent().parent().find(\"input\").val();\n            if (rPath.length > 0) {\n                if (rPath.substring(0,1) == \".\") {\n                    window.open('";
echo getURL();
echo "' + rPath.substring(1, rPath.length));\n                } else if (rPath.substring(0,1) == \"/\") {\n                    window.open('";
echo getURL();
echo "' + rPath);\n                } else {\n                    window.open(rPath);\n                }\n            }\n        }\n        \$(document).ready(function() {\n            \$('select').select2({width: '100%'});\n            \n            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));\n            elems.forEach(function(html) {\n              var switchery = new Switchery(html);\n            });\n            \n            \$(\"#series_form\").submit(function(e){\n                if (\$(\"#title\").val().length == 0) {\n                    e.preventDefault();\n                    \$.toast(\"";
echo $_["enter_a_series_name"];
echo "\");\n                }\n            });\n            \n            \$(window).keypress(function(event){\n                if(event.which == 13 && event.target.nodeName != \"TEXTAREA\") return false;\n            });\n            \n            \$(\"#title\").change(function() {\n                if (!window.changeTitle) {\n                    \$(\"#tmdb_search\").empty().trigger('change');\n                    if (\$(\"#title\").val().length > 0) {\n                        \$.getJSON(\"./api.php?action=tmdb_search&type=series&term=\" + \$(\"#title\").val(), function(data) {\n                            if (data.result == true) {\n                                if (data.data.length > 0) {\n                                    newOption = new Option(\"";
echo $_["found_"];
echo "\" + data.data.length + \"";
echo $_["_results"];
echo "\", -1, true, true);\n                                } else {\n                                    newOption = new Option(\"";
echo $_["no_results_found"];
echo "\", -1, true, true);\n                                }\n                                \$(\"#tmdb_search\").append(newOption).trigger('change');\n                                \$(data.data).each(function(id, item) {\n                                    if (item.first_air_date.length > 0) {\n                                        rTitle = item.name + \" (\" + item.first_air_date.substring(0, 4) + \")\";\n                                    } else {\n                                        rTitle = item.name;\n                                    }\n                                    newOption = new Option(rTitle, item.id, true, true);\n                                    \$(\"#tmdb_search\").append(newOption);\n                                });\n                            } else {\n                                newOption = new Option(\"";
echo $_["no_results_found"];
echo "\", -1, true, true);\n                            }\n                            \$(\"#tmdb_search\").val(-1).trigger('change');\n                        });\n                    }\n                } else {\n                    window.changeTitle = false;\n                }\n            });\n\t\t    \$(\"#search_id\").click(function() {\n               if ((\$(\"#tmdbid\").val()) && (\$(\"#tmdbid\").val() > -1)) {\n\t\t\t\t\t\$.getJSON(\"./api.php?action=tmdb&type=series&id=\" + \"&id=\" + \$(\"#tmdbid\").val() + \"&tmdb_language=\" + \$(\"#tmdb_language\").val(), function(data) {\n\t\t\t\t\t\tif (data.result == true) {\n                            window.changeTitle = true;\n\t\t\t\t\t\t\t";
if ($rAdminSettings["tmdb_http_enable"]) {
    echo "\t\t\t\t\t\t\t\$(\"#tmdbid\").val(data.data.id);\n\t\t\t\t\t\t\t\$(\"#title\").val(data.data.name);\n                            \$(\"#cover\").val(\"\");\n                            if (data.data.poster_path.length > 0) {\n                                \$(\"#cover\").val(\"http://image.tmdb.org/t/p/w600_and_h900_bestv2\" + data.data.poster_path);\n\t\t\t\t\t\t\t\t\$(\"#cover_preview\").attr(\"src\", \$(\"#cover\").val());\n                            }\n                            \$(\"#backdrop_path\").val(\"\");\n                            if (data.data.backdrop_path.length > 0) {\n                                \$(\"#backdrop_path\").val(\"http://image.tmdb.org/t/p/w1280\" + data.data.backdrop_path);\n\t\t\t\t\t\t\t\t\$(\"#backdrop_path_preview\").attr(\"src\", \$(\"#backdrop_path\").val());\n                            }\n\t\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t\$(\"#tmdbid\").val(data.data.id);\n\t\t\t\t\t\t\t\$(\"#title\").val(data.data.name);\n                            \$(\"#cover\").val(\"\");\n                            if (data.data.poster_path.length > 0) {\n                                \$(\"#cover\").val(\"https://image.tmdb.org/t/p/w600_and_h900_bestv2\" + data.data.poster_path);\n\t\t\t\t\t\t\t\t\$(\"#cover_preview\").attr(\"src\", \$(\"#cover\").val());\n                            }\n                            \$(\"#backdrop_path\").val(\"\");\n                            if (data.data.backdrop_path.length > 0) {\n                                \$(\"#backdrop_path\").val(\"https://image.tmdb.org/t/p/w1280\" + data.data.backdrop_path);\n\t\t\t\t\t\t\t\t\$(\"#backdrop_path_preview\").attr(\"src\", \$(\"#backdrop_path\").val());\n                            }\n\t\t\t\t\t\t\t";
}
echo "                            \$(\"#releaseDate\").val(data.data.first_air_date);\n                            \$(\"#episode_run_time\").val(data.data.episode_run_time[0]);\n                            \$(\"#youtube_trailer\").val(\"\");\n                            if (data.data.trailer) {\n                                \$(\"#youtube_trailer\").val(data.data.trailer);\n                            }\n                            rCast = \"\";\n                            rMemberID = 0;\n                            \$(data.data.credits.cast).each(function(id, member) {\n                                rMemberID += 1;\n                                if (rMemberID <= 5) {\n                                    if (rCast.length > 0) {\n                                        rCast += \", \";\n                                    }\n                                    rCast += member.name;\n                                }\n                            });\n                            \$(\"#cast\").val(rCast);\n                            rGenres = \"\";\n                            rGenreID = 0;\n                            \$(data.data.genres).each(function(id, genre) {\n                                rGenreID += 1;\n                                if (rGenreID <= 3) {\n                                    if (rGenres.length > 0) {\n                                        rGenres += \", \";\n                                    }\n                                    rGenres += genre.name;\n                                }\n                            });\n                            \$(\"#genre\").val(rGenres);\n                            \$(\"#director\").val(\"\");\n                            \$(data.data.credits.crew).each(function(id, member) {\n                                if (member.department == \"Directing\") {\n                                    \$(\"#director\").val(member.name);\n                                    return true;\n                                }\n                            });\n                            \$(\"#plot\").val(data.data.overview);\n                            \$(\"#rating\").val(data.data.vote_average);\n                            \$(\"#tmdb_id\").val(data.data.id);\n\t\t\t\t\t\t\t\$(\"#viewtmdb\").attr(\"href\", \"//www.themoviedb.org/tv/\" + \$(\"#tmdb_id\").val());\n                        }\n                    });\n\t\t\t\t} else {\n\t\t\t\t\t\$(\"#tmdbid\").addClass('parsley-error');\n\t\t\t\t}\n            });\n\t\t\t\n\t\t\t\n\t\t\t\n\t\t\t\n\t\t\t\n\n            \$(\"#tmdb_search\").change(function() {\n                if ((\$(\"#tmdb_search\").val()) && (\$(\"#tmdb_search\").val() > -1)) {\n\t\t\t\t\t\$.getJSON(\"./api.php?action=tmdb&type=series&id=\" + \"&id=\" + \$(\"#tmdb_search\").val() + \"&tmdb_language=\" + \$(\"#tmdb_language\").val(), function(data) {\n                        if (data.result == true) {\n                            window.changeTitle = true;\n\t\t\t\t\t\t\t";
if ($rAdminSettings["tmdb_http_enable"]) {
    echo "                            \$(\"#title\").val(data.data.name);\n                            \$(\"#cover\").val(\"\");\n                            if (data.data.poster_path.length > 0) {\n                                \$(\"#cover\").val(\"http://image.tmdb.org/t/p/w600_and_h900_bestv2\" + data.data.poster_path);\n                            }\n                            \$(\"#backdrop_path\").val(\"\");\n                            if (data.data.backdrop_path.length > 0) {\n                                \$(\"#backdrop_path\").val(\"http://image.tmdb.org/t/p/w1280\" + data.data.backdrop_path);\n                            }\n\t\t\t\t\t\t\t";
} else {
    echo "\t\t\t\t\t\t\t\$(\"#title\").val(data.data.name);\n                            \$(\"#cover\").val(\"\");\n                            if (data.data.poster_path.length > 0) {\n                                \$(\"#cover\").val(\"https://image.tmdb.org/t/p/w600_and_h900_bestv2\" + data.data.poster_path);\n                            }\n                            \$(\"#backdrop_path\").val(\"\");\n                            if (data.data.backdrop_path.length > 0) {\n                                \$(\"#backdrop_path\").val(\"https://image.tmdb.org/t/p/w1280\" + data.data.backdrop_path);\n                            }\n\t\t\t\t\t\t\t";
}
echo "                            \$(\"#releaseDate\").val(data.data.first_air_date);\n                            \$(\"#episode_run_time\").val(data.data.episode_run_time[0]);\n                            \$(\"#youtube_trailer\").val(\"\");\n                            if (data.data.trailer) {\n                                \$(\"#youtube_trailer\").val(data.data.trailer);\n                            }\n                            rCast = \"\";\n                            rMemberID = 0;\n                            \$(data.data.credits.cast).each(function(id, member) {\n                                rMemberID += 1;\n                                if (rMemberID <= 5) {\n                                    if (rCast.length > 0) {\n                                        rCast += \", \";\n                                    }\n                                    rCast += member.name;\n                                }\n                            });\n                            \$(\"#cast\").val(rCast);\n                            rGenres = \"\";\n                            rGenreID = 0;\n                            \$(data.data.genres).each(function(id, genre) {\n                                rGenreID += 1;\n                                if (rGenreID <= 3) {\n                                    if (rGenres.length > 0) {\n                                        rGenres += \", \";\n                                    }\n                                    rGenres += genre.name;\n                                }\n                            });\n                            \$(\"#genre\").val(rGenres);\n                            \$(\"#director\").val(\"\");\n                            \$(data.data.credits.crew).each(function(id, member) {\n                                if (member.department == \"Directing\") {\n                                    \$(\"#director\").val(member.name);\n                                    return true;\n                                }\n                            });\n                            \$(\"#plot\").val(data.data.overview);\n                            \$(\"#rating\").val(data.data.vote_average);\n                            \$(\"#tmdb_id\").val(\$(\"#tmdb_search\").val());\n                        }\n                    });\n                }\n            });\n            \n            \$(\"#episode_run_time\").inputFilter(function(value) { return /^\\d*\$/.test(value); });\n            \$(\"form\").attr('autocomplete', 'off');\n            \n            ";
if (isset($rSeries)) {
    echo "            \$(\"#title\").trigger(\"change\");\n            ";
}
echo "        });\n        </script>\n    </body>\n</html>";

?>