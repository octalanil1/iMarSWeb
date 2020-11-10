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
use App\Models\Port;
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

	{   $user_id=$request->input('user_id');
		$port=$request->input('port');
		$status=$request->input('status');
		$start_date= $request->input('start_date');
		$end_date=$request->input('end_date');

		$surveydata = Survey::select("survey.*",'users.first_name as username','surveyor.first_name as surveyorname','ship.name as shipname','survey_category.name as surveycatname','port.port as portname')
			->leftJoin('users', 'survey.user_id', '=', 'users.id')	
			->leftJoin('ship', 'survey.ship_id', '=', 'ship.id')
			->leftJoin('port', 'survey.port', '=', 'port.id')
			->leftJoin('survey_category', 'survey.survey_cat_id', '=', 'survey_category.id')
			->leftJoin('users as surveyor', 'survey.surveyors_id', '=', 'surveyor.id')	
			->orderBy("survey.created_at","DESC");
			

		if($user_id!="")
		{
			$surveydata = $surveydata->where('survey.user_id',"=",$user_id);
		}
		if($port!="")
		{
			$surveydata = $surveydata->where('survey.port',"=",$port);
		}
		if($status!="")
		{
			$surveydata = $surveydata->where('survey.status',$status);
		}
	 if ($start_date!="" && $end_date!="") {

            $_start_date = date('Y-m-d H:i:s', strtotime($request->input('start_date')));
			$_end_date = date('Y-m-d H:i:s', strtotime($request->input('end_date') . ' 23:59:59'));
			$surveydata = $surveydata->whereBetween('survey.created_at', [$_start_date, $_end_date]);
            

        }  else if ($start_date!="" && $end_date=="") {

			$_start_date = date('Y-m-d H:i:s', strtotime($request->input('start_date')));
			$surveydata = $surveydata->where('survey.created_at',">=",$_start_date)->orderBy("created_at","DESC");

           
        }else if ($start_date=="" && $end_date!="") {

            $end_date = date('Y-m-d H:i:s', strtotime( $request->input('end_date')));
			$surveydata = $surveydata->where('survey.created_at',"<=",$end_date)->orderBy("created_at","DESC");

        }
		
		$surveydata = $surveydata->paginate(20);
		$userdata = User::where('type','0')->where('is_admin','0')->orderBy("created_at","DESC")->get();
		$user_box=array(''=>'Select User');
		foreach($userdata as $key=>$value){
			$user_box[$value->id]=sprintf('%s %s',$value->first_name,$value->last_name);
		}

		$portdata = Port::orderBy("port","DESC")->get();
		$port_box=array(''=>'Select Port');
		foreach($portdata as $key=>$value){
			$port_box[$value->id]=$value->port;
		}

		if ($request->ajax()) 
		{
			return view('admin.survey.search', compact('surveydata'));  
        }
		$admindata = Admin::find(session('admin')['id']);

        return view('admin.survey.show', compact('surveydata','user_box','admindata','port_box'));

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
		$surveydata = Survey::select("survey.*",'users.first_name as username','surveyor.first_name as surveyorname','ship.name as shipname','survey_category.name as surveycatname','port.port as portname')
			->where('survey.id',$surveyId)
			->leftJoin('users', 'survey.user_id', '=', 'users.id')	
			->leftJoin('ship', 'survey.ship_id', '=', 'ship.id')
			->leftJoin('port', 'survey.port', '=', 'port.id')
			->leftJoin('survey_category', 'survey.survey_cat_id', '=', 'survey_category.id')
			->leftJoin('users as surveyor', 'survey.surveyors_id', '=', 'surveyor.id')	
			->orderBy("survey.created_at","DESC")->first();
		return view('admin.survey.view',["surveydata" => $surveydata]);

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