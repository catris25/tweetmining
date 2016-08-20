<?php
require_once 'lib/twitteroauth.php';

define('CONSUMER_KEY', 'gvRiFt2Gwfe9XItc1EaWUSegk');
define('CONSUMER_SECRET', 'Upomt5fFMSSC1zcVMijOUPd7NdSL4JMURDzDS0RfjUr9iJGaqb');
define('ACCESS_TOKEN', '100399829-UAGazL2iC23uvFLHio461YwWPeb0Om0rxe44egRY');
define('ACCESS_TOKEN_SECRET', 'TUapT9Fo2TBhysSlY9EtRu4J1lsVh56FKjE2lrtKsHFns');

// $users = array('TMCPoldaMetro');
$user = 'PTJASAMARGA';
//, 'TMCPolresBogor', 'tvOneNews', 'detikcom', 'RepublikaOnline', 'Metro_TV', 'tribunnews', 'RTMC_PoldaJabar', 'RTMCJatim'
// 'RTMCRiau', 'OfficialNETNews', 'RadioElshinta', 'suaramerdeka', 'tmcrestabekasi', 'POLRES_BPPN', 'idbreakingnews'
// 'infomacetcom', 'lewatmana'
// 'PasangMata', 'tempodotco', 'hariankompas', 'VIVAcoid', 'korantempo', 'antaranews', 'mediaindonesia', 'SINDOnews'
// 'lantas_subang', 'restadepok', 'Lantas_Surabaya', 'RTMC_Jogja','LantasResMlg', 'lantascianjur', 'BeritaCenter'
// 'ditlantasbali', 'LantasPamekasan', 'lantascianjur', 'rpkdfm', 'PTJASAMARGA  '

$toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

$pg = 1;
$loop = 0;
$tn = 1;

$tweets= array();

while($loop<16){
  $content = $toa->get('statuses/user_timeline', array(
    'count' => 200,
    'exclude_replies' => true,
    'screen_name' => $user,
    'include_rts' => FALSE,
    'page' => $pg
  ));

  foreach ($content as $i=> $tweet) {
      if (strpos($tweet->text, 'kecelakaan') !== false) {
        $tweetData = "$tweet->id,\"$tweet->text\"";
        $tweets[] = $tweetData;
        echo "$tweetData<br>";
        // echo "$tn: ($tweet->created_at) $tweet->text"."<br>";
        $tn++;
      }

  }
  // print_r ($tweets);
  $pg++;
  $loop++;
}


// header("Content-Type: text/csv");
// header("Content-Transfer-Encoding: binary");
// header('Content-disposition: attachment;filename=tweets_'.$user.'.csv');
// header("Pragma: no-cache");
// header("Expires: 0");
// $header = array("id","tweet");
// $fp = fopen("php://output", "w");
// fputcsv ($fp, $header);
// foreach($tweets as $row){
//     fputcsv($fp, $row);
// }
// fclose($fp);



?>
