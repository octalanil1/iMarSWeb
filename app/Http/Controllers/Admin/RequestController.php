<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\Models\Emailtemplates;

use App\User;
use App\Uniqcode;
use App\Models\Admin;
use App\Models\Survey;
use App\Models\Paymentrequest;
use App\Models\Disputerequest;
use App\Models\Earning;
use App\Models\Bankdetail;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;
class RequestController extends Controller {
  private $admin;
  public function __construct()
    {
		if (session('admin')['id'])
		{
			$admindata = Admin::find(session('admin')['id']);
			$this->user = $admindata;
		}
    }



	public function disputerequest(Request $request)
	{  
		$survey_number=$request->input('survey_number');
		$status=$request->input('status');
		$start_date= $request->input('start_date');
		$end_date=$request->input('end_date');
		$dispute_request_data = Disputerequest::select("dispute_request.*",'users.email as useremail','survey.survey_number' )
		->leftJoin('users', 'dispute_request.requested_id', '=', 'users.id')	
		->leftJoin('survey', 'survey.id', '=', 'dispute_request.survey_id')	
		->orderBy("dispute_request.created_at","DESC");
		if($survey_number!="")
		{
			$dispute_request_data = $dispute_request_data->where('survey.survey_number',"=",$survey_number);

		}
		if($status!="")
		{
			$dispute_request_data = $dispute_request_data->where('dispute_request.status',"=",$status);

		}

		 if ($start_date!="" && $end_date!="") {


            $start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$end_date = date('Y-m-d H:i:s', strtotime($end_date . ' 23:59:59'));
			$dispute_request_data = $dispute_request_data->whereBetween('dispute_request.created_at', [$start_date, $end_date]);
            

        } else if ($start_date!="" && $end_date=="") {

			$start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$dispute_request_data =$dispute_request_data->where('dispute_request.created_at',">=",$start_date);

           
        } else if ($start_date=="" && $end_date!="") {

            $end_date = date('Y-m-d H:i:s', strtotime($end_date));
			$dispute_request_data = $dispute_request_data->where('dispute_request.created_at',"<=",$end_date);

        }
		$dispute_request_data = $dispute_request_data->paginate(20);
		
		if ($request->ajax()) 
		{
			return view('admin.dispute-request.search', compact('dispute_request_data'));  
        }
		$admindata = Admin::find(session('admin')['id']);
        return view('admin.dispute-request.show', compact('dispute_request_data','admindata'));
	}

	public function editdisputerequest($id)
	{

		$id = base64_decode($id);
		$Disputerequest = Disputerequest::find($id);
		return view('admin.dispute-request.edit',["Disputerequest" => $Disputerequest]);
	}

	



	public function editdisputerequestpost(Request $request)
	{

		$id = $request->input('id');
		 $id =  base64_decode($id);
		$validator = Validator::make($request->all(), ['status' => 'required']);
		if ($validator->fails()) 
		{return response()->json(['success'=>false ,'errors'=>$validator->errors()]);}
		else
		{

			$Disputerequest = Disputerequest::find($id);
			
			$status =  $request->input('status');
			
			$Disputerequest->status = $status;
			$Disputerequest->save();

			echo json_encode(array('class'=>'success','message'=>'Dispute Request Edit successfully.'));die;
		}	
	}
	public function disputerequestaction(Request $request)
	{
		 $id = base64_decode($request->input('id'));
		 $action = $request->input('action');
		 $dipsuterequest = Disputerequest::find($id);	

		if(!empty($dipsuterequest))
		{

			if($action=='1')
			{
				$dipsuterequest->status =$action;
				$dipsuterequest->save();
				echo json_encode(array('class'=>'success','message'=>' Request Accept successfully.'));die;


				
			}else{

				$dipsuterequest->status =$action;
				$dipsuterequest->save();
				echo json_encode(array('class'=>'success','message'=>' Request Decline successfully.'));die;

				
			}

		}

	}
	public function viewdisputerequest($id)

