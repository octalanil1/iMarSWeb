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
use App\Models\Emailtemplates;
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

		 $userdata = User::select('users.*','p.country as country' )
		 ->leftJoin('port as p', 'users.country_id', '=', 'p.id')
		 ->where('is_admin','0')
		 
		 ->orderBy("created_at","DESC");
		 if($status!="")
		 { 
			 $userdata=$userdata->where('status',$status);
			 
		 }else{
			$userdata=$userdata->where('status','1');
		 }


		 if($first_name!="")
		 { 
			 $userdata=$userdata->where('first_name','LIKE',"%$first_name%");
			 
		 }
		else if($last_name!="")
		 { 
			 $userdata=$userdata->where('last_name','LIKE',"%$last_name%");
			 
		 }
		 else if($email!="")
		 { 
			 $userdata=$userdata->where('email','LIKE',"%$email%");
			 
		 }
		 else if($company!="")
		 { 
			 $userdata=$userdata->where('company','LIKE',"%$company%");
			 
		 }
		 else if($country_id!="")
		 { 
			 $userdata=$userdata->where('country_id','=',$country_id);
			 
		 }
		else if($type!="")
		{ 
			$userdata=$userdata->where('type',$type);
			
		} 
		else if ($start_date!="" && $end_date!="") {


            $start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$end_date = date('Y-m-d H:i:s', strtotime($end_date . ' 23:59:59'));
			$userdata = $userdata->whereBetween('created_at', [$start_date, $end_date]);
            

        } else if ($start_date!="" && $end_date=="") {

			$start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$userdata =$userdata->where('created_at',">=",$start_date);

           
        } else if ($start_date=="" && $end_date!="") {

            $end_date = date('Y-m-d H:i:s', strtotime($end_date));
			$userdata = $userdata->where('created_at',"<=",$end_date);

        }
		 $userdata = $userdata->paginate(10);
			
		 $countrydata = Port::orderBy("country","DESC")->groupBY('country')->get();
		 $country_box=array(''=>'Select Country');
		 foreach($countrydata as $key=>$value){
 
			 $country_box[$value->id]=$value->country;
 
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
		return view('admin.users.add');
	} 
	public function adduserpost(Request $request)
	{
			$validator = Validator::make($request->all(), [
			'type' => 'required',
			'first_name' => 'required|max:50',
			'last_name' => 'required|max:50',
			'email' => 'required|max:50',
			'mobile' => 'required',
			'password' => 'required',
		
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
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
				$image = $request->file('image');


				if(isset($image))
					{
						$imageName = time().$image->getClientOriginalName();
						$image->move(public_path().'/media/users', $imageName);
						$user->profile_pic = $imageName;
					}

				$user->type = $type;
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->mobile = $mobile;
				if($company!=""){
					$user->company = $company;
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
		return view('admin.users.edit',["userdata" => $userdata]);

	}
	public function viewuser($id)

	{
		$userId = base64_decode($id);
		$userdata = User::select('users.*','p.country as country')
		->where('users.id',$userId)
		->leftJoin('port as p', 'users.country_id', '=', 'p.id')->first();
		return view('admin.users.view',["userdata" => $userdata]);

	}
	

	public function edituserpost(Request $request)

	{
		$userId = $request->input('user_id');
		 $user_id =  base64_decode($userId);
		$validator = Validator::make($request->all(), [
			'type' => 'required',
			'status' => 'required',
			
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
				


				
				$user->type = $type;
				$user->status = $status;
				
				
				$user->save();
				echo json_encode(array('class'=>'success','message'=>' User Edit successfully.'));die;

				
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