<style>
.company_details_list li .verfy-btn {
    background: transparent;
	color:#ec8a5d;
    float: right;
    padding: 7px 23px;
    font-size: 16px;
    position: relative;
    top: -9px;
    border-radius: 55px;
}
.upload-file .btn-success.sucgreen, .company_details_list li .verfy-btn.sucgreen {
    border-color: green !important;
	color:green;
	padding: 7px 17px;
}

    .user-info-field fieldset {
    border: 1px solid #ddd !important;
	margin: 8px;
	width: 100%;
	padding: 10px;
    padding-left: 10px;
	position: relative;
	border-radius: 4px;
	background-color:#f5f5f5;
    padding-left: 10px !important;
}.user-info-field legend {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 0px;
    width: 35%;
    border: 1px solid #ddd;
	border-radius: 4px;
	padding: 5px 5px 5px 10px;
	background-color:#ffffff;
}
</style>

<script type="text/javascript">
    
	$(document).ready(function () 
    {
		
       
		var upload_id=$('#upload_id').val();
		var tax_id_document=$('#tax_id_document').val();
		<?php 
			if($userdata->mobile_verify=='0'){
				$mv=0;
			}else{
				$mv=1;
			}
			
		?>
			var mobile_veirfy=<?php echo $mv;?>;
	<?php 	if($userdata->type=='2' || $userdata->type=='4') {?>
			var experience = $('#experience').val();
			var about_me = $('#about_me').val();
			if (experience != '' && experience != 0 &&  about_me != '' && upload_id != '' && tax_id_document!="" && mobile_veirfy==1) {
				$('#document_submit').attr("disabled", false);	        }
			else {
			
				$('#document_submit').attr("disabled",true);
			}
	<?php } else{?>
		if (upload_id != '' && tax_id_document!="" && mobile_veirfy==1) {
				$('#document_submit').attr("disabled", false);	        }
			else {
			
				$('#document_submit').attr("disabled",true);
			}
	<?php  } ?>

		$('body').on('change', ".input-file", function (e) {
			var mainclass = $(this).attr('name');
            if(!_.isUndefined(e.target.files[0])) {
                let name = e.target.files[0].name;
                $('#'+mainclass).val(name);
            }
		})
		
        $( '#MyAgentsForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/editprofilepost') }}',
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
          
           		 if(data.class == 'success'){showMsg(data.message, "success");}
                	$("#editProfileModel").modal('hide');
					showpage('{{URL::asset('/myprofile')}}');
           }
		  			$.LoadingOverlay("hide");     
					  $('.modal-backdrop').remove();     

        });

    });

	$( '#addUserForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/uploaddocument') }}',
        }).done(function( data ) 
        {  error_remove (); 
		if(data.success==false)
            {
                $.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                });
				


          }
		  if(data.class == 'danger'){
			{
				showMsg(data.message, "danger");}		  
			}
		  	else
			  {
           
				if(data.class == 'success'){showMsg(data.message, "success");}
				showpage('{{URL::asset('/myprofile')}}'); 
				$.LoadingOverlay("hide");     
	            return false;
          }
		  $.LoadingOverlay("hide");     
        });
    });
});

function EmailVerify(email)
{
    
        $.LoadingOverlay("show");
        $.ajax({
            dataType: 'json',
        type: "POST",
        url: '{{ URL::to('/email-verify') }}',
        data: { email: email },
        success: function(data)
        {
            $.LoadingOverlay("hide");
            if(data.success==false)
            {
                if(data.class == 'danger'){showMsg("Something Went Wrong", "danger");}

            }else
            {
                if(data.class == 'success'){showMsg(data.message, "success");}
                
                 showpage('{{URL::asset('/myprofile')}}');
            }
           
        }
        });
    
    
}

	function MobileVerify(mobile) 
	{
		$('#UserModal').html(''); 
		$(".form-title").text('Enter mobile otp');
		$(".desc").text('(Sent otp on your mobile please check mobile)');
		$('#UserModal').load('{{ URL::to('/mobile-verify') }}'+'/'+mobile);
		$("#myModal").modal();
	}
	function manage() {
        var document_submit = document.getElementById('document_submit');
      
		var upload_id=document.getElementById('upload_id');
		var tax_id_document=document.getElementById('tax_id_document');
		<?php 
			if($userdata->mobile_verify=='0'){
				$mv=0;
			}else{
				$mv=1;
			}
			
		?>
			var mobile_veirfy=<?php echo $mv;?>;
			///alert(mobile_veirfy);
		<?php 	if($userdata->type=='2' || $userdata->type=='4') {?>
			var experience = document.getElementById('experience');
            var about_me = document.getElementById('about_me');
			if (experience.value != '' && experience.value != 0 &&  about_me.value != '' && upload_id.value != '' && mobile_veirfy==1 ) {
            document_submit.disabled = false;
        }
        else {
            document_submit.disabled = true;
        }
	<?php } else{?>
		if ( upload_id.value != ''  && tax_id_document.value!="" && mobile_veirfy==1) {
            document_submit.disabled = false;
        }
        else {
            document_submit.disabled = true;
        }
	<?php } ?>
        
    }    
