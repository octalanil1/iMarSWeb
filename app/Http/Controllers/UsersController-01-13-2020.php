<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Validator;
use App\User;
use App\Models\Contact;
use App\Models\Login;
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
use App\Models\Notification;
use App\Models\Chat;
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

class UsersController extends Controller 
{

	public function signup()
	{
		$user = Auth::user();
		if (Auth::check() && $user->is_admin=='0')
		{
			return redirect('/myaccount');
		}else{
			return view('pages.signup');
		}

	}
	public function operatorsignup()
	{
		$user = Auth::user();
		if (Auth::check() && $user->is_admin=='0')
		{
			return redirect('/myaccount');
		}else{
			return view('pages.operator-signup');
		}

	}
	public function individualoperatorsignup($id)
	{
		$user = Auth::user();
		if (Auth::check() && $user->is_admin=='0')
		{
			return redirect('/myaccount');
		}else
		{
			$user = User::where('id','=', base64_decode($id))->first();
			$user->email_verify = '1';
			$user->save();
			if($user->is_signup=='1'){
				return view('pages.individual-operator-email-verify',['user'=>$user]);

			}else{
				return view('pages.individual-operator-signup',['user'=>$user]);

			}

		}

	}

	public function bycompanysurveyorsignup($id)
	{
		$user = Auth::user();
		if (Auth::check() && $user->is_admin=='0')
		{
			return redirect('/myaccount');
		}else
		{
			$user = User::where('id','=', base64_decode($id))->first();
			$user->email_verify = '1';
			$user->save();
			if($user->is_signup=='1'){
				return view('pages.bycompany-surveyor-email-verify',['user'=>$user]);

			}else{
				return view('pages.bycompany-surveyor-signup',['user'=>$user]);

			}

		}

	}

	public function surveyorsignup()
	{
		$user = Auth::user();
		if (Auth::check() && $user->is_admin=='0')
		{
			return redirect('/myaccount');
		}else{
			return view('pages.surveyor-signup');
		}

	}


	public function individualsurveyorsignup()
	{
		$user = Auth::user();
		if (Auth::check() && $user->is_admin=='0')
		{
			return redirect('/myaccount');
		}else{
			return view('pages.individual-surveyor-signup');
		}

	}
	public function companysurveyorsignup()
	{
		$user = Auth::user();
		if (Auth::check() && $user->is_admin=='0')
		{
			return redirect('/myaccount');
		}else{
			return view('pages.company-surveyor-signup');
		}

	}
	
	

