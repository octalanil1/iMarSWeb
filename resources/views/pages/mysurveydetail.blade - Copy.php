<style>
    .fas.fa-star.selected{
        color:#ec8b5d;
    }
            .upload-name {
                border: 0px;
                background: transparent;
                position: relative;
                top: 10px;
                padding-left: 15px;
            }
            .progress .progress-bar.progress-bar-success.myprogress {
                background: #ec8a5d !important;
            }
            .progress {
                margin: 15px 0px 0px 0px;
            }
            #assign_to{
                width: 150px;
                display:inline-block;
            }
        </style>
<script type="text/javascript">
        function Rating(survey_id,operator_id,surveyor_id) {

        $.LoadingOverlay("show");
        $('#RatingModal').html(''); $(".form-title").text('Add Rating');
        $('#RatingModal').load('{{ URL::to('/add-rating') }}'+'/'+survey_id+'/'+operator_id+'/'+surveyor_id);
        $("#ratingModal").modal();
        }
    $(document).ready(function () 
    { 
         $.LoadingOverlay("hide");

         $( '#BIdAccept' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/operatorCustomeSurveyAccept') }}',
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
           
                $("#addportModal").modal('hide');
                $("#myModal").modal('hide');
                $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/mysurvey')}}');
          }
            
		  $.LoadingOverlay("hide");     
        });

    });
         $( '#BidForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/CustomeSurveyAcceptReject') }}',
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
            $("#myModal").modal('hide');
            $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/mysurvey')}}');

			
          }
            
		  $.LoadingOverlay("hide");     
        });

    });


        $( '#reportsubmit' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/reportsubmit') }}',
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
                $("#myModal").modal('hide');
                $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/mysurvey')}}');
            }
                 
            $.LoadingOverlay("hide");    
             
        });

     });
      
});
    
 function statusChange(surveyors_id,survey_id,type) 
{
    if(type=="accept"){
            var result = confirm("Are you sure to accept this survey?");

        }else{
            var result = confirm("Are you sure to decline this survey?");


        }        
        if(result)
        {
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
        $.ajax({
                dataType: 'json',
                data: { surveyors_id:surveyors_id,survey_id:survey_id,type:type}, 
                type: "POST",
                url: '{{ URL::to('/survey-accept-reject') }}',
            }).done(function( data ) 
            {   
            if(data.class == 'success')
                {showMsg(data.message, "success");}
                $("#myModal").modal('hide');
                $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/mysurvey')}}');
            });
        }
        
}
function CustomstatusChange(survey_id) 
{
    var result = confirm("Are you sure you want to cancel this survey?");
        if(result)
        {
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
        $.ajax({
                dataType: 'json',
                data: { survey_id:survey_id}, 
                type: "POST",
                url: '{{ URL::to('/CancelSurvey') }}',
            }).done(function( data ) 
            {   
            if(data.class == 'success')
                {showMsg(data.message, "success");}
                $("#myModal").modal('hide');
                $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/mysurvey')}}');
            });
        }
        
}
function AssignTo(ob,survey_id) 
{
   
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
        $.ajax({
                dataType: 'json',
                data: { assign_to:ob.value,survey_id:survey_id}, 
                type: "POST",
                url: '{{ URL::to('/AssignTo') }}',
            }).done(function( data ) 
            {   
            if(data.class == 'success')
                {showMsg(data.message, "success");}
                $("#myModal").modal('hide');
                $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/mysurvey')}}');
            });
        
        
}
function AssignToop(ob,survey_id) 
{
   
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
        $.ajax({
                dataType: 'json',
                data: { assign_to_op:ob.value,survey_id:survey_id}, 
                type: "POST",
                url: '{{ URL::to('/AssignToop') }}',
            }).done(function( data ) 
            {   
            if(data.class == 'success')
                {showMsg(data.message, "success");}
                $("#myModal").modal('hide');
                $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/mysurvey')}}');
            });
        
        
}


    function reportaccept(surveyors_id,survey_id,type) 
    {
        var result = confirm("Are you sure you want to accept the report? You will be sent an invoice once you accept the report. If you do not respond within three days after report is submitted, it will automatically be considered as accepted.");
        
        if(result)
        {
            $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
            $.ajax({
                    dataType: 'json',
                    data: { surveyors_id:surveyors_id,survey_id:survey_id,type:type}, 
                    type: "POST",
                    url: '{{ URL::to('/reportaccept') }}',
                }).done(function( data ) 
                {   
                
                if(data.class == 'success')
                    {showMsg(data.message, "success");}
                    $("#myModal").modal('hide');
                    $('.modal-backdrop').remove();     

                    showpage('{{URL::asset('/mysurvey')}}');
                });
        }
 }
    
    $(function () {
                $('#btn').click(function () {
                    $('.myprogress').css('width', '0');
                    $('.msg').text('');
                    var filename = $('#filename').val();
                    var myfile = $('#myfile').val();
                    var nofdays=$('#no_of_days').val()
                    if ( myfile == '') {
                        alert('Please select file');
                        return;
                    }
                    
                    var formData = new FormData();
                    formData.append('myfile', $('#myfile')[0].files[0]);
                    formData.append('survey_id', $('#survey_id').val());
                    formData.append('no_of_days', $('#no_of_days').val());


                    $('#btn').attr('disabled', 'disabled');
                     $('.msg').text('Uploading in progress...');
                    $.ajax({
                        url: '{{ URL::to('/reportsubmit') }}',
                        data: formData,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        // this part is progress bar
                        xhr: function () {
                            var xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function (evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    percentComplete = parseInt(percentComplete * 100);
                                    $('.myprogress').text(percentComplete + '%');
                                    $('.myprogress').css('width', percentComplete + '%');
                                }
                            }, false);
                            return xhr;
                        },
                        success: function (data) 
                        {
                            if(data.success==false)
                                {
                                
                                        $.each(data.errors, function(key, value){
                                            $('#'+key).closest('.form-group').addClass('has-error');
                                            $('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                                            
                                        });
                                    
                                    
                            }else
                            {
                                $('.msg').text(data.message);
                                if(data.class == 'success'){showMsg(data.message, "success");}
                                $("#myModal").modal('hide');
                                $('.modal-backdrop').remove();     

                               showpage('{{URL::asset('/mysurvey')}}');
                            }

                            
                            $('#btn').removeAttr('disabled');
                        }
                    });
                });
            });

            function totalPrice(){
                var no_of_days=document.getElementById("no_of_days").value;  
                var cat_price=document.getElementById("cat_price").value;  
                var port_price=document.getElementById("port_price").value; 
            //    / alert(cat_price);
                var total_cat = no_of_days*cat_price;
               
                var total_price= +total_cat+ +port_price
                if(no_of_days!=""){
                    document.getElementById("total").innerHTML="Invoice Total (USD): $"+total_price;
                    document.getElementById("total").style.display='block';

                }else{
                    document.getElementById("total").style.display='none';
                }
               

            }
