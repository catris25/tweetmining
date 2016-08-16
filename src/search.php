
<html>
<?php
require_once __DIR__ . '/../vendor/autoload.php'; 
ini_set('max_execution_time',0);
use Abraham\TwitterOAuth\TwitterOAuth;

define('CONSUMER_KEY', 'jNC9glDFjyaPgIdC2SvIRRml9');
define('CONSUMER_SECRET', 'ALzPlL8kdzmLrYQZZNzsckjeZ58oTTSLbi06X27tT8CynjzSOg');
define('ACCESS_TOKEN', '540627083-CehuUsiaoop4IZ0t1K2SuLrW6QTlLheA7tCnsw6E');
define('ACCESS_TOKEN_SECRET', 'IWbQSC0jOGY3LlU2Oy8grCNGVUHmO3atJlZWqsDgMLdYA');
 
function search(array $query)
{
  $toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
  return $toa->get('search/tweets', $query);
}
 
$query = array(
  //"q" => " kecelakaan -filter:retweets",
  "q" => "kecelakaan",
  "since" => "2016-08-01",
  "until" => "2016-08-03",
  "max_id" => "765097458259091456",
  //"since_id" => "700000000000000000",
  //"count" => 100,
  "include_entities" => "false"
);


$results = search($query);

header('Content-Type: application/json');
$results = json_encode($results, JSON_PRETTY_PRINT);

$contentsDecoded = json_decode($results, true);

foreach ($contentsDecoded['statuses'] as $tweet => $value) {
	//reformat the date time when the tweet is created
	$date = new DateTime($value['created_at']);
  $formatted_date = $date->format('Y-m-d\TH:i:s.\0\0\0\Z');
	
	$contentsDecoded['statuses'][$tweet]['created_at']=$formatted_date;

  //reformat the date time when the account is created
  $dateAccount = new DateTime($value['user']['created_at']);
  $formatted_date = $dateAccount->format('Y-m-d\TH:i:s.\0\0\0\Z');
  
  $contentsDecoded['statuses'][$tweet]['user']['created_at']=$formatted_date;
	
}

header('Content-Type: application/json'); 
$newResults= json_encode($contentsDecoded, JSON_PRETTY_PRINT);
echo $newResults;
file_put_contents('tweets_training_v4.json', $newResults);

?>

<script type="text/javascript">
    

</script>


</html>