	public function individualoperatorsignuppost(Request $request)
	{

		$rules=array('id' => 'required',
		'country' => 'required',
		'mobile' => 'required|numeric',
		'first_name' => 'required|max:50',
		'last_name' => 'required|max:50',
		'email' => 'required|email|unique:users,email,'.base64_decode($request->input('id')),
		'password' => 'required|min:6|max:16|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
		'confirm_password' => 'required|min:6|max:16|same:password',
		'terms_conditions' => 'required',);
		
		
		$messages = array( 'password.regex' => 'Password must be between 8 and 16 characters & contain one lower & uppercase letter, and one non-alpha character (a number or a symbol.)',
		 );

		$validator = Validator::make($request->all(),$rules,$messages);

		

		if ($validator->fails()) {
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else
		{
				
			

				$first_name =  $request->input('first_name');
				$id =  $request->input('id');

				$last_name =  $request->input('last_name');
				$email =  $request->input('email');
				$mobile =  $request->input('mobile');
				$password =  $request->input('password');
				
				$country_id =  $request->input('country');
				$user = User::where('id','=', base64_decode($id))->first();
				//dd($user);
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->mobile = $mobile;
				if($country_id!=""){
					$user->country_id = $country_id;
					$countrydata = Countries::where('id',$country_id)->first();
					$user->country_code = $countrydata->phonecode;
				}
				
				
		
			$user->password = Hash::make($password);
			$user->type = '1';
			$user->email_verify = '1';
			$user->is_signup = '1';
			
			$user->save();
			$create_url = \App::make('url')->to('/verify-email')."/".base64_encode($user->id);
			
			$data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));
				Mail::send( 'pages.email.emailconfirm',$data1, function( $message ) use ($data1)
				{
					$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

				});
			return response()->json(['class'=>'success' ,'message'=>'You have successfully signed up.']);
		}
	}

	public function operatorsignuppost(Request $request)
	{

		$country_id =  $request->input('country');
		$company_tax_id =  $request->input('company_tax_id');
		$rules=array('country' => 'required',
		'mobile' => 'required|numeric',
		'company' => 'required|max:50',
		'company_tax_id' => 'required|max:50',
		'first_name' => 'required|max:50',
		'last_name' => 'required|max:50',
		'email' => 'required|email|unique:users',
		'password' => 'required|min:6|max:16|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
		'confirm_password' => 'required|min:6|max:16|same:password',
		'terms_conditions' => 'required',);
		
		
		$messages = array( 'password.regex' => 'Password must be between 8 and 16 characters & contain one lower & uppercase letter, and one non-alpha character (a number or a symbol.)',
		 );

		$validator = Validator::make($request->all(),$rules,$messages);
        $company_tax_check=User::where('country_id',$country_id)->where('company_tax_id',$company_tax_id)->count();

		if ($validator->fails()) 
		{
				if($company_tax_check>0)
				{
					return response()->json(['success'=>false ,'message'=>false,'errors'=>array('company_tax_id'=>'company tax id has already been taken')]);
				}
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else
		{
			$user = new User;
			
			
				$first_name =  $request->input('first_name');
				$last_name =  $request->input('last_name');
				$email =  $request->input('email');
				$mobile =  $request->input('mobile');
				$password =  $request->input('password');
				$company =  $request->input('company');
				
				
				
				$company_website =  $request->input('company_website');
				$country_id =  $request->input('country');
				
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->mobile = $mobile;
				$user->is_signup = '1';
				if($country_id!=""){
					$user->country_id = $country_id;
					$countrydata = Countries::where('id',$country_id)->first();
					$user->country_code = $countrydata->phonecode;
				}
				
				if($company_tax_id!=""){
					$user->company_tax_id = $company_tax_id;
				}
				if($company!=""){
					$user->company = $company;
				}
				
				
				
				if($company_website!=""){
					$user->company_website = $company_website;
				}
				
		
			$user->password = Hash::make($password);
			$user->save();
			Session::put('signup_user_id', $user->id);

			$create_url = \App::make('url')->to('/verify-email')."/".base64_encode($user->id);
			
			$data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));
				Mail::send( 'pages.email.emailconfirm',$data1, function( $message ) use ($data1)
				{
					$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

				});

			return response()->json(['class'=>'success' ,'message'=>'You have successfully signed up.Please verify your email']);
		}
	}

	public function individualsurveyorsignuppost(Request $request)
	{

		$rules=array(	'country' => 'required',
		'mobile' => 'required|numeric',

		'first_name' => 'required|max:50',
		'last_name' => 'required|max:50',
		'email' => 'required|email|unique:users|max:50',
		'password' => 'required|min:6|max:16|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
		'confirm_password' => 'required|min:6|max:16|same:password',
		'terms_conditions' => 'required',);
		
		
		$messages = array( 'password.regex' => 'Password must be between 8 and 16 characters & contain one lower & uppercase letter, and one non-alpha character (a number or a symbol.)',
		 );

		$validator = Validator::make($request->all(),$rules,$messages);



		if ($validator->fails()) {
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else
		{
			$user = new User;
			
			
				$first_name =  $request->input('first_name');
				$last_name =  $request->input('last_name');
				$email =  $request->input('email');
				$mobile =  $request->input('mobile');
				$password =  $request->input('password');
			
				$country_id =  $request->input('country');
				
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->mobile = $mobile;
				$user->is_signup = '1';
				$user->type = '4';
				$user->email_verify = '1';
		
			
				if($country_id!=""){
					$user->country_id = $country_id;
					$countrydata = Countries::where('id',$country_id)->first();
					$user->country_code = $countrydata->phonecode;
				}
				
				
		
			$user->password = Hash::make($password);
			$user->save();
			$create_url = \App::make('url')->to('/verify-email')."/".base64_encode($user->id);
			
			$data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));
				Mail::send( 'pages.email.emailconfirm',$data1, function( $message ) use ($data1)
				{
					$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

				});
			return response()->json(['class'=>'success' ,'message'=>'You have successfully signed up.']);
		}
	}
	public function bycompanysurveyorsignuppost(Request $request)
	{
		


		$rules=array('id' => 'required',
		'country' => 'required',
		'mobile' => 'required|numeric',

		'first_name' => 'required|max:50',
		'last_name' => 'required|max:50',
		'email' => 'required|email|unique:users,email,'.base64_decode($request->input('id')),
		'password' => 'required|min:6|max:16|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
		'confirm_password' => 'required|min:6|max:16|same:password',
		'terms_conditions' => 'required',);
		
		
		$messages = array( 'password.regex' => 'Password must be between 8 and 16 characters & contain one lower & uppercase letter, and one non-alpha character (a number or a symbol.)',
		 );

		$validator = Validator::make($request->all(),$rules,$messages);
		if ($validator->fails()) {
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else
		{
				
			

				$first_name =  $request->input('first_name');
				$id =  $request->input('id');

				$last_name =  $request->input('last_name');
				$email =  $request->input('email');
				$mobile =  $request->input('mobile');
				$password =  $request->input('password');
				
				$country_id =  $request->input('country');
				$user = User::where('id','=', base64_decode($id))->first();
				//dd($user);
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->mobile = $mobile;
				if($country_id!=""){
					$user->country_id = $country_id;
					$countrydata = Countries::where('id',$country_id)->first();
					$user->country_code = $countrydata->phonecode;
				}
				
				
		
			$user->password = Hash::make($password);
			$user->type = '3';
			$user->email_verify = '1';
			$user->is_signup = '1';
			
			$user->save();
			return response()->json(['class'=>'success' ,'message'=>'You have successfully signed up.']);
		}
	}

	
	public function getcountrycode(Request $request)
	{
		$country_id =  $request->input('country_id');
		$countrydata = Countries::where('id',$country_id)->first();
		if($countrydata){
			return $countrydata ->phonecode;

		}else{
			return '+00';
		}


	}

	public function companysurveyorsignuppost(Request $request)
	{
		$company_tax_id =  $request->input('company_tax_id');
		$country_id =  $request->input('country');
		$rules=array('country' => 'required',
		'mobile' => 'required|numeric',

		'company' => 'required|max:50',
		'company_tax_id' => 'required|unique:users|max:50',
		'first_name' => 'required|max:50',
		'last_name' => 'required|max:50',
		'email' => 'required|email|unique:users|max:50',
		'password' => 'required|min:6|max:16|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
		'confirm_password' => 'required|min:6|max:16|same:password',
		'terms_conditions' => 'required',);
		
		
		$messages = array( 'password.regex' => 'Password must be between 8 and 16 characters & contain one lower & uppercase letter, and one non-alpha character (a number or a symbol.)',
		 );

		$validator = Validator::make($request->all(),$rules,$messages);

		

		$company_tax_check=User::where('country_id',$country_id)->where('company_tax_id',$company_tax_id)->count();

		if ($validator->fails()) {
			if($company_tax_check>0)
				{
					return response()->json(['success'=>false ,'message'=>false,'errors'=>array('company_tax_id'=>'company tax id has already been taken')]);
				}
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else
		{
			$user = new User;
			
			
				$first_name =  $request->input('first_name');
				$last_name =  $request->input('last_name');
				$email =  $request->input('email');
				$mobile =  $request->input('mobile');
				$password =  $request->input('password');
				$company =  $request->input('company');
				
				
				$company_website =  $request->input('company_website');
				
				
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->mobile = $mobile;
				$user->is_signup = '1';
				if($country_id!=""){
					$user->country_id = $country_id;
					$countrydata = Countries::where('id',$country_id)->first();
					$user->country_code = $countrydata->phonecode;
				}
				
				if($company_tax_id!=""){
					$user->company_tax_id = $company_tax_id;
				}
				if($company!=""){
					$user->company = $company;
				}
				
				
			
				if($company_website!=""){
					$user->company_website = $company_website;
				}
				
		
			$user->password = Hash::make($password);
			$user->type = '2';
			$user->save();

			$create_url = \App::make('url')->to('/verify-email')."/".base64_encode($user->id);
			
			$data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));
				Mail::send( 'pages.email.emailconfirm',$data1, function( $message ) use ($data1)
				{
					$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

				});
			return response()->json(['class'=>'success' ,'message'=>'You have successfully signed up.Please verify your email']);
		}
	}

	

	public function signin(Request $request)
	{    $user = Auth::user();
		
		if (!empty($user->is_admin) && $user->is_admin!='1')
		{
			return redirect('/myaccount');
		}else
		{
			// if(!Session::has('reurl')){
			// Session::put('reurl',"/myaccount");
			// }
			return view('pages.signin');
		}
	}


	public function signinpost(Request $request)
	{
		$validator = Validator::make($request->all(), [
		'email' => 'required|string|email',
		'password' => 'required',
		]);
		if ($validator->fails()) {
			// return redirect('/signin')
			// ->withErrors($validator)
			// ->withInput();
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else{
			$email =  $request->input('email');
			$password =  $request->input('password');
			
			if (Auth::attempt(['email' => $email, 'password' => $password]))
			{
					$user = Auth::user();
	//dd($user);
					if ($user->is_admin== '0' && ($user->status=='2' || $user->status=='1'))
					{
						return response()->json(['class'=>'success' ,'message'=>'login Successfully.']);

							//Session::put('msg', '<strong class="alert alert-success">Login Successfully</strong>');
							//return redirect('/myaccount');
							//exit();	
					}
					else{
						return response()->json(['class'=>'success' ,'success'=>false ,'message'=>'Invalid Email or Password or not activated account.']);
						//Session::put('msg', '<strong class="alert alert-danger">Invalid Email or Password or not activated account.</strong>');
						

					}
					
			}  
			else
			{
				return response()->json(['class'=>'success' , 'success'=>false ,'message'=>'Invalid Email or Password.']);


			//Session::put('msg', '<strong class="alert alert-danger">Invalid Email or Password.</strong>');
			//return  redirect('/signin');
			}
		}
	}

	public function forgot()
	{
		if (Auth::check())
		{
			return redirect('/myaccount');
		}else{
			return view('pages.fogot');
		}
	}

	public function logout()
	{
		if (Auth::check())
		{
		Auth::logout();
		return redirect('/signin');
		}else {
		return redirect('/signin');
		}
	}


	public function forgotpost(Request $request)
	{

		$validator = Validator::make($request->all(), [

		'email_id' => 'required|email',

		]);

		if ($validator->fails()) {

			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);

		}else{
			   $email =  $request->input('email_id');
			$userArray = User::where('email','=', $email)->first();
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
				return response()->json(['class'=>'success' ,'message'=>'Forgot password instruction has been successfully sent on your email.']);

				
				
						
			}else {
				return response()->json(['class'=>'success' , 'success'=>false ,'message'=>'Please enter register email id.']);

			
			}

		}
	}



	public function createpass($uniqurl)
	{	
		if (Auth::check())
		{
			return redirect('/');
		}else {
			$user = User::whereForgot_url($uniqurl)->first();
			if(!isset($user->id)){
				return view('pages.error404',['msg'=>'Invalid url!']);
			}else{
				$time = date('Y-m-d H:i:s');
				$time1 = strtotime($user->forgot_time);
				$time2 = strtotime($time);
				$diff = $time2 - $time1;
				$hour_diff = $diff/3600;
				if($hour_diff > 24)
				{
					return view('pages.error404',['msg'=>'Your Session has been expired!']);
				}else{
					return view('pages.createpassword',['uniqurl'=>$uniqurl]);
				}
			}
		}
	} 

	public function createpasspost(Request $request)
	{
		if (Auth::check())
		{
			return redirect('/myaccount');
		}else {
		$validator = Validator::make($request->all(), [
		'uniqurl' => 'required',
		'password' => 'required|min:6|max:16|confirmed',
		'password_confirmation' => 'required|min:6|max:16',
		]);
		if ($validator->fails()) {
			return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
		}else{ 
			$password =  $request->input('password');
			$uniqurl =  $request->input('uniqurl');
		   $uniqurldata = User::whereForgot_url($uniqurl)->first();
		   //dd(  $uniqurldata );
		   if(!isset($uniqurldata->id)){
			   return view('pages.error404',['msg'=>'Invalid url!']);
		   }else{
			   
			   $uniqurldata->forgot_url = "";
			   $uniqurldata->password = Hash::make($password);
			   $uniqurldata->save();
			   echo json_encode(array('class'=>'success','message'=>' Password changed successfully. You can login with new password.'));

			 
		   
		      }
			}
		}
	} 		

	public function myaccount()
	{
		return view('pages.myaccount');	
	}
	public function myprofile(Request $request)
	{
		$user = Auth::user();

		$userdetail = User::select('users.*','p.port as portname','c.name as country_name')
		->leftJoin('port as p', 'users.port_id', '=', 'p.id')
		->leftJoin('countries as c', 'users.country_id', '=', 'c.id')
		->where('users.id',$user->id)->first();
		//dd($userdetail);

		$first = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$user->id);
		
		$total_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$user->id)->union($first)->count();

		$second = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$user->id) ->where('status','upcoming');

		$total_accept_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$user->id)->where('status','upcoming')->union($second)->count();

		
		$percentage_job_acceptance="0";
		if($total_accept_job!="" && $total_accept_job!="0" && $total_job!="" && $total_job!="0")
		{
				$percentage_job_acceptance=floor($total_accept_job/$total_job*100);
		}

		$survey_count=Survey::where('surveyors_id',$user->id)->count();
		return view('pages.myprofile',['userdata'=>$userdetail,'survey_count'=>$survey_count,'percentage_job_acceptance'=>$percentage_job_acceptance]);	
	}

	
	public function editprofilepost(Request $request)
{
	$user= Auth::user();
	$validator = Validator::make($request->all(), [
		
		'email' => 'required|email|unique:users,email,'.$user->id,
		'mobile' => 'required',
		
		]);	
	
	if ($validator->fails()) {
		return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
	}else{
		
		$email =  $request->input('email');
		$mobile =  $request->input('mobile');	
		$street_address =  $request->input('street_address');
		$city =  $request->input('city');
		$state =  $request->input('state');
		$country_id =  $request->input('country');
		$pincode =  $request->input('pincode');
		$experience =  $request->input('experience');
		$about_me =  $request->input('about_me');
		$is_surveyor =  $request->input('is_surveyor');
		
		$image = $request->file('image');

		$Users =  User::find($user->id);
		if(isset($image))
		{
			$imageName = time().$image->getClientOriginalName();
			$image->move(public_path().'/media/users', $imageName);
			$Users->profile_pic = $imageName;
		}
		
		$Users->email = $email;
		$Users->mobile = $mobile;


		
		if(	$country_id!=""){
			$Users->country_id = $country_id;
		}
		if(	$street_address!=""){
			$Users->street_address = $street_address;
		}
		if(	$city!=""){
			$Users->city = $city;
		}
		if(	$state!=""){
			$Users->state = $state;
		}
		if(	$experience!=""){
			$Users->experience = $experience;
		}
		if(	$about_me!=""){
			$Users->about_me = $about_me;
		}
		if(	$pincode!=""){
			$Users->pincode = $pincode;
		}
		if(	$is_surveyor!=""){
			$Users->is_surveyor = $is_surveyor;
		}else{
			$Users->is_surveyor = '0';
		}
		
		
		
		$Users->save();
		return response()->json(['class'=>"success" ,'message'=>"Edit Profile succesfully"]);

	}
}

	public function myoperator(Request $request)
	{
		$user = Auth::user();
		$operators_data =  User::select('*')->where('created_by',$user->id)->where('status','!=','0')->get();
		return view('pages.myoperator',['operators_data'=>$operators_data,'type'=>$user->type]);	
	}
	public function myoperatorpost(Request $request)
	{
		$id =  $request->input('id');
		$user = Auth::user();
		$email =  $request->input('email');
		$idpop="";
		if($id!=""){
			$userArray =   User::where('id',base64_decode($id))->first();
			$idpop=base64_decode($id);
			$message="Edit Operator Successfully.";
			$validator = Validator::make($request->all(), [
				'email' => 'required|email|unique:users,email,'.$idpop,
			]);
		}else{
		
			$userArray = new  User;
			$message="Add Operator Successfully.";
			$validator = Validator::make($request->all(), [
				'email' => 'required|email|unique:users,email',

			]);
			
		}

		
		if ($validator->fails()) 
		{
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors(),'id'=>$idpop]);
		}else
		{		
			$usertypecheck =  User::select('*')->where('id',$user->id)->first();

			if($id!="")
			{
				$userArray =   User::where('id',base64_decode($id))->first();
				$userArray->created_by=$user->id;
				$userArray->email=$email;
				if($usertypecheck->type=='2')
				{$userArray->type='3';}else{$userArray->type='1';}
				$userArray->email_verify='0';
				$userArray->save();
	
			}else
			{
				
				$userArray = new  User;
				$userArray->created_by=$user->id;
				$userArray->email=$email;
				if($usertypecheck->type=='2'){$userArray->type='3';}else{$userArray->type='1';}
				$userArray->save();
	
			}
			if($usertypecheck->type=='2')
			{$userArray->type='3';}else{$userArray->type='1';}

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

		    return response()->json(['class'=>'success' ,  'message'=>$message,'id'=>$idpop]);
		
		}
	}

	public function removeoperator(Request $request)
	{
		$user = Auth::user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			
			]);
			if ($validator->fails()) {
				
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else{
				     $user = Auth::user();
	
					$id =  $request->input('id');

					$users =   User::where('id',base64_decode($id))->where('created_by',$user->id)->first();
					
					if(!empty($users))
					{

						
							$users->status='0';
							$users->save();
							//echo $users->type;exit;
							if($users->type=='1'){
								$var='operator';
							}
							if($users->type=='3'){
								$var='surveyor';
							}
							return response()->json(['class'=>'success' ,  'message'=>"You have removed a  $var successfully"]);
						
						
						
					}else{
						
						return response()->json(['class'=>'danger' ,  'message'=>"Something Went Wrong"]);
					}

				
			}
		

	}
	public function removesurveytype(Request $request)
	{
		$user = Auth::user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			
			]);
			if ($validator->fails()) {
				
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else{
				     $user = Auth::user();
	
					$id =  $request->input('id');

					$Surveytype =  UsersSurveyPrice::where('id',base64_decode($id))->first();
					//dd($Surveytype);
					if(!empty($Surveytype))
					{
						$Surveytype->delete();
						return response()->json(['class'=>'success' ,  'message'=>"You have remove Survey successfully"]);
						
					}else{
						
						return response()->json(['class'=>'danger' ,  'message'=>"Something Went Wrong"]);
					}

				
			}
		

	}
	public function myship(Request $request)
	{
		$user = Auth::user();
		$user_id=$user->id;
		
		if($user->type=='0'){
			$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get();
			$ids=array();
			if(!empty($createdbysurveyor)){
				foreach($createdbysurveyor as $data){
					$ids[]=$data->id;
				}
			}
				array_push($ids,$user_id);
		
		}else
		{
					$createdbysurveyor =  User::select('created_by')->where('id',$user_id)->first();
					$ids=array();
					if(!empty($createdbysurveyor)){
						
							$ids[]=$createdbysurveyor->created_by;
					}
						array_push($ids,$user_id);
		}
		
		$ship_data =  Vessels::select('*')->whereIn('user_id',$ids)
		// ->orderBy('vessels.favourite','DESC')
		->orderBy('vessels.name','asc')->get();
		return view('pages.myship',['ship_data'=>$ship_data]);	
		

	}
	public function myshippost(Request $request)
	{
		$id =  $request->input('id');
		$idpop="";
		if($id!=""){
			$vessels =   Vessels::where('id',"=",base64_decode($id))->first();
			$idpop=base64_decode($id);
			$message="Edit Ship Successfully.";
		}else{
			$vessels = new  Vessels;
			$message="Add Ship Successfully.";
		}
		if($id!=""){
			$validator = Validator::make($request->all(), [
				'name' => 'required',
				'imo_number' => 'required|unique:vessels,imo_number,'.base64_decode($id),
				'email' => 'required|max:50',
				'company' => 'required|max:50',
				'street_address' => 'required|max:50',
				'city' => 'required|max:50',
				'pincode' => 'required|max:50',
				'state' => 'required|max:50',
			]);
		}else{
			$validator = Validator::make($request->all(), [
				'name' => 'required',
				'imo_number' => 'required|unique:vessels',
				'email' => 'required|max:50',
				'company' => 'required|max:50',
				'street_address' => 'required|max:50',
				'city' => 'required|max:50',
				'pincode' => 'required|max:50',
				'state' => 'required|max:50',
			]);
		}

		

	

		if ($validator->fails()) {
			
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors(),'id'=>$idpop]);
		}else{

				 $user = Auth::user();
				 
				 $name =  $request->input('name');
				 $imo_number =  $request->input('imo_number');
				 $email =  $request->input('email');
				 $additional_email =  $request->input('additional_email');
				 $company =  $request->input('company');
				 $same_as_company =  $request->input('same_as_company');
				 $same_as_company_address =  $request->input('same_as_company_address');
				 $street_address =  $request->input('street_address');
				 $city =  $request->input('city');
				 $state =  $request->input('state');
				 $pincode =  $request->input('pincode');
				       
						$vessels->name=$name;
						$vessels->imo_number=$imo_number;
						$vessels->user_id=$user->id;
						$vessels->email=$email;
						if(isset($additional_email))
						{$vessels->additional_email=$additional_email;
						}
						
						if($same_as_company=='1')
						{
							$vessels->same_as_company=$same_as_company;
							$user_info= User::find($user->id);
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

						if(	$street_address!=""){
							$vessels->address = $street_address;
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
				       $vessels->save();
				
				return response()->json(['class'=>'success' ,  'message'=>$message,'id'=>$idpop]);
		
		}
	}


	public function 	removevessel(Request $request)
	{
		$user = Auth::user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			
			]);
			if ($validator->fails()) {
				
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else{
				     $user = Auth::user();
	
					$id =  $request->input('id');

					$Vessels =  Vessels::where('id',base64_decode($id))->first();
					//dd($Surveytype);
					if(!empty($Vessels))
					{
						$Vessels->delete();
						return response()->json(['class'=>'success' ,  'message'=>"You have removed a vessel successfully"]);
						
					}else{
						
						return response()->json(['class'=>'danger' ,  'message'=>"Something Went Wrong"]);
					}

				
			}
		

	}
	public function addshipfavourite(Request $request)
	{
		$user = Auth::user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			
			]);
			if ($validator->fails()) {
				
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else{
				     $user = Auth::user();
	
					$id =  $request->input('id');

					$vesselsf =   Vessels::where('id',base64_decode($id))->where('user_id',$user->id)->first();
					
					if(!empty($vesselsf))
					{

						if($vesselsf->favourite=='1'){
							$vesselsf->favourite='0';
							$vesselsf->save();
							return response()->json(['class'=>'success' ,  'message'=>"You have unfavorited this vessel successfully"]);
						}else{
							$vesselsf->favourite='1';
							$vesselsf->save();
							return response()->json(['class'=>'success' ,  'message'=>"You have favourited this vessel successfully"]);
						}
						
						
					}else{
						
						return response()->json(['class'=>'danger' ,  'message'=>"Something Went Wrong"]);
					}

				
			}
		

	}
	public function myagent(Request $request)
	{
		$user = Auth::user();
		$user_id=$user->id;
		if($user->type=='0'){
			$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get();
			$ids=array();
			if(!empty($createdbysurveyor)){
				foreach($createdbysurveyor as $data){
					$ids[]=$data->id;
				}
			}
				array_push($ids,$user_id);
		
		}else
		{
					$createdbysurveyor =  User::select('created_by')->where('id',$user_id)->first();
					$ids=array();
					if(!empty($createdbysurveyor)){
						
							$ids[]=$createdbysurveyor->created_by;
					}
						array_push($ids,$user_id);
		}


		
			$agents_data =  Agents::select('*')->whereIn('user_id',$ids)->orderBy('first_name','Asc')->get();
		


		
		return view('pages.myagent',['agents_data'=>$agents_data]);	
		

	}
	public function myagentpost(Request $request)
	{
		$id =  $request->input('id');
		$idpop="";
		if($id!=""){
			$userport =   Agents::where('id',base64_decode($id))->first();
			$idpop=base64_decode($id);
			$message="Edit Agent Successfully.";
		}else{
			$userport = new  Agents;
			$message="Add Agent Successfully.";
		}

		$validator = Validator::make($request->all(), [
		'first_name' => 'required',
	
		'email' => 'required',
		'mobile' => 'required',
		]);
		if ($validator->fails()) {
			
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors(),'id'=>$idpop]);
		}else{

			
				
			     $user = Auth::user();

				$first_name =  $request->input('first_name');
				$email =  $request->input('email');
				$mobile =  $request->input('mobile');

				$userport->user_id=$user->id ;
				$userport->first_name=$first_name ;
				$userport->email=$email ;
				$userport->mobile=$mobile;
				$userport->save();
				
				return response()->json(['class'=>'success' ,  'message'=>$message,'id'=>$idpop]);
		
		}
	}
	public function removeagent(Request $request)
	{
		$user = Auth::user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			
			]);
			if ($validator->fails()) {
				
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else{
				     $user = Auth::user();
	
					$id =  $request->input('id');

					$agent =  Agents::where('id',base64_decode($id))->first();
					//dd($Surveytype);
					if(!empty($agent))
					{
						$agent->delete();
						return response()->json(['class'=>'success' ,  'message'=>"You have removed an agent successfully"]);
						
					}else{
						
						return response()->json(['class'=>'danger' ,  'message'=>"Something Went Wrong"]);
					}

				
			}
		

	}
	
	public function myport(Request $request)
	{
		$user = Auth::user();

		$userportdetail = UsersPort::select('users_port.*','p.port as portname')
		->leftJoin('port as p', 'users_port.port_id', '=', 'p.id')
		->where('users_port.user_id',$user->id)
		->orderBy('portname','asc')
		->get();
		//dd($userportdetail);
		

		return view('pages.myport',['userportdetail'=>$userportdetail]);	
		

	}
	public function getdata(Request $request)
	{
		$user = Auth::user();
		$type =  $request->input('type');

		if($type=="company")
		{
			$user_detail = User::select('company')->where('id',$user->id)->first();
			return $user_detail->company;
		}else{
			$user_detail = User::select('users.*')->where('id',$user->id)->first();
			return $user_detail;
		}


		

	}
	
	public function myportpost(Request $request)
	{
			   $id =  $request->input('id');
			   $port_ids =  $request->input('port_id');
			   $country_id =  $request->input('country_id');
			   $price =  $request->input('price');
				$idpop="";
				if($id!=""){
					$userport =   UsersPort::where('id', base64_decode($id))->first();

					$idpop=base64_decode($id);
					$message="Edit Port Successfully.";
				}else{
					
					$message="Add Port Successfully.";
				}
				if($id!="")
				{
					$rules=array('price' =>'required');
					$messages = array( 'price.required' => 'Transportation cost required!' );
					$validator = Validator::make($request->all(),$rules,$messages);
				}else
				{
					$rules=array( 'country_id' => 'required', 'price' =>'required','port_id' => 'required|array');
					$messages = array( 'port_id.required' => 'Click on checkmark to add a port!' );

					$validator = Validator::make($request->all(),$rules,$messages);

				}

		if ($validator->fails()) {
			
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors(),'id'=>$idpop]);
		}else{

				
				
			     $user = Auth::user();

			
			
			//	dd($port_ids);
				
				if($id!="")
				{
							$userport->user_id=$user->id ;
							$userport->cost=!empty($price)?$price:'0';
							$userport->save();
				}else{
					// $price=	array_filter($price);
					// dd($price);
					foreach($port_ids as $key=>$value)
						{
							
							$userport =   UsersPort::where('user_id', $user->id)->where('port_id',$value)->first();
							if(empty($userport))
							{
								$userport =  new  UsersPort;
							}
								$userport->user_id=$user->id ;
								$userport->country_id=$country_id ;
								$userport->port_id=$value;
								$userport->cost=!empty($price[$key])?$price[$key]:'';
								$userport->save();
						}

				}

				
				return response()->json(['class'=>'success' ,  'message'=>$message,'id'=>$idpop]);

			
				
		
		}
	}
	public function removeport(Request $request)
	{
		$user = Auth::user();
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			
			]);
			if ($validator->fails()) {
				
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else{
				     $user = Auth::user();
	
					$id =  $request->input('id');

					$port =  UsersPort::where('id',base64_decode($id))->first();
					//dd($Surveytype);
					if(!empty($port))
					{
						$port->delete();
						return response()->json(['class'=>'success' ,  'message'=>"You have remove port successfully"]);
						
					}else{
						
						return response()->json(['class'=>'danger' ,  'message'=>"Something Went Wrong"]);
					}

				
			}
		

	}
	public function mysurveytypes(Request $request)
	{
		$user = Auth::user();

		$usersurveytypesdetail = UsersSurveyPrice::select('users_survey_price.*','survey_type.name as survey_type_name')
		->leftJoin('survey_type', 'users_survey_price.survey_type_id', '=', 'survey_type.id')
		->where('users_survey_price.user_id',$user->id)->orderBy('survey_type.name','asc')->get();
		//dd($userportdetail);
		

		return view('pages.mysurveytypes',['usersurveytypesdetail'=>$usersurveytypesdetail]);	
		

	}
	
	public function mysurveytypespost(Request $request)
	{
		$id =  $request->input('id');
		$survey_type_id =  $request->input('survey_type_id');
		$idpop="";
		$user = Auth::user();
		if($id!="")
		{
			
			$userport =   UsersSurveyPrice::where('user_id',$user->id)->where('survey_type_id',$survey_type_id)->first();
			if(!empty($userport))
			{
				$userport =   UsersSurveyPrice::where('user_id',$user->id)->where('survey_type_id',$survey_type_id)->first();
			}else{
				$userport =   UsersSurveyPrice::where('id',base64_decode($id))->first();
			}
			$idpop=base64_decode($id);
			$message="Edit Survey Type Successfully.";

		}else
		{

			
			$userport =   UsersSurveyPrice::where('user_id',$user->id)->where('survey_type_id',$survey_type_id)->first();
			if(!empty($userport))
			{
				$userport =   UsersSurveyPrice::where('user_id',$user->id)->where('survey_type_id',$survey_type_id)->first();
			}else{
				$userport = new  UsersSurveyPrice;
			}


			$message="Add Survey Type Successfully.";
		}
		
		$validator = Validator::make($request->all(), [
		'survey_type_id' => 'required',
		'price' => 'required',
		]);
		if ($validator->fails()) {
			
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors(),'id'=>$idpop]);
		}else{

			
			     $user = Auth::user();

			
				$cost =  $request->input('price');

				$userport->user_id=$user->id ;
				$userport->survey_type_id=$survey_type_id ;
				if($survey_type_id=='8' || $survey_type_id=='23' || $survey_type_id=='24' ||
				 $survey_type_id=='25' || $survey_type_id=='29'){
					$userport->type="daily";
				}else{
					$userport->type="fix";
				}

				$userport->survey_price=$cost;
				$userport->save();
				
				return response()->json(['class'=>'success' ,  'message'=>$message,'id'=>$idpop]);
		
		}
	}
	
	public function mysurvey(Request $request)
	{
		$user = Auth::user();
		$user_id=$user->id;

		$surveyor_upcomming_status = $request->input('status');
		$surveyor_upcomming_surveyor_id =  !empty($request->input('surveyor_id'))?$request->input('surveyor_id') : "";


		$surveyor_past_status = $request->input('past_status');
		$surveyor_past_surveyor_id =  !empty($request->input('past_surveyor_id'))?$request->input('past_surveyor_id') : ""; 

		$operator_upcomming_status = $request->input('status'); 
		$search = $request->input('search');

		$operator_upcomming_operator_id =  !empty($request->input('operator_id'))?$request->input('operator_id') : "";

		$operator_past_status = $request->input('past_status') ;
		$past_search = $request->input('past_search');
		$operator_past_operator_id =  !empty($request->input('past_operator_id'))?$request->input('past_operator_id') : "";

		$upcomming_filter_type=$request->input('upcomming_filter_type');
		$past_filter_type=$request->input('past_filter_type');


			$user_d =  User::select('type')->where('id',$user->id)->first(); 
			if($user_d->type =="0" || $user_d->type =="1")
			{
				
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
									
									
									if($operator_upcomming_operator_id !="")
									{
										$users_type =  User::select('type')->where('id',$operator_upcomming_operator_id)->first();

										$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($operator_upcomming_operator_id) {
											$query->Where('survey.user_id',$operator_upcomming_operator_id)
											->orWhere('survey.assign_to_op',$operator_upcomming_operator_id);});	

									}
									else if($operator_past_operator_id !="")
									{
										$users_type =  User::select('type')->where('id',$operator_past_operator_id)->first();

										$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($operator_past_operator_id) {
											$query->Where('survey.user_id',$operator_past_operator_id)
											->orWhere('survey.assign_to_op',$operator_past_operator_id);});	

									}
									
									else
									{
										$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
										$ids=array();
										foreach($createdbysurveyor as $data){
											$ids[]=$data->id;
										}

										array_push($ids,$user_id);
										

										$upcoming_survey_data=$upcoming_survey_data->WhereIn('survey.user_id',$ids);
									
	
									}
								}
								if($user_d->type =="1")
								{

									if($operator_upcomming_operator_id !="")
									{
										$users_type =  User::select('type')->where('id',$operator_upcomming_operator_id)->first();
										// if($users_type->type=='1'){
										// 	$upcoming_survey_data=$upcoming_survey_data->where('survey.assign_to_op', '=',$operator_upcomming_operator_id );

										// }else{
										// 	$upcoming_survey_data=$upcoming_survey_data->where('survey.user_id', '=',$operator_upcomming_operator_id )
										// 	->where('survey.assign_to_op', '=',"" );

										// }
										$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($operator_upcomming_operator_id) {
											$query->Where('survey.user_id',$operator_upcomming_operator_id)
											
											->orWhere('survey.assign_to_op',$operator_upcomming_operator_id);});										
									}else
									{
										$createdbysurveyor =  User::select('created_by')->where('id',$user_id)->first();
										$ids=array();
										if(!empty($createdbysurveyor)){
											
												$ids[]=$createdbysurveyor->created_by;
										}
											array_push($ids,$user_id);
											$upcoming_survey_data=$upcoming_survey_data->whereIn('survey.user_id',$ids);
									
	
									}
									

								}
								if($operator_upcomming_status!="")
								{
									if($operator_upcomming_status!='0'){
										$upcoming_survey_data=$upcoming_survey_data->where('survey.status',$operator_upcomming_status);
									}else{
										$upcoming_survey_data=$upcoming_survey_data->where('survey.status',$operator_upcomming_status)
										->where('survey.declined','0');
									}
									
								}

								
								else{
									$upcoming_survey_data=$upcoming_survey_data->where(function ($query) {
										$query->where('survey.status', '=','0' )
												->orWhere('survey.status', '=', '1')
												->orWhere('survey.status', '=', '2')
												->orWhere('survey.status', '=', '3');
									});
									
								//$upcoming_survey_data=$upcoming_survey_data->orderByRaw('survey.status = ? desc',['1']);
								}
								if($search !="")
								{
									$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($search) {
										$query->where('vessels.name', 'like','%'.$search.'%' )
												->orWhere('port.port', 'like','%'.$search.'%' )
												->orWhere('survey.survey_number', 'like','%'.$search.'%' );
												
									});
									
								}
								if($past_search !="")
								{
									$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($past_search) {
										$query->where('vessels.name', 'like','%'.$past_search.'%' )
												->orWhere('port.port', 'like','%'.$past_search.'%' )
												->orWhere('survey.survey_number', 'like','%'.$past_search.'%' );
												
									});
									
								}
								$upcoming_survey_data=$upcoming_survey_data->groupBy('survey.id');

								$chat_unread_count=Chat::select('chat.id')->where('is_read','0')->where('receiver_id',$user->id)->count();
								if($chat_unread_count>0){
									$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
								}
								

								$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.start_date','desc');
								$upcoming_survey_data=$upcoming_survey_data->paginate(10);
								//dd($upcoming_survey_data);
						
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
						
								if($surveyor_upcomming_surveyor_id !="")
								{
									// $upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($surveyor_upcomming_surveyor_id) {
									// 	$query->Where('custom_survey_users.surveyors_id',$surveyor_upcomming_surveyor_id)
									// 	->orwhere('survey_users.surveyors_id',$surveyor_upcomming_surveyor_id )
									// 	->orWhere('survey.assign_to',$surveyor_upcomming_surveyor_id);});	

									$u=	User::select('type')->where('id',$surveyor_upcomming_surveyor_id)->first();

										if($u->type =="3")
											{
												$upcoming_survey_data=$upcoming_survey_data->where('survey.assign_to',$surveyor_upcomming_surveyor_id);
											}else
											{
												$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($surveyor_upcomming_surveyor_id) {
													$query->Where('custom_survey_users.surveyors_id',$surveyor_upcomming_surveyor_id)
													 ->orwhere('survey_users.surveyors_id',$surveyor_upcomming_surveyor_id );});
													  $upcoming_survey_data=$upcoming_survey_data->where('survey.assign_to','0');
											}


								}
								else if($surveyor_past_surveyor_id){
									$u=	User::select('type')->where('id',$surveyor_past_surveyor_id)->first();

										if($u->type =="3")
											{
												$upcoming_survey_data=$upcoming_survey_data->where('survey.assign_to',$surveyor_past_surveyor_id);
											}else
											{
												$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($surveyor_past_surveyor_id) {
													$query->Where('custom_survey_users.surveyors_id',$surveyor_past_surveyor_id)
													 ->orwhere('survey_users.surveyors_id',$surveyor_past_surveyor_id );});
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
									//dd($ids);

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
					

					if($surveyor_upcomming_status!=""){
						$upcoming_survey_data=$upcoming_survey_data->where('survey.status',$surveyor_upcomming_status);
					} else{
						$upcoming_survey_data=$upcoming_survey_data->where(function ($query) {
							$query->where('survey.status', '=','0' )
									->orWhere('survey.status', '=', '1')
									->orWhere('survey.status', '=', '2')
									->orWhere('survey.status', '=', '3');
						});
						//$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
						//$upcoming_survey_data=$upcoming_survey_data->orderByRaw('survey.status = ? desc',['1']);
					}

					$upcoming_survey_data=$upcoming_survey_data->where(function ($query)  {
						$query->Where('survey_users.status','pending')
							->orwhere('survey_users.status','upcoming' )
							->orwhere('custom_survey_users.status','waiting' )
							->orwhere('custom_survey_users.status','upcoming' )
							->orwhere('custom_survey_users.status','approved' );});	


					$upcoming_survey_data=$upcoming_survey_data->groupBy('survey.id');

					$chat_unread_count=Chat::select('chat.id')->where('is_read','0')->where('receiver_id',$user->id)->count();
								if($chat_unread_count>0){
									$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
								}
								

					$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.start_date','desc');
					$upcoming_survey_data=$upcoming_survey_data->paginate(10);

					
			}
			//dd($upcoming_survey_data);
			if($user_d->type =="0" || $user_d->type =="1")
			{

				$past_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
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
							
							if($operator_upcomming_operator_id !="")
									{
										$users_type =  User::select('type')->where('id',$operator_upcomming_operator_id)->first();
										if($users_type->type=='0'){
											$past_survey_data=$past_survey_data->where('survey.user_id',$operator_upcomming_operator_id);
											$past_survey_data=$past_survey_data->where('survey.assign_to_op','0');;
										}else{
											$past_survey_data=$past_survey_data->where(function ($query) use ($operator_upcomming_operator_id) {
												$query->Where('survey.user_id',$operator_upcomming_operator_id)
												->orWhere('survey.assign_to_op',$operator_upcomming_operator_id);});
										}
											

									}
									else if($operator_past_operator_id !="")
									{
										$users_type =  User::select('type')->where('id',$operator_past_operator_id)->first();
										if($users_type->type=='0'){
											$past_survey_data=$past_survey_data->where('survey.user_id',$operator_past_operator_id);
											$past_survey_data=$past_survey_data->where('survey.assign_to_op','0');;
										}else{
											$past_survey_data=$past_survey_data->where(function ($query) use ($operator_past_operator_id) {
												$query->Where('survey.user_id',$operator_past_operator_id)
												->orWhere('survey.assign_to_op',$operator_past_operator_id);});
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
										

										$past_survey_data=$past_survey_data->WhereIn('survey.user_id',$ids);
									
	
									}
						}
						if($user_d->type =="1")
						{
							if($operator_upcomming_operator_id !="")
							{
								$past_survey_data=$past_survey_data->where('survey.user_id',$operator_upcomming_operator_id);
								
							}else
							{

								$createdbysurveyor =  User::select('created_by')->where('id',$user_id)->first();
										$ids=array();
										if(!empty($createdbysurveyor)){
											
												$ids[]=$createdbysurveyor->created_by;
										}
											array_push($ids,$user_id);
									$past_survey_data=$past_survey_data->whereIn('survey.user_id',$ids);

							
									}

						}
						if($operator_past_status!=""){
							$past_survey_data=$past_survey_data->where('survey.status',$operator_past_status);
						}else{
							$past_survey_data=$past_survey_data->where(function ($query) {
								$query->where('survey.status', '=','4' )
										->orWhere('survey.status', '=', '5')
										->orWhere('survey.status', '=', '6');
							});
						}
						if($search !="")
						{
							$past_survey_data=$past_survey_data->where(function ($query) use ($search) {
								$query->where('vessels.name', 'like','%'.$search.'%' )
										->orWhere('port.port', 'like','%'.$search.'%' )
										->orWhere('survey.survey_number', 'like','%'.$search.'%' );
										
							});
							
						}
						if($past_search !="")
								{
									$past_survey_data=$past_survey_data->where(function ($query) use ($past_search) {
										$query->where('vessels.name', 'like','%'.$past_search.'%' )
												->orWhere('port.port', 'like','%'.$past_search.'%' )
												->orWhere('survey.survey_number', 'like','%'.$past_search.'%' );
												
									});
									
								}
						
						$past_survey_data=$past_survey_data->groupBy('survey.id');
						//$past_survey_data=$past_survey_data->orderBy('survey.active_thread','desc');
						$past_survey_data=$past_survey_data->orderBy('survey.start_date','desc');
						//$past_survey_data=$past_survey_data->orderByRaw('survey.status = ? desc',['4']);
					   // $past_survey_data=$past_survey_data->orderBy('survey.start_date','desc');
						$past_survey_data=$past_survey_data->paginate(10);
						//dd($past_survey_data);
					
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
							if($surveyor_upcomming_surveyor_id !="")
									{
										// $past_survey_data=$past_survey_data->where('survey_users.surveyors_id', '=',$surveyor_past_surveyor_id );
										// $past_survey_data=$past_survey_data->where(function ($query) use ($surveyor_upcomming_surveyor_id) {
										// 	$query->Where('custom_survey_users.surveyors_id',$surveyor_upcomming_surveyor_id)
										// 	->orwhere('survey_users.surveyors_id',$surveyor_upcomming_surveyor_id )
										// 	->orWhere('survey.assign_to',$surveyor_upcomming_surveyor_id);});	
											$u=	User::select('type')->where('id',$surveyor_upcomming_surveyor_id)->first();

										if($u->type =="3")
											{
												$past_survey_data=$past_survey_data->where('survey.assign_to',$surveyor_upcomming_surveyor_id);
											}else
											{
												$past_survey_data=$past_survey_data->where(function ($query) use ($surveyor_upcomming_surveyor_id) {
													$query->Where('custom_survey_users.surveyors_id',$surveyor_upcomming_surveyor_id)
													 ->orwhere('survey_users.surveyors_id',$surveyor_upcomming_surveyor_id );});
													  $past_survey_data=$past_survey_data->where('survey.assign_to','0');
											}
										}	
											else if($surveyor_past_surveyor_id){
												$u=	User::select('type')->where('id',$surveyor_past_surveyor_id)->first();
			
													if($u->type =="3")
														{
															$past_survey_data=$past_survey_data->where('survey.assign_to',$surveyor_past_surveyor_id);
														}else
														{
															$upcoming_survpast_survey_dataey_data=$past_survey_data->where(function ($query) use ($surveyor_past_surveyor_id) {
																$query->Where('custom_survey_users.surveyors_id',$surveyor_past_surveyor_id)
																 ->orwhere('survey_users.surveyors_id',$surveyor_past_surveyor_id );});
																  $past_survey_data=$past_survey_data->where('survey.assign_to','0');
														}
											
									}else{
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
												
									}
					}else
					{
						$past_survey_data=$past_survey_data->where(function ($query) use ($user_id) {
							$query->where('survey_users.surveyors_id', '=',$user_id )
							->orWhere('custom_survey_users.surveyors_id',$user_id)
							->orWhere('survey.assign_to',$user_id);});
					
					}
						
					 if($surveyor_past_status!=""){
							$past_survey_data=$past_survey_data->where('survey.status',$surveyor_past_status);
						
						
					
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
					$past_survey_data=$past_survey_data->orderBy('survey.start_date','desc');
					$past_survey_data=$past_survey_data->paginate(10);
					
			}
