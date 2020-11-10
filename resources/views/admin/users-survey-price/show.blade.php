@extends('layouts.adminmaster')
@section('title')
IMARS | User Survey Price Manager
@stop
@section('content') 
<style>
  #replace-div{overflow: scroll;}
  
  .select2-container{
    margin: 4px!important;
  }
 
  </style>
<script type="text/javascript">

    var init = [];

    function search() {

        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/admin/users-survey-price') }}',
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
        loadPiece( '{{ URL::to('/admin/users-survey-price') }}');
    })

</script>
<link rel="stylesheet" type="text/css" href="{{ asset('admin_assets/bower_components/select2/dist/css/select2.min.css') }}"/>
<script type="text/javascript" src="{{ asset('admin_assets/bower_components/select2/dist/js/select2.min.js') }}"></script>

<section class="content-header">
      <h1>All User Survey Type Price </h1>
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
          
            {!! Form::text('surveyor_email',null, ['class' => 'form-control','required'=>'required','placeholder'=>'User Email','onkeypress' => 'error_remove()']) !!}
           </div>
		   
		   
			
           <!-- <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
            <?php $helper=new App\Helpers;?>
            {!! Form::select('port',$helper->PortList(),null, ['class' => 'form-control','required'=>'required','id'=>'port_id','onChange' => 'error_remove()' ]) !!}

           </div> -->
           
		    <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
            <?php $helper=new App\Helpers;?>
            {!! Form::select('srveryor_category',$helper->SurveyTypeList() ,null, ['class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()']) !!}
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
              <h3 class="box-title"> All Users Survey Price List</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body" id="replace-div">
                 @include('admin.users-survey-price.search')
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
</div>    </div>
</div>
<script>
  $(function () {
    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })
    $('#datepicker2').datepicker({
      autoclose: true
    })
	 $('#datepicker3').datepicker({
      autoclose: true
    })
	 $('#datepicker4').datepicker({
      autoclose: true
    })
    
  })
</script>
@stop 