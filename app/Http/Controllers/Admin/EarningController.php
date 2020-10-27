<?php 

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Pagination\Paginator;

use Validator;

use App\User;

use App\Models\Emailtemplates;

use App\Models\Admin;

use App\Models\Survey;
use App\Models\Port;
use App\Models\Earning;

use Hash;

use Auth;

use DB;

use App\Helpers;

use Config;

use Session;

use Mail;

class EarningController extends Controller {

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
			$survey_number=$request->input('survey_number');
			$operator_email=$request->input('operator_email');
			$surveyor_email=$request->input('surveyor_email');
			$invoice_amount= $request->input('invoice_amount');
			$invoice_paid_status=$request->input('invoice_paid_status');
			$country_id=$request->input('country_id');


			$earning_data = Earning::select("payment.*",'survey.invoice','survey.survey_number'
			,'survey_views.surveyor_email',
			'survey_views.operator_email')
			->leftJoin('users', 'payment.operator_id', '=', 'users.id')	
			->leftJoin('survey', 'payment.survey_id', '=', 'survey.id')	
			->leftJoin('survey_views', 'survey.survey_number', '=', 'survey_views.survey_number')	
			->orderBy("payment.created_at","DESC");
			
			if($survey_number!="")
			{$earning_data = $earning_data->where('survey.survey_number',"=",$survey_number);}

			if($operator_email!="")
			{$earning_data = $earning_data->where('survey_views.operator_email',"=",$operator_email);}

			if($surveyor_email!=""){$earning_data = $earning_data->where('survey_views.surveyor_email',"=",$surveyor_email);}

			if($invoice_amount!=""){$earning_data = $earning_data->where('payment.invoice_amount',"=",$invoice_amount);}
			if($invoice_paid_status!=""){$earning_data = $earning_data->where('payment.invoice_status',"=",$invoice_paid_status);}

			if($country_id!=""){$earning_data = $earning_data->where('survey.country_id',"=",$country_id);}


		$earning_data = $earning_data->paginate(20);
		//dd($earning_data );
		$customerdata = User::where('type','0')->where('is_admin','0')->orderBy("created_at","DESC")->get();
		$customer_box=array(''=>'Select Customer');
		foreach($customerdata as $key=>$value){

			$customer_box[$value->id]=sprintf('%s %s',$value->first_name,$value->last_name);

		}
		
		$surveyordata = User::where('type','1')->orderBy("created_at","DESC")->get();
		$surveyor_box=array(''=>'Select Surveyor');
		foreach($surveyordata as $key=>$value){

			$surveyor_box[$value->id]=sprintf('%s %s',$value->first_name,$value->last_name);

		}
		$portdata = Port::orderBy("port","DESC")->get();
		$port_box=array(''=>'Select Port');
		foreach($portdata as $key=>$value){
			$port_box[$value->id]=$value->port;
		}

		if ($request->ajax()) 
			{
				return view('admin.earning.search', compact('earning_data'));  
			}
			$admindata = Admin::find(session('admin')['id']);

			return view('admin.earning.show', compact('earning_data','customer_box','surveyor_box','admindata','port_box'));
	}

	public function editearning($id)
	{
		$id = base64_decode($id);
		$earning_data = Earning::find($id);
		return view('admin.earning.edit',["earning_data" => $earning_data]);
	}


	public function editearningpost(Request $request)
	{
		$id = $request->input('id');
		 $id =  base64_decode($id);
		$validator = Validator::make($request->all(), [
			'received_from_operator' => 'required',
			'invoice_status' => 'required', ]);

			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{

				$Earning = Earning::find($id);
                $received_from_operator =  $request->input('received_from_operator');
                $invoice_status =  $request->input('invoice_status');
				
				if($received_from_operator!="")
				{
					$Earning->received_from_operator=$received_from_operator;
					$transfer_cost_operator=$Earning->invoice_amount -$received_from_operator;
					$Earning->transfer_cost_operator =$transfer_cost_operator;
					$transfer_to_surveyor=$received_from_operator*0.8;
					$Earning->transfer_to_surveyor =$transfer_to_surveyor;
					$Earning->balance_for_this_surveyor  =$transfer_to_surveyor;
					$Earning->commission_amount =$received_from_operator-$transfer_to_surveyor;
				}

                $Earning->invoice_status = $invoice_status;
				$Earning->save();

				  $opeartor_token =  User::select('users.id','users.first_name','users.email','users.type','users.device_id','users.country_id')
				  ->where('id',$Earning->operator_id)->first(); 
				//  $data1 = array( 'email' =>$opeartor_token->email, 'from' => 'info@imars.com', 'from_name' => 'IMARS',
				//  "data1"=>array('email' => $opeartor_token->email,'content' => 'Thank You for your payment'));
				// 	Mail::send( 'pages.email.survey',$data1, function( $message ) use ($data1)
				// 	{
				// 		$message->to( $data1['email'] )->from( $data1['from'], $data1['from_name'] )->subject( 'Thank You' );

				// 	});
					
				$survey = Survey::find($Earning->survey_id);
				$survey->status='5';
				$survey->save();
				$emailData = Emailtemplates::where('slug','=','payment-receive-from-operator')->first();

					if($emailData){
						$textMessage = strip_tags($emailData->description);
						$subject = $emailData->subject;
						$to = $opeartor_token->email;

						if($opeartor_token->first_name!='')
						{
							$textMessage = str_replace(array('USER_NAME','SURVEY_NUMBER'), array($opeartor_token->first_name,$survey->survey_number),$textMessage);
							
							Mail::raw($textMessage, function ($messages) use ($to,$subject) {
								
								$messages->from('imars@marineinfotech.com','iMarS')->to($to)->subject($subject);
							});
						}
					}
				echo json_encode(array('class'=>'success','message'=>' Information  Edit successfully.'));die;
			}	



	}







	

		

	



		



		
	

}



?>