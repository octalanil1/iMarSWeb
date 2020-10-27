<?php
/**
 * AuthController Controller
 */
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Vessels;
use App\Models\Agents;
use App\Models\Survey;
use App\Models\Notification;
use App\Models\Surveytype;
use App\Models\Port;
use App\Models\SurveyUsers;
use App\Models\Countries;
use App\Models\UsersSurveyPrice;
use App\Models\Events;
use App\Models\Rating;
use App\Models\Customsurveyusers;
use App\Models\Earning;
use App\Models\UsersPort;
use App\Models\Disputerequest;
use App\Models\Emailtemplates;

use PDF;
use App\Helpers;
use App,Auth,Blade,Config,Cache,Cookie,DB,File,Ajax,Hash,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
use phpDocumentor\Reflection\Types\Float_;

class OperatorController extends Controller {
	/** 
	 * Function use for send a forgot password email to user
	 *
	 * @param null
	 * 
	 * @return void
	 */

	public function AddVessels(Request $request)
	{
			$validator = Validator::make($request->all(), [
			'name' => 'required',
			'imo_number' => 'required|max:50',
			'user_id' => 'required|max:50',
			'email' => 'required|max:50',
		
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{

				
				$name =  $request->input('name');
				$imo_number =  $request->input('imo_number');
				$user_id =  (string)($request->input('user_id'));
				$email =  $request->input('email');
				$additional_email =  $request->input('additional_email');

				$company =  $request->input('company');
				 $same_as_company =  $request->input('same_as_company');
				 $same_as_company_address =  $request->input('same_as_company_address');
				 $address =  $request->input('address');
				 $city =  $request->input('city');
				 $state =  $request->input('state');
				 $pincode =  $request->input('pincode');

				$image = $request->file('image');
				$r=1;
					$vessels_data_count =  Vessels::select('*')->where('imo_number',$imo_number)->count();
					if(!empty($vessels_data_count)){
						$r=0;
						$status=0;
						$data1=(object)array();
						$message = 'IMO Number Already Exist.';
					}

					if($r==1)
					{
						$vessels= new Vessels();
						$vessels->name=$name;
						$vessels->imo_number=$imo_number;
						$vessels->user_id=$user_id;
						$vessels->email=$email;
						if(isset($additional_email))
						{$vessels->additional_email=$additional_email;
						}
						
						if($same_as_company=='1')
						{
							$vessels->same_as_company=$same_as_company;
							$user_info= User::find($request->input('user_id'));
							$vessels->company=$user_info->company;

						}else{
							$vessels->company=$company;
						}
						if($same_as_company_address=='1')
						 {$vessels->same_as_company_address=$same_as_company_address;
						 }
						// 	$user_info= User::find($user->id);
						// 	$vessels->address=$user_info->company_address;
						// }

						if(	$address!=""){
							$vessels->address = $address;
						}
						if(	$city!=""){
							$vessels->city = $city;
						}
						if(	$state!=""){
							$vessels->state = $state;
						}
						if(	$pincode!=""){
							$vessels->pincode = $pincode;
						}
						if(isset($image))
						{
							$imageName = time().$image->getClientOriginalName();
							$image->move(public_path().'/media/vessels', $imageName);
							$imageName =str_replace(" ", "", $imageName);
							$vessels->image = $imageName;
						}
						$vessels->favourite ='0';
						$vessels->save();
						// dd($vessels);
						$data1=array('id'=>(string)($vessels->id),
						'name'=>$vessels->name,
						'user_id'=>(string)($vessels->user_id),
						'imo_number'=>$vessels->imo_number,
						'company'=>!empty($vessels->company)?$vessels->company:'',
						'address'=>!empty($vessels->address)?$vessels->address:'',
						'city'=>!empty($vessels->city)?$vessels->city:'',
						'state'=>!empty($vessels->state)?$vessels->state:'',
						'pincode'=>!empty($vessels->pincode)?$vessels->pincode:'',
						'email'=>$vessels->email,
						'additional_email'=>!empty($vessels->additional_email)?$vessels->additional_email:''
						,'same_as_company'=>$vessels->same_as_company
						,'is_favourite'=>$vessels->favourite
						,'same_as_company_address'=>$vessels->same_as_company_address,
						'image'=>URL::to('/media/vessels').'/'.$vessels->image);
						$status=1;
						$message = 'New vessel - added successfully…';
					}
			}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
}

public function VesselsList(Request $request)
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
				
				if(!empty($decoded['user_id']))
				{	
					$r=1;
					$user =  User::select('id','type')->where('id',$decoded['user_id'])->first();

						if($user->type=='0'){
							$createdbysurveyor =  User::select('id')->where('created_by',$decoded['user_id'])->get();
							$ids=array();
							if(!empty($createdbysurveyor)){
								foreach($createdbysurveyor as $data){
									$ids[]=$data->id;
								}
							}
								array_push($ids,$decoded['user_id']);
						
						}else
						{
							$createdbysurveyor =  User::select('created_by')->where('id',$decoded['user_id'])->first();
							$ids=array();
							$createdbydpsurveyor =  User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
							if(!empty($createdbydpsurveyor)){
								
								foreach($createdbydpsurveyor as $data){
									$ids[]=$data->id;
								}
							}
								array_push($ids,$createdbysurveyor->created_by);
						}
						
						$vessels_data_count =  Vessels::select('*')->whereIn('user_id',$ids)
						// ->orderBy('vessels.favourite','DESC')
						->orderBy('vessels.name','asc')->count();
					if(empty($vessels_data_count)){
						$r=0;
					}
					if($r==1)
					{
						$user =  User::select('type')->where('id',$decoded['user_id'])->first();

						if($user->type=='0'){
							$createdbysurveyor =  User::select('id')->where('created_by',$decoded['user_id'])->get();
							$ids=array();
							if(!empty($createdbysurveyor)){
								foreach($createdbysurveyor as $data){
									$ids[]=$data->id;
								}
							}
								array_push($ids,$decoded['user_id']);
						
						}else
						{
							$createdbysurveyor =  User::select('created_by')->where('id',$decoded['user_id'])->first();
							$ids=array();
							$createdbydpsurveyor =  User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
							if(!empty($createdbydpsurveyor)){
								
								foreach($createdbydpsurveyor as $data){
									$ids[]=$data->id;
								}
							}
								array_push($ids,$createdbysurveyor->created_by);
						}
						
						$vessels_data =  Vessels::select('*')->whereIn('user_id',$ids)
						// ->orderBy('vessels.favourite','DESC')
						->orderBy('vessels.name','asc')->get();

						foreach($vessels_data  as $vessels )
						{
							$data1[]=array('id'=>(string)($vessels->id),
							'user_id'=>(string)($vessels->user_id),
							'name'=>$vessels->name,
						'user_id'=>(string)($vessels->user_id),
						'imo_number'=>$vessels->imo_number,
						'company'=>!empty($vessels->company)?$vessels->company:'',
						'address'=>!empty($vessels->address)?$vessels->address:'',
						'city'=>!empty($vessels->city)?$vessels->city:'',
						'state'=>!empty($vessels->state)?$vessels->state:'',
						'pincode'=>!empty($vessels->pincode)?$vessels->pincode:'',
						'email'=>$vessels->email,
						'additional_email'=>!empty($vessels->additional_email)?$vessels->additional_email:''
						,'same_as_company'=>$vessels->same_as_company
						,'is_favourite'=>$vessels->favourite
						,'same_as_company_address'=>$vessels->same_as_company_address,
						'image'=>URL::to('/media/vessels').'/'.$vessels->image);
					}
						$status = 1;
						$message = 'Vessels listing below.';
					}
					else{
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
   public function addshipfavourite(Request $request)
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
		   if (!empty($decoded['ship_id']) && !empty($decoded['user_id'])) 
		   {
					$user = Auth::user();
   
				   $vesselsf =   Vessels::where('id',$decoded['ship_id'])->where('user_id',$decoded['user_id'])->first();
				   
				   if(!empty($vesselsf))
				   {

					   if($vesselsf->favourite=='1'){
						
						$message = 'You have add already favourite this ship.';
						$vesselsf->favourite='0';
						$vesselsf->save();
						$data1=array('id'=>(string)($vesselsf->id),'name'=>$vesselsf->name,'user_id'=>(string)($vesselsf->user_id),
							 'imo_number'=>$vesselsf->imo_number,'company'=>$vesselsf->company,
							 'address'=>$vesselsf->address,'email'=>$vesselsf->email,
							 'additional_email'=>$vesselsf->additional_email,
							 'same_as_company'=>$vesselsf->same_as_company
							 ,'same_as_company_address'=>$vesselsf->same_as_company_address
							 ,'is_favourite'=>$vesselsf->favourite,
							 'image'=>URL::to('/media/vessels').'/'.$vesselsf->image);
						$status = 1;
						$message = 'You add Unfavourite ship successfully.';
						   
					   }else{
						   $vesselsf->favourite='1';
						   $vesselsf->save();
						   $data1=array('id'=>(string)($vesselsf->id),'name'=>$vesselsf->name,'user_id'=>(string)($vesselsf->user_id),
								'imo_number'=>$vesselsf->imo_number,'company'=>$vesselsf->company,
								'address'=>$vesselsf->address,'email'=>$vesselsf->email,
								'additional_email'=>$vesselsf->additional_email,
								'same_as_company'=>$vesselsf->same_as_company
								,'same_as_company_address'=>$vesselsf->same_as_company_address
								,'is_favourite'=>$vesselsf->favourite,
								'image'=>URL::to('/media/vessels').'/'.$vesselsf->image);
						   $status = 1;
						   $message = 'You add favourite ship successfully.';
						  
					   }
					   
					   
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
   public function EditVessels(Request $request)
	{
			$validator = Validator::make($request->all(), [
			'id' => 'required',
			'user_id' => 'required|max:50',
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{

				$id =  (string)($request->input('id'));
				$name =  $request->input('name');
				$imo_number =  $request->input('imo_number');
				$user_id =  (string)($request->input('user_id'));
				$email =  $request->input('email');
				$additional_email =  $request->input('additional_email');
				$company =  $request->input('company');

				 $address =  $request->input('address');
				 $city =  $request->input('city');
				 $state =  $request->input('state');
				 $pincode =  $request->input('pincode');

				$same_as_company =  $request->input('same_as_company');
				$same_as_company_address =  $request->input('same_as_company_address');
				$image = $request->file('image');
				$r=1;
					$vessels_data_count =  Vessels::select('*')->where('imo_number',$imo_number)->where('id','!=',$id)->count();
					if(!empty($vessels_data_count)){
						$r=0;
						$status=0;
						$data1=(object)array();
						$message = 'IMO Number Already Exist.';
					}
					if($r==1)
					{
						$vessels= Vessels::where('id',$id)->where('user_id',$user_id)->first();
						if(!empty($vessels))
						{
								$vessels->name=$name;
								$vessels->imo_number=$imo_number;
								$vessels->user_id=$user_id;
								$vessels->email=$email;
								if(isset($additional_email))
								{$vessels->additional_email=$additional_email;
								}
								
								if($same_as_company=='1')
								{$vessels->same_as_company=$same_as_company;
									$user_info= User::find($user_id);
									$vessels->company=$user_info->company;

								}else{
									$vessels->company=$company;
								}
								if($same_as_company=='1')
						{
							$vessels->same_as_company=$same_as_company;
							$user_info= User::find($request->input('user_id'));
							$vessels->company=$user_info->company;

						}else{
							$vessels->company=$company;
						}
						if($same_as_company_address=='1')
						 {$vessels->same_as_company_address=$same_as_company_address;
						 }
						// 	$user_info= User::find($user->id);
						// 	$vessels->address=$user_info->company_address;
						// }

						if(	$address!=""){
							$vessels->address = $address;
						}
						if(	$city!=""){
							$vessels->city = $city;
						}
						if(	$state!=""){
							$vessels->state = $state;
						}
						if(	$pincode!=""){
							$vessels->pincode = $pincode;
						}
								if(isset($image))
								{
									$imageName = time().$image->getClientOriginalName();
									$image->move(public_path().'/media/vessels', $imageName);
									$imageName =str_replace(" ", "", $imageName);
									$vessels->image = $imageName;
								}
								$vessels->save();
								
								$data1=array('id'=>(string)($vessels->id),'name'=>$vessels->name,
								'user_id'=>(string)($vessels->user_id),
								'imo_number'=>$vessels->imo_number,
								'company'=>!empty($vessels->company)?$vessels->company:'',
								'address'=>!empty($vessels->address)?$vessels->address:'',
								'city'=>!empty($vessels->city)?$vessels->city:'',
								'state'=>!empty($vessels->state)?$vessels->state:'',
								'pincode'=>!empty($vessels->pincode)?$vessels->pincode:'',
								'email'=>$vessels->email,
								'additional_email'=>!empty($vessels->additional_email)?$vessels->additional_email:''
								,'same_as_company'=>$vessels->same_as_company
								,'is_favourite'=>$vessels->favourite
								,'same_as_company_address'=>$vessels->same_as_company_address,
								'image'=>URL::to('/media/vessels').'/'.$vessels->image);
								$status=1;
								$message = 'Vessel - edited successfully…';
						}else{
							$status=0;
							$data1=(object)array();
							$message = 'Invalid Vessels.';
						}
					}
			}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
	   }
	   
	    public function OperatorEditProfile(Request $request)
	{
			$validator = Validator::make($request->all(), [
			//'id' => 'required',
			'user_id' => 'required|max:50',
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{

				$id =  (string)($request->input('user_id'));
				$first_name =  $request->input('first_name');
				$last_name =  $request->input('last_name');
				//$user_id =  (string)($request->input('user_id'));
				$email =  $request->input('email');
				$company =  $request->input('company');
				$address =  $request->input('address');
				//$company_name =  $request->input('company_name');
				$type =  $request->input('type');
				$company_website =  $request->input('company_website');
				$mobile =  $request->input('mobile');
				$country_id =  $request->input('country_id');

				$profile_pic = $request->file('profile_pic');
				$r=1;
					
					
						$users= User::where('id',$id)->first();
						if(!empty($users))
						{
								$users->first_name=$first_name;
								$users->last_name=$last_name;
								//$users->user_id=$user_id;
								$users->email=$email;
								if($company)
								{
								
									$users->company=$company;
								}
								$users->company_address=$address;
								if($country_id)
								{
									$users->country_id=$country_id;
								}
								if($type)
								{
								$users->type=$type;
								}
								if($company_website)
								{
								$users->company_website=$company_website;
								}
								if($mobile)
								{
								$users->mobile=$mobile;
								}
								if(isset($profile_pic))
								{
									$imageName = time().$profile_pic->getClientOriginalName();
									$profile_pic->move(public_path().'/media/users', $imageName);
									//$imageName =str_replace(" ", "", $imageName);
									$users->profile_pic = $imageName;
								} 
								$users->save();
								$address="";
								if(!empty( $users->street_address) || !empty( $users->state) || !empty( $users->city) || !empty( $users->pincode))
								{
									$address=$users->street_address.','.$users->state.','.$users->city;
								}	
								
								//echo '<pre>'; print_r($users); die;
								$data1=array('user_id'=>(string)($users->id),
								'first_name'=>$users->first_name,
								'last_name'=>$users->last_name,
								'country_code'=>$users->country_code,
								'email'=>$users->email,
								'company'=>$users->company,
								'address'=>$address,
								//'designation'=>$users->designation,
								'profile_pic'=>URL::to('/media/users').'/'.$users->profile_pic,
								'company_website'=> $company_website,
								'mobile_number'=>$users->mobile,
								'country_id'=> !empty($users->country_id) ? $users->country_id : '',
								'about_me'=> $users->about_me,
								'company_tax_id'=>$users->company_tax_id,
								'ssn'=>$users->ssn,
								'experience'=>$users->experience,
								'type'=>$users->type);
								if($users->country_id!=""){
									
									$countrydata = Countries::where('id',$users->country_id)->first();
									$data1['country_name'] = $countrydata->name;
								}
								$status=1;
								$message = 'User edited successfully.';
						}else{
							$status=0;
							$data1=(object)array();
							$message = 'Invalid User.';
						}
					
			}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
	   }
	   public function OperatorProfile(Request $request)
	{

		header('Content-Type: application/json');
				$status = 0;
				$message = NULL;
				$data = array();
				$data1 =array();
				$data_row 		= 	file_get_contents("php://input");
				$decoded 	    = 	json_decode($data_row, true);

			$validator = Validator::make($request->all(), [
			//'id' => 'required',
			'user_id' => 'required|max:50',
			]);
			if (empty($decoded['user_id'])) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{

			
				$r=1;
					
					
						$users= User::where('id',$decoded['user_id'])->first();
						if(!empty($users))
						{		
							$helper=new App\Helpers;  
							$survey_count = $helper->NoOfSurvey($users->id);

								if($users->conduct_custom=='1')
								{
									$first = DB::table('custom_survey_users')
									->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))
									->where('surveyors_id',$users->id);
									$responce = DB::table('survey_users')
									->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))
									->where('surveyors_id',$users->id)->union($first)->first();

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
							
											$responce=SurveyUsers::select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))
											->where('surveyors_id',$users->id)->first();
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
								$first = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$users->id);

								$total_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$users->id)->union($first)->count();

								$second = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$users->id) ->where('status','upcoming');

								$total_accept_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$users->id)->where('status','upcoming')->union($second)->count();
						
								

								if(!empty($total_accept_job) && !empty($total_job) ){
									$percentage_job_acceptance=floor($total_accept_job/$total_job*100);
								}	
								$address="";
								if(!empty( $users->street_address) || !empty( $users->state) || !empty( $users->city) || !empty( $users->pincode))
								{
									$address=$users->street_address.','.$users->state.','.$users->city;
								}
								$company=	$users->company;
								$address=$address;
								$street_address=$users->street_address;
								$state=$users->state;
								$city=$users->city;
								$pincode=$users->pincode;
								$mailing_address=$users->mailing_address;
								$company_website=$users->company_website;

								$designated_person="";
								if($users->type=="1" || $users->type=="3")
								{
									if($users->created_by!=""){

										$createdby =  User::select('users.*')->where('users.id',$users->created_by)->first(); 
										$designated_person=sprintf('%s %s',$createdby->first_name,$createdby->last_name);
										$designated_person_email=$createdby->email;


										if(!empty( $createdby->street_address) || !empty( $createdby->state) || !empty( $createdby->city) || !empty( $createdby->pincode))
										{
											$address=$createdby->street_address.','.$createdby->state.','.$createdby->city;
										}

										$company=	$users->company;

										$address=$address;
										$street_address=$createdby->street_address;
										$state=$createdby->state;
										$city=$createdby->city;
										$pincode=$createdby->pincode;
										$mailing_address=$createdby->mailing_address;
										$company_website=$createdby->company_website;
									}
								}
								//echo '<pre>'; print_r($users); die;
								$data1=array('user_id'=>(string)($users->id),
								'designated_person'=>$designated_person,
								'dp_email'=>!empty($designated_person_email) ? $designated_person_email : '',
								'first_name'=>$users->first_name,
								'last_name'=>$users->last_name,
								'country_code'=>$users->country_code,
								'email'=>$users->email,
								'company'=>!empty($users->company) ? $users->company : '',
								'address'=>$address,

								'street_address'=>!empty($street_address) ? $street_address : '',
								'state'=>!empty($state) ? $state : '',
								'city'=>!empty($city) ? $city : '',
								'pincode'=>!empty($pincode) ? $pincode : '',

								'mailing_address'=>!empty($mailing_address) ? $mailing_address : '',

								//'designation'=>$users->designation,
								'profile_pic'=>URL::to('/public/media/users').'/'.$users->profile_pic,
								'company_website'=> !empty($company_website) ? $company_website : '',
								'mobile_number'=>!empty($users->mobile) ? $users->mobile : '',
								'country_id'=> !empty($users->country_id) ? $users->country_id : '',
								'about_me'=>!empty( $users->about_me) ?  $users->about_me: '',
								'company_tax_id'=>!empty($users->company_tax_id) ? $users->company_tax_id: '',
								'ssn'=>!empty($users->ssn) ? $users->ssn : '',
								'experience'=>!empty($users->experience) ? $users->experience: '',
								'rating'=>!empty($users->rating) ? (string)$users->rating: '0',
								'total_no_of_survey'=>!empty($survey_count) ? $survey_count: '',
								'average_response_time'=>!empty($average_response_time) ? (string)$average_response_time : '',
								'percentage_job_acceptance'=>!empty($percentage_job_acceptance) ? (string)$percentage_job_acceptance.'%': '0%',
								
								
								'type'=>$users->type);
								if($users->country_id!=""){
									
									$countrydata = Countries::where('id',$users->country_id)->first();
									$data1['country_name'] = $countrydata->name;
								}
								$status=1;
								$message = 'User Profile .';
						}else{
							$status=0;
							$data1=(object)array();
							$message = 'Invalid User.';
						}
					
			}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
	   }
	   public function AddAgents(Request $request)
	{
			$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			'first_name' => 'required|max:50',
			'mobile' => 'required',
			'email' => 'required',
			
			
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{

				
				
				$user_id =  (string)($request->input('user_id'));
				$email =  $request->input('email');
				$first_name =  $request->input('first_name');
				
				$mobile =  $request->input('mobile');
				
				$image = $request->file('image');
				$r=1;
					$agent_mobile_count =  Agents::select('*')->where('mobile',$mobile)->count();
					if(!empty($agent_mobile_count )){
						$r=0;
						$status=0;
						$data1=(object)array();
						$message = 'Mobile Number Already Exist.';
					}
					$agent_email_count =  Agents::select('*')->where('email',$email)->count();
					if(!empty($agent_email_count )){
						$r=2;
						$status=0;
						$data1=(object)array();
						$message = 'Email Number Already Exist.';
					}

					if($r==1)
					{
						$agents= new Agents();
						$agents->user_id=$user_id;
						$agents->first_name=$first_name;
						
						$agents->email=$email;
						$agents->mobile=$mobile;
						
						if(isset($image))
						{
							$imageName = time().$image->getClientOriginalName();
							$image->move(public_path().'/media/agents', $imageName);
							$imageName =str_replace(" ", "", $imageName);
							$agents->image = $imageName;
						}
						$agents->save();
						 
						$data1=array('id'=>(string)($agents->id),'first_name'=>$agents->first_name,
						'last_name'=>$agents->last_name,'user_id'=>(string)($agents->user_id),
						'mobile'=>$agents->mobile,'email'=>$agents->email,'image'=>"");
						$status=1;
						$message = 'New agent - added successfully…';
					}
			}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
	   }
	    
	   public function AgentsList(Request $request)
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
				
				if(!empty($decoded['user_id']))
				{	
					$r=1;
					$user =  User::select('type')->where('id',$decoded['user_id'])->first();

					if($user->type=='0'){
						$createdbysurveyor =  User::select('id')->where('created_by',$decoded['user_id'])->get();
						$ids=array();
						if(!empty($createdbysurveyor)){
							foreach($createdbysurveyor as $data){
								$ids[]=$data->id;
							}
						}
							array_push($ids,$decoded['user_id']);
					
					}else
					{
								$createdbysurveyor =  User::select('created_by')->where('id',$decoded['user_id'])->first();
								$ids=array();
								if(!empty($createdbysurveyor)){
									
										$ids[]=$createdbysurveyor->created_by;
								}
									array_push($ids,$decoded['user_id']);
					}
			
			
					
						$agnets_data_count =  Agents::select('*')->whereIn('user_id',$ids)->orderBy('first_name','Asc')->count();
					
			
					if(empty($agnets_data_count)){
						$r=0;
					}
					if($r==1)
					{
						if($user->type=='0'){
							$createdbysurveyor =  User::select('id')->where('created_by',$decoded['user_id'])->get();
							$ids=array();
							if(!empty($createdbysurveyor)){
								foreach($createdbysurveyor as $data){
									$ids[]=$data->id;
								}
							}
								array_push($ids,$decoded['user_id']);
						
						}else
						{
									$createdbysurveyor =  User::select('created_by')->where('id',$decoded['user_id'])->first();
									$ids=array();
									if(!empty($createdbysurveyor)){
										
											$ids[]=$createdbysurveyor->created_by;
									}
										array_push($ids,$decoded['user_id']);
						}
				
				
						
							$agents_data =  Agents::select('*')->whereIn('user_id',$ids)->orderBy('first_name','Asc')->get();
				
						
						
						foreach($agents_data  as $agents )
						{
							$data1[]=array('id'=>(string)($agents->id),'first_name'=>$agents->first_name,
							'last_name'=>$agents->last_name,'user_id'=>(string)($agents->user_id),'mobile'=>$agents->mobile,
							'email'=>$agents->email,'image'=>"");

						}
						$status = 1;
						$message = 'Agents listing below.';
					}
					else{
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

   public function EditAgents(Request $request)
	{
			$validator = Validator::make($request->all(), [
			'id' => 'required',
			'user_id' => 'required|max:50',
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{
				$user_id =  (string)($request->input('user_id'));
				$id =  (string)($request->input('id'));
				$email =  $request->input('email');
				$first_name =  $request->input('first_name');
				$mobile =  $request->input('mobile');
				
				$image = $request->file('image');
				$r=1;
					$agent_mobile_count =  Agents::select('*')->where('mobile',$mobile)->where('id','!=',$id)->count();
					if(!empty($agent_mobile_count )){
						$r=0;
						$status=0;
						$data1=(object)array();
						$message = 'Mobile Number Already Exist.';
					}
					$agent_email_count =  Agents::select('*')->where('email',$email)->where('id','!=',$id)->count();
					if(!empty($agent_email_count )){
						$r=2;
						$status=0;
						$data1=(object)array();
						$message = 'Email Number Already Exist.';
					}

					if($r==1)
					{

						$vessels= Agents::where('id',$id)->first();
						if(!empty($vessels))
						{

								$agents= Agents::where('id',$id)->first();

								
								$agents->user_id=$user_id;
								$agents->first_name=$first_name;
								$agents->email=$email;
								$agents->mobile=$mobile;
								
								if(isset($image))
								{
									$imageName = time().$image->getClientOriginalName();
									$image->move(public_path().'/media/agents', $imageName);
									$imageName =str_replace(" ", "", $imageName);
									$agents->image = $imageName;
								}
								$agents->save();
								
								$data1=array('id'=>(string)($agents->id),'first_name'=>$agents->first_name,
								'last_name'=>$agents->last_name,'user_id'=>(string)($agents->user_id)
								,'mobile'=>$agents->mobile,'email'=>$agents->email,'image'=>"");
								$status=1;
								$message = 'Agent - edited successfully…';
							}else{
								$status=0;
							$data1=(object)array();
								$message = 'Invalid data.';
							}
						}
			}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
	   }

	public function AddOperators(Request $request)
	{       $data = array();
			$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			'email' => 'required',
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{
				
				$user_id =  (string)($request->input('user_id'));
				$email =  $request->input('email');
			
				
				$r=1;
					
					$operator_email_count =  User::select('*')->where('email',$email)->count();
					if(!empty($operator_email_count )){
						$r=2;
						$status=0;
						$data=(object)array();
						$message = 'Email Id Already Exist.';
					}

					if($r==1)
					{
						$userArray= new User();
						$userArray->created_by=$user_id;
						$userArray->email=$email;
						$usertypecheck =  User::select('*')->where('id',$user_id)->first();
						if($usertypecheck->type=='2')
						{
							$userArray->type='3';
						}else{
							$userArray->type='1';
						}
						
						
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
								$data['type'] = !empty($userArray->type) ? $userArray->type: '';								
								$data['country_code'] = !empty($userArray->country_code) ? $userArray->country_code: '';
								$data['country_id'] = !empty($userArray->country_id) ? $userArray->country_id : '';
								if($userArray->country_id!=""){
									
									$countrydata = Countries::where('id',$userArray->country_id)->first();
									$data['country_name'] = !empty($countrydata->name) ? $countrydata->name : '' ;
								}
								

								$data['mobile_number'] = !empty($userArray->mobile) ? $userArray->country_code.$userArray->mobile : '';
															
								//$data['address'] = $userArray->address ? $userArray->address : 'New York';
								$data['company'] =!empty( $userArray->company) ?  $userArray->company : '';
								$data['address'] = !empty($userArray->company_address)? $userArray->company_address : 'New York';
								$data['company_tax_id'] = !empty( $userArray->company_tax_id) ?  $userArray->company_tax_id : '';
								$data['company_website'] = !empty( $userArray->company_website) ?  $userArray->company_website : '';
								$data['ssn'] = !empty( $userArray->ssn) ? $userArray->ssn : '';
								$data['about_me'] =!empty( $userArray->about_me) ? $userArray->about_me : '' ;
								$data['experience'] = !empty( $userArray->experience) ? $userArray->experience : '';
								$data['email_verify'] =!empty(  $userArray->email_verify) ?  $userArray->email_verify : '0';
								$data['is_signup'] =!empty(  $userArray->is_signup) ?  $userArray->is_signup : '0';

								

								if($userArray->profile_pic &&  file_exists(public_path('/media/users/').$userArray->profile_pic ))
									{
										$data['profile_pic'] = url('/').'/media/operators/'.$userArray->profile_pic;
									}else{
										$data['profile_pic'] ='';
									}
									if($usertypecheck->type=='2')
									{
										$create_url = \App::make('url')->to('/bycompany-surveyor-signup')."/".base64_encode($userArray->id);


									}else{
										$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

									}
									// $create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

									// $data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));

									// 	Mail::send( 'pages.email.add_operator_email_verify_for_signup',$data1, function( $message ) use ($data1)
									// 	{
									// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

									// 	});
									$emailData = Emailtemplates::where('slug','=','surveyor-add-by-dp')->first();

						if($emailData){
							$textMessage = $emailData->description;
							$subject = $usertypecheck->first_name.''.$usertypecheck->last_name." has added you to iMarS!";
							$to =$email;

							if($email!='')
							{ $dpname=$usertypecheck->first_name.$usertypecheck->last_name;
								$textMessage = str_replace(array('COMPANY_NAME','DP_NAME','SIGNUP_LINK'), 
								array($userArray->company,$dpname,$create_url),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}
		    
						$status=1;
						if($usertypecheck->type=='2'){
							$message = 'You have added a new surveyor An email is sent to the new surveyor to sign up. ';
						}else{
							$message = 'You have added a new operator… An email is sent to the new operator to sign up. ';
						}
						

					}
			}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data);
					echo json_encode(array('response' => $response_data));
					die;
	   }
	   public function ResendAddOperators(Request $request)
	{       $data = array();
			$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			'email' => 'required',
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{$status=0;
				
				$user_id =  (string)($request->input('user_id'));
				$email =  $request->input('email');
				$userArray =  User::select('*')->where('email',$email)->first();
			if(!empty($userArray ))
			{
				$data['user_id'] = (string)$userArray->id;
					$data['first_name'] = !empty($userArray->first_name) ? $userArray->first_name: '';
					$data['last_name'] = !empty($userArray->last_name) ? $userArray->last_name: '';
					$data['email'] = $userArray->email;
					/*if($userArray->type=="0" || $userArray->type=="1" ){
						$data['type'] = 'operator';
					}else{
						$data['type'] = 'surveyor';
					}*/
					$data['type'] = !empty($userArray->type) ? $userArray->type: '';								
					$data['country_code'] = !empty($userArray->country_code) ? $userArray->country_code: '';
					$data['country_id'] = !empty($userArray->country_id) ? $userArray->country_id : '';
					if($userArray->country_id!=""){
						
						$countrydata = Countries::where('id',$userArray->country_id)->first();
						$data['country_name'] = !empty($countrydata->name) ? $countrydata->name : '' ;
					}
					

					$data['mobile_number'] = !empty($userArray->mobile) ? $userArray->country_code.$userArray->mobile : '';
												
					//$data['address'] = $userArray->address ? $userArray->address : 'New York';
					$data['company'] =!empty( $userArray->company) ?  $userArray->company : '';
					$data['address'] = !empty($userArray->company_address)? $userArray->company_address : 'New York';
					$data['company_tax_id'] = !empty( $userArray->company_tax_id) ?  $userArray->company_tax_id : '';
					$data['company_website'] = !empty( $userArray->company_website) ?  $userArray->company_website : '';
					$data['ssn'] = !empty( $userArray->ssn) ? $userArray->ssn : '';
					$data['about_me'] =!empty( $userArray->about_me) ? $userArray->about_me : '' ;
					$data['experience'] = !empty( $userArray->experience) ? $userArray->experience : '';
					$data['email_verify'] =!empty(  $userArray->email_verify) ?  $userArray->email_verify : '0';
					$data['is_signup'] =!empty(  $userArray->is_signup) ?  $userArray->is_signup : '0';

					

					if($userArray->profile_pic &&  file_exists(public_path('/media/users/').$userArray->profile_pic ))
						{
							$data['profile_pic'] = url('/').'/media/operators/'.$userArray->profile_pic;
						}else{
							$data['profile_pic'] ='';
						}
						$usertypecheck =  User::select('*')->where('id',$user_id)->first();
						if($usertypecheck->type=='2')
						{
							$create_url = \App::make('url')->to('/bycompany-surveyor-signup')."/".base64_encode($userArray->id);


						}else{
							$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

						}
						$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

						// $data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));

						// 	Mail::send( 'pages.email.add_operator_email_verify_for_signup',$data1, function( $message ) use ($data1)
						// 	{
						// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

						// 	});

						$emailData = Emailtemplates::where('slug','=','surveyor-add-by-dp')->first();

						if($emailData){
							$textMessage = $emailData->description;
							$subject = $usertypecheck->first_name.''.$usertypecheck->last_name." has added you to iMarS!";
							$to =$email;

							if($email!='')
							{ $dpname=$usertypecheck->first_name.$usertypecheck->last_name;
								$textMessage = str_replace(array('COMPANY_NAME','DP_NAME','SIGNUP_LINK'), 
								array($userArray->company,$dpname,$create_url),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}


						
							$status=1;
							$usertypecheck =  User::select('*')->where('id',$user_id)->first();
						if($usertypecheck->type=='2')
						{
							$message = 'A new email has been sent to the surveyor to sign up. ';
						}else{
							$message = 'A new email has been sent to the operator to sign up. ';
						}

							

			     }else{
					$status=0;
					$message = 'Please  use registered email Id.';
				 }
			
					
				}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data);
					echo json_encode(array('response' => $response_data));
					die;
	   }

	   public function EditOperators(Request $request)
	{     
			$email =  $request->input('email');
			$user_id =  $request->input('user_id');
			$edit_id =  $request->input('edit_id');

			$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			'edit_id' => 'required',
			'email' => 'required|email',
			
			]);
			if ($validator->fails()) 
			{
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{
				$r=1;
				$agent_email_count =  User::select('*')->where('email',$email)->where('id','!=',$edit_id)->count();
					if(!empty($agent_email_count )){
						$r=2;
						$status=0;
						$data2=(object)array();
						$message = 'Email Id Already Exist.';
					}

					if($r==1)
					{
				
				       $operators= User::where('id',$edit_id)->first();
						if(!empty($operators))
						{
					           $userArray= User::where('id',$edit_id)->first();
								if($userArray->email!=$email)
								{
									$userArray->email_verify='0';

								}
								
									$userArray->email=$email;
									$userArray->save();
						
						        $data2['user_id'] = (string)$userArray->id;
								$data2['first_name'] = !empty($userArray->first_name) ? $userArray->first_name: '';
								$data2['last_name'] = !empty($userArray->last_name) ? $userArray->last_name: '';
								$data2['email'] = $userArray->email;
								
								$data2['type'] = !empty($userArray->type) ? $userArray->type: '';								
								$data2['country_code'] = !empty($userArray->country_code) ? $userArray->country_code: '';
								$data2['country_id'] = !empty($userArray->country_id) ? $userArray->country_id : '';
								if($userArray->country_id!=""){
									
									$countrydata = Countries::where('id',$userArray->country_id)->first();
									$data2['country_name'] = !empty($countrydata->name) ? $countrydata->name : '' ;
								}
								

								$data2['mobile_number'] = !empty($userArray->mobile) ? $userArray->country_code.$userArray->mobile : '';
															
								//$data['address'] = $userArray->address ? $userArray->address : 'New York';
								$data2['company'] =!empty( $userArray->company) ?  $userArray->company : '';
								$data2['address'] = !empty($userArray->company_address)? $userArray->company_address : 'New York';
								$data2['company_tax_id'] = !empty( $userArray->company_tax_id) ?  $userArray->company_tax_id : '';
								$data2['company_website'] = !empty( $userArray->company_website) ?  $userArray->company_website : '';
								$data2['ssn'] = !empty( $userArray->ssn) ? $userArray->ssn : '';
								$data2['about_me'] =!empty( $userArray->about_me) ? $userArray->about_me : '' ;
								$data2['experience'] = !empty( $userArray->experience) ? $userArray->experience : '';
								$data2['email_verify'] =!empty(  $userArray->email_verify) ?  $userArray->email_verify : '0';
								$data2['is_signup'] =!empty(  $userArray->is_signup) ?  $userArray->is_signup : '0';

								

								if($userArray->profile_pic &&  file_exists(public_path('/media/users/').$userArray->profile_pic ))
									{
										$data2['profile_pic'] = url('/').'/media/operators/'.$userArray->profile_pic;
									}else{
										$data2['profile_pic'] ='';
									}
									$usertypecheck =  User::select('*')->where('id',$user_id)->first();
									if($usertypecheck->type=='2')
									{
										$create_url = \App::make('url')->to('/bycompany-surveyor-signup')."/".base64_encode($userArray->id);


									}else{
										$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

									}
									// $data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));

									// 	Mail::send( 'pages.email.add_operator_email_verify_for_signup',$data1, function( $message ) use ($data1)
									// 	{
									// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

									// 	});

										$emailData = Emailtemplates::where('slug','=','surveyor-add-by-dp')->first();

						if($emailData){
							$textMessage = $emailData->description;
							$subject = $usertypecheck->first_name.''.$usertypecheck->last_name." has added you to iMarS!";
							$to =$email;

							if($email!='')
							{ $dpname=$usertypecheck->first_name.$usertypecheck->last_name;
								$textMessage = str_replace(array('COMPANY_NAME','DP_NAME','SIGNUP_LINK'), 
								array($userArray->company,$dpname,$create_url),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}

									$status=1;
									$usertypecheck =  User::select('*')->where('id',$user_id)->first();
									if($usertypecheck->type=='2')
									{
										$message = 'Surveyor – edited successfully. ';
									}else{
										$message = 'Operator – edited successfully, and an email has been sent to the operator! ';
									}
									
									
						}else{
							$status=0;
							$data2=(object)array();
							$message = 'Invalid data.';
						}
					}
			}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data2);
					echo json_encode(array('response' => $response_data));
					die;
	   }

	   public function OperatorsList(Request $request)
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
					
					if(!empty($decoded['user_id']))
					{	
						$r=1;
						$Operators_data_count =  User::select('*')->where('created_by',$decoded['user_id'])->where('status','!=','0')->count();
						if(empty($Operators_data_count)){
							$r=0;
						}
						if($r==1)
						{
							$operators_data =  User::select('*')->where('created_by',$decoded['user_id'])->where('status','!=','0')
							->orderBy('first_name','asc')
		->orderBy('last_name','asc')->get();
							foreach($operators_data  as $userArray )
							{
								$data['user_id'] = (string)$userArray->id;
								$data['first_name'] = !empty($userArray->first_name) ? $userArray->first_name: '';
								$data['last_name'] = !empty($userArray->last_name) ? $userArray->last_name: '';
								$data['email'] = $userArray->email;
								/*if($userArray->type=="0" || $userArray->type=="1" ){
									$data['type'] = 'operator';
								}else{
									$data['type'] = 'surveyor';
								}*/
								$data['type'] = !empty($userArray->type) ? $userArray->type: '';								
								// $data['country_code'] = !empty($userArray->country_code) ? $userArray->country_code: '';
								$data['country_id'] = !empty($userArray->country_id) ? $userArray->country_id : '';
								if($userArray->country_id!=""){
									
									$countrydata = Countries::where('id',$userArray->country_id)->first();
									$data['country_name'] = !empty($countrydata->name) ? $countrydata->name : '' ;
								}
								

								$data['mobile_number'] = !empty($userArray->mobile) ? $userArray->country_code.$userArray->mobile : '';
															
								//$data['address'] = $userArray->address ? $userArray->address : 'New York';
								if($userArray->status=='2' || $userArray->status=='0')
								{
												$status='0';
								}else{
									
									$status='1';
								}
								$data['company'] =!empty( $userArray->company) ?  $userArray->company : '';
								$data['address'] = !empty($userArray->company_address)? $userArray->company_address : 'New York';
								$data['company_tax_id'] = !empty( $userArray->company_tax_id) ?  $userArray->company_tax_id : '';
								$data['company_website'] = !empty( $userArray->company_website) ?  $userArray->company_website : '';
								$data['ssn'] = !empty( $userArray->ssn) ? $userArray->ssn : '';
								$data['about_me'] =!empty( $userArray->about_me) ? $userArray->about_me : '' ;
								$data['experience'] = !empty( $userArray->experience) ? $userArray->experience : '';
								$data['email_verify'] =$status;
								$data['is_signup'] =$status;

								if($userArray->profile_pic &&  file_exists(public_path('/media/users/').$userArray->profile_pic ))
								{
									$data['profile_pic'] = url('/').'/media/operators/'.$userArray->profile_pic;
								}else{
									$data['profile_pic'] ='';
								}

								$data1[]=$data;
						}
							$status = 1;
							$message = 'Operators listing below.';
						}
						else{
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

	public function DeletedSurveyor(Request $request)
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
				
				if(!empty($decoded['user_id']))
				{	
					$r=1;
					$Operators_data_count =  User::select('*')->where('created_by',$decoded['user_id'])->where('status','0')->where('type','3')->count();
					if(empty($Operators_data_count)){
						$r=0;
					}
					if($r==1)
					{
						$operators_data = User::select('*')->where('created_by',$decoded['user_id'])->where('status','0')->where('type','3')->get();
						foreach($operators_data  as $userArray )
						{
							$data['user_id'] = (string)$userArray->id;
							$data['first_name'] = !empty($userArray->first_name) ? $userArray->first_name: '';
							$data['last_name'] = !empty($userArray->last_name) ? $userArray->last_name: '';
							$data['email'] = $userArray->email;
							/*if($userArray->type=="0" || $userArray->type=="1" ){
								$data['type'] = 'operator';
							}else{
								$data['type'] = 'surveyor';
							}*/
							$data['type'] = !empty($userArray->type) ? $userArray->type: '';								
							$data['country_code'] = !empty($userArray->country_code) ? $userArray->country_code: '';
							$data['country_id'] = !empty($userArray->country_id) ? $userArray->country_id : '';
							if($userArray->country_id!=""){
								
								$countrydata = Countries::where('id',$userArray->country_id)->first();
								$data['country_name'] = !empty($countrydata->name) ? $countrydata->name : '' ;
							}
							

							$data['mobile_number'] = !empty($userArray->mobile) ? $userArray->mobile : '';
														
							//$data['address'] = $userArray->address ? $userArray->address : 'New York';
							$data['company'] =!empty( $userArray->company) ?  $userArray->company : '';
							$data['address'] = !empty($userArray->company_address)? $userArray->company_address : 'New York';
							$data['company_tax_id'] = !empty( $userArray->company_tax_id) ?  $userArray->company_tax_id : '';
							$data['company_website'] = !empty( $userArray->company_website) ?  $userArray->company_website : '';
							$data['ssn'] = !empty( $userArray->ssn) ? $userArray->ssn : '';
							$data['about_me'] =!empty( $userArray->about_me) ? $userArray->about_me : '' ;
							$data['experience'] = !empty( $userArray->experience) ? $userArray->experience : '';
							$data['email_verify'] =!empty(  $userArray->email_verify) ?  $userArray->email_verify : '0';
							$data['is_signup'] =!empty(  $userArray->is_signup) ?  $userArray->is_signup : '0';

							if($userArray->profile_pic &&  file_exists(public_path('/media/users/').$userArray->profile_pic ))
							{
								$data['profile_pic'] = url('/').'/media/operators/'.$userArray->profile_pic;
							}else{
								$data['profile_pic'] ='';
							}

							$data1[]=$data;
					}
						$status = 1;
						$message = 'Deleted Operators listing below.';
					}
					else{
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

	
	public function DeleteOperators(Request $request)
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
					
					if(!empty($decoded['user_id']) && !empty($decoded['operator_id']))
					{	
						$r=1;
						$Operators_data_count =  User::select('*')->where('created_by',$decoded['user_id'])->where('id',$decoded['operator_id'])->count();
						if(empty($Operators_data_count)){
							$r=0;
						}
						if($r==1)
						{
							$operators_data =  User::select('*')->where('created_by',$decoded['user_id'])->where('id',$decoded['operator_id'])->first();
							$operators_data->status='0';
							$operators_data->save();
							$status = 1;
							$message = 'Operators Deleted Successfully.';
						}
						else{
							$message = 'Data not found.';
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
	
	 public function SurveyUsers(Request $request)
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
					
					if(!empty($decoded['survey_category_id']) && !empty($decoded['port_id']) 
					&& !empty($decoded['start_date']) && !empty($decoded['end_date']))
					{	
						$r=1;
						
						if($decoded['survey_category_id']=="31")
						{
							$surveyor_data_count = DB::table('users')->select('users.*')
								->leftJoin('events', 'events.user_id', '=', 'users.id')
								->leftJoin('users_port', 'users_port.user_id', '=', 'users.id')
								->where('users_port.port_id',$decoded['port_id'])
								->where('users.status','1')
								->where('users.is_avail','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','2' )
												->orWhere('users.type', '=', '4');
										})
							->where('users.conduct_custom', '=', '1')
							->groupBy('users.id')->count();
						}else
						{
							
							$surveyor_data_count = DB::table('users')->select('users.*',
							'users_survey_price.survey_price','users_port.cost','users_survey_price.type as price_type')
								->leftJoin('users_survey_price', 'users_survey_price.user_id', '=', 'users.id')
								->leftJoin('users_port', 'users_port.user_id', '=', 'users.id')
								->where('users_survey_price.survey_type_id',$decoded['survey_category_id'])
								->where('users_port.port_id',$decoded['port_id'])
								->where('users.status','1')
								->where('users.is_avail','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','2' )
												->orWhere('users.type', '=', '4');
										})->count();
						

						}				 	
						


						//$surveyor_data_count =  User::select('*')->where('survey_category_id',$decoded['survey_category_id'])->count();
						if(empty($surveyor_data_count)){
							$r=0;
						}
						if($r==1)
						{
						
						if($decoded['survey_category_id']=="31")
						{
							$surveyor_data = DB::table('users')->select('users.*')
								->leftJoin('events', 'events.user_id', '=', 'users.id')
								->leftJoin('users_port', 'users_port.user_id', '=', 'users.id')
								->where('users_port.port_id',$decoded['port_id'])
								->where('users.status','1')
								->where('users.is_avail','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','2' )
												->orWhere('users.type', '=', '4');
										})
							->where('users.conduct_custom', '=', '1')
							->groupBy('users.id')
								->get();
						}else
						{
							DB::enableQueryLog(); // Enable query log
							$surveyor_data = DB::table('users')->select('users.*',
							'users_survey_price.survey_price','users_port.cost','users_survey_price.type as price_type')
								->leftJoin('users_survey_price', 'users_survey_price.user_id', '=', 'users.id')
								->leftJoin('users_port', 'users_port.user_id', '=', 'users.id')
								->where('users_survey_price.survey_type_id',$decoded['survey_category_id'])
								->where('users_port.port_id',$decoded['port_id'])
								->where('users.status','1')
								->where('users.is_avail','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','2' )
												->orWhere('users.type', '=', '4');
										})
										->orderBy('rating','Desc')
										->get();
						
						//dd(DB::getQueryLog()); // Show results of log
						}	
					//	dd($surveyor_data );
							foreach($surveyor_data  as $surveyor )
							{
								$user_id=$surveyor->id;
								$ids=array();
								$createdbysurveyor =  User::select('id')->where('created_by',$user_id)
								  ->where('email_verify','1')->where('is_signup','1')
								  ->get(); 
								  //dd($createdbysurveyor );
								  
								   if(!empty($createdbysurveyor))
								   {
									   foreach($createdbysurveyor as $sdata){
										   $ids[]=$sdata->id;
										}
								   }
								  array_push($ids,$user_id);  
								  $users_count=count($ids);
									//print_r($ids);
									$surveyor_event_data = DB::table('events')->select('events.*')
								  ->where('events.start_event',$decoded['start_date'])
								  ->whereIn('events.user_id',$ids)
								  ->where('events.title','0')
								 ->count();

								if( $surveyor_event_data!=$users_count)
								{
									
									if($surveyor->conduct_custom=='1')
									{
										$first = DB::table('custom_survey_users')->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))->where('surveyors_id',$surveyor->id);
										$responce = DB::table('survey_users')->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))->where('surveyors_id',$surveyor->id)->union($first)->first();
   
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
								
												 $responce=SurveyUsers::select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))
												->where('surveyors_id',$surveyor->id)->first();
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
									
									$first = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$surveyor->id);
		
									$total_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$surveyor->id)->union($first)->count();
		
									$second = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$surveyor->id) ->where('status','upcoming');
		
									$total_accept_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$surveyor->id)->where('status','upcoming')->union($second)->count();
							
