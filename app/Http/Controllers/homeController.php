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
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use URL;
use Mail;
use Redirect;
use App\Models\Content;
class homeController extends Controller
 {
			
	public function aboutus()
	{
	 return view('pages.about-us');
	
	}
	public function contactus()
	{
	 return view('pages.contact-us');
	
	}
		public function contactuspost(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'first_name' => 'required|max:150',
				'last_name' => 'required',
				'email' => 'required|email',
				'mobile' => 'required',
				'comment' => 'required|max:255',
			]);
			if ($validator->fails()) {
				return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
			}else{
				$first_name =  $request->input('first_name');
				$last_name =  $request->input('last_name');
				$email =  $request->input('email');
				$mobile =  $request->input('mobile');
				$comment =  $request->input('comment');
				
				$contact = new Contact;
				$contact->first_name = $first_name;
				$contact->last_name = $last_name;
				$contact->email = $email;
				$contact->mobile = $mobile;
				$contact->comment = $comment;
				
				$contact->save();
				$data = array( 'email' => "imars@marineinfotech.com", 'from' => 'imars@marineinfotech.com', 'from_name' => 'iMarS',"data"=>array('name' => $first_name.' '.$last_name,'email' => $email, 'mobile' => $mobile, 'comment' => $comment));

				Mail::send( 'pages.email.contact',$data, function( $message ) use ($data)
				{
					$message->to( $data['email'] )->from( $data['from'], $data['from_name'] )->subject( 'Contact Detail' );

				});

				return response()->json(['class'=>'success' ,'message'=>'You have successfully submit your query.']);
			
			}
		}
		public function staticpages($type,$slug)
		{
			$page_content = Content::where('slug',$slug)->where('user',$type)->first();
		    return view('pages.static_page',['page_content'=>$page_content,'slug'=>$slug]);			
		}

	public function emailVerify(Request $request)
	{
		
		$user = Auth::user();
		$email =  $request->input('email');
		
			$validator = Validator::make($request->all(), [
				'email' => 'required|email',

			]);
			
		
		if ($validator->fails()) 
		{
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else
		{		
			$useremailcheck =  User::select('*')->where('email',$email)->first();

			if($useremailcheck!="")
			{
				$create_url = \App::make('url')->to('/verify-email')."/".base64_encode($useremailcheck->id);
			
				$data1 = array( 'email' =>$email, 'from' => 'imars@marineinfotech.com', 'from_name' => 'iMarS',"data1"=>array('email' => $email,'create_url' => $create_url));
					Mail::send( 'pages.email.emailconfirm',$data1, function( $message ) use ($data1)
					{
						$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Verify Email' );

					});
					return response()->json(['class'=>'success' ,  'message'=>"Please check your email and click the link provided in the email to verify your email address. "]);
	
			}else{
				return response()->json(['class'=>'danger' ,  'message'=>"Something went wrong"]);
			}

		}
	}
			
		
	public function emailconfirm($id)
	{
		$user = Auth::user();
		if (Auth::check() && $user->is_admin=='0')
		{
			$user = User::where('id','=', base64_decode($id))->first();

			if($user)
			{
				$user->email_verify = '1';
				$user->save();
				return redirect('/myaccount');
			}
			
		}else
		{
			$user = User::where('id','=', base64_decode($id))->first();
			
			if($user)
			{
				$user->email_verify = '1';
				$user->save();
				Session(['msg' => '<strong class="alert alert-success">Your email has been verified. Please log in to complete your account verification… </strong>']);
				//return view('admin.login',[]);

				return view('pages.signin',[]);
			}
		}

	}

	public function mobileVerify($mobile)
	{
			$useremailcheck =  User::select('*')->where('email',$mobile)->first();
			if($useremailcheck!="")
			{
				$length = 4;
				$characters = '0123456789';
				$charactersLength = strlen($characters);
				$otp = '';

				for ($i = 0; $i < $length; $i++) 
				{
					$otp .= $characters[rand(0, $charactersLength - 1)];
				}

						$code_number = $useremailcheck->country_code.$useremailcheck->mobile;
						try
						{
							$is_send = $this->send_sms($otp,$code_number);
							if($is_send)
							{
								$useremailcheck->otp=$otp;
								$useremailcheck->save();
							}
							return view('pages.mobile_verify_form');
						}
						catch (Exception $e)
						{
							return view('pages.mobile_verify_form');
						}


			}else{
				return response()->json(['class'=>'danger' ,  'message'=>"Something went wrong"]);
			}

		
	}
	

	public function mobileconfirm(Request $request)
	{
		
		$user = Auth::user();
		$otp =  $request->input('otp');
		
			$validator = Validator::make($request->all(), [
				'otp' => 'required',
			

			]);
			
		
		if ($validator->fails()) 
		{
			return response()->json(['success'=>false ,'message'=>false,'errors'=>$validator->errors()]);
		}else
		{	
			if(Auth::check())
			{

			
				$useremailcheck =  User::select('*')->where('id',$user->id)->where('otp',$otp)->first();

				if($useremailcheck!="")
				{
					$useremailcheck->otp = '';
					$useremailcheck->mobile_verify = '1';
					$useremailcheck->save();
						return response()->json(['class'=>'success' ,  'message'=>"Your mobile number has been verified…"]);
		
				}else{
					return response()->json(['class'=>'danger' ,  'message'=>"Wrong otp code"]);
				}
			}else{
				return redirect('/signin');
			}

		}
	}


}

?>