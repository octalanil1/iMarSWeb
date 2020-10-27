<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                  <th>Name</th>
				          <th>Survey Code</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($catedata as  $key =>$data)
                <tr>
                <td>{{$catedata->firstItem() + $key}}</td>

                  <td>{{$data->name}}</td>
				   <td>{{$data->code}}</td>
                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit Category" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
                 <!-- <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Remove Category" onclick="remove_record('{{base64_encode($data->id)}}')"><i class="fa fa-trash" aria-hidden="true"></i></a>-->
				  <?php if($data->status=="0"){?>

                <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-success" data-original-title="Active User" onclick="statusChange('{{base64_encode($data->id)}}')"><i class="fa fa-check" aria-hidden="true"></i></a>

              <?php }else{?>

              <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Deactive User" onclick="statusChange('{{base64_encode($data->id)}}')"><i class="fa fa-close" aria-hidden="true"></i></a>

            <?php }?>

                
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="5">No Category Data</td>
                </tr>
                @endif    
                </tbody> 
               
    </table>{!! $catedata->links() !!}