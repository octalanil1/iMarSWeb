<script type="text/javascript">
    $(document).ready(function () 
    { $.LoadingOverlay("hide");
        $( '#editUserForm' ).on( 'submit', function(e) 
        {
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
            
            $.ajax({
                dataType: 'json',
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
            url: '{{ URL::to('/admin/edit-user-post') }}',
        }).done(function( data ) 
        {  error_remove (); if(data.success==false)
            {
                $.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					if(key=="mobile"){
						$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('.country-code-outer'));

					}else{
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
					}                    });

          }else{
            search();
            //alert(data.message);
            if(data.class == 'success'){showMsg(data.message, "success");}
            $("#myModal").modal('hide');
            
	            return false;
          }
            
                         
        });

    });
});
$('#type').on('change', function() {
  //alert( this.value );
  var valdata = this.value;
  
  if(valdata == 2 || valdata == 3 || valdata == 4)
  {
	  
	 $(".survey_category_id").css("display", "block"); 
  }
  
});
function Getcountrycode() 
    { $.LoadingOverlay("show");   
      $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
              var country_id=document.getElementById("country_id").value;  
           // alert(country_id);
      $.ajax({
            
            data: { country_id:country_id}, 
            type: "POST",
            url: '{{ URL::to('/getcountrycode') }}',
        }).done(function( data ) 
        {  // alert(data);
            document.getElementById("country_code").innerHTML =data;  
            $.LoadingOverlay("hide");   
           
          
        });
        
    }
</script>
<style>
     .country-code-outer {
    display: inline-flex;
    background: #fff;
    align-content: center;
    border: 1px solid #ced4da;
    border-radius: 5px;
    /* width: 100%; */
    overflow: hidden
}

.country-code-outer #country_code {
    width: auto;
    display: inline-block;
    position: relative;
    top: 6px;
    left: 6px
}

.country-code-outer .form-control {
    border: 0px!important;
    width: auto;
    padding-left: 21px
}

