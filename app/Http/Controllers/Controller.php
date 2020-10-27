<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use View;
use Auth;
use Twilio\Rest\Client;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	public $admindata;
	public function __construct() {
		$user = "";
		if (Auth::check())
	{
	$user = Auth::user();
	}
	View::share('userdata', $user);	
}
function exportdb(){

	$host = '3.21.49.221';
	$uname = 'root';
	$pass = '7EXSDZGvvL3sFxjs';
	$database = 'imars';
	
	 
	
	$conn = mysqli_connect($host, $uname, $pass);
	if ($conn) {
		die("Connection failed: " );
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
	$filename = $folder."db-backup-".$date; 
	
	$handle = fopen($filename.'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
	
}


function send_sms($message, $recipients)
{
	$account_sid = 'ACd8eca3eb6a6a6919ef196b3e0fd35a76';
	$auth_token = '7c515601c591ca76da1d3b7e82e51e1c';
	$twilio_number = '+19143446277';
	$client = new Client($account_sid, $auth_token);

	try
	{	

		$client->messages->create($recipients, 
			['from' => $twilio_number, 'body' => 'Your iMarS OTP is '.$message] );
			//echo 1; exit;
		//	return true;
		//dd($client);
			return $client;
	}
	catch (Exception $e)
	{ //
		
	echo "Error: " . $e->getMessage();
	}
					
}
}