									$helper=new Helpers;
									$pricing1=$helper->SurveyorPriceDetail($decoded['survey_category_id'],$surveyor->id);						
									$pricing2=$helper->SurveyorPortPrice($decoded['port_id'],$surveyor->id);
									$pricing="0";
									$pricing="0";
									if($pricing1!="")
									{
										$pricing1=!empty($pricing1)?$pricing1:'0';
										$pricing=$pricing1;
									}	
									$company="";
									if($surveyor->type=='2'){
									$company=$surveyor->company;
									}
   
									if(!empty($total_accept_job) && !empty($total_job) ){
										$percentage_job_acceptance=floor($total_accept_job/$total_job*100);
									}										

									

									$data1[]=array('id'=>(string)$surveyor->id,
										'first_name'=>(string)$surveyor->first_name,
										'last_name'=>(string)$surveyor->last_name,
										'about_me'=>(string)$surveyor->about_me,
										'experience'=>(string)$surveyor->experience,
										'company'=>(string)$company,
										'mobile'=>(string)$surveyor->mobile,
										'pricing'=>!empty($pricing) ? (string)$pricing : '0',
										'port_price'=>!empty($pricing2) ? (string)$pricing2 : '0',
										'rating'=>!empty($surveyor->rating) ? (string)$surveyor->rating : '0',
										'average_response_time'=>!empty($average_response_time) ? (string)$average_response_time : '',
										'percentage_job_acceptance'=>!empty($percentage_job_acceptance) ? (string)$percentage_job_acceptance: '',
										'email'=>$surveyor->email,
										'price_type'=>!empty($surveyor->price_type) ? (string)$surveyor->price_type : '',
										'image'=>!empty($surveyor->profile_pic)  ? URL::to('/public/media/users').'/'.$surveyor->profile_pic :""
									);
									}
							
								}
						
						
							$status = 1;
							$message = 'Surveyor listing below.';
						}
						else{
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

	

	public function editagentinsurvey(Request $request)
	{     //echo '<pre>'; print_r($request->all()); die;header('Content-Type: application/json');
		$status = 0;
		$message = NULL;
		$data = array();
		$data1 =array();
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);
		if($decoded) 
		{
				
			if (empty($decoded['survey_id']) || empty($decoded['agent_id'])  || empty($decoded['user_id']))
			{
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{

				
				$survey=  Survey::where('user_id',$decoded['user_id'])->where('id',$decoded['survey_id'])->first();
				$r=1;
				if(empty($survey)){
						$r=0;
						$status='0';
				}

				if($r=='1'){
					$survey->agent_id=$decoded['agent_id'];
					$survey->save();
					$status=1;
					$message = 'Survey agent update successfully.';
				}
				else{
					$message = 'No survey Found.';
				}
			    
					
			}
		}else 
		{
			$message = 'Opps! Something went wrong. Please try again.';
		}
					$response_data = array('status'=>$status,'message'=>$message);
					echo json_encode(array('response' => $response_data));
					die;
	   }
	public function requestSurvey(Request $request)
	{     //echo '<pre>'; print_r($request->all()); die;
			$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			'agent_id' => 'required',
			'port_id' => 'required',
			'ship_id' => 'required|',
			'start_date' => 'required',
			'end_date' => 'required',
			'survey_type_id' => 'required',
			'surveyors_id' => 'required',
			'status' => 'required'
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{

				$user_id =  $request->input('user_id');
				$agent_id =  $request->input('agent_id');
				$port_id =  $request->input('port_id');
				$ship_id =  $request->input('ship_id');
				$start_date =  $request->input('start_date');
				$end_date =  $request->input('end_date');
				$survey_type_id =  $request->input('survey_type_id');
				$surveyors_id =  $request->input('surveyors_id');
				$instruction =  !empty($request->input('instruction')) ? $request->input('instruction') : '';
				$status =  $request->input('status');
				//$last_status =  $request->input('last_status');
				$file_data = $request->file('file_data');
				$country_data=  Port::select('country_id')->where('id',$port_id)->first();

						$survey= new Survey();
						$survey->user_id=$user_id;
						$survey->assign_to_op=$user_id;
						$survey->agent_id=$agent_id;
						$survey->port_id=$port_id;
						$survey->country_id=$country_data->country_id;
						
						$survey->ship_id=$ship_id;
						$survey->start_date=$start_date;
						$survey->end_date=$end_date;
						$survey->survey_type_id=$survey_type_id;
						$survey->surveyors_id=$surveyors_id;
						$survey->instruction=$instruction;
						$survey->status=$status;
						//$survey->last_status=$last_status;
						$cdate=date("ymd");
						$las_survey_id =  Survey::select('*')->orderBy('id','desc')->first();
						$lastsurveynumber= $las_survey_id->survey_number;
						$prev_survey_digit= substr($lastsurveynumber , -4);
						$var=0001;
						$final=$prev_survey_digit+$var;
						
						$survey->survey_number =  $cdate.$final;						
						if(isset($file_data))
						{
							$imageName = time().$file_data->getClientOriginalName();
							$file_data->move(public_path().'/media/survey', $imageName);
							$imageName =str_replace(" ", "", $imageName);
							$survey->file_data = $imageName;
						}
						$survey->save();
						
						$usersdata = explode(",",$request->input('surveyors_id'));
						$usercount = count($usersdata);
						if($usercount == 1)
						{
							$uservalue = array('1');
						}else if($usercount == 2)
						{
							$uservalue = array('1','2');					
						}else{
							$uservalue = array('1','2','3');
					
						}
						$usersdata1 = array_combine($usersdata,$uservalue);
						foreach($usersdata1 as $key => $value)
						{
							$surveyuser= new SurveyUsers();
							$surveyuser->surveyors_id = $key;
							$surveyuser->type = $value;
							$surveyuser->survey_id = $survey->id;
							if($value=='1'){
								$status="pending";
								$helper=new Helpers;
								$surveyor_data=  Survey::find($survey->id);
									$survey_price=$helper->SurveyorPriceDetail($surveyor_data->survey_type_id,$key);
									if($survey_price){
										$surveyor_data->survey_price=$survey_price;
									}
									$port_price=$helper->SurveyorPortPrice($surveyor_data->port_id,$key);
									if($port_price){
										$surveyor_data->port_price=$port_price;
									}
									$surveyor_data->save();

							}
							if($value=='2'){
                                $status="waiting";
							}
							if($value=='3'){
                                $status="waiting";
							}
							$surveyuser->status = $status;
							$surveyuser->save();
							
						}
					
										$message_token =  SurveyUsers::select('users.id','users.email','users.first_name','users.device_id',
										'users.type','users.country_id')
										->leftJoin('users', 'survey_users.surveyors_id', '=', 'users.id')
										->where('survey_users.survey_id',$survey->id)
										->where('survey_users.type','1')
										->first();
										//echo $message_token->id;exit;
										$helper=new Helpers;
										$helper->SendNotification($message_token->device_id,'New survey request received!','New Survey Request Received. Please accept within 8 hours, or the request will be cancelled.');
										
										$notification = new Notification();
										$notification->user_id = $message_token->id;
										$notification->title = 'New survey request received!';
										$notification->noti_type = 'New survey request received!';
										$notification->user_type = $message_token->type;
										$notification->notification = 'New Survey Request Received. Please accept within 8 hours, or the request will be cancelled.';
										$notification->country_id = $message_token->country_id;
										$notification->is_read = 0;
										$notification->save();

										$emailData = Emailtemplates::where('slug','=','appoint-survey')->first();
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
										$status=1;
										$message = 'Your request has been sent and is now listed in Pending tab…';
									
			}
					$response_data = array('status'=>$status,'message'=>$message);
					echo json_encode(array('response' => $response_data));
					die;
	   }

	public function SurveyAcceptReject(Request $request)
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
				
			if(!empty($decoded['surveyors_id']) && !empty($decoded['survey_id']) && !empty($decoded['type']))
			{
				$survey_users =  SurveyUsers::select('survey_users.*')
				->where('survey_users.survey_id',$decoded['survey_id'])
				->where('survey_users.surveyors_id',$decoded['surveyors_id'])
				->first();
				
				$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])->first();
				//dd($survey_users);
				if(!empty($surveyor_data) && !empty($survey_users))
				{

					if($surveyor_data->assign_to_op!="" || $surveyor_data->assign_to_op!="0"){
						$operator_id=$surveyor_data->assign_to_op;
					}else{
						$operator_id=$surveyor_data->user_id;
	
					}

					$operator_token =  User::select('users.id','users.email','users.first_name','users.device_id',
					'users.type','users.country_id')->where('id',$operator_id)->first(); 
					if($decoded['type']=="accept")
					{   $survey_users->status="upcoming";
						$survey_users->accept_date=date("Y-m-d H:i:s");

						$surveyor_data->status='1';
						$surveyor_data->accept_by=$decoded['surveyors_id'];
						$surveyor_data->save();
						$helper=new Helpers;
						$helper->SendNotification($operator_token->device_id,'Your survey request accepted!','Your Survey request has been accepted by the surveyor. You can manage it now in your upcoming surveys tab.');
	
						$notification = new Notification();
						$notification->user_id = $operator_token->id;
						$notification->title = 'Your survey request accepted!';
						$notification->noti_type = 'Your survey request accepted!';
						$notification->user_type = $operator_token->type;
						$notification->notification = 'Your Survey request has been accepted by the surveyor. You can manage it now in your upcoming surveys tab.';
						$notification->country_id = $operator_token->country_id;
						$notification->is_read = 0;
						$notification->save();

						$emailData = Emailtemplates::where('slug','=','accept-survey')->first();

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
					}else
					{
						$survey_users->status="declined";

						// $survey_users->status="cancelled";
						
						// $survey_usersc =  SurveyUsers::select('survey_users.*')
						// ->where('survey_users.survey_id',$decoded['survey_id'])
						// ->count();
						// if($survey_usersc=='1')
						// {
						// 	$surveyor_data->status='2';
												
						// 	$surveyor_data->save(); 
						// }
						// $survey_usersd =  SurveyUsers::select('survey_users.*')
						// 	->where('survey_users.survey_id',$decoded['survey_id'])
						// 	->where('survey_users.surveyors_id',$decoded['surveyors_id'])
						// 	->first();

						// $survey_usersd->delete();
						

						$helper=new Helpers;
						$helper->SendNotification($operator_token->device_id,'Survey Request Declined','One of the surveyors you selected for the survey has declined the request. If all your selected surveyors decline the request, the survey will be listed in Cancelled surveys tab.');
						$notification = new Notification();
						$notification->user_id = $operator_token->id;
						$notification->title = 'Survey Request Declined';
						$notification->noti_type = 'Survey Request Declined';
						$notification->user_type = $operator_token->type;
						$notification->notification = 'One of the surveyors you selected for the survey has declined the request. If all your selected surveyors decline the request, the survey will be listed in Cancelled surveys tab.';
						$notification->country_id = $operator_token->country_id;
						$notification->is_read = 0;
						$notification->save();
						
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
						$survey_users_next = SurveyUsers::select('survey_users.*')->where('id', '>', $survey_users->id)->where('survey_users.survey_id',$decoded['survey_id'])->first();
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

							$message_token =  User::select('users.id','users.email','users.first_name','users.device_id','users.type','users.country_id')->where('id',$survey_users_next->surveyors_id)->first(); 
							$helper=new Helpers;
							
							$helper->SendNotification($message_token->device_id,'New survey request received!','New Survey Request Received. Please accept within 8 hours, or the request will be cancelled');


							$notification = new Notification();
							$notification->user_id = $message_token->id;
							$notification->title = 'New survey request received!';
							$notification->noti_type = 'New survey request received!';
							$notification->user_type = $message_token->type;
							$notification->notification = 'New Survey Request Received. Please accept within 8 hours, or the request will be cancelled.';
							$notification->country_id = $message_token->country_id;
							$notification->is_read = 0;
							$notification->save();

							$emailData = Emailtemplates::where('slug','=','appoint-survey')->first();

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
						$survey_users->save();
						$SurveyUsers =new SurveyUsers;
						$Surveyc= $SurveyUsers->where('survey_id',$decoded['survey_id'])->count();
								
						$Surveydeclinec= $SurveyUsers->where('survey_id',$decoded['survey_id'])
						->where('status','declined')
						->count();
									 
						if($Surveyc==$Surveydeclinec)
						{
							$surveyor_data->declined='1';
							$surveyor_data->active_thread='0';
							$surveyor_data->save(); 
						}
						
						
						$surveyor =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
							DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
							DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
							DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
							'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
							'vessels.name as vesselsname')
								->leftJoin('port', 'port.id', '=', 'survey.port_id')
								->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
								->leftJoin('users', 'users.id', '=', 'survey.user_id')
								->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
								->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
								->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
								->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
								->groupBy('survey_users.survey_id')
								->orderBy('survey.id','desc')
								->where('survey.id',$surveyor_data->id)
								->first();

							
							    
							   if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
									{
										$image = url('/').'/media/users/'.$surveyor->image;
									}else{
										$image ='';
									}
									if($decoded['surveyors_id']==$surveyor->surveor_id && $surveyor->usstatus=="pending")
									{
										$status='0';
									}else{
										$status=$surveyor->status;
									}
							 
								$data1=array('id'=>(string)($surveyor->id),
								'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
								'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
								'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
								'operator_id'=>!empty($surveyor->user_id) ? (string)$surveyor->user_id : '',
								'username'=>!empty($surveyor->username) ? $surveyor->username : '',
								'surveyor_id'=>!empty($surveyor->surveor_id) ?(string)$surveyor->surveor_id :'',
								'surveyors_name'=>!empty($surveyor->suusername) ? $surveyor->suusername : '',
								'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
								'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
								'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
								'status'=>$status,
								

								'last_status'=>$surveyor->last_status,
								'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
								'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
								'image_url'=>$image,
								'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
								//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
								);

						
						$status=1;
						$message = 'Survey request ' .$decoded['type'].' successfully.';
				}else{
					$message = 'Data Not Found.';
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
	 public function surveyListUpcomming(Request $request)
	{   $user_id =  (string)($request->input('user_id'));
		$status =  $request->input('status');
		header('Content-Type: application/json');
		$statuss = 0;
		$message = NULL;
		$data = array();
		$data1 =array();
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);
		
		$r=1;
		if(!empty($user_id))
		{
			$user_d =  User::select('type')->where('id',$user_id)->first(); 
			if(!empty($user_d))
		    {
					if($user_d->type =="0" || $user_d->type =="1")
					{

						$upcoming_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
						DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
						DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
						DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
						'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
						'custom_survey_users.surveyors_id as surveyor_id',
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
							// $createdbyopeartor =  User::select('id')->where('created_by',$user_id)->get(); 
							// //dd($createdbyopeartor );
							// $upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($user_id,$createdbyopeartor) {
							// $query->where('survey.user_id', '=',$user_id )
							// ->orwhereIn('survey.user_id',$createdbyopeartor);});
							$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
										$ids=array();
										foreach($createdbysurveyor as $data){
											$ids[]=$data->id;
										}

										array_push($ids,$user_id);
										

										$upcoming_survey_data=$upcoming_survey_data->WhereIn('survey.user_id',$ids);
							
											
						}
						if($user_d->type =="1")
						{
							//$upcoming_survey_data=$upcoming_survey_data->where('survey.user_id',$user_id);
							//$upcoming_survey_data=$upcoming_survey_data->whereIn('survey_users.surveyors_id',$createdbyopeartor );
							$createdbysurveyor =User::select('created_by')->where('id',$user_id)->first();
									$ids=array();
									$createdbydpsurveyor = User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
									if(!empty($createdbydpsurveyor)){
										
										foreach($createdbydpsurveyor as $data){
											$ids[]=$data->id;
										}
									}
										array_push($ids,$createdbysurveyor->created_by);
								$upcoming_survey_data=$upcoming_survey_data->whereIn('survey.user_id',$ids);
						}
						if($status!=""){
							$upcoming_survey_data=$upcoming_survey_data->where('survey.status',$status);
						}else{
							$upcoming_survey_data=$upcoming_survey_data->where(function ($query) {
								$query->where('survey.status', '=','0' )
										->orWhere('survey.status', '=', '1')
										->orWhere('survey.status', '=', '2')
										->orWhere('survey.status', '=', '3');
							});
						// $upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
						// 	$upcoming_survey_data=$upcoming_survey_data->orderByRaw('survey.status = ? desc',['1']);
					}
						// $upcoming_survey_data=$upcoming_survey_data->Where('survey_users.status', '!=', 'cancelled');

						$upcoming_survey_data=$upcoming_survey_data->groupBy('survey.id');
						$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.start_date','desc');
						$upcoming_survey_data=$upcoming_survey_data->get();
					}else
					{
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
							// $createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
							// $upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($user_id,$createdbysurveyor) {
							// $query->where('survey_users.surveyors_id', '=',$user_id )
							// ->orWhere('custom_survey_users.surveyors_id',$user_id)
							// ->orwhereIn('survey_users.surveyors_id',$createdbysurveyor );});
							$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
									$ids=array();
									foreach($createdbysurveyor as $data){
										$ids[]=$data->id;
									}

									array_push($ids,$user_id);
									//dd($ids);

									$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($ids) {
									$query->WhereIn('custom_survey_users.surveyors_id',$ids)
									->orwhereIn('survey_users.surveyors_id',$ids );});		

							//$upcoming_survey_data=$upcoming_survey_data->whereIn('survey_users.surveyors_id',$createdbysurveyor );

						}else{
							// $upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($user_id) {
							// 	$query->where('survey_users.surveyors_id', '=',$user_id )
							// 	->orWhere('custom_survey_users.surveyors_id',$user_id);});

							$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($user_id) {
							$query->where('survey_users.surveyors_id', '=',$user_id )
							->orWhere('custom_survey_users.surveyors_id',$user_id)
							->orWhere('survey.assign_to',$user_id);});	

							//$upcoming_survey_data=$upcoming_survey_data->where('survey_users.surveyors_id',$user_id);
						}
			

						if($status!=""){
							$upcoming_survey_data=$upcoming_survey_data->where('survey.status',$status);
						}else{
							$upcoming_survey_data=$upcoming_survey_data->where(function ($query) {
								$query->where('survey.status', '=','0' )
										->orWhere('survey.status', '=', '1')
										->orWhere('survey.status', '=', '2')
										->orWhere('survey.status', '=', '3');
							});
						// 	$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
						// $upcoming_survey_data=$upcoming_survey_data->orderByRaw('survey.status = ? desc',['1']);
						}
						$upcoming_survey_data=$upcoming_survey_data->where(function ($query)  {
							$query->Where('survey_users.status','pending')
							->orwhere('survey_users.status','upcoming' )
							->orwhere('custom_survey_users.status','waiting' )
							->orwhere('custom_survey_users.status','upcoming' )
							->orwhere('custom_survey_users.status','approved' );});	
							$upcoming_survey_data=$upcoming_survey_data->groupBy('survey.id');
							$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.start_date','desc');
							
						$upcoming_survey_data=$upcoming_survey_data->get();
					}
								//	dd($upcoming_survey_data);
						if(count($upcoming_survey_data)>0)
						{
							foreach($upcoming_survey_data  as $surveyor)
							{    
								if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
									{
										$image = url('/').'/media/users/'.$surveyor->image;
									}else{
										$image ='';
									}
									// if($user_id==$surveyor->surveor_id && $surveyor->usstatus=="pending")
									// {
									// 	$status='0';
									// }else{
									// 	$status=$surveyor->status;
									// }
								


									$pricing="0";
									$pricing1="0";
									$pricing2="0";
									$surveyor_name="";
									$operator_name="";
								
									
									$userdata = User::where('id',$user_id)->first();
									if($surveyor->survey_type_id=='31')
									{ 
										if($userdata->type!="0" && $userdata->type!="1")
											{
												$survey_ch =  Customsurveyusers::where('survey_id',$surveyor->id)
												->Where('custom_survey_users.status','approved')->first();
											}else{
												$survey_ch =  Customsurveyusers::where('survey_id',$surveyor->id)
												->Where('custom_survey_users.status','approved')->first();
												
											}
											
											
											
									}else{		
											if($userdata->type!="0" && $userdata->type!="1")
											{
												$survey_ch =  Surveyusers::where('survey_id',$surveyor->id);
												$survey_ch=$survey_ch->where(function ($query)  {
														$query->Where('survey_users.status','pending')
														->orwhere('survey_users.status','upcoming' );});
														$survey_ch=$survey_ch->first();
														//dd($survey_ch);
											}else
											{
												$survey_chc =  Surveyusers::where('survey_id',$surveyor->id)->count();
												$survey_ch =  Surveyusers::where('survey_id',$surveyor->id);
												$survey_ch=$survey_ch->where(function ($query) use ($survey_chc) 
												{
														$query->Where('survey_users.status','pending')
														->orwhere('survey_users.status','upcoming' );
														if($survey_chc=='1'){
															$query->orwhere('survey_users.status','declined');
		
														}
		
													});
														$survey_ch=$survey_ch->first();
											}
										
													
										}
												
										
													// dd($survey_ch);
													$surveyor_id_id='';
													$suusername="";
											if(!empty($survey_ch))
											{
												if($surveyor->assign_to!="0"){$surveyor_id_id=$surveyor->assign_to;}else{$surveyor_id_id=$survey_ch->surveyors_id;}
												
												$suusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as suusername'),'company')->where('id',$surveyor_id_id)->first();
												
												if(!empty($suusername))
												{$surveyor_name=$suusername->suusername;}
												
												
													
											}
											if($surveyor->assign_to_op!="0"){$operator_id_id=$surveyor->assign_to_op;}else{$operator_id_id=$surveyor->user_id;}
													
											$opusername = User::select('id',DB::raw('CONCAT(first_name, "  ", last_name) as opusername'),'company')->where('id',$operator_id_id)->first();

												if(!empty($opusername))
												{$operator_name=$opusername->opusername;}
										

								$data1[]=array('id'=>(string)($surveyor->id),
								'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
								'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
								'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
								'operator_id'=>!empty($operator_id_id) ? (string)$operator_id_id : '',
								'username'=>!empty($operator_name) ? $operator_name : '',

								'surveyor_id'=>!empty($surveyor_id_id) ?(string)$surveyor_id_id :'',

								'surveyors_name'=>!empty($surveyor_name) ? $surveyor_name : '',
								'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
								'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
								'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
								'status'=>$surveyor->status,				
								'last_status'=>$surveyor->last_status,
								'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
								'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
								'image_url'=>$image,
								'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
								//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
								);

						}
						$statuss = 1;
						$message = 'Surveyor listing below.';
					}
					else{
						$message = 'Data Not Found.';
					}
					
				}
				else{
					$message = 'Invalid User.';
				}
				
		}else {
			$message = 'One or more required fields are missing. Please try again.';
		}
		$response_data = array('status'=>$statuss,'message'=>$message,'data'=>$data1);
		echo json_encode(array('response' => $response_data));
		die;
	}
	public function surveyListPast(Request $request)
	{   $user_id =  (string)($request->input('user_id'));
		$status =  $request->input('status');
		header('Content-Type: application/json');
		$statuss = 0;
		$message = NULL;
		$data = array();
		$data1 =array();
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);
		
		$r=1;
		if(!empty($user_id))
		{
			$user_d =  User::select('type')->where('id',$user_id)->first(); 
			if(!empty($user_d))
		    {
			if($user_d->type =="0" || $user_d->type =="1")
			{

				$past_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
				DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
				DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
				DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
				'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
				'custom_survey_users.surveyors_id as surveyor_id',
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
					//dd($ids);

					$past_survey_data=$past_survey_data->WhereIn('survey.user_id',$ids);
					
				}
				if($user_d->type =="1")
				{
				

					//$past_survey_data=$past_survey_data->where('survey.user_id',$user_id);
					$createdbysurveyor =User::select('created_by')->where('id',$user_id)->first();
									$ids=array();
									$createdbydpsurveyor = User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
									if(!empty($createdbydpsurveyor)){
										
										foreach($createdbydpsurveyor as $data){
											$ids[]=$data->id;
										}
									}
										array_push($ids,$createdbysurveyor->created_by);
									$past_survey_data=$past_survey_data->whereIn('survey.user_id',$ids);

				}
				if($status!=""){
					$past_survey_data=$past_survey_data->where('survey.status',$status);
				}else{
					$past_survey_data=$past_survey_data->where(function ($query) {
						$query->where('survey.status', '=','4' )
								->orWhere('survey.status', '=', '5')
								->orWhere('survey.status', '=', '6');
					});
				}

				$past_survey_data=$past_survey_data->groupBy('survey.id');
				$past_survey_data=$past_survey_data->orderByRaw('survey.status = ? desc',['4']);
				$past_survey_data=$past_survey_data->orderBy('survey.start_date','desc');
				$past_survey_data=$past_survey_data->get();
			}else
			{
				$past_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
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
					//dd($ids);

					$past_survey_data=$past_survey_data->where(function ($query) use ($ids) {
					$query->WhereIn('custom_survey_users.surveyors_id',$ids)
					->orwhereIn('survey_users.surveyors_id',$ids );});

						
				}else
				{
					
					$past_survey_data=$past_survey_data->where(function ($query) use ($user_id) {
						$query->where('survey_users.surveyors_id', '=',$user_id )
						->orWhere('custom_survey_users.surveyors_id',$user_id)
						->orWhere('survey.assign_to',$user_id);});
				}
				if($status!=""){
					$past_survey_data=$past_survey_data->where('survey.status',$status);
				}
				if($status!=""){
					$past_survey_data=$past_survey_data->where('survey.status',$status);
				}else{
					$past_survey_data=$past_survey_data->where(function ($query) {
						$query->where('survey.status', '=','4' )
								->orWhere('survey.status', '=', '5')
								->orWhere('survey.status', '=', '6');
					});
				}
				$past_survey_data=$past_survey_data->where(function ($query)  {
					$query->Where('survey_users.status','pending')
					->orwhere('survey_users.status','upcoming' )
					->orwhere('custom_survey_users.status','waiting' )
					->orwhere('custom_survey_users.status','upcoming' )
					->orwhere('custom_survey_users.status','approved' );});	
				$past_survey_data=$past_survey_data->groupBy('survey.id');
				$past_survey_data=$past_survey_data->orderByRaw('survey.status = ? desc',['4']);
					$past_survey_data=$past_survey_data->orderBy('survey.start_date','desc');
				$past_survey_data=$past_survey_data->get();
			}
							
		if(count($past_survey_data)>0)
			{
				foreach($past_survey_data  as $surveyor)
				{    
					if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
						{
							$image = url('/').'/media/users/'.$surveyor->image;
						}else{
							$image ='';
						}
						
					
					if($surveyor->surveor_id!="")
					{
						$surveyor_id_id=$surveyor->surveor_id;
					}else{
						$surveyor_id_id=$surveyor->surveyor_id;
					}
					$data1[]=array('id'=>(string)($surveyor->id),
					'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
					'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
					'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
					'operator_id'=>!empty($surveyor->user_id) ? (string)$surveyor->user_id : '',
					'username'=>!empty($surveyor->username) ? $surveyor->username : '',
					'surveyor_id'=>!empty($surveyor_id_id) ?(string)$surveyor_id_id :'',
					'surveyors_name'=>!empty($surveyor->suusername) ? $surveyor->suusername : '',
					'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
					'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
					'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
					'status'=>$surveyor->status,				
					'last_status'=>$surveyor->last_status,
					'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
					'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
					'image_url'=>$image,
					'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
					//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
					);

			}
			$statuss = 1;
			$message = 'Surveyor listing below.';
		}
		else{
			$message = 'Data Not Found.';
		}
		
	}
	else{
		$message = 'Invalid User.';
	}
	
}else {
$message = 'One or more required fields are missing. Please try again.';
}
		$response_data = array('status'=>$statuss,'message'=>$message,'data'=>$data1);
		echo json_encode(array('response' => $response_data));
		die;
	}
	
	
	public function surveyDetails(Request $request)
	{   $survey_id =  (string)($request->input('id'));
		$user_id =  (string)($request->input('user_id'));
		header('Content-Type: application/json');
		$status = 0;
		$message = NULL;
		$data = array();
		$data1 =array();
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);
		//if($decoded) 
		//{
				
				if(!empty($survey_id) && !empty($user_id))
				{	// echo $user_id; die;
					//$r=1;
					// $surveyor_data_count =  Survey::select('*')->count(); 
					//if($surveyor_data_count > 0){
						
						$surveyor_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
						DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'),
						'users.company as operator_company'
						,'users.company_website as operator_company_website',
						'users.country_id as user_country_id','users.id as operator_id',
						'su.id as surveyor_id','csu.id as csurveyor_id','su.rating as surveyor_rating'
						,'csu.rating as csurveyor_rating',
						DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
						 'su.profile_pic as image','survey_users.surveyors_id as surveyors_ids', 
						 'vessels.name as vesselsname', 
						 'vessels.email as vesselsemail', 'vessels.address as vesselsaddress',DB::raw('CONCAT(vessels.city, " , ", users.state, " ,  ", users.pincode) as vesselsad'),
						  'vessels.company as vesselscompany', 'agents.email as agentsemail',
						   'agents.mobile as agentsmobile','vessels.imo_number' ,'port.port as port_name')
						->leftJoin('port', 'port.id', '=', 'survey.port_id')
						->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
						->leftJoin('users', 'users.id', '=', 'survey.user_id')
						->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
						->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
						->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
						->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id')
						->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
						->leftJoin('users as csu', 'csu.id', '=', 'custom_survey_users.surveyors_id')
						->groupBy('survey_users.survey_id')
						->where('survey.id',$survey_id);
						
						// $surveyor_data=$surveyor_data->where(function ($query)  {
						// 	$query->Where('survey_users.status','pending')
						// 	->orwhere('survey_users.status','upcoming' )
						// 	->orwhere('custom_survey_users.status','upcoming' );});	
							$surveyor_data=$surveyor_data->first();
						
						if(!empty($surveyor_data))
						{

							
						
						//foreach($surveyor_data  as $surveyor)
						//{    
						   if($surveyor_data->image!="")
								{
									$image = url('/').'/public/media/users/'.$surveyor_data->image;
								}else{
									$image ='';
								}
								if($surveyor_data->report !="")
								{
									$report = url('/').'/public/media/report/'.$surveyor_data->report;
								}else{
									$report ='';
								}
								if($surveyor_data->invoice !="")
								{
									$invoice = url('/').'/public/media/invoice/'.$surveyor_data->invoice;
								}else{
									$invoice ='';
								}
							$operator_survey_count =  Survey::where('user_id',$surveyor_data->user_id)->count();
							$country_data =  Countries::where('id',$surveyor_data->user_country_id)->first();
						  	//dd($country_data);

							$helper=new Helpers;
							//echo $surveyor_data->surveyors_ids;exit;
							$pricing="0";
							$total_price="0";
							$port_price="0";
							$surveyor_name="";
							$op_company_name="";
							$su_company_name="";
							$userdata = User::where('id',$user_id)->first();
							if($surveyor_data->survey_type_id=='31')
							{
								if($userdata->type!="0" && $userdata->type!="1")
									{
										$survey_ch =  Customsurveyusers::where('survey_id',$surveyor_data->id)
										->Where('custom_survey_users.status','approved')->first();
									}else{
										$survey_ch =  Customsurveyusers::where('survey_id',$surveyor_data->id)
										->Where('custom_survey_users.status','approved')->first();
										
									}
									if(!empty($survey_ch)){
										$total_price=$survey_ch->amount;
									}
									
							}else{		
									if($userdata->type!="0" && $userdata->type!="1")
									{
										$survey_ch =  Surveyusers::where('survey_id',$surveyor_data->id);
										$survey_ch=$survey_ch->where(function ($query)  {
												$query->Where('survey_users.status','pending')
												->orwhere('survey_users.status','upcoming' );});
												$survey_ch=$survey_ch->first();
												//dd($survey_ch);
									}else
									{
										$survey_chc =  Surveyusers::where('survey_id',$surveyor_data->id)->count();
										$survey_ch =  Surveyusers::where('survey_id',$surveyor_data->id);
										$survey_ch=$survey_ch->where(function ($query) use ($survey_chc) 
										{
												$query->Where('survey_users.status','pending')
												->orwhere('survey_users.status','upcoming' );
												if($survey_chc=='1'){
													$query->orwhere('survey_users.status','declined');

												}

											});
												$survey_ch=$survey_ch->first();
									}


									if($surveyor_data->no_of_days!="0"){
										$total_price=$surveyor_data->survey_price*$surveyor_data->no_of_days;
									}else{
										$total_price=$surveyor_data->survey_price;
									}
									$port_price=$surveyor_data->port_price;
								
								}
										
								
											// dd($survey_ch);
											$surveyor_id_id='';
											$suusername="";
									if(!empty($survey_ch))
									{
										if($surveyor_data->assign_to!="0"){
											$surveyor_id_id=$surveyor_data->assign_to;
										}else
										{
											$surveyor_id_id=$survey_ch->surveyors_id;
										}

											$suusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as suusername'),'company','type','created_by')->where('id',$surveyor_id_id)->first();
										
											if(!empty($suusername))
											{
												 $surveyor_name=$suusername->suusername;
												 if($suusername->type=='3')
												 {
													$s = User::select('company')->where('id',$suusername->created_by)->first();
													$su_company_name=$s->company;

												 }else{
													$su_company_name=$suusername->company;
												 }
												
											}
		
									}
								
							
									if($surveyor_data->assign_to_op=="")
									{
										$opusername = User::select('id',DB::raw('CONCAT(first_name, "  ", last_name) as opusername'),'company')->where('id',$surveyor_data->operator_id)->first();
										$op_company_name=$opusername->company;
									}else{
										$opusername = User::select('id',DB::raw('CONCAT(first_name, "  ", last_name) as opusername'),'company')->where('id',$surveyor_data->assign_to_op)->first();
										$op_company_name=$opusername->company;
									}


									// if($surveyor_data->surveyor_id!="")
									// {
									// 		$surveyor_id_id=$surveyor_data->surveyor_id;
									// }else{
									// 		$surveyor_id_id=$surveyor_data->csurveyor_id;
									// }
							
							
								$rating_data =  Rating::select('*')
								->where('survey_id',$surveyor_data->id)
								->first(); 
			
								$rating=$surveyor_data->surveyor_rating;

								$bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)->where('status','upcoming')->count();
								if($bid_count>0){
									$bid_status='1';
								}else{
									$bid_status='0';
								}

								$bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)->where('surveyors_id',$user_id)->where('status','approved')->count();
								if($bid_count>0){
								$bid_accept_status='1';
								}else{
									$bid_accept_status='0';
								}

								if($surveyor_data->survey_type_id=='8' || $surveyor_data->survey_type_id=='23'
								 || $surveyor_data->survey_type_id=='24' ||
								$surveyor_data->survey_type_id=='25' || $surveyor_data->survey_type_id=='29'){
									$type="daily";
								}else{
									$type="fix";
								}

								// $pricing=$helper->SurveyorPrice($surveyor_data->survey_type_id,$surveyor_data->surveyors_ids);	
												 
								// $helper=new Helpers;
							 
								 if(($userdata->type=='0' || $userdata->type=='1') && $surveyor_data->declined=='1')
								 {
									 $status='2';
								 }else{
									 $status=$surveyor_data->status;
								 }
							
							$data1[]=array('id'=>(string)($surveyor_data->id),
							'survey_number'=>!empty($surveyor_data->survey_number) ? $surveyor_data->survey_number :'',
							'port'=>!empty($surveyor_data->port_name) ? $surveyor_data->port_name :'',
							'surveytype_name'=>!empty($surveyor_data->surveytype_name) ? $surveyor_data->surveytype_name :'',
							'survey_category_type'=>!empty($type) ? $type :'',
							'pricing'=>!empty($total_price) ? (string)$total_price : '0',
							'transportation_cost'=>!empty($port_price) ? (string)$port_price : '',
							'surveyor_id'=>!empty($surveyor_id_id) ? (string)$surveyor_id_id : '',
							'surveyors_name'=>!empty($surveyor_name) ? $surveyor_name : '',
							'surveyor_company'=>!empty($su_company_name) ? $su_company_name : '',
							'surveyor_rating'=>!empty($rating_data->rating) ? (string)$rating_data->rating : '0',
							'instruction'=>!empty($surveyor_data->instruction) ? $surveyor_data->instruction : '',
							'file_data'=>  !empty($surveyor_data->file_data) ?  URL::to('/public/media/survey').'/'.$surveyor_data->file_data : '',
							'start_date'=>!empty($surveyor_data->start_date) ? $surveyor_data->start_date : '',
							'end_date'=>!empty($surveyor_data->end_date) ? $surveyor_data->end_date : '',
							'status'=>$status,
							'vesselsname'=>!empty($surveyor_data->vesselsname) ? $surveyor_data->vesselsname : '',
							'vesselsemail'=>!empty($surveyor_data->vesselsemail) ? $surveyor_data->vesselsemail : '',
							'vesselsaddress'=>!empty($surveyor_data->vesselsaddress) ? $surveyor_data->vesselsaddress : $surveyor_data->vesselsad,
							'vesselscompany'=>!empty($surveyor_data->vesselscompany) ? $surveyor_data->vesselscompany : '',
							'imo_number'=>!empty($surveyor_data->imo_number) ? $surveyor_data->imo_number : '',
							'agent_name'=>!empty($surveyor_data->agent_name) ? $surveyor_data->agent_name : '',
							'agentsemail'=>!empty($surveyor_data->agentsemail) ? $surveyor_data->agentsemail : '',
							'agentsmobile'=>!empty($surveyor_data->agentsmobile) ? $surveyor_data->agentsmobile : '',
							'last_status'=>$surveyor_data->last_status,
							'operator_id'=>!empty($opusername->id) ? (string)$opusername->id: '',
							'operator_name'=>!empty($opusername->opusername) ? $opusername->opusername : '',
							'operator_company'=>!empty($surveyor_data->operator_company) ? $surveyor_data->operator_company : '',
							'operator_company_website'=>!empty($surveyor_data->operator_company_website) ? $surveyor_data->operator_company_website : '',
							'operator_survey_count'=>!empty($operator_survey_count) ? $operator_survey_count : '',
							'operator_country_name'=>!empty($country_data->name) ? $country_data->name : '',
							'operator_average_invoice_payment_time'=>"24 days",
							'bid_status'=>$bid_status,
							'bid_accept_status'=>$bid_accept_status,
							'report_url'=>$report,
							'invoice_url'=>$invoice,
							//'image_url'=>$image,
							'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor_data->created_at)),
							);

					//}
						$status = 1;
						$message = 'Surveyor Data below.';
					}
					else{
						$message = 'Data not found.';
					}
					
			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
		//}else 
		//{
		//	$message = 'Opps! Something went wrong. Please try again.';
		//}
				$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
				echo json_encode(array('response' => $response_data));
				die;
}

	

		
		// public function SurveyerList(Request $request)
		// {
		// 		header('Content-Type: application/json');
		// 		$status = 0;
		// 		$message = NULL;
		// 		$data = array();
		// 		$data1 =array();
		// 		$data_row 		= 	file_get_contents("php://input");
		// 		$decoded 	    = 	json_decode($data_row, true);
		// 		$category_data =  Surveycategory::select('*')->get();
		// 		if(!empty($category_data))
		// 		{
		// 				foreach($category_data  as $data )
		// 				{
		// 					$data1[]=array('id'=>(string)($data->id),
		// 					'name'=>$data->name,
		// 					'code'=>$data->code);
		// 				}
		// 				$status = 1;
		// 				$message = 'Survey Category listing below.';
		// 			}
		// 			else{
		// 				$message = 'Data not found.';
		// 			}
		// 			$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
		// 			echo json_encode(array('response' => $response_data));
		// 			die;
		// }
		public function CountryList(Request $request)
		{
				header('Content-Type: application/json');
				$status = 0;
				$message = NULL;
				$data = array();
				$data1 =array();
				$data_row 		= 	file_get_contents("php://input");
				$decoded 	    = 	json_decode($data_row, true);
				$Countries_data =  Countries::select('*')->get();
				if(!empty($Countries_data))
				{
						foreach($Countries_data  as $data )
						{
							$data1[]=array('id'=>(string)($data->id),'name'=>$data->name,'sortname'=>$data->sortname,'phonecode'=>$data->phonecode);
						}
						$status = 1;
						$message = 'Country listing below.';
					}
					else{
						$message = 'Data not found.';
					}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
		}
		public function notificationList(){
			
		header('Content-Type: application/json');
		$status = 0;
		$message = NULL;
		$home_store_data=array();
		$home_data=array();
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);

		if($decoded)
		{
			if(!empty($decoded['user_id'])) 
			{
				
				$notification_data = Notification::where('user_id',$decoded['user_id'])->orderBy('created_at','Desc')
							->get();
				$notification_count_data = Notification::where('user_id',$decoded['user_id'])
							->count();	
				$unreadnotification_count_data = Notification::where('user_id',$decoded['user_id'])->where('is_read',0)
							->count();								
				
				if($notification_data->count()>0)
				{
					$home_data['notification_count']=$notification_count_data;
					$home_data['unread_notification_count']=$unreadnotification_count_data;
					
					foreach($notification_data as $data)
					{
						$home_product_data[] =  array(
												'id' =>$data->id,
												'noti_type' => !empty($data->noti_type) ? $data->noti_type : '',
												'user_type'=>!empty($data->user_type) ? $data->user_type : '',
												'notification'=> !empty($data->notification) ? $data->notification : '',
												'user_id'=>$decoded['user_id'],
												'created_at'=> date("d/m/Y h:i:s A",strtotime($data->created_at)),
												'is_read'=> $data->is_read 
												
												);
						$home_data['notification']=$home_product_data;
					}	
						$status = 1;
						$message='Notification listed below.';	
				}else{
					$home_data['notification'] = [];
					$message = 'No Notification data found.';
				}
			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
				
		}else {
			$message = 'Opps! Something went wrong. Please try again.';
		}
		$response_data = array('status'=>$status,'message'=>$message,'data'=>$home_data);
	    echo json_encode(array('response' => $response_data));
	  // return response()->json(['status'=>$status,'message'=>$message,'data'=>$home_data], 200, [], JSON_UNESCAPED_SLASHES);
       die();
	}
	
	public function notificationDelete(){ 
		header('Content-Type: application/json');
		$status = 0;
		$message = NULL;
		$data = array();
	    
		$user_locations = array();
		$sflag = '0';
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);
		//$decoded        = $_REQUEST;
		    if($decoded){
					if(!empty($decoded['user_id'])) 
					{   
				        //echo $decoded['type']; die;
						if($decoded['type'] == "All")
						{	
								$notificationdata =  DB::table('notification')->where('user_id',$decoded['user_id'])->delete();
								
								if(!empty($notificationdata))
								{
									$status = 1;
									$message = "All notifications have been deleted…";
								}else{
									 $message = "Notification not found.";
								}	
								
						
						} else{
							$notificationdata = DB::table('notification')->where('user_id',$decoded['user_id'])->where('id',$decoded['notification_id'])->delete();
							if(!empty($notificationdata))
								{
									$status = 1;
									$message = "The notification has been deleted… ";
								}else{
									 $message = "Notification not found.";
								}	
						
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
	
	
	public function notificationReadStatus(){
		header('Content-Type: application/json');
		$status = 0;
		$message = NULL;
		$data = array();
	    
		$user_locations = array();
		$sflag = '0';
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);
		//$decoded        = $_REQUEST;
		    if($decoded){
					if(!empty($decoded['notification_id'])) 
					{      //echo $decoded['notification_id']; die;
							$id = $decoded['notification_id'];
						$userdata = Notification::find($id);
							//$userdata =  Notification::where('id',$id)->first();
				           //echo '<pre>'; print_r($userdata); die;
						if(!empty($userdata))
						{		
							if($decoded['is_read']=="1")
								{
									$userdata->is_read = "1";
									$userdata->save();
									$status = 1;
									$message = "Notification Read successfully.";
						
									
						
							}
						}else{
							$message = "Notification Not found";
						}
							/*else
							{
								$userdata->is_read = "0";
								$userdata->save();
								$status = 1;
								$message = "Notification Unread successfully.";
					
								
								
							}*/
						
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
	
	

	
		public function SurveyCategory(Request $request)
		{
				header('Content-Type: application/json');
				$status = 0;
				$message = NULL;
				$data = array();
				$data1 =array();
				$data_row 		= 	file_get_contents("php://input");
				$decoded 	    = 	json_decode($data_row, true);
			//	$category_data =  Surveycategory::select('*')->get();
			if($decoded){
				if(!empty($decoded['user_id'])) 
				{
					$user =  User::select('id','type')->where('id',$decoded['user_id'])->first();
				//$port_data =  Port::select('*')->where('status','1')->orderby('port', 'asc')->get();
				
				$port_data =  Port::select('*')->where('status','1')->get();


				//$vessels_data =  Vessels::select('*')->get();

				if($user->type=='0')
						{
							$createdbysurveyor =  User::select('id')->where('created_by',$user->id)->get();
							$ids=array();
							if(!empty($createdbysurveyor)){
								foreach($createdbysurveyor as $data){
									$ids[]=$data->id;
								}
							}
								array_push($ids,$user->id);
						
						}else
						{
							$createdbysurveyor =  User::select('created_by')->where('id',$user->id)->first();
							$ids=array();
							$createdbydpsurveyor =  User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
							if(!empty($createdbydpsurveyor)){
								
								foreach($createdbydpsurveyor as $data){
									$ids[]=$data->id;
								}
							}
								array_push($ids,$createdbysurveyor->created_by);
						}
				
				$vessels_data =  Vessels::select('*')->whereIn('user_id',$ids)
				
				->orderBy('vessels.name','asc')->get();

				//$surtype_data =  Surveytype::select('*')->where('status','1')->get();
				$surtype_data =  Surveytype::select('*')->where('status','1')->orderBy('name','Asc')->get();
				//$agent_data =    Agents::select('*')->get();

				if($user->type=='0'){
					$createdbysurveyor =  User::select('id')->where('created_by',$user->id)->get();
					$ids=array();
					if(!empty($createdbysurveyor)){
						foreach($createdbysurveyor as $data){
							$ids[]=$data->id;
						}
					}
						array_push($ids,$user->id);
				
				}else
				{
							$createdbysurveyor =  User::select('created_by')->where('id',$user->id)->first();
							$ids=array();
							if(!empty($createdbysurveyor)){
								
									$ids[]=$createdbysurveyor->created_by;
							}
								array_push($ids,$user->id);
				}
				$agent_data =  Agents::select('*')->whereIn('user_id',$ids)->orderBy('first_name','Asc')->get();

				if(!empty($surtype_data) && !empty($port_data) && !empty($vessels_data))
				{
						foreach($surtype_data  as $data )
						{
							if(isset($data->price) && $data->price!="" && !empty($data->price)){
								$price=$data->price;
							}else{
								$price="";
							}
							$data1['type_data'][] =array('id'=>(string)($data->id),
							'name'=>$data->name,
							'price'=>$price);
						}
						
						foreach($port_data  as $datas)
						{
							$data1['port_data'][]=array('id'=>(string)($datas->id),
							'country_id'=>!empty($datas->country_id) ? (string)$datas->country_id : '',
							'country'=>!empty($datas->country) ? $datas->country : '',
							'port'=>!empty($datas->port) ? $datas->port : '');
						}

						
						foreach($vessels_data  as $vessels )
						{
							$image = !empty($vessels->image) ? URL::to('/media/vessels').'/'.$vessels->image : '';

							$data1['vessels'][]=array('id'=>(string)($vessels->id),
							'user_id'=>(string)($vessels->user_id),
							'name'=>$vessels->name,
							'imo_number'=>$vessels->imo_number,
							'company'=>$vessels->company,
							'address'=>$vessels->address,
							'email'=>$vessels->email,
							'additional_email'=>$vessels->additional_email,
							'same_as_company'=>$vessels->same_as_company
							,'is_favourite'=>$vessels->favourite
						    ,'same_as_company_address'=>$vessels->same_as_company_address,
							'image'=>$image);
							
					    }
						foreach($agent_data  as $agent )
						{
							$data1['agents'][]=array('id'=>(string)($agent->id),'first_name'=>$agent->first_name,
							'last_name'=>$agent->last_name,'user_id'=>(string)($agent->user_id),'mobile'=>$agent->mobile,'email'=>$agent->email,'image'=>URL::to('/media/agents').'/'.$agent->image);
							
					    }

						$status = 1;
						$message = 'Survey Category listing below.';
					}
					else{
						$message = 'Data not found.';
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


	public function addSurveyoravail(Request $request)
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
			if (isset($decoded['status']) && $decoded['status']!="" && !empty($decoded['user_id']) && !empty($decoded['start']) && !empty($decoded['end'])) 
			{

				$events=Events::select('events.id')->where('user_id',$decoded['user_id'])->where('start_event',$decoded['start'])->first();

					$user = Auth::user();	   
					$status = $decoded['status'];
					$user_id =  $decoded['user_id'];
					$start =  $decoded['start'];
					$end =  $decoded['end'];

					if(empty($events)){
						$events=new Events;
					}
					
					$events->user_id=$user_id;
					$events->title=$status;
					$events->start_event=$start;
					$events->end_event=$end;
					$events->save();					
					if($events->title=='1'){
						
						$type="";
						$title="1";
					}elseif($events->title=='0'){
						
						$type="";
						$title="0";
					}
					$data1[]=array(
						'id'   => $events->id,
						'status'   =>$title,
						'start'   =>date('d-m-Y',strtotime($events->start_event)),
						'end'   => date('d-m-Y',strtotime($events->end_event)),
						'type'   => $type,
						'imageurl'=>'https://localhost/imars/public/media/logo.png'
					
					
					);

					$status = 1;
					$message = 'User availability add successfully.';
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
   
   public function eventsload()
   {
	$user = Auth::user();
	header('Content-Type: application/json');
	$status = 0;
	$message = NULL;
	$data = array();
	$is_avail="";
	$data1 =array();
	$data_row 		= 	file_get_contents("php://input");
	$decoded 	    = 	json_decode($data_row, true);
	if($decoded) 
	{
		if (!empty($decoded['user_id']) && !empty($decoded['filter'])) 
		{
			       $userid=$decoded['user_id'];
				   $user = Auth::user();
				  // $events=Events::select('events.id as id','events.title','events.start_event','events.end_event')->where('user_id',$decoded['user_id'])->get();
				  
				   $surveyor_data =  Survey::select('survey.id as id','survey.survey_number as title','survey.start_date as start_event',
				   'survey.end_date as end_event')	
				   ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
				   ->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id')
				   
				   ->where(function ($query)  {
					$query->Where('survey_users.status','upcoming')
					->orWhere('custom_survey_users.status','approved' );});
					$users =   User::where('id',$userid)->first();



					if($decoded['filter']=='true' && $users->type=="3"){
						$surveyor_data= $surveyor_data->where('survey.assign_to',$userid );
						
					}
					else if($decoded['filter']=='false' && $users->type=="3"){
						$surveyor_data= $surveyor_data->where('survey.assign_to',$userid );

					}
					else if($decoded['filter']=='true' && $users->type=="2"){
						$surveyor_data= $surveyor_data->where(function ($query)  use ($userid)
						{
							$query->Where('custom_survey_users.surveyors_id',$userid)
							->orwhere('survey_users.surveyors_id',$userid )->where('survey.assign_to','0' );
						  });
						  
						  $surveyor_data= $surveyor_data->where('survey.assign_to','0' );
					}
					else{
						
						$u=	User::select('type')->where('id',$userid)->first();
						
						$surveyor_data= $surveyor_data->where(function ($query)  use ($userid)
						{
							$query->Where('custom_survey_users.surveyors_id',$userid)
							->orwhere('survey_users.surveyors_id',$userid );
						  });
					}
					

					  $surveyor_data= $surveyor_data->where('survey.status','1')
				//    ->groupBy('urvey_users.survey_id')
				   ->get();

				 //dd($surveyor_data);
				$ids=array();
				if( $decoded['filter']=='false' && $users->type=="2"){
					$createdbysurveyor =  User::select('id')->where('created_by', $userid)
					->where('status','1')
					->get(); 
					
					foreach($createdbysurveyor as $data){
					$ids[]=$data->id;
					}

					array_push($ids, $userid);   
				}else{
					array_push($ids, $userid);   
				}
								
									   
				$events=Events::select('events.id as id','events.title','events.start_event','events.end_event')
					   ->whereIn('user_id',$ids);
						// ->whereNotBetween('start_event', [$data->start_event, $data->end_event])
						// ->where('title','0')
						if($decoded['filter']=='false' && $users->type=="2"){
							$events=$events->where('title','1')->get();
						}else{
							$events=$events->get();
						}
					   

					   foreach($events as $event)
						{
							
								$surveyor_data->add($event);

						}
			if(!empty( $surveyor_data) && count ($surveyor_data)>0)
			{
	               $data=array();

					foreach($surveyor_data as $event)
					{
						if($event->title=='1'){
							$class="onuser";
							$type="";
							$title="1";
						}elseif($event->title=='0'){
							$class="offuser";
							$type="";
							$title="0";
						}else{
							$class="running";
							$type="survey";
							$title=$event->title;
						}
						$data[] = array(
							'id'   => $event->id,
							'status'   =>$title,
							'start'   =>date('d-m-Y',strtotime($event->start_event)),
							'end'   => date('d-m-Y',strtotime($event->end_event)),
							'type'   => $type,
							'imageurl'=>'https://localhost/imars/public/media/logo.png'
						
						
						);
					}

					$user_data = DB::table('users')->select('users.is_avail')->where('id',$decoded['user_id'])->first();
					if($user_data->is_avail==0){
						$is_avail=3;
					}else{
						$is_avail=4;
					}
					$is_avail = (string)$is_avail;
	                $status = 1;
					$message = 'Event List below.';
				}else
				{

					$message = 'Data Not Found';
				}
		}else {
			$message = 'One or more required fields are missing. Please try again.';
		}
	}else 
	{
		$message = 'Opps! Something went wrong. Please try again.';
	}
	$response_data = array('status'=>$status,'message'=>$message,'data'=>$data,'is_avail'=>$is_avail);
	echo json_encode(array('response' => $response_data));
	die;
   }	
   public function UserSurveyType(Request $request)
		{
				header('Content-Type: application/json');
				$status = 0;
				$message = NULL;
				$data = array();
				$data1 =array();
				$data_row 		= 	file_get_contents("php://input");
				$decoded 	    = 	json_decode($data_row, true);
			//	$category_data =  Surveycategory::select('*')->get();
				
			
			if (!empty($decoded['user_id'])) 
			{
				$usersurveytypesdetail = UsersSurveyPrice::select('users_survey_price.id','users_survey_price.survey_price','users_survey_price.survey_type_id','survey_type.name as survey_type_name')
			->leftJoin('survey_type', 'users_survey_price.survey_type_id', '=', 'survey_type.id')
			->where('users_survey_price.user_id',$decoded['user_id'])->orderBy('survey_type.name','asc')->get();
				if(!empty($usersurveytypesdetail) )
				{
				
						foreach($usersurveytypesdetail  as $data )
						{
							if(!empty($data->survey_price))
							{
								$price=$data->survey_price;
							}else{
								$price="";
							}
							if($data->survey_type_id=='8' || $data->survey_type_id=='23' || $data->survey_type_id=='24' || $data->survey_type_id=='25' || $data->survey_type_id=='29')
        					{
								$price='$'.$price.'/day';
							}else{
								$price='$'.$price;
							}

							$data1[] =array('id'=>(string)($data->id),
							'name'=>!empty($data->survey_type_name)?$data->survey_type_name:"",
							'price'=>(string)$price);
						}
						$userd=User::where('id',$decoded['user_id'])->where('conduct_custom','1')->first();
						if($userd){
							$data1[] =array('id'=>'31',
							'name'=>"Custom Occasional Survey",
							'price'=>"");
						}
						
						$status = 1;
						$message = 'Survey Category listing below.';
					}
					else{
						$message = 'Data not found.';
					}
				}else {
					$message = 'One or more required fields are missing. Please try again.';
				}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
		}
		public function UserSurveyPort(Request $request)
		{
				header('Content-Type: application/json');
				$status = 0;
				$message = NULL;
				$data = array();
				$data1 =array();
				$data_row 		= 	file_get_contents("php://input");
				$decoded 	    = 	json_decode($data_row, true);
			//	$category_data =  Surveycategory::select('*')->get();
				
				//$user_port_data =  DB::table('users_port')->select('*')->where('user_id',$decoded['user_id'])->get();
				
				$userportdetail = UsersPort::select('users_port.*','p.port as portname')
				->leftJoin('port as p', 'users_port.port_id', '=', 'p.id')
				->where('users_port.user_id',$decoded['user_id'])
				->orderBy('portname','asc')
				->get();

				if (!empty($decoded['user_id'])) 
				{
					if(!empty($userportdetail) )
					{
				


						foreach($userportdetail  as $data )
						{	

							if(!empty($data->cost))
							{
								$price=$data->cost;
							}else{
								$price="";
							}

							$data1[] =array('id'=>(string)($data->id),
							'name'=>$data->portname,
							'price'=>(string)$price);
						}
						
						
						$status = 1;
						$message = 'Port listing below.';
					}
					else{
						$message = 'Data not found.';
					}
				}else {
					$message = 'One or more required fields are missing. Please try again.';
				}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
		}
		public function reportsubmit(Request $request)
		{
			$data1 =array();
			$validator = Validator::make($request->all(), [
			
			'file' => 'required',
			'survey_id' => 'required',
			'surveyor_id' => 'required',
			
			]);
			if ($validator->fails()) {
				$status="0";
				$message = 'One or more required fields are missing. Please try again.';	
			}
			else
			{
				$user = Auth::user();
				$survey_id =  $request->input('survey_id');
				$surveyor_id =  $request->input('surveyor_id');
				$no_of_days =  $request->input('no_of_days');

				$survey =   Survey::where('id',$survey_id)->first();
				if(!empty($survey))
				{
					$image = $request->file('file');
					if(isset($image))
					{
						$imageName = time().$image->getClientOriginalName();
						$image->move(public_path().'/media/report', $imageName);
						$survey->report = $imageName;
					}
					$survey->status='3';


				
					$user=User::where('id',$surveyor_id)->first();

					if($no_of_days!="" && $no_of_days!="undefined")
						{
							if($user->type=="3"){
								$UsersSurveyPrice =  UsersSurveyPrice::where('survey_type_id',$survey->survey_type_id)
								->where('user_id',$user->created_by)->first();
							}else{
								$UsersSurveyPrice =  UsersSurveyPrice::where('survey_type_id',$survey->survey_type_id)
							->where('user_id',$user->id)->first();
							}
							
							$UsersSurveyPrice->no_of_days=$no_of_days;
							$UsersSurveyPrice->save();
							$survey->no_of_days=$no_of_days;
						}

						$survey->save();

					$helper=new Helpers;
					if($survey->assign_to_op!="0"){
						$operator_token =  User::select('users.*')->where('id',$survey->assign_to_op)->first(); 
					}else
					{
						$operator_token =  User::select('users.*')->where('id',$survey->user_id)->first(); 
					}

					$helper->SendNotification($operator_token->device_id,'Survey report received',' You have received a survey report. You can view and download the report in your account under Report Submitted surveys tab.');
					$notification = new Notification();
										$notification->user_id = $operator_token->id;
										$notification->title = 'Survey report received';
										$notification->noti_type = 'Survey report received';
										$notification->user_type = $operator_token->type;
										$notification->notification = ' You have received a survey report. You can view and download the report in your account under Report Submitted surveys tab.';
										$notification->country_id = $operator_token->country_id;
										$notification->is_read = 0;
										$notification->save();
					
					
					$emailData = Emailtemplates::where('slug','=','survey-report-submit')->first();

							if($emailData){
								$textMessage = strip_tags($emailData->description);
								$subject = $emailData->subject;
								$to = $operator_token->email;
				
								if($operator_token->first_name!=''  )
								{
									$textMessage = str_replace(array('USER_NAME'), array($operator_token->first_name),$textMessage);
									
									Mail::raw($textMessage, function ($messages) use ($to,$subject) {
										
										$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
									});
								}
							}
					$surveyor =  Survey::select('survey.*','survey_type.name as surveytype_name',
					'port.port as port_name',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
					DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
					DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
						'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
						'users.id as operator_id',
						'survey_users.status as usstatus','vessels.name as vesselsname')
					->leftJoin('port', 'port.id', '=', 'survey.port_id')
					->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
					->leftJoin('users', 'users.id', '=', 'survey.user_id')
					->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
					->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
					->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
					->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
					->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id')
					->where(function ($query)  {
						$query->Where('survey_users.status','pending')
						->orwhere('survey_users.status','upcoming' )
						->orwhere('custom_survey_users.status','waiting' )
						->orwhere('custom_survey_users.status','upcoming' )
						->orwhere('custom_survey_users.status','approved' );})
					->groupBy('survey_users.survey_id')
					->where('survey.id',$survey_id)
					->first();
				//	dd($surveyor);
					 if($surveyor->report &&  file_exists(public_path('/media/report/').$surveyor->report ))
					{
						$image = url('/').'/public/media/users/'.$surveyor->report;
					}else
					{
						$image ='';
					}
					if($surveyor_id==$surveyor->surveor_id && $surveyor->usstatus=="pending")
					{
						$status='0';
					}else{
						$status=$surveyor->status;
					}
							 
					$data1=array('id'=>(string)($surveyor->id),
					'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
					'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
					'operator_id'=>!empty($surveyor->operator_id) ? (string)$surveyor->operator_id : '',
					'username'=>!empty($surveyor->username) ? $surveyor->username : '',
					'surveyor_id'=>!empty($surveyor->surveor_id) ?(string)$surveyor->surveor_id :'',
					'surveyors_name'=>!empty($surveyor->suusername) ? $surveyor->suusername : '',
					'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
					'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
					'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
					'status'=>$status,							
					'last_status'=>$surveyor->last_status,
					'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
					'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
					'image_url'=>$image,
					'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
					//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
					);
					$status="1";
					$message = 'You have successfully submitted your survey report… ';
				}else
				{
					$status="0";
					$message = 'Invalid survey.';				
				}
			}
		
			$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
			echo json_encode(array('response' => $response_data));
			die;
	}
	public function reportaccept(Request $request)
		{
			header('Content-Type: application/json');
				$status = 0;
				$message = NULL;
				$data = array();
				$data1 =array();
				$data_row 		= 	file_get_contents("php://input");
				$decoded 	    = 	json_decode($data_row, true);
			//	$category_data =  Surveycategory::select('*')->get();
				
			
			if (!empty($decoded['operator_id']) && !empty($decoded['surveyor_id']) && !empty($decoded['survey_id'])) 
				{
					$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])->first();
				if(!empty($surveyor_data))
				{


					if($surveyor_data->survey_type_id!='31')
					{
						$survey_users =  SurveyUsers::select('survey_users.*')
						->where('survey_users.survey_id',$decoded['survey_id'])
						->where('survey_users.surveyors_id',$decoded['surveyor_id'])
						->first();
						if(!empty($survey_users)){
							$survey_users->is_finished='1';
							$survey_users->save();
						}
						
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
							//dd($surveyor_data);
							$surveyor_data->save();
							
							$helper=new Helpers;
							$helper->SendNotification($surveyor_token->device_id,' Survey report has been accepted','The report you submitted has been accepted by the operator. The invoice has been emailed to the operator');

							$notification = new Notification();
							$notification->user_id = $surveyor_token->id;
							$notification->title = 'Survey report has been accepted';
							$notification->noti_type = 'Your survey report is accepted!';
							$notification->user_type = $surveyor_token->type;
							$notification->notification = 'The report you submitted has been accepted by the operator. The invoice has been emailed to the operator';
							$notification->country_id = $surveyor_token->country_id;
							$notification->is_read = 0;
							$notification->save();
							// $data1 = array( 'email' =>$surveyor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyor_token->email,'content' => 'The report you submitted has been accepted by the operator. The invoice has been emailed to the operator'));
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
							$survey_type_price=0;
							$user=User::where('id',$decoded['surveyor_id'])->first();

							if($surveyor_data->survey_type_id=='31')
							{
								if($user->type=="3"){
									$custom_survey_price_data = Customsurveyusers::select('custom_survey_users.*')
								->where("custom_survey_users.survey_id",$surveyor_data->id)
								->where("custom_survey_users.surveyors_id",$user->created_by)->first();
								}else{
									$custom_survey_price_data = Customsurveyusers::select('custom_survey_users.*')
								->where("custom_survey_users.survey_id",$surveyor_data->id)
								->where("custom_survey_users.surveyors_id",$user->id)->first();
								}

								$total_price=$custom_survey_price_data->amount;
								$port_price=0;
								$survey_type_price=$custom_survey_price_data->amount;
							}else{

								if($user->type=="3"){
									$survey_price_data = UsersSurveyPrice::select('users_survey_price.*')
								->where("users_survey_price.survey_type_id",$surveyor_data->survey_type_id)
								->where("users_survey_price.user_id",$user->created_by)->first();
								}else{
									$survey_price_data = UsersSurveyPrice::select('users_survey_price.*')
								->where("users_survey_price.survey_type_id",$surveyor_data->survey_type_id)
								->where("users_survey_price.user_id",$user->id)->first();
								}

								if($surveyor_data->no_of_days!="0"){
									$total_price=$surveyor_data->survey_price*$surveyor_data->no_of_days+$surveyor_data->port_price;
									$survey_type_price=$surveyor_data->survey_price*$surveyor_data->no_of_days;
								}else{
									$total_price=$surveyor_data->survey_price+$surveyor_data->port_price;
									$survey_type_price=$surveyor_data->survey_price;
								}
								
								$port_price=$surveyor_data->port_price;
							}
							

							
							if($surveyor_data->assign_to_op!="" || $surveyor_data->assign_to_op!="0"){
								$operator_id=$surveyor_data->assign_to_op;
							}else{
								$operator_id=$surveyor_data->user_id;

							}

							$payment = new Earning;

							$payment->survey_id= $decoded['survey_id'];
							$payment->operator_id= $operator_id;
							$payment->surveyor_id =$surveyor_data->accept_by;
							$payment->invoice_amount=$total_price ;
							$payment->save();

							$invoice_data = Earning::select("payment.*",'port.port as port_name','survey_type.name as survey_type_name',
							'vessels.name as vesselsname','survey.survey_number','vessels.imo_number','survey.survey_type_id')
							->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
							->leftJoin('port', 'port.id', '=', 'survey.port_id')
							->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
							->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
							->where('payment.id',$payment->id)
							->first();
							
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
							'to'=>array('company'=>$company  ,
							'email'=>$op_token->email ,
							'operator_name'=>$op_token->username ,
							'address1'=>$address1,
							'address2'=>$address2)
										);
									//	dd($invoice_ar);
									$random_string=$helper->generateRandomString();
									$data2=array('content' => $invoice_ar);
									$pdf = PDF::loadView('pages.invoice', compact('data2'));
									$invoice_file= 'invoice_'.$random_string.'_'.$invoice_data->survey_number.'.pdf';
									$pdf->save(public_path().'/media/invoice/'. $invoice_file);
									$surveyor_data->invoice=$invoice_file;
									$surveyor_data->save();
							//  $data1 = array( 'email' =>$op_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $op_token->email,'content' => $invoice_ar));
							// Mail::send( 'pages.email.invoice',$data1, function( $message ) use ($data1)
							// {
							// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Survey Report Accept' );
			
							// });
							$link = \App::make('url')->to('/public/media/invoice')."/".$surveyor_data->invoice;

							//  $data1 = array( 'email' =>$op_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $op_token->email,'content' => $invoice_ar));
							// Mail::send( 'pages.email.invoice',$data1, function( $message ) use ($data1)
							// {
							// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Survey Report Accept' );
			
							// });
							$emailData = Emailtemplates::where('slug','=','survey-accept-invoice-send-to-operator')->first();

							if($emailData){
								$textMessage = $emailData->description;
								$subject = $emailData->subject;
								$to = $op_token->email;

								if($op_token->first_name!='' )
								{
									$textMessage = str_replace(array('USER_NAME','LINK'), array($op_token->first_name,$link),$textMessage);

								Mail::send([], [], function ($messages) use ($to,$subject,$textMessage) {
								  
									$messages->to($to)->subject($subject)
									->from('imars@marineinfotech.com','iMarS')
									->setBody($textMessage, 'text/html');
								});	
								}
							}
							if($vessels_data->email!=""){
								$emailData = Emailtemplates::where('slug','=','survey-accept-invoice-send-to-operator')->first();
	
								if($emailData){
									$textMessage = $emailData->description;
									$subject = $emailData->subject;
									$to = $vessels_data->email;
		
									if($op_token->first_name!='' )
									{
										$textMessage = str_replace(array('USER_NAME','LINK'),
										 array($op_token->first_name,$link),$textMessage);

								Mail::send([], [], function ($messages) use ($to,$subject,$textMessage) {
								  
									$messages->to($to)->subject($subject)
									->from('imars@marineinfotech.com','iMarS')
									->setBody($textMessage, 'text/html');
								});	
									}
								}
							}
							if($vessels_data->additional_email!=""){
								$emailData = Emailtemplates::where('slug','=','survey-accept-invoice-send-to-operator')->first();
	
								if($emailData){
									$textMessage = $emailData->description;
									$subject = $emailData->subject;
									$to = $vessels_data->additional_email;
		
									if($op_token->first_name!='' )
									{
										$textMessage = str_replace(array('USER_NAME','LINK'), array($op_token->first_name,$link),$textMessage);

								Mail::send([], [], function ($messages) use ($to,$subject,$textMessage) {
								  
									$messages->to($to)->subject($subject)
									->from('imars@marineinfotech.com','iMarS')
									->setBody($textMessage, 'text/html');
								});	
									}
								}
							}
							$surveyor =  Survey::select('survey.*','survey_type.name as surveytype_name',
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
								->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id')
								->groupBy('survey.id')
								->where(function ($query)  {
									$query->Where('survey_users.status','pending')
									->orwhere('survey_users.status','upcoming' )
									->orwhere('custom_survey_users.status','waiting' )
									->orwhere('custom_survey_users.status','upcoming' )
									->orwhere('custom_survey_users.status','approved' );})
								->where('survey.id',$decoded['survey_id'])
								->first();
								if(!empty($surveyor->image) &&  file_exists(public_path('/media/users/').$surveyor->image ))
								{
									$image = url('/').'/media/users/'.$surveyor->image;
								}else
								{
									$image ='';
								}
								
								$status=$surveyor->status;
								
								// if($surveyor->surveor_id!="")
								// {
								// 	$surveyor_id_id=$surveyor->surveor_id;
								// }else{
								// 	$surveyor_id_id=$surveyor->surveyor_id;
								// }

								
								$data1=array('id'=>(string)($surveyor->id),
								'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
								'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
								'operator_id'=>!empty($surveyor->user_id) ? (string)$surveyor->user_id : '',
								'username'=>!empty($surveyor->username) ? $surveyor->username : '',
								'surveyor_id'=>!empty($decoded['surveyor_id']) ?(string)$decoded['surveyor_id']:'',
								'surveyors_name'=>!empty($surveyor->suusername) ? $surveyor->suusername : '',
								'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
								'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
								'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
								'status'=>$status,							
								'last_status'=>$surveyor->last_status,
								'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
								'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
								'image_url'=>$image,
								'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
								//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
								);
							$status = 1;
							$message = 'Report Accept Successfully.';
					}
					else{
						$message = 'Survey not found.';
					}
				}
				else{
					$message = 'Survey not found.';
				}
				}else 
				{
					$message = 'One or more required fields are missing. Please try again.';
				}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
					echo json_encode(array('response' => $response_data));
					die;
	}

	public function SurveyFilterUser(Request $request)
	{
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
		//	$category_data =  Surveycategory::select('*')->get();
			
		if (!empty($decoded['user_id'])) 
		{
				$user_data =  User::select('users.id','users.type',
				DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),'users.mobile')->where('status','!=','0')
				->where('id',$decoded['user_id'])
				->first();
				if($user_data->type=='1')
				{
					$createdbysurveyor =   User::select('created_by')->where('id',$user_data->id)->first();
					$ids=array();
					$createdbydpsurveyor = User::select('id')->where('created_by',$createdbysurveyor->created_by)->where('id','!=',$user_data->id)->get();
					if(!empty($createdbydpsurveyor)){
						
						foreach($createdbydpsurveyor as $data){
							$ids[]=$data->id;
						}
					}
					array_push($ids,$createdbysurveyor->created_by);
					//dd($ids);
					$survey_rec =  User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
					'users.mobile','users.id')
					->where('users.first_name','!=',"")->whereIn('users.id',$ids)->get();
					
				

				}else if($user_data ->type=='0')
				{
					$survey_rec = User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
					,'users.id','users.mobile')->where('users.created_by',$user_data->id)->get();
				}
				else if($user_data ->type=='2')
				{
					$survey_rec = User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
					,'users.id','users.mobile')->where('email_verify','1')->where('users.created_by',$user_data->id)->get();
				}

				if(!empty($survey_rec) )
				{
					$data1=array();
					$data1[]=array('id'=>(string)$user_data->id,'name'=>(string)$user_data->username,'mobile'=>(string)$user_data->mobile);
					///dd($survey_rec);
					foreach($survey_rec as $data)
					{
						$Survey=new App\Models\Survey;
						if($user_data->type=='0'  || $user_data->type=='1')
						{
							$ip=$data->id;
							$osurveys=$Survey->select('survey.*')
							->where('assign_to_op',$ip );
							$osurveys=$osurveys->get();
							if(count($osurveys)>0){
								$data1[]=array('id'=>(string)$data->id,'name'=>(string)$data->username,'mobile'=>(string)$data->mobile);

							}
						}else{
							$ip=$data->id;
							$ssurveys=$Survey->select('survey.*')
							->where(function ($query) use ($ip) {
								$query->whereRaw("find_in_set($ip,surveyors_id)")
									->orwhere('assign_to',$ip );});
									$ssurveys=$ssurveys->get();
							if(count($ssurveys)>0){
								$data1[]=array('id'=>(string)$data->id,'name'=>(string)$data->username,(string)'mobile'=>$data->mobile);
							}
						}
						
					}
				
					
					$status = 1;
					$message = 'User Data below.';
				}
				else{
					$message = 'Data not found.';
				}
			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
				$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
				echo json_encode(array('response' => $response_data));
				die;
	}
	public function addrating(Request $request)
	{
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
		//	$category_data =  Surveycategory::select('*')->get();
			
		if (!empty($decoded['operator_id']) && !empty($decoded['surveyor_id']) && !empty($decoded['rating']) && !empty($decoded['survey_id'])) 
		{
					$rating =  Rating::select('*')->where('operator_id',$decoded['operator_id'])
					->where('survey_id',$decoded['survey_id'])
					->where('surveyor_id',$decoded['surveyor_id'] )->first(); 
						
					if(empty($rating))
					{
						$rating =  new Rating ;
											
						$rating->operator_id=$decoded['operator_id'];
						$rating->surveyor_id=$decoded['surveyor_id'];
						$rating->survey_id=$decoded['survey_id'];

						$rating->rating=$decoded['rating'];
						$rating->comment=!empty($decoded['comment'])?$decoded['comment']:"";
						$rating->save();
						
					}else{
						$rating->rating=$decoded['rating'];
						$rating->comment=!empty($decoded['comment'])?$decoded['comment']:"";
						$rating->save();
					}
					$rating_data_count =  Rating::select('*')->where('surveyor_id',$decoded['surveyor_id'] )->count(); 
					$total =  Rating::where('surveyor_id',$decoded['surveyor_id'] )->sum('rating'); 
					$user_rating =$total/$rating_data_count;
					if(!empty($user_rating))
					{
						$user= User::where('id',$decoded['surveyor_id'])->first();
						$user->rating=$user_rating;
						$user->save();
						
					}
					$rating_data_count =  Rating::select('*')->where('surveyor_id',$decoded['surveyor_id'])->count(); 
					
					$total =  Rating::where('surveyor_id',$decoded['surveyor_id'] )->sum('rating'); 
					$user_rating =0;
					if($total!='0' && $rating_data_count!="0")
					{
						$user_rating =$total/$rating_data_count;
					}
					

					if(!empty($user_rating))
					{
						$user= User::where('id',$decoded['surveyor_id'])->first();
						$user->rating=$user_rating;
						$user->save();

						$userd= User::select('created_by')->where('id',$user->id)->first();
						//dd($userd);
						if($userd->created_by!="")
						{
							$createdbysurveyor =  User::select('id')->where('created_by',$userd->created_by)->get();
							$ids=array();
							if(!empty($createdbysurveyor)){
								foreach($createdbysurveyor as $data){
									$ids[]=$data->id;
								}
							}
							
								array_push($ids,$userd->created_by);
								     $rating_data_count1 =  Rating::select('*')->whereIn('surveyor_id',$ids )->count(); 
					
										$total1 =  Rating::whereIn('surveyor_id',$ids )->sum('rating'); 

									 	$user_rating1 =$total1/$rating_data_count1;


								$user1= User::where('id',$userd->created_by)->first();
								$user1->rating=$user_rating1;
								$user1->save();
						}
						
						
					}
					$status="1";
					$message = 'You have given rating successfully.';

			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
				$response_data = array('status'=>$status,'message'=>$message);
				echo json_encode(array('response' => $response_data));
				die;
	}
	public function Finance(Request $request)
	{
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = (object) array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
		//	$category_data =  Surveycategory::select('*')->get();
			
		if (!empty($decoded['user_id'])) 
		{
			$user = User::where('id','=', $decoded['user_id'])->first();
			if($user->type=='0' )
			{
				$createdbysurveyor =  User::select('id')->where('created_by',$user->id)->get();
				$ids=array();
				if(!empty($createdbysurveyor)){
					foreach($createdbysurveyor as $data){
						$ids[]=$data->id;
					}
				}
				
				array_push($ids,$user->id);
			}
			else if($user->type=='1'){
				$createdbysurveyor =User::select('created_by')->where('id',$user->id)->first();
				$ids=array();
				$createdbydpsurveyor = User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
				if(!empty($createdbydpsurveyor)){
					
					foreach($createdbydpsurveyor as $data){
						$ids[]=$data->id;
					}
				}
					array_push($ids,$createdbysurveyor->created_by);
			}
			else
			{
						$createdbysurveyor =  User::select('id')->where('id',$user->id)->first();
						$ids=array();
						if(!empty($createdbysurveyor)){
							
								$ids[]=$createdbysurveyor->id;
						}
							array_push($ids,$user->id);
			}

			if($user->type==0 || $user->type==1)
			{
				$paid_finance_data = Earning::select("payment.*",'port.port as port_name',
				'vessels.name as vesselsname','survey.survey_number')
				->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
				->leftJoin('port', 'port.id', '=', 'survey.port_id')
				->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
				->whereIn('payment.operator_id',$ids)
				->where('payment.invoice_status','paid')
				->groupBy('payment.survey_id')
				->get();
				$unpaid_finance_data = Earning::select("payment.*",'port.port as port_name',
				'vessels.name as vesselsname','survey.survey_number')
				->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
				->leftJoin('port', 'port.id', '=', 'survey.port_id')
				->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
				->whereIn('payment.operator_id',$ids)
				->where('payment.invoice_status','unpaid')
				->groupBy('payment.survey_id')
				->get();

			}else{
				$paid_finance_data = Earning::select("payment.*",'port.port as port_name',
				'vessels.name as vesselsname','survey.survey_number')
				->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
				->leftJoin('port', 'port.id', '=', 'survey.port_id')
				->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
				->where('payment.paid_to_surveyor_status','paid')
				->whereIn('payment.surveyor_id',$ids)
				->groupBy('payment.survey_id')
				->get();
				
				$unpaid_finance_data = Earning::select("payment.*",'port.port as port_name',
				'vessels.name as vesselsname','survey.survey_number')
				->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
				->leftJoin('port', 'port.id', '=', 'survey.port_id')
				->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
				->where('payment.paid_to_surveyor_status','unpaid')
				->whereIn('payment.surveyor_id',$ids)
				->groupBy('payment.survey_id')
				->get();

			}
			$data1=array();
			//dd($unpaid_finance_data);
				if(count($paid_finance_data)> 0 || count($unpaid_finance_data) > 0)
				{
						if(count($paid_finance_data)> 0)
						{
							foreach($paid_finance_data as $data)
							{
								$data1['paid'][] =array('id'=>$data->id,
								'survey_number'=>$data->survey_number,
								'invoice_date'=>date("d/m/Y h:i:s A",strtotime($data->created_at)),
								'invoice_amount'=>(int)$data->invoice_amount,
								'vessels_name'=>$data->vesselsname,
								'port_name'=>$data->port_name,
								'survey_code'=>'',
								'status'=>'paid',
								'invoice'=>$data->invoice);
							}
							
						}else{
							$data1['paid']=array();;
						}

						if(count($unpaid_finance_data) > 0)
						{
							foreach($unpaid_finance_data as $data)
							{
								$data1['unpaid'][] =array('id'=>$data->id,
								'survey_number'=>$data->survey_number,
								'invoice_date'=>date("d/m/Y h:i:s A",strtotime($data->created_at)),
								'invoice_amount'=>(int)$data->invoice_amount,
								'vessels_name'=>$data->vesselsname,
								'port_name'=>$data->port_name,
								'survey_code'=>'',
								'status'=>'unpaid',
								'invoice'=>$data->invoice);
							}
							
						}else{
							
							$data1['unpaid']=array();
							
						}
						
						$status = 1;
						$message = 'Finance Data below.';
				}else{
					$data1 = (object) array();
					$status = 0;
					$message = 'No Data Found.';
				}
			
				
				
			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
				$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
				echo json_encode(array('response' => $response_data));
				die;
	}
	public function CustomerequestSurvey(Request $request)

	{     //echo '<pre>'; print_r($request->all()); die;
			$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			'agent_id' => 'required',
			'port_id' => 'required',
			'ship_id' => 'required|',
			'start_date' => 'required',
			'end_date' => 'required',
			'survey_type_id' => 'required',
			'surveyors_id' => 'required',
			'status' => 'required'
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{

				
				
				$user_id =  (string)($request->input('user_id'));
				$agent_id =  (string)($request->input('agent_id'));
				$port_id =  $request->input('port_id');
				$ship_id =  $request->input('ship_id');
				$start_date =  $request->input('start_date');
				$end_date =  $request->input('end_date');
				$survey_type_id =  $request->input('survey_type_id');
				$surveyors_id =  $request->input('surveyors_id');
				$instruction =  !empty($request->input('instruction')) ? $request->input('instruction') : '';
				$status =  $request->input('status');
				//$last_status =  $request->input('last_status');
				$file_data = $request->file('file_data');
						$country_data=  Port::select('country_id')->where('id',$port_id)->first();

						$survey= new Survey();
						$survey->user_id=$user_id;
						$survey->assign_to_op=$user_id;
						$survey->agent_id=$agent_id;
						$survey->port_id=$port_id;
						$survey->country_id=$country_data->country_id;
						$survey->ship_id=$ship_id;
						$survey->start_date=$start_date;
						$survey->end_date=$end_date;
						$survey->survey_type_id=$survey_type_id;
						$survey->surveyors_id=$surveyors_id;
						$survey->instruction=$instruction;
						$survey->status=$status;
						//$survey->last_status=$last_status;

						$cdate=date("ymd");
						$las_survey_id =  Survey::select('*')->orderBy('id','desc')->first();
						if(!empty($las_survey_id))
						{
							$lastsurveynumber= $las_survey_id->survey_number;
						}else{
							$lastsurveynumber= $cdate.'0001';
						}
						
						$prev_survey_digit= substr($lastsurveynumber , -4);
						$var=0001;
						$final=$prev_survey_digit+$var;
						
						$survey->survey_number =  $cdate.$final;

						//$survey->survey_number = time();


						
						if(isset($file_data))
						{
							$imageName = time().$file_data->getClientOriginalName();
							$file_data->move(public_path().'/media/survey', $imageName);
							$imageName =str_replace(" ", "", $imageName);
							$survey->file_data = $imageName;
						}
						$survey->save();
						
						$usersdata = explode(",",$survey->surveyors_id);
						
						foreach($usersdata as $key => $value)
						{
							$csurveyuser= new Customsurveyusers();
							$csurveyuser->surveyors_id =$value;
							$csurveyuser->survey_id = $survey->id;
							$status="waiting";
							$csurveyuser->status = $status;
							$csurveyuser->save();

							$message_token =  User::select('users.*')->where('users.id',$value)
							->first();
							//echo $message_token->id;
							$helper=new Helpers;
							$helper->SendNotification($message_token->device_id,'You have a new Custom Occasional Survey request!','New Custom Occasional Survey request! Please check survey details and submit a quote within 24 hours. The sooner the better chance to be selected for the survey. ');
						
							$notification = new Notification();
										$notification->user_id = $message_token->id;
										$notification->title = 'You have a new Custom Occasional Survey request!';
										$notification->noti_type = 'You have a new Custom Occasional Survey request!';
										$notification->user_type = $message_token->type;
										$notification->notification = 'New Custom Occasional Survey request! Please check survey details and submit a quote within 24 hours. The sooner the better chance to be selected for the survey. ';
										$notification->country_id = $message_token->country_id;
										$notification->is_read = 0;
										$notification->save();
							$emailData = Emailtemplates::where('slug','=','appoint-custom-survey')->first();

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
					
					                   
						$status=1;
						$message = 'You have started the bidding process, check survey details to view quotes submitted… ';
					
			}
					$response_data = array('status'=>$status,'message'=>$message);
					echo json_encode(array('response' => $response_data));
					die;
	   }
	   public function CustomeSurveyAcceptReject(Request $request)
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
				
			if(!empty($decoded['surveyors_id']) && !empty($decoded['survey_id']) && !empty($decoded['type']))
			{
				$survey_users =  Customsurveyusers::select('custom_survey_users.*')
				->where('custom_survey_users.survey_id',$decoded['survey_id'])
				->where('custom_survey_users.surveyors_id',$decoded['surveyors_id'])
				->first();
				$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])->first();
					
				if(!empty($surveyor_data) && !empty($survey_users))
				{
					if($surveyor_data->assign_to_op!="" || $surveyor_data->assign_to_op!="0"){
						$operator_id=$surveyor_data->assign_to_op;
					}else{
						$operator_id=$surveyor_data->user_id;
	
					}

					$operator_token =  User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'),
					'users.id','users.email','users.type','users.device_id','users.country_id')
						->where('id',$operator_id)->first(); 

						$surveyor_token =  User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as surveyor_name'),
						'users.id','users.email','users.type','users.device_id','users.country_id')
							->where('id',$decoded['surveyors_id'])->first(); 
	
					if($decoded['type']=="accept")
					{   $survey_users->status="upcoming";
						$survey_users->amount=$decoded['amount'];
						
						$helper=new Helpers;
						$helper->SendNotification($operator_token->device_id,'You have received a quote for your Custom Occasional Survey request','You can see the quotes submitted in the survey details and select the winning  bid to finalize the request.');
						
						$notification = new Notification();
						$notification->user_id = $operator_token->id;
						$notification->title = 'You have received a quote for your Custom Occasional Survey request!';
						$notification->noti_type = 'You have received a quote for your Custom Occasional Survey request';
						$notification->user_type = $operator_token->type;
						$notification->notification = 'You can see the quotes submitted in the survey details and select the winning  bid to finalize the request.';
						$notification->country_id = $operator_token->country_id;
						$notification->is_read = 0;
						$notification->save();

						$emailData = Emailtemplates::where('slug','=','survey-submit-bid')->first();

						if($emailData){
							$textMessage = strip_tags($emailData->description);
							$subject = $emailData->subject;
							$to = $operator_token->email;
							$amount=$decoded['amount'];
							if($operator_token->operator_name!='' && $surveyor_token->surveyor_name!="" && $surveyor_data->survey_number )
							{
								$textMessage = str_replace(array('OPERATOR_NAME','SURVEYOR_NAME','BID_AMOUNT','SURVEY_NUMBER'),
								 array($operator_token->operator_name,$surveyor_token->surveyor_name,$amount,$surveyor_data->survey_number),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}
					}else
					{
						$survey_users->status="cancelled";
												 
						$helper=new Helpers;
						$helper->SendNotification($operator_token->device_id,'Survey Request Declined','One of the surveyors you selected for the survey has declined the request. If all your selected surveyors decline the request, the survey will be listed in Cancelled surveys tab..
						');

						$notification = new Notification();
						$notification->user_id = $operator_token->id;
						$notification->title = 'Survey Request Declined';
						$notification->noti_type = 'Survey Request Declined';
						$notification->user_type = $operator_token->type;
						$notification->notification = 'One of the surveyors you selected for the survey has declined the request. If all your selected surveyors decline the request, the survey will be listed in Cancelled surveys tab..';
						$notification->country_id = $operator_token->country_id;
						$notification->is_read = 0;
						$notification->save();
						
					}
						$survey_users->save();
						
						$surveyor =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
							DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
							DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
							DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
							'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
							'vessels.name as vesselsname')
								->leftJoin('port', 'port.id', '=', 'survey.port_id')
								->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
								->leftJoin('users', 'users.id', '=', 'survey.user_id')
								->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
								->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
								->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
								->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
								->groupBy('survey_users.survey_id')
								->orderBy('survey.id','desc')
								->where('survey.id',$surveyor_data->id)
								->first();

							
							    
							   if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
									{
										$image = url('/').'/media/users/'.$surveyor->image;
									}else{
										$image ='';
									}
									if($decoded['surveyors_id']==$surveyor->surveor_id && $surveyor->usstatus=="pending")
									{
										$status='0';
									}else{
										$status=$surveyor->status;
									}
							 
								$data1=array('id'=>(string)($surveyor->id),
								'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
								'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
								'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
								'operator_id'=>!empty($surveyor->user_id) ? (string)$surveyor->user_id : '',
								'username'=>!empty($surveyor->username) ? $surveyor->username : '',
								'surveyor_id'=>!empty($surveyor->surveor_id) ?(string)$surveyor->surveor_id :'',
								'surveyors_name'=>!empty($surveyor->suusername) ? $surveyor->suusername : '',
								'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
								'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
								'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
								'status'=>$status,
								

								'last_status'=>$surveyor->last_status,
								'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
								'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
								'image_url'=>$image,
								'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
								//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
								);

						
						$status=1;
						$message = 'Survey request ' .$decoded['type'].' successfully.';
				}else{
					$message = 'Data Not Found.';
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
	public function operatorCustomeSurveyAccept(Request $request)
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
				
			if(!empty($decoded['operator_id']) && !empty($decoded['surveyors_id']) && !empty($decoded['survey_id']))
			{
				$survey_users =  Customsurveyusers::select('custom_survey_users.*')
				->where('custom_survey_users.survey_id',$decoded['survey_id'])
				->where('custom_survey_users.surveyors_id',$decoded['surveyors_id'])
				->first();
				$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])->first();
					
				if(!empty($surveyor_data) && !empty($survey_users))
				{

					   $survey_users->status="approved";
					   $survey_users->save();

					   $surveyor_data->status='1';
					   $surveyor_data->accept_by=$decoded['surveyors_id'];
					   $surveyor_data->save();

					   
						$survey_usersd =  Customsurveyusers::select('custom_survey_users.*')
						->where('custom_survey_users.survey_id',$decoded['survey_id'])
						->where('custom_survey_users.status','!=','approved')
						->get();
						foreach($survey_usersd as $data)
						{
							$d =  Customsurveyusers::select('custom_survey_users.*')
							->where('custom_survey_users.id',$data->id)
						     ->first();

							$d->delete();
						}
					

						
						
						$helper=new Helpers;
						$surveyor_token =  User::select('users.*')
						->where('id',$decoded['surveyors_id'])->first(); 

						$helper->SendNotification($surveyor_token->device_id,'Congratulations, your quote won the bidding process!','The quote you submitted won the bidding process for Custom Occasional Survey. You can see the details of the survey request not in upcoming surveys tab');
						
						$notification = new Notification();
						$notification->user_id = $surveyor_token->id;
						$notification->title = 'Congratulations, your quote won the bidding process!';
						$notification->noti_type = 'Congratulations, your quote won the bidding process!';
						$notification->user_type = $surveyor_token->type;
						$notification->notification = 'The quote you submitted won the bidding process for Custom Occasional Survey. You can see the details of the survey request not in upcoming surveys tab';
						$notification->country_id = $surveyor_token->country_id;
						$notification->is_read = 0;
						$notification->save();
						$helper=new Helpers;
						//$helper->SendNotification($message_token->device_id,'Appoint Survey','New Survey Request Received. Please accept within 8 hours, or the request will be cancelled.');
						$emailData = Emailtemplates::where('slug','=','operator-accept-bid-for-custom-survey')->first();

						if($emailData){
							$textMessage = strip_tags($emailData->description);
							$subject = $emailData->subject;
							$to = $surveyor_token->email;
			
							if($surveyor_token->first_name!=''  )
							{
								$textMessage = str_replace(array('USER_NAME','SURVEY_NUMBER'),
								 array($surveyor_token->first_name,$surveyor_data->survey_number),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}
						$surveyor =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
							DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
							DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
							DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
							'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
							'vessels.name as vesselsname')
								->leftJoin('port', 'port.id', '=', 'survey.port_id')
								->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
								->leftJoin('users', 'users.id', '=', 'survey.user_id')
								->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
								->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
								->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
								->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
								->groupBy('survey_users.survey_id')
								->orderBy('survey.id','desc')
								->where('survey.id',$surveyor_data->id)
								->first();

							
							    
							   if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
									{
										$image = url('/').'/media/users/'.$surveyor->image;
									}else{
										$image ='';
									}
									if($decoded['surveyors_id']==$surveyor->surveor_id && $surveyor->usstatus=="pending")
									{
										$status='0';
									}else{
										$status=$surveyor->status;
									}
							 
								$data1=array('id'=>(string)($surveyor->id),
								'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
								'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
								'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
								'operator_id'=>!empty($surveyor->operator_id) ? (string)$surveyor->operator_id : '',
								'username'=>!empty($surveyor->username) ? $surveyor->username : '',
								'surveyor_id'=>!empty($surveyor->surveor_id) ?(string)$surveyor->surveor_id :'',
								'surveyors_name'=>!empty($surveyor->suusername) ? $surveyor->suusername : '',
								'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
								'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
								'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
								'status'=>$status,
								

								'last_status'=>$surveyor->last_status,
								'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
								'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
								'image_url'=>$image,
								'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
								//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
								);

						
						$status=1;
						$message = 'Survey request Accept successfully.';
				}else{
					$message = 'Data Not Found.';
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
	public function CustomSurveyUsersList(Request $request)
	{
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
		//	$category_data =  Surveycategory::select('*')->get();
			
		if (!empty($decoded['survey_id'])) 
		{
			 $csurvey_users=User::select('users.id',
			 DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
			 ,'users.mobile')
			 ->leftJoin('custom_survey_users', 'custom_survey_users.surveyors_id', '=', 'users.id')
				->where('custom_survey_users.survey_id',$decoded['survey_id'])
				->count();
			
			if($csurvey_users!="0")
			{
				$csurvey_users=User::select('users.id',
				DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
				,'users.mobile','users.country_code','custom_survey_users.amount','users.rating' ,'users.company')
				->leftJoin('custom_survey_users', 'custom_survey_users.surveyors_id', '=', 'users.id')
				   ->where('custom_survey_users.survey_id',$decoded['survey_id'])
				   ->where('custom_survey_users.status','upcoming')
				   
				   ->get();
			
					foreach($csurvey_users  as $data )
					{
						$data1[] =array('id'=>(string)($data->id),
						'name'=>$data->username,
						'mobile'=>(string)$data->country_code.$data->mobile,
						'amount'=>(string)$data->amount,
						'rating'=>!empty($data->rating)?(string)$data->rating:"0",
						'companyName'=>(string)$data->company
					);
					}
					
					
					$status = 1;
					$message = 'User Data below.';
				}
				else{
					$message = 'Data not found.';
				}
			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
				$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
				echo json_encode(array('response' => $response_data));
				die;
	}
	public function AssigntosurveyorList(Request $request)
	{
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
		//	$category_data =  Surveycategory::select('*')->get();
			
		if (!empty($decoded['user_id'])) 
		{
			$surveyor_list_count =User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
						,'users.id','users.mobile')->where('status','1')
						->where('users.created_by',$decoded['user_id'])
                        ->count();
			
			if($surveyor_list_count!="0")
			{
				$surveyor_list =User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
						,'users.id','users.mobile')->where('status','1')
						->where('users.created_by',$decoded['user_id'])
						->get();
						
						$current_surveyor_list =User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
						,'users.id')
						->where('users.id',$decoded['user_id'])
                        ->first();
						$data1[] =array('id'=>(string)($current_surveyor_list->id),
						'name'=>$current_surveyor_list->username);
					foreach($surveyor_list  as $data )
					{
						$data1[] =array('id'=>(string)($data->id),
						'name'=>$data->username,
						'mobile'=>$data->mobile);
					}
					
					
					$status = 1;
					$message = 'User Data below.';
				}
				else{
					$message = 'Data not found.';
				}
			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
				$response_data = array('status'=>$status,'message'=>$message,'data'=>$data1);
				echo json_encode(array('response' => $response_data));
				die;
	}


	public function AssignToSurveyor(Request $request)
	{
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
		
		
		if (!empty($decoded['user_id']) && !empty($decoded['survey_id']) && !empty($decoded['surveyor_id'])) 
		{
			$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])->first();

						if(!empty($surveyor_data) )
						{
							if( $decoded['surveyor_id']!=$decoded['user_id']){
								$surveyor_data->assign_to=$decoded['surveyor_id'];	
								$surveyor_data->save();
							 }	
							 else{
								$surveyor_data->assign_to= '0';
								$surveyor_data->save();
							 }		

							$helper=new Helpers;

							$surveyors_token =  User::select('users.id','users.email','users.first_name','users.type','users.device_id','users.country_id')->where('id',$decoded['surveyor_id'])->first(); 
							
							$helper->SendNotification($surveyors_token->device_id,'You are assinged a new survey!','You have been assigned a new survey '.$surveyor_data->survey_number.'.Check your upcoming surveys for details.');
									$notification = new Notification();
									
									$notification->user_id = $surveyors_token->id;
									$notification->title = 'You are assinged a new survey!';
									$notification->noti_type = 'You are assinged a new survey!';
									$notification->user_type = $surveyors_token->type;
									$notification->notification = 'You have been assigned a new survey '.$surveyor_data->survey_number.'. Check your upcoming surveys for details.';
									$notification->country_id =!empty($surveyors_token->country_id)?$surveyors_token->country_id :"";
									$notification->is_read = 0;
									$notification->save();


							// $data1 = array( 'email' =>$surveyors_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyors_token->email,'content' => 'You have been assigned a new survey '.$surveyor_data->survey_number.'. Check your upcoming surveys to see the details.'));
							// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
							// {
							// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Appoint Survey' );
			
							// });

							$emailData = Emailtemplates::where('slug','=','assign-survey-to-surveyor')->first();

						if($emailData){
							$textMessage = strip_tags($emailData->description);
							$subject = $emailData->subject;
							$to = $surveyors_token->email;

							if($surveyors_token->first_name!='')
							{
								$textMessage = str_replace(array('USER_NAME','SURVEY_NUMBER'), array($surveyors_token->first_name,$surveyor_data->survey_number),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}
							 
						
							$status='1';
							$message='You have successfully assigned this survey to another surveyor.';

						}
				else{
					$message = 'Data not found.';
				}
			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
				$response_data = array('status'=>$status,'message'=>$message);
				echo json_encode(array('response' => $response_data));
				die;
	}

	public function AssignToop(Request $request)
	{
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
		//	$category_data =  Surveycategory::select('*')->get();
		
		if (!empty($decoded['user_id']) && !empty($decoded['survey_id']) && !empty($decoded['operator_id'])) 
		{
			$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])->first();

						if(!empty($surveyor_data) )
						{

							
							if( $decoded['operator_id']!=$decoded['user_id']){
								$surveyor_data->assign_to_op= $decoded['operator_id'];
								$surveyor_data->save();
							 }	
							 else{
								$surveyor_data->assign_to_op= $decoded['user_id'];
								$surveyor_data->save();
							 }		

							$helper=new Helpers;
							$opeartor_token =  User::select('users.id','users.email','users.first_name',
							'users.type','users.device_id','users.country_id')->where('id',$decoded['operator_id'])->first(); 
							$helper->SendNotification($opeartor_token->device_id,'You are assinged a new survey!','You have been assigned a survey'.' '.$surveyor_data->survey_number.'.Check your upcoming surveys for details.');
										
							$notification = new Notification();
							$notification->user_id = $opeartor_token->id;
							$notification->title = 'You are assinged a new survey!';
							$notification->noti_type = 'You are assinged a new survey!';
							$notification->user_type = $opeartor_token->type;
							$notification->notification = 'You have been assigned a survey '.$surveyor_data->survey_number.'.Check your upcoming surveys for details.';
							$notification->country_id = !empty($opeartor_token->country_id)?$opeartor_token->country_id :"";
							$notification->is_read = 0;
							$notification->save();
	
	
							
	
							// $data1 = array( 'email' =>$opeartor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $opeartor_token->email,'content' => 'You have been assigned a survey'.$surveyor_data->survey_number.'.Login and see the details'));
							// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
							// {
							// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Assign  Survey' );
			
							// });

							$emailData = Emailtemplates::where('slug','=','assign-survey-to-operator')->first();

						if($emailData){
							$textMessage = strip_tags($emailData->description);
							$subject = $emailData->subject;
							$to = $opeartor_token->email;

							if($opeartor_token->first_name!='')
							{
								$textMessage = str_replace(array('USER_NAME','SURVEY_NUMBER'), array($opeartor_token->first_name,
								$surveyor_data->survey_number),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}
								
								$status='1';
								$message='You have successfully assigned this survey to another operator';

						}
				else{
					$message = 'Data not found.';
				}
			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
				$response_data = array('status'=>$status,'message'=>$message);
				echo json_encode(array('response' => $response_data));
				die;
	}

	public function ChangeStartDate(Request $request)
	{
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
		//	$category_data =  Surveycategory::select('*')->get();
		
		if (!empty($decoded['survey_id']) && !empty($decoded['start_date'])) 
		{
			$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])->first();

						if(!empty($surveyor_data) )
						{

							$date2=$surveyor_data->end_date;
							$date1=$surveyor_data->start_date;
							$diff = strtotime($date2) - strtotime($date1); 
          					$day= abs(round($diff / 86400)); 
							$surveyor_data->start_date =  date('Y-m-d',strtotime($decoded['start_date']));		  
							$NewDate= date('Y-m-d',strtotime($decoded['start_date'].' +'.$day.'day'));
							$surveyor_data->end_date = $NewDate;
							$surveyor_data->save();
							$status='1';
							$message='You have successfully changes start date';

						}
				else{
					$message = 'Data not found.';
				}
			}else {
				$message = 'One or more required fields are missing. Please try again.';
			}
				$response_data = array('status'=>$status,'message'=>$message);
				echo json_encode(array('response' => $response_data));
				die;
	}
	public function CancelSurvey(Request $request)
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
				
			if(!empty($decoded['user_id']) && !empty($decoded['survey_id']))
			{
				
				$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])->first();
				$helper=new Helpers;
				if(!empty($surveyor_data) )
				{
					    $surveyor_data->status="2";
						$surveyor_data->save();						 
						$users_data =  User::select('users.*')->where('id',$decoded['user_id'])->first(); 
						if($users_data->type=='0' || $users_data->type=='1')
						{
							
							$surveyors_id = DB::table('custom_survey_users')->select('surveyors_id')->where('custom_survey_users.id',$decoded['survey_id'])->get();
							
							foreach($surveyors_id as $surveyor_id)
							{
								$surveyors_token =  User::select('users.*')->where('id',$surveyor_id->surveyors_id)->first(); 

								$helper->SendNotification($surveyors_token->device_id,'Survey Cancelled','Survey '.$surveyor_data->survey_number.' has been cancelled by the operator. Please be advised.');
								$notification = new Notification();
								$notification->user_id = $surveyors_token->id;
								$notification->title = 'Survey Cancelled!';
								$notification->noti_type = 'Survey Cancelled!';
								$notification->user_type = $surveyors_token->type;
								$notification->notification = 'Survey '.$surveyor_data->survey_number.' has been cancelled by the operator. Please be advised.';
								$notification->country_id = !empty($surveyors_token->country_id)?$surveyors_token->country_id :"";
								$notification->is_read = 0;
								$notification->save();
							}

							if($surveyor_data->assign_to!=0)
							{
							   $surveyors_token =  User::select('users.id','users.first_name','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyor_data->assign_to)->first(); 

							}else
							{
							   $surveyors_token =  SurveyUsers::select('users.id','users.first_name',
							   'users.email','users.type','users.device_id','users.country_id')
							   ->leftJoin('users', 'survey_users.surveyors_id', '=', 'users.id')
							   ->where('survey_users.survey_id',$decoded['survey_id'])
							   ->where('survey_users.status','upcoming')
							   ->first();
							}

							if(!empty($surveyors_token))
							{
								   $helper->SendNotification($surveyors_token->device_id,'Survey Cancelled','Survey '.$surveyor_data->survey_number.' has been cancelled by the operator. Please be advised.');
								   $notification = new Notification();
								   $notification->user_id = $surveyors_token->id;
								   $notification->title = 'Survey Cancelled';
								   $notification->noti_type = 'Survey Cancelled';
								   $notification->user_type = $surveyors_token->type;
								   $notification->notification = 'Survey '.$surveyor_data->survey_number.' has been cancelled by the operator. Please be advised.';
								   $notification->country_id = $surveyors_token->country_id;
								   $notification->is_read = 0;
								   $notification->save();

									$emailData = Emailtemplates::where('slug','=','operator-cancel-survey')->first();
									if($emailData){
										$textMessage = strip_tags($emailData->description);
										$subject = $emailData->subject;
										if($surveyors_token->email!='')
										{
											$surveyor_email=$surveyors_token->email;
											$textMessage = str_replace(array("USER_NAME",'SURVEY_NUMBER'), array($surveyors_token->first_name,$surveyor_data->survey_number),$textMessage);
											
											Mail::raw($textMessage, function ($messages) use ($subject,$surveyor_email) {
												$to = $surveyor_email;
												$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
											});
										}
									}
					
							}
						}else
						{
							if($surveyor_data->assign_to_op!="0"){
								$opeartor_token =  User::select('users.*')->where('id',$surveyor_data->assign_to_op)->first(); 
							}else
							{
								$opeartor_token =  User::select('users.*')->where('id',$surveyor_data->user_id)->first(); 
							}
						
							$helper->SendNotification($opeartor_token->device_id,'Survey Cancelled','Survey '.$surveyor_data->survey_number.' has been cancelled by the surveyor and moved to Cancelled surveys tab. You can start over, and send a new request to another surveyor for your survey need.');
							$notification = new Notification();
							$notification->user_id = $opeartor_token->id;
							$notification->title = 'Survey Cancelled';
							$notification->noti_type = 'Survey Cancelled';
							$notification->user_type = $opeartor_token->type;
							$notification->notification = 'Survey '.$surveyor_data->survey_number.' has been cancelled by the surveyor and moved to Cancelled surveys tab. You can start over, and send a new request to another surveyor for your survey need.';
							$notification->country_id = $opeartor_token->country_id;
							$notification->is_read = 0;
							$notification->save();

							 $emailData = Emailtemplates::where('slug','=','surveyor-cancel-survey')->first();
							 if($emailData){
								 $textMessage = strip_tags($emailData->description);
								 $subject = $emailData->subject;
								 if($opeartor_token->email!='')
								 {
									 $operator_email=$opeartor_token->email;
									 $textMessage = str_replace(array("USER_NAME",'SURVEY_NUMBER'), array($opeartor_token->first_name,$surveyor_data->survey_number),$textMessage);
									 
									 Mail::raw($textMessage, function ($messages) use ($subject,$operator_email) {
										 $to = $operator_email;
										 $messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
									 });
								 }
							 }
						}

						$surveyor =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
							DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
							DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
							DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
							'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
							'vessels.name as vesselsname')
								->leftJoin('port', 'port.id', '=', 'survey.port_id')
								->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
								->leftJoin('users', 'users.id', '=', 'survey.user_id')
								->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
								->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
								->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
								->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
								->groupBy('survey_users.survey_id')
								->orderBy('survey.id','desc')
								->where('survey.id',$surveyor_data->id)
								->first();

							
							    
							   if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
									{
										$image = url('/').'/public/media/users/'.$surveyor->image;
									}else{
										$image ='';
									}
									
							 
								$data1=array('id'=>(string)($surveyor->id),
								'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
								'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
								'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
								'operator_id'=>!empty($surveyor->operator_id) ? (string)$surveyor->operator_id : '',
								'username'=>!empty($surveyor->username) ? $surveyor->username : '',
								'surveyor_id'=>!empty($surveyor->surveor_id) ?(string)$surveyor->surveor_id :'',
								'surveyors_name'=>!empty($surveyor->suusername) ? $surveyor->suusername : '',
								'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
								'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
								'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
								'status'=>!empty($surveyor->status) ? $surveyor->status : '0',
								'last_status'=>$surveyor->last_status,
								'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
								'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
								'image_url'=>$image,
								'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
								//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
								);

						
						$status=1;
						$message = 'You have successfully cancelled this survey. The other party will be notified.';
				}else{
					$message = 'Data Not Found.';
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

	public function surveyall(Request $request)
	{   $user_id =  (string)($request->input('user_id'));
		$surveyor_id =  (string)($request->input('surveyor_id'));
		$operator_id =  (string)($request->input('operator_id'));
		$status =  $request->input('status');
		$search =  $request->input('search');
		header('Content-Type: application/json');
		$statuss = 0;
		$message = NULL;
		$data = array();
		$data1 =array();
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);
		
		$r=1;
		if(!empty($user_id))
		{
			$user_d =  User::select('type')->where('id',$user_id)->first(); 
			if(!empty($user_d))
		    {
				if($user_d->type =="0" || $user_d->type =="1")
						{
	
							$upcoming_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
							DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
							DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
							DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
							'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
							'custom_survey_users.surveyors_id as surveyor_id',
							'vessels.name as vesselsname')
							->leftJoin('port', 'port.id', '=', 'survey.port_id')
							->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
							->leftJoin('users', 'users.id', '=', 'survey.user_id')
							->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
							->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
							->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
							->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
							->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
											
							if($operator_id !="")
							{
								$upcoming_survey_data=$upcoming_survey_data->Where('survey.assign_to_op',$operator_id);	
	

							}else
							{

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
									
									$createdbysurveyor =User::select('created_by')->where('id',$user_id)->first();
									$ids=array();
									$createdbydpsurveyor = User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
									if(!empty($createdbydpsurveyor)){
										
										foreach($createdbydpsurveyor as $data){
											$ids[]=$data->id;
										}
									}
										array_push($ids,$createdbysurveyor->created_by);
										$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($ids) {
											$query->WhereIn('survey.user_id',$ids)
											->orWhereIn('survey.assign_to_op',$ids);});	
								}

							}

							
								$upcoming_survey_data=$upcoming_survey_data->where(function ($query) {
									$query->where('survey.status', '=','0' )
											->orWhere('survey.status', '=', '1')
											->orWhere('survey.status', '=', '3');
								});

								if($search !="")
								{
									$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($search) {
										$query->where('vessels.name', 'like','%'.$search.'%' )
												->orWhere('port.port', 'like','%'.$search.'%' )
												->orWhere('survey.survey_number', 'like','%'.$search.'%' );
												
									});
									
								}

								$upcoming_survey_data=$upcoming_survey_data->where('survey.declined','0');
								// $upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
								// $upcoming_survey_data=$upcoming_survey_data->orderByRaw('survey.status = ? desc',['1']);
							
	
	
							$upcoming_survey_data=$upcoming_survey_data->groupBy('survey.id');
							$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.start_date','desc');
							$upcoming_survey_data=$upcoming_survey_data->get();
						}else
						{
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
								if($surveyor_id !="")
									{
												
										$u=	User::select('type')->where('id',$surveyor_id)->first();

										if($u->type =="3")
										{
											$upcoming_survey_data=$upcoming_survey_data->where('survey.assign_to',$surveyor_id);
										}else
										{
											$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($surveyor_id) {
												$query->Where('custom_survey_users.surveyors_id',$surveyor_id)
													->orwhere('survey_users.surveyors_id',$surveyor_id );});
													$upcoming_survey_data=$upcoming_survey_data->where('survey.assign_to','0');
										}
									}
									else
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
									}
	
							}else{
								
								$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($user_id) {
								$query->where('survey_users.surveyors_id', '=',$user_id )
								->orWhere('custom_survey_users.surveyors_id',$user_id)
								->orWhere('survey.assign_to',$user_id);});	
	
							}
				
								$upcoming_survey_data=$upcoming_survey_data->where(function ($query) {
									$query->where('survey.status', '=','0' )
											->orWhere('survey.status', '=', '1')
											->orWhere('survey.status', '=', '3');
								});
							$upcoming_survey_data=$upcoming_survey_data->where('survey.declined','0');							
							$upcoming_survey_data=$upcoming_survey_data->where(function ($query)  {
								$query->Where('survey_users.status','pending')
								->orwhere('survey_users.status','upcoming' )
								->orwhere('custom_survey_users.status','waiting' )
								->orwhere('custom_survey_users.status','upcoming' )
								->orwhere('custom_survey_users.status','approved' );});	
								$upcoming_survey_data=$upcoming_survey_data->groupBy('survey.id');
								$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.start_date','desc');
								
							$upcoming_survey_data=$upcoming_survey_data->get();
						}

			if($user_d->type =="0" || $user_d->type =="1")
			{

				$past_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
				DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
				DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
				DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
				'su.profile_pic as image','survey_users.surveyors_id as surveor_id',
				'custom_survey_users.surveyors_id as surveyor_id',
				'vessels.name as vesselsname')
				->leftJoin('port', 'port.id', '=', 'survey.port_id')
				->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
				->leftJoin('users', 'users.id', '=', 'survey.user_id')
				->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
				->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
				->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
				->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
				->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
				if($operator_id !="")
				{
					$past_survey_data=$past_survey_data->Where('survey.assign_to_op',$operator_id);	


				}else
				{

					if($user_d->type =="0")
					{
						$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
						$ids=array();
						foreach($createdbysurveyor as $data){
							$ids[]=$data->id;
						}
						array_push($ids,$user_id);
						$past_survey_data=$past_survey_data->where(function ($query) use ($ids) {
							$query->WhereIn('survey.user_id',$ids)
							->orWhereIn('survey.assign_to_op',$ids);});	

						//$upcoming_survey_data=$upcoming_survey_data->WhereIn('survey.user_id',$ids);
										
					}
					if($user_d->type =="1")
					{
						
						$createdbysurveyor =User::select('created_by')->where('id',$user_id)->first();
									$ids=array();
									$createdbydpsurveyor = User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
									if(!empty($createdbydpsurveyor)){
										
										foreach($createdbydpsurveyor as $data){
											$ids[]=$data->id;
										}
									}
										array_push($ids,$createdbysurveyor->created_by);
							$past_survey_data=$past_survey_data->where(function ($query) use ($ids) {
								$query->WhereIn('survey.user_id',$ids)
								->orWhereIn('survey.assign_to_op',$ids);});	
					}

				}
				
					$past_survey_data=$past_survey_data->where(function ($query) {
						$query->where('survey.status', '=','4' )
								->orWhere('survey.status', '=', '5')
								->orWhere('survey.status', '=', '2')
								->orWhere('survey.status', '=', '6')
								->orWhere('survey.declined','1');
					});
					if($search !="")
					{
						$past_survey_data=$past_survey_data->where(function ($query) use ($search) {
							$query->where('vessels.name', 'like','%'.$search.'%' )
									->orWhere('port.port', 'like','%'.$search.'%' )
									->orWhere('survey.survey_number', 'like','%'.$search.'%' );
									
						});
						
					}

				$past_survey_data=$past_survey_data->groupBy('survey.id');
				$past_survey_data=$past_survey_data->orderBy('survey.start_date','desc');
				$past_survey_data=$past_survey_data->get();
			}else
			{
				$past_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
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
					if($surveyor_id !="")
					{
								
						$u=	User::select('type')->where('id',$surveyor_id)->first();

						if($u->type =="3")
						{
							$past_survey_data=$past_survey_data->where('survey.assign_to',$surveyor_id);
						}else
						{
							$past_survey_data=$past_survey_data->where(function ($query) use ($surveyor_id) {
								$query->Where('custom_survey_users.surveyors_id',$surveyor_id)
									->orwhere('survey_users.surveyors_id',$surveyor_id );});
									$past_survey_data=$past_survey_data->where('survey.assign_to','0');
						}
					}
					else
					{
						$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
						$ids=array();
						foreach($createdbysurveyor as $data){
							$ids[]=$data->id;
						}
						array_push($ids,$user_id);
						$past_survey_data=$past_survey_data->where(function ($query) use ($ids) {
						$query->WhereIn('custom_survey_users.surveyors_id',$ids)
						->orwhereIn('survey_users.surveyors_id',$ids );});	
					}

						
				}else
				{
					//$past_survey_data=$past_survey_data->where('survey_users.surveyors_id',$user_id);
					// $past_survey_data=$past_survey_data->where(function ($query) use ($user_id) {
					// 	$query->where('survey_users.surveyors_id', '=',$user_id )
					// 	->orWhere('custom_survey_users.surveyors_id',$user_id);});
					$past_survey_data=$past_survey_data->where(function ($query) use ($user_id) {
						$query->where('survey_users.surveyors_id', '=',$user_id )
						->orWhere('custom_survey_users.surveyors_id',$user_id)
						->orWhere('survey.assign_to',$user_id);});
				}
				
					$past_survey_data=$past_survey_data->where(function ($query) {
						$query->where('survey.status', '=','4' )
						->orWhere('survey.status', '=', '2')
								->orWhere('survey.status', '=', '5')
								->orWhere('survey.status', '=', '6');
					});
				
				$past_survey_data=$past_survey_data->where(function ($query)  {
					$query->Where('survey_users.status','pending')
					->orwhere('survey_users.status','upcoming' )
					->orwhere('custom_survey_users.status','waiting' )
					->orwhere('custom_survey_users.status','upcoming' )
					->orwhere('custom_survey_users.status','approved' );});	
				$past_survey_data=$past_survey_data->groupBy('survey.id');
				$past_survey_data=$past_survey_data->orderByRaw('survey.status = ? desc',['4']);
				$past_survey_data=$past_survey_data->orderBy('survey.start_date','desc');
				$past_survey_data=$past_survey_data->get();
			}
							if(count($upcoming_survey_data)>0 || count($past_survey_data)>0)
							{$data1['active']=array();
								$data1['past']=array();
								foreach($upcoming_survey_data  as $surveyor)
								{    
									if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
										{
											$image = url('/').'/media/users/'.$surveyor->image;
										}else{
											$image ='';
										}
										// if($user_id==$surveyor->surveor_id && $surveyor->usstatus=="pending")
										// {
										// 	$status='0';
										// }else{
										// 	$status=$surveyor->status;
										// }
									
	
	
										$pricing="0";
										$pricing1="0";
										$pricing2="0";
										$surveyor_name="";
										$operator_name="";
									
										
										$userdata = User::where('id',$user_id)->first();
										if($surveyor->survey_type_id=='31')
										{ 
											if($userdata->type!="0" && $userdata->type!="1")
												{
													$survey_ch =  Customsurveyusers::where('survey_id',$surveyor->id)
													->Where('custom_survey_users.status','approved')->first();
												}else{
													$survey_ch =  Customsurveyusers::where('survey_id',$surveyor->id)
													->Where('custom_survey_users.status','approved')->first();
													
												}
												
												
												
										}else{		
												if($userdata->type!="0" && $userdata->type!="1")
												{
													$survey_ch =  Surveyusers::where('survey_id',$surveyor->id);
													$survey_ch=$survey_ch->where(function ($query)  {
															$query->Where('survey_users.status','pending')
															->orwhere('survey_users.status','upcoming' );});
															$survey_ch=$survey_ch->first();
															//dd($survey_ch);
												}else
												{
													$survey_chc =  Surveyusers::where('survey_id',$surveyor->id)->count();
													$survey_ch =  Surveyusers::where('survey_id',$surveyor->id);
													$survey_ch=$survey_ch->where(function ($query) use ($survey_chc) 
													{
															$query->Where('survey_users.status','pending')
															->orwhere('survey_users.status','upcoming' );
															if($survey_chc=='1'){
																$query->orwhere('survey_users.status','declined');
			
															}
			
														});
															$survey_ch=$survey_ch->first();
												}
											
														
											}
													
											
														// dd($survey_ch);
														$surveyor_id_id='';
														$suusername="";
												if(!empty($survey_ch))
												{
													if($surveyor->assign_to!="0"){$surveyor_id_id=$surveyor->assign_to;}else{$surveyor_id_id=$survey_ch->surveyors_id;}
													
													$suusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as suusername'),'company')->where('id',$surveyor_id_id)->first();
													
													if(!empty($suusername))
													{$surveyor_name=$suusername->suusername;}
													
													
														
												}
												if($surveyor->assign_to_op!="0"){$operator_id_id=$surveyor->assign_to_op;}else{$operator_id_id=$surveyor->user_id;}
														
												$opusername = User::select('id',DB::raw('CONCAT(first_name, "  ", last_name) as opusername'),'company')->where('id',$operator_id_id)->first();
	
													if(!empty($opusername))
													{$operator_name=$opusername->opusername;}
													
											if($user_id!=$surveyor_id_id)
											{
												$device_user = User::select('device_id')->where('id',$surveyor_id_id)->first();

											}
											if($user_id!=$operator_id_id)
											{$device_user = User::select('device_id')->where('id',$operator_id_id)->first();

											}
	
									$data1['active'][]=array('id'=>(string)($surveyor->id),
									'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
									'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
									'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
									'device_id'=>!empty($device_user->device_id) ? (string)$device_user->device_id : '',
									'operator_id'=>!empty($operator_id_id) ? (string)$operator_id_id : '',
									'username'=>!empty($operator_name) ? $operator_name : '',
	
									'surveyor_id'=>!empty($surveyor_id_id) ?(string)$surveyor_id_id :'',
	
									'surveyors_name'=>!empty($surveyor_name) ? $surveyor_name : '',
									'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
									'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
									'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
									'status'=>$surveyor->status,
										
									'last_status'=>$surveyor->last_status,
									'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
									'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
									'image_url'=>$image,
									'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
									//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
									);
	
							}
							foreach($past_survey_data  as $surveyor)
							{    
								if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
										{
											$image = url('/').'/media/users/'.$surveyor->image;
										}else{
											$image ='';
										}
										// if($user_id==$surveyor->surveor_id && $surveyor->usstatus=="pending")
										// {
										// 	$status='0';
										// }else{
										// 	$status=$surveyor->status;
										// }
									
	
	
										$pricing="0";
										$pricing1="0";
										$pricing2="0";
										$surveyor_name="";
										$operator_name="";
									
										
										$userdata = User::where('id',$user_id)->first();
										if($surveyor->survey_type_id=='31')
										{ 
											if($userdata->type!="0" && $userdata->type!="1")
												{
													$survey_ch =  Customsurveyusers::where('survey_id',$surveyor->id)
													->Where('custom_survey_users.status','approved')->first();
												}else{
													$survey_ch =  Customsurveyusers::where('survey_id',$surveyor->id)
													->Where('custom_survey_users.status','approved')->first();
													
												}
												
												
												
										}else{		
												if($userdata->type!="0" && $userdata->type!="1")
												{
													$survey_ch =  Surveyusers::where('survey_id',$surveyor->id);
													$survey_ch=$survey_ch->where(function ($query)  {
															$query->Where('survey_users.status','pending')
															->orwhere('survey_users.status','upcoming' );});
															$survey_ch=$survey_ch->first();
															//dd($survey_ch);
												}else
												{
													$survey_chc =  Surveyusers::where('survey_id',$surveyor->id)->count();
													$survey_ch =  Surveyusers::where('survey_id',$surveyor->id);
													$survey_ch=$survey_ch->where(function ($query) use ($survey_chc) 
													{
															$query->Where('survey_users.status','pending')
															->orwhere('survey_users.status','upcoming' );
															if($survey_chc=='1'){
																$query->orwhere('survey_users.status','declined');
			
															}
			
														});
															$survey_ch=$survey_ch->first();
												}
											
														
											}
													
											
														// dd($survey_ch);
														$surveyor_id_id='';
														$suusername="";
												if(!empty($survey_ch))
												{
													if($surveyor->assign_to!="0"){$surveyor_id_id=$surveyor->assign_to;}else{$surveyor_id_id=$survey_ch->surveyors_id;}
													
													$suusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as suusername'),'company')->where('id',$surveyor_id_id)->first();
													
													if(!empty($suusername))
													{$surveyor_name=$suusername->suusername;}
													
													
														
												}
												if($surveyor->assign_to_op!="0"){$operator_id_id=$surveyor->assign_to_op;}else{$operator_id_id=$surveyor->user_id;}
														
												$opusername = User::select('id',DB::raw('CONCAT(first_name, "  ", last_name) as opusername'),'company')->where('id',$operator_id_id)->first();
	
													if(!empty($opusername))
													{$operator_name=$opusername->opusername;}
											if($user_id!=$surveyor_id_id)
											{
												$device_user = User::select('device_id')->where('id',$surveyor_id_id)->first();

											}
											if($user_id!=$operator_id_id)
											{$device_user = User::select('device_id')->where('id',$operator_id_id)->first();

											}
											if(($user_d->type=='0' || $user_d->type=='1') && $surveyor->declined=='1')
											{
												$status='2';
											}else{
												$status=$surveyor->status;
											}
						$data1['past'][]=array('id'=>(string)($surveyor->id),
						'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
						'port'=>!empty($surveyor->port_name) ? $surveyor->port_name :'',
						'surveytype_name'=>!empty($surveyor->surveytype_name) ? $surveyor->surveytype_name :'',
						'device_id'=>!empty($device_user->device_id) ? (string)$device_user->device_id : '',
								'operator_id'=>!empty($surveyor->user_id) ? (string)$surveyor->user_id : '',
								'username'=>!empty($surveyor->username) ? $surveyor->username : '',
								'surveyor_id'=>!empty($surveyor_id_id) ?(string)$surveyor_id_id :'',

								'surveyors_name'=>!empty($surveyor->suusername) ? $surveyor->suusername : '',
								'agent_name'=>!empty($surveyor->agent_name) ? $surveyor->agent_name : '',
								'vesselsname'=>!empty($surveyor->vesselsname) ? $surveyor->vesselsname : '',
								'instruction'=>!empty($surveyor->instruction) ? $surveyor->instruction : '',
								'status'=>$status,				
								'last_status'=>$surveyor->last_status,
								'start_date'=>!empty($surveyor->start_date) ? $surveyor->start_date : '',
								'end_date'=>!empty($surveyor->end_date) ? $surveyor->end_date : '',
								'image_url'=>$image,
								'created_at'=> date("d/m/Y h:i:s A",strtotime($surveyor->created_at)),
								//'file_data'=>URL::to('/media/survey').'/'.$surveyor->file_data
								);

						}

							$statuss = 1;
							$message = 'Surveyor listing below.';

						
					}
					else{
						$data1 = (object) array();
						$message = 'Survey Data Not Found.';
					}
					
				}
				else{
					$message = 'Invalid User.';
				}
				
		}else {
			$message = 'One or more required fields are missing. Please try again.';
		}
		$response_data = array('status'=>$statuss,'message'=>$message,'data'=>$data1);
		echo json_encode(array('response' => $response_data));
		die;
	}
	
