<script type="text/javascript">
  function formatDate(date) {
		  	var monthNames = [
				"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
		  	];

		  	var day = date.getDate();
		  	var monthIndex = date.getMonth();
		  	var year = date.getFullYear();
		  	var hour = date.getHours();
		 	var min = date.getMinutes();

		  	return day + ' ' + monthNames[monthIndex] + ' '+ hour+':'+min;
		}
    $(document).ready(function () 
    { 
      $.LoadingOverlay("hide");
      survey_id    = '{{$survey_id}}'; //seeker
      senderID    = '{{$sender_id}}'; //seeker
			receiverID  = '{{$receiver_id}}'; // consultant
			$('.attachment_outer').hide();

      $.ajax({ 
                      type:'POST', 
                      url:'{{ URL::to('/updatechat') }}', 
                      data:'survey_id='+survey_id+'&sender_id='+senderID+'&receiver_id='+receiverID, 
                      success:function(html){ 
                         
                      } 
                  }); 

			var current_time  = formatDate(new Date());
      if(senderID>receiverID)
      {
        var chat_node     =  senderID+"_"+receiverID;
      }else{
        var chat_node     =  receiverID+"_"+senderID;
      }
		
           // alert(survey_id);
				firebase.database().ref('Recents/'+survey_id+'/'+chat_node).on('value', resp => {
                   
					chat_msg = resp.val();
          //alert(JSON.stringify(chat_msg));
					$(".chat_window").html('');
					var userId = senderID; 
					$.each(chat_msg,function(key,value){
            if(userId != value.senderId)
						{
                            
							if(value.senderImage!=''){
								var img = value.senderImage;
							}else{
								var img ='https://image.flaticon.com/icons/png/512/149/149071.png';
							}
							if(value.chatType=='Text') {//alert(1);
								var msg_html = '<div class="incoming_msg"><div class="received_msg"><div class="received_withd_msg"><p>'+value.message+'</p><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span></div></div></div>';
							}
							if(value.chatType=='Image') {
			                    var msg_html = '<div class="incoming_msg"><div class="received_msg"><div class="received_withd_msg"><a data-fancybox="images" href="'+value.message+'"><img src="'+value.message+'" alt="img"></a><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span></div></div></div>';
			                }
			               
						}else{
							if(value.chatType=='Text') {
								var msg_html = '<div class="outgoing_msg"><div class="sent_msg"><p>'+value.message+'</p><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span> </div></div>';
							}
							if(value.chatType=='Image') {
			                    var msg_html = '<div class="outgoing_msg"><div class="sent_msg"><a data-fancybox="images" href="'+value.message+'"><img src="'+value.message+'" alt="img"></a><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span> </div></div>';
			                }
						}
                        //alert(msg_html);
						$(".chat_window").append(msg_html);		
					});		
				    
					var elem = $(".chat_window");
					var iScrollHeight = $(".chat_window").prop("scrollHeight");		
					elem.animate({scrollTop:iScrollHeight});

			      	$('.chatDiv').show();
			  	});
			
       

    });

  
    document.getElementById("sendMsgBtn").addEventListener("click",myFunction );

    

    document.getElementById("msg").addEventListener('keyup',function(e){
     var msg= $('.msg').val();
    
     
      if (e.keyCode === 13) 
      {//alert(msg.length-1);
        var msge =msg.length-1;
       // alert();
        if(msge!=0){
            myFunction();
          }else{
             alert("Please fill out this field");
            }
        }
     
         
});


   function  myFunction(){

       <?php 
            $Users=new App\User;
       			$sender_user_data =  $Users->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'))->where('id',$sender_id)->first(); 
       			$receiver_user_data =  $Users->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'))->where('id',$receiver_id)->first(); 

       ?>
      survey_id    = '{{$survey_id}}'; //seeker
      senderID    = '{{$sender_id}}'; //seeker
			receiverID  = '{{$receiver_id}}'; // consultant
			senderName  = '{{$sender_user_data->username}}';
      receiverName  = '{{$receiver_user_data->username}}';

            

			senderImage = '1.jpg';
			$('.attachment_outer').hide();
			var date  = formatDate(new Date());
      if(senderID>receiverID)
      {
        var chat_node     =  senderID+"_"+receiverID;
      }else{
        var chat_node     =  receiverID+"_"+senderID;
      }			
      var senderMessage =  $('#msg').val();
			//var senderMessage = 'hi';
       // alert(chat_node);
      

			if(senderMessage != '' && senderMessage != null && senderMessage != undefined)
      {
        $.ajax({ 
                      type:'POST', 
                      url:'{{ URL::to('/addchat') }}', 
                      data:'survey_id='+survey_id+'&sender_id='+senderID+'&receiver_id='+receiverID+'&msg='+senderMessage, 
                      success:function(html){ 
                         
                      } 
                  }); 

				firebase.database().ref('Recents/'+survey_id+'/'+chat_node).push().set({
						
                        "chatType" : "Text",
                        "date" : date,
                        "message" : senderMessage,
                        "receiverId" : receiverID,
                        "receiverName" : receiverName,
                        "senderId" : senderID,
                        "senderImage" : "",
                        "senderName" : senderName,
                        "timestamp" : ""
				}); 
				$('.msg').val('');
				
				firebase.database().ref('Recents/'+survey_id+'/'+chat_node).on('value', resp => {
                   
					chat_msg = resp.val();
                   //alert(JSON.stringify(chat_msg));
					$(".chat_window").html('');
					var userId = senderID; 
					$.each(chat_msg,function(key,value){
						if(userId != value.senderId)
						{
                            
							if(value.senderImage!=''){
								var img = value.senderImage;
							}else{
								var img ='https://image.flaticon.com/icons/png/512/149/149071.png';
							}
							if(value.chatType=='Text') {//alert(1);
								var msg_html = '<div class="incoming_msg"><div class="received_msg"><div class="received_withd_msg"><p>'+value.message+'</p><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span></div></div></div>';
							}
							if(value.chatType=='Image') {
			                    var msg_html = '<div class="incoming_msg"><div class="received_msg"><div class="received_withd_msg"><a data-fancybox="images" href="'+value.message+'"><img src="'+value.message+'" alt="img"></a><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span></div></div></div>';
			                }
			               
						}else{
							if(value.chatType=='Text') {
								var msg_html = '<div class="outgoing_msg"><div class="sent_msg"><p>'+value.message+'</p><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span> </div></div>';
							}
							if(value.chatType=='Image') {
			                    var msg_html = '<div class="outgoing_msg"><div class="sent_msg"><a data-fancybox="images" href="'+value.message+'"><img src="'+value.message+'" alt="img"></a><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span> </div></div>';
			                }
						}
                        //alert(msg_html);
						$(".chat_window").append(msg_html);		
					});		
				    
					var elem = $(".chat_window");
					// var iScrollHeight = $(".chat_window").prop("scrollHeight");		
					// elem.animate({scrollTop:iScrollHeight});

			      	$('.chatDiv').show();
			  	});
			}
       

    }

    function uploadFile(){

<?php 
$Users=new App\User;
      $sender_user_data =  $Users->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'))->where('id',$sender_id)->first(); 
      $receiver_user_data =  $Users->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username'))->where('id',$receiver_id)->first(); 

?>
survey_id    = '{{$survey_id}}'; //seeker
senderID    = '{{$sender_id}}'; //seeker
receiverID  = '{{$receiver_id}}'; // consultant
senderName  = '{{$sender_user_data->username}}';
receiverName  = '{{$receiver_user_data->username}}';

senderImage = '1.jpg';
$('.attachment_outer').hide();
var date  = formatDate(new Date());
if(senderID>receiverID)
{
 var chat_node     =  senderID+"_"+receiverID;
}else{
 var chat_node     =  receiverID+"_"+senderID;
}			
//alert(chat_node);
//var senderMessage =  $('#msg').val();
//var senderMessage = 'hi';
// alert(chat_node);
	var storageRef = firebase.storage().ref();      // Get the file from DOM
     var file = document.getElementById("files").files[0];
    // alert(file.name);
      
      //dynamically set reference to the file name
      var thisRef = storageRef.child(chat_node+'/'+file.name);
     // var path = spaceRef.fullPath;
     // console.log(thisRef.fullPath);
      thisRef.put(file).then(function(snapshot) {
         //alert("File Uploaded")
         //console.log('Uploaded a blob or file!');
      });
      thisRef.getDownloadURL().then(function(url) {
        
              firebase.database().ref('Recents/'+survey_id+'/'+chat_node).push().set({
          
          "chatType" : "Image",
          "date" : date,
          "message" : url,
          "receiverId" : receiverID,
          "receiverName" : receiverName,
          "senderId" : senderID,
          "senderImage" : "",
          "senderName" : senderName,
          "timestamp" : ""
      }); 
      if(url != '' && url != null && url != undefined){
        

        $('.msg').val('');
        
        firebase.database().ref('Recents/'+survey_id+'/'+chat_node).on('value', resp => {
                   
          chat_msg = resp.val();
                   //alert(JSON.stringify(chat_msg));
          $(".chat_window").html('');
          var userId = senderID; 
          $.each(chat_msg,function(key,value){
            if(userId != value.senderId)
            {
                            
              if(value.senderImage!=''){
                var img = value.senderImage;
              }else{
                var img ='https://image.flaticon.com/icons/png/512/149/149071.png';
              }
              if(value.chatType=='Text') {//alert(1);
                var msg_html = '<div class="incoming_msg"><div class="received_msg"><div class="received_withd_msg"><p>'+value.message+'</p><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span></div></div></div>';
              }
              if(value.chatType=='Image') {
                          var msg_html = '<div class="incoming_msg"><div class="received_msg"><div class="received_withd_msg"><a data-fancybox="images" href="'+value.message+'"><img src="'+value.message+'" alt="img"></a><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span></div></div></div>';
                      }
                     
            }else{
              if(value.chatType=='Text') {
                var msg_html = '<div class="outgoing_msg"><div class="sent_msg"><p>'+value.message+'</p><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span> </div></div>';
              }
              if(value.chatType=='Image') {
                          var msg_html = '<div class="outgoing_msg"><div class="sent_msg"><a data-fancybox="images" href="'+value.message+'"><img src="'+value.message+'" alt="img"></a><span class="time_date"><i class="fas fa-clock"></i>'+value.date+'</span> </div></div>';
                      }
            }
                        //alert(msg_html);
            $(".chat_window").append(msg_html);		
          });		
            
          var elem = $(".chat_window");
          var iScrollHeight = $(".chat_window").prop("scrollHeight");		
          elem.animate({scrollTop:iScrollHeight});
       
              $('.chatDiv').show();
          });
       }
})



}
    </script>
