<style>
select::-ms-expand {
    display: none;
}</style>

<script> 
$(document).ready(function () 
    {
 		$( '#mySearchForm' ).on( 'submit', function(e) 
        {
			
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });

              
            var amount=document.getElementById("amount").value;  
            var payment_method=document.getElementById("payment_method").value;  


                    var early_withdrawal_fees=0;
					var after_wire_surveyor_balance="";
					if(payment_method=='wire')
					{
						// if(amount>=2000){
						// 	var imars_transfer_cost=40;
						// }
						if(amount<2000){
							var imars_transfer_cost=amount*0.02;
							var early_withdrawal_fees=40-imars_transfer_cost;

						}
                        var msg="There will be a " + early_withdrawal_fees + " USD early withdrawal fee for this request (No fee for wire transfers of 2000 USD or more). Do you want to proceed?";
						
					}else{
                        var msg="Do you want to proceed to request payment?";

                    }


           
              if (confirm(msg)) 
           {

                    $.ajax({
                        type: 'POST',
                        url: '{{ URL::to('/paymentrequest') }}',
                        data: $('#mySearchForm').serialize(),
                        beforeSend: function(){
                            $.LoadingOverlay("show");
                        },
                        success: function(data)
                        {
                            if(data.success==false)
                            {
                            $.each(data.errors, function(key, value){
                                $('#'+key).closest('.form-group').addClass('has-error');
                                $('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                            });
                        }else
                        {
                        
                        if(data.class == 'success'){showMsg(data.message, "success");}
                        if(data.class == 'danger'){showMsg(data.message, "danger");}

                        showpage('{{URL::asset('/myearning')}}');
                        }
                                $.LoadingOverlay("hide");     
                        }
                    });
          
        }
   
        
     
    });



});

</script> 

<style>
	.col-md-2.text-center.searchbtn .btn.btn-primary {
		margin-top: 0px;
	}
	#upcomming_filter .form-control, #past_filter .form-control{
		border-radius: 0px !important;
		height: initial !important;
		padding: 7px 15px;
	}
	#upcomming_filter .form-submit, #past_filter .form-submit {
		float: right;
		width:100%;
	}
	#upcomming_filter .form-submit .btn.btn-primary,
	#past_filter .form-submit .btn.btn-primary {
		border-radius: 0px;
		padding: 5px;
		float: right;
	}
