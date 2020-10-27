<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                  <th>Country</th>
                  <th>Port</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($port_data as $data)
                <tr>
                  <td>{{$i}}</td>
                  <td>{{$data->country}}</td>
                  <td>{{$data->port}}</td>
                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit Country" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Remove Country" onclick="remove_record('{{base64_encode($data->id)}}')"><i class="fa fa-trash" aria-hidden="true"></i></a></a>
                
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td>No Country Data</td>
                </tr>
                @endif    
                </tbody> 
                
    </table>{!! $port_data->links() !!}