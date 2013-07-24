<?php

/**
 * A simple class to read an RSS2 feed
 *
 * USAGE:
 * =========================
 * include('rss2.php');
 * $rss2 = new RSS2Feed(array('url' => 'http://en.blog.wordpress.com/feed/'));
 * $feed = $rss2->read();
 * foreach($feed as $item) {
 *     echo $item['title'];
 * }
*/
class RSS2Feed {
    public $rss_url = "";
    public function __construct($opts = array()) {
        $this->_rss_url = $opts['url'];
    }

    public function read() {
        $doc = new DOMDocument();
        $doc->load($this->_rss_url);
        $feed = array();
        foreach ($doc->getElementsByTagName('item') as $node) {
            $feed[] = array(
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'comments' => $node->getElementsByTagName('comments')->item(0)->nodeValue,
                'pubDate' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
                'description' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'content' => $node->getElementsByTagName('encoded')->item(0)->nodeValue,
            );
        }

        return $feed;
    }
}

$rss2 = new RSS2Feed(array('url' => 'http://blog.altris.co.nz/?feed=rss2'));
$feed = $rss2->read();