</style>
<section class="page">
    <div class="row">
        <div class="col-md-12 col-lg-12 col-xl-12">
            <div class="surveyors ports">

                <div class="right-flex-box">
                    <h4>My Earning</h4>
                </div>
                <?php  //echo count($account_data);exit;?>
               
                    <div class="right-flex-box">
                        <h4>Account Balance:</h4>
                    </div>
                   
                    <div class="login-inner">
                    <div class="row">
                    <div class="col-md-12">
                        {!! Form::open(array('url' => '/paymentrequest', 'method' => 'post','name'=>'mySearchForm','files'=>true,'novalidate' => 'novalidate','id' => 'mySearchForm')) !!}
                        
                        <?php $helper=new App\Helpers;  	
                        $SurveyTypeList=$helper->SurveyTypeList();?>
                        
                        <div id="upcomming_filter">
                        

                        @if($user_country_detail!="")
                            
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xl-12">
                                <div class="surveyors">
                            
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <select id="payment_method" name="payment_method" class="form-control">
                                                        <option value="">Select Payment Method</option>
                                                        @if($bank['current_payment']=='paypal')
                                                        <option value="paypal" selected>Paypal</option>
                                                        @endif

                                                        @if($bank['current_payment']=='wire')
                                                        <option value="wire" selected>Wire Transfer </option>
                                                        @endif

                                                        @if($bank['current_payment']=='ach')
                                                        <option value="ach" selected>ACH</option>
                                                        @endif
                                                    </select>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                        {!! Form::text('amount',$account_amount, ['id' => 'amount','class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()','readonly'=>"readonly"]) !!}
                                                </div>
                                            </div>
                                            
                                        <div class="col-md-2 text-center searchbtn">
                                                <div class="form-submit">
                                                    <button type="submit" class="btn btn-primary" style=" padding: 8px 0px;   font-size: 13px;" @if(count($account_data)==0) disabled @endif>Request Payment</button>
                                                </div>
                                            </div>
                                        </div>
                                        @if($bank['current_payment']=='paypal')
                                        <div class="row" >
                                        <div class="col-md-12">
                                        <ul class="company_details_list">
                                            <li> <span> Paypal Email:</span> {{$bank['paypal_email_address']}} </li>
                                        </ul>
                                        </div>
                                        </div>
                                        @endif

                                        @if($bank['current_payment']=='ach')
                                    
                                    <div class="row" >
                                        <div class="col-md-12">
                                            <ul class="company_details_list">
                                                    <li> <span> Account Holder Name:</span> {{$bank['acc_holder_name']}} </li>
                                                    <li> <span> Routing Number:</span> {{$bank['routing_number']}} </li>
                                                    <li> <span>Account Number:</span> {{$bank['ach_acc_number']}} </li>

                                                </ul>
                                            </div>
                                    </div>
                                    @endif   @if($bank['current_payment']=='wire')
                                        <div class="row" >
                                            <div class="col-md-12">
                                                <ul class="company_details_list">
                                                        <li> <span> Company Name:</span> {{$bank['company_name']}} </li>
                                                        <li> <span> Beneficiary Name:</span> {{$bank['beneficiary_name']}} </li>
                                                        <li> <span>Street Address:</span> {{$bank['street_address']}} </li>
                                                        <li> <span>City:</span> {{$bank['city']}} </li>
                                                        <li> <span>State:</span> {{$bank['state']}} </li>
                                                        <li> <span>Zip Code:</span> {{$bank['pincode']}} </li>
                                                        <li> <span>Country:</span> {{$bank['country']}} </li>
                                                        <li> <span> Bank Name:</span> {{$bank['bank_name']}} </li>
                                                        <li> <span> Swift Code:</span> {{$bank['swift_code']}} </li>
                                                        <li> <span>Account Number:</span> {{$bank['acc_number']}} </li>
                                                        <li> <span>Instructions:</span> {{$bank['more_info']}} </li>
                                                        <li> <span>File:</span> 

                                                        <?php 
                                                        if($bank['file']!="" && $bank['file_type']!=""){
                                                            $arr=explode('/',$bank['file_type']);
                                                        
                                                        } ?>
                                                
                                                            @if(!empty($arr['0']))   
                                                                    @if($arr['0']=='image')   
                                                                    <img src="{{ URL::asset('/public/media/bank_instruction'.'/'.$bank['file']) }}" alt="#" style="width: 46px;margin: 20px;">   
                                                                    @else
                                                                    <a href="{{ URL::asset('/public/media/bank_instruction'.'/'.$bank['file']) }}"> {{$bank['file']}} </a>
                                                                    @endif
                                                            @endif 
                                                            </li>

                                                    </ul>
                                                </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                </div>
                                
                            @endif
                        
                        </div>
                        
                    {!! Form::close() !!}
                    </div>
                    </div>
                    </div>

                   
                    
                    @if(count($account_data)!=0)
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Survey#</th>
                                    <th>Invoice Date</th>
                                    <th>Amount</th>
                                    <th>Vessels Name</th>
                                    <th>Port Name</th>
                                    <th>Survey Code</th>
                                    <th>Status</th>
                                    <th>Invoice</th>
                                    <th>Created</th>
                                
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach($account_data as $data)
                                <tr>
                                
                                    <td>{{$data->survey_number}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td>{{$data->balance_for_this_surveyor}}</td>
                                    <td>{{$data->vesselsname}}</td>
                                    <td>{{$data->port_name}}</td>
                                    <td>{{$data->port_name}}</td>
                                    <td>{{$data->paid_to_surveyor_status}}</td>
                                    <td><a href="{{ URL::asset('/public/media/invoice') }}/{{$data->invoice}}" target="_blank">View </a></td>
                                    <td>{{$data->created_at}}</td>
                                    
                                </tr>
                                <?php $i++; ?>
                                @endforeach

                               
                            </tbody>

                        </table>
                     @endif
                <div class="right-flex-box">
                    <h4>Pending Payments:</h4>
                </div>
                @if(!empty($pending_data))
                <div class="login-inner">
				<div class="row">
				<div class="col-md-12">
					
					<div id="upcomming_filter">
					  <div class="row" id="payment_method">

						   <div class="col-md-5">
                           <div class="form-group">
						   		{!! Form::text('amount',$pending_amount, ['id' => 'pending_amount','class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()','readonly'=>"readonly"]) !!}
                           </div>
                           </div>
                           
					</div>
					</div>
					
					
					
				
				</div>
				</div>
				</div>
                    @endif
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Survey#</th>
                            <th>Invoice Date</th>
                            <th>Amount</th>
                            <th>Vessels Name</th>
                            <th>Port Name</th>
                            <th>Survey Code</th>
                            <th>Status</th>
                            <th>Invoice</th>
                            <th>Created</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach($pending_data as $data)
                        <tr>
                          
                            <td>{{$data->survey_number}}</td>
                            <td>{{$data->created_at}}</td>
                            <td>{{$data->invoice_amount}}</td>
                            <td>{{$data->vesselsname}}</td>
                            <td>{{$data->port_name}}</td>
                            <td>{{$data->code}}</td>
                            <td>{{$data->paid_to_surveyor_status}}</td>
                            <td><a href="{{ URL::asset('/public/media/invoice') }}/{{$data->invoice}}" target="_blank">View </a></td>
                            <td>{{$data->created_at}}</td>
                            
                        </tr>
                        <?php $i++; ?>
                        @endforeach

                        @if($i<2) <tr>
                            <td>No Pending Payments Data</td>
                            </tr>
                            @endif
                    </tbody>

                </table>
                <div class="right-flex-box">
                    <h4>Past Payments:</h4>
                </div>
                @if(!empty($past_data))
                <div class="login-inner">
				<div class="row">
				<div class="col-md-12">
					
                    
					
					<div id="upcomming_filter">
					  <div class="row" id="payment_method">

                     
                      
						   <div class="col-md-5">
                           <div class="form-group">
						   		{!! Form::text('amount',$past_amount, ['id' => 'amount','class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()','readonly'=>"readonly"]) !!}
                           </div>
                           </div>
                           
					</div>
					</div>
					
					
					
				</div>
				</div>
				</div>
                    @endif
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Survey#</th>
                            <th>Invoice Date</th>
                           
                            <th>Amount</th>
                            <th>Vessels Name</th>
                            <th>Port Name</th>
                            <th>Survey Code</th>
                            <th>Status</th>
                            <th>Invoice</th>
                            <th>Created</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach($past_data as $data)
                        <tr>
                          
                            <td>{{$data->survey_number}}</td>
                            <td>{{$data->created_at}}</td>
                            
                            <td>{{$data->transfer_to_surveyor}}</td>
                            
                            <td>{{$data->vesselsname}}</td>
                            <td>{{$data->port_name}}</td>
                            <td>{{$data->port_name}}</td>
                            <td>{{$data->paid_to_surveyor_status}}</td>
                            <td><a href="{{ URL::asset('/public/media/invoice') }}/{{$data->invoice}}" target="_blank">View </a></td>
                            <td>{{$data->created_at}}</td>
                            
                        </tr>
                        <?php $i++; ?>
                        @endforeach

                        @if($i<2) <tr>
                            <td>No Past Payments Data</td>
                            </tr>
                            @endif
                    </tbody>

                </table>




            </div>
        </div>
    </div>
</section>