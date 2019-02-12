<?php
ini_set("expect.timeout", -1); // unlimit time to use expect

$lastEnteredDate = "'2018-10-12 00:00:00'";
$linuxPassRoot = 'password';
$freeBSDAdminP = 'password2';
$freeBSDRootP = 'password3';
$namesArr; // names if users
$i = 0; //use for loop to copi array of users
$scpCopyFrom = 'scp -c blowfish -rp /var/mail/exim/'; //-c blowfish encoding is selected for higher copy speeds, -p key is used to preserve the rights and attributes of the date
$scpCopyTo = ' root@0.0.0.0:/var/vmail/folder/';   // copy to Linux server

//********************** Connect to DB**************************************************************************
$conn_string = "host=localhost port=5432 dbname=testbase user=admin password=12345";
$dbconnPg = pg_connect($conn_string);

if (!$dbconnPg) {
       echo "Error occured\n";
       exit;
    }else
        {  
         $result = pg_query($dbconnPg, "select username from users where last_login >= ".$lastEnteredDate);
        //$result = pg_query($dbconnPg, "select username from users where last_login >= '2018-01-01 00:00:00'");
        while ($row = pg_fetch_row($result)) {  // 
              echo "Username: $row[0]" ;
              echo "\n";
              $downP = strpos($row[0],'@domain.com'); // cut domain part
              $name =  substr($row[0],0,$downP);  // get names user's fo create the same folders
              if ($name == ''){   // skip empty fields which arise after parsing other domains
               continue;
              }else{
                   $namesArr[] = $name;   // fill array whis names 
                   }
            }
        // #############displey names, can be deleted or commented#############
        echo '<pre>';
        print_r($namesArr);
        echo '</pre>';
        echo 'kilkist imen '.count($namesArr);
        echo "\n";
        //#####################################################################    
        //*********************************************************************************************************
       $stream = expect_popen("ssh admin@0.0.0.1");   // connect to smtp server FreeBSD

       $cases = array(
            // array(a return pattern in case of a match)
            array("Password:",'PASSWORD'),
            array("$ ",'ADMINSHELL'));

        while (true) {
             switch (expect_expectl($stream, $cases)) {
               case 'PASSWORD':
                     fwrite($stream, "$freeBSDAdminP\n"); // admin for FreeBSD
                     break;
               case 'ADMINSHELL':
                     fwrite ($stream, "su\n");
                       $cases2 = array(array ("Password:",'ROOTPASSWORD'),array ("smtp# ", 'ROOT'),array("$ ",'ADMINAGEIN')); // get ROOT for FreeBSD9
                      while (true) {
                           switch (expect_expectl ($stream,$cases2)){
                            case 'ROOTPASSWORD':
                                  fwrite($stream,"$freeBSDRootP\n");
                                  break;
                            case 'ROOT':  // start copy files and input Linux passowd 
                                                           
                              if ($i == 0){
                                 fwrite ($stream,"$scpCopyFrom".$namesArr[$i]."$scpCopyTo".$namesArr[$i]."\n");
                                 $i++;
                                }
                                while (true){  // copy folders in a loop while one of cases is true
                                  switch (expect_expectl ($stream, array (array ("root@0.0.0.111's password: ",'LINUXPASS'),array("smtp# ",'FINISHCOPY'),array("$ ",'ADMINAGEIN')))){   // connection to Linux server                 
                                      case 'LINUXPASS':
                                           fwrite($stream,"$linuxPassRoot\n");
                                        break; 
                                      case 'FINISHCOPY':
                                           if ($i < count($namesArr)){
                                           fwrite ($stream,"$scpCopyFrom".$namesArr[$i]."$scpCopyTo"."\n");
                                           $i++;
                                           }else {
                                                fwrite ($stream, "exit\n");
                                                }
                                           break;
                                     
                                      case 'ADMINAGEIN':
                                           fwrite ($stream, "exit\n");
                                           break 2 ;  // break switch and loop  while
                                  
                                      default:
                                           die ("Error has occurred in loop!\n");
                                   }
                                }
                            break 2;  //break switch end loop while
                        
                            default:
                                die ("Error has occurred in cases ROOTPASSWORD, ROOT !\n");
                           }
                   }
     break 2; // break switch and loop while
     default:
        die ("Error has occurred in firs case!\n");
     
    }
                    
  }
fclose ($stream);        //closed sream
 `chown -R vmail:vmail /var/vmail/domain.com `; //  change recursively owner and owner of  group for folders  
}

echo "JOB DUN!\n";


