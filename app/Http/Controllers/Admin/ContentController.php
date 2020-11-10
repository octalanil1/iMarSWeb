<?php 

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\Models\Country;
use App\Models\Content;
use App\Uniqcode;
use App\Models\Admin;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;

class ContentController extends Controller {
	public function index(Request $request)
	{
		$title=$request->input('title');

		$content_data = Content::orderBy("created_at","DESC");

		if($title!=""){ 
			$content_data =$content_data->where('title','like',"%$title%");
		}
		$content_data =$content_data->paginate(10);

		if ($request->ajax()) 
		{
			return view('admin.content.search', compact('content_data'));  
        }
		$admindata = Admin::find(session('admin')['id']);
		return view('admin.content.show', compact('content_data','admindata'));
	}

	public   function cleanStr($string){
		// Replaces all spaces with hyphens.
		$string = str_replace(' ', '-', $string);
	
		// Removes special chars.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
		// Replaces multiple hyphens with single one.
		$string = preg_replace('/-+/', '-', $string);
		
		return $string;
	}

	public function addcontent(Request $request)
	{return view('admin.content.add');} 
	public function addcontentpost(Request $request)
	{
			$validator = Validator::make($request->all(), ['user_type' => 'required','title' => 'required|max:50','description' => 'required',]);
			if ($validator->fails()) 
			{
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$content = new Content();				
				$title =  $request->input('title');
				$description =  $request->input('description');	
				$user_type =  $request->input('user_type');	
				$slug = strtolower($this->cleanStr($title));	
			
				$slug_check = Content::where('slug', $slug)->where('user', $user_type)->first();

				if(!empty($slug_check)){
					echo json_encode(array('class'=>'danger','message'=>' This Page URL ALready Exist..'));die;

				}else{

					$content->slug = $slug;
				}
				$content->title = $title;
				$content->user = $user_type;
				$content->description = $description;
                $content->save();
				echo json_encode(array('class'=>'success','message'=>' Content Added successfully.'));die;
			}

	} 

	

	public function editcontent($id)
	{
		$id = base64_decode($id);
		$content_data = Content::find($id);
		return view('admin.content.edit',["content_data" => $content_data]);

	}


	public function editcontentpost(Request $request)
	{
		$id = $request->input('id');
		 $id =  base64_decode($id);
		$validator = Validator::make($request->all(), ['user_type' => 'required','title' => 'required|max:50','description' => 'required',]);
			  if ($validator->fails()) 

			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$content = Content::find($id);
				$title =  $request->input('title');
				$description =  $request->input('description');
				$user_type =  $request->input('user_type');	

				 $slug = str_slug($title,'-');

				$slug_check = Content::where('slug', $slug)->where('user', $user_type)->where('id','!=',$id)->first();

				if(!empty($slug_check)){
					echo json_encode(array('class'=>'danger','message'=>' This Page URL ALready Exist.
					.'));die;

				}else{

					$content->title = $title;
					$content->slug = $slug;

					$content->user = $user_type;
					$content->description = $description;
					$content->save();
					echo json_encode(array('class'=>'success','message'=>'  Content Edit successfully.'));die;
				}
				
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
			echo json_encode(array('class'=>'success','message'=>' User Deactive successfully.'));die;

		}else
		{
			$userdata->status = "1";
			$userdata->save();
			echo json_encode(array('class'=>'success','message'=>'  User Active successfully.'));die;
		}
	}



	public function contentremove(Request $request)
	{
         $id = base64_decode($request->input('id'));
        $Content = Content::find($id);
        $Content->delete();
		echo json_encode(array('class'=>'success','message'=>'Content Remove Successfully.'));die;
	   }

	

}



?>