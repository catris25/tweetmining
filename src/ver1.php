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
    if (($handle = fopen("combinetrain.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $training[] = $data;
        }
        fclose($handle);
    }

    //load testing set file
    $testing = array();
    if (($handle = fopen("combinetest.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $testing[] = $data;
        }
        fclose($handle);
    }

	//stem initialization 
	$stemmerFactory = new StemmerFactory();
	$stemmer  = $stemmerFactory->createStemmer();
	
	//stopwords initialization 
	$stopwFactory = new StopWordRemoverFactory();
	$stopw = $stopwFactory->createStopWordRemover();

    //stem and remove stopwords training set 
	for($x = 0; $x < 341; $x++) {
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

    //stem and remove stopwords testing set 
	for($x = 0; $x < 90; $x++) {
		$testing[$x][1] = $stemmer->stem($testing[$x][1]);
		$testing[$x][1] = $stopw->remove($testing[$x][1]);
		//echo $twoDarray[$x][1]."<br>";
	}

    //classification initialization 
    $tset = new TrainingSet(); // will hold the training documents
    $tok = new WhitespaceTokenizer(); // will split into tokens
    $ff = new DataAsFeatures(); // see features in documentation

    // ---------- Training ----------------
    foreach ($training as $d)
    {
        //echo $d[1] . " " . $d[2];
        $tset->addDocument(
            $d[2], // class
            new TokensDocument(
                $tok->tokenize($d[1]) // The actual document
            )
        );
    }
    
    $model = new FeatureBasedNB(); // train a Naive Bayes model
    $model->train($ff,$tset);
    
    
    // ---------- Classification ----------------
    $cls = new MultinomialNBClassifier($ff,$model);
    $correct = 0;
    $counter = 0; 
    
    foreach ($testing as $d)
    {
        // predict if it is spam or ham
        $prediction = $cls->classify(
            array('T','F'), // all possible classes
            new TokensDocument(
                $tok->tokenize($d[1]) // The document
            )
        );
        if ($prediction==$d[2]) {
            $correct ++;
            echo $d[1] . " " . $d[2] . "<br>";
        //     //printf($counter, " ");
        //     if($d[2] == 'terkait') {
        //         print $d[1] . "<br>";
        //         //ada pembatas   jalan solo jogja
        //         //0   1          2     3    4     5       6    7     8   9
        //         //convert string to unigram
        //         $bigram = new Bigram();
        //         $unibitri = $bigram->tokenize($d[1]);
        //         $countword = 0; 
        //         $countall = count($unibitri);
        //         $add = "Jl HR Rasuna Said";
        //         $data_arr = geocode($add);
        //         if($data_arr) {
        //             printf("horee");
        //         }
        //         // foreach($unibitri as $value) {
        //         //     if($value == 'jalan' || $value == 'jl' || $value == 'jln' || $value == 'tol') {
        //         //         if($countall - $countword == 2) {
        //         //             $lokasi = $unibitri[$countword] . $unibitri[$countword + 1];
        //         //             printf($lokasi);
        //         //             $data_arr = geocode($lokasi);
        //         //             if($data_arr) {
        //         //                 $printf("yey");
        //         //                 $latitude = $data_arr[0];
        //         //                 $longitude = $data_arr[1];
        //         //                 //$formatted_address = $data_arr[2];
        //         //                 print "found in : " . $value . "<br>" . $latitude . "<br>" . $longitude . "<br>";
        //         //             }
        //         //             //printf("1");
        //         //             //printf($unibitri[$countword] . " " . $unibitri[$countword+1]);
        //         //         } else if($countall - $countword == 3 ) {
        //         //             $lokasi = $unibitri[$countword];
        //         //             for($i = 0; $i < 2; $i++) {
        //         //                 $lokasi = $lokasi . " " . $unibitri[$i+$countword+1];
        //         //                 printf($lokasi);
        //         //                 $data_arr = geocode($lokasi);
        //         //                 if($data_arr) {
        //         //                     printf("yey");
        //         //                     $latitude = $data_arr[0];
        //         //                     $longitude = $data_arr[1];
        //         //                     //$formatted_address = $data_arr[2];
        //         //                     print "found in : " . $value . "<br>" . $latitude . "<br>" . $longitude . "<br>";
        //         //                 }
        //         //             }
        //         //             //printf("2");
        //         //             //printf($unibitri[$countword] . " " . $unibitri[$countword+1]);
        //         //         } else if($countall - $countword >= 4) {
        //         //             $lokasi = $unibitri[$countword];
        //         //             for($i = 0; $i < 3; $i++) {
        //         //                 $lokasi = $lokasi . " " . $unibitri[$i+$countword+1];
        //         //                 printf($lokasi);
        //         //                 $data_arr = geocode($lokasi);
        //         //                 if($data_arr) {
        //         //                     printf("yey");
        //         //                     $latitude = $data_arr[0];
        //         //                     $longitude = $data_arr[1];
        //         //                     //$formatted_address = $data_arr[2];
        //         //                     print "found in : " . $value . "<br>" . $latitude . "<br>" . $longitude . "<br>";
        //         //                 }
        //         //             }
        //         //         } else {
        //         //             //printf("4");
        //         //         }
                        
        //         //     }
        //         //     $countword++;
        //         //     // //search in maps
        //         //     // $data_arr = geocode($value);
    
        //         //     // // if able to geocode the address
        //         //     // if($data_arr){
                        
        //         //     //     $latitude = $data_arr[0];
        //         //     //     $longitude = $data_arr[1];
        //         //     //     $formatted_address = $data_arr[2];
        //         //     //     print "found in : " . $value . "<br>" . $latitude . "<br>" . $longitude . "<br>";
        //         //     // }
        //         // }
        //         print "<br><br>";
        //     }
            
        //     $counter ++;
        }
    }

    printf("Accuracy: %.2f\n", 100*$correct / count($testing));
    
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
