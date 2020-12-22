
<script type="text/javascript">
  	$(document).on('change', '[type=checkbox]', function() 
      {
        var survey_type_id=document.getElementById("survey_type_id").value; 

         if(survey_type_id!='31')
         {

         

            var count = $("input[type=checkbox]:checked").size();
            var total=$("input[type=checkbox]:checked").length;
            
                if(total<4)
                {
                    if($(this).prop("checked") == true)
                    {
                            if(count==1)
                        {
                            $('#ap').attr("disabled", false);	        

                            $('<span class="label-formbox" id="p">Primary</span>').insertBefore(this);
                            $('<input type="hidden" name="pos[]" value="1" id="p1">').insertAfter(this);

                            $('#surveyor-msg').html('Select Substitute Surveyor 1 (Optional)');	        

                        } else if(count==2){
                            
                            $('<span class="label-formbox" id="s1">Substitute 1</span>').insertBefore(this);
                            $('<input type="hidden" name="pos[]" value="2" id="p2">').insertAfter(this);
                            $('#surveyor-msg').html('Select Substitute Surveyor 2 (Optional)');	        

                        }
                        else if(count==3){
                            $('<span class="label-formbox" id="s2">Substitute 2</span>').insertBefore(this);
                            $('<input type="hidden" name="pos[]" value="3" id="p3">').insertAfter(this);

                        }else{

                        }

                    }else
                    {
                        var type= $(this).prev('span').text();
                        if(type=='Primary')
                        {
                            $('input:checkbox').removeAttr('checked');
                            $('input:checkbox').prev('span').remove();
                            $('input:checkbox').next('input').remove();
                          //  $(this).next('input').remove();
                            $('#surveyor-msg').html('Select Primary Surveyor');	        

                        }else if(type=='Substitute 1')
                        {
                                $(this).removeAttr('checked');
                                $(this).prev('span').remove();
                                $(this).next('input').remove();

                            $("span#s2").next('input:checkbox').removeAttr('checked');
                            $("span#s2").next('input:checkbox').prev('span').remove();
                            $("#p3").remove();
                            
                            $('#surveyor-msg').html('Select Substitute Surveyor 1 (Optional)');	   

                        }
                        else if(type=='Substitute 2')
                        {
                                $(this).removeAttr('checked');
                                $(this).prev('span').remove();
                                $(this).next('input').remove();
                                $('#surveyor-msg').html('Select Substitute Surveyor 2 (Optional)');

                        }
                    }

                }else{
                    alert('You can select up to three surveyors.');
                    $(this).removeAttr('checked');
                }
            
         }else
         {
            var count = $("input[type=checkbox]:checked").size();
            var total=$("input[type=checkbox]:checked").length;
            
                if(total<6)
                {
                    if($(this).prop("checked") == true)
                    {
                        
                            if(count==1)
                        {
                            $('#ap').attr("disabled", false);	        

                        

                        }
                    }
                }else{
                    alert('You can select up to 5 surveyors to receive quotes.');
                    $(this).removeAttr('checked');
                }
         }
    });


    $(document).ready(function () 
    {
        $('#ap').attr("disabled", true);	 

        $.LoadingOverlay("hide");     

        $('#port_id').select2();

        $( '#AppoinsurveyorForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/appoint-surveyor-post') }}',
        }).done(function( data ) 
        {  error_remove (); 
			if(data.success==false)
            {
             
                    $.each(data.errors, function(key, value){
                        $('#'+key).closest('.form-group').addClass('has-error');

                        
                        if(key=="port_id"){
						$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('.select2-container'));

					}else{
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
					}      
                    });
                
				
          }else
		  {
          
            if(data.class == 'success'){showMsg(data.message, "success");}
           
			showpage('{{URL::asset('/mysurvey')}}');
          }
            
		  $.LoadingOverlay("hide");     
        });

    });
});
function GetSurveyor() 
    { $.LoadingOverlay("show");   
      $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
              var survey_type_id=document.getElementById("survey_type_id").value; 
              var port_id=document.getElementById("port_id").value;  
              var start_date=document.getElementById("start_date").value;  
              var end_date=document.getElementById("end_date").value;  

             // alert(survey_type_id);
             if(survey_type_id!="" && port_id!="" &&  start_date!="" && end_date!="" )
             { 
                

                 //alert(Date.parse(start_date.replace(/-/g, " "))+'_'+Date.parse(end_date.replace(/-/g, " ")));
                 if(Date.parse(start_date.replace(/-/g, " ")) > Date.parse(end_date.replace(/-/g, " ") ))
                 {
                        alert('Departure Date cannot be an earlier date than the Arrival date')
                        $.LoadingOverlay("hide");   
                 }else{
                    $.ajax({
                        
                        data: { survey_type_id:survey_type_id,port_id:port_id,start_date:start_date,end_date:end_date}, 
                        type: "POST",
                        url: '{{ URL::to('/getsurveyor') }}',
                    }).done(function( data ) 
                    {   
                        document.getElementById("surveyors_id").innerHTML =data; 
                        document.getElementById("first").style.display ='none';  
                        document.getElementById("second").style.display ='block';  
                        $.LoadingOverlay("hide");   
                    
                    
                    });
                 }
                   
             }else
             {
                 alert("Please fill required field like port , start date,end date,survey type");
                 $.LoadingOverlay("hide");   
             }
           
        
    }
    function addVessel()
    {
        var ship_id= document.getElementById('ship_id').value;

        if(ship_id=="add")
        {
            showpage('{{URL::asset('/myship')}}');
        }
    }
    function addAgent()
    {
        var agent_id= document.getElementById('agent').value;

        if(agent_id=="add")
        {
            showpage('{{URL::asset('/myagent')}}');
        }
    }
    function view_record(view_id) 
    {

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('User Detail');
        $('#UserModal').load('{{ URL::to('/user-detail') }}'+'/'+view_id);
        $("#myModal").modal();
    }
    function Sort() 
            { $.LoadingOverlay("show");   
            $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
              var survey_type_id=document.getElementById("survey_type_id").value; 
              var port_id=document.getElementById("port_id").value;  
              var start_date=document.getElementById("start_date").value;  
              var end_date=document.getElementById("end_date").value;  
              var sort=document.getElementById("sort").value;  
           // alert(country_id);
      $.ajax({
            
         
            type: "POST",
			url:'{{ URL::to('/getsurveyor') }}', 
            data: { sort:sort,survey_type_id:survey_type_id,port_id:port_id,start_date:start_date,end_date:end_date}, 
        }).done(function( html ) 
        { //alert(html);
            document.getElementById("surveyors_id").innerHTML =html; 

                         
						  $.LoadingOverlay("hide");
						 
						 showpage('{{URL::asset('/mycalendar')}}');
           
          
        });
        
    }
