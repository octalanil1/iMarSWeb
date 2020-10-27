@extends('layouts.adminmaster')
@section('title')
IMARS | Port Manager
@stop
@section('content') 
<script type="text/javascript">

    var init = [];

    function search() {

        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/admin/port') }}',
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
        $('#UserModal').html(''); $(".form-title").text('Add Port');
        $('#UserModal').load('{{ URL::to('/admin/add-port') }}');
        $("#myModal").modal();
    }

    function edit_record(edit_id) {

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('Edit Port');
        $('#UserModal').load('{{ URL::to('/admin/edit-port') }}'+'/'+edit_id);
        $("#myModal").modal();
    }

    function view_record(view_id) {

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('View User');
        $('#UserModal').load('{{ URL::to('/admin/view-user') }}'+'/'+view_id);
        $("#myModal").modal();
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

    function remove_record(id) 
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
            url: '{{ URL::to('/admin/port-remove') }}',
        }).done(function( data ) 
        {   
          search();
          if(data.class == 'success'){showMsg(data.message, "success");}

          
          
        });
        
    }
    function ImportPort() 
    {

        $.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('Select XLS File to import');
        $('#UserModal').load('{{ URL::to('/admin/import-port') }}');
        $("#myModal").modal();

    }
    $(document).ready(function() {

      $('#port_id').select2();

        loadPiece( '{{ URL::to('/admin/port') }}');
    })

</script>
<link rel="stylesheet" type="text/css" href="{{ asset('admin_assets/bower_components/select2/dist/css/select2.min.css') }}"/>
<script type="text/javascript" src="{{ asset('admin_assets/bower_components/select2/dist/js/select2.min.js') }}"></script>

<section class="content-header"><h1> Port<small>List</small></h1>
      <ol class="breadcrumb">
        <li><a href="{{ URL::to('/admin') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Port</li>
      </ol>
</section>
<section class="" style="padding: 15px;">
      <div class="row">
        <div class="col-xs-12 page-user">
          <div class="box">
            <div class="box-body">
            {!! Form::open(array('url' => '/admin/port', 'method' => 'post','name'=>'mySearchForm','files'=>true,'id' => 'mySearchForm')) !!}
            
            <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
            <?php $helper=new App\Helpers;?>
                      {!! Form::select('country_id',$helper->CountryList(),null, ['class' => 'form-control','onkeypress' => 'error_remove()']) !!}
            </div>
            <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            {!! Form::text('port',null, ['class' => 'form-control','placeholder'=>'Port Name','onkeypress' => 'error_remove()']) !!}
           </div>

           <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
            <?php $helper=new App\Helpers;?>
                      {!! Form::select('num_rows',[''=>'Select No of Rows','all'=>'All','20'=>'20','50'=>'50','100'=>'100','200'=>'200'],null, ['class' => 'form-control','onkeypress' => 'error_remove()']) !!}
            </div>
            
            <div class="col-xs-12 col-sm-6 col-md-6">
                <label></label>
                <input style="margin-top: 20px;" id="search" class="btn btn-outline-primary pull-left" type="submit" value="Search">
                <button style="margin-top: 20px;margin-left: 10px;" onclick="resetSearchForm();" class="btn btn-outline-success pull-left" type="submit">Reset</button>
                <button type="button" style="margin-top: 20px;margin-left: 10px;" onclick="ImportPort();" class="btn btn-outline-primary pull-left"><i class="fa fa-file-excel-o"></i> Import XLS</button>
                <a href="javascript:" style="margin-top: 20px;" class="custom-btn pull-right btn btn-outline-primary btn-labeled" onclick="add_record();" title="Add Port"><span class="btn-label icon fa fa-plus"></span> Add Port</a>
              </div>
            {!! Form::close() !!}
           
            

            </div>
        </div>
        </div>
        </div>
      </section>

<section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Port Table</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body" id="replace-div">
                 @include('admin.port.search')
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
    $('#datepicker3').datepicker({
      autoclose: true
    })
    $('#datepicker4').datepicker({
      autoclose: true
    })
    
  })
</script>
@stop 