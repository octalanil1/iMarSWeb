@extends('layouts.adminmaster')
@section('title')
{{$admindata->store_name}} - Change Password
@stop
@section('content') 
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
<div class="right-section" ng-app="app" ng-controller="homeCtrl">
<div class="dashboard-page-box">
<div class="row">
<div class="col-md-12 dashboard-head">
        <h2>Change <span>Password</span></h2>
        <ul class="breadcrumb">
          <li><a href="{{URL::to('/admin')}}">Home</a></li>
          <li><span>Change Password</span></li>
        </ul>
      </div>
</div>
  <div class="row">
<div class="col-md-12 edit-form-box">
<fieldset class="dashboard-disp">
<legend>Change Password</legend>
 @if(Session::has('msg')) {!! session('msg') !!} @endif
{!! Form::open(array('url' => 'admin/change-password-post', 'method' => 'post','name'=>'changePassword','files'=>true,'novalidate' => 'novalidate')) !!}
   <div class="form-group">
          <label class="col-md-2 control-label">New Password</label>
          <div class="col-md-4"> 
          {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Password','ng-model' => 'formData.password','ng-minlength' => '6','ng-maxlength' => '16','required' => 'required']) !!}
        @if ($errors->has('password'))
        <p class="alert alert-danger">{{ $errors->first('password') }}</p>
        @endif <span ng-show="changePassword.password.$error.required && changePassword.password.$dirty">required</span> <span ng-show="!changePassword.password.$error.required && (changePassword.password.$error.minlength || changePassword.password.$error.maxlength) && changePassword.password.$dirty">Passwords must be between 6 and 16 characters.</span>
      
         </div>
        </div>
   <div class="form-group">
          <label class="col-md-2 control-label">Confirm Password</label>
          <div class="col-md-4">
          {!! Form::password('password_confirmation', ['class' => 'form-control','id' => 'password_confirmation','placeholder' => 'Confirm Password','ng-model'=>'formData.password_confirmation','valid-password-c'=>'formData.password','required'=>'required']) !!}
        @if ($errors->has('password_confirmation'))
        <p class="alert alert-danger">{{ $errors->first('password_confirmation') }}</p>
        @endif <span ng-show="changePassword.password_confirmation.$error.required && changePassword.password_confirmation.$dirty">Please confirm your password.</span> <span ng-show="!changePassword.password_confirmation.$error.required && changePassword.password_confirmation.$error.noMatch && changePassword.password.$dirty">Passwords do not match.</span> 
          </div>
        </div>
    <div class="row">
     <div class="col-md-2">&nbsp;</div>
      <div class="col-md-9"> {!! Form::submit('Update',['class' => 'btn btn-signup','ng-disabled' => 'changePassword.$invalid']) !!} <a href="{{URL::to('/admin')}}">Cancel</a></div></div>

 {!! Form::close() !!}    
</fieldset>
</div>
</div>
</div>
</div>
@stop 