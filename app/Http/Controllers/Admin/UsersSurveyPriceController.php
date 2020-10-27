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
use App\Models\UsersSurveyPrice;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;
class UsersSurveyPriceController extends Controller {
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
	   
		$start_date= $request->input('start_date');
		$end_date= $request->input('end_date');
		$surveyor_email= $request->input('surveyor_email');
		$status=$request->input('status');
		$srveryor_category=$request->input('srveryor_category');
		// $surveydata = Survey::select('survey.id','survey.port_id','survey.survey_type_id',
		// 'survey_users.surveyors_id','survey_type.name as survey_type_name','users_survey_price.survey_price'
		// ,'users_port.cost','users.email as user_email','port.port as port_name')
		// ->leftJoin('users_survey_price', 'survey.survey_type_id', '=', 'users_survey_price.survey_type_id')
		// ->leftJoin('survey_users', 'survey.id', '=', 'survey_users.survey_id')
		// ->leftJoin('users_port', 'users_port.port_id', '=', 'survey.port_id')	
		// ->leftJoin('users', 'survey_users.surveyors_id', '=', 'users.id')	
		// ->leftJoin('survey_type', 'survey.survey_type_id', '=', 'survey_type.id')
		// ->leftJoin('port', 'users_port.port_id', '=', 'port.id')	

		// ->where('survey_users.status','upcoming');

		$surveydata = UsersSurveyPrice::select('users_survey_price.*','users.email as user_email',
		'countries.name as country_name','survey_type.name as survey_type_name')
			->leftJoin('users', 'users_survey_price.user_id', '=', 'users.id')	
			->leftJoin('countries', 'users_survey_price.country_id', '=', 'countries.id')
			->leftJoin('survey_type', 'users_survey_price.survey_type_id', '=', 'survey_type.id')
			->orderBy("users_survey_price.created_at","DESC");
			
			
		
		if($surveyor_email!="")
		{
			$surveydata = $surveydata->where('users.email','LIKE',"%$surveyor_email%");
		}
		
		
		
		if($status!="")
		{
			$surveydata = $surveydata->where('users_survey_price.status',$status);
		}
		
		
		
		if($srveryor_category!="")
		{
			$surveydata = $surveydata->where('users_survey_price.survey_type_id',$srveryor_category);
		}
	 if ($start_date!="" && $end_date!="") {

            $_start_date = date('Y-m-d H:i:s', strtotime($request->input('start_date')));
			$_end_date = date('Y-m-d H:i:s', strtotime($request->input('end_date') . ' 23:59:59'));
			$surveydata = $surveydata->whereBetween('users_survey_price.created_at', [$_start_date, $_end_date]);
            

        }  else if ($start_date!="" && $end_date=="") {

			$_start_date = date('Y-m-d H:i:s', strtotime($request->input('start_date')));
			$surveydata = $surveydata->where('users_survey_price.created_at',">=",$_start_date)->orderBy("created_at","DESC");

           
        }else if ($start_date=="" && $end_date!="") {

            $end_date = date('Y-m-d H:i:s', strtotime( $request->input('end_date')));
			$surveydata = $surveydata->where('users_survey_price.created_at',"<=",$end_date)->orderBy("created_at","DESC");

        }
		
		$surveydata = $surveydata->paginate(20);
		
		if ($request->ajax()) 
		{
			return view('admin.users-survey-price.search', compact('surveydata'));  
        }
		$admindata = Admin::find(session('admin')['id']);
		
		//$surveyCategoryList		=	DB::table('survey_category')->orderBy('name','ASC')->pluck('name','id')->toArray();
        
        return view('admin.users-survey-price.show', compact('surveydata','admindata'));

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
		$surveydata = Survey::select("survey.*",'users.first_name as username','users.company as company','users.type as type','users.email as useremail','users.id as user_id','surveyor.first_name as surveyorname','surveyor.company as surveyorcompany','surveyor.id as surveyor_id','vessels.name as shipname','survey_type.name as surveycatname','port.port as portname','port.country as portcountry')
			->where('survey.id',$surveyId)
			->leftJoin('users', 'survey.user_id', '=', 'users.id')	
			->leftJoin('vessels', 'survey.ship_id', '=', 'vessels.id')
			->leftJoin('port', 'survey.port', '=', 'port.id')
			->leftJoin('survey_type', 'survey.survey_type_id', '=', 'survey_type.id')
			->leftJoin('users as surveyor', 'survey.surveyors_id', '=', 'surveyor.id')	
			->orderBy("survey.created_at","DESC")->first();
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
	
}

?>