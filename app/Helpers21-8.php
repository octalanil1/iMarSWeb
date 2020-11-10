<?php 
namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
use DateTime;
use DateInterval;
use DatePeriod;
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
class Helpers {
public static function num_format($num=0) {
//setlocale(LC_MONETARY, 'en_IN');
//substr(money_format('%!i',2000000),0,-3);	
$host = request()->getHttpHost();
if($host=="192.168.0.165"){
return number_format($num);	
}else{
return substr(money_format('%!i',$num),0,-3);	
}
}
public static function genrateotp($length=6) {  //Genrate Otp
	 $characters = '3450216987';
     $charactersLength = strlen($characters);
     $randomString = '';

		for ($i = 0; $i < $length; $i++) {

			$randomString .= $characters[rand(0, $charactersLength - 1)];

		}
	return($randomString);
}
 public static function sendsms($mobile,$msg) {  //send sms
 $url = "http://sms.saginfotech.com/sendsms.jsp?user=saginfo&password=766efe0070XX&mobiles=+91".$mobile."&sms=".rawurlencode($msg)."&senderid=SAGRTA";
$response = file_get_contents($url);
return($response);
}   
	
public static function url_format($string) {  //remove all spacial character from string
   $output = preg_replace('!\-!', ' ', $string);
   $output = preg_replace('!\s+!', ' ', $output);
   $string = preg_replace('/\s+/', '-', $output);

   return strtolower(preg_replace('/[^A-Za-z0-9\-\$\&]/', '', $string));
}

public static function getTime($string) 
{  //get date diffrence in php
            $curdate = date("Y-m-d h:i:s");
         $start_date = new \DateTime($string);
         $since_start = $start_date->diff(new \DateTime($curdate));
         $day = $since_start->days;
         $hur = $since_start->h;
         $minutes = $since_start->i;
         $result = $day."d";
         if($day<1)
         {
         $result = $hur."h";
         if($hur<1)
         {
         $result = $minutes."m";
         if($minutes<1)
         {
         $result = "a moment ago";
         }
         }
         }
         return $result;
}	

public static function GetSurveyStatusBykey($key) { 
  $status=array('0'=>'Requested','1'=>'Confirmed','2'=>'Completed','3'=>'Ongoing','4'=>'Cancelled','5'=>'Expired');
return $status[$key];
 
}
public static function GetSurveyStatus() { 
   $status=array(''=>'Select Status','0'=>'Requested','1'=>'Confirmed','2'=>'Completed','3'=>'Ongoing','4'=>'Cancelled','5'=>'Expired');
 return $status;
  
 }
 public static function SurveyNo($id) { 
 return 10000+$id;
  
 }
 public static function UserTypeList() { 
  $user_type_data=array(''=>'Select User Type','0'=>'DP of an Operator Company','1'=>'Operator of an Operator Company','2'=>'DP of a Surveyor Company','3'=>'Surveyor of a Surveyor Company','4'=>'Individual Surveyor');
return $user_type_data;
   }
   public static function UserTypeName($key) { 
      $user_type_data=array('0'=>'DP of an Operator Company','1'=>'Operator of an Operator Company','2'=>'DP of a Surveyor Company','3'=>'Surveyor of a Surveyor Company','4'=>'Individual Surveyor');
    return $user_type_data[$key];
       }

}
?>