<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                <th>Port Name</th>
                  <th>Port Country</th>
                  <th>Created</th>
                  <th>No Of Surveys</th>
                  <th>No Of Surveys Completed</th>
                  <th>No Of Surveys Upcoming</th>
                  <th>Status</th>

                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($port_data as $key=>$data)
                <tr>
                  <td>{{$port_data->firstItem() + $key}}</td>
                  <td>{{$data->port}}</td>
                  <td>{{$data->country}}</td>
                  
                  <?php 
                   $survey=new App\Models\Survey;
                   $no_of_surveys=$survey->where('port_id',$data->id)->count();
                   $no_of_surveys_completed=$survey->where('port_id',$data->id)->where('status','6')->count();

                   $no_of_surveys_upcomming=$survey->where('port_id',$data->id)->where('status','2')->count();

                  ?>

                  <td>{{$data->created_at}}</td>
                  <td>{{$no_of_surveys}}</td>
                  <td>{{$no_of_surveys_completed}}</td>
                  <td>{{$no_of_surveys_upcomming}}</td>
                  <td>@if($data->status=='1') Active @else Deactivated  @endif</td>
                  <td class="res-dropdown" style="" align="center">
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit Port" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
                  <!--<a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Remove Country" onclick="remove_record('{{base64_encode($data->id)}}')"><i class="fa fa-trash" aria-hidden="true"></i></a>--></a>
                
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="9">No Port Data</td>

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