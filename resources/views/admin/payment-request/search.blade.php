<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                  <th>Surveyor Email</th>
                  <th>Surveyor Country</th>
                  <th>Invoice Total</th>
                  <th>Survey IDs</th>
                  <th>Surveyor Balance</th>
                  <th>Payment Method</th>
                  <th>Actual Transfer To Surveyor</th>
                  <th>Early Withdrawal Fees</th>
                  <th>iMarS Transfer Cost($USD)</th>
                  <th>Total Commission</th>
                  <th>Created</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($payment_request_data as $key=> $data)
                <tr>
                <td>{{$payment_request_data->firstItem() + $key}}</td>
                  <td>{{$data->surveyor_email}}</td>
                  <td>{{$data->country_name}}</td>
                  <td>{{$data->invoice_total}}</td>
                  <?php $survey=new App\Models\Survey;
                     $survey_ata =$survey->select('survey_number')->whereIn('id',explode(',',$data->survey_ids))->get();
                   // dd( $survey_ata);
                   $survey_numbers="";
                   $value="";
                   foreach($survey_ata as $S){
                      $survey_numbers.=$value.$S->survey_number;
                      $value=",";
                   }
                     //$survey_numbers=implode(',',$survey_ata->survey_number);
                   ?>
                  <td>{{$survey_numbers}}</td>
                  <td>{{$data->surveyor_balance}}</td>
                  <td>{{$data->payment_method}}</td>
                  <td>{{$data->actual_transferto_surveyor}}</td>
                  <td>{{$data->early_withdrawal_fees}}</td>
                  <td>{{$data->imars_transfer_cost }}</td>
                  <td>{{$data->total_commission }}</td>
                 
                  <td>{{$data->created_at}}</td>
                  <td>{{$data->status}}</td>

                  <td class="res-dropdown" style="" align="center">
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>

                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-warning" data-original-title="View Survey Detail" onclick="view_record('{{base64_encode($data->id)}}')"><i class="fa fa-eye" aria-hidden="true"></i></a></a>
                  </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="14">No Payment Request Data</td>

                </tr>
                @endif    
                </tbody> 
                
    </table>
    {!! $payment_request_data->links() !!}