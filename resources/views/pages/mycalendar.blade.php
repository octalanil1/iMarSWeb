<style>
.form-group .switch { width: 70px; height: 30px; display: inline-block; border-radius: 35px; overflow: hidden; position: relative; margin:5px 0px;}
.form-group .switch input { width: 100%; height: 100%; opacity: 0; position: absolute; top: 0px; left: 0px; z-index: 2; padding: 0px 0px; margin: 0px 0px;}
.form-group .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #eaeaea; -webkit-transition: .4s; transition: .4s;}
.form-group .slider:before { width: 22px; height: 22px; border-radius: 50%; content: "Off"; position: absolute; left: 4px; bottom: 4px; background-color: #ffffff; -webkit-transition: .5s; transition: .5s; text-indent: 30px; font-size: 14px; color: #008000;}
.form-group .switch input:checked + .slider { background-color: #008000;}
.form-group .switch input:checked + .slider:before { left: calc(100% - 26px); content: "On"; color: #ffffff; text-indent: -30px;}
.center-box .form-group {
    width: 83%;
    margin: 10px auto 10px;
}
.myclass {
    width: 83%;
    margin: 0 auto;
}
ul li ul.dropdown{
        min-width: 100%; /* Set width of the dropdown */
        background: #f2f2f2;
        display: none;
        position: absolute;
        z-index: 999;
        left: 0;
        overflow: scroll;
        height: 79px;
    }
    ul li:hover ul.dropdown{
        display: block;	/* Display the dropdown */
    }
    ul li ul.dropdown li{
        display: block;
    }
    ul li ul.dropdown li {

display: block;
height: 32px;
padding: 1px;


}
</style>
<?php   
    $user = Auth::user();	
    if($user->type=='2'){
?>
<script> 
        function getCalendar(target_div, year, month){ 
                var surveyor_id=document.getElementById("surveyor_id").value;  

                  $.ajax({ 
                      type:'POST', 
                      url:'{{ URL::to('/GetCalender') }}', 
                      data:'func=getCalender&year='+year+'&month='+month+'&surveyor_id='+surveyor_id, 
                      success:function(html){ 
                          $('#'+target_div).html(html); 
                      } 
                  }); 
              } 
    </script>
    <?php }else{?>
        <script> 
        function getCalendar(target_div, year, month){ 
                

                  $.ajax({ 
                      type:'POST', 
                      url:'{{ URL::to('/GetCalender') }}', 
                      data:'func=getCalender&year='+year+'&month='+month, 
                      success:function(html){ 
                          $('#'+target_div).html(html); 
                      } 
                  }); 
              } 
    </script>
    <?php } ?>
              <script> 
              function getEvents(date){ 
                  $.ajax({ 
                      type:'POST', 
                      url:'{{ URL::to('/GetCalender') }}', 
                      data:'func=getEvents&date='+date, 
                      success:function(html){ 
                          $('#event_list').html(html); 
                          $('#event_list').slideDown('slow'); 
                      } 
                  }); 
              } 
			  function Onoff(date,onoff){ 
				$.LoadingOverlay("show");
                  $.ajax({ 
                      type:'POST', 
                      url:'{{ URL::to('/eventsadd') }}', 
                      data:'title='+onoff+'&start='+date, 
                      success:function(html){ 
                        
						  $.LoadingOverlay("hide");
						  getEvents();
						  showpage('{{URL::asset('/mycalendar')}}');


                      } 
                  }); 
              } 
               
              $(document).ready(function(){ 
                  $('.date_cell').mouseenter(function(){ 
                      date = $(this).attr('date'); 
                      $(".date_popup_wrap").fadeOut(); 
                      $("#date_popup_"+date).fadeIn();     
                  }); 
                  $('.date_cell').mouseleave(function(){ 
                      $(".date_popup_wrap").fadeOut();         
                  }); 
                  $(document).on("change", '.month_dropdown', function(event) { 
                    getCalendar('calendar_div', $('.year_dropdown').val(), $('.month_dropdown').val()); 
                  //$('.month_dropdown').on('change',function(){ 
                     // getCalendar('calendar_div', $('.year_dropdown').val(), $('.month_dropdown').val()); 
                  }); 
                  $(document).on("change", '.year_dropdown', function(event) { 
                  //$('.year_dropdown').on('change',function(){ 
                      getCalendar('calendar_div', $('.year_dropdown').val(), $('.month_dropdown').val()); 
                  }); 
                  $(document).click(function(){ 
                      $('#event_list').slideUp('slow'); 
                  }); 
              }); 

function Calendersearch() 
            { $.LoadingOverlay("show");   
            $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
              var surveyor_id=document.getElementById("surveyor_id").value;  
           // alert(country_id);
      $.ajax({
            
         
            type: "POST",
			url:'{{ URL::to('/GetCalender') }}', 
          data:'func=getCalender&surveyor_id='+surveyor_id, 
        }).done(function( html ) 
        { //alert(html);
			$('#calendar_div').html(html); 
                         
						  $.LoadingOverlay("hide");
						  getEvents();
						 // showpage('{{URL::asset('/mycalendar')}}');
           
          
        });
        
    }
    function IsAvail()
{
    var is_avail=$('#is_avail').val();
   
    if(is_avail=='0'){
        var result = confirm("Do you want to make yourself available again? You will be listed in operators’ search results in your available days… ");
    }else{
        var result = confirm("You will be made unavailable for an indefinite time. You will not be listed in operators’ search results during this time. Do you want to proceed?");
    }
    

    if(result){
      
        $.LoadingOverlay("show");
        $.ajax({
            dataType: 'json',
        type: "POST",
        url: '{{ URL::to('/isavail') }}',
        data: { is_avail: is_avail },
        success: function(data)
        {
            $.LoadingOverlay("hide");
            if(data.success==false)
            {
                if(data.class == 'danger'){showMsg("Something Went Wrong", "danger");}

            }else
            {
                if(data.class == 'success'){showMsg(data.message, "success");}
                


                 showpage('{{URL::asset('/mycalendar')}}');
            }
           
        }
        });
    }
    
}

function view_record(view_id) {

$.LoadingOverlay("show");
$('#UserModal').html(''); $(".form-title").text('Survey Detail');
$('#UserModal').load('{{ URL::to('/survey-detail-cal') }}'+'/'+view_id);
$("#myModal").modal();
}
          </script> 
<section class="page">
	<div class="row">
		<div class="col-md-12 col-lg-12 col-xl-12">
        <?php $helper=new App\Helpers;  
                        $user = Auth::user();	
      
                        $usertavle=new App\User;
                        $survey_rec = $usertavle->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
						,'users.id')->where('status','1')
						->where('users.created_by',$user->id)
                        ->get();
                        $surveyoruser_box=array();
                        if(count($survey_rec)!=0)
						{
							$surveyorList=array();
							$surveyoruser_box=array(''=>'Select Surveyor');
							
							
							$surveyoruser_box[$user->id]=$user->first_name.' '.$user->last_name;
							foreach($survey_rec as $data)
							{
                                    $surveyoruser_box[$data->id]=$data->username;
                                
                            }

                           $surveyoruser_box['all']='All Surveyor';
						}
        ?>


        <div class="right-flex-box">
                    <h4>{{$user->first_name }}'s Calendar : </h4>
                </div>
			<div class="surveyors">
					 @if(!empty($surveyoruser_box) && $user->type=='2' )
						   		{!! Form::select('surveyor_id',$surveyoruser_box,null, ['class' => 'form-control', 'id' => 'surveyor_id','required'=>'required','onkeypress' => 'error_remove()','onchange'=>'Calendersearch();']) !!}
							@endif
			<div id="calendar_div">
			<?php 
                $helper=new App\Helpers;
                echo $helper->getCalender(); ?>

						
					</div>
				</div>
			</div>
        </div>
        <div class="row">
        <div class="col-md-12 center-box">
            <div class="form-group">
            <?php $user = Auth::user();?>
            <span class="switch"><input name="is_avail" type="checkbox" @if($user->is_avail=='1') checked="checked" @endif id='is_avail' value="{{$user->is_avail}}" onclick='IsAvail();'><span class="slider">&nbsp;</span></span>
            </div>
            <ul class="myclass" style="list-style:none">
              <li><div class="foo fgreen"></div> &nbsp;&nbsp;<span> Available </span></li>
              <li><div class="foo fgrey"></div>&nbsp;&nbsp;<span> Unavailable</span></li>
            </ul>
            </div>
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
  <style>
      .foo {
  float: left;
  width: 20px;
  height: 20px;
  padding: 5px;
  border: 1px solid rgba(0, 0, 0, .2);
}

.fgreen {
  background: green !important;
}

.fgrey {
  background: #DDDDDD !important;
}
      </style>