@extends('layouts.adminmaster')
@section('title')
SAG RTA - User Edit
@stop
@section('content') 
<script>
  var app = angular.module('app', []);
app.controller('homeCtrl', function($scope) {
$scope.description = "{{Request::old('description')}}";
});
      </script>
 <script>
	var norep = jQuery.noConflict(); 
  norep(document).ready(function() {
	  norep('#openreplyfrm').click(function(){
	   norep('html,body').animate({
        scrollTop: norep("#reply-frm").offset().top-250},
        'slow');
		  norep('#description').focus();
		  });
	 });
  </script>         
<div class="right-section" ng-app="app" ng-controller="homeCtrl">
<div class="dashboard-page-box">
<div class="row">
<div class="col-md-12 dashboard-head">
        <h2>Edit <span>Profile</span></h2>
        <ul class="breadcrumb">
          <li><a href="{{URL::to('')}}">Home</a></li>
          <li><span>Edit Profile</span></li>
        </ul>
      </div>
</div>
<div class="row">
<div class="col-md-12 edit-form-box">
<div class="card">
             <div class="card-header">
            <div class="form-group">  
             <div class="col-md-8">  
             <h2 class="card-title">{{ucfirst($grievances->title)}}</h2>
               </div>
				 <div class="col-md-4 text-right"><a id="openreplyfrm" class="btn btn-primary" href="javascript:void(0)"><i class="fa fa-reply"></i> Reply</a>
                @if(Session::has('msg')) {!! session('msg') !!} @endif
                </div>
               </div>
              </div>
              <div class="card-body"  ng-app="app" ng-controller="homeCtrl">
              <div class="gre-det">
              <ol class="comment-list">
              @foreach($grievancesrep as $key=>$grievancesrep)
              <li class="commen massage-txt">
              <div class="head-msg">
              <div class="row">
              <div class="col-md-6 user-nm"><i class="fa fa-user"></i> <span>
              @if($grievancesrep->company_name!="")
               {{$grievancesrep->company_name}}
                @else
              {{"SAG RTA Team"}}
                @endif	
              </span></div>
              <div class="col-md-6 text-right user-dt">{{date("d-M-Y H:i",strtotime($grievancesrep->created_at))}}</div>
              </div>
              </div>
              <div class="description-msg">
              <div class="row">
              <div class="col-md-12">
               {{$grievancesrep->description}}
              </div>
              </div>
              </div>
               @if($grievancesrep->attached) <div class="attached-msg">
              <span class="text-attached">Attached File</span>
               <?php $extfile = pathinfo(public_path().$grievancesrep->attached, PATHINFO_EXTENSION); ?>
                @if($extfile=="pdf")
					<span class="file-acc">  <i class="fa fa-file"></i><a target="_blank" href="{{URL::to('public')}}/media/grievance/{{$grievancesrep->attached}}" title="{{$grievancesrep->attached}}">{{$grievancesrep->attached}}</a></span>
                @else
              <span class="file-acc"> <a target="_blank" href="{{URL::to('public')}}/media/grievance/{{$grievancesrep->attached}}" title="{{$grievancesrep->attached}}"> <img src="{{URL::to('public')}}/media/grievance/{{$grievancesrep->attached}}" width="50" class="form-editimg"></a></span>
                 @endif
                  </div>
               @endif
              </li>
               @endforeach
               <li class="commen massage-txt ticket-box">
                <div class="ticket-info">
                <div class="row">
              <div class="col-md-6 ticket-heading"><i class="fa fa-qrcode"></i> {{$grievances->title}}</div>
               <div class="col-md-6 text-right ticket-date">{{date("d-M-Y H:i",strtotime($grievances->created_at))}}</div>
              </div>
                </div>
                <div class="ticket-id">
               <div class="row">
              <div class="col-md-12">
              <div class="ticket-dt"><span class="ticket-b">BO Id</span><p> {{$grievances->bo_id}}</p></div>
              <div class="ticket-dt"><span class="ticket-b">Types</span><p> {{$grievances->types}}</p></div>
              <div class="ticket-dt">
              {{$grievances->description}}</div>
               @if($grievances->attached) <div class="ticket-dt"><span class="ticket-b">Attached File</span>
                @if($ext=="pdf")
                <p><i class="fa fa-file"></i> <a target="_blank" href="{{URL::to('public')}}/media/grievance/{{$grievances->attached}}" title="{{$grievances->attached}}">{{$grievances->attached}}</a></p>
					
                @else
              <p> <a target="_blank" href="{{URL::to('public')}}/media/grievance/{{$grievances->attached}}" title="{{$grievances->attached}}"> <img src="{{URL::to('public')}}/media/grievance/{{$grievances->attached}}" width="50" class="form-editimg"></a></p>
                 @endif
                 </div>
               @endif
              </div>
              </div>
                </div>
              </li>
              <li id="reply-frm" class="commen massage-txt">
              <div class="ticket-info">
              <div class="row">
              <div class="col-md-6 ticket-heading"> <span>
             Reply Form
              </span></div>
              </div>
              </div>
               {!! Form::open(array('url' => 'admin/grievance/reply-post', 'method' => 'post','files'=>true,'name'=>'signupForm','novalidate' => 'novalidate')) !!}
                <div class="form-box-replay">
                <div class="form-group">
                <label class="col-md-2 control-label">Attach File</label>
                <div class="col-md-10">
                {!! Form::hidden('id', $grievances->id) !!}
                {!! Form::file('attached',['class'=>'form-control']) !!}
        @if ($errors->has('attached')) <p class="alert alert-danger">{{ $errors->first('attached') }}</p> @endif
                </div>
                </div>
                <div class="form-group">
                <label class="col-md-12 control-label">Description<span>*</span></label>
                <div class="col-md-12">
                {!! Form::textarea('description', null, ['class' => 'form-control','id' => 'description','placeholder' => 'Description','ng-model'=>'description','required'=>'required']) !!}
        @if ($errors->has('description')) <p class="alert alert-danger">{{ $errors->first('description') }}</p> @endif
               <span ng-show="signupForm.description.$error.required && signupForm.description.$dirty">Description is required</span>
                </div>
               
                </div>

                 <div class="form-group">
                 <div class="col-md-4">
                 {!! Form::submit('Reply',['class' => 'btn btn-signup','ng-disabled' => 'signupForm.$invalid']) !!}
                  <a class="btn btn-cancel" href="{{URL::to('/myaccount/grievance')}}">Cancel</a>
                 </div>
                </div>
                 </div>
                {!! Form::close() !!}
				  </li>
              </ol>
			</div>
              
              </div>
          </div>
</div>
</div>
</div>

</div>

@stop