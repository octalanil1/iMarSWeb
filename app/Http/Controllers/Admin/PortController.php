<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\Models\Port;
use App\Models\Countries;
use App\Uniqcode;
use App\Models\Admin;
use App\Models\UsersPort;

use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;
use Excel;
class PortController extends Controller {
  private $admin;
  public function __construct()
    {
		if (session('admin')['id'])
		{
			$admindata = Admin::find(session('admin')['id']);
			$this->user = $admindata;
		}
    }



	public function usersport(Request $request)
	{
		$country_id = $request->input('country_id');
		$port = $request->input('port');
		$email=$request->input('email');
		$start_date= $request->input('start_date');
		$end_date=$request->input('end_date');
		

		//$port_data = Port::orderBy("created_at","DESC");


		$port_data = UsersPort::select('users_port.*','users.email as user_email',
		'countries.name as country_name','port.port as port_name')
			->leftJoin('users', 'users_port.user_id', '=', 'users.id')	
			->leftJoin('countries', 'users_port.country_id', '=', 'countries.id')
			->leftJoin('port', 'users_port.port_id', '=', 'port.id')
				->orderBy("users_port.created_at","DESC");


		if($country_id!="")
		{ $port_data = $port_data->where('users_port.country_id',$country_id); }

		if($port!="")
		{ $port_data = $port_data->where('users_port.port_id','like',"%$port%"); }
		if($email!="")
		{ $port_data = $port_data->where('users.email','like',"%$email%"); }

		else if ($start_date!="" && $end_date!="") {


            $start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$end_date = date('Y-m-d H:i:s', strtotime($end_date . ' 23:59:59'));
			$port_data = $port_data->whereBetween('users.created_at', [$start_date, $end_date]);
            

        } else if ($start_date!="" && $end_date=="") {

			//$start_date = date('Y-m-d H:i:s', strtotime($start_date));
			$start_date = date('Y-m-d H:i:s', strtotime($start_date . ' 01:00:00'));

			$port_data =$port_data->where('users.created_at',">=",$start_date);

           
        } else if ($start_date=="" && $end_date!="") {
			$end_date = date('Y-m-d H:i:s', strtotime($end_date .' 23:59:59'));
			$port_data = $port_data->where('users.created_at',"<=",$end_date);

        }
		


		$port_data=$port_data->paginate(20);	

		if ($request->ajax()) 
		{
			return view('admin.users-port.search', compact('port_data'));  
		}


		$admindata = Admin::find(session('admin')['id']);
        return view('admin.users-port.show', compact('port_data','admindata'));  
	}

	public function port(Request $request)
	{
		$country_id = $request->input('country_id');
		$port = $request->input('port');
		$port_data = Port::select('port.*')->orderBy("port","Asc");
		$num_rows=$request->input('num_rows');
		if($country_id!="")
		{ $port_data = $port_data->where('port.country_id',$country_id); }

		if($port!="")
		{ $port_data = $port_data->where('port.port','like',"%$port%"); }
		
		$rows="20";	
		if($num_rows!="")
		{
			if($num_rows=='all'){
				$count=$port_data->count();	
				$rows=$count;
			}else{
				$rows=$num_rows;
			}
			
		}

		$port_data=$port_data->paginate($rows);	

		if ($request->ajax()) 
		{
			return view('admin.port.search', compact('port_data'));  
		}


		$admindata = Admin::find(session('admin')['id']);
        return view('admin.port.show', compact('port_data','admindata'));  
	}

	

	public function addport(Request $request)

	{

		return view('admin.port.add');

	} 

	public function addportpost(Request $request)

	{
			$validator = Validator::make($request->all(), ['country_id' => 'required','port' => 'required','status' => 'required']);

			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{

				$portdatasave = new Port();				
				$country_id =  $request->input('country_id');
				$countrydata =  Countries::where('id',$country_id)->first();	
				$status =  $request->input('status');

				$port =  $request->input('port');	
				$portdatasave->country_id = $country_id;
				$portdatasave->country = $countrydata->name;
				$portdatasave->country_code = $countrydata->sortname;
				$portdatasave->port = $port;
				$portdatasave->status =$status ;

                $portdatasave->save();
				echo json_encode(array('class'=>'success','message'=>'Port Added successfully.'));die;
			}
	} 

	

