<?php

$ipserver = $_GET["ip"];


    
function url_result($url) {
    $ch = curl_init();
    $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml')); 
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch);
        return $data;
    } 
    
function get_between($content, $start, $end) {
  $r = explode($start, $content);
  if (isset($r[1])) {
    $r = explode($end, $r[1]);
    return $r[0];
  }
  return '';
}
function type_name($ttype) {
    if($ttype=="Proxy Public"){return "PROXY";}
    elseif($ttype=="DCH"){return "HOSTING";}
       else{
       return $ttype;
    }
 } 
    
    
if(!empty($ipserver) ){
$backup = 'apibackup.txt';
$content = file_get_contents($backup);
$boss = strpos($content,"$ipserver =");    
$ledebut = "$ipserver = ";
$lafin = "\n";
if($boss !== false)
    {
   	$koko = get_between($content, $ledebut, $lafin);
   echo $koko;
}
    else{      
$dataports = url_result("https://awebanalysis.com/fr/ip-lookup/$ipserver/");
    
    $startru = "ISP</b></th>\n                                <td>";
	$endru = '</td>';
    $isp = trim(get_between($dataports, $startru, $endru));
    
    $startaaz = "mt2\"></span>";
	$endaaz = '</td>';
 	$countryy = trim(get_between($dataports, $startaaz, $endaaz));
   
    $startaaz22 = "Code CCTLD</td>\n                                        <td>";
	$endaaz22 = '</td>';
 	$countryycode = trim(get_between($dataports, $startaaz22, $endaaz22));
   
    $tvpnproxy = "Proxy détecté";
    $startoz = "Type de proxy</b></th>\n                                            <td><b>";
	$endoz = '</b>';    

if(strpos($dataports, $tvpnproxy)) {
      $vpnproxy = '1';  
	  $ttype = trim(get_between($dataports, $startoz, $endoz));        
    } 
    else {
    $vpnproxy = '0';
    $ttype = 'HOME';
    }
    $ayeh = '{"status":1,"isp_info":{"description":"' .$isp. '","is_server":"' .$vpnproxy. '","type":"' .type_name($ttype). '","country_code":"' .$countryycode. '","country_name":"' .$countryy. '"}}';
    echo $ayeh;
    $wayy = "$ipserver = $ayeh";
    $byte = file_put_contents($backup,$wayy . "\r\n", FILE_APPEND | LOCK_EX);
    }
    }
    else {
            echo 'come back with an ip';
    }

?>