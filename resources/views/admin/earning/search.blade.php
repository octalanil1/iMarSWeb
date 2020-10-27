<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                  <th>Survey ID</th>
                  <th>Operator Email</th>
                  <th>Surveyor Email</th>
                  <th>Invoice Amount</th>
                  <th>Transfer Cost From Operator</th>
                  <th>Received From Operator</th>
                  <th>Invoice Paid Status</th>
                  <th>Transfer To Surveyor</th>
                  <th>Blanace for this Surveyor</th>
                  <th>Paid to Surveyor</th>
                  <th>Surveyor Total Balance</th>
                  <th>Commission</th>
                  <th>iMarS Transfer Cost</th>
                  <th>Invoice</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($earning_data as $key=> $data)
                <tr>
                <td>{{$earning_data->firstItem() + $key}}</td>
                  <?php $survey=new App\Models\Survey;
                     $survey_ata =$survey->select('survey_number')->whereIn('id',explode(',',$data->survey_id))->first();
                   // dd( $survey_ata);
                  //  $survey_numbers="";
                  //  $value="";
                  //  foreach($survey_ata as $S){
                  //     $survey_numbers.=$value.$S->survey_number;
                  //     $value=",";
                  //  }
                     //$survey_numbers=implode(',',$survey_ata->survey_number);
                   ?>
                  <td>{{$survey_ata->survey_number}}</td>
                  <td>{{$data->operator_email}}</td>
                  <td>{{$data->surveyor_email}}</td>
                  <td>{{$data->invoice_amount}}</td>
                  <td>{{$data->transfer_cost_operator}}</td>
                  <td>{{$data->received_from_operator}}</td>
                  <td>{{$data->invoice_status}}</td>
                  <td>{{$data->transfer_to_surveyor }}</td>
                  <td>{{$data->balance_for_this_surveyor}}</td>
                  <td>{{$data->paid_to_surveyor_status}}</td>
                    <?php $Earning=new App\Models\Earning();
                        $total_b = $Earning->where('surveyor_id',$data->surveyor_id)->sum('balance_for_this_surveyor');
                    ?>
                  <td>{{$total_b}}</td>

                  <td>{{$data->commission_amount}}</td>
                  <td>{{$data->imars_transfer_cost}}</td>
                  <td>@if($data->invoice!="") 
                                <a href="{{ URL::asset('/public/media/invoice') }}/{{$data->invoice}}" target="_blank">View</a>
                            @endif
                            </td>

                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                  <!-- <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-warning" data-original-title="View  Detail" onclick="view_record('{{base64_encode($data->id)}}')"><i class="fa fa-eye" aria-hidden="true"></i></a></a> -->

                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
                  <!-- <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Remove Content" onclick="remove_record('{{base64_encode($data->id)}}')"><i class="fa fa-trash" aria-hidden="true"></i></a></a> -->
                
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="17">No Earning Data</td>
                </tr>
                @endif    
                </tbody> 
                
    </table>{!! $earning_data->links() !!}