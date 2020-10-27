<script type="text/javascript">
    $(document).ready(function () 
    {  $.LoadingOverlay("hide");
    });
    </script>


  <script type="text/javascript">
    $(document).ready(function () 
    {  $.LoadingOverlay("hide");
    });
</script>

<table class="table table-bordered table-hover">
    <tbody>
        <?php $helper=new App\Helpers;?>
       
        <tr> <th>User Image</th><td> @if($userdata->profile_pic!="")
                        <img src="{{asset('/public/media/users').'/'.$userdata->profile_pic}}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>

                   @if($userdata->created_by!="" || $userdata->type=='0' || $userdata->type=='2'  )
						<?php 
						
								$userser=new App\User;
								$usera_id=$userdata->id;

								if($userdata->created_by!="" || $userdata->created_by!="0"){

									$createdby =  $userser->select('first_name','last_name','city')->where('users.id',$userdata->created_by)->first(); 
								}else{
									$createdby =  $userser->select('first_name','last_name','city')->where('users.id',$usera_id)->first(); 

								}
								

                         ?>
                         
                <tr> <th>Designated Person</th><td>{{$createdby->first_name}}  {{$createdby->last_name}}</td></tr>
        @endif	
        <tr> <th>Country</th><td>{{$userdata->country}}</td></tr>
        <tr> <th>state</th><td>
            @if(!empty($createdby)) {{$createdby->state}}

            @else {{$userdata->state}} @endif
        </td></tr>
        <tr> <th>City</th><td>
            @if(!empty($createdby)) {{$createdby->city}}

            @else {{$userdata->city}} @endif
        </td></tr>
        <tr> <th>Street Address</th><td>
            @if(!empty($street_address)) {{$createdby->street_address}}

            @else {{$userdata->street_address}} @endif
        </td></tr>
        </td></tr>
        <tr> <th>Zipcode</th><td>
            @if(!empty($pincode)) {{$createdby->pincode}}

            @else {{$userdata->pincode}} @endif
        </td></tr>
        <tr> <th>Phone Number</th><td>{{$userdata->mobile}} @if($userdata->mobile_verify) <span style="color:green;">Verified</span> @else  <span style="color:red;">Unverified</span> @endif</td></tr>
        <tr> <th>Email</th><td> {{$userdata->email}} @if($userdata->email_verify)  <span style="color:green;">Verified</span> @else  <span style="color:red;">Unverified</span> @endif</td></tr>
        <tr> <th>Company Name</th><td> {{$userdata->company}}</td></tr>
        <!-- <tr> <th>Company Address</th><td> {{$userdata->company_address}}</td></tr> -->
        <tr> <th>Company Tax Id</th><td> {{$userdata->company_tax_id }}</td></tr>
        <tr> <th>Company Website</th><td> {{$userdata->company_website 	}}</td></tr>
        @if($userdata->type!=2)

        <tr> <th>Invoice address</th><td> 
                  @if($userdata->street_address!="") {{$userdata->street_address.','}}  @endif 
            @if($userdata->state!="") {{$userdata->state.','}}  @endif
            @if($userdata->city!="") {{$userdata->city}}  @endif
        </td></tr>
        @endif
        
<tr> <th>Years of experience</th><td> 
          @if($userdata->experience!="") {{$userdata->experience}}  @endif 
    
</td></tr>
<tr> <th>About</th><td> 
          @if($userdata->about_me!="") {{$userdata->about_me}}  @endif 
    
</td></tr>

<tr> <th>SSN (For USA only)#</th><td> 
          @if($userdata->ssn!="") {{$userdata->ssn}}  @endif 
    
</td></tr>
<tr> <th>Tax ID Number #:</th><td> 
          @if($userdata->company_tax_id!="") {{$userdata->company_tax_id}}  @endif 
    
</td></tr>
										    
											

        <tr> <th>Type</th><td> {{$helper->UserTypeName($userdata->type)}} </td></tr>
        <tr> <th>Status</th><td> @if($userdata->status==0) Deactive @elseif($userdata->status==1) Active @else Pending @endif</td></tr>
        @if($userdata->type=="4")
        <tr> <th>Upload Id</th><td> @if($userdata->upload_id !="")
                        <img src="{{asset('/public/media/users/upload_id').'/'.$userdata->upload_id }}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
        <tr> <th>Diploma</th><td> @if($userdata->diploma !="")
                        <img src="{{asset('/public/media/users/diploma').'/'.$userdata->diploma }}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
                   <tr> <th>Employment Reference Letter  </th><td> @if($userdata->employment_reference_letter !="")
                        <img src="{{asset('/public/media/users/employment_reference_letter').'/'.$userdata->employment_reference_letter }}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
                   <tr> <th>Certificates</th><td> @if($userdata->certificates!="")
                        <img src="{{asset('/public/media/users/certificates').'/'.$userdata->certificates}}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
                   <tr> <th>Port Gate Entry Pass</th><td> @if($userdata->port_gate_pass!="")
                        <img src="{{asset('/public/media/users/port_gate_pass').'/'.$userdata->port_gate_pass}}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
                   


        @else
        <tr> <th>Upload Id</th><td> @if($userdata->upload_id !="")
                        <img src="{{asset('/public/media/users/upload_id').'/'.$userdata->upload_id }}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
                   <tr> <th>Tax ID Document  </th><td> @if($userdata->tax_id_document !="")
                        <img src="{{asset('/public/media/users/tax_id_document').'/'.$userdata->tax_id_document }}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
                   <tr> <th>Sample Invoice for company</th><td> @if($userdata->invoice_address_to_company!="")
                        <img src="{{asset('/public/media/users/invoice_address_to_company').'/'.$userdata->invoice_address_to_company}}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
                   <tr> <th> Utility Bill </th><td> @if($userdata->utility_bill !="")
                        <img src="{{asset('/public/media/users/utility_bill').'/'.$userdata->utility_bill }}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
                   <tr> <th>Incorporation Cerificate </th><td> @if($userdata->incorporation_certificate !="")
                        <img src="{{asset('/public/media/users/incorporation_certificate').'/'.$userdata->incorporation_certificate }}" width="55px" height="55px">
                    @else
                        <img src="{{asset('/public/media/no-image.png')}}" width="55px" height="55px"> 
                   @endif</td></tr>
@endif
        <tr> <th>Created</th><td>{{$userdata->created_at}}</td></tr>
    </tbody>
</table>
