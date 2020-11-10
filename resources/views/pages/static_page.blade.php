@extends('layouts.master')
@section('title') iMarS | {{$slug}} @stop

@section('content')  
<section class="static-page">
		<div class="container">
			<div class="row">
				<div class="col-md-12">

					@if($page_content->slug=="contact-us")
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
					<h2>{{$page_content->title}}</h2>
					<p>{!!$page_content->description!!}</p>

					<!-- <div class="col-12 col-sm-12 col-md-6 col-lg-6">
	            <div class="contact-map">
	               <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3559.517964292462!2d75.8125313150437!3d26.855279983152375!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x396db67301706cff%3A0x470ca1dd08d37cad!2sOctal+IT+Solution!5e0!3m2!1sen!2sin!4v1529409535970" width="" height="" frameborder="0" style="border:0" allowfullscreen=""></iframe>

	            </div>
	          </div> -->
	<section class="contact-form">
	  <div class="container">
	    <div class="row">
	      <div class="col-12 col-sm-12 col-md-12 col-lg-12">
          {!! Form::open(array('url' => 'contactuspost', 'method' => 'post','name'=>'MyContactForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyContactForm')) !!}
	          <div class="row">
	            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
	               <div class="form-group">
	                  <label for="">First Name</label>
                      {!! Form::text('first_name',null, ['class' => 'form-control','placeholder' => 'First Name','required'=>'required','id'=>'first_name','onkeypress' => 'error_remove()' ]) !!}
	                </div>
	            </div>
	            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
	               <div class="form-group">
                      <label for="">Last Name</label>
                      {!! Form::text('last_name',null, ['class' => 'form-control','placeholder' => 'Last Name','required'=>'required','id'=>'last_name','onkeypress' => 'error_remove()' ]) !!}   

	                </div>
	            </div>
	            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
	               <div class="form-group">
	                  <label for="">Email</label>
                      {!! Form::text('email',null, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email','onkeypress' => 'error_remove()' ]) !!}   
	                </div>
	            </div>
	            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
	               <div class="form-group">
	                  <label for="">Phone</label>
                      {!! Form::text('mobile',null, ['class' => 'form-control','placeholder' => 'Mobile ','required'=>'required','id'=>'mobile','onkeypress' => 'error_remove()' ]) !!}   
	                </div>
	            </div>
	            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
	               <div class="form-group">
                      <label for="">Comment</label>
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

					@else
					<h2>{{$page_content->title}}</h2>
					<p>{!!$page_content->description!!}</p>

					@endif
					
				</div>
			</div>
        </div>
	</section>
@stop
