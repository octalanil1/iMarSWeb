
<script type="text/javascript">
    $(document).ready(function () 
    {
        $( '#MyOperatorForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/verify-mobile') }}',
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
                $("#myModal").modal('hide');
                $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/myprofile')}}');
            }
            else{
                showMsg(data.message, "danger");
            }
            
           
            
			
			
          }
            
		  $.LoadingOverlay("hide");     

        });

    });
});



  
</script> 
  <div class="login-inner">
{!! Form::open(array('url' => 'verify-mobile', 'method' => 'post','name'=>'MyOperatorForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyOperatorForm')) !!}

<div class="form-group">
{!! Form::text('otp', null, ['class' => 'form-control','placeholder' => 'OTP','required'=>'required','id'=>'otp']) !!}
@if ($errors->has('otp')) <p class="alert alert-danger">{{ $errors->first('otp') }}</p> @endif
</div>
{!! Form::hidden('id','') !!}

<button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
{!! Form::close() !!}
  </div>