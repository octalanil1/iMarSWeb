<script type="text/javascript">

    $(document).ready(function () 

    {  $.LoadingOverlay("hide");

    });

    </script><table class="table table-bordered table-hover">
<tbody><?php $helper=new App\Helpers;?>
        <tr> <th>Company Name</th><td>{{$userdata->company}}</td></tr>
        <tr> <th>Company Address</th><td>{{$userdata->company_address}}</td></tr>
		<tr> <th>Company Tax Id</th><td>{{$userdata->company_tax_id}}</td></tr>
		<tr> <th>Company Website</th><td> {{$userdata->company_website}}</td></tr>
    </tbody>
               
  </table>