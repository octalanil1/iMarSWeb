@extends('layouts.master')
@section('title')My Account @stop
@section('description')My Account @stop
@section('keywords')My Account @stop
@section('content')    
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<!-- datepicker -->
<script src="{{ asset('/public/admin_assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <!-- Date Picker -->
  <link rel="stylesheet" href="{{ asset('/public/admin_assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <!-- Daterange picker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.3.2/bootbox.js"></script>

  <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase.js"></script>
  <script type="text/javascript">
    var config = {
        apiKey: "AIzaSyCe1Dwe9qjc0HULjRJD4apzLkAORoCOwpY",
        authDomain: "",
        databaseURL: "https://imars-b70c5.firebaseio.com/",
        projectId: "imars-b70c5",
        storageBucket: "gs://imars-b70c5.appspot.com",
        messagingSenderId: "864642153946"
    };
    firebase1 = firebase.initializeApp(config);
</script>
<!-- <script src='https://cdn.firebase.com/js/client/2.2.1/firebase.js'></script> -->

<script type="text/javascript">
    var init = [];
    function showpage(href) 
	{
		setTimeout( function(){ 
			loadPiece( href);
  					}  , 200 );
		
    }
 
    function loadPiece( href ) {
		var lastPart = href.split("/").pop();
	  	document.title=lastPart.toUpperCase();
     $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });

		        $.ajax({
						type: 'POST',
						url: href,
						beforeSend:  function(){
							$.LoadingOverlay("show");
						},
						
						success: function(msg){
							$('#replace-div').html(msg);
							$.LoadingOverlay("hide");
							
							return false;
						},
						error: function (error) {
							$.LoadingOverlay("hide");
							showMsg("Something Went Wrong!", "danger");
						}
					});
    }

    $(document).ready(function() {
		loadPiece( '{{ URL::to('/myprofile') }}');
	})


</script>

<section class="main-outer">
		<div class="container">
			<div class="siderbarmenu-button" style="display: none;">
        		<button class="side-btn"><i class="fas fa-bars"></i></button>
        	</div>
        	<span class="custon-sidebar">
	        <nav class="side_bar">
	              <div class="menu_nav">
					  <h3>My Account </h3>
					  
	                 @include('includes.myaccount-menu')
	              </div>
	          </nav>
	          <div class="overlay"></div>
			</span>
			<?php //$user = Auth::user(); ?>
	        <div id="replace-div">

			</div>
        </div>
	</section>
@stop



      