	{
		$id = base64_decode($id);



		$dispute_request_data = Disputerequest::select("dispute_request.*",'users.email as useremail','survey.survey_number' )
		->leftJoin('users', 'dispute_request.requested_id', '=', 'users.id')
		->leftJoin('survey', 'survey.id', '=', 'dispute_request.survey_id')	

		->where('dispute_request.id',$id)->first();
		
		return view('admin.dispute-request.view',["dispute_request_data" => $dispute_request_data]);

	}
	public function paymentrequest(Request $request)
	{  
		 $surveyor_email=$request->input('surveyor_email');
		 $country_id=$request->input('country_id');
		 $payment_method=$request->input('payment_method');
		 $status=$request->input('status');


		$payment_request_data = Paymentrequest::select("payment_request.*",
		'users.email AS surveyor_email','c.name as country_name')
		->leftJoin('users', 'payment_request.surveyor_id', '=', 'users.id')	
		->leftJoin('countries as c', 'users.country_id', '=', 'c.id')

		->orderBy("payment_request.created_at","DESC");


		if($status!="")
		{
			$payment_request_data = $payment_request_data->where('payment_request.status',"=",$status);

		}
		
		if($surveyor_email!="")
		{
			$payment_request_data = $payment_request_data->where('users.email',"=",$surveyor_email);

		}
		if($country_id!="")
		{
			$payment_request_data = $payment_request_data->where('payment_request.country_id',"=",$country_id);

		}
		if($payment_method!="")
		{
			$payment_request_data = $payment_request_data->where('payment_request.payment_method',"=",$payment_method);

		}
		$payment_request_data = $payment_request_data->paginate(20);

		if ($request->ajax()) 
		{
			return view('admin.payment-request.search', compact('payment_request_data'));  
        }
		$admindata = Admin::find(session('admin')['id']);

        return view('admin.payment-request.show', compact('payment_request_data','admindata'));


	}
	public function viewpaymentrequest($id)

	{
		$id = base64_decode($id);
		$payment_request_data = Paymentrequest::select("payment_request.*",DB::raw('CONCAT(users.first_name, "  ", users.last_name) AS username'))
		->leftJoin('users', 'payment_request.surveyor_id', '=', 'users.id')		
		->where('payment_request.id',$id)->first();
		//dd($payment_request_data);

		$userBankdetail=  Bankdetail::where('user_id', $payment_request_data->surveyor_id)->first();
			$bank=array(
				'acc_holder_name'=>!empty($userBankdetail->acc_holder_name) ? $userBankdetail->acc_holder_name : '',
				'routing_number'=>!empty($userBankdetail->routing_number) ? $userBankdetail->routing_number : '',
				'acc_number'=>!empty($userBankdetail->acc_number) ? $userBankdetail->acc_number : '',
				'company_name'=>!empty($userBankdetail->company_name) ? $userBankdetail->company_name : '',
				'beneficiary_name'=>!empty($userBankdetail->beneficiary_name) ? $userBankdetail->beneficiary_name : '',
				'beneficiary_address'=>!empty($userBankdetail->beneficiary_address) ? $userBankdetail->beneficiary_address : '',
				'bank_name'=>!empty($userBankdetail->bank_name) ? $userBankdetail->bank_name : '',
				'swift_code'=>!empty($userBankdetail->swift_code) ? $userBankdetail->swift_code : '',
				'ach_acc_number'=>!empty($userBankdetail->ach_acc_number) ? $userBankdetail->ach_acc_number : '',
				'more_info'=>!empty($userBankdetail->more_info) ? $userBankdetail->more_info : '',
				'file_type'=>!empty($userBankdetail->file_type) ? $userBankdetail->file_type : '',
				'file'=>!empty($userBankdetail->file) ? $userBankdetail->file : '',

				'paypal_email_address'=>!empty($userBankdetail->paypal_email_address) ? $userBankdetail->paypal_email_address : '',
				'current_payment'=>!empty($userBankdetail->current_payment) ? $userBankdetail->current_payment : '',
				'country'=>!empty($userBankdetail->country) ? $userBankdetail->country : '',
		
				'city'=>!empty($userBankdetail->city) ? $userBankdetail->city : '',
	
				'state'=>!empty($userBankdetail->state) ? $userBankdetail->state : '',
	
				'street_address'=>!empty($userBankdetail->street_address) ? $userBankdetail->street_address : '',
	
				'pincode'=>!empty($userBankdetail->pincode) ? $userBankdetail->pincode : '',
	
			);
			//dd($bank);
		return view('admin.payment-request.view',["payment_request_data" => $payment_request_data,'bank'=>$bank]);

	}
	
