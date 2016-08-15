<?php 
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
    if (($handle = fopen("training.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $training[] = $data;
        }
        fclose($handle);
    }

    //load testing set file
    $testing = array();
    if (($handle = fopen("testing.csv", "r")) !== FALSE) {
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
	for($x = 0; $x < 80; $x++) {
		$training[$x][1] = $stemmer->stem($training[$x][1]);
		$training[$x][1] = $stopw->remove($training[$x][1]);
		//echo $twoDarray[$x][1]."<br>";
	}

    //stem and remove stopwords testing set 
	for($x = 0; $x < 14; $x++) {
		$training[$x][1] = $stemmer->stem($training[$x][1]);
		$training[$x][1] = $stopw->remove($training[$x][1]);
		//echo $twoDarray[$x][1]."<br>";
	}

    //classification initialization 
    $tset = new TrainingSet(); // will hold the training documents
    $tok = new WhitespaceTokenizer(); // will split into tokens
    $ff = new DataAsFeatures(); // see features in documentation

    // ---------- Training ----------------
    foreach ($training as $d)
    {
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
            array('terkait','tidakterkait'), // all possible classes
            new TokensDocument(
                $tok->tokenize($d[1]) // The document
            )
        );
        if ($prediction==$d[2]) {
            $correct ++;
            printf($counter, " ");
        }
            
        $counter ++;
    }
    
    printf("Accuracy: %.2f\n", 100*$correct / count($testing));

?>
