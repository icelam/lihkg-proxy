<?php
/* error_reporting(E_ALL);
ini_set('display_errors',1); */

include "./simpleProxy.php";

session_start();

$protocol = "https://";
$originalHost = preg_match("/^\/(-?)(\d+)\/https:\/\/(.*)$/", $_SERVER["REQUEST_URI"])
  ? "i.lih.kg" // Use image host for images. Sample URL: https://i.lih.kg/540/https://na.cx/i/73nJmnv.png
  : "lihkg.com";
$cookieFolder = "cookies";
$cookieFile = "/proxy-cookie-" . session_id();

$lihkgProxy = new SimpleProxy($protocol, $originalHost,$cookieFolder,$cookieFile);
$proxyOutput = $lihkgProxy->start();

foreach($proxyOutput["header"] as $header){
  header($header);
}

$customBody = $proxyOutput["body"];

// Brute replace encoded domains from lihkg's main.js
$customBody = str_replace('m=function(){var e=Array.prototype.slice.call(arguments),t=e.shift();return e.reverse().map(function(e,n){return String.fromCharCode(e-t-21-n)}).join("")}(18,156,143)+38512..toString(36).toLowerCase()+10..toString(36).toLowerCase().split("").map(function(e){return String.fromCharCode(e.charCodeAt()+-39)}).join("")+1147..toString(36).toLowerCase().split("").map(function(e){return String.fromCharCode(e.charCodeAt()+-71)}).join("")+36134512..toString(36).toLowerCase()+30..toString(36).toLowerCase().split("").map(function(e){return String.fromCharCode(e.charCodeAt()+-71)}).join("")+function(){var e=Array.prototype.slice.call(arguments),t=e.shift();return e.reverse().map(function(e,n){return String.fromCharCode(e-t-22-n)}).join("")}(58,130,191,192,179)+13878..toString(36).toLowerCase()+function(){var e=Array.prototype.slice.call(arguments),t=e.shift();return e.reverse().map(function(e,n){return String.fromCharCode(e-t-33-n)}).join("")}(57,209,185)+2..toString(36).toLowerCase();', "m=\"https://" . $_SERVER["HTTP_HOST"] . "/api_v2\";" ,$customBody);

// Implemet CDN / assets domain later in SimpleProxy
$customBody = str_replace("i.lih.kg", $_SERVER["HTTP_HOST"], $customBody);

// Support short domain
$customBody = str_replace("lih.kg", $_SERVER["HTTP_HOST"] . "/thread", $customBody);

// Brute rewrite share id to thread
$customBody = str_replace('getShareId:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;if(t>0){var r=n.toString().length-1,a=r<<1|t-1;return _(parseInt(""+e+n,10),"abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQR")+"STUVWXYZ"[a]}return _(parseInt(""+e,10),"abcdefghijkmnopqrstuvwxyz")}', 'getShareId:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;return t===1?e+"/page/"+n:e+"/page/"+(((n-1)/25)+1)+"?ref=sharer&post="+n}', $customBody);

echo $customBody;
?>
