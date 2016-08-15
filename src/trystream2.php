<?php
require_once __DIR__ . '/../vendor/autoload.php'; 
ini_set('max_execution_time',0);
//require_once('../lib/Phirehose.php');
//require_once('../lib/OauthPhirehose.php');

/**
 * Example of using Phirehose to display the 'sample' twitter stream.
 */
class FilterTrackConsumer extends OauthPhirehose
{
  /**
   * Enqueue each status
   *
   * @param string $status
   */
  public function enqueueStatus($status)
  {
    /*
     * In this simple example, we will just display to STDOUT rather than enqueue.
     * NOTE: You should NOT be processing tweets at this point in a real application, instead they should be being
     *       enqueued and processed asyncronously from the collection process.
     */
    $data = json_decode($status, true);
    if (is_array($data) && isset($data['user']['screen_name'])) {
      print "tweet : " . $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "<br>" ;
    }
  }
}
// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "jNC9glDFjyaPgIdC2SvIRRml9");
define("TWITTER_CONSUMER_SECRET", "ALzPlL8kdzmLrYQZZNzsckjeZ58oTTSLbi06X27tT8CynjzSOg");
// The OAuth data for the twitter account
define("OAUTH_TOKEN", "540627083-CehuUsiaoop4IZ0t1K2SuLrW6QTlLheA7tCnsw6E");
define("OAUTH_SECRET", "IWbQSC0jOGY3LlU2Oy8grCNGVUHmO3atJlZWqsDgMLdYA");
// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->setTrack(array('trending'));
$sc->consume();