<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Validator;
use App\User;
use Session;
use Hash;
use DB;
use Auth;
use Mail;
use App\Admin;
class ForgotPasswordController extends Controller {

public function index()

{

	 if (session('admin')['id'])

	 { 

	

	}else {

	return view('admin.fogot');

	}

}



public function postmail(Request $request)

{



	$validator = Validator::make($request->all(), [

	'email' => 'required|email',

	]);

	if ($validator->fails()) {

	return redirect('/admin/forgot-password')->withErrors($validator)->withInput();

	}else{

	$email =  $request->input('email');

	$emaildata = DB::table('users')->where('email',$email)->where('is_admin','1')->first();

	if(!isset($emaildata->id)){

	Session::put('msg', '<strong class="alert alert-danger">Please enter your registered email.</strong>');

	return redirect('/admin/forgot-password');

	}else{
     $length = 10;
	 $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
     $charactersLength = strlen($characters);
     $randomString = '';

		for ($i = 0; $i < $length; $i++) {

			$randomString .= $characters[rand(0, $charactersLength - 1)];

		}
	$url = $randomString;

	$cur_date = date("Y-m-d H:i:s");

	$user = User::find($emaildata->id);

	$user->forgot_url = $url;

	$user->forgot_time = $cur_date;

	$user->save();

	

	$create_url = \App::make('url')->to('/admin/create-password')."/".$url;

	$data = array( 'email' => $email, 'first_name' => $emaildata->first_name, 'from' => 'noreply@demoasite1.com', 'from_name' => 'iMarS', 'data' =>$create_url );

	Mail::send( 'admin.temp_forgot', $data, function( $message ) use ($data)

	{

		$message->to( $data['email'] )->from( $data['from'], $data['from_name'] )->subject( 'forget password' );

	});

	

	Session::put('msg', '<strong class="alert alert-success">Forgot password instruction has been successfully sent.</strong>');

	return redirect('/admin/login');

	}

	

	   }

   

  }

 

 public function createpass($uniqurl)

{


	$emaildata = DB::table('users')->whereForgot_url($uniqurl)->first();

	if(!isset($emaildata->id)){

	return view('pages.error404',['msg'=>'Invalid url!']);

	}else{

	$time = date('Y-m-d H:i:s');

	$time1 = strtotime($emaildata->forgot_time);

	$time2 = strtotime($time);

	$diff = $time2 - $time1;

	$hour_diff = $diff/3600;

	if($hour_diff > 24)

	{

	return view('pages.error404',['msg'=>'Your Session has been expired!']);

	}else{

	return view('admin.createpassword',['uniqurl'=>$uniqurl]);

	}

	}

	

}

public function createpasspost(Request $request)

{

	

	$validator = Validator::make($request->all(), [

	'uniqurl' => 'required',

	'password' => 'required|min:8|max:16|confirmed',

    'password_confirmation' => 'required|min:8|max:16',

	

	]);

		if ($validator->fails()) {

		return redirect()->back()->withErrors($validator)->withInput();

		}else{ 

			$password =  $request->input('password');

			$uniqurl =  $request->input('uniqurl');

			$uniqurldata = DB::table('users')->whereForgot_url($uniqurl)->first();

			if(!isset($uniqurldata->id)){

				return view('pages.error404',['msg'=>'Invalid url!']);

				}else{

					$user = User::find($uniqurldata->id);

	            $user->forgot_url = "";

			    $user->password = Hash::make($password);

			    $user->save();

			    Session::put('msg', '<strong class="alert alert-success">password changed successfully. You can login with new password.</strong>');

			    return redirect('/admin/login');

			}

		}

	



} 

 

  

  

}



?>