	public function editport($id)
	{

		$id = base64_decode($id);
		$port_data = Port::find($id);
		return view('admin.port.edit',["port_data" => $port_data]);
	}

	



	public function editportpost(Request $request)
	{

		$id = $request->input('id');
		 $id =  base64_decode($id);
		$validator = Validator::make($request->all(), ['country_id' => 'required','port' => 'required','status' => 'required' ]);
		if ($validator->fails()) 
		{return response()->json(['success'=>false ,'errors'=>$validator->errors()]);}
		else
		{

			$portdatasave = Port::find($id);
			$country_id =  $request->input('country_id');
			$countrydata =  Countries::where('id',$country_id)->first();	
			$status =  $request->input('status');

				$port =  $request->input('port');	
				$portdatasave->country_id = $country_id;
				$portdatasave->country = $countrydata->name;$portdatasave->country_code = $countrydata->sortname;
				$portdatasave->port = $port;
				$portdatasave->status = $status;

                $portdatasave->save();

			echo json_encode(array('class'=>'success','message'=>'Port Edit successfully.'));die;
		}	
	}
	public function edituserport($id)
	{

		$id = base64_decode($id);
		$port_data = UsersPort::find($id);
		return view('admin.users-port.edit',["port_data" => $port_data]);
	}

	



	public function edituserportpost(Request $request)
	{

		$id = $request->input('id');
		 $id =  base64_decode($id);
		$validator = Validator::make($request->all(), ['port_id' => 'required','cost' => 'required' ]);
		if ($validator->fails()) 
		{return response()->json(['success'=>false ,'errors'=>$validator->errors()]);}
		else
		{

			$portdatasave = UsersPort::find($id);
			
			$port_id =  $request->input('port_id');
			$cost =  $request->input('cost');	
			$portdatasave->port_id = $port_id;
			$portdatasave->cost = $cost;
			$portdatasave->save();

			echo json_encode(array('class'=>'success','message'=>'Port Edit successfully.'));die;
		}	
	}
	public function userportremove(Request $request)

	{

        $id = base64_decode($request->input('id'));
		$Port = UsersPort::find($id);
		if($Port->status=='0'){
			$Port->status='1';
		}else{
			$Port->status='0';
		}
		
        $Port->save();

		echo json_encode(array('class'=>'success','message'=>'  Port Remove Successfully.'));die;
       



	   }


	public function importcountry(Request $request)
	{
				$path = $request->file('import_file')->getRealPath();
				$excel_data = Excel::load($path)->get();
			
	}

	public function importport(Request $request)

	{

		return view('admin.port.import-port');

	} 

	public function importportpost(Request $request)
	{
		$validator = Validator::make($request->all(), ['import_file' => 'required|mimes:xls,xlsx|',]);

			if ($validator->fails()) 
			{
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$path = $request->file('import_file')->getRealPath();
				$excel_data = Excel::load($path)->get();
				//dd(	$excel_data );
			//echo '<pre>';print_r($excel_data[0][1] );exit;


			if($excel_data->count())

			{
				//echo $excel_data->ddd;
				foreach($excel_data[0] as $key=> $data)
				{
					
					$portdata = new Port;
					$country_code= $data->country_code? $data->country_code :'';

					//$port_code_data = Port::where('country_code',$country_code)->first();
					$country_data = Countries::where('sortname',$country_code)->first();
// echo $country_data->id;exit;
					if(!empty($country_data))
					{
						
							$portdata->country_id=$country_data->id;
							$portdata->country=$data->country;
							$portdata->country_code=$data->country_code;
							$portdata->port=$data->port;
							$portdata->save();
							
						
					}
					
				}
				echo json_encode(array('class'=>'success','message'=>'Port insert successfully.'));die;

				
			}else{
				echo json_encode(array('class'=>'danger','message'=>'Something Went Wrong.'));die;

			}
		
			
			}
	} 

}



?>