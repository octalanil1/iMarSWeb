<style>
.company_details_list {
    background: #f4f4f4;
    padding: 10px 15px;
    border-radius: 5px;
    list-style: none;
}
</style>
<script type="text/javascript">

    $(document).ready(function () 

    {  $.LoadingOverlay("hide");

    });

    </script>
    <table class="table table-bordered table-hover">
<tbody>
        
        <tr> <th>Request By</th><td>{{$payment_request_data->username}}</td></tr>
        <!-- <tr> <th>Survey Ids</th><td>{{$payment_request_data->survey_ids}}</td></tr> -->
        <tr> <th>Payment method</th><td>{{$payment_request_data->payment_method}}</td></tr>
        <tr> <th>Status</th><td>{{$payment_request_data->status}}</td></tr>
        <tr> <th>Created</th><td>{{$payment_request_data->created_at}}</td></tr>

        
    </tbody>
               
  </table>
  <?php 
        if($payment_request_data->survey_ids!="")
        {
            $payment_request_data_survey_ids=explode(',',$payment_request_data->survey_ids);
        // echo    count($payment_request_data_survey_ids);exit;
           for($i=0;$i<count($payment_request_data_survey_ids);$i++)
            {
               $Earning=new App\Models\Earning;
            $earning_data = $Earning->select("payment.*")->where('survey_id',$payment_request_data_survey_ids[$i])->orderBy("payment.created_at","DESC")->get();
         //   dd($earning_data );
              
              ?>

                    <table class="table table-bordered table-hover">
                    <tbody>
                                <tr>
                                    <th>Survey Id</th>
                                    <th>Invoice Amount</th>
                                    <th>Amount Received from Operator</th>
                                    <th>Actual Transfer to the Surveyor</th>
                                     
                                </tr>
                                @foreach($earning_data as $data)
                                <tr>
                                    <?php
                                     $survey=new App\Models\Survey;
                                    $survey_ata =$survey->select('survey_number')->where('id',$data->survey_id)->first();
                                      ?>
                                    <td>{{$survey_ata->survey_number}}</td>
                                    <td>{{$data->invoice_amount}}</td>
                                    <td>{{$data->received_from_operator}}</td>
                                    <td>{{$data->transfer_to_surveyor}}</td>
                                </tr>
                                @endforeach
                        </tbody>
                    
                    </table>

             <?php } } ?>

           
    <div class="row" >
    <div class="col-md-12">
    <ul class="company_details_list">
        <h3>Paypal Details</h3>
        <li> <span> Paypal Email:</span> {{$bank['paypal_email_address']}} </li>
    </ul>
    </div>
    </div>
    

<div class="row" >
    <div class="col-md-12">
        <ul class="company_details_list">
        <h3>ACH Details</h3>
                <li> <span> Account Holder Name:</span> {{$bank['acc_holder_name']}} </li>
                <li> <span> Routing Number:</span> {{$bank['routing_number']}} </li>
                <li> <span>Account Number:</span> {{$bank['ach_acc_number']}} </li>

            </ul>
        </div>
</div>

    <div class="row" >
        <div class="col-md-12">
            <ul class="company_details_list">
            <h3>Wire Details</h3>
                    <li> <span> Company Name:</span> {{$bank['company_name']}} </li>
                    <li> <span> Beneficiary Name:</span> {{$bank['beneficiary_name']}} </li>
                    <li> <span>Street Address:</span> {{$bank['street_address']}} </li>
                    <li> <span>City:</span> {{$bank['city']}} </li>
                    <li> <span>State:</span> {{$bank['state']}} </li>
                    <li> <span>Zipcode:</span> {{$bank['pincode']}} </li>
                    <li> <span>Country:</span> {{$bank['country']}} </li>
                    <li> <span> Bank Name:</span> {{$bank['bank_name']}} </li>
                    <li> <span> Swift Code:</span> {{$bank['swift_code']}} </li>
                    <li> <span>Account Number:</span> {{$bank['acc_number']}} </li>
                    <li> <span>Instructions:</span> {{$bank['more_info']}} </li>
                    <?php 
                    if($bank['file']!="" && $bank['file_type']!=""){
                        $arr=explode('/',$bank['file_type']);
                       
                    }
                           
                    ?>
                    <li> <span>file:</span> 
                    @if(!empty($arr['0']))   
                            @if($arr['0']=='image')   
                            <img src="{{ URL::asset('/public/media/bank_instruction'.'/'.$bank['file']) }}" alt="#" style="width: 46px;margin: 20px;">   
                            @else
                            <a href="{{ URL::asset('/public/media/bank_instruction'.'/'.$bank['file']) }}"> {{$bank['file']}} </a>
                            @endif
                    @endif </li>
                    

                </ul>
            </div>
    </div>
                                        