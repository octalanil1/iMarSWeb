<script type="text/javascript">
    $(document).ready(function () 
    {
        $( '#MySurveyTypeForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/mysurveytypespost') }}',
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
			
			showpage('{{URL::asset('/my-survey-types')}}');
          }
          $('.modal-backdrop').remove();   
		  $.LoadingOverlay("hide");     
        });

    });
});
function DeleteSurveytype(id)
{
    var result = confirm("Are you sure to delete?");
    if(result){
        $.LoadingOverlay("show");
        $.ajax({
            dataType: 'json',
        type: "POST",
        url: '{{ URL::to('/removesurveytype') }}',
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
                


                 showpage('{{URL::asset('/my-survey-types')}}');
            }
           
        }
        });
    }
    
}
function pricefromcat() 
    {
       var cat_id= document.getElementById('survey_type_id').value;
        if(cat_id=='8' || cat_id=='23' || cat_id=='24' || cat_id=='25' || cat_id=='29')
        { $('#price').attr('placeholder','Enter price per day ($USD)');
                                                
        }else{
            $('#price').attr('placeholder','Enter Price ($USD)');
           
        }
       
      
        
    }
    function editpricefromcat(id) 
    {
        var cat_id= document.getElementById('survey_type_id_'+id).value;

        if(cat_id=='8' || cat_id=='23' || cat_id=='24' || cat_id=='25' || cat_id=='29')
        { 
            $('#price_'+id).attr('placeholder',"Enter price per day ($USD)");
                                                
        }else
        {
            $('#price_'+id).attr('placeholder','Enter Price ($USD)');
           
        }
        
      
        
    }

    function conductcustomsurvey()
{
    var conduct_custom=$('#conduct_custom').val();
    if(conduct_custom=='0'){
        var result = confirm("iMarS will notify you when there is a Custom Occasional Survey request. There is a bidding process after the request and the operator selects the surveyor based on the bids submitted.");
    }else{
        var result = confirm("Are you sure to Remove Conduct Custom Occasional Surveys?");
    }
    

    if(result){
      
        $.LoadingOverlay("show");
        $.ajax({
            dataType: 'json',
        type: "POST",
        url: '{{ URL::to('/conductcustomsurvey') }}',
        data: { conduct_custom: conduct_custom },
        success: function(data)
        {
            $.LoadingOverlay("hide");
            if(data.success==false)
            {
                if(data.class == 'danger'){showMsg("Something Went Wrong", "danger");}

            }else
            {
                if(data.class == 'success'){showMsg(data.message, "success");}
                


                 showpage('{{URL::asset('/my-survey-types')}}');
            }
           
        }
        });
    }
    
}

