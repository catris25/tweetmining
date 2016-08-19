|||GEMASTIK 9 DATA MINING - THE BIG OMEGA|||
Changelog : 19 - 08 - 2016 .

This is a READ ME file. 
Instructions : 

***You can find all the files in src folder***

---VERSION 1---
***Using different datasets for training set and testing set***
Note : If you already have a .csv training & testing file, you can skip to step 3. If you want to fetch data first 
       from Twitter, you can follow from step 1. 

1. Run search.php, after that you will get a .json file from Twitter. 
2. Run cobaparsing.php, after that you will get a .csv file from a .json file that you get at step 1. 
   This is for changing your .json file to .csv file.
3. Run ver1.php, remember to put training.csv (your training set) and testing.csv (your testing set) in the same directory. 
   This is for removing the punctuations, mentions, and emoticons from tweets, stemming, stop words removal, and classification using Multinomial Naive Bayes Classifier.
   If you want to try, you can use file combinetrain4.csv and combinetest.csv 

---VERSION 2--- 
***Using Fold Cross Validation - in this case you only need 1 dataset for both training & testing set***
Note : If you already have a .csv training & testing file, you can skip to step 3. If you want to fetch data first 
       from Twitter, you can follow from step 1. 
       
1. Run search.php, after that you will get a .json file from Twitter. 
2. Run cobaparsing.php, after that you will get a .csv file from a .json file that you get at step 1. 
   This is for changing your .json file to .csv file, removing the punctuations, mentions, and emoticons from tweets.
3. Run ver2.php, remember to put training.csv (your training set & testing set) in the same directory. 
   This is for removing the punctuations, mentions, and emoticons from tweets, stemming, stop words removal, and classification using Multinomial Naive Bayes Classifier.
   If you want to try, you can use combinetrain4.csv.  