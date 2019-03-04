// Deletes the addresses of invalid mailboxes from the database 
/*
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
//********************** Connect to DB**************************************************************************
$host = '0.0.0.0';
$port = '5432';
$dbname = 'dbname';
$user = 'user'; 
$password = 'password';
 // fields for update query
$tableName = 'users';  // table where is email field
$fieldName = 'username';  // field name where mailbox addresses are stored

$conn_string = "host = ".$host." port = ".$port." dbname = ".$dbname." user = ".$user." password = ".$password;


$dbconnPg = pg_connect($conn_string);
   if (!$dbconnPg) {
        echo "Error occured\n";
        exit;
    }else
    {

    $result = pg_query($dbconnPg, "select username from users where last_login <= '2015-01-01 00:00:00'");
    echo pg_last_error($dbconnPg);
    echo "\n";
       while ($row = pg_fetch_row($result)) {
        if ( !empty($row[0])){
            $allEmails[] = $row[0];  // fill the array with the names of the electronic mailboxes
            }

        }
        for ($i = 0; $i < count($allEmails); $i ++ ){
             $downP = strpos($allEmails[$i],'@');  // get first number in line where is '@'
             $name =  substr($allEmails[$i],$downP);  // get  domain part

            if (($name == '@mail.ru') || ($name == '@yandex.ru') || ($name == '@yandex.ua') ){ // we do not resolve this domains
              echo 'Email will be not delivered to this domain '.' '.$name.' ';
              $falseEmails[] = $allEmails[$i];
              try{
                 if (pg_query($dbconnPg, "update ".$tableName." set ".$fieldName." = ' ' where ".$fieldName." = '".$allEmails[$i]."'")){$erasingCount++;} // erase in DB
                }catch (Exception $e) {
                 echo "There are  some problem to make changes in DB whis email ".$allEmails[$i]." catching exeption is ".$e->getMessage(). "\n";
                }


            } else {
             $resultOfCheking = $checkMail->execute($allEmails[$i]); // call function to check email
                    if ($resultOfCheking == FALSE){
                       $falseEmails[] = $allEmails[$i];
                       try{
                           if (pg_query($dbconnPg, "update ".$tableName." set ".$fieldName." = ' ' where ".$fieldName." = '".$allEmails[$i]."'")){$erasingCount++;} // erase in DB
                       } catch (Exception $e) {
                       echo "There are  some problem to make changes in DB whis email ".$allEmails[$i]." catching exeption is ".$e->getMessage(). "\n";
                       }
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
     echo " must repeed agai \n";
    }
}