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
use App\Models\Survey;
use App\Models\Surveytype;
use App\Models\Vessels;
use App\Models\Port;
use App\Models\Surveyadmin;

use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;
class SurveyController extends Controller {
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
	    $arrival_start_date= $request->input('arrival_start_date');
		$arrival_end_date= $request->input('arrival_end_date');
		$start_date= $request->input('start_date');
		$end_date= $request->input('end_date');
		$operator_name= $request->input('operator_name');
		$operator_company= $request->input('operator_company');
		$surveyor_name= $request->input('surveyor_name');
		$surveyor_company= $request->input('surveyor_company');
		$port=$request->input('port');
		$status=$request->input('status');
		$ship_id=$request->input('ship_id');
		$srveryor_category=$request->input('srveryor_category');
		
		$start_date= $request->input('start_date');
		$end_date=$request->input('end_date');

		$operator_email=$request->input('operator_email');
		$surveyor_email=$request->input('surveyor_email');
		$country_id=$request->input('country_id');
		$survey_number=$request->input('survey_number');


		$surveydata = Surveyadmin::select("survey_views.*")
		
		->orderBy("created_at","DESC");
			
			
		if($operator_name!="")
		{
			$surveydata = $surveydata->where('operator_name','LIKE',"%$operator_name%");
		}
		if($operator_email!="")
		{
			$surveydata = $surveydata->where('operator_email','LIKE',"%$operator_email%");
		}
		if($operator_company!="")
		{
			$surveydata = $surveydata->where('operator_company','LIKE',"%$operator_company%");
		}
		
		if($surveyor_name!="")
		{
			$surveydata = $surveydata->where("surveyor_name",'LIKE',"%$surveyor_name%");
		}
		if($surveyor_email!="")
		{
			$surveydata = $surveydata->where('surveyor_email','LIKE',"%$surveyor_email%");
		}
		if($surveyor_company!="")
		{
			$surveydata = $surveydata->where('surveyor_company','LIKE',"%$surveyor_company%");
		}
		if($port!="")
		{
			$surveydata = $surveydata->where('port_id',"=",$port);
		}
		if($country_id!="")
		{
			$surveydata = $surveydata->where('country_id',"=",$country_id);
		}
		
		if($status!="")
		{
			$surveydata = $surveydata->where('status',$status);
		}
		if($ship_id!="")
		{
			$surveydata = $surveydata->where('ship_id',$ship_id);
		}
		
		
		if($srveryor_category!="")
		{
			$surveydata = $surveydata->where('survey_type_id',$srveryor_category);
		}
		if($survey_number!="")
		{
			$surveydata = $surveydata->where('survey_number',$survey_number);
		}
		
	 if ($start_date!="" && $end_date!="") {

            $_start_date = date('Y-m-d H:i:s', strtotime($request->input('start_date')));
			$_end_date = date('Y-m-d H:i:s', strtotime($request->input('end_date') . ' 23:59:59'));
			$surveydata = $surveydata->whereBetween('survey.created_at', [$_start_date.'00:00:00', $_end_date.'00:00:00']);
            

        }  else if ($start_date!="" && $end_date=="") {

			$_start_date = date('Y-m-d H:i:s', strtotime($request->input('start_date')));
			$surveydata = $surveydata->whereDate('survey.created_at', $_start_date.'00:00:00')->orderBy("created_at","DESC");

           
        }else if ($start_date=="" && $end_date!="") {

            $end_date = date('Y-m-d H:i:s', strtotime( $request->input('end_date')));
			$surveydata = $surveydata->whereDate('survey.created_at', $end_date.'00:00:00')->orderBy("created_at","DESC");

        }
		
		
		if ($arrival_start_date!="" && $arrival_end_date!="") {

            $a_start_date = date('Y-m-d', strtotime($request->input('arrival_start_date')));
			$a_end_date = date('Y-m-d', strtotime($request->input('arrival_end_date') . ' 23:59:59'));
			//$surveydata = $surveydata->whereBetween('survey.created_at', [$_start_date, $_end_date]);
			$surveydata = $surveydata->where('start_date',">=",$a_start_date)->where('end_date',">=",$a_end_date);
            

        }  else if ($arrival_start_date!="" && $arrival_end_date=="") {

			$a_start_date = date('Y-m-d', strtotime($request->input('arrival_start_date')));
			$surveydata = $surveydata->where('start_date',"=",$a_start_date);

           
        }else if ($arrival_start_date=="" && $arrival_end_date!="") {

            $a_end_date = date('Y-m-d', strtotime( $request->input('arrival_end_date')));
			$surveydata = $surveydata->where('end_date',"=",$a_end_date);

        }
		$surveydata = $surveydata->groupBy('id');
		$surveydata = $surveydata->paginate(20);
//dd($surveydata);
		$userdata = User::where('type','0')->where('is_admin','0')->orderBy("created_at","DESC")->get();
		$user_box=array(''=>'Select User');
		foreach($userdata as $key=>$value){
			$user_box[$value->id]=sprintf('%s %s',$value->first_name,$value->last_name);
		}

