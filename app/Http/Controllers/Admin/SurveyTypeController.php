<?php 

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\Models\Surveytype;
use App\Uniqcode;
use App\Models\Admin;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;

class SurveyTypeController extends Controller {
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
		$name=$request->input('name');

		$catedata = Surveytype::orderBy("name","ASc");
		if($name!=""){ $catedata = $catedata->where('name','like',"%$name%")->Orwhere('code','like',"%$name%");}

		$catedata=$catedata->paginate(20);

		if ($request->ajax()) 
		{
			return view('admin.survey-type.search', compact('catedata'));  
        }
		$admindata = Admin::find(session('admin')['id']);
        return view('admin.survey-type.show', compact('catedata','admindata'));

	}

	

	public function addsurveycategory(Request $request)

	{
		return view('admin.survey-type.add');
	} 

	public function addsurveycategorypost(Request $request)

	{

			$validator = Validator::make($request->all(), [
			'name' => 'required',
			'code' => 'required',
			//'price' => 'required|numeric',
			'status' => 'required',
			]);

			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);

			}else
			{

				$Surveytype = new Surveytype();				
				$name =  $request->input('name');
				$type =  $request->input('type');
					if($type!=""){
						$Surveytype->type = $type;
					}

				$Surveytype->name = $name;
				$Surveytype->code = $request->input('code');
				//$type->price = $request->input('price');
				$Surveytype->status = $request->input('status');
                $Surveytype->save();
				echo json_encode(array('class'=>'success','message'=>' Survey type Added successfully.'));die;
			}

	} 

	

	public function editsurveycategory($id)
	{

		$id = base64_decode($id);
		$catdata = Surveytype::find($id);
		return view('admin.survey-type.edit',["catdata" => $catdata]);

	}

	



	public function editsurveycategorypost(Request $request)
	{

		$id = $request->input('id');
		 $id =  base64_decode($id);
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'code' => 'required',
			// 'price' => 'required|numeric',
			 'status' => 'required',]);
			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$Surveytype = Surveytype::find($id);	
				$name =  $request->input('name');
				$type =  $request->input('type');
					if($type!=""){
						$Surveytype->type = $type;
					}
				$Surveytype->name = $name;
				$Surveytype->code = $request->input('code');
				//$Surveytype->price = $request->input('price');
				$Surveytype->status = $request->input('status');;
                $Surveytype->save();
				echo json_encode(array('class'=>'success','message'=>' Survey Type Edit successfully.'));die;
			}	
	}

public function surveystatus(Request $request)
	{
		 $id = base64_decode($request->input('id'));
		$userdata = Surveytype::find($id);
		
		if($userdata->status=="1")
		{
			$userdata->status = "0";
			$userdata->save();
			echo json_encode(array('class'=>'success','message'=>'  Survey type Deactive successfully.'));die;
		

		}else
		{
			
			$userdata->status = "1";
			$userdata->save();
			echo json_encode(array('class'=>'success','message'=>'  Survey type Active successfully.'));die;
		
		}
		
	}


	public function categoryremove(Request $request)
	{
         $id = base64_decode($request->input('id'));
        $Surveycategory = Surveycategory::find($id);
        $Surveycategory->delete();
		echo json_encode(array('class'=>'success','message'=>' Category Remove Successfully.'));die;
	   }
}



?>