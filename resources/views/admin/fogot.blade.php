<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>IMARS - Forgot Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link href="{{ asset('/admin_assets/css/style.css') }}" type="text/css" rel="stylesheet" />
<link href="{{ asset('/admin_assets/css/bootstrap.min.css') }}" type="text/css" rel="stylesheet" />


<link rel="shortcut icon" type="image/png" href="{{ URL::asset('resources/assets/images/favicon.ico') }}"/>
<script src="{{ asset('/assets/js/jquery-latest.min.js') }}" type="text/javascript"></script>
<script src = "{{ asset('/assets/js/angular.min.js') }}"></script>
  <script>
  
         var mainApp = angular.module("mainApp", []);
          mainApp.controller('loginController', function($scope) {
          });
		 
      </script>
<style>
  input:disabled {
      opacity: 0.5;
  }
  .all-error{
    width: 100%;
    text-align: left;
    display: inherit;
    position: relative;
    top: -15px;
  }
</style>
</head>
<body class="login-page">
  <div class="container">
    <div class="row">
      <div class="col-sm-8 col-md-6 col-sm-offset-2  col-md-offset-3">
        <div class="logo"><a href="{{ URL::to('/') }}"><img style="width:350px" src="{{ asset('/media/imars-logo.png') }}" /></a></div>
      </div>
    </div>
  </div>
<section>
  <div class="container">
    <div class="row">
      <div class="col-sm-8 col-md-6 col-sm-offset-2  col-md-offset-3" ng-app="mainApp" ng-controller="loginController">
       @if(Session::has('msg')) {!! session('msg') !!} @endif
        <div class="contsct-form">
       
        {!! Form::open(array('url' => '/admin/post-mail', 'method' => 'post','name'=>'loginForm','novalidate' => 'novalidate')) !!}
           {!! Form::email('email', null, ['class' => 'user-fild','id' => 'email','placeholder'=>'Registered Email Address','ng-model'=>'email','length'=>'100','required'=>'required']) !!}
            <span class="all-error" style = "color:red" ng-show = "loginForm.email.$dirty && loginForm.email.$invalid">
                        <span ng-show = "loginForm.email.$error.required">Email is required.</span>
                        <span ng-show = "loginForm.email.$error.email">Invalid email address.</span>
                     </span>
            @if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
           
           {!! Form::submit('Send',['class' => 'login','id' => 'login-id','ng-disabled' => 'loginForm.$invalid']) !!}
                        
          {!! Form::close() !!}
          <p class="forgot_pass"><a href="{{ URL::to('/admin/login') }}">Login</a></p>
          
        </div>
        
      </div>
    </div>
  </div>
  {{Session::forget('msg')}}
</section>
</body>
</html>

