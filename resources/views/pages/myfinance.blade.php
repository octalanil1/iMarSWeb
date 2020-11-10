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
				url: '{{ URL::to('/myfinance') }}',
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
            <div class="surveyors ports">

                <div class="right-flex-box">
                    <h4>Finance</h4>
                </div>
                <div class="login-inner">
				<div class="row">
				<div class="col-md-12">
					{!! Form::open(array('url' => '/mysurvey', 'method' => 'post','name'=>'mySearchForm','files'=>true,'novalidate' => 'novalidate','id' => 'mySearchForm')) !!}
					
                    <?php $helper=new App\Helpers;  	
                    $SurveyTypeList=$helper->SurveyTypeList();?>
					
					<div id="upcomming_filter">
					  <div class="row">
						   
						  
						   <div class="col-md-3">
						   		{!! Form::select('category',$SurveyTypeList,$category, ['class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()']) !!}
                           </div>
                           <div class="col-md-3">
						   		{!! Form::select('status',[''=>'Select Status','paid'=>'Paid','unpaid'=>'Unpaid'],$status, ['class' => 'form-control','required'=>'required','onkeypress' => 'error_remove()']) !!}
                           </div>
                           <div class="col-md-4">
						   		{!! Form::text('search',$search, ['class' => 'form-control','required'=>'required','placeholder'=>'Survey #, Vessel Name','onkeypress' => 'error_remove()']) !!}
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
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Survey Number</th>
                            <th>Invoice Date</th>
                            <th>Invoice Amount</th>
                            <th>Vessels Name</th>
                            <th>Port Name</th>
                            <th>Survey Code</th>
                            <th>Status</th>
                            <th>Invoice</th>
                            <th>Created</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach($finance_data as $data)
                        <tr>
                          
                            <td>{{$data->survey_number}}</td>
                            <td>{{$data->created_at}}</td>
                            <td>{{$data->invoice_amount}}</td>
                            <td>{{$data->vesselsname}}</td>
                            <td>{{$data->port_name}}</td>
                            <td>&nbsp;</td>
                            <td>{{$data->invoice_status}}</td>
                            <td>@if($data->invoice!="") 
                                <a href="{{ URL::asset('/public/media/invoice') }}/{{$data->invoice}}" target="_blank">View</a>
                            @endif
                            </td>
                            <td>{{$data->created_at}}</td>
                            
                        </tr>
                        <?php $i++; ?>
                        @endforeach

                        @if($i<2) <tr>
                            <td>No Finance Data</td>
                            </tr>
                            @endif
                    </tbody>

                </table>{!! $finance_data->links() !!}




            </div>
        </div>
    </div>
</section>