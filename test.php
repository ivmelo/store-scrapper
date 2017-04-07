<?php

require 'vendor/autoload.php';

date_default_timezone_set('America/Fortaleza');

use Goutte\Client;

$client = new Client();

$urls = [];
array_push($urls, 'https://itunes.apple.com/us/app/tabs-chords-by-ultimate-guitar-learn-and-play/id357828853?mt=8');
array_push($urls, 'https://itunes.apple.com/us/app/my-talking-tom/id657500465?mt=8');

// Go to the symfony.com website
$crawler = $client->request('GET', $urls[0]);

// echo $crawler->text();

$app = [];

$app['name'] = $crawler->filter('[itemprop=name]')->text();
$app['developer'] = substr($crawler->filter('#title h2')->text(), 3);
$app['icon_url'] = $crawler->filter('.artwork > meta')->attr('content');
$app['description'] = substr($crawler->filter('[itemprop=description]')->text(), 0, 100);
$app['price'] = $crawler->filter('.price')->text();
$app['category'] = $crawler->filter('[itemprop=applicationCategory]')->text();

$date = DateTime::createFromFormat('M d, Y', $crawler->filter('[itemprop=datePublished]')->text());
$app['last_update'] = $date->format('Y-m-d');

$app['version'] = $crawler->filter('[itemprop=softwareVersion]')->text();
$app['language'] = $crawler->filter('.language')->text();
$app['author'] = $crawler->filter('[itemprop=author]')->text();
$app['copyright'] = $crawler->filter('.copyright')->text();

$screens = $crawler->filter('[itemprop=screenshot]')->each(function($node) use (&$app) {
    return$node->attr('src');
});

$app['screens'] = $screens;

print_r($app);




print_r($app);