</script> 
 <section class="page">
		    	<div class="row">
		    		<div class="col-md-12 col-lg-12 col-xl-12">
		    			<div class="surveyors ports">
		    				
                           <div class="right-flex-box">
                           <h4>My Survey Types</h4><br>
                            <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#addportModal" ><i class="fas fa-plus" aria-hidden="true"></i></a>
					  	
                           </div>
                           <p><strong>Note:</strong> Add survey types and pricing for the surveys that you want to conduct. Use + button to add a survey type.</p>

		    				<ul class="upcomeing-past-list">
                                <?php // echo count($usersurveytypesdetail);?>
                               <?php if(count($usersurveytypesdetail)!=0 ) {?>
                              
                                        @foreach($usersurveytypesdetail as $data)

                                        <li>
						  			<span class="past-img">
						  				<img src="{{ URL::asset('/public/media') }}/ship.png" alt="#">
						  			</span>
						  			<span class="ship-info">
						  				<h3>{{$data->survey_type_name}}</h3>
                                          <p>Cost of Survey : <span>${{$data->survey_price}}</span></p>
                                   </span>

                                      <span class="right-arrow editportc">
                                      <a href="javaScript:Void(0);" class="" onclick="DeleteSurveytype('{{base64_encode($data->id)}}');" title="Remove"><i class="fas fa-trash" aria-hidden="true"></i> </a>   

                                      <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#editportModal_{{$data->id}}" ><i class="fas fa-edit" aria-hidden="true"></i></a>

                                      </span>
                                      
                                  </li>
                                  
                                           
                                            <div class="modal login-modal fade" id="editportModal_{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Edit Survey Types</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="login-inner">
                                                {!! Form::open(array('url' => 'mysurveytypespost', 'method' => 'post','name'=>'MySurveyTypeForm','files'=>true,'novalidate' => 'novalidate','id' => 'MySurveyTypeForm')) !!}

                                                    <div class="form-group">
                                                    <?php $helper=new App\Helpers;
                                                              $survey_types= $helper->SurveyTypeList();
                                                             // dd( $survey_types);
                                                              unset($survey_types['31']);
                                                              


                                                    ?>
                                                    {!! Form::select('survey_type_id',$survey_types,$data->survey_type_id, ['class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()','id'=>'survey_type_id_'.$data->id,'onchange'=>"editpricefromcat('$data->id');"]) !!}
                                                        @if ($errors->has('survey_type_id')) <p class="alert alert-danger">{{ $errors->first('survey_type_id') }}</p> @endif
                                                        {!! Form::hidden('id',base64_encode($data->id),['id'=>'id']) !!}

                                                    </div>
                                                   <?php 
                                                   if($data->survey_type_id=='8' || $data->survey_type_id=='23' || $data->survey_type_id=='24' ||
                                                   $data->survey_type_id=='25' || $data->survey_type_id=='29'){
                                                    $place='Enter Price per Day ($USD)';
                                                  }else{
                                                      $place='Enter Price ($USD)';
                                                  }
                                                   ?>
                                                    <div class="form-group">
                                                    {!! Form::text('price', $data->survey_price, ['class' => 'form-control','placeholder' => $place,'required'=>'required','id'=>'price_'.$data->id]) !!}
                                                        @if ($errors->has('price')) <p class="alert alert-danger">{{ $errors->first('price') }}</p> @endif
                                                        
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
						  				<h3>No Survey Type  Available</h3>
						  			</span>
                                  </li>
                               <?php  } ?>
						  		
						  	</ul>
		    			</div>
			    	</div>
                </div>
                <?php  		$user = Auth::user();?>

                @if($user->type=='2' || $user->type=='4')
						<div class="form-group">
						
							<span class="custom_check">I want to conduct Custom Occasional surveys &nbsp;
							{!! Form::checkbox('conduct_custom',$user->conduct_custom,$user->conduct_custom,['class' => 'form-control','id'=>'conduct_custom','onclick'=>'conductcustomsurvey();']) !!}
                           <span class="check_indicator">&nbsp;</span></span>

						</div>
						@endif
            </section>
            <div class="modal login-modal fade" id="addportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	         <div class="modal-content">
	        <div class="modal-header">
	        <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Add Survey Types</h5>
	      </div>
	      <div class="modal-body">
	        <div class="login-inner">
            {!! Form::open(array('url' => 'mysurveytypespost', 'method' => 'post','name'=>'MySurveyTypeForm','files'=>true,'novalidate' => 'novalidate','id' => 'MySurveyTypeForm')) !!}

				  <div class="form-group">
                  <?php $helper=new App\Helpers;
                  $survey_types= $helper->SurveyTypeList();
                  // dd( $survey_types);
                   unset($survey_types['31']);
                  ?>
                    {!! Form::select('survey_type_id',$survey_types,null, ['class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()','id'=>'survey_type_id','onchange'=>'pricefromcat();']) !!}
        			@if ($errors->has('survey_type_id')) <p class="alert alert-danger">{{ $errors->first('survey_type_id') }}</p> @endif
				    
				  </div>
				  <div class="form-group">
				  {!! Form::text('price', null, ['class' => 'form-control','placeholder' => 'Enter Price ($USD)' ,'required'=>'required','id'=>'price']) !!}
        			@if ($errors->has('price')) <p class="alert alert-danger">{{ $errors->first('price') }}</p> @endif
				    
				  </div>
				  
                  <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>

				  
                  {!! Form::close() !!}
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
  