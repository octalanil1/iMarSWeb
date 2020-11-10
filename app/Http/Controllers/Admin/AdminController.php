<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Validator;
use App\User;
use App\Models\Admin;
use App\Models\Survey;
use App\Models\Port;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;
use Image;
use File;
use Cookie;
use App\Models\Paymentrequest;
use App\Models\Countries;
use App\Models\Disputerequest;

class AdminController extends Controller {
	
 
	
	public function index(Request $request)
	{
		
		$admindata = Admin::find(session('admin')['id']);
		$filter_type =  $request->input('filter_type');
		
		$date = \Carbon\Carbon::today()->subDays(7);

		$all_users = User::where('is_admin','0')->where('created_at', '>=', $date)->count();
		$new_pending_users = User::where('is_admin','0')->where('status','2')->where('created_at', '>=', $date)->count();
		$total_operators = User::where('is_admin','0')->where(function ($query) 
		{$query->where('type','0' ) ->orWhere('type',  '1');})
		->where('created_at', '>=', $date)->count();
		$total_surveyors = User::where('is_admin','0')
		->where(function ($query) {$query->where('type', '2' )->orWhere('type','3')->orWhere('type','4');
					})->where('created_at', '>=', $date)->count();

		$total_survey =  Survey::where('created_at', '>=', $date)->count();
		$pending_survey =  Survey::where('status','0')->where('created_at', '>=', $date)->count();
		$complete_survey =  Survey::where(function ($query) 
		{$query->where('status', '3' )->orWhere('status','4')->orWhere('status','5')->orWhere('status','6');
		})->where('created_at', '>=', $date)->count();
		$cancelled_survey =  Survey::where('status','2')->where('created_at', '>=', $date)->count();

		$dispute_job =  Disputerequest::where('created_at', '>=', $date)->count();
		$total_ports =  Port::where('created_at', '>=', $date)->count();
		$total_country =  Countries::where('created_at', '>=', $date)->count();
		$payment_request_count = Paymentrequest::where('created_at', '>=', $date)->count();
		$payment_request = Paymentrequest::sum('invoice_total');
		
		$topfiveport = Survey::select(DB::raw('count(survey.port_id) as noofrequest'),'p.port as portname' )
			->leftJoin('port as p', 'survey.port_id', '=', 'p.id')
			->groupBy('survey.port_id')
			->orderBy("noofrequest","DESC")->limit(5)->get();
			
 //echo '<pre>';print_r($topfiveport);exit;
		if(!empty($filter_type) && $filter_type!="")
		{
			if($filter_type=='year')
			{  $date = \Carbon\Carbon::today()->subDays(365);
				$all_users = User::where('is_admin','0')->where('created_at', '>=', $date)->count();
				$new_pending_users = User::where('is_admin','0')->where('status','2')->where('created_at', '>=', $date)->count();
				$total_operators = User::where('is_admin','0')->where(function ($query) 
				{$query->where('type','0' ) ->orWhere('type',  '1');})
				->where('created_at', '>=', $date)->count();
				$total_surveyors = User::where('is_admin','0')
				->where(function ($query) {$query->where('type', '2' )->orWhere('type','3')->orWhere('type','4');
							})->where('created_at', '>=', $date)->count();
							$total_survey =  Survey::where('created_at', '>=', $date)->count();
							$pending_survey =  Survey::where('status','0')->where('created_at', '>=', $date)->count();
							$complete_survey =  Survey::where(function ($query) 
							{$query->where('status', '3' )->orWhere('status','4')->orWhere('status','5')->orWhere('status','6');
							})->where('created_at', '>=', $date)->count();
							$cancelled_survey =  Survey::where('status','2')->where('created_at', '>=', $date)->count();
				$dispute_job =  Disputerequest::where('created_at', '>=', $date)->count();
				$total_ports =  Port::where('created_at', '>=', $date)->count();
				$total_country =  Countries::where('created_at', '>=', $date)->count();
				$payment_request_count = Paymentrequest::where('created_at', '>=', $date)->count();
				$payment_request = Paymentrequest::sum('invoice_total');

			}else if($filter_type=='month')
			{   $date = \Carbon\Carbon::today()->subDays(30);
				$all_users = User::where('is_admin','0')->where('created_at', '>=', $date)->count();
				$new_pending_users = User::where('is_admin','0')->where('status','2')->where('created_at', '>=', $date)->count();
				$total_operators = User::where('is_admin','0')->where(function ($query) 
				{$query->where('type','0' ) ->orWhere('type',  '1');})
				->where('created_at', '>=', $date)->count();
				$total_surveyors = User::where('is_admin','0')
				->where(function ($query) {$query->where('type', '2' )->orWhere('type','3')->orWhere('type','4');
							})->where('created_at', '>=', $date)->count();
							$total_survey =  Survey::where('created_at', '>=', $date)->count();
							$pending_survey =  Survey::where('status','0')->where('created_at', '>=', $date)->count();
							$complete_survey =  Survey::where(function ($query) 
							{$query->where('status', '3' )->orWhere('status','4')->orWhere('status','5')->orWhere('status','6');
							})->where('created_at', '>=', $date)->count();
							$cancelled_survey =  Survey::where('status','2')->where('created_at', '>=', $date)->count();
				$dispute_job =  Disputerequest::where('created_at', '>=', $date)->count();
				$total_ports =  Port::where('created_at', '>=', $date)->count();
				$total_country =  Countries::where('created_at', '>=', $date)->count();
				$payment_request_count = Paymentrequest::where('created_at', '>=', $date)->count();
				$payment_request = Paymentrequest::sum('actual_transferto_surveyor');

			}

		}

		if ($request->ajax()) 
		 {
			 return view('admin.dashboard.search', compact('admindata','all_users','new_pending_users',
			 'total_operators','total_surveyors','total_survey','pending_survey','complete_survey',
			 'cancelled_survey','dispute_job','total_ports','total_country','payment_request_count','payment_request','topfiveport'));  
		 }
		 $admindata = Admin::find(session('admin')['id']);
		 return view('admin.dashboard.show',compact('admindata','all_users',
		 'total_operators','total_surveyors','new_pending_users','total_survey','pending_survey','complete_survey',
		 'cancelled_survey','dispute_job','total_ports','total_country','payment_request_count','payment_request','topfiveport'));	
	
	}
   
