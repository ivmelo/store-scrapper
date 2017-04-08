<?php

namespace Ivmelo\StoreScraper;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Scraps app data from the App Store and Google Play Store.
 * @author Ivanilson Melo
 */
class Scraper
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
     * Get the top 100 free apps from the App Store.
     *
     * @return Array $top_apps
     */
    public function getAppStoreTopFree()
    {
        $top_apps = [];

        $crawler = $this->client->request('GET', 'http://www.apple.com/itunes/charts/free-apps/');

        $top_apps = $crawler->filter('.section-content li')->each(function($node){
            $app_data = [];

            $app_data['position'] = intval($node->filter('strong')->text());
            $app_data['name'] = $node->filter('h3')->text();
            $app_data['category'] = $node->filter('h4')->text();
            $app_data['app_icon']  = 'https://www.apple.com' . $node->filter('a > img')->attr('src');
            $app_data['url'] = $node->filter('a')->attr('href');

            return $app_data;
        });

        return $top_apps;
    }

    /**
     * Get the top 100 paid apps from the App Store.
     *
     * @return Array $top_apps
     */
    public function getAppStoreTopPaid()
    {
        $top_apps = [];

        $crawler = $this->client->request('GET', 'http://www.apple.com/itunes/charts/paid-apps/');

        $top_apps = $crawler->filter('.section-content li')->each(function($node){
            $app_data = [];

            $app_data['position'] = intval($node->filter('strong')->text());
            $app_data['name'] = $node->filter('h3')->text();
            $app_data['category'] = $node->filter('h4')->text();
            $app_data['app_icon']  = 'https://www.apple.com' . $node->filter('a > img')->attr('src');
            $app_data['url'] = $node->filter('a')->attr('href');

            return $app_data;
        });

        return $top_apps;
    }

    /**
     * Get the top 100 free apps from Google Play Store.
     *
     * @return Array $top_apps
     */
    public function getPlayStoreTopFree($store = 'us')
    {
        $top_apps = [];

        $crawler = $this->client->request('GET', 'https://play.google.com/store/apps/collection/topselling_free?hl=en&num=100&gl=' . $store);

        $top_apps = $crawler->filter('.card.small.square-cover')->each(function($node){
            $app_data = [];

            $app_data['position'] = intval($node->filter('.title')->text());
            $app_data['name'] = $node->filter('.title')->text();
            $app_data['category'] = $node->filter('.subtitle')->text();
            $app_data['app_icon']  = 'https:' . $node->filter('.cover-image')->attr('data-cover-large');
            $app_data['url'] = 'https://play.google.com' . $node->filter('.card-click-target')->attr('href');

            return $app_data;
        });

        return $top_apps;
    }

    /**
     * Get the top 60 paid apps from Google Play Store.
     *
     * @return Array $top_apps
     */
    public function getPlayStoreTopPaid($store = 'us')
    {
        $top_apps = [];

        $crawler = $this->client->request('GET', 'https://play.google.com/store/apps/collection/topselling_paid?hl=en&num=100&gl=' . $store);

        $top_apps = $crawler->filter('.card.small.square-cover')->each(function($node){
            $app_data = [];

            $app_data['position'] = intval($node->filter('.title')->text());
            $app_data['name'] = $node->filter('.title')->text();
            $app_data['category'] = $node->filter('.subtitle')->text();
            $app_data['app_icon']  = 'https:' . $node->filter('.cover-image')->attr('data-cover-large');
            $app_data['url'] = 'https://play.google.com' . $node->filter('.card-click-target')->attr('href');

            return $app_data;
        });

        return $top_apps;
    }

    /**
     * Gets app data from the App Store.
     *
     * @var String $app_url
     * @return Array $app
     */
    public function getAppStoreAppData($app_url)
    {
        $crawler = $this->client->request('GET', $app_url);

        $app = [];

        $app['name'] = $crawler->filter('[itemprop=name]')->text();
        $app['developer'] = $crawler->filter('[itemprop=author]')->text();
        $app['icon_url'] = $crawler->filter('.artwork > meta')->attr('content');
        $app['description'] = substr($crawler->filter('[itemprop=description]')->text(), 0, 100);
        $app['price'] = $crawler->filter('.price')->text();
        $app['category'] = $crawler->filter('[itemprop=applicationCategory]')->text();

        $date = \DateTime::createFromFormat('M d, Y', $crawler->filter('[itemprop=datePublished]')->text());
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

    /**
     * Gets app data from Google Play Store.
     *
     * @var String $app_url
     * @return Array $app
     */
    public function getPlayStoreAppData($app_url)
    {
        $crawler = $this->client->request('GET', $app_url);

        $app = [];

        $app['name'] = $crawler->filter('.id-app-title')->text();
        $app['developer'] = $crawler->filter('[itemprop=author] [itemprop=name]')->text();
        $app['icon_url'] = $crawler->filter('.cover-image')->attr('src');
        $app['description'] = substr($crawler->filter('[itemprop=description] > div')->text(), 0, 100);
        $app['price'] = trim($crawler->filter('.price')->text()) == 'Install' ? 'Free' : substr(trim($crawler->filter('.price')->text()), 0, -4);
        $app['category'] = $crawler->filter('[itemprop=genre]')->text();

        $date = \DateTime::createFromFormat('F d, Y', $crawler->filter('[itemprop=datePublished]')->text());
        $app['last_update'] = $date->format('Y-m-d');

        // $app['version'] = $crawler->filter('[itemprop=softwareVersion]')->text();
        // $app['languages'] = explode(', ', substr($crawler->filter('.language')->text(), 11));
        // $app['author'] = $crawler->filter('[itemprop=author]')->text();
        // $app['copyright'] = $crawler->filter('.copyright')->text();

        $app['rating'] = doubleval($crawler->filter('[itemprop=ratingValue]')->attr('content'));
        $app['rating_count'] = intval(str_replace(',', '', $crawler->filter('.reviews-num')->text()));

        $screens = $crawler->filter('[itemprop=screenshot]')->each(function($node) use (&$app) {
            return$node->attr('src');
        });

        $app['screens'] = $screens;

        return $app;
    }
}
