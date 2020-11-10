<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\User;
use App\Uniqcode;
use App\Models\Admin;
use App\Models\Port;
use App\Models\Countries;
use App\Models\Emailtemplates;
use App\Models\Surveytype;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;
class usersController extends Controller {
  private $admin;
  public function __construct()
    {
		if (session('admin')['id'])
		{
			$admindata = Admin::find(session('admin')['id']);
			
			$this->user = $admindata;
		}

    }

	public function index(Request $request)

	{   
		$first_name=$request->input('first_name');
		$last_name=$request->input('last_name');
		$email=$request->input('email');
		$company=$request->input('company');
		$country_id=$request->input('country_id');

		$type=$request->input('type');
		$status=$request->input('status');
		$start_date= $request->input('start_date');
		$end_date=$request->input('end_date');

		 $userdata = User::select('users.*','p.port as portname','countries.name as country')
		->leftJoin('port as p', 'users.port_id', '=', 'p.id')
		->leftJoin('countries', 'countries.id', '=', 'users.country_id')
		 ->where('is_admin','0')
		 ->orderBy("created_at","DESC");
		 
		 if($status!="")
		 { 
			 $userdata=$userdata->where('users.status',$status);
			 
		 }else{
			$userdata=$userdata->where('users.status','1');
		 }


		 if($first_name!="")
		 { 
			 $userdata=$userdata->where('users.first_name','LIKE',"%$first_name%");
			 
		 }
		else if($last_name!="")
		 { 
			 $userdata=$userdata->where('users.last_name','LIKE',"%$last_name%");
			 
		 }
		 else if($email!="")
		 { 
			 $userdata=$userdata->where('users.email','LIKE',"%$email%");
			 
		 }
		 else if($company!="")
		 { 
			$userdata=$userdata->where('users.company','LIKE',"%$company%");
			// $company_user=User::select('id')->where('company','LIKE',"%$company%")->first();
			// $userdata=$userdata->where(function ($query) use ($company_user,$company) {
			// 	$query->Where('created_by',$company_user->id)
			// 		->orwhere('users.company','LIKE',"%$company%" );});
					

			
			 
		 }
		 else if($country_id!="")
		 { 
			 $userdata=$userdata->where('users.country_id','=',$country_id);
			 
		 }
		else if($type!="")
		{ 
			$userdata=$userdata->where('users.type',$type);
			
		} 
		else if ($start_date!="" && $end_date!="") {


            $start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$end_date = date('Y-m-d H:i:s', strtotime($end_date . ' 23:59:59'));
			$userdata = $userdata->whereBetween('users.created_at', [$start_date, $end_date]);
            

        } else if ($start_date!="" && $end_date=="") {

			//$start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$start_date = date('Y-m-d H:i:s', strtotime($start_date . ' 01:00:00'));

			$userdata =$userdata->where('users.created_at',">=",$start_date);

           
        } else if ($start_date=="" && $end_date!="") {
			$end_date = date('Y-m-d H:i:s', strtotime($end_date .' 23:59:59'));
			$userdata = $userdata->where('users.created_at',"<=",$end_date);

        }
		 $userdata = $userdata->paginate(20);
			
		 $countrydata = Countries::orderBy("name","ASC")->get();
		$country_box=array(''=>'Select Country');
		foreach($countrydata as $key=>$value){

			$country_box[$value->id]=$value->name;

		}
//dd( $userdata);
		 if ($request->ajax()) 
		 {
			 return view('admin.users.search', compact('userdata','admindata'));  
		 }
		 $admindata = Admin::find(session('admin')['id']);
		 return view('admin.users.show', ['admindata'=>$admindata,'userdata'=>$userdata,'country_box'=>$country_box]);	
	}
	