	public function editpaymentrequest($id)
	{
		$id = base64_decode($id);
		$payment_request_data = Paymentrequest::find($id);
		return view('admin.payment-request.edit',["payment_request_data" => $payment_request_data]);
	}


	public function editpaymentrequestpost(Request $request)
	{
			$id = $request->input('id');
			$id =  base64_decode($id);
		$validator = Validator::make($request->all(), [
			'status' => 'required', ]);

			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$status=$request->input('status');

				$Paymentrequest = Paymentrequest::find($id);
               
				
				if($Paymentrequest!="")
				{
					$Paymentrequest->status=$status;
					$Paymentrequest->save();
					$survey_ids=explode(',',$Paymentrequest->survey_ids);
					$survey_numbers="";
					$c="";
						for($i=0;$i<count($survey_ids);$i++){
							$survey = survey::where('id',$survey_ids[$i])->first();
							$survey_numbers.=$c.$survey->survey_number;
							$c=",";
						}
						// echo $survey_numbers;exit;
					$surveyor_token =  User::select('users.id','users.first_name','users.email','users.type','users.device_id','users.country_id')->where('id',$Paymentrequest->surveyor_id)->first(); 
				  

					$survey_ids=explode(',',$Paymentrequest->survey_ids);
				//	dd($survey_ids);
					for($i=0;$i<count($survey_ids);$i++){
						$Earning = Earning::where('survey_id',$survey_ids[$i])->first();
						$Earning->paid_to_surveyor_status ='paid';
						$Earning->balance_for_this_surveyor ='';
						$Earning->save();
						$survey = Survey::find($Earning->survey_id);
						$survey->status='6';
						$survey->save();

					}
					// $data1 = array( 'email' =>$surveyor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',"data1"=>array('email' => $surveyor_token->email,'content' => 'Your Payment for survey number '.$survey_numbers.' has been sent  to your bank account'));
					// Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
					// {
					// 	$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Payment Send Your Account' );

					// });
					$emailData = Emailtemplates::where('slug','=','payment-send-to-surveyor')->first();

					if($emailData){
						$textMessage = strip_tags($emailData->description);
						$subject = $emailData->subject;
						$to = $surveyor_token->email;

						if($surveyor_token->first_name!='')
						{
							$textMessage = str_replace(array('USER_NAME','SURVEY_NUMBER'), array($surveyor_token->first_name,$survey_numbers),$textMessage);
							
							Mail::raw($textMessage, function ($messages) use ($to,$subject) {
								
								$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
							});
						}
					}

				}
				
				echo json_encode(array('class'=>'success','message'=>' Information  Edit successfully.'));die;
			}	
		}

	public function paymentrequestaction(Request $request)

	{

		 $id = base64_decode($request->input('id'));

		 $action = $request->input('action');

		 $paymentrequest = Paymentrequest::find($id);

		

		if(!empty($paymentrequest))

		{

			if($action=='1')

			{

				$paymentrequest->status =$action;

				$paymentrequest->save();

				return response()->json(['success'=>true ,'message'=>'<div class="alert alert-success alert-dismissible">

					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

					<h4><i class="icon fa fa-check"></i> Alert!</h4>

					Payment Request Accept successfully

				</div>']);



			}else{

				$paymentrequest->status =$action;

				$paymentrequest->save();

				return response()->json(['success'=>true ,'message'=>'<div class="alert alert-success alert-dismissible">

					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

					<h4><i class="icon fa fa-check"></i> Alert!</h4>

					Payment Request Decline successfully

				</div>']);

			}

			



		}

		

	}

	// 	public function usersdelete($id)



	//    {



	// 	$id = base64_decode($id);



	// 	$user = User::find($id);



	// 	$user->delete();



	// 	 Session::put('msg', '<strong class="alert alert-success"> User successfully deleted.</strong>');



	// 	 return redirect('/admin/user-management/users');	



	//    }

	

}



?>