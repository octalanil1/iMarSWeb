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


use App\Helpers;
use App,Auth,Blade,Config,Cache,Cookie,DB,File,Ajax,Hash,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;

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
				$address =  $request->input('address');
				$same_as_company =  $request->input('same_as_company');
				$same_as_company_address =  $request->input('same_as_company_address');
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
							$user_info= User::find($user_id);
							$vessels->company=$user_info->company;

						}else{
							$vessels->company=$company;
						}
						if($same_as_company_address=='1')
						{$vessels->same_as_company_address=$same_as_company_address;
							$user_info= User::find($user_id);
							$vessels->address=$user_info->company_address;
						}else{
							$vessels->address=$address;
						}
						if(isset($image))
						{
							$imageName = time().$image->getClientOriginalName();
							$image->move(public_path().'/media/vessels', $imageName);
							$imageName =str_replace(" ", "", $imageName);
							$vessels->image = $imageName;
						}
						$vessels->save();
						 
						$data1=array('id'=>(string)($vessels->id),'name'=>$vessels->name,'user_id'=>(string)($vessels->user_id),
						'imo_number'=>$vessels->imo_number,'company'=>$vessels->company,'address'=>$vessels->address,
						'email'=>$vessels->email,'additional_email'=>$vessels->additional_email,'same_as_company'=>$vessels->same_as_company
						,'same_as_company_address'=>$vessels->same_as_company_address,
						'image'=>URL::to('/media/vessels').'/'.$vessels->image);
						$status=1;
						$message = 'Vessels added successfully.';
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
					$vessels_data_count =  Vessels::select('*')->where('user_id',$decoded['user_id'])->count();
					if(empty($vessels_data_count)){
						$r=0;
					}
					if($r==1)
					{
						$vessels_data =  Vessels::select('*')->where('user_id',$decoded['user_id'])->paginate(10);
						foreach($vessels_data  as $vessels )
						{
							$data1[]=array('id'=>(string)($vessels->id),
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
								if($same_as_company_address=='1')
								{
									$vessels->same_as_company_address=$same_as_company_address;
									$user_info= User::find($user_id);
									$vessels->address=$user_info->company_address;
								}else{
									$vessels->address=$address;
								}
								if(isset($image))
								{
									$imageName = time().$image->getClientOriginalName();
									$image->move(public_path().'/media/vessels', $imageName);
									$imageName =str_replace(" ", "", $imageName);
									$vessels->image = $imageName;
								}
								$vessels->save();
								
								$data1=array('id'=>(string)($vessels->id),'name'=>$vessels->name,'user_id'=>(string)($vessels->user_id),
								'imo_number'=>$vessels->imo_number,'company'=>$vessels->company,
								'address'=>$vessels->address,'email'=>$vessels->email,
								'additional_email'=>$vessels->additional_email,
								'same_as_company'=>$vessels->same_as_company
						        ,'same_as_company_address'=>$vessels->same_as_company_address,
								'image'=>URL::to('/media/vessels').'/'.$vessels->image);
								$status=1;
								$message = 'Vessels edited successfully.';
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
								  
								
								//echo '<pre>'; print_r($users); die;
								$data1=array('user_id'=>(string)($users->id),
								'first_name'=>$users->first_name,
								'last_name'=>$users->last_name,
								'country_code'=>$users->country_code,
								'email'=>$users->email,
								'company'=>$users->company,
								'address'=>$users->company_address,
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
							$survey_count =  Survey::where('user_id',$users->user_id)->count();

								
								//echo '<pre>'; print_r($users); die;
								$data1=array('user_id'=>(string)($users->id),
								'first_name'=>$users->first_name,
								'last_name'=>$users->last_name,
								'country_code'=>$users->country_code,
								'email'=>$users->email,
								'company'=>!empty($users->company) ? $users->company : '',
								'address'=>!empty($users->company_address) ? $users->company_address : '',
								'mailing_address'=>!empty($users->mailing_address) ? $users->mailing_address : '',

								//'designation'=>$users->designation,
								'profile_pic'=>URL::to('/media/users').'/'.$users->profile_pic,
								'company_website'=> !empty($users->company_website) ? $users->company_website : '',
								'mobile_number'=>!empty($users->mobile) ? $users->mobile : '',
								'country_id'=> !empty($users->country_id) ? $users->country_id : '',
								'about_me'=>!empty( $users->about_me) ?  $users->about_me: '',
								'company_tax_id'=>!empty($users->company_tax_id) ? $users->company_tax_id: '',
								'ssn'=>!empty($users->ssn) ? $users->ssn : '',
								'experience'=>!empty($users->experience) ? $users->experience: '',
								'rating'=>!empty($users->rating) ? $users->rating: '',
								'total_no_of_survey'=>!empty($survey_count) ? $survey_count: '',
								'average_response_time'=>!empty($users->average_response_time) ? $users->average_response_time: '',
								'percentage_job_acceptance'=>!empty($users->percentage_job_acceptance) ? $users->percentage_job_acceptance: '',
								
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
						'mobile'=>$agents->mobile,'email'=>$agents->email,'image'=>URL::to('/media/agents').'/'.$agents->image);
						$status=1;
						$message = 'Agent added successfully.';
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
					$agnets_data_count =  Agents::select('*')->where('user_id',$decoded['user_id'])->count();
					if(empty($agnets_data_count)){
						$r=0;
					}
					if($r==1)
					{
						$agents_data =  Agents::select('*')->where('user_id',$decoded['user_id'])->paginate(10);
						foreach($agents_data  as $agents )
						{
							$data1[]=array('id'=>(string)($agents->id),'first_name'=>$agents->first_name,
							'last_name'=>$agents->last_name,'user_id'=>(string)($agents->user_id),'mobile'=>$agents->mobile,'email'=>$agents->email,'image'=>URL::to('/media/agents').'/'.$agents->image);

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

						$vessels= Agents::where('id',$id)->where('user_id',$user_id)->first();
						if(!empty($vessels))
						{

								$agents= Agents::where('id',$id)->where('user_id',$user_id)->first();

								
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
								'last_name'=>$agents->last_name,'user_id'=>(string)($agents->user_id),'mobile'=>$agents->mobile,'email'=>$agents->email,'image'=>URL::to('/media/agents').'/'.$agents->image);
								$status=1;
								$message = 'Agent Edited successfully.';
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
						$data1=(object)array();
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
									if($usertypecheck->type=='2')
									{
										$create_url = \App::make('url')->to('/bycompany-surveyor-signup')."/".base64_encode($userArray->id);


									}else{
										$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

									}
									$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

									$data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));

										Mail::send( 'pages.email.add_operator_email_verify_for_signup',$data1, function( $message ) use ($data1)
										{
											$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

										});
						$status=1;
						$message = 'Operators added successfully.';

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
						$usertypecheck =  User::select('*')->where('id',$user_id)->first();
						if($usertypecheck->type=='2')
						{
							$create_url = \App::make('url')->to('/bycompany-surveyor-signup')."/".base64_encode($userArray->id);


						}else{
							$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

						}
						$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

						$data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));

							Mail::send( 'pages.email.add_operator_email_verify_for_signup',$data1, function( $message ) use ($data1)
							{
								$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

							});
							$status=1;
							$message = 'Ressend Request successfully.';

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
	{$data = array();
			$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			'edit_id' => 'required',
			'email' => 'required',
			
			]);
			if ($validator->fails()) 
			{
				return response()->json(['status'=>0 ,'message'=>"All Fields are required",'data'=>(object)array()]);
			}else
			{
				
				$email =  $request->input('email');
				$user_id =  $request->input('user_id');

				$edit_id =  $request->input('edit_id');
				$operators= User::where('id',$edit_id)->first();
				if(!empty($operators))
				{
					$userArray= User::where('id',$edit_id)->first();

					    $userArray->email_verify='0';
						$userArray->email=$email;
						$userArray->save();
						
						        $data['user_id'] = (string)$userArray->id;
								$data['first_name'] = !empty($userArray->first_name) ? $userArray->first_name: '';
								$data['last_name'] = !empty($userArray->last_name) ? $userArray->last_name: '';
								$data['email'] = $userArray->email;
								
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
									$usertypecheck =  User::select('*')->where('id',$user_id)->first();
									if($usertypecheck->type=='2')
									{
										$create_url = \App::make('url')->to('/bycompany-surveyor-signup')."/".base64_encode($userArray->id);


									}else{
										$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);

									}
									$data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));

										Mail::send( 'pages.email.add_operator_email_verify_for_signup',$data1, function( $message ) use ($data1)
										{
											$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

										});
						$status=1;
						$message = 'Operators Edited successfully.';
					}else{
						$status=0;
						$data1=(object)array();
						$message = 'Invalid data.';
					}
						
			}
					$response_data = array('status'=>$status,'message'=>$message,'data'=>$data);
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
						$Operators_data_count =  User::select('*')->where('created_by',$decoded['user_id'])->count();
						if(empty($Operators_data_count)){
							$r=0;
						}
						if($r==1)
						{
							$operators_data =  User::select('*')->where('created_by',$decoded['user_id'])->where('status','!=','0')->get();
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
								->whereBetween('events.start_event', [$decoded['start_date'], $decoded['end_date']])
								->where('events.title','1')
								->where('users.status','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','3' )
												->orWhere('users.type', '=', '4');
											
										})
							->where('users.conduct_custom', '=', '1')
							->where('users.is_avail','1')
								->count();
						}else
						{
							$surveyor_data_count = DB::table('users')->select('users.*',
							'users_survey_price.survey_price','users_port.cost')
								->leftJoin('users_survey_price', 'users_survey_price.user_id', '=', 'users.id')
								->leftJoin('users_port', 'users_port.user_id', '=', 'users.id')
								->leftJoin('events', 'events.user_id', '=', 'users.id')
								->where('users_survey_price.survey_type_id',$decoded['survey_category_id'])
								->where('users_port.port_id',$decoded['port_id'])
								->whereBetween('events.start_event', [$decoded['start_date'], $decoded['end_date']])
								->where('events.title','1')
								->where('users.status','1')
								->where('users.is_avail','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','3' )
												->orWhere('users.type', '=', '4')
												->orWhere('users.conduct_custom', '=', '1');
										})
								->count();
						

						}				 	
						
