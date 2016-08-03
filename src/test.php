<?php 

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
use Sastrawi\Stemmer\StemmerFactory;
//use Wamania\Snowball\French;
$stemmerFactory = new StemmerFactory();
$stemmer  = $stemmerFactory->createStemmer();

// stem
$sentence = 'Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan';
$output   = $stemmer->stem($sentence);

//echo $output . "\n";
// ekonomi indonesia sedang dalam tumbuh yang bangga

echo $stemmer->stem('Memakan') . "\n";
// mereka tiru

?>