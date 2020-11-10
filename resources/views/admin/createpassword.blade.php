<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>IMARS - Create Pasword</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link href="{{ asset('/admin_assets/css/style.css') }}" type="text/css" rel="stylesheet" />
<link href="{{ asset('/admin_assets/css/bootstrap.min.css') }}" type="text/css" rel="stylesheet" />


<link rel="shortcut icon" type="image/png" href="{{ asset('/assets/images/favicon.ico') }}"/>
<script src="{{ asset('/assets/js/jquery-latest.min.js') }}" type="text/javascript"></script>
<script src = "{{ asset('/assets/js/angular.min.js') }}"></script>
  <script>
  
      var app = angular.module('app', []);
app.directive('validPasswordC', function() {
  return {
    require: 'ngModel',
    scope: {

      reference: '=validPasswordC'

    },
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$parsers.unshift(function(viewValue, $scope) {

        var noMatch = viewValue != scope.reference
        ctrl.$setValidity('noMatch', !noMatch);
        return (noMatch)?noMatch:!noMatch;
      });

      scope.$watch("reference", function(value) {;
        ctrl.$setValidity('noMatch', value === ctrl.$viewValue);

      });
    }
  }
});
app.controller('homeCtrl', function($scope) {
});
		 
      </script>
<style>
  .all-error {
    width: 100%;
    text-align: left;
    display: inherit;
    position: relative;
    top: -15px;
    color:red;
}
  </style>
</head>
<body class="login-page">
  <div class="container">
    <div class="row">
      <div class="col-sm-8 col-md-6 col-sm-offset-2  col-md-offset-3">
        <div class="logo"><a href="{{ URL::to('/') }}"><img style="width:350px" src="{{asset('/media/imars-logo.png') }}" /></a></div>
      </div>
    </div>
  </div>
<section>
  <div class="container" ng-app="app" ng-controller="homeCtrl">
    <div class="row">
      <div class="col-sm-8 col-md-6 col-sm-offset-2  col-md-offset-3" >
       @if(Session::has('msg')) {!! session('msg') !!} @endif
        <div class="contsct-form">
       
        {!! Form::open(array('url' => '/admin/password-save', 'method' => 'post','name'=>'changePassword','novalidate' => 'novalidate')) !!}
         
         {!! Form::hidden('uniqurl',$uniqurl) !!}
         
           {!! Form::password('password', ['class' => 'password-fild','placeholder' => 'Password','ng-model' => 'formData.password','ng-minlength' => '8','ng-maxlength' => '16','ng-pattern' => '/(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z])/','required' => 'required']) !!}
         @if ($errors->has('password')) <p class="alert alert-danger">{{ $errors->first('password') }}</p> @endif
          <span class="all-error" ng-show="changePassword.password.$error.required && changePassword.password.$dirty">Password field is required</span>
   <span class="all-error" ng-show="!changePassword.password.$error.required && (changePassword.password.$error.minlength || changePassword.password.$error.maxlength) && changePassword.password.$dirty">Passwords must be between 8 and 16 characters.</span>
   <span class="all-error" ng-show="!changePassword.password.$error.required && !changePassword.password.$error.minlength && !changePassword.password.$error.maxlength && changePassword.password.$error.pattern && changePassword.password.$dirty">Must contain one lower &amp; uppercase letter, and one non-alpha character (a number or a symbol.)</span>
           
          {!! Form::password('password_confirmation', ['class' => 'password-fild','id' => 'password_confirmation','placeholder' => 'Confirm Password','ng-model'=>'formData.password_confirmation','valid-password-c'=>'formData.password','required'=>'required']) !!}
           @if ($errors->has('password_confirmation')) <p class="alert alert-danger">{{ $errors->first('password_confirmation') }}</p> @endif
            <span class="all-error" ng-show="changePassword.password_confirmation.$error.required && changePassword.password_confirmation.$dirty">Please confirm your password.</span>
   <span class="all-error" ng-show="!changePassword.password_confirmation.$error.required && changePassword.password_confirmation.$error.noMatch && changePassword.password.$dirty">Passwords do not match.</span>
           {!! Form::submit('Save',['class' => 'login','id' => 'login-id','ng-disabled' => 'changePassword.$invalid']) !!}
           
          {!! Form::close() !!}
          
          
        </div>
       
      </div>
    </div>
  </div>
  {{Session::forget('msg')}}
</section>
</body>
</html>

