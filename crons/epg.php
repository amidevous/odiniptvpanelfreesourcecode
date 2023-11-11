<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

if ($argc) {
    ini_set("memory_limit", -1);
    shell_exec("kill -9 `ps -ef | grep 'XtreamCodes\\[EPG\\]' | grep -v grep | awk '{print \$2}'`;");
    require str_replace("\\", "/", dirname($argv[0])) . "/../wwwdir/init.php";
    cli_set_process_title("XtreamCodes[EPG]");
    $f566700a43ee8e1f0412fe10fbdf03df->query("SELECT * FROM `epg`");
    foreach ($f566700a43ee8e1f0412fe10fbdf03df->C126FD559932f625Cdf6098D86c63880() as $c72d66b481d02f854f0bef67db92a547) {
        $C1037d0090aa4e7d78973574b5b0c906 = new E3223a8Ad822526d8F69418863B6e8b5($c72d66b481d02f854f0bef67db92a547["epg_file"]);
        if (!$C1037d0090aa4e7d78973574b5b0c906->validEpg) {
        } else {
            $f566700a43ee8e1f0412fe10fbdf03df->query("UPDATE `epg` SET `data` = '%s' WHERE `id` = '%d'", json_encode($C1037d0090aa4e7d78973574b5b0c906->a53d17aB9bd15890715E7947c1766953()), $c72d66b481d02f854f0bef67db92a547["id"]);
            $C1037d0090aa4e7d78973574b5b0c906 = NULL;
        }
    }
    $f566700a43ee8e1f0412fe10fbdf03df->query("SELECT DISTINCT(t1.`epg_id`),t2.* \n                    FROM `streams` t1\n                    INNER JOIN `epg` t2 ON t2.id = t1.epg_id\n                    WHERE t1.`epg_id` IS NOT NULL");
    $F640dfe0defc5175cce4037b873e2d3a = $f566700a43ee8e1f0412fe10fbdf03df->c126Fd559932f625CdF6098d86C63880();
    foreach ($F640dfe0defc5175cce4037b873e2d3a as $faca5f1c4c9dec5b739d7a905876b0cd) {
        if ($faca5f1c4c9dec5b739d7a905876b0cd["days_keep"] != 0) {
        } else {
            $f566700a43ee8e1f0412fe10fbdf03df->query("DELETE FROM `epg_data` WHERE `epg_id` = '%d'", $faca5f1c4c9dec5b739d7a905876b0cd["epg_id"]);
        }
        $C1037d0090aa4e7d78973574b5b0c906 = new E3223A8AD822526D8f69418863b6e8b5($faca5f1c4c9dec5b739d7a905876b0cd["epg_file"]);
        if (!$C1037d0090aa4e7d78973574b5b0c906->validEpg) {
        } else {
            $f566700a43ee8e1f0412fe10fbdf03df->query("SELECT                          t1.`channel_id`,                          t1.`epg_lang`,                          last_row.start                        FROM                          `streams` t1                        LEFT JOIN                          (                          SELECT                            channel_id,                            MAX(`start`) as start                          FROM                            epg_data                          WHERE                            epg_id = '%d'                          GROUP BY                            channel_id                        ) last_row ON last_row.channel_id = t1.channel_id                        WHERE                          `epg_id` = '%d';", $faca5f1c4c9dec5b739d7a905876b0cd["epg_id"], $faca5f1c4c9dec5b739d7a905876b0cd["epg_id"]);
            $A19ecb98ac2f5cd659422c29c53394dc = $f566700a43ee8e1f0412fe10fbdf03df->c126FD559932f625cDf6098D86C63880(true, "channel_id");
            $E06c818b92ce645bbbeb4a5536373854 = $C1037d0090aa4e7d78973574b5b0c906->a0b90401c3241088846a84f33c2B50Ff($faca5f1c4c9dec5b739d7a905876b0cd["epg_id"], $A19ecb98ac2f5cd659422c29c53394dc);
            $C48e0083a9caa391609a3c645a2ec889 = 0;
            while ($C48e0083a9caa391609a3c645a2ec889 >= count($E06c818b92ce645bbbeb4a5536373854)) {
                $f566700a43ee8e1f0412fe10fbdf03df->query("INSERT INTO `epg_data` (`epg_id`,`channel_id`,`start`,`end`,`lang`,`title`,`description`) VALUES " . $E06c818b92ce645bbbeb4a5536373854[$C48e0083a9caa391609a3c645a2ec889]);
                $C48e0083a9caa391609a3c645a2ec889++;
            }
            $f566700a43ee8e1f0412fe10fbdf03df->query("UPDATE `epg` SET `last_updated` = '%d' WHERE `id` = '%d'", time(), $faca5f1c4c9dec5b739d7a905876b0cd["epg_id"]);
        }
        if (0 >= $faca5f1c4c9dec5b739d7a905876b0cd["days_keep"]) {
        } else {
            $f566700a43ee8e1f0412fe10fbdf03df->query("DELETE FROM `epg_data` WHERE `epg_id` = '%d' AND `start` < '%s'", $faca5f1c4c9dec5b739d7a905876b0cd["epg_id"], date("Y-m-d H:i:00", strtotime("-" . $faca5f1c4c9dec5b739d7a905876b0cd["days_keep"] . " days")));
        }
    }
    $f566700a43ee8e1f0412fe10fbdf03df->query("DELETE n1 FROM `epg_data` n1, `epg_data` n2 WHERE n1.id < n2.id AND n1.epg_id = n2.epg_id AND n1.channel_id = n2.channel_id AND n1.start = n2.start AND n1.title = n2.title");
} else {
    exit(0);
}
class E3223A8AD822526D8F69418863b6E8b5
{
    public $validEpg = false;
    public $epgSource = NULL;
    public $from_cache = false;
    public function __construct($F3803fa85b38b65447e6d438f8e9176a, $F7b03a1f7467c01c6ea18452d9a5202f = false)
    {
        $this->eCe97c9fe9A866E5b522e80E43B30997($F3803fa85b38b65447e6d438f8e9176a, $F7b03a1f7467c01c6ea18452d9a5202f);
    }
    public function A53D17ab9bD15890715e7947c1766953()
    {
        $output = [];
        foreach ($this->epgSource->channel as $d76067cf9572f7a6691c85c12faf2a29) {
            $e818ebc908da0ee69f4f99daba6a1a18 = trim((string) $d76067cf9572f7a6691c85c12faf2a29->attributes()->id);
            $cfd246a8499e5bb4a9d89e37c524322a = !empty($d76067cf9572f7a6691c85c12faf2a29->{"display-name"}) ? trim((string) $d76067cf9572f7a6691c85c12faf2a29->{"display-name"}) : "";
            if (!array_key_exists($e818ebc908da0ee69f4f99daba6a1a18, $output)) {
                $output[$e818ebc908da0ee69f4f99daba6a1a18] = [];
                $output[$e818ebc908da0ee69f4f99daba6a1a18]["display_name"] = $cfd246a8499e5bb4a9d89e37c524322a;
                $output[$e818ebc908da0ee69f4f99daba6a1a18]["langs"] = [];
            }
        }
        foreach ($this->epgSource->programme as $d76067cf9572f7a6691c85c12faf2a29) {
            $e818ebc908da0ee69f4f99daba6a1a18 = trim((string) $d76067cf9572f7a6691c85c12faf2a29->attributes()->channel);
            if (array_key_exists($e818ebc908da0ee69f4f99daba6a1a18, $output)) {
                $b798ef834bcdc73cfeb4e4e0309db68d = $d76067cf9572f7a6691c85c12faf2a29->title;
                foreach ($b798ef834bcdc73cfeb4e4e0309db68d as $E4416ae8f96620daee43ac43f9515200) {
                    $lang = (string) $E4416ae8f96620daee43ac43f9515200->attributes()->lang;
                    if (in_array($lang, $output[$e818ebc908da0ee69f4f99daba6a1a18]["langs"])) {
                    } else {
                        $output[$e818ebc908da0ee69f4f99daba6a1a18]["langs"][] = $lang;
                    }
                }
            }
        }
        return $output;
    }
    public function A0B90401C3241088846a84f33c2B50fF($E2b08d0d6a74fb4e054587ee7c572a9f, $dfc6b62ce4c2bd11aeb45ae2e9441819)
    {
        global $f566700a43ee8e1f0412fe10fbdf03df;
        $f8f0da104ec866e0d96947b27214d28a = [];
        foreach ($this->epgSource->programme as $d76067cf9572f7a6691c85c12faf2a29) {
            $e818ebc908da0ee69f4f99daba6a1a18 = (string) $d76067cf9572f7a6691c85c12faf2a29->attributes()->channel;
            if (array_key_exists($e818ebc908da0ee69f4f99daba6a1a18, $dfc6b62ce4c2bd11aeb45ae2e9441819)) {
                $ff153ef1378baba89ae1f33db3ad14bf = $Fe7c1055293ad23ed4b69b91fd845cac = "";
                $start = strtotime(strval($d76067cf9572f7a6691c85c12faf2a29->attributes()->start));
                $stop = strtotime(strval($d76067cf9572f7a6691c85c12faf2a29->attributes()->stop));
                if (!empty($d76067cf9572f7a6691c85c12faf2a29->title)) {
                    $b798ef834bcdc73cfeb4e4e0309db68d = $d76067cf9572f7a6691c85c12faf2a29->title;
                    if (is_object($b798ef834bcdc73cfeb4e4e0309db68d)) {
                        $A2b796e1bb70296d4bed8ce34ce5691b = false;
                        foreach ($b798ef834bcdc73cfeb4e4e0309db68d as $E4416ae8f96620daee43ac43f9515200) {
                            if ($E4416ae8f96620daee43ac43f9515200->attributes()->lang != $dfc6b62ce4c2bd11aeb45ae2e9441819[$e818ebc908da0ee69f4f99daba6a1a18]["epg_lang"]) {
                            } else {
                                $A2b796e1bb70296d4bed8ce34ce5691b = true;
                                $ff153ef1378baba89ae1f33db3ad14bf = base64_encode($E4416ae8f96620daee43ac43f9515200);
                                if ($A2b796e1bb70296d4bed8ce34ce5691b) {
                                } else {
                                    $ff153ef1378baba89ae1f33db3ad14bf = base64_encode($b798ef834bcdc73cfeb4e4e0309db68d[0]);
                                }
                            }
                        }
                    } else {
                        $ff153ef1378baba89ae1f33db3ad14bf = base64_encode($b798ef834bcdc73cfeb4e4e0309db68d);
                    }
                    if (empty($d76067cf9572f7a6691c85c12faf2a29->desc)) {
                    } else {
                        $d1294148eb5638fe195478093cd6b93b = $d76067cf9572f7a6691c85c12faf2a29->desc;
                        if (is_object($d1294148eb5638fe195478093cd6b93b)) {
                            $A2b796e1bb70296d4bed8ce34ce5691b = false;
                            foreach ($d1294148eb5638fe195478093cd6b93b as $d4c3c80b508f5d00d05316e7aa0858de) {
                                if ($d4c3c80b508f5d00d05316e7aa0858de->attributes()->lang != $dfc6b62ce4c2bd11aeb45ae2e9441819[$e818ebc908da0ee69f4f99daba6a1a18]["epg_lang"]) {
                                } else {
                                    $A2b796e1bb70296d4bed8ce34ce5691b = true;
                                    $Fe7c1055293ad23ed4b69b91fd845cac = base64_encode($d4c3c80b508f5d00d05316e7aa0858de);
                                    if ($A2b796e1bb70296d4bed8ce34ce5691b) {
                                    } else {
                                        $Fe7c1055293ad23ed4b69b91fd845cac = base64_encode($d1294148eb5638fe195478093cd6b93b[0]);
                                    }
                                }
                            }
                        } else {
                            $Fe7c1055293ad23ed4b69b91fd845cac = base64_encode($d76067cf9572f7a6691c85c12faf2a29->desc);
                        }
                    }
                    $e818ebc908da0ee69f4f99daba6a1a18 = addslashes($e818ebc908da0ee69f4f99daba6a1a18);
                    $dfc6b62ce4c2bd11aeb45ae2e9441819[$e818ebc908da0ee69f4f99daba6a1a18]["epg_lang"] = addslashes($dfc6b62ce4c2bd11aeb45ae2e9441819[$e818ebc908da0ee69f4f99daba6a1a18]["epg_lang"]);
                    $A73d5129dfb465fd94f3e09e9b179de0 = date("Y-m-d H:i:s", $start);
                    $cdd6af41b10abec2ff03fe043f3df1cf = date("Y-m-d H:i:s", $stop);
                    $f8f0da104ec866e0d96947b27214d28a[] = "('" . $f566700a43ee8e1f0412fe10fbdf03df->escape($E2b08d0d6a74fb4e054587ee7c572a9f) . "', '" . $f566700a43ee8e1f0412fe10fbdf03df->escape($e818ebc908da0ee69f4f99daba6a1a18) . "', '" . $f566700a43ee8e1f0412fe10fbdf03df->escape($A73d5129dfb465fd94f3e09e9b179de0) . "', '" . $f566700a43ee8e1f0412fe10fbdf03df->escape($cdd6af41b10abec2ff03fe043f3df1cf) . "', '" . $f566700a43ee8e1f0412fe10fbdf03df->escape($dfc6b62ce4c2bd11aeb45ae2e9441819[$e818ebc908da0ee69f4f99daba6a1a18]["epg_lang"]) . "', '" . $f566700a43ee8e1f0412fe10fbdf03df->escape($ff153ef1378baba89ae1f33db3ad14bf) . "', '" . $f566700a43ee8e1f0412fe10fbdf03df->escape($Fe7c1055293ad23ed4b69b91fd845cac) . "')";
                }
            }
        }
        return !empty($f8f0da104ec866e0d96947b27214d28a) ? $f8f0da104ec866e0d96947b27214d28a : false;
    }
    public function eCe97C9fE9a866e5B522E80E43B30997($F3803fa85b38b65447e6d438f8e9176a, $F7b03a1f7467c01c6ea18452d9a5202f)
    {
        $F1350a5569e4b73d2f9cb26483f2a0c1 = pathinfo($F3803fa85b38b65447e6d438f8e9176a, PATHINFO_EXTENSION);
        if ($F1350a5569e4b73d2f9cb26483f2a0c1 == "gz") {
            $d31de515789f8101b06d8ca646ef5e24 = gzdecode(file_get_contents($F3803fa85b38b65447e6d438f8e9176a));
            $a41f6a5b2ce6655f27b7747349ad1f33 = simplexml_load_string($d31de515789f8101b06d8ca646ef5e24, "SimpleXMLElement", LIBXML_COMPACT | LIBXML_PARSEHUGE);
        } else {
            if ($F1350a5569e4b73d2f9cb26483f2a0c1 == "xz") {
                $d31de515789f8101b06d8ca646ef5e24 = shell_exec("wget -qO- \"" . $F3803fa85b38b65447e6d438f8e9176a . "\" | unxz -c");
                $a41f6a5b2ce6655f27b7747349ad1f33 = simplexml_load_string($d31de515789f8101b06d8ca646ef5e24, "SimpleXMLElement", LIBXML_COMPACT | LIBXML_PARSEHUGE);
            } else {
                $d31de515789f8101b06d8ca646ef5e24 = file_get_contents($F3803fa85b38b65447e6d438f8e9176a);
                $a41f6a5b2ce6655f27b7747349ad1f33 = simplexml_load_string($d31de515789f8101b06d8ca646ef5e24, "SimpleXMLElement", LIBXML_COMPACT | LIBXML_PARSEHUGE);
            }
        }
        if ($a41f6a5b2ce6655f27b7747349ad1f33 !== false) {
            $this->epgSource = $a41f6a5b2ce6655f27b7747349ad1f33;
            if (empty($this->epgSource->programme)) {
                a78bf8D35765BE2408c50712Ce7A43ad::E501281AD19Af8A4bbBF9bed91Ee9299("Not A Valid EPG Source Specified or EPG Crashed: " . $F3803fa85b38b65447e6d438f8e9176a);
            } else {
                $this->validEpg = true;
            }
        } else {
            A78bF8d35765bE2408c50712Ce7a43aD::E501281AD19aF8a4BbBF9BEd91ee9299("No XML Found At: " . $F3803fa85b38b65447e6d438f8e9176a);
        }
        $a41f6a5b2ce6655f27b7747349ad1f33 = $d31de515789f8101b06d8ca646ef5e24 = NULL;
    }
}

?>