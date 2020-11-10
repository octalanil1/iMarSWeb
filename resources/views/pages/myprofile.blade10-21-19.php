<script type="text/javascript">
    $(document).ready(function () 
    {
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
        {  error_remove (); if(data.success==false)
            {
                $.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                });

          }else{
           
            if(data.class == 'success'){showMsg(data.message, "success");}
			showpage('{{URL::asset('/myprofile')}}');
        
            
		  $.LoadingOverlay("hide");     
         
	            return false;
          }
            
                         
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

function MobileVerify(mobile) {

$('#UserModal').html(''); 
$(".form-title").text('Enter mobile otp');
$(".desc").text('(Sent otp on your mobile please check mobile)');
$('#UserModal').load('{{ URL::to('/mobile-verify') }}'+'/'+mobile);
$("#myModal").modal();
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
<script type="text/javascript" src="{{ URL::asset('/assets/js/lodash.min.js') }}"></script>

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
			    						<img src="{{ URL::asset('/media/users') }}/{{$userdata->profile_pic}}" alt="#">
			    					</span>
								</div>
								
		    					<h3>{{$userdata->first_name.' '.$userdata->last_name}}</h3>
		    					<span class="location"><i class="fas fa-map-marker-alt"></i> {{$userdata->city}}, {{$userdata->country_name}}</span>
								<h6>{{$userdata->email}}</h6>
								@if($userdata->email_verify=='0') <span style="color:red;">Unverified</span> 
								<a href="javaScript:Void(0);" class="verfy-btn" onclick="EmailVerify('{{$userdata->email}}');" title="Email Verify"> Verify </a>   
                               @else <span style="color:green;"> verify</span> @endif
		    				</div>

		    				<div class="company_details">
		    					<h2>User Details</h2>
		    					<div class="row">
		    						<div class="col-md-12">
		    							<ul class="company_details_list">
										<li>
														<span>Country name:</span>{{$userdata->country_name}}
													</li>
													<li>
														<span>Phone Number :</span>{{$userdata->mobile}}
														@if($userdata->mobile_verify=='0') <span style="color:red;">Unverified</span> 
								                           <a href="javaScript:Void(0);" class="verfy-btn" onclick="MobileVerify('{{$userdata->mobile}}');" title="Mobile Verify"> Verify </a>   
                                                        @else <span style="color:green;"> verify</span> @endif
													</li>
											<?php $user = Auth::user();?>
											@if($user->type=='2' || $user->type=='0')
													@if(!empty($userdata->company))
													<li>
														<span>Company name:</span>{{$userdata->company}}
													</li>
													@endif
													@if(!empty($userdata->company_address))
													<li>
														<span>Company address(Invoice address):</span>{{$userdata->company_address}}
													</li>
													@endif
													@if(!empty($userdata->email))
													<li>
														<span>Email Address:</span>{{$userdata->email}}
													</li>
													@endif
												@if(!empty($userdata->company_tax_id))
												<li>
														<span>Company Tax ID :</span>{{$userdata->company_tax_id}}
													</li>
												@endif
												<li>
														<span>Website:</span>{{$userdata->company_website }}
													</li>

													<li>
														<span>Designated Person :</span>{{$userdata->first_name }} {{$userdata->last_name }}
													</li>
													
													



											@endif
											<?php
											if ($user->type=='2')
											{ ?>
											   <li>
											   @if($userdata->is_surveyor=='1')
												
											   <i class="fas fa-check-square"></i>&nbsp; Designated person is also a surveyor that conducts surveys
												

												
												@endif
												</li>
												
												<li>
													<span>Number of surveys :</span>{{$survey_count}}
												</li>
												
												<li>
													<span>Experience :</span>@if(!empty($userdata->experience)) {{$userdata->experience}} years @endif
												</li>
												
											
												<li>
													<span>About me:</span>
													<p>{{$userdata->about_me}}</p>
												</li>
												
												
												<li><span>Rating :</span> 
												<?php $rating=$userdata->rating;?>
												<div>
													<i class="fas fa-star <?php  if($rating>=1){?> checked <?php } ?>"></i>
													<i class="fas fa-star <?php  if($rating>=2){?> checked <?php } ?>"></i>
													<i class="fas fa-star <?php  if($rating>=3){?> checked <?php } ?>"></i>
													<i class="fas fa-star <?php  if($rating>=4){?> checked <?php } ?>"></i>
													<i class="fas fa-star <?php  if($rating>=5){?> checked <?php } ?>"></i></div>
												</li>
												
												
												<li>
													<span>Average Response Time :</span> {{$userdata->average_response_time}}
												</li>
												
												
												<li>
													<span>Job Acceptance :</span> @if($userdata->percentage_job_acceptance!="") {{$userdata->percentage_job_acceptance}} % @else @endif
												</li>
												
											<?php } ?>
											
		    							</ul>
										<div class="user-info-field">
										{!! Form::open(array('url' => '/uploaddocument', 'method' => 'post','name'=>'addUserForm','files'=>true,'novalidate' => 'novalidate','id' => 'addUserForm')) !!}
										@if($userdata->type=='0' || $userdata->type=='1')
                					<fieldset>
               							<legend>User Documents:</legend>
										   <ul class="sieldest-list">
											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">Diploma:
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="diploma" id="myfile" class="form-control input-file" onkeypress="error_remove()">
														<button class="btn-success">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="input-file-placeholder" placeholder="File name" disabled>
												</div>

												@if($userdata->diploma!="")
													<div class="col-md-2">
														<img style="width: 50px;" src="{{ URL::asset('/media/users/diploma') }}/{{$userdata->diploma}}">
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
														<button class="btn-success">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="input-file-placeholder" placeholder="File name" disabled>
												</div>

												@if($userdata->diploma!="")
													<div class="col-md-2">
														<img style="width: 50px;" src="{{ URL::asset('/media/users/employment_reference_letter') }}/{{$userdata->employment_reference_letter}}">
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
														<button class="btn-success">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="input-file-placeholder" placeholder="File name" disabled>
												</div>

												@if($userdata->diploma!="")
													<div class="col-md-2">
														<img style="width: 50px;" src="{{ URL::asset('/media/users/certificates') }}/{{$userdata->certificates}}">
													</div>
												@endif
												</div>
											</li>
											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">Port Gate Pass:
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="port_gate_pass" id="myfile" class="form-control input-file" onkeypress="error_remove()">
														<button class="btn-success">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="input-file-placeholder" placeholder="File name" disabled>
												</div>

												@if($userdata->diploma!="")
													<div class="col-md-2">
														<img style="width: 50px;" src="{{ URL::asset('/media/users/port_gate_pass') }}/{{$userdata->port_gate_pass}}">
													</div>
												@endif
												</div>
											</li>
											</ul>
											<div class="form-btn text-center">
											<div class="col-sm-12 p-r-30">
											<div class="col-md-12"> 
											{!! Form::submit('Save',['class' => 'btn btn-primary btn-flat subbtn', 'type' => 'submit']) !!}
											</div>
    									</div>
								</fieldset>
								@else 
								<fieldset>
               						<legend>User Documents:</legend>
									    <ul class="sieldest-list">
											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">An invoice addressed to the company
													Utility bill (Phone, electricity, internet etc):
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="utility_bill" id="myfile" class="form-control input-file" onkeypress="error_remove()">
														<button class="btn-success">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="input-file-placeholder" placeholder="File name" disabled>
												</div>

												@if($userdata->utility_bill!="")
													<div class="col-md-2">
														<img style="width: 50px;" src="{{ URL::asset('/media/users/utility_bill') }}/{{$userdata->utility_bill}}">
													</div>
												@endif
												</div>
											</li>
											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">Incorporation certificate:
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="incorporation_certificate" id="myfile" class="form-control input-file" onkeypress="error_remove()">
														<button class="btn-success">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="input-file-placeholder" placeholder="File name" disabled>
												</div>

												@if($userdata->incorporation_certificate!="")
													<div class="col-md-2">
														<img style="width: 50px;" src="{{ URL::asset('/media/users/incorporation_certificate') }}/{{$userdata->incorporation_certificate}}">
													</div>
												@endif
												</div>
											</li>
											<li>
											<div class="row">
												<label class="col-md-4 control-label Upload-Wire">Upload ID (ID can be one of the following passport, driver’s license, Permanent resident card, National Identification Card) :
												</label>
												<div class="col-md-2">
													<div class="upload-file" id="file">
													<input type="file" name="upload_id" id="myfile" class="form-control input-file" onkeypress="error_remove()">
														<button class="btn-success">Upload</button>
													</div>
													
												</div>
												<div class="col-md-4 ">
												<input type="text" class="upload-name" id="input-file-placeholder" placeholder="File name" disabled>
												</div>

												@if($userdata->upload_id!="")
													<div class="col-md-2">
														<img src="{{ URL::asset('/media/users/upload_id') }}/{{$userdata->upload_id}}" style="width: 50px;">
													</div>
												@endif
												</div>
											</li>
											<div class="login-inner" style="width:350px">
												<button type="submit" class="btn btn-primary">Submit</button>
												
											</div>
										   </ul>
										
								</fieldset>
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
					<h5 class="modal-title"><img src="{{ URL::asset('/media') }}/logo-icon.png" alt="">Edit Profile</h5>
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
										<img src="{{ URL::asset('/media/users') }}/{{$userdata->profile_pic}}" alt="#">
										
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
						<div class="form-group">
						{!! Form::text('company_address', $userdata->company_address, ['class' => 'form-control','placeholder' => 'company address','required'=>'required','id'=>'company_address']) !!}
							@if ($errors->has('company_address')) <p class="alert alert-danger">{{ $errors->first('company_address') }}</p> @endif
						</div>
						<div class="form-group">
						{!! Form::text('street_address', $userdata->street_address, ['class' => 'form-control','placeholder' => 'Street address','required'=>'required','id'=>'street_address']) !!}
							@if ($errors->has('street_address')) <p class="alert alert-danger">{{ $errors->first('street_address') }}</p> @endif
						</div>
						<div class="form-group">
						{!! Form::text('city', $userdata->city, ['class' => 'form-control','placeholder' => 'City','required'=>'required','id'=>'city']) !!}
							@if ($errors->has('city')) <p class="alert alert-danger">{{ $errors->first('city') }}</p> @endif
						</div>
						<div class="form-group">
						{!! Form::text('state', $userdata->state, ['class' => 'form-control','placeholder' => 'State','required'=>'required','id'=>'state']) !!}
							@if ($errors->has('state')) <p class="alert alert-danger">{{ $errors->first('state') }}</p> @endif
						</div>
						<div class="form-group">
						{!! Form::text('pincode', $userdata->pincode, ['class' => 'form-control','placeholder' => 'Zip','required'=>'required','id'=>'pincode']) !!}
							@if ($errors->has('pincode')) <p class="alert alert-danger">{{ $errors->first('pincode') }}</p> @endif
						</div>
						@if($userdata->type=='2')
						<div class="form-group">
						
							<span class="custom_check">Dp is also surveyor &nbsp;
							{!! Form::checkbox('is_surveyor','1',$userdata->is_surveyor,['class' => 'form-control','id'=>'is_surveyor']) !!}
                           <span class="check_indicator">&nbsp;</span></span>

						</div>
						@endif
						<button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/media') }}/arrow.png" alt="#"></button>

						
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