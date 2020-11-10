<?php 

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\Models\Surveycategory;
use App\Uniqcode;
use App\Models\Admin;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;

class SurveyCategoryController extends Controller {
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
		$catedata = Surveycategory::orderBy("created_at","DESC");
		if($name!=""){ $catedata = $catedata->where('name','like',"%$name%");}
		$catedata=$catedata->paginate(20);

		if ($request->ajax()) 
		{
			return view('admin.survey-category.search', compact('catedata'));  
        }
		$admindata = Admin::find(session('admin')['id']);
        return view('admin.survey-category.show', compact('catedata','admindata'));

	}

	

	public function addsurveycategory(Request $request)

	{
		return view('admin.survey-category.add');
	} 

	public function addsurveycategorypost(Request $request)

	{

			$validator = Validator::make($request->all(), [
			'name' => 'required',
			]);

			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);

			}else
			{

				$category = new Surveycategory();				
				$name =  $request->input('name');	
				$category->name = $name;
                $category->save();
				echo json_encode(array('class'=>'success','message'=>' Category Added successfully.'));die;
			}

	} 

	

	public function editsurveycategory($id)
	{

		$id = base64_decode($id);
		$catdata = Surveycategory::find($id);
		return view('admin.survey-category.edit',["catdata" => $catdata]);

	}

	



	public function editsurveycategorypost(Request $request)
	{

		$id = $request->input('id');
		 $id =  base64_decode($id);
		$validator = Validator::make($request->all(), ['name' => 'required', ]);
			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$Surveycategory = Surveycategory::find($id);	
                $name =  $request->input('name');
                $Surveycategory->name = $name;
                $Surveycategory->save();
				echo json_encode(array('class'=>'success','message'=>' Category Edit successfully.'));die;
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



	public function categoryremove(Request $request)
	{
         $id = base64_decode($request->input('id'));
        $Surveycategory = Surveycategory::find($id);
        $Surveycategory->delete();
		echo json_encode(array('class'=>'success','message'=>' Category Remove Successfully.'));die;
	   }
}



?>