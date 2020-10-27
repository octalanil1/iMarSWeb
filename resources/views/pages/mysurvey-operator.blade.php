

<script> 

$(document).ready(function () 
    {
 		$( '#mySearchForm' ).on( 'submit', function(e) 
        {
			
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
            
			  $.ajax({
				type: 'POST',
				url: '{{ URL::to('/mysurvey') }}',
				data: $('#mySearchForm').serialize(),
				beforeSend: function(){
					$.LoadingOverlay("show");
				},
				success: function(msg)
				{
					//	$('html,body').animate({scrollTop:$('.page-user').offset().top-0},1400);
						$('#replace-div').html(msg);
						//alert();
						$.LoadingOverlay("hide");
						return false;
				}
			});

    });
});
 
function view_record(view_id) {

$.LoadingOverlay("show");
$('#UserModal').html(''); $(".form-title").text('Survey Detail');
$('#UserModal').load('{{ URL::to('/survey-detail') }}'+'/'+view_id);
$("#myModal").modal();
}
function view_chat(survey_id,sender_id,receiver_id) {

$.LoadingOverlay("show");
$('#UserModal1').html(''); $(".form-title").text('Chat');
$('#UserModal1').load('{{ URL::to('/chat-form') }}'+'/'+survey_id+'/'+sender_id+'/'+receiver_id);
$("#myModal1").modal();
}
function Rating(operator_id,surveyor_id) {

$.LoadingOverlay("show");
$('#RatingModal').html(''); $(".form-title").text('Add Rating');
$('#RatingModal').load('{{ URL::to('/add-rating') }}'+'/'+operator_id+'/'+surveyor_id);
$("#ratingModal").modal();
}
$(document).ready(function() {


loadPiece2( '{{ URL::to('/mysurvey') }}');
})
function loadPiece2( href ) {

$('body').on('click', 'ul.pagination a', function() {
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
			//$('html,body').animate({scrollTop:$('.replace-div').offset().top-0},1400);
			$('#replace-div').html(msg);
			$.LoadingOverlay("hide");
			return false;
		}
	});
	return false;
});
}
</script> 
<style>
	.col-md-2.text-center.searchbtn .btn.btn-primary {
		margin-top: 0px;
	}
	#upcomming_filter .form-control, #past_filter .form-control{
		border-radius: 0px !important;
		height: initial !important;
		padding: 7px 15px;
	}
	#upcomming_filter .form-submit, #past_filter .form-submit {
		float: right;
		width:100%;
	}
	#upcomming_filter .form-submit .btn.btn-primary,
	#past_filter .form-submit .btn.btn-primary {
		border-radius: 0px;
		padding: 5px;
		float: right;
	}