</script> 

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js"></script>

  

<section class="detail_outer">
		<div class="container">
			<div class="row">
            <?php $user = Auth::user();
        if (Auth::check() && ($user->type=='3' || $user->type=='2' || $user->type=='4') && $surveyor_data->status=="0"
        )
		{ ?>
            <span class="right-detail">
            @if($user->conduct_custom=='1'  && $surveyor_data->survey_type_id=='31')

               @if( $bid_status!=='1')
                    {!! Form::open(array('url' => 'CustomeSurveyAcceptReject', 'method' => 'post','name'=>'BidForm','files'=>true,'novalidate' => 'novalidate','id' => 'BidForm')) !!}
                            <div class="row box-outer">
                                <div class="form-group">
                                    <div class="col-md-12">
                                            <input type="text" name="amount" id="amount" class="form-control input-file" placeholder="Enter Bid Amount">
                                        </div>
                                </div>
                                    {!! Form::hidden('survey_id',$surveyor_data->id,['id'=>'survey_id']) !!}
                                    {!! Form::hidden('surveyors_id',$user->id,['id'=>'survey_id']) !!}

                        
                                    <div class="col-md-4 text-center">
                                        <div class="form-submit">
                                        <button type="submit" class="btn reportSubmit">Submit</button>

                                        </div>
                                    </div>
                                
                                    </div>
                    {!! Form::close() !!}
                    @endif
            @else
            
                       <?php 
                       $surveyors_id_arr= explode(',',$surveyor_data->surveyors_id); 
                       ///dd($surveyors_id_arr);
                       
                       ?> 
                    <?php if($user->id==$surveyor_id_id){
                        	
                        ?>

                    <a href="javaScript:Void(0);" class="active" onclick="statusChange('{{$surveyor_id_id}}','{{$surveyor_data->id}}','accept');">Accept</i> </a>
                    <?php } ?>
            @endif
            @if($surveyor_data->survey_type_id!='31')
            <?php if($user->id==$surveyor_id_id){?>
                <a href="javaScript:Void(0);" class="active" onclick="statusChange('{{$surveyor_id_id}}','{{$surveyor_data->id}}','decline');">Decline </a>   
                <?php } ?>
                @endif
            </span>
        <?php }elseif(Auth::check() && ($user->type=='3' || $user->type=='2' || $user->type=='4') 
        && $surveyor_data->status=="1"){ ?>

            <div class="row">
                <div class="col-md-12 col-lg-12 col-xl-12">
                    <div class="login-inner box-outer">
                    <!-- {!! Form::open(array('url' => 'reportsubmit', 'method' => 'post','name'=>'reportsubmit','files'=>true,'novalidate' => 'novalidate','id' => 'reportsubmit')) !!}
                    
                    <div class="row">
                    
                            <div class="col-md-6">
                            <label class="col-md-12 col-sm-12 col-xs-12 control-label">Upload Report:</label>
                            <div class="upload-file" id="file">
                            <input type="file" name="file"  class="form-control input-file"  onkeypress="error_remove()">
                            <button class="btn-success">Upload</button>
                            </div>
                            </div>
                            {!! Form::hidden('survey_id',base64_encode($surveyor_data->id),['id'=>'survey_id']) !!}

                            <div class="col-md-6 text-center">
                                <div class="form-submit">
                                <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                                </div>
                            </div>
                    </div>
       
        {!! Form::close() !!} -->
        
                    <form id="myform" method="post">
                    <div class="row">
                            <div class="col-md-2">
                                <div class="upload-file" id="file">
                                    <input  type="file" id="myfile" class="form-control input-file"/>

                                    <button class="btn-success">Upload</button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="upload-name" id="input-file-placeholder" placeholder="Initial Report" disabled>
                            </div>
                            <?php if($surveyor_data->survey_type_id=='8' || $surveyor_data->survey_type_id=='23'
                             || $surveyor_data->survey_type_id=='24' || $surveyor_data->survey_type_id=='25' || $surveyor_data->survey_type_id=='29')
                        { ?>
                            <div class="col-md-4">

                            <input type="text" name="no_of_days" id="no_of_days" class="form-control input-file" placeholder="Number of Days" onkeyup="totalPrice();">
                           
                            <input type="hidden" name="cat_price" id="cat_price" value="{{$cat_price}}">
                            <input type="hidden" name="port_price" id="port_price" value="{{$port_price}}" >
                            <div id="total" style="color: green;margin: 10px 9px;"></div>
                                

                            </div>
                    <?php } ?>
                            <div class="col-md-4 text-center">
                                <div class="form-submit">
                                <input type="button" id="btn" class="btn reportSubmit" value="Submit" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
                                </div>
                            </div>
                            {!! Form::hidden('survey_id',base64_encode($surveyor_data->id),['id'=>'survey_id']) !!}
                            <div class="form-group">
                                 <div class="msg"></div>
                            </div>
        </div>
                    
                    
                </form>
        </div>    
        </div>          
        <?php }elseif(Auth::check() && $surveyor_data->status=="3"){?>
            <span class="right-detail">
                @if($user->type=='0' || $user->type=='1'  )
                <a href="javaScript:Void(0);" class="active" onclick="reportaccept('{{$user->id}}','{{$surveyor_data->id}}','report_accept');">Accept Report</i> </a>
               @endif
                @if($surveyor_data->report!="" || $user->type=='0' || $user->type=='1' || $user->type=='2')
                    <a href="{{ URL::asset('/public/media/report') }}/{{$surveyor_data->report}}" target="_blank">View Report </a>

                    <a href="{{ URL::asset('/public/media/report') }}/{{$surveyor_data->report}}" download>Download Report</a>


                    @endif                
            </span>
                   
        <?php  ?>
        <?php }elseif(Auth::check() && ($surveyor_data->status=="4" || $surveyor_data->status=="5" || $surveyor_data->status=="6")){?>
            <span class="right-detail">
             
                @if($surveyor_data->report!="" || $user->type=='0' || $user->type=='1' || $user->type=='2')
                
                    <a href="{{ URL::asset('/public/media/report') }}/{{$surveyor_data->report}}" target="_blank">View Report </a>
                    <a href="{{ URL::asset('/public/media/report') }}/{{$surveyor_data->report}}" download>Download Report</a>
                    <a href="{{ URL::asset('/public/media/invoice') }}/{{$surveyor_data->invoice}}" target="_blank">View Invoice </a>
                    <a href="{{ URL::asset('/public/media/invoice') }}/{{$surveyor_data->invoice}}" download>Download Invoice</a>
                    <?php if(($user->type=='0' || $user->type=='1') && ($surveyor_data->status=='4'|| $surveyor_data->status=='5'|| $surveyor_data->status=='6'))
                        {
                            $rating=new App\Models\Rating;
                             $rating_data =  $rating->select('*')->where('survey_id',$surveyor_data->id)->where('operator_id',$operator_id_id)->count(); 
                        ?>
                        @if($rating_data==0)
                        <a href="javaScript:Void(0);" class="login"  onclick="Rating('{{$surveyor_data->id}}','{{$operator_id_id}}','{{$surveyor_id_id}}');" title="Give Rating " ><i class="fas fa-star" aria-hidden="true"></i> </a>   

                       @endif
                    <?php } ?>

                    @endif                
            </span>
                   
        <?php  ?>
        <?php }elseif(Auth::check() && ($user->type=='0' || $user->type=='1' )  && $surveyor_data->survey_type_id=="31" && $operator_bid_count>0){?>
            <span class="right-detail">
            <a href="JavaScript:Void(0);" class="login" data-toggle="modal" data-target="#addportModal" > View Quotes</a>
                                  
            </span>
        

        <?php }?>
       
      

				<div class="col-md-12">
					<div class="box-outer">
                        <p><span>Survey Number : </span>{{$surveyor_data->survey_number}}</p>
                        <p><span>Port Name : </span>{{$surveyor_data->portname}}</p>
                        <p><span>Date : </span> 
                            <?php echo  date("d M Y",strtotime($surveyor_data->start_date));?>
                            to  
                            <?php echo  date("d M Y",strtotime($surveyor_data->end_date));?>
                            @if(($user->type=='2' || $user->type=='3' || $user->type=='4') && $surveyor_data->status=='1')
                                <span style="float: right;">Change Start Date: 
                                    <span> 
                                    {!! Form::text('start_date',null, ['class' => 'form-control','placeholder' => 'Start Date','required'=>'required','id'=>'start_date','onkeypress' => 'error_remove()' ]) !!}
                                    {!! Form::hidden('survey_id',$surveyor_data->id,['id'=>'start_date_survey_id']) !!}

                                </span>
                                </span>
                                @endif
                        </p>                      
                        

                        <p><span>Survey Type : </span>{{$surveyor_data->surveytype_name}}</p>
                        <p><span>Cost of the Survey (USD):</span> <span id="total_price">
                        
                        @if($surveyor_data->status=='0' || $surveyor_data->status=='1' || $surveyor_data->status=='3'
                        || $surveyor_data->status=='4'
                        || $surveyor_data->status=='5'
                        || $surveyor_data->status=='6')  ${{$total_price}} 
                        @if(($surveyor_data->survey_type_id=='8' || $surveyor_data->survey_type_id=='23'
                             || $surveyor_data->survey_type_id=='24' || $surveyor_data->survey_type_id=='25' || 
                             $surveyor_data->survey_type_id=='29') && ($surveyor_data->status==1 || $surveyor_data->status==0))
                             /Day
                             @endif
                        
                        @if($port_price!="" && $surveyor_data->survey_type_id!='31') + ${{$port_price}} Transportation Cost  @endif
                         @endif 
                         </span>
                         </p>
						
                        <?php    
                        
                        $usertavle=new App\User;
                        if($user->type=='0'){
                            $survey_rec = $usertavle->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
                            ,'users.id')->where('status','1')
                            ->orderBy('users.first_name','asc')
                            ->orderBy('users.last_name','asc')
                            ->where('users.created_by',$user->id)
                            ->orWhere('users.id',$user->id)
                            ->get();
                        }
                        else if($user->type=='1'){
                            $users_created_by = $usertavle->select('created_by')->Where('users.id',$user->id)
                            ->first();  
                            $survey_rec = $usertavle->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
                            ,'users.id')->where('status','1')
                            ->orderBy('users.first_name','asc')
                            ->orderBy('users.last_name','asc')
                            ->where('users.created_by',$users_created_by->created_by)
                            ->orWhere('users.id',$users_created_by->created_by)
                            ->get();  
                        }
                        else{
                            $survey_rec = $usertavle->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
                            ,'users.id')->where('status','1')
                            ->orderBy('users.first_name','asc')
                            ->orderBy('users.last_name','asc')
                            ->where('users.created_by',$user->id)
                            ->orWhere('users.id',$user->id)
                            ->get();  
                        }
                        
                        $surveyoruser_box=array();
                        $operatoruser_box=array();
							
                        if(count($survey_rec)!=0)
						{
							$surveyorList=array();
							
							foreach($survey_rec as $data)
							{
								$Survey=new App\Models\Survey;
                                $operatoruser_box[$data->id]=$data->username;
                                $surveyoruser_box[$data->id]=$data->username;
                                
								
                            }
                            //dd($surveyoruser_box);
                          
                          
                        }?>
                        <p><span>Operator : </span>{{$opusername}}
                        @if(($user->type=='0' ||  $user->type=='1' ) && $surveyor_data->status=='1')
                                <span style="float: right;">  Change Operator: 
                                    <span> 
                                    {!! Form::select('operator_id',$operatoruser_box,$operator_id_id, ['id' => 'assign_to_op','class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()','onchange'=>"AssignToop(this,'$surveyor_data->id');"]) !!}
                                    </span>
                                </span>
                                @endif
                            </p>
                            
                        <p>
                       
                            <span> Surveyor : </span>{{$suusername}} 
                                @if($user->type=='2' && $surveyor_data->status=='1')
                                <span style="float: right;">  Change Surveyor: 
                                    <span> 
                                    {!! Form::select('surveyor_id',$surveyoruser_box,$surveyor_id_id, ['id' => 'assign_to','class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()','onchange'=>"AssignTo(this,'$surveyor_data->id');"]) !!}
                                    </span>
                                </span>
                                @endif
                               
                        </p>
                        <p><span>Surveyor Company : </span>{{$su_company_name}}</p>
                       
                        <p><span>Instructions  : </span>{{$surveyor_data->instruction}}</p>
                        <p><span>Instructions Document: </span> @if($surveyor_data->file_data!="")
                        <a href="{{ URL::asset('/public/media/survey') }}/{{$surveyor_data->file_data}}" target="_blank">View</a>
                        <a href="{{ URL::asset('/public/media/survey') }}/{{$surveyor_data->file_data}}" download>Download</a>
                        @endif</p>
                       

					</div>
					<h4>Vessels Details:</h4>
					<div class="box-outer">
						<p><span>Vessels Name : </span>{{$surveyor_data->vesselsname}}</p>
						<p><span>IMO Number : </span>{{$surveyor_data->imo_number}}</p>
						<p><span>Address : </span><i class="fas fa-map-marker-alt"></i> {{$surveyor_data->vesselsaddress}}</p>
						<p><span>Company Name : </span>{{$surveyor_data->vesselscompany}}</p>
						<p><span>Email Address : </span>{{$surveyor_data->vesselsemail}}</p>
                    </div>
                    <h4>Operator Company Details:</h4>
					<div class="box-outer">
						<p><span>Company Name : </span>{{$surveyor_data->operator_company}}</p>
						<p><span>Operator Name : </span>{{$surveyor_data->operator_name}}</p>
						<p><span>Website : </span> {{$surveyor_data->operator_company_website}}</p>
						<p><span>No of Surveys : </span>{{$operator_survey_count}}</p>
						<p><span>Country: </span>{{$country_data->name}}</p>
                        <p><span>Average invoice payment time:</span>24 days</p>
                        
                    </div>
                    <h4>Agents Details:</h4>
					<div class="box-outer">
						<p><span> Name : </span>{{$surveyor_data->agent_name}}</p>
						<p><span>Phone Number : </span>{{$surveyor_data->agentsmobile}}</p>
						<p><span>Email : </span> {{$surveyor_data->agentsemail}}</p>
						
					</div>
                    <?php if(Auth::check()  &&  ($user->type=='0' || $user->type=='1' ) && ($surveyor_data->status=="0" || $surveyor_data->status=="1"))
                    {?>
                        <span class="right-detail">
                            
                            <a href="javaScript:Void(0);" class="active" onclick="CustomstatusChange('{{$surveyor_data->id}}');">Cancel </a>
                           

                        </span>
                <?php } ?>
              
                <?php if(Auth::check()  &&  ($user->type=='2' || $user->type=='3' || $user->type=='4' ) && $surveyor_data->status=="1" )
                    {?>
                        <span class="right-detail">
                            
                            <a href="javaScript:Void(0);" class="active" onclick="CustomstatusChange('{{$surveyor_data->id}}');">Cancel </a>
                           

                        </span>
                <?php } ?>
				 </div>
			</div>
		</div>
    </section>
    <?php if(Auth::check() && ($user->type=='0' || $user->type=='1' )  && $surveyor_data->survey_type_id=="31" && $operator_bid_count>0){?>
    <div class="modal login-modal fade" id="addportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	         <div class="modal-content">
	        <div class="modal-header">
	        <h5 class="modal-title" style="font-size: 26px;"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Select Your Surveyor</h5>
	      </div>
	      <div class="modal-body">
	        <div class="login-inner">

            {!! Form::open(array('url' => 'BIdAccept', 'method' => 'post','name'=>'BIdAccept','files'=>true,'novalidate' => 'novalidate','id' => 'BIdAccept')) !!}

            <?php 
            $User=new App\User;
                    $csurvey_users=$User->select('users.id',
                    DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
                    ,'users.company','users.rating','custom_survey_users.amount')
                    ->leftJoin('custom_survey_users', 'custom_survey_users.surveyors_id', '=', 'users.id')
                    ->where('custom_survey_users.status','upcoming')
                    ->where('custom_survey_users.survey_id',$surveyor_data->id)->get();
            ?>

        <ul class="ccompany_details_list">
              
                    @foreach($csurvey_users as $cusers)
                        <li> 
                            <div class="form-group">
                            {{$cusers->username}} - {{$cusers->company}}  &nbsp;
                            <span>
                                <?php
                                for($i=1;$i<=5;$i++) {
                                    $selected = "";
                                
                                    if(!empty($cusers->rating) && $i<=$cusers->rating) {
                                    $selected = "selected";
                                    }
                                    ?>
                                <i class="fas fa-star <?php echo  $selected;?> "></i>
                                    <?php }  ?>
                                
                                
                            </span>
                           
                           <span> ${{$cusers->amount}} <input type="radio" name="surveyors_id" value="{{$cusers->id}}" style="float: right;margin: 9px;"></span>
                            </div>
                        </li>
                    @endforeach
                    {!! Form::hidden('survey_id',$surveyor_data->id,['id'=>'survey_id']) !!}
               
            </ul>
         <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                  {!! Form::close() !!}
	        </div>
	      </div>
	    </div>
	  </div>
    </div>
    <div id="ratingModal" class="modal fade form-modal" data-keyboard="false"  role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <i class="fa fa-user"></i>&nbsp;&nbsp;<span class='form-title'></span>
                </h4>
                <button type="button" class="close subbtn" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" id="RatingModal">

            </div>
        </div>
    </div>
</div> 

    <?php } ?>

<script>

 $(document).ready(function () {

        $("#start_date").datepicker({
            startDate: "today" ,
            format: 'yyyy-M-dd'
        });
		
    });

    $('#start_date').datepicker().on('changeDate', function (ev) {
        var start_date =$('#start_date').val()
        var survey_id =$('#start_date_survey_id').val()
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
        $.ajax({
                dataType: 'json',
                data: { survey_id:survey_id,start_date:start_date}, 
                type: "POST",
                url: '{{ URL::to('/ChangeStartDate') }}',
            }).done(function( data ) 
            {   
            if(data.class == 'success')
                {showMsg(data.message, "success");}
                $("#myModal").modal('hide');
                $('.datepicker').hide();
                $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/mysurvey')}}');
            });
        
    
});
</script>