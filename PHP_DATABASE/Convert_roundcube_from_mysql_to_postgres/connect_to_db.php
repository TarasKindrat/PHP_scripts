<?php
//   php csvdump.php localhost root password database tablename > whatever-you-like.csv


$server = $argv[1];
$user = $argv[2];
$password = $argv[3];
$db = $argv[4];
$table = $argv[5];

mysql_connect($server, $user, $password) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());

// fetch the data
$rows = mysql_query('SELECT * FROM ' . $table);
$rows || die(mysql_error());


// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings

$fields = [];
for($i = 0; $i < mysql_num_fields($rows); $i++) {
    $field_info = mysql_fetch_field($rows, $i);
    $fields[] = $field_info->name;
}
fputcsv($output, $fields);

// loop over the rows, outputting them
while ($row = mysql_fetch_assoc($rows)) fputcsv($output, $row);

?>