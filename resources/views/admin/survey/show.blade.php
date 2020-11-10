@extends('layouts.adminmaster')
@section('title')
IMARS | Survey Manager
@stop
@section('content') 
<style>
  #replace-div{overflow: scroll;}
  .select2-container .select2-selection--single{
    height: 32px!important;
  }
  .select2-container{
    margin-top: 19px!important;
  }
  .select2.select2-container {

width: 100% !important;
margin: 0px !important;
border-radius: 0px !important;

}
  </style>
<script type="text/javascript">

    var init = [];

    function search() {

        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/admin/survey') }}',
            data: $('#mySearchForm').serialize(),
            beforeSend: function(){
                $.LoadingOverlay("show");
            },
            success: function(msg){
              
             
                $('#replace-div').html(msg);
                $('.loading-top').fadeOut();
                $('html,body').animate({scrollTop:$('.page-user').offset().top-0},1400);
                $.LoadingOverlay("hide");
                return false;
            }
        });
    }

    function exportData() {
        var UserSearchName = $.trim($('#UserSearchName').val());
        var UserEmail = $.trim($('#UserEmail').val());           
        var UserMobile = $.trim($('#UserMobile').val());             
        var UserAddress = $.trim($('#UserAddress').val());           
        var UserCreated = $.trim($('#UserCreated').val());           
        var UserTodate = $.trim($('#UserTodate').val());           
        var UserSearchStatus = $.trim($('#UserSearchStatus').val());          
        window.location.href = '/karicare-admin/export-users?search_name='+UserSearchName+'&email='+UserEmail+'&mobile='+UserMobile+'&address='+UserAddress+'&created='+UserCreated+'&todate='+UserTodate+'&search_status='+UserSearchStatus;    
    }

    function add_record() {

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('Add User');
        $('#UserModal').load('{{ URL::to('/admin/add-user') }}');
        $("#myModal").modal();
    }

    function edit_record(edit_id) {

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('Edit User');
        $('#UserModal').load('{{ URL::to('/admin/edit-user') }}'+'/'+edit_id);
        $("#myModal").modal();
    }

    function view_record(view_id) {

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('View Survey');
        $('#UserModal').load('{{ URL::to('/admin/view-survey') }}'+'/'+view_id);
        $("#myModal").modal();
    }
   function view_records(view_id) { 

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('View User');
        $('#UserModal').load('{{ URL::to('/admin/view-user') }}'+'/'+view_id);
        $("#myModal").modal();
    }
	
	 function view_surveyor(view_id) { 

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('View Surveryor User');
        $('#UserModal').load('{{ URL::to('/admin/view-user') }}'+'/'+view_id);
        $("#myModal").modal();
    }
	function view_company(view_id) { 

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('View Company');
        $('#UserModal').load('{{ URL::to('/admin/view-company') }}'+'/'+view_id);
        $("#myModal").modal();
    }
	
	function changeStatus(view_id, status_id) { 

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('Change Status');
        $('#UserModal').load('{{ URL::to('/admin/change-surveryor-status') }}'+'/'+view_id+'/'+status_id);
        $("#myModal").modal();
		 $.LoadingOverlay("hide");
    }
	
    function loadPiece( href ) {

        $('body').on('click', 'ul.pagination a', function() {
          var getPage = $(this).attr('href').split('page=')[1];
            //alert(getPage);
            var go_url = href+'?page='+getPage;
            $.ajax({
                type: 'POST',
                url: go_url,
                beforeSend:  function(){
                    $.LoadingOverlay("show");
                },
                data: ($('#mySearchForm').serialize()),
                success: function(msg){
                    $('html,body').animate({scrollTop:$('.page-user').offset().top-0},1400);
                    $('#replace-div').html(msg);
                    $.LoadingOverlay("hide");
                    return false;
                }
            });
            return false;
        });
    }

    function statusChange(id) 
    {
      $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
      $.ajax({
            dataType: 'json',
            data: { id:id}, 
            type: "POST",
            url: '{{ URL::to('/admin/user-status') }}',
        }).done(function( data ) 
        {   
          search();
            $("#msg-data").html(data.message);
          
          
        });
        
    }

    $(document).ready(function() {

      $('#port_id').select2();
      $('#ship_id').select2();

        loadPiece( '{{ URL::to('/admin/survey') }}');
    })

</script>
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
//     function view_chat(sender_id,receiver_id) {

// $.LoadingOverlay("show");
// $('#UserModal2').html(''); $(".form-title").text('Chat');
// $('#UserModal2').load('{{ URL::to('/admin/chat-form') }}'+'/'+sender_id+'/'+receiver_id);
// $("#myModal2").modal();
// }

function view_chat(survey_id,sender_id,receiver_id) {

$.LoadingOverlay("show");
$('#UserModal1').html(''); $(".form-title").text('Chat');
$('#UserModal1').load('{{ URL::to('/admin/chat-form') }}'+'/'+survey_id+'/'+sender_id+'/'+receiver_id);
$("#myModal1").modal();
}
</script>

