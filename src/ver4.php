<?php 
    namespace TweetMining;
    ini_set('max_execution_time',0);
	require_once __DIR__ . '/../vendor/autoload.php';
	use Sastrawi\Stemmer\StemmerFactory;
	use Sastrawi\StopWordRemover\StopWordRemoverFactory; 
    use NlpTools\Tokenizers\WhitespaceTokenizer;
    use NlpTools\Models\FeatureBasedNB;
    use NlpTools\Documents\TrainingSet;
    use NlpTools\Documents\TokensDocument;
    use NlpTools\FeatureFactories\DataAsFeatures;
    use NlpTools\Classifiers\MultinomialNBClassifier;
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
    <title>Live Demo of Google Maps Geocoding Example with PHP</title>
     
    <style>
    body{
        font-family:arial;
        font-size:.8em;
    }
     
    input[type=text]{
        padding:0.5em;
        width:20em;
    }
     
    input[type=submit]{
        padding:0.4em;
    }
     
    #gmap_canvas{
        width:100%;
        height:30em;
    }
     
    #map-label,
    #address-examples{
        margin:1em 0;
    }
    </style>
</head>
<body>
<?php 
    //load training set file
    $training = array();
    if (($handle = fopen("combinetrain4.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $training[] = $data;
        }
        fclose($handle);
    }

    $fold = 10; //fold for testing w/ cross validation
    $total = count($training); //total data loaded
    $dperfold = $total / $fold; //total data per fold
    echo "Total data : " . $total . " with data perfold : " . $dperfold .  "<br>";


    //testing array initialization
    $testing = array();

	//stem initialization 
	$stemmerFactory = new StemmerFactory();
	$stemmer  = $stemmerFactory->createStemmer();
	
	//stopwords initialization 
	$stopwFactory = new StopWordRemoverFactory();
	$stopw = $stopwFactory->createStopWordRemover();

    //stem and remove stopwords training set 
	for($x = 0; $x < $total; $x++) {
        $training[$x][1] = preg_replace('/(?:https?|ftp):\/\/[\n\S]+/i', '', $training[$x][1]);
        $training[$x][1] = preg_replace('/\B@[a-z0-9_-]+/i', ' ', $training[$x][1]);
        $training[$x][1] = preg_replace('/[.,\/#!$%\^&\*;:{}=\-_`~()]/i', ' ', $training[$x][1]);
        $training[$x][1] = preg_replace('/\s{2,}/i', ' ', $training[$x][1]);
        $training[$x][1] = preg_replace('/[^\w.,\s]/i', '', $training[$x][1]);
        $training[$x][1] = preg_replace('/[0-9]/i', '', $training[$x][1]);
		$training[$x][1] = $stemmer->stem($training[$x][1]);
		$training[$x][1] = $stopw->remove($training[$x][1]);
		//echo $training[$x][1]."<br>";
	}

    //initialization for 10 Fold Cross Validation, split data to fold
    $counttweet = 0;
    //$countpart = 0;
    for($i = 0; $i < $fold; $i++) {
        for($j = 0; $j < $dperfold; $j++) {
            $training[$counttweet][3] = $i;
            //echo $training[$counttweet][1] . " is part " . $training[$counttweet][3] . "<br>";
            $counttweet++;
        }
    }

    $accuracy = 0; 
    //10 Fold Cross Validation begins
    for($ii = 0; $ii < $fold; $ii++) {

        $testcount = 0;
        $counter = 0;
        $correct = 0;   
        //classification initialization 
        $tset = new TrainingSet(); // will hold the training documents
        $tok = new WhitespaceTokenizer(); // will split into tokens
        $ff = new DataAsFeatures(); // see features in documentation

        // ---------- Training ----------------
        foreach ($training as $d)
        {
            //echo $d[3] . "<br>";
            //echo $d[1] . " " . $d[2];
            if($d[3] != $ii) {
                //echo $d[1] . " is for train. <br>";
                $tset->addDocument(
                    $d[2], // class
                    new TokensDocument(
                        $tok->tokenize($d[1]) // The actual document
                    )
                );
            } else {
                $testing[$testcount][0] = $d[0];
                $testing[$testcount][1] = $d[1];
                $testing[$testcount][2] = $d[2];
                $testing[$testcount][3] = $d[3];
                $testcount++;
            }
        }
        
        $model = new FeatureBasedNB(); // train a Naive Bayes model
        $model->train($ff,$tset);

        // ---------- Classification ----------------
        $cls = new MultinomialNBClassifier($ff,$model);
        //$correct = 0;
        //$counter = 0; 
        $places = array();
        $countplace = 0; 
        
        foreach ($testing as $d)
        {
            // predict if it is spam or ham
            //echo $d[1] . " " . $d[3] . " for test." . "<br>";
            $prediction = $cls->classify(
                array('T','F'), // all possible classes
                new TokensDocument(
                    $tok->tokenize($d[1]) // The document
                )
            );
            if ($prediction==$d[2]) {
                $correct++;
                if($d[2] == 'T') {
                    print $d[1] . "<br>";

                    //convert string to unigram
                    $bigram = new Bigram();
                    $unibitri = $bigram->tokenize($d[1]);
                    $countword = 0; 
                    $countall = count($unibitri);

                    //find word 'jl', 'jln', 'jalan', 'tol' to geocode
                    foreach($unibitri as $value) {
                        if($value == 'jalan' || $value == 'jl' || $value == 'jln' || $value == 'tol') {
                            if($countall - $countword == 2) {
                                $lokasi = $unibitri[$countword] . $unibitri[$countword + 1];
                                //printf($lokasi . "<br>");
                                $data_arr = geocode($lokasi);
                                $places[$countplace][3] = 'F';
                                if($data_arr) {
                                    //$printf("yey");
                                    $places[$countplace][0] = $data_arr[0];
                                    $places[$countplace][1] = $data_arr[1];
                                    $places[$countplace][2] = $data_arr[2];
                                    //$formatted_address = $data_arr[2];
                                    //print "found in : " . $lokasi . "<br>" . $latitude . "<br>" . $longitude . "<br>";
                                    $places[$countplace][3] = 'T';
                                }
                                if($places[$countplace][3] == 'T') {
                                    $countplace++;
                                }
                                //printf("1");
                                //printf($unibitri[$countword] . " " . $unibitri[$countword+1]);
                            } else if($countall - $countword == 3 ) {
                                $lokasi = $unibitri[$countword];
                                $places[$countplace][3] = 'F';
                                for($i = 0; $i < 2; $i++) {
                                    $lokasi = $lokasi . " " . $unibitri[$i+$countword+1];
                                    //printf($lokasi . "<br>");
                                    $data_arr = geocode($lokasi);
                                    if($data_arr) {
                                        //printf("yey");
                                        $places[$countplace][0] = $data_arr[0];
                                        $places[$countplace][1] = $data_arr[1];
                                        $places[$countplace][2] = $data_arr[2];
                                        //$formatted_address = $data_arr[2];
                                        //print "found in : " . $lokasi . "<br>" . $latitude . "<br>" . $longitude . "<br>";
                                        $places[$countplace][3] = 'T';
                                    }
                                }
                                if($places[$countplace][3] == 'T') {
                                    $countplace++;
                                }
                                //printf("2");
                                //printf($unibitri[$countword] . " " . $unibitri[$countword+1]);
                            } else if($countall - $countword >= 4) {
                                $lokasi = $unibitri[$countword];
                                $places[$countplace][3] = 'F';
                                for($i = 0; $i < 3; $i++) {
                                    $lokasi = $lokasi . " " . $unibitri[$i+$countword+1];
                                    //printf($lokasi . "<br>");
                                    $data_arr = geocode($lokasi);
                                    if($data_arr) {
                                        //printf("yey");
                                        $places[$countplace][0] = $data_arr[0];
                                        $places[$countplace][1] = $data_arr[1];
                                        $places[$countplace][2] = $data_arr[2];
                                        //$formatted_address = $data_arr[2];
                                        //print "found in : " . $lokasi . "<br>" . $latitude . "<br>" . $longitude . "<br>";
                                        $places[$countplace][3] = 'T';
                                    }
                                }
                                if($places[$countplace][3] == 'T') {
                                    $countplace++;
                                }
                            } else {
                                //printf("4");
                            }
                            
                        }
                        $countword++;

                    }
                    //print "<br><br>";
                }
                //echo $d[1] . " " . $d[2] . "<br>";
            } else {
                //echo $d[1] . " --- classification is " . $prediction . ". It should be " . $d[2] . "<br>";
            }
            $counter++;
        }
        $temp = 100*$correct / $counter;
        //echo $temp . "<br>";
        $accuracy = $accuracy + $temp;
        printf("Test " . $ii . " : " . $correct . " is correct. The accuracy is " . $temp  . " percent.  <br><br>");
        printf("<br> Places detected in : " . $countplace . " tweets.");
    }

    printf("Total accuracy: %.2f\n", $accuracy / $fold);
    //echo "<br>" . $correct . " out of " . $counter . " is correct.";
?>

    <div id="gmap_canvas">Loading map...</div>
    <div id='map-label'>Map shows approximate location.</div>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBovhzE3lBt6hB25gzPUdoIctwvQa6j378"></script>    
    <script type="text/javascript">
        var map = new google.maps.Map(document.getElementById('gmap_canvas'), {
            zoom: 7,
            center: new google.maps.LatLng(-7.7091461, 112.176335),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();
        var marker;
        var y = 0;
    </script>
    <?php $countformap = 0; ?>
    <?php for ($x = 0; $x < $countplace; $x++) {  ?>
    <script>
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(<?php echo $places[$x][0]; ?>, <?php echo $places[$x][1]; ?>),
            map: map
        });
    
        google.maps.event.addListener(marker, 'click', (function(marker, y) {
            return function() {
            infowindow.setContent("<?php echo $places[$x][2]; ?>");
            infowindow.open(map, marker);
            }
        })(marker, y));

        y++;
    </script>    
    <?php } ?>

<?php 
    //function to search in maps
    function geocode($address){
        
        //printf("masuk");
        // url encode the address
        $address = urlencode($address);
        
        // google map geocode api url
        $url = "http://maps.google.com/maps/api/geocode/json?address={$address}";
    
        // get the json response
        $resp_json = file_get_contents($url);
        
        // decode the json
        $resp = json_decode($resp_json, true);
    
        // response status will be 'OK', if able to geocode given address 
        if($resp['status']=='OK'){
    
            // get the important data
            $lati = $resp['results'][0]['geometry']['location']['lat'];
            $longi = $resp['results'][0]['geometry']['location']['lng'];
            $formatted_address = $resp['results'][0]['formatted_address'];
            
            // verify if data is complete
            if($lati && $longi && $formatted_address){
            
                // put the data in the array
                $data_arr = array();            
                
                array_push(
                    $data_arr, 
                        $lati, 
                        $longi, 
                        $formatted_address
                    );
                
                return $data_arr;
                
            }else{
                return false;
            }
            
        }else{
            return false;
        }
    }
?>
</body>
</html>