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
			$user->status = '1';
			$user->save();
			$create_url = \App::make('url')->to('/verify-email')."/".base64_encode($user->id);
			
			// $data1 = array( 'email' =>$email, 'from' => 'imars@marineinfotech.com', 'from_name' => 'iMarS',"data1"=>array('email' => $email,'create_url' => $create_url));
			// 	Mail::send( 'pages.email.emailconfirm',$data1, function( $message ) use ($data1)
			// 	{
			// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

			// 	});
				$emailData = Emailtemplates::where('slug','=','signup-operator-of-operator-company')->first();
				if($emailData){
					$textMessage = strip_tags($emailData->description);
					$user->subject = $emailData->subject;
					if($user->email!='')
					{
						$textMessage = str_replace(array("USER_NAME",'EMAIL_VERIFY_LINK'), array($user->first_name,$create_url),$textMessage);
						
						Mail::raw($textMessage, function ($messages) use ($user) {
							$to = $user->email;
							$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($user->subject);
						});
					}
				}
				
			return response()->json(['class'=>'success' ,'message'=>'Thank you for signing up and welcome to iMarS community! Please check your emails for further instructions to complete your account set up.']);
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
			
			// $data1 = array( 'email' =>$email, 'from' => 'imars@marineinfotech.com', 'from_name' => 'iMarS',"data1"=>array('email' => $email,'create_url' => $create_url));
			// 	Mail::send( 'pages.email.emailconfirm',$data1, function( $message ) use ($data1)
			// 	{
			// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

			// 	});

				$emailData = Emailtemplates::where('slug','=','user-registration')->first();
				if($emailData){
					$textMessage = strip_tags($emailData->description);
					$user->subject = $emailData->subject;
					if($user->email!='')
					{
						$textMessage = str_replace(array("USER_NAME",'EMAIL_VERIFY_LINK'), array($user->first_name,$create_url),$textMessage);
						
						Mail::raw($textMessage, function ($messages) use ($user) {
							$to = $user->email;
							$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($user->subject);
						});
					}
				}
			return response()->json(['class'=>'success' ,'message'=>'Thank you for signing up and welcome to iMarS community! Please check your emails for further instructions to complete your account set up.']);
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
				// $user->email_verify = '1';
		
			
				if($country_id!=""){
					$user->country_id = $country_id;
					$countrydata = Countries::where('id',$country_id)->first();
					$user->country_code = $countrydata->phonecode;
				}
				
				
		
			$user->password = Hash::make($password);
			$user->save();
			$create_url = \App::make('url')->to('/verify-email')."/".base64_encode($user->id);
			
			// $data1 = array( 'email' =>$email, 'from' => 'imars@marineinfotech.com', 'from_name' => 'iMarS',"data1"=>array('email' => $email,'create_url' => $create_url));
			// 	Mail::send( 'pages.email.emailconfirm',$data1, function( $message ) use ($data1)
			// 	{
			// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

			// 	});
			$emailData = Emailtemplates::where('slug','=','user-registration')->first();
				if($emailData){
					$textMessage = strip_tags($emailData->description);
					$user->subject = $emailData->subject;
					if($user->email!='')
					{
						$textMessage = str_replace(array("USER_NAME",'EMAIL_VERIFY_LINK'), array($user->first_name,$create_url),$textMessage);
						
						Mail::raw($textMessage, function ($messages) use ($user) {
							$to = $user->email;
							$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($user->subject);
						});
					}
				}
			return response()->json(['class'=>'success' ,'message'=>'Thank you for signing up and welcome to iMarS community! Please check your emails for further instructions to complete your account set up.']);
	
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
			$user->status = '1';
			
			$user->save();

			$emailData = Emailtemplates::where('slug','=','signup-surveyor-of-surveyor-company')->first();
				if($emailData){
					$textMessage = strip_tags($emailData->description);
					$user->subject = $emailData->subject;
					if($user->email!='')
					{
						$textMessage = str_replace(array("USER_NAME"), array($user->first_name),$textMessage);
						
						Mail::raw($textMessage, function ($messages) use ($user) {
							$to = $user->email;
							$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($user->subject);
						});
					}
				}
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
			
			// $data1 = array( 'email' =>$email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));
			// 	Mail::send( 'pages.email.emailconfirm',$data1, function( $message ) use ($data1)
			// 	{
			// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

			// 	});

				$emailData = Emailtemplates::where('slug','=','user-registration')->first();
				if($emailData){
					$textMessage = strip_tags($emailData->description);
					$user->subject = $emailData->subject;
					if($user->email!='')
					{
						$textMessage = str_replace(array("USER_NAME",'EMAIL_VERIFY_LINK'), array($user->first_name,$create_url),$textMessage);
						
						Mail::raw($textMessage, function ($messages) use ($user) {
							$to = $user->email;
							$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($user->subject);
						});
					}
				}
			return response()->json(['class'=>'success' ,'message'=>'Thank you for signing up and welcome to iMarS community! Please check your emails for further instructions to complete your account set up.']);
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
						return response()->json(['class'=>'success' ,'message'=>'Log in successful…']);

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
							$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($userArray->subject);
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

		if($user->conduct_custom=='1')
		{
			$first = DB::table('custom_survey_users')
			->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))
			->where('surveyors_id',$user->id);
			$responce = DB::table('survey_users')
			->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))
			->where('surveyors_id',$user->id)->union($first)->first();

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
					->where('surveyors_id',$user->id)->first();
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
		$survey_count=Survey::where('surveyors_id',$user->id)->count();
		return view('pages.myprofile',['userdata'=>$userdetail,'survey_count'=>$survey_count,
		'percentage_job_acceptance'=>$percentage_job_acceptance,'average_response_time'=>$average_response_time]);	
	}

	
	public function editprofilepost(Request $request)
{
	$user= Auth::user();
	$validator = Validator::make($request->all(), [
		
		'email' => 'required|email|unique:users,email,'.$user->id,
		'mobile' => 'required',
		'street_address' => 'required',
		'city' => 'required'
		
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
			$imageName =str_replace(" ", "", $imageName);
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
		return response()->json(['class'=>"success" ,'message'=>"You have successfully updated your profile…"]);

	}
}

	public function myoperator(Request $request)
	{
		$user = Auth::user();
		$operators_data =  User::select('*')->where('created_by',$user->id)->where('status','!=','0')->orderBy('first_name','asc')
		->orderBy('last_name','asc')->get();

		return view('pages.myoperator',['operators_data'=>$operators_data,'type'=>$user->type]);	
	}
	public function myoperatorpost(Request $request)
	{
		$id =  $request->input('id');
		$user = Auth::user();
		$email =  $request->input('email');
		$usertypecheck =  User::select('users.type',DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'))->where('id',$user->id)->first();
		$idpop="";
		if($usertypecheck->type=='2'){
			$var="a Surveyor";
		}else{
			$var="an Operator";
		}
		if($id!=""){
			$userArray =   User::where('id',base64_decode($id))->first();
			$idpop=base64_decode($id);
			$message="You have successfully edited  $var …";
			$validator = Validator::make($request->all(), [
				'email' => 'required|email|unique:users,email,'.$idpop,
			]);
		}else{
		
			$userArray = new  User;
			$message="You have successfully added $var …";
			$validator = Validator::make($request->all(), [
				'email' => 'required|email|unique:users,email',

			]);
			
		}

		
		if ($validator->fails()) 
		{
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors(),'id'=>$idpop]);
		}else
		{		
			

			if($id!="")
			{
				$userArray =   User::where('id',base64_decode($id))->first();
				$userArray->created_by=$user->id;
				$userArray->email=$email;
				$userArray->company=!empty($user->company)?$user->company:"";
				if($usertypecheck->type=='2')
				{$userArray->type='3';}else{$userArray->type='1';}
				$userArray->email_verify='0';
				$userArray->save();
	
			}else
			{
				
				$userArray = new  User;

				$userArray->created_by=$user->id;
				$userArray->company=!empty($user->company)?$user->company:"";
				$userArray->email=$email;
				if($usertypecheck->type=='2'){$userArray->type='3';}else{$userArray->type='1';}
				$userArray->save();
	
			}
			if($usertypecheck->type=='2')
			{$userArray->type='3';}else{$userArray->type='1';}

		if($usertypecheck->type=='2')
			{
				$create_url = \App::make('url')->to('/bycompany-surveyor-signup')."/".base64_encode($userArray->id);
				$emailData = Emailtemplates::where('slug','=','surveyor-add-by-dp')->first();

			}else{
				$create_url = \App::make('url')->to('/individual-operator-signup')."/".base64_encode($userArray->id);
				$emailData = Emailtemplates::where('slug','=','operator-add-by-dp')->first();

			}
		// $data1 = array( 'email' =>$email, 'from' => 'info@imars.com', '"from_name"' => 'IMARS',"data1"=>array('email' => $email,'create_url' => $create_url));

		// 	Mail::send( 'pages.email.add_operator_email_verify_for_signup',$data1, function( $message ) use ($data1)
		// 	{
		// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

		// 	});


						if($emailData){
							$textMessage = strip_tags($emailData->description);
							$subject = $usertypecheck->username." has added you to iMarS!";
							$to =$email;

							if($email!='')
							{ $dpname=$usertypecheck->first_name.' '.$usertypecheck->last_name;
								$textMessage = str_replace(array('COMPANY_NAME','DP_NAME','SIGNUP_LINK'), 
								array($userArray->company,$usertypecheck->username,$create_url),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}
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
							return response()->json(['class'=>'success' ,  'message'=>"You have removed a $var successfully…"]);
						
						
						
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
						return response()->json(['class'=>'success' ,  'message'=>"You have successfully removed a survey type from your services portfolio…"]);
						
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
			$message="Vessel - edited successfully…";
		}else{
			$vessels = new  Vessels;
			$message="New vessel - added successfully…";
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
						else{
							$vessels->additional_email="";

						}
						
						if($same_as_company=='1')
						{
							$vessels->same_as_company=$same_as_company;
							$user_info= User::find($user->id);
							$vessels->company=$user_info->company;

						}else{
							$vessels->company=$company;
							$vessels->same_as_company=0;
						}
						if($same_as_company_address=='1')
						 {$vessels->same_as_company_address=$same_as_company_address;
						 }else{
							$vessels->same_as_company_address=0;
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
						return response()->json(['class'=>'success' ,  'message'=>"Vessel - removed successfully"]);
						
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
			$message="Agent - edited successfully…";
		}else{
			$userport = new  Agents;
			$message="New agent - added successfully…";
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
						return response()->json(['class'=>'success' ,  'message'=>"Agent - removed successfully"]);
						
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
					$message="You have successfully edited a port… ";
				}else{
					
					$message="You now will be listed operators’ search results in this port… ";
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
			$message="You have successfully edited a survey type…";

		}else
		{

			
			$userport =   UsersSurveyPrice::where('user_id',$user->id)->where('survey_type_id',$survey_type_id)->first();
			if(!empty($userport))
			{
				$userport =   UsersSurveyPrice::where('user_id',$user->id)->where('survey_type_id',$survey_type_id)->first();
			}else{
				$userport = new  UsersSurveyPrice;
			}


			$message="You have added a new survey type to your services portfolio… ";
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

								// $chat_unread_count=Chat::select('chat.id')->where('is_read','0')->where('receiver_id',$user->id)->count();
								// if($chat_unread_count>0){
								// 	$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
								// }
								

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

					// $chat_unread_count=Chat::select('chat.id')->where('is_read','0')->where('receiver_id',$user->id)->count();
					// 			if($chat_unread_count>0){
					// 				$upcoming_survey_data=$upcoming_survey_data->orderBy('survey.active_thread','desc');
					// 			}
								

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
	public function mysurveyLatest(Request $request)
	{
		$user = Auth::user();
		$user_id=$user->id;
		
		$surveyor_id =  !empty($request->input('surveyor_id'))?$request->input('surveyor_id') : "";
		$search = $request->input('search');

		$operator_id =  !empty($request->input('operator_id'))?$request->input('operator_id') : "";
			
			$user_d =  User::select('type')->where('id',$user->id)->first(); 
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
								
							if($operator_id !="")
							{
								
								$pending_survey_data=$pending_survey_data->Where('survey.assign_to_op',$operator_id);	
								

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
									$pending_survey_data=$pending_survey_data->where(function ($query) use ($ids) {
										$query->WhereIn('survey.user_id',$ids)
										->orWhereIn('survey.assign_to_op',$ids);});	
													
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
										
										$pending_survey_data=$pending_survey_data->where(function ($query) use ($ids) {
											$query->WhereIn('survey.user_id',$ids)
											->orWhereIn('survey.assign_to_op',$ids);});	
								}

									
							}
							$pending_survey_data=$pending_survey_data->where('survey.status', '=','0')->where('survey.declined', '=','0');
									
								if($search !="")
								{
									$pending_survey_data=$pending_survey_data->where(function ($query) use ($search) {
										$query->where('vessels.name', 'like','%'.$search.'%' )
												->orWhere('port.port', 'like','%'.$search.'%' )
												->orWhere('survey.survey_number', 'like','%'.$search.'%' );
												
									});
									
								}
							
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
								
							if($operator_id !="")
							{
					
								
									$upcoming_survey_data=$upcoming_survey_data->Where('survey.assign_to_op',$operator_id);	
								
					
							}else{
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
							$upcoming_survey_data=$upcoming_survey_data->where('survey.status', '=','1')->where('survey.declined', '=','0');	
								if($search !="")
								{
									$upcoming_survey_data=$upcoming_survey_data->where(function ($query) use ($search) {
										$query->where('vessels.name', 'like','%'.$search.'%' )
												->orWhere('port.port', 'like','%'.$search.'%' )
												->orWhere('survey.survey_number', 'like','%'.$search.'%' );
												
									});
									
								}
							
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
								
							if($operator_id !="")
							{
								$report_submit_survey_data=$report_submit_survey_data->Where('survey.assign_to_op',$operator_id);	
									
							}else{
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
									
									$createdbysurveyor =User::select('created_by')->where('id',$user_id)->first();
									$ids=array();
									$createdbydpsurveyor = User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
									if(!empty($createdbydpsurveyor)){
										
										foreach($createdbydpsurveyor as $data){
											$ids[]=$data->id;
										}
									}
										array_push($ids,$createdbysurveyor->created_by);
										$report_submit_survey_data=$report_submit_survey_data->where(function ($query) use ($ids) {
											$query->WhereIn('survey.user_id',$ids)
											->orWhereIn('survey.assign_to_op',$ids);});	
								}						

							

							}
							$report_submit_survey_data=$report_submit_survey_data->where('survey.status', '=','3')->where('survey.declined', '=','0');	
								if($search !="")
								{
									$report_submit_survey_data=$report_submit_survey_data->where(function ($query) use ($search) {
										$query->where('vessels.name', 'like','%'.$search.'%' )
												->orWhere('port.port', 'like','%'.$search.'%' )
												->orWhere('survey.survey_number', 'like','%'.$search.'%' );
												
									});
									
								}
							
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
									
								if($operator_id !="")
								{
									
										$unpaid_survey_data=$unpaid_survey_data->Where('survey.assign_to_op',$operator_id);	
									
	
								}else{
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
										
										$createdbysurveyor =User::select('created_by')->where('id',$user_id)->first();
									$ids=array();
									$createdbydpsurveyor = User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
									if(!empty($createdbydpsurveyor)){
										
										foreach($createdbydpsurveyor as $data){
											$ids[]=$data->id;
										}
									}
										array_push($ids,$createdbysurveyor->created_by);
											$unpaid_survey_data=$unpaid_survey_data->where(function ($query) use ($ids) {
												$query->WhereIn('survey.user_id',$ids)
												->orWhereIn('survey.assign_to_op',$ids);});	
									}		
								
	
								}
								$unpaid_survey_data=$unpaid_survey_data->where('survey.status', '=','4')->where('survey.declined', '=','0');	
									if($search !="")
									{
										$unpaid_survey_data=$unpaid_survey_data->where(function ($query) use ($search) {
											$query->where('vessels.name', 'like','%'.$search.'%' )
													->orWhere('port.port', 'like','%'.$search.'%' )
													->orWhere('survey.survey_number', 'like','%'.$search.'%' );
													
										});
									}
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
									
								if($operator_id !="")
								{
									
										$paid_survey_data=$paid_survey_data->Where('survey.assign_to_op',$operator_id);	
									
	
								}else{
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
										
										$createdbysurveyor =User::select('created_by')->where('id',$user_id)->first();
									$ids=array();
									$createdbydpsurveyor = User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
									if(!empty($createdbydpsurveyor)){
										
										foreach($createdbydpsurveyor as $data){
											$ids[]=$data->id;
										}
									}
										array_push($ids,$createdbysurveyor->created_by);
											$paid_survey_data=$paid_survey_data->where(function ($query) use ($ids) {
												$query->WhereIn('survey.user_id',$ids)
												->orWhereIn('survey.assign_to_op',$ids);});	
									}
								}

								$paid_survey_data=$paid_survey_data->where(function ($query) use ($search) {
									$query->where('survey.status', '=','5' )
											->orWhere('survey.status', '=','6' );
											
								});

								$paid_survey_data=$paid_survey_data->where('survey.declined', '=','0');	
									if($search !="")
									{
										$paid_survey_data=$paid_survey_data->where(function ($query) use ($search) {
											$query->where('vessels.name', 'like','%'.$search.'%' )
													->orWhere('port.port', 'like','%'.$search.'%' )
													->orWhere('survey.survey_number', 'like','%'.$search.'%' );
													
										});
										
									}
								
									$paid_survey_data=$paid_survey_data->groupBy('survey.id');
									$paid_survey_data=$paid_survey_data->orderBy('survey.start_date','desc');
									$paid_survey_data=$paid_survey_data->get();

								$cancelled_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name','port.port as port_name',
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
									
								if($operator_id !="")
								{
									
										$cancelled_survey_data=$cancelled_survey_data->Where('survey.assign_to_op',$operator_id);	
								
	
								}else{
									if($user_d->type =="0")
									{
										$createdbysurveyor =  User::select('id')->where('created_by',$user_id)->get(); 
										$ids=array();
										foreach($createdbysurveyor as $data){
											$ids[]=$data->id;
										}
										array_push($ids,$user_id);
										$cancelled_survey_data=$cancelled_survey_data->where(function ($query) use ($ids) {
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
											$cancelled_survey_data=$cancelled_survey_data->where(function ($query) use ($ids) {
												$query->WhereIn('survey.user_id',$ids)
												->orWhereIn('survey.assign_to_op',$ids);});	
									}
								
	
								}
								$cancelled_survey_data=$cancelled_survey_data->where(function ($query) use ($search) {
									$query->where('survey.status','2' )
											->orWhere('survey.declined','1');
											
								});
									if($search !="")
									{
										$cancelled_survey_data=$cancelled_survey_data->where(function ($query) use ($search) {
											$query->where('vessels.name', 'like','%'.$search.'%' )
													->orWhere('port.port', 'like','%'.$search.'%' )
													->orWhere('survey.survey_number', 'like','%'.$search.'%' );
													
										});
										
									}
								
									$cancelled_survey_data=$cancelled_survey_data->groupBy('survey.id');
									$cancelled_survey_data=$cancelled_survey_data->orderBy('survey.start_date','desc');
									$cancelled_survey_data=$cancelled_survey_data->get();
						
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
						if($surveyor_id !="")
						{
									
							$u=	User::select('type')->where('id',$surveyor_id)->first();

							if($u->type =="3")
							{
								$pending_survey_data=$pending_survey_data->where('survey.assign_to',$surveyor_id);
							}else
							{
								$pending_survey_data=$pending_survey_data->where(function ($query) use ($surveyor_id) {
									$query->Where('custom_survey_users.surveyors_id',$surveyor_id)
										->orwhere('survey_users.surveyors_id',$surveyor_id );});
										$pending_survey_data=$pending_survey_data->where('survey.assign_to','0');
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
							$pending_survey_data=$pending_survey_data->where(function ($query) use ($ids) {
							$query->WhereIn('custom_survey_users.surveyors_id',$ids)
							->orwhereIn('survey_users.surveyors_id',$ids );});	
						}
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
						if($surveyor_id !="")
						{
									
							$u=	User::select('type')->where('id',$surveyor_id)->first();

							if($u->type =="3")
							{
								$report_submit_survey_data=$report_submit_survey_data->where('survey.assign_to',$surveyor_id);
							}else
							{
								$report_submit_survey_data=$report_submit_survey_data->where(function ($query) use ($surveyor_id) {
									$query->Where('custom_survey_users.surveyors_id',$surveyor_id)
										->orwhere('survey_users.surveyors_id',$surveyor_id );});
										$report_submit_survey_data=$report_submit_survey_data->where('survey.assign_to','0');
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
							$report_submit_survey_data=$report_submit_survey_data->where(function ($query) use ($ids) {
							$query->WhereIn('custom_survey_users.surveyors_id',$ids)
							->orwhereIn('survey_users.surveyors_id',$ids );});	
						}
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
							if($surveyor_id !="")
							{
										
								$u=	User::select('type')->where('id',$surveyor_id)->first();
	
								if($u->type =="3")
								{
									$pending_payment_survey_data=$pending_payment_survey_data->where('survey.assign_to',$surveyor_id);
								}else
								{
									$pending_payment_survey_data=$pending_payment_survey_data->where(function ($query) use ($surveyor_id) {
										$query->Where('custom_survey_users.surveyors_id',$surveyor_id)
											->orwhere('survey_users.surveyors_id',$surveyor_id );});
											$pending_payment_survey_data=$pending_payment_survey_data->where('survey.assign_to','0');
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
								$pending_payment_survey_data=$pending_payment_survey_data->where(function ($query) use ($ids) {
								$query->WhereIn('custom_survey_users.surveyors_id',$ids)
								->orwhereIn('survey_users.surveyors_id',$ids );});	
							}
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
							if($surveyor_id !="")
							{
										
								$u=	User::select('type')->where('id',$surveyor_id)->first();
	
								if($u->type =="3")
								{
									$received_payment_survey_data=$received_payment_survey_data->where('survey.assign_to',$surveyor_id);
								}else
								{
									$received_payment_survey_data=$received_payment_survey_data->where(function ($query) use ($surveyor_id) {
										$query->Where('custom_survey_users.surveyors_id',$surveyor_id)
											->orwhere('survey_users.surveyors_id',$surveyor_id );});
											$received_payment_survey_data=$received_payment_survey_data->where('survey.assign_to','0');
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
								$received_payment_survey_data=$received_payment_survey_data->where(function ($query) use ($ids) {
								$query->WhereIn('custom_survey_users.surveyors_id',$ids)
								->orwhereIn('survey_users.surveyors_id',$ids );});	
							}
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
								if($surveyor_id !="")
								{
											
									$u=	User::select('type')->where('id',$surveyor_id)->first();
		
									if($u->type =="3")
									{
										$paid_survey_data=$paid_survey_data->where('survey.assign_to',$surveyor_id);
									}else
									{
										$paid_survey_data=$paid_survey_data->where(function ($query) use ($surveyor_id) {
											$query->Where('custom_survey_users.surveyors_id',$surveyor_id)
												->orwhere('survey_users.surveyors_id',$surveyor_id );});
												$paid_survey_data=$paid_survey_data->where('survey.assign_to','0');
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
									$paid_survey_data=$paid_survey_data->where(function ($query) use ($ids) {
									$query->WhereIn('custom_survey_users.surveyors_id',$ids)
									->orwhereIn('survey_users.surveyors_id',$ids );});	
								}
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

							$cancelled_survey_data =  Survey::select('survey.*','survey_type.name as surveytype_name',
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
									$cancelled_survey_data=$cancelled_survey_data->where('survey.assign_to',$surveyor_id);
								}else
								{
									$cancelled_survey_data=$cancelled_survey_data->where(function ($query) use ($surveyor_id) {
										$query->Where('custom_survey_users.surveyors_id',$surveyor_id)
											->orwhere('survey_users.surveyors_id',$surveyor_id );});
											$cancelled_survey_data=$cancelled_survey_data->where('survey.assign_to','0');
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
								$cancelled_survey_data=$cancelled_survey_data->where(function ($query) use ($ids) {
								$query->WhereIn('custom_survey_users.surveyors_id',$ids)
								->orwhereIn('survey_users.surveyors_id',$ids );});	
							}
						}else{
							$cancelled_survey_data=$cancelled_survey_data->where(function ($query) use ($user_id) {
								$query->where('survey_users.surveyors_id', '=',$user_id )
								->orWhere('custom_survey_users.surveyors_id',$user_id)
								->orWhere('survey.assign_to',$user_id);});					
							}
						
							$cancelled_survey_data=$cancelled_survey_data->where('survey.status', '=','2')->where('survey.declined', '=','0');
							$cancelled_survey_data=$cancelled_survey_data->where(function ($query)  {
							$query->Where('survey_users.status','pending')
								->orwhere('survey_users.status','upcoming' )
								->orwhere('custom_survey_users.status','waiting' )
								->orwhere('custom_survey_users.status','upcoming' )
								->orwhere('custom_survey_users.status','approved' );});	
	
								$cancelled_survey_data=$cancelled_survey_data->groupBy('survey.id');
								$cancelled_survey_data=$cancelled_survey_data->orderBy('survey.start_date','desc');
								$cancelled_survey_data=$cancelled_survey_data->get();

					
			}

			if(!$pending_survey_data->isEmpty())
			{
				$pending_survey_data=$pending_survey_data;

			}
			if(!$upcoming_survey_data->isEmpty())
			{
				$upcoming_survey_data=$upcoming_survey_data;

			}
			if(!$report_submit_survey_data->isEmpty())
			{
				$report_submit_survey_data=$report_submit_survey_data;

			}
			if(isset($paid_survey_data) && !$paid_survey_data->isEmpty())
			{
				$paid_survey_data=$paid_survey_data;

			}
			else{
					$paid_survey_data=Survey::where('id',0)->paginate(0);
				}
			if(isset($unpaid_survey_data) && !$unpaid_survey_data->isEmpty())
			{
				$unpaid_survey_data=$unpaid_survey_data;
			}else{
				$unpaid_survey_data=Survey::where('id',0)->paginate(0);
			
			}

			if(isset($pending_payment_survey_data) && !$pending_payment_survey_data->isEmpty())
			{
				$pending_payment_survey_data=$pending_payment_survey_data;

			}else{
				$pending_payment_survey_data=Survey::where('id',0)->paginate(0);
			}

			if(isset($received_payment_survey_data) && !$received_payment_survey_data->isEmpty())
			{
				$received_payment_survey_data=$received_payment_survey_data;

			}else{
				$received_payment_survey_data=Survey::where('id',0)->paginate(0);
			}
			if(!$cancelled_survey_data->isEmpty())
			{
				$cancelled_survey_data=$cancelled_survey_data;

			}
		if($user->type=='0' || $user->type=='1')
		{
				return view('pages.mysurvey-operator',['pending_survey_data'=>$pending_survey_data,
					'upcoming_survey_data'=>$upcoming_survey_data,
					'report_submit_survey_data'=>$report_submit_survey_data,
					'paid_survey_data'=>$paid_survey_data,
					'unpaid_survey_data'=>$unpaid_survey_data,
					'cancelled_survey_data'=>$cancelled_survey_data,
					'operator_id'=>$operator_id,
					'search'=>$search,
					'surveyor_id'=>$surveyor_id,
					]);	
		}else{
				return view('pages.mysurvey-surveyor',['pending_survey_data'=>$pending_survey_data,
				'upcoming_survey_data'=>$upcoming_survey_data,
				'report_submit_survey_data'=>$report_submit_survey_data,
				'paid_survey_data'=>$paid_survey_data,
				'pending_payment_survey_data'=>$pending_payment_survey_data,
				'received_payment_survey_data'=>$received_payment_survey_data,
				'cancelled_survey_data'=>$cancelled_survey_data,
				'operator_id'=>$operator_id,
				'search'=>$search,
				'surveyor_id'=>$surveyor_id,
				]);	

		}
		

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
							
							
								
							
							$user = Auth::user();
							$pricing="0";
							$total_price="0";
							$port_price="0";
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



								$bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)->where('surveyors_id',$user->id)->where('status','upcoming')->count();
								if($bid_count>0){
								$bid_status='1';
								}else{
									$bid_status='0';
								}

								
								 $operator_bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)
								 ->where('status','=','upcoming')->count();

							//dd($surveyor_data);
							
						
				 
						return view('pages.mysurveydetail',['surveyor_data'=>$surveyor_data,
				 'surveyor_id_id'=>$surveyor_id_id,
				 'operator_id_id'=>$opusername->id,
				 'suusername'=>$surveyor_name,
				 'opusername'=>$opusername->opusername,
				 'su_company_name'=>$su_company_name,
				 'op_company_name'=>$op_company_name,
				 'operator_survey_count'=>$operator_survey_count,'country_data'=>$country_data,'total_price'=>$total_price,
				 'port_price'=>$port_price,'bid_status'=>$bid_status,'operator_bid_count'=>$operator_bid_count]);
	}	

	public function userdetail($id)
	{					
		
		$surveyor_data =  User::select('users.*')->where('users.id',$id)->first();
	//	echo $surveyor_data->type;
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




								$bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)->where('surveyors_id',$user->id)->where('status','upcoming')->count();
								if($bid_count>0){
								$bid_status='1';
								}else{
									$bid_status='0';
								}

								
							 	$operator_bid_count = DB::table('custom_survey_users')->where('survey_id',$surveyor_data->id)->where('status','!=','approved')->count();

							//dd($surveyor_data);
							if($surveyor_data->no_of_days!="0"){
								$total_price=$surveyor_data->survey_price*$surveyor_data->no_of_days;
							}else{
								$total_price=$surveyor_data->survey_price;
							}
							$port_price=$surveyor_data->port_price;
				 return view('pages.mysurveydetailcal',['surveyor_data'=>$surveyor_data,'surveyor_id_id'=>$surveyor_id_id,
				 'suusername'=>$surveyor_name,
				 'opusername'=>$opusername->opusername,'su_company_name'=>$su_company_name,'op_company_name'=>$op_company_name,
				 'operator_survey_count'=>$operator_survey_count,'country_data'=>$country_data,'total_price'=>$total_price,'cat_price'=>$total_price,'port_price'=>$port_price,'bid_status'=>$bid_status,'operator_bid_count'=>$operator_bid_count]);


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
				$helper=new Helpers;
				if(!empty($surveyor_data))
				{
					if($surveyor_data->assign_to_op!="" || $surveyor_data->assign_to_op!="0"){
						$operator_id=$surveyor_data->assign_to_op;
					}else{
						$operator_id=$surveyor_data->user_id;

					}

					$operator_token =  User::select('users.*')->where('id',$operator_id)->first(); 

					if($type=="accept")
					{   $survey_users->status="upcoming";
						$survey_users->accept_date=date("Y-m-d H:i:s");

						$surveyor_data->status='1';
						$surveyor_data->assign_to=$assign_to;
						$surveyor_data->accept_by=$surveyors_id;
						$surveyor_data->save(); 
						
						$helper->SendNotification($operator_token->device_id,'Your survey request accepted!','Your Survey request has been accepted by the surveyor. You can manage it now in your upcoming surveys tab.');

						$notification = new Notification();
						$notification->user_id = $operator_token->id;
						$notification->title = 'Your survey request accepted!';
						$notification->noti_type = 'Your survey request accepted!' ;
						$notification->user_type = $operator_token->type;
						$notification->notification = 'Your Survey request has been accepted by the surveyor. You can manage it now in your upcoming surveys tab.';
						$notification->country_id = $operator_token->country_id;
						$notification->is_read = 0;
						$notification->save();
						// $data1 = array( 'email' =>$operator_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $operator_token->email,'content' => 'Accept Your Survey Request','Your Survey request has been confirmed by iMarS. You can manage your request by logging into your account.'));
						// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
						// {
						// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Accept Your Survey Request' );
		
						// });
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
									$message="Check your “Upcoming” tab for newly accepted survey’s details. ";	
					}else
					{
								  
						
						$survey_users->status="declined";
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

						// $data1 = array( 'email' =>$operator_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $operator_token->email,'content' => 'Your primary Surveyor has declined the Survey request. Your Substitute 1 and-or Substitute 2 will be contacted in order to fulfill your Survey request within 24 hours.'));
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

						$survey_users_next = SurveyUsers::select('survey_users.*')->where('type', '=', $survey_users->type+1)->where('survey_users.survey_id',$survey_id)->first();
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

							$message_token =  User::select('users.id','users.email','users.first_name','users.type','users.device_id','users.country_id')->where('id',$survey_users_next->surveyors_id)->first(); 
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

								// $data1 = array( 'email' =>$message_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $message_token->email,'content' => 'New Survey Request Received. Please accept within 8 hours, or the request will be cancelled.'));
								// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
								// {
								// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Appoint Survey' );
				
								// });

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
						$message = 'You have declined a survey request. Make yourself unavailable in calendar for dates that you are not available. ';

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
	'new_password' => 'required|min:6|max:16|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
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
				return response()->json(['class'=>"success" ,'message'=>"You have successfully changed your password… "]);
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
							$imageName =str_replace(" ", "", $imageName);
							$file_data->move(public_path().'/media/users/dispute-request', $imageName);
							
							$Disputerequest->file = $imageName;
						}
				
				$Disputerequest->save();
				return response()->json(['class'=>"success" ,'message'=>"Issue submitted successfully… Customer service will contact you shortly…"]);
			
			
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
									$createdbydpsurveyor =  User::select('id')->where('created_by',$createdbysurveyor->created_by)->get();
									if(!empty($createdbydpsurveyor)){
										
										foreach($createdbydpsurveyor as $data){
											$ids[]=$data->id;
										}
									}
										array_push($ids,$createdbysurveyor->created_by);
						}
				//dd($ids);
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
								->leftJoin('users_port', 'users_port.user_id', '=', 'users.id')
								->where('users_port.port_id',$port_id)
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
										 $first = DB::table('custom_survey_users')->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))->where('surveyors_id',$surveyor->id);
										 $responce = DB::table('survey_users')->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))->where('surveyors_id',$surveyor->id)->union($first)->first();

										
												$average_response_time=(int)$responce->recponce_time*(-1);
												
												$average_response_time=gmdate("H:i", $average_response_time);

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
								
												 $responce=SurveyUsers::select(DB::raw('AVG(TIMESTAMPDIFF(SECOND,accept_date,created_at)) as recponce_time'))
												->where('surveyors_id',$surveyor->id)->first();
												
												  $average_response_time=(int)$responce->recponce_time*(-1);
												
												 $average_response_time=gmdate("H:i", $average_response_time);
											
												$average_response_time=explode(':',$average_response_time);
												if($average_response_time[0]=='00'){
													$average_response_time=$average_response_time[1].' min';
												}elseif($average_response_time[1]=='00'){
													$average_response_time=$average_response_time[0].' hours ';
				
												}else{
													$average_response_time=$average_response_time[0].' hours '.$average_response_time[1].' min';
												}
												
												
									}
									//echo $average_response_time;
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
								  $surveyor_price=UsersSurveyPrice::where('user_id',$surveyor->id)->where('survey_type_id',$survey_type_id)->first();;						
								  $pricing2=$helper->SurveyorPortPrice($port_id,$surveyor->id);
								  
								  $pricing="0";
									if(!empty($surveyor_price))
									{
										$pricing1=!empty($surveyor_price->survey_price)?$surveyor_price->survey_price:'0';
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
								'image'=>!empty($surveyor->profile_pic)  ?URL::to('/public/media/users').'/'.$surveyor->profile_pic :URL::to('/public/media').'/list-user.png'
							);
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
					$sort="rating";
			}
       
			//dd($data);
			return view('pages.surveyor-list',['surveyor_user_data'=>$data,'survey_type_id'=>$survey_type_id,'sort'=>$sort]);
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
						$helper=new Helpers;
						$survey= new Survey();
						$survey->user_id=$user->id;
						$survey->assign_to_op=$user->id;
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

								$message_token =  User::select('users.id','users.first_name','users.email','users.type','users.device_id','users.country_id')->where('users.id',$value)
								->first();
								//echo $message_token->id;
								
								$helper->SendNotification($message_token->device_id,'You have a new Custom Occasional Survey request!','New Custom Occasional Survey request! Please check survey details and submit a quote within 24 hours. The sooner the better chance to be selected for the survey.');
								
										  
								$notification = new Notification();
								$notification->user_id = $message_token->id;
								$notification->title = 'You have a new Custom Occasional Survey request!';
								$notification->noti_type = 'You have a new Custom Occasional Survey request!';
								$notification->user_type = $message_token->type;
								$notification->notification = 'New Custom Occasional Survey request! Please check survey details and submit a quote within 24 hours. The sooner the better chance to be selected for the survey.';
								$notification->country_id = $message_token->country_id;
								$notification->is_read = 0;
								$notification->save();

								// $data1 = array( 'email' =>$message_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $message_token->email,
								// 'content' => 'New Survey Request Received. Please accept within 8 hours, or the request will be cancelled.'));
								// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
								// {
								// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Appoint Survey' );
				
								// });

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
								$message_token =  SurveyUsers::select('users.id','users.first_name','users.email','users.type','users.device_id','users.country_id')
								->leftJoin('users', 'survey_users.surveyors_id', '=', 'users.id')
								->where('survey_users.survey_id',$survey->id)
								->where('survey_users.type','1')
								->first();
								//echo $message_token->id;
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

										// $data1 = array( 'email' =>$message_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $message_token->email,'content' => 'New Survey Request Received. Please accept within 8 hours, or the request will be cancelled.'));
										// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
										// {
										// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Appoint Survey' );
						
										// });

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
					
						
						$status=1;
						$message = 'Your request has been sent and is now listed in “Pending” tab…';
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
		if($payment_method=="paypal"){
				$current_payment="paypal";
		}
		if($payment_method=="wire"){
			$current_payment="wire";
	}
	if($payment_method=="ach"){
		$current_payment="ach";
}

		if($payment_method=='paypal'){
			$validator = Validator::make($request->all(), [
				'payment_method'=>'required',
				
				'paypal_email_address'=>'required|email'
				
					]);
		}else{
		$validator = Validator::make($request->all(), [
		'payment_method'=>'required',
		
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
				$pincode =  $request->input('zip');
				$country =  $request->input('country');
				
				


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
				$message = 'You have successfully updated your payment details… ';
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
							$imageName =str_replace(" ", "", $imageName);
							$image->move(public_path().'/media/report', $imageName);
							$survey->report = $imageName;
						}
					
							$survey->status='3';
							

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

							if($survey->assign_to_op!="" || $survey->assign_to_op!="0"){
										$operator_id=$survey->assign_to_op;
							}else{
								$operator_id=$survey->user_id;
	
							}

							$operator_token =  User::select('users.id','users.first_name','users.email','users.type',
							'users.device_id','users.country_id')->where('id',$operator_id)->first(); 

							$helper->SendNotification($operator_token->device_id,'Survey report received','You have received a survey report. You can view and download the report in your account under Report Submitted surveys tab.');

							$notification = new Notification();
							$notification->user_id = $operator_token->id;
							$notification->title = 'Survey report received';
							$notification->noti_type = 'Survey report received';
							$notification->user_type = $operator_token->type;
							$notification->notification = 'You have received a survey report. You can view and download the report in your account under Report Submitted surveys tab.';
							$notification->country_id = $operator_token->country_id;
							$notification->is_read = 0;
							$notification->save();

							// $data1 = array( 'email' =>$operator_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $operator_token->email,'content' => 'Your survey request has been submitted. Please wait for the surveyor(s) to respond'));
							// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
							// {
							// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Survey Report Submit' );
			
							// });

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

							return response()->json(['class'=>'success' ,  'message'=>"You have successfully submitted your survey report… "]);	
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
				$surveyor_data =  Survey::select('survey.*')->where('survey.id',$survey_id)->where('survey.status','!=','4')->first();
				$helper=new Helpers;
					if(!empty($surveyor_data) || !empty($survey_users))
					{
						if($surveyor_data->survey_type_id!='31')
					{
						$survey_users =  SurveyUsers::select('survey_users.*')
						->where('survey_users.survey_id',$survey_id)
						->where('survey_users.status','upcoming')
						->first();
						$survey_users->is_finished='1';
						$survey_users->save();
					}


						
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

						if($type=="report_accept")
						{   
							$surveyor_data->status='4';
							

							
							$helper->SendNotification($surveyor_token->device_id,' Survey report has been accepted','The report you submitted has been accepted by the operator. The invoice has been emailed to the operator');

							$notification = new Notification();
							$notification->user_id = $surveyor_token->id;
							$notification->title = 'Survey report has been accepted';
							$notification->noti_type = 'Survey report has been accepted';
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

						}
							
							
							$total_price=0;
							$survey_price=0;
							$port_price=0;
							$survey_type_price=0;
							$user=User::where('id',$surveyors_id)->first();

							if($surveyor_data->survey_type_id=='31')
							{
								
								if($user->type=="3"){
									$custom_survey_price_data = Customsurveyusers::select('custom_survey_users.*')
								->where("custom_survey_users.survey_id",$surveyor_data->id)
								->where("custom_survey_users.surveyors_id",$surveyor_data->created_by)->first();
								}else{
									$custom_survey_price_data = Customsurveyusers::select('custom_survey_users.*')
								->where("custom_survey_users.survey_id",$surveyor_data->id)
								->where("custom_survey_users.surveyors_id",$surveyor_data->accept_by)->first();
								}

								 $total_price=$custom_survey_price_data->amount;
								 $port_price=0;
								 $survey_type_price=$custom_survey_price_data->amount;
							}else
							{

								if($user->type=="3"){
									$survey_price_data = UsersSurveyPrice::select('users_survey_price.*')
								->where("users_survey_price.survey_type_id",$surveyor_data->survey_type_id)
								->where("users_survey_price.user_id",$surveyor_data->created_by)->first();
								}else{
									$survey_price_data = UsersSurveyPrice::select('users_survey_price.*')
								->where("users_survey_price.survey_type_id",$surveyor_data->survey_type_id)
								->where("users_survey_price.user_id",$surveyor_data->accept_by)->first();
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

							$payment->survey_id= $survey_id;
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
							'to'=>array('company'=>$company ,
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
								$textMessage = str_replace(array('USER_NAME','LINK'),
								 array($op_token->first_name,$link),$textMessage);

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
							 $message = 'You have accepted the survey report and an invoice is created. Please rate/review the surveyor!';
							 return response()->json(['class'=>"success" ,'message'=>$message]);

					}else{
					
						return response()->json(['class'=>"danger" ,'message'=>"Data Not Found"]);

					}
		}
		
		
		public function addrating($survey_id,$operator_id,$surveyor_id)
		{
			return view('pages.addratingform',['survey_id'=>$survey_id,'operator_id'=>$operator_id,'surveyor_id'=>$surveyor_id]);
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
					 $survey_id =  $request->input('survey_id');
					 $comment =  $request->input('comment');
					 $rating_1 =  $request->input('rating_1');
					 $check =  Rating::select('*')->where('survey_id',base64_decode($survey_id))->where('operator_id',$user->id )->where('surveyor_id',base64_decode($surveyor_id) )->first(); 
				
					 if(empty($check))
					{
						$rating =  new Rating ;
											
						$rating->operator_id=$user->id;
						$rating->survey_id=base64_decode($survey_id);
						$rating->surveyor_id=base64_decode($surveyor_id);
						$rating->rating=$rating_1;
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

					
					// echo $user_rating=(int)$total/(int)$rating_data_count;exit;
					return response()->json(['class'=>'success' ,  'message'=>"Thank you for rating/reviewing the surveyor…"]);	
					

				
			}
		

	}
	

	public function uploaddocument(Request $request)
	{
		$user = Auth::user();
		$user = User::where('id',$user->id)->first();
		if($user->type=="2")
		{
			$validator = Validator::make($request->all(), [
				'experience' => 'required',
				'about_me' => 'max:1000',
				]);

				if($user->invoice_address_to_company==""){
					$validator = Validator::make($request->all(), [
						'invoice_address_to_company' => 'required_without_all:utility_bill,incorporation_certificate']);
				}
				if($user->utility_bill==""){
					$validator = Validator::make($request->all(), [
						'utility_bill' => 'required_without_all:invoice_address_to_company,incorporation_certificate',]);
				}
				if($user->incorporation_certificate==""){
					$validator = Validator::make($request->all(), [
						'incorporation_certificate' => 'required_without_all:utility_bill,invoice_address_to_company',]);
				}

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
		{
			return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
		}
		else if($user->city=="" || $user->street_address==""){
			echo json_encode(array('class'=>'danger','message'=>'Please hit "edit profile" button and enter your address details.'));die;

		}
		else if($user->type=="4" && $user->country_id=="235" && $request->input('ssn')=="" ){
			echo json_encode(array('class'=>'danger','message'=>'Please enter SSN .'));die;
		}
		else if($user->type=="4" && $user->country_id!="235" && $request->input('company_tax_id')==""){
			echo json_encode(array('class'=>'danger','message'=>'Please enter Tax ID.'));die;
		}
		else
		{
			try 
			{
				
			 
				
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
				$sac_Document =str_replace(" ", "", $sac_Document);
				$sac_document->move(public_path().'/media/users/sac_document', $sac_Document);
				$user->sac_document = $sac_Document;
			}
			if(isset($profile_pic))
			{
				$profile_Pic = time().$profile_pic->getClientOriginalName();
				$profile_Pic =str_replace(" ", "", $profile_Pic);
				$profile_pic->move(public_path().'/media/users', $profile_Pic);
				$user->profile_pic = $profile_Pic;
			}

			
			if(isset($invoice_address_to_company))
			{
				$invoice_address_to_Company = time().$invoice_address_to_company->getClientOriginalName();
				$invoice_address_to_Company =str_replace(" ", "", $invoice_address_to_Company);
				$invoice_address_to_company->move(public_path().'/media/users/invoice_address_to_company', $invoice_address_to_Company);
				$user->invoice_address_to_company = $invoice_address_to_Company;
			}
					
			
			if(isset($tax_id_document))
			{
				$tax_id_Document = time().$tax_id_document->getClientOriginalName();
				$tax_id_Document =str_replace(" ", "", $tax_id_Document);
				$tax_id_document->move(public_path().'/media/users/tax_id_document', $tax_id_Document);
				$user->tax_id_document = $tax_id_Document;
			}



			if(isset($utility_bill))
			{
				$utility_Bill = time().$utility_bill->getClientOriginalName();
				$utility_Bill =str_replace(" ", "", $utility_Bill);
				$utility_bill->move(public_path().'/media/users/utility_bill', $utility_Bill);
				$user->utility_bill = $utility_Bill;
			}
		
			if(isset($incorporation_certificate))
			{
				$incorporation_Certificate = time().$incorporation_certificate->getClientOriginalName();
				$incorporation_Certificate =str_replace(" ", "", $incorporation_Certificate);
				$incorporation_certificate->move(public_path().'/media/users/incorporation_certificate', $incorporation_Certificate);
				$user->incorporation_certificate = $incorporation_Certificate;
			}
		
			if(isset($upload_id))
			{
				$upload_Id = time().$upload_id->getClientOriginalName();
				$upload_Id =str_replace(" ", "", $upload_Id);
				$upload_id->move(public_path().'/media/users/upload_id', $upload_Id);
				$user->upload_id = $upload_Id;
			}
		
			if(isset($diploma))
			{
				$diplomaName = time().$diploma->getClientOriginalName();
				$diplomaName =str_replace(" ", "", $diplomaName);
				$diploma->move(public_path().'/media/users/diploma', $diplomaName);
				$user->diploma = $diplomaName;
			}
			if(isset($employment_reference_letter))
			{
					$employment_reference_letterName = time().$employment_reference_letter->getClientOriginalName();
					$employment_reference_letterName =str_replace(" ", "", $employment_reference_letterName);
					$employment_reference_letter->move(public_path().'/media/users/employment_reference_letter', $employment_reference_letterName);
			
					$user->employment_reference_letter = $employment_reference_letterName;
			}
			if(isset($certificates))
			{
				$certificatesName = time().$certificates->getClientOriginalName();
				$certificatesName =str_replace(" ", "", $certificatesName);
				$certificates->move(public_path().'/media/users/certificates', $certificatesName);
				$user->certificates = $certificatesName;
			}
			if(isset($port_gate_pass))
			{
				$port_gate_passName = time().$port_gate_pass->getClientOriginalName();
				$port_gate_passName =str_replace(" ", "", $port_gate_passName);
				$port_gate_pass->move(public_path().'/media/users/port_gate_pass', $port_gate_passName);
				$user->port_gate_pass = $port_gate_passName;
			}
			$user->save();
			//dd($user);
			$data1 = array( 'email' =>$user->email, 'from' => ' imars@marineinfotech.com', 'from_name' => 'iMarS',"data1"=>array('user_name' =>$user->first_name,
			'content' => 'Your profile is under review, and will be verified within 24 hours.'));
			Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
			{
				$message->from('imars@marineinfotech.com','iMarS')->to( $data1['email'])->subject('Your profile is under review.');

				//$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Your profile is under review.' );

			});
			echo json_encode(array('class'=>'success','message'=>'Your profile is under review, and will be verified within 24 hours.'));die;

		}
			catch(Exception $e) {
				echo json_encode(array('class'=>'alert','message'=>'Something Went Wrong!.'));die;

			  }
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
				$message="You can now make yourself available/unavailable for any day on your calendar… ";

			}else{
				$users->is_avail='0';
				$message="You successfully made yourself unavailable for an indefinite time!";
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
					$surveyor_token =  User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as surveyor_name'))
					->where('id',$surveyors_id)->first(); 
					if(!empty($surveyor_data) && !empty($survey_users))
					{
						if($surveyor_data->assign_to_op!="" || $surveyor_data->assign_to_op!="0"){
							$operator_id=$surveyor_data->assign_to_op;
						}else{
							$operator_id=$surveyor_data->user_id;
	
						}
	
							// $operator_token =  User::select('users.*')->where('id',$operator_id)->first(); 

						$operator_token =  User::select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as operator_name'),'users.id','users.email','users.type','users.device_id','users.country_id')
						->where('id',$operator_id)->first(); 

						$survey_users->status="upcoming";
						$survey_users->amount=$amount;
						$survey_users->save();

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
				
								if($operator_token->operator_name!='' && $surveyor_token->surveyor_name!="" && $surveyor_data->survey_number )
								{
									$textMessage = str_replace(array('OPERATOR_NAME','SURVEYOR_NAME','BID_AMOUNT','SURVEY_NUMBER'),
									 array($operator_token->operator_name,$surveyor_token->surveyor_name,$amount,$surveyor_data->survey_number),$textMessage);
									
									Mail::raw($textMessage, function ($messages) use ($to,$subject) {
										
										$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
									});
								}
							}
							return response()->json(['class'=>'success' ,  'message'=>"Thank you for bidding for a Custom Occasional Survey request. Fingers crossed! You will be notified if your bid wins… "]);	
						
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
								 $surveyor_token =  User::select('users.id','users.first_name','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyors_id)->first(); 
		 
								 $helper->SendNotification($surveyor_token->device_id,'Congratulations, your quote won the bidding process!','The quote you submitted won the bidding process for Custom Occasional Survey. You can see the details of the survey request not in upcoming surveys tab.');
								
								 $notification = new Notification();
								 $notification->user_id = $surveyor_token->id;
								 $notification->title = 'Congratulations, your quote won the bidding process!';
								 $notification->noti_type = 'Congratulations, your quote won the bidding process!';
								 $notification->user_type = $surveyor_token->type;
								 $notification->notification = 'The quote you submitted won the bidding process for Custom Occasional Survey. You can see the details of the survey request not in upcoming surveys tab';
								 $notification->country_id = $surveyor_token->country_id;
								 $notification->is_read = 0;
								 $notification->save();

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
								
						 }
							return response()->json(['class'=>'success' ,  'message'=>"You have selected the surveyor for your Custom Occasional Survey…"]);	
							
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
			$surveyor_data->save();	  
			$helper=new Helpers;
			$csurveyors_id = DB::table('custom_survey_users')->select('surveyors_id')->where('custom_survey_users.id',$survey_id)->get();
				
			if($user->type=='0' || $user->type=='1')
			{	 
				if(!empty($csurveyors_id))
				{
					foreach($csurveyors_id as $surveyor_id)
					{
						$surveyors_token =  User::select('users.id','users.email','users.first_name','users.type','users.device_id','users.country_id')->where('id',$surveyor_id->surveyors_id)->first(); 
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
					}

					if($surveyor_data->assign_to!=0)
					{
						$surveyors_token =  User::select('users.id','users.first_name','users.email','users.type','users.device_id','users.country_id')->where('id',$surveyor_data->assign_to)->first(); 

					}else
					{
						$surveyors_token =  SurveyUsers::select('users.id','users.first_name',
						'users.email','users.type','users.device_id','users.country_id')
						->leftJoin('users', 'survey_users.surveyors_id', '=', 'users.id')
						->where('survey_users.survey_id',$survey_id)
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
	
			if($user->type=='2' || $user->type=='3' || $user->type=='4'){
				$message="You have successfully cancelled this survey. The other party will be notified. ";
			}else{
				$message="You cancelled a survey and it is now listed in “Cancelled” tab…";
			}
				return response()->json(['class'=>'success' ,  'message'=>$message]);	
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
				$createdbysurveyor =  User::select('id')->where('id',$user->id)->first();
				$ids=array();
				if(!empty($createdbysurveyor)){
					
						$ids[]=$createdbysurveyor->id;
				}
					array_push($ids,$user->id);
			}
			$finance_data = Earning::select("payment.*",'port.port as port_name',
			'vessels.name as vesselsname','survey.survey_number','survey.invoice')
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
			'vessels.name as vesselsname','survey.survey_number','survey.invoice')
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
			'vessels.name as vesselsname','survey.survey_number','survey.invoice')
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
			'vessels.name as vesselsname','survey.survey_number','survey.invoice','survey_type.code as code')
			->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
			->leftJoin('survey_type', 'survey.survey_type_id', '=', 'survey_type.id')	
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
				'acc_number'=>!empty($userBankdetail->acc_number) ? $userBankdetail->acc_number : '',
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
					->where('payment.invoice_status','paid')

					->where('payment.request','0')
					->get();

					

					$surveyor_balance = Earning::select("payment.*")
					->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
					->where('payment.surveyor_id',$user->id)
					->where('payment.paid_to_surveyor_status','unpaid')
					->where('payment.invoice_status','paid')
					->sum('payment.transfer_to_surveyor');

					$total_commission = Earning::select("payment.*")
					->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
					->where('payment.surveyor_id',$user->id)
					->where('payment.paid_to_surveyor_status','unpaid')
					->where('payment.invoice_status','paid')
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
						->where('payment.request','0')
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
					
					

					return response()->json(['class'=>'success' ,  'message'=>"You have successfully requested the transfer of your account balance via your preferred payment method… "]);	

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
	 
						$surveyors_token =  User::select('users.id','users.first_name','users.email','users.type','users.device_id','users.country_id')->where('id',$assign_to)->first(); 
						
						$helper->SendNotification($surveyors_token->device_id,'You are assinged a new survey!','You have been assigned a new survey '.$surveyor_data->survey_number.'. Check your upcoming surveys for details.');
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
						// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Assign Survey' );
		
						// });

						$emailData = Emailtemplates::where('slug','=','assign-survey-to-surveyor')->first();

						if($emailData){
							$textMessage = strip_tags($emailData->description);
							$subject = $emailData->subject;
							$to = $surveyors_token->email;

							if($surveyors_token->first_name!='')
							{
								$textMessage = str_replace(array('USER_NAME','SURVEY_NUMBER'),
								 array($surveyors_token->first_name,$surveyor_data->survey_number),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);

								});
							}
						}
							 
							 return response()->json(['class'=>'success' ,  'message'=>"You have successfully assigned this survey to another surveyor… "]);	

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
							$surveyor_data->assign_to_op= $user->id;
							$surveyor_data->save();
						 }						  
						 $helper=new Helpers;
						 $opeartor_token =  User::select('users.id','users.first_name','users.email','users.type',
						 'users.device_id','users.country_id')->where('id',$assign_to_op)->first(); 
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
								$textMessage = str_replace(array("USER_NAME",'SURVEY_NUMBER'),
								 array($opeartor_token->first_name,$surveyor_data->survey_number),$textMessage);
								
								Mail::raw($textMessage, function ($messages) use ($to,$subject) {
									
									$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
								});
							}
						}
						 return response()->json(['class'=>'success' ,  'message'=>"You have successfully assigned this survey to another operator…"]);	

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
								 
								 return response()->json(['class'=>'success' ,  'message'=>"You have successfully updated the start date of a survey and your calendar has been updated… "]);	
	
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