simple-php-tools
================

Simple PHP Tools

- Twitter
- RSS2

If RSS2 doesn't work in your webhost do something like this:
(I will fix this issue when I find some time!)
<ul class="slides">
<?php
$curl = curl_init();

curl_setopt_array($curl, Array(
    CURLOPT_URL            => 'http://blog.altris.co.nz/?feed=rss2',
    CURLOPT_USERAGENT      => 'spider',
    CURLOPT_TIMEOUT        => 120,
    CURLOPT_CONNECTTIMEOUT => 30,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_ENCODING       => 'UTF-8'
));
 
$data = curl_exec($curl);
curl_close($curl); 
$xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);

foreach ($xml->channel->item as $item) :
    $creator = $item->children('dc', TRUE);
?>
    <li>
        <article>
            <h4>BLOG</h4>
            <br/>
            <br/>
            <div class="clear"></div>
            <p>
                <?php echo $item->title ?>
                <br/>
                <a href="<?php echo $item->link ?>" target="_blank">Read more &raquo;</a>
            </p>
        </article>  
    </li>
<?php
endforeach;
?> 
</ul><!--.slides-->
