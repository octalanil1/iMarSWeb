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
            url: '{{ URL::to('/myagentpost') }}',
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

			showpage('{{URL::asset('/myagent')}}');
          }
            
		  $.LoadingOverlay("hide");     

        });

    });
});
function DeleteAgent(id)
{
    var result = confirm("Are you sure to delete?");
    if(result){
        $.LoadingOverlay("show");
        $.ajax({
            dataType: 'json',
        type: "POST",
        url: '{{ URL::to('/removeagent') }}',
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
                


                 showpage('{{URL::asset('/myagent')}}');
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
                            <h4>My Agents</h4>
                            <?php $user = Auth::user();?>
                          
					  	    <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#addportModal" ><i class="fas fa-plus" aria-hidden="true"></i></a>
                             
					       </div>
		    				<ul class="listing">
                                <?php // echo count($userportdetail);?>
                               <?php if(count($agents_data)!=0 ) {?>
                              
                                        @foreach($agents_data as $data)
                                        <li>
		    						<span class="user-img"><img src="{{ URL::asset('/public/media') }}/list-user.png" alt="#"></span>
		    						<span class="user-info">
		    							<h4>{{$data->first_name}}  {{$data->last_name}}</h4>
		    							<p><a href="#"><i class="fas fa-phone-volume" aria-hidden="true"></i> {{$data->mobile}}</a></p>
                                        <p><a href="#"><i class="fas fa-envelope" aria-hidden="true"></i> {{$data->email}}</a></p>

                                    </span>
                                   
                                    <span class="right-arrow editportc">
                                    <a href="javaScript:Void(0);" class="" onclick="DeleteAgent('{{base64_encode($data->id)}}');" title="Remove"><i class="fas fa-trash" aria-hidden="true"></i> </a>   
                                    <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#editportModal_{{$data->id}}" ><i class="fas fa-edit" aria-hidden="true"></i></a>
                                   </span>
                                  
		    					</li>
                                            <div class="modal login-modal fade" id="editportModal_{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Edit an Agent</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="login-inner">
                                                {!! Form::open(array('url' => 'myagentpost', 'method' => 'post','name'=>'MyAgentsForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyAgentsForm')) !!}

                                                    <div class="form-group">
                                                    {!! Form::text('first_name', $data->first_name, ['class' => 'form-control','placeholder' => 'Company Name','required'=>'required','id'=>'first_name_'.$data->id]) !!}
                                                        @if ($errors->has('first_name')) <p class="alert alert-danger">{{ $errors->first('first_name') }}</p> @endif
                                                        {!! Form::hidden('id',base64_encode($data->id),['id'=>'id']) !!}
                                                    </div>
                                                   
                                                    <div class="form-group">
                                                    {!! Form::text('email', $data->email, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email_'.$data->id]) !!}
                                                        @if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
                                                      
                                                    </div>
                                                    <div class="form-group">
                                                    {!! Form::text('mobile', $data->mobile, ['class' => 'form-control','placeholder' => 'Phone Number','required'=>'required','id'=>'mobile_'.$data->id]) !!}
                                                        @if ($errors->has('mobile')) <p class="alert alert-danger">{{ $errors->first('mobile') }}</p> @endif
                                                    </div>
                                                    
                                                    <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>

                                                    
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
						  				<h3>No Agents Available</h3>
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
	        <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Add an Agent</h5>
	      </div>
	      <div class="modal-body">
	        <div class="login-inner">

            {!! Form::open(array('url' => 'myagentpost', 'method' => 'post','name'=>'MyAgentsForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyAgentsForm')) !!}

        <div class="form-group">
        {!! Form::text('first_name', null, ['class' => 'form-control','placeholder' => 'Company Name','required'=>'required','id'=>'first_name']) !!}
            @if ($errors->has('first_name')) <p class="alert alert-danger">{{ $errors->first('first_name') }}</p> @endif
        </div>
        
        <div class="form-group">
        {!! Form::text('email', null, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email']) !!}
            @if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
        
        </div>
        <div class="form-group">
        {!! Form::text('mobile', null, ['class' => 'form-control','placeholder' => 'Phone Number','required'=>'required','id'=>'mobile']) !!}
            @if ($errors->has('mobile')) <p class="alert alert-danger">{{ $errors->first('mobile') }}</p> @endif
        </div>

     <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                  {!! Form::close() !!}
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
  