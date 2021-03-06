@extends('layouts.master')
@section('title') iMarS | Signup @stop

@section('content')  
<style>
	 #terms_conditions
	  {
		float: left;
		width: auto;
		margin-right: 7px;
	}
</style>
<script type="text/javascript">
window.onload = function() {
 const myInput = document.getElementById('confirm_password');
 myInput.onpaste = function(e) {
   e.preventDefault();
 }
}
    $(document).ready(function () 
    {
        $( '#operatorsignupForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/bycompanysurveyorsignuppost') }}',
        }).done(function( data ) 
        {  error_remove (); 
			if(data.success==false)
            {
				
				if(data.message==false)
				{
					$.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					if(key=="mobile"){
						$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('.country-code-outer'));

					}else{
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
					}                   });
				}
				else{
					
					if(data.class == 'success'){showMsg(data.message, "danger");}
				}
				  

          }else
		  {
          
            if(data.class == 'success'){showMsg(data.message, "success");}
           
			var frm = document.getElementsByName('operatorsignupForm')[0];
				frm.reset();  
			
			//window.location = "{{URL::asset('/myaccount')}}";
	                  }
            
		  $.LoadingOverlay("hide");     
        }).fail(function(data) {
			$.LoadingOverlay("hide");

			showMsg('Sorry, an error has occurred during sign up. Please try again later!', "danger");
               // window.location.reload();
            });


    });
});

function Getcountrycode() 
    { $.LoadingOverlay("show");   
      $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
              var country_id=document.getElementById("country").value;  
           // alert(country_id);
      $.ajax({
            
            data: { country_id:country_id}, 
            type: "POST",
            url: '{{ URL::to('/getcountrycode') }}',
        }).done(function( data ) 
        {  // alert(data);
            document.getElementById("country_code").innerHTML =data;  
            $.LoadingOverlay("hide");   
           
          
        });
        
    }
</script> 

<section class="signup-outer">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="appoint-inner">
						<div class="form-header">
							<span class="user-icon">
								<img src="{{ URL::asset('/public/media') }}/icon-2.png" alt="#" class="img-fluid">
							</span>
							<span class="user-content">
								<h4>Sign up to Conduct Surveys</h4>
								<p>Start your own business</p>
							</span>
						</div>
                        {!! Form::open(array('url' => 'bycompanysurveyorsignuppost', 'method' => 'post','name'=>'operatorsignupForm','files'=>true,'novalidate' => 'novalidate','id' => 'operatorsignupForm')) !!}
						  <div class="row">
						  <div class="col-md-6">
						  		<div class="form-group">
								    <label >FirstName<sup>*</sup></label>
                                    {!! Form::text('first_name',null, ['class' => 'form-control','placeholder' => 'First Name','required'=>'required','id'=>'first_name','onkeypress' => 'error_remove()' ]) !!}
								 </div>
						  	</div>
						  	<div class="col-md-6">
						  		<div class="form-group">
								    <label >LastName<sup>*</sup></label>
                                    {!! Form::text('last_name',null, ['class' => 'form-control','placeholder' => 'Last Name','required'=>'required','id'=>'last_name','onkeypress' => 'error_remove()' ]) !!}   
								 </div>
						  	</div>
						  	<div class="col-md-12">
						  		<div class="form-group">
								    <label >Enter your email <sup>*</sup></label>
                                    {!! Form::text('email',$user->email, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email','onkeypress' => 'error_remove()' ]) !!}   
								 </div>
								 {!! Form::hidden('id',base64_encode($user->id),['id'=>'id']) !!}

						  	</div>
						  	<div class="col-md-6">
						  		<div class="form-group">
                                  <label >Country <sup>*</sup></label>
                                  <?php $helper=new App\Helpers;?>
                                {!! Form::select('country',$helper->CountryList(),null, ['class' => 'form-control','required'=>'required','id'=>'country','onchange'=>'Getcountrycode();']) !!}

								   
								 </div>
						  	</div>
						  	<div class="col-md-6">
						  		<div class="form-group">
									<label >Phone number <sup>*</sup></label>
									<div class="country-code-outer">
									<span id="country_code">+00</span>
                                    {!! Form::text('mobile',null, ['class' => 'form-control','placeholder' => 'Mobile ','required'=>'required','id'=>'mobile','onkeypress' => 'error_remove()' ]) !!}
									</div>   	
								</div>
						  	</div>
						  	
						  	<div class="col-md-6">
						  		<div class="form-group">
								    <label >Password<sup>*</sup></label>
                                    {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Password ','required'=>'required','id'=>'password','onkeypress' => 'error_remove()' ]) !!}   
								 </div>
						  	</div>
						  	<div class="col-md-6">
						  		<div class="form-group">
								    <label >Re-enter Password<sup>*</sup></label>
                                    {!! Form::password('confirm_password', ['class' => 'form-control','placeholder' => 'Confirm Password ','required'=>'required','id'=>'confirm_password','onkeypress' => 'error_remove()' ]) !!}   
								 </div>
							  </div>
							  <div class="col-md-12">
						  		<div class="form-group d-flex">
                                    {!! Form::checkbox('terms_conditions',null,null, ['class' => 'form-control','required'=>'required','id'=>'terms_conditions','onkeypress' => 'error_remove()' ]) !!}   
								<span>I agree to iMarS's <a href="http://www.imarinesurvey.com/page/all/terms-of-service" target="_blank">Terms of Service</a></span>
								</div>
							  </div>
						  	<div class="col-md-12 text-center">
							  	<div class="center-btn">
                                  <button type="submit" class="btn btn-primary">Submit</button>
                                  <p>Already have an account? <a href="{{ URL::asset('/signin') }}">Sign in</a></p>
							  	</div>
							  </div>
						  </div>
						  
                          {!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</section>
@stop
