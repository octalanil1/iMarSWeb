<?php
/**
 * AuthController Controller
 */
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Emailtemplates;
use App\Models\Countries;
use App\Models\SurveyUsers;
use App\Models\Customsurveyusers;
use App\Models\Notification;


use App\Helpers;
use App,Auth,Blade,Config,Cache,Cookie,DB,File,Ajax,Hash,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;

class AuthController extends Controller {
	/** 
	 * Function use for send a forgot password email to user
	 *
	 * @param null
	 * 
	 * @return void
	 */
public function login() 
{ 
	header('Content-Type: application/json');
	$status = 0;
	$message = NULL;
	$data = array();
	$data1 = (object) array();
	$is_deactivate = '0';
	$sflag = '0';
	$data_row 		= 	file_get_contents("php://input");
	$decoded 	    = 	json_decode($data_row, true);
			if($decoded) 
			{
				$userArray="";
				if(!empty($decoded['password']) && !empty($decoded['email'])  &&  !empty($decoded['device_id']) && !empty($decoded['device_type'])) 
				{	
					
								if(Auth::attempt(['email' => $decoded['email'], 'password' => $decoded['password']])){ 
									$userArray = Auth::user(); 
								} 
									    												
					if($userArray)
					{
						if ($userArray->id!="")
						{
										
							if($userArray->status == '1')
							{							
								$userArray->device_id = $decoded['device_id'];
								$userArray->device_type = $decoded['device_type'];	

								$userArray->save();
								$data['user_id'] = (string)$userArray->id;
								$data['first_name'] = !empty($userArray->first_name) ? $userArray->first_name: '';
								$data['last_name'] = !empty($userArray->last_name) ? $userArray->last_name: '';
								$data['email'] = $userArray->email;
								/*if($userArray->type=="0" || $userArray->type=="1" ){
									$data['type'] = 'operator';
								}else{
									$data['type'] = 'surveyor';
								}*/
								$data['type'] = $userArray->type;								
								$data['country_code'] = !empty($userArray->country_code) ? $userArray->country_code: '';
								$data['country_id'] = !empty($userArray->country_id) ? $userArray->country_id : '';
								if($userArray->country_id!=""){
									
									$countrydata = Countries::where('id',$userArray->country_id)->first();
									$data['country_name'] = !empty($countrydata->name) ? $countrydata->name : '' ;
								}
								

								$data['mobile_number'] = !empty($userArray->mobile) ? $userArray->mobile : '';
								$average_response_time=0;
								$percentage_job_acceptance=0;
							//	echo $userArray->type;exit;
								if($userArray->type=='2' || $userArray->type=='3' || $userArray->type=='4')
								{
									if($userArray->conduct_custom=='1')
									{
										$first = DB::table('custom_survey_users')->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,updated_at,created_at)) as recponce_time'))->where('surveyors_id',$userArray->id);
										$responce = DB::table('survey_users')->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,updated_at,created_at)) as recponce_time'))->where('surveyors_id',$userArray->id)->union($first)->first();
		
												$average_response_time=(int)$responce->recponce_time*(-1);
												
												$average_response_time=gmdate("H:i", $average_response_time);
												$average_response_time=explode(':',$average_response_time);
												if($average_response_time[0]=='00'){
													$average_response_time=$average_response_time[1].' min';
												}elseif($average_response_time[1]=='00'){
													$average_response_time=$average_response_time[0].' hour ';
				
												}else{
													$average_response_time=$average_response_time[0].' hour '.$average_response_time[1].' min';
												}
												
												
									}else
									{
								
												 $responce=SurveyUsers::select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,updated_at,created_at)) as recponce_time'))
												->where('surveyors_id',$userArray->id)->first();
												 $average_response_time=(int)$responce->recponce_time*(-1);
												
												$average_response_time=gmdate("H:i", $average_response_time);
												$average_response_time=explode(':',$average_response_time);
												if($average_response_time[0]=='00'){
													$average_response_time=$average_response_time[1].' min';
												}elseif($average_response_time[1]=='00'){
													$average_response_time=$average_response_time[0].' hour ';
				
												}else{
													$average_response_time=$average_response_time[0].' hour '.$average_response_time[1].' min';
												}
												
												
									}
									$first = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$userArray->id);
		
