<?php
// $ php WithoutDotEnv.php
// (vlucas/phpdotenv is not loaded in vendors)
require_once __DIR__ . '/../vendor/autoload.php'; 
use TwitterStreaming\Tracker;
use TwitterStreaming\Endpoints;
/**
 * @see https://apps.twitter.com
 */
$tracker = new Tracker([
    'TWITTERSTREAMING_CONSUMER_KEY' => 'jNC9glDFjyaPgIdC2SvIRRml9',
    'TWITTERSTREAMING_CONSUMER_SECRET' => 'ALzPlL8kdzmLrYQZZNzsckjeZ58oTTSLbi06X27tT8CynjzSOg',
    'TWITTERSTREAMING_TOKEN' => '540627083-CehuUsiaoop4IZ0t1K2SuLrW6QTlLheA7tCnsw6E',
    'TWITTERSTREAMING_TOKEN_SECRET' => 'IWbQSC0jOGY3LlU2Oy8grCNGVUHmO3atJlZWqsDgMLdYA'
]);

$tracker
    ->endpoint('user')
    ->parameters([
        'track' => 'kecelakaan'
    ])
    ->track(function ($tweet) {
        // Print the tweet object
        print"Tweet details:" . PHP_EOL;
        print "User: @" . $tweet->user->screen_name . PHP_EOL;
        print "Content: " . $tweet->text . PHP_EOL;
        print_r($tweet);
    });
