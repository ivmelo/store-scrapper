<?php

require dirname(__DIR__).'/vendor/autoload.php';

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

use Ivmelo\AppStoreScrapper\AppStoreScrapper;

date_default_timezone_set('America/Fortaleza');

$play_store_urls = [];
array_push($play_store_urls, 'https://play.google.com/store/apps/details?id=com.ultimateguitar.tabs&hl=en');
array_push($play_store_urls, 'https://play.google.com/store/apps/details?id=com.outfit7.mytalkingtomfree&hl=en');

$app_store_urls = [];
array_push($app_store_urls, 'https://itunes.apple.com/us/app/tabs-chords-by-ultimate-guitar-learn-and-play/id357828853?mt=8');
array_push($app_store_urls, 'https://itunes.apple.com/us/app/my-talking-tom/id657500465?mt=8');

$appstore = new AppStoreScrapper();
print_r($appstore->getAppStoreAppData($app_store_urls[0]));
print_r($appstore->getPlayStoreAppData($play_store_urls[0]));
