<table class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>SR.NO.</th>
                  <th>Operator Email</th>
                  <th>Operator Company</th>
                  <th>Surveyor Email</th>
                  <th>Surveyor Company</th>
                  <th>Job Id</th>
                  <th>Vessel</th>
                  <th>Arrival Date</th> 
                  <th>Departure Date</th>
                  <th>Cost</th>
                  <th>Survey Code</th>
                  <th>Status</th>
                  <th>Last Status</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($surveydata as  $key=>$data)
                <tr>
                  <td>{{$surveydata->firstItem() + $key}}</td>
                  <td>{{$data->operator_email}}</td>
                  <td>{{$data->operator_company}}</td>
                  <td>{{$data->surveyor_email}}</td>
                
                 <td>{{$data->surveyor_company}}</td> 

                  <td>{{$data->survey_number}}</td>
                  <td>{{$data->shipname}}</td>
                  <td>{{$data->start_date}}</td>
                  <td>{{$data->end_date}}</td>
                  <td>
                    <?php $helper=new App\Helpers;
							$total_price=0;
              if($data->survey_type_id=='31')
							{
                 $Customsurveyusers =new App\Models\Customsurveyusers;
										$survey_ch =   $Customsurveyusers->where('survey_id',$data->id)
										->Where('custom_survey_users.status','approved')->first();
									
									if(!empty($survey_ch)){
                    $total_price=$survey_ch->amount;
									}
									
							}else{		
									
                    $Surveyusers =new App\Models\SurveyUsers;
										$survey_ch =  $Surveyusers->where('survey_id',$data->id);
										$survey_ch=$survey_ch->where(function ($query)  {
												$query->Where('survey_users.status','pending')
												->orwhere('survey_users.status','upcoming' );});
                        $survey_ch=$survey_ch->first();

                        if($data->no_of_days!="0"){
                          //$total_price=$data->survey_price*$data->no_of_days;
                          $total_price=$data->survey_price*$data->no_of_days+$data->port_price;
                        }else{
                         // $total_price=$data->survey_price;
                          $total_price=$data->survey_price+$data->port_price;
                        }
                        
											
                }
                if($total_price) {echo $total_price; }
                ?>

                  </td>
                  <td></td>
                  
                  <td><?php $helper=new App\Helpers;?>{{$helper->GetSurveyStatusBykey($data->status)}}</td>
				  <td><?php $helper=new App\Helpers;?>@if(!empty($data->last_status)) {{$helper->GetSurveyStatusBykey($data->last_status)}} @endif</td>
                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                  <!-- <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit User" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a> -->
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-warning" data-original-title="View User" onclick="view_record('{{base64_encode($data->id)}}')"><i class="fa fa-eye" aria-hidden="true"></i></a></a>
                <!-- <?php if($data->status=="0"){?>
                <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-success" data-original-title="Active User" onclick="statusChange('{{base64_encode($data->id)}}')"><i class="fa fa-check" aria-hidden="true"></i></a>
              <?php }else{?>
              <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Deactive User" onclick="statusChange('{{base64_encode($data->id)}}')"><i class="fa fa-close" aria-hidden="true"></i></a>
            <?php }?> -->
			<?php if($data->status=="4"){ ?>
			        <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Change Status" onclick="changeStatus('{{base64_encode($data->id)}}','{{$data->status}}')"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>
			<?php } else if($data->status=="5"){ ?>
			      <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-success" data-original-title="Change Status" onclick="changeStatus('{{base64_encode($data->id)}}','{{$data->status}}')"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>
			<?php } ?>
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                  <td class="text-center" colspan="15">No Survey Data</td>

                </tr>
                @endif    
                </tbody> 
                
    </table>
    <?php 
          $per_page =  $surveydata ->perPage();
          $cuurent_page = $surveydata ->currentPage();
          $strt_at =  ($per_page*($cuurent_page-1))+1;
					$end_at = ($strt_at+$surveydata ->count())-1;
          $text_line = "Showing ".$strt_at." to ".$end_at; 
    ?>
  <div class="col-sm-4"> <?php echo $text_line; ?> of <?php echo $surveydata ->total(); ?> rows </div>

    {!! $surveydata->links() !!}