<script type="text/javascript">
    $(document).ready(function () 
    { 
        $( '#MyPortForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/myportpost') }}',
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
          
            if(data.class == 'success')
            {
                showMsg(data.message, "success");
                if(data.id!=""){
                $("#editportModal_"+data.id).modal('hide');
            }else
            {
                $("#addportModal").modal('hide');
            }
			
			showpage('{{URL::asset('/myport')}}');
            $('.modal-backdrop').remove();     

            }
            else{
                showMsg(data.message, "danger");
            }
            
          }
            
		  $.LoadingOverlay("hide"); 
    
        });

    });
});

function GetPort() 
    { $.LoadingOverlay("show");   
      $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
              var country_id=document.getElementById("country_id").value;  
             // alert(survey_type_id);
      $.ajax({
            
            data: { country_id:country_id}, 
            type: "POST",
            url: '{{ URL::to('/getport') }}',
        }).done(function( data ) 
        {   
            document.getElementById("port_id_div").innerHTML =data;  
            $.LoadingOverlay("hide");   
           
          
        });
        
    }
    function DeletePort(id)
{
    var result = confirm("Are you sure to delete?");
    if(result){
        $.LoadingOverlay("show");
        $.ajax({
            dataType: 'json',
        type: "POST",
        url: '{{ URL::to('/removeport') }}',
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
                


                 showpage('{{URL::asset('/myport')}}');
            }
           
        }
        });
    }
    
}
function selectAll() {
    var  selectAllCheckbox=document.getElementById("ckbCheckAll");

   if(selectAllCheckbox.checked==true){
     var items = document.getElementsByClassName('checkBoxClass');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }else {
        var items = document.getElementsByClassName('checkBoxClass');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }


        
    }
</script> 

 <section class="page">
		    	<div class="row">
		    		<div class="col-md-12 col-lg-12 col-xl-12">
		    			<div class="surveyors ports">
		    				
                            <div class="right-flex-box">
                            <h4>Ports</h4>
					  	    <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#addportModal" ><i class="fas fa-plus" aria-hidden="true"></i></a>
					  	
                           </div>
                           <p><strong>Note:</strong> Use + button to add ports that you want to conduct surveys. You may enter a transportation cost associated
                        with a port. Your total price for a survey will be the total of the survey cost and the transportation cost
                    for that port. If you don't want to have transportation cost for a port, you can leave it at $0</p>

		    				<ul class="upcomeing-past-list">
                                <?php // echo count($userportdetail);?>
                               <?php if(count($userportdetail)!=0 ) {?>
                              
                                        @foreach($userportdetail as $data)
                                            <li>
                                                <span class="past-img">
                                                    <img src="{{ URL::asset('/public/media') }}/ship.png" alt="#">
                                                </span>
                                                <span class="ship-info">
                                                    <h3>{{$data->portname}}</h3>
                                                    <p>Transportation cost : <span>$ {{$data->cost}}</span></p>
                                                </span>
                                                <span class="right-arrow editportc">
                                                <a href="javaScript:Void(0);" class="" onclick="DeletePort('{{base64_encode($data->id)}}');" title="Remove"><i class="fas fa-trash" aria-hidden="true"></i> </a>   
                                    <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#editportModal_{{$data->id}}" ><i class="fas fa-edit" aria-hidden="true"></i></a></span>

                                            </li>
                                            <div class="modal login-modal fade" id="editportModal_{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Edit Port</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="login-inner">
                                                {!! Form::open(array('url' => 'myportpost', 'method' => 'post','name'=>'MyPortForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyPortForm')) !!}

                                                    <div class="form-group">
                                                        <?php $helper=new App\Helpers;?>
                                                        <div class="form-group">                                                                                               
                                                            {!! Form::select('country_id',$helper->CountryList(),$data->country_id, ['class' => 'form-control','required'=>'required','id'=>'country_id','onchange'=>'GetPort();','id'=>'country_id_'.$data->id,'disabled'=>"disabled" ]) !!}
                                                        </div>
                                                        <div class="form-group">    
                                                            <div id="port_id_div_{{$data->id}}" >
                                                                <?php
                                                                    $port=new App\Models\Port;;
                                                                    $port_data =  $port->select('*')->where('country_id',$data->country_id )->where('id',$data->port_id )->first(); 
                                                                
                                                                ?>  
                                                                    <div class="row">
                                                                            <div class="col-md-12 col-lg-12 col-xl-12">
                                                                                <div class="surveyors">
                                                                                    <div id="port_id">
                                                                                        <div id="price">
                                                                                            <ul class="listing"><!-- <span class="all-select"><input type="checkbox" id="ckbCheckAll" onclick='selectAll()' checked/> Sellect All</span> -->
                                                                                                <li>
                                                                                                    {!! Form::hidden('id',base64_encode($data->id),['id'=>'id']) !!}
                                                                                                    <span class="user-info">
                                                                                                    <h4>{{$port_data['port']}}</h4>
                                                                                                    </span>
                                                                                                    <input type="text" name="price"  placeholder="Transportation Cost ($USD)" value="{{$data['cost']}}"  class="form-control">
                                                                                                    </li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                </div>                                                                                           
                                                            </div> 
                                                            {!! Form::hidden('id',base64_encode($data->id),['id'=>'id']) !!}

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
						  				<h3>No Port Available</h3>
						  			</span>
                                  </li>
                               <?php  } ?>
						  		
						  	</ul>
		    			</div>
			    	</div>
			    </div>
            </section>
            
            <div class="modal login-modal fade" id="addportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	        <div class="modal-dialog" role="document" style="max-width: 552px;">
	         <div class="modal-content">
	        <div class="modal-header">
	        <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Add Port</h5>
	      </div>
	      <div class="modal-body">
	        <div class="login-inner">
            {!! Form::open(array('url' => 'myportpost', 'method' => 'post','name'=>'MyPortForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyPortForm')) !!}
                <?php $helper=new App\Helpers;?>
                <div class="form-group">                                                                                               
                    {!! Form::select('country_id',$helper->CountryList(),null, ['class' => 'form-control','required'=>'required','id'=>'country_id','onchange'=>'GetPort();' ]) !!}
                </div>
                <div class="form-group">    
                    <div id="port_id_div"></div>                                                                                           
                </div> 
                <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
            {!! Form::close() !!}
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
  