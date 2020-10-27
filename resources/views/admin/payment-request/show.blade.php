@extends('layouts.adminmaster')
@section('title')
IMARS | Dispute Request Manager
@stop
@section('content') 
<style>
  #replace-div{overflow: scroll;}

 
  </style>
<script type="text/javascript">

    var init = [];

    function search() {

        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/admin/payment-request') }}',
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

    function edit_record(edit_id) {

$.LoadingOverlay("show");
$('#UserModal').html(''); $(".form-title").text('Edit Payment Request');
$('#UserModal').load('{{ URL::to('/admin/edit-payment-request') }}'+'/'+edit_id);
$("#myModal").modal();
}

    function view_record(view_id) {

        //$.LoadingOverlay("show");
        $('#UserModal').html(''); $(".form-title").text('View Payment Request');
        $('#UserModal').load('{{ URL::to('/admin/view-payment-request') }}'+'/'+view_id);
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
                    //$.LoadingOverlay("show");
                },
                data: ($('#mySearchForm').serialize()),
                success: function(msg){
                    $('html,body').animate({scrollTop:$('.page-user').offset().top-0},1400);
                    $('#replace-div').html(msg);
                    //$.LoadingOverlay("hide");
                    return false;
                }
            });
            return false;
        });
    }

    function statusChange(id,action) 
    {
      $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
      $.ajax({
            dataType: 'json',
            data: { id:id,action:action}, 
            type: "POST",
            url: '{{ URL::to('/admin/payment-request-action') }}',
        }).done(function( data ) 
        {   
          search();
            $("#msg-data").html(data.message);
          
          
        });
        
    }

    $(document).ready(function() {


        loadPiece( '{{ URL::to('/admin/payment-request') }}');
    })

</script>
<section class="content-header">
      <h1>
      Payment  Request
   
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ URL::to('/admin') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Payment  Request</li>
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
          
            {!! Form::text('surveyor_email',null, ['class' => 'form-control','required'=>'required','placeholder'=>'Surveyor Email','onkeypress' => 'error_remove()']) !!}
           </div>
           <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            <?php $helper=new App\Helpers;?>
            {!! Form::select('country_id',$helper->CountryList(),null, ['class' => 'form-control','required'=>'required','id'=>'country_id','onchange'=>'Getcountrycode();']) !!}
           </div>
           <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            <?php $helper=new App\Helpers;?>
            {!! Form::select('payment_method',[''=>'Select Payment Method','ach'=>'Ach' ,'wire'=>'Wire','paypal'=>'Paypal'],null, ['class' => 'form-control','required'=>'required','id'=>'country_id']) !!}
           </div>
           <div class="col-xs-12 col-sm-6 col-md-2">
            <label></label>
          
            {!! Form::select('status',[''=>'Select Status','paid'=>'Paid' ,'unpaid'=>'Unpaid'],null, ['class' => 'form-control','required'=>'required']) !!}
           </div>
           <div class="col-xs-12 col-sm-6 col-md-6">
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
              <h3 class="box-title">Payment  Request Table</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body" id="replace-div">
                 @include('admin.payment-request.search')
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
    
  })
</script>
@stop 