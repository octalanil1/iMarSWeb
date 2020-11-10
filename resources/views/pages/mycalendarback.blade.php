
<script>
   
   $(document).ready(function() {
	   
	var calendar = $('#calendar').fullCalendar({
	 editable:true,
	 header:{
	  left:'prev,next today',
	  center:'title',
	  right:'month'
	 },
	 events: '{{ URL::to('/eventsload') }}',
	 selectable:true,
	 selectHelper:true,
	 displayEventTime :false,
	 eventRender: function(event, element,calEvent)
	  {

		//here i am adding icon next to title
		
		if(event.type=="survey"){
			element.find(".fc-title").before($("<span class=\"fc-event-icons\"></span>").html("<img style=\"width: 24px;\" src=\"{{ URL::asset('/public/media') }}/logo-icon.png\" />")
		);

		}
	 },
	//  eventRender: function (event, element, view) { 
	// 	 // event.start is already a moment.js object
	// 	 // we can apply .format()
	// 	 var dateString = event.start.format("YYYY-MM-DD");
		 
	// 	 $(view.el[0]).find('.fc-day[data-date=' + dateString + ']').css('background-color', '#FAA732');
	//   },
	
	 select: function(start, end, allDay)
	 {
		var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
		var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");

				bootbox.prompt({
			title: "PLease Select Availability",
			
			inputType: 'radio',
			inputOptions: [{text: 'ON',value: '1',},{text: 'OFF',value: '0',}],
			callback: function (result) 
			{
				//console.log(result);
				
				
				$.ajax({
				url:"{{ URL::to('/eventsadd') }}",
				type:"POST",
				data:{title:result, start:start, end:end},
				success:function()
				{
				calendar.fullCalendar('refetchEvents');
				alert("Availability Added Successfully");
				}
			})
			}
		});
//alert(result);
			
	 },
	 editable:true,
	 eventResize:function(event)
	 {
	  var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
	  var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
	  var title = event.title;
	  var id = event.id;
	  $.ajax({
	   url:"{{ URL::to('/eventsupdate') }}",
	   type:"POST",
	   data:{title:title, start:start, end:end, id:id},
	   success:function(){
		calendar.fullCalendar('refetchEvents');
		alert('Availability Update');
	   }
	  })
	 },
 
	 eventDrop:function(event)
	 {
		if(event.type!="survey")
		{
			var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
			var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
			var title = event.title;
			var id = event.id;
			$.ajax({
			url:"{{ URL::to('/eventsupdate') }}",
			type:"POST",
			data:{title:title, start:start, end:end, id:id},
			success:function()
			{
				calendar.fullCalendar('refetchEvents');
				alert("Availability  Changed");
			}
			});
		}
	 },
 
	 eventClick:function(event)
	 {
		// alert(event.type);

		if(event.type!="survey"){
			if(confirm("Are you sure you want to remove it?"))
			{
			var id = event.id;
			$.ajax({
				url:"{{ URL::to('/eventsdelete') }}",
				type:"POST",
				data:{id:id},
				success:function()
				{
				calendar.fullCalendar('refetchEvents');
				alert("Availability Removed");
				}
			})
			}
		}
	
	 },
	 eventMouseover: function (data, event, view) 
	 { 
		// alert(data.type);
		if(data.type=="survey")
		{
			$('#UserModal').html(''); $(".form-title").text('Survey Detail');
			$('#UserModal').load('{{ URL::to('/event-detail') }}'+'/'+data.id);
			$("#myModal").modal();
		}

		},
		eventMouseout: function (data, event, view) {
		$(this).css('z-index', 8);

		$('.tooltiptopicevent').remove();

		},
 
	});
   });
	
   </script>
<section class="page">
	<div class="row">
		<div class="col-md-12 col-lg-12 col-xl-12">
			<div class="surveyors">
			<div id="calendar"></div>

						
					</div>
				</div>
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
  