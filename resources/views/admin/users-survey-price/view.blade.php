<script type="text/javascript">

    $(document).ready(function () 

    {  $.LoadingOverlay("hide");

    });

    </script><table class="table table-bordered table-hover">
<tbody><?php $helper=new App\Helpers;?>
        
        <tr> <th>Survey No.</th><td>{{$helper->SurveyNo($surveydata->id)}}</td></tr>
        <tr> <th>Operator Name</th><td><a href='javascript:;' onclick="view_records('{{base64_encode($surveydata->user_id)}}')">{{$surveydata->username}}</a></td></tr>
		<tr> <th>Operator Company</th><td><a href='javascript:;' onclick="view_company('{{base64_encode($surveydata->user_id)}}')"> {{$surveydata->company}}</a></td></tr>
		<tr> <th>Operator Type</th><td> @if(!empty($surveydata->type)) {{$helper->UserTypeName($surveydata->type)}} @endif</td></tr>
		<tr> <th>Operator Email</th><td>{{$surveydata->useremail}}</td></tr>
        <tr> <th>Ship Name</th><td>{{$surveydata->shipname}}</td></tr>
        <tr> <th>Port Name</th><td>{{$surveydata->portname}}</td></tr>
		 <tr> <th>Port Country</th><td>{{$surveydata->portcountry}}</td></tr>
        <tr> <th>Arrival Date</th><td>{{$surveydata->start_date}}</td></tr>
        <tr> <th>Departure Date</th><td>{{$surveydata->end_date}}</td></tr>
        <tr> <th>Survey Category</th><td>{{$surveydata->surveycatname}}</td></tr>
        <tr> <th>Surveyor Name</th><td><a href='javascript:;' onclick="view_surveyor('{{base64_encode($surveydata->surveyor_id)}}')">{{$surveydata->surveyorname}}  </a></td></tr>
		<tr> <th>Surveyor Company</th><td><a href='javascript:;' onclick="view_company('{{base64_encode($surveydata->surveyor_id)}}')"> {{$surveydata->surveyorcompany}}</a></td></tr>
		<tr> <th>Surveyor Instructions</th><td>{{$surveydata->instruction}}</td></tr>
        <tr> <th>Status</th><td>{{$helper->GetSurveyStatusBykey($surveydata->status)}}</td></tr>
        <tr> <th>Created</th><td>{{$surveydata->created_at}}</td></tr>

        
    </tbody>
               
  </table>