	public function login()
	{
		 if (session('admin')['id'])
	{ 
		return redirect('/admin'); 
	}else
	{
	  	return view('admin.login');
	}
	}
	public function loginpost(Request $request)
	{
		$validator = Validator::make($request->all(), [ 'email' => 'required', 
		'password' => 'required',
		'g-recaptcha-response' => 'required|captcha'
		
		 ]);
		 if ($validator->fails()) 
		 {
		  return redirect('/admin/login')->withErrors($validator)->withInput();
		 }else
		 {
			// $email =  $request->input('email');
			 //$password =  $request->input('password');
			 $userdata = array(
				 'email' 		=> $request->input('email'),
				 'password' 		=> $request->input('password'),
				 'is_admin' 		=>'1',
			 );
			 //echo "<pre>";print_r($userdata);die; 
			 //$admin = Admin::where('email', $email)->where('password', md5($password))->first();
			 $remember_me = $request->has('remember') ? true : false; 
			 if (Auth::attempt($userdata, $remember_me))
			 {	
				if($remember_me)
				{ 
						setcookie ("email",$request->email,time()+ (86400 * 30));
						setcookie ("password",$request->password,time()+ (86400 * 30));
						
						
				}else{ //die;
					 unset($_COOKIE['email']);
					 setcookie('email', '', 1);
					 unset($_COOKIE['password']);
					 setcookie('password', '', 1);
				}
		 

				 return redirect('/admin');

			 }else{ 

				 Session(['msg' => '<strong class="alert alert-danger">Invalid email and password.</strong>']);
				 return view('admin.login',[]);
				 //return redirect('/admin/login');
			 }
		 }
		
	}
	
	 public function logout()
    { 
		if (session('admin')['id'])
		{ 
			Session::forget('admin');
			Session::put('msg', '<strong class="alert alert-success">Logout Successfully.</strong>');
			return redirect('/admin/login');
		}else 
		{
			return redirect('/admin/login'); 
		 }
				  
	}
		
	public function profile()
	{
		$admindata = User::find('1');

	//dd(	$admindata);
		return view('admin.profile',['admindata'=>$admindata]);
	}
				
	public function editprofile(Request $request)
	{
				   
		$validator = Validator::make($request->all(), 
		[
			
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email',
		]);
		if ($validator->fails()) 
		{
			return redirect('/admin/profile')->withErrors($validator)->withInput();
	
		}else
		{
			$admin = User::find('1');
			//dd($admin);
			$first_name =  $request->input('first_name');
			$last_name =  $request->input('last_name');
			$email =  $request->input('email');
			$password =  $request->input('password');

			
			$admin->first_name = $first_name;
			$admin->last_name = $last_name;
			$admin->email = $email;
			if(!empty($password )){
				$admin->password = Hash::make($password);
			}
			$admin->save();
			echo json_encode(array('class'=>'success','message'=>'Data successfully updated..'));die;

		}
	}
				
				
				
				public function changepassword()
			     {
					$admin = Admin::find(session('admin')['id']);
				  return view('admin.changePassword',['admindata'=>$admin]);
				 }
				
				public function changepasswordpost(Request $request)
			 {
				   $admin = Admin::find(session('admin')['id']);
				 
	$validator = Validator::make($request->all(), [
				'password' => 'required|min:6|max:16|confirmed',
			'password_confirmation' => 'required|min:6|max:16',
			 ]);
			  if ($validator->fails()) {
	               return redirect('/admin/change-password')
	                           ->withErrors($validator)
	                           ->withInput();

					}else{
	 
							$password =  $request->input('password');
							$admin->password = md5($password);
							$admin->save();
							Session::put('msg', '<strong class="alert alert-success">Your password successfully changed.</strong>');
							return redirect('/admin');
	
					}
					
				}
						
		
		public function sendemail(Request $request)
			 {
				   $admin = $this->admin;
				 
	$validator = Validator::make($request->all(), [
				'email' => 'required|email',
				'uniqcode' => 'required',
			 ]);
			  if ($validator->fails()) {
	               return redirect('/admin/code-generate')
	                           ->withErrors($validator)
	                           ->withInput();

					}else{
	                        
							$email =  $request->input('email');
							$uniqcodeval =  $request->input('uniqcode');
							
							
							$data = array( 'email' => $email, 'from' => 'noreply@demoasite1.com', 'from_name' => 'Manta Play', 'data' => $uniqcodeval );
							Mail::send( 'admin.emailtemp', $data, function( $message ) use ($data)
	{
		$message->to( $data['email'] )->from( $data['from'], "manta play" )->subject( 'Sign Up Code' );
	});
				
							Session::put('msg', '<strong class="alert alert-success">Mail successfully sent.</strong>');
							return redirect('/admin');
	
					}
					
				}	
				
				

}


?>