<style>
        .incoming_msg {
            display: inline-block;
            width: 100%;
        }
        .incoming_msg .received_msg {
            display: inline-block;
            width: 100%;
        }
        .incoming_msg .received_msg .received_withd_msg,
        .outgoing_msg .sent_msg {
            display: inline-block;
            width: 100%;
            margin-bottom: 30px;
            position: relative;
        }
        .incoming_msg .received_msg .received_withd_msg p {
            margin: 0px;
            background: #2f3c7f;
            color: #fff;
            padding: 5px 10px;
            border-radius: 20px;
            width: auto;
            display: inline-block;
            max-width: 70%;
            word-wrap: anywhere;
        }
        .outgoing_msg .sent_msg p {
            margin: 0px;
            background: #ec8a5d;
            color: #fff;
            padding: 5px 10px;
            border-radius: 20px;
            width: auto;
            display: inline-block;
            max-width: 70%;
            word-wrap: anywhere;
        }
        .incoming_msg .received_msg .received_withd_msg .time_date {
            position: absolute;
            bottom: -20px;
            left: 0px;
            font-size: 12px;
            color: #888;
        }
        .outgoing_msg .sent_msg .time_date {
            position: absolute;
            bottom: -20px;
            right: 0px;
            font-size: 12px;
            color: #888;
        }
        .incoming_msg .received_msg .received_withd_msg .time_date i,
        .outgoing_msg .sent_msg .time_date i {
            margin-right: 5px;
        }
        .incoming_msg .received_msg .received_withd_msg  a,
        .outgoing_msg .sent_msg a {
          display: inline-block;
          color: #fff;
        }
        .incoming_msg .received_msg .received_withd_msg a img,
        .outgoing_msg .sent_msg a img {
            max-width: 150px;
            background: #2f3c7f;
            padding: 5px;
            border-radius: 2px;
            height: 150px;
            min-width: 150px;
            object-fit: cover;
        }
        .outgoing_msg .sent_msg a img{
            background: #ec8a5d;
        }
        .outgoing_msg {
            display: inline-block;
            width: 100%;
            text-align: right;
        }
        .massage_outer {
          display: flex;
          width: 100%;
          background: #f4f4f4;
          border-radius: 0px;
        }
        .massage_outer .msgInner-outer {
          width: calc(100% - 48px);
          display: flex;
          overflow: hidden;
        }
        .massage_outer .msgInner-outer #msg {
          border: 0px;
          resize: none;
          height: 43px;
          width: calc(100% - 30px);
          float: left;
          background: #f4f4f4;
          padding: 8px 14px;
          border-radius: 55px 0px 0px 55px;
        }
        .massage_outer .msgInner-outer .file_attachment {
          width: 30px;
          float: left;
          overflow: hidden;
          position: relative;
          height: 43px;
          left: 3px;
          background: #f4f4f4;
        }
        .massage_outer .msgInner-outer .file_attachment #files {
          width: 100%;
          opacity: 0;
          position: absolute;
          top: 0px;
          bottom: 0px;
          z-index: 100;
        }
        .massage_outer .msgInner-outer .file_attachment i {
          position: relative;
          left: 1px;
          top: 11px;
          color: #ec8a5d;
          font-size: 20px;
        }
        .send_btn .sendMsgBtn {
          background: transparent;
          border: 0px;
          outline: none;
          box-shadow: none;
          padding: 0px;
        }
        .send_btn .sendMsgBtn img {
          width: 31px;
          position: relative;
          top: 7px;
          right: -6px;
        }
        .chat_window {
          max-height: 300px;
          overflow-y: auto;
          overflow-x: hidden;
          padding-top: 15px;
      }
      .chatModal .modal-body {
        padding: 0px;
      }
      .outgoing_msg, .incoming_msg {
          padding: 0px 15px;
      }
  </style>
<div class="chatDiv">
<!-- <button class="open-button" onclick="openForm()">Chat</button> -->
<div class="chat-popup" id="myForm">
  <div class="form-container">
    
    <div class="chat_window">
      
    </div>

    <div class="progressDiv">
      <div class="progressDivInner">
        <span class="progressMsgSuccess"></span>
        <span class="progressMsgError"></span>
      </div>
    </div>

    <div class="massage_outer">
      <div class="msgInner-outer">
        <textarea id="msg"  placeholder="Type message.."  class="msg" name="msg" required ></textarea>
        <span class="file_attachment">
        <input type="file" onchange="uploadFile()" id="files" name="files" /> 
         <i class="fa fa-paperclip" aria-hidden="true"></i>
        </span>
      </div>
      <span class="send_btn"><button class="sendMsgBtn" id="sendMsgBtn"><img src="{{ URL::asset('/media') }}/send.png" align=""></button></span>
    </div>
  </div>
</div>
</div>