// dd($past_survey_data);

		return view('pages.mysurvey',['upcoming_survey_data'=>$upcoming_survey_data,
										'past_survey_data'=>$past_survey_data,
										'upcomming_filter_type'=>$upcomming_filter_type,
										'past_filter_type'=>$past_filter_type,

										'operator_upcomming_operator_id'=>$operator_upcomming_operator_id,
										'operator_past_operator_id'=>$operator_past_operator_id,
										'operator_upcomming_status'=>$operator_upcomming_status,
										'operator_past_status'=>$operator_past_status,
										'search'=>$search,
										'past_search'=>$past_search,
										
										
										'surveyor_upcomming_status'=>$surveyor_upcomming_status,
										'surveyor_past_status'=>$surveyor_past_status,
										'surveyor_upcomming_surveyor_id'=>$surveyor_upcomming_surveyor_id,
										'surveyor_past_surveyor_id'=>$surveyor_past_surveyor_id
										]);	
		

	}
			
public function surveydetail($id)
	{					
		$surveyor_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
							DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'),
							'users.company as operator_company'
							,'users.company_website as operator_company_website',
							'users.country_id as user_country_id','users.id as operator_id',
							DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
							DB::raw('CONCAT(csu.first_name, "  ", csu.last_name) as csuusername'),
							'su.id as surveyor_id','csu.id as csurveyor_id','su.rating as surveyor_rating'
							,'csu.rating as csurveyor_rating',
							DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
							 'su.profile_pic as image','survey_users.surveyors_id as surveyors_ids', 
							 'vessels.name as vesselsname', 
							 'vessels.email as vesselsemail', 'vessels.address as vesselsaddress',
							  'vessels.company as vesselscompany', 'agents.email as agentsemail',
							   'agents.mobile as agentsmobile','vessels.imo_number','port.port as portname')
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
							->where('survey.id',$id);

							// $surveyor_data=$surveyor_data->where(function ($query)  {
							// 	$query->Where('survey_users.status','pending')
							// 	->orwhere('survey_users.status','upcoming' )
							// 	->orwhere('custom_survey_users.status','upcoming' );});	
								$surveyor_data=$surveyor_data->first();
							//dd($surveyor_data );
							$operator_survey_count =  Survey::where('user_id',$surveyor_data->user_id)->count();
							$country_data =  Countries::where('id',$surveyor_data->user_country_id)->first();
							
							$helper=new Helpers;
							
							
								
							// $total_price=0;
							// $survey_price=0;
							// $port_price=0;
							// if($surveyor_data->survey_type_id=='31')
							// {
							// 	$custom_survey_price_data = Customsurveyusers::select('custom_survey_users.*')
							// 	->where("custom_survey_users.survey_id",$surveyor_data->id)
							// 	->where("custom_survey_users.surveyors_id",$surveyor_data->accept_by)->first();
							// 	//dd(	$custom_survey_price_data);
							// 	 $survey_price=$custom_survey_price_data->amount;
							// }else{

							// 	$survey_price_data = UsersSurveyPrice::select('users_survey_price.*')
							// 	->where("users_survey_price.survey_type_id",$surveyor_data->survey_type_id)
							// 	->where("users_survey_price.user_id",$surveyor_data->accept_by)->first();
							// 	if($survey_price_data->type=='daily')
							// 	{
							// 		$survey_price=$survey_price_data->survey_price*$survey_price_data->no_of_days;
							// 	}else{
							// 	 $survey_price=$survey_price_data->survey_price;
							// 	}
							// }
							

							// $user_port_data = UsersPort::select('users_port.cost')
							// ->where("users_port.port_id",$surveyor_data->port_id)
							// ->where("users_port.user_id",$surveyor_data->accept_by)->first();
							// if(!empty($user_port_data ))
							// {
							// 	$port_price=$user_port_data->cost;
							// }
							
							// if($survey_price!="" || $port_price!="")
							// {
							// 	$total_price=$survey_price + $port_price ;

							// }
							$user = Auth::user();
							$pricing="0";
							$pricing1="0";
							$pricing2="0";
							$surveyor_name="";
							$op_company_name="";
							$su_company_name="";
							$userdata = User::where('id',$user->id)->first();
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
										$pricing=$survey_ch->amount;
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
								if(!empty($survey_ch)){
									$pricing1=$helper->SurveyorPrice($surveyor_data->survey_type_id,$survey_ch->surveyors_id);						
									$pricing2=$helper->SurveyorPortPrice($surveyor_data->port_id,$survey_ch->surveyors_id);
								}
																		

										
										if($pricing1!="" || $pricing2!="")
										{
											$pricing2=!empty($pricing2)?$pricing2:'0';
											$pricing1=!empty($pricing1)?$pricing1:'0';

											$pricing=$pricing1+$pricing2;
										}	
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
		
											
		
											$suusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as suusername'),'company')->where('id',$surveyor_id_id)->first();
										
											if(!empty($suusername)){
												 $surveyor_name=$suusername->suusername;
												$su_company_name=$suusername->company;
											}
		
									}
								
										if($surveyor_data->assign_to_op=="")
										{
											$opusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as opusername'),'company')->where('id',$surveyor_data->operator_id)->first();
											$op_company_name=$opusername->company;
										}else{
											$opusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as opusername'),'company')->where('id',$surveyor_data->assign_to_op)->first();
											$op_company_name=$opusername->company;
										}



								$bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)->where('surveyors_id',$user->id)->where('status','upcoming')->count();
								if($bid_count>0){
								$bid_status='1';
								}else{
									$bid_status='0';
								}

								
							 	$operator_bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)->where('status','!=','approved')->count();

							//dd($surveyor_data);
							
				 return view('pages.mysurveydetail',['surveyor_data'=>$surveyor_data,'surveyor_id_id'=>$surveyor_id_id,'suusername'=>$surveyor_name,
				 'opusername'=>$opusername->opusername,'su_company_name'=>$su_company_name,'op_company_name'=>$op_company_name,
				 'operator_survey_count'=>$operator_survey_count,'country_data'=>$country_data,'total_price'=>$pricing,'cat_price'=>$pricing1,'port_price'=>$pricing2,'bid_status'=>$bid_status,'operator_bid_count'=>$operator_bid_count]);
	}	

	public function userdetail($id)
	{					
		
		$surveyor_data =  User::select('users.*')->where('users.id',$id)->first();
		$comment_data =  Rating::select('rating.rating','rating.comment',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'))
		->leftJoin('users', 'users.id', '=', 'rating.operator_id')
		->where('rating.surveyor_id',$id)->groupBy('rating.operator_id')->get();
						//dd($comment_data);
		return view('pages.userdetail',['surveyor_data'=>$surveyor_data,'comment_data'=>$comment_data]);
	}	
	public function surveydetailcal($id)
	{					
		$surveyor_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
							DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'),
							'users.company as operator_company'
							,'users.company_website as operator_company_website',
							'users.country_id as user_country_id','users.id as operator_id',
							DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
							DB::raw('CONCAT(csu.first_name, "  ", csu.last_name) as csuusername'),
							'su.id as surveyor_id','csu.id as csurveyor_id','su.rating as surveyor_rating'
							,'csu.rating as csurveyor_rating',
							DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
							 'su.profile_pic as image','survey_users.surveyors_id as surveyors_ids', 
							 'vessels.name as vesselsname', 
							 'vessels.email as vesselsemail', 'vessels.address as vesselsaddress',
							  'vessels.company as vesselscompany', 'agents.email as agentsemail',
							   'agents.mobile as agentsmobile','vessels.imo_number','port.port as portname')
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
							->where('survey.id',$id);

							// $surveyor_data=$surveyor_data->where(function ($query)  {
							// 	$query->Where('survey_users.status','pending')
							// 	->orwhere('survey_users.status','upcoming' )
							// 	->orwhere('custom_survey_users.status','upcoming' );});	
								$surveyor_data=$surveyor_data->first();
							//dd($surveyor_data );
							$operator_survey_count =  Survey::where('user_id',$surveyor_data->user_id)->count();
							$country_data =  Countries::where('id',$surveyor_data->user_country_id)->first();
							
							$helper=new Helpers;
							
							
								
							// $total_price=0;
							// $survey_price=0;
							// $port_price=0;
							// if($surveyor_data->survey_type_id=='31')
							// {
							// 	$custom_survey_price_data = Customsurveyusers::select('custom_survey_users.*')
							// 	->where("custom_survey_users.survey_id",$surveyor_data->id)
							// 	->where("custom_survey_users.surveyors_id",$surveyor_data->accept_by)->first();
							// 	//dd(	$custom_survey_price_data);
							// 	 $survey_price=$custom_survey_price_data->amount;
							// }else{

							// 	$survey_price_data = UsersSurveyPrice::select('users_survey_price.*')
							// 	->where("users_survey_price.survey_type_id",$surveyor_data->survey_type_id)
							// 	->where("users_survey_price.user_id",$surveyor_data->accept_by)->first();
							// 	if($survey_price_data->type=='daily')
							// 	{
							// 		$survey_price=$survey_price_data->survey_price*$survey_price_data->no_of_days;
							// 	}else{
							// 	 $survey_price=$survey_price_data->survey_price;
							// 	}
							// }
							

							// $user_port_data = UsersPort::select('users_port.cost')
							// ->where("users_port.port_id",$surveyor_data->port_id)
							// ->where("users_port.user_id",$surveyor_data->accept_by)->first();
							// if(!empty($user_port_data ))
							// {
							// 	$port_price=$user_port_data->cost;
							// }
							
							// if($survey_price!="" || $port_price!="")
							// {
							// 	$total_price=$survey_price + $port_price ;

							// }
							$user = Auth::user();
							$pricing="0";
							$pricing1="0";
							$pricing2="0";
							$surveyor_name="";
							$op_company_name="";
							$su_company_name="";
							$userdata = User::where('id',$user->id)->first();
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
										$pricing=$survey_ch->amount;
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
								if(!empty($survey_ch)){
									$pricing1=$helper->SurveyorPrice($surveyor_data->survey_type_id,$survey_ch->surveyors_id);						
									$pricing2=$helper->SurveyorPortPrice($surveyor_data->port_id,$survey_ch->surveyors_id);
								}
																		

										
										if($pricing1!="" || $pricing2!="")
										{
											$pricing2=!empty($pricing2)?$pricing2:'0';
											$pricing1=!empty($pricing1)?$pricing1:'0';

											$pricing=$pricing1+$pricing2;
										}	
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
		
											
		
											$suusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as suusername'),'company')->where('id',$surveyor_id_id)->first();
										
											if(!empty($suusername)){
												 $surveyor_name=$suusername->suusername;
												$su_company_name=$suusername->company;
											}
		
									}
								
										if($surveyor_data->assign_to_op=="")
										{
											$opusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as opusername'),'company')->where('id',$surveyor_data->operator_id)->first();
											$op_company_name=$opusername->company;
										}else{
											$opusername = User::select(DB::raw('CONCAT(first_name, "  ", last_name) as opusername'),'company')->where('id',$surveyor_data->assign_to_op)->first();
											$op_company_name=$opusername->company;
										}



								$bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)->where('surveyors_id',$user->id)->where('status','upcoming')->count();
								if($bid_count>0){
								$bid_status='1';
								}else{
									$bid_status='0';
								}

								
							 	$operator_bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)->where('status','!=','approved')->count();

							//dd($surveyor_data);
							
				 return view('pages.mysurveydetailcal',['surveyor_data'=>$surveyor_data,'surveyor_id_id'=>$surveyor_id_id,'suusername'=>$surveyor_name,
				 'opusername'=>$opusername->opusername,'su_company_name'=>$su_company_name,'op_company_name'=>$op_company_name,
				 'operator_survey_count'=>$operator_survey_count,'country_data'=>$country_data,'total_price'=>$pricing,'cat_price'=>$pricing1,'port_price'=>$pricing2,'bid_status'=>$bid_status,'operator_bid_count'=>$operator_bid_count]);


							//dd($surveyor_data);
							
				//  return view('pages.mysurveydetailcal',['surveyor_data'=>$surveyor_data,'surveyor_id_id'=>$surveyor_id_id,'suusername'=>$suusername,
				//  'operator_survey_count'=>$operator_survey_count,'country_data'=>$country_data,'total_price'=>$pricing,'cat_price'=>$pricing1,'port_price'=>$pricing2,'bid_status'=>$bid_status,'operator_bid_count'=>$operator_bid_count]);
	}	
	public function chatForm($survey_id,$sender_id,$receiver_id)
	{			 return view('pages.chat',['survey_id'=>$survey_id,'sender_id'=>$sender_id,'receiver_id'=>$receiver_id]);
	}	
	
	public function eventdetail($id)
	{					
		$surveyor_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
							DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'),
							'users.company as operator_company'
							,'users.company_website as operator_company_website','users.country_id as user_country_id',
							DB::raw('CONCAT(su.first_name, "  ", su.last_name) as suusername'),
							DB::raw('CONCAT(agents.first_name, "  ", agents.last_name) as agent_name'),
							 'su.profile_pic as image','survey_users.surveyors_id as surveyors_ids', 'vessels.name as vesselsname', 
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
							->where('survey.id',$id)
							->first();

							$operator_survey_count =  Survey::where('user_id',$surveyor_data->user_id)->count();
							$country_data =  Countries::where('id',$surveyor_data->user_country_id)->first();

							$helper=new Helpers;

						//	$pricing=$helper->SurveyorPrice($surveyor_data->survey_type_id,$surveyor_data->surveyors_ids);	
						
						$pricing1=$helper->SurveyorPrice($surveyor_data->survey_type_id,$surveyor_data->surveyors_ids);						
						$pricing2=$helper->SurveyorPortPrice($surveyor_data->port_id,$surveyor_data->surveyors_ids);
						$pricing="0";
						if($pricing1!="" && $pricing2!="")
							{
								$pricing=$pricing1+$pricing2;
							}	
// $total_price=0;
// 								$survey_price=0;
// 								$port_price=0;
// 								if($surveyor_data->survey_type_id=='31')
// 								{
// 									$custom_survey_price_data = Customsurveyusers::select('custom_survey_users.*')
// 									->where("custom_survey_users.survey_id",$surveyor_data->id)
// 									->where("custom_survey_users.surveyors_id",$surveyor_data->accept_by)->first();
// 									 $survey_price=$custom_survey_price_data->amount;
// 								}else{
	
// 									$survey_price_data = UsersSurveyPrice::select('users_survey_price.*')
// 									->where("users_survey_price.survey_type_id",$surveyor_data->survey_type_id)
// 									->where("users_survey_price.user_id",$surveyor_data->accept_by)->first();
// 									if($survey_price_data->type=='daily')
// 									{
// 										$survey_price=$survey_price_data->survey_price*$survey_price_data->no_of_days;
// 									}else{
// 									 $survey_price=$survey_price_data->survey_price;
// 									}
// 								}
								
	
// 								$user_port_data = UsersPort::select('users_port.cost')
// 								->where("users_port.port_id",$surveyor_data->port_id)
// 								->where("users_port.user_id",$surveyor_data->accept_by)->first();
// 								if(!empty($user_port_data ))
// 								{
// 									$port_price=$user_port_data->cost;
// 								}
								
// 								if($survey_price!="" || $port_price!="")
// 								{
// 									$total_price=$survey_price + $port_price ;
	
// 								}
							
							//dd($surveyor_data);
							
				 return view('pages.eventdetail',['surveyor_data'=>$surveyor_data,
				 'operator_survey_count'=>$operator_survey_count,'country_data'=>$country_data,'pricing'=>$pricing]);
	}	
	public function SurveyAcceptReject(Request $request)
	{   
		
		$surveyors_id =  $request->input('surveyors_id');
		$survey_id =  $request->input('survey_id');
		$type =  $request->input('type');
		$assign_to =  $request->input('assign_to');

		$survey_users =  SurveyUsers::select('survey_users.*')
		->where('survey_users.survey_id',$survey_id)
		->where('survey_users.surveyors_id',$surveyors_id)
		->first();
				$surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_id)->first();
					
				if(!empty($surveyor_data))
				{

					$operator_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyor_data->user_id)->first(); 
					if($type=="accept")
					{   $survey_users->status="upcoming";
						$surveyor_data->status='1';
						$surveyor_data->assign_to=$assign_to;
						$surveyor_data->accept_by=$surveyors_id;
						$surveyor_data->save(); 
						$helper=new Helpers;
						$helper->SendNotification($operator_token->device_id,'Accept Your Survey Request','Your Survey request has been confirmed by iMarS.You can manage your request by logging into your account.');

					

						$notification = new Notification();
						$notification->user_id = $operator_token->id;
						$notification->title = 'Accept Your Survey Request';
						$notification->noti_type = ' Survey Request' ;
						$notification->user_type = $operator_token->type;
						$notification->notification = 'Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.';
						$notification->country_id = $operator_token->country_id;
						$notification->is_read = 0;
						$notification->save();
						$data1 = array( 'email' =>$operator_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $operator_token->email,'content' => 'Accept Your Survey Request','Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.'));
						Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
						{
							$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Accept Your Survey Request' );
		
						});
										
					}else
					{
								  
						
						$survey_users->status="declined";
						
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
						$helper->SendNotification($operator_token->device_id,'Decline Your Survey Request','Your primary Surveyor has declined the Survey request. Your Substitute 1, Substitute 2, and other eligible Surveyors will be contacted in order automatically to fulfill your Surveyor request within 24 hours. You will be notified once a Surveyor is assigned to the Survey request
						');
						$notification = new Notification();
						$notification->user_id = $operator_token->id;
						$notification->title = 'Decline Your Survey Request';
						$notification->noti_type = 'Decline Survey Request' ;
						$notification->user_type = $operator_token->type;
						$notification->notification = 'Your primary Surveyor has declined the Survey request. Your Substitute 1, Substitute 2, and other eligible Surveyors will be contacted in order automatically to fulfill your Surveyor request within 24 hours. You will be notified once a Surveyor is assigned to the Survey request';
						$notification->country_id = $operator_token->country_id;
						$notification->is_read = 0;
						$notification->save();

						$data1 = array( 'email' =>$operator_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $operator_token->email,'content' => 'Your primary Surveyor has declined the Survey request. Your Substitute 1, Substitute 2, and other eligible Surveyors will be contacted in order automatically to fulfill your Surveyor request within 24 hours. You will be notified once a Surveyor is assigned to the Survey request'));
						Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
						{
							$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Decline Your Survey Request' );
		
						});

						$survey_users_next = SurveyUsers::select('survey_users.*')->where('type', '>', $survey_users->type)->where('survey_users.survey_id',$survey_id)->first();
						if(!empty($survey_users_next))
						{
							$survey_users_next->status='pending';
							$survey_users_next->save();
							$message_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$survey_users_next->surveyors_id)->first(); 
							$helper=new Helpers;
							$helper->SendNotification($message_token->device_id,'Appoint Survey','New survey appoint. Please Accept Survey Request');
							
								$notification = new Notification();
								$notification->user_id = $message_token->id;
								$notification->title = 'Appoint Survey';
								$notification->noti_type = 'Appoint Survey';
								$notification->user_type = $message_token->type;
								$notification->notification = 'New survey appoint. Please Accept Survey Request';
								$notification->country_id = $message_token->country_id;
								$notification->is_read = 0;
								$notification->save();

								$data1 = array( 'email' =>$message_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $message_token->email,'content' => 'New survey appoint. Please Accept Survey Request.'));
								Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
								{
									$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Appoint Survey' );
				
								});
						}
					}
						$survey_users->save();

						$SurveyUsers =new SurveyUsers;
						$Surveyc= $SurveyUsers->where('survey_id',$survey_id)->count();
								
						$Surveydeclinec= $SurveyUsers->where('survey_id',$survey_id)
						->where('status','declined')
						->count();
									 
						if($Surveyc==$Surveydeclinec)
						{
							$surveyor_data->declined='1';
							$surveyor_data->active_thread='0';
							$surveyor_data->save(); 
						}
						
						$status=1;
						$message = 'Survey request ' .$type.' successfully.';
						return response()->json(['class'=>"success" ,'message'=>$message]);

				}else{
				
					return response()->json(['class'=>"danger" ,'message'=>"Data Not Found"]);

				}
			
		
		
	}

