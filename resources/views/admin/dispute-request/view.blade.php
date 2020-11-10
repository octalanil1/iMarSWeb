<script type="text/javascript">

    $(document).ready(function () 

    {  $.LoadingOverlay("hide");

    });

    </script>
    <table class="table table-bordered table-hover">
<tbody><?php $helper=new App\Helpers;?>
        
        <tr> <th>Request By</th><td>{{$dispute_request_data->useremail}}</td></tr>
        <tr> <th>Survey Id</th><td>{{$dispute_request_data->survey_number}}</td></tr>
        <tr> <th>Status</th><td>@if($dispute_request_data->status==1) Active  @else Resolve @endif</td></tr>
        <tr> <th>Created</th><td>{{$dispute_request_data->created_at}}</td></tr>
        <tr> <th>Message</th><td>{{$dispute_request_data->comment}}</td></tr>
        <tr> <th>File</th><td>	@if($dispute_request_data->file!="")
												
                          <a href="{{ URL::asset('/public//media/users/dispute-request') }}/{{$dispute_request_data->file}}" class="btn btn-warning"  target="_blank">View File </a>

												
												@endif</td></tr>



        
    </tbody>
               
  </table>