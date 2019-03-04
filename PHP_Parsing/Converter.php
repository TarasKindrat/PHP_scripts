<?php

class Converter {
    // find the necessary tags
      public function  findOption( $html, $id){
      if(count($html->find($id))){
        foreach($html->find($id.' option') as $div){
              preg_match('/value="(.*?)"/' , $div->outertext , $regs);
               if (!empty($regs[1])){
               $array[] = $regs[1];
               }
           }
         } 
      
    return $array;
    
    }

public function init_curl ($url, $date_array_element, $categoris_array_element){
        $myCurl = curl_init();
  curl_setopt_array($myCurl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => http_build_query(array('startdate'=>$date_array_element )),
      CURLOPT_POSTFIELDS => http_build_query(array('genre'=> $categoris_array_element))
   ));
  $response = curl_exec($myCurl);
  curl_close($myCurl);
  return $response;
  
 }
 // convert to XML file
 public function arrayToXml($array, &$xml){
    foreach ($array as $key => $value) {
        if(is_array($value)){
           if(is_int($key)){
                $key = '';
            }
                        
            $label = $xml->addChild($key);
            $this->arrayToXml($value, $label);
        }
        else {
            // skip unnecessary tags
            if ($key === 'aid' || $key === 'popup' || $key === 'logo'){
                continue;
            }// Обрізаємо лишнє із опису
            if($key === 'divtitle'){
              $value = strstr($value, 'Synopsis :');
              $value = substr($value,11);
              $key = 'desc';
              $key->writeAttribute('lang', 'en');
            }
            
            if ($key === 'programe_name'){
                $key = 'title';
               // $key->setAttribute('lang','en');
            }
            
            $xml->addChild($key, htmlspecialchars($value));
        }
    }

    //saving generated xml file; 
$result = $xml->asXML('name2.xml');
    }
 
/*
    public function arrayToXml($array, &$xml){
    foreach ($array as $key => $value) {
        if(is_array($value)){
            if(is_int($key)){
                $key = "e";
            }
            $label = $xml->addChild($key);
            $this->arrayToXml($value, $label);
        }
        else {
            $xml->addChild($key, $value);
        }
    }

    //saving generated xml file; 
$result = $xml->asXML('name.xml');
        }
 * 
 */
}



