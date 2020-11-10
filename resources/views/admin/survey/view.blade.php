<script type="text/javascript">

    $(document).ready(function () 

    {  $.LoadingOverlay("hide");

    });

    </script><table class="table table-bordered table-hover">
<tbody><?php $helper=new App\Helpers;?>
        
        <tr> <th>Survey No.</th><td>{{$helper->SurveyNo($surveydata->id)}}</td></tr>
        <tr> <th>Operator Name</th><td><a href='javascript:;' onclick="view_records('{{base64_encode($surveydata->user_id)}}')">{{$surveydata->operator_name}}</a></td></tr>
		<tr> <th>Operator Company</th><td><a href='javascript:;' onclick="view_company('{{base64_encode($surveydata->user_id)}}')"> {{$surveydata->operator_company}}</a></td></tr>
		<tr> <th>Operator Type</th><td> {{$helper->UserTypeName($surveydata->type)}} </td></tr>
		<tr> <th>Operator Email</th><td>{{$surveydata->operator_email}}</td></tr>
        <tr> <th>Ship Name</th><td>{{$surveydata->shipname}}</td></tr>
        <tr> <th>Port Name</th><td>{{$surveydata->portname}}</td></tr>
		 <tr> <th>Port Country</th><td>{{$surveydata->portcountry}}</td></tr>
        <tr> <th>Arrival Date</th><td>{{$surveydata->start_date}}</td></tr>
        <tr> <th>Departure Date</th><td>{{$surveydata->end_date}}</td></tr>
        <tr> <th>Survey Category</th><td>{{$surveydata->surveycatname}}</td></tr>
        <tr> <th>Surveyor Name</th><td><a href='javascript:;' onclick="view_surveyor('{{base64_encode($surveydata->final_surveyor_id)}}')">{{$surveydata->surveyor_name}}  </a></td></tr>
		<tr> <th>Surveyor Company</th><td><a href='javascript:;' onclick="view_company('{{base64_encode($surveydata->final_surveyor_id)}}')"> {{$surveydata->surveyor_company}}</a></td></tr>
        <tr> <th>Status</th><td>{{$helper->GetSurveyStatusBykey($surveydata->status)}}</td></tr>
        <tr> <th>Created</th><td>{{$surveydata->created_at}}</td></tr>
        <tr> <th>Instruction</th><td>{{$surveydata->instruction}}</td></tr>
        <tr> <th>Instruction Document</th><td>@if($surveydata->file_data!="")<a href="{{ URL::asset('/public/media/survey') }}/{{$surveydata->file_data}}" target="_blank">View</a>@endif</td></tr>

        <tr> <th>Survey Report</th><td> @if($surveydata->report!="")
        <a href="{{ URL::asset('/public/media/report') }}/{{$surveydata->report}}" target="_blank">View Report </a>
        @endif   
            </td>
        </tr>
        <tr> <th>Issue submitted</th><td> 
            <?php  
            $Disputerequest=new App\Models\Disputerequest;
             $dispute_request_data = $Disputerequest->select("dispute_request.comment")->where('survey_id',$surveydata->id)->first();
            // dd($dispute_request_data);
        ?>
                @if(!empty($dispute_request_data))
                {{$dispute_request_data->comment}}
                @endif   
              
            </td>
       </td>
       </tr>
       <tr> <th>Chat History</th><td> <a href="#" onclick="view_chat('{{$surveydata->id}}','{{$surveydata->user_id}}','{{$surveydata->surveyor_id}}')" class="massage_send"><img src="{{ URL::asset('/media') }}/massage_icon.png" alt=""></a>





        </tr>

        
    </tbody>
               
  </table>
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