									$total_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$userArray->id)->union($first)->count();
		
									$second = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$userArray->id) ->where('status','upcoming');
		
									$total_accept_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$userArray->id)->where('status','upcoming')->union($second)->count();
							
									
		
									if(!empty($total_accept_job) && !empty($total_job) ){
										$percentage_job_acceptance=floor($total_accept_job/$total_job*100);
									}	
									
							}
							  
									
							$unreadnotification_count_data = Notification::where('user_id',$userArray->id)->where('is_read',0)
							->count();	
							$address="";
									if(!empty( $userArray->street_address) || !empty( $userArray->state) || !empty( $userArray->city) || !empty( $userArray->pincode))
									{
										$address=$userArray->street_address.','.$userArray->state.','.$userArray->city;
									}		
								//$data['address'] = $userArray->address ? $userArray->address : 'New York';
								$data['company'] =!empty( $userArray->company) ?  $userArray->company : '';
								$data['address'] = $address;
								$data['company_tax_id'] = !empty( $userArray->company_tax_id) ?  $userArray->company_tax_id : '';
								$data['company_website'] = !empty( $userArray->company_website) ?  $userArray->company_website : '';
								$data['ssn'] = !empty( $userArray->ssn) ? $userArray->ssn : '';
								$data['about_me'] =!empty( $userArray->about_me) ? $userArray->about_me : '' ;
								$data['experience'] = !empty( $userArray->experience) ? $userArray->experience : '';
								$data['email_verify'] =!empty(  $userArray->email_verify) ?  $userArray->email_verify : '';
								$data['rating']=!empty($userArray->rating) ? (string)$userArray->rating : '0';
								$data['average_response_time']=!empty($average_response_time) ? (string)$average_response_time : '';
								$data['percentage_job_acceptance']=!empty($percentage_job_acceptance) ? (string)$percentage_job_acceptance.'%': '0%';
								$data['unread_count']=!empty($unreadnotification_count_data) ? (string)$unreadnotification_count_data: '0';

								
								

								if($userArray->profile_pic &&  file_exists(public_path('/media/users/').$userArray->profile_pic ))
									{
										$data['profile_pic'] = url('/').'/media/users/'.$userArray->profile_pic;
									}else{
										$data['profile_pic'] ='';
									}
							    $message = "You have logged in successfully.";
								$data1 = $data;
								$status = 1;
						    } elseif($userArray->status == '0') {
								$message = "Your account is deactive. Please contact to admin";
							} 
							else {
								$message = "Your account is pending. Please contact to admin";
							} 	
								
						}else{
							$message = "Invalid login credentials. Please try again.";
						}
					}else{
						$message = "Invalid login credentials. Please try again.";
					}
				}else {
					$message = 'One or more required fields are missing. Please try again.';
				}
			}else {
				$message = 'Opps! Something went wrong. Please try again.';
			}
		
	
	$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
	echo json_encode(array('response' => $response_data));
	die;
}

