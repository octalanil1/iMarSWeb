<?php 
namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
use DateTime;
use DateInterval;
use DatePeriod;
use App\Models\Port;
use App\Models\UsersSurveyPrice;
use App\Models\Countries;
use App\Models\Surveytype;
use App\Models\Events;
use App\Models\Survey;
use Auth;
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
public  function generateRandomString($length = 20) {
   $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $charactersLength = strlen($characters);
   $randomString = '';
   for ($i = 0; $i < $length; $i++) {
       $randomString .= $characters[rand(0, $charactersLength - 1)];
   }
   return $randomString;
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
  $status=array('0'=>'Pending','1'=>'Upcoming','2'=>'Cancelled','3'=>'Report Submitted','4'=>'Pending Payment','5'=>'Payment Received','6'=>'Paid');
return $status[$key];
 
}
public static function GetSurveyStatus() { 
   $status=array(''=>'Select Status','0'=>'Pending','1'=>'Upcoming','2'=>'Cancelled','3'=>'Report Submitted','4'=>'Pending Payment','5'=>'Payment Received','6'=>'Paid');
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

       public static function UserList()
      { 
         $userdata = User::where('type','0')->where('is_admin','0')->orderBy("created_at","DESC")->get();
         $user_box=array(''=>'Select User');
         foreach($userdata as $key=>$value){
            $user_box[$value->id]=sprintf('%s %s',$value->first_name,$value->last_name);
         }
          return $user_box;
      }
      public static function SurveyorList()
      { 
         $userdata = User::where('is_admin','0')->where('status','1')
         ->where(function ($query) {
            $query->where('type', '=','3' )
                  ->orWhere('type', '=', '4')
                  ->orWhere('type', '=', '2');
                 
         })
         ->orderBy("created_at","DESC")->get();
         $user_box=array(''=>'Select Surveyor');
         foreach($userdata as $key=>$value){
            $user_box[$value->id]=sprintf('%s %s',$value->first_name,$value->last_name);
         }
          return $user_box;
      }
      public static function OperatorList()
      { 
         $userdata = User::where('is_admin','0')->where('status','1')
         ->where(function ($query) {
            $query->where('type', '=','1' );
                 
         })
         ->orderBy("created_at","DESC")->get();
         $user_box=array(''=>'Select Operator');
         foreach($userdata as $key=>$value){
            $user_box[$value->id]=sprintf('%s %s',$value->first_name,$value->last_name);
         }
          return $user_box;
      }
      public static function PortList()
      { 
         $portdata = Port::orderBy("port","DESC")->get();
            $port_box=array(''=>'Select Port');
            foreach($portdata as $key=>$value){
               $port_box[$value->id]=$value->port;
            }

          return $port_box;
      }
      public static function SurveyTypeList()
      { 
         $categorydata = Surveytype::where('status','1')->orderBy("name","ASC")->get();
            $category_box=array(''=>'Select Category');
            foreach($categorydata as $key=>$value){
               $category_box[$value->id]=$value->name;
            }

          return $category_box;
      }

      public static function CountryList()
      { 
         $countrydata = Countries::orderBy("name","ASC")->get();
         $country_box=array(''=>'Select Country');
         foreach($countrydata as $key=>$value){
            $country_box[$value->id]=$value->name;
         }
          return $country_box;
      }
      public static function SurveyorPrice($survey_type_id,$surveyor_id)
      { 
         $userd=User::select('type','created_by')->where('id',$surveyor_id)->first();
       if(!empty($userd))
       {
         if( $userd->type=='2' || $userd->type=='4' ){
            $surveyor_data = DB::table('users_survey_price')->where('user_id',$surveyor_id)->where('survey_type_id',$survey_type_id)->first();

         }
         if( $userd->type=='3'){
            $surveyor_data = DB::table('users_survey_price')->where('user_id',$userd->created_by)->where('survey_type_id',$survey_type_id)->first();

         }

               if(!empty($surveyor_data))
               {
                  if($surveyor_data->no_of_days!=""){
                     return $surveyor_data->survey_price*$surveyor_data->no_of_days;
                  }else{
                     return $surveyor_data->survey_price;

                  }
                     

            }else{
               return "";

            }

       }  
         
      }
      public static function SurveyorPriceDetail($survey_type_id,$surveyor_id)
      { 
         $userd=User::select('type','created_by')->where('id',$surveyor_id)->first();
       if(!empty($userd))
       {
         if( $userd->type=='2' || $userd->type=='4' ){
            $surveyor_data = DB::table('users_survey_price')->where('user_id',$surveyor_id)->where('survey_type_id',$survey_type_id)->first();

         }
         if( $userd->type=='3'){
            $surveyor_data = DB::table('users_survey_price')->where('user_id',$userd->created_by)->where('survey_type_id',$survey_type_id)->first();

         }

               if(!empty($surveyor_data))
               {
                 
                     return $surveyor_data->survey_price;


            }else{
               return "";

            }

       }  
         
      }
   
      public static function SurveyorPortPrice($port_id,$surveyor_id)
      { 
         $userd=User::select('type','created_by')->where('id',$surveyor_id)->first();
         if(!empty($userd))
       {
               if( $userd->type=='2' || $userd->type=='4' ){
                  $surveyor_data = DB::table('users_port')->where('user_id',$surveyor_id)->where('port_id',$port_id)->first();

               }
               if( $userd->type=='3'){
                  $surveyor_data = DB::table('users_port')->where('user_id',$userd->created_by)->where('port_id',$port_id)->first();

               }
            
               if(!empty($surveyor_data)){
               return $surveyor_data->cost;

            }else{
               return "";

            }
         }
      }

      public static function SendNotification($device_id,$title,$body)
      { 
         $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
							//$tokenList = $userstoken;
							$token = $device_id;
							//echo '<pre>'; print_r($tokenList); die;
							$notification = [
								'title' => $title,
								'body' =>  $body,
								//'icon' => $imageUrl,
								'sound' => 'mySound',
							];
						//dd($notification);
							//$extraNotificationData = ["message" => $notification,"moredata" =>'dd'];
							$fcmNotification = [
								//'registration_ids' => $tokenList, //multple token array
								'to'        => $token, //single token
								'notification' => $notification,
								//'data' => $extraNotificationData
							];
				
							$headers = ['Authorization:key=AAAAyVCwPdo:APA91bFYeZCzN1qjjA2pVenLoPLXKm92hQc1ExhkPxDXh-w1O_a8pO0xwSh3RiibB75Zw5Z9SxmRFmckwifj_IyK30geYtd95VAzXsTHEbNnWwmpm6gUeX_EY7I_PiYJedh0YtGkyTtn',
								'Content-Type: application/json'
							];

							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL,$fcmUrl);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
							$result = curl_exec($ch);
							curl_close($ch);
                        $result = json_decode($result, true);
                        //dd($result);
      }
      
     
      /* 
       * Load function based on the Ajax request 
       */ 
      // if(isset($_POST['func']) && !empty($_POST['func'])){ 
      //     switch($_POST['func']){ 
      //         case 'getCalender': 
      //             getCalender($_POST['year'],$_POST['month']); 
      //             break; 
      //         case 'getEvents': 
      //             getEvents($_POST['date']); 
      //             break; 
      //         default: 
      //             break; 
      //     } 
      // } 
       
      /* 
       * Generate event calendar in HTML format 
       */ 
      public  function getAllMonths($selected = ''){ 
         $options = ''; 
         for($i=1;$i<=12;$i++) 
         { 
             $value = ($i < 10)?'0'.$i:$i; 
             $selectedOpt = ($value == $selected)?'selected':''; 
             $options .= '<option value="'.$value.'" '.$selectedOpt.' >'.date("F", mktime(0, 0, 0, $i+1, 0, 0)).'</option>'; 
         } 
         return $options; 
     } 
     public  function getCalender($year = '', $month = '', $surveyor_id = ''){ 
      
          $dateYear = ($year != '')?$year:date("Y"); 
          $dateMonth = ($month != '')?$month:date("m"); 
          $date = $dateYear.'-'.$dateMonth.'-01'; 
          $currentMonthFirstDay = date("N",strtotime($date)); 
          $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear); 
          $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 7)?($totalDaysOfMonth):($totalDaysOfMonth + $currentMonthFirstDay); 
          $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42; 
      ?> 
      <?php 
                     $user = Auth::user();
                     $user_id=$user->id;
                     $ids=array();
                     if($surveyor_id!="")
                              {
                                 if($surveyor_id=="all")
                                    {
                                       
                                       $createdbysurveyor =  User::select('id')->where('created_by',$user_id)
                                       ->where('email_verify','1')->where('is_signup','1')
                                       ->get(); 
                                       
                                       foreach($createdbysurveyor as $data){
                                          $ids[]=$data->id;
                                       }

                                       array_push($ids,$user_id);   

                                    }else{

                                       
                                       array_push($ids,$surveyor_id);   
                                    }

                              }else{
                                 array_push($ids,$user_id);
                              }
                     $currentMonth = date('m');
                   $eventNum=Events::select('start_event')->whereIn('user_id',$ids)->where('title','0')
                  ->whereRaw('MONTH(start_event) = ?',[$currentMonth])
                  ->count();
                  $avail='100';
                  if(!empty($eventNum)){
                  
                     $var=$eventNum/ $totalDaysOfMonth;
                     $avail=$var*100;

                  }
               $final=100-$avail;
      ?>
   <div class="row" style="padding: 12px;">
	<div class="col-md-12 col-lg-12 col-xl-12">
      <?php if($surveyor_id=="all" && $user->type=='2'){?>
            <p>Aggregate Availability :<?php  echo number_format($final,2).'%'; ?></p>
      <?php } ?>
	</div></div>
          <div class="calendar-wrap"> 
              <div class="cal-nav"> 
                  <a href="javascript:void(0);" onclick="getCalendar('calendar_div','<?php echo date("Y",strtotime($date.' - 1 Month')); ?>','<?php echo date("m",strtotime($date.' - 1 Month')); ?>');">&laquo;</a> 
                  <select class="month_dropdown"><?php echo $this->getAllMonths($dateMonth); ?></select> 
                  <select class="year_dropdown"><?php echo $this->getYearList($dateYear); ?></select> 
                  <a href="javascript:void(0);" onclick="getCalendar('calendar_div','<?php echo date("Y",strtotime($date.' + 1 Month')); ?>','<?php echo date("m",strtotime($date.' + 1 Month')); ?>');">&raquo;</a> 
              </div> 
              <div id="event_list" class="none"></div> 
              <div class="calendar-days"> 
                  <ul> 
                      <li>SUN</li> 
                      <li>MON</li> 
                      <li>TUE</li> 
                      <li>WED</li> 
                      <li>THU</li> 
                      <li>FRI</li> 
                      <li>SAT</li> 
                  </ul> 
              </div> 
              <div class="calendar-dates"> 
                  <ul> 
                  <?php  
                      $dayCount = 1; 
                      $eventNum = 0; 
                    
                      for($cb=1;$cb<=$boxDisplay;$cb++){ 
                          if(($cb >= $currentMonthFirstDay+1 || $currentMonthFirstDay == 7) && $cb <= ($totalDaysOfMonthDisplay)){ 
                              // Current date 
                              $currentDate = $dateYear.'-'.$dateMonth.'-'.$dayCount; 
                              $cal_date= date('Y-m-d',strtotime($currentDate));
                               
                              DB::enableQueryLog(); // Enable query log
                              $eventNum=Events::select('start_event')->whereIn('user_id',$ids)->where('title','0')->where('start_event',$currentDate)->count();
                             // dd(DB::getQueryLog()); // Show results of log
                                  $survey_data_num =  Survey::select('survey.id as id','survey.survey_number as title','survey.start_date as start_event',
                              'survey.end_date as end_event')	
                              ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                              ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id')
                              ->where('survey.status', '=', '1')
                              ->where(function ($query) use ($ids,$surveyor_id) {
                                 
                                       if($surveyor_id!="")
                                       {
                                          if($surveyor_id=="all")
                                          {$query->WhereIn('custom_survey_users.surveyors_id',$ids)
                                             ->orwhereIn('survey_users.surveyors_id',$ids );

                                          }else
                                          {
                                             //echo 1; exit;
                                             $u=	User::select('type')->where('id',$ids['0'])->first();
                                             if($u->type =="3"){
                                                $query->where('survey.assign_to',$ids['0'] );
                                             }else
                                             {
                                                $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                                                ->orwhereIn('survey_users.surveyors_id',$ids );
                                                // $query->Where('survey.assign_to',0);
                                             }
                                          }

                                          
                                       }else
                                       {
                                                  
                                             
                                           $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                                             ->orwhereIn('survey_users.surveyors_id',$ids )                                         
                                               ->orwhereIn('survey.assign_to',$ids );
                                             
                                       }
                                  ;})
                                  ->where(function ($query) use ($ids,$surveyor_id) {
                                    if($surveyor_id!="")
                                    {
                                       if($surveyor_id!="all")
                                       {
                                           $u=	User::select('type')->where('id',$ids['0'])->first();
                                          if($u->type =="2"){
                                             $query->where('survey.assign_to','0' );
                                          }
                                       }

                                       
                                    }else
                                    {
                                       $u=	User::select('type')->where('id',$ids['0'])->first();
                                       if($u->type =="2"){
                                          $query->where('survey.assign_to','0' );
                                       }
                                    }
                                   

                               ;})
                                 
                              ->where(function ($query)  {
                                    $query->Where('survey_users.status','upcoming')
                                    ->orWhere('custom_survey_users.status','approved' );})
                              ->where('survey.start_date',$cal_date)
                              ->groupBy('survey.surveyors_id')
                              ->count();

                           

                              //$eventNum = $result->num_rows; 
                              $user = User::select('is_avail')->where('id','=',$user_id)->first();

                              // Define date cell color 
                              $users = Auth::user();
                              if($surveyor_id!="")
                              {
                                 if($surveyor_id==$users->id)
                                 { 
                                       $onof='1';
                                 }else{
                                    
                                    $onof='0';
                                 }
                              }else{
                                
                                 $onof='1';
                              }
                              $survey_data =  Survey::select('survey.id as id','survey.survey_number as title','survey.start_date as start_event',
                                 'survey.end_date as end_event')	
                                 ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                                 ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id')
                                 ->where('survey.status', '=', '1')
                                 
                                 ->where(function ($query) use ($ids,$surveyor_id) {
                                 
                                    if($surveyor_id!="")
                                    {
                                       if($surveyor_id=="all")
                                       {$query->WhereIn('custom_survey_users.surveyors_id',$ids)
                                          ->orwhereIn('survey_users.surveyors_id',$ids );

                                       }else
                                       {
                                          //echo 1; exit;
                                          $u=	User::select('type')->where('id',$ids['0'])->first();
                                          if($u->type =="3"){
                                             $query->where('survey.assign_to',$ids['0'] );
                                          }else
                                          {
                                             $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                                             ->orwhereIn('survey_users.surveyors_id',$ids );
                                             // $query->Where('survey.assign_to',0);
                                          }
                                       }

                                       
                                    }else
                                    {

                                        $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                                          ->orwhereIn('survey_users.surveyors_id',$ids )                                         
                                           ->orwhereIn('survey.assign_to',$ids );
                                          
                                    }
                               ;})
                               ->where(function ($query) use ($ids,$surveyor_id) {
                                 if($surveyor_id!="")
                                 {
                                    if($surveyor_id!="all")
                                    {
                                        $u=	User::select('type')->where('id',$ids['0'])->first();
                                       if($u->type =="2"){
                                          $query->where('survey.assign_to','0' );
                                       }
                                    }

                                    
                                 }else
                                 {
                                    $u=	User::select('type')->where('id',$ids['0'])->first();
                                    if($u->type =="2"){
                                       $query->where('survey.assign_to','0' );
                                    }
                                 }
                                

                            ;})
                                  ->where(function ($query)  {
                                       $query->Where('survey_users.status','upcoming')
                                       ->orWhere('custom_survey_users.status','approved' );})
                                 ->where('survey.start_date',$cal_date)
                                 ->groupBy('survey_users.survey_id')
                                 ->get();
                              //echo $eventNum.'-'.count($ids);
                          if($eventNum==count($ids)){ ?>
                                  <li date="<?php echo $currentDate ; ?>" class="grey <?php if($survey_data_num > 0){ echo 'surveyc'; } ?> date_cell" <?php if($onof=='1' && $survey_data_num==0){ ?> onclick="Onoff('<?php echo $currentDate ; ?>','1');" <?php } ?> <?php if($user->is_avail=='0'){?> style="background-color:#DDDDDD !important" <?php } ?>>
                                  <?php if($survey_data_num > 0){?>
                                  <ul class="dropdown"> 
                                       <?php foreach( $survey_data as $s){ ?>
                                       <li><a href="javascript:void(0)" onclick=view_record(<?php echo $s->id;?>)> <?php echo $s->title;?> </a></li>
                                       <?php } ?>
                                    </ul>
                                    <?php } ?>

                                 
                                  <?php  }else{ ?>
                                  <li date="<?php echo $currentDate ; ?>" class="light_sky <?php if($survey_data_num > 0){ echo 'surveyc'; } ?> date_cell" <?php if($onof=='1' && $survey_data_num==0){ ?> onclick="Onoff('<?php echo $currentDate ; ?>','0');"<?php } ?> <?php if($user->is_avail=='0'){?> style="background-color:#DDDDDD !important" <?php } ?>>
                                  <?php if($survey_data_num > 0){?>
                                  <ul class="dropdown"> 
                                       <?php foreach( $survey_data as $s){ ?>
                                       <li><a href="javascript:void(0)" onclick=view_record(<?php echo $s->id;?>)> <?php echo $s->title;?> </a></li>
                                       <?php } ?>
                                    </ul>
                                    <?php } ?>
                              <?php } 
                               
                              // Date cell 
                              echo '<span>'; 
                              echo $dayCount; 
                              echo '</span>'; 
                               
                              // Hover event popup 
                              // echo '<div id="date_popup_'.$currentDate.'" class="date_popup_wrap none">'; 
                              // echo '<div class="date_window">'; 
                              // echo '<div class="popup_event">Events ('.$eventNum.')</div>'; 
                              // echo ($eventNum > 0)?'<a href="javascript:;" onclick="getEvents(\''.$currentDate.'\');">view events</a>':''; 
                              // echo '</div></div>'; 
                               
                              echo '</li>'; 
                              $dayCount++; 
                  ?> 
                  <?php }else{ ?> 
                      <li><span>&nbsp;</span></li> 
                  <?php } } ?> 
                  </ul> 
              </div> 
          </div> 
       
         
      <?php 
      } 
       
      /* 
       * Generate months options list for select box 
       */ 
      
       
      /* 
       * Generate years options list for select box 
       */ 
      public static function getYearList($selected = ''){ 
          $options = ''; 
          for($i=2019;$i<=2025;$i++) 
          { 
              $selectedOpt = ($i == $selected)?'selected':''; 
              $options .= '<option value="'.$i.'" '.$selectedOpt.' >'.$i.'</option>'; 
          } 
          return $options; 
      } 
       
      /* 
       * Generate events list in HTML format 
       */ 
      public static function getEvents($date = ''){ 
          // Include the database config file 
           
          $eventListHTML = ''; 
          $date = $date?$date:date("Y-m-d"); 
           
          // Fetch events based on the specific date 
         //  /$result = $db->query("SELECT title FROM events WHERE date = '".$date."' AND status = 1");
         $user = Auth::user();
          $result=Events::select('title')->where('user_id',$user->id)->where('title','0')->where('start_event',$date)->get();
 
          if($result->count() > 0){ 
              $eventListHTML = '<h2>Events on '.date("l, d M Y",strtotime($date)).'</h2>'; 
              $eventListHTML .= '<ul>'; 
              while($row = $result->fetch_assoc()){  
                  $eventListHTML .= '<li>'.$row['title'].'</li>'; 
              } 
              $eventListHTML .= '</ul>'; 
          } 
          echo $eventListHTML; 
      }
      public static function phparraysort($Array, $SortBy=array(), $Sort) {
         if (is_array($Array) && count($Array) > 0 && !empty($SortBy)) {
                 $Map = array();
                 foreach ($Array as $Key => $Val) {
                     $Sort_key = '';
                     foreach ($SortBy as $Key_key) {
                     $Sort_key .= $Val[$Key_key];
                     }                
                     $Map[$Key] = $Sort_key;
                 }

                 sort($Map, $Sort);
                 $Sorted = array();
                 foreach ($Map as $Key => $Val) {
                     $Sorted[] = $Array[$Key];
                 }
                 return $Sorted;
         }
         return $Array;
     }
     public static function NoOfSurvey($user_id) 
     { 
      $user_d =  User::select('type')->where('id',$user_id)->first(); 
      if($user_d->type =="0" || $user_d->type =="1")
      {

                   $pending_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
                  DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
                  DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
                  DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
                  'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                  'custom_survey_users.surveyors_id as surveyor_id','users.id as operator_id',
                  'vessels.name as vesselsname')
                  ->leftJoin('port', 'port.id', '=', 'survey.port_id')
                  ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
                  ->leftJoin('users', 'users.id', '=', 'survey.user_id')
                  ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
                  ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                  ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
                  ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
                  ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
                     
                

                     if($user_d->type =="0")
                     {
                        $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                        $ids=array();
                        foreach($createdbysurveyor as $data){
                           $ids[]=$data->id;
                        }
                        array_push($ids,$user_id);
                        $pending_survey_data=$pending_survey_data->where(function ($query) use ($ids) {
                           $query->WhereIn('survey.user_id',$ids)
                           ->orWhereIn('survey.assign_to_op',$ids);});	
                                    
                     }
                     if($user_d->type =="1")
                     {
                        
                        $createdbysurveyor =  User::select('created_by')->where('id',$user_id)->first();
                        $ids=array();
                        if(!empty($createdbysurveyor)){
                           
                              $ids[]=$createdbysurveyor->created_by;
                        }
                           array_push($ids,$user_id);
                           $pending_survey_data=$pending_survey_data->where(function ($query) use ($ids) {
                              $query->WhereIn('survey.user_id',$ids)
                              ->orWhereIn('survey.assign_to_op',$ids);});	
                     }

                        
                  
                  $pending_survey_data=$pending_survey_data->where('survey.status', '=','0')->where('survey.declined', '=','0');
                   
                  
                     $pending_survey_data=$pending_survey_data->groupBy('survey.id');
                     $pending_survey_data=$pending_survey_data->orderBy('survey.start_date','ASC');
                     $pending_survey_data=$pending_survey_data->get();
         
                  $upcoming_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
                  DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
                  DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
                  DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
                  'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                  'custom_survey_users.surveyors_id as surveyor_id','users.id as operator_id',
                  'vessels.name as vesselsname')
                  ->leftJoin('port', 'port.id', '=', 'survey.port_id')
                  ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
                  ->leftJoin('users', 'users.id', '=', 'survey.user_id')
                  ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
                  ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                  ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
                  ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
                  ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
                     
                 
                     if($user_d->type =="0")
                     {
                        $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                        $ids=array();
                        foreach($createdbysurveyor as $data){
                           $ids[]=$data->id;
                        }
                        array_push($ids,$user_id);
                        $upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($ids) {
                           $query->WhereIn('survey.user_id',$ids)
                           ->orWhereIn('survey.assign_to_op',$ids);});	

                        //$upcoming_survey_data=$upcoming_survey_data->WhereIn('survey.user_id',$ids);
                                    
                     }
                     if($user_d->type =="1")
                     {
                        
                        $createdbysurveyor =  User::select('created_by')->where('id',$user_id)->first();
                        $ids=array();
                        if(!empty($createdbysurveyor)){
                           
                              $ids[]=$createdbysurveyor->created_by;
                        }
                           array_push($ids,$user_id);
                           $upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($ids) {
                              $query->WhereIn('survey.user_id',$ids)
                              ->orWhereIn('survey.assign_to_op',$ids);});	
                     }						

                  
                  $upcoming_survey_data=$upcoming_survey_data->where('survey.status', '=','1')->where('survey.declined', '=','0');	
                     
                  
                     $upcoming_survey_data=$upcoming_survey_data->groupBy('survey.id');

                     // $chat_unread_count=Chat::select('chat.id')->where('is_read','0')->where('receiver_id',$user->id)->count();
                     // if($chat_unread_count>0){
                     // 	$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
                     // }
                     
                     $upcoming_survey_data=$upcoming_survey_data->orderBy('survey.start_date','ASC');
                     $upcoming_survey_data=$upcoming_survey_data->get();

                     $report_submit_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
                  DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
                  DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
                  DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
                  'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                  'custom_survey_users.surveyors_id as surveyor_id','users.id as operator_id',
                  'vessels.name as vesselsname')
                  ->leftJoin('port', 'port.id', '=', 'survey.port_id')
                  ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
                  ->leftJoin('users', 'users.id', '=', 'survey.user_id')
                  ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
                  ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                  ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
                  ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
                  ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
                     
                 
                     if($user_d->type =="0")
                     {
                        $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                        $ids=array();
                        foreach($createdbysurveyor as $data){
                           $ids[]=$data->id;
                        }
                        array_push($ids,$user_id);
                        $report_submit_survey_data=$report_submit_survey_data->where(function ($query) use ($ids) {
                           $query->WhereIn('survey.user_id',$ids)
                           ->orWhereIn('survey.assign_to_op',$ids);});	

                        //$upcoming_survey_data=$upcoming_survey_data->WhereIn('survey.user_id',$ids);
                                    
                     }
                     if($user_d->type =="1")
                     {
                        
                        $createdbysurveyor =  User::select('created_by')->where('id',$user_id)->first();
                        $ids=array();
                        if(!empty($createdbysurveyor)){
                           
                              $ids[]=$createdbysurveyor->created_by;
                        }
                           array_push($ids,$user_id);
                           $report_submit_survey_data=$report_submit_survey_data->where(function ($query) use ($ids) {
                              $query->WhereIn('survey.user_id',$ids)
                              ->orWhereIn('survey.assign_to_op',$ids);});	
                     }						

                  

                  
                  $report_submit_survey_data=$report_submit_survey_data->where('survey.status', '=','3')->where('survey.declined', '=','0');	
                     
                  
                     $report_submit_survey_data=$report_submit_survey_data->groupBy('survey.id');

                     // $chat_unread_count=Chat::select('chat.id')->where('is_read','0')->where('receiver_id',$user->id)->count();
                     // if($chat_unread_count>0){
                     // 	$report_submit_survey_data=$report_submit_survey_data->orderBy('survey.active_thread','desc');
                     // }
                     
                     $report_submit_survey_data=$report_submit_survey_data->orderBy('survey.start_date','desc');
                     $report_submit_survey_data=$report_submit_survey_data->get();
                     //dd($upcoming_survey_data);

                     $unpaid_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
                     DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
                     DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
                     DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
                     'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                     'custom_survey_users.surveyors_id as surveyor_id','users.id as operator_id',
                     'vessels.name as vesselsname')
                     ->leftJoin('port', 'port.id', '=', 'survey.port_id')
                     ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
                     ->leftJoin('users', 'users.id', '=', 'survey.user_id')
                     ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
                     ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                     ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
                     ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
                     ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
                        
                    
                        if($user_d->type =="0")
                        {
                           $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                           $ids=array();
                           foreach($createdbysurveyor as $data){
                              $ids[]=$data->id;
                           }
                           array_push($ids,$user_id);
                           $unpaid_survey_data=$unpaid_survey_data->where(function ($query) use ($ids) {
                              $query->WhereIn('survey.user_id',$ids)
                              ->orWhereIn('survey.assign_to_op',$ids);});	

                           //$upcoming_survey_data=$upcoming_survey_data->WhereIn('survey.user_id',$ids);
                                       
                        }
                        if($user_d->type =="1")
                        {
                           
                           $createdbysurveyor =  User::select('created_by')->where('id',$user_id)->first();
                           $ids=array();
                           if(!empty($createdbysurveyor)){
                              
                                 $ids[]=$createdbysurveyor->created_by;
                           }
                              array_push($ids,$user_id);
                              $unpaid_survey_data=$unpaid_survey_data->where(function ($query) use ($ids) {
                                 $query->WhereIn('survey.user_id',$ids)
                                 ->orWhereIn('survey.assign_to_op',$ids);});	
                        }		
                     

                     
                     $unpaid_survey_data=$unpaid_survey_data->where('survey.status', '=','4')->where('survey.declined', '=','0');	
                        
                        $unpaid_survey_data=$unpaid_survey_data->groupBy('survey.id');
                        $unpaid_survey_data=$unpaid_survey_data->orderBy('survey.start_date','ASC');
                        $unpaid_survey_data=$unpaid_survey_data->get();

                        
                     $paid_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
                     DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
                     DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
                     DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
                     'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                     'custom_survey_users.surveyors_id as surveyor_id','users.id as operator_id',
                     'vessels.name as vesselsname')
                     ->leftJoin('port', 'port.id', '=', 'survey.port_id')
                     ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
                     ->leftJoin('users', 'users.id', '=', 'survey.user_id')
                     ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
                     ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                     ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
                     ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
                     ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
                  
                        if($user_d->type =="0")
                        {
                           $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                           $ids=array();
                           foreach($createdbysurveyor as $data){
                              $ids[]=$data->id;
                           }
                           array_push($ids,$user_id);
                           $paid_survey_data=$paid_survey_data->where(function ($query) use ($ids) {
                              $query->WhereIn('survey.user_id',$ids)
                              ->orWhereIn('survey.assign_to_op',$ids);});	

                           //$upcoming_survey_data=$upcoming_survey_data->WhereIn('survey.user_id',$ids);
                                       
                        }
                        if($user_d->type =="1")
                        {
                           
                           $createdbysurveyor =  User::select('created_by')->where('id',$user_id)->first();
                           $ids=array();
                           if(!empty($createdbysurveyor)){
                              
                                 $ids[]=$createdbysurveyor->created_by;
                           }
                              array_push($ids,$user_id);
                              $paid_survey_data=$paid_survey_data->where(function ($query) use ($ids) {
                                 $query->WhereIn('survey.user_id',$ids)
                                 ->orWhereIn('survey.assign_to_op',$ids);});	
                        }
                     

                     $paid_survey_data=$paid_survey_data->where(function ($query) {
                        $query->where('survey.status', '=','5' )
                              ->orWhere('survey.status', '=','6' );
                              
                     });

                     $paid_survey_data=$paid_survey_data->where('survey.declined', '=','0');	
                       
                     
                        $paid_survey_data=$paid_survey_data->groupBy('survey.id');
                        $paid_survey_data=$paid_survey_data->orderBy('survey.start_date','desc');
                        $paid_survey_data=$paid_survey_data->get();

                    
               
      }else
      {
            
         $pending_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
         'port.port as port_name',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
         DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
         DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
            'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
            'custom_survey_users.surveyors_id as surveyor_id',
            'users.id as operator_id',
            'survey_users.status as usstatus','vessels.name as vesselsname')
         ->leftJoin('port', 'port.id', '=', 'survey.port_id')
         ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
         ->leftJoin('users', 'users.id', '=', 'survey.user_id')
         ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
         ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
         ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
         ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
         ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
         
            if($user_d->type =="2")
            {
               
                  $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                  $ids=array();
                  foreach($createdbysurveyor as $data){
                     $ids[]=$data->id;
                  }
                  array_push($ids,$user_id);
                  $pending_survey_data=$pending_survey_data->where(function ($query) use ($ids) {
                  $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                  ->orwhereIn('survey_users.surveyors_id',$ids );});	
               
            }else{
               $pending_survey_data=$pending_survey_data->where(function ($query) use ($user_id) {
                  $query->where('survey_users.surveyors_id', '=',$user_id )
                  ->orWhere('custom_survey_users.surveyors_id',$user_id)
                  ->orWhere('survey.assign_to',$user_id);});					
               }
            
                  $pending_survey_data=$pending_survey_data->where('survey.status', '=','0')->where('survey.declined', '=','0');
                  
                  $pending_survey_data=$pending_survey_data->where(function ($query)  {
                     $query->Where('survey_users.status','pending')
                        ->orwhere('survey_users.status','upcoming')
                        ->orwhere('custom_survey_users.status','waiting' )
                        ->orwhere('custom_survey_users.status','upcoming' )
                        ->orwhere('custom_survey_users.status','approved' );});	

                  $pending_survey_data=$pending_survey_data->groupBy('survey.id');
                  $pending_survey_data=$pending_survey_data->orderBy('survey.start_date','ASC');
                  $pending_survey_data=$pending_survey_data->get();

                  $upcoming_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
               'port.port as port_name',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
               DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
               DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
                  'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                  'custom_survey_users.surveyors_id as surveyor_id',
                  'users.id as operator_id',
                  'survey_users.status as usstatus','vessels.name as vesselsname')
               ->leftJoin('port', 'port.id', '=', 'survey.port_id')
               ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
               ->leftJoin('users', 'users.id', '=', 'survey.user_id')
               ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
               ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
               ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
               ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
               ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
         
            if($user_d->type =="2")
            {
              
                  $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                  $ids=array();
                  foreach($createdbysurveyor as $data){
                     $ids[]=$data->id;
                  }
                  array_push($ids,$user_id);
                  $upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($ids) {
                  $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                  ->orwhereIn('survey_users.surveyors_id',$ids );});	
               
            }else{
               $upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($user_id) {
                  $query->where('survey_users.surveyors_id', '=',$user_id )
                  ->orWhere('custom_survey_users.surveyors_id',$user_id)
                  ->orWhere('survey.assign_to',$user_id);});					
               }
            
               $upcoming_survey_data=$upcoming_survey_data->where('survey.status', '=','1')->where('survey.declined', '=','0');
               $upcoming_survey_data=$upcoming_survey_data->where(function ($query)  {
               $query->Where('survey_users.status','pending')
                  ->orwhere('survey_users.status','upcoming' )
                  ->orwhere('custom_survey_users.status','waiting' )
                  ->orwhere('custom_survey_users.status','upcoming' )
                  ->orwhere('custom_survey_users.status','approved' );});	

                  $upcoming_survey_data=$upcoming_survey_data->groupBy('survey.id');

                  // $chat_unread_count=Chat::select('chat.id')->where('is_read','0')->where('receiver_id',$user->id)->count();
                  // 	if($chat_unread_count>0){
                  // 		$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
                  // 	}
                     
                  $upcoming_survey_data=$upcoming_survey_data->orderBy('survey.start_date','ASC');
                  $upcoming_survey_data=$upcoming_survey_data->get();


                  $report_submit_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
               'port.port as port_name',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
               DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
               DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
                  'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                  'custom_survey_users.surveyors_id as surveyor_id',
                  'users.id as operator_id',
                  'survey_users.status as usstatus','vessels.name as vesselsname')
               ->leftJoin('port', 'port.id', '=', 'survey.port_id')
               ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
               ->leftJoin('users', 'users.id', '=', 'survey.user_id')
               ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
               ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
               ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
               ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
               ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
         
            if($user_d->type =="2")
            {
               
                  $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                  $ids=array();
                  foreach($createdbysurveyor as $data){
                     $ids[]=$data->id;
                  }
                  array_push($ids,$user_id);
                  $report_submit_survey_data=$report_submit_survey_data->where(function ($query) use ($ids) {
                  $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                  ->orwhereIn('survey_users.surveyors_id',$ids );});	
               
            }else{
               $report_submit_survey_data=$report_submit_survey_data->where(function ($query) use ($user_id) {
                  $query->where('survey_users.surveyors_id', '=',$user_id )
                  ->orWhere('custom_survey_users.surveyors_id',$user_id)
                  ->orWhere('survey.assign_to',$user_id);});					
               }
            
               $report_submit_survey_data=$report_submit_survey_data->where('survey.status', '=','3')->where('survey.declined', '=','0');
               $report_submit_survey_data=$report_submit_survey_data->where(function ($query)  {
               $query->Where('survey_users.status','pending')
                  ->orwhere('survey_users.status','upcoming' )
                  ->orwhere('custom_survey_users.status','waiting' )
                  ->orwhere('custom_survey_users.status','upcoming' )
                  ->orwhere('custom_survey_users.status','approved' );});	

                  $report_submit_survey_data=$report_submit_survey_data->groupBy('survey.id');
                  $report_submit_survey_data=$report_submit_survey_data->orderBy('survey.start_date','desc');
                  $report_submit_survey_data=$report_submit_survey_data->get();


                  $pending_payment_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
                  'port.port as port_name',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
                  DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
                  DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
                     'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                     'custom_survey_users.surveyors_id as surveyor_id',
                     'users.id as operator_id',
                     'survey_users.status as usstatus','vessels.name as vesselsname')
                  ->leftJoin('port', 'port.id', '=', 'survey.port_id')
                  ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
                  ->leftJoin('users', 'users.id', '=', 'survey.user_id')
                  ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
                  ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                  ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
                  ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
                  ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
            
               if($user_d->type =="2")
               {
                  
                     $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                     $ids=array();
                     foreach($createdbysurveyor as $data){
                        $ids[]=$data->id;
                     }
                     array_push($ids,$user_id);
                     $pending_payment_survey_data=$pending_payment_survey_data->where(function ($query) use ($ids) {
                     $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                     ->orwhereIn('survey_users.surveyors_id',$ids );});	
                  
               }else{
                  $pending_payment_survey_data=$pending_payment_survey_data->where(function ($query) use ($user_id) {
                     $query->where('survey_users.surveyors_id', '=',$user_id )
                     ->orWhere('custom_survey_users.surveyors_id',$user_id)
                     ->orWhere('survey.assign_to',$user_id);});					
                  }
               
                  $pending_payment_survey_data=$pending_payment_survey_data->where('survey.status', '=','4')->where('survey.declined', '=','0');
                  $pending_payment_survey_data=$pending_payment_survey_data->where(function ($query)  {
                  $query->Where('survey_users.status','pending')
                     ->orwhere('survey_users.status','upcoming' )
                     ->orwhere('custom_survey_users.status','waiting' )
                     ->orwhere('custom_survey_users.status','upcoming' )
                     ->orwhere('custom_survey_users.status','approved' );});	

                     $pending_payment_survey_data=$pending_payment_survey_data->groupBy('survey.id');
                     $pending_payment_survey_data=$pending_payment_survey_data->orderBy('survey.start_date','desc');
                     $pending_payment_survey_data=$pending_payment_survey_data->get();


                     $received_payment_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
                  'port.port as port_name',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
                  DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
                  DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
                     'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                     'custom_survey_users.surveyors_id as surveyor_id',
                     'users.id as operator_id',
                     'survey_users.status as usstatus','vessels.name as vesselsname')
                  ->leftJoin('port', 'port.id', '=', 'survey.port_id')
                  ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
                  ->leftJoin('users', 'users.id', '=', 'survey.user_id')
                  ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
                  ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                  ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
                  ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
                  ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
            
               if($user_d->type =="2")
               {
                  
                     $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                     $ids=array();
                     foreach($createdbysurveyor as $data){
                        $ids[]=$data->id;
                     }
                     array_push($ids,$user_id);
                     $received_payment_survey_data=$received_payment_survey_data->where(function ($query) use ($ids) {
                     $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                     ->orwhereIn('survey_users.surveyors_id',$ids );});	
                  
               }else{
                  $received_payment_survey_data=$received_payment_survey_data->where(function ($query) use ($user_id) {
                     $query->where('survey_users.surveyors_id', '=',$user_id )
                     ->orWhere('custom_survey_users.surveyors_id',$user_id)
                     ->orWhere('survey.assign_to',$user_id);});					
                  }
               
                  $received_payment_survey_data=$received_payment_survey_data->where('survey.status', '=','5')->where('survey.declined', '=','0');
                  $received_payment_survey_data=$received_payment_survey_data->where(function ($query)  {
                  $query->Where('survey_users.status','pending')
                     ->orwhere('survey_users.status','upcoming' )
                     ->orwhere('custom_survey_users.status','waiting' )
                     ->orwhere('custom_survey_users.status','upcoming' )
                     ->orwhere('custom_survey_users.status','approved' );});	

                     $received_payment_survey_data=$received_payment_survey_data->groupBy('survey.id');
                     $received_payment_survey_data=$received_payment_survey_data->orderBy('survey.start_date','desc');
                     $received_payment_survey_data=$received_payment_survey_data->get();


                     $paid_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
                     'port.port as port_name',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
                     DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
                     DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
                        'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
                        'custom_survey_users.surveyors_id as surveyor_id',
                        'users.id as operator_id',
                        'survey_users.status as usstatus','vessels.name as vesselsname')
                     ->leftJoin('port', 'port.id', '=', 'survey.port_id')
                     ->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
                     ->leftJoin('users', 'users.id', '=', 'survey.user_id')
                     ->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
                     ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
                     ->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
                     ->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
                     ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
               
                  if($user_d->type =="2")
                  {
                     
                        $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
                        $ids=array();
                        foreach($createdbysurveyor as $data){
                           $ids[]=$data->id;
                        }
                        array_push($ids,$user_id);
                        $paid_survey_data=$paid_survey_data->where(function ($query) use ($ids) {
                        $query->WhereIn('custom_survey_users.surveyors_id',$ids)
                        ->orwhereIn('survey_users.surveyors_id',$ids );});	
                     
                  }else{
                     $paid_survey_data=$paid_survey_data->where(function ($query) use ($user_id) {
                        $query->where('survey_users.surveyors_id', '=',$user_id )
                        ->orWhere('custom_survey_users.surveyors_id',$user_id)
                        ->orWhere('survey.assign_to',$user_id);});					
                     }
                  
                     $paid_survey_data=$paid_survey_data->where('survey.status', '=','6')->where('survey.declined', '=','0');
                     $paid_survey_data=$paid_survey_data->where(function ($query)  {
                     $query->Where('survey_users.status','pending')
                        ->orwhere('survey_users.status','upcoming' )
                        ->orwhere('custom_survey_users.status','waiting' )
                        ->orwhere('custom_survey_users.status','upcoming' )
                        ->orwhere('custom_survey_users.status','approved' );});	
   
                        $paid_survey_data=$paid_survey_data->groupBy('survey.id');
                        $paid_survey_data=$paid_survey_data->orderBy('survey.start_date','desc');
                        $paid_survey_data=$paid_survey_data->get();

                 
            
      }

      if($user_d->type=='0' || $user_d->type=='1')
		{
         $pending_survey_data=$pending_survey_data->count();
         $upcoming_survey_data=$upcoming_survey_data->count();
         $report_submit_survey_data=$report_submit_survey_data->count();
         $unpaid_survey_data=$unpaid_survey_data->count();
         $paid_survey_data=$paid_survey_data->count();
         $survey_data= $pending_survey_data+$upcoming_survey_data+$report_submit_survey_data+$unpaid_survey_data+$paid_survey_data;
		}else{
				
				
            $pending_survey_data=$pending_survey_data->count();
            $upcoming_survey_data=$upcoming_survey_data->count();
            $report_submit_survey_data=$report_submit_survey_data->count();
            $paid_survey_data=$paid_survey_data->count();
            $pending_payment_survey_data=$pending_payment_survey_data->count();
            $received_payment_survey_data=$received_payment_survey_data->count();
            $survey_data= $pending_survey_data+$upcoming_survey_data+$report_submit_survey_data+$paid_survey_data+$pending_payment_survey_data+$received_payment_survey_data;

		}
      
        return  $survey_data ;
        
     }
		
}
?>