<?php
// crypt passwords with Dovecot CRAM-MD5. On server must be installed DOVECOT! 
$conn_string = "host = 0.0.0.0 port=5432 dbname=dbname user=user password=password";
$dbconn = pg_connect($conn_string);
// connect to DB whis textplain passwords and gets password's fields
$result = pg_query($dbconn, "SELECT username, password2 FROM public.mailbox");
    if (!$result) {
       echo "Error occured\n";
       exit;
    }else{
   // start cripting 
      while ($row = pg_fetch_row($result)) {
         echo "Username: $row[0]  Password: $row[1]";
         echo "<br />\n";
         $userName =  $cripd[] = $row[0];
         $criptPassword = $cripd[] = `dovecot pw -s CRAM-MD5 -p $row[1]`;
         // Write cripted passwords to DB
         pg_query($dbconn, "UPDATE public.mailbox SET password3 = '".$criptPassword."' WHERE username = '".$row[0]."'");
      }
     echo '</br>';    
     echo  '</pre>'; 
     print_r($cripd); 
     echo  '</pre>'; 
   }
pg_close($dbconn);
?>