<?php

require 'vendor/autoload.php';

date_default_timezone_set('America/Fortaleza');

use Goutte\Client;

$client = new Client();

$urls = [];
array_push($urls, 'https://play.google.com/store/apps/details?id=com.ultimateguitar.tabs&hl=en');
array_push($urls, 'https://play.google.com/store/apps/details?id=com.outfit7.mytalkingtomfree&hl=en');

// Go to the symfony.com website
$crawler = $client->request('GET', $urls[0]);

$app = [];
//
$app['name'] = $crawler->filter('.id-app-title')->text();
$app['developer'] = $crawler->filter('[itemprop=author] [itemprop=name]')->text();
$app['icon_url'] = $crawler->filter('.cover-image')->attr('src');
$app['description'] = substr($crawler->filter('[itemprop=description] > div')->text(), 0, 100);
$app['price'] = trim($crawler->filter('.price')->text()) == 'Install' ? 'Free' : substr(trim($crawler->filter('.price')->text()), 0, -4);
$app['category'] = $crawler->filter('[itemprop=genre]')->text();
//
$date = DateTime::createFromFormat('F d, Y', $crawler->filter('[itemprop=datePublished]')->text());
$app['last_update'] = $date->format('Y-m-d');
//
// $app['version'] = $crawler->filter('[itemprop=softwareVersion]')->text();
// $app['languages'] = explode(', ', substr($crawler->filter('.language')->text(), 11));
// $app['author'] = $crawler->filter('[itemprop=author]')->text();
// $app['copyright'] = $crawler->filter('.copyright')->text();
//
$app['rating'] = doubleval($crawler->filter('[itemprop=ratingValue]')->attr('content'));
$app['rating_count'] = intval(str_replace(',', '', $crawler->filter('.reviews-num')->text()));
//
//
$screens = $crawler->filter('[itemprop=screenshot]')->each(function($node) use (&$app) {
    return$node->attr('src');
});
//
$app['screens'] = $screens;

print_r($app);
