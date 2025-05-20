<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'SimplePie/autoloader.php';

$feed = new SimplePie();
$feed->set_feed_url('https://rss.nytimes.com/services/xml/rss/nyt/HomePage.xml');
$feed->init();

if ($feed->error()) {
    die("SimplePie Error: " . $feed->error());
}

echo "<h2>RSS Feed Loaded</h2>";
foreach ($feed->get_items(0, 3) as $item) {
    echo "<h3>" . $item->get_title() . "</h3>";
    echo "<p>" . $item->get_description() . "</p>";
}
?>