	public function adduser(Request $request)
	{   
	    $surveycategory = Surveytype::select('name','id')->get();

        $surveycategory_box=array(''=>"Select Survey Category");
        foreach ($surveycategory as $key => $value) {
            $surveycategory_box[$value->id]=$value->name;
		}
		
		$countrydata = Countries::orderBy("name","ASC")->get();
		$country_box=array(''=>'Code');
		foreach($countrydata as $key=>$value){

			$country_box[$value->id]=$value->name.'('.$value->phonecode.')';

		}


		return view('admin.users.add',['surveycategory_box'=>$surveycategory_box,'country_box'=>$country_box]);
	} 
	public function adduserpost(Request $request)
	{    $company_tax_id =  $request->input('company_tax_id');
		$country_id =  $request->input('country_id');
			$validator = Validator::make($request->all(), [
			'type' => 'required',
			'first_name' => 'required|max:50',
			'last_name' => 'required|max:50',
			'email' => 'required|unique:users|max:50',
			'country_id' => 'required',
			'city' => 'required',
			'mobile' => 'required|regex:/[0-9]{9}/',
			'password' => 'required|min:6|max:16|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
			
			]);
			$company_tax_check=User::where('country_id',$country_id)->where('company_tax_id',$company_tax_id)->count();

			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				if($company_tax_check>0)
				{
					return response()->json(['success'=>false ,'message'=>false,'errors'=>array('company_tax_id'=>'company tax id has already been taken')]);
				}
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$user = new User();
				$type =  $request->input('type');
				$first_name =  $request->input('first_name');
				$last_name =  $request->input('last_name');
				$email =  $request->input('email');
				$mobile =  $request->input('mobile');
				$password =  $request->input('password');
				$company =  $request->input('company');
				
				
				$company_website =  $request->input('company_website');
				$company_address =  $request->input('company_address');
				
				
				$city =  $request->input('city');

				$survey_category_id =  $request->input('survey_category_id');

				$about_me =  $request->input('about_me');
				
				$experience =  $request->input('experience');
				
				

				$image = $request->file('image');

				$upload_id = $request->file('upload_id');

				$tax_id_document = $request->file('tax_id_document');
				$invoice_address_to_company = $request->file('invoice_address_to_company');
				$utility_bill = $request->file('utility_bill');
				$incorporation_certificate = $request->file('incorporation_certificate');

				if(isset($image))
					{
						$imageName = time().$image->getClientOriginalName();
						$image->move(public_path().'/media/users', $imageName);
						$user->profile_pic = $imageName;
					}

				if(isset($upload_id))
				{
					$upload_idName = time().$upload_id->getClientOriginalName();
					$upload_id->move(public_path().'/media/users/upload_id', $upload_idName);
					$user->upload_id = $upload_idName;
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


				

				$user->type = $type;
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->mobile = $mobile;

				if($survey_category_id =="31"){
					$user->conduct_custom = '1';
				}
				


				if($country_id!=""){
					$user->country_id = $country_id;
					$countrydata = Countries::where('id',$country_id)->first();
					$user->country_code = $countrydata->phonecode;
				}
				$user->city = $city;
				if($company_tax_id!=""){
					$user->company_tax_id = $company_tax_id;
				}
				if($company!=""){
					$user->company = $company;
				}
				
				if($about_me!=""){
					$user->about_me = $about_me;
				}
				
				if($company_website!=""){
					$user->company_website = $company_website;
				}
				if($company_address!=""){
					$user->company_address = $company_address;
				}
				if($survey_category_id!=""){
					$user->survey_category_id = $survey_category_id;
				}
				
				if($experience!=""){
					$user->experience = $experience;
				}
				

				

				$user->password =Hash::make($password);
				$user->save();
			// 	$emailData = Emailtemplates::where('slug','=','user-registration')->first();
			// 	//dd($emailData);
			// 	if($emailData){
			// 	  $textMessage = strip_tags($emailData->description);
			// 	   $user->subject = $emailData->subject;
				 
			// 	  if($user->email!='')
			// 	  {
			// 		  $textMessage = str_replace(array('{USERNAME}','{EMAIL}','{PASS}'), array($user->first_name,$user->email,$password),$textMessage);
					  
			// 		  Mail::raw($textMessage, function ($messages) use ($user) {
			// 			  $to = $user->email;
			// 			  $messages->to($to)->subject($user->subject);
			// 		  });
			// 	  }
			//   }
				echo json_encode(array('class'=>'success','message'=>'User Added successfully.'));die;

			
			}
		
	} 
	
	public function edituser($id)

