<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\User;
use App\Models\Notification;
use App\Uniqcode;
use App\Models\Admin;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;

use Mail;
class NotificationController extends Controller {
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
		$end_date=$request->input('end_date');

		$noti_data = Notification::select('notification.*','users.type','users.email AS useremail')
		->leftJoin('users', 'notification.user_id', '=', 'users.id')
		->orderBy("created_at","DESC");

		if(!empty($request->input('email')))
		{ 
			$email = $request->input('email');
			$noti_data =$noti_data->where('users.email','=',$email );
		
		}
		if(!empty($request->input('user_type')))
		{ 
			$user_type = $request->input('user_type');
			$noti_data =$noti_data->where('users.type','=',$user_type );
		
		}
		
		//echo '<pre>'; print_r($noti_data); exit;

		if ($start_date!="" && $end_date!="") {


            $start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$end_date = date('Y-m-d H:i:s', strtotime($end_date . ' 23:59:59'));
			$noti_data = $noti_data->whereBetween('notification.created_at', [$start_date, $end_date]);
            

        } else if ($start_date!="" && $end_date=="") {

			$start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$noti_data =$noti_data->where('notification.created_at',">=",$start_date);

           
        } else if ($start_date=="" && $end_date!="") {

            $end_date = date('Y-m-d H:i:s', strtotime($end_date));
			$noti_data = $noti_data->where('notification.created_at',"<=",$end_date);

		}
		$noti_data =$noti_data->paginate(10);
		if ($request->ajax()) 
		{
			return view('admin.notification.search', compact('noti_data'));  
		}
		
		$userdata = User::where('type','!=','3')->orderBy("created_at","DESC")->get();
		$user_box=array(''=>'Select User');
		foreach($userdata as $key=>$value){
			$user_box[$value->id]=sprintf('%s %s',$value->first_name,$value->last_name);
		}
		$admindata = Admin::find(session('admin')['id']);

		// $noti_data = Notification::select('notification.noti_type')->orderBy("noti_type","Asc")->groupBy('noti_type')->get();

		// 			$noti_data_box=array(''=>"Select Type");
		// 				foreach ($noti_data as $key => $value) {
		// 					$noti_data_box[$value->noti_type]=$value->noti_type;
		// 				}


		 return view('admin.notification.show', compact('noti_data','user_box','admindata'));
   	
	}
	
	public function viewnotification($id)

	{
		$id = base64_decode($id);
		$noti_data = Notification::select('notification.*',
		DB::raw('CONCAT(users.first_name, "  ", users.last_name) AS username'),'countries.name as country_name')
		->where('notification.id','=',$id )
		->leftJoin('users', 'notification.user_id', '=', 'users.id')
		->leftJoin('countries', 'notification.country_id', '=', 'countries.id')
		->orderBy("created_at","DESC")->first();
	//	dd($noti_data);
		return view('admin.notification.view',["noti_data" => $noti_data]);

	}
	public function addnotification()
	{    
	   
		return view('admin.notification.add');
    } 

	public function addnotificationpost(Request $request)
	{
		
			$validator = Validator::make($request->all(), [
			'title' => 'required|max:50',
			'message' => 'required|max:50',
			'noti_type' => 'required',
			'user_type' => 'required|array',
			'country' => 'required|array',
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{     
				$type =  array_filter($request->input('user_type'),'is_numeric');
				$country_id =  array_filter($request->input('country','is_numeric'));

		        $noti_type =  $request->input('noti_type');
				$title =  $request->input('title');
				$message =  $request->input('message');
				$file_data = $request->file('file');
				$imageName="";
				if(isset($file_data))
						{
							$imageName = time().$file_data->getClientOriginalName();
							$file_data->move(public_path().'/media/notification', $imageName);
							$imageName =str_replace(" ", "", $imageName);
							
						}
						
				$fcmUrl = 'https://fcm.googleapis.com/fcm/send';
		       
				$userstoken = User::where('status','1')
				->select('id','device_id');
				if(!empty($type)){
					$userstoken=$userstoken->whereIn('type',$type);
				}
				if(!empty($country_id)){
					$userstoken=$userstoken->whereIn('country_id',$country_id);
				}
			
				
				$userstoken=$userstoken->get();
				//dd($userstoken);
				//$tokenList = $userstoken;
				$tokenList = array();
				foreach($userstoken as $token)
				{   if(!empty($token)){
					$tokenList[] = $token->device_id;
					}
				}
				//dd($tokenList);
			//	echo '<pre>'; print_r($tokenList); die;
			     $notification = [
					'title' => $title,
					'body' => $message,
					"image"=>$request->root().'/media/notification/'.$imageName,
					//'icon' => $imageUrl,
					'sound' => 'mySound',
				];
				//dd($notification);
				$extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

				$fcmNotification = [
					'registration_ids' => $tokenList, //multple token array
					//'to'        => $token, //single token
					'notification' => $notification,
					//'data' => $extraNotificationData
				];
		
				$headers = ['Authorization:key=AAAAyVCwPdo:APA91bFYeZCzN1qjjA2pVenLoPLXKm92hQc1ExhkPxDXh-w1O_a8pO0xwSh3RiibB75Zw5Z9SxmRFmckwifj_IyK30geYtd95VAzXsTHEbNnWwmpm6gUeX_EY7I_PiYJedh0YtGkyTtn',
					'Content-Type: application/json'
				];

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,$fcmUrl);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
				$result = curl_exec($ch);
				curl_close($ch);
				$result = json_decode($result, true);
				//dd($result);
				if($result['success']){
					//\Session::flash('success', 'Push Notification Send Successfully.');
					 $users =  User::where('status','1')
					 ->select('id','country_id');
					 if(!empty($type)){
						$users=$users->whereIn('type',$type);
					}
					if(!empty($country_id)){
						$users=$users->whereIn('country_id',$country_id);
					}
					$users=$users->get();
					 //echo '<pre>'; print_r($users); die;
					 foreach($users as $user)
					 {   
						$notification = new Notification();
						$notification->user_id = $user->id;
						$notification->noti_type = $noti_type;
						$notification->user_type = $user->type;
						$notification->title = $title;
						$notification->notification = $message;
						if($user->country_id!=""){
							$notification->country_id = $user->country_id;

						}

						$notification->is_read = 0;
						if(isset($file_data))
						{
							$notification->file = $imageName;
						}
						$notification->save();
					 }
					
					
					 echo json_encode(array('class'=>'success','message'=>'Push Notification Send Successfully.'));die;
				}else{
					//echo $result['results'][0]['error']; die;
					//\Session::flash('error', 'Push Notification Not Send Successfully, '.$result['results'][0]['error'].' ,Please try again.');
					echo json_encode(array('class'=>'success','message'=>'Push Notification Not Send Successfully'));die;
				}
                    
			}
		
	} 

	
	
}

?>