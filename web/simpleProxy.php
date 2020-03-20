<?php
include "./polyfill.php";

class SimpleProxy {
  public function __construct($protocol, $originalHost,$cookieFolder,$cookieFile) {
    $this->originalDomain = $protocol . $originalHost;
    $this->originalHost = $originalHost;
    $this->originalProtocol = $protocol;

    $this->proxyDomain = ($_SERVER["HTTPS"] == "on" ? "https://" : "http://") . $_SERVER["HTTP_HOST"];
    $this->proxyHost = $_SERVER["HTTP_HOST"];

    $this->cookieFolder = $cookieFolder;
    $this->cookieFile = $cookieFolder . $cookieFile;
    $this->cookieDomain = str_replace("www.", "", $originalHost);

    // Create cookie folder if not exists
    if (!file_exists($cookieFolder)) {
      mkdir($cookieFolder, 0755, true);
    }
  }

  private function getAndReplaceHeaders($originalHost, $blacklist) {
    $currentRequestHeader = getallheaders();
    $newHeader = array();

    foreach ($currentRequestHeader as $name => $value) {
      if(!in_array($name, $blacklist)) {
        $processedVal = str_replace($this->proxyHost, $originalHost, $value);
        array_push($newHeader, "$name: " . $processedVal);
      }
    }

    return $newHeader;
  }

  private function replaceHostOrDomain($contentStr, $mode) {
    return ($mode == "domain") 
      ? str_replace($this->originalDomain, $this->proxyDomain, $contentStr)
      : str_replace($this->originalHost, $this->proxyHost, $contentStr);
  }

  private function scrapWebpage ($url, $requestHeaders) {
    $curlSession = curl_init();

    curl_setopt($curlSession, CURLOPT_URL, $url);
    curl_setopt($curlSession, CURLOPT_HEADER, 1); // get return header
    curl_setopt($curlSession, CURLOPT_HTTPHEADER, $requestHeaders); // set request headers

    if($_SERVER["REQUEST_METHOD"] == "POST"){
      curl_setopt($curlSession, CURLOPT_POST, 1);
      curl_setopt($curlSession, CURLOPT_POSTFIELDS, $_POST);
    }

    curl_setopt($curlSession, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curlSession, CURLOPT_COOKIEJAR, $this->cookieFile); 
    curl_setopt($curlSession, CURLOPT_COOKIEFILE, $this->cookieFile);
    
    foreach($_COOKIE as $k=>$v){
      if(is_array($v)){
        $v = serialize($v);
      }
      curl_setopt($curlSession, CURLOPT_COOKIE, "$k=$v; domain=.$this->cookieDomain ; path=/");
    }

    $response = curl_exec ($curlSession);

    $curlOutput = array(
      "header" => array(),
      "body" => ""
    );

    if (curl_error($curlSession)){
      $curlOutput["body"] = curl_error($curlSession);
    } else {
      // clean duplicate header that seems to appear on fastcgi with output buffer on some servers
      $response = str_replace("HTTP/1.1 100 Continue\r\n\r\n", "", $response);

      $ar = explode("\r\n\r\n", $response, 2); 

      $header = $ar[0];
      $body = $ar[1];

      $headerArr = explode("\r\n", $header); 

      foreach($headerArr as $key => $value){
        // Header rewrite if needed
        if(!preg_match("/^Transfer-Encoding/", $value)){
          $value = $this->replaceHostOrDomain($value, "domain");
          array_push($curlOutput["header"], trim($value));
        }
      }

      // Replace scrapping host to current host to keep links working
      $body = $this->replaceHostOrDomain($body, "host");

      $curlOutput["body"] = $body;
    }

    curl_close ($curlSession);

    return $curlOutput;
  }

  public function start() {
    $originalHost = strpos($_SERVER["REQUEST_URI"], '/cdn-assets/') === 0 
      ? "cdn." . $this->originalHost
      : $this->originalHost;

    $url = strpos($_SERVER["REQUEST_URI"], '/cdn-assets/') === 0 
      ? $this->originalProtocol . $originalHost . str_replace("/cdn-assets", "", $_SERVER["REQUEST_URI"])
      : $this->originalProtocol . $originalHost . $_SERVER["REQUEST_URI"];

    $requestHeaders = $this->getAndReplaceHeaders($originalHost, ["Cookie", "X-Real-Ip", "X-Accel-Internal", "Connection", "Accept-Encoding"]);

    return $this->scrapWebpage($url, $requestHeaders);
  }
}
?>