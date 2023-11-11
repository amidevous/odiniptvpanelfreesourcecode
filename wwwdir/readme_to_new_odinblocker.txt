===================================
- dont remove configblock_odin.php, all files depend this file to work.

 - if after updating you are getting database errors access OdinToolbox and perform the update and sanitization of your database.

===================================
Below is a step-by-step guide to configure your anti-line theft blocker:

edit configblock_odin.php and change:

 //Number of Wrong Attempts to block (Default: 10)
 $attempts = 10;

change attempts as you wish and as better to your infrastructure.

Here you will put your whitelist ips as restreamer ips and other you think is important:

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