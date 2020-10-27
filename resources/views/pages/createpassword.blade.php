@extends('layouts.master')
@section('title') iMarS | Create Password @stop

@section('content')     
<script src = "{{ asset('/public/assets/js/angular.min.js') }}"></script>
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
<script type="text/javascript">
    $(document).ready(function () 
    {
        $( '#MyChangePasswordForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/create-password-post') }}',
        }).done(function( data ) 
        {  error_remove (); 
			if(data.success==false)
            {
                    $.each(data.errors, function(key, value){
                        $('#'+key).closest('.form-group').addClass('has-error');
                        $('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                    });
                }else
            {
                
                  if(data.class == 'success'){showMsg(data.message, "success");}
                  if(data.class == 'danger'){showMsg(data.message, "danger");}
                  // Simulate a mouse click:
                $.LoadingOverlay("hide");   
              window.location.href = "{{URL::asset('/signin')}}";
            }
             
           

        });

    });
});


</script> 

<div class="login-modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" ng-app="app" ng-controller="homeCtrl">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
		  <h5 class="modal-title"><img src="{{ URL::asset('/public/media') }}/logo-icon.png" alt="">Create New Password</h5>
		  
          @if(Session::has('msg')) {!! session('msg') !!} @endif
        </div>
         <div class="modal-body">
         <div class="login-inner">
                
                @if(Session::has('msg')) {!! session('msg') !!} @endif
               {!! Form::open(array('url' => 'create-password-post', 'method' => 'post','name'=>'signupForm','novalidate' => 'novalidate','id'=>'MyChangePasswordForm')) !!}
               
                
                <div class="form-group">
               
                 {!! Form::hidden('uniqurl',$uniqurl) !!}
                 
                  {!! Form::password('password', ['class' => 'form-control','id' => 'password','placeholder' => 'Password','ng-model' => 'formData.password','ng-minlength' => '6','ng-maxlength' => '16','required' => 'required']) !!}
         @if ($errors->has('password')) <p class="alert alert-danger signuperr">{{ $errors->first('password') }}</p> @endif
        
                
                </div>
                <div class="form-group">
                
                {!! Form::password('password_confirmation', ['class' => 'form-control','id' => 'password_confirmation','placeholder' => 'Confirm Password','ng-model'=>'formData.password_confirmation','valid-password-c'=>'formData.password','required'=>'required']) !!}
           @if ($errors->has('password_confirmation')) <p class="alert alert-danger signuperr">{{ $errors->first('password_confirmation') }}</p> @endif
                </div>
           
                
                 
                 <div class="form-group">
                 <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#" ng-disabled = "signupForm.$invalid"></button>

                 <!-- {!! Form::submit('Save',['class' => 'btn btn-primary','ng-disabled' => 'signupForm.$invalid']) !!} -->
               
                </div>
                {!! Form::close() !!}
                </div>
                </div>
            </div>
        </div>
    </div>

@stop