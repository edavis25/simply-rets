<?php

class SimplyRets {
    
    private $api_user = 'simplyrets';
    private $api_pass = 'simplyrets';
    private $base_url = 'https://api.simplyrets.com/';

    public function __construct() {
        
    }
    
    public function test() {
        $url = $this->base_url . 'properties?q=texas';
        $json = $this->get($url, true);
        $headers = $json['headers'];
        $link = $this->getResponseLink($headers[7]);
        return $json;
    }
    
    // Textual keyword search
    // Searches for: listingId, streen #, street name, city, subdivision, ZIP
    public function keywordSearch($keyword, $includeHeaders = false) {        
        $url  = $this->base_url . 'properties?q=';
        $url .= urlencode($keyword);
        return $this->get($url, $includeHeaders);
    }
    
    // Run direct query for a given full, paramerized url
    public function adHocQuery($url) {
        return $this->get($url);
    }

    // Retrieve JSON data from the API
    private function  get($url, $includeHeaders = false) {
        $http_opts = array(
            'method' => 'GET',
            'timeout' => 10,
            'header' => "Accept: application/vnd.simplyrets-v0.1+json",
            'header'  => 'Authorization: Basic ' . base64_encode("{$this->api_user}:{$this->api_pass}")
        );
        
        $context_opts = array(
            'http' => $http_opts
         );
        
        $context = stream_context_create($context_opts);
        $feed = file_get_contents($url, false, $context);
        $json = json_decode($feed, true);
        
         // If headers wanted return array w/ both json data and headers
         if ($includeHeaders) {
             $headers = $http_response_header;
             $data = array(
                 'json' => $json,
                 'headers' => $headers
             );
             return $data;
         }       
      
        //$headers = $http_response_header;
        //$link = $this->getResponseLink($headers[7]);
        //echo $link; die;
        //echo "<a href='$link'>next</a>";
 
        return $json;
    }

    private function getResponseLink($header) {
        $first = strrpos($header, '<') + 1;
        $last = strrpos($header, '>');
        return substr($header, $first, $last-$first);
    }
    
}
