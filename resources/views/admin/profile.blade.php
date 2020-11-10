@extends('layouts.adminmaster')
@section('title')
IMARS- Admin Profile
@stop
@section('content') 
<script>
  $(document).ready(function () 
    {
        $( '#editprofileForm' ).on( 'submit', function(e) 
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
                  url: '{{ URL::to('/admin/edit-profile-post') }}',
              }).done(function( data ) 
              {  
                  error_remove (); 
                  if(data.success==false)
                    {
                        $.each(data.errors, function(key, value)
                        {
                            $('#'+key).closest('.form-group').addClass('has-error');
                          $('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                        });

                  }else
                  {
                      $.LoadingOverlay("hide");
                      if(data.class == 'success'){showMsg(data.message, "success");}
                      return false;
                  }
                    
              });

          });
    });
  </script>
<section class="content">
      <div class="row">
      <div class="col-md-12 dashboard-head">
        <h2>Admin <span>Profile</span></h2>
        <ul class="breadcrumb">
          <li><a href="{{URL::to('/admin')}}">Home</a></li>
          <li><span>Profile Management</span></li>
        </ul>
      </div>
</div>

<div class="row">
      <div class="col-md-12">
         @if(Session::has('msg')) {!! session('msg') !!} @endif
      </div>
</div>

        <!-- left column -->
        <div class="col-md-12">
          
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Profile</h3>
            </div> 
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(array('url' => '/admin/edit-profile-post', 'method' => 'post','name'=>'editProfile','files'=>true,'novalidate' => 'novalidate' ,'id'=>'editprofileForm')) !!}
              <div class="box-body">
              <!--  -->
                <div class="form-group">
                  <label for="exampleInputEmail1">First Name</label>
                  {!! Form::text('first_name',$admindata->first_name, ['class' => 'form-control','placeholder' => 'First Name','required'=>'required']) !!}
                  @if ($errors->has('first_name'))
                  <p class="alert alert-danger">{{ $errors->first('first_name') }}</p>
                  @endif
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Last Name</label>
                  {!! Form::text('last_name',$admindata->last_name, ['class' => 'form-control','placeholder' => 'Last Name','required'=>'required']) !!}
                  @if ($errors->has('last_name'))
                  <p class="alert alert-danger">{{ $errors->first('last_name') }}</p>
                  @endif
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Email</label>
                  {!! Form::text('email',$admindata->email, ['class' => 'form-control','placeholder' => 'Email','required'=>'required']) !!}
                  @if ($errors->has('email'))
                  <p class="alert alert-danger">{{ $errors->first('email') }}</p>
                  @endif
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Password</label>
                  {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Passsword']) !!}
                  @if ($errors->has('password'))
                  <p class="alert alert-danger">{{ $errors->first('password') }}</p>
                  @endif
                </div>
                
              <!-- /.box-body -->

              <div class="box-footer">
               {!! Form::submit('Update',['class' => 'btn btn-primary']) !!}
               <a href="{{URL::to('/admin')}}">Cancel</a>
              </div>
              {!! Form::close() !!}
          </div>
         
        </div>
       
      </div>
      <!-- /.row -->
    </section>
@stop 