public function changepassword()
{					
	return view('pages.changePassword');
}				

public function changepasswordpost(Request $request)
{
	$user = Auth::user();
	$rules=array('old_password'=> 'required',
	'new_password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/',
	'confirm_password' => 'required|same:new_password');
	$message=array('new_password.regex'=>'Password must be between 8 and 16 characters & contain one lower & uppercase letter, and one non-alpha character (a number or a symbol.');
	$validator = Validator::make($request->all(),$rules,$message);


		if ($validator->fails()) 
		{
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else
		{
			$password =  $request->input('new_password');
			$old_password =  $request->input('old_password');

			if(!Hash::check($old_password, $user->password))
			{
				return response()->json(['class'=>"danger" ,'message'=>"Old Password Wrong"]);
				
			}else{
				
				$user->password = Hash::make($password);
				$user->save();
				return response()->json(['class'=>"success" ,'message'=>"Password change succesfully"]);
			}
			
		}
}


public function reportissue()
{	$user = Auth::user();
    $user_id=$user->id;
	if($user->type=='0' || $user->type=='1')
	{
		if($user->type=='0')
		{
			$survey_data=Survey::whereRaw("FIND_IN_SET('". $user_id."',user_id)")->get();
			$survey_data_box=array(''=>"Select Survey");
				foreach ($survey_data as $key => $value) {
					$survey_data_box[$value->id]=$value->survey_number;
				}
		}else
		{
			$createdbysurveyor =  User::select('created_by')->where('id',$user->id)->first();

			$survey_data=Survey::whereRaw("FIND_IN_SET('". $createdbysurveyor->created_by."',user_id)")->get();
			$survey_data_box=array(''=>"Select Survey");
				foreach ($survey_data as $key => $value) {
					$survey_data_box[$value->id]=$value->survey_number;
				}
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
								//$createdbysurveyor =  User::select('id')->where('id',$user_id)->get(); 
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
				
		

			

						$survey_data_box=array(''=>"Select Survey");
						foreach ($survey_data as $key => $value) {
							$survey_data_box[$value->id]=$value->survey_number;
						}
		}else{
					$survey_data=Survey::whereRaw("FIND_IN_SET('". $user_id."',assign_to)")
					->get();

					$survey_data_box=array(''=>"Select Survey");
						foreach ($survey_data as $key => $value) {
							$survey_data_box[$value->id]=$value->survey_number;
						}
		}
	}

//dd($survey_data_box);
					
	return view('pages.report-issue',['survey_data_box'=>$survey_data_box]);
}				

public function reportissuepost(Request $request)
{
	$user = Auth::user();
	$validator = Validator::make($request->all(), [
		'survey_id'=> 'required',
		'comment' => 'required',
		
	]);
		if ($validator->fails()) 
		{
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else
		{
			$survey_id =  $request->input('survey_id');
			$comment =  $request->input('comment');

			$Disputerequest=new Disputerequest;
			$Disputerequest->requested_id=$user->id;
			$Disputerequest->survey_id=$survey_id;
			$Disputerequest->comment=$comment;
			$file_data = $request->file('file');

						if(isset($file_data))
						{
							$imageName = time().$file_data->getClientOriginalName();
							$file_data->move(public_path().'/media/users/dispute-request', $imageName);
							$imageName =str_replace(" ", "", $imageName);
							$Disputerequest->file = $imageName;
						}
				
				$Disputerequest->save();
				return response()->json(['class'=>"success" ,'message'=>"Your request has been successfully submitted"]);
			
			
		}
}
	public function appointsurveyor()
	{				
				$user = Auth::user();	
				$port_data =  Port::select('*')->where('status','1')->get();
				
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
									if(!empty($createdbysurveyor)){
										
											$ids[]=$createdbysurveyor->created_by;
									}
										array_push($ids,$user->id);
						}
				
				$vessels_data =  Vessels::select('*')->whereIn('user_id',$ids)
				
				->orderBy('vessels.name','asc')->get();

				
				
				
				$surtype_data =  Surveytype::select('*')->where('status','1')->orderBy('name','Asc')->get();
				
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

				
						foreach($surtype_data  as $data )
						{
							$data1['type_data'][] =array('id'=>(string)($data->id),
							'name'=>$data->name,
							'price'=>$data->price);
						}
						$surtype_data_box=array(''=>"Select Survey Type");
						foreach ($surtype_data as $key => $value) {
							$surtype_data_box[$value->id]=$value->name;
						}
						
						
						$port_box=array(''=>"Select Port");
						foreach ($port_data as $key => $value) {
							$port_box[$value->id]=$value->port;
						}
						$vessels_box=array(''=>"Select Vessels");
						foreach ($vessels_data as $key => $value) {
							$vessels_box[$value->id]=$value->name;
						}
						$vessels_box['add']='Add a New Vessel';
					
						
						$agent_box=array(''=>"Select Agent");
						foreach ($agent_data as $key => $value) {
							$agent_box[$value->id]=$value->first_name.' '.$value->last_name;
						}
						$agent_box['add']='Add a New Agent';
					 return view('pages.appoint-surveyor',['surtype_data_box'=>$surtype_data_box,'port_box'=>$port_box,
					 'vessels_box'=>$vessels_box,'agent_box'=>$agent_box,]);
	}

	
	public function getsurveyor(Request $request)
	{
			$survey_type_id =  $request->input('survey_type_id');
			$port_id =  $request->input('port_id');
			$start_date =  date('Y-m-d',strtotime($request->input('start_date'))).' 00:00:00';
			$end_date =  date('Y-m-d',strtotime($request->input('end_date'))).' 23:59:59';
			$sort=$request->input('sort');
			         if($survey_type_id=="31")
						{
							$surveyor_data = DB::table('users')->select('users.*')
								->leftJoin('events', 'events.user_id', '=', 'users.id')
								->where('users.status','1')
								->where('users.is_avail','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','2' )
												->orWhere('users.type', '=', '4');
										})
							->where('users.conduct_custom', '=', '1')
							->groupBy('users.id')
								->paginate(10);
						}else
						{
							DB::enableQueryLog(); // Enable query log
							$surveyor_data = DB::table('users')->select('users.*',
							'users_survey_price.survey_price','users_port.cost','users_survey_price.type as price_type')
								->leftJoin('users_survey_price', 'users_survey_price.user_id', '=', 'users.id')
								->leftJoin('users_port', 'users_port.user_id', '=', 'users.id')
								->where('users_survey_price.survey_type_id',$survey_type_id)
								->where('users_port.port_id',$port_id)
								->where('users.status','1')
								->where('users.is_avail','1')
								->where(function ($query) 
										{
											$query->where('users.type', '=','2' )
												->orWhere('users.type', '=', '4');
										})
										->paginate(10);
						
						//dd(DB::getQueryLog()); // Show results of log
						}	
								
             // dd($surveyor_data);
			//$surveyor_data =  User::select('*')->where('survey_category_id',$survey_type_id)->get();
			$data=array();
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
									   ->where('events.start_event',$start_date)
									   ->whereIn('events.user_id',$ids)
									   ->where('events.title','0')
									  ->count();
								 
						

						

						// $surveyor_survey_ap_count =  Survey::select('survey.*')
						// ->where('survey.accept_by',$surveyor->id)
						// ->where('survey.status','1')
						// ->where('survey.start_date',$start_date)						
						// ->count();
						//echo $surveyor_survey_ap_count;
						//echo $surveyor_event_data;
						if( $surveyor_event_data!=$users_count)
							{
								if($surveyor->conduct_custom=='1')
									{
										$first = DB::table('custom_survey_users')->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,updated_at,created_at)) as recponce_time'))->where('surveyors_id',$surveyor->id);
										 $responce = DB::table('survey_users')->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,updated_at,created_at)) as recponce_time'))->where('surveyors_id',$surveyor->id)->union($first)->first();

										
												$average_response_time=(int)$responce->recponce_time*(-1);
												
												$average_response_time=gmdate("g:i", $average_response_time);
												$average_response_time=explode(':',$average_response_time);
												if($average_response_time[0]=='00'){
													$average_response_time=$average_response_time[1].' min';
												}elseif($average_response_time[1]=='00'){
													$average_response_time=$average_response_time[0].' hours ';
				
												}else{
													$average_response_time=$average_response_time[0].' hours '.$average_response_time[1].' min';
												}
												
												
									}else
									{
								
												 $responce=SurveyUsers::select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,updated_at,created_at)) as recponce_time'))
												->where('surveyors_id',$surveyor->id)->first();
												 $average_response_time=(int)$responce->recponce_time*(-1);
												
												 $average_response_time=gmdate("g:i", $average_response_time);
											
												$average_response_time=explode(':',$average_response_time);
												if($average_response_time[0]=='00'){
													$average_response_time=$average_response_time[1].' min';
												}elseif($average_response_time[1]=='00'){
													$average_response_time=$average_response_time[0].' hours ';
				
												}else{
													$average_response_time=$average_response_time[0].' hours '.$average_response_time[1].' min';
												}
												
												
									}
									
									$first = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$surveyor->id);
		
									$total_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$surveyor->id)->union($first)->count();
		
									$second = DB::table('survey_users')->select('survey_users.id')->where('surveyors_id',$surveyor->id) ->where('status','upcoming');
		
									$total_accept_job = DB::table('custom_survey_users')->select('custom_survey_users.id')->where('surveyors_id',$surveyor->id)->where('status','upcoming')->union($second)->count();
							
								
									$percentage_job_acceptance="0";
									if($total_accept_job!="" && $total_accept_job!="0" && $total_job!="" && $total_job!="0")
									{
											$percentage_job_acceptance=floor($total_accept_job/$total_job*100);
									}
										

								  $helper=new Helpers;
								  $pricing1=$helper->SurveyorPrice($survey_type_id,$surveyor->id);						
								  $pricing2=$helper->SurveyorPortPrice($port_id,$surveyor->id);
								  
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
								  
								$data[]=array('id'=>$surveyor->id,
								
								'first_name'=>$surveyor->first_name,
								'last_name'=>$surveyor->last_name,
								'about_me'=>$surveyor->about_me,
								'experience'=>$surveyor->experience,
								'company'=>$company,
								'mobile'=>$surveyor->mobile,
								'pricing'=>!empty($pricing) ? $pricing : '0',
								'port_price'=>!empty($pricing2) ? $pricing2 : '0',
								'rating'=>!empty($surveyor->rating) ? $surveyor->rating : '',
								'average_response_time'=>!empty($average_response_time) ? (string)$average_response_time : '',
								'percentage_job_acceptance'=>!empty($percentage_job_acceptance) ? (string)$percentage_job_acceptance: '',
								'email'=>$surveyor->email,
								'price_type'=>!empty($surveyor->price_type) ? (string)$surveyor->price_type : '',
								'image'=>URL::to('/media/users').'/'.$surveyor->profile_pic);
							}

					
			
		}

		   $helper=new Helpers;
		   

			if($sort=='job_ac'){
				$price = array();
				foreach ($data as $key => $row)
						{
							$price[$key] = $row['percentage_job_acceptance'];
						}
						array_multisort($price, SORT_DESC, $data);
					//	dd($data);
					//$Array = $helper->phparraysort($data, array('rating'),SORT_DESC);
			

			}else if($sort=='low_high'){
				
				$price = array();
						foreach ($data as $key => $row)
						{
							$price[$key] = $row['pricing'];
						}
						array_multisort($price, SORT_ASC, $data);
						//dd($data);
					//$Array = $helper->phparraysort($data, array('rating'),SORT_ASC);

			}
			else if($sort=='high_low'){
						$price = array();
						foreach ($data as $key => $row)
						{
							$price[$key] = $row['pricing'];
						}
						array_multisort($price, SORT_DESC, $data);
						//dd($data);
					$Array = $helper->phparraysort($data, array('rating'),SORT_DESC);

				}
			else{
					$price = array();
					foreach ($data as $key => $row)
					{
						$price[$key] = $row['rating'];
					}
					array_multisort($price, SORT_DESC, $data);

			}
       
			//dd($data);
			return view('pages.surveyor-list',['surveyor_user_data'=>$data,'survey_type_id'=>$survey_type_id]);
	}
	public function getport(Request $request)
	{
			$country_id =  $request->input('country_id');
			$port_data =  Port::select('*')->where('country_id',$country_id )->where('status','1' )->orderBy('port','ASC')->get();

			
			return view('pages.port_list',['port_data'=>$port_data]);
	}
	
	public function appointsurveyorpost(Request $request)
	{  $user = Auth::user();
		$survey_type_id =  $request->input('survey_type_id');
		if($survey_type_id =='31'){
			$validator = Validator::make($request->all(), [
		
				'agent' => 'required',
				'instruction' => 'required',
				'file_data' => 'required',
				
				'port_id' => 'required',
				'ship_id' => 'required',
				'start_date' => 'required',
				'end_date' => 'required',
				'survey_type_id' => 'required',
				
				]);
		}else
		{
		$validator = Validator::make($request->all(), [
		
			'agent' => 'required',
			'port_id' => 'required',
			'ship_id' => 'required',
			'start_date' => 'required',
			'end_date' => 'required',
			'survey_type_id' => 'required',
			
			]);
		}
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				
				return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
			}else
			{

				
				
			
				$agent_id =  (string)($request->input('agent'));
				$port_id =  $request->input('port_id');
				$ship_id =  $request->input('ship_id');
			    $start_date =  date('Y-m-d',strtotime($request->input('start_date')));
				$end_date =  date('Y-m-d',strtotime($request->input('end_date')));
			
				$surveyors_id =  $request->input('surveyors_id');
				$instruction =  !empty($request->input('instruction')) ? $request->input('instruction') : '';
				
				//$last_status =  $request->input('last_status');
						$file_data = $request->file('file_data');
						$country_data=  Port::select('country_id')->where('id',$port_id)->first();

						$survey= new Survey();
						$survey->user_id=$user->id;
						$survey->agent_id=$agent_id;
						$survey->port_id=$port_id;
						$survey->country_id=$country_data->country_id;
						$survey->ship_id=$ship_id;
						$survey->start_date=$start_date;
						$survey->end_date=$end_date;
						$survey->survey_type_id=$survey_type_id;
						$survey->surveyors_id=implode(',',$surveyors_id);
						$survey->instruction=$instruction;
						
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
						if(isset($file_data))
						{
							$imageName = time().$file_data->getClientOriginalName();
							$imageName =str_replace(" ", "", $imageName);
							$file_data->move(public_path().'/media/survey', $imageName);
							
							$survey->file_data = $imageName;
						}
						$survey->save();
						
						$usersdata = $request->input('surveyors_id');
						$pos = $request->input('pos');
						if($survey_type_id=='31') 
						{
							foreach($usersdata as $key => $value)
							{
								$csurveyuser= new Customsurveyusers();
								$csurveyuser->surveyors_id =$value;
								$csurveyuser->survey_id = $survey->id;
								$status="waiting";
								$csurveyuser->status = $status;
								$csurveyuser->save();

								$message_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('users.id',$value)
								->first();
								//echo $message_token->id;
								$helper=new Helpers;
								$helper->SendNotification($message_token->device_id,'Appoint Survey','New survey appoint. Please Accept Survey Request');
								
										  
								$notification = new Notification();
								$notification->user_id = $message_token->id;
								$notification->title = 'Appoint Survey';
								$notification->noti_type = 'Appoint Survey';
								$notification->user_type = $message_token->type;
								$notification->notification = 'New survey appoint. Please Accept Survey Request';
								$notification->country_id = $message_token->country_id;
								$notification->is_read = 0;
								$notification->save();
								$data1 = array( 'email' =>$message_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $message_token->email,'content' => 'New survey appoint. Please Accept Survey Request'));
								Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
								{
									$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Appoint Survey' );
				
								});
								
										
							}

						}else
						{

						
							$usercount = count($usersdata);

							if($usercount == 1)
							{$uservalue = array('1');}else if($usercount == 2){$uservalue = array('1','2');	}else{$uservalue = array('1','2','3');}
							
							$usersdata1 = array_combine($usersdata,array_unique($pos));
							//dd($usersdata1);
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
								$message_token =  SurveyUsers::select('users.id','users.email','users.type','users.device_id','users.country_id')
								->leftJoin('users', 'survey_users.surveyors_id', '=', 'users.id')
								->where('survey_users.survey_id',$survey->id)
								->where('survey_users.type','1')
								->first();
								//echo $message_token->id;
								$helper=new Helpers;
								$helper->SendNotification($message_token->device_id,'Appoint Survey','New survey appoint. Please Accept Survey Request');
										
										$notification = new Notification();
										$notification->user_id = $message_token->id;
										$notification->title = 'Appoint Survey';
										$notification->noti_type = 'Appoint Survey';
										$notification->user_type = $message_token->type;
										$notification->notification = 'New survey appoint. Please Accept Survey Request';
										$notification->country_id = $message_token->country_id;
										$notification->is_read = 0;
										$notification->save();

										$data1 = array( 'email' =>$message_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $message_token->email,'content' => 'New survey appoint. Please Accept Survey Request'));
										Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										{
											$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Appoint Survey' );
						
										});
						}
					
						
						$status=1;
						$message = 'Survey request sent successfully.';
						return response()->json(['class'=>"success" ,'message'=>$message]);

					
			}
					
	}

	
	public function mycalendar(Request $request)
	{
		$user = Auth::user();

		// $userportdetail = UsersPort::select('users_port.*','p.port as portname')
		// ->leftJoin('port as p', 'users_port.port_id', '=', 'p.id')
		// ->where('users_port.user_id',$user->id)->get();
		//dd($userportdetail);
		

		return view('pages.mycalendar',[]);	
		

	}

	public function GetCalender(Request $request)
	{
		$func = $request->input('func');
		$year = $request->input('year');
		$month = $request->input('month');
		$date = $request->input('date');
	   $surveyor_id = $request->input('surveyor_id');

		$helper=new Helpers();
		if( !empty($func)){ 
		switch($func){ 
			case 'getCalender': 
			$helper->getCalender($year,$month,$surveyor_id); 
				break; 
			case 'getEvents': 
			$helper->getEvents($date); 
				break; 
			default: 
				break; 
		} 
	} 
	}
	// public function eventsload()
	// {
	// 				$user = Auth::user();
	// 				$events=Events::select('events.id as id','events.title','events.start_event','events.end_event')->where('user_id',$user->id)->get();
	// 				$surveyor_data =  Survey::select('survey.id as id','survey.survey_number as title','survey.start_date as start_event',
	// 				'survey.end_date as end_event')	
	// 				->leftJoin('survey_users', 'survey_users.survey_id', '=', 'survey.id')
	// 				->groupBy('survey_users.survey_id')
	// 				->where(function ($query) {
	// 				$query->where('survey_users.status', '=','pending' )
	// 				->orWhere('survey_users.status', '=', 'upcoming');
	// 				})
	// 				->where('survey_users.surveyors_id',$user->id)
	// 				->get();

	// 				foreach($surveyor_data as $data)
	// 				{
	// 					$events=Events::select('events.id as id','events.title','events.start_event','events.end_event')
	// 					->where('user_id',$user->id)
	// 					 ->whereNotBetween('start_event', [$data->start_event, $data->end_event])
	// 					->get();
	// 				 }
	// 				 //dd($events);
	// 			 foreach($events as $event)
	// 			 {
	// 				 $surveyor_data->add($event);
	// 			 }	
	// 	         $data=array();

	// 	foreach($surveyor_data as $event)
	// 	{
	// 		if($event->title=='1'){
	// 		   $class="onuser";
	// 		   $type="";
	// 		   $title="ON";
	// 		   $end_event=$event->end_event;
	// 		}elseif($event->title=='0'){
	// 			$class="offuser";
	// 			$type="";
	// 			$title="OFF";
	// 			$end_event=$event->end_event;
	// 		}
	// 		else{
	// 			$class="running";
	// 			$type="survey";
	// 			$title=$event->title;
	// 			$end_event=date('Y-m-d', strtotime('+1 day', strtotime($event->end_event)));
	// 		}
	// 		$data[] = array(
	// 			'id'   => $event->id,
	// 			'title'   =>$title,
	// 			'start'   =>$event->start_event,
	// 			'end'   => $end_event,
	// 			'className'   => $class,
	// 			'type'   => $type,
	// 			'imageurl'=>'https://localhost/imars/public/media/logo-icon.png'
			
			
	// 		);
	// 	}
	//     echo json_encode($data);
	// }
	public function eventsadd(Request $request)
	{
		$user = Auth::user();
		$title =  $request->input('title');
		$start =  $request->input('start');
		$events=Events::select('events.id')->where('user_id',$user->id)->where('start_event',$start)->first();
	
		if(empty($events)){
			$events=new Events;
		}

		
		$events->user_id=$user->id;
		$events->title=$title;
		$events->start_event=$start;
		$events->end_event=$start;
		$events->save();
		
	  
	}
	
	// public function eventsupdate(Request $request)
	// {
	// 	$user = Auth::user();
	// 	$id =  $request->input('id');
	// 	$title =  $request->input('title');
	// 	$start =  $request->input('start');
	// 	$end =  $request->input('end');
	// 	if($id){
	// 		$events= Events::where('id',$id)->first();
	// 		$events->user_id=$user->id;
	// 		$events->title=$title;
	// 		$events->start_event=$start;
	// 		$events->end_event=$end;
	// 		$events->save();
			
	// 	}
		

		
	   
	// }
	// public function eventsdelete(Request $request)
	// {
	// 	$user = Auth::user();
	// 	$id =  $request->input('id');
	// 	if($id){
	// 		$events= Events::where('id',$id)->first();
	// 		$events->delete();
	// 	}
	
	// }
	
	public function PaymentDetail(Request $request)
	{
		$user = Auth::user();

		$user_country_detail = Countries::select('*')->where('id',$user->country_id)->first();
		
		// echo $payment_type;exit;
		$userBankdetail=  Bankdetail::where('user_id', $user->id)->first();
		$bank=array(
			'acc_holder_name'=>!empty($userBankdetail->acc_holder_name) ? $userBankdetail->acc_holder_name : '',
			'routing_number'=>!empty($userBankdetail->routing_number) ? $userBankdetail->routing_number : '',
			'acc_number'=>!empty($userBankdetail->acc_holder_name) ? $userBankdetail->acc_number : '',
			'ach_acc_number'=>!empty($userBankdetail->acc_holder_name) ? $userBankdetail->ach_acc_number : '',
			'company_name'=>!empty($userBankdetail->company_name) ? $userBankdetail->company_name : '',
			'beneficiary_name'=>!empty($userBankdetail->beneficiary_name) ? $userBankdetail->beneficiary_name : '',
			'beneficiary_address'=>!empty($userBankdetail->beneficiary_address) ? $userBankdetail->beneficiary_address : '',
			'bank_name'=>!empty($userBankdetail->bank_name) ? $userBankdetail->bank_name : '',
			'swift_code'=>!empty($userBankdetail->swift_code) ? $userBankdetail->swift_code : '',
			'more_info'=>!empty($userBankdetail->more_info) ? $userBankdetail->more_info : '',
			'file_type'=>!empty($userBankdetail->file_type) ? $userBankdetail->file_type : '',
			'file'=>!empty($userBankdetail->file) ? $userBankdetail->file : '',
			'paypal_email_address'=>!empty($userBankdetail->paypal_email_address) ? $userBankdetail->paypal_email_address : '',
			'country'=>!empty($userBankdetail->country) ? $userBankdetail->country : '',
			'current_payment'=>!empty($userBankdetail->current_payment) ? $userBankdetail->current_payment : '',

			'city'=>!empty($userBankdetail->city) ? $userBankdetail->city : '',

			'state'=>!empty($userBankdetail->state) ? $userBankdetail->state : '',

			'street_address'=>!empty($userBankdetail->street_address) ? $userBankdetail->street_address : '',

			'pincode'=>!empty($userBankdetail->pincode) ? $userBankdetail->pincode : '',


		);
		
		return view('pages.bank_detail',['user_country_detail'=>$user_country_detail,'bank'=>$bank]);	
	}

	public function PaymentDetailPost(Request $request)
	{   $user = Auth::user();
		
		$user_country_detail = Countries::select('*')->where('id',$user->country_id)->first();
		$payment_method =  $request->input('payment_method');

		if($payment_method=='paypal'){
			$validator = Validator::make($request->all(), [
				'payment_method'=>'required',
				'current_payment'=>'required',
				'paypal_email_address'=>'required|email'
				
					]);
		}else{
		$validator = Validator::make($request->all(), [
		'payment_method'=>'required',
		'current_payment'=>'required',
			]);
		}
			if ($validator->fails()) 
			{
				
				return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
			}else
			{

				$paypal_email_address =  $request->input('paypal_email_address');
				 $acc_holder_name  =  $request->input('acc_holder_name');
				$routing_number =  $request->input('routing_number');
				$acc_number =  $request->input('acc_number');
				
				$ach_acc_number =  $request->input('ach_acc_number');

				$company_name =  $request->input('company_name');
				$beneficiary_name =  $request->input('beneficiary_name');
				$bank_name =  $request->input('bank_name');
				$swift_code =  $request->input('swift_code');
				$more_info =  $request->input('more_info');
				$file_data = $request->file('file_data');

				$street_address =  $request->input('street_address');
				$city =  $request->input('city');
				$state =  $request->input('state');
				$pincode =  $request->input('pincode');
				$country =  $request->input('country');
				$current_payment =  $request->input('current_payment');
				


				$userBankdetail=  Bankdetail::where('user_id', $user->id)->first();
				if(empty($userBankdetail))
				{
					$userBankdetail=  new Bankdetail;
				}
				$userBankdetail->user_id=$user->id;

				if(	$street_address!=""){
					$userBankdetail->street_address = $street_address;
				}
				if(	$city!=""){
					$userBankdetail->city = $city;
				}
				if(	$state!=""){
					$userBankdetail->state = $state;
				}
				if(	$pincode!=""){
					$userBankdetail->pincode = $pincode;
				}
				if(	$country!=""){
					$userBankdetail->country = $country;
				}
				$userBankdetail->current_payment=!empty($current_payment) ? $current_payment: '';

				$userBankdetail->paypal_email_address=!empty($paypal_email_address) ? $paypal_email_address: '';
				$userBankdetail->acc_holder_name=!empty($acc_holder_name) ? $acc_holder_name: '';
				$userBankdetail->routing_number=!empty($routing_number) ? $routing_number: '';
				$userBankdetail->acc_number=!empty($acc_number) ? $acc_number: '';
				$userBankdetail->ach_acc_number=!empty($ach_acc_number) ? $ach_acc_number: '';
				$userBankdetail->company_name=!empty($company_name) ? $company_name: '';
				$userBankdetail->beneficiary_name=!empty($beneficiary_name) ? $beneficiary_name: '';
				$userBankdetail->bank_name=!empty($bank_name) ? $bank_name: '';
				$userBankdetail->swift_code=!empty($swift_code) ? $swift_code: '';
				$userBankdetail->more_info=!empty($more_info) ? $more_info: '';

							
				if(isset($file_data))
				{
					$imageName = time().$file_data->getClientOriginalName();
					$file_type=$file_data->getClientMimeType();
					
					$imageName =str_replace(" ", "", $imageName);
					$file_data->move(public_path().'/media/bank_instruction', $imageName);
					$userBankdetail->file = $imageName;
					$userBankdetail->file_type = $file_type;
				}
				$userBankdetail->save();
						        
				$status=1;
				$message = 'Update  Bank Detail successfully.';
				return response()->json(['class'=>"success" ,'message'=>$message,'payment_method'=>$payment_method]);

					
			}
					
	}
	
