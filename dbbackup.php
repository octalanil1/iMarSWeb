<?php
 
// =========================Yammya==============
// $host = 'localhost';
// $uname = 'yawmya';
// $pass = '3aDuB6DsDdNcj5Iu';
// $database = 'yawmya';
// =========================imars==============

$host = 'localhost';
$uname = 'root';
$pass = '7EXSDZGvvL3sFxjs';
$database = 'imars';

$conn = mysqli_connect($host, $uname, $pass);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
 echo "Connected successfully";


$selectdb=mysqli_select_db($conn,$database) or die("Database could not be selected"); 
$result=mysqli_select_db($conn,$database)
or die("database cannot be selected <br>");

/* Store All Table name in an Array */
$allTables = array();
$result = mysqli_query($conn,'SHOW TABLES');
while($row = mysqli_fetch_row($result)){
     $allTables[] = $row[0];
}
$return = "";
foreach($allTables as $table){
$result = mysqli_query($conn,'SELECT * FROM '.$table);
$num_fields = mysqli_num_fields($result);

$return.= 'DROP TABLE IF EXISTS '.$table.';';
$row2 = mysqli_fetch_row(mysqli_query($conn,'SHOW CREATE TABLE '.$table));
$return.= "\n\n".$row2[1].";\n\n";

for ($i = 0; $i < $num_fields; $i++) {
while($row = mysqli_fetch_row($result)){
   $return.= 'INSERT INTO '.$table.' VALUES(';
     for($j=0; $j<$num_fields; $j++){
       $row[$j] = addslashes($row[$j]);
       $row[$j] = str_replace("\n","\\n",$row[$j]);
       if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } 
       else { $return.= '""'; }
       if ($j<($num_fields-1)) { $return.= ','; }
     }
   $return.= ");\n";
}
}
$return.="\n\n";
}

// Create Backup Folder
$folder = 'Backup/';
if (!is_dir($folder))
mkdir($folder, 0777, true);
chmod($folder, 0777);

$date = date('m-d-Y-H-i-s', time()); 
$filename = $folder."imars-db-backup-".$date; 

$handle = fopen($filename.'.sql','w+');
fwrite($handle,$return);
fclose($handle);



?>
