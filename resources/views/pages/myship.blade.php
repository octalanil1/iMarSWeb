<script type="text/javascript">
    $(document).ready(function () 
    {
        $( '#MyShipForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/myshippost') }}',
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

			showpage('{{URL::asset('/myship')}}');
          }
            
		  $.LoadingOverlay("hide"); 
    
        });

    });
});

function shipfavourite(id)
{
    $.LoadingOverlay("show");
        $.ajax({
            dataType: 'json',
        type: "POST",
        url: '{{ URL::to('/addshipfavourite') }}',
        data: { id: id },
        success: function(data)
        {
            $.LoadingOverlay("hide");
            if(data.success==false)
            {
                if(data.class == 'danger'){showMsg("Something Went Wrong", "danger");}

            }else
            {
                if(data.class == 'success'){showMsg(data.message, "success");}else{
                    showMsg("Something Went Wrong", "danger");  
                }
                


                 showpage('{{URL::asset('/myship')}}');
            }
           
        }
        });
}
function GetData(type) 
    {
         $.LoadingOverlay("show");   
      $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
              
           // alert(country_id);
      $.ajax({
            
            data: { type:type}, 
            type: "POST",
            url: '{{ URL::to('/getdata') }}',
        }).done(function( data ) 
        {   

        if(type=='company'){
            document.getElementById("company").value =data;  
        }else{
            document.getElementById("city").value =data.city;  

            document.getElementById("state").value =data.state;  

            document.getElementById("street_address").value =data.street_address;  

            document.getElementById("pincode").value =data.pincode;  

        }
          
            $.LoadingOverlay("hide");   
           
          
        });
        
    }
    function GetDataEdit(type,id) 
    {
         $.LoadingOverlay("show");   
      $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
              
           // alert(country_id);
      $.ajax({
            
            data: { type:type}, 
            type: "POST",
            url: '{{ URL::to('/getdata') }}',
        }).done(function( data ) 
        {  // alert(data);
        if(type=='company'){
            document.getElementById("company_"+id).value =data;  
        }else{
           
            document.getElementById("city_"+id).value =data.city;  

            document.getElementById("state_"+id).value =data.state;  

            document.getElementById("street_address_"+id).value =data.street_address;  

            document.getElementById("pincode_"+id).value =data.pincode;  
        }
          
            $.LoadingOverlay("hide");   
           
          
        });
        
    }
    function DeleteVessel(id)
{
    var result = confirm("Are you sure to delete?");
    if(result){
        $.LoadingOverlay("show");
        $.ajax({
            dataType: 'json',
        type: "POST",
        url: '{{ URL::to('/removevessel') }}',
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
                


                 showpage('{{URL::asset('/myship')}}');
            }
           
        }
        });
    }
    
}
</script> 
<style>
    .user-info-field fieldset {
    border: 1px solid #ddd !important;
margin: 8px;
width: 100%;
padding: 10px;
    padding-left: 10px;
position: relative;
border-radius: 4px;
background-color:
    #f5f5f5;
    padding-left: 10px !important;
}.user-info-field legend {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 0px;
    width: 35%;
    border: 1px solid 
#ddd;
border-radius: 4px;
padding: 5px 5px 5px 10px;
background-color:
    #ffffff;
}
    </style>
 <section class="page">
		    	<div class="row">
		    		<div class="col-md-12 col-lg-12 col-xl-12">
		    			<div class="surveyors">
                            
                        <div class="right-flex-box">
                            <h4>My Vessels</h4>
                            <?php $user = Auth::user();?>
                            
					  	    <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#addportModal" ><i class="fas fa-plus" aria-hidden="true"></i></a>
                         
					       </div>
		    				<ul class="listing">
                                <?php // echo count($userportdetail);?>
                               <?php if(count($ship_data)!=0 ) {?>
                              
                                        @foreach($ship_data as $data)
                                        <li>
		    						<span class="user-img"><img src="{{ URL::asset('/public/media') }}/list-user.png" alt="#"></span>
		    						<span class="user-info">
                                    <h4> {{$data->name}}</h4>
		    						<p>IMO Number : #{{$data->imo_number}}</p>

                                    </span>
                                    
                                    <span class="right-arrow editportc">
                                    <!-- @if($user->id==$data->user_id)    
                                    <a href="javaScript:Void(0);" class="<?php if($data->favourite=="1") echo 'active';?>" onclick="shipfavourite('{{base64_encode($data->id)}}');"><i class="fas fa-heart" aria-hidden="true"></i> </a>
                                  @endif -->
                                    <a href="javaScript:Void(0);" class="login" data-toggle="modal" data-target="#editportModal_{{$data->id}}" ><i class="fas fa-edit" aria-hidden="true"></i> </a>
                                    <a href="javaScript:Void(0);" class="login" style="right: 84px;" onclick="DeleteVessel('{{base64_encode($data->id)}}');" title="Remove"><i class="fas fa-trash" aria-hidden="true"></i> </a>   

                                    </span>
                                   

		    					</li>
                                            <div class="modal login-modal fade" id="editportModal_{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Edit a Vessel</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="login-inner">
                                                {!! Form::open(array('url' => 'myshippost', 'method' => 'post','name'=>'MyShipForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyShipForm')) !!}

                                                    <div class="form-group">
                                                    {!! Form::text('imo_number', $data->imo_number, ['class' => 'form-control','placeholder' => 'IMO#','required'=>'required','id'=>'imo_number_'.$data->id]) !!}
                                                        @if ($errors->has('imo_number')) <p class="alert alert-danger">{{ $errors->first('imo_number') }}</p> @endif
                                                        {!! Form::hidden('id',base64_encode($data->id),['id'=>'id']) !!}
                                                    </div>
                                                    <div class="form-group">
                                                    {!! Form::text('name', $data->name, ['class' => 'form-control','placeholder' => 'Ship Name','required'=>'required','id'=>'name_'.$data->id]) !!}
                                                        @if ($errors->has('name')) <p class="alert alert-danger">{{ $errors->first('name') }}</p> @endif
                                                       
                                                    </div>
                                                    <div class="form-group">
                                                {!! Form::text('company', $data->company, ['class' => 'form-control','placeholder' => 'Invoice Company Name','required'=>'required','id'=>'company_'.$data->id]) !!}
                                                    @if ($errors->has('company')) <p class="alert alert-danger">{{ $errors->first('company') }}</p> @endif
                                                    <span class="custom_check">Same as Company Name &nbsp; <input type="checkbox" name="same_as_company" value="1" <?php if($data->same_as_company=="1") echo "checked"; ?> onchange="GetDataEdit('company','{{$data->id}}');"><span class="check_indicator">&nbsp;</span></span>

                                                </div>
                                                <div class="row user-info-field">
                                                <fieldset>
                                                    <legend>Invoice Company Address:</legend>
                                                        <div class="form-group">
                                                        {!! Form::text('street_address', $data->address, ['class' => 'form-control','placeholder' => 'Street address','required'=>'required','id'=>'street_address_'.$data->id]) !!}
                                                            @if ($errors->has('street_address')) <p class="alert alert-danger">{{ $errors->first('street_address') }}</p> @endif
                                                        </div>
                                                        <div class="form-group">
                                                        {!! Form::text('city', $data->city, ['class' => 'form-control','placeholder' => 'City','required'=>'required','id'=>'city_'.$data->id]) !!}
                                                            @if ($errors->has('city')) <p class="alert alert-danger">{{ $errors->first('city') }}</p> @endif
                                                        </div>
                                                        <div class="form-group">
                                                        {!! Form::text('state', $data->state, ['class' => 'form-control','placeholder' => 'State','required'=>'required','id'=>'state_'.$data->id]) !!}
                                                            @if ($errors->has('state')) <p class="alert alert-danger">{{ $errors->first('state') }}</p> @endif
                                                        </div>
                                                        <div class="form-group">
                                                        {!! Form::text('pincode', $data->pincode, ['class' => 'form-control','placeholder' => 'Zip','required'=>'required','id'=>'pincode_'.$data->id]) !!}
                                                            @if ($errors->has('pincode')) <p class="alert alert-danger">{{ $errors->first('pincode') }}</p> @endif
                                                        </div>
                                                        <span class="custom_check">Same as Company Address &nbsp; <input type="checkbox" name="same_as_company_address" value="1" <?php if($data->same_as_company_address=="1") echo "checked"; ?> onchange="GetDataEdit('address','{{$data->id}}');"><span class="check_indicator">&nbsp;</span></span>
                                                </fieldset>
                                                </div>
                                                    <div class="row user-info-field">
                                                            <fieldset>
                                                            <legend>Invoice Email Addresses:</legend>

                                                                <div class="form-group">
                                                                {!! Form::text('email', $data->email, ['class' => 'form-control','placeholder' => 'Email for invoices to be sent','required'=>'required','id'=>'email_'.$data->id]) !!}
                                                                    @if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
                                                                
                                                                </div>
                                                                <div class="form-group">
                                                                {!! Form::text('additional_email', $data->additional_email, ['class' => 'form-control','placeholder' => 'Additional email for invoices to be sent','required'=>'required','id'=>'additional_email_'.$data->id]) !!}
                                                                    @if ($errors->has('additional_email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
                                                                
                                                                </div>
                                                            </fieldset>
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
						  				<h3>No Ships Available</h3>
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
	        <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Add a Vessel</h5>
	      </div>
	      <div class="modal-body">
	        <div class="login-inner">

            {!! Form::open(array('url' => 'myshippost', 'method' => 'post','name'=>'MyShipForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyShipForm')) !!}

        <div class="form-group">
        {!! Form::text('imo_number', null, ['class' => 'form-control','placeholder' => 'IMO#','required'=>'required','id'=>'imo_number']) !!}
            @if ($errors->has('imo_number')) <p class="alert alert-danger">{{ $errors->first('imo_number') }}</p> @endif
        </div>
        <div class="form-group">
        {!! Form::text('name', null, ['class' => 'form-control','placeholder' => 'Ship Name','required'=>'required','id'=>'name']) !!}
            @if ($errors->has('name')) <p class="alert alert-danger">{{ $errors->first('name') }}</p> @endif
        
        </div>
        <div class="form-group">
        {!! Form::text('company', null, ['class' => 'form-control','placeholder' => 'Invoice Company Name','required'=>'required','id'=>'company']) !!}
            @if ($errors->has('company')) <p class="alert alert-danger">{{ $errors->first('company') }}</p> @endif
            <span class="custom_check">Same as Company Name &nbsp; <input type="checkbox" name="same_as_company" value="1" onchange="GetData('company');"><span class="check_indicator">&nbsp;</span></span>

        </div>
       
                               
                                 
       
        <div class="row user-info-field">
            <fieldset>
                    <legend>Invoice Company Address:</legend>
                    <div class="form-group">
                    {!! Form::text('street_address', null, ['class' => 'form-control','placeholder' => 'Street address','required'=>'required','id'=>'street_address']) !!}
                        @if ($errors->has('street_address')) <p class="alert alert-danger">{{ $errors->first('street_address') }}</p> @endif
                    </div>
                    <div class="form-group">
                    {!! Form::text('city', null, ['class' => 'form-control','placeholder' => 'City','required'=>'required','id'=>'city']) !!}
                        @if ($errors->has('city')) <p class="alert alert-danger">{{ $errors->first('city') }}</p> @endif
                    </div>
                    <div class="form-group">
                    {!! Form::text('state', null, ['class' => 'form-control','placeholder' => 'State','required'=>'required','id'=>'state']) !!}
                        @if ($errors->has('state')) <p class="alert alert-danger">{{ $errors->first('state') }}</p> @endif
                    </div>
                    <div class="form-group">
                    {!! Form::text('pincode', null, ['class' => 'form-control','placeholder' => 'Zip','required'=>'required','id'=>'pincode']) !!}
                        @if ($errors->has('pincode')) <p class="alert alert-danger">{{ $errors->first('pincode') }}</p> @endif
                    </div>
                    
                
                    <span class="custom_check">Same as Company Address &nbsp; <input type="checkbox" name="same_as_company_address" value="1" onchange="GetData('address');"><span class="check_indicator">&nbsp;</span></span>
                </fieldset>
        </div>

        <div class="row user-info-field">
            <fieldset>
                    <legend>Invoice Email Addresses:</legend>
            <div class="form-group">
        {!! Form::text('email', null, ['class' => 'form-control','placeholder' => 'Email for invoices to be sent','required'=>'required','id'=>'email']) !!}
            @if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
        
        </div>
        <div class="form-group">
        {!! Form::text('additional_email', null, ['class' => 'form-control','placeholder' => 'Additional email for invoices to be sent','required'=>'required','id'=>'additional_email']) !!}
            @if ($errors->has('additional_email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
        
        </div>
        </fieldset>
        </div>

     <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                  {!! Form::close() !!}
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
  