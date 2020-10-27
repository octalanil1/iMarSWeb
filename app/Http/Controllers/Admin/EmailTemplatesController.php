<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\Models\Country;
use App\Models\Content;
use App\Models\Emailtemplates;
use App\Uniqcode;
use App\Models\Admin;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;
class EmailTemplatesController extends Controller {
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
        $title = $request->input('title');
		$email_templates_data = Emailtemplates::orderBy("created_at","DESC");

		if($title!="")
		{ $email_templates_data =$email_templates_data->where('title','like',"%$title%"); }
		$email_templates_data =$email_templates_data->paginate(10);
        if ($request->ajax()) 
		{
			return view('admin.email-templates.search', compact('email_templates_data'));  
        }
		$admindata = Admin::find(session('admin')['id']);

		return view('admin.email-templates.show', compact('email_templates_data','admindata'));
		
	}
	public function searchemailtemplates(Request $request)

	{
		$title = $request->input('title');
		$email_templates_data = Emailtemplates::orderBy("created_at","DESC");

		if($title!="")
		{ $email_templates_data =$email_templates_data->where('title','like',"%$title%"); }
		$email_templates_data =$email_templates_data->paginate(10);
		 $src = "";
		 return view('admin.email-templates.search',['email_templates_data'=>$email_templates_data,'src'=>$src ]);	
	}
	public function addemailtemplates(Request $request)
	{
		return view('admin.email-templates.add');
	} 
	public function addemailtemplatespost(Request $request)
	{
			$validator = Validator::make($request->all(), [
			'title' => 'required',
			'description' => 'required',
			'subject' => 'required',

			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$Emailtemplates = new Emailtemplates();				
				$title =  $request->input('title');
				$description =  $request->input('description');	
				$subject =  $request->input('subject');	

				$slug = strtolower(preg_replace('/\s+/', '-', $title));	
				$slug_check = Emailtemplates::where('slug', $slug)->first();
				if(!empty($slug_check)){
					echo json_encode(array('class'=>'success','message'=>'This Page URL ALready Exist..'));die;

					
				}else{
					$Emailtemplates->slug = $slug;
				}
				
				$Emailtemplates->title = $title;
				$Emailtemplates->subject = $subject;

				$Emailtemplates->description = $description;
                $Emailtemplates->save();
				echo json_encode(array('class'=>'success','message'=>'Email Added successfully'));die;

			
			}
		
	} 
	
	public function editemailtemplates($id)

	{
		$id = base64_decode($id);
		$email_templates_data = Emailtemplates::find($id);
		return view('admin.email-templates.edit',["email_templates_data" => $email_templates_data]);

	}
	

	public function editemailtemplatespost(Request $request)

	{
		$id = $request->input('id');
		 $id =  base64_decode($id);
		$validator = Validator::make($request->all(), [
			
			'title' => 'required',
			'description' => 'required',
			'subject' => 'required',
			
			 ]);

			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$Emailtemplates = Emailtemplates::find($id);
			
				$title =  $request->input('title');
				$description =  $request->input('description');
				$subject =  $request->input('subject');
				$slug = strtolower(preg_replace('/\s+/', '-', $title));	
				$slug_check = Content::where('slug', $slug)->where('id','!=',$id)->first();
				if(!empty($slug_check)){
					echo json_encode(array('class'=>'success','message'=>'This Page URL ALready Exist'));die;

				
				}else{
					$Emailtemplates->slug = $slug;
				}

				$Emailtemplates->title = $title;
				$Emailtemplates->subject = $subject;
				$Emailtemplates->description = $description;
                $Emailtemplates->save();
				echo json_encode(array('class'=>'success','message'=>'Email Templates Edit successfully'));die;

				
			}	

	}

	public function contentremove(Request $request)
	{
         $id = base64_decode($request->input('id'));
        $Content = Content::find($id);
        $Content->delete();
		echo json_encode(array('class'=>'success','message'=>'Content Remove Successfully'));die;

      

	   }
	
}

?>