</script> 
<link rel="stylesheet" type="text/css" href="{{ asset('/public/admin_assets/bower_components/select2/dist/css/select2.min.css') }}"/>
<script type="text/javascript" src="{{ asset('/public/admin_assets/bower_components/select2/dist/js/select2.min.js') }}"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js"></script>
<style>
    #ship_id :last-child {
  color: white;
  background-color: #2f3c7f;
  padding: 5px;

}
#agent :last-child {
  color: white;
  background-color: #2f3c7f;
  padding: 5px;
  
}

	.sieldest-list {
		display: inline-block;
		width: 100%;
		border: 1px solid #d2e9f3;
		border-radius: 5px;
	}
	.sieldest-list li {
		list-style: none;
		padding: 15px;
		// border-bottom: 1px solid #d2e9f3;
	}
	.upload-name {
		border: 0px;
		background: transparent;
		position: relative;
		top: 10px;
		padding-left: 15px;
	}

    </style>
<section class="page">
<div class="row">
    <div class="col-md-12 col-lg-12 col-xl-12">
        <div class="surveyors ports">
        <h4>Appoint Surveyor</h4>
        </div>
        <div class="login-inner">
        {!! Form::open(array('url' => 'appoint-surveyor-post', 'method' => 'post','name'=>'AppoinsurveyorForm','files'=>true,'novalidate' => 'novalidate','id' => 'AppoinsurveyorForm')) !!}
        
        <div class="row" id="first">
                <div class="col-md-6">
                    <div class="form-group">                                                                                               
                        {!! Form::select('port_id',$port_box,null, ['class' => 'form-control','required'=>'required','id'=>'port_id','onChange' => 'error_remove()' ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">            
                        {!! Form::select('ship_id',$vessels_box,null, ['class' => 'form-control','required'=>'required','id'=>'ship_id','onChange' => 'error_remove(),addVessel()' ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('start_date',null, ['class' => 'form-control','placeholder' => 'Start Date','required'=>'required','id'=>'start_date','onkeypress' => 'error_remove()' ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('end_date',null, ['class' => 'form-control','placeholder' => 'End Date','required'=>'required','id'=>'end_date','onkeypress' => 'error_remove()' ]) !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">                                                                                               
                        {!! Form::select('survey_type_id',$surtype_data_box,null, ['class' => 'form-control','required'=>'required','id'=>'survey_type_id' ]) !!}
                    </div>
                    
                </div>
                <div class="col-md-12 text-center">
                    <div class="form-submit">
                    <button onclick="GetSurveyor();" class="btn btn-primary">Search<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                    </div>
                </div>
            </div>
            <div class="row" id="second" style="display:none">
                <div class="col-md-12">
                <div class="form-group">    
                        <div id="surveyors_id"></div>                                                                                           
                    </div> 
                    </div> 
                    
                <div class="col-md-12">
                    <div class="form-group">                                                                                               
                        {!! Form::textarea('instruction',null, ['placeholder' => 'Instructions','class' => 'form-control','required'=>'required','id'=>'instruction','onChange' => 'error_remove()' ]) !!}
                    </div> 
                </div>
            
                <div class="col-md-6">
                    <label class="col-md-12 col-sm-12 col-xs-12 control-label">Upload Instructions Document:</label>
                    <div class="upload-file">
                        <input type="file" name="file_data" id="" class="form-control input-file" onkeypress="error_remove()">
                        <button class="btn-success">Upload</button>
                    </div>
                    <div class="form-group">
                            <input type="text"class="upload-name" id="input-file-placeholder" 
                            placeholder="Upload Image" disabled>
                    </div>
                    <div id="file_data"></div>
                                                
                </div>
                <div class="col-md-6">
                    <div class="form-group">                                                                                               
                        {!! Form::select('agent',$agent_box,null, ['class' => 'form-control','required'=>'required','id'=>'agent','onChange' => 'error_remove(),addAgent()' ]) !!}
                    </div> 
                </div>
                <div class="col-md-12 text-center">
                    <div class="form-submit">
                    <button type="submit" class="btn btn-primary" id="ap">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                    </div>
                </div>
        </div>
         
            

        {!! Form::close() !!}
        </div>
    </div>

</section>
<div id="myModal" class="modal fade form-modal" data-keyboard="false"  role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <i class="fa fa-user"></i>&nbsp;&nbsp;<span class='form-title'></span>
                </h4>
                <button type="button" class="close subbtn" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" id="UserModal">

            </div>
        </div>
    </div>
</div>
<script>
 $(document).ready(function () {
        $("#start_date").datepicker({
            startDate: "today" ,
            format: 'yyyy-M-dd'
        });
		 $("#end_date").datepicker({
            startDate: "today" ,
            format: 'yyyy-M-dd'
        });
    });
</script>