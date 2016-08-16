<?php
    $url = "http://maps.google.com/maps/api/geocode/json?address=West+Bridgford&sensor=false&region=UK";
    $response = file_get_contents($url);
    $response = json_decode($response, true);
    
    //print_r($response);
    
    $lat = $response['results'][0]['geometry']['location']['lat'];
    $long = $response['results'][0]['geometry']['location']['lng'];
    
    echo "latitude: " . $lat . " longitude: " . $long;
?>