		$portdata = Port::orderBy("port","Asc")->get();
		$port_box=array(''=>'Select Port');
		foreach($portdata as $key=>$value){
			$port_box[$value->id]=$value->port;
		}

        $categorydata = Surveytype::where('status','1')->orderBy("name","Asc")->get();
		$category_box=array(''=>'Select Category');
		foreach($categorydata as $key=>$value){
			$category_box[$value->id]=$value->name;
		}
		
		$shipdata = Vessels::orderBy("name","Asc")->get();
		$ship_box=array(''=>'Select Vessel');
		foreach($shipdata as $key=>$value){
			$ship_box[$value->id]=$value->name.'(Imo: '.$value->imo_number.')';
		}
		
		
		
		
		if ($request->ajax()) 
		{
			return view('admin.survey.search', compact('surveydata'));  
        }
		$admindata = Admin::find(session('admin')['id']);
		
		//$surveyCategoryList		=	DB::table('survey_category')->orderBy('name','ASC')->pluck('name','id')->toArray();
        
        return view('admin.survey.show', compact('surveydata','user_box','admindata','port_box','category_box','ship_box'));

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

				$user->type = $type;
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->mobile = $mobile;
				$user->company = $company;
				$user->password =md5($password);
				$user->save();
				return response()->json(['success'=>true ,'message'=>'<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
                User Added successfully
              </div>']);
			}
		
	} 
	
	public function edituser($id)

	{
		$userId = base64_decode($id);
		$userdata = User::find($userId);
		return view('admin.users.edit',["userdata" => $userdata]);

	}
	public function viewsurvey($id)

	{
		$surveyId = base64_decode($id);
		$surveydata = Surveyadmin::select("survey_views.*")
			->where('survey_views.id',$surveyId)
			->first();
		return view('admin.survey.view',["surveydata" => $surveydata]);

	}
	public function viewcompany($id)
	{
		$userId = base64_decode($id);
		$userdata = User::select('users.*')
		->where('users.id',$userId)
		->first();
		return view('admin.survey.viewcompany',["userdata" => $userdata]);

	}

	public function edituserpost(Request $request)

	{
		$userId = $request->input('user_id');
		 $user_id =  base64_decode($userId);
		$validator = Validator::make($request->all(), [
			'type' => 'required',
			'first_name' => 'required|max:50',
			'last_name' => 'required|max:50',
			'email' => 'required|email|unique:users,email,'.$user_id,
			'mobile' => 'required|unique:users,mobile,'.$user_id,
			// 'profile_img' => 'max:2048|mimes:jpg,jpeg,gif,png',

			 ]);

			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$user = User::find($user_id);
				$type =  $request->input('type');
				$first_name =  $request->input('first_name');
				$last_name =  $request->input('last_name');
				$email =  $request->input('email');
				$mobile =  $request->input('mobile');
				$password =  $request->input('password');
				$company =  $request->input('company');

				$user->type = $type;
				$user->first_name = $first_name;
				$user->last_name = $last_name;
				$user->email = $email;
				$user->mobile = $mobile;
				$user->company = $company;
				if(!empty($password))
				{
					$user->password =md5($password);
				}
				
				$user->save();
				
				return response()->json(['success'=>true ,'message'=>'<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
                User Edit successfully
              </div>']);
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
			return response()->json(['success'=>true ,'message'=>'<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
                User Deactive successfully
              </div>']);

		}else
		{
			$userdata->status = "1";
			$userdata->save();
			return response()->json(['success'=>true ,'message'=>'<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
                User Active successfully
			  </div>']);
			  
		}
		
	}

	public function changesurveryorstatus($id, $status_id)
	{
		$survey_id = base64_decode($id);
		return view('admin.survey.status', ['survey_id'=>$survey_id,'status_id'=>$status_id]);
	} 

	public function changesurveryorstatuspost(Request $request)
	{
		$survey_id = $request->input('survey_id');
		//$status_id = $request->input('status_id');
		$validator = Validator::make($request->all(), [
			'status' => 'required',
			
			 ]);

			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$survey = Survey::find($survey_id);
				$status_id =  $request->input('status_id');
				$status =  $request->input('status');				
				$survey->last_status = $status_id;
				$survey->status = $status;
				$survey->save();
				echo json_encode(array('class'=>'success','message'=>'Survey Update successfully.'));die;

				
			}	

	}
		public function usersdelete($id)

	   {

		$id = base64_decode($id);

		$user = User::find($id);

		$user->delete();

		 Session::put('msg', '<strong class="alert alert-success"> User successfully deleted.</strong>');

		 return redirect('/admin/user-management/users');	

	   }
	   public function chatForm($survey_id,$sender_id,$receiver_id)
	   {			
		    return view('admin.survey.chat',['sender_id'=>$sender_id,'receiver_id'=>$receiver_id,'survey_id'=>$survey_id]);
	   }	
}

?>