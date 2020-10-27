<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Validator;
use App\User;
use App\Models\Notification;
use App\Models\Survey;
use App\Models\UsersPort;
use App\Models\Countries;
use App\Models\Emailtemplates;
use App\Models\Agents;
use App\Models\Vessels;
use App\Models\Surveytype;
use App\Models\Port;
use App\Models\UsersSurveyPrice;
use App\Models\SurveyUsers;
use App\Models\Events;
use App\Models\Bankdetail;
use App\Models\Rating;
use App\Models\Earning;
use App\Models\Disputerequest;
use App\Models\Customsurveyusers;
use App\Models\Paymentrequest;
use Hash;
use Auth;
use DB;
use File;
use App\Helpers;
use Config;
use Session;
use URL;
use Mail;
use PDF;
class CronController extends Controller 
{
	public function SendSurveyRequest(Request $request)
	{   
		
		$survey_users =  SurveyUsers::select('survey_users.*')
		->where('status','pending')
		//->where('survey_id',57)
		 ->where('created_at', '<=', DB::raw('DATE_SUB(NOW(), INTERVAL 8 HOUR)'))
		->get();

		//dd($survey_users);
		if(!empty($survey_users))
		{
			foreach($survey_users as $survey_data)
			{
				

				$surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_data->survey_id)->first();

				if($surveyor_data->assign_to_op!="" || $surveyor_data->assign_to_op!="0"){
					$operator_id=$surveyor_data->assign_to_op;
				}else{
					$operator_id=$surveyor_data->user_id;

				}

				$operator_token =  User::select('users.*')->where('id',$operator_id)->first(); 

				 
				$survey_users1 =  SurveyUsers::select('survey_users.*')
				->where('id',$survey_data->id)->first();
				$survey_users1->status="declined";
				$survey_users1->save();

				$SurveyUsers =new SurveyUsers;
				$Surveyc= $SurveyUsers->where('survey_id',$survey_data->survey_id)->count();
					$Surveydeclinec= $SurveyUsers->where('survey_id',$survey_data->survey_id)
					->where('status','declined')
					->count();
					
						if($Surveyc==$Surveydeclinec)
						{
							$surveyor_data->declined='1';
							$surveyor_data->active_thread='0';
							$surveyor_data->save(); 
						}
						
				// $survey_usersc =  SurveyUsers::select('survey_users.*')
				// ->where('survey_users.survey_id',$survey_id)
				// ->count();
				// if($survey_usersc=='1')
				// {
				// 	$surveyor_data->status='2';
										
				// 	$surveyor_data->save(); 
				// }
				// $survey_usersd =  SurveyUsers::select('survey_users.*')
				// 	->where('survey_users.survey_id',$survey_id)
				// 	->where('survey_users.surveyors_id',$surveyors_id)
				// 	->first();

				// $survey_usersd->delete();
				
				
				$helper=new Helpers;
				$helper->SendNotification($operator_token->device_id,'Survey Request Declined','One of the surveyors you selected for the survey has declined the request. If all your selected surveyors decline the request, the survey will be listed in Cancelled surveys tab.
				');
				$notification = new Notification();
				$notification->user_id = $operator_token->id;
				$notification->title = 'Survey Request Declined';
				$notification->noti_type = 'Survey Request Declined' ;
				$notification->user_type = $operator_token->type;
				$notification->notification = 'One of the surveyors you selected for the survey has declined the request. If all your selected surveyors decline the request, the survey will be listed in Cancelled surveys tab.';
				$notification->country_id = $operator_token->country_id;
				$notification->is_read = 0;
				$notification->save();

				// $data1 = array( 'email' =>$operator_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $operator_token->email,'content' => 'Your primary Surveyor has declined the Survey request. Your Substitute 1, Substitute 2, and other eligible Surveyors will be contacted in order automatically to fulfill your Surveyor request within 24 hours. You will be notified once a Surveyor is assigned to the Survey request'));
				// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
				// {
				// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Decline Your Survey Request' );

				// });

				$emailData = Emailtemplates::where('slug','=','decline-survey')->first();

						if($emailData){
							$textMessage = strip_tags($emailData->description);
							$subject = $emailData->subject;
							$to = $operator_token->email;

							if($operator_token->first_name!='')
							{
								$textMessage = str_replace(array('USER_NAME'), array($operator_token->first_name),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}

				$survey_users_next = SurveyUsers::select('survey_users.*')->where('survey_users.type', '>', $survey_data->type)->where('survey_users.survey_id',$survey_data->survey_id)->first();
				//dd($survey_users_next);
				if(!empty($survey_users_next))
				{
					$survey_users_next->status='pending';
					$survey_users_next->save();


							$survey_price=$helper->SurveyorPriceDetail($surveyor_data->survey_type_id,$survey_users_next->surveyors_id);
							if($survey_price){
								$surveyor_data->survey_price=$survey_price;
							}
							$port_price=$helper->SurveyorPortPrice($surveyor_data->port_id,$survey_users_next->surveyors_id);
							if($port_price){
								$surveyor_data->port_price=$port_price;
							}
							$surveyor_data->save();

					$message_token =  User::select('users.*')->where('id',$survey_users_next->surveyors_id)->first(); 
					$helper=new Helpers;
					$helper->SendNotification($message_token->device_id,'New survey request received!','New Survey Request Received. Please accept within 8 hours, or the request will be cancelled');
					
						$notification = new Notification();
						$notification->user_id = $message_token->id;
						$notification->title = 'New survey request received!';
						$notification->noti_type = 'New survey request received!';
						$notification->user_type = $message_token->type;
						$notification->notification = 'New Survey Request Received. Please accept within 8 hours, or the request will be cancelled';
						$notification->country_id = $message_token->country_id;
						$notification->is_read = 0;
						$notification->save();
						
						if($surveyor_data->survey_type_id=='31'){
							$emailData = Emailtemplates::where('slug','=','appoint-custom-survey')->first();
						}else{
							$emailData = Emailtemplates::where('slug','=','appoint-survey')->first();
						}

								if($emailData){
									$textMessage = strip_tags($emailData->description);
									$subject = $emailData->subject;
									$to = $message_token->email;

									if($message_token->first_name!='')
									{
										$textMessage = str_replace(array('USER_NAME'), array($message_token->first_name),$textMessage);
										
										Mail::raw($textMessage, function ($messages) use ($to,$subject) {
											
											$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
										});
									}
								}






				}

			}
			$message = 'Survey request updated successfully.';
			return response()->json(['status'=>"1" ,'message'=>$message]);
		}else
		{
			return response()->json(['status'=>"0" ,'message'=>"Data Not Found"]);
		}
		
	}


	public function AcceptReport(Request $request)
		{
					$surveys_data =  Survey::select('survey.*')->where('survey.status','3')
					 //->where('id',50)
					->where('created_at', '<=', DB::raw('DATE_SUB(NOW(), INTERVAL 3 DAY)'))
					->get();

				//	dd($surveys_data);
					
					if(!empty($surveys_data))
					{
							foreach($surveys_data as $surveyor_data)
							{

								$survey_id=$surveyor_data->id;
							
								if($surveyor_data->survey_type_id!='31')
								{
									$survey_users =  SurveyUsers::select('survey_users.*')
									->where('survey_users.survey_id',$survey_id)
									->where('survey_users.status','upcoming')
									->first();
									$survey_users->is_finished='1';
									$survey_users->save();
								}

								
									
								if(!empty($surveyor_data) || !empty($survey_users))
								{

									if($surveyor_data->assign_to!="0"){
										$surveyor_token =  User::select('users.company','users.company_address'
										,'c.name as country','users.city','users.state','users.street_address','users.pincode'
										,'users.id','users.email','users.first_name','users.type','users.device_id','users.country_id')
										->leftJoin('countries as c', 'users.country_id', '=', 'c.id')
										->where('users.id',$surveyor_data->assign_to)->first(); 						
									}else
									{
										$surveyor_token =  User::select('users.company','users.company_address'
										,'c.name as country','users.city','users.state','users.street_address','users.pincode'
										,'users.id','users.email','users.first_name','users.type','users.device_id','users.country_id')
										->leftJoin('countries as c', 'users.country_id', '=', 'c.id')
										->where('users.id',$surveyor_data->accept_by)->first(); 
									}
									  
										$surveyor_data->status='4';
										

										$helper=new Helpers;
										$helper->SendNotification($surveyor_token->device_id,'Survey report has been accepted','The report you submitted has been accepted by the operator. The invoice has been emailed to the operator.');

										$notification = new Notification();
										$notification->user_id = $surveyor_token->id;
										$notification->title = 'Survey report has been accepted';
										$notification->noti_type = 'Survey report has been accepted';
										$notification->user_type = $surveyor_token->type;
										$notification->notification = 'The report you submitted has been accepted by the operator. The invoice has been emailed to the operator';
										$notification->country_id = $surveyor_token->country_id;
										$notification->is_read = 0;
										$notification->save();

										// $data1 = array( 'email' =>$surveyor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyor_token->email,'content' => 'Survey report has been accepted'));
										// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										// {
										// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Survey Report Accept' );
						
										// });
										$emailData = Emailtemplates::where('slug','=','survey-report-accept')->first();

										if($emailData){
											$textMessage = strip_tags($emailData->description);
											$subject = $emailData->subject;
											$to = $surveyor_token->email;
							
											if($surveyor_token->first_name!=''  )
											{
												$textMessage = str_replace(array('USER_NAME'), array($surveyor_token->first_name),$textMessage);
												
												Mail::raw($textMessage, function ($messages) use ($to,$subject) {
													
													$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
												});
											}
										}
			
										$total_price=0;
										$survey_price=0;
										$port_price=0;

										if($surveyor_data->survey_type_id=='31')
										{
											$custom_survey_price_data = Customsurveyusers::select('custom_survey_users.*')
											->where("custom_survey_users.survey_id",$surveyor_data->id)
											->where("custom_survey_users.surveyors_id",$surveyor_data->accept_by)->first();

											$total_price=$custom_survey_price_data->amount;
											$port_price=0;
											$survey_type_price=$custom_survey_price_data->amount;
										}else{

											$survey_price_data = UsersSurveyPrice::select('users_survey_price.*')
											->where("users_survey_price.survey_type_id",$surveyor_data->survey_type_id)
											->where("users_survey_price.user_id",$surveyor_data->accept_by)->first();

											if($surveyor_data->no_of_days!="0"){
												$total_price=$surveyor_data->survey_price*$surveyor_data->no_of_days+$surveyor_data->port_price;
												$survey_type_price=$surveyor_data->survey_price*$surveyor_data->no_of_days;
											}else{
												$total_price=$surveyor_data->survey_price+$surveyor_data->port_price;
												$survey_type_price=$surveyor_data->survey_price;
											}
											
											$port_price=$surveyor_data->port_price;
										}
										

										
										$payment = new Earning;

										$payment->survey_id= $survey_id;
										$payment->operator_id= $surveyor_data->user_id;
										$payment->surveyor_id =$surveyor_data->accept_by;
										$payment->invoice_amount=$total_price ;
										$payment->save();

										$invoice_data = Earning::select("payment.*",'port.port as port_name','survey_type.name as survey_type_name',
										'vessels.name as vesselsname','survey.survey_number','vessels.imo_number')
										->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
										->leftJoin('port', 'port.id', '=', 'survey.port_id')
										->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
										->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
										->where('payment.id',$payment->id)
										->first();

										if($surveyor_data->assign_to_op!="" || $surveyor_data->assign_to_op!="0"){
											$operator_id=$surveyor_data->assign_to_op;
										}else{
											$operator_id=$surveyor_data->user_id;
			
										}
								
								$vessels_data=Vessels::select('vessels.*')->where('id',$surveyor_data->ship_id)->first();
								//dd($vessels_data);
									$op_token =  User::select('users.company','users.first_name','users.company_address'
									,'c.name as country','users.city','users.state',
									'users.street_address','users.pincode','users.id','users.email',
									DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
									'users.type','users.device_id','users.country_id')
									->leftJoin('countries as c', 'users.country_id', '=', 'c.id')
									->where('users.id',$operator_id)->first(); 
									if($surveyor_data->ship_id!="")
									{
										// $vessels_prime=Vessels::where('id',$surveyor_data->ship_id)->first();
										
										if($vessels_data->same_as_company==1)
										{ 
											$company=$op_token->company;
											
										}else{
											$company=$vessels_data->company;
											
										}
										
										if($vessels_data->same_as_company_address==1)
										{ 
											
											$address1=$op_token->street_address;
											$address2=$op_token->city.' ,'.$op_token->state.' ,'.$op_token->state.' ,'.$op_token->pincode;
										}else{
											
											$address1=$vessels_data->address;
											$address2=$vessels_data->city.' ,'.$vessels_data->state.'  ,'.$vessels_data->pincode;
										}
	
									} 

										
						
									$invoice_ar=array('survey_number'=> $surveyor_data->survey_number,
									'vesselsname'=> $invoice_data->vesselsname,
									'imo_number'=> $invoice_data->imo_number,
									'port_name'=> $invoice_data->port_name,
									'date'=>date('d-M-Y'),
									'due_date'=>date('d-M-Y', strtotime('+1 month', strtotime(date('d-M-Y')))),
									'amount'=>$invoice_data->invoice_amount,
									'survey_type_price'=>$survey_type_price,
									'survey_type_id'=>$invoice_data->survey_type_id,
									'survey_type_name'=>$invoice_data->survey_type_name,
									'port_price'=>$port_price,
	   
									'from'=>array('company'=>$surveyor_token->company ,
													'email'=>$surveyor_token->email ,
												   'address1'=>$surveyor_token->street_address,
												   'address2'=>$surveyor_token->city.' ,'.$surveyor_token->state.' ,'.$surveyor_token->state.' ,'.$surveyor_token->pincode),
								   'to'=>array('company'=>$company ,
								   'email'=>$op_token->email ,
								   'operator_name'=>$op_token->username ,
								   'address1'=>$address1,
								   'address2'=>$address2)
											   );
												//	dd($invoice_ar);

												$data2=array('content' => $invoice_ar);
												$pdf = PDF::loadView('pages.invoice', compact('data2'));
												$invoice_file= 'invoice_'.$invoice_data->survey_number.'.pdf';
												$pdf->save(public_path().'/media/invoice/'. $invoice_file);
												$surveyor_data->invoice=$invoice_file;
												$surveyor_data->save();

										// $data1 = array( 'email' =>$op_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $op_token->email,'content' => $invoice_ar));
										// Mail::send( 'pages.email.invoice',$data1, function( $message ) use ($data1)
										// {
										// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Survey Report Accept' );
						
										// });
										$link = \App::make('url')->to('/public/media/invoice')."/".$surveyor_data->invoice;

										
							$emailData = Emailtemplates::where('slug','=','survey-accept-invoice-send-to-operator')->first();

							if($emailData){
								$textMessage = strip_tags($emailData->description);
								$subject = $emailData->subject;
								$to = $op_token->email;
				
								if($op_token->first_name!='' )
								{
									$textMessage = str_replace(array('USER_NAME','LINK'), array($op_token->first_name,$link),$textMessage);
									
									Mail::raw($textMessage, function ($messages) use ($to,$subject) {
										
										$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
									});
								}
							}
							if($vessels_data->email!=""){
								$emailData = Emailtemplates::where('slug','=','survey-accept-invoice-send-to-operator')->first();
	
								if($emailData){
									$textMessage = strip_tags($emailData->description);
									$subject = $emailData->subject;
									$vessels_email_to = $vessels_data->email;
		
									if($op_token->first_name!='' )
									{
										$textMessage = str_replace(array('USER_NAME','LINK'), array($op_token->first_name,$link),$textMessage);
										
										Mail::raw($textMessage, function ($messages) use ($vessels_email_to,$subject) {
											
											$messages->from('imars@marineinfotech.com','iMarS')->to($vessels_email_to)->subject($subject);
										});
									}
								}
							}
							if($vessels_data->additional_email!=""){
								$emailData = Emailtemplates::where('slug','=','survey-accept-invoice-send-to-operator')->first();
	
								if($emailData){
									$textMessage = strip_tags($emailData->description);
									$subject = $emailData->subject;
									$additional_email_to = $vessels_data->additional_email;
		
									if($op_token->first_name!='' )
									{
										$textMessage = str_replace(array('USER_NAME','LINK'), array($op_token->first_name,$link),$textMessage);
										
										Mail::raw($textMessage, function ($messages) use ($additional_email_to,$subject) {
											
											$messages->from('imars@marineinfotech.com','iMarS')->to($additional_email_to)->subject($subject);
										});
									}
								}
							}

								
						}
								
						}
						$message = 'Report Accept Successfully.';
						return response()->json(['class'=>"success" ,'message'=>$message]);
				}
				else{
				
					return response()->json(['status'=>"0" ,'message'=>"Data Not Found"]);

				}
		}
	
}

?>