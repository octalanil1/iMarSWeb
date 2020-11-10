<script type="text/javascript">
    $(document).ready(function () 
    {  $.LoadingOverlay("hide");
    });
</script>

<table class="table table-bordered table-hover">
<tbody>
    <tr> <th>Recipients : </th><td>{{$noti_data->username}}</td></tr>
    <tr> <th>Country : </th><td>{{$noti_data->country_name}}</td></tr>
    <tr> <th>Notification Type : </th><td>{{$noti_data->noti_type}}</td></tr>
    <tr> <th>Message : </th><td>{{$noti_data->notification}}</td></tr>
    <tr> <th>File : </th><td>@if($noti_data->file!="") <img style="width:100px" src="{{ URL::asset('/media/notification').'/'.$noti_data->file }}" >@endif</td></tr>

    <tr> <th>Created : </th><td>{{$noti_data->created_at}}</td></tr>
</tbody>
</table>