<script>$(function () {  $('[data-toggle="tooltip"]').tooltip()});</script>
<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                <th>Image</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Company Name</th>
                  <th>Type</th>
                  <th>City</th>
                  <th>Country</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($userdata as $key => $data)
                <tr>
                  <td>{{$userdata->firstItem() + $key}}</td>
                  <td>
                     @if($data->profile_pic!="")
                        <img src="{{asset('/media/users').'/'.$data->profile_pic}}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif
                  </td>
                  <td>{{$data->first_name}}</td>
                  <td>{{$data->last_name}}</td>
                  <td>{{$data->email}}</td>
                  <td>{{$data->company}}</td>

                  <?php $helper=new App\Helpers;?>
                  <td>{{$helper->UserTypeName($data->type)}}</td>
                  <td>{{$data->city}}</td>
                  <td>{{$data->country}}</td>
                 

                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                 
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit User" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
                
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-warning" data-original-title="View User" onclick="view_record('{{base64_encode($data->id)}}')"><i class="fa fa-eye" aria-hidden="true"></i></a></a>
                  @if($data->status=="2")
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-info" data-original-title="Pending"><i class="fa fa-clock-o" aria-hidden="true"></i></a></a>

                  
                @elseif($data->status=="1")
                <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-success" data-original-title="Active"><i class="fa fa-check" aria-hidden="true"></i></a></a>

                
                @else 
                <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Dective"><i class="fa fa-close" aria-hidden="true"></i></a></a>

                
                @endif
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="11">No User Data</td>

                </tr>
                @endif    
                </tbody> 

  </table> 
  <?php 
          $per_page =  $userdata ->perPage();
          $cuurent_page = $userdata ->currentPage();
          $strt_at =  ($per_page*($cuurent_page-1))+1;
					$end_at = ($strt_at+$userdata ->count())-1;
          $text_line = "Showing ".$strt_at." to ".$end_at; 
    ?>
  <div class="col-sm-4"> <?php echo $text_line; ?> of <?php echo $userdata ->total(); ?> rows </div>
  {!! $userdata->links() !!}