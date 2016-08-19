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
    for($i = 0; $i < $fold; $i++) {

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
            if($d[3] != $i) {
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
                //echo $d[1] . " " . $d[2] . "<br>";
            } else {
                echo $d[1] . " --- classification is " . $prediction . ". It should be " . $d[2] . "<br>";
            }
            $counter++;
        }
        $temp = 100*$correct / $counter;
        //echo $temp . "<br>";
        $accuracy = $accuracy + $temp;
        printf("Test " . $i . " : " . $correct . " is correct. The accuracy is " . $temp  . " percent.  <br><br>");
        
    }

    printf("Total accuracy: %.2f\n", $accuracy / $fold);
    //echo "<br>" . $correct . " out of " . $counter . " is correct.";
    
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
