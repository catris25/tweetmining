<?php
    namespace TweetMining;
    require_once __DIR__ . '/../vendor/autoload.php'; 

    $bigram = new Bigram();
    $result = $bigram->tokenize("aku habis makan buah kelengkeng.");
    foreach($result as $value) {
        echo "$value <br>";
    }
?>