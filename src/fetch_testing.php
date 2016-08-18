<?php
require_once 'lib/twitteroauth.php';

define('CONSUMER_KEY', 'gvRiFt2Gwfe9XItc1EaWUSegk');
define('CONSUMER_SECRET', 'Upomt5fFMSSC1zcVMijOUPd7NdSL4JMURDzDS0RfjUr9iJGaqb');
define('ACCESS_TOKEN', '100399829-UAGazL2iC23uvFLHio461YwWPeb0Om0rxe44egRY');
define('ACCESS_TOKEN_SECRET', 'TUapT9Fo2TBhysSlY9EtRu4J1lsVh56FKjE2lrtKsHFns');

$users = array('TMCPoldaMetro');
//, 'TMCPolresBogor', 'tvOneNews', 'detikcom', 'RepublikaOnline'

$toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

echo "Fetching Tweets<br>";

for($u=0; $u<count($users); $u++){

  $pg = 1;
  $loop = 0;
  $tn = 1;

  echo "tweets by @".$users[$u]."<br>";

  while($loop<16){
    $content = $toa->get('statuses/user_timeline', array(
      'count' => 200,
      'exclude_replies' => true,
      'screen_name' => $users[$u],
      'include_rts' => 1,
      'page' => $pg
    ));

    foreach ($content as $i=> $tweet) {
        if (strpos($tweet->text, 'kecelakaan') !== false) {
          echo "$tn: ($tweet->created_at) $tweet->text"."<br>";
          $tn++;
        }

    }
    $pg++;
    $loop++;
  }

}



?>
