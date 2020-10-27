<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                  <th>Name</th>
                  <th>Code</th>
                  <th>Dialing Code</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($country_data as $key=> $data)
                <tr>
                <td>{{$country_data->firstItem() + $key}}</td>
                  <td>{{$data->name}}</td>
                  <td>{{$data->sortname}}</td>
                  <td>{{$data->phonecode}}</td>
                  <td>@if($data->status==0) Deactive @else Active @endif</td>
                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit Country" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
                  <?php if($data->status=="0"){?>
                <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-success" data-original-title="Active Country" onclick="statusChange('{{base64_encode($data->id)}}')"><i class="fa fa-check" aria-hidden="true"></i></a>
              <?php }else{?>
              <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Deactive Country" onclick="statusChange('{{base64_encode($data->id)}}')"><i class="fa fa-close" aria-hidden="true"></i></a>
            <?php }?>                
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="7">No Country Data</td>

                </tr>
                @endif    
                </tbody> 
                
    </table>
    <?php 
          $per_page =  $country_data ->perPage();
          $cuurent_page = $country_data ->currentPage();
          $strt_at =  ($per_page*($cuurent_page-1))+1;
					$end_at = ($strt_at+$country_data ->count())-1;
          $text_line = "Showing ".$strt_at." to ".$end_at; 
    ?>
  <div class="col-sm-4"> <?php echo $text_line; ?> of <?php echo $country_data ->total(); ?> rows </div>
    {!! $country_data->links() !!}    