public function reportsubmit(Request $request)
{
	$survey_id =  $request->input('survey_id');
	$no_of_days =  $request->input('no_of_days');
	$surveyor_data =   Survey::select('survey.*')->where('id',base64_decode($survey_id))->first();

	 if($surveyor_data->survey_type_id=='8' || $surveyor_data->survey_type_id=='23'
                             || $surveyor_data->survey_type_id=='24' || $surveyor_data->survey_type_id=='25' || $surveyor_data->survey_type_id=='29')
                        { 
		
		$validator = Validator::make($request->all(), [
			
			'myfile' => 'required',
				
			'no_of_days' => 'required',
			
			]);
		}else{
			$validator = Validator::make($request->all(), [
			
				'myfile' => 'required',
				
				]);
		}
			if ($validator->fails()) {
				
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else{
				     $user = Auth::user();



					$survey =   Survey::select('survey.*')->where('id',base64_decode($survey_id))->first();
					
					if(!empty($survey))
					{
						$image = $request->file('myfile');
						if(isset($image))
						{
							$imageName = time().$image->getClientOriginalName();
							$image->move(public_path().'/media/report', $imageName);
							$survey->report = $imageName;
						}
					
							$survey->status='3';
							$survey->save();

						if($no_of_days!="" && $no_of_days!="undefined"){
							$UsersSurveyPrice =  UsersSurveyPrice::where('survey_type_id',$survey->survey_type_id)->where('user_id',$user->id)->first();
							
							$UsersSurveyPrice->no_of_days=$no_of_days;

							$UsersSurveyPrice->save();

						}
						
							
							$helper=new Helpers;
							$operator_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$survey->user_id)->first(); 
							$helper->SendNotification($operator_token->device_id,'Survey report has been submitted','Survey report has been submitted.');

							$notification = new Notification();
							$notification->user_id = $operator_token->id;
							$notification->title = 'Survey report has been submitted';
							$notification->noti_type = 'Survey Report Submit';
							$notification->user_type = $operator_token->type;
							$notification->notification = 'Survey report has been submitted';
							$notification->country_id = $operator_token->country_id;
							$notification->is_read = 0;
							$notification->save();

							$data1 = array( 'email' =>$operator_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $operator_token->email,'content' => 'Survey report has been submitted'));
							Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
							{
								$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Survey Report Submit' );
			
							});



							return response()->json(['class'=>'success' ,  'message'=>"You have submit report successfully"]);	
					}else{
						
						return response()->json(['class'=>'danger' ,  'message'=>"Something Went Wrong"]);
					}

				
			}
		

	}
	public function reportaccept(Request $request)
		{
				
				$surveyors_id =  $request->input('surveyors_id');
				$survey_id =  $request->input('survey_id');
				$type =  $request->input('type');

					$surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_id)->first();

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

						$surveyor_token =  User::select('users.company','users.company_address'
						,'c.name as country','users.city','users.state','users.street_address','users.pincode'
						,'users.id','users.email','users.type','users.device_id','users.country_id')
						->leftJoin('countries as c', 'users.country_id', '=', 'c.id')

						->where('users.id',$surveyor_data->accept_by)->first(); 
						if($type=="report_accept")
						{   
							$surveyor_data->status='4';
							

							$helper=new Helpers;
							$helper->SendNotification($surveyor_token->device_id,' Survey report has been accepted',' survey report has been accepted.');

							$notification = new Notification();
							$notification->user_id = $surveyor_token->id;
							$notification->title = 'Survey report has been accepted';
							$notification->noti_type = 'Survey Report Accept';
							$notification->user_type = $surveyor_token->type;
							$notification->notification = 'Survey report has been accepted';
							$notification->country_id = $surveyor_token->country_id;
							$notification->is_read = 0;
							$notification->save();

							$data1 = array( 'email' =>$surveyor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyor_token->email,'content' => 'Survey report has been accepted'));
							Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
							{
								$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Survey Report Accept' );
			
							});

						}
							
							
							

							$total_price=0;
							$survey_price=0;
							$port_price=0;
							if($surveyor_data->survey_type_id=='31')
							{
								$custom_survey_price_data = Customsurveyusers::select('custom_survey_users.*')
								->where("custom_survey_users.survey_id",$surveyor_data->id)
								->where("custom_survey_users.surveyors_id",$surveyor_data->accept_by)->first();
								 $survey_price=$custom_survey_price_data->amount;
							}else{

								$survey_price_data = UsersSurveyPrice::select('users_survey_price.*')
								->where("users_survey_price.survey_type_id",$surveyor_data->survey_type_id)
								->where("users_survey_price.user_id",$surveyor_data->accept_by)->first();
								if($survey_price_data->type=='daily')
								{
									$survey_price=$survey_price_data->survey_price*$survey_price_data->no_of_days;
								}else{
								 $survey_price=$survey_price_data->survey_price;
								}
							}
							

							$user_port_data = UsersPort::select('users_port.cost')
							->where("users_port.port_id",$surveyor_data->port_id)
							->where("users_port.user_id",$surveyor_data->accept_by)->first();
							if(!empty($user_port_data ))
							{
								$port_price=$user_port_data->cost;
							}
							
							if($survey_price!="" || $port_price!="")
							{
								$total_price=$survey_price + $port_price ;

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

							

							

							 $op_token =  User::select('users.company','users.company_address'
							 ,'c.name as country','users.city','users.state',
							 'users.street_address','users.pincode','users.id','users.email',
							 DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'),
							 'users.type','users.device_id','users.country_id')
							 ->leftJoin('countries as c', 'users.country_id', '=', 'c.id')
							 ->where('users.id',$surveyor_data->user_id)->first(); 
							
							 $invoice_ar=array('survey_number'=> $surveyor_data->survey_number,
							 'vesselsname'=> $invoice_data->vesselsname,
							 'imo_number'=> $invoice_data->imo_number,
							 'port_name'=> $invoice_data->port_name,
							 'date'=>date('d-M-Y'),
							 'due_date'=>date('d-M-Y', strtotime('+1 month', strtotime(date('d-M-Y')))),
							 'amount'=>$invoice_data->invoice_amount,
							 'survey_type_price'=>$survey_price,
							 'survey_type_name'=>$invoice_data->survey_type_name,
							 'port_price'=>$port_price,

							 'from'=>array('company'=>$surveyor_token->company ,
							 				'email'=>$surveyor_token->email ,
											'address1'=>$surveyor_token->street_address,
											'address2'=>$surveyor_token->city.' ,'.$surveyor_token->state.' ,'.$surveyor_token->state.' ,'.$surveyor_token->pincode),
							'to'=>array('company'=>$op_token->company ,
							'email'=>$op_token->email ,
							'operator_name'=>$op_token->username ,
							'address1'=>$op_token->street_address,
							'address2'=>$op_token->city.' ,'.$op_token->state.' ,'.$op_token->state.' ,'.$op_token->pincode)
										);
									//	dd($invoice_ar);
									$data2=array('content' => $invoice_ar);
									$pdf = PDF::loadView('pages.invoice', compact('data2'));
									$invoice_file= 'invoice_'.$invoice_data->survey_number.'.pdf';
									$pdf->save(public_path().'/media/invoice/'. $invoice_file);
									$surveyor_data->invoice=$invoice_file;
									$surveyor_data->save();
							 $data1 = array( 'email' =>$op_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $op_token->email,'content' => $invoice_ar));
							Mail::send( 'pages.email.invoice',$data1, function( $message ) use ($data1)
							{
								$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Survey Report Accept' );
			
							});
							
							 $message = 'Report Accept Successfully.';
							 return response()->json(['class'=>"success" ,'message'=>$message]);

					}else{
					
						return response()->json(['class'=>"danger" ,'message'=>"Data Not Found"]);

					}
		}
		
		
		public function addrating($operator_id,$surveyor_id)
		{
			return view('pages.addratingform',['operator_id'=>$operator_id,'surveyor_id'=>$surveyor_id]);
		}
		public function addratingpost(Request $request)
{
		
		$validator = Validator::make($request->all(), [
			
			'operator_id' => 'required',
			'surveyor_id' => 'required',
			'rating_1' => 'required',
			
			]);
			if ($validator->fails()) {
				
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else{
				     $user = Auth::user();
					 $operator_id =  $request->input('operator_id');
					 $surveyor_id =  $request->input('surveyor_id');
					 $comment =  $request->input('comment');
					 $rating_1 =  $request->input('rating_1');
					 $check =  Rating::select('*')->where('operator_id',$user->id )->where('surveyor_id',base64_decode($surveyor_id) )->first(); 
				
					 if(empty($check))
					{
						$rating =  new Rating ;
											
						$rating->operator_id=$user->id;
						$rating->surveyor_id=base64_decode($surveyor_id);
						$rating->rating='3';
						$rating->comment=!empty($comment)?$comment:"";
						$rating->save();
					    
					}
					$rating_data_count =  Rating::select('*')->where('surveyor_id',base64_decode($surveyor_id) )->count(); 
					$total =  Rating::where('surveyor_id',base64_decode($surveyor_id) )->sum('rating'); 
					$user_rating =$total/$rating_data_count;
					if(!empty($user_rating))
					{
						$user= User::where('id',base64_decode($surveyor_id))->first();
						$user->rating=$user_rating;
						$user->save();
						
					}

					
					// echo $user_rating=(int)$total/(int)$rating_data_count;exit;
					return response()->json(['class'=>'success' ,  'message'=>"You have submit rating successfully"]);	
					

				
			}
		

	}
	

	public function uploaddocument(Request $request)
	{
		$user = Auth::user();
		if($user->type=="2")
		{
			$validator = Validator::make($request->all(), [
				'experience' => 'required',
				'about_me' => 'max:1000',
				'invoice_address_to_company' => 'required_without_all:utility_bill,incorporation_certificate',
				'utility_bill' => 'required_without_all:invoice_address_to_company,incorporation_certificate',
				'incorporation_certificate' => 'required_without_all:utility_bill,invoice_address_to_company',
				
				]);
		}elseif($user->type=="4"){
			$validator = Validator::make($request->all(), [
				'experience' => 'required',
				'about_me' => 'max:1000',
				]);
		}
		elseif($user->type=="0"){
				$validator = Validator::make($request->all(), [
				
				]);
		}
			

			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				
				$user = User::where('id',$user->id)->first();
				$diploma = $request->file('diploma');
				$employment_reference_letter = $request->file('employment_reference_letter');
				$certificates = $request->file('certificates');
				$port_gate_pass = $request->file('port_gate_pass');

				$upload_id = $request->file('upload_id');
				$incorporation_certificate = $request->file('incorporation_certificate');
				$utility_bill = $request->file('utility_bill');
				$tax_id_document = $request->file('tax_id_document');
				$invoice_address_to_company = $request->file('invoice_address_to_company');

				$sac_document = $request->file('sac_document');

				$profile_pic = $request->file('profile_pic');

				$about_me = $request->input('about_me');
				$experience = $request->input('experience');
				$ssn = $request->input('ssn');
				$company_tax_id = $request->input('company_tax_id');

				

				if(!empty($ssn )){
					$user->ssn = $ssn;
				}
				if(!empty($company_tax_id )){
					$user->company_tax_id = $company_tax_id;
				}

				if(!empty($experience )){
					$user->experience = $experience;
				}

				if(!empty($about_me )){
					$user->about_me = $about_me;
				}


				if(isset($sac_document))
				{
					$sac_Document = time().$sac_document->getClientOriginalName();
					$sac_document->move(public_path().'/media/users/sac_document', $sac_Document);
					$user->sac_document = $sac_Document;
				}
				if(isset($profile_pic))
				{
					$profile_Pic = time().$profile_pic->getClientOriginalName();
					$profile_pic->move(public_path().'/media/users', $profile_Pic);
					$user->profile_pic = $profile_Pic;
				}

				
				if(isset($invoice_address_to_company))
				{
					$invoice_address_to_Company = time().$invoice_address_to_company->getClientOriginalName();
					$invoice_address_to_company->move(public_path().'/media/users/invoice_address_to_company', $invoice_address_to_Company);
					$user->invoice_address_to_company = $invoice_address_to_Company;
				}
						
				
				if(isset($tax_id_document))
				{
					$tax_id_Document = time().$tax_id_document->getClientOriginalName();
					$tax_id_document->move(public_path().'/media/users/tax_id_document', $tax_id_Document);
					$user->tax_id_document = $tax_id_Document;
				}



				if(isset($utility_bill))
				{
					$utility_Bill = time().$utility_bill->getClientOriginalName();
					$utility_bill->move(public_path().'/media/users/utility_bill', $utility_Bill);
					$user->utility_bill = $utility_Bill;
				}
			
				if(isset($incorporation_certificate))
				{
					$incorporation_Certificate = time().$incorporation_certificate->getClientOriginalName();
					$incorporation_certificate->move(public_path().'/media/users/incorporation_certificate', $incorporation_Certificate);
					$user->incorporation_certificate = $incorporation_Certificate;
				}
			
				if(isset($upload_id))
				{
					$upload_Id = time().$upload_id->getClientOriginalName();
					$upload_id->move(public_path().'/media/users/upload_id', $upload_Id);
					$user->upload_id = $upload_Id;
				}
			
				if(isset($diploma))
				{
					$diplomaName = time().$diploma->getClientOriginalName();
					$diploma->move(public_path().'/media/users/diploma', $diplomaName);
					$user->diploma = $diplomaName;
				}
				if(isset($employment_reference_letter))
				{
					 $employment_reference_letterName = time().$employment_reference_letter->getClientOriginalName();
					  $employment_reference_letter->move(public_path().'/media/users/employment_reference_letter', $employment_reference_letterName);
				
					  $user->employment_reference_letter = $employment_reference_letterName;
				}
				if(isset($certificates))
				{
					$certificatesName = time().$certificates->getClientOriginalName();
					$certificates->move(public_path().'/media/users/certificates', $certificatesName);
					$user->certificates = $certificatesName;
				}
				if(isset($port_gate_pass))
				{
					$port_gate_passName = time().$port_gate_pass->getClientOriginalName();
					$port_gate_pass->move(public_path().'/media/users/port_gate_pass', $port_gate_passName);
					$user->port_gate_pass = $port_gate_passName;
				}
				$user->save();
				$data1 = array( 'email' =>$user->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $user->email,'content' => 'Your profile is under review, and will be verified within 24 hours.'));
				Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
				{
					$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Your profile is under review.' );

				});

			    //dd($user);
				echo json_encode(array('class'=>'success','message'=>'Your profile is under review, and will be verified within 24 hours.'));die;

			
			}
		
	} 
	public function conductcustomsurvey(Request $request)
	{
		$user = Auth::user();
		$conduct_custom =  $request->input('conduct_custom');
		$users=User::where('id',$user->id)->first();
	
		if(!empty($users))
		{
			if($conduct_custom=="0"){
				$users->conduct_custom='1';
				$message="Custom Surveys Added successfully";

			}else{
				$users->conduct_custom='0';
				$message="Custom Surveys Removed successfully";
			}
			$users->save();
			echo json_encode(array('class'=>'success','message'=>$message));die;

		}
		
		
		
	  
	}
	public function isavail(Request $request)
	{
		$user = Auth::user();
		$isavail =  $request->input('is_avail');
		$users=User::where('id',$user->id)->first();
	
		if(!empty($users))
		{
			if($isavail=="0"){
				$users->is_avail='1';
				$message="You availability is permanent On";

			}else{
				$users->is_avail='0';
				$message="You availability is permanent Off";
			}
			$users->save();
			echo json_encode(array('class'=>'success','message'=>$message));die;

		}
		
		
		
	  
	}
	public function CustomeSurveyAcceptReject(Request $request)
	{
		
		$validator = Validator::make($request->all(), [
			
			'amount' => 'required',
			
			
			]);
			if ($validator->fails()) 
			{
				
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				     $user = Auth::user();
					 $amount =  $request->input('amount');
					 $surveyors_id =  $request->input('surveyors_id');
					 $survey_id =  $request->input('survey_id');

					 $survey_users =  Customsurveyusers::select('custom_survey_users.*')
					->where('custom_survey_users.survey_id',$survey_id)
					->where('custom_survey_users.surveyors_id',$surveyors_id)
					->first();
					$surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_id)->first();
					
					if(!empty($surveyor_data) && !empty($survey_users))
					{
						$operator_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyor_data->user_id)->first(); 
						$survey_users->status="upcoming";
						$survey_users->amount=$amount;
						$survey_users->save();

							$helper=new Helpers;
							$helper->SendNotification($operator_token->device_id,'Confirm Survey Request','Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.');
						
							
							$notification = new Notification();
							$notification->user_id = $operator_token->id;
							$notification->title = 'Confirm Survey Request';
							$notification->noti_type = 'Confirm Survey Request';
							$notification->user_type = $operator_token->type;
							$notification->notification = 'Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.';
							$notification->country_id = $operator_token->country_id;
							$notification->is_read = 0;
							$notification->save();
						
						
							return response()->json(['class'=>'success' ,  'message'=>"You have submit bid successfully"]);	
						
					}
			}
		
		}

		public function operatorCustomeSurveyAccept(Request $request)
		{
			
			$validator = Validator::make($request->all(), [
				
				'surveyors_id' => 'required',
				
				
				]);
				if ($validator->fails()) 
				{
					
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
				}else
				{
						 $user = Auth::user();
						 
						 $surveyors_id =  $request->input('surveyors_id');
						 $survey_id =  $request->input('survey_id');
	
						 $survey_users =  Customsurveyusers::select('custom_survey_users.*')
						 ->where('custom_survey_users.survey_id',$survey_id)
						 ->where('custom_survey_users.surveyors_id',$surveyors_id)
						 ->first();
						 $surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_id)->first();
							 
						 if(!empty($surveyor_data) && !empty($survey_users))
						 {
		 
								$survey_users->status="approved";
								$survey_users->save();
		 
								$surveyor_data->status='1';
								$surveyor_data->accept_by=$surveyors_id;
								$surveyor_data->save();


								$survey_usersd =  Customsurveyusers::select('custom_survey_users.*')
								->where('custom_survey_users.survey_id',$survey_id)
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
								 $surveyor_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyors_id)->first(); 
		 
								 $helper->SendNotification($surveyor_token->device_id,'Accept Your Survey Request','Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.');
								
								 $notification = new Notification();
								 $notification->user_id = $surveyor_token->id;
								 $notification->title = 'Confirm Survey Request';
								 $notification->noti_type = 'Confirm Survey Request';
								 $notification->user_type = $surveyor_token->type;
								 $notification->notification = 'Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.';
								 $notification->country_id = $surveyor_token->country_id;
								 $notification->is_read = 0;
								 $notification->save();
								
						 }
							return response()->json(['class'=>'success' ,  'message'=>"Survey request Accept successfully"]);	
							
				}
				
			
			}


	public function CancelSurvey(Request $request)
	{
	
					 $user = Auth::user();

					 $survey_id =  $request->input('survey_id');
					 $surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_id)->first();
					
					 if(!empty($surveyor_data) )
					 {
	 
						 $surveyor_data->status="2";
													  
							 $helper=new Helpers;
	 
							 $csurveyors_id = DB::table('custom_survey_users')->select('surveyors_id')->where('custom_survey_users.id',$survey_id)->get();
							 
							 if(!empty($csurveyors_id))
							 {
								foreach($csurveyors_id as $surveyor_id)
								{
									$surveyors_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyor_id->surveyors_id)->first(); 
		
									$helper->SendNotification($surveyors_token->device_id,'Cancel  Survey','This Survey has been cancelled');
									$notification = new Notification();
									$notification->user_id = $surveyors_token->id;
									$notification->title = 'Cancel  Survey Request';
									$notification->noti_type = 'Cancel  Survey ';
									$notification->user_type = $surveyors_token->type;
									$notification->notification = 'This Survey has been cancelled.';
									$notification->country_id = $surveyors_token->country_id;
									$notification->is_read = 0;
									$notification->save();


									   $data1 = array( 'email' =>$surveyors_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyors_token->email,'content' => 'This Survey has been cancelled'));
										Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										{
											$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Cancel  Survey' );
						
										});

										$opeartor_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyor_data->user_id)->first(); 
										$data1 = array( 'email' =>$opeartor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $opeartor_token->email,'content' => 'This Survey has been cancelled'));
										Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										{
											$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Cancel  Survey' );
						
										});
									
								}
							 }
							 

							 $surveyors_id = DB::table('survey_users')->select('surveyors_id')->where('survey_users.id',$survey_id)->get();
							 
							 if(!empty($surveyors_id))
							 {
								foreach($surveyors_id as $surveyor_id)
								{
									$surveyors_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyor_id->surveyors_id)->first(); 
		
									$helper->SendNotification($surveyors_token->device_id,'Cancel  Survey','This Survey has been cancelled');
									$notification = new Notification();
									$notification->user_id = $surveyors_token->id;
									$notification->title = 'Cancel  Survey Request';
									$notification->noti_type = 'Cancel  Survey ';
									$notification->user_type = $surveyors_token->type;
									$notification->notification = 'This Survey has been cancelled.';
									$notification->country_id = $surveyors_token->country_id;
									$notification->is_read = 0;
									$notification->save();


									   $data1 = array( 'email' =>$surveyors_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyors_token->email,'content' => 'This Survey has been cancelled'));
										Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										{
											$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Cancel  Survey' );
						
										});

										$opeartor_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyor_data->user_id)->first(); 
										$data1 = array( 'email' =>$opeartor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $opeartor_token->email,'content' => 'This Survey has been cancelled'));
										Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										{
											$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Cancel  Survey' );
						
										});
									
								}
							 }
							 
	 
							 $surveyor_data->save();
							 
							 return response()->json(['class'=>'success' ,  'message'=>"Survey cancel successfully"]);	

					 }
		
		}
		public function myfinance(Request $request)
		{
			$category =  $request->input('category');
			$status =  $request->input('status');
			$search =  $request->input('search');

			$user = Auth::user();
		
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
			$finance_data = Earning::select("payment.*",'port.port as port_name',
			'vessels.name as vesselsname','survey.survey_number')
			->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
			->leftJoin('port', 'port.id', '=', 'survey.port_id')
			->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
			->whereIn('payment.operator_id',$ids);


			if($category!="")
			{
				$finance_data=$finance_data->where('survey.survey_type_id',$category);
			}
			if($status!="")
			{
				$finance_data=$finance_data->where('payment.invoice_status',$status);
			}
			if($search!="")
			{
				//$finance_data=$finance_data->where('payment.invoice_status',$search);

				$finance_data=$finance_data->where(function ($query) use ($search) {
					$query->where('survey.survey_number', 'like','%'.$search.'%' )
							->orWhere('vessels.name', 'like','%'.$search.'%' )
							;
							
				});
			}
			
			$finance_data=$finance_data->orderBy('payment.created_at','desc');
			$finance_data=$finance_data->paginate(10);
			//dd($finance_data);
			return view('pages.myfinance',['finance_data'=>$finance_data,'category'=>$category,'status'=>$status,'search'=>$search]);	

		}
		public function myearning(Request $request)
		{

			$user = Auth::user();
			$account_data = Earning::select("payment.*",'port.port as port_name',
			'vessels.name as vesselsname','survey.survey_number')
			->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
			->leftJoin('port', 'port.id', '=', 'survey.port_id')
			->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
			->where('payment.surveyor_id',$user->id)
			->where('payment.request','0')
			->where('payment.paid_to_surveyor_status','unpaid')
			->where('payment.invoice_status','paid')
			->orderBy('payment.created_at','desc')
			->get();

		 	$account_amount = Earning::select("payment.*"
			)->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')
			->where('payment.surveyor_id',$user->id)
			->where('payment.request','0')
			->where('payment.paid_to_surveyor_status','unpaid')
			->where('payment.invoice_status','paid')
			->sum('payment.balance_for_this_surveyor');

			
			

			$past_data = Earning::select("payment.*",'port.port as port_name',
			'vessels.name as vesselsname','survey.survey_number')
			->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
			->leftJoin('port', 'port.id', '=', 'survey.port_id')
			->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
			->where('payment.surveyor_id',$user->id)
			->where('payment.request','1')
			->where('payment.paid_to_surveyor_status','paid')
			->orderBy('payment.created_at','desc')
			->get();
			
			$past_amount = Earning::select("payment.*"
			)->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')
			->where('payment.surveyor_id',$user->id)
			->where('payment.request','1')
			->where('payment.paid_to_surveyor_status','paid')->sum('payment.transfer_to_surveyor');
			

			$pending_data = Earning::select("payment.*",'port.port as port_name',
			'vessels.name as vesselsname','survey.survey_number')
			->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
			->leftJoin('port', 'port.id', '=', 'survey.port_id')
			->leftJoin('vessels', 'vessels.id', '=', 'survey.ship_id')
			->where('payment.surveyor_id',$user->id)
			->where('payment.request','0')
			->where('payment.invoice_status','unpaid')
			->orderBy('payment.created_at','desc')
			->get();
			$pending_amount = Earning::select("payment.*"
			)->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')
			->where('payment.surveyor_id',$user->id)
			->where('payment.request','0')
			->where('payment.invoice_status','unpaid')->sum('payment.invoice_amount');

			$user_country_detail = Countries::select('*')->where('id',$user->country_id)->first();
			$user = Auth::user();

			$user_country_detail = Countries::select('*')->where('id',$user->country_id)->first();
			
			// echo $payment_type;exit;
			$userBankdetail=  Bankdetail::where('user_id', $user->id)->first();
			$bank=array(
				'acc_holder_name'=>!empty($userBankdetail->acc_holder_name) ? $userBankdetail->acc_holder_name : '',
				'routing_number'=>!empty($userBankdetail->routing_number) ? $userBankdetail->routing_number : '',
				'acc_number'=>!empty($userBankdetail->acc_holder_name) ? $userBankdetail->acc_number : '',
				'company_name'=>!empty($userBankdetail->company_name) ? $userBankdetail->company_name : '',
				'beneficiary_name'=>!empty($userBankdetail->beneficiary_name) ? $userBankdetail->beneficiary_name : '',
				'beneficiary_address'=>!empty($userBankdetail->beneficiary_address) ? $userBankdetail->beneficiary_address : '',
				'bank_name'=>!empty($userBankdetail->bank_name) ? $userBankdetail->bank_name : '',
				'swift_code'=>!empty($userBankdetail->swift_code) ? $userBankdetail->swift_code : '',
				'ach_acc_number'=>!empty($userBankdetail->ach_acc_number) ? $userBankdetail->ach_acc_number : '',
				'more_info'=>!empty($userBankdetail->more_info) ? $userBankdetail->more_info : '',
				'file_type'=>!empty($userBankdetail->file_type) ? $userBankdetail->file_type : '',
				'file'=>!empty($userBankdetail->file) ? $userBankdetail->file : '',
				'paypal_email_address'=>!empty($userBankdetail->paypal_email_address) ? $userBankdetail->paypal_email_address : '',
				'current_payment'=>!empty($userBankdetail->current_payment) ? $userBankdetail->current_payment : '',
				'country'=>!empty($userBankdetail->country) ? $userBankdetail->country : '',

				'city'=>!empty($userBankdetail->city) ? $userBankdetail->city : '',
	
				'state'=>!empty($userBankdetail->state) ? $userBankdetail->state : '',
	
				'street_address'=>!empty($userBankdetail->street_address) ? $userBankdetail->street_address : '',
	
				'pincode'=>!empty($userBankdetail->pincode) ? $userBankdetail->pincode : '',
	
			);
	

			//dd($account_data);
			return view('pages.myearning',['account_data'=>$account_data,'past_data'=>$past_data,
			'pending_data'=>$pending_data,'account_amount'=>$account_amount,'past_amount'=>$past_amount,
			'pending_amount'=>$pending_amount,'user_country_detail'=>$user_country_detail,'user_country_detail'=>$user_country_detail,'bank'=>$bank]);	

		}
		public function paymentrequest(Request $request)
		{

			$validator = Validator::make($request->all(), ['amount' => 'required','payment_method'=>'required']);
				if ($validator->fails()) 
				{return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
				}else
				{
					$user = Auth::user();
					$amount = $request->input('amount');
					$payment_method = $request->input('payment_method');

					$unpaid_earning_data = Earning::select("payment.*")
					->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
					->where('payment.surveyor_id',$user->id)
					->where('payment.paid_to_surveyor_status','unpaid')
					->where('payment.request','0')
					->get();

					

					$surveyor_balance = Earning::select("payment.*")
					->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
					->where('payment.surveyor_id',$user->id)
					->where('payment.paid_to_surveyor_status','unpaid')
					->sum('payment.transfer_to_surveyor');

					$total_commission = Earning::select("payment.*")
					->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
					->where('payment.surveyor_id',$user->id)
					->where('payment.paid_to_surveyor_status','unpaid')
					->where('payment.request','0')
					->sum('payment.commission_amount');
					
					if($payment_method=='ach')
					{
						$imars_transfer_cost=0.15;
					}
					$early_withdrawal_fees=0;
					$after_wire_surveyor_balance="";
					if($payment_method=='wire')
					{
						if($surveyor_balance>=2000){
							$imars_transfer_cost=40;
						}
						if($surveyor_balance<2000){
							$imars_transfer_cost=$surveyor_balance*0.02;
							$early_withdrawal_fees=40-$imars_transfer_cost;
							$after_wire_surveyor_balance=$surveyor_balance-$early_withdrawal_fees;

						}
						
					}
					if($payment_method=='paypal')
					{
						$imars_transfer_cost=$surveyor_balance*0.02;
					}
					$value="";
					$survey_ids="";
					$no_of_survey=count($unpaid_earning_data);
					//dd($unpaid_earning_data);
					foreach($unpaid_earning_data as $data)
					{
						//echo $data->survey_id;exit;
						$survey_ids.=$value.$data->survey_id;
						$value=",";

						$datatranfercost = Earning::select("payment.*")->where('payment.surveyor_id',$user->id)
						->where('payment.survey_id',$data->survey_id)
						->first();
						
						$datatranfercost->imars_transfer_cost=$imars_transfer_cost/$no_of_survey;
						$datatranfercost->request='1';
						$datatranfercost->save();

					}

					if($after_wire_surveyor_balance!="")
					{
						$actual_transferto_surveyor=$after_wire_surveyor_balance;
					}else{
						$actual_transferto_surveyor=$surveyor_balance;
					}
					$country_id=$user->country_id;

					$Paymentrequest = new Paymentrequest;
					$Paymentrequest->surveyor_id=$user->id;
					$Paymentrequest->country_id=$country_id;
					$Paymentrequest->invoice_total=$amount;
					$Paymentrequest->survey_ids=$survey_ids;
					$Paymentrequest->surveyor_balance=$surveyor_balance;
					$Paymentrequest->payment_method=$payment_method;
					$Paymentrequest->actual_transferto_surveyor=$actual_transferto_surveyor;
					$Paymentrequest->early_withdrawal_fees=$early_withdrawal_fees;

					$Paymentrequest->imars_transfer_cost=$imars_transfer_cost;
					
					$Paymentrequest->total_commission=$total_commission;
					$Paymentrequest->save();
					
					

					return response()->json(['class'=>'success' ,  'message'=>"Payment request successfully submit"]);	

				}

		}

		
		
		public function chat()
	{
		
			return view('pages.chat,[]');
		
	}
		

	public function AssignTo(Request $request)
	{
	
					 $user = Auth::user();

					 $survey_id =  $request->input('survey_id');
					 $assign_to =  $request->input('assign_to');
					 $surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_id)->first();
					
					 if(!empty($surveyor_data) )
					 {
	 
						 	
						 if( $assign_to!=$user->id){
							$surveyor_data->assign_to= $assign_to;
							$surveyor_data->save();
						 }	
						 else{
							$surveyor_data->assign_to= '0';
							$surveyor_data->save();
						 }		  
						 $helper=new Helpers;
	 
									$surveyors_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$assign_to)->first(); 
		
									$helper->SendNotification($surveyors_token->device_id,'Assign Survey','You have been assigned a survey'.$surveyor_data->survey_number.'.Login and see the details');
									$notification = new Notification();
									
									$notification->user_id = $surveyors_token->id;
									$notification->title = 'Assign Survey';
									$notification->noti_type = 'Assign Survey';
									$notification->user_type = $surveyors_token->type;
									$notification->notification = 'You have been assigned a survey'.$surveyor_data->survey_number.'.Login and see the details';
									$notification->country_id = $surveyors_token->country_id;
									$notification->is_read = 0;
									$notification->save();


									   $data1 = array( 'email' =>$surveyors_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyors_token->email,'content' => 'You have been assigned a survey'.$surveyor_data->survey_number.'.Login and see the detail'));
										Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										{
											$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Assign Survey' );
						
										});

							
							 
							 return response()->json(['class'=>'success' ,  'message'=>"You have successfully assigned this survey to another surveyor"]);	

					 }
		
		}
	public function AssignToop(Request $request)
	{
	
					 $user = Auth::user();
					 $survey_id =  $request->input('survey_id');
					 $assign_to_op =  $request->input('assign_to_op');
					 $surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_id)->first();
					
					 if(!empty($surveyor_data) )
					 {
	 
						 
						 if( $assign_to_op!=$user->id){
							$surveyor_data->assign_to_op= $assign_to_op;
							$surveyor_data->save();
						 }else{
							$surveyor_data->assign_to_op= '0';
							$surveyor_data->save();
						 }						  
						 $helper=new Helpers;
						 $opeartor_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$assign_to_op)->first(); 
						$helper->SendNotification($opeartor_token->device_id,'Assign Survey','You have been assigned a survey'.$opeartor_token->survey_number.'.Login and see the details');
									
						$notification = new Notification();
						$notification->user_id = $opeartor_token->id;
						$notification->title = 'Assign Survey';
						$notification->noti_type = 'Assign Survey';
						$notification->user_type = $opeartor_token->type;
						$notification->notification = 'You have been assigned a survey'.$surveyor_data->survey_number.'.Login and see the details';
						$notification->country_id = $opeartor_token->country_id;
						$notification->is_read = 0;
						$notification->save();


						// $data1 = array( 'email' =>$surveyors_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyors_token->email,'content' => 'New survey appoint. Please Complete survey and report submit.'));
						// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
						// {
						// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Appoint Survey' );
		
						// });

						$data1 = array( 'email' =>$opeartor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $opeartor_token->email,'content' => 'You have been assigned a survey'.$surveyor_data->survey_number.'.Login and see the details'));
						Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
						{
							$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Assign  Survey' );
		
						});
						 return response()->json(['class'=>'success' ,  'message'=>"You have successfully assigned this survey to another operator"]);	

					 }
		
		}
		public function ChangeStartDate(Request $request)
		{
		
						 $user = Auth::user();
	
						 $survey_id =  $request->input('survey_id');
						$start_date= $request->input('start_date');
						
						 $surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_id)->first();
						
						 if(!empty($surveyor_data) )
						 {
							$date2=$surveyor_data->end_date;
							$date1=$surveyor_data->start_date;
							$diff = strtotime($date2) - strtotime($date1); 
          					$day= abs(round($diff / 86400)); 
							
							$surveyor_data->start_date =  date('Y-m-d',strtotime($start_date));
										  
							 $NewDate= date('Y-m-d',strtotime($start_date.' +'.$day.'day'));
							 $surveyor_data->end_date = $NewDate;
								 
								
										// $surveyors_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$assign_to_op)->first(); 
			
										// $helper->SendNotification($surveyors_token->device_id,'Cancel  Survey','This Survey has been cancelled');
										// $notification = new Notification();
										// $notification = new Notification();
										// $notification->user_id = $surveyors_token->id;
										// $notification->title = 'Appoint Survey';
										// $notification->noti_type = 'Appoint Survey';
										// $notification->user_type = $surveyors_token->type;
										// $notification->notification = 'New survey appoint. Please Accept Survey Request';
										// $notification->country_id = $surveyors_token->country_id;
										// $notification->is_read = 0;
										// $notification->save();
	
	
										//    $data1 = array( 'email' =>$surveyors_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyors_token->email,'content' => 'New survey appoint. Please Complete survey and report submit.'));
										// 	Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										// 	{
										// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Appoint Survey' );
							
										// 	});
	
										// 	$opeartor_token =  User::select('users.id','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyor_data->user_id)->first(); 
										// 	$data1 = array( 'email' =>$opeartor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $opeartor_token->email,'content' => 'This Survey has been cancelled'));
										// 	Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										// 	{
										// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Cancel  Survey' );
							
										// 	});
										
								
								 
		 
								 $surveyor_data->save();
								 
								 return response()->json(['class'=>'success' ,  'message'=>"You have successfully changes start date"]);	
	
						 }
			
			}

		

		public function addchat(Request $request)
		{
					$survey_id =  $request->input('survey_id');
					$sender_id =  $request->input('sender_id');
					$receiver_id=  $request->input('receiver_id');
					$msg =  $request->input('msg');
					$chat=new Chat;
					$chat->survey_id=$survey_id;
					$chat->sender_id=$sender_id;
					$chat->receiver_id=$receiver_id;
					$chat->msg=$msg;
					$chat->save();
                   $chatcount=Chat::where('survey_id',$survey_id)->where('sender_id',$sender_id)->where('receiver_id',$receiver_id)->where('is_read','0')->count();

					$survey=Survey::where('id',$survey_id)->first();
					$chatc="";
					if($chatcount!="" && $chatcount!="0" )
					{ 
						$survey->active_thread=(int)$chatc;
						$survey->save();
						
					}
					else{
						$survey->active_thread=NULL;
						$survey->save();
						
					}

					

		}

		public function updatechat(Request $request)
		{
					$survey_id =  $request->input('survey_id');
					$sender_id =  $request->input('sender_id');
					$receiver_id=  $request->input('receiver_id');
					
					$chats=Chat::select('chat.id')->where('survey_id',$survey_id)->where('receiver_id',$sender_id)->where('sender_id',$receiver_id)->where('is_read','0')->get();
					foreach($chats as $chat)
					{
						$chatss=Chat::where('id',$chat->id)->first();
						$chatss->is_read='1';
						$chatss->save();
						
					}
					 $chatcount=Chat::where('survey_id',$survey_id)->where('sender_id',$sender_id)->where('receiver_id',$receiver_id)->where('is_read','0')->count();

					$survey=Survey::where('id',$survey_id)->first();
					$chatc="";

					if($chatcount!="" && $chatcount!="0" )
					{ 
						$survey->active_thread=(int)$chatc;
						$survey->save();
						
					}else{
						$survey->active_thread=NULL;
						$survey->save();
						
					}
					
					
					
					return response()->json(['survey_id'=>$survey_id]);	

					

		}
		
		public function getchat(Request $request)
		{
					$survey_id =  $request->input('survey_id');
					$sender_id =  $request->input('sender_id');
					$receiver_id=  $request->input('receiver_id');
					
					$chats=Chat::select('chat.id')->where('survey_id',$survey_id)->where('receiver_id',$sender_id)->where('is_read','0')->count();
					
					return $chats;
				
					

		}
		
}

?>