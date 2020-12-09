<?php
/*
 * @author Taras
 */
class Querys {
    // Метод приймає об'єкт PHP масиву бази, коннект до бази і  (string) назву таблиці яка буде опрацювуватись
      public function sendQuery($obgArray, $dbconn,$tableName){
      
            foreach ($obgArray as $key => $value) {
        //    echo "Key is $key  ";
        //    echo '</br>';
            
            $stringKey = '';
            $stringValue = '';
            foreach ($value as $key2 => $value2) {
        
                // Змінюємо назву хоста розміщення раундкуба, в старій базі є певна кількість записів із назвою хоста smtp.some_domen.if.ua які є не актуальними 
                if ($key2 == 'mail_host'  ){                                                                     
                   if ($value2 == 'smtp.some_domen.if.ua'){                                                             
                    $value2 = 'old.smtp';
                    //continue;                                                                                       
                    }else{                                                                                          
                    $value2 = gethostname();                                                                        
                    }                                                                                               
                }             
            
                if ($key2 == 'alias'){   //стовця alias немає в раундкубі                                           
                   continue;                                                                                      
                }

                // Екрануєм стовпчик  replay-to через символ "-" для таблиці identities                             
                if ($key2 == 'reply-to'){                                                                           
                    $key2 = (String)'"reply-to"';                                                                  
                 }
                  // Екранування ESCAPE символів під вимоги POSTGES
                   $value2 = pg_escape_string($value2); 

            // Конкатинапція для створення запиту у базу
             $stringKey.="$key2, ";
             $stringValue.= is_string($value2)?"'$value2', ":"$value2, ";

        }
        // Обрізка ком в кінці відповідних конкатинацій
        $stringKey = trim($stringKey, ", ");
        $stringValue = trim($stringValue,", "); 
        // Відсилання запиту в базу на виколнання
        pg_query($dbconn, "INSERT INTO ".$tableName." (".$stringKey.") VALUES (".$stringValue.")");    
        
        } 
    }
}
