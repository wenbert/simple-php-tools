<?php

/**
 * Twitter Class for API 1.1
 *
 * USAGE:
 * =========================
 * include('twitter.php');
 * $opts = array(
 *     'oauth_access_token' => "xxx",
 *     'oauth_access_token_secret' => "xxx",
 *     'consumer_key' => "xxx",
 *     'consumer_secret' => "xxx",
 *     'count' => 2,
 *     'screen_name' => "wenbert",
 * );
 *
 * $twitter = new Twitter($opts);
 * $tweets = $twitter->fetchTweets();
 * foreach($tweets AS $tweet) {
 *     echo $twitter->parseLinks($tweet->text);
 * }
 *
 */
class Twitter {

    public $_url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
    public $_oauth_access_token = "";
    public $_oauth_access_token_secret = "";
    public $_consumer_key = "";
    public $_consumer_secret = "";
    public $_count = 10;
    public $_screen_name = "";

    public function __construct($options = array()) {
        $this->_oauth_access_token = $options['oauth_access_token'];
        $this->_oauth_access_token_secret = $options['oauth_access_token_secret'];
        $this->_consumer_key = $options['consumer_key'];
        $this->_consumer_secret = $options['consumer_secret'];
        $this->_screen_name = $options['screen_name'];
        $this->_count = $options['count'];
    }

    //http://blog.ekini.net/2013/06/28/get-tweets-from-user-timeline-with-api-1-1-php/
    private function _buildBaseString($baseURI, $method, $params) {
        $r = array();
        ksort($params);
        foreach($params as $key=>$value){
            $r[] = "$key=" . rawurlencode($value);
        }
        return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }
     
    private function _buildAuthorizationHeader($oauth) {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach($oauth as $key=>$value)
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        $r .= implode(', ', $values);
        return $r;
    }
    
    /**
     * Fetches the twitter data 
     *
     * @return json
     */
    public function fetchTweets() {
        $oauth = array( 'screen_name' => $this->_screen_name,
                        'count' => $this->_count,
                        'oauth_consumer_key' => $this->_consumer_key,
                        'oauth_nonce' => time(),
                        'oauth_signature_method' => 'HMAC-SHA1',
                        'oauth_token' => $this->_oauth_access_token,
                        'oauth_timestamp' => time(),
                        'oauth_version' => '1.0');
         
        $base_info = $this->_buildBaseString($this->_url, 'GET', $oauth);
        $composite_key = rawurlencode($this->_consumer_secret) . '&' . rawurlencode($this->_oauth_access_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;
         
        // Make Requests
        $header = array($this->_buildAuthorizationHeader($oauth), 'Expect:');
        $options = array( CURLOPT_HTTPHEADER => $header,
                          //CURLOPT_POSTFIELDS => $postfields,
                          CURLOPT_HEADER => false,
                          CURLOPT_URL => $this->_url."?screen_name=".$this->_screen_name."&count=".$this->_count,
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_SSL_VERIFYPEER => false);
         
        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);
         
        $twitter_data = json_decode($json);
        return $twitter_data;
    }

    /**
     * Auto link urls, etc.
     * @param string $tweet
     * @return string
     */
    public function parseLinks($tweet = "") {
        $text = $tweet;
        $text = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $text);
        $text = preg_replace("/#([A-Za-z0-9\/\.]*)/", "<a target=\"_new\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $text);
        $text = preg_replace("/@([A-Za-z0-9\/\.]*)/", "<a href=\"http://www.twitter.com/$1\">@$1</a>", $text);
        return $text;
    }
}

