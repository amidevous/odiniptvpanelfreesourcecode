<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

set_time_limit(0);
ini_set("memory_limit", -1);
if ($argc) {
    require str_replace("\\", "/", dirname($argv[0])) . "/../wwwdir/init.php";
    cli_set_process_title("XtreamCodes[Users Parser]");
    $Ed756578679cd59095dfa81f228e8b38 = TMP_DIR . md5(AFFb052cca396818D81004fF99dB49aa() . __FILE__);
    bbd9e78AC32626e138e758e840305A7C($Ed756578679cd59095dfa81f228e8b38);
    $c910879e78df4d0416f541527f6fe0fa = A78bf8D35765Be2408c50712Ce7A43Ad::$settings["user_auto_kick_hours"] * 3600;
    $ddbd898760d5c96a0ba50b5036daf027 = [];
    $Bcf87c9b8f60adb6d7364a2c5c48f8d8 = f0Bb8dBeaB7Fb0ECCcC0A73980Dbf47a("open");
    $a92e6212ebeeb6b4443bc635ccaab4b3 = explode("\n", shell_exec("find /home/xtreamcodes/iptv_xtream_codes/tmp/ -maxdepth 1 -name \"*.con\" -print0 | xargs -0 grep \"\" -H"));
    shell_exec("rm -f /home/xtreamcodes/iptv_xtream_codes/tmp/*.con");
    foreach ($Bcf87c9b8f60adb6d7364a2c5c48f8d8 as $E38668abaa324e464e266fb7b7e784b1 => $E80aae019385d9c9558555fb07017028) {
        $f90851c60f1d8fbc667db36343a9d05d = count($E80aae019385d9c9558555fb07017028);
        foreach ($E80aae019385d9c9558555fb07017028 as $E7cca48cfca85fc445419a32d7d8f973 => $Ca70a741859162e34df7986a31778a14) {
            if (!($Ca70a741859162e34df7986a31778a14["max_connections"] != 0 && $Ca70a741859162e34df7986a31778a14["max_connections"] < $f90851c60f1d8fbc667db36343a9d05d)) {
                if ($Ca70a741859162e34df7986a31778a14["server_id"] != SERVER_ID) {
                } else {
                    if (is_null($Ca70a741859162e34df7986a31778a14["exp_date"]) || $Ca70a741859162e34df7986a31778a14["exp_date"] >= time()) {
                        $E3425a71ab35e84bcfc9c7f23adb8df4 = time() - $Ca70a741859162e34df7986a31778a14["date_start"];
                        if (!($c910879e78df4d0416f541527f6fe0fa != 0 && $c910879e78df4d0416f541527f6fe0fa <= $E3425a71ab35e84bcfc9c7f23adb8df4 && $Ca70a741859162e34df7986a31778a14["is_restreamer"] == 0)) {
                            if ($Ca70a741859162e34df7986a31778a14["container"] == "hls") {
                                if (!(60 <= time() - $Ca70a741859162e34df7986a31778a14["hls_last_read"] || $Ca70a741859162e34df7986a31778a14["hls_end"] == 1)) {
                                } else {
                                    echo "[+] Closing ENDED Con HLS: " . $Ca70a741859162e34df7986a31778a14["activity_id"] . "\n";
                                    cD89785224751CcA8017139daf9e891e::a1EaE86369aa95a55b4BE332f1e22FE3($Ca70a741859162e34df7986a31778a14);
                                    $f90851c60f1d8fbc667db36343a9d05d--;
                                }
                            } else {
                                if ($Ca70a741859162e34df7986a31778a14["container"] != "rtmp") {
                                    if (Cd89785224751CCa8017139daF9e891e::ps_running($Ca70a741859162e34df7986a31778a14["pid"], "php-fpm")) {
                                    } else {
                                        echo "[+] Closing Connection (Closed UnExp): " . $Ca70a741859162e34df7986a31778a14["activity_id"] . "\n";
                                        cD89785224751cca8017139DaF9E891E::a1EAe86369aa95A55B4BE332f1E22FE3($Ca70a741859162e34df7986a31778a14);
                                    }
                                }
                                $ddbd898760d5c96a0ba50b5036daf027[$Ca70a741859162e34df7986a31778a14["activity_id"]] = intval($Ca70a741859162e34df7986a31778a14["bitrate"] / 8 * 0);
                            }
                        } else {
                            echo "[+] Closing Connection[KICK TIME ONLINE]: " . $Ca70a741859162e34df7986a31778a14["activity_id"] . "\n";
                            cd89785224751cCA8017139daf9e891E::a1eAe86369aa95A55B4Be332f1e22fe3($Ca70a741859162e34df7986a31778a14);
                            $f90851c60f1d8fbc667db36343a9d05d--;
                        }
                    } else {
                        echo "[+] Closing Connection: " . $Ca70a741859162e34df7986a31778a14["activity_id"] . "\n";
                        cD89785224751CCA8017139daf9e891E::A1EAE86369AA95A55B4be332F1e22Fe3($Ca70a741859162e34df7986a31778a14);
                        $f90851c60f1d8fbc667db36343a9d05d = 0;
                    }
                }
            } else {
                echo "[+] Closing Connection caused max Connections overflow...\n";
                Cd89785224751CCa8017139daF9e891E::A1Eae86369Aa95a55b4be332f1e22Fe3($Ca70a741859162e34df7986a31778a14);
                $f90851c60f1d8fbc667db36343a9d05d--;
            }
        }
    }
    foreach ($a92e6212ebeeb6b4443bc635ccaab4b3 as $Ce47bb1f0df114d58acb6d0794c04a6f) {
        if (!empty($Ce47bb1f0df114d58acb6d0794c04a6f)) {
            list($a00d379ae275b2c1c76602c62ea25a1e, $f76e52f3fb075f170ef135646478b252) = explode(":", basename($Ce47bb1f0df114d58acb6d0794c04a6f));
            list($E821605d1d9382d422040b86d29632d9, $Faf66509f7083ec5a58786b5a05efd17) = explode(".", $a00d379ae275b2c1c76602c62ea25a1e);
            if (isset($ddbd898760d5c96a0ba50b5036daf027[$E821605d1d9382d422040b86d29632d9])) {
                $cd928459ba4109cd579cea10a8cb5bc3 = intval(($f76e52f3fb075f170ef135646478b252 - $ddbd898760d5c96a0ba50b5036daf027[$E821605d1d9382d422040b86d29632d9]) / $ddbd898760d5c96a0ba50b5036daf027[$E821605d1d9382d422040b86d29632d9] * 100);
                if (0 >= $cd928459ba4109cd579cea10a8cb5bc3) {
                } else {
                    $cd928459ba4109cd579cea10a8cb5bc3 = 0;
                }
                $f566700a43ee8e1f0412fe10fbdf03df->query("UPDATE `user_activity_now` SET `divergence` = '%d' WHERE `activity_id` = '%d'", abs($cd928459ba4109cd579cea10a8cb5bc3), $E821605d1d9382d422040b86d29632d9);
            } else {
                @unlink(TMP_DIR . $a00d379ae275b2c1c76602c62ea25a1e);
            }
        }
    }
    @unlink($Ed756578679cd59095dfa81f228e8b38);
} else {
    exit(0);
}

?>