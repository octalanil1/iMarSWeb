<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                  <th>User Email</th>
                  <th>Job Id</th>
                  <th>status</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($dispute_request_data as  $key=> $data)
                <tr>
                <td>{{$dispute_request_data->firstItem() + $key}}</td>
                  <td>{{$data->useremail}}</td>
                  <td>{{$data->survey_number}}</td>
                  <td>@if($data->status==1) Active  @else Resolve @endif</td>
                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-warning" data-original-title="View  Detail" onclick="view_record('{{base64_encode($data->id)}}')"><i class="fa fa-eye" aria-hidden="true"></i></a></a>
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit Request" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
     </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="6">No Request Data</td>

                </tr>
                @endif    
                </tbody> 
                
    </table>
    <?php 
          $per_page =  $dispute_request_data ->perPage();
          $cuurent_page = $dispute_request_data ->currentPage();
          $strt_at =  ($per_page*($cuurent_page-1))+1;
					$end_at = ($strt_at+$dispute_request_data ->count())-1;
          $text_line = "Showing ".$strt_at." to ".$end_at; 
    ?>
  <div class="col-sm-4"> <?php echo $text_line; ?> of <?php echo $dispute_request_data ->total(); ?> rows </div>

    {!! $dispute_request_data->links() !!}
              