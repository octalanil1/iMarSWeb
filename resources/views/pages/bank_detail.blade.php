<script type="text/javascript">
    $(document).ready(function () 
    {
        $( '#Mybankdetail' ).on( 'submit', function(e) 
        {
			$.LoadingOverlay("show");
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
            
            $.ajax({
                dataType: 'json',
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
            url: '{{ URL::to('/bank-detail-post') }}',
        }).done(function( data ) 
        {  error_remove (); 
			if(data.success==false)
            {
                    $.each(data.errors, function(key, value){
                        $('#'+key).closest('.form-group').addClass('has-error');
                        $('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                    });
                }else
            {
                
                  if(data.class == 'success')
                  {
                      showMsg(data.message, "success");
                }
                  if(data.class == 'danger')
                  {
                      showMsg(data.message, "danger");
                }

                if(data.payment_method == "paypal")
                    {
                            $('#paypal').show();
                            $('#wire').hide();
                            $('#ach').hide();
                    } else if(data.payment_method == "wire")
                    {
                            $('#wire').show();
                            $('#paypal').hide();
                            $('#ach').hide();
                    }
                    else if(data.payment_method == "ach") 
                    {
                            $('#ach').show();
                            $('#paypal').hide();
                            $('#wire').hide();
                    }else
                    {
                            $('#achd').hide();
                            $('#paypal').hide();
                            $('#wire').hide();
                    }

                  //showpage('{{URL::asset('/payment-detail')}}');
            }
           $.LoadingOverlay("hide");     
        });

    });
});

$('#payment_method').on('change', function() {
  //  alert( this.value ); // or $(this).val()
  if(this.value == "paypal")
   {//alert('paypal');
        $('#paypal').show();
        $('#wire').hide();
        $('#ach').hide();
        
        $('#p').prop( "checked", true );
        $('#w').prop('checked',false);
        $('#achd').prop('checked',false);
  } else if(this.value == "wire")
   { //alert('wire');
        $('#wire').show();
        $('#paypal').hide();
        $('#achd').hide();
        
        $('#w').prop( "checked", true );

        $('#p').prop('checked',false);
        $('#achd').prop('checked',false);
  }
  else if(this.value == "ach") 
  {
        $('#achd').show();
        $('#paypal').hide();
        $('#wire').hide();
       
        $('#ach').prop('checked',true);
        $('#p').prop('checked',false);
        $('#w').prop('checked',false);
  }else
  {
        $('#achd').hide();
        $('#paypal').hide();
        $('#wire').hide();
        $('#w').prop('checked',false);
        $('#p').prop('checked',false);
        $('#ach').prop('checked',false);
  }

});
</script> 
<style>
.upload-name {
    border: 0px;
    background: transparent;
    position: relative;
    top: 10px;
    padding-left: 15px;
}
.Upload-Wire {
    position: relative;
    top: 9px;
}
#wire label span {
    display: block;
}
    .user-info-field fieldset {
    border: 1px solid #ddd !important;
margin: 8px;
width: 100%;
padding: 10px;
    padding-left: 10px;
position: relative;
border-radius: 4px;
background-color:
    #f5f5f5;
    padding-left: 10px !important;
}.user-info-field legend {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 0px;
    width: 35%;
    border: 1px solid 
#ddd;
border-radius: 4px;
padding: 5px 5px 5px 10px;
background-color:
    #ffffff;
}
    </style>
    <style>
/* The container */
.current_payment {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 15px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  margin: 7px;
  float: left;
}

/* Hide the browser's default radio button */
.current_payment input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  float: left;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.current_payment:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.current_payment input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.current_payment input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.current_payment .checkmark:after {
 	top: 9px;
	left: 9px;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: white;
}
</style>
 <section class="page">
		<div class="row">
		    <div class="col-md-12 col-lg-12 col-xl-12">
		    	<div class="surveyors">
             <div class="right-flex-box"> <h4>Payment Details</h4> </div>
             <p> <strong>Note:</strong> Enter details of the method you want to receive fundsaccumulated in your account balance</p>
            
                {!! Form::open(array('url' => 'bank-detail-post', 'method' => 'post','name'=>'Mybankdetail','files'=>true,'novalidate' => 'novalidate','id' => 'Mybankdetail')) !!}
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <select id="payment_method" name="payment_method" class="form-control">
                                <option value="">Select Payment Method</option>
                                @if($user_country_detail->paypal_country=='1' && $user_country_detail->sortname!='US')
                                <option value="paypal">Paypal</option>
                                @endif

                                @if($user_country_detail->chase_wire_country=='1' && $user_country_detail->sortname!='US')
                                <option value="wire">Wire Transfer </option>
                                @endif

                                @if($user_country_detail->sortname=='US')
                                <option value="ach">ACH</option>
                                @endif
                            </select>
                            
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label>Current Method</label>
                        </div></div>
                    <div class="col-md-5" >
                        <div class="form-group" id="current_payment"> 
                            
                               
                                @if($user_country_detail->paypal_country=='1' && $user_country_detail->sortname!='US')
                                <label class="current_payment">Paypal
                                <input type="radio" name="current_payment" value="paypal" <?php if($bank['current_payment']=='paypal'){ echo 'checked'; } ?> id="p" disabled>
                                    <span class="checkmark"></span>
                                </label>

                                
                                @endif

                                @if($user_country_detail->chase_wire_country=='1' && $user_country_detail->sortname!='US')
                                <label class="current_payment">Wire
                                <input type="radio" name="current_payment" value="wire" <?php if($bank['current_payment']=='wire') {echo 'checked'; } ?> id="w" disabled>                                    
                                <span class="checkmark"></span>
                                </label>
                                
                                @endif

                                @if($user_country_detail->sortname=='US')
                                <label class="current_payment">ACH
                                <input type="radio" name="current_payment" value="ach" <?php if($bank['current_payment']=='ach') {echo 'checked'; }?> id="ach" disabled>                               
                                <span class="checkmark"></span>
                               </label>
                              

                                @endif
                           
                            
                        </div>
                    </div>
                </div>
                
                <div class="row" id="paypal" style="display:none">
                    <div class="col-md-6">
                        <label><strong>Paypal Email:</strong></label>
                        <div class="form-group">
                            {!! Form::text('paypal_email_address', $bank['paypal_email_address'], ['class' => 'form-control','placeholder' => 'Enter PayPal Email Address','required'=>'required','id'=>'paypal_email_address']) !!}
                            
                        </div>
                    </div>
                </div>

               
              
              <div class="row" id="achd" style="display:none">
                <div class="col-md-6">
                <label><strong>Account Holder Name & Last Name:</strong></label>
                        <div class="form-group">
                            {!! Form::text('acc_holder_name', $bank['acc_holder_name'], ['class' => 'form-control','placeholder' => 'Account Holder Name','required'=>'required','id'=>'acc_holder_name']) !!}

                        </div>
                 </div>
                <div class="col-md-6">
                <label><strong>Routing Number:</strong></label>
                    <div class="form-group">
                        {!! Form::text('routing_number', $bank['routing_number'], ['class' => 'form-control','placeholder' => 'Routing Number','required'=>'required','id'=>'routing_number']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                <label><strong>Account Number:</strong></label>
                    <div class="form-group">
                        {!! Form::text(' ach_acc_number', $bank['ach_acc_number'], ['class' => 'form-control','placeholder' => 'Account Number','required'=>'required','id'=>'acc_number']) !!}

                    </div>
                </div>
            </div>
            
                <div class="row" id="wire" style="display:none">
                <div class="col-md-12">
                <p> Enter Intermediary Bank Information if there is any: Bank Name, Bank Address, Swift Code etc</p>
                </div>
                <div class="col-md-6">
                    <label><strong>Beneficiary Name & Last Name <span>(Personal Bank Accounts):</span></strong></label>
                        <div class="form-group">
                            {!! Form::text('beneficiary_name', $bank['beneficiary_name'], ['class' => 'form-control','placeholder' => 'Beneficiary Name','required'=>'required','id'=>'beneficiary_name']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                    <label><strong>Company Name <span>(For Business Bank Accounts):</span></strong></label>
                        <div class="form-group">
                            {!! Form::text('company_name', $bank['company_name'], ['class' => 'form-control','placeholder' => 'Company Name','required'=>'required','id'=>'company_name']) !!}
                        </div>
                    </div>
                    
                    
                    <div class="col-md-12">
                    <legend>Beneficiary Address:</legend>
                    </div>
                            <div class="col-md-6">
                            <label><strong>Street Address:</strong></label>
                                <div class="form-group">
                                    {!! Form::text('street_address', $bank['street_address'], ['class' => 'form-control','placeholder' => 'Street Address','required'=>'required','id'=>'street_address']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                            <label><strong>City:</strong></label>
                                <div class="form-group">
                                    {!! Form::text('city', $bank['city'], ['class' => 'form-control','placeholder' => 'City','required'=>'required','id'=>'city']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                            <label><strong>State:</strong></label>
                                <div class="form-group">
                                    {!! Form::text('state', $bank['state'], ['class' => 'form-control','placeholder' => 'State','required'=>'required','id'=>'state']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                            <label><strong>Zip:</strong></label>
                                <div class="form-group">
                                    {!! Form::text('zip', $bank['pincode'], ['class' => 'form-control','placeholder' => 'Zip','required'=>'required','id'=>'zip']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                            <label><strong>Country:</strong></label>
                                <div class="form-group">
                                    {!! Form::text('country', $bank['country'], ['class' => 'form-control','placeholder' => 'Country','required'=>'required','id'=>'country']) !!}
                                </div>
                            </div>
                      
                    <div class="col-md-6">
                    <label><strong>Bank Name:</strong></label>
                        <div class="form-group">
                            {!! Form::text('bank_name', $bank['bank_name'], ['class' => 'form-control','placeholder' => 'Bank Name','required'=>'required','id'=>'bank_name']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                    <label><strong>Swift Code:</strong></label>
                        <div class="form-group">
                            {!! Form::text('swift_code', $bank['swift_code'], ['class' => 'form-control','placeholder' => 'Swift Code','required'=>'required','id'=>'swift_code']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                    <label><strong>Account Number/IBAN:</strong></label>
                        <div class="form-group">
                            {!! Form::text('acc_number', $bank['acc_number'], ['class' => 'form-control','placeholder' => 'Account Number/IBAN','required'=>'required','id'=>'acc_number']) !!}
                        </div>
                    </div>
                
                        <label class="col-md-4 control-label Upload-Wire"><strong>Upload (Wire Transfer Instructions): </strong>Check with your bank for special instructions such as Sort Code, Intermediary Bank Information) to receive funds</label>
                        <div class="col-md-2">
                            <div class="upload-file" id="file">
                            <input type="file" name="file_data" id="myfile" class="form-control input-file" onkeypress="error_remove()">
                                <button class="btn-success">Upload</button>
                            </div>
                            
                        </div>
                        <div class="col-md-4 ">
                        <input type="text" class="upload-name" id="input-file-placeholder" placeholder="" disabled>
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
                        </div>
                        <div class="form-group">
                    {!! Form::textarea('more_info', $bank['more_info'], ['class' => 'form-control mt-2','placeholder' => 'More Information','required'=>'required','id'=>'more_info','style'=>"height: 87px;"]) !!}

                    </div>
                    
                       
                </div>

                    
            
              <div class="login-inner">
              <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                  {!! Form::close() !!}
              </div>
              @if($user_country_detail->paypal_country=='1' && $user_country_detail->sortname!='US')
              <p> <strong>Note:</strong> After you receive funds in your PayPal account, there may be additional PayPal 
              charges when you transfer funds in your PayPal account to your bank account in 
              the country you reside. iMarS is not responsible for these charges. 
              If Wire Transfer is available in your country, you may consider it as an alternative.
              Wire transfers of $2000 or more are free of charge and sponsored by iMarS.”	</p>
              @endif		
		  	</div>
		</div>
		</div>
</section>
      
  