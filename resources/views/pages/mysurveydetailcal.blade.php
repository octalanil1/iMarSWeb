<script type="text/javascript">
    $(document).ready(function () 
    { 
         $.LoadingOverlay("hide");
        });
 </script>
<section class="detail_outer">
		<div class="container">
			<div class="row">
            <?php $user = Auth::user();?>

				<div class="col-md-12">
					<div class="box-outer">
                        <p><span>Survey Number : </span>{{$surveyor_data->survey_number}}</p>
                        <p><span>Port Name : </span>{{$surveyor_data->portname}}</p>
                        <p><span>Date : </span> <?php echo  date("d M Y",strtotime($surveyor_data->start_date));?> to  <?php echo  date("d M Y",strtotime($surveyor_data->end_date));?></p>                      
						<p><span>Survey Type : </span>{{$surveyor_data->surveytype_name}}</p>
						<p><span>Cost of the Survey : </span id="total_price">${{$total_price}}</p>
						<p><span>Operator : </span>{{$opusername}}</p>
						<p><span>Surveyor : </span>{{$suusername}}</p>
						<p><span>Surveyor Company : </span>{{$su_company_name}}</p>
					</div>
					<h4>Vessels Details:</h4>
					<div class="box-outer">
						<p><span>Vessels Name : </span>{{$surveyor_data->vesselsname}}</p>
						<p><span>IMO Number : </span>#{{$surveyor_data->imo_number}}</p>
						<p><span>Address : </span><i class="fas fa-map-marker-alt"></i> {{$surveyor_data->vesselsaddress}}</p>
						<p><span>Company Name : </span>{{$surveyor_data->vesselscompany}}</p>
						<p><span>Email Address : </span>{{$surveyor_data->vesselsemail}}</p>
                    </div>
                    <h4>Operator Company Details:</h4>
					<div class="box-outer">
						<p><span>Company Name : </span>{{$surveyor_data->operator_company}}</p>
						<p><span>Operator Name : </span>{{$surveyor_data->operator_name}}</p>
						<p><span>Website : </span> {{$surveyor_data->operator_company_website}}</p>
						<p><span>No of survey : </span>{{$operator_survey_count}}</p>
						<p><span>Country: </span>{{$country_data->name}}</p>
                    </div>
                    <h4>Agents Details:</h4>
					<div class="box-outer">
						<p><span> Name : </span>{{$surveyor_data->agent_name}}</p>
						<p><span>Phone Number : </span>{{$surveyor_data->agentsmobile}}</p>
						<p><span>Email : </span> {{$surveyor_data->agentsemail}}</p>
						
					</div>
        

				 </div>
			</div>
		</div>
    </section>
    