<link rel="stylesheet" type="text/css" href="{{ asset('admin_assets/bower_components/select2/dist/css/select2.min.css') }}"/>
<script type="text/javascript" src="{{ asset('admin_assets/bower_components/select2/dist/js/select2.min.js') }}"></script>

<section class="content-header">
      <h1>All Surveys </h1>
      <ol class="breadcrumb">
        <li><a href="{{ URL::to('/admin') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"> All Surveys </li>
      </ol>
    </section>
    <section class="" style="padding: 15px;">
      <div class="row">
        <div class="col-xs-12 page-user">
          <div class="box">
            <div class="box-body">
            {!! Form::open(array('url' => '/admin/users', 'method' => 'post','name'=>'mySearchForm','files'=>true,'novalidate' => 'novalidate','id' => 'mySearchForm')) !!}

            <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
              <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="datepicker" name="arrival_start_date" placeholder="Arrival Date Start">
              </div>
            </div>

            <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
              <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="datepicker2" name="arrival_end_date" placeholder="Arrival Date End">
              </div>
            </div>
			
			 <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
              <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="datepicker3" name="start_date" placeholder="Created Date Start">
              </div>
            </div>
			 <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
              <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="datepicker4" name="end_date" placeholder="Created Date End">
              </div>
            </div>
			<div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            {!! Form::text('operator_name',null, ['class' => 'form-control','required'=>'required','placeholder'=>'Operator Name','onkeypress' => 'error_remove()']) !!}
           </div>
		   <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            {!! Form::text('operator_company',null, ['class' => 'form-control','required'=>'required','placeholder'=>'Operator Company','onkeypress' => 'error_remove()']) !!}
           </div>
			<div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            {!! Form::text('surveyor_name',null, ['class' => 'form-control','required'=>'required','placeholder'=>'Surveyor Name','onkeypress' => 'error_remove()']) !!}
           </div>
		   
		   <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            {!! Form::text('surveyor_company',null, ['class' => 'form-control','required'=>'required','placeholder'=>'Surveyor Company','onkeypress' => 'error_remove()']) !!}
           </div>
			 <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
            <?php //$helper=new App\Helpers;?>
            {!! Form::select('ship_id',$ship_box ,null, ['class' => 'form-control','id'=>'ship_id','required'=>'required','onkeypress' => 'error_remove()']) !!}
           </div>
			
            <!--<div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
            {!! Form::select('user_id',$user_box,null, ['class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()']) !!}
           </div>-->
           <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
            {!! Form::select('port',$port_box,null, ['class' => 'form-control','required'=>'required','id'=>'port_id','onChange' => 'error_remove()' ]) !!}

           </div>
           <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
            <?php $helper=new App\Helpers;?>
            {!! Form::select('status',$helper->GetSurveyStatus(),null, ['class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()']) !!}
           </div>
		   
		    <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
            <?php //$helper=new App\Helpers;?>
            {!! Form::select('srveryor_category',$category_box ,null, ['class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()']) !!}
           </div>

           <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            {!! Form::text('operator_email',null, ['class' => 'form-control','required'=>'required','placeholder'=>'Operator Email','onkeypress' => 'error_remove()']) !!}
           </div>
			<div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            {!! Form::text('surveyor_email',null, ['class' => 'form-control','required'=>'required','placeholder'=>'Surveyor Email','onkeypress' => 'error_remove()']) !!}
           </div>
           <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            <?php $helper=new App\Helpers;?>
            {!! Form::select('country_id',$helper->CountryList(),null, ['class' => 'form-control','required'=>'required','id'=>'country_id','onchange'=>'Getcountrycode();']) !!}
           </div>
           <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            {!! Form::text('survey_number',null, ['class' => 'form-control','required'=>'required','placeholder'=>'Job Id','onkeypress' => 'error_remove()']) !!}
           </div>
           <div class="col-xs-12 col-sm-6 col-md-4">
                <label></label>
                <input style="margin-top: 20px;" id="search" class="btn btn-outline-primary pull-left" type="submit" value="Search">
                <button style="margin-top: 20px;margin-left: 10px;" onclick="resetSearchForm();" class="btn btn-outline-success pull-left" type="submit">Reset</button>
          </div>
            {!! Form::close() !!}
           
            

            </div>
        </div>
        </div>
        </div>
      </section>
     <div id="msg-data"> </div>

<section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"> All Surveys</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body" id="replace-div">
                 @include('admin.survey.search')
           </div>
            <!-- /.box-body -->
          </div>
         
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
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
<div id="myModal2" class="modal fade chatModal form-modal" data-keyboard="false"  role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <i class="fa fa-user"></i>&nbsp;&nbsp;<span class='form-title'></span>
                </h4>
                <button type="button" class="close subbtn" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" id="UserModal2">

            </div>
        </div>
    </div>
</div> 

</div>
</div>
<script>
  $(function () {
    //Date picker
    $('#datepicker').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    })
    $('#datepicker2').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    })
	 $('#datepicker3').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    })
	 $('#datepicker4').datepicker({
      autoclose: true,
        format: 'yyyy-mm-dd'
    })
    
  })
</script>
@stop 