///
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
								->whereBetween('events.start_event', [$decoded['start_date'], $decoded['end_date']])
								->where('events.title','1')
								->where('users.status','1')
								->where('users.is_avail','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','3' )
												->orWhere('users.type', '=', '4');
											
										})
							->where('users.conduct_custom', '=', '1')
								->get();
						}else
						{
							$surveyor_data = DB::table('users')->select('users.*',
							'users_survey_price.survey_price','users_port.cost')
								->leftJoin('users_survey_price', 'users_survey_price.user_id', '=', 'users.id')
								->leftJoin('users_port', 'users_port.user_id', '=', 'users.id')
								->leftJoin('events', 'events.user_id', '=', 'users.id')
								->where('users_survey_price.survey_type_id',$decoded['survey_category_id'])
								->where('users_port.port_id',$decoded['port_id'])
								->whereBetween('events.start_event', [$decoded['start_date'], $decoded['end_date']])
								->where('events.title','1')
								->where('users.status','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','3' )
												->orWhere('users.type', '=', '4')
												->orWhere('users.conduct_custom', '=', '1');
										})
								->get();
						

						}	
							foreach($surveyor_data  as $surveyor )
							{
								// $helper=new Helpers;
								// $pricing=$helper->SurveyorPrice($decoded['survey_category_id'],$surveyor->user_id);	
								// $port_pricing=$helper->SurveyorPortPrice($decoded['port_id'],$surveyor->user_id);

								// $eventscheck=Events::select('events.*')->where('user_id',$surveyor->user_id)
								// ->whereDate('start_event', '>=', $decoded['start_date'])
								// ->whereDate('start_event', '<=', $decoded['end_date'])
								// ->where('title',1)
								// ->first();

									$data1[]=array('id'=>(string)($surveyor->id),
									'first_name'=>$surveyor->first_name,
									'last_name'=>$surveyor->last_name,
									//'user_id'=>(string)($operators->user_id),
									'mobile'=>$surveyor->mobile,
									'pricing'=>!empty( $surveyor->survey_price) ?  (string)$surveyor->survey_price : '',
									'rating'=>!empty($surveyor->rating) ? $surveyor->rating : '',
									'average_response_time'=>!empty($surveyor->average_response_time) ? $surveyor->average_response_time : '',
									'percentage_job_acceptance'=>!empty($surveyor->percentage_job_acceptance) ? $surveyor->percentage_job_acceptance : '',
									'email'=>$surveyor->email,
									'image'=>URL::to('/media/users').'/'.$surveyor->profile_pic);
							
								
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
						$survey= new Survey();
						$survey->user_id=$user_id;
						$survey->agent_id=$agent_id;
						$survey->port_id=$port_id;
						$survey->ship_id=$ship_id;
						$survey->start_date=$start_date;
						$survey->end_date=$end_date;
						$survey->survey_type_id=$survey_type_id;
						$survey->surveyors_id=$surveyors_id;
						$survey->instruction=$instruction;
						$survey->status=$status;
						//$survey->last_status=$last_status;
						$survey->survey_number = time();
						
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
					
					                    $message_token =  SurveyUsers::select('users.id','users.device_id')
										->leftJoin('users', 'survey_users.surveyors_id', '=', 'users.id')
										->where('survey_users.survey_id',$survey->id)
										->where('survey_users.type','1')
										->first();
										//echo $message_token->id;
										$helper=new Helpers;
										$helper->SendNotification($message_token->device_id,'Notification','Notification Body');
						$status=1;
						$message = 'Survey request send successfully.';
					
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
					
				if(!empty($surveyor_data) && !empty($survey_users))
				{

					$operator_token =  User::select('device_id')->where('id',$surveyor_data->user_id)->first(); 
					if($decoded['type']=="accept")
					{   $survey_users->status="upcoming";
						$surveyor_data->status='1';
						$surveyor_data->accept_by=$decoded['surveyors_id'];
						$helper=new Helpers;
						$helper->SendNotification($operator_token->device_id,'Accept Your Survey Request','Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.');

					}else
					{
						$survey_users->status="cancelled";
						$surveyor_data->status='2';						 
						$helper=new Helpers;
						$helper->SendNotification($operator_token->device_id,'Decline Your Survey Request','Your primary Surveyor has declined the Survey request. Your Substitute 1, Substitute 2, and other eligible Surveyors will be contacted in order automatically to fulfill your Surveyor request within 24 hours. You will be notified once a Surveyor is assigned to the Survey request
						');
						$survey_users_next = SurveyUsers::select('survey_users.*')->where('id', '>', $survey_users->id)->where('survey_users.survey_id',$decoded['survey_id'])->first();
						if(!empty($survey_users_next))
						{
							$survey_users_next->status='pending';
							$survey_users_next->save();
							$message_token =  User::select('device_id')->where('id',$survey_users_next->surveyors_id)->first(); 
							$helper=new Helpers;
							$helper->SendNotification($message_token->device_id,'Survey Request','Notification Body');
						}
					}
						$survey_users->save();
						$surveyor_data->save();
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
		$surveyor_data_count =  Survey::select('*')->count(); 
		if($surveyor_data_count > 0)
		{
			$user_d =  User::select('type')->where('id',$user_id)->first(); 
			if($user_d->type =="0" || $user_d->type =="1")
			{

				$upcoming_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
				DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
				DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
				DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
				'su.profile_pic as image','survey_users.surveyors_id as surveor_id','vessels.name as vesselsname')
				->leftJoin('port', 'port.id', '=', 'survey.port_id')
				->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
				->leftJoin('users', 'users.id', '=', 'survey.user_id')
				->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
				->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
				->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
				->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
				->orderBy('survey.id','desc');
								
				if($user_d->type =="0")
				{
					$createdbyopeartor =  User::select('id')->where('created_by',$user_id)->get(); 
					//dd($createdbyopeartor );
					$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($user_id,$createdbyopeartor) {
					$query->where('survey.user_id', '=',$user_id )->orwhereIn('survey.user_id',$createdbyopeartor);});
					
									
				}
				if($user_d->type =="1")
				{
					$upcoming_survey_data=$upcoming_survey_data->where('survey.user_id',$user_id);
					//$upcoming_survey_data=$upcoming_survey_data->whereIn('survey_users.surveyors_id',$createdbyopeartor );

				}
				if($status!=""){
					$upcoming_survey_data=$upcoming_survey_data->where('survey.status',$status);
				}else{
					$upcoming_survey_data=$upcoming_survey_data->where(function ($query) {
						$query->where('survey.status', '=','0' )
								->orWhere('survey.status', '=', '1')
								->orWhere('survey.status', '=', '3');
					});
				}
				$upcoming_survey_data=$upcoming_survey_data->groupBy('survey_users.survey_id');
				$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.id','desc');
				$upcoming_survey_data=$upcoming_survey_data->get();
			}else
			{
				$upcoming_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
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
				->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id');

				if($user_d->type =="2")
				{
					$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 

					$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($user_id,$createdbysurveyor) {
					$query->where('survey_users.surveyors_id', '=',$user_id )
					->orwhereIn('survey_users.surveyors_id',$createdbysurveyor );});	


					//$upcoming_survey_data=$upcoming_survey_data->whereIn('survey_users.surveyors_id',$createdbysurveyor );

				}else{
					$upcoming_survey_data=$upcoming_survey_data->where('survey_users.surveyors_id',$user_id);
				}
	

				if($status!=""){
					$upcoming_survey_data=$upcoming_survey_data->where('survey.status',$status);
				}else{
					$upcoming_survey_data=$upcoming_survey_data->where(function ($query) {
						$query->where('survey.status', '=','0' )
								->orWhere('survey.status', '=', '1')
								->orWhere('survey.status', '=', '3');
					});
				}
				$upcoming_survey_data=$upcoming_survey_data->groupBy('survey_users.survey_id');
				$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.id','desc');
				$upcoming_survey_data=$upcoming_survey_data->get();
			}
							//dd($surveyor_data);
				foreach($upcoming_survey_data  as $surveyor)
				{    
					if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
						{
							$image = url('/').'/media/users/'.$surveyor->image;
						}else{
							$image ='';
						}
						if($user_id==$surveyor->surveor_id && $surveyor->usstatus=="pending")
						{
							$status='0';
						}else{
							$status=$surveyor->status;
						}
					
					$data1[]=array('id'=>(string)($surveyor->id),
					'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
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

			}
			$statuss = 1;
			$message = 'Surveyor listing below.';
		}
		else{
			$message = 'Data not found.';
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
		$surveyor_data_count =  Survey::select('*')->count(); 
		if($surveyor_data_count > 0)
		{
			$user_d =  User::select('type')->where('id',$user_id)->first(); 
			if($user_d->type =="0" || $user_d->type =="1")
			{

				$past_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
				DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
				DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
				DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'), 
				'su.profile_pic as image','survey_users.surveyors_id as surveor_id','vessels.name as vesselsname')
				->leftJoin('port', 'port.id', '=', 'survey.port_id')
				->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
				->leftJoin('users', 'users.id', '=', 'survey.user_id')
				->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
				->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
				->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
				->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id');
				if($user_d->type =="0")
				{
					
					$createdbyopeartor =  User::select('id')->where('created_by',$user_id)->get(); 
					//dd($createdbyopeartor );
					$past_survey_data=$past_survey_data->where(function ($query) use ($user_id,$createdbyopeartor) {
					$query->where('survey.user_id', '=',$user_id )->orwhereIn('survey.user_id',$createdbyopeartor);});
					
				}
				if($user_d->type =="1")
				{
				

					$past_survey_data=$past_survey_data->where('survey.user_id',$user_id);

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

				$past_survey_data=$past_survey_data->groupBy('survey_users.survey_id');
				$past_survey_data=$past_survey_data->orderBy('survey.id','desc');
				$past_survey_data=$past_survey_data->get();
			}else
			{
				$past_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
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
				->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id');
				if($user_d->type =="2")
				{
					$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 

					$past_survey_data=$past_survey_data->where(function ($query) use ($user_id,$createdbysurveyor) {
					$query->where('survey_users.surveyors_id', '=',$user_id )
					->orwhereIn('survey_users.surveyors_id',$createdbysurveyor );});	

						
				}else
				{
					$past_survey_data=$past_survey_data->where('survey_users.surveyors_id',$user_id);
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

				$past_survey_data=$past_survey_data->groupBy('survey_users.survey_id');
				$past_survey_data=$past_survey_data->orderBy('survey.id','desc');
				$past_survey_data=$past_survey_data->get();
			}
							//dd($surveyor_data);
				foreach($past_survey_data  as $surveyor)
				{    
					if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
						{
							$image = url('/').'/media/users/'.$surveyor->image;
						}else{
							$image ='';
						}
						if($user_id==$surveyor->surveor_id && $surveyor->usstatus=="pending")
						{
							$status='0';
						}else{
							$status=$surveyor->status;
						}
					
					$data1[]=array('id'=>(string)($surveyor->id),
					'survey_number'=>!empty($surveyor->survey_number) ? $surveyor->survey_number :'',
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

			}
			$statuss = 1;
			$message = 'Surveyor listing below.';
		}
		else{
			$message = 'Data not found.';
		}
		$response_data = array('status'=>$statuss,'message'=>$message,'data'=>$data1);
		echo json_encode(array('response' => $response_data));
		die;
	}
	
	
	 public function surveyDetails(Request $request)
		{   $survey_id =  (string)($request->input('id'));
			header('Content-Type: application/json');
			$status = 0;
			$message = NULL;
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
			//if($decoded) 
			//{
					
					if(!empty($survey_id))
					{	// echo $user_id; die;
						//$r=1;
						// $surveyor_data_count =  Survey::select('*')->count(); 
						//if($surveyor_data_count > 0){
							
							$surveyor_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
							DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'),
							'users.company as operator_company'
							,'users.company_website as operator_company_website',
							'users.country_id as user_country_id','users.id as operator_id',
							DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
							'su.id as surveyor_id',
							DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
							 'su.profile_pic as image','survey_users.surveyors_id as surveyors_ids', 
							 'vessels.name as vesselsname', 
							 'vessels.email as vesselsemail', 'vessels.address as vesselsaddress',
							  'vessels.company as vesselscompany', 'agents.email as agentsemail',
							   'agents.mobile as agentsmobile','vessels.imo_number')
							->leftJoin('port', 'port.id', '=', 'survey.port_id')
							->leftJoin('survey_type', 'survey_type.id', '=', 'survey.survey_type_id')
							->leftJoin('users', 'users.id', '=', 'survey.user_id')
							->leftJoin('agents', 'agents.id', '=', 'survey.agent_id')
							->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
							->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
							->leftJoin('users as su', 'su.id', '=', 'survey_users.surveyors_id')
							->groupBy('survey_users.survey_id')
							->where('survey.id',$survey_id)
							->first();
							
							if(!empty($surveyor_data))
							{
							
							//foreach($surveyor_data  as $surveyor)
							//{    
							   if($surveyor_data->image &&  file_exists(public_path('/media/users/').$surveyor_data->image ))
									{
										$image = url('/').'/media/users/'.$surveyor_data->image;
									}else{
										$image ='';
									}
								$operator_survey_count =  Survey::where('user_id',$surveyor_data->user_id)->count();
								$country_data =  Countries::where('id',$surveyor_data->user_country_id)->first();
//dd($country_data);

                        $helper=new Helpers;
						//echo $surveyor_data->surveyors_ids;exit;

						 $pricing=$helper->SurveyorPrice($surveyor_data->survey_type_id,$surveyor_data->surveyors_ids);						
								
								$data1[]=array('id'=>(string)($surveyor_data->id),
								'survey_number'=>!empty($surveyor_data->survey_number) ? $surveyor_data->survey_number :'',
								'port'=>!empty($surveyor_data->port) ? $surveyor_data->port :'',
								'surveytype_name'=>!empty($surveyor_data->surveytype_name) ? $surveyor_data->surveytype_name :'',
								'pricing'=>!empty($pricing) ? (string)$pricing : '',
								'surveyor_id'=>!empty($surveyor_data->surveyor_id) ? $surveyor_data->surveyor_id : '',

								'surveyors_name'=>!empty($surveyor_data->suusername) ? $surveyor_data->suusername : '',
								'instruction'=>!empty($surveyor_data->instruction) ? $surveyor_data->instruction : '',
								'file_data'=>  !empty($surveyor_data->file_data) ?  URL::to('/media/survey').'/'.$surveyor_data->file_data : '',
								'start_date'=>!empty($surveyor_data->start_date) ? $surveyor_data->start_date : '',
								'end_date'=>!empty($surveyor_data->end_date) ? $surveyor_data->end_date : '',
								'status'=>$surveyor_data->status,
								
								'vesselsname'=>!empty($surveyor_data->vesselsname) ? $surveyor_data->vesselsname : '',
								'vesselsemail'=>!empty($surveyor_data->vesselsemail) ? $surveyor_data->vesselsemail : '',
								'vesselsaddress'=>!empty($surveyor_data->vesselsaddress) ? $surveyor_data->vesselsaddress : '',
								'vesselscompany'=>!empty($surveyor_data->vesselscompany) ? $surveyor_data->vesselscompany : '',
								'imo_number'=>!empty($surveyor_data->imo_number) ? $surveyor_data->imo_number : '',
								'agent_name'=>!empty($surveyor_data->agent_name) ? $surveyor_data->agent_name : '',
								'agentsemail'=>!empty($surveyor_data->agentsemail) ? $surveyor_data->agentsemail : '',
								'agentsmobile'=>!empty($surveyor_data->agentsmobile) ? $surveyor_data->agentsmobile : '',
								'last_status'=>$surveyor_data->last_status,
								'operator_id'=>!empty($surveyor_data->operator_id) ? $surveyor_data->operator_id : '',

								'operator_name'=>!empty($surveyor_data->operator_name) ? $surveyor_data->operator_name : '',
								'operator_company'=>!empty($surveyor_data->operator_company) ? $surveyor_data->operator_company : '',
								'operator_company_website'=>!empty($surveyor_data->operator_company_website) ? $surveyor_data->operator_company_website : '',
								'operator_survey_count'=>!empty($operator_survey_count) ? $operator_survey_count : '',
								'operator_country_name'=>!empty($country_data->name) ? $country_data->name : '',
								'operator_average_invoice_payment_time'=>"24 days",


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
				
				$notification_data = Notification::where('user_id',$decoded['user_id'])
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
									$message = "Notification Delete successfully.";
								}else{
									 $message = "Notification not found.";
								}	
								
						
						} else{
							$notificationdata = DB::table('notification')->where('user_id',$decoded['user_id'])->where('id',$decoded['notification_id'])->delete();
							if(!empty($notificationdata))
								{
									$status = 1;
									$message = "Notification Delete successfully.";
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
				$port_data =  Port::select('*')->get();
				$vessels_data =  Vessels::select('*')->get();
				$surtype_data =  Surveytype::select('*')->where('status',1)->get();
				$agent_data =    Agents::select('*')->get();
				if(!empty($surtype_data) && !empty($port_data) && !empty($vessels_data))
				{
						foreach($surtype_data  as $data )
						{
							$data1['type_data'][] =array('id'=>(string)($data->id),
							'name'=>$data->name,
							'price'=>$data->price);
						}
						
						foreach($port_data  as $datas)
						{
							$data1['port_data'][]=array('id'=>(string)($datas->id),
							'country_id'=>!empty($datas->country_id) ? $datas->country_id : '',
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
	$data1 =array();
	$data_row 		= 	file_get_contents("php://input");
	$decoded 	    = 	json_decode($data_row, true);
	if($decoded) 
	{
		if (!empty($decoded['user_id'])) 
		{

				   $user = Auth::user();
				  // $events=Events::select('events.id as id','events.title','events.start_event','events.end_event')->where('user_id',$decoded['user_id'])->get();
				  
				   $surveyor_data =  Survey::select('survey.id as id','survey.survey_number as title','survey.start_date as start_event',
				   'survey.end_date as end_event')	
				   ->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
				   ->groupBy('survey_users.survey_id')
				   ->where(function ($query) {
				   $query->where('survey_users.status', '=','pending' )
				   ->orWhere('survey_users.status', '=', 'upcoming');
				   })
				   ->where('survey_users.surveyors_id',$decoded['user_id'])
				   ->get();

				  
				   foreach($surveyor_data as $data)
				   {
					   $events=Events::select('events.id as id','events.title','events.start_event','events.end_event')
					   ->where('user_id',$decoded['user_id'])
					    ->whereNotBetween('start_event', [$data->start_event, $data->end_event])
					   ->get();
					}
					//dd($events);
				foreach($events as $event)
				{
					$surveyor_data->add($event);
				}
					   
				   
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
	                $status = 1;
					$message = 'Event List below.';
		}else {
			$message = 'One or more required fields are missing. Please try again.';
		}
	}else 
	{
		$message = 'Opps! Something went wrong. Please try again.';
	}
	$response_data = array('status'=>$status,'message'=>$message,'data'=>$data);
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
				
				$surtype_data =  Surveytype::select('*')->where('status',1)->get();
			if (!empty($decoded['user_id'])) 
			{
				if(!empty($surtype_data) )
				{
				
						foreach($surtype_data  as $data )
						{
							$user_type_detail =   UsersSurveyPrice::select('survey_price')->where('survey_type_id',$data->id)->where('user_id',$decoded['user_id'])->first();
							if(!empty($user_type_detail->survey_price))
							{
								$price=$user_type_detail->survey_price;
							}else{
								$price="";
							}

							$data1[] =array('id'=>(string)($data->id),
							'name'=>$data->name,
							'price'=>(string)$price);
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
				
				$user_port_data =  DB::table('users_port')->select('*')->where('user_id',$decoded['user_id'])->get();
				if (!empty($decoded['user_id'])) 
				{
					if(!empty($user_port_data) )
					{
				


						foreach($user_port_data  as $data )
						{	$port_data =   Port::select('port')->where('id',$data->port_id)->first();

							if(!empty($data->cost))
							{
								$price=$data->cost;
							}else{
								$price="";
							}

							$data1[] =array('id'=>(string)($data->id),
							'name'=>$port_data->port,
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
					$survey->save();
					$helper=new Helpers;
					$operator_token =  User::select('device_id')->where('id',$survey->user_id)->first(); 
					$helper->SendNotification($operator_token->device_id,'Survey report has been submitted',' Survey report has been submitted.');
					
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
					->groupBy('survey_users.survey_id')
					->where(function ($query) 
					{
						$query->where('survey_users.status', '=','pending' )
										  ->orWhere('survey_users.status', '=', 'upcoming');
					})
					->where('survey.id',$survey_id)
					->first();
					 if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
					{
						$image = url('/').'/media/users/'.$surveyor->image;
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
					$message = 'Report submit succesfully.';
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
					$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])
					->where('survey.user_id',$decoded['operator_id'])->first();
					if(!empty($surveyor_data) )
					{
						$surveyor_token =  User::select('device_id')->where('id',$surveyor_data->accept_by)->first(); 
						  
							$surveyor_data->status='4';
							$helper=new Helpers;
							$helper->SendNotification($surveyor_token->device_id,' survey report has been accepted',' survey report has been accepted.');

							$surveyor_data->save();
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
								->groupBy('survey_users.survey_id')
								->where(function ($query) 
								{
									$query->where('survey_users.status', '=','pending' )
													->orWhere('survey_users.status', '=', 'upcoming');
								})
								->where('survey.id',$decoded['survey_id'])
								->first();
								if($surveyor->image &&  file_exists(public_path('/media/users/').$surveyor->image ))
								{
									$image = url('/').'/media/users/'.$surveyor->image;
								}else
								{
									$image ='';
								}
								
									$status=$surveyor->status;
								
										
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
							$status = 1;
							$message = 'Report Accept Successfully.';
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
			$user_data =  User::select('*')->where('status','1')
			->where('created_by',$decoded['user_id'])
			->where('is_signup','1')
			->get();
			
			if(!empty($user_data) )
			{
			
					foreach($user_data  as $data )
					{
						$data1[] =array('id'=>(string)($data->id),
						'name'=>$data->first_name.' '.$data->last_name,
						'mobile'=>(string)$data->mobile);
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
			
		if (!empty($decoded['operator_id']) && !empty($decoded['surveyor_id']) && !empty($decoded['rating'])) 
		{
					$check =  Rating::select('*')->where('operator_id',$decoded['operator_id'])->where('surveyor_id',$decoded['surveyor_id'] )->first(); 
						
					if(empty($check))
				{
					$rating =  new Rating ;
										
					$rating->operator_id=$decoded['operator_id'];
					$rating->surveyor_id=$decoded['surveyor_id'];
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
			$data = array();
			$data1 =array();
			$data_row 		= 	file_get_contents("php://input");
			$decoded 	    = 	json_decode($data_row, true);
		//	$category_data =  Surveycategory::select('*')->get();
			
		if (!empty($decoded['user_id'])) 
		{
			
			
			      $data1['paid'][] =array('id'=>'1',
						'survey_number'=>'201910160001',
						'invoice_date'=>'2019-10-16 00:00:00',
						'invoice_amount'=>'400',
						'vessels_name'=>'new',
						'port_name'=>'Durres',
						'survey_code'=>'ss',
						'status'=>'paid',
						'invoice'=>'1.pdf'
		);
						$data1['unpaid'][] =array('id'=>'2',
						'survey_number'=>'201910160002',
						'invoice_date'=>'2019-10-16 00:00:00',
						'invoice_amount'=>'400',
						'vessels_name'=>'new',
						'port_name'=>'Durres',
						'survey_code'=>'ss',
						'status'=>'unpaid',
						'invoice'=>'1.pdf'
				);
					
					
					
					$status = 1;
					$message = 'Finance Data below.';
				
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
						$survey= new Survey();
						$survey->user_id=$user_id;
						$survey->agent_id=$agent_id;
						$survey->port_id=$port_id;
						$survey->ship_id=$ship_id;
						$survey->start_date=$start_date;
						$survey->end_date=$end_date;
						$survey->survey_type_id=$survey_type_id;
						$survey->surveyors_id=$surveyors_id;
						$survey->instruction=$instruction;
						$survey->status=$status;
						//$survey->last_status=$last_status;
						$survey->survey_number = time();
						
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

							$message_token =  User::select('users.id','users.device_id')->where('users.id',$value)
							->first();
							//echo $message_token->id;
							$helper=new Helpers;
							$helper->SendNotification($message_token->device_id,'Notification','Notification Body');
							
						}
					
					                   
						$status=1;
						$message = 'Survey request send successfully.';
					
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
				
			if(!empty($decoded['surveyors_id']) && !empty($decoded['survey_id']) && !empty($decoded['type']) && !empty($decoded['amount']))
			{
				$survey_users =  Customsurveyusers::select('custom_survey_users.*')
				->where('custom_survey_users.survey_id',$decoded['survey_id'])
				->where('custom_survey_users.surveyors_id',$decoded['surveyors_id'])
				->first();
				$surveyor_data =  Survey::select('survey.*')->where('survey.id',$decoded['survey_id'])->first();
					
				if(!empty($surveyor_data) && !empty($survey_users))
				{

					$operator_token =  User::select('device_id')->where('id',$surveyor_data->user_id)->first(); 
					if($decoded['type']=="accept")
					{   $survey_users->status="upcoming";
						$survey_users->amount=$decoded['amount'];
						
						$helper=new Helpers;
						$helper->SendNotification($operator_token->device_id,'Accept Your Survey Request','Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.');

					}else
					{
						$survey_users->status="cancelled";
												 
						$helper=new Helpers;
						$helper->SendNotification($operator_token->device_id,'Decline Your Survey Request','Your primary Surveyor has declined the Survey request. Your Substitute 1, Substitute 2, and other eligible Surveyors will be contacted in order automatically to fulfill your Surveyor request within 24 hours. You will be notified once a Surveyor is assigned to the Survey request
						');
						
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

						$helper=new Helpers;
						$surveyor_token =  User::select('device_id')->where('id',$decoded['surveyors_id'])->first(); 

						$helper->SendNotification($surveyor_token->device_id,'Accept Your Survey Request','Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.');

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
	
}