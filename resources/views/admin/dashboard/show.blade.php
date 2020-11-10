@extends('layouts.adminmaster')
@section('title')
IMARS - Dashboard
@stop
@section('content')
<script type="text/javascript">

    var init = [];

    function search() {
      

        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/admin/dashboard') }}',
            data: $('#mySearchForm').serialize(),
            beforeSend: function(){
              $.LoadingOverlay("show");
            },
            success: function(msg){
              
              
                $('#replace-div').html(msg);
                $('.loading-top').fadeOut();
                // $('html,body').animate({scrollTop:$('.page-user').offset().top-0},1400);
               
                $.LoadingOverlay("hide");
                return false;
            }
        });
    }

    function loadPiece( href ) {

        $('body').on('click', 'ul.pagination a', function() {
          
            //var getPage = $(this).attr("href")..split('page=')[1];
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
                  //alert(msg);
                    $('html,body').animate({scrollTop:$('.page-user').offset().top-0},1400);
                    $('#replace-div').html(msg);
                    $.LoadingOverlay("hide");
                    return false;
                }
            });
            return false;
        });
    }

    

    $(document).ready(function() {
        loadPiece( '{{ URL::to('/admin/dashboard') }}');
    })

</script>
<section class="content-header">
      <h1>
        Dashboard
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ URL::to('/admin') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>
 <!-- Main content -->
 <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            {!! Form::open(array('url' => '/admin/search-dashboard', 'method' => 'post','name'=>'mySearchForm','files'=>true,'novalidate' => 'novalidate','id' => 'mySearchForm')) !!}
                    <!--<div class="box-header">
                        <h3 class="box-title">Search</h3>
                    </div>-->
                    <div class="box-body">
                        <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <label></label>
                           
                            {!! Form::select('filter_type',["weak"=>"Week","month"=>"Month","year"=>"Year"],null, ['class' => 'form-control','required'=>'required','id'=>'','onkeypress' => 'error_remove()']) !!}
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <label></label>
                            <input style="margin-top: 20px;" id="search" class="btn btn-outline-primary pull-left" type="submit" value="Search">
                            <button style="margin-top: 20px;margin-left: 10px;" onclick="resetSearchForm();" class="btn btn-outline-success pull-left" type="submit">Reset</button>

                        </div>
                        </div>
                    </div><!-- /.box-body -->
                    {!! Form::close() !!} 
            </div>
        </div>
 <section class="content">
      <!-- Small boxes (Stat box) -->
        <div class="box-body" id="replace-div">
                    @include('admin.dashboard.search')
        </div>
    

    </section>

    <!-- /.content -->

@stop 