<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Validator;
use App\Models\Countries;
use App\Uniqcode;
use App\Models\Admin;
use Hash;
use Auth;
use DB;
use App\Helpers;
use Config;
use Session;
use Mail;
use Excel;
class CountryController extends Controller {
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
		$country_data = Countries::orderBy("name","ASC");
		$name = $request->input('name');
	
		if($name!="")
		{ $country_data = $country_data->where('name','like',"%$name%"); }
		$country_data=$country_data->paginate(20);	 
        

		 if ($request->ajax()) 
		{
			return view('admin.country.search', compact('country_data'));  
        }
		$admindata = Admin::find(session('admin')['id']);

        return view('admin.country.show', compact('country_data','admindata'));
	}
	
	public function addcountry(Request $request)
	{
		return view('admin.country.add');
	} 
	public function addcountrypost(Request $request)
	{
			$validator = Validator::make($request->all(), [
			'name' => 'required',
			'sortname' => 'required',
			'phonecode' => 'required',
			]);
			if ($validator->fails()) 
			{//echo '<pre>';print_r($validator->errors());exit;
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$Country = new Countries();				
				$name =  $request->input('name');	
				$sortname =  $request->input('sortname');	
				$phonecode =  $request->input('phonecode');	
				$registration =  $request->input('registration');	
				$country_with_ports =  $request->input('country_with_ports');	
				$chase_wire_country =  $request->input('chase_wire_country');	
				$paypal_country =  $request->input('paypal_country');	


				$Country->name = $name;
				$Country->sortname = $sortname;
				$Country->phonecode = $phonecode;
				if(!empty($registration)){
					$Country->registration = $registration;
				}
				if(!empty($country_with_ports)){
					$Country->country_with_ports = $country_with_ports;
				}
				if(!empty($chase_wire_country)){
					$Country->chase_wire_country = $chase_wire_country;
				}
				if(!empty($paypal_country)){
					$Country->paypal_country = $paypal_country;
				}
                $Country->save();
                echo json_encode(array('class'=>'success','message'=>'Country Added successfully.'));die;
				
			}
		
	} 
	
	public function editcountry($id)

	{
		$id = base64_decode($id);
		$country_data = Countries::find($id);
		return view('admin.country.edit',["country_data" => $country_data]);

	}
	

	public function editcountrypost(Request $request)

	{
		$id = $request->input('id');
		 $id =  base64_decode($id);
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'sortname' => 'required',
			'phonecode' => 'required',

			 ]);

			  if ($validator->fails()) 
			  {
					return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$Country = Countries::find($id);
			
				$name =  $request->input('name');	
				$sortname =  $request->input('sortname');	
				$phonecode =  $request->input('phonecode');	
				$registration =  $request->input('registration');	
				$country_with_ports =  $request->input('country_with_ports');	
				$chase_wire_country =  $request->input('chase_wire_country');	
				$paypal_country =  $request->input('paypal_country');	


				$Country->name = $name;
				$Country->sortname = $sortname;
				$Country->phonecode = $phonecode;
				if(!empty($registration)){
					$Country->registration = $registration;
				}
				if(!empty($country_with_ports)){
					$Country->country_with_ports = $country_with_ports;
				}
				if(!empty($chase_wire_country)){
					$Country->chase_wire_country = $chase_wire_country;
				}
				if(!empty($paypal_country)){
					$Country->paypal_country = $paypal_country;
				}
                $Country->save();
				echo json_encode(array('class'=>'success','message'=>'Country Edit successfully.'));die;
				
			}	

	}



	public function countrystatus(Request $request)
	{
		 $id = base64_decode($request->input('id'));
		$countrydata = Countries::find($id);
		
		if($countrydata->status=="1")
		{
			$countrydata->status = "0";
			$countrydata->save();
			echo json_encode(array('class'=>'success','message'=>' Country Deactive successfully.'));die;
			

		}else
		{
			$countrydata->status = "1";
			$countrydata->save();
			echo json_encode(array('class'=>'success','message'=>'Country Active successfully.'));die;
			
			  
		}
		
	}

	public function importcountry(Request $request)

	{

		return view('admin.country.import-country');

	} 

	public function importcountrypost(Request $request)
	{
		$validator = Validator::make($request->all(), ['import_file' => 'required|mimes:xls,xlsx|',]);

			if ($validator->fails()) 
			{
				return response()->json(['success'=>false ,'errors'=>$validator->errors()]);
			}else
			{
				$path = $request->file('import_file')->getRealPath();
				$excel_data = Excel::load($path)->get();
			//	dd(	$excel_data );
		//	echo '<pre>';print_r($excel_data[0][1] );exit;


			if($excel_data->count())

			{
				//echo $excel_data->ddd;
				foreach($excel_data[0] as $key=> $data)
				{
					echo $data->registration;
					$countrydata = new Countries;
					 $country_code= $data->code? $data->code :'';

					$country_code_data = Countries::where('sortname',$country_code)->first();

					if(empty($country_code_data))
					{
						$countrydata->sortname=$data->code;
						$countrydata->name=$data->country_name;
						$countrydata->phonecode=$data->dialing_code;
						$countrydata->registration=$data->registration;
						$countrydata->country_with_ports=$data->countries_with_ports;
						$countrydata->chase_wire_country=$data->chase_wire_countries;
						$countrydata->paypal_country=$data->paypal_countries;
						$countrydata->status='1';

						$countrydata->save();
						
					}else
					{
						$country_code_data->sortname=$data->code;
						$country_code_data->name=$data->country_name;
						$country_code_data->phonecode=$data->dialing_code;
						$country_code_data->registration=$data->registration;
						$country_code_data->country_with_ports=$data->countries_with_ports;
						$country_code_data->chase_wire_country=$data->chase_wire_countries;
						$country_code_data->paypal_country=$data->paypal_countries;
						$country_code_data->status='1';

						$country_code_data->save();
						
					}
					
				}
				echo json_encode(array('class'=>'success','message'=>'Country insert successfully.'));die;

				
			}else{
				echo json_encode(array('class'=>'danger','message'=>'Something Went Wrong.'));die;

			}
		
			
			}
	} 
	
}

?>