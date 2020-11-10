@extends('layouts.adminmaster')
@section('title')
@stop
@section('content') 
<script>
  var app = angular.module('app', []);
app.controller('homeCtrl', function($scope) {
$scope.search = '{{$src}}';	
	$scope.rowsize = '{{$grievancedata ->perPage()}}'; 
});
 </script> 
<script>
	function serachChild()
	  {
		var vl = document.getElementById("search-box").value;
		window.location = "{{URL::to('/admin/grievance')}}?q=" + vl;
		return false;  
	  }
	  </script>
<div class="right-section" >
<div class="dashboard-page-box">
<div class="row">
<div class="col-md-12 dashboard-head">
        <h2>Grievance</h2>
        <ul class="breadcrumb">
          <li><a href="{{URL::to('/admin')}}">Home</a></li>
          <li><span>Grievance</span></li>
        </ul>
      </div>
</div>
<div class="row"  ng-app="app" ng-controller="homeCtrl">
<div class="col-md-12">
@if(Session::has('msg')) {!! session('msg') !!} @endif
<fieldset class="dashboard-disp">
<legend>Grievance List</legend>
<div class="form-group">
<div class="col-md-12"><div class="search-frm srch-res"> {!! Form::open(array('url' => '/admin/grievance', 'method' => 'get','name'=>'addpost','files'=>true,'novalidate' => 'novalidate')) !!}
                
                {!!  Form::select('rowsize',$pagedrop_data,null, ['class' => 'form-control wdh30','ng-model'=>'rowsize','required'=>'required'])!!}
                
                
                {!! Form::submit('Submit',['class' => 'ser btn btn-primary','ng-disabled' => 'addpost.$invalid']) !!}
                {!! Form::close() !!} </div><div class="search-frm srch-res"> {!! Form::open(array('url' => '/admin/company-management', 'method' => 'get','name'=>'searchForm','onsubmit'=>'return serachChild();','novalidate' => 'novalidate')) !!}
                
                {!! Form::text('search',$src, ['class' => 'form-control wdh250','placeholder' => 'Search...','id'=>'search-box','ng-model'=>'search','required'=>'required']) !!}
                
                {!! Form::submit('Search',['class' => 'ser btn btn-primary','ng-disabled' => 'searchForm.$invalid']) !!}
                
                {!! Form::close() !!}
                
                @if($src!="")
                <div class="search-text"> <span>Search By : </span> <strong>{{$src}}</strong> <a class="btn btn-danger" href="{{URL::to('/admin/grievance')}}" title="Clear Search"> <i class="fa fa-close" aria-hidden="true"></i> </a> </div>
                @endif </div></div>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
<th width="5%">S.No.</th>
<th width="15%">Company Name</th>
<th width="15%">Title</th>
<th width="25%">Types</th>
<th width="10%">Status</th>
<th width="15%">Created Date</th>
<th width="15%">Action</th>
  </tr>
<?php $i = 1;?>
@foreach ($grievancedata  as $grievance)
<tr>
<td>{{$i}}</td>
<td>{{$grievance->company_name}} </td>
<td>{{$grievance->title}}</td>
<td>{{$grievance->types}} </td>
<td>@if($grievance->status=="0")
       {{"Open"}}
       @elseif($grievance->status=="1")
        {{"Processing"}}
        @else
        {{"Closed"}}
        @endif</td>
<td>{{$grievance->created_at}}</td>
<td><a href="{{URL::to('/admin/grievance/reply')}}/{{base64_encode($grievance->id)}}" title="Reply" class="btn btn-primary"><i class="fa fa-reply" aria-hidden="true"></i></a>
<?php if($grievance->status=="1" || $grievance->status=="0"){?>
<a href="{{URL::to('/admin/grievance/status')}}/{{base64_encode($grievance->id)}}" title="Close" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></a>
<?php }else{?>
<a href="{{URL::to('/admin/grievance/status')}}/{{base64_encode($grievance->id)}}"  title="Open" class="btn btn-danger"><i class="fa fa-close" aria-hidden="true"></i></a>
<?php }?></td>
</tr>
<?php $i++;?>
@endforeach

@if($i<2)
<tr>
<td>No Grievance Data</td>
</tr>
@endif  
</table>


</fieldset>

      
</div>
</div>
  
  <div class="row"> 
    <div class="instructors-table">
      
      <?php $per_page =  $grievancedata ->perPage();

							  $total_page =  ceil($grievancedata ->total()/$per_page);

				 if($total_page>1){?>
      <div class="panel-primary pagging">
        <?php $cuurent_page = $grievancedata ->currentPage();

						      $next_page = $grievancedata ->currentPage()+1;

							  $prev_page = $grievancedata ->currentPage()-1;

							

							  $pageUrl = URL::to('/admin/company-management')."?";

							  if($src!="") {$pageUrl = $pageUrl."q=".$src."&"; }  

							  $strt_at =  ($per_page*($cuurent_page-1))+1;

							  $end_at = ($strt_at+$grievancedata ->count())-1;

if($cuurent_page==1){ $curent_pg_url = "javascript:void();"; $cur_pg_class = "prev btn btn-success btn-xs";}else{$curent_pg_url = $pageUrl."page=".$prev_page."&rowsize=".$per_page; $cur_pg_class = "prev btn btn-info btn-xs";}

if($cuurent_page>=$total_page){ $nxt_pg_url = 'javascript:void();'; $nxt_pg_class = "next btn btn-success btn-xs";}else{$nxt_pg_url = $pageUrl."page=".$next_page."&rowsize=".$per_page; $nxt_pg_class = "next btn btn-info btn-xs";} 





$text_line = "Showing ".$strt_at." to ".$end_at;

						?>
        <div class="row page-count panel-heading">
          <div class="col-sm-4"> <?php echo $text_line; ?> of <?php echo $grievancedata ->total(); ?> entries </div>
          <div class="col-sm-4">
            <ul>
              <?php if($grievancedata ->currentPage()>1){?>
              <li><a href="<?php echo $curent_pg_url;?>" class="<?php echo $cur_pg_class;?>"><i class="fa fa-angle-double-left"></i></a> </li>
              <?php } ?>
              <?php for($i = max($grievancedata ->currentPage()-2, 1); $i <= min(max($grievancedata ->currentPage()-2, 1)+4,$grievancedata ->lastPage()); $i++) {?>
              <li><a <?php if($i==$grievancedata ->currentPage()){echo 'class="btn btn-success btn-xs"'; echo 'href="javascript:void();"'; }else{ $pageu = $pageUrl.'page='.$i."&rowsize=".$per_page; echo "href='$pageu'"; echo 'class="btn btn-info btn-xs"';}?>><?php echo $i; ?></a> </li>
              <?php } ?>
              <?php if($grievancedata ->currentPage()<$total_page){?>
              <li><a href="<?php echo $nxt_pg_url;?>" class="<?php echo $nxt_pg_class;?>"><i class="fa fa-angle-double-right"></i></a> </li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
@stop 