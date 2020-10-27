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
            url: '{{ URL::to('/myoperatorpost') }}',
        }).done(function( data ) 
        {  error_remove (); 
			if(data.success==false)
            {
				
                if(data.id!=""){
                    $.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key+'_'+data.id));
                   });
                
                }else
                {
                    $.each(data.errors, function(key, value){
                        $('#'+key).closest('.form-group').addClass('has-error');
                        $('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                    });
                }
				
					
				

          }else
		  {
          
            if(data.class == 'success'){showMsg(data.message, "success");}
            if(data.id!=""){
                $("#editportModal_"+data.id).modal('hide');
            }else
            {
                $("#addportModal").modal('hide');
            }
            $('.modal-backdrop').remove();     

			showpage('{{URL::asset('/myoperator')}}');

          }
            
		  $.LoadingOverlay("hide");
        });

    });
});
function RemoveOperator(id)
{
    var result = confirm("Are you sure to delete?");
    if(result){
        $.LoadingOverlay("show");
        $.ajax({
            dataType: 'json',
        type: "POST",
        url: '{{ URL::to('/removeoperator') }}',
        data: { id: id },
        success: function(data)
        {
            $.LoadingOverlay("hide");
            if(data.success==false)
            {
                if(data.class == 'danger'){showMsg("Something Went Wrong", "danger");}

            }else
            {
                if(data.class == 'success'){showMsg(data.message, "success");}
                


                 showpage('{{URL::asset('/myoperator')}}');
            }
           
        }
        });
    }
    
}

</script> 
 <section class="page">
		    	<div class="row">
		    		<div class="col-md-12 col-lg-12 col-xl-12">
		    			<div class="surveyors">
                            
                        <div class="right-flex-box">
                            <h4>My @if($type=='0') Operators @else Surveyors @endif</h4>
					  	    <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#addportModal" ><i class="fas fa-plus" aria-hidden="true"></i></a>

					       </div>
		    				<ul class="listing">
                                <?php // echo count($userportdetail);?>
                               <?php if(count($operators_data)!=0 ) {?>
                              
                                        @foreach($operators_data as $data)
                                        <li>
		    						<span class="user-img"><img src="{{ URL::asset('/public/media') }}/list-user.png" alt="#"></span>
		    						<span class="user-info">
                                    @if($data->is_signup =='1')	<h4>{{$data->first_name}}  {{$data->last_name}}</h4>
		    							<p><a href="#"><i class="fas fa-phone-volume" aria-hidden="true"></i> {{$data->mobile}}</a></p>@endif
                                        <p><a href="#"><i class="fas fa-envelope" aria-hidden="true"></i> {{$data->email}}</a>
                                        @if($data->email_verify=='1') <span class="verify">Verified</span>  @else <span class="unverify"> Unverified </span> @endif
                                    </p>

                                    </span>
                                    
                                    <span class="right-arrow editportc">
     
                                    <a href="javaScript:Void(0);" class="" onclick="RemoveOperator('{{base64_encode($data->id)}}');" title="Remove"><i class="fas fa-trash" aria-hidden="true"></i> </a>   
                                    <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#editportModal_{{$data->id}}" title="Remove"><i class="fas fa-edit" aria-hidden="true"></i></a></span>

		    					</li>
                                            <div class="modal login-modal fade" id="editportModal_{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Edit @if($type=='0') Operator @else Surveyor @endif</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="login-inner">
                                                {!! Form::open(array('url' => 'myoperatorpost', 'method' => 'post','name'=>'MyOperatorForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyOperatorForm')) !!}

                                                    
                                                    <div class="form-group">
                                                    {!! Form::text('email', $data->email, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email_'.$data->id]) !!}
                                                        @if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
                                                      
                                                    </div>
                                                    {!! Form::hidden('id',base64_encode($data->id),['id'=>'id']) !!}

                                                    
                                                    <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/media') }}/arrow.png" alt="#"></button>

                                                    
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                            @endforeach
                               <?php }else{ ?>
                                 
                                  <li>
						  			<span class="ship-info">
						  				<h3>No @if($type=='0') Operators @else Surveyors @endif Available</h3>
						  			</span>
                                  </li>
                               <?php  } ?>
						  		
						  	</ul>
		    			</div>
			    	</div>
			    </div>
            </section>
            <div class="modal login-modal fade" id="addportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	         <div class="modal-content">
	        <div class="modal-header">
	        <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Add an @if($type=='0')  Operator @else Surveyor @endif</h5>
	      </div>
	      <div class="modal-body">
	        <div class="login-inner">

            {!! Form::open(array('url' => 'myoperatorpost', 'method' => 'post','name'=>'MyOperatorForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyOperatorForm')) !!}

        
       
        <div class="form-group">
        {!! Form::text('email', null, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email']) !!}
            @if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
        
        </div>
       

     <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                  {!! Form::close() !!}
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
  