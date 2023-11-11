<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include "functions.php";
if (!isset($_SESSION["hash"])) {
    header("Location: ./login.php");
    exit;
}
if (!$rPermissions["is_admin"]) {
    exit;
}
$ACCESS_PWD = $_INFO["db_pass"];
$DBDEF = ["user" => $_INFO["db_user"], "pwd" => $_INFO["db_pass"], "db" => $_INFO["db_name"], "host" => $_INFO["host"], "port" => $_INFO["db_port"], "socket" => "", "chset" => "utf8mb4", "ssl_key" => NULL, "ssl_cert" => NULL, "ssl_ca" => ""];
$IS_COUNT = false;
$DUMP_FILE = dirname(__FILE__) . "/pmadump";
if (function_exists("date_default_timezone_set")) {
    date_default_timezone_set("UTC");
}
$VERSION = "1.9.210705";
$MAX_ROWS_PER_PAGE = 50;
$D = "\r\n";
$BOM = chr(239) . chr(187) . chr(191);
$SHOW_D = "SHOW DATABASES";
$SHOW_T = "SHOW TABLE STATUS";
$DB = [];
$self = $_SERVER["PHP_SELF"];
session_write_close();
session_set_cookie_params(0, NULL, NULL, false, true);
session_start();
if (!isset($_SESSION["XSS"])) {
    $_SESSION["XSS"] = get_rand_str(16);
}
$xurl = "XSS=" . $_SESSION["XSS"];
ini_set("display_errors", 0);
error_reporting(32759);
if (get_magic_quotes_gpc()) {
    $_COOKIE = array_map("killmq", $_COOKIE);
    $_REQUEST = array_map("killmq", $_REQUEST);
}
if ($_REQUEST["login"]) {
    if ($_REQUEST["pwd"] != $ACCESS_PWD) {
        $err_msg = "Invalid password. Try again";
    } else {
        $_SESSION["is_logged"] = true;
        loadcfg();
    }
}
if ($_REQUEST["logoff"]) {
    check_xss();
    $_SESSION = [];
    savecfg();
    session_destroy();
    $url = $self;
    if (!$ACCESS_PWD) {
        $url = "/";
    }
    header("location: " . $url);
    exit;
}
if (!$_SESSION["is_logged"]) {
    if (!$ACCESS_PWD) {
        $_SESSION["is_logged"] = true;
        loadcfg();
    } else {
        print_login();
        exit;
    }
}
if ($_REQUEST["savecfg"]) {
    check_xss();
    savecfg();
}
loadsess();
if ($_REQUEST["showcfg"]) {
    print_cfg();
    exit;
}
$SQLq = trim(b64d($_REQUEST["q"]));
$page = intval($_REQUEST["p"]);
if ($_REQUEST["refresh"] && $DB["db"] && preg_match("/^show/", $SQLq)) {
    $SQLq = $SHOW_T;
}
if (db_connect("nodie")) {
    $time_start = microtime_float();
    if ($_REQUEST["pi"]) {
        ob_start();
        phpinfo();
        $html = ob_get_clean();
        preg_match("/<body[^>]*>(.*?)<\\/body>/is", $html, $m);
        $sqldr = "<div class=\"pi\">" . $m[1] . "</div>";
    } else {
        if ($DB["db"]) {
            if ($_REQUEST["shex"]) {
                print_export();
            } else {
                if ($_REQUEST["doex"]) {
                    check_xss();
                    do_export();
                } else {
                    if ($_REQUEST["shim"]) {
                        print_import();
                    } else {
                        if ($_REQUEST["doim"]) {
                            check_xss();
                            do_import();
                        } else {
                            if ($_REQUEST["dosht"]) {
                                check_xss();
                                do_sht();
                            } else {
                                if (!$_REQUEST["refresh"] || preg_match("/^select|show|explain|desc/i", $SQLq)) {
                                    if ($SQLq) {
                                        check_xss();
                                    }
                                    do_sql($SQLq);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if ($_REQUEST["refresh"]) {
                check_xss();
                do_sql($SHOW_D);
            } else {
                if ($_REQUEST["crdb"]) {
                    check_xss();
                    do_sql("CREATE DATABASE `" . $_REQUEST["new_db"] . "`");
                    do_sql($SHOW_D);
                } else {
                    if (preg_match("/^(?:show\\s+(?:databases|status|variables|process)|create\\s+database|grant\\s+)/i", $SQLq)) {
                        check_xss();
                        do_sql($SQLq);
                    } else {
                        $err_msg = "Select Database first";
                        if (!$SQLq) {
                            do_sql($SHOW_D);
                        }
                    }
                }
            }
        }
    }
    $time_all = ceil((microtime_float() - $time_start) * 10000) / 10000;
    print_screen();
} else {
    print_cfg();
}
function do_sql($q)
{
    global $dbh;
    global $last_sth;
    global $last_sql;
    global $reccount;
    global $out_message;
    global $SQLq;
    global $SHOW_T;
    $SQLq = $q;
    if (!do_multi_sql($q)) {
        $out_message = "Error: " . mysqli_error($dbh);
    } else {
        if ($last_sth && $last_sql) {
            $SQLq = $last_sql;
            if (preg_match("/^select|show|explain|desc/i", $last_sql)) {
                if ($q != $last_sql) {
                    $out_message = "Results of the last select displayed:";
                }
                display_select($last_sth, $last_sql);
            } else {
                $reccount = mysqli_affected_rows($dbh);
                $out_message = "Done.";
                if (preg_match("/^insert|replace/i", $last_sql)) {
                    $out_message .= " Last inserted id=" . get_identity();
                }
                if (preg_match("/^drop|truncate/i", $last_sql)) {
                    do_sql($SHOW_T);
                }
            }
        }
    }
}
function display_select($sth, $q)
{
    global $dbh;
    global $DB;
    global $sqldr;
    global $reccount;
    global $is_sht;
    global $xurl;
    global $is_sm;
    $rc = ["o", "e"];
    $dbn = ue($DB["db"]);
    $sqldr = "";
    $is_shd = preg_match("/^show\\s+databases/i", $q);
    $is_sht = preg_match("/^show\\s+tables|^SHOW\\s+TABLE\\s+STATUS/", $q);
    $is_show_crt = preg_match("/^show\\s+create\\s+table/i", $q);
    if ($sth === false || $sth === true) {
        return NULL;
    }
    $reccount = mysqli_num_rows($sth);
    $fields_num = mysqli_field_count($dbh);
    $w = "";
    if ($is_sm) {
        $w = "sm ";
    }
    if ($is_sht || $is_shd) {
        $w = "wa";
        $url = "?" . $xurl . "&db=" . $dbn;
        $sqldr .= "<div class='dot'>\n MySQL Server:\n &#183; <a href='" . $url . "&q=" . b64u("show variables") . "'>Show Configuration Variables</a>\n &#183; <a href='" . $url . "&q=" . b64u("show status") . "'>Show Statistics</a>\n &#183; <a href='" . $url . "&q=" . b64u("show processlist") . "'>Show Processlist</a> ";
        if ($is_shd) {
            $sqldr .= "&#183; <label>Create new database: <input type='text' name='new_db' placeholder='type db name here'></label> <input type='submit' name='crdb' value='Create'>";
        }
        $sqldr .= "<br>";
        if ($is_sht) {
            $sqldr .= "Database: &#183; <a href='" . $url . "&q=" . b64u("show table status") . "'>Show Table Status</a>";
        }
        $sqldr .= "</div>";
    }
    $abtn = "";
    if ($is_sht) {
        $abtn = "<div><input type='submit' value='Export' onclick=\"sht('exp')\">\n <input type='submit' value='Drop' onclick=\"if(ays()){sht('drop')}else{return false}\">\n <input type='submit' value='Truncate' onclick=\"if(ays()){sht('trunc')}else{return false}\">\n <input type='submit' value='Optimize' onclick=\"sht('opt')\">\n <b>selected tables</b></div>";
        $sqldr .= $abtn . "<input type='hidden' name='dosht' value=''>";
    }
    $sqldr .= "<div><table id='res' class='res " . $w . "'>";
    $headers = "<tr class='h'>";
    if ($is_sht) {
        $headers .= "<td><input type='checkbox' name='cball' value='' onclick='chkall(this)'></td>";
    }
    $i = 0;
    while ($i < $fields_num) {
        if (!($is_sht && 0 < $i)) {
            $meta = mysqli_fetch_field($sth);
            $headers .= "<th><div>" . hs($meta->name) . "</div></th>";
            $i++;
        }
    }
    if ($is_shd) {
        $headers .= "<th>show create database</th><th>show table status</th><th>show triggers</th>";
    }
    if ($is_sht) {
        $headers .= "<th>engine</th><th>~rows</th><th>data size</th><th>index size</th><th>show create table</th><th>explain</th><th>indexes</th><th>export</th><th>drop</th><th>truncate</th><th>optimize</th><th>repair</th><th>comment</th>";
    }
    $headers .= "</tr>\n";
    $sqldr .= $headers;
    $swapper = false;
    $swp = 0;
    while ($row = mysqli_fetch_row($sth)) {
        $sqldr .= "<tr class='" . $rc[$swp = !$swp] . "' onclick='tc(this)'>";
        $v = $row[0];
        if ($is_sht) {
            $vq = "`" . $v . "`";
            $url = "?" . $xurl . "&db=" . $dbn . "&t=" . b64u($v);
            $sqldr .= "<td><input type='checkbox' name='cb[]' value=\"" . hs($vq) . "\"></td>" . "<td><a href=\"" . $url . "&q=" . b64u("select * from " . $vq) . "\">" . hs($v) . "</a></td>" . "<td>" . hs($row[1]) . "</td>" . "<td align='right'>" . hs($row[4]) . "</td>" . "<td align='right'>" . hs($row[6]) . "</td>" . "<td align='right'>" . hs($row[8]) . "</td>" . "<td>&#183;<a href=\"" . $url . "&q=" . b64u("show create table " . $vq) . "\">sct</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=" . b64u("explain " . $vq) . "\">exp</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=" . b64u("show index from " . $vq) . "\">ind</a></td>" . "<td>&#183;<a href=\"" . $url . "&shex=1&rt=" . hs(ue($vq)) . "\">export</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=" . b64u("drop table " . $vq) . "\" onclick='return ays()'>dr</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=" . b64u("truncate table " . $vq) . "\" onclick='return ays()'>tr</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=" . b64u("optimize table " . $vq) . "\" onclick='return ays()'>opt</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=" . b64u("repair table " . $vq) . "\" onclick='return ays()'>rpr</a></td>" . "<td>" . hs($row[$fields_num - 1]) . "</td>";
        } else {
            if ($is_shd) {
                $url = "?" . $xurl . "&db=" . ue($v);
                $sqldr .= "<td><a href=\"" . $url . "&q=" . b64u("SHOW TABLE STATUS") . "\">" . hs($v) . "</a></td>" . "<td><a href=\"" . $url . "&q=" . b64u("show create database `" . $v . "`") . "\">scd</a></td>" . "<td><a href=\"" . $url . "&q=" . b64u("show table status") . "\">status</a></td>" . "<td><a href=\"" . $url . "&q=" . b64u("show triggers") . "\">trig</a></td>";
            } else {
                for ($i = 0; $i < $fields_num; $i++) {
                    $v = $row[$i];
                    if (is_null($v)) {
                        $v = "<i>NULL</i>";
                    } else {
                        if (preg_match("/[\\x00-\\x09\\x0B\\x0C\\x0E-\\x1F]+/", $v)) {
                            $vl = strlen($v);
                            $pf = "";
                            if (16 < $vl && 1 < $fields_num) {
                                $v = substr($v, 0, 16);
                                $pf = "...";
                            }
                            $v = "BINARY: " . chunk_split(strtoupper(bin2hex($v)), 2, " ") . $pf;
                        } else {
                            $v = hs($v);
                        }
                    }
                    if ($is_show_crt) {
                        $v = "<pre>" . $v . "</pre>";
                    }
                    $sqldr .= "<td><div>" . $v . (!strlen($v) ? "<br>" : "") . "</div></td>";
                }
            }
        }
        $sqldr .= "</tr>\n";
    }
    $sqldr .= "</table></div>\n" . $abtn;
}
function print_header()
{
    global $err_msg;
    global $VERSION;
    global $DB;
    global $dbh;
    global $self;
    global $is_sht;
    global $xurl;
    global $SHOW_T;
    $dbn = $DB["db"];
    echo "<!DOCTYPE html>\n<html>\n<head><title>phpMiniAdmin</title>\n<meta charset=\"utf-8\">\n<style type=\"text/css\">\n*{box-sizing:border-box;}\nbody{font-family:Arial,sans-serif;font-size:80%;padding:0;margin:0}\ndiv{padding:3px}\npre{font-size:125%}\ntextarea{width:100%}\n.nav{text-align:center}\n.ft{text-align:right;margin-top:20px;font-size:smaller}\n.inv{background-color:#069;color:#FFF}\n.inv a{color:#FFF}\ntable{border-collapse:collapse}\ntable.res{width:100%}\ntable.wa{width:auto}\ntable.res th,table.res td{padding:2px;border:1px solid #fff;vertical-align:top}\ntable.sm th,table.sm td{max-width:30em}\ntable.sm th>div,table.sm td>div{max-height:3.5em;overflow:hidden}\ntable.sm th.lg,table.sm td.lg{max-width:inherit}\ntable.sm th.lg>div,table.sm td.lg>div{max-height:inherit;overflow:inherit}\ntable.restr{vertical-align:top}\ntr.e{background-color:#CCC}\ntr.o{background-color:#EEE}\ntr.e:hover, tr.o:hover{background-color:#FF9}\ntr.h{background-color:#99C}\ntr.s{background-color:#FF9}\n.err{color:#F33;font-weight:bold;text-align:center}\n.frm{width:460px;border:1px solid #999;background-color:#eee;text-align:left}\n.frm label .l{width:100px;float:left}\n.dot{border-bottom:1px dotted #000}\n.ajax{text-decoration:none;border-bottom: 1px dashed}\n.qnav{width:30px}\n.sbtn{width:100px}\n.clear{clear:both;height:0;display:block}\n.pi a{text-decoration:none}\n.pi hr{display:none}\n.pi img{float:right}\n.pi .center{text-align:center}\n.pi table{margin:0 auto}\n.pi table td, .pi table th{border:1px solid #000000;text-align:left;vertical-align:baseline}\n.pi table .e{background-color:#ccccff;font-weight:bold}\n.pi table .v{background-color:#cccccc}\n</style>\n\n<script type=\"text/javascript\">\nvar LSK='pma_',LSKX=LSK+'max',LSKM=LSK+'min',qcur=0,LSMAX=32;\n\nfunction \$(i){return document.getElementById(i)}\nfunction frefresh(){\n var F=document.DF;\n F.method='get';\n F.refresh.value=\"1\";\n F.GoSQL.click();\n}\nfunction go(p,sql){\n var F=document.DF;\n F.p.value=p;\n if(sql)F.q.value=sql;\n F.GoSQL.click();\n}\nfunction ays(){\n return confirm('Are you sure to continue?');\n}\nfunction chksql(){\n var F=document.DF,v=F.qraw.value;\n if(/^\\s*(?:delete|drop|truncate|alter)/.test(v)) if(!ays())return false;\n if(lschk(1)){\n  var lsm=lsmax()+1,ls=localStorage;\n  ls[LSK+lsm]=v;\n  ls[LSKX]=lsm;\n  //keep just last LSMAX queries in log\n  if(!ls[LSKM])ls[LSKM]=1;\n  var lsmin=parseInt(ls[LSKM]);\n  if((lsm-lsmin+1)>LSMAX){\n   lsclean(lsmin,lsm-LSMAX);\n  }\n }\n return true;\n}\nfunction tc(tr){\n if (tr.className=='s'){\n  tr.className=tr.classNameX;\n }else{\n  tr.classNameX=tr.className;\n  tr.className='s';\n }\n}\nfunction lschk(skip){\n if (!localStorage || !skip && !localStorage[LSKX]) return false;\n return true;\n}\nfunction lsmax(){\n var ls=localStorage;\n if(!lschk() || !ls[LSKX])return 0;\n return parseInt(ls[LSKX]);\n}\nfunction lsclean(from,to){\n ls=localStorage;\n for(var i=from;i<=to;i++){\n  delete ls[LSK+i];ls[LSKM]=i+1;\n }\n}\nfunction q_prev(){\n var ls=localStorage;\n if(!lschk())return;\n qcur--;\n var x=parseInt(ls[LSKM]);\n if(qcur<x)qcur=x;\n \$('qraw').value=ls[LSK+qcur];\n}\nfunction q_next(){\n var ls=localStorage;\n if(!lschk())return;\n qcur++;\n var x=parseInt(ls[LSKX]);\n if(qcur>x)qcur=x;\n \$('qraw').value=ls[LSK+qcur];\n}\nfunction after_load(){\n var F=document.DF;\n var p=F['v[pwd]'];\n if (p) p.focus();\n qcur=lsmax();\n\n F.addEventListener('submit',function(e){\n  if(!F.qraw)return;\n  if(!chksql()){e.preventDefault();return}\n  \$('q').value=btoa(encodeURIComponent(\$('qraw').value).replace(/%([0-9A-F]{2})/g,function(m,p){return String.fromCharCode('0x'+p)}));\n });\n var res=\$('res');\n if(res)res.addEventListener('dblclick',function(e){\n  if(!\$('is_sm').checked)return;\n  var el=e.target;\n  if(el.tagName!='TD')el=el.parentNode;\n  if(el.tagName!='TD')return;\n  if(el.className.match(/\\b\\lg\\b/))el.className=el.className.replace(/\\blg\\b/,' ');\n  else el.className+=' lg';\n });\n}\nfunction logoff(){\n if(lschk()){\n  var ls=localStorage;\n  var from=parseInt(ls[LSKM]),to=parseInt(ls[LSKX]);\n  for(var i=from;i<=to;i++){\n   delete ls[LSK+i];\n  }\n  delete ls[LSKM];delete ls[LSKX];\n }\n}\nfunction cfg_toggle(){\n var e=\$('cfg-adv');\n e.style.display=e.style.display=='none'?'':'none';\n}\nfunction qtpl(s){\n \$('qraw').value=s.replace(/%T/g,'`";
    echo $_REQUEST["t"] ? b64d($_REQUEST["t"]) : "tablename";
    echo "`');\n}\nfunction smview(){\n if(\$('is_sm').checked){\$('res').className+=' sm'}else{\$('res').className = \$('res').className.replace(/\\bsm\\b/,' ')}\n}\n";
    if ($is_sht) {
        echo "function chkall(cab){\n var e=document.DF.elements;\n if (e!=null){\n  var cl=e.length;\n  for (i=0;i<cl;i++){var m=e[i];if(m.checked!=null && m.type==\"checkbox\"){m.checked=cab.checked}}\n }\n}\nfunction sht(f){\n document.DF.dosht.value=f;\n}\n";
    }
    echo "</script>\n\n</head>\n<body onload=\"after_load()\">\n<form method=\"post\" name=\"DF\" id=\"DF\" action=\"";
    eo($self);
    echo "\" enctype=\"multipart/form-data\">\n<input type=\"hidden\" name=\"XSS\" value=\"";
    eo($_SESSION["XSS"]);
    echo "\">\n<input type=\"hidden\" name=\"refresh\" value=\"\">\n<input type=\"hidden\" name=\"p\" value=\"\">\n\n<div class=\"inv\">\n<a href=\"http://phpminiadmin.sourceforge.net/\" target=\"_blank\"><b>phpMiniAdmin ";
    eo($VERSION);
    echo "</b></a>\n";
    if ($_SESSION["is_logged"] && $dbh) {
        echo " | <a href=\"?";
        eo($xurl . "&q=" . b64u("show databases"));
        echo "\">Databases</a>: <select name=\"db\" onChange=\"frefresh()\"><option value='*'> - select/refresh -</option><option value=''> - show all -</option>\n";
        echo get_db_select($dbn);
        echo "</select>\n";
        if ($dbn) {
            $z = " &#183; <a href='" . hs($self . "?" . $xurl . "&db=" . ue($dbn));
            echo $z . "&q=" . b64u($SHOW_T);
            echo "'>show tables</a>\n";
            echo $z;
            echo "&shex=1'>export</a>\n";
            echo $z;
            echo "&shim=1'>import</a>\n";
        }
        echo " | <a href=\"?showcfg=1\">Settings</a>\n";
    }
    if ($_SESSION["is_logged"]) {
        echo " | <a href=\"?";
        eo($xurl);
        echo "&logoff=1\" onclick=\"logoff()\">Logoff</a> ";
    }
    echo " | <a href=\"?pi=1\">phpinfo</a>\n</div>\n\n<div class=\"err\">";
    eo($err_msg);
    echo "</div>\n\n";
}
function print_screen()
{
    global $out_message;
    global $SQLq;
    global $err_msg;
    global $reccount;
    global $time_all;
    global $sqldr;
    global $page;
    global $MAX_ROWS_PER_PAGE;
    global $is_limited_sql;
    global $last_count;
    global $is_sm;
    $nav = "";
    if ($is_limited_sql && ($page || $MAX_ROWS_PER_PAGE <= $reccount)) {
        $nav = "<div class='nav'>" . get_nav($page, 10000, $MAX_ROWS_PER_PAGE, "javascript:go(%p%)") . "</div>";
    }
    print_header();
    echo "\n<div class=\"dot\" style=\"padding:3px 20px\">\n<label for=\"qraw\">SQL-query (or multiple queries separated by \";\"):</label>&nbsp;<button type=\"button\" class=\"qnav\" onclick=\"q_prev()\">&lt;</button><button type=\"button\" class=\"qnav\" onclick=\"q_next()\">&gt;</button><br>\n<textarea id=\"qraw\" cols=\"70\" rows=\"10\">";
    eo($SQLq);
    echo "</textarea><br>\n<input type=\"hidden\" name=\"q\" id=\"q\" value=\"";
    b64e($SQLq);
    echo "\">\n<input type=\"submit\" name=\"GoSQL\" value=\"Go\" class=\"sbtn\">\n<input type=\"button\" name=\"Clear\" value=\" Clear \" onclick=\"\$('qraw').value='';\" style=\"width:100px\">\n";
    if (!empty($_REQUEST["db"])) {
        echo "<div style=\"float:right\"><input type=\"button\" value=\"Select\" class=\"sbtn\" onclick=\"qtpl('SELECT *\\nFROM %T\\nWHERE 1')\"><input type=\"button\" value=\"Insert\" class=\"sbtn\" onclick=\"qtpl('INSERT INTO %T (`column`, `column`)\\nVALUES (\\'value\\', \\'value\\')')\"><input type=\"button\" value=\"Update\" class=\"sbtn\" onclick=\"qtpl('UPDATE %T\\nSET `column`=\\'value\\'\\nWHERE 1=0')\"><input type=\"button\" value=\"Delete\" class=\"sbtn\" onclick=\"qtpl('DELETE FROM %T\\nWHERE 1=0')\"></div><br class=\"clear\">";
    }
    echo "</div>\n<div class=\"dot\">\n<div style=\"float:right;padding:0 15px\"><label><input type=\"checkbox\" name=\"is_sm\" value=\"1\" id=\"is_sm\" onclick=\"smview()\" ";
    eo($is_sm ? "checked" : "");
    echo "> compact view</label></div>\nRecords: <b>";
    eo($reccount);
    if (!is_null($last_count) && $reccount < $last_count) {
        eo(" out of " . $last_count);
    }
    echo "</b> in <b>";
    eo($time_all);
    echo "</b> sec<br>\n<b>";
    eo($out_message);
    echo "</b>\n</div>\n";
    echo $nav . $sqldr . $nav;
    print_footer();
}
function print_footer()
{
    echo "</form>\n<div class=\"ft\">&copy; 2004-2021 <a href=\"http://osalabs.com\" target=\"_blank\">Oleg Savchuk</a></div>\n</body></html>\n";
}
function print_login()
{
    print_header();
    echo "<center>\n<h3>Enter your MySQL password for the xtream codes user</h3>\n<div style=\"width:400px;border:1px solid #999999;background-color:#eeeeee\">\n<label>Password: <input type=\"password\" name=\"pwd\" value=\"\"></label>\n<input type=\"hidden\" name=\"login\" value=\"1\">\n<input type=\"submit\" value=\" Login \">\n</div>\n</center>\n";
    print_footer();
}
function print_cfg()
{
    global $DB;
    global $err_msg;
    global $self;
    print_header();
    echo "<center>\n<h3>DB Connection Settings</h3>\n<div class=\"frm\">\n<label><div class=\"l\">DB user name:</div><input type=\"text\" name=\"v[user]\" value=\"";
    eo($DB["user"]);
    echo "\"></label><br>\n<label><div class=\"l\">Password:</div><input type=\"password\" name=\"v[pwd]\" value=\"\"></label><br>\n<div style=\"text-align:right\"><a href=\"#\" class=\"ajax\" onclick=\"cfg_toggle()\">advanced settings</a></div>\n<div id=\"cfg-adv\" style=\"display:none;\">\n<label><div class=\"l\">DB name:</div><input type=\"text\" name=\"v[db]\" value=\"";
    eo($DB["db"]);
    echo "\"></label><br>\n<label><div class=\"l\">MySQL host:</div><input type=\"text\" name=\"v[host]\" value=\"";
    eo($DB["host"]);
    echo "\"></label> <label>port: <input type=\"text\" name=\"v[port]\" value=\"";
    eo($DB["port"]);
    echo "\" size=\"4\"></label> <label>socket: <input type=\"text\" name=\"v[socket]\" value=\"";
    eo($DB["socket"]);
    echo "\" size=\"4\"></label><br>\n<label><div class=\"l\">Charset:</div><select name=\"v[chset]\"><option value=\"\">- default -</option>";
    echo chset_select($DB["chset"]);
    echo "</select></label><br>\n<br><label for =\"rmb\"><input type=\"checkbox\" name=\"rmb\" id=\"rmb\" value=\"1\" checked> Remember in cookies for 30 days or until Logoff</label>\n</div>\n<center>\n<input type=\"hidden\" name=\"savecfg\" value=\"1\">\n<input type=\"submit\" value=\" Apply \"><input type=\"button\" value=\" Cancel \" onclick=\"window.location='";
    eo($self);
    echo "'\">\n</center>\n</div>\n</center>\n";
    print_footer();
}
function db_connect($nodie = 0)
{
    global $dbh;
    global $DB;
    global $err_msg;
    $po = $DB["port"];
    if (!$po) {
        $po = ini_get("mysqli.default_port");
    }
    $so = $DB["socket"];
    if (!$so) {
        $so = ini_get("mysqli.default_socket");
    }
    if ($DB["ssl_ca"]) {
        $dbh = mysqli_init();
        mysqli_options($dbh, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
        mysqli_ssl_set($dbh, $DB["ssl_key"], $DB["ssl_cert"], $DB["ssl_ca"], NULL, NULL);
        if (!mysqli_real_connect($dbh, $DB["host"], $DB["user"], $DB["pwd"], $DB["db"], $po, $so, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT)) {
            $dbh = NULL;
        }
    } else {
        $dbh = mysqli_connect($DB["host"], $DB["user"], $DB["pwd"], $DB["db"], $po, $so);
    }
    if (!$dbh) {
        $err_msg = "Cannot connect to the database because: " . mysqli_connect_error();
        if (!$nodie) {
            exit($err_msg);
        }
    }
    if ($dbh && $DB["db"]) {
        $res = mysqli_select_db($dbh, $DB["db"]);
        if (!$res) {
            $err_msg = "Cannot select db because: " . mysqli_error($dbh);
            if (!$nodie) {
                exit($err_msg);
            }
        } else {
            if ($DB["chset"]) {
                db_query("SET NAMES " . $DB["chset"]);
            }
        }
    }
    return $dbh;
}
function db_checkconnect($dbh1 = NULL, $skiperr = 0)
{
    global $dbh;
    if (!$dbh1) {
        $dbh1 =& $dbh;
    }
    if (!$dbh1 || !mysqli_ping($dbh1)) {
        db_connect($skiperr);
        $dbh1 =& $dbh;
    }
    return $dbh1;
}
function db_disconnect()
{
    global $dbh;
    mysqli_close($dbh);
}
function dbq($s)
{
    global $dbh;
    if (is_null($s)) {
        return "NULL";
    }
    return "'" . mysqli_real_escape_string($dbh, $s) . "'";
}
function db_query($sql, $dbh1 = NULL, $skiperr = 0, $resmod = MYSQLI_STORE_RESULT)
{
    $dbh1 = db_checkconnect($dbh1, $skiperr);
    if ($dbh1) {
        $sth = mysqli_query($dbh1, $sql, $resmod);
    }
    if (!$sth && $skiperr) {
        return NULL;
    }
    if (!$sth) {
        exit("Error in DB operation:<br>\n" . mysqli_error($dbh1) . "<br>\n" . $sql);
    }
    return $sth;
}
function db_array($sql, $dbh1 = NULL, $skiperr = 0, $isnum = 0)
{
    $sth = db_query($sql, $dbh1, $skiperr, MYSQLI_USE_RESULT);
    if (!$sth) {
        return NULL;
    }
    $res = [];
    if ($isnum) {
        while ($row = mysqli_fetch_row($sth)) {
            $res[] = $row;
        }
    } else {
        while ($row = mysqli_fetch_assoc($sth)) {
            $res[] = $row;
        }
    }
    mysqli_free_result($sth);
    return $res;
}
function db_row($sql)
{
    $sth = db_query($sql);
    return mysqli_fetch_assoc($sth);
}
function db_value($sql, $dbh1 = NULL, $skiperr = 0)
{
    $sth = db_query($sql, $dbh1, $skiperr);
    if (!$sth) {
        return NULL;
    }
    $row = mysqli_fetch_row($sth);
    return $row[0];
}
function get_identity($dbh1 = NULL)
{
    $dbh1 = db_checkconnect($dbh1);
    return mysqli_insert_id($dbh1);
}
function get_db_select($sel = "")
{
    global $DB;
    global $SHOW_D;
    if (is_array($_SESSION["sql_sd"]) && $_REQUEST["db"] != "*") {
        $arr = $_SESSION["sql_sd"];
    } else {
        $arr = db_array($SHOW_D, NULL, 1);
        if (!is_array($arr)) {
            $arr = [["Database" => $DB["db"]]];
        }
        $_SESSION["sql_sd"] = $arr;
    }
    return @sel($arr, "Database", $sel);
}
function chset_select($sel = "")
{
    global $DBDEF;
    $result = "";
    if ($_SESSION["sql_chset"]) {
        $arr = $_SESSION["sql_chset"];
    } else {
        $arr = db_array("show character set", NULL, 1);
        if (!is_array($arr)) {
            $arr = [["Charset" => $DBDEF["chset"]]];
        }
        $_SESSION["sql_chset"] = $arr;
    }
    return @sel($arr, "Charset", $sel);
}
function sel($arr, $n, $sel = "")
{
    foreach ($arr as $a) {
        $b = $a[$n];
        $res .= "<option value='" . hs($b) . "' " . ($sel && $sel == $b ? "selected" : "") . ">" . hs($b) . "</option>";
    }
    return $res;
}
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return (double) $usec + (double) $sec;
}
function get_nav($pg, $all, $PP, $ptpl, $show_all = "")
{
    $n = "&nbsp;";
    $sep = " " . $n . "|" . $n . "\n";
    if (!$PP) {
        $PP = 10;
    }
    $allp = floor($all / $PP + 0);
    $pname = "";
    $res = "";
    $w = ["Less", "More", "Back", "Next", "First", "Total"];
    $sp = $pg - 2;
    if ($sp < 0) {
        $sp = 0;
    }
    if ($allp - $sp < 5 && 5 <= $allp) {
        $sp = $allp - 5;
    }
    $res = "";
    if (0 < $sp) {
        $pname = pen($sp - 1, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[0] . "</a>";
        $res .= $sep;
    }
    for ($p_p = $sp; $p_p < $allp && $p_p < $sp + 5; $p_p++) {
        $first_s = $p_p * $PP + 1;
        $last_s = ($p_p + 1) * $PP;
        $pname = pen($p_p, $ptpl);
        if ($all < $last_s) {
            $last_s = $all;
        }
        if ($p_p == $pg) {
            $res .= "<b>" . $first_s . ".." . $last_s . "</b>";
        } else {
            $res .= "<a href='" . $pname . "'>" . $first_s . ".." . $last_s . "</a>";
        }
        if ($p_p + 1 < $allp) {
            $res .= $sep;
        }
    }
    if ($sp + 5 < $allp) {
        $pname = pen($sp + 5, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[1] . "</a>";
    }
    $res .= " <br>\n";
    if (0 < $pg) {
        $pname = pen($pg - 1, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[2] . "</a> " . $n . "|" . $n . " ";
        $pname = pen(0, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[4] . "</a>";
    }
    if (0 < $pg && $pg + 1 < $allp) {
        $res .= $sep;
    }
    if ($pg + 1 < $allp) {
        $pname = pen($pg + 1, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[3] . "</a>";
    }
    if ($show_all) {
        $res .= " <b>(" . $w[5] . " - " . $all . ")</b> ";
    }
    return $res;
}
function pen($p, $np = "")
{
    return str_replace("%p%", $p, $np);
}
function killmq($value)
{
    return is_array($value) ? array_map("killmq", $value) : stripslashes($value);
}
function savecfg()
{
    global $DBDEF;
    $v = $_REQUEST["v"];
    if (!is_array($v)) {
        $v = [];
    }
    unset($v["ssl_ca"]);
    unset($v["ssl_key"]);
    unset($v["ssl_cert"]);
    $_SESSION["DB"] = array_merge($DBDEF, $v);
    unset($_SESSION["sql_sd"]);
    if ($_REQUEST["rmb"]) {
        $tm = time() + 2592000;
        newcookie("conn[db]", $v["db"], $tm);
        newcookie("conn[user]", $v["user"], $tm);
        newcookie("conn[pwd]", $v["pwd"], $tm);
        newcookie("conn[host]", $v["host"], $tm);
        newcookie("conn[port]", $v["port"], $tm);
        newcookie("conn[socket]", $v["socket"], $tm);
        newcookie("conn[chset]", $v["chset"], $tm);
    } else {
        newcookie("conn[db]", false, -1);
        newcookie("conn[user]", false, -1);
        newcookie("conn[pwd]", false, -1);
        newcookie("conn[host]", false, -1);
        newcookie("conn[port]", false, -1);
        newcookie("conn[socket]", false, -1);
        newcookie("conn[chset]", false, -1);
    }
}
function newcookie($n, $v, $e)
{
    $x = "";
    return setcookie($n, $v, $e, $x, $x, $x, !$x);
}
function loadcfg()
{
    global $DBDEF;
    if (isset($_COOKIE["conn"])) {
        $_SESSION["DB"] = array_merge($DBDEF, $_COOKIE["conn"]);
    } else {
        $_SESSION["DB"] = $DBDEF;
    }
    if (!strlen($_SESSION["DB"]["chset"])) {
        $_SESSION["DB"]["chset"] = $DBDEF["chset"];
    }
}
function loadsess()
{
    global $DB;
    global $is_sm;
    $DB = $_SESSION["DB"];
    $rdb = $_REQUEST["db"];
    if ($rdb == "*") {
        $rdb = "";
    }
    if ($rdb) {
        $DB["db"] = $rdb;
    }
    if ($_REQUEST["GoSQL"]) {
        $_SESSION["is_sm"] = $_REQUEST["is_sm"] + 0;
    }
    $is_sm = $_SESSION["is_sm"] + 0;
}
function print_export()
{
    global $self;
    global $xurl;
    global $DB;
    global $DUMP_FILE;
    $t = $_REQUEST["rt"];
    $l = $t ? "Table " . $t : "whole DB";
    print_header();
    echo "<center>\n<h3>Export ";
    eo($l);
    echo "</h3>\n<div class=\"frm\">\n<input type=\"checkbox\" name=\"s\" value=\"1\" checked> Structure<br>\n<input type=\"checkbox\" name=\"d\" value=\"1\" checked> Data<br><br>\n<div><label><input type=\"radio\" name=\"et\" value=\"\" checked> .sql</label>&nbsp;</div>\n<div>\n";
    if ($t && !strpos($t, ",")) {
        echo " <label><input type=\"radio\" name=\"et\" value=\"csv\"> .csv (Excel style, data only and for one table only)</label>\n";
    } else {
        echo "<label>&nbsp;( ) .csv</label> <small>(to export as csv - go to 'show tables' and export just ONE table)</small>\n";
    }
    echo "</div>\n<br>\n<div><label><input type=\"checkbox\" name=\"sp\" value=\"1\"> import has super privileges</label></div>\n<div><label><input type=\"checkbox\" name=\"gz\" value=\"1\"> compress as .gz</label></div>\n<br>\n<input type=\"hidden\" name=\"doex\" value=\"1\">\n<input type=\"hidden\" name=\"rt\" value=\"";
    eo($t);
    echo "\">\n<input type=\"submit\" value=\" Download \">\n<input type=\"submit\" name=\"srv\" value=\" Dump on Server \">\n<input type=\"button\" value=\" Cancel \" onclick=\"window.location='";
    eo($self . "?" . $xurl . "&db=" . ue($DB["db"]));
    echo "'\">\n<p><small>\"Dump on Server\" exports to file:<br>";
    eo(export_fname($DUMP_FILE) . ".sql");
    echo "</small></p>\n</div>\n</center>\n";
    print_footer();
    exit;
}
function export_fname($f, $ist = false)
{
    $t = $ist ? date("Y-m-d-His") : "YYYY-MM-DD-HHMMSS";
    return $f . $t;
}
function do_export()
{
    global $DB;
    global $VERSION;
    global $D;
    global $BOM;
    global $ex_isgz;
    global $ex_issrv;
    global $dbh;
    global $out_message;
    $rt = str_replace("`", "", $_REQUEST["rt"]);
    $t = explode(",", $rt);
    $th = array_flip($t);
    $ct = count($t);
    $z = db_row("show variables like 'max_allowed_packet'");
    $MAXI = floor($z["Value"] * 0);
    if (!$MAXI) {
        $MAXI = 838860;
    }
    $MAXI = min($MAXI, 16777216);
    $aext = "";
    $ctp = "";
    $ex_super = $_REQUEST["sp"] ? 1 : 0;
    $ex_isgz = $_REQUEST["gz"] ? 1 : 0;
    if ($ex_isgz) {
        $aext = ".gz";
        $ctp = "application/x-gzip";
    }
    $ex_issrv = $_REQUEST["srv"] ? 1 : 0;
    if ($ct == 1 && $_REQUEST["et"] == "csv") {
        ex_start(".csv");
        ex_hdr($ctp ? $ctp : "text/csv", $t[0] . ".csv" . $aext);
        if ($DB["chset"] == "utf8mb4") {
            ex_w($BOM);
        }
        $sth = db_query("select * from `" . $t[0] . "`", NULL, 0, MYSQLI_USE_RESULT);
        $fn = mysqli_field_count($dbh);
        for ($i = 0; $i < $fn; $i++) {
            $m = mysqli_fetch_field($sth);
            ex_w(qstr($m->name) . ($i < $fn - 1 ? "," : ""));
        }
        ex_w($D);
        while ($row = mysqli_fetch_row($sth)) {
            ex_w(to_csv_row($row));
        }
        mysqli_free_result($sth);
    } else {
        ex_start(".sql");
        ex_hdr($ctp ? $ctp : "text/plain", (string) $DB["db"] . ($ct == 1 && $t[0] ? "." . $t[0] : (1 < $ct ? "." . $ct . "tables" : "")) . ".sql" . $aext);
        ex_w("-- phpMiniAdmin dump " . $VERSION . $D . "-- Datetime: " . date("Y-m-d H:i:s") . $D . "-- Host: " . $DB["host"] . $D . "-- Database: " . $DB["db"] . $D . $D);
        if ($DB["chset"]) {
            ex_w("/*!40030 SET NAMES " . $DB["chset"] . " */;" . $D);
        }
        $ex_super && ex_w("/*!40030 SET GLOBAL max_allowed_packet=16777216 */;" . $D . $D);
        ex_w("/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;" . $D . $D);
        $sth = db_query("show full tables from `" . $DB["db"] . "`");
        while ($row = mysqli_fetch_row($sth)) {
            if (!$rt || array_key_exists($row[0], $th)) {
                do_export_table($row[0], $row[1], $MAXI);
            }
        }
        ex_w("/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;" . $D . $D);
        ex_w($D . "-- phpMiniAdmin dump end" . $D);
    }
    ex_end();
    if (!$ex_issrv) {
        exit;
    }
    $out_message = "Export done successfully";
}
function do_export_table($t = "", $tt = "", $MAXI = 838860)
{
    global $D;
    global $ex_issrv;
    @set_time_limit(600);
    if ($_REQUEST["s"]) {
        $sth = db_query("show create table `" . $t . "`");
        $row = mysqli_fetch_row($sth);
        $ct = preg_replace("/\n\r|\r\n|\n|\r/", $D, $row[1]);
        ex_w("DROP TABLE IF EXISTS `" . $t . "`;" . $D . $ct . ";" . $D . $D);
    }
    if ($_REQUEST["d"] && $tt != "VIEW") {
        $exsql = "";
        ex_w("/*!40000 ALTER TABLE `" . $t . "` DISABLE KEYS */;" . $D);
        $sth = db_query("select * from `" . $t . "`", NULL, 0, MYSQLI_USE_RESULT);
        while ($row = mysqli_fetch_row($sth)) {
            $values = "";
            foreach ($row as $v) {
                $values .= ($values ? "," : "") . dbq($v);
            }
            $exsql .= ($exsql ? "," : "") . "(" . $values . ")";
            if ($MAXI < strlen($exsql)) {
                ex_w("INSERT INTO `" . $t . "` VALUES " . $exsql . ";" . $D);
                $exsql = "";
            }
        }
        mysqli_free_result($sth);
        if ($exsql) {
            ex_w("INSERT INTO `" . $t . "` VALUES " . $exsql . ";" . $D);
        }
        ex_w("/*!40000 ALTER TABLE `" . $t . "` ENABLE KEYS */;" . $D . $D);
    }
    if (!$ex_issrv) {
        flush();
    }
}
function ex_hdr($ct, $fn)
{
    global $ex_issrv;
    if ($ex_issrv) {
        return NULL;
    }
    header("Content-type: " . $ct);
    header("Content-Disposition: attachment; filename=\"" . $fn . "\"");
}
function ex_start($ext)
{
    global $ex_isgz;
    global $ex_gz;
    global $ex_tmpf;
    global $ex_issrv;
    global $ex_f;
    global $DUMP_FILE;
    if ($ex_isgz) {
        $ex_tmpf = ($ex_issrv ? export_fname($DUMP_FILE, true) . $ext : tmp_name()) . ".gz";
        if (!($ex_gz = gzopen($ex_tmpf, "wb9"))) {
            exit("Error trying to create gz tmp file");
        }
    } else {
        if ($ex_issrv && !($ex_f = fopen(export_fname($DUMP_FILE, true) . $ext, "wb"))) {
            exit("Error trying to create dump file");
        }
    }
}
function ex_w($s)
{
    global $ex_isgz;
    global $ex_gz;
    global $ex_issrv;
    global $ex_f;
    if ($ex_isgz) {
        gzwrite($ex_gz, $s, strlen($s));
    } else {
        if ($ex_issrv) {
            fwrite($ex_f, $s);
        } else {
            echo $s;
        }
    }
}
function ex_end()
{
    global $ex_isgz;
    global $ex_gz;
    global $ex_tmpf;
    global $ex_issrv;
    global $ex_f;
    if ($ex_isgz) {
        gzclose($ex_gz);
        if (!$ex_issrv) {
            readfile($ex_tmpf);
            unlink($ex_tmpf);
        }
    } else {
        if ($ex_issrv) {
            fclose($ex_f);
        }
    }
}
function print_import()
{
    global $self;
    global $xurl;
    global $DB;
    global $DUMP_FILE;
    print_header();
    echo "<center>\n<h3>Import DB</h3>\n<div class=\"frm\">\n<div><label><input type=\"radio\" name=\"it\" value=\"\" checked> import by uploading <b>.sql</b> or <b>.gz</b> file:</label>\n <input type=\"file\" name=\"file1\" value=\"\" size=40><br>\n</div>\n<div><label><input type=\"radio\" name=\"it\" value=\"sql\"> import from file on server:<br>\n ";
    eo($DUMP_FILE . ".sql");
    echo "</label></div>\n<div><label><input type=\"radio\" name=\"it\" value=\"gz\"> import from file on server:<br>\n ";
    eo($DUMP_FILE . ".sql.gz");
    echo "</label></div>\n<input type=\"hidden\" name=\"doim\" value=\"1\">\n<input type=\"submit\" value=\" Import \" onclick=\"return ays()\"><input type=\"button\" value=\" Cancel \" onclick=\"window.location='";
    eo($self . "?" . $xurl . "&db=" . ue($DB["db"]));
    echo "'\">\n</div>\n<br><br><br>\n<!--\n<h3>Import one Table from CSV</h3>\n<div class=\"frm\">\n.csv file (Excel style): <input type=\"file\" name=\"file2\" value=\"\" size=40><br>\n<input type=\"checkbox\" name=\"r1\" value=\"1\" checked> first row contain field names<br>\n<small>(note: for success, field names should be exactly the same as in DB)</small><br>\nCharacter set of the file: <select name=\"chset\">";
    echo chset_select("utf8mb4");
    echo "</select>\n<br><br>\nImport into:<br>\n<input type=\"radio\" name=\"tt\" value=\"1\" checked=\"checked\"> existing table:\n <select name=\"t\">\n <option value=''>- select -</option>\n ";
    echo sel(db_array("show tables", NULL, 0, 1), 0, "");
    echo "</select>\n<div style=\"margin-left:20px\">\n <input type=\"checkbox\" name=\"ttr\" value=\"1\"> replace existing DB data<br>\n <input type=\"checkbox\" name=\"tti\" value=\"1\"> ignore duplicate rows\n</div>\n<input type=\"radio\" name=\"tt\" value=\"2\"> create new table with name <input type=\"text\" name=\"tn\" value=\"\" size=\"20\">\n<br><br>\n<input type=\"hidden\" name=\"doimcsv\" value=\"1\">\n<input type=\"submit\" value=\" Upload and Import \" onclick=\"return ays()\"><input type=\"button\" value=\" Cancel \" onclick=\"window.location='";
    eo($self);
    echo "'\">\n</div>\n-->\n</center>\n";
    print_footer();
    exit;
}
function do_import()
{
    global $err_msg;
    global $out_message;
    global $dbh;
    global $SHOW_T;
    global $DUMP_FILE;
    $err_msg = "";
    $it = $_REQUEST["it"];
    if (!$it) {
        $F = $_FILES["file1"];
        if ($F && $F["name"]) {
            $filename = $F["tmp_name"];
            $pi = pathinfo($F["name"]);
            $ext = $pi["extension"];
        }
    } else {
        $ext = $it == "gz" ? "sql.gz" : "sql";
        $filename = $DUMP_FILE . "." . $ext;
    }
    if ($filename && file_exists($filename)) {
        if ($ext != "sql") {
            $tmpf = tmp_name();
            if (($gz = gzopen($filename, "rb")) && ($tf = fopen($tmpf, "wb"))) {
                while (!gzeof($gz)) {
                    if (fwrite($tf, gzread($gz, 8192), 8192) === false) {
                        $err_msg = "Error during gz file extraction to tmp file";
                    }
                }
                gzclose($gz);
                fclose($tf);
                $filename = $tmpf;
            } else {
                $err_msg = "Error opening gz file";
            }
        }
        if (!$err_msg) {
            if (!do_multi_sql("", $filename)) {
                $err_msg = "Import Error: " . mysqli_error($dbh);
            } else {
                $out_message = "Import done successfully";
                do_sql($SHOW_T);
                return NULL;
            }
        }
    } else {
        $err_msg = "Error: Please select file first";
    }
    print_import();
    exit;
}
function do_multi_sql($insql, $fname = "")
{
    @set_time_limit(600);
    $sql = "";
    $ochar = "";
    $is_cmt = "";
    $GLOBALS["insql_done"] = 0;
    while ($str = get_next_chunk($insql, $fname)) {
        $opos = -1 * strlen($ochar);
        $cur_pos = 0;
        $i = strlen($str);
        while ($i--) {
            if ($ochar) {
                list($clchar, $clpos) = get_close_char($str, $opos + strlen($ochar), $ochar);
                if ($clchar) {
                    if ($ochar == "--" || $ochar == "#" || $is_cmt) {
                        $sql .= substr($str, $cur_pos, $opos - $cur_pos);
                    } else {
                        $sql .= substr($str, $cur_pos, $clpos + strlen($clchar) - $cur_pos);
                    }
                    $cur_pos = $clpos + strlen($clchar);
                    $ochar = "";
                    $opos = 0;
                } else {
                    $sql .= substr($str, $cur_pos);
                }
            } else {
                list($ochar, $opos) = get_open_char($str, $cur_pos);
                if ($ochar == ";") {
                    $sql .= substr($str, $cur_pos, $opos - $cur_pos + 1);
                    if (!do_one_sql($sql)) {
                        return 0;
                    }
                    $sql = "";
                    $cur_pos = $opos + strlen($ochar);
                    $ochar = "";
                    $opos = 0;
                } else {
                    if (!$ochar) {
                        $sql .= substr($str, $cur_pos);
                    } else {
                        $is_cmt = 0;
                        if ($ochar == "/*" && substr($str, $opos, 3) != "/*!") {
                            $is_cmt = 1;
                        }
                    }
                }
            }
        }
    }
    if ($sql) {
        if (!do_one_sql($sql)) {
            return 0;
        }
        $sql = "";
    }
    return 1;
}
function get_next_chunk($insql, $fname)
{
    global $LFILE;
    global $insql_done;
    if ($insql) {
        if ($insql_done) {
            return "";
        }
        $insql_done = 1;
        return $insql;
    }
    if (!$fname) {
        return "";
    }
    if (!$LFILE) {
        $LFILE = fopen($fname, "r+b");
        exit("Can't open [" . $fname . "] file \$!");
    }
    $LFILE = fopen($fname, "r+b");
    return fread($LFILE, 65536);
}
function get_open_char($str, $pos)
{
    $ochar = "";
    $opos = "";
    if (preg_match("/(\\/\\*|^--|(?<=\\s)--|#|'|\"|;)/", $str, $m, PREG_OFFSET_CAPTURE, $pos)) {
        $ochar = $m[1][0];
        $opos = $m[1][1];
    }
    return [$ochar, $opos];
}
function get_close_char($str, $pos, $ochar)
{
    $aCLOSE = ["'" => "(?<!\\\\)'|(\\\\+)'", "\"" => "(?<!\\\\)\"", "/*" => "\\*\\/", "#" => "[\\r\\n]+", "--" => "[\\r\\n]+"];
    if ($aCLOSE[$ochar] && preg_match("/(" . $aCLOSE[$ochar] . ")/", $str, $m, PREG_OFFSET_CAPTURE, $pos)) {
        $clchar = $m[1][0];
        $clpos = $m[1][1];
        $sl = strlen($m[2][0]);
        if ($ochar == "'" && $sl) {
            if ($sl % 2) {
                list($clchar, $clpos) = get_close_char($str, $clpos + strlen($clchar), $ochar);
            } else {
                $clpos += strlen($clchar) - 1;
                $clchar = "'";
            }
        }
    }
    return [$clchar, $clpos];
}
function do_one_sql($sql)
{
    global $last_sth;
    global $last_sql;
    global $MAX_ROWS_PER_PAGE;
    global $page;
    global $is_limited_sql;
    global $last_count;
    global $IS_COUNT;
    $sql = trim($sql);
    $sql = preg_replace("/;\$/", "", $sql);
    if ($sql) {
        $last_sql = $sql;
        $is_limited_sql = 0;
        $last_count = NULL;
        if (preg_match("/^select/i", $sql) && !preg_match("/limit +\\d+/i", $sql)) {
            if ($IS_COUNT) {
                $sql1 = "select count(*) from (" . $sql . ") ___count_table";
                $last_count = db_value($sql1, NULL, "noerr");
            }
            $offset = $page * $MAX_ROWS_PER_PAGE;
            $sql .= " LIMIT " . $offset . "," . $MAX_ROWS_PER_PAGE;
            $is_limited_sql = 1;
        }
        $last_sth = db_query($sql, 0, "noerr");
        return $last_sth;
    }
    return 1;
}
function do_sht()
{
    global $SHOW_T;
    $cb = $_REQUEST["cb"];
    if (!is_array($cb)) {
        $cb = [];
    }
    $sql = "";
    switch ($_REQUEST["dosht"]) {
        case "exp":
            $_REQUEST["t"] = join(",", $cb);
            print_export();
            exit;
            break;
        case "drop":
            $sq = "DROP TABLE";
            break;
        case "trunc":
            $sq = "TRUNCATE TABLE";
            break;
        case "opt":
            $sq = "OPTIMIZE TABLE";
            break;
        default:
            if ($sq) {
                foreach ($cb as $v) {
                    $sql .= $sq . " " . $v . ";\n";
                }
            }
            if ($sql) {
                do_sql($sql);
            }
            do_sql($SHOW_T);
    }
}
function to_csv_row($adata)
{
    global $D;
    $r = "";
    foreach ($adata as $a) {
        $r .= ($r ? "," : "") . qstr($a);
    }
    return $r . $D;
}
function qstr($s)
{
    $s = nl2br($s);
    $s = str_replace("\"", "\"\"", $s);
    return "\"" . $s . "\"";
}
function get_rand_str($len)
{
    $result = "";
    $chars = preg_split("//", "ABCDEFabcdef0123456789");
    for ($i = 0; $i < $len; $i++) {
        $result .= $chars[rand(0, count($chars) - 1)];
    }
    return $result;
}
function check_xss()
{
    global $self;
    if ($_SESSION["XSS"] != trim($_REQUEST["XSS"])) {
        unset($_SESSION["XSS"]);
        header("location: " . $self);
        exit;
    }
}
function rw($s)
{
    echo hs(var_dump($s)) . "<br>\n";
}
function tmp_name()
{
    if (function_exists("sys_get_temp_dir")) {
        return tempnam(sys_get_temp_dir(), "pma");
    }
    if (!($temp = getenv("TMP")) && !($temp = getenv("TEMP")) && !($temp = getenv("TMPDIR"))) {
        $temp = tempnam(__FILE__, "");
        if (file_exists($temp)) {
            unlink($temp);
            $temp = dirname($temp);
        }
    }
    return $temp ? tempnam($temp, "pma") : NULL;
}
function hs($s)
{
    return htmlspecialchars($s, ENT_COMPAT | ENT_HTML401, "UTF-8");
}
function eo($s)
{
    echo hs($s);
}
function ue($s)
{
    return urlencode($s);
}
function b64e($s)
{
    return base64_encode($s);
}
function b64u($s)
{
    return ue(base64_encode($s));
}
function b64d($s)
{
    return base64_decode($s);
}

?>