</script> 
	<style>
	.sieldest-list {
		display: inline-block;
		width: 100%;
		border: 1px solid #d2e9f3;
		border-radius: 5px;
	}
	.sieldest-list li {
		list-style: none;
		padding: 15px;
		// border-bottom: 1px solid #d2e9f3;
	}
	.upload-name {
		border: 0px;
		background: transparent;
		position: relative;
		top: 10px;
		padding-left: 15px;
	}
	</style>

<script type="text/javascript" src="{{ URL::asset('/public/assets/js/lodash.min.js') }}"></script>
<section class="page">
<div class="row">
	<div class="col-md-12 col-lg-12 col-xl-12">
		<div class="surveyors ports">
			<h4>Account</h4>
			<span class="right-arrow editportc">
					<a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#editProfileModel" ><i class="fas fa-edit" aria-hidden="true"></i></a>
				</span>
			<div class="profile_picther">
				<div class="picther_center">
					<span class="picther_inner">
					@if($userdata->profile_pic!="")
						<img src="{{ URL::asset('/public/media/users') }}/{{$userdata->profile_pic}}" alt="{{$userdata->profile_pic}}">
						@else
						<img src="{{ URL::asset('/public/media/') }}/no-image.png" alt="No Image">

						@endif
					</span>
				</div>
				
				<h3>{{$userdata->first_name}} {{$userdata->last_name }}</h3>
				<?php
								$userser=new App\User;
								
								$usera_id=$userdata->id;

								if($userdata->created_by!="" || $userdata->created_by!="0"){

									$createdby =  $userser->select('users.*')->where('users.id',$userdata->created_by)->first(); 
									
									
								
								}else{
									$createdby =  $userser->select('users.*')->where('users.id',$usera_id)->first(); 

								}?>
				<span class="location"><i class="fas fa-map-marker-alt"></i> {{$createdby->city}}, {{$userdata->country_name}}</span>
				<h6>{{$userdata->email}}</h6>
				<!---@if($userdata->email_verify=='0') <span style="color:red;font-size:13px;">Unverified</span> 
				<a href="javaScript:Void(0);" class="verfy-btn" onclick="EmailVerify('{{$userdata->email}}');" title="Email Verify"> Verify </a>   
				@else <span style="color:green;font-size:13px;"> verify</span> @endif---!>
			</div>

			<div class="company_details">
				<h2> User Details</h2>
				
				<div class="row">
					<div class="col-md-12">
						<ul class="company_details_list">
						<?php 
						if(!empty( $userdata->street_address) || !empty( $userdata->state) || !empty( $userdata->city) || !empty( $userdata->pincode))
						{
							$address=$userdata->street_address.','.$userdata->state.','.$userdata->city;
						}
						$company=	$userdata->company;

						$street_address=$userdata->street_address;

						$state=$userdata->state;
						$city=$userdata->city;
						$pincode=$userdata->pincode;
						$mailing_address=$userdata->mailing_address;
						$company_website=$userdata->company_website;

						?>
						@if ($userdata->created_by!="" && ($userdata->type=='1' || $userdata->type=='3'  ))
						

						<?php
								$userser=new App\User;
								
								$usera_id=$userdata->id;

								if($userdata->created_by!="" || $userdata->created_by!="0"){

									$createdby =  $userser->select('users.*')->where('users.id',$userdata->created_by)->first(); 
									
									//dd($createdby);
									$company=	$createdby->company;
									if(!empty( $createdby->street_address) || !empty( $createdby->state) || !empty( $createdby->city) || !empty( $createdby->pincode))
									{
										$address=$createdby->street_address.','.$createdby->state.','.$createdby->city;
									}
									$address=$address;
									$street_address=$createdby->street_address;
									$state=$createdby->state;
									$city=$createdby->city;
									$pincode=$createdby->pincode;
									$mailing_address=$createdby->mailing_address;
									$company_website=$createdby->company_website;
								}else{
									$createdby =  $userser->select('users.*')->where('users.id',$usera_id)->first(); 

								}
								

						 ?>
						<li> <span> Designated Person:</span> {{$createdby->first_name}}  {{$createdby->last_name}}</li>
						<li> <span> Designated Email:</span> {{$createdby->email}}  </li>

						@endif	
						
						@if(!empty($userdata->email))
							<li><span>Email Address:</span>{{$userdata->email}} 
								@if($userdata->email_verify=='0') 
								<a href="javaScript:Void(0);" class="verfy-btn" onclick="EmailVerify('{{$userdata->email}}');" title="Email Verify"> Verify </a>   
								@else 							
								<a href="javaScript:Void(0);" class="verfy-btn sucgreen" > Verified  </a>   
								@endif 
							 </li>
						@endif

						@if($userdata->type=='2' )
						<li> <span> Company Name:</span> @if($company ) {{$company }} @endif</li>
						<li> <span> Company Website:</span> @if($company_website  ) {{$company_website  }} @endif</li>

						@endif
						<li>
							<span>Phone Number :</span>{{$userdata->country_code}} {{$userdata->mobile}}
							@if($userdata->mobile_verify=='0') 
								<a href="javaScript:Void(0);" class="verfy-btn" onclick="MobileVerify('{{$userdata->email}}');" title="Mobile Verify"> Verify </a>   
								@else 							
								<a href="javaScript:Void(0);" class="verfy-btn sucgreen" > Verified  </a>   
								@endif 
							
							
						</li>
						<li> <span> @if($userdata->type=='4') Mailing Address @else Company Address (Invoice Address) @endif:</span>
								<span class="invocead" style="display: inline-grid;">
									<p> <b style="width:130px">Street Address:</b> <span class="us-ad">{{$street_address}}</p>
									<p>	<b>City:</b> {{$city}}</p>
									<p>	<b>State:</b> {{$state}}</p>
									<p>	<b>Zip:</b> {{$pincode}}</p>
									<p>	<b>Country:</b> {{$userdata->country_name}}</p>
								</span>
									
 						</li>

						 			@if($userdata->type=='2' && $userdata->is_surveyor=='1')
										<li>
											<i class="fas fa-check-square"></i>&nbsp; Designated person is also a surveyor that conducts surveys
									 	</li>
									 @endif	
									 
									 @if($userdata->type=='2' || $userdata->type=='3' || $userdata->type=='4')
									 <li> <span> Job Acceptance Rate:</span>{{$percentage_job_acceptance}}%</li>
									 <li> <span> Average Response Time:</span>{{$average_response_time}}</li>

									 @endif
									 
									 <li> <span> Number of Surveys:</span><?php $helper=new App\Helpers; 
									 echo $helper->NoOfSurvey($userdata->id);?></li>
						</ul>
						<div class="user-info-field">
							{!! Form::open(array('url' => '/uploaddocument', 'method' => 'post','name'=>'addUserForm','files'=>true,'novalidate' => 'novalidate','id' => 'addUserForm')) !!}
							
								@if($userdata->type=='0' || $userdata->type=='2' || $userdata->type=='4')

								
										<ul class="sieldest-list">
										
										

										@if($userdata->type=='2' || $userdata->type=='4')

											<li> <span> Years of Experience <span style="color:red">* </span>:</span>

											{!! Form::select('experience', ['Select Experience','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','10+'=>'10+'],$userdata->experience, ['class' => 'form-control','required'=>'required','id'=>'experience' ,'onChange'=>"manage()"]) !!} 
											
											</li>
											<li> <span> About Me<span style="color:red">* </span>:</span>{!! Form::textarea('about_me', $userdata->about_me, ['class' => 'form-control','placeholder' => 'About Me','required'=>'required','id'=>'about_me','onkeyup'=>"manage()"]) !!} </li>
										@endif
										@if($userdata->type=='4')
										    
												<li> <span> SSN (For USA only)#:</span>{!! Form::text('ssn', $userdata->ssn, ['class' => 'form-control','placeholder' => 'SSN #','required'=>'required','id'=>'ssn']) !!} </li>
											
												<li> <span> Tax ID (other than USA)  #:</span>{!! Form::text('company_tax_id', $userdata->company_tax_id , ['class' => 'form-control','placeholder' => 'Tax ID Number','required'=>'required','id'=>'company_tax_id']) !!} </li>
											
										@endif
										
										@if($userdata->type=='0' || $userdata->type=='2')
											<li>
											<b>Note: </b> Upload at least one of the following documents: Invoice Addressed to the Company, Utility Bill, or Incorporation Certificate.<span style="color:red">*</span>
											<li>

											

											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">Invoice Addressed to Company:
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="invoice_address_to_company" id="myfile" class="form-control input-file" onkeypress="error_remove()">
														<button class="btn-success @if($userdata->invoice_address_to_company!="") sucgreen @endif">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="invoice_address_to_company" value="{{$userdata->invoice_address_to_company}}" disabled>
												</div>

												@if($userdata->invoice_address_to_company!="")
													<div class="col-md-2">
														<img style="width: 50px;" src="{{ URL::asset('/public/media/users/invoice_address_to_company') }}/{{$userdata->invoice_address_to_company}}">
													</div>
												@endif
												</div>
											</li>

											<li>
									<div class="row">
										<label class="col-md-4 control-label Upload-Wire">Utility Bill:</label>
										<div class="col-md-2">
											<div class="upload-file" id="file">
											<input type="file" name="utility_bill" id="myfile" class="form-control input-file" onkeypress="error_remove()">
												<button class="btn-success @if($userdata->utility_bill!="") sucgreen @endif">Upload</button>
											</div>
											
										</div>
										<div class="col-md-4 ">
										<input type="text" class="upload-name" id="utility_bill" value="{{$userdata->utility_bill}}" disabled>								</div>

										@if($userdata->utility_bill!="")
											<div class="col-md-2">
												<img style="width: 50px;" src="{{ URL::asset('/public/media/users/utility_bill') }}/{{$userdata->utility_bill}}">
											</div>
										@endif
										</div>
									</li>
											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">Incorporation Certificate:
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="incorporation_certificate" id="myfile" class="form-control input-file" onkeypress="error_remove()">
														<button class="btn-success @if($userdata->incorporation_certificate!="") sucgreen @endif">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="incorporation_certificate" value="{{$userdata->incorporation_certificate}}" disabled>								
												</div>

												@if($userdata->incorporation_certificate!="")
													<div class="col-md-2">
														<img style="width: 50px;" src="{{ URL::asset('/public/media/users/incorporation_certificate') }}/{{$userdata->incorporation_certificate}}">
													</div>
												@endif
												</div>
											</li>
											<br><br>
											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">Upload ID<span style="color:red">*</span>:
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="upload_id"   class="form-control input-file" onkeypress="error_remove()" onChange="setTimeout(manage, 2000);">
														<button class="btn-success @if($userdata->upload_id!="") sucgreen @endif">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="upload_id"  disabled value="{{$userdata->upload_id}}">								</div>

												@if($userdata->upload_id!="")
													<div class="col-md-2">
														<img src="{{ URL::asset('/public/media/users/upload_id') }}/{{$userdata->upload_id}}" style="width: 50px;">
													</div>
												@endif
												</div>
											</li>
											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">Tax ID Document<span style="color:red">*</span>:
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="tax_id_document"  class="form-control input-file" onkeypress="error_remove()" onChange="setTimeout(manage, 2000);">
														<button class="btn-success @if($userdata->tax_id_document!="") sucgreen @endif">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="tax_id_document" value="{{$userdata->tax_id_document}}" disabled>									</div>

												@if($userdata->tax_id_document!="")
													<div class="col-md-2">
														<img src="{{ URL::asset('/public/media/users/tax_id_document') }}/{{$userdata->tax_id_document}}" style="width: 50px;">
													</div>
												@endif
												</div>
											</li>
											@else
											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">Upload ID (ID can be one of the following passport, driver’s license, Permanent resident card, National Identification Card)<span style="color:red">*</span>:
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="upload_id"  class="form-control input-file" onkeypress="error_remove()" onChange="setTimeout(manage, 2000);">
														<button class="btn-success @if($userdata->upload_id!="") sucgreen @endif">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="upload_id" value="{{$userdata->upload_id}}"  disabled>										</div>

												@if($userdata->upload_id!="")
													<div class="col-md-2">
														<img src="{{ URL::asset('/public/media/users/upload_id') }}/{{$userdata->upload_id}}" style="width: 50px;">
													</div>
												@endif
												</div>
											</li>
											<li>
											<li>
												<div class="row">
													<label class="col-md-4 control-label Upload-Wire">Diploma:
													</label>
													<div class="col-md-2">
														<div class="upload-file" id="file">
														<input type="file" name="diploma" id="myfile" class="form-control input-file" onkeypress="error_remove()">
															<button class="btn-success @if($userdata->diploma!="") sucgreen @endif">Upload</button>
														</div>
														
													</div>
													<div class="col-md-4 ">
													<input type="text" class="upload-name" id="diploma" value="{{$userdata->diploma}}" disabled>
													</div>

													@if($userdata->diploma!="")
														<div class="col-md-2">
															<img style="width: 50px;" src="{{ URL::asset('/public/media/users/diploma') }}/{{$userdata->diploma}}">
														</div>
													@endif
													</div>
												</li>
												
												<li>
												<div class="row">
													<label class="col-md-4 control-label Upload-Wire">Employment reference letter:
													</label>
													<div class="col-md-2">
														<div class="upload-file" id="file">
														<input type="file" name="employment_reference_letter" id="myfile" class="form-control input-file" onkeypress="error_remove()">
															<button class="btn-success  @if($userdata->employment_reference_letter!="") sucgreen @endif">Upload</button>
														</div>
														
													</div>
													<div class="col-md-4 ">
													<input type="text" class="upload-name" id="employment_reference_letter" value="{{$userdata->employment_reference_letter}}" disabled>
													</div>

													@if($userdata->employment_reference_letter!="")
														<div class="col-md-2">
															<img style="width: 50px;" src="{{ URL::asset('/public/media/users/employment_reference_letter') }}/{{$userdata->employment_reference_letter}}">
														</div>
													@endif
													</div>
												</li>
												<li>
												<div class="row">
													<label class="col-md-4 control-label Upload-Wire">Certificates:
													</label>
													<div class="col-md-2">
														<div class="upload-file" id="file">
														<input type="file" name="certificates" id="myfile" class="form-control input-file" onkeypress="error_remove()">
															<button class="btn-success @if($userdata->certificates!="") sucgreen @endif">Upload</button>
														</div>
														
													</div>
													<div class="col-md-4 ">
													<input type="text" class="upload-name" id="certificates" value="{{$userdata->certificates}}" disabled>
													</div>

													@if($userdata->certificates!="")
														<div class="col-md-2">
															<img style="width: 50px;" src="{{ URL::asset('/public/media/users/certificates') }}/{{$userdata->certificates}}">
														</div>
													@endif
													</div>
												</li>
												<li>
												<div class="row">
													<label class="col-md-4 control-label Upload-Wire">Port Gate Entry Pass:
													</label>
													<div class="col-md-2">
														<div class="upload-file" id="file">
														<input type="file" name="port_gate_pass" id="myfile" class="form-control input-file" onkeypress="error_remove()">
															<button class="btn-success @if($userdata->port_gate_pass!="") sucgreen @endif">Upload</button>
														</div>
														
													</div>
													<div class="col-md-4 ">
													<input type="text" class="upload-name" id="port_gate_pass" value="{{$userdata->port_gate_pass}}" disabled>
													</div>

													@if($userdata->port_gate_pass!="")
														<div class="col-md-2">
															<img style="width: 50px;" src="{{ URL::asset('/public/media/users/port_gate_pass') }}/{{$userdata->port_gate_pass}}">
														</div>
													@endif
													</div>
												</li>

											@endif

											@if($userdata->type=='2' &&  $userdata->country_id=='170')
												<li>
												<div class="row">
													<label class="col-md-4 control-label Upload-Wire">SAC Document:
													</label>
													<div class="col-md-2">
														<div class="upload-file" id="file">
														<input type="file" name="sac_document" id="myfile" class="form-control input-file" onkeypress="error_remove()">
															<button class="btn-success  @if($userdata->sac_document!="") sucgreen @endif">Upload</button>
														</div>
														
													</div>
													<div class="col-md-4 ">
													<input type="text" class="upload-name" id="sac_document" value="{{$userdata->sac_document}}" disabled>										</div>

													@if($userdata->sac_document!="")
														<div class="col-md-2">
															<img src="{{ URL::asset('/public/media/users/sac_document') }}/{{$userdata->sac_document}}" style="width: 50px;">
														</div>
													@endif
													</div>
												</li>
											@endif
											@if($userdata->type=='2' || $userdata->type=='4')
												<li>
												<div class="row">
													<label class="col-md-4 control-label Upload-Wire">Picture:
													</label>
													<div class="col-md-2">
														<div class="upload-file" id="file">
														<input type="file" name="profile_pic" id="myfile" class="form-control input-file" onkeypress="error_remove()">
															<button class="btn-success @if($userdata->profile_pic!="") sucgreen @endif" >Upload</button>
														</div>
														
													</div>
													<div class="col-md-4 ">
													<input type="text" class="upload-name" id="profile_pic" value="{{$userdata->profile_pic}}"  disabled>										</div>

													@if($userdata->profile_pic!="")
														<div class="col-md-2">
															<img src="{{ URL::asset('/public/media/users') }}/{{$userdata->profile_pic}}" style="width: 50px;">
														</div>
													@endif
													</div>
												</li>
											@endif
											<li>
											<div class="row">
													
													<div class="col-md-12">
													<p><b>Note: </b><span style="color:red">*</span> Indicates mandatory uploads.</p>

													</div>
														
											</div>
											</li>
											<div class="login-inner text-center" style="width:100%">
												<button type="submit" id="document_submit" class="btn btn-primary" style="width: auto;padding: 12px 57px;"  >Submit</button>
												
											</div>
											</ul>
							
								@endif
							{!! Form::close() !!}
					  </div>
									
									<!-- <div class="comments-list">
							<h4>Comments</h4>
							<ul>
								<li>
									<span class="user-commentinfo-img"><img src="img/user_img.png" alt="#"></span>
									<span class="user-commentinfo">
										<h4>Steve smith</h4>
										<p>Lorem Ipsum is simply dummy text of the printing and  typesetting industry. Lorem Ipsum has been the standard dummy text ever since the 1500s.</p>
									</span>
								</li>
								<li>
									<span class="user-commentinfo-img"><img src="img/user_img.png" alt="#"></span>
									<span class="user-commentinfo">
										<h4>Steve smith</h4>
										<p>Lorem Ipsum is simply dummy text of the printing and  typesetting industry. Lorem Ipsum has been the standard dummy text ever since the 1500s.</p>
									</span>
								</li>
							</ul>
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
				
			</section>
			<div class="modal login-modal fade" id="editProfileModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					<div class="modal-dialog" role="document">
					<div class="modal-content">
					<div class="modal-header">
					<h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Edit Profile</h5>
				</div>
				<div class="modal-body">
					<div class="login-inner">
					{!! Form::open(array('url' => 'editprofilepost', 'method' => 'post','name'=>'MyAgentsForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyAgentsForm')) !!}

						<div class="col-12 col-sm-12 col-md-12">
						<label class="col-md-12 col-sm-12 col-xs-12 control-label">Profile:</label>
							<div class="row">
								<div class="col-md-12 text-center">
								<div class="picther_center">
									<span class="picther_inner">

									@if($userdata->profile_pic!="")
									<img src="{{ URL::asset('/public/media/users') }}/{{$userdata->profile_pic}}" alt="{{$userdata->profile_pic}}">
									@else
									<img src="{{ URL::asset('/public/media/') }}/no-image.png" alt="No Image">

									@endif
										
										
									</span>
									<span class="import-excel">
											<input type="file" name="image" id="technician_category_image" class="form-control input-file" onkeypress="error_remove()">
											<button class="btn btn-outline-success">Browse</button>
											<i class="fas fa-camera"></i>
										</span>
								</div>
										
								</div>
								
							</div>
						</div>
						
							<div class="form-group">
							{!! Form::text('first_name', $userdata->first_name, ['class' => 'form-control','placeholder' => 'First Name','required'=>'required','id'=>'first_name','disabled'=>'disabled']) !!}
								@if ($errors->has('first_name')) <p class="alert alert-danger">{{ $errors->first('first_name') }}</p> @endif
							</div>
						<div class="form-group">
						{!! Form::text('last_name', $userdata->last_name, ['class' => 'form-control','placeholder' => 'Last Name','required'=>'required','id'=>'last_name','disabled'=>'disabled']) !!}
							@if ($errors->has('last_name')) <p class="alert alert-danger">{{ $errors->first('last_name') }}</p> @endif
							
						</div>
						<div class="form-group">
						{!! Form::text('email', $userdata->email, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email']) !!}
							@if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
							
						</div>
						<div class="form-group">
						{!! Form::text('mobile', $userdata->mobile, ['class' => 'form-control','placeholder' => 'Mobile','required'=>'required','id'=>'mobile']) !!}
							@if ($errors->has('mobile')) <p class="alert alert-danger">{{ $errors->first('mobile') }}</p> @endif
						</div>
						<div class="form-group">
						{!! Form::text('company', $userdata->company, ['class' => 'form-control','placeholder' => 'company','required'=>'required','id'=>'company','disabled'=>'disabled']) !!}
							@if ($errors->has('company')) <p class="alert alert-danger">{{ $errors->first('company') }}</p> @endif
						</div>
						<div class="row user-info-field">
							<fieldset>
								<legend>@if($userdata->type=="4") Mailing Address @else Company Address  @endif:</legend>
									<div class="form-group">
									{!! Form::text('street_address', $street_address, ['class' => 'form-control','placeholder' => 'Street address','required'=>'required','id'=>'street_address']) !!}
										@if ($errors->has('street_address')) <p class="alert alert-danger">{{ $errors->first('street_address') }}</p> @endif
									</div>
									<div class="form-group">
									{!! Form::text('city', $city, ['class' => 'form-control','placeholder' => 'City','required'=>'required','id'=>'city']) !!}
										@if ($errors->has('city')) <p class="alert alert-danger">{{ $errors->first('city') }}</p> @endif
									</div>
									<div class="form-group">
									{!! Form::text('state', $state, ['class' => 'form-control','placeholder' => 'State','required'=>'required','id'=>'state']) !!}
										@if ($errors->has('state')) <p class="alert alert-danger">{{ $errors->first('state') }}</p> @endif
									</div>
									<div class="form-group">
									{!! Form::text('pincode', $pincode, ['class' => 'form-control','placeholder' => 'Zip','required'=>'required','id'=>'pincode']) !!}
										@if ($errors->has('pincode')) <p class="alert alert-danger">{{ $errors->first('pincode') }}</p> @endif
									</div>
									<div class="form-group">
									<?php $helper=new App\Helpers;?>
                                		{!! Form::select('country',$helper->CountryList(), $userdata->country_id, ['class' => 'form-control','required'=>'required','id'=>'country','onchange'=>'Getcountrycode();']) !!}
										@if ($errors->has('pincode')) <p class="alert alert-danger">{{ $errors->first('pincode') }}</p> @endif
									</div>

							</fieldset>
							</div>
						@if($userdata->type=='2')
						<div class="form-group">
						
							<span class="custom_check">Dp is also surveyor &nbsp;
							{!! Form::checkbox('is_surveyor','1',$userdata->is_surveyor,['class' => 'form-control','id'=>'is_surveyor']) !!}
                           <span class="check_indicator">&nbsp;</span></span>

						</div>
						@endif
						@if($userdata->type=='2' || $userdata->type=='4')
						<div class="form-group"><level>Years of Experience:</level>
						{!! Form::select('experience', ['Select Experience','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','10+'=>'10+'],$userdata->experience, ['class' => 'form-control','required'=>'required','id'=>'experience' ,'onChange'=>"manage()"]) !!} 
							@if ($errors->has('experience')) <p class="alert alert-danger">{{ $errors->first('experience') }}</p> @endif
						</div>
						<div class="form-group">
						<level>About Me:</level>
						{!! Form::textarea('about_me', $userdata->about_me, ['class' => 'form-control','placeholder' => 'About Me','required'=>'required','id'=>'about_me','onkeyup'=>"manage()"]) !!}							@if ($errors->has('about_me')) <p class="alert alert-danger">{{ $errors->first('about_me') }}</p> @endif
						</div>
						@endif
						<button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>

						
						{!! Form::close() !!}
					</div>
				</div>
				</div>
			</div>
			</div>
			<div id="myModal" class="modal fade form-modal" data-keyboard="false"  role="dialog" style="display: none;">
			<div class="modal-dialog modal-lg modal-big">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">
						<i class="fa fa-user"></i>&nbsp;&nbsp;<span class='form-title'></span>
						</h4><p class="desc" style="margin: 8px;"></p>
						
						<button type="button" class="close subbtn" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<div class="modal-body" id="UserModal">

					</div>
				</div>
			</div>
		</div> 