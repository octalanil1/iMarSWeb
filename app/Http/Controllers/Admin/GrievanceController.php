<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\User;
use App\Models\Company;
use App\Models\Agent;
use App\Models\Admin;
use App\Models\Login;
use App\Models\Grievance;
use App\Models\GrievanceReply;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;
class GrievanceController extends Controller {
 
	public function index(Request $request)
	{


	$rowsize =  $request->input('rowsize');
	if(isset($rowsize))
	{$row_per_page= $rowsize ;}
	else{$row_per_page= 50 ; }

	$pagedrop=array("50"=>'50',"100"=>'100','200'=>'200','250'=>'250');
	$pagedrop_data=array();
	foreach($pagedrop as $key=>$cdata)
	{
	$pagedrop_data[$key]=ucfirst($cdata);
	}
	if(isset($_GET['q']) && $_GET['q']!=""){ 
	$src = $_GET['q'];
	$userdata = Company::where('company_name',"like","%$src%")->orderBy("created_at","DESC")->paginate($row_per_page);
		
	$grievancedata = Grievance::where('grievance.types',"like","%$src%")->orWhere('company.company_name',"like","%$src%")->orderBy("grievance.created_at","DESC")
    ->leftJoin('company', 'grievance.company_id', '=', 'company.id')			  
    ->select('grievance.id','grievance.bo_id','company.company_name','grievance.types','grievance.title','grievance.status','grievance.created_at')
->paginate($row_per_page);		
	}else{
	$grievancedata = Grievance::orderBy("grievance.created_at","DESC")
    ->leftJoin('company', 'grievance.company_id', '=', 'company.id')			  
    ->select('grievance.id','grievance.bo_id','company.company_name','grievance.types','grievance.title','grievance.status','grievance.created_at')
->paginate($row_per_page);	
	$src = "";
	}
     $admindata = Admin::find(session('admin')['id']);
	return view('admin.grievance.show',['admindata'=>$admindata,'grievancedata'=>$grievancedata,'src'=>$src,'pagedrop_data'=>$pagedrop_data,'rowsize '=>$rowsize ]);	

	}

    public function status($id)
	     {
		$id = base64_decode($id);
		$grievance = Grievance::find($id);
		if($grievance->status=="1" || $grievance->status=="0"){
			$grievance->status = "2";
			}else{
				$grievance->status = "0";
				}
				$grievance->save();
		 Session::put('msg', '<strong class="alert alert-success">Grievance Status successfully changed.</strong>');
		 return redirect('/admin/grievance');	
	    }

		public function reply($id)
	   {
		$id = base64_decode($id);
	    $grievances = Grievance::whereId($id)->first();
		$grievancesrep =  GrievanceReply::whereG_id($id)->orderBy("id","DESC")->leftJoin('company', 'grievance_reply.c_id', '=', 'company.id')->select('grievance_reply.id','grievance_reply.attached','company.company_name','grievance_reply.description','grievance_reply.created_at')->get();
$ext = pathinfo(public_path().$grievances->attached, PATHINFO_EXTENSION);
return view('admin.grievance.reply',['grievances'=>$grievances,"ext"=>$ext,'grievancesrep'=>$grievancesrep]);

	   }
	public function replypost(Request $request)
	{
	$id =  $request->input('id');		
$validator = Validator::make($request->all(), [
'id' => 'required',
'description' => 'required',
'attached' => 'max:2048|mimes:jpg,jpeg,gif,png,pdf',
]);
if ($validator->fails()) {
return redirect('/admin/grievance/reply/'.base64_encode($id))
->withErrors($validator)
->withInput();
}else{
$description =  $request->input('description');
$attached = $request->file('attached');
$grievancesrep =  new GrievanceReply;
if(isset($attached))
{
$attachedName = time().$attached->getClientOriginalName();
$attached->move(public_path().'/media/grievance', $attachedName);
$grievancesrep->attached = $attachedName;
}
$grievancesrep->g_id = $id;
$grievancesrep->c_id = 0;
$grievancesrep->description = $description;	
$grievancesrep->save();
$grievances = Grievance::find($id);	
$grievances->status = "1";	
$grievances->save();	
Session::put('msg', '<strong class="alert alert-success"> Reply successfully sent.</strong>');
return redirect('/admin/grievance/reply/'.base64_encode($id));
}	
	}
	
}

?>