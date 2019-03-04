//An attempt to parse the web resource using simple_html_dom.php. and converting the received content into a XML file. Converting is not complete
<?php
set_time_limit(60000);
require_once 'Converter.php';
require_once 'simple_html_dom.php';
$url = 'https://www.someurl.php';
// date array after parsing
$date_array;
// array of category values ​​after parsing
$categoris_array;
// a jason array
$jsonArray;
// id date 
$date_id = '#startdate';
// id categories
$category_id = '#genre';

// Object class converter
$converter = new Converter();
// curl for parsing dates and categories of channels.
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_HEADER, 0);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Set the parameter so that curl returns the data instead of displaying it in the browser.
   curl_setopt($ch, CURLOPT_URL, $url);
   $data = curl_exec($ch);
   curl_close($ch);
 
// Create a DOM object and write to it the received response from CURL
 $html = new simple_html_dom();    
 $html->load($data);

// Fill the arrays with dates and channel identifiers
$date_array = $converter->findOption($html,$date_id);
$categoris_array = $converter->findOption($html,$category_id);

echo 'date array is ';
echo '</br>';
echo '<pre>';
   print_r($date_array);
   echo '</pre>';
echo 'categoris array is ';
echo '</br>';
echo '<pre>';
   print_r($categoris_array);
   echo '</pre>';   

   // make request 
for ($j = 0; $j < count($date_array); $j++){
    echo 'count date array is ';
    echo $j;
    echo '</br>';
    
    for ($i = 0; $i < count($categoris_array); $i++){
         $response = $converter->init_curl($url, $date_array[$j], $categoris_array[$i]);
        sleep (3);
        // Crop the webpage for the beginning and end of the json element
        $upper_part = strstr($response, 'var json1 = ');
        // define the lower position
        $upper_possition = strpos($upper_part, '"}]}]}}'); 
        // write json to the corresponding variable
        $json = substr($upper_part,12,$upper_possition-5);
        $jsonArray[] = json_decode($json,true);
   }
}

echo '<pre>';
 print_r($jsonArray);
  echo '<pre>';

$xml = new SimpleXMLElement('<tv/>');
$converter->arrayToXml($jsonArray, $xml);