	{
		$userId = base64_decode($id);
		$userdata = User::find($userId);
		 $surveycategory = Surveytype::select('name','id')->get();

        $surveycategory_box=array(''=>"Select Survey Category");
        foreach ($surveycategory as $key => $value) {
            $surveycategory_box[$value->id]=$value->name;
		}
		$countrydata = Countries::orderBy("name","ASC")->get();
		$country_box=array(''=>'Code');
		foreach($countrydata as $key=>$value){

			$country_box[$value->id]=$value->name.'('.$value->phonecode.')';

		}
		return view('admin.users.edit',["userdata" => $userdata, 'surveycategory_box' => $surveycategory_box,'country_box'=>$country_box]);

	}
	
	public function resetpassword($id)
	{
		//$userId = base64_decode($id);
		$userdata = User::find($id);
		return view('admin.users.resetpassword',["userdata" => $userdata]);

	}
	
	public function viewuser($id)
	{
		$userId = base64_decode($id);

	

		
			$userdata = User::select('users.*','p.name as country')
			->where('users.id',$userId)
			->leftJoin('countries as p', 'users.country_id', '=', 'p.id')->first();
	
		
		//dd($userdata);
		return view('admin.users.view',["userdata" => $userdata]);

	}
	

	public function edituserpost(Request $request)

	{
		$userId = $request->input('user_id');
		 $user_id =  base64_decode($userId);
		$validator = Validator::make($request->all(), [
			'type' => 'required',
			'status' => 'required',
			'country_id' => 'required',
			'city' => 'required',
			'mobile' => 'required|regex:/[0-9]{9}/',

			// 'profile_img' => 'max:2048|mimes:jpg,jpeg,gif,png',

			 ]);

			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$user = User::find($user_id);
				$type =  $request->input('type');
				$status =  $request->input('status');
				$survey_category_id =  $request->input('survey_category_id');
				$country_id =  $request->input('country_id');
				$city =  $request->input('city');
				$mobile =  $request->input('mobile');

				if($survey_category_id =="31"){
					$user->conduct_custom = '1';
				}
				$user->type = $type;
				$user->country_id = $country_id;
				$user->city = $city;
				
				$user->status = $status;
				
				$user->mobile = $mobile;

				$user->survey_category_id = $survey_category_id;
				$user->save();
				if( $status==1){
					$data1 = array( 'email' =>$user->email, 'from' => 'imars@marineinfotech.com', 'from_name' => 'iMarS',"data1"=>array('email' => $user->email,'user_name' => $user->first_name.' '. $user->last_name,'content' => 'Your profile is approved, and you can now log in and see all menus.'));
					Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
					{
						$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Your profile is activated.' );
	
					});
				}
				echo json_encode(array('class'=>'success','message'=>' User Edit successfully.'));die;

				
			}	

	}

    public function resetpasswordpost(Request $request)

	{
		$userId = $request->input('user_id');
		 $user_id =  base64_decode($userId);
		$validator = Validator::make($request->all(), [
				'password' => 'required|min:6|max:16|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
			'password_confirmation' => 'required|min:6|max:16',
			 ]);

			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$user = User::find($user_id);
				
				$password =  $request->input('password');
				$user->password = Hash::make($password);
				$user->save();
				echo json_encode(array('class'=>'success','message'=>' Password change successfully.'));die;

				
			}	

	}


	public function userstatus(Request $request)
	{
		$id = base64_decode($request->input('id'));
		$userdata = User::find($id);
		
		if($userdata->status=="1")
		{
			$userdata->status = "0";
			$userdata->save();
			echo json_encode(array('class'=>'success','message'=>'  User Deactive successfully.'));die;
		

		}else
		{
			
			$userdata->status = "1";
			$userdata->save();
			echo json_encode(array('class'=>'success','message'=>'  User Active successfully.'));die;
		
		}
		
	}

		

public function usersdelete($id)
 {

		$id = base64_decode($id);

		$user = User::find($id);

		$user->delete();
		echo json_encode(array('class'=>'success','message'=>' User successfully deleted.'));die;
		
		 return redirect('/admin/user-management/users');	

	   }
	
}

?>