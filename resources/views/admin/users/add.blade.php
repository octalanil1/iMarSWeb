<script type="text/javascript">
    $(document).ready(function () 
    { $.LoadingOverlay("hide");
        $( '#addUserForm' ).on( 'submit', function(e) 
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
            url: '{{ URL::to('/admin/add-user-post') }}',
        }).done(function( data ) 
        {  error_remove (); if(data.success==false)
            {
                $.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					if(key=="mobile"){
						$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('.country-code-outer'));

					}else{
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
					}                });

          }else{
            search();
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
    function showUser(){
        var type=document.getElementById("type").value;  

        
        if(type=='2'){
            document.getElementById("company_info").style.display='block'; 
            document.getElementById("document_info").style.display='block'; 
            document.getElementById("about_expiremce").style.display='block';
        }
        else if(type=='4'){
            document.getElementById("about_expiremce").style.display='block';

        }
        else if(type=='0'){
            document.getElementById("company_info").style.display='block'; 
            document.getElementById("document_info").style.display='block'; 
        }
        else{
            document.getElementById("company_info").style.display='none'; 
            document.getElementById("document_info").style.display='none'; 
            document.getElementById("about_expiremce").style.display='none';

        }

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

    {!! Form::open(array('url' => '/admin/add-user-post', 'method' => 'post','name'=>'addUserForm','files'=>true,'novalidate' => 'novalidate','id' => 'addUserForm')) !!}
    <div class="col-sm-12 p-r-30">
        <div class="panel form-horizontal panel-transparent">
            <div class="panel-body">
                <div class="row user-info-field">
                <fieldset>
               <legend>User Information:</legend>
                
              
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Designated Person First Name<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('first_name',null, ['class' => 'form-control','placeholder' => 'First Name','required'=>'required','id'=>'first_name','onkeypress' => 'error_remove()' ]) !!}
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Designated Person Last Name<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('last_name',null, ['class' => 'form-control','placeholder' => 'Last Name','required'=>'required','id'=>'last_name','onkeypress' => 'error_remove()' ]) !!}   
                            </div>
                        </div>
                    </div>
					  <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Type<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            <?php $helper=new App\Helpers;?>
                            {!! Form::select('type',$helper->UserTypeList(),null, ['class' => 'form-control','required'=>'required','id'=>'type','onkeypress' => 'error_remove()','onChange'=>'showUser();']) !!}
                            </div>
                            
                        </div>
                    </div>
					 <div class="col-12 col-sm-12 col-md-6 survey_category_id" style="display:none;">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Survey Category </label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            
                            {!! Form::select('survey_category_id',$surveycategory_box,null, ['class' => 'form-control','required'=>'required','id'=>'survey_category_id','onkeypress' => 'error_remove()']) !!}
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Email<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('email',null, ['class' => 'form-control','placeholder' => 'Email','required'=>'required','id'=>'email','onkeypress' => 'error_remove()' ]) !!}   
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Country<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            <?php $helper=new App\Helpers;?>
                                {!! Form::select('country_id',$helper->CountryList(),null, ['class' => 'form-control','required'=>'required','id'=>'country_id','onchange'=>'Getcountrycode();']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">City<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('city',null, ['class' => 'form-control','placeholder' => 'City','required'=>'required','id'=>'city','onkeypress' => 'error_remove()']) !!}   
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
                                    <span id="country_code">+00</span>                            
                                
                                    {!! Form::text('mobile',null, ['class' => 'form-control','placeholder' => 'Mobile ','required'=>'required','id'=>'mobile','onkeypress' => 'error_remove()' ]) !!}
                                </div>
                            </div>
                    </div>
                    </div>
                   
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Password<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Password ','required'=>'required','id'=>'password','onkeypress' => 'error_remove()' ]) !!}   
                            </div>
                        </div>
                    </div>
              	 
					 
                    
                
             </fieldset>
        </div>
        
				
        <div class="row user-info-field" id="company_info" style="display:none">
                <fieldset>
               <legend>Company Information:</legend>
               <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Company Name</label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('company',null, ['class' => 'form-control','placeholder' => 'Company Name','required'=>'required','id'=>'company','onkeypress' => 'error_remove()' ]) !!}   
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Company Tax Id</label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('company_tax_id',null, ['class' => 'form-control','placeholder' => 'Company Tax ID','required'=>'required','id'=>'company_tax_id','onkeypress' => 'error_remove()' ]) !!}   
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Company website</label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('company_website',null, ['class' => 'form-control','placeholder' => 'Company website','required'=>'required','id'=>'company_website','onkeypress' => 'error_remove()' ]) !!}   
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Company Address</label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('company_address',null, ['class' => 'form-control','placeholder' => 'Company Address','required'=>'required','id'=>'company_address','onkeypress' => 'error_remove()' ]) !!}   
                            </div>
                        </div>
                    </div>
                </fieldset>
        </div>
        <div class="row user-info-field"  id="document_info" style="display:none">
                <fieldset>
               <legend>User Documents:</legend>
               
                    <div class="col-12 col-sm-12 col-md-6">
                    <div class="row form-group">
                        <label class="col-md-4 col-sm-6 col-xs-12 control-label">Profile Picture</label>
                       
                                <div class="col-md-8">
                                    <span class="import-excel">
                                    <input type="file" name="image" id="technician_category_image" class="form-control input-file" onkeypress="error_remove()">
                                    <button class="btn btn-outline-success">Browse</button>
                                </span>
                                </div>
                           
                    </div>
                </div>
                    <div class="col-12 col-sm-12 col-md-6">
                    <div class="row form-group">
                        <label class="col-md-4 col-sm-6 col-xs-12 control-label">Upload Id</label>
                       
                                <div class="col-md-8">
                                    <span class="import-excel">
                                    <input type="file" name="upload_id"  class="form-control input-file" onkeypress="error_remove()">
                                    <button class="btn btn-outline-success">Browse</button>
                                </span>
                                </div>
                           
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="row form-group">
                        <label class="col-md-4 col-sm-6 col-xs-12 control-label">Tax ID</label>
                       
                                <div class="col-md-8">
                                    <span class="import-excel">
                                    <input type="file" name="tax_id_document"  class="form-control input-file" onkeypress="error_remove()">
                                    <button class="btn btn-outline-success">Browse</button>
                                </span>
                                </div>
                           
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="row form-group">
                        <label class="col-md-4 col-sm-6 col-xs-12 control-label">Sample Invoice For Company</label>
                       
                                <div class="col-md-8">
                                    <span class="import-excel">
                                    <input type="file" name="invoice_address_to_company"  class="form-control input-file" onkeypress="error_remove()">
                                    <button class="btn btn-outline-success">Browse</button>
                                </span>
                                </div>
                           
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="row form-group">
                        <label class="col-md-4 col-sm-6 col-xs-12 control-label">Utility Bill</label>
                       
                                <div class="col-md-8">
                                    <span class="import-excel">
                                    <input type="file" name="utility_bill"  class="form-control input-file" onkeypress="error_remove()">
                                    <button class="btn btn-outline-success">Browse</button>
                                </span>
                                </div>
                           
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="row form-group">
                        <label class="col-md-4 col-sm-6 col-xs-12 control-label">Incorporation Certificate </label>
                       
                                <div class="col-md-8">
                                    <span class="import-excel">
                                    <input type="file" name="incorporation_certificate"  class="form-control input-file" onkeypress="error_remove()">
                                    <button class="btn btn-outline-success">Browse</button>
                                </span>
                                </div>
                           
                    </div>
                </div>

                
             </fieldset>
                </div>
        <div class="row user-info-field" id="about_expiremce" style="display:none">
                <fieldset>
               <legend>About User:</legend>

             
                
                    
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">About</label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            <textarea name="about_me" class ="form-control" style="height: 87px;width: 277px;"> </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Experience<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::select('experience', ['Select Experience','1'=>'1','2'=>'2','3'=>'4','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','10+'=>'10+'],null, ['class' => 'form-control','required'=>'required','id'=>'experience' ]) !!} 
                            </div>
                        </div>
                    </div> 
                    
                </fieldset>
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
<style>
    .user-info-field fieldset 
	{
		border: 1px solid #ddd !important;
		margin: 8px;
		min-width: 0;
		padding: 10px;       
		position: relative;
		border-radius:4px;
		background-color:#f5f5f5;
		padding-left:10px!important;
	}	
	
    .user-info-field legend
		{
			font-size:14px;
			font-weight:bold;
			margin-bottom: 0px; 
			width: 35%; 
			border: 1px solid #ddd;
			border-radius: 4px; 
			padding: 5px 5px 5px 10px; 
			background-color: #ffffff;
		}
</style>