<script type="text/javascript">
    $(document).ready(function () 
    {
        $( '#MyChangePasswordForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/change-password-post') }}',
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
                  if(data.class == 'danger'){showMsg(data.message, "danger");}

                  showpage('{{URL::asset('/change-password')}}');
            }
           $.LoadingOverlay("hide");     
        });

    });
});


</script> 
 <section class="page">
		<div class="row">
		    <div class="col-md-12 col-lg-12 col-xl-12">
		    	<div class="surveyors">
             <div class="right-flex-box"> <h4>Change Password</h4> </div>
                {!! Form::open(array('url' => 'change-password-post', 'method' => 'post','name'=>'MyChangePasswordForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyChangePasswordForm')) !!}
                <div class="form-group">
                    {!! Form::password('old_password', ['class' => 'form-control','placeholder' => 'Old Password','required'=>'required','id'=>'old_password']) !!}

              </div>
              <div class="form-group">
                    {!! Form::password('new_password', ['class' => 'form-control','placeholder' => 'New Password','required'=>'required','id'=>'new_password']) !!}

              </div>
              <div class="form-group">
                    {!! Form::password('confirm_password', ['class' => 'form-control','placeholder' => 'Confirm Password','required'=>'required','id'=>'confirm_password']) !!}

              </div>
              <div class="login-inner">
              <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                  {!! Form::close() !!}
              </div>

		    				
		  	</div>
		</div>
		</div>
</section>
      
  