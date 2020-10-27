@extends('layouts.master')
@section('title') iMarS | Signin @stop

@section('content')  

<script type="text/javascript">
    $(document).ready(function () 
    {
        $( '#signinForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/signinpost') }}',
        }).done(function( data ) 
        {  error_remove (); 
			if(data.success==false)
            {
				
				if(data.message==false)
				{
					$.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                   });
				}
				else{
					
					if(data.class == 'success'){showMsg(data.message, "danger");}
				}
				  

          }else
		  {
          
            if(data.class == 'success'){showMsg(data.message, "success");}
           
			window.location = "{{URL::asset('/myaccount')}}";
	            
          }
            
		  $.LoadingOverlay("hide");     
        });

    });

	$( '#forgotForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/forgotpost') }}',
        }).done(function( data ) 
        {  error_remove (); 
			if(data.success==false)
            {
				
				if(data.message==false)
				{
					$.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                   });
				}
				else{
					
					if(data.class == 'success'){showMsg(data.message, "danger");}
				}
				  

          }else
		  {
          
            if(data.class == 'success'){showMsg(data.message, "success");}
			$("#forgetpassword").modal('hide');

			
	            
          }
            
		  $.LoadingOverlay("hide");     
        });

    });
});


</script> 

<div class="login-modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
		  <h5 class="modal-title"><img src="{{ URL::asset('/media') }}/logo-icon.png" alt="">Login</h5>
		  
          @if(Session::has('msg')) {!! session('msg') !!} @endif
	      </div>
	      <div class="modal-body">
	        <div class="login-inner">
			{!! Form::open(array('url' => 'signinpost', 'method' => 'post','name'=>'signinForm','files'=>true,'novalidate' => 'novalidate','id' => 'signinForm')) !!}


				  <div class="form-group">
          {!! Form::text('email', null, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email']) !!}
        @if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
				    
				  </div>
				  <div class="form-group">
          {!! Form::password('password', ['class' => 'form-control','placeholder'=>'Password','required'=>'required','id'=>'password']) !!}
        @if ($errors->has('password')) <p class="alert alert-danger">{{ $errors->first('password') }}</p> @endif
				  </div>
				  <div class="form-group">
				    <span class="custom_check">Remember me &nbsp; <input type="checkbox"><span class="check_indicator">&nbsp;</span></span>
					<a href="JavaScript:Void(0);" class="forget-password" data-toggle="modal" data-target="#forgetpassword" >Forgot Password ?</a>

				  </div>
				  
				  <button type="submit" class="btn btn-primary">Sign in<img src="{{ URL::asset('/media') }}/arrow.png" alt="#"></button>
				  <p>Don't have an account? <a href="{{ URL::asset('/signup') }}">Sign up</a></p>
          {!! Form::close() !!}
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	<div class="modal login-modal fade" id="forgetpassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title"><img src="{{ URL::asset('/media') }}/logo-icon.png" alt="">Forgot  Password</h5>
	      </div>
	      <div class="modal-body">
	        <div class="login-inner">
            {!! Form::open(array('url' => 'forgotpost', 'method' => 'post','name'=>'forgotForm','files'=>true,'novalidate' => 'novalidate','id' => 'forgotForm')) !!}

				  
				  <div class="form-group">
				  {!! Form::text('email_id',null, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email_id','onkeypress' => 'error_remove()' ]) !!}   
        			@if ($errors->has('email_id')) <p class="alert alert-danger">{{ $errors->first('email_id') }}</p> @endif
				    
				  </div>
				  
                  <button type="submit" class="btn btn-primary">Send<img src="{{ URL::asset('/media') }}/arrow.png" alt="#"></button>

				  
                  {!! Form::close() !!}
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	{{Session::forget('msg')}}

@stop