public function reportissuesurvey(Request $request)
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
		if(!empty($decoded['user_id']))
			{	
				$user_id=$decoded['user_id'];
				
				$user=User::where('id',$user_id)->first();
				if($user->type=='0' || $user->type=='1')
				{
					if($user->type=='0')
					{
						$survey_data=Survey::whereRaw("FIND_IN_SET('". $user_id."',user_id)")->get();
					}else
					{
						$createdbysurveyor =  User::select('created_by')->where('id',$user->id)->first();
						$survey_data=Survey::whereRaw("FIND_IN_SET('". $createdbysurveyor->created_by."',user_id)")->get();
					}
				}else
				{
					if($user->type=='2' ||  $user->type=='4')
					{
									
								$survey_data =  Survey::select('survey.id','survey.survey_number')
										->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
										->leftJoin('custom_survey_users', 'custom_survey_users.survey_id', '=', 'survey.id');
										
										if($user->type=='2')
										{
											$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
											$ids=array();
											foreach($createdbysurveyor as $data){
												$ids[]=$data->id;
											}
											array_push($ids,$user_id);

										}else{
											$ids=array();
											array_push($ids,$user_id);
										}
										
										$survey_data=$survey_data->where(function ($query) use ($ids) {
										$query->WhereIn('custom_survey_users.surveyors_id',$ids)
										->orwhereIn('survey_users.surveyors_id',$ids );});	
										$survey_data=$survey_data->where(function ($query) {
											$query->where('survey.status', '=','0' )
													->orWhere('survey.status', '=', '1')
													->orWhere('survey.status', '=', '2')
													->orWhere('survey.status', '=', '3')
													->orWhere('survey.status', '=', '4')
													->orWhere('survey.status', '=', '5')
													->orWhere('survey.status', '=', '6');
										});
				
									$survey_data=$survey_data->where(function ($query)  {
										$query->Where('survey_users.status','pending')
											->orwhere('survey_users.status','upcoming' )
											->orwhere('custom_survey_users.status','waiting' )
											->orwhere('custom_survey_users.status','upcoming' )
											->orwhere('custom_survey_users.status','approved' );});	
											$survey_data=$survey_data->groupBy('survey.id');
											$survey_data=$survey_data->get();
							
									
					}else{
								$survey_data=Survey::whereRaw("FIND_IN_SET('". $user_id."',assign_to)")
								->get();
					}
				}
						foreach($survey_data as $data){
							$data1[]=array('id'=>(string)($data->id),
							'survey_number'=>$data->survey_number,
						
							);
						}
				
				$status='1';
				$message = 'Survey Number List.';
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


	public function reportissuepost(Request $request)
	{
		
		$validator = Validator::make($request->all(), [
			'user_id'=> 'required',
			'survey_id'=> 'required',
			'comment' => 'required',
			
		]);
			if ($validator->fails()) 
			{
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);

			}else
			{
				$user_id =  $request->input('user_id');
				$survey_id =  $request->input('survey_id');
				$comment =  $request->input('comment');

				$Disputerequest=new Disputerequest;
				$Disputerequest->requested_id=$user_id;
				$Disputerequest->survey_id=$survey_id;
				$Disputerequest->comment=$comment;
				$file_data = $request->file('file');

				if(isset($file_data))
				{
					$imageName = time().$file_data->getClientOriginalName();
					$imageName =str_replace(" ", "", $imageName);
					$file_data->move(public_path().'/media/users/dispute-request', $imageName);
					$Disputerequest->file = $imageName;
				}

				$Disputerequest->save();

				$status=1;
				$message="Issue submitted… Customer service will contact you shortly…";
				
				$response_data = array('status'=>$status,'message'=>$message);
				echo json_encode(array('response' => $response_data));
				die;				
				
			}
	}

	public function logout(Request $request)
	{
		header('Content-Type: application/json');
		$status = 0;
		$message = NULL;
		$data = array();

		$user_locations = array();
		$sflag = '0';
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);
		//$decoded        = $_REQUEST;

		$auth_token = $request->header('auth');
		
			if ($decoded) {
				if (!empty($decoded['user_id'])) {
				
						$userdata = User::find($decoded['user_id']);

						if (!empty($userdata)) {

							$userdata->device_id = "";
							$userdata->save();
							$status = 1;
							$message ="Logout successfully";
						} else {
							$message ="User Not found";
						}
							/*else
						{
							$userdata->is_read = "0";
							$userdata->save();
							$status = 1;
							$message = "Notification Unread successfully.";
				
							
							
						}*/
				} else {
					$message = 'One or more required fields are missing Please try again';
				}
			} else {
				$message = 'Opps Something went wrong Please try again';
			}
		
		$response_data = array('status' => $status, 'message' => $message);
		echo json_encode(array('response' => $response_data));
		die;
	}

			public function chatEmail(Request $request)
			{

				header('Content-Type: application/json');
				$status = 0;
				$message = NULL;
				$data = array();

				$user_locations = array();
				$sflag = '0';
				$data_row 		= 	file_get_contents("php://input");
				$decoded 	    = 	json_decode($data_row, true);
				//$decoded        = $_REQUEST;

	
		
			if ($decoded) {
				if (!empty($decoded['sender_id']) && !empty($decoded['receiver_id']) && !empty($decoded['message'])) 
				{

					$sender_data = User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as susername'),'email')->where('id',$decoded['sender_id'])->first();
					$receiver_data = User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as rusername'),'email')->where('id',$decoded['receiver_id'])->first();
				//dd($sender_data);
					// $data1 = array( 'email' =>$receiver_data->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('name'=>$receiver_data->rusername,'content' => $sender_data->susername.' have sent you message "'.$decoded['message'].'"'));
					// Mail::send( 'pages.email.chatemail',$data1, function( $message ) use ($data1)
					// {
					// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Chat Message' );
	
					// });

					$emailData = Emailtemplates::where('slug','=','chat-message')->first();

			if($emailData){
				$textMessage = strip_tags($emailData->description);
				$subject = $emailData->subject;
				$to = $receiver_data->email;
				//echo env('MAIL_PASSWORD');exit;
				if($receiver_data->rusername!='' && $sender_data->susername!="" )
				{
					$textMessage = str_replace(array('SENDER_NAME','RECEIVER_NAME','MESSAGE_CONTENT'), array($sender_data->susername,$receiver_data->rusername,$decoded['message']),$textMessage);
					
					Mail::raw($textMessage, function ($messages) use ($to,$subject) {
						
						$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
					});
				}
			}


					$status = 1;
							$message ="Mail Sent successfully";

				} else {
					$message = 'One or more required fields are missing Please try again';
				}
			} else {
				$message = 'Opps Something went wrong Please try again';
			}
		
		$response_data = array('status' => $status, 'message' => $message);
		echo json_encode(array('response' => $response_data));
		die;
				
	}
	public function chatEmailw(Request $request)
	{

		$sender_id =  $request->input('sender_id');
		$receiver_id =  $request->input('receiver_id');
		$message =  $request->input('message');
		if (!empty($sender_id) && !empty($receiver_id) && !empty($message)) 
		{
			$sender_data = User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as susername'),'email')->where('id',$sender_id)->first();
			$receiver_data = User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as rusername'),'email')->where('id',$receiver_id)->first();
			$emailData = Emailtemplates::where('slug','=','chat-message')->first();

			if($emailData){
				$textMessage = strip_tags($emailData->description);
				$subject = $emailData->subject;
				$to = $receiver_data->email;

				if($receiver_data->rusername!='' && $sender_data->susername!="" )
				{
					$textMessage = str_replace(array('SENDER_NAME','RECEIVER_NAME','MESSAGE_CONTENT'), array($sender_data->susername,$receiver_data->rusername,$message),$textMessage);
					Mail::raw($textMessage, function ($messages) use ($to,$subject) {
						$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
					});
				}
			}
			$status = 1;
			$message ="Mail Sent successfully";

		} else {
			$message = 'One or more required fields are missing Please try again';
		}
	
		$response_data = array('status' => $status, 'message' => $message);
		echo json_encode(array('response' => $response_data));
		die;
		
	}

	public function userdetail(Request $request)
	{		
		header('Content-Type: application/json');
		$status = 0;
		$message = NULL;
		$data = array();
		$user_locations = array();
		$sflag = '0';
		$data_row 		= 	file_get_contents("php://input");
		$decoded 	    = 	json_decode($data_row, true);
		//$decoded        = $_REQUEST;
		if ($decoded) 
		{
			if (empty($decoded['user_id'])) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['status'=>0 ,'message'=>"User Id Required",'data'=>(object)array()]);
			}else
			{	
				$id =  $request->input('user_id');
				$surveyor_data =  User::select('users.*')->where('users.id',$id)->first();

				if($surveyor_data->type=='2')
				{
					$createdbysurveyor =  User::select('id')->where('created_by',$id)->get();
					$ids=array();
					if(!empty($createdbysurveyor)){
						foreach($createdbysurveyor as $data){
							$ids[]=$data->id;
						}
					}
					array_push($ids,$id);
					$comment_data =  Rating::select('rating.rating','rating.comment',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'))
							->leftJoin('users', 'users.id', '=', 'rating.operator_id')
							->whereIn('rating.surveyor_id',$ids)->get();

				}else{
						$comment_data =  Rating::select('rating.rating','rating.comment',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'))
						->leftJoin('users', 'users.id', '=', 'rating.operator_id')
						->where('rating.surveyor_id',$id)->get();
				}
						
					$status=1;
					$message = 'User Profile .';
					$data=array('id'=>$surveyor_data->id,
					'name'=>sprintf("%s %s",$surveyor_data->first_name,$surveyor_data->last_name),
					'about'=>$surveyor_data->about_me,
					'image'=>!empty($surveyor_data->profile_pic)  ? URL::to('/public/media/users').'/'.$surveyor_data->profile_pic :"",
					'experience'=>$surveyor_data->experience,
					'comment_data'=>$comment_data);
			}	
			$response_data = array('status' => $status, 'message' => $message,'data'=>$data);
			echo json_encode(array('response' => $response_data));
			die;
		}
	}

}