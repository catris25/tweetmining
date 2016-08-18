<?php
    namespace TweetMining;

    require_once __DIR__ . '/../vendor/autoload.php'; 
    use NlpTools\Tokenizers\TokenizerInterface;

    class Bigram implements TokenizerInterface
    {
        private $n = 3; // phrase word length
        
        const PATTERN = '/[\pZ\pC]+/u';
        public function set_n( $n ) {
        $this->n = $n;
        }
        public function tokenize( $str )
        {
        
            // generate unigrams
            $unigrams = preg_split(self::PATTERN,$str,null,PREG_SPLIT_NO_EMPTY);
            $num_unigrams = count( $unigrams );
            
            // generate other nGrams
            $ngrams = array();
            for( $n=3; $n<=$this->n; $n++ ) {
                //echo "hooray";
                // loop through each unigram location in the text
                for( $i=0; $i<=$num_unigrams-$n; $i++ ) {
                    $key = $i;
                    $ngram = array();
                    for( $key=$i; $key<$i+$n; $key++ )
                    $ngram[] = $unigrams[$key];
                    $ngrams[] = implode( ' ', $ngram );
                }
            }
            
            // combine unigrams with new ngrams
            $ngrams = array_merge( $unigrams );
            //print_r($ngrams);
            return $ngrams;
            
        }
    }
?>