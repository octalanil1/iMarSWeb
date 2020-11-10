<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>IMARS - Admin Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link href="{{asset('admin_assets/css/style.css') }}" type="text/css" rel="stylesheet" />
<link href="{{asset('admin_assets/css/bootstrap.min.css') }}" type="text/css" rel="stylesheet" />

<link rel="shortcut icon" type="image/png" href="{{asset('assets/images/favicon.ico') }}"/>
<script src="{{asset('assets/js/jquery-latest.min.js') }}" type="text/javascript"></script>
<script src = "{{asset('assets/js/angular.min.js') }}"></script>
  <script>
  
         var mainApp = angular.module("mainApp", []);
          mainApp.controller('loginController', function($scope) {
            $scope.email = "<?php if(!empty( $_COOKIE["email"]) ){ echo  $_COOKIE["email"];}  ?>";
            $scope.password = "<?php if(!empty( $_COOKIE["password"]) ){ echo  $_COOKIE["password"];}  ?>";

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
        <div class="logo"><a href="{{ URL::to('/admin') }}"><img style="width:350px" src="{{asset('/media/imars-logo.png') }}" /></a></div>
      </div> 
    </div>
  </div>
<section>
  <div class="container">
    <div class="row">
      <div class="col-sm-8 col-md-6 col-sm-offset-2  col-md-offset-3" ng-app="mainApp" ng-controller="loginController">
     
       @if(Session::has('msg')) {!! session('msg') !!} @endif
      
        <div class="contsct-form">
       
        {!! Form::open(array('url' => 'admin/loginpost', 'method' => 'post','name'=>'loginForm','novalidate' => 'novalidate')) !!}
        <input type="email" value='<?php if(!empty( $_COOKIE["email"]) ){ echo  $_COOKIE["email"];}  ?>'  name="email" class="user-fild" id="email" placeholder="Email" ng-model='email' required="required"/>
            <span  class="all-error"  style = "color:red" ng-show = "loginForm.email.$dirty && loginForm.email.$invalid">
                        <span ng-show = "loginForm.email.$error.required">Email is required.</span>
                        <span ng-show = "loginForm.email.$error.email">Invalid email address.</span>
                     </span>
                      @if ($errors->has('email')) <p class="alert alert-danger">{{ $errors->first('email') }}</p> @endif
            
                      <input type="password" value='<?php if(!empty( $_COOKIE["password"]) ){ echo  $_COOKIE["password"];}  ?>' name="password" class="password-fild" id="password"  placeholder="Password" required="required" ng-model='password'/>
           <span  class="all-error"  style = "color:red" ng-show = "loginForm.password.$dirty && loginForm.password.$invalid">
                        <span ng-show = "loginForm.password.$error.required">Password is required.</span>
                        <span ng-show = "loginForm.password.$error.maxlength">Password max lenth is 16.</span>
                        <span ng-show = "loginForm.password.$error.minlength">Password min lenth is 6.</span>
                     </span>
                   
          
          {!! NoCaptcha::renderJs() !!}
          {!! NoCaptcha::display() !!}
          <span class="all-error" style="color:red">
              <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
        </span>     
           {!! Form::submit('login',['class' => 'login','id' => 'login-id','ng-disabled' => 'loginForm.$invalid']) !!}
           <div class="form-group">
            <label for="remember"><input type="checkbox" name="remember" id="remember" {{!empty($_COOKIE['email']) ? 'checked' : '' }}  >Remember Me</label>          
        </div>          {!! Form::close() !!}
          
          <p class="forgot_pass"><a href="{{ URL::to('/admin/forgot-password') }}">Forgotten Password ?</a></p>
          
        </div>
       
      </div>
    </div>
  </div>
  <style>
    .g-recaptcha{
      margin: 0 0 18px;
    }
    
    </style>
  {{Session::forget('msg')}}
</section>
</body>
</html>