.country-code-outer #country_code::after {
    content: "";
    width: 1px;
    position: absolute;
    height: 24px;
    background: #ccc;
    right: -6px
}
    </style>

    {!! Form::open(array('url' => '/admin/edit-user-post', 'method' => 'post','name'=>'editUserForm','files'=>true,'novalidate' => 'novalidate','id' => 'editUserForm')) !!}
    <div class="col-sm-12 p-r-30">
        <div class="panel form-horizontal panel-transparent">
            <div class="panel-body">
                <div class="row">
                    @if($userdata->profile_pic!="")
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                        <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Profile Pic<span class="red-star">*</span></label>
                            <img src="{{asset('/media/users').'/'.$userdata->profile_pic}}" width="70px" height="70px">
                        </div>
                    </div>
                    @else
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                        <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Profile Pic<span class="red-star">*</span></label>

                            <img src="{{asset('/media/no-image.png')}}" width="70px" height="70px">
                        </div>
                    </div>
                    @endif
                </div>
                <div class="row">
                   
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">First Name<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('first_name',$userdata->first_name, ['class' => 'form-control','placeholder' => 'First Name','required'=>'required','id'=>'first_name','onkeypress' => 'error_remove()','disabled'=>'disabled' ]) !!}
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Last Name<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('last_name',$userdata->last_name, ['class' => 'form-control','placeholder' => 'Last Name','required'=>'required','id'=>'last_name','onkeypress' => 'error_remove()' ,'disabled'=>'disabled']) !!}   
                            </div>
                        </div>
                    </div>
					 <div class="col-12 col-sm-12 col-md-6">
                            <div class="row form-group">
                                <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Type<span class="red-star">*</span></label>
                                <div class="col-lg-8 col-sm-6 col-xs-12">
                                {!! Form::hidden('user_id',base64_encode($userdata->id),['id'=>'user_id']) !!}
                                <?php $helper=new App\Helpers;?>
                                {!! Form::select('type',$helper->UserTypeList(),$userdata->type, ['class' => 'form-control','required'=>'required','id'=>'type','onkeypress' => 'error_remove()' ]) !!}
                                </div>
                                
                            </div>
                    </div>
					 <div class="col-12 col-sm-12 col-md-6 survey_category_id" style="<?php if($userdata->type == 2 || $userdata->type == 3 || $userdata->type == 4) {?> <?php } else { ?> display:none; <?php } ?> ">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Survey Category </label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            
                            {!! Form::select('survey_category_id',$surveycategory_box,$userdata->survey_category_id, ['class' => 'form-control','required'=>'required','id'=>'survey_category_id','onkeypress' => 'error_remove()']) !!}
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Email<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('email',$userdata->email, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email','onkeypress' => 'error_remove()' ,'disabled'=>'disabled']) !!}   
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Country<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            <?php $helper=new App\Helpers;?>
                                {!! Form::select('country_id',$helper->CountryList(),$userdata->country_id, ['class' => 'form-control','required'=>'required','id'=>'country_id','onchange'=>'Getcountrycode();']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">City<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('city',$userdata->city, ['class' => 'form-control','placeholder' => 'City','required'=>'required','id'=>'city','onkeypress' => 'error_remove()']) !!}   
                            </div>
                        </div>
                    </div>
                    <!-- <div class="country-code-outer">
									<span id="country_code">+00</span>
                                    {!! Form::text('mobile',null, ['class' => 'form-control','placeholder' => 'Mobile ','required'=>'required','id'=>'mobile','onkeypress' => 'error_remove()' ]) !!}
                                    </div>  -->
                                    
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Mobile<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                                <div class="country-code-outer">
                                <?php $country=new App\Models\Countries;
                                		$countrydata = $country->select('countries.phonecode')->where('id',$userdata->country_id)->first();

                                ?>

                                    <span id="country_code">{{$countrydata->phonecode}}</span>                            
                                
                                    {!! Form::text('mobile',$userdata->mobile, ['class' => 'form-control','placeholder' => 'Mobile ','required'=>'required','id'=>'mobile','onkeypress' => 'error_remove()' ]) !!}
                                </div>
                            </div>
                    </div>
                    </div>
					
					 <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label"></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                         
						   <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit User" onclick="reset_password({{$userdata->id}})" >Reset password</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                            <div class="row form-group">
                                <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Status<span class="red-star">*</span></label>
                                <div class="col-lg-8 col-sm-6 col-xs-12">
                                <?php $helper=new App\Helpers;?>
                                {!! Form::select('status',['2'=>'Pending','1'=>'Active','0'=>'Deactivated'],$userdata->status, ['class' => 'form-control','required'=>'required','id'=>'type','onkeypress' => 'error_remove()']) !!}
                                </div>
                                
                            </div>
                    </div>
					 <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Company Name</label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('company',$userdata->company, ['class' => 'form-control','placeholder' => 'Company Name','required'=>'required','id'=>'company','onkeypress' => 'error_remove()' ,'disabled'=>'disabled']) !!}   
                            </div>
                        </div>
                    </div>
					
					 <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Company website</label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('company_website',$userdata->company_website, ['class' => 'form-control','placeholder' => 'Company website','required'=>'required','id'=>'company_website','onkeypress' => 'error_remove()' ,'disabled'=>'disabled' ]) !!}   
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Password</label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Password ','required'=>'required','id'=>'password','onkeypress' => 'error_remove()' ]) !!}   
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="row form-btn text-center">
        <div class="col-sm-12 p-r-30">
        <div class="col-md-12"> 
        {!! Form::submit('Save',['class' => 'btn btn-primary btn-flat subbtn', 'type' => 'submit']) !!}
        {!! Form::submit('Cancel',['class' => 'btn btn-flat subbtn','data-dismiss'=>'modal']) !!}
        </div>
    </div>
    {!! Form::close() !!}
