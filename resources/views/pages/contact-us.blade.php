@extends('layouts.master')
@section('title') iMarS|Contact Us @stop

@section('content')  
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">

<script type="text/javascript">
    $(document).ready(function () 
    {
        $( '#MyContactForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/contactuspost') }}',
        }).done(function( data ) 
        {  error_remove (); 
			$.LoadingOverlay("hide");  
			if(data.success==false)
            { $.each(data.errors, function(key, value){
                        $('#'+key).closest('.form-group').addClass('has-error');
                        $('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                    });
                
          }else
		  {
        
            if(data.class == 'success'){showMsg(data.message, "success");}
			
            $.LoadingOverlay("hide");     
          }
            
		  
        });

    });
});



</script> 
<section class="main Contact-info">
	    <div class="container">
	        <div class="row">
	          <div class="col-12 col-sm-12 col-md-6 col-lg-6">
	            <div class="contact-deatils">
	              <h2>Contact us</h2>
				  <p>We look forward to hearing from you and answering questions
				  you may have.</p>
	              <ul class="compny-info">
	                <li class="location-arya">9494 Southwest Fwy. Ste 720 Houston, TX, USA 77074
					</li>
										
					<li class="emailId">imars@marineinfotech.com </li>
	               
	              </ul>
	            </div>
	          </div>
	          <div class="col-12 col-sm-12 col-md-6 col-lg-6">
	            <div class="contact-map">
	              
					<iframe src="https://maps.google.com/maps?width=100%&amp;height=600&amp;hl=en&amp;q=9494%20Southwest%20Fwy.%20Ste%20720%20Houston%2C%20TX%2C%20USA%2077074+(My%20Business%20Name)&amp;ie=UTF8&amp;t=&amp;z=14&amp;iwloc=B&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
	            </div>
	          </div>
	        </div>
	    </div>
	</section>
	<section class="contact-form">
	  <div class="container">
	    <div class="row">
	      <div class="col-12 col-sm-12 col-md-12 col-lg-12">
          {!! Form::open(array('url' => 'contactuspost', 'method' => 'post','name'=>'MyContactForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyContactForm')) !!}
	          <div class="row">
	            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
	               <div class="form-group">
	                 
                      {!! Form::text('first_name',null, ['class' => 'form-control','placeholder' => 'First Name','required'=>'required','id'=>'first_name','onkeypress' => 'error_remove()' ]) !!}
	                </div>
	            </div>
	            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
	               <div class="form-group">
                     
                      {!! Form::text('last_name',null, ['class' => 'form-control','placeholder' => 'Last Name','required'=>'required','id'=>'last_name','onkeypress' => 'error_remove()' ]) !!}   

	                </div>
	            </div>
	            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
	               <div class="form-group">
	                 
                      {!! Form::text('email',null, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email','onkeypress' => 'error_remove()' ]) !!}   
	                </div>
	            </div>
	            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
	               <div class="form-group">
	                 
                      {!! Form::text('mobile',null, ['class' => 'form-control','placeholder' => 'Mobile ','required'=>'required','id'=>'mobile','onkeypress' => 'error_remove()' ]) !!}   
	                </div>
	            </div>
	            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
	               <div class="form-group">
                      
                      {!! Form::textarea('comment',null, ['class' => 'form-control','placeholder' => 'Comment ','required'=>'required','id'=>'comment','onkeypress' => 'error_remove()' ]) !!}   

	                </div>
	            </div>
	           
	          <div class="col-12 col-sm-12 col-md-12 col-lg-12">
	               <div class="form-group">
	                  <button type="submit" class="submit contact-submit">Submit</button>
	            </div>
	          </div>
	        
	      </div>
          {!! Form::close() !!}
	    </div>
	  </div>
	</section>
@stop
