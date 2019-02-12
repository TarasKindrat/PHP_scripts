<?php
// Copy folders from FReeBSD to Linux

$linuxPassRoot = 'password';
$freeBSDAdminP = 'password2';
$freeBSDRootP = 'password3';
$namesArr = [];

$scpCopyFrom = 'scp -c blowfish -rp /var/mail/exim/';
$scpCopyTo = ' root@0.0.0.111:/var/vmail/foldermail/';   //-c blowfish - this encoding is used to increase the speed of copying, -p is used to preserve the rights and attributes of the file

// initializes connection to DB
$conn_string = "host=localhost port=5432 dbname=testbase user=admin password=thispassword";
$dbconnPg = pg_connect($conn_string);

if (!$dbconnPg) {
   echo "Error occured\n";
   exit;
}else
    {
     $result = pg_query($dbconnPg, "select username from users where last_login >= '2018-01-01 00:00:00'");
     while ($row = pg_fetch_row($result)) {
         echo "Username: $row[0]" ;
                  echo "\n";

         $downP = strpos($row[0],'@domain.com');  // cut domain part
         $name =  substr($row[0],0,$downP);
         echo 'name '.$name;
         echo "\n";

         $stream = expect_popen("ssh admin@192.168.0.100");  //  connect to FreeBSD
         echo"\n input password FBSD\n";
         sleep (3);
         fwrite ($stream, "$freeBSDAdminP\n");  // input Admin password
         sleep (3);
         fwrite ($stream,"su\n");
         echo " input su ok\n";
         sleep(3);
         echo " input root password\n";
         fwrite ($stream,"$freeBSDRootP\n");   // login as root
         sleep (3);
         echo " start copy\n";
         fwrite ($stream,"$scpCopyFrom".$name."$scpCopyTo".$name."\n");
         sleep (2);
         echo " unswer yes\n";
         fwrite ($stream,"yes\n");
         sleep (3);
         echo " input linux password\n";
         fwrite ($stream,"$linuxPassRoot\n");  //login to Linux server
         sleep(10);
        fclose ($stream);
        // chmod -r /var/vmail/domain.com ;  
        }
    }


