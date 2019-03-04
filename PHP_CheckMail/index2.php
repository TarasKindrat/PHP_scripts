/* Check the addresses of invalid mailboxes from the file
*  example sorce file : 
*  id      email
*  344     someName@someMail.com
*
*  This script was writed by Taras  Kindrat - kindrat5@gmail.com
*
*  This script is distributed under the GPL License
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*   GNU General Public License for more details.
*
*  http://www.gnu.org/licenses/gpl.txt
*
*/
<?php
set_time_limit(60000);
require_once 'Validation.php';
$erasingCount = 0; // counter all operations to erase false emails
$checkMail = new CCheckMail();
$correctEmails = array(); // will be written only correct emails
$falseEmails = array();  // will be written only false emails
$allEmails = array(); // will be written all emails from database

$file = 'public_abonent_tbl.txt';
$result = file($file);
$file2 = 'false.txt';

foreach ($result as $row) {
 echo '<pre>';
//print_r($line = explode("/w",$row));
 print_r($line = preg_split("/[\s]+/ ",$row)); 
        if (!empty($line[1])){
            $allEmails[] = $row;  // fill the array with the names of the electronic mailboxes
            }
     echo '</pre>';
}

    for ($i = 0; $i < count($allEmails); $i ++ ){
        $line2 = preg_split("/[\s]+/ ",$allEmails[$i]);
          
        $downP = strpos($line2[1],'@');  // get first number in line where is '@'
        $name =  substr($line2[1],$downP);  // get  domain part
             
        if (($name == '@mail.ru') || ($name == '@yandex.ru') ){ // we do not resolve this domains
           echo '</br> Email will be not delivered to this domain '.' '.$name."</br> \r\n";
           $falseEmails[] = $allEmails[$i];
           file_put_contents($file2,$line2[0].' '.$line[1]."\r\n",FILE_APPEND);
           $erasingCount++; // erase in DB
           echo $line2[0].' '.$line2[1]."</br>";
        } 
         else  {
            $resultOfCheking = $checkMail->execute($line2[1]); // call function to check email
            if ($resultOfCheking == FALSE){
            $falseEmails[] = $allEmails[$i];
            file_put_contents($file2,$line2[0].' '.$line2[1]."\r\n",FILE_APPEND);
            $erasingCount++; // erase in DB
            }
            if ($resultOfCheking == TRUE){
               $correctEmails[] = $allEmails[$i];
            }
        }
   }
    echo 'total emails is :'.count($allEmails)."\n";
    //print_r($allEmails);
    echo "\n";

     echo 'correct emails is :'.count($correctEmails)."\n";
     // print_r($correctEmails);
     echo "\n";
// ******************************* Check all operations********************************************* 
    echo "Number oparations to erase False Emails is: " .$erasingCount."\n";
    //print_r($falseEmails);
    echo "\n";

    if ((count($correctEmails)+count($falseEmails)==count($allEmails)) && ($erasingCount == count($falseEmails))){
       echo "All job is dun!";
    }else{ 
    echo "Number emails that woth erasing is  ".$erasingCount." but must be ".count($falseEmails)."\n";
     print_r(array_diff ($allEmails,$correctEmails,$falseEmails )); // if some part will be not delete, print what left or if all emails is wrong - print nothing
     echo " need to repeat again \n";
    }
 