</style>
<section class="page">
	<div class="row">
		<div class="col-md-12 col-lg-12 col-xl-12">
			<div class="surveyors">
			<div class="right-flex-box">
				<h4>Surveys</h4>
			</div>
			<?php 
						$helper=new App\Helpers;  
						$user = Auth::user();
						$usertavle=new App\User;	
						if($user->type=='1')
						{
							$createdbysurveyor =  $usertavle->select('created_by')->where('id',$user->id)->first();
									$ids=array();
									$createdbydpsurveyor = $usertavle->select('id')->where('created_by',$createdbysurveyor->created_by)->get();
									if(!empty($createdbydpsurveyor)){
										
										foreach($createdbydpsurveyor as $data){
											$ids[]=$data->id;
										}
									}
								array_push($ids,$createdbysurveyor->created_by);
								//dd($ids);
							$survey_rec = $usertavle->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
							,'users.id')->where('users.first_name','!=',"")->whereIn('users.id',$ids)->get();
						}else
						{
							$survey_rec = $usertavle->select(DB::raw('CONCAT(users.first_name, "  ", users.last_name) as username')
							,'users.id')->where('users.created_by',$user->id)->get();
						}
						
						if(count($survey_rec)!=0)
						{
							$surveyorList=array();
							$operatoruser_box=array(''=>'Select Operator');
							$operatoruser_box[$user->id]=$user->first_name.' '.$user->last_name;
							///dd($survey_rec);
							foreach($survey_rec as $data)
							{
								$Survey=new App\Models\Survey;
								if($user->type=='0'  || $user->type=='1')
								{
									$ip=$data->id;
									$osurveys=$Survey->select('survey.*')
									->where('assign_to_op',$ip );
									$osurveys=$osurveys->get();
									if(count($osurveys)>0){
									$operatoruser_box[$data->id]=$data->username;}
								}
								
							}
						}
						$operatorList=$helper->OperatorList();
					?>
			@if(($user->type=='0'  || $user->type=='1') && count($survey_rec)!=0)
				<div class="login-inner">
				<div class="row">
				<div class="col-md-12">
					{!! Form::open(array('url' => '/mysurvey', 'method' => 'post','name'=>'mySearchForm','files'=>true,'novalidate' => 'novalidate','id' => 'mySearchForm')) !!}
					
					
					
					<div id="upcomming_filter">
						<div class="row">
								<div class="col-md-3">
										{!! Form::select('operator_id',$operatoruser_box,$operator_id, ['class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()']) !!}
								</div>
								<div class="col-md-6">
									{!! Form::text('search',$search, ['placeholder' => 'By Ports,Vessels Name ,Survey Number','class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()']) !!}
								</div>
								<div class="col-md-2 text-center searchbtn">
									<div class="form-submit">
										<button type="submit" class="btn btn-primary">Search</button>
									</div>
								</div>
						</div>
					</div>
				{!! Form::close() !!}
				</div>
				</div>
				</div>
				@endif
				<div class="surveys-list">
                            <div class="list-group" id="myList" role="tablist">
                                <a class="list-group-item list-group-item-action active" data-toggle="list" href="#active" role="tab">Active ({{$pending_survey_data->count()+$upcoming_survey_data->count()+$report_submit_survey_data->count()}})  </a>
                                <a class="list-group-item list-group-item-action" data-toggle="list" href="#past" role="tab">Past ({{$paid_survey_data->count()+$unpaid_survey_data->count()}})</a>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="active" role="tabpanel">
                                    <div class="list-group" id="myListInner" role="tablist">
                                        <a class="list-group-item list-group-item-action active" data-toggle="list" href="#pendingInner" role="tab">Pending ({{$pending_survey_data->count()}})</a>
                                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#upcomingInner" role="tab">Upcoming ({{$upcoming_survey_data->count()}})</a>
                                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#reportInner" role="tab">Report Submitted ({{$report_submit_survey_data->count()}})</a>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="pendingInner" role="tabpanel">
											<ul class="upcomeing-past-list">
												<?php if(count($pending_survey_data)!=0 ) 
												{?>

													@foreach($pending_survey_data as $data)  
													<li>
														<span class="past-img">
															<img src="{{ URL::asset('/public/media') }}/ship.png" alt="#">
														</span>
														<span class="ship-info">
															<h3>{{$data->surveytype_name}}  </h3>
															<h3>{{$data->vesselsname}}  </h3>
															<p><span class="location_icon"><i class="fas fa-map-marker-alt"></i></span>{{$data->port_name}}</p>
															<p><span class="time_icon"><i class="far fa-clock"></i></span>{{date("d M Y",strtotime($data->start_date))}}- {{date("d M Y",strtotime($data->end_date))}}</p>
															<?php 	$helper=new App\Helpers;
																	$user = Auth::user();
																	$SurveyUsers =new App\Models\SurveyUsers;
																	$Surveyusersc= $SurveyUsers->where('survey_id',$data->id)->count();
															?>
															<a href="#" class="paid pending"> {{$helper->GetSurveyStatusBykey($data->status)}}</a>
																<?php if($data->status=='1' || $data->status=='3')
																{
																
																		if($data->assign_to!=""){$surveyor_id=$data->assign_to;}else{$surveyor_id=$data->accept_by;}
																		if($data->assign_to_op!=""){$operator_id=$data->assign_to_op;}else{$operator_id=$data->user_id;}
																		
																		if($operator_id==$user->id){$sender_id=$operator_id;$receiver_id=$surveyor_id;}else{$sender_id=$surveyor_id;$receiver_id=$operator_id;}
																	
																	?>
															
																	
														<?php } ?>
															
														</span>
														<span class="right-header">
															<!-- <a href="javaScript:Void(0);" class="active" onclick="shipfavourite('{{base64_encode($data->id)}}');"><i class="fas fa-heart" aria-hidden="true"></i> </a>     -->
															<a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn" data-original-title="View User" onclick="view_record('{{$data->id}}')"><i class="fa fa-eye" aria-hidden="true"></i></a>   
														</span>                           
													</li>
													
													@endforeach
														
														<?php }else{ ?>
													
																		<li>
																			<span class="ship-info">
																				<h3 class="text-center">No pending surveys…</h3>
																			</span>
																		</li>
															<?php  } ?>
												</ul>
                                        </div>
                                        <div class="tab-pane" id="upcomingInner" role="tabpanel">
											<ul class="upcomeing-past-list">
													<?php if(count($upcoming_survey_data)!=0 ) 
													{?>

														@foreach($upcoming_survey_data as $data)  
														<li>
															<span class="past-img">
																<img src="{{ URL::asset('/public/media') }}/ship.png" alt="#">
															</span>
															<span class="ship-info">
																<h3>{{$data->surveytype_name}}  </h3>
																<h3>{{$data->vesselsname}}  </h3>
																<p><span class="location_icon"><i class="fas fa-map-marker-alt"></i></span>{{$data->port_name}}</p>
																<p><span class="time_icon"><i class="far fa-clock"></i></span>{{date("d M Y",strtotime($data->start_date))}}- {{date("d M Y",strtotime($data->end_date))}}</p>
																<?php 	$helper=new App\Helpers;
																		$user = Auth::user();
																		$SurveyUsers =new App\Models\SurveyUsers;
																		$Surveyusersc= $SurveyUsers->where('survey_id',$data->id)->count();
																?>
																<a href="#" class="paid pending"> {{$helper->GetSurveyStatusBykey($data->status)}}</a>
																	<?php if($data->status=='1' || $data->status=='3')
																	{
																	
																			if($data->assign_to!=""){$surveyor_id=$data->assign_to;}else{$surveyor_id=$data->accept_by;}
																			if($data->assign_to_op!=""){$operator_id=$data->assign_to_op;}else{$operator_id=$data->user_id;}
																			
																			if($operator_id==$user->id){$sender_id=$operator_id;$receiver_id=$surveyor_id;}else{$sender_id=$surveyor_id;$receiver_id=$operator_id;}
																		?>
																
																		<a href="#" id="viewchat_{{$data->id}}"  onclick="view_chat('{{$data->id}}','{{$sender_id}}','{{$receiver_id}}')" class="massage_send">
																		
																		<img src="{{ URL::asset('/public/media') }}/massage_icon.png" alt="">
																	
																		</a>
															<?php } ?>
																
															</span>
															<span class="right-header">
																<!-- <a href="javaScript:Void(0);" class="active" onclick="shipfavourite('{{base64_encode($data->id)}}');"><i class="fas fa-heart" aria-hidden="true"></i> </a>     -->
																<a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn" data-original-title="View User" onclick="view_record('{{$data->id}}')"><i class="fa fa-eye" aria-hidden="true"></i></a>   
															</span>                           
														</li>
														
														@endforeach
														
															<?php }else{ ?>
														
																			<li>
																				<span class="ship-info">
																					<h3 class="text-center">No upcoming surveys… </h3>
																				</span>
																			</li>
																<?php  } ?>
												</ul>
                                        </div>
                                        <div class="tab-pane" id="reportInner" role="tabpanel">
										<ul class="upcomeing-past-list">
										<?php if(count($report_submit_survey_data)!=0 ) 
										{?>

											@foreach($report_submit_survey_data as $data)  
											<li>
												<span class="past-img">
													<img src="{{ URL::asset('/public/media') }}/ship.png" alt="#">
												</span>
												<span class="ship-info">
													<h3>{{$data->surveytype_name}}  </h3>
													<h3>{{$data->vesselsname}}  </h3>
													<p><span class="location_icon"><i class="fas fa-map-marker-alt"></i></span>{{$data->port_name}}</p>
													<p><span class="time_icon"><i class="far fa-clock"></i></span>{{date("d M Y",strtotime($data->start_date))}}- {{date("d M Y",strtotime($data->end_date))}}</p>
													<?php 	$helper=new App\Helpers;
															$user = Auth::user();
															$SurveyUsers =new App\Models\SurveyUsers;
															$Surveyusersc= $SurveyUsers->where('survey_id',$data->id)->count();
													?>
													<a href="#" class="paid pending"> {{$helper->GetSurveyStatusBykey($data->status)}}</a>
														<?php if($data->status=='1' || $data->status=='3')
														{
														
																if($data->assign_to!=""){$surveyor_id=$data->assign_to;}else{$surveyor_id=$data->accept_by;}
																if($data->assign_to_op!=""){$operator_id=$data->assign_to_op;}else{$operator_id=$data->user_id;}
																
																if($operator_id==$user->id){$sender_id=$operator_id;$receiver_id=$surveyor_id;}else{$sender_id=$surveyor_id;$receiver_id=$operator_id;}
															?>
													
															<a href="#" id="viewchat_{{$data->id}}"  onclick="view_chat('{{$data->id}}','{{$sender_id}}','{{$receiver_id}}')" class="massage_send">
															<img src="{{ URL::asset('/public/media') }}/massage_icon.png" alt="">
															
															</a>
												<?php } ?>
													
												</span>
												<span class="right-header">
													<!-- <a href="javaScript:Void(0);" class="active" onclick="shipfavourite('{{base64_encode($data->id)}}');"><i class="fas fa-heart" aria-hidden="true"></i> </a>     -->
													<a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn" data-original-title="View User" onclick="view_record('{{$data->id}}')"><i class="fa fa-eye" aria-hidden="true"></i></a>   
												</span>                           
											</li>
											
											@endforeach
												
												<?php }else{ ?>
											
																<li>
																	<span class="ship-info">
																		<h3 class="text-center">No survey reports newly submitted…</h3>
																	</span>
																</li>
													<?php  } ?>
												</ul>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane" id="past" role="tabpanel">
                                    <div class="list-group" id="myListpast" role="tablist">
                                        <a class="list-group-item list-group-item-action active" data-toggle="list" href="#unpaidpast" role="tab">Unpaid ({{$unpaid_survey_data->count()}})</a>
                                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#paidpast" role="tab">Paid ({{$paid_survey_data->count()}})</a>
                                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#cancelledpast" role="tab">Cancelled ({{$cancelled_survey_data->count()}})</a>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="unpaidpast" role="tabpanel">
										<ul class="upcomeing-past-list">
												<?php if(count($unpaid_survey_data)!=0 ) 
												{?>

													@foreach($unpaid_survey_data as $data)  
													<li>
														<span class="past-img">
															<img src="{{ URL::asset('/public/media') }}/ship.png" alt="#">
														</span>
														<span class="ship-info">
															<h3>{{$data->surveytype_name}}  </h3>
															<h3>{{$data->vesselsname}}  </h3>
															<p><span class="location_icon"><i class="fas fa-map-marker-alt"></i></span>{{$data->port_name}}</p>
															<p><span class="time_icon"><i class="far fa-clock"></i></span>{{date("d M Y",strtotime($data->start_date))}}- {{date("d M Y",strtotime($data->end_date))}}</p>
															<?php 	$helper=new App\Helpers;
																	$user = Auth::user();
																	$SurveyUsers =new App\Models\SurveyUsers;
																	$Surveyusersc= $SurveyUsers->where('survey_id',$data->id)->count();
															?>
															<a href="#" class="paid pending"> 

														Unpaid
															</a>
																<?php if($data->status=='1' || $data->status=='3')
																{
																
																		if($data->assign_to!=""){$surveyor_id=$data->assign_to;}else{$surveyor_id=$data->accept_by;}
																		if($data->assign_to_op!=""){$operator_id=$data->assign_to_op;}else{$operator_id=$data->user_id;}
																		
																		if($operator_id==$user->id){$sender_id=$operator_id;$receiver_id=$surveyor_id;}else{$sender_id=$surveyor_id;$receiver_id=$operator_id;}
																		
																	?>
															
																
														<?php } ?>
															
														</span>
														<span class="right-header">
															<!-- <a href="javaScript:Void(0);" class="active" onclick="shipfavourite('{{base64_encode($data->id)}}');"><i class="fas fa-heart" aria-hidden="true"></i> </a>     -->
															<a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn" data-original-title="View User" onclick="view_record('{{$data->id}}')"><i class="fa fa-eye" aria-hidden="true"></i></a>   
														</span>                           
													</li>
													
													@endforeach
														
														<?php }else{ ?>
													
																		<li>
																			<span class="ship-info">
																				<h3 class="text-center">No completed surveys awaiting payment…</h3>
																			</span>
																		</li>
															<?php  } ?>
												</ul>
                                        </div>
                                        <div class="tab-pane" id="paidpast" role="tabpanel">
										<ul class="upcomeing-past-list">
												<?php if(count($paid_survey_data)!=0 ) 
												{?>

													@foreach($paid_survey_data as $data)  
													<li>
														<span class="past-img">
															<img src="{{ URL::asset('/public/media') }}/ship.png" alt="#">
														</span>
														<span class="ship-info">
															<h3>{{$data->surveytype_name}}  </h3>
															<h3>{{$data->vesselsname}}  </h3>
															<p><span class="location_icon"><i class="fas fa-map-marker-alt"></i></span>{{$data->port_name}}</p>
															<p><span class="time_icon"><i class="far fa-clock"></i></span>{{date("d M Y",strtotime($data->start_date))}}- {{date("d M Y",strtotime($data->end_date))}}</p>
															<?php 	$helper=new App\Helpers;
																	$user = Auth::user();
																	$SurveyUsers =new App\Models\SurveyUsers;
																	$Surveyusersc= $SurveyUsers->where('survey_id',$data->id)->count();
															?>
															<a href="#" class="paid pending"> Paid</a>
																<?php if($data->status=='1' || $data->status=='3')
																{
																
																		if($data->assign_to!=""){$surveyor_id=$data->assign_to;}else{$surveyor_id=$data->accept_by;}
																		if($data->assign_to_op!=""){$operator_id=$data->assign_to_op;}else{$operator_id=$data->user_id;}
																		
																		if($operator_id==$user->id){$sender_id=$operator_id;$receiver_id=$surveyor_id;}else{$sender_id=$surveyor_id;$receiver_id=$operator_id;}
																	?>
															
																	
														<?php } ?>
															
														</span>
														<span class="right-header">
															<!-- <a href="javaScript:Void(0);" class="active" onclick="shipfavourite('{{base64_encode($data->id)}}');"><i class="fas fa-heart" aria-hidden="true"></i> </a>     -->
															<a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn" data-original-title="View User" onclick="view_record('{{$data->id}}')"><i class="fa fa-eye" aria-hidden="true"></i></a>   
														</span>                           
													</li>
													
													@endforeach
														
														<?php }else{ ?>
													
																		<li>
																			<span class="ship-info">
																				<h3 class="text-center">No past paid surveys…</h3>
																			</span>
																		</li>
															<?php  } ?>
												</ul>
                                        </div>
                                        <div class="tab-pane" id="cancelledpast" role="tabpanel">
										<ul class="upcomeing-past-list">
													<?php if(count($cancelled_survey_data)!=0 ) 
													{?>

														@foreach($cancelled_survey_data as $data)  
														<li>
															<span class="past-img">
																<img src="{{ URL::asset('/public/media') }}/ship.png" alt="#">
															</span>
															<span class="ship-info">
																<h3>{{$data->surveytype_name}}  </h3>
																<h3>{{$data->vesselsname}}  </h3>
																<p><span class="location_icon"><i class="fas fa-map-marker-alt"></i></span>{{$data->port_name}}</p>
																<p><span class="time_icon"><i class="far fa-clock"></i></span>{{date("d M Y",strtotime($data->start_date))}}- {{date("d M Y",strtotime($data->end_date))}}</p>
																<?php 	$helper=new App\Helpers;
																		$user = Auth::user();
																		$SurveyUsers =new App\Models\SurveyUsers;
																		$Surveyusersc= $SurveyUsers->where('survey_id',$data->id)->count();
																?>
																<a href="#" class="paid pending">
																@if(($user->type=='0' || $user->type=='1') && $data->declined=='1')
																Cancelled
																@else
																{{$helper->GetSurveyStatusBykey($data->status)}}

																@endif
																 </a>
																	<?php if($data->status=='1' || $data->status=='3')
																	{
																	
																			if($data->assign_to!=""){$surveyor_id=$data->assign_to;}else{$surveyor_id=$data->accept_by;}
																			if($data->assign_to_op!=""){$operator_id=$data->assign_to_op;}else{$operator_id=$data->user_id;}
																			
																			if($operator_id==$user->id){$sender_id=$operator_id;$receiver_id=$surveyor_id;}else{$sender_id=$surveyor_id;$receiver_id=$operator_id;}
																		?>
																
																	
															<?php } ?>
																
															</span>
															<span class="right-header">
																<!-- <a href="javaScript:Void(0);" class="active" onclick="shipfavourite('{{base64_encode($data->id)}}');"><i class="fas fa-heart" aria-hidden="true"></i> </a>     -->
																<a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn" data-original-title="View User" onclick="view_record('{{$data->id}}')"><i class="fa fa-eye" aria-hidden="true"></i></a>   
															</span>                           
														</li>
														
														@endforeach
															 
															<?php }else{ ?>
														
																			<li>
																				<span class="ship-info">
																					<h3 class="text-center">No cancelled surveys…</h3>
																				</span>
																			</li>
																<?php  } ?>
												</ul>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
			</div>
		</div>
	</div>
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
</div> 
<div id="myModal1" class="modal fade chatModal form-modal" data-keyboard="false"  role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <i class="fa fa-user"></i>&nbsp;&nbsp;<span class='form-title'></span>
                </h4>
                <button type="button" class="close subbtn" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" id="UserModal1">

            </div>
        </div>
    </div>
</div> 
<div id="ratingModal" class="modal fade form-modal" data-keyboard="false"  role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg modal-big">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <i class="fa fa-user"></i>&nbsp;&nbsp;<span class='form-title'></span>
                </h4>
                <button type="button" class="close subbtn" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" id="RatingModal">

            </div>
        </div>
    </div>
</div> 