function forgotpassword(Request $request)

	{
		header('Content-Type: application/json');
		$status = 0;
		$message = NULL;
		$data = array();
		$data1 = array();
		$is_deactivate = '0';
		$sflag = '0';
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);

			if($decoded) 
			{
				if(!empty($decoded['email'])) {
					
					$userArray = User::where('email','=',$decoded['email'])->first();
					if($userArray)
					{
						$status = 1;
						$length = 10;
						$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						$charactersLength = strlen($characters);
						$randomString = '';

						for ($i = 0; $i < $length; $i++) 
						{$randomString .= $characters[rand(0, $charactersLength - 1)];}
						$url = $randomString;
						$cur_date = date("Y-m-d H:i:s");
						$userArray->forgot_url = $url;
						$userArray->forgot_time = $cur_date;
						$userArray->save();
						$create_url = \App::make('url')->to('/create-password')."/".$url;

						$emailData = Emailtemplates::where('slug','=','user-forgot-password')->first();
						if($emailData){
						  $textMessage = strip_tags($emailData->description);
						  $userArray->subject = $emailData->subject;
						  if($userArray->email!='')
						  {
							  $textMessage = str_replace(array('{USER_NAME}','{FORGOT_PASSWORD_LINK}'), array($userArray->first_name,$create_url),$textMessage);
							  
							  Mail::raw($textMessage, function ($messages) use ($userArray) {
								  $to = $userArray->email;
								  $messages->to($to)->subject($userArray->subject);
							  });
						  }
					  }
						
						
						$message = "Forgot password instruction has been successfully sent on your email.";
								
					}else {
					$message = 'Please enter register email id.';
				    }		
						
					
				}else {
					$message = 'One or more required fields are missing. Please try again.';
				}
			}else {
				$message = 'Opps! Something went wrong. Please try again.';
			}
		
			$response_data = array('status'=>$status,'message'=>$message);
			echo json_encode(array('response' => $response_data));
			die;
	}

	public function ChangeDeviceId(Request $request)
		{
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
			if($decoded) 
			{
					
					if(!empty($decoded['user_id']) && !empty($decoded['device_id']))
					{	
						$r=1;
						$user_data =  User::select('*')->where('id',$decoded['user_id'])->count();
						if(empty($user_data)){
							$r=0;
							
						}
						if($r==1)
						{
							$user_data =  User::select('*')->where('id',$decoded['user_id'])->first();
							$user_data->device_id=$decoded['device_id'];
							$user_data->save();
							$status = 1;
							$message = 'Device id Changed Successfully.';
						}
						else{
							$message = 'User data not found.';
						}
						
				}else {
					$message = 'One or more required fields are missing. Please try again.';
				}
			}else 
			{
				$message = 'Opps! Something went wrong. Please try again.';
			}
					$response_data = array('status'=>$status,'message'=>$message);
					echo json_encode(array('response' => $response_data));
					die;
		}
		public function UserSetting(Request $request)
		{
			$user = Auth::user();
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
			if($decoded) 
			{
				if (!empty($decoded['user_id'])) 
				{
						 $user = Auth::user();
		
						$user_data =   User::where('id',$decoded['user_id'])->first();
						
						if(!empty($user_data))
						{
	 
							if($user_data->is_avail=='1')
							{
							 $user_data->is_avail='0';
							 $user_data->save();
							 
							}else{
								$user_data->is_avail='1';
								$user_data->save();
							}
							$data1=array('id'=>(string)($user_data->id),'status'=>$user_data->is_avail);
							$status = 1;
							$message = 'User setting save successfully.';
							
						}else{
							
							 $message = 'Data not found.';
						}
	 
					
				 }else {
					 $message = 'One or more required fields are missing. Please try again.';
				 }
	 
			 }else 
			 {
				 $message = 'Opps! Something went wrong. Please try again.';
			 }
			 $response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
			 echo json_encode(array('response' => $response_data));
			 die;
	 
		}
		// public function changepasswordpost(Request $request)
		// {
		// 	header('Content-Type: application/json');
		// 	$status = 0;
		// 	$message = NULL;
		// 	$data = array();
		// 	$data1 =array();
		// 	$data_row 		= 	file_get_contents("php://input");
		// 	$decoded 	    = 	json_decode($data_row, true);
		// 	if($decoded) 
		// 	{
					
		// 			if(!empty($decoded['user_id']) && !empty($decoded['old_password']) && !empty($decoded['new_password']) )
		// 			{	
		// 				$r=1;
		// 				$user_data =  User::select('*')->where('id',$decoded['user_id'])->count();
		// 				if(empty($user_data)){
		// 					$r=0;
							
		// 				}
		// 				if($r==1)
		// 				{
		// 					$user =  User::select('*')->where('id',$decoded['user_id'])->first();

				
		// 					if(!Hash::check($decoded['old_password'], $user->password))
		// 					{
		// 						$message ="Old Password Wrong";
								
		// 					}else{
								
		// 						$user->password = Hash::make($decoded['new_password']);
		// 						$user->save();
		// 						$status = 1;
		// 						$message ="Password change succesfully";
		// 					}
							
							
		// 				}
		// 				else{
		// 					$message = 'User data not found.';
		// 				}
						
		// 		}else {
		// 			$message = 'One or more required fields are missing. Please try again.';
		// 		}
		// 	}else 
		// 	{
		// 		$message = 'Opps! Something went wrong. Please try again.';
		// 	}
		// 			$response_data = array('status'=>$status,'message'=>$message);
		// 			echo json_encode(array('response' => $response_data));
		// 			die;
		// }

	
}//end Class()