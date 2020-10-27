<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                  <th>Title</th>
                  <th>Visible User</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($content_data as  $key=>$data)
                <tr>
                <td>{{$content_data->firstItem() + $key}}</td>
                  <td>{{$data->title}}</td>
                  <td>
                    @if($data->user=='operator')
                   Operator
                   @endif
                   @if($data->user=='surveyor')
                   Surveyor
                   @endif
                   @if($data->user=='all')
                   All
                   @endif

                  </td>
                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit Content" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Remove Content" onclick="remove_record('{{base64_encode($data->id)}}')"><i class="fa fa-trash" aria-hidden="true"></i></a></a>
                
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="4">No Content Data</td>
  
                </tr>
                @endif    
                </tbody> 
                
    </table>
    <?php 
          $per_page =  $content_data ->perPage();
          $cuurent_page = $content_data ->currentPage();
          $strt_at =  ($per_page*($cuurent_page-1))+1;
					$end_at = ($strt_at+$content_data ->count())-1;
          $text_line = "Showing ".$strt_at." to ".$end_at; 
    ?>
  <div class="col-sm-4"> <?php echo $text_line; ?> of <?php echo $content_data ->total(); ?> rows </div>


    {!! $content_data->links() !!}