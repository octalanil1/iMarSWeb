<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                <th>User Email</th>
                  <th>Port Country</th>
                  <th>Port Name</th>
                  <th>Transportation Cost</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($port_data as $key=> $data)
                <tr>
                <td>{{$port_data->firstItem() + $key}}</td>
                  <td>{{$data->user_email}}</td>
                  <td>{{$data->country_name}}</td>
                  <td>{{$data->port_name}}</td>
                  <td>{{$data->cost}}</td>
                  <td>@if($data->status=='1')Active @else Deactivated @endif</td>
                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit User Port" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
                  @if($data->status=='1')
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Deactive User Port" onclick="remove_record('{{base64_encode($data->id)}}')"><i class="fa fa-remove" aria-hidden="true"></i></a></a>
                @else
                <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-success" data-original-title="Active User Port" onclick="remove_record('{{base64_encode($data->id)}}')"><i class="fa fa-check" aria-hidden="true"></i></a></a>
              @endif

                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="8">No User Port Data</td>

                </tr>
                @endif    
                </tbody> 
                
    </table>
    <?php 
          $per_page =  $port_data ->perPage();
          $cuurent_page = $port_data ->currentPage();
          $strt_at =  ($per_page*($cuurent_page-1))+1;
					$end_at = ($strt_at+$port_data ->count())-1;
          $text_line = "Showing ".$strt_at." to ".$end_at; 
    ?>
  <div class="col-sm-4"> <?php echo $text_line; ?> of <?php echo $port_data ->total(); ?> rows </div>
    {!! $port_data->links() !!}