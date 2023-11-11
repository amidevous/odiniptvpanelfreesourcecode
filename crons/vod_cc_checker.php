<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

if ($argc) {
    require str_replace("\\", "/", dirname($argv[0])) . "/../wwwdir/init.php";
    $Ed756578679cd59095dfa81f228e8b38 = TMP_DIR . md5(aFFb052CCA396818D81004Ff99db49aA() . __FILE__);
    BBD9e78ac32626E138e758e840305a7C($Ed756578679cd59095dfa81f228e8b38);
    cli_set_process_title("XtreamCodes[VOD CC Checker]");
    ini_set("memory_limit", -1);
    $f566700a43ee8e1f0412fe10fbdf03df->query("SELECT * FROM `streams` t1\n                  INNER JOIN `transcoding_profiles` t2 ON t2.profile_id = t1.transcode_profile_id\n                  WHERE t1.type = 3\n                ");
    if (0 < $f566700a43ee8e1f0412fe10fbdf03df->d1e5CE3b87bB868B9E6EfD39aA355a4f()) {
        $D465fc5085f41251c6fa7c77b8333b0f = $f566700a43ee8e1f0412fe10fbdf03df->C126fd559932F625CDf6098D86c63880();
        foreach ($D465fc5085f41251c6fa7c77b8333b0f as $c3a18c26bfa971a25d2e6ada870ff735) {
            echo "\n\n[*] Checking Stream " . $c3a18c26bfa971a25d2e6ada870ff735["stream_display_name"] . "\n";
            e3cf480c172e8b47FE10857C2A5aEb48::EeED2f36fa093B45bC2d622eD0231684($c3a18c26bfa971a25d2e6ada870ff735["id"]);
            switch (e3cf480c172e8b47FE10857C2A5aEb48::EeED2f36fa093B45bC2d622eD0231684($c3a18c26bfa971a25d2e6ada870ff735["id"])) {
                case 1:
                    echo "\tBuild Is Still Going!\n";
                    break;
                case 2:
                    echo "\tBuild Finished\n";
                    break;
            }
        }
    }
    $A5edd58fb5d148d909e5e9e279ec2ffc = a7785208D901Bea02b65446067CFD0b3::b95e6892fb5B229151aaFF96d4D172e3(SERVER_ID, FFMPEG_PATH);
    $f566700a43ee8e1f0412fe10fbdf03df->query("SELECT t1.*,t2.* FROM `streams_sys` t1 \n                INNER JOIN `streams` t2 ON t2.id = t1.stream_id AND t2.direct_source = 0\n                INNER JOIN `streams_types` t3 ON t3.type_id = t2.type AND t3.live = 0\n                WHERE (t1.to_analyze = 1 OR t1.stream_status = 2) AND t1.server_id = '%d'", SERVER_ID);
    if (0 < $f566700a43ee8e1f0412fe10fbdf03df->D1e5ce3B87bB868b9E6EFD39AA355A4F()) {
        $Cd4eabf7ecf553f46c17f0bd5a382c46 = $f566700a43ee8e1f0412fe10fbdf03df->C126FD559932f625CDf6098d86C63880();
        foreach ($Cd4eabf7ecf553f46c17f0bd5a382c46 as $c72d66b481d02f854f0bef67db92a547) {
            echo "[*] Checking Movie " . $c72d66b481d02f854f0bef67db92a547["stream_display_name"] . " ON Server ID " . $c72d66b481d02f854f0bef67db92a547["server_id"] . " \t\t---> ";
            if ($c72d66b481d02f854f0bef67db92a547["to_analyze"] == 1) {
                if (!empty($A5edd58fb5d148d909e5e9e279ec2ffc[$c72d66b481d02f854f0bef67db92a547["server_id"]]) && in_array($c72d66b481d02f854f0bef67db92a547["pid"], $A5edd58fb5d148d909e5e9e279ec2ffc[$c72d66b481d02f854f0bef67db92a547["server_id"]])) {
                    echo "WORKING\n";
                } else {
                    echo "\n\n\n";
                    $ecb89a457f7f7216f5564141edfd6269 = json_decode($c72d66b481d02f854f0bef67db92a547["target_container"], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $c72d66b481d02f854f0bef67db92a547["target_container"] = $ecb89a457f7f7216f5564141edfd6269;
                    } else {
                        $c72d66b481d02f854f0bef67db92a547["target_container"] = [$c72d66b481d02f854f0bef67db92a547["target_container"]];
                    }
                    $c72d66b481d02f854f0bef67db92a547["target_container"] = $c72d66b481d02f854f0bef67db92a547["target_container"][0];
                    $ed147a39fb35be93248b6f1c206a8023 = MOVIES_PATH . $c72d66b481d02f854f0bef67db92a547["stream_id"] . "." . $c72d66b481d02f854f0bef67db92a547["target_container"];
                    if ($Ec610f8d82d35339f680a3ec9bbc078c = E3cF480C172e8b47fE10857c2A5aeb48::e0a1164567005185e0818F081674e240($ed147a39fb35be93248b6f1c206a8023, $c72d66b481d02f854f0bef67db92a547["server_id"])) {
                        $fd08711a26bab44719872c7fff1f2dfb = isset($Ec610f8d82d35339f680a3ec9bbc078c["duration"]) ? $Ec610f8d82d35339f680a3ec9bbc078c["duration"] : 0;
                        sscanf($fd08711a26bab44719872c7fff1f2dfb, "%d:%d:%d", $fd8f2c4ad459c3f2b875636e5d3ac6a7, $Bc1d36e0762a7ca0e7cbaddd76686790, $Ba3faa92a82fb2d1bb6bb866cb272fee);
                        $Bed5705166e68002911f53d0e71685f5 = isset($Ba3faa92a82fb2d1bb6bb866cb272fee) ? $fd8f2c4ad459c3f2b875636e5d3ac6a7 * 3600 + $Bc1d36e0762a7ca0e7cbaddd76686790 * 60 + $Ba3faa92a82fb2d1bb6bb866cb272fee : $fd8f2c4ad459c3f2b875636e5d3ac6a7 * 60 + $Bc1d36e0762a7ca0e7cbaddd76686790;
                        $Ff876e96994aa5b09ce92e771efe2038 = a7785208d901bEa02b65446067CfD0b3::F320b6a3920944D8a18d7949C8aBaCe4($c72d66b481d02f854f0bef67db92a547["server_id"], "wc -c < " . $ed147a39fb35be93248b6f1c206a8023, "raw");
                        $D2f61e797d44efa20d9d559b2fc2c039 = round($Ff876e96994aa5b09ce92e771efe2038[$c72d66b481d02f854f0bef67db92a547["server_id"]] * 0 / $Bed5705166e68002911f53d0e71685f5);
                        $f3f2a9f7d64ad754f9f888f441df853a = json_decode($c72d66b481d02f854f0bef67db92a547["movie_propeties"], true);
                        if (is_array($f3f2a9f7d64ad754f9f888f441df853a)) {
                        } else {
                            $f3f2a9f7d64ad754f9f888f441df853a = [];
                        }
                        if (isset($f3f2a9f7d64ad754f9f888f441df853a["duration_secs"]) && $Bed5705166e68002911f53d0e71685f5 == $f3f2a9f7d64ad754f9f888f441df853a["duration_secs"]) {
                        } else {
                            $f3f2a9f7d64ad754f9f888f441df853a["duration_secs"] = $Bed5705166e68002911f53d0e71685f5;
                            $f3f2a9f7d64ad754f9f888f441df853a["duration"] = $fd08711a26bab44719872c7fff1f2dfb;
                        }
                        if (isset($f3f2a9f7d64ad754f9f888f441df853a["video"]) && $Ec610f8d82d35339f680a3ec9bbc078c["codecs"]["video"]["codec_name"] == $f3f2a9f7d64ad754f9f888f441df853a["video"]) {
                        } else {
                            $f3f2a9f7d64ad754f9f888f441df853a["video"] = $Ec610f8d82d35339f680a3ec9bbc078c["codecs"]["video"];
                        }
                        if (isset($f3f2a9f7d64ad754f9f888f441df853a["audio"]) && $Ec610f8d82d35339f680a3ec9bbc078c["codecs"]["audio"]["codec_name"] == $f3f2a9f7d64ad754f9f888f441df853a["audio"]) {
                        } else {
                            $f3f2a9f7d64ad754f9f888f441df853a["audio"] = $Ec610f8d82d35339f680a3ec9bbc078c["codecs"]["audio"];
                        }
                        if (isset($f3f2a9f7d64ad754f9f888f441df853a["bitrate"]) && $D2f61e797d44efa20d9d559b2fc2c039 == $f3f2a9f7d64ad754f9f888f441df853a["bitrate"]) {
                        } else {
                            $f3f2a9f7d64ad754f9f888f441df853a["bitrate"] = $D2f61e797d44efa20d9d559b2fc2c039;
                        }
                        $f566700a43ee8e1f0412fe10fbdf03df->query("UPDATE `streams` SET `movie_propeties` = '%s' WHERE `id` = '%d'", json_encode($f3f2a9f7d64ad754f9f888f441df853a), $c72d66b481d02f854f0bef67db92a547["stream_id"]);
                        $f566700a43ee8e1f0412fe10fbdf03df->query("UPDATE `streams_sys` SET `bitrate` = '%d',`to_analyze` = 0,`stream_status` = 0,`stream_info` = '%s'  WHERE `server_stream_id` = '%d'", $D2f61e797d44efa20d9d559b2fc2c039, json_encode($Ec610f8d82d35339f680a3ec9bbc078c), $c72d66b481d02f854f0bef67db92a547["server_stream_id"]);
                        echo "VALID\n";
                    } else {
                        $f566700a43ee8e1f0412fe10fbdf03df->query("UPDATE `streams_sys` SET `to_analyze` = 0,`stream_status` = 1  WHERE `server_stream_id` = '%d'", $c72d66b481d02f854f0bef67db92a547["server_stream_id"]);
                        echo "BAD MOVIE\n";
                    }
                }
            } else {
                echo "NO ACTION\n";
            }
        }
    }
} else {
    exit(0);
}

?>