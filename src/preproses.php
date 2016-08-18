<?php 
	require_once __DIR__ . '/../vendor/autoload.php';
	use Sastrawi\Stemmer\StemmerFactory;
	use Sastrawi\StopWordRemover\StopWordRemoverFactory; 

	//open csv file to stem and stopwords removal
	$twoDarray = array();
	if (($handle = fopen("datafix.csv", "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	        $twoDarray[] = $data;
	    }
	    fclose($handle);
	}

	//stem
	$stemmerFactory = new StemmerFactory();
	$stemmer  = $stemmerFactory->createStemmer();
	
	//stopwords
	$stopwFactory = new StopWordRemoverFactory();
	$stopw = $stopwFactory->createStopWordRemover();

	for($x = 0; $x < 7; $x++) {
		$twoDarray[$x][1] = $stemmer->stem($twoDarray[$x][1]);
		$twoDarray[$x][1] = $stopw->remove($twoDarray[$x][1]);
		//echo $twoDarray[$x][1]."<br>";
	}

	print_r ($twoDarray);
	//export again to csv
	// header("Content-Type: text/csv");
	// header("Content-Transfer-Encoding: binary");
  	// header('Content-disposition: attachment;filename=datafixx.csv');
  	// header("Pragma: no-cache");
  	// header("Expires: 0");
  	// $header = array("id","tweet");
	// $fp = fopen("php://output", "w");
	// fputcsv ($fp, $header);
	// foreach($twoDarray as $row){
    //     fputcsv($fp, $row);
    // }
    // fclose($fp);

?>
