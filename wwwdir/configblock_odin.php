<?php

/*
 * ODIN Line Scaners Blocker
 * Blocking System against line leaks and other types of attacks
 * https://discord.gg/mH6D7VWXmt
 * -- Distribution prohibited without prior authorization from the developer (Avoid future problems #).
 */

 //Number of Wrong Attempts to block (Default: 10)
 $attempts = 10;

// WhiteList IPs.
// ** When adding a new IP, remember to put it inside quotes and put a comma at the end
// *** Eg: "1.2.3.4",
$ips = [ 
	"8.8.8.8",
	// "xx.xx.xx.xxx", 
	// "xxx.xxx.xxx.xxx", 
	// "xxx.xxx.xxx.xxx",
	"8.8.4.4" // Note that in these lines you need a comma at the end and in the last line we don't put a comma at the end.
 ];
 

/*
 * Do not modify ANYTHING in the code below, otherwise the system will stop working.
 */

 $ba9d5787caed81a9 = $ips; $D75c498dcc82bdc3 = $attempts; define("\x44\x33\x30\x39\x63\x37\x33\64\x39\143\x39\x33\x33\x63\141\x39", $ba9d5787caed81a9); define("\x62\x62\71\x36\61\x37\65\x62\142\x33\x35\x65\60\71\x37\146", $D75c498dcc82bdc3); 

?>