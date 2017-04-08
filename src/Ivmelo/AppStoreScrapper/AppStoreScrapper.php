<?php

namespace Ivmelo\AppStoreScrapper;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Scraps app data from the App Store and Google Play Store.
 * @author Ivanilson Melo
 */
class AppStoreScrapper
{

    /**
     * The Goutte HTTP Client.
     *
     * @var client
     */
    private $client;

    /**
     * Constructor for the class.
     */
    function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Gets app data from the App Store.
     *
     * @var String $app_url
     * @return Array $app
     */
    public function getAppData($app_url)
    {
        $crawler = $this->client->request('GET', $app_url);

        $app = [];

        $app['name'] = $crawler->filter('[itemprop=name]')->text();
        $app['developer'] = $crawler->filter('[itemprop=author]')->text();
        $app['icon_url'] = $crawler->filter('.artwork > meta')->attr('content');
        $app['description'] = substr($crawler->filter('[itemprop=description]')->text(), 0, 100);
        $app['price'] = $crawler->filter('.price')->text();
        $app['category'] = $crawler->filter('[itemprop=applicationCategory]')->text();

        $date = DateTime::createFromFormat('M d, Y', $crawler->filter('[itemprop=datePublished]')->text());
        $app['last_update'] = $date->format('Y-m-d');

        $app['version'] = $crawler->filter('[itemprop=softwareVersion]')->text();
        $app['languages'] = explode(', ', substr($crawler->filter('.language')->text(), 11));
        $app['copyright'] = $crawler->filter('.copyright')->text();

        $app['rating'] = doubleval($crawler->filter('[itemprop=ratingValue]')->text());
        $app['rating_count'] = intval($crawler->filter('.rating .rating-count')->eq(1)->text());


        $screens = $crawler->filter('[itemprop=screenshot]')->each(function($node) use (&$app) {
            return$node->attr('src');
        });

        $app['screens'] = $screens;

        return $app;
    }
}
