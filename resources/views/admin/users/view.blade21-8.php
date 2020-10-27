<script type="text/javascript">
    $(document).ready(function () 
    {  $.LoadingOverlay("hide");
    });
    </script>


  <script type="text/javascript">
    $(document).ready(function () 
    {  $.LoadingOverlay("hide");
    });
</script>

<table class="table table-bordered table-hover">
    <tbody>
        <?php $helper=new App\Helpers;?>
       
        <tr> <th>User Image</th><td> @if($userdata->profile_pic!="")
                        <img src="{{asset('/media/users').'/'.$userdata->profile_pic}}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
        <tr> <th>User Name</th><td>{{$userdata->first_name}}{{$userdata->last_name}}</td></tr>
        <tr> <th>SSN No.</th><td>{{substr($userdata->ssn,-4)}}</td></tr>
        <tr> <th>Country.</th><td>{{$userdata->country}}</td></tr>
        <tr> <th>Email</th><td> {{$userdata->email}}</td></tr>
        <tr> <th>Company Name</th><td> {{$userdata->company}}</td></tr>
        <tr> <th>Company Address</th><td> {{$userdata->company_address}}</td></tr>
        <tr> <th>Company Tax Id</th><td> {{$userdata-> 	company_tax_id }}</td></tr>
        <tr> <th>Company Website</th><td> {{$userdata->company_website 	}}</td></tr>
        <tr> <th>Type</th><td> {{$helper->UserTypeName($userdata->type)}}</td></tr>
        <tr> <th>Status</th><td> @if($userdata->status==0) Deactive @else Active @endif</td></tr>
        <tr> <th>About Me</th><td> {{$userdata->about_me}}</td></tr>

        <tr> <th>Experience</th><td> {{$userdata->experience}}</td></tr>

        <tr> <th>Created</th><td>{{$userdata->created_at}}</td></tr>